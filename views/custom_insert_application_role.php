<?php 
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['role'] = '1';
$apiurl = API."?mode=getapplicationroles";
$callapi = callAPI($apiurl);
$result = json_decode($callapi, 1);
//echo "<pre>";print_r($result);echo "</pre>";
$resultlist = $result['application_roles'];
$application_process = $result['application_process'];
$module_setup = $result['module_setup'];
?>
<script type="text/javascript">
  function checkMailidAvailableOrNot(email_id){
    var usedemailids = $("#usedemailids").val();
    var data  = JSON.parse(usedemailids);
    var emailid = $.trim(email_id);
    if ($.inArray(emailid, data) != -1)
    {      
      $("#iduser_email").show();
    }else{
      $("#iduser_email").hide();
   }
 }
 function changeModule(module_id){
 	$(".hideall").hide('slow');
 	if(module_id != '0'){
 		$(".modulegrpid_"+module_id).show('slow');
 		$(".addmore").show('slow');
 	}
 }
</script>
<div class="card card-custom card-sticky " id="kt_page_sticky_card">
  <div class="card-header table-card-header">
    <h5> Application roles</h5>
    <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light ">
        <i class="icofont icofont-reply"></i>  Back
      </a>
    </div>
  </div>
  <div class="card-body"> 
  	<div class="row mb-3">
  		<div class="col-lg-4">
  			<label class="form-label">Module</label>
  			<select class="form-select js-example-basic-single" id="module_id" onchange="changeModule(this.value)" >
  				<option value="0">Select Any One Module</option>
				<?php foreach($module_setup as $module_setups){ ?>
					<option value="<?php echo $module_setups['module_id']; ?>" ><?php echo $module_setups['display_name']; ?></option>
				<?php  } ?>
			</select>
  		</div>
  		<div class="col-lg-4"></div>
  		<div class="col-lg-4">
  			<!-- <div class="input-group input-group-button mb-0" >
		        <input type="text" id="dynamicfilterhideshow" data-id='applicationrolesid' data-type="div" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search"> 
		        <span class="input-group-text addon-style">
		            <label class=""><span class="ti-search text-white mt-2"></span></label>
		        </span>
		    </div> --> 
  		</div>
  		
  	</div>
    <div class="row mt-2">
      <?php
      $i= 1; $jj=0;
      foreach($resultlist as $resultlist){ 
        if($jj == '0'){  $i= 2;  ?>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12 hideall addmore" style="display: none"> 
            <div class="card card-custom cursor-pointer card-stretch" onclick="addrolemodal()">
              <div class="card-header">
                <div class="card-title">
                  <img style="width: 100%;" src="assets/images/role_edite.png">
                </div>
              </div>
              <div class="card-body" style="min-height: 106px;">
                <h3 class="text-gray-600" style="text-align: center;">Add New Role</h3>
              </div>
            </div>
          </div>
        <?php } ?>    
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 hideall modulegrpid_<?php echo $resultlist['application_module']; ?> " id="applicationrolesid" style="display: none"> 
          <div class="card card-custom  card-stretch">
            <div class="card-header">
              <div class="card-title">
                <h4 class="text-gray-600"><?php echo ucwords($resultlist['display_name']); ?></h4>
              </div>
              <hr>
            </div>
            <div class="card-body">
              <ul class="legend list-unstyled text-gray-600" style="min-height: 215px;">
                <?php
                $resutlvalue = $application_process[$resultlist['id']];
                foreach($resutlvalue as $key => $resutlvalue){  if($key==5){ break; }?>
                <li>
                  <p>
                    <span class="icon"><i style="color: cornflowerblue;" class="fa fa-minus"></i></span> <span class="name"><?php echo $resutlvalue['process_name']; ?></span>
                  </p>
                </li>
              <?php }
              if($count > '5'){ ?>
                <li>
                  <p>
                    <span class="icon"><i style="color: cornflowerblue;" class="fa fa-minus"></i></span> <span class="name">and <?php echo ($count-5); ?> more...</span>
                  </p>
                </li>
              <?php  } ?>
            </ul>
              <button type="submit" name="submit" class="btn btn-primary w-100" style="background-color: #f5f8fa; color: #7e8299;" data-toggle="modal" onclick="addrolemodal('<?php echo $resultlist['id']; ?>')"><b><i class="fa fa-edit"></i> Edit Role</b></button>
              <a href="index.php?option=COM_VIEW_APPLICATION_ROLE&Rid=<?php echo deocdeId("id:".$resultlist['id']); ?>&name=<?php echo $resultlist['display_name']; ?>" class="btn btn-success  mt-2 w-100" target="_blank"><i class="icofont icofont-plus icon-white "></i> Assign User</a>
            
        </div>
      </div>
    </div>
    <?php $i++; $jj++; /*if($i==5){ echo '</div><div class="row mt-4">'; $i=1; }*/  } ?>
  </div>          
  </div>
</div>


<div class="modal fade updaterolemodal" id="large-Modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Role</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body ajaxmodal j-pro j-content">
      </div>
    </div>
  </div>
</div>