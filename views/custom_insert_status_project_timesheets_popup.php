<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);

    ?>
<script type="text/javascript">
    function submitrom(){
          var  str = '';
          status = $('#status_value').val();
         str = 'table='+$('#table').val()+'&comment='+$('#comment').val()+'&status='+status+'&hid='+$('#rid').val()+'&extra='+$('#extra').val();
          console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatusprojecttimesheets&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: "Comments Updated ",
                            text: "Do you want to close ? ",
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-success",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        });
            }
        });
    }

</script>

    <div class="modal-header">
                        <h5 class="modal-title">

                            <small>&nbsp;&nbsp; <b class="f-w-600">Transaction Type: </b>&nbsp; <?php echo viewtitle($_POST['table']); ?> &nbsp;&nbsp;
                             <b class="f-w-600">Status: </b>&nbsp; <?php echo $nm['name']  ?> </small>
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <span>Comment</span>
                                    <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                                </div>
                            </div>
                    </div>
                </div>


            <div class="modal-footer">
                <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
                <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
                <input type="hidden" name="extra" id='extra' value="<?php echo $_POST['extra']; ?>">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
            </div>

    </form>
</div>
