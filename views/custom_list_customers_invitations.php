<?php
   
$wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','user_type'=>'2');
$wher["field"] = "id,first_name,display_name,user_email,user_mobile,created_date,user_id,role_id,company_name";
$encodeurl = urlencode(json_encode($wher));
$urlcat = API."?mode=gettabledetailswithconditions&table=application_user_invitations&data=".$encodeurl;  
$invitationres = callAPI($urlcat);
$invitationlis = json_decode($invitationres,1);
$invitationlists =  $invitationlis;
//echo "<pre>"; print_r($invitationlists); echo "</pre>";
$wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wher["field"] = "id,image,display_name,first_name,user_id,company,image";
$userencodeurl = urlencode(json_encode($wher));
$userurlcat = API."?mode=gettabledetailswithconditions&table=customers&data=".$userencodeurl;  
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$applicationusers = array();
foreach($useruresponse as $useruresponse1){
  $applicationusers[$useruresponse1['user_id']] = $useruresponse1;
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
          <h5>Customers</h5>
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
          <a href="index.php?option=COM_INSERT_INVITE_CUSTOMERS" class="btn btn-success mb-2 desktop-view">
            <i class="icofont icofont-plus icon-white text-white"></i>
            Invite Customer           
          </a>
        </div>
      </div>
  
        <div class="row p-4">
          <?php 
           $bg_color_array = array('bg-warning','bg-info','bg-success');  
            if(count((array)$invitationlists) !="0"){   
            foreach ($invitationlists as $vendorslist){
              $tven = 1;
              $ribbon =  "Register"; 
                  $classadded = "register";
                  $url = "";
              if($vendorslist['user_id'] != $_SESSION['userid']){
                  $displayname =$vendorslist['display_name'];
                  $first_name = $vendorslist['first_name']; 
                  $company_name = $vendorslist['company'];  
                  $image = $vendorslist['image'];
                if($vendorslist['user_id'] && isset($applicationusers[$vendorslist['user_id']])){
                  $tven = 2;
                  $ribbon = "Invited";
                  $classadded = "invited";
                  $applicationuser = $applicationusers[$vendorslist['user_id']];
                  $vendorregisters11 = $vendorregisters[$vendorslist['user_id']];
                  $stausresult = $_SESSION['status'][$vendorslist['current_status']];
                  $url = "index?option=COM_INSERT_CUSTOMERS&VTYPE=V&Rid=".deocdeId('id:'.$applicationuser['id']);
                  $displayname =$applicationuser['display_name'];
                  $first_name = $applicationuser['first_name']; 
                  $company_name = $applicationuser['company'];  
                  $image = $applicationuser['image'];  
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
                       <?php if($applicationuser['user_photo'] != "" && $applicationuser['user_activated'] == "Y"){ ?>
                        <img src="uploads/all/<?php echo $applicationuser['user_photo']; ?>"  class="img-radius img-30 m-r-15" alt="Customer Photo" onerror="this.src='uploads/all/no-image.jpg';">
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
                <span class="col-lg-3 f-12 mb-1">Created  :</span>
                <span class="col-lg-9 f-12"> <?php echo fromDate($vendorslist['created_date']); ?> </span>
                <span class="col-lg-3 f-12 mb-1">Email  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_email']; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Phone  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_mobile']!=""?$vendorslist['user_mobile']:"-"; ?> </span>
              </div>
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
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Customers Invitation Available</p>   
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
