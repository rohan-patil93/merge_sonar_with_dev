<?php
$url = API . "?mode=toinsertreportviewdata&myreport_id=" . $_GET['RID'];
$apires = callAPI($url);
$postdata = array();
$from_date = "";
$to_date = "";

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
  $from_date = date('Y-m-01');
  $to_date = date('Y-m-t');
}
$url = API . "?mode=togetorderfulfilmentbyitemrepot&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$data_sales_orders = $result['data_sales_orders'];
$data_delivery_orders = $result['data_delivery_orders'];
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;

?>
<style type="text/css">
    .ontable tr td {
        vertical-align: middle;
        padding: 5px;
    }
</style>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5 class="f-30">
                <?php
                $title = "Order Fulfilment by Item";
                echo $title;
                ?>
            </h5>

            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                        data-dropdown-out="fadeOut" style="">

                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=Age Profile" target="_blank">CSV</a>
                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once "custom_standard_top_print.php"; ?>
<div class="card">
    <div class="card-body">
            <?php include_once 'date_filter_common.php'; ?>
            <hr>
        <div class="table-responsive mt-3">
            <table class="table table-hover">
                <tr style="background-color: #eeeeee;">
                    <th scope="col"># </th>
                    <th scope="col">ITEM NAME </th>
                    <th scope="col">SKU</th>
                    <th scope="col" class="text-right">TOTAL ORDERED QTY</th>
                    <th scope="col" class="text-right">FULFILLED QTY</th>
                    <th scope="col" class="text-right">BACKORDERED QTY</th>
                    <th scope="col" class="text-right">FULFILMENT RATE</th>
                </tr>
                <?php $sl = 1;
                if (count((array) $data_sales_orders) > 0) {
                    foreach ($data_sales_orders as $data) {
                        $do_detail = $data_delivery_orders[$data['item_id']];
                        if ($data['order_qty'] != "" && $data['order_qty'] != "0") {
                            $fullfilment_rate = ($do_detail['fulfilment_qty'] / $data['order_qty']) * 100;
                        } else {
                            $fullfilment_rate = "0";
                        }
                        $cssdata[] = array('Sl No' => $sl, 'ITEM NAME' => $data['item_name'], 'ITEM SKU' => $data['sku'], 'TOTAL ORDERED QTY' => $data['order_qty'], 'FULFILLED QTY' => $data['total'], 'BACKORDERED QTY' => ($data['order_qty'] - $do_detail['fulfilment_qty']), 'FULFILMENT RATE' => $fullfilment_rate);
                        ?>

                        <tr>
                            <td><?php echo $sl; ?></td>
                            <td><span class="" href=""><?php echo $data['item_name']; ?></span></td>
                            <td><?php echo $data['sku']; ?></td>
                            <td class="text-right"><?php echo customonlynumberformate($data['order_qty']); ?></td>
                            <td class="text-right"><?php echo customonlynumberformate($do_detail['fulfilment_qty']); ?></td>
                            <td class="text-right">
                                <?php echo customonlynumberformate($data['order_qty'] - $do_detail['fulfilment_qty']); ?>
                            </td>
                            <td class="text-right"><?php echo $fullfilment_rate; ?>%</td>

                        </tr>
                        <?php $sl++;
                    }
                    $_SESSION['download_data'] = $cssdata;
                    ?>
                    <?php
                } else { ?>
                    <tr>
                        <td colspan="9">
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data
                                    Available</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                        style="max-height: 300px!important;">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

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
