<?php
if (isset($_POST['submit'])){
        print_r($_POST); exit;
        if($_POST['submit'] == 'Add'){
        $data= array();
        $data['sku'] = $_POST['sku'];
        $data['item_id'] = $_POST['item_id'];
        $data['specifications'] = implode(",", (array)$_POST['specifications']);
        $data['Current_Status'] = $_POST['Current_Status'];

        $url="";
        $urldata = urlencode(json_encode($data,true));
        $url = API."?mode=adddata&table=".TBL_ITEM_SPECIFICATIONS."&data=$urldata";
        $response = file_get_contents($url, false, $context);
        if ($response) {
            header("location:index.php?option=com_view_item_specifications");
        }
    }
    if($_POST['submit'] == 'Update'){
        $data= array();
        $data['sku'] = $_POST['sku'];
        $data['item_id'] = $_POST['item_id'];
        $data['specifications'] = implode(",", (array)$_POST['specifications']);
        $data['Current_Status'] = $_POST['Current_Status'];
        $urldata = urlencode(json_encode($data,true));
        $url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".TBL_ITEM_SPECIFICATIONS."&data=$urldata";
        $response = file_get_contents($url, false, $context);
        if ($response) {
            header("location:index.php?option=com_view_item_specifications");
        }
    }
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $rid =  encodeId($_GET['Rid']);
    $item_id = $rid;
    $fid = $_GET['fid']==1 ? 'item_id' : '';
    $url = API."?mode=singlerowtabledata&fid=$fid&rid=$rid&table=".TBL_ITEM_SPECIFICATIONS;
    $response = file_get_contents($url, false, $context);
    $role = json_decode($response,1);
    $permissions = json_decode($role['permissions'],1);
    $rid = $role['id'];
}
$itemurl = API."?mode=gettabledetails&table=".TPREFIX.TBL_ITEMS;
$itemresponse = file_get_contents($itemurl, false, $context);
$itemresponse = json_decode($itemresponse,1);
if ($_GET['item_id'] != ''){
    $item_id =$_GET['item_id'];
    $specurl = API."?mode=singlerowtabledata&fid=item_id&rid=$item_id&table=".TBL_ITEM_SPECIFICATIONS;
    $specresponse = file_get_contents($specurl, false, $context);
    $specresponse = json_decode($specresponse,1);
    $nums= $specresponse['specifications'];
    $atturl = API."?mode=getspcificattributes&fid=id&rid=$nums&table=".TBL_ITEM_ATTRIBUTES;
    echo $atturl; exit;
    $attrponse = file_get_contents($atturl, false, $context);
    $attrponse = json_decode($attrponse,1);
}
$title = viewTitle($_GET['option']);
?>
<script type="text/javascript">
    function GetSelectedTextValue(key) {
        window.location.href = 'index.php?option=com_item_quality_check&item_id='+key;
    }
</script>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    <?php echo $title; ?>
                </h3>
            </div>
            <div class="kt-subheader__toolbar">
            </div>
        </div>
    </div>

    <!-- end:: Content Head -->

    <!-- begin:: Content -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__body">
                <form action="index.php?option=com_result" method="post" name="myForm" onsubmit="return validateForm()" >
                    <div class="tab-content">
                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                            <div class="kt-form kt-form--label-right">
                                <div class="kt-form__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Item<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                            <select aria-label="Select option" class="form-control" id="select_qc_item" name= "<?php echo  $attdata['name']; ?>" onchange="GetSelectedTextValue(this.value)" >
                                                                <option > Select Any One </option>
                                                            <?php foreach ($itemresponse as $key => $item) { ?>
                                                            <option  <?php if( $item_id == $item['id']){ ?> selected <?php
} ?> value = <?php echo $item['id']; ?>  ><?php echo $item['name']; ?></option>

                                                            <?php } ?>
                                                            </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if  ($item_id =="") { exit;
}
                                                  if (!sizeof($attrponse)>0) {
                                                          echo '<div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label"></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <h4>    No specifications found in this item        </h4>
                                                        <span></span>
                                                    </div>
                                                </div>
                                            </div>'; exit;
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
                                                        <span class="col-xl-3 col-lg-3 col-form-label"><?php echo $attdata['name']; ?><span class="requried"></span></span>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-bolt" aria-hidden="true"></i></span></div>
                                                            <input aria-label="Input field" class="form-control" type="text" name="<?php echo  $attdata['name']; ?>" value="">
                                                        </div>
                                                    </div>
                                                    </div>
                                                <?php } elseif( $attdata['display_type']== 'dop_down'){ ?>
                                                    <div class="form-group  row">
                                                        <span class="col-xl-3 col-lg-3 col-form-label"><?php echo $attdata['name']; ?><span class="requried"></span></span>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group">
                                                               <select aria-label="Select option" class="form-control" id="select_qc_attr_<?php echo  $attdata['name']; ?>" name= "<?php echo  $attdata['name']; ?>">
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
                                                    <span class="col-3 col-form-label"><?php echo $attdata['name']; ?></span>
                                                    <div class="col-9">
                                                        <div class="kt-radio-inline">
                                                            <?php $radio_value = explode(",",  $attdata['specifications']);
                                                                foreach ($radio_value as $radio) { ?>
                                                            <label class="kt-radio">
                                                                <input aria-label="Radio option" type="radio" name="<?php echo  $attdata['name']; ?>" value = <?php echo $radio; ?> >  <?php echo $radio; ?>
                                                                <span></span>
                                                            </label>
                                                            <?php } ?>

                                                        </div>

                                                    </div>
                                                </div>
                                        <?php } ?>
                                        <?php } ?>


                                            <div class="row">
                                                <div class="col-lg-3 col-xl-3">
                                                </div>
                                                <div class="col-lg-9 col-xl-9">
                                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1' && $rid !=''){ ?>
                                                    <input type="hidden" name="hid" value="<?php echo $rid; ?>" />
                                                    <input type="hidden" name="submit" value="Update" />
                                                    <button id="kt_llogin_signup_submit" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">Upadate</button>
                                                    <button type="reset" class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Cancel</button>
                                                <?php } else{ ?>
                                                    <input type="hidden" name="submit" id="submit" value="Add" />
                                                    <button id="kt_llogin_signup_submit_2" class="btn btn-brand btn-elevate btn-pill btn-elevate-air">ADD</button>
                                                    <button type="reset" class="btn btn-warning btn-elevate btn-pill btn-elevate-air">Cancel</button>
                                                <?php } ?>
                                                </div>
                                            </div>
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

    <!-- end:: Content -->
</div>
