<?php
$_SESSION['apply_js']['multiselest'] = 1;

$isEdit = false;
$editData = [];
$dynamic_fields = [];
$checklist_items = [];
// handle insert/update
// echo '<pre>';print_r($_POST);echo '</pre>';
if (isset($_POST['submit']) && $_POST['submit'] != '') {
    $url = API . "?mode=insertamcregistrationdetails&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($response, 1);
    $result = $result['res'];
    if ($result['status'] == 'sucess') {
        ?>
        <script>
            window.addEventListener("load", function() {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX',
                    'Data Saved Successfully', '');
            });
        </script>
        <?php
        header("location:index.php?option=COM_LIST_AMC_REGISTRATION_DETAILS");
    }
    else {
        ?>
        <script>
        window.addEventListener("load", function(){
            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger',
                'animated flipInX','animated flipOutX','Operation failed','');
        });
        </script>
        <?php
    }
}
if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $isEdit = true;
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid)[1];

    $url = API . "?mode=getamcregistrationdetails&method=get&id=" . $rid;
    $res = json_decode(callAPI($url), true)?? [];
    // echo '<pre>';print_r($res);echo '</pre>';
    $editData = $res['amc_registration_details'] ?? [];
    $dynamic_fields = $res['dynamic_fields_details'] ?? [];
    $checklist_items = $res['checklist_items'] ?? [];
}

$url = API . "?mode=getallproductdetails";
$response = json_decode(callAPI($url), true);
$products = $response['pm_details'];
$customers = $response['cust_details'];
$ven_details = $response['ven_details'];
$ag_details = $response['ag_details'];
$gc_details = $response['gc_details'];
$buildings = $response['buildings'];

$payment_types = $services = [];
foreach ($gc_details as $row) {
    if ($row['genc_code'] == 'AMCPAYTYPE') $payment_types[] = $row;
    if ($row['genc_code'] == 'AMCSERVICE') $services[] = $row;
}


?>

<style>
    .form-label { font-weight: 500; }
    .card { border-radius: 10px; }
</style>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $isEdit ? 'Edit AMC Registration' : 'AMC Registration'; ?></h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="" method="POST">
                <?php if ($isEdit){ ?>
                    <input type="hidden" name="record_id" value="<?php echo $editData['id']; ?>">
                <?php }; ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">AMC Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amc_name" id="amc_name" placeholder="Enter AMC Name" value="<?php echo $isEdit?$editData['amc_name']:''; ?>" required>
                        </div>
                    </div>
                    <?php
                        $selectedType = 'customer';

                        if ($isEdit) {
                            if (!empty($editData['vendor_id'])) {
                                $selectedType = 'vendor';
                            } elseif (!empty($editData['customer_id'])) {
                                $selectedType = 'customer';
                            }
                        }
                        ?>
                    <!-- Type Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Type <span class="text-danger">*</span></label><br>

                            <label class="mr-3">
                                <input type="radio" name="party_type" value="customer"
                                    <?php echo $selectedType == 'customer' ? 'checked' : ''; ?>>
                                Customer
                            </label>

                            <label>
                                <input type="radio" name="party_type" value="vendor"
                                    <?php echo $selectedType == 'vendor' ? 'checked' : ''; ?>>
                                Vendor
                            </label>
                        </div>
                    </div>
                    
                </div>
                <div class="row g-2">
                    <!-- Product -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-control js-example-basic-single" required>
                                <option value="">Select Any One</option>
                                <?php foreach ($products as $p){ ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo $isEdit && $editData['product_id']==$p['id']?'selected':''; ?>>
                                        <?php echo htmlspecialchars($p['item_name']); ?>
                                    </option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Customer -->
                    <div class="col-md-3 party-box" id="customer_box"
                        style="<?php echo $selectedType == 'customer' ? '' : 'display:none;'; ?>">
                        <div class="form-group w-full">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customer_id"
                                class="form-control js-example-basic-single"
                                <?php echo $selectedType == 'customer' ? 'required' : ''; ?>>

                                <option value="">Select Any One</option>

                                <?php foreach ($customers as $c) { ?>
                                    <option value="<?php echo $c['id']; ?>"
                                        <?php echo $isEdit && $editData['customer_id'] == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo $c['display_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                <!-- Vendor -->
                <div class="col-md-3 party-box" id="vendor_box"
                    style="<?php echo $selectedType == 'vendor' ? '' : 'display:none;'; ?>">
                    <div class="form-group w-full">
                        <label class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id"
                            class="form-control js-example-basic-single"
                            <?php echo $selectedType == 'vendor' ? 'required' : ''; ?>>

                            <option value="">Select Any One</option>

                            <?php foreach ($ven_details as $c) { ?>
                                <option value="<?php echo $c['id']; ?>"
                                    <?php echo $isEdit && $editData['vendor_id'] == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo $c['display_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                    <!-- Activation & Deactivation Dates -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Activation Date <span class="text-danger">*</span></label>
                            <input type="date" name="activation_date" id="activationDate" class="form-control"
                                value="<?php echo $isEdit?$editData['activation_date']:''; ?>" onchange="setDeactivationDate()" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Deactivation Date</label>
                            <input type="date" name="deactivation_date" id="deactivationDate" class="form-control"
                                value="<?php echo $isEdit?$editData['deactivation_date']:''; ?>" readonly>
                        </div>
                    </div>

                    <!-- Payment Type -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Payment / Service Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-control js-example-basic-single" id="serviceType" onchange="toggleServiceCharge()" required>
                                <option value="">Select Any One</option>
                                <?php foreach ($payment_types as $pt){ ?>
                                    <option value="<?php echo $pt['genc_detail_code']; ?>" <?php echo $isEdit && $editData['payment_type']==$pt['genc_detail_code']?'selected':''; ?>>
                                        <?php echo $pt['genc_detail_name']; ?>
                                    </option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Service Charge -->
                    <div class="col-md-3" id="serviceChargeContainer" style="<?php echo ($isEdit && $editData['payment_type']=='2')?'':'display:none;'; ?>">
                        <div class="form-group">
                            <label class="form-label">Service Charge <span class="text-danger">*</span></label>
                            <input type="number" name="service_charge" id="serviceCharge" class="form-control"
                                   value="<?php echo $isEdit?$editData['service_charge']:''; ?>"
                                   <?php echo ($isEdit && $editData['payment_type']=='2')?'required':''; ?>>
                        </div>
                    </div>
                    <!-- Account Group -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Service  Name<span class="text-danger">*</span></label>
                            <select name="account_group_id" class="form-control js-example-basic-single" id="selectGroup" onchange="updateGroups(this)" required>
                                <option value="">Select Any One</option>
                                <?php foreach ($ag_details as $k=>$v){ ?>
                                    <option value="<?php echo $k; ?>" <?php echo $isEdit && $editData['account_group_id']==$k?'selected':''; ?>>
                                        <?php echo $v; ?>
                                    </option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Technician -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Service Company <span class="text-danger">*</span></label>
                            <select name="technician_id" class="form-control js-example-basic-single" id="selectTech" required>
                                <option value="">Select Any One</option>
                                <?php if ($isEdit && !empty($editData['technician_id'])){ ?>
                                    <option value="<?php echo $editData['technician_id']; ?>" selected><?php echo $editData['technician_name']; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Service On -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Service On <span class="text-danger">*</span></label>
                            <select name="service_on" id="serviceOn" class="form-control js-example-basic-single" onchange="resetDynamicFields()" required>
                                <option value="">Select Any One</option>
                                <?php foreach ($services as $s){ ?>
                                    <option value="<?php echo $s['genc_detail_code']; ?>" <?php echo $isEdit && $editData['service_on']==$s['genc_detail_code']?'selected':''; ?>>
                                        <?php echo $s['genc_detail_name']; ?>
                                    </option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                    <!-- No of Services -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">No of Services <span class="text-danger">*</span></label>
                            <select name="no_of_services" id="noOfService" class="form-control js-example-basic-single" onchange="generateServiceFields()" required>
                                <option value="">Select Any One</option>
                                <?php for ($i=1;$i<=10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php echo $isEdit && $editData['no_of_services']==$i?'selected':''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- Dynamic Fields -->
                    <div id="dynamicFieldsContainer" class="row  mt-1"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Building Name<span class="text-danger">*</span></label>
                                <select name="building_id" class="form-control js-example-basic-single" 
                                        id="selectBuilding" onchange="updateFloors(this)" required>
                                    <option value="">Select Any One</option>
                                    <?php foreach ($buildings as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>" 
                                            <?php echo $isEdit && $editData['building_id']==$row['id']?'selected':''; ?>>
                                            <?php echo $row['building_name']; ?>
                                        </option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Floor Dropdown -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Floor <span class="text-danger">*</span></label>
                                <select name="building_floor_id" class="form-control js-example-basic-single"
                                        id="selectFloor" onchange="updateFloorSpaces(this)" disabled required>
                                    <option value="">Select Any One</option>
                                </select>
                            </div>
                        </div>

                        <!-- Floor Spaces Dropdown -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Floor Space <span class="text-danger">*</span></label>
                                <select name="building_floor_space_id" class="form-control js-example-basic-single"
                                        id="selectFloorSpace" disabled required>
                                    <option value="">Select Any One</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Checklist Toggle -->
                
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label d-block">Is there Checklist?</label>
                            <div class="form-switch form-check form-check-inline">
                                <input class="form-check-input" name="check_list" type="checkbox" id="checklistToggle"
                                    <?php echo (!empty($checklist_items))?'checked':''; ?> onchange="toggleChecklistSection()">
                            </div>
                        </div>
                    
                        <!-- Checklist -->
                        <div class="col-md-12 mt-2" id="checklistSection" style="display:<?php echo !empty($checklist_items)?'block':'none'; ?>;">
                            <div id="checklistContainer" class="row g-2">
                                <?php if (!empty($checklist_items)){ foreach($checklist_items as $item_id => $item){ ?>
                                    <div class="col-md-12 d-flex align-items-start checklist-item">
                                        <div class="form-group flex-grow-1 me-2">
                                            <input type="hidden" name="existing_checklist_ids[]" value="<?php echo htmlspecialchars($item_id); ?>">
                                            <input type="text" name="checklist_items[]" class="form-control" value="<?php echo htmlspecialchars($item); ?>" required>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentNode.remove();">
                                            <i class="icofont icofont-trash"></i>
                                        </button>
                                    </div>
                                <?php } } ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addChecklistField()">
                                <i class="icofont icofont-plus"></i> Add More
                            </button>
                        </div>
                    </div>
                </div>
                </div>

                <div class="text-start mt-4">
                    <button type="submit" name="submit" class="btn btn-primary px-5" value="<?php echo $isEdit ? 'UPDATE' : 'SAVE'; ?>">
                        <i class="icofont icofont-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script>
$(document).ready(function () {

    function togglePartyFields() {
        var type = $('input[name="party_type"]:checked').val();

        if (type === 'customer') {
            $('#customer_box').show();
            $('#vendor_box').hide();

            $('#customer_id').prop('required', true);
            $('#vendor_id').prop('required', false).val('').trigger('change');
        } else {
            $('#vendor_box').show();
            $('#customer_box').hide();

            $('#vendor_id').prop('required', true);
            $('#customer_id').prop('required', false).val('').trigger('change');
        }
    }

    $('input[name="party_type"]').on('change', togglePartyFields);

    togglePartyFields();
});
</script>
<script>

function updateFloors(sel){
    const floorSel = $('#selectFloor');
    const spaceSel = $('#selectFloorSpace');
    floorSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);
    spaceSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);

    if(!sel.value) return;

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {page:'getselectedlist', id:sel.value, field:'building_id', table_name:'building_floors'},
        success: function(d){
            if (d) {
                JSON.parse(d).forEach(function(r){
                    floorSel.append('<option value="'+r.id+'">'+r.floor_name+'</option>');
                });
                floorSel.prop('disabled', false);

                // Handle pre-select in edit mode
                <?php if ($isEdit && !empty($editData['building_floor_id'])) { ?>
                    floorSel.val("<?php echo $editData['building_floor_id']; ?>").trigger('change');
                    updateFloorSpaces(document.getElementById('selectFloor'), true);
                <?php } ?>
            }
        }
    });
}

function updateFloorSpaces(sel, fromEdit = false){
    const spaceSel = $('#selectFloorSpace');
    spaceSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);

    if(!sel.value) return;

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {page:'getselectedlist', id:sel.value, field:'building_floor_id', table_name:'building_floor_spaces'},
        success: function(d){
            if (d) {
                JSON.parse(d).forEach(function(r){
                    spaceSel.append('<option value="'+r.id+'">'+r.cabin_name+'</option>');
                });
                spaceSel.prop('disabled', false);

                // Handle pre-select in edit mode
                <?php if ($isEdit && !empty($editData['building_floor_space_id'])) { ?>
                    spaceSel.val("<?php echo $editData['building_floor_space_id']; ?>");
                <?php } ?>
            }
        }
    });
}


function updateGroups(sel){
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {page:'getselectedlist', id:sel.value, field:sel.name, table_name:'amc_account_details'},
        success: function(d){
            const techSel = $('#selectTech').empty().append('<option value="">Select Any One</option>');
            if (d) {
                JSON.parse(d).forEach(function(r){
                    techSel.append('<option value="'+r.id+'">'+r.account_name+'</option>');
                });
            }
            <?php if ($isEdit && !empty($editData['technician_id'])) { ?>
                techSel.val("<?php echo $editData['technician_id']; ?>");
            <?php } ?>
        }
    });
}

function toggleServiceCharge(){
    const t = $('#serviceType').val();
    if(t==='2'){ $('#serviceChargeContainer').show(); $('#serviceCharge').prop('required',true);}
    else { $('#serviceChargeContainer').hide(); $('#serviceCharge').val('').prop('required',false);}
}

function setDeactivationDate(){
    const val=$('#activationDate').val();
    if(val){
        const d=new Date(val); d.setFullYear(d.getFullYear()+1);
        $('#deactivationDate').val(d.toISOString().split('T')[0]);
    }
}

function resetDynamicFields(){
    $('#dynamicFieldsContainer').empty();
    $('#noOfService').val("").trigger('change');
}

function generateServiceFields(){
    const serviceOn = $('#serviceOn').val();
    const count = parseInt($('#noOfService').val());
    const container = $('#dynamicFieldsContainer').empty();

    if(!count){
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger',
            'animated flipInX','animated flipOutX','Please select No. of Services','');
        return;
    }
    if(serviceOn==''){
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger',
            'animated flipInX','animated flipOutX','Please select Service On type first','');
        $('#noOfService').val("").trigger('change');
        return;
    }

    const saved = <?php echo json_encode($dynamic_fields); ?>;
    const savedMap = {};
    saved.forEach(f=>savedMap[f.service_key]=f.service_value);

    for(let i=1;i<=count;i++){
        let key='', html='';
        if(serviceOn==='1'){ key=`time_${i}`; html=`<input type="time" class="form-control" required name="dynamic_field[${key}]" value="${savedMap[key]||''}">`; }
        else if(serviceOn==='2'){
            key=`week_${i}`;
            html=`<select class="form-control js-example-basic-single" required  name="dynamic_field[${key}]">
                ${['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'].map(day=>`<option value="${day}" ${(savedMap[key]==day)?'selected':''}>${day}</option>`).join('')}
            </select>`;
        }else if(serviceOn==='3'){
            key=`month_${i}`;
            html=`<select class="form-control js-example-basic-single" required name="dynamic_field[${key}]">
                ${Array.from({length:30},(_,d)=>d+1).map(day=>`<option value="${day}" ${(savedMap[key]==day)?'selected':''}>Day ${day}</option>`).join('')}
            </select>`;
        }else if(serviceOn==='4'){
            key=`year_${i}`;
            html=`<input type="date" class="form-control" required name="dynamic_field[${key}]" value="${savedMap[key]||''}">`;
        }

        container.append(`<div class="col-md-12"><div class="form-group">
            <label class="form-label">Service ${i} <span class="text-danger">*</span></label>${html}</div></div>`);
    }
    $(".js-example-basic-single").select2();
}

function toggleChecklistSection(){
    if($('#checklistToggle').prop('checked')) $('#checklistSection').show();
    else $('#checklistSection').hide().find('#checklistContainer').empty();
}

function addChecklistField(value=''){
    $('#checklistContainer').append(`
        <div class="col-md-12 d-flex align-items-start checklist-item">
            <div class="form-group flex-grow-1 me-2">
                <input type="text" name="checklist_items[]" class="form-control" value="${value}" required>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentNode.remove();">
                <i class="icofont icofont-trash"></i>
            </button>
        </div>
    `);
}

$(document).ready(function(){
    $(".js-example-basic-single").select2();
    <?php if ($isEdit && !empty($editData['account_group_id'])){ ?>
        updateGroups(document.getElementById('selectGroup'));
    <?php }; ?>
    // auto generate dynamic fields on edit
    if('<?php echo $isEdit ? $editData['no_of_services'] : ''; ?>'){
        generateServiceFields();
    }

    <?php if ($isEdit && !empty($editData['building_id'])){ ?>
        // Auto-load floors and spaces on edit
        updateFloors(document.getElementById('selectBuilding'));
    <?php } ?>
});
</script>
