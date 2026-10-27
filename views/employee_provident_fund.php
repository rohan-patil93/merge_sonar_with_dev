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
$curl = API."?mode=gettabledetailswithconditions&table=emp_provident_funds&data=".$data;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$dcarray = array('M'=>'Month','Y'=>'Year');

$data1q["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ssdata = urlencode(json_encode($data1q));
$csuurl = API."?mode=gettabledetailswithconditions&table=emp_state_sub_insaurance&data=".$ssdata;
$esudata = callAPI($csuurl);
$esudata = json_decode($esudata,1);
?>
<div class="card card-custom gutter-b">


<div class="card-header">
    <h5>Employee's Provident Fund(EPF)</h5>
    <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
    <div class="card-header-right">
           <button type="button" onclick="dynamiceditpopup('COM_INSERT_EMP_PROVIDENT_FUNDS',<?php echo "'".deocdeId("id:".$esdata['id'])."'" ?>)" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"> <i class="fa fa-edit text-white"></i>   </button>
    </div>
</div>

                                                    <!-- Modal-->


                                                <div class="card-body">
                                                    <div class="col-md-6">
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">PF Number</span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg"><?php echo $esdata['epf_number']; ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">Deductions Cycle</span>
                                                        <span class="f-w-700 font-weight-bold float-right "><?php echo $dcarray[$esdata['deduction_cycle']]; ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted ">Employee's Contribution</span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg"><?php echo $esdata['employee_contribution']; ?>% of Gross Pay</span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">Employer's Contribution</span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg"><?php echo $esdata['employer_contribution']; ?>% of Gross Pay</span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">Other Details</span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg">Employer's contribution is <?php if($esdata['employer_contribution_in_ctc']!=0){ echo "not";
} ?> included in the CTC</span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted ">Pro-rate Restricted PF Wage</span>
                                                        <span class=" f-w-700 font-weight-bold float-right font-size-lg"><?php if($esdata['pro_rate_restricted']!=0){ echo "NO";
}else{ echo "YES";
} ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">Allow Override</span>
                                                        <span class="f-w-700 font-weight-bold float-right font-size-lg"><?php if($esdata['allow_override']!=0){ echo "NO";
}else{ echo "YES";
} ?></span>
                                                        <hr>
                                                        <span class="font-size-lg font-weight-bold text-muted">Eraned PF Calucation</span>
                                                        <span class=" f-w-700 font-weight-bold float-right font-size-lg"><?php if($esdata['earned_pf_calculation']!=0){ echo "NO";
}else{ echo "YES";
} ?></span>
                                                    </div>

                                                <!--end::Header-->
                                                <!--begin::Body-->
                                                <div class=" col-md-12 mt-12 table-responsive">

<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Sub Components</th>
            <th scope="col">Employee's Contribution</th>
            <th scope="col">Employer's Contribution</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach ($esudata as  $esub) { ?>
        <tr>
            <th scope="row"><?php echo $i; ?></th>
            <td><?php echo $esub['component_name']; ?></td>

            <td><?php if($esub['employee_contribution']!=0) { echo $esub['employee_contribution']."% PF Wage";
}else{ echo "NA";
} ?> </td>
            <td><?php if($esub['employer_contribution']!=0) { echo $esub['employer_contribution']."% PF Wage";
}else{ echo "NA";
} ?></td>
        </tr>
    <?php $i++;
} ?>
    </tbody>
</table>
                                                </div>
                                            </div>
                                            </div>

