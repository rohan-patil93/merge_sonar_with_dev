<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$wh1["where"] = array('company_id'=>$_SESSION['company_id'],'genc_code'=>'SEPERATION_MODE','is_active'=>0);
$wh1["field"] = "genc_detail_code,genc_detail_name";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1;
$data_details1 = callAPI($url1);
$result = json_decode($data_details1,1);

?>
<script type="text/javascript">
    function insertEmployeeTerminations(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var formdata = $("#employeeterminations_form").serialize();
            jQuery.ajax({
                url: "ajax.php",
                data:"page=insertemployeeterminations&"+formdata,
                type: "POST",
                success:function(data){
                    console.log(data);
                    $('#kt_modal_dynamic_popup').modal("hide");
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    location.reload();
                },
                error:function (){}
            });
        }

    }
</script>
<div class="modal-header  ">
    <h5 class="modal-title">Employee Termination </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row">
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Employee Name</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $_POST['display_name']; ?></h6>
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Employee Code</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $_POST['emp_code']; ?></h6>
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Email Id</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $_POST['email_id']; ?></h6>
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Joining Date</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo fromDate($_POST['joining_date']); ?></h6>
        </div>
    </div>
    <form id="employeeterminations_form">
        <div class="row mt-5">
            <div class="col-lg-6 form-group  w-full">
                <span class="form-label">Termination Reason<span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <select aria-label="Termination Type" class="js-example-basic-single requried_field" name="termination_type">
                    <option value="">Select Any One</option>
                    <?php foreach($result as $tertype){ ?>
                    <option value="<?php echo $tertype['genc_detail_code']; ?>"><?php echo $tertype['genc_detail_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-6 form-group ">
                <span class="form-label">Termination Date <span class="requried">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="Effective Date" type="text" class="form-control setdatepicker requried_field" name="effective_date" value="">
            </div>
        </div>
        <input type="hidden" name="emp_id" value="<?php echo $_POST['emp_id']; ?>">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="insertEmployeeTerminations()"> Submit</button>
</div>
