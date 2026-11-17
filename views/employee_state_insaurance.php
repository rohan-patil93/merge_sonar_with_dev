<?php
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$accord = json_decode($list['additional_attributes'],1);
$table_name = $list['application_table_name'];

$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$data1["order"] = "id Desc";
$data1["limit"] = "0,1";
$data = urlencode(json_encode($data1));
$curl = API."?mode=gettabledetailswithconditions&table=emp_state_insaurance&data=".$data;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$dcarray = array('M'=>'Month','Y'=>'Year');

?>
<div class="card card-custom gutter-b">
                                                <!--begin::Header-->

<div class="card-header">
    <h5><?= __('Employee\'s State Insaurance(ESI)'); ?></h5>
    <div class="card-header-right">
          <button type="button" onclick="dynamiceditpopup('COM_INSERT_EMP_STATE_INSAURANCE',<?php echo "'".deocdeId("id:".$esdata['id'])."'" ?>)" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"> <i class="fa fa-edit text-white"></i>   </button>
    </div>
</div>

                                                    <!-- Modal-->


                                                <div class="card-body">
                                                    <div class="col-md-6">
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted"><?= __('ESI Number'); ?></span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg"><?php echo renderSensitiveDataWithToggle($esdata['esi_no'], 'pf_number'); ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted"><?= __('Deductions Cycle'); ?></span>
                                                        <span class="f-w-700 font-weight-bold float-right "><?php echo $dcarray[$esdata['deduction_cycle']]; ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted "><?= __('Employee\'s Contribution'); ?></span>
                                                        <span class=" f-w-700 font-weight-bold float-right font-size-lg"><?php echo $esdata['employee_contribution']; ?><?= __('% of Gross Pay'); ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted"><?= __('Employer\'s Contribution'); ?></span>
                                                        <span class=" f-w-700 font-weight-bold float-right font-size-lg"><?php echo $esdata['employer_contribution']; ?><?= __('% of Gross Pay'); ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted"><?= __('Other Details'); ?></span>
                                                        <span class=" f-w-700 font-weight-bold float-right font-size-lg"><?= __('Employer\'s contribution is'); ?> <?php if($esdata['employer_contribution_in_ctc']!=0){ echo "not";
} ?> <?= __('included in the CTC'); ?></span>
                                                    </div>

                                                <!--end::Header-->
                                                <!--begin::Body-->

                                            </div>
                                            </div>

