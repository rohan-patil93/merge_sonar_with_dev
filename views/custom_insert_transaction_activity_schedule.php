 <?php
  if(isset($_POST['submit'])){

     foreach ($_POST['check_list_name'] as $key => $value) {
       $listdata[$key]['name'] = $value;
       $listdata[$key]['check_value'] = $_POST['check_list'][$key] == 'on' ? 1 :"";
     }
    $data['updated_date'] = nowDate();
    $data['updated_by'] = $_SESSION['userid'];
    $data['check_list'] = json_encode($listdata,1);
    $data['action_flag'] =  $_POST['action_flag'];
    $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=transaction_activity_schedule&method=post";
    $response = file_post_contents($url, $data, 'update');
  }
  $title = viewTitle($_GET['option']);
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=transaction_activity_schedule";
  $response = callAPI($url);
  $trdata = json_decode($response,1);

  $wrprt["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0,'id'=>$trdata['task_id']);
  $prtcon = urlencode(json_encode($wrprt));
  $prturl = API."?mode=gettabledetailswithconditions&table=process_task_settings&data=".$prtcon;
  $prtdata = callAPI($prturl);
  $data = json_decode($prtdata,1);
  $data = $data['0'];

  $data1q["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
  $ssdata = urlencode(json_encode($data1q));
  $csuurl = API."?mode=gettabledetailswithconditions&table=company_documents&data=".$ssdata;
  $esudata = callAPI($csuurl);
  $document_details = json_decode($esudata,1);
  foreach ($document_details as $document) {
    $documents[$document['id']] = $document;
  }
  ?>
  <style>
  .radio-inline .form-label input[type="radio"]{
     display: none;
  }
</style>
 <div class="d-flex flex-column-fluid">
   <div class="container-fluid " >
      <div class="card card-custom <?php echo $card_sticky; ?> j-content" id="kt_page_sticky_card">


<div class="card-header mb-3">
    <h5><?php echo $title;  ?></h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>
         <div class="card-body p-1">
            <div class="row ">
              <div class="col-lg-6">
                 <div class="row justify-content-center py-10 px-8 py-lg-12 px-lg-10 ">
                    <div class="col-xl-12 col-xxl-7">
                       <form  method="post" action="" enctype="multipart/form-data">
                          <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                             <div class="row">
                                <div class="col-xl-9">
                                   <span class="form-label">Task Name </span>
                                   <input aria-label="Task Name" type="text" class="form-control" name="task_name" value="<?php echo $data['task_name']; ?>" />
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-xl-9 mt-5">
                                   <span class="form-label">Description</span>
                                   <textarea aria-label="Description" style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $data['description']; ?> </textarea>
                                </div>
                             </div>
                             <?php $files = json_decode($data['files'],1);
                                    if(count((array)$files)>0){

                                    ?>
                             <div class="row">
                                <div class="col-xl-6 mt-5">
                                   <span class="form-label"> <b> Download File(s)</b></span>
                                   <div id="divshowfiles">
                                      <?php  foreach ($files as $key_file=>$file) {
                                         $link ='uploads/all/'.$documents[$key_file]['documnet_path'];
                                          if($documents[$key_file]['documnet_path_type'] == 1){
                                             $link = $documents[$key_file]['documnet_path'];
                                          }
                                         ?>
                                      <div ><a target="_blank" href="<?php echo $link; ?>" class=" ml-4 text-dark" ><?php echo $file; ?></a></div>
                                      <?php } ?>
                                   </div>
                                </div>
                                <?php }  ?>
                             </div>
                             <button  class="btn btn-primary mr-2" ><i class="la la-check"></i>Send Mail</button>
                          </div>

                    </div>
                 </div>

               <div class="col-lg-6">
                <div class="row  py-10 px-8 py-lg-12 px-lg-10">
                   <h6 class="text-left mb-3 "> Check List</h6>
                  <?php
                    $checklist = $trdata['check_list'];
                    $checklist = json_decode($checklist,1);
                    foreach ($checklist as $key => $list) { ?>
            <input type="hidden"  name="check_list_name[<?php echo $key; ?>]" value="<?php echo $list['name']; ?>">
            <div class="checkbox-fade fade-in-primary mb-2" name="individual_approvalcheck">
                          <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" <?php if($list['check_value']=='1'){ echo "checked ";
} ?> class="itemRow checkbox1" name="check_list[<?php echo $key; ?>]" value="">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span><?php echo $list['name'];  ?>
                          </label>
                        </div>

                   <?php } ?>
                    </div>
                     <div class="form-group col-lg-6 px-10">
         <span class="form-label">Task Complete</span></br>


<div class="form-radio">

  <div class="radio radio-inline">
    <label class="form-label">
      <input aria-label="Radio option" type="radio" <?php if($trdata['action_flag']==0){ ?> checked <?php
} ?> name="action_flag" value="0">
      <i class="helper"></i>YES
    </label>
  </div>
  <div class="radio radio-inline">
    <label class="form-label">
    <input aria-label="Radio option" type="radio" <?php if($trdata['action_flag']==1){ ?> checked <?php
} ?> name="action_flag" value="1">
      <i class="helper"></i>NO
    </label>
  </div>

</div>

                </div>


                  </div>
               </div>

             <div class="card-footer">
                <div class="row">
<?php if($_SESSION['emp_id'] == $trdata['assigned_to']){  ?>
                  <div class="col-lg-3">
                      <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                      <input type="hidden" name="submit" value="Update" />
                      <button  class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
                </div>
<?php } ?>
                </div>
             </div>
           </form>
         </div>
      </div>
   </div>
</div>
