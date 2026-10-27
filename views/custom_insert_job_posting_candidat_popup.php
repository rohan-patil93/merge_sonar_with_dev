<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<script type="text/javascript">
    function submitrom(){
          var  str = '';
         str = 'rate='+$('#ratting').val()+'&comment='+$('#comment').val()+'&type_id='+$('#type_id').val()+'&type='+$('#type').val()+'&job_can_id='+$('#job_can_id').val();
          console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updatejobpostcandidate&'+str,
            success: function(response) {
                console.log(response);
                   swal('Comments Successfully Updated','','success');
                   $('#kt_modal_KTDatatable_remote').modal('hide');
            }
        });
    }

</script>

<div class="modal-header">
    <h5 class="modal-title">Change <?php echo viewtitle($_POST['type']); ?></small>    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form kt-form--label-right" action="" method="post">
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
            <?php if($_POST['type']!="comments"){
                if($_POST['type']=="ratting"){ ?>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <span>Ratting</span>
                            <input aria-label="Ratting" type="text" class="form-control" id="ratting" name="ratting"  value="<?php echo $_POST['type_id'];?>">
                        </div>
                    </div>
                    <?php }else{ ?>
                        <input type="hidden" class="form-control" id="ratting_2" name="ratting" value="<?php echo $_POST['ratting']; ?>">
                    <?php } ?>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <span>Comment</span>
                            <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                        </div>
                    </div>
            <?php }else{

                $vdata1["where"] = array('job_posting_candedate_id'=> $_POST['job_can_id'],'type'=>'process');
                $vdata = urlencode(json_encode($vdata1));
                $vurl = API."?mode=gettabledetailswithconditions&table=candidate_history&data=".$vdata;
                $candis = callAPI($vurl);
                $candis = json_decode($candis,1);

                $vurl = API."?mode=gettabledetailswithconditions&table=job_selection_process_steps";
                $processs = callAPI($vurl);
                $processs = json_decode($processs,1);
                foreach ($processs as $proc) {
                    $pro[$proc['id']] = $proc['select_name'];
                }
            ?>
                    <div class="card-body pt-0">
                        <?php if(count((array)$candis) !=0){ foreach ($candis as  $candi) { ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45 symbol-light mr-5" data-toggle="tooltip" title="<?php echo $_SESSION['users'][$candi['created_by']]; ?>">
                                            <span class="symbol-label font-weight-bold"><?php echo initials($_SESSION['users'][$candi['created_by']]); ?></span>
                                        </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1"><?php echo $pro[$candi['value']]; ?></a>
                                    <span class="text-muted font-weight-bold"><?php echo notificationdatesdiff($candi['created_date']); ?>         </span>
                                </div>
                            </div>
                            <p class="text-dark-50 m-0 pt-2 font-weight-normal"><span class="text-dark m-0 pt-2 font-weight-bold">Comments: &nbsp;&nbsp;</span><?php echo $candi['comments']; ?></p>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No History</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>

                        <?php } ?>

                    </div>

            <?php } ?>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="job_can_id" id='job_can_id' value="<?php echo $_POST['job_can_id']; ?>">
            <input type="hidden" name="type_id"  id='type_id'  value="<?php echo $_POST['type_id']; ?>">
            <input type="hidden" name="type"   id='type' value="<?php echo $_POST['type']; ?>">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
            <?php if($_POST['type']!="comments"){ ?>
            <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
        <?php } ?>
        </div>
    </form>
</div>
