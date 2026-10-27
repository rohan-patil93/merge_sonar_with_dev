<?php
$_SESSION['apply_js']['multiselest'] = 1;
$vurl = API . "?mode=getleadcontactsourcetype&method=get";
$che = callAPI($vurl);
$return = json_decode($che, true);
$sources = $return['crm_general_codes'];

if (isset($_POST['submit']) && $_POST['submit'] === 'upload_csv' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tempPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileData = file_get_contents($tempPath);

        $postData = [
            'original_file_name' => $fileName,
            'filedata' => base64_encode($fileData),
            'submit' => 'upload_csv',
            'file_name' => $_POST['file_name'],
            'source_type' => $_POST['source_type'],
            'import_type' => $_POST['import_type'],
        ];

        $url = API . "?mode=importContractCSV&method=post";
        $response = file_post_contents($url, $postData, 'insert');
        $response = json_decode($response, 1);

        if (strtolower(trim($response['status'])) === 'success') {
            $summary = $response['summary'] ?? null;
            $msg = "CSV imported successfully.";

            if ($summary) {
                $msg .= " Total: {$summary['total_rows']
}, Inserted: {$summary['inserted']
}, Duplicates: {$summary['duplicates']
}, Failed: {$summary['failed']
}";
            }

            $_SESSION['upload_msg_type'] = 'success';
            $_SESSION['upload_msg'] = $msg;
            $rrid = deocdeId('id:'.$response['rid']);
            $url = "location:index.php?option=COM_LIST_CONTRACT_IMPORT_FILE_ROWS&Rid=".$rrid;
            header($url);
        } else {
            $errorMsg = $response['message'] ?? 'File upload error.';
            $_SESSION['upload_msg_type'] = 'error';
            $_SESSION['upload_msg'] = $errorMsg;
        }
    }

if (!empty($_SESSION['upload_msg'])) {
    $msgType = $_SESSION['upload_msg_type'];
    $msg = addslashes($_SESSION['upload_msg']);

    $_SESSION['upload_msg'] = '';
    $_SESSION['upload_msg_type'] = '';
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify(
                    'top',
                    'right',
                    'fa <?php echo ($msgType === "success") ? "fa-check-circle" : "fa-exclamation-circle"; ?>',
                    'form-label label label-<?php echo ($msgType === "success") ? "success" : "danger"; ?>',
                    'animated flipInX',
                    'animated flipOutX',
                    '<?php echo $msg; ?>',
                    ''
                );
            }
        };
    </script>
<?php
}
?>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header table-card-header">
        <h5>Contracts Import</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body m-l-50 m-r-50">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group row">
                <div class="form-group col-lg-4">
                    <span class="form-label">File Name </span>
                    <div>
                        <input aria-label="Enter Upload File Name" class="form-control" type="text" id="file_name" name="file_name" value="" placeholder="Enter Upload File Name" required>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">
                        Select Type
                    </span>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                          <label class="form-label">
                            <input aria-label="Import Type" type="radio" id="import_type_customer" name="import_type" value="customer" checked>
                            <i class="helper"></i>Customer
                          </label>
                        </div>
                        <div class="radio radio-inline">
                          <label class="form-label">
                            <input aria-label="Import Type" type="radio" id="import_type_vendor" name="import_type" value="vendor">
                            <i class="helper"></i>Vendor
                          </label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">CSV File: </span>
                    <div>
                        <input aria-label="Csv File" class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <button id="kt_login_signin_submit" type="submit" class="btn btn-primary col-lg-1" name="submit" value="upload_csv"><i class="la la-check"></i>Submit</button>
            </div>
        </form>
    </div>
</div>
