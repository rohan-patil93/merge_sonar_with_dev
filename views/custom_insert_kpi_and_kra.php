<?php
$kratable = "appraisal_key_result_areas";
$kpitable = "appraisal_key_performance_indicators";
$kpitasktable = "appraisal_kpi_tasks";
if(isset($_POST['submit_value']) && $_POST['submit_value']=="insertkpiandkra"){
    $_POST['kratable'] =  $kratable;
    $_POST['kpitable'] =$kpitable;
    $_POST['kpitasktable'] = $kpitasktable;
    $url = API."?mode=addkpidata&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }
        }
    </script>
    <?php
}

$url = API."?mode=getkipandkradetails&appraisal_id=".$performance_appraisal_setup_id."&kratable=".$kratable."&kpitable=".$kpitable."&kpitasktable=".$kpitasktable;
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);
$modellist  = $kipandkradetails['modellist'];
$upmllist = $kipandkradetails['uomllist'];
$kralist = $kipandkradetails['kralist'];
$performance_setup  = $kipandkradetails['performance_setup'];
$kpilistres = $kipandkradetails['kpilist'];
foreach($kpilistres as $kpilistre){
    $kpilist[$kpilistre['key_reslut_id']][] = $kpilistre;
}

?>
<script type="text/javascript">
    function editKRA(id){
   var table = '<?php echo $kratable; ?>';
   jQuery.ajax({
       url: "views/custom_edit_key_result_areas.php",
       data:'id='+id+'&table='+table,
       type: "POST",
       success:function(data){
           $('#dynamic_popup11').html(data);
           $("#kt_modal_dynamic_popup2").modal("show");
            $(".js-example-basic-single").select2();
       },
       error:function (){}
   });
}
function taskpopupkpikra(kpi_id,kri_id,row){
    var kpitasktable = '<?php echo $kpitasktable; ?>';
    var kpi_tasks_ids = $("#kpi_tasks_ids_"+kpi_id+row).val();
    jQuery.ajax({
        url: "views/custom_kpi_and_kri_popup.php",
        data:'page=dis_att_type&id='+kri_id+'&kpi_id='+kpi_id+'&kpi_tasks_ids='+kpi_tasks_ids+'&row='+row+"&kpitasktable="+kpitasktable,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
    $("#kt_modal_dynamic_popup2").modal("show");
},
error:function (){}
});
}

function addrows(kpi_id,returnhtml=""){
    var count = $(".itemRow").length;
    count++;
    var htmlRows = '';
    htmlRows += '<tr>';
    htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="Checkbox" class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
    htmlRows += '<td><input aria-label="Name[" type="text" name="name['+kpi_id+'][]"  class="form-control"   autocomplete="off"> </td>';
    htmlRows += '<td><select aria-label="Model[" class="form-control form-select js-example-basic-single" name="model['+kpi_id+'][]" ><option value="0">Select Any One</option><?php foreach ($modellist as  $model) { ?><option value="<?php echo $model['genc_detail_code']; ?>"><?php echo $model['genc_detail_name']; ?></option><?php
} ?></select></td>';

    htmlRows += '<td><select aria-label="Uom[" class="form-control form-select js-example-basic-single" name="uom['+kpi_id+'][]" ><option value="0">Select Any One</option><?php foreach ($upmllist as  $uom) { ?><option value="<?php echo $uom['genc_detail_code']; ?>"><?php echo $uom['genc_detail_name']; ?></option><?php
} ?></select></td>';

    htmlRows += '<td><input aria-label="Weightage[" type="text" name="weightage['+kpi_id+'][]"  onblur="weightagecheck('+kpi_id+')" class="form-control amount weightage_'+kpi_id+' text-right" value="0" autocomplete="off"><input type="hidden" name="kpi_tasks_ids['+kpi_id+'][]" id="kpi_tasks_ids_'+kpi_id+count+'" value=""></td>';

    htmlRows += '</tr>';
    if(returnhtml == "Y"){
        return htmlRows;
    }else{
        $('#invoiceItem_'+kpi_id).append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $( ".datepicker" ).datepicker({
            showOn: "both",
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        });
    }
}
function addrowskra(type){ // insert or update
    var kratable = '<?php echo $kratable; ?>';
    var appraisal_id = $("#kpiandkra_appraisal_id").val();
    var assessment_year = $("#kpiandkra_assessment_year").val();

    if(type == "update"){
        var kra_id = $("#pop_kra_id").val();
        var text_area_name = $("#pop_area_name").val();
        var text_descrption= $("#pop_descrption").val();
        var text_weightage = $("#pop_weightage").val();
        var kra_type = $("#pop_kra_type").val();
    }else{
        var kra_id = "";
        var text_area_name = $("#text_area_name").val();
        var text_descrption= $("#text_descrption").val();
        var text_weightage = $("#text_weightage").val();
        var kra_type = $("#textkra_type").val();

    }
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=addkra&area_name='+text_area_name+'&description='+text_descrption+'&weightage='+text_weightage+'&kra_id='+kra_id+"&appraisal_id="+appraisal_id+"&assessment_year="+assessment_year+"&kra_type="+kra_type+"&kratable="+kratable,
        success: function(response) {
            console.log(response);
            var respon = JSON.parse(response);
            var errorresult = dynamicErrorShow(respon);
               if(errorresult.result == '0'){  // 0 successfull result
                   if(type == "update"){
                       $("#kt_modal_dynamic_popup2").modal("hide");
                       $("#text_kra_name_"+kra_id).text(text_area_name);
                       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

                   }else{
                       addkraHTML(errorresult.LASTID,text_area_name);
                       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                       $("#text_area_name").val("");
                       $("#text_descrption").val("");
                       $("#text_weightage").val("");
                   }
               }
    }
});
}
function submitForm(){
    var check  = 0;
    $(".removebg").css('background-color','none');
    $('.totalkra').each(function(key,value) {
        console.log($(this));
        var value = $(this).val();
        var kraid = $(this).attr("data-kraid");
        console.log(value);
        if(value != 100){
            check = 1;
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Total Not match All 100','');
            $("#deletekra_"+kraid).css('background-color','#ff7272');
            $("#deletekra_"+kraid).css("background-color", "yellow");
            $('html, body').animate({
                scrollTop: $("#deletekra_"+kraid).offset().top
            }, 500);
            return false;
        }
    });
    if(check == 0){
        $("#formsubmitid").submit();
    }
}
function weightagecheck(kpi_id){
    var sum=0;
    $('.weightage_'+kpi_id).each(function(key,value) {
        var value = $(this).val();
        sum = parseFloat(sum)+ parseFloat(value);
    });
    console.log(sum);
    $('#dis_Total_'+kpi_id).val(sum);

}
function addkraHTML(id,area_name){
    var htmlRows = '';
    htmlRows += '<div class="card newpra_'+id+' removebg" id="deletekra_'+id+'" ><div class="card-body"><div class="card-header"><h5><span id="text_kra_name_'+id+'" class="float-left">'+area_name+'</span>  <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil cursor-pointer float-left mt-1 m-l-10" onclick="editKRA('+id+')", tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></h5><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-danger float-right mt-1 m-l-10 cursor-pointer" onclick="removekra('+id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>';

    htmlRows +='<div class="card-body"><div class="row">';
    htmlRows +='<table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer" id="invoiceItem_'+id+'">';
    htmlRows +='<tr><th scope="col" style="width: 2%;"></th><th scope="col" class="text-center" style="width: 25%;">Indicator Name</th><th scope="col" class="text-center" style="width: 25%;">Model </th><th scope="col" class="text-center" style="width: 20%;">UoM </th><th scope="col" class="text-center" style="width: 20%;">Weightage </th></tr>';
    htmlRows += addrows(id,'Y');
    htmlRows +='</table>';
    htmlRows +='<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" ><tr><th scope="col" class="amount" style="width: 80%;"><span>Total</span></th><td style="width: 20%;"><span><input aria-label="Dis Total" type="text" class="form-control amount" readonly value="0" id="dis_Total_'+id+'" ></span></td></tr>';
    htmlRows +='</table></div></div></div></div>';
    $('#newhpirow').append(htmlRows);
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $('html, body').animate({
        speed: 100,
        scrollTop: $(".newpra_"+id).offset().top

    }, 1000);
}
function removerows(){
    var kratable = '<?php echo $kratable; ?>';
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: 'Do you want to Delete KPI?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            $(".itemRow:checked").each(function() {
            var kpi_id = $(this).data("parentid");
            $(this).closest('tr').remove();
            weightagecheck(kpi_id);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
            });
            $('#checkAll').prop('checked', false);
        }
    });
}
function removekra(id){
    var kratable = '<?php echo $kratable; ?>';
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: 'Do you want to Delete KRA?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            $("#deletekra_"+id).remove();
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=softdeletedrecord&id='+id+_2'&table="+kratable,
                success: function(response){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                }
            });
        }
    });
}
function getOldKRA(id){
    $("#getoldkrabtn").attr("disabled", true);
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=insertappraisalkra&appraisal_id='+id,
        success: function(response) {
            console.log(response);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            location.reload();
        }
    });
}
</script>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header">
        <h5>KRA And KPI </h5>
        <div class="card-header-right">
            <?php if(count((array)$kralist)=="0"){ ?>
                <button type="button" class="btn btn-primary" id="getoldkrabtn" onclick="getOldKRA('<?php echo $performance_appraisal_setup_id; ?>')">Create KRA
                </button>
                <?PHP } ?>
                <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>  Back
                    </a>
                <?php } ?>
            </div>
        </div>
        <div class="card-body">
            <?php if(count((array)$kralist)=="0"){ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No KPI </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
            <?php }else{  ?>
                <div class="card">
                    <div class="card-body row">
                        <div class="row d-none" >
                            <div class="col-lg-4">
                                <div class="form-group mb-2"  >
                                    <span class="form-label">Appraisal Name</span>
                                    <input type="hidden" class="form-control" name="appraisal_id" id="kpiandkra_appraisal_id"  value="<?php echo $performance_appraisal_setup_id ?>">
                                    <input aria-label="Input field" type="text"  class="form-control"  value="<?php echo $performance_setup['name']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-2"  >
                                    <span class="form-label">Aassessment Year:</span>
                                    <input aria-label="Assessment Year" type="text" class="form-control"  name="assessment_year" id="kpiandkra_assessment_year" value="<?php echo $assessment_year ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 form-group">
                            <span class="form-label">KRA Name <span class="text-danger">*</span></span>
                            <input aria-label="Text Area Name" type="text"  class="form-control text-capitalize"  id="text_area_name"  />
                        </div>
                        <div class="col-lg-3 form-group">
                            <span class="form-label">Description <span class="text-danger">*</span></span>
                            <textarea aria-label="Text Descrption" class="form-control"   id="text_descrption"></textarea>
                        </div>
                        <div class="col-lg-2 form-group">
                            <span class="form-label">Weightage<span class="text-danger">*</span></span>
                            <input aria-label="Text Weightage" type="text"  class="form-control text-capitalize"  id="text_weightage"  />
                        </div>
                        <div class="col-lg-2 form-group w-full">
                            <span class="form-label">Type<span class="text-danger">*</span></span>
                            <select aria-label="Textkra Type" class="form-control  form-select js-example-basic-single" name="textkra_type" id="textkra_type" ><option value="1">Professional</option><option value="2">Personal</option></select>
                        </div>
                        <div class="col-lg-2 form-group">
                            <button class="btn btn-primary btn-shadow-hover font-weight-bold mt-4" onclick="addrowskra('insert')" ><i class="fa fa-plus"></i> Add KRA</button></div>
                        </div>
                    </div>
                    <form method="post" id="formsubmitid">
                        <?php foreach($kralist as $kralist){ ?>
                            <div class="card removebg" id="deletekra_<?php echo $kralist['id']; ?>">
                                <div class="card-body">
                                    <div class="card-header">
                                        <h5><span id="text_kra_name_<?php echo $kralist['id']; ?>" class="float-left"><?php echo $kralist['area_name']; ?>  </span>  <i class="ti-pencil cursor-pointer float-left mt-1 m-l-10" onclick="editKRA('<?php echo $kralist['id']; ?>')", tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></h5>

                                        <i class="fa fa-trash text-danger float-right mt-1 m-l-10 cursor-pointer" onclick="removekra(<?php echo $kralist['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer" id="invoiceItem_<?php echo $kralist['id']; ?>">
                                                <tr>
                                                    <th scope="col" style="width: 2%;"></th>
                                                    <th scope="col" class="text-center" style="width: 40%;">Indicator Name <span class="text-danger">*</span></th>
                                                    <th scope="col" class="text-center" style="width: 20%;">Model </th>
                                                    <th scope="col" class="text-center" style="width: 20%;">UoM </th>
                                                    <th scope="col" class="text-center" style="width: 10%;">Weightage </th>

                                                </tr>
                                                <?php $i=1;  $total = 0;
                                                if(count((array)$kpilist[$kralist['id']]) == '0'){
                                                    $kpilist[$kralist['id']][] = array();
                                                }
                                                foreach ($kpilist[$kralist['id']] as $res){ ?>
                                                    <tr >
                                                        <td>
                                                            <div class="checkbox-fade fade-in-primary">
                                                                <label class="form-label">
                                                                    <input aria-label="Checkbox" class="form-control itemRow" type="checkbox" data-parentid='<?php echo $kralist['id']; ?>'>   <span class="cr">
                                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                                    </span> </label></div>
                                                                </td>
                                                                <td>
                                                                    <input aria-label="Input field" type="text" name="name[<?php echo $kralist['id']; ?>][]"  class="form-control" value="<?php echo $res['indicator_name']; ?>"  autocomplete="off">
                                                                </td>
                                                                <td class="w-full">
                                                                    <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="model[<?php echo $kralist['id']; ?>][]" >
                                                                        <option value="0">Select Any One</option>
                                                                        <?php foreach ($modellist as  $model) { ?>
                                                                            <option <?php if($res['model'] == $model['genc_detail_code']){ ?> selected <?php
} ?>  value="<?php echo $model['genc_detail_code']; ?>"><?php echo $model['genc_detail_name']; ?></option>
                                                                        <?php } ?>

                                                                    </select>
                                                                </td>
                                                                <td class="w-full">
                                                                    <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="uom[<?php echo $kralist['id']; ?>][]" >
                                                                        <option value="0">Select Any One</option>
                                                                        <?php foreach ($upmllist as  $uom) { ?>
                                                                            <option <?php if($res['uom'] == $uom['genc_detail_code']){ ?> selected <?php
} ?> value="<?php echo $uom['genc_detail_code']; ?>"><?php echo $uom['genc_detail_name']; ?></option>
                                                                        <?php } ?>

                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input aria-label="Input field" type="text" name="weightage[<?php echo $kralist['id']; ?>][]"  onblur="weightagecheck(<?php echo $kralist['id']; ?>)"  class="form-control amount text-right weightage_<?php echo $kralist['id']; ?>" value="<?php echo $res['weightage']; ?>"  autocomplete="off">
                                                                    <input type="hidden" name="kpi_id[<?php echo $kralist['id']; ?>][]"  value="<?php echo $res['id']; ?>">
                                                                    <input type="hidden" name="kpi_tasks_ids[<?php echo $kralist['id']; ?>][]" id="kpi_tasks_ids_<?php echo $kralist['id'].$i; ?>" value="<?php echo $res['kpi_tasks_ids']; ?>">
                                                                </td>
                                                            </tr>
                                                            <?php $i++; $total = $total+$res['weightage'];
} ?>
                                                        </table>
                                                        <div>
                                                        </div>
                                                        <table class="" >
                                                            <tr>
                                                                <th scope="col" style="width: 60%;"><button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold  m-l-5"  onclick="addrows(<?php echo $kralist['id']; ?>)"><i class="la la-check"></i>Add KPI</button>
                                                                    <button class="btn btn-danger delete" id="removeRows" onclick="removerows()"  type="button"> Delete Delete</button></th>
                                                                    <th scope="col" class="amount text-center" style="width: 20%;"><span>Total</span></th>
                                                                    <td style="width: 8%;"><span><input aria-label="Input field"  type="text" class="form-control amount totalkra text-right" readonly value="<?php echo $total; ?>" data-kraid="<?php echo $kralist['id']; ?>" id="dis_Total_<?php echo $kralist['id']; ?>"  >
                                                                    </span></td>


                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div id="newhpirow"></div>
                                        <div class="card-footer">
                                            <div class="col-lg-4">
                                                <input type="hidden" id="table_name" name="table_name" value=<?php echo $kpitasktable; ?> />
                                                <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                                                <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                                                <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">
                                                <?php  $disable = "disabled";     if($total == 100) { $disable = "";
} ?>
                                                <input type="hidden" name="submit_value"  value="insertkpiandkra">
                                                <input type="hidden" name="parent_tab" value="kra">
                                                <input type="hidden" name="child_tab" value="setkra">
                                                <button type="button" onclick="submitForm()" class="btn btn-primary mr-2"   ><i class="la la-check"></i>Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>

