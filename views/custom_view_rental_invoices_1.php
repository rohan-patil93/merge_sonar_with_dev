<?php $config = $_SESSION['config'];
$rid = $_GET['rid'];
$table = $_GET['table'];    
$rpurl = API."?mode=getInvoicesData&in_id=$rid&table=$table"; 
//echo $rpurl;
$rp_data = callAPI($rpurl);
$rpres = json_decode($rp_data, 1);
//echo "<pre>"; print_r($rpres); exit;
$data = $rpres['data'];
$items = $rpres['items'];
//echo "<pre>"; print_r($data);
$cat1["field"] = "id,category_description";
$datas = urlencode(json_encode($cat1));

$curr = API."?mode=singlerowtabledata&rid=$data[currency_id]&table=".TBL_CURRENCIES;
//echo $curr; exit;
$curre = callAPI($curr);
$curre = json_decode($curre,1);

$idata1["field"] = "id,sku";
$idata = urlencode(json_encode($data1));
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$idata; 
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
foreach ($itemres as  $ite) {
    $itee[$ite['id']] = $ite['sku'];
}

$vdata1["where"] = array('vendor_id'=> $data['party_id']);
$vdata1["field"] = "id,vendor_part_no,item_id";
///print_r($vdata1);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_VENDOR_ITEMS."&data=".$vdata; 
//echo $vurl;
$vdata = callAPI($vurl);
$vdata = json_decode($vdata, 1);
foreach ($vdata as  $vite) {
    $vitee[$vite['item_id']] = $vite['vendor_part_no'];
}
$uomlist = array('1'=>'Numbers');
//print_r($vitee); exit;

/*$itemcaturl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEM_CATEGORIES."&data=".$datas; 
//echo $itemurl;
$itemcat_data = callAPI($itemcaturl);
$itemcatres = json_decode($itemcat_data,  );
//print_r($itemres);exit;
foreach ($itemcatres as $item) {
    $cate[$item['id']] = $item['category_description'];
    //print_r($item);
}*/
 $print_img ='uploads/all/'.$config['display_logo'];
?>
<link rel="stylesheet" type="text/css" href="assets/css/invoice.css">

<script type="text/javascript">
   function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
       window.print();
       document.body.innerHTML = originalContents;
   }
   document.onreadystatechange = function(){
        if(document.readyState == "complete") {
            setTimeout(function(){
                 printDiv('invoice_wrapper');
                // convertAndDownloadPdf();
            },500);
        } 
    }
    function convertAndDownloadPdf() {
        var htmlContent = $('#invoice_wrapper').html();
        $.ajax({
            url: 'views/custom_view_download_pdf.php',
            type: 'POST',
            data: { html: htmlContent },
            success: function() {
                console.log('PDF downloaded successfully');
            }
        });
    }
</script>

<style type="text/css">
    .informeshon p, .invoice-name p, .inv-header-1{color: #ffffff;}
/*
    @page{size: landscape;}*/
    @page{size: portrait;}
    @media print{
        .invoice-3 .invoice-top, .invoice-3 .invoice-center, .invoice-3 .invoice-bottom{padding: 20px;}
        body {writing-mode: tb-rl;}
        table tr td{word-wrap: break-word; white-space: normal; color: #000000 !important;}
        .informeshon p{word-wrap: break-word; white-space: normal; color: #000000;}
        p{color: #000000 !important;}
        .informeshon p, .invoice-name p, .inv-header-1{color: #000000;}
    }
</style>
<div class="invoice-3 invoice-content" id="printDiv">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner">
                    <div class="invoice-info" id="invoice_wrapper">
                        <div class="invoice-top">
                            <table class="table">
                                <tr>
                                    <td>
                                        <div class="invoice">
                                            <h4 class="inv-header-1"><?php echo urldecode($_GET['title']); ?></h4>
                                            <h4 class="inv-header-1"><?php echo $data['transaction_number']; ?></h4>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="invoice-name text-end">
                                            <div class="logo text-white">
                                                <img style="width:150px; filter: brightness(2.5);" class="logo" src="<?php echo $print_img; ?>" alt="logo">
                                            </div>
                                            <p class="mt-4"><?php echo $config['contact_address']; ?></p>
                                            <!-- <p class="mb-0">Mississippi 96522</p> -->
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!-- <hr class="text-white"> -->
                            <table class="table mt-4">
                                <tr>
                                    <td>
                                        <div class="informeshon">
                                            
                                            <p> Tenant &nbsp;&nbsp;: &nbsp;&nbsp; <?php echo $data['party_id']; ?> - <?php echo $data['party_name']; ?><br/><br/>
                                            Email&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_email']; ?><br/><br/>
                                            Phone&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_phone']; ?>
                                            <br/><br/>
                                            Taxes&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_tax_number']; ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="informeshon">
                                            <p class="inv-title-1">Billing Adderss:</p>
                                            <p class="invo-addr-1">
                                                <?php echo $data['party_address']; ?> <br/>
                                            </p>
                                            <p class="inv-title-1">Shiping Adderss :</p>
                                            <p class="invo-addr-1">
                                                <?php echo $data['party_ship_adderss']; ?>   <br/>
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="informeshon">
                                            <p> Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; <?php echo $data['transaction_date']; ?><br/><br/>
                                            Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo "NET + ".$data['payment_terms']." DAYS"; ?><br/><br/>
                                            Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $data['place_of_supply']; ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="invoice-center">
                            <div class="table-section table-responsive clearfix">
                                <table class="table caption-top invoice-table">
                                    <thead class="bg-active">
                                    <tr>
                                        <th>Name</th>
                                        <th>Descrpition</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Tax</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;  foreach ($items as $item) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $item['item_name']; ?>
                                        </td>
                                        <td>
                                            <div class="item-desc-1">
                                                
                                                <small><?php echo $item['description']; ?></small>
                                            </div>
                                        </td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['item_price']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['tax_amount']); ?></td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($item['total_amount']+$item['tax_amount']); ?></td>
                                    </tr>
                                    <?php $i++; } ?>
                                    <tr>
                                        <td colspan="4" class="text-end">SubTotal</td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($data['amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">Tax</td>
                                        <td class="text-right"><?php echo customeummarmonyformate ($data['tax_amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Grand Total</td>
                                        <td class="text-right fw-bold"><?php echo customeummarmonyformate ($data['total_amount']); ?></td>
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
                                       <!--  <p class="mb-0">Taxes Included</p> -->
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const element = document.body; // The HTML element to convert
    //         html2pdf()
    //     .from(element)
    //     .save('document.pdf');
    //      window.close();
    // });


document.addEventListener('DOMContentLoaded', function() {
   // var element = document.body;
   var element = $("#printDiv").html();
    var opt = {
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 3 },
      jsPDF:        { unit: 'cm', format: 'letter', orientation: 'landscape' }
    };
    html2pdf().from(element).set(opt).toPdf().output('datauristring').then(function (pdfAsString) {
        // The PDF has been converted to a Data URI string and passed to this function.
        // Use pdfAsString however you like (send as email, etc)!
        var arr = pdfAsString.split(',');
        pdfAsString= arr[1];   
         //console.log(pdfAsString); 
        var data = new FormData();
        data.append("data" , pdfAsString);
        data.append("page" , 'storepdfsendmail');
        var xhr = new XMLHttpRequest();
        xhr.open('post','../ajax.php', true ); //Post the Data URI to php Script to save to server
        xhr.send(data);
        console.log(data);
    })
   // e.preventDefault();  //stop the browser from following
    //window.location.href = 'uploads/file.pdf';
});

</script>


