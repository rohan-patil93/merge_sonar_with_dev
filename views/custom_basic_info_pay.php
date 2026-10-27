 <?php 
 $employee_id = $employee_id!=""?$employee_id: $_SESSION['emp_id'];
 $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'employee_id'=>$employee_id, 'is_active'=>0);
  $vdata = urlencode(json_encode($whrcon));
  $url = API."?mode=gettabledetailswithconditions&table=pay_run_summary_components&data=".$vdata; 
  $response = callAPI($url);
  $data1 = json_decode($response,1);
  $speical_allowance_component_id = OA;
  $itd_component_id = TDS;

  foreach((array)$data1 as $value) {
    if($value['salary_component_type']==0){
      $total_monthly_earnings[$value['pay_year']][$value['pay_month']]= $total_monthly_earnings['pay_year']['pay_month'] + $value['salary_component_actual'];
      $total_earnings_in_current_year[$value['pay_year']] = $total_earnings_in_current_year['pay_year'] + $value['salary_component_actual'];
    }
    if($value['salary_component_type']==1){
      $total_monthly_deductions[$value['pay_year']][$value['pay_month']] = $total_monthly_deductions['pay_year']['pay_month'] + $value['salary_component_actual']; 
      $total_deductions_in_current_year[$value['pay_year']] = $total_deductions_in_current_year['pay_year'] + $value['salary_component_actual'];

    }
    if($value['component_id']== $itd_component_id){
      $total_tax_paid_in_current_year[$value['pay_year']] = $total_tax_paid_in_current_year['pay_year']+$value['salary_component_actual']; 

    }
    if($value['component_id'] == $speical_allowance_component_id){
      $total_perks_in_current_year[$value['pay_year']] = $total_perks_in_current_year['pay_year'] + $value['salary_component_actual']; 
    }
  }
  $m=1; //echo "-----------------------"; print_r($total_monthly_earnings);

 ?>
<script type="text/javascript">
   function showpayslip(employe_id,year,month){
      var data = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
       $("#payslipview").html(data);
      jQuery.ajax({
        url: "views/custom_list_payslip_template3.php",
        data:'ajaxpage=ajaxdisplay&employe_id='+employe_id+'&month='+month+'&year='+year,
        type: "GET",
        success:function(data){
          console.log(data);
          $("#payslipview").html(data);
        },
        error:function (){}
      });
   }
 </script>
 <div class="card card-custom gutter-b">
  <div class="" id="kt_page_sticky_card">
    <div class="card-header">
      <h5>My Pay <?php //echo $employee_id; ?></h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-2 j-custom_class" >
          <div class=" nnnnnsss">
            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
              <div class="card p-2">
                <div class="card-header p-2 f-w-600" id="headingOne3">
                  <div class="card-title" data-bs-toggle="collapse" data-bs-target="#collapseOne3">
                    Pay Years
                  </div>
                </div>
                <?php foreach((array)$total_monthly_earnings as $keyyear=>$yeardata ){ ?>
                <div class="card card_count deleterow<?php echo $m; ?>" style="border: 1px #ccc solid; margin: 5px;">
                  <div class="card-header p-2 f-w-600" id="headingOne<?php echo $m; ?>">
                    <div class="card-title collapsed textboxclass<?php echo $m; ?>" data-bs-toggle="collapse" data-bs-target="#collapseOnee<?php echo $m; ?>" style="cursor: pointer;" >
                      <i class="flaticon2-cancel-music" ></i> <?php echo $keyyear;?>
                    </div>
                  </div>
                  <div id="collapseOnee<?php echo $m; ?>" class="collapse" data-parent="#accordionExample5">
                    <div class="card-body">
                      <div class="example-preview">
                        <div class="form-group row">
                          <?php foreach((array)$yeardata as $monthkey=>$monthdata){   ?>
                          <div style="cursor: pointer;" onclick="showpayslip(<?php echo $employee_id.",".$keyyear.",".$monthkey; ?>)" class="col-12 col-form-label "><?php echo date("F", strtotime($keyyear."-".$monthkey."-01")); ?></div>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php $m++;} ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-10">
          <div id="payslipview">
             <?php $_GET['month']=$monthkey;$_GET['year']=$keyyear; $_GET['employe_id']=$employee_id;
             include("custom_list_payslip_template3.php"); ?>  
          </div>        
        </div>
      </div>
    </div>
  </div>
</div>