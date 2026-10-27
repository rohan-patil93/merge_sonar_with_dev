<?php

if(isset($_POST['applyform'])) {
  $fromdate = $_POST['fromdate']!="" ? $_POST['fromdate'] : "";
  $todate = $_POST['todate']!="" ? $_POST['todate'] : "";
  if($_POST['fromdate']!=""){
    $_POST['fromdate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['fromdate'])));
  }
  if($_POST['todate']!=""){
     $_POST['todate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['todate'])));
  }
  $pass="fromdate=". $_POST['fromdate']."&todate=". $_POST['todate']."&vendor_id=". $_POST['vendor_id']."&job_title_id=". $_POST['job_title_id'];
}else{
   $currentYear = date("Y");
    $oneYearBefore = $currentYear - 1;
     $_POST['fromdate'] =  $oneYearBefore."-01-01";
   $_POST['todate'] = date("Y-m-t");
   $pass="fromdate=". $_POST['fromdate']."&todate=". $_POST['todate']."&vendor_id=0&job_title_id=0";
}
 $job_title_id = $_POST['job_title_id']!="" ? $_POST['job_title_id'] : "0";
  $vendor_id = $_POST['vendor_id']!="" ? $_POST['vendor_id'] : "0";
$fromdate = $_POST['fromdate']!="" ? $_POST['fromdate'] : "";
$todate = $_POST['todate']!="" ? $_POST['todate'] : "";
$_SESSION['apply_js']['multiselest'] = 1;
$url = API."?mode=togetjobtitleexp&".$pass;
$apires = callAPI($url);
$result = json_decode($apires,1);
$_SESSION['download_data'] = array();
function averagearray($ave){
  $cc = count((array)$ave);
  $return = 0;
  if($cc>0){
      if($cc==1){
          $return = $ave[0];
      }else{
        foreach($ave as $av){
            $total = $total+$av;
        }
        $return = $total/$cc;
      }
  }
  return $return;
}
?>
<style type="text/css">
  .ontable tr td{
    vertical-align: middle;
    padding: 5px;
  }
  .th_head{
    vertical-align: middle;
    white-space: normal;
  }
</style>
<script type="text/javascript">
  function showdetails(trn, job_id, min, max, vendor_id, price){
      jQuery.ajax({
            url: "views/custom_view_rate_emp_popup.php",
            data:'table=currency_convertor&trn='+trn+'&job_id='+job_id+'&min='+min+'&max='+max+'&vendor_id='+vendor_id+'&price='+price,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });
  }
</script>
<div class="card">
  <div class="card-header">
    <h5 class="f-30">Job Title Wise Vendor Rates</h5>
    <div class="card-header-right">

    </div>
  </div>
    <div class="card-body">
      <div class="row">
        <form method="post" action="">
          <div class="row col-lg-12 ">
  <div class="col-lg-2">
  From Date
  </div>
  <div class="col-lg-2">
  To Date
        </div>
  <div class="col-lg-2">
    Vendors
        </div>
  <div class="col-lg-2">
    Job Titles
        </div>
  <div class="col-lg-4">

        </div>

        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="Fromdate" class="form-control datepicker p-2" name="fromdate" type="text" <?php if($fromdate!=""){ ?>  value="<?php echo $fromdate; ?>" <?php
} ?> placeholder="From Date" >
          </div>
        </div>

        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="Input field" class="form-control datepicker p-2" <?php if($todate!=""){ ?>  value="<?php echo $todate; ?>" <?php
} ?> name="todate" type="text" placeholder="To Date">
          </div>
        </div>
        <div class="col-lg-2">
          <select aria-label="Vendor" class=" form-select js-example-basic-single" name="vendor_id">
            <option value="0"> Select Vendors</option>
            <?php foreach($result['vendor'] as $keyven=>$ven){ ?>
                <option <?php if($vendor_id == $keyven){ echo "selected";
} ?> value="<?php echo $keyven; ?>"> <?php echo $ven; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-lg-2">
          <select aria-label="Job Title" class="form-select js-example-basic-single " name="job_title_id">
            <option value="0"> Select Job Title</option>
            <?php foreach($result['job_title'] as $keyjob=>$jobtitle){
                    if($keyjob){
              ?>
                <option <?php if($job_title_id == $keyjob){ echo "selected";
} ?> value="<?php echo $keyjob; ?>"> <?php echo $jobtitle; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
        <div class="col-lg-2 mt-1">
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
            Apply
          </button>
          <input type="hidden" name="submit" value="applyform">
        </div>

      </div>
        </form>
  <div class="row mt-2">
      <div class="col-lg-3">
        <div class="col bg-light-primary ">
          <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
            <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
          </span>
          <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
        </div>
      </div>
      <div class="col-lg-6">
        <h4 class="card-label text-center f-w-700">Job Title Wise Vendor Rates</br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small> </h4>
      </div>
      <div class="col-lg-3">
        <div>
          <a href="custom_view_download.php?page=Job_Title_Wise_Vendor_Rates" class="btn btn-primary float-right" target="_blank"> Download </a>
        </div>
      </div>
    </div>
    </div>
  </div>
    <div class="card-body">
      <div class="table-responsive mt-3">
        <!-- table table-hover -->
        <table class=" table table-bordered">
          <tr style="background-color: #eeeeee;">
            <th scope="col" class="th_head">#</th>
            <th scope="col" class="th_head">Job Title</th>
            <th scope="col" class="th_head">Experience</th>
            <th scope="col" class="th_head">Minimum Rate</th>
            <th scope="col" class="th_head">Maximum Rate </th>
            <th scope="col" class="th_head">Average Rate</th>
            <?php foreach ($result['vendor'] as $venkey=>$vendor) {  ?>
             <th scope="col" class="th_head"><?php echo $vendor; ?></th>
            <?php } ?>
          </tr>
          <?php $sl=1;$lists= array();  if(count((array)$result['data'])>0){
           foreach($result['data'] as $job_id=>$job_data ){
            foreach($job_data as $keyexp=>$exp ){
          ?>
            <tr>
            <td><?php echo $sl; $lists[$sl]['Sl_No']=$sl; ?></td>
            <td><?php echo $result['job_title'][$job_id]; $lists[$sl]['Job_Title']=$result['job_title'][$job_id]; ?></td>
            <td><?php echo $keyexp; $lists[$sl]['Experience']=$keyexp;  ?></td>
            <td class="text-right"><?php  echo customeummarmonyformate($result['min'][$job_id][$keyexp]);  $lists[$sl]['Minimum_Rate']=round($result['min'][$job_id][$keyexp],2); ?></td>
            <td class="text-right"><?php  echo customeummarmonyformate($result['max'][$job_id][$keyexp]); $lists[$sl]['Maximum_Rate']= round($result['max'][$job_id][$keyexp],2); ?></td>
            <td class="text-right"><?php $avee =averagearray($result['ave'][$job_id][$keyexp]); echo customeummarmonyformate($avee); $lists[$sl]['Average Rate']=round($avee,2) ; ?></td>
            <?php foreach ($result['vendor'] as $venkey=>$vendor) {  ?>
             <td class="text-right">
                  <?php if(isset($exp[$venkey])){

                  ?>
                    <?php
                    $viewarray = array(); $viewaallrray = array();
                     foreach( $exp[$venkey] as $expvven){
                         if(!in_array($expvven['item_price'],$viewarray)){
                     ?>
                    <a href="#" class="text-primary" onclick="showdetails(<?php echo $expvven['transaction_id'].",".$expvven['job_title_id'].",".$expvven['min_exp'].",".$expvven['max_exp'].",".$expvven['vendor_id'].",".$expvven['item_price']; ?>)">
                     <?php echo customeummarmonyformate($expvven['item_price']);
                        $viewaallrray[]="(".$expvven['item_price'].")"; ?>
                   </a></br>
                   <?php } ?>
                   <?php }
                       $lists[$sl][$vendor]= implode("",$viewaallrray);
                    ?>
                  <?php }else{
                    $lists[$sl][$vendor]="()";
                     echo "-";
                  }
                  ?>

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
</div>

