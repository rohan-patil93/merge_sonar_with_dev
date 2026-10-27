<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$data1q["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
                           //print_r($data1q); //exit;
$ssdata = urlencode(json_encode($data1q));
$csuurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$ssdata;
                           //echo  $csuurl ;
$esudata = callAPI($csuurl);
$document_details = json_decode($esudata,1);
//print_r($document_details);

$selectedlist = json_decode($_GET['filelist'],1);
print_r($selectedlist);
$file_array = array_keys($selectedlist);

?>
<script type="text/javascript">
 function selectfiles(){
   var elements = document.getElementById('kt_select2_42').childNodes;
   var selectedKeyValue = {};
   var arrayOfSelecedIDs=[];

   for(i=0;i<elements.length;i++){
    if(elements[i].selected){
     selectedKeyValue[elements[i].value]=elements[i].textContent;
     arrayOfSelecedIDs.push(elements[i].value)
   }
 }

 console.log("-------------------"+selectedKeyValue);
            var jsonresponse = JSON.stringify(selectedKeyValue); 
            //var jsonresponse = JSON.parse(jsonresponse); 

            //console.log("jsonformat:"+JSON.stringify(selectedKeyValue));
            console.log(jsonresponse);
            document.getElementById('files').value = jsonresponse;
}

 function submitrom(){

    $.ajax({
      success: function(response) {
       // console.log(response);
            $('#kt_modal_dynamic_popup').modal('hide');
            console.log("[closed]");
                getfile();
               
              //window.location.reload();
      }
    });
  }
</script>

       <div class="modal-header">
         <h5 class="modal-title">
          Add Files 
        </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 
     </div>


     <form class="kt-form kt-form-label-right" action="" method="post">

      <div class="modal-body" >
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
         <div class="row">
          <div class="col-lg-3">
          </div>
          <div class="col-lg-6">
          </div>
          <div class="col-lg-3">
          </div>

          <div class="col-lg-3">
          </div>
          <div class="col-lg-6">
           <!-- Start content -->
           <label>Select files</label>
                <select onchange="selectfiles()" class="form-control mt-5 form-select js-example-basic-multiple" id="kt_select2_42" name="filelist[]" multiple="multiple">
           <?php 
           foreach ($document_details as $table_data) {
           $field_name='document_name';
            ?>
             <option value="<?php echo $table_data['id'];?>" <?php if(in_array($table_data['id'], $file_array)) { ?> selected <?php  } ?> ><?php echo $table_data[$field_name];?></option>
           <?php                      }
           ?>
         </select>
          <!-- end content -->


        </div>

        <div class="col-lg-3">
        </div>


      </div>
    </div>
  </div>

  <!--end: Search Form -->

  <!-- </div> -->


  <div class="modal-footer mt-5" >
     <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
     
   <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Attach files</button>
 </div>
</form>
<!-- Select 2 js -->
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<!-- Multiselect js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>