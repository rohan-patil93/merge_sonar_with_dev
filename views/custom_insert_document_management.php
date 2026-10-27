<?php
$_SESSION['apply_js']['multiselest'] = 1;

$pageOption = 'COM_INSERT_DOCUMENT_MANAGEMENT';
$listOption = 'COM_LIST_DOCUMENT_MANAGEMENT';
$uploadDir = 'uploads/document_management/';

function dmText($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function dmDecodeRid($ridParam)
{
    if ($ridParam == '') {
        return 0;
    }
    $rid = encodeId($ridParam);
    $rid = urldecode($rid);
    $ridParts = explode(':', $rid);
    return (int)($ridParts[1] ?? 0);
}

$rid = 0;
$errorMessage = '';
$isEdit = false;

if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = dmDecodeRid($_GET['Rid']);
    if ($rid > 0) {
        $isEdit = true;
    }
}

if (isset($_POST['submit']) && $_POST['submit'] != '') {
    $submitAction = $_POST['submit'];
    $postRid = isset($_POST['rid']) ? (int)$_POST['rid'] : 0;
    $documentTypeId = isset($_POST['document_type_id']) ? (int)$_POST['document_type_id'] : 0;
    $documentName = trim((string)($_POST['document_name'] ?? ''));
    $fileIds = $_POST['file_id'] ?? array();
    $fileNames = $_POST['file_name'] ?? array();
    $storedFiles = $_POST['file'] ?? array();
    $actualFileNames = $_POST['actual_file_name'] ?? array();
    $uploadedFiles = $_FILES['document_file'] ?? array();

    if ($documentTypeId <= 0) {
        $errorMessage = 'Document type is required.';
    } elseif ($documentName == '') {
        $errorMessage = 'Document name is required.';
    } elseif (count((array)$fileNames) == 0) {
        $errorMessage = 'At least one file row is required.';
    } else {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $saveFileIds = array();
        $saveFileNames = array();
        $saveFiles = array();
        $saveActualFileNames = array();
        $hasValidationError = false;

        if (count((array)$fileNames) > 0) {
            foreach ($fileNames as $index => $fileLabel) {
                $fileLabel = trim((string)$fileLabel);
                $fileId = (int)($fileIds[$index] ?? 0);
                $storedFile = trim((string)($storedFiles[$index] ?? ''));
                $actualFileName = trim((string)($actualFileNames[$index] ?? ''));
                $tmpName = $uploadedFiles['tmp_name'][$index] ?? '';
                $fileError = $uploadedFiles['error'][$index] ?? UPLOAD_ERR_NO_FILE;
                $uploadName = $uploadedFiles['name'][$index] ?? '';

                if ($fileLabel == '' && $storedFile == '' && $fileError == UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($fileLabel == '') {
                    $errorMessage = 'File name is required for each file row.';
                    $hasValidationError = true;
                    break;
                }

                if ($fileError == UPLOAD_ERR_OK && $tmpName != '') {
                    $ext = pathinfo($uploadName, PATHINFO_EXTENSION);
                    $uniqueFileName = 'doc_' . mt_rand(10000, 99999) . '_' . date('Ymd_His') . '_' . $index;
                    if ($ext != '') {
                        $uniqueFileName .= '.' . $ext;
                    }
                    $destinationPath = $uploadDir . $uniqueFileName;
                    if (move_uploaded_file($tmpName, $destinationPath)) {
                        $saveFileIds[] = $fileId;
                        $saveFileNames[] = $fileLabel;
                        $saveFiles[] = $uniqueFileName;
                        $saveActualFileNames[] = $uploadName;
                    } else {
                        $errorMessage = 'Unable to upload one or more files.';
                        $hasValidationError = true;
                        break;
                    }
                } elseif ($storedFile != '') {
                    $saveFileIds[] = $fileId;
                    $saveFileNames[] = $fileLabel;
                    $saveFiles[] = $storedFile;
                    $saveActualFileNames[] = $actualFileName != '' ? $actualFileName : $storedFile;
                } else {
                    $errorMessage = 'File upload is required for each file row.';
                    $hasValidationError = true;
                    break;
                }
            }
        }

        if (!$hasValidationError && count((array)$saveFileNames) == 0) {
            $errorMessage = 'At least one valid file is required.';
            $hasValidationError = true;
        }

        if (!$hasValidationError) {
            $savePost = array(
                'rid' => $postRid,
                'document_type_id' => $documentTypeId,
                'document_name' => $documentName,
                'file_id' => $saveFileIds,
                'file_name' => $saveFileNames,
                'file' => $saveFiles,
                'actual_file_name' => $saveActualFileNames,
                'company_id' => $_SESSION['company_id'],
                'user_id' => $_SESSION['userid']
            );

            $saveUrl = API . "?mode=savedocumentmanagement&method=post&company_id=" . urlencode($_SESSION['company_id']) . "&user_id=" . urlencode($_SESSION['userid']);
            $saveAction = ($postRid > 0) ? 'update' : 'insert';
            $saveResponse = file_post_contents($saveUrl, $savePost, $saveAction);
            $saveResult = json_decode($saveResponse, true);

            if (is_array($saveResult) && ($saveResult['status'] ?? '') == 'sucess') {
                $successMsg = ($postRid > 0) ? 'Update successfully' : 'Insert successfully';
                if($_SESSION['admin_type'] == 1){
                    header("location:index.php?option=COM_DASHBOARD_DOCUMENT_MANAGEMENT");
                }else{
                    header("location:index.php?option=COM_DASHBOARD_DOCUMENT_MANAGEMENT_USER");
                }
                exit;
            }

            $errorMessage = $saveResult['message'] ?? 'Unable to save document.';
        }
    }

    $rid = $postRid;
    $isEdit = ($rid > 0);
}

$apiUrl = API . "?mode=getdocumentmanagementdata&method=get&company_id=" . urlencode($_SESSION['company_id']) . "&user_id=" . urlencode($_SESSION['userid']);
if ($rid > 0) {
    $apiUrl .= "&rid=" . $rid;
}
$apiResponse = callAPI($apiUrl);
$apiData = json_decode($apiResponse, true);
$apiData = is_array($apiData) ? $apiData : array();

$documentTypes = $apiData['document_types'] ?? array();
$documentData = $apiData['document_data'] ?? null;

$selectedTypeId = '';
$selectedDocumentName = '';
$fileRows = array(array('id' => '', 'file_name' => '', 'file' => '', 'actual_file_name' => ''));

if (isset($_POST['submit']) && $_POST['submit'] != '' && $errorMessage != '') {
    $selectedTypeId = $_POST['document_type_id'] ?? '';
    $selectedDocumentName = $_POST['document_name'] ?? '';
    $fileRows = array();
    $postedFileIds = $_POST['file_id'] ?? array();
    $postedFileNames = $_POST['file_name'] ?? array();
    $postedStoredFiles = $_POST['file'] ?? array();
    $postedActualFiles = $_POST['actual_file_name'] ?? array();
    if (count((array)$postedFileNames) > 0) {
        foreach ($postedFileNames as $idx => $postedName) {
            $fileRows[] = array(
                'id' => $postedFileIds[$idx] ?? '',
                'file_name' => $postedName,
                'file' => $postedStoredFiles[$idx] ?? '',
                'actual_file_name' => $postedActualFiles[$idx] ?? ''
            );
        }
    }
    if (count((array)$fileRows) == 0) {
        $fileRows[] = array('id' => '', 'file_name' => '', 'file' => '', 'actual_file_name' => '');
    }
} elseif (is_array($documentData) && count((array)$documentData) > 0) {
    $selectedTypeId = $documentData['document_type_id'] ?? '';
    $selectedDocumentName = $documentData['document_name'] ?? '';
    $existingFiles = $documentData['files'] ?? array();
    $fileRows = array();
    if (count((array)$existingFiles) > 0) {
        foreach ($existingFiles as $fileRow) {
            $fileRow = is_array($fileRow) ? $fileRow : (array)$fileRow;
            $fileRows[] = array(
                'id' => $fileRow['id'] ?? '',
                'file_name' => $fileRow['file_name'] ?? '',
                'file' => $fileRow['file'] ?? '',
                'actual_file_name' => $fileRow['actual_file_name'] ?? ''
            );
        }
    }
    if (count((array)$fileRows) == 0) {
        $fileRows[] = array('id' => '', 'file_name' => '', 'file' => '', 'actual_file_name' => '');
    }
}
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $isEdit ? 'Edit Document Management' : 'Add Document Management'; ?></h5>
            <div class="card-header-right">
                <a href="index.php?option=<?php echo dmText($listOption); ?>" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-list"></i> List
                </a>

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <?php
            $workflowsteps = array();
            if ($rid > 0) {
                $currentStatus = isset($documentData['current_status']) ? (int)$documentData['current_status'] : 0;
                $workflowsteps = getWorkflowStatus( TBL_DOCUMENT_MANAGEMENT, $rid, $currentStatus);
                // print_r($workflowsteps);
            }
            ?>

            <?php if ($rid > 0) { ?>
                <?php echo $workflowsteps['current_status_ribbon']; ?>
            <?php } ?>

            <?php if ($errorMessage != '') { ?>
                <div class="alert alert-danger"><?php echo dmText($errorMessage); ?></div>
            <?php } ?>

            <?php if ($rid > 0) { ?>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <?php echo $workflowsteps['breadcrumb']; ?>
                    </div>
                    <div class="col-lg-6 text-right">
                        <?php echo $workflowsteps['workflow_btn']; ?>
                    </div>
                </div>
            <?php } ?>

            <?php $disabled = '';
            $arr = array(ST_DRAFT,ST_ACTIVE,ST_SEND_BACK,ST_CALLBACK,""); 
             if($rid>0 && !in_array($currentStatus, $arr)){
                $disabled = 'event_disabled';
            } ?>

            <form method="post" action="" enctype="multipart/form-data" id="documentManagementForm" autocomplete="off" class="<?php echo $disabled; ?>">
                <input type="hidden" name="rid" value="<?php echo (int)$rid; ?>">

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Document Type <span class="text-danger">*</span></label>
                        <select name="document_type_id" class="form-control js-example-basic-single" required>
                            <option value="">Select Document Type</option>
                            <?php if (count((array)$documentTypes) > 0) {
                                foreach ($documentTypes as $typeRow) {
                                    $typeRow = is_array($typeRow) ? $typeRow : (array)$typeRow;
                                    $typeId = $typeRow['id'] ?? '';
                                    $typeName = $typeRow['type_name'] ?? '';
                            ?>
                                    <option value="<?php echo dmText($typeId); ?>" <?php echo ($selectedTypeId != '' && $selectedTypeId == $typeId) ? 'selected' : ''; ?>>
                                        <?php echo dmText($typeName); ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Document Name <span class="text-danger">*</span></label>
                        <input type="text" name="document_name" class="form-control" value="<?php echo dmText($selectedDocumentName); ?>" required>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="d-block mb-2">Files <span class="text-danger">*</span></label>
                        <div id="documentFileRows">
                            <?php if (count((array)$fileRows) > 0) {
                                foreach ($fileRows as $fileIndex => $fileRow) {
                                    $fileRow = is_array($fileRow) ? $fileRow : (array)$fileRow;
                                    $hasStoredFile = ($fileRow['file'] ?? '') != '';
                            ?>
                                    <div class="row document-file-row mb-2">
                                        <div class="col-md-4 form-group">
                                            <input type="hidden" name="file_id[]" value="<?php echo dmText($fileRow['id'] ?? ''); ?>">
                                            <input type="text" name="file_name[]" class="form-control" placeholder="File Name" value="<?php echo dmText($fileRow['file_name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <input type="file" name="document_file[]" class="form-control" <?php echo $hasStoredFile ? '' : 'required'; ?>>
                                            <input type="hidden" name="file[]" value="<?php echo dmText($fileRow['file'] ?? ''); ?>">
                                            <input type="hidden" name="actual_file_name[]" value="<?php echo dmText($fileRow['actual_file_name'] ?? ''); ?>">
                                            <?php if ($hasStoredFile) { ?>
                                                <small class="text-muted d-block mt-1">
                                                    Current:
                                                    <a target="_blank" href="<?php echo dmText($uploadDir . ($fileRow['file'] ?? '')); ?>" download>
                                                        <?php echo dmText($fileRow['actual_file_name'] ?? $fileRow['file']); ?>
                                                    </a>
                                                </small>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <button type="button" class="btn btn-danger btn-remove-file-row">Remove</button>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                        <button type="button" class="btn btn-primary" id="addDocumentFileRow">+ Add More</button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" name="submit" value="<?php echo $isEdit ? 'update' : 'insert'; ?>" class="btn btn-success">
                            Save
                        </button>
                        <a href="index.php?option=<?php echo dmText($listOption); ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>

            <div class="row mt-4">

                        
                            
                            <?php 
                            echo $todisplaythree = todisplaythree(array('table'=>TBL_DOCUMENT_MANAGEMENT,'originator'=>$documentData['created_by'],'encode'=>'no','id'=>$rid,'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'no','dis_track'=>'no'));  
 
                             ?>
                        

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // -----------------------------
    // Workflow button handlers (same contract/CSR engine)
    // -----------------------------
    $(document).on("click", ".submitDynamicWorkflowPopUp", function () {
        var table_name = $(this).data("table_name");
        var transaction_id = $(this).data("transaction_id");
        var current_status = $(this).data("current_status");
        var next_status = $(this).data("next_status");
        submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status);
    });

    $(document).on("click", ".submitDynamicWorkflow", function () {
        var table_name = $(this).data("table_name");
        var transaction_id = $(this).data("transaction_id");
        var current_status = $(this).data("current_status");
        var next_status = $(this).data("next_status");
        var old_status_name = $(this).data("old_status_name");
        // Some configs generate `new_status_name` without `data-` prefix.
        var new_status_name = $(this).data("new_status_name") || $(this).attr("new_status_name");
        submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name);
    });

    function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data: 'table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
            type: "POST",
            success: function (data) {
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error: function () {
            }
        });
    }

    function submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name) {
        swal({
            title: "<?php echo ucwords((string)($_SESSION['config']['name'] ?? '')); ?>",
            text: "Please note the status of the document will change to " + new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function () {
            jQuery.ajax({
                url: "ajax.php",
                data: 'page=submitstatusdynamicworkflow&table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
                type: "POST",
                success: function (response) {
                    var res = JSON.parse(response);
                    window.location.href = window.location.href;
                    if (res.transaction_update.status == 'sucess') {
                        window.location.href = window.location.href;
                        swal({
                            title: "<?php echo ucwords((string)($_SESSION['config']['name'] ?? '')); ?>",
                            text: "<?php echo (string)($_SESSION['error_message']['9059'] ?? ''); ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.href = window.location.href;
                        });
                    } else {
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error: function () {
                }
            });
        });
    }

    function bindDocumentFileRowEvents() {
        $('.btn-remove-file-row').off('click').on('click', function () {
            var rows = $('#documentFileRows .document-file-row');
            if (rows.length > 1) {
                $(this).closest('.document-file-row').remove();
            } else {
                var row = rows.first();
                row.find('input[type="text"]').val('');
                row.find('input[type="file"]').val('').prop('required', true);
                row.find('input[name="file_id[]"]').val('');
                row.find('input[name="file[]"]').val('');
                row.find('input[name="actual_file_name[]"]').val('');
                row.find('small').remove();
            }
        });
    }

    function addDocumentFileRow() {
        var rowHtml = ''
            + '<div class="row document-file-row mb-2">'
            + '<div class="col-md-4 form-group">'
            + '<input type="hidden" name="file_id[]" value="">'
            + '<input type="text" name="file_name[]" class="form-control" placeholder="File Name" required>'
            + '</div>'
            + '<div class="col-md-4 form-group">'
            + '<input type="file" name="document_file[]" class="form-control" required>'
            + '<input type="hidden" name="file[]" value="">'
            + '<input type="hidden" name="actual_file_name[]" value="">'
            + '</div>'
            + '<div class="col-md-2 form-group">'
            + '<button type="button" class="btn btn-danger btn-remove-file-row">Remove</button>'
            + '</div>'
            + '</div>';
        $('#documentFileRows').append(rowHtml);
        bindDocumentFileRowEvents();
    }

    $(document).ready(function () {
        var selectedTypeId = <?php echo json_encode((string)$selectedTypeId); ?>;
        if (selectedTypeId != '') {
            $('select[name="document_type_id"]').val(selectedTypeId).trigger('change');
        }

        $('#documentManagementForm').on('submit', function () {
            var typeVal = $('select[name="document_type_id"]').val();
            if (typeVal == '' || typeVal == null) {
                typeVal = $('select[name="document_type_id"] option:selected').val();
            }
            if (typeVal != '' && typeVal != null) {
                $('select[name="document_type_id"]').val(typeVal);
            }
        });

        bindDocumentFileRowEvents();
        $('#addDocumentFileRow').on('click', function () {
            addDocumentFileRow();
        });
    });
</script>
