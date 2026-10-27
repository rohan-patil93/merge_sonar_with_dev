<?php
$_SESSION['apply_js']['multiselest'] = 1;
$rid = '';
$url = API."?mode=togetitemrelateddetails&rid=".$rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);
//echo $url;
if(isset($_GET['option'])){
    $table_name='items';
    
    $url = API."?mode=togetdatabasepaimairy&rid=".$_GET['Rid']."&table=".$table_name;
    
    $response = callAPI($url);
    $data = json_decode($response,1);
}
?>


<div class="row">
    <div class="col-lg-12">
        <div class="card 1 ">
            <div class="card-header table-card-header">
                <h5> Items Form</h5><i class="ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Go Tour" aria-label="Go Tour"></i>
                <div class="card-header-right table-card-header ">

                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>

                </div>
            </div>
            <!--begin::Portlet-->
            <div class="card-infisuite">
                <div class="card-body">
                    <ul class="nav md-tabs tabs infisuite_tab  mt-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#items" role="tab">Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#price" role="tab">Price</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#inventory" role="tab">Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tax" role="tab">Tax</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#account" role="tab">Account</a>
                        </li>
                    </ul>
                    <form class="item_form" id="item_form" method="POST" action="views/itemform_submit.php" name="item_form">
                        <div class="tab-content mt-4">
                            <div class="tab-pane active" id="items" role="tabpanel">
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label class="">SKU&nbsp;<span class="requried">*</span></label>
                                        <input class="form-control" type="text" id="sku" name="sku" value="<?php echo $data['sku'] ?>">
                                        <div style="display: none;color: red;" id="sku_err"> Please enter SKU </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="">Ref SKU</label>
                                        <input class="form-control" type="text" id="ref_sku" name="ref_sku" value="<?php echo $data['ref_sku'] ?>">
                                        <div style="display: none;color: red;" id="ref_sku_err"> Please enter Ref Sku </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="">Item Name&nbsp;<span class="requried">*</span></label>
                                        <input class="form-control" type="text" id="item_name" name="item_name" value="<?php echo $data['item_name'] ?>">
                                        <div style="display: none;color: red;" id="item_name_err"> Please enter Item Name </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label">Category</label>
                                        <select class="form-select border js-example-basic-single" id="category_id" name="category_id">

                                            <option value="0">Select Any one Category</option>

                                            <?php foreach ($result['category'] as $category) { ?>
                                                <option value="<?php echo $category['id']; ?>" <?php if(isset($data['category_id'])){ if($data['category_id']==$category['id']){ echo 'selected=selected'; }}?>"><?php echo $category['category_description'] ?></option>;
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="category_id_err"> Please Select Category </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label">Item Type</label>
                                        <select class="form-select border js-example-basic-single" id="item_type" name="item_type">
                                            <option value="0"> Select Any one Item Type</option>
                                            <?php foreach ($result['item_types'] as $item_type) { ?>
                                                <option value="<?php $item_type['id']; ?>" <?php if(isset($data['item_type'])){ if($data['item_type']==$item_type['id']){echo "selected=selected";}} ?>"><?php echo $item_type['item_type_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="item_type_err"> Please Select Item Type </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label">Costing Type</label>
                                        <select class="form-select border js-example-basic-single" id="default_costing_type" name="default_costing_type">
                                        <option value="0"> Select Any one Costing Type</option>
                                        <?php foreach ($result['costing_type'] as $ctype) { ?>
                                                <option value="<?php $ctype['genc_detail_code']; ?>" <?php if(isset($data['default_costing_type'])){ if($data['default_costing_type']==$ctype['genc_detail_code']){echo "selected=selected";}} ?>"><?php echo $ctype['genc_detail_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="default_costing_type_err"> Please Select Costing Type </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label d-block">Switch</label>
                                        <input type="checkbox" class="js-single" checked name="sold_individually" value="<?php echo $data['sold_individually'] ?>" />
                                        <input type="hidden" name="sold_individually" value="">
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label d-block">Sold In Groups
                                        </label>
                                        <input type="checkbox" class="js-switch" checked name="sold_in_groups" value="<?php echo $data['sold_in_groups'] ?>" />
                                        <input type="hidden" name="sold_in_groups" value="1">
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="form-label d-block">Quality Check
                                        </label>
                                        <input type="checkbox" class="js-switch" checked name="quality_check" value="<?php echo $data['quality_check'] ?>" />
                                        <input type="hidden" name="quality_check" value="1">
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="form-label">Item Description</label>
                                        <textarea class="form-control" rows="5" cols="5" id="item_description" name="item_description"><?php echo $data['item_description'] ?></textarea>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="">Specifications</label>
                                        <input class="form-control" type="text" id="specifications" name="specifications" value="<?php echo $data['specifications']; ?>">
                                        <div style="display: none;color: red;" id="specifications_err"> Please enter Specifications </div>
                                    </div>


                                    <div class="form-group col-lg-4">
                                        <label class="form-label d-block">Item Image</label>
                                        <div class="profile-pic">
                                            <label for="item_image">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image" id="item_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)">
                                            <input type="hidden" name="hiditem_image" value="">
                                            <img src="uploads/all/no-image.jpg" class="img-radius" width="200" id="outputitem_image">
                                        </div>
                                    </div>


                                    <div class="form-group col-lg-4">
                                        <label class="form-label d-block">Item Image2</label>
                                        <div class="profile-pic">
                                            <label for="item_image2">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image2" id="item_image2" accept=".png, .jpg, .jpeg" onchange="">
                                            <input type="hidden" name="hiditem_image2" value="">
                                            <img src="uploads/all/no-image.jpg" class="img-radius" width="200" id="outputitem_image2">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label>Item Image3</label>
                                        <div class="profile-pic">
                                            <label for="item_image3">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image3" id="item_image3" accept=".png, .jpg, .jpeg" onchange="">
                                            <input type="hidden" name="hiditem_image3" value="">
                                            <img src="uploads/all/no-image.jpg" class="img-radius" width="200" id="outputitem_image2">
                                        </div>
                                    </div>



                                </div>
                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">

                                        </div>
                                        <div class="col-lg-3 text-right">
                                            <button type="button" class="btn btn-primary btn_mobile" onclick="if($('#sku').val() == '' || $('#item_name').val() == '') { return ValidateForm(); } else { $('[href=\'#price\']').tab('show'); }"><i class="la la-next"></i>Next</button>

                                            <!-- <a class="btn btn-primary" href="#price" role="tab">Next</a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="price" role="tabpanel">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label class="form-label">Purchase UoM</label>
                                        <select class="form-select border js-example-basic-single" id="purchase_uom" name="purchase_uom">
                                            <option value="0">Select Any one Purchase UoM</option>
                                            <?php foreach ($result['uom'] as $uom) { ?>
                                                <option value="<?php $uom['id']; ?>" <?php if(isset($data['purchase_uom'])){ if($data['purchase_uom']==$uom['id']){echo "selected=selected";}}  ?>"><?php echo $uom['uom_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="purchase_uom_err"> Please Select Category </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Purchase Price</label>
                                        <input type="text" class="form-control autonumber text-right fill" data-a-sign="₹" name="purchase_price" id="purchase_price" value="<?php echo $data['purchase_price']; ?>">

                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="form-label">Sale UoM</label>
                                        <select class="form-select border js-example-basic-single" id="sale_uom" name="sale_uom">
                                            <option value="0">Select Any one Sale UoM</option>
                                            <?php foreach ($result['uom'] as $uom) {  ?>
                                                <option value="<?php $uom['id']; ?>" <?php if(isset($data['sale_uom'])){ if($data['sale_uom']==$uom['id']){echo "selected=selected";}} ?>"><?php echo $uom['uom_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="sale_uom_err"> Please Select Category </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Sale Price</label>
                                        <input type="text" class="form-control autonumber text-right fill" data-a-sign="₹" name="sale_price" id="sale_price" value="<?php echo $data['sale_price'] ?>">
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Minimum Sale Price</label>
                                        <input type="text" class="form-control autonumber text-right" data-a-sign="₹" name="minimum_sale_price" id="minimum_sale_price" value="<?php echo $data['minimum_sale_price'] ?>">
                                    </div>
                                </div>

                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">

                                        </div>
                                        <div class="col-lg-3 text-right">
                                            <button type="button" class="btn btn-primary btn_mobile" onclick=" $('[href=\'#inventory\']').tab('show');"><i class="la la-next"></i>Next</button>

                                            <!-- <a class="btn btn-primary" href="#price" role="tab">Next</a> -->
                                        </div>
                                    </div>
                                </div>


                            </div>



                            <div class="tab-pane" id="inventory" role="tabpanel">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>Minimum Level</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="minimum_level" name="minimum_level" value="<?php echo $data['minimum_level'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Danger Level</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="danger_level" name="danger_level" value="<?php echo $data['danger_level'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Maximum Capacity</label>
                                        <div>
                                            <input class="form-control text-right fill" type="text" id="maximum_capacity" name="maximum_capacity" value="<?php echo $data['maximum_capacity'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Reorder Qty</label>
                                        <div>
                                            <input class="form-control text-right fill" type="text" id="reorder_qty" name="reorder_qty" value="<?php echo $data['reorder_qty'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Minimum Discount (%)</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="minimum_discount" name="minimum_discount" value="<?php echo $data['minimum_discount'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="">Conversion Box</label>
                                        <input class="form-control" type="text" id="conversion_box" name="conversion_box" value="<?php echo $data['conversion_box'] ?>">
                                        <div style="display: none;color: red;" id="conversion_box_err"> Please enter Conversion Box </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Minimum Margin</label>
                                        <div>
                                            <input type="text" class="form-control autonumber text-right" data-a-sign="₹" name="minimum_margin" value="<?php echo $data['minimum_margin'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">

                                        </div>
                                        <div class="col-lg-3 text-right">
                                            <button type="button"
                                            class="btn btn-primary btn_mobile" onclick=" $('[href=\'#tax\']').tab('show');"><i class="la la-next"></i>Next</button>

                                            <!-- <a class="btn btn-primary" href="#price" role="tab">Next</a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tax" role="tabpanel">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>HSN/SAC Code</label>
                                        <select class="form-select col-lg-4 js-example-basic-single" name="hsn_and_sac_code_id" id="hsn_and_sac_code_id">
                                            <option value="0"> Select Any one HSN/SAC Code</option>
                                            <?php foreach ($result['hsnsac'] as $hsn) { ?>
                                                <option value="<?php $hsn['id']; ?>" <?php if(isset($data['hsn_and_sac_code_id'])){ if($data['hsn_and_sac_code_id']==$hsn['id']){echo "selected=selected";}} ?>"><?php echo $hsn['code'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>HS Code</label>
                                        <select class="form-select js-example-basic-single" id="hs_code" name="hs_code">
                                            <option value="0">Select Any one HS Code</option>
                                            <?php foreach ($result['hscode'] as $hscode) { ?>
                                                <option value="<?php $hscode['id']; ?>" <?php if(isset($data['hscode'])){ if($data['hscode']==$hscode['id']){echo "selected=selected";}} ?>"><?php echo $hscode['code_description'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="hs_code_err"> Please Select HS Code </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>SGST</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="sgst" name="sgst" value="<?php echo $data['sgst']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>CGST</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="cgst" name="cgst" value="<?php echo $data['cgst']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>IGST</label>
                                        <div>
                                            <input class="form-control text-right" type="text" id="igst" name="igst" value="<?php echo $data['igst']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">

                                        </div>
                                        <div class="col-lg-3 text-right">
                                            <button type="button" class="btn btn-primary btn_mobile" onclick=" $('[href=\'#account\']').tab('show');"><i class="la la-next"></i>Next</button>

                                            <!-- <a class="btn btn-primary" href="#price" role="tab" onclick=" $('[href=\'#account\']').tab('show');">Next</a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="account" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Inventory Asset Account</label>
                                        <select class="form-select border js-example-basic-single" name="inventory_asset_account" id="inventory_asset_account">
                                            <option value="0"> Select Any one Inventory Asset Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>" <?php if(isset($data['inventory_asset_account'])){ if($data['inventory_asset_account']==$acc['account_code']){ echo "selected=selected";}} ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Inventory Adjustment Account </label>
                                        <select class="form-select border js-example-basic-single" name="inventory_adjustment_account" id="inventory_adjustment_account">
                                            <option value="0"> Select Any one Inventory Adjustment Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>" <?php if(isset($data['inventory_adjustment_account'])){ if($data['inventory_adjustment_account']==$acc['account_code']){ echo "selected=selected";}} ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Inventory Cost Variance Account </label>
                                        <select class="form-select border js-example-basic-single" name="inventory_cost_variance_account" id="inventory_cost_variance_account">
                                            <option value="0"> Select Any one Inventory Cost Variance Account Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>" <?php if(isset($data['inventory_cost_variance_account'])){ if($data['inventory_cost_variance_account']==$acc['account_code']){ echo "selected=selected";}} ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Cogs Account</label>
                                        <select class="form-select border js-example-basic-single" name="cogs_account" id="cogs_account">
                                            <option value="0"> Select Any one Cogs Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>" <?php if(isset($data['cogs_account'])){ if($data['cogs_account']==$acc['account_code']){ echo "selected=selected";}} ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Purchase Expense Account </label>
                                        <select class="form-select border js-example-basic-single" name="purchase_expense_account" id="purchase_expense_account">
                                            <option value="0"> Select Any one Purchase Expense Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>" <?php if(isset($data['purchase_expense_account'])){ if($data['purchase_expense_account']==$acc['account_code']){ echo "selected=selected";}} ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sales Revenue Account</label>
                                        <select class="form-select border js-example-basic-single" name="sales_revenue_account" id="sales_revenue_account">
                                            <option value="0"> Select Any one Sales Revenue Account</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sales Discount Account </label>
                                        <select class="form-select border js-example-basic-single" name="sales_discount_account" id="sales_discount_account">
                                            <option value="0"> Select Any one Sales Discount Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sales Return Account </label>
                                        <select class="form-select border js-example-basic-single" name="sales_return_account" id="sales_return_account">
                                            <option value="0"> Select Any one Sales Return Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Purchase Price Variance Account </label>
                                        <select class="form-select border js-example-basic-single" name="purchase_price_variance_account" id="purchase_price_variance_account">
                                            <option value="0"> Select Any one Purchase Price Variance Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Freight Expense Account </label>
                                        <select class="form-select border js-example-basic-single" name="freight_expense_account" id="freight_expense_account">
                                            <option value="0"> Select Any one Freight Expense Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Packaging Expense Account </label>
                                        <select class="form-select border js-example-basic-single" name="packaging_expense_account" id="packaging_expense_account">
                                            <option value="0"> Select Any one Packaging Expense Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sales Tax Payable Account </label>
                                        <select class="form-select border js-example-basic-single" name="sales_tax_payable_account" id="sales_tax_payable_account">
                                            <option value="0"> Select Any one Sales Tax Payable Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Purchase Tax Payable Account </label>
                                        <select class="form-select border js-example-basic-single" name="purchase_tax_payable_account" id="purchase_tax_payable_account">
                                            <option value="0"> Select Any one Purchase Tax Payable Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Promotions And Discounts Account </label>
                                        <select class="form-select border js-example-basic-single" name="promotions_and_discounts_account" id="promotions_and_discounts_account">
                                            <option value="0"> Select Any one Promotions And Discounts Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sales Allowances Account </label>
                                        <select class="form-select border js-example-basic-single" name="sales_allowances_account" id="sales_allowances_account">
                                            <option value="0"> Select Any one Sales Allowances Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Deferred Revenue Account </label>
                                        <select class="form-select border js-example-basic-single" name="deferred_revenue_account" id="deferred_revenue_account">
                                            <option value="0"> Select Any one Deferred Revenue Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Ap Account </label>
                                        <select class="form-select border js-example-basic-single" name="ap_account" id="ap_account">
                                            <option value="0"> Select Any one Ap Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Ar Account </label>
                                        <select class="form-select border js-example-basic-single" name="ar_account" id="ar_account">
                                            <option value="0"> Select Any one Ar Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Accrued Expenses Account </label>
                                        <select class="form-select border js-example-basic-single" name="accrued_expenses_account" id="accrued_expenses_account">
                                            <option value="0"> Select Any one Accrued Expenses Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Prepaid Expenses Account </label>
                                        <select class="form-select border js-example-basic-single" name="prepaid_expenses_account" id="prepaid_expenses_account">
                                            <option value="0"> Select Any one Prepaid Expenses Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Wip Asset Account </label>
                                        <select class="form-select border js-example-basic-single" name="wip_asset_account" id="wip_asset_account">
                                            <option value="0"> Select Any one Wip Asset Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Goods Received Not Invoiced Account </label>
                                        <select class="form-select border js-example-basic-single" name="goods_received_not_invoiced_account" id="goods_received_not_invoiced_account">
                                            <option value="0"> Select Any one Goods Received Not Invoiced Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Invoiced Not Received Account </label>
                                        <select class="form-select border js-example-basic-single" name="invoiced_not_received_account" id="invoiced_not_received_account">
                                            <option value="0"> Select Any one Invoiced Not Received Account </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Purchase Base Uom </label>
                                        <select class="form-select border js-example-basic-single" name="purchase_base_uom" id="purchase_base_uom">
                                            <option value="0"> Select Any one Purchase Base Uom </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">

                                    <div class="col-lg-6 form-group">
                                        <label class="form-label">Sale Base Uom </label>
                                        <select class="form-select border js-example-basic-single" name="sale_base_uom" id="sale_base_uom">
                                            <option value="0"> Select Any one Sale Base Uom </option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php $acc['account_code']; ?>"><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Is Active</label
                                            <input type="hidden" name="is_active" value="1">
                                        <input type="checkbox" class="js-switch" value="0" name="is_active" checked="" data-switchery="true" style="display: none;">
                                    </div>
                                </div>

                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">

                                            <?php
                                            if(isset($_GET['option']))
                                            {
                                                ?>
                                                <button type="submit" onclick="formUpdate()" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Update</button>
                                                <input type="hidden" name="submit" value="Update">
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <button type="submit" onclick="formSubmit()" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Save &amp; Exit</button>
                                                <button type="submit" onclick="formSubmit()" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save &amp; Continue</button>
                                                <input type="hidden" name="submit" value="Add">
                                                <?php
                                            }

                                            ?>
                                        </div>
                                        <div class="col-lg-3 text-right">
                                            <button type="reset" disabled="disabled" class="btn btn-danger btn_mobile"><i class="la la-trash"></i>Delete</button>
                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>


</div>
</div>

<script>
    function formSubmit() {
        $.ajax({
            url: "views/itemform_submit.php",
            method: "POST",
            data: $('#item_form').serialize(),
            success: function(data) {
                console.log(data);
                //$('#slab_form')[0].reset();
            }
        });
    }

    function formUpdate() {
        $.ajax({
            url: "views/itemform_submit.php",
            method: "POST",
            data: $('#item_form').serialize(),
            success: function(data) {
                console.log(data);
                //$('#slab_form')[0].reset();
            }
        });
    }

    function ValidateForm() {
        var valid = true;

    if ($('#sku').val() == '') { valid = false; $('#sku_err').show(); } else { $('#sku_err').hide(); }
    if ($('#item_name').val() == '') { valid = false;$('#item_name_err').show(); } else{ $('#item_name_err').hide(); }

    return valid;
}

    // function editForm(rid)
    // {
    //     $.ajax({
    //         url: window.location.href,
    //         method : "POST",
    //         data:'option=1&rid='+rid,
    //         success:function(data){
    //             $('#item_form').html(data);
    //             //$('#kt_modal_KTDatatable_remote1').modal("show");
    //         },
    //         error:function (){}
    //     });
    // }
</script>
