<?php
if (isset($_POST['submit']) && $_POST['submit'] == 'Add') {
        $ext=explode(".",$_FILES["data"]['name']);
        $file_name = $ext[0];
        $separator = ',';
        $file_insert_name = $_POST['table'].'file'.'_'.time().uniqid().'.'.$ext[1];
        $file_path='uploads/edi/'.$file_insert_name;
        move_uploaded_file($_FILES["data"]["tmp_name"],$file_path);
        $fp = fopen($file_path, "r");
            $content = fread($fp, filesize($file_path));
            $testdata = array();
            if (($h = fopen($file_path, "r")) !== false) {
                while (($content1 = fgetcsv($h, 10000, $separator)) !== false) {
                    $testdata[]= $content1;
                }
                fclose($h);
            }
        $data= array();
        $data['header'] = json_encode($testdata['0']);
        $data['company_id'] = $_SESSION['company_id'];
        $data['file_name'] = $file_insert_name;
        $data['application_table_name'] = $_POST['table'];
        $data['receive_date'] = date("Y-m-d H:i:s");
        $data['read_date'] =date("Y-m-d H:i:s");
        $data['content'] = $content;
        $data['current_status'] = $_POST['Current_Status'];
        $data['status_date'] = date("Y-m-d H:i:s");
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['from_date'] = date("Y-m-d H:i:s");
        $data['created_by'] = $_SESSION['userid'];
        $data['is_active'] ='0';
        $url="";
        $url = API."?mode=adddata&table=".TBL_EDIREAD."&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $response = json_decode($response,1);
        $count = count((array)$testdata[0]);
                              //multiinsert
        $testurl = API."?mode=multiinsert&filename=".$file_insert_name."&edi_read_id=".$response['LASTID']."&method=post";
        $testresponse = file_post_contents($testurl, $testdata, 'insert');
        $testresponse = json_decode($testresponse,1);
        if ($response) {

            header("location:index.php?option=COM_INSERT_DYNAMIC_FIELD_MAPPING&file_name=".$file_insert_name."&table=".$_POST['table']);
        }
    }
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row workflow-step text-center mt-5 mb-5">
            <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon  bg-secondary"></div>
                <div class="font-size-lg mt-1">Step 1</div>

            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                <div class="work-box bg-secondary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon bg-secondary"></div>
                <div class="font-size-lg mt-1">Step 2</div>

            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                <div class="work-box bg-secondary">
                    <i class="ti-settings"></i>
                </div>

                <div class="font-size-lg mt-1">Step 3</div>

            </div>

        </div>
        <div class="row">
            <div class="col-lg-12">

                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header table-card-header">
                      <h5><?php echo "Import";  ?></h5>
                      <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i>
                                  Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="kt-form " id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span>Import</span>
                                <div>
                                    <input aria-label="Data" class="form-control" type="file" id="data" name="data" value="" required>
                                </div>
                            </div>
                            <input type="hidden" name="table" value="<?php echo $_GET['table'] ?>">

                        </div>
                        <?php $dsk = $_GET['DISPLAY_ATTRIBUTE']!="" ? "&DISPLAY_ATTRIBUTE=1" :"" ; ?>
                        <span> Sample CSV file for <a target="_blank"  href="csv_download.php?table=<?php echo $_GET['table'].$dsk; ?>"> Download</a></span>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="hidden" name="submit" value="Add">
                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="exit"><i class="la la-check"></i>Save &amp; exit</button>
                                <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save &amp; continue</button>
                                <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2 btn_mobile"><i class="la la-close"></i>Cancel</button>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-3 text-right">
                                <button type="reset" disabled="disabled" class="btn btn-danger btn_mobile"><i class="la la-trash"></i>Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
