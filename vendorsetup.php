<?php
ini_set('display_errors' ,0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
$user_id =  $_SESSION['userid'];
$data12["where"] = array('email'=>$_SESSION['email']);
$datw12 = urlencode(json_encode($data12));
$curl12 = API."?mode=gettabledetailswithconditions&table=vendors&data=".$datw12; 
$cudata12 = callAPI($curl12);
$approved = json_decode($cudata12,1);
$approved_data = $approved[0];
if(count((array)$approved_data) > 0){
  //header('Location:index.php');
}
$File_Uploads_Path = "uploads/all/";
$_SESSION['error_message']  =  toGetErrorMessages();
if(isset($_POST) && !empty($_POST)){
    $listofdocs = $_POST['document_id'];
    foreach($listofdocs as $kkk=>$docvalue){
     $File_Uploads_Path = "uploads/all/";
     if($_POST['document_categoryid'][$kkk]!= "" && $_FILES["fileToUpload"]["name"][$kkk]!=""){
        $temp = explode(".", $_FILES["fileToUpload"]["name"][$kkk]);
        $Filename=  "file_".time()."_".uniqid();
        $newfilename = $Filename.'.' .end($temp);
        $documents[] = array('doc_id'=>$_POST['document_file_id'][$kkk],'document_id'=>$docvalue,'categoryid'=>$_POST['document_categoryid'][$kkk],'filename'=>$newfilename);
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$kkk], $File_Uploads_Path.$newfilename);	
    }else{ 
        $documents[] = array('doc_id'=>$_POST['document_file_id'][$kkk],'document_id'=>$docvalue,'categoryid'=>$_POST['document_categoryid'][$kkk],'filename'=>$_POST['hidden_fileToUpload'][$kkk]);
    }
  }
  unset($_POST['document_categoryid']);
  unset($_POST['hidden_fileToUpload']);
  unset($_POST['document_file_id']);
  if(!empty($_FILES["logo_image"]["name"])){
    $temp = explode(".", $_FILES["logo_image"]["name"]);
    $Filename=  "file_".time()."_".uniqid();
    $newfilename = $Filename.'.' .end($temp);
    $_POST['image'] = $newfilename;
    move_uploaded_file($_FILES["logo_image"]["tmp_name"], $File_Uploads_Path.$newfilename);
  }
  unset($_POST['logo_image']);
  unset($_POST['logo_remove']);
  $_POST['user_idd'] = $_POST['user_id'];
  $_POST['attachments'] = json_encode($documents,1);
  $rowid = $_POST['Rid'];
  unset($_POST['Rid']);
if($rowid !=""){
     $_POST['company_id'] = $_SESSION['company_id'];
     $_POST['is_active'] = '0';
     $_POST['updated_date'] = nowDate();
     $_POST['updated_by'] = $_SESSION['userid'];
     $url = API."?mode=setupvendor&rid=".$rowid."&method=post";
     $response = file_post_contents($url, $_POST, 'update');
 }else{ 
   
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['is_active'] = '0';
    $_POST['created_date'] = nowDate();
    $_POST['created_by'] = $_SESSION['userid'];
    $url = API."?mode=setupvendor&method=post";
    $response = file_post_contents($url, $_POST, 'insert'); 
}
$response = json_decode($response,1);
if($response['status'] == 'fail'){
  if(isset($_SESSION['error'][$response['error_no']])){
   $dis_err = explode(",", $response['other']);
   foreach ($dis_err as $va){
    $dis_er [] = $dis_labels[$va];
  }
  $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
}else{
 $error_message = $response['message'];
}
}else{   //header('Location:vendorsetup.php');
?>
<script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            } 
        }
    </script>
<?php
} 
}
   
$helptext = array('first_name'=>'Please Enter Your Proprietor/Owner/CEO First Name',
  'last_name' =>  'Please Enter Proprietor/Owner/CEO Last Name',
  'fax' =>  'Please Enter Your Fax Number',
  'firstname'=>'Please Enter Your First Name',
  'email'=>'Please Enter Your Email',
  'mobile'=>'Please Enter Mobile Number',
  'post_code'=> 'Please Enter Your Post code',
  'country'=> 'Please Select Your Country',
  'state' =>'Please Select Your State',
  'city'=>'Please Select Your City',
  'company'=>'Please Enter Your Company Name',
  'business_type'=>'Please Select Your Business Type',
  'gstin' =>  'Please Enter GST Number',
  'tin_number' => 'Please Enter TIN Number',
  'cst_reg_no' => 'Please Enter CST Number',
  'vat_number' => 'Please Enter VAT Number',
  'company'=>'Please Enter Your Company Name',
  'bank_ac_name'=>'Please Select Your Business Type',
  'bank_name' =>  'Please Enter Bank Name',
  'account_type' => 'Please Enter Account Type',
  'bank_account_no' => 'Please Enter Bank Account Number',
  'ifsc_code' => 'Please Enter IFSC Code',
  'bank_address' => 'Please Enter Bank Address',
  'contact_person'=>'Please Enter Cotact Person Name',
  'store_pincode' => 'Please Enter Store Pincode',
  'businesssize'=>'Please Select Business Size',
  'businessstatus'=>'Please Select Industry',
  'website_url'=> 'Please Enter Website Address',
  'established_year'=>'Please Select Incorporation Date',
  'phone'=>'Please Enter Phone Number',
  'display_name'=>'Please Enter Display Name'
);
$placeholder= array('first_name'=>'First Name','last_name'=>'Last Name','phone'=>'9187945496','email'=>'hello@infisuite.com','mobile'=>'9874563210','fax'=>'78945487877','post_code'=>'641004','company'=>'Technology','gstin'=>'04AABCU9603R1ZV','tin_number'=>'29567129023','cst_reg_no'=>'GB123456789','vat_number'=>'GB123456789','contact_person'=>'James William','store_pincode'=>'641035','established_year'=>'','contactperson'=>'George','contactnumber'=>'0422 5688 9500','pan_no'=>'DMUT6868O', 'display_name'=>'Display Name');

$url = API."?mode=getvendorsettings";
$response = callAPI($url);
$setup_details = json_decode($response,1);
$countrieslist = $setup_details['countries'];
$document_lists = $setup_details['document_lists'];
$doctypes = $setup_details['document_list_types'];
$register_data = $setup_details['vendor_registers'];
$invitation = $setup_details['application_user_invitations'];
$connect_user = json_decode($invitation['connect_users'],1);
$states = $setup_details['states'];
$cities = $setup_details['cities'];
$genc_code_list = $setup_details['bankactype'];
$vendor_documen = $setup_details['vendor_documents'];
$vendor_documents = array();
foreach($vendor_documen as $states5){
  $vendor_documents[$states5['category_id']] = $states5;
}
$businesstypes = array('Sole Proprietorship','Partnerships','Corporation','Limited Partnership','Limited Liability Company','Nonprofit Organization','Cooperative');
$businesssize = array('MSME','Large');
$businessstatus = array('Manufacturer','Authorised Dealer','Stockist/Trader','Importer/Indian Agent','Service Provider','Others');

$notificationlist = toGetNotigficationListByUsingUserId($_SESSION['userid']);
$nptificationcnt = 0;
foreach($notificationlist as $notifcn){
   if($notifcn['Notification_Type'] == 'ACTION' || $notifcn['Notification_Type'] == 'INFORMATION'){
    $nptificationcnt++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Infistaq | Home</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="Unlock your business's full potential with affordable ERP software Infistaq. ERP that supports manufacturing, distribution, retail, trading, services, education, non profits and healthcare. Try our customizable ERP solutions today." />
<meta name="keywords" content="ERP software, Enterprise Resource Planning, ERP system, Cloud ERP, On-premise ERP, ERP solutions, ERP implementation, ERP integration, Business process automation, Supply chain management, Inventory management, Financial management, Manufacturing ERP, Human resources management, Customer relationship management (CRM), Sales and marketing automation, ERP consulting, ERP customization, Enterprise software, ERP for small businesses, ERP for medium-sized businesses, ERP for large enterprises, Industry-specific ERP (e.g., ERP for manufacturing, retail, healthcare, etc.), ERP benefits, ERP features, ERP comparison, ERP reporting and analytics, ERP training, ERP support and maintenance, ERP scalability, ERP ROI (Return on Investment), Cloud-based ERP solutions, ERP for business growth, ERP best practices, ERP compliance, ERP case studies/testimonials" />
<meta name="author" content="infisuite" />
  <!-- Favicon icon -->
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">

  <!-- Required Fremwork -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">


  <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome-n.min.css">



  <!-- Data Table Css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/buttons/css/buttons.dataTables.min.css">

  <!-- Chartlist chart css -->
  <link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
  <!-- Bootstrap date-time-picker js -->  
  <link rel="stylesheet" type="text/css" href="assets/pages/advance-elements/css/bootstrap-datetimepicker.css">
  <link rel="stylesheet" type="text/css" href="assets/css/date-jquery-ui.css">
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
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/responsive/css/responsive.dataTables.css">
  <!-- Switch component css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/switchery/css/switchery.min.css">
  <!-- Tags js -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
  <!-- Style.css -->  
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/component.css">
  <link rel="stylesheet" type="text/css" href="assets/css/pages.css"> 
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <link rel="stylesheet" type="text/css" href="assets/css/custom_mobile.css">
  <link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
  <!----notification---->
  <link rel="stylesheet" type="text/css" href="assets/pages/notification/notification.css">
  <!-- Calender css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.print.css" media='print'>
  <link rel="stylesheet" type="text/css" href="assets/bower_components/owl.carousel/css/owl.carousel.css">
  <!-- Redial css -->
  <link rel="stylesheet" href="assets/pages/chart/radial/css/radial.css" type="text/css" media="all">
  <!--forms-wizard css-->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery.steps/css/jquery.steps.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/intro.js/css/introjs.css">
  <!--forms-widget css-->
  <link rel="stylesheet" type="text/css" href="assets/css/widget.css">
  <!-- sweet alert framework -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/sweetalert/css/sweetalert.css">
  <!-- sweet alert framework -->


 <?php /** ?> 
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
  <!----new---->
 <link rel="stylesheet" type="text/css" href="assets/pages/notification/notification.css">
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery.steps/css/jquery.steps.css">
<link rel="stylesheet" type="text/css" href="assets/bower_components/intro.js/css/introjs.css">
<?php **/?>
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
  .setup-page{ padding: 0 !important;  }
  .accbora{border: 1px solid #3699ff !important; border-radius: 5px;
    }}
  </style>
  <style>
    .alert.alert-custom.alert-white .alert-text {
      color: #3f4254;
    }
    .alert.alert-custom .alert-text {
      -ms-flex-item-align: center;
      align-self: center;
      -webkit-box-flex: 1;
      -ms-flex-positive: 1;
      flex-grow: 1;
    }
    .symbol {
      display: inline-block;
      -ms-flex-negative: 0;
      flex-shrink: 0;
      position: relative;
      border-radius: 0.42rem;
    }
    .nav-link {
      display: block;
      padding: 0.5rem 1rem;
    }
    .image-input {
      position: relative;
      display: inline-block;
      border-radius: 0.42rem;
      background-repeat: no-repeat;
      background-size: cover;
    }
    label.btn-hover-text-primary{
      cursor: pointer;
      position: absolute;
      right: -10px;
      top: -10px;
    }
    .image-input input {
      width: 0!important;
      height: 0!important;
      overflow: hidden;
      opacity: 0;
    }


    .pricing .title {
      background: #1ABB9C;
      color: #fff;
      padding: 15px 0px 2px 0px;
      /** text-align: center; **/
    }
    .pricing_features {
      background: #FAFAFA;
      padding: 10px 7px;
      min-height: 100px;
      font-size: 13.5px;
    }
    .list-unstyled {
      padding-left: 0;
      list-style: none;
    }
    .pricing_features ul li {
      margin-top: 10px;
    }
    .ui-ribbon-container .ui-ribbon-wrapper {
      position: absolute;
      overflow: hidden;
      width: 85px;
      height: 88px;
      top: -3px;
      right: -3px;
    }
    .ui-ribbon-container .ui-ribbon {
      position: relative;
      display: block;
      text-align: center;
      font-size: 15px;
      font-weight: 700;
      color: #fff;
      transform: rotate(
        45deg);
      padding: 7px 0;
      left: -5px;
      top: 15px;
      width: 120px;
      line-height: 20px;
      background-color: #555;
      box-shadow: 0 0 3px rgb(0 0 0 / 30%);
    }
    .pricing .title span {
      background: rgba(51,51,51,0.28);
      padding: 2px 5px;
    }


    span.badge.bg-c-red.custom_bell {
      position: absolute;
      top: -3px;
      left: 26px;
      color:white;
      -webkit-animation: not-blink 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
      animation: not-blink 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
    }
  </style>


</head>
<!--end::Head-->
<script type="text/javascript">

  function sameaddress() {
    var adr = document.getElementById('is_same');
    if(adr.checked) {
     document.getElementById('shipping_address').value =document.getElementById('billing_address').value;
     document.getElementById('shipping_post_code').value =document.getElementById('billing_post_code').value;
     //document.getElementById('selectedshipcountry').value = document.getElementById('selectedbillcountry').value;

      // $("#selectedshipcountry").select2().val($("#selectedbillcountry").val()).trigger("change");
      // $("#selectedshipstate").select2().val($("#selectedbillstate").val()).trigger("change");
      // $("#selectedshipcity").select2().val($("#selectedbillcity").val()).trigger("change");
    /// document.getElementById('selectedshipstate').value =document.getElementById('selectedbillstate').value;
    // document.getElementById('selectedshipcity').value= document.getElementById('selectedbillcity').value;
     // var e = document.getElementById("kt_select2_102");
     // var selectcountry = e.options[e.selectedIndex].text;
     // console.log(selectcountry);
     
     // var e1 = document.getElementById("kt_select2_101");
     // var selectstate = e1.options[e1.selectedIndex].text;

     // var e2 = document.getElementById("kt_select2_103");
     // var selectcity = e2.options[e2.selectedIndex].text;

    $('#kt_select2_104').select2();
    var selectedOptions = $("#kt_select2_102").select2("data");
    $('#kt_select2_104').empty();
    for (var i = 0; i < selectedOptions.length; i++) {
      var option = new Option(selectedOptions[i].text, selectedOptions[i].id, true, true);
      $("#kt_select2_104").append(option);
    }
   
    $('#kt_select2_105').select2();
    var selectedOptions = $("#kt_select2_101").select2("data");
    $('#kt_select2_105').empty();
    for (var i = 0; i < selectedOptions.length; i++) {
      var option = new Option(selectedOptions[i].text, selectedOptions[i].id, true, true);
      $("#kt_select2_105").append(option);
    }

    $('#kt_select2_106').select2();
    var selectedOptions = $("#kt_select2_103").select2("data");
    $('#kt_select2_106').empty();
    for (var i = 0; i < selectedOptions.length; i++) {
      var option = new Option(selectedOptions[i].text, selectedOptions[i].id, true, true);
      $("#kt_select2_106").append(option);
    }
    //  $("#kt_select2_104").trigger("change");
         // $("#kt_select2_105").trigger("change");
    // $("#kt_select2_106").trigger("change");

      // var select = $('#kt_select2_102').html();
      // console.log(select);
      // $('#kt_select2_104').html(select);  
    //  select.options[select.selectedIndex].text = selectcountry;
     
     // var select = document.getElementById('kt_select2_104'); 
     // select.options[select.selectedIndex].text = selectcountry;

     // var select1 = document.getElementById('kt_select2_105'); 
     // select1.options[select1.selectedIndex].text = selectstate;

     // var select2 = document.getElementById('kt_select2_106'); 
     // select2.options[select2.selectedIndex].text = selectcity;

   } else {
     document.getElementById('shipping_address').value = "";
     document.getElementById('shipping_post_code').value = "";
    $('#kt_select2_104').empty();
     $('#kt_select2_105').empty();
      $('#kt_select2_106').empty();
      $("#kt_select2_105").html('<option value="">Select Any One<option>');
      $("#kt_select2_106").html('<option value="">Select Any One<option>');
     $.ajax({
      url: "ajax.php",
      data:'page=getCountrylist',
      type: "POST",
      success: function(response) {
                  console.log(response);
                 response = JSON.parse(response);
                 var country = document.getElementById('kt_select2_104'); 
                 for (var i = 0; i < response.length; i++) 
                 {
                  var el = document.createElement("option");
                  el.textContent = response[i].country_name;
                  el.value = response[i].id;
                  country.add(el);
                }
              }
            });
   }
 }


 function selectedcategory(selected_id,element_id,table_name,field_name,targetelement) {
  var selectedvalues = selected_id;
                //alert(selected_id);

                if(table_name=='states' || table_name=='cities'){
                 $.ajax({
                  url: "ajax.php",
                  data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
                  type: "POST",
                  success: function(response) {
                    var response = JSON.parse(response);
                    console.log(response.length);
                    console.log(response);
                    var select = document.getElementById(element_id); 
                    document.getElementById(element_id).innerHTML = "";
                    for (var i = 0; i < response.length; i++) 
                    {
                     var el = document.createElement("option");
                     if(table_name=='states'){
                      el.textContent = response[i].state_name;
                      el.value = response[i].id;

                    }
                    if(table_name=='cities'){
                      el.textContent = response[i].city_name;
                      el.value = response[i].id;

                    }
                    select.add(el);
                  }
                }
              });
               }

             }


  function displaystausdrop(id,table,status_id){
   $.ajax({
     url: "ajax.php",
     data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
     type: "POST",
     success:function(data){
       console.log(data);
       $("#dropdownlist").html(data);
     },
     error:function (){}
   }); 
 }
 function statuspopup(status,id){
   var table_name = 'vendor_registers';
   jQuery.ajax({
     url: "views/custom_insert_status_popup.php",
     data:'table='+table_name+'&status='+status+'&rid='+id,
     type: "POST",
     success:function(data){
       $('#popup1').html(data);
       $('#kt_modal_KTDatatable_remote').modal("show");
     },
     error:function (){}
   });
 }
 function showFormDetailPopuop(){
   var outputdisplay_logo = document.getElementById("outputdisplay_logo").src;
   var formvalue =  $("#example-advanced-form_vendor").serialize();
   console.log(formvalue);
   jQuery.ajax({
    url: "views/custom_insert_vendorsetup_popup.php",
    data: formvalue+"&outputdisplay_logo="+outputdisplay_logo,
    type: "POST",
    success:function(data){
     console.log(data);
     $('#dynamic_popupxxl').html(data);
     $('#kt_modal_dynamic_popupxxl').modal("show");
   }
 });
 }
 
 
 function reaprovedoc(hid){
   var vendor_hrconnect = $("#vendor_hrconnect").val();
   var user_id = $("#user_id").val();



swal({
  title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
  text: '<?php echo $_SESSION['error_message']['1004']; ?>',
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
      $.ajax({
        url: "ajax.php",
        data:'page=reaprovevendordoc&userid='+vendor_hrconnect+'&vendor_id='+user_id+'&hid='+hid,
        type: "POST",
        success:function(data){
          console.log(data);
         //location.reload(); 
         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','Notification Successfully Sent','');
       },
       error:function (){}
     });
  }
});









  //  Swal.fire({
  //    title: '<?php //echo $_SESSION['error_message'][1004]; ?>',
  //    showDenyButton: true,
  //    showCancelButton: false,
  //    confirmButtonText: 'Yes',
  //    denyButtonText: 'No',
  //  }).then((result) => {
  //    if (result.isConfirmed) {
  //     $.ajax({
  //       url: "ajax.php",
  //       data:'page=reaprovevendordoc&userid='+vendor_hrconnect+'&vendor_id='+user_id+'&hid='+hid,
  //       type: "POST",
  //       success:function(data){
  //         console.log(data);
		// 	   //location.reload(); 
  //        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','Notification Successfully Sent','');
  //      },
  //      error:function (){}
  //    });
  //   }
  // })

 } 
 
 
 function checkFileSelect(e,id){
  var filename = e.target.files[0].name;
  $("#fileattach_"+id).text(filename);
} 

function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
                   window.location = window.location.href;
                    if(res.transaction_update.status=='sucess'){
                         window.location = window.location.href;
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location = window.location.href;
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        }); 
}
</script>
<!--begin::Body-->


<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
  <div id="kt_header_mobile" class="header-mobile align-items-center  header-mobile-fixed p-1" style="background-color: white;">
   <a href="">
   <!--  <img alt="Logo" style="width:70px;" src="assets/images/logo.png" />  -->
   <?php if($_SESSION['config']['display_logo']!=""){ ?>
                    <img class="img-fluid img-100" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                <?php }else{ echo $_SESSION['config']['display_logo']; } ?></a>
         <!--- 
		 <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle"> <span></span> </button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle"> <span></span> </button>
         </div>
         --->



       </div>
       <?php 
if($register_data['current_status'] !=""){
        $workflowsteps = getWorkflowStatus('vendor_registers',$register_data['id'],$register_data['current_status']);

    
        }
            ?>

       <!--end::Header Mobile-->
       <div class="d-flex flex-column flex-root mt-2">
        <!--begin::Page-->
        <div class=" flex-row flex-column-fluid page">
         <!--begin::Wrapper-->
         <div class="d-flex flex-column flex-row-fluid wrapper setup-page" id="kt_wrapper">
          <!--end::Header-->
          <!--begin::Content-->
          <div class="content  d-flex flex-column flex-column-fluid " id="kt_content">
           <!--begin::Entry-->
           <div class="flex-column-fluid">
            <!--begin::Container-->
            <div class="p-2">
              <?php if($register_data['current_status'] ==ST_APPROVAL_PENDING){ ?>

                <div class="alert alert-info mb-2 p-3" role="alert">
                  <h3 class="alert-heading">Approval in Progress!!.</h3>
                  <p class="font-size-h6">Thank you for applying for an account. Your account is currently pending approval by the site administrator.In the meantime, a welcome message with further instructions has been sent to your e-mail address.</p>
                  <p class="mb-0 font-size-h6">Incase of any queries please contact to admin.</p>
                </div>
              <?php } ?>

              <!-- Form wizard with validation card start -->
              <div class="card " style="margin-bottom: 131px;">
                <?php echo $workflowsteps['current_status_ribbon']; ?>
               <div class="card-header">
                <h5>Vendor Registers</h5>
               <div class="card-header-right table-card-header m-r-50">
                  <button  type="button" class="displayChatbox  btn waves-effect waves-light btn-instagram bg-white" data-bs-toggle="dropdown"  style="overflow: visible; "> <i class="ti-bell text-mute"></i> <span class="badge bg-c-red custom_bell"><?php echo $nptificationcnt; ?></span></button>

                 <?php 
                     echo $workflowsteps['workflow_btn'];
    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
   if(!in_array($register_data['current_status'], $check_array)){ $disablecss = "style='pointer-events: none; opacity: 0.7;'";  } ?>

                <a href="index.php?option=com_logout" class="btn btn-danger">
                  <i class="ti-power-off"></i> Logout
                </a>
				<!--	<a onclick="showFormDetailPopuop()" class="btn btn-danger">
                       Popup
                     </a>   -->
                   </div>
                 </div>
                 <div class="card-block">
                  <div class="row">
                    <?php echo $workflowsteps['breadcrumb'];  ?>
                    <div class="col-md-12">
                     <?php if($error_message !=''){ ?>
                      <div class="alert alert-danger background-danger alert-dismissible">
                        <strong><?php echo $error_message; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>	
                    <?php } ?>
                    <div id="wizard">
                      <section>
                       <form class="form wizard-form" id="example-advanced-form-vendorsetup" method="post" action="" enctype="multipart/form-data">

                        <h3> Profile Information <small>Please enter profile details</small></h3>
                        <fieldset>
                          <!--begin: Wizard Step 1-->
                          <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                            <h4 class="mb-10 font-weight-bold text-dark">Vendor Registration</h4>
                            <!--end::Input-->
                            <div class="row" <?php echo $disablecss; ?>>
                              <div class="col-xl-6">
                                <!--begin::Input-->
                                <div class="form-group">
            <!--       <input type="hidden" name="vendor_hrconnect" id="vendor_hrconnect" value="<?php //echo ST_VENDOR_HRCONNECT ==""?$invitation[0]['created_by']:ST_VENDOR_HRCONNECT; ?>"> -->


 <input type="hidden" name="Rid" value="<?php echo $register_data['id']; ?>">
    <input type="hidden" name="hr_connect" id="hr_connect" value="<?php echo $connect_user['hr_connect']; ?>">
    <input type="hidden" name="finance_connect" id="finance_connect" value="<?php echo $connect_user['finance_connect']; ?>">
    <input type="hidden" name="purchase_connect" id="purchase_connect" value="<?php echo $connect_user['purchase_connect']; ?>">
    <input type="hidden" name="admin_connect" id="admin_connect" value="<?php echo $connect_user['admin_connect']; ?>">
                
                                  <label>Proprietor/Owner/CEO Name <span class="requried">*</span></label>
                                  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>">
                                   <input type="text" class="form-control required text-capitalize" name="first_name" placeholder="<?php echo $placeholder['first_name']; ?>" value="<?php echo $register_data['first_name'] !=""?$register_data['first_name']:$_SESSION['user']['first_name'];?>" />

                               </div>
                               <!--end::Input-->
                             </div>
                             <div class="col-xl-6">
                              <!--begin::Input-->
                              <div class="form-group">
                               <label>Last Name</label>
                                <input type="text" class="form-control text-capitalize" name="last_name" value="<?php echo $register_data['last_name']!=""?$register_data['last_name']:$_SESSION['user']['last_name'];?>" placeholder="<?php echo $placeholder['user']['last_name']; ?>"  />
                             </div>
                             <!--end::Input-->
                           </div>
                           <div class="col-xl-6">
                            <!--begin::Input-->
                            <div class="form-group">
                              <label>Display Name<span class="requried">*</span></label>
                              <input type="text" class="form-control required text-capitalize" name="display_name" value="<?php echo $register_data['display_name'] !=""?$register_data['display_name']:$_SESSION['user']['display_name'];?>" placeholder="<?php echo $placeholder['display_name']; ?>"  />
                           </div>
                           <!--end::Input-->
                         </div>
                         <div class="form-group col-lg-4">
                          <?php
                          $logo = $register_data['image']==''?  $File_Uploads_Path.'no-image.jpg':$File_Uploads_Path.$register_data['image']; ?>
                          <label class="ml-5">Logo</label>
                          <div class="col-lg-9 col-xl-6">
                            <div class="profile-pic">
                              <label class="-label" for="display_logo">
                                <span> <i class="fa fa-camera"></i> &nbsp; Image</span>
                              </label>
                              <input  type="file" name="logo_image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                              <input type="hidden" name="logo_remove" >
                              <img src="<?php echo $logo; ?>" id="outputdisplay_logo" width="200"  onerror="this.src='uploads/all/no-image.jpg';"/>
                            </div>
                          </div>  
                          <span class="form-text text-muted"><?php //echo $helptext['logo'];?> </span>
                        </div> 
                        <div class="form-group col-lg-12">
                         <label>Email <span class="requried">*</span></label>
                          <input type="text" class="form-control input-group-solid required"  name="email" readonly value="<?php echo $_SESSION['email'];?>" placeholder="<?php //echo $placeholder['email']; ?>"  />
                        </div>
                        <div class="col-xl-6">
                          <!--begin::Input-->
                          <div class="form-group">
                           <label>Contact Person</label>
                             <input type="text" class="form-control " name="contact_person" value="<?php echo $register_data['contact_person'];?>" placeholder="<?php echo $placeholder['contact_person']; ?>"  />

                         </div>
                         <!--end::Input-->
                       </div>
                       <div class="col-lg-6">
                        <!--begin::Input-->
                        <div class="form-group">
                         <label>Mobile Number <span class="requried">*</span></label>
                         <input type="number" class="form-control required" name="mobile" value="<?php echo $register_data['mobile'];?>" placeholder="<?php echo $placeholder['mobile']; ?>"  />
                       </div>
                       <!--end::Input-->
                     </div>
                     <div class="col-xl-6"> 
                      <div class="form-group">
                       <label>Phone Number</label>
                         <input type="number" class="form-control" name="phone" value="<?php echo $register_data['phone'];?>" placeholder="<?php echo $placeholder['phone']; ?>"  />
                     </div>
                   </div>
                   <div class="col-xl-6">
                    <div class="form-group">
                     <label>Fax</label>
                       <input type="number" class="form-control" value="<?php echo $register_data['fax'];?>" name="fax" placeholder="<?php echo $placeholder['fax']; ?>"  />
                     </div>
                   </div>
                 </div>

               </fieldset>
               <h3> Business Information <small>Vendor business details</small></h3>
               <fieldset>

                <!--begin: Wizard Step 3-->
                <div class="pb-5" data-wizard-type="step-content">
                  <h4 class="mb-10 font-weight-bold text-dark">Enter Your Business Details</h4>
                  <!--begin::Select-->
                  <div class="row" <?php echo $disablecss; ?>>
                    <div class="form-group col-lg-12">
                     <label>Company Name <span class="requried">*</span></label>
                      <input type="text" class="form-control required" name="company" value="<?php echo $register_data['company']!=""?$register_data['company']:$invitation['company_name'];?>" placeholder="<?php echo $placeholder['company']; ?>"  />
                  </div>

                  <div class="form-group col-lg-12">
                   <label>Website</label>
                    <input type="text" class="form-control" value="<?php echo $register_data['website_url'];?>" name="website_url" placeholder="<?php echo $placeholder['website_url']; ?>"  />
                </div>
                <div class="form-group col-lg-6">
                 <label>Year of Establishment</label>
                  <input type="text" class="form-control"  id="kt_datepicker_2" name="established_year" value="<?php echo $register_data['established_year'];?>" placeholder="<?php echo $placeholder['established_year']; ?>"  />
              </div>


              <div class="form-group col-xl-6 w-full">
               <label>Business Type</label></br>
               <select name="business_type" class="form-control form-select js-example-basic-single">
                <option value="">Select Any One</option>
                <?php foreach($businesstypes as $buztype) { ?>
                  <option value="<?php echo $buztype?>" <?php echo $register_data['business_type']==$buztype?'selected':'';?> ><?php echo $buztype; ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group col-xl-6 w-full">
             <label>Size</label></br>
             <select name="business_size" class="form-control form-select js-example-basic-single">
              <option value="">Select Any One</option>
              <?php foreach($businesssize as $buztype) { ?>
                <option value="<?php echo $buztype?>" <?php echo $register_data['business_size']==$buztype?'selected':'';?>><?php echo $buztype; ?></option>
              <?php } ?>
            </select>

          </div>

          <div class="form-group col-xl-6 w-full">
           <label>Industry</label></br>
           <select name="business_status" class="form-control form-select js-example-basic-single">
            <option value="">Select Any One</option>
            <?php foreach($businessstatus as $buztype) { ?>
              <option value="<?php echo $buztype?>" <?php echo $register_data['business_status']==$buztype ? 'selected':'';?> ><?php echo $buztype; ?></option>
            <?php } ?>
          </select>

        </div>



        <div class="form-group col-lg-12">
          <label>Business Description</label></br>
          <textarea type="text" class="form-control" name="vendor_po_notes" ><?php echo $register_data['vendor_po_notes'];?></textarea>
        </div>


      </div>

    </div>
    <!--end: Wizard Step 3-->


  </fieldset>
  <h3> Communication <small>Vendor contact details</small></h3>
  <fieldset>
    <!--begin: Wizard Step 3-->
    <div class="pb-5" data-wizard-type="step-content">
      <h4 class="mb-10 font-weight-bold text-dark">Enter Your Business Details</h4>
      <!--begin::Select-->
      <div class="row" <?php echo $disablecss; ?>>
        <div class="form-group col-lg-12">
          <label>Billing Address</label>
          <textarea type="text" class="form-control" id="billing_address" name="billing_address" ><?php echo $register_data['billing_address'];?></textarea>
        </div>
        <div class="form-group col-lg-6">
         <label>Post Code</label>
          <input type="text" class="form-control"id='billing_post_code' value="<?php echo $register_data['billing_post_code'];?>" name="billing_post_code" placeholder="<?php echo $placeholder['post_code']; ?>"  />
      </div>

      <div class="form-group col-lg-6 w-full">
       <label>Country</label></br>
       <select id="kt_select2_102" class="form-select js-example-basic-single" name="billing_country"  onchange="selectedcategory(this.value,'kt_select2_101','states','country_id','selectedbillcountry')">
        <option value="">Select Any One</option>
        <?php foreach($countrieslist as $countryvalue) { ?>
          <option <?php echo $register_data['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group col-xl-6 w-full">
     <label>State </label></br>
     <select id="kt_select2_101" name="billing_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_103','cities','state_id','selectedbillstate')">
      <option value="">Select Any One</option>
      <?php foreach($states as $states1) { ?>
          <option <?php echo $register_data['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
        <?php } ?>
    </select>
  </div>

  <div class="form-group col-xl-6 w-full">
   <label>City</label></br>
   <select id="kt_select2_103" name="billing_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
    <option value="">Select Any One</option>
     <?php foreach($cities as $cities1) { ?>
          <option <?php echo $register_data['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
        <?php } ?>
  </select>
</div>
<div class=" col-lg-6 mt-10"> 
 <div class="checkbox-fade fade-in-primary"> 
  <label class="form-label"> 
    <input type="checkbox" style="display:none" <?php if($register_data['shipping_address']==$register_data['billing_address'] && $rowid!="") { echo 'checked'; } else { echo ''; } ?> id="is_same" onclick="sameaddress()">   
    <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span><span>
    Billing Address same as Shipping Address</span>
  </label>
</div> </div>

<div class="form-group col-lg-12">
  <label>Shipping Address</label>
  <textarea type="text" class="form-control" name="shipping_address" id="shipping_address"><?php echo $register_data['shipping_address'];?></textarea>
</div>

<div class="form-group col-lg-6">
 <label>Post Code</label></br>
  <input type="text" class="form-control outline-blue" name="shipping_post_code" id="shipping_post_code" value="<?php echo $register_data['shipping_post_code'];?>" placeholder="<?php echo $placeholder['post_code']; ?>"  />
</div>

<div class="form-group col-xl-6 w-full">
 <label>Country</label></br>
 <select id="kt_select2_104" class="form-select js-example-basic-single"  name="shipping_country" onchange="selectedcategory(this.value,'kt_select2_105','states','country_id','selectedshipcountry')">
  <option value="">Select Any One</option>
  <?php foreach($countrieslist as $countryvalue) { ?>
    <option <?php echo $register_data['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
  <?php } ?>
</select>
</div>


<div class="form-group col-xl-6 w-full">
 <label>State</label></br>
 <select id="kt_select2_105" name="shipping_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_106','cities','state_id','selectedshipstate')">
  <option value="">Select Any One</option>
    <?php foreach($states as $states1) { ?>
          <option <?php echo $register_data['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
        <?php } ?>
  
</select>
</div>

<div class="form-group col-xl-6 w-full">
 <label>City</label></br>
 <select id="kt_select2_106" name="shipping_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
  <option value="">Select Any One</option>
     <?php foreach($cities as $cities1) { ?>
          <option <?php echo $register_data['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
        <?php } ?>
          </select>
        </div>
      </div>
    </div>
  </fieldset>

                                              <h3> Tax Information <small>Vendor tax details</small></h3>
                                              <fieldset>
                                                <!--end: Wizard Step 3-->
                                                <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                                  <h4 class="mb-10 font-weight-bold text-dark">Vendor Tax Information</h4>
                                                  <!--end::Input-->
                                                  <div class="row" <?php echo $disablecss; ?>>

                                                    <div class="form-group col-lg-6">
                                                     <label>PAN Number<span class="requried">*</span></label>
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control outline-blue required" value="<?php echo $register_data['pan_no'];?>" name="pan_no" placeholder="<?php echo $placeholder['pan_no'];?>">
                                                      
                                                    </div>
                                                    <!--       <span class="form-text text-muted"><?php echo $helptext['pan_no'];?> </span> -->

                                                  </div>
                                                  
                                                  <div class="form-group col-lg-6">
                                                   <label>VAT Number<span class="requried">*</span></label>
                                                   <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control outline-blue required"  name="vat_number" value="<?php echo $register_data['vat_number'];?>" placeholder="<?php echo $placeholder['vat_number'];?>">
                                                    
                                                  </div>
                                                  <!--    <span class="form-text text-muted"><?php echo $helptext['vat_number'];?> </span> -->

                                                </div>
                                                <div class="form-group col-lg-6">
                                                 <label>TIN Number</label>
                                                 <div class="input-group input-icon-right">
                                                  <input type="text" class="form-control" value="<?php echo $register_data['tin_number'];?>" name="tin_number" placeholder="<?php echo $placeholder['tin_number'];?>">
                                                  
                                                </div>
                                                <!--   <span class="form-text text-muted"><?php echo $helptext['tin_number'];?> </span> -->

                                              </div>
                                              <div class="form-group col-lg-6">
                                               <label>CST Number<span class="requried">*</span></label>
                                               <div class="input-group input-icon-right">
                                                <input type="text" class="form-control required"  value="<?php echo $register_data['cst_reg_no'];?>" name="cst_reg_no" placeholder="<?php echo $placeholder['cst_reg_no'];?>">
                                              </div>
                                              <!--   <span class="form-text text-muted"><?php echo $helptext['cst_reg_no'];?> </span> -->

                                            </div>
                                            <div class="form-group col-lg-6">
                                             <label>GST Number</label>
                                             <div class="input-group input-icon-right">
                                              <input type="text" class="form-control" value="<?php echo $register_data['gstin'];?>"  name="gstin" placeholder="<?php echo $placeholder['gstin'];?>">
                                              
                                            </div>
                                            <!--       <span class="form-text text-muted"><?php echo $helptext['gstin'];?> </span> -->

                                          </div>


                                          


                                        </div>
                                      </div>
                                      
                                      
                                    </fieldset>
                                    <h3> Bank Details </br><small>Vendor bank details</small></h3>
                                    <fieldset>
                                      <!-- end wizard 3 -->
                                      <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current" <?php echo $disablecss; ?>>
                                        <h4 class="mb-10 font-weight-bold text-dark">Vendor Bank Details</h4>
                                        <!--end::Input-->
                                        <div class="row">

                                          <div class="form-group col-lg-6">
                                           <label>Bank Account Name<span class="requried">*</span></label>
                                           <div class="input-group input-icon-right">
                                            <input type="text" class="form-control required" value="<?php echo $register_data['bank_account_name'];?>" name="bank_account_name" placeholder="<?php echo $placeholder['bank_ac_name'];?>">
                                            
                                          </div>
                                          <!--    <span class="form-text text-muted"><?php echo $helptext['bank_ac_name'];?> </span> -->

                                        </div>
                                        
                                        <div class="form-group col-lg-6">
                                         <label>Bank Name<span class="requried">*</span></label>
                                         <div class="input-group input-icon-right">
                                          <input type="text" class="form-control required"  name="bank_name" value="<?php echo $register_data['bank_name'];?>" placeholder="<?php echo $placeholder['bank_name'];?>">
                                          
                                        </div>
                                        <!--    <span class="form-text text-muted"><?php echo $helptext['bank_name'];?> </span> -->

                                      </div>
                                 <?php /** ?>     <div class="form-group col-lg-6">
                                       <label>Account Type</label>
                                       <div class="input-group input-icon-right">
                                        <input type="text" class="form-control" value="<?php echo $register_data['account_type'];?>" name="account_type" placeholder="<?php echo $placeholder['account_type'];?>">
                                        
                                      </div>
                                      <!--   <span class="form-text text-muted"><?php echo $helptext['account_type'];?> </span> -->

                                    </div>
                                    <?php **/ ?>

                                    <div class="form-group col-xl-6 w-full">
                                     <label>Account Type</label></br>
                                     <select id="kt" class="form-select js-example-basic-single" name="account_type">
                                       <option value="">Select Any One</option>

                                       <?php
                                       foreach($genc_code_list as $genc_code_lists) { ?>
                                        <option value="<?php echo $genc_code_lists['genc_detail_code']; ?>" <?php echo $register_data['account_type']==$genc_code_lists['genc_detail_code']?"selected":""; ?> ><?php echo $genc_code_lists['genc_detail_name']; ?></option>
                                      <?php } ?>
                                    </select>  
                                  </div>



                                  <div class="form-group col-lg-6">
                                   <label>Bank Account Number<span class="requried">*</span></label>
                                   <div class="input-group input-icon-right">
                                    <input type="text" class="form-control required"  value="<?php echo $register_data['bank_account_no'];?>" name="bank_account_no" placeholder="<?php echo $placeholder['bank_account_no'];?>">

                                  </div>
                                  <!--     <span class="form-text text-muted"><?php echo $helptext['bank_account_no'];?> </span> -->

                                </div>
                                <div class="form-group col-lg-6">
                                 <label>IFSC Code</label>
                                 <div class="input-group input-icon-right">
                                  <input type="text" class="form-control" value="<?php echo $register_data['ifsc_code'];?>"  name="ifsc_code" placeholder="<?php echo $placeholder['ifsc_code'];?>">

                                </div>
                                <!--   <span class="form-text text-muted"><?php echo $helptext['ifsc_code'];?> </span> -->

                              </div>
                              <div class="form-group col-lg-6">
                               <label>Bank Address</label>
                               <div class="input-group input-icon-right">
                                 <textarea type="text" class="form-control" id="bank_address" name="bank_address" ><?php echo $register_data['bank_address'];?></textarea>

                               </div>
                               <!-- <span class="form-text text-muted"><?php echo $helptext['bank_address'];?> </span> -->

                             </div>
                           </div>
                         </div>


                       </fieldset>
                       <h3> Upload Documents <small>Please upload necessary documents</small></h3>
                       <fieldset>
                        <div class="pb-5" data-wizard-type="step-content">
                          <div class="row mb-3">
                            <h4 class="mb-10 font-weight-bold text-dark col-lg-6">List of Documents to be Submitted</h4>
                           <!--  <div class="col-lg-6 text-right">
                             <button  type="button" class="btn btn-primary waves-effect waves-light" onclick="reaprovedoc('<?php //echo $rowid; ?>')">Submit For Approval Documnets</button>
                           </div> -->
                         </div>
                         <div class="row" <?php echo $disablecss; ?>>

                          <?php
 //echo "<pre>"; print_r($attach_doccategory);print_r($attach_filename); echo "</pre>";
                          foreach ($document_lists as $dockey => $docvalue){ 
                            $vendor_documen = $vendor_documents[$dockey];
                            $filestatus = $_SESSION['status'][$vendor_documen['current_status']];

                            ?>							

                            <div class="col-md-6 col-sm-6 col-xs-12 mb-10 " >

                              <input type="hidden" name="document_id[]" value="<?php echo $dockey;?>" >
                              <input type="hidden" name="document_file_id[]" value="<?php echo $vendor_documen['id'];?>" >
                              <div class="pricing ui-ribbon-container custom-ribbon-accordian">
                               <?php if($vendor_documen['current_status'] ==ST_REJECTED){ ?>
                                 <div class="ribbon ribbon-clip ribbon-right ribbanid_<?php echo $vendor_documen['id']; ?>">
                                  <div class="ribbon-target f-10 bg-danger" >
                                    <span class="ribbon-inner" ></span> <?php echo "Rejected"; ?> 
                                  </div>
                                </div> 
                              <?php } ?>
                              <div class="title" style="border-radius: 5px;">
      <a  class="text-white" <?php if($vendor_documen['filename']!=""){ ?>  href="<?php  echo $File_Uploads_Path.$vendor_documen['filename'];   ?>"  target="_blank" <?php } ?> > <h6 class="f-14 m-l-10"> <?php if($vendor_documen['filename']!=""){ echo '<i class="ti-cloud-down"></i>'; } ?> <?php echo ucwords($docvalue['document_name']); ?> <?php if($vendor_documen['filename']!="" && $vendor_documen['current_status'] != ST_REJECTED){  ?><label class="form-label label label-info"    style="color: <?php echo "#". $filestatus['color']; ?>;    background:  <?php echo "#".strval($filestatus['background_color'] ); ?>; border-color: <?php echo "#".strval($filestatus['background_color']); ?>;"><?php echo $filestatus['name']; ?></label><?php } ?></h6> </a>
                              </div>
                              <div class="x_content">
                                <div class="">
                                  <div class="pricing_features">    
                                    <div style="  box-shadow: 0px 1px 9px #cccccc; padding: 2px 10px 2px 10px;" class="alert alert-custom alert-white fade show" role="alert">
                                      <div class="alert-text">

                                        <div class="d-flex align-items-center mb-0">
                                          <div class="symbol symbol-35 symbol-success mr-3 mt-1">
                                            <?php if($vendor_documen['filename']!=""){ if($vendor_documen['current_status'] == ST_REJECTED){ ?> 
                                             <i class="fa fa-times text-danger"></i> 
                                           <?php }elseif($vendor_documen['current_status'] == ST_APPROVED){ ?>  
                                             <i class="fa fa-thumbs-up"></i>
                                           <?php }else{  ?>
                                             <i class="fa fa-hourglass-half"></i>
                                           <?php } }else{ ?>
                                             <i class="fa fa-upload"></i>
                                           <?php }     ?>
                                         </div>
                                         <div class="d-flex flex-column font-weight-bold w-100 p-l-20 p-r-20 p-t-5 p-b-5 w-full">
                                          <select name="document_categoryid[]" class="form-control js-example-basic-single">
                                           <?php if(count((array)$doctypes[$dockey]) == 0){ ?>
                                            <option value="0"><?php echo ucwords($docvalue['document_name']); ?></option>
                                          <?php }else{ ?>
                                           <option value="">Select Any One</option>
                                           <?php foreach($doctypes[$dockey] as $docltype) { ?>
                                            <option value="<?php echo $docltype['id']?>" <?php echo $vendor_documen['document_id']==$docltype['id'] ? 'selected':'' ?>><?php echo $docltype['document_type']; ?></option>
                                          <?php } } ?>
                                        </select>
                                      </div>
                                      <?php if($vendor_documen['current_status'] != ST_APPROVED){ ?>
                                       <div class="d-flex font-weight-bold">   
                                        <div class="image-input image-input-empty image-input-outline mr-10" id="kt_image_5"  style=" height:0px; ">
                                          <label class="btn-hover-text-primary  <?php //echo $value['document_shared']!=""?"mt-3":"mt-1"; ?>" data-action="change" data-toggle="tooltip" title="" data-original-title="Uploade File">
                                            <i class="ti-clip" style="font-size: 20px;"></i>
                                            <input type="file" class="custom-file-input" id="customFile" name="fileToUpload[]"  onchange="checkFileSelect(event,'<?php echo $dockey; ?>')"/>			
                                            <input type="hidden" name="hidden_fileToUpload[]" value="<?php echo $vendor_documen['filename']; ?>">
                                          </label>
                                        </div>
                                      </div>
                                    <?php } ?>	
                                  </div>
                                  <p class="mb-0 text-right f-10 " id="fileattach_<?php echo $dockey; ?>"></p>
                                </div>
                              </div>




                            </div>
                          </div>

                        </div>
                      </div>
                    </div>



                  <?php } ?>	


							  <?php /** ?>
							  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center ">
                                <tr>
                                  <th  class="thheader" width="2%">#</th>
                                  <th  class="thheader" width="30%">Document</th>
                                  <th  class="thheader" width="25%">Category</th>
                                  <th  class="thheader" width="36%">Attach Files</th>
                                  <?php if($rowid!='') { ?>
                                    <th  class="thheader" width="7%">View</th>
                                  <?php } ?>

                                </tr>
                                <?php
                                $sno=0;
                                foreach ($document_lists as $dockey => $docvalue) { $sno++;?>
                                  <tr>
                                    <td> <?php echo $sno; ?> </td>
                                    <td align="left"> <input type="hidden" name="document_id[]" value="<?php echo $dockey;?>" ><?php echo $docvalue['document_name']; ?> </td>
                                    <td ><select name="document_categoryid<?php echo $dockey;?>" class="form-select js-example-basic-single">
                                      <option value="">Select Any One</option>
                                      <?php foreach($doctypes[$dockey] as $docltype) { ?>
                                        <option value="<?php echo $docltype['id']?>" <?php echo $attach_doccategory[$dockey]==$docltype['id'] ? 'selected':'' ?>><?php echo $docltype['document_type']; ?></option>
                                      <?php } ?>
                                    </select></td>
                                    <td> 
                                                    
                                                    <div class="custom-file">
                                                      <input type="hidden" name="hidden_fileToUpload<?php echo $dockey;?>" value="<?php echo $attach_filename[$dockey]; ?>">
                                                      <input type="file" class="custom-file-input" id="customFile" name="fileToUpload<?php echo $dockey;?>" />
                                                      <label class="custom-file-label" for="customFile"></label>
                                                    </div>

                                                  </td>
                                                  <?php if($rowid!='') { ?>
                                                   <td> <?php if($attach_filename[$dockey]!='') { ?> <a href="<?php echo $File_Uploads_Path.$attach_filename[$dockey];?>" target="_blank"> <i class="ti-cloud-down text-primary f-18"></i>  </a>
                                                     <?php } ?> </td>
                                                   <?php } ?>

                                                 </tr>
                                               <?php } ?>
                                             </table>
                                             <?php **/ ?>


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
                         <!-- Form wizard with validation card end -->














                       </div>
                     </div>
                   </div>
                   <!--end::Entry-->
                 </div>
                 <!--end::Content-->
               </div>
               <!--end::Wrapper-->
             </div>
             <!--end::Page-->
           </div>
           <!-- Modal-->
           <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog " >
              <div class="modal-content"  id="popup1">

              </div>
            </div>
          </div>
          
          <!--end::Demo Panel-->

          <div class="footer bg-white p-1" id="kt_footer">

            <!--begin::Container-->
            <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">

              <!--begin::Copyright-->
              <div class="text-dark order-2 order-md-1">
                <span class="text-muted font-weight-bold mr-2">2021©</span>
                <a href="https://infisuite.com/" target="_blank" class="text-dark-75 text-hover-primary">Infisuite</a>
              </div>

              <!--end::Copyright-->

              <!--begin::Nav-->
              <div class="nav nav-dark">
                <a href="https://infisuite.com/about-us/" target="_blank" class="nav-link pl-0 pr-5">About</a>
                <a href="https://infisuite.com/contact/" target="_blank" class="nav-link pl-0 pr-0">Contact</a>
              </div>

              <!--end::Nav-->
            </div>

            <!--end::Container-->
          </div>
          <div id="kt_modal_dynamic_popupxxl" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl"  style="max-width: 98% !important;" >
              <div class="modal-content"  id="dynamic_popupxxl" style="border-radius: 20px;">

              </div>
            </div>
          </div>
          <!-- [ chat user list ] start -->





          <div id="sidebar" class="users p-chat-user showChat width25">
            <div class="had-container">
              <div class="p-fixed users-main width25">
                <div class="user-box">
                  <div class="chat-search-box">
                    <a class="back_friendlist">
                      <i class="ti-close"></i>
                    </a>
                    <div class="right-icon-control">
                      <div class="input-group input-group-button">
                        <input type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search a notiffication">
                        <button class="btn btn-primary waves-effect waves-light" type="button">
                          <i class="ti-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="main-friend-list p-2">
                    <ul class="nav md-tabs infisuite_tab tabs mb-2" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab">Information</a>
                      </li>
                    </ul>
                    <div class="tab-content tabs card-block ">
                      <div class="tab-pane active custom-scroll" style="height: 550px;" id="home1" role="tabpanel" >
                        <?php $aci = 0; foreach ($notificationlist as  $value) { 
    if($value['Notification_Type'] == 'ACTION' || $value['Notification_Type'] == 'INFORMATION'){ //print_r($value);
      $option_url = $value['option'];
      $recent_notification = $value;
      if($value['option']==""|| $value['option']==Null ){
        $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$value['Notification_Table'],1);
        $option_url = $tabledata['insert_page_name'];
			//$nrid = "id:".$value['option_id'];
			//$link = "index.php?option=".$option_url."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($value['id']);
      }
      ?>
      <div class="media userlist-box waves-effect waves-light notification-box-shadow p-2 bg-white mb-1" >
        <a class="" href="#!">
          <div class="img-radius img-50 align-top m-r-15 text-center userimageround pt-3">
            <span class="font-size-h3 font-weight-boldest"><?php echo initials(str_replace('_', ' ',$value['Notification_Table'])); ?></span>
          </div>
        </a>
        <a class="" href="#!">
          <div class="media-body">
            <div class="chat-header"><?php echo $value['Notification_Message']; ?></br>
              <small class="f-w-200 m-t-20 d-block"><?php echo notificationdatesdiff($value['Notification_DateTime']); ?></small>
            </div>
          </div>
        </a>
      </div>
      <?php $aci++; }  } if($aci == 0){?>
        <a href="javascript:;" class="kt-notification__item">
          <div class="kt-notification__item-details">
            <div class="kt-notification__item-title">
              All caught up!  No new notifications.
            </div>

          </div>
        </a>
      <?php } ?>
    </div>


  </div>




</div>
</div>
</div>
</div>
</div>

<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script> 
<script src="assets/pages/waves/js/waves.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>

<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<!-- Custom js menu js -->
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/vertical/horizontal-layout.min.js"></script>
<script type="text/javascript" src="assets/js/script.min.js"></script>
<!--------- Start wizard js -------------------->
<!--Forms - Wizard js-->
<script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
<script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
<script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
<script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>
<!--------- End wizard js -------------------->

    <!-- Select 2 js -->
    <script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
    <!-- Multiselect js -->
    <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
    </script>
    <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>






<!-- Date picker js -->
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>

<!-- Date-dropper js -->
<script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>
<!-- Switch component js -->
<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>     

<!------- Genral item work ------------------------------>
<script> var table_name ='employees';  </script>
<script type="text/javascript" src="assets/js/custom.js"></script>
<!-- scrollbars modernizr js -->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
<!-- modal window POPUP  modalEffects js nifty modal window effects -->
<script src="assets/js/classie.js"></script>
<script type="text/javascript" src="assets/js/modalEffects.js"></script>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<!-- calender js -->
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>

<script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
<script type="text/javascript" src="assets/pages/notification/notification.js"></script>

<!-- sweet alert js Rohan Added-->
<script type="text/javascript" src="assets/bower_components/sweetalert/js/sweetalert.min.js"></script>
<!-- <script type="text/javascript" src="assets/js/modal.js"></script> -->

<?php /** ?>
<!-- [ chat user list ] end -->
          <script>
           var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";
         </script>
         <!--begin::Global Config(global config for global JS scripts)-->
         <script>
           var KTAppSettings = {
            "breakpoints": {
             "sm": 576,
             "md": 768,
             "lg": 992,
             "xl": 1200,
             "xxl": 1400
           },
           "colors": {
             "theme": {
              "base": {
               "white": "#ffffff",
               "primary": "#3699FF",
               "secondary": "#E5EAEE",
               "success": "#1BC5BD",
               "info": "#8950FC",
               "warning": "#FFA800",
               "danger": "#F64E60",
               "light": "#E4E6EF",
               "dark": "#181C32"
             },
             "light": {
               "white": "#ffffff",
               "primary": "#E1F0FF",
               "secondary": "#EBEDF3",
               "success": "#C9F7F5",
               "info": "#EEE5FF",
               "warning": "#FFF4DE",
               "danger": "#FFE2E5",
               "light": "#F3F6F9",
               "dark": "#D6D6E0"
             },
             "inverse": {
               "white": "#ffffff",
               "primary": "#ffffff",
               "secondary": "#3F4254",
               "success": "#ffffff",
               "info": "#ffffff",
               "warning": "#ffffff",
               "danger": "#ffffff",
               "light": "#464E5F",
               "dark": "#ffffff"
             }
           },
           "gray": {
            "gray-100": "#F3F6F9",
            "gray-200": "#EBEDF3",
            "gray-300": "#E4E6EF",
            "gray-400": "#D1D3E0",
            "gray-500": "#B5B5C3",
            "gray-600": "#7E8299",
            "gray-700": "#5E6278",
            "gray-800": "#3F4254",
            "gray-900": "#181C32"
          }
        },
        "font-family": "Poppins"
      };
    </script>

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
    <!-- Custom js -->
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
    <!--     <script src="assets/js/pcoded.min.js"></script> -->
    <!--    <script src="assets/js/vertical/vertical-layout.min.js"></script> -->
    <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <!--    <script type="text/javascript" src="assets/js/script.js"></script> -->
    <script type="text/javascript" src="assets/js/custom.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
    <script type="text/javascript" src="assets/pages/notification/notification.js"></script>

    <?php **/ ?>
  </body>
  <!--end::Body-->
  </html>