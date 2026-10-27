<script type="text/javascript">
    function addandremove(type,valu){
        console.log("step0:Type="+type+"&value="+valu);
        var cvall = $("#xxx").val();
        var contt =[];
        if(cvall!=""){
            console.log("step0.1:Check");
            contt = cvall.split(",");
        }

        if(type=="ADD"){
            contt.push(valu);
            console.log("step0.2:ADD contt="+contt);
        }
        if(type=="REMOVE"){
            var conttArray = contt.filter(function(e) { return e !== valu })
            contt = conttArray;
            console.log("step0.2:Remove contt="+contt);
        }
        console.log("step0.3:contt="+contt);
        var dis = contt.join(',');
        $('#xxx').val(dis);
    }
    function calucationstocat(){
       var cvall = $("#xxx").val();
       var contt = cvall.split(",");
       var count = contt.length;
         console.log("XXX="+count);
        var month =0;  var year =0;
        console.log("step1-- Count:"+count+"count="+contt);
        for(var k=0 ; k<count;k++ ){
            var i= contt[k];
            var cat_type = $("#cat_type_"+i).val();
            var cat_code= $("#cat_code_"+i).val();
            console.log("Step 2 :  code:"+cat_code+"type"+cat_type);
            if(cat_type != undefined){

                if( cat_type =='P'){
                    var cat_cat = $("#cat_calculation_category_"+i).val();
                    var cat_num = $("#cat_num_"+i).val();
                    var basic = $("#"+cat_cat).val();
                   if(!basic){ basic = 0; }
                   var month_amount = (parseFloat(cat_num)/100)* parseFloat(basic);
                   month_amount = parseFloat(month_amount).toFixed(2);
                   console.log("Step 3:  basic:"+basic+"cat cal code"+cat_cat+"cat val:"+cat_num+"cat nw val:"+month_amount);

                   $('#m_amount_'+i).val(parseFloat(month_amount).toFixed());
                   $('#'+cat_code).val(month_amount);
                   $('#y_amount_'+i).val(parseFloat(month_amount*12).toFixed());

               }else if(cat_type== 'F' &&  cat_type != ''){
                   console.log("step2.1:"+i);
                   var mid=  $("#id_"+i).val();
                   console.log("step3:"+mid);
                   var obj = $('#'+mid+"_detail").val();
                   console.log(obj);
                   var data = JSON.parse(obj);
                   console.log(data);
                   var formula = data.formula_details;
                   console.log("-------------------"+data.name+"---------------------------------");
                   console.log(formula);
                        var obj = JSON.parse(formula);
                        var details = obj.details
                        console.log(details);
                        var fche = ""; var calq = "0"; var calw = "0"; var cale = "0";
                        var calr = "";  var cals = ""; var laop=""; var lastlop = "";
                        for (let i = 0; i < obj.count; i++) {
                            console.log('Ravi'+i);
                            console.log(details[i]);
                            fche = details[i];
                            console.log(fche.lcode);
                            if(isNaN(fche.lcode)){
                                calw = $("#"+fche.lcode).val();
                                console.log("c1:"+calw);
                                if(!calw){ calw = 0; }
                           console.log("c11:"+calw);
                       }else{
                        calw = fche.lcode;
                        console.log("b1:"+calw);
                    }
                    console.log(fche.rcode);
                    if(isNaN(fche.rcode)){
                        cale = $("#"+fche.rcode).val();
                        console.log("c2:"+cale);
                        if(!cale){ cale = 0; }
                           console.log("c21:"+cale);
                       }else{
                        cale = fche.rcode;
                        console.log("b2:"+cale);
                    }
                        calr = fche.loperand;
                        laop = fche.roperand;

                        cals =  calucalationsum(calw,cale,calr);
                        console.log("check1##"+calw+"check2##"+cale+"check3##"+calr+'check4##'+laop);
                        console.log("check"+i+"--"+cals);
                        if(i>0){
                            calq = calucalationsum(calq,cals,lastlop);
                        }else{
                            calq = cals;
                        }
                        lastlop = laop;
                    }
                    console.log("check All--"+calq);
                    month_amount = calq;
                    $('#m_amount_'+i).val(month_amount.toFixed());
                    $('#'+cat_code).val(month_amount);
                    $('#y_amount_'+i).val((month_amount*12).toFixed());
                }else if(cat_type == 'L' &&  cat_type != ''){
                    var mid=  $("#id_"+i).val();
                    var obj = $('#'+mid+"_detail").val();
                    console.log(obj);
                    var data = JSON.parse(obj);
                    cat = data.calculation_category;
                    catlink = data.calculation_link;
                    catlink_value = $("#"+cat).val();
                    month_amount = satutrycalucation(catlink_value);
                    $('#m_amount_'+i).val((month_amount*1).toFixed());
                    $('#'+cat_code).val(month_amount);
                    $('#y_amount_'+i).val((month_amount*12).toFixed());
                }
            }
        }
    }


    function monthlytoyearlycat(val,id){
     var cat_cat = $("#cat_cat_"+id).val();
     console.log("cat_cat:"+cat_cat);
     var cat_type = $("#cat_type_"+id).val();
     console.log("cat_type:"+cat_type);
     var cat_code =  $("#cat_code_"+id).val();
     console.log("cat_code:"+cat_code);
     $('#cat_num_'+id).val(val);

     if(cat_type != ''){
        if(cat_cat !="" &&  cat_type == 'P'){
            var basic = $("#"+cat_cat).val();
            if(!basic){ basic = 0; }

            month_amount = (parseFloat(val)/100)* parseFloat(basic);
            month_amount = parseFloat(month_amount).toFixed(2);

            $('#m_amount_'+id).val(month_amount);
            $('#'+cat_code).val(month_amount);
            $('#y_amount_'+id).val(month_amount*12);
        }else{
            var month_amount = val;

            console.log(month_amount);
            console.log(month_amount);
            $('#m_amount_'+id).val(month_amount);
            $('#'+cat_code).val(month_amount);
            $('#y_amount_'+id).val((month_amount*12));
        }
    }
    calucationstocat();
}


    function dynamicinformationopup(option){
       $('#popup1').html(decodeURI(option));
       $("#kt_modal_KTDatatable_remote").modal("show");
   }
   function calucalationsum(item1,item2,option){
    var res ="0";
    if(option=='+'){ res = parseFloat(item1)+parseFloat(item2); }
    if(option=='-'){ res = parseFloat(item1)-parseFloat(item2); }
    if(option=='*'){ res = parseFloat(item1)*parseFloat(item2); }
    if(option=='/'){ res = parseFloat(item1)/parseFloat(item2); }
    return  res;
}
function addsaldata(val){
    var check = $('#'+val+"check").val();
    if(check == 0){
        $('#'+val+"check").val(1);
        var data = $('#'+val+"_detail").val();
            var obj = JSON.parse(data);
            addrows(obj,val);
            addandremove('ADD',val);
            calucationstocat();
        }else{
            $('#'+val+"check").val(0);
            $('#'+val).remove();
            addandremove('REMOVE',val);
            calucationstocat();
        }
   }
   function satutrycalucation(catlink_value){
    console.log("cat="+cat+":catlink="+catlink+":catlink_value="+catlink_value);
    var month_amount=0;
    if(catlink=='PF'){
        var basic = $("#BASIC").val();
        if(!basic){ basic = 0; }
        var da = $("#DA").val();
        if(!da){ da = 0; }
        var basicda = parseFloat(basic)+parseFloat(da);
        if(basicda<=15000){
            var sa = $("#OA").val();
            if(!sa){ sa = 0; }
            var basicsp = parseFloat(basic)+parseFloat(sa);
            if(basicsp<=15000){
                month_amount = (basicsp*12)/100;
            }else{
                month_amount = (15000*12)/100;
            }
        }else{
           month_amount = (basicda*12)/100;
       }
   }
   if(catlink=='EMPLOYERPF'){
    var basic = $("#BASIC").val();
    if(!basic){ basic = 0; }
    var da = $("#DA").val();
    if(!da){ da = 0; }
    var basicda = parseFloat(basic)+parseFloat(da);
    if(basicda<=15000){
        var sa = $("#OA").val();
        if(!sa){ sa = 0; }
        var basicsp = parseFloat(basic)+parseFloat(sa);
        if(basicsp<=15000){
            month_amount = (basicsp*13.61)/100;
        }else{
            month_amount = (15000*13.61)/100;
        }
    }else{
       month_amount = (basicda*13.61)/100;
   }
}
if(catlink=='ESI'){
    var catlink_evv = $("#catlink_"+catlink).val();
    console.log("step1="+catlink_evv);
    var catlink_esvv = JSON.parse(catlink_evv);
    var catlink_vv = catlink_esvv.employee_contribution;
    console.log("step1="+catlink_evv+":step2="+catlink_esvv+":stp3="+catlink_vv);
    if( parseFloat(catlink_esvv.check_value) > parseFloat(catlink_value) ){
        console.log("1step1="+catlink_esvv.check_value+":step2="+catlink_value+":stp3="+catlink_vv);
        month_amount = (catlink_value*catlink_vv)/100;
    }else{
        console.log("2step1="+catlink_esvv.check_value+":step2="+catlink_value+":stp3="+catlink_vv);
        month_amount =0;
    }

}
if(catlink=='EMPLOYERESI'){
    var catlink_evv = $("#catlink_"+catlink).val();
    console.log("step1="+catlink_evv);
    var catlink_esvv = JSON.parse(catlink_evv);
    var catlink_vv = catlink_esvv.employer_contribution;
    console.log("step1="+catlink_evv+":step2="+catlink_esvv+":stp3="+catlink_vv);
    if( parseFloat(catlink_esvv.check_value) > parseFloat(catlink_value) ){
        console.log("1step1="+catlink_esvv.check_value+":step2="+catlink_value+":stp3="+catlink_vv);
        month_amount = (catlink_value*catlink_vv)/100;
    }else{
        console.log("2step1="+catlink_esvv.check_value+":step2="+catlink_value+":stp3="+catlink_vv);
        month_amount =0;
    }

}
if(catlink=='PTX'){
    var catlink_evv = $("#catlink_"+catlink).val();
    console.log("Ravi==step1="+catlink_evv);
                var catlink_esvv = JSON.parse(catlink_evv);
                for(var i=0; i<4; i++){

                    var checks = catlink_esvv[i];
                    console.log(checks);
                    console.log(checks['start_range']);
                    if(checks['start_range']<catlink_value && checks['end_range']>catlink_value){
                        month_amount = checks['tax_amount'];
                        console.log(month_amount);
                        break;
                    }
                }
            }
            return month_amount;
        }
        function addrows(data,id){

    var count = id;
    var month_amount = 0;
    var reead = '';
        console.log(data);
        if(data.calculation_type !="" ){
         if(data.calculation_type == 'P' &&  data.calculation_category != ''){
            var codedetail1 = data.calculation_category.toUpperCase();
            var codedetail = "% "+codedetail1.replace("_","  ");
            var basic = $("#"+data.calculation_category).val();
           if(!basic){ basic = 0; }
           month_amount = (parseFloat(data.calculation_number)/100)* parseFloat(basic);
           console.log("check percentage:"+data.calculation_number+"check valu:"+basic+"month_amount:"+month_amount);
       }else if(data.calculation_type == 'F' &&  data.calculation_category != ''){
        var codedetail = "Formula";
        var formula = data.formula_details;
        reead = 'readonly';
        console.log(formula);
            var obj = JSON.parse(formula);
            var details = obj.details
            console.log(details);
            var fche = ""; var calq = "0"; var calw = "0"; var cale = "0";
            var calr = "";  var cals = ""; var laop=""; var lastlop = "";
            var month_amount = 0;
            for (let i = 0; i < obj.count; i++) {
                console.log('Ravi'+i);
                console.log(details[i]);
                fche = details[i];
                console.log(fche.lcode);
                if(isNaN(fche.lcode)){
                    calw = $("#"+fche.lcode).val();
                    if(!calw){ calw = 0; }
                }else{
                    calw = fche.lcode;
                }
                if(isNaN(fche.rcode)){
                    cale = $("#"+fche.rcode).val() ;
                    if(!cale){ cale = 0; }
                }else{
                    cale = fche.rcode;
                }
                calr = fche.loperand;
                laop = fche.roperand;

                cals =  calucalationsum(calw,cale,calr);
                console.log("check1##"+calw+"check2##"+cale+"check3##"+calr+'check4##'+laop);
                console.log("check"+i+"--"+cals);
                if(i>0){
                    calq = calucalationsum(calq,cals,lastlop);
                }else{
                    calq = cals;
                }
                lastlop = laop;
            }
            console.log("check All--"+calq);
            month_amount = calq;
        }else if(data.calculation_type == 'L' &&  data.calculation_category != ''){
            var codedetail = "Statutory";
            reead = 'readonly';
            cat = data.calculation_category;
            catlink = data.calculation_link;
            catlink_value = $("#"+cat).val();
            month_amount = satutrycalucation(catlink_value);


        }else{
            console.log(data.calculation_number);
            month_amount = data.calculation_number;
            console.log(month_amount);
            var codedetail = "Fixed";
            console.log(codedetail);
        }

        var gettotalamt  = parseFloat(month_amount*12).toFixed();
        month_amount = parseFloat(month_amount).toFixed();
        }


       var htmlRows = '';
       var note = "'"+data.note+"'";
       htmlRows += '<tr id="'+id+'">';
       htmlRows += '<td><div class="form-group col-lg-11" ><input aria-label="Name[]" type="text" class="form-control itemRow f-w-800" readonly value="'+data.name+' ('+data.code+')" name="name[]"><input type="hidden" class="form-control " readonly value="'+data.id+'" id="id_'+count+'"  name="id[]"></div></td>';
       htmlRows += '<td><div class="form-group col-lg-11">  <div class="input-group input-group-button"> <input aria-label="Calucation[]" type="text" onblur="monthlytoyearlycat(this.value,'+count+')" '+reead+' class="form-control text-right width1" value="'+data.calculation_number+'" id="change_val_'+count+'" name="calucation[]" /> <button class="btn btn-primary w-100PX" type="button">'+codedetail+'</button> </td>';
       htmlRows += '<td><div class="form-group col-lg-11"><input aria-label="0.00" type="text" class="form-control text-right"  placeholder="0.00" name="monthly_amount[]" readonly id="m_amount_'+count+'"  value ="'+month_amount+'"  ></div> <input type="hidden" name="cat_type[]" value="'+data.calculation_type+'" id="cat_type_'+count+'">  <input type="hidden" name="cat_calculation_category[]" value="'+data.calculation_category+'" id="cat_calculation_category_'+count+'"> <input type="hidden" name="cat_code[]" value="'+data.code+'" id="cat_code_'+count+'">     <input type="hidden" name="cat_cat[]" value="'+data.category_id+'" id="cat_cat_'+count+'"> <input type="hidden" name="cat_num[]" value="'+data.calculation_number+'" id="cat_num_'+count+'"></td>';
       htmlRows += '<td><div class="form-group col-lg-11"><div class="input-group"> <input type="hidden" name="'+data.code+'" value="'+month_amount+'" id="'+data.code+'"><input aria-label="0.00" type="text" readonly id="y_amount_'+count+'" name="yearly_amount[]"  value ="'+gettotalamt+'" class="form-control text-right width1" placeholder="0.00"><button type="button" class="mt-6" onclick="dynamicinformationopup('+encodeURI(note)+')">&nbsp;<i class="fa fa-info-circle f-14"></i> </button></div> </div></td>';
       htmlRows += '</tr>';
       $("#invoiceItem"+data.category_id).css("display", "block");
       $('#invoiceItem'+data.category_id).append(htmlRows);
   }


</script>
                         <div class="card-body pt-0 pb-3">
                            <div class="table-responsive">
                                <table class="table table-head-custom table-head-bg table-borderless table-vertical-center"  >
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th scope="col" style="width: 30%" class="pl-7">
                                                <span class="f-14">Salary Component</span>
                                            </th>
                                            <th scope="col" style="width: 20%"><span class="f-14">Calculation Type</span></th>
                                            <th scope="col" style="width: 20%;text-align: center;"><span class="f-14">Amount Monthly</span></th>
                                            <th scope="col" style="width: 20%"><span class="f-14">Amount Annually</span></th>

                                        </tr>
                                    </thead>
                                </table>
                                <?php $j=1; foreach ($data as  $dat) {
                                    if(in_array($dat['id'], $checkmainids)){
                                        $maincss ="";
                                    }else{
                                    }
                                    ?>
                                    <table <?php echo $maincss; ?> id="invoiceItem<?php echo  $dat['id']; ?>">
                                        <tbody>
                                            <tr  id="<?php echo "cat_".$dat['id']; ?>">
                                                <th scope="col" style="width: 30%" class="pl-0 py-4" >
                                                    <b><?php echo $dat['name'];?></b>
                                                </th>

                                            </tr>
                                        </tbody>

                                        <?php
                                        foreach ($dat['data'] as  $value){
                                            $read_only = "readonly";
                                            if($value['calculation_type'] == 'P'){
                                                $codedetail = "% ".str_replace("_", "", strtoupper($value['calculation_category']));
                                                $read_only = "";
                                            }elseif($value['calculation_type'] == 'F'){
                                             $codedetail = "Formula";
                                         }else{
                                             $codedetail = "Fixed";
                                             $read_only = "";
                                         }
                                         $note = urlencode($value['note']);
                                         if(in_array($value['id'], $checkdata)){
                                             ?>
                                             <tr  id="<?php echo $value['id']; ?>" >
                                                <td style="width: 30%">
                                                    <div class="form-group col-lg-11" >
                                                        <input aria-label="Input field" type="text" class="form-control itemRow f-w-800" readonly value="<?php echo $value['name']."(".$value['code'].")"; ?>" name="name[]">
                                                        <input type="hidden" class="form-control" readonly id="id_<?php echo $value['id'];?>" value="<?php echo $value['id']; ?>" name="id[]">
                                                    </div>
                                                </td>
                                                <td style="width: 30%" >
                                                    <div class="form-group col-lg-11">
                                                        <?php
                                                            $component_number=$placevalue[$value['id']]['component_number'];
                                                         if($placevalue[$value['id']]['component_number']==""){ $component_number=$value['calculation_number'];
                                                        } ?>
                                                       <div class="input-group input-group-button">
                                                        <input aria-label="Input field" type="text" <?php echo $read_only; ?> onblur="monthlytoyearlycat(this.value,<?php echo$value['id'] ?>)" class="form-control text-right width1" value="<?php echo $component_number; ?>" name="calucation[]" aria-describedby="basic-addon2"/>
                                                        <button class="btn btn-primary w-100PX" type="button"><?php echo $codedetail; ?> </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 20%" >
                                                <?php
                                                 $cat_type = $placevalue[$value['id']]['component_type'];
                                                     $cat_type = $value['calculation_type'];
                                                 ///}
                                                  $component_number = $placevalue[$value['id']]['component_number'];
                                                  if($component_number ==""){
                                                     $component_number = $value['calculation_number'];
                                                 }
                                                ?>
                                                <div class="form-group col-lg-11">
                                                    <input aria-label="0.00" type="text" class="form-control text-right" readonly placeholder="0.00" name="monthly_amount[]" value ="<?php echo $placevalue[$value['id']]['monthly_amount'];?>"  id="m_amount_<?php echo $value['id']; ?>">
                                                    <input type="hidden" name="cat_type[]" value="<?php echo $cat_type; ?>" id="cat_type_<?php echo $value['id']; ?>">
                                                    <input type="hidden" name="cat_calculation_category[]" value="<?php echo $value['calculation_category'] ?>" id="cat_calculation_category_<?php echo $value['id']; ?>">
                                                    <input type="hidden" name="cat_code[]" value="<?php echo $value['code'] ?>" id="cat_code_<?php echo $value['id']; ?>">
                                                    <input type="hidden" name="cat_cat[]" value="<?php echo $value['category_id']; ?>" id="cat_cat_<?php echo $value['id'] ?>">
                                                    <input type="hidden" name="cat_num[]" value="<?php echo $component_number; ?>" id="cat_num_<?php echo $value['id']; ?>">
                                                </div>
                                            </td>
                                            <td style="width: 20%" >
                                                    <?php $ddcode =  $placevalue[$value['id']]['monthly_amount'];
                                                        if($ddcode==""){
                                                            $ddcode = 0;
                                                        }
                                                    ?>
                                                <div class="form-group col-lg-11">
                                                    <div class="input-group">
                                                        <input type="hidden" name="<?php echo $value['code']; ?>" value="<?php echo $ddcode; ?>" id="<?php echo $value['code']; ?>" >

                                                        <input aria-label="Input field" type="text" readonly id="y_amount_<?php echo $value['id'] ?>" name="yearly_amount[]"  value ="<?php echo   $placevalue[$value['id']]['yearly_amount']; ?>" class="form-control text-right width1"placeholder="0.00">

                                                        <button type="button" class="mt-6" onclick="dynamicinformationopup(<?php echo "'".$note."'"; ?>)">&nbsp;<i class="fa fa-info-circle f-14"></i></button>

                                                    </div> </div>

                                                </td>
                                            </tr>

                                        <?php } ?>
                                        <?php } ?>
                                    </table>
                                    <?php $j++;
} ?>
                                    <input type="hidden" name="sp" id="SPS" value="0">
                                    <input type="hidden" name="" id="xxx" value="<?php echo implode(",", (array)$checkdata);?>">
                                    <input type="hidden" name="" id="catlink_PF" value="<?php echo $pfval; ?>">
                                    <input type="hidden" name="" id="catlink_ESI" value='<?php echo json_encode($esival); ?>'>
                                    <input type="hidden" name="" id="catlink_PTX" value='<?php echo $ptsalabs; ?>'>
                                    <input type="hidden" name="" id="catlink_EMPLOYERESI" value='<?php echo json_encode($esival); ?>'>
                                    <input type="hidden" name="" id="catlink_EMPLOYERPF" value="<?php echo $emppfval; ?>">

</div>
</div>
</div>
