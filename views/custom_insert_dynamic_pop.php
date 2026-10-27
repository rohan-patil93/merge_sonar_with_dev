    <?php
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $nm = ViewStatusValue($_POST['status']);
    ?>
    <script type="text/javascript">
    function submitrom(){
      var  str = '';
      str = 'table='+$('#table').val()+'&'+$('#filed_name').val()+'='+$('#filed').val()+'&id='+$('#rid').val();
      console.log(str);
          $.ajax({
              url: "ajax.php",
              type: "POST",
              data: 'page=dynmicupdatetable&'+str,
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
      <h5 class="modal-title"><?php echo $_POST['title']; ?> </h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i aria-hidden="true" class="ki ki-close"></i>
      </button>
  </div>
  <div class="modal-body" >

      <form class="kt-form kt-form--label-right" action="" method="post">
          <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
              <div class="form-group row">
                  <div class="col-lg-12">
                      <span><?php echo $_POST['field_title']; ?> </span>
                      <textarea aria-label="Filed" class="form-control" name="filed" id="filed"></textarea>
                  </div>

              </div>
          </div>
      </div>


      <div class="modal-footer">
          <input type="hidden" name="filed_name" id='filed_name' value="<?php echo $_POST['filed_name']; ?>">
          <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
          <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">

          <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
      </div>

  </form>
</div>
