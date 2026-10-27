<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$filter_id = $_GET['filter_id'];
$filter_table = 'employees';
$url = API."?mode=getfilterdetails&company_id=".$company_id."&filter_id=".$filter_id."&filter_table=".$filter_table;
$response = callAPI($url);
$tasks_details = json_decode($response,1);
$filter_list = $tasks_details['filter_list'];
$filter_category = $tasks_details['filter_option'];
$emp_list = $tasks_details['emp_list'];
$selectedlist = array();
$emp_count = 0;
$emp_list_names = array();
$selectedoption = 'All Employees';
$select_value = array();

if($filter_id != ""){
  $filtercontent = trim($tasks_details['filter_data']['content'],'"');
  $selectedlist = json_decode($filtercontent,1);
  if(!is_array($selectedlist)){
    $selectedlist = array();
  }
}

if(count((array)$selectedlist) > 0){
  $selectedoption = 'Only Some Employees';
  $emp_count = isset($selectedlist['matches_count']) ? $selectedlist['matches_count'] : 0;
  $emp_list_names = isset($selectedlist['matches_names']) ? $selectedlist['matches_names'] : array();
}

if(count((array)$filter_category) > 0){
  $index = 0;
  foreach ($filter_category as $index=>$filter) {
    $index++;
    $select_value[$filter['fieldname']] = "exampleSelect".$index;
  }
}

$emp_list_names_json = json_encode($emp_list_names,1);
$emp_list_json = json_encode($emp_list,1);
$select_value_json = json_encode($select_value);
?>
<style type="text/css">
  .filter-bulktransfer-hidden-container {
    display: none;
  }
  input.select2-search__field{
    padding: 5px 10px !important;
    border-radius: 3px;
    margin: 6px 0 0 0;
  }
  .card-title.collapsed:after{
    content: "\e64b";
    font-family: "themify";
    right: 10px;
    position: absolute;
    font-size: 11px;
    top: 18px;
  }
  .card-title:after{
    content: "\e648";
    font-family: "themify";
    right: 10px;
    position: absolute;
    font-size: 11px;
    top: 18px;
  }

  /* Select2 container ko relative position dein taaki dropdown uske respect mein open ho */
  .filter-employees .select2-container {
      width: 100% !important;
  }

  /* Dropdown ko proper position par rakhne ke liye */
  .filter-employees .select2-container--default .select2-dropdown {
      position: absolute !important;
      top: 100% !important;
      left: 0 !important;
      z-index: 9999 !important;
      margin-top: 4px;
  }

  /* Accordion body ya card container se overflow hidden ko hatayein agar koi ho */
  .filter-employees .card-border, 
  .filter-employees .card-header, 
  .filter-employees .collapse {
      overflow: visible !important;
  }
</style>

<div class="d-none filter-bulktransfer-php-data"
  data-emp-list-names='<?php echo $emp_list_names_json; ?>'
  data-select-value='<?php echo $select_value_json; ?>'
  data-emp-list='<?php echo $emp_list_json; ?>'>
</div>

<div class="modal-header">
  <h5 class="modal-title">Filter Options</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form class="kt-form kt-form-label-right j-pro" action="" method="post">
  <div class="modal-body j-content" >
    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
      <div class="row">
        <div class="col-lg-6">
          <label>Include</label>
          <input type="hidden" name="filterlist" id="filterlist" value="">
          <input type="hidden" name="filter_id" id="filter_id" value="<?php echo $filter_id; ?>">
          <input type="hidden" name="tablename" id="tablename" value="<?php echo $filter_table; ?>">
          <select id="filterfor" name="filterfor" class="form-control form-select js-example-basic-single filter-bulktransfer-filterfor">
            <?php
            if(count((array)$filter_list) > 0){
             foreach ($filter_list as $filter) {
              ?>
              <option <?php if($selectedoption==$filter['genc_detail_name']) {?> selected <?php } ?> value="<?php echo $filter['genc_detail_code'];?>"><?php echo $filter['genc_detail_name'];?></option>
            <?php
             }
            } ?>
          </select>
        </div>
        <div class="col-lg-6">
          <div id="empimages"> 

          </div>
        </div>
        <div class="col-lg-12">
          <div class="bg-white rounded row mt-4 <?php if($selectedoption !== 'Only Some Employees') { ?>filter-bulktransfer-hidden-container<?php } ?>" id="filtershow">
            <div class="col-lg-6">
              <label>Filter <?php echo $filter_table; ?> by</label>
              <i class="fas fa-user icon-nm pl-40"></i>
              <label>
                <span id="matches_count"><?php echo $emp_count;?> </span> Matches
              </label>
            </div>
            <div class="col-lg-6">
              <div class="card card-custom card-border">
                <div class="card-body">
                  <div class="accordion accordion-light accordion-svg-toggle filter-employees" id="accordionExample7">
                    <?php
                      $index = 0;
                      if(count((array)$filter_category) > 0){
                      foreach ($filter_category as $index=>$filter) {
                      $targetid= $filter['id'];
                      $targettable= $filter['application_tablename'];
                      $targettable_field= $filter['fieldname'];
                      $index++;
                    ?>
                    <div class="card-border">
                      <div class="card-header border p-3 mb-2 cursor-pointer" id="headingOne7">
                        <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#aaa<?php echo $targetid; ?>">
                          <div class="card-label pl-4">
                            <?php echo $filter['filter_name']; ?>
                          </div>
                        </div>
                      </div>
                      <div id="aaa<?php echo $targetid; ?>" class="collapse mb-2 p-2 position-relative" data-parent="#accordionExample7">
                        <?php
                        $table_details = dyanamicselectdata($targettable, $targettable_field);
                        ?>
                        <div class="w-full">
                          <select multiple="multiple" class="form-control mt-5 form-select js-example-basic-multiple filter-bulktransfer-empstatus" name="empstatus[]" id="exampleSelect<?php echo $index;?>">
                          <?php
                            if(count((array)$table_details) > 0){
                            foreach ($table_details as $key => $value) { ?>
                            <option value="<?php echo $key;?>" <?php if(in_array($key, array_keys((array)$selectedlist[$targettable_field]))) { ?> selected <?php  } ?> ><?php echo $value;?></option>
                            <?php
                          }
                          } ?>
                          </select>
                        </div>
                      </div>
                      <?php }
                      } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" id="employee_names_list">
  <div class="modal-footer mt-5" >
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary mr-2 filter-bulktransfer-submit-btn">Select</button>
  </div>
</form>

<script type="text/javascript">
(function() {
 'use strict';

 var selectedemp = '';
 var eventsBound = false;

 function getPhpData() {
  var dataContainer = document.querySelector('#dynamic_popup1 .filter-bulktransfer-php-data');
  if (!dataContainer) {
   return {};
  }
  return {
   empListNames: dataContainer.dataset.empListNames ? JSON.parse(dataContainer.dataset.empListNames) : null,
   selectValue: dataContainer.dataset.selectValue ? JSON.parse(dataContainer.dataset.selectValue) : {},
   empList: dataContainer.dataset.empList ? JSON.parse(dataContainer.dataset.empList) : []
  };
 }

 function getInitials(name) {
  var parts = name.split(' ');
  var initials = '';
  for (var i = 0; i < parts.length; i++) {
   if (parts[i].length > 0 && parts[i] !== '') {
    initials += parts[i][0];
   }
  }
  return initials;
 }

 function buildAllEmployeesFilterList() {
  var phpData = getPhpData();
  var empList = phpData.empList || [];
  var matches = [];
  for (var i = 0; i < empList.length; i++) {
   matches.push({
    id_value: empList[i].id,
    display_name: empList[i].display_name,
    image: empList[i].image || ''
   });
  }
  return JSON.stringify({
   matches_count: matches.length,
   matches_names: matches
  });
 }

 function selectemployee() {
  var phpData = getPhpData();
  var select_value = phpData.selectValue;
  var application_table = document.getElementById('tablename').value;
  var str = '';
  var obj = {};

  for (var key in select_value) {
   if (!select_value.hasOwnProperty(key)) {
    continue;
   }
   var selectId = select_value[key];
   var selectedIds = $('#' + selectId).val() || [];
   var selectedKeyValue = {};
   var arrayOfSelecedIDs = [];

   for (var i = 0; i < selectedIds.length; i++) {
    var optVal = selectedIds[i];
    selectedKeyValue[optVal] = $('#' + selectId + ' option[value="' + optVal + '"]').text();
    arrayOfSelecedIDs.push(optVal);
   }

   if (arrayOfSelecedIDs.length > 0) {
    obj[key] = selectedKeyValue;
   }
   str += '&' + key + '=' + arrayOfSelecedIDs;
  }

  $.ajax({
   url: 'ajax.php',
   data: 'page=selectedfilter&table=' + application_table + str,
   type: 'POST',
   success: function(data) {
    var result = JSON.parse(data);
    document.getElementById('matches_count').innerHTML = result.count;
    obj.matches_count = result.count;
    obj.matches_names = result.details;

    var jsonData = {};
    for (var j = 0; j < result.count; j++) {
     var ids = result.details[j].id_value;
     jsonData[ids] = ids;
    }
    document.getElementById('employee_names_list').value = JSON.stringify(jsonData);

    var maxlimit = 10;
    var more = 0;
    if (result.count > maxlimit) {
     more = result.count - maxlimit;
    } else {
     maxlimit = result.count;
    }

    var imagelist = '  <label>Employees</label> <div class="text-start"><div class="symbol-group symbol-hover justify-content-center">';
    for (var k = 0; k < maxlimit; k++) {
     var imgpath = 'uploads/all/' + result.details[k].image;
     if (result.details[k].image != '') {
      imagelist += '<div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="' + result.details[k].display_name + '"><img alt="Pic" class="img-30 img-radius" src="' + imgpath + '" /></div>';
     } else {
      imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-success img-30" data-toggle="tooltip" title="' + result.details[k].display_name + '"><span class="symbol-label font-weight-bold">' + getInitials(result.details[k].display_name) + '</span></div>';
     }
    }

    if (more > 0) {
     imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-warning" data-toggle="tooltip" title="' + more + ' More"><a class="filter-bulktransfer-more-employees"><span class="symbol-label font-weight-bold">' + more + '+</span></a></div>';
    }
    imagelist += '</div></div>';

    document.getElementById('empimages').innerHTML = imagelist;

    var jsonresponse = JSON.stringify(obj);
    selectedemp = jsonresponse;
    document.getElementById('filterlist').value = jsonresponse;
   },
   error: function() {}
  });
 }

 function moreemployees() {
  var employee_names_list = document.getElementById('employee_names_list').value;
  jQuery.ajax({
   url: 'views/custom_view_employees_popup.php',
   data: 'empids=' + employee_names_list,
   type: 'GET',
   success: function(data) {
    $('#popup2').html(data);
    $('#kt_modal_KTDatatable_remote1').modal({ show: true });
   },
   error: function() {}
  });
 }

 function showfilter(value) {
  var filterShow = document.getElementById('filtershow');
  if (!filterShow) {
   return;
  }
  if (value == 'SE') {
   filterShow.style.display = 'block';
   filterShow.classList.remove('filter-bulktransfer-hidden-container');
  } else {
   filterShow.style.display = 'none';
   filterShow.classList.add('filter-bulktransfer-hidden-container');
  }
 }

 function submitrom() {
  var application_table = document.getElementById('tablename').value;
  var selectmain = document.getElementById('filterfor').value;
  var filterId = document.getElementById('filter_id').value;

  if (selectmain === 'AE') {
   var allEmpJson = buildAllEmployeesFilterList();
   document.getElementById('filterlist').value = allEmpJson;
   selectedemp = 'All Employees';
  } else {
   var filterlistVal = document.getElementById('filterlist').value;
   if (!filterlistVal || filterlistVal.trim() == '') {
    if (typeof swal === 'function') {
     swal('Validation Error', 'Please select at least one filter criteria.', 'error');
    }
    return;
   }
   selectedemp = filterlistVal;
  }

  var sendjsoncontent = JSON.stringify(document.getElementById('filterlist').value);
  var rowIdParam = filterId != '' ? '&row_id=' + filterId : '';

  $.ajax({
   url: 'ajax.php',
   data: 'page=sendtofiltertable&appln_table=' + application_table + '&jsoncontent=' + encodeURIComponent(sendjsoncontent) + rowIdParam,
   type: 'POST',
   success: function(response) {
    document.getElementById('filtertbl_rowid').value = response;
    $('#kt_modal_dynamic_popup').modal('hide');
    if (typeof getnamelist === 'function') {
     getnamelist(selectedemp);
    }
   }
  });
 }

 function bindEvents() {
  if (eventsBound) {
   return;
  }
  eventsBound = true;

  $(document).on('change', '.filter-bulktransfer-filterfor', function() {
   showfilter(this.value);
  });

  $(document).on('change', '.filter-bulktransfer-empstatus', function() {
   selectemployee();
  });

  $(document).on('click', '.filter-bulktransfer-submit-btn', function(e) {
   e.preventDefault();
   submitrom();
  });

  $(document).on('click', '.filter-bulktransfer-more-employees', function(e) {
   e.preventDefault();
   moreemployees();
  });
 }

 function initSelect2() {
  if (typeof $.fn.select2 === 'undefined') {
   return;
  }

  var $modal = $('#kt_modal_dynamic_popup');
  $('#dynamic_popup1 .js-example-basic-single, #dynamic_popup1 .js-example-basic-multiple').each(function() {
   if ($(this).hasClass('select2-hidden-accessible')) {
    $(this).select2('destroy');
   }
  });

  $('#dynamic_popup1 .js-example-basic-single').select2({
   width: '100%',
   dropdownParent: $modal
  });
  $('#dynamic_popup1 .js-example-basic-multiple').select2({
   width: '100%',
   dropdownParent: $modal
  });
 }

 function initFilterBulktransferPopup() {
  bindEvents();
  initSelect2();

  var filterFor = document.getElementById('filterfor');
  if (filterFor) {
   showfilter(filterFor.value);
  }

  var phpData = getPhpData();
  if (phpData.empListNames && phpData.empListNames.length > 0) {
   selectemployee();
  }
 }

 initFilterBulktransferPopup();
})();
</script>
