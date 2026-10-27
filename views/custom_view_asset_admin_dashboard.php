<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['asset_allocations_js']="1";
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=ADMINDASH";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$application_users = $details['application_users'];
$asset_status= $details['asset_status'];
$ass_condition = $details['asset_condition_type'];
$assets = $details['assets'];
$assets_graph = $details['assets_graph'];
$user_asset_cnt = $details['user_asset_cnt'];
$asset_cnt = $details['asset_cnt'];
$assetlistcnt = array('self_cnt'=>'0','Partner_cnt'=>'0','allocated_cnt'=>'0','unallocated_cnt'=>'0','parent_asset_cnt'=>'0','not_parent_asset_cnt'=>'0');
foreach($assets_graph as $ag){
    $assetstatusary[$ag['asset_status']]++;
    $assetconditionsary[$ag['conditions']]++;
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
    if($listassets['parent_asset'] == "Y"){
        $assetlistcnt['parent_asset_cnt']++;
    }else{
        $assetlistcnt['not_parent_asset_cnt']++;
    }
}
$assetstatuscntwithname =$assetconditioncntwithname = array();
foreach($assetstatusary as $statuid=>$statuscnt){
    $assetstatuscntwithname[] = array('staus'=>$asset_status[$statuid]['name'],'cnt'=>$statuscnt);
}
foreach($assetconditionsary as $conditionid=>$conditioncnt){
    $assetconditioncntwithname[] = array('condition'=>$ass_condition[$conditionid]['condition_name'],'cnt'=>$conditioncnt);
}

?>
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
function assetFilter(){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('.assetstable').html(htmloader);
    var ownership = (document.getElementById('ownership'))? $('input[name="ownership"]:checked').val(): '';
    var allocation_status = (document.getElementById('allocation_status'))? $('input[name="allocation_status"]:checked').val(): '';
    var parent_asset = (document.getElementById('parent_asset'))? $('input[name="parent_asset"]:checked').val(): '';
    var category = (document.getElementById('category'))? document.getElementById('category').value : '';
    var location = (document.getElementById('location'))? document.getElementById('location').value : '';
    var asset_owner = (document.getElementById('asset_owner'))? document.getElementById('asset_owner').value : '';
    var status = (document.getElementById('status'))? document.getElementById('status').value : '';
    var candition = (document.getElementById('candition'))? document.getElementById('candition').value : '';
    jQuery.ajax({
        url: "views/custom_view_asset_admin_dashboard_ajax.php",
        data: "ajaxpagecall=Y&resultgettype=ADMINDASH&allocation_status="+allocation_status+"&parent_asset="+parent_asset+"&category="+category+"&location="+location+"&asset_owner="+asset_owner+"&status="+status+"&candition="+candition+"&ownership="+ownership,
        type: "POST",
        success:function(data){
            $('.assetstable').html(data);
        },
        error:function (){}
    });
}
function AddAssets(asset_id=""){
        jQuery.ajax({
            url: "views/custom_insert_asset_popup.php",
            data: "asset_id="+asset_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "calendar",
                    dateFormat: date_format,
                    yearRange: "-60:+10",
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                     var rect = input.getBoundingClientRect();
                     setTimeout(function () {
                     inst.dpDiv.css({ top: rect.top + 10, left: rect.left + 0 });
                     }, 0);
                     }
                    }).next("button.ui-datepicker-trigger")
              .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
            },
            error:function (){}
        });
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

    //extra functions


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

</script>
<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<div class="row mb-4">
    <div class="col-lg-2 col-md-6 col-sm-6 col-12 vertical-justify mb-tab-20">
        <div class="card ticket-card mb-0">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $user_asset_cnt['overduecnt'] !=""?$user_asset_cnt['overduecnt']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Asset Overdue</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card mb-0">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $user_asset_cnt['duesooncnt'] !=""?$user_asset_cnt['duesooncnt']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Asset Due Soon</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card mb-0">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['warranty_expiry_cnt']!=""?$asset_cnt['warranty_expiry_cnt']:0; ?></h2>
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
                <div id="asset_status_chart" class="demo-placeholder" style="height:300px;"></div>
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
                <div id="asset_condition_chart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-6 col-sm-6 col-12 vertical-justify">
        <div class="card ticket-card mb-0">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['end_of_life'] !=""?$asset_cnt['end_of_life']:0; ?></h2>
                    <p class="m-b-0 m-t-5">End Of Life</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card mb-0">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $asset_cnt['unallocatedcnt']!=""?$asset_cnt['unallocatedcnt']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Asset Un-Allocat</p>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary waves-effect waves-light col-lg-12 f-12" onclick="AddAssets()"><i class="fa fa-plus"></i> Add Assets</button>
        <button  type="button" class="btn btn-success waves-effect waves-light col-lg-12 f-12" onclick="verifyAssets()"><i class="fa fa-plus"></i> Verify Assets</button>

        <a href="index?option=COM_VIEW_ASSET_VERIFICATIONS" type="button" class="btn btn-primary waves-effect waves-light col-lg-12 f-12" target="_blank"> <i class="ti-link"></i> Verification List</a>
    </div>
</div>
<div class="text-right">
     <button type="button" class="btn btn-success waves-effect waves-light btn-sm mb-mob-10" onclick="alloteAssets()">Allot Assets</button>

    <button type="button" class="btn btn-warning waves-effect waves-light btn-sm mb-mob-10" onclick="assetDiscrdOrSell()"><i class="fa fa-frown-o"></i> Discard</button>
    <button type="button" class="btn btn-danger waves-effect waves-light btn-sm mb-mob-10" onclick="momentAssets()"><i class="fa fa-file"></i> History</button>
    <button  type="button" class="btn btn-inverse waves-effect waves-light btn-sm mb-mob-10" onclick="updateMultipleAssets()">Update</button>


</div>
<div class="row mt-4">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="form-group col-lg-6 w-full">
                        <span class="form-label">Category</span>
                        <select aria-label="Category"  class="form-select js-example-basic-single " name="category" id="category" onchange="assetFilter()">
                            <option value="">All</option>
                            <?php foreach ($assetcategory as $assetcategor) { ?>
                                <option value="<?php echo $assetcategor['id']; ?>"><?php echo ucwords($assetcategor['category_description']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6 w-full">
                        <span class="form-label">Assets location</span>
                        <select aria-label="Location"  class="form-select js-example-basic-single " name="location" id="location" onchange="assetFilter()">
                            <option value="">All</option>
                            <?php foreach ($locations as $location) { ?>
                                <option location value="<?php echo $location['id']; ?>"><?php echo ucwords($location['location_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6 w-full">
                        <span class="form-label">Status</span>
                        <select aria-label="Status"  class="form-select js-example-basic-single " name="status" id="status" onchange="assetFilter()">
                            <option value="">All</option>
                            <?php foreach ($asset_status as $ass_statu) { ?>
                                <option value="<?php echo $ass_statu['id']; ?>"><?php echo ucwords($ass_statu['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6 w-full">
                        <span class="form-label">Condition</span>
                        <select aria-label="Candition"  class="form-select js-example-basic-single " name="candition" id="candition" onchange="assetFilter()">
                            <option value="">All</option>
                            <?php foreach ($ass_condition as $ass_conditio) { ?>
                            <option  value="<?php echo $ass_conditio['id']; ?>"><?php echo ucwords($ass_conditio['condition_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-12 w-full">
                        <span class="form-label">User</span>
                        <select aria-label="Asset Owner"  class="form-select js-example-basic-single " name="asset_owner"  id="asset_owner"  onchange="assetFilter()">
                        <option value="">All</option>
                        <?php foreach ($application_users as $user) { ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo ucwords($user['display_name']); ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="selector f-12">
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
                    <div class="col-lg-12 mt-3">
                        <div class="selector  f-12">
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
                                <span >Un-Alloc &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['unallocated_cnt']; ?></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <div class="selector  f-12">
                            <div class="selection selected">
                                <input aria-label="Parent Asset" type="radio" name="parent_asset"  id="parent_asset" value="" checked onclick="assetFilter()" onkeydown="">
                                <span >All &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['all_cnt']; ?></span></span>
                            </div>
                            <div class="selection">
                                <input aria-label="Parent Asset" type="radio" name="parent_asset" value="N" onclick="assetFilter()" onkeydown="">
                                <span >Linked  &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['parent_asset_cnt']; ?></span></span>
                            </div>
                            <div class="selection">
                                <input aria-label="Parent Asset" type="radio" name="parent_asset" value="Y" onclick="assetFilter()" onkeydown="">
                                <span >Un Linked  &nbsp;<span class="form-label label label-primary label-sm"><?php echo $assetlistcnt['not_parent_asset_cnt']; ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 assetstable">
        <?php
            if(file_exists("views/custom_view_asset_admin_dashboard_ajax.php")){
                include_once "views/custom_view_asset_admin_dashboard_ajax.php";
            }else{
                include_once "custom_view_asset_admin_dashboard_ajax.php";
            }
        ?>
    </div>
</div>

