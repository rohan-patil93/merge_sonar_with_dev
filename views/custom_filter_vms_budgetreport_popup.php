
<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$filter_id = $_GET['filter_id'];
//print_r($_GET);
$company_id = $_SESSION['company_id'];
$filter_table = 'manpower_requisitions';
 $url = API."?mode=getbudgetfilterdetails&company_id=".$company_id."&filter_id=".$filter_id."&filter_table=".$filter_table;
$response = callAPI($url);
$tasks_details = json_decode($response,1);
$filter_list = $tasks_details['filter_list'];
$filter_category = $tasks_details['filter_option'];
/*$emp_count = json_decode($_GET['matchcount'],1);*/
//print_r($selectedlist);
//echo "<pre>";print_r($filter_list); //exit;

?>
<?php 


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

//print_r($emp_list_names);
if($emp_list_names!="") {
  echo '<script type="text/JavaScript">  
  populatenamelist(); 
  </script>' 
  ; 
}

?>   
<div class="modal-header">
 <h5 class="modal-title">
  Filter Options 
</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
 <i aria-hidden="true" class="ki ki-close"></i>
</button>
</div>
<form class="kt-form kt-form-label-right" action="" method="post">
 <div class="modal-body" >
  <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
   <div class="row">

    <div class="col-lg-12">
     <div class="bg-white rounded row mt-5"  id="filtershow">
     <input type="hidden" name="filterlist" id="filterlist" value="">
       <input type="hidden" name="filter_id" id="filter_id" value="<?php echo $filter_id; ?>">
     <input type="hidden" name="tablename" id="tablename" value="<?php echo $filter_table; ?>">

       <div class="col-lg-12 card card-custom card-border">
         <div class="accordion accordion-light accordion-svg-toggle" id="accordionExample7">
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
              <div class="card-title collapsed" data-toggle="collapse" data-target="#aaa<?php echo $targetid; ?>">
               <span class="svg-icon svg-icon-primary">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24" />
                  <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
                  <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
                </g>
              </svg>
            </span>
            <div class="card-label pl-4"><?php echo $filter['filter_name']; ?></div>
          </div>
        </div>
        <div id="aaa<?php echo $targetid; ?>" class="collapse mb-5" data-parent="#accordionExample7">
          <?php
                                 //print_r($fieldlist);
                               // echo $targettable_field;
                                //echo $targettable;

                                 //print_r($targettable,$targettable_field);
          $table_details = dyanamicselectdata($targettable, $targettable_field);
                                 // print_r($table_details); 
          ?>
          <select onchange="selectemployee()" class="form-control mt-5" id="kt_select2_4<?php echo $index;?>" name="empstatus[]" multiple="multiple">
           <?php 
           foreach ($table_details as $key => $value) { ?>
             <option value="<?php echo $key;?>" <?php if(in_array($key, array_keys((array)$selectedlist[$targettable_field]))) { ?> selected <?php  } ?> ><?php echo $value;?></option>
             <?php
           }
           ?>
         </select>
         <?php
         ?>
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
<!--end: Search Form -->
<!-- </div> -->
</div>
<div class="modal-footer mt-5" >
  <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Select</button>
</div>
</form>
<script type="text/javascript">
  var selectedemp="";
  function selectemployee(){
   var select_value = <?php echo $select_value_json; ?>;
   var application_table = document.getElementById('tablename').value;

   console.log("Values:"+application_table);
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
   var jsonresponse = JSON.stringify(obj); 
    console.log ('inserted to filtertable'+jsonresponse);
    selectedemp  =jsonresponse;

    document.getElementById('filterlist').value = jsonresponse;
  
}



function submitrom(){
  var application_table = document.getElementById('tablename').value;
  var sendjsoncontent = document.getElementById('filterlist').value;
  var sendjsoncontent = JSON.stringify(sendjsoncontent); 

  console.log(sendjsoncontent+application_table);
     $.ajax({
       url: "ajax.php",
       data:'page=sendtofiltertable&appln_table='+application_table+'&jsoncontent='+sendjsoncontent,
       type: "POST",
       success: function(response) {
         console.log("Inserted row id ="+response);
         document.getElementById('filtertbl_rowid').value = response;
    $('#kt_modal_KTDatatable_remote1').modal('hide');
       }
     });
 }
</script>
