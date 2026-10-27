<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submititem'])){
    unset($_POST['submititem']);
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $image = imageupload('all','item_image',$_POST['hid_item_image']);
    unset($_POST['hid_item_image']);
    $_POST['item_image'] = $image;
    $image = imageupload('all','item_image2',$_POST['hid_item_image2']);
    unset($_POST['hid_item_image2']);
    $_POST['item_image2'] = $image;
    $image = imageupload('all','item_image3',$_POST['hid_item_image3']);
    unset($_POST['hid_item_image3']);
    $_POST['item_image3'] = $image;
    $_POST['sold_individually'] = $_POST['sold_individually'] =="0"?"0":"1";
    $_POST['sold_in_groups'] = $_POST['sold_in_groups'] =="0"?"0":"1";
    $_POST['quality_check'] = $_POST['quality_check'] =="0"?"0":"1";
    $_POST['negative_stock'] = $_POST['negative_stock'] =="0"?"0":"1";
    $_POST['allow_back_order'] = $_POST['allow_back_order'] =="0"?"0":"1";
    

   // foreach($_POST['old_attachment'] as $rowid=>$imgdetail){     
        foreach($_POST['old_attachment'] as $key=>$image){
            $attachedfiles = array();
            $attachedfiles['attachment'] = $_POST['old_attachment'][$key];
            $attachedfiles['attachment_name'] = $_POST['old_attachment_name'][$key];
            $_POST['all_attachment'][] = $attachedfiles;
        }
   // }
        unset($_POST['old_attachment']);
        unset($_POST['old_attachment_name']);
    //foreach($_FILES['attachment']['name'] as $rowid=>$imgdetail){
        foreach($_FILES['attachment']['name'] as $key=>$image){
            if($_FILES['attachment']['error'][$key] != 4){
                $temp = explode(".", basename($_FILES['attachment']['name'][$key]));
                $Filename=  "file_".time()."_".uniqid();
                $newfilename = $Filename.'.' .end($temp);
                // $_POST['all_attachment'][$rowid][] = $newfilename;
                // $_POST['all_attachment_name'][$rowid][] = $_POST['attachment_name'][$rowid][$key];
                $attachedfiles = array();
                $attachedfiles['attachment'] = $newfilename;
                $attachedfiles['attachment_name'] = $_POST['attachment_name'][$key]!=""?$_POST['attachment_name'][$key]:"file_".$key;
                $_POST['all_attachment'][] = $attachedfiles;
                move_uploaded_file($_FILES['attachment']['tmp_name'][$key], 'uploads/compliance_specification_documents/'.$newfilename);
            }
        }
    //}
    //echo "<pre>"; print_r($_POST); echo "</pre>"; 
   $url = API."?mode=insertitems&method=post";
   $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    //echo "<pre>"; print_r($res);echo "</pre>"; 
    $response = $res['result'];
    if($response['status'] == 'sucess'){ 
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_ITEMS");
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
$url = API."?mode=getitems&rid=".$rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$customers = $result['customers'];
$vendors = $result['vendors'];
$items = $result['items'];
$vendor_items = $result['vendor_items'];
$customer_items = $result['customer_items'];
$display_attributes = $result['display_attributes'];
$lists = json_decode($display_attributes['list_attributes'],1);

$locations = $result['locations'];
$location = array();
foreach($locations as $lloc){
    $location[$lloc['id']] = $lloc['location_name'];
}
$warehouses = $result['warehouses'];
$item_specification_fields=json_decode(urldecode($result['item_specification_fields']),1);
//echo "<pre>"; print_r($result); echo "</pre>";
//echo $url;
// if(isset($_GET['option'])){
//     $table_name='items';
    
//     $url = API."?mode=togetdatabasepaimairy&rid=".$_GET['Rid']."&table=".$table_name;
    
//     $response = callAPI($url);
//     $data = json_decode($response,1);
// }
?>





<script type="text/javascript">
    var maxid = 10000;
    function addMoreVendorItems(){
        maxid = maxid+1;
        var deletrowid ="vendoritemrow_"+maxid; 
        var html = '';
        html = '<div class="row mt-2" id="'+deletrowid+'"><input type="hidden" name="vendor_items[vendor_item_id][]" value="">';
        html += '<div class="col-lg-5"><select class="form-select js-example-basic-single" name="vendor_items[vendor_id][]"><?php foreach($vendors as $ven){ ?><option value="<?php echo $ven['id']; ?>" ><?php echo trim(preg_replace('/\s+/', ' ', $ven['display_name']."(".$ven['company'].")")); ?></option><?php } ?></select></div>';
        html += '<div class="col-lg-3"><input class="form-control text-right" type="text" name="vendor_items[vendor_part_no][]" value=""></div>';
        html += '<div class="col-lg-3"><input class="form-control text-right" type="text" name="vendor_items[vendor_part_name][]" value=""></div>';
        html += '<div class="col-lg-1"><i class="ti-trash text-danger" onclick="removeItem(\''+deletrowid+'\')"></i></div>';
        html +='</div>';
        $("#addmorevendoritem").append(html);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    function removeItem(id){
        $("#"+id).remove();
    }
 
                                                
function submititemform() {
        var itemType = document.getElementById("item_type").value;
        var warehouseId = document.getElementById("warehouse_id").value;
        var categoryId = document.getElementById("category_id").value;
        var ridField = document.querySelector("input[name='rid']");
        var rid = ridField ? ridField.value : "";

        var missingFields = [];
        if (itemType === "") missingFields.push("Item Type");
        if (warehouseId === "") missingFields.push("Warehouse");

        // Step 1: Handle missing required fields
        if (missingFields.length > 0) {
            var message = "Please select <strong>" + missingFields.join("</strong> and <strong>") + "</strong> before submitting.<br>Otherwise your SKU code will not be generated correctly.";
            document.getElementById("validationMessage").innerHTML = message;

            var validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
            validationModal.show();

            document.getElementById("proceedAnywayBtn").onclick = function () {
                $('#itemform').submit();
                validationModal.hide();
            };
            return; // stop here
        }

        // Step 2: If updating (rid exists), check if Category/ItemType/Warehouse changed
        if (rid !== "") {
            var originalCategory = "<?php echo $items['category_id']; ?>";
            var originalItemType = "<?php echo $items['item_type']; ?>";
            var originalWarehouse = "<?php echo $items['warehouse_id']; ?>";

            var changedFields = [];
            if (categoryId !== originalCategory) changedFields.push("Category");
            if (itemType !== originalItemType) changedFields.push("Item Type");
            if (warehouseId !== originalWarehouse) changedFields.push("Warehouse");

            if (changedFields.length > 0) {
                var msg = "You changed <strong>" + changedFields.join("</strong>, <strong>") + "</strong>.<br>Your SKU will be different.";
                document.getElementById("skuChangeMessage").innerHTML = msg;

                var skuChangeModal = new bootstrap.Modal(document.getElementById('skuChangeModal'));
                skuChangeModal.show();

                // If duplicate is chosen → clear rid and submit
                document.getElementById("duplicateItemBtn").onclick = function () {
                    ridField.value = ""; // clear rid → becomes new item
                    $('#itemform').submit();
                    skuChangeModal.hide();
                };

                return; // stop further execution
            }
        }

        // Step 3: Normal validation + submit
        var validations = ajaxFormSubmitValidation();
        if (validations == 0) {
            $('#itemform').submit();
        }
    }                                                

// function submititemform(){
//     var validations = ajaxFormSubmitValidation();
//     if(validations == 0){
//         $('#itemform').submit();
//     }
// }
                                         
function deletRowjoinOwner(rowid){
    $("#"+rowid).remove();
    $('.tooltip').remove();
}
var maxrowsattachemets = 36980;
function addMoreAttachment(rowid){
    maxrowsattachemets = maxrowsattachemets+1;
    var deleteid = "deleteattachementsid_"+maxrowsattachemets+"";
    var html = '';
    html += '<div class="row" id="'+deleteid+'">';
    html += '<div class="form-group col-lg-8"><input type="text" class="form-control" name="attachment_name[]" value=""></div>';
    html += '<div class="form-group col-lg-2 text-center"><div class="image-input image-input-empty image-input-outline" id="kt_image_5"><label class="btn-hover-text-primary text-dark" data-action="remove" data-bs-toggle="tooltip" title="" style="left: -9px;top: 0px;" data-bs-original-title="Upload File"><span> <i class="fa fa-upload"></i></span><input type="file" name="attachment[]" accept=".pdf, image/*"></label></div></div>';
    html += '<div class="form-group col-lg-2"><i class="ti-trash float-end text-danger"  onclick="deletRowjoinOwner(\''+deleteid+'\')"></i> </div>';
    html += '</div>';
    $("#addmoreattachements_id").append(html);
}                                       

</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>  
<?php } ?>

        <div class="card">
            <div class="card-header table-card-header">
                <h5> Items</h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <!--begin::Portlet-->
            <!-- <div class="card-infisuite"> -->

                
            <div class="card-body">
                <form  method="POST" enctype="multipart/form-data" id="itemform">
                    <div class="row">
                        <div class="col-lg-6">        
                            <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Items</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label class="">Item Name&nbsp;<span class="text-danger">*</span></label>
                                             <span class="d-block error-message text-danger"></span>
                                            <input class="form-control requried_field" type="text" id="item_name" name="item_name" value="<?php echo $items['item_name']!=""?$items['item_name']:$_GET['ITMNAME']; ?>">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label class="">SKU</label>
                                            <input class="form-control" type="text" id="sku" name="sku" value="<?php echo $items['sku'] ?>" readonly>
                                        </div>
                                        <?php if($lists['ref_sku']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-3">
                                            <label class="">Ref SKU</label>
                                            <input class="form-control" type="text" id="ref_sku" name="ref_sku" value="<?php echo $items['ref_sku'] ?>">
                                        </div>
                                        <?php }if($lists['item_description']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label">Item Description</label>
                                            <textarea class="form-control" rows="5" cols="5" id="item_description" name="item_description"><?php echo $items['item_description'] ?></textarea>
                                        </div>
                                        <?php }if($lists['specifications']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="">Specifications</label>
                                            <input class="form-control" type="text" id="specifications" name="specifications" value="<?php echo $items['specifications']; ?>">   
                                        </div>
                                        <?php }if($lists['category_id']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-4">
                                            <label class="form-label">Category <span class="text-danger">*</span></label>
                                             <span class="d-block error-message text-danger"></span>
                                            <select class="form-select border js-example-basic-single requried_field" id="category_id" name="category_id">
                                                <option value="">Select Any one</option>
                                                  <?php foreach ($result['category'] as $category) { ?>
                                                    <option value="<?php echo $category['id']; ?>" <?php if($items['category_id']==$category['id']){ echo 'selected'; }?>><?php echo $category['category_description'] ?></option>;
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['item_type']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-4">
                                            <label class="form-label">Item Type </label>
                                            <select class="form-select border js-example-basic-single" id="item_type" name="item_type">
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['item_types'] as $item_type) { ?>
                                                    <option value="<?php echo $item_type['id']; ?>" <?php if($items['item_type']==$item_type['id']){ echo "selected"; } ?>><?php echo $item_type['item_type_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['default_costing_type']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label">Costing Type</label>
                                            <select class="form-select border js-example-basic-single" id="default_costing_type" name="default_costing_type">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['costing_type'] as $ctype) { ?>
                                                    <option value="<?php echo $ctype['genc_detail_code']; ?>" <?php if($items['default_costing_type']==$ctype['genc_detail_code']){ echo "selected"; } ?>><?php echo $ctype['genc_detail_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['sold_individually']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Sold Individually</label>
                                            <input type="checkbox" class="js-switch" <?php echo $items['sold_individually'] =="0"?"checked":""; ?> name="sold_individually" value="0" />
                                        </div>
                                        <?php }if($lists['sold_in_groups']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Sold In Groups
                                            </label>
                                            <input type="checkbox" class="js-switch" name="sold_in_groups" value="0" <?php echo $items['sold_in_groups'] =="0"?"checked":""; ?>/>
                                        </div>
                                        <?php }if($lists['quality_check']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Quality Check
                                            </label>
                                            <input type="checkbox" class="js-switch" name="quality_check" value="0" <?php echo $items['quality_check'] =="0"?"checked":""; ?>/>
                                        </div>
                                        <?php }if($lists['item_image']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Item Image</label>
                                            <div class="profile-pic">
                                                <label class="-label" for="item_image">
                                                    <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                                </label>
                                                <input type="file" name="item_image" id="item_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                                <img src="<?php echo "uploads/all/".$items['item_image'] ; ?>" id="outputitem_image" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
                                                <input type="hidden" name="hid_item_image" value="<?php echo $items['item_image'] ;  ?>">
                                            </div>

                                        </div>
                                        <?php }if($lists['item_image2']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Item Image2</label>
                                            <div class="profile-pic">
                                                <label class="-label" for="item_image2">
                                                    <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                                </label>
                                                <input type="file" name="item_image2" id="item_image2" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                                <img src="<?php echo "uploads/all/".$items['item_image2'] ; ?>" id="outputitem_image2" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
                                                <input type="hidden" name="hid_item_image2" value="<?php echo $items['item_image2'] ;  ?>">
                                            </div>

                                        </div>
                                        <?php }if($lists['item_image3']['insert_display_list'] == 1){ ?>
                                        
                                        <div class="form-group col-lg-4">
                                            <label class="form-label d-block">Item Image3</label>
                                            <div class="profile-pic">
                                                <label class="-label" for="item_image3">
                                                    <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                                </label>
                                                <input type="file" name="item_image3" id="item_image3" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                                <img src="<?php echo "uploads/all/".$items['item_image3'] ; ?>" id="outputitem_image3" width="200" onerror="this.src='uploads/all/no-image.jpg';"/>
                                                <input type="hidden" name="hid_item_image3" value="<?php echo $items['item_image3'] ;  ?>">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        
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
                                        <?php if($lists['purchase_uom']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label">Purchase UoM</label>
                                            <select class="form-select border js-example-basic-single" id="purchase_uom" name="purchase_uom">
                                                <option value="0">Select Any one Purchase UoM</option>
                                                <?php foreach ($result['uom'] as $uom) { ?>
                                                    <option value="<?php echo $uom['id']; ?>" <?php  if($items['purchase_uom']==$uom['id']){echo "selected=selected";}  ?>><?php echo $uom['uom_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['purchase_price']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Purchase Price</label>
                                            <input type="text" class="form-control autonumber text-right" data-a-sign="₹" name="purchase_price" id="purchase_price" value="<?php echo $items['purchase_price']; ?>">
                                        </div>
                                        <?php }if($lists['sale_uom']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label">Sale UoM</label>
                                            <select class="form-select border js-example-basic-single" id="sale_uom" name="sale_uom">
                                                <option value="0">Select Any one Sale UoM</option>
                                                <?php foreach ($result['uom'] as $uom) {  ?>
                                                    <option value="<?php echo $uom['id']; ?>" <?php  if($items['sale_uom']==$uom['id']){echo "selected";} ?>><?php echo $uom['uom_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['sale_price']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Sale Price</label>
                                            <input type="text" class="form-control autonumber text-right fill" data-a-sign="₹" name="sale_price" id="sale_price" value="<?php echo $items['sale_price'] ?>">
                                        </div>
                                        <?php }if($lists['minimum_sale_price']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Minimum Sale Price</label>
                                            <input type="text" class="form-control text-right" name="minimum_sale_price" id="minimum_sale_price" value="<?php echo $items['minimum_sale_price'] ?>">
                                        </div>
                                        <?php }if($lists['minimum_discount']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Maximum Discount(%)</label>
                                            <input type="text" class="form-control text-right"  name="minimum_discount" id="minimum_discount" value="<?php echo $items['minimum_discount'] ?>">
                                        </div>
                                        <?php } /**if($lists['conversion_box']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Conversion Box</label>
                                            <input type="text" class="form-control text-right"  name="conversion_box" id="conversion_box" value="<?php echo $items['conversion_box'] ?>">
                                        </div>
                                        <?php }**/ if($lists['minimum_margin']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Minimum Margin</label>
                                            <input type="text" class="form-control text-right"  name="minimum_margin" id="minimum_margin" value="<?php echo $items['minimum_margin'] ?>">
                                        </div>
                                        <?php } ?>
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
                                        <?php if($lists['opening_quantity']['insert_display_list'] == 1){ ?>
                                            <input type="hidden" name="opening_quantity_added_or_not" values="<?php echo ($items['opening_quantity']=="" OR $items['opening_quantity']=="0")?"N":"Y"; ?>">
                                        <div class="form-group col-lg-6 <?php echo ($items['opening_quantity']=="" OR $items['opening_quantity']=="0")?"":"custom_readonly"; ?>">
                                            <label class="form-label">Opening Quantity</label>
                                            <input class="form-control text-right" type="text" id="opening_quantity" name="opening_quantity" value="<?php echo $items['opening_quantity']; ?>" >
                                        </div>
                                        <div class="form-group col-lg-6 <?php echo ($items['opening_quantity']=="" OR $items['opening_quantity']=="0")?"":"custom_readonly"; ?>">
                                            <label class="form-label">Warehouses</label>
                                            <select class="form-select js-example-basic-single"  id="warehouse_id" name="warehouse_id" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach($warehouses as $waro){ ?>
                                                    <option value="<?php echo $waro['id']; ?>" <?php if($items['warehouse_id']==$waro['id']){ echo "selected";} ?>><?php echo $waro['warehouse_name']."(".$location[$waro['location_id']].")"; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 <?php echo ($items['opening_quantity']=="" OR $items['opening_quantity']=="0")?"":"custom_readonly"; ?>">
                                            <label class="form-label">Opening Quantity Date</label>
                                            <input class="form-control datepicker" type="text" id="opening_quantity_date" name="opening_quantity_date" value="<?php echo fromDate($items['opening_quantity_date']); ?>">
                                        </div>
                                        <?php }if($lists['quantity_check']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label">Quantity Check</label>
                                            <div class="radio radio-outline  mt-1">
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" <?php echo $items['quantity_check'] =="1"?"checked":""; ?> name="quantity_check" value="1">  <span></span>Yes 
                                                </label>
                                                 
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" name="quantity_check" value="2" <?php echo $items['quantity_check'] =="2"?"checked":""; ?>> <span></span>No 
                                                </label>
                                                 
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" name="quantity_check" value="3" <?php echo $items['quantity_check'] =="3"?"checked":""; ?>> <span></span>Not Applicable
                                                </label>
                                            </div>
                                        </div>
                                        <?php }if($lists['minimum_level']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Minimum Level</label>
                                            <div>
                                                <input class="form-control text-right" type="text" id="minimum_level" name="minimum_level" value="<?php echo $items['minimum_level'] ?>">
                                            </div>
                                        </div>
                                        <?php }if($lists['danger_level']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Danger Level</label>
                                            <div>
                                                <input class="form-control text-right" type="text" id="danger_level" name="danger_level" value="<?php echo $items['danger_level'] ?>">
                                            </div>
                                        </div>
                                        <?php }if($lists['maximum_capacity']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Maximum Capacity</label>
                                            <div>
                                                <input class="form-control text-right fill" type="text" id="maximum_capacity" name="maximum_capacity" value="<?php echo $items['maximum_capacity'] ?>">
                                            </div>
                                        </div>
                                        <?php }if($lists['reorder_qty']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label>Reorder Qty</label>
                                            <div>
                                                <input class="form-control text-right fill" type="text" id="reorder_qty" name="reorder_qty" value="<?php echo $items['reorder_qty'] ?>">
                                            </div>
                                        </div>
                                        <?php }if($lists['tracking_type']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label">Tracking Type</label>
                                            <div class="radio radio-outline  mt-1">
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" checked="" name="tracking_type" value="0" <?php echo $items['tracking_type'] =="0"?"checked":""; ?>>   <span></span>None 
                                                </label>
                                             
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" name="tracking_type" value="1" <?php echo $items['tracking_type'] =="1"?"checked":""; ?>>  <span></span>Serial 
                                                </label>
                                             
                                                <label class="radio radio-inline"> 
                                                    <input type="radio" name="tracking_type" value="2" <?php echo $items['tracking_type'] =="2"?"checked":""; ?>>  <span></span>Lot 
                                                </label>
                                            </div>
                                        </div>
                                        <?php }if($lists['negative_stock']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label d-block">Negative Stock </label>
                                            <input type="checkbox" class="js-switch" name="negative_stock" value="0" <?php echo $items['negative_stock'] =="0"?"checked":""; ?>/>
                                        </div>
                                        <?php }if($lists['allow_back_order']['insert_display_list'] == 1){ ?>
                                        <div class="form-group col-lg-6">
                                            <label class="form-label d-block">Allow Back Order </label>
                                            <input type="checkbox" class="js-switch" name="allow_back_order" value="0" <?php echo $items['allow_back_order'] =="0"?"checked":""; ?>/>
                                        </div>
                                        <?php } ?>
                                    </div>


                                </div>
                            </div>
                            <?php /** ?>
                             <div class="card">
                                <div class="card-header table-card-header">
                                    <h5>Item Specification</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row newfield">
                                        <?php  
                                        foreach($item_specification_fields as $custom_fields){ 
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
                                            <label><?php echo $custom_fields['item_specification_fields_label']; ?></label>
                                            <input class="form-control <?php echo $clss; ?>  fieldname"  type="<?php echo $types; ?>"   name="item_specification_fields_value[]"  value="<?php echo $custom_fields['item_specification_fields_value']; ?>">
                                            <input  type="hidden" class="fieldname"  name="item_specification_fields_label[]"  value="<?php echo $custom_fields['item_specification_fields_label']; ?>">
                                            <input  type="hidden" class="fieldname"  name="item_specification_fields_type[]"  value="<?php echo $custom_fields['item_specification_fields_type']; ?>">
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div> <?php **/ ?>
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
                                        <?php if($lists['inventory_asset_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Inventory Asset Account</label>

                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_asset_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['inventory_asset_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                         <?php }if($lists['inventory_adjustment_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Inventory Adjustment Account </label>

                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_adjustment_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['inventory_adjustment_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['inventory_cost_variance_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Inventory Cost Variance Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="inventory_cost_variance_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['inventory_cost_variance_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['cogs_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Cogs Account</label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="cogs_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['cogs_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['purchase_expense_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Purchase Expense Account </label>

                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['purchase_expense_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['sales_revenue_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sales Revenue Account</label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_revenue_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['sales_revenue_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['sales_discount_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sales Discount Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_discount_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['sales_discount_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['sales_return_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sales Return Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_return_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['sales_return_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['purchase_price_variance_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Purchase Price Variance Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_price_variance_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['purchase_price_variance_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['freight_expense_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Freight Expense Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="freight_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['freight_expense_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['packaging_expense_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Packaging Expense Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="packaging_expense_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['packaging_expense_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['sales_tax_payable_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sales Tax Payable Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_tax_payable_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['sales_tax_payable_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['purchase_tax_payable_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Purchase Tax Payable Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="purchase_tax_payable_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['purchase_tax_payable_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['promotions_and_discounts_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Promotions And Discounts Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="promotions_and_discounts_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['promotions_and_discounts_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['sales_allowances_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sales Allowances Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="sales_allowances_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['sales_allowances_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['deferred_revenue_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Deferred Revenue Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="deferred_revenue_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['deferred_revenue_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['ap_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Ap Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="ap_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['ap_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['ar_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Ar Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="ar_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['ar_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['accrued_expenses_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Accrued Expenses Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="accrued_expenses_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['accrued_expenses_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['prepaid_expenses_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Prepaid Expenses Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="prepaid_expenses_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['prepaid_expenses_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['wip_asset_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Wip Asset Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="wip_asset_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['wip_asset_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['goods_received_not_invoiced_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Goods Received Not Invoiced Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="goods_received_not_invoiced_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['goods_received_not_invoiced_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <?php }if($lists['invoiced_not_received_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Invoiced Not Received Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="invoiced_not_received_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['invoiced_not_received_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['invoiced_not_received_account']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Goods Sold Not Invoiced Account </label>
                                            <select class="form-select js-data-example-ajax-Comman-form  col-lg-4"   data-tablename="chart_of_accounts"  data-fieldsname="account_name" data-value="" data-pagename="chart_of_accounts" name="goods_sold_not_invoiced_account" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['account'] as $acc) { ?>
                                                    <option value="<?php echo $acc['account_code']; ?>" <?php if($items['goods_sold_not_invoiced_account']==$acc['account_code']){ echo "selected";} ?>><?php echo $acc['account_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>


                                        <?php }if($lists['purchase_base_uom']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Purchase Base Uom </label>
                                            <select class="form-select js-example-basic-single  col-lg-4"  name="purchase_base_uom" >
                                                <label class="form-label">Purchase Base Uom </label>
                                            
                                                <?php foreach ($result['uom'] as $uom) { ?>
                                                    <option value="<?php echo $uom['id']; ?>" <?php  if($items['purchase_base_uom']==$uom['id']){echo "selected=selected";}  ?>><?php echo $uom['uom_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?php }if($lists['sale_base_uom']['insert_display_list'] == 1){ ?>
                                        <div class="col-lg-6 form-group">
                                            <label class="form-label">Sale Base Uom </label>
                                            <select class="form-select js-example-basic-single  col-lg-4"    name="sale_base_uom" >
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['uom'] as $uom) { ?>
                                                    <option value="<?php echo $uom['id']; ?>" <?php  if($items['sale_base_uom']==$uom['id']){echo "selected=selected";}  ?>><?php echo $uom['uom_name'] ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <?php } ?>
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
                                            <label class="form-label">HSN/SAC Code</label>
                                            <select name="hsn_and_sac_code_id" class="form-control js-data-example-ajax-hsncodes" data-tablename="hsn_and_sac_codes" data-fieldsname="code"  data-fieldsname1="code" data-fieldsname2="code_description" data-fieldsname3="gst" data-category="id">
                                                <option value="">Select Any One </option>
                                                <?php foreach ($result['hsnsac'] as $hsn) { ?>
                                                    <option value="<?php echo $hsn['id']; ?>" <?php if($items['hsn_and_sac_code_id']==$hsn['id']){ echo "selected"; } ?>><?php echo $hsn['code'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label class="form-label">HS Code</label>
                                            <select class="form-select js-example-basic-single" id="hs_code" name="hs_code">
                                                <option value=""> Select Any one</option>
                                                <?php foreach ($result['hscode'] as $hscode) { ?>
                                                    <option value="<?php echo $hscode['id']; ?>" <?php if($items['hs_code']==$hscode['id']){echo "selected";} ?>><?php echo $hscode['code_description'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- <div class="form-group col-lg-6">
                                            <label class="form-label">SGST</label>
                                            <input class="form-control text-right" type="text" id="sgst" name="sgst" value="<?php //echo $items['sgst']; ?>">
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label class="form-label">CGST</label>
                                            <input class="form-control text-right" type="text" id="cgst" name="cgst" value="<?php //echo $items['cgst']; ?>">
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label class="form-label">IGST</label>
                                            <input class="form-control text-right" type="text" id="igst" name="igst" value="<?php //echo $items['igst']; ?>">
                                        </div> -->
                                    </div>

                                </div>
                            </div>
                            <div class="card">
                            <div class="card-header table-card-header">
                                    <h5>Specification Documents</h5>
                                    <div class="card-header-right table-card-header ">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-8">
                                            <label class="form-label">File Name</label>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">Download/Upload</label>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                <?php
                                    $attachments = json_decode($items['compliance_specification_documents'],1); $attchmentseq =1;   $i=100;  
                                    foreach($attachments as $attachment){ ?>
                                    <div class="row mb-2" id="remove_attache_<?php echo $attchmentseq; ?>">
                                            <!-- <a href="uploads/compliance_specification_documents/<?php echo $attachment['attachment']; ?>" class=" col-lg-6" target="_blank"><label class="form-label label label-primary cursor-pointer w-100" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download"><i class="ti-cloud-down"></i> &nbsp; <?php echo $attachment['attachment_name'] !=""?$attachment['attachment_name']:$attachment['attachment']; ?></label></a> -->
                                          <div class="col-lg-8">
                                                <input type="text" name="old_attachment_name[]" class="form-control" value="<?php echo $attachment['attachment_name']; ?>" readonly> 
                                            </div>
                                            <div class="col-lg-2 text-center">
                                                <a href="uploads/compliance_specification_documents/<?php echo $attachment['attachment']; ?>" class="text-primary" target="_blank"><i class="fa fa-download"></i></a>
                                            </div>
                                          <div class="col-lg-2">
                                           
                                            <i class="ti-trash float-end text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRowjoinOwner('remove_attache_<?php echo $attchmentseq; ?>')"></i>

                                            <input type="hidden" name="old_attachment[]" class="form-control" value="<?php echo $attachment['attachment']; ?>"> 
                                            
                                        </div>
                                    </div>
                                    <?php $attchmentseq++;}  ?>
                                    
                                    <div id="addmoreattachements_id">
                                        <div class="row mb-2" id="remove_attache_<?php echo $attchmentseq; ?>">
                                            <div class="form-group col-lg-8">
                                                <input type="text" class="form-control" name="attachment_name[]" value="">
                                            </div>
                                            <div class="form-group col-lg-2 text-center">

                                                <div class="image-input image-input-empty image-input-outline" id="kt_image_5">
                                                    <label class="btn-hover-text-primary text-dark" data-action="remove" data-bs-toggle="tooltip" title="" style="left: -9px;top: 0px;" data-bs-original-title="Upload File">
                                                      <span> <i class="fa fa-upload"></i></span>
                                                      <input type="file" name="attachment[]" accept=".pdf, image/*">
                                                    </label>
                                                </div>
                                                <!-- <input type="file" class="form-control fileinputvalidation" name="attachment[]" value=""> -->
                                            </div>
                                            <div class="form-group col-lg-2">
                                                <i class="ti-trash float-end text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRowjoinOwner('remove_attache_<?php echo $attchmentseq; ?>')"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMoreAttachment()"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More</button>
                                    </div>
                                </div>
                        </div>
                    </div>
                         <div class="row"> 
                            <div class="form-group col-lg-6">
                                <div class="card">
                                    <div class="card-header table-card-header">
                                        <h5>Vendor Items</h5>
                                        <div class="card-header-right table-card-header ">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label class="form-label">Vendors</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Part No</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Part Name</label>
                                            </div>
                                            <div class="col-lg-1"></div>
                                        </div>
                                        <div id="addmorevendoritem">
                                            <?php 
                                            $mk = 1;
                                            if(count((array)$vendor_items) == 0){
                                                $vendor_items[] = array();
                                            }
                                            foreach($vendor_items as $vi){ 
                                            $deletrowid ="vendoritemrow_".$mk;
                                            ?> 
                                            <div class="row mt-2" id="<?php echo $deletrowid; ?>"> 
                                                <input type="hidden" name="vendor_items[vendor_item_id][]" value="<?php echo $vi['id']; ?>">
                                                <div class="col-lg-5">
                                                    <select class="form-select js-example-basic-single"  name="vendor_items[vendor_id][]" >
                                                        <?php foreach ($vendors as $ven) { ?>
                                                            <option value="<?php echo $ven['id']; ?>" <?php if($ven['id']==$vi['vendor_id']){ echo "selected"; } ?> ><?php echo $ven['display_name']."(".$ven['company'].")" ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3">
                                                    <input class="form-control text-right" type="text"  name="vendor_items[vendor_part_no][]" value="<?php echo $vi['vendor_part_no']; ?>">
                                                </div>
                                                <div class="col-lg-3">
                                                    <input class="form-control text-right" type="text" name="vendor_items[vendor_part_name][]" value="<?php echo $vi['vendor_part_name']; ?>">
                                                </div>
                                                <div class="col-lg-1">
                                                    <i class="ti-trash text-danger" onclick="removeItem('<?php echo $deletrowid; ?>')"></i>
                                                </div>
                                            </div>
                                    <?php $mk++; } ?>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xs-12">
                                                <button class="btn btn-success"  type="button" onclick="addMoreVendorItems()">+ Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <div class="card">
                                    <div class="card-header table-card-header">
                                        <h5>Customers Items</h5>
                                        <div class="card-header-right table-card-header ">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label class="form-label">Customers</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Part No</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Part Name</label>
                                            </div>
                                            <div class="col-lg-1"></div>
                                        </div>
                                        <div id="addmorecustomeritem">
                                            <?php 
                                            $mks = 1;
                                            if(count((array)$customer_items) == 0){
                                                $customer_items[] = array();
                                            }
                                            foreach($customer_items as $vi){ 
                                            $deletrowid ="customeritemrow_".$mks;
                                            ?> 
                                            <div class="row mt-2" id="<?php echo $deletrowid; ?>"> 
                                                <input type="hidden" name="customer_items[customer_item_id][]" value="<?php echo $vi['id']; ?>">
                                                <div class="col-lg-5">
                                                    <select class="form-select js-example-basic-single"  name="customer_items[customer_id][]" >
                                                        <?php foreach ($customers as $ven) { ?>
                                                            <option value="<?php echo $ven['id']; ?>" <?php if($ven['id']==$vi['customer_id']){ echo "selected"; } ?> ><?php echo $ven['display_name']."(".$ven['company'].")" ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3">
                                                    <input class="form-control text-right" type="text"  name="customer_items[customer_part_no][]" value="<?php echo $vi['customer_part_no']; ?>">
                                                </div>
                                                <div class="col-lg-3">
                                                    <input class="form-control text-right" type="text" name="customer_items[customer_part_name][]" value="<?php echo $vi['customer_part_name']; ?>">
                                                </div>
                                                <div class="col-lg-1">
                                                    <i class="ti-trash text-danger" onclick="removeItem('<?php echo $deletrowid; ?>')"></i>
                                                </div>
                                            </div>
                                    <?php $mks++; } ?>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-xs-12">
                                                <button class="btn btn-success"  type="button" onclick="addMoreCustomerItems()">+ Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                    <div class="mt-3">
                        
                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">  
                        <input type="hidden" name="submititem" value=""> 
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submititemform()"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>       
                    </div>
                </form>
            </div>    
        </div>    



<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="validationModalLabel">Missing Required Fields</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-bold text-danger" id="validationMessage"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="proceedAnywayBtn">Proceed Anyway</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="skuChangeModal" tabindex="-1" aria-labelledby="skuChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="skuChangeModalLabel">SKU Change Warning</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-bold text-danger" id="skuChangeMessage"></p>
                <p class="text-muted">
                    Because SKU codes are linked in multiple places, you cannot directly update these fields.<br>
                    You can either correct your selection or create a <strong>duplicate item</strong> with the new
                    values.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn btn-primary" id="duplicateItemBtn">Make Duplicate</button>
            </div>
        </div>
    </div>
</div>

