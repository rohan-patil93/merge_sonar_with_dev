<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$chosse_list =1;

$url = API."?mode=getrecruitngmanagerdashboard&job_posting_id=".$_POST['job_posting_id']."&field_name=".$_POST['field_name']."&value=".$_POST['value'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$job_posting_candidates = $result['job_posting_candidates'];

$stausresult = ViewStatusData();
$statuslist  = getstatusprogresslist('candidates');
foreach($statuslist as $statuslist){
    $statusnamelist[$statuslist['status']] = $statuslist['name'];
}
?>
<script type="text/javascript">
    function displaystausdrop(id,table,status_id,tab){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist_"+tab+"_"+id).html(data);
            },
            error:function (){}
        });
    }
    function statuspopup(status,id,table_name){
      jQuery.ajax({
          url: "views/custom_insert_status_popup.php",
          data:'table='+table_name+'&status='+status+'&rid='+id,
          type: "POST",
          success:function(data){
              $('#popup1').html(data);
              $('#kt_modal_KTDatatable_remote').modal("show");
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
<input type="hidden" name="job_posting_id" id="job_posting_id" value="<?php echo $jobpost; ?>">
<div class="modal-header">
    <h5 class="modal-title">
        Job Posting Candidates
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
        <div class="card">

            <div class="card-body custom-scroll" style=" height: 450px;">


                    <table  class="table table-hover m-b-0 " style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col">#.</th>
                                <th scope="col">Candidate Name</th>
                                <th scope="col">Email Id</th>
                                <th scope="col">Source</th>
                                <th scope="col">Applied Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count((array)$job_posting_candidates)>0){
                             $k=1;  foreach($job_posting_candidates as $jobpostings){
                                 $statucolorname = $stausresult[$jobpostings['current_status']];
                                 ?>
                            <tr>
                                <td><?php echo $k; ?></td>
                                <td class="text-capitalize"><?php  echo $jobpostings['candidate_name']; ?></td>
                                <td class="text-capitalize"><?php  echo $jobpostings['candidate_email']; ?></td>

                                <td class="text-capitalize"><?php  echo $jobpostings['job_hiring_source_name'] !=""?$jobpostings['job_hiring_source_name']:" - "; ?></td>
                                <td><?php  echo fromDate
                                ($jobpostings['created_date']); ?></td>
                                <td>
                                    <span class="form-label label label-danger" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $statusnamelist[$jobpostings['current_status']]; ?></span>
                                </td>

                    <?php $k++;
}
}else{ ?>
                            <tr>
                                <td colspan="8">
                                    <div class="card-px text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Job Posting Candidates </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    </div>
