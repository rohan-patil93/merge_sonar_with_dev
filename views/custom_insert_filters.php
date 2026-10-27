<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = TBL_PROCESS_TASK_SETTINGS;
$filter_id = $_GET['filter_id'];
if($_GET['process_type']=='O'){
  $filter_table = 'candidates';
}else{
  $filter_table = 'employees';
}
$table = TBL_PROCESS_TASK_SETTINGS;
if(isset($_GET['tablename']) && $_GET['tablename'] !=""){
    $table = $_GET['tablename'];
}
$url = API."?mode=gettasksdetails&filter_id=".$filter_id."&filter_table=".$filter_table."&apply_table_name=".$table; // rohan addedd apply_table_name 01-11-2022
$response = callAPI($url);
$tasks_details = json_decode($response,1);
$filter_list = $tasks_details['filter_list'];
$filter_category = $tasks_details['filter_option'];

$emp_list = $tasks_details['emp_list'];
$employees = array();
foreach($emp_list as $eml){
  $employees[$eml['id']] = $eml;
}
$selectedlist =  $tasks_details['filter_data']['filter_condition'];
if($tasks_details['filter_data']['content'] == ""){
 $selectedoption = 'AE';
}else {
 $selectedoption = 'SE';
 ?>
 <script type="text/javascript">document.getElementById('filtershow').style.display = 'block';</script>
 <?php
}
$eee_count = json_decode($tasks_details['filter_data']['content'],1);
$emp_count = count((array)$eee_count);
if(isset($selectedlist['matches_names'])){
  $emp_list_names = $selectedlist['matches_names'];
  $emp_list_names  = json_encode($emp_list_names,1);
}

$show_filter_cond = $tasks_details['filter_data']['filter_condition'];
$show_filter_cond = explode('&',$show_filter_cond);

foreach($show_filter_cond as $show_filter_co){
    $lkm = explode("=",$show_filter_co);
    $show_filter_condion[$lkm['0']]=$lkm['1'];
}

?>
<script src="assets/js/pages/myscrpit/selecter.js"></script>
<script type="text/javascript">
 function populatenamelist(){
   console.log("******************");
   jQuery(document).ready(function() {
     KTBootstrapDatepicker.init();
   });
   var data = <?php echo $emp_list_names; ?>;
   var result = JSON.stringify(data);
   var result = JSON.parse(result);
   var sel = document.getElementById('sel_option');
   for(var i = 0; i < result.length; i++) {

    var opt = document.createElement('option');
    opt.innerHTML = result[i].display_name;
    opt.value = result[i].id_value;
    sel.appendChild(opt);
  }
}
</script>
<?php

if($emp_list_names!="") {
  echo '<script type="text/JavaScript">
  populatenamelist();
  </script>';
}

?>
<div class="modal-header ">
  <h5 class="modal-title">  Filter Options   </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form-label-right " action="" method="post">
  <div class="modal-body j-pro" >
    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10 j-content">
      <div class="row">
        <div class="col-lg-6 w-full">
          <span>Include</span>
           <input type="hidden"  id="filter_row_id" value="<?php echo $filter_id; ?>">
          <input type="hidden" name="filterlist" id="filterlist" value="">
           <input type="hidden" name="filter_condition" id="filter_condition" value="">
          <input type="hidden" name="filter_table" id="filter_table" value="<?php echo $filter_table; ?>">
          <input type="hidden" name="tablename" id="tablename" value="<?php echo $table; ?>">
     <input type="hidden" name="filter" id="filter" value="">
<input type="hidden" id="jsonselectcodition" value='<?php echo $_GET['fieldlist']; ?>'>

          <select aria-label="Filterfor" onchange="showfilter(this.value);" id="filterfor" name="filterfor" class="form-control form-select js-example-basic-single">
            <?php foreach ($filter_list as $filter) { ?>
              <option <?php if($selectedoption==$filter['genc_detail_code']) {?> selected <?php
} ?> value="<?php echo $filter['genc_detail_code'];?>"><?php echo $filter['genc_detail_name'];?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-lg-6" id="empimages">
              <span>Employees</span>
              <div class="symbol-group symbol-hover justify-content-center">
                <?php foreach($eee_count as $empid){
                  $empdetail = $employees[$empid];
                  ?>
                  <?php if($empdetail['image']) {?>
    <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-40 align-top mt-3" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $empdetail['display_name']; ?>" alt="<?php echo $empdetail['display_name']; ?>">
  <?php } else { ?>
    <span  alt="user" class="img-radius img-40 align-top mt-3 custom_txt_round p-2 f-12 text-center" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $empdetail['display_name']; ?>"><?php echo initials($empdetail['display_name']); ?></span>
  <?php } ?>
                <?php } ?>
              </div>
        </div>

        <div class="col-lg-12">
          <div class="bg-white rounded row mt-5" style="display: none"  id="filtershow">
            <div class="col-lg-6  py-3">
              <span>Filter employees by</span>
              <div class="fr">
                <i class="fas fa-user icon-nm pl-40"></i>
                <span><span id="matches_count">  <?php echo $emp_count;?> </span> Matches</span>
              </div>
            </div>
            <div class="col-lg-6 card card-custom card-border">
              <div class="card-block accordion-block">
                <div class="accordion-box" id="single-open">
                  <?php $index = 0; foreach ($filter_category as $filter) {
                      $targetid= $filter['id'];
                      $targettable= $filter['application_tablename'];
                      $targettable_field= $filter['fieldname'];
                      $index++;
                      $select_value[$targettable_field] = "kt_select2_4".$index;
                      $table_details = dyanamicselectdata($targettable, $targettable_field);
                      $check_show_filter_cond = $show_filter_condion[$targettable_field];
                      $check_show_filter_cond = explode(",",$check_show_filter_cond);
                  ?>
                    <a class="accordion-msg waves-effect waves-dark"><?php echo $filter['filter_name']; ?> </br><small class="text-info">Please Select <?php echo $filter['filter_name']; ?></small></a>
                    <div class="accordion-desc w-full">
                      <select aria-label="Select option" onchange="selectemployee()" class="form-control mt-5 form-select js-example-basic-multiple" id="kt_select2_4<?php echo $index;?>" name="empstatus[]" multiple="multiple">
                          <?php foreach ($table_details as $key => $value) { ?>
                              <option value="<?php echo $key;?>"
                                <?php if(in_array($key, $check_show_filter_cond)) { ?> selected <?php
} ?> ><?php echo $value;?></option>
                           <?php   }  ?>
                      </select>
                    </div>
                  <?php } $select_value_json = json_encode($select_value);  ?>
                  <input type="hidden" id="select_value" name="select_value" value='<?php echo json_encode($select_value); ?>' >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer mt-5" >
  <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>    <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Select</button>
</div>
</form>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<script type="text/javascript">
  function moreemployees() {
    var employee_names_list = document.getElementById("filterlist").value;
    alert(employee_names_list);
    jQuery.ajax({
      url: "views/custom_view_employees_popup.php",
      data: 'empids='+employee_names_list,
      type: "GET",
      success:function(data){
        $('#dynamic_popup11').html(data);
        $('#kt_modal_dynamic_popup2').modal("show");
      },
      error:function (){}
    });

  }

 function selectemployee(){
   var select_value = <?php echo $select_value_json; ?>;
   var application_table = document.getElementById('filter_table').value;
   var str = "&table="+application_table;
   var obj = {};
   var objnew = {};
   for(var key in select_value) {

     console.log("Key: " + key + " value: " + select_value[key]);
     var elements = document.getElementById(select_value[key]).childNodes;
     var selectedKeyValue = {};
     var arrayOfSelecedIDs=[];
     for(i=0;i<elements.length;i++){
       if(elements[i].selected){
        selectedKeyValue[elements[i].value]=elements[i].textContent;
        obj[key] = selectedKeyValue;
        objnew[key] = selectedKeyValue;
        arrayOfSelecedIDs.push(elements[i].value)
      }
    }
    str += '&'+key+'='+arrayOfSelecedIDs;
  }
  console.log(str);


   document.getElementById('filter_condition').value = str;
    document.getElementById('jsonselectcodition').value = JSON.stringify(objnew);
  $.ajax({
   url: "ajax.php",
   data:'page=selectedfilter'+str,
   type: "POST",
   success:function(data){
    console.log(data);
    var result = JSON.parse(data);
    console.log(result.details);

    console.log('data'+data);
     var result = JSON.parse(data);
   document.getElementById("matches_count").innerHTML = result.count;
   obj['matches_count'] = result.count;
   obj['matches_names'] = result.details;

  var jsonData = {};
   for(var i = 0; i < result.count; i++) {
    var ids = result.details[i].id_value;
   jsonData[ids] = ids;
 }
 var json_empids = JSON.stringify(jsonData);
     console.log('data'+json_empids);
    document.getElementById('filterlist').value = json_empids;
   var maxlimit  = 10;
   if(result.count>maxlimit) {
     var more = result.count - maxlimit;
   }else{
    maxlimit = result.count;
   }
   var imagelist = '  <span>Employees</span> <div class=""><div class="symbol-group symbol-hover justify-content-center">';
   for(var i = 0; i < maxlimit; i++) {
     imgpath = "uploads/all/"+result.details[i].image;
     if(result.details[i].image!=''){
       imagelist +='<img alt="Pic"  src="'+imgpath+'"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'+result.details[i].display_name+'" class="img-radius img-40 align-top m-r-15  mt-3"/>';
     }else{
       imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-success" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'+result.details[i].display_name+'"><span class="symbol-label font-weight-bold">'+getInitials(result.details[i].display_name)+'</span></div>';
     }
   }
    if(more>0) {
      imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-warning" data-toggle="tooltip" title="'+more+' More"><button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer" onclick="moreemployees()"><span class="symbol-label font-weight-bold">'+more+'\+</span></button></div>';
    }
   imagelist +='</div></div>';
   document.getElementById("empimages").innerHTML = imagelist;
$('[data-bs-toggle="tooltip"]').tooltip();


              },
              error:function (){}
            });

}

function showfilter(value) {
     if(value=='SE') {
         document.getElementById('filtershow').style.display ='block';
          $("#empimages").show();
     } else {
        document.getElementById('filtershow').style.display ='none';
        $("#empimages").hide();
     }
   }
   function submitrom(){

    var application_table = document.getElementById('tablename').value;
    var selectmain = document.getElementById('filterfor').value;
    var jsonselectcodition = document.getElementById('jsonselectcodition').value;
    var filter_row_id = document.getElementById('filter_row_id').value;

     if(selectmain==='AE') {
       document.getElementById('filter').value = 'All Employees';

     } else {
       var selectsub = document.getElementById('filterlist').value;

       document.getElementById('filter').value = selectsub;

     }
     var sendjsoncontent = document.getElementById('filterlist').value;
     console.log(sendjsoncontent);
     console.log(sendjsoncontent);
     console.log(sendjsoncontent+application_table);
     var filter_condition = document.getElementById('filter_condition').value;
     console.log(filter_condition);
     $.ajax({
       url: "ajax.php",
       data:'page=sendtofiltertable&row_id='+filter_row_id+'&appln_table='+application_table+'&jsoncontent='+sendjsoncontent+'&filter_condition=check=1'+encodeURIComponent(filter_condition),
       type: "POST",
       success: function(response){
         console.log("Inserted row id ="+response);
         document.getElementById('filtertbl_rowid').value = response;
         <?php if($table == 'leave_types'){ ?>
          document.getElementById('fieldlist').value = jsonselectcodition;
       <?php  } ?>
         $('#kt_modal_dynamic_popup').modal('hide');
         //task-setting other modul close
          $('#kt_modal_dynamic_popup_task_setting').modal('hide');
      $('[data-bs-toggle="tooltip"]').tooltip();

       }
     });
   }

 </script>
