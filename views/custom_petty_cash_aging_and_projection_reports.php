<?php
$dretrundataind = array( '15'=>0, '30'=>0, '45'=>0, '60'=>0, '90'=>0);
$retrundataind = array(); $returnvendorind = array();
foreach($dretrundataind as $key=>$rfrr){
    $gdata[] = $key." days :".($retrundataind[$key]!="" ? $retrundataind[$key] : 0);
}
$_SESSION['download_data'] = array();

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
<div class="card">
    <div class="card-header">
      <h5 class="f-30">Spending Petty Cash</h5>
      <div class="card-header-right">
     </div>
   </div>
   <div class="card-body">

<?php
$retrundataind = $retrundata  = $returnvendorind = $vendortotal = $allids = $billdays = $vendordaytotal = $vendordayleveltotal = $returnvendorind = array();
foreach($result['bills'] as $data){

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

    $retrundataind[$payment_terms] = $retrundataind[$payment_terms] + $data['total_amount'];

    $total =  $total + $data['total_amount'];

    $returnvendorind[$data['party_id']][$payment_terms] = $returnvendorind[$data['party_id']][$payment_terms] +$data['total_amount'];

    $vendortotal[$data['party_id']] = $vendortotal[$data['party_id']] + $data['total_amount'];

    $allids[] = $data['id'];

    $billdays[$payment_terms][] = $data['id'];

    $vendordaytotal[$data['party_id']][] = $data['id'];

    $vendordayleveltotal[$data['party_id']][$payment_terms][] = $data['id'];
}
foreach($dretrundataind as $key=>$rfrr){
    $gdata2[] = $key." days :".($retrundataind[$key]!="" ? $retrundataind[$key] : 0);
}
?>

        <div class="row">
          <div class="col-lg-8">
            <div class="card-block p-b-0">
              <div class="table-responsive">
                <table class=" table table-hover m-b-0">
                  <thead>
                    <th scope="col"> Invoice </th>
                    <th scope="col" class="text-right">Total </th>
                    <th scope="col" class="text-right">15 Days</th>
                    <th scope="col" class="text-right">30 Days</th>
                    <th scope="col" class="text-right">45 Days</th>
                    <th scope="col" class="text-right">60 Days</th>
                    <th scope="col" class="text-right">90 Days</th>
                    <th scope="col" class="text-right"> > 90 Days</th>
                  </thead>
                  <?php
                      $one = "'".implode("-",(array)$allids)."'";
                      $two = "'".implode("-",(array)$billdays['15'])."'";
                      $three = "'".implode("-",(array)$billdays['30'])."'";
                      $four = "'".implode("-",(array)$billdays['45'])."'";
                      $five = "'".implode("-",(array)$billdays['60'])."'";
                      $six = "'".implode("-",(array)$billdays['90'])."'";
                      $seven = "'".implode("-",(array)$billdays['120'])."'";
                   ?>
                  <tbody>
                    <td>Invoice</td>

                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $one; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($total); ?> </span> </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $two; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['15']);  ?> </span>
                    </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $three; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['30']); ?> </span> </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $four; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['45']); ?> </span> </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $five; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['60']); ?> </span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $six; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['90']); ?> </span> </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $seven; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($retrundataind['120']); ?></span></td>
                  </tbody>
                </table>
                <table class=" table table-hover m-b-0 m-t-5">
                  <thead>
                    <th scope="col"> Vendors </th>
                    <th scope="col" class="text-right">Total </th>
                    <th scope="col" class="text-right">15 Days</th>
                    <th scope="col" class="text-right">30 Days</th>
                    <th scope="col" class="text-right">45 Days</th>
                    <th scope="col" class="text-right">60 Days</th>
                    <th scope="col" class="text-right">90 Days</th>
                    <th scope="col" class="text-right"> > 90 Days</th>
                  </thead>
                  <tbody>
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
                    <td class="text-capitalize"><?php echo $_SESSION['users'][$vendor_id]; ?></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $one; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($value); ?> </span> </td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $two; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['15']);  ?> </span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $three; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['30']); ?></span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $four; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['45']); ?></span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $five; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['60']); ?></span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $six; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['90']); ?></span></td>
                    <td class="text-right"> <span class="cursor-pointer" onclick="todisplayviewvendor(<?php echo $seven; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo customeummarmonyformate($returnvendorind[$vendor_id]['120']); ?></span></td>
                    </tr>
                  <?php  } ?>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div id="chart_Donut_projections" style="width: 100%; height: 400px;"></div>
              <input type="hidden"  id="chart_data_bill_projecttion" value="<?php echo implode("||",(array)$gdata2); ?>" >
            </div>
        </div>
      </div>
    </div>


