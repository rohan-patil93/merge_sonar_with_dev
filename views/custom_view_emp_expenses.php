<?php
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getempexpenses&method=get&id=$rid";
$result = callAPI($url);
$result = json_decode($result, 1);
$trips = $result['trips'];
$expenses_user_details = $result['expenses_user_details'];
$projects_list = $result['projects'];
$emp_expenses = $result['emp_expenses'];
$emp_expenses_details = $result['emp_expenses_details'];
foreach($result['expense_categories'] as $catdat){
  $datacat[$catdat['category_id']]=$catdat;
}
foreach($result['expenses_mileages_perdiem'] as $catdat){
    if($catdat['expense_type']=='0'){
        $mileageslist[$catdat['id']]=$catdat;
    }else{
        $perdiamarray[$catdat['id']]=$catdat;
    }
}
$arraycurrncy = array("1"=>"INR","2"=>"USD");
foreach($result['general_code_details'] as $code){
    if($code['genc_code']=="PAYMETHOD"){
        $Payment_mode[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }elseif($code['genc_code']=="EXPENSTYPE"){
        $expense_typelist[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }
}

$trips_select = $result['trips_select'];
$domestic_round_trips = $result['domestic_round_trips'];
$trip_hotels = $result['trip_hotels'];
$local_trips = $result['local_trips'];
$cities = $result['cities'];
$vehicle_types = $result['vehicle_types'];

$countries = $result['countries'];

$emp_expenses_child = array();
foreach($emp_expenses_details as $expdetail){
    if($expdetail['trip_transaction_table'] !="" && $expdetail['trip_transaction_id']!=""){
        $emp_expenses_child[$expdetail['trip_transaction_table']][$expdetail['trip_transaction_id']][] = $expdetail;
    }else{
        $emp_expenses_child['others'][] = $expdetail;
    }
}
$trip_with_expense = array();
foreach($domestic_round_trips as $roundtrp){
    $trip_with_expense['domestic_round_trips'][$roundtrp['id']]['trip'] = $roundtrp;
    if(isset($emp_expenses_child['domestic_round_trips'][$roundtrp['id']])){
        $trip_with_expense['domestic_round_trips'][$roundtrp['id']]['child'] = $emp_expenses_child['domestic_round_trips'][$roundtrp['id']];
    }

}
foreach($trip_hotels as $trphotel){
    $trip_with_expense['trip_hotels'][$trphotel['id']]['trip'] = $trphotel;
    if(isset($emp_expenses_child['trip_hotels'][$trphotel['id']])){
        $trip_with_expense['trip_hotels'][$trphotel['id']]['child'] = $emp_expenses_child['trip_hotels'][$trphotel['id']];
    }
}

foreach($local_trips as $trplocal){
    $trip_with_expense['local_trips'][$trplocal['id']]['trip'] = $trplocal;
     if(isset($emp_expenses_child['local_trips'][$trplocal['id']])){
        $trip_with_expense['local_trips'][$trplocal['id']]['child'] = $emp_expenses_child['local_trips'][$trplocal['id']];
    }
}

$all_expense_categories = $result['all_expense_categories'];
$expense_categories_list = array();
foreach($all_expense_categories as $expcat){
    if($expcat['parent_category_id'] == 0){
        $expense_categories_list[$expcat['id']]['parent'] = $expcat;
    }else{
        $expense_categories_list[$expcat['parent_category_id']]['child'][$expcat['id']]  = $expcat;
    }

}

        $trip_with_expense['Others'][0]['trip'] = array();
        $trip_with_expense['Others'][0]['child'] = $emp_expenses_child['others'];

?>
    <style type="text/css">
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        td, th{font-size: 13px; padding: 5px !important; vertical-align: middle;white-space: nowrap;}
        @media (max-width:772px){
            .scan{width: 36%;}
        }
        table{
            border-collapse : collapse;
        }
    </style>

    <script>
        function printDiv(divName) {
           var printContents = document.getElementById(divName).innerHTML;
           var originalContents = document.body.innerHTML;
           document.body.innerHTML = printContents;

           window.print();

           document.body.innerHTML = originalContents;
        }
    </script>

 <div class="card">
    <div class="card-body">
    <div class="" id="printableArea">
        <div class="row mb-5">
            <div class="col-lg-4">
                <div class="col bg-light-primary ">
                    <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                        <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                    </span>
                    <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo ucwords($_SESSION['config']['name']); ?></span>
                </div>
            </div>
            <div class="col-lg-4">
                <h4 class="card-label text-center f-w-700">Expense Report <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                </h4>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <th scope="col" colspan="6"></th>
                <th scope="col" colspan="3" style="text-align: right; font-size: 13px;">Name: <?php echo $expenses_user_details['display_name']; ?></th>
                <th scope="col" colspan="3" style="text-align: right; font-size: 13px;">Manager: <?php echo $_SESSION['users'][$expenses_user_details['reports_to']]; ?></th>
            </tr>
            <tr>
                <th scope="col" colspan="6"></th>
                <th scope="col" colspan="3" style="text-align: right; font-size: 13px;">Trip Budget: <?php echo customeNumberFormate($trips['budget_amount']); ?></th>
                <th scope="col" colspan="3" style="text-align: right; font-size: 13px;">Total Expense: <?php echo customeNumberFormate($emp_expenses['total_amount']); ?></th>
            </tr>


            <tr>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Request Number</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;" style="font-size: 13px; border: 1px solid #eeeeee;">Event Name</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Place</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Date</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Budget</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Expense</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Exp Date</th>


                <?php foreach($expense_categories_list as $epcat){ ?>
                    <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;"><?php echo $epcat['parent']['category_name']; ?></th>
                <?php } ?>

                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Transport</th>
                <th scope="col" style="font-size: 13px; border: 1px solid #eeeeee;">Others</th>
            </tr>
            <tr>
                <td style="font-size: 13px; border: 1px solid #eeeeee;"><?php echo $trips_select['transaction_no']." (".fromDate($trips_select['transaction_date']).")"; ?></td>
            </tr>
            <?php
            $total_rowexpense = array();
             foreach($trip_with_expense as $trantable=>$tripwithexpense){ ?>
            <tr>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">

                <?php
                    if($trantable == "domestic_round_trips"){
                        echo "Journey";
                    }elseif($trantable == "trip_hotels"){
                        echo "Hotel";
                    }elseif($trantable == "Others"){
                        echo "Others";
                    }else{
                       echo "Local";
                    }
                ?></td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
            </tr>
            <?php foreach($tripwithexpense as $key1=>$key1_detail){ ?>
                <tr>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee; ">
                        <?php
                if($trantable == "domestic_round_trips"){
                    echo $cities[$key1_detail['trip']['origin_location']]['city_name']." (".$cities[$key1_detail['trip']['origin_location']]['country_code'].")";

                }elseif($trantable == "trip_hotels"){
                    echo $cities[$key1_detail['trip']['location']]['city_name']." (".$cities[$key1_detail['trip']['location']]['country_code'].")";
                }elseif($trantable == "Others"){
                     echo "-";
                }else{
                    echo $cities[$key1_detail['trip']['from_location']]['city_name']." (".$cities[$key1_detail['trip']['from_location']]['country_code'].")";
                }
                    ?>
                    </td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">
                    <?php
                        if($trantable == "domestic_round_trips"){
                            echo fromDate($key1_detail['trip']['departure_date']);

                        }elseif($trantable == "trip_hotels"){
                            echo "IN :".$key1_detail['trip']['check_in']." - Out :".$key1_detail['trip']['check_out'];
                        }elseif($trantable == "Others"){
                            echo "-";
                        }else{
                            echo fromDate($key1_detail['trip']['trip_date']);
                        }
                    ?>

                    </td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">

                         <?php
                        if($trantable == "domestic_round_trips"){
                            echo customeNumberFormate($key1_detail['trip']['estimated_cost']);

                        }elseif($trantable == "trip_hotels"){
                            echo customeNumberFormate($key1_detail['trip']['cost']);
                        }elseif($trantable == "Others"){
                            echo "-";
                        }else{
                            echo customeNumberFormate($key1_detail['trip']['estimated_cost']);
                        }
                    ?>

                    </td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;"></td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;"></td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;"></td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                </tr>

                <?php foreach($key1_detail['child'] as $tripdetail){ ?>
                                    <tr>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;"></td>
                     <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>

                    <td style="font-size: 13px; border: 1px solid #eeeeee;"><?php echo $tripdetail['description']; ?></td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;"><?php echo fromDate($tripdetail['bill_date']); ?></td>
                    <?php
                    if($tripdetail['expense_type']=='0'){
                        foreach($expense_categories_list as $parentkeyid=>$epcat){
                           if(isset($epcat['child'][$tripdetail['category_id']])){

                           $total_rowexpense[$parentkeyid] += $tripdetail['expense_amount'];

                    ?>
                            <td style="font-size: 13px; border: 1px solid #eeeeee; text-align: right; "><?php echo customeNumberFormate($tripdetail['expense_amount']); ?></td>
                            <?php }else{ ?>

                            <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                            <?php } ?>
                    <?php } ?>
                    <?php } else { ?>
                        <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <?php } ?>
                    <?php
                    if($tripdetail['expense_type']=='1'){
                        $total_rowexpense['transport'] += $tripdetail['expense_amount'];
                     ?>
                        <td style="font-size: 13px; border: 1px solid #eeeeee; text-align: right; "><?php echo customeNumberFormate($tripdetail['expense_amount']); ?></td>
                        <?php
                    }else{ ?>
                        <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <?php }
                    if($tripdetail['expense_type']=='2'){
                        $total_rowexpense['other'] += $tripdetail['expense_amount'];
                        ?>
                        <td style="font-size: 13px; border: 1px solid #eeeeee; text-align: right; "><?php echo customeNumberFormate($tripdetail['expense_amount']); ?></td>
                    <?php }else{ ?>
                        <td style="font-size: 13px; border: 1px solid #eeeeee;">-</td>
                    <?php } ?>
                    </tr>
                 <?php } ?>
            <?php } ?>
        <?php } ?>

            <tr>
                <th scope="col" colspan = "7" style="font-size: 13px; border: 1px solid #eeeeee;">Total</th>
                <?php foreach($expense_categories_list as $parentkeyid=>$epcat){ ?>
                <th scope="col" style="text-align: right; "><?php echo customeNumberFormate($total_rowexpense[$parentkeyid]); ?></th>
                <?php } ?>
                <th scope="col"  style="text-align: right; "><?php echo customeNumberFormate($total_rowexpense['transport']); ?></th>
                <th scope="col"  style="text-align: right; "><?php echo customeNumberFormate($total_rowexpense['other']); ?></th>
            </tr>
        </table>
    </div>
</div>
</div>
    <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 col-lg-1 my-1" style="margin-top: 20px;" onclick="printDiv('printableArea')" >Print</button>
