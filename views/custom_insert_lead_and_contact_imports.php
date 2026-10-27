<?php
$_SESSION['apply_js']['multiselest'] = "1";
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

        $url = API . "?mode=importleadcontactCSV&method=post";
        $response = file_post_contents($url, $postData, 'insert');
        $response = json_decode($response, 1);

        // Debug response

        if (strtolower(trim($response['status'])) === 'success') {
            $summary = $response['summary'] ?? null;
            $msg = "CSV imported successfully.";

            if ($summary) {
                $msg .= " Total: {$summary['total_rows']
}, Inserted: {$summary['inserted']
}, Duplicates: {$summary['duplicates_skipped']
}, Failed: {$summary['failed']
}";
            }


            $_SESSION['insert_msg'] = 3000;
            $nrid = "id:".$response['rid'];
            header("location:index.php?option=COM_VIEW_LEAD_AND_CONTACT_IMPORTS&Rid=".deocdeId($nrid));
            exit;
        } else {
            $errorMsg = $response['message'] ?? 'File upload error.';
            $_SESSION['upload_msg_type'] = 'error';
            $_SESSION['upload_msg'] = $errorMsg;
        }
    }

if (!empty($_SESSION['upload_msg'])) {
    $msgType = $_SESSION['upload_msg_type'];
    $msg = addslashes($_SESSION['upload_msg']);

    // clear session
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
        <h5>Leads/Contacts Import</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light  m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body m-r-50  m-l-50">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group row">
                <div class="form-group col-lg-6">
                    <span>File Name <span class="text-danger">*</span></span>
                    <div>
                        <input aria-label="Enter Upload File Name" class="form-control" type="text" id="file_name" name="file_name" value="" placeholder="Enter Upload File Name" required>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span>Lead/Contact Source Type <span class="text-danger">*</span> </span>
                    <div>
                        <select aria-label="Source Type" class="form-select js-example-basic-single" name="source_type" id="source_type" required>
                            <option value=""> Select Any One</option>
                            <?php
                            if (!empty($sources)) {
                                foreach ($sources as $source) {
                                    $value = htmlspecialchars($source['value']);
                                    $label = htmlspecialchars($source['label']);
                                    echo "<option value='{$value
}'>{$label
}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span>
                        Select Type <span class="text-danger">*</span>
                    </span>
                    <div>
                        <input aria-label="Import Type" type="radio" id="import_type_leads" name="import_type" value="leads" checked> Leads
                        <input aria-label="Import Type" type="radio" id="import_type_contacts" name="import_type" value="contacts"> Contacts
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span>CSV File <span class="text-danger">*</span> </span>
                    <div>
                        <input aria-label="Csv File" class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                    <button  type="submit" class="btn btn-primary" name="submit" value="upload_csv"> Submit</button>
            </div>
        </form>
    </div>
</div>
