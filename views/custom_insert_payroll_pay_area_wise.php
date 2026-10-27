<?php
if( $_GET["PAY_MONTH"] == ''){
    $_GET["PAY_MONTH"] = date('n');
    $_GET["PAY_YEAR"] = date('Y');
}
$title = "Payroll - Pay Areas";
 $url = API."?mode=togetpayrollpayarea&emp_id=".$_SESSION['emp_id']."&pay_year=".$_GET["PAY_YEAR"]."&pay_month=".$_GET["PAY_MONTH"];
$response = callAPI($url);
$bills = json_decode($response,1);
$pay_areas = $bills['pay_area'];
$count_emp = $bills['count_emp'];
$payrolldatas = $bills['payroll'];
foreach($pay_areas as $sinpay_area){
    $payareaids[]=$sinpay_area['id'];
}
foreach($payrolldatas as $payrolldata){
  $payrolls[] = $payrolldata['pay_area_id'];
  $payrolldata[$payrolldata['pay_area_id']] = $payrolldata;
  if(in_array($payrolldata['pay_area_id'],(array)$payareaids)){
    $check[]=$payrolldata['pay_area_id'];
  }
}


?>
<script type="text/javascript">
  function tocheckallboxeshrwork(){
    var val = $('#val_select_all').val();
    console.log(val);
    if(val==0){
      $('#val_select_all').val('1');
      $('.checkbox1').each(function(){
        this.checked = true;
      });
    }else{
      $('#val_select_all').val('0');
      $('.checkbox1').each(function(){
        this.checked = false;
      });
    }
  }
  function passpayrollsteps(){
    var favorite = [];
    var ick=0;
    $.each($("input[name='checkbox1']:checked"), function(){
      favorite.push($(this).val());
      ick=ick+1;
    });

    if(ick>0){
      var pay_year = $('#pay_year').val();
      var pay_month = $('#pay_month').val();
      window.location.href = 'index.php?option=COM_INSERT_PAYROLL_PAY_AREA_STEPS&PAY_YEAR='+pay_year+'&PAY_MONTH='+pay_month+'&PPJ='+favorite;
    }else{

      var msg = <?php echo "'".$_SESSION['error_message']['9038']."'" ?>;
      swal('<?php echo ucwords($_SESSION['config']['name']); ?>', msg, "error");

    }
  }

  function resetthepayarea(payYear, payMonth, payAreaId){
    swal({
      title: 'Confirm Reset',
      text: 'Are you sure you want to reset the payroll data for this pay area? This action will delete all payroll records for the selected month and year.',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Reset',
      cancelButtonText: 'Cancel',
      confirmButtonClass: 'btn btn-danger',
      cancelButtonClass: 'btn btn-secondary'
    }, function(isConfirm){
      if(isConfirm){
        jQuery.ajax({
          url: 'ajax.php',
          type: 'POST',
          dataType: 'json',
          data: {
            page: 'payroll_reset_pay_area',
            pay_year: payYear,
            pay_month: payMonth,
            pay_area_id: payAreaId
          },
          success: function(response){
            if(response && ((response.status === 'success' || response.status === true) || (Array.isArray(response) && response.length > 0))){
              var msg = response.message ? response.message : 'Payroll reset completed successfully';
              swal('<?php echo ucwords($_SESSION['config']['name']); ?>', msg, 'success');
              setTimeout(function(){
                window.location.reload();
              }, 800);
            }else{
              var errMsg = (response && response.message) ? response.message : 'Unable to reset payroll for selected pay area';
              swal('<?php echo ucwords($_SESSION['config']['name']); ?>', errMsg, 'error');
            }
          },
          error: function(){
            swal('<?php echo ucwords($_SESSION['config']['name']); ?>', 'Something went wrong. Please try again.', 'error');
          }
        });
      }
    });
  }
</script>
<div class="pcoded-inner-content mob-pd p-0">
  <div class="main-body">
    <div class="page-wrapper mob-pd p-0">
      <div class="page-body">
        <div class="card">
          <div class="card-header table-card-header">
            <div class="col-lg-2">
              <h5 class=""><?php echo $title;  ?></h5>
              <div class="card-header-right table-card-header text-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                  <i class="icofont icofont-reply"></i>
                  Back
                </a>
              </div>
            </div>
          </div>
          <div class="card-block card-custom mt-3">
            <input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">
            <div class="mb-3">
              <?php
                $month = strtotime(date('Y').'-'.date('m').'-01 -12 months');
                $end = strtotime(date('Y').'-'.date('m').'-01 +0 months');
                $monthd = $month; $k=1;
                while($month <= $end){
                $disss = date('M Y',$month);
                $dis_year = date('Y',$month);
                $dis_month = date('n',$month);
                if($_GET['PAY_YEAR'] == $dis_year && $_GET['PAY_MONTH'] == $dis_month ){
                  $class = 'btn btn-primary';
                }else{
                  $class = 'btn btn-light';
                } ?>
                <div class="d-inline-block m-r-5 b-radius-5 nav-item mb-2">
                  <a class="<?php echo $class; ?> nav-link  me-6 active fs-12" style="font-size:12px;" href="index.php?option=COM_INSERT_PAYROLL_PAY_AREA_WISE&PAY_YEAR=<?php echo $dis_year; ?>&PAY_MONTH=<?php echo $dis_month; ?>"> <?php echo $disss; ?></a>
                </div>
              <?php $month = strtotime("+".$k." month", $monthd);  $k++;
} ?>
            </div>
            <div class="row">
              <div class="card-header table-card-header ">
                <h5 class="">
                  <b class="text-dark f-18 f-w-600"><?php echo date("F", mktime(0, 0, 0, $_GET['PAY_MONTH'], 10))." ".$_GET["PAY_YEAR"]; ?>  Payroll </b>
                </h5>
                <?php
                $date2 = date("Y-m-d", strtotime('+1 months', strtotime($date1)));
                $date2 = date("Y-m-d", strtotime('-1 days', strtotime($date2)));
                ?>
                <div class="card-header-right table-card-header text-right">
                    <?php if(count((array)$pay_areas)>0){ ?>
                    <button type="button" onclick="passpayrollsteps()" class="btn btn-success mb-2 desktop-view"> Generate Payroll - Steps
                    </button>
                    <?php } ?>

                                  <?php if(count((array)$check)>1){ ?>
                    <a href="index.php?option=COM_INSERT_PAYAREA_PAYROLL&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&PPJ=<?php echo implode(",",(array)$payareaids); ?>"  class="btn btn-success mb-2 desktop-view" > View All Payroll
                    </a>
                    <?php } ?>
                </div>
              </div>
            </div>
            <div class="row p-3">
              <input type="hidden" name="pay_year" id="pay_year" value="<?php echo $_GET["PAY_YEAR"]; ?>">
              <input type="hidden" name="pay_month" id="pay_month" value="<?php echo $_GET["PAY_MONTH"]; ?>">
              <table id="new-cons11" class="table table-striped table-bordered nowrap mt-4 table-sm">
                <thead>
                  <tr>
                    <th scope="col">

                    </th>
                    <th scope="col">Sl No</th>
                    <th scope="col">Payroll Name</th>
                    <th scope="col">Earnings</th>
                      <th scope="col">Deductions </th>
                    <th scope="col">View Payroll</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; if(count((array)$pay_areas)>0 && $count_emp>0){
                   foreach($pay_areas as $data){
                  ?>
                  <tr>
                    <td>
                      <div class="checkbox-fade fade-in-primary" name="individual_approvalcheck">
                        <label class="checkbox checkbox-success">
                          <input aria-label="Checkbox" type="checkbox"<?php if(in_array($data['id'],(array)$payrolls)){ echo "disabled";
} ?>  onclick="tocheckdrodownhrwork()" class="itemRow checkbox1" name="checkbox1" value="<?php echo $data['id']; ?>" onkeydown="">
                          <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span>
                        </label>
                      </div>
                    </td>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $data['name']; ?></td>
                    <td><?php echo $payrolldata[$data['id']]['gross_pay']; ?></td>
                    <td><?php echo $payrolldata[$data['id']]['deductions']; ?></td>
                    <td>
                      <?php if(in_array($data['id'],(array)$payrolls)){ ?>
                          <a href="index.php?option=COM_INSERT_PAYAREA_PAYROLL&PAY_YEAR=<?php echo $_GET['PAY_YEAR']; ?>&PAY_MONTH=<?php echo $_GET['PAY_MONTH']; ?>&PPJ=<?php echo $data['id']; ?>"  class="btn btn-success mb-2 desktop-view">
                              View
                       </a>

                       <button type="button" onclick="resetthepayarea(<?php echo $_GET['PAY_YEAR'].", ".$_GET['PAY_MONTH'].", ".$data['id']; ?>)"  class="btn btn-primary mb-2 desktop-view m-l-5">
                        ReSet
                       </button>
                            <?php } ?>

                          </td>
                  </tr>
                  <?php $i++;
} ?>
                    <?php }else{ ?>
                    <tr>
                    <td colspan='6' class="text-center  text-danger">
                      <?php if(count((array)$pay_areas)==0){
                        echo  $_SESSION['error_message']['9037'];
                      }
                      if($count_emp==0){
                        echo  " ".$_SESSION['error_message']['9108'];
                      }
                      ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
