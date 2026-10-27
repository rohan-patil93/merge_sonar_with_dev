function toggleCsrProposalSubmitButton() {
    var checkbox1 = document.getElementById('declaration-checkbox');
    var checkbox2 = document.getElementById('misrepresentation-checkbox');
    var checkbox3 = document.getElementById('blacklist-checkbox');
    var submitButton = document.getElementById('submit-button');

    if (!checkbox1 || !checkbox2 || !checkbox3 || !submitButton) {
        return;
    }

    if (checkbox1.checked && checkbox2.checked && checkbox3.checked) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
}

window.initCsrProposalViewDetails = function () {
    var $declaration = $('#declaration-checkbox');
    var $misrepresentation = $('#misrepresentation-checkbox');
    var $blacklist = $('#blacklist-checkbox');
    var $submitButton = $('#submit-button');
    var $editBtn = $('#editBtn');

    $declaration.off('change.csrProposalView').on('change.csrProposalView', toggleCsrProposalSubmitButton);
    $misrepresentation.off('change.csrProposalView').on('change.csrProposalView', toggleCsrProposalSubmitButton);
    $blacklist.off('change.csrProposalView').on('change.csrProposalView', toggleCsrProposalSubmitButton);

    $editBtn.off('click.csrProposalView').on('click.csrProposalView', function () {
        PageChange();
    });

    $submitButton.off('click.csrProposalView').on('click.csrProposalView', function () {
        var recordId = this.getAttribute('data-record-id');
        submitFormData(recordId);
    });

    toggleCsrProposalSubmitButton();
};

document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.initCsrProposalViewDetails == 'function') {
        window.initCsrProposalViewDetails();
    }
});

function submitFormData(lastRecId) {
    var cfg3 = document.getElementById('csr_config3');
    var oldId = null;
    if (cfg3 && cfg3.dataset && cfg3.dataset.oldId && cfg3.dataset.oldId != '') {
        oldId = cfg3.dataset.oldId;
    }

    $.ajax({
        url: "ajax.php",
        data: {
            page: 'insertData',
            rid: lastRecId,
            old_id: oldId
        },
        type: "POST",
        success: function(response) {
            const data = JSON.parse(JSON.parse(response));
            if (data['status'] == 'sucess') {
                window.location.href = 'index.php';
            }
        },
        error: function(error) {
            console.error('Error fetching UOM:', error);
        }
    });
}

function printPDF(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
    window.location.reload();
}

function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                var res = JSON.parse(response);
                window.location.href = window.location.href;
                if(res.transaction_update.status=='sucess'){
                   window.location.href = window.location.href;
                   swal({
                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                    type: "success",
                    showCancelButton: false,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    window.location.href = window.location.href;
                });
               }else{
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
        error:function (){}
    });
    });
}

function PageChange() {
    $('#kt_modal_dynamic_popupxxl').modal('hide');
}
