<?php
$_SESSION['apply_js']['multiselest'] = 1;

$listOption = 'COM_LIST_DOCUMENT_MANAGEMENT';
$insertOption = 'COM_INSERT_DOCUMENT_MANAGEMENT';

function dmListText($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function dmListPageUrl($params)
{
    $query = http_build_query($params);
    return 'index.php?' . $query;
}

$documentTypeId = isset($_GET['document_type_id']) ? (int)$_GET['document_type_id'] : 0;
$documentName = isset($_GET['document_name']) ? trim((string)$_GET['document_name']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) {
    $page = 1;
}

$listUrl = API . "?mode=getdocumentmanagementlist&method=get"
    . "&company_id=" . urlencode($_SESSION['company_id'])
    . "&user_id=" . urlencode($_SESSION['userid'])
    . "&page=" . $page
    . "&document_type_id=" . $documentTypeId
    . "&document_name=" . urlencode($documentName);
$listResponse = callAPI($listUrl);
$listData = json_decode($listResponse, true);
// print_r($listData); // Debugging line to check the API response
$listData = is_array($listData) ? $listData : array();

$documentTypes = $listData['document_types'] ?? array();
$rows = $listData['rows'] ?? array();
$total = (int)($listData['total'] ?? 0);
$page = max(1, (int)($listData['page'] ?? $page));
$limit = max(1, (int)($listData['limit'] ?? 20));
$totalPages = (int)($listData['total_pages'] ?? 0);

$successMessage = isset($_GET['msg']) ? trim((string)$_GET['msg']) : '';
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Document Management</h5>
            <div class="card-header-right">
                <a href="index.php?option=<?php echo dmListText($insertOption); ?>" class="btn btn-success waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-plus"></i> Add
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="get" action="index.php" class="mb-4">
                <input type="hidden" name="option" value="<?php echo dmListText($listOption); ?>">
                <div class="row">
                    <div class="col-lg-3 col-md-6 form-group">
                        <label>Document Type</label>
                        <select name="document_type_id" class="form-control js-example-basic-single">
                            <option value="">All Types</option>
                            <?php if (count((array)$documentTypes) > 0) {
                                foreach ($documentTypes as $typeRow) {
                                    $typeRow = is_array($typeRow) ? $typeRow : (array)$typeRow;
                                    $typeId = (int)($typeRow['id'] ?? 0);
                                    $typeName = $typeRow['type_name'] ?? '';
                            ?>
                                    <option value="<?php echo $typeId; ?>" <?php echo ($documentTypeId == $typeId) ? 'selected' : ''; ?>>
                                        <?php echo dmListText($typeName); ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 form-group">
                        <label>Document Name</label>
                        <input type="text" name="document_name" class="form-control" value="<?php echo dmListText($documentName); ?>" placeholder="Search document name">
                    </div>
                    <div class="col-lg-3 col-md-6 form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">Search</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Document Type</th>
                            <th>Document Name</th>
                            <th>Files</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$rows) > 0) {
                            $serial = (($page - 1) * $limit) + 1;
                            foreach ($rows as $row) {
                                $row = is_array($row) ? $row : (array)$row;
                                $editRid = deocdeId('id:' . ($row['id'] ?? 0));
                                $createdDate = !empty($row['created_date']) ? fromDate($row['created_date']) : '-';
                        ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><?php echo dmListText($row['type_name'] ?? '-'); ?></td>
                                    <td><?php echo dmListText($row['document_name'] ?? '-'); ?></td>
                                    <td><?php echo (int)($row['file_count'] ?? 0); ?></td>
                                    <td><?php echo dmListText($createdDate); ?></td>
                                    <td>
                                        <a href="index.php?option=<?php echo dmListText($insertOption); ?>&Rid=<?php echo urlencode($editRid); ?>" class="btn btn-primary btn-sm">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                        <?php
                                $serial++;
                            }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No documents found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1) { ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-muted">
                        Showing page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $total; ?> records)
                    </span>
                    <div>
                        <?php if ($page > 1) {
                            $prevParams = array(
                                'option' => $listOption,
                                'page' => $page - 1
                            );
                            if ($documentTypeId > 0) {
                                $prevParams['document_type_id'] = $documentTypeId;
                            }
                            if ($documentName != '') {
                                $prevParams['document_name'] = $documentName;
                            }
                        ?>
                            <a href="<?php echo dmListText(dmListPageUrl($prevParams)); ?>" class="btn btn-outline-primary btn-sm">Previous</a>
                        <?php } ?>
                        <?php if ($page < $totalPages) {
                            $nextParams = array(
                                'option' => $listOption,
                                'page' => $page + 1
                            );
                            if ($documentTypeId > 0) {
                                $nextParams['document_type_id'] = $documentTypeId;
                            }
                            if ($documentName != '') {
                                $nextParams['document_name'] = $documentName;
                            }
                        ?>
                            <a href="<?php echo dmListText(dmListPageUrl($nextParams)); ?>" class="btn btn-outline-primary btn-sm ms-2">Next</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- <?php if ($successMessage != '') { ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            tostarnotify(
                'top',
                'right',
                'fa fa-thumbs-up',
                'form-label label label-success',
                'animated flipInX',
                'animated flipOutX',
                <?php echo json_encode($successMessage); ?>,
                ''
            );
        });
    </script>
<?php } ?> -->
