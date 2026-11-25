<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$tdurl1 = API."?mode=showtables";
$showtablesdata1 = callAPI($tdurl1);
$showtables1 = json_decode($showtablesdata1,1);
$table = $_POST['table'];
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
    //print_r($table_data);
    //pecho "replace:".$table_str;
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
            //alert(i);
            $("#qur_div_"+i).css("display", "none");
            $("#delete_value_"+i).val('1');
        }
    }
    function Getfields(val){
        //alert('hai');
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dis_att_type&table='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#field_id").html("<select class='form-control form-select js-example-basic-single' id ='field_name' name='field_name' aria-label="Select">"+data+"</select>");
                $("#label_id").html("<select class='form-control form-select js-example-basic-single' id='value' name='value' aria-label="Select">"+data+"</select>");
                $("#order_by").html("<select class='form-control form-select js-example-basic-single' id='order_by' name='order_by' aria-label="Select">"+data+"</select>");

                $("#refer_by_field").html("<select class='form-control form-select js-example-basic-single' id='refer_by_field' name='refer_by_field' aria-label="Select">"+data+"</select>");



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
    //    var that = $(this),
      //  data = that.serialize(); //This will capture all form input values, no need to reinvent the wheel

      var  str = '';
      var arr = [];
      var type = $("#type_value").val();
      var company_id = $('#company_id_val').val();
      var refer_action = $("#refer_action").is(":checked") ==true?'0':'1';
      var refer_by_action = $("#refer_by_action").is(":checked") ==true?'0':'1';
      arr['table'] = $('#table').val();
      arr['field'] = $('#field').val();
      arr['field_name'] = $('#field_name').val();
      arr['type'] = type;

      if(type=='static'){
          var count = $(".sfitemRow").length;

          str = 'table='+$('#table').val()+'&field='+$('#field').val()+'&type='+type+'&count='+count+'&company_id='+company_id;
          for (i = 1; i < count+1 ; i++) {
              str += '&static_value_'+i+'='+$('#static_value_'+i).val()+'&static_lable_'+i+'='+$('#static_lable_'+i).val();
          }

      }else if(type=='rating'){
          str = 'table='+$('#table').val()+'&field='+$('#field').val()+'&type='+type+'&count='+count+'&company_id='+company_id+'&field_name='+$('#value_field').val();
      }else{
          arr['field_name'] = $('#field_name').val();
          arr['value'] = $('#value').val();
          arr['table_name'] = $('#table_name').val();
          arr['dis_custom_sql'] = $('#dis_custom_sql').val();
          arr['order_by'] = $('#order_by').val();
          arr['sort'] = $('#sort').val();
          arr['refer_action'] = refer_action;
          arr['refer_field'] = $('#refer_field').val();
          arr['refer_by_action'] = refer_by_action;
          arr['refer_by_field'] = $('#refer_by_field').val();
          var count = $(".fitemRow").length;
          arr['count'] = count;
          str = 'table='+$('#table').val()+'&field='+$('#field').val()+'&type='+type+'&field_name='+$('#field_name').val()+'&value='+$('#value').val()+'&order_by='+$('#order_by').val()+'&sort='+$('#sort').val()+'&table_name='+$('#table_name').val()+'&dis_custom_sql='+$('#dis_custom_sql').val()+'&count='+count+'&refer_action='+refer_action+'&refer_field='+$('#refer_field').val()+'&refer_by_action='+refer_by_action+'&refer_by_field='+$('#refer_by_field').val()+'&company_id='+company_id;
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
          data: 'page=dis_submit&'+str,
          success: function(response) {
                console.log(response);
                //$('.success').html(response);



// Swal.fire({
//   title: 'Record has been saved successfully',
//   text: "Do you want to close ?",
//   icon: 'success',
//   showCancelButton: true,
//  confirmButtonClass: "btn btn-success",
//   cancelButtonClass: "btn btn-warning"
//   confirmButtonText: 'Yes, close!'
// }).then((result) => {
//   if (result.isConfirmed) {
//    $('#kt_modal_KTDatatable_remote').modal('hide');
//   }
// })



// swal({
//     title: <?= json_encode(__("Record has been saved successfully")) ?>,
//     text: "Do you want to close ? ",
//     type: "success",
//     buttonsStyling: false,
//     confirmButtonText: "Yes, close! ",
//     confirmButtonClass: "btn btn-success",
//     showCancelButton: true,
//     cancelButtonText: " No, continue",
//     cancelButtonClass: "btn btn-warning"
// }).then(function(result) {
//     if (result.value) {
//         $('#kt_modal_KTDatatable_remote').modal('hide');
//     }
// });

    swal({
            title: <?= json_encode(__("Record has been saved successfully")) ?>,
            type: "success",
            showCancelButton: false,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        }, function () {
            $('#kt_modal_KTDatatable_remote').modal('hide');
        });




}
});
    }
    function createselectquery(){
        //alert('hai');
        var table = $('#table_name').val();
        var field_name = $('#field_name').val();
        var value = $('#value').val();
        var whr = '';
        var order = '';
        //alert(table+'||'+field_name+'||'+value);
        if(table!='' && field_name !='-1' && value !='-1' ){
            var val= 'SELECT  '+field_name+', '+value+'  FROM  '+ table;
        //var rrr = checkName(val);
    }else{
        var val = "Please Choose Table name and Field names .. ";

    }
    var count = $(".fitemRow").length;
    if(count > 0){
        whr += ' WHERE ';
            //alert(count);
            for (i = 1; i < count+1 ; i++) {

                var delete_d = $('#delete_value_'+i).val();
                //alert(delete_d);
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

          //console.log(whr);
          val = val+whr+order;
          $('#dis_custom_sql').val(val);
      }
      function deletestatickRows(val){
        //alert(val);
        $("#qur_div_"+val).css("display", "none");
        $("#delete_value_"+val).val('1');
    }
    function deleteRows(val){
        alert(val);
        $("#static_div_"+val).css("display", "none");
        $("#static_delete_value_"+val).val('1');
    }
    function addstatickRows123(){
        var count = $(".sfitemRow").length;
        //alert('la-hand-o-right'+count);
        count++;
        var htmlRows = '';
        htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
        htmlRows += '<div class="col-lg-3">';
        htmlRows += '<input type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="type" aria-label="Type">';
        htmlRows += '</div>';
        htmlRows += '<div class="col-lg-3">';
        htmlRows += '<input type="text"class="form-control"  id="static_lable_'+count+'"  value=""  name="type" aria-label="Type"> ';
        htmlRows += '</div>';

        htmlRows += '<div class="col-lg-3">';
        htmlRows += ' <input type="hidden" value="0" id="static_delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deleteRows(this.id)">' + <?= json_encode(__('Delete')) ?> + '</button>';
        htmlRows += '</div>';
        htmlRows += '</div>';
        $('#append_statick').append(htmlRows);
    }
    function addRows123() {
        //var selectdata = ajaxselectdata();
        var selectdata = $('#drop').val();
        //alert(selectdata);
         //alert('hai');

         var count = $(".fitemRow").length;
        //alert(count);
        count++;
        var htmlRows = '';
        htmlRows += '<tr id="qur_div_'+count+'" >';
        htmlRows += '<td><select class="form-control fitemRow js-example-basic-single" id="whrfield_'+count+'" name= "whrfield[]" aria-label="Whrfield">'+selectdata+'</select></td>';
        htmlRows += '<td><select class="form-control form-select js-example-basic-single" id="comparator_'+count+'" name= "comparator[]" aria-label="Comparator"><?php foreach ($comparator_array as $dd) {  ?><option  value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php } ?> </select></td>';
        htmlRows += '<td><input type="text" id="whrvalue_'+count+'" name="whrvalue[]"  class="form-control amount" aria-label="Whrvalue"></td>';
        htmlRows += '<td><select class="form-control form-select js-example-basic-single "  id="operand_'+count+'"  name= "operand[]" id="" aria-label="Operand">    <option value="AND"> <?= htmlspecialchars(__('AND'), ENT_QUOTES, 'UTF-8'); ?> </option><option  value = "OR"> <?= htmlspecialchars(__('OR'), ENT_QUOTES, 'UTF-8'); ?> </option>    </select></td>';
        htmlRows += '<td><input type="hidden" id="delete_value_'+count+'" value="0"><button class="btn btn-warning" id="'+count+'" type="button" onclick="deletestatickRows(this.id)">' + <?= json_encode(__('Delete')) ?> + '</button></td>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    }
    function ajaxselectdata(){
        //alert('ajax');
        var val =  $('#table_name').val();
        //alert(val);
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
        //var type = radiofrom();
        //alert(type);
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

//new code added in Rohan
function swictchrefer_by1(){
    if($('#refer_by_action').prop("checked") == true){
        $(".referbyfieldname").show();
    }else{
        $(".referbyfieldname").hide();
    }
}
function swictchrefer1(){
    console.log("dsdc");
    if($('#refer_action').prop("checked") == true){
        $(".referfieldname").show();
    }else{
        $(".referfieldname").hide();
    }
}


var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-s'));
elem.forEach(function(html) {
    var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
});
//new code added in Rohan
</script>

<style>
    .swal2-popup {
        font: 12px 'Open Sans',Helvetica,Arial,sans-serif !important;
    }
</style>
<!-- remote datatable modal -->
        <div class="modal-header">


            <div class="card-header">
                <h5><?= __("Field Datasource") ?> <small>&nbsp;&nbsp; <b><?= __("Table:") ?> </b>&nbsp; <?php echo $table; ?> <b> &nbsp;&nbsp; <?= __("Field:") ?> </b>&nbsp; <?php echo $field; ?> </small></h5>



            </div>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>


        </div>
        <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <?php if($_POST['type']=='rating'){
                    $attribute_type = 'rating';
                } ?>
                <input type="hidden"  id='type_value' value="<?php echo $attribute_type; ?>"  name="type_value">
                <?php if($_POST['type']=='rating'){ ?>
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="row align-items-center">
                            <div class="col-xl-12 order-2 order-xl-1">
                                <div class="row align-items-center">
                                    <div class="col-lg-4">
                                        <label for="value_field" class="d-block"><?= __("Ratting") ?></label>
                                        <input type="text" name="value_field" class="form-control" id="value_field" value="<?php echo $view['value_field']; ?>" aria-label="<?= htmlspecialchars(__('Value Field'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="row align-items-center">
                            <div class="col-xl-12 order-2 order-xl-1">
                                <div class="row align-items-center">
                                    <div class="col-lg-2">
                                <div class="form-radio">
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input type="radio" id='type_custom_sql' value="custom_sql" class="cursor-pointer" name="type" <?php if($attribute_type != 'static' ){ ?> checked="checked" <?php } ?> onchange="difftype(this.value)" style="display:none" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="helper"></i><?= __("Static values") ?>    </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input type="radio" id="type_static"  value="static"  name="type"  <?php if($attribute_type == 'static' ){ ?> checked="checked" <?php } ?> onchange="difftype(this.value)" style="display:none" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="helper"></i><?= __("Static values") ?>    </label>
                                            </div>
                                        </div>
                                </div>

                            </div>
                            <div id="radio_switch"  <?php if($attribute_type == 'static') { ?> style='display: none;' <?php } ?> >
                                <div class="row align-items-center" style="margin-top: 10px;">
                                    <div class="col-lg-3 wi-full">
                                        <label for="table_name" class="d-block"><?= __("Table") ?></label>
                                        <select class="form-control js-example-basic-single" id="table_name" name= "table_name" onchange="Getfields(this.value)" aria-label="<?= htmlspecialchars(__('Table Name'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <option value=""><?= __("Select Table Name") ?></option>
                                            <?php foreach ($showtables1 as $key => $data) {  ?>
                                                <option   value = <?php echo $data ?> <?php if($table_name_field == $data){ echo "selected";
                                                } ?>   > <?php echo ucwords(str_replace('_',' ',$data)); ?> </option>
                                            <?php    } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 w-full">
                                        <label for="field_name" class="d-block"><?= __("Value Field") ?></label>
                                        <div id='field_id'>
                                            <select class="form-control js-example-basic-single" id='field_name' name="field_name" aria-label="<?= htmlspecialchars(__('Field Name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option value="-1"><?= __("Select Filed") ?></option>
                                                <?php foreach ($view['list'] as  $list) { ?>
                                                    <option value="<?php echo $list; ?>" <?php if($view['value_field'] == $list){ echo "selected";
                                                    } ?> ><?php echo $list; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 w-full">
                                        <label for="value" class="d-block"><?= __("Lable Field") ?></label>
                                        <div id='label_id'>
                                            <select class="form-control js-example-basic-single" id="value" name="value" aria-label="<?= htmlspecialchars(__('Value'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option value="-1"><?= __("Select Filed") ?></option>
                                                <?php foreach ($view['list'] as  $list) { ?>
                                                    <option value="<?php echo $list; ?>" <?php if($view['display_field'] == $list){ echo "selected";
                                                    } ?> ><?php echo $list; ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 w-full" id='order_check_order'>
                                        <label for="order_by" class="d-block"><?= __("Order By Field") ?></label>
                                        <div >
                                            <input type="hidden" name="drop" id="drop" value="<?php echo $duplicate; ?>">
                                            <select class="form-control form-select js-example-basic-single" id="order_by" name="order_by" aria-label="<?= htmlspecialchars(__('Order By'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option value="-1"><?= __("Select Filed") ?></option>
                                                <?php foreach ($view['list'] as  $list) { ?>
                                                    <option value="<?php echo $list; ?>" <?php if($view_details['order_by'] == $list){ echo "selected";
                                                    } ?> ><?php echo $list; ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 w-full" id='order_check_soft'>
                                        <label for="sort" class="d-block"><?= __("Sort") ?></label>
                                        <div>
                                            <select class="form-control js-example-basic-single" id="sort" name="sort" aria-label="<?= htmlspecialchars(__('Sort'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option  value="ASC"><?= __('ASC'); ?></option>
                                                <option  <?php if($view_details['sort'] == 'DESC'){ echo "selected";
                                                } ?>  value="DESC"><?= __('DESC'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mt-4" >
                                    <div class="col-lg-2">
                                        <label for="refer_action" class="d-block"><?= __("Reference for") ?> </label>
                                        <input type="checkbox" class="js-switch-s" name="refer_action" id="refer_action" onchange="swictchrefer1()" value="0" <?php if($view_details['refer_action'] == '0'){ echo "checked";
                                        } ?> aria-label="<?= htmlspecialchars(__('Refer Action'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-lg-3 w-full">
                                    <?php $fieldlists = getListAttributes($_POST['table']); ?>
                                        <div class="referfieldname col-lg-12" style="display: <?php if($view_details['refer_action'] == '1' || $view_details['refer_action'] == ''){ echo 'none';
                                        } ?>">
                                        <select class="form-control js-example-basic-single" id="refer_field" name= "refer_field" aria-label="<?= htmlspecialchars(__('Refer Field'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <option value="-1"><?= __("Select Filed") ?></option>
                                            <?php foreach ($fieldlists as  $fieldlist) { ?>
                                                <option value="<?php echo $fieldlist['Field']; ?>" <?php if($view_details['refer_field'] == $fieldlist['Field']){ echo "selected";
                                                } ?>><?php echo $fieldlist['Field']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="refer_by_action" class="d-block"><?= __("Referenced By") ?></label>
                                        <input type="checkbox" class="js-switch-s" name="refer_by_action" id="refer_by_action" onchange="swictchrefer_by1()" value="0" <?php if($view_details['refer_by_action'] == '0'){ echo "checked";
                                        } ?> aria-label="<?= htmlspecialchars(__('Refer By Action'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-lg-3 w-full">
                                        <div class="referbyfieldname" style="display: <?php if($view_details['refer_by_action'] == '1' || $view_details['refer_by_action'] == ''){ echo 'none';
                                        } ?>">
                                        <select class="form-control js-example-basic-single" id="refer_by_field" name= "refer_by_field" aria-label="<?= htmlspecialchars(__('Refer By Field'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <option value="-1"><?= __("Select Filed") ?></option>
                                            <?php foreach ($view['list'] as  $list) { ?>
                                                <option value="<?php echo $list; ?>"  <?php if($view_details['refer_by_field'] == $list){ echo "selected";
                                                } ?>><?php echo $list; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    </div>
                                </div>










                                <div class="row align-items-center" style="margin-top: 10px;padding: 3px;">
                                    <div class="col-lg-7">
                                        <table class="table table-striped- table-bordered table-hover table-checkable  no-footer" style="color: #646c9a;" id="invoiceItem">
                                            <tr>
                                                <th scope="col" class="" width="15%"><?= __("Field") ?></th>
                                                <th scope="col" class="thheader" width="5%"><?= __("Operator") ?></th>
                                                <th scope="col" class="thheader" width="15%"><?= __("Value") ?></th>
                                                <th scope="col" class="thheader" width="5%"><?= __("Condition") ?></th>
                                                <th scope="col" class="thheader" width="5%"><?= __("Action") ?></th>
                                            </tr>
                                            <?php if(count((array)$tr)>0){
                                                $count = 1;
                                                foreach ($tr as $td) {?>


                                                    <tr id="qur_div_<?php echo $count; ?>" >
                                                        <td>
                                                            <select class="form-control fitemRow form-select js-example-basic-single" id="whrfield_<?php echo $count; ?>" name= "whrfield[]" aria-label="<?= htmlspecialchars(__('Whrfield'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php foreach ($view['list'] as  $list) { ?>
                                                                    <option value="<?php echo $list; ?>" <?php if($td['drop_field'] == $list){ echo "selected";
                                                                    } ?> ><?php echo $list; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-select js-example-basic-single" id="comparator_<?php echo $count; ?>" name= "comparator[]" aria-label="<?= htmlspecialchars(__('Comparator'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php foreach ($comparator_array as $dd) {  ?>
                                                                    <option <?php if ($td['drop_condition'] == $dd) {
                                                                        echo "selected";
                                                                    }
                                                                    ?> value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" value="<?php echo $td['drop_value']?>" id="whrvalue_<?php echo $count; ?>" name="whrvalue[]"  class="form-control amount" aria-label="<?= htmlspecialchars(__('Whrvalue'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            </td>
                                                            <td>
                                                                <select class="form-control form-select js-example-basic-single"  id="operand_<?php echo $count; ?>"  name= "operand[]" id="" aria-label="<?= htmlspecialchars(__('Operand'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <option value="AND"> <?= __('AND'); ?> </option>
                                                                    <option <?php if($td['drop_condition'] == $dd){ echo "selected";
                                                                    } ?>  value = "OR"> <?= __('OR'); ?> </option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" value="0" id="delete_value_<?php echo $count; ?>" >
                                                                <button class="btn btn-warning" id="<?php echo $count; ?>" type="button" onclick="deletestatickRows(this.id)"><?= __("Delete") ?></button>
                                                            </td>
                                                        </tr>
                                                        <?php $count++;
                                                        }
                                                        } ?>
                                                    </table>
                                                </div>
                                                <div class="col-lg-5">
                                                    <label><?= __("Query") ?></label>
                                                    <div class="kt-input-icon">
                                                        <textarea class="form-control"  type="text" id="dis_custom_sql" name="dis_custom_sql" style="height: 80px;" value="" aria-label="<?= htmlspecialchars(__('Dis Custom Sql'), ENT_QUOTES, 'UTF-8'); ?>"> <?php echo urldecode($view['content']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row align-items-center" style="margin-top: 10px;margin-left: 2px;">
                                                <button class="btn btn-success col-lg-2" id="addRows" type="button" onclick="addRows123()"><?= __("+ Add More") ?></button>&nbsp;&nbsp;
                                                <button type="button" class="btn btn-primary col-lg-2" onclick="createselectquery()"><?= __("Bulid Query") ?></button>&nbsp;&nbsp;
                                                <button type="button" class="btn btn-danger col-lg-2" onclick="createselectquery()"><?= __("Excute Query") ?></button>
                                            </div>

                                        </div>
                                        <!----------- defalut start ----------->
                                        <div id="radio_switch2" <?php if($attribute_type != 'static') { ?> style='display: none;' <?php } ?> >
                                            <div id='append_statick'>
                                                <div class="row align-items-center" style="margin-top: 10px;">
                                                    <div class="col-lg-3">
                                                        <label><b><?= __("Field") ?></b></label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label><b><?= __("Value") ?></b></label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label><b></b></label>
                                                    </div>
                                                </div>
                                                <?php if(count((array)$static_str)>0){
                                                    $i=1;
                                                    foreach($static_str as $key => $sstr){ ?>
                                                        <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                                                            <div class="col-lg-3">
                                                                <input type="text" class="form-control"  id="static_value_<?php echo $i; ?>"  value="<?php echo $key; ?>"  name="type" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <input type="text"class="form-control"  id="static_lable_<?php echo $i; ?>"  value="<?php echo $sstr; ?>"  name="type" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <input type="hidden" value="0" id="static_delete_value_<?php echo $i; ?>" >
                                                                <button class="btn btn-warning" id="<?php echo $i; ?>" type="button" onclick="deleteRows(this.id)"><?= __("Delete") ?></button>
                                                            </div>
                                                        </div>
                                                        <?php $i++;
                                                        }
                                                        } else{ ?>
                                                            <div class="row align-items-center sfitemRow" id="static_div_1" style="margin-top: 10px;">
                                                                <div class="col-lg-3">
                                                                    <input type="text" class="form-control"  id="static_value_1"  value=""  name="type" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <input type="text"class="form-control"  id="static_lable_1"  value=""  name="type" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <input type="hidden" value="0" id="static_delete_value_1" >
                                                                    <button class="btn btn-warning" id="1" type="button" onclick="deleteRows(this.id)"><?= __("Delete") ?></button>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row align-items-center" >
                                                        <button class="btn btn-success waves-effect waves-light mt-3 m-l-10 col-lg-2" id="addstatickRows" type="button" onclick="addstatickRows123()"><?= __("+ Add More") ?></button>&nbsp;&nbsp;
                                                    </div>
                                                </div>
                                                <!----------- defalut start ----------->
                                            </div>

                                        </div>
                                    </div>
                                <?php } ?>
                                <!--end: Search Form -->
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="company_id_val" id='company_id_val' value="<?php echo $_SESSION['company_id']; ?>">
                                <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
                                <input type="hidden" name="field" id='field' value="<?php echo $field; ?>">
                                <input type="hidden" name="page" value="dis_submit">
                                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __("Close") ?></button>
                                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitrom()" ><?= __("Submit") ?></button>
                            </div>
                        </form>
                    </div>
                    <!-- local datatable modal -->
                    <!-- Select 2 js -->


