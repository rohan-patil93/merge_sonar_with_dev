<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$optiondetails = getOptionPageTableName($_POST['option']);
$url = API."?mode=getdynamictransactioninvoices&transaction_table=".$optiondetails['application_table_name']."&admin_type=".$_SESSION['admin_type']."&refrance_res=".$_POST['refrance_res']."&transaction_id=".$_POST['transaction_id']."&ref_table=".$_POST['ref_table']."&ref_id=".$_POST['ref_id'];

if($_POST['fieldaccess'] !=""){
    $url .= "&vendor_id=".$_SESSION['vendor_id']."&fieldaccess=party_type";
}
$apires = callAPI($url);
$result = json_decode($apires,1);
$transactiontables = $result['transaction_list'];
$status_progress = $result['status_progress'];
$statuse = ViewStatusData();
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
  });
});
    function opensubtable(id){
        var showhide = $("#showhide"+id).val();
        $(".showclassval").val(0);
        $(".showclassdiv").hide();
        if(showhide=='0'){
            $(".showclass"+id).show('slow');
            $("#showhide"+id).val('1');
        }else{
            $(".showclass"+id).hide('slow');
            $("#showhide"+id).val('0');
        }
    }
</script>
<div class="card">
    <div class="card-header">
        <h5><?php echo ucwords(str_replace("_"," ",($optiondetails['application_table_name']))); ?></h5>
    </div>
    <input type="hidden" id="transaction_option_name" value='<?php echo $_POST['option']; ?>'>
    <div class="card-body table-responsive">
    <?php if(count((array)$transactiontables) !=0){ ?>
    <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >
        <tr>
            <th scope="col"></th>
            <th scope="col">Transaction No.</th>
            <th scope="col">Transaction Date</th>
            <th scope="col">Originator</th>
            <th scope="col">Party Name</th>
            <th scope="col" class="text-right">Amount</th>
            <th scope="col" class="text-right">Tax</th>
            <th scope="col" class="text-right">Total</th>
            <th scope="col">Status</th>
        </tr>
    <?php foreach($transactiontables as $trans_id=>$tableslist){ ?>
        <tr class="main">
            <td class="cursor-pointer">
                <i class="ti-angle-double-down" onclick="opensubtable('<?php echo $trans_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                <input type="hidden" class="showclassval" id="showhide<?php echo $trans_id; ?>" value="0">
            </td>
            <td><?php echo $tableslist[0]['transaction_number']; ?></td>
            <td><?php echo $tableslist[0]['transaction_date']; ?></td>
            <td><?php echo $_SESSION['users'][$tableslist[0]['originator']]; ?></td>
            <td><?php echo $tableslist[0]['party_name']; ?></td>
            <td class="text-right"><?php echo customeummarmonyformateifine($tableslist[0]['amount']); ?></td>
            <td class="text-right"><?php echo customeummarmonyformateifine($tableslist[0]['tax_amount']); ?></td>
            <td class="text-right"><?php echo customeummarmonyformateifine($tableslist[0]['total_amount']); ?></td>
            <td><span class="form-label label label-md" style="color:<?php echo $statuse[$tableslist[0]['current_status']]['color']; ?>; background-color:<?php echo $statuse[$tableslist[0]['current_status']]['background_color']; ?>;"><?php echo $status_progress[$tableslist[0]['current_status']]['name']; ?></span>
            </td>
        </tr>
        <tr class="data showclass<?php echo $trans_id; ?> showclassdiv">
            <td colspan="20">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-td-6">
                            <thead>
                                <tr><th scope="col">Item</th><th scope="col" class="text-right">Quantity</th><th scope="col" class="text-right">Pending Quantity</th><th scope="col" class="text-right">Amount</th><th scope="col" class="text-right">Tax</th><th scope="col" class="text-right">Total</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($tableslist as $tableslist1){?>
                                <tr>
                                    <td class="w-b-w-s"><?php echo $tableslist1['item_name']; ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformate($tableslist1['item_quantity']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformate($tableslist1['pending_qty']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformate($tableslist1['atm_item_amount']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformate($tableslist1['itm_tax_amount']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformate($tableslist1['atm_total_amount']); ?></td>

                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <div class="card" style="background: #f5f6f7;">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                            <div class="card-header">
                                <h5>Order Summary</h5>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-6 p-t-20 text-center">
                            <p><i class="fa fa-map-marker"></i> <?php echo $tableslist[0]['party_name']; ?></p>
                        </div>
                        </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr><th scope="col">Sub Total </th><td style="text-align: right;"><?php echo customeummarmonyformateifine($tableslist[0]['amount']); ?></td></tr>
                                    <tr><th scope="col">Discount </th><td style="text-align: right;"><?php echo customeummarmonyformateifine($tableslist[0]['discount_amount']); ?></td></tr>
                                    <tr><th scope="col">Tax</th><td style="text-align: right;"><?php echo customeummarmonyformateifine($tableslist[0]['tax_amount']); ?></td></tr>
                                    <tr class="bg-primary"><th scope="col">Total</th><td style="text-align: right;"><span class="f-18"><?php echo customeummarmonyformateifine($tableslist[0]['total_amount']); ?></span></td></tr>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-1">

                                <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$tableslist[0]['id']);?>" class="d-block text-primary f-16 mt-1" target="_blank"><i class="ti-eye"></i> View</a>

                                <a  href="index.php?option=COM_QO_PRINT&rid=<?php echo $tableslist[0]['id']; ?>&table=<?php echo $optiondetails['application_table_name']; ?>&title=<?php echo ucwords(str_replace("_"," ",($optiondetails['application_table_name']))); ?>" class="d-block  text-primary f-16 mt-1" target="_blank"><i class="ti-link"></i> View Invoice</a>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
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
