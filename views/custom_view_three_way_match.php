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
  $select_items = $_POST['vendor'];
  $vendorpass = implode(",",(array)$_POST['vendor']);
}
$url = API."?mode=togetthreewaymatch&from_date=".$from_date."&to_date=".$to_date."&vendors=".$vendorpass;
$apires =  callAPI($url);
$result = json_decode($apires,1);
$datas =  $result['data'];
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr']=1;
$array = array('transaction_number', 'transaction_date', 'vendor_name', 'payment_terms', 'amount', 'tax_amount', 'discount_amount', 'total_amount',  'grn_no', 'po_item_quantity' , 'sb_item_quantity', 'grn_item_quantity', 'qty_remark', 'price_remark', 'tax_remark', 'total_amount', 'po_amount', 'po_tax_amount', 'po_total_amount', 'grn_amount', 'grn_tax_amount', 'grn_total_amount');
$vendors = $result['vendors'];

?>
<style type="text/css">
  .ontable tr td{
    vertical-align: middle;
    padding: 5px;
  }
</style>
<script type="text/javascript">

</script>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">
        <?php
        $title = "Account Payable 3 Way Match";
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
           <span class="form-label"> Vendors &nbsp; </span>
           <div>
          <select aria-label="Vendor[]" class="js-example-basic-multiple" name="vendor[]"  multiple>
            <option value="0"> Select Any one </option>
            <?php foreach($vendors as $vendor){ ?>
                <option <?php if(in_array($vendor['id'],(array)$select_items)){ echo "selected";
} ?> value="<?php echo $vendor['id']; ?>"><?php echo $vendor['company']; ?></option>
            <?php } ?>
          </select>
          </div>
          <?php if(count((array)$select_items)==0){ ?>
          <div class="text-danger f-w-600 f-14"> Please Select Any  </div>
          <?php } ?>
        </div>
        <div class="col-lg-1 ">
          <span class="form-label"> &nbsp; </span>
          <div>
            <button type="submit" name="applyform" value="apply" class=" btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
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
            <?php foreach($array as $ar){
               echo  "<th scope='col'>".strtoupper(str_replace('_', ' ', $ar))."</th>";
            }
            ?>
          </tr>
          <?php $sl=1; if(count((array)$datas)>0){
          foreach($datas as $data){
            $balance = isset($result['open_balances'][$key]) ? $result['open_balances'][$key] : 0;
          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <?php foreach($array as $ar){

              if(is_numeric($data[$ar])){ ?>
                  <td class="text-right"><?php echo customonlynumberformate($data[$ar]); ?></td>
               <?php }else{ ?>
                  <td ><?php   echo ucwords(str_replace('_', ' ', $data[$ar])); ?></td>
               <?php  } ?>
            <?php } ?>
          </tr>

          <?php
            $_SESSION['download_data'] = $cssdata;
            }   $sl++;            ?>
            <?php
          }else{ ?>
          <tr>
            <td colspan="12">
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
