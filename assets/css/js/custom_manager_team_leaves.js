(function () {
    var tbody        = document.getElementById('manager-team-leaves-tbody');
    var emptyState   = document.getElementById('manager-team-leaves-empty');
    var statTotal    = document.getElementById('stat-total');
    var keywordInput = document.getElementById('filter-keyword');
    var fromInput    = document.getElementById('filter-from');
    var toInput      = document.getElementById('filter-to');

    function getActionLabel(status) {
        if (String(status) === '199') {
            return 'approve';
        }
        if (String(status) === '40') {
            return 'cancle';
        }
        return 'reject';
    }

    function applyFilters() {
        if (!tbody) {
            return;
        }

        var keyword  = (keywordInput ? keywordInput.value : '').toLowerCase().trim();
        var fromDate = fromInput ? fromInput.value : '';
        var toDate   = toInput   ? toInput.value   : '';
        var visible  = 0;

        var rows = tbody.querySelectorAll('tr');
        rows.forEach(function (row) {
            var searchText = row.getAttribute('data-search') || '';
            var leaveFrom  = row.getAttribute('data-from')   || '';
            var leaveTo    = row.getAttribute('data-to')     || '';
            var show       = true;

            if (keyword && searchText.indexOf(keyword) === -1) {
                show = false;
            }

            if (show && fromDate && leaveTo && leaveTo < fromDate) {
                show = false;
            }

            if (show && toDate && leaveFrom && leaveFrom > toDate) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) {
                visible++;
            }
        });

        if (emptyState) {
            if (visible == 0) {
                emptyState.classList.remove('d-none');
                emptyState.textContent = 'No leave records found for your current filters.';
            } else {
                emptyState.classList.add('d-none');
            }
        }

        if (statTotal) {
            statTotal.textContent = String(visible);
        }
    }

    document.addEventListener('input', function (event) {
        var id = event.target.id;
        if (id == 'filter-keyword' || id == 'filter-from' || id == 'filter-to') {
            applyFilters();
        }
    });

    document.addEventListener('click', function (event) {
        var clearBtn = event.target.closest('[data-action="clear-leave-filters"]');
        if (clearBtn) {
            if (keywordInput) { keywordInput.value = ''; }
            if (fromInput)    { fromInput.value    = ''; }
            if (toInput)      { toInput.value      = ''; }

            applyFilters();
            return;
        }

        var actionBtn = event.target.closest('[data-action="team-leave-status"]');
        if (!actionBtn) {
            return;
        }

        var empId = actionBtn.getAttribute('data-empid') || '';
        var leaveId = actionBtn.getAttribute('data-leaveid') || '';
        var status = actionBtn.getAttribute('data-status') || '';
        var actionLabel = getActionLabel(status);

        if (!empId || !leaveId || !status) {
            Swal.fire('Error', 'Invalid leave action data.', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure want to ' + actionLabel + ' this leave request?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            jQuery.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'manager_team_leave_status',
                    emp_id: empId,
                    leave_id: leaveId,
                    approval_status: status
                },
                success: function (response) {
                    var res = JSON.parse(response);
                    var icon = res.status == 'success' ? 'success' : 'error';
                    var title = res.status == 'success' ? 'Success' : 'Error';

                    Swal.fire(title, res.message || 'Unable to process request.', icon);

                    if (res.status == 'success') {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Unable to process request. Please try again.', 'error');
                }
            });
        });
    });
})();
