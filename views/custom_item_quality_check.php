<?php
$_SESSION['apply_js']['multiselest'] = '1';

$itemurl = API."?mode=gettabledetails&table=".TBL_ITEMS;
$itemresponse = callAPI($itemurl);
$itemresponse = json_decode($itemresponse,1);
if ($_GET['Rid'] != '' || $_GET['item_id'] != '' ){
    $rid =  encodeId($_GET['Rid']);
    $item_id = $rid;
    $item_id = $item_id!="" ? $item_id : $_GET['item_id'];
    $trn_id = $_GET['TRNID'];
    $specurl = API."?mode=singlerowtabledata&rid=$item_id&table=".TBL_ITEMS;
    $specresponse = callAPI($specurl);
    $specresponse = json_decode($specresponse,1);
    $datanums= $specresponse['specifications'];
    $listnums = json_decode($datanums,1);
    $nums = implode(",", (array)$listnums);
    if(count((array)$listnums)>0){
        $atturl = API."?mode=getspcificattributes&fid=id&rid=$nums&table=".TBL_ITEM_ATTRIBUTES;
        $attrponse = callAPI($atturl);
        $attrponse = json_decode($attrponse,1);
        $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'transaction_table'=>'goods_receipt_notes');
        $condi = urlencode(json_encode($wh1));
        $pages = API."?mode=gettabledetailswithconditions&table=item_serial_nos&data=".$condi;
        $page_list = callAPI($pages);
        $res = json_decode($page_list,1);
    }
}
$title = viewTitle($_GET['option']);
?>
<script type="text/javascript">
    function GetSelectedTextValue(key) {
        window.location.href = 'index.php?option=com_item_quality_check&item_id='+key;
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5><?php echo $title;  ?> </h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-custom">
                <div class="card-body">
                    <form action="index.php?option=com_result" method="post" name="myForm" onsubmit="return validateForm()" >
                        <div class="tab-content">
                            <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                <div class="kt-form kt-form--label-right">
                                    <div class="kt-form__body">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">
                                                <div class="form-group  row">
                                                    <span class="col-xl-3 col-lg-3 col-form-label">Sieral<span class="requried"></span></span>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group">
                                                            <select aria-label="Item" class="form-control js-example-basic-single" id="select_qc_item" name= "item_id" onchange="GetSelectedTextValue(this.value)" >
                                                                <option > Select Any One </option>
                                                                <?php foreach ($itemresponse as $key => $item) { ?>
                                                                    <option  <?php if( $item_id == $item['id']){ ?> selected <?php
} ?> value = <?php echo $item['id']; ?>  ><?php echo $item['item_name']; ?></option>

                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if (!sizeof((array)$attrponse)>0) {
                                                    echo '<div class="form-group  row">
                                                    <span class="col-xl-3 col-lg-3 col-form-label"></span>
                                                    <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                    <h4>    No specifications found in this item        </h4>
                                                    <span></span>
                                                    </div>
                                                    </div>
                                                    </div>';
                                                }
                                                ?>
                                                <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Sku<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-bolt" aria-hidden="true"></i></span></div>
                                                        <input aria-label="Sku" class="form-control" type="text" name="sku" value="<?php echo $specresponse['sku']; ?>">
                                                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                                        <input type="hidden" name="att_ids" value="<?php echo $nums; ?>">
                                                        <input type="hidden" name="spec_id" value="<?php echo $specresponse['id']; ?>">
                                                        <span></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            foreach ($attrponse as $attdata) { ?>
                                                <?php if ( $attdata['display_type']== 'text') { ?>
                                                    <div class="form-group  row">
                                                        <span class="col-xl-3 col-lg-3 col-form-label"><?php echo $attdata['attribute_name']; ?><span class="requried"></span></span>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend"><span class="input-group-text"><i class="la la-bolt" aria-hidden="true"></i></span></div>
                                                                <input aria-label="Input field" class="form-control" type="text" name="<?php echo  $attdata['attribute_name']; ?>" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } elseif( $attdata['display_type']== 'dop_down'){ ?>
                                                    <div class="form-group  row">
                                                        <span class="col-xl-3 col-lg-3 col-form-label"><?php echo $attdata['attribute_name']; ?><span class="requried"></span></span>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <div class="input-group">
                                                                <select aria-label="Select option" class="form-control" id="select_qc_attr_<?php echo $attdata['attribute_name']; ?>" name= "<?php echo  $attdata['attribute_name']; ?>">
                                                                    <?php $dispay_value = explode(",",  $attdata['specifications']);
                                                                    foreach ($dispay_value as $key => $value) { ?>
                                                                        <option  <?php if( $role['value'] == $value){ ?> selected <?php
} ?>value = <?php echo $value; ?>  ><?php echo $value; ?></option>

                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } elseif( $attdata['display_type']== 'radio'){ ?>
                                                    <div class="form-group row">
                                                        <span class="col-3 col-form-label"><?php echo $attdata['attribute_name']; ?></span>
                                                        <div class="col-9">
                                                            <div class="kt-radio-inline">
                                                                <?php $radio_value = explode(",",  $attdata['specifications']);
                                                                foreach ($radio_value as $radio) { ?>
                                                                    <label class="kt-radio">
                                                                        <input aria-label="Radio option" type="radio" name="<?php echo  $attdata['attribute_name']; ?>" value = <?php echo $radio; ?> >  <?php echo $radio; ?>
                                                                        <span></span>
                                                                    </label>
                                                                <?php } ?>

                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php } ?>
