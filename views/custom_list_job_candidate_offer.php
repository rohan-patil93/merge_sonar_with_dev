<?php
$chosse_list =1;
$_SESSION['apply_js']['table'] = 1;
$url = API."?mode=getlistofjobcandidate&reportstartdate=".$reportstartdate."&reportenddate=".$reportenddate."&roleshortcode=".$_SESSION['role_ids'][0];
$apires = callAPI($url);
$result = json_decode($apires,1);
$stausresult = ViewStatusData();
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
function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
                $('#genrate').show();
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
                 $('#genrate').hide();
             }
         }
     }
 }
 function checkthebox(){
      $('#genrate').show();
 }
</script>
<input type="hidden" name="job_posting_id" id="job_posting_id" value="<?php echo $jobpost; ?>">
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
        <div class="card">
        <div class="card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5>Job Post Candidates</h5>
                <div class="card-header-right">
                    <button class="btn btn-primary" id="genrate" style="display:none;" type="button"> Submit to Genrate Offer</button>
                    <input type="hidden" id="job_posting_id_ajax" value="<?php echo $jobpost; ?>">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-body">
                <div class="dt-responsive table-responsive row">
                    <table id="new-cons" class="table table-striped table-bordered nowrap table-custom-width">

                        <thead>
                            <tr>

                                <th scope="col">
                                    <div class="checkbox-zoom zoom-primary mt-1">
                      <label class="form-label">
                          <input aria-label="Checkbox" type="checkbox"    name="" value= "" onclick="checkAll(this)" onkeydown="">
                          <span class="cr">
                               <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                           </span>
                       </label>
                  </div>
                </th>
                                <th scope="col">Job Post Name</th>
                                <th scope="col">Candidate Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Shortlist</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1; foreach ($result as $res) {
                                $candidate_table = $candidatelist[$res['candidate_id']];
                            ?>
                            <tr>

                                <td>
                                    <div class="checkbox-zoom zoom-primary mt-1">
                      <label class="form-label">
                          <input aria-label="Checkbox" type="checkbox"  onclick="checkthebox()"  name="genlist[<?php echo $res['id']; ?>]" value= "<?php echo $res['id']; ?>" onkeydown="">
                          <span class="cr">
                               <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                           </span>
                       </label>
                  </div>
                </td>
                                <td class="text-capitalize"><?php  echo $res['job_name']." - ".$res['job_number']; ?></td>
                                <td class="text-capitalize"><?php  echo $res['display_name']; ?></td>
                                <td><?php  echo $res['candidate_email']; ?></td>
                                <td><?php  echo $res['candidate_phone']; ?></td>
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
                                    <button class="btn btn-primary btn-sm btn-pd" style="color: <?php echo "#".$stausresult[$res['current_status']]['color']; ?>;    background:  <?php echo "#".strval( $stausresult[$res['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$res['current_status']]['background_color']); ?>;" type="button"><?php echo  $stausresult[$res['current_status']]['name']; ?> </button>
                                </td>
                                <td nowrap="nowrap" style="display: flex;">

                                    <button type="button" class="btn btn-sm btn-clean btn-icon" onclick="displaycandidateview(<?php echo $res['job_post_id']; ?>)" title="View"><i class="fa fa-eye"></i>    </button>
                            </td>
                        </tr>
                        <?php $k++;
} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
