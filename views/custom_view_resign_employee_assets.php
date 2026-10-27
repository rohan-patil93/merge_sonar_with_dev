<?php
$_SESSION['apply_js']['multiselest'] = 1;

if (isset($_POST['submit'])) {
    $table_name = 'user_assets';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        for ($i = 0; $i < count((array)$_POST['asset_id']); $i++) {
            $data = [
                'user_id' => $_POST['user_id'],
                'emp_id' => $_POST['emp_id'],
                'asset_id' => $_POST['asset_id'][$i],
                'received_date' => $_POST['received_date'][$i],
                'received_note' => $_POST['received_note'][$i],
                'allocation_type' => 3,
                'current_status' => '0',
                'is_active' => '0',
                'status_date' => date('Y-m-d'),
                'company_id' => $_SESSION['company_id'],
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['userid']
            ];
            $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
            $response = file_post_contents($url, $data, 'insert');
        }

        $res = json_decode($response, 1);
        if ($res['status'] == 'sucess') {
            header("location:index.php?option=COM_VIEW_RESIGN_EMPLOYEE_ASSETS");
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        $rid = $_POST['emp_id'];
        $data = array(
            'is_active' => 1,
            'updated_date' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['userid']
        );

        $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=".$table_name."&method=post&fid=emp_id";

        $response = file_post_contents($url, $data, 'update');
        $res = json_decode($response, 1);

        for ($i = 0; $i < count((array)$_POST['asset_id']); $i++) {
            $data = [
                'user_id' => $_POST['user_id'],
                'emp_id' => $_POST['emp_id'],
                'asset_id' => $_POST['asset_id'][$i],
                'received_date' => $_POST['received_date'][$i],
                'received_note' => $_POST['received_note'][$i],
                'allocation_type' => 3,
                'current_status' => '0',
                'is_active' => '0',
                'status_date' => date('Y-m-d'),
                'company_id' => $_SESSION['company_id'],
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['userid']
            ];
            $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
            $response1 = file_post_contents($url, $data, 'insert');
        }
        $res1 = json_decode($response1, 1);

        if ($res1['status'] == 'sucess') {
            header("location:index.php?option=COM_VIEW_RESIGN_EMPLOYEE_ASSETS");
        }
    }
}

?>
<style type="text/css">
    .spanactive{
        cursor: pointer;
        font-size: 14px;
        padding: 10px;
        background-color: #FFB64D;
        border-radius: 5px;
    }
    .spandeactive{
        cursor: pointer;
        font-size: 14px;
        padding: 10px;
        border: 1px solid #FFB64D;
         border-radius: 5px;
    }

</style>
<div class="page-body">
    <!-- Pradeep HTML Start here -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Resign Employee Assets</h5>

            <div class="card-header-right">

            </div>
        </div>
        <div class="card-body">
            <div class="mb-10">
            <span class="m-r-5 spanactive" id="resignBtn" >Resign Employee</span>
            <button type="button" class="btn p-0 border-0 bg-transparent m-r-5 spandeactive" onclick="resignhistorydaybyempid()" id="historyBtn" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">History</button>
            </div>
            <div class="row m-t-20" id="resigningContainer">
                <?php include_once 'custom_view_resign_employee_asset.php'; ?>
            </div>
        </div>
    </div>
    <!-- Pradeep HTML End here -->
</div>

<script>

    function toselectemployevaluereplace(empId,userId){
        console.log("userId="+userId+",empId="+empId);
        if(userId=='new'){
            $("#showonetime").show();
            document.getElementById("emp_id").value = 0;
            document.getElementById("user_id").value = 0;
        }else{
            $("#showonetime").hide();
            document.getElementById("emp_id").value = empId;
            document.getElementById("user_id").value = userId;
        }
    }

        function addRow() {
            console.log('addRow', 'addRow');
            var count = $(".cc_val").length;
            count++;
            let container = document.getElementById("resignContainer");
            let selectedValues = Array.from(document.querySelectorAll(".asset-select"))
                .map(select => select.value);

            let newRow = document.createElement("div");
            newRow.classList.add("row", "asset-row");
            newRow.innerHTML = `
                <div class="col-lg-4 cc_val">
                    <div class="form-group">
                        <select aria-label="Asset []" class="form-control form-select js-example-basic-single asset-select" name="asset_id[]" id="asset_id_`+count+`" required>
                            <option value="">Select Asset</option>
                            <?php foreach ($assets as $asset){ ?>
                                <option value="<?php echo $asset['id']; ?>"><?php echo $asset['asset_description']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input aria-label="Received Date[]" type="date" class="form-control"  name="received_date[]" id="received_date" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <textarea aria-label="Remarks" class="form-control" placeholder="Remarks"  name="received_note[]" id="received_note"></textarea>
                    </div>
                </div>
                <div class="col-lg-2">
                <button type="button" class="removeRow btn text-danger">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            `;

            container.appendChild(newRow);
            $(".js-example-basic-single").select2();
            updateSelectOptions();
        }

        function updateSelectOptions() {
            let selectedValues = Array.from(document.querySelectorAll(".asset-select"))
                .map(select => select.value);

            document.querySelectorAll(".asset-select").forEach(select => {
                let currentValue = select.value;
                select.innerHTML = '<option value="">Select Asset</option>';

                <?php foreach ($assets as $asset) : ?>
                    if (!selectedValues.includes("<?php echo $asset['id']; ?>") || currentValue === "<?php echo $asset['id']; ?>") {
                        let option = document.createElement("option");
                        option.value = "<?= $asset['id']; ?>";
                        option.textContent = "<?= $asset['asset_description']; ?>";
                        if (currentValue === "<?= $asset['id']; ?>") {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    }
                <?php endforeach; ?>
            });
        }


    function resignhistorydaybyempid(empId="",userId=""){
        console.log('pageOption', 'history');
        $("#resignBtn").removeClass("spanactive");
        $("#resignBtn").addClass("spandeactive");
        $("#historyBtn").removeClass("spandeactive");
        $("#historyBtn").addClass("spanactive");

        $.ajax({
            url: "views/custom_view_history_resign_assets.php",
            data: {
                reshis_emp_id: empId,
                reshis_user_id: userId,
            },
            type: "GET",
            success: function(data) {
                $('#resigningContainer').html(data);
                $(".js-example-basic-single").select2();
                //Reattach "Add More" button event after AJAX load
            },
            error: function() {
                console.error("Error loading page.");
            }
        });
    }
    document.getElementById("resignBtn").addEventListener("click", function() {
        let empId ="";
        console.log('pageOption', 'Resign');
        $("#historyBtn").removeClass("spanactive");
        $("#historyBtn").addClass("spandeactive");
        $("#resignBtn").removeClass("spandeactive");
        $("#resignBtn").addClass("spanactive");

        $.ajax({
            url: "views/custom_view_resign_employee_asset.php",
            data: { emp_id: empId },
            type: "GET",
            success: function(data) {
                $('#resigningContainer').html(data);
                $(".js-example-basic-single").select2();
                // Reattach "Add More" button event after AJAX load
            },
            error: function() {
                console.error("Error loading page.");
            }
        });
    });

    // Add this inside the existing script block
    document.addEventListener("click", function(event) {
        if (event.target && event.target.id === "addMore") {
            addRow();
        }
            // Handle "Remove Row" button
    if (event.target.closest(".removeRow")) {
        event.target.closest(".asset-row").remove();
    }
    });
    function onselectemp(val){
        console.log(val);
        var parts = val.split("#");
        document.getElementById("emp_id").value = parts['0'];
        document.getElementById("user_id").value = parts['1'];
    }

</script>
