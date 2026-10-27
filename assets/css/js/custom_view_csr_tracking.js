document.addEventListener("DOMContentLoaded", function () {
    const input = document.querySelector('.profile-pic-input');
    if (input) {
        input.addEventListener('change', function () {
            updateProfilePic();
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-complete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            closingRequest(this.dataset.rid);
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            updateStatus(this.dataset.id, this.dataset.status, this.dataset.rid);
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-end-date').forEach(el => {
        const color = el.dataset.color;
        if (color) el.style.color = color;
    });
});
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-start-btn').forEach(btn => {
        btn.addEventListener('click', () =>
            updateStatus(btn.dataset.id, 'started', btn.dataset.rid)
        );
    });

    document.querySelectorAll('.csr-po-btn').forEach(btn => {
        btn.addEventListener('click', () =>
            openPODetails(btn.dataset.id, btn.dataset.rid)
        );
    });

    document.querySelectorAll('.csr-settlement-btn').forEach(btn => {
        btn.addEventListener('click', () =>
            settlementDetails(btn.dataset.id, btn.dataset.rid)
        );
    });
});
function settlementDetails(traking_id,project_id) {
    let title = 'Settlement Details';
    $.ajax({
        url: 'views/custom_csr_invoice_settlement_old1.php',  // 👈 This should be your PO details modal file
        type: 'POST',
        data: {
            title: title,
            traking_id: traking_id,
            project_id: project_id
        },
        success: function(response) {
            $('#dynamic_popupxxl').html(response);
            $('.js-example-basic-single').select2();
            $('#kt_modal_dynamic_popupxxl').modal("show");
        },
        error: function(xhr) {
            alert('Failed to load data');
        }
    });
}
function openPODetails(traking_id,project_id) {
    let title = 'PO Details';
    $.ajax({
        url: 'views/custom_csr_po_details_old1.php',  // 👈 This should be your PO details modal file
        type: 'POST',
        data: {
            title: title,
            traking_id: traking_id,
            project_id: project_id
        },
        success: function(response) {
            $('#dynamic_popupxxl').html(response);
            $('#kt_modal_dynamic_popupxxl').modal("show");
        },
        error: function(xhr) {
            alert('Failed to load data');
        }
    });
}

// global rid
const cfg = document.getElementById('csr_config')?.dataset || {};

const rid = cfg.rid || '';
const appName = cfg.appName || '';
const successMsg = cfg.successMsg || '';


/* =========================
   UPDATE STATUS
========================= */
function updateStatus(id, newStatus, project_id) {
    console.log(id, ' , ', newStatus);

    const formData = {
        id: id,
        fid: 'id',
        status: newStatus
    };

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            page: 'updatemasters',
            table_name: 'csr_project_tracking',
            sendData: JSON.stringify(formData)
        },
        success: function (response) {
            const data = JSON.parse(JSON.parse(response));

            if (data['status'] === 'sucess') {
                if (newStatus === 'hold') {
                    getBrandList();
                }

                $('#kt_modal_dynamic_popupxxl').on('hidden.bs.modal', function () {
                    getBrandList();
                });

            } else {
                alert('Failed to save item Type: ' + data.message);
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
        }
    });

    if (newStatus === 'completed') {
        settlementDetails(id, project_id);
    }

    if (newStatus === 'started') {
        openPODetails(id, project_id);
    }
}

/* =========================
   REFRESH CSR CONTENT
========================= */
function getBrandList() {
    var loader = '<div class="text-center" id="loader-show">' +
        '<img src="assets/images/spiner/spiner3.gif" style="width: 300px;">' +
        '</div>';

    $("#CSRContiner").html(loader);

    $.ajax({
        url: "views/custom_view_csr_tracking.php",
        type: "GET",
        data: "ajax_page=Y&Rid=" + rid,
        success: function (response) {
            $("#CSRContiner").html(response);
        },
        error: function () {}
    });
}

/* =========================
   CLOSE REQUEST
========================= */
function closingRequest(rid) {
    if (!rid) {
        console.error('Invalid rid');
        return;
    }

    const formData = {
        id: rid,
        fid: 'id',
        tracking_status: '1'
    };

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            page: 'updatemasters',
            table_name: 'csr_proposal_data',
            sendData: JSON.stringify(formData)
        },
        success: function (response) {
            const data = JSON.parse(JSON.parse(response));

            if (data['status'] === 'sucess') {
                window.location.href = "index.php";
            } else {
                alert('Failed to save item Type: ' + data.message);
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
        }
    });
}

/* =========================
   WORKFLOW POPUP
========================= */
function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
    $.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data: 'table_name=' + table_name +
              '&current_status=' + current_status +
              '&transaction_id=' + transaction_id +
              '&next_status=' + next_status,
        type: "POST",
        success: function (data) {
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error: function () {}
    });
}

/* =========================
   WORKFLOW SUBMIT
========================= */
function submitDynamicWorkflow(
    table_name,
    transaction_id,
    current_status,
    next_status,
    old_status_name,
    new_status_name
) {
    swal({
        title: appName,
        text: "Please note the status of the document will change to " + new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    }, function () {

        $.ajax({
            url: "ajax.php",
            data: 'page=submitstatusdynamicworkflow' +
                  '&table_name=' + table_name +
                  '&current_status=' + current_status +
                  '&transaction_id=' + transaction_id +
                  '&next_status=' + next_status,
            type: "POST",
            success: function (response) {
                var res = JSON.parse(response);

                if (res.transaction_update.status === 'sucess') {
                    swal({
                        title: appName,
                        text: successMsg,
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
            error: function () {}
        });
    });
}

/* =========================
   MODAL CLOSE
========================= */
function PageChange() {
    $('#kt_modal_dynamic_popupxxl').modal('hide');
}
