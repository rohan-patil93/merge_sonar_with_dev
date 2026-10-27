function toast(msg) {
    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
        'animated flipInX', 'animated flipOutX', msg, '');
}

function fwdpShowErr(id, msg) {
    const $el = $('#' + id);
    if ($el.length == 0) {
        return;
    }
    $el.text(msg).removeClass('d-none');
}

function fwdpHideErr(id) {
    const $el = $('#' + id);
    if ($el.length == 0) {
        return;
    }
    $el.text('').addClass('d-none');
}

function fwdpFilterIds(val) {
    if (val == null || val == '') {
        return [];
    }
    if (!$.isArray(val)) {
        val = [val];
    }
    return val.filter(function (id) {
        return id != null && id != '' && id != '0';
    });
}

function fwdpAllManagerOptionIds() {
    const ids = [];
    $('#manager_id option').each(function () {
        const id = $(this).val();
        if (id != null && id != '' && id != '0') {
            ids.push(id);
        }
    });
    return ids;
}

function selectUser(element) {
    const selectedManagers = fwdpFilterIds($(element).val());
    fwdpHideErr('user_id_err');

    if (selectedManagers.length == 0) {
        $("#user_id").html('<option value="0">Select Any one</option>').trigger('change');
        return;
    }
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: {
            page: 'getEmployeeData',
            user_id: selectedManagers,
        },
        success: function (response) {
            response = JSON.parse(response);
            const data = response['qeury_reslut'];
            $("#user_id").html('<option value="0">Select Any one</option>');

            if (data && data.length > 0) {
                data.forEach(function (user) {
                    $("#user_id").append(
                        '<option value="' + user.id + '">' + user.display_name + '</option>'
                    );
                });
                fwdpHideErr('user_id_err');
            } else {
                fwdpShowErr('user_id_err', 'No users found for the selected managers');
                toast('No users found for the selected managers');
            }
            $("#user_id").trigger('change');
        },
        error: function (error) {
            console.error("Error fetching users:", error);
            fwdpShowErr('user_id_err', 'Failed to fetch users. Please try again.');
            toast('Failed to fetch users. Please try again.');
        }
    });
}

function handleFromDateChange() {
    var fromDateInput = document.getElementById("from_date");
    var toDateInput = document.getElementById("to_date");

    toDateInput.min = fromDateInput.value;
    if (toDateInput.value && fromDateInput.value && toDateInput.value < fromDateInput.value) {
        toDateInput.value = fromDateInput.value;
    }
}

function fwdpInsertUrl(userId, date, extra) {
    const option = window.fwdpInsertOption || 'COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW';
    let url = 'index.php?option=' + option + '&user_id=' + encodeURIComponent(userId) + '&date=' + encodeURIComponent(date);
    if (extra) {
        url += '&' + extra;
    }
    return url;
}

function loadFieldWorkData(adminType, userId) {
    fwdpHideErr('manager_id_err');
    fwdpHideErr('user_id_err');
    fwdpHideErr('from_date_err');
    fwdpHideErr('to_date_err');

    let manager_ids = fwdpFilterIds($('#manager_id').val());
    let user_ids = fwdpFilterIds($('#user_id').val());
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    let isValid = true;

    if (!from_date) {
        fwdpShowErr('from_date_err', 'Please select a from date');
        toast('Please select a from date');
        isValid = false;
    }

    if (!to_date) {
        fwdpShowErr('to_date_err', 'Please select a to date');
        toast('Please select a to date');
        isValid = false;
    }

    if (from_date && to_date && to_date < from_date) {
        fwdpShowErr('to_date_err', 'To Date must be on or after From Date');
        toast('To Date must be on or after From Date');
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    if (adminType == '3') {
        user_ids = fwdpFilterIds($('#user_id').val() || userId);
        manager_ids = [];
    } else if (adminType == '2') {
        manager_ids = fwdpFilterIds($('#manager_id').val() || userId);
        if (manager_ids.length == 0) {
            manager_ids = [userId];
        }
    } else if (adminType == '1') {
        if (manager_ids.length == 0 && user_ids.length == 0) {
            manager_ids = fwdpAllManagerOptionIds();
        }
    }

    const $btn = $('#getDataButton');
    $btn.prop('disabled', true).text('Searching...');

    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: {
            page: 'getFieldWorkDailyPlanData',
            manager_id: manager_ids,
            user_id: user_ids,
            from_date: from_date,
            to_date: to_date,
        },
        success: function (response) {
            let data;
            try {
                data = JSON.parse(response);
            } catch (e) {
                toast('An error occurred while processing the request.');
                return;
            }
            if (!$.isArray(data)) {
                data = [];
            }

            document.getElementById('workType1').classList.remove('d-none');

            const tableBody = document.querySelector('#fiedWorkTable tbody');
            tableBody.innerHTML = '';

            if (data.length == 0) {
                tableBody.innerHTML =
                    '<tr>' +
                    '<td colspan="8">' +
                    '<div class="fwdp-empty-state">' +
                    '<p><i class="fa fa-warning"></i> No Records</p>' +
                    '<div class="text-center px-4">' +
                    '<img class="mw-100 mh-300px work-img" alt="" src="assets/images/work.png">' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
            } else {
                let i = 1;
                const currentDate = new Date().toISOString().split('T')[0];

                data.forEach(function (item) {
                    const isDisabled = item.from_date <= currentDate ? 'disabled-link' : '';
                    const newRow =
                        '<tr>' +
                        '<td>' + i + '</td>' +
                        '<td>' + (item.display_name || item.id) + '</td>' +
                        '<td>' + (item.location || '') + '</td>' +
                        '<td>' + (item.lat || '') + '</td>' +
                        '<td>' + (item.lng || '') + '</td>' +
                        '<td>' + (item.from_date || '') + '</td>' +
                        '<td>' + (item.to_date || '') + '</td>' +
                        '<td>' +
                        '<a href="' + fwdpInsertUrl(item.id, item.from_date) + '" class="fwdp-action-btn ' + isDisabled + '" title="Edit Details"><i class="fa fa-edit"></i></a>' +
                        '<a href="' + fwdpInsertUrl(item.id, item.from_date, 'view=1') + '" class="fwdp-action-btn" title="View Details"><i class="fa fa-eye"></i></a>' +
                        '<a href="' + fwdpInsertUrl(item.id, item.from_date, 'copy=1') + '" class="fwdp-action-btn" title="Copy Details"><i class="fa fa-clone"></i></a>' +
                        '</td>' +
                        '</tr>';
                    tableBody.innerHTML += newRow;
                    i++;
                });
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            toast('An error occurred while processing the request.');
        },
        complete: function () {
            $btn.prop('disabled', false).text('Search');
        }
    });
}

$(document).ready(function () {
    $('#manager_id').on('change', function () {
        selectUser(this);
    });

    $('#from_date').on('change', function () {
        handleFromDateChange();
    });

    const pageEl = document.getElementById('fieldWorkDailyPlansPage');
    const adminType = pageEl.dataset.adminType;
    const userId = pageEl.dataset.userId;

    if (adminType == "2") {
        const managerInput = $('<input>', {
            type: 'hidden',
            id: 'manager_id',
            name: 'manager_id',
        });

        $(".fwdp-card").append(managerInput);
        $("#manager_id").val(userId).trigger("change");
        selectUser(managerInput);
    } else if (adminType == "3") {
        if ($('#user_id').length == 0) {
            const userInput = $('<input>', {
                type: 'hidden',
                id: 'user_id',
                name: 'user_id',
                value: userId,
            });
            $(".fwdp-card").append(userInput);
        } else {
            $('#user_id').val(userId);
        }
    }

    $('#getDataButton').on('click', function () {
        loadFieldWorkData(adminType, userId);
    });

    handleFromDateChange();
    loadFieldWorkData(adminType, userId);
});
