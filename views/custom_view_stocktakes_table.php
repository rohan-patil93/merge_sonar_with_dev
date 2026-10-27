<?php
 $_SESSION['apply_js']['multiselest'] = "1";
$filter_veriances = $_POST['filter_veriances']!=""?$_POST['filter_veriances']:'all';
if(isset($_POST['sid'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $filter_location = $_POST['filter_location']!=""?json_encode(explode(',',$_POST['filter_location'])):"null";
    $rid= $_POST['sid'];
    $url = API."?mode=getstocktakes&Rid=".$rid."&stoctype=list&filter_location=".$filter_location."&filter_veriances=0";
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);
    $stocktakes = $details['stocktakes'];
    $location_nameres = $details['locations'];
    $itemlists= $details['item_list'];
    $stockitems = array();
    foreach($details['stockitems'] as $stockim){
        $stockitems[$stockim['location_id']][$stockim['item_id']] =$stockim;
    }
    $locationitemavailability = $details['locationitemavailability'];
    foreach($location_nameres as $location){
        $location_name[$location['id']] =$location;
    }

}

?>
<style type="text/css">

</style>

<div class="dt-responsive table-responsive">
    <table id="new-cons" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Location</th>
                <th scope="col">Item</th>
                <th scope="col">Attributes</th>
                <th scope="col">UOM</th>
                <th scope="col">Physical Qty</th>
                <?php if($stocktakes['status'] == '2' || $stocktakes['status'] == '3'){ ?>
                    <th scope="col">System Qty</th>
                    <th scope="col">Variance</th>
                    <?php if($stocktakes['status'] == '2'){?>
                        <th scope="col"><div class="checkbox-fade fade-in-primary">
                            <label class="checkbox checkbox-success">
                                <input aria-label="Ectall Checks" type="checkbox" id="selectall_checks" class="itemRow" onclick="selectallcheck()" onkeydown="">
                                <span class="cr">
                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                            </label> All
                        </div></th>
                    <?php } ?>
                <?php } ?>
                <?php if($stocktakes['status'] == '3'){ ?>
                    <th scope="col">Now System Qty</th>
                    <th scope="col">Action</th>
                <?php } ?>

            </tr>
        </thead>
        <tbody>
            <?php
            if($stocktakes['status'] == '2' || $stocktakes['status'] == '3'){
                $rextboxdis = 'disabled';
                $reedonly = 'readonly';
            }
            $incnt = 1;    $in=0; foreach($itemlists as $itemlist){
                $availableqty = $locationitemavailability[$itemlist['default_costing_type']][$itemlist['location_id']][$itemlist['id']]['available'];
                $additional_attributes = json_decode($itemlist['additional_attributes'],1);
                $stockitemdetail = $stockitems[$itemlist['location_id']][$itemlist['id']];
                $sumamt = $stockitemdetail['count_qty']-$availableqty;
                if($sumamt['status'] == '2'){
                    if($sumamt < 0){
                        $varianc_class = "cls_sub";
                        $varianc_filt = 0;
                    }elseif($sumamt > 0){
                        $varianc_class = "cls_add";
                        $varianc_filt = 0;
                    }else{
                        $varianc_class = "cls_zero";
                        $varianc_filt = 1;
                    }
                }
                if($stocktakes['status'] == '3'){
                    if($stockitemdetail['variance'] < 0){
                        $varianc_class = "cls_sub";
                        $varianc_filt = 0;
                    }elseif($stockitemdetail['variance'] > 0){
                        $varianc_class = "cls_add";
                        $varianc_filt = 0;
                    }else{
                        $varianc_class = "cls_zero";
                        $varianc_filt = 1;
                    }
                }
                if($filter_veriances !="all" && $filter_veriances != $varianc_class){

                }else{
                    ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td><?php echo $incnt; ?></td>
                        <td><?php echo $location_name[$itemlist['location_id']]['location_name']; ?></td>
                        <td><?php echo $itemlist['item_name']; ?></td>
                        <td><?php if(count((array)$additional_attributes)=='0'){ echo "-";
}else{foreach($additional_attributes as $aatrr){ ?>
                            <span class="form-label label label-warning"><?php echo $aatrr; ?></span>
                        <?php } ?>
                        <?php } ?>

                    </td>
                    <td><span class="form-label badge badge-inverse-primary"><?php echo $itemlist['code']; ?></span>
                    </td>
                    <td>
                        <input type="hidden" name="uom_details[]" id="uom_details_<?php echo $in; ?>" value='<?php echo $stockitemdetail['uom_details']; ?>'>
                        <input type="hidden" name="item_id[]"  value="<?php echo $itemlist['id']; ?>">
                        <input type="hidden" name="location_id[]"  value="<?php echo $itemlist['location_id']; ?>">
                        <input type="hidden" name="system_qty[]"  value="<?php echo $availableqty; ?>">
                        <input type="hidden" name="sale_price[]"  value="<?php echo $itemlist['sale_price']; ?>">
                        <input type="hidden" name="purchase_price[]"  value="<?php echo $itemlist['purchase_price']; ?>">
                        <input type="hidden" name="available_qty[]"  value="<?php echo $sumamt; ?>">
                        <input type="hidden" name="stocktake_items_id[]"  value="<?php echo $stockitemdetail['id']; ?>">
                        <input type="hidden" name="submited" value="count_complete">

                        <div class="input-group input-group-button mb-0" style="width: 100px;">
                            <input aria-label="Count Qty[]" type="text" class="form-control width1 text-right" name="count_qty[]" id="count_qty_<?php echo $in; ?>" value="<?php echo $stockitemdetail['count_qty']; ?>" <?php echo $reedonly; ?>>

                            <button type="button" class="input-group-text addon-style" onclick="uompopup('<?php echo $itemlist['id']; ?>','<?php echo $itemlist['purchase_uom']; ?>','<?php echo $itemlist['code']; ?>','<?php echo $itemlist['location_id']; ?>','<?php echo $in; ?>')" <?php echo $rextboxdis; ?>>
                                <span class=""><i class="fas fa-caret-down f-18 "></i></span>
                            </button>
                        </div>
                        <div class="table-popup popup-uom-<?php echo $in; ?>">

                        </div>
                    </td>
                    <?php if($stocktakes['status'] == '2'){ ?>
                        <td class="text-right">
                            <?php echo $availableqty;  ?>
                        </td>
                        <td class="text-right">
                            <?php
                            if($sumamt < 0){
                                echo '<span class="form-label label label-danger f-14">'.$sumamt.'</span>';
                            }elseif($sumamt > 0){
                                echo '<span class="form-label label label-warning f-14">'.$sumamt.'</span>';
                            }else{
                                echo '<span class="form-label label label-success f-14">'.$sumamt.'</span>';
                            }

                            ?>
                        </td>
                        <td class="text-center">
                            <div class="checkbox-fade fade-in-primary" >
                                <label class="checkbox checkbox-success">
                                    <input aria-label="Checkbox" type="checkbox" class="itemRow checkbox2" name="substoktakecheck[<?php echo $in; ?>]"  value="0">
                                    <span class="cr">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                    <?php }if($stocktakes['status'] == '3'){

                        $stock_adjusted      = "-";
                        if($stockitemdetail['update_balance']=="Y"){
                            $stock_adjusted      = "Adjusted";
                        }
                        ?>
                        <td class="text-right">
                            <?php echo $stockitemdetail['system_qty'];  ?>
                        </td>

                        <?php
                        if($stockitemdetail['variance'] < 0){
                            echo '<td class="text-right"><span class="form-label label label-danger f-14">'.$stockitemdetail['variance'].'</span></td><td class="text-right">'.$availableqty.'</td><td>'.$stock_adjusted.'</td> ';
                        }elseif($stockitemdetail['variance'] > 0){
                            echo '<td class="text-right"><span class="form-label label label-warning f-14">'.$stockitemdetail['variance'].'</span></td><td class="text-right">'.$availableqty.'</td><td>'.$stock_adjusted.'</td> ';
                        }else{
                            echo '<td class="text-right"><span class="form-label label label-success f-14">'.$stockitemdetail['variance'].'</span></td><td class="text-right">'.$availableqty.'</td><td>'.$stock_adjusted.'</td>';
                        }
                        ?>
                    <?php } ?>
                </tr>
                <?php $in++; $incnt++; ?>
                <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

<?php if(isset($_POST['sid'])){ ?>
    <!-- nw adde table -->
<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
<!-- end adde table -->
   <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
<?php } ?>
