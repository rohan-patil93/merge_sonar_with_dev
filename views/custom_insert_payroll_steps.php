<?php
   $array = array(array('id'=>1,'name'=>'New Joinees','page'=>'custom_insert_payroll_addition.php'),array('id'=>2,'name'=>'Separation','page'=>'custom_insert_payroll_separation.php'),array('id'=>3,'name'=>'Employees to be excluded','page'=>'custom_insert_payroll_exclusion.php'),array('id'=>4,'name'=>'Settlements','page'=>'custom_insert_payroll_settlements.php'),array('id'=>7,'name'=>'Salary Revisions','page'=>'custom_insert_payroll_salary_revisions.php'),array('id'=>8,'name'=>'Update Onetime Earnings','page'=>'custom_insert_payroll_updateonetime_earnings.php'),array('id'=>10,'name'=>'Update Onetime Deductions','page'=>'custom_insert_payroll_updateonetime_deductions.php'),array('id'=>9,'name'=>'Hold Salary','page'=>'custom_insert_payroll_hold_salaray.php'));


   $yearr = $_GET['PAY_YEAR']!=""?$_GET['PAY_YEAR']:date('Y');
   $monthh = $_GET['PAY_MONTH']!=""?$_GET['PAY_MONTH']:date('m');
    $url1 = API."?mode=insertcheckpayroll&id=".$array[0]['id']."&year=".$yearr."&month=".$monthh;
    $insertdetails = callAPI($url1);

    $payrollaray = array();
  $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'pay_year'=>$yearr,'pay_month'=>$monthh);
  $wh1["field"] = "id,category_id";
  $data1 = urlencode(json_encode($wh1));
  $url1 = API."?mode=gettabledetailswithconditions&table=payroll_steps&data=".$data1;
  $data_details1 = callAPI($url1);
  $data_details1 = json_decode($data_details1,1);
  foreach($data_details1 as $data_detail){
    $payrollaray[] =  $data_detail['category_id'];
  }


?>
   <script type="text/javascript">
     function notifyuser(id){
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
   }

function checkpayrollsteps(id,year,month,maxid,cnt){
$('#numberid'+maxid).hide();
$('#checkid'+maxid).show();
  jQuery.ajax({
            url: "ajax.php",
            data:'page=insertcheckpayroll&id='+id+'&year='+year+'&month='+month,
            type: "POST",
            success:function(data){
               console.log(data);
               var res = JSON.parse(data);
               console.log(res.check);
               if(cnt == res.check){
                  $(".genratepayrolbtn").show();
               }
            },
            error:function (){}
        });
}

  </script>
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
<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
    <div class="card card-custom ">


 <div class="card-header table-card-header mb-2">
              <h5> PayRoll Process</h5>
            <div class="card-header-right table-card-header ">


<a   href="index.php?option=COM_INSERT_PAYROLL&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 genratepayrolbtn" <?php if(count((array)$array)!= count((array)$payrollaray)){?>style="display: none" <?php
} ?>>
              <?php echo "Genrate Payroll";  ?>
      </a>

        </div>
      </div>
        <div class="example-preview bg-preview">
          <div class="row">
            <div class="form-group col-lg-3 pd-2">
              <ul class="page-list nav nav-tabs flex-column infisuite_tab payroll-step m-l-5" id="myTab3">
                <?php $m=1; foreach($array as $arr){ ?>
                  <li class="nav-item mail-section ">
                  <a class="nav-link waves-effect d-block  <?php if($m==1) { ?> active <?php
} ?> " id="home-tab-3<?php echo $m; ?>" data-bs-toggle="tab" href="#home-3<?php echo $m; ?>" onclick="checkpayrollsteps('<?php echo $arr['id']; ?>','<?php echo $yearr; ?>','<?php echo $monthh; ?>','<?php echo $m; ?>','<?php echo count((array)$array); ?>')">

                  <b id="numberid<?php echo $m; ?>" <?php  if(in_array($arr['id'],$payrollaray)){ ?> style="display: none" <?php
} ?>><?php echo $m; ?></b>
                  <span id="checkid<?php echo $m; ?>" class="checkpayrollicons" <?php if(!in_array($arr['id'],$payrollaray)){ ?> style="display: none" <?php
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
                  <?php  if($arr['page']!="") {   include_once $arr['page'];
                  }else{ echo "Under Process ". $m;
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
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-xl" >
      <div class="modal-content"  id="popup1">
      </div>
   </div>
</div>
<div id="kt_modal_KTDatatable_remote123" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog " >
      <div class="modal-content"  id="popup123">
      </div>
   </div>
</div>
