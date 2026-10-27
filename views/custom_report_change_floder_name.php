<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<script type="text/javascript">

    function submitflderrom(){
        var  str = '';
         str = 'fid=page_name&table=my_reports&hid='+$('#report_id').val()+'&rid='+$('#change_floder_name').val();
          console.log(str);
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updatedynamicfied&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                    title: "Name  is chaged successfully",
                    type: "success",
                    buttonsStyling: true,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                    confirmButtonClass: "btn btn-success",
                    showCancelButton: false

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
    <span id="backid">  </span> &nbsp;
    <h5 class="modal-title" id="title"> Change to floder Name</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" >
        <form class="kt-form kt-form--label-right" action="" method="post">
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <textarea aria-label="Name new floder" class="form-control" name="change_floder_name" id="change_floder_name" placeholder="Name new floder"><?php echo $_POST['floder_name']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="report_id" id='report_id' value="<?php echo $_POST['report_id']; ?>">
                <button type="button" class="btn btn-primary mr-2" onclick="submitflderrom()" >Submit</button>
            </div>
        </form>
    </div>
</div>
