<?php
     $vurl = API."?mode=customadmdashboardmodule3";
    $configesponse = callAPI($vurl);
    $mcats = json_decode($configesponse,1);
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
    foreach($vendor_admin as $vnkey=>$vn_admins){
        $ym =array();
        foreach($vn_admins as $yymm=>$vn_adminsq){
            foreach($vn_adminsq as $vn_adm){
                $name = $vn_adm['vendor_name'];

                $vnbillable[$vnkey][$yymm]['billable_amount'] = $vnbillable[$vnkey][$yymm]['billable_amount']+$vn_adm['billable_amount'];
            }
            $ym[] = $yymm;
        }
        $vntdetail['name'][$vnkey] = $name;
        $vntdetail['ym'] = $ym;

    }
    foreach($vnbillable as $vnkey=>$vnbillaa){
        $vnarray = array();
        foreach($vnbillaa as $vnbil){
            $vnarray[]=$vnbil['billable_amount']/1000;
        }
        $vndatas[$vnkey] = implode('|',(array)$vnarray);
    }
    $vntdetail['data'] = implode(',',(array)$vndatas);
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
                                    <h3 class="card-label col-lg-3">&nbsp;Vendor Wise Usage</h3>
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
                                        <input type="hidden" id="filterby" name="filterby">
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body row">
                                <div class="col-lg-12">
                                <input type="hidden" name="vname" id="vname" value="<?php echo implode("|",(array)$vtdetail['name']);?>">
                                    <input type="hidden" name="vvalue" id="vvalue" value="<?php echo implode("|",(array)$vtdetail['bdget']);?>">

                                    <input type="hidden" name="coscentervalue" id="coscentervalue" value="<?php echo $vntdetail['data']; ?>">
                                    <input type="hidden" name="coscentercount" id="coscentercount" value="<?php echo count((array)$vntdetail['name']); ?>">
                                    <input type="hidden" name="coscentery" id="coscentery" value="<?php echo implode("|",(array)$vntdetail['ym']); ?>">
                                    <input type="hidden" name="coscentertitle" id="coscentertitle" value="<?php echo implode("|",(array)$vntdetail['name']); ?>">

                                <input type="hidden" name="vendorvalue" id="vendorvalue" value="<?php echo $vntdetail['data']; ?>">
                                <input type="hidden" name="vendorcount" id="vendorcount" value="<?php echo count((array)$vntdetail['name']); ?>">
                                <input type="hidden" name="vendory" id="vendory" value="<?php echo implode("|",(array)$vntdetail['ym']); ?>">
                                <input type="hidden" name="vendortitle" id="vendortitle" value="<?php echo implode("|",(array)$vntdetail['name']); ?>">

                                    <div class="card-body">
                                        <!--begin::Chart-->
                                        <div id="admin_dash_chart4"></div>
                                        <!--end::Chart-->
                                    </div>
                                </div>

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
                                <th scope="col">Employee Name </th>
                                <th scope="col">Vertical Name </th>
                                <th scope="col">Vendor Name </th>
                                <th scope="col">Job Title </th>
                                <th scope="col">Budget </th>
                                <th scope="col">Utilized </th>
                                <th scope="col">Percentage </th>
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
                                    <td> <?php echo $budget['employee_name']; ?> </td>
                                    <td> <?php echo $budget['vertical_name'];  ?> </td>
                                    <td> <?php echo $budget['vendor_name'];  ?> </td>
                                    <td> <?php echo $budget['job_title'];  ?> </td>
                                    <td class="text-right"> <?php echo customeummarmonyformate($estimatedbudget); ?> </td>
                                    <td class="text-right"> <?php echo customeummarmonyformate($utilizedbudget); ?> </td>
                                    <td class="text-right"> <?php echo number_format($utilizedbudget/$estimatedbudget * 100,2); ?> </td>

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
