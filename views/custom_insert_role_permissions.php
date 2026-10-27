<?php
if (isset($_POST['submit'])){
    $data= array();
    foreach ($_POST['db'] as $postkey => $postvalue) {
        $data1[$postvalue]['table_name'] = $_POST['display_postion'][$postkey];
        $data1[$postvalue]['permission_view'] = $_POST['display_postion'][$postkey];
        $data1[$postvalue]['permission_insert'] = $_POST['display_list'][$postkey];
        $data1[$postvalue]['permission_update'] = $_POST['lable'][$postkey];
        $data1[$postvalue]['permission_delete'] = $_POST['mandatory'][$postkey];
    }
    $data_json = json_encode($data1);
    $data_json2 = json_encode($data2);
    $data['page_name'] = $_POST['page_name'];
    $data['table_name'] = $_POST['table_name'];
    $data['page_title'] = $_POST['page_title'];
    $data['insert_view'] =  $_POST['insert_view'];
    $data['file_attribute'] = $data_json;
    $data['insert_attribute'] = $data_json;
    $data['view'] = $_POST['view'];
    $data['add_url'] = $_POST['add_url'];
    $data['content'] = $_POST['content'];
    if($_POST['id'] !=''){

            $url = API."?mode=updaterowtabledata&method=post&rid=".$_POST['id']."&table=".TPREFIX.TBL_DISPLAYATTRIBUTES;
    } else{
            $url = API."?mode=adddata&method=post&table=".TPREFIX.TBL_DISPLAYATTRIBUTES;
    }
    $response = file_post_contents($url, $data);
    if ($response) {
        header("location:index.php?option=com_view_display_attributes&error=3010");
    }

}
if((isset($_GET['id']) && $_GET['id'] !='') || $_GET['Rid']){
        $role_id = $_GET['id'];
        $tableurl = API."?mode=displayrolepermission&rid=$role_id";
        $response = callAPI($tableurl);
        $roles = json_decode($response,1);
        $table = $table!="" ? $table : $role['table_name'];
}
$tdurl = API."?mode=gettabledetails&table=".TBL_ROLES;
$showtablesdata =callAPI($tdurl);
$show = json_decode($showtablesdata,1);
$title = viewTitle($_GET['option']);

?>
<script type="text/javascript">
    function GetSelectedTextValue(key) {
        window.location.href = 'index.php?option=COM_INSERT_ROLE_PERMISSIONS&id='+key;
    }
     function viewcheckalldata(val){
        if ($('#'+val).prop("checked")) {
            $('.'+val).prop("checked", true);
        } else {
            $('.'+val).prop("checked", false);
        }
    }
</script>
<div class="d-flex flex-column-fluid">

    <div class="container-fluid">
    <?php if($content !=''){ ?>
        <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text"><?php echo "Help Text"; ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>

<?php } ?>
<div class="row">
    <div class="col-lg-12">
        <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
        <!--begin::Portlet-->
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">

                                <div style="float: left;">
                                    <span style="margin-top: 10px;"> <?php echo $title; ?><span class="requried">*</span> &nbsp;&nbsp; </span>

                               </div>
                              <div style="float: left;">
                               <select aria-label="Display List" class="form-control" id="kt_select2_11" name= "display_list" onchange="GetSelectedTextValue(this.value)">
                                           <option> Select Role</option>
                                           <?php  foreach ($show as $key => $data) { ?>
                                               <option  <?php if($role_id == $data['id']){ ?> selected  <?php
} ?> value = <?php echo $data['id'];  ?>   > <?php echo ucwords($data['name']); ?> </option>
                                       <?php    } ?>
                                </select>
                             </div>
                    </div>

                <div class="card-toolbar">
                    <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button> &nbsp;
                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-4">
                        <span>Table Name</span>
                    </div>
                    <div class="col-lg-2">
                        <div>
                            <span>  View</span>
                            <div>
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        <input aria-label="Viewchk" type="checkbox" id='viewchk'   onclick='viewcheckalldata(this.id)' onkeydown="">
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                            <span class=""> Insert</span>
                            <div>
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        <input aria-label="Insertchk" type="checkbox" id='insertchk'   onclick='viewcheckalldata(this.id)' onkeydown="">
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                    </div>
                    <div class="col-lg-2">
                        <span class=""> Update </span>
                        <div>
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        <input aria-label="Updatechk" type="checkbox" id='updatechk'   onclick='viewcheckalldata(this.id)' onkeydown="">
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <span class="">   Delete</span>
                        <div>
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        <input aria-label="Deletechk" type="checkbox" id='deletechk'   onclick='viewcheckalldata(this.id)' onkeydown="">
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                </div>
                <?php foreach ($roles as $key => $value) {  ?>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <input aria-label="Db[]" type="text" class="form-control" readonly name="db[]" value=<?php echo $key;?> >
                            </div>

                            <div class="col-lg-2">
                                <div class="col-3">
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            <input aria-label="View[]" type="checkbox"  name="view[]" class='viewchk'  <?php if($value['permission_view'] == 1){ ?> checked <?php
} ?>  >
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="col-3">
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            <input aria-label="Insert[]" type="checkbox"  name="insert[]"  class="insertchk" <?php if($value['permission_insert'] == 1){ ?> checked <?php
} ?> >
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="col-3">
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            <input aria-label="Update[]" type="checkbox"  name="Update[]" class="updatechk"  <?php if($value['permission_update'] == 1){ ?> checked <?php
} ?>>
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="col-3">
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            <input aria-label="Delete[]" type="checkbox" class="deletechk" name="delete[]"  <?php if($value['permission_delete'] == 1){ ?> checked <?php
} ?> >
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
                                            <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                            <input type="hidden" name="submit" value="Update" />
                                            <button id="kt_llogin_signup_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" ><i class="la la-check"></i>Upadate</button>
                                            <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                                    <?php } else{ ?>
                                            <input type="hidden" name="submit" value="Add" />
                                            <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                                            <button id="kt_login_signin_submit_3" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                                            <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-6 text-right">
                                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
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
