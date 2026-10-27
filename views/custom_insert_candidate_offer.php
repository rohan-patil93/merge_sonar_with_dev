<?php
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$_SESSION['apply_js']['multiselest'] = 1;

$datawr1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0','id'=>$candidate_id);
$costdata1 = urlencode(json_encode($datawr1));
$url1 = API."?mode=gettabledetailswithconditions&table=candidates&data=".$costdata1;
$salarydata1 = callAPI($url1);
$data = json_decode($salarydata1,1);
$data = $data[0];

$datawr["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0','candidate_id'=>$candidate_id);
$costdata = urlencode(json_encode($datawr));
$url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
$salarydata = callAPI($url);
$salarydata = json_decode($salarydata,1);
$salarydata = $salarydata[0];
$salary_temp_id =$salarydata['id'];
$_GET['SCid'] = $candidate_id;
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="">
            <?php  include_once 'candidate_side_nav.php'; ?>
            <?php $pay_template= $data['pay_template'];
            if($pay_template == 0 && $salary_temp_id =='' && $_SESSION['config']['salary_grade_define'] ==0){
            ?>
        <div class="">
            <?php
  $data2["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0');
    $costdata = urlencode(json_encode($data2));
  $costurl = API."?mode=gettabledetailswithconditions&table=job_grade&data=".$costdata;
   $costdata = callAPI($costurl);
   $grade = json_decode($costdata,1);

  $datapaytmp["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=> '0','grade_id'=>$role['job_grade']);
  $datapaytmpd = urlencode(json_encode($datapaytmp));
  $tempurl = API."?mode=gettabledetailswithconditions&table=job_grade_templates&data=".$datapaytmpd;
  $tempdata = callAPI($tempurl);
  $tempdata = json_decode($tempdata,1);
            ?>
<script type="text/javascript">
function gradechange(val){
    $.ajax({
        url: "ajax.php",
        data:'page=dynmictabledata&table=job_grade_templates&field=id&value=name&rid=grade_id&fid='+val,
        type: "POST",
        success:function(data){
        console.log(data);

        $("#grade").html("<select aria-label='Pay Template' class='form-control' name='pay_template'>"+data+"</select>");
        },
        error:function (){}
    });
}
</script>

            <div class="row ">
                <div class="col-lg-12 ">
                <form class="kt-form" id="submitcandidatesoffer" method="post" action="" name="" enctype="multipart/form-data">
                    <div class="card " id="kt_page_sticky_card">
                        <div class="card-header">
    <h5><?php echo "Generate LoI";  ?></h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
        </div>
</div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-3 form-group">
                                    <span class="form-label d-block">Job Grad</span>
                                     <span class="d-block error-message text-danger"></span>
                                    <select aria-label="Job Grade" class="form-select js-example-basic-single requried_field" name="job_grade" id="job_grade" onchange="gradechange(this.value)">
                                        <option value=""> Select Any One</option>
                                        <?php foreach ($grade as  $gra) { ?>
                                        <option  <?php if($data['job_grade'] == $gra['id'] ){  ?> selected <?php
} ?> value="<?php echo $gra['id']; ?>"><?php echo $gra['job_grade_name']; ?></option>
                                        <?php  } ?>
                                    </select>
                                    <div style="display: none;color: red;" id="expected_ctc_err"> Please enter Expected Ctc </div>
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Pay template </span>
                                     <span class="d-block error-message text-danger"></span>
                                    <select aria-label="Pay Template" class="form-select js-example-basic-single requried_field" name="pay_template" id="grade">
                                        <option value=""> Select Any One</option>

                                        <?php foreach ($tempdata as  $temp) { ?>
                                            <option  <?php if($data['pay_template'] == $temp['id'] ){  ?> selected <?php
} ?> value="<?php echo $temp['id']; ?>"><?php echo $temp['name']; ?></option>
                                        <?php  } ?>
                                    </select>
                                    <div style="display: none;color: red;" id="expected_ctc_err_2"> Please enter Pay template </div>
                                </div>
                                <div class="form-group col-lg-2" >

                                    <span class="form-label">Expected CTC <span class="text-danger">*</span></span>
                                     <span class="d-block error-message text-danger"></span>
                                     <input aria-label="Input field" type="text" class="form-control text-right requried_field"  value="<?php echo $data['expected_ctc']; ?>"  name="expected_ctc" id="expected_ctc"  />
                                </div>

                                <?php
                                 $reporting_date = fromDate($role['reporting_date']);
                                $offer_validity = fromDate($role['offer_validity']);
                                ?>
                                <div class="form-group col-lg-2">
                                    <span class="form-label">Joining Date<span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                       <input aria-label="Input field" type="text" class="form-control datepicker requried_field"  value="<?php echo $reporting_date; ?>"  name="reporting_date"  id="kt_datepicker_4_1" />

                                </div>
                                <div class="form-group col-lg-2" >
                                      <span class="form-label">LoI validity<span class="text-danger">*</span></span>
                                      <span class="d-block error-message text-danger"></span>
                                      <input aria-label="Input field" type="text" class="form-control datepicker requried_field"  value="<?php echo $offer_validity; ?>"  name="offer_validity"  id="kt_datepicker_4_2" />
                                </div>
                            </div>
                             <div class="row">
                            <div class="col-lg-4">
                                <input type="hidden" name="candidate_id" value="<?php echo $data['id'];  ?>" />
                                <input type="hidden" name="insertcandidateoffer" value="submit" />
                                <?php if($data['hm_id'] =="" || $data['rm_id'] ==""){ $disablebt = "disabled"; echo "<span class='text-danger d-block'>Hiring Manager Or Recruitment Manager Please Defind First?</span>";
} ?>
                                 <button id="" class="btn btn-primary mr-2 <?php echo $disabled; ?>"  onclick="formSubmitValidation('submitcandidatesoffer','exit')" type="button"  name="submitto"><i class="la la-check"></i>Save</button>
                            </div>
                        </div>
                        </div>

                    </div>

                </form>
                </div>
            </div>
        </div>
        </div>
    </div>
        <?php }else{
            if($data['salary_template_id'] !=""){
                $_GET['SCid'] = $candidate_id;
                $_GET['VIEW'] ='1';
            }
            include_once 'custom_insert_salary.php';
        } ?>
    </div>
    </div>
</div>
