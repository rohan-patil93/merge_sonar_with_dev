<?php $config = $_SESSION['config'];
$rid = $_GET['rid'];
$table = $_GET['table'];
$rpurl = API."?mode=getInvoicesData&in_id=$rid&table=$table";
$rp_data = callAPI($rpurl);
$rpres = json_decode($rp_data, 1);
$data = $rpres['data'];
$items = $rpres['items'];
$cat1["field"] = "id,category_description";
$datas = urlencode(json_encode($cat1));

$curr = API."?mode=singlerowtabledata&rid=$data[currency_id]&table=".TBL_CURRENCIES;
$curre = callAPI($curr);
$curre = json_decode($curre,1);

$idata1["field"] = "id,sku";
$idata = urlencode(json_encode($data1));
$itemurl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$idata;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
foreach ($itemres as  $ite) {
    $itee[$ite['id']] = $ite['job_title'];
}

$vdata1["where"] = array('vendor_id'=> $data['customer_id']);
$vdata1["field"] = "id,vendor_part_no,item_id";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_VENDOR_ITEMS."&data=".$vdata;
$vdata = callAPI($vurl);
$vdata = json_decode($vdata, 1);
foreach ($vdata as  $vite) {
    $vitee[$vite['item_id']] = $vite['vendor_part_no'];
}
$uomlist = array('1'=>'Numbers');

 $print_img ='uploads/all/'.$config['display_logo'];
?>
<link rel="stylesheet" type="text/css" href="assets/css/invoice.css">

<script type="text/javascript">
   function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html lang=\"en\"><head><title>Purchase Order Print</title></head><body>" + printContents + "</body>";
       window.print();
       document.body.innerHTML = originalContents;
   }
</script>

<style type="text/css">
    @page{size: landscape;}

    @media print{
        .invoice-3 .invoice-top, .invoice-3 .invoice-center, .invoice-3 .invoice-bottom{padding: 20px;}
        body {writing-mode: tb-rl;}
    }
</style>
<div class="invoice-3 invoice-content" id="printDiv">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner">
                    <div class="invoice-info" id="invoice_wrapper">
                        <div class="invoice-top">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="invoice">
                                        <h4 class="inv-header-1 text-white"><?php echo urldecode($_GET['title']); ?></h4>

                                        <div class="d-flex align-items-center gap-2">
  <input aria-label="Transaction Number" type="text"
         name="transaction_number"
         class="form-control form-control-sm fw-bold"
         value="<?php echo htmlspecialchars($data['transaction_number']); ?>"
         style="max-width:260px"
         placeholder="Enter Transaction No">
  <input type="hidden" name="transaction_record_id" value="<?php echo htmlspecialchars($data['id'] ?? $_GET['rid']); ?>">
</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="invoice-name text-end text-white">
                                        <div class="logo">
                                            <img class="logo" src="<?php echo $print_img; ?>" alt="logo">
                                        </div>
                                        <p class="mt-4"><?php echo $config['contact_address']; ?></p>

                                    </div>
                                </div>
                            </div>
                            <hr class="text-white">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="informeshon text-white">

                                        <p> <?php if($table == 'requisitions') { echo "Employee";
} else {  echo "Vendor";
}  ?> &nbsp;&nbsp;: &nbsp;&nbsp; <?php echo $data['customer_id']; ?> - <?php echo $data['customer_name']; ?><br/><br/>
                                        Email&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['customer_email']; ?><br/><br/>
                                        Phone&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['customer_phone']; ?>
                                        <br/><br/>
                                        Taxes&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['customer_tax_number']; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="informeshon text-white">
                                        <p class="inv-title-1">Billing Adderss:</p>
                                        <p class="invo-addr-1">
                                            <?php echo $data['customer_address']; ?> <br/>
                                        </p>
                                        <p class="inv-title-1">Shiping Adderss :</p>
                                        <p class="invo-addr-1">
                                            <?php echo $data['customer_ship_adderss']; ?>   <br/>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="informeshon text-white">
                                        <p> Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; <?php echo $data['transaction_date']; ?><br/><br/>
                                        Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo "NET + ".$data['payment_terms']." DAYS"; ?><br/><br/>
                                        Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $data['place_of_supply']; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-center">
                            <div class="table-section table-responsive clearfix">
                                <table class="table caption-top invoice-table">
                                    <thead class="bg-active">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Descrpition</th>
                                        <th scope="col" class="text-center">UOM</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-center">Price</th>
                                        <th scope="col" class="text-center">Amount</th>
                                        <th scope="col" class="text-right">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;  foreach ($items as $item) {
                                         ?>
                                    <tr>
                                        <td>
                                            <?php echo $itee[$item['item_id']]; ?>
                                        </td>
                                        <td>
                                            <div class="item-desc-1">

                                                <small><?php echo $item['description']; ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo $uomlist[1]; ?></td>

                                        <td style="min-width:140px;">
                                            <input aria-label="Enter number" type="number"
                                                   step="0.01"
                                                   min="0"
                                                   class="form-control form-control-sm text-right item-qty"
                                                   name="items[<?php echo $i; ?>][quantity]"
                                                   value="<?php echo htmlspecialchars($item['item_quantity']); ?>">
                                            <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo htmlspecialchars($item['id']); ?>">
                                          </td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['item_price']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['item_amount']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['total_amount']+$item['tax_amount']); ?></td>
                                    </tr>
                                    <?php $i++;
} ?>
                                    <tr>
                                        <td colspan="6" class="text-end">SubTotal</td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($data['amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">Tax</td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($data['tax_amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Grand Total</td>
                                        <td class="text-right fw-bold"><?php echo customeummarmonyformate ($data['total_amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Ref Total Amount</td>
                                        <td class="text-right fw-bold">
                                            <input aria-label="Ref Total Amount" type="number"
                                                   step="0.01"
                                                   min="0"
                                                   class="form-control form-control-sm text-right"
                                                   name="ref_total_amount"
                                                   value="<?php echo htmlspecialchars($data['ref_total_amount'] ?? ''); ?>"
                                                   placeholder="Enter Ref Total Amount">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="bank-transfer" style="display:none;">
                                        <h3 class="inv-title-1">Bank Transfer</h3>
                                        <ul class="bank-transfer-list-1">
                                            <li><strong>Account Name:</strong>Syamsuddin</li>
                                            <li><strong>Account Number:</strong>1234567890934</li>
                                            <li><strong>Code:</strong>BARC0032UK</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="amount text-end">
                                        <h3 class="inv-title-1">Total Amount</h3>
                                        <h1><?php echo customeummarmonyformate ($data['total_amount']); ?></h1>

                                    </div>
                                </div>
                            </div>
                            <div class="note mt-3">
                                <p class="text-muted"><?php echo $data['printing_notes']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-btn-section clearfix d-print-none">
                        <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
                            <i class="fa fa-print"></i> &nbsp Print Invoice
                        </button>
                        <button class="btn waves-effect waves-light btn-grd-info"  onclick="printDiv('printDiv');">
                            <i class="fa fa-download"></i> &nbsp Download Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

