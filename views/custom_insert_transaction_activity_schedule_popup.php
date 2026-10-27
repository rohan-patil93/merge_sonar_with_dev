<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$reviewwrw["where"] = array('company_id'=>$_SESSION['company_id'],'id'=>$_POST['id'],'is_active'=>0);
$reviewwrw["field"] = "check_list";
$reviewconw = urlencode(json_encode($reviewwrw));
$listcomplianurl = API."?mode=gettabledetailswithconditions&table=transaction_activity_schedule&data=".$reviewconw;
$listcompliances= callAPI($listcomplianurl);
$compliancetaskhistory = json_decode($listcompliances, 1);
$compliancetaskhistory = $compliancetaskhistory[0];
$check_list = json_decode($compliancetaskhistory['check_list'], 1);
?>
<script>
function submitform(){
     var rid = $("#riddd").val();
        var attachment_type = $("#attachment_type").val();
        var popupfile = $("#popupfile").val();
        if(attachment_type == 'Y' && popupfile ==""){

                swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Its mandatory to upload the attachment",
            type: "error",
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: "Ok",
            confirmButtonClass: "btn-danger",
            closeOnCancel: false
        });
            return false;
        }
        var formData = new FormData();
        formData.append("page", "updatetransactionactivityschedule");
        var dataArray = $("#frmsubmit").serializeArray(),
        dataObj = {};
        $(dataArray).each(function(i, field){
          formData.append(field.name, field.value);
        });
        var popattachment = document.getElementById('popupfile').files[0];
       formData.append("file",popattachment);
        $.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            async:false,
            contentType: false,
            processData: false,
            success:function(response){
                console.log(response);
                var jsonres = JSON.parse(response);
                if(jsonres.status == "<?php echo ST_INPROGRESS; ?>"){
                    $(".statusid_"+rid).html('<span class="form-label label" style="color: #FFFFFF;    background:  #f5b700; border-color: ##f5b700;">In Progress </span>');
                }
                if(jsonres.status == "<?php echo ST_COMPLETE; ?>"){
                    $('.sendapprove_'+rid).attr("disabled", true);
                    $(".statusid_"+rid).html('<span class="form-label label " style="color:#FFFFFF;background:#bc00dd;border-color:#bc00dd;">Completed</span>');
                    location.reload();
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                $('#kt_modal_KTDatatable_remote').modal("hide");
             }
        });

     //        url: "ajax.php",
     //        type: "POST",
     //        data: 'page=insertcompliancetaskanswer&'+formdata,
    }
    function closecompliencfunction(){
        $('.checkcheckboxcopliance_<?php echo $_POST['id'].'_'.$_POST['aryposition']; ?>').prop('checked', false);
        $('.checkcheckboxcopliance_<?php echo $_POST['id'].'_'.$_POST['aryposition']; ?>').attr("disabled", false);
        $(".campliancecros_<?php echo $_POST['id'].'_'.$_POST['aryposition']; ?>").removeClass("done-task");

        $('#showusername_<?php echo $_POST['aryposition']; ?>').hide();
        $('#showselectuser_<?php echo $_POST['aryposition']; ?>').show();
    }
    </script>
<div class="modal-header">
    <h5 class="modal-title">
        <?php echo "Submit Task / Checklist"; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closecompliencfunction()"></button>
</div>
<div class="modal-body" >
    <form class="kt-form kt-form--label-right" action="" id="frmsubmit" method="post">
        <input type="hidden" name="candidate_id" value="<?php echo $_POST['candidate_id']; ?>">
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
            <div class="form-group row">
                <div class="col-lg-12  form-group">
                    <span class="form-label"><?php echo $check_list[$_POST['aryposition']]['name']; ?><span class="text-danger">*</span></span>
                    <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                </div>
                <div class="col-lg-12  form-group" style="<?php echo $check_list[$_POST['aryposition']]['attachment_requerd']=='Y'?"display:block":"display:none";?>">
                    <span class="form-label">Attchements<span class="text-danger">*</span></span>
                    <input aria-label="Popupfile" class="form-control" id="popupfile" type="file">
                </div>
            </div>
        </div>
        <input type="hidden" name="attachment_type" id='attachment_type' value="<?php echo $check_list[$_POST['aryposition']]['attachment_requerd']; ?>">
        <input type="hidden" name="transaction_table" id='transaction_table' value="<?php echo $_POST['transaction_table']; ?>">
        <input type="hidden" name="aryposition" id='aryposition' value="<?php echo $_POST['aryposition']; ?>">
    <input type="hidden" name="rid" id='riddd' value="<?php echo $_POST['id']; ?>">
    <input type="hidden" name="check_list" id='check_list' value='<?php echo $compliancetaskhistory['check_list']; ?>'>
    <input type="hidden" name="compliance_id" id='check_list_2' value='<?php echo $compliancetaskhistory['compliance_id']; ?>'>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" onclick="closecompliencfunction()">Close</button>
    <button type="button" class="btn btn-primary mr-2" onclick="submitform()" >Submit</button>
</div>
