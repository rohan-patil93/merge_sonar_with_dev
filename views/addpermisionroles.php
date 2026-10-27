<?php
  include('../includes/session.php');
  include('../dbconfig.php');
  include('../comm.php');
if($_POST['action']=="pagelist"){ 
   $id = json_encode($_POST['permision']);
   $url = API."?mode=applicationprocesspages&id=".$id; 
   $collfunction = callAPI($url);
   $resutlvalue = json_decode($collfunction,1);
?>
      <div class="form-group row">
        <div class="form-group col-lg-4">
       <h4 class="modal-title col-md-7" id="myModalLabel2" ><b>Page Permissions</b></h4>
     </div>
    <?php if(!empty($resutlvalue)){ ?> 
     <div class="form-group col-lg-6">
       <div class="checkbox-fade fade-in-primary">
      <label class="form-label">
        <input type="checkbox" id="checkpermisionalls" />
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span> Select Permissions
      </label>
    </div>
  </div><?php } ?>
</div>
     </div>
   <?php 

   $i = 0;
//print_r($resutlvalue);
$vdata1["where"] = array('role_short_code'=> $_POST['role_short_code'],'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=role_permissions&data=".$vdata; 
$respons = callAPI($vurl);
$responsarry = json_decode($respons,1);
foreach($resutlvalue as $resultlist){ 
 $valueg = filterArrayValue($responsarry,"application_process_page_id",$resultlist['id']);
 $valuegs = $valueg[0];
    ?>
      <div class="form-group row">
        <label class="col-md-2  control-label" style="margin-top:10px"><?php echo ucwords($resultlist['name']); ?></label>
        <input type="hidden" name="processname[<?php echo $i; ?>]" value="<?php echo $resultlist['application_page_name']; ?>">
        <input type="hidden" name="processpageid[<?php echo $i; ?>]" value="<?php echo $resultlist['id']; ?>">
          <input type="hidden" name="processtablename[<?php echo $i; ?>]" value="<?php echo checkTableName($resultlist['application_page_name']); ?>">
        <div class="col-md-10 m-b-30">
          <div class="checkbox-fade fade-in-primary">
            <label class="form-label">
             <input type="checkbox" class="pagecheckboxsss" id="checkboxRead<?php echo $i; ?>" name="pageslistread[<?php echo $i; ?>]"   <?php echo $valuegs['permission_view'] == '0'?"checked":"";?>  value="<?php echo $resultlist['id']; ?>">
             <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>View</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary">
          <label class="form-label">
            <input type="checkbox" class="pagecheckboxsss" id="checkboxWrite<?php echo $i; ?>" name="pageslistwrite[<?php echo $i; ?>]" <?php echo $valuegs['permission_insert'] == '0'?"checked":"";?> value="<?php echo $resultlist['id']; ?>">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>Insert</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary">
          <label class="form-label">
            <input type="checkbox" class="pagecheckboxsss" id="checkboxCreate<?php echo $i; ?>" name="pageslistcreate[<?php echo $i; ?>]" <?php echo $valuegs['permission_update'] == '0'?"checked":"";?> value="<?php echo $resultlist['id']; ?>">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>Update</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary">
          <label class="form-label">
           <input type="checkbox" <?php echo $valuegs['permission_delete'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxdelete<?php echo $i; ?>"  name="pageslistdelete[<?php echo $i; ?>]"  >
           <span class="cr">
            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
          </span>
          <span>Delete</span>
        </label>
      </div>  
      <div class="checkbox-fade fade-in-primary">
        <label class="form-label"> 
         <input type="checkbox" <?php echo $valuegs['permission_print'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxprint<?php echo $i; ?>"  name="pageslistprint[<?php echo $i; ?>]"  >
         <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
        <span>Print</span>
      </label>
    </div>  
    <div class="checkbox-fade fade-in-primary">
      <label class="form-label"> 
        <input type="checkbox" <?php echo $valuegs['permission_import'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboximport<?php echo $i; ?>"  name="pageslistimport[<?php echo $i; ?>]"  >

        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
        <span>Import</span>
      </label>
    </div>
  <!--   <div class="checkbox-fade fade-in-primary">
      <label class="form-label"> 
       <input type="checkbox" <?php echo $valuegs['permission_export'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxexport<?php echo $i; ?>"  name="pageslistexport[<?php echo $i; ?>]"  >
       <span class="cr">
        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
      </span>
      <span>Export</span>
    </label>
  </div>  --> 
  <div class="checkbox-fade fade-in-primary">
    <label class="form-label"> 
      <input type="checkbox" <?php echo $valuegs['permission_download'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxdownload<?php echo $i; ?>"  name="pageslistdownload[<?php echo $i; ?>]"  >
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
      <span>Download</span>
    </label>
  </div>
  <div class="checkbox-fade fade-in-primary">
    <label class="form-label"> 
      <input type="checkbox" <?php echo $valuegs['permission_approval'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxapproval<?php echo $i; ?>"  name="pageslistapproval[<?php echo $i; ?>]"  >
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
      <span>Approval</span>
    </label>
  </div>
  <div class="checkbox-fade fade-in-primary">
    <label class="form-label"> 
      <input type="checkbox" <?php echo $valuegs['permission_analytic'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxanalytic<?php echo $i; ?>"  name="pageslistanalytic[<?php echo $i; ?>]"  >
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
      <span>Analytic</span>
    </label>
  </div>  
</div>
</div> 
<?php $i++; }  









}else{  


































    $applicatroleapi = API."?mode=getapplicationrole&application_roles=".$_POST['role_id']."&selectmodule_id=".$_POST['module_id']; 
    $applicatrolecall = callAPI($applicatroleapi);
    $result = json_decode($applicatrolecall,1);
    //echo "<pre>";print_r($result['module_setup']);echo "</pre>";
    $aruserdetails  = $result['application_roles'];
    $menulocation  = $result['menulocation'];
    
    $modulelist = $result['module_setup'];
    $menulocationse  =json_decode($aruserdetails['view_nav'],1);
    $resutlvalue  = $result['application_process_role'];
    $processseleclis = array();
    $newaray = array();
    foreach($resutlvalue as $resutlvalue){
      if($resutlvalue['is_active']==0){
          $processseleclis[] = $resutlvalue['application_process_id'];
        //array_push($processseleclis, );
      }
     // $newaray[$resutlvalue['application_process_id']] = $resutlvalue['id'];
    }

    $resultlist = $result['application_process'];
    $resutlvalue22 = $result['application_process_pages'];
    $responsarry = $result['role_permissions'];
    foreach($result['all_application_roles'] as $roleshortcode){
        if($aruserdetails['role_short_code'] !=$roleshortcode['role_short_code']){
          $roleshortcodes[] = $roleshortcode['role_short_code'];
        }
    }



  ?>
  <input type="hidden" id="usedemailids" value='<?php echo json_encode($roleshortcodes); ?>'>    
  <form id="addroleform">
   <input type="hidden" name="role_id" value="<?php echo $_POST['role_id']; ?>">
   <div class="row">
   <div class="form-group col-lg-3 col-md-12 col-sm-12 col-12">
    <label class="font-weight-bolder">Role Name</label>
    <input type="text" class="form-control text-capitalize" id="role_name" name="role_name" value="<?php echo $aruserdetails['name']; ?>">
  </div>
  <div class="form-group col-lg-3 col-md-12 col-sm-12 col-12">
    <label class="font-weight-bolder">Short Code</label>
   <input type="text" class="form-control text-uppercase" id="role_short_code" name="role_short_code" value="<?php echo $aruserdetails['role_short_code']; ?>" data-rowid="1" onkeyup="checkMailidAvailableOrNot(this.value)">
    <span class="text-danger f-12" id="iduser_email" style="display:none">Already Used This Short Code</span>
  </div>
  <div class="form-group col-lg-3 col-md-12 col-sm-12 col-12">
    <label class="font-weight-bolder">Module</label>
    <input type="hidden" name="application_module" id="application_module" value="<?php echo $modulelist['module_id']; ?>">
    <input type="text" class="form-control" value="<?php echo $modulelist['display_name']; ?>" readonly>

    
 
  </div>
  <div class="form-group col-lg-3 col-md-12 col-sm-12 col-12">
      <label class="font-weight-bolder">Menu Location</label></br>
    <?php foreach($menulocation as $menulocations){  ?>
          <div class="checkbox-fade fade-in-primary mt-2">
            <label class="form-label">
             <input type="checkbox"  name="menulocation[]"   <?php echo (in_array($menulocations['genc_detail_code'], (array)$menulocationse))?"checked":"";?>  value="<?php echo $menulocations['genc_detail_code'];?>">
             <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span><?php echo $menulocations['genc_detail_name'];?></span>
          </label>
        </div>
        <?php } ?> 

  </div>
</div>
  <div class="form-group row">
   <h4 class="modal-title" id="myModalLabel2"><b>Role Permissions</b></h4>
 </div>
 <div class="form-group row">
   <label class="col-lg-3 col-md-6 col-sm-6 col-12 control-label font-weight-bolder"  onclick ="checkall()">Administrator Access  </label>

   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="checkbox-fade fade-in-primary">
      <label class="form-label">
        <input type="checkbox" class="select_all" id="checkbox" />
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span> Select All
      </label>
    </div></div>
  </div> 
  <div class="form-group row">
    <?php
  $i= 0;
  foreach($resultlist as $resultlist){ 
    ?>               
    <div class="col-lg-4 col-md-6 col-sm-6 col-12 mt-3">
    <div class="checkbox-fade fade-in-primary">
      <label class="form-label">
        <input type="checkbox" class="checkboxsss" <?php echo in_array($resultlist['id'], (array)$processseleclis)?"checked":""; ?>  id="checkbox<?php echo $i; ?>" name="permision[<?php echo $i; ?>]"  value="<?php echo $resultlist['id']; ?>"> <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
          </span> <?php echo ucwords($resultlist['process_name']); ?>
        </label>
      </div>
    </div>
    <?php $i++; } ?>
  </div>
    <div class="pageswooow">
  <div class="form-group row">
        <div class="form-group col-lg-4">
       <h4 class="modal-title col-md-7" id="myModalLabel2" ><b>Page Permissions</b></h4>
     </div>
    <?php if(!empty($resutlvalue22)){ ?> 
     <div class="form-group col-lg-6">
       <div class="checkbox-fade fade-in-primary">
      <label class="form-label">
        <input type="checkbox" id="checkpermisionalls" />
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span> All Permissions
      </label>
    </div>
  </div>
<?php } ?>
</div>
<?php
   $i = 0;
foreach($resutlvalue22 as $resultlist){ 
 $valueg = filterArrayValue($responsarry,"application_process_page_id",$resultlist['id']);
 $valuegs = $valueg[0];
    ?>
      <div class="form-group row">
        <label class="col-md-2  control-label" style="margin-top:10px"><?php echo ucwords($resultlist['name']); ?></label>
        <input type="hidden" name="processname[<?php echo $i; ?>]" value="<?php echo $resultlist['application_page_name']; ?>">
         <input type="hidden" name="processtablename[<?php echo $i; ?>]" value="<?php echo checkTableName($resultlist['application_page_name']); ?>">


        <input type="hidden" name="processpageid[<?php echo $i; ?>]" value="<?php echo $resultlist['id']; ?>">

        <div class="col-md-10 m-b-30">
          <div class="checkbox-fade fade-in-primary mt-2">
            <label class="form-label">
             <input type="checkbox" class="pagecheckboxsss" id="checkboxRead<?php echo $i; ?>" name="pageslistread[<?php echo $i; ?>]"   <?php echo $valuegs['permission_view'] == '0'?"checked":"";?>  value="<?php echo $resultlist['id']; ?>">
             <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>View</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary mt-2">
          <label class="form-label">
            <input type="checkbox" class="pagecheckboxsss" id="checkboxWrite<?php echo $i; ?>" name="pageslistwrite[<?php echo $i; ?>]" <?php echo $valuegs['permission_insert'] == '0'?"checked":"";?> value="<?php echo $resultlist['id']; ?>">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>Insert</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary mt-2">
          <label class="form-label">
            <input type="checkbox" class="pagecheckboxsss" id="checkboxCreate<?php echo $i; ?>" name="pageslistcreate[<?php echo $i; ?>]" <?php echo $valuegs['permission_update'] == '0'?"checked":"";?> value="<?php echo $resultlist['id']; ?>">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            <span>Update</span>
          </label>
        </div>  
        <div class="checkbox-fade fade-in-primary mt-2">
          <label class="form-label">
           <input type="checkbox" <?php echo $valuegs['permission_delete'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxdelete<?php echo $i; ?>"  name="pageslistdelete[<?php echo $i; ?>]"  >
           <span class="cr">
            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
          </span>
          <span>Delete</span>
        </label>
      </div>  
      <div class="checkbox-fade fade-in-primary mt-2">
        <label class="form-label"> 
         <input type="checkbox" <?php echo $valuegs['permission_print'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxprint<?php echo $i; ?>"  name="pageslistprint[<?php echo $i; ?>]"  >
         <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
        <span>Print</span>
      </label>
    </div>  
    <div class="checkbox-fade fade-in-primary mt-2">
      <label class="form-label"> 
        <input type="checkbox" <?php echo $valuegs['permission_import'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboximport<?php echo $i; ?>"  name="pageslistimport[<?php echo $i; ?>]"  >

        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
        <span>Import</span>
      </label>
    </div>
<!--     <div class="checkbox-fade fade-in-primary mt-2">
      <label class="form-label"> 
       <input type="checkbox" <?php echo $valuegs['permission_export'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxexport<?php echo $i; ?>"  name="pageslistexport[<?php echo $i; ?>]"  >
       <span class="cr">
        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
      </span>
      <span>Export</span>
    </label>
  </div>  --> 
  <div class="checkbox-fade fade-in-primary mt-2">
    <label class="form-label"> 
     <input type="checkbox" <?php echo $valuegs['permission_download'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxdownload<?php echo $i; ?>"  name="pageslistdownload[<?php echo $i; ?>]"  >
     <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span>
    <span>Download</span>
  </label>
</div> 
<div class="checkbox-fade fade-in-primary">
    <label class="form-label"> 
      <input type="checkbox" <?php echo $valuegs['permission_approval'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxapproval<?php echo $i; ?>"  name="pageslistapproval[<?php echo $i; ?>]"  >
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
      <span>Approval</span>
    </label>
  </div>
  <div class="checkbox-fade fade-in-primary">
    <label class="form-label"> 
      <input type="checkbox" <?php echo $valuegs['permission_analytic'] == '0'?"checked":"";?> class="pagecheckboxsss" id="checkboxanalytic<?php echo $i; ?>"  name="pageslistanalytic[<?php echo $i; ?>]"  >
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>
      <span>Analytic</span>
    </label>
  </div>   
</div>
</div> 
<?php $i++; }  ?>

</div>  
    <div class="modal-footer">
     <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
     <button type="button" class="btn btn-primary font-weight-bold" onclick="addRole()">Save changes</button>
   </div>
 </form>
 <?php }  ?>  