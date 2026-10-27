<?php 
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid= $rid[1];
$vdata1["where"] = array('id'=> $rid,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$vdata1["field"] = "id,emp_id,current_status";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_expenses&data=".$vdata; 
$vdata_details = callAPI($vurl);
$main_data11 = json_decode($vdata_details,1);
$main_data11 =$main_data11[0];
$emp_id = $main_data11['emp_id']!=""?$main_data11['emp_id']:$_SESSION['emp_id'];
$url = API."?mode=getemployeeexpencesdetails&emp_id=".$emp_id."&exp_id=".$rid;
$response = callAPI($url);
$response= json_decode($response,1);
//echo "<pre>"; print_r($response);echo "</pre>";
$main_details = $response['main_details'];
$sub_data = $response['sub_details'];
$projects_list = $response['projects'];
$emp_details = $response['emp_details'];
//echo "<pre>"; print_r($response['expense_categories']);echo "</pre>";
foreach($response['expense_categories'] as $catdat){
  $categoryexpense[$catdat['category_id']]=$catdat['category_name'];
}
//echo "<pre>"; print_r($categoryexpense);echo "</pre>";
foreach($response['expenses_mileages_perdiem'] as $catdat){
  if($catdat['expense_type']=='0'){
    $mileageslist[$catdat['id']]=$catdat['name'];
  }else{
    $perdiamarray[$catdat['id']]=$catdat['name'];
  }
}
$stausresult = ViewStatusData();
$File_Uploads_Path = "uploads/reimbursement/";
?>
<script>
  function expensesDetailsPopUp(exp_id,sub_id){
    var table_name = 'project_timesheets';
    var emp_id = $("#emp_id").val();
    jQuery.ajax({
     url: "views/custom_insert_emp_expense_details_popup.php",
     data:'table='+table_name+'&exp_id='+exp_id+'&sub_id='+sub_id+'&emp_id='+emp_id,
     type: "POST",
     success:function(data){
      $('#dynamic_popup11').html(data);
      $('#kt_modal_dynamic_popup2').modal("show");
    },
    error:function (){}
  });
  }
 function displaystausdrop(id,table,status_id){
   //alert("id");
   $.ajax({
     url: "ajax.php",
     data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
     type: "POST",
     success:function(data){
       console.log(data);
       $("#dropdownlist").html(data);
     },
     error:function (){}
   });
   
 }
 
function slidepopupdetails(id){
  closedPopup();
  $(".custom-popup-slide-"+id).show('slow');
}
function closedPopup(){
  $(".table-popup-open").hide('fast');
}
function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                         window.location.reload();
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        }); 
}
</script>
      <div class="card">
        <?php 
        $workflowsteps = getWorkflowStatus('emp_expenses',$main_data11['id'],$main_data11['current_status']);
            echo $workflowsteps['current_status_ribbon'];

?>
        <div class="card-header">
          <h5>Expense Report</h5>
          <div class="card-header-right table-card-header m-r-50">
            <?php 
                echo $workflowsteps['workflow_btn'];
            ?>
            <?php 
    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
    if(in_array($main_data11['current_status'], (array)$check_array) AND $main_data11['emp_id'] ==$_SESSION['emp_id']){ 
?>
<button onclick="expensesDetailsPopUp('<?php echo $main_details['id']; ?>','')" class="btn btn-success desktop-view" <?php echo $disable; ?>>
              <i class="icofont icofont-plus icon-white"></i>
              Attach Expense</button>
  <?php  }else{ $disable = "disabled" ; }

    ?>

            
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i> Back </a>
            </div>
          </div>
          <div class="row p-4">
            <?php echo $workflowsteps['breadcrumb'];  ?>
            <div class="">
              <table class="table table-ex">
                <thead>
                  <tr>
                    <th>Details</th>
                  </tr>    
                </thead>
                <tbody>
                  <?php foreach($sub_data as $details){ 
                    $totalexpense += $details['expense_amount'];
                    ?>
                    <tr>
                      <td>
                        <div class="align-items-center" id="table-popup" >
                          <div class="d-inline-block" onclick="slidepopupdetails('<?php echo $details['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Expense">
                            <h3 class="f-16 text-primary" >
                              <?php 
                              if($details['expense_type']=='1'){ 
                                $category_name = $mileageslist[$details['category_id']];
                                $expense_type = "Mileage";
                              }elseif($details['expense_type']=='2'){
                                $category_name = $perdiamarray[$details['category_id']];
                                $expense_type = "Per-Diam";
                              }else{
                               $category_name = $categoryexpense[$details['category_id']];
                               $expense_type = "Expense";

                             }
                             ?>
                             <?php echo $category_name; ?></h3>
                            <h6 class="text-muted f-14">Invoice : <?php echo $main_details['expense_code']; ?>, <?php echo fromDate($details['bill_date']); ?></h6>
                           </div>
                           <div class="table-popup-open custom-popup-slide-<?php echo $details['id']; ?>">
                            <div class="card">
                              <div class="card-body">
                                <div class="row align-items-center">
                                  <div class="col">
                                    <h6 class="m-b-5 f-16"><?php echo $category_name; ?></h6>
                                    <i class="ti-close float-end" onclick="closedPopup()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                                    <h6 class="m-b-0 text-muted f-12"><?php echo $main_details['reference_no']; ?></h6>
                                  </div>
                                </div>
                                <p class="mt-3 text-muted" style="word-break:break-word;white-space:break-spaces;"><?php echo $details['description']; ?></p>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Expense Type</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo $expense_type; ?></small>
                                  </div>
                                </div>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Amount</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo customeummarmonyformate($details['rate']); ?></small>
                                  </div>
                                </div>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Tax</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo customeummarmonyformate($details['tax_amount']); ?></small>
                                  </div>
                                </div>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Total</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo customeummarmonyformate($details['expense_amount']); ?></small>
                                  </div>
                                </div>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Merchant Name</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo $details['merchant_name'] !=""?$details['merchant_name']:"-"; ?></small>
                                  </div>
                                </div>
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                    <small class="f-w-600">Bill Number</small>
                                  </div> 
                                  <div class="col-auto d-flex align-items-center">
                                    <small><?php echo $details['bill_no']!=""?$details['bill_no']:"-"; ?></small>
                                  </div>
                                </div> 
                                <div class="row text-center">
                                  <div class="col-auto d-flex align-items-center col-lg-5">
                                   <small class="f-w-600">Bill Date</small>
                                 </div> 
                                 <div class="col-auto d-flex align-items-center">
                                  <small><?php echo $details['bill_date'] !=""?fromDate($details['bill_date']):"-"; ?></small>
                                </div>
                              </div>  
                              <div class="mt-4">
                                <button class="btn btn-primary" onclick="expensesDetailsPopUp('<?php echo $main_details['id']; ?>','<?php echo $details['id']; ?>')" <?php echo $disable; ?>>Edit</button>
                                <button class="btn btn-success" onclick="expensesDetailsPopUp('<?php echo $main_details['id']; ?>','')" <?php echo $disable; ?>>Add More</button>
                              </div> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="text-right">
                      <a href="<?php echo $File_Uploads_Path.$details['bill_file']; ?>" download><?php echo $details['bill_file']; ?> </a>
                    </td>
                    <td class="text-right">
                      <?php echo "Rs. ".customeummarmonyformate($details['expense_amount']); ?>
                    </td>
                    <td>
                      <a class="text-primary cursor-pointer" onclick="expensesDetailsPopUp('<?php echo $main_details['id']; ?>','<?php echo $details['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit Expense" <?php echo $disable; ?>>
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                  </tr>
                <?php } ?>

                <tr>
                  <td>
                    -  
                  </td>
                  <td align="right">
                    -
                  </td>
                  <td align="right">
                    <b class="f-w-600">Total - Rs. <?php echo customeummarmonyformate($totalexpense); ?></b>
                  </td>
                </tr>
              </tbody>
            </table>
            <input type="hidden" name="emp_id"  id="emp_id" value="<?php echo $emp_id; ?>">
          </div>
        </div>
      </div>

