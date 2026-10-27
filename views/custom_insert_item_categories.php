<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submititemcategory'])){
    unset($_POST['submititemcategory']);
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['price_variance'] = $_POST['price_variance'] =="0"?"0":"1";
    $_POST['qty_variance'] = $_POST['qty_variance'] =="0"?"0":"1";
    $item_specification_fields= array();
    foreach($_POST['item_specification_fields_label'] as $key=>$value){
        $item_specification_fields[$key]['item_specification_fields_label'] = $value;
        $item_specification_fields[$key]['item_specification_fields_type'] = $_POST['item_specification_fields_type'][$key];
    }
    unset($_POST['item_specification_fields_label']);
    unset($_POST['item_specification_fields_type']);
    $_POST['item_specification_fields'] =urlencode(json_encode($item_specification_fields));
    $url = API."?mode=insertitemcategories&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['result'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_ITEM_CATEGORIES");
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
        if($response['status'] == 'fail'){
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
$rid1 =$_GET['Rid'];
$rid2 =  encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = $rrid['1'];
$url = API."?mode=getitemcategories&rid=".$rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$industries = $result['industries'];
$variance_type = $result['variance_type'];
$item_categories = $result['item_categories'];
$bill_matching_type = $result['bill_matching_type'];
$customer_items = $result['customer_items'];
$tax_collected_sources = $result['tax_collected_sources'];
$tax_deducted_sources = $result['tax_deducted_sources'];
$item_categories_list = $result['item_categories_list'];
$item_specification_fields=json_decode(urldecode($item_categories['item_specification_fields']),1);
$first_removal_strategy = array('FIFO'=>'First In First Out','LIFO'=>'Last In First Out','LEFO'=>'First Expiry First Out','CL'=>'Closest Location')


?>
<script type="text/javascript">
function submititemcategoryform(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
        $('#itemcategoryform').submit();
    }
}
function addnewfields(){
    var ent_field_name = $("#ent_field_name").val();
    var slct_field_type = $("#slct_field_type").val();
    $("#fileserrormess").text(" ");
    $("#ent_field_name").val("");
    if(ent_field_name==""){
        $("#ent_field_name").val("");
        $("#fileserrormess").text("Please Enter Field Name");
        return false;
    }
    if(slct_field_type == "number"){
        var html = '<div class="form-group col-lg-6"><span>'+ent_field_name+'</span><input aria-label="Enter number" class="form-control text-right"  type="number"  ><input  type="hidden" class="fieldname"  name="item_specification_fields_label[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="item_specification_fields_type[]"  value="number"></div>';
    }else if(slct_field_type == "date"){
        var html = '<div class="form-group col-lg-6"><span>'+ent_field_name+'</span><input aria-label="Input field" class="form-control datepicker"  type="text"   ><input  type="hidden" class="fieldname"  name="item_specification_fields_label[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="item_specification_fields_type[]"  value="date"></div>';
    }else{
        var html = '<div class="form-group col-lg-6"><span>'+ent_field_name+'</span><input aria-label="Input field" class="form-control "  type="text"   ><input  type="hidden" class="fieldname"  name="item_specification_fields_label[]"  value="'+ent_field_name+'"><input  type="hidden" class="fieldname"  name="item_specification_fields_type[]"  value="text"></div>';
    }
    $(".newfield").append(html);
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
}
</script>

        <div class="card">
            <div class="card-header table-card-header">
                <h5> Item Categories</h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <!--begin::Portlet-->

            <div class="card-body">
                <form  method="POST" enctype="multipart/form-data" id="itemcategoryform">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Item Categories</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <span class="">Category Name &nbsp;<span class="text-danger">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <input aria-label="Category Description" class="form-control requried_field" type="text" id="category_description" name="category_description" value="<?php echo $item_categories['category_description'] ?>">
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <span class="form-label">Costing Type <span class="text-danger">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <select aria-label="Default Costing Type" class="form-select border js-example-basic-single requried_field" id="default_costing_type" name="default_costing_type">
                                            <?php foreach ($result['costing_type'] as $ctype) { ?>
                                                    <option value="<?php echo $ctype['genc_detail_code']; ?>" <?php if($item_categories['default_costing_type']==$ctype['genc_detail_code']){ echo "selected";
} ?>><?php echo $ctype['genc_detail_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <span class="form-label">Industry </span>
                                            <select aria-label="Industry" class="form-select border js-example-basic-single" id="industry_id" name="industry_id">
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($industries as $indusrt) { ?>
                                                    <option value="<?php echo $indusrt['id']; ?>" <?php if($item_categories['industry_id']==$indusrt['id']){ echo "selected";
} ?>><?php echo $indusrt['industries'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span class="form-label">First Removal Strategy</span>
                                            <select aria-label="First Removal Strategy" class="form-select border js-example-basic-single" id="first_removal_strategy" name="first_removal_strategy">
                                                <?php foreach ($first_removal_strategy as $key=>$val) {  ?>
                                                    <option value="<?php echo $key; ?>" <?php  if($item_categories['first_removal_strategy']==$key){echo "selected";
} ?>><?php echo $val; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                         <div class="form-group col-lg-12">
                                            <span class="form-label">Bill Matching Type</span>
                                            <div class="radio radio-outline  mt-1">
                                                <?php $mk = 1; foreach($bill_matching_type as $bmtype){ ?>
                                                <label class="radio radio-inline">
                                                    <input aria-label="Radio option" type="radio" <?php echo (($item_categories['bill_matching_type'] =="" && $mk == '1') || $bmtype['genc_detail_code'] == $item_categories['bill_matching_type'])?"checked":""; ?> name="bill_matching_type" value="<?php echo $bmtype['genc_detail_code']; ?>">  <span></span><?php echo $bmtype['genc_detail_name']; ?>
                                                </label>
                                                <?php $mk++;
} ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span class="form-label">Parent  Category</span>
                                            <select aria-label="Parent Item Category" class="form-select border js-example-basic-single" id="parent_item_category_id" name="parent_item_category_id">
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($item_categories_list as $catval) {  ?>
                                                    <option value="<?php echo $catval['id']; ?>" <?php  if($item_categories['parent_item_category_id']==$catval['id']){ echo "selected";
} ?>><?php echo $catval['category_description']." - ".$catval['category_short_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span class="">Short Name &nbsp;<span class="text-danger">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <input aria-label="Category Short Name" class="form-control requried_field" type="text" id="category_short_name" name="category_short_name" value="<?php echo $item_categories['category_short_name'] ?>">
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="card mt-2">
                                <div class="card-header table-card-header">
                                    <h5>Price</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <span class="form-label d-block">Price Variance
                                            </span>
                                            <input aria-label="Price Variance" type="checkbox" class="js-switch" name="price_variance" value="0" <?php echo $item_categories['price_variance'] =="0"?"checked":""; ?>/>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span class="form-label">Price Variance Type</span>
                                            <select aria-label="Price Variance Type" class="form-select border js-example-basic-single" name="price_variance_type">
                                                <?php foreach ($variance_type as $vt) {  ?>
                                                    <option value="<?php echo $vt['genc_detail_code']; ?>" <?php  if($item_categories['price_variance_type']==$vt['genc_detail_code']){echo "selected";
} ?>><?php echo $vt['genc_detail_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span>Price Variance Amount</span>
                                            <input aria-label="Price Variance Amount" type="text" class="form-control autonumber text-right fill" data-a-sign="₹" name="price_variance_amount" id="price_variance_amount" value="<?php echo $item_categories['price_variance_amount'] ?>">
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Inventory</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <span class="form-label d-block">Qty Variance
                                            </span>
                                            <input aria-label="Qty Variance" type="checkbox" class="js-switch" name="qty_variance" value="0" <?php echo $item_categories['qty_variance'] =="0"?"checked":""; ?>/>
                                        </div>
                                         <div class="form-group col-lg-4">
                                            <span class="form-label">Qty Variance</span>
                                            <select aria-label="Qty Variance Type" class="form-select border js-example-basic-single" name="qty_variance_type">
                                                <?php foreach ($variance_type as $vt) {  ?>
                                                    <option value="<?php echo $vt['genc_detail_code']; ?>" <?php  if($item_categories['qty_variance_type']==$vt['genc_detail_code']){echo "selected";
} ?>><?php echo $vt['genc_detail_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label">Qty Variance Amount</span>
                                            <input aria-label="Qty Variance Amount" class="form-control text-right" type="text" id="qty_variance_amount" name="qty_variance_amount" value="<?php echo $item_categories['qty_variance_amount'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Tax</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <span class="form-label">HSN/SAC Code</span>
                                            <select aria-label="Hsn And Sac Code" name="hsn_and_sac_code_id" class="form-control js-data-example-ajax-hsncodes" data-tablename="hsn_and_sac_codes" data-fieldsname="code_description"  data-fieldsname1="code" data-fieldsname2="code_description" data-fieldsname3="gst" data-category="id">
                                                <option value="">Select Any One </option>
                                                <?php foreach ($result['hsnsac'] as $hsn) { ?>
                                                    <option value="<?php echo $hsn['id']; ?>" <?php if($item_categories['hsn_and_sac_code_id']==$hsn['id']){ echo "selected";
} ?>><?php echo $hsn['code'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>


                                        <div class="form-group col-lg-6">
                                            <span class="form-label">Tax Deducted Source (TDS)</span>
                                            <select aria-label="Tax Deducted Source" class="form-select border js-example-basic-single" id="tax_deducted_source_id" name="tax_deducted_source_id">
                                                    <option value="0">Select Any one</option>
                                                    <?php foreach ($tax_deducted_sources as $tds) {  ?>
                                                        <option value="<?php echo $tds['id']; ?>" <?php  if($item_categories['tax_deducted_source_id']==$tds['id']){echo "selected";
} ?>><?php echo $tds['tds_name']." - ".$tds['tds_description']." (".$tds['threshold'].")"; ?></option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span class="form-label">Tax Collected Source (TCS)</span>
                                            <select aria-label="Tax Collected Source" class="form-select border js-example-basic-single" id="tax_collected_source_id" name="tax_collected_source_id">
                                                    <option value="">Select Any one</option>
                                                    <?php foreach ($tax_collected_sources as $tcs) {  ?>
                                                        <option value="<?php echo $tcs['id']; ?>" <?php  if($item_categories['tax_collected_source_id']==$tcs['id']){echo "selected";
} ?>><?php echo $tcs['tcs_name']."(".$tcs['percentage']."%)" ?></option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Item Specification Fields</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row newfield">
                                        <?php  foreach($item_specification_fields as $custom_fields){
                                            $clss = "";
                                             $types = "text";
                                            if($custom_fields['item_specification_fields_type'] == "date"){
                                                $clss = "datepicker";
                                            }elseif($custom_fields['item_specification_fields_type'] == "number"){
                                                $clss = "text-right";
                                                $types = "number";
                                            }
                                         ?>
                                        <div class="form-group col-lg-6">
                                            <span><?php echo $custom_fields['item_specification_fields_label']; ?></span>
                                            <input aria-label="Input field" class="form-control <?php echo $clss; ?>  fieldname"  type="<?php echo $types; ?>"   >
                                            <input  type="hidden" class="fieldname"  name="item_specification_fields_label[]"  value="<?php echo $custom_fields['item_specification_fields_label']; ?>">
                                            <input  type="hidden" class="fieldname"  name="item_specification_fields_type[]"  value="<?php echo $custom_fields['item_specification_fields_type']; ?>">
                                        </div>
                                    <?php } ?>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-lg-4 form-group">
                                            <span>Enter Field Name</span>
                                            <input aria-label="Ent Field Name" class="form-control "  type="text"  id="ent_field_name" >
                                            <span class="text-danger" id="fileserrormess"></span>
                                        </div>
                                        <div class="col-lg-4 form-group w-full">
                                            <span>Type</span>
                                            <select aria-label="Slct Field Type"  class="form-select js-example-basic-single " id="slct_field_type">
                                                    <option value="text">Text</option>
                                                    <option value="number">Number</option>
                                                    <option value="date">Date</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <button type="button" class="btn btn-primary waves-effect waves-light p-2 m-t-10 b-radius-5" onclick="addnewfields()">Add Field</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Account</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Inventory Asset Account</span>

                                            <select aria-label="Inventory Asset Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_asset_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['inventory_asset_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Inventory Adjustment Account </span>

                                            <select aria-label="Inventory Adjustment Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_adjustment_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['inventory_adjustment_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Inventory Cost Variance Account </span>
                                            <select aria-label="Inventory Cost Variance Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_cost_variance_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['inventory_cost_variance_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Cogs Account</span>
                                            <select aria-label="Cogs Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="cogs_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['cogs_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Purchase Expense Account </span>

                                            <select aria-label="Purchase Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['purchase_expense_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Sales Revenue Account</span>
                                            <select aria-label="Sales Revenue Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_revenue_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['sales_revenue_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Sales Discount Account </span>
                                            <select aria-label="Sales Discount Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_discount_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['sales_discount_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Sales Return Account </span>
                                            <select aria-label="Sales Return Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_return_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['sales_return_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Purchase Price Variance Account </span>
                                            <select aria-label="Purchase Price Variance Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_price_variance_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['purchase_price_variance_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Freight Expense Account </span>
                                            <select aria-label="Freight Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="freight_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['freight_expense_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Packaging Expense Account </span>
                                            <select aria-label="Packaging Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="packaging_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['packaging_expense_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Sales Tax Payable Account </span>
                                            <select aria-label="Sales Tax Payable Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_tax_payable_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['sales_tax_payable_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Purchase Tax Payable Account </span>
                                            <select aria-label="Purchase Tax Payable Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_tax_payable_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['purchase_tax_payable_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Promotions And Discounts Account </span>
                                            <select aria-label="Promotions And Discounts Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="promotions_and_discounts_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['promotions_and_discounts_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Sales Allowances Account </span>
                                            <select aria-label="Sales Allowances Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_allowances_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['sales_allowances_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Deferred Revenue Account </span>
                                            <select aria-label="Deferred Revenue Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="deferred_revenue_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['deferred_revenue_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Ap Account </span>
                                            <select aria-label="Ap Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="ap_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['ap_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Ar Account </span>
                                            <select aria-label="Ar Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="ar_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['ar_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Accrued Expenses Account </span>
                                            <select aria-label="Accrued Expenses Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="accrued_expenses_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['accrued_expenses_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Prepaid Expenses Account </span>
                                            <select aria-label="Prepaid Expenses Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="prepaid_expenses_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['prepaid_expenses_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Wip Asset Account </span>
                                            <select aria-label="Wip Asset Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="wip_asset_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['wip_asset_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Goods Received Not Invoiced Account </span>
                                            <select aria-label="Goods Received Not Invoiced Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="goods_received_not_invoiced_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['goods_received_not_invoiced_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <span class="form-label">Invoiced Not Received Account </span>
                                            <select aria-label="Invoiced Not Received Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="invoiced_not_received_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($item_categories['invoiced_not_received_account']==$acc['account_code']){ echo "selected";
} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    <div class="mt-3">
                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                        <input type="hidden" name="submititemcategory" value="">
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submititemcategoryform()"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
                    </div>
                </form>
            </div>
        </div>
