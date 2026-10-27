<?php
if(isset($_POST['submit'])){

   $_POST['company_id'] = $_SESSION['company_id'];
   $_POST['current_status'] = 0;
   $_POST['is_active'] = 0;
   $_POST['status_date'] = nowDate();
   $_POST['from_date'] = dateFormate($_POST['from_date']);
   $_POST['to_date'] = dateFormate($_POST['to_date']);
   $_POST['trial_period_duration_from']  = date('Y-m-d',strtotime($_POST['trial_period_duration_from']));
   $_POST['trial_period_duration_to']  = date('Y-m-d',strtotime($_POST['trial_period_duration_to']));
   $_POST['duration_from']  = date('Y-m-d',strtotime($_POST['duration_from']));
   $_POST['duration_to']  = date('Y-m-d',strtotime($_POST['duration_to']));
   $_POST['trial_period_duration_from']  = date('Y-m-d',strtotime($_POST['trial_period_duration_from']));
   $_POST['visa_expire']  = date('Y-m-d',strtotime($_POST['visa_expire']));


   if($_POST['submit']=='Add'){
      unset($_POST['submit']);unset($_POST['submit_value']);
      $_POST['status_date'] = nowDate();
      $_POST['created_date'] = nowDate();
      $_POST['created_by'] = $_SESSION['userid'];
      $url = API."?mode=adddata&table=emp_contracts&method=post";
      $response = file_post_contents($url, $_POST, 'insert');
   }
   if($_POST['submit'] == 'Update'){
      unset($_POST['submit']);unset($_POST['submit_value']);
      $_SESSION['check'] = 1;
      $data['updated_date'] = nowDate();
      $data['updated_by'] = $_SESSION['userid'];
      $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=emp_contracts&method=post";
      unset($_POST['hid']);
      $response = file_post_contents($url, $_POST, 'update');
   }
}
if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $data['id']==""){
   $vdata_details = array();
   $rid =  encodeId($_GET['Rid']);
   $rid = urldecode($rid);
   $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=emp_contracts";
   $response = callAPI($url);
   $role = json_decode($response,1);
}
if($data['id']!=""){
        $vdata1["where"] = array('candidate_id'=> $data['id']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=emp_contracts&data=".$vdata;
    $configesponse = callAPI($vurl);
    $econ = json_decode($configesponse,1);
   $econ = $econ['0'];
   $rid = $econ['id']!=""? "id:".$econ['id'] : "";
   $role = $econ;
}

$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_contract_types&data=".$vdata;
$configesponse = callAPI($vurl);
$contract_types = json_decode($configesponse,1);

$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$employees = json_decode($configesponse,1);

$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_working_schedules&data=".$vdata;
$configesponse = callAPI($vurl);
$working_schedules = json_decode($configesponse,1);

$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$vdata;
$configesponse = callAPI($vurl);
$job_titles = json_decode($configesponse,1);

?>
<div class="d-flex flex-column-fluid j-wrapper">
   <div class="container-fluid j-pro">
   <div class=" flex-row j-content">
      <div class="card card-custom card-sticky" id="kt_page_sticky_card">


         <div class="card-header">
    <h5>Contract</h5>

    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>


      <div class=" card-custom">
         <div class="card-body">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
            <div class="col-lg-12">
               <div class="form-group row mt-3">

                   <span class="col-lg-2 col-form-label text-lg-right">Job Title:</span>
                  <div class="col-lg-3">
                     <select aria-label="Job Title" class="form-control form-select js-example-basic-single" name="job_title">
                        <option> Select Any One</option>
                        <?php foreach($job_titles as $job_title){ ?>
                           <option <?php if($role['job_title'] == $job_title['id']){ ?> selected <?php
} ?> value="<?php echo $job_title['id'] ?>"><?php echo $job_title['job_title'] ?></option>
                        <?php } ?>
                     </select>
                  </div>
                  <span class="col-lg-2 col-form-label text-lg-right">Contract Type:</span>
                  <div class="col-lg-3">
                     <select aria-label="Contract Type" class="form-control form-select js-example-basic-single" name="contract_type">
                        <option> Select Any One</option>
                        <?php foreach($contract_types as $contract_type){ ?>
                           <option <?php if($role['contract_type'] == $contract_type['id']){ ?> selected <?php
} ?> value="<?php echo $contract_type['id'] ?>"><?php echo $contract_type['name'] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>


         <div class="form-group ">
            <div class="example-preview">
               <ul class="nav md-tabs" id="myTab1">
                  <li class="nav-item">
                     <a class="nav-link active" id="home-tab-1" data-bs-toggle="tab" href="#home-1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-chat-1"></i>
                        </span>
                        <span class="nav-text">Information</span>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="profile-tab-1" data-bs-toggle="tab" href="#profile-1" aria-controls="profile" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-console"></i>
                        </span>
                        <span class="nav-text">Work Permit</span>
                     </a>
                  </li>

               </ul>
               <div class="tab-content mt-5" id="myTabContent1">
                  <div class="tab-pane fade show active " id="home-1" role="tabpanel" aria-labelledby="home-tab-1">
                     <div class="col-lg-12 row mt-3">
                        <div class="col-lg-6">
                           <h3> Salary and Advantages</h3>
                           <div class="form-group row mt-3">
                              <span class="form-label text-lg-left">Wage:</span>
                              <div class="">
                               <input aria-label="Wage" type="text" class="form-control" placeholder="" name="wage" value="<?php echo $role['wage']; ?>" />
                              </div>
                           </div>
                           <div class="form-group row">
                              <div class="col-lg-12">
                               <span class="form-label">Advantages:</span>
                                    <textarea aria-label="Advantages" class="form-control" rows="7" name="advantages"><?php echo $role['advantages']; ?></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <h3> Duration</h3>
                           <div class="form-group row mt-3">
                              <span class="form-label">Trial Period Duration:</span>
                              <div class="col-lg-5">
                                 <?php $date_pic = fromDate($role['trial_period_duration_from']); ?>
                                 <input aria-label="Trial Period Duration From" type="date" class="form-control" name="trial_period_duration_from" value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_31" />
                              </div>
                              <div class="col-lg-2 mt-3 form-label text-center">TO</div>
                              <div class="col-lg-5">

                                    <?php $date_pic = fromDate($role['trial_period_duration_to']); ?>
                                    <input aria-label="Trial Period Duration To" type="date" class="form-control" name="trial_period_duration_to"  value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_32" />


                              </div>
                           </div>
                           <div class="form-group row mt-3">
                              <span class="form-label">Duration:</span>
                              <div class="col-lg-5">

                                    <?php $date_pic = fromDate($role['duration_from']); ?>
                                    <input aria-label="Duration From" type="date" class="form-control" name="duration_from" value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_33" />

                              </div>
                              <div class="col-lg-2 mt-3 form-label text-center">TO</div>
                              <div class="col-lg-5">

                                    <?php $date_pic = fromDate($role['duration_to']); ?>
                                    <input aria-label="Duration To" type="date" class="form-control" name="duration_to" value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_34" />


                              </div>
                           </div>
                           <div class="form-group row mt-3">
                              <span class="col-lg-4 col-form-label text-lg-left">Working Schedules:</span>
                              <div class="col-lg-8">
                                 <select aria-label="Working Schedule" class="form-control form-select js-example-basic-single" name="working_schedule">
                                    <option> Select Any One</option>
                                    <?php foreach($working_schedules as $working_schedule){ ?>
                                       <option <?php if($role['working_schedule'] == $working_schedule['id']){ ?> selected <?php
} ?> value="<?php echo $working_schedule['id'] ?>"><?php echo $working_schedule['name'] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="profile-1" role="tabpanel" aria-labelledby="profile-tab-1">
                     <div class="col-lg-12">
                        <div class="form-group row mt-3">
                           <span class="col-lg-2 col-form-label text-lg-right">Visa No:</span>
                           <div class="col-lg-3">
                            <input aria-label="Visa No" type="text" class="form-control" name="visa_no" placeholder="" value="<?php echo $role['visa_no']; ?>" />
                           </div>
                           <span class="col-lg-2 col-form-label text-lg-right">Work Permit No:</span>
                           <div class="col-lg-3">
                            <input aria-label="Work Permit No" type="text" class="form-control" name="work_permit_no" value="<?php echo $role['work_permit_no']; ?>" />
                           </div>
                        </div>
                        <div class="form-group row mt-3">
                           <span class="col-lg-2 col-form-label text-lg-right">Visa Expire :</span>
                           <div class="col-lg-3">
                                 <div class="input-group date">
                                    <?php $date_pic = fromDate($role['visa_expire']); ?>
                                    <input aria-label="Visa Expire" type="date" class="form-control" name="visa_expire" value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_34_2" />

                                 </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="profile-tab-3">
                     Conten 3
                  </div>
               </div>
            </div>
          </div>
         </div>
      </div>
      <div class="card-footer">
         <div class="row">
            <div class="col-lg-9">

               <?php if($rid!= ''){ ?>
                  <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                  <input type="hidden" name="submit" value="Update" />

                  <button id="kt_llogin_signup_submit" <?php if($status_res == '1'){ ?> disabled="disabled" <?php
} ?> class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
               <?php }else{ ?>
               <input type="hidden" name="vendor_id" value="<?php echo $data['vendor_id'] ?>" />
                  <input type="hidden" name="candidate_id" value="<?php echo $data['id'] ?>" />
               <input type="hidden" name="submit" value="Add" />
               <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save</button>
               <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
               <?php } ?>
            </div>
            <div class="col-lg-3 text-right">
               <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
            </div>
         </div>
      </div>

   </div>
   </form>
</div></div></div>
