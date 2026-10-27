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
                        $("#allreadycompanyname").removeClass('d-none').addClass('d-block');
                        $("#company_check_status").val("exists");
                        return true;
                    } else {
                        console.log('frash');
                        $("#allreadycompanyname").removeClass('d-block').addClass('d-none');
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
            $("#allreadycompanyname").removeClass('d-block').addClass('d-none');
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
        $(".js-example-basic-single").select2();
        // Lock the PAN step
        lockVerificationStep('pan');
        verificationSteps.pan.locked = true;
        verificationSteps.pan.completed = true; 

        $('#pan_number, #company_name, #company_email, #company_type').prop('readonly', true).addClass('custom_readonly');
        $('#pan_verification_fields').removeClass('d-none').addClass('d-block');
        $('#pan_confirmation_section').removeClass('d-none').addClass('d-block');
        $('#verify_pan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked').prop('disabled', true);
        $('#verify_pan_btn_hidden').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked').prop('disabled', true);
        $('#pan_edit_btn').text('Locked').removeClass('btn-warning btn-secondary').addClass('btn-success').prop('disabled', true);

        $('#pan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> PAN Already Verified & Locked');
        $('#pan_confirmation_section p').html('This PAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + ' and is now locked.');

        // Note: Locked message is already shown in PHP template, no need to add duplicate
    }

    function setupPreCompletedGST(data) {
        var stepDiv = $('#step-' + getStepForSection('gstin'));
        stepDiv.addClass('pre-completed-verification');

        $('#gstin').prop('readonly', true);
        $('#gst_verification_fields').removeClass('d-none').addClass('d-block');
        $('#gst_confirmation_section').removeClass('d-none').addClass('d-block');
        $('#verify_gst_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#gstin_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#gst_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> GST Already Verified');
        $('#gst_confirmation_section p').html('This GST has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        // Note: Locked message is already shown in PHP template, no need to add duplicate
    }

    function setupPreCompletedBank(data) {
        var stepDiv = $('#step-' + getStepForSection('bank'));
        stepDiv.addClass('pre-completed-verification');

        $('#ifsc_code, #bank_account_no, #bank_name').prop('readonly', true);
        $('#account_type').prop('disabled', true);
        $('#bank_verification_fields').removeClass('d-none').addClass('d-block');
        $('#bank_confirmation_section').removeClass('d-none').addClass('d-block');
        $('#verify_bank_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#bank_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#bank_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> Bank Already Verified');
        $('#bank_confirmation_section p').html('This bank account has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        // Note: Locked message is already shown in PHP template, no need to add duplicate
    }

    function setupPreCompletedMSME(data) {
        var stepDiv = $('#step-' + getStepForSection('msme'));
        stepDiv.addClass('pre-completed-verification');

        $('#msme_number').prop('readonly', true);
        $('#msme_verification_fields').removeClass('d-none').addClass('d-block');
        $('#msme_confirmation_section').removeClass('d-none').addClass('d-block');
        $('#verify_msme_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#msme_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#msme_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> MSME Already Verified');
        $('#msme_confirmation_section p').html('This MSME has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        // Note: Locked message is already shown in PHP template, no need to add duplicate
    }

    function setupPreCompletedTAN(data) {
        var stepDiv = $('#step-' + getStepForSection('tan'));
        stepDiv.addClass('pre-completed-verification');

        $('#tan_number').prop('readonly', true);
        $('#tan_verification_fields').removeClass('d-none').addClass('d-block');
        $('#tan_confirmation_section').removeClass('d-none').addClass('d-block');
        $('#verify_tan_btn').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Already Verified').prop('disabled', true);
        $('#tan_edit_btn').text('Already Verified').removeClass('btn-warning').addClass('btn-success').prop('disabled', true);

        $('#tan_confirmation_section h6').html('<i class="fa fa-check-circle text-success"></i> TAN Already Verified');
        $('#tan_confirmation_section p').html('This TAN has been previously verified on ' + (data.verification_datetime || 'a previous date') + '.');

        // Note: Locked message is already shown in PHP template, no need to add duplicate
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
        
        // Ensure unverified steps are not locked and buttons are enabled
        visibleVerifications.forEach(function(verificationType) {
            var isVerified = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
            var stepType = verificationType.toLowerCase();
            
            if (!isVerified) {
                // Remove readonly from main input fields for unverified steps
                $('#' + stepType + '_number, #' + stepType + 'in, #ifsc_code, #bank_account_no, #bank_name, #account_type, #msme_number, #tan_number').prop('readonly', false).removeClass('custom_readonly');
                
                // Enable buttons for unverified steps
                $('#' + stepType + '_edit_btn, #verify_' + stepType + '_btn, #verify_' + stepType + '_btn_visible').prop('disabled', false);
                
                // Special handling for TAN since it's not required
                if (stepType === 'tan') {
                    $('#tan_number').prop('readonly', false).removeClass('custom_readonly');
                    $('#tan_edit_btn, #verify_tan_btn, #verify_tan_btn_visible').prop('disabled', false);
                    $('#tan_edit_btn').removeClass('btn-success').addClass('btn-warning').text('Change Info');
                    $('.skip-step-btn[data-step-type="tan"]').prop('disabled', false);
                }
                
                console.log('Enabled buttons for unverified step:', stepType);
            }
        });
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
        console.log('currentVerificationStep:', currentVerificationStep);
       
        // Check if current step is already verified and locked in database
        var isVerifiedAndLocked = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
       
        if (isVerifiedAndLocked) {
            console.log('Step is already verified and locked in database, allowing next step');
            // If current step is already verified and locked, just move to next step
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
       
        // Check if this step is locked (verified and confirmed by user)
        var isStepLocked = currentVerificationStep && currentVerificationStep.locked;
       
        console.log('isStepLocked:', isStepLocked);
        console.log('currentVerificationStep.locked:', currentVerificationStep ? currentVerificationStep.locked : 'undefined');
       
        if (!isStepLocked) {
            var sectionName = currentSectionKey.toUpperCase();
            console.log('Step is not locked, showing warning for:', sectionName);
            showNotification("Please complete and confirm " + sectionName + " verification before proceeding to the next step.", "warning");
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
/**
    function nextStep() {
        var currentSectionKey = getSectionForStep(currentStep);
        var currentVerificationStep = verificationSteps[currentSectionKey];
        var verificationType = currentSectionKey.toUpperCase();
        
        console.log('nextStep called for step:', currentStep, 'section:', currentSectionKey, 'verificationType:', verificationType);
        
        // Check if current step is already verified and locked in database
        var isVerifiedAndLocked = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
        
        if (isVerifiedAndLocked) {
            console.log('Step is already verified and locked in database, allowing next step');
            // If current step is already verified and locked, just move to next step
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
        
        // Check if this step is locked (verified and confirmed by user)
        var isStepLocked = currentVerificationStep && currentVerificationStep.locked;
        
        if (!isStepLocked) {
            var sectionName = currentSectionKey.toUpperCase();
            showNotification("Please complete and confirm " + sectionName + " verification before proceeding to the next step.", "warning");
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
**/
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
                return $('#pan_number').val().trim() !== '' || $('#company_name').val().trim() !== '' || $('#company_type').val().trim() !== '';
            case 'gstin':
                return $('#gstin').val().trim() !== '';
            case 'bank':
                return $('#ifsc_code').val().trim() !== '' || $('#bank_account_no').val().trim() !== '' || $('#bank_name').val().trim() !== '' || $('#account_type').val().trim() !== '';
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
                $('#company_type').val('');
                break;
            case 'gstin':
                $('#gstin').val('');
                break;
            case 'bank':
                $('#ifsc_code').val('');
                $('#bank_account_no').val('');
                $('#bank_name').val('');
                $('#account_type').val('');
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

        if (verificationStep.data && verificationStep.nameMatch === false && !verificationStep.skipped && !verificationStep.userConfirmed) {
            var sectionName = sectionKey.toUpperCase();
            showNotification(sectionName + " verification shows company name mismatch. Please confirm the company name details to proceed to the next step.", "warning");
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
        $('.verification-step').removeClass('d-block').addClass('d-none');
        $('#step-' + step).removeClass('d-none').addClass('d-block');

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
        console.log('skipStep called with stepType:', stepType);
       
        // Get the current section key to ensure consistency
        var currentSectionKey = getSectionForStep(currentStep);
        console.log('currentSectionKey from getSectionForStep:', currentSectionKey);
        console.log('currentStep:', currentStep);
       
        // Use the current section key instead of the passed stepType to ensure consistency
        var sectionToSkip = currentSectionKey;
       
        console.log('verificationSteps:', verificationSteps);
        console.log('verificationSteps[sectionToSkip]:', verificationSteps[sectionToSkip]);
       
        if (hasDataInCurrentStep()) {
            clearCurrentStepData();
        }
 
        // Ensure the verification step object exists
        if (!verificationSteps[sectionToSkip]) {
            verificationSteps[sectionToSkip] = {};
        }
       
        verificationSteps[sectionToSkip].skipped = true;
        verificationSteps[sectionToSkip].completed = false;
        verificationSteps[sectionToSkip].locked = true; // Mark as locked so user can proceed to next step
       
        console.log('After setting properties, verificationSteps[sectionToSkip]:', verificationSteps[sectionToSkip]);
 
        var stepName = sectionToSkip.toUpperCase();
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.verification-fields').removeClass('d-block').addClass('d-none');
        stepDiv.find('.confirmation-section').removeClass('d-block').addClass('d-none');
 
        var skipMessage = `
        <div class="alert alert-info mt-3" id="${sectionToSkip}_skip_message">
            <i class="fa fa-info-circle"></i> ${stepName} verification has been skipped.
            You can complete this verification later if needed.
        </div>`;
 
        stepDiv.find(`#${sectionToSkip}_skip_message`).remove();
        stepDiv.find('.step-header').after(skipMessage);
 
        showNotification(stepName + " verification skipped automatically", "info");
 
        if (currentStep === totalSteps) {
            // finishVerification();
        } else {
            console.log('Calling nextStep() after skip');
            nextStep();
        }
    }
/**
    function skipStep(stepType) { 
        if (hasDataInCurrentStep()) {
            clearCurrentStepData();
        }
 
        verificationSteps[stepType].skipped = true;
        verificationSteps[stepType].completed = false;
 
        var stepName = stepType.toUpperCase();
        var stepDiv = $('#step-' + currentStep);
        stepDiv.find('.verification-fields').removeClass('d-block').addClass('d-none');
        stepDiv.find('.confirmation-section').removeClass('d-block').addClass('d-none');

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
    } **/

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
        
        // No auto-verification, user must click verify button
    }

    function checkBankFieldsComplete() {
        var ifsc = $("#ifsc_code").val().trim();
        var account = $("#bank_account_no").val().trim();
        var bankName = $("#bank_name").val().trim();
        var accountType = $("#account_type").val();

        return validateIFSC(ifsc) && account && account.length >= 8 && bankName && accountType;
    }

    function validateBankFields() {
        var accountType = $("#account_type").val();
        var bankName = $("#bank_name").val().trim();
        var ifsc = $("#ifsc_code").val().trim();
        var account = $("#bank_account_no").val().trim();

        if (!accountType) {
            showNotification("Please select an account type", "warning");
            return false;
        }

        if (!bankName) {
            showNotification("Please enter bank name", "warning");
            return false;
        }

        if (!ifsc) {
            showNotification("Please enter IFSC code", "warning");
            return false;
        }

        if (!validateIFSC(ifsc)) {
            showNotification("Please enter a valid IFSC code", "warning");
            return false;
        }

        if (!account) {
            showNotification("Please enter account number", "warning");
            return false;
        }

        if (account.length < 8) {
            showNotification("Please enter a valid account number (minimum 8 digits)", "warning");
            return false;
        }

        return true;
    }

    function formatBankAccountInput(input) {
        var value = input.value.replace(/[^0-9]/g, '');
        input.value = value;
        
        // No auto-verification, user must click verify button
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
        if(value.length === 19){       
            if (isValid) {
                setTimeout(function () {
                    verifyMSME();
                     console.log('msme timeout');
                }, 500);
            }else{
                showNotification("The MSME number is not properly formatted.", "error");
            }
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

        // Check if the section is locked (especially for PAN)
        if (section === 'pan' && (isStepLocked('pan') || verificationSteps.pan.locked)) {
            showNotification("PAN verification is locked and cannot be edited", "warning");
            return;
        }

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
            $('#pan_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#pan_company_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#verify_pan_btn').removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#verify_pan_btn_hidden').removeClass('btn-success btn-warning').addClass('btn-primary').html('').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification status reset');
        } else if (stepType === 'gstin') { 
            $('#gst_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#gst_address_options').removeClass('d-block').addClass('d-none');
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
            $('#bank_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#bank_manual_confirmation_section').removeClass('d-block').addClass('d-none');
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
            $('#msme_confirmation_section').removeClass('d-block').addClass('d-none');
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
            $('#tan_confirmation_section').removeClass('d-block').addClass('d-none');
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
            $('#pan_verification_fields').removeClass('d-block').addClass('d-none');
            $('#pan_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#pan_company_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#verify_pan_btn').removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
            $('#verify_pan_btn_hidden').removeClass('btn-success btn-warning').addClass('btn-primary').html('').prop('disabled', false);
            $('#pan_verified_name').val('');
            $('#pan_verified_type').val('');
            $('#pan_verified_status').val('');
            $('#pan_number').closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
            $('#pan_number').closest('.input-group').removeClass('has-success has-error');
            console.log('PAN verification reset');
        } else if (stepType === 'gstin') {
            $('#gst_verification_fields').removeClass('d-block').addClass('d-none');
            $('#gst_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#gst_address_options').removeClass('d-block').addClass('d-none');
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
            $('#bank_verification_fields').removeClass('d-block').addClass('d-none');
            $('#bank_confirmation_section').removeClass('d-block').addClass('d-none');
            $('#bank_manual_confirmation_section').removeClass('d-block').addClass('d-none');
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
            $('#msme_verification_fields').removeClass('d-block').addClass('d-none');
            $('#msme_confirmation_section').removeClass('d-block').addClass('d-none');
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
            $('#tan_verification_fields').removeClass('d-block').addClass('d-none');
            $('#tan_confirmation_section').removeClass('d-block').addClass('d-none');
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

        // Check if PAN step is locked
        if (isStepLocked('pan') || verificationSteps.pan.locked) {
            showNotification("PAN verification is locked and cannot be modified", "warning");
            return;
        }

        var pan_no = $("#pan_number").val();
        var company_name = $("#company_name").val();
        var company_email = $("#company_email").val();
        var company_type = $("#company_type").val();

        // Validate Required fields
        if (!company_type.trim()) {
            showNotification("Please select company type", "warning");
            return;
        }

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

        // Check if verification is already in progress or completed
        if (($("#verify_pan_btn").prop('disabled') && $("#verify_pan_btn").is(':visible')) || 
            ($("#verify_pan_btn_hidden").prop('disabled')) || 
            verificationSteps.pan.completed) {
            return;
        }

        // Disable both buttons and set readonly state
        $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', true);
        $("#company_type").prop('disabled', true);
        $("#verify_pan_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        $("#verify_pan_btn_hidden").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
        $("#pan_verification_fields").removeClass('d-none').addClass('d-block');
        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verifying...");

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: {
                'pan_holder_name': company_name,
                'customer_pan_number': pan_no,
                'company_type': company_type,
                'company_email': company_email,
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

                        $("#pan_confirmation_section").removeClass('d-none').addClass('d-block');
                        $("#verify_pan_btn").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#verify_pan_btn_hidden").removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
                        $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', true);
                        $("#company_type").prop('disabled', true);
                        $("#pan_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-success');
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").prop('readonly', true).addClass('custom_readonly');

                        var company_name_entered = company_name.toLowerCase().trim();
                        var pan_holder_name = (result.result.user_full_name || result.result.pan_holder_name || "").toLowerCase().trim();

                        // Store the verified company name
                        $("#verified_company_name").val(result.result.user_full_name || result.result.pan_holder_name || "");

                        if (company_name_entered === pan_holder_name) {
                            // Names match - proceed with direct save
                            showNotification("PAN Details are Correct", "success");
                            verificationSteps.pan.nameMatch = true;
                            verificationSteps.pan.completed = true;
                            
                            // Update company name field with verified name (even if they match, use the verified one)
                            $("#company_name").val(result.result.user_full_name || result.result.pan_holder_name || "");
                            vendorDetailCompany = result.result.user_full_name || result.result.pan_holder_name || "";
                            
                            // Update the PHP data container as well
                            $('.vendor-detail-company-data').text(result.result.user_full_name || result.result.pan_holder_name || "");
                            
                            savePANVerification(company_name, result.result.user_full_name || result.result.pan_holder_name || "", pan_no, company_email, result);
                        } else {
                            // Names don't match - show confirmation dialog
                            verificationSteps.pan.completed = false;
                            verificationSteps.pan.nameMatch = false;
                            verificationSteps.pan.userConfirmed = false; // Add flag for user confirmation
                            
                            // Show company name confirmation section
                            $("#confirm_user_company_name_display").text(company_name);
                            $("#confirm_pan_company_name_display").text(result.result.user_full_name || result.result.pan_holder_name || "");
                            $("#confirm_pan_number_display").text(pan_no);
                            $("#pan_company_confirmation_section").removeClass('d-none').addClass('d-block');
                            $("#pan_confirmation_section").removeClass('d-block').addClass('d-none');
                            
                            showNotification("Data fetched successfully! Please verify the company name details to proceed.", "info");
                            $("#pan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');
                            $("#verify_pan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Verification Required');
                            $("#verify_pan_btn_hidden").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Verification Required');
                        }

                    } else {
                        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "Unable to verify PAN at this time. ";
                        if (result.response_message) {
                            errorMessage += result.response_message;
                        } else {
                            errorMessage += "Please check your PAN number and try again.";
                        }
                        
                        showNotification(errorMessage, "error");
                        
                        // Reset button states on error
                        $("#verify_pan_btn").removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
                        $("#verify_pan_btn_hidden").removeClass('btn-success btn-warning').addClass('btn-primary').html('').prop('disabled', false);
                        $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', false);
                        $("#company_type").prop('disabled', false);
                    }
                } catch (e) {
                    $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verification Failed");
                    showNotification("Unable to process PAN verification response. Please try again.", "error");
                    
                    // Reset button states on error
                    $("#verify_pan_btn").removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
                    $("#verify_pan_btn_hidden").removeClass('btn-success btn-warning').addClass('btn-primary').html('').prop('disabled', false);
                    $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', false);
                    $("#company_type").prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("Verification Failed");
                
                var errorMessage = "Network error occurred during PAN verification. Please check your connection and try again.";
                
                showNotification(errorMessage, "error");
                
                // Reset button states on error
                $("#verify_pan_btn").removeClass('btn-success btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN').prop('disabled', false);
                $("#verify_pan_btn_hidden").removeClass('btn-success btn-warning').addClass('btn-primary').html('').prop('disabled', false);
                $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', false);
                $("#company_type").prop('disabled', false);
            }
        });
    }

    // Function to save PAN verification data
    function savePANVerification(userCompanyName, verifiedCompanyName, panNo, companyEmail, verificationResult) {
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'insertvendorsandvendorregisters',
                company_name: verifiedCompanyName, 
                display_name: verifiedCompanyName,
                user_entered_company_name: userCompanyName,
                pan_no: panNo,
                company_email: companyEmail,
                transaction_table: transaction_table,
                vendor_id: vendor_id,
                verification_type: 'PAN',
                api_hit_id: verificationResult.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log('PAN save response:', response);
                    console.log('About to lock PAN step...');
                    
                    // Update the company name field with verified name
                    $("#company_name").val(verifiedCompanyName);
                    
                    // Also update the global variable for consistency
                    vendorDetailCompany = verifiedCompanyName;
                    
                    // Update the PHP data container as well
                    $('.vendor-detail-company-data').text(verifiedCompanyName);
                    
                    // Lock the PAN step after successful save
                    lockVerificationStep('pan');
                    verificationSteps.pan.locked = true;
                    verificationSteps.pan.completed = true;
                    
                    console.log('PAN step locked successfully. Verification steps:', verificationSteps.pan);
                    
                    // Update button to show locked state
                    $("#verify_pan_btn").removeClass('btn-success btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked').prop('disabled', true);
                    $("#verify_pan_btn_hidden").removeClass('btn-success btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked').prop('disabled', true);
                    $("#pan_edit_btn").text('Locked').removeClass('btn-secondary btn-success').addClass('btn-success').prop('disabled', true);
                    $(".js-example-basic-single").select2();
                    // Make all fields permanently readonly
                    $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', true).addClass('custom_readonly');
                    
                    // Add visual confirmation that step is locked
                    $("#pan_confirmation_section").removeClass('d-none').addClass('d-block');
                    $("#pan_confirmation_section h6").html('<i class="fa fa-check-circle text-success"></i> PAN Verification Completed & Locked');
                    $("#pan_confirmation_section p").html('PAN details have been successfully verified and locked.');
                    
                    showNotification("PAN verification completed and locked successfully", "success");
                    
                    // Try to parse response for additional handling
                    try {
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
                        }
                    } catch (parseErr) {
                        console.log('Response parsing optional - continuing with lock');
                    }
                    
                } catch (err) {
                    console.error('PAN save parse error:', err);
                    showNotification("PAN verification saved but UI update failed", "warning");
                }
            },
            error: function (xhr, status, error) {
                console.error('PAN save error:', error);
                showNotification("Error saving PAN verification data", "error");
            }
        });
    }

    // Function to confirm PAN company details
    function confirmPANCompanyDetails() {
        var userCompanyName = $("#confirm_user_company_name_display").text();
        var verifiedCompanyName = $("#confirm_pan_company_name_display").text();
        var panNo = $("#confirm_pan_number_display").text();
        var companyEmail = $("#company_email").val();
        
        // Get the stored verification result
        var verificationResult = verificationSteps.pan.data;
        
        // Update the company name field with verified name
        $("#company_name").val(verifiedCompanyName);
        
        // Add visual indicator that company name was updated
        $("#company_name").addClass('field-updated').attr('title', 'Company name updated from PAN verification');
        
        // Also update the global variable for consistency
        vendorDetailCompany = verifiedCompanyName;
        
        // Update the PHP data container as well
        $('.vendor-detail-company-data').text(verifiedCompanyName);
        
        verificationSteps.pan.completed = true;
        verificationSteps.pan.nameMatch = false; // Names don't match but user confirmed
        verificationSteps.pan.userConfirmed = true; // User has confirmed the mismatch
        
        // Hide confirmation section and show success
        $("#pan_company_confirmation_section").removeClass('d-block').addClass('d-none');
        $("#pan_confirmation_section").removeClass('d-none').addClass('d-block');
        $("#verify_pan_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
        $("#verify_pan_btn_hidden").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified');
        
        showNotification("Company name updated successfully! PAN verification completed.", "success");
        
        // Save with verified company name (this will also lock the step)
        savePANVerification(userCompanyName, verifiedCompanyName, panNo, companyEmail, verificationResult);
    }

    // Function to confirm GST details
    function confirmGSTDetails() {
        var verificationResult = verificationSteps.gstin.data;
        var gstin = $("#gstin").val();
        
        // Extract address data from verification result
        var full_address = "";
        var state = "";
        var city = "";
        var location = "";
        var pincode = "";
        
        if (verificationResult.result && verificationResult.result.primary_business_address) {
            var addr = verificationResult.result.primary_business_address;
            full_address = addr.full_address || "";
            state = addr.state_code || "";
            city = addr.district || "";
            location = addr.location || "";
            pincode = addr.pincode || "";
        }
        
        // Save GST verification data
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
                api_hit_id: verificationResult.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log("GST save response", response);
                    
                    verificationSteps.gstin.completed = true;
                    verificationSteps.gstin.locked = true;
                    verificationSteps.gstin.userConfirmed = true;
                    
                    // Hide confirmation section and show success
                    $("#gst_manual_confirmation_section").removeClass('d-block').addClass('d-none');
                    $("#gst_confirmation_section").removeClass('d-none').addClass('d-block');
                    $("#verify_gst_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    $("#verify_gst_btn_visible").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    
                    // Make fields readonly after confirmation
                    $("#gstin").prop('readonly', true).addClass('custom_readonly');
                    $("#gstin").closest('.input-group').find('.status-icon').html('<i class="fa fa-check text-success"></i>');
                    
                    showNotification("GST verification completed and locked successfully.", "success");
                    
                    // Lock the step
                    lockVerificationStep('gstin');
                } catch (err) {
                    console.error('GST save parse error:', err);
                    showNotification("GST verification saved but UI update failed", "warning");
                }
            },
            error: function (xhr, status, error) {
                console.error('GST save error:', error);
                showNotification("Error saving GST verification data", "error");
            }
        });
    }

    // Function to cancel GST confirmation
    function cancelGSTConfirmation() {
        // Reset verification state
        verificationSteps.gstin.completed = false;
        verificationSteps.gstin.locked = false;
        verificationSteps.gstin.data = null;
        verificationSteps.gstin.nameMatch = null;
        
        // Hide confirmation section
        $("#gst_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Make fields editable temporarily (keep verification fields visible)
        $("#gstin").prop('readonly', false).removeClass('custom_readonly');
        $("#verify_gst_btn").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST');
        $("#verify_gst_btn_visible").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify GST');
        $("#gstin").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Keep verification fields visible but clear them
        $("#gstin_legal_name, #gstin_trade_name, #gstin_registration_status, #gstin_taxpayer_type, #gstin_business_nature, #gstin_registration_date, #gstin_business_address").val("");
        // Don't hide verification fields - keep them visible
        
        // Reset edit button
        $("#gstin_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-secondary');
        
        showNotification("GST verification cancelled. You can modify and verify again.", "info");
    }

    // Function to cancel MSME confirmation
    function cancelMSMEConfirmation() {
        // Reset verification state
        verificationSteps.msme.completed = false;
        verificationSteps.msme.locked = false;
        verificationSteps.msme.data = null;
        
        // Hide confirmation section
        $("#msme_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Reset form fields to editable state
        $("#msme_number").prop('readonly', false);
        $("#verify_msme_btn").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME');
        $("#verify_msme_btn_visible").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME');
        $("#msme_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Clear verification fields
        $("#msme_verified_enterprise_name, #msme_verified_major_activity, #msme_verified_organization_type, #msme_verified_social_category, #msme_verified_commencement_date, #msme_verified_dic_name, #msme_verified_registration_date, #msme_verified_status").val("");
        $("#msme_verification_fields").removeClass('d-block').addClass('d-none');
        
        // Reset edit button
        $("#msme_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
        
        showNotification("Verification cancelled. You can update the details and try again.", "info");
    }

    // Function to confirm TAN details
    function confirmTANDetails() {
        var verificationResult = verificationSteps.tan.data;
        
        verificationSteps.tan.completed = true;
        verificationSteps.tan.locked = true;
        verificationSteps.tan.userConfirmed = true;
        
        // Hide confirmation section and show success
        $("#tan_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        $("#tan_confirmation_section").removeClass('d-none').addClass('d-block');
        $("#verify_tan_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
        $("#verify_tan_btn_visible").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
        
        // Make fields readonly after confirmation
        $("#tan_number").prop('readonly', true).addClass('custom_readonly');
        $("#tan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-check text-success"></i>');
        
        showNotification("TAN verification completed and locked successfully.", "success");
        
        // Lock the step
        lockVerificationStep('tan');
    }

    // Function to cancel TAN confirmation
    function cancelTANConfirmation() {
        // Reset verification state
        verificationSteps.tan.completed = false;
        verificationSteps.tan.locked = false;
        verificationSteps.tan.data = null;
        verificationSteps.tan.nameMatch = null;
        
        // Hide confirmation section
        $("#tan_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Make fields editable temporarily (keep verification fields visible)
        $("#tan_number").prop('readonly', false).removeClass('custom_readonly');
        $("#verify_tan_btn").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN');
        $("#verify_tan_btn_visible").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN');
        $("#tan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Keep verification fields visible but clear them
        $("#tan_verified_company, #tan_verified_status").val("");
        // Don't hide verification fields - keep them visible
        
        // Reset edit button
        $("#tan_edit_btn").text('Change Info').removeClass('btn-success').addClass('btn-secondary');
        
        showNotification("TAN verification cancelled. You can modify and verify again.", "info");
    }

    // Function to cancel PAN company confirmation
    function cancelPANCompanyConfirmation() {
        // Check if step is locked
        if (isStepLocked('pan') || verificationSteps.pan.locked) {
            showNotification("PAN verification is locked and cannot be modified", "warning");
            return;
        }
        
        // Reset verification state
        verificationSteps.pan.completed = false;
        verificationSteps.pan.nameMatch = false;
        
        // Hide confirmation section
        $("#pan_company_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Reset form fields to editable state
        $("#pan_number, #company_name, #company_email, #company_type").prop('readonly', false);
        $("#company_type").prop('disabled', false);
        $("#verify_pan_btn").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify PAN');
        $("#verify_pan_btn_hidden").prop('disabled', false).removeClass('btn-warning btn-success').addClass('btn-primary').html('');
        $("#pan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Clear verification fields
        $("#pan_verified_name, #pan_verified_type, #pan_verified_status").val("");
        $("#pan_verification_fields").removeClass('d-block').addClass('d-none');
        
        showNotification("Verification cancelled. You can update the details and try again.", "info");
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

        $("#gst_verification_fields").removeClass('d-none').addClass('d-block');
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
                        
                        // Always show confirmation dialog for user to verify data
                        $("#confirm_gst_legal_name_display").text(result.result.legal_name || "N/A");
                        $("#confirm_gst_trade_name_display").text(result.result.trade_name || "N/A");
                        $("#confirm_gst_number_display").text(gstin);
                        $("#gst_manual_confirmation_section").removeClass('d-none').addClass('d-block');
                        $("#gst_confirmation_section").removeClass('d-block').addClass('d-none');
                        
                        showNotification("GST data fetched successfully! Please verify the details to proceed.", "info");
                        $("#gstin").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');
                        $("#verify_gst_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');
                        $("#verify_gst_btn_visible").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');

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
                        // if (result.api_hit_id) {
                        //     errorMessage += " - api_hit_id: " + result.api_hit_id;
                        // }
                        // if (result.response_code) {
                        //     errorMessage += " - response_code: " + result.response_code;
                        // }
                        if (result.response_message) {
                            errorMessage += result.response_message;
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

        // Validate all required fields before proceeding
        if (!validateBankFields()) {
            return;
        }

        var ifsc = $("#ifsc_code").val();
        var account = $("#bank_account_no").val();
        var bankName = $("#bank_name").val();
        var accountType = $("#account_type").val();

        if (!account) {
            showNotification("Please enter bank account number", "warning");
            return;
        }

        if ($("#verify_bank_btn").prop('disabled') || verificationSteps.bank.completed) {
            return;
        }

        console.log('Bank Verification: Locking inputs');
        $("#ifsc_code, #bank_account_no, #bank_name").prop('readonly', true);
        $("#account_type").prop('disabled', true);
        $("#verify_bank_btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

        $("#bank_verification_fields").removeClass('d-none').addClass('d-block');
        $("#bank_verified_ifsc").val("Verifying...");
        $("#bank_verified_account").val("Verifying...");
        $("#bank_verified_name").val("Verifying...");
        $("#bank_verified_status").val("Verifying...");

        var postData = {
            'account_number': account,
            'ifsc': ifsc,
            'bank_name': bankName,
            'account_type': accountType,
            'transaction_table': transaction_table,
            'transaction_id': vendor_id,
            'api_code': 'BANKACVERIFICATIONLITE',
            'consent': 'Y',
            'consent_text': 'I hear by declare my consent agreement for fetching my information via ZOOP AP',
            'mode': 'sync'
        };

        console.log('Sending bank verification data:', postData);

        $.ajax({
            url: "apis/call_api.php",
            type: "POST",
            data: postData,
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
                        $("#bank_manual_confirmation_section").removeClass('d-none').addClass('d-block');
                        $("#verify_bank_btn").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');
                        $("#verify_bank_btn_visible").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');

                        console.log('Bank verification successful - showing manual confirmation');
                        $("#ifsc_code, #bank_account_no, #bank_name").prop('readonly', true);
                        $("#account_type").prop('disabled', true);
                        $("#bank_edit_btn").text('Change Info').removeClass('btn-warning').addClass('btn-secondary');
                        
                        // Update status icon
                        $("#bank_account_no").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                        $("#bank_verified_ifsc, #bank_verified_account, #bank_verified_name, #bank_verified_status").prop('readonly', true).addClass('custom_readonly');

                        // Store verification data for later use
                        window.bankVerificationData = {
                            ifsc_code: ifsc,
                            bank_account_no: account,
                            bank_name: bankName,
                            account_type: accountType,
                            transaction_table: transaction_table,
                            vendor_id: vendor_id,
                            verification_type: 'BANK',
                            api_hit_id: result.api_hit_id,
                            result: result
                        };

                        console.log('Stored bank verification data:', window.bankVerificationData);

                        // Populate confirmation display fields
                        $("#confirm_ifsc_display").text(ifsc);
                        $("#confirm_account_display").text(account);
                        $("#confirm_name_display").text(result.result.beneficiary_name || "N/A");

                        showNotification("Bank data fetched successfully! Please verify the details to proceed.", "info");

                    } else {
                        $("#bank_verified_ifsc").val("Verification Failed");
                        $("#bank_verified_account").val("Verification Failed");
                        $("#bank_verified_name").val("Verification Failed");
                        $("#bank_verified_status").val(result.response_message || "Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "Bank Account Verification Failed";
                        // if (result.api_hit_id) {
                        //     errorMessage += " - api_hit_id: " + result.api_hit_id;
                        // }
                        // if (result.response_code) {
                        //     errorMessage += " - response_code: " + result.response_code;
                        // }
                        if (result.response_message) {
                            errorMessage +=result.response_message;
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

        $("#msme_verification_fields").removeClass('d-none').addClass('d-block');
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

                        // Store verification data
                        verificationSteps.msme.data = result;
                        verificationSteps.msme.completed = false; // Will be set to true when user confirms
                        
                        // Store verification data for confirmation function
                        window.msmeVerificationData = {
                            msme_number: msme_number,
                            transaction_table: transaction_table,
                            vendor_id: vendor_id,
                            verification_type: 'MSME',
                            api_hit_id: result.api_hit_id,
                            result: result
                        };
                        
                        // Always show confirmation dialog for user to verify data
                        $("#confirm_msme_enterprise_name_display").text(result.result.name_of_enterprise || "N/A");
                        $("#confirm_msme_org_type_display").text(result.result.organization_type || "N/A");
                        $("#confirm_msme_reg_number_display").text(msme_number);
                        $("#msme_manual_confirmation_section").removeClass('d-none').addClass('d-block');
                        $("#msme_confirmation_section").removeClass('d-block').addClass('d-none');
                        
                        showNotification("MSME data fetched successfully! Please verify the details to proceed.", "info");
                        $("#msme_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');
                        $("#verify_msme_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');
                        $("#verify_msme_btn_visible").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');

                    } else {
                        $("#msme_verified_enterprise_name").val("Verification Failed");
                        $("#msme_verified_major_activity").val("Verification Failed");
                        $("#msme_verified_organization_type").val("Verification Failed");
                        $("#msme_verified_social_category").val("Verification Failed");
                        $("#msme_verified_commencement_date").val("Verification Failed");
                        $("#msme_verified_dic_name").val("Verification Failed");
                        $("#msme_verified_registration_date").val("Verification Failed");
                        $("#msme_verified_status").val(result.response_message || "Verification Failed");

                        showNotification("Unable to verify MSME at this time. Please check your registration number and try again.", "error");
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

        console.log('Confirming bank details with data:', window.bankVerificationData);

        // Show loading state
        $("#bank_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        $("#verify_bank_btn").html('<i class="fa fa-spinner fa-spin"></i> Updating Status...');

        var postData = {
            page: 'insertvendorsandvendorregisters',
            ifsc_code: window.bankVerificationData.ifsc_code,
            bank_account_no: window.bankVerificationData.bank_account_no,
            bank_name: window.bankVerificationData.bank_name,
            account_type: window.bankVerificationData.account_type,
            transaction_table: window.bankVerificationData.transaction_table,
            vendor_id: window.bankVerificationData.vendor_id,
            verification_type: window.bankVerificationData.verification_type,
            api_hit_id: window.bankVerificationData.api_hit_id,
        };

        console.log('Sending confirmation data to ajax.php:', postData);

        // Call ajax.php to update the status
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: postData,
            success: function (response) {
                try {
                    console.log("Bank confirmation response", response);
                    
                    // Update UI to show completion
                    $("#bank_confirmation_section").removeClass('d-none').addClass('d-block');
                    $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    $("#verify_bank_btn_visible").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                    
                    // Make fields readonly after confirmation
                    $("#ifsc_code, #bank_account_no, #bank_name").prop('readonly', true).addClass('custom_readonly');
                    $("#account_type").prop('disabled', true);
                    $("#bank_account_no").closest('.input-group').find('.status-icon').html('<i class="fa fa-check text-success"></i>');
                    
                    // Mark verification as completed and locked
                    verificationSteps.bank.completed = true;
                    verificationSteps.bank.locked = true;
                   // verificationSteps.bank.data = window.bankVerificationData.result;
                    
                    // Lock the Bank step
                    lockVerificationStep('bank');
                    
                    showNotification("Bank Account Verification completed and locked successfully", "success");
                    
                    // Clear stored data
                  //  window.bankVerificationData = null;
                    
                    // Optionally reload the page to refresh verification status
                    // location.reload();
                    
                } catch (err) {
                    console.error('Bank confirmation parse error:', err);
                    showNotification("Error processing bank confirmation", "error");
                }
            },
            error: function (xhr, status, error) {
                console.error('Bank confirmation error:', error);
                console.error('Response text:', xhr.responseText);
                showNotification("Error confirming bank details", "error");
                
                // Reset button state
                $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank');
                $("#bank_manual_confirmation_section").removeClass('d-none').addClass('d-block');
            }
        });
    }

    function cancelBankConfirmation() {
        // Hide manual confirmation section
        $("#bank_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Make fields editable temporarily (keep verification fields visible)
        $("#ifsc_code, #bank_account_no, #bank_name").prop('readonly', false).removeClass('custom_readonly');
        $("#account_type").prop('disabled', false);
        $("#verify_bank_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
        $("#verify_bank_btn_visible").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify Bank Account').prop('disabled', false);
        $("#bank_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
        
        // Reset status icon
        $("#bank_account_no").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Keep verification fields visible but clear them
        $("#bank_verified_ifsc").val("");
        $("#bank_verified_account").val("");
        $("#bank_verified_name").val("");
        $("#bank_verified_status").val("");
        
        // Don't hide verification fields - keep them visible
        
        // Clear stored data
        window.bankVerificationData = null;
        
        // Reset verification step
        verificationSteps.bank.completed = false;
        verificationSteps.bank.locked = false;
        verificationSteps.bank.data = null;
        verificationSteps.bank.nameMatch = null;
        
        showNotification("Bank verification cancelled. You can modify and verify again.", "info");
    }

    function confirmMSMEDetails() {
        if (!window.msmeVerificationData) {
            showNotification("No MSME verification data found", "error");
            return;
        }

        // Get company names
        var userCompanyName = $("#company_name").val() || "";
        var verifiedCompanyName = $("#confirm_msme_enterprise_name_display").text() || "";

        // Show loading state
        $("#msme_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        $("#verify_msme_btn").html('<i class="fa fa-spinner fa-spin"></i> Updating Status...');

        // Call ajax.php to update the status with verified company name
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'insertvendorsandvendorregisters',
                company_name: verifiedCompanyName, // Save the verified company name
                user_entered_company_name: userCompanyName,
                msme_number: window.msmeVerificationData.msme_number,
                transaction_table: window.msmeVerificationData.transaction_table,
                vendor_id: window.msmeVerificationData.vendor_id,
                verification_type: window.msmeVerificationData.verification_type,
                api_hit_id: window.msmeVerificationData.api_hit_id,
            },
            success: function (response) {
                try {
                    console.log("MSME confirmation response", response);
                    
                    // Update UI to show completion
                    $("#msme_confirmation_section").removeClass('d-none').addClass('d-block');
                    $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Verified & Locked');
                    $("#msme_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-success');
                    
                    // Mark verification as completed and locked
                    verificationSteps.msme.completed = true;
                    verificationSteps.msme.locked = true;
                    verificationSteps.msme.data = window.msmeVerificationData.result;
                    
                    // Lock the MSME step
                    lockVerificationStep('msme');
                    
                    showNotification("MSME Verification completed with confirmed company name", "success");
                    
                    // Clear stored data
                    window.msmeVerificationData = null;
                    
                } catch (err) {
                    console.error('MSME confirmation parse error:', err);
                    showNotification("Error processing MSME confirmation", "error");
                }
            },
            error: function (xhr, status, error) {
                console.error('MSME confirmation error:', error);
                showNotification("Error confirming MSME details", "error");
                
                // Reset button state
                $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME');
                $("#msme_manual_confirmation_section").removeClass('d-none').addClass('d-block');
            }
        });
    }

    function cancelMSMEConfirmation() {
        // Hide manual confirmation section
        $("#msme_manual_confirmation_section").removeClass('d-block').addClass('d-none');
        
        // Make fields editable temporarily (keep verification fields visible)
        $("#msme_number").prop('readonly', false).removeClass('custom_readonly');
        $("#verify_msme_btn").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
        $("#verify_msme_btn_visible").removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify MSME').prop('disabled', false);
        $("#msme_edit_btn").text('Change Info').removeClass('btn-secondary').addClass('btn-secondary');
        
        // Keep verification fields visible but clear them
        $("#msme_verified_enterprise_name").val("");
        $("#msme_verified_major_activity").val("");
        $("#msme_verified_organization_type").val("");
        $("#msme_verified_social_category").val("");
        $("#msme_verified_commencement_date").val("");
        $("#msme_verified_dic_name").val("");
        $("#msme_verified_registration_date").val("");
        $("#msme_verified_status").val("");
        
        // Don't hide verification fields - keep them visible
        
        // Reset status icon
        $("#msme_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-clock-o text-muted"></i>');
        
        // Clear stored data
        window.msmeVerificationData = null;
        
        // Reset verification step
        verificationSteps.msme.completed = false;
        verificationSteps.msme.locked = false;
        verificationSteps.msme.data = null;
        verificationSteps.msme.nameMatch = null;
        
        showNotification("MSME verification cancelled. You can modify and verify again.", "info");
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

        $("#tan_verification_fields").removeClass('d-none').addClass('d-block');
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

                        $("#tan_confirmation_section").removeClass('d-none').addClass('d-block');
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

                            // Store verification data for confirmation dialog
                            verificationSteps.tan.data = result;
                            
                            // Populate confirmation dialog data
                            $("#confirm_tan_company_name_display").text(result.result.user_full_name || "N/A");
                            $("#confirm_tan_number_display").text(tan_no);
                            $("#confirm_user_company_name_tan_display").text($("#company_name").val());
                            
                            // Show manual confirmation section
                            $("#tan_manual_confirmation_section").removeClass('d-none').addClass('d-block');
                            $("#tan_confirmation_section").removeClass('d-block').addClass('d-none');

                            showNotification("TAN data fetched successfully! Please verify the details to proceed.", "info");
                            $("#tan_number").closest('.input-group').find('.status-icon').html('<i class="fa fa-exclamation-triangle text-warning"></i>');

                            $("#verify_tan_btn").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');
                            $("#verify_tan_btn_visible").removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-exclamation-triangle"></i> Confirmation Required');
                        }

                    } else {
                        $("#tan_verified_company").val("Verification Failed");
                        $("#tan_verified_status").val("Verification Failed");
                        
                        // Show detailed error information
                        var errorMessage = "TAN Verification Failed";
                        // if (result.api_hit_id) {
                        //     errorMessage += " - api_hit_id: " + result.api_hit_id;
                        // }
                        // if (result.response_code) {
                        //     errorMessage += " - response_code: " + result.response_code;
                        // }
                        if (result.response_message) {
                            errorMessage += result.response_message;
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
        
        // Note: Locked message is already shown in PHP template, no need to add duplicate
    }

    function getStepNumber(stepType) {
        var stepMapping = {
            'pan': 1,
            'gstin': 2,
            'bank': 3,
            'msme': 4,
            'tan': 5
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
                
                // Ensure unverified steps are not locked
                ensureUnverifiedStepsUnlocked();
                
                // Initialize event handlers
                initializeEventHandlers();

                console.log("Verification system initialized");
                console.log("All Change Info buttons should now work properly - check console for debugging info");
            });
        }
        
        function ensureUnverifiedStepsUnlocked() {
            console.log("Ensuring unverified steps are not locked");
            
            visibleVerifications.forEach(function(verificationType) {
                var isVerified = vendorVerifications[verificationType] && vendorVerifications[verificationType].status === 'Y';
                var stepType = verificationType.toLowerCase();
                var stepNumber = getStepForSection(stepType);
                
                if (!isVerified) {
                    // Remove locked class from step container
                    $('#step-' + stepNumber).removeClass('verification-step-locked');
                    
                    // Remove locked class from step indicator
                    $('.step-indicator .step').eq(stepNumber - 1).removeClass('locked');
                    
                    // Ensure input fields are not readonly (except verification result fields)
                    var mainInputs = '';
                    switch(stepType) {
                        case 'pan':
                            mainInputs = '#pan_number, #company_name, #company_email, #company_type';
                            break;
                        case 'gstin':
                            mainInputs = '#gstin';
                            break;
                        case 'bank':
                            mainInputs = '#ifsc_code, #bank_account_no, #bank_name, #account_type';
                            break;
                        case 'msme':
                            mainInputs = '#msme_number';
                            break;
                        case 'tan':
                            mainInputs = '#tan_number';
                            break;
                    }
                    
                    if (mainInputs) {
                        $(mainInputs).prop('readonly', false).removeClass('custom_readonly');
                        // Special handling for select elements
                        $('#company_type, #account_type').prop('disabled', false).removeClass('custom_readonly');
                    }
                    
                    // Enable buttons
                    $('#' + stepType + '_edit_btn').prop('disabled', false);
                    $('#verify_' + stepType + '_btn').prop('disabled', false);
                    $('#verify_' + stepType + '_btn_visible').prop('disabled', false);
                    
                    // Reset button classes and text for unverified steps
                    if (stepType === 'tan') {
                        $('#tan_edit_btn').removeClass('btn-success').addClass('btn-warning').text('Change Info');
                        $('#verify_tan_btn').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN');
                        $('#verify_tan_btn_visible').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-search"></i> Verify TAN');
                    }
                    
                    // Special handling for skip buttons
                    $('.skip-step-btn[data-step-type="' + stepType + '"]').prop('disabled', false);
                    
                    console.log('Unlocked unverified step:', stepType);
                }
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
            
            // Company email blur handler - auto verify PAN
            $(document).on('blur', '.company-email-input', function() {
                // Only auto-verify if not already completed, not locked, and all required fields are filled
                if (!verificationSteps.pan.completed && 
                    !verificationSteps.pan.locked &&
                    !isStepLocked('pan') &&
                    $("#pan_number").val().trim() && 
                    $("#company_name").val().trim() && 
                    $("#company_type").val().trim() &&
                    $("#company_email").val().trim()) {
                    verifyPAN();
                }
            });
            
            // PAN input blur handler - auto verify PAN
            $(document).on('blur', '.pan-input', function() {
                // Only auto-verify if not already completed, not locked, and all required fields are filled
                if (!verificationSteps.pan.completed && 
                    !verificationSteps.pan.locked &&
                    !isStepLocked('pan') &&
                    $("#pan_number").val().trim() && 
                    $("#company_name").val().trim() && 
                    $("#company_type").val().trim() &&
                    $("#company_email").val().trim()) {
                    verifyPAN();
                }
            });
            
            // Company name input handler
            $(document).on('input', '.company-name-input', function() {
                checkCompanyNameOnInput();
            });
            
            // Company type change handler - auto verify PAN
            $(document).on('change', '#company_type', function() {
                // Only auto-verify if not already completed, not locked, and all required fields are filled
                if (!verificationSteps.pan.completed && 
                    !verificationSteps.pan.locked &&
                    !isStepLocked('pan') &&
                    $("#pan_number").val().trim() && 
                    $("#company_name").val().trim() && 
                    $("#company_type").val().trim() &&
                    $("#company_email").val().trim()) {
                    verifyPAN();
                }
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
            
            // Bank name input handler
            $(document).on('input', '.bank-name-input', function() {
                checkBankAutoVerification();
            });
            
            // Bank name input handler
            $(document).on('input', '.bank-name-input', function() {
                // No auto-verification, just input handling
            });
            
            // Account type change handler
            $(document).on('change', '.account-type-select', function() {
                // No auto-verification, just selection handling
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
                console.log('Verify button clicked for:', verificationType);
                
                if (verificationType === 'pan') {
                    verifyPAN();
                } else if (verificationType === 'gst') {
                    verifyGST();
                } else if (verificationType === 'bank') {
                    verifyBank();
                } else if (verificationType === 'msme') {
                    verifyMSME();
                } else if (verificationType === 'tan') {
                    verifyTAN();
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

            // GST confirmation handlers
            $(document).on('click', '.confirm-gst-btn', function() {
                confirmGSTDetails();
            });
            
            $(document).on('click', '.cancel-gst-btn', function() {
                cancelGSTConfirmation();
            });

            // TAN confirmation handlers
            $(document).on('click', '.confirm-tan-btn', function() {
                confirmTANDetails();
            });
            
            $(document).on('click', '.cancel-tan-btn', function() {
                cancelTANConfirmation();
            });

            // MSME confirmation handlers
            $(document).on('click', '.confirm-msme-btn', function() {
                confirmMSMEDetails();
            });
            
            $(document).on('click', '.cancel-msme-btn', function() {
                cancelMSMEConfirmation();
            });

            // PAN company confirmation handlers
            $(document).on('click', '.confirm-pan-company-btn', function() {
                confirmPANCompanyDetails();
            });
            
            $(document).on('click', '.cancel-pan-company-btn', function() {
                cancelPANCompanyConfirmation();
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

        // Initialize the verification system
        initVerificationSystem();
    })();