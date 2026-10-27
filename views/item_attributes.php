<?php
if (isset($_POST['submit'])){
        if($_POST['submit'] == 'Add'){
        $data= array();
        $data['name'] = $_POST['name'];
        $data['data_type'] = $_POST['data_type'];
        $data['specifications'] = $_POST['specifications'];
        $data['display_type'] = $_POST['display_type'];
        $data['Current_Status'] = $_POST['Current_Status'];
        $data['Status_Date'] = nowDate();
        $data['Created_Date'] = nowDate();
        $data['From_Date'] = dateFormate($_POST['From_Date']);
        $data['To_Date'] = dateFormate($_POST['To_Date']);
        $data['Created_By'] = $_SESSION['userid'];
        $url="";
        $urldata = urlencode(json_encode($data,true));
        $url = API."?mode=adddata&table=".TBL_ITEM_ATTRIBUTES."&data=$urldata";
        $response = file_get_contents($url, false, $context);
        if ($response) {
            header("location:index.php?option=com_view_item_attributes");
        }
    }
    if($_POST['submit'] == 'Update'){
        $data= array();
        $data['name'] = $_POST['name'];
        $data['data_type'] = $_POST['data_type'];
        $data['specifications'] = $_POST['specifications'];
        $data['display_type'] = $_POST['display_type'];
        $data['Current_Status'] = $_POST['Current_Status'];
        $data['Status_Date'] = nowDate();
        $data['From_Date'] = dateFormate($_POST['From_Date']);
        $data['To_Date'] = dateFormate($_POST['To_Date']);
        $data['Updated_Date'] = nowDate();
        $data['Updated_By'] = $_SESSION['userid'];
        $urldata = urlencode(json_encode($data,true));
        $url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".TBL_ITEM_ATTRIBUTES."&data=$urldata";
        $response = file_get_contents($url, false, $context);
        if ($response) {
            header("location:index.php?option=com_view_item_attributes");
        }
    }
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $rid =  encodeId($_GET['Rid']);
    $url = API."?mode=singlerowtabledata&rid=$rid&table=".TBL_ITEM_ATTRIBUTES;
    $response = file_get_contents($url, false, $context);
    $role = json_decode($response,1);
    $permissions = json_decode($role['permissions'],1);
}
$title = viewTitle($_GET['option']);
$stausresult = statusData(TBL_ACCOUNTGROUPS);
$itemAttributesDisplayType = itemAttributesDisplayType();
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
                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Name<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-comment" aria-hidden="true"></i></span></div>
                                                        <input aria-label="Name" class="form-control" type="text" name="name" id="name" value="<?php echo $role['name']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Data Type<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-bolt" aria-hidden="true"></i></span></div>
                                                        <input aria-label="Data Type" class="form-control" type="text" name="data_type" value="<?php echo $role['data_type']; ?>">
                                                        <span></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Specifications<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-comment" aria-hidden="true"></i></span></div>
                                                        <input aria-label="Specifications" class="form-control" type="text" name="specifications" value="<?php echo $role['specifications']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Display Type<span class="requried"></span></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <select aria-label="Display Type" class="form-control" id="exampleSelect1" name= "display_type">
                                                        <?php foreach ($itemAttributesDisplayType as $key => $display_type) {?>

                                                        <option  <?php if( $role['display_type'] == $key){ ?> selected <?php
} ?> value = <?php echo $key; ?>  ><?php echo $display_type; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <?php echo dates(); ?>
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
                                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
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
