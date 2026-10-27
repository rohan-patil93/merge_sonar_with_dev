document.addEventListener('DOMContentLoaded', function () {
    console.log('page testing');
    // ---------- ELEMENTS ----------
    var declarationCheckbox = document.getElementById('declaration-checkbox');
    var misrepresentationCheckbox = document.getElementById('misrepresentation-checkbox');
    var blacklistCheckbox = document.getElementById('blacklist-checkbox');
    var submitButton = document.getElementById('submit-button');
    var editBtn = document.getElementById('editBtn');

    	// ---------- TOGGLE SUBMIT BUTTON ----------
		function toggleSubmitButton() {
         // Get all checkboxes
               const checkbox1 = document.getElementById('declaration-checkbox');
               const checkbox2 = document.getElementById('misrepresentation-checkbox');
               const checkbox3 = document.getElementById('blacklist-checkbox');
               console.log('0000');
         // Get the submit button
               const submitButton = document.getElementById('submit-button');

         // Enable the button if all checkboxes are checked
               if (checkbox1.checked && checkbox2.checked && checkbox3.checked) {
                    console.log('1111');
                   submitButton.disabled = false;
               } else {
                console.log('2222');
                   submitButton.disabled = true;
               }
           }

    // Bind checkbox events
    [declarationCheckbox, misrepresentationCheckbox, blacklistCheckbox].forEach(function (checkbox) {
        console.log('----');
        if (checkbox) {
            checkbox.addEventListener('change', toggleSubmitButton);
        }
    });

    // ---------- EDIT BUTTON ----------
    if (editBtn) {
        editBtn.addEventListener('click', function () {
            console.log('edit');
            PageChange();
        });
    }

    // ---------- SUBMIT BUTTON ----------
    if (submitButton) {
        submitButton.addEventListener('click', function () {
            var recordId = this.getAttribute('data-record-id');
            submitFormData(recordId);
        });
    }

});
console.log('page testing23324');
const cfg3 = document.getElementById('csr_config3')?.dataset || {};

const oldId = cfg3.oldId && cfg3.oldId !== '' ? cfg3.oldId : null;
    function submitFormData(lastRecId) {



	    console.log('oldId:', oldId);
           $.ajax({
               url: "ajax.php",
               data: {
                   page: 'insertData',
                   rid: lastRecId,
                   old_id: oldId
               },
               type: "POST",
               success: function(response) {
                   console.log(response);
                   const data = JSON.parse(JSON.parse(response));
                   if (data['status'] === 'sucess') {
                 /*Swal.fire({
                     title: "Data Saved",
                     text: "Do you want to print a PDF?",
                     icon: "success",
                     showCancelButton: true,
                     confirmButtonText: "Print PDF",
                 }).then((result) => {
                    if (result.isConfirmed) {
                        printPDF('CSRViewContainer');

                    }
                    window.location.reload();
                 });*/
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
	   console.log(printContents)
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
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
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
    };