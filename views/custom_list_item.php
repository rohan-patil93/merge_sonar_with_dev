<?php
defined('URL_GET_TABLE_DETAILS') || define('URL_GET_TABLE_DETAILS', '?mode=gettabledetails&table=');
if (isset($_POST['submit']) && $_POST['submit'] == 'search') {
        $array =  array('category_id', 'vendor_id', 'cost_center_id', 'location_id');
        foreach ($array as  $value) {
            if($_POST[$value] != ''){
                $ress [$value] = $_POST[$value];
            }
        }
        $data1["where"] = $ress;
        $data = urlencode(json_encode($data1));
        $tableurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEM_ORDER_LIST."&data=".$data;
        $response = callAPI($tableurl);
        $data = json_decode($response,1);

    }

$tablecat = API.URL_GET_TABLE_DETAILS.TBL_ITEM_CATEGORIES;
$rescat = callAPI($tablecat);
$datacat = json_decode($rescat,1);
$tablecost = API.URL_GET_TABLE_DETAILS.TBL_COST_CENTERS;
$rescost = callAPI($tablecost);
$datacost = json_decode($rescost,1);
$tablelocation = API.URL_GET_TABLE_DETAILS.TBL_LOCATIONS;
$reslocation = callAPI($tablelocation);
$datalocation = json_decode($reslocation,1);
$tablevendor = API.URL_GET_TABLE_DETAILS.TBL_VENDOR;
$resvendor = callAPI($tablevendor);
$datavendor = json_decode($resvendor,1);
$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";

?>
<script type="text/javascript">
     $(document).on('click', '#checkall1', function() {
          alert(<?php echo json_encode(__('haii'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);

    });
     function chackd(e){
         alert(check);
         var checkboxes = document.getelementbyid('check');
       if (e.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = true;
         }
       } else {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = false;
         }
       }
     }

</script>

<div creport_codelass="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
                    <?php echo $content; ?>
        </div>
    </div>
        <?php } ?>
                    <div class="kt-portlet kt-portlet--mobile">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon-list-2"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    <?php echo __("Purchase Requisitions Order");  ?>
                                </h3>

                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">
                                        <div class="dropdown dropdown-inline">
                                            <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                                    <i class="la la-arrow-left"></i>
                                                    <span class="kt-hidden-mobile"><?= __('Back'); ?></span>
                                                </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">

<form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
    <div class="row kt-margin-b-20">
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Item Category"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Category'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-input" name="category_id" data-col-index="2">
                <option value=""> <?= __('All Categories'); ?> </option>
                <?php foreach($datacat as $selectcat){ ?>
                <option  <?php if($ress['category_id'] == $selectcat['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcat['id']; ?>"><?php echo $selectcat['category_description']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Cost Center"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Cost Center'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-input" name="cost_center_id" data-col-index="2">
                <option value=""> <?= __('All Cost Center'); ?> </option>
                <?php foreach($datacost as $selectcost){ ?>
                <option   <?php if($ress['cost_center_id'] == $selectcost['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcost['id']; ?>"><?php echo $selectcost['cost_center_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Location"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Location'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-input" name="location_id" data-col-index="2">
                <option value=""> <?= __('All Locations'); ?> </option>
                <?php foreach($datalocation as $seleclocation){ ?>
                <option  <?php if($ress['location_id'] == $seleclocation['id'] ){ ?> selected <?php
} ?> value="<?php echo $seleclocation['id']; ?>"><?php echo $seleclocation['location_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Vendors"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Vendor'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-input" name="vendor_id" data-col-index="2">
                <option value=""> <?= __('All Vendors'); ?> </option>
                <?php foreach($datavendor as $selectvendor){ ?>
                <option  <?php if($ress['vendor_id'] == $selectvendor['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectvendor['id']; ?>"><?php echo $selectvendor['display_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile" style="margin-top: 15px;">

                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                        <input type="hidden" name="static_list" value="no">
                        <input aria-label="<?= htmlspecialchars(__('Static List'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="static_list" value='yes' > <?= __('Reached Reorder Level'); ?>
                        <span></span>
                    </label>

        </div>

    </div>
    <div class="kt-separator kt-separator--md kt-separator--dashed"></div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-lg-12">

            <input type="hidden" name="submit" value="search" />
            <button class="btn btn-primary btn-brand--icon" id="kt_search">
                <span>
                    <i class="la la-search"></i>
                    <span><?= __('Search'); ?></span>
                </span>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                <span>
                    <i class="la la-close"></i>
                    <span><?= __('Reset'); ?></span>
                </span>
            </button>
        </div>
    </div>
</form>
<div>
    <form class="kt-form kt-form--fit kt-margin-b-20" action="index.php?option=COM_PURCHASE_REQUISITIONS" method="post">
    <div class="dropdown dropdown-inline">
                                        <input type="hidden" name="submit" value="list" />
                                        <button class="btn btn-primary btn-brand--icon">
                                            <span>
                                                <span><?= __('Genrate Requisition'); ?></span>
                                            </span>
                                        </button>
                                        </div>
</div>

                            <!--begin: Datatable -->
        <table class="table table-striped- table-bordered table-hover table-checkable" style="margin-top: 15px;" id="kt_table_1">

            <thead>
                <tr>
                    <th scope="col"><label class="kt-checkbox">
                        <input aria-label="<?= htmlspecialchars(__('Checkall1'), ENT_QUOTES, 'UTF-8'); ?>"  type="checkbox"  id="checkall1" onclick="chackd(this)" onkeydown="">  <span> </span> </label>
                    </th>
                    <th scope="col"><?= __('Item Name'); ?></th>
                    <th scope="col"><?= __('SKU'); ?></th>
                    <th scope="col"><?= __('Category'); ?></th>
                    <th scope="col"><?= __('UOM'); ?></th>
                    <th scope="col"><?= __('Reorder Qty'); ?> </th>
                    <th scope="col"><?= __('image'); ?></th>
                    <th scope="col"><?= __('QOH'); ?></th>
                </tr>
            </thead>
            <tbody>
                    <?php $i=0; foreach ($data as $row){?>
                <tr>
                    <td>
                        <input type="hidden" name="id[<?php echo $i; ?>]" value="<?php echo $row['id'] ?>">
                        <label class="kt-checkbox">
                        <input type="hidden" name="check[<?php echo $i; ?>]" value="no">
                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>"  class="itemRow" name="check[<?php echo $i; ?>]" id="check" type="checkbox" value="yes">  <span> </span> </label>
                    </td>
                    <td><?php echo $row['item_name'] ?></td>
                    <td><?php echo $row['sku'] ?></td>
                    <td><?php echo $row['category_desc'] ?></td>
                    <td><?php echo $row['uom_desc'] ?></td>
                    <td> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="qty[<?php echo $i; ?>]" value="<?php echo $row['reorder_level'] ?>"></td>
                    <td><?php echo $row['item_image'] ?></td>
                    <td><?php echo $row['qoh'] ?></td>
                </tr>
                <?php $i++;
} ?>

            </tbody>
        </table>
</form>

        <!--end: Datatable -->
    </div>
</div>
</div>
