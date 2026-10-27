<?php
$_SESSION['apply_js']['multiselest'] = 1;
$location_id = $_SESSION['location_id'] ?? 1;

$data = array();
if (!in_array('UNIFORM_ADMIN', $_SESSION['role_ids'])) {
    $_SESSION['config']['job_grade'] = "Y";
    $url = API . "?mode=getEmployeeUniformData"
        . "&company_id=" . $_SESSION['company_id']
        . "&user_id=" . $_SESSION['userid']
        . "&config_jobgrade=" . $_SESSION['config']['job_grade'];

    $response = callAPI($url);
    $data = json_decode($response, true);
}
$employee      = $data['employee'];

$colors = array();
foreach ((array)$data['colors'] as $row) {
    $colors[$row['genc_detail_code']] = $row['color_name'];
}

$uniform_types = $data['uniform_types'];
$sizes         = $data['sizes'];
$job_grades    = array();
foreach ((array)$data['job_grades'] as $row) {
    $job_grades[$row['id']] = $row;
}
$emp_uniforms  = $data['employee_uniforms'];
$locations = array();
foreach ((array)$data['locations'] as $row) {
    $locations[$row['id']] = $row['location_name'];
}
?>

<style>
    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: #4B49AC;
        margin-bottom: 15px;
        border-left: 4px solid #4B49AC;
        padding-left: 10px;
    }

    .uniform-tabs .nav-link {
        font-weight: 600;
        font-size: 14px;
        padding: 10px 18px;
        border-radius: 8px;
        color: #67748e;
        background: #f1f5f9;
        margin-right: 8px;
        transition: 0.25s;
    }

    .uniform-tabs .nav-link:hover {
        background: #e2e8f0;
        color: #333;
    }

    .uniform-tabs .nav-link.active {
        background: #4B49AC;
        color: #fff;
        box-shadow: 0px 4px 12px rgba(75, 73, 172, 0.4);
        transform: translateY(-2px);
    }
</style>
<style>
    .item-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        background: #fff;
        transition: 0.2s;

    }

    .item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .item-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }

    .badge-uniform_type {
        background: #fee8e8;
        color: #e81a1a;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .badge-color {
        background: #e8f0fe;
        color: #1a73e8;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .badge-size {
        background: #fff4e6;
        color: #d17936;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .qty-box {
        font-size: 24px;
        font-weight: 700;
        color: #4B49AC;
    }
</style>
<style>
    .cart-item {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        background: #f8f9fa;
    }

    .cart-item-title {
        font-size: 15px;
        font-weight: 700;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        margin-top: 33px;
    }

    .qty-btn {
        width: 38px;
        height: 36px;
        border: none;
        border-radius: 50%;
        font-weight: bold;
    }

    .qty-display {
        width: 40px;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
    }

    .remove-item {
        color: red;
        cursor: pointer;
        font-size: 12px;
    }
    @media (max-width:575px){
        .uniform-tabs-open{
            display: block !important;
            position: static !important;
            float: none !important;
            margin-top: 20px;
        }
    }

</style>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0 text-primary">Uniform Stock</h4>
            <div class="card-header-right uniform-tabs-open">
                <ul class="nav nav-pills uniform-tabs" id="uniformTabs">
                    <li class="nav-item">
                        <a class="nav-link" id="tabNewItem" href="javascript:void(0)">
                            <i class="fa fa-plus-circle me-2"></i> New Item
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="tabTopupItem" href="javascript:void(0)">
                            <i class="fa fa-level-up-alt me-2"></i> Topup Item
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="card-body">

            <?php if (in_array('UNIFORM_ADMIN', $_SESSION['role_ids'])) { ?>
                <style>
                    #adminFilterSection select {
                        background: #ffffff;
                        border-radius: 8px;
                        font-size: 14px;
                    }
                </style>

                <div class="card mb-3" id="adminFilterSection">
                    <div class="card-header ">
                        <div class="section-title">Admin: Uniform Search Filters</div>

                    </div>

                    <div class="card-body">

                        <div class="row">
                            <input type="hidden" name="config_jobgrade" id="config_jobgrade" value="<?php echo $_SESSION['config']['job_grade'] ?? ''; ?>">
                            <!-- LOCATION -->
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Location <span class="text-danger">*</span></span>
                                    <select aria-label="Admin Location" id="adminLocation" class="form-control js-example-basic-single">
                                        <option value="">-- Select Location --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- EMPLOYEE -->
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Employee <span class="text-danger">*</span></span>
                                    <select aria-label="Admin Employee" id="adminEmployee" class="form-control js-example-basic-single">
                                        <option value="">-- Select Employee --</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Uniform Type</span>
                                    <select aria-label="Admin Type" id="adminType" class="form-control js-example-basic-single">
                                        <option value="">-- All Types --</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <!-- SIZE -->
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Size</span>
                                    <select aria-label="Admin Size" id="adminSize" class="form-control js-example-basic-single">
                                        <option value="">-- All Sizes --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- COLOR -->
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Color</span>
                                    <select aria-label="Admin Color" id="adminColor" class="form-control js-example-basic-single">
                                        <option value="">-- All Colors --</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-4 col-md-4 col-sm-6 col-12 d-flex align-items-end">
                                <button class="btn btn-success w-100" id="adminSearchBtn">
                                    <i class="fa fa-search me-2"></i> Search Items
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                <script>
                    <?php if (in_array('UNIFORM_ADMIN', $_SESSION['role_ids'])) { ?>
                        loadAdminLocations();
                    <?php } ?>

                    function loadAdminLocations() {
                        $.ajax({
                            url: "ajax/uniform_ajax.php",
                            type: "POST",
                            data: {
                                page: "getAdminLocations"
                            },
                            success: function(response) {
                                let res = JSON.parse(response);
                                $("#adminLocation").html('<option value="">-- Select Location --</option>');
                                res.forEach(loc => {
                                    $("#adminLocation").append(`<option value="${loc.id}">${loc.location_name}</option>`);
                                });
                            }
                        });
                    }

                    $(document).ready(function() {
                        $("#adminLocation").change(function() {
                            let loc = $(this).val();
                            if (!loc) {
                                $("#adminEmployee").html('<option value="">-- Select Employee --</option>');
                                return;
                            }

                            $.ajax({
                                url: "ajax/uniform_ajax.php",
                                type: "POST",
                                data: {
                                    page: "getAdminEmployees",
                                    location_id: loc
                                },
                                success: function(response) {
                                    let res = JSON.parse(response);
                                    $("#adminEmployee").html('<option value="">-- Select Employee --</option>');
                                    res.forEach(emp => {
                                        $("#adminEmployee").append(`<option value="${emp.id}">${emp.display_name}</option>`);
                                    });
                                }
                            });
                        });

                        $("#adminEmployee").change(function() {
                            let emp_id = $(this).val();

                            if (!emp_id) return;

                            $.ajax({
                                url: "ajax/uniform_ajax.php",
                                type: "POST",
                                data: {
                                    page: "getAdminEmpUniforms",
                                    employee_id: emp_id
                                },
                                success: function(response) {
                                    let res = JSON.parse(response);

                                    // LOAD TYPES
                                    $("#adminType").html('<option value="">-- All Types --</option>');
                                    res.types.forEach(t => {
                                        $("#adminType").append(`<option value="${t.uniform_type_id}">${t.uniform_type_name}</option>`);
                                    });

                                    // LOAD SIZES
                                    $("#adminSize").html('<option value="">-- All Sizes --</option>');
                                    res.sizes.forEach(s => {
                                        $("#adminSize").append(`<option value="${s.size_id}">${s.size_name}</option>`);
                                    });

                                    // LOAD COLORS
                                    $("#adminColor").html('<option value="">-- All Colors --</option>');
                                    if (res.job_grade_color) {
                                        // Only allow one color
                                        $("#adminColor").append(`<option value="${res.job_grade_color}">${res.job_grade_color_name}</option>`);
                                    } else {
                                        res.colors.forEach(c => {
                                            $("#adminColor").append(`<option value="${c.color_id}">${c.color_name}</option>`);
                                        });
                                    }
                                }
                            });
                        });

                        $("#adminSearchBtn").click(function() {

                            let loc = $("#adminLocation").val();
                            let emp = $("#adminEmployee").val();

                            if (!loc) {
                                Swal.fire("Location Required", "Please select location", "warning");
                                return;
                            }

                            if (!emp) {
                                Swal.fire("Employee Required", "Please select employee", "warning");
                                return;
                            }

                            // NEXT → Call searchUniformAdmin() API (PART 2)
                            loadAdminSearchResults();
                        });

                    });

                    function loadAdminSearchResults() {

                        let location_id = $("#adminLocation").val();
                        let employee_id = $("#adminEmployee").val();
                        let type_id = $("#adminType").val();
                        let size_id = $("#adminSize").val();
                        let color_id = $("#adminColor").val();
                        let config_jobgrade = $("#config_jobgrade").val() || "";

                        $("#itemGrid").html("<div class='text-center p-3'>Loading...</div>");

                        $.ajax({
                            url: "ajax/uniform_ajax.php",
                            type: "POST",
                            data: {
                                page: "searchUniformAdmin",
                                location_id: location_id,
                                employee_id: employee_id,
                                type_id: type_id,
                                size_id: size_id,
                                color_id: color_id,
                                config_jobgrade: config_jobgrade
                            },
                            success: function(res) {

                                let r = JSON.parse(res);

                                if (r.status !== "success") {
                                    $("#itemGrid").html(`
                                    <div class="text-center">
                                    <p class="text-muted f-16 fw-bold m-b-30">
                                        <i class="fa fa-warning"></i>No items found.
                                    </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>`);
                                    return;
                                }

                                let items = r.items;

                                if (items.length === 0) {
                                    $("#itemGrid").html(`<div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i>No items found.
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>`);
                                    return;
                                }

                                let html = "";

                                items.forEach(row => {

                                    html += `
                                <div class="col-md-4 mb-3 item-card-wrapper"
                                    data-search="${(row.item_name + ' ' + row.color_name + ' ' + row.size_name).toLowerCase()}">

                                    <div class="item-card">

                                        <div class="item-title">${row.item_name}</div>
                                        <div class="mt-2">
                                            <span class="badge-uniform_type">Uniform Type: ${row.uniform_type_name}</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge-color">Color: ${row.color_name}</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge-size">Size:${row.size_name}</span>
                                        </div>

                                        <div class="qty-box mt-2">${row.available_qty}</div>
                                        <div class="text-muted" style="font-size:12px">Available Qty</div>

                                        <button class="btn btn-primary w-100 mt-3 addToCartBtn"
                                            data-id="${row.id}"
                                            data-name="${row.item_name.replace(/"/g, '&quot;')}"
                                            data-color="${row.color_name}"
                                            data-color_id="${row.color_id}"
                                            data-size="${row.size_name}"
                                            data-size_id="${row.size_id}"
                                            data-available="${row.available_qty}"
                                            data-location="${row.location_id}"
                                            data-uniform_type_id="${row.uniform_type_id}">
                                            Add
                                        </button>

                                    </div>
                                </div>`;
                                });

                                $("#itemGrid").html(html);
                            }
                        });
                    }
                </script>
            <?php } ?>

            <div id="uniformContent">

                <div class="row">

                    <!-- LEFT SIDE — AVAILABLE ITEMS -->
                    <div class="col-lg-7 col-md-12 col-sm-12 col-12">
                        <div class="section-title">Available Uniform Items</div>

                        <div class="mb-3">
                            <input aria-label="Search item, color, size..." type="text" id="searchItem" class="form-control" placeholder="Search item, color, size...">
                        </div>

                        <div class="row" id="itemGrid">

                            <?php
                            $items = $data['stationery_items'] ?? array();
                            if (!empty($items)) {
                                foreach ($items as $row) { ?>

                                    <div class="col-md-4 mb-3 item-card-wrapper"
                                        data-search="<?= strtolower($row['item_name'] . ' ' . $row['color_name'] . ' ' . $row['size_name']) ?>">

                                        <div class="item-card">

                                            <div class="item-title"><?= $row['item_name'] ?></div>
                                            <div class="mt-2">
                                                <span class="badge-uniform_type">Uniform Type: <?= $row['uniform_type_name'] ?></span>
                                            </div>

                                            <?php if ($_SESSION['config']['job_grade'] == "Y") { ?>
                                                <div class="mt-2">
                                                    <span class="badge-color">Color: <?= $row['color_name'] ?></span>
                                                </div>
                                            <?php } ?>

                                            <div class="mt-2">
                                                <span class="badge-size">Size: <?= $row['size_name'] ?></span>
                                            </div>

                                            <div class="qty-box mt-2"><?= $row['available_qty'] ?></div>
                                            <div class="text-muted" style="font-size:12px">Available Qty</div>

                                            <button class="btn btn-primary w-100 mt-3 addToCartBtn"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= addslashes($row['item_name']) ?>"
                                                data-color="<?= $row['color_name'] ?>"
                                                data-color_id="<?= $row['color_id'] ?>"
                                                data-size_id="<?= $row['size_id'] ?>"
                                                data-size="<?= $row['size_name'] ?>"
                                                data-uniform_type_id="<?= $row['uniform_type_id'] ?>"
                                                data-available="<?= $row['available_qty'] ?>"
                                                data-location="<?= $row['location_id'] ?>">Add</button>

                                        </div>
                                    </div>

                                <?php }
                            } else { ?>

                                <div class="col-12  text-muted">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i>No uniform items available.
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>
                        <div id="noSearchResults" class="col-12 text-center d-none">
                            <p class="text-muted f-16 fw-bold m-b-30">
                                <i class="fa fa-warning"></i> No items Matching.
                            </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE — CART -->
                    <div class="col-lg-5 col-md-12 col-sm-12 col-12">
 <?php if (!in_array('UNIFORM_ADMIN', $_SESSION['role_ids'])) { ?>


<div class="card shadow-sm mb-4">
    <div class="section-title">Employee Information</div>

    <div class="card-body">


            <div class="form-label fw-bold text-secondary">Employee Name: <span class="form-label fw-bold text-secondary fs-5"><?= $data['employee']['display_name'] ?></span></div>

            <div class="form-label fw-bold text-secondary">Location: <span class="form-label fw-bold text-secondary fs-6">
                <?= $locations[$data['employee']['location_id']] ?? '' ?></span>
            </div>

            <div class="form-label fw-bold text-secondary">Job Grade :
            <?php if (!empty($data['employee']['job_grade_id'])) { ?>
                <span class=" fw-bold text-secondary fs-6">
                    <?php $job_color_id = $job_grades[$data['employee']['job_grade_id']]['uniform_color'];
                    echo $colors[$job_color_id] ?? ''; ?> (Color Code)
                </span>
            <?php } else { ?>

            <?php } ?>
            </div>

            <div class="form-label fw-bold text-secondary">Uniform Eligibility:
            <div class="mt-2">
            <?php if (!empty($data['employee_uniforms'])) { ?>
                <ul class="list-group">

                    <?php foreach ($data['employee_uniforms'] as $u) { ?>
                        <li class="fw-bold text-secondary list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= $u['uniform_type_name'] ?></strong>
                                <div class="text-muted small"><?= $u['size_name'] ?></div>
                            </div>
                        </li>
                    <?php } ?>

                </ul>
            <?php } else { ?>

            <?php } ?>
        </div>
        <i class="fa fa-edit text-primary"
           id="editEmpUniformBtn"
           style="cursor:pointer; font-size:14px;">
        </i>

        </div>

    </div>
</div>

<div class="modal fade" id="empUniformModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Job Grade & Uniform Types</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <!-- JOB GRADE -->
                <h6 class="text-primary">Job Grade</h6>
                <select aria-label="Job Grade Dropdown" id="jobGradeDropdown" class="form-control mb-4 js-example-basic-single">
                    <option value="">-- Select Job Grade --</option>

                    <?php foreach ($job_grades as $jg) { ?>
                        <option value="<?= $jg['id'] ?>"
                            <?= ($jg['id'] == $data['employee']['job_grade_id']) ? 'selected' : '' ?>>
                            <?= $colors[$jg['uniform_color']] ?>
                        </option>
                    <?php } ?>
                </select>
                <hr>

                <h6 class="text-primary">Uniform Types</h6>
                <div id="existingUniformList">

                    <?php foreach ($emp_uniforms as $u) { ?>
                    <div class="card p-2 mb-2 uniformRow" >

                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" class="existingId" value="<?= $u['id'] ?>">
                            <input type="hidden" class="typeField" value="<?= $u['uniform_type_id'] ?>">

                            <div>
                                <strong><?= $u['uniform_type_name'] ?></strong><br>
                                <small>Size:</small>

                                <select aria-label="Select option" class="form-control sizeField js-example-basic-single"
                                        data-id="<?= $u['id'] ?>"
                                        style="width:150px; display:inline-block;">

                                    <?php foreach ($sizes as $s) { ?>
                                        <option value="<?= $s['genc_detail_code'] ?>"
                                            <?= ($s['genc_detail_code'] == $u['size_id']) ? 'selected' : '' ?>>
                                            <?= $s['size_name'] ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <i class="fa fa-trash text-danger removeUniform"
                               data-id="<?= $u['id'] ?>"
                               style="font-size:20px;cursor:pointer;"></i>

                        </div>
                    </div>
                    <?php } ?>

                </div>

                <hr>


                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                        <select aria-label="New Uniform Type" id="newUniformType" class="form-control js-example-basic-single">
                            <option value="">-- Select Uniform Type --</option>
                            <?php foreach ($uniform_types as $ut) { ?>
                            <option value="<?= $ut['id'] ?>"><?= $ut['uniform_type_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                        <select aria-label="New Uniform Size" id="newUniformSize" class="form-control js-example-basic-single">
                            <option value="">-- Select Size --</option>
                            <?php foreach ($sizes as $s) { ?>
                            <option value="<?= $s['genc_detail_code'] ?>"><?= $s['size_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                        <button class="btn btn-success w-100" id="addUniformTypeBtn">+ Add</button>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="saveUniformDataBtn">Save Changes</button>
            </div>

        </div>
    </div>
</div>
<script>
    $("#editEmpUniformBtn").click(function(){
        $('.js-example-basic-single').select2();
        $("#empUniformModal").modal("show");
    });
    $("#addUniformTypeBtn").click(function() {

        let type_id  = $("#newUniformType").val();
        let typeText = $("#newUniformType option:selected").text();
        let size_id  = $("#newUniformSize").val();
        let sizeText = $("#newUniformSize option:selected").text();

        if (!type_id || !size_id) {
            Swal.fire("Error", "Please select both Uniform Type and Size", "warning");
            return;
        }

        // Build HTML block using PHP options
        let html = `
        <div class="card p-2 mb-2 uniformRow">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <strong>${typeText}</strong><br>


                    <input type="hidden" class="typeField" value="${type_id}">

                    <small>Size:</small>

                    <select aria-label="Select option" class="form-control sizeField js-example-basic-single" style="width:150px;display:inline-block;">

                        <?php foreach ($sizes as $s) { ?>
                            <option value="<?= $s['genc_detail_code'] ?>">
                                <?= $s['size_name'] ?>
                            </option>
                        <?php } ?>

                    </select>

                </div>

                <i class="fa fa-trash text-danger removeUniform"
                   style="cursor:pointer;font-size:20px;"></i>

            </div>

        </div>
        `;

        // Add to list
        $("#existingUniformList").append(html);

        // Preselect the chosen size
        $("#existingUniformList .uniformRow:last .sizeField").val(size_id);
        $('.js-example-basic-single').select2();

    });
    let deletedUniforms = [];
    $(document).on("click", ".removeUniform", function () {

        let $row = $(this).closest(".uniformRow");
        let existingId = $row.find(".existingId").val();

        if (existingId) {
            deletedUniforms.push(existingId); // will be sent to backend
        }

        $row.remove();
    });

    $("#saveUniformDataBtn").click(function () {

        let job_grade_id = $("#jobGradeDropdown").val();
        let employee_id  = "<?= $employee['id'] ?>"; // from PHP

        let uniforms = [];

        $(".uniformRow").each(function () {

            let existing_id = $(this).find(".existingId").val() || "";
            let type_id     = $(this).find(".typeField").val();
            let size_id     = $(this).find(".sizeField").val();

            if (!type_id || !size_id) {
                return; // skip bad row
            }

            uniforms.push({
                existing_id: existing_id,
                type_id: type_id,
                size_id: size_id
            });
        });

        $.ajax({
            url: "ajax/uniform_ajax.php",
            type: "POST",
            data: {
                page: "saveEmpJobGradeAndUniforms",
                employee_id: employee_id,
                job_grade_id: job_grade_id,
                uniforms: JSON.stringify(uniforms),
                deleted_uniforms: JSON.stringify(deletedUniforms)
            },
            success: function (res) {
                let r = JSON.parse(res);

                if (r.status === "success") {
                    Swal.fire("Saved", r.msg, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", r.msg || "Error while saving", "error");
                }
            }
        });
    });

</script>

<?php } ?>

                        <div class="section-title">Requested Items</div>

                        <div id="cartList" class="mb-3">

                            <!-- Auto filled by JS -->

                        </div>

                        <div class="card p-3">
                            <span>Required Date</span>
                            <input aria-label="Require Date" type="date" id="requireDate" class="form-control mb-3"
                                value="<?= date('Y-m-d') ?>">
                                <input type="hidden" name="location_id" id="location_id" value="<?php echo $location_id ?? 0; ?>">
                            <span>Remarks</span>
                            <textarea aria-label="Request Comment" id="requestComment" class="form-control mb-3"></textarea>

                            <button class="btn btn-success w-100" id="submitAllBtn">Save & Submit For Apporval</button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $("#tabNewItem").on("click", function() {
            $("#uniformTabs .nav-link").removeClass("active");
            $(this).addClass("active");

            $("#uniformContent").html("Loading...");
            $("#uniformContent").load("views/custom_insert_uniform.php");
        });

        $("#tabTopupItem").on("click", function() {
            $("#uniformTabs .nav-link").removeClass("active");
            $(this).addClass("active");

            $("#uniformContent").html("Loading...");
            $("#uniformContent").load("views/custom_insert_uniform_topup.php");
        });

        // Live Search
        $("#searchItem").on("keyup", function() {

            let v = $(this).val().toLowerCase();
            let visibleCount = 0;

            $(".item-card-wrapper").each(function() {
                let match = $(this).data("search").includes(v);
                $(this).toggle(match);

                if (match) visibleCount++;
            });

            if (visibleCount === 0) {
                $("#noSearchResults").removeClass("d-none");
            } else {
                $("#noSearchResults").addClass("d-none");
            }
        });

    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

<script>
    let cart = {}; // store items here

    // ADD ITEM TO CART
    $(document).on("click", ".addToCartBtn", function() {

        let id = $(this).data("id");

        if (!cart[id]) {
            cart[id] = {
                item_id: id,
                name: $(this).data("name"),
                color: $(this).data("color"),
                color_id: $(this).data("color_id"),
                size: $(this).data("size"),
                size_id: $(this).data("size_id"),
                available: parseInt($(this).data("available")),
                location_id: $(this).data("location"),
                uniform_type_id: $(this).data("uniform_type_id"),
                qty: 1
            };
        } else {
            if (cart[id].qty < cart[id].available) {
                cart[id].qty++;
            }
        }

        renderCart();
    });

    // RENDER CART LIST
    function renderCart() {
        let html = "";

        Object.values(cart).forEach(item => {
            html += `

            <div class="cart-item row" data-id="${item.item_id}">
                <div class="col-md-6">
                    <div class="cart-item-title">
                        ${item.name}

                    </div>
                    <div><strong>Available Qty:</strong> ${item.available}</div>
                    <div><strong>Color:</strong> ${item.color}</div>
                    <div><strong>Size:</strong> ${item.size}</div>
                </div>
                <div class="col-md-6 d-flex align-items-end flex-column">
                    <div class="">
                        <i class="fa fa-trash  remove-item float-end text-danger"
                           style="cursor:pointer; font-size:16px;"
                           data-id="${item.item_id}">
                        </i>
                    </div>
                    <div class="qty-controls">
                    <button class="qty-btn btn btn-outline-dark decreaseQty" data-id="${item.item_id}">-</button>
                    <div class="qty-display">${item.qty}</div>
                    <button class="qty-btn btn btn-outline-dark increaseQty" data-id="${item.item_id}">+</button>
                    </div>
                </div>
            </div>
        `;
        });

        $("#cartList").html(html);
    }

    // INCREASE QTY
    $(document).on("click", ".increaseQty", function() {
        let id = $(this).data("id");
        if (cart[id].qty < cart[id].available) {
            cart[id].qty++;
            renderCart();
        }
    });

    // DECREASE QTY
    $(document).on("click", ".decreaseQty", function() {
        let id = $(this).data("id");
        if (cart[id].qty > 1) {
            cart[id].qty--;
            renderCart();
        }
    });

    // REMOVE ITEM
    $(document).on("click", ".remove-item", function() {
        let id = $(this).data("id");
        delete cart[id];
        renderCart();
    });

    // SUBMIT ALL SELECTED ITEMS
    $("#submitAllBtn").click(function() {

        let items = Object.values(cart);

        if (items.length === 0) {
            Swal.fire("Error", "No items selected", "warning");
            return;
        }

        $.ajax({
            url: "ajax/uniform_ajax.php",
            type: "POST",
            data: {
                page: "saveMultipleStationeryRequests",
                items: JSON.stringify(items),
                require_date: $("#requireDate").val(),
                comment: $("#requestComment").val(),
                location_id: $("#adminLocation").val() ?? $("#location_id").val()
            },
            success: function(res) {
                let r = JSON.parse(res);
                if (r.status === "success") {
                    Swal.fire({
                        title: "Request Submitted!",
                        html: "<b>Request No:</b> " + r.request_no,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        location.reload();
                    });

                }else {
                    Swal.fire("Error", "Unknown error occurred", "error");
                }
            }
        });

    });
</script>
