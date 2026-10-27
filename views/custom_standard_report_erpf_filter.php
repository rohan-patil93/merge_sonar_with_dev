<?php
  $from_date = $from_date !="" ? fromDate($from_date) : "";
  $to_date = $to_date !="" ? fromDate($to_date) : "";
?>
<script type="text/javascript">
    function enddatechange(){
        console.log("check date");
        var sd = $("#date_of_grant").val();
        $("#date_of_end").val(sd);
    }
</script>
  <div class="col-lg-4">
    <input type="hidden" id="backdate_limit" value="1360">
    <span class="form-label"> Start Date</span>
    <div class="input-group custom-date">
        <span class="input-group-text addon-style" id="basic-addon3">
            <span class=""><i class="fa fa-calendar"></i></span>
        </span>
        <input aria-label="Start Date" type="text" class="form-control datepicker" name="start_date" id="date_of_grant" onchange="enddatechange()" value="<?php echo $from_date; ?>" required>
    </div>
  </div>
  <div class="col-lg-4">
    <span class="form-label"> End Date</span>
    <div class="input-group custom-date">
        <span class="input-group-text addon-style" id="basic-addon3_2">
            <span class=""><i class="fa fa-calendar"></i></span>
        </span>
        <input aria-label="To Date" type="text" class="form-control datepicker" name="to_date" id="date_of_end" value="<?php echo $to_date; ?>" required>
    </div>
  </div>
