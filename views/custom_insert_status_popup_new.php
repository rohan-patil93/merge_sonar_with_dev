<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$statuse = ViewStatusData();

    ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
function closebutnfunction(){
             $('#checkstausinput<?php echo $_POST['rid']; ?>').prop('checked', false);
             $("#checkstausid<?php echo $_POST['rid']; ?>").removeClass("done-task");
}

    function submitrom(){
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatusnew&'+$("#insertstatsuform").serialize(),
            success: function(response){
                console.log(response);
                var datas = JSON.parse(response);
                if(datas.next_user =="" || datas.next_user ==null || datas.next_user =="0"){
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: '<?php echo $_SESSION['error_message']['9003']; ?>'
                    })
                    return false;
                }
                $('#kt_modal_KTDatatable_remote').modal('hide');
                 window.location.reload();
            }
        });
    }

</script>

    <div class="modal-header">
                        <h5 class="modal-title">
                            <small> <b>Transaction Type: </b>&nbsp; <?php echo viewtitle($_POST['table']); ?> &nbsp;&nbsp;
                             <b>Status: </b>&nbsp; <?php echo $statuse[$_POST['status']]['name'];  ?> </small>
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closebutnfunction()"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post" id="insertstatsuform">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <span>Comment</span>
                                    <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                                </div>
                            </div>
                    </div>


            <div class="modal-footer">
                <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
                <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
                <input type="hidden" name="transaction_id"  value="<?php echo $_POST['transaction_id']; ?>">
                <input type="hidden" name="workflow_id"  value="<?php echo $_POST['workflow_id']; ?>">
                <input type="hidden" name="type"  value="<?php echo $_POST['type']; ?>">
                <input type="hidden" name="ids"  value="<?php echo $_POST['ids']; ?>">
                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" onclick="closebutnfunction()">Close</button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
            </div>

    </form>
</div>
