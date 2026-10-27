<?php 
$cand_detaail = $data;
if(isset($_POST['submit'])){
   $option =$_GET['option'];
   $_POST['company_id'] = $_SESSION['company_id'];
   $_POST['created_by'] = $_SESSION['userid'];
   $_POST['created_date'] = nowDate();
   $url = API."?mode=addsalary&method=post";
   $response = file_post_contents($url, $_POST, 'insert');
   if ($response) {
        if($_GET['option']=='COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS'){
            header("location:index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=ATT&EMPID=".$EMPID);
        }else{
            header("location:index.php?option=COM_LIST_SALARY");
        }
       
       
   }
}
$pageurl = API."?mode=togetsalarycatdetaillist";
$data =  callAPI($pageurl);
$data = json_decode($data,1);
//echo "<pre>"; print_r($data);
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','employee_type'=>1);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata; 
$configesponse = callAPI($vurl);
$employees = json_decode($configesponse,1);
$disabled="";
if($_GET['Rid']!="" || $_GET['SEid']!=""){ 
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
        //echo "<pre>".$main_ids."--"; print_r($_GET);print_r($main_id); print_r($datawr);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        //print_r($salarydata);
        //print_r($salarydata);
        $salarydata = $salarydata[0];
    }
    if($_GET['SCid']!=""){
        $disabled = "";
        if($cand_detaail['current_status']==ST_APPROVED){
            $disabled = "disabled"; 
        }
        $datawr["where"] = array('is_active'=> '0','candidate_id'=>$_GET['SCid']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
        $salary_temp_id =$salarydata['id'];
    }
    $stpdata["where"] = array('is_active'=> '0','salary_template_id'=>$salarydata['id']);
    $stpdata["order"] = "component_cat_id ASC";
    $stpmdata = urlencode(json_encode($stpdata));
    //echo "<pre>"; print_r($stpdata);
    $stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
    $salarytmpdata = callAPI($stpurl);
    $salarytmpdata = json_decode($salarytmpdata,1);
    //print_r($salarytmpdata);
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
    //echo $curl;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$esdata =  $cudata['0'];
$listpf = $esdata;
$pfval = $esdata['employee_contribution'];
$emppfval = $esdata['employer_contribution'];
$curl = API."?mode=gettabledetailswithconditions&table=emp_state_insaurance&data=".$eedata; 
    //echo $curl;
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

$sal_emp_id = $salarydata['employee_id']!="" ? $salarydata['employee_id'] : $_GET['newempid'];

/*if(count((array)$salarytmpdata)<1){
    $salarytmpdata = "";
}
echo "---------"; print_r($salarydata);echo "++++";print_r($salarytmpdata);*/
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
    function calucationstocat(){
       // console.log("step1");
       var cvall = $("#xxx").val();
       var contt = cvall.split(",");
       var count = contt.length;
        //var count = $(".itemRow").length;
        //alert("test");
        console.log("XXX="+count);
        var month =0;  var year =0;
        console.log("step1-- Count:"+count+"count="+contt);
        for(var k=0 ; k<count;k++ ){
            var i= contt[k];
            var cat_type = $("#cat_type_"+i).val();
            //alert(cat_type);
            ///var ccheck = $('#cat_type_'+i).val();
            var cat_code= $("#cat_code_"+i).val();
            console.log("Step 2 :  code:"+cat_code+"type"+cat_type);
            if(cat_type != undefined){  

                if( cat_type =='P'){
                    var cat_cat = $("#cat_calculation_category_"+i).val();
                    var cat_num = $("#cat_num_"+i).val();
                    var basic = $("#"+cat_cat).val();
                   // //alert(cat_num);
                   if(!basic){ basic = 0; }
                   // //alert(basic);
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
                        //const json = '{"result":true, "count":42}';
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
                           // if(calw == undefined){ calw =0; }
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
                           // if(cale == undefined){ cale =0; }
                           console.log("c21:"+cale);
                       }else{
                        cale = fche.rcode;
                        console.log("b2:"+cale);
                    }
                        //calw = $("#"+fche.lcode).val();
                        //cale = $("#"+fche.rcode).val();
                        calr = fche.loperand;
                        laop = fche.roperand;
                        
                        cals =  calucalationsum(calw,cale,calr);
                        console.log("check1##"+calw+"check2##"+cale+"check3##"+calr+'check4##'+laop);
                        //cals = parseFloat(calw)+calr+parseFloat(cale);
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
   /*function spicalallowances(id){
        //alert("rav");
        var basic = $("#total_month").val();
        var ctc = $("#ctc").val();
        sp_amount = ctc-basic;
        var OHE = $("#OHE").val(); if(!OHE){ OHE = 0; }
        var PF = $("#PF").val(); if(!PF){ PF = 0; }
        var CEA = $("#CEA").val(); if(!CEA){ CEA = 0; }
        var HEA = $("#HEA").val(); if(!HEA){ HEA = 0; }
        console.log("CTC:"+ctc+" ,total month:"+basic+" ,OHE:"+OHE+",PF:"+PF+"SP Amount :"+sp_amount);
        sp_amount = sp_amount+parseFloat(OHE)+parseFloat(PF)+parseFloat(CEA)+parseFloat(HEA);
        sp_amount = parseFloat(sp_amount).toFixed(2);
        $('#m_amount_'+id).val(sp_amount);
        $('#y_amount_'+id).val(sp_amount*12);
        var month_amount = parseFloat(basic)+parseFloat(sp_amount);
        $('#total_month').val(Math.round(month_amount));
        $('#total_year').val(Math.round(month_amount*12));

    }*/
   /* function monthlytoyearlycatnew(){
        alert("hai");
        $("[id^='cat_type_']").each(function() {
            var i = $(this).attr('id');
            i = i.replace("cat_type_",'');
            var cat_type = $('#cat_type_'+i).val();           
            var cal = $('#m_amount_'+i).val();
            var cal2 = $('#y_amount_'+i).val();
            var cha =  $('#change_val_'+i).val();
            var code = $('#cat_code_'+i).val(); 
            console.log(cat_type+"----"+cal+"----"+ cal2+"----"+cha+"----");
            if(cat_type == 'P'){

            }else if(cat_type == 'F'){

            }else{
                month_amount=parseFloat(cha)/12;
                year_amount = cha;
            }
            $('#m_amount_'+i).val(month_amount);
            $('#y_amount_'+i).val(year_amount);
            $('#'+code).val(month_amount);
            $('#cat_num_'+i).val(month_amount);

        });
    }*/
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
    /*function ctcmonthlychange(val){
        var mvar = val/12;
        $('#ctc').val(parseFloat(mvar).toFixed(2));
    }*/
    /*function monthlytoyearly(val,id){
        ////alert(val);
        $('#y_amount_'+id).val(val*12);
        //calucations();
    }*/
    function dynamicinformationopup(option){
       // //alert("information");
       $('#popup1').html(decodeURI(option));
       // $('#kt_modal_KTDatatable_remote').modal({ show: true});
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
            ////alert(data);
            var obj = JSON.parse(data);
            //lert(obj.name);
            addrows(obj,val);
            addandremove('ADD',val);
            calucationstocat();
        }else{
            $('#'+val+"check").val(0);
            //alert('shankar');
            $('#'+val).remove();
            addandremove('REMOVE',val);
            //alert('shankar');
            calucationstocat();
        }
       //calucationstocat();
   }
   function satutrycalucation(catlink_value){
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
                //catlink_value =15678;
                var catlink_esvv = JSON.stringify(catlink_evv);
                console.log(catlink_esvv);
                for(var i=0; i<4; i++){

                    var checks = catlink_esvv[i];
                    console.log("Datat"+checks);
                    //console.log(checks['start_range']);
                    if(checks['start_range']<catlink_value && checks['end_range']>catlink_value){
                        //console.log('----------------Yes---------------'+checks["tax_amount"]);
                        month_amount = checks['tax_amount'];
                        console.log(month_amount);
                        break;
                    }else{
                        month_amount = 200;
                    }
                }                            
            }
            return month_amount;
        }
        function addrows(data,id){
    /*var count = $(".itemRow").length;
    count++;*/
    var count = id;
    var month_amount = 0;
    var reead = '';
        //alert(data.calculation_type);
        console.log(data);
        if(data.calculation_type !="" ){
           // //alert("ravi"+data.calculation_type);
         //  //alert(data.calculation_type);
         if(data.calculation_type == 'P' &&  data.calculation_category != ''){
            var codedetail1 = data.calculation_category.toUpperCase(); 
            var codedetail = "% "+codedetail1.replace("_","  ");
            var basic = $("#"+data.calculation_category).val();
           // console.log("check percentage:"+data.calculation_number+"check valu:"+basic);
           if(!basic){ basic = 0; }
           month_amount = (parseFloat(data.calculation_number)/100)* parseFloat(basic);
           console.log("check percentage:"+data.calculation_number+"check valu:"+basic+"month_amount:"+month_amount);
       }else if(data.calculation_type == 'F' &&  data.calculation_category != ''){
        var codedetail = "Formula";
        var formula = data.formula_details;
        reead = 'readonly';
        console.log(formula);
            //const json = '{"result":true, "count":42}';
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
                //calw = $("#"+fche.lcode).val();
                //cale = $("#"+fche.rcode).val();
                calr = fche.loperand;
                laop = fche.roperand;
                
                cals =  calucalationsum(calw,cale,calr);
                console.log("check1##"+calw+"check2##"+cale+"check3##"+calr+'check4##'+laop);
                //cals = parseFloat(calw)+calr+parseFloat(cale);
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
            // //alert('ravi');
            console.log(data.calculation_number);
            month_amount = data.calculation_number;
            console.log(month_amount);
            var codedetail = "Fixed";
            console.log(codedetail);
        }

        var gettotalamt  = parseFloat(month_amount*12).toFixed();
        month_amount = parseFloat(month_amount).toFixed();
            // console.log(month_amount);
        }
       // //alert(month_amount); //alert(data.code);
       

       var htmlRows = '';
       var note = "'"+data.note+"'";
       htmlRows += '<tr id="'+id+'">'; 
       htmlRows += '<td><div class="form-group col-lg-11" ><input type="text" class="form-control itemRow f-w-800" readonly value="'+data.name+' ('+data.code+')" name="name[]"><input type="hidden" class="form-control " readonly value="'+data.id+'" id="id_'+count+'"  name="id[]"></div></td>';     
       htmlRows += '<td><div class="form-group col-lg-11">  <div class="input-group input-group-button"> <input type="text" onblur="monthlytoyearlycat(this.value,'+count+')" '+reead+' class="form-control text-right width1" value="'+data.calculation_number+'" id="change_val_'+count+'" name="calucation[]" /> <button class="btn btn-primary w-100PX" type="button">'+codedetail+'</button> </td>';
       htmlRows += '<td><div class="form-group col-lg-11"><input type="text" class="form-control text-right"  placeholder="0.00" name="monthly_amount[]" readonly id="m_amount_'+count+'"  value ="'+month_amount+'"  ></div> <input type="hidden" name="cat_type[]" value="'+data.calculation_type+'" id="cat_type_'+count+'">  <input type="hidden" name="cat_calculation_category[]" value="'+data.calculation_category+'" id="cat_calculation_category_'+count+'"> <input type="hidden" name="cat_code[]" value="'+data.code+'" id="cat_code_'+count+'">     <input type="hidden" name="cat_cat[]" value="'+data.category_id+'" id="cat_cat_'+count+'"> <input type="hidden" name="cat_num[]" value="'+data.calculation_number+'" id="cat_num_'+count+'"></td>';            
       htmlRows += '<td><div class="form-group col-lg-11"><div class="input-group"> <input type="hidden" name="'+data.code+'" value="'+month_amount+'" id="'+data.code+'"><input type="text" readonly id="y_amount_'+count+'" name="yearly_amount[]"  value ="'+gettotalamt+'" class="form-control text-right width1" placeholder="0.00"><a href="#" class="mt-6" onclick="dynamicinformationopup('+encodeURI(note)+')">&nbsp;<i class="fa fa-info-circle f-14"></i> </a></div> </div></td>'; 
       htmlRows += '</tr>';
       $("#invoiceItem"+data.category_id).css("display", "block");
       $('#invoiceItem'+data.category_id).append(htmlRows);
       //calucations();
   }
   /*function removerows(val){
       // $(".itemRow:checked").each(function() {
            //console.log(this);
            $('#'+val).remove();
           // calucationstocat();
          // calucations();
        //});
        //$('#checkAll').prop('checked', false);
    }*/
    /*function calucations(){
       // //alert("ravi");
       var count = $(".itemRow").length;
        ////alert(count);
        var month =0;  var year =0;
       // //alert("RAVI");
       $("[id^='cat_type_']").each(function() {
            var i = $(this).attr('id');
            i = i.replace("cat_type_",'');
               // //alert(i);
               var ccheck = $('#cat_type_'+i).val();
               if( ccheck!='SP'){
                var cal = $('#m_amount_'+i).val();
                var cal2 = $('#y_amount_'+i).val();
                month = parseFloat(month)+ parseFloat(cal);
                year = parseFloat(year)+ parseFloat(cal2);
            }
        });
         ////alert(month);
         $('#total_month').val(Math.round(month));
         $('#total_year').val(Math.round(year));
         var sps = $('#SPS').val();
         if(sps !=0){
            spicalallowances(sps);
        }
    }*/
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid ">
        <div class="">
            <div class="">
                <?php if($_SESSION['admin_type']==1){ ?>
                    <div class="row salary-template">
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
                                                            <input type="checkbox"   <?php if(in_array($addsaldata, (array)$checkdata)){ echo "checked "; } ?> <?php if(in_array($addsaldata, (array)$mandatroy)){ echo "disabled"; } ?>   name="Checkboxes1" onclick="addsaldata(<?php echo "'".$addsaldata."'"; ?>)">
                                                            <span class="cr">
                                                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                          </span>

                                                          <span></span>
                                                      </label>
                                                  </div></div>
                                              <?php } ?>
                                          </div>
                                          <?php $j++; } ?>

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
                                                <?php if($_GET['Rid']!=""){ ?>
                                                    <a href="index.php?option=COM_VIEW_SALARY_PAYSLIP&Rid=<?php echo $_GET['Rid']; ?>" target="_blank" class="btn btn-primary">
                                                       Payslip
                                                   </a>
                                               <?php } ?>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="card-body">
                                    <div class="form-group row">
                                        <?php if($_GET['SCid']==""){ ?>
                                            <div class="col-lg-4">
                                                <label>Employee </label>
                                                <select class="form-select js-example-basic-single" id="kt_select2_41" name='employee_id'>
                                                    <option> Select Any One</option>
                                                    <?php foreach ($employees as $employe) { ?>
                                                       <option <?php if( $sal_emp_id == $employe['id'] ){ echo "selected"; } ?> value="<?php echo $employe['id'] ?>"> <?php echo $employe['display_name'] ?></option>
                                                   <?php } ?>
                                               </select>
                                           </div>
                                       <?php  } ?>
                                       <div class="col-lg-4">
                                        <label>Description</label>
                                        <textarea class="form-control" id="kt_autosize_2" ><?php echo $salarydata['description']; ?></textarea>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Notes</label>
                                        <textarea class="form-control" id="kt_autosize_2" name="note" placeholder="Max 500 characters" ><?php echo $salarydata['note']; ?></textarea>
                                    </div>

                                    <div class="col-lg-4 ">
                                        <label>Effective Date <?php echo $salarydata['affected_date']; ?></label>
                                        <input class="form-control datepicker" type="text" name="affected_date" value="<?php echo fromDate($salarydata['affected_date']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-0 pb-3">
                                <div class="table-responsive">
                                    <table class="table table-head-custom table-head-bg table-borderless table-vertical-center"  >
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th style="width: 30%" class="pl-7">
                                                    <label class="f-14">Salary Component</label>
                                                </th>
                                                <th style="width: 20%"><label class="f-14">Calculation Type</label></th>
                                                <th style="width: 20%;text-align: center;"><label class="f-14">Amount Monthly</label></th>
                                                <th style="width: 20%"><label class="f-14">Amount Annually</label></th>
                                                <!-- <th style="width: 15%">Remarks</th> -->
                                            </tr>
                                        </thead>
                                    </table>
                                    <?php $j=1; foreach ($data as  $dat) { 
                                            //print_r($dat);
                                        if(in_array($dat['id'], (array)$checkmainids)){
                                            $maincss ="";
                                        }else{
                                                //$maincss = "style='display: none;'";
                                        }
                                        ?>
                                        <table <?php echo $maincss; ?> id="invoiceItem<?php echo  $dat['id']; ?>">
                                            <tbody>
                                                <tr  id="<?php echo "cat_".$dat['id']; ?>">
                                                    <td style="width: 30%" class="pl-0 py-4" >
                                                        <b><?php echo $dat['name'];?></b>
                                                    </td>

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
                                                 //print_r($value);
                                             if(in_array($value['id'], (array)$checkdata)){
                                                 ?>
                                                 <tr  id="<?php echo $value['id']; ?>" > 
                                                    <td style="width: 30%">
                                                        <div class="form-group col-lg-11" >
                                                            <input type="text" class="form-control itemRow f-w-800" readonly value="<?php echo $value['name']."(".$value['code'].")"; ?>" name="name[]">
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
                                                            <input type="text" <?php echo $read_only; ?> onblur="monthlytoyearlycat(this.value,<?php echo$value['id'] ?>)" class="form-control text-right width1" value="<?php echo $component_number; ?>" name="calucation[]" aria-describedby="basic-addon2"/>  
                                                            <button class="btn btn-primary w-100PX" type="button"><?php echo $codedetail; ?> </button>
                                                        </div>
                                                    </div>
                                                </td>   
                                                <td style="width: 20%" >
                                                    <?php
                                                    $cat_type = $placevalue[$value['id']]['component_type'];
                                                        // if($cat_type ==""){
                                                    $cat_type = $value['calculation_type'];
                                                         ///}
                                                    $component_number = $placevalue[$value['id']]['component_number'];
                                                    if($component_number ==""){
                                                     $component_number = $value['calculation_number'];
                                                 }
                                                 ?>
                                                 <div class="form-group col-lg-11">
                                                    <input type="text" class="form-control text-right" readonly placeholder="0.00" name="monthly_amount[]" value ="<?php echo $placevalue[$value['id']]['monthly_amount'];?>"  id="m_amount_<?php echo $value['id']; ?>">
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

                                                        <input type="text" readonly id="y_amount_<?php echo $value['id'] ?>" name="yearly_amount[]"  value ="<?php echo   $placevalue[$value['id']]['yearly_amount']; ?>" class="form-control text-right width1"placeholder="0.00">

                                                        <a href="#" class="mt-6" onclick="dynamicinformationopup(<?php echo "'".$note."'"; ?>)">&nbsp;<i class="fa fa-info-circle f-14"></i></a>


                                                    </div> </div>

                                                </td>
                                            </tr>

                                        <?php }  } ?>
                                    </table>
                                    <?php $j++; } ?>
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

                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8 text-right">
                                   <?php if($_GET['Rid']!=""){ ?> 
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
   /* $main_ids = encodeId($_GET['Rid']);
    $main_id = explode(":",$main_ids);
    $datawr["where"] = array('is_active'=> '0','id'=>$main_id['1']);
    $costdata = urlencode(json_encode($datawr));
    $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
    $salarydata = callAPI($url);
    $salarydata = json_decode($salarydata,1);
    $salarydata = $salarydata[0];
    $stpdata["where"] = array('is_active'=> '0','salary_template_id'=>$salarydata['id']);
    $stpdata["order"] = "component_cat_id ASC";
    $stpmdata = urlencode(json_encode($stpdata));
    $stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
    $salarytmpdata = callAPI($stpurl);
    $salarytmpdata = json_decode($salarytmpdata,1);*/
    $datacat["where"] = array('is_active'=> '0');
    $catdata = urlencode(json_encode($datacat));
    $caturl = API."?mode=gettabledetailswithconditions&table=salary_categories&data=".$catdata;
    $salarycatdata = callAPI($caturl);
    $salarycatdata = json_decode($salarycatdata,1);
    foreach ($salarycatdata as $cate) {
        $cat[$cate['id']] = $cate['name'];
    }

    foreach ($employees as $employee) {
        $empdata[$employee['id']] = $employee['display_name'];
    }
    ?>

    <div class="card card-custom " id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">&nbsp;<?php echo " Salaray Template";  ?> </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="row align-items-center" style="margin-top: 10px;padding: 0px 10%;" id="printarea">

                    <div  class="col-lg-12" style="page-break-before: always;">                             
                        <table width="80%" align="center" style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px;" >
                            <tr style="height: 50px;border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-600" >Name </td>
                                <td  style="border:1px solid #000;padding: 5px;"colspan="2">&nbsp;<?php echo  $empdata[$salarydata['employee_id']]; ?></td>
                            </tr>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-600">COMPENSATION DETAILS   </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;text-align: right;" class="f-w-600">MONTHLY  </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;text-align: right;" class="f-w-600">YEARLY</td>
                            </tr>
                            <?php $nm=0; foreach ((array)$salarytmpdata as  $stvalue) { ?>
                                <?php if($stvalue['component_cat_id'] !=$nm ){ $nm =$stvalue['component_cat_id'];  ?>
                                <tr style="border: 1px solid #000;" >
                                    <td style="font-size: 14px;border:1px solid #000;padding: 5px;" class="f-w-600" >&nbsp;  <?php echo $cat[$nm];?></td>
                                    <td style="border:1px solid #000;padding: 5px;text-align: right;"> </td>
                                    <td style="border:1px solid #000;padding: 5px;text-align: left">  </td>
                                </tr>
                            <?php } ?>
                            <tr style="border: 1px solid #000;" >
                                <td style="border:1px solid #000;padding: 5px;" class="f-w-600" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $stvalue['component_name'];?></td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;text-align: right;"> <?php echo customeummarmonyformate($stvalue['monthly_amount']);?></td>
                                <td style="text-align: left;border:1px solid #000;padding: 5px;text-align: right;" ><?php echo customeummarmonyformate($stvalue['yearly_amount']);?></td>
                            </tr>
                        <?php } ?>
                       <!--  <tr style="border: 1px solid #000;" >
                            <td style="border:1px solid #000;padding: 5px;" ><?php echo "Total";?></td>
                            <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($salarydata['company_cost_monthly']);?></td>
                            <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($salarydata['company_cost_yearly']);?></td>
                        </tr>
                        <tr style="border: 1px solid #000;" >
                            <td colspan="3" style="border:1px solid #000;padding: 5px;">&nbsp;  Notes: </br>
                                <p style="padding: 0px 5%;"><?php echo  $salarydata['note']; ?></p>
                            </td>

                        </tr> -->
                    </table>
                </div>
                <!-- <div style="width: 50%; display: inline-block; margin: 30px 0 0 0;">
                    <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">With best wishes.<br />For <?php echo  $send_data['company_name']; ?></b>
                </div>
                <div style="width: 48%; display: inline-block; margin: 30px 0 0 0; text-align: right;">
                    <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">ANJANA AUDUKOORI<br />DIRECTOR HR</b>
                </div>
                <div style="width: 50%; display: inline-block; margin: 40px 0 0 0;">
                    <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
                    <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Candidate Signature</b>
                </div>
                <div style="width: 48%; display: inline-block; margin: 40px 0 0 0; text-align: right;">
                    <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
                    <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Date</b>
                </div>
 -->
            </div>
            <!--end: Search Form -->
        </div>
    </div>
</div>
</div>

<?php } ?>