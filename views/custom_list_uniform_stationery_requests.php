<?php
$url = API . "?mode=getStationeryRequestList"
            . "&company_id=" . $_SESSION['company_id']
            . "&user_id=" . $_SESSION['userid'];

$res = callAPI($url);
$data = json_decode($res, true);
$rows = $data['requests'] ?? [];
?>

<style>
.action-icons i {
    font-size: 18px;
    cursor: pointer;
    margin-right: 12px;
}
.action-icons i:hover {
    transform: scale(1.2);
}
.section-title {
    font-size: 17px;
    font-weight: 700;
    color: #4B49AC;
    margin-bottom: 15px;
    border-left: 4px solid #4B49AC;
    padding-left: 10px;
}
</style>

<div class="page-body">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
        <div class="section-title">Stationery Requests</div>

            <a href="custom_insert_uniform.php" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> New Request
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Request No</th>
                        <th scope="col">Employee</th>
                        <th scope="col">Location</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Required Date</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="width: 90px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rows)) {
                        foreach ($rows as $r) { ?>

                        <tr id="row<?= $r['id'] ?>">
                            <td><?= $r['request_no'] ?></td>
                            <td><?= $r['employee_name'] ?></td>
                            <td><?= $r['location_name'] ?></td>
                            <td><?= fromDate($r['request_date']) ?></td>
                            <td><?= fromDate($r['require_date']) ?></td>
                            <td><span class="badge badge-info"><?= $r['status_text'] ?></span></td>

                            <td class="action-icons">
                                <!-- Edit / View -->
                                <i class="fa fa-eye text-primary"
                                   onclick="viewRequest('<?= deocdeId('id:'.$r['id']) ?>>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                                <!-- Delete -->
                                <i class="fa fa-trash text-danger"
                                   onclick="deleteRequest(<?= $r['id'] ?>>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            </td>
                        </tr>

                    <?php }
                    } else { ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No Records Found
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>

            </table>
            </div>
        </div>

    </div>

</div>

<!-- JS Functions -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

<script>
function viewRequest(id) {
    window.location.href = "index.php?option=COM_VIEW_UNIFORM_STATIONERY_REQUESTS&Rid=" + id;
}

function deleteRequest(id) {

    Swal.fire({
        title: "Delete Request?",
        text: "Are you sure you want to delete this request?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete"
    })
    .then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: "ajax/uniform_ajax.php",
                type: "POST",
                data: {
                    page: "deleteStationeryRequest",
                    rid: id
                },
                success: function(res) {
                    let r = JSON.parse(res);

                    if (r.status === "success") {
                        Swal.fire("Deleted!", r.msg, "success");

                        // optional remove row instantly
                        $("#row" + id).fadeOut();
                    } else {
                        Swal.fire("Error!", r.msg, "error");
                    }
                }
            });

        }
    });

}
</script>
