document.addEventListener("DOMContentLoaded", function () {

    var step1Btn = document.querySelector(".general_step_1");
    var instructions = document.querySelector(".general_instructions");
    var instructions2 = document.querySelector(".general_instructions_2");
    var instructions3 = document.querySelector(".general_instructions_3");
    var previousBtn = document.querySelector(".previous_open");

    var oldId = cfg.oldId || '0';

    // ---------- INITIAL LOAD ----------
    if (oldId && oldId !== '0') {
        instructions2.classList.remove("d-none");
        instructions3.classList.remove("d-none");
        instructions.classList.add("d-none");
    }

    // ---------- NEXT ----------
    if (step1Btn) {
        step1Btn.addEventListener("click", function () {
            instructions2.classList.remove("d-none");
            instructions3.classList.remove("d-none");
            instructions.classList.add("d-none");
        });
    }

    // ---------- PREVIOUS ----------
    if (previousBtn) {
        previousBtn.addEventListener("click", function () {
            instructions2.classList.add("d-none");
            instructions3.classList.add("d-none");
            instructions.classList.remove("d-none");
        });
    }

});



//part1
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'add_row') {
        addRows();
    }
});
const cfg = document.getElementById('csr_config')?.dataset || {};

// Subtotal
let subtotalCount = parseInt(cfg.subtotalCount || 0, 10);

// Function to add new rows (empty rows for user input)
function addRows(groupId = `group_${++subtotalCount}`) {
    const tableBody = document.querySelector('#financial_table tbody');
    let rows = '';

    // Add 3 empty rows
    for (let i = 1; i <= 3; i++) {
        rows += `
        <tr data-group="${groupId}">
            <td>${i}</td>
            <td><input type="text" class="form-control" id="details_${groupId}_${i}" name="groups[${subtotalCount}][details][${i}]"></td>
            <td><input type="number" class="form-control unit-cost text_align_right" id="unit_cost_${groupId}_${i}" name="groups[${subtotalCount}][unit_cost][${i}]" data-group="${groupId}"></td>
            <td><input type="number" class="form-control num-units text_align_right" id="num_units_${groupId}_${i}" name="groups[${subtotalCount}][num_units][${i}]" data-group="${groupId}"></td>
            <td><input type="text" class="form-control budget text_align_right" id="budget_${groupId}_${i}" name="groups[${subtotalCount}][budget][${i}]" readonly></td>
            <td><input type="number" class="form-control num-months text_align_right" id="num_months_${groupId}_${i}" name="groups[${subtotalCount}][num_months][${i}]" data-group="${groupId}"></td>
            <td><input type="text" class="form-control total-budget text_align_right" id="total_budget_${groupId}_${i}" name="groups[${subtotalCount}][total_budget][${i}]" readonly></td>
            <td><input type="text" class="form-control" id="remarks_${groupId}_${i}" name="groups[${subtotalCount}][remarks][${i}]"></td>
            <td class="text-center"></td>
        </tr>`;
    }

    // Add subtotal row
    rows += `
    <tr data-group="${groupId}" class="subtotal-row" bgcolor="#eeeeee">
        <td></td>
        <th class="text-center">Sub Total (${subtotalCount})</th>
        <td></td>
        <td></td>
        <td><input type="text" class="form-control budget-subtotal text_align_right" id="budget_sub_total_${subtotalCount}" name="groups[${subtotalCount}][subtotal][${subtotalCount}]" readonly></td>
        <td></td>
        <td><input type="text" class="form-control total-budget-subtotal text_align_right" id="total_budget_sub_total_${subtotalCount}" name="groups[${subtotalCount}][total_subtotal][${subtotalCount}]" readonly></td>
        <td></td>
        <td class="text-center"><i class="fas fa-trash-alt text-danger cursor-pointer remove-row data-delete-group" data-delete-group="${groupId}"></i></td>
    </tr>`;

    tableBody.insertAdjacentHTML('beforeend', rows);
}
document.addEventListener('input', function (e) {

    if (
        e.target.classList.contains('unit-cost') ||
        e.target.classList.contains('num-units') ||
        e.target.classList.contains('num-months')
    ) {
    const groupId = e.target.getAttribute('data-group');
        if (groupId) {
            calculateRowBudget(groupId);
        }
    }
});
// Function to calculate row budgets
function calculateRowBudget(groupId) {
	// console.log('test', groupId);
    const groupRows = document.querySelectorAll(`[data-group="${groupId}"]:not(.subtotal-row)`);
    let budgetSum = 0;
    let totalBudgetSum = 0;

groupRows.forEach(row => {
    const unitCost = parseFloat(row.querySelector('.unit-cost')?.value) || 0;
    const numUnits = parseFloat(row.querySelector('.num-units')?.value) || 0;
    const numMonths = parseFloat(row.querySelector('.num-months')?.value) || 0;

    const budget = unitCost * numUnits;
    const totalBudget = budget * numMonths;

    const budgetInput = row.querySelector('.budget');
    const totalBudgetInput = row.querySelector('.total-budget');

    if (budgetInput) {
        budgetInput.value = budget.toFixed(2);
    }

    if (totalBudgetInput) {
        totalBudgetInput.value = totalBudget.toFixed(2);
    }

    budgetSum += budget;
    totalBudgetSum += totalBudget;
});


    const subtotalRow = document.querySelector(`[data-group="${groupId}"].subtotal-row`);
    subtotalRow.querySelector('.budget-subtotal').value = budgetSum.toFixed(2);
    subtotalRow.querySelector('.total-budget-subtotal').value = totalBudgetSum.toFixed(2);

    calculateGrandTotal();
}

// Function to calculate grand totals
function calculateGrandTotal() {
    const subtotalRows = document.querySelectorAll('.subtotal-row');
    let budgetGrandTotal = 0;
    let totalBudgetGrandTotal = 0;

    subtotalRows.forEach(row => {
        budgetGrandTotal += parseFloat(row.querySelector('.budget-subtotal')?.value) || 0;
        totalBudgetGrandTotal += parseFloat(row.querySelector('.total-budget-subtotal')?.value) || 0;
    });

    document.getElementById('budget_grand_total').value = budgetGrandTotal.toFixed(2);
    document.getElementById('total_budget_grand_total').value = totalBudgetGrandTotal.toFixed(2);
}

document.addEventListener('click', function (e) {
    const deleteIcon = e.target.closest('.data-delete-group');

    if (!deleteIcon) return;

    const groupId = deleteIcon.getAttribute('data-delete-group');

    if (groupId) {
        deleteGroup(groupId);
    }
});

// Function to delete a group of rows
function deleteGroup(groupId) {
    const groupRows = document.querySelectorAll(`[data-group="${groupId}"]`);
    groupRows.forEach(row => row.remove());
    calculateGrandTotal();
}

//part2
document.addEventListener('DOMContentLoaded', function () {
    const addPaymentScheduleBtn = document.getElementById('add_payment_schedule_row');

    if (addPaymentScheduleBtn) {
        addPaymentScheduleBtn.addEventListener('click', function () {
            addPaymentScheduleRows();
        });
    }
});

let rowCount = document.querySelectorAll("#paymentScheduleTable tbody tr").length;

function addPaymentScheduleRows() {
    rowCount++;
    const tableBody = document.querySelector("#paymentScheduleTable tbody");
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" class="form-control" id="payment_schedule_description_${rowCount}" name="payment_schedule_description[${rowCount}]"></td>
            <td><input type="number"  class="form-control percent-input text_align_right" id="percent_${rowCount}" name="payment_schedule_percent[${rowCount}]"  data-row="${rowCount}"></td>
            <td><input type="text" class="form-control amount-input text_align_right" id="amount_${rowCount}" name="payment_schedule_amount[${rowCount}]" readonly></td>
            <td><textarea  class="form-control" id="deliverables_${rowCount}" name="payment_schedule_deliverables[${rowCount}]"></textarea></td>
            <td class="text-center">
            <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-payment-row'  data-delete-payment-row="1"></i>
            
            </td>
                `;

    tableBody.appendChild(newRow);
    updateRows();
}

document.addEventListener('click', function (e) {
    const deleteIcon = e.target.closest('.delete-payment-row');
    if (!deleteIcon) return;
    if (deleteIcon.hasAttribute('data-delete-payment-row')) {
        deletePaymentScheduleRow(deleteIcon);
    }
});

function deletePaymentScheduleRow(button) {
    const row = button.closest("tr");
    row.remove();
    updateRows();
    calculatePaymentScheduleGrandTotal()
}

function updateRows() {
    const rows = document.querySelectorAll("#paymentScheduleTable tbody tr");
    rowCount = rows.length;

    rows.forEach((row, index) => {
        const currentIndex = index + 1; // Start numbering from 1
        row.children[0].textContent = currentIndex; // Update Sr.No

        row.querySelectorAll("input").forEach(input => {
            const baseName = input.getAttribute("id").split("_")[0];
            input.id = `${baseName}_${currentIndex}`;
            input.name = `payment_schedule_${baseName}[${currentIndex}]`;
        });
    });
}
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('percent-input')) {
        const rowIndex = e.target.getAttribute('data-row');
        if (rowIndex) {
            calculateAmount(rowIndex);
        }
    }
});

function calculateAmount(rowIndex) {
    const percentInput = document.getElementById(`percent_${rowIndex}`);
    const amountInput = document.getElementById(`amount_${rowIndex}`);
    const totalBudget = parseFloat(document.getElementById("total_budget_grand_total").value) || 0;

    // Ensure percentage value is formatted to two decimal places
    let percentValue = parseFloat(percentInput.value) || 0;
    percentValue = Math.round(percentValue * 100) / 100;
    percentInput.value = percentValue;

    // Calculate the corresponding amount
    const calculatedAmount = (percentValue / 100) * totalBudget;
    amountInput.value = calculatedAmount.toFixed(2);

    // Recalculate the grand total
    calculatePaymentScheduleGrandTotal();
}

function calculatePaymentScheduleGrandTotal() {
    const amountInputs = document.querySelectorAll(".amount-input");
    let totalAmount = 0;

    // Calculate total sum of all amount inputs
    amountInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        totalAmount += value;
    });

    // Update Grand Total field
    const grandTotalInput = document.getElementById("payment_grand_total");
    grandTotalInput.value = totalAmount;

    // Check against total budget
    const totalBudget = parseFloat(document.getElementById("total_budget_grand_total").value) || 0;

    if (totalAmount === totalBudget) {
        grandTotalInput.classList.remove("amount-mismatch");
        grandTotalInput.classList.add("amount-match");
    } else {
        grandTotalInput.classList.remove("amount-match");
        grandTotalInput.classList.add("amount-mismatch");
    }

}

//part3
document.addEventListener("DOMContentLoaded", function() {

    const radioButtons = document.querySelectorAll('input[type="radio"][data-file-id]');
    radioButtons.forEach(radio => {
        if (radio.checked && radio.value === "1") {
            handleFileInput(radio); // Trigger if the radio is set to "Yes"
        }
    });

    document.addEventListener('change', function (e) {
        const target = e.target;

        if (target.matches('input[type="radio"][data-file-id]')) {
            handleFileInput(target);
        }

        if (target.matches('input[type="file"]')) {
            handleFileInput(target);
        }
    });

});
function handleFileInput(element) {
    const fileInputId = element.getAttribute('data-file-id');
    const fileInput = document.getElementById(fileInputId);

    if (element.type === 'radio') {
        if (element.value === "1") {
            fileInput.classList.remove('d-none');
            fileInput.classList.add('is-invalid');
        } else {
            fileInput.classList.add('d-none');
            fileInput.value = '';
            fileInput.classList.remove('is-invalid');
        }
    }

    if (element.type === 'file') {
        if (element.files.length === 0) {
            element.classList.add('is-invalid');
        } else {
            element.classList.remove('is-invalid');
            element.classList.add('is-valid');
        }
    }
}

//part4
document.addEventListener('click', function (e) {
    const deleteIcon = e.target.closest('.delete-income-row');
    if (!deleteIcon) return;

    removeIncomeRow(deleteIcon);
});

function removeIncomeRow(button) {
    const row = button.closest('tr');
    row.remove();
    updateRowNumbers();
    calculateTotals();
}

function updateRowNumbers() {
    // const rows = document.querySelectorAll('#income_table_body tr');
    // rows.forEach((row, index) => {
    //     const rowNum = index + 1;
    //     row.querySelector('td:first-child').textContent = rowNum;

    //     row.querySelectorAll('input').forEach(input => {
    //         const baseName = input.name.replace(/\d+/g, rowNum);
    //         input.name = baseName;
    //         input.id = baseName.match(/\w+/)[0] + "_" + rowNum;
    //     });
    // });
    // incomeRowCount = rows.length;
    const rows = document.querySelectorAll('#income_table tbody tr');
    rowCounter = 0; // Reset counter
    rows.forEach((row, index) => {
        rowCounter = index + 1;
        row.querySelector('td:first-child').textContent = rowCounter; // Update Sr. No.
        row.querySelectorAll('input').forEach(input => {
            // Extract base name and keys from the current name
            const nameParts = input.name.match(/source_income\[(\d+)\](\w+)\[(\d+)\]/);
            if (nameParts) {
                const groupIndex = nameParts[1]; // Group ID
                const fieldName = nameParts[2]; // Field Name
                input.id = `${fieldName}_${rowCounter}`;
                input.name = `source_income[${rowCounter}]${fieldName}[${rowCounter}]`;
            }
        });
    });
}
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('income-input')) {
        calculateTotals();
    }
});

function calculateTotals() {
    let totalFY1 = 0, totalFY2 = 0, totalFY3 = 0;

    document.querySelectorAll('input[name^="source_income"][name*="fy1"]').forEach(input => totalFY1 += parseFloat(input.value) || 0);
    console.log(totalFY1);
    document.querySelectorAll('input[name^="source_income"][name*="fy2"]').forEach(input => totalFY2 += parseFloat(input.value) || 0);
    document.querySelectorAll('input[name^="source_income"][name*="fy3"]').forEach(input => totalFY3 += parseFloat(input.value) || 0);

    document.getElementById('total_fy1').value = totalFY1.toFixed(2);
    document.getElementById('total_fy2').value = totalFY2.toFixed(2);
    document.getElementById('total_fy3').value = totalFY3.toFixed(2);
}

// Recalculate totals on page load
calculateTotals();

//part5
document.addEventListener('DOMContentLoaded', function () {
    const addIncomeBtn = document.getElementById('add_row_income');

    if (addIncomeBtn) {
        addIncomeBtn.addEventListener('click', function () {
            addIncomeRow();
        });
    }
});

let incomeRowCount = document.querySelectorAll('#income_table_body tr').length || 0;

function addIncomeRow() {
    incomeRowCount++;
    const tableBody = document.querySelector('#income_table_body');
    console.log(incomeRowCount);
    const newRow = `
        <tr>
            <td>${incomeRowCount}</td>
            <td><input type="text" class="form-control" id="source_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]source[${incomeRowCount}]"></td>
            <td><input type="number" min="0" class="form-control text_align_right income-input" id="fy1_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy1[${incomeRowCount}]" ></td>
            <td><input type="number" min="0" class="form-control text_align_right income-input" id="fy2_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy2[${incomeRowCount}]" ></td>
            <td><input type="number" min="0" class="form-control text_align_right income-input" id="fy3_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy3[${incomeRowCount}]" ></td>
            <td class="text-center">
                <i class="fas fa-trash-alt text-danger cursor-pointer delete-income-row" ></i>
            </td>
        </tr>`;
    tableBody.insertAdjacentHTML('beforeend', newRow);
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.add-row-btn');
    if (!btn) return;

    const tableId = btn.getAttribute('data-table-id');
    const section = btn.getAttribute('data-section');

    if (tableId && section) {
        addRow(tableId, section);
    }
});

// Row counters
const rowCounters = {
    income: parseInt(cfg.incomeCount || 0, 10),
    expenditure: parseInt(cfg.expenditureCount || 0, 10),
    assets: parseInt(cfg.assetsCount || 0, 10),
    liabilities: parseInt(cfg.liabilitiesCount || 0, 10)
};


function addRow(tableId, section) {
    rowCounters[section]++;
    const tableBody = document.querySelector(`#${tableId} tbody`);
    const newRow = `
            <tr>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy1_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy1"></td>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy2_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy2"></td>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy3_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy3"></td>
                <td><input type="text" class="form-control" id="${section}_var1_${rowCounters[section]}" name="${section}[${rowCounters[section]}]var1"></td>
                <td><input type="text" class="form-control" id="${section}_var2_${rowCounters[section]}" name="${section}[${rowCounters[section]}]var2"></td>
                <td class="text-center">
                    <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-table-row' data-table-id="${tableId}"
      				 data-section="${section}" ></i>
                </td>
            </tr>`;
            tableBody.insertAdjacentHTML('beforeend', newRow);
}
document.addEventListener('click', function (e) {
    const deleteIcon = e.target.closest('.delete-table-row');
    if (!deleteIcon) return;

    const tableId = deleteIcon.getAttribute('data-table-id');
    const section = deleteIcon.getAttribute('data-section');

    if (tableId && section) {
        deleteRow(deleteIcon, tableId, section);
    }
});


function deleteRow(button, tableId, section) {
    const row = button.closest('tr');
    row.remove();
    updateRowNames(tableId, section);
}

function updateRowNames(tableId, section) {
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rowCounters[section] = 0;

    rows.forEach((row, index) => {
        const newRowNumber = index + 1;
        rowCounters[section] = newRowNumber;

        row.querySelectorAll('input').forEach(input => {
            const nameMatch = input.name.match(/(\w+)\[(\d+)\](\w+)/);
            if (nameMatch) {
                const baseName = nameMatch[1];
                const fieldName = nameMatch[3];
                input.id = `${baseName}_${fieldName}_${newRowNumber}`;
                input.name = `${baseName}[${newRowNumber}]${fieldName}`;
            }
        });
    });
}

//part6
let rowCounter = parseInt(cfg.formERowCounter || 0, 10);
document.addEventListener('DOMContentLoaded', function () {
    const addFormERowBtn = document.getElementById('add_form_e_row');

    if (addFormERowBtn) {
        addFormERowBtn.addEventListener('click', function () {
            addFormERow();
        });
    }
});

 // Function to add a new row
function addFormERow() {
    rowCounter++;
    const tableBody = document.querySelector('#form_e_table tbody');
    const newRow = `
            <tr>
                <td>${rowCounter}</td>
                <td><input type="text" class="form-control" id="project_description_${rowCounter}" name="project_description[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="focus_area_${rowCounter}" name="focus_area[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="location_${rowCounter}" name="location[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="period_${rowCounter}" name="period[${rowCounter}]"></td>
                <td><input type="number" class="form-control text_align_right" id="value_${rowCounter}" name="value[${rowCounter}]"></td>
                <td><input type="number" class="form-control text_align_right" id="beneficiaries_${rowCounter}" name="beneficiaries[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="partner_name_${rowCounter}" name="partner_name[${rowCounter}]"></td>
                <td class="text-center">
                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-form-e-row' ></i>
                    
                </td>
            </tr>`;
    tableBody.insertAdjacentHTML('beforeend', newRow);
}
document.addEventListener('click', function (e) {
    const deleteIcon = e.target.closest('.delete-form-e-row');
    if (!deleteIcon) return;

    deleteFormERow(deleteIcon);
});

// Function to delete a row
function deleteFormERow(button) {
    const row = button.closest('tr');
    row.remove();
    updateFormERowNumbers();
}

// Function to update row numbers after a row is removed
function updateFormERowNumbers() {
    const rows = document.querySelectorAll('#form_e_table tbody tr');
    rowCounter = 0;
    rows.forEach((row, index) => {
        rowCounter = index + 1;
        row.querySelector('td:first-child').textContent = rowCounter;
        row.querySelectorAll('input').forEach(input => {
            const nameParts = input.name.split('_');
            input.id = `${nameParts[0]}_${rowCounter}`;
            input.name = `${nameParts[0]}[${rowCounter}]`;
        });
    });
}


//part8
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('csr-checkbox')) {
        updateCheckboxValue(e.target);
    }
});


function updateCheckboxValue(element) {
    element.value = element.checked ? 1 : 0;
}

//part9
document.addEventListener("DOMContentLoaded", function() {
    const formIds = ['form_a', 'form_b', 'form_c', 'form_d', 'form_e'];
    // Loop through each form ID and check if the "Yes" radio button is pre-selected
    formIds.forEach(function (formId) {
        const yesRadio = document.getElementById(`${formId}_yes`);
        if (yesRadio && yesRadio.checked) {
            validateFile(formId); // Dynamically call the function for each form
        }
    });
    document.addEventListener('change', function (e) {
        // Radio buttons
        if (e.target.classList.contains('csr-form-radio')) {
            const formId = e.target.name;
            validateFile(formId);
        }

        // File inputs
        if (e.target.classList.contains('csr-form-file')) {
            const formId = e.target.id.replace('_file', '');
            validateFile(formId);
        }
    });
});
function validateFile(formId) {

    const yesRadio = document.getElementById(`${formId}_yes`);
    const noRadio = document.getElementById(`${formId}_no`);
    const fileInput = document.getElementById(`${formId}_file`);
    const container = document.getElementById(formId);
    const oldFile = document.querySelector(`input[name="${formId}_file_old"]`);

    if (yesRadio.checked) {

// Show container + file input
        container.classList.remove('d-none');
        fileInput.classList.remove('d-none');

        if (!fileInput.value) {
            fileInput.classList.add("is-invalid");
            fileInput.classList.remove("is-valid");
        } else {
            fileInput.classList.remove("is-invalid");
            fileInput.classList.add("is-valid");
        }
    } else if (noRadio.checked) {
        // Hide everything
        container.classList.add('d-none');
        fileInput.classList.add('d-none');

        // Reset
        fileInput.classList.remove('is-invalid', 'is-valid');
        fileInput.value = '';
    }
}

//part10
document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Validation 0: Check if financial year is selected
        const financialYearField = document.getElementById('financial_year');
        if (!financialYearField || !financialYearField.value.trim()) {
            tostarnotify(
                    'top','right','fa fa-thumbs-down',
                    'form-label label label-danger',
                    'animated flipInX','animated flipOutX',
                    'Please select the Financial Year.',''
                );
            if (financialYearField) financialYearField.focus();
            return false;
        }

        // Validation 1: Check if subject textarea is filled
        const subjectField = document.getElementById('subject');
        if (!subjectField || !subjectField.value.trim()) {
            tostarnotify(
                    'top','right','fa fa-thumbs-down',
                    'form-label label label-danger',
                    'animated flipInX','animated flipOutX',
                    'Please fill in the CSR Name field.',''
                );
            if (subjectField) subjectField.focus();
            return false;
        }



        var loader = '<div class="text-center height-300" id="loader-show"><img src="assets/images/spiner/spiner3.gif"></div>';
        $('#dynamic_popupxxl').html(loader);
        $('#kt_modal_dynamic_popupxxl').modal("show");


        const formData = new FormData(this);
        formData.append('page', 'insertCSR');
        formData.append('table_name', 'temp_csr_proposal_data');
        const finalFormData = {
            groups_data: {},
            source_income_data: {},
            income_data: {},
            expenditure_data: {},
            assets_data: {},
            liabilities_data: {},
            project_data: {},
            tracking_data: {},
            tentative_payment_schedule: {}
        };
        // Process all form fields
        formData.forEach((value, key) => {
            // Handle dynamic fields
            if (key.startsWith('groups')) {
                const match = key.match(/groups\[(\d+)\]\[(\w+)\]\[(\d+)\]/);
                if (match) {
                    const [_, groupId, fieldName, itemIndex] = match;
                    if (!finalFormData.groups_data[groupId]) finalFormData.groups_data[groupId] = {};
                    if (!finalFormData.groups_data[groupId][fieldName]) finalFormData.groups_data[groupId][fieldName] = {};
                    finalFormData.groups_data[groupId][fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('payment_schedule')) {
                const match = key.match(/payment_schedule_(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemIndex] = match;
                    console.log('itemIndex:', itemIndex, "fieldName: ", fieldName)
                    if (!finalFormData.tentative_payment_schedule[fieldName]) finalFormData.tentative_payment_schedule[fieldName] = {};
                    finalFormData.tentative_payment_schedule[fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('source_income')) {
                const match = key.match(/source_income\[(\d+)\](\w+)\[(\d+)\]/);
                if (match) {
                    const [_, incomeId, fieldName, itemIndex] = match;
                    if (!finalFormData.source_income_data[incomeId]) finalFormData.source_income_data[incomeId] = {};
                    if (!finalFormData.source_income_data[incomeId][fieldName]) finalFormData.source_income_data[incomeId][fieldName] = {};
                    finalFormData.source_income_data[incomeId][fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('income')) {
                const match = key.match(/income\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.income_data[itemId]) finalFormData.income_data[itemId] = {};
                    finalFormData.income_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('expenditure')) {
                const match = key.match(/expenditure\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.expenditure_data[itemId]) finalFormData.expenditure_data[itemId] = {};
                    finalFormData.expenditure_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('assets')) {
                const match = key.match(/assets\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.assets_data[itemId]) finalFormData.assets_data[itemId] = {};
                    finalFormData.assets_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('liabilities')) {
                const match = key.match(/liabilities\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.liabilities_data[itemId]) finalFormData.liabilities_data[itemId] = {};
                    finalFormData.liabilities_data[itemId][fieldName] = value;
                }
            } else if (
                key.startsWith('project_description') ||
                key.startsWith('focus_area') ||
                key.startsWith('location') ||
                key.startsWith('period') ||
                key.startsWith('value') ||
                key.startsWith('beneficiaries') ||
                key.startsWith('partner_name')
                ) {
                const match = key.match(/(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemId] = match;
                    if (!finalFormData.project_data[itemId]) finalFormData.project_data[itemId] = {};
                    finalFormData.project_data[itemId][fieldName] = value;
                }
            } else if (
                key.startsWith('tracking_name') ||
                key.startsWith('description') ||
                key.startsWith('start_date') ||
                key.startsWith('end_date') ||
                key.startsWith('tracking_budget')
                ) {
                const match = key.match(/(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemId] = match;
                    if (!finalFormData.tracking_data[itemId]) finalFormData.tracking_data[itemId] = {};
                    finalFormData.tracking_data[itemId][fieldName] = value;
                }
            } else {
                // Handle static fields
                finalFormData[key] = value;
            }
        });

     // Append serialized JSON objects to FormData
        formData.append('groups_data', JSON.stringify(finalFormData.groups_data));
        formData.append('source_income_data', JSON.stringify(finalFormData.source_income_data));
        formData.append('income_data', JSON.stringify(finalFormData.income_data));
        formData.append('expenditure_data', JSON.stringify(finalFormData.expenditure_data));
        formData.append('assets_data', JSON.stringify(finalFormData.assets_data));
        formData.append('liabilities_data', JSON.stringify(finalFormData.liabilities_data));
        formData.append('project_data', JSON.stringify(finalFormData.project_data));
        formData.append('tracking_data', JSON.stringify(finalFormData.tracking_data));
        formData.append('tentative_payment_schedule', JSON.stringify(finalFormData.tentative_payment_schedule));

        console.log('Final Form Data:', JSON.stringify(formData, null, 2));

        const cfg1 = document.getElementById('csr_config1')?.dataset || {};

        // Normalize oldId
        const oldId1 = cfg1.oldId && cfg1.oldId !== '' ? cfg1.oldId : null;

        $.ajax({          
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false, // Important: Prevent jQuery from processing FormData
            contentType: false, // Important: Prevent jQuery from setting content type header
            success: function(response) {
                console.log(response);
                const itemData = JSON.parse(JSON.parse(response));
                console.log(itemData['status']);
                if (itemData['status'] == 'sucess') {
                    var lastId = itemData['LASTID']; 
                    var dataString = 'lastId=' + lastId;
                    if (oldId1 !== null) {
                        dataString += '&oldId=' + oldId1;
                    }

                    jQuery.ajax({
                        url: "views/custom_view_csr_proposal_details.php",
                        data:dataString,
                        type: "POST",
                        success:function(data){
                            $('#dynamic_popupxxl').html(data);
                            if (typeof window.initCsrProposalViewDetails == 'function') {
                                window.initCsrProposalViewDetails();
                            }
                           // $('#kt_modal_dynamic_popupxxl').modal("show");
                        },
                        error:function (){}
                    });
                }
            },
            error: function() {
                alert('An error occurred while saving item request items. Please try again.');
            }
    });
});
