<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getitemsearch";
$apires = callAPI($url);
$result = json_decode($apires,1);
$item_types = $result['item_types'];
$item_categories = $result['item_categories'];
$locations = $result['locations'];
$warehouses = $result['warehouses'];
$vendors = $result['vendors'];
$customers = $result['customers'];
$statuse = ViewStatusData();
?>
<script type="text/javascript">
    function submitItemSearch(){
        var postarray = $("#itemserach_form").serialize();
         $.ajax({
            url: "views/custom_list_item_search_table.php",
            type: "POST",
            data: 'submitsearchform=Y&'+postarray,
            success: function(response) {
                console.log(response);
                $("#searchitemresults").html(response);
            }
        });
    }
    function getUserVendorCustomerItemList(usertype,item_id){
        $('#kt_modal_dynamic_popup').modal("show");
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popup1").html(loader);
        $.ajax({
            url: "views/custom_list_item_search_popup.php",
            data:'usertype='+usertype+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
            }
        });
    }
</script>
<div class="card">
    <div class="card-header table-card-header">
          <h5>Item Search</h5>
          <div class="card-header-right table-card-header">
          </div>
      </div>
      <div class="card-body">
          <form id="itemserach_form">
              <div class="row">
                  <div class="form-group col-lg-3">
                    <span class="form-label">Item Name/ SKU Code</span>
                     <input aria-label="Item Name Sku Code" class="form-control" type="text" name="item_name_sku_code" value="">
                </div>
                   <div class="form-group col-lg-3">
                    <span class="form-label">Item Type </span>
                    <select aria-label="Item Type" class="form-select border js-example-basic-single" name="item_type">
                        <option value=""> Select Any one</option>
                        <?php foreach ($item_types as $item_type) { ?>
                            <option value="<?php echo $item_type['id']; ?>"><?php echo $item_type['item_type_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                   <div class="form-group col-lg-3">
                    <span class="form-label">Category</span>
                    <select aria-label="Category" class="form-select border js-example-basic-single" name="category_id">
                        <option value="">Select Any one</option>
                          <?php foreach ($item_categories as $category) { ?>
                            <option value="<?php echo $category['id']; ?>" ><?php echo $category['category_description'] ?></option>;
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label">Location</span>
                    <select aria-label="Location" class="form-select border js-example-basic-single"  name="location_id">
                        <option value="">Select Any one</option>
                          <?php foreach ($locations as $locc) { ?>
                            <option value="<?php echo $locc['id']; ?>" ><?php echo $locc['location_name'] ?></option>;
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label">Warehous</span>
                    <select aria-label="Warehouse" class="form-select js-example-basic-single" name="warehouse_id" >
                        <option value=""> Select Any one</option>
                        <?php foreach($warehouses as $waro){ ?>
                            <option value="<?php echo $waro['id']; ?>" ><?php echo $waro['warehouse_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label">Vendor</span>
                    <select aria-label="Vendor" class="form-select js-example-basic-single" name="vendor_id" >
                        <option value=""> Select Any one</option>
                        <?php foreach($vendors as $ven){ ?>
                            <option value="<?php echo $ven['id']; ?>" ><?php echo $ven['company']."-".$ven['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label">Customer</span>
                    <select aria-label="Customer" class="form-select js-example-basic-single" name="customer_id" >
                        <option value=""> Select Any one</option>
                        <?php foreach($customers as $cust){ ?>
                            <option value="<?php echo $cust['id']; ?>" ><?php echo $cust['company']."-".$cust['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <span class="form-label d-block">&nbsp</span>
                    <button class="btn btn-primary form-control" type="button" onclick="submitItemSearch()"><i class="ti-search m-r-2"></i>Search</button>
                </div>
             </div>
         </form>
         <div class="table-responsive" id ="searchitemresults">
             <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> Please Search</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
         </div>
     </div>
</div>
