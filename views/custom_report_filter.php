<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$tdurl1 = API."?mode=showtables";
$showtablesdata1 = callAPI($tdurl1);
$showtables1 = json_decode($showtablesdata1,1);
$report_id = $_POST['report_id'];
$field= $_POST['field'];

$dataurl = API."?mode=viewdattributetype&table=".$table."&field=".$field;
$view = callAPI($dataurl);
$view = json_decode($view,1);
$attribute_type = $view['attribute_type'] !="" ? $view['attribute_type'] : "custom_sql" ;
if($attribute_type == 'static'){
    $table_str = urldecode($view['content']);
    $static_str = json_decode($table_str,1);
}
if($attribute_type == 'custom_sql'){
    $view_details = json_decode(urldecode($view['content_details']),1);
    $table_str = urldecode($view['content']);
    $table_str = trim($table_str);
    $table_name_data = explode('FROM', $table_str);
    $table_name_data = explode(' ',trim($table_name_data['1']));
    $table_name_field = $table_name_data['0'];
    $table_data = str_replace('ORDER', 'WHERE', $table_str);
    $table_data = explode('WHERE', $table_data);
    $trs = explode(' ', trim($table_data[1]));
    if(count((array)$trs) >1){
        for ($i=0; $i < count((array)$trs) ; $i=$i+3) {
            $arry['drop_field'] = $trs[$i];
            $arry['drop_operends'] = $trs[$i+2];
            $exp = explode("'", $trs[$i+1]);
            $arry['drop_condition'] = $exp[0];
            $arry['drop_value'] = $exp[1];
            $tr[] = $arry;
        }
    }

    $duplicate='';
    foreach ($view['list'] as $list  ) {
        $duplicate.="<option value ='".$list."'>".$list."</option>";
    }
}
$comparator_array = array('=','!=','>','>=','<','<=');
?>

<script type="text/javascript">
    function selectcheck(val){
        if (val != 0) {
            $('#select').val('0');
            $("#where_check").css("display", "none");
            $("#oredr_check").css("display", "none");
        } else {
            $('#select').val('1');
           $("#where_check").css("display", "block");
            $("#oredr_check").css("display", "block");
        }
    }
    function ordercheck(val){
        if (val != 0) {
            $('#ordercheck').val('0');
            $("#order_check_order").css("display", "none");
            $("#order_check_soft").css("display", "none");
        } else {
            $('#ordercheck').val('1');
           $("#order_check_order").css("display", "block");
            $("#order_check_soft").css("display", "block");
        }
    }
    function deleteAllRows(){
        var count = $(".fitemRow").length;

        for(var i=1; i < count+1; i++){
        $("#qur_div_"+i).css("display", "none");
        $("#delete_value_"+i).val('1');
        }
    }
    function Getfields(val){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dis_att_type&table='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                    $("#field_id").html("<select aria-label="Field Name" class='form-control form-select js-example-basic-single' id ='field_name' name='field_name'>"+data+"</select>");
                    $("#label_id").html("<select aria-label="Value" class='form-control form-select js-example-basic-single' id='value' name='value'>"+data+"</select>");
                    $("#order_by").html("<select aria-label="Order By" class='form-control form-select js-example-basic-single' id='order_by' name='order_by'>"+data+"</select>");
                    $("#drop").val(data);
                    $("#dis_custom_sql").val('');

                    deleteAllRows();
                    $(".js-example-basic-single").select2();
                    $(".js-example-disabled-results").select2();
            },
            error:function (){}
        });
    }
    function radiofrom(){
     var ele = document.getElementsByName('type');
           for(i = 0; i < ele.length; i++) {
               if(ele[i].checked)
            var type =ele[i].value;
        }
        return type;
    }
    function submitrom(){
          var  str = '';
         var arr = [];
         var type = $("#type_value").val();
         arr['table'] = $('#table').val();
         arr['field'] = $('#field').val();
         arr['field_name'] = $('#field_name').val();
         arr['type'] = type;
         if(type=='static'){
             var count = $(".sfitemRow").length;
             str = 'table='+$('#table').val()+'&field='+$('#field').val()+'&type='+type+'&count='+count;
             for (i = 1; i < count+1 ; i++) {
                 str += '&static_value_'+i+'='+$('#static_value_'+i).val()+'&static_lable_'+i+'='+$('#static_lable_'+i).val();
             }
         }else{
             arr['field_name'] = $('#field_name').val();
             arr['value'] = $('#value').val();
             arr['table_name'] = $('#table_name').val();
             arr['dis_custom_sql'] = $('#dis_custom_sql').val();
             arr['order_by'] = $('#order_by').val();
             arr['sort'] = $('#sort').val();
             var count = $(".fitemRow").length;
             arr['count'] = count;
             str = 'table='+$('#table_name').val()+'&report_id='+$('#report_id').val()+'&field='+$('#field').val()+'&type='+$('#type').val()+'&field_name='+$('#field_name').val()+'&value='+$('#value').val()+'&table_name='+$('#table_name').val()+'&dis_custom_sql='+$('#dis_custom_sql').val()+'&count='+count;
             for (i = 1; i < count+1 ; i++) {
                 str += '&whrfield_'+i+'='+$('#whrfield_'+i).val()+'&comparator_'+i+'='+$('#comparator_'+i).val()+'&whrvalue_'+i+'='+$('#whrvalue_'+i).val()+'&operand_'+i+'='+$('#operand_'+i).val();
                arr['whrfield_'+i] = $('#whrfield_'+i).val();
                arr['comparator_'+i] = $('#comparator_'+i).val();
                arr['whrvalue_'+i] = $('#whrvalue_'+i).val();
                arr['operand_'+i] = $('#operand_'+i).val();
              }
          }
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=save_report_filter&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: "Record has been saved successfully ",
                            text: "Do you want to close ? ",
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_dynamic_popup').modal("hide");
                            }
                        });

            }
        });
    }

    function createselectquery(){
        var table = $('#table_name').val();
        var field_name = $('#field_name').val();
        var value = $('#value').val();
        var whr = '';
        var order = '';
        if(table!='' && field_name !='-1' && value !='-1' ){
        var val= 'SELECT  '+field_name+', '+value+'  FROM  '+ table;
        }else{
            var val = "Please Choose Table name and Field names .. ";
        }
        var count = $(".fitemRow").length;
        if(count > 0){
            whr += ' WHERE ';
            for (i = 1; i < count+1 ; i++) {
            var delete_d = $('#delete_value_'+i).val();
                if(delete_d == 0){
                    var whrfield = $('#whrfield_'+i).val();
                    var comparator = $('#comparator_'+i).val();
                    var whrvalue = $('#whrvalue_'+i).val();
                    var operand = $('#operand_'+i).val();
                      whr +=  whrfield+" "+comparator+ "'"+whrvalue+"' "+operand+" ";
                  }
              }
              if (whr.length>7){
              whr = whr.slice(0, -4);
              }else{
                  whr = '';
              }
          }
          order += " ORDER BY "+ $('#order_by').val() +" "+$('#sort').val();
          val = val+whr+order;
        $('#dis_custom_sql').val(val);
    }

    function deletestatickRows(val){
        $("#qur_div_"+val).css("display", "none");
        $("#delete_value_"+val).val('1');
    }
    function deleteRows(val){
        $("#static_div_"+val).css("display", "none");
        $("#static_delete_value_"+val).val('1');
    }
    function addstatickRows123(){
        var count = $(".sfitemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
        htmlRows += '<div class="col-lg-3">';
        htmlRows += '<input aria-label="Type" type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="type">';
        htmlRows += '</div>';
        htmlRows += '<div class="col-lg-3">';
        htmlRows += '<input aria-label="Type" type="text"class="form-control"  id="static_lable_'+count+'"  value=""  name="type"> ';
        htmlRows += '</div>';

        htmlRows += '<div class="col-lg-3">';
        htmlRows += ' <input type="hidden" value="0" id="static_delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deleteRows(this.id)">Delete</button>';
        htmlRows += '</div>';
        htmlRows += '</div>';
        $('#append_statick').append(htmlRows);
    }

function addRows123() {
        var selectdata = $('#drop').val();
        var count = $(".fitemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr id="qur_div__2"+count+'" >';
        htmlRows += '<td><select aria-label="Whrfield[]" class="form-control fitemRow js-example-basic-single" id="whrfield__2"+count+'" name= "whrfield[]" >'+selectdata+'</select></td>';
        htmlRows += '<td><select aria-label="Comparator[]" class="form-control form-select js-example-basic-single" id="comparator__2"+count+'" name= "comparator[]"  ><?php foreach ($comparator_array as $dd) {  ?><option  value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php
} ?> </select></td>';
        htmlRows += '<td><input aria-label="Whrvalue[]" type="text" id="whrvalue__2"+count+'" name="whrvalue[]"  class="form-control amount" ></td>';
        htmlRows += '<td><select aria-label="Operand[]" class="form-control form-select js-example-basic-single "  id="operand__2"+count+'"  name= "operand[]" id=""  >    <option value="AND"> AND </option><option  value = "OR"> OR </option>    </select></td>';
        htmlRows += '<td><input type="hidden" id="delete_value__2"+count+'" value="0"><button class="btn btn-warning" id="'+count+'" type="button" onclick="deletestatickRows(this.id)">Delete</button></td>';
        htmlRows += '</tr>';
        console.log(htmlRows);
        $('#invoiceItemffff').append(htmlRows);
             $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    }

    function ajaxselectdata(){
        var val =  $('#table_name').val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dis_att_type&table='+val,
            type: "POST",
            success:function(resp){
                console.log(resp);
                data = resp;
            },
            error:function (){}
        });
        return data;
    }
    function difftype(type){
        if(type == 'static'){
            $("#radio_switch").css("display", "none");
            $("#radio_switch2").css("display", "block");
            $("#type_value").val(type);
        }else{
            $("#radio_switch").css("display", "block");
            $("#radio_switch2").css("display", "none");
            $("#type_value").val(type);
        }
    }
</script>

<!-- remote datatable modal -->

    <div class="modal-header">
                        <h5 class="modal-title">
                            Field Datasource
                            <small>&nbsp;&nbsp;  Field: </b>&nbsp; <?php echo $field; ?> </small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="row align-items-center">
                        <div class="col-xl-12 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="hidden"  id='type_value' value="<?php echo $attribute_type; ?>"  name="type_value">
                                    <input aria-label="Type" type="radio"  id='type' value="custom_sql"  name="type" <?php if($attribute_type != 'static' ){ ?> checked="checked" <?php
} ?> onchange="difftype(this.value)"> From Table <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input aria-label="Type" type="radio" id="type_2"  value="static"  name="type"  <?php if($attribute_type == 'static' ){ ?> checked="checked" <?php
} ?> onchange="difftype(this.value)"> Static values<span></span>
                                    </label>
                                </div>

                            </div>
                        <div id="radio_switch"  <?php if($attribute_type == 'static') { ?> style='display: none;' <?php
} ?> >
                            <div class="row align-items-center" style="margin-top: 10px;">
                                <div class="col-lg-3">
                                    <span>Table</span>
                                     <select aria-label="Table Name" class="form-control form-select js-example-basic-single" id="table_name" name= "table_name" onchange="Getfields(this.value)">
                                         <option value="">Select Table Name</option>
                                           <?php foreach ($showtables1 as $key => $data) {  ?>
                                               <option   value = <?php echo $data ?> <?php if($table_name_field == $data){ echo "selected";
} ?>   > <?php echo ucwords(str_replace('_',' ',$data)); ?> </option>
                                       <?php    } ?>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <span>value Filed</span>
                                    <div id='field_id'>
                                     <select aria-label="Field Name" class="form-control form-select js-example-basic-single" id='field_name_2' name="field_name">
                                           <option value="-1">Select Filed</option>
                                           <?php foreach ($view['list'] as  $list) { ?>
                                               <option value="<?php echo $list; ?>" <?php if($view['value_field'] == $list){ echo "selected";
} ?> ><?php echo $list; ?></option>
                                           <?php } ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <span>Lable Filed</span>
                                    <div id='label_id'>
                                         <select aria-label="Value" class="form-control form-select js-example-basic-single" id="value_2" name="value">
                                               <option value="-1">Select Filed</option>
                                               <?php foreach ($view['list'] as  $list) { ?>
                                               <option value="<?php echo $list; ?>" <?php if($view['display_field'] == $list){ echo "selected";
} ?> ><?php echo $list; ?></option>
                                           <?php } ?>

                                        </select>
                                    </div>
                                </div>
<div class="col-lg-2" id='order_check_order'>
                                    <span>Order By Field</span>
                                    <div >
                                        <input type="hidden" name="drop" id="drop" value="<?php echo $duplicate; ?>">
                                         <select aria-label="Order By" class="form-control form-select js-example-basic-single" id="order_by_2" name="order_by">
                                               <option value="-1">Select Filed</option>
                                               <?php foreach ($view['list'] as  $list) { ?>
                                               <option value="<?php echo $list; ?>" <?php if($view_details['order_by'] == $list){ echo "selected";
} ?> ><?php echo $list; ?></option>
                                           <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2" id='order_check_soft'>
                                    <span>Sort</span>
                                    <div>
                                         <select aria-label="Sort" class="form-control form-select js-example-basic-single" id="sort" name="sort">
                                               <option  value="ASC">ASC</option>
                                               <option  <?php if($view_details['sort'] == 'DESC'){ echo "selected";
} ?>  value="DESC">DESC</option>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="drop" id="drop_2" value="<?php echo $duplicate; ?>">

                            </div>
                            <div class="row align-items-center" style="margin-top: 10px;padding: 3px;">
                                <div class="col-lg-7">
                                    <table class="table table-striped- table-bordered table-hover table-checkable  no-footer" style="color: #646c9a;" id="invoiceItemffff">
                                            <tr>
                                                <th scope="col" class="" style="width: 15%;">Field</th>
                                                <th scope="col" class="thheader" style="width: 5%;">Comparator</th>
                                                <th scope="col" class="thheader" style="width: 15%;">Value</th>
                                                <th scope="col" class="thheader" style="width: 5%;">oprandes</th>
                                                <th scope="col" class="thheader" style="width: 5%;">Delete</th>
                                            </tr>
                                            <?php if(count((array)$tr)>0){
                                                $count = 1;
                                                foreach ($tr as $td) { //print_r($td); ?>


                                                <tr id="qur_div_<?php echo $count; ?>" >
                                                    <td>
                                                        <select aria-label="Select option" class="form-control fitemRow form-select js-example-basic-single" id="whrfield_<?php echo $count; ?>" name= "whrfield[]" >
                                                            <?php foreach ($view['list'] as  $list) { ?>
                                                                   <option value="<?php echo $list; ?>" <?php if($td['drop_field'] == $list){ echo "selected";
} ?> ><?php echo $list; ?></option>
                                                               <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single" id="comparator_<?php echo $count; ?>" name= "comparator[]"  >
                                                        <?php foreach ($comparator_array as $dd) {  ?>
                                                            <option  <?php if($td['drop_condition'] == $dd){ echo "selected";
} ?>  value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php
} ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input aria-label="Input field" type="text" value="<?php echo $td['drop_value']?>" id="whrvalue_<?php echo $count; ?>" name="whrvalue[]"  class="form-control amount"  >
                                                    </td>
                                                    <td>
                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single"  id="operand_<?php echo $count; ?>"  name= "operand[]" id=""  >
                                                            <option value="AND"> AND </option>
                                                            <option <?php if($td['drop_condition'] == $dd){ echo "selected";
} ?>  value = "OR"> OR </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" value="0" id="delete_value_<?php echo $count; ?>" >
                                                        <button class="btn btn-warning" id="<?php echo $count; ?>" type="button" onclick="deletestatickRows(this.id)">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php $count++; ?>
                                            <?php } ?>
                                            <?php } ?>
                                    </table>
                                </div>
                                <div class="col-lg-5">
                                        <span>Select Query</span>
                                        <div class="kt-input-icon">
                                            <textarea aria-label="Dis Custom Sql" class="form-control"  type="text" id="dis_custom_sql" name="dis_custom_sql" style="height: 80px;" value=""> <?php echo urldecode($view['content']); ?></textarea>
                                        </div>
                                </div>
                            </div>

                        <div class="row align-items-center" style="margin-top: 10px;margin-left: 2px;">
                                <button class="btn btn-success col-lg-2" id="addRows" type="button" onclick="addRows123()">+ Add More</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-primary col-lg-2" onclick="createselectquery()">Bulid Query</button>&nbsp;&nbsp;

                            </div>


                        </div>
                        <!----------- defalut start ----------->
                        <div id="radio_switch2" <?php if($attribute_type != 'static') { ?> style='display: none;' <?php
} ?> >
                            <div id='append_statick'>
                                <div class="row align-items-center" style="margin-top: 10px;">
                                    <div class="col-lg-3">
                                        <span><b>value</b></span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span><b>Lable</b></span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span><b></b></span>
                                    </div>
                                </div>
                                <?php if(count((array)$static_str)>0){
                                    $i=1;
                                    foreach($static_str as $key => $sstr){ ?>
                                <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                                    <div class="col-lg-3">
                                        <input aria-label="Input field" type="text" class="form-control"  id="static_value_<?php echo $i; ?>"  value="<?php echo $key; ?>"  name="type">
                                    </div>
                                    <div class="col-lg-3">
                                        <input aria-label="Input field" type="text"class="form-control"  id="static_lable_<?php echo $i; ?>"  value="<?php echo $sstr; ?>"  name="type">
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="hidden" value="0" id="static_delete_value_<?php echo $i; ?>" >
                                        <button class="btn btn-warning" id="<?php echo $i; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                                    </div>
                                </div>
                            <?php $i++;
}
} else{ ?>
                                <div class="row align-items-center sfitemRow" id="static_div_1" style="margin-top: 10px;">
                                    <div class="col-lg-3">
                                        <input aria-label="Type" type="text" class="form-control"  id="static_value_1"  value=""  name="type">
                                    </div>
                                    <div class="col-lg-3">
                                        <input aria-label="Type" type="text"class="form-control"  id="static_lable_1"  value=""  name="type">
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="hidden" value="0" id="static_delete_value_1" >
                                        <button class="btn btn-warning" id="1" type="button" onclick="deleteRows(this.id)">Delete</button>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="row align-items-center" style="margin-top: 10px;">
                                <button class="btn btn-success" id="addstatickRows" type="button" onclick="addstatickRows123()">+ Add More</button>&nbsp;&nbsp;
                            </div>
                        </div>
                        <!----------- defalut start ----------->
                        </div>

                    </div>
                </div>


            <div class="modal-footer">
                <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
                <input type="hidden" name="field" id='field' value="<?php echo $field; ?>">
                <input type="hidden" name="report_id" id='report_id' value="<?php echo $report_id; ?>">
                <input type="hidden" name="page" value="dis_submit">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitrom()" >Submit</button>
            </div>

    </form>
</div>
<!-- local datatable modal -->

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
