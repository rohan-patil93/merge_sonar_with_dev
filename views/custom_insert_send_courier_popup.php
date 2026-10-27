<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetcourier&id=".$_POST['id'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$emps = $data['employees'];
$row = $data['rowdata'];
?>
<script type="text/javascript">
    function submitincourierfrom(){
        var post = $("#submitformadditionalservice").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=addcouriers&table_name=couriers&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.reload();
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Add Out Courier  </h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitformadditionalservice" >
    <div  class="row col-lg-12"  <?php if($row['created_by'] != $_SESSION['userid'] && $row['current_status']<11 && $_POST['id'] !=""  ){ ?>  style="pointer-events: none; opacity: 0.7;" <?php
} ?>  >
        <div class="form-group col-lg-6">
            <span>From (Sender)</span>
            <input aria-label="From Detail" type="text" name="from_detail" id="from_detail" class="form-control" value="<?php echo $row['from_detail']; ?>">
        </div>
        <div class="form-group col-lg-6">
            <span>To (Recipient)</span>
            <input aria-label="To Detail" type="text" name="to_detail" id="to_detail" class="form-control" value="<?php echo $row['to_detail']; ?>">
        </div>
        <div class="form-group col-lg-6">
            <span>Courier Name</span>
            <input aria-label="Courier Company" type="text" name="courier_company" id="courier_company" class="form-control" value="<?php echo $row['courier_company']; ?>">
        </div>
        <div class="form-group col-lg-3">
                <span>Date</span>
                <input aria-label="Courier Date" type="text" name="courier_date" class="form-control datepicker" value="<?php echo fromDate($row['courier_date']); ?>">
            </div>
            <div class="form-group col-lg-3">
                <span>Time</span>
                <input aria-label="Courier Time" type="time" name="courier_time" step="1" class="form-control " value="<?php echo fromtime($row['courier_time']); ?>">
            </div>
            <div class="form-group col-lg-6">
                <span>Courier Reference Number</span>
                <input aria-label="Courier Ref No" type="text" name="courier_ref_no" id="courier_ref_no" class="form-control" value="<?php echo $row['courier_ref_no']; ?>">
            </div>
            <div class="form-group col-lg-6">
                <span>Quantity</span>
                <input aria-label="Courier Qty" type="text" id="courier_qty" name="courier_qty" class="form-control" value="<?php echo $row['courier_qty']; ?>">
            </div>
            <div class="form-group col-lg-6">
                <span>Purpose</span>
                <textarea aria-label="Description" name="description" id="description" class="form-control"> <?php echo $row['description']; ?></textarea>
            </div>
        <div class="form-group col-lg-6">
            <span>Address</span>
            <textarea aria-label="Courier Adderss" class="form-control" name="courier_adderss" id="courier_adderss" ><?php echo $row['courier_adderss']; ?></textarea>
        </div>
        <div class="form-group col-lg-6">
            <span>Description</span>
            <textarea aria-label="Remarks" class="form-control" name="remarks" id="remarks" ><?php echo $row['remarks']; ?></textarea>
        </div>
        <div class="form-group w-full col-lg-6">
            <span  class="d-block">Requested By </span>
            <select aria-label="Requested By"  name="requested_by" id="requested_by" class="form-select js-example-basic-single">
                <option value="">Select Any One</option>
                <?php foreach($emps as $emp){ ?>
                    <option <?php if($row['requested_by']== $emp['id']){ echo "selected";
} ?>  value="<?php echo $emp['id']; ?>"><?php echo $emp['emp_code'].' - '.$emp['display_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-lg-6">
                <span>Invoice No</span>
                <input aria-label="Invoice No" type="text" id="invoice_no" name="invoice_no" class="form-control" value="<?php echo $row['invoice_no']; ?>">
            </div>
        <div class="form-group col-lg-6">
            <span>Mobile</span>
            <input aria-label="Courier Mobile" type="text" name="courier_mobile" id="courier_mobile" class="form-control" value="<?php echo $row['courier_mobile']; ?>">
        </div>
        <?php  ?>

        <?php if($row['id'] !=""){ ?>

        <div class="form-group col-lg-6">
                <span>Status </span>
                <div class="radio radio-outline  mt-1">
                    <?php foreach ($data['status'] as $selectkey => $selectvalue) {   ?>
                        <label class="radio radio-inline"> <input aria-label="Radio option" type="radio"  <?php if($selectkey == $row['current_status']){ ?> checked <?php
} ?> name="current_status" value='<?php echo $selectkey; ?>'>    <span></span><?php echo $selectvalue; ?> </label>
                        <?php $sch++;
} ?>
                    </div>
                </div>
    <?php } ?>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="location_id" id="location_id" value="<?php echo $_SESSION['location_id']; ?>">
            <input type="hidden" name="courier_type" id="courier_type" value="1">
            <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
            <?php if($row['id']==""){?>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['userid']; ?>">
             <?php } ?>
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitincourierfrom()">Save</button>
        </div>
    </form>
</div>


<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
$(".js-example-basic-single").select2({
    dropdownParent: $("#kt_modal_KTDatatable_remote")
});
$(".js-example-basic-multiple").select2({
    dropdownParent: $("#kt_modal_KTDatatable_remote")
});
$(".js-example-disabled-results").select2({
    dropdownParent: $("#kt_modal_KTDatatable_remote")
});
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
