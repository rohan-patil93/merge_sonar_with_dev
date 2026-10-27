<?php
$_SESSION['apply_js']['table'] = 1;

$notification = array('message' => '', 'type' => '');
if (isset($_GET['msg']) && $_GET['msg'] != '') {
    $notification['message'] = urldecode((string) $_GET['msg']);
    $notification['type'] = (isset($_GET['msgtype']) && $_GET['msgtype'] == 'danger') ? 'danger' : 'success';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $deleteRid = isset($_POST['rid']) ? (int) $_POST['rid'] : 0;
    if ($deleteRid > 0) {
        $delUrl = API . "?mode=softdeleteuserassignedcontracttypes&method=post";
        $delRes = json_decode(file_post_contents($delUrl, array('rid' => $deleteRid), 'update'), true);
        if (isset($delRes['status']) && $delRes['status'] == 'sucess') {
            $error = $delRes['message'] ?? 'Assignment deleted successfully.';
            header('Location: index.php?option=COM_LIST_USER_ASSIGNED_CONTRACT_TYPES');
            exit;
        }
        $errMsg = $delRes['message'] ?? 'Delete failed.';
        header('Location: index.php?option=COM_LIST_USER_ASSIGNED_CONTRACT_TYPES');
        exit;
    }
}

$listUrl = API . "?mode=getuserassignedcontracttypeslist&method=get";
$listRes = json_decode(callAPI($listUrl), true);
$listRows = array();
if (is_array($listRes) && isset($listRes['list']) && is_array($listRes['list'])) {
    $listRows = $listRes['list'];
}
$title = 'User Assigned Contract Types';
?>
<style>
.uact-list-actions form { display: inline-block; margin: 0; }
.uact-list-actions .btn { margin-right: 4px; }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<div class="pcoded-inner-content mob-pd">
    <div class="main-body">
        <div class="page-wrapper mob-pd p-0">
            <div class="page-body">
                <div class="card">
                    <div class="card-header table-card-header">
                        <h5><?php echo htmlspecialchars($title); ?></h5>
                        <div class="card-header-right table-card-header col-lg-6 text-right">
                            <a href="index.php?option=COM_INSERT_USER_ASSIGNED_CONTRACT_TYPES" class="btn btn-success mb-2 desktop-view">
                                <i class="icofont icofont-plus icon-white"></i>
                                Assign Types
                            </a>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-block table-responsive">
                        <table id="basic-btn" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>User</th>
                                    <th>Assigned Types</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array) $listRows) > 0) {
                                    foreach ($listRows as $row) {
                                        $rowArr = is_array($row) ? $row : json_decode(json_encode($row), true);
                                        $rowId = (int) ($rowArr['id'] ?? 0);
                                        $editRid = deocdeId('id:' . $rowId);
                                        ?>
                                        <tr>
                                            <td><?php echo $rowId; ?></td>
                                            <td><?php echo htmlspecialchars((string) ($rowArr['user_name'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars((string) ($rowArr['type_names'] ?? '')); ?></td>
                                            <td class="uact-list-actions">
                                                <a href="index.php?option=COM_INSERT_USER_ASSIGNED_CONTRACT_TYPES&Rid=<?php echo htmlspecialchars($editRid); ?>" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="post" action="" class="js-uact-delete-form">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="rid" value="<?php echo $rowId; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No assignments found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var msg = <?php echo json_encode((string) ($notification['message'] ?? '')); ?>;
    var msgType = <?php echo json_encode((string) ($notification['type'] ?? 'success')); ?>;
    if (msg && typeof Toastify !== 'undefined') {
        Toastify({
            text: msg,
            duration: 3500,
            gravity: 'top',
            position: 'right',
            backgroundColor: (msgType === 'danger') ? '#dc3545' : '#28a745'
        }).showToast();
    }

    var forms = document.querySelectorAll('.js-uact-delete-form');
    if (forms && forms.length) {
        for (var i = 0; i < forms.length; i++) {
            forms[i].addEventListener('submit', function (e) {
                e.preventDefault();
                var form = this;
                if (typeof swal !== 'function') {
                    if (window.confirm('Delete this assignment?')) {
                        form.submit();
                    }
                    return false;
                }
                swal({
                    title: 'Are you sure?',
                    text: 'Delete this assignment?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'No',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        form.submit();
                    }
                });
                return false;
            });
        }
    }
})();
</script>
