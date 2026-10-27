<?php
$_SESSION['apply_js']['multiselest'] = 1;

$table_name = 'status';
$url = API . "?mode=gettableData&method=get&table=$table_name";
$result = callAPI($url);
$result = json_decode($result, 1);
$status = $result['table'];

?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Complaints List</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_INSERT_COMPLAINTS" class="btn btn-success mb-2 desktop-view">
                    <i class="icofont icofont-plus icon-white "></i>
                Complaints            </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body p-4 ">
            <div class="row">
                <div class="form-group col-lg-3">
                    <span>Complaint Type<span style="color:red;">*</span></span>
                    <select aria-label="Complaint Type" class="form-select js-example-basic-single" id="complaint_type" name="complaint_type" >
                        <option value="0"> Select Any one </option>
                        <option value="Item">Item</option>
                        <option value="Asset">Asset</option>
                        <option value="Building">Building</option>

                    </select>
                    <div style="display: none;color: red;" id="complaint_type_err"></div>
                </div>

                <div class="form-group col-lg-3">
                    <span>Complaint priority </span>
                    <select aria-label="Complaint Priority" class="form-select js-example-basic-single" id="complaint_priority" name="complaint_priority">
                        <option value="0"> Select Any one </option>
                        <option value="L"> Low</option>
                        <option value="M"> Medium </option>
                        <option value="H"> High </option>

                    </select>

                </div>

                <div class="col-lg-2 col-xs-12 form-group">
                    <span>Date</span>
                    <input aria-label="Transaction Date" class="form-control" type="date" name="transaction_date" id="transaction_date"  />

                </div>
                <div class="form-group col-lg-2">
                    <span>Status</span>
                    <select aria-label="Current Status" class="form-select js-example-basic-single" id="current_status" name="current_status">
                        <option value=""> Select Any one </option>
                        <?php foreach ($status as $row): ?>
                            <option value="<?php echo $row['name_value']; ?>" >
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="col-lg-2 col-xs-6 form-group ">
                    <span> &nbsp;</span>
                    <button type="button" class="btn btn-primary form-control " id="getDataButton">Search</button>
                </div>
            </div>
            <!-- table -->
            <div class="row" id="TableContent">
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        $("#getDataButton").click(function () {

            let isValid = true;
            $(".form-group div").hide();

            const complaintType = $("#complaint_type").val();
            if (!complaintType || complaintType === "0") {
                $("#complaint_type_err").text("Please select a complaint type").show();
                isValid = false;
            }
            var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
            $("#TableContent").html(loader);
            if(!isValid){
                return;
            }

            const complaintPriority = $("#complaint_priority").val();
            const transactionDate = $("#transaction_date").val();
            const status = $("#current_status").val();

            const requestData = {
                complaint_type: complaintType,
                complaint_priority: complaintPriority,
                transaction_date: transactionDate,
                status: status
            };
            $.ajax({
                url: "views/custom_list_complaints_table.php",
                data: {sendData: requestData,},
                type: "POST",
                success:function(data){
                    $('#TableContent').html(data);
                },
                error:function (){}
            });
        });
    });

</script>
