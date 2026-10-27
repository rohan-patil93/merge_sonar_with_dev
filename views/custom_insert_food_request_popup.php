<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=togetinsertdetalsfoods&food_option_id=".$_POST['option_id']."&rowid=".$_POST['rowid'];
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);
$row = $data['food_options'];
$rowdata = $data['value'];
$start = $rowdata['start_date'] !="" ? displayDate($rowdata['start_date']) : "";
$end = $rowdata['stop_date'] !="" ? displayDate($rowdata['stop_date']) : "";
?>
<script type="text/javascript">
    function submitintranspotfrom(){
        var post = $("#submitformadditionalservice").serialize();
        console.log(post);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=addfoodrequest&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=4';
            },
            error:function (){}
        });
    }
    function updatefoodfrom(update_id){
        console.log("Checkkkkk");
        var id = $('input[name=current_status]:checked').attr('id');

          var status = $('#'+id).val();
          console.log("status:"+status);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatefoodrequest&current_status='+status+'&update_id='+update_id,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=4';
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

    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id'];?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['userid'];?>">
    <input type="hidden" name="food_option_id" value="<?php echo $row['id'];?>">
    <div  <?php if( count((array)$rowdata)>0){ ?> style="pointer-events: none; opacity: 0.7;" <?php
} ?> >
        <div class="form-group">
            <span>Food Type</span>
            <input aria-label="Input field" type="text" readonly class="form-control" name="" value="<?php echo $row['food_option']; ?>">
        </div>
        <div class="form-group">
            <span>Start Date</span>
            <input aria-label="Start Date" type="text" name="start_date" class="form-control datepicker" value="<?php echo $start; ?>">

        </div>
        <div class="form-group">
            <span>End Date</span>
            <input aria-label="Stop Date" type="text"  class="form-control datepicker" name="stop_date" value="<?php echo $end; ?>">
        </div>
        <div class="form-group">
            <span>Comments</span>
            <textarea aria-label="Comments" class="form-control" name="comments"><?php echo $rowdata['comments'] ?></textarea>
        </div>
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
            <button type="button" class="btn btn-primary waves-effect waves-light " onclick="updatefoodfrom(<?php echo $rowdata['id']; ?>)">Save</button>

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
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-basic-multiple").select2();
    $(".js-example-disabled-results").select2();
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>
