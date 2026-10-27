<?php
if($_POST['ajax_choose']=='Y'){
    include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $url = API."?mode=togetofficemanagement&location_id=".$_POST['loc_id']."&type=2&admin_type=".$_SESSION['admin_type']."&cat_id=".$_POST['cat_id']."&search=".urlencode($_POST['search']);
    $data_details = callAPI($url);
    $data = json_decode($data_details,1);
    $sitem = $data['sitem'];
}
?>
<div class="" id="stationeryitem_view" >
<div class="row mt-3" id="stationeryitem" >
    <?php
        if(count((array)$data['stationery_items'])>0){
        foreach((array)$data['stationery_items'] as $stationery_items){
        $sqty = $sitem[$stationery_items['id']]!="" ? $sitem[$stationery_items['id']] : 0;
        $stisitem[$stationery_items['id']] = $stationery_items;
        ?>
        <input type="hidden" name="item_name_<?php echo $stationery_items['id']; ?>" id="item_name_<?php echo $stationery_items['id']; ?>" value="<?php echo $stationery_items['item_name']; ?>">
        <input type="hidden" name="available_qty_<?php echo $stationery_items['id']; ?>" id="available_qty_<?php echo $stationery_items['id']; ?>" value="<?php echo $stationery_items['available_qty']; ?>">
        <div class="col-lg-4 col-md-4 sol-sm-4 col-12 m-b-15">
            <div class="pro-dsc-sec border p-2 pt-2">
                <div class="pro-img">
                    <?php if($stationery_items['image']!=""){ ?>
                        <img src="uploads/all/<?php echo $stationery_items['image'] ?>" class="img-fluid" alt="">
                    <?php } ?>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="f-12 text-primary f-w-600 text-uppercase mb-0 m-l-5"><?php echo $stationery_items['item_name'] ?></h4>
                    <?php if($_SESSION['admin_type'] == 1){ ?>
                        <button type="button" class="btn btn-link p-0 text-primary" title="Edit Item" onclick="editstationeryitem('<?php echo $stationery_items['id']; ?>')">
                            <i class="ti-pencil-alt"></i>
                        </button>
                    <?php } ?>
                </div>
                <h3 class="f-16 f-w-600 m-l-10 m-b-15"><?php echo $stationery_items['description'] ?></h3>
                <div class="row align-items-center mb-2">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                        <span class="m-l-5 mb-0">Qty:</span>
                        <span class="mb-0"><?php echo $stationery_items['available_qty']; ?></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                        <div class="text-right m-r-5 quantity-sec">
                            <div class="input-group mb-0">
                              <b class="input-group-btn position-static">
                                  <button type="button" class="quantity-left-minus btn btn-danger btn-number p-1" onclick="addmins('mins',<?php echo $stationery_items['id'] ?>)">
                                    <i class="ti-minus m-0"></i>
                                </button>
                            </b>
                            <input aria-label="Quantity" type="text"  name='quantity' id="qty_<?php echo $stationery_items['id'] ?>" value='<?php echo $sqty; ?>'  onblur="addqty(<?php echo $stationery_items['id'] ?>)"  class="form-control input-number text-center" min="1" max="100">
                            <b class="input-group-btn position-static">
                              <button type="button" class="quantity-right-plus btn btn-success btn-number p-1" onclick="addmins('add',<?php echo $stationery_items['id'] ?>)" >
                                  <i class="ti-plus m-0"></i>
                              </button>
                          </b>
                      </div>
                  </div>
              </div>
          </div>

           </div>
       </div>
   <?php } ?>
    <?php }else{?>
        <div class="col-lg-12 col-md-4 sol-sm-4 col-6 m-t-25 f-24 text-danger text-center">
            No Data Found
        </div>
    <?php } ?>
</div>
</div>
