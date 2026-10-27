<?php
ini_set('display_errors' ,0);
include('includes/session.php');
//$_SESSION['DB_NAME']="infistaq_infisuite_1";
include('dbconfig.php');
include('comm.php');

if(isset($_SESSION['sbt_message']) AND $_SESSION['sbt_message'] == 1){ ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
           tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'];?>','');
        } 
      } 
    </script>

<?php  unset($_SESSION['sbt_message']); }


$helptext = array('location'=>'Please Enter Your Location',
  'org_name'=>'Please Enter Organization Name',
  'phone'=>'Please Enter Phone Number',
  'email'=>'Please Enter Email Address',
  'industry'=>'Please Select Your Industry',
  'country'=>'Please Select Your Country',
  'state'=>'Please Select Your State',
  'pincode'=>'Please Enter Your Pincode',
  'city'=>'Please Select Your City',
  'dateformat'=>'Please Enter Date Format',
  'logo'=>'Please Choose Your Company Logo',
  'kpimonth'=>'Please Select KPI Monthly Task Day',
  'kpiweekday'=>'Please Select KPI Weekly Task Day',
  'licensenumber'=>'Please Enter License Number',
  'pan'=>'Please Enter PAN Number',
  'tan'=>'Please Enter TAN Number',
  'gst'=>'Please Enter GST Number',
  'cin'=>'Please Enter CIN Number',
  'workdays'=>'Please Select Working Days',
  'minhrs'=>'Please Select Minimum Working Hours',
  'halfhrs'=>'Please Select Halfday Working Days',
  'grace'=>'Please Select Grace Period',
  'payroll'=>'Please Choose Your Payroll Day',
  'epf'=>'Please Enter EPF Number',
  'epfempc'=>'Please Enter Employee Contribution',
  'epfemprc'=>'Please Enter Employer Contribution',
  'epfdeduct'=>'Please Select EPF Deduction Cycle',
  'esi'=>'Please Enter ESI Number',
  'esiempc'=>'Please Enter Employee Contribution',
  'esiemprc'=>'Please Enter Employer Contribution',
  'esideduct'=>'Please Select ESI Deduction Cycle',
  'pt'=>'Please Enter PT Number',
  'ptloc'=>'Please Enter Work Location',
  'ptstate'=>'Please Select State',
  'ptdeduct'=>'Please Select PT Deduction Cycle',
  'lwfrs'=>'Please Enter Contribution Amount',
  'lwfloc'=>'Please Select State',
  'lwfdeduct'=>'Please Select Deduction Cycle',
  'compname' =>'Please Enter Component Name',
  'comptype' =>'Please Enter Component Type',
  'compdisplay' =>'Please Enter Name In Payslip',
  'compamt'=>'Please Enter Amount',
  'compcat'=>'Please Select Component Category'
);
$placeholder= array('location'=>'London','org_name'=>'Infisuite Technologies','phone'=>'4547987440','email'=>'hello@infisuite.com','pincode'=>'641004','dateformat'=>'YYYY-MM-DD','licensenumber'=>'U72200MH2009PLC123456','pan'=>'AAAPZ1234C','tan'=>'PDES03028F','gst'=>'04AABCU9603R1ZV', 'cin'=>'L21091KA2019OPC141331','epf'=>'TN/MAS/0054055/0000175','epfempc'=>'8.33','epfemprc'=>'3.67','esi'=>'31–00–123456–000–0001','esiempc'=>'8.33','esiemprc'=>'3.67','pt'=>'27123456789P','ptloc'=>'Bangalore','lwfrs'=>'10.00','compname'=>'BASIC PAY','comptype'=>'BASIC','compdisplay'=>'FULL BASIC','dcompname'=>'PROFESSIONAL TAX','dcomptype'=>'PTAX','dcompdisplay'=>'PROFESSIONAL TAX','rcompname'=>'FUEL REIMBURSEMENT','rcomptype'=>'FUELREIM','rcompdisplay'=>'FUEL REIMBURSEMENT','compamt'=>'5000.00');
//$dateformatlist = array('YYYY-MM-DD','YYYY/MM/DD','DD-MM-YYYY','MM-DD-YYYY','MM/DD/YYYY','DD/MM/YYYY');

$dateformatlist = array('YYYY MM DD','DD MM YYYY','MM DD YYYY');
$datesparatelist = array("-","/"); 
$url = API."?mode=getconfigsettings";
$response = callAPI($url);
$setup_details = json_decode($response,1);
$countrieslist = $setup_details['countries'];
$currencies = $setup_details['currencies'];
  // print_r($countrieslist); exit;
$statelist = $setup_details['statelist'];
$earnings_componentlist = $setup_details['earnings_componentlist'];
$deduction_componentlist = $setup_details['deduction_componentlist'];
$other_earnings_componentlist = $setup_details['other_earnings_componentlist'];
$salary_categories = $setup_details['salary_categories'];
$role_list = $setup_details['roles'];
$holidays_list = $setup_details['holidays'];
$process_list = $setup_details['ess_process'];
$users_list = $setup_details['application_users'];
$emp_list = $setup_details['employees'];
$deduction_cycle = array('M'=>'Monthly','Q'=>'Quaterly','H'=>'Half Yearly','Y'=>'Yearly');
$industrylist = $setup_details['industries'];
if(isset($_POST['Save'])){
  if(!empty($_FILES["display_logo"]["name"])){
    $temp = explode(".", $_FILES["display_logo"]["name"]);
    $newfilename = 'Logo.' . end($temp);
    $_POST['display_logo'] = $newfilename;
    move_uploaded_file($_FILES["display_logo"]["tmp_name"], "uploads/all/" . $newfilename);
  }
  if($_POST['payroll_day']!='0' || $_POST['payroll_day']=='' ){
   $_POST['default_payroll_day'] = '31';
 } 
 $_POST['esi_employer_contribution_in_ctc'] = '0';
 $_POST['epf_employer_contribution_in_ctc'] = '0';
 if($_POST['esi_employer_contribution_in_ctc']){
   $_POST['esi_employer_contribution_in_ctc'] = '1';
 }
 if($_POST['epf_employer_contribution_in_ctc']){
   $_POST['epf_employer_contribution_in_ctc'] = '1';
 }
 if($_POST['allow_override']){
  $_POST['allow_override']='0';
}else{
  $_POST['allow_override']='1';
}
$_POST['selectedcomponentlist'] = array_merge($_POST['selectedcomponentlist1'],$_POST['selectedcomponentlist2']);
$_POST['selectedcomponents'] = array_merge($_POST['selectedcomponentlist1'],$_POST['selectedcomponentlist2']);
$_POST['selected_other_earnings'] = $_POST['selectedcomponentlist3'];
  //  echo "<pre>"; print_r($_POST);  //exit;
$url = API."?mode=setuphr&method=post";
$response = file_post_contents($url, $_POST, 'insert');
header('Location:index.php?option=COM_INSERT_HRSETUP');
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
  <title>Infistaq | Home</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Admindek Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
  <meta name="keywords" content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
  <meta name="author" content="colorlib" />
  <!-- Favicon icon -->
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
  <!-- Required Fremwork -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
  <!-- waves.css -->
  <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all">
  <!-- feather icon -->
  <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
  <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
  <!-- ico font -->
  <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
  <!-- Data Table Css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/buttons/css/buttons.dataTables.min.css">
  <!-- font-awesome-n -->
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome-n.min.css">
  <!-- Chartlist chart css -->
  <link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
  <!--Rohan  -->
  <!-- Bootstrap date-time-picker js -->
  <link rel="stylesheet" type="text/css" href="assets/pages/advance-elements/css/bootstrap-datetimepicker.css">
  <!-- Date-range picker css  -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-daterangepicker/css/daterangepicker.css" />
  <!-- Date-Dropper css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datedropper/css/datedropper.min.css" />
  <link rel="stylesheet" href="assets/bower_components/select2/css/select2.min.css" />
  <!-- Multi Select css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css" />
  <link rel="stylesheet" type="text/css" href="assets/bower_components/multiselect/css/multi-select.css" />

  <!-- Color Picker css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/spectrum/css/spectrum.css" />
  <!-- Mini-color css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-minicolors/css/jquery.minicolors.css" />
  <!-- Switch component css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/switchery/css/switchery.min.css">
  <!-- Tags css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
  <!-- animation nifty modal window effects css -->
  <link rel="stylesheet" type="text/css" href="assets/css/component.css">
  <!-- Calender css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.print.css" media='print'>
  <!--Rohan -->
  <!-- Style.css -->
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="assets/css/pages.css">


  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">

<link rel="stylesheet" type="text/css" href="assets/pages/notification/notification.css">

  <!--forms-wizard css-->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery.steps/css/jquery.steps.css">

  <style>
   .content.clearfix {
      background-color: white;
      overflow-y: scroll;
      height: 500px;
      width: 75% !important; 
    }
    .steps.clearfix {
      width: 20% !important;
    }
    .text-muted{
      display: none;
    }
.wizard > .content > .body label.error {
  margin-left: 0px !important;
}
  </style>
  <style type="text/css">
    .setup-page{ padding: 0 !important;  }
    .accbora{border: 1px solid #3699ff !important; border-radius: 5px;
      }}
    </style>

  </head>

  <script type="text/javascript">
    function curreniceconvetor(){
      var dcurr =$('#currency').val();
      jQuery.ajax({
            url: "views/custom_insert_currency_convertor_popup.php",
            data:'table=currency_convertor&dcurr='+dcurr,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function selectedcategory(selected_id,element_id,table_name,field_name) {
      var selectedvalues = selected_id.split("|");

      if(table_name=='states'){
        document.getElementById('selectedcountry').value= selectedvalues[1];
      }
      if(table_name=='cities'){
        document.getElementById('selectedstate').value= selectedvalues[1];
      }
      if(table_name=='city'){
        document.getElementById('selectedcity').value= selectedvalues[1];
      }
      console.log('page=getselectedlist&table_name='+table_name+'&id='+selectedvalues[0]+'&field='+field_name);
      if(table_name=='states' || table_name=='cities'){
       $.ajax({
        url: "ajax.php",
        data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues[0]+'&field='+field_name,
        type: "POST",
        success: function(response) {
          console.log(response);
          var response = JSON.parse(response);
          console.log(response.length);
          var select = document.getElementById(element_id); 
          document.getElementById(element_id).innerHTML = null;
          for (var i = 0; i < response.length; i++) 
          {
           var el = document.createElement("option");
           if(table_name=='states'){
            el.textContent = response[i].state_name;
            el.value = response[i].id+'|'+response[i].state_name;
          }
          if(table_name=='cities'){
            el.textContent = response[i].city_name;
            el.value = response[i].id+'|'+response[i].city_name;
          }
          select.add(el);

          console.log(el);
        }
      }
    });
     }
     if(table_name=='states'){
      console.log("currency_"+selectedvalues[0]);
      //document.getElementById("currency_"+selectedvalues[0]).selected = "true";
        //$('#currency option[value=93]').attr('selected','selected');
        //$('#currency option[id=currency_101]').attr('selected','selected');
        //$("#currency").select2("val", "93");
        //$("#currency").select2().select2('id','currency_'+selectedvalues[0]);
        //$("#currency").select2().select2('val','93');
        var curr = 93;
        var timz ="Asia/Kolkata";
        $('#currency').val(curr).select2();
        $('#timezoneid').val(timz).select2();
      }
   }
   function getcountrytimezone(selected_id,element_id,table_name,field_name){
      console.log("----- Data ---------------------");
      var selectedvalues = selected_id.split("|");
      //document.getElementById('selectedcountry').value= selectedvalues[1];
      var select = document.getElementById(element_id);
      $.ajax({
            url: "ajax.php",
            data:'page=togettimezonecountries&table_name='+table_name+'&id='+selectedvalues[0]+'&field='+field_name,
            type: "POST",
            success: function(response) {
              console.log('checkkk');
              console.log(response);
             /* var response = JSON.parse(response);
              console.log(response.length)*/;
              var select = document.getElementById(element_id); 
              document.getElementById(element_id).innerHTML = null;
              select.add(response);
              console.log(response);
          }
      });
      console.log("----- Data end  ---------------------");
    }
   function listindiastates(country_name,element_id,table_name,selectedvalue,field_name) {

     $.ajax({
      url: "ajax.php",
      data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalue+'&field='+field_name,
      type: "POST",
      success: function(response) {
        var response = JSON.parse(response);
        console.log(response.length);
        var select = document.getElementById(element_id); 
        document.getElementById(element_id).innerHTML = null;
        for (var i = 0; i < response.length; i++) 
        {
         var el = document.createElement("option");
         if(table_name=='states'){
          el.textContent = response[i].state_name;
          el.value = response[i].id+'|'+response[i].state_name;

        }
        select.add(el);
      }
    }
  });

   }
   function setenable(section) {
    if(section=='epf'){
      if(document.getElementById('pf_checked').value=='0'){
       document.getElementById('pf_checked').value = 1;
       document.getElementById("epf").className = 'btn btn-primary';
     }
     else if(document.getElementById('pf_checked').value=='1'){
       document.getElementById("epf").className = 'btn btn-light';
       document.getElementById('pf_checked').value = 0;
     }
   }
   if(section=='esi'){
    if(document.getElementById('esi_checked').value=='0'){
     document.getElementById('esi_checked').value = 1;
     document.getElementById("esi").className = 'btn btn-primary';
   }
   else if(document.getElementById('esi_checked').value=='1'){
     document.getElementById("esi").className = 'btn btn-light';
     document.getElementById('esi_checked').value = 0;
   }
             //  alert(section);
           }
           if(section=='pt'){
             if(document.getElementById('pt_checked').value=='0'){
               document.getElementById('pt_checked').value = 1;
               document.getElementById("pt").className = 'btn btn-primary';
             }
             else if(document.getElementById('pt_checked').value=='1'){
               document.getElementById("pt").className = 'btn btn-light';
               document.getElementById('pt_checked').value = 0;
             }
             //  alert(section);
           }
           if(section=='lwf'){
             if(document.getElementById('lwf_checked').value=='0'){
               document.getElementById('lwf_checked').value = 1;
               document.getElementById("lwf").className = 'btn btn-primary';
             }
             else if(document.getElementById('lwf_checked').value=='1'){
               document.getElementById("lwf").className = 'btn btn-light';
               document.getElementById('lwf_checked').value = 0;
             }
              // alert(section);
            }
          }

          function showreimburse(popup_function_name,id,table_name) {
         //  alert(id);
         document.getElementById('reimburse_popup').style.display = "block";
         document.getElementById('earnings_popup').style.display = "none";
         $.ajax({
          url: "ajax.php",
          data:'page=getcomponents&table_name='+table_name+'&id='+id,
          type: "POST",
          success: function(response) {
           console.log(response);
           document.getElementById('pop_table_name').value = table_name;

           if(id!='') {
             console.log(response);
             response=response.replace("[","");
             response=response.replace("]","");
             var result = JSON.parse(response);
             console.log("response ="+result.id);
             document.getElementById('updaterowid').value = result.id;
             document.getElementById('reimpop_code').value = result.system_code;
             document.getElementById('reimpop_name').value = result.earning_name;
             document.getElementById('reimpop_payslip_name').value = result.shown_label_onpayslip;
             document.getElementById('reimpop_partofctc').checked = result.part_of_ctc=='0'?true:false;
             document.getElementById('reimpop_calculation_number').value = result.earning_upto_amount;
           } else{
            document.getElementById('updaterowid').value = "";
            document.getElementById('reimpop_code').value = "";
            document.getElementById('reimpop_name').value = "";
            document.getElementById('reimpop_payslip_name').value = "";
            document.getElementById('reimpop_partofctc').checked =false;
            document.getElementById('reimpop_calculation_number').value = "";
          }
          var division = document.getElementById('ctype');
          division.innerHTML = popup_function_name;
          $('#exampleModal').modal('show');

        }
      });
       }

       function showpopup(popup_function_name,id,table_name){
         //  alert(popup_function_name+id+table_name);
         document.getElementById('earnings_popup').style.display = "block";
         document.getElementById('reimburse_popup').style.display = "none";
         $.ajax({
          url: "ajax.php",
          data:'page=getcomponents&table_name='+table_name+'&id='+id,
          type: "POST",
          success: function(response) {
            document.getElementById('pop_table_name').value = table_name;

            if(id!='') {
             console.log(response);
             response=response.replace("[","");
             response=response.replace("]","");
             var result = JSON.parse(response);
             console.log("response ="+result.code);
             document.getElementById('updaterowid').value = id;
             document.getElementById('pop_code').value = result.code;
             document.getElementById('pop_name').value = result.name;
             document.getElementsByClassName('kt_select2_46').value = result.category_id;
             document.getElementById('pop_payslip_name').value = result.payslip_name;
             document.getElementById('pop_pro_rata').checked = result.pro_rata=='0'?true:false;
             document.getElementById('pop_component_paytype1').checked = result.varible_pay=='1'?true:false;
             document.getElementById('pop_component_paytype2').checked = result.varible_pay=='0'?true:false;
             document.getElementById('pop_calculation_type1').checked = result.calculation_type=='A'?true:false;
             document.getElementById('pop_calculation_type2').checked = result.calculation_type=='P'?true:false;
             document.getElementsByClassName('kt_select2_47').value = result.calculation_category;
             document.getElementById('pop_calculation_number').value = result.calculation_number;
           }
           else{
             document.getElementById('updaterowid').value = "";
             document.getElementById('pop_code').value = "";
             document.getElementById('pop_name').value = "";
             document.getElementsByClassName('kt_select2_46').value = "";
             document.getElementById('pop_payslip_name').value = "";
             document.getElementById('pop_pro_rata').checked = false;
             document.getElementById('pop_component_paytype1').checked = false;
             document.getElementById('pop_component_paytype2').checked = false;
             document.getElementById('pop_calculation_type1').checked = false;
             document.getElementById('pop_calculation_type2').checked = false;
             document.getElementsByClassName('kt_select2_47').value = "";
             document.getElementById('pop_calculation_number').value = "";
           }
           if(popup_function_name=='Earning'){
             document.getElementById('pop_calculation_mode').value = 'ADD';
           }else {
             document.getElementById('pop_calculation_mode').value = 'MINUS';
           }
           var division = document.getElementById('ctype');
           division.innerHTML = popup_function_name;
           $('#exampleModal').modal('show');
         }
       });
       }
       function submitpopup(){
         var table_name = document.getElementById('pop_table_name').value;
         if(table_name!='system_other_earnings') {
           var updaterowid = document.getElementById('updaterowid').value;
           var pop_calculation_mode = document.getElementById('pop_calculation_mode').value;
           var paytype = document.getElementById('pop_component_paytype1').checked ? 1 : document.getElementById('pop_component_paytype2').checked? 0 : 0;
           var pro_rata =document.getElementById('pop_pro_rata').checked?0:1;
           var pop_calculation_type =document.getElementById('pop_calculation_type1').checked?'A':document.getElementById('pop_calculation_type2').checked?'P':'N';
           var obj='&company_id=1'+'&name='+document.getElementById('pop_name').value+'&'+
           'code='+document.getElementById('pop_code').value+'&'+
           'category_id='+document.getElementsByClassName('kt_select2_46').value+'&'+
           'payslip_name='+document.getElementById('pop_payslip_name').value+'&'+
           'pro_rata='+pro_rata+'&'+
           'varible_pay='+paytype+'&'+
           'calculation_type='+pop_calculation_type+'&'+
           'calculation_number='+document.getElementById('pop_calculation_number').value+'&'+
           'calculation_category='+document.getElementsByClassName('kt_select2_47').value+'&'+
           'calculation_mode='+document.getElementById('pop_calculation_mode').value;
         }else {
           var updaterowid = document.getElementById('updaterowid').value;
           var part_of_ctc =document.getElementById('reimpop_partofctc').checked?0:1;
           var obj='&company_id=1'+'&earning_name='+document.getElementById('reimpop_name').value+'&'+
           'system_code='+document.getElementById('reimpop_code').value+'&'+
           'shown_label_onpayslip='+document.getElementById('reimpop_payslip_name').value+'&'+
           'part_of_ctc='+part_of_ctc+'&'+
           'earning_upto_amount='+document.getElementById('reimpop_calculation_number').value;

         }
         console.log(obj);
         //    alert(updaterowid);

         $.ajax({
           url: "ajax.php",
           data:'page=setupcomponents&table_name='+table_name+'&id='+updaterowid+obj,
           type: "POST",
           success: function(response) {
             console.log("response ="+response);

             response=response.replace("[","");
             response=response.replace("]","");
             var response = JSON.parse(response);

             updaterowid = response.LASTID==0?updaterowid:response.LASTID;
             console.log("updaterowid ="+updaterowid);

             $('#exampleModal').modal('hide');
             if(table_name=='system_other_earnings') {
               var funname ='Reimbursement';
               if(response.LASTID!=0){
                document.getElementById("kt_tab_pane_3").innerHTML += '<div class="row" id="rcomponentid_'+updaterowid+'">'+
                '<div class="col-xl-5">  <label class="checkbox checkbox-primary" style="float: left;">  <input type="checkbox" checked name="selectedcomponentlist3[]" > <span></span>  </label> &nbsp; <a href="#"  onclick="showreimburse(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('reimpop_name').value+'</label> </a></div>  <div class="col-xl-2"><label>'+document.getElementById('reimpop_code').value+'</label></div><div class="col-xl-5"><label>'+document.getElementById('reimpop_calculation_number').value+'</label></div></div>';
              }else{

                document.getElementById("rcomponentid_"+updaterowid).innerHTML = '<div class="col-xl-5"> <label class="checkbox checkbox-primary" style="float: left;">  <input type="checkbox" checked name="selectedcomponentlist3[]" > <span></span>  </label> &nbsp;<a href="#"  onclick="showreimburse(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('reimpop_name').value+'</label> </a></div>  <div class="col-xl-2"><label>'+document.getElementById('reimpop_code').value+'</label></div><div class="col-xl-5"><label>'+document.getElementById('reimpop_calculation_number').value+'</label></div>';
              }
            } 
            else {
             var newcolum="";

             if(document.getElementById('pop_calculation_mode').value=='ADD'){
               var funname ='Earning';
               if(response.LASTID!=0){
                 newcolum= '<div class="row" id="componentid_'+updaterowid+'">'+
                 '<div class="col-xl-5 "><label class="checkbox checkbox-primary" style="float: left;"> <input type="checkbox" checked name="selectedcomponentlist1[]" /><span></span>  </label> &nbsp;<a href="#" onclick="showpopup(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('pop_name').value+'</label></a></div><div class="col-xl-2"> <label>'+document.getElementById('pop_code').value+'</label></div><div class="col-xl-5"> <label>';
                 paytype=='0' ?  newcolum+='Variable;':  newcolum+='Fixed;';
                 if(pop_calculation_type='A') {
                  newcolum+= 'Flat Amount';
                  document.getElementById('pop_calculation_number').value==''?'': newcolum+=' of '+document.getElementById('pop_calculation_number').value;
                }else{
                  if(pop_calculation_type='P') {
                    newcolum+=document.getElementById('pop_calculation_number').value+' % of '+document.getElementById('kt_select2_47').value;
                  }else{
                    newcolum+=' of '+document.getElementById('pop_calculation_number').value;
                  }
                }
                newcolum+='</label></div> </div>';

                document.getElementById("kt_tab_pane_1").innerHTML +=newcolum;

              }else{
               newcolum = '<div class="col-xl-5 "><label class="checkbox checkbox-primary" style="float: left;"> <input type="checkbox" checked name="selectedcomponentlist1[]" /><span></span>  </label> &nbsp;<a href="#" onclick="showpopup(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('pop_name').value+'</label></a></div><div class="col-xl-2"> <label>'+document.getElementById('pop_code').value+'</label></div><div class="col-xl-5"> <label>';
               paytype=='0' ?  newcolum+='Variable;':  newcolum+='Fixed;';
               if(pop_calculation_type='A') {
                newcolum+= 'Flat Amount';
                document.getElementById('pop_calculation_number').value==''?'': newcolum+=' of '+document.getElementById('pop_calculation_number').value;
              }else{
                if(pop_calculation_type='P') {
                  newcolum+=document.getElementById('pop_calculation_number').value+' % of '+document.getElementById('kt_select2_47').value;
                }else{
                  newcolum+=' of '+document.getElementById('pop_calculation_number').value;
                }
              }
              newcolum+='</label></div>';
              document.getElementById("componentid_"+updaterowid).innerHTML =newcolum;
            }
          }else{
           //onclick="showpopup('Earning',updaterowid,'earnings');
           var funname ='Deduction';
           var newcolum="";
           if(response.LASTID!=0){
             //onclick="showpopup('Earning','<?php echo $scvalue['id'];?>','earnings');
             newcolum= '<div class="row" id="componentid_'+updaterowid+'">'+
             '<div class="col-xl-5 "><label class="checkbox checkbox-primary" style="float: left;"> <input type="checkbox" checked name="selectedcomponentlist2[]" /> <span></span>  </label> &nbsp;<a href="#" onclick="showpopup(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('pop_name').value+'</label></a></div><div class="col-xl-2"> <label>'+document.getElementById('pop_code').value+'</label></div><div class="col-xl-5"> <label>';
             paytype=='0' ?  newcolum+='Variable;':  newcolum+='Fixed;';
             if(pop_calculation_type='A') {
              newcolum+= 'Flat Amount';
              document.getElementById('pop_calculation_number').value==''?'': newcolum+=' of '+document.getElementById('pop_calculation_number').value;
            }else{
              if(pop_calculation_type='P') {
                newcolum+=document.getElementById('pop_calculation_number').value+' % of '+document.getElementById('kt_select2_47').value;
              }else{
                newcolum+=' of '+document.getElementById('pop_calculation_number').value;
              }
            }
            newcolum+='</label></div> </div>';

            document.getElementById("kt_tab_pane_2").innerHTML +=newcolum;

          }else{
           newcolum = '<div class="col-xl-5 "><label class="checkbox checkbox-primary" style="float: left;"> <input type="checkbox" checked name="selectedcomponentlist2[]" /><span></span>  </label> &nbsp;<a href="#" onclick="showpopup(\''+funname+'\',\''+updaterowid+'\',\''+table_name+'\')"><label>'+document.getElementById('pop_name').value+'</label></a></div><div class="col-xl-2"> <label>'+document.getElementById('pop_code').value+'</label></div><div class="col-xl-5"> <label>';
           paytype=='0' ?  newcolum+='Variable;':  newcolum+='Fixed;';
           if(pop_calculation_type='A') {
            newcolum+= 'Flat Amount';
            document.getElementById('pop_calculation_number').value==''?'': newcolum+=' of '+document.getElementById('pop_calculation_number').value;
          }else{
            if(pop_calculation_type='P') {
              newcolum+=document.getElementById('pop_calculation_number').value+' % of '+document.getElementById('kt_select2_47').value;
            }else{
              newcolum+=' of '+document.getElementById('pop_calculation_number').value;
            }
          }
          newcolum+='</label></div>';
          document.getElementById("componentid_"+updaterowid).innerHTML =newcolum;
        }
      }
    }
    document.getElementById('updaterowid').value ="";
  }      
});
}
function  removeslabrows(argument) {
 $(".itemRow:checked").each(function() {
   console.log(this);
   $(this).closest('tr').remove();
 });
 $('#checkAll').prop('checked', false);
}
function addslabrows(){
                // var count = $(".itemRow").length;
                //  count++;
                jQuery(document).ready(function() {    
                 KTBootstrapDatepicker.init();
               });
                var htmlRows = '';
                htmlRows += '<tr>';
                htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input type="checkbox" value="" class="itemRow d-none"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td>';
                htmlRows += '<td>       <input type="text" class="form-control"   autocomplete="off" name="start_range[]" >  </td><td><input type="text" class="form-control"  autocomplete="off" name="end_range[]" ></td>  <td><input class="form-control"   autocomplete="off" type="text" name="tax_amount[]" > </td>';   
                htmlRows += '</tr>';
                $('#slabtable').append(htmlRows);
              }


            </script>
            <style>
              .select2-selection__choice{
                margin: 2px;
              }
            </style>
          </head>
          <!--end::Head-->
          <!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
  <div id="kt_header_mobile" class="header-mobile align-items-center  header-mobile-fixed " style="background-color: white;">
    <a href="index.php"> <img alt="Logo" src="assets/images/logo.png"  class="img-100"/> </a>
  </div>

  <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class=" flex-column-fluid">
     <div class="card card-custom">
      <div class="card-body p-0">
        <div class="card ">
          <div class="card-block j-pro">
            <div class="row j-content">
              <div class="col-md-12">
                <div id="wizard">
                  <section>
                    <form class="wizard-form" id="example-advanced-form_new"  method="post" action="" enctype="multipart/form-data">
                     <input type="hidden" name="Save" value="Save">
                      <h3> Organisation Setup </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                          <h3 class="font-weight-bold text-dark">Let's get started</h3>
                          <div class="mb-10 form-text text-muted">Tell us a bit about your organisation.</div>
                          <div class="form-group row">
                            <div class="form-group col-lg-8">
                              <label class="form-label block">Organisation Name <span class="requried">*</span></label>
                              <div class="input-group input-icon-right">
                               <input type="text" class="form-control required" name="company_name" placeholder="<?php echo $placeholder['org_name'];?>" >
                             </div>
                           </div>
                           <div class="form-group col-lg-4">
                             <label class="ml-5">Logo</label>
                             <div class="col-lg-9 col-xl-6">
                              <div class="profile-pic">
                                <label class="-label" for="display_logo">

                                  <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                </label>
                                <input  type="file" name="display_logo" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                <input type="hidden" name="logo_remove" >

                                <img src="<?php echo $bimage ; ?>" id="outputdisplay_logo" width="200"  onerror="this.src='uploads/all/no-image.jpg';"/>
                              </div>
                            </div>  
                            <span class="form-text text-muted"><?php //echo $helptext['logo'];?> </span>
                          </div> 
                          <div class="form-group col-lg-6">
                            <label>Location</label>
                            <div class="input-group input-icon-right">
                              <input type="text"  class="form-control " name="location" placeholder="<?php echo $placeholder['location'];?>" >
                            </div>                                                   
                          </div>
                          <div class="form-group col-lg-6">
                            <label>Industry</label>
                            <div class="input-group input-icon-right">
                              <select class="form-select js-example-basic-single"  name="industry">
                                <option value="">Select Any One </option>
                                <?php foreach($industrylist as $valueindustry) { ?>
                                  <option value="<?php echo $valueindustry['id'];?>"><?php echo $valueindustry['industries']; ?></option>
                                <?php } ?>
                              </select> 
                            </div>
                          </div>
                          <div class="form-group col-lg-6">
                            <label>Phone <span class="requried">*</span></label>
                            <div class="input-group input-icon-right">
                             <input type="text"  name="phone_number" class="form-control required" placeholder="<?php echo $placeholder['phone'];?>">
                           </div>
                         </div>
                         <div class="form-group col-lg-6">
                          <label>Email <span class="requried">*</span></label>
                          <div class="input-group input-icon-right">
                            <input type="text"  name="admin_email" class="form-control required" placeholder="<?php echo $placeholder['email'];?>" >
                          </div>
                        </div>
                        <div class="form-group col-lg-12">
                          <label>Address</label>
                          <textarea type="text" class="form-control"  name="address" ></textarea>
                        </div>
                        <div class="form-group col-lg-6">
                          <label>Country</label>
                          <input type="hidden" name="country" id="selectedcountry">
                          <input type="hidden" name="state" id="selectedstate">
                          <input type="hidden" name="city" id="selectedcity">
                          <div class="input-group input-icon-right">
                            <select class="form-select js-example-basic-single" class="form-control" id="selectedcountry" onchange="selectedcategory(this.value,'kt_select2_51','states','country_id')">
                              <option value="">Select Any One</option>
                              <?php foreach($countrieslist as $countryvalue) { ?>
                                <option value="<?php echo $countryvalue['id'].'|'.$countryvalue['country_name'];?>"><?php echo $countryvalue['country_name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group col-lg-6">
                          <label>State</label>
                          <div class="input-group input-icon-right">
                            <select class="form-select js-example-basic-single" class="form-control" id="kt_select2_51" onchange="selectedcategory(this.value,'kt_select2_33','cities','state_id')">
                              <option value="">Select Any One</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group col-lg-6">
                          <label>City</label>
                          <div class="input-group input-icon-right">
                            <select class="form-select js-example-basic-single" class="form-control" id="kt_select2_33" onchange="selectedcategory(this.value,'','city','')">
                              <option value="">Select Any One</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group col-lg-6">
                          <label>Pincode <span class="requried">*</span></label>
                          <div class="input-group input-icon-right">
                            <input type="text" class="form-control required"  name="pincode" placeholder="<?php echo $placeholder['pincode'];?>">
                          </div>
                        </div>
                        <!-- <div class="form-group col-lg-4">
                          <label>Date Format</label>
                          <div class="input-group input-icon-right">
                            <select class="form-select js-example-basic-single"  name="date_format">
                              <option value="">Select Any One </option>
                              <?php foreach($dateformatlist as $valuedates) { ?>
                                <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
                              <?php } ?>
                            </select> 
                          </div>
                        </div>
                        <div class="form-group col-lg-4">
                          <label>Date Format</label>
                          <div class="input-group input-icon-right">
                            <select class="form-select js-example-basic-single"  name="date_format">
                              <option value="">Select Any One </option>
                              <?php foreach($dateformatlist as $valuedates) { ?>
                                <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
                              <?php } ?>
                            </select> 
                          </div>
                        </div> -->
                        <!-- <div class="form-group col-lg-4">
                          <label>Default KPI Weekly Task Day</label>
                          <select class="form-select js-example-basic-single"  name="default_kpi_weekly_task_day">
                            <option value="">Select Any One</option>
                            <option value="Mon">Mon</option>
                            <option value="Tue">Tue</option>
                            <option value="Wed">Wed</option>
                            <option value="Thu">Thu</option>
                            <option value="Fri">Fri</option>
                            <option value="Sat">Sat</option>
                            <option value="Sun">Sun</option>
                          </select>
                        </div>
                        <div class="form-group col-lg-4">
                          <label>Default KPI Monthly Task Day</label>
                          <select  class="form-select js-example-basic-single selectlist "  name="default_kpi_monthly_task_day">
                            <?php for($i=1; $i<=31; $i++) { ?>
                              <option value="<?php echo $i;?>">&nbsp;&nbsp;<?php echo $i; ?>&nbsp;&nbsp;</option>
                            <?php } ?>                                                 
                          </select>
                        </div> -->
                      </fieldset>
                      <h3> System Formats </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="form-group row">
                            <div class="form-group col-lg-6 w-full">
                              <label>Date Sapertor</label>
                              <div class="input-group input-icon-right">
                                <select class="form-select js-example-basic-single"  name="date_separator">
                                  <?php foreach($datesparatelist as $sapertor) { ?>
                                    <option value="<?php echo $sapertor;?>"><?php echo $sapertor; ?></option>
                                  <?php } ?>
                                </select> 
                              </div>
                            </div>
                            <div class="form-group col-lg-6 w-full">
                              <label>Date Format</label>
                              <div class="input-group input-icon-right">
                                <select class="form-select js-example-basic-single" class="form-control"  name="date_format">
                                  
                                  <?php foreach($dateformatlist as $valuedates) { ?>
                                    <option value="<?php echo $valuedates;?>"><?php echo $valuedates; ?></option>
                                  <?php } ?>
                                </select> 
                              </div>
                            </div>
                            
                            <div class="form-group col-lg-6 w-full">
                              <label>Time Zone</label>
                              <div class="input-group input-icon-right">
                                <select class="form-select js-example-basic-single" id="timezoneid" name="time_zone">
                                  <option value="">Select Any One </option>
                                  <?php 
                                    foreach($countrieslist as $tzlist1){
                                     $cc=1; $timezonelists = json_decode($tzlist1['time_zones'],1);
                                      foreach($timezonelists as $timezonelists1){ ?>
                                        <option id="tzlist1_<?php echo $cc."_".$tzlist1['id'];?>" value="<?php echo $timezonelists1['zoneName']; ?>"><?php echo $timezonelists1['zoneName'];?></option>
                                     <?php $cc++; }
                                    } 
                                  ?>
                                </select> 
                              </div>
                            </div>
                            <div class="form-group col-lg-6 w-full">
                              <label>Currency</label>
                              <div class="input-group input-icon-right">
                                <select class="form-select js-example-basic-single" id="currency" class="form-control"  name="date_format">
                                  
                                  <?php foreach($currencies as $currencie) { ?>
                                    <option id="currency_<?php echo $currencie['country_id'];?>" value="<?php echo $currencie['id'];?>"><?php echo $currencie['currency_name']; ?></option>
                                  <?php } ?>
                                </select> 
                              </div>
                            </div>
                            <div class="form-group col-lg-6 w-full">
                               <label>To Add Currency Convetor</label>
                               <div>
                               <button type="button" onclick="curreniceconvetor()" class="btn btn-primary">Convetor</button>
                               </div>
                            </div>
                         </div>
                        </div>
                      </fieldset>
                      <h3> Attendance  </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="form-group row">
                            <div class="form-group col-lg-12">
                              <label>Select days you worked in a calendar week.</label>
                              <select class="selectlist form-select js-example-basic-multiple"  name="working_days[]" multiple="multiple">
                                <option value="Mon" selected>Mon</option>
                                <option value="Tue" selected>Tue</option>
                                <option value="Wed" selected>Wed</option>
                                <option value="Thu" selected>Thu</option>
                                <option value="Fri" selected>Fri</option>
                                <option value="Sat">Sat</option>
                                <option value="Sun">Sun</option>
                              </select>
                            </div>
                            <div class="form-group col-lg-4">
                              <label>Minimum Working Hours In A Day <span class="requried">*</span></label>
                              <div class="input-group input-icon-right">
                                <input type="time" class="form-control"  id="kt_timepicker_2"  name="minimum_hours_required" placeholder="0:00:00">
                              </div>
                            </div>
                            <div class="form-group col-lg-4">
                             <label>Half Day Working Hours</label>
                             <div class="input-group input-icon-right">
                              <input type="time" class="form-control" id="kt_timepicker_2"  name="half_day_hours" placeholder="0:00:00" >
                            </div>
                          </div>
                          <div class="form-group col-lg-4">
                            <label>Grace Period</label>
                            <div class="input-group input-icon-right">
                              <input type="time" class="form-control"  id="kt_timepicker_2"  name="grace_perieod" placeholder="0:00:00" >
                            </div>
                          </div>
                         </div>
                        </div>
                      </fieldset>
                      <h3> Time Sheet  </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="form-group row">
                            <div class="form-group col-lg-6">
                              <label>Default Timesheet Date</label>
                              <div class="input-group input-icon-right">
                              <select class="selectlist form-select js-example-basic-single w-full"  name="working_days[]" >
                                <?php for($i=1; $i<32; $i++){ ?>
                                  <option><?php echo $i; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                            </div>
                            
                         </div>
                        </div>
                      </fieldset>
                       <h3> PMS  </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="form-group row">
                            <div class="form-group col-lg-6">
                          <label>Default KPI Weekly Task Day</label>
                          <div class="input-group input-icon-right">
                          <select class="form-select js-example-basic-single w-full"  name="default_kpi_weekly_task_day">
                            <option value="">Select Any One</option>
                            <option value="Mon">Mon</option>
                            <option value="Tue">Tue</option>
                            <option value="Wed">Wed</option>
                            <option value="Thu">Thu</option>
                            <option value="Fri">Fri</option>
                            <option value="Sat">Sat</option>
                            <option value="Sun">Sun</option>
                          </select>
                        </div>
                        </div>
                        <div class="form-group col-lg-6">
                          <label>Default KPI Monthly Task Day</label>
                          <div class="input-group input-icon-right">
                          <select  class="form-select js-example-basic-single selectlist w-full"  name="default_kpi_monthly_task_day">
                            <?php for($i=1; $i<=31; $i++) { ?>
                              <option value="<?php echo $i;?>">&nbsp;&nbsp;<?php echo $i; ?>&nbsp;&nbsp;</option>
                            <?php } ?>                                                 
                          </select>
                        </div>
                        </div> 
                            
                         </div>
                        </div>
                      </fieldset>
                       <h3> Food Option  </h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="form-group row">
                            <div class="form-group col-lg-4">
                          <label>Default KPI Weekly Task Day</label>
                          <select class="form-select js-example-basic-single"  name="default_kpi_weekly_task_day">
                            <option value="">Select Any One</option>
                            <option value="Mon">Mon</option>
                            <option value="Tue">Tue</option>
                            <option value="Wed">Wed</option>
                            <option value="Thu">Thu</option>
                            <option value="Fri">Fri</option>
                            <option value="Sat">Sat</option>
                            <option value="Sun">Sun</option>
                          </select>
                        </div>
                        <div class="form-group col-lg-4">
                          <label>Default KPI Monthly Task Day</label>
                          <select  class="form-select js-example-basic-single selectlist "  name="default_kpi_monthly_task_day">
                            <?php for($i=1; $i<=31; $i++) { ?>
                              <option value="<?php echo $i;?>">&nbsp;&nbsp;<?php echo $i; ?>&nbsp;&nbsp;</option>
                            <?php } ?>                                                 
                          </select>
                        </div> 
                            
                         </div>
                        </div>
                      </fieldset>
                      <h3> Tax Information e</h3>
                      <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <h4 class="font-weight-bold text-dark">Provide your organization's tax information</h4>
                          <div class="mb-10 form-text text-muted">There tax details will be displayed in your emplyee's Form 16.</div>
                            <div class="form-group row">
                              <div class="form-group col-lg-6">
                               <label>License Number<span class="requried">*</span></label>
                               <div class="input-group input-icon-right">
                                <input type="text" class="form-control required"  name="license_number" placeholder="<?php echo $placeholder['licensenumber'];?>">
                              </div>
                            </div>
                            <div class="form-group col-lg-6">
                              <label>PAN Number<span class="requried">*</span></label>
                              <div class="input-group input-icon-right">
                                <input type="text" class="form-control required"  name="pan_number" placeholder="<?php echo $placeholder['pan'];?>">
                              </div>
                            </div>
                            <div class="form-group col-lg-6">
                              <label>TAN Number</label>
                              <div class="input-group input-icon-right">
                                <input type="text" class="form-control"  name="tan_number" placeholder="<?php echo $placeholder['tan'];?>">
                              </div>
                            </div>
                            <div class="form-group col-lg-6">
                              <label>GST Number<span class="requried">*</span></label>
                              <div class="input-group input-icon-right">
                                <input type="text" class="form-control required"  name="gst_number" placeholder="<?php echo $placeholder['gst'];?>">
                              </div>
                            </div>
                            <div class="form-group col-lg-6">
                              <label>CIN Number</label>
                              <div class="input-group input-icon-right">
                                <input type="text" class="form-control"  name="cin_number" placeholder="<?php echo $placeholder['cin'];?>">
                              </div>
                            </div>
                         </div>
                        </div>
                      </fieldset>
              <h3> Pay Schedule </h3>
              <fieldset>
               <div class="pb-5" data-wizard-type="step-content">
                <h4 class="font-weight-bold text-dark">It's time to set up your pay schedule</h4>
                <div class="mb-10 form-text text-muted">Tell us when and how you pay your employees. We'll make sure they get paid on time.</div>
                <!--begin::Select-->
                <div class="row mt-2">
                  <div class="form-group col-lg-12">
                    <label>Select days you worked in a calendar week.</label>

                    <select class="selectlist form-select js-example-basic-multiple"  name="working_days[]" multiple="multiple">
                      <option value="Mon" selected>Mon</option>
                      <option value="Tue" selected>Tue</option>
                      <option value="Wed" selected>Wed</option>
                      <option value="Thu" selected>Thu</option>
                      <option value="Fri" selected>Fri</option>
                      <option value="Sat">Sat</option>
                      <option value="Sun">Sun</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-4">
                    <label>Minimum Working Hours In A Day <span class="requried">*</span></label>
                    <div class="input-group input-icon-right">
                      <input type="time" class="form-control"  id="kt_timepicker_2"  name="minimum_hours_required" placeholder="0:00:00">
                    </div>
                  </div>
                  <div class="form-group col-lg-4">
                   <label>Half Day Working Hours</label>
                     <div class="input-group input-icon-right">
                      <input type="time" class="form-control" id="kt_timepicker_2"  name="half_day_hours" placeholder="0:00:00" >
                    </div>
                  </div>
                  <div class="form-group col-lg-4">
                    <label>Grace Period</label>
                    <div class="input-group input-icon-right">
                      <input type="time" class="form-control"  id="kt_timepicker_2"  name="grace_perieod" placeholder="0:00:00" >
                    </div>
                  </div>

            <div class="col-lg-12 row">
              <label>Pay your employees on</label>
              <div class="form-radio  mb-2 col-lg-5">
                <div class="radio radio-inline">
                  <label class="form-label">
                    <input type="radio" name="payroll_day" value="31" style="display:none"> <i class="helper"></i>The last working day of every month
                  </label>
                </div>

                <div class="radio radio-inline">
                  <label class="form-label">
                    <input type="radio" name="payroll_day" value="0" style="display:none"><i class="helper"></i>Day of the following month &nbsp;
                  </label>
                </div>
              </div>


              <div class="col-lg-4"> 
                <select class="form-control mt-5 form-select js-example-basic-single"  name="default_payroll_day">
                  <?php for($i=1; $i<=31; $i++) { ?>
                    <option value="<?php echo $i;?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
              </div>

            </div>
          </div>
        </fieldset>
        <h3> Statutory Components </h3>
        <fieldset>

         <div class="pb-5" data-wizard-type="step-content">
          <h4 class="font-weight-bold text-dark">Configure PF, ESI, Professional Tax and LWF</h4>
          <div class="mb-10 form-text text-muted">These benefits and taxes are recommended by the Government for your employees. Enable the components that are applicable to your organization.</div>
          <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
            <div class="card">
             <div class="card-block accordion-block">
              <div id="accordion" role="tablist" aria-multiselectable="true">
                <div class="accordion-panel">
                  <div class="accordion-heading" role="tab" id="headingOne">
                    <h3 class="card-title accordion-title">
                      <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse"
                      data-parent="#accordion" href="#collapseOnedd"
                      aria-expanded="true" aria-controls="collapseOne">
                      Employee's Provident Fund (EPF)
                    </a>
                  </h3>
                </div>
                <div id="collapseOnedd" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                  <div class="accordion-content accordion-desc">
                   <div class="card-body">

                     <div class="form-group row">
                      <div class="form-group col-lg-6">
                        <label>EPF Number</label>
                        <div class="input-group input-icon-right">
                          <input type="text" class="form-control"  name="epf_number" placeholder="<?php echo $placeholder['epf'];?>">
                        </div>
                      </div>
                      <div class="form-group col-lg-6">
                        <label>Deduction Cycle</label>
                        <select  class="form-control form-select js-example-basic-single" name="epf_deduction_cycle" >
                         <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                           <option value="<?php echo $ckey; ?>"><?php echo $cycle ?></option>
                         <?php } ?>
                       </select>
                     </div>
                     <div class="form-group col-lg-6">
                      <label>Employee Contribution (%):</label>
                      <div class="input-group input-icon-right">
                        <input type="text" class="form-control"  name="epf_employee_contribution" placeholder="<?php echo $placeholder['epfempc'];?>">
                      </div>
                    </div>
                    <div class="form-group col-lg-6">
                      <label>Employer Contribution (%):</label>
                      <div class="input-group input-icon-right">
                        <input type="text" class="form-control" placeholder="<?php echo $placeholder['epfemprc'];?>"  name="epf_employer_contribution">
                      </div>
                    </div> 
                    <div class="form-group col-lg-12"> 
                      <div class="checkbox-fade fade-in-primary">
                        <div class="form-label">
                          <label class="checkbox"> 
                            <input type="checkbox" style="display: none"  name="payroll_day" value="31"> <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span>  Employer Contribution In CTC </span>
                          </label>
                        </div> 
                      </div>
                    </div> 
                    <div class="form-group col-lg-12"> 
                      <div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                          <input type="checkbox"  style="display: none" name="allow_override">   <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span> &nbsp;  Override Contribution rate at employee level
                        </label>
                      </div>
                    </div>               
                  </div>             
                  <div class="row">                          
                   <div class="col-lg-6">      
                    <a href="#"  onclick="setenable('epf')" class="btn btn-bg-light" id="epf">Enable</a>
                    <input type="hidden" name="pf_checked" id="pf_checked" value="0">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-panel">
          <div class="accordion-heading" role="tab" id="headingOne">
            <h3 class="card-title accordion-title">
              <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse"
              data-parent="#accordion" href="#collapseOne"
              aria-expanded="true" aria-controls="collapseOne">
              Employee's State Insurance (ESI)
            </a>
          </h3>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="accordion-content accordion-desc">
           <div class="card-body">
            <div class="form-group row">
              <div class="form-group col-lg-6">
                <label>ESI Number</label>
                <div class="input-group input-icon-right">
                  <input type="text" class="form-control" placeholder="<?php echo $placeholder['esi'];?>" name="esi_no">

                </div>
              </div>
              <div class="form-group col-lg-6">
                <label>Deduction Cycle</label>
                <select  class="form-select js-example-basic-single" name="esi_deduction_cycle" >
                 <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                   <option value="<?php echo $ckey; ?>"><?php echo $cycle ?></option>
                 <?php } ?>
               </select>
             </div>
             <div class="form-group col-lg-6">
              <label>Employee Contribution (%):</label>
              <div class="input-group input-icon-right">
                <input type="text" class="form-control"  name="esi_employee_contribution" placeholder="<?php echo $placeholder['esiempc'];?>">
              </div>
            </div>
            <div class="form-group col-lg-6">
              <label>Employer Contribution (%):</label>
              <div class="input-group input-icon-right">
                <input type="text" class="form-control"  placeholder="<?php echo $placeholder['esiemprc'];?>" name="esi_employer_contribution">

              </div>
            </div> 
            <div class="form-group col-lg-6"> 

              <div class="checkbox-fade fade-in-primary">
                <div class="form-label">
                  <label class="checkbox"> 
                    <input type="checkbox" style="display: none"  name="esi_employer_contribution_in_ctc" value="31"> <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                    <span>  Employer Contribution In CTC </span>
                  </label>
                </div> </div>

              </div>  
            </div>
            <div class="col-xl-6 mt-10">      
             <a href="#"  onclick="setenable('esi')" class="btn btn-bg-light" id="esi">Enable</a>

             <input type="hidden" name="esi_checked" id="esi_checked"  value="0">
           </div>
         </div>
       </div>
     </div>
   </div>
   <div class="accordion-panel">
    <div class="accordion-heading" role="tab" id="headingTwo">
      <h3 class="card-title accordion-title">
        <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse"
        data-parent="#accordion" href="#collapseTwo"
        aria-expanded="false"
        aria-controls="collapseTwo">
        Professional Tax (PT)
      </a>
    </h3>
  </div>
  <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
    <div class="accordion-content accordion-desc">
      <div class="card-body">
        <div class="form-group row">
          <div class="form-group col-lg-6">
            <label>PT Number</label>
            <div class="input-group input-icon-right">
              <input type="text" class="form-control"  name="pt_number" placeholder="<?php echo $placeholder['pt'];?>">

            </div>
          </div>
          <div class="form-group col-lg-6">
            <label>Work Location</label>
            <div class="input-group input-icon-right">
              <input type="text" class="form-control"  placeholder="<?php echo $placeholder['ptloc'];?>" name="work_location_id">
            </div>
          </div>

          <div class="form-group col-lg-6">
            <input type="hidden" name="pt_state_id" id="pt_state_id">
            <label>State</label>
            <select  class="form-select js-example-basic-single" name="pt_state_id" >
             <?php foreach ($statelist as $statevalue)  { ?>
               <option value="<?php echo $statevalue['id']; ?>"><?php echo $statevalue['state_name']; ?></option>
             <?php } ?>
           </select>
         </div>
         <div class="form-group col-lg-6">
           <div> <label>Deduction Cycle</label></div>
           <select  class="form-select js-example-basic-single" name="pt_deduction_cycle" >
             <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
               <option value="<?php echo $ckey; ?>"><?php echo $cycle ?></option>
             <?php } ?>
           </select>
         </div>
         <div class="form-group col-lg-12">

           <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="slabtable">
            <tr>
             <th  width="2%"></th>
             <th class="" width="33%"> Start Range</th>
             <th class="thheader" width="32%">End Range</th>
             <th class="thheader" width="33%">Tax Amount</th>
           </tr>
           <tr>
             <td>

              <div class="checkbox-fade fade-in-primary">
                <label class="form-label">
                  <input type="checkbox" value="" class="itemRow d-none">
                  <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
                </label>
              </div>
            </td>
            <td>
              <input type="text" class="form-control" autocomplete="off" name="start_range[]" >
            </td>
            <td>
             <input type="text" class="form-control" autocomplete="off" name="end_range[]" >

           </td>
           <td>
            <input type="text" class="form-control" autocomplete="off" name="tax_amount[]" >

          </td>
        </tr>
      </table>
      <div class="form-group">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
         <button class="btn btn-success"  onclick="addslabrows()" id="addRows" type="button">+ Add More</button>
         <button class="btn btn-danger " id="removeRows" onclick="removeslabrows()"  type="button">- Delete</button> &nbsp;
       </div>
     </div>
   </div>

 </div>
 <div class="">      
   <a href="#"  onclick="setenable('pt')" class="btn btn-bg-light" id="pt">Enable</a>

   <input type="hidden" name="pt_checked" id="pt_checked"  value="0" >
 </div>
</div>
</div>
</div>
</div>
<div class="accordion-panel">
  <div class=" accordion-heading" role="tab" id="headingThree">
    <h3 class="card-title accordion-title">
      <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse"
      data-parent="#accordion" href="#collapseThree"
      aria-expanded="false"
      aria-controls="collapseThree">
    Labour Welfare Fund (LWF)                                                           </a>
  </h3>
</div>
<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
  <div class="accordion-content accordion-desc">
   <div class="card-body">
    <div class="form-group row">
      <div class="form-group col-lg-6">
        <div> <label>State</label> </div>
        <select class="form-select js-example-basic-single" name="lwf_state_id" >
          <option> Select Any One State</option>
          <?php foreach ($statelist as $statevalue)  { ?>
           <option value="<?php echo $statevalue['id']; ?>"><?php echo $statevalue['state_name']; ?></option>
         <?php } ?>
       </select>
     </div>
     <div class="form-group col-lg-6">
      <div><label>Deduction Cycle</label> </div>
      <select  class="form-control form-select js-example-basic-single" name="lwf_deduction_cycle" >
       <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
         <option value="<?php echo $ckey; ?>"><?php echo $cycle ?></option>
       <?php } ?>
     </select>
   </div>
   <div class="form-group col-lg-6">
     <label>Employer Contribution Amount:</label>
     <div class="input-group input-group-button "> 
      <button class="btn btn-info" type="button" onclick="deloptionrows('s1rowid1')">RS.</button>
      <input type="text" class="form-control width1"  name="lwf_employer_contribution" placeholder="<?php echo $placeholder['lwfrs'];?>">
    </div>

  </div>

</div>
<div class="">      
 <a href="#"  onclick="setenable('lwf')" class="btn btn-bg-light" id="lwf">Enable</a>

 <input type="hidden" name="lwf_checked" id="lwf_checked"  value="0">
</div>
</div>


</div>
</div>
</div>
</div>
</div>



</fieldset>

<h3> Salary Components </h3>
<fieldset>

 <!--begin: Wizard Step 5-->
 <div class="pb-5" data-wizard-type="step-content">
  <!--begin::Section-->
  <h4 class="font-weight-bold text-dark">Configure your employee's salary structure.</h4>
  <div class="mb-10 form-text text-muted">Select the necessary components to be included in your employee's salary structure and preview the payslip.

  </div>

  <ul class="nav md-tabs mt-2 infisuite_tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1" role="tab">Earnings</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" role="tab">Deductions</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3" role="tab">Reimbursements</a>
    </li>
  </ul>

  <div class="tab-content mt-5" id="myTabContent">
    <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_1">
      <div class="text-right">
        <a href="#" onclick="showpopup('Earning','','system_earnings');" class="btn btn-success waves-effect waves-light">
          <i class="ti-plus"></i> New Earning
        </a>
      </div>
      <div class="row mt-2">
        <div class="col-xl-5">
          <h4 class="font-weight-bold text-dark">Component Name</h4>
        </div>
        <div class="col-xl-2">
          <h4 class="font-weight-bold text-dark"> Type</h4>
        </div>
        <div class="col-xl-5">
          <h4 class="mb-10 font-weight-bold text-dark">Calculation</h4>
        </div>
      </div>
      <?php 
      foreach ($earnings_componentlist as $scvalue) {

        ?>
        <div class="row" id="componentid_<?php echo $scvalue['id'];?>">
          <div class="col-xl-5 " > 
            <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input class="itemRow d-none" type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist1[]" value="<?php echo $scvalue['id'];?>" >
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>  <a href="#"  onclick="showpopup('Earning','<?php echo $scvalue['id'];?>','system_earnings');">
                  <label><?php echo $scvalue['name']; ?></label></a></span>
                </label>
              </div>

            </div>



            <div class="col-xl-2"> 
              <label><?php echo $scvalue['code']; ?></label>
            </div>
            <div class="col-xl-5">      
              <label>
                <?php   $scvalue['varible_pay']=='0' ? 
                $display_text_calculationtype = "Variable; " : $display_text_calculationtype = "Fixed; ";

                if($scvalue['calculation_type']=='A')  {
                  $display_text_calculationtype .= "Flat Amount"; 
                }

                if($scvalue['calculation_number']!=0) {
                  if($scvalue['calculation_type']=='P')  {
                    $display_text_calculationtype .= $scvalue['calculation_number']."% of ".$scvalue['calculation_category']; 
                  }else{
                    $display_text_calculationtype .=' of '.$scvalue['calculation_number'];

                  }

                } 

                echo $display_text_calculationtype; ?>
              </label>
            </div>
          </div>
          <?php 
        } ?>
      </div>
      <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
       <div class="text-right">
        <a href="#" onclick="showpopup('Deduction','','system_earnings');" class="btn btn-success waves-effect waves-light">
          <i class="ti-plus "></i> New Deduction
        </a>
      </div>
      <div class="row mt-2">
        <div class="col-xl-5">
          <h4 class=" font-weight-bold text-dark">Component Name</h4>
        </div>
        <div class="col-xl-2">
          <h4 class=" font-weight-bold text-dark"> Type</h4>
        </div>
        <div class="col-xl-5">
          <h4 class="font-weight-bold text-dark">Calculation </h4>
        </div>

      </div>
      <?php
      foreach ($deduction_componentlist as $scvalue) {
        ?>
        <div class="row" id='componentid_<?php echo $scvalue['id'];?>' >
          <div class="col-xl-5" id="componentid_<?php echo $scvalue['id'];?>"> 
            <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist2[]" value="<?php echo $scvalue['id'];?>"  class="d-none">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>  <a href="#" onclick="showpopup('Deduction','<?php echo $scvalue['id'];?>','system_earnings');">
                  <label><?php echo $scvalue['name']; ?></label> </a></span>
                </label>
              </div>
            </div>
            <div class="col-xl-2">      
              <label><?php echo $scvalue['code']; ?></label>
            </div>
            <div class="col-xl-5">      
              <label>
                <?php   $scvalue['varible_pay']=='0' ? 
                $display_text_calculationtype = "Variable; " : $display_text_calculationtype = "Fixed; ";

                if($scvalue['calculation_type']=='A')  {
                  $display_text_calculationtype .= "Flat Amount"; 
                }

                if($scvalue['calculation_number']!=0) {
                  if($scvalue['calculation_type']=='P')  {
                    $display_text_calculationtype .= $scvalue['calculation_number']."% of ".$scvalue['calculation_category']; 
                  }else{
                    $display_text_calculationtype .=' of '.$scvalue['calculation_number'];

                  }

                } 

                echo $display_text_calculationtype; ?>
              </label>
            </div>
          </div>
          <?php 
        } ?>
      </div>
      <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
       <div class="text-right">
        <a href="#"onclick="showreimburse('Reimbursement','','system_other_earnings');" class="btn btn-success waves-effect waves-light">
          <i class="ti-plus"></i> New Reimbursement
        </a>
      </div>
      <div class="row mt-2">
        <div class="col-xl-5">
          <h4 class=" font-weight-bold text-dark">Component Name</h4>
        </div>
        <div class="col-xl-2">
          <h4 class=" font-weight-bold text-dark">Type</h4>
        </div>
        <div class="col-xl-5">
          <h4 class="font-weight-bold text-dark">Maximum Amount</h4>
        </div>

      </div>
      <?php
      foreach ($other_earnings_componentlist as $scvalue) {
        ?>
        <div class="row" id="rcomponentid_<?php echo $scvalue['id'];?>">
         <div class="col-xl-5" >      
          <div class="checkbox-fade fade-in-primary">
            <label class="form-label">
             <input type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist3[]" value="<?php echo $scvalue['id'];?>" class="d-none"/>
             <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>   <a href="#" onclick="showreimburse('Reimbursement','<?php echo $scvalue['id'];?>','system_other_earnings');">
              <label><?php echo $scvalue['earning_name']; ?></label> </a></span>
            </label>
          </div>


        </div>
        <div class="col-xl-2">      
          <label><?php echo $scvalue['system_code']; ?></label>
        </div>
        <div class="col-xl-5">      
          <label> <?php echo $scvalue['earning_upto_amount'] . " Per month";?></label>
        </div>
      </div>
      <?php 
    } ?>
  </div>
</div>
</div>
</fieldset>
</form>
</section>
</div>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"  role="document">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel"> <span class="kt-portlet__head-icon">
       <i class="kt-font-brand flaticon-add-circular-button"></i>
     </span>Add <span id="ctype"> </span> Component
   </h5>
   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="">
  <div class="modal-body j-pro j-content">
    <div id="earnings_popup" style="display: none">
     <div class="row">
      <div class="form-group col-xl-6">
       <label>Component Category</label></br> 
       <select class="form-select js-example-basic-single  kt_select2_46">
         <option value=0> Select Any One  </option>
         <?php foreach ($salary_categories as $catname)  { ?>
           <option value="<?php echo $catname['id']; ?>"><?php echo $catname['name']; ?></option>
         <?php } ?>
       </select>
       <span class="form-text text-muted"><?php echo $helptext['compcat'];?> </span>
     </div>
     <div class="form-group col-lg-6">
       <label>Component Name</label>
       <div class="input-group input-icon-right">
        <input type="text" class="form-control" id="pop_name" placeholder="<?php echo $placeholder['compname'];?>" name="pop_name">
      </div>
    </div>
    <span class="form-text text-muted"><?php echo $helptext['compname'];?> </span>
  </div>
  <div class="row">
    <div class="form-group col-lg-6">
     <label>Component Type</label>
     <div class="input-group input-icon-right">
      <input type="text" class="form-control" id="pop_code" placeholder="<?php echo $placeholder['comptype'];?>" name="pop_code">
    </div>
    <span class="form-text text-muted"><?php echo $helptext['comptype'];?> </span>
  </div>
  <div class="form-group col-lg-6">
   <label>Name in Payslip</label>
   <div class="input-group input-icon-right">
    <input type="text" class="form-control" id="pop_payslip_name" placeholder="<?php echo $placeholder['compdisplay'];?>" name="pop_payslip_name">
  </div>
  <span class="form-text text-muted"><?php echo $helptext['compdisplay'];?> </span>
</div>
</div>
<div class="form-group row">
  <div class="checkbox-fade fade-in-primary">
    <label class="form-label">
      <input type="checkbox" value="" id="pop_pro_rata">
      <span class="cr">
        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
      </span>
      <span> Calculate on Pro-rata basis</span>
    </label>
  </div>
</div>
<div class=" row">
  <div class="form-radio">
    <div class="radio radio-inline">
      <label class="form-label">
        <input type="radio" name="pop_component_paytype" id="pop_component_paytype1" class="d-none" checked="checked">
        <i class="helper"></i>Fixed Pay
      </label>
    </div>

    <div class="radio radio-inline">
      <label class="form-label">
        <input type="radio" name="pop_component_paytype" id="pop_component_paytype2" class="d-none"  checked="checked">
        <i class="helper"></i>Variable Pay
      </label>
    </div>
  </div>
</div>
<div class=" row">
 <h4>Calculate Type</h4>
</div>
<div class="row">
  <div class="form-radio">
   <div class="radio radio-inline">
    <label class="form-label">
      <input type="radio" id="pop_calculation_type1" name="pop_calculation_type"   value="A" class="d-none">
      <i class="helper"></i>Flat Amount 
    </label>
  </div>

  <div class="radio radio-inline">
    <label class="form-label">
      <input type="radio" id="pop_calculation_type2" name="pop_calculation_type"  value="P" class="d-none">
      <i class="helper"></i>Percentage
    </label>
  </div>
</div>
</div>
<div class="row">
 <div class="col-lg-6 form-group">
   <label>Calculation Value</label>
   <div class="input-group input-group-button"> 
     <button class="btn btn-info" type="button" >Rs.</button>
     <input type="text"  id="pop_calculation_number" placeholder="<?php echo $placeholder['compamt'];?>" class="form-control text-right width1"> 

   </div>
 </div>
 <div class="col-xl-6 form-group">
   <div><label>Component Category</label> </div>
   <select class="form-select js-example-basic-single kt_select2_47">
    <option value="">Select Any One</option>
    <?php foreach ($earnings_componentlist as $earn)  { ?>
      <option value="<?php echo $earn['code']; ?>"><?php echo $earn['code']; ?></option>
    <?php } ?>
  </select>
</div>
<input type="hidden" id="pop_table_name" value="">
<input type="hidden" id="pop_calculation_mode" value="">
<input type="hidden" id="updaterowid" value="">
</div>
</div>

<div id="reimburse_popup" style="display: none">
 <input type="hidden" id="pop_table_name" value="">
 <input type="hidden" id="updaterowid" value="">
 <div class="row">
  <div class="form-group col-lg-6">
   <label>Component Name</label>
   <div class="input-group input-icon-right">
    <input type="text" class="form-control" id="reimpop_name" placeholder="<?php echo $placeholder['rcompname'];?>" name="reimpop_name">

  </div>

</div>
<div class="form-group col-lg-6">
 <label>Component Type</label>
 <div class="input-group input-icon-right">
  <input type="text" class="form-control" id="reimpop_code" placeholder="<?php echo $placeholder['rcomptype'];?>" name="reimpop_code">

</div>
</div>
<div class="form-group col-lg-6">
 <label>Name in Payslip</label>
 <div class="input-group input-icon-right">
  <input type="text" class="form-control" id="reimpop_payslip_name" placeholder="<?php echo $placeholder['rcompdisplay'];?>" name="reimpop_payslip_name">

</div>
</div>
<div class="form-group col-lg-6">
 <label>Enter Amount</label>

 <div class="input-group input-group-button"> 
   <button class="btn btn-info" type="button" >Rs.</button>
   <input type="text"  id="reimpop_calculation_number" placeholder="<?php echo $placeholder['compamt'];?>" name="reimpop_calculation_number" class="form-control text-right width1"> 

 </div>
 <span class="form-text text-muted"><?php echo $helptext['compamt'];?> </span>
</div>
<div class="form-group ml-5">
 <div class="row">

  <div class="checkbox-fade fade-in-primary">
    <label class="form-label">
      <input type="checkbox" value="" id="reimpop_partofctc">
      <span class="cr">
        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
      </span>
      <span>Include this as a Flexible Benefit Plan Component.</span>
    </label>
  </div>


</div>
</div>
</div>
</div>
<div class="modal-footer">

  <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary font-weight-bold" onclick="submitpopup()">Save changes</button>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
    <div class="text-dark order-2 order-md-1">
      <span class="text-muted font-weight-bold mr-2">2021©</span>
      <a href="http://keenthemes.com/metronic" target="_blank" class="text-dark-75 text-hover-primary">Infisuite</a>
    </div>

    <div class="nav nav-dark">
      <a href="#" target="_blank" class="nav-link pl-0 pr-5">About</a>
      <a href="#" target="_blank" class="nav-link pl-0 pr-5">Team</a>
      <a href="#" target="_blank" class="nav-link pl-0 pr-0">Contact</a>
    </div>
  </div>
</div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup1" style="border:none;">
            
        </div>
    </div>
</div>

<!-- Required Jquery -->
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- waves js -->
<script src="assets/pages/waves/js/waves.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- Float Chart js -->
<script src="assets/pages/chart/float/jquery.flot.js"></script>
<script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
<script src="assets/pages/chart/float/curvedLines.js"></script>
<script src="assets/pages/chart/float/jquery.flot.tooltip.min.js"></script>
<!-- Chartlist charts -->
<script src="assets/bower_components/chartist/js/chartist.js"></script>
<!-- amchart js -->
<script src="assets/pages/widget/amchart/amcharts.js"></script>
<script src="assets/pages/widget/amchart/serial.js"></script>
<script src="assets/pages/widget/amchart/light.js"></script>
<!-- Custom js -->
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/vertical/vertical-layout.min.js"></script>
<script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.min.js"></script>
<script type="text/javascript" src="assets/js/script.min.js"></script>
<!--------------  data table   --------------->
<!-- modernizr js -->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
<!-- data-table js -->
<script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/pages/data-table/js/jszip.min.js"></script>
<script src="assets/pages/data-table/js/pdfmake.min.js"></script>
<script src="assets/pages/data-table/js/vfs_fonts.js"></script>

<script src="assets/pages/data-table/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/buttons.flash.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/jszip.min.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
<script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
<script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<!-- Select 2 js -->
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<!-- Multiselect js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<!-- Date-range picker js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-daterangepicker/js/daterangepicker.js"></script>
<!-- Date-dropper js -->
<script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>
<!-- Color picker js -->
<script type="text/javascript" src="assets/bower_components/spectrum/js/spectrum.js"></script>
<script type="text/javascript" src="assets/bower_components/jscolor/js/jscolor.js"></script>
<!-- Mini-color js -->
<script type="text/javascript" src="assets/bower_components/jquery-minicolors/js/jquery.minicolors.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/custom-picker.js"></script>
<!-- tinymce js -->
<script src="assets/pages/wysiwyg-editor/js/tinymce.min.js"></script>
<!-- Custom js -->
<script src="assets/pages/wysiwyg-editor/wysiwyg-editor.js"></script>
<!-- Masking js -->

<script src="assets/pages/form-masking/inputmask.js"></script>
<script src="assets/pages/form-masking/jquery.inputmask.js"></script>
<script src="assets/pages/form-masking/autoNumeric.js"></script>
<script src="assets/pages/form-masking/form-mask.js"></script>

<!-- Switch component js -->
<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
<!-- Tags js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>
<!-- Max-length js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>

<!-- modalEffects js nifty modal window effects -->
<script src="assets/js/classie.js"></script>
<script type="text/javascript" src="assets/js/modalEffects.js"></script>
<!-- calender js -->
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
<!-- Accordion js -->
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<!--Forms - Wizard js-->
<script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
<script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
<script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
<script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>

<script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
<script type="text/javascript" src="assets/pages/notification/notification.js"></script>

<!-- Custom js -->
<script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
<!--     <script src="assets/js/pcoded.min.js"></script> -->
<!--    <script src="assets/js/vertical/vertical-layout.min.js"></script> -->
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<!--    <script type="text/javascript" src="assets/js/script.js"></script> -->
<script type="text/javascript" src="assets/js/custom.js"></script>

</body>
<!--end::Body-->
</html>