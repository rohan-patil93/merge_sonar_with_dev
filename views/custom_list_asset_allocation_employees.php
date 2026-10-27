<?php

if($_GET['EMP'] == ''){
    $url = API."?mode=getassetallocation&list=Y";
}else{
    $url = API."?mode=getassetallocation&list=Y&employeetype=Y&employee=".$_SESSION['emp_id'];
}
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$employees = $details['employees'];
$users = $details['users'];
$list_assets = $details['list_assets'];
$ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');
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
        alert("please select Minimum one assets");
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
        alert("please select Minimum one assets");
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
        alert("please select Minimum one assets");
        return false;
    }
    jQuery.ajax({
        url: "views/custom_insert_asset_return_popup.php",
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
        alert("please select Minimum one assets");
        return false;
    }
    jQuery.ajax({
        url: "views/custom_insert_multiple_asset_popup.php",
        data: "assetid="+assetid,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
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
        alert("please select Minimum one assets");
        return false;
    }
    jQuery.ajax({
        url: "views/custom_insert_asset_discard_sale_popup.php",
        data: "assetid="+assetid,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
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
        }
    });
}

function assetFilter(){

        var ownership = (document.getElementById('ownership'))? $('input[name="ownership"]:checked').val(): '';
        var allocation_status = (document.getElementById('allocation_status'))? $('input[name="allocation_status"]:checked').val(): '';
        var Parent_asset = (document.getElementById('Parent_asset'))? $('input[name="Parent_asset"]:checked').val(): '';

        var category = (document.getElementById('category'))? document.getElementById('category').value : '';
        var location = (document.getElementById('location'))? document.getElementById('location').value : '';
        var employee = (document.getElementById('employee'))? document.getElementById('employee').value : '';
        var status = (document.getElementById('status'))? document.getElementById('status').value : '';
        var candition = (document.getElementById('candition'))? document.getElementById('candition').value : '';
        var tabletype = (document.getElementById('tabletype'))? document.getElementById('tabletype').value : '';

        jQuery.ajax({
            url: "views/custom_list_asset_allocation_table.php",
            data: "ownership="+ownership+"&allocation_status="+allocation_status+"&Parent_asset="+Parent_asset+"&category="+category+"&location="+location+"&employee="+employee+"&status="+status+"&candition="+candition+'&tabletype='+tabletype,
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
    width: 315px;
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

                    <div class="card-header mb-3">
                        <h5> Assets Employee Allocation List</h5>
                        <div class="card-header-right">
                            <?php if(isset($_GET['Rid'])){ ?>
                                <a href="index.php?option=COM_VIEW_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-success"> <i class="fa fa-eye"></i> Priview</a>
                            <?php } ?>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
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
                    <div class="col-lg-2"><button  type="button" class="btn btn-primary waves-effect waves-light col-lg-12" onclick="requestAssets()">Requets</button></div>
                </div>

                <div class="row mt-4 assetstable">
                    <?php include_once "views/custom_list_asset_allocation_table.php";?>
                </div>
</div>

                </div>
            </div>
        </div>

