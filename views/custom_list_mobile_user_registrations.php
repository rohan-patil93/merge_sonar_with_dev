
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Mobile Registered Users</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body p-4 ">
                <div class="row">
                 <div class="form-group col-lg-5">
                    <span>(Name/Email/Phone No.)</span>
                    <input aria-label="User" class="form-control" id="user_id" name="user_id" />
                    <div style="display: none;color: red;" id="user_id_err"></div>
                </div>

                <div class="col-lg-3 col-xs-6 form-group">
                    <span>From Date <span style="color:red;">*</span></span>
                    <input aria-label="From Date" class="form-control" type="date" name="from_date" id="from_date" value="" onchange="handleFromDateChange()">
                    <div style="display: none;color: red;" id="from_date_err"></div>
                </div>
                <div class="col-lg-3 col-xs-4 form-group">
                    <span>To Date <span style="color:red;">*</span></span>
                    <input aria-label="To Date" class="form-control" type="date" name="to_date" id="to_date" value="">
                    <div style="display: none;color: red;" id="to_date_err"></div>
                </div>
                <div class="col-lg-1 col-xs-6 form-group ">
                    <span> &nbsp;</span>
                    <button type="button" class="btn btn-primary form-control " id="getDataButton">Search</button>
                </div>
            </div>
            <div class="row" id="TableContent">
            </div>

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>

<script>
    function handleFromDateChange() {
        var fromDateInput = document.getElementById("from_date");
        var toDateInput = document.getElementById("to_date");

        toDateInput.min = fromDateInput.value;
        if (toDateInput.value < fromDateInput.value) {
            toDateInput.value = fromDateInput.value;
        }
    }
        $(document).ready(function () {
        $("#getDataButton").click(function () {

            let isValid = true;
            $(".form-group div").hide();

            const fromDate = $("#from_date").val();
            if (!fromDate || fromDate === "") {
                $("#from_date_err").text("Please select From Date").show();
                isValid = false;
            }
            const toDate = $("#to_date").val();
            if (!toDate || toDate === "") {
                $("#to_date_err").text("Please select To Date").show();
                isValid = false;
            }

            if(!isValid){
                return;
            }
            var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
            $("#TableContent").html(loader);

            userId = $("#user_id").val();

            const requestData = {
                user_id: userId,
                from_date: fromDate,
                to_date: toDate,
            };
            $.ajax({
                url: "views/custom_list_mobile_user_registrations_table.php",
                data: {sendData: requestData,},
                type: "GET",
                success:function(data){
                    $('#TableContent').html(data);
                },
                error:function (){}
            });
        });
    });
</script>

