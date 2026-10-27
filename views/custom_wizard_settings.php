
<?php
if(isset($_POST['submit'])){
  $_POST['created_by'] = $_SESSION['userid'];
  $_POST['created_date'] = nowDate();
  $url = API."?mode=addorupdatewizardsettings&method=post";
  $response = file_post_contents($url, $_POST, 'insert');

}

$data2["where"] = array('is_active'=> '0');
$costdata = urlencode(json_encode($data2));
$typeurl = API."?mode=gettabledetails&fid=genc_code&table=application_widget_groups&data=".$costdata;
$typeresponsept = callAPI($typeurl);
$posresponsept = json_decode($typeresponsept,1);
$costurl = API."?mode=gettabledetailswithconditions&table=application_widgets&data=".$costdata;
$costdata = callAPI($costurl);
$costress = json_decode($costdata, 1);
$appwizwr["where"] = array('is_active'=> '0','user_id'=>$_SESSION['userid']);
$appwizcon = urlencode(json_encode($appwizwr));
$appwizurl = API."?mode=gettabledetailswithconditions&table=application_user_widgets&data=".$appwizcon;
$appwizdata = callAPI($appwizurl);
$appwizdata = json_decode($appwizdata, 1);
foreach ($appwizdata as  $value) {
    $acceptwz[] = $value['widget_id'];
}
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
        <div class="alert alert-warning alert-dismissible">
            <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        <ul class="nav md-tabs">
                            <?php
                            $i=1;
                            foreach ($posresponsept as  $navdata) { ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if ($i==1){ ?> active <?php
} ?> " data-bs-toggle="tab" href="#kt_tabs_1_<?php echo $navdata['id']; ?>">
                                         <?php echo $navdata['group_name']; ?>
                                    </a>
                                </li>
                                <?php $i++;
}  ?>
                            </ul>
                        </div></div>
                        <form class="kt-form" id="kt_form" method="post" action=""  name="myForm" enctype="multipart/form-data">
                            <div class="tab-content">

                                <?php
                                $j=1;
                                foreach ($posresponsept as  $navdata) { ?>
                                    <div class="tab-pane <?php if ($j==1){ ?> active <?php
} ?>" id="kt_tabs_1_<?php echo $navdata['id']; ?>" role="tabpanel">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <?php foreach ($costress as $cost) { if($cost['widget_group']== $navdata['id']){ ?>
                                                  <div class="form-group  col-lg-4">
                                                    <span><?php echo $cost['widget_name']; ?></span>
                                                    <div class="">
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>

                                                                <input aria-label="Checkbox" type="checkbox" <?php if(in_array($cost['id'], $acceptwz)){ ?> checked <?php
} ?> value="<?php echo $cost['id']; ?>" class="js-switch"  name="name[]" >
                                                                <span></span>
                                                            </label>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $j++;
} ?>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <input type="hidden" name="submit" value="Add" />
                                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save</button>
                                            <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

