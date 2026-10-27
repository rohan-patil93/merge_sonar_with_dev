<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['asset_allocations_js']="1";
$userroleid = getUserRoles();
if(ASSET_USER_ROLE == $_SESSION['working_role_short_code']){// employeeee
    $url = API."?mode=getassetallocation&list=Y&employeetype=Y&asset_owner=".$_SESSION['userid']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
}else{
    $url = API."?mode=getassetallocation&list=Y&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
}
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$employees = $details['employees'];
$users = $details['users'];
$list_assets = $details['list_assets'];
$asset_status= $details['asset_status'];
$ass_condition = $details['asset_condition_type'];
$assetstatus =$assetstatusary =$assetconditionsary = array();
foreach($asset_status as $assstatus){
    $assetstatus[$assstatus['id']] = $assstatus['name'];
}
$assetlistcnt = array('self_cnt'=>'0','Partner_cnt'=>'0','allocated_cnt'=>'0','unallocated_cnt'=>'0','parent_asset_cnt'=>'0','not_parent_asset_cnt'=>'0');
foreach($list_assets as $listassets){
    $assetstatusary[$listassets['asset_status']]++;
    $assetconditionsary[$listassets['conditions']]++;
    $assetlistcnt['all_cnt']++;
    if($listassets['ownership'] == "S"){
        $assetlistcnt['self_cnt']++;
    }elseif($listassets['ownership'] == "P"){
        $assetlistcnt['Partner_cnt']++;
    }
    if($listassets['allocation_status'] == "Y"){
        $assetlistcnt['allocated_cnt']++;
    }else{
        $assetlistcnt['unallocated_cnt']++;
    }
    if($listassets['Parent_asset'] == "Y"){
        $assetlistcnt['parent_asset_cnt']++;
    }else{
        $assetlistcnt['not_parent_asset_cnt']++;
    }
}

foreach($details['asset_categories'] as $asset_category_list){
    $asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach($details['locations'] as $locations_list){
    $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
foreach($details['asset_status'] as $asset_status){
    $ass_status[$asset_status['id']] = $asset_status['name'];
}
$allocation_type = array('1'=>'Create Assets','2'=>'Update Assets','3'=>'Allocation','4'=>'Asset accept','5'=>'Asset Reject','6'=>'Return Assets','7'=>'Transefer');

$user_asset_cnt = $details['user_asset_cnt'];
$asset_cnt = $details['asset_cnt'];
$assetstatuscntwithname =$assetconditioncntwithname = array();
foreach($assetstatusary as $statuid=>$statuscnt){
    $assetstatuscntwithname[] = array('staus'=>$assetstatus[$statuid],'cnt'=>$statuscnt);
}
foreach($assetconditionsary as $conditionid=>$conditioncnt){
    $assetconditioncntwithname[] = array('condition'=>$ass_condition[$conditionid],'cnt'=>$conditioncnt);
}

?>
<script type="text/javascript">
    function AddAssets(asset_id=""){
        jQuery.ajax({
            url: "views/custom_insert_asset_popup.php",
            data: "asset_id="+asset_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                 $(".datepicker").datepicker({
                showOn: "both",
                buttonText: "Calendar", // placeholder text
                dateFormat: date_format,
                minDate: backdate_limit,
                setDate: new Date(),
                changeMonth: true,
                changeYear: true
            }).next("button.ui-datepicker-trigger")
              .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
    //                 showOn: "both",
    //                 buttonText: "Calendar",
    //                 dateFormat: date_format,
    //                 yearRange: "-60:+10",
    //                 changeMonth: true,
    //                 changeYear: true,
            },
            error:function (){}
        });
    }
    function alloteAssets(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_insert_asset_allocation_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }
    function momentAssets(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_view_asset_moment_history_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }
    function returnAssets(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_insert_asset_return_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }
    function updateMultipleAssets(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_insert_multiple_asset_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }
    function assetDiscrdOrSell(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_insert_asset_discard_sale_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }
    function verifyAssets(){
        var assetid = [];
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                assetid.push($(this).val());
            }
        });
        if (assetid.length === 0) {
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "<?php echo $_SESSION['error_message']['1009'];?>",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }
        jQuery.ajax({
            url: "views/custom_insert_asset_verifications_popup.php",
            data: "assetid="+assetid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }

    function requestAssets(){
        jQuery.ajax({
            url: "views/custom_insert_asset_requests.php",
            data: "assetid=assetid",
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }

    function assetaccept(assetid,id){
        jQuery.ajax({
            url: "views/custom_list_asset_accept_popup.php",
            data: "assetid="+assetid+"&id="+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }

    function extraassetattributesreqquest(assetid,id,cat_id,attribute){
        jQuery.ajax({
            url: "views/custom_list_asset_accept_popup.php",
            data: "assetid="+assetid+"&id="+id+"&extra=Y&cat_id="+cat_id+"&attribute="+attribute,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                 var rect = input.getBoundingClientRect();
                 setTimeout(function () {
                 inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                 }, 0);
                 }
                });
            }
        });
    }

    function assetFilter(){
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('.assetstable').html(htmloader);

        var ownership = (document.getElementById('ownership'))? $('input[name="ownership"]:checked').val(): '';
        var allocation_status = (document.getElementById('allocation_status'))? $('input[name="allocation_status"]:checked').val(): '';
        var Parent_asset = (document.getElementById('Parent_asset'))? $('input[name="Parent_asset"]:checked').val(): '';

        var category = (document.getElementById('category'))? document.getElementById('category').value : '';
        var location = (document.getElementById('location'))? document.getElementById('location').value : '';
        var asset_owner = (document.getElementById('asset_owner'))? document.getElementById('asset_owner').value : '';
        var status = (document.getElementById('status'))? document.getElementById('status').value : '';
        var candition = (document.getElementById('candition'))? document.getElementById('candition').value : '';
        var tabletype = (document.getElementById('tabletype'))? document.getElementById('tabletype').value : '';

        jQuery.ajax({
            url: "views/custom_list_asset_allocation_table.php",
            data: "ownership="+ownership+"&allocation_status="+allocation_status+"&Parent_asset="+Parent_asset+"&category="+category+"&location="+location+"&asset_owner="+asset_owner+"&status="+status+"&candition="+candition+'&tabletype='+tabletype+'&userid=<?php echo $_SESSION['userid']; ?>',
            type: "POST",
            success:function(data){
                $('.assetstable').html(data);
            },
            error:function (){}
        });
    }

</script>
<style type="text/css">
    .selector {
        display: flex;
        height: 36px;
        width: 100%;
        align-items: center;
        justify-content: center;
    }
    .selector .selection {
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        height: 100%;
        width: 100%;
        margin-left: -1px;
        background-clip: border-box;
        transform: scale3d(1, 1, 1);

        border: 1px solid #485769;
        color: #000000;
        transition: 0.15s ease-in-out background-color;
        position: relative;
    }
    .selector .selection:first-child {
        margin-left: 0;
        border-radius: 3px 0 0 3px;
    }
    .selector .selection:hover {
        color: #2196f3;
        background: rgba(91, 150, 213, 0.05);
    }
    .selector .selection:last-child {
        border-radius: 0 3px 3px 0;
    }
    .selector .selection.selected {
        background: rgba(91, 150, 213, 0.2) !important;
        border-color: #5b96d5;
        color: #2196f3 !important;
        z-index: 2;
        cursor: text;
    }
    .selection input[type="radio"]{    width: 100%; height: 36px; opacity: 0 !important; position: absolute; cursor: pointer !important;}

</style>
<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<script type="text/javascript">
    function selectallcheck(){
        if($('#selectall_checks').is(':checked')){
            $('.checkboxassets').each(function(){
                this.checked = true;
            });
        }else{
            $('.checkboxassets').each(function(){
                this.checked = false;
            });
        }
        selectcheck();
    }
    function selectcheck(){
        var cnt = 0;
        $('.checkboxassets').each(function(){
            if($(this).is(':checked')){
                cnt++;
            }
        });
        $("#astcnt").text(cnt);
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <?php if(in_array(ASSET_USER_ROLE,(array)$_SESSION['role_ids'])){ ?>
            <div class="card-header mb-3">
                <h5>Assets Employee Allocation List</h5>
                <div class="card-header-right">
                    <?php if(isset($_GET['Rid'])){ ?>
                        <a href="index.php?option=COM_VIEW_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-success"> <i class="fa fa-eye"></i> Priview</a>
                    <?php } ?>
                </div>
            </div>
            <input type="hidden" id="employee"  value="<?php echo $_SESSION['emp_id']; ?>">
            <input type="hidden" id="tabletype"  value="E">
            <div class="card-body">
                <div class="row ml-20 mr-20">
                    <div class="form-group col-lg-4">
                        <span class="col-lg-12">Category</span>
                        <select aria-label="Category"  class="form-select js-example-basic-single " name="category" id="category" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($assetcategory as $assetcategor) { ?>
                                <option value="<?php echo $assetcategor['id']; ?>"><?php echo ucwords($assetcategor['category_description']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-4">
                        <span class="col-lg-12">Status</span>
                        <select aria-label="Status"  class="form-select js-example-basic-single " name="status" id="status" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>
                                <option  value="<?php echo $ass_statukey; ?>"><?php echo ucwords($ass_statu); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="col-lg-12">Condition</span>
                        <select aria-label="Candition"  class="form-select js-example-basic-single " name="candition" id="candition" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>
                                <option  value="<?php echo $ass_conditiokey; ?>"><?php echo ucwords($ass_conditio); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row ml-20 mr-20">
                    <div class="col-lg-2"><button type="button" class="btn btn-primary waves-effect waves-light col-lg-12" onclick="returnAssets()">Assets Return</button></div>
                    <div class="col-lg-2"><button type="button" class="btn btn-danger waves-effect waves-light col-lg-12" onclick="momentAssets()"><i class="fa fa-file"></i> History</button></div>
                    <div class="col-lg-2"><button  type="button" class="btn btn-primary waves-effect waves-light col-lg-12" onclick="requestAssets()">Request</button></div>
                </div>

                <div class="row mt-4 assetstable">
                    <?php
                    if(file_exists("views/custom_list_asset_allocation_table.php")){
                        include_once "views/custom_list_asset_allocation_table.php";
                    }else{
                        include_once "custom_list_asset_allocation_table.php";
                    }

                    ?>
                </div>
            </div>

            <?php }else{ ?> <!-- ADMIN -->

            <input type="hidden" id="tabletype_2"  value="A">
            <div class="card-header mb-3">
                <h5>Assets Allocation List</h5>
                <div class="card-header-right">
                    <?php if(isset($_GET['Rid'])){ ?>
                        <a href="index.php?option=COM_VIEW_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-success"> <i class="fa fa-eye"></i> Priview</a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 vertical-justify mb-tab-20">
                        <div class="card ticket-card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $user_asset_cnt['overduecnt']; ?></h2>
                                    <p class="m-b-0 m-t-5">Asset Overdue</p>
                                </div>
                            </div>
                        </div>
                        <div class="card ticket-card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $user_asset_cnt['duesooncnt']; ?></h2>
                                    <p class="m-b-0 m-t-5">Asset Due Soon</p>
                                </div>
                            </div>
                        </div>
                        <div class="card ticket-card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['warranty_expiry_cnt']; ?></h2>
                                     <p class="m-b-0 m-t-5">Warrenty Expiry Soon</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-tab-20">
                        <div class="card mb-0">
                            <div class="card-header">
                                <h5>Assets Status</h5>
                            </div>
                            <div class="card-block">
                                <input type="hidden" id="asset_status_json" value='<?php echo json_encode($assetstatuscntwithname); ?>'>
                                <div id="asset_status_chart" class="demo-placeholder" style="height:350px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-mob-20">
                        <div class="card mb-0">
                            <div class="card-header">
                                <h5>Assets Condition</h5>
                            </div>
                            <div class="card-block">
                                <input type="hidden" id="asset_condition_json" value='<?php echo json_encode($assetconditioncntwithname); ?>'>
                                <div id="asset_condition_chart" style="width: 100%; height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-12 vertical-justify">
                        <div class="card ticket-card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['end_of_life']; ?></h2>
                                    <p class="m-b-0 m-t-5">End Of Life</p>
                                </div>
                            </div>
                        </div>
                        <div class="card ticket-card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['unallocatedcnt']; ?></h2>
                                    <p class="m-b-0 m-t-5">Asset Un-Allocat</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary waves-effect waves-light col-lg-12 f-12" onclick="AddAssets()"><i class="fa fa-plus"></i> Add Assets
                        </button>
                        <button class="btn btn-success waves-effect waves-light col-lg-12 f-12">
                            <i class="fa fa-line-chart"></i> Inventory
                        </button>
                        <button class="btn btn-info waves-effect waves-light col-lg-12 f-12"><i class="ti-info-alt"></i> Help Desk</button>
                        <a class="btn btn-warning waves-effect waves-light col-lg-12 f-12" href="index?option=COM_LIST_MY_REPORTS" target="_blank">
                            <i class="fa fa-line-chart"></i> Reports
                        </a>
                    </div>
                </div>
                <div class="form-group row ml-20 mr-20">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-3"></div>
                    <div class="col-lg-3"></div>
                    <div class="col-lg-3"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <div class="selector">
                            <div class="selection selected">
                                <input aria-label="Ownership" type="radio" name="ownership" id="ownership" value="" checked onclick="assetFilter()" onkeydown="">
                                <label for="radio_1">All &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['all_cnt']; ?></span></label>
                            </div>
                            <div class="selection">
                                <input aria-label="Ownership" type="radio" name="ownership" value="S"  onclick="assetFilter()" onkeydown="">
                                <label for="radio_2">Self &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['self_cnt']; ?></span></label>
                            </div>
                            <div class="selection ">
                                <input aria-label="Ownership" type="radio" name="ownership"  value="P" onclick="assetFilter()" onkeydown="">
                                <span >Partner &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['Partner_cnt']; ?></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <div class="selector">
                            <div class="selection selected">
                                <input aria-label="Allocation Status" type="radio" name="allocation_status" id="allocation_status" value="" checked onclick="assetFilter()" onkeydown="">
                                <span >All &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['all_cnt']; ?></span></span>
                            </div>
                            <div class="selection">
                                <input aria-label="Allocation Status" type="radio" name="allocation_status" value="Y" onclick="assetFilter()" onkeydown="">
                                <span >Allocated &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['allocated_cnt']; ?></span></span>
                            </div>
                            <div class="selection ">
                                <input aria-label="Allocation Status" type="radio" name="allocation_status" value="N" onclick="assetFilter()" onkeydown="">
                                <span >Un-Alloted &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['unallocated_cnt']; ?></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <div class="selector">
                            <div class="selection selected">
                                <input aria-label="Parent Asset" type="radio" name="Parent_asset"  id="Parent_asset" value="" checked onclick="assetFilter()" onkeydown="">
                                <span >All &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['all_cnt']; ?></span></span>
                            </div>
                            <div class="selection">
                                <input aria-label="Parent Asset" type="radio" name="Parent_asset" value="N" onclick="assetFilter()" onkeydown="">
                                <span >Linked  &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['parent_asset_cnt']; ?></span></span>
                            </div>
                            <div class="selection">
                                <input aria-label="Parent Asset" type="radio" name="Parent_asset" value="Y" onclick="assetFilter()" onkeydown="">
                                <span >Un Linked  &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['not_parent_asset_cnt']; ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ml-20 mr-20 mb-3">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <span class="w-100">Category</span>
                        <select aria-label="Category"  class="form-select js-example-basic-single w-full" name="category" id="category_2" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($assetcategory as $assetcategor) { ?>
                                <option value="<?php echo $assetcategor['id']; ?>"><?php echo ucwords($assetcategor['category_description']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <span class="col-lg-12">Assets location</span>
                        <select aria-label="Location"  class="form-select js-example-basic-single w-full" name="location" id="location" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($locations as $location) { ?>
                                <option location value="<?php echo $location['id']; ?>"><?php echo ucwords($location['location_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                            <span class="col-lg-12">User</span>
                            <select aria-label="Asset Owner"  class="form-select js-example-basic-single w-full" name="asset_owner"  id="asset_owner"  onchange="assetFilter()">
                                <option value="">Select Any One</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo ucwords($user['display_name']); ?></option>
                        <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <span class="col-lg-12">Status</span>
                        <select aria-label="Status"  class="form-select js-example-basic-single w-full" name="status" id="status_2" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>
                                <option  value="<?php echo $ass_statukey; ?>"><?php echo ucwords($ass_statu); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                        <span class="col-lg-12">Condition</span>
                        <select aria-label="Candition"  class="form-select js-example-basic-single w-full" name="candition" id="candition_2" onchange="assetFilter()">
                            <option value="">Select Any One</option>
                            <?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>
                                <option  value="<?php echo $ass_conditiokey; ?>"><?php echo ucwords($ass_conditio); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-1 mob-d-flex ml-20 mr-20 mb-3">
                    <button type="button" class="btn btn-success waves-effect waves-light form-group" onclick="alloteAssets()">Allot Assets</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light form-group" onclick="returnAssets()">Assets Return</button>
                    <button type="button" class="btn btn-warning waves-effect waves-light form-group" onclick="assetDiscrdOrSell()"><i class="fa fa-frown-o"></i> Discard</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light form-group" onclick="momentAssets()"><i class="fa fa-file"></i> History</button>
                    <button  type="button" class="btn btn-inverse waves-effect waves-light form-group" onclick="updateMultipleAssets()">Update</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light form-group" onclick="requestAssets()">Request</button>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="d-flex gap-1 mob-d-flex">
                            <button type="button" class="btn btn-primary waves-effect waves-light form-group" onclick="verifyAssets()">Verify</button>
                            <a href="index?option=COM_VIEW_ASSET_VERIFICATIONS" type="button" class="btn btn-primary waves-effect waves-light form-group" target="_blank">Verification List</a>
                        </div>
                    </div>
                </div>
                <span class="form-label label label-warning mt-5">
                    <span id="astcnt" class="f-18">0</span> - Asset Selected
                </span>

                <div class="row mt-4 assetstable">
                    <?php
                    if(file_exists("views/custom_list_asset_allocation_table.php")){
                        include_once "views/custom_list_asset_allocation_table.php";
                    }else{
                        include_once "custom_list_asset_allocation_table.php";
                    }

                    ?>
                </div>
            </div>

        <?php } ?>

    </div>
</div>
</div>
<?php if($_POST['ajaxpage']=='Y'){ ?>
    <script src="assets/pages/chart/float/jquery.flot.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
    <script src="assets/pages/chart/float/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="assets/js/custom_list_asset_allocations.js"></script>
<?php } ?>
