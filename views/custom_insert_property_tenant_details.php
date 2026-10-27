<?php
if($_GET['ajaxcall'] =='Y'){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $dyncss = 'style="pointer-events: none; opacity: 0.6;"';
}
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['textediter'] = '1';
$_SESSION['apply_js']['table'] = '1';
if(isset($_POST['submittenantdetails'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
       $url = API."?mode=insertpropertytenantdetails&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
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
            $msgcode = 3000; //insert
             $_SESSION['insert_msg'] = $msgcode;
               header("location:index?option=COM_LIST_PROPERTY_TENANT_DETAILS");
        }
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
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getpropertytenantdetails&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$property_owners = $result['property_owners'];
$property_tenant_details = $result['property_tenant_details'];
$property_owner_details = $result['property_owner_details'];
$podareas = $result['property_owner_detail_areas'];
$rentbilltoberaise = $result['rent_bill_to_be_raise_type'];
$billtoberaisedtype = $result['bill_to_be_raise_type'];
$rental_bill_types = $result['rental_bill_types'];
$property_tenant_billings = $result['property_tenant_billings'];
$hsn_and_sac_codes = $result['hsn_and_sac_codes'];
$customers = $result['customers'];
$rental_bill_sub_types = $result['rental_bill_sub_types'];
$utilityuom = $result['utilityuom'];
$property_owner_detail_areas = array();
foreach($podareas as $podarea){
    $property_owner_detail_areas[$podarea['id']] = $podarea;
}
$podaajax = json_decode($property_tenant_details['property_owner_detail_areas'],1);
foreach($podaajax as $poda){
    $totaldis +=$poda['rent'];
}
$owners = array();
foreach($property_owners as $coa){
    $owners[$coa['id']] =$coa['owner_name'];
}

?>
<script type="text/javascript">

    function formSubmitTenantDetails(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            $("#tenantdetailform").submit();
        }
    }
    var maxrowid = 5000;
    function addMoreFollowing(){
        maxrowid = maxrowid+1;
        var html = '';
        html += '<div class="row" id="deleterowid_'+maxrowid+'">';
        html += '<div class="form-group col-lg-4 p-0"><input aria-label="Name" type="text" name="following_name[]" class="form-control" value="" placeholder="Name"></div>';
        html += '<div class="form-group col-lg-4"><textarea aria-label="Following Address[]" class="form-control" name="following_address[]" style="height: 91px;"></textarea></div>';
        html += '<div class="form-group col-lg-4"><input aria-label="Email Id" type="text" class="form-control" name="following_email[]" value="" placeholder="Email Id"><input aria-label="Contact Number" type="text" class="form-control mt-2" name="following_mobile[]" value="" placeholder="Contact Number"></div>';

        html += '</div>';
        $("#addmorerow").append(html);
    }
    function deletRow(id,rowid){
        $("#deleterowid_"+id).remove();
    }
    function getPropertyOwner(ownerid){
        console.log(ownerid);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getajaxpropertyownerdetails&ownerid='+ownerid,
            type: "POST",
            success:function(data){
              console.log(data);
              $("#property_owner_detail_id").html(data);
              setAggrementDetails();
            },
            error:function (){}
        });

    }
    function getPropertyOwnerDetail(ownerdetailid){
        console.log(ownerdetailid);
        var rid = $("#rid").val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getajaxpropertyownerdetailareas&rid='+rid+'&ownerdetailid='+ownerdetailid,
            type: "POST",
            success:function(data){
              console.log(data);
              $("#ownerproperties").html(data);
              setAggrementDetails();
              $("#ownerpropertiesarea").html('');
              calculateRent();
            },
            error:function (){}
        });
    }
    function getPropertyOwnerDetailAreas(){
        var rid = $("#rid").val();
        var ownerdetailareaid = [];
        $('.podalist').each(function(){
            if($(this).is(':checked')){
                ownerdetailareaid.push($(this).val());
            }
        });
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getajaxpropertyownerdetailareasselection&rid='+rid+'&ownerdetailareaid='+ownerdetailareaid,
            type: "POST",
            success:function(data){
              console.log(data);
              $("#ownerpropertiesarea").html(data);
              calculateRent();
            },
            error:function (){}
        });
        console.log(ownerdetailareaid);
    }
    function calculateRent(id=""){
        if(id !=""){
            var property_area = $("#property_area_"+id).val();
            var per_sq_ft_rate = $("#per_sq_ft_rate_"+id).val();
            var per_sq_ft_discount = $("#per_sq_ft_discount_"+id).val();
            if(parseFloat(per_sq_ft_discount) > parseFloat(per_sq_ft_rate)){
               swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Discount cannot be more than "+per_sq_ft_rate+", Please Check", "error");
               $("#per_sq_ft_discount_"+id).val(per_sq_ft_rate);
               per_sq_ft_discount = per_sq_ft_rate;
            }
            if(per_sq_ft_discount !=""){
                var persqrate = parseFloat(per_sq_ft_rate) - parseFloat(per_sq_ft_discount);
            }else{
                var persqrate = per_sq_ft_rate;
            }
        }
        var totalrate = parseFloat(property_area)*parseFloat(persqrate);
        $("#rent_"+id).val(totalrate);

        var calcrent = 0;
        $('.calcrent').each(function(){
            if($(this).val() !=""){
                calcrent = parseFloat(calcrent) + parseFloat($(this).val());
            }
        });
        calcrent= calcrent.toFixed(2)
        $("#totalrent").text(calcrent);
        $("#monthly_rent").val(calcrent);

        setAggrementDetails();
    }
    function setAggrementDetails(){
        var lease_period_start_date = $("#lease_period_start_date").val();
        var lease_period_end_date = $("#lease_period_end_date").val();
        var property_owner_name= $("#property_owner_id option:selected").text();
        var tenant_name = $("#tenant_name").val();
        console.log(lease_period_start_date+" - "+lease_period_end_date+" -- "+property_owner_name);
        $("#property_owner_name_aggrement").text(property_owner_name);
        $("#tenant_name_aggrement").text(tenant_name);
        $("#lease_period_start_date_aggrement").text(lease_period_start_date);
        $("#lease_period_end_date_aggrement").text(lease_period_end_date);
        var totalrent = $("#monthly_rent").val();
        $("#rent_per_month_aggrement").text(totalrent);
        $("#rent_per_year_aggrement").text(parseFloat(totalrent)*12);
    }

    function rentFreePeriodDays(){
        var lease_period_start_date = $("#lease_period_start_date").val();
        var rent_free_period_day = $("#rent_free_period_day").val();
        rent_free_period_day = rent_free_period_day !=""?rent_free_period_day:0;
        jQuery.ajax({
            url: "ajax.php",
            data:'page=adddaystodate&date='+lease_period_start_date+'&numberofdays='+rent_free_period_day,
            type: "POST",
            success:function(data){
                $("#rent_commencement_date").val(data);
                setAggrementDetails();
                  var datearray = getDateMonthYear(data,'<?php echo $date_format ?>');
                  console.log(datearray);
                  console.log("scsd "+datearray.year);
                  console.log("mmm "+datearray['year']);
                  $('#rental_invoice_stoped_from').attr({
                    'min': datearray['year']+'-'+datearray['month'],
                    'max': '2024-12'
                    });
                  $('#rental_invoice_stoped_from').val(datearray['year']+'-'+datearray['month']);
            },
            error:function (){}
        });
  // Usage example:
    }
    function getDateMonthYear(dateString, format) {
            var day, month, year;
            if (format === "yy-mm-dd") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    year = parseInt(parts[0]);
                    month = parts[1]; // Months are zero-based
                    day = parseInt(parts[2]);
                }
            } else if (format === "dd-mm-yy") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    day = parseInt(parts[0]);
                    month = parts[1]; // Months are zero-based
                    year = parseInt(parts[2]);
                }
            } else if (format === "mm-dd-yy") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    month = parseInt(parts[0]); // Months are zero-based
                    day = parts[1];
                    year = parseInt(parts[2]);
                }
            } else {
                console.error("Unsupported date format:", format);
                return null;
            }
            if (isNaN(day) || isNaN(month) || isNaN(year)) {
                console.error("Unable to parse date string:", dateString);
                return null;
            }
            // Construct the Date object
               var dateary = [];
               dateary['year'] = year;
               dateary['month'] = month;
               dateary['day'] = day;
            return dateary;
        }

            function lockTenanatDetails(){
                var rid = $("#rid").val();
                swal({
                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    text: '<?php echo $_SESSION['error_message']['9034']; ?>',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: "ajax.php",
                            data:'page=insertrentalinvoices&rid='+rid,
                            type: "POST",
                            success:function(data){
                                console.log(data);
                                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message']['9079']; ?>", "success");
                                location.reload();
                            }
                        });
                    }
                });

            }
            function getCustomerId(custid){
                if(custid != 0 && custid !=""){
                    $("#insertfrmopen").css({"pointer-events":"none","opacity":"0.8"});

                    jQuery.ajax({
                      url: "ajax.php",
                      data:'page=getcustomerdetails&custid='+custid,
                      type: "POST",
                      success:function(data){
                        console.log(data);
                        var result = JSON.parse(data);
                        $("#company_name").val(result.company);
                        $("#tenant_name").val(result.display_name);
                        $("#tenant_email").val(result.email);
                        $("#tenant_mobile").val(result.phone);
                        $("#tenant_address").val(result.billing_address);
                        $("#tenant_pin_code").val(result.billing_post_code);
                       }
                    });
                }else{

                    $("#company_name").val('');
                    $("#tenant_name").val('');
                    $("#tenant_email").val('');
                    $("#tenant_mobile").val('');
                    $("#tenant_address").val('');
                    $("#tenant_pin_code").val('');
                    $("#insertfrmopen").css({"pointer-events":"visible","opacity":"1"});
                }

            }
    function getRentalBillType(utility_id,valu){
        console.log(utility_id+" - "+valu);
        if(valu == '0'){
            $("#divhideshow_"+utility_id).show();
        }else{
            $("#divhideshow_"+utility_id).hide();
        }
    }
    function getUtilityBillAutoAndFix(utility_id,valu){
        console.log(utility_id+" - "+valu);
        if(valu == 'A'){
            $("#utility_bill_amount_"+utility_id).attr("readonly", true);
            $("#utility_bill_amount_"+utility_id).show("slow");
            $("#utility_bill_manual_amount_"+utility_id).hide("slow");
        }else if(valu == 'F'){
            $("#utility_bill_amount_"+utility_id).attr("readonly", false);
            $("#utility_bill_amount_"+utility_id).show("slow");
            $("#utility_bill_manual_amount_"+utility_id).hide("slow");
        }else if(valu == 'M'){
            $("#utility_bill_manual_amount_"+utility_id).show("slow");
            $("#utility_bill_amount_"+utility_id).attr("readonly", true);
            $("#utility_bill_amount_"+utility_id).hide("slow");
        }
    }
    function addOpeningBalances(rid){
        var startdate = $('#rent_commencement_date').val();
        var start_dates = getDateMonthYear(startdate,'<?php echo $_SESSION['system_date_formate'] ?>');
        var start_date = new Date(start_dates['year'], start_dates['month']-1, start_dates['day']);

        var system_start_date = $('#system_start_date').val();
        var systestrmdate = getDateMonthYear(system_start_date,'<?php echo $_SESSION['system_date_formate'] ?>');
        var sys_start_date = new Date(systestrmdate['year'], systestrmdate['month']-1, systestrmdate['day']);

        var system_start_date_check =$('#system_start_date').val();
        var startDateObjectss = "";
        if(system_start_date_check !=""){
            var maxDate = start_date > sys_start_date ? start_date : sys_start_date;
            startDateObjectss = new Date(maxDate);
        }else{
            startDateObjectss = startDateObject;
        }
        jQuery.ajax({
            url: "views/custom_insert_tenant_opening_balances_popup.php",
            data:'rid='+rid,
            type: "POST",
            success:function(data){
                 $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: "<?php echo $_SESSION['config_date_formate'] ?>",
                    maxDate: startDateObjectss,
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                });
            },
            error:function (){}
        });
    }
    function unlockTenant(rid){
        var startdate = $('#rent_commencement_date').val(); // Assuming this gets the selected date from your date picker
        var startDateObject = new Date(startdate);
        jQuery.ajax({
            url: "views/custom_insert_unlock_tenant_popup.php",
            data:'rid='+rid,
            type: "POST",
            success:function(data){
                 $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: date_format,
                    maxDate: startDateObject,
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                });
            },
            error:function (){}
        });
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
function checkGSTAdded(valu){
    if(valu =="0"){
        $("#checkgstadded_hsn_code").show('slow');
    }else{
        $("#checkgstadded_hsn_code").hide('slow');
    }
}
function checkRentalInvoiceRaised(valu){
    if(valu =="N"){
        $("#checkrentalinvoiceraised_rental_stop_date").show('slow');
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message']['9128']; ?>", "info");
    }else{
        $("#checkrentalinvoiceraised_rental_stop_date").hide('slow');

    }
}

function childUtilityCheckBocChecked(ths,utid){
    console.log(ths);
    if ($(ths).is(':checked')) {
        $(".child_utility_class_"+utid).removeClass('event_disabled');
    }else{
        $(".child_utility_class_"+utid).addClass('event_disabled');
    }
}
function dueBillToBeRaiseType(valu){
    if(valu == "DY"){
        $("#due_bill_to_be_raise_dy").show();
        $("#due_bill_to_be_raise_dt").hide();
        $("#due_bill_to_be_raise_dy_text").attr("name", "due_bill_to_be_raise");
        $("#due_bill_to_be_raise_dt_select").attr("name", "due_bill_to_be_raise_hide");
    }else{
        $("#due_bill_to_be_raise_dy").hide();
        $("#due_bill_to_be_raise_dt").show();
        $("#due_bill_to_be_raise_dy_text").attr("name", "due_bill_to_be_raise_hide");
        $("#due_bill_to_be_raise_dt_select").attr("name", "due_bill_to_be_raise");
    }
}
function expireTenantAgreement(rid){
    var startdate = $('#rent_commencement_date').val(); // Assuming this gets the selected date from your date picker
        var startDateObject = new Date(startdate);
    jQuery.ajax({
            url: "views/custom_insert_expire_property_tenant_detail_popup.php",
            data:'rid='+rid,
            type: "POST",
            success:function(data){
                 $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: date_format,
                    maxDate: startDateObject,
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                });
            },
            error:function (){}
        });
}
function extendAgreement(){
    var transaction_id = '<?php echo $rid; ?>';
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#popupm').html(loader);
    $('#kt_modal_KTDatatable_remotem').modal("show");
    jQuery.ajax({
        url: "views/custom_insert_property_tenant_details_extend_agreement.php",
        data:'transaction_id='+transaction_id,
        type: "POST",
        success:function(data){
             $('#popupm').html(data);
            $('#kt_modal_KTDatatable_remotem').modal("show");
        },
        error:function (){}
    });
}
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<?php
$check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
if(in_array($property_tenant_details['current_status'], (array)$check_array) && $property_tenant_details['originator'] == $_SESSION['userid'] || $property_tenant_details['originator'] ==""){

}else{
    $dyncss = 'style="pointer-events: none; opacity: 0.6;"';
}
if($property_tenant_details['parent_id'] !="0" && $property_tenant_details['originator'] !=""){
    $un_lockdyncss = "style='pointer-events: none; opacity: 0.6;'";
}
?>
<div class="row">
    <div class="col-lg-8" >
        <form method="post" id="tenantdetailform">
        <div class="card">
        <?php
            if($property_tenant_details['id'] !=""){
                $workflowsteps = getWorkflowStatus('property_tenant_details',$property_tenant_details['id'],$property_tenant_details['current_status']);
            }
            echo $workflowsteps['current_status_ribbon'];
        ?>
            <div class="card-header">
                <h5>Property Tenant Details</h5>
                <div class="card-header-right table-card-header m-r-50">
                    <?php echo $workflowsteps['workflow_btn']; ?>


                </div>
            </div>
            <div class="card-body" <?php echo $dyncss; ?>>
                <?php echo $workflowsteps['breadcrumb']; ?>
                <div class="row">
                    <div class="form-group col-lg-6" <?php echo $un_lockdyncss; ?>>
                        <span class="form-label ">Property Owner Name <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="Property Owner" name="property_owner_id" id="property_owner_id" class="form-control form-select js-example-basic-single requried_field" onchange="getPropertyOwner(this.value)">
                            <option value="">Select Any One </option>
                            <?php foreach($property_owners as $coa) {  ?>
                                <option value="<?php echo $coa['id']; ?>" <?php echo $property_tenant_details['property_owner_id'] ==$coa['id']?"selected":""; ?>><?php echo $coa['owner_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6" <?php echo $un_lockdyncss; ?>>
                        <span class="form-label ">Property <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="Property Owner Detail" name="property_owner_detail_id" class="form-control form-select js-example-basic-single requried_field" id="property_owner_detail_id" onchange="getPropertyOwnerDetail(this.value)" >
                            <option value="">Select Any One </option>
                            <?php foreach($property_owner_details as $pod) {  ?>
                                <option value="<?php echo $pod['id']; ?>" <?php echo $property_tenant_details['property_owner_detail_id'] ==$pod['id']?"selected":""; ?>><?php echo $pod['property_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-12" <?php echo $un_lockdyncss; ?>>
                        <div class="row" id="ownerproperties">
                        <?php
                        $pownerareids =json_decode($property_tenant_details['property_owner_area_ids'],1);
                         foreach($property_owner_detail_areas as $poda){     ?>
                            <div class="col-lg-4"><div class="checkbox-fade fade-in-primary">
                              <label class="form-label">
                                <input aria-label="Property Owner Area Ids[]" type="checkbox" class="podalist" onchange="getPropertyOwnerDetailAreas()" name="property_owner_area_ids[]" value="<?php echo $poda['id']; ?>" <?php echo in_array($poda['id'],$pownerareids)?"checked":""; ?>>
                                <span class="cr">
                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                                <span style="display: table;"><?php echo $poda['property_area_name']; ?></span>
                              </label>
                        </div></div>
                       <?php  } ?>

                        </div>
                    </div>
                    <div class="form-group col-lg-12" <?php echo $un_lockdyncss; ?>>
                        <div id="ownerpropertiesarea">
                        <?php $podaajax = json_decode($property_tenant_details['property_owner_detail_areas'],1); ?>
                        <table class="table table-hover " style="width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Area Name</th>
                                    <th scope="col" class="text-right">Area</th>
                                    <th scope="col" class="">UOM</th>
                                    <th scope="col" class="text-right">Rate</th>
                                    <th scope="col" class="text-right">Discount</th>
                                    <th scope="col" class="text-right">Rent (Monthly)</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                        $totaldis = 0;
                        foreach($podaajax as $poda){
                             $detail = $property_owner_detail_areas[$poda['property_area_id']];
                            $totaldis +=$poda['rent'];
                        ?>
                            <tr>
                                <input type="hidden" name="property_area_id[]" value="<?php echo $poda['property_area_id']; ?>"><input type="hidden" name="property_area_name[]" value="<?php echo $detail['property_area_name']; ?>"><td class="p-2"><?php echo $detail['property_area_name']; ?> </td>

                                <td class="text-right p-2">
                                    <input type="hidden" class="form-control text-right" name="property_area[]" id="property_area_<?php echo $poda['property_area_id']; ?>" value="<?php echo $poda['property_area']; ?>" >
                                    <?php echo $detail['property_area']; ?>
                                </td>
                                <td class="p-2">
                                    <input type="hidden" class="form-control text-right" name="uom[]" value="<?php echo $detail['uom']; ?>" id="uom_<?php echo $poda['property_area_id']; ?>">
                                    <?php echo $poda['uom']; ?>
                                </td>
                                <td class="text-right p-2">
                                    <input type="hidden" class="form-control text-right" name="per_sq_ft_rate[]" value="<?php echo $detail['per_sq_ft_rate']; ?>" id="per_sq_ft_rate_<?php echo $poda['property_area_id']; ?>">
                                    <?php echo customeummarmonyformate($detail['per_sq_ft_rate']); ?>
                                </td>

                                <td class="p-2">
                                    <input aria-label="Per Sq Ft Discount[]" type="number" class="form-control text-right" name="per_sq_ft_discount[]" id="per_sq_ft_discount_<?php echo $poda['property_area_id']; ?>" value="<?php echo $poda['per_sq_ft_discount']; ?>" onblur="calculateRent('<?php echo $poda['property_area_id']; ?>')">
                                </td>

                                <td class="p-2"><input aria-label="Rent[]" type="text" class="form-control text-right calcrent " name="rent[]" value="<?php echo $poda['rent']; ?>" id="rent_<?php echo $poda['property_area_id']; ?>" readonly></td>
                            </tr>
                        <?php } ?>
                            </tbody></table>
                        </div>
                            <span class="float-end m-r-20">
                                <span class="text-warning">Total Monthly Rent : </span>
                                <span id="totalrent"><?php echo customeummarmonyformate($totaldis); ?></span>
                                <input type="hidden" name="monthly_rent" id="monthly_rent" value="<?php echo $totaldis; ?>">
                            </span>

                    </div>
                    <div class="form-group col-lg-12 mt-2" >
                        <span class="form-label">Lease Period <span class="text-danger">*</span></span>
                        <div class="row">
                            <div class="col-lg-5" <?php echo $un_lockdyncss; ?>>
                                <span class="d-block error-message text-danger"></span>
                                <input aria-label="Lease Period Start Date" type="text" class="form-control startdatepicker_1 rangedatepicker requried_field" data-id="1" name="lease_period_start_date" id="lease_period_start_date" value="<?php echo fromDate($property_tenant_details['lease_period_start_date']); ?>" onchange="rentFreePeriodDays()" <?php echo $property_tenant_details['un_lock_tenant_date'] !=""?"readonly":""; ?>>
                            </div>
                            <div class="col-lg-2 text-center">To </div>
                            <div class="col-lg-5">
                                <span class="d-block error-message text-danger"></span>
                                <input aria-label="Lease Period End Date" type="text" class="form-control enddatepicker_1 requried_field" name="lease_period_end_date"  id="lease_period_end_date" value="<?php echo fromDate($property_tenant_details['lease_period_end_date']); ?>" onchange="rentFreePeriodDays()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Rent Free Period</span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Rent Free Period Day" type="number" name="rent_free_period_day" id="rent_free_period_day" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['rent_free_period_day'] !=""?$property_tenant_details['rent_free_period_day']:0; ?>" onblur="rentFreePeriodDays()">
                            <span class="input-group-text addon-style">
                                <span class="">Days</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"></div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Rent Commencement Date <span class="text-danger">*</span></span>
                        <input aria-label="Rent Commencement Date" type="text" class="form-control " name="rent_commencement_date" id="rent_commencement_date" value="<?php echo fromDate($property_tenant_details['rent_commencement_date']); ?>" readonly>
                        <input type="hidden" class="form-control" id="system_start_date" value="<?php echo fromDate($_SESSION['config']['system_start_date']); ?>" readonly>
                        <input type="hidden" class="form-control" id="system_start_date_check" value="<?php echo $_SESSION['config']['system_start_date']; ?>" readonly>
                    </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Rent Escalation After </span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Rent Escalation After Month" type="number" name="rent_escalation_after_month" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['rent_escalation_after_month']; ?>">
                            <span class="input-group-text addon-style">
                                <span class="">Month</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Rent Escalation Percentage </span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Rent Escalation Percentage" type="number" name="rent_escalation_percentage" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['rent_escalation_percentage']; ?>">
                            <span class="input-group-text addon-style">
                                <span class=""> % </span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Notice Period </span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Notice Period" type="number" name="notice_period" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['notice_period']; ?>">
                            <span class="input-group-text addon-style">
                                <span class="">Month</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label ">Security Deposit</span>
                        <input aria-label="Security Deposit" type="number" class="form-control text-right " name="security_deposit" value="<?php echo $property_tenant_details['security_deposit']; ?>" >
                    </div>


                    <div class="form-group col-lg-5">
                        <span class="form-label">Locking Period Tenant</span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Locking Period Tenant" type="number" name="locking_period_tenant" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['locking_period_tenant']; ?>">
                            <span class="input-group-text addon-style">
                                <span class="">Month</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Locking Period Landlord</span>
                        <div class="input-group input-group-button ">
                            <input aria-label="Locking Period Landlord" type="number" name="locking_period_landlord" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['locking_period_landlord']; ?>">
                            <span class="input-group-text addon-style">
                                <span class="">Month</span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-5" >
                        <span class="form-label">Rent Bill To Be Raise <span class="text-danger">*</span></span>
                        <div class="row">
                            <div class="col-lg-6">
                                <span class="d-block error-message text-danger"></span>
                                <select aria-label="Rent Bill To Be Raise" name="rent_bill_to_be_raise" class="form-control form-select js-example-basic-single f-14 requried_field" >

                                    <?php for($r=1; $r<=31; $r++){ ?>
                                        <option value="<?php echo $r; ?>" <?php echo $r == $property_tenant_details['rent_bill_to_be_raise']?"selected":""; ?>><?php echo $r; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <span class="d-block error-message text-danger"></span>
                                <select aria-label="Rent Bill To Be Raise Type" name="rent_bill_to_be_raise_type" class="form-control form-select js-example-basic-single requried_field" >

                                    <?php foreach($rentbilltoberaise as $rbr) {  ?>
                                        <option value="<?php echo $rbr['genc_detail_code']; ?>" <?php echo $property_tenant_details['rent_bill_to_be_raise_type'] ==$rbr['genc_detail_code']?"selected":""; ?>><?php echo $rbr['genc_detail_name'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label">Due Bill To Be Raise <span class="text-danger">*</span></span>
                        <div class="row">
                            <div class="col-lg-6">
                                <select aria-label="Due Bill To Be Raise Type" name="due_bill_to_be_raise_type" class="form-control form-select js-example-basic-single" onchange="dueBillToBeRaiseType(this.value)">
                                    <option value="DY" <?php echo $property_tenant_details['due_bill_to_be_raise_type'] =='DY'?"selected":""; ?>>Days</option>
                                    <option value="DT" <?php echo $property_tenant_details['due_bill_to_be_raise_type'] =='DT'?"selected":""; ?>> Date</option>
                                </select>
                            </div>
                            <div class="col-lg-6" id="due_bill_to_be_raise_dy" <?php echo ($property_tenant_details['due_bill_to_be_raise_type'] =='DY' || $property_tenant_details['due_bill_to_be_raise_type'] =='')?'':'style="display: none"'; ?>>
                                <div class="input-group input-group-button ">
                                    <input aria-label="Enter number" type="number" name="due_bill_to_be_raise<?php echo ($property_tenant_details['due_bill_to_be_raise_type'] =='DY' || $property_tenant_details['due_bill_to_be_raise_type'] =='')?'':'_hide'; ?>" class="form-control form-control-sm width1 text-right" value="<?php echo $property_tenant_details['due_bill_to_be_raise']!=""?$property_tenant_details['due_bill_to_be_raise']:"0"; ?>" id="due_bill_to_be_raise_dy_text">
                                    <span class="input-group-text addon-style">
                                        <span class="">Days</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-6" id="due_bill_to_be_raise_dt" <?php echo $property_tenant_details['due_bill_to_be_raise_type'] =='DT'?'':'style="display: none"'; ?>>
                                <select aria-label="Select option" name="due_bill_to_be_raise<?php echo $property_tenant_details['due_bill_to_be_raise_type'] =='DT'?'':'_hide'; ?>" class="form-control form-select js-example-basic-single f-14" id="due_bill_to_be_raise_dt_select">
                                    <?php for($r=1; $r<=31; $r++){  ?>
                                        <option value="<?php echo $r; ?>" <?php echo $r == $property_tenant_details['due_bill_to_be_raise']?"selected":""; ?>><?php echo $r; ?></option>
                                    <?php } ?>
                                </select>
                            </div>


                        </div>
                    </div>
                    <div class="form-group col-lg-5">
                        <span class="form-label d-block">GST Added </span>
                        <div class="form-radio">
                            <div class="radio radio-inline" onclick="checkGSTAdded('0')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                              <label class="form-label">
                                <input aria-label="Gst Added" type="radio" name="gst_added" value="0" <?php echo $property_tenant_details['gst_added'] == '0'?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                            </div>
                            <div class="radio radio-inline" onclick="checkGSTAdded('')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                              <label class="form-label">
                                <input aria-label="Gst Added" type="radio" name="gst_added" value="" <?php echo $property_tenant_details['gst_added'] == ''?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group  col-lg-5" id="checkgstadded_hsn_code" <?php echo $property_tenant_details['gst_added'] == '0'?"":"style='display:none'"; ?>>
                        <span class="form-label d-block">GST HSN Code </span>
                        <select aria-label="Hsn And Sac Code" name="hsn_and_sac_code_id" class="form-control js-data-example-ajax-hsncodes" data-tablename="hsn_and_sac_codes" data-fieldsname="code_description"  data-fieldsname1="code" data-fieldsname2="code_description" data-fieldsname3="gst" data-category="id">
                            <option value="">Select Any One </option>
                            <?php foreach($hsn_and_sac_codes as $hsncodes){ ?>
                                <option value="<?php echo $hsncodes['id']; ?>" <?php echo $hsncodes['id'] == $property_tenant_details['hsn_and_sac_code_id']?"selected":""; ?>><?php echo $hsncodes['code']." - ".$hsncodes['code_description']."(".$hsncodes['gst'].")"; ?></option>
                            <?php } ?>
                        </select>
                    </div>


                    <div class="form-group col-lg-5" >

                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="form-group col-lg-5">

                    </div>

                    <div class="col-lg-12 mb-3"> </div>


                    <div class="form-group col-lg-5">
                        <span class="form-label d-block">Rental Invoice To Be Raised </span>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Rental Invoice To Be Raised" type="radio" name="rental_invoice_to_be_raised" value="Y" <?php echo ($property_tenant_details['rental_invoice_to_be_raised'] == '' || $property_tenant_details['rental_invoice_to_be_raised'] == 'Y')?"checked":""; ?> onclick="checkRentalInvoiceRaised('Y')" onkeydown="">
                                <i class="helper"></i>Yes
                              </label>
                            </div>
                            <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Rental Invoice To Be Raised" type="radio" name="rental_invoice_to_be_raised" value="N" <?php echo ($property_tenant_details['rental_invoice_to_be_raised'] == 'N')?"checked":""; ?> onclick="checkRentalInvoiceRaised('N')" onkeydown="">
                                <i class="helper"></i>No
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 text-center"> </div>
                    <div class="col-lg-5 " id="checkrentalinvoiceraised_rental_stop_date" <?php echo ($property_tenant_details['rental_invoice_to_be_raised'] == 'N')?"":"style='display:none'"; ?>>
                        <span class="form-label d-block">Rental Invoice Stoped From </span>
                        <input aria-label="Rental Invoice Stoped From" type="month" class="form-control" name="rental_invoice_stoped_from" value="<?php echo $property_tenant_details['rental_invoice_stoped_from'] !=""?$property_tenant_details['rental_invoice_stoped_from']:date('Y-m'); ?>" id="rental_invoice_stoped_from">
                    </div>
                    <div class="col-lg-12 mb-3"> </div>
                    <?php
                    $mc = 1;
                    $rbtype = json_decode($property_tenant_details['rental_bill_types'],1);
                    foreach($rental_bill_types as $rbt){
                        $rbtyp =$rbtype[$rbt['id']];
                        ?>
                        <div class="form-group col-lg-4">
                            <input type="hidden" name="rental_bill_type_name[<?php echo $rbt['id'];?>]" value="<?php echo $rbt['type_name']; ?>">
                            <span class="form-label d-block"><?php echo $rbt['type_name']; ?> <span class="text-danger">*</span></span>
                            <div class="form-radio">
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Radio option" type="radio" name="rental_bill_types[<?php echo $rbt['id'];?>]" value="0" <?php echo $rbtyp['status'] == '0'?"checked":""; ?> onchange="getRentalBillType(<?php echo $rbt['id'];?>,this.value)">
                                    <i class="helper"></i>Yes
                                  </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Radio option" type="radio" name="rental_bill_types[<?php echo $rbt['id'];?>]" value="" <?php echo $rbtyp['status'] == ''?"checked":""; ?> onchange="getRentalBillType(<?php echo $rbt['id'];?>,this.value)">
                                    <i class="helper"></i>No
                                  </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-8">
                            <div id="divhideshow_<?php echo $rbt['id'];?>" <?php echo $rbtyp['status'] == '0'?"":"style='display:none'"; ?>>
                                <div class="form-radio">
                                    <div class="radio radio-inline">
                                      <label class="form-label">
                                        <input aria-label="Radio option" type="radio" name="utility_bill_auto_and_fix[<?php echo $rbt['id'];?>]" value="A" <?php echo ($rbtyp['utility_bill_auto_and_fix'] == 'A' || $rbtyp['utility_bill_auto_and_fix'] == '')?"checked":""; ?> onchange="getUtilityBillAutoAndFix(<?php echo $rbt['id'];?>,this.value)">
                                        <i class="helper"></i>Actual
                                      </label>
                                    </div>
                                    <div class="radio radio-inline">
                                      <label class="form-label">
                                        <input aria-label="Radio option" type="radio" name="utility_bill_auto_and_fix[<?php echo $rbt['id'];?>]" value="F" <?php echo $rbtyp['utility_bill_auto_and_fix'] == 'F'?"checked":""; ?> onchange="getUtilityBillAutoAndFix(<?php echo $rbt['id'];?>,this.value)">
                                        <i class="helper"></i>Fixed
                                      </label>
                                    </div>
                                    <?php if(count((array)$rental_bill_sub_types[$rbt['id']]) != 0){ ?>
                                    <div class="radio radio-inline">
                                      <label class="form-label">
                                        <input aria-label="Radio option" type="radio" name="utility_bill_auto_and_fix[<?php echo $rbt['id'];?>]" value="M" <?php echo $rbtyp['utility_bill_auto_and_fix'] == 'M'?"checked":""; ?> onchange="getUtilityBillAutoAndFix(<?php echo $rbt['id'];?>,this.value)">
                                        <i class="helper"></i>Manual
                                      </label>
                                    </div>
                                    <?php } ?>
                                </div>
                                <table class="table table-hover m-b-0" id="utility_bill_manual_amount_<?php echo $rbt['id'];?>" <?php echo $rbtyp['utility_bill_auto_and_fix'] == 'M'?"":"style='display:none'"; ?>>
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%;"></th>
                                            <th scope="col" style="width: 40%;">Particulars</th>
                                            <th scope="col" class="text-right" style="width: 20%;">Qty</th>
                                            <th scope="col" class="text-right" style="width: 10%;">Opr</th>
                                            <th scope="col" class="text-right" style="width: 20%;">Rate</th>
                                            <th scope="col" style="width: 10%;">UOM</th>
                                        </tr>
                                    </thead>
                                    <?php foreach($rental_bill_sub_types[$rbt['id']] as $rudetail){ ?>
                                    <tr>
                                        <td class="p-2">
                                            <div class="checkbox-fade fade-in-primary m-0">
                                                <label class="form-label  m-0 w-b-w-s">
                                                    <input aria-label="Checkbox" type="checkbox" value="Y" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][check]" <?php echo $rbtyp['child_utility'][$rudetail['id']]['check'] =="Y"?"checked":""; ?> onchange="childUtilityCheckBocChecked(this,'<?php echo $rudetail['id']; ?>')">
                                                    <span class="cr  m-0"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                </label>
                                            </div>
                                            <input type="hidden" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][rental_bill_type_id]" class="form-control text-right" value="<?php echo $rudetail['id']; ?>">
                                        </td>
                                        <td class="p-2  w-b-w-s child_utility_class_<?php echo $rudetail['id']; ?> <?php echo $rbtyp['child_utility'][$rudetail['id']]['check'] =="Y"?"":"event_disabled"; ?>"><?php echo $rudetail['type_name']; ?><input type="hidden" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][rental_bill_type_name]" class="form-control text-right" value="<?php echo $rudetail['type_name']; ?>"></td>
                                        <td class="p-2 w-b-w-s child_utility_class_<?php echo $rudetail['id']; ?> <?php echo $rbtyp['child_utility'][$rudetail['id']]['check'] =="Y"?"":"event_disabled"; ?>"><input aria-label="Enter number" type="number" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][qty]" class="form-control text-right" value="<?php echo $rbtyp['child_utility'][$rudetail['id']]['qty']; ?>"></td>
                                        <td class="p-2 w-b-w-s child_utility_class_<?php echo $rudetail['id']; ?> <?php echo $rbtyp['child_utility'][$rudetail['id']]['check'] =="Y"?"":"event_disabled"; ?>">
                                            <select aria-label="Select option" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][condition]" class="form-control js-example-basic-single" >
                                                <option value="+" <?php echo $rbtyp['child_utility'][$rudetail['id']]['condition']=="+"?"selected":""; ?>>+</option>
                                                <option value="/" <?php echo $rbtyp['child_utility'][$rudetail['id']]['condition']=="/"?"selected":""; ?>>/</option>
                                                <option value="*" <?php echo $rbtyp['child_utility'][$rudetail['id']]['condition']=="*"?"selected":""; ?>>*</option>
                                                <option value="%" <?php echo $rbtyp['child_utility'][$rudetail['id']]['condition']=="%"?"selected":""; ?>>%</option>
                                                <option value="=" <?php echo $rbtyp['child_utility'][$rudetail['id']]['condition']=="="?"selected":""; ?>>=</option>
                                            </select>
                                        </td>
                                        <td class="p-2 child_utility_class_<?php echo $rudetail['id']; ?> <?php echo $rbtyp['child_utility'][$rudetail['id']]['check'] =="Y"?"":"event_disabled"; ?>"><input aria-label="Enter number" type="number" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][rate]" class="form-control text-right" value="<?php echo $rbtyp['child_utility'][$rudetail['id']]['rate']; ?>"></td>
                                        <td class="p-2">
                                            <select aria-label="Select option" name="utility_bill_manual_amount[<?php echo $rbt['id'];?>][<?php echo $rudetail['id'];?>][uom]" class="form-control js-example-basic-single" >

                                                <?php foreach($utilityuom as $uuomkey=>$uuomval){ ?>
                                                        <option value="<?php echo $uuomkey; ?>" <?php echo $rbtyp['child_utility'][$rudetail['id']]['uom']==$uuomkey?"selected":""; ?>><?php echo $uuomval; ?></option>
                                                <?php } ?>
                                                <option value="" <?php echo $rbtyp['child_utility'][$rudetail['id']]['uom']==""?"selected":""; ?>>NA</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <input aria-label="Enter number" type="number" name="utility_bill_amount[<?php echo $rbt['id'];?>]" class="form-control text-right" id="utility_bill_amount_<?php echo $rbt['id'];?>" value="<?php echo $rbtyp['utility_bill_amount']; ?>" <?php echo $rbtyp['utility_bill_auto_and_fix'] == 'F'?"":"readonly"; ?> <?php echo $rbtyp['utility_bill_auto_and_fix'] == 'M'?"style='display:none'":""; ?>>
                            </div>
                        </div>
                    <?php   } ?>
                </div>
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <h5>Property Tenant Details</h5>
                        </div>
                        <div class="card-body" <?php echo $un_lockdyncss; ?>>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <span class="form-label ">Tenant List <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <select aria-label="Customer" name="customer_id" id="customer_id" class="form-control form-select js-example-basic-single requried_field" onchange="getCustomerId(this.value)" >

                                        <?php foreach($customers as $cust) {  ?>
                                            <option value="<?php echo $cust['id']; ?>" <?php echo $property_tenant_details['customer_id'] ==$cust['id']?"selected":""; ?> <?php echo $_SESSION['userid']!=$cust['created_by']?"disabled":""; ?>><?php echo $cust['display_name'];?></option>
                                        <?php } ?>
                                        <option value="0" <?php echo $property_tenant_details['customer_id'] ==""?"selected":"";?>>New Tenant</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="insertfrmopen" <?php echo $property_tenant_details['customer_id'] !=""?'style="pointer-events:none;opacity:0.8"':'';?> >
                                <div class="form-group col-lg-6">
                                    <span class="form-label ">Company Name <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Company Name" type="text" class="form-control requried_field" name="company_name"  id="company_name" value="<?php echo $property_tenant_details['company_name']; ?>" >
                                </div>
                                <div class="form-group col-lg-6">
                                    <span class="form-label ">Tenant Name <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Tenant Name" type="text" class="form-control requried_field" name="tenant_name"  id="tenant_name" value="<?php echo $property_tenant_details['tenant_name']; ?>" >
                                </div>
                                <div class="form-group col-lg-6">
                                    <span class="form-label ">Email <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Tenant Email" type="text" class="form-control requried_field" name="tenant_email" id="tenant_email" value="<?php echo $property_tenant_details['tenant_email']; ?>" >
                                </div>
                                <div class="form-group col-lg-6">
                                    <span class="form-label ">Mobile <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Tenant Mobile" type="number" class="form-control requried_field" name="tenant_mobile" id="tenant_mobile" value="<?php echo $property_tenant_details['tenant_mobile']; ?>" >
                                </div>
                                <div class="form-group col-lg-8">
                                    <span class="form-label ">Address <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <textarea aria-label="Tenant Address" class="form-control requried_field" name="tenant_address" id="tenant_address"><?php echo $property_tenant_details['tenant_address']; ?></textarea>
                                </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label ">Pin Code <span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Tenant Pin Code" type="number" class="form-control requried_field" name="tenant_pin_code" id="tenant_pin_code" value="<?php echo $property_tenant_details['tenant_pin_code']; ?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <h5>Bill To Be Send To The Following</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <span class="form-label ">Name</span>
                                </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label ">Address</span>
                                </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label ">Details</span>
                                </div>

                            </div>
                            <div id="addmorerow">
                                <?php $i = 0;
                                $bill_to_be_send_for_the_following = json_decode($property_tenant_details['bill_to_be_send_for_the_following'],1);
                                if(count((array)$bill_to_be_send_for_the_following) == 0){
                                    $bill_to_be_send_for_the_following[] = array();
                                }
                                foreach($bill_to_be_send_for_the_following as $fa){
                                ?>
                                <div class="row" id="deleterowid_<?php echo $i; ?>">
                                    <div class="form-group col-lg-4  p-1">
                                        <input aria-label="Following Name[]" type="text" name="following_name[]" class="form-control form-control-sm" value="<?php echo $fa['following_name']; ?>" placeholder="Name">

                                    </div>
                                    <div class="form-group col-lg-4  p-1">
                                        <textarea aria-label="Following Address[]" class="form-control" name="following_address[]" style="height: 91px;"><?php echo $fa['following_address']; ?></textarea></div>
                                    <div class="form-group col-lg-4  p-1">
                                        <input aria-label="Following Email[]" type="text" class="form-control" name="following_email[]" value="<?php echo $fa['following_email']; ?>" placeholder="Email Id">
                                        <input aria-label="Following Mobile[]" type="number" class="form-control mt-2" name="following_mobile[]" value="<?php echo $fa['following_mobile']; ?>" placeholder="Contact Number">
                                    </div>
                                </div>
                                <?php $i++;
} ?>
                            </div>
                            <div class="">
                                 <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" onclick="addMoreFollowing()"><i class="icofont icofont-plus icon-white m-r-10"></i>Add More</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <h5>Note</h5>
                        </div>
                        <div class="card-body">
                            <textarea aria-label="Note" class="form-control textediters" name="note" id="<?php echo $key; ?>"><?php echo $property_tenant_details['note']; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer pt-0 mt-3">
                     <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                     <input type="hidden" name="submittenantdetails">
                     <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"  onclick="formSubmitTenantDetails()"> <?php echo $rid !=""?"Update":"Submit";?></button>

                </div>

            </div>
        </div>
    </form>
    </div>

    <div class="col-lg-4">
        <?php
            $aryseq = array();
            $billingdetails = $property_tenant_billings[0]['bill_detail'];
            $billingdetail = json_decode($billingdetails,1);
            foreach($billingdetail as $bd1){
                $aryseq[] = $bd1['name'];
            }
            $utilitydetails = $property_tenant_billings[0]['utility_details'];
            $utilitydetail = json_decode($utilitydetails,1);
            foreach($utilitydetail as $bd1){
                $aryseq[] = $bd1['name'];
            }
            $datearray = array();
            foreach($property_tenant_billings as $ptb){
                $aryval = array();
                $billingdetail = json_decode($ptb['bill_detail'],1);
                foreach($billingdetail as $bd1){
                    $aryval[] = $bd1;
                }
                $utilitydetails = json_decode($ptb['utility_details'],1);
                foreach($utilitydetails as $bd1){
                    $aryval[] = $bd1;
                }
                $datearray[$ptb['month_start_date']] = $aryval;
            }
        ?>
        <?php if(count((array)$property_tenant_billings) !=0){ ?>
        <div class="card">
            <div class="card-header">
                <h5>Rental Chart</h5>
                <div class="card-header-right table-card-header">
                    <?php
                    if($property_tenant_details['lock_tenant']=='0' && $property_tenant_details['originator']==$_SESSION['userid'] && $property_tenant_details['agreement_expiry_date']==''){ ?>
                        <button type="button" class="cursor-pointer float-left btn btn-info m-l-10" onclick="extendAgreement()"><i class="ti-reload
 text-white"></i> Extend Agreement</button>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body ">
                <div class="text-right">
                    <?php
                    if($property_tenant_details['lock_tenant']=='0' && $property_tenant_details['originator']==$_SESSION['userid'] && $property_tenant_details['agreement_expiry_date']==''){ ?>
                    <button type="button" class="btn btn-danger m-l-10" onclick="expireTenantAgreement('<?php echo $rid; ?>')"><i class="ti-unlock text-white"></i>  Expire Agreement</button>
                <?php }  ?>
                <?php
                     if($property_tenant_details['lock_tenant']=='0' && $property_tenant_details['originator']==$_SESSION['userid'] && $property_tenant_details['agreement_expiry_date']==''){ ?>
                    <button type="button" class="float-left btn btn-primary" onclick="addOpeningBalances('<?php echo $rid; ?>')"><i class="ti-plus text-white"></i> Opening Balance</button>
                <?php } ?>
                </div>
                <div clas="dt-responsive table-responsive" style="overflow: auto;">
                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                    <thead>
                        <tr>
                            <th scope="col">Bill</th>
                            <?php foreach($property_tenant_billings as $ptb){ ?>
                            <th scope="col" class="text-right"><?php echo fromDate($ptb['month_start_date']); ?></th>
                            <?php } ?>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalary = array();
                        foreach($aryseq as $keyid =>$bd1){ ?>
                            <tr>
                                <th scope="col" class="p-2">
                                    <?php echo $bd1; ?>
                                </th>
                                <?php foreach($datearray as $datekey=>$ptb){
                                    $billingdetails1 = $ptb[$keyid];
                                    $totalary[$datekey] += $billingdetails1['rent'];
                            ?>
                                <td class="text-right p-2"><?php echo customeummarmonyformateifine($billingdetails1['rent']); ?></td>
                                <?php }  ?>
                            </tr>
                                <?php } ?>
                        <tr>
                            <th scope="col" class="p-2">
                                Total
                            </th>
                            <?php foreach($datearray as $datekey=>$ptb){ ?>
                                <td class="p-2 text-right"><?php echo customeummarmonyformateifine($totalary[$datekey]!=""?$totalary[$datekey]:0); ?></td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    <?php } ?>

        <div class="card">
            <div class="card-header">
                <h5>Property Rental Agreement</h5>
            </div>
            <div class="card-body">

                <pre class="f-14">This agreement is between <span class="f-w-600 f-18" id="property_owner_name_aggrement"><?php echo $owners[$property_tenant_details['property_owner_id']]; ?></span> (landowner) and <span class="f-w-600 f-18" id="tenant_name_aggrement"><?php echo $property_tenant_details['tenant_name']; ?></span>,
(tenant), for the lease of certain parcels of land for the purpose of __________________
___________________________ [describe agricultural purpose(s) and operation].

1.The parcel(s) contained in this agreement are is/described as follows: [parcel location,
acreage, bounds, features, condition, etc.]

2. The term of this lease shall be from <span class="f-w-600 f-18" id="lease_period_start_date_aggrement"><?php echo $property_tenant_details['lease_period_start_date']; ?></span> to <span class="f-w-600 f-18" id="lease_period_end_date_aggrement"><?php echo $property_tenant_details['lease_period_end_date']; ?></span>
except as terminated earlier according to the provisions below.

3. The tenant agrees to pay a lease fee to the landowner of <span class="f-w-600 f-18" id="rent_per_month_aggrement"><?php echo round($totaldis*12); ?></span> per acre or
<span class="f-w-600 f-18" id="rent_per_year_aggrement"><?php echo round($totaldis); ?></span> total, per year. The tenant agrees to pay such sum at the beginning of the
lease term and on the anniversary thereof unless otherwise mutually agreed. A late
penalty of up to [ ]%/month may be assessed on all late payments. This lease fee may
be renegotiated annually.

4. Permitted Uses: The tenant is permitted all normal activities associated with the above
purposes, including but not limited to:
The tenant agrees to employ standard best management practices. It shall not be
considered a default of this Lease if weather or other circumstance prevents timely
practices or harvesting.

5. Prohibited Uses: The tenant shall not, unless by mutual agreement to the contrary,
engage in any of the following activities on said parcel(s):

6. The tenant agrees to prepare an annual management plan for review by the landlord,
complete annual soil testing, and apply amendments as indicated at his/her own
expense. The tenant agrees to proper disposal of trash and waste. The tenant further
agrees:

7. The [landowner/tenant] agrees to pay all taxes and assessments associated with this
parcel.

8. The farmer agrees to provide the landowner with evidence of liability insurance
coverage.

9. Either party may terminate this lease at any time with _____ month notice to the other
party. The tenant agrees not to assign or sublease his/her interest.

10. The terms of this lease may be amended by mutual consent.

11. A default in any of these provisions by either party may be cured upon written notice
by the other party within ____ days of receipt of such notice. Any disputes occurring
from this lease may be resolved by standard mediation practices, if necessary.

12. Landowner retains his/her right to access the parcel(s) for the purposes of inspection
with prior notification to the tenant.

13. Other special terms and conditions in this lease
</pre>
            </div>
        </div>
    </div>
</div>
