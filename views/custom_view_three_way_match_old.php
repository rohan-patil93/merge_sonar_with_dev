<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url); 
$postdata = array();
$from_date = "";
$to_date = "";
if(isset($_POST['applyform'])) {
  //echo "<pre>"; print_r($_POST); echo "</pre>";
  $postdata = $_POST;
  $from_date =  str_replace('/"', '-', $_POST['start_date']);
  $from_date =  date('Y-m-d', strtotime($from_date));
  $to_date =  str_replace('/"', '-', $_POST['to_date']);
  $to_date =  date('Y-m-d', strtotime($to_date));
}
$url = API."?mode=togetthreewaymatch&from_date=".$from_date."&to_date=".$to_date;
$apires =  callAPI($url);
$result = json_decode($apires,1);
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr']=1;

?>
<style type="text/css">
  .ontable tr td{
    vertical-align: middle;
    padding: 5px;
  }
</style>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">
        <?php 
        $title = "Product Sales";
        echo $title;
         ?>
        </h5>
      <!-- <h6 class="mt-4"></h6> -->
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
           <!--  <a class="dropdown-item waves-light waves-effect" href="#">PDF</a> -->
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form method="post" action="">
      <div class="row col-lg-12 ">
        <?php include('views/custom_standard_report_erpf_filter.php'); ?>
        <div class="col-lg-2 mt-1">
          <label class="form-label"> &nbsp; </label>
            <button type="submit" name="applyform" value="apply" class=" mt-3 btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </form>
      </div>
    </div>
  </div>

<?php include("custom_standard_top_print.php"); ?>
  
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
           <tr bgcolor="#eeeeee">
            <th colspan="3" class="text-center" >ITEM </th>            
            <th colspan="2" class="text-center" > PURCHAE ORDER </th>
            <th colspan="2" class="text-center" > GRN </th>
            <th colspan="2" class="text-center" > SALE INVOICES</th>
            <th colspan="2" class="text-center" > MATCH</th>
          </tr>
          <tr bgcolor="#eeeeee">
            <th># </th>
            <th>ITEM NAME </th>
            <th>ITEM SKU </th>
            <th>QTY</th>
            <th class="text-right">AMOUNT</th>
            <th class="text-right">QTY </th>
            <th class="text-right">AMOUNT</th>
            <th class="text-right">QTY</th>
            <th class="text-right">AMOUNT</th>
            <th class="text-left">Match</th>
            <th class="text-left">ACTION</th>
          </tr>
          <?php

          $match_resaon = array('Matched', 'Missing GRN', 'Missing Sales Invoice', 'Dedit Note', 'Creadit Note');
           $sl=1; if(count((array)$result['data'])>0){
           foreach($result['data'] as $data ){ 
            $val = 0;
            if($data['grn_qty'] > $data['so_qty']){
              $val = 1;
            }else if($data['grn_qty'] < $data['so_qty']){
               $val = 2;
            }
            //$cssdata[] = array('Sl No'=> $sl,'ITEM NAME'=> $data['po_item_name'], 'ITEM SKU'=> $data['item_name'], 'MARGIN'=> customonlynumberformate($data['margin']),'QUANTITY SOLD'=> customonlynumberformate($data['qty']),'SALES PRICE'=> customonlynumberformate($data['sale_price']*$data['qty']),'SALES WITH TAX'=> customonlynumberformate($data['salewithprice']*$data['qty']));
          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <td><span class="" href=""><?php echo $data['po_item_name']; ?></span></td>
            <td><span class="" href=""><?php echo $data['po_item_sku']; ?></span></td>
            <td class="text-right"><?php echo customonlynumberformate($data['po_qty']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['po_item_price']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['grn_qty']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['grn_item_price']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['so_qty']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['so_item_price']); ?></td>
            <td class="text-right"><?php echo $match_resaon[$val]; ?></td>
            <td></td>
          </tr>
          <?php $sl++; } 
            $_SESSION['download_data'] = $cssdata;
            ?> 
            <?php 
          }else{ ?>
          <tr>
            <td colspan="11">
              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>   
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>   
              </div>
            </td>
          </tr>
        <?php } ?>
        </table>
      </div> 
    </div>
  </div>
</div>