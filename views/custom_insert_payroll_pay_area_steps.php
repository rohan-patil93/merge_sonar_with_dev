<?php
$_SESSION['apply_js']['multiselest'] = 1;
$array = array(
array('id'=>1,'name'=>'New Joinees','page'=>'custom_insert_payroll_addition.php'),
array('id'=>2,'name'=>'Separation','page'=>'custom_insert_payroll_separation.php'),
array('id'=>3,'name'=>'Employees to be excluded','page'=>'custom_insert_payroll_exclusion.php'),
array('id'=>4,'name'=>'Settlements','page'=>'custom_insert_payroll_settlements.php'),
array('id'=>5,'name'=>'Salary Revisions','page'=>'custom_insert_payroll_salary_revisions.php'),
array('id'=>6,'name'=>'Update Onetime Earnings','page'=>'custom_insert_payroll_updateonetime_earnings.php'),
array('id'=>7,'name'=>'Update Onetime Deductions','page'=>'custom_insert_payroll_updateonetime_deductions.php'),
array('id'=>8,'name'=>'Hold Salary','page'=>'custom_insert_payroll_hold_salaray.php'));
$yearr = $_GET['PAY_YEAR']!=""?$_GET['PAY_YEAR']:date('Y');
$monthh = $_GET['PAY_MONTH']!=""?$_GET['PAY_MONTH']:date('m');

$vurl = API."?mode=togetpayrollstepsdetails&pay_year=".$_GET['PAY_YEAR']."&pay_month=".$_GET['PAY_MONTH']."&pay_area_id=".$_GET["PPJ"];
$steps_detail = callAPI($vurl);
$steps_details = json_decode($steps_detail,1);

$step1_data = $steps_details['step1']['data'];
$step2_data = $steps_details['step2']['data'];
$step3_data = $steps_details['step3']['data'];
$step4_data = $steps_details['step4']['data'];
$step5_data = $steps_details['step5']['data'];
$step8_data = $steps_details['step8']['data'];
$payroll_steps = array();
foreach((array)$steps_details['payroll_steps'] as $paysp){
  $payroll_steps[] = $paysp['category_id'];
}
$employes = $steps_details['employes'];
foreach($step8_data as $step_hold){
  $hold_emps[] = $step_hold['emp_id'];
}
foreach($employes as $employe){
  $list_emp[$employe['id']] = $employe;
}

  if(isset($_POST['submit_earning'])){
    print_r($_POST);
    print_r($_FILES);
    $ext=explode(".",$_FILES["name"]['name']);
    $file_name = $ext[0];
    $file_path='uploads/edi/file'.'_'.time().uniqid().'.'.$ext[1];
    move_uploaded_file($_FILES["name"]["tmp_name"],$file_path);
    $fp = fopen($file_path, "r");
      $content = fread($fp, filesize($file_path));
      $testdata = array();
      if (($h = fopen($file_path, "r")) !== false) {
      while (($content1 = fgetcsv($h, 1000, ',')) !== false) {
        $testdata[]= $content1;
      }
      fclose($h);
    }

    $_POST['data']= $testdata;
    $_POST['company_id']= $_SESSION['company_id'];
    $url = API."?mode=payrollupdateonetime&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    if($response){
      header("location:index.php?option=COM_INSERT_PAYROLL_PAY_AREA_STEPS&PAY_YEAR=".$_GET['PAY_YEAR']."&PAY_MONTH=".$_GET['PAY_MONTH']."&PPJ=".$_GET['PPJ']); exit;
    }
    exit;
  }
?>
<style>
  .checkpayrollicons{
    display: inline-block;
    background: green;
    color: white;
    padding: 8px 15px;
    border-radius: 50%;
    font-size: 21px;
    margin-right: 10px;
    transition-delay: 1s;
  }
</style>
<script type="text/javascript">
function addinsertexcluded(type, cat){
  var y= $('#year').val();
  var m= $('#month').val();
  var pay_area_id= $('#pay_area_id').val();
  var emp_id= $('#'+type+"_emp").val();
  console.log("Year:"+y+"Month:"+m+"emp_id:"+emp_id+"pay_area_id:"+pay_area_id+"cat:"+cat);
  jQuery.ajax({
    url: "ajax.php",
    data:'page=insertpayrollexcludd&category_id='+cat+'&pay_year='+y+'&pay_month='+m+'&emp_id='+emp_id+'&pay_area_id='+pay_area_id,
    type: "POST",
    success:function(data){
      $('#'+type+"_no").hide();
      $('#'+type+"_table").append(data);
    },
    error:function (){}
  });
}
function notifyuser(id=""){
  if(id!=""){
    jQuery.ajax({
      url: "views/custom_insert_user_notify.php",
      data:'id='+id,
      type: "POST",
      success:function(data){
        $('#popup123').html(data);
        $("#kt_modal_KTDatatable_remote123").modal("show");
      },
      error:function (){}
    });
  }else{
    var msg = <?php echo "'".$_SESSION['error_message']['9109']."'" ?>;
    swal('<?php echo ucwords($_SESSION['config']['name']); ?>', msg, "error");
  }
}
function checkpayrollsteps(cat,maxid){
  $('#numberid'+maxid).hide();
  $('#checkid'+maxid).show();
  var y= $('#year').val();
  var m= $('#month').val();
  var pay_area_id= $('#pay_area_id').val();
  jQuery.ajax({
    url: "ajax.php",
     data:'page=tocheckpayrolldetailsfull&payroll_steps_cat='+cat+'&pay_year='+y+'&pay_month='+m+'&pay_area_id='+pay_area_id,
    type: "POST",
    success:function(data){
      console.log(data);
    },
    error:function (){}
  });
}
function checkdisplays(val){
    var count = $("#count").val();
    console.log(count+","+val);
    for(var i=1; i<count; i++){
        if(val==i){
          $('#check_'+i).show();
          $('#next').val(i+1);
          $('#pre').val(i-1);
        }else{
          $('#check_'+i).hide();
        }
    }
    if(val == count-1){
       $('#next').hide();
    }else{
       $('#next').show();
    }
    if(val == 1){
       $('#pre').hide();
    }else{
       $('#pre').show();
    }
}
function holdpayroll_exclusions1(emp_id,cat){
  console.log("hai ppppppp");
    var y= $('#year').val();
    var m= $('#month').val();
    var pay_area_id= $('#pay_area_id').val();
    console.log("Year:"+y+"Month:"+m+"emp_id:"+emp_id+"pay_area_id:"+pay_area_id+"cat:"+cat);
    jQuery.ajax({
          url: "ajax.php",
          data:'page=holdpayroll_exclusions&category_id='+cat+'&pay_year='+y+'&pay_month='+m+'&emp_id='+emp_id+'&pay_area_id='+pay_area_id,
          type: "POST",
          success:function(data){
              console.log(data);

          },
          error:function (){}
      });
  }
  function deleteonerow(id,cat){
    jQuery.ajax({
      url: "ajax.php",
      data:'page=dynmicupdatetable&id='+id+'&is_active=1&table=payroll_exclusions',
      type: "POST",
      success:function(data){

        $('#'+cat).remove();
      },
      error:function (){}
    });
  }
</script>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
    <div class="card card-custom ">
      <div class="card-header table-card-header mb-2">
        <h5> PayRoll Process</h5>
        <div class="card-header-right table-card-header ">
          <a   href="index.php?option=COM_INSERT_PAYAREA_PAYROLL&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&PPJ=<?php echo $_GET['PPJ']; ?>&REG=R"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 genratepayrolbtn" <?php if(count((array)$pay_area_ids)!= count((array)$payrollaray)){?>style="display: none" <?php
} ?>> <?php echo "Generate Payroll";  ?>
          </a>
        </div>
      </div>
      <div class="example-preview bg-preview">
        <input type="hidden" name="" id="pay_area_id" value="<?php echo $_GET['PPJ']; ?>">
        <input type="hidden" name="pay_year" id="year" value="<?php echo $yearr; ?>">
        <input type="hidden" name="pay_month" id="month" value="<?php echo $monthh; ?>">
        <div class="row" id="check_<?php echo $cc; ?>">
          <div class="form-group col-lg-3 pd-2">
            <ul class="page-list nav nav-tabs flex-column infisuite_tab payroll-step m-l-5" id="myTab3">
              <?php $m=1; foreach($array as $mkk=>$arr){ ?>
              <li class="nav-item mail-section ">
                <a class="nav-link waves-effect d-block  <?php if($m==1) { ?> active <?php
} ?> " id="home-tab-3<?php echo $m; ?>" data-bs-toggle="tab" href="#home-3<?php echo $m; ?>" onclick="checkpayrollsteps('<?php echo $arr['id']; ?>','<?php echo $m; ?>')">
                  <b id="numberid<?php echo $m; ?>" <?php  if($m==1 || in_array($mkk, $payroll_steps)){ ?> style="display: none" <?php
} ?>><?php echo $m; ?></b>
                  <span id="checkid<?php echo $m; ?>" class="checkpayrollicons" <?php if( $m!=1 && !in_array($mkk, $payroll_steps) ){ ?> style="display: none" <?php
} ?>><i class="ti-check"></i></span>
                  <span class="navi-text" ><?php echo $arr['name']; ?></span>
                </a>
              </li>
              <?php $m++;
} ?>
            </ul>
          </div>
          <div class="form-group col-lg-8">
            <div class="tab-content " id="myTabContent3">
              <?php $m=1; foreach($array as $arr){ ?>
              <div class="tab-pane fade <?php if($m==1) { ?> show active <?php
} ?> " id="home-3<?php echo $m; ?>" role="tabpanel" aria-labelledby="home-tab-3<?php echo $m; ?>">
                <?php  if($arr['page']!="") {
                  include_once $arr['page'];
                }else{
                  echo "Under Process ". $m;
                } ?>
              </div>
              <?php $m++;
} ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
