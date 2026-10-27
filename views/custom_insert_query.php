<?php
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $querytypeurl =  API."?mode=gettabledetails&fid=genc_code&rid=QUERYTYPES&table=".TBL_GENRAL_CODE_DETAILS;
    $querytypelist = callAPI($querytypeurl);
    $querytypelist = json_decode($querytypelist,1);
    $query_types = array();
    foreach ($querytypelist as $querystype) {
        $query_types[$querystype['genc_detail_code']] = $querystype['genc_detail_name'];
    }
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
    function submitrom(){

          var  str = '';
          status = $('#status_value').val();
         str = 'company_id='+$('#company_id').val()+'&comment='+$('#comment').val()+'&table_name='+$('#table_name').val()+'&trno='+$('#trno').val()+'&transaction_id='+$('#transaction_id').val()+'&created_by='+$('#created_by').val()+'&new_reply='+$('#new_reply').val()+'&send_id='+$('#send_id').val()+'&query_text='+$('#query_text').val()+'&query_type='+$('#query_type').val();
          console.log(str);

        $.ajax({
            url: "ajax.php",
            type: "POST",
             data: 'page=toinsertquery&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: "<?php echo $_SESSION['error_message']['3003']; ?> ",
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
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                            }
                        });
            }
        });
    }

</script>

    <div class="modal-header">
                        <h5 class="modal-title">Queries</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <div class="modal-body" >

            <form method="post" action="">
                    <div class="notes">
                        <div class="form-radio">

                        <?php foreach ($querytypelist as $querytype) { ?>

                        <div class="radio radio-inline">
  <label class="form-label">
   <input aria-label="Query Type" type="radio" id="query_type_2" name="query_type" value="<?php echo $querytype['genc_detail_code']; ?>" id="imp_2">
    <i class="helper"></i><?php echo $querytype['genc_detail_name']; ?>
  </label>
</div>
                        <?php } ?>

</div>

                        <textarea aria-label="Query Text" name="query_text" id="query_text" class="form-control mt-2"></textarea>
                        <input type="hidden" name="submit_query" value="query">
                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                        <input type="hidden" id="trno" name="trno" value="<?php echo $_POST['tran_no']; ?>">
                        <input type="hidden" id="table_name" name="table_name" value="<?php echo $_POST['table_name']; ?>">
                        <input type="hidden" id="transaction_id" name="transaction_id" value="<?php echo $_POST['tran_no']; ?>">
                        <input type="hidden" id="created_by" name="created_by" value="<?php echo $_SESSION['userid']; ?>">
                        <input type="hidden" id="send_id" name="send_id" value="<?php echo $_POST['hr_connect']; ?>">
                        <input type="hidden" id="new_reply" name="new_reply" value="N">

                    </div>


            <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
            </div>

    </form>
</div>
