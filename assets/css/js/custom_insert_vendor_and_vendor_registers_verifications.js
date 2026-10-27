    // Get PHP data from hidden containers
    var vendor_id = '';
    var transaction_table = '';
    var requiredVerifications = [];
    var visibleVerifications = [];
    var vendorVerifications = {};
    var vendorDetailEmail = '';
    var vendorDetailCompany = '';
    var sessionError3001 = '';
    var sessionError3000 = '';

    function initializePHPData() {
        console.log('Starting PHP data initialization...');
        
        vendor_id = $('.vendor-id-data').text() || '';
        transaction_table = $('.transaction-table-data').text() || '';
        
        console.log('Raw PHP data texts:', {
            requiredText: $('.required-verifications-data').text(),
            visibleText: $('.visible-verifications-data').text(),
            vendorText: $('.vendor-verifications-data').text()
        });
        
        try {
            requiredVerifications = JSON.parse($('.required-verifications-data').text() || '[]');
            visibleVerifications = JSON.parse($('.visible-verifications-data').text() || '[]');
            vendorVerifications = JSON.parse($('.vendor-verifications-data').text() || '{}');
        } catch (e) {
            console.error('Error parsing PHP data:', e);
            console.error('Failed text:', $('.required-verifications-data').text(), $('.visible-verifications-data').text(), $('.vendor-verifications-data').text());
            requiredVerifications = [];
            visibleVerifications = [];
            vendorVerifications = {};
        }
        
        // Ensure arrays are not null
        if (!Array.isArray(requiredVerifications)) {
            console.warn('requiredVerifications is not an array, converting:', requiredVerifications);
            requiredVerifications = [];
        }
        if (!Array.isArray(visibleVerifications)) {
            console.warn('visibleVerifications is not an array, converting:', visibleVerifications);
            visibleVerifications = [];
        }
        if (!vendorVerifications || typeof vendorVerifications !== 'object') {
            console.warn('vendorVerifications is not an object, converting:', vendorVerifications);
            vendorVerifications = {};
        }
        
        // Update totalSteps after visibleVerifications is loaded
        totalSteps = visibleVerifications.length;
        
        vendorDetailEmail = $('.vendor-detail-email-data').text() || '';
        vendorDetailCompany = $('.vendor-detail-company-data').text() || '';
        sessionError3001 = $('.session-error-3001-data').text() || '';
        sessionError3000 = $('.session-error-3000-data').text() || '';
        
        console.log('PHP Data initialized successfully:', {
            vendor_id: vendor_id,
            transaction_table: transaction_table,
            requiredVerifications: requiredVerifications,
            visibleVerifications: visibleVerifications,
            vendorVerifications: vendorVerifications,
            totalSteps: totalSteps
        });
    }
    var verificationSteps = {};
    var stepMapping = {};
    var totalSteps = 0;
    var currentStep = 1;
    var maxReachedStep = 1;

    function initializeVerificationSteps() {
        if (!vendorVerifications || typeof vendorVerifications !== 'object') {
            vendorVerifications = {};
        }

        if (!Array.isArray(visibleVerifications)) {
            console.error("visibleVerifications is not an array in initializeVerificationSteps:", visibleVerifications);
            return;
        }

        visibleVerifications.forEach(function (verification, index) {
            var stepNumber = index + 1;
            var verificationKey = verification.toLowerCase();

            var verificationData = (vendorVerifications && typeof vendorVerifications === 'object')
                ? vendorVerifications[verification] || {}
                : {};

            var isCompleted = verificationData.status === 'Y';

            verificationSteps[verificationKey] = {
                completed: isCompleted,
                data: isCompleted ? verificationData : null,
                required: (verification === 'PAN'),
                skipped: false,
                verified: isCompleted
            };

            stepMapping[verificationKey] = stepNumber;
            stepMapping[stepNumber] = verificationKey;

            if (isCompleted) {
                console.log('✅ Verification already completed for:', verification);
            } else {
                console.log('⏳ Verification pending for:', verification);
            }
        });
    }

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
                        $("#allreadycompanyname").show();
                        $("#company_check_status").val("exists");
                        return true;
                    } else {
                        console.log('frash');
                        $("#allreadycompanyname").hide();
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
            $("#allreadycompanyname").hide();
            $("#company_check_status").val("");
        }
    }

    function testJQuery() {
        console.log("Testing jQuery...");
        if (typeof $ !== 'undefined') {
            console.log("jQuery is available");
            console.log("jQuery version:", $.fn.jquery);
            return true;
        } else {
            console.log("jQuery is NOT available");
            return false;
        }
    }

    function initializePreCompletedVerifications() {
        console.log("Initializing pre-completed verifications...");

        Object.keys(completedVerifications).forEach(function (verificationType) {
            var verificationKey = verificationType.toLowerCase();
            var stepNumber = getStepForSection(verificationKey);
            var verificationData = completedVerifications[verificationType];

            console.log("Setting up pre-completed verification for:", verificationType);

            $('.step-indicator .step').eq(stepNumber - 1).addClass('completed');

            if (verificationType === 'PAN') {
                setupPreCompletedPAN(verificationData);
            } else if (verificationType === 'GSTIN') {
                setupPreCompletedGST(verificationData);
            } else if (verificationType === 'BANK') {
                setupPreCompletedBank(verificationData);
            } else if (verificationType === 'MSME') {
                setupPreCompletedMSME(verificationData);
            } else if (verificationType === 'TAN') {
                setupPreCompletedTAN(verificationData);
            }
        });
    }

    function setupPreCompletedPAN(data) {
        var stepDiv = $('#step-' + getStepForSection('pan'));
        stepDiv.addClass('pre-completed-verification');

        $('#pan_number, #company_name, #company_email').prop('readonly', true);
        $('#pan_verification_fields').show();
        $('#pan_confirmation_section').show();
        $('#verify_pan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#pan_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#pan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> PAN Already Verified');
        $('#pan_confirmation_section p').html('This PAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#pan_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedGST(data) {
        var stepDiv = $('#step-' + getStepForSection('gstin'));
        stepDiv.addClass('pre-completed-verification');

        $('#gstin').prop('readonly', true);
        $('#gst_verification_fields').show();
        $('#gst_confirmation_section').show();
        $('#verify_gst_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#gstin_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#gst_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> GST Already Verified');
        $('#gst_confirmation_section p').html('This GST has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#gst_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedBank(data) {
        var stepDiv = $('#step-' + getStepForSection('bank'));
        stepDiv.addClass('pre-completed-verification');

        $('#ifsc_code, #bank_account_no').prop('readonly', true);
        $('#bank_verification_fields').show();
        $('#bank_confirmation_section').show();
        $('#verify_bank_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#bank_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#bank_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> Bank Already Verified');
        $('#bank_confirmation_section p').html('This bank account has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#bank_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedMSME(data) {
        var stepDiv = $('#step-' + getStepForSection('msme'));
        stepDiv.addClass('pre-completed-verification');

        $('#msme_number').prop('readonly', true);
        $('#msme_verification_fields').show();
        $('#msme_confirmation_section').show();
        $('#verify_msme_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#msme_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#msme_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> MSME Already Verified');
        $('#msme_confirmation_section p').html('This MSME has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#msme_confirmation_section').after(restrictionMsg);
    }

    function setupPreCompletedTAN(data) {
        var stepDiv = $('#step-' + getStepForSection('tan'));
        stepDiv.addClass('pre-completed-verification');

        $('#tan_number').prop('readonly', true);
        $('#tan_verification_fields').show();
        $('#tan_confirmation_section').show();
        $('#verify_tan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#tan_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#tan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> TAN Already Verified');
        $('#tan_confirmation_section p').html('This TAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        var restrictionMsg = '<div class="verification-restriction-notice"><i class="fa fa-info-circle"></i> This verification was completed previously and cannot be modified.</div>';
        $('#tan_confirmation_section').after(restrictionMsg);
    }

    function initializeButtonStates() {
        console.log("Initializing button states...");

        if (!Array.isArray(visibleVerifications) || visibleVerifications.length === 0) {
            console.warn('initializeButtonStates: visibleVerifications is empty or not an array');
            $('.custom_readonly').prop('readonly', true);
            console.log("All verification details set to readonly");
            return;
        }

        var sections = visibleVerifications.map(function (verification) {
            return verification.toLowerCase();
        });

        sections.forEach(function (section) {
            var stepDiv = $('#step-' + getStepForSection(section));
            var inputs = stepDiv.find('input:not(.custom_readonly), textarea:not(.custom_readonly), select:not(.custom_readonly)');
            var button = $('#' + section + '_edit_btn');

            var hasReadonlyInputs = false;
            inputs.each(function () {
                if ($(this).prop('readonly') || $(this).prop('disabled')) {
                    hasReadonlyInputs = true;
                    return false;
                }
            });

            if (hasReadonlyInputs) {
                button.text('Change Info').removeClass('btn-warning').addClass('btn-success');
                console.log(section + ' button set to readonly state (success)');
            } else {
                button.text('Change Info').removeClass('btn-success').addClass('btn-warning');
                console.log(section + ' button set to editable state (warning)');
            }
        });

        $('.custom_readonly').prop('readonly', true);
        console.log("All verification details set to readonly");
    }

    function getStepForSection(section) {
        return stepMapping[section] || 1;
    }

    function getSectionForStep(step) {
        return stepMapping[step] || 'pan';
    }

    function nextStep() {
        var currentSectionKey = getSectionForStep(currentStep);
        var currentVerificationStep = verificationSteps[currentSectionKey];
        var verificationType = currentSectionKey.toUpperCase();
        
        console.log('nextStep called for step:', currentStep, 'section:', currentSectionKey, 'verificationType:', verificationType);
        
        // Check if current step is already verified in database (locked)
        var isVerified = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
        
        if (isVerified) {
            console.log('Step is already verified in database, allowing next step');
            // If current step is already verified, just move to next step
            if (currentStep < totalSteps) {
                currentStep++;
                if (currentStep > maxReachedStep) {
                    maxReachedStep = currentStep;
                }
                showStep(currentStep);
                updateStepIndicator();
            }
            return;
        }
        
        // Check if this is a required step
        var isRequired = requiredVerifications.includes(verificationType);
        console.log('Step is required:', isRequired);
        
        if (isRequired) {
            // For required steps, check if completed or skipped
            var isCompleted = currentVerificationStep && currentVerificationStep.completed;
            var isSkipped = currentVerificationStep && currentVerificationStep.skipped;
            
            console.log('Required step status - Completed:', isCompleted, 'Skipped:', isSkipped);
            
            if (!isCompleted && !isSkipped) {
                var sectionName = currentSectionKey.toUpperCase();
                showNotification("Please complete " + sectionName + " verification before proceeding to the next step. If you don't want to verify now, please skip this step.", "warning");
                return;
            }
        } else {
            // For non-required steps, allow if completed, skipped, or no data entered
            var hasData = hasDataInCurrentStep();
            var isCompleted = currentVerificationStep && currentVerificationStep.completed;
            var isSkipped = currentVerificationStep && currentVerificationStep.skipped;
            
            console.log('Non-required step - HasData:', hasData, 'Completed:', isCompleted, 'Skipped:', isSkipped);
            
            if (hasData && !isCompleted && !isSkipped) {
                var sectionName = currentSectionKey.toUpperCase();
                swal("", "Please complete " + sectionName + " verification or skip this step before proceeding.", "warning");
                return;
            }
        }

        // Check for name mismatch issues
        if (currentVerificationStep && currentVerificationStep.data && currentVerificationStep.nameMatch === false && !currentVerificationStep.skipped) {
            var sectionName = currentSectionKey.toUpperCase();
            swal("", "Cannot proceed to next step. " + sectionName + " verification shows company name mismatch. Please correct the company name or contact support.", "warning");
            return;
        }

        console.log('Validation passed, moving to next step');
        if (currentStep < totalSteps) {
            currentStep++;
            // Update maxReachedStep if we're going to a new step
            if (currentStep > maxReachedStep) {
                maxReachedStep = currentStep;
            }
            showStep(currentStep);
            updateStepIndicator();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateStepIndicator();
        }
    }

    function goToStep(step) {
        console.log('goToStep called with step:', step, 'currentStep:', currentStep, 'maxReachedStep:', maxReachedStep);
        
        // Allow backward navigation or navigation to previously reached steps
        if (step <= currentStep || step <= maxReachedStep) {
            console.log('Allowing navigation to previously visited step:', step);
            currentStep = step;
            showStep(currentStep);
            updateStepIndicator();
            return;
        }
        
        // For forward navigation to new steps, validate all previous required steps
        console.log('Forward navigation to new step, validating previous steps...');
        for (let i = currentStep; i < step; i++) {
            var sectionKey = getSectionForStep(i);
            var verificationType = sectionKey.toUpperCase();
            var verificationStep = verificationSteps[sectionKey];
            
            // Check if step is already verified in database
            var isVerified = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
            
            if (isVerified) {
                console.log('Step', i, 'is verified in database, skipping validation');
                continue;
            }
            
            // Check if this is a required step
            var isRequired = requiredVerifications.includes(verificationType);
            
            if (isRequired) {
                var isCompleted = verificationStep && verificationStep.completed;
                var isSkipped = verificationStep && verificationStep.skipped;
                
                console.log('Validating required step', i, '- Completed:', isCompleted, 'Skipped:', isSkipped);
                
                if (!isCompleted && !isSkipped) {
                    var sectionName = sectionKey.toUpperCase();
                    showNotification("Please complete " + sectionName + " verification (step " + i + ") before proceeding to step " + step, "warning");
                    return;
                }
            } else {
                // For non-required steps with data, they must be completed or skipped
                var hasData = hasDataInStep(i);
                var isCompleted = verificationStep && verificationStep.completed;
                var isSkipped = verificationStep && verificationStep.skipped;
                
                if (hasData && !isCompleted && !isSkipped) {
                    var sectionName = sectionKey.toUpperCase();
                    swal("","Please complete or skip " + sectionName + " verification (step " + i + ") before proceeding to step " + step, "warning");
                    //  showNotification("Please complete or skip " + sectionName + " verification (step " + i + ") before proceeding to step " + step, "warning");
                    return;
                }
            }
        }
        
        // Update maxReachedStep if we're going to a new step
        if (step > maxReachedStep) {
            maxReachedStep = step;
        }
        
        currentStep = step;
        showStep(currentStep);
        updateStepIndicator();
        
        console.log('Successfully navigated to step:', step);
    }

    function validateCurrentStep() {
        return validateStep(currentStep);
    }

    function hasDataInCurrentStep() {
        return hasDataInStep(currentStep);
    }

    function hasDataInStep(step) {
        var sectionKey = getSectionForStep(step);

        switch (sectionKey) {
            case 'pan':
                return $('#pan_number').val().trim() !== '' || $('#company_name').val().trim() !== '';
            case 'gstin':
                return $('#gstin').val().trim() !== '';
            case 'bank':
                return $('#ifsc_code').val().trim() !== '' || $('#bank_account_no').val().trim() !== '';
            case 'msme':
                return $('#msme_number').val().trim() !== '';
            case 'tan':
                return $('#tan_number').val().trim() !== '';
            default:
                return false;
        }
    }

    function clearCurrentStepData() {
        var currentSectionKey = getSectionForStep(currentStep);

        switch (currentSectionKey) {
            case 'pan':
                $('#pan_number').val('');
                $('#company_name').val('');
                $('#company_email').val('');
                break;
            case 'gstin':
                $('#gstin').val('');
                break;
            case 'bank':
                $('#ifsc_code').val('');
                $('#bank_account_no').val('');
                break;
            case 'msme':
                $('#msme_number').val('');
                break;
            case 'tan':
                $('#tan_number').val('');
                break;
        }
 
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.input-group .status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        stepDiv.find('.input-group').removeClass('has-success has-error');
 
        resetCurrentStepVerification();
    }

    function validateStep(step) {
        var sectionKey = getSectionForStep(step);
        var verificationStep = verificationSteps[sectionKey];
        var verificationType = sectionKey.toUpperCase();

        // If step doesn't exist in verificationSteps, check if it's already verified in database
        if (!verificationStep) {
            var isVerified = vendorVerifications[verificationType] && 
                vendorVerifications[verificationType].status === 'Y';
            return isVerified; // Return true if already verified, false otherwise
        }
 
        // If not completed and not skipped, check if it's required
        if (!verificationStep.completed && !verificationStep.skipped) {
            // Allow if not required or if already verified in database
            var isRequired = requiredVerifications.includes(verificationType);
            var isVerified = vendorVerifications[verificationType] && 
                vendorVerifications[verificationType].status === 'Y';
            
            if (!isRequired || isVerified) {
                return true;
            }
            
            var sectionName = sectionKey.toUpperCase();
            showNotification("Please complete " + sectionName + " verification or skip this step before proceeding", "warning");
            return false;
        }

        if (verificationStep.data && verificationStep.nameMatch === false && !verificationStep.skipped) {
            var sectionName = sectionKey.toUpperCase();
            showNotification(sectionName + " verification shows company name mismatch. Please correct the company name or contact support before proceeding to the next step.", "warning");
            return false;
        }

        if (verificationSteps.tan && verificationSteps.tan.data && verificationSteps.tan.nameMatch === false && !verificationSteps.tan.skipped) {
            showNotification("TAN verification shows company name mismatch. Please correct the company name or contact support before proceeding.", "warning");
            return false;
        }
        return true;
    }

    function showStep(step) {
        console.log('showStep called for step:', step);
        $('.verification-step').hide();
        $('#step-' + step).show();

        $('#prevBtn').toggle(step > 1);
        $('#nextBtn').toggle(step < totalSteps);
        $('#completeBtn').toggle(step === totalSteps);

        var sectionKey = getSectionForStep(step);
        if (sectionKey) {
            console.log('Showing step ' + step + ' for section: ' + sectionKey);
            
            // Check if this step is verified in database
            var verificationType = sectionKey.toUpperCase();
            var isVerified = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
            console.log('Step', step, 'verification status:', isVerified ? 'VERIFIED' : 'NOT VERIFIED');
        }
    }

    function updateStepIndicator() {
        if (!Array.isArray(visibleVerifications) || visibleVerifications.length === 0) {
            console.warn('updateStepIndicator: visibleVerifications is empty or not an array');
            return;
        }

        $('.step-indicator .step').each(function (index) {
            var stepNum = index + 1;
            $(this).removeClass('active completed');

            var verification = visibleVerifications[index];
            if (!verification) {
                console.warn('updateStepIndicator: verification is undefined at index', index);
                return;
            }
            
            var verificationKey = verification.toLowerCase();
            var isVerified = vendorVerifications[verification] &&
                vendorVerifications[verification].status === 'Y';
            var isCompleted = verificationSteps[verificationKey] && verificationSteps[verificationKey].completed;
            var isSkipped = verificationSteps[verificationKey] && verificationSteps[verificationKey].skipped;

            if (isVerified || isCompleted || (stepNum < currentStep && (isCompleted || isSkipped))) {
                $(this).addClass('completed');
                $(this).find('.step-number').html('<i class="fa fa-check"></i>');
            } else if (stepNum === currentStep) {
                $(this).addClass('active');
                $(this).find('.step-number').html(stepNum);
            } else {
                $(this).find('.step-number').html(stepNum);
            }
        });
    }

    function skipStep(stepType) { 
        if (hasDataInCurrentStep()) {
            clearCurrentStepData();
        }
 
        verificationSteps[stepType].skipped = true;
        verificationSteps[stepType].completed = false;
 
        var stepName = stepType.toUpperCase();
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.verification-fields').hide();
        stepDiv.find('.confirmation-section').hide();

        var skipMessage = `
        <div class="alert alert-info mt-3" id="${stepType}_skip_message">
            <i class="fa fa-info-circle"></i> ${stepName} verification has been skipped. 
            You can complete this verification later if needed.
        </div>`;

        stepDiv.find(`#${stepType}_skip_message`).remove();
        stepDiv.find('.step-header').after(skipMessage);
 
        showNotification(stepName + " verification skipped automatically", "info");
 
        if (currentStep === totalSteps) {
            // finishVerification();
        } else {
            nextStep();
        }
    }

    // New function to handle skip button clicks
    function skipStepHandler(stepType) {
        // Check if this is the last step
        if (currentStep === totalSteps) {
            // On last step, skip button should call complete verification
            completeVerification();
        } else {
            // On other steps, work as normal skip
            skipStep(stepType);
        }
    }

    // Function to check if all required verifications are completed
    function areRequiredVerificationsComplete() {
        for (var i = 0; i < requiredVerifications.length; i++) {
            var verificationType = requiredVerifications[i];
            var verificationKey = verificationType.toLowerCase();
            
            // Check if this verification is in visible list
            if (visibleVerifications.includes(verificationType)) {
                var isVerified = vendorVerifications[verificationType] && 
                    vendorVerifications[verificationType].status === 'Y';
                var isCompleted = verificationSteps[verificationKey] && 
                    verificationSteps[verificationKey].completed;
                
                // If required verification is not completed and not already verified
                if (!isVerified && !isCompleted) {
                    return false;
                }
            }
        }
        return true;
    }

    // Function to complete verification process
    function completeVerification() {
        // Check if all required verifications are done
        if (!areRequiredVerificationsComplete()) {
            showNotification("Please complete all required verifications before finishing the process.", "warning");
            return;
        }

        // All required verifications are complete, reload the page
        showNotification("Verification process completed successfully! Reloading page...", "success");
        
        setTimeout(function() {
            location.reload();
        }, 2000);
    }


    function validatePAN(pan) {
        var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        return panRegex.test(pan);
    }

    function validateGST(gst) {
        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        return gstRegex.test(gst);
    }

    function validateIFSC(ifsc) {
        var ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        return ifscRegex.test(ifsc);
    }

    function validateMSME(msme) {
        var msmeRegex = /^UDYAM-[A-Z]{2}-[0-9]{2}-[0-9]{7}$/;
        return msmeRegex.test(msme);
    }

    function validateTAN(tan) {
        var tanRegex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
        return tanRegex.test(tan);
    }

    function formatPANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validatePAN(value);
        updateInputStatus('pan_number', isValid, value.length === 10);

        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyPAN();
            }, 500);
        }
    }

    function formatGSTInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 15) value = value.substring(0, 15);
        input.value = value;

        var isValid = validateGST(value);
        updateInputStatus('gstin', isValid, value.length === 15);

        if (isValid && value.length === 15) {
            setTimeout(function () {
                verifyGST();
            }, 500);
        }
    }

    function formatIFSCInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        input.value = value;

        var isValid = validateIFSC(value);
        updateInputStatus('ifsc_code', isValid, value.length === 11);

        if (isValid && value.length === 11) {
            checkBankAutoVerification();
        }
    }

    function checkBankAutoVerification() {
        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        if (validateIFSC(ifsc) && account && account.length >= 8) {
            setTimeout(function () {
                verifyBank();
            }, 500);
        }
    }

    function formatBankAccountInput(input) {
        var value = input.value.replace(/[^0-9]/g, '');
        input.value = value;

        if (value.length >= 10) {
           // checkBankAutoVerification();
            $("#verify_bank_btn").removeClass('d-none');
        }else{
            $("#verify_bank_btn").addClass('d-none');
        }
    }

    function formatMSMEInput(input) {
        var value = input.value.toUpperCase();
        if (!value.startsWith('UDYAM-')) {
            value = 'UDYAM-' + value.replace(/[^A-Z0-9-]/g, '');
        }
        if (value.length > 19) value = value.substring(0, 19);
        input.value = value;

        var isValid = validateMSME(value);
        updateInputStatus('msme_number', isValid, value.length === 19);

        if (isValid && value.length === 19) {
            setTimeout(function () {
                verifyMSME();
            }, 500);
        }
    }

    function formatTANInput(input) {
        var value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        input.value = value;

        var isValid = validateTAN(value);
        updateInputStatus('tan_number', isValid, value.length === 10);

        if (isValid && value.length === 10) {
            setTimeout(function () {
                verifyTAN();
            }, 500);
        }
    }

    function updateInputStatus(fieldId, isValid, isComplete) {
        var inputGroup = $('#' + fieldId).closest('.input-group');
        var statusIcon = inputGroup.find('.status-icon');

        if (isComplete) {
            if (isValid) {
                statusIcon.html('<i class="fa fa-check text-success"></i>');
                inputGroup.removeClass('has-error').addClass('has-success');
            } else {
                statusIcon.html('<i class="fa fa-times text-danger"></i>');
                inputGroup.removeClass('has-success').addClass('has-error');
            }
        } else {
            statusIcon.html('<i class="fa fa-clock-o text-muted"></i>');
            inputGroup.removeClass('has-success has-error');
        }
    }

    function toggleEdit(section) {
        console.log('toggleEdit called for section:', section);
        console.log('Current step:', currentStep);

        var stepDiv = $('#step-' + currentStep);
        var inputs = stepDiv.find('input:not(.custom_readonly), textarea:not(.custom_readonly), select:not(.custom_readonly)');
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
 
            resetCurrentStepVerificationStatus();

            button.text('Change Info').removeClass('btn-success').addClass('btn-warning');
            console.log('Button changed to Change Info (editable mode)');
        } else {
            console.log('Disabling editing mode...');
            inputs.each(function () {
                console.log('Disabling field:', $(this).attr('id') || $(this).attr('name'));
                $(this).prop('readonly', true);
                if ($(this).is('select')) {
                    $(this).prop('disabled', true);
                }
            });
            button.text('Change Info').removeClass('btn-warning').addClass('btn-success');
            console.log('Button changed to Change Info (readonly mode)');
        }
    }

    function resetCurrentStepVerificationStatus() {
        console.log('Resetting verification status for step:', currentStep);

        var stepType = getSectionForStep(currentStep);

        if (stepType && verificationSteps[stepType]) {
            verificationSteps[stepType].completed = false;
            verificationSteps[stepType].skipped = false;
            verificationSteps[stepType].data = null;

            $('#' + stepType + '_skip_message').remove();
        }

        if (stepType === 'pan') { 
            $('#pan_confirmation_section').hide();
            $('#verify_pan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification status reset');
        } else if (stepType === 'gstin') { 
            $('#gst_confirmation_section').hide();
            $('#gst_address_options').hide();
            $('#verify_gst_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST').prop('disabled', false);
            $('#gstin_legal_name').val('');
            $('#gstin_trade_name').val('');
            $('#gstin_registration_status').val('');
            $('#gstin_taxpayer_type').val('');
            $('#gstin_business_nature').val('');
            $('#gstin_registration_date').val('');
            $('#gstin_business_address').val('');
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            console.log('GST verification status reset');
        } else if (stepType === 'bank') { 
            $('#bank_confirmation_section').hide();
            $('#bank_manual_confirmation_section').hide();
            $('#verify_bank_btn').removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
            $('#bank_verified_ifsc').val('');
            $('#bank_verified_account').val('');
            $('#bank_verified_name').val('');
            $('#bank_verified_status').val('');
            $('#ifsc_code').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#ifsc_code').closest('.input-group').removeClass('has-success has-error');
            $('#bank_account_no').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#bank_account_no').closest('.input-group').removeClass('has-success has-error');
            console.log('Bank verification status reset');
        } else if (stepType === 'msme') { 
            $('#msme_confirmation_section').hide();
            $('#verify_msme_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
            $('#msme_verified_enterprise_name').val('');
            $('#msme_verified_major_activity').val('');
            $('#msme_verified_organization_type').val('');
            $('#msme_verified_social_category').val('');
            $('#msme_verified_commencement_date').val('');
            $('#msme_verified_dic_name').val('');
            $('#msme_verified_registration_date').val('');
            $('#msme_verified_status').val('');
            console.log('MSME verification status reset');
        } else if (stepType === 'tan') { 
            $('#tan_confirmation_section').hide();
            $('#verify_tan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN').prop('disabled', false);
            $('#tan_verified_company').val('');
            $('#tan_verified_status').val('');
            console.log('TAN verification status reset');
        }
    }

    function resetCurrentStepVerification() {
        console.log('Resetting verification for step:', currentStep);

        var stepType = getSectionForStep(currentStep);

        if (stepType && verificationSteps[stepType]) {
            verificationSteps[stepType].completed = false;
            verificationSteps[stepType].skipped = false;
            verificationSteps[stepType].data = null;

            $('#' + stepType + '_skip_message').remove();
        }

        if (stepType === 'pan') {
            $('#pan_verification_fields').hide();
            $('#pan_confirmation_section').hide();
            $('#verify_pan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification reset');
        } else if (stepType === 'gstin') {
            $('#gst_verification_fields').hide();
            $('#gst_confirmation_section').hide();
            $('#gst_address_options').hide();
            $('#verify_gst_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST').prop('disabled', false);
            $('#gstin_legal_name').val('');
            $('#gstin_trade_name').val('');
            $('#gstin_registration_status').val('');
            $('#gstin_taxpayer_type').val('');
            $('#gstin_business_nature').val('');
            $('#gstin_registration_date').val('');
            $('#gstin_business_address').val('');
            $('#gstin').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#gstin').closest('.input-group').removeClass('has-success has-error');
            console.log('GST verification reset');
        } else if (stepType === 'bank') {
            $('#bank_verification_fields').hide();
            $('#bank_confirmation_section').hide();
            $('#bank_manual_confirmation_section').hide();
            $('#verify_bank_btn').removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
            $('#bank_verified_ifsc').val('');
            $('#bank_verified_account').val('');
            $('#bank_verified_name').val('');
            $('#bank_verified_status').val('');
            $('#ifsc_code').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#ifsc_code').closest('.input-group').removeClass('has-success has-error');
            $('#bank_account_no').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#bank_account_no').closest('.input-group').removeClass('has-success has-error');
            console.log('Bank verification reset');
        } else if (stepType === 'msme') {
            $('#msme_verification_fields').hide();
            $('#msme_confirmation_section').hide();
            $('#verify_msme_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
            $('#msme_verified_enterprise_name').val('');
            $('#msme_verified_major_activity').val('');
            $('#msme_verified_organization_type').val('');
            $('#msme_verified_social_category').val('');
            $('#msme_verified_commencement_date').val('');
            $('#msme_verified_dic_name').val('');
            $('#msme_verified_registration_date').val('');
            $('#msme_verified_status').val('');
            console.log('MSME verification reset');
        } else if (stepType === 'tan') {
            $('#tan_verification_fields').hide();
            $('#tan_confirmation_section').hide();
            $('#verify_tan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN').prop('disabled', false);
            $('#tan_verified_company').val('');
            $('#tan_verified_status').val('');
            console.log('TAN verification reset');
        }
    }

    function verifyPAN() {
        if (vendorVerifications.PAN && vendorVerifications.PAN.status === 'Y') {
            showNotification("PAN is already verified and cannot be modified", "warning");
            return;
        }

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
        if (companyCheckStatus === "exists") {
            showNotification("Company name already registered. Please use a different name.", "warning");
            return;
        } else if (companyCheckStatus === "" || companyCheckStatus === "error") {
            // If no check was performed or there was an error, perform the check now
            if (vendor_id === "") {
                checkCompanyNameAllReadyAvilableOrNot();
                companyCheckStatus = $("#company_check_status").val();
                if (companyCheckStatus === "exists") {
                    showNotification("Company name already registered. Please use a different name.", "warning");
                    return;
                }
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
        $("#pan_verification_fields").show();
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

                        $("#pan_confirmation_section").show();
                        $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#pan_number, #company_name, #company_email").prop('readonly', true);
                        $("#pan_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").prop('readonly', true).addClass('custom_readonly');

                        var company_name_entered = company_name.toLowerCase().trim();
                        var pan_holder_name = (result.result.user_full_name || result.result.pan_holder_name || "").toLowerCase().trim();

                        if (company_name_entered === pan_holder_name) {
                            showNotification("PAN Details are Correct", "success");
                            verificationSteps.pan.nameMatch = true;

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
                                        console.log('pan data', response);
                                        var obj = JSON.parse(response);
                                        if (obj.all_result && obj.all_result.LASTID) {
                                            var lastID = obj.all_result.LASTID || '0';
                                            tostarnotify('top', 'right', 'fa fa-thumbs-up',
                                                'form-label label label-success',
                                                'animated flipInX', 'animated flipOutX',
                                                (lastID == '0'
                                                    ? sessionError3001
                                                    : sessionError3000),
                                                ''
                                            );

                                            if (lastID != '0') {
                                                updateUrlParameter('Rid', obj.rid);
                                            }
                                            location.reload();
                                        } else {
                                            tostarnotify('top', 'right', 'fa fa-thumbs-down',
                                                'form-label label label-danger',
                                                'animated flipInX', 'animated flipOutX',
                                                'Insert failed or response invalid', '');
                                        }
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },

                                error: function () { }
                            });

                        } else {
                            verificationSteps.pan.completed = false;
                            verificationSteps.pan.nameMatch = false;

                            $("#pan_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#pan_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> PAN Verification Done - Data Mismatch');
                            $("#pan_confirmation_section p").html('PAN details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');
                            showNotification("Verification completed but company name doesn't match with PAN holder name. Please check the data.", "warning");
                            $("#pan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');
                            $("#verify_pan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
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
                    }
                } catch (e) {
                    $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Parse Error");
                    showNotification("PAN Verification Parse Error: " + e.message, "error");
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
            }
        });
    }

    function verifyGST() {
        if (vendorVerifications.GSTIN && vendorVerifications.GSTIN.status === 'Y') {
            showNotification("GST is already verified and locked. Cannot be modified.", "warning");
            return;
        }

        if (isStepLocked('gstin')) {
            showNotification("GST verification is locked and cannot be modified", "warning");
            return;
        }

        var gstin = $("#gstin").val();
        var company_name = $("#company_name").val();

        if (!validateGST(gstin)) {
            showNotification("Please enter a valid GST number", "warning");
            return;
        }

        console.log('GST Verification: Locking input');
        $("#gstin").prop('readonly', true);
        $("#verify_gst_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#gst_verification_fields").show();
        $("#gstin_legal_name").val("Verifying...");
        $("#gstin_trade_name").val("Verifying...");
        $("#gstin_registration_status").val("Verifying...");
        $("#gstin_taxpayer_type").val("Verifying...");
        $("#gstin_business_nature").val("Verifying...");
        $("#gstin_registration_date").val("Verifying...");
        $("#gstin_business_address").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'business_gstin_number': gstin,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'GSTVERIFICATIONLITE',
                'consent': 'Y',
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);
                    console.log('GST', response);
                    if (result.success == true && result.result) {
                        console.log('success');
                        $("#gstin_legal_name").val(result.result.legal_name || "N/A");
                        $("#gstin_trade_name").val(result.result.trade_name || "N/A");
                        $("#gstin_registration_status").val(result.result.current_registration_status || "N/A");
                        $("#gstin_taxpayer_type").val(result.result.tax_payer_type || "N/A");
                        $("#gstin_business_nature").val(result.result.business_nature ? result.result.business_nature.join(', ') : "N/A");
                        $("#gstin_registration_date").val(result.result.register_date || "N/A");

                        var address = "N/A";
                        if (result.result.primary_business_address) {
                            var addr = result.result.primary_business_address; 
                            var addressParts = [];

                            var full_address = addr.full_address; 
                            var state = addr.state_code; 
                            var city = addr.district; 
                            var location = addr.location; 
                            var pincode = addr.pincode; 

                            if (addr.building_name) addressParts.push(addr.building_name);
                            if (addr.building_number) addressParts.push(addr.building_number);
                            if (addr.street) addressParts.push(addr.street);
                            if (addr.city) addressParts.push(addr.city);
                            if (addr.district) addressParts.push(addr.district);
                            if (addr.state) addressParts.push(addr.state);
                            if (addr.pincode) addressParts.push(addr.pincode);
                            address = addressParts.length > 0 ? addressParts.join(', ') : "";
                        }
                        $("#gstin_business_address").val(address);

                        $("#gstin").prop('readonly', true);
                        $("#gstin_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');

                        $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").prop('readonly', true).addClass('custom_readonly');

                       // if (typeof showGSTAddressOptions === 'function') {
                           // showGSTAddressOptions(result.result);
                        //}

                        console.log('company_name', company_name);
                        console.log('result.result.trade_name', result.result.trade_name);
                        console.log('result.result.legal_name', result.result.legal_name);
                        var company_name_entered = company_name.toLowerCase().trim();
                        var gst_trade_name = (result.result.trade_name || "").toLowerCase().trim();
                        var gst_legal_name = (result.result.legal_name || "").toLowerCase().trim();

                        var isNameMatch = (company_name_entered === gst_trade_name || company_name_entered === gst_legal_name);

                        // Store verification data
                        verificationSteps.gstin.data = result;
                        verificationSteps.gstin.nameMatch = isNameMatch;

                        console.log('isNameMatch', isNameMatch);
                        if (isNameMatch) {
                            showNotification("GST Details are Correct", "success");
                            console.log('GST verification part');

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
                                    api_hit_id: result.api_hit_id,
                                },
                                async: false,
                                success: function (response) {
                                    try {
                                        console.log("gstin response", response);
                                        $("#gst_verification_fields").show();
                                        $("#gst_confirmation_section").show(); 
                                        lockVerificationStep('gstin');
                                        showNotification("GST Verification completed and locked successfully", "success");
                                        $("#verify_gst_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                                        verificationSteps.gstin.completed = true;
                                        verificationSteps.gstin.locked = true;
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },
                                error: function () { }
                            });

                            console.log('GST AJAX end part');
                        } else {
                            console.log('gst not status part');
                            verificationSteps.gstin.completed = false;
                            verificationSteps.gstin.nameMatch = false;

                            $("#gst_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#gst_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> GST Verification Done - Data Mismatch');
                            $("#gst_confirmation_section p").html('GST details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');

                            showNotification("Verification completed but company name doesn't match with GST records. Please check the data.", "warning");
                            $("#gstin").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                            $("#verify_gst_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                        }

                    } else {
                        $("#gstin_legal_name").val("Verification Failed");
                        $("#gstin_trade_name").val("Verification Failed");
                        $("#gstin_registration_status").val("Verification Failed");
                        $("#gstin_taxpayer_type").val("Verification Failed");
                        $("#gstin_business_nature").val("Verification Failed");
                        $("#gstin_registration_date").val("Verification Failed");
                        $("#gstin_business_address").val("Verification Failed");
                        
                        // Reset button states on failure
                        $("#gstin").prop('readonly', false);
                        $("#verify_gst_btn").prop('disabled', false).html('<i class="fa fa-search"></i> Verify GST');
                        
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
                    }
                } catch (e) {
                    // Reset button states on parse error
                    $("#gstin").prop('readonly', false);
                    $("#verify_gst_btn").prop('disabled', false).html('<i class="fa fa-search"></i> Verify GST');
                    showNotification("GST Verification Parse Error: " + e.message, "error");
                }
            },
            error: function (xhr, status, error) {
                // Reset button states on network error
                $("#gstin").prop('readonly', false);
                $("#verify_gst_btn").prop('disabled', false).html('<i class="fa fa-search"></i> Verify GST');
                
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
            }
        });
    }

    function verifyBank() {
        if (vendorVerifications.BANK && vendorVerifications.BANK.status === 'Y') {
            showNotification("Bank Account is already verified and locked. Cannot be modified.", "warning");
            return;
        }

        if (isStepLocked('bank')) {
            showNotification("Bank verification is locked and cannot be modified", "warning");
            return;
        }

        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();

        if (!validateIFSC(ifsc)) {
            showNotification("Please enter a valid IFSC code", "warning");
            return;
        }

        if (!account) {
            showNotification("Please enter bank account number", "warning");
            return;
        }

        if ($("#verify_bank_btn").prop('disabled') || verificationSteps.bank.completed) {
            return;
        }

        console.log('Bank Verification: Locking inputs');
        $("#ifsc_code, #bank_account_no").prop('readonly', true);
        $("#verify_bank_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#bank_verification_fields").show();
        $("#bank_verified_ifsc").val("Verifying...");
        $("#bank_verified_account").val("Verifying...");
        $("#bank_verified_name").val("Verifying...");
        $("#bank_verified_status").val("Verifying...");

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
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
                'mode': 'sync'
            },
            success: function (response) {
                try {
                    var result = JSON.parse(response);
                    console.log('bank result', result);
                    if (result && result.response_code == "200" && result.result && result.result.verification_status == 'VERIFIED') {

                        $("#bank_verified_ifsc").val(ifsc);
                        $("#bank_verified_account").val(account);
                        $("#bank_verified_name").val(result.result.beneficiary_name || "N/A");
                        $("#bank_verified_status").val("Verified Successfully");

                        verificationSteps.bank.completed = true;
                        verificationSteps.bank.data = result;

                        // Show manual confirmation section instead of immediately updating status
                        $("#bank_manual_confirmation_section").show();
                        $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Pending Confirmation');

                        console.log('Bank verification successful - showing manual confirmation');
                        $("#ifsc_code, #bank_account_no").prop('readonly', true);
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-secondary');

                        $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status").prop('readonly', true).addClass('custom_readonly');

                        // Store verification data for later use
                        window.bankVerificationData = {
                            ifsc_code: ifsc,
                            bank_account_no: account,
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

                        showNotification("Bank Account Verified Successfully - Please confirm the details", "success");

                    } else {
                        $("#bank_verified_ifsc").val("Verification Failed");
                        $("#bank_verified_account").val("Verification Failed");
                        $("#bank_verified_name").val("Verification Failed");
                        $("#bank_verified_status").val(result.response_message || "Verification Failed");
                        
                        // Show detailed error information
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
                    }
                } catch (e) {
                    showNotification("Bank Verification Parse Error: " + e.message, "error");
                }
            },
            error: function (xhr, status, error) {
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
            }
        });
    }

    function verifyMSME() {
        if (vendorVerifications.MSME && vendorVerifications.MSME.status === 'Y') {
            showNotification("MSME is already verified and locked. Cannot be modified.", "warning");
            return;
        }

        if (isStepLocked('msme')) {
            showNotification("MSME verification is locked and cannot be modified", "warning");
            return;
        }

        var msme_number = $("#msme_number").val();

        if (!validateMSME(msme_number)) {
            showNotification("Please enter a valid MSME/Udyam Registration number", "warning");
            return;
        }

        $("#msme_number").prop('readonly', true);
        $("#verify_msme_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#msme_verification_fields").show();
        $("#msme_verified_enterprise_name").val("Verifying...");
        $("#msme_verified_major_activity").val("Verifying...");
        $("#msme_verified_organization_type").val("Verifying...");
        $("#msme_verified_social_category").val("Verifying...");
        $("#msme_verified_commencement_date").val("Verifying...");
        $("#msme_verified_dic_name").val("Verifying...");
        $("#msme_verified_registration_date").val("Verifying...");
        $("#msme_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'udyog_aadhaar': msme_number,
                'transaction_table': transaction_table,
                'transaction_id': vendor_id,
                'api_code': 'UDYOGAADHAAR',
                'consent': 'Y',
                'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
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

                        $("#msme_confirmation_section").show();
                        $("#verify_msme_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        console.log('MSME verification successful');

                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: {
                                page: 'insertvendorsandvendorregisters',
                                msme_number: msme_number,
                                transaction_table: transaction_table,
                                vendor_id: vendor_id,
                                verification_type: 'MSME',
                                api_hit_id: result.api_hit_id,
                            },
                            async: false,
                            success: function (response) {
                                try {
                                    console.log("msme response", response);
                                    var result = JSON.parse(response);
                                    if (result && result.result && result.result.status === 'success') {
                                        // Lock the MSME step
                                        lockVerificationStep('msme');
                                        showNotification("MSME Verification completed and locked successfully", "success");
                                        $("#verify_msme_btn").removeClass('btn-success').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                                        verificationSteps.msme.locked = true;
                                    }
                                } catch (err) {
                                    console.error('Insert vendor parse error:', err);
                                }
                            },
                            error: function () { }
                        });

                        showNotification("MSME Details Verified Successfully", "success");
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
                    }
                } catch (e) {
                    showNotification("MSME Verification Parse Error: " + e.message, "error");
                }
            },
            error: function (xhr, status, error) {
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
            }
        });
    }
 
    function confirmBankDetails() {
        if (!window.bankVerificationData) {
            showNotification("No bank verification data found", "error");
            return;
        }

        // Show loading state
        $("#bank_manual_confirmation_section").hide();
        $("#verify_bank_btn").html('<i class="fa fa-spinner fa-spin"></i> Updating Status...');

        // Call ajax.php to update the status
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'insertvendorsandvendorregisters',
                ifsc_code: window.bankVerificationData.ifsc_code,
                bank_account_no: window.bankVerificationData.bank_account_no,
                transaction_table: window.bankVerificationData.transaction_table,
                vendor_id: window.bankVerificationData.vendor_id,
                verification_type: window.bankVerificationData.verification_type,
                api_hit_id: window.bankVerificationData.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log("Bank confirmation response", response);
                    
                    // Update UI to show completion
                    $("#bank_confirmation_section").show();
                    $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                    
                    // Mark verification as completed and locked
                    verificationSteps.bank.completed = true;
                    verificationSteps.bank.locked = true;
                    verificationSteps.bank.data = window.bankVerificationData.result;
                    
                    // Lock the Bank step
                    lockVerificationStep('bank');
                    
                    showNotification("Bank Account Verification completed and locked successfully", "success");
                    
                    // Clear stored data
                    window.bankVerificationData = null;
                    
                    // Optionally reload the page to refresh verification status
                    // location.reload();
                    
                } catch (err) {
                    console.error('Bank confirmation parse error:', err);
                    showNotification("Error processing bank confirmation", "error");
                }
            },
            error: function (xhr, status, error) {
                console.error('Bank confirmation error:', error);
                showNotification("Error confirming bank details", "error");
                
                // Reset button state
                $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank');
                $("#bank_manual_confirmation_section").show();
            }
        });
    }

    function cancelBankConfirmation() {
        // Hide manual confirmation section
        $("#bank_manual_confirmation_section").hide();
        
        // Reset verification fields
       // $("#bank_verification_fields").hide();
        $("#ifsc_code, #bank_account_no").prop('readonly', false);
        $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
        $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
        
        // Clear verification data
        $("#bank_verified_ifsc").val("");
        $("#bank_verified_account").val("");
        $("#bank_verified_name").val("");
        $("#bank_verified_status").val("");
        
        // Clear stored data
        window.bankVerificationData = null;
        
        // Reset verification step
        verificationSteps.bank.completed = false;
        verificationSteps.bank.data = null;
        
        showNotification("Bank verification cancelled. You can verify again.", "info");
    }

    function verifyTAN() {
        if (vendorVerifications.TAN && vendorVerifications.TAN.status === 'Y') {
            showNotification("TAN is already verified and locked. Cannot be modified.", "warning");
            return;
        }

        if (isStepLocked('tan')) {
            showNotification("TAN verification is locked and cannot be modified", "warning");
            return;
        }

        var tan_no = $("#tan_number").val();

        if (!validateTAN(tan_no)) {
            showNotification("Please enter a valid TAN number", "warning");
            return;
        }

        $("#tan_number").prop('readonly', true);
        $("#verify_tan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#tan_verification_fields").show();
        $("#tan_verified_company").val("Verifying...");
        $("#tan_verified_status").val("Verifying...");

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
                        $("#tan_verified_company").val(result.result.user_full_name || "N/A");
                        $("#tan_verified_status").val(result.success || "N/A");

                        verificationSteps.tan.completed = true;
                        verificationSteps.tan.data = result;

                        $("#tan_confirmation_section").show();
                        $("#verify_tan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');

                        var company_name_entered = $("#company_name").val().toLowerCase().trim();
                        var tan_company_name = (result.result.user_full_name || "").toLowerCase().trim();

                        if (company_name_entered === tan_company_name) {
                            showNotification("TAN Details are Correct", "success");
                            verificationSteps.tan.nameMatch = true;

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
                                        console.log("Tan response", response);
                                        var result = JSON.parse(response);
                                        lockVerificationStep('tan');
                                        showNotification("TAN Verification completed and locked successfully", "success");
                                        $("#verify_tan_btn").removeClass('btn-success').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                                        verificationSteps.tan.locked = true;
                                    } catch (err) {
                                        console.error('Insert vendor parse error:', err);
                                    }
                                },
                                error: function () { }
                            }); 
                        } else {
                            verificationSteps.tan.completed = false;
                            verificationSteps.tan.nameMatch = false;

                            $("#tan_confirmation_section").removeClass('confirmation-section').addClass('alert alert-warning');
                            $("#tan_confirmation_section h6").html('<i class="fa fa-exclamation-triangle text-warning"></i> TAN Verification Done - Data Mismatch');
                            $("#tan_confirmation_section p").html('TAN details have been retrieved but company name does not match. Please verify the information is correct before proceeding.');

                            showNotification("Verification completed but company name doesn't match with TAN company name. Please check the data.", "warning");
                            $("#tan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                            $("#verify_tan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Data Mismatch');
                        }

                    } else {
                        $("#tan_verified_company").val("Verification Failed");
                        $("#tan_verified_status").val("Verification Failed");
                        
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
                    }
                } catch (e) {
                    $("#tan_verified_company").val("Parse Error");
                    $("#tan_verified_status").val("Parse Error");
                    showNotification("TAN Verification Parse Error: " + e.message, "error");
                }
            },
            error: function (xhr, status, error) {
                $("#tan_verified_company").val("Error occurred");
                $("#tan_verified_status").val("Error occurred");
                
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
            }
        });
    }

    function lockVerificationStep(stepType) {
        console.log('Locking verification step:', stepType);
        
        // Add locked class to the step container
        $('#step-' + getStepNumber(stepType)).addClass('verification-step-locked');
        
        // Disable all input fields in the step
        $('#step-' + getStepNumber(stepType) + ' input, #step-' + getStepNumber(stepType) + ' textarea, #step-' + getStepNumber(stepType) + ' select').prop('readonly', true).addClass('custom_readonly');
        
        // Disable all buttons except the verification status button
        $('#step-' + getStepNumber(stepType) + ' button').not('.verification-status-btn').prop('disabled', true);
        
        // Update the edit button to show locked status
        $('#' + stepType + '_edit_btn').removeClass('btn-secondary btn-warning').addClass('btn-success').html('<i class="fa fa-lock"></i> Locked').prop('disabled', true);
        
        // Add locked indicator to step indicator
        var stepIndicator = $('.step-indicator .step').eq(getStepNumber(stepType) - 1);
        stepIndicator.addClass('completed locked');
        stepIndicator.find('.step-number').html('<i class="fa fa-lock"></i>');
        
        // Add locked message
        var lockedMessage = '<div class="alert alert-success mt-3"><i class="fa fa-lock"></i> This verification is completed and locked. Contact administrator if changes are needed.</div>';
        $('#step-' + getStepNumber(stepType)).append(lockedMessage);
    }

    function getStepNumber(stepType) {
        var stepMapping = {
            'pan': 1,
            'gstin': 2,
            'bank': 3,
            'tan': 4,
            'msme': 5
        };
        return stepMapping[stepType] || 1;
    }

    function isStepLocked(stepType) {
        return vendorVerifications[stepType.toUpperCase()] && vendorVerifications[stepType.toUpperCase()].status === 'Y';
    }

    function showNotification(message, type) {
        console.log("showNotification called:", message, type);
        
        // Map type to SweetAlert type
        var swalType = type;
        if (type === 'error') {
            swalType = 'error';
            swal("", message, swalType);
        } else if (type === 'warning') {
            swalType = 'warning';
            swal("", message, swalType);
        } else if (type === 'success') {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', message, '');
        } else {
            swalType = 'info';
            swal("", message, swalType);
        }
    }

    function showGSTAddressOptions(gstData) {
        var addressOptionsHtml = `
        <div class="row">
            <div class="col-lg-12">
                <h6><i class="fa fa-map-marker"></i> Address Options</h6>
                <p class="text-muted">Use the GST registered address for billing/shipping?</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Use for Billing Address</h6>
                        <button type="button" class="btn btn-primary btn-sm use-gst-address-btn" data-address-type="billing">
                            <i class="fa fa-copy"></i> Use GST Address
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Use for Shipping Address</h6>
                        <button type="button" class="btn btn-primary btn-sm use-gst-address-btn" data-address-type="shipping">
                            <i class="fa fa-copy"></i> Use GST Address
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        $("#gst_address_options").html(addressOptionsHtml);
    }

    function useGSTAddress(type) {
        var gstData = verificationSteps.gstin.data;
        if (gstData && gstData.result && gstData.result.primary_business_address) {
            var addr = gstData.result.primary_business_address;
            var addressText = '';
            var addressParts = [];

            if (addr.building_name) addressParts.push(addr.building_name);
            if (addr.building_number) addressParts.push(addr.building_number);
            if (addr.street) addressParts.push(addr.street);
            if (addr.city) addressParts.push(addr.city);
            if (addr.district) addressParts.push(addr.district);
            if (addr.state) addressParts.push(addr.state);
            if (addr.pincode) addressParts.push(addr.pincode);

            addressText = addressParts.join(', ');

            window.gstAddress = {
                type: type,
                address: addressText,
                city: addr.city || '',
                state: addr.state || '',
                pincode: addr.pincode || ''
            };

            showNotification('GST address saved for ' + type + ' address', 'success');
        }
    }
 

    function showNotification(message, type) {
        console.log("showNotification called:", message, type);
        
        // Map type to SweetAlert type
        var swalType = type;
        if (type === 'error') {
            swalType = 'error';
        } else if (type === 'warning') {
            swalType = 'warning';
        } else if (type === 'success') {
            swalType = 'success';
        } else {
            swalType = 'info';
        }
        
        // Use SweetAlert for notifications
        swal("", message, swalType);
    }

    (function () {
        function waitForJQuery() {
            if (typeof $ !== 'undefined') {
                initVerificationSystem();
            } else {
                setTimeout(waitForJQuery, 50);
            }
        }

        waitForJQuery();

        function initializeVerificationStatus() {
            console.log("Initializing verification status...");
            console.log("Vendor verifications:", vendorVerifications);

            if (!Array.isArray(visibleVerifications)) {
                console.error("visibleVerifications is not an array:", visibleVerifications);
                return;
            }

            visibleVerifications.forEach(function (verification, index) {
                var stepNumber = index + 1;
                var isVerified = vendorVerifications[verification] &&
                    vendorVerifications[verification].status === 'Y';

                if (isVerified) {
                    var stepElement = $('.step-indicator .step').eq(stepNumber - 1);
                    stepElement.addClass('completed locked');
                    stepElement.find('.step-number').html('<i class="fa fa-lock"></i>');
                    console.log('Marked step', stepNumber, 'as completed and locked for', verification);
                }
            });
        }

        function getFirstIncompleteStep() {
            if (!Array.isArray(visibleVerifications)) {
                console.error("visibleVerifications is not an array in getFirstIncompleteStep:", visibleVerifications);
                return 1;
            }
            
            for (var i = 0; i < visibleVerifications.length; i++) {
                var verification = visibleVerifications[i];
                var isVerified = vendorVerifications[verification] &&
                    vendorVerifications[verification].status === 'Y';

                if (!isVerified) {
                    return i + 1;
                }
            }
            return 1;
        }

        function initVerificationSystem() {
            $(document).ready(function () {
                console.log("Document ready, initializing verification system");
                console.log("Change Info functionality has been updated with improved readonly handling");
                
                // Initialize PHP data first
                initializePHPData();
                
                // Initialize verification steps after PHP data is loaded
                initializeVerificationSteps();
                
                testJQuery();

                initializeVerificationStatus();
                
                // Initialize locked steps
                initializeLockedSteps();

                updateStepIndicator();

                var firstIncompleteStep = getFirstIncompleteStep();
                currentStep = firstIncompleteStep;
                maxReachedStep = firstIncompleteStep;
                showStep(firstIncompleteStep);

                ensureVerificationDetailsReadonly();

                initializeButtonStates();
                
                // Initialize event handlers
                initializeEventHandlers();

                console.log("Verification system initialized");
                console.log("All Change Info buttons should now work properly - check console for debugging info");
            });
        }
        
        function initializeEventHandlers() {
            // Step indicator click handler
            $(document).on('click', '.step-indicator-item', function() {
                var step = $(this).data('step');
                if (step) {
                    goToStep(step);
                }
            });
            
            // Company email blur handler
            $(document).on('blur', '.company-email-input', function() {
                verifyPAN();
            });
            
            // Company name input handler
            $(document).on('input', '.company-name-input', function() {
                checkCompanyNameOnInput();
            });
            
            // PAN input handler
            $(document).on('input', '.pan-input', function() {
                formatPANInput(this);
            });
            
            // GST input handler
            $(document).on('input', '.gst-input', function() {
                formatGSTInput(this);
            });
            
            // IFSC input handler
            $(document).on('input', '.ifsc-input', function() {
                formatIFSCInput(this);
            });
            
            // Bank account input handler
            $(document).on('input', '.bank-account-input', function() {
                formatBankAccountInput(this);
            });
            
            // MSME input handler
            $(document).on('input', '.msme-input', function() {
                formatMSMEInput(this);
            });
            
            // TAN input handler
            $(document).on('input', '.tan-input', function() {
                formatTANInput(this);
            });
            
            // Toggle edit button handler
            $(document).on('click', '.toggle-edit-btn', function() {
                var section = $(this).data('section');
                if (section) {
                    toggleEdit(section);
                }
            });
            
            // Verify button handler
            $(document).on('click', '.verify-btn', function() {
                var verificationType = $(this).data('verification-type');
                switch(verificationType) {
                    case 'pan':
                        verifyPAN();
                        break;
                    case 'gst':
                        verifyGST();
                        break;
                    case 'bank':
                        verifyBank();
                        break;
                    case 'msme':
                        verifyMSME();
                        break;
                    case 'tan':
                        verifyTAN();
                        break;
                }
            });
            
            // Skip step button handler
            $(document).on('click', '.skip-step-btn', function() {
                var stepType = $(this).data('step-type');
                if (stepType) {
                    skipStepHandler(stepType);
                }
            });
            
            // Navigation button handlers
            $(document).on('click', '.prev-step-btn', function() {
                prevStep();
            });
            
            $(document).on('click', '.next-step-btn', function() {
                nextStep();
            });
            
            $(document).on('click', '.complete-verification-btn', function() {
                completeVerification();
            });
            
            // Bank confirmation handlers
            $(document).on('click', '.confirm-bank-btn', function() {
                confirmBankDetails();
            });
            
            $(document).on('click', '.cancel-bank-btn', function() {
                cancelBankConfirmation();
            });
            
            // GST address button handler
            $(document).on('click', '.use-gst-address-btn', function() {
                var addressType = $(this).data('address-type');
                if (addressType) {
                    useGSTAddress(addressType);
                }
            });
            
            console.log("Event handlers initialized");
        }

        function initializeLockedSteps() {
            console.log("Initializing locked steps...");
            
            // Check each verification type and lock if already verified
            var verificationTypes = ['PAN', 'GSTIN', 'BANK', 'MSME', 'TAN'];
            var stepMapping = {
                'PAN': 'pan',
                'GSTIN': 'gstin', 
                'BANK': 'bank',
                'MSME': 'msme',
                'TAN': 'tan'
            };
            
            verificationTypes.forEach(function(verificationType) {
                if (vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y') {
                    var stepType = stepMapping[verificationType];
                    console.log('Locking step:', stepType, 'for verification type:', verificationType);
                    lockVerificationStep(stepType);
                }
            });
        }

        function ensureVerificationDetailsReadonly() {
            console.log("Ensuring verification details are always readonly");

            $("#pan_verified_name, #pan_verified_type, #pan_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").prop('readonly', true).addClass('custom_readonly');

            $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#msme_verified_enterprise_name, #msme_verified_major_activity, #msme_verified_organization_type, #msme_verified_social_category, #msme_verified_commencement_date, #msme_verified_dic_name, #msme_verified_registration_date, #msme_verified_status").prop('readonly', true).addClass('custom_readonly');

            $("#tan_verified_company, #tan_verified_status").prop('readonly', true).addClass('custom_readonly');

            console.log("All verification details are now readonly and disabled");
        }
    })();