<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_id = $_POST['task_id'];
$url = API."?mode=togetprojecttaskdetailspopup&task_id=".$task_id;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$task = $data['taskdetils'];
$project_teams = $data['project_teams'];
$projec_phases = $data['phases'];
?>
<script type="text/javascript">
    function submitgeneralcodefrom(){
        var post = $("#submitgeneralcodefromall").serialize();
        var code = $("#genc_code").val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=general_codes&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = 'index.php?option=COM_LIST_GENERAL_CODE_DETAILS&CODE='+code;
            },
            error:function (){}
        });
    }

</script>
<div class="modal-header">
    <h5 class="modal-title"> Add General Code </h5>
    <button type="button" class="btn-close " data-dismiss="alert "  data-dismiss="modal"  aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitgeneralcodefromall" >
        <div class="row col-lg-12">
            <div class="form-group col-lg-6">
                <span class=""> General Code</span>
                <input aria-label="Genc Code" class="form-control" type="text" id="genc_code" name="genc_code" value="">
            </div>
            <div class="form-group col-lg-6">
                <span class=""> General Code Name</span>
                <input aria-label="Genc Name" class="form-control" type="text" id="genc_name" name="genc_name" value="">
            </div>
            <div class="form-group col-lg-6">
                <span class="">Descrption</span>
                <textarea aria-label="Description" class="form-control" name="description" id="description"></textarea>
            </div>
        </div>
        <div class="modal-footer">

            <button type="button" class="btn btn-primary mr-2" onclick="submitgeneralcodefrom()" >Submit</button>
        </div>
    </form>
</div>
