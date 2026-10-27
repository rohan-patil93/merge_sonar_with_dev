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
 $pass="fromdate=". $_POST['fromdate']."&todate=". $_POST['todate']."&vendor_id=". $_POST['vendor_id']."&job_title_id=". $_POST['job_title_id']."&cc_id=".$_POST['cc_id'];
}else{
 $currentYear = date("Y");
 $oneYearBefore = $currentYear - 1;
 $_POST['fromdate'] =  $oneYearBefore."-01-01";
 $_POST['todate'] = date("Y-m-t");
 $pass="fromdate=". $_POST['fromdate']."&todate=". $_POST['todate']."&vendor_id=0&job_title_id=0&cc_id=".CURRENCY_ID_IND;
}
$job_title_id = $_POST['job_title_id']!="" ? $_POST['job_title_id'] : "0";
$vendor_id = $_POST['vendor_id']!="" ? $_POST['vendor_id'] : "0";
$fromdate = $_POST['fromdate']!="" ? $_POST['fromdate'] : "";
$todate = $_POST['todate']!="" ? $_POST['todate'] : "";
$cc_id = $_POST['cc_id']!="" ? $_POST['cc_id'] : CURRENCY_ID_IND;

$_SESSION['apply_js']['multiselest'] = 1;
$url = API."?mode=togetjobtitleexp&".$pass;
$apires = callAPI($url);
$result = json_decode($apires,1);
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
    .heat-drop .select2-container{
      width:200px !important;
    }
    .heat-drop a{
      padding:12px 25px;
      border-radius: 3px;
    }
  </style>
  <script type="text/javascript">
    function viewallpop(data){
      jQuery.ajax({
        url: "views/custom_view_job_exp_rate_popup.php",
        data:'data='+$('#'+data).val(),
        type: "POST",
        success:function(data){
          $('#dynamic_popupxxl').html(data);
          $('#kt_modal_dynamic_popupxxl').modal("show");
        },
        error:function (){}
      });
    }
  </script>
  <script type="text/javascript">
  function changeCurrenciesconverterates(val){
    $('#listdata').html('<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner2.gif" style="width: 300px;" alt="Preview"></div>');
    jQuery.ajax({
      url: "views/custom_view_job_vendor_exp_rate_ajax.php",
      data:'ajaxpagecall=Y&cc_id='+val+'&fromdate='+$('#fromdate').val()+'&todate='+$('#todate').val()+'&vendor_id='+$('#vendor_id').val()+'&job_title_id='+$('#job_title_id').val(),
      type: "POST",
      success:function(data){
        console.log(data);
        $('#listdata').html(data);

      },
      error:function (){}
    });
  }
</script>
<div class="card">
  <div class="card-header">
    <h5 class="f-30">Vendor  Rate Card Heatmap</h5>
    <div class="card-header-right">

    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <form method="post" action="">
        <div class="row ">

          <div class="col-lg-2">
            <div class="form-group">

              <span class="form-label mb-0">Form date</span>

              <input aria-label="Fromdate" class="form-control datepicker  p-t-10 p-b-10" id="fromdate" name="fromdate" type="text" <?php if($fromdate!=""){ ?>  value="<?php echo fromDate($fromdate); ?>" <?php
} ?> placeholder="From Date" >
            </div>
          </div>

          <div class="col-lg-2">
            <div class="form-group">
              <span class="form-label mb-0"> To Date</span>
              <input aria-label="Todate" class="form-control datepicker p-t-10 p-b-10" id="todate"  <?php if($todate!=""){ ?>  value="<?php echo fromDate($todate); ?>" <?php
} ?> name="todate" type="text" placeholder="To Date">
            </div>
          </div>
          <div class="col-lg-2">
           <span class="form-label mb-0">Vendors </span>
           <select aria-label="Vendor" class=" form-select js-example-basic-single" name="vendor_id" id="vendor_id">
            <option value="0"> Select Vendor</option>
            <?php foreach($result['vendor'] as $keyven=>$ven){ ?>
              <option <?php if($vendor_id == $keyven){ echo "selected";
} ?> value="<?php echo $keyven; ?>"> <?php echo $ven; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-lg-2">
         <span class="form-label mb-0"> Job Titles</span>
         <select aria-label="Job Title" class="form-select js-example-basic-single " name="job_title_id" id="job_title_id">
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
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-3 btn-md">
            Apply
          </button>
          <input type="hidden" name="submit" value="applyform">
        </div>

      </div>

    <div class="row mt-5 align-items-center">
      <div class="col-lg-3">
        <div class="col bg-light-primary ">
          <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
            <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
          </span>
          <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
        </div>
      </div>
      <div class="col-lg-6">
        <h4 class="card-label text-center f-w-700">Vendor Rate Card Heatmap</br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small> </h4>
      </div>
      <div class="col-lg-3 heat-drop">
        <div class="float-left ">
          <select aria-label="Select option" class="js-example-basic-single <?php echo $cc_id; ?>"  onchange="changeCurrenciesconverterates(this.value)" name="cc_id" id="cc_id">
            <?php foreach($result['cu_data'] as $cudata){ ?>
              <option <?php if($cc_id == $cudata['cu_id'] ){ echo "selected";
} ?>  value="<?php echo $cudata['cu_id']; ?>"><?php echo $cudata['currency_short_code']; ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <a href="custom_view_download.php?page=Job_Title_Wise_Vendor_Rates" class=" btn-primary float-right" target="_blank"> Download </a>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>
<div class="card-body" id="listdata">
<?php include_once 'views/custom_view_job_vendor_exp_rate_ajax.php' ?>
</div>

