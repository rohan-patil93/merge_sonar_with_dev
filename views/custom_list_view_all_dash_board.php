<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$chosse_list =1;
$_SESSION['apply_js']['table'] = 1;
$_GET['TYPE'] = $_REQUEST['TYPE'];
$url = API."?mode=getviewalldetilarecruitmentdashboardineg&roleshortcode=".$_SESSION['role_ids'][0]."&type=".$_REQUEST['TYPE'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$title = $result['name'];
$job_postings = $result['data'];
$stausresult = ViewStatusData();
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
        <?php echo $title; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
        <div class="card">

            <div class="card-body">


                    <table  class="table table-hover m-b-0 " style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col">#.</th>
                                <th scope="col">Job Post Name</th>
                                <th scope="col">Job Number</th>
                                <th scope="col">Requested Date</th>
                                <th scope="col">Required Date</th>
                                <th scope="col">No of Resources</th>
                                <th scope="col">Job Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count((array)$job_postings)>0){
                             $k=1;  foreach($job_postings as $jobpostings){ ?>
                            <tr>
                                <td><?php echo $k; ?></td>
                                <td class="text-capitalize"><?php  echo $jobpostings['job_name']; ?></td>
                                <td class="text-capitalize"><?php  echo $jobpostings['job_number']; ?></td>
                                <td><?php  echo fromDate
                                ($jobpostings['requested_date']); ?></td>
                                <td><?php  echo fromDate($jobpostings['required_date']); ?></td>
                                <td class="text-right"><?php  echo $jobpostings['no_of_resources']; ?></td>
                                <td>
                                    <?php if($jobpostings['job_status']=='0'){ ?>
                                        <span class="label label-lg label-success label-inline mr-2">Open</span>
                                    <?php }elseif($jobpostings['job_status']=='1'){ ?>
                                        <span class="label label-lg label-danger label-inline mr-2">Close</span>
                                    <?php }else{ ?>
                                        <span class="label label-lg label-warning label-inline mr-2">Hold</span>
                                    <?php  } ?>
                                </td>
                                <td>
                                    <?php if(($_SESSION['role_ids'][0]==HIRINGMANAGER_ROLE && $_GET['TYPE']!="ACTIVE" ) || $jobpostings['job_status']=="2"){ ?>
                                        <span class="text-muted f-10">
                                                        <i class="far fa-clock"></i>
                                                       <?php echo  notificationdatesdiff($jobpostings['updated_date']);?>
                                                    </span>
                                    <?php }else{ ?>
                                    <?php if($_GET['TYPE']!="PUBLISH" && $_GET['TYPE']!="YET"){
                                        if($_GET['TYPE']!="UNPUBLISH"){ ?>
                                    <div class="btn-group">
                                            <span onclick="displaystausdrop(<?php echo $jobpostings['id']; ?>,<?php echo "'job_postings'"; ?>,<?php echo $jobpostings['current_status']; ?>,'1act')" class="btn " style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-target"></i> </span>
                                            <div class="dropdown-menu" id="dropdownlist_1act_<?php echo $jobpostings['id']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else{?>
                                        <span class="text-muted f-10">
                                            <i class="far fa-clock"></i>
                                           <?php echo  notificationdatesdiff($jobpostings['updated_date']);?>
                                        </span>
                                    <?php } ?>
                                    <?php }else{ ?>
                                        <div class="btn-group">
                                            <span  class="btn "  type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="ti-target"></i> </span>
                                            <div class="dropdown-menu">
                                                <button type="button" !" class="dropdown-item waves-effect waves-light" onclick="publishdatapopup(<?php echo $jobpostings['id']; ?>)" name="status" id="status" value="">Publish</button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php $k++;
}
                        }else{?>
                            <tr>
                                <td colspan="8">
                                    <div class="card-px text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>
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
