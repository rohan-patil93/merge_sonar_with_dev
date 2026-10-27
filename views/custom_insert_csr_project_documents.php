<?php
$_SESSION['apply_js']['multiselest'] = 1;

function csrProjectDocText($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrProjectDocDecodeRid($ridParam)
{
    if ($ridParam == '') {
        return 0;
    }
    $rid = encodeId($ridParam);
    $rid = urldecode($rid);
    $ridParts = explode(':', $rid);
    return (int)($ridParts[1] ?? 0);
}

function csrProjectDocEncodeRid($proposalId)
{
    $proposalId = (int)$proposalId;
    if ($proposalId <= 0) {
        return '';
    }
    return deocdeId('id:' . $proposalId);
}

$selectedProposalRid = isset($_GET['Rid']) ? (string)$_GET['Rid'] : '';
$selectedProposalId = csrProjectDocDecodeRid($selectedProposalRid);
$saveMessage = '';
$errorMessage = '';
$allowedAccess = array('public', 'private', 'internal');
if (isset($_POST['submit']) && $_POST['submit'] == 'update_document') {
    $selectedProposalRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : '';
    $selectedProposalId = csrProjectDocDecodeRid($selectedProposalRid);
    $documentId = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
    $documentName = trim((string)($_POST['document_name'] ?? ''));
    $accessType = trim((string)($_POST['access_type'] ?? 'internal'));

    $uploadedFile = $_FILES['document_file'] ?? array();

    if ($selectedProposalId > 0 && $documentId > 0 && $documentName != '') {
        if (!in_array($accessType, (array)$allowedAccess)) {
            $accessType = 'internal';
        }

        $updatePost = array(
            'id' => $documentId,
            'proposal_id' => $selectedProposalId,
            'document_name' => $documentName,
            'access_type' => $accessType
        );

        $fileError = $uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE;
        $tmpName = $uploadedFile['tmp_name'] ?? '';
        $actualFileName = $uploadedFile['name'] ?? '';
        if ($fileError == UPLOAD_ERR_OK && $tmpName != '') {
            if (!is_dir('uploads/csr')) {
                mkdir('uploads/csr', 0777, true);
            }
            $ext = pathinfo($actualFileName, PATHINFO_EXTENSION);
            $randomNumber = random_int(10000, 99999);
            $microtime = microtime(true);
            $dateTime = date('Ymd_His', (int)$microtime) . sprintf('%03d', ($microtime - floor($microtime)) * 1000);
            $uniqueFileName = "csr_doc_{$randomNumber
}_{$dateTime
}";
            if ($ext != '') {
                $uniqueFileName .= "." . $ext;
            }
            $destinationPath = "uploads/csr/" . $uniqueFileName;
            if (move_uploaded_file($tmpName, $destinationPath)) {
                $updatePost['file_name'] = $uniqueFileName;
                $updatePost['actual_file_name'] = $actualFileName;
            }
        }

        $updateUrl = API . "?mode=updateCsrProjectDocument&method=post";
        file_post_contents($updateUrl, $updatePost, 'update');
        header("location:index.php?option=COM_INSERT_CSR_PROJECT_DOCUMENTS&Rid=" . urlencode(csrProjectDocEncodeRid($selectedProposalId)) . "&updated=1");
        exit;
    } else {
        $errorMessage = 'Please provide valid document details for update.';
    }
}
if (isset($_POST['submit']) && $_POST['submit'] == 'save_documents') {
    $selectedProposalRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : '';
    $selectedProposalId = csrProjectDocDecodeRid($selectedProposalRid);
    $documentNames = $_POST['document_name'] ?? array();
    $accessTypes = $_POST['access_type'] ?? array();
    $uploadedFiles = $_FILES['document_file'] ?? array();

    $insertedCount = 0;
    $failedCount = 0;
    $failedMessages = array();

    if ($selectedProposalId <= 0) {
        $errorMessage = 'Please select approved CSR project.';
    } else {
        if (!is_dir('uploads/csr')) {
            mkdir('uploads/csr', 0777, true);
        }

        if (count((array)$documentNames) > 0) {
            foreach ($documentNames as $index => $documentNameValue) {
                $documentName = trim((string)$documentNameValue);
                $accessType = trim((string)($accessTypes[$index] ?? 'internal'));
                $tmpName = $uploadedFiles['tmp_name'][$index] ?? '';
                $fileError = $uploadedFiles['error'][$index] ?? UPLOAD_ERR_NO_FILE;
                $actualFileName = $uploadedFiles['name'][$index] ?? '';

                if ($documentName == '' && $fileError == UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($documentName == '' || $fileError == UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if (!in_array($accessType, (array)$allowedAccess)) {
                    $accessType = 'internal';
                }

                if ($fileError == UPLOAD_ERR_OK && $tmpName != '') {
                    $ext = pathinfo($actualFileName, PATHINFO_EXTENSION);
                    $randomNumber = random_int(10000, 99999);
                    $microtime = microtime(true);
                    $dateTime = date('Ymd_His', (int)$microtime) . sprintf('%03d', ($microtime - floor($microtime)) * 1000);
                    $uniqueFileName = "csr_doc_{$randomNumber
}_{$dateTime
}";
                    if ($ext != '') {
                        $uniqueFileName .= "." . $ext;
                    }

                    $destinationPath = "uploads/csr/" . $uniqueFileName;
                    if (move_uploaded_file($tmpName, $destinationPath)) {
                        $postData = array(
                            'company_id' => $_SESSION['company_id'],
                            'proposal_id' => $selectedProposalId,
                            'document_name' => $documentName,
                            'file_name' => $uniqueFileName,
                            'actual_file_name' => $actualFileName,
                            'access_type' => $accessType,
                            'is_active' => '0',
                            'current_status' => '0',
                            'status_date' => date('Y-m-d H:i:s'),
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $_SESSION['userid']
                        );
                        $url = API . "?mode=insertCsrProjectDocument&method=post";
                        $insertResponseRaw = file_post_contents($url, $postData, 'insert');
                        $insertResponse = json_decode($insertResponseRaw, true);
                        if (isset($insertResponse['status']) && ($insertResponse['status'] == 'sucess' || $insertResponse['status'] == 'success')) {
                            $insertedCount++;
                        } else {
                            $failedCount++;
                            $apiMessage = '';
                            if (is_array($insertResponse)) {
                                $apiMessage = $insertResponse['message'] ?? ($insertResponse['msg'] ?? '');
                                if ($apiMessage == '' && isset($insertResponse['error_no'])) {
                                    $apiMessage = 'DB Error: ' . $insertResponse['error_no'];
                                }
                            }
                            if ($apiMessage == '') {
                                $apiMessage = trim((string)$insertResponseRaw);
                            }
                            if ($apiMessage == '') {
                                $apiMessage = 'Unknown API insert failure';
                            }
                            $failedMessages[] = 'Failed to insert "' . $documentName . '": ' . $apiMessage;
                        }
                    } else {
                        $failedCount++;
                        $failedMessages[] = 'Failed to upload file: ' . $actualFileName;
                    }
                }
            }
        }

        if ($insertedCount > 0 && $failedCount == 0) {
            $redirectUrl = "index.php?option=COM_INSERT_CSR_PROJECT_DOCUMENTS&Rid=" . urlencode(csrProjectDocEncodeRid($selectedProposalId)) . "&saved=1";
            header("location:" . $redirectUrl);
            exit;
        }
        if ($insertedCount > 0 && $failedCount > 0) {
            $saveMessage = $insertedCount . " document(s) uploaded. " . $failedCount . " failed.";
            $errorMessage = implode(' | ', array_slice($failedMessages, 0, 3));
        }
        if ($insertedCount == 0) {
            $errorMessage = count((array)$failedMessages) > 0 ? implode(' | ', array_slice($failedMessages, 0, 3)) : 'No documents were uploaded.';
        }
    }
}

if (isset($_GET['saved']) && $_GET['saved'] == '1') {
    $saveMessage = 'Documents uploaded successfully.';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $saveMessage = 'Document updated successfully.';
}

$approvedProjects = array();
$existingDocuments = array();

$approvedUrl = API . "?mode=getCsrApprovedProjectsForDocuments&method=get";
$approvedResponse = callAPI($approvedUrl);
$approvedData = json_decode($approvedResponse, true);
$approvedProjects = $approvedData['approved_projects'] ?? array();

if ($selectedProposalId > 0) {
    $docsUrl = API . "?mode=getCsrProjectDocuments&method=get&proposal_id=" . $selectedProposalId;
    $docsResponse = callAPI($docsUrl);
    $docsData = json_decode($docsResponse, true);
    $existingDocuments = $docsData['project_documents'] ?? array();
}
?>

<div class="page-body">
    <div class="card">
        <div class="card-header table-card-header">
            <h5>CSR Project Documents</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <?php if ($saveMessage != '') { ?>
                <div class="alert alert-success"><?php echo csrProjectDocText($saveMessage); ?></div>
            <?php } ?>
            <?php if ($errorMessage != '') { ?>
                <div class="alert alert-danger"><?php echo csrProjectDocText($errorMessage); ?></div>
            <?php } ?>

            <form method="GET" id="csrProjectFilterForm" class="mb-3">
                <input type="hidden" name="option" value="COM_INSERT_CSR_PROJECT_DOCUMENTS">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="proposal_rid">Approved CSR Project</label>
                        <select name="Rid" id="proposal_rid" class="form-control js-example-basic-single select2" required>
                            <option value="">Select Project</option>
                            <?php if (count((array)$approvedProjects) > 0) { ?>
                                <?php foreach ($approvedProjects as $project) {
                                    $projectId = (int)($project['id'] ?? 0);
                                    $projectRid = csrProjectDocEncodeRid($projectId);
                                ?>
                                    <option value="<?php echo csrProjectDocText($projectRid); ?>" <?php echo $selectedProposalId == $projectId ? 'selected' : ''; ?>>
                                        <?php echo csrProjectDocText(($project['subject'] ?? '-') . ' - ' . ($project['institue_name'] ?? '-')); ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm">
                    <tr>
                        <th scope="col">Document Name</th>
                        <th scope="col">Access Type</th>
                        <th scope="col">File</th>
                        <th scope="col">Replace File</th>
                        <th scope="col">Download</th>
                        <th scope="col">Action</th>
                    </tr>
                    <?php if ($selectedProposalId > 0 && count((array)$existingDocuments) > 0) { ?>
                        <?php foreach ($existingDocuments as $docRow) { ?>
                            <tr>
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="Rid" value="<?php echo csrProjectDocText($selectedProposalRid); ?>">
                                    <input type="hidden" name="document_id" value="<?php echo (int)($docRow['id'] ?? 0); ?>">
                                    <td>
                                        <input aria-label="Document Name" type="text" class="form-control" name="document_name" value="<?php echo csrProjectDocText($docRow['document_name'] ?? ''); ?>" required>
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Access Type" class="form-control js-example-basic-single select2" name="access_type">
                                            <?php foreach($allowedAccess as $row){ ?>
                                                <option value="<?php echo $row; ?>" <?php echo ($docRow['access_type'] ?? '') == $row ? 'selected' : ''; ?>><?php echo $row; ?></option>
                                        <?php } ?>
                                        </select>
                                    </td>
                                    <td><?php echo csrProjectDocText($docRow['actual_file_name'] ?? ''); ?></td>
                                    <td><input aria-label="Document File" type="file" class="form-control" name="document_file"></td>
                                    <td>
                                        <?php if (($docRow['file_name'] ?? '') != '') { ?>
                                            <a target="_blank" href="uploads/csr/<?php echo csrProjectDocText($docRow['file_name']); ?>" download>
                                                <i class="ti-cloud-down csr-download-icon"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="submit" name="submit" value="update_document" class="btn btn-primary btn-sm">Update</button>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center">No documents found</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <form method="POST" action="" enctype="multipart/form-data" id="csrProjectDocumentForm" autocomplete="off">
                <input type="hidden" name="Rid" value="<?php echo csrProjectDocText($selectedProposalRid); ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="csrDocumentUploadTable">
                        <tr>
                            <th scope="col" style="width: 35%;">Document Name</th>
                            <th scope="col" style="width: 20%;">Access Type</th>
                            <th scope="col" style="width: 35%;">Upload File</th>
                            <th scope="col" style="width: 10%;">Action</th>
                        </tr>
                        <tr class="doc-upload-row">
                            <td><input aria-label="Enter document name" type="text" class="form-control" name="document_name[]" placeholder="Enter document name"></td>
                            <td>
                                <select aria-label="Access Type[]" class="form-control js-example-basic-single" name="access_type[]">
                                    <?php foreach($allowedAccess as $row){ ?>
                                                <option value="<?php echo $row; ?>" ><?php echo $row; ?></option>
                                        <?php } ?>
                                </select>
                            </td>
                            <td><input aria-label="Document File[]" type="file" class="form-control" name="document_file[]"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-sm" id="addDocumentRowBtn">+ Add New File</button>
                </div>
                <div>
                    <button type="submit" name="submit" value="save_documents" class="btn btn-primary" <?php echo $selectedProposalId > 0 ? '' : 'disabled'; ?>>Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#proposal_rid').on('change', function() {
            $('#csrProjectFilterForm').submit();
        });

        $('#addDocumentRowBtn').on('click', function() {
        var options = '<?php foreach ($allowedAccess as $row) { ?><option value="<?php echo $row; ?>"><?php echo $row; ?></option><?php
} ?>';
        var rowHtml = '' +
            '<tr class="doc-upload-row">' +
            '<td><input aria-label="Enter document name" type="text" class="form-control" name="document_name[]" placeholder="Enter document name"></td>' +
            '<td><select aria-label="Access Type[]" class="form-control js-example-basic-single" name="access_type[]">' + options + '</select></td>' +
            '<td><input aria-label="Document File[]" type="file" class="form-control" name="document_file[]"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button></td>' +
            '</tr>';
        $('#csrDocumentUploadTable').append(rowHtml);
        $('.js-example-basic-single').select2({});
});

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#csrDocumentUploadTable .doc-upload-row').length > 1) {
                $(this).closest('tr').remove();
            }
        });
    });
</script>
