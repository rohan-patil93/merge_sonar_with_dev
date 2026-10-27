<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_id = $_POST['task_id'];
$url = API."?mode=togetprojecttaskdetailspopup&task_id=".$task_id;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$task = $data['taskdetils'];
$project_teams = $data['project_teams'];
$projec_phases = $data['phases'];
?>
<script type="text/javascript">
    function submitsubfrom(){
        var post = $("#submitformadditionalservice").serialize();
        var task_id =  $('#parent_id').val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=project_task_details&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                taskdetailspopup(task_id);
            },
            error:function (){}
        });
    }
    function taskdetailspopup(id){
        console.log("check task"+id);
        jQuery.ajax({
            url: "views/custom_insert_project_task_detail_popup.php",
            data:'option=noption&task_id='+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#modal_status").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#kt_modal_dynamic_popup').modal("show");
                checkllll();
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title"> Add Sub Task </h5>
    <button type="button" class="btn-close "  aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitformadditionalservice" >
        <div class="row col-lg-12">
            <div class="form-group col-lg-6">
                <span class="">Task Name</span>
                <input aria-label="Task Name" class="form-control" type="text" id="task_name    " name="task_name    " value="">
            </div>
            <div class="form-group col-lg-6">
                <span class="">Descrption</span>
                <textarea aria-label="Description" class="form-control" name="description" id="description"></textarea>
            </div>
            <div class="form-group col-lg-6">
                <span class="">Task Owner</span>
                <select aria-label="Assigen" class="form-control js-example-basic-single"  name="assigen_id" id="assigne">
                    <option value="0"> Select Assigner</option>
                    <?php foreach($project_teams as $team){ ?>
                        <option value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-6">
                <span class="">Project Phase</span>
                <select aria-label="Project Phase" class="form-control js-example-basic-single"  name="project_phase_id" id="project_phase_id">
                    <option value="0"> Select Project Phase</option>
                    <?php foreach($projec_phases as $phase){ ?>
                        <option <?php if($phase['id']==$task['project_phase_id']){ echo "selected";
} ?> value="<?php echo $phase['id']; ?>"> <?php echo $phase['project_phase_name']; ?> </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-lg-6">
                <span class="">Start Date</span>
                <input aria-label="Planned Start" type="text" name="planned_start" value="<?php echo fromDate($task['planned_start']); ?>" class="form-control datepicker">
            </div>
            <div class="form-group col-lg-6">
                <span class="">Due Date</span>
                <input aria-label="Planned Finish" type="text" name="planned_finish" value="<?php echo fromDate($task['planned_start']); ?>" class="form-control datepicker">
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="company_id" id='company_id' value="<?php echo $_SESSION['company_id']; ?>">
            <input type="hidden" name="project_id" id='project_id' value="<?php echo $task['project_id']; ?>">
            <input type="hidden" name="task_type" id='task_type' value="3">
            <input type="hidden" name="parent_id" id='parent_id' value="<?php echo $task['id']; ?>">
            <input type="hidden" name="customer_id" id='customer_id' value="<?php echo $task['customer_id']; ?>">
            <input type="hidden" name="current_status" id='current_status' value="0">


            <button type="button" class="btn btn-primary mr-2" onclick="submitsubfrom()" >Submit</button>
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
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>
