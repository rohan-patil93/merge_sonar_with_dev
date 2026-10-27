
document.addEventListener('DOMContentLoaded', function () {

    const cfg = document.getElementById('csr_config')?.dataset || {};

    let existingBaseYears = [];

    if (cfg.existingBaseYears) {
        try {
            existingBaseYears = JSON.parse(cfg.existingBaseYears);
        } catch (e) {
            console.error('Invalid base years data', e);
        }
    }


    var showBtn = document.getElementById('showFormBtn');
    var form = document.getElementById('budgetForm');
    var baseYearInput = document.getElementById('base_year');
    var financeInput = document.getElementById('finance_budget');
    var ridInput = document.getElementById('rid');
    var submitBtn = document.getElementById('budgetSubmitBtn');
    var errorMsg = document.getElementById('baseYearError');

    // SHOW FORM (ADD)
    if (showBtn) {
        showBtn.addEventListener('click', function () {
            form.classList.remove('d-none');
            baseYearInput.value = '';
            financeInput.value = '';
            ridInput.value = '';
            submitBtn.textContent = 'Submit';
            submitBtn.value = 'insert';
            errorMsg.style.display = 'none';
            submitBtn.disabled = false;
        });
    }

    // EDIT FORM
    document.querySelectorAll('.editBudget').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.classList.remove('d-none');
            baseYearInput.value = this.getAttribute('data-base_year');
            financeInput.value = this.getAttribute('data-finance_budget');
            ridInput.value = this.getAttribute('data-id');
            submitBtn.textContent = 'Update';
            submitBtn.value = 'update';
            errorMsg.style.display = 'none';
            submitBtn.disabled = false;
        });
    });

    // BASE YEAR VALIDATION
    if (baseYearInput) {
        baseYearInput.addEventListener('input', function () {
            validateBaseYear(this.value);
        });
    }

    function validateBaseYear(val) {
        var rid = ridInput.value;
        var exists = existingBaseYears.includes(parseInt(val, 10));

        if (rid !== '') {
            var current = document.querySelector(
                '.editBudget[data-id="' + rid + '"]'
            )?.getAttribute('data-base_year');

            if (current == val) {
                exists = false;
            }
        }

        if (exists) {
            errorMsg.style.display = '';
            submitBtn.disabled = true;
        } else {
            errorMsg.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    // IMAGE FALLBACK
    document.querySelectorAll('img').forEach(function (img) {
        img.addEventListener('error', function () {
            this.src = 'uploads/all/no-image.jpg';
        });
    });
        // ---------- STATUS COLOR APPLY ----------
    document.querySelectorAll('.status-label').forEach(function (label) {
        label.style.color = label.dataset.color;
        label.style.backgroundColor = label.dataset.bg || 'blue';
    });


});
