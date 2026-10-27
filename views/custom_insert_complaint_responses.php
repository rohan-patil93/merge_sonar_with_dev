
<?php
if($_POST['submit']){
    $table_name = 'complaint_responses';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);
        if ($response["status"] == "sucess") {
            header("location:index.php?option=COM_LIST_COMPLAINTS");
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
}
?>

<!-- Page body start -->

<div class="page-body">

    <div class="card">
        <div class="card-header">
            <h5>Complaint Response</h5>

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
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Complaint Name</span>
                            <input aria-label="Input field" class="form-control" type="text"  value="<?php echo $return['complaint_name']; ?>" disabled/>
                        </div>

                        <div class="col-lg-4 col-xs-8 form-group">
                            <span>Complaint Date</span>
                            <input aria-label="Select date" class="form-control" type="date"  value="<?php echo $return['transaction_date']; ?>" disabled/>

                        </div>
                        <div class="col-lg-2 col-xs-4 form-group">
                            <span>&nbsp;</span>
                            <button  type="button" class="form-control btn btn-primary " id="history">History</button>
                        </div>
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Response Comments</span>
                            <input aria-label="Remarks" class="form-control" type="text" name="remarks" id="remarks" value="" />
                        </div>
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Response Date<span style="color:red;">*</span></span>
                            <input aria-label="Transaction Date" class="form-control" type="date" name="transaction_date" id="transaction_date"  value="<?php echo date('Y-m-d'); ?>"/>
                            <div style="display: none;color: red;" id="transaction_date_err"></div>
                        </div>

                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Complaint Image</span>
                            <input aria-label="Complaint Image" class="form-control" type="file" name="complaint_image" id="complaint_image" value="" />
                        </div>

                    </div>
                    <div class="border-top">
                        <input name="complaint_id" id="complaint_id" type="hidden" value="<?php echo $rid; ?>">
                        <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary mt-2">
                            Save
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>

<script>
let complaint_id = <?php echo json_encode($_GET['Rid']); ?>;
    $(document).ready(function () {
        $('#history').on('click', function () {
            $.ajax({
                url: "views/custom_view_complaint_responses.php",
                data: {Rid: complaint_id,},
                type: "GET",
                success:function(data){
                    $('#popup1').html(data);
                    $('#kt_modal_KTDatatable_remote').modal("show");
                },
                error:function (){}
            });
        });
    });
</script>

