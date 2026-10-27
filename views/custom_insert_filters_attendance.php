
<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$filter_id = $_GET['filter_id'];
$filter_table = 'employees';
$table = TBL_PROCESS_TASK_SETTINGS;
$url = API."?mode=gettasksdetails&company_id=".$company_id."&filter_id=".$filter_id."&filter_table=".$filter_table;
$response = callAPI($url);
$tasks_details = json_decode($response,1);
$filter_list = $tasks_details['filter_list'];
$filter_category = $tasks_details['filter_option'];

$emp_list = $tasks_details['emp_list'];
$selectedlist = json_decode(trim($tasks_details['filter_data']['content'],'"'),1);
if( empty($selectedlist)){
 $selectedoption = 'All Employees';
}else {
 $selectedoption = 'Only Some Employees';
 ?>
 <script type="text/javascript">document.getElementById('filtershow').style.display = 'block';</script>
 <?php
}

$emp_count = $selectedlist['matches_count'];
$emp_list_names = $selectedlist['matches_names'];
$emp_list_names  = json_encode($emp_list_names,1);

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
<div class="modal-header">
 <h5 class="modal-title">
  Filter Options
</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form-label-right j-pro" action="" method="post">
 <div class="modal-body j-content" >
  <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
   <div class="row">
    <div class="col-lg-6">
     <span>Include</span>
     <input type="hidden" name="filterlist" id="filterlist" value="">
     <input type="hidden" name="filter_id" id="filter_id" value="<?php echo $filter_id; ?>">
     <input type="hidden" name="tablename" id="tablename" value="<?php echo $filter_table; ?>">

     <select aria-label="Filterfor" onchange="showfilter(this.value);" id="filterfor" name="filterfor" class="form-control form-select js-example-basic-single">
      <?php
      foreach ($filter_list as $filter) {
       ?>
       <option <?php if($selectedoption==$filter['genc_detail_name']) {?> selected <?php
} ?> value="<?php echo $filter['genc_detail_code'];?>"><?php echo $filter['genc_detail_name'];?></option>
     <?php } ?>
   </select>
 </div>
 <div class="col-lg-6">
  <select aria-label="Option" id="sel_option" class="form-control form-select js-example-basic-single"></select>


                        </div>
                        <div class="col-lg-12">
                         <div class="bg-white rounded row mt-5" style="display: none"  id="filtershow">
                          <div class="col-lg-6">
                           <span>Filter <?php echo $filter_table; ?> by</span>
                           <i class="fas fa-user icon-nm pl-40"></i>
                           <span><span id="matches_count">  <?php echo $emp_count;?> </span> Matches</span>
                         </div>
                         <div class="col-lg-6 card card-custom card-border p-2">
                           <div class="accordion accordion-light accordion-svg-toggle" id="accordionExample7_att">
                            <?php
                            $index = 0;
                            foreach ($filter_category as $filter) {
                             $targetid= $filter['id'];
                             $targettable= $filter['application_tablename'];
                             $targettable_field= $filter['fieldname'];
                             $index++;
                             $select_value[$targettable_field] = "kt_select2_4".$index;
                             ?>
                             <div class="card card-border">
                               <div class="card-header" id="headingOne7">
                                <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#aaa<?php echo $targetid; ?>">

                              <div class="card-label pl-4"><?php echo $filter['filter_name']; ?></div>
                            </div>
                          </div>
                          <div id="aaa<?php echo $targetid; ?>" class="collapse mb-5" data-parent="#accordionExample7_att">
                            <?php

                            $table_details = dyanamicselectdata($targettable, $targettable_field);
                            ?>
                            <select aria-label="Select option" onchange="selectemployee()" class="form-control mt-5 form-select js-example-basic-multiple" id="kt_select2_4<?php echo $index;?>" name="empstatus[]" multiple="multiple">
                             <?php
                             foreach ($table_details as $key => $value) { ?>
                               <option value="<?php echo $key;?>" <?php if(in_array($key, array_keys((array)$selectedlist[$targettable_field]))) { ?> selected <?php
} ?> ><?php echo $value;?></option>
                               <?php
                             }
                             ?>
                           </select>

                         </div>
                       <?php }
                       $select_value_json = json_encode($select_value);

                       ?>
                       <input type="hidden" id="select_value" name="select_value" value='<?php echo json_encode($select_value); ?>' >
                     </div>

                   </div>
                 </div>

               </div>
             </div>
           </div>
         </div>


       </div>
       <div class="modal-footer mt-5" >
       <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Select</button>
      </div>
    </form>
    <script type="text/javascript">
      var selectedemp="";
     function selectemployee(){
       var select_value = <?php echo $select_value_json; ?>;
       var application_table = document.getElementById('tablename').value;

   console.log(select_value);
   var str = "";
   var obj = {};
   for(var key in select_value) {

     console.log("Key: " + key + " value: " + select_value[key]);
     var elements = document.getElementById(select_value[key]).childNodes;
     var selectedKeyValue = {};
     var arrayOfSelecedIDs=[];

     for(i=0;i<elements.length;i++){
       if(elements[i].selected){
        selectedKeyValue[elements[i].value]=elements[i].textContent;
        obj[key] = selectedKeyValue;
        arrayOfSelecedIDs.push(elements[i].value)
      }
    }
    str += '&'+key+'='+arrayOfSelecedIDs;

  }
  console.log('sha'+str);

  $.ajax({
   url: "ajax.php",
   data:'page=selectedfilter&table='+application_table+str,
   type: "POST",
   success:function(data){
     var result = JSON.parse(data);
                document.getElementById("matches_count").innerHTML = result.count;
                obj['matches_count'] = result.count;
                obj['matches_names'] = result.details;

                 var sel = document.getElementById('sel_option');
                 for(var i = 0; i < result.details.length; i++) {

                  var opt = document.createElement('option');
                  opt.innerHTML = result.details[i].display_name;
                  opt.value = result.details[i].id_value;
                  sel.appendChild(opt);
                }

                var jsonresponse = JSON.stringify(obj);
                console.log ('ravi'+jsonresponse);
                selectedemp  =jsonresponse;

                document.getElementById('filterlist').value = jsonresponse;

              },
              error:function (){}
            });

}

function showfilter(value) {
     if(value=='SE') {
       document.getElementById('filtershow').style.display ='block';
     } else {
       document.getElementById('filtershow').style.display ='none';

     }
   }


   function submitrom(){
     var selectmain = document.getElementById('filterfor').value;
     if(selectmain==='AE') {
       getnamelist('All Employees');
     } else {
       getnamelist(selectedemp);
     }
   }
 </script>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
