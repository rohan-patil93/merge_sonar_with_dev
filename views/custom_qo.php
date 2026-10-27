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
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$idata;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
foreach ($itemres as  $ite) {
    $itee[$ite['id']] = $ite['sku'];
}

$vdata1["where"] = array('vendor_id'=> $data['party_id']);
$vdata1["field"] = "id,vendor_part_no,item_id";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_VENDOR_ITEMS."&data=".$vdata;
$vdata = callAPI($vurl);
$vdata = json_decode($vdata, 1);
foreach ($vdata as  $vite) {
    $vitee[$vite['item_id']] = $vite['vendor_part_no'];
}

?>
 <style type="text/css">
     .align_center{
         text-align: center;
     }
     .align_right{
         text-align: right;
     }
     .discrwidth{
         width: 300px;
     }
 </style>

                        <div class="d-flex flex-column-fluid">
                            <!--begin::Container-->
                            <div class="container-fluid">
                                <!-- begin::Card-->
                                <div class="card card-custom overflow-hidden">
                                    <div class="card-body p-0">
                                        <!-- begin: Invoice-->
                                        <!-- begin: Invoice header-->
                                        <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                                            <div class="col-md-9">
                                                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                                    <div class="d-flex flex-column px-0">
                                                        <h5 class="display-4 font-weight-boldest mb-10"><?php echo "TAX INVOICE"; ?></h5>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-md-end px-0">
                                                        <?php $print_img ='uploads/all/'.$config['display_logo'];?>
                                                        <!--begin::Logo-->
                                                        <a href="#" class="mb-5">
                                                            <img src="<?php echo $print_img; ?>" alt="" />
                                                        </a>
                                                        <!--end::Logo-->
                                                        <span class="d-flex flex-column align-items-md-end opacity-70">
                                                            <span><?php echo $config['contact_address']; ?></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="border-bottom w-100"></div>
                                                <div class="d-flex justify-content-between pt-6">
                                                    <div class="d-flex flex-column flex-root">
                                                        <span class="font-weight-bolder mb-2"> <?php if($table == 'requisitions') { echo "Employee";
} else {  echo "Vendor";
}  ?> :&nbsp;&nbsp;<?php echo $data['party_id']; ?> - <?php echo $data['party_name']; ?></span>
                                <span class="font-weight-bolder mb-2">Email&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_email']; ?></span>
                                <span class="font-weight-bolder mb-2">Phone&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_phone']; ?> </span>
                                <span class="font-weight-bolder mb-2">Taxes&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;<?php echo $data['party_tax_number']; ?> </span>
                                                    </div>
                                                    <div class="d-flex flex-column flex-root">
                                                        <span class="font-weight-bolder mb-2"> Billing Adderss:</span>
                                <span class="font-weight-bolder mb-2"> <?php echo $data['party_address']; ?>  </span>
                                <span class="font-weight-bolder mb-2"> &nbsp;</span>
                                <span class="font-weight-bolder mb-2"> Shiping Adderss :</span>
                                <span class="font-weight-bolder mb-2"> <?php echo $data['party_ship_adderss']; ?>  </span>
                                                    </div>
                                                    <div class="d-flex flex-column flex-root">
                                                        <span class="font-weight-bolder mb-2">Transaction Date&nbsp;:&nbsp;&nbsp;<?php echo $data['transaction_date']; ?> </span>
                                <span class="font-weight-bolder mb-2">Currency &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $curre['currency_list']; ?></span>
                                <span class="font-weight-bolder mb-2"> Currency Rate &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo customeummarmonyformate($data['currency_rate']); ?> </span>
                                <span class="font-weight-bolder mb-2">Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $data['payment_terms']; ?> </span>
                                <span class="font-weight-bolder mb-2">Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $data['place_of_supply']; ?> </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end: Invoice header-->
                                        <!-- begin: Invoice body-->
                                        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                                            <div class="col-md-9">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                        <th scope="col" style="width: 2%;"># &nbsp;&nbsp;</th>
                                        <th scope="col" style="width: 5%;">SKU</th>
                                        <th scope="col" style="width: 5%;">NAME</th>
                                        <th scope="col" style="width: 7%;">DESCRIPTION</th>
                                        <?php $col=7; if($table != 'requisitions'){  $col=8;?>
                                        <th scope="col" style="width: 5%;"> VENDOR ITEM</th>
                                        <?php } ?>
                                        <th scope="col" class="align_center" style="width: 4%;">UOM</th>
                                        <th scope="col" class="align_center" style="width: 4%;">QTY</th>
                                        <th scope="col" class="align_center" style="width: 5%;">RATE</th>
                                        <th scope="col" class="align_center" style="width: 5%;">AMOUNT</th>
                                        <th scope="col" class="align_center" style="width: 5%;">TAX AMOUNT</th>
                                        <th scope="col" class="align_center" style="width: 7%;">TOTAL AMOUNT</th>
                                    </tr>
                                                        </thead>
                                                        <tbody>
                                    <?php $i=1;  foreach ($items as $item) { ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $itee[$item['item_id']]; ?></td>
                                        <td><?php echo $item['item_name']; ?></td>
                                        <td class="discrwidth"><?php echo $item['description']; ?></td>
                                        <?php if($table != 'requisitions'){ ?>
                                        <td><?php echo $vitee[$item['item_id']]; ?></td>
                                        <?php } ?>
                                        <td class="align_center"><?php echo $item['purchase_uom']; ?></td>
                                        <td class="align_center"><?php echo $item['item_quantity'] ;?></td>
                                        <td class="align_center"><?php echo customeummarmonyformate ($item['item_price']); ?></td>
                                        <td class="align_center"><?php echo customeummarmonyformate ($item['item_amount']); ?></td>
                                        <td class="align_center"><?php echo customeummarmonyformate ($item['tax_amount']); ?></td>
                                        <td class="align_right"><?php echo customeummarmonyformate ($item['total_amount']+$item['tax_amount']); ?></td>
                                    </tr>
                                <?php $i++;
} ?>
                                    <tr>
                                        <td colspan="<?php echo $col; ?>" class="align_right">TOTAL&nbsp;:&nbsp;&nbsp;</td>
                                        <td class="kt-font-danger kt-font-lg  align_center"><?php echo customeummarmonyformate ($data['amount']); ?></td>
                                        <td class="kt-font-danger kt-font-lg align_center"><?php echo customeummarmonyformate ($data['tax_amount']); ?></td>
                                        <td class="kt-font-danger kt-font-lg align_right"><?php echo customeummarmonyformate ($data['amount']+$data['tax_amount']); ?></td>
                                    </tr>
                                </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end: Invoice body-->
                                        <!-- begin: Invoice footer-->
                                        <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                                            <div class="col-lg-12">
                                                <div class=" col-lg-6 text-info" style="float: left;">
                                                    &nbsp;
                                                </div>
                                                <div class="col-lg-6" style="float: left;text-align:  right;">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="width: 20%;">DISCOUNT </th>
                                                                <th scope="col" style="width: 20%;">TOTAL AMOUNT</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="font-weight-bolder">
                                                                <td><?php echo customeummarmonyformate ($data['discount_amount']); ?></td>

                                                                <td class="text-danger font-size-h3 font-weight-boldest"><?php echo customeummarmonyformate ($data['total_amount']); ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class=" col-lg-12" >
                                                <div class="col-lg-12" style="text-align: center;">
                                                 <h5 class="text-success">Notes: <?php echo  numberToWordFormateRupees($data['total_amount']);  ?> </h5>
                                                    </div>
                                            </div>
                                        </div>
                                        <!-- end: Invoice footer-->
                                        <!-- begin: Invoice action-->
                                        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                                            <div class="col-md-9">
                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-light-primary font-weight-bold" onclick="window.print();">Download Invoice</button>
                                                    <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print Invoice</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end: Invoice action-->
                                        <!-- end: Invoice-->
                                    </div>
                                </div>
                                <!-- end::Card-->
                            </div>
                            <!--end::Container-->
                        </div>

