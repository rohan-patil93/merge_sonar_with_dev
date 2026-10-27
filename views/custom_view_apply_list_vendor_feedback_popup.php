<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetvendorfeedbacksetupdetails&user_id=".$_SESSION['userid']."&emp_id=".$_SESSION['userid']."&vendor_id=".$_POST['vendor_id'];
$data_details = callAPI($url);
$data_details = json_decode($data_details,1);
$data= $data_details['list'];
$rat = $data_details['ratting'];
$stu = $data_details['reviewstatus'];
?>
<script type="text/javascript">

function appliedvendorfeedbackapplyed(vendor_id,setup_id){
    console.log("vendor_id:"+vendor_id);
    jQuery.ajax({
        url: "views/custom_view_vendor_feedback_questions_popup.php",
        data:'vendor_id='+vendor_id+'&setup_id='+setup_id,
        type: "POST",
        success:function(data){
          $('#dynamic_popup11').html(data);
          $('#kt_modal_dynamic_popup2').modal("show");
        },
        error:function (){}
  });
}
</script>
<div class="modal-header">
    <h5 class="modal-title">List Of Vendor Feedback  </h5>
    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-5" >
    <table class="table table-styling">
        <tr class="table-primary">
            <th scope="col">#</th>
            <th scope="col">Reviews</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Multiple Reviews</th>
            <th scope="col">Last Ratting</th>
            <th scope="col">Status</th>
            <th scope="col"></th>
        </tr>
        <?php $i=1; foreach($data as $dat){ ?>
        <tr class="">
            <td><?php echo $i; ?></td>
            <td><?php echo $dat['feedback_name']; ?> </td>
            <td><?php echo fromDate($dat['start_date']); ?></td>
            <td><?php echo fromDate($dat['end_date']); ?></td>
            <td>
                <?php if($dat['allow_multiple_times']==0){ ?>
                    <span class="text-success"> Allowed</span>
                <?php }else{?>
                    <span class="text-warning"> Not Allowed</span>
                <?php } ?>
            </td>
            <td>

                <?php
                    if(isset($rat[$dat['id']])){
                    for($n=1;$n<11;$n++){ ?>
                    <i class="fa fa-star f-12 text-warning" <?php if($n>$rat[$dat['id']]){ ?> style= "color: #b7aeae !important;"; <?php
} ?>></i>
                <?php }
                } ?>
            </td>
            <td>

                <?php
                    if(isset($stu[$dat['id']])){
                        if($stu[$dat['id']]==2){ ?>
                            <span class="f-16 f-w-600 text-warning"> Doing</span>
                        <?php }else{ ?>
                                <span class="text-success f-16 f-w-600 "> Completed</span>
                        <?php }
                }else{
                } ?>
            </td>
            <td>
                <div  <?php if(isset($rat[$dat['id']]) && $dat['allow_multiple_times']==1){ ?> class="text-muted" <?php
}else{ ?>  class="text-info cursor-pointer" onclick="appliedvendorfeedbackapplyed(<?php echo $_POST['vendor_id'].",".$dat['id']; ?>)" <?php
} ?> role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                Feedback
                </div>
            </td>
        </tr>
        <?php $i++;
} ?>
    </table>
</div>

