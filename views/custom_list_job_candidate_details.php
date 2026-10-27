<?php $chosse_list =1;
include_once "new_from_submit.php";
$_SESSION['apply_js']['table'] = 1;
$allpermision = $_SESSION['role_permission']['candidates'];
if($allpermision['permission_view'] == '1'){$permission_listbtn = "disabled";
}
if($allpermision['permission_insert'] == '1'){$insert_permissionbtn = "disabled";
}
if($allpermision['permission_import'] == '1'){$permission_importbtn = "disabled";
}
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled";
}
if($allpermision['permission_approval'] == '1'){$approval_permissionbtn = "disabled";
}

?>
<script type="text/javascript">
    function displaycandidateview(job_can_id){
        jQuery.ajax({
            url: "views/custom_job_candidate_popup.php",
            data:'job_post_can_id='+job_can_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#dynamic_popup1').html(data);
                 $("#kt_modal_dynamic_popup").modal("show");
            },
            error:function (){}
        });
    }
    function dynamiceditpopup(id=''){
        var job_posting_id = $('#job_posting_id').val();
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option=COM_INSERT_NEW_JOB_POSTING_CANDIDATES&job_posting_id='+job_posting_id+'&Rid='+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                 $("#kt_modal_dynamic_popup").modal("show");
            },
            error:function (){}
        });
    }
    function AddCandidate(){
        var job_posting_id = $("#job_posting_id_ajax").val();
        jQuery.ajax({
            url: "views/custom_insert_candidates_popup.php",
            data:'job_posting_id='+job_posting_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                 $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
</script>

<input type="hidden" name="job_posting_id" id="job_posting_id" value="<?php echo $jobpost; ?>">
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card-custom card-sticky" id="kt_page_sticky_card">

<div class="card-header">
    <h5>Job Post Candidates</h5>
    <div class="card-header-right">
        <input type="hidden" id="job_posting_id_ajax" value="<?php echo $jobpost; ?>">

        <button type="button" class="btn btn-primary" onclick="AddCandidate()" <?php echo $insert_permissionbtn; ?>><i class="ti-plus text-white"></i> Candidates</button>

          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>

    </div>
</div>

        <div class="card-custom">
            <div class="card-body">
             <div class="dt-responsive table-responsive row">
                 <?php if(count((array)$resdata) !=0){ ?>
                <table  class="table table-hover m-b-0 " style="font-size:13px;">

                    <thead>
                        <tr>
                            <th scope="col">#.</th>
                            <th scope="col">Candidate Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">RM Shortlist</th>
                            <th scope="col">HM Shortlist</th>
                            <th scope="col">Selection Process</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $k=1; foreach ($resdata as $res) {
                            $candidate_table = array();
                            if(isset($candidatelist[$res['candidate_id']])){
                                $candidate_table = $candidatelist[$res['candidate_id']];
                            }
                         ?>
                            <tr>
                                <td><?php echo $k; ?></td>
                                <td class="text-capitalize"><?php  echo $candidate_table['display_name']; ?></td>
                                <td><?php  echo $candidate_table['email_id']; ?></td>
                                <td><?php  echo $candidate_table['phone']; ?></td>
                                <td>
                                    <?php if($res['shortlisted']=='0'){ ?>
                                        <span class="label label-lg label-success label-inline mr-2">Shortlisted</span>
                                    <?php }elseif($res['shortlisted']=='1'){ ?>
                                        <span class="label label-lg label-danger label-inline mr-2">Rejected</span>
                                    <?php }else{ ?>
                                        <span class="label label-lg label-warning label-inline mr-2">Pending</span>
                                    <?php  } ?>

                                </td>
                                <td>
                                    <?php if($res['hm_shortlisted']=='0'){ ?>
                                        <span class="label label-lg label-success label-inline mr-2">Shortlisted</span>
                                    <?php }elseif($res['hm_shortlisted']=='1'){ ?>
                                        <span class="label label-lg label-danger label-inline mr-2">Rejected</span>
                                    <?php }else{ ?>
                                        <span class="label label-lg label-warning label-inline mr-2">Pending</span>
                                    <?php  } ?>

                                </td>
                                <td>

                                    <p class="font-weight-bold text-<?php echo $process_color[$res['job_selection_process_step_id']]; ?>">
                                        <?php echo $res['job_selection_process_step_id'] != '0'?$process[$res['job_selection_process_step_id']]:$processdata['process_name']; ?>
                                    </p>
                                </td>
                                <td>


                                    <button class="btn btn-primary btn-sm btn-pd" style="color: <?php echo "#".$stausresult[$res['current_status']]['color']; ?>;    background:  <?php echo "#".strval( $stausresult[$res['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$res['current_status']]['background_color']); ?>;" type="button"><?php echo  $stausresult[$res['current_status']]['name']; ?> </button>
                                </td>
                                <td nowrap="nowrap" style="display: flex;">
                                    <div class="dropdown dropdown-inline d-inline-block">
                                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-bs-toggle="dropdown" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <i class="fa fa-star"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">    <ul class="nav nav-hoverable flex-column">
                                            <li class="nav-item"  style="margin-left:10px"><button type="button" class="nav-link" onclick="updatepostcandidate('0',<?php echo $res['id']; ?>,'comments')" ><i class="nav-icon la la-comments"></i><span class="nav-text">Commennts</span></button></li>
                                            <li class="nav-item"  style="margin-left:10px"><a class="nav-link" href="#"><span class="nav-text">Ratting</span></a></li>

                                            <li class="nav-item" style="margin-left:30px">
                                                <?php $rate= $res['ratting'];
                                                for ($i=1; $i < 6 ; $i++) {
                                                    if($rate >= $i){ ?>
                                                        <button type="button" class="bg-transparent border-0 p-0" onclick="updatepostcandidate(<?php echo $i; ?>,<?php echo $res['id']; ?>,'ratting')">
                                                            <i class="fa fa-star  text-warning mt-3"></i>
                                                        </button>
                                                    <?php }elseif ($rate >=($i-0.5)) { ?>
                                                        <button type="button" class="bg-transparent border-0 p-0" onclick="updatepostcandidate(<?php echo $i; ?>,<?php echo $res['id']; ?>,'ratting')">
                                                            <i class="fa fa-star-half-alt  text-warning mt-3"></i>
                                                        </button>
                                                    <?php }else{?>
                                                        <button type="button" class="bg-transparent border-0 p-0" onclick="updatepostcandidate(<?php echo $i; ?>,<?php echo $res['id']; ?>,'ratting')">
                                                            <i class="fa fa-star  text-warning mt-3"></i>
                                                        </button>
                                                    <?php }

                                                } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

    <a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$res['candidate_id']); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit" <?php echo $edit_permissionbtn; ?>><i class="fa fa-edit"></i>    </a>
    <button type="button" class="btn btn-sm btn-clean btn-icon" onclick="displaycandidateview(<?php echo $res['id']; ?>)" title="View"><i class="fa fa-eye"></i>    </button>

                            </td>
                        </tr>
                        <?php $k++;
} ?>
                    </tbody>
                </table>
<?php }else{ ?>
    <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php } ?>
            </div>
            </div>
    </div>
</div>
