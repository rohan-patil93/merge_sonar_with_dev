<?php
if (isset($_POST['submit']) && $_POST['submit'] == 'Add') {
    if (!isset($_FILES['data']) || $_FILES['data']['error'] !== UPLOAD_ERR_OK || empty($_FILES['data']['tmp_name'])) {
        header("location:index.php?option=COM_INSERT_EDI_READe&err=4003");
        exit;
    }

    $ext = explode(".", $_FILES["data"]['name']);
    $file_name = !empty($ext[0]) ? $ext[0] : '';
    $file_ext = count($ext) > 1 ? end($ext) : 'txt';

    $resurl = API."?mode=singlerowtabledata&table=".TBL_EDIMASTER."&fid=EDI_Name&rid=". $file_name;
    $resresponse = callAPI($resurl);

    $resdata = json_decode($resresponse, 1);
    if (empty($resdata) || empty($resdata['id']) || $resresponse === null) {
        header("location:index.php?option=COM_INSERT_EDI_READe&err=4003");
        exit;
    }

    $separator = !empty($resdata['edi_delimiter']) ? $resdata['edi_delimiter'] : ',';
    $str_separator = $resdata['EDI_String_Separator'];

    $target_dir = 'uploads/edi';
    if (!is_dir($target_dir)) {
        @mkdir($target_dir, 0777, true);
    }
    $file_path = $target_dir . '/file_' . time() . uniqid() . '.' . $file_ext;

    if (!move_uploaded_file($_FILES["data"]["tmp_name"], $file_path)) {
        header("location:index.php?option=COM_INSERT_EDI_READe&err=4003");
        exit;
    }

    $content = file_exists($file_path) ? file_get_contents($file_path) : '';
    if ($content === false) {
        $content = '';
    }

    $testdata = array();
    if (empty($str_separator)) {
        if (file_exists($file_path) && ($h = fopen($file_path, "r")) !== false) {
            while (($content1 = fgetcsv($h, 1000, $separator)) !== false) {
                $testdata[]= $content1;
            }
            fclose($h);
        }
    } else {
        $str_separator_arr = is_array($str_separator) ? $str_separator : json_decode($str_separator, true);
        if (!empty($str_separator_arr['lenght'])) {
            $lenth = array_sum($str_separator_arr['lenght']);
            if ($lenth > 0) {
                $contentdata = str_split($content, $lenth);
                foreach ($contentdata as $split) {
                    $array = array();
                    $field_count = isset($str_separator_arr['filed']) ? count((array)$str_separator_arr['filed']) : 0;
                    for ($j = 0; $j < $field_count; $j++) {
                        $start = isset($str_separator_arr['start_pos'][$j]) ? ((int)$str_separator_arr['start_pos'][$j] - 1) : 0;
                        $len = isset($str_separator_arr['lenght'][$j]) ? (int)$str_separator_arr['lenght'][$j] : 0;
                        $array[$j] = substr($split, $start, $len);
                    }
                    $testdata[] = $array;
                }
            }
        }
    }

    $count = !empty($testdata[0]) ? count((array)$testdata[0]) : 0;
    if (!empty($testdata)) {
        $testurl = API."?mode=multiinsert&filename=".$file_name."&method=post";
        $testresponse = file_post_contents($testurl, $testdata, 'insert');
    }

    $data = array();
    $data['file_name'] = $file_path;
    $data['receive_date'] = date("Y-m-d H:i:s");
    $data['read_date'] = date("Y-m-d H:i:s");
    $data['content'] = $content;
    $data['current_status'] = isset($_POST['Current_Status']) ? $_POST['Current_Status'] : '0';
    $data['status_date'] = date("Y-m-d H:i:s");
    $data['created_date'] = date("Y-m-d H:i:s");
    $data['from_date'] = date("Y-m-d H:i:s");
    $data['created_by'] = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
    $data['is_active'] = '0';

    $url = API."?mode=adddata&table=".TBL_EDIREAD."&method=post";
    $response = file_post_contents($url, $data, 'insert');

    if ($response) {
        if (isset($resdata['EDI_Type']) && $resdata['EDI_Type'] == 'Upload With Confirm') {
            $content = "The  total ".$count."records inserted successfully";
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=read.txt");
            print $content;
        }
        header("location:index.php?option=com_view_edi_read");
    }
}
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!-- begin:: Content Head -->
        <?php if (isset($_GET['error']) && $_GET['error'] != '') { ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">    <?php echo isset($_SESSION['error_message'][$_GET['error']]) ? $_SESSION['error_message'][$_GET['error']] : ''; ?>  </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <?php } ?>
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                    <?php 
                    $title = isset($_GET['option']) ? ucwords(str_replace('_', ' ', str_replace('view_', '', str_replace('com_', '', $_GET['option'])))) : ''; 
                    echo $title; 
                    ?>
                    </h3>
                </div>
                <div class="card-toolbar">
                    <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                        <i class="ki ki-long-arrow-back icon-xs"></i>
                        Back
                    </a>
                </div>
            </div>
            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="tab-content">
                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                            <div class="kt-form kt-form--label-right">
                                <div class="kt-form__body">
                                    <?php if (isset($_GET['err']) && $_GET['err'] != '') { ?>
                                    <div class="form-group form-group-last">
                                        <div class="alert alert-secondary" role="alert">
                                            <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                                            <div class="alert-text">
                                             <?php
                                                echo $_GET['err']."--" ;
                                                echo $message= errorMessageUseErrorCode($_GET['err']);
                                              ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group  row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">File Upload</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <input aria-label="Data" type="file" name="data" class="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-3 col-xl-3">
                                                </div>
                                                <div class="col-lg-9 col-xl-9">
                                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ ?>
                                                    <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                                    <input type="hidden" name="submit" value="Update" />
                                                    <button id="kt_llogin_signup_submit" class="btn btn-success">Upadate</button>
                                                <?php } else{ ?>
                                                    <input type="hidden" name="submit" value="Add" />
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
