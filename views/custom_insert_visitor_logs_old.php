<?php
$_SESSION['apply_js']['multiselest'] = 1;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$message = '';
if ($_POST['submit']) {
    $table_name = 'visitor_logs';
    $existingImage = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $newImageName = imageupload('visitors', 'image_path', $_POST['image_path']);
    } else {
        $newImageName = $existingImage;
    }
    $_POST['image_path'] = $newImageName;
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $data = $_POST;
        unset($data['luggage']);
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date'] = date('Y-m-d H:i:s');
        $data['company_id'] = $_SESSION['company_id'];
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $response = json_decode($response, 1);
        if ($response["status"] == "sucess") {
            $visitorLogId = $response['LASTID'] ?? '';
        if ($visitorLogId) {
            saveOrUpdateLuggageRows($visitorLogId);
        }
            $message = 'Successfully Data Inserted';
            // header("location:index.php?option=COM_INSERT_COMPLAINTS");
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        unset($_POST['existing_image']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = date('Y-m-d H:i:s');
        $_POST['updated_by'] = $_SESSION['userid'];
        $data = $_POST;
        unset($data['luggage']); 
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'update');
        $response = json_decode($response, 1);

        if ($response["UpdatedRes"] &&  $response["status"] == "sucess") {
            $visitorLogId = $rid; // same as current record id
            saveOrUpdateLuggageRows($visitorLogId);
            $message = 'Successfully Data Updated';
            // header("location:index.php?option=COM_LIST_COMPLAINTS");

        }
    }
}
function uploadLuggageImage($i) {
    // Access nested file: luggage[i][image]
    if (isset($_FILES['luggage']['error'][$i]['image']) && $_FILES['luggage']['error'][$i]['image'] === UPLOAD_ERR_OK) {
        // Repackage to a flat index so your imageupload works
        $tmpName = $_FILES['luggage']['tmp_name'][$i]['image'];
        $nameKey = "luggage_{$i}_image"; // temp key
        // Create a mock $_FILES entry for imageupload helper if it expects $_FILES[$field]
        $_FILES[$nameKey] = [
            'name'     => $_FILES['luggage']['name'][$i]['image'],
            'type'     => $_FILES['luggage']['type'][$i]['image'],
            'tmp_name' => $tmpName,
            'error'    => $_FILES['luggage']['error'][$i]['image'],
            'size'     => $_FILES['luggage']['size'][$i]['image'],
        ];
        // Use the helper (save under 'visitor_luggage')
        return imageupload('visitor_luggage', $nameKey, $tmpName);
    }
    return ''; // no new image
}

function saveOrUpdateLuggageRows($visitorLogId) {
    if (!isset($_POST['luggage']) || !is_array($_POST['luggage'])) return;

    foreach ($_POST['luggage'] as $i => $row) {
        $lugId           = trim($row['id'] ?? '');
        $materialTypeId  = trim($row['material_type_id'] ?? '');
        $itemName        = trim($row['item_name'] ?? '');
        $itemCode        = trim($row['item_code'] ?? '');
        $desc            = trim($row['description'] ?? '');
        $existingImage   = trim($row['existing_image'] ?? '');

        // upload image if present
        $newImg = uploadLuggageImage($i);
        $finalImg = $newImg !== '' ? $newImg : $existingImage;

        $payload = [
            'visitor_log_id'  => $visitorLogId,
            'material_type_id'=> $materialTypeId,
            'item_name'       => $itemName,
            'item_code'       => $itemCode,
            'description'     => $desc,
            'image_path'      => $finalImg,
            'is_active'       => 0
        ];

        if ($lugId !== '') {
            // UPDATE
            $payload['updated_by']   = $_SESSION['userid'];
            $payload['updated_date'] = date('Y-m-d H:i:s');
            $url = API . "?mode=updaterowbasepimarydata&rid=" . urlencode($lugId) . "&table=visitor_luggages&method=post";
            file_post_contents($url, $payload, 'update');
        } else {
            // INSERT (skip empty rows)
            if ($materialTypeId !== '' || $itemName !== '' || $itemCode !== '' || $desc !== '' || $finalImg !== '') {
                $payload['created_by']   = $_SESSION['userid'];
                $payload['created_date'] = date('Y-m-d H:i:s');
                $payload['company_id']   = $_SESSION['company_id'];
                $payload['vistor_type']  = 'IN';
                $url = API . "?mode=adddata&table=visitor_luggages&method=post";
                file_post_contents($url, $payload, 'insert');
            }
        }
    }
}

$rid = 5;
if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}
$url = API . "?mode=getVisitorInsertRelatedData&method=get&rid=$rid";
$result = callAPI($url);
$result = json_decode($result, 1);
// print_r($result);
$return = $result['visitor_logs_res'] ?? array();
$employees_res = $result['employees_res'] ?? array();
$building_gates_res = $result['building_gates_res'] ?? array();
$visitor_types_res = $result['visitor_types_res'] ?? array();
$id_card_types_res = $result['id_card_types_res'] ?? array();

$materials_res = $result['materials_res'] ?? array(['id' =>1, 'name' => 'material_1'],['id' =>2, 'name' => 'material_2']);   // [{id, name} or your schema]
$visitor_luggages_res = $result['visitor_luggages_res'] ?? array(); // for edit prefilling
// print_r($visitor_luggages_res);

?>

<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page body start -->
            <div class="page-body">
                <div class="card">
                    <div class="card-header">
                        <h5>Visitor Logs</h5>
                        <div class="card-header-right">
                            <!-- <a href="index.php?option=COM_LIST_VISITOR_LOGS" class="btn btn-success mb-2 desktop-view">
                                Visitors List</a> -->
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-infisuite mt-2">
                        <div class="card-body p-4 ">
                            <form id="visitorFom" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-xs-12 form-group">
                                        <label>Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="name" id="name" value="<?php echo $return['name'] ?? ''; ?>">
                                        <div style="display: none;color: red;" id="name_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6 col-xs-12 form-group">
                                        <label>Phone<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="phone" id="phone" value="<?php echo $return['phone'] ?? ''; ?>">
                                        <div style="display: none;color: red;" id="phone_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6 col-xs-12 form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="text" name="email" id="email" value="<?php echo $return['email'] ?? ''; ?>">
                                        <div style="display: none;color: red;" id="email_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6  col-xs-12">
                                        <label>Address</label>
                                        <div class="kt-radio-inline">
                                            <textarea type="text" class="form-control " rows="5" cols="5" id="address" name="address"><?php echo $return['address'] ?? ''; ?></textarea>
                                        </div>
                                        <div style="display: none;color: red;" id="address_err"> Please Select Address </div>
                                    </div>


                                    <div class="form-group col-lg-6">
                                        <label>Whom To Meet <span style="color:red;">*</span></label>
                                        <select class="form-select js-example-basic-single" id="whom_to_meet" name="whom_to_meet">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$employees_res) > 0) {
                                                foreach ($employees_res as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $return['whom_to_meet'] == $value['id'] ? 'selected' : '' ?>> <?php echo $value['display_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="whom_to_meet_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Purpose</label>
                                        <div class="kt-radio-inline">
                                            <textarea type="text" class="form-control" rows="5" cols="5" id="purpose" name="purpose"><?php echo $return['purpose'] ?? ''; ?></textarea>
                                        </div>
                                        <div style="display: none;color: red;" id="purpose_err"> Please Select Purpose </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Identity Card Type </label>
                                        <select class="form-select js-example-basic-single" id="idcard_type" name="idcard_type">
                                            <option value="0"> Select Any one </option>
                                            <?php if (count((array)$id_card_types_res) > 0) {
                                                foreach ($id_card_types_res as $value) {  ?>
                                                    <option value="<?php echo $value['genc_detail_code']; ?>" <?php echo $return['idcard_type'] == $value['genc_detail_code'] ? 'selected' : '' ?>> <?php echo $value['genc_detail_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="idcard_type_err"></div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label class="">Identity Card Number</label>
                                        <input class="form-control" type="text" id="idcard_number" name="idcard_number" value="<?php echo $return['idcard_number'] ?? ''; ?>">
                                        <div style="display: none;color: red;" id="idcard_number_err"> Please enter Identity Card Number </div>
                                    </div>


                                    <div class="form-group col-lg-6">
                                        <label>Visitor Type</label>
                                        <select class="form-select js-example-basic-single" id="visitor_type" name="visitor_type">
                                            <option value="0"> Select Any one </option>
                                            <?php if (count((array)$visitor_types_res) > 0) {
                                                foreach ($visitor_types_res as $value) {  ?>
                                                    <option value="<?php echo $value['genc_detail_code']; ?>" <?php echo $return['visitor_type'] == $value['genc_detail_code'] ? 'selected' : '' ?>> <?php echo $value['genc_detail_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="visitor_type_err"></div>
                                    </div>
                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <label>Photo</label>
                                        <input class="form-control mb-1" type="file" name="image_path" id="image_path" />
                                        <?php
                                        if ($return['image_path'] != '') {
                                            echo '<a href="uploads/visitors/' . $return['image_path'] . '" target="_blank" class="mx-2">
												<img src="uploads/visitors/' . $return['image_path'] . '" class="img-60 border" alt="">
												</a>';
                                        }

                                        ?>
                                    </div>
                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <label>Visitor Identity Card No</label>
                                        <input class="form-control" type="text" name="visitor_idcard_no" id="visitor_idcard_no" value="<?php echo $return['visitor_idcard_no'] ?? ''; ?>" />

                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Gate No</label>
                                        <select class="form-select js-example-basic-single" id="in_gate_no" name="in_gate_no">
                                            <option value="0"> Select Any one </option>
                                            <?php if (count((array)$building_gates_res) > 0) {
                                                foreach ($building_gates_res as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $return['in_gate_no'] == $value['id'] ? 'selected' : '' ?>> <?php echo $value['name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                    <?php
                                    // Build <option> HTML once
                                    $materialOptionsHtml = '<option value="">Select Material</option>';
                                    if (!empty($materials_res)) {
                                        foreach ($materials_res as $m) {
                                            // Adjust keys if your API names differ (e.g., $m['id'], $m['name'])
                                            $materialOptionsHtml .= '<option value="' . htmlspecialchars($m['id']) . '">' . htmlspecialchars($m['name']) . '</option>';
                                        }
                                    }
                                    ?>

                                    <hr class="my-3">
                                    <h6 class="mb-2">Visitor Luggage</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="luggageTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 18%">Material Type<span style="color:red;">*</span></th>
                                                    <th style="width: 16%">Item Name</th>
                                                    <th style="width: 16%">Item Code</th>
                                                    <th>Description</th>
                                                    <th style="width: 18%">Image</th>
                                                    <th style="width: 6%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $prefill = !empty($visitor_luggages_res) ? $visitor_luggages_res : [[]]; // at least one empty row
                                                foreach ($prefill as $rowIndex => $lug) {
                                                    $rowId          = $lug['id'] ?? '';
                                                    $materialId     = $lug['material_type_id'] ?? '';
                                                    $itemName       = $lug['item_name'] ?? '';
                                                    $itemCode       = $lug['item_code'] ?? '';
                                                    $desc           = $lug['description'] ?? '';
                                                    $img            = $lug['image_path'] ?? '';
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <select class="form-select material-select js-example-basic-single" name="luggage[<?= $rowIndex ?>][material_type_id]">
                                                                <?php
                                                                // print options with selected
                                                                $opts = str_replace(
                                                                    'value="' . htmlspecialchars($materialId) . '"',
                                                                    'value="' . htmlspecialchars($materialId) . '" selected',
                                                                    $materialOptionsHtml
                                                                );
                                                                echo $materialId ? $opts : $materialOptionsHtml;
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="luggage[<?= $rowIndex ?>][item_name]" value="<?= htmlspecialchars($itemName) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="luggage[<?= $rowIndex ?>][item_code]" value="<?= htmlspecialchars($itemCode) ?>">
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control" name="luggage[<?= $rowIndex ?>][description]" rows="2"><?= htmlspecialchars($desc) ?></textarea>
                                                        </td>
                                                        <td>
                                                            <input type="file" class="form-control" name="luggage[<?= $rowIndex ?>][image]">
                                                            <?php if (!empty($img)): ?>
                                                                <div class="mt-1 d-flex align-items-center gap-2">
                                                                    <a href="uploads/visitor_luggage/<?= htmlspecialchars($img) ?>" target="_blank">
                                                                        <img src="uploads/visitor_luggage/<?= htmlspecialchars($img) ?>" class="img-60 border" alt="">
                                                                    </a>
                                                                    <input type="hidden" name="luggage[<?= $rowIndex ?>][existing_image]" value="<?= htmlspecialchars($img) ?>">
                                                                </div>
                                                            <?php endif; ?>
                                                            <input type="hidden" name="luggage[<?= $rowIndex ?>][id]" value="<?= htmlspecialchars($rowId) ?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if($rowId == ''){ ?>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeLuggageRow(this)">
                                                                <i class="icofont icofont-trash"></i>
                                                            </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3 text-end">
                                        <button type="button" class="btn btn-outline-primary" id="addLuggageBtn">
                                            <i class="icofont icofont-plus"></i>Luggage
                                        </button>
                                    </div>

                                </div>
                                <div class="border-top">
                                    <?php if ($view != '1'):
                                        if ($rid != "") {  ?>
                                            <button type="submit" name='submit' value='update' class="btn waves-effect waves-light btn-primary mt-2">
                                                Update
                                            </button>
                                            <input name="rid" type="hidden" value="<?php echo $rid; ?>">
                                            <input type="hidden" name="existing_image" value="<?php echo $return['image_path'] ?? ''; ?>">

                                        <?php } else {  ?>
                                            <button type="submit" name='submit' value='insert' class="btn waves-effect waves-light btn-primary mt-2">
                                                Save
                                            </button>
                                    <?php }
                                    endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    // Initialize the form with default visibility
    $(document).ready(function() {
        $("#visitorFom").submit(function(e) {
            if (!validatevisitorFom()) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });

    });


    function validatevisitorFom() {
        let isValid = true;
        // Clear previous error messages
        $(".form-group div").hide();

        // Validate common mandatory fields
        const visitorName = $("#name").val();
        const visitorPhone = $("#phone").val();
        const whomToMeet = $("#whom_to_meet").val();

        if (!visitorName) {
            $("#name_err").text("Please Enter visitor Name").show();
            isValid = false;
        }
        if (!visitorPhone) {
            $("#phone_err").text("Please enter visitor Phone no.").show();
            isValid = false;
        }
        if (!whomToMeet || whomToMeet === "") {
            $("#whom_to_meet_err").text("Please select a whom to meet").show();
            isValid = false;
        }
        return isValid;
    }
</script>

<script>
    (function() {
        // Build material <option>s from PHP into JS
        const materialOptions = <?= json_encode($materialOptionsHtml) ?>;

        // compute next index based on existing rows
        function nextLuggageIndex() {
            const rows = document.querySelectorAll('#luggageTable tbody tr');
            return rows.length;
        }

        function luggageRowHtml(idx) {
            return `
        <tr>
            <td>
                <select class="form-select material-select js-example-basic-single" name="luggage[${idx}][material_type_id]">
                    ${materialOptions}
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="luggage[${idx}][item_name]" value="">
            </td>
            <td>
                <input type="text" class="form-control" name="luggage[${idx}][item_code]" value="">
            </td>
            <td>
                <textarea class="form-control" name="luggage[${idx}][description]" rows="2"></textarea>
            </td>
            <td>
                <input type="file" class="form-control" name="luggage[${idx}][image]">
                <input type="hidden" name="luggage[${idx}][existing_image]" value="">
                <input type="hidden" name="luggage[${idx}][id]" value="">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeLuggageRow(this)">
                    <i class="icofont icofont-trash"></i>
                </button>
            </td>
        </tr>`;
        }

        window.removeLuggageRow = function(btn) {
            const tbody = document.querySelector('#luggageTable tbody');
            const rows = tbody.querySelectorAll('tr');
            if (rows.length === 1) {
                // If only one row, just clear its inputs instead of removing
                const inputs = rows[0].querySelectorAll('input[type=text], textarea, select');
                inputs.forEach(i => i.value = '');
                // clear file
                const file = rows[0].querySelector('input[type=file]');
                if (file) file.value = '';
                // clear hidden id/existing_image
                const hiddenIds = rows[0].querySelectorAll('input[type=hidden]');
                hiddenIds.forEach(h => h.value = '');
            } else {
                btn.closest('tr').remove();
                // no need to renumber names; PHP handles sparse indices, but if you want compact indices you can re-index here
            }
        }

        document.getElementById('addLuggageBtn').addEventListener('click', function() {
            const idx = nextLuggageIndex();
            const tbody = document.querySelector('#luggageTable tbody');
            tbody.insertAdjacentHTML('beforeend', luggageRowHtml(idx));
            // Get the last (newly added) row
            const newRow = tbody.lastElementChild;
            // Find the select element in the new row
            const newSelect = newRow.querySelector('.js-example-basic-single');
            // Initialize Select2 on the new select
            $(newSelect).select2();

        });

        // Extend your existing form validation
        const originalValidate = window.validatevisitorFom;
        window.validatevisitorFom = function() {
            let ok = true;
            if (typeof originalValidate === 'function') {
                ok = originalValidate();
            }

            // Basic luggage validation (material type required if any fields entered)
            const rows = document.querySelectorAll('#luggageTable tbody tr');
            rows.forEach((tr) => {
                const material = tr.querySelector('select.material-select');
                const itemName = tr.querySelector('input[name*="[item_name]"]');
                const itemCode = tr.querySelector('input[name*="[item_code]"]');
                const desc = tr.querySelector('textarea[name*="[description]"]');
                const file = tr.querySelector('input[type=file]');

                const anyField = (itemName.value || itemCode.value || desc.value || (file && file.value));
                if (anyField && !material.value) {
                    material.classList.add('is-invalid');
                    ok = false;
                } else {
                    material.classList.remove('is-invalid');
                }
            });

            if (!ok) {
                // You can show a toast if you like
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select Material Type for each luggage row that has data', '');

            }
            return ok;
        }
    })();
</script>