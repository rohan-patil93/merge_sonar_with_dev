<?php
$_SESSION['apply_js']['multiselest'] = 1;
$cand_detaail = $data;
if($cand_detaail['id']!=""){
    unset($_GET['Rid']);
    if($_GET['VIEW'] != 1){
        unset($_GET['SCid']);
    }
}
$view_type = $_GET['VIEW'] !=""?$_GET['VIEW']:0;
if($view_type==0){
    $view_type = $_GET['view'] !=""?$_GET['view']:0;
}
if(isset($_POST['submit'])){
   $option =$_GET['option'];
   $_POST['company_id'] = $_SESSION['company_id'];
   $_POST['created_by'] = $_SESSION['userid'];
   $_POST['created_date'] = nowDate();
   $url = API."?mode=addsalary&method=post";
   $response = file_post_contents($url, $_POST, 'insert');
    $response3 = json_decode($response,1);
   if($response){
        if($_GET['option']=='COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS'){
             header("location:index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=ATT&EMPID=".$EMPID);
        }elseif($_POST['candidate_id']!=''){
            header("location:index.php?option=COM_INSERT_CANDIDATE&Rid=".deocdeId('id:'.$_POST['candidate_id']));
        }else{
            header("location:index.php?option=COM_LIST_SALARY");
        }
   }
}
$pageurl = API."?mode=togetsalarycatdetaillist";
$data =  callAPI($pageurl);
$data = json_decode($data,1);
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','employee_type'=>1);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$employees = json_decode($configesponse,1);
$disabled="";
if($_GET['Rid']!="" || $_GET['SEid']!="" || $_GET['SCid']!=""){
    if($_GET['Rid']!=""){
        $main_ids = encodeId($_GET['Rid']);
        $main_id = explode(":",$main_ids);
        $rid = $main_id['1'];
        $datawr["where"] = array('is_active'=> '0','id'=>$main_id['1']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
        if($salarydata['current_status']==ST_APPROVED){
            $disabled = "disabled";
        }
    }
    if($_GET['SEid']!=""){
        $main_ids = encodeId($_GET['SEid']);
        $disabled = "disabled";
        $main_id = explode(":",$main_ids);
        $datawr["where"] = array('is_active'=> '0','employee_id'=>$main_id['1']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
    }
    if($_GET['SCid']!=""){
        $datawr["where"] = array('is_active'=> '0','candidate_id'=>$_GET['SCid']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
        $salary_temp_id =$salarydata['id'];
        $rid = $salary_temp_id;
        $datawr["where"] = array('is_active'=> '0','id'=>$_GET['SCid']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=candidates&data=".$costdata;
        $salarydata1 = callAPI($url);
        $salarydata22 = json_decode($salarydata1,1);
        $cand_detaail = $salarydata22[0];

        $disabled = "";
        if($cand_detaail['current_status']==ST_APPROVED){
            $disabled = "disabled";
        }
    }


    $stpurl = API."?mode=getsalarytemplatecomponents&salary_template_id=".$salarydata['id'];
    $salarytmpdata1 = callAPI($stpurl);
    $result2 = json_decode($salarytmpdata1,1);
    $salarytmpdata = $result2['salary_template_components'];

    foreach ((array)$salarytmpdata as  $salarytmpda) {
      $checkdata[] = $salarytmpda['component_id'];
      $placevalue[$salarytmpda['component_id']] = $salarytmpda;
      $checkmainids[] =  $salarytmpda['component_category'];
  }
}else{
    foreach ($data as  $dat) {
        foreach ($dat['data'] as  $value){
            if($value['default_display']==0){
                $checkdata[] = $value['id'];
            }
        }
    }
}
foreach ($data as  $dat) {
    foreach ($dat['data'] as  $value){
        if($value['mandatory']==0){
            $mandatroy[] = $value['id'];
        }
    }
}
$eedata1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$eedata1["order"] = "id Desc";
$eedata1["limit"] = "0,1";
$eedata = urlencode(json_encode($eedata1));
$curl = API."?mode=gettabledetailswithconditions&table=emp_provident_funds&data=".$eedata;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$listpf = $esdata;
$pfval = $esdata['employee_contribution'];
$emppfval = $esdata['employer_contribution'];
$curl = API."?mode=gettabledetailswithconditions&table=emp_state_insaurance&data=".$eedata;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$esival['employee_contribution'] = $esdata['employee_contribution'];
$esival['employer_contribution'] = $esdata['employer_contribution'];
$esival['check_value'] = '21000';

$ptdata1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ptdata1["order"] = "id Desc";
$ptdata1["limit"] = "0,1";
$ptdata = urlencode(json_encode($ptdata1));
$pturl = API."?mode=gettabledetailswithconditions&table=professional_tax&data=".$ptdata;
$ptdata = callAPI($pturl);
$ptdata = json_decode($ptdata,1);
$ptsalabs = $ptdata['0']['slabs'];


$ptda["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ptda["order"] = "id ASC";
$ptdat = urlencode(json_encode($ptda));
$pturl = API."?mode=gettabledetailswithconditions&table=salary_variable_pay_slabs&data=".$ptdat;
$salary_varjs= callAPI($pturl);
$salary_variable_pay_slabs = json_decode($salary_varjs,1);
foreach($salary_variable_pay_slabs as $salary_variable_pay){
    $svp['start_range'] = $salary_variable_pay['start_range'];
    $svp['end_range'] = $salary_variable_pay['end_range'];
    $svp['percentage'] = $salary_variable_pay['pay_percentage'];
    $svp_data[] = $svp;
}
$svp_js = json_encode($svp_data);

$sal_emp_id = $salarydata['employee_id']!="" ? $salarydata['employee_id'] : $_GET['newempid'];


?>
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
    function calucationstocat(valcheck=""){
       var cvall = $("#xxx").val();
       var contt = cvall.split(",");
       var count = contt.length;
        console.log("XXX="+count);
        var month =0;  var year =0; var sparerun = 0;
        console.log("step1-- Count:"+count+"count="+contt);
        for(var k=0 ; k<count;k++ ){
            var i= contt[k];
            var cat_type = $("#cat_type_"+i).val();
            var cat_code= $("#cat_code_"+i).val();
            console.log("Step 2 :  code:"+cat_code+"type"+cat_type);

            if(cat_type != undefined){
               if( cat_type =='A'){
                    month_amount = $("#ernam_"+i).val();
                    $('#m_amount_'+i).val((month_amount*1).toFixed(2));
                    $('#'+cat_code).val(month_amount);
                    $('#y_amount_'+i).val((Math.round(month_amount*12)).toFixed());
               }else if( cat_type =='P'){
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
                    if(catlink == 'SPA' && valcheck !=1){
                        console.log('checkkk 123456789');
                       sparerun =1;
                       var sparid =i;
                    }
                    catlink_value = $("#"+cat).val();
                    month_amount = satutrycalucation(catlink_value);
                    $('#m_amount_'+i).val((month_amount*1).toFixed());
                    $('#'+cat_code).val(month_amount);
                    $('#y_amount_'+i).val((month_amount*12).toFixed());
               }
            }
        }
        if(sparerun ==1){

            calucationstocat(sparerun);

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
     console.log("LIVE dataaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
    console.log("cat="+cat+":catlink="+catlink+":catlink_value="+catlink_value);
    var valofemppf = $("#valofemppf").val();
    var checkval = $("#checkval").val();
    var month_amount=0;
    if(catlink=='PF'){
        var basic = $("#BASIC").val();
        if(!basic){ basic = 0; }
        var da = $("#DA").val();
        if(!da){ da = 0; }
        var basicda = parseFloat(basic)+parseFloat(da);
        if(basicda<=checkval){
            console.log("check if"+basicda);
            var sa = $("#OA").val();
            if(!sa){ sa = 0; }
            var basicsp = parseFloat(basicda)+parseFloat(sa);
            if(basicsp<=checkval){
                month_amount = (basicsp*valofemppf)/100;
            }else{
                month_amount = (checkval*valofemppf)/100;
            }
        }else{
            console.log("check else"+basicda+"checkval"+checkval+"valofemppf"+valofemppf);
           month_amount = (checkval*valofemppf)/100;
       }
   }
   if(catlink=='EMPLOYERPF'){
    var valofemprpf = $("#valofemprpf").val();
    var checkval = $("#checkval").val();
    var basic = $("#BASIC").val();
    if(!basic){ basic = 0; }
    var da = $("#DA").val();
    if(!da){ da = 0; }
    var basicda = parseFloat(basic)+parseFloat(da);
    if(basicda<=checkval){
        var sa = $("#OA").val();
        if(!sa){ sa = 0; }
        var basicsp = parseFloat(basic)+parseFloat(sa);
        if(basicsp<=checkval){
            month_amount = (basicsp*valofemprpf)/100;
        }else{
            month_amount = (checkval*valofemprpf)/100;
        }
    }else{
       month_amount = (checkval*valofemprpf)/100;
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
    var catlink_esvv = JSON.stringify(catlink_evv);
    console.log(catlink_esvv);
    for(var i=0; i<4; i++){

        var checks = catlink_esvv[i];
        console.log("Datat"+checks);
        if(checks['start_range']<catlink_value && checks['end_range']>catlink_value){
            month_amount = checks['tax_amount'];
            console.log(month_amount);
            break;
        }else{
            month_amount = 200;
        }
    }
}
if(catlink=='SPA'){
    console.log("_________________________________________");
    var basic = parseFloat($("#BASIC").val());
    var hra =   parseFloat($("#HRA").val());
    var lta = parseFloat($("#LTA").val());
    var pf = parseFloat($("#PF").val());
    var esi = parseFloat($("#EMPLOYERESI").val());
    var gt = parseFloat($("#GRATUITY").val());
    var mp = parseFloat($("#MIP").val());
    var pc = parseFloat($("#PAY_CTC").val());
    console.log("PCCCCCCCCCCCCCCCCCCCCCCCCC"+pc);
    var sb = basic+hra+lta+pf+esi+gt+mp;
    console.log("SBBBBBBBBBBBBBBBBBBBBBB"+sb);
    var month_amount = pc-parseFloat(sb);
    console.log(month_amount);
    if(month_amount<0){
        month_amount = 0;
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


    function changeyearctcwork(val){
        console.log(val);
        var vpctcval =  $("#vp_with_ctc_val").val();
         console.log("vpctcval"+vpctcval);
        if(val=='S'){
            val = $("#ctc_yearly").val();
            if(vpctcval==0){
                $("#vp_with_ctc_val").val(1);
                vpctcval =1;
            }else{
                $("#vp_with_ctc_val").val(0);
                vpctcval =0;
            }
        }

        console.log("val"+val);
        var val_lac = val;

        var month_amount = 0;
        if(vpctcval==0){
            var  catlink_evv =  $("#salary_variable_pay_slabs").val();
            var catlink_esvv = JSON.parse(catlink_evv);
            if(catlink_esvv==null){
                catlink_esvv = [];
            }
            console.log(catlink_esvv);
            var jsonArray = catlink_esvv;
            if (catlink_esvv.length > 0) {
                for(var i=0; i<10; i++){
                    var checks = catlink_esvv[i];
                    console.log('start:'+checks["start_range"]+':end:'+checks["end_range"]+"percentage:"+checks["percentage"]);changeyearctcwork
                    if(parseFloat(checks["start_range"]) <= parseFloat(val_lac) && parseFloat(checks["end_range"]) >=parseFloat(val_lac)){
                        console.log('checkkkkk1');
                         month_amount = checks['percentage'];
                        console.log(month_amount);
                        break;
                    }else{
                        console.log('checkkkkk2');
                        month_amount = 0;
                    }
                }
            }
        }
        console.log("Last:"+month_amount);
        var vard_per = (parseFloat(val)*parseFloat(month_amount))/100;
        console.log("vard_per:"+vard_per);
        var var_amount = parseFloat(val)-parseFloat(vard_per);
        console.log("var_amount:"+var_amount);
        $("#variable_yearly").val(parseFloat(vard_per));
        $("#fixed_ctc_yearly").val(parseFloat(var_amount));
        var divval =parseFloat(var_amount)/12;

        console.log("divval:"+divval);
        //m_amount_16  variable_yearly
        $("#ernam_"+16).val(parseFloat(divval).toFixed(2));
        monthlytoyearlycat(divval,16);
    }
    function changejoiningboun(){
        var is_joining_bouns_val =  $("#is_joining_bouns_val").val();
         console.log("is_joining_bouns_val"+is_joining_bouns_val);
        if(is_joining_bouns_val==1){
            $("#is_joining_bouns_val").val(0);
            $("#empty_text").hide();
            $("#joining_bouns_text").show();
        }else{
            $("#is_joining_bouns_val").val(1);
            $("#joining_bouns_text").hide()
            $("#empty_text").show();
        }
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid " >
        <div class="">
            <div class="" >
                <?php if($view_type != '1'){ ?>
                    <div class="row salary-template"<?php if( isset($_GET['SCRVIEW']) && $_GET['SCRVIEW'] =='Y' ){ ?> style="pointer-events: none; opacity: 0.7;" <?php
} ?> >
                        <div class="col-lg-3 col-md-3 col-12 flex-row-fluid">
                            <div class="row col-lg-12 p-3">
                                <div class="page-header card" style="margin: 0px; color: black;">
                                    <div class="d-inline">
                                        <h5>Build Your Template</h5>
                                        <span class="sub-title">Include active salary components</span>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block mt-2" style="padding: 2px;">
                                    <div class="color-accordion" id="color-accordion">
                                       <?php $j=1; foreach ($data as  $dat) { ?>
                                           <div class="card">
                                            <a class="accordion-msg b-none waves-effect waves-light"> <?php echo $dat['name'];?></a></div>
                                            <div class="accordion-desc">
                                                <?php foreach ($dat['data'] as  $value) {
                                                    $sal_cat[$value['id']]= $value;
                                                    ?>
                                                    <div class="f-12 mt-3">
                                                       <?php $addsaldata = $value['id'];
                                                       echo $value['name']; ?>
                                                       <div class="checkbox-fade fade-in-primary float-right">
                                                        <label class="form-label ">
                                                            <input type="hidden" name="addsaldata" id="<?php echo $addsaldata."_detail"; ?>" value='<?php echo json_encode($value); ?>' >
                                                            <input type="hidden" id="<?php echo $addsaldata."check"; ?>" value="<?php echo in_array($addsaldata, (array)$checkdata) ? 1 : 0 ; ?>">
                                                            <input aria-label="Checkbox" type="checkbox"   <?php if(in_array($addsaldata, (array)$checkdata)){ echo "checked ";
} ?>
 <?php if(in_array($addsaldata, (array)$mandatroy)){ echo "disabled";
} ?>   name="Checkboxes1" onclick="addsaldata(<?php echo "'".$addsaldata."'"; ?>)" onkeydown="">
                                                            <span class="cr">
                                                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                          </span>

                                                          <span></span>
                                                      </label>
                                                  </div></div>
                                              <?php } ?>
                                          </div>
                                          <?php $j++;
} ?>

                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php $inn = "Basic salary Amount";?>
                          <div class="col-lg-9 col-md-9 col-12">
                            <form class="form" method="post" action="">
                                <div class="card card-custom gutter-b">
                                    <div class="page-header card p-3" style="margin: 0px; color: black;">
                                        <div class="d-inline">
                                            <h5>Salary</h5>
                                            <span>Define salary for employee</span>
                                            <div class="float-right">
                                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                                    <i class="icofont icofont-reply"></i> Back
                                                </a>
                                                <?php if($_GET['Rid']!=""){
                                                    $nid = $nid = "id:".$salarydata['employee_id'];
                                                ?>
                                                    <a href="index.php?option=COM_VIEW_SALARY_STRUCTURE&SEid=<?php echo deocdeId($nid); ?>" target="_blank" class="btn btn-primary">
                                                       View CTC
                                                   </a>
                                               <?php } ?>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="card-body">
                                    <div class="form-group row">
                                        <?php if($_GET['SCid']=="" && $cand_detaail['id'] ==""){ ?>
                                            <div class="form-group col-lg-4">
                                                <span class="form-label">Employee </span>
                                                <select aria-label="Employee" class="form-select js-example-basic-single" id="kt_select2_41" name='employee_id'>
                                                    <option> Select Any One</option>
                                                    <?php foreach ($employees as $employe) { ?>
                                                       <option <?php if( $sal_emp_id == $employe['id'] ){ echo "selected";
} ?> value="<?php echo $employe['id'] ?>"> <?php echo $employe['emp_code']." - ".$employe['display_name'] ?></option>
                                                   <?php } ?>
                                               </select>
                                           </div>
                                       <?php  } ?>
                                       if($cand_detaail['id'] !=""){
                                        $reporting_date = fromDate($cand_detaail['reporting_date']);
                                        $offer_validity = fromDate($cand_detaail['offer_validity']);
                                        $stylecss = 'style="display:none"';
                                        ?>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label d-block">Name</span>
                                            <span><?php echo $cand_detaail['display_name'];?></span>
                                            <input type="hidden" name="candidate_id" value="<?php echo $cand_detaail['id']; ?>">
                                        </div>
                                        <div class="form-group  col-lg-4">
                                            <span class="form-label">Joining Date<span class="text-danger">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                               <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo $reporting_date; ?>"  name="reporting_date"  id="kt_datepicker_4_1" />
                                        </div>
                                        <div class="form-group  col-lg-4" >
                                            <span class="form-label">LoI validity<span class="text-danger">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo $offer_validity; ?>"  name="offer_validity"  id="kt_datepicker_4_2" />
                                        </div>

                                    <div class="form-group  col-lg-4">
                                        <span class="form-label">Description</span>
                                        <textarea aria-label="Kt Autosize 2" class="form-control" id="kt_autosize_2" ><?php echo $salarydata['description']; ?></textarea>
                                    </div>
                                    <div class="form-group  col-lg-4">
                                        <span class="form-label">Notes</span>
                                        <textarea aria-label="Max 500 characters" class="form-control" id="kt_autosize_2_2" name="note" placeholder="Max 500 characters" ><?php echo $salarydata['note']; ?></textarea>
                                    </div>

                                    <div class="form-group  col-lg-4 " <?php echo $stylecss; ?>>
                                        <span class="form-label">Effective Date </span>
                                        <input aria-label="Affected Date" class="form-control datepicker" type="text" name="affected_date" value="<?php echo fromDate($salarydata['affected_date']); ?>">
                                    </div>

                                    <div class="form-group  col-lg-4">
                                        <span class="form-label">
                                           Joining Bonus Applicable
                                         </span>
                                        <div>
                                             <button type="button" onclick="changejoiningboun()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent">
                                            <?php $is_joining_bouns = $salarydata['joining_bonus_applicable']!=""?$salarydata['joining_bonus_applicable']: 1; ?>
                                            <input aria-label="Checkbox" type="checkbox"  class="js-switch" value="<?php echo $sen; ?>"  name="is_joining_bonus" <?php if($is_joining_bouns== 0 ){ ?> checked <?php
} ?>  />
                                             <input type="hidden"   value="<?php echo $is_joining_bouns; ?>"  name="joining_bonus_applicable" id="is_joining_bouns_val"  />
                                             </button>
                                        </div>
                                    </div>

                                    <div class="form-group  col-lg-4" id="joining_bouns_text" <?php if($is_joining_bouns ==1){ ?> style="display:none; "; <?php
} ?>>
                                        <span class="form-label">
                                           Please Enter Joining Bonus
                                         </span>
                                        <div>
                                            <input aria-label="Joining Bonus Amount" class="form-control text-right" id="joining_bouns" type="text" name="joining_bonus_amount" value="<?php echo $salarydata['joining_bonus_amount']!=""? $salarydata['joining_bonus_amount']: '0.00'; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group  col-lg-4" id="empty_text" <?php if($is_joining_bouns ==0){ ?> style="display:none; "; <?php
} ?>>
                                    </div>

                                    <div class="form-group  col-lg-4 ">
                                        <span class="form-label">
                                           Please Enter Annual CTC
                                         </span>
                                        <div>
                                            <input aria-label="Ctc Val" class="form-control text-right" id="ctc_yearly" type="text" name="ctc_val" value="<?php echo $salarydata['ctc_yearly']!=""? $salarydata['ctc_yearly']: '0.00'; ?>" onblur="changeyearctcwork(this.value)" >
                                        </div>
                                    </div>

                                    <div class="form-group  col-lg-2 ">
                                        <span class="form-label">
                                           Variable Pay With CTC
                                         </span>
                                        <div>
                                             <button type="button" onclick="changeyearctcwork('S')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent">
                                            <?php $vp_with_ctc = $salarydata['vp_with_ctc']!=""?$salarydata['vp_with_ctc']: 0; ?>
                                            <input aria-label="Checkbox" type="checkbox"  class="js-switch" value="<?php echo $sen; ?>"  name="vp_with_ctc" <?php if($vp_with_ctc== 0 ){ ?> checked <?php
} ?>  />
                                             <input type="hidden"   value="<?php echo $vp_with_ctc; ?>"  name="vp_with_ctc_val" id="vp_with_ctc_val"  />
                                             </button>
                                        </div>
                                    </div>
                                    <?php
                                    $fixed_ctc_yearly = $salarydata['fixed_ctc_yearly']!=""? $salarydata['fixed_ctc_yearly']: '0.00';
                                    $variable_yearly = $salarydata['variable_yearly']!=""? $salarydata['variable_yearly']: '0.00';
                                    ?>
                                    <div class="form-group  col-lg-3">
                                        <span class="form-label">
                                           Variable Pay
                                         </span>
                                        <div>
                                             <input aria-label="Variable Yearly" class="form-control text-right mt-2" id="variable_yearly" readonly type="text" name="variable_yearly" value="<?php echo round($variable_yearly); ?>" >
                                        </div>
                                    </div>
                                    <div class="form-group  col-lg-3 ">
                                        <span class="form-label">
                                           Fixed CTC
                                         </span>
                                        <div>
                                             <input aria-label="Fixed Ctc Yearly" class="form-control text-right mt-2" id="fixed_ctc_yearly" readonly type="text" name="fixed_ctc_yearly" value="<?php echo round($fixed_ctc_yearly); ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                        if(in_array($dat['id'], (array)$checkmainids)){
                                            $maincss ="";
                                        }else{
                                        }
                                        ?>
                                        <table <?php echo $maincss; ?> id="invoiceItem<?php echo  $dat['id']; ?>">
                                            <tbody>
                                                <tr  id="<?php echo "cat_".$dat['id']; ?>">
                                                    <th scope="col" style="width: 30%" class="pl-0 py-4" >
                                                        <span class="form-label f-16">
                                                        <u><?php echo $dat['name'];?></u>
                                                        </span>
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
                                                }elseif($value['calculation_type'] == 'L'){
                                                    $codedetail = "Link";
                                                     $read_only = "readonly";
                                                }else{
                                                     $codedetail = "Fixed";
                                                     $read_only = "";
                                                }
                                                if($value['code'] == 'PAY_CTC'){
                                                    $read_only = "readonly";
                                                 }
                                             $note = urlencode($value['note']);
                                             if(in_array($value['id'], (array)$checkdata)){
                                                 ?>
                                                 <tr  id="<?php echo $value['id']; ?>" >
                                                    <td style="width: 30%">
                                                        <div class="form-group col-lg-11" >
                                                            <input aria-label="Input field" type="text" class="form-control itemRow f-w-800" readonly value="<?php echo $value['name']; ?>" name="name[]">
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
                                                            <?php
                                                             if ($rid!="" && $value['calculation_type']=='A') {
                                                                    $component_number = $placevalue[$value['id']]['monthly_amount'];
                                                                }

                                                            ?>
                                                            <input aria-label="Input field" type="text" <?php echo $read_only; ?> onblur="monthlytoyearlycat(this.value,<?php echo $value['id'] ?>)" class="form-control text-right width1" value="<?php echo $component_number; ?>" name="calucation[]" id="<?php echo 'ernam_'.$value['id']; ?>" aria-describedby="basic-addon2"/>
                                                            <button style="cursor: none;" class="btn btn-primary w-100PX" type="button"><?php echo $codedetail; ?> </button>
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

                                                        <input aria-label="Input field" type="text" readonly id="y_amount_<?php echo $value['id'] ?>" name="yearly_amount[]"  value ="<?php echo   round($placevalue[$value['id']]['yearly_amount']); ?>" class="form-control text-right width1"placeholder="0.00">

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

                                    <input type="hidden" name="" id="valofemppf" value="<?php echo $listpf['employee_contribution']; ?>">
                                    <input type="hidden" name="" id="valofemprpf" value="<?php echo $listpf['employer_contribution']; ?>">
                                    <input type="hidden" name="" id="checkval" value="<?php echo $listpf['condition_limit']; ?>">
                                    <input type="hidden" name="" id="salary_variable_pay_slabs" value='<?php echo $svp_js; ?>'>
                                    <input type="hidden" name="" id="exp" value="5">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8 text-right">
                                   <?php if($_GET['Rid']!="" || $_GET['SCid']!=""){ ?>
                                    <input type="hidden"   name="check" value="update">
                                    <input type="hidden"   name="rid" value="<?php  echo $rid; ?>">
                                <?php } ?>
                                <input type="hidden"   name="submit" value="submit">
                                <button type="submit" <?php echo $disabled; ?> class="btn btn-primary mr-2">Submit</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog"  tabindex="-1" aria-hidden="true"  role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content" >

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"  id="popup1">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect waves-light " data-bs-dismiss="modal" aria-label="Close">Close</button>

            </div>
        </div>
    </div>
</div>
<?php }else{
    $main_ids = encodeId($_GET['SEid']);
    $main_id = explode(":",$main_ids);
    $url = API."?mode=togetsalaryemployeedetails&employee_id=".$main_id['1'];
    $salaryjs = callAPI($url);
    $salarydata = json_decode($salaryjs,1);
    $emp_data = $salarydata['emp_data'];
    foreach($salarydata['salary_data'] as $sald){
        $sal[$sald['code']] = $sald;
    }
    ?>
     <div class="card card-custom " id="kt_page_sticky_card">
        <div class="card-header">
    <h5> Salary Structure </h5>
    <div class="card-header-right">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                                    <i class="icofont icofont-reply"></i> Back
                                                </a>
    </div>
  </div>
        <div class="card-body">
            <div class="row">
                <div class="row align-items-center" style="margin-top: 10px;padding: 0px 10%;" id="printarea">
                    <div class="col-lg-12">
                        <div style="width:80%;text-align: center;" class="f-w-800">
                            <div class=" width:10%; float-left m-r-10">
                                Effective Date :
                            </div>
                            <div class="width:70% float-left">
                                <?php echo  date ( "dS M , Y", strtotime($salarydata['salary']['affected_date'])); ?>
                            </div>
                        </div>
                    </div>
                    <div  class="col-lg-12" style="page-break-before: always;">
                        <table style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px; margin-left: auto; margin-right: auto;" role="presentation">
                            <tr style="height: 50px;border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px; font-weight: bold;font-size: 16px;" class="f-w-600" >Name </td>
                                <td  style="border:1px solid #000;padding: 5px; font-weight: bold;font-size: 16px;"colspan="2">&nbsp;<?php echo   $emp_data['display_name']." (".$emp_data['emp_code'].")"; ?></td>
                            </tr>
                            <tr style="height: 50px;border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px; font-weight: bold;font-size: 16px;" class="f-w-600" >Effective Date </td>
                                <td  style="border:1px solid #000;padding: 5px; font-weight: bold;font-size: 16px;"colspan="2">&nbsp;<?php echo  date ( "dS M , Y", strtotime($salarydata['salary']['affected_date'])); ?></td>
                            </tr>
                            <tr style="border: 1px solid #000; font-weight: bold;font-size: 16px;background-color: #F4C294;">
                                <td style="border:1px solid #000;padding: 5px;text-align: center;" class="f-w-800">Particulars</td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-800">Per Month  </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-800">Per Year</td>
                            </tr>
                            <?php
                                $frist_arry = array('BASIC', 'HRA', 'SPA', 'LTA', 'BONUS');
                                $fsumm =0; $fsumy =0;
                                foreach($frist_arry as $arr){
                                     $fsumm = $fsumm+$sal[$compcode]['monthly_amount'];
                                     $fsumy = $fsumy+$sal[$compcode]['yearly_amount'];
                                    $compcode =$arr;
                            ?>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo $sal[$compcode]['component_name']; ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                     <?php echo customeNumberFormate(round($sal[$compcode]['monthly_amount'])); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($sal[$compcode]['yearly_amount'])); ?>
                                </td>
                            </tr>
                        <?php } ?>
                            <tr>
                                <td colspan="3"></td>
                            </tr>

                            <tr style="border: 1px solid #000;font-weight: bold;font-size: 16px;background-color: #F4C294;">
                                <td style="border:1px solid #000;padding: 5px;font-weight: bold;font-size: 16px;" class="f-w-600">
                                    <?php echo "Total (A)"; ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                     <?php echo customeNumberFormate(round($fsumm)); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($fsumy)); ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3"></td>
                            </tr>


                            <tr>
                                <td colspan="3"></td>
                            </tr>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;font-weight: bold;font-size: 16px;" class="f-w-600">
                                    <?php echo "Variable Component (B)";
                                    $ssumy=$salarydata['salary']['variable_yearly'];
                                    $ssumm= $salarydata['salary']['variable_yearly']/12;
                                ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($ssumm)); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($ssumy)); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                            <tr style="border: 1px solid #000;font-weight: bold;font-size: 16px;background-color: #F4C294;">
                                <td style="border:1px solid #000;padding: 5px;font-weight: bold;font-size: 16px;text-align: center;" class="f-w-600">
                                    <?php echo "Other employment benefits"; ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">

                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">

                                </td>
                            </tr>
                            <?php
                                $frist_arry = array('EMPLOYERPF', 'GRATUITY', 'MIP', 'EMPLOYERESI');
                                $tsumm =0; $tsumy =0;
                                foreach($frist_arry as $arr){
                                     $tsumm = $tsumm+$sal[$compcode]['monthly_amount'];
                                     $tsumy = $tsumy+intval(round($sal[$compcode]['yearly_amount']));
                                    $compcode =$arr;
                            ?>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo $sal[$compcode]['component_name']; ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                     <?php echo customeNumberFormate(round($sal[$compcode]['monthly_amount'])); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($sal[$compcode]['yearly_amount'])); ?>
                                </td>
                            </tr>
                        <?php } ?>

                            <tr style="border: 1px solid #000;font-weight: bold;font-size: 16px;background-color: #F4C294;">
                                <td style="border:1px solid #000;padding: 5px;font-weight: bold;font-size: 16px;" class="f-w-600">
                                    <?php echo "Total (C)"; ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                     <?php echo customeNumberFormate(round($tsumm)); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-600">
                                    <?php echo customeNumberFormate(round($tsumy)); ?>
                                </td>
                            </tr>


                            <tr>
                                <td colspan="3"></td>
                            </tr>
                             <tr style="border: 1px solid #000;font-weight: bold;font-size: 16px;background-color: #F4C294;">
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-800">
                                    <?php echo "CTC  (D= A+B+C)"; ?>
                                </td>
                                <?php
                                    $ctc = $fsumy+$ssumy+$tsumy;
                                    $mctc = $fsumm+$ssumm+$tsumm;
                                ?>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-800">
                                       <?php
                                    echo customeNumberFormate(round($mctc)); ?>
                                </td>
                                <td style="text-align: right;border:1px solid #000;padding: 5px;" class="f-w-800">
                                    <?php
                                    echo customeNumberFormate(round($ctc)); ?>
                                </td>
                            </tr>
                              <tr>
                                <td colspan="3"></td>
                            </tr>

                    </table>
                </div>

            </div>

        </div>
</div>

<?php } ?>
