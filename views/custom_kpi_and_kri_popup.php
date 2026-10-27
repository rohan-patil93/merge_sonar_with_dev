<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = $_POST['kpitasktable'];
$url = API."?mode=getkipandkradetails&kpi_tasks_ids=".$_POST['kpi_tasks_ids']."&kpi_kra_popup=Y&kpitasktable=".$table;
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);

$tr = $kipandkradetails['kpi_taskslist'];
$frequencyllist = $kipandkradetails['task_frequency_list'];

?>
<script type="text/javascript">
    function mvaluechange(id){
        var mval = $('#mandatory_'+id).val();
        var change =( mval!=1 ) ? 1 : 0;
        $('#mandatory_'+id).val(change);
    }
    function addmoreRows() {
        var count = $(".itemRow123").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr class="itemRow123" id="tr_'+count+'" >';
        htmlRows += '<td><input type="hidden" value="" id="kpi_task_id_'+count+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><input type="hidden" value="0" id="delete_value_'+count+'" ><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-danger" id="'+count+'" onclick="deletestatickRows('+count+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td>';

        htmlRows += '<td><input aria-label="Name[]" type="text" name="name[]" class="form-control" value="" id="name_'+count+'"></td> ';
        htmlRows += '<td><textarea aria-label="Descrption[]" class="form-control" name="descrption[]" id="descrption_'+count+'"></textarea></td>';
        htmlRows += '<td><input aria-label="Short Code[]" type="text" name="short_code[]" class="form-control" value="" id="short_code_'+count+'"></td>';
       htmlRows += '<td><select aria-label="Frequency[]" class="form-control js-example-basic-single" name="frequency[]" id="frequency_'+count+'" ><option value="0">Select Any One</option><?php foreach ($frequencyllist as  $frequency) { ?><option value="<?php echo $frequency['genc_detail_code']; ?>"><?php echo $frequency['genc_detail_name']; ?></option><?php
} ?></select></td>';

       htmlRows += '<td> <input aria-label="Mandatory[]" class="js-single'+count+'"  type="checkbox" onclick="mvaluechange('+count+')" id="mandatory_'+count+'"   value="0"  name="mandatory[]" onkeydown=""><span></span></label></span></td>';

       htmlRows += '</tr>';
       $('#invoiceItem123').append(htmlRows);
       $(".js-example-basic-single").select2();
       $(".js-example-disabled-results").select2();
       $( ".datepicker" ).datepicker({
           showOn: "both",
           buttonText: "<i class='ti-calendar'></i>",
           dateFormat: date_format,
           changeMonth: true,
           changeYear: true
       });
       var elemsingle = document.querySelector('.js-single'+count);
       var switchery = new Switchery(elemsingle, { color: '#4099ff', jackColor: '#fff' });
   }
   function deletestatickRows(id){
       $('#tr_'+id).remove();
   }
   function submitrom(){
       var str = 'kpi_id='+$('#kpi_id').val();
       var kra_id = $("#KRA_ID").val();
       var row_id = $("#row_id").val();
       str += '&table='+$('#table').val();
       var i=0;
       $("[id^='name_']").each(function() {
           var id = $(this).attr('id');
           id = id.replace("name_",'');
           console.log($('#mandatory_'+id));
           if($('#mandatory_' + id).is(":checked")){
               var mandatory  = '1';
           }else{
               var mandatory  = '0';
           }
           i++;
           str += '&name_'+id+'='+$('#name_'+id).val()+'&descrption_'+id+'='+$('#descrption_'+id).val()+'&short_code_'+id+'='+$('#short_code_'+id).val()+'&frequency_'+id+'='+$('#frequency_'+id).val()+'&mandatory_'+id+'='+mandatory+'&kpi_task_id_'+id+'='+$('#kpi_task_id_'+id).val();
       });
       str += '&count='+i;
       $.ajax({
           url: "ajax.php",
           type: "POST",
           data: 'page=task_submit&'+str,
           success: function(response) {
               console.log(response);
               var respon = JSON.parse(response);
               console.log(respon);
               console.log(respon.insertorupdate);
               var errorresult = dynamicErrorShow(respon.insertorupdate);
               console.log(errorresult);

            if(errorresult.result == '0'){  // 0 successfull result
                $("#kpi_tasks_ids_"+kra_id+row_id).val(respon.lastid);
                   $('#kt_modal_KTDatatable_remote').modal('hide');
                   $('#kt_modal_dynamic_popup2').modal('hide');
                 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }


           }
       });
   }
</script>

<div class="modal-header">
    <h5 class="modal-title">Tasks</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<input type="hidden" id="KRA_ID" value="<?php echo  $_POST['kpi_id']; //name kpi but value kra ... ?>">
<input type="hidden" id="row_id" value="<?php echo  $_POST['row']; //name kpi but value kra ... ?>">
<div class="modal-body" >
    <div class="row align-items-center" style="margin-top: 10px;padding: 3px;">
        <div class="col-lg-12">
            <table class="table table-striped- table-bordered table-hover table-checkable  no-footer"  id="invoiceItem123">
                <tr>
                    <th scope="col" class="thheader " style="width: 5%;"></th>
                    <th scope="col" class="text-center" style="width: 25%;">Name</th>
                    <th scope="col" class="thheader text-center" style="width: 30%;">Descrption</th>
                    <th scope="col" class="thheader text-center" style="width: 15%;">Short Code</th>
                    <th scope="col" class="thheader text-center" style="width: 15%;">Frequency</th>
                    <th scope="col" class="thheader text-center" style="width: 10%;">Mandatory</th>

                </tr>
                <?php
                $count = 1;
                if(count((array)$tr) == '0'){
                    $tr[] = array();
                }
                foreach ($tr as $td) { ?>

                    <tr class="itemRow123" id="tr_<?php echo $count; ?>"  >
                        <td>
                            <input type="hidden" value="<?php echo $td['id']; ?>" id="kpi_task_id_<?php echo $count; ?>" >
                            <input type="hidden" value="0" id="delete_value_<?php echo $count; ?>" >
                            <i class="fa fa-trash text-danger" id="<?php echo $count; ?>"  onclick="deletestatickRows(<?php echo $count; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                        </td>
                        <td>
                            <input aria-label="Name[]" type="text" name="name[]" class="form-control" value="<?php echo $td['task_name'] ?>" id="name_<?php echo $count; ?>">
                        </td>
                        <td>
                            <textarea aria-label="Descrption[]" class="form-control" name="descrption[]"  id="descrption_<?php echo $count; ?>"><?php echo $td['description'] ?></textarea>
                        </td>
                        <td>
                            <input aria-label="Short Code[]" type="text" name="short_code[]" class="form-control" value="<?php echo $td['task_shot_code'] ?>" id="short_code_<?php echo $count; ?>">
                        </td>
                        <td>
                            <select aria-label="Frequency[]" class="form-control js-example-basic-single" name="frequency[]" id="frequency_<?php echo $count; ?>" >
                                <option value="0">Select Any One</option>
                                <?php foreach ($frequencyllist as  $frequency) { ?>
                                    <option <?php if($td['task_frequency'] == $frequency['genc_detail_code']){ ?> selected <?php
} ?> value="<?php echo $frequency['genc_detail_code']; ?>"><?php echo $frequency['genc_detail_name']; ?></option>
                                <?php } ?>

                            </select>

                        </td>
                        <td class="text-center">
                            <input aria-label="Checkbox"  class="js-switch" type="checkbox" onclick="mvaluechange(<?php echo $count; ?>)" id="mandatory_<?php echo $count; ?>" <?php   if($td['mandatory'] == '1'){ ?> checked <?php
} ?>  value = "1"  name="mandatory[]" onkeydown="" />
                        </td>
                        </tr>
                        <?php $count++;
}  ?>
                    </table>
                    <div class="row align-items-center" >
                        <button class="btn btn-success col-lg-2  m-l-10" id="addRows" type="button" onclick="addmoreRows()">+ Add More</button>

                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="table" id="table" value="<?php echo $table; ?>">
            <input type="hidden" name="kpi_id" id="kpi_id" value="<?php echo $_POST['id']; ?>">
            <input type="hidden" name="page" value="dis_submit">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <?php if($_POST['table'] !="employee_kpi_tasks"){ ?>
                <button type="button" class="btn btn-primary" onclick="submitrom()" >Submit</button>
            <?php } ?>
        </div>

    </form>
</div>
<!-- local datatable modal -->
<!-- Switch component js -->
<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
<script>
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>
