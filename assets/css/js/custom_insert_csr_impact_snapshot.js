(function () {
    function initCsrImpactSnapshotForm() {
        var config = document.getElementById('csrImpactSnapshotConfig');
        var fySelect = document.getElementById('financial_year');
        var projectSelect = document.getElementById('project_id');
        var emptyText = document.querySelector('.js-project-empty');
        var projectsByYear = {};
        var selectedProject = '';

        if (!config || !fySelect || !projectSelect) {
            return;
        }

        try {
            projectsByYear = JSON.parse(config.dataset.projectsByYear || '{}');
        } catch (e) {
            projectsByYear = {};
        }

        selectedProject = config.dataset.selectedProject || '';
        initSelect2(fySelect);
        initSelect2(projectSelect);

        function handleFinancialYearChange() {
            selectedProject = '';
            renderProjects(this.value);
        }

        if (window.jQuery) {
            jQuery(fySelect).off('change.csrImpactSnapshot').on('change.csrImpactSnapshot', handleFinancialYearChange);
        } else {
            fySelect.addEventListener('change', handleFinancialYearChange);
        }

        renderProjects(fySelect.value);

        function renderProjects(financialYear) {
            var projects = projectsByYear[financialYear] || [];

            destroySelect2(projectSelect);
            projectSelect.innerHTML = '<option value="">Select Project</option>';
            projects.forEach(function (project) {
                var option = document.createElement('option');
                option.value = project.id;
                option.textContent = project.subject || '-';
                if (String(project.id) === String(selectedProject)) {
                    option.selected = true;
                }
                projectSelect.appendChild(option);
            });

            if (emptyText) {
                emptyText.classList.toggle('d-none', projects.length > 0);
            }

            initSelect2(projectSelect);

            if (window.jQuery && jQuery(projectSelect).data('select2')) {
                jQuery(projectSelect).trigger('change.select2');
            }
        }

        function initSelect2(selectElement) {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) {
                return;
            }

            if (!jQuery(selectElement).data('select2')) {
                jQuery(selectElement).select2({
                    width: '100%'
                });
            }
        }

        function destroySelect2(selectElement) {
            if (window.jQuery && jQuery.fn && jQuery.fn.select2 && jQuery(selectElement).data('select2')) {
                jQuery(selectElement).select2('destroy');
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCsrImpactSnapshotForm);
    } else {
        initCsrImpactSnapshotForm();
    }
}());
