<?php
if (isset($_POST['submit'])){
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
$attributesurl = API."?mode=gettabledetails&table=".TBL_ITEM_ATTRIBUTES;
$attributesresponse = file_get_contents($attributesurl, false, $context);
$attributesresponse = json_decode($attributesresponse,1);
$title = viewTitle($_GET['option']);
$stausresult = statusData(TBL_ACCOUNTGROUPS);
$sku = $_GET['sku'] !='' ? $_GET['sku'] : $role['sku'];

$item_id = $_GET['fid'] ==1 ? $item_id : $role['item_id'];
?>
<script type="text/javascript">
    function validateForm(){
        var error = <?php echo json_encode($error_data); ?>;
        var x = document.forms["myForm"]["Group_name"].value;
          if (x == "") {
                alert(error[1001]);
                return false;
          }
          var y = document.forms["myForm"]["TB_Seq"].value;
          if (y == "") {
                alert(error[1002]);
                return false;
          }
          var z = document.forms["myForm"]["BS_Seq"].value;
          if (z == "") {
               alert(error[1004]);
                return false;
          }
          var m = document.forms["myForm"]["AccSec_Section_ID"].value;
          if (m == "") {
                alert(error[1005]);
                return false;
          }
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
                <form action="" method="post" name="myForm" onsubmit="return validateForm()" >
                    <div class="tab-content">
                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                            <div class="kt-form kt-form--label-right">
                                <div class="kt-form__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">

                                            <div class="form-group row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Specifications<span class="requried"></span></span>
                                                <div class="col-lg-6">
                                                    <div class="kt-portlet">
                                                        <div class="kt-portlet__body">
                                                            <select aria-label="Specifications[]" id="kt-dual-listbox-1" class="kt-dual-listbox" name="specifications[]" multiple>
                                                                <?php $spc = explode(",",$role['specifications']);
                                                                    foreach ($attributesresponse as $key => $attribut) { ?>
                                                                    <option  <?php if(in_array( $attribut['id'], $spc)){ ?> selected <?php
} ?> value="<?php echo $attribut['id'] ?>"><?php echo $attribut['name'] ?></option>
                                                                <?php } ?>

                                                            </select>
                                                            <input type="hidden" name="sku" value="<?php echo $sku; ?>">
                                                            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-sm-12">Status</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <?php foreach($stausresult as $key=>$radio){ ?>
                                                    <label class="kt-radio">
                                                        <input aria-label="Radio option" type="radio" <?php if($role['Current_Status'] == $key){?> checked="checked" <?php
} ?> value="<?php echo $key; ?>"  name="Current_Status"> <?php echo $radio; ?>
                                                        <span></span>
                                                    </label>
                                                <?php } ?>
                                                </div>
                                            </div>
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
