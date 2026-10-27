<?php
$url = API."?mode=togetreportvillvendors";
$apires = callAPI($url);
$datas = json_decode($apires,1);
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_bill_vendor']=1;

?>
<style type="text/css">
.ontable tr td{
  vertical-align: middle;
  padding: 5px;
}
</style>
<script type="text/javascript">
  function todisplayviewvendor(data){
      jQuery.ajax({
            url: "views/custom_bill_vender_popup.php",
            data:'val='+data,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
  }
</script>

<div class="page-body">
  <?php
  $title = "Bill Report";
  include_once "custom_standard_top_print.php"; ?>
  <div class="card">
    <div class="card-header">
      <h5 class="f-30"><?php echo $title; ?></h5>
      <div class="card-header-right">

     </div>
   </div>
   <div class="card-body">
        <?php
        $dretrundataind = array( '15'=>0, '30'=>0, '45'=>0, '60'=>0, '90'=>0);
        $retrundataind = $retrundata  = $returnvendorind = $vendortotal = $allids = $billdays = $vendordaytotal = $vendordayleveltotal = $returnvendorind = array();
        foreach($datas['bills'] as $data1){
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

