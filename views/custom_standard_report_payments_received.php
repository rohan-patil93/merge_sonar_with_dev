<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
$from_date = "";
$to_date = "";
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
  $from_date =  str_replace('/"', '-', $_POST['start_date']);
  $from_date =  date('Y-m-d', strtotime($from_date));
  $to_date =  str_replace('/"', '-', $_POST['to_date']);
  $to_date =  date('Y-m-d', strtotime($to_date));
}
$url = API."?mode=togetpaymentreceivedrepot&from_date=".$from_date."&to_date=".$to_date;
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
        $title = "Payments Received";
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
        <div class="col-lg-2 mt-1">
          <span class="form-label"> &nbsp; </span>
            <button type="submit" name="applyform" value="apply" class=" mt-3 btn btn-info waves-effect waves-light input-group-text mt-0">
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
            <th scope="col">PAYMENT NUMBER </th>
            <th scope="col">DATE</th>
            <th scope="col">REFERENCE NUMBER</th>
            <th scope="col">VENDOR NAME</th>
            <th scope="col">PAYMENT MODE</th>
            <th scope="col">Transaction No</th>
            <th scope="col">NOTES</th>
            <th scope="col" class="text-right">AMOUNT (FCY)</th>
          </tr>
          <?php $sl=1; if(count((array)$result['data'])>0){
           foreach($result['data'] as $data ){
            $cssdata[] = array('Sl No'=> $sl,'PAYMENT NUMBER'=> $data['transaction_no'], 'DATE'=> $data['payment_date'], 'REFERENCE NUMBER'=> $data['ref_no'],'VENDOR NAME'=> $data['display_name'],'PAYMENT MODE'=> $data['payment_type'],'NOTES'=> $data['remarks'], 'AMOUNT'=> $data['total_paid_amonut']);
          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <td><?php echo $data['transaction_no']; ?></td>
            <td ><?php echo fromDate($data['payment_date']); ?></td>
            <td ><?php echo $data['ref_no']; ?></td>
            <td><?php echo $data['display_name']; ?></td>
            <td><?php echo $data['payment_type']; ?></td>
            <td><?php echo $data['transaction_no']; ?></td>
            <td><?php echo $data['remarks']; ?></td>
            <td class="text-right"><?php echo customonlynumberformate($data['total_paid_amonut']); ?></td>
          </tr>
          <?php $sl++;
}
            $_SESSION['download_data'] = $cssdata;
            ?>
            <?php
          }else{ ?>
          <tr>
            <td colspan="9">
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
