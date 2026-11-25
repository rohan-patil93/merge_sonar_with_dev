 <?php $operator = array('+','-','*','/');
         $get_table = $table;
         $url = API."?mode=filednames&table=".$get_table;
        $response = file_get_contents($url, false, $context);
        $response = json_decode($response,1);
 ?>
<script type="text/javascript">
    function submitfrom(){
        var  str = '';
         var arr = [];
         var table = $('#tables').val();
         var    type = $('#type_value').val();
         var views_type = $('#view_value').val();
         //type = type +'_'+views_type;
         var width = $('#width').val();
         var field_name = $('#field_names').val();
         field_name = field_name+"_after";
         alert(views_type);
         if(type=='spacer'){
             str = 'table='+table+'&field='+field_name+'&type='+type+'#'+views_type+'&field_name='+width;
         }
         if(type=='text'){
             width = $('#width_text').val();
             str = 'table='+table+'&field='+field_name+'&type='+type+'#'+views_type+'&field_name='+width+'&value='+$('#lable').val()+'&dis_custom_sql='+$('#content').val();
         }
         if(type=='static'){
             width = $('#width_static').val();
             str = 'table='+table+'&field='+field_name+'&type='+type+'#'+views_type+'&field_name='+width+'&value='+$('#static_lable').val()+'&content='+$('#total').val();
         }
         if(type=='dynamic'){

             width = $('#width_dynamic').val();
             var count = $(".itemRows").length;
             var con = "";
            alert(count);
            for(var i=1; i< count+1; i++){
                con += 'select1_value_'+i+'='+$('#ds_1_'+i).val()+'#text1_value_'+i+'='+$('#f1_'+i).val()+'#select2_value_'+i+'='+$('#ds_2_'+i).val()+'#text2_value_'+i+'='+$('#s1_'+i).val()+'#oparends1_value_'+i+'='+$('#op1_1_'+i).val()+'#oparends2_value_'+i+'='+$('#op1_2_'+i).val();
            }
             str = 'table='+table+'&field='+field_name+'&type='+type+'#'+views_type+'&field_name='+width+'&value='+$('#lable_dynamic').val()+'&discontent='+con;

         }
        alert(str);
         $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dis_submit&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: <?= json_encode(__("Record has been saved successfully")) ?>,
                            text: <?= json_encode(__("Do you want to close ?")) ?>,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>" + <?= json_encode(__("Yes, close!")) ?>,
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> " + <?= json_encode(__("No, continue")) ?>,
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_add_custom_field').modal('hide');
                            }
                        });

            }
        });
    }

    function difftype(type){
        // alert("hai");
         alert(type);
        if(type == 'text'){
            $("#spacer_data").css("display", "none");
            $("#text_data").css("display", "block");
            $("#static_data").css("display", "none");
            $("#dynamic_data").css("display", "none");
            $('#type_value').val('text');
        }
        if(type == 'spacer'){
            $("#spacer_data").css("display", "block");
            $("#text_data").css("display", "none");
            $("#static_data").css("display", "none");
            $("#dynamic_data").css("display", "none");
            $('#type_value').val('spacer');
        }
        if(type == 'static'){
            $("#spacer_data").css("display", "none");
            $("#text_data").css("display", "none");
            $("#static_data").css("display", "block");
            $("#dynamic_data").css("display", "none");
            $('#type_value').val('static');
        }
        if(type == 'dynamic'){
            $("#spacer_data").css("display", "none");
            $("#text_data").css("display", "none");
            $("#static_data").css("display", "none");
            $("#dynamic_data").css("display", "block");
            $('#type_value').val('dynamic');
        }
    }
    function viewtype(type) {
        $('#view_value').val(type);
    }

    function addRowssd() {
        alert(<?php echo json_encode(__('hai'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);

        var count = $(".itemRows").length;
        alert(count);
        count++;
        var htmlRows = '';
        htmlRows += '<tr id="qur_div_'+count+'" >';
        htmlRows += '<td><div class="col-lg-6" style="float: left;"><select class="form-control" id="ds_1_'+count+'" aria-label="Select"><option value="0"> <?= __("Select") ?> </option><?php foreach($response as $res){?><option value="<?php echo $res; ?>"><?php echo $res; ?></option><?php } ?></select>    </div><div class="col-lg-6" style="float: left;"><input  class="form-control itemRows" type="text" id="f1_'+count+'" name="" aria-label="Input"></td>';
        htmlRows += '<td><select class="form-control" id="op1_1_'+count+'" aria-label="Select"><?php foreach($operator as $ope){?><option value="<?php echo $ope; ?>"> <?php echo $ope; ?></option><?php } ?> </select></td>';
        htmlRows += '<td><div class="col-lg-6" style="float: left;"><select class="form-control" id="ds_2_'+count+'" aria-label="Select"><option value="0"> <?= __("Select") ?> </option><?php foreach($response as $res){?><option value="<?php echo $res; ?>"><?php echo $res; ?></option><?php } ?></select>    </div><div class="col-lg-6" style="float: left;"><input type="text" class="form-control" id="s1_'+count+'" name="" aria-label="Input"></td>';
        //htmlRows += '<td><input type="text" class="form-control" id="v_'+count+'" name="" aria-label="Input"></td>';
        htmlRows += '<td><select class="form-control" id="op1_2_'+count+'" aria-label="Select"><?php foreach($operator as $ope){?><option value="<?php echo $ope; ?>"> <?php echo $ope; ?></option><?php } ?> </select></td>';
        htmlRows += '<td><button class="btn btn-warning" id="'+count+'" type="button" onclick="deletestatickRows(this.id)"><?= __("Delete") ?></button></td>';
        htmlRows += '</tr>';
        $('#invoiceItem1').append(htmlRows);
    }
    function checkval(){
        var count = $(".itemRows").length;
        alert(count);
        for(var i=1; i< count+1; i++){
            var cal= 0 ;
            var F1 = $('#f1_'+i).val();
            var s1 = $('#s1_'+i).val();
            var F2 = $('#ds1_1_'+i).val();
            var s2 = $('#ds1_2_'+i).val();
            alert(F1+"***"+F2)
            if((F1=='' && F2 == '0') || (s1=='' && s2 == '0') ){
                alert(<?php echo json_encode(__('check values'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
                return false;
            }
        }
    }

    function addRowss() {
        alert(<?php echo json_encode(__('hai'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);

        var count = $(".itemRow").length;
        alert(count);
        count++;
        var htmlRows = '';
        htmlRows += '<tr id="qur_div_'+count+'" >';
        htmlRows += '<td><input  class="form-control itemRow" type="text" id="f_'+count+'" name="" aria-label="Input"></td>';
        htmlRows += '<td><select class="form-control" id="op_1_'+count+'" aria-label="Select"><?php foreach($operator as $ope){?><option value="<?php echo $ope; ?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ope; ?></option><?php } ?> </select></td>';
        htmlRows += '<td><input type="text" class="form-control" id="s_'+count+'" name="" aria-label="Input"></td>';
        htmlRows += '<td><input type="text" class="form-control" id="v_'+count+'" name="" aria-label="Input"></td>';
        htmlRows += '<td><select class="form-control" id="op_2_'+count+'" aria-label="Select"><?php foreach($operator as $ope){?><option value="<?php echo $ope; ?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ope; ?></option><?php } ?> </select></td>';
        htmlRows += '<td><button class="btn btn-warning" id="'+count+'" type="button" onclick="deletestatickRows(this.id)"><?= __("Delete") ?></button></td>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    }
    function checkcal(){

        var count = $(".itemRow").length;
        alert(count);

        for(var i=1; i< count+1; i++){
            var cal= 0 ;
            var F = $('#f_'+i).val();
            var op = $('#op_1_'+i).val();
            var s = $('#s_'+i).val();
            var v1 = 0;
            /*if(i>0){
            vl = $('#v_'+i).val();
            }*/
            //cal =  Number(F)  + op+ Number(s);
            if (op == '+'){    cal =  Number(F)  + Number(s); }
            if (op == '-'){    cal =  Number(F)  - Number(s); }
            if (op == '*'){    cal =  Number(F)  * Number(s); }
            if (op == '/'){    cal =  Number(F)  / Number(s); }
             $('#v_'+i).val(parseFloat(cal));
             caltotal();

        }
    }
    function caltotal(){
        var cal= $('#v_1').val();
        var count = $(".itemRow").length;
        for(var i=2; i< count+1; i++){

            var F = cal;
            var o = i-1;
            var op = $('#op_2_'+o).val();
            var s = $('#v_'+i).val();

            //cal =  Number(F)  + op+ Number(s);
            if (op == '+'){    cal =  Number(F)  + Number(s); }
            if (op == '-'){    cal =  Number(F)  - Number(s); }
            if (op == '*'){    cal =  Number(F)  * Number(s); }
            if (op == '/'){    cal =  Number(F)  / Number(s); }
        }
        $('#total').val(parseFloat(cal));

    }

    function deletestatickRows(val){
        $('#qur_div_'+val).remove();
    }
</script>

<div id="kt_modal_add_custom_field" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="min-height: 590px;">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= __("Add Custom Field") ?>  <?php echo $get_table; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="kt-form kt-form--label-right" action="" method="post">

            <div class="modal-body">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="row align-items-center">
                        <div class="col-xl-12 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <input type="hidden"  id='type_value' value="spacer"  name="type_value">
                                    <input type="hidden"  id='field_names' value="name"  name="field_names">
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">

                                    <input type="radio"  id='spacer' value="custom_sql"  name="type" checked="checked" onclick="difftype(this.id)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= __("spacer") ?> <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" id="text"  value="static"  name="type" onclick="difftype(this.id)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __("Test") ?> <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" id="static"  value="static"  name="type" onchange="difftype(this.id)" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __("Static Formula") ?> <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" id="dynamic"  value="dynamic"  name="type" onchange="difftype(this.id)" aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __("Dynamic Formula") ?> <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-xl-12 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">

                                    <input type="radio"  id='insert' value="insert" onclick="viewtype(this.id)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"  name="view" checked="checked" aria-label="<?= htmlspecialchars(__('View'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= __("Insert") ?> <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" id="list"  value="list" onclick="viewtype(this.id)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"  name="view" aria-label="<?= htmlspecialchars(__('View'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __("List") ?> <span></span>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" id="all"  value="all" onclick="viewtype(this.id)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"  name="view" aria-label="<?= htmlspecialchars(__('View'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __("All") ?> <span></span>
                                    </label>
                                </div>
                                <input type="hidden" id="view_value" value="insert">
                            </div>
                        </div>
                    </div>
                    <!---- start spacer----->
                    <div class="row align-items-center" id="spacer_data" style="margin-top: 10px;" >
                        <div class="form-group col-lg-3">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lenth") ?></span>
                            <select class="form-control" id="width" name= "width" aria-label="<?= htmlspecialchars(__('Width'), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php for($i=1; $i<13;$i++){
                                    $view_type = "col-lg-".$i; ?>
                                    <option  value = "<?php echo $i; ?>" ><?php echo $view_type; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <!---- end spacer----->
                    <!---- start test----->
                    <div class="row align-items-center" id="text_data" style="margin-top: 10px;display: none;" >
                        <div class="form-group col-lg-3">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lenth") ?></span>
                            <select class="form-control" id="width_text" name= "width" aria-label="<?= htmlspecialchars(__('Width'), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php for($i=1; $i<13;$i++){
                                    $view_type = "col-lg-".$i; ?>
                                    <option  value = "<?php echo $i; ?>" > <?php echo $view_type; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lable") ?></span>
                            <input class="form-control"  type="text" id="lable" name="lable"  value="" aria-label="<?= htmlspecialchars(__('Lable'), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Content") ?></span>
                            <textarea class="form-control" id="content" rows="3" aria-label="<?= htmlspecialchars(__('Textarea'), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
                        </div>
                    </div>
                    <!---- end test----->
                    <!---- start formula----->
                    <div class="row align-items-center" id="static_data" style="margin-top: 10px;display: none;" >
                        <div class="form-group col-lg-3" >
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lenth") ?></span>
                            <select class="form-control" id="width_static" name= "insert_view_width[]" aria-label="<?= htmlspecialchars(__('Insert View Width'), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php for($i=1; $i<13;$i++){
                                    $view_type = "col-lg-".$i; ?>
                                    <option  value = "<?php echo $i; ?>"><?php echo $view_type; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3" >
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lable") ?></span>
                            <input class="form-control"  type="text" id="static_lable" name="lable"  value="" aria-label="<?= htmlspecialchars(__('Lable'), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="col-lg-7">
                            <table class="table table-striped- table-bordered table-hover table-checkable  no-footer" style="color: #646c9a;" id="invoiceItem">
                                    <tr>
                                        <th scope="col" class="" style="width: 15%;"><?= __("Frist Field") ?> </th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("operater") ?></th>
                                        <th scope="col" class="thheader" style="width: 15%;"><?= __("Second Field") ?> </th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("Value") ?></th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("operater") ?></th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("Delete") ?></th>

                                    </tr>
                                    <tr>
                                        <td>
                                            <input  class="form-control itemRow" type="text" id="f_1" name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                        </td>
                                        <td>
                                            <select class="form-control" id="op_1_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach($operator as $ope){?>
                                                <option value="<?php echo $ope; ?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ope; ?></option>
                                            <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="s_1" name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="v_1" name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                        </td>
                                        <td>
                                            <select class="form-control" id="op_2_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach($operator as $ope){?>
                                                <option value="<?php echo $ope; ?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ope; ?></option>
                                            <?php } ?>
                                            </select>
                                        </td>
                                        <td><button class="btn btn-warning" id="del_row_static_1" type="button" onclick="$(this).closest('tr').remove()"><?= __("Delete") ?></button></td>
                                    </tr>

                            </table>
                            <div class="row align-items-center mt-2">
                                <div class="col-auto"> <?= __("Total") ?></div>
                                <div class="col-auto"> <input type="text" class="form-control" id="total"  name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>"></div>
                            </div>
                        </div>

                            <div class="row align-items-center" style="margin-top: 10px;margin-left: 2px;">
                                <button class="btn btn-success" id="addRows_static" type="button" onclick="addRowss()"><?= __("+ Add More") ?></button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-brand" onclick="checkcal()"><?= __("Check") ?> </button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" onclick="createselectquery()"><?= __("Excute Query") ?></button>
                            </div>
                    </div>
                    <!---- end formula----->
                    <!---- start Dynamic formula----->
                    <div class="row align-items-center" id="dynamic_data" style="margin-top: 10px;display: none;" >
                        <div class="form-group col-lg-3">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lenth") ?></span>
                            <select class="form-control" id="width_dynamic" name= "width" aria-label="<?= htmlspecialchars(__('Width'), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php for($i=1; $i<13;$i++){
                                    $view_type = "col-lg-".$i; ?>
                                    <option  value = "<?php echo $i; ?>"><?php echo $view_type; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __("Lable") ?></span>
                            <input class="form-control"  type="text" id="lable_dynamic" name="lable"  value="" aria-label="<?= htmlspecialchars(__('Lable'), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="col-lg-12">
                            <table class="table table-striped- table-bordered table-hover table-checkable  no-footer" style="color: #646c9a;" id="invoiceItem1">
                                    <tr>
                                        <th scope="col" class="" style="width: 40%;"><?= __("Frist Table/Static Field") ?> </th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("operater") ?></th>
                                        <th scope="col" class="thheader" style="width: 40%;"><?= __("Second Table/Static Field") ?> </th>

                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("operater") ?></th>
                                        <th scope="col" class="thheader" style="width: 5%;"><?= __("Delete") ?></th>
                                    </tr>
                                    <tr>
                                        <td>
                                        <div class="col-lg-6" style="float: left;">
                                            <select class="form-control" id="ds_1_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option value="0"> <?= __("Select") ?> </option>
                                            <?php foreach($response as $res){?>
                                                <option value="<?php echo $res; ?>"><?php echo $res; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6" style="float: left;">
                                            <input  class="form-control itemRows" type="text" id="f1_1" name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        </td>
                                        <td>
                                            <select class="form-control" id="op1_1_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach($operator as $ope){?>
                                                <option value="<?php echo $ope; ?>"> <?php echo $ope; ?></option>
                                            <?php } ?>
                                            </select>
                                        </td>
                                        <td> <div class="col-lg-6" style="float: left;">
                                            <select class="form-control" id="ds_2_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <option value="0"> <?= __("Select") ?> </option>
                                            <?php foreach($response as $res){?>
                                                <option value="<?php echo $res; ?>"><?php echo $res; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-6" style="float: left;">
                                            <input type="text" class="form-control" id="s1_1" name="" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        </td>

                                        <td>
                                            <select class="form-control" id="op1_2_1" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach($operator as $ope){?>
                                                <option value="<?php echo $ope; ?>"> <?php echo $ope; ?></option>
                                            <?php } ?>
                                            </select>
                                        </td>
                                        <td><button class="btn btn-warning" id="del_row_dynamic_1" type="button" onclick="$(this).closest('tr').remove()"><?= __("Delete") ?></button></td>
                                    </tr>

                            </table>

                        </div>




                            <div class="row align-items-center" style="margin-top: 10px;margin-left: 2px;">
                                <button class="btn btn-success" id="addRows_dynamic" type="button" onclick="addRowssd()"><?= __("+ Add More") ?></button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-brand" onclick="checkval()"><?= __("Check") ?> </button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" onclick="createselectquery()"><?= __("Excute Query") ?></button>
                            </div>
                        </div>
                    </div>
                    <!---- end Dynamic formula----->
                </div>

                <!--end: Search Form -->

            <div class="modal-footer">
                <input type="hidden" name="tables" id='tables' value="<?php echo $get_table; ?>">

                <input type="hidden" name="page" value="dis_submit">
                <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal"><?= __("Close") ?></button>
                <button type="button" class="btn btn-default btn-bold btn-upper btn-font-sm" onclick="submitfrom()" ><?= __("Submit") ?></button>
            </div>
        </div>
    </form>
</div>
</div>

</div>
</div>
