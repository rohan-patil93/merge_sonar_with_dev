<style>
 /*.content.clearfix {
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
}*/


</style>
<?php
 //echo "<pre>"; 
 //print_r($_POST);
$_SESSION['apply_js']['wizardjs']=1;
$table_name = "candidates";
if (isset($_POST['first_name'])) {
  $hid = $_POST['hid'];
  $trn_id = explode(':', $hid);
  //echo "<pre>".$_FILES['file']['name']['2']; print_r($_POST); print_r($_FILES); exit;
  foreach ($images as  $value_image) {
    $hid_image = "hid".$value_image;

  }
  $_POST['is_active'] ='0';
  $_POST['current_status'] ='0';
  $_POST['company_id'] =$_SESSION['company_id'];;
  foreach ($_POST['company_name'] as $key=> $company_name) {
    $compay_file = $_POST['hidden_file'][$key];;
    if($_FILES['file']['name'][$key]!=""){
      $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$trn_id['1']."/Documents";
      //echo $path;
      
      $compay_file = imageuploadwitharray($path,'file','','',$key);
    }
    $ex['company_name'] = $company_name;
    $ex['job_descrption'] = $_POST['job_descrption'][$key];
    $ex['work_from'] = $_POST['work_from'][$key];
    $ex['work_to'] = $_POST['work_to'][$key];
    $ex['compay_file'] = $compay_file;
    //print_r($ex); exit;
    $exp[] = $ex;
  }
  $_POST['candidate_experiances'] = json_encode($exp,1);
  $ex =array();
  foreach ($_POST['institute_name'] as $key=> $institute_name) {
   $qualification_file = $_POST['hidden_qualification_file'][$key];;
   if($_FILES['qualification_file']['name'][$key]!=""){
    $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$trn_id['1']."/Documents";
      //echo $path;

    $qualification_file = imageuploadwitharray($path,'qualification_file','','',$key);
  }
  $ex['institute_name'] = $institute_name;
  $ex['education_type'] = $_POST['education_type'][$key];
  $ex['start_date'] = $_POST['start_date'][$key];
  $ex['end_date'] = $_POST['end_date'][$key];
  $ex['score'] = $_POST['score'][$key];
  $ex['qualification_file'] = $qualification_file;

  $edu[] = $ex;
}
$_POST['candidate_qualifications'] = json_encode($edu,1);
$ex =array();
foreach ($_POST['award_name'] as $key=> $award_name) {
  $ex['award_name'] = $_POST['award_name'];
  $ex['award_description'] = $_POST['award_description'][$key];
  $ex['award_year'] = $_POST['award_year'][$key];
  $awd[] = $ex;
}
$_POST['candidate_awards'] = json_encode($awd,1);
$ex =array();
foreach ($_POST['dependent_name'] as $key=> $dependent_name) {
  $ex['dependent_name'] = $dependent_name;
  $ex['dependent_relationship_type'] = $_POST['dependent_relationship_type'][$key];
  $ex['dependent_relationship'] = $_POST['dependent_relationship'][$key];
  $ex['dependent_date_of_birth'] = $_POST['dependent_date_of_birth'][$key];
  $dep[] = $ex;
}
$_POST['employee_dependents'] = json_encode($dep,1);
          //print_r($exp);
     //echo "<pre>"; print_r($_POST); exit; 

$unset =  array('company_name','job_descrption','work_from','work_to','institute_name','institute_name','education_type','start_date','end_date','score','dependent_name','dependent_relationship_type','dependent_relationship','dependent_date_of_birth','hid','hidimage','award_name','award_description','award_year','hidden_file','hidden_qualification_file');
foreach ($unset as  $set) {
 unset($_POST[$set]);
}

$_POST['date_of_birth'] = date('Y-m-d',strtotime($_POST['date_of_birth']));
$_POST['wedding_date'] = date('Y-m-d',strtotime($_POST['wedding_date']));
$_POST['blood_group'] = $_POST['blood_group'];
$_POST['no_of_reportees'] = $_POST['no_of_reportees'];
$_POST['first_pay'] = $_POST['first_pay'];
$_POST['current_pay'] = $_POST['current_pay'];
$_POST['first_role'] = $_POST['first_role'];
$_POST['candidate_current_role'] = $_POST['candidate_current_role']; 
$_POST['performance_ratings'] = $_POST['performance_ratings'];
$_POST['gaps_in_employment_reason'] = $_POST['gaps_in_employment_reason'];

    //echo "<pre>"; print_r($_POST);    //echo "<pre>"; print_r($_POST); exit;

    //print_r($_SESSION);

$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$trn_id['1'];
    //echo $path;
     //exit;
$_POST['image'] = imageupload($path,'image',$_POST[$hidimage]);

    if($hid!=""){ //echo "ravi"; exit;
    $_POST['updated_date'] = nowDate();
    $_POST['updated_by'] = $_SESSION['userid'];
          //$urldata = urlencode(json_encode($data,true));
    $url = API."?mode=updaterowbasepimarydata&rid=".$hid."&table=candidates&method=post";
    $response = file_post_contents($url, $_POST, 'update');
          //echo $response;  exit;
      }else{ //echo "shankar"; exit;
      $_POST['created_date'] = nowDate();
      $_POST['created_by'] = $_SESSION['userid'];
      $url = API."?mode=adddata&table=candidates&method=post";
      $response = file_post_contents($url, $_POST, 'insert');
         // echo $response;

    }
    header("location:index.php?option=COM_LIST_CANDIDATES"); exit;
  }
  $role['email_id'] =  $_SESSION['user_email'];
  $role['phone'] =  $_SESSION['user_mobile'];
  if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
    $vdata_details = array();
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid); 
    $trn_id = explode(':', $rid);
    $candidate_id = $trn_id['1'];
    $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=candidates";
      //echo $rid;
    $response = callAPI($url);
      //echo $response; exit;
    $role = json_decode($response,1);
     // print_r($role);
  }
  $data2["where"] = array('is_active'=> '0');
  $costdata = urlencode(json_encode($data2));
  $costurl = API."?mode=gettabledetailswithconditions&table=job_grade&data=".$costdata;
  $costdata = callAPI($costurl);
  $grade = json_decode($costdata,1);

  $datavender["where"] = array('is_active'=> '0'); 
  $datavender["field"] = array('is_active','id','display_name');
  $costdatavender = urlencode(json_encode($datavender));
  $venderurl = API."?mode=gettabledetailswithconditions&table=vendors&data=".$costdatavender;
  $venderdata = callAPI($venderurl);
  $venderdata = json_decode($venderdata,1);

  $dataedu["where"] = array('is_active'=> '0','genc_code'=>'EDUCATIONTYPE');
  $costdataedu = urlencode(json_encode($dataedu));
  $eduurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$costdataedu;
  $edutypedata = callAPI($eduurl);
  $edutypedata = json_decode($edutypedata,1);

  $datarela["where"] = array('is_active'=> '0');
  $costdatarela = urlencode(json_encode($datarela));
  $relaurl = API."?mode=gettabledetailswithconditions&table=relations&data=".$costdatarela;
  $relatypedata = callAPI($relaurl);
  $relatypedata = json_decode($relatypedata,1);

  $datapaytmp["where"] = array('is_active'=> '0','grade_id'=>$role['pay_template']);
  $datapaytmpd = urlencode(json_encode($datapaytmp));
  $tempurl = API."?mode=gettabledetailswithconditions&table=job_grade_templates&data=".$datapaytmpd;
  $tempdata = callAPI($tempurl);
  $tempdata = json_decode($tempdata,1);
  
  
  $databloodgroup["where"] = array('is_active'=> '0','genc_code'=>'BLOODGROUPS');
  $costdatabloodgroup = urlencode(json_encode($databloodgroup));
  $bloodgroupurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$costdatabloodgroup;
  $bloodgrouptypedata = callAPI($bloodgroupurl);
  $blood_groups_details = json_decode($bloodgrouptypedata,1);
   //exit;
     // print_r($relatypedata); 
  ?>
  <script type="text/javascript">
   function displaystausdrop(id,table,status_id){
       //alert("id");
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
     //alert("data");
    //var table_name = document.getElementById('duplicate_table_name').value;
    jQuery.ajax({
     url: "views/custom_insert_status_popup.php",
     data:'table=candidates'+'&status='+status+'&rid='+id,
     type: "POST",
     success:function(data){
       $('#popup123').html(data);
       $('#kt_modal_KTDatatable_remote123').modal("show");
     },
     error:function (){}
   });
  }
  function viewpopup(){
       //alert("hai");
       jQuery.ajax({
         url: "views/custom_offer_letter_popup.php",
         data:'table=letters',
         type: "POST",
         success:function(data){
           $('#popup1').html(data);
           $('#kt_modal_KTDatatable_remote').modal("show");
           $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
            submit();
          });  
         },
         error:function (){}
       });
     }
     function gradechange(val){
       alert(val);
       $.ajax({
         url: "ajax.php",
         data:'page=dynmictabledata&table=job_grade_templates&field=id&value=name&rid=grade_id&fid='+val,
         type: "POST",
         success:function(data){
           console.log(data);

           $("#grade").html("<select class='form-select js-example-basic-single' name='pay_template'>"+data+"</select>");
         },
         error:function (){}
       });
     }

     function adddeprows(){
      // var count = $(".itemRow").length;
      //  count++;
      jQuery(document).ready(function() {    
       KTBootstrapDatepicker.init();
     });
      var htmlRows = '';
      htmlRows += '<tr>'; 
      htmlRows += '<td><input type="text" name="dependent_name[]"  class="form-control " autocomplete="off">  </td>'; 
      htmlRows += '<td><select class="form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php   } ?></select>  </td>'; 
     // htmlRows += '<td><input type="text" name="dependent_relationship[]"  class="form-control " autocomplete="off">  </td>'; 
     htmlRows += '<td><input type="date" name="dependent_date_of_birth[]"  class="form-control "  id="kt_datepicker_31" autocomplete="off">  </td>';   
     htmlRows += '</tr>';
     $('#deptable').append(htmlRows);
   }
   function addedurows(){
      // var count = $(".itemRow").length;
      //  count++;
     // document.write("<script src='// ../assets/js/pages/myscrpit/date-picker.js'><\/script>");
      //datepicker();
      jQuery(document).ready(function() {    
       KTBootstrapDatepicker.init();
     });
      var htmlRows = '';
      htmlRows += '<tr>'; 
      htmlRows += '<td><input type="text" name="institute_name[]"  class="form-control " autocomplete="off">  </td>'; 
      htmlRows += '<td><select class="form-select js-example-basic-single" name="education_type" > <option value="0"> Select Any One</option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option> <?php   } ?></select> </td>'; 
      htmlRows += '<td><input type="date" name="start_date[]"  class="form-control " id="kt_datepicker_35" autocomplete="off">  </td>'; 
      htmlRows += '<td><input type="date" name="end_date[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>'; 
      htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
      htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;">  <span class="svg-icon svg-icon-primary svg-icon-lg"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/><path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/></g></svg></span><input type="file" name="file[]" />  <input type="hidden" name="hidden_file[]" value=""/> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>'

      htmlRows += '</tr>';
      $('#edutable').append(htmlRows);
    }
    function addexpreencerows(){
      // var count = $(".itemRow").length;
       // count++;
       jQuery(document).ready(function() {    
         KTBootstrapDatepicker.init();
       });
       var htmlRows = '';
       htmlRows += '<tr>'; 
       htmlRows += '<td><input type="text" name="company_name[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="text" name="job_descrption[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="date" name="work_from[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="date" name="work_to[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>';   
     //  htmlRows += '<td>     <div class="image-input image-input-empty image-input-outline" id="kt_image_5" > <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">  <i class="icon-2x text-dark-50 flaticon-tool-1"></i>  <input type="file" name="file[]" /> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">  <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>';
     htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;">  <span class="svg-icon svg-icon-primary svg-icon-lg"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/><path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/></g></svg></span><input type="file" name="file[]" />  <input type="hidden" name="hidden_file[]" value=""/> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>'
     htmlRows += '</tr>';
     $('#extable').append(htmlRows);
   }

   function addawardrows(){
      // var count = $(".itemRow").length;
       // count++;
       jQuery(document).ready(function() {    
         KTBootstrapDatepicker.init();
       });
       var htmlRows = '';
       htmlRows += '<tr>'; 
       htmlRows += '<td><input type="text" name="award_name[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="text" name="award_description[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="date" name="award_year[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>'; 
       htmlRows += '</tr>';
       $('#awdtable').append(htmlRows);
     }
   </script>

   <script type="text/javascript">

    function displaystausdrop(id,table,status_id){
        //alert("id");
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
      //alert("data");
     //var table_name = document.getElementById('duplicate_table_name').value;
     jQuery.ajax({
      url: "views/custom_insert_status_popup.php",
      data:'table=candidates'+'&status='+status+'&rid='+id,
      type: "POST",
      success:function(data){
        $('#popup123').html(data);
        $('#kt_modal_KTDatatable_remote123').modal("show");
      },
      error:function (){}
    });
   }
   function viewpopup(){
        //alert("hai");
        jQuery.ajax({
          url: "views/custom_offer_letter_popup.php",
          data:'table=letters',
          type: "POST",
          success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
            $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
             submit();
           });  
          },
          error:function (){}
        });
      }
      function gradechange(val){
        alert(val);
        $.ajax({
          url: "ajax.php",
          data:'page=dynmictabledata&table=job_grade_templates&field=id&value=name&rid=grade_id&fid='+val,
          type: "POST",
          success:function(data){
            console.log(data);

            $("#grade").html("<select class='form-select js-example-basic-single' name='pay_template'>"+data+"</select>");
          },
          error:function (){}
        });
      }

      function adddeprows(){
       // var count = $(".itemRow").length;
       //  count++;
       jQuery(document).ready(function() {    
        KTBootstrapDatepicker.init();
      });
       var htmlRows = '';
       htmlRows += '<tr>'; 
       htmlRows += '<td><input type="text" name="dependent_name[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><select class="form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php   } ?></select>  </td>'; 
      // htmlRows += '<td><input type="text" name="dependent_relationship[]"  class="form-control " autocomplete="off">  </td>'; 
      htmlRows += '<td><input type="date" name="dependent_date_of_birth[]"  class="form-control "  id="kt_datepicker_31" autocomplete="off">  </td>';   
      htmlRows += '</tr>';
      $('#deptable').append(htmlRows);
    }
    function addedurows(){
       // var count = $(".itemRow").length;
       //  count++;
      // document.write("<script src='// ../assets/js/pages/myscrpit/date-picker.js'><\/script>");
       //datepicker();
       jQuery(document).ready(function() {    
        KTBootstrapDatepicker.init();
      });
       var htmlRows = '';
       htmlRows += '<tr>'; 
       htmlRows += '<td><input type="text" name="institute_name[]"  class="form-control " autocomplete="off">  </td>'; 
       htmlRows += '<td><select class="form-select js-example-basic-single" name="education_type" > <option value="0"> Select Any One</option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option> <?php   } ?></select> </td>'; 
       htmlRows += '<td><input type="date" name="start_date[]"  class="form-control " id="kt_datepicker_35" autocomplete="off">  </td>'; 
       htmlRows += '<td><input type="date" name="end_date[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>'; 
       
       htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
       htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;">  <span class="svg-icon svg-icon-primary svg-icon-lg"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/><path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/></g></svg></span><input type="file" name="qualification_file[]" />  <input type="hidden" name="hidden_qualification_file[]" value=""/> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>'

       htmlRows += '</tr>';
       $('#edutable').append(htmlRows);
     }
     function addexpreencerows(){
       // var count = $(".itemRow").length;
        // count++;
        jQuery(document).ready(function() {    
          KTBootstrapDatepicker.init();
        });
        var htmlRows = '';
        htmlRows += '<tr>'; 
        htmlRows += '<td><input type="text" name="company_name[]"  class="form-control " autocomplete="off">  </td>'; 
        htmlRows += '<td><input type="text" name="job_descrption[]"  class="form-control " autocomplete="off">  </td>'; 
        htmlRows += '<td><input type="date" name="work_from[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>'; 
        htmlRows += '<td><input type="date" name="work_to[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>';   
        //htmlRows += '<td> &nbsp; <div class="image-input image-input-empty image-input-outline" id="kt_image_5" > <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">  <i class="icon-2x text-dark-50 flaticon-tool-1"></i>  <input type="file" name="file[]" /> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">  <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>';
        htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;">  <span class="svg-icon svg-icon-primary svg-icon-lg"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/><path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/></g></svg></span><input type="file" name="file[]" />  <input type="hidden" name="hidden_file[]" value=""/> </label> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span> <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar"> <i class="ki ki-bold-close icon-xs text-muted"></i> </span></div></td>';
        
        htmlRows += '</tr>';
        $('#extable').append(htmlRows);
      }


function savedetails(){
$(".formsavecandidate").submit();
}



    </script>
    <div class="flex-column-fluid">
     <!--begin::Container-->
     <div class="">
      <!-- Verticle Wizard card start -->
      <div class="">

        <div class="card-header">
          <h5>Candidate</h5>
          <div class="card-header-right">
           <button type="" class="btn btn-primary font-weight-bolder text-uppercase" onclick="savedetails()">SAVE & CONTINUE</button>
         </div>
       </div>
       <div class="card-block">
        <div class="row">
          <div class="">
            <div id="wizardb">
              <section>
                  <!-- <form class="wizard-form" id="verticle-wizard" action="#"> -->
                  <!-- <form class="wizard-form form" id="verticle-wizard"  method="post" action="" enctype="multipart/form-data" > -->
                  <form class="wizard-form form formsavecandidate" id="verticle-wizard" method="post" action="" enctype="multipart/form-data">
                       
                    <h3> Registration </br><small>Please enter your Name and Display Name</small></h3>
                    <fieldset>

                     <!--begin: Wizard Step 1-->
                     <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                      <div class="row">
                       <div class="form-group col-lg-12">
                        <label>Image</label>
                        <div class="col-lg-9 col-xl-6">
                          <div class="profile-pic">
                            <label class="-label" for="image">
                              <span><i class="fa fa-camera"></i> &nbsp; Change Image</span>
                            </label>

                            <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg"  onchange="loadFile(event,this.id)">
                            <input type="hidden" name="hidimage" value="<?php echo $role['image']; ?>">
                            <img src="<?php echo $dispath ; ?>" id="outputimage" width="200" onerror="this.src='uploads/all/no-image.jpg';" />
                          </div>

                          <div class="image-input image-input-outline" id="kt_image_61">
                            <div class="image-input-wrapper" style="background-image: url('<?php echo $dispath; ?>')"></div>
                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="opacity: 0;">
                              <i class="fa fa-pen icon-sm text-muted"></i>
                              <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg">
                              <input type="hidden" name="hidimage" value="<?php echo $role['image']; ?>">
                            </label>
                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar"  style="opacity: 0;">
                              <i class="ki ki-bold-close icon-xs text-muted"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                      <?php //print_r($role);?>
                      <div class="form-group col-lg-6">
                        <label>First Name <span class="text-danger">*</span></label>
                        <div class="input-group input-icon-right mb-0">
                         <input class="form-control"  type="text"  id="first_name" name="first_name"  value="<?php echo $role['first_name']; ?>">

                       </div>
                       <div style="display: none;color: red;" id="first_name_err"> Please enter First Name </div>
                     </div>
                     <div class="form-group col-lg-6">
                      <label>Middle Name</label>
                      <div class="input-group input-icon-right mb-0">
                       <input class="form-control"  type="text"  id="middle_name" name="middle_name"  value="<?php echo $role['middle_name']; ?>">

                     </div>
                     <div style="display: none;color: red;" id="middle_name_err"> Please enter Middle Name </div>
                   </div>
                   <div class="form-group col-lg-6">
                    <label>Last Name<span class="text-danger">*</span></label>
                    <div class="input-group input-icon-right mb-0">
                     <input class="form-control"  type="text"  id="last_name" name="last_name"  value="<?php echo $role['last_name']; ?>">

                   </div>
                   <div style="display: none;color: red;" id="last_name_err"> Please enter Last Name </div>
                 </div>
                 <div class="form-group col-lg-6">
                  <label>Display Name<span class="text-danger">*</span></label>
                  <div class="input-group input-icon-right mb-0">
                   <input class="form-control"  type="text"  id="display_name" name="display_name"  value="<?php echo $role['display_name']; ?>">

                 </div>
                 <div style="display: none;color: red;" id="display_name_err"> Please enter Display Name </div>
               </div>
             </div>
           </div>
           <!--end: Wizard Step 2-->







         </fieldset>



         <h3> Contact Details </br><small>Please enter your contact details</small></h3>
         <fieldset>
           <!--begin: Wizard Step 3-->
           <div class="pb-5" data-wizard-type="step-content">
            <div class="row">
             <div class="form-group col-lg-12">
              <label>Address</label>
              <div class="kt-radio-inline">
               <textarea type="text" class="form-control" id="address" name="address" ><?php echo $role['address']; ?></textarea>
             </div>
             <div style="display: none;color: red;" id="address_err"> Please Select Address </div>
           </div>

           <div class="form-group col-lg-6">
            <label>Post Code<span class="text-danger">*</span></label>
            <div class="input-group input-icon-right">
             <input class="form-control"  type="text"  id="post_code" name="post_code"  value="<?php echo $role['post_code']; ?>">

           </div>
           <div style="display: none;color: red;" id="post_code_err"> Please enter Post Code </div>
         </div>
         <div class="form-group col-lg-6">
          <label>Phone<span class="text-danger">*</span></label>
          <div class="input-group input-icon-right">
           <input class="form-control"  type="text"  id="phone" name="phone" readonly  value="<?php echo $role['phone']; ?>">

         </div>
         <div style="display: none;color: red;" id="phone_err"> Please enter Phone </div>
       </div>
       <div class="form-group col-lg-12">
        <label>Email<span class="text-danger">*</span></label>
        <div class="input-group input-icon-right">
         <input class="form-control"  type="text"  id="email_id" readonly name="email_id"  value="<?php echo $role['email_id']; ?>">

       </div>
       <div style="display: none;color: red;" id="email_id_err"> Please enter Email </div>
     </div>
     <!--end::Select-->
   </div>
 </div>
 <!--end: Wizard Step 3-->



</fieldset>


<h3> Personal Details </br><small>Please enter your personal details</small></h3>
<fieldset>

 <!--begin: Wizard Step 4-->
 <div class="pb-5" data-wizard-type="step-content">
  <div class="row">
   <div class="col-lg-6">

    <label>Gender</label>
    <div class="form-radio ">
      <div class="radio radio-inline">
       <label class="form-label"> <input type="radio"  class="mt-2"  name="gender"  <?php if($role['gender'] == 'F'){  ?> checked <?php } ?> value='F'> <span></span> Female </label>
     </div>
     <div class="radio radio-inline">
       <label class="form-label"> 
        <input type="radio"   name="gender" class="mt-2" <?php if($role['gender'] == 'M'){  ?> checked <?php } ?>  value='M'> <span></span> Male </label>
      </div>
    </div> 
    <div style="display: none;color: red;" id="gender_err"> Please Select Gender </div> 
  </div>
  <?php 
  $daob = fromDate($role['date_of_birth']); 
  $weding = fromDate($role['wedding_date']); 
  ?>
  <div class="form-group col-lg-6" >
    <label>Date Of Birth<span class="text-danger">*</span></label>

    <div class="input-group date">
     <input type="date" class="form-control"  value="<?php echo $daob; ?>"  name="date_of_birth"  id="kt_datepicker_4_1" />

   </div>

 </div>
 <div class="form-group col-lg-6">
  <label>Marital Status<span class="text-danger">*</span></label>
  <select class="form-select js-example-basic-single" id="marital_status" name="marital_status" onchange="maritalstatuschange(this.value)">
   <option value=""> Select Any one Marital Status</option>
   <option <?php if($role['marital_status'] == 'M'){  ?> selected <?php } ?>  value="M">Married</option>

   <option <?php if($role['marital_status'] == 'S'){  ?> selected <?php } ?>  value="S">Single</option>

 </select>
 <div style="display: none;color: red;" id="marital_status_err"> Please Select Marital Status </div>
</div>
<div class="form-group col-lg-6 <?php echo $mclass; ?>" id="marriagedateshow" >
  <label>Anniversary Date</label><?php if($role['marital_status'] == 'M'){ $mclass = ""; }else{ $mclass = "d-none"; } ?>

  <div class="input-group date">
   <input type="date" class="form-control"  value="<?php echo $weding; ?>"  name="wedding_date"  id="kt_datepicker_4_2" />

 </div>
</div>

<div class="form-group col-lg-6">
  <label>Blood Group<span class="text-danger">*</span></label>
  <select class="form-select js-example-basic-single" id="kt_select2_41" name="blood_group">
   <option value=""> Select Any one Marital Status</option>
   <?php foreach ($blood_groups_details as $blood_groups) { ?>
     <option <?php if($data['blood_group'] == $blood_groups['genc_detail_name']){ ?>selected <?php } ?> value="<?php echo $blood_groups['genc_detail_name']; ?>" >
      <?php echo $blood_groups['genc_detail_name']; ?>
    </option>
  <?php } ?>

</select>
<div style="display: none;color: red;" id="post_code_err"> Please enter Blood Group </div>
</div>
<div class="form-group col-lg-6">
  <label>Father Name<span class="text-danger">*</span></label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="father_name" name="father_name"   value="<?php echo $role['father_name']; ?>">

 </div>
 <div style="display: none;color: red;" id="phone_err"> Please enter Phone </div>
</div>
<div class="form-group col-lg-6">
  <label>Spouse Name</label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="spouse_name" name="spouse_name"   value="<?php echo $role['spouse_name']; ?>">

 </div>
 <div style="display: none;color: red;" id="phone_err"> Please enter Spouse Name </div>
</div>
<div class="form-group col-lg-6">
  <label>Adhar Number<span class="text-danger">*</span></label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="adhar_no" name="adhar_no"   value="<?php echo $role['adhar_no']; ?>">

 </div>
 <div style="display: none;color: red;" id="phone_err"> Please enter adhar Number </div>
</div>
<div class="form-group col-lg-6">
  <label>Pan Number<span class="text-danger">*</span></label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="pan_number" name="pan_number"   value="<?php echo $role['pan_number']; ?>">

 </div>
 <div style="display: none;color: red;" id="phone_err"> Please enter Pan Number </div>
</div>
<?php if($_SESSION['user_type'] == 1){ ?>
 <div class="form-group col-lg-6">
  <label>Hiring Source</label>
  <select class="form-select js-example-basic-single"  id="kt_select2_41" name="hiring_source">
   <option value="0"> Select Any One </option>
   <?php foreach ($venderdata as $key => $vender) { ?>
     <option <?php if($role['hiring_source'] == $vender['id']){  ?> selected <?php } ?>  value="<?php echo $vender['id']; ?>"><?php echo $vender['display_name']; ?> </option>
   <?php } ?>
 </select>
 <div style="display: none;color: red;" id="hiring_source_err"> Please enter Hiring Source </div>
</div>
<?php } ?>
</div>
</div>
<!--end: Wizard Step 4-->


</fieldset>

<h3> 
Employment Details </br><small>Please enter your employement details</small></h3>
<fieldset>


  <!--begin: Wizard Step 5-->
  <div class="pb-5" data-wizard-type="step-content">
    <div class="row">
     <div class="form-group col-lg-4">
      <label>Total Experiance</label>
      <div class="input-group input-icon-right">
       <input class="form-control"  type="text"  id="total_experiance" name="total_experiance"  value="<?php echo $role['total_experiance']; ?>">
     </div>
     <div style="display: none;color: red;" id="total_experiance_err"> Please enter Total Experiance </div>
   </div>
   <div class="form-group col-lg-4">
    <label>Notice Period</label>
    <div class="input-group input-icon-right">
     <input class="form-control"  type="text"  id="notice_period" name="notice_period"  value="<?php echo $role['notice_period']; ?>">
   </div>
   <div style="display: none;color: red;" id="notice_period_err"> Please enter Notice Period </div>
 </div>
 <div class="form-group col-lg-4">

  <label class="mb-3">Negotiatable</label>
  <div class="form-radio">
    <div class="radio radio-inline">
     <label class="form-label"> <input type="radio"   name="np_negotiatable" <?php if($role['np_negotiatable'] == '0'){  ?> checked <?php } ?>  value='0'>  <span></span>Yes </label></div>
     <div class="radio radio-inline">
       <label class="form-label"> <input type="radio"    name="np_negotiatable"  <?php if($role['np_negotiatable'] == '1'){  ?> checked <?php } ?> value='1'> <span></span>No </label></div>
     </div>
     <div style="display: none;color: red;" id="np_negotiatable_err"> Please Select Negotiatable </div>
   </div>


   <div class="form-group col-lg-12 dt-responsive table-responsive">
    <table class="table table-framed " id="extable">
     <tr>
      <th class="" width="30%">Company Name</th>
      <th class="thheader" width="30%">job Descrption </th>
      <th class="" width="20%"> From</th>
      <th class="thheader" width="20%">To </th>
      <th width="10%">Files </th>
    </tr>
    <?php 
    $display_file = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents";
    $k=1; $exper= json_decode($role['candidate_experiances'],1); if(count((array)$exper)>0){ 
      foreach ($exper as $expe) { ?>
       <tr>
        <td><input type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>">  </td>
        <td><input type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off">  </td>
        <td><input type="date" name="work_from[]" class="form-control " id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo $expe['work_from']; ?>" autocomplete="off">  </td>
        <td><input type="date" name="work_to[]" class="form-control " id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo $expe['work_to']; ?>"autocomplete="off">  </td>
        <td>
          <?php if($expe['compay_file']!=''){ ?>
            <a href="<?php echo $display_file."/".$expe['compay_file']; ?>"  data-toggle="tooltip" title="Download"  target="_blank" style="float:left;position:absolute;right:18px;" class="mt-2"> 
              <span class="svg-icon svg-icon-warning svg-icon-lg"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-02-01-052524/theme/html/demo1/dist/../src/media/svg/icons/Files/DownloadedFile.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                    <path d="M14.8875071,11.8306874 L12.9310336,11.8306874 L12.9310336,9.82301606 C12.9310336,9.54687369 12.707176,9.32301606 12.4310336,9.32301606 L11.4077349,9.32301606 C11.1315925,9.32301606 10.9077349,9.54687369 10.9077349,9.82301606 L10.9077349,11.8306874 L8.9512614,11.8306874 C8.67511903,11.8306874 8.4512614,12.054545 8.4512614,12.3306874 C8.4512614,12.448999 8.49321518,12.5634776 8.56966458,12.6537723 L11.5377874,16.1594334 C11.7162223,16.3701835 12.0317191,16.3963802 12.2424692,16.2179453 C12.2635563,16.2000915 12.2831273,16.1805206 12.3009811,16.1594334 L15.2691039,12.6537723 C15.4475388,12.4430222 15.4213421,12.1275254 15.210592,11.9490905 C15.1202973,11.8726411 15.0058187,11.8306874 14.8875071,11.8306874 Z" fill="#000000"/>
                  </g>
                </svg><!--end::Svg Icon--></span>
              </a>
            <?php } ?>
            <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
             <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" style="left:-5px;top:-15px;">
              <span class="svg-icon svg-icon-primary svg-icon-lg"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-02-01-052524/theme/html/demo1/dist/../src/media/svg/icons/Files/Uploaded-file.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"/>
                  <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                  <path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/>
                </g>
              </svg><!--end::Svg Icon--></span>
              <input type="file" name="file[]" />
              <input type="hidden" name="hidden_file[]" value="<?php echo $expe['compay_file']; ?>"/>
            </label>

            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>

            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>
          </div>

        </td>
      </tr>
    <?php } } ?>
  </table>
  <div class="row">
   <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->
    <button class="btn btn-success float-left mb-2"  onclick="addexpreencerows()" id="addRows" type="button">Add More</button>
  </div>
</div>
</div>
</div>
</div>
</fieldset>
<h3> Educational Details </br><small>Please enter your educational details</small></h3>
<fieldset>
  <div class="pb-5" data-wizard-type="step-content" >
    <h4 class="mb-10 font-weight-bold text-dark">Qualification</h4>
    <div class=" container row">
     <div class="form-group col-lg-12 dt-responsive table-responsive">
      <table class="table table-framed " id="edutable">
       <tr>
        <th class="" width="25%">Institute Name</th>
        <th class="thheader" width="20%">Type</th>
        <th class="" width="20%"> Start Date</th>
        <th class="thheader" width="20%">End Date </th>
        <th class="thheader" width="15%">Score </th>
        <th class="thheader" width="15%">Files </th>
      </tr>
      <?php $insts= json_decode($role['candidate_qualifications'],1); if(count((array)$insts)>0){ 
        foreach ($insts as $inst) { ?>
         <tr>
          <td><input type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off">  </td>
          <td>
           <select class="form-select js-example-basic-single" name="education_type[]" >
            <option value="0"> Select Any One</option>
            <?php foreach ($edutypedata as  $edutype) {   ?> 
              <option  <?php if($inst['education_type']== $edutype['genc_detail_code']){  ?> selected <?php } ?>   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
            <?php   } ?>
          </select>
        </td>
        <td><input type="date" name="start_date[]" class="form-control "  id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo $inst['start_date']; ?>"  autocomplete="off">  </td>
        <td><input type="date" name="end_date[]" class="form-control " id="kt_datepicker_3<?php echo $k; ?>"  value="<?php echo $inst['end_date']; ?>" autocomplete="off">  </td>
        <td><input type="text" name="score[]" class="form-control "  value="<?php echo $inst['score']; ?>"  autocomplete="off">  </td>
        <td>
          <?php if($inst['qualification_file']!=''){ ?>
            <a href="<?php echo $display_file."/".$inst['qualification_file']; ?>"  data-toggle="tooltip" title="Download"  target="_blank" style="float:left;position:absolute;right:18px;" class="mt-2"> 
              <span class="svg-icon svg-icon-warning svg-icon-lg"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-02-01-052524/theme/html/demo1/dist/../src/media/svg/icons/Files/DownloadedFile.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                    <path d="M14.8875071,11.8306874 L12.9310336,11.8306874 L12.9310336,9.82301606 C12.9310336,9.54687369 12.707176,9.32301606 12.4310336,9.32301606 L11.4077349,9.32301606 C11.1315925,9.32301606 10.9077349,9.54687369 10.9077349,9.82301606 L10.9077349,11.8306874 L8.9512614,11.8306874 C8.67511903,11.8306874 8.4512614,12.054545 8.4512614,12.3306874 C8.4512614,12.448999 8.49321518,12.5634776 8.56966458,12.6537723 L11.5377874,16.1594334 C11.7162223,16.3701835 12.0317191,16.3963802 12.2424692,16.2179453 C12.2635563,16.2000915 12.2831273,16.1805206 12.3009811,16.1594334 L15.2691039,12.6537723 C15.4475388,12.4430222 15.4213421,12.1275254 15.210592,11.9490905 C15.1202973,11.8726411 15.0058187,11.8306874 14.8875071,11.8306874 Z" fill="#000000"/>
                  </g>
                </svg><!--end::Svg Icon--></span>
              </a>
            <?php } ?>
            <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
             <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" style="left:-5px;top:-15px;">
              <span class="svg-icon svg-icon-primary svg-icon-lg"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-02-01-052524/theme/html/demo1/dist/../src/media/svg/icons/Files/Uploaded-file.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"/>
                  <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                  <path d="M8.95128003,13.8153448 L10.9077535,13.8153448 L10.9077535,15.8230161 C10.9077535,16.0991584 11.1316112,16.3230161 11.4077535,16.3230161 L12.4310522,16.3230161 C12.7071946,16.3230161 12.9310522,16.0991584 12.9310522,15.8230161 L12.9310522,13.8153448 L14.8875257,13.8153448 C15.1636681,13.8153448 15.3875257,13.5914871 15.3875257,13.3153448 C15.3875257,13.1970331 15.345572,13.0825545 15.2691225,12.9922598 L12.3009997,9.48659872 C12.1225648,9.27584861 11.8070681,9.24965194 11.596318,9.42808682 C11.5752308,9.44594059 11.5556598,9.46551156 11.5378061,9.48659872 L8.56968321,12.9922598 C8.39124833,13.2030099 8.417445,13.5185067 8.62819511,13.6969416 C8.71848979,13.773391 8.8329684,13.8153448 8.95128003,13.8153448 Z" fill="#000000"/>
                </g>
              </svg><!--end::Svg Icon--></span>
              <input type="file" name="qualification_file[]" />
              <input type="hidden" name="hidden_qualification_file[]" value="<?php echo $expe['qualification_file']; ?>"/>
            </label>

            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>

            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>
          </div>

        </td>
      </tr>
    <?php } } ?>
  </table>
  <div class="row">
   <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->
    <button class="btn btn-success float-left mb-2"  onclick="addedurows()" id="addRows" type="button">Add More</button>
  </div>
</div>
</div>
</div>
</div> 

</fieldset>

<h3> Dependents Details </br><small>Please enter your dependent details</small></h3>
<fieldset>

 <div class="pb-5" data-wizard-type="step-content" >
  <h4 class="mb-10 font-weight-bold text-dark">Dependents</h4>
  <div class="row">
   <div class="form-group col-lg-12 dt-responsive table-responsive">
    <table class="table table-framed" id="deptable">
     <tr>
      <th class="" width="25%"> Name</th>
      <th class="thheader" width="20%">Relationship Type</th>
      <!--<th class="" width="20%"> Relationship</th>-->
      <th class="thheader" width="20%">DOB</th>
    </tr>
    <?php $depen= json_decode($role['employee_dependents'],1); if(count((array)$depen)>0){ 
      foreach ($depen as $depe) { ?>
       <tr>
        <td><input type="text" name="dependent_name[]" class="form-control " value="<?php echo $depe['dependent_name']; ?>" autocomplete="off">  </td>
        <td>
         <select class="form-select js-example-basic-single" name="dependent_relationship_type[]" >
          <option value="0"> Select Any One</option>
          <?php foreach ($relatypedata as  $reladata) {   ?> 
            <option   <?php if($depe['dependent_relationship_type']== $reladata['id']){  ?> selected <?php } ?> value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
          <?php   } ?>
        </select>
      </td>
                            <!--<td><input type="text" name="dependent_relationship[]" class="form-control " value="<?php echo $depe['dependent_relationship']; ?>" autocomplete="off">  </td>
                            --><td><input type="date" name="dependent_date_of_birth[]" class="form-control " id="kt_d atepicker_3<?php echo $k; ?>" value="<?php echo $depe['dependent_date_of_birth']; ?>" autocomplete="off">  </td>
                          </tr>
                        <?php } } ?>
                      </table>
                      <div class="row">
                       <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->
                        <button class="btn btn-success float-left mb-2"  onclick="adddeprows()" id="addRows" type="button">Add More</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--end: Wizard Step 5-->



            </fieldset>

            <h3> Current Employment Details </br><small>Please enter your current employment Details</small></h3>
            <fieldset>

              <!--begin: Wizard Step 6-->
              <div class="pb-5" data-wizard-type="step-content">
                <!--begin::Section-->
                <div class="row">
                 <div class="form-group col-lg-6">
                  <label>Employee Contacts</label>
                  <div class="input-group input-icon-right">
                   <input class="form-control"  type="text"  id="employee_contacts" name="employee_contacts"  value="<?php echo $role['employee_contacts']; ?>">

                 </div>
                 <div style="display: none;color: red;" id="employee_contacts_err"> Please enter Employee Contacts </div>
               </div>
               <div class="form-group col-lg-6">
                <label>Current Location</label>
                <div class="input-group input-icon-right">
                 <input class="form-control"  type="text"  id="current_location" name="current_location"  value="<?php echo $role['current_location']; ?>">

               </div>
               <div style="display: none;color: red;" id="current_location_err"> Please enter Current Location </div>
             </div>
             <div class="form-group col-lg-6">
              <label>Reason For Change</label>
              <div class="input-group input-icon-right">
               <input class="form-control"  type="text"  id="reason_for_change" name="reason_for_change"  value="<?php echo $role['reason_for_change']; ?>">

             </div>
             <div style="display: none;color: red;" id="reason_for_change_err"> Please enter Reason For Change </div>
           </div>

           <div class="form-group col-lg-6">
            <label class="mb-3">Relocate</label>
            <div class="form-radio">
              <div class="radio radio-inline">
               <label class="form-label"> <input type="radio"    name="relocate"  <?php if($role['relocate'] == '0'){  ?> checked <?php } ?> value='0'> <span></span>Yes </label></div>
               <div class="radio radio-inline">
                 <label class="form-label"> <input type="radio"   name="relocate" <?php if($role['relocate'] == '1'){  ?> checked <?php } ?>  value='0'>  <span></span>No </label></div>
               </div>
               <div style="display: none;color: red;" id="relocate_err"> Please enter Relocate </div>
             </div>
             <div class="form-group col-lg-6">
              <label>Current Ctc</label>
              <div class="input-group input-icon-right">
               <input class="form-control"  type="text"  id="current_ctc" name="current_ctc"  value="<?php echo $role['current_ctc']; ?>">
             </div>
             <div style="display: none;color: red;" id="current_ctc_err"> Please enter Current Ctc </div>
           </div>
           <div class="form-group col-lg-6">
            <label>Expected Ctc</label>
            <div class="input-group input-icon-right">
             <input class="form-control"  type="text"  id="expected_ctc" name="expected_ctc"  value="<?php echo $role['expected_ctc']; ?>">
           </div>
           <div style="display: none;color: red;" id="expected_ctc_err"> Please enter Expected Ctc </div>
         </div>

         <div class="form-group col-lg-6">
          <label>Number of Reportees</label>
          <div class="input-group input-icon-right">
           <input class="form-control"  type="text"  id="no_of_reportees" name="no_of_reportees"  value="<?php echo $role['no_of_reportees']; ?>">
         </div>
         <div style="display: none;color: red;" id="expected_ctc_err"> Please enter number of Reportees </div>
       </div>
       <?php if($_SESSION['user_type'] !=4){ ?>
         <div class="form-group col-lg-6">
          <label>Job Grad</label>
          <select class="form-select js-example-basic-single" name="job_grade" onchange="gradechange(this.value)">
           <option value="0"> Select Any One</option>
           <?php foreach ($grade as  $gra) { ?>
             <option  <?php if($role['job_grade'] == $gra['id'] ){  ?> selected <?php } ?> value="<?php echo $gra['id']; ?>"><?php echo $gra['job_grade_name']; ?></option>
           <?php  } ?>
         </select>
         <div style="display: none;color: red;" id="expected_ctc_err"> Please enter Expected Ctc </div>
       </div>
       <div class="form-group col-lg-6">
        <label>Pay template</label>
        <select   class="form-select js-example-basic-single" name="pay_template" id="grade">
         grade_id
         <option value="0"> Select Any One</option>
         <option value="0"> Select Any One</option>
         <?php foreach ($tempdata as  $temp) { ?>
           <option  <?php if($role['pay_template'] == $temp['id'] ){  ?> selected <?php } ?> value="<?php echo $temp['id']; ?>"><?php echo $temp['name']; ?></option>
         <?php  } ?>
       </select>
       <div style="display: none;color: red;" id="expected_ctc_err"> Please enter Pay template </div>
     </div>
     <!-- <button type="button" class="btn btn-light-primary" onclick="viewpopup()">View Offer</button> -->
   <?php } ?>
   <input type="hidden" name="hid" value="<?php echo $rid; ?>">
 </div>
</div>




</fieldset>

<h3> 
Awards and Aculates </br><small>Please enter your awards details</small></h3>
<fieldset>


  <div class="pb-5" data-wizard-type="step-content">
    <div class="form-group col-lg-12 dt-responsive table-responsive">
      <table class="table table-framed " id="awdtable">
       <tr>
        <th class="" width="30%">Award Name</th>
        <th class="thheader" width="30%">Award Descrption </th>
        <th class="" width="20%"> Received Year</th>
      </tr>
      <?php $k=1; $awards= json_decode($role['candidate_awards'],1); if(count((array)$awards)>0){ 
        foreach ($awards as $awd) { ?>
         <tr>
          <td><input type="text" name="award_name[]" class="form-control " autocomplete="off" value="<?php echo $awd['award_name']; ?>">  </td>
          <td><input type="text" name="award_description[]" class="form-control" value="<?php echo $awd['award_description']; ?>" autocomplete="off">  </td>
          <td><input type="text" name="award_year[]" class="form-control " value="<?php echo $awd['year']; ?>" autocomplete="off">  </td>

        </tr>
      <?php } } ?>
    </table>
    <div class="row">
     <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->
      <button class="btn btn-success float-left mb-2"  onclick="addawardrows()" id="addRows" type="button">Add More</button>
    </div>
  </div>
</div>
</div>
<!--end: Wizard Step 6-->



</fieldset>

<h3> HR Data </br><small>Please enter your HR details</small></h3>
<fieldset>
  <div class="pb-5" data-wizard-type="step-content">
    <!--begin::Section-->
    <div class="row">
     <div class="form-group col-lg-6">
      <label>First Role</label>
      <div class="input-group input-icon-right">
       <input class="form-control"  type="text"  id="first_role" name="first_role"  value="<?php echo $role['first_role']; ?>">
     </div>
     <div style="display: none;color: red;" id="employee_contacts_err"> Please enter First Role </div>
   </div>
   <div class="form-group col-lg-6">
    <label>Current Role</label>
    <div class="input-group input-icon-right">
     <input class="form-control"  type="text"  id="candidate_current_role" name="candidate_current_role"  value="<?php echo $role['candidate_current_role']; ?>">
   </div>
   <div style="display: none;color: red;" id="candidate_current_role_err"> Please enter Current Role </div>
 </div>
 <div class="form-group col-lg-6">
  <label>First Pay</label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="first_pay" name="first_pay"  value="<?php echo $role['first_pay']; ?>">
 </div>
 <div style="display: none;color: red;" id="first_pay_err"> Please enter First Pay</div>
</div>
<div class="form-group col-lg-6">
  <label>Current Pay</label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="current_pay" name="current_pay"  value="<?php echo $role['current_pay']; ?>">
 </div>
 <div style="display: none;color: red;" id="Current Pay_err"> Please enter Current Pay </div>
</div>
<div class="form-group col-lg-6">
  <label class="form-label">Gap in employment if any And Reason</label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="gaps_in_employment_reason" name="gaps_in_employment_reason"  value="<?php echo $role['gaps_in_employment_reason']; ?>">
 </div>
 <div style="display: none;color: red;" id="current_ctc_err"> Please enter Gap in employment if any And Reason </div>
</div>
<div class="form-group col-lg-6">
  <label>Performance Rating</label>
  <div class="input-group input-icon-right">
   <input class="form-control"  type="text"  id="performance_ratings" name="performance_ratings"  value="<?php echo $role['performance_ratings']; ?>">
 </div>
 <div style="display: none;color: red;" id="expected_ctc_err"> Please enter Performance Rating </div>
</div>


</div>
</div>                         <!--begin: Wizard Actions-->
</fieldset>
</div>
</form>
</section>
</div>
</div>
</div>
</div>
</div>


</div>
<!--end::Entry-->
</div>

</div>





<!--end::Content-->
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-xl" >
  <div class="modal-content"  id="popup1">
  </div>
</div>
</div>
<div id="kt_modal_KTDatatable_remote123" class="modal fade" role="dialog" aria-hidden="true">
 <div class="modal-dialog " >
  <div class="modal-content"  id="popup123">
  </div>
</div>
</div>











