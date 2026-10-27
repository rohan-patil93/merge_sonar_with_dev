<?php
if (isset($_POST['submit'])){
    $table_name = $_POST['duplicate_table_name'];
    $add_url = $_POST['insert_page_names'];
    $view_url = $_POST['list_page_names'];
    $mandatory_exp = explode(',', $_POST['mandatory_str']);
    $url="";
    $dis_labels = json_decode(urldecode($_POST['dis_label']),1);
    $data= array();
    $now_date = fromDate($role['from_date']);
    if( $_POST['current_status']==0 ){
        $data['is_active'] = '0';
    }else{
        $data['is_active'] = '';
    }
    $data['company_id'] = $_SESSION['company_id'];
    if($table_name == 'currencies'){
        $country_name = toGetCountryNameBaseCountryID($_POST['country_id']);
        $data['currency_list'] = $_POST['currency_symbol']."-".$_POST['currency_name']."-".$country_name;
    }
    $in_date_pic = $_POST['in_date_pic'];
    $in_date_pic = explode(',', $in_date_pic);
    foreach ($mandatory_exp as $key => $value) {
        $data[$value] = $_POST[$value];
        if(is_array($_POST[$value])){
            $data[$value] = json_encode($_POST[$value],true);
        }
        if (count((array)$in_date_pic)>0 && in_array($value, $in_date_pic)) {
                $date_at = str_replace('/"', '-', $_POST[$value]);
                $date_at =  date('Y-m-d', strtotime($date_at));
                $data[$value] = $date_at;
            }
    }

//start curruncy
$in_currency_fileds = $_POST['in_currency_fileds'];
if($_POST['in_currency_fileds']!=""){
    $in_currency_fileds = explode(',', $in_currency_fileds);
    foreach ($mandatory_exp as $key => $value) {
        $data[$value] = $_POST[$value];
        if(is_array($_POST[$value])){
            $data[$value] = json_encode($_POST[$value],true);
        }
        if (count((array)$in_currency_fileds)>0 && in_array($value, $in_currency_fileds)) {
                $lastmuny = substr($_POST[$value],-2);
                $currency = substr($_POST[$value], 0, -3);
                $numb = (int) filter_var($currency, FILTER_SANITIZE_NUMBER_INT);
                $data[$value] = $numb.".".$lastmuny;
            }
    }
}
//curruncy
//start Phone
$in_phone_fileds = $_POST['in_phone_fileds'];
if($_POST['in_phone_fileds']!=""){
    $in_phone_fileds = explode(',', $in_phone_fileds);
    foreach ($mandatory_exp as $key => $value) {
        $data[$value] = $_POST[$value];
        if(is_array($_POST[$value])){
            $data[$value] = json_encode($_POST[$value],true);
        }
        if (count((array)$in_phone_fileds)>0 && in_array($value, $in_phone_fileds)) {
                $numb = str_replace("-","",$_POST[$value]);
                $data[$value] = $numb;
            }
    }
}
//phone

    if($_POST['add_mandatory_str'] !=""){
        $add_mandatory_exp = explode(',', $_POST['add_mandatory_str']);
        foreach ($add_mandatory_exp as $key => $value) {
            $add_data[$value] = $_POST[$value];

            if (count((array)$in_date_pic)>0 && in_array($value, $in_date_pic)) {
                    $date_at = str_replace('/"', '-', $_POST[$value]);
                    $date_at =  date('Y-m-d', strtotime($date_at));
                    $add_data[$value] = $date_at;
                }
        }
        $data['additional_attributes'] = json_encode($add_data,true);
    }
    $data['current_status'] = $_POST['current_status'];
    $data['status_date'] = nowDate();
    $data['from_date'] = date("Y-m-d", strtotime($_POST['from_date']));
    $data['to_date'] = date("Y-m-d", strtotime($_POST['to_date']));
    if($_POST['image']!=''){
        $images= explode(',', $_POST['image']);
        foreach ($images as  $value_image) {
            $hid_image = "hid".$value_image;
            $data[$value_image] = imageupload('all',$value_image,$_POST[$hid_image]);
        }

    }
    if($table_name == 'gst_setting'){
        $valida = validategstin($_POST['gst_no']);
        if($valida == 0){
            header("location:index.php?option=$add_url&error=3111");
        }
    }
    if($_POST['submit'] == 'Add'){
        $data['current_status'] = $data['current_status']!="" ? $data['current_status'] : '0';
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $url = API."?mode=adddata&table=".$table_name."&method=post";
        if(isset($_POST['module_setup_id']) && $_POST['module_setup_id'] !=""){
            foreach($_POST['module_setup_id'] as $module_id){
                $data['module_setup_id'] = $module_id;
                $response = file_post_contents($url, $data, 'insert');
            }
        }else{
            $response = file_post_contents($url, $data, 'insert');
        }


    }
    if($_POST['submit'] == 'Update'){
        $_SESSION['check'] = 1;
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data, 'update');

    }
    $response = json_decode($response,1);
    if($popup=='Y'){?>
        <script type="text/javascript">
             $(document).ready(function() {
                swal({
                    title: "Saved sucessfuly",
                    type: "success",
                    showCancelButton: false,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    window.location.reload();
                });
            });
        </script>
    <?php
    }else{
        if($response['status'] == 'fail'){
            if(isset($_SESSION['error'][$response['error_no']])){
                $dis_err = explode(",", $response['other']);
                foreach ($dis_err as $va) {
                    $dis_er [] = $dis_labels[$va];
                }
                $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
            }else{
                $error_message = $response['message'];
            }
        }else{
            if ($_POST['submit_value'] == 'continue'){
                    header("location:index.php?option=$add_url"); exit;
                }else{
                    header("location:index.php?option=$view_url"); exit;
                }
        }
    }
}
?>
