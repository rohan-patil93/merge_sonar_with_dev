<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$candidate_id = $_POST['candidate_id'];
?>
<script type="text/javascript">
function submitCandidateDocuments(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
        var formData = new FormData();
        formData.append("page", "submitcandidatedocuments");
        formData.append("document_name", $("#document_name_popup").val());
        formData.append("candidate_id", '<?php echo $candidate_id; ?>');
        var dataArray = $("#candidatesform").serializeArray();
        if($('input[name=document_file]')[0]){
            formData.append('document_file', $('input[name=document_file]')[0].files[0]);
        }
        $.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                var returnresult = JSON.parse(data);
                console.log(returnresult);
                var result = returnresult.respons;
                if(result.status =="fail"){
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.message,'');
                }else{
                    if(result.LASTID == '0'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    }
                    $("#uploadedalldocuments").html(returnresult.all_attchaments);
                    $('#kt_modal_KTDatatable_remotem').modal("hide");

                }
            },
            error:function (){}
        });
    }
}
</script>
<div class="modal-header">
    <h5 class="modal-title">
        Upload Documents
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
    <div class="row">
        <div class="col-lg-6 form-group">
            <span class="form-label">Document Name <span class="requried text-danger">*</span></span>
              <span class="d-block error-message text-danger"></span>
            <input aria-label="Document Name" type="text" class="form-control requried_field" name="document_name" id="document_name_popup">
        </div>
        <div class="col-lg-6 form-group">
            <span class="form-label">Document <span class="requried text-danger">*</span></span>
              <span class="d-block error-message text-danger"></span>
            <input aria-label="Document File" type="file" class="form-control requried_field" name="document_file" id="document_file_popup">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitCandidateDocuments()">Upload</button>
</div>
