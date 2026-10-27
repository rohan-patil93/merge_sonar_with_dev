<?php $_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitesopallocation'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertesopallocations&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$url1 = API."?mode=getesopallocations&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);

$esop_plans = $result['esop_plans'];
$esop_allocations_all = $result['esop_allocations_all'];
$esop_allocations = $result['esop_allocations'];
$esop_plan_buckets = $result['esop_plan_buckets'];
$company_contribution_users = $result['company_contribution_users'];
$application_users = $result['application_users'];

$statuse = ViewStatusData();
$status_progress = $result['status_progress'];

?>
<script type="text/javascript">
    function selectESOPPlans(esop_plan_id){
        var date_of_joining = $("#date_of_joining").val();
        jQuery.ajax({
          url: "ajax.php",
          data:'page=getesopplansbucketsoption&esop_plan_id='+esop_plan_id+'&date_of_joining='+date_of_joining,
          type: "POST",
          success:function(data){
            var result = JSON.parse(data);
            console.log(result);
            $("#tenure").val(result.tenure);
            $("#esop_bucket_id").html(result.option);
            $("#grant_price").val(0);
            $("#option_granted").val(0);
            $("#discount_per_option").val(0);
            $("#total_discount").val(0);
            $("#expected_option_benefit").val(0);
            $("#total_expected_benefit").val(0);
            $("#benefit_ctc").val(0);
          },
          error:function (){}
        });
    }
    function selectESOPPlansBuckets(esop_bucket_id){
        var annual_ctc = $("#annual_ctc").val();
        var stock_value = $("#stock_value").val();
        if(annual_ctc !="" && annual_ctc !="0"){
            jQuery.ajax({
              url: "ajax.php",
              data:'page=getesopplansbuckets&type=getbucket&esop_bucket_id='+esop_bucket_id,
              type: "POST",
              success:function(data){
                var result = JSON.parse(data);
                console.log("stock_value - "+stock_value);

                $("#vesting_detail").html(result.vesting_schedule);
                var esopbucket = result.bucket_details;
                console.log(esopbucket);
                var expected_option_benefit = esopbucket.expected_option_benefit;
                if(expected_option_benefit == 0){
                    expected_option_benefit = esopbucket.grant_price;
                }
                var allocatesharectccnt =  parseInt(stock_value/expected_option_benefit);
                console.log('CTC allocate share - '+allocatesharectccnt);
                console.log('Bucket aallocate share - '+esopbucket.unallocated_shares);
                var options_granted = esopbucket.unallocated_shares;
                if(allocatesharectccnt <= options_granted){
                    options_granted = allocatesharectccnt;
                }
                discount_per_option = esopbucket.discount_per_option;
                console.log("final granted - "+options_granted);
                $("#grant_price").val(esopbucket.grant_price);
                $("#option_granted").val(options_granted);
                $("#discount_per_option").val(discount_per_option);
                $("#total_discount").val(discount_per_option*options_granted);
                $("#expected_option_benefit").val(expected_option_benefit);
                var total_expected_benefit = (parseFloat(expected_option_benefit)*parseFloat(options_granted));
                $("#total_expected_benefit").val(total_expected_benefit.toFixed(2));
                var benefit_ctc =  (parseFloat(total_expected_benefit)/parseFloat(annual_ctc))*100;
                $("#benefit_ctc").val(benefit_ctc.toFixed(2));
                $(".datepicker").datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: date_format,
                    yearRange: "-60:+10",
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                });
              },
              error:function (){}
            });
        }else{
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: 'Please Enter Annual CTC',
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            });
        }
    }
    function selectEmp(empid){
        console.log(empid);
        if(empid == ''){
             $("#insertfrmopen").css({"pointer-events":"none","opacity":"0.8"});
        }else if(empid != 0){
             $("#insertfrmopen").css({"pointer-events":"visible","opacity":"1"});
            jQuery.ajax({
              url: "ajax.php",
              data:'page=getcompanycontributionusers&id='+empid+'&user_type=EMP',
              type: "POST",
              success:function(data){
                console.log(data);
                var result = JSON.parse(data);
                $("#emp_code").val(result.emp_code);
                $("#date_of_joining").val(result.date_of_joining);
                $("#emp_name").val(result.display_name);
                $("#designation").val(result.designation);
                $("#annual_ctc").val(result.annual_ctc);
                $("#user_email").val(result.user_email);
                $("#allocatassetlocation").select2("val", assetdet[3]);
                $("#tenure").val('');
               }
            });
            $("#emp_code").prop("readonly", true);
            $("#date_of_joining").prop("readonly", true);
            $("#emp_name").prop("readonly", true);
            $("#user_email").prop("readonly", true);
        }else{
            $("#insertfrmopen").css({"pointer-events":"visible","opacity":"1"});
            $("#emp_code").val('');
            $("#date_of_joining").val('');
            $("#emp_name").val('');
            $("#designation").val('');
            $("#annual_ctc").val('');
            $("#tenure").val('');
            $("#emp_code").prop("readonly", false);
            $("#date_of_joining").prop("readonly", false);
            $("#emp_name").prop("readonly", false);
            $("#designation").prop("readonly", false);
            $("#user_email").prop("readonly", false);
        }
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
function deletRowVestage(id,rowid){
    $("#deleteidvesting_"+id).remove();
    calculateVestingPercentage(rowid);
}
function calculateVestingPercentage(rowid){
    var totaldistributionqty = 0;
    $(".vestingpercentage_1").each(function(){
        totaldistributionqty +=parseFloat($(this).val());
    });
    if(isNaN(totaldistributionqty)){
        totaldistributionqty = 0;
    }
    $("#totaldistributionqty_1").text(totaldistributionqty);
    $("#totaldistributionpending_1").text(100-parseFloat(totaldistributionqty));
}
var maxrows = 1000;
  function addMore(rowid){
    maxrows = maxrows+1;
    var html = '';
    html += '<div class="row" id="deleteidvesting_'+maxrows+'">';
    html += '<div class="form-group col-lg-4"><input aria-label="Vesting Date[]" type="text" class="form-control datepicker vesategdatpicker_'+rowid+'" name="vesting_date[]" ></div>';
    html += '<div class="form-group col-lg-3"><input aria-label="Vesting Percentage[]" type="number" class="form-control text-right vestingpercentage_'+rowid+'" name="vesting_percentage[]" onblur="calculateVestingPercentage('+rowid+')"></div>';
    html += '<div class="form-group col-lg-4"><input aria-label="Till Date[]" type="text" class="form-control datepicker vesategetilldate_'+rowid+'" name="till_date[]"></div>';

    html += '<div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRowVestage('+maxrows+','+rowid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
    html += '</div>';
    $("#addmorediv-"+rowid).append(html);
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        dateFormat: date_format,
        yearRange: "-60:+10",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true,
        });
  }

function calculateStockValue(){
    var annual_ctc = $("#annual_ctc").val();
    var value = $("#value").val();
    var disValue1 = 0;

    var disType  = $("#stock:checked").val();
    if(disType=='P'){
        disValue1 = (value/100)*annual_ctc;
    }else if(disType=='A'){
        disValue1 = value;
    }
    if(isNaN(disValue1)){
        disValue1 = 0;
    }
    $("#stock_value").val(disValue1);
    if(parseFloat(annual_ctc) < parseFloat(disValue1)){
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Stock Value cannot be more than "+annual_ctc+", Please check", "error");
        var value = $("#value").val(0);
        calculateStockValue();
    }
}
</script>
<div class="row">
        <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
 <?php if($esop_allocations['id'] !=""){
    $workflowsteps = getWorkflowStatus('esop_allocations',$esop_allocations['id'],$esop_allocations['current_status']);
    echo $workflowsteps['breadcrumb'];
} ?>
    <div class="col-lg-4">
        <form method="post">
        <div class="card">
            <?php
            if($esop_allocations['id'] !=""){ echo $workflowsteps['current_status_ribbon'];
}  ?>
            <div class="card-header">
                <h5>ESOP Allocation</h5>
                <div class="card-header-right table-card-header  m-r-50">
                    <?php echo $workflowsteps['workflow_btn']; ?>
                </div>
            </div>
            <div class="card-body" <?php $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,' ');
        if((in_array($esop_allocations['current_status'], (array)$check_array) && $esop_allocations['esop_connect'] ==$_SESSION['userid']) || $esop_allocations['current_status'] ==""){
}else{ ?> style="pointer-events: none; opacity: 0.6;" <?php
} ?>>
                <div class="row">
                <div class="form-group col-lg-12">
                    <span class="form-label">Employee<span class="text-danger">*</span></span>
                    <select aria-label="Company Contribution User" class="form-select js-example-basic-single" name="company_contribution_user_id" onchange="selectEmp(this.value)">
                         <option value="">Select Any One</option>
                         <?php foreach($company_contribution_users as $ccu){ ?>
                         <option value ='<?php echo $ccu['id']; ?>' <?php echo $esop_allocations['company_contribution_user_id'] ==$ccu['id']?"selected":""; ?>> <?php echo $ccu['display_name']; ?></option>
                     <?php } ?>
                        <option value ='0'>New Employee</option>
                    </select>
                </div>
            </div>
                <div class="row" id="insertfrmopen"  <?php if($esop_allocations['company_contribution_user_id']==""){ ?>style="pointer-events: none; opacity: 0.8;" <?php
} ?>>
                <div class="form-group col-lg-6">
                    <span class="form-label">Employee Code <span class="text-danger">*</span></span>
                    <input aria-label="Emp Code" type="text" class="form-control" name="emp_code"  id="emp_code" value="<?php echo $esop_allocations['emp_code']; ?>" required>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Date of Joning <span class="text-danger">*</span></span>
                    <input aria-label="Date Of Joining" type="text" class="form-control datepicker" name="date_of_joining" id="date_of_joining" value="<?php echo fromDate($esop_allocations['date_of_joining']); ?>" required>
                </div>
                <div class="form-group col-lg-12">
                    <span class="form-label">Employee Name <span class="text-danger">*</span></span>
                    <input aria-label="Emp Name" type="text" class="form-control" name="emp_name" id="emp_name" value="<?php echo $esop_allocations['emp_name']; ?>" required>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Designation </span>
                    <input aria-label="Designation" type="text" class="form-control" name="designation" id="designation" value="<?php echo $esop_allocations['designation']; ?>" >
                </div>
                 <div class="form-group col-lg-6">
                    <span class="form-label">Tenure (Year)</span>
                    <input aria-label="Tenure" type="text" class="form-control" name="tenure"  id="tenure" value="<?php echo $esop_allocations['tenure']; ?>" readonly >
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Annual CTC <span class="text-danger">*</span></span>
                    <input aria-label="Annual Ctc" type="text" class="form-control text-right" name="annual_ctc" id="annual_ctc"  value="<?php echo $esop_allocations['annual_ctc']; ?>" required>
                </div>
                 <div class="form-group col-lg-6">
                    <span class="form-label">Approval Manager<span class="text-danger">*</span></span>
                    <select aria-label="Esop Connect" class="form-control js-example-basic-single" name="esop_connect"  id="esop_connect" onchange="selectESOPPlans(this.value)" required>
                         <option value="">Select Any One</option>
                        <?php foreach($application_users as $au){?>
                             <option value="<?php echo $au['id']; ?>" <?php echo ($au['id']==$esop_allocations['esop_connect'] || ($au['id']==$_SESSION['userid'] && $esop_allocations['esop_connect']=="")) ?"selected":""; ?>><?php echo $au['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-12 form-group">
                    <span class="form-label">Stock<span class="text-danger">*</span></span>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                            <label class="d-inline-block">
                                <input aria-label="Stock" type="radio"  name="stock"  id="stock" onclick="calculateStockValue()" value="A" <?php echo $esop_allocations['stock'] =="A"?"checked":""; ?> onkeydown="">
                                <i class="helper"></i>Amount
                            </label>
                        </div>
                        <div class="radio radio-inline">
                            <label class="m-l-10 d-inline-block">
                                <input aria-label="Stock" type="radio" name="stock" checked="" id="stock_2" onclick="calculateStockValue()" value="P" <?php echo ($esop_allocations['stock'] =="" || $esop_allocations['stock'] =="P")?"checked":""; ?> onkeydown="">
                                <i class="helper"></i> Percentage(%)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Value <span class="text-danger">*</span></span>
                    <input aria-label="Value" type="text" class="form-control text-right" name="value" id="value"  onblur="calculateStockValue()" value="<?php echo $esop_allocations['value'];?>" required>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Stock Value <span class="text-danger">*</span></span>
                    <input aria-label="Stock Value" type="text" class="form-control text-right" name="stock_value" id="stock_value"  value="<?php echo $esop_allocations['stock_value'];?>" readonly>
                </div>

                <div class="form-group col-lg-12">
                    <span class="form-label">Email <span class="text-danger">*</span></span>
                    <input aria-label="User Email" type="email" class="form-control" name="user_email" id="user_email"  value="<?php echo $esop_allocations['user_email']; ?>" required>
                </div>


                <div class="form-group ">
                    <span class="form-label">ESOP Plans <span class="text-danger">*</span></span>
                    <select aria-label="Esop Plan" class="form-control js-example-basic-single" name="esop_plan_id" onchange="selectESOPPlans(this.value)" required>
                         <option value="">Select Any One</option>
                        <?php foreach($esop_plans as $esopplan){?>
                             <option value="<?php echo $esopplan['id']; ?>" <?php echo $esopplan['id']==$esop_allocations['esop_plan_id']?"selected":""; ?>><?php echo $esopplan['plan_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <span class="form-label">Bucket <span class="text-danger">*</span></span>
                    <select aria-label="Esop Bucket" class="form-control js-example-basic-single" name="esop_bucket_id" id="esop_bucket_id" onchange="selectESOPPlansBuckets(this.value)" required>
                        <option value="">Select Any One</option>
                        <?php foreach($esop_plan_buckets[$esop_allocations['esop_plan_id']] as $esopplan){ ?>
                             <option value="<?php echo $esopplan['id']; ?>" <?php echo $esopplan['id']==$esop_allocations['esop_bucket_id']?"selected":""; ?>><?php echo ucwords($esopplan['bucket_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="card  pt-3 pb-3">
                        <div class="form-group">
                            <span class="form-label">Grant Date <span class="text-danger">*</span></span>
                            <input aria-label="Grant Date" type="text" class="form-control datepicker" name="grant_date" id="grant_date" value="<?php echo fromDate($esop_allocations['grant_date']); ?>" required>
                        </div>
                        <div id="vesting_detail">
                           <div id="addmorediv-1">
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <span>Allocation Date <span class="text-danger">*</span></span>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <span>Percentage <span class="text-danger">*</span></span>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <span>Till Date <span class="text-danger">*</span></span>
                                    </div>
                                </div>
                                <?php

                                $vesting_detail = json_decode($esop_allocations['vesting_schedule'],1);
                                  $totaldis = 0;
                                  $i = 1;
                                foreach($vesting_detail as $vd){
                                  $totaldis +=$vd['vesting_percentage'];
                            ?>
                                <div class="row" id="deleteidvesting_<?php echo $i; ?>">
                                    <div class="form-group col-lg-4">
                                       <input aria-label="Vesting Date[]" type="text" class="form-control datepicker vesategdatpicker_1" name="vesting_date[]" value="<?php echo fromDate($vd['vesting_date']); ?>" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <input aria-label="Vesting Percentage[]" type="number" class="form-control text-right vestingpercentage_1" name="vesting_percentage[]" value="<?php echo $vd['vesting_percentage']; ?>" onblur="calculateVestingPercentage(<?php echo $i; ?>)" required>
                                    </div>
                                    <div class="form-group col-lg-4">
                                       <input aria-label="Till Date[]" type="text" class="form-control datepicker vesategetilldate_1" name="till_date[]" value="<?php echo fromDate($vd['till_date']); ?>" required >
                                    </div>
                                    <div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRowVestage('<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </div>
                                </div>
                            <?php  $i++;
} ?>
                        </div>
                        <div class="card-footer pt-0">
                            <button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addMore('1')"><i class="fa fa-check m-r-10"></i>Add More</button>
                            <span class="float-end m-t-20">
                                <span class="text-danger">Un-Schedule (%) : </span>
                                <span id="totaldistributionpending_1"><?php echo 100-$totaldis; ?></span>
                                <span class="text-warning m-l-20">Total (%) : </span>
                                <span id="totaldistributionqty_1"><?php echo $totaldis; ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Grant Price</span>
                    <input aria-label="Grant Price" type="text" class="form-control text-right"  name="grant_price" id="grant_price" value="<?php echo $esop_allocations['grant_price']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Options Granted</span>
                    <input aria-label="Option Granted" type="text" class="form-control text-right" name="option_granted" id="option_granted" value="<?php echo $esop_allocations['option_granted']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Discount per Option</span>
                    <input aria-label="Discount Per Option" type="text" class="form-control text-right" name="discount_per_option"  id="discount_per_option" value="<?php echo $esop_allocations['discount_per_option']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Total Discount</span>
                    <input aria-label="Total Discount" type="text" class="form-control text-right" name="total_discount" id="total_discount" value="<?php echo $esop_allocations['total_discount']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Expected Options Benefit</span>
                     <input aria-label="Expected Option Benefit" type="text" class="form-control text-right" name="expected_option_benefit"  id="expected_option_benefit" value="<?php echo $esop_allocations['expected_option_benefit']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Total Expected Benefit</span>
                    <input aria-label="Total Expected Benefit" type="text" class="form-control text-right" name="total_expected_benefit"  id="total_expected_benefit" value="<?php echo $esop_allocations['total_expected_benefit']; ?>" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Benefit, CTC</span>
                    <div class="input-group">
                        <input aria-label="Benefit Ctc" type="text" class="form-control text-right" name="benefit_ctc" id="benefit_ctc" value="<?php echo $esop_allocations['benefit_ctc']; ?>" readonly>
                        <span class="input-group-text addon-style" >
                            <span class="">%</span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <span class="form-label">Notes</span>
                    <textarea aria-label="Note" class="form-control" name="note"><?php echo $esop_allocations['note']; ?></textarea>
                </div>
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">

             <?php if($rid ==""){ ?>
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-lg-3 m-l-15" name="submitesopallocation"><i class="fa fa-check m-r-10"></i>Submit</button>
            <?php }else{ ?>
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-lg-3 m-l-15" name="submitesopallocation"><i class="fa fa-check m-r-10"></i>Update</button>
            <?php } ?>

            </div>

            </div>
        </div>
    </form>
    </div>
    <div class="col-lg-8 ui-sortable-handle">
        <div class="card">
            <div class="card-header">
                <h5>Recent ESOP Allocations</h5>
                <div class="card-header-right table-card-header">
                    <a href="index.php?option=COM_VIEW_ESOP_ALLOCATIONS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i>Details</a>
                <?php if($rid !=""){ ?>
                    <a href="index.php?option=COM_INSERT_ESOP_ALLOCATIONS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i>ESOP Allocation</a>
                <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 715px;">
                    <?php if(count((array)$esop_allocations_all) != 0){ ?>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th scope="col">Employee</th>
                            <th scope="col">ESOP Plan</th>
                            <th scope="col">Bucket</th>
                            <th scope="col" class="text-right">Grant Price</th>
                            <th scope="col" class="text-right">Options Granted</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        <?php foreach($esop_allocations_all as $esopallocations){ ?>
                            <tr>
                                <td>
                                    <span class="d-block"><?php echo $esopallocations['emp_name']; ?></span>
                                    <span><?php echo $esopallocations['emp_code']; ?></span></td>
                                <td><?php echo $esop_plans[$esopallocations['esop_plan_id']]['plan_name']; ?></td>
                                <td><?php echo $esop_plan_buckets[$esopallocations['esop_plan_id']][$esopallocations['esop_bucket_id']]['bucket_name']; ?></td>
                                <td class="text-right"><?php echo customeummarmonyformateifine($esopallocations['grant_price']); ?></td>
                                <td class="text-right"><?php echo $esopallocations['option_granted']; ?></td>
                                <td>
                                    <span class="form-label label label-md" style="color:<?php echo $statuse[$esopallocations['current_status']]['color']; ?>; background-color:<?php echo $statuse[$esopallocations['current_status']]['background_color']; ?>;"><?php echo $status_progress[$esopallocations['current_status']]['name']; ?></span>
                                </td>
                                 <td class="text-center"><a href="index.php?option=COM_INSERT_ESOP_ALLOCATIONS&Rid=<?php echo deocdeId('id:'.$esopallocations['id']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" ><i class="fa fa-edit"></i></a>
                                <?php if($esopallocations['current_status'] == ST_APPROVED){?>
                                 <a href="index.php?option=COM_INSERT_ESOP_ALLOCATION_APPROVALS&Rid=<?php echo deocdeId('id:'.$esopallocations['id']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Share" class="m-l-10"><i class="ti-wallet"></i></a>
                             <?php } ?>
                                 </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No ESOP Allocations</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
