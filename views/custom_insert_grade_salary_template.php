<?php
if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
        $url = API."?mode=addorupdategradedailysalary&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        if ($response) {
            header("location:index.php?option=COM_LIST_GRADE_SALARY_TEMPLATE");
        }
    }
if($_GET['Rid']!=""){
        $main_ids = encodeId($_GET['Rid']);
        $main_id = explode(":",$main_ids);
    $checkdata = array();
        $rid = $main_id['1'];
        $datawr["where"] = array('is_active'=> '0','id'=>$main_id['1']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=grade_salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
        if($salarydata['current_status']==ST_APPROVED){
            $disabled = "disabled";
        }
     $stpdata["where"] = array('is_active'=> '0','grade_salary_template_id'=>$salarydata['id']);
    $stpdata["order"] = "component_cat_id ASC";
    $stpmdata = urlencode(json_encode($stpdata));
    $stpurl = API."?mode=gettabledetailswithconditions&table=grade_salary_template_components&data=".$stpmdata;
    $salarytmpdata = callAPI($stpurl);
    $salarytmpdata = json_decode($salarytmpdata,1);
    foreach ($salarytmpdata as  $salarytmpda) {
      $checkdata[] = $salarytmpda['component_id'];
      $placevalue[$salarytmpda['component_id']] = $salarytmpda;
      $checkmainids[] =  $salarytmpda['component_category'];
    }
    }
$salary_type = $salarydata['salary_type']!="" ? $salarydata['salary_type'] : $_GET['ST'] ;
$pageurl = API."?mode=togetsalarycatdetaillist&type=".$salary_type ;
$data =  callAPI($pageurl);
$data = json_decode($data,1);
$check_count='Y';
if(count((array)$checkdata)>0){ $check_count = 'N';
}
foreach ($data as  $dat) {
        foreach ($dat['data'] as  $value){
            if($value['mandatory']==0){
                $mandatroy[] = $value['id'];
            }

            if($value['default_display']==0 && $check_count=='Y'){
                $checkdata[] = $value['id'];
            }
        }
    }

$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','employee_type'=>1);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$employees = json_decode($configesponse,1);
$disabled="";

$ptdata1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ptdata1["order"] = "job_grade_name ASC";
$ptdata = urlencode(json_encode($ptdata1));
$pturl = API."?mode=gettabledetailswithconditions&table=job_grade&data=".$ptdata;
$ptdata = callAPI($pturl);
$job_grade = json_decode($ptdata,1);

$eedata1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$eedata1["order"] = "id Desc";
$eedata1["limit"] = "0,1";
$eedata = urlencode(json_encode($eedata1));
$curl = API."?mode=gettabledetailswithconditions&table=emp_provident_funds&data=".$eedata;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$pfval = $esdata['employee_contribution'];
$emppfval = $esdata['employer_contribution'];
$curl = API."?mode=gettabledetailswithconditions&table=emp_state_insaurance&data=".$eedata;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$esival['employee_contribution'] = $esdata['employee_contribution'];
$esival['employer_contribution'] = $esdata['employer_contribution'];
$esival['check_value'] = '21000';

$ptdata1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ptdata1["order"] = "id Desc";
$ptdata1["limit"] = "0,1";
$ptdata = urlencode(json_encode($ptdata1));
$pturl = API."?mode=gettabledetailswithconditions&table=professional_tax&data=".$ptdata;
$ptdata = callAPI($pturl);
$ptdata = json_decode($ptdata,1);
$ptsalabs = $ptdata['0']['slabs'];

?>

<script type="text/javascript">
function changesalarytype(val){
            window.location.href = 'index.php?option=COM_INSERT_GRADE_SALARY_TEMPLATE&ST='+val;
        }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid ">
        <div class="card card-custom">
            <div class="card-body">
                <?php if($_SESSION['admin_type']==1){ ?>
                    <div class="d-flex flex-row">
                        <div class="flex-row-fluid ml-lg-8">
                            <div class="row salary-template col-lg-12 p-3">
                                <div class="page-header card" style="margin: 0px; color: black;">
                                    <div class="d-inline">
                                        <h5>Build Your Template</h5>
                                        <span class="sub-title">Include active salary components</span>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block mt-2" style="padding: 2px;">
                                    <div class="color-accordion" id="color-accordion">
                                       <?php $j=1; foreach ($data as  $dat) { ?>
                                           <div class="card">
                                            <a class="accordion-msg b-none waves-effect waves-light"> <?php echo $dat['name'];?></a></div>
                                            <div class="accordion-desc">
                                                <?php foreach ($dat['data'] as  $value) {
                                                    $sal_cat[$value['id']]= $value;
                                                    ?>
                                                    <div class="f-12 mt-3">
                                                       <?php $addsaldata = $value['id'];
                                                       echo $value['name']; ?>
                                                       <div class="checkbox-fade fade-in-primary float-right">
                                                        <label class="form-label ">
                                                            <input type="hidden" name="addsaldata" id="<?php echo $addsaldata."_detail"; ?>" value='<?php echo json_encode($value); ?>' >
                                                            <input type="hidden" id="<?php echo $addsaldata."check"; ?>" value="0">
                                                            <input aria-label="Checkbox" type="checkbox"   <?php if(in_array($addsaldata, (array)$checkdata)){ echo "checked";
} ?> name="Checkboxes1" onclick="addsaldata(<?php echo "'".$addsaldata."'"; ?>)" onkeydown="">
                                                            <span class="cr">
                                                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                          </span>

                                                          <span></span>
                                                      </label>
                                                  </div></div>
                                              <?php } ?>
                                          </div>
                                          <?php $j++;
} ?>

                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php $inn = "Basic salary Amount";?>
                          <div class="col-md-9">
                            <form class="form j-pro" method="post" action="">
                                <div class="card card-custom gutter-b">
                                    <div class="page-header card p-3" style="margin: 0px; color: black;">
                                        <div class="d-inline">
                                            <h5>Job Grad Salary</h5>
                                            <span>Define salary for Grad</span>
                                            <div class="float-right">
                                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                                    <i class="icofont icofont-reply"></i> Back
                                                </a>
                                                <?php if($_GET['Rid']!=""){ ?>
                                                    <a href="index.php?option=COM_VIEW_SALARY_PAYSLIP&Rid=<?php echo $_GET['Rid']; ?>" target="_blank" class="btn btn-primary">
                                                       Payslip
                                                   </a>
                                               <?php } ?>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <span>Salary Type</span>
                                            <select aria-label="Salary Type" class="form-select js-example-basic-single" onchange="changesalarytype(this.value)" name='salary_type'>
                                                <option value="0"> Select Any One</option>
                                                <option <?php if($salary_type=='M'){ echo "selected";
} ?> value="M">Montly</option>
                                                <option <?php if($salary_type=='D'){ echo "selected";
} ?> value="D">Daily</option>
                                           </select>
                                       </div>
                                       <div class="col-lg-4">
                                            <span>Job Grade </span>
                                            <select aria-label="Grade" class="form-select js-example-basic-single" name='grade_id'>
                                                <option value="0"> Select Any One</option>
                                                <?php foreach ($job_grade as $grade) { ?>
                                                   <option <?php if($salarydata['grade_id']==$grade['id']){ echo "selected";
} ?> value="<?php echo $grade['id'] ?>"> <?php echo $grade['job_grade_name'] ?></option>
                                               <?php } ?>

                                           </select>

                                       </div>
                                       <div class="col-lg-4 ">
                                            <span>Effective Date</span>
                                            <input aria-label="Affected Date" class="form-control datepicker hasDatepicker fill" type="date" name="affected_date" value="">
                                        </div>
                                       <div class="col-lg-4 ">
                                            <span>Description</span>
                                            <textarea aria-label="Kt Autosize 2" class="form-control" id="kt_autosize_2" ><?php echo $salarydata['description']; ?></textarea>
                                        </div>
                                    <div class="col-lg-4">
                                        <span>Notes</span>
                                        <textarea aria-label="Max 500 characters" class="form-control" id="kt_autosize_2_2" name="note" placeholder="Max 500 characters" ><?php echo $salarydata['note']; ?></textarea>
                                    </div>


                                </div>
                            </div>
                            <?php
                 if($salary_type=='D'){
                 include_once 'custom_insert_daily_wages.php';
                }else{
                  include_once 'custom_monthly_salary.php';
                }
                ?>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-4"></div>
                                    <div class="col-lg-8">
                                       <?php if($_GET['Rid']!=""){ ?>
                                        <input type="hidden"   name="check" value="update">
                                        <input type="hidden"   name="rid" value="<?php  echo $rid; ?>">
                                    <?php } ?>
                                    <input type="hidden"   name="submit" value="submit">
                                    <button type="submit" <?php echo $disabled; ?> class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog"  tabindex="-1" aria-hidden="true"  role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content" >

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"  id="popup1">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect waves-light " data-bs-dismiss="modal" aria-label="Close">Close</button>

            </div>
        </div>
    </div>
</div>
<?php }else{
    $main_ids = encodeId($_GET['Rid']);
    $main_id = explode(":",$main_ids);
    $datawr["where"] = array('is_active'=> '0','id'=>$main_id['1']);
    $costdata = urlencode(json_encode($datawr));
    $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
    $salarydata = callAPI($url);
    $salarydata = json_decode($salarydata,1);
    $salarydata = $salarydata[0];
    $stpdata["where"] = array('is_active'=> '0','salary_template_id'=>$salarydata['id']);
    $stpdata["order"] = "component_cat_id ASC";
    $stpmdata = urlencode(json_encode($stpdata));
    $stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
    $salarytmpdata = callAPI($stpurl);
    $salarytmpdata = json_decode($salarytmpdata,1);
    $datacat["where"] = array('is_active'=> '0');
    $catdata = urlencode(json_encode($datacat));
    $caturl = API."?mode=gettabledetailswithconditions&table=salary_categories&data=".$catdata;
    $salarycatdata = callAPI($caturl);
    $salarycatdata = json_decode($salarycatdata,1);
    foreach ($salarycatdata as $cate) {
        $cat[$cate['id']] = $cate['name'];
    }

    foreach ($employees as $employee) {
        $empdata[$employee['id']] = $employee['display_name'];
    }
    ?>

    <div class="card card-custom " id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">&nbsp;<?php echo " Salaray Template";  ?> </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="row align-items-center" style="margin-top: 10px;padding: 0px 10%;" id="printarea">

                    <div  class="col-lg-12" style="page-break-before: always;">
                        <table style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px; margin-left: auto; margin-right: auto;" role="presentation">
                            <tr style="height: 50px;border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;"><b>Name</b></td>
                                <td  style="border:1px solid #000;padding: 5px;"colspan="2">&nbsp;<?php echo  $empdata[$salarydata['employee_id']]; ?></td>
                            </tr>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;"><b>COMPENSATION DETAILS</b>             </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>MONTHLY</b>       </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>YEARLY</b>    </td>
                            </tr>
                            <?php $nm=0; foreach ($salarytmpdata as  $stvalue) { ?>
                                <?php if($stvalue['component_cat_id'] !=$nm ){ $nm =$stvalue['component_cat_id'];  ?>
                                <tr style="border: 1px solid #000;" >
                                    <td style="font-size: 14px;border:1px solid #000;padding: 5px;">&nbsp;  <?php echo $cat[$nm];?></td>
                                    <td style="border:1px solid #000;padding: 5px;"> </td>
                                    <td style="border:1px solid #000;padding: 5px;">  </td>
                                </tr>
                            <?php } ?>
                            <tr style="border: 1px solid #000;" >
                                <td style="border:1px solid #000;padding: 5px;" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $stvalue['component_name'];?></td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($stvalue['monthly_amount']);?></td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($stvalue['yearly_amount']);?></td>
                            </tr>
                        <?php } ?>
                        <tr style="border: 1px solid #000;" >
                            <td style="border:1px solid #000;padding: 5px;" ><?php echo "Total";?></td>
                            <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($salarydata['company_cost_monthly']);?></td>
                            <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($salarydata['company_cost_yearly']);?></td>
                        </tr>
                        <tr style="border: 1px solid #000;" >
                            <td colspan="3" style="border:1px solid #000;padding: 5px;">&nbsp;  Notes: </br>
                                <p style="padding: 0px 5%;"><?php echo  $salarydata['note']; ?></p>
                            </td>

                        </tr>
                    </table>
                </div>
                <div style="width: 50%; display: inline-block; margin: 30px 0 0 0;">
                    <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">With best wishes.<br />For <?php echo  $send_data['company_name']; ?></b>
                </div>
                <div style="width: 48%; display: inline-block; margin: 30px 0 0 0; text-align: right;">
                    <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">ANJANA AUDUKOORI<br />DIRECTOR HR</b>
                </div>
                <div style="width: 50%; display: inline-block; margin: 40px 0 0 0;">
                    <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
                    <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Candidate Signature</b>
                </div>
                <div style="width: 48%; display: inline-block; margin: 40px 0 0 0; text-align: right;">
                    <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
                    <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Date</b>
                </div>

            </div>

        </div>
    </div>
</div>
</div>

<?php } ?>
