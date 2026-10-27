<?php
if(in_array(ADMIN_ROLE,(array)$_SESSION['role_ids']) || in_array(VMS_ADMIN_ROLE,(array)$_SESSION['role_ids'])){    
  $wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','user_type'=>'3');
}else{
  $wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','user_type'=>'3','created_by'=>$_SESSION['userid']);
}
$wher["field"] = "id,first_name,display_name,user_email,user_mobile,created_date,user_id,role_id,company_name";
$encodeurl = urlencode(json_encode($wher));
$urlcat = API."?mode=gettabledetailswithconditions&table=application_user_invitations&data=".$encodeurl;  
$invitationres = callAPI($urlcat);
$invitationlis = json_decode($invitationres,1);
if(!in_array(SUPERADMIN_ROLE,(array)$_SESSION['roles'])){ 
  foreach($invitationlis as $invitationlis){
    if($invitationlis['role_id'] != ADMIN_ROLES){
      $invitationlists[] =  $invitationlis;
    }
  }
}else{
  $invitationlists =  $invitationlis;
}
$wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wher["field"] = "id,image,display_name,first_name,user_id,company,image";
$userencodeurl = urlencode(json_encode($wher));
$userurlcat = API."?mode=gettabledetailswithconditions&table=vendors&data=".$userencodeurl;  
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$applicationusers = array();
foreach($useruresponse as $useruresponse1){
  $applicationusers[$useruresponse1['user_id']] = $useruresponse1;
}

$userurlcat = API."?mode=gettabledetailswithconditions&table=vendor_registers&data=".$userencodeurl;  
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$vendorregisters = array();
foreach($useruresponse as $useruresponse2){
  $vendorregisters[$useruresponse2['user_id']] = $useruresponse2;
}
//print_r($vendorregisters);
// $invitationlists = $invitationlist['invitation_list'];
// $vendors = $invitationlist['vendors'];
// foreach($vendors as $vendor){
// 	$vend_array[$vendor['user_id']] = $vendor;
// }
if(isset($_SESSION['message']) && $_SESSION['message'] !=""){
  unset($_SESSION['message']);
  ?>
   <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            } 
        }
    </script><?php
}

?>
<script type="text/javascript">
	function resendrequest(val){
		$.ajax({
	        url: "ajax.php",
	        data:'page=resenduserdetails&id='+val,
	        type: "POST",
	        success:function(data){
	        console.log(data);
	         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Invitation Send To User Successfully','');
	        },
	        error:function (){}
    	});

	}
	function dynamicinsertpopup(option){
		//alert("insert");
		jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
	}
   function statuspopup(status,id){
      //alert("data");
     var table_name = document.getElementById('table_name').value;
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

	function displaystausdrop(id,table,status_id){
		//alert("id");
		$.ajax({
	        url: "ajax.php",
	        data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
	        type: "POST",
	        success:function(data){
	        	console.log(data);
	            $("#dropdownlist_"+id).html(data);
	        },
	        error:function (){}
    	});
    	
	}
function listUserType(){
	var reg_invoit = $('input[name="reg_invoit"]:checked').val();
        if(reg_invoit=='1'){ 
            $(".register").show();
			      $(".invited").hide();			
        }else if(reg_invoit=='2'){
            $(".invited").show();
			      $(".register").hide();
        }else{
			      $(".invited").show();
			      $(".register").show();
		}
}

</script>


<div class="page-body">

  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header ">
          <h5>Vendors</h5>
          <div class="card-header-right table-card-header ">
          <div class="radio radio-inline">
					  <label class="form-label">
						<input type="radio" name="reg_invoit" value="0" checked onchange="listUserType()">
						All
					  </span>
          </div>
					<div class="radio radio-inline">
					  <label class="form-label">
						<input type="radio" name="reg_invoit" value="1" onchange="listUserType()">
						Active
					  </span>
					</div>
					<div class="radio radio-inline">
					  <label class="form-label">
						<input type="radio" name="reg_invoit" value="2" onchange="listUserType()">
						Invited
					  </span>
					</div>                           
			 
      <?php /*if(in_array(SUPERADMIN,$_SESSION['roles'])){  ?>
  				<a  href="<?php echo string_to_ascii('PAGE=CUSTOM_INSERT_APPLICATION_USER_INVITATIONS'); ?>" class="btn btn-primary mb-2 desktop-view">
  				  <i class="icofont icofont-plus icon-white text-white"></i>
  					Invite User           
  				</a>
        <?php }else{ */?>
    
<a href="index.php?option=COM_INSERT_INVITE_VENDOR" class="btn btn-success mb-2 desktop-view">
            <i class="icofont icofont-plus icon-white text-white"></i>
            Invite Vendor           
          </a>
      <?php  // } ?>
	</div>
		<!-- 		<div class="col-lg-1">
				<a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</a>
				</div> -->
			</div>
  
        <div class="row p-4">
          <?php
         /* $bg_color_array = array('bg-primary','bg-success','bg-info','bg-danger');  
          $tven = 0;
          foreach($admindashboardd as $vendorslist){ 
            $stausresult = $_SESSION['status'][$vendorslist['current_Status']];
        */    ?>  
          <?php 
           $bg_color_array = array('bg-warning','bg-info','bg-success');  
            if(count((array)$invitationlists) !="0"){   
            foreach ($invitationlists as $vendorslist){
              if($vendorslist['user_id'] != $_SESSION['userid']){
                //$send = implode(",", $send);
                //$rid = deocdeId($send);
                $applicationuser = $applicationusers[$vendorslist['user_id']];
                $vendorregisters11 = $vendorregisters[$vendorslist['user_id']];
               // print_r($applicationuser);
                $stausresult = $_SESSION['status'][$vendorslist['current_status']];
        				if(count((array)$applicationuser) != 0){ 
        					$ribbon =  "Active"; 
        					$classadded = "register";
                  $tven =2;
                  $url = "index?option=COM_INSERT_VENDORS&VTYPE=V&Rid=".deocdeId('id:'.$applicationuser['id']);
                  $displayname =$applicationuser['display_name'];
                  $first_name = $applicationuser['first_name']; 
                  $company_name = $applicationuser['company'];  
                  $image = $applicationuser['image'];
        				}elseif(count((array)$vendorregisters11) != 0){ 
                  $ribbon =  "Register"; 
                  $classadded = "register";
                  $url = "index?option=COM_INSERT_VENDORS&VTYPE=VR&Rid=".deocdeId('id:'.$vendorregisters11['id']);
                  $tven =1;
                  $displayname =$vendorregisters11['display_name'];
                  $first_name = $vendorregisters11['first_name'];
                  $company_name = $vendorregisters11['company']; 
                  $image = $applicationuser['image'];   
                }else{ 
        					$ribbon = "Invited";
        					$classadded = "invited";
                  $tven =0; 
                  $displayname =$vendorslist['display_name']; 
                  $first_name = $vendorslist['first_name'];
                  $company_name = $vendorregisters11['company_name']; 
        				}     
			?>  

      <div class="col-lg-3 custom-ribbon-accordian cursor-pointer  position-relative <?php echo $classadded; ?>" >
         
              <div class="ribbon ribbon-clip ribbon-right" style= "right: 0;">
                <div class="ribbon-target" style= "right: 3px;">
                  <span class="ribbon-inner" ></span> <?php echo $ribbon; ?> 
                </div>
              </div>
              <?php if($tven == '1' || $tven == '2'){ ?>
       <a  href="<?php echo $url; ?>">
       <?php }?> 
              <div class="card <?php echo $bg_color_array[$tven]; ?>" style="/*background-image:url('assets/images/student-courses/bg-1.png');  */ /** rohan min-height:170px">
                <div class="p-3">
                  <div class="row m-b-10 align-items-center ">
                    <div class="col-auto col-lg-10">
                      <div class="align-middle">
                        <!-- <span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-14 text-center bg-white text-mute"><?php echo initials($vendorslist['display_name']); ?></span> -->
                      
                       <?php if($applicationuser['user_photo'] != "" && $applicationuser['user_activated'] == "Y"){ ?>
    <img src="uploads/all/<?php echo $applicationuser['user_photo']; ?>"  class="img-radius img-30 m-r-15" alt="User Photo" onerror="this.src='uploads/all/no-image.jpg';">
  <?php } else { ?>
   <span class="img-radius img-50 custom_txt_round  p-2 f-14 m-r-15 text-center bg-white text-mute"><?php echo initials($displayname); ?></span>
  <?php } ?> 
                      <div class="d-inline-block width73" >
                        <h6 class="mb-1"><?php echo $company_name !=""?$company_name:$displayname; ?></h6>
                        <p class="text-white m-b-0  f-12"></p>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="row">
                <span class="col-lg-3 f-12 mb-1">Name :</span>
                <span class="col-lg-9 f-12"> <?php echo $displayname; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Since  :</span>
                <span class="col-lg-9 f-12"> <?php echo fromDate($vendorslist['created_date']); ?> </span>
                <span class="col-lg-3 f-12 mb-1">Email  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_email']; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Phone  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_mobile']!=""?$vendorslist['user_mobile']:"-"; ?> </span>
              </div>
              <?php if($tven == '0'){ ?>
              <i class="ti-share float-right position-absolute text-white" data-bs-toggle="tooltip" data-placement="bottom" title="" onclick="resendrequest('<?php echo $vendorslist['user_id']; ?>')" data-bs-original-title="Re-Invite User" style="right: 70px;top: 10px;"></i>
            <?php } ?>
            </div>
          </div>
           <?php if($tven == '1' || $tven == '2'){?>
</a>
<?php } ?>
        </div>
        <?php $tven++; 
        if($tven ==2){ $tven=0; }
          } 
        }  
    }else{   ?>

 <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Vendor Invitation Available</p>   
                                    <div class="text-center px-4">
                                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                  </div>   
                              </div>
                    
            <?php    } ?> 
      </div>
    </div>
  </div>
</div>
</div>
<?php /**/ ?>
