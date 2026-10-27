<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// ===============================
// 📅 Date Filters
// ===============================
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

$transaction_table = "Sales_orders";
$url = API."?mode=getsalessummaryreport&transaction_table=".$transaction_table."&admin_type=".$_SESSION['admin_type']."&refrance_res=".$_POST['refrance_res']."&transaction_id=".$_POST['transaction_id']."&ref_table=".$_POST['ref_table']."&ref_id=".$_POST['ref_id']."&from_date=".$from_date."&to_date=".$to_date;

if($_POST['fieldaccess'] !=""){
    $url .= "&vendor_id=".$_SESSION['vendor_id']."&fieldaccess=party_type";
}
$apires = callAPI($url);
$result = json_decode($apires,1);
$transactiontables = $result['transaction_list'];
$status_progress = $result['status_progress'];
$statuse = ViewStatusData();
?>
<div class="card">
    <div class="card-header">
        <h5>Sales Orders Details</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesSummary&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SalesSummary&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="transaction_option_name" value='<?php echo $_POST['option']; ?>'>
    <div class="card-body table-responsive">
    <?php include_once 'date_filter_common.php'; ?>
    <hr>
    <?php if(count((array)$transactiontables) !=0){ ?>
    <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" id="sales_summary_table">
        <thead>
            <tr>
                <th scope="col">Sno</th>
                <th scope="col">Date</th>
                <th scope="col">Voucher No</th>
                <th scope="col">Customer</th>
                <th scope="col">GSTIN</th>
                <th scope="col">HSN/SAC</th>
                <th scope="col" class="text-right">Units</th>
                <th scope="col" class="text-right">Quantity</th>
                <th scope="col" class="text-right">Rate</th>
                <th scope="col" class="text-right">Gross</th>
                <th scope="col" class="text-right">Discount</th>
                <th scope="col" class="text-right">Taxable Amt.</th>
                <th scope="col" class="text-right">SGST</th>
                <th scope="col" class="text-right">CGST</th>
                <th scope="col" class="text-right">IGST</th>
                <th scope="col" class="text-right">Net</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sno = 1;
            foreach($transactiontables as $transaction){
                $rowspan = count($transaction['items']);
                $first_item = true;
                foreach($transaction['items'] as $item){
            ?>
            <tr>
                <?php if($first_item){ ?>
                <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $sno; ?></td>
                <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo fromdate($transaction['transaction_date']); ?></td>
                <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $transaction['transaction_number']; ?></td>
                <td class="p-1 w-b-w-s" rowspan="<?php echo $rowspan; ?>"><?php echo $transaction['party_name']; ?></td>
                <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $transaction['gstin']; ?></td>
                <?php } ?>
                <td class="p-1"><?php echo $item['hsn_sac']; ?></td>
                <td class="text-right p-1"><?php echo $item['purchase_uom']; ?></td>
                <td class="text-right p-1"><?php echo $item['item_quantity']; ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['item_price']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['gross_amount']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['discount_amount']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['taxable_amount']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['sgst']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['cgst']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['igst']); ?></td>
                <td class="text-right p-1"><?php echo customeummarmonyformate($item['net_amount']); ?></td>
            </tr>
            <?php
                    $first_item = false;
                }
                $sno++;
            }
            ?>
        </tbody>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Invoices</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
</div>
</div>
