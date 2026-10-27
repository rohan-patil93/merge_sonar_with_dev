<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=togetallslectedtrasportdetails&transport_schedule_id=".$_POST['id']."&rowid=".$_POST['rowid'];
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);
$row = $data['rowdata'];
$rowdata = $data['value'];
?>
<script type="text/javascript">
    function submitintranspotfrom(){
        var post = $("#submitformadditionalservice").serialize();
        console.log(post);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=toinserttranspordatarequest&table_name=employee_transport_requests&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=3';
            },
            error:function (){}
        });
    }
    function updatetranspotfrom(update_id){
        console.log("Checkkkkk");
        var id = $('input[name=current_status]:checked').attr('id');

          var status = $('#'+id).val();
          console.log("status:"+status);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatetranspotfrom&current_status='+status+'&update_id='+update_id,
            type: "POST",
            success:function(data){
                console.log(data);
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">
        <small>&nbsp;&nbsp; <b class="f-w-600">Transport</small>
    </h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitformadditionalservice" >
        <input type="hidden" name="location_id" value="<?php echo $row['location_id'];?>">
    <input type="hidden" name="transport_schedule_id" value="<?php echo $row['transport_schedule_id'];?>">
    <input type="hidden" name="route_id" value="<?php echo $row['transport_route_id'];?>">
    <input type="hidden" name="company_transport_id" value="<?php echo $row['company_transport_id'];?>">
    <input type="hidden" name="pickup_point" value="<?php echo $row['stop_name'];?>">
    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id'];?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['userid'];?>">
    <div    style="pointer-events: none; opacity: 0.7;"  >
        <div class="form-group">
            <span>Vehicle Name</span>
            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $row['vehicle_name']; ?>">
        </div>
        <div class="form-group">
            <span>Vehicle Type</span>
            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $row['vehicle_type']; ?>">
        </div>
        <div class="form-group">
            <span>Stop Name</span>
            <input aria-label="Input field" type="text"  class="form-control" value="<?php echo $row['stop_name']; ?>">
        </div>
        <div class="form-group">
            <span>Arrival Time</span>
            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $row['arrival_time']; ?>">
        </div>
        <div class="form-group">
            <span>Monthly Charges</span>
            <input aria-label="Input field" type="text"  class="form-control" value="<?php echo $row['route_charges_monthly']; ?>">
        </div>
    </div>

    <div class="form-group" <?php if( count((array)$rowdata)>0){ ?> style="pointer-events: none; opacity: 0.7;" <?php
} ?> >
            <span>Month </span>
            <?php
                $mshow = $rowdata['transport_date']!="" ? date('Y-m', strtotime($rowdata['transport_date'])) : "";
            ?>
            <input aria-label="Transport Date" type="month" name="transport_date" id="transport_date" class="form-control" value="<?php echo $mshow; ?>">
    </div>

    <?php if( count((array)$rowdata)==0){ ?>
        </div>
    <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitintranspotfrom()">Save</button>

        </div>
    </form>

    <?php }elseif($row['created_by'] != $_SESSION['userid'] && $_SESSION['admin_type'] ==1 && $rowdata['current_status']!=ST_APPROVED){ ?>
            <div class="form-group">
                <span>Status </span>
                <div class="radio radio-outline  mt-1">
                    <?php foreach ($data['status'] as $selectkey => $selectvalue) {  if($selectkey >10){   ?>
                        <label class="radio radio-inline"> <input aria-label="Radio option" type="radio"  id="current_status_<?php echo $selectkey; ?>"  name="current_status" value='<?php echo $selectkey; ?>'>    <span></span><?php echo $selectvalue; ?> </label>
                        <?php $sch++; ?>
                        <?php } ?>
                        <?php } ?>
                </div>
            </div>

    </div>
    <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light " onclick="updatetranspotfrom(<?php echo $rowdata['id']; ?>)">Save</button>

        </div>
    </form>
    <?php }else{ ?>
        <div class="form-group" style="pointer-events: none; opacity: 0.7;">
                <span>Status </span>
                <div>
                    <?php echo $data['status'][$rowdata['current_status']]; ?>
                </div>
            </div>

    </div>
    <?php } ?>

</div>
