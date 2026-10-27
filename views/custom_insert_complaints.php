<?php
$_SESSION['apply_js']['multiselest'] = 1;
if($_POST['submit']){
    $table_name = 'complaints';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $data = $_POST;
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date'] = nowDate();
        $data['company_id'] = $_SESSION['company_id'];
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $response = json_decode($response, 1);
        if ($response["status"] == "sucess") {
            header("location:index.php?option=COM_INSERT_COMPLAINTS");
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        $response = json_decode($response, 1);
        if ($response["UpdatedRes"] &&  $response["status"] == "sucess") {
            header("location:index.php?option=COM_INSERT_COMPLAINTS");
        }
    }
}

if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'complaints';
    $url = API . "?mode=singlerowtabledata&method=get&rid=$rid&table=$table_name";
    $che = callAPI($url);
    $return = json_decode($che, 1);
    $view  = $_GET['view'];
    $copy  = $_GET['copy'];
}
$url = API . "?mode=getAllComplaintsData&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
$buildings = $items = $assets = array();
$buildings = $result['buildings'];
$items = $result['items'];
$assets = $result['assets'];
?>
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page body start -->
            <div class="page-body">
                <div class="card">
                    <div class="card-header">
                        <h5>Complaints</h5>
                        <div class="card-header-right">
                            <a href="index.php?option=COM_LIST_COMPLAINTS" class="btn btn-success mb-2 desktop-view">

                            Complaints List</a>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-infisuite mt-2">
                        <div class="card-body p-4 ">
                            <form id="complaintsForm" method="post">
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['userid']; ?>">
                                <div class="row">


                                    <div class="form-group col-lg-6">
                                        <span>Complaint priority <span style="color:red;">*</span></span>
                                        <select aria-label="Complaint Priority" class="form-select js-example-basic-single" id="complaint_priority" name="complaint_priority">
                                            <option value="0"> Select Any one </option>
                                            <option value="L" <?php echo $return['complaint_priority']=="L"? 'selected': '' ?>> Low</option>
                                            <option value="M" <?php echo $return['complaint_priority']=="M"? 'selected': '' ?>> Medium </option>
                                            <option value="H" <?php echo $return['complaint_priority']=="H"? 'selected': '' ?>> High </option>

                                        </select>
                                        <div style="display: none;color: red;" id="complaint_priority_err"></div>
                                    </div>
                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <span>Date<span style="color:red;">*</span></span>
                                        <input aria-label="Transaction Date" class="form-control" type="date" name="transaction_date" id="transaction_date"  value="<?php echo isset($return['transaction_date']) && $return['transaction_date'] != '' ? date('Y-m-d', strtotime($return['transaction_date'])) :  date('Y-m-d'); ?>"/>
                                        <div style="display: none;color: red;" id="transaction_date_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6 col-xs-12 form-group">
                                        <span>Complaint Name<span style="color:red;">*</span></span>
                                        <input aria-label="Complaint Name" class="form-control" type="text" name="complaint_name" id="complaint_name" value="<?php echo $return['complaint_name'] ?? ''; ?>">
                                        <div style="display: none;color: red;" id="complaint_name_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Complaint Type<span style="color:red;">*</span></span>
                                        <select aria-label="Complaint Type" class="form-select js-example-basic-single" id="complaint_type" name="complaint_type" onchange="handleComplaintType()">
                                            <option value="0"> Select Any one </option>
                                            <option value="Item" <?php echo $return['complaint_type']=="Item"? 'selected': '' ?> >Item</option>
                                            <option value="Asset" <?php echo $return['complaint_type']=="Asset"? 'selected': '' ?> >Asset</option>
                                            <option value="Building" <?php echo $return['complaint_type']=="Building"? 'selected': '' ?> >Building</option>

                                        </select>
                                        <div style="display: none;color: red;" id="complaint_type_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Asset<span style="color:red;">*</span></span>
                                        <select aria-label="Asset" class="form-select js-example-basic-single" id="asset_id" name="asset_id">
                                            <option value="0"> Select Any one </option>
                                            <?php foreach ($assets as $row): ?>
                                                <option value="<?php echo $row['id']; ?>"  <?php echo $return['asset_id']==$row['id']? 'selected': '' ?> >
                                                    <?php echo $row['asset_code'].'-'.$row['asset_description']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div style="display: none;color: red;" id="asset_id_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Item <span style="color:red;">*</span></span>
                                        <select aria-label="Item" class="form-select js-example-basic-single" id="item_id" name="item_id">
                                            <option value="0"> Select Any one </option>
                                            <?php foreach ($items as $row): ?>
                                                <option value="<?php echo $row['id']; ?>" <?php echo $return['item_id']==$row['id']? 'selected': '' ?>  >
                                                    <?php echo $row['item_name']; ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                        <div style="display: none;color: red;" id="item_id_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Building<span style="color:red;">*</span></span>
                                        <select aria-label="Building" class="form-select js-example-basic-single" id="building_id" name="building_id">
                                            <option value="0"> Select Any one </option>
                                            <?php foreach ($buildings as $row): ?>
                                                <option value="<?php echo $row['id']; ?>" <?php echo $return['building_id']==$row['id']? 'selected': '' ?>  >
                                                    <?php echo $row['building_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div style="display: none;color: red;" id="building_id_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Building Floor<span style="color:red;">*</span></span>
                                        <select aria-label="Building Floor" class="form-select js-example-basic-single" id="building_floor_id" name="building_floor_id">
                                            <option value="0"> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="building_floor_id_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Building Floor Space<span style="color:red;">*</span></span>
                                        <select aria-label="Building Floor Space" class="form-select js-example-basic-single" id="building_floor_space_id" name="building_floor_space_id">
                                            <option value="0"> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="building_floor_space_id_err"></div>
                                    </div>

                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <span>Complaint Image</span>
                                        <input aria-label="Complaint Image" class="form-control mb-1" type="file" name="complaint_image" id="complaint_image" value="<?php echo $return['complaint_image'] ?? ''; ?>" />
                                        <?php
                                        if(!empty($rid)){
                                            $table_name = 'complaints';
                                            $column = 'complaint_image';
                                            $url = API."?mode=getImagesData&method=get&table=$table_name&id=$rid&column=$column";
                                            $img_result = callAPI($url);
                                            $img_result = json_decode($img_result, 1);
                                            $img_result = $img_result['table'];
                                            foreach ($img_result as $row) {
                                                echo '<a href="uploads/all/'.$row['file_name'] . '" target="_blank" class="mx-2">
                                                <img src="uploads/all/'.$row['file_name'] . '" class="img-60 border" alt="">
                                                </a>';
                                            }

                                        }
                                        ?>

                                    </div>
                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <span>Remarks</span>
                                        <input aria-label="Remarks" class="form-control" type="text" name="remarks" id="remarks" value="<?php echo $return['remarks'] ?? ''; ?>" />

                                    </div>
                                </div>
                                <div class="border-top">
                                    <?php if ($view != '1'):
                                        if ($rid != "" && $copy != '1') {  ?>
                                            <button name='submit' value='update' class="btn waves-effect waves-light btn-primary mt-2">
                                                Update
                                            </button>
                                            <input name="rid" type="hidden" value="<?php echo $rid; ?>">

                                        <?php } else {  ?>
                                            <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary mt-2">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
<script>
    // Initialize the form with default visibility
    $(document).ready(function() {
        handleComplaintType(); // Set the initial visibility
        $("#complaint_type").change(handleComplaintType); // Bind to change event

        $("#complaintsForm").submit(function(e) {
            if (!validateComplaintsForm()) {
                e.preventDefault();
            }
        });
        const preselectedBuildingId = <?php echo json_encode($return['building_id']); ?>;
        const preselectedFloorId = <?php echo json_encode($return['building_floor_id']); ?>;
        const preselectedSpaceId = <?php echo json_encode($return['building_floor_space_id']); ?>;

        function loadBuildingFloors(buildingId, selectedFloorId) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'gettableData',
                    table: 'building_floors',
                    id: buildingId,
                    column: 'building_id',
                },
                success: function (response) {
                    response = JSON.parse(response);
                    const buildingFloorSelect = $("#building_floor_id");
                    buildingFloorSelect.empty().append('<option value="0"> Select Any one </option>');
                    const tableData = Object.values(response.table);
                    if (tableData && tableData.length > 0) {
                        tableData.forEach(function (floor) {
                            buildingFloorSelect.append(
                        `<option value="${floor.id}" ${selectedFloorId == floor.id ? 'selected' : ''}>${floor.floor_name}</option>`
                        );
                        });
                        if (selectedFloorId) {
                            loadFloorSpaces(selectedFloorId, preselectedSpaceId);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching building floors:", error);
                },
            });
        }

        function loadFloorSpaces(floorId, selectedSpaceId) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'gettableData',
                    table: 'building_floor_spaces',
                    id: floorId,
                    column: 'building_floor_id',
                },
                success: function (response) {
                    response = JSON.parse(response);
                    const floorSpaceSelect = $("#building_floor_space_id");
                    floorSpaceSelect.empty().append('<option value="0"> Select Any one </option>');
                    const tableData = Object.values(response.table);
                    if (tableData && tableData.length > 0) {
                        tableData.forEach(function (space) {
                            floorSpaceSelect.append(
                        `<option value="${space.id}" ${selectedSpaceId == space.id ? 'selected' : ''}>${space.cabin_name}</option>`
                        );
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching floor spaces:", error);
                },
            });
        }

        if (preselectedBuildingId && preselectedBuildingId !== "0") {
            loadBuildingFloors(preselectedBuildingId, preselectedFloorId);
        }

        // Handle building selection
        $("#building_id").change(function () {
            const buildingId = $(this).val();
            if (buildingId && buildingId !== "0") {
                loadBuildingFloors(buildingId);
            } else {
                $("#building_floor_id").empty().append('<option value="0"> Select Any one </option>');
                $("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
            }
        });

        // Handle floor selection
        $("#building_floor_id").change(function () {
            const floorId = $(this).val();
            if (floorId && floorId !== "0") {
                loadFloorSpaces(floorId);
            } else {
                $("#building_floor_space_id").empty().append('<option value="0"> Select Any one </option>');
            }
        });
    });

    function handleComplaintType() {
        const complaintType = $("#complaint_type").val();

        // Hide all optional fields initially
        $("#item_id").closest(".form-group").hide();
        $("#building_id").closest(".form-group").hide();
        $("#building_floor_id").closest(".form-group").hide();
        $("#building_floor_space_id").closest(".form-group").hide();
        $("#asset_id").closest(".form-group").hide();
        $("#complaint_image").closest(".form-group").hide();

        if (complaintType === "Item") {
            $("#item_id").closest(".form-group").show();

            $("#complaint_image").closest(".form-group").show();
        } else if (complaintType === "Asset") {
            $("#asset_id").closest(".form-group").show();
            $("#complaint_image").closest(".form-group").show();
        }else if(complaintType === "Building"){
            $("#building_id").closest(".form-group").show();
            $("#building_floor_id").closest(".form-group").show();
            $("#building_floor_space_id").closest(".form-group").show();
            $("#complaint_image").closest(".form-group").show();
        }
    }

    function validateComplaintsForm() {
        let isValid = true;

        // Clear previous error messages
        $(".form-group div").hide();

        // Validate common mandatory fields
        const complaintType = $("#complaint_type").val();
        const complaintName = $("#complaint_name").val();
        const complaintPriority = $("#complaint_priority").val();
        const transactionDate = $("#transaction_date").val();

        if (!complaintType || complaintType === "0") {
            $("#complaint_type_err").text("Please select a complaint type").show();
            isValid = false;
        }
        if (!complaintName) {
            $("#complaint_name_err").text("Please enter the complaint name").show();
            isValid = false;
        }
        if (!complaintPriority || complaintPriority === "0") {
            $("#complaint_priority_err").text("Please select a complaint priority").show();
            isValid = false;
        }
        if (!transactionDate) {
            $("#transaction_date_err").text("Please select a transaction date").show();
            isValid = false;
        }

        // Validate fields specific to the complaint type
        if (complaintType === "Item") {
            const itemId = $("#item_id").val();
            if (!itemId || itemId === "0") {
                $("#item_id_err").text("Please select an item").show();
                isValid = false;
            }

        } else if (complaintType === "Asset") {
            const assetId = $("#asset_id").val();
            if (!assetId || assetId === "0") {
                $("#asset_id_err").text("Please select an asset").show();
                isValid = false;
            }

        }else if (complaintType === "Building") {
            const buildingId = $("#building_id").val();
            const buildingFloorId = $("#building_floor_id").val();
            const buildingFloorSpaceId = $("#building_floor_space_id").val();

            if (!buildingId || buildingId === "0") {
                $("#building_id_err").text("Please select a building").show();
                isValid = false;
            }
            if (!buildingFloorId || buildingFloorId === "0") {
                $("#building_floor_id_err").text("Please select a building floor").show();
                isValid = false;
            }
            if (!buildingFloorSpaceId || buildingFloorSpaceId === "0") {
                $("#building_floor_space_id_err").text("Please select a building floor space").show();
                isValid = false;
            }

        }

        return isValid;
    }
</script>
