<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$shortcode = $_POST['shortcode_date'] ?? "TM";
$pick_date = $_POST['pick_date'] ?? "This Month";
$transaction_table = "sales_orders";

$from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
$to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
$url = API . "?mode=togetstandardtransactiondetailsreports&from_date=".$from_date."&to_date=".$to_date."&transaction_table=".$transaction_table;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$transaction_list = $result['transaction_list'];
$unit_of_measurements = $result['unit_of_measurements'];
$items = $result['items'];

$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<script>
    function dynamicSelectDateRange(type, name, start = "", end = "") {
        $("#btn_date").text(name);
        $("#pick_date").val(name);
        $("#shortcode_date").val(type);
        $(".empty_active").removeClass("active");
        $("#" + type).addClass("active");
        if (name == "Custom Date") {
            $("#custome_date").show('slow');

        } else {
            $("#custome_date").hide();
            $("#from_date").val(start);
            $("#to_date").val(end);
        }

    }
</script>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5 class="">
                Sales Order Detail Report
            </h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                        data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo str_replace("_", " ", $transaction_table); ?>" target="_blank">CSV</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="" id="datedelectpostform">
                <div class="row ">
                    <div class="col-lg-4 mb-2">
                        <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $pick_date; ?>">
                        <input type="hidden" name="shortcode_date" id="shortcode_date"
                            value="<?php echo $shortcode; ?>">
                        <span class="form-label">Select Date Range</span>
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                                <i class="ti-calendar"></i> <span id="btn_date"><?php echo $pick_date; ?></span>
                            </button>
                            <button type="button"
                                class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="width: 20%;height: 40px;">
                                <span class="visually-hidden">Toggle</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item <?php echo $shortcode == "TD" ? "active" : ""; ?>" id="TD"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date('Y-m-d')); ?>','<?php echo fromDate(date('Y-m-d')); ?>')">Today</a>
                                <a class="dropdown-item <?php echo $shortcode == 'YD' ? 'active' : ''; ?>" id="YD"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime('-1 day'))); ?>','<?php echo fromDate(date('Y-m-d', strtotime('-1 day'))); ?>')">Yesterday</a>
                                <a class="dropdown-item <?php echo $shortcode == 'TW' ? 'active' : ''; ?>" id="TW"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date('Y-m-d', strtotime('monday this week'))); ?>','<?php echo fromDate(date('Y-m-d', strtotime('sunday this week'))); ?>')">This
                                    Week</a>
                                <a class="dropdown-item <?php echo $shortcode == 'TM' ? 'active' : ''; ?>" id="TM"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date('Y-m-01')); ?>','<?php echo fromDate(date('Y-m-t')); ?>')">This
                                    Month</a>
                                <a class="dropdown-item <?php echo $shortcode == 'TY' ? 'active' : ''; ?>" id="TY"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date('Y-01-01')); ?>','<?php echo fromDate(date('Y-12-31')); ?>')">This
                                    Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item <?php echo $shortcode == 'PM' ? 'active' : ''; ?>" id="PM"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date('Y-m-01', strtotime('last month'))); ?>','<?php echo fromDate(date('Y-m-t', strtotime('last month'))); ?>')">Previous
                                    Month</a>
                                <a class="dropdown-item <?php echo $shortcode == 'PY' ? 'active' : ''; ?>" id="PY"
                                    href="#!"
                                    onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date('Y-01-01', strtotime('last year'))); ?>','<?php echo fromDate(date('Y-12-31', strtotime('last year'))); ?>')">Previous
                                    Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item <?php echo $shortcode == 'CD' ? 'active' : ''; ?>" id="CD"
                                    href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                            </div>
                        </div>

                        <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none'; ?>"
                            id="custome_date">
                            <div class="col-lg-6">
                                <input aria-label="From" type="text" class="form-control datepicker" name="from" id="from_date"
                                    value="<?php echo fromDate($from_date); ?>">
                            </div>
                            <div class="col-lg-6">
                                <input aria-label="To" type="text" class="form-control datepicker" name="to" id="to_date"
                                    value="<?php echo fromDate($to_date); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 mt-1">
                        <span class="form-label"> &nbsp; </span>
                        <button type="submit" name="applyform" value="apply" class="mt-4 btn btn-info waves-effect waves-light input-group-text">
                            Apply
                        </button>
                        <input type="hidden" name="submit" value="applyform">
                    </div>
                </div>
            </form>
        </div>

   <div class="card-body">
  <?php if(count((array)$transaction_list) !='0'){ ?>
      <?php include_once "custom_standard_top_print.php"; ?>
  <div class="table-responsive">
    <table class="table " >
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">SOB no</th>
                <th scope="col">Sob Date</th>
                <th scope="col">Ref Trans No</th>
                <th scope="col">Ref Trans Date</th>
                <th scope="col">Ref Doc No</th>
                <th scope="col">Ref Doc Date</th>
                <th scope="col">Party Name</th>
                <th scope="col">Part Number</th>
                <th scope="col">Item Name</th>
                <th scope="col">Item Desc</th>
                <th scope="col" class="text-right">Quantity</th>
                 <th scope="col" class="text-right">Pending Qty</th>
                <th scope="col">UOM</th>
                <th scope="col" class="text-right">Rate</th>
                <th scope="col" class="text-right">value</th>
                <th scope="col" class="text-right">CGST</th>
                <th scope="col" class="text-right">SGST</th>

                <th scope="col" class="text-right">IGST</th>
                <th scope="col" class="text-right">Discount</th>
                <th scope="col" class="text-right">Additional charges</th>
                <th scope="col" class="text-right">Sub Total</th>
                <th scope="col" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $seqno = 1; $cssdata = array(); foreach($transaction_list as $tranlist){
                $rowspan = count((array)$tranlist);
                $parshowcnt = 1;
                foreach($tranlist as $tranitem){

                    $exceldata = array('SOB no'=>emptytextdashShow($tranlist[0]['transaction_number']) ,'Sob Date'=>emptytextdashShow(fromdate($tranlist[0]['transaction_date'])),'Ref Trans No'=>emptytextdashShow($tranlist[0]['ref_no']),'Ref Trans Date'=>emptytextdashShow(!empty($tranlist[0]['ref_date'] && $tranlist[0]['ref_date'] != '1970-01-01')? fromdate($tranlist[0]['ref_date']) : '-'),'Ref Doc No'=>emptytextdashShow($tranlist[0]['doc_ref_no']),'Ref Doc Date'=>emptytextdashShow(!empty($tranlist[0]['doc_ref_date']) ? fromdate($tranlist[0]['doc_ref_date']) : ''),'Party Name'=>emptytextdashShow($tranlist[0]['party_name']),'Part Number'=>emptytextdashShow($items[$tranitem['item_id']]['sku']),'Item Name'=>emptytextdashShow($tranitem['item_name']),'Item Desc'=>emptytextdashShow($tranitem['description']), 'Quantity'=>emptytextdashShow($tranitem['item_quantity']), 'pending_qty'=>emptytextdashShow($tranitem['pending_qty']), 'UOM'=>emptytextdashShow($unit_of_measurements[$tranitem['purchase_uom']]),'Rate'=>emptytextdashShow($tranitem['item_price']),'value'=>emptytextdashShow($tranitem['item_amount']),'CGST'=>emptytextdashShow(($tranitem['originator_state_id'] != $tranitem['supplier_state_id'])?"-":($tranitem['tax_amount']/2)),'SGST'=>emptytextdashShow(($tranitem['originator_state_id'] != $tranitem['supplier_state_id'])?"-":($tranitem['tax_amount']/2)),'IGST'=>emptytextdashShow(($tranitem['originator_state_id'] != $tranitem['supplier_state_id'])?$tranitem['tax_amount']:"-"),'Discount'=>emptytextdashShow($tranitem['discount_amount']),'Additional charges'=>emptytextdashShow($tranlist[0]['shipping_amount']+$tranlist[0]['shipping_tax_amount']),'Sub Total'=>emptytextdashShow($tranitem['total_amount']+$tranitem['tax_amount']));
                    if($parshowcnt == 1){
                        $exceldata['Total'] = $tranlist[0]['par_total_amount'];
                    }
                    $cssdata[] = $exceldata;
                ?>
                <tr>
                    <?php if($parshowcnt == 1){ ?>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $seqno; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $tranlist[0]['transaction_number']; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo fromdate($tranlist[0]['transaction_date']); ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $tranlist[0]['ref_no']; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo !empty($tranlist[0]['ref_date'] && $tranlist[0]['ref_date'] != '1970-01-01')? fromdate($tranlist[0]['ref_date']) : '-'; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $tranlist[0]['doc_ref_no']; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo !empty($tranlist[0]['doc_ref_date']) ? fromdate($tranlist[0]['doc_ref_date']) : ''; ?></td>
                    <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $tranlist[0]['party_name']; ?></td>
                    <?php } ?>
                    <td class="p-1"><?php echo $items[$tranitem['item_id']]['sku']; ?></td>
                    <td class="p-1"><?php echo $tranitem['item_name']; ?></td>
                    <td class="p-1"><?php echo $tranitem['description']; ?></td>

                    <td class="text-right p-1"><?php echo $tranitem['item_quantity']; ?></td>
                    <td class="text-right p-1"><?php echo $tranitem['pending_qty']; ?></td>
                    <td class="p-1"><?php echo $unit_of_measurements[$tranitem['purchase_uom']]; ?></td>
                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['item_price']); ?></td>
                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['item_amount']); ?></td>
                    <?php if($tranitem['originator_state_id'] != $tranitem['supplier_state_id']){ ?>
                    <td class="text-right p-1">-</td>
                    <td class="text-right p-1">-</td>
                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['tax_amount']); ?></td>
                    <?php }else{ ?>

                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['tax_amount']/2); ?></td>
                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['tax_amount']/2); ?></td>
                    <td class="text-right p-1">-</td>
                    <?php } ?>
                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['discount_amount']); ?></td>
                    <?php if($parshowcnt == 1){ ?>
                    <td class="p-1 text-right" rowspan="<?php echo $rowspan; ?>">
                        <?php
                            $additional_charges = json_decode($tranlist[0]['additional_charges'], 1);
                            if(count((array)$additional_charges) != 0){
                                foreach((array)$additional_charges as $addchrg){
                                    echo $addchrg['charge_name']." -- ".$addchrg['shipping_amount']." (".$addchrg['shipping_tax']."%)";
                                }
                            }else{
                                echo $tranlist[0]['shipping_amount']+$tranlist[0]['shipping_tax_amount'];
                            }
                        ?>
                    </td>
                    <?php } ?>

                    <td class="text-right p-1"><?php echo customeummarmonyformate($tranitem['total_amount']+$tranitem['tax_amount']); ?></td>
                    <?php if($parshowcnt == 1){ ?>
                        <td class="p-1" rowspan="<?php echo $rowspan; ?>"><?php echo $tranlist[0]['par_total_amount']; ?></td>
                    <?php } ?>

                </tr>
                 <?php $parshowcnt++; $_SESSION['download_data'] = $cssdata;
}  ?>
            <?php $seqno++;
} ?>
        </tbody>
    </table>
</div>

<?php }else{ ?>

    <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Transactions</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php } ?>
</div>
    </div>
</div>
