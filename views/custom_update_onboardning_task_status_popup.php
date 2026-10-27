<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$statuse = ViewStatusData();
?>
<script type="text/javascript">
    function submitform(){
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updateonboardingtaskstatus&'+$("#frmsubmit").serialize(),
            success: function(response){
                var result = JSON.parse(response);
                console.log(result);
                if(result.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    $('#kt_modal_KTDatatable_remote').modal('hide');
                    location.reload();
                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.res.message,'');
                }
            }
        });
    }

</script>
<div class="modal-header">
    <h5 class="modal-title">Task Update To <?php echo $statuse[$_POST['status']]['name'];  ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
    <form class="kt-form kt-form--label-right" action="" id="frmsubmit" method="post">
        <div class="form-group">
            <span class="form-label">Comment <span class="text-danger">*</span></span>
            <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
        </div>
        <input type="hidden" name="transaction_id" value="<?php echo $_POST['task_id']; ?>">
        <input type="hidden" name="status"  value="<?php echo $_POST['status']; ?>">
        <input type="hidden" name="transaction_action_table"  value="<?php echo $_POST['transaction_action_table']; ?>">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary mr-2" onclick="submitform()" >Submit</button>
</div>
