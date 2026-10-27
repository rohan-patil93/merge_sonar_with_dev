<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// Enquiry
// Estimate
// Sales Order
// Indenting/
// Quotations/
// Purchase Order/
// Grn
// Delivery order
// Sales invoice -

$frist_array = array(
    'estimates'=>array('enquiries','sales_orders','requisitions','quotations','purchase_orders')
    ,'enquiries'=>array('sales_orders','requisitions','quotations','purchase_orders')
    ,'requisitions'=>array('quotations','purchase_orders')
    ,'quotations'=>array('purchase_orders')
    ,'purchase_orders'=>array('goods_receipt_notes','purchase_invoices')
    ,'purchase_invoices'=>array()
    ,'goods_receipt_notes'=>array('purchase_invoices')
    ,'delivery_orders'=>array('sales_invoices')
    ,'sales_orders'=>array('requisitions','quotations','purchase_orders','delivery_orders')
    ,'sales_invoices'=>array()
    );

$baseconvert = base64_encode(json_encode((array)$frist_array));
$getapi = API."?mode=getslatrackinglogreports&ajaxcll=Y&transaction_table=".$_POST['transaction_table']."&transaction_id=".$_POST['transaction_id']."&from_date=".$_POST['from_date']."&to_date=".$_POST['to_date']."&transation_table_list=".$baseconvert;
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$data = $result['transaction_details'];
function collectChildrenRecursive($record, $table, $relationMap, &$row) {
    if (!isset($relationMap[$table])) { return;
}

    foreach ($relationMap[$table] as $childTable) {
        if (!empty($record['child'][$childTable])) {
            foreach ($record['child'][$childTable] as $child) {
                if ($row[$childTable] === '-') {
                    $row[$childTable] = [];
                }
                $row[$childTable][] = $child;
                collectChildrenRecursive($child, $childTable, $relationMap, $row); // Recursive step
            }
        }
    }
}

function buildTransactionHierarchy($data, $relationMap) {
    $keyName = $keyName ?? '';
    $output = [];

    foreach ($data as $mainTable => $records) {
        if (!isset($relationMap[$mainTable])) { continue;
}

        foreach ($records as $rec) {
            $row = [];

            // Set default keys as "-"
            foreach (array_keys($relationMap) as $keyName) {
                $row[$keyName] = "-";
            }

            // Put current record in current key
            $row[$mainTable] = [$rec];

            // Recursively collect all children
            collectChildrenRecursive($rec, $mainTable, $relationMap, $row);

            $output[] = $row;
        }
    }
    return $output;
}
$finalResult = buildTransactionHierarchy($data, $frist_array);

$allatblelist = array();
foreach($frist_array as $frist_key =>$frist){
    $allatblelist[$frist_key] = $frist_key;
    foreach($frist as $fristtab){
        $allatblelist[$fristtab] = $fristtab;
    }
}

?>

<table class="table table-hover m-b-0  mt-3" style="font-size:13px;" id="costcenterfilter">
    <thead>
        <tr>
            <th scope="col">#</th>
            <?php foreach($allatblelist as $transtab){ ?>
            <th scope="col" colspan="" class="text-center border">
                <?php echo ucwords(str_replace('_',' ',$transtab)); ?></th>
            <?php } ?>
            <th scope="col" rowspan="2" class="border">Transaction Total Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i=1;
        foreach($finalResult as $resary){  $transactiontotaltime = array();
            $userwithid11 = array();
            foreach($allatblelist as $transtab){
                $resdata = $resary[$transtab];
                foreach($resdata as $rdata){
                    $userwithid11[$rdata['originator']] =$rdata['originator'];
                    $slatracklog = $rdata['sla_tracking_logs'];
                    foreach($slatracklog as $subslalg){
                        $userwithid11[$subslalg['originator']] =$subslalg['originator'];
                    }
                }
            }

        ?>
        <tr class="hidealluserdata <?php foreach($userwithid11 as $uids=>$useidtimxasx){ echo " selectuserid_".$uids;
} ?>">
            <td class=" p-2"><?php echo $i; ?></td>
        <?php
            foreach($allatblelist as $transtab){
                $resdata = $resary[$transtab];
            ?>
            <td colspan="" class="text-left border p-2">
                <?php
                $pageurl = getPagenametotable($transtab,'insert_page_name');
                foreach($resdata as $rdata){
                    $slatracklog = $rdata['sla_tracking_logs'];

                    $totaltimetransaction = array();
                    $userdateTimes  = array();
                    $starttimeary ="";
                    if($starttimeary !=""){
                        $userdateTimes[] = $starttimeary;
                    }
                    $userwithid = $userwithid22 = array();
                    $userwithid22[$rdata['originator']] = $rdata['originator'];
                    foreach($slatracklog as $subslalg){
                        $userdateTimes[] = $subslalg['request_datetime'];
                        $totaltimetransaction[] = $subslalg['request_datetime'];
                        $starttimeary= $subslalg['request_datetime'];
                        $transactiontotaltime[] = $subslalg['request_datetime'];
                        $userwithid[$subslalg['originator']][] =$subslalg['request_datetime'];
                        $userwithid22[$subslalg['originator']] = $subslalg['originator'];
                    }
                    $usertime = totalHoursAndMinutesInDateTimeArray($userdateTimes);

                    ?>
                    <span class=" mt-2 hidealluserdata <?php foreach($userwithid22 as $uids){ echo " selectuserid_".$uids;
} ?>">
                    <?php
                    $riddd = deocdeId('id:'.$rdata['id']);
                    echo "<a class='d-block f-w-600 mb-0 text-primary' href='index.php?option=".$pageurl."&Rid=".$riddd."'>".$rdata['transaction_number']."(".fromDate($rdata['transaction_date']).")</a>";
                    $lastusertime1 = "";

                    foreach($userwithid as $uid=>$useidtim){
                        $lastusertime = array();
                        if($lastusertime1 !=""){
                            $lastusertime[] = $lastusertime1;
                        }
                        foreach($useidtim as $utime){
                            $lastusertime[] = $utime;
                        }
                        $lastusertime1 = end($useidtim);
                        $perticulerusertime = totalHoursAndMinutesInDateTimeArray($lastusertime);
                        ?>
                        <p class=" mb-0 hidealluserdata <?php echo "selectuserid_".$uid; ?>"><span class="f-w-600"><?php echo $_SESSION['users'][$uid]." </span>- ".$perticulerusertime['hours']." Hours : ".$perticulerusertime['minutes']." Minutes";  ?> </p>

                    <?php
                    }
                    echo "<p class='d-block mb-0'><span class='f-w-600'>Total </span>- ".$usertime['hours']." Hours : ".$usertime['minutes']." Minutes";
                    ?>
                    </span>
                    <?php
                }
                ?>
            </td>
            <?php
            } ?>
            <td>
                <?php
                if(count((array)$transactiontotaltime) != 0){
                    sort($transactiontotaltime);
                    $transactiontotaltimes = totalHoursAndMinutesInDateTimeArray($transactiontotaltime);
                    echo "<span class='f-w-600'>".$transactiontotaltimes['days']." Days : ".$transactiontotaltimes['hours']." Hours : ".$transactiontotaltimes['minutes']." Minutes</span>"."</br>";
                    }else{
                        echo "-";
                    }
                ?>

            </td>
            </tr><?php
        $i++;
}
        ?>
    </tbody>
</table>
