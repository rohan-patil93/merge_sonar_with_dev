<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
 $url = API."?mode=getjobhiringsource&job_post_id=".$_POST['job_post_id'];
$api = callAPI($url);
$resultt = json_decode($api,1);
$job_postings = $resultt['job_postings'];
$tabledata = $resultt['job_hiring_source'];
$hirdata = $resultt['job_posting_publish_details'];

foreach($hirdata as $hirs){
    $publish[$hirs['job_hiring_source_id']] = $hirs['current_status'];
    $pubids[] = $hirs['job_hiring_source_id'];
    $publishdetails[$hirs['job_hiring_source_id']] = $hirs;
    $publishdetails_id[$hirs['job_hiring_source_id']] = $hirs['id'];
}
$stausresult = ViewStatusData();
$statuslist  = getstatusprogresslist('job_posting_publish_details');
foreach($statuslist as $statuslist){
    $statusnamelist[$statuslist['status']] = $statuslist['name'];
}
?>
<script type="text/javascript">
function shareinfisuitejob(id,type,source_id,publish_id){
    console.log('ID:'+id+'Type:'+type);
    $.ajax({
        url: "test_cul.php",
        data:'page=shareinfisuitejob&type='+type+'&post_id='+id+'&source_id='+source_id+'&publish_id='+publish_id+'&company_id=<?php echo $_SESSION['company_id']; ?>',
        type: "POST",
        success:function(data){
            console.log(data);
            swal({
                title: "<?php echo $_SESSION['error_message']['9059']; ?>",
                text: "<?php echo $_SESSION['error_message']['9058']; ?>",
                type: "success",
                showCancelButton: false,
                closeOnConfirm: true,
                showLoaderOnConfirm: true
            },function () {
                $('#kt_modal_dynamic_popup2').modal('hide');
            });

   //          swal.fire({
            //     title: data,
            //     type: "success",
            //     buttonsStyling: false,
            //     confirmButtonClass: "btn btn-success",
            //     showCancelButton: true,
            //     cancelButtonClass: "btn btn-danger"

        },
        error:function (){}
    });
}
function publishdatapopup(job_post_id){
        jQuery.ajax({
            url: "views/custom_insert_job_hiring_source_popup.php",
            data:'job_post_id='+job_post_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#dynamic_popup11').html(data);
                $("#kt_modal_dynamic_popup2").modal("show");
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header  ">
    <h5 class="modal-title">
        <small>&nbsp;&nbsp; <b class="f-w-600"><?php echo $job_postings['job_name']; ?></b></small>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <form class="" action="" method="post">
        <div class="">
            <div class="form-group row">
                <div class="col-lg-12 row" style="margin-left: 0px;">
                    <?php if(count((array)$tabledata) !=0){ ?>
                    <table class="table table-hover"><!--  table table-bordered nowrap -->
                        <thead>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 20%;">Logo</th>
                            <th scope="col" style="width: 20%;">Source Name</th>
                            <th scope="col" style="width: 10%;">Status</th>
                            <th scope="col" style="width: 10%;">Date</th>
                            <th scope="col" class="text-center" style="width: 25%;">Action</th>
                        </thead>
                        <?php $m=1; foreach($tabledata as $data){
                            $statucolorname = $stausresult[$publish[$data['id']]]; ?>
                            <tr>
                                <td><?php echo $m; ?></td>
                                <td><img class="img-80" src="uploads/all/<?php echo $data['hiring_source_logo']; ?>" alt="Logo"></td>
                                <td><?php echo $data['hiring_source_name']; ?></td>
                                <td>
                                    <?php if(in_array($data['id'],(array)$pubids)){ ?>
                                        <span class="form-label label label-danger" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $statusnamelist[$publish[$data['id']]]; ?></span></td>
                                    <?php }else{ ?>
                                        <span class="form-label label label-warning"> Yet To Published </span></td>
                                    <?php } ?>
                                    <td>
                                        <?php
                                        $publishdate = $publishdetails[$data['id']]['publish_date'];
                                        echo $publishdate != ""?fromDate($publishdate):"-"; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(in_array($data['id'],(array)$pubids)){
                                            $statusaction = toGetStatusProgressAction('job_posting_publish_details',$publish[$data['id']]);
                                            foreach($statusaction as $statusactions){
                                                $actionstatucolorname = $stausresult[$statusactions['status']];
                                                if($statusactions['status'] == ST_HOLD){ $status_val = "DRAFT";
}
                                                if($statusactions['status'] == ST_UNPUBLISH){  $status_val = "TRASH";
}
                                                if($statusactions['status'] == ST_PUBLISH){  $status_val = "CREATE";
}
                                                ?>
                                                <button type="button" style="color: <?php echo $actionstatucolorname['color']; ?>;    background:  <?php echo $actionstatucolorname['background_color']; ?>; border-color: <?php echo $actionstatucolorname['background_color']; ?>;" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'<?php echo $status_val; ?>',<?php echo $data['id']; ?>,<?php echo $publishdetails_id[$data['id']]; ?>)"class="btn btn-primary desktop-view text-capitalize" type="button"><?php echo $statusactions['action_label']; ?></button>

                                                <?php
                                            }
                                        }else{
                                            $statucolorname = $stausresult[0];  ?>
                                            <button type="button" class="btn btn-primary desktop-view text-capitalize" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'CREATE',<?php echo $data['id']; ?>,'')">Publish</button>
                                        <?php     }

                                        ?>
                                    </div>

                                </td>
                            </tr>
                            <?php $m++;
} ?>
                        </table>
                    <?php }else{ ?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Hiring Sources </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>

                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</div>
