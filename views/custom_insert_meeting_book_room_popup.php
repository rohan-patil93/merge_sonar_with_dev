<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$vurl = API . "?mode=tobeforeaddmeetingrommbook&id=" . $_POST['id'];
$configesponse = callAPI($vurl);
$data = json_decode($configesponse, 1);
$cabin_res = $data['cabin_res'] ?? array();
$rowdata = $data['meeting_rooms_res'] ?? array();

?>
<script type="text/javascript">
    function tosavemrttingroom(){
        var formdata = $('#frmsubmit').serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=tosavemrttingroom&'+formdata,
            success: function(response){
                console.log(response);
                window.location.reload();
            }
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">
        <small>&nbsp;&nbsp; <b class="f-w-600">Meeting Rooms</small>
    </h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form action="" id="frmsubmit">
        <div>
            <div class="form-group">
                <span>Cabin Name</span></br>
                <select aria-label="Building Floor Space" class="form-control js-example-basic-single w-full" name="building_floor_space_id" id="building_floor_space_id">
                    <option value="">Select Cabin</option>
                    <?php foreach ((array)$cabin_res as $selectkey => $selectvalue) {  ?>
                        <option value="<?php echo $selectkey; ?>" <?php echo $rowdata['building_floor_space_id'] == $selectkey ? "selected" : ''; ?>><?php echo $selectvalue; ?></option>
                    <?php } ?>
                </select>

            </div>

            <div class="form-group">
                <span>Booking Date</span>
                <input aria-label="Booking Date" type="date" class="form-control " name="booking_date" id="booking_date" value="<?php echo $rowdata['booking_date']; ?>" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <span>Booking Start Time</span>
                <input aria-label="Booking Start Time" type="time" name="booking_start_time" id="booking_start_time" class="form-control " value="<?php echo $rowdata['booking_start_time']; ?>">
            </div>
            <div class="form-group">
                <span>Booking End Time</span>
                <input aria-label="Booking End Time" type="time" class="form-control " name="booking_end_time" id="booking_end_time" value="<?php echo $rowdata['booking_end_time']; ?>">
            </div>
            <div class="form-group">
                <span>Request Reason</span>
                <textarea aria-label="Request Reason" class="form-control" name="request_reason" id="request_reason"><?php echo $rowdata['request_reason'] ?></textarea>
            </div>
        </div>

    <?php
    $isUpdate = !empty($rowdata['id']);
    if (!$isUpdate): ?>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="tosavemrttingroom()">Save</button>
            <input type="hidden" name="submit" value="insert" >
        </div>
    <?php else: ?>
        <div class="modal-footer">
            <input type="hidden" name="id" value="<?php (int)$rowdata['id'] ?>">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" >Update</button>
            <input type="hidden" name="submit" value="update" >
        </div>
    <?php endif; ?>
</div>
</form>

</div>
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-basic-multiple").select2();
    $(".js-example-disabled-results").select2();
    $(".datepicker").datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#booking_end_time').on('change', function() {
            var endTime = $(this).val();
            $('#booking_start_time').attr('max', endTime);
            if ($('#booking_start_time').val() > endTime) {
                $('#booking_start_time').val(endTime);
            }
        });
        $('#booking_start_time').on('change', function() {
            var startTime = $(this).val();
            console.log(startTime);
            $('#booking_end_time').attr('min', startTime);
            $('#booking_end_time').val(startTime);

        });
    });
</script>
