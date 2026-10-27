<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$url1 = API."?mode=getjournalvouchers&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$ref_details_table = $result['ref_details_table'];
$view_page_name = $ref_details_table['view_page_name'];
$insert_page_name = $ref_details_table['insert_page_name'];
$journal_vouchers = $result['journal_vouchers'];
$jv_items = $result['jv_items'];
$ref_details = $result['ref_details'];
$ref_item_details = $result['ref_item_details'];
?>
<div class="modal-header">
    <h4 class="modal-title">Journal Voucher</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="">


        <ul class="nav md-tabs infisuite_tab mb-2">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#selfdocuments" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Journal Voucher</a>
            </li>
            <?php if($journal_vouchers['ref_id'] !=""){ ?>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#samerefdocumentssasas" role="tab" aria-selected="true"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Reference Document</a>
            </li>
        <?php } ?>
        </ul>
        <div class="tab-content tabs">
            <div class="tab-pane card-body p-0 active" id="selfdocuments" role="tabpanel">

                <?php
                    $_GET['option'] = "COM_VIEW_JOURNAL_VOUCHERS";
                    $rid1 = deocdeId("id:".$rid);
                    $_GET['Rid'] = $rid1;
                    include_once "new_view.php";
                ?>
                <div class="m-r-50 m-l-50">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 70%;">Particulars</th>
                                <th scope="col" class="text-right" style="width: 15%;">Debit</th>
                                <th scope="col" class="text-right" style="width: 15%;">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $debit_total = $credit_total =$difference_amt= "0";
                            foreach ($jv_items as $jv) {
                                $debit_total += $jv['debit_amount'];
                                $credit_total += $jv['credit_amount'];
                            ?>
                            <tr>
                                <td class="p-2">
                                    <?php echo $jv['account_code']; ?>
                                    <span class="f-12 d-block"><?php echo $jv['account_name']; ?></span>
                                    <span class="f-12 d-block"><?php echo $jv['particulars']; ?></span>
                                </td>
                                <td class="text-right p-2"><?php echo ($jv['debit_amount']!="" && $jv['debit_amount']!="0")?customeummarmonyformate($jv['debit_amount']):"-"; ?></td>
                                <td class="text-right p-2"><?php echo ($jv['credit_amount']!="" && $jv['credit_amount']!="0")?customeummarmonyformate($jv['credit_amount']):"-"; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="col" class="text-right">
                                    Total
                                </th>
                                <td class="text-right"><?php echo customeummarmonyformate($debit_total); ?></td>
                                <td class="text-right"><?php echo customeummarmonyformate($credit_total); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php if($journal_vouchers['ref_id'] !=""){ ?>
            <div class="tab-pane card-body p-0" id="samerefdocumentssasas" role="tabpanel">

                <?php
                    $_GET['option'] = $view_page_name;
                    $rid2 = deocdeId("id:".$journal_vouchers['ref_id']);
                    $_GET['Rid'] = $rid2;
                    include_once "new_view.php";
                ?>
                <div class="m-r-50 m-l-50">
                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table table-td-6">
                                <thead>
                                    <tr><th scope="col">Item</th><th scope="col" class="text-right">Quantity</th><th scope="col" class="text-right">Amount</th><th scope="col" class="text-right">Tax</th><th scope="col" class="text-right">Total</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($ref_item_details as $tableslist1){?>
                                    <tr>
                                        <td class="w-b-w-s"><?php echo $tableslist1['item_name']; ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate($tableslist1['item_quantity']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate($tableslist1['item_amount']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate($tableslist1['tax_amount']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate($tableslist1['total_amount']); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                            <div class="card" style="background: #f5f6f7;">
                                <div class="row">
                                    <div class="col-lg-10">
                                <div class="card-header">
                                    <h5>Order Summary</h5>
                                </div>
                            </div>
                            </div>
                                <div class="card-body">
                                    <table class="table table-sm mb-0">
                                        <tr><th scope="col">Sub Total </th><td style="text-align: right;"><?php echo customeummarmonyformateifine($ref_details['amount']); ?></td></tr>
                                        <tr><th scope="col">Discount </th><td style="text-align: right;"><?php echo customeummarmonyformateifine($ref_details['discount_amount']); ?></td></tr>
                                        <tr><th scope="col">Tax</th><td style="text-align: right;"><?php echo customeummarmonyformateifine($ref_details['tax_amount']); ?></td></tr>
                                        <tr class="bg-primary"><th scope="col">Total</th><td style="text-align: right;"><span class="f-18"><?php echo customeummarmonyformateifine($ref_details['total_amount']); ?></span></td></tr>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-1">
                                    <a href="index.php?option=<?php echo $insert_page_name; ?>&Rid=<?php echo deocdeId('id:'.$ref_details['id']);?>" class="d-block text-primary f-16 mt-1" target="_blank"><i class="ti-eye"></i> View</a>
                        </div>
                    </div>
                </div>

            </div>
        <?php } ?>
        </div>


    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
