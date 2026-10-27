// Configuration variables - fetched from DOM
    var verification_type = '';
    var record_id = '';
    var action_type = '';
    var table_name = '';
    var is_verified = false;
    var company_name = '';
    var company_name_from_record = '';

    // Initialize data from DOM
    function initializeDataFromDOM() {
        var dataContainer = document.getElementById('js-data-container');
        console.log('Data container found:', !!dataContainer);
        if (dataContainer) {
            verification_type = dataContainer.querySelector('.js-verification-type')?.textContent || '';
            record_id = dataContainer.querySelector('.js-record-id')?.textContent || '';
            action_type = dataContainer.querySelector('.js-action-type')?.textContent || '';
            table_name = dataContainer.querySelector('.js-table-name')?.textContent || '';
            is_verified = dataContainer.querySelector('.js-is-verified')?.textContent === 'true';
            company_name = dataContainer.querySelector('.js-company-name')?.textContent || '';
            company_name_from_record = dataContainer.querySelector('.js-company-name-from-record')?.textContent || '';
            
            console.log('Extracted data:', {
                verification_type, record_id, action_type, table_name, is_verified, company_name
            });
        } else {
            console.warn('js-data-container not found, using default values');
        }
    }

    // Helper functions for CSS class management
    function showElement(selector) {
        $(selector).addClass('show').removeClass('btn-hidden');
    }
    
    function hideElement(selector) {
        $(selector).removeClass('show').addClass('btn-hidden');
    }
    
    function toggleElement(selector) {
        $(selector).toggleClass('show');
    }

    // Initialize event listeners
    window.initializeEventListeners = function initializeEventListeners() {
        console.log('Initializing event listeners for verification popup');
        
        // Initialize data from DOM first
        initializeDataFromDOM();
        
        // Set global variables
        transaction_table = table_name;
        vendor_id = record_id;
        
        // Close modal buttons
        document.querySelectorAll('.js-close-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                closeModal();
            });
        });
        
        // Show current data button
        document.querySelectorAll('.js-show-current-data').forEach(function(btn) {
            btn.addEventListener('click', function() {
                showCurrentData();
            });
        });
        
        // Format input fields
        document.querySelectorAll('.js-format-pan-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatPANInput(this);
            });
        });
        
        document.querySelectorAll('.js-format-ifsc-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatIFSCInput(this);
            });
        });
        
        document.querySelectorAll('.js-format-bank-account-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatBankAccountInput(this);
            });
        });
        
        document.querySelectorAll('.js-format-tan-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatTANInput(this);
            });
        });
        
        document.querySelectorAll('.js-format-msme-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatMSMEInput(this);
            });
        });
        
        document.querySelectorAll('.js-format-gst-input').forEach(function(input) {
            input.addEventListener('input', function() {
                formatGSTInput(this);
            });
        });
        
        // Check company name
        document.querySelectorAll('.js-check-company-name').forEach(function(input) {
            input.addEventListener('input', function() {
                checkCompanyNameOnInput();
            });
        });
        
        // Check GST availability
        document.querySelectorAll('.js-check-gst-availability').forEach(function(input) {
            input.addEventListener('blur', function() {
                checkalreadyavailableornotcustomerorvendordetails('GSTIN', this);
            });
        });
        
        // Verification buttons
        document.querySelectorAll('.js-verify-pan').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyPAN();
            });
        });
        
        document.querySelectorAll('.js-verify-bank').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyBank();
            });
        });
        
        document.querySelectorAll('.js-verify-tan').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyTAN();
            });
        });
        
        document.querySelectorAll('.js-verify-msme').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyMSME();
            });
        });
        
        document.querySelectorAll('.js-verify-gst').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyGST();
            });
        });
        
        document.querySelectorAll('.js-verify-gst-new').forEach(function(btn) {
            btn.addEventListener('click', function() {
                verifyNewGST();
            });
        });
        
        // Toggle edit buttons
        document.querySelectorAll('.js-toggle-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var type = this.getAttribute('data-type');
                toggleEdit(type);
            });
        });
        
        // Bank confirmation buttons
        document.querySelectorAll('.js-cancel-bank-confirmation').forEach(function(btn) {
            btn.addEventListener('click', function() {
                cancelBankConfirmation();
            });
        });
        
        document.querySelectorAll('.js-confirm-bank-details').forEach(function(btn) {
            btn.addEventListener('click', function() {
                confirmBankDetails();
            });
        });
        
        // MSME confirmation buttons
        document.querySelectorAll('.js-cancel-msme-confirmation').forEach(function(btn) {
            btn.addEventListener('click', function() {
                cancelMSMEConfirmation();
            });
        });
        
        document.querySelectorAll('.js-proceed-msme-save').forEach(function(btn) {
            btn.addEventListener('click', function() {
                proceedWithMSMESave();
            });
        });
        
        // Save verification data button
        document.querySelectorAll('.js-save-verification-data').forEach(function(btn) {
            btn.addEventListener('click', function() {
                saveVerificationData();
            });
        });
    }

    // Show current data function
    function showCurrentData() {
        var section = document.getElementById('current_data_section');
        section.classList.toggle('show');
    }

    // Close modal function
    function closeModal(reloadParent = false) {
        $('#kt_modal_dynamic_popup').modal('hide');
        
        // Reload parent page if requested
        if (reloadParent) {
            setTimeout(function() {
                if (window.parent && window.parent !== window) {
                    window.parent.location.reload();
                } else {
                    window.location.reload();
                }
            }, 500); // Small delay to ensure modal closes first
        }
    }

    // Show notification function
    function showNotification(message, type) {
        var alertClass = 'alert-info';
        var icon = 'fa-info-circle';
        
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                icon = 'fa-check-circle';
                break;
            case 'error':
                alertClass = 'alert-danger';
                icon = 'fa-times-circle';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                icon = 'fa-exclamation-triangle';
                break;
        }
        
        var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fa ' + icon + '"></i> ' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        
        // Remove existing notifications
        $('.alert').remove();
        
        // Add new notification at the top of the modal body
        $('.modal-body').prepend(notification);
        
        // Auto-hide after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
    }

    // Global variables for verification
    var transaction_table = '';
    var vendor_id = '';
    var verificationSteps = {
        pan: { completed: false, data: null, nameMatch: null, skipped: false },
        bank: { completed: false, data: null, nameMatch: null, skipped: false },
        tan: { completed: false, data: null, nameMatch: null, skipped: false },
        msme: { completed: false, data: null, nameMatch: null, skipped: false },
        gst: { completed: false, data: null, nameMatch: null, skipped: false }
    };

    // Validation functions
    function validatePAN(pan) {
        var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        return panRegex.test(pan);
    }

    function validateIFSC(ifsc) {
        var ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        return ifscRegex.test(ifsc);
    }

    function validateBankAccount(account) {
        // Basic validation - should be numeric and between 8-18 digits
        var accountRegex = /^[0-9]{8,18}$/;
        return accountRegex.test(account);
    }

    function validateTAN(tan) {
        var tanRegex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
        return tanRegex.test(tan);
    }

    function validateMSME(msme) {
        var msmeRegex = /^UDYAM-[A-Z]{2}-[0-9]{2}-[0-9]{7}$/;
        return msmeRegex.test(msme);
    }

    function validateGST(gst) {
        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        return gstRegex.test(gst);
    }

    function formatIFSCInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        input.value = value;

        var isValid = validateIFSC(value);
        updateInputStatus('ifsc_code', isValid, value.length === 11);

        // Automatic verification when both IFSC and account are complete and valid
        if (isValid && value.length === 11) {
            checkBankAutoVerification();
        }
    }

    function formatBankAccountInput(input) {
        var value = input.value.replace(/[^0-9]/g, '');
        if (value.length > 18) value = value.substring(0, 18);
        input.value = value;

        var isValid = validateBankAccount(value);
        updateInputStatus('bank_account_no', isValid, value.length >= 8);

        // Automatic verification when both fields are complete and valid
        if (value.length >= 8) {
            checkBankAutoVerification();
        }
    }

    function checkBankAutoVerification() {
        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        if (validateIFSC(ifsc) && validateBankAccount(account)) {
            setTimeout(function () {
                verifyBank();
            }, 500);
        }est(pan);
    }

    function formatPANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validatePAN(value);
        updateInputStatus('pan_number', isValid, value.length === 10);

        // Automatic verification when PAN is complete and valid
        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyPAN();
            }, 500);
        }
    }

    function formatTANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validateTAN(value);
        updateInputStatus('tan_number', isValid, value.length === 10);

        // Automatic verification when TAN is complete and valid
        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyTAN();
            }, 500);
        }
    }

    function formatMSMEInput(input) {
        var value = input.value.toUpperCase();
        
        // Ensure it starts with UDYAM-
        if (!value.startsWith('UDYAM-')) {
            value = 'UDYAM-' + value.replace(/[^A-Z0-9-]/g, '');
        }
        
        // Limit to 19 characters (UDYAM-XX-00-0000000)
        if (value.length > 19) value = value.substring(0, 19);
        input.value = value;

        var isValid = validateMSME(value);
        updateInputStatus('msme_number', isValid, value.length === 19);

        // Automatic verification when MSME is complete and valid
        if (isValid && value.length === 19) {
            setTimeout(function () {
                verifyMSME();
            }, 500);
        }
    }

    function formatGSTInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 15) value = value.substring(0, 15);
        input.value = value;

        var isValid = validateGST(value);
        updateInputStatus('gstin', isValid, value.length === 15);

        // Automatic verification when GST is complete and valid
        if (isValid && value.length === 15) {
            setTimeout(function () {
                if (action_type === 'add_new') {
                    verifyNewGST();
                } else {
                    verifyGST();
                }
            }, 500);
        }
    }

    function updateInputStatus(fieldId, isValid, isComplete) {
        var field = $('#' + fieldId);
        var inputGroup = field.closest('.input-group');
        
        if (inputGroup.length === 0) {
            // Create input group if it doesn't exist
            field.wrap('<div class="input-group"></div>');
            field.after('<div class="input-group-append"><span class="input-group-text status-icon"><i class="fa fa-clock-o text-muted"></i></span></div>');
            inputGroup = field.closest('.input-group');
        }
        
        var statusIcon = inputGroup.find('.status-icon');
        if (statusIcon.length === 0) {
            inputGroup.append('<div class="input-group-append"><span class="input-group-text status-icon"><i class="fa fa-clock-o text-muted"></i></span></div>');
            statusIcon = inputGroup.find('.status-icon');
        }

        inputGroup.removeClass('has-success has-error');
        
        if (isComplete && isValid) {
            field.removeClass('is-invalid').addClass('is-valid');
            inputGroup.addClass('has-success');
            statusIcon.html('<i class="fa fa-check text-success"></i>');
        } else if (isComplete && !isValid) {
            field.removeClass('is-valid').addClass('is-invalid');
            inputGroup.addClass('has-error');
            statusIcon.html('<i class="fa fa-times text-danger"></i>');
        } else if (!isComplete && field.val().length > 0) {
            field.removeClass('is-valid is-invalid');
            statusIcon.html('<i class="fa fa-clock-o text-muted"></i>');
        } else {
            field.removeClass('is-valid is-invalid');
            statusIcon.html('<i class="fa fa-clock-o text-muted"></i>');
        }
    }

    // Company name check functions
    function checkCompanyNameAllReadyAvilableOrNot() {
        var company_name = $("#company_name").val();
        if (company_name != "") {
            $.ajax({
                url: "ajax.php",
                data: 'page=checkcompanynameallreadyavilableornot&company_name=' + company_name,
                type: "POST",
                async: false,
                success: function (data) {
                    console.log(data);
                    var res = JSON.parse(data);
                    if (res.company_name == '1') {
                        console.log('already');
                        showElement("#allreadycompanyname");
                        $("#company_check_status").val("exists");
                    } else {
                        console.log('fresh');
                        hideElement("#allreadycompanyname");
                        $("#company_check_status").val("available");
                        return false;
                    }
                },
                error: function () { 
                    $("#company_check_status").val("error");
                    return false;
                }
            });
        }
        return false;
    }

    function checkCompanyNameOnInput() {
        var company_name = $("#company_name").val();
        if (company_name.trim() != "") {
            // Debounce the API call to avoid too many requests
            clearTimeout(window.companyCheckTimeout);
            window.companyCheckTimeout = setTimeout(function() {
                checkCompanyNameAllReadyAvilableOrNot();
            }, 500); // Wait 500ms after user stops typing
        } else {
            hideElement("#allreadycompanyname");
            $("#company_check_status").val("");
        }
    }

    // PAN Verification function
    function verifyPAN() {
        var pan_no = $("#pan_number").val();
        var company_name = $("#company_name").val();
        var company_email = $("#company_email").val();

        // Validate Required fields
        if (!company_name.trim()) {
            showNotification("Please enter company name", "warning");
            return;
        }

        if (!company_email.trim()) {
            showNotification("Please enter email address", "warning");
            return;
        }

        // Validate email format
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(company_email)) {
            showNotification("Please enter a valid email address", "warning");
            return;
        }

        // Check company existence using hidden field value
        var companyCheckStatus = $("#company_check_status").val();
        if (companyCheckStatus === "exists" && action_type === 'insert') {
            showNotification("Company name already registered. Please use a different name.", "warning");
            return;
        } else if (companyCheckStatus === "" || companyCheckStatus === "error") {
            // If no check was performed or there was an error, perform the check now
            checkCompanyNameAllReadyAvilableOrNot();
            companyCheckStatus = $("#company_check_status").val();
            if (companyCheckStatus === "exists" && action_type === 'insert') {
                showNotification("Company name already registered. Please use a different name.", "warning");
                return;
            }
        }

        if (!validatePAN(pan_no)) {
            showNotification("Please enter a valid PAN number", "warning");
            return;
        }

        if ($("#verify_pan_btn").prop('disabled') || verificationSteps.pan.completed) {
            return;
        }

        $("#pan_number, #company_name, #company_email").prop('readonly', true);
        $("#verify_pan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        showElement("#pan_verification_fields");
        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'pan_holder_name': company_name,
                'customer_pan_number': pan_no,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'ZOOPONEPANLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success == true && result.result) {
                        $("#pan_verified_name").val(result.result.user_full_name || result.result.pan_holder_name || "N/A");
                        $("#pan_verified_type").val(result.result.pan_type || "N/A");
                        $("#pan_verified_status").val(result.result.pan_status || "N/A");

                        verificationSteps.pan.completed = true;
                        verificationSteps.pan.data = result;

                        showElement("#pan_confirmation_section");
                        $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        var company_name_entered = company_name.toLowerCase().trim();
                        var pan_holder_name = (result.result.user_full_name || result.result.pan_holder_name || "").toLowerCase().trim();

                        if (company_name_entered === pan_holder_name) {
                            showNotification("PAN Details are Correct", "success");
                            verificationSteps.pan.nameMatch = true;

                            // Update UI for successful verification
                            $("#pan_number, #company_name, #company_email").prop('readonly', true);
                            hideElement("#verify_pan_btn");
                            showElement("#pan_edit_btn");
                            $("#pan_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');

                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: {
                                    page: 'insertvendorsandvendorregisters',
                                    company_name: company_name,
                                    display_name: pan_holder_name,
                                    pan_no: pan_no,
                                    company_email: company_email,
                                    transaction_table: transaction_table,
                                    vendor_id: vendor_id,
                                    verification_type: 'PAN',
                                    api_hit_id: result.api_hit_id,
                                },
                                async: false,
                                success: function (response) {
                                    try {
                                        console.log('pan data response:', response);
                                        var obj = JSON.parse(response);
                                        console.log('parsed response object:', obj);
                                        
                                        // Handle different response structures
                                        var lastID = '0';
                                        if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                                            lastID = obj.all_result.res.LASTID || '0';
                                            console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                                        } else if (obj.all_result && obj.all_result.LASTID) {
                                            lastID = obj.all_result.LASTID || '0';
                                            console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                                        } else if (obj.res && obj.res.LASTID) {
                                            lastID = obj.res.LASTID || '0';
                                            console.log('Found LASTID in obj.res.LASTID:', lastID);
                                        } else if (obj.LASTID) {
                                            lastID = obj.LASTID || '0';
                                            console.log('Found LASTID in obj.LASTID:', lastID);
                                        } else {
                                            console.log('LASTID not found in expected locations');
                                        }
                                        
                                        console.log('Final extracted LASTID:', lastID);
                                        
                                        if (lastID != '0') {
                                            showNotification("PAN verified successfully!", "success");
                                            
                                            // Close popup and reload parent page
                                            setTimeout(function() {
                                                closeModal(true);
                                                // Show notification in parent window
                                                notifyParent("PAN is verified", "success");
                                            }, 2000);
                                        } else {
                                            // Check if there's an UpdatedRes indicating update success
                                            var updatedRes = '0';
                                            if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                                                updatedRes = obj.all_result.res.UpdatedRes || '0';
                                                console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                                            } else if (obj.res && obj.res.UpdatedRes) {
                                                updatedRes = obj.res.UpdatedRes || '0';
                                                console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                                            } else if (obj.UpdatedRes) {
                                                updatedRes = obj.UpdatedRes || '0';
                                                console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                                            }
                                            
                                            console.log('Final extracted UpdatedRes:', updatedRes);
                                            
                                            if (updatedRes != '0') {
                                                showNotification("PAN verified and updated successfully!", "success");
                                                
                                                // Close popup and reload parent page
                                                setTimeout(function() {
                                                    closeModal(true);
                                                    // Show notification in parent window
                                                    notifyParent("PAN is verified", "success");
                                                }, 2000);
                                            } else {
                                                // Check if the response indicates success in other ways
                                                var status = '';
                                                if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                                                    status = obj.all_result.res.status;
                                                } else if (obj.res && obj.res.status) {
                                                    status = obj.res.status;
                                                } else if (obj.status) {
                                                    status = obj.status;
                                                }
                                                
                                                console.log('Response status:', status);
                                                
                                                if (status === 'success' || status === 'sucess') {
                                                    showNotification("PAN verified successfully!", "success");
                                                    
                                                    // Close popup and reload parent page
                                                    setTimeout(function() {
                                                        closeModal(true);
                                                        // Show notification in parent window
                                                        notifyParent("PAN is verified", "success");
                                                    }, 2000);
                                                } else {
                                                    showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                                                }
                                            }
                                        }
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                        console.error('Response that caused error:', response);
                                        showNotification("Error processing response: " + err.message, "error");
                                    }
                                },
                                error: function () {
                                    showNotification("Error saving verification data", "error");
                                }
                            });

                        } else {
                            verificationSteps.pan.completed = false;
                            verificationSteps.pan.nameMatch = false;

                            $("#pan_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#pan_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> PAN Verification Done - Data Mismatch');
                            $("#pan_confirmation_section p").html('PAN details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');
                            showNotification("Verification completed but company name doesn't match with PAN holder name. Please check the data.", "warning");
                            
                            // Show change button for failed verification
                            $("#verify_pan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                            showElement("#pan_edit_btn");
                            $("#pan_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
                        }

                    } else {
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "PAN Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }
                        
                        showNotification(errorMessage, "error");
                        
                        // Show change button for failed verification
                        $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        showElement("#pan_edit_btn");
                        $("#pan_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Parse Error");
                    showNotification("PAN Verification Parse Error: " + e.message, "error");
                    
                    // Show change button for parse error
                    $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    showElement("#pan_edit_btn");
                    $("#pan_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Error occurred");
                
                var errorMessage = "PAN Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }
                
                showNotification(errorMessage, "error");
                
                // Show change button for network error
                $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                showElement("#pan_edit_btn");
                $("#pan_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }

    // Bank Verification function
    function verifyBank() {
        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        // Validate Required fields
        if (!validateIFSC(ifsc)) {
            showNotification("Please enter a valid IFSC code", "warning");
            return;
        }

        if (!validateBankAccount(account)) {
            showNotification("Please enter a valid bank account number", "warning");
            return;
        }

        if ($("#verify_bank_btn").prop('disabled') || verificationSteps.bank.completed) {
            return;
        }

        $("#ifsc_code, #bank_account_no").prop('readonly', true);
        $("#verify_bank_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        showElement("#bank_verification_fields");
        $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status, #bank_verified_bank_name").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'account_number': account,
                'ifsc': ifsc,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'BANKACVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);
                    console.log('Bank verification result:', result);

                    if (result && result.response_code == "200" && result.result && result.result.verification_status == 'VERIFIED') {
                        $("#bank_verified_ifsc").val(ifsc);
                        $("#bank_verified_account").val(account);
                        $("#bank_verified_name").val(result.result.beneficiary_name || "N/A");
                        $("#bank_verified_status").val("Verified Successfully");
                        $("#bank_verified_bank_name").val($("#bank_name").val() || "Not provided");

                        verificationSteps.bank.completed = true;
                        verificationSteps.bank.data = result;

                        // Show manual confirmation section
                        showElement("#bank_manual_confirmation_section");
                        $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Pending Confirmation');

                        $("#ifsc_code, #bank_account_no").prop('readonly', true);
                        showElement("#bank_edit_btn");
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-secondary');

                        // Store verification data for later use
                        window.bankVerificationData = {
                            ifsc_code: ifsc,
                            bank_account_no: account,
                            bank_name: $("#bank_name").val() || '',
                            transaction_table: transaction_table,
                            vendor_id: vendor_id,
                            verification_type: 'BANK',
                            api_hit_id: result.api_hit_id,
                            result: result
                        };

                        // Populate confirmation display fields
                        $("#confirm_ifsc_display").text(ifsc);
                        $("#confirm_account_display").text(account);
                        $("#confirm_name_display").text(result.result.beneficiary_name || "N/A");
                        $("#confirm_bank_name_display").text($("#bank_name").val() || "Not provided");

                        showNotification("Bank Account Verified Successfully - Please confirm the details", "success");

                    } else {
                        $("#bank_verified_ifsc").val("Verification Failed");
                        $("#bank_verified_account").val("Verification Failed");
                        $("#bank_verified_name").val("Verification Failed");
                        $("#bank_verified_status").val(result.response_message || "Verification Failed");
                        $("#bank_verified_bank_name").val($("#bank_name").val() || "Not provided");

                        var errorMessage = "Bank Account Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }

                        showNotification(errorMessage, "error");

                        // Show change button for failed verification
                        $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        showElement("#bank_edit_btn");
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status, #bank_verified_bank_name").val("Parse Error");
                    showNotification("Bank Verification Parse Error: " + e.message, "error");

                    // Show change button for parse error
                    $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    showElement("#bank_edit_btn");
                    $("#bank_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status, #bank_verified_bank_name").val("Error occurred");

                var errorMessage = "Bank Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }

                showNotification(errorMessage, "error");

                // Show change button for network error
                $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                showElement("#bank_edit_btn");
                $("#bank_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }

    // TAN Verification function
    function verifyTAN() {
        var tan_no = $("#tan_number").val();

        // Validate Required fields
        if (!validateTAN(tan_no)) {
            showNotification("Please enter a valid TAN number", "warning");
            return;
        }

        if ($("#verify_tan_btn").prop('disabled') || verificationSteps.tan.completed) {
            return;
        }

        $("#tan_number").prop('readonly', true);
        $("#verify_tan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        showElement("#tan_verification_fields");
        $("#tan_verified_number, #tan_verified_company, #tan_verified_status, #tan_verification_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'customer_tan_number': tan_no,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'ZOOPONETANLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success === true && result.result) {
                        $("#tan_verified_number").val(tan_no);
                        $("#tan_verified_company").val(result.result.user_full_name || "N/A");
                        $("#tan_verified_status").val(result.result.tan_status || "N/A");
                        $("#tan_verification_status").val("Verified Successfully");

                        verificationSteps.tan.completed = true;
                        verificationSteps.tan.data = result;

                        showElement("#tan_confirmation_section");
                        $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        $("#tan_number").prop('readonly', true);
                        hideElement("#verify_tan_btn");
                        showElement("#tan_edit_btn");
                        $("#tan_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');

                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                tan_number: tan_no,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'TAN',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log('TAN data response:', response);
                                    var obj = JSON.parse(response);
                                    console.log('parsed response object:', obj);
                                    
                                    // Handle different response structures
                                    var lastID = '0';
                                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                                        lastID = obj.result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                                        lastID = obj.all_result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.LASTID) {
                                        lastID = obj.all_result.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                                    } else if (obj.res && obj.res.LASTID) {
                                        lastID = obj.res.LASTID || '0';
                                        console.log('Found LASTID in obj.res.LASTID:', lastID);
                                    } else if (obj.LASTID) {
                                        lastID = obj.LASTID || '0';
                                        console.log('Found LASTID in obj.LASTID:', lastID);
                                    } else {
                                        console.log('LASTID not found in expected locations');
                                    }
                                    
                                    console.log('Final extracted LASTID:', lastID);
                                    
                                    if (lastID != '0') {
                                        showNotification("TAN verified successfully!", "success");
                                        
                                        // Close popup and reload parent page
                                        setTimeout(function() {
                                            closeModal(true);
                                            // Show notification in parent window
                                            notifyParent("TAN is verified", "success");
                                        }, 2000);
                                    } else {
                                        // Check if there's an UpdatedRes indicating update success
                                        var updatedRes = '0';
                                        if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                                            updatedRes = obj.result.res.UpdatedRes || '0';
                                            console.log('Found UpdatedRes in obj.result.res.UpdatedRes:', updatedRes);
                                        } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                                            updatedRes = obj.all_result.res.UpdatedRes || '0';
                                            console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                                        } else if (obj.res && obj.res.UpdatedRes) {
                                            updatedRes = obj.res.UpdatedRes || '0';
                                            console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                                        } else if (obj.UpdatedRes) {
                                            updatedRes = obj.UpdatedRes || '0';
                                            console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                                        }
                                        
                                        console.log('Final extracted UpdatedRes:', updatedRes);
                                        
                                        if (updatedRes != '0') {
                                            showNotification("TAN verified and updated successfully!", "success");
                                            
                                            // Close popup and reload parent page
                                            setTimeout(function() {
                                                closeModal(true);
                                                // Show notification in parent window
                                                notifyParent("TAN is verified", "success");
                                            }, 2000);
                                        } else {
                                            // Check if the response indicates success in other ways
                                            var status = '';
                                            if (obj.result && obj.result.res && obj.result.res.status) {
                                                status = obj.result.res.status;
                                            } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                                                status = obj.all_result.res.status;
                                            } else if (obj.res && obj.res.status) {
                                                status = obj.res.status;
                                            } else if (obj.status) {
                                                status = obj.status;
                                            }
                                            
                                            console.log('Response status:', status);
                                            
                                            if (status === 'success' || status === 'sucess') {
                                                showNotification("TAN verified successfully!", "success");
                                                
                                                // Close popup and reload parent page
                                                setTimeout(function() {
                                                    closeModal(true);
                                                    // Show notification in parent window
                                                    notifyParent("TAN is verified", "success");
                                                }, 2000);
                                            } else {
                                                showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                                            }
                                        }
                                    }
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                    console.error('Response that caused error:', response);
                                    showNotification("Error processing response: " + err.message, "error");
                                }
                            },
                            error: function () {
                                showNotification("Error saving verification data", "error");
                            }
                        });

                    } else {
                        $("#tan_verified_number").val("Verification Failed");
                        $("#tan_verified_company").val("Verification Failed");
                        $("#tan_verified_status").val("Verification Failed");
                        $("#tan_verification_status").val("Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "TAN Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }
                        
                        showNotification(errorMessage, "error");
                        
                        // Show change button for failed verification
                        $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        $("#tan_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#tan_verified_number").val("Parse Error");
                    $("#tan_verified_company").val("Parse Error");
                    $("#tan_verified_status").val("Parse Error");
                    $("#tan_verification_status").val("Parse Error");
                    showNotification("TAN Verification Parse Error: " + e.message, "error");
                    
                    // Show change button for parse error
                    $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    $("#tan_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#tan_verified_number").val("Error occurred");
                $("#tan_verified_company").val("Error occurred");
                $("#tan_verified_status").val("Error occurred");
                $("#tan_verification_status").val("Error occurred");
                
                var errorMessage = "TAN Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }
                
                showNotification(errorMessage, "error");
                
                // Show change button for network error
                $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                $("#tan_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }

    // MSME Verification function
    function verifyMSME() {
        var msme_number = $("#msme_number").val();

        // Validate Required fields
        if (!validateMSME(msme_number)) {
            showNotification("Please enter a valid MSME/Udyam Registration number", "warning");
            return;
        }

        if ($("#verify_msme_btn").prop('disabled') || verificationSteps.msme.completed) {
            return;
        }

        $("#msme_number").prop('readonly', true);
        $("#verify_msme_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        showElement("#msme_verification_fields");
        $("#msme_verified_enterprise_name, #msme_verified_major_activity, #msme_verified_organization_type, #msme_verified_social_category, #msme_verified_commencement_date, #msme_verified_dic_name, #msme_verified_registration_date, #msme_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'udyog_aadhaar': msme_number,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'UDYOGAADHAAR',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result && result.response_code == "200" && result.result) {
                        $("#msme_verified_enterprise_name").val(result.result.name_of_enterprise || "N/A");
                        $("#msme_verified_major_activity").val(result.result.major_activity || "N/A");
                        $("#msme_verified_organization_type").val(result.result.organization_type || "N/A");
                        $("#msme_verified_social_category").val(result.result.social_category || "N/A");
                        $("#msme_verified_commencement_date").val(result.result.date_of_commencement || "N/A");
                        $("#msme_verified_dic_name").val(result.result.dic_name || "N/A");
                        $("#msme_verified_registration_date").val(result.result.applied_date || "N/A");
                        $("#msme_verified_status").val(result.response_message || "Verified");

                        verificationSteps.msme.completed = true;
                        verificationSteps.msme.data = result;

                        // Store verification data for later use
                        window.msmeVerificationData = {
                            msme_number: msme_number,
                            transaction_table: transaction_table,
                            vendor_id: vendor_id,
                            verification_type: 'MSME',
                            api_hit_id: result.api_hit_id,
                            result: result
                        };

                        // Check company name match
                        var company_name_from_record_local = company_name_from_record;
                        var enterprise_name = result.result.name_of_enterprise || "";
                        var unit_name = "";
                        
                        // Check if plant_address exists and has unit_name
                        if (result.result.plant_address && result.result.plant_address.length > 0) {
                            unit_name = result.result.plant_address[0].unit_name || "";
                        }

                        var companyMatched = false;
                        var matchedWith = "";

                        // Check if company name matches with enterprise name or unit name
                        if (company_name_from_record_local.toLowerCase().trim() === enterprise_name.toLowerCase().trim()) {
                            companyMatched = true;
                            matchedWith = "Enterprise Name";
                        } else if (unit_name && company_name_from_record_local.toLowerCase().trim() === unit_name.toLowerCase().trim()) {
                            companyMatched = true;
                            matchedWith = "Unit Name";
                        }

                        if (companyMatched) {
                            // Company name matches - proceed automatically
                            showElement("#msme_confirmation_section");
                            $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                            $("#msme_number").prop('readonly', true);
                            $("#verify_msme_btn").hide();
                            $("#msme_edit_btn").show().text('Change Info').removeClass('btn-warning').addClass('btn-success');

                            showNotification("MSME Details Verified Successfully - Company name matched with " + matchedWith, "success");
                            
                            // Automatically proceed to save data
                            proceedWithMSMESave();
                        } else {
                            // Company name doesn't match - show confirmation dialog
                            $("#msme_confirmation_section").hide();
                            showMSMEConfirmationDialog(company_name_from_record_local, enterprise_name, unit_name);
                        }

                        // Don't make the AJAX call here anymore - it will be called from confirmation functions


                    } else {
                        $("#msme_verified_enterprise_name").val("Verification Failed");
                        $("#msme_verified_major_activity").val("Verification Failed");
                        $("#msme_verified_organization_type").val("Verification Failed");
                        $("#msme_verified_social_category").val("Verification Failed");
                        $("#msme_verified_commencement_date").val("Verification Failed");
                        $("#msme_verified_dic_name").val("Verification Failed");
                        $("#msme_verified_registration_date").val("Verification Failed");
                        $("#msme_verified_status").val(result.response_message || "Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "MSME Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }
                        
                        showNotification(errorMessage, "error");
                        
                        // Show change button for failed verification
                        $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        $("#msme_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#msme_verified_enterprise_name").val("Parse Error");
                    $("#msme_verified_major_activity").val("Parse Error");
                    $("#msme_verified_organization_type").val("Parse Error");
                    $("#msme_verified_social_category").val("Parse Error");
                    $("#msme_verified_commencement_date").val("Parse Error");
                    $("#msme_verified_dic_name").val("Parse Error");
                    $("#msme_verified_registration_date").val("Parse Error");
                    $("#msme_verified_status").val("Parse Error");
                    showNotification("MSME Verification Parse Error: " + e.message, "error");
                    
                    // Show change button for parse error
                    $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    $("#msme_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#msme_verified_enterprise_name").val("Error occurred");
                $("#msme_verified_major_activity").val("Error occurred");
                $("#msme_verified_organization_type").val("Error occurred");
                $("#msme_verified_social_category").val("Error occurred");
                $("#msme_verified_commencement_date").val("Error occurred");
                $("#msme_verified_dic_name").val("Error occurred");
                $("#msme_verified_registration_date").val("Error occurred");
                $("#msme_verified_status").val("Error occurred");
                
                var errorMessage = "MSME Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }
                
                showNotification(errorMessage, "error");
                
                // Show change button for network error
                $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                $("#msme_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }

    // GST Verification function
    function verifyGST() {
        console.log('GST Verification started');
        var gstin = $("#gstin").val();

        // Validate Required fields
        if (!validateGST(gstin)) {
            showNotification("Please enter a valid GST number", "warning");
            return;
        }

        if ($("#verify_gst_btn").prop('disabled') || verificationSteps.gst.completed) {
            return;
        }

        $("#gstin").prop('readonly', true);
        $("#verify_gst_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        $("#gst_verification_fields").show();
        $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'business_gstin_number': gstin,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'GSTVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync', 
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success === true && result.result) {
                        $("#gstin_legal_name").val(result.result.legal_name || "N/A");
                        $("#gstin_trade_name").val(result.result.trade_name || "N/A");
                        $("#gstin_registration_status").val(result.result.current_registration_status || "N/A");
                        $("#gstin_taxpayer_type").val(result.result.tax_payer_type || "N/A");
                        $("#gstin_business_nature").val(result.result.business_nature ? result.result.business_nature.join(', ') : "N/A");
                        $("#gstin_registration_date").val(result.result.register_date || "N/A");

                        var address = "N/A";
                        var full_address = "";
                        var state = "";
                        var city = "";
                        var location = "";
                        var pincode = "";
                        
                        if (result.result.primary_business_address) {
                            var addr = result.result.primary_business_address;
                            var addressParts = [];

                            full_address = addr.full_address || ""; 
                            state = addr.state_code || ""; 
                            city = addr.district || ""; 
                            location = addr.location || ""; 
                            pincode = addr.pincode || ""; 

                            if (addr.building_name) addressParts.push(addr.building_name);
                            if (addr.building_number) addressParts.push(addr.building_number);
                            if (addr.street) addressParts.push(addr.street);
                            if (addr.city) addressParts.push(addr.city);
                            if (addr.district) addressParts.push(addr.district);
                            if (addr.state) addressParts.push(addr.state);
                            if (addr.pincode) addressParts.push(addr.pincode);
                            address = addressParts.length > 0 ? addressParts.join(', ') : "N/A";
                        }
                        $("#gstin_business_address").val(address);

                        verificationSteps.gst.completed = true;
                        verificationSteps.gst.data = result;

                        $("#gst_confirmation_section").show();
                        $("#verify_gst_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        $("#gstin").prop('readonly', true);
                        $("#verify_gst_btn").hide();
                        $("#gst_edit_btn").show().text('Change Info').removeClass('btn-warning').addClass('btn-success');

                        // Store GST verification data for comparison
                        window.gstVerificationData = {
                            gstin: gstin,
                            legal_name: result.result.legal_name || "N/A",
                            trade_name: result.result.trade_name || "N/A",
                            api_hit_id: result.api_hit_id,
                            verification_result: result,
                            address: full_address,
                            state: state,
                            city: city,
                            post_code: pincode,
                            location: location
                        };

                        // Show company comparison message instead of direct save
                        showGSTComparisonMessage(result, 'change');
                        
                        return; // Don't proceed with AJAX call yet
                        
                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                gstin: gstin,
                                address: full_address,
                                state: state,
                                city: city,
                                post_code: pincode,
                                location: location,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'GST',
                                gst_type: 'old',
                                gst_default: 'old',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log('GST data response:', response);
                                    var obj = JSON.parse(response);
                                    console.log('parsed response object:', obj);
                                    
                                    // Handle different response structures for GST
                                    var lastID = '0';
                                    var updatedRes = '0';
                                    var status = '';
                                    
                                    // Check for LASTID in various response structures
                                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                                        lastID = obj.result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                                        lastID = obj.all_result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.LASTID) {
                                        lastID = obj.all_result.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                                    } else if (obj.res && obj.res.LASTID) {
                                        lastID = obj.res.LASTID || '0';
                                        console.log('Found LASTID in obj.res.LASTID:', lastID);
                                    } else if (obj.LASTID) {
                                        lastID = obj.LASTID || '0';
                                        console.log('Found LASTID in obj.LASTID:', lastID);
                                    }
                                    
                                    // Check for UpdatedRes in various response structures
                                    if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                                        updatedRes = obj.result.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.result.res.UpdatedRes:', updatedRes);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                                        updatedRes = obj.all_result.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                                    } else if (obj.res && obj.res.UpdatedRes) {
                                        updatedRes = obj.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                                    } else if (obj.UpdatedRes) {
                                        updatedRes = obj.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                                    }
                                    
                                    // Check for status in various response structures
                                    if (obj.result && obj.result.res && obj.result.res.status) {
                                        status = obj.result.res.status;
                                        console.log('Found status in obj.result.res.status:', status);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                                        status = obj.all_result.res.status;
                                        console.log('Found status in obj.all_result.res.status:', status);
                                    } else if (obj.res && obj.res.status) {
                                        status = obj.res.status;
                                        console.log('Found status in obj.res.status:', status);
                                    } else if (obj.status) {
                                        status = obj.status;
                                        console.log('Found status in obj.status:', status);
                                    }
                                    
                                    console.log('Final extracted values - LASTID:', lastID, 'UpdatedRes:', updatedRes, 'Status:', status);
                                    
                                    // Check for successful verification
                                    if (lastID != '0' || updatedRes != '0' || status === 'success' || status === 'sucess') {
                                        var successMessage = "GST verified successfully!";
                                        if (lastID != '0') {
                                            successMessage = "GST verified and saved successfully!";
                                        } else if (updatedRes != '0') {
                                            successMessage = "GST verified and updated successfully!";
                                        }
                                        
                                        showNotification(successMessage, "success");
                                        
                                        // Close popup and reload parent page
                                        setTimeout(function() {
                                            closeModal(true);
                                            // Show notification in parent window
                                            notifyParent("GST is verified", "success");
                                        }, 2000);
                                    } else {
                                        showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                                    }
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                    console.error('Response that caused error:', response);
                                    showNotification("Error processing response: " + err.message, "error");
                                }
                            },
                            error: function () {
                                showNotification("Error saving verification data", "error");
                            }
                        });

                        showNotification("GST Details Verified Successfully", "success");

                    } else {
                        $("#gstin_legal_name").val("Verification Failed");
                        $("#gstin_trade_name").val("Verification Failed");
                        $("#gstin_registration_status").val("Verification Failed");
                        $("#gstin_taxpayer_type").val("Verification Failed");
                        $("#gstin_business_nature").val("Verification Failed");
                        $("#gstin_registration_date").val("Verification Failed");
                        $("#gstin_business_address").val("Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "GST Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }
                        
                        showNotification(errorMessage, "error");
                        
                        // Show change button for failed verification
                        $("#verify_gst_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        $("#gst_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#gstin_legal_name").val("Parse Error");
                    $("#gstin_trade_name").val("Parse Error");
                    $("#gstin_registration_status").val("Parse Error");
                    $("#gstin_taxpayer_type").val("Parse Error");
                    $("#gstin_business_nature").val("Parse Error");
                    $("#gstin_registration_date").val("Parse Error");
                    $("#gstin_business_address").val("Parse Error");
                    showNotification("GST Verification Parse Error: " + e.message, "error");
                    
                    // Show change button for parse error
                    $("#verify_gst_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    $("#gst_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#gstin_legal_name").val("Error occurred");
                $("#gstin_trade_name").val("Error occurred");
                $("#gstin_registration_status").val("Error occurred");
                $("#gstin_taxpayer_type").val("Error occurred");
                $("#gstin_business_nature").val("Error occurred");
                $("#gstin_registration_date").val("Error occurred");
                $("#gstin_business_address").val("Error occurred");
                
                var errorMessage = "GST Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }
                
                showNotification(errorMessage, "error");
                 
                $("#verify_gst_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                $("#gst_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }

    function verifyNewGST() {
        console.log('New GST Verification started');
        var gstin = $("#gstin").val();
 
        if (!validateGST(gstin)) {
            showNotification("Please enter a valid GST number", "warning");
            return;
        }

        if ($("#verify_gst_new_btn").prop('disabled') || verificationSteps.gst.completed) {
            return;
        }

        $("#gstin").prop('readonly', true);
        $("#verify_gst_new_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        $("#gst_verification_fields_new").show();
        $("#gstin_legal_name_new, #gstin_trade_name_new, #gstin_registration_status_new, #gstin_taxpayer_type_new, #gstin_business_nature_new, #gstin_registration_date_new, #gstin_business_address_new").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'business_gstin_number': gstin,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'GSTVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hereby declare my consent agreement for fetching my information via ZOOP API',
                'mode': 'sync', 
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);

                    if (result.success === true && result.result) {
                        $("#gstin_legal_name_new").val(result.result.legal_name || "N/A");
                        $("#gstin_trade_name_new").val(result.result.trade_name || "N/A");
                        $("#gstin_registration_status_new").val(result.result.current_registration_status || "N/A");
                        $("#gstin_taxpayer_type_new").val(result.result.tax_payer_type || "N/A");
                        $("#gstin_business_nature_new").val(result.result.business_nature ? result.result.business_nature.join(', ') : "N/A");
                        $("#gstin_registration_date_new").val(result.result.register_date || "N/A");

                        var address = "N/A";
                        var full_address = "";
                        var state = "";
                        var city = "";
                        var location = "";
                        var pincode = "";
                        
                        if (result.result.primary_business_address) {
                            var addr = result.result.primary_business_address;
                            var addressParts = [];

                            full_address = addr.full_address || ""; 
                            state = addr.state_code || ""; 
                            city = addr.district || ""; 
                            location = addr.location || ""; 
                            pincode = addr.pincode || ""; 
                        
                            if (addr.building_name) addressParts.push(addr.building_name);
                            if (addr.building_number) addressParts.push(addr.building_number);
                            if (addr.street) addressParts.push(addr.street);
                            if (addr.city) addressParts.push(addr.city);
                            if (addr.district) addressParts.push(addr.district);
                            if (addr.state) addressParts.push(addr.state);
                            if (addr.pincode) addressParts.push(addr.pincode);
                            address = addressParts.length > 0 ? addressParts.join(', ') : "N/A";
                        }
                        $("#gstin_business_address_new").val(address);

                        verificationSteps.gst.completed = true;
                        verificationSteps.gst.data = result;

                        $("#gst_confirmation_section_new").show();
                        $("#verify_gst_new_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        $("#gstin").prop('readonly', true);
                        $("#verify_gst_new_btn").hide();
                        $("#gst_new_edit_btn").show().text('Change Info').removeClass('btn-warning').addClass('btn-success');
 
                        var business_location = $("#business_location").val();
                        var gst_purpose = $("#gst_purpose").val();
 
                        window.gstVerificationData = {
                            gstin: gstin,
                            legal_name: result.result.legal_name || "N/A",
                            trade_name: result.result.trade_name || "N/A",
                            api_hit_id: result.api_hit_id,
                            verification_result: result,
                            address: full_address,
                            state: state,
                            city: city,
                            post_code: pincode,
                            location: location
                        };
 
                        showGSTComparisonMessage(result, 'add_new');
                        
                        return;
                        
                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                gstin: gstin,
                                address: full_address,
                                state: state,
                                city: city,
                                post_code: pincode,
                                location: location,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'GSTIN',
                                gst_type: 'new',
                                gst_default: 'new',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log('New GST data response:', response);
                                    var obj = JSON.parse(response);
                                    console.log('parsed response object:', obj);
                                     
                                    var lastID = '0';
                                    var updatedRes = '0';
                                    var status = '';
                                     
                                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                                        lastID = obj.result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                                        lastID = obj.all_result.res.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                                    } else if (obj.all_result && obj.all_result.LASTID) {
                                        lastID = obj.all_result.LASTID || '0';
                                        console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                                    } else if (obj.res && obj.res.LASTID) {
                                        lastID = obj.res.LASTID || '0';
                                        console.log('Found LASTID in obj.res.LASTID:', lastID);
                                    } else if (obj.LASTID) {
                                        lastID = obj.LASTID || '0';
                                        console.log('Found LASTID in obj.LASTID:', lastID);
                                    }
                                     
                                    if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                                        updatedRes = obj.result.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.result.res.UpdatedRes:', updatedRes);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                                        updatedRes = obj.all_result.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                                    } else if (obj.res && obj.res.UpdatedRes) {
                                        updatedRes = obj.res.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                                    } else if (obj.UpdatedRes) {
                                        updatedRes = obj.UpdatedRes || '0';
                                        console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                                    }
                                     
                                    if (obj.result && obj.result.res && obj.result.res.status) {
                                        status = obj.result.res.status;
                                        console.log('Found status in obj.result.res.status:', status);
                                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                                        status = obj.all_result.res.status;
                                        console.log('Found status in obj.all_result.res.status:', status);
                                    } else if (obj.res && obj.res.status) {
                                        status = obj.res.status;
                                        console.log('Found status in obj.res.status:', status);
                                    } else if (obj.status) {
                                        status = obj.status;
                                        console.log('Found status in obj.status:', status);
                                    }
                                    
                                    console.log('Final extracted values - LASTID:', lastID, 'UpdatedRes:', updatedRes, 'Status:', status);
                                     
                                    if (lastID != '0' || updatedRes != '0' || status === 'success' || status === 'sucess') {
                                        var successMessage = "New GST verified successfully!";
                                        if (lastID != '0') {
                                            successMessage = "New GST verified and saved successfully!";
                                        } else if (updatedRes != '0') {
                                            successMessage = "New GST verified and updated successfully!";
                                        }
                                        
                                        showNotification(successMessage, "success");
                                         
                                        setTimeout(function() {
                                            closeModal(true); 
                                            notifyParent("New GST is verified", "success");
                                        }, 2000);
                                    } else {
                                        showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                                    }
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                    console.error('Response that caused error:', response);
                                    showNotification("Error processing response: " + err.message, "error");
                                }
                            },
                            error: function () {
                                showNotification("Error saving verification data", "error");
                            }
                        });

                        showNotification("New GST Details Verified Successfully", "success");

                    } else {
                        $("#gstin_legal_name_new").val("Verification Failed");
                        $("#gstin_trade_name_new").val("Verification Failed");
                        $("#gstin_registration_status_new").val("Verification Failed");
                        $("#gstin_taxpayer_type_new").val("Verification Failed");
                        $("#gstin_business_nature_new").val("Verification Failed");
                        $("#gstin_registration_date_new").val("Verification Failed");
                        $("#gstin_business_address_new").val("Verification Failed");
                         
                        var errorMessage = "New GST Verification Failed";
                        if (result.api_hit_id) {
                            errorMessage += " - api_hit_id: " + result.api_hit_id;
                        }
                        if (result.response_code) {
                            errorMessage += " - response_code: " + result.response_code;
                        }
                        if (result.response_message) {
                            errorMessage += " - response_message: \"" + result.response_message + "\"";
                        }
                        
                        showNotification(errorMessage, "error");
                         
                        $("#verify_gst_new_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Verification Failed');
                        $("#gst_new_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                    }
                } catch (e) {
                    $("#gstin_legal_name_new").val("Parse Error");
                    $("#gstin_trade_name_new").val("Parse Error");
                    $("#gstin_registration_status_new").val("Parse Error");
                    $("#gstin_taxpayer_type_new").val("Parse Error");
                    $("#gstin_business_nature_new").val("Parse Error");
                    $("#gstin_registration_date_new").val("Parse Error");
                    $("#gstin_business_address_new").val("Parse Error");
                    showNotification("New GST Verification Parse Error: " + e.message, "error");
                     
                    $("#verify_gst_new_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Parse Error');
                    $("#gst_new_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
                }
            },
            error: function (xhr, status, error) {
                $("#gstin_legal_name_new").val("Error occurred");
                $("#gstin_trade_name_new").val("Error occurred");
                $("#gstin_registration_status_new").val("Error occurred");
                $("#gstin_taxpayer_type_new").val("Error occurred");
                $("#gstin_business_nature_new").val("Error occurred");
                $("#gstin_registration_date_new").val("Error occurred");
                $("#gstin_business_address_new").val("Error occurred");
                
                var errorMessage = "New GST Verification Error";
                try {
                    if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.api_hit_id) {
                            errorMessage += " - api_hit_id: " + errorResponse.api_hit_id;
                        }
                        if (errorResponse.response_code) {
                            errorMessage += " - response_code: " + errorResponse.response_code;
                        }
                        if (errorResponse.response_message) {
                            errorMessage += " - response_message: \"" + errorResponse.response_message + "\"";
                        }
                    } else {
                        errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                    }
                } catch (parseError) {
                    errorMessage += " - HTTP Status: " + xhr.status + " - " + error;
                }
                
                showNotification(errorMessage, "error");
                 
                $("#verify_gst_new_btn").removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i> Network Error');
                $("#gst_new_edit_btn").show().text('Change Info').removeClass('btn-success').addClass('btn-warning');
            }
        });
    }
 
    function confirmBankDetails() {
        if (!window.bankVerificationData) {
            showNotification("No bank verification data found", "error");
            return;
        }
 
        $("#bank_manual_confirmation_section").hide();
        $("#verify_bank_btn").html('<i class="fa fa-spinner fa-spin"></i> Updating Status...');
 
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'insertvendorsandvendorregisters',
                ifsc_code: window.bankVerificationData.ifsc_code,
                bank_account_no: window.bankVerificationData.bank_account_no,
                bank_name: window.bankVerificationData.bank_name,
                transaction_table: window.bankVerificationData.transaction_table,
                vendor_id: window.bankVerificationData.vendor_id,
                verification_type: window.bankVerificationData.verification_type,
                api_hit_id: window.bankVerificationData.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log("Bank confirmation response", response);
                    var obj = JSON.parse(response);
                    console.log('parsed response object:', obj);
 
                    var lastID = '0';
                    var updatedRes = '0';
                    var status = '';
                     
                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                        lastID = obj.result.res.LASTID || '0';
                        console.log('Found LASTID in obj.result.res.LASTID:', lastID);
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                        lastID = obj.all_result.res.LASTID || '0';
                        console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                    } else if (obj.all_result && obj.all_result.LASTID) {
                        lastID = obj.all_result.LASTID || '0';
                        console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                    } else if (obj.res && obj.res.LASTID) {
                        lastID = obj.res.LASTID || '0';
                        console.log('Found LASTID in obj.res.LASTID:', lastID);
                    } else if (obj.LASTID) {
                        lastID = obj.LASTID || '0';
                        console.log('Found LASTID in obj.LASTID:', lastID);
                    }
                     
                    if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                        updatedRes = obj.result.res.UpdatedRes || '0';
                        console.log('Found UpdatedRes in obj.result.res.UpdatedRes:', updatedRes);
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                        updatedRes = obj.all_result.res.UpdatedRes || '0';
                        console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                    } else if (obj.res && obj.res.UpdatedRes) {
                        updatedRes = obj.res.UpdatedRes || '0';
                        console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                    } else if (obj.UpdatedRes) {
                        updatedRes = obj.UpdatedRes || '0';
                        console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                    }
                     
                    if (obj.result && obj.result.res && obj.result.res.status) {
                        status = obj.result.res.status;
                        console.log('Found status in obj.result.res.status:', status);
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                        status = obj.all_result.res.status;
                        console.log('Found status in obj.all_result.res.status:', status);
                    } else if (obj.res && obj.res.status) {
                        status = obj.res.status;
                        console.log('Found status in obj.res.status:', status);
                    } else if (obj.status) {
                        status = obj.status;
                        console.log('Found status in obj.status:', status);
                    }
                    
                    console.log('Final extracted values - LASTID:', lastID, 'UpdatedRes:', updatedRes, 'Status:', status);
 
                    if (lastID != '0' || updatedRes != '0' || status === 'success' || status === 'sucess') {
                        $("#bank_confirmation_section").show();
                        $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-success');

                        verificationSteps.bank.completed = true;
                        verificationSteps.bank.data = window.bankVerificationData.result;

                        var successMessage = "Bank Account verified successfully!";
                        if (lastID != '0') {
                            successMessage = "Bank Account verified and saved successfully!";
                        } else if (updatedRes != '0') {
                            successMessage = "Bank Account verified and updated successfully!";
                        }
                        
                        showNotification(successMessage, "success");
 
                        setTimeout(function() {
                            closeModal(true); 
                            notifyParent("Bank Account is verified", "success");
                        }, 2000);
                    } else {
                        showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                    }
 
                    window.bankVerificationData = null;

                } catch (err) {
                    console.error('Bank confirmation parse error:', err);
                    showNotification("Error processing bank confirmation", "error");
                }
            },
            error: function (xhr, status, error) {
                console.error('Bank confirmation error:', error);
                showNotification("Error confirming bank details", "error");
 
                $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank');
                $("#bank_manual_confirmation_section").show();
            }
        });
    }

    function cancelBankConfirmation() { 
        $("#bank_manual_confirmation_section").hide();
 
        $("#bank_verification_fields").hide();
        $("#ifsc_code, #bank_account_no").prop('readonly', false);
        $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
        $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
 
        $("#bank_verified_ifsc").val("");
        $("#bank_verified_account").val("");
        $("#bank_verified_name").val("");
        $("#bank_verified_status").val("");
        $("#bank_verified_bank_name").val("");
 
        window.bankVerificationData = null;
 
        verificationSteps.bank.completed = false;
        verificationSteps.bank.data = null;

        showNotification("Bank verification cancelled. You can verify again.", "info");
    }
 
    function showMSMEConfirmationDialog(companyName, enterpriseName, unitName) {
        var content = '<p><strong>Verification completed but company name does not match:</strong></p>';
        content += '<div class="row mb-3">';
        content += '<div class="col-md-4"><strong>Your Company Name:</strong><br>' + companyName + '</div>';
        content += '<div class="col-md-4"><strong>MSME Enterprise Name:</strong><br>' + enterpriseName + '</div>';
        if (unitName) {
            content += '<div class="col-md-4"><strong>MSME Unit Name:</strong><br>' + unitName + '</div>';
        }
        content += '</div>';
        content += '<p class="text-warning"><i class="fa fa-exclamation-triangle"></i> <strong>Company name does not match with MSME records.</strong></p>';
        content += '<p>Do you want to proceed with saving this MSME verification?</p>';
        
        $("#msme_comparison_content").html(content);
        $("#msme_manual_confirmation_section").show();
        
        $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Pending Confirmation');
        $("#msme_number").prop('readonly', true);
        $("#msme_edit_btn").show().text('Change Info').removeClass('btn-warning').addClass('btn-secondary');
        
        showNotification("MSME verification completed but company name doesn't match. Please confirm to proceed.", "warning");
    }

    function proceedWithMSMESave() {
        if (!window.msmeVerificationData) {
            showNotification("No MSME verification data found", "error");
            return;
        }
 
        $("#msme_manual_confirmation_section").hide();
        $("#verify_msme_btn").html('<i class="fa fa-spinner fa-spin"></i> Saving...');
 
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'insertvendorsandvendorregisters',
                msme_number: window.msmeVerificationData.msme_number,
                transaction_table: window.msmeVerificationData.transaction_table,
                vendor_id: window.msmeVerificationData.vendor_id,
                verification_type: window.msmeVerificationData.verification_type,
                api_hit_id: window.msmeVerificationData.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log('MSME data response:', response);
                    var obj = JSON.parse(response);
                    console.log('parsed response object:', obj);
                     
                    var lastID = '0';
                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                        lastID = obj.result.res.LASTID || '0';
                        console.log('Found LASTID in obj.result.res.LASTID:', lastID);
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                        lastID = obj.all_result.res.LASTID || '0';
                        console.log('Found LASTID in obj.all_result.res.LASTID:', lastID);
                    } else if (obj.all_result && obj.all_result.LASTID) {
                        lastID = obj.all_result.LASTID || '0';
                        console.log('Found LASTID in obj.all_result.LASTID:', lastID);
                    } else if (obj.res && obj.res.LASTID) {
                        lastID = obj.res.LASTID || '0';
                        console.log('Found LASTID in obj.res.LASTID:', lastID);
                    } else if (obj.LASTID) {
                        lastID = obj.LASTID || '0';
                        console.log('Found LASTID in obj.LASTID:', lastID);
                    } else {
                        console.log('LASTID not found in expected locations');
                    }
                    
                    console.log('Final extracted LASTID:', lastID);
                    
                    if (lastID != '0') {
                        $("#msme_confirmation_section").show();
                        $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#msme_number").prop('readonly', true);
                        $("#verify_msme_btn").hide();
                        $("#msme_edit_btn").show().text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                        
                        showNotification("MSME verified successfully!", "success");
                         
                        setTimeout(function() {
                            closeModal(true); 
                            notifyParent("MSME is verified", "success");
                        }, 2000);
                    } else { 
                        var updatedRes = '0';
                        if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                            updatedRes = obj.result.res.UpdatedRes || '0';
                            console.log('Found UpdatedRes in obj.result.res.UpdatedRes:', updatedRes);
                        } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                            updatedRes = obj.all_result.res.UpdatedRes || '0';
                            console.log('Found UpdatedRes in obj.all_result.res.UpdatedRes:', updatedRes);
                        } else if (obj.res && obj.res.UpdatedRes) {
                            updatedRes = obj.res.UpdatedRes || '0';
                            console.log('Found UpdatedRes in obj.res.UpdatedRes:', updatedRes);
                        } else if (obj.UpdatedRes) {
                            updatedRes = obj.UpdatedRes || '0';
                            console.log('Found UpdatedRes in obj.UpdatedRes:', updatedRes);
                        }
                        
                        console.log('Final extracted UpdatedRes:', updatedRes);
                        
                        if (updatedRes != '0') {
                            $("#msme_confirmation_section").show();
                            $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                            $("#msme_number").prop('readonly', true);
                            $("#verify_msme_btn").hide();
                            $("#msme_edit_btn").show().text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                            
                            showNotification("MSME verified and updated successfully!", "success");
                             
                            setTimeout(function() {
                                closeModal(true); 
                                notifyParent("MSME is verified", "success");
                            }, 2000);
                        } else { 
                            var status = '';
                            if (obj.result && obj.result.res && obj.result.res.status) {
                                status = obj.result.res.status;
                            } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                                status = obj.all_result.res.status;
                            } else if (obj.res && obj.res.status) {
                                status = obj.res.status;
                            } else if (obj.status) {
                                status = obj.status;
                            }
                            
                            console.log('Response status:', status);
                            
                            if (status === 'success' || status === 'sucess') {
                                $("#msme_confirmation_section").show();
                                $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                                $("#msme_number").prop('readonly', true);
                                $("#verify_msme_btn").hide();
                                $("#msme_edit_btn").show().text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                                
                                showNotification("MSME verified successfully!", "success");
                                 
                                setTimeout(function() {
                                    closeModal(true); 
                                    notifyParent("MSME is verified", "success");
                                }, 2000);
                            } else {
                                showNotification("Verification completed but response unclear. Please check if data was saved.", "warning");
                            }
                        }
                    }
                } catch (err) {
                    console.error('Insert vendor parse error:', err);
                    console.error('Response that caused error:', response);
                    showNotification("Error processing response: " + err.message, "error");
                }
            },
            error: function () {
                showNotification("Error saving verification data", "error");
                 
                $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
                $("#msme_manual_confirmation_section").show();
            }
        });
    }

    function cancelMSMEConfirmation() { 
        $("#msme_manual_confirmation_section").hide();
 
        $("#msme_verification_fields").hide();
        $("#msme_number").prop('readonly', false);
        $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
        $("#msme_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
 
        $("#msme_verified_enterprise_name").val("");
        $("#msme_verified_major_activity").val("");
        $("#msme_verified_organization_type").val("");
        $("#msme_verified_social_category").val("");
        $("#msme_verified_commencement_date").val("");
        $("#msme_verified_dic_name").val("");
        $("#msme_verified_registration_date").val("");
        $("#msme_verified_status").val("");
 
        window.msmeVerificationData = null;
 
        verificationSteps.msme.completed = false;
        verificationSteps.msme.data = null;

        showNotification("MSME verification cancelled. You can verify again.", "info");
    }
 
    function saveVerificationData() {
        var form = document.getElementById('verificationForm');
        if (!form) {
            showNotification('Form not found', 'error');
            return;
        }

        var formData = new FormData(form);
         
        var isValid = true;
        var requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }
 
        var saveBtn = event.target;
        var originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;
         
        jQuery.ajax({
            url: 'ajax.php?mode=save_verification_data',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        showNotification('Verification data saved successfully!', 'success');
                         
                        setTimeout(function() {
                            closeModal(true); 
                            notifyParent(verification_type.toUpperCase() + " is verified", "success");
                        }, 2000);
                        
                    } else {
                        showNotification(result.message || 'Failed to save verification data', 'error');
                        saveBtn.innerHTML = originalText;
                        saveBtn.disabled = false;
                    }
                } catch (e) {
                    showNotification('Invalid response from server', 'error');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error saving data: ' + error, 'error');
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });
    }
 
    $(document).ready(function() {
        console.log('Verification popup loaded for:', verification_type, action_type);
         
        setTimeout(function() {
            $('input:not([type="hidden"]):first').focus();
        }, 500); 
        if (verification_type && !verificationSteps[verification_type]) {
            verificationSteps[verification_type] = { 
                completed: false, 
                data: null, 
                nameMatch: null, 
                skipped: false 
            };
        }
 
        if (verification_type === 'bank') {
            $('#ifsc_code, #bank_account_no').on('input', function() {
                var ifsc = $('#ifsc_code').val();
                var account = $('#bank_account_no').val();
                
                if (validateIFSC(ifsc) && validateBankAccount(account)) {
                    $('#verify_bank_btn').show();
                } else {
                    $('#verify_bank_btn').hide();
                }
            });
        }
 
        if (verification_type === 'tan') {
            $('#tan_number').on('input', function() {
                var tan = $('#tan_number').val();
                
                if (validateTAN(tan)) {
                    $('#verify_tan_btn').show();
                } else {
                    $('#verify_tan_btn').hide();
                }
            });
        }
 
        if (verification_type === 'msme') {
            $('#msme_number').on('input', function() {
                var msme = $('#msme_number').val();
                
                if (validateMSME(msme)) {
                    $('#verify_msme_btn').show();
                } else {
                    $('#verify_msme_btn').hide();
                }
            });
        }
 
        if (verification_type === 'gst' && action_type === 'add_new') {
            $('#gstin').on('input', function() {
                var gst = $('#gstin').val();
                
                if (validateGST(gst)) {
                    $('#verify_gst_new_btn').show();
                } else {
                    $('#verify_gst_new_btn').hide();
                }
            });
        } else if (verification_type === 'gst') {
            $('#gstin').on('input', function() {
                var gst = $('#gstin').val();
                
                if (validateGST(gst)) {
                    $('#verify_gst_btn').show();
                } else {
                    $('#verify_gst_btn').hide();
                }
            });
        }
    }); 
    function toggleEdit(section) {
        console.log('toggleEdit called for section:', section);

        var inputs = $('#verificationForm input:not([type="hidden"]):not(.custom_readonly)');
        var button = $('#' + section + '_edit_btn');

        console.log('Found editable inputs:', inputs.length);

        var hasReadonlyInputs = false;
        inputs.each(function () {
            if ($(this).prop('readonly') || $(this).prop('disabled')) {
                hasReadonlyInputs = true;
                return false;
            }
        });

        console.log('Has readonly inputs:', hasReadonlyInputs);

        if (hasReadonlyInputs) {
            console.log('Enabling editing mode...');
            inputs.each(function () {
                console.log('Enabling field:', $(this).attr('id') || $(this).attr('name'));
                $(this).prop('readonly', false).prop('disabled', false);
            });

            resetVerificationStatus(section);

            button.text('Change Info').removeClass('btn-success').addClass('btn-warning');
            
            if (section === 'pan') {
                $("#verify_pan_btn").show().removeClass('btn-success btn-danger btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            } else if (section === 'bank') {
                $("#verify_bank_btn").show().removeClass('btn-success btn-danger btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
            } else if (section === 'tan') {
                $("#verify_tan_btn").show().removeClass('btn-success btn-danger btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN').prop('disabled', false);
            } else if (section === 'msme') {
                $("#verify_msme_btn").show().removeClass('btn-success btn-danger btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
            }
            console.log('Button changed to Change Info (editable mode)');
        } else {
            console.log('Disabling editing mode...');
            inputs.each(function () {
                console.log('Disabling field:', $(this).attr('id') || $(this).attr('name'));
                $(this).prop('readonly', true);
            });
            button.text('Change Info').removeClass('btn-warning').addClass('btn-success');
            console.log('Button changed to Change Info (readonly mode)');
        }
    }

    function resetVerificationStatus(section) {
        console.log('Resetting verification status for section:', section);

        if (section === 'pan' && verificationSteps.pan) {
            verificationSteps.pan.completed = false;
            verificationSteps.pan.skipped = false;
            verificationSteps.pan.data = null;
            verificationSteps.pan.nameMatch = null;
 
            $('#pan_verification_fields').hide();
            $('#pan_confirmation_section').hide();
            $('#pan_verified_name, #pan_verified_type, #pan_verified_status').val('');
             
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            $('#pan_number').removeClass('is-valid is-invalid');
        } else if (section === 'bank' && verificationSteps.bank) {
            verificationSteps.bank.completed = false;
            verificationSteps.bank.skipped = false;
            verificationSteps.bank.data = null;
            verificationSteps.bank.nameMatch = null;
 
            $('#bank_verification_fields').hide();
            $('#bank_confirmation_section').hide();
            $('#bank_manual_confirmation_section').hide();
            $('#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status, #bank_verified_bank_name').val('');
             
            $('#ifsc_code').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#ifsc_code').closest('.input-group').removeClass('has-success has-error');
            $('#ifsc_code').removeClass('is-valid is-invalid');
            $('#bank_account_no').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#bank_account_no').closest('.input-group').removeClass('has-success has-error');
            $('#bank_account_no').removeClass('is-valid is-invalid');
 
            window.bankVerificationData = null;
        } else if (section === 'tan' && verificationSteps.tan) {
            verificationSteps.tan.completed = false;
            verificationSteps.tan.skipped = false;
            verificationSteps.tan.data = null;
            verificationSteps.tan.nameMatch = null;
 
            $('#tan_verification_fields').hide();
            $('#tan_confirmation_section').hide();
            $('#tan_verified_number, #tan_verified_company, #tan_verified_status, #tan_verification_status').val('');
             
            $('#tan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#tan_number').closest('.input-group').removeClass('has-success has-error');
            $('#tan_number').removeClass('is-valid is-invalid');
        } else if (section === 'msme' && verificationSteps.msme) {
            verificationSteps.msme.completed = false;
            verificationSteps.msme.skipped = false;
            verificationSteps.msme.data = null;
            verificationSteps.msme.nameMatch = null;
 
            $('#msme_verification_fields').hide();
            $('#msme_confirmation_section').hide();
            $('#msme_verified_enterprise_name, #msme_verified_major_activity, #msme_verified_organization_type, #msme_verified_social_category, #msme_verified_commencement_date, #msme_verified_dic_name, #msme_verified_registration_date, #msme_verified_status').val('');
             
            $('#msme_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#msme_number').closest('.input-group').removeClass('has-success has-error');
            $('#msme_number').removeClass('is-valid is-invalid');
        } else if (section === 'gst' && verificationSteps.gst) {
            verificationSteps.gst.completed = false;
            verificationSteps.gst.skipped = false;
            verificationSteps.gst.data = null;
            verificationSteps.gst.nameMatch = null; 
            $('#gst_verification_fields').hide();
            $('#gst_confirmation_section').hide();
            $('#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address').val('');
             
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            $('#gstin').removeClass('is-valid is-invalid');
        } else if (section === 'gst_new' && verificationSteps.gst) {
            verificationSteps.gst.completed = false;
            verificationSteps.gst.skipped = false;
            verificationSteps.gst.data = null;
            verificationSteps.gst.nameMatch = null; 
            $('#gst_verification_fields_new').hide();
            $('#gst_confirmation_section_new').hide();
            $('#gstin_legal_name_new, #gstin_trade_name_new, #gstin_registration_status_new, #gstin_taxpayer_type_new, #gstin_business_nature_new, #gstin_registration_date_new, #gstin_business_address_new').val('');
             
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            $('#gstin').removeClass('is-valid is-invalid');
        }
        
        console.log('Verification status reset for', section);
    } 
    function notifyParent(message, type) {
        try {
            if (window.parent && window.parent !== window) { 
                if (type === 'success' && typeof window.parent.setVerificationCompleted === 'function') {
                    window.parent.setVerificationCompleted();
                } 
                if (typeof window.parent.showNotification === 'function') {
                    window.parent.showNotification(message, type);
                } else if (typeof window.parent.toastr !== 'undefined') {
                    window.parent.toastr[type](message);
                } else if (typeof window.parent.alert === 'function') {
                    window.parent.alert(message);
                } else {
                    console.log('Parent notification:', message, type);
                }
            } else {
                console.log('No parent window or same window:', message, type);
            }
        } catch (e) {
            console.log('Error notifying parent:', e, message, type);
        }
    } 
    function showGSTComparisonMessage(result, actionType) {
        var currentCompanyName = company_name.toLowerCase().trim();
        var gstCompanyName = (result.result.legal_name || result.result.trade_name || "").toLowerCase().trim();
        
        var sectionId = actionType === 'add_new' ? 'gst_company_comparison_section_new' : 'gst_company_comparison_section';
        var contentId = actionType === 'add_new' ? 'gst_comparison_content_new' : 'gst_comparison_content';
        
        var content = '';
        
        if (currentCompanyName === gstCompanyName) {
            content = `
                <p><strong>Company Name Match Found!</strong></p>
                <p>The GST company name matches your current company name. Please choose which GST should be set as default:</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Current Company:</strong> ${currentCompanyName}
                    </div>
                    <div class="col-md-6">
                        <strong>GST Company:</strong> ${gstCompanyName}
                    </div>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="gst_default_choice" id="gst_default_old" value="old" ${actionType === 'change' ? 'checked' : ''}>
                    <label class="form-check-label" for="gst_default_old">
                        Set existing GST as default
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="gst_default_choice" id="gst_default_new" value="new" ${actionType === 'add_new' ? 'checked' : ''}>
                    <label class="form-check-label" for="gst_default_new">
                        Set this GST as default
                    </label>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="cancelGSTVerification('${actionType}')">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmGSTSelection('${actionType}')">
                        <i class="fa fa-check"></i> Confirm & Save
                    </button>
                </div>
            `;
        } else { 
            content = `
                <p><strong>Verification Failed!</strong></p>
                <p>The GST company name does not match your current company name:</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Your Company:</strong> ${currentCompanyName}
                    </div>
                    <div class="col-md-6">
                        <strong>GST Company:</strong> ${gstCompanyName}
                    </div>
                </div>
                <p class="text-danger">Please verify the GST number or update your company information.</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="cancelGSTVerification('${actionType}')">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-warning" onclick="changeGSTInfo('${actionType}')">
                        <i class="fa fa-edit"></i> Change Info
                    </button>
                </div>
            `;
        }
        
        document.getElementById(contentId).innerHTML = content;
        document.getElementById(sectionId).style.display = 'block';
         
        if (actionType === 'add_new') {
            $('#gst_verification_fields_new').hide();
            $('#verify_gst_new_btn').hide();
            $('#gst_new_edit_btn').hide();
        } else {
            $('#gst_verification_fields').hide();
            $('#verify_gst_btn').hide();
            $('#gst_edit_btn').hide();
        }
    }
 
    function confirmGSTSelection(actionType) {
        var gstDefault = $('input[name="gst_default_choice"]:checked').val();
        var gstType = actionType === 'add_new' ? 'new' : 'old';
        
        if (!gstDefault) {
            showNotification("Please select which GST should be set as default", "warning");
            return;
        }
        
        var data = {
            page: 'insertvendorsandvendorregisters',
            gstin: window.gstVerificationData.gstin,
            transaction_table: transaction_table,
            vendor_id: vendor_id,
            verification_type: actionType === 'add_new' ? 'GSTIN' : 'GST',
            gst_type: gstType,
            gst_default: gstDefault,
            api_hit_id: window.gstVerificationData.api_hit_id,
            address: window.gstVerificationData.address || '',
            post_code: window.gstVerificationData.post_code || '',
            state: window.gstVerificationData.state || '',
            city: window.gstVerificationData.city || '',
            location: window.gstVerificationData.location || ''
        };
         
        if (actionType === 'add_new') {
            data.business_location = $("#business_location").val();
            data.gst_purpose = $("#gst_purpose").val();
        }
        
        console.log("data", data);

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: data,
            success: function (response) {
                try {
                    console.log('GST data response:', response);
                    var obj = JSON.parse(response);
                    console.log('parsed response object:', obj);
                     
                    var lastID = '0';
                    var updatedRes = '0';
                    var status = '';
                     
                    if (obj.result && obj.result.res && obj.result.res.LASTID) {
                        lastID = obj.result.res.LASTID || '0';
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.LASTID) {
                        lastID = obj.all_result.res.LASTID || '0';
                    } else if (obj.all_result && obj.all_result.LASTID) {
                        lastID = obj.all_result.LASTID || '0';
                    } else if (obj.res && obj.res.LASTID) {
                        lastID = obj.res.LASTID || '0';
                    } else if (obj.LASTID) {
                        lastID = obj.LASTID || '0';
                    } 
                    if (obj.result && obj.result.res && obj.result.res.UpdatedRes) {
                        updatedRes = obj.result.res.UpdatedRes || '0';
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.UpdatedRes) {
                        updatedRes = obj.all_result.res.UpdatedRes || '0';
                    } else if (obj.res && obj.res.UpdatedRes) {
                        updatedRes = obj.res.UpdatedRes || '0';
                    } else if (obj.UpdatedRes) {
                        updatedRes = obj.UpdatedRes || '0';
                    } 
                    if (obj.result && obj.result.res && obj.result.res.status) {
                        status = obj.result.res.status;
                    } else if (obj.all_result && obj.all_result.res && obj.all_result.res.status) {
                        status = obj.all_result.res.status;
                    } else if (obj.res && obj.res.status) {
                        status = obj.res.status;
                    } else if (obj.status) {
                        status = obj.status;
                    } 
                    if (lastID != '0' || updatedRes != '0' || status === 'success' || status === 'sucess') {
                        var successMessage = "GST verified successfully!";
                        if (lastID != '0') {
                            successMessage = "GST verified and saved successfully!";
                        } else if (updatedRes != '0') {
                            successMessage = "GST verified and updated successfully!";
                        }
                        
                        showNotification(successMessage, "success"); 
                        var sectionId = actionType === 'add_new' ? 'gst_company_comparison_section_new' : 'gst_company_comparison_section';
                        var confirmationId = actionType === 'add_new' ? 'gst_confirmation_section_new' : 'gst_confirmation_section';
                        
                        document.getElementById(sectionId).style.display = 'none';
                        document.getElementById(confirmationId).style.display = 'block';
                         
                        setTimeout(function() {
                            closeModal(true); 
                            notifyParent("GST is verified", "success");
                        }, 2000);
                    } else {
                        showNotification("Failed to save GST verification data. Please try again.", "error");
                    }
                } catch (e) {
                    console.error('Error parsing GST response:', e);
                    showNotification("Error processing GST verification response", "error");
                }
            },
            error: function (xhr, status, error) {
                console.error('GST AJAX Error:', error);
                showNotification("Error saving GST verification data", "error");
            }
        });
    } 
    function cancelGSTVerification(actionType) {
        var sectionId = actionType === 'add_new' ? 'gst_company_comparison_section_new' : 'gst_company_comparison_section';
        document.getElementById(sectionId).style.display = 'none'; 
        if (actionType === 'add_new') {
            $('#gstin').prop('readonly', false);
            $('#verify_gst_new_btn').prop('disabled', false).html('<i class="fa fa-search"></i> Verify New GST').show();
            $('#gst_new_edit_btn').hide();
        } else {
            $('#gstin').prop('readonly', false);
            $('#verify_gst_btn').prop('disabled', false).html('<i class="fa fa-search"></i> Verify GST').show();
            $('#gst_edit_btn').hide();
        } 
        window.gstVerificationData = null;
        verificationSteps.gst.completed = false;
    } 
    function changeGSTInfo(actionType) {
        cancelGSTVerification(actionType);
        showNotification("Please enter the correct GST number", "info");
    }

    function checkalreadyavailableornotcustomerorvendordetails(field, el = null){
		$('#emailerror').hide();
		$('.gstinerror').hide();
		$('#pannoerror').hide();
		var fieldName = '';
		var fieldValue = '';
		if(field  == 'EMAIL'){
			fieldName = 'email';
			fieldValue = $("#email").val().trim();
		}else if (field == 'GSTIN') {
			fieldName = 'gstin';
			fieldValue = $(el).val().trim();
		}else if(field == 'PAN_NO'){
			fieldName = 'pan_no';
			fieldValue = $("#pan_no").val().trim();
		}
		console.log('fieldValue', (fieldValue));
        
        if(fieldValue !== ""){
			console.log('Start');
            $.ajax({
                    url: "ajax.php",
                    data:`page=checkalreadyavailableornotcustomerorvendordetails&table_name=vendors&field_name=${fieldName}&field_value=${fieldValue}`,
                    type: "POST",
                    async: false,
                    success:function(data){                        
                    var res = JSON.parse(data);
					if(res.result != ''  &&  res.result != null){
						console.log('GST already exists:', res.result);
						if(field  == 'EMAIL'){
							$('#emailerror').text(fieldValue+" Already Register");
							$('#emailerror').show();
							$("#email").val('');
						}
						if (field === 'GSTIN' && el) { 
							showNotification(fieldValue + " already exists in the system", "warning");
							$(el).siblings(".gstinerror")
								.text(fieldValue + " Already Registered")
								.show();
							$(el).val('');
						}
						if(field  == 'PAN_NO'){
							$('#pannoerror').text(fieldValue+" Already Register");
							$('#pannoerror').show();
							$("#pan_no").val('');
						}
					} else {
						console.log('Field not found in database, result:', res.result); 
						if (field === 'GSTIN' && el) {
							console.log('GST not found, calling verification automatically'); 
							if (fieldValue.length === 15) {
								console.log('GST format valid, calling verification function');
								setTimeout(function() { 
									if (typeof action_type !== 'undefined' && action_type === 'add_new') {
										console.log('Calling verifyNewGST for add_new action');
										if (typeof verifyNewGST === 'function') {
											verifyNewGST();
										}
									} else {
										console.log('Calling verifyGST for regular action');
										if (typeof verifyGST === 'function') {
											verifyGST();
										}
									}
								}, 500);
							} else {
								console.log('GST format invalid, length:', fieldValue.length);
							}
						}
					}
                    },
                    error:function (){}
                });
        }
		console.log('end');
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
    });

    // Also initialize when the script loads (for dynamic content)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEventListeners);
    } else {
        initializeEventListeners();
    }