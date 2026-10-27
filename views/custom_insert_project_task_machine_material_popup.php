<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_id = $_POST['task_id'];
$type = $_POST['type'];
$url = API."?mode=togetprojecttaskmahinematerial&task_id=".$task_id."&id=".$_POST['id']."&project_id=".$_POST['project_id'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$project_machine_material = $data['project_machine_material'];
$task = $data['task'];
$row = $data['rowdata'];
$type = $_POST['type'];
$listtype  = array('MA'=>'Machine' , 'MT'=>'Material');
?>
<script type="text/javascript">

    function submitsubfrom(){
        var post = $("#submitformadditionalservicemm").serialize();
        var task_id =  $('#project_task_id').val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=project_task_machine_material&'+post,
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
    <h5 class="modal-title"> Add <?php echo $listtype[$type]; ?> </h5>
    <button type="button" class="btn-close "  aria-label="Close" onclick="taskdetailspopup(<?php echo $task['id']; ?>)"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitformadditionalservicemm" >
        <div class="row col-lg-12">
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
            <input type="hidden" name="project_id" id="project_id" value="<?php echo $task['project_id']; ?>">
            <input type="hidden" name="project_task_id" id="project_task_id" value="<?php echo $task['id']; ?>">
            <input type="hidden" name="type" id="type" value="<?php echo $type; ?>">

            <div class="form-group col-lg-6" id="machine">
                <span class=""><?php echo $listtype[$type]; ?></span>
                <select aria-label="Project Machine Material" class="form-control js-example-basic-single"  name="project_machine_material_id" id="project_machine_material_id">
                    <option value="0"> Select Any One <?php echo $listtype[$type]; ?>e</option>
                    <?php foreach($project_machine_material as $machine){
                          if($machine['type']==$type){ ?>
                        <option <?php if($row['project_machine_material_id'] == $machine['id']){ echo "selected";
} ?> value="<?php echo $machine['id']; ?>"> <?php echo $machine['name']; ?> </option>
                    <?php }
                        } ?>
                </select>
            </div>
            <div class="form-group col-lg-6">
                <span class="">Description</span>
                <textarea aria-label="Description" class="form-control" name="description" id="description"><?php echo $row['description']; ?></textarea>
            </div>

            <div class=" form-group col-lg-6">
                <span class="">Start Date</span>
                <input aria-label="Start Date" class="form-control datepicker" type="text" id="start_date" name="start_date" value="<?php echo $row['start_date']; ?>">
            </div>
            <div class="form-group col-lg-6">
                <span class="">End Date</span>
                <input aria-label="End Date" class="form-control datepicker" type="text" id="end_date" name="end_date" value="<?php echo $row['end_date']; ?>">
            </div>

        </div>
        <div class="modal-footer">
            <input type="hidden" name="company_id" id="company_id_2" value="<?php echo $_SESSION['company_id']; ?>">
            <input type="hidden" name="project_id" id="project_id_2" value="<?php echo $task['project_id']; ?>">
            <input type="hidden" name="current_status" id='current_status' value="0">
            <input type="hidden" name="is_active" id='is_active' value="0">
            <input type="hidden" name="id" id='id' value="<?php echo $_POST['id'] ?>">

            <button type="button" class="btn btn-primary mr-2" onclick="submitsubfrom()" >Save</button>
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
