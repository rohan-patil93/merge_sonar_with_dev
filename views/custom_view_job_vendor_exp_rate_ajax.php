<?php
if($_POST['ajaxpagecall'] == "Y"){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  if($_POST['fromdate']!=""){
    $_POST['fromdate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['fromdate'])));
  }
  if($_POST['todate']!=""){
   $_POST['todate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['todate'])));
 }
 $pass="fromdate=". $_POST['fromdate']."&todate=". $_POST['todate']."&vendor_id=". $_POST['vendor_id']."&job_title_id=". $_POST['job_title_id']."&cc_id=".$_POST['cc_id'];
 $url = API."?mode=togetjobtitleexp&".$pass;
$apires = callAPI($url);
$result = json_decode($apires,1);
$cc_id = $_POST['cc_id'];
}
$ave_array = array( '0'=>"0-1", '1'=>"1-2", '2'=>"2-3", '3'=>"3-4", '4'=>"4-5", '5'=>"5-6", '6'=>"6-7", '7'=>"7-8",  '8'=>"8-9", '9'=>"9-10", '10'=>"10-12", '11'=>"12-15", '12'=>"15-999");
foreach($result['fulldata'] as $fulldata){
  $min = $fulldata['min_exp'];
  $max = $fulldata['max_exp'];
  $avg = ($fulldata['min_exp']+$fulldata['max_exp'])/2;

  foreach($ave_array as $key=>$ave_ar){
    $ex_arr = explode('-',$ave_ar);
    if($ex_arr['0']>= $avg &&  $avg < $ex_arr['1'] ){
      $val_key = $key;
      break;
    }

      }
      $now_venders[$fulldata['vendor_id']] = $fulldata['vendor_id'];
      $latsetdate[$fulldata['vendor_id']] = $fulldata['latest_date'];
      $listvendorpricevalue[$fulldata['vendor_id']][$val_key][] = $fulldata['purchase_uom'] !='PD' ? $fulldata['item_price'] : $fulldata['item_price']*22 ;
      $listvendorminmaxjobvalue[$fulldata['vendor_id']][$val_key][] = $fulldata['job_title']."###".$fulldata['min_exp']." - ".$fulldata['max_exp'];
      $listvendorallvalue[$fulldata['vendor_id']][$val_key][] = $fulldata;
      $listofavg[$val_key][$fulldata['item_price']] = $fulldata['item_price'];
      if($fulldata['purchase_uom']!='PM'){
        $staruomdetail[$fulldata['vendor_id']][$val_key] = 'T';
      }
    }
    $_SESSION['download_data'] = array();
    function averagearray($ave){
      $cc = count((array)$ave);
      $return = 0;
      if($cc>0){
        if($cc==1){
          $return = $ave['0'];
        }else{
          foreach($ave as $av){
            $total = $total+$av;
          }
          $return = $total/$cc;
        }
      }
      return $return;
    }

function gradenetcolorcodes($grd,$inputValue){
  $gradient = array('0'=>'#FF0000', '1'=>'#E61900', '2'=>'#CC3200', '3'=>'#B34B00', '4'=>'#996400', '5'=>'#7F7D00', '6'=>'#668600', '7'=>'#4C9F00', '8'=>'#32B800', '9'=>'#19D100');
  $roundedPercentage = 0;
  if(count((array)$grd)>1){
   sort($grd);
   $firstValue = $grd['0'];
   $last_key = count($grd)-1;
   $lastValue = $grd[$last_key];
   if($firstValue == $lastValue) {
    return 0;
  }
      // Calculate the percentage

  $percentage = (($inputValue - $firstValue) / ($lastValue - $firstValue)) * 100;
  $roundPercentage = (round($percentage / 10) * 10)/10;
  $roundedPercentage = 9 - $roundPercentage;
  $roundedPercentage = $roundedPercentage!=-1?$roundedPercentage:0;

      }
      return $gradient[$roundedPercentage];
    }

    foreach($result['cu_data'] as $ccdata){
      $cc_val_arr[$ccdata['cu_id']] = $ccdata['currency_short_code'];
    }
?>

  <div class="f-12 f-w-800 float-start" style="width:270px; color: #19D100;">Low </div>
  <div class="f-12 f-w-800 " style="color: #FF0000;">High</div>
  <div class="float-left">
   <?php $gradient = array('0'=>'#FF0000', '1'=>'#E61900', '2'=>'#CC3200', '3'=>'#B34B00', '4'=>'#996400', '5'=>'#7F7D00', '6'=>'#668600', '7'=>'#4C9F00', '8'=>'#32B800', '9'=>'#19D100');
   for($gn=9;$gn>=0;$gn--){ ?>
    <div style="background-color:<?php echo $gradient[$gn]; ?>;width: 30px;height: 30px;" class="float-start"></div>
   <?php  } ?>
</div>
<?php $top_val = array('J'=>'Job Title', 'P'=>'Item Price', 'U'=>'Purchase UOM', 'M'=>'Min/MAX'); ?>
<div class="float-right">
   <span class="f-14 "> <b class="f-14 f-w-600">Currency:</b> <?php
$_SESSION['download_data_currency_code'] = $cc_val_arr[$cc_id];

echo $cc_val_arr[$cc_id]; ?> &nbsp;</span>
  <?php foreach ($top_val as $topkey=>$topval) {  ?>
    <span class="m-r-10">
      <span class="form-label label label-success f-14 f-w-bold m-0"><?php echo $topkey; ?> </span>

      <span class="m-l-5 f-12"><?php echo "Different ".$topval; ?> </span>
    </span>
  <?php } ?>
</div>
<div class="table-responsive mt-3">
  <!-- table table-hover -->
  <table class=" table table-bordered table-sm">
    <tr style="background-color: #eeeeee;">
      <th scope="col" class="th_head">#</th>
      <th scope="col" class="th_head">Vendors </th>
      <?php foreach ($ave_array as $ave_arr) {
        if(strpos($ave_arr,'-999')){
          $ave_arr = str_replace('-999','+',$ave_arr);
        }
        ?>
        <th scope="col" class="th_head">Average of <?php echo $ave_arr; ?> yrs</th>
      <?php } ?>
    </tr>
    <?php $sl=1;$lists= array();  if(count((array)$result['data'])>0){
     foreach($result['vendor'] as $vendor_id=>$vendor ){
      if(in_array($vendor_id, (array)$now_venders)){
        ?>
        <tr>
          <td><?php echo $sl; $lists[$sl]['Sl_No']=$sl; ?></td>
          <td>
                <span class=" <?php if(strtotime(date('Y-m-01'))>strtotime($latsetdate[$vendor_id])){ echo 'text-danger';
} ?> "><?php echo $vendor; $lists[$sl]['Vendor']=$vendor; ?></span>
              </br>
                <span class="text-muted f-12"> <?php echo fromDate($latsetdate[$vendor_id]); ?></span>
          </td>
          <?php foreach ($ave_array as $key=>$ave_arr) {
            $disval = "-";
            $css_text = "text-center";$bgcolor = ""; $avvr =0;
            if(isset($listvendorpricevalue[$vendor_id][$key])){
              $avvr =0;

              $avvr = averagearray($listvendorpricevalue[$vendor_id][$key]);

              $bgcolor = gradenetcolorcodes($listofavg[$key],$avvr);
              $disval = number_format(round($avvr), 0, '', ',');
              $css_text = "text-right";
            }
            $vieeee= "Average of ".$ave_arr." yrs";
            $lists[$sl][$vieeee] = $avvr;
            ?>
            <td class="<?php echo $css_text; ?>" style="position: relative; background-color: <?php echo $bgcolor; ?> !important;" >
              <span class="cursor-pointer text-white d-block text-center" onclick="viewallpop('id_<?php echo $vendor_id."_".$key; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  <?php echo $disval; ?>
              <?php
              $pass_array = array(); $p_job_id = array();
              $p_purchase_uom = array(); $p_rate = array(); $mul_pass_array =array();
              $p_max = array(); $p_min = array();
              foreach($listvendorallvalue[$vendor_id][$key] as $lvk){
                if(!in_array($lvk['job_title_id'],$p_job_id) || !in_array($lvk['purchase_uom'],$p_purchase_uom) || !in_array($lvk['item_price'],$p_rate) || !in_array($lvk['max_exp'],$p_max) || !in_array($lvk['min_exp'],$p_min) ){
                  $p_job_id[$lvk['job_title_id']]=$lvk['job_title_id'];
                  $p_purchase_uom[$lvk['purchase_uom']]=$lvk['purchase_uom'];
                  $p_rate[round($lvk['item_price'])]=$lvk['item_price'];
                  $p_max[$lvk['max_exp']]=$lvk['max_exp'];
                  $p_min[$lvk['min_exp']]=$lvk['min_exp'];
                  $pass_array[] =  $lvk;
                }
              }
              $mul_pass_array['data']=$pass_array;
              $mul_pass_array['currency']=$cc_val_arr[$cc_id];
              if(isset($staruomdetail[$vendor_id][$key])&& count($pass_array)>1){
                ?>
                <span class="text-white position-absolute top-0 f-10 pt-1 " style="right:4px;">
                  <sup>
                    <?php if(count((array)$p_job_id)>1){ echo "J";
} ?>
                    <?php if(count((array)$p_purchase_uom)>1){ echo "U";
} ?>
                    <?php if(count((array)$p_min)>1){ echo "M";
} ?>
                    <?php if(count((array)$p_rate)>1){ echo "P";
} ?>
                  </sup></span>
                <?php } ?>
              </span>
              <input type="hidden" name="" id="id_<?php echo $vendor_id."_".$key; ?>" value="<?php echo urlencode(json_encode($mul_pass_array)); ?>">
            </td>
          <?php } ?>
        </tr>
        <?php $sl++;
}
}
        $_SESSION['download_data'] = $lists;
        ?>
      </table>
      <?php
    }else{ ?>
    </table>
    <div class="text-center px-4">
     <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
   </div>
 <?php } ?>


</div>

</div>
