<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['jobposting'] = '1';
$table_name = "item_quality_parameters_mapping";
if (isset($_POST['submit'])){
    $data['company_id'] = $_SESSION['company_id'] ?? '';
    $data['item_id'] = $_POST['item_id'] ?? '';
    $data['item_cat_id'] = $_POST['item_cat_id'] ?? '';
    $data['process'] = $_POST['process'] ?? '';
    $data['from_date'] = $_POST['from_date'] ?? '';
    $data['to_date'] = $_POST['to_date'] ?? '';
    $jsqpm['group'] = isset($_POST['cat']) && is_array($_POST['cat']) ? array_keys($_POST['cat']) : array();
    $jsp = array();
    if(isset($_POST['desc']) && is_array($_POST['desc'])){
        foreach($_POST['desc'] as $jskey=>$jspid){
            $jsp[$jskey]['id'] = $jskey; 
            $jsp[$jskey]['mand'] = $_POST['mand'][$jskey] ?? '';
            $jsp[$jskey]['res'] = $_POST['descpass'][$jskey] ?? '';
        }
    }
    $jsqpm['parameters'] = $jsp;
    $data['specifications'] = json_encode($jsqpm, 1);
    if($_POST['submit'] == 'Add'){
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'] ?? '';
        $url = API."?mode=adddata&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data, 'insert');
    }
    if($_POST['submit'] == 'Update'){    
        $_SESSION['check'] = 1;
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'] ?? '';
        $url = API."?mode=updaterowbasepimarydata&rid=".($_POST['hid'] ?? '')."&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data, 'update');
    }
    $response = json_decode($response, 1);
    if(isset($response['status']) && $response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other'] ?? '');
            $dis_er = array();
            foreach ($dis_err as $va) {
                if(isset($dis_labels[$va])){
                    $dis_er[] = $dis_labels[$va];
                }
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", $dis_er);
        }else{
            $error_message = $response['message'] ?? 'Operation failed';
        }
    }else{
        header("location:index.php?option=COM_LIST_ITEM_QUALITY_PARAMETERS_MAPPING");
        exit;
    }
}
$dispay = array('GRN'=>'Goods Receipt Notes');
$process = array(
    'all' => 'All',
    'goods_receipt_notes' => 'GRN',
    'grn_returns' => 'GRN Returns',
    'delivery_orders' => 'Delivery Orders',
    'sales_returns' => 'Sales Returns',
    'rgp_delivery_orders' => 'RGP Delivery Orders',
    'rgp_sales_returns' => 'RGP Sales Returns',
    'manufacturing_work_orders' => 'Manufacturing Work Orders',
    'manufacturing_product_notes' => 'Manufacturing Product Notes',
    'subcontract_work_order_receipts' => 'Subcontract Work Order Receipts'
);
$vurl = API."?mode=togetqualitychecmappingindetails"; 
$vdata_details = callAPI($vurl);
$detail = json_decode($vdata_details, 1);
$qcparameters = $detail['parameter'] ?? array();

$role = array();
$role_cat = array();
$role_des = array();
$rid = "";
$status_res = "0";

if((isset($_GET['Rid']) && $_GET['Rid'] !='') || (isset($_GET['ITEMID']) && $_GET['ITEMID']!="")){ 
    if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
        $rid = encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $url = API."?mode=togetdatabasepaimairy&rid=".$rid."&table=".$table_name;
        $response = callAPI($url);
        $role = json_decode($response, 1);
    }
    if(isset($_GET['ITEMID']) && $_GET['ITEMID']!=""){
        $vdata1["where"] = array(
            'item_id' => $_GET['ITEMID'],
            'is_active' => '0',
            'item_cat_id' => $_GET['CATID'] ?? '',
            'process' => $_GET['PROCESS'] ?? ''
        );
        $vdata = urlencode(json_encode($vdata1));
        $url = API."?mode=gettabledetailswithconditions&table=".$table_name."&data=".$vdata; 
        $response = callAPI($url);
        $roles = json_decode($response, 1);
        $role = $roles['0'] ?? array();
        if(empty($role)){
            $role['item_id'] = $_GET['ITEMID'];
            $role['item_cat_id'] = $_GET['CATID'] ?? '';
            $role['process'] = $_GET['PROCESS'] ?? '';
        }
    }
    
    if(!empty($rid)){ $rid = "id:".($role['id'] ?? ''); }
    $jsdata = json_decode($role['specifications'] ?? '{}', 1);
    $role_cat = $jsdata['group'] ?? array();
    if(isset($jsdata['parameters']) && is_array($jsdata['parameters'])){
        foreach($jsdata['parameters'] as $jdata){
            if(isset($jdata['id'])){
                $role_des[] = $jdata['id'];
            }
        }
    }
}
?>
<link rel="stylesheet" href="assets/css/custom_insert_item_quality_parameters_mapping.css">
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <?php if(isset($error_message) && $error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>    
    <?php } ?>
    <div class="card-header">
        <h5>Quality Parameters Mapping</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light js-back-btn">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-custom">
        <div class="card-body">
            <form method="post" action="">
                <div class="row p-4">
                    <div class="form-group col-lg-4">
                        <span class="form-label">Item Categories</span>        
                        <div class="w-full">
                            <select aria-label="Item Categories" class="form-select js-example-basic-single" name="item_cat_id" id="item_cat_id" data-selected-item="<?php echo $role['item_id'] ?? '0'; ?>">
                                <option <?php echo (isset($role['item_cat_id']) && $role['item_cat_id']=='0')?'selected':''; ?> value="0">All</option>
                                <?php if(isset($detail['item_categories']) && is_array($detail['item_categories'])){ ?>
                                    <?php foreach($detail['item_categories'] as $item){ ?>
                                        <option <?php echo (isset($role['item_cat_id']) && $item['id']==$role['item_cat_id'])?'selected':''; ?> value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['category_name'], ENT_QUOTES); ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Process</span>        
                        <div class="w-full">
                            <select aria-label="Process" class="form-select js-example-basic-single" name="process" id="process">
                                <?php foreach($process as $key => $value){ ?>
                                    <option <?php echo (isset($role['process']) && $key==$role['process'])?'selected':''; ?> value="<?php echo $key; ?>"><?php echo htmlspecialchars($value, ENT_QUOTES); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Item</span>        
                        <div class="w-full">
                            <select aria-label="Item" class="form-select js-example-basic-single item-select" name="item_id" id="item_id">
                                <option value="0">All</option>
                            </select>
                        </div>
                    </div>
                    <?php
                    $from_date = !empty($role['from_date'])
                        ? date('Y-m-d', strtotime($role['from_date']))
                        : date('Y-m-d');

                    $to_date = !empty($role['to_date'])
                        ? date('Y-m-d', strtotime($role['to_date']))
                        : date('Y-m-d', strtotime('+5 years'));
                    ?>
                    <div class="form-group col-lg-4">
                        <span class="form-label">From Date</span>    
                        <input aria-label="From Date" type="date" class="form-control" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <span class="form-label">To Date</span>    
                        <input aria-label="To Date" type="date" class="form-control" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
                    </div>
                    <div class="col-lg-12 row">
                        <div class="col-lg-7 f-w-600"> Quality Parameters</div>
                        <div class="col-lg-3 f-w-600">
                            Reference Interval with Quality 
                        </div>
                        <div class="col-lg-2 f-w-600">
                            Mandatory
                        </div>
                    </div>
                    <?php if(isset($detail['groups']) && is_array($detail['groups'])){ ?>
                        <?php foreach ($detail['groups'] as $group){
                            if(isset($qcparameters[$group['id']]) && count((array)$qcparameters[$group['id']]) > 0){
                                ?>
                                <div class="col-lg-12 row">
                                    <div class="col-lg-7 mt-3">
                                        <div class="checkbox-fade fade-in-primary">    
                                            <label class="form-label line-height24">
                                                <input type="checkbox" <?php if(in_array($group['id'],(array)$role_cat)){ echo "checked"; } ?> name="cat[<?php echo $group['id']; ?>]" id="cat_<?php echo $group['id']; ?>" 
                                                class="job-category-checkbox"
                                                data-catid="<?php echo $group['id']; ?>" 
                                                aria-label="<?php echo htmlspecialchars($group['group_name'], ENT_QUOTES); ?>" />
                                                <span class="cr float-left">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <?php echo htmlspecialchars($group['group_name'], ENT_QUOTES); ?>
                                            </label>
                                        </div> 
                                    </div>
                                    <div class="col-lg-5"></div>
                                </div>
                                <div id="cate_<?php echo $group['id']; ?>" class="<?php if(!in_array($group['id'],(array)$role_cat)){ echo "d-none"; } ?>">
                                    <?php foreach ($qcparameters[$group['id']] as $par) { ?>
                                        <div class="col-lg-12 row ml-10 mt-2">
                                            <div class="col-lg-7">
                                                <div class="checkbox-fade fade-in-primary mt-2 p-l-20">
                                                    <label class="form-label text-auto-hight line-height24">
                                                        <input type="checkbox" <?php if(in_array($par['id'],(array)$role_des)){ echo "checked"; } ?> name="desc[<?php echo $par['id']; ?>]" id="par_<?php echo $par['id']; ?>" aria-label="<?php echo htmlspecialchars($par['name'], ENT_QUOTES); ?>" />
                                                        <span class="cr float-left">
                                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                        </span>
                                                        <?php echo htmlspecialchars($par['name'], ENT_QUOTES); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <?php 
                                                if($par['display_type']==1 || $par['display_type']==2){ 
                                                    $selectdata = json_decode($par['quality_parameters'] ?? '[]', 1);
                                                    $check = (isset($jsdata['parameters'][$par['id']]['res']) && $jsdata['parameters'][$par['id']]['res']!="") ? $jsdata['parameters'][$par['id']]['res'] : 0;
                                                ?>
                                                <div class="toggle">
                                                    <?php $mn=1; foreach((array)$selectdata as $selectkey => $selectvalue){
                                                        $id = "check_".$par['id']."_".$mn;
                                                    ?> 
                                                    <input aria-label="<?php echo htmlspecialchars($selectvalue, ENT_QUOTES); ?>" type="radio" <?php if($check==$selectkey){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
                                                    <label for="<?php echo $id; ?>"><?php echo htmlspecialchars($selectvalue, ENT_QUOTES); ?></label>
                                                    <?php $mn++; } ?>
                                                </div>
                                                <?php } ?>
                                                <?php if($par['display_type']==3 || $par['display_type']==4){
                                                    $selectdata = json_decode($par['quality_parameters'] ?? '[]', 1);
                                                    $check = (isset($jsdata['parameters'][$par['id']]['res']) && $jsdata['parameters'][$par['id']]['res']!="") ? $jsdata['parameters'][$par['id']]['res'] : 3;
                                                ?>
                                                <div class="toggle">
                                                    <?php $mn=1; for($i=1;$i<=5;$i++){     
                                                        $id = "check_".$par['id']."_".$mn;
                                                    ?> 
                                                    <input aria-label="Rating <?php echo $i; ?>" type="radio" <?php if($check==$i){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $i; ?>' id='<?php echo $id; ?>' />
                                                    <label for="<?php echo $id; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo htmlspecialchars($selectdata[$i-1] ?? '', ENT_QUOTES); ?>"><?php echo $i; ?></label>
                                                    <?php $mn++; } ?>
                                                </div>
                                                <?php } ?>
                                                <?php if($par['display_type']==5){ 
                                                    $selectdata = array('Yes','No');
                                                    $check = (isset($jsdata['parameters'][$par['id']]['res']) && $jsdata['parameters'][$par['id']]['res']!="") ? $jsdata['parameters'][$par['id']]['res'] : 0;
                                                ?>
                                                <div class="toggle">
                                                    <?php $mn=1; foreach($selectdata as $selectkey => $selectvalue){
                                                        $id = "check_".$par['id']."_".$mn;
                                                    ?> 
                                                    <input aria-label="<?php echo $selectvalue; ?>" type="radio" <?php if($check ==$selectkey){ echo "checked"; } ?> name="descpass[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
                                                    <label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
                                                    <?php $mn++; } ?>
                                                </div>
                                                <div class="f-12 text-muted"> With in range ?</div>
                                                <?php } ?>
                                            </div>
                                            
                                            <div class="col-lg-2">
                                                <div class="toggle">
                                                    <?php $selectdata = array('Yes','No'); $mn=1;
                                                     foreach($selectdata as $selectkey => $selectvalue){
                                                        $id = "mand_".$par['id']."_".$mn;
                                                        $mcheck = (isset($jsdata['parameters'][$par['id']]['mand']) && $jsdata['parameters'][$par['id']]['mand']!="") ? $jsdata['parameters'][$par['id']]['mand'] : 1;
                                                        ?> 
                                                        <input aria-label="<?php echo $selectvalue; ?>" type="radio" <?php if($mcheck==$selectkey){ echo "checked"; } ?> name="mand[<?php echo $par['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' />
                                                        <label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
                                                    <?php $mn++; } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } } ?>
                    <?php } ?>
                    <div class="card-footer mt-5 p-4">
                        <div class="row">
                            <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && (!isset($_GET['copy']) || $_GET['copy'] != '1')){ ?>
                                <div class="col-lg-9 col-md-6 col-sm-6 col-6">
                                    <input type="hidden" name="hid" value="<?php echo isset($rid) ? $rid : ''; ?>" />
                                    <input type="hidden" name="submit" value="Update" />
                                    <button id="kt_llogin_signup_submit" <?php if(isset($status_res) && $status_res == '1'){ ?> disabled="disabled" <?php } ?> class="btn btn-primary mr-2"><i class="la la-check"></i>Update</button>
                                </div>
                            <?php } else{ ?>
                                <div class="col-lg-9">
                                    <input type="hidden" name="submit" value="Add" />
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value_exit" value="exit"><i class="la la-check"></i>Save & exit</button>
                                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2"><i class="la la-close"></i>Cancel</button>
                                </div>
                            <?php } ?>    
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6 text-right">
                                <button type="button" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/custom_insert_item_quality_parameters_mapping.js"></script>
