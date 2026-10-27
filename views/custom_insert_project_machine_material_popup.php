<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_id = $_POST['task_id'];
$type = $_POST['type'];
$url = API."?mode=togetprojectmahinematerial&project_id=".$_POST['project_id']."&id=".$_POST['id'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$mtypes = $data['mtypes'];
$items = $data['items'];
$machines = $data['machines'];
$task = $data['task'];
$row = $data['rowdata'];
$listtype  = array('MA'=>'Machine' , 'MT'=>'Material');
?>
<script type="text/javascript">
    function costdisplay(ele){
        var country_select = document.querySelector("#machine_id");
        var myTag = country_select.options[country_select.selectedIndex].getAttribute('myTag');
        console.log("cost"+myTag);
        $('#budget_cost').val(myTag);
    }
    function showdetails(val){
        machine
        rental
        if(val=='A'){
            $('#rental').hide();
            $('#machine').show();
        }else if(val=='R'){
            $('#machine').hide();
            $('#rental').show();
        }else{
            $('#machine').hide();
            $('#rental').hide();
        }

    }
    function submitsubfrom(){
        var post = $("#submitformadditionalservicemm").serialize();
        var project_id =  $('#project_id').val();
        var type =  $('#type').val();
        var rid = 10;
        if(type == 'MT'){ rid = 11; }
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=project_machine_material&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = "index.php?option=COM_VIEW_PROJECT&RID="+project_id+"&TAB="+rid;
            },
            error:function (){}
        });
    }

</script>
<div class="modal-header">
    <h5 class="modal-title"> Add <?php echo $listtype[$type]; ?> </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form " id="submitformadditionalservicemm" >
        <div class="row col-lg-12">
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
            <input type="hidden" name="project_id" id="project_id" value="<?php echo $task['project_id']; ?>">
            <input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
            <div class="form-group col-lg-6">
                <span class=""><?php echo $listtype[$type]; ?> Name</span>
                <input aria-label="Name" class="form-control" type="text" id="name" name="name" value="<?php echo $row['name']; ?>">
            </div>
            <?php if($type == 'MA'){ ?>

                <div class="form-group col-lg-6"   id="machine">
                    <span class="">Machines</span>
                    <select aria-label="Machine" class="form-control js-example-basic-single"  name="machine_id" id="machine_id" onchange="costdisplay(this)">
                        <option value="0"> Select Machine Type</option>
                        <?php foreach($machines as $machine){ ?>
                            <option myTag="<?php echo $machine['cost']; ?>"  <?php if($row['machine_id'] == $machine['id']){ echo "selected";
} ?> value="<?php echo $machine['id']; ?>"> <?php echo $machine['machine_name']; ?> </option>
                        <?php } ?>
                    </select>
                </div>

            <?php } ?>
            <?php if($type == 'MT'){ ?>
                <div class="form-group col-lg-6">
                    <span class="">Material</span>
                    <select aria-label="Item" class="form-control js-example-basic-single"  name="item_id" id="machine_id_2"  onchange="costdisplay(this)">
                        <option value="0"> Select Machine Type</option>
                        <?php foreach($items as $item){ ?>
                            <option myTag="<?php echo $item['cost']; ?>" <?php if($row['item_id'] == $item['id']){ echo "selected";
} ?> value="<?php echo $item['id']; ?>"> <?php echo $item['item_name']; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <div class="form-group col-lg-6">
                <span class="">Description</span>
                <textarea aria-label="Description" class="form-control" name="description" id="description"><?php echo $row['description']; ?></textarea>
            </div>
            <div class="row">
                <div class=" form-group col-lg-6">
                    <span class="">Cost (Per Day)</span>
                    <input aria-label="Budget Cost" class="form-control text-right" type="text" id="budget_cost" name="budget_cost" value="<?php echo $row['budget_cost']; ?>">
                </div>
                <div class="form-group col-lg-6">
                    <span class="">Bill Rate (Per Day)</span>
                    <input aria-label="Bill Rate" class="form-control text-right" type="text" id="bill_rate" name="bill_rate" value="<?php echo $row['bill_rate']; ?>">
                </div>
            </div>
        </div>
        <div class="modal-footer">

            <input type="hidden" name="project_id" id="project_id_2" value="<?php echo $_POST['project_id']; ?>">
            <input type="hidden" name="current_status" id='current_status' value="0">
            <input type="hidden" name="is_active" id='is_active' value="0">
            <input type="hidden" name="id" id='id' value="<?php echo $_POST['id'] ?>">

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
