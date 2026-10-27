<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $candidate_id = $_POST['cand_id'];
    $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$candidate_id);
    $wh1['field'] = "id,first_name,last_name,email_id,phone";
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=candidates&data=".$data1;
    $data_details1 = callAPI($url1);
    $json = json_decode($data_details1,1);
    $result = $json[0];


    $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'job_status'=>'0');

    $wh1['field'] = "id,job_number,job_name,requested_by,recruitment_manager";
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$data1;
    $data_details11 = callAPI($url1);
    $json2 = json_decode($data_details11,1);
    foreach($json2 as $json222){
        if (in_array(HIRINGMANAGER_ROLE,(array)$_SESSION['role_ids']) && $json222['requested_by'] == $_SESSION['userid']) {
                $job_postingslist[] = $json222;
            }
        if (in_array(RECRUITINGMANAGER_ROLE,(array)$_SESSION['role_ids']) && $json222['recruitment_manager'] == $_SESSION['userid']) {
                $job_postingslist[] = $json222;
            }
        $job_postings[$json222['id']] = $json222['job_name']."-".$json222['job_number'];
    }

    $wh3["where"] = array('company_id'=>$_SESSION['company_id'],'candidate_id'=>$candidate_id);
    $wh3['field'] = "id,job_posting_id,shortlisted,hm_shortlisted,is_active";
    $data3 = urlencode(json_encode($wh3));
    $url3 = API."?mode=gettabledetailswithconditions&table=job_posting_candidates&data=".$data3;
    $data_details3 = callAPI($url3);
    $result3 = json_decode($data_details3,1);

?>
<script type="text/javascript">
    function submitassigndetails(){
        var validations = ajaxFormSubmitValidation();
           if(validations == 0){
             var postarray = $("#candidatesform").serialize();
             $.ajax({
                url: "ajax.php",
                data: "page=assignecandidatejobpostingpopup&"+postarray,
                type: "POST",
                success:function(data){
                    $('#kt_modal_dynamic_popup2').modal("hide");
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    location.reload();
                },
                error:function (){}
            });
        }
    }
    function showAlreadyRecordActive(){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: '<?php echo $_SESSION['error_message']['9047'];?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if(isConfirm) {
                submitassigndetails();
            }
        });

        // Swal.fire({
        //   showDenyButton: true,
        //   showCancelButton: false,
        //   confirmButtonText: 'Ok',
        //   denyButtonText: `No`,
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Assign Job Postings</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body ">

  <div class="col-lg-12">
  <div class="card">
  <table id="new-cons" class="table table-bordered table-xs">
    <thead>
      <tr><th scope="col">#</th><th scope="col">Job Posting</th><th scope="col">HR ShortListed</th><th scope="col">HM ShortListed</th></tr>
    </thead>
    <tbody>
      <?php $activejobpost = "N"; $mk=1; $jobpostingids= array(); if(count((array)$result3) !=0){  foreach($result3 as $resulttable){
          $jobpostingids[] = $resulttable['job_posting_id'];
          if($resulttable['is_active'] == 0){
               $activejobpost = "Y";
          }
          $hm_shortlisted = "";
          if($resulttable['hm_shortlisted'] == ""){ $hm_shortlisted  = "Pending";
}elseif($resulttable['hm_shortlisted'] == "0"){ $hm_shortlisted  = "Short List";
}elseif($resulttable['hm_shortlisted'] == "1"){ $hm_shortlisted  = "Rejected";
}
          $shortlisted = "";
          if($resulttable['shortlisted'] == ""){ $shortlisted  = "Pending";
}elseif($resulttable['shortlisted'] == "0"){ $shortlisted  = "Short List";
}elseif($resulttable['shortlisted'] == "1"){ $shortlisted  = "Rejected";
}
        ?>
      <tr class="<?php echo $resulttable['is_active'] !="0"?"text-danger":""; ?>">
        <td><?php echo $mk; ?></td>
        <td><?php echo $job_postings[$resulttable['job_posting_id']]; ?></td>
        <td><?php echo $shortlisted; ?></td>
        <td><?php echo $hm_shortlisted; ?></td>
      </tr>
     <?php $mk++;
}
}else{ echo "<tr><td colspan='4' class='text-center'>No Apply Any job Post</td></tr>";
} ?>
    </tbody>
  </table>
</div>
</div>
    <form id="candidatesform" method="post" >
        <div class="row ">
            <div class="form-group col-lg-3 w-full">
                <span class="form-label">Job Posting <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <select aria-label="Job Posting" class="form-control js-example-basic-single requried_field" name="job_posting_id" >
                    <option value="">Select Any One</option>
              <?php foreach($job_postingslist as $res){ if(!in_array($res['id'], (array)$job_postings)){ ?>
                <option value="<?php echo $res['id']; ?>" <?php echo $_POST['job_posting_id'] == $res['id']?"selected":"";?>><?php echo $res['job_name']."- ".$res['job_number']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            </div>
            <div class="form-group col-lg-2">
                <span class="form-label">First Name <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="First Name" class="form-control notemptyclass text-capitalize requried_field"  type="text" name="first_name"  value="<?php echo $result['first_name']; ?>" readonly="">
            </div>
            <div class="form-group col-lg-2">
                <span class="form-label">Last Name <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="Last Name" class="form-control notemptyclass text-capitalize requried_field"  type="text"  name="last_name" value="<?php echo $result['last_name']; ?>" readonly="">
            </div>
            <div class="form-group col-lg-2">
                <span class="form-label">Phone <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="Phone" class="form-control notemptyclass requried_field"  type="number"  name="phone" value="<?php echo $result['phone']; ?>" readonly="">
            </div>
            <div class="form-group col-lg-3">
                <span class="form-label">Email Id <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="Email" class="form-control notemptyclass requried_field"  type="email"  name="email_id" value="<?php echo $result['email_id']; ?>" readonly="">
            </div>

         </div>
         <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>">
     </form>
</div>
<div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" <?php if($activejobpost == "Y"){ ?> onclick="showAlreadyRecordActive()" <?php
}else{ ?> onclick="submitassigndetails()" <?php
} ?>>Submit</button>
</div>
