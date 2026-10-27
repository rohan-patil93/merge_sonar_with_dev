<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$part_table = $_POST['part_table'];
$party_id = $_POST['party_id'];
$vurl = API."?mode=getpartydetails&part_table=".$part_table."&party_id=".$party_id;
$data_details = callAPI($vurl);
$result = json_decode($data_details,1);
$vendordetail = $result['party_detail'];
$invoice_detail = $result['invoice_detail'];
$indentings = $quotations = $purchase_orders = $purchase_invoice = array();
if($invoice_detail['req_cnt'] !=""){
    $indentings = explode('||', $invoice_detail['req_cnt']);
}
if($invoice_detail['quo_cnt'] !=""){
    $quotations = explode('||', $invoice_detail['quo_cnt']);
}
if($invoice_detail['po_cnt'] !=""){
    $purchase_orders = explode('||', $invoice_detail['po_cnt']);
}
if($invoice_detail['pi_cnt'] !=""){
    $purchase_invoice = explode('||', $invoice_detail['pi_cnt']);
}
$all_transactions = $result['all_transactions'];
$pending_amt_transactions = $result['pending_amt_transactions'];
$transaction_items = $result['transaction_items'];
if($part_table == "vendors"){
    $urloption = "index?option=COM_INSERT_SUPPLIER_BILLS";
}else{
    $urloption = "index?option=COM_INSERT_SALES_INVOICES";
}

?>
<div class="modal-header">
    <h5 class="modal-title"> Party Details </h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="">
    <div class="card mb-1 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <div class="banner">
                <img src="assets/images/user-profile/bg-img1.jpg" class="w-100 b-radius-20" alt="">
            </div>
        </div>
        <div class="profile-img" style="margin:-40px 0 0 0;">
            <div class="row align-items-bottom">
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
                        <form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
                            <label class="btn-hover-text-primary position-absolute  text-white" data-action="remove" data-bs-toggle="tooltip" title="Edit Profile Pic" style="left: -3px; top: -2px;">
                                <span><i class="ti-pencil-altj"></i></span>
                                <input aria-label="Profile Pic File" type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg"  onchange="updateProfilePic()" />
                            </label>
                            <?php if($vendordetail['image']!=""){ ?>
                                <img src="<?php echo $image; ?>" class="img-radius img-90" alt="">
                            <?php  }else{ ?>
                                <div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
                                    <span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40" style=" border-radius: 50%; line-height: 1.1;"><?php echo $vendordetail['display_name']!=""?initials($vendordetail['display_name']):"NA"; ?></span>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                    <div class="mt-3 d-block mb-3">
                        <div class="d-flex mr-3 mb-2">
                            <?php if($vendordetail['company'] !=""){ ?>
                                <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
                                <?php echo $vendordetail['company'];?>
                                    <small class="d-block f-14"><?php echo $vendordetail['display_name'];?></small>
                                </a>
                            <?php }else{  ?>
                                <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
                                <?php echo $vendordetail['display_name'];?></a>
                            <?php } ?>
                        </div>
                        <?php if($vendordetail['email']!=""){ ?>
                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $vendordetail['email'];?></a>
                            <?php }
if($vendordetail['phone']!=""){  ?>
                                <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                    <i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo DynamicPhoneNumberFormt($vendordetail['phone']);?></a>
                                <?php }  ?>
                                <?php if($vendordetail['billing_address']!=""){ ?>
                                    <a class="font-weight-bold text-dark-50 d-block mb-2 mt-2"><i class="ti-location-pin text-primary f-18"></i>&nbsp;<?php echo $vendordetail['billing_address']." ".$vendordetail['post_code'];?> </a>
                                <?php } ?>
                                <div class="font-weight-bold text-dark-50 mt-1 f-14">
                                    <?php if($vendordetail['established_year']!="" && $vendordetail['established_year']!="0"){  ?>
                                        <b class="f-w-600">Established Year : </b><?php echo $vendordetail['established_year'];?>&nbsp;
                                    <?php }  ?>
                                    <?php if($vendordetail['credit_limit']!=""){  ?>
                                        <b class="f-w-600">Credit Limit : </b><?php echo customeNumberFormate($vendordetail['credit_limit']);?>&nbsp;
                                    <?php }  ?>

                                </div>
                                <div class="font-weight-bold text-dark-50 mt-1 f-14">
                                    <?php if($vendordetail['payment_term_name']!=""){  ?>
                                        <b class="f-w-600">Payment Terms : </b><?php echo $vendordetail['payment_term_name'];?>&nbsp;
                                    <?php }  ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 p-3">
                             <div class="row mt-5">
                                <div class="col-6 b-l-default text-center">
                                    <div class="s-caption  text-info"><i class="ti-file f-20"></i></div>
                                    <span class="f-w-700 m-b-0"><?php echo $quotations[2]; ?> </span>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $quotations[0] !=""?customeNumberFormate($quotations[0]):0; ?></span>
                                                <p class="m-b-0">Documents</p>
                                            </div>
                                        </div>
                                        <div class="col-6 b-l-default">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $quotations[1] !=""?convert_Rupee_Number($quotations[1]):0; ?></span>
                                                <p class="m-b-0">Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="s-caption  text-primary"><i class="ti-file f-20"></i></div>
                                    <span class="f-w-700 m-b-0"><?php echo $purchase_orders[2]; ?> </span>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $purchase_orders[0] !=""?$purchase_orders[0]:0; ?></span>
                                                <p class="m-b-0">Documents</p>
                                            </div>
                                        </div>
                                        <div class="col-6 b-l-default">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $purchase_orders[1] !=""?convert_Rupee_Number($purchase_orders[1]):0; ?></span>
                                                <p class="m-b-0">Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6 text-center">
                                    <div class="s-caption  text-primary"><i class="ti-file f-20"></i></div>
                                    <span class="f-w-700 m-b-0"><?php echo $purchase_invoice[3]; ?></span>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $purchase_invoice[0] !=""?$purchase_invoice[0]:0; ?></span>
                                                <p class="m-b-0">Documents</p>
                                            </div>
                                        </div>
                                        <div class="col-6 b-l-default">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $purchase_invoice[1] !=""?convert_Rupee_Number($purchase_invoice[1]):0; ?></span>
                                                <p class="m-b-0">Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 text-center text-danger">
                                    <div class="s-caption  text-primary"><i class="ti-file f-20"></i></div>
                                    <span class="f-w-700 m-b-0">Due</span>
                                    <div class="row">
                                        <div class="col-12 b-l-default">
                                            <div class="s-cont d-inline-block">
                                                <span class="f-w-700 m-b-0"><?php echo $purchase_invoice[2] !=""?convert_Rupee_Number($purchase_invoice[2]):0; ?></span>
                                                <p class="m-b-0">Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row modal-body">
                <div class="col-lg-8">
                    <div class="card sale-card mb-2">
                        <div class="card-header">
                            <h5>All Documents <span class="form-label label label-md label-primary"><?php echo count((array)$pending_amt_transactions); ?></span></h5>
                            <div class="card-header-right table-card-header">
                                 <div class="input-group input-group-button mb-0">
                                    <input type="text" id="dynamicfilterhideshow" data-id="all_documents" data-type="tr" class="form-control form-control-sm width1 fill" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                    <span class="input-group-text addon-style">
                                        <span class=""><span class="ti-search text-white mt-2"></span></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="custom-scroll" style="height: 300px;">
                                <?php if(count((array)$all_transactions) != 0){ ?>
                                    <table class="table table-hover m-b-0 register_vendor_filter" style="font-size:13px;" >
                                        <thead>
                                            <tr>
                                                <th scope="col" class="p-2">Inv. No.</th>
                                                <th scope="col" class="p-2">Date</th>
                                                <th scope="col" class="p-2 text-right">Amount</th>
                                                <th scope="col" class="p-2 text-right">Shipping</th>
                                                <th scope="col" class="p-2 text-right">Discount</th>
                                                <th scope="col" class="p-2 text-right">Tax</th>
                                                <th scope="col" class="p-2 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="all_documents">
                                            <?php foreach($all_transactions as $trans){
                                                $url = $urloption."&Rid=".deocdeId('id:'.$trans['id']);
                                                ?>
                                            <tr class="showallemp" <?php echo $trans['cancel_invoice'] =="Y"?"style='text-decoration: line-through;'":""; ?>>
                                                <td class="p-2"><a class="text-primary" href="<?php echo $url; ?>" target="_blank"><?php echo $trans['transaction_number']; ?></a></td>
                                                <td class="p-2"><?php echo fromDate($trans['transaction_date']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['amount']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['shipping_amount']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['discount_amount']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['tax_amount']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['total_amount']); ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php }else{ ?>
                                    <div class="card-px text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> No Documents</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                <?php }  ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card sale-card mb-2">
                        <div class="card-header">
                            <h5>Due <span class="form-label label label-md label-danger"><?php echo count((array)$pending_amt_transactions); ?></span></h5>
                            <div class="card-header-right table-card-header">
                                 <div class="input-group input-group-button mb-0">
                                    <input type="text" id="dynamicfilterhideshow" data-id="pending_payment_documents" data-type="tr" class="form-control form-control-sm width1 fill" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                    <span class="input-group-text addon-style">
                                        <span class=""><span class="ti-search text-white mt-2"></span></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="custom-scroll" style="height: 300px;">
                                <?php if(count((array)$pending_amt_transactions) != 0){ ?>
                                    <table class="table table-hover m-b-0 register_vendor_filter" style="font-size:13px;" >
                                        <thead>
                                            <tr>
                                                <th scope="col" class="p-2">Inv/Due Date</th>
                                                <th scope="col" class="p-2 text-right">Amount</th>
                                                <th scope="col" class="p-2 text-right">Pending Amt</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pending_payment_documents">
                                            <?php foreach($pending_amt_transactions as $trans){
                                                $url = $urloption."&Rid=".deocdeId('id:'.$trans['id']);
                                                ?>
                                            <tr class="showallemp" <?php echo $trans['cancel_invoice'] =="Y"?"style='text-decoration: line-through;'":""; ?>>
                                                <td class="p-2"><a class="text-primary" href="<?php echo $url; ?>" target="_blank"><?php echo $trans['transaction_number']; ?><span class="d-block f-12"><?php echo fromDate($trans['due_date']); ?></span></a></td>

                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['total_amount']); ?></td>
                                                <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['pending_amount']); ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php }else{ ?>
                                    <div class="card-px text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> No Documents</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                <?php }  ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="card sale-card mb-2">
            <div class="card-header">
                <h5>Items <span class="form-label label label-md label-danger"><?php echo count((array)$transaction_items); ?></span></h5>
                <div class="card-header-right table-card-header">
                     <div class="input-group input-group-button mb-0">
                        <input type="text" id="dynamicfilterhideshow" data-id="item_documents" data-type="tr" class="form-control form-control-sm width1 fill" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                        <span class="input-group-text addon-style">
                            <span class=""><span class="ti-search text-white mt-2"></span></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="custom-scroll" style="height: 300px;">
                    <?php if(count((array)$transaction_items) != 0){ ?>
                        <table class="table table-hover m-b-0 register_vendor_filter" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col" class="p-2">Name</th>
                                    <th scope="col" class="p-2 text-right">Quantity</th>
                                    <th scope="col" class="p-2 text-right">Item Price</th>
                                    <th scope="col" class="p-2 text-right">Item Amount</th>
                                    <th scope="col" class="p-2 text-right">Tax Amount</th>
                                    <th scope="col" class="p-2 text-right">Total Amount</th>
                                     <th scope="col" class="p-2 text-right">AVG Price</th>
                                </tr>
                            </thead>
                            <tbody id="item_documents">
                                <?php foreach($transaction_items as $trans){
                                    ?>
                                <tr class="showallemp">
                                    <td class="p-2"><?php echo $trans['item_name']; ?></td>
                                    <td class="p-2 text-right"><?php echo customeNumberFormate($trans['item_quantity']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['item_price']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['item_amount']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['tax_amount']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['total_amount']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($trans['item_amount']/$trans['item_quantity']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php }else{ ?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> No Items</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php }  ?>
                </div>
            </div>
        </div>
        <div class="card sale-card mb-2">
            <div class="card-header">
                <h5>Ageing Report</h5>
                <div class="card-header-right"></div>
            </div>
            <div class="card-body">
                <?php
                $dretrundataind = array( '15'=>0, '30'=>0, '45'=>0, '60'=>0, '90'=>0);
                $retrundataind = $retrundata  = $returnvendorind = $vendortotal = $allids = $billdays = $vendordaytotal = $vendordayleveltotal = $returnvendorind = array();
                foreach($result['bills'] as $data1){
                    $payment_term_history = json_decode($data1['payment_term_history'],1);
                    foreach($payment_term_history as $data){
                        $todaydate = date('Y-m-d');
                        $sdate = new DateTime($data['due_date']);
                        $edate = new DateTime($todaydate);
                        $totald= $sdate->diff($edate);
                        $totaldays = $totald->days;
                        if($totaldays < 15){
                            $payment_terms = 15;
                        }elseif($totaldays < 30){
                            $payment_terms = 30;
                        }elseif($totaldays < 45){
                            $payment_terms = 45;
                        }elseif($totaldays < 60){
                            $payment_terms = 60;
                        }elseif($totaldays < 90){
                           $payment_terms = 90;
                        }else{
                           $payment_terms = 120;
                        }
                        $retrundata[$payment_terms][] = $data;
                        $retrundataind[$payment_terms] = $retrundataind[$payment_terms] + $data['amount'];
                        $total =  $total + $data['amount'];
                        $returnvendorind[$data['party_id']][$payment_terms] = $returnvendorind[$data['party_id']][$payment_terms] +$data['amount'];
                        $vendortotal[$data['party_id']] = $vendortotal[$data['party_id']] + $data['amount'];
                        $allids[] = $data['id'];
                        $billdays[$payment_terms][] = $data['id'];
                        $vendordaytotal[$data['party_id']][] = $data['id'];
                        $vendordayleveltotal[$data['party_id']][$payment_terms][] = $data['id'];
                    }
                }
                $fistdonut = 1;
                foreach($dretrundataind as $key=>$rfrr){
                   $gdata[] = $key." days :".($retrundataind[$key]!="" ? $retrundataind[$key] : 0);
                   if($retrundataind[$key] !="0" && $retrundataind[$key] !=""){
                      $fistdonut = 0;
                   }
                }
                ?>
                <style type="text/css">
                .ontable tr td{
                  vertical-align: middle;
                  padding: 5px;
                }
                </style>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="card-block p-b-0">
                            <div class="table-responsive">
                                <table class=" table table-hover m-b-0 m-t-5">
                                      <thead>
                                        <th scope="col"> <?php echo ucwords($part_table); ?> </th>
                                        <th scope="col" class="text-right">Total </th>
                                        <th scope="col" class="text-right">15 Days</th>
                                        <th scope="col" class="text-right">30 Days</th>
                                        <th scope="col" class="text-right">45 Days</th>
                                        <th scope="col" class="text-right">60 Days</th>
                                        <th scope="col" class="text-right">90 Days</th>
                                        <th scope="col" class="text-right"> > 90 Days</th>
                                    </thead>
                                      <tbody>
                                    <?php
                                    foreach ($vendortotal as $vendor_id => $value) {
                                      $one = "'".implode("-",(array)$vendordaytotal[$vendor_id])."'";
                                      $two = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['15'])."'";
                                      $three = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['30'])."'";
                                      $four = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['45'])."'";
                                      $five = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['60'])."'";
                                      $six = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['90'])."'";
                                      $seven = "'".implode("-",(array)$vendordayleveltotal[$vendor_id]['120'])."'";
                                    ?>
                                    <tr>
                                        <td class="text-capitalize p-2"><?php echo $vendordetail['company'];?> <span class="d-block f-12"><?php echo $vendordetail['display_name'];?></span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer"><?php echo $value!=""?customeummarmonyformate($value):customeummarmonyformate(0); ?> </span> </td>
                                        <td class="text-right p-2"> <span class="cursor-pointer"><?php echo $returnvendorind[$vendor_id]['15']!=""?customeummarmonyformate($returnvendorind[$vendor_id]['15']):customeummarmonyformate(0);  ?> </span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer" ><?php echo $returnvendorind[$vendor_id]['30']!=""?customeummarmonyformate($returnvendorind[$vendor_id]['30']):customeummarmonyformate(0); ?></span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer" ><?php echo $returnvendorind[$vendor_id]['45']!=""?customeummarmonyformate($returnvendorind[$vendor_id]['45']):customeummarmonyformate(0); ?></span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer" ><?php echo $returnvendorind[$vendor_id]['60'] !=""?customeummarmonyformate($returnvendorind[$vendor_id]['60']):customeummarmonyformate(0);customeummarmonyformate(0); ?></span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer"><?php echo $returnvendorind[$vendor_id]['90']!=""?customeummarmonyformate($returnvendorind[$vendor_id]['90']):customeummarmonyformate(0); ?></span></td>
                                        <td class="text-right p-2"> <span class="cursor-pointer" ><?php echo $returnvendorind[$vendor_id]['120']!=""?customeummarmonyformate($returnvendorind[$vendor_id]['120']):customeummarmonyformate(0); ?></span></td>
                                    </tr>
                                  <?php  } ?>
                                      </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                          <input type="hidden" name="chart_data_gender" id="chart_data_bill_vendor" value="<?php echo implode("||",(array)$gdata); ?>" >
                          <?php if($fistdonut =='1'){ ?>
                              <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php }else{ ?>
                              <div id="chart_Donut_gender" style="width: 100%; height: 200px;"></div>
                        <?php } ?>
                    </div>
                </div>
                   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
                   <script type="text/javascript" src="assets/js/custom_rental_aging_and_projection_reports.js"></script>
            </div>
        </div>
    </div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
