// -------------------- INPUT TRIM (ON BLUR) --------------------
document.querySelectorAll(
    'input[type="text"], input[type="email"], input[type="number"]'
).forEach(function (input) {
    input.addEventListener('blur', function () {
        this.value = this.value.trim();
    });
});

// -------------------- RESET FORM --------------------
var resetBtn = document.getElementById('resetBtn');
if (resetBtn) {
    resetBtn.addEventListener('click', function () {
        var form = document.getElementById('contractorForm');
        if (form) {
            form.reset();
        }
    });
}

// -------------------- WORKFLOW POPUP --------------------
function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {

    var loader =
        '<div class="text-center" id="loader-show">' +
            '<img src="assets/images/spiner/spiner3.gif" class="workflow-loader">' +
        '</div>';

    $("#popup1").html(loader);
    $('#kt_modal_KTDatatable_remote').modal("show");

    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:
            'table_name=' + table_name +
            '&current_status=' + current_status +
            '&transaction_id=' + transaction_id +
            '&next_status=' + next_status,
        type: "POST",
        success: function (data) {
            $('#popup1').html(data);
        },
        error: function () {}
    });
}

// -------------------- WORKFLOW SUBMIT --------------------
function submitDynamicWorkflow(
    table_name,
    transaction_id,
    current_status,
    next_status,
    old_status_name,
    new_status_name
) {
    swal({
        title: window.APP_NAME || "",
        text: "Please note the status of the document will change to " + new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    }, function () {

        jQuery.ajax({
            url: "ajax.php",
            data:
                'page=submitstatusdynamicworkflow' +
                '&table_name=' + table_name +
                '&current_status=' + current_status +
                '&transaction_id=' + transaction_id +
                '&next_status=' + next_status,
            type: "POST",
            success: function (response) {

                var res = JSON.parse(response);

                if (res.transaction_update.status === 'sucess') {
                    swal({
                        title: window.APP_NAME || "",
                        text: window.SUCCESS_MSG || "",
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
