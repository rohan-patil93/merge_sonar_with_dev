<?php
// Handle POST request

if ($_POST['submit']) {

    $table_name = 'csr_contractor_registations';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = date('Y-m-d H:i:s');
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);
        $transaction_id = $response['LASTID'];
        $files = $_FILES;

        fileUploading($files, $transaction_id, $table_name);
        if ($response["status"] == "sucess") {
            header("location:index.php?option=COM_LIST_CSR_CONTRACTOR_REGISTATIONS");
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = date('Y-m-d H:i:s');
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        $response = json_decode($response, 1);

        $files = $_FILES;
        $file_res =  fileUploading($files, $rid, $table_name);
        if ($response["status"] == "sucess") {
            header("location:index.php?option=COM_LIST_CSR_CONTRACTOR_REGISTATIONS");
        }
    }
}

function fileUploading($files, $transaction_id, $table_name)
{
    $uploadedFiles = [];

    foreach ($files as $key => $fileArray) {
        if (!is_array($fileArray['name'])) {
            $fileArray['name'] = [$fileArray['name']];
            $fileArray['type'] = [$fileArray['type']];
            $fileArray['tmp_name'] = [$fileArray['tmp_name']];
            $fileArray['error'] = [$fileArray['error']];
            $fileArray['size'] = [$fileArray['size']];
        }

        // Process each file in the array
        foreach ($fileArray['name'] as $index => $fileName) {

            if ($fileArray['error'][$index] != UPLOAD_ERR_NO_FILE) {
                // Extract file details
                $actualFileName = $fileName;
                $temp = explode(".", $actualFileName);
                $randomNumber = random_int(10000, 99999);
                $microtime = microtime(true);
                $dateTime = date('Ymd_His', (int)$microtime) . sprintf('%03d', ($microtime - floor($microtime)) * 1000);
                $uniqueFilename = "file_{$randomNumber
}_{$dateTime
}." . end($temp);

                $fileSizeBytes = $fileArray['size'][$index];
                $fileSizeMB = round($fileSizeBytes / (1024 * 1024), 2);

                // Prepare upload directory
                $destinationPath = "uploads/csr/" . $uniqueFilename;
                // Ensure the directory exists
                if (!is_dir('uploads/csr')) {
                    mkdir('uploads/csr', 0777, true);
                }

                // Move uploaded file
                if (move_uploaded_file($fileArray['tmp_name'][$index], $destinationPath)) {
                    $data = [
                        'file_name' => $uniqueFilename,
                        'actual_file_name' => $actualFileName,
                        'file_size_mb' => $fileSizeMB,
                        'application_table_name' => $table_name,
                        'transaction_id' => $transaction_id,
                        'is_active' => '0',
                        'status_date' => date('Y-m-d'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $_SESSION['userid'],
                        'company_id' => $_SESSION['company_id']
                    ];

                    $url = API . "?mode=adddata&table=application_attachments&method=post";
                    $file_res = file_post_contents($url, $data, 'insert');
                    $uploadedFiles[] = $file_res;
                }
            }
        }
    }

    return $uploadedFiles;
}

if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getCSRContractors&method=get&rid=" . $rid;
    $response = callAPI($url);
    $data = json_decode($response, true);
    $data = $data['csr_contractors'][0];
    if (isset($_GET['view']) && $_GET['view'] == 1) {
        $status = 'view';
    } elseif (isset($_GET['edit']) && $_GET['edit'] == 1) {
        $status = 'update';
    }
} else {
    $data = array();
    $rid = '';
    $status = '';
}

?>
<?php
if($data['current_status'] !=""){
    $workflowsteps = getWorkflowStatus('csr_contractor_registations',$rid,$data['current_status']);
}
?>
<link rel="stylesheet" href="assets/css/custom_insert_csr_contractor_registations.css">

<div class="page-body">
    <?php echo $workflowsteps['current_status_ribbon']; ?>
    <div class="card">
        <div class="card-header table-card-header">
            <h5>Contractor Registration</h5>
            <div class="card-header-right">
                <?php echo $workflowsteps['workflow_btn']; ?>

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <?php echo $workflowsteps['breadcrumb']; ?>
        <?php
            $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,'');
            if(in_array($data['current_status'], (array)$check_array) || ($data['current_status'] == ST_CALLBACK && $data['originator'] == $_SESSION['userid'])){
                    $disable = "";
            }else{
                    $disable = "disabled";
                    $dyncss = "form-disabled";
            }

            ?>
        <div class="card-body<?php if (isset($status) && $status == 'view') {
                                    echo 'form-disabled';
                                 } ?> <?php echo $dyncss; ?> " >
            <form method="POST" action="" enctype="multipart/form-data" autocomplete="off" id="contractorForm">
                <div class="row">
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="contractor_name">Contractor Name <span class="text-danger">*</span></label>
                        <input type="text" id="contractor_name" name="contractor_name" class="form-control" required
                            value="<?php echo htmlspecialchars($data['contractor_name'] ?? ''); ?>"
                            >
                    </div>
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required
                            value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"
                             pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                    </div>
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="number" id="phone" name="phone" class="form-control" required
                            value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>"
                            >
                    </div>
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="gst">GST</label>
                        <input type="text" id="gst" name="gst" class="form-control"
                            value="<?php echo htmlspecialchars($data['gst'] ?? ''); ?>"
                            >
                    </div>
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="tan">TAN</label>
                        <input type="text" id="tan" name="tan" class="form-control"
                            value="<?php echo htmlspecialchars($data['tan'] ?? ''); ?>"
                            >
                    </div>
                    <div class="form-group mb-3 col-xl-6 col-md-6">
                        <label for="files">Files</label>
                        <input type="file" id="files" name="files[]" class="form-control" multiple >
                    </div>
                    <?php
                    $table_name = 'application_attachments';
                    $vdata1['order'] = "id ASC";
                    $vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'transaction_id' => $rid, 'application_table_name' => 'csr_contractor_registations', 'is_active' => 0);
                    $vdata = urlencode(json_encode($vdata1));
                    $vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
                    $che = callAPI($vurl);
                    $return = json_decode($che, 1);

                    ?>
                    <div class="col-xl-6 col-md-6"></div>
                    <div class="form-group col-xl-6 col-md-6 form-enabled" >
                        <div class="row">
                        <?php if (count((array)$return) > 0) {
                            foreach ($return as $row) { ?>
                                <div class="col-lg-4 "><?php echo $row['actual_file_name']; ?></div>
                                <div class="col-lg-2 ">
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($row['file_name']); ?>" class="mb-2" download><i class="ti-cloud-down csr-download-icon" ></i></a>
                                </div>
                        <?php  }
                        } ?>
                        </div>
                    </div>
                    <?php if ($status != 'view') { ?>
                        <div class="form-group col-md-4 align-self-end ">
                            <?php if (isset($rid) && $rid != '') { ?>
                                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                                <button name="submit" value="update" type="submit" class="btn btn-primary">Update</button>
                            <?php } else { ?>
                                <button name="submit" value="insert" type="submit" class="btn btn-primary">Submit</button>
                            <?php } ?>
                            <button type="button" id="resetBtn" class="btn btn-danger waves-effect waves-light">Cancel</button>

                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/custom_insert_csr_contractor_registations.js"></script>
