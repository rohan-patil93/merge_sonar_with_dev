<?php
$_SESSION['apply_js']['multiselest'] = 1;
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
$from_date = "";
$to_date = "";
$from_date = date('Y-m-01');
 $to_date =  date('Y-m-t');
 $select_items = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
  $from_date =  str_replace('/"', '-', $_POST['start_date']);
  $from_date =  date('Y-m-d', strtotime($from_date));
  $to_date =  str_replace('/"', '-', $_POST['to_date']);
  $to_date =  date('Y-m-d', strtotime($to_date));
  $select_items = $_POST['item'];
  $itemids = implode(",",(array)$_POST['item']);
}
$url = API."?mode=itemwarehouseledger&from_date=".$from_date."&to_date=".$to_date."&itemids=".$itemids;
$apires =  callAPI($url);
$result = json_decode($apires,1);
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr']=1;
foreach($result['data'] as $data ){
    $item_wise_data[$data['id']][] = $data;
    $itemdet[$data['id']] = $data;
}
$items = $result['items'];

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
        $title = "Item Ledger";
        echo $title;
        ?>
       </h5>

      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>


    <div class="card-body">
      <form method="post" action="">
      <div class="row col-lg-12 ">
        <?php include_once 'views/custom_standard_report_erpf_filter.php'; ?>
        <div class="col-lg-3">
           <span class="form-label"> Item &nbsp; </span>
           <div>
          <select aria-label="Item[]" class="js-example-basic-multiple" name="item[]"  >
            <option value="0"> Select Any one </option>
            <?php foreach($items as $item){ ?>
                <option <?php if(in_array($item['id'],(array)$select_items)){ echo "selected";
} ?> value="<?php echo $item['id']; ?>"><?php echo $item['sku']." - ".$item['item_name']; ?></option>
            <?php } ?>
          </select>
          </div>
          <?php if(count((array)$select_items)==0){ ?>
          <div class="text-danger f-w-600 f-14"> Please Select Any  </div>
          <?php } ?>
        </div>
        <div class="col-lg-1 ">
          <span class="form-label"> &nbsp; </span>
            <button type="submit" name="applyform" value="apply" class=" btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php include_once "custom_standard_top_print.php"; ?>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col"># </th>
            <th scope="col">ITEM CATEGORY </th>
            <th scope="col">ITEM TYPE </th>
            <th scope="col">ITEM NAME </th>
            <th scope="col">ITEM SKU </th>
            <th scope="col"> DOCUMENT REF NO</th>
             <th scope="col"> DOCUMENT NAME</th>
            <th scope="col">DOCUMENT DATE</th>
            <th scope="col" class="text-right">IN QUANTITY </th>
            <th scope="col" class="text-right">IN VALUE </th>
            <th scope="col" class="text-right">OUT QUANTITY </th>
            <th scope="col" class="text-right">OUT VALUE </th>
            <th scope="col" class="text-right">RUN QUANTITY </th>
            <th scope="col" class="text-right">RUN VALUE </th>
          </tr>
          <?php $sl=1; if(count((array)$item_wise_data)>0){
          foreach($item_wise_data as $key=>$item_wise){
            $balance = isset($result['open_balances'][$key]) ? $result['open_balances'][$key] : 0;
            $running_value = isset($result['running_value'][$key]) ? $result['running_value'][$key] : 0;

          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <td><?php echo  $itemdet[$key]['item_category_name']; ?></td>
            <td ><?php echo  $itemdet[$key]['item_type_name']; ?></td>
            <td><?php echo  $itemdet[$key]['item_name']; ?></td>
            <td ><?php echo  $itemdet[$key]['sku']; ?></td>
            <td ><?php echo  "Opeing Balance"; ?></td>
            <td ><?php echo  fromDate($from_date); ?></td>
            <td class="text-right"></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right"></td>
            <td class="text-right">-</td>
            <td class="text-right"><?php echo customonlynumberformate($balance); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($running_value); ?></td>
          </tr>

          <?php
           foreach($item_wise as $data ){
            $cssdata[] = array('Sl No'=> $sl,'ITEM NAME'=> $data['item_name'], 'ITEM SKU'=> $data['item_name'], 'OPENING STOCK'=> $data['open_qty'],'QUANTITY IN'=> $data['in_qty'],'QUANTITY OUT'=> $data['out_qty'],'CLOSING STOCK'=> $data['in_qty']-$data['out_qty']);
          ?>
         <tr>
            <td></td>
            <td></td>
            <td ></td>
            <td></td>
            <td ></td>
            <td><?php echo  $data['document_ref_no']; ?></td>
            <td><?php echo  ucwords(strtolower(str_replace('_', ' ', $data['document_type']))); ?></td>
            <td ><?php echo  fromDate($data['document_date']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['in_qty']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['in_value']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['out_qty']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['out_value']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['running_total']); ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['running_value']); ?></td>
          </tr>
          <?php
            $_SESSION['download_data'] = $cssdata;
            }
             $sl++;
          }
            ?>
            <?php
          }else{ ?>
          <tr>
            <td colspan="13">
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
