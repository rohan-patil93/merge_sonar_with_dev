<?php
    if(isset($_POST['applyform'])) {
        $filter_table_rowid = $_POST['filtertbl_rowid'];
         $actuallist = array();
        $estimationlist = array();
        $vurl = API."?mode=customadmdashboardmodule3&company_id=".$_SESSION['company_id']."&filterid=".$filter_table_rowid."&fromdate=".$_POST['from_date']."&todate=".$_POST['to_date'];
       $configesponse = callAPI($vurl);
        $mcats = json_decode($configesponse,1);
        $from_cur_year = date('Y', strtotime($_POST['from_date']));
        $from_cur_month = date('m', strtotime($_POST['from_date']));

        $to_cur_year = date('Y', strtotime($_POST['to_date']));
        $to_cur_month = date('m', strtotime($_POST['to_date']));
        $defalut_fields = $_POST['displayattributes'];
    }else{
     $vurl = API."?mode=customadmdashboardmodule3";
    $configesponse = callAPI($vurl);
    $mcats = json_decode($configesponse,1);
    }
    foreach($mcats as $mkey=>$mca){
        $maingra['months'][]=date('M',strtotime($mkey));
        $budget = 0; $bilable =0;
        foreach($mca as $madmin){
            $vendor_admin[$madmin['vendor_id']][$mkey][] = $madmin;
            $cc_admin[$madmin['cc_id']][$mkey][] = $madmin;
            $vertical_admin[$madmin['vertical_id']][] = $madmin;
            $department_admin[$madmin['department_id']][] = $madmin;
            $budget =$budget+$madmin['budgeted_amount'];
            $bilable =$bilable+$madmin['billable_amount'];
        }
        $maingra['bdget'][]=$budget;
        $maingra['billable'][]=$bilable;
    }
    foreach($vertical_admin as $vkey=>$vertical_a){
        foreach($vertical_a as $vertical_ad){
            $name = $vertical_ad['vertical_name'];
            $vbdget = $vbdget+$vertical_ad['budgeted_amount'];
            $vbillable = $vbillable+$vertical_ad['billable_amount'];
        }
        $vtdetail['name'][] = $name;
        $vtdetail['bdget'][] = $vbdget;
    }
    $employees_fieldlist = array('employee_name'=>'Employee Name','vendor_name'=>'Vendor','vertical_name'=>'Vertical','cost_center_name'=>'Cost Center','department_name'=>'Department','billable_hrs'=>'Billable Hours','nonbillable_hrs'=>'Nonbillable Hours','leave_days_from_to'=>'Leave','budgeted_amount'=>'Budgeted Amount','billable_amount'=>'Billable Amount','nonbillable_amount'=>'Nonbillable Amount','exp'=>'Experiences','job_title'=>'Job Title');
    if(count((array)$defalut_fields)<1){
        $defalut_fields = array('employee_name','vertical_name','vendor_name','cost_center_name','job_title','budgeted_amount','billable_amount');
    }
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="">
            <form class="kt-form" id="kt_form" method="post" action="" >

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom " id="kt_page_sticky_card">
                            <div class="card-header">
                                <div class="card-title col-lg-9">
                                    <h3 class="card-label col-lg-3">&nbsp;Vertical Wise Billing </h3>
                                    From  <div class="col-lg-3">
                                            <input aria-label="From Date" class="form-control" type="month" name="from_date" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year" />
                                            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
                                    </div>

                                    To  <div class="col-lg-3">
                                          <input aria-label="To Date" class="form-control" type="month" name="to_date" value="<?php echo $to_cur_year."-".$to_cur_month?>"  id="att_month_year_2" />
                                    </div>
                                    <div class="col-lg-3">
                                        <a href="#" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</a>
                                    </div>
                                    <div class="col-lg-2">
                                        <select aria-label="Displayattributes[]" class="form-control kt-select2" id="kt_select2_31" multiple="multiple" name="displayattributes[]" >

                                            <?php foreach ($employees_fieldlist as $fkey => $fieldvalue) { ?>
                                                <option value="<?php echo $fkey; ?>" <?php echo in_array($fkey, $defalut_fields) ? 'selected' : ''; ?>  > <?php echo $fieldvalue; ?> </option>
                                            <?php }    ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <input type="hidden" id="filterby" name="filterby">
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="vname" id="vname" value="<?php echo implode("|",(array)$vtdetail['name']);?>">
                                    <input type="hidden" name="vvalue" id="vvalue" value="<?php echo implode("|",(array)$vtdetail['bdget']);?>">
                                    <div class="card-body">
                                        <!--begin::Chart-->
                                        <div id="admin_dash_chart2" class="d-flex justify-content-center"></div>
                                        <!--end::Chart-->
                                    </div>
                                </div>
                                <!--end::Chart-->

                            </div>
                            <!--end::Charts Widget 2-->
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="card card-custom col-lg-12">
            <div class="card-body">
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                                        if(in_array($fkey,$defalut_fields)){
                                    ?>
                                    <th scope="col"><?php echo $fkey; ?> </th>
                                <?php } ?>
                                <?php } ?>
                                </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialno = 1;
                            foreach($mcats as $mtats){
                            foreach ($mtats as $budgetkey => $budget) {

                                $utilizedbudget = $budget['billable_amount'] != '' ? $budget['billable_amount'] : 0;
                                $estimatedbudget = $budget['budgeted_amount'];
                                if($utilizedbudget!=0){
                                 ?>

                                <tr>
                                    <td> <?php echo $serialno; ?></td>
                                    <?php foreach($employees_fieldlist as $fkey=>$fieldlist){
                                        if(in_array($fkey,$defalut_fields)){
                                    ?>
                                    <td> <?php if(!is_numeric($budget[$fkey])){ echo $budget[$fkey];
}else{ echo customeummarmonyformate($budget[$fkey]);
}  ?> </td>
                                <?php } ?>
                                <?php } ?>
                                </tr>
                            <?php $serialno++;
}
}
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
