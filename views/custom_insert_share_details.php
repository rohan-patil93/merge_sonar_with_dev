<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);
?>
<script type="text/javascript">
    function submitrom(){
        var  str = '';
        str = 'comment='+$('#comment').val()+'&share_id='+$('#kt_select2_4').val()+'&share_table_id='+$('#share_table_id').val()+'&share_table='+$('#share_table').val();
        console.log(str);
          $.ajax({
              url: "ajax.php",
              type: "POST",
              data: 'page=add_share_data&'+str,
              success: function(response) {
                  console.log(response);
                swal.fire({
                    title: "Comments Updated ",
                    text: "Do you want to close ? ",
                    type: "success",
                    buttonsStyling: false,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                    confirmButtonClass: "btn btn-success",
                    showCancelButton: false,
                    cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                    cancelButtonClass: "btn btn-default"
                }).then(function(result) {
                    if (result.value) {
                        jQuery.ajax({
                            url: "views/custom_job_candidate_popup.php",
                            data:'job_post_can_id='+$('#share_table_id').val(),
                            type: "POST",
                            success:function(data){
                                console.log(data);
                                $('#dynamic_popup1').html(data);
                                $('#kt_modal_dynamic_popup').modal("show");
                            },
                            error:function (){}
                        });
                    }
                });
            }
        });
      }

  </script>

<div class="modal-header">
      <h5 class="modal-title">Share Details </h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>X
    </button>
</div>
      <div class="modal-body" >
          <form class="kt-form kt-form--label-right" action="" method="post">
              <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                  <div class="form-group row">
                      <div class="col-lg-6">
                          <span>User </span>
                          <select aria-label="Shar Is" class="form-control js-example-basic-single" id="kt_select2_4" name="shar_is" >
                                <option value="0">Select Any One</option>
                                <?php foreach ($_SESSION['users'] as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                          </select>
                      </div>
                      <div class="col-lg-6">
                          <span>Comment</span>
                          <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                      </div>

                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <input type="hidden" name="share_table_id" id='share_table_id' value="<?php echo $_POST['share_table_id']; ?>">
              <input type="hidden" name="share_table" id='share_table' value="<?php echo $_POST['share_table']; ?>">
              <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
          </div>
      </form>
  </div>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
