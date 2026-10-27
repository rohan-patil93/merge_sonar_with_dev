<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$transaction_table = "Sales_orders";
$url = API."?mode=getdynamictransactioninvoices&transaction_table=".$transaction_table."&admin_type=".$_SESSION['admin_type']."&refrance_res=".$_POST['refrance_res']."&transaction_id=".$_POST['transaction_id']."&ref_table=".$_POST['ref_table']."&ref_id=".$_POST['ref_id'];

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
    </div>
    <input type="hidden" id="transaction_option_name" value='<?php echo $_POST['option']; ?>'>
    <div class="card-body table-responsive">
    <?php if(count((array)$transactiontables) !=0){ ?>
    <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >
        <thead>
            <tr>
                <th scope="col" >Transaction No.</th>
                <th scope="col">Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($transactiontables as $trans_id=>$tableslist){ ?>
            <tr >
                <td>
                    <table role="presentation">
                        <tr>
                            <td class="p-1">transaction_number</td>
                            <td  class="p-1"><?php echo $tableslist[0]['transaction_number']; ?></td>
                        </tr>
                        <tr>
                            <td  class="p-1">transaction_date</td>
                            <td  class="p-1"><?php echo $tableslist[0]['transaction_date']; ?></td>
                        </tr>
                        <tr>
                            <td  class="p-1">party_name</td>
                            <td  class="p-1 w-b-w-s "><?php echo $tableslist[0]['party_name']; ?></td>
                        </tr>
                        <tr>
                            <td  class="p-1">Statuse</td>
                            <td  class="p-1"><span class="form-label label label-md" style="color:<?php echo $statuse[$tableslist[0]['current_status']]['color']; ?>; background-color:<?php echo $statuse[$tableslist[0]['current_status']]['background_color']; ?>;"><?php echo $status_progress[$tableslist[0]['current_status']]['name']; ?></span></td>
                        </tr>
                    </table>
                </td>
                <td class="align-top">
                    <table style="width: 100%;">
                        <thead>
                            <tr><th scope="col" class="p-1">Item</th><th scope="col" class="text-right p-1">Quantity</th><th scope="col" class="text-right p-1">Pending Quantity</th><th scope="col" class="text-right p-1">Amount</th><th scope="col" class="text-right p-1">Tax</th><th scope="col" class="text-right p-1">Total</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach($tableslist as $tableslist1){?>
                            <tr>
                                <td class="w-b-w-s p-1"><?php echo $tableslist1['item_name']; ?></td>
                                <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['item_quantity']); ?></td>
                                <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['pending_qty']); ?></td>
                                <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['atm_item_amount']); ?></td>
                                <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['itm_tax_amount']); ?></td>
                                <td class="text-right p-1"><?php echo customeummarmonyformate($tableslist1['atm_total_amount']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php } ?>
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
