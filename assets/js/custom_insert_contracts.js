
let milestoneCount = 1;
let isEditMode = false;
let isRenewMode = false;
let contractId = 0;
let companyId = 0;
let userId = 0;
let usersData = [];
let workflowStepsData = [];

function initializeData() {
    try {
        const milestoneCountElement = document.getElementById('milestone-count');
        const isEditModeElement = document.getElementById('is-edit-mode');
        const isRenewModeElement = document.getElementById('is-renew-mode');
        const contractIdElement = document.getElementById('contract-id');
        const companyIdElement = document.getElementById('company-id');
        const userIdElement = document.getElementById('user-id');
        const usersDataElement = document.getElementById('users-data');
        const workflowStepsElement = document.getElementById('workflow-steps-data');

        if (milestoneCountElement) {
            milestoneCount = parseInt(milestoneCountElement.textContent) || 1;
        }
        if (isEditModeElement) {
            isEditMode = isEditModeElement.textContent === 'true';
        }
        if (isRenewModeElement) {
            isRenewMode = isRenewModeElement.textContent === 'true';
        }
        if (contractIdElement) {
            contractId = parseInt(contractIdElement.textContent) || 0;
        }
        if (companyIdElement) {
            companyId = parseInt(companyIdElement.textContent) || 0;
        }
        if (userIdElement) {
            userId = parseInt(userIdElement.textContent) || 0;
        }
        if (usersDataElement) {
            usersData = JSON.parse(usersDataElement.textContent) || [];
        }
        if (workflowStepsElement) {
            workflowStepsData = JSON.parse(workflowStepsElement.textContent) || [];
        }
    } catch (error) {
        console.error('Error initializing data:', error);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    initializeData();

    const container = document.getElementById("milestone-container");
    const addBtn = document.getElementById("add-milestone");
    const errorDiv = document.getElementById("milestone-error");
    const contractInput = document.getElementById("contract_amount");

    // OEM Toggle Logic
    const oemYes = document.getElementById("oem_yes");
    const oemNo = document.getElementById("oem_no");
    const oemDetails = document.getElementById("oem_details");

    function toggleOemDetails() {
        if (oemYes && oemYes.checked) {
            oemDetails.classList.remove("d-none");
        } else if (oemDetails) {
            oemDetails.classList.add("d-none");
        }
    }

    if (oemYes) oemYes.addEventListener("change", toggleOemDetails);
    if (oemNo) oemNo.addEventListener("change", toggleOemDetails);

    // Run on load
    toggleOemDetails();

    function getTotalMilestoneAmount() {
        let total = 0;
        container.querySelectorAll(".milestone-price").forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });
        return total;
    }

    function areAllFieldsFilled() {
        const rows = container.querySelectorAll(".milestone-row");
        for (let row of rows) {
            const name = row.querySelector(".milestone-name").value.trim();
            const date = row.querySelector(".milestone-date").value.trim();
            const price = row.querySelector(".milestone-price").value.trim();
            if (name === "" || date === "" || price === "") {
                return false;
            }
        }
        return true;
    }

    function showError(msg) {
        errorDiv.textContent = msg;
        errorDiv.style.display = "block";
    }

    function hideError() {
        errorDiv.style.display = "none";
    }

    function validateMilestoneAmounts() {
        const contractAmount = parseFloat(contractInput.value) || 0;
        const totalMilestone = getTotalMilestoneAmount();

        if (totalMilestone > contractAmount) {
            showError(`⚠️ Total milestone amount (${totalMilestone.toFixed(2)}) exceeds contract amount (${contractAmount.toFixed(2)}).`);
            return false;
        }
        hideError();
        return true;
    }

    container.addEventListener("input", function (e) {
        if (e.target.classList.contains("milestone-price")) {
            const price = parseFloat(e.target.value) || 0;
            const contractAmount = parseFloat(contractInput.value) || 0;

            if (price > contractAmount) {
                showError(`⚠️ Single milestone price (${price.toFixed(2)}) cannot exceed contract amount (${contractAmount.toFixed(2)}).`);
                e.target.value = contractAmount;
            } else {
                validateMilestoneAmounts();
            }
        }
    });

    addBtn.addEventListener("click", function () {
        if (!areAllFieldsFilled()) {
            showError("Please fill all milestone fields before adding a new one.");
            return;
        }

        if (!validateMilestoneAmounts()) {
            return;
        }

        // Check if there's remaining amount to allocate
        const contractAmount = parseFloat(contractInput.value) || 0;
        const totalMilestone = getTotalMilestoneAmount();

        if (totalMilestone >= contractAmount) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Cannot add new milestone. The total milestone amount already equals or exceeds the contract amount.', '');
            console.log("Cannot add new milestone. The total milestone amount already equals or exceeds the contract amount.");
            return;
        }

        hideError();
        milestoneCount++;

        const row = document.createElement("div");
        row.className = "milestone-row mb-2 d-flex align-items-center gap-2";

        row.innerHTML = `
            <input type="text" name="milestone_name_${milestoneCount}" class="form-control milestone-name" placeholder="Milestone Name">
            <input type="date" name="milestone_date_${milestoneCount}" class="form-control datepicker milestone-date">
            <input type="number" step="0.01" name="milestone_price_${milestoneCount}" class="form-control milestone-price" placeholder="Price">
            <button type="button" class="btn btn-danger btn-sm remove-milestone">×</button>
        `;


        $(".datepicker").datepicker({
            showOn: "both",
            buttonText: "Calendar", // placeholder text
            dateFormat: date_format,
            setDate: new Date(),
            changeMonth: true,
            changeYear: true
        }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");


        container.appendChild(row);

        row.querySelector(".remove-milestone").addEventListener("click", function () {
            row.remove();
            validateMilestoneAmounts();
        });

        initDatePicker(row);
    });

    document.querySelectorAll(".remove-milestone").forEach(btn => {
        btn.addEventListener("click", function () {
            btn.closest(".milestone-row").remove();
            validateMilestoneAmounts();
        });
    });
});

function initDatePicker(context = document) {
    $(context).find(".datepicker, .rangedatepicker").datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        minDate: backdate_limit,
        changeMonth: true,
        changeYear: true
    }).next("button.ui-datepicker-trigger")
        .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
}

document.addEventListener("DOMContentLoaded", function () {
    initializeData();
    initDatePicker();
});

document.addEventListener('DOMContentLoaded', function () {
    initializeComponents();
    initializeFormValidation();
    initializeAjaxHandlers();
    initializeModalHandlers();
    initializeWorkflowHandlers();
    initializeRenewalDates();
    initializeEventHandlers();
});

function initializeComponents() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    const autoDismissAlert = document.getElementById('autoDismissAlert');
    if (autoDismissAlert) {
        setTimeout(function () {
            const alert = new bootstrap.Alert(autoDismissAlert);
            alert.close();
        }, 5000);
    }
}

function initializeFormValidation() {
    const contractForm = document.getElementById('contractForm');

    if (contractForm) {
        contractForm.addEventListener('submit', function (e) {
            if (!contractForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                showAlert('Please fill in all required fields.', 'danger');
            } else {
                contractForm.submit();
            }
            contractForm.classList.add('was-validated');
        });
    } else {
        console.error('Error: contractForm not found!');
    }
    if (!isEditMode) {
        const documentsForm = document.getElementById('documentsForm');
        if (documentsForm) {
            documentsForm.addEventListener('submit', function (e) {
                console.log('Copying contract data to documents form...');

                const contractFormElement = document.getElementById('contractForm');
                if (contractFormElement && !contractFormElement.checkValidity()) {
                    e.preventDefault();
                    showAlert('Please fill in all required contract fields in the Contract Details tab.', 'danger');
                    const contractTab = document.querySelector('a[href="#contractdetails"]');
                    if (contractTab) {
                        contractTab.click();
                    }
                    return false;
                }

                const fieldsToCopy = [
                    'contract_name', 'transaction_no', 'invoice_number', 'rt_number',
                    'business_code', 'parent_contract_id', 'contract_payment_terms',
                    'typeofaggrements', 'contract_owner_name', 'contract_owner_email',
                    'contract_owner_number', 'party_id', 'start_date', 'end_date',
                    'renewal_frequency', 'contract_amount', 'description', 'additional_notes',
                    'payment_term_id', 'termination_condition', 'contract_status',
                    'renew_type', 'state', 'isp', 'paid_by', 'internet_charges',
                    'hub_spoc_name', 'hub_spoc_number', 'wifi_provider', 'wifi_pass',
                    'description_id', 'department_id', 'type_of_contract_id'
                ];

                fieldsToCopy.forEach(fieldName => {
                    copyFormFieldToHidden(fieldName);
                });

                const partyTypeRadio = document.querySelector('input[name="party_type"]:checked');
                const hiddenPartyType = document.getElementById('hidden_party_type');
                if (partyTypeRadio && hiddenPartyType) {
                    hiddenPartyType.value = partyTypeRadio.value;
                    console.log('Party type:', partyTypeRadio.value);
                }

                const autoRenewCheckbox = document.querySelector('input[name="auto_renew"]');
                const hiddenAutoRenew = document.getElementById('hidden_auto_renew');
                if (hiddenAutoRenew) {
                    hiddenAutoRenew.value = (autoRenewCheckbox && autoRenewCheckbox.checked) ? '1' : '0';
                    console.log('Auto renew:', hiddenAutoRenew.value);
                }

                const locationsSelect = document.querySelector('select[name="locations[]"]');
                const hiddenLocations = document.getElementById('hidden_locations');
                if (locationsSelect && hiddenLocations) {
                    const selectedOptions = Array.from(locationsSelect.selectedOptions).map(opt => opt.value);
                    hiddenLocations.value = JSON.stringify(selectedOptions);
                    console.log('Locations:', selectedOptions);
                }

                const milestoneContainer = document.getElementById('hidden_milestones_container');
                if (milestoneContainer) {
                    milestoneContainer.innerHTML = '';
                    const allMilestoneInputs = document.querySelectorAll('input[name^="milestone_name_"]');

                    allMilestoneInputs.forEach((nameInput) => {
                        const match = nameInput.name.match(/milestone_name_(\d+)/);
                        if (match) {
                            const milestoneIndex = match[1];

                            const dateInput = document.querySelector(`input[name="milestone_date_${milestoneIndex}"]`);
                            const priceInput = document.querySelector(`input[name="milestone_price_${milestoneIndex}"]`);

                            if (nameInput.value || (dateInput && dateInput.value) || (priceInput && priceInput.value)) {
                                const hiddenName = document.createElement('input');
                                hiddenName.type = 'hidden';
                                hiddenName.name = `milestone_name_${milestoneIndex}`;
                                hiddenName.value = nameInput.value || '';
                                milestoneContainer.appendChild(hiddenName);

                                if (dateInput) {
                                    const hiddenDate = document.createElement('input');
                                    hiddenDate.type = 'hidden';
                                    hiddenDate.name = `milestone_date_${milestoneIndex}`;
                                    hiddenDate.value = dateInput.value || '';
                                    milestoneContainer.appendChild(hiddenDate);
                                }

                                if (priceInput) {
                                    const hiddenPrice = document.createElement('input');
                                    hiddenPrice.type = 'hidden';
                                    hiddenPrice.name = `milestone_price_${milestoneIndex}`;
                                    hiddenPrice.value = priceInput.value || '';
                                    milestoneContainer.appendChild(hiddenPrice);
                                }

                                console.log(`Milestone ${milestoneIndex}:`, nameInput.value, dateInput?.value, priceInput?.value);
                            }
                        }
                    });
                }

                console.log('All contract data copied successfully');
            });
        }
    }

    function copyFormFieldToHidden(fieldName) {
        const sourceField = document.querySelector(`[name="${fieldName}"]`);
        const targetField = document.getElementById(`hidden_${fieldName}`);
        if (sourceField && targetField) {
            targetField.value = sourceField.value || '';
            if (sourceField.value) {
                console.log(`Copied ${fieldName}:`, sourceField.value);
            }
        }
    }
    const documentCheckbox = document.getElementById('document');
    const documentPermissionsSection = document.getElementById('documentPermissionsSection');

    if (documentCheckbox && documentPermissionsSection) {
        documentCheckbox.addEventListener('change', function () {
            documentPermissionsSection.style.display = this.checked ? 'block' : 'none';
            const docCheckboxes = documentPermissionsSection.querySelectorAll('.doc-checkbox');
            if (this.checked) {
                docCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            } else {
                docCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        });
    }

    const documentsForm = document.getElementById('documentsForm');
    if (documentsForm) {
        documentsForm.addEventListener('submit', function (e) {
            const fileInputs = documentsForm.querySelectorAll('input[type="file"]');
            let hasFiles = false;

            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    hasFiles = true;
                }
            });
        });
    }

    const permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm) {
        permissionsForm.addEventListener('submit', function (e) {
            const userSelect = permissionsForm.querySelector('select[name="selected_user"]');
            const checkboxes = permissionsForm.querySelectorAll('input[name="permissions[]"]:checked');

            if (!userSelect.value) {
                e.preventDefault();
                showAlert('Please select a user.', 'warning');
                return;
            }

            if (checkboxes.length === 0) {
                e.preventDefault();
                showAlert('Please select at least one permission.', 'warning');
                return;
            }

            const docPermCheckbox = permissionsForm.querySelector('input[name="permissions[]"][value="document"]');
            if (!docPermCheckbox || !docPermCheckbox.checked) {
                const docCheckboxes = permissionsForm.querySelectorAll('.doc-checkbox');
                docCheckboxes.forEach(cb => { cb.checked = false; });
            }
        });
    }
}

function initializeAjaxHandlers() {
    console.log("=== initializeAjaxHandlers bound ===");

    const partyTypeInputs = document.querySelectorAll('input[name="party_type"]');
    partyTypeInputs.forEach(input => {
        input.addEventListener('change', function () {
            console.log("Radio button changed:", this.value);
            loadPartiesAjax();
        });
    });

    const selectedPartyType = document.querySelector('input[name="party_type"]:checked');
    if (selectedPartyType) {
        console.log("Auto loading for pre-selected type:", selectedPartyType.value);
        loadPartiesAjax();
    }
}

function initializeRenewalDates() {
    if (isRenewMode) {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        if (startDateInput && endDateInput) {
            const currentEndDate = endDateInput.value;

            if (currentEndDate) {
                const dateParts = currentEndDate.split('-');
                if (dateParts.length === 3) {
                    const currentEndDateObj = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                    const newStartDate = new Date(currentEndDateObj);
                    newStartDate.setDate(newStartDate.getDate() + 1);
                    const newEndDate = new Date(newStartDate);
                    newEndDate.setFullYear(newEndDate.getFullYear() + 1);

                    const formatDate = (date) => {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    };

                    startDateInput.value = formatDate(newStartDate);
                    endDateInput.value = formatDate(newEndDate);
                    startDateInput.setAttribute('readonly', 'readonly');
                    startDateInput.style.backgroundColor = '#f8f9fa';
                }
            }
        }
    }
}

function initializeEventHandlers() {
    const renewBtn = document.querySelector('.renew-contract-btn');
    if (renewBtn) {
        renewBtn.addEventListener('click', function () {
            console.log('test');
            updateUrlParameter('renew', 'Y');
            location.reload();
        });
    }

    const addPartyBtn = document.querySelector('.add-party-btn');
    if (addPartyBtn) {
        addPartyBtn.addEventListener('click', openAddPartyModal);
    }

    const addPartyForm = document.querySelector('.add-party-form');
    if (addPartyForm) {
        addPartyForm.addEventListener('submit', submitAddPartyForm);
    }

    const additionalDocInput = document.querySelector('.additional-document-file-input');
    if (additionalDocInput) {
        additionalDocInput.addEventListener('change', function () {
            handleAdditionalDocumentFileSelect(this);
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-additional-document-btn')) {
            const btn = e.target.closest('.remove-additional-document-btn');
            const fileName = btn.getAttribute('data-filename');
            removeAdditionalDocumentFile(fileName);
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('document-file-input')) {
            const documentId = e.target.getAttribute('data-document-id');
            handleDocumentFileSelect(e.target, documentId);
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-document-file-btn')) {
            const btn = e.target.closest('.remove-document-file-btn');
            const documentId = btn.getAttribute('data-document-id');
            const fileName = btn.getAttribute('data-filename');
            const recordId = btn.getAttribute('data-file-id');
            removeDocumentFile(documentId, fileName, recordId);
        }
    });

    const startWorkflowBtn = document.querySelector('.start-workflow-btn');
    if (startWorkflowBtn) {
        startWorkflowBtn.addEventListener('click', startWorkflow);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.add-workflow-step-btn')) {
            const btn = e.target.closest('.add-workflow-step-btn');
            const step = parseInt(btn.getAttribute('data-step'));
            addWorkflowStep(step);
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-workflow-step-btn')) {
            const btn = e.target.closest('.remove-workflow-step-btn');
            removeWorkflowStep(btn);
        }
    });

    const cancelEditBtn = document.querySelector('.cancel-permission-edit-btn');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', cancelPermissionEdit);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.edit-permission-btn')) {
            const btn = e.target.closest('.edit-permission-btn');
            const permissionData = JSON.parse(btn.getAttribute('data-permission'));
            editPermission(permissionData);
        }
    });
}

function updateUrlParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, '', url);
}

function initializeModalHandlers() {
    const addPartyModal = document.getElementById('addPartyModal');
    if (addPartyModal) {
        addPartyModal.addEventListener('show.bs.modal', function () {
            const partyType = document.querySelector('input[name="party_type"]:checked')?.value;
            if (partyType) {
                document.getElementById('partyTypeText').textContent = partyType;
                document.getElementById('partyTypeText2').textContent = partyType;
                document.getElementById('partyTypeText3').textContent = partyType.toLowerCase();
            }
        });
    }

    const rechargeModal = document.getElementById('rechargeModal');
    if (rechargeModal) {
        rechargeModal.addEventListener('show.bs.modal', function () {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('activationDate').value = today;
        });
    }

    const rechargeForm = document.getElementById('rechargeForm');
    if (rechargeForm) {
        rechargeForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const subscriptionType = formData.get('subscription_type');
            const activationDate = formData.get('activation_date');
            const contractId = formData.get('contract_id');

            if (!subscriptionType || !activationDate) {
                alert('Please select a subscription type and activation date.');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            try {

                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        page: 'insertrechargeplan',
                        contract_id: contractId,
                        subscription_type: subscriptionType,
                        activation_date: activationDate,
                        company_id: companyId,
                        user_id: userId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error('Invalid JSON response from server: ' + responseText.substring(0, 200));
                }

                if (data.result) {

                    const modal = bootstrap.Modal.getInstance(rechargeModal);
                    if (modal) modal.hide();
                    this.reset();

                    if (typeof loadRechargeHistoryAjax === 'function') {
                        loadRechargeHistoryAjax(contractId);
                    }
                } else {
                    console.warn("[WARN] Insert Failed:", data);
                    alert('Error: ' + (data.message || 'Failed to save recharge plan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }

        });
    }

    const rechargeHistoryModal = document.getElementById('rechargeHistoryModal');
    if (rechargeHistoryModal) {
        rechargeHistoryModal.addEventListener('show.bs.modal', function () {
            const contractId = document.getElementById('contractId')
                ? document.getElementById('contractId').value
                : null;
            loadRechargeHistoryAjax(contractId);
        });
    }
}

function initializeWorkflowHandlers() {
    const workflowForm = document.getElementById('workflowForm');
    if (workflowForm) {
        workflowForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const steps = document.querySelectorAll('.workflow-step, .step-block');
            const workflowSteps = steps.length;
            const stepsPermissions = {};

            let isValid = true;
            steps.forEach((step, index) => {
                const select = step.querySelector('select');
                if (!select.value) {
                    isValid = false;
                    select.classList.add('is-invalid');
                } else {
                    select.classList.remove('is-invalid');
                    stepsPermissions[index + 1] = select.value;
                }
            });

            if (!isValid) {
                showAlert('Please select permissions for all workflow steps.', 'warning');
                return;
            }

            const workflowStepsInput = document.createElement('input');
            workflowStepsInput.type = 'hidden';
            workflowStepsInput.name = 'workflow_steps';
            workflowStepsInput.value = workflowSteps;

            const stepsPermissionsInput = document.createElement('input');
            stepsPermissionsInput.type = 'hidden';
            stepsPermissionsInput.name = 'steps_permissions';
            stepsPermissionsInput.value = JSON.stringify(stepsPermissions);

            workflowForm.appendChild(workflowStepsInput);
            workflowForm.appendChild(stepsPermissionsInput);

            workflowForm.submit();
        });
    }
}

function openAddPartyModal() {

    const partyType = document.getElementById('customer')?.checked
        ? 'customer'
        : document.getElementById('vendor')?.checked
            ? 'vendor'
            : null;

    if (!partyType) {
        showAlert('Please select Party Type (Customer or Vendor) first.', 'warning');
        return;
    }

    const modalPartyType = document.getElementById('modalPartyType');
    const text1 = document.getElementById('partyTypeText');
    const text2 = document.getElementById('partyTypeText2');
    const text3 = document.getElementById('partyTypeText3');

    const formattedPartyType =
        partyType.charAt(0).toUpperCase() + partyType.slice(1);

    if (modalPartyType) {
        modalPartyType.value = partyType;
    }

    if (text1) text1.textContent = formattedPartyType;
    if (text2) text2.textContent = formattedPartyType;
    if (text3) text3.textContent = formattedPartyType;

    const modalEl = document.getElementById('addPartyModal');

    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        console.error('[ERROR] addPartyModal element not found');
    }
}

function submitAddPartyForm(e) {
    e.preventDefault();
    const form = document.getElementById('addPartyForm');
    const formData = new FormData(form);
    const partyName = formData.get('party_name');

    console.log("formData:", formData);


    if (!partyName || !partyName.trim()) {
        showAlert('Party name is required.', 'danger');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Adding...';

    const payload = new URLSearchParams({
        page: "add_party",
        party_type: formData.get("party_type"),
        party_name: formData.get("party_name"),
        email: formData.get("email"),
        phone: formData.get("phone"),
        company: formData.get("company"),
        address: formData.get("address"),
        bp_code: formData.get("bp_code")
    });

    console.log("[DEBUG] Sending payload:", Object.fromEntries(payload));

    fetch("ajax.php", {
        method: "POST",
        body: payload
    })
        .then(res => {
            console.log("[DEBUG] Raw Response Status:", res.status);
            return res.text();
        })
        .then(text => {
            console.log("[DEBUG] Raw Response Text:", text);

            let data;
            try {
                data = JSON.parse(text);
                console.log("[DEBUG] Parsed JSON:", data);
            } catch (err) {
                console.error("[ERROR] Failed to parse JSON:", err, "Raw text:", text);
                throw err;
            }

            if (data.success) {
                const partyType = formData.get('party_type') || 'Party';
                const partyName = formData.get('party_name') || '';
                const formattedType = partyType.charAt(0).toUpperCase() + partyType.slice(1);
                const message = `${formattedType} '${partyName}' has been added successfully. You can now select them from the list.`;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Party Added',
                        text: message,
                        confirmButtonText: 'Great!',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }

                $('#addPartyModal').modal('hide');
                const newId = data.result?.res?.LASTID;
                loadPartiesAjax(newId);
            }
            else {
                showAlert(data.message || 'Error adding party.', 'danger');
            }
        })
        .catch(error => {
            console.error("[ERROR] Fetch/Processing Error:", error);
            showAlert('Error adding party. Please try again.', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
}

function loadPartiesAjax() {
    console.log("=== loadPartiesAjax triggered ===");

    // Get selected party type using IDs
    const partyType = document.getElementById('customer')?.checked
        ? 'customer'
        : document.getElementById('vendor')?.checked
            ? 'vendor'
            : null;

    console.log("Selected partyType:", partyType);

    if (!partyType) {
        console.warn("No party type selected. Exiting.");
        return;
    }

    const partySelect = document.getElementById('party_id');

    if (!partySelect) {
        console.error("party_id select element not found");
        return;
    }

    const preselectedId = partySelect.dataset.selected
        ? String(partySelect.dataset.selected)
        : '';

    partySelect.innerHTML = '<option value="">Loading...</option>';

    $.ajax({
        url: "ajax.php",
        type: "POST",
        dataType: "json",
        data: {
            page: "load_parties",
            party_type: partyType
        },

        beforeSend: function () {
            console.log("AJAX Request sending with data:", {
                page: "load_parties",
                party_type: partyType
            });
        },

        success: function (data, textStatus, xhr) {

            console.log("AJAX Success Response:", data);

            partySelect.innerHTML = '<option value="">Select a Party</option>';

            const parties = (data && data.result)
                ? data.result
                : data;

            if (Array.isArray(parties)) {

                console.log("Total parties received:", parties.length);

                parties.forEach((party, index) => {
                    console.log("party" + party);
                    const option = document.createElement('option');

                    option.value = party.id;

                    // Dropdown display
                    //const company =  party.company ||.display_name ||Party ${party.id}`;
                    const company = party.company || 'N/A';
                    const email = party.email || 'N/A';
                    const display_name = party.display_name || 'N/A';
                    const createdAt = party.Created_Date
                        ? new Date(party.Created_Date).toLocaleDateString()
                        : 'N/A';

                    option.textContent = `${company} |${display_name} | ${email}`;

                    // Store details in dataset
                    option.dataset.name = company;
                    option.dataset.email = email;
                    option.dataset.phone = party.phone || '—';
                    option.dataset.created = createdAt;
                    option.dataset.bp_code = party.bp_code || '—';
                    option.dataset.billing_address = party.billing_address || '—';
                    option.dataset.shipping_address = party.shipping_address || '—';

                    partySelect.appendChild(option);
                });

                // Preview handler
                $(partySelect)
                    .off('change.preview')
                    .on('change.preview', function () {

                        const selectedOpt =
                            this.options[this.selectedIndex];

                        const previewContainer =
                            document.getElementById('party-details-preview');

                        if (selectedOpt && selectedOpt.value) {

                            document.getElementById('preview-party-name').textContent =
                                selectedOpt.dataset.name;

                            document.getElementById('preview-party-email').textContent =
                                selectedOpt.dataset.email;

                            document.getElementById('preview-party-phone').textContent =
                                selectedOpt.dataset.phone;

                            document.getElementById('preview-bp-code').textContent =
                                selectedOpt.dataset.bp_code;

                            document.getElementById('preview-billing-address').textContent =
                                selectedOpt.dataset.billing_address;

                            document.getElementById('preview-shipping-address').textContent =
                                selectedOpt.dataset.shipping_address;

                            if (previewContainer) {
                                previewContainer.classList.remove('d-none');
                            }

                        } else {

                            if (previewContainer) {
                                previewContainer.classList.add('d-none');
                            }
                        }
                    });

                // Preselect value if exists
                if (preselectedId) {

                    const optExists = Array.from(partySelect.options)
                        .some(opt => opt.value === preselectedId);

                    if (optExists) {

                        partySelect.value = preselectedId;

                        $(partySelect).trigger('change');

                        delete partySelect.dataset.selected;

                        console.log("Preselected party set to:", preselectedId);
                    }
                }

            } else {

                console.warn("Expected an array, got:", parties);

                partySelect.innerHTML =
                    '<option value="">No parties found</option>';
            }
        },

        error: function (xhr, status, error) {

            console.error("AJAX error:", status, error);

            console.log("Full xhr object:", xhr);

            partySelect.innerHTML =
                '<option value="">Error loading parties</option>';
        }
    });
}


function startWorkflow() {
    const container = document.getElementById('workflow-container');
    container.innerHTML = `
            <div class="workflow-wrapper mt-3" id="workflow-steps">
                ${renderExistingSteps()}
            </div>
            <div class="submit-container mt-3">
                <button type="submit" class="btn btn-primary">Submit Workflow</button>
            </div>
        `;
    $(".js-example-basic-single").select2();
}

function renderExistingSteps() {
    const stepsFromDB = workflowStepsData;
    let html = '';

    // If DB has no steps → create first step
    if (!stepsFromDB || Object.keys(stepsFromDB).length === 0) {
        html += createWorkflowStep(1);
    } else {
        let stepNum = 1;
        for (const step in stepsFromDB) {
            html += createWorkflowStep(stepNum, stepsFromDB[step]);
            stepNum++;
        }
    }
    return html;
}


function createWorkflowStep(step, selected = '') {
    return `
    <div class="step-block" data-step="${step}">
        <div class="step-title">Step ${step} </div>
        <div class="step-content">
            <select name="step_permission_${step}" id="step_permission_${step}" 
                class="form-select js-example-basic-single" required>
                <option value="">Select User</option>
                ${getUserOptions(selected)}
            </select>
            <div class="button-group">
                <button type="button" class="btn btn-primary img-30 btn-sm p-1" 
                    onclick="addWorkflowStep(${step})">
                    <i class="fa fa-plus m-0"></i>
                </button>
                ${step > 1 ? `
                <button type="button" class="btn btn-danger img-30 btn-sm p-1" 
                    onclick="removeWorkflowStep(this)">
                    <i class="fa fa-trash m-0"></i>
                </button>` : ''}
            </div>
        </div>
    </div>`;
}



function getUserOptions(selected = '') {
    return usersData.map(user => {
        const userName = user.user_name || user.name || `User ${user.id}`;
        const isSelected = user.id == selected ? 'selected' : '';
        return `<option value="${user.id}" ${isSelected}>${userName}</option>`;
    }).join('');
}

function addWorkflowStep(currentStep) {
    const stepsContainer = document.getElementById('workflow-steps');
    const totalSteps = stepsContainer.children.length;
    const newStep = createWorkflowStep(totalSteps + 1);
    stepsContainer.insertAdjacentHTML('beforeend', newStep);
    $(".js-example-basic-single").select2();
}

function removeWorkflowStep(button) {
    const stepBlock = button.closest('.step-block, .workflow-step');
    stepBlock.remove();
}

// new data
function loadRechargeHistoryAjax(contractId) {
    const tbody = document.getElementById('rechargeHistoryBody');

    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading...</td></tr>';

    $.ajax({
        url: "ajax.php",
        type: "POST",
        dataType: "json",
        data: {
            page: "load_recharge_history",
            contract_id: contractId
        },
        success: function (data) {
            console.log("load_recharge_history", data);
            tbody.innerHTML = '';

            let records = [];
            if (data && data.result && Array.isArray(data.result)) {
                records = data.result;
            } else if (Array.isArray(data)) {
                records = data;
            }

            if (records.length > 0) {
                let sn = 1;
                let totalRemaining = 0;
                let prevExpiry = null;

                records.forEach(row => {
                    let recharge = row.recharge_date;
                    let activation = row.activation_date;
                    let expiry = row.expiry_date;

                    // --- Fix 1: Activation = prevExpiry + 1 day ---
                    if (prevExpiry) {
                        const [dd, mm, yyyy] = prevExpiry.split('-');
                        let prevDate = new Date(`${yyyy}-${mm}-${dd}`);
                        prevDate.setDate(prevDate.getDate() + 1); // next day
                        activation = `${String(prevDate.getDate()).padStart(2, '0')}-${String(prevDate.getMonth() + 1).padStart(2, '0')}-${prevDate.getFullYear()}`;

                        // --- Fix 2: Expiry = activation + plan_duration - 1 day ---
                        let expDate = new Date(prevDate);
                        expDate.setDate(expDate.getDate() + (row.plan_duration - 1));
                        expiry = `${String(expDate.getDate()).padStart(2, '0')}-${String(expDate.getMonth() + 1).padStart(2, '0')}-${expDate.getFullYear()}`;
                    }

                    prevExpiry = expiry; // update for next iteration

                    // --- Remaining Days (unchanged) ---
                    let remainingDays = 0;
                    if (row.expiry_date) {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        const [dd, mm, yyyy] = row.expiry_date.split('-');
                        const expiryDate = new Date(`${yyyy}-${mm}-${dd}`);
                        expiryDate.setHours(0, 0, 0, 0);

                        const diff = (expiryDate - today) / (1000 * 60 * 60 * 24);
                        remainingDays = Math.max(0, diff);
                    }

                    if (row.status === "Active") {
                        totalRemaining += remainingDays;
                    }

                    // --- Plan type mapping (unchanged) ---
                    const planMap = {
                        30: 'Monthly',
                        90: 'Quarterly',
                        180: 'Half Yearly',
                        365: 'Yearly'
                    };
                    const planType = planMap[row.plan_duration] || `Custom (${row.plan_duration || 'N/A'} days)`;

                    tbody.innerHTML += `
                            <tr>
                                <td>${sn++}</td>
                                <td>${recharge}</td>
                                <td>${activation}</td>
                                <td>${expiry}</td>
                                <td>${planType}</td>
                                <td>${remainingDays}</td>
                                <td>
                                    <span class="badge ${row.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                        ${row.status === 'Active' ? 'Active' : 'Expired'}
                                    </span>
                                </td>
                            </tr>`;
                });

                tbody.innerHTML += `
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Remaining Days (Active Plans Only):</td>
                            <td colspan="2" class="fw-bold">${totalRemaining}</td>
                        </tr>`;
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No recharge history found.</td></tr>';
            }

        },
        error: function (xhr, status, error) {
            console.error("Error loading history:", error);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading history. Check console for details.</td></tr>';
        }
    });
}


function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.col-lg-12');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const existingAlerts = alertContainer.querySelectorAll('.alert');
    existingAlerts.forEach(existingAlert => existingAlert.remove());

    alertContainer.appendChild(alert);

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function editPermission(permission) {
    console.log('Edit permission:', permission);

    const permissionsTab = document.querySelector('a[href="#permissions"]');
    if (permissionsTab) {
        permissionsTab.click();
    }

    const ridInput = document.getElementById('permission_rid');
    if (ridInput) {
        ridInput.value = permission.id || '';
    }

    /**
     const userSelect = document.querySelector('select[name="selected_user"]');
    console.log(userSelect);
    console.log(permission.user);
    if (userSelect && permission.user) {
        const currentOption = userSelect.querySelector(`option[value="${permission.user}"]`);
        if (currentOption) {
            currentOption.disabled = false;
            userSelect.value = permission.user;
        }
    } **/

    const userSelect = $('select[name="selected_user"]');

    console.log(userSelect);
    console.log(permission.user);

    if (userSelect.length && permission.user) {

        const currentOption = userSelect.find(
            `option[value="${permission.user}"]`
        );

        console.log(currentOption);

        if (currentOption.length) {

            currentOption.prop('disabled', false);

            // select value in select2
            userSelect.val(String(permission.user)).trigger('change');
        }
    }


    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    permissionCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    if (permission.basic_permissions) {
        let permissionsArray = [];
        if (typeof permission.basic_permissions === 'string') {
            try {
                permissionsArray = JSON.parse(permission.basic_permissions);
            } catch (e) {
                permissionsArray = permission.basic_permissions.split(',').map(p => p.trim());
            }
        } else if (Array.isArray(permission.basic_permissions)) {
            permissionsArray = permission.basic_permissions;
        }

        permissionsArray.forEach(perm => {
            const checkbox = document.querySelector(`input[name="permissions[]"][value="${perm}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        if (permissionsArray.includes('document')) {
            const docSection = document.getElementById('documentPermissionsSection');
            if (docSection) {
                docSection.style.display = 'block';
            }
        }
    }

    const docCheckboxes = document.querySelectorAll('input[name="document_permissions[]"]');

    docCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    if (permission.document_permissions) {
        let docPermissionsArray = [];

        if (typeof permission.document_permissions === 'string') {
            try {
                docPermissionsArray = JSON.parse(permission.document_permissions);
            } catch (e) {
                docPermissionsArray = permission.document_permissions.split(',').map(p => p.trim());
            }
        } else if (Array.isArray(permission.document_permissions)) {
            docPermissionsArray = permission.document_permissions;
        }

        docPermissionsArray.forEach(docId => {
            const checkbox = document.querySelector(`input[name="document_permissions[]"][value="${docId}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }

    const submitBtn = document.getElementById('permissionSubmitBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    if (submitBtn) {
        submitBtn.textContent = 'Update Permissions';
        submitBtn.className = 'btn btn-warning';
    }
    if (cancelBtn) {
        cancelBtn.style.display = 'inline-block';
    }

    const permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm) {
        permissionsForm.scrollIntoView({ behavior: 'smooth' });
    }

    $(".js-example-basic-single").select2();

    showAlert('Permission data loaded for editing. Make your changes and submit.', 'info');
}

function cancelPermissionEdit() {
    const permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm) {
        permissionsForm.reset();
    }

    const ridInput = document.getElementById('permission_rid');
    if (ridInput) {
        ridInput.value = '';
    }

    const submitBtn = document.getElementById('permissionSubmitBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    if (submitBtn) {
        submitBtn.textContent = 'Submit Permissions';
        submitBtn.className = 'btn btn-success';
    }
    if (cancelBtn) {
        cancelBtn.style.display = 'none';
    }

    const userSelect = document.querySelector('select[name="selected_user"]');
    if (userSelect) {
        userSelect.disabled = false;
        userSelect.value = '';
    }

    showAlert('Edit cancelled. Form reset for new permission entry.', 'info');
}

// Additional Document File Management Functions
function removeDocumentFile(fileName) {
    if (confirm('Are you sure you want to remove this file?')) {
        const contractId = contractId;

        // If we have a contract ID, delete from server
        if (contractId > 0) {
            fetch('delete_contract_file.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fileName: fileName,
                    contractId: contractId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from table
                        const row = document.querySelector(`tr[data-filename="${fileName}"]`);
                        if (row) {
                            row.remove();
                        }

                        // Update the hidden input with remaining files
                        updateExistingDocuments();

                        // Check if table is empty and hide it
                        checkEmptyTable();

                        showAlert('File deleted successfully', 'success');
                    } else {
                        showAlert('Error deleting file: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting file', 'danger');
                });
        } else {
            // Just remove from UI for new contracts
            const row = document.querySelector(`tr[data-filename="${fileName}"]`);
            if (row) {
                row.remove();
            }

            updateExistingDocuments();
            checkEmptyTable();
        }
    }
}

// Document List Management Functions (for documents tab)
function removeDocumentFile(documentId, fileName, recordId) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to remove this file?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Remove the button from UI
            const downloadBtn = document.querySelector(`a[data-file-id="${recordId}"]`);
            const deleteBtn = document.querySelector(`button[data-file-id="${recordId}"]`);

            if (downloadBtn) downloadBtn.remove();
            if (deleteBtn) deleteBtn.remove();

            // Update the hidden input with remaining documents
            updateDocumentsList(documentId);

            Swal.fire('Removed!', 'File has been removed from the list.', 'success');
        }
    });
}

function updateDocumentsList(documentId) {
    const buttonsContainer = document.getElementById(`file_buttons_${documentId}`);
    const hiddenInput = document.getElementById(`all_documents_${documentId}`);

    if (!buttonsContainer || !hiddenInput) return;

    const downloadButtons = buttonsContainer.querySelectorAll('a[data-file-id]');
    const documents = [];

    downloadButtons.forEach(btn => {
        const fileId = btn.getAttribute('data-file-id');
        const fileName = btn.getAttribute('data-filename');

        if (fileId && fileName) {
            documents.push({
                id: fileId,
                file_name: fileName,
                comments: fileName
            });
        }
    });

    hiddenInput.value = JSON.stringify(documents);
}

function handleDocumentFileSelect(input, documentId) {
    if (input.files.length > 0) {
        const buttonsContainer = document.getElementById(`file_buttons_${documentId}`);

        if (!buttonsContainer) return;

        // Just show visual feedback for selected files
        // Don't modify hidden input - let backend handle file processing
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const tempId = 'pending_' + Date.now() + '_' + i;

            // Create buttons for pending file (will be processed on submit)
            const pendingBtn = document.createElement('span');
            pendingBtn.className = 'btn btn-sm btn-outline-warning';
            pendingBtn.title = `Pending upload: ${file.name}`;
            pendingBtn.setAttribute('data-temp-id', tempId);
            pendingBtn.innerHTML = '<i class="fa fa-clock m-0"></i>';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.title = `Remove: ${file.name}`;
            removeBtn.setAttribute('data-temp-id', tempId);
            removeBtn.innerHTML = '<i class="fa fa-times m-0"></i>';
            removeBtn.onclick = () => removePendingFile(documentId, tempId, input);

            buttonsContainer.appendChild(pendingBtn);
            buttonsContainer.appendChild(removeBtn);
        }
    }
}

function removePendingFile(documentId, tempId, fileInput) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to remove this pending file?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Remove buttons from UI
            const pendingBtn = document.querySelector(`span[data-temp-id="${tempId}"]`);
            const removeBtn = document.querySelector(`button[data-temp-id="${tempId}"]`);

            if (pendingBtn) pendingBtn.remove();
            if (removeBtn) removeBtn.remove();

            // Clear the file input if no more pending files
            const remainingPending = document.querySelectorAll(`#file_buttons_${documentId} span[data-temp-id^="pending_"]`);
            if (remainingPending.length === 0) {
                fileInput.value = '';
            }

            Swal.fire('Removed!', 'Pending file has been removed.', 'success');
        }
    });
}

// Additional Document Management Functions
function handleAdditionalDocumentFileSelect(input) {
    if (input.files.length > 0) {
        const buttonsContainer = document.getElementById('additional_file_buttons');

        if (!buttonsContainer) return;

        // Just show visual feedback for selected files
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const tempId = 'pending_additional_' + Date.now() + '_' + i;

            // Create buttons for pending file
            const pendingBtn = document.createElement('span');
            pendingBtn.className = 'btn btn-sm btn-outline-warning';
            pendingBtn.title = `Pending upload: ${file.name}`;
            pendingBtn.setAttribute('data-temp-id', tempId);
            pendingBtn.innerHTML = '<i class="fa fa-clock m-0"></i>';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.title = `Remove: ${file.name}`;
            removeBtn.setAttribute('data-temp-id', tempId);
            removeBtn.innerHTML = '<i class="fa fa-times m-0"></i>';
            removeBtn.onclick = () => removePendingAdditionalFile(tempId, input);

            buttonsContainer.appendChild(pendingBtn);
            buttonsContainer.appendChild(removeBtn);
        }
    }
}

function removeAdditionalDocumentFile(fileName) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to remove this file?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Remove buttons from UI
            const downloadBtn = document.querySelector(`a[data-filename="${fileName}"]`);
            const deleteBtn = document.querySelector(`button[data-filename="${fileName}"]`);

            if (downloadBtn) downloadBtn.remove();
            if (deleteBtn) deleteBtn.remove();

            // Update hidden input - remove the file from the list
            const hiddenInput = document.getElementById('all_additional_documents');
            if (hiddenInput) {
                let currentFiles = hiddenInput.value.split(',').filter(file => file.trim() !== fileName.trim());
                hiddenInput.value = currentFiles.join(',');
            }

            Swal.fire('Removed!', 'File has been removed from the list.', 'success');
        }
    });
}

function removePendingAdditionalFile(tempId, fileInput) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to remove this pending file?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Remove buttons from UI
            const pendingBtn = document.querySelector(`span[data-temp-id="${tempId}"]`);
            const removeBtn = document.querySelector(`button[data-temp-id="${tempId}"]`);

            if (pendingBtn) pendingBtn.remove();
            if (removeBtn) removeBtn.remove();

            // Clear the file input if no more pending files
            const remainingPending = document.querySelectorAll('#additional_file_buttons span[data-temp-id^="pending_"]');
            if (remainingPending.length === 0) {
                fileInput.value = '';
            }

            Swal.fire('Removed!', 'Pending file has been removed.', 'success');
        }
    });
}

function checkEmptyTable() {
    const tbody = document.getElementById('documents_tbody');
    if (tbody && tbody.children.length === 0) {
        const table = document.getElementById('additional_documents_table');
        if (table) {
            table.innerHTML = '';
        }
    }
}

function showAlert(message, type) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

    // Insert at top of card-block
    const cardBlock = document.querySelector('.card-block');
    if (cardBlock) {
        cardBlock.insertBefore(alertDiv, cardBlock.firstChild);
    }

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Reusable File Management Functions
function createFileTable(containerId, files, documentId = null) {
    const container = document.getElementById(containerId);
    if (!container || !files || files.length === 0) return;

    const tableHTML = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70%;">File Name</th>
                            <th style="width: 30%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="${containerId}_tbody">
                        ${files.map(file => createFileRow(file, documentId)).join('')}
                    </tbody>
                </table>
            </div>
        `;

    container.innerHTML = tableHTML;
}

function createFileRow(file, documentId = null) {
    const recordId = file.id || '';
    const fileName = file.file_name || file.name;
    const downloadUrl = file.file_name ? `uploads/contracts/${file.file_name}` : '#';
    const deleteFunction = documentId
        ? `removeDocumentFromList(${documentId}, '${fileName}', ${recordId})`
        : `removeDocumentFile('${fileName}')`;

    return `
            <tr data-document-id="${recordId}" data-filename="${fileName}">
                <td>
                    <i class="fa fa-file m-0"></i>
                    ${fileName}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        ${file.file_name ? `
                            <a href="${downloadUrl}" download class="btn btn-sm btn-outline-primary" 
                               title="Download ${fileName}">
                                <i class="fa fa-download"></i>
                            </a>
                        ` : ''}
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="${deleteFunction}"
                                title="Remove ${fileName}">
                            <i class="fa fa-trash m-0"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
}

function validateFileUpload(files, allowedTypes = ['pdf', 'xls', 'xlsx'], maxSize = 10485760) {
    const errors = [];

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileExt = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExt)) {
            errors.push(`${file.name}: Invalid file type. Allowed: ${allowedTypes.join(', ')}`);
        }

        if (file.size > maxSize) {
            errors.push(`${file.name}: File too large. Max size: ${Math.round(maxSize / 1024 / 1024)}MB`);
        }
    }

    return errors;
}

function updateExistingDocuments() {
    const rows = document.querySelectorAll('#documents_tbody tr');
    const fileNames = [];

    rows.forEach(row => {
        const fileName = row.getAttribute('data-filename');
        if (fileName) {
            fileNames.push(fileName);
        }
    });

    const hiddenInput = document.getElementById('existing_additional_documents');
    if (hiddenInput) {
        hiddenInput.value = fileNames.join(',');
    }
}

function initializeDocumentTable() {
    // Initialize additional document input
    const additionalFileInput = document.querySelector('.additional-document-file-input');
    if (additionalFileInput) {
        additionalFileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                console.log('Additional files selected:', this.files.length);
            }
        });
    }

    // Initialize document list inputs
    const documentInputs = document.querySelectorAll('.document-file-input');
    documentInputs.forEach(input => {
        input.addEventListener('change', function () {
            if (this.files.length > 0) {
                const documentId = this.getAttribute('data-document-id');
                const documentName = this.getAttribute('data-document-name');
                console.log(`Files selected for ${documentName}:`, this.files.length);

                // Optional: Show preview of selected files
                showSelectedFiles(documentId, this.files);
            }
        });
    });
}

function showSelectedFiles(documentId, files) {
    // Optional: Create a preview of selected files before upload
    const container = document.getElementById(`documents_table_${documentId}`);
    if (!container) return;

    // Create or update preview section
    let previewDiv = document.getElementById(`preview_${documentId}`);
    if (!previewDiv) {
        previewDiv = document.createElement('div');
        previewDiv.id = `preview_${documentId}`;
        previewDiv.className = 'mt-2 p-2 bg-light border rounded';
        container.appendChild(previewDiv);
    }

    let previewHTML = '<small class="text-muted">Selected files to upload:</small><ul class="mb-0 mt-1">';
    for (let i = 0; i < files.length; i++) {
        previewHTML += `<li><i class="fa fa-file m-0"></i>${files[i].name}</li>`;
    }
    previewHTML += '</ul>';

    previewDiv.innerHTML = previewHTML;
}

document.addEventListener("DOMContentLoaded", () => {
    // Initialize document management
    initializeDocumentTable();

    /*const rules = {
        contract_name: {
            regex: /^[A-Za-z\s]+$/,
            message: "Contract name must only contain letters and spaces."
        },
    };*/
    function showNotification(input, message) {
        let alert = input.parentNode.querySelector(".validation-error");
        if (!alert) {
            alert = document.createElement("div");
            alert.className = "validation-error text-danger small mt-1";
            input.parentNode.appendChild(alert);
        }
        alert.textContent = message;
        input.classList.add("is-invalid");
    }

    function clearNotification(input) {
        let alert = input.parentNode.querySelector(".validation-error");
        if (alert) alert.remove();
        input.classList.remove("is-invalid");
    }

    Object.keys(rules).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input) return;

        input.addEventListener("input", () => {
            const { regex, message } = rules[field];
            if (!regex.test(input.value)) {
                showNotification(input, message);
            } else {
                clearNotification(input);
            }
        });
    });

    const form = document.getElementById("contractsForm") || document.querySelector("form");
    form?.addEventListener("submit", e => {
        let valid = true;
        Object.keys(rules).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                const { regex, message } = rules[field];
                if (!regex.test(input.value)) {
                    showNotification(input, message);
                    valid = false;
                }
            }
        });
        if (!valid) e.preventDefault();
    });
});

// OEM Radio Button Functionality
document.addEventListener('DOMContentLoaded', function () {
    const oemYes = document.getElementById('oem_yes');
    const oemNo = document.getElementById('oem_no');
    const oemDetails = document.getElementById('oem_details');
    const oemRegistrationInput = document.querySelector('input[name="oem_registration_number"]');
    const oemProductCategoryInput = document.querySelector('input[name="oem_product_category"]');

    function toggleOemFields() {
        if (oemYes && oemYes.checked) {
            oemDetails.style.display = 'block';
            // Make required fields mandatory when OEM is Yes
            if (oemRegistrationInput) {
                oemRegistrationInput.setAttribute('required', 'required');
            }
            if (oemProductCategoryInput) {
                oemProductCategoryInput.setAttribute('required', 'required');
            }
        } else {
            oemDetails.style.display = 'none';
            // Remove required attribute when OEM is No
            if (oemRegistrationInput) {
                oemRegistrationInput.removeAttribute('required');
                oemRegistrationInput.value = '';
            }
            if (oemProductCategoryInput) {
                oemProductCategoryInput.removeAttribute('required');
                oemProductCategoryInput.value = '';
            }
            // Clear distributor name as well
            const oemDistributorInput = document.querySelector('input[name="oem_distributor_name"]');
            if (oemDistributorInput) {
                oemDistributorInput.value = '';
            }
        }
    }

    // Add event listeners to radio buttons
    if (oemYes) {
        oemYes.addEventListener('change', toggleOemFields);
    }
    if (oemNo) {
        oemNo.addEventListener('change', toggleOemFields);
    }

    // Initialize on page load
    toggleOemFields();
});

function showEditOptions() {
    const modalEl = document.getElementById('editConfirmationModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function submitWithOption(option) {
    const editOptionInput = document.getElementById('edit_option');

    // Set the value as a string that the backend expects
    editOptionInput.value = option; // This will be 'Amendment', 'Addendum', or 'SaveUpdate'

    document.getElementById('contractForm').submit();
}

function showCancelModal() {
    const modalEl = document.getElementById('cancelContractModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

async function submitCancellation() {
    const reason = document.getElementById('cancel_reason').value;
    if (!reason || reason.trim() === "") {
        alert("Please enter a reason for cancellation.");
        return;
    }

    const rid = document.getElementById('contractId').value;
    const company_id = document.getElementById('company-id').textContent;
    const user_id = document.getElementById('user-id').textContent;
    const data_name = document.getElementById('data-name').textContent;

    const submitBtn = document.querySelector('#cancelContractModal .btn-danger');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;

    try {
        await fetch(`api.php?mode=cancelcontractreact&method=post&user_id=${user_id}&company_id=${company_id}&data_name=${data_name}&platform=react`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                Rid: rid,
                cancel_reason: reason
            })
        });

        // Move user to list page immediately as requested
        window.location.href = 'index.php?option=COM_LIST_CONTRACTS';
    } catch (error) {
        console.error("Error during cancellation:", error);
        // Still move to list page as the database might have been updated
        window.location.href = 'index.php?option=COM_LIST_CONTRACTS';
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
}
function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
    if (typeof checkMandatoryArray === "function") {
        var validate = checkMandatoryArray();
    } else {
        var validate = 0;
    }

    if (validate == 0) {
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#popup1").html(loader);
        $('#kt_modal_KTDatatable_remote').modal("show");
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data: 'table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
            type: "POST",
            success: function (data) {
                $('#popup1').html(data);
            },
            error: function () { }
        });
    }
}
function submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name) {
    if (typeof checkMandatoryArray === "function") {
        var validate = checkMandatoryArray();
    } else {
        var validate = 0;
    }
    var session_config_json = JSON.parse($("#session_config_json").val());
    if (validate == 0) {
        swal({
            title: session_config_json.name,
            text: "Please note the status of the document will change to " + new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
            function () {
                jQuery.ajax({
                    url: "ajax.php",
                    data: 'page=submitstatusdynamicworkflow&table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
                    type: "POST",
                    success: function (response) {
                        var res = JSON.parse(response);
                        window.location.href = window.location.href;
                        if (res.transaction_update.status == 'sucess') {
                            window.location.href = window.location.href;
                            swal({
                                title: session_config_json.name,
                                text: error_message_json['9059'],
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                window.location.href = window.location.href;
                            });
                        } else {
                            swal({
                                title: "Oops...",
                                text: res.transaction_update.message,
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: "Ok",
                                confirmButtonClass: "btn-danger",
                                closeOnCancel: false
                            });
                        }
                    },
                    error: function () { }
                });
            });
    }
}


var inccnt = 100;
function addMoreContactPerson() {
    inccnt = inccnt + 1;
    var idcnt = 'contact_persons_remove_' + inccnt;
    var innerHTML = ``;
    innerHTML = `<div class="row" id="` + idcnt + `">
                    <div class="form-group mb-3 col-lg-4" >
                        <input type="text" name="contact_persons[name][]"  value="" class="form-control" placeholder="Contact Person Name">
                    </div>
                    <div class="form-group mb-3 col-lg-4" >
                        <input type="email" name="contact_persons[email][]" value="" class="form-control" placeholder="Contact Person Email">
                    </div>
                    <div class="form-group mb-3 col-lg-3">
                        <input type="number" name="contact_persons[number][]" value="" class="form-control" placeholder="Contact Person Number">
                    </div>
                    <div class="form-group mb-3 col-lg-1">
                        <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click to Remove" onclick="removeContactPersonsRemove('`+ idcnt + `')"></i>
                    </div>
                </div>`;
    $("#MoreContactPerson").append(innerHTML);

}
function removeContactPersonsRemove(id) {
    $('#' + id).remove();

}