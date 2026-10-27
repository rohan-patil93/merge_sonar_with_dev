<?php
if (isset($_POST['submit'])){
        $ext=explode(".",$_FILES["file"]['name']);
        $file_name = $ext[0];
        $file_path='uploads/product/file'.'_'.time().uniqid().'.'.$ext[1];
        move_uploaded_file($_FILES["file"]["tmp_name"],$file_path);
        $fp = fopen($file_path, "r");
        $content = fread($fp, filesize($file_path));
        if (($h = fopen($file_path, "r")) !== false) {
            while ($content1 = fgetcsv($h, 1000, ',')) {
                $testdata[]= $content1;
            }
            fclose($h);
        }
        $i=0;
        $feet =array('item_code'=>'Item Code','rate'=>'Rate (without GST)','hsn_code'=>'HSN Code','gst'=>'GST','main_color'=>'Saree Color','sub_color'=>'Blouse Color','main_fabric'=>'Saree Fabric','sub_fabric'=>'Blouse Fabric','work'=>'Work','main_size'=>'Saree Size','sub_size'=>'Blouse Size','wash'=>'Wash','note'=>'Note','time_to_ship'=>'Time To Ship','description'=>'Description');
        $ff = array('Item Code'=>'item_code','Rate (without GST)'=>'rate','HSN Code'=>'hsn_code','GST'=>'gst','Saree Color'=>'main_color','Blouse Color'=>'sub_color','Saree Fabric'=>'main_fabric','Blouse Fabric'=>'sub_fabric','Work'=>'work','Saree Size'=>'main_size','Blouse Size'=>'sub_size','Wash'=>'wash','Note'=>'note','Time To Ship'=>'time_to_ship','Description'=>'description');
        $ss = array('Item Code','Rate (without GST)','HSN Code','GST','Saree Color','Blouse Color','Saree Fabric','Blouse Fabric','Work','Saree Size','Blouse Size','Wash','Note','Time To Ship','Description');

        $k=0;
        foreach ($testdata[1] as  $names) {
            if(in_array($names, $ss)){
                $data_names[$k]=$ff[$names];
            }else{
                $data_names[$k]=$names;
            }
        $k++;
}
        foreach ($testdata as $key => $value) {
            if($i>1 && $value[0]!=''&& $value[2]!=''&& $value[3]!=''){
                $maimdata[] = $value;
            }
        $i++;
}
        $lasdata['names']= $data_names;
        $lasdata['data']= $maimdata;
        $lasdata['vendor_id']= '3';
        $url = API."?mode=productupload&method=post";
    $response = file_post_contents($url, $lasdata, 'insert');
        print_r($lasdata); exit;
}
$tablevendor = API."?mode=gettabledetails&table=".TBL_VENDOR;
$resvendor = callAPI($tablevendor);
$datavendor = json_decode($resvendor,1);
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" style="margin-top: 20px;">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
            <?php echo $content; ?>
        </div>
    </div>
<?php }
if($_GET['error'] !=''){ ?>
    <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">    <?php echo $_SESSION['error_message'][$_GET['error']]; ?>    </div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php } ?>
    <div class="row">
        <div class="col-lg-12">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile kt-form kt-form--label-right" id="kt_page_portlet">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand <?php echo $icon; ?>"></i>
                                </span>
                        <h3 class="kt-portlet__head-title">Product File Upload </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                            <i class="la la-arrow-left"></i>
                            <span class="kt-hidden-mobile">Back</span>
                        </a>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <span>Vender&nbsp;</span>
                            <select aria-label="Select option" class="form-control kt-select2" id="kt_select2_4<?php echo $select; ?>" name="<?php echo $key?>">
                                        <?php foreach($datavendor as $selectvendor){ ?>
                            <option  <?php if($ress['vendor_id'] == $selectvendor['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectvendor['id']; ?>"><?php echo $selectvendor['display_name']; ?></option>
                        <?php } ?>
                            </select>
                        <div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div>
                    </div>
                    <div class="form-group col-lg-6">
                            <span>File Upload &nbsp;</span>
                            <input aria-label="File" type="file" name="file" class="form-control">
                        <div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" name="submit" value="Add" />
                            <button id="kt_login_signin_submit" class="btn btn-brand"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                            <button id="kt_login_signin_submit_continue" class="btn btn-brand"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                            <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>
    </div>
