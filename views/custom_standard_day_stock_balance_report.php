<?php
$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$balance_date = $from_date = $_POST['balance_date'] ? date('Y-m-d', strtotime($_POST['balance_date'])) : date('Y-m-d');
$url = API . "?mode=togetstandarddaystockbalancereport&balance_date=".$balance_date;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$stock_list = $result['stock_list'];

$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5 class="">
                Day Stock Balance
            </h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                        data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Day Stock Balance" target="_blank">CSV</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="" id="datedelectpostform">
                <div class="row ">
                    <div class="col-lg-4">
                        <input aria-label="Balance Date" type="text" class="form-control datepicker" name="balance_date" id="balance_date" value="<?php echo fromDate($balance_date); ?>">
                    </div>
                    <div class="col-lg-2">
                        <span class="form-label"> &nbsp; </span>
                        <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text">
                            Apply
                        </button>
                        <input type="hidden" name="submit" value="applyform">
                    </div>
                </div>
            </form>
        </div>

   <div class="card-body">
  <?php if(count((array)$stock_list) !=0){ ?>
      <?php include_once "custom_standard_top_print.php"; ?>
  <div class="table-responsive">
    <table class="table " >
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">SKU</th>
                <th scope="col">Item Name</th>
                <th scope="col">Last Stock Update Date</th>
                <th scope="col">Opening Qty</th>
                <th scope="col">In Qty</th>
                <th scope="col">Out Qty</th>
                <th scope="col">Closing Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php $seqno = 1;
                foreach($stock_list as $stocklist){

                    $cssdata[] = array("SKU"=>$stocklist['sku'],"Item Name"=>$stocklist['item_name'],"Last Stock Update Date"=>fromDate($stocklist['balance_date']),"Opening Qty"=>$stocklist['opening_qty'],"In Qty"=>$stocklist['in_qty'],"Out Qty"=>$stocklist['out_qty'],"Closing Qty"=>$stocklist['closing_qty']);

                ?>
                <tr>
                    <td class="p-1" ><?php echo $seqno; ?></td>
                    <td class="p-1"><?php echo $stocklist['sku']; ?></td>
                    <td class="p-1 w-b-w-s"><?php echo $stocklist['item_name']; ?></td>
                    <td class="p-1"><?php echo fromDate($stocklist['balance_date']); ?></td>
                    <td class="text-right p-1"><?php echo $stocklist['opening_qty']; ?></td>
                    <td class="text-right p-1"><?php echo $stocklist['in_qty']; ?></td>
                    <td class="text-right p-1"><?php echo $stocklist['out_qty']; ?></td>
                    <td class="text-right p-1"><?php echo $stocklist['closing_qty']; ?></td>

                </tr>
             <?php $_SESSION['download_data'] = $cssdata;  $seqno++;
} ?>
        </tbody>
    </table>
</div>

<?php }else{ ?>

    <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Stock</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php } ?>
</div>
    </div>
</div>
