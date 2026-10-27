<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['wizardjs'] = 1;
    $File_Uploads_Path = "uploads/all/";
    if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
      $rsid =  base64_decode(base64_decode($_GET['Rid'])); 
      $cand = explode(':', $rsid);
     $rid = $cand['1'];
      $url = API."?mode=singlerowtabledata&rid=".$rid."&table=vendor_registers";
     // echo $url;
      $response = callAPI($url);
      $register_data = json_decode($response,1);
      //print_r($register_data);
      $rowid = $register_data['id'];
    }
	$user_id =  $register_data['user_id'];
	 if($rowid!="") {
    $data11["where"] = array('id'=> $register_data['billing_state']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=states&data=".$datw; 
    $cudata1 = callAPI($curl1);
    $states1 = json_decode($cudata1,1);
    $selectedbillstate = $states1[0]['id'];
    $selectedbillstatename = $states1[0]['state_name'];


    $data11["where"] = array('id'=> $register_data['shipping_state']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=states&data=".$datw; 
    $cudata1 = callAPI($curl1);
    $states2 = json_decode($cudata1,1);
    $selectedshipstate = $states2[0]['id'];
    $selectedshipstatename = $states2[0]['state_name'];

    $data11["where"] = array('id'=> $register_data['billing_city']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=cities&data=".$datw; 
    $cudata1 = callAPI($curl1);
    $city1 = json_decode($cudata1,1);
    $selectedbillcity = $city1[0]['id'];
    $selectedbillcityname = $city1[0]['city_name'];

    $data11["where"] = array('id'=> $register_data['shipping_city']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=cities&data=".$datw; 
    $cudata1 = callAPI($curl1);
    $city2 = json_decode($cudata1,1);
    $selectedshipcity = $city2[0]['id'];
    $selectedshipcityname = $city2[0]['city_name'];

    $attachments = json_decode($register_data['attachments'],1);
    foreach ($attachments as $avalue) {
      $attach_doccategory[$avalue['document_id']] =$avalue['categoryid'];
      $attach_filename[$avalue['document_id']] =$avalue['filename'];
    }
	
	
	   $data55["where"] = array('vendor_user_id'=> $user_id);
    $dat55 = urlencode(json_encode($data55));
    $curl2 = API."?mode=gettabledetailswithconditions&table=vendor_documents&data=".$dat55; 
    $cudata5 = callAPI($curl2);
    $states5 = json_decode($cudata5,1);
	$vendor_documents = array();
	foreach($states5 as $states5){
		$vendor_documents[$states5['category_id']] = $states5;
	}
  }
  
  /* $helptext = array('first_name'=>'Please Enter Your First Name',
                      'last_name' =>  'Please Enter Last Name',
                      'fax' =>  'Please Enter Your Fax Number',
                      'firstname'=>'Please Enter Your First Name',
                      'email'=>'Please Enter Your Email',
                      'mobile'=>'Please Enter Mobile Number',
                      'post_code'=> 'Please Enter Your Post code',
                      'country'=> 'Please Select Your Country',
                      'state' =>'Please Select Your State',
                      'city'=>'Please Select Your City',
                      'company'=>'Please Enter Your Company Name',
                      'business_type'=>'Please Select Your Business Type',
                      'gstin' =>  'Please Enter GST Number',
                      'tin_number' => 'Please Enter TIN Number',
                      'cst_reg_no' => 'Please Enter CST Number',
                      'vat_number' => 'Please Enter VAT Number',
                      'pan_no' => 'Please Enter PAN Number',
                      'contact_person'=>'Please Enter Cotact Person Name',
                      'store_pincode' => 'Please Enter Store Pincode',
                      'businesssize'=>'Please Select Business Size',
                      'businessstatus'=>'Please Select Industry',
                      'website_url'=> 'Please Enter Website Address',
                      'established_year'=>'Please Enter Establishment Year',
                      'phone'=>'Please Enter Phone Number',
                      'display_name'=>'Please Enter Display Name'
                    );
*/

   $placeholder= array('first_name'=>'Johnson','last_name'=>'Victoria','phone'=>'044 87945496','email'=>'hello@infisuite.com','mobile'=>'+91 9874563210','fax'=>'78945487877','post_code'=>'641004','company'=>'Technology','gstin'=>'04AABCU9603R1ZV','tin_number'=>'29567129023','cst_reg_no'=>'GB123456789','vat_number'=>'GB123456789','contact_person'=>'James William','store_pincode'=>'641035','established_year'=>'2000','contactperson'=>'George','contactnumber'=>'0422 5688 9500','pan_no'=>'DMUT6868O', 'display_name'=>'Johnson');

   $url = API."?mode=getvendorsettings";
   $response = callAPI($url);
   $setup_details = json_decode($response,1);
   $countrieslist = $setup_details['countries'];
   $document_lists = $setup_details['document_lists'];
   $doctypes = $setup_details['document_list_types'];
   //echo "<pre>"; print_r($doctypes['10']); exit;
   $businesstypes = array('Sole Proprietorship','Partnerships','Corporation','Limited Partnership','Limited Liability Company','Nonprofit Organization','Cooperative');
   $businesssize = array('MSME','Large');
   $businessstatus = array('Manufacturer','Authorised Dealer','Stockist/Trader','Importer/Indian Agent','Service Provider','Others');

   
   
   
   
   
   
   //echo "<pre>";print_r($_POST); echo "</pre>";
   if(isset($_POST) && !empty($_POST)){
    $listofdocs = $_POST['document_id'];
    $_POST['current_status'] ='0';
    foreach($listofdocs as $kkk=>$docvalue){
	$File_Uploads_Path = "uploads/all/";
      if($_POST['document_categoryid'.$docvalue]!='' && $_FILES["fileToUpload".$docvalue]["name"]!='') {
          $temp = explode(".", $_FILES["fileToUpload".$docvalue]["name"]);
          $Filename=  "file_".time()."_".uniqid();
          $newfilename = $Filename.'.' .end($temp);
         $documents[] = array('doc_id'=>$_POST['document_file_id'][$kkk],'document_id'=>$docvalue,'categoryid'=>$_POST['document_categoryid'.$docvalue],'filename'=>$newfilename);
          move_uploaded_file($_FILES["fileToUpload".$docvalue]["tmp_name"], $File_Uploads_Path.$newfilename);
			
      }  
      else{ 
        $documents[] = array('doc_id'=>$_POST['document_file_id'][$kkk],'document_id'=>$docvalue,'categoryid'=>$_POST['document_categoryid'.$docvalue],'filename'=>$_POST['hidden_fileToUpload'.$docvalue]);
      }
      unset($_POST['document_categoryid'.$docvalue]);
      unset($_POST['hidden_fileToUpload'.$docvalue]);
    }
	
     if(!empty($_FILES["logo_image"]["name"])){
      $temp = explode(".", $_FILES["logo_image"]["name"]);
       $Filename=  "file_".time()."_".uniqid();
       $newfilename = $Filename.'.' .end($temp);
      $_POST['image'] = $newfilename;
      move_uploaded_file($_FILES["logo_image"]["tmp_name"], $File_Uploads_Path.$newfilename);
    }
      unset($_POST['logo_image']);
      unset($_POST['logo_remove']);
    
    $_POST['attachments'] = json_encode($documents,1);
      $_POST['user_idd'] = $_POST['user_id'];
      if($user_id!=""){
		 unset($_POST['current_status']); 
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['is_active'] = '0';
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=setupvendor&rid=".$rowid."&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        }else{ 
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['is_active'] = '0';
		$_POST['current_status'] ='0';
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API."?mode=setupvendor&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
      }
      // header('Location:index.php');
      ?>
       <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            } 
        }
    </script><?php
   }

 $data14["where"] = array('is_active'=> '0','genc_code'=>'BANKACCTTYPE','company_id'=>$_SESSION['company_id']);
  $data14["field"] = "genc_detail_code,genc_detail_name";
  $datw4 = urlencode(json_encode($data14));
  $curl4 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$datw4; 
  $cudata4 = callAPI($curl4);
  $genc_code_list = json_decode($cudata4,1);

   ?>
  <script type="text/javascript">

      function sameaddress() {
        var adr = document.getElementById('is_same');
        if(adr.checked) {
         document.getElementById('shipping_address').value =document.getElementById('billing_address').value;
         document.getElementById('shipping_post_code').value =document.getElementById('billing_post_code').value;
           document.getElementById('selectedshipcountry').value = document.getElementById('selectedbillcountry').value;
            document.getElementById('selectedshipstate').value =document.getElementById('selectedbillstate').value;
            document.getElementById('selectedshipcity').value= document.getElementById('selectedbillcity').value;

            var e = document.getElementById("kt_select2_102");
            var selectcountry = e.options[e.selectedIndex].text;

            var e1 = document.getElementById("kt_select2_101");
            var selectstate = e1.options[e1.selectedIndex].text;

            var e2 = document.getElementById("kt_select2_103");
            var selectcity = e2.options[e2.selectedIndex].text;

            var select = document.getElementById('kt_select2_104'); 
            select.options[select.selectedIndex].text = selectcountry;

            var select1 = document.getElementById('kt_select2_105'); 
            select1.options[select1.selectedIndex].text = selectstate;

            var select2 = document.getElementById('kt_select2_106'); 
            select2.options[select2.selectedIndex].text = selectcity;

        } else {
             document.getElementById('shipping_address').value = "";
             document.getElementById('shipping_post_code').value = "";
              var select = document.getElementById('kt_select2_104'); 
              select.options[select.selectedIndex].text = "Select Any One";

              var select1 = document.getElementById('kt_select2_105'); 
              select1.options[select1.selectedIndex].text = "Select Any One";

              var select2 = document.getElementById('kt_select2_106'); 
              select2.options[select2.selectedIndex].text = "Select Any One";

              $.ajax({
                url: "ajax.php",
                data:'page=getCountrylist',
                type: "POST",
                success: function(response) {
                 // console.log(response);
                  response = JSON.parse(response);
                  var country = document.getElementById('kt_select2_104'); 
                  for (var i = 0; i < response.length; i++) 
                  {
                    var el = document.createElement("option");
                    el.textContent = response[i].country_name;
                    el.value = response[i].id+'|'+response[i].country_name;
                    country.add(el);
                  }
                }
              });
        }
      }

     
     function selectedcategory(selected_id,element_id,table_name,field_name,targetelement) {
                var selectedvalues = selected_id.split("|");
                //alert(selected_id);

                  if(table_name=='states'){
                    document.getElementById(targetelement).value= selectedvalues[0];
                   }
                   if(table_name=='cities'){
                    document.getElementById(targetelement).value= selectedvalues[0];
                   }
                   if(table_name=='city'){
                    document.getElementById(targetelement).value= selectedvalues[0];
                   }
                   if(table_name=='states' || table_name=='cities'){
                   $.ajax({
                      url: "ajax.php",
                      data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues[0]+'&field='+field_name,
                      type: "POST",
                      success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response.length);
                        console.log(response);
                        var select = document.getElementById(element_id); 
                        document.getElementById(element_id).innerHTML = "";
                        for (var i = 0; i < response.length; i++) 
                        {
                         var el = document.createElement("option");
                          if(table_name=='states'){
                          el.textContent = response[i].state_name;
                          el.value = response[i].id+'|'+response[i].state_name;

                          }
                          if(table_name=='cities'){
                          el.textContent = response[i].city_name;
                          el.value = response[i].id+'|'+response[i].city_name;

                          }
                          select.add(el);
                         }
                    }
                });
                 }

              }


   function displaystausdrop(id,table,status_id){
   //alert("id");
   $.ajax({
   url: "ajax.php",
   data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
   type: "POST",
   success:function(data){
     console.log(data);
     $("#dropdownlist").html(data);
   },
   error:function (){}
   });
   
   }

   function statuspopup(status,id){
   //alert("data");
   var table_name = 'vendor_registers';
   jQuery.ajax({
   url: "views/custom_insert_status_popup.php",
   data:'table='+table_name+'&status='+status+'&rid='+id,
   type: "POST",
   success:function(data){
   $('#popup1').html(data);
  // $('#kt_modal_KTDatatable_remote').modal({ show: true});
	$("#kt_modal_KTDatatable_remote").modal("show");
   },
   error:function (){}
   });
   }
   
   
     function statsonlychange(id,status,vendor_register_id){
		 
		var user_id = $("#user_id").val();
		$.ajax({
		   url: "ajax.php",
		   data:'page=updatestatusvendordocuments&hid='+id+'&table=vendor_documents&status='+status+'&vendor_register_id='+vendor_register_id+'&user_id='+user_id,
		   type: "POST",
		   success:function(data){
			   
			   console.log(data);
			 if(status=="199"){
				 $(".status_rej_"+id).removeClass("active");
				$(".status_app_"+id).addClass("active");
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Successfully updated status','');
			$(".stausbtn_"+id).addClass('bg-success');
				
			 }else{
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger','animated flipInX','animated flipOutX','Successfully updated status','');
				$(".stausbtn_"+id).addClass('bg-danger');
				$(".status_app_"+id).removeClass("active");
				$(".status_rej_"+id).addClass("active");
			 }
			 $(".ribbanid_"+id).hide();
			 
			 }
		 });
  	}
   
   
function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                         window.location.reload();
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        }); 
}
           </script>
   <!--begin::Body-->
   <style>
   
    .content.clearfix {
      background-color: white;
      overflow-y: scroll;
      height: 500px;
      width: 75% !important; 
    }
    .steps.clearfix {
      width: 20% !important;
    }
   
/**   
  .content.clearfix {
    background-color: white;
overflow: scroll;
}
**/
.wizard > .content > .body input[type="checkbox"] {
     display: none !important;
}


</style>
<style>
.alert.alert-custom.alert-white .alert-text {
    color: #3f4254;
}
.alert.alert-custom .alert-text {
    -ms-flex-item-align: center;
    align-self: center;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
}
.symbol {
    display: inline-block;
    -ms-flex-negative: 0;
    flex-shrink: 0;
    position: relative;
    border-radius: 0.42rem;
}
.nav-link {
    display: block;
    padding: 0.5rem 1rem;
}
.image-input {
    position: relative;
    display: inline-block;
    border-radius: 0.42rem;
    background-repeat: no-repeat;
    background-size: cover;
}
label.btn-hover-text-primary{
    cursor: pointer;
    position: absolute;
    right: -10px;
    top: -10px;
}
.image-input input {
    width: 0!important;
    height: 0!important;
    overflow: hidden;
    opacity: 0;
}

</style> 

  <style>

.pricing .title {
    background: #1ABB9C;
    color: #fff;
    padding: 15px 0px 2px 0px;
   /** text-align: center; **/
}
.pricing_features {
    background: #FAFAFA;
    padding: 10px 7px;
    min-height: 100px;
    font-size: 13.5px;
}
.list-unstyled {
    padding-left: 0;
    list-style: none;
}
.pricing_features ul li {
    margin-top: 10px;
}
.ui-ribbon-container .ui-ribbon-wrapper {
    position: absolute;
    overflow: hidden;
    width: 85px;
    height: 88px;
    top: -3px;
    right: -3px;
}
.ui-ribbon-container .ui-ribbon {
    position: relative;
    display: block;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    transform: rotate(
45deg);
    padding: 7px 0;
    left: -5px;
    top: 15px;
    width: 120px;
    line-height: 20px;
    background-color: #555;
    box-shadow: 0 0 3px rgb(0 0 0 / 30%);
}
.pricing .title span {
    background: rgba(51,51,51,0.28);
    padding: 2px 5px;
}

.custom_select_docs .select2-selection__rendered{
	padding :4px 10px !important;
	font-size :12px; 
}
.custom_select_docs .select2-selection__arrow{
	top: 6px !important;
    right: 10px !important;
}


  </style>

 <?php 
            if($register_data['current_status'] !=""){
        $workflowsteps = getWorkflowStatus('vendor_registers',$register_data['id'],$register_data['current_status']);
        }
            ?>
   <div class="card">
    <?php  echo $workflowsteps['current_status_ribbon']; ?>
            <div class="card-header">
                <h5>Vendor Registers</h5>
				  <div class="card-header-right table-card-header">
             <?php   echo $workflowsteps['workflow_btn']; ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i> Back</a>
					<?php 
				/*	$table_name = 'vendor_registers';
					if($rowid!="") { $nmm= ViewStatusValue($register_data['current_status'],$table_name);  ?>
					   <div class="btn-group">
						<button onclick="displaystausdrop(<?php echo $rowid; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $register_data['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?> </button>
						<div class="dropdown-menu" id="dropdownlist">
						</div>
					  </div>
					<?php } */?>
				  </div>
            </div>
            <div class="card-block">
                <div class="row">
                    <?php echo $workflowsteps['breadcrumb'];  ?>
                    <?php  //$disable = 0;
   // $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,ST_APPROVED,'');
   //if(in_array($register_data['current_status'], $check_array)){ $disablecss = "style='pointer-events: none; opacity: 0.7;'";  $disable = 1; }

   $disablecss = "style='pointer-events: none; opacity: 0.7;'"; 

    ?>
                    <div class="col-md-12" >
                        <div id="wizardb">
                            <section>
                                <form class="wizard-form" id="example-advanced-form-vendorsetup" action="#" method="post" action="" enctype="multipart/form-data">
                                   

								   <h3> Profile Information </br><small>Please enter profile details</small></h3>
                                    <fieldset>
                                     <!--begin: Wizard Step 1-->
                                             <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                              
                                      <div class="card-header mb-3"> <h5>Vendor Registration</h5></div>
									  
				
                                                <!--end::Input-->
                                                <div class="row" <?php echo $disablecss; ?>>
                                                  
                                                    <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <!--  <input type="hidden" name="vendor_hrconnect" value="<?php //echo ST_VENDOR_HRCONNECT ==""?$register_data['vendor_hrconnect']:ST_VENDOR_HRCONNECT; ?>"> -->
        <input type="hidden" name="current_status" value="<?php echo $register_data['current_status']!=""?$register_data['current_status']:'0'; ?>">
   <input type="hidden" name="hr_connect" id="hr_connect" value="<?php echo $register_data['hr_connect']; ?>">
    <input type="hidden" name="finance_connect" id="finance_connect" value="<?php echo $register_data['finance_connect']; ?>">
    <input type="hidden" name="purchase_connect" id="purchase_connect" value="<?php echo $register_data['purchase_connect']; ?>">
    <input type="hidden" name="admin_connect" id="admin_connect" value="<?php echo $register_data['admin_connect']; ?>">

															<label  class="form-label">First Name <span class="requried">*</span></label>
                                                         <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>">
                                                          <div class="input-group input-icon-right">

                                                         <input type="text" class="form-control required" name="first_name" placeholder="<?php echo $placeholder['first_name']; ?>" value="<?php echo $register_data['first_name'];?>" />
                                                       <!--   <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['first_name']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>

                                                    

                                                    <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label  class="form-label">Last Name</label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="text" class="form-control" name="last_name" value="<?php echo $register_data['last_name'];?>" placeholder="<?php echo $placeholder['last_name']; ?>"  />
                                                       <!--   <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['last_name']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>


                                                    <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label  class="form-label">Display Name<span class="requried">*</span></label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="text" class="form-control required" name="display_name" value="<?php echo $register_data['display_name'];?>" placeholder="<?php echo $placeholder['display_name']; ?>"  />
                                                        <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['display_name']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>

                                                    <div class="form-group col-lg-6">
                                                     <label class="ml-5">Image</label>
                                                     <div class="col-lg-9 col-xl-6">
 <?php $logo = $register_data['image']==''?  $File_Uploads_Path.'no-image.jpg':$File_Uploads_Path.$register_data['image']; ?>

  <div class="profile-pic">
    <label class="-label" for="logo_image">
      
      <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
    </label>
    <input  type="file" name="logo_image" id="logo_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
      <input type="hidden" name="logo_remove" >

    <img src="<?php echo $logo;?>" id="outputlogo_image" width="200"  />
  </div>

                                                    <!--   <div class="image-input image-input-outline" id="kt_image_1">
                                                        <?php $logo = $register_data['image']==''?  $File_Uploads_Path.'no-image.jpg':$File_Uploads_Path.$register_data['image']; ?>
                                                            <div class="image-input-wrapper" style="background-image: url(<?php echo $logo;?>)"></div>
                                                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Image">
                                                                <i class="fa fa-pen icon-sm text-muted"></i>
                                                                  <input type="file" name="logo_image" accept=".png, .jpg, .jpeg"/>
                                                                   <input type="hidden" name="logo_remove"/>
                                                            </label>
                                                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel Image">
                                                                <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                            </span>
                                                        </div>  -->   
                                                      </div>  
                                                       <span class="form-text text-muted"><?php echo $helptext['logo'];?> </span>
                                                </div> 
                                                  <div class="form-group col-lg-12">
                                                   <label class="form-label">Email <span class="requried">*</span></label>
                                                   
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control required" name="email" readonly value="<?php echo $register_data['email'];?>" placeholder="<?php //echo $placeholder['email']; ?>"  />
                                                     <!--  <div class="input-group-append"><span class="input-group-text"><span>example.com</span></span></div> --> </div>
                                                   <span class="form-text text-muted"><?php echo $helptext['email']; ?></span>
                                                </div>
                                                  

                                                    <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label class="form-label">Contact Person</label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="text" class="form-control" name="contact_person" value="<?php echo $register_data['contact_person'];?>" placeholder="<?php echo $placeholder['contact_person']; ?>"  />
                                                      <!--    <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['contact_person']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>
                                                   
                                                   <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label class="form-label">Mobile Number <span class="requried">*</span></label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="number" class="form-control required" name="mobile" value="<?php echo $register_data['mobile'];?>" placeholder="<?php echo $placeholder['mobile']; ?>"  />
                                                        <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['mobile']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>
                                                   


                                                   <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label class="form-label">Phone Number</label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="number" class="form-control" name="phone" value="<?php echo $register_data['phone'];?>" placeholder="<?php echo $placeholder['phone']; ?>"  />
                                                  <!--        <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['phone']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>

                                                   <div class="col-xl-6">
                                                      <!--begin::Input-->
                                                      <div class="form-group">
                                                         <label class="form-label">Fax</label>
                                                          <div class="input-group input-icon-right">

                                                         <input type="number" class="form-control" value="<?php echo $register_data['fax'];?>" name="fax" placeholder="<?php echo $placeholder['fax']; ?>"  />
                                                        <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                         <span class="form-text text-muted"><?php echo $helptext['fax']; ?></span>
                                                      </div>

                                                      <!--end::Input-->
                                                   </div>
                                                   

                                                   
                                                </div>
                                             </div>
                                             <!--begin: Wizard Step 3-->
                                    </fieldset>
                                   <h3> Business Information </br><small>Vendor business details</small></h3>
                                    <fieldset>
                                                                                <!--begin: Wizard Step 3-->
                                             <div class="pb-5" data-wizard-type="step-content">
                                               
                                                  <div class="card-header mb-3"> <h5>Enter Your Business Details</h5></div>
                                    
                                                <!--begin::Select-->
                                                <div class="row" <?php echo $disablecss; ?>>
                                              <div class="form-group col-lg-12">
                                                   <label class="form-label">Company Name <span class="requried">*</span></label>
                                                   
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control required" name="company" value="<?php echo $register_data['company'];?>" placeholder="<?php echo $placeholder['company']; ?>"  />
                                                   <!--    <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                   <span class="form-text text-muted"><?php echo $helptext['company']; ?></span>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                   <label class="form-label">Website</label>
                                                   
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control" value="<?php echo $register_data['website_url'];?>" name="website_url" placeholder="<?php echo $placeholder['website_url']; ?>"  />
                                                    <!--   <div class="input-group-append"><span class="input-group-text"><span>.com</span></span></div> --> </div>
                                                   <span class="form-text text-muted"><?php echo $helptext['website_url']; ?></span>
                                                </div>
                                                 <div class="form-group col-lg-6">
                                                   <label class="form-label">Year of Establishment</label>
                                                      <input type="text" class="form-control" name="established_year" value="<?php echo $register_data['established_year'];?>" placeholder="<?php echo $placeholder['established_year']; ?>"  />
                                                     <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> 
													<span class="form-text text-muted"><?php echo $helptext['established_year']; ?></span>
                                                </div>


                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">Business Type</label>
                                                    <select name="business_type" class="form-control form-select js-example-basic-single">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($businesstypes as $buztype) { ?>
                                                      <option value="<?php echo $buztype?>" <?php echo $register_data['business_type']==$buztype?'selected':'';?> ><?php echo $buztype; ?></option>
                                                      <?php } ?>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['business_type']; ?></span>
                                                   
                                                </div>

                                                 <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">Size</label>
                                                    <select name="business_size" class="form-control form-select js-example-basic-single">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($businesssize as $buztype) { ?>
                                                      <option value="<?php echo $buztype?>" <?php echo $register_data['business_size']==$buztype?'selected':'';?>><?php echo $buztype; ?></option>
                                                      <?php } ?>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['businesssize']; ?></span>
                                                   
                                                </div>

                                                 <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">Industry</label>
                                                    <select name="business_status" class="form-control form-select js-example-basic-single">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($businessstatus as $buztype) { ?>
                                                      <option value="<?php echo $buztype?>" <?php echo $register_data['business_status']==$buztype ? 'selected':'';?> ><?php echo $buztype; ?></option>
                                                      <?php } ?>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['businessstatus']; ?></span>
                                                   
                                                </div>
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label">Business Description</label>
                                                    <textarea type="text" class="form-control" name="vendor_po_notes" ><?php echo $register_data['vendor_po_notes'];?></textarea>
                                                </div>
                                            

                                             </div>

                                             </div>
                                             <!--end: Wizard Step 3-->

                                    </fieldset>
                                     <h3> Communication </br><small>Vendor contact details</small></h3>
                                    <fieldset>
                                   
                                             <!--begin: Wizard Step 3-->
                                             <div class="pb-5" data-wizard-type="step-content">
                                                
                                                           
                                      <div class="card-header mb-3"> <h5>Vendor contact details</h5></div>
                                                <!--begin::Select-->
                                                <div class="row" <?php echo $disablecss; ?>>
                                              <div class="form-group col-lg-12">
                                                    <label class="form-label">Billing Address</label>
                                                    <textarea type="text" class="form-control" id="billing_address" name="billing_address" ><?php echo $register_data['billing_address'];?></textarea>
                                                </div>
                                                 <div class="form-group col-lg-6">
                                                   <label class="form-label">Post Code</label>
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control"id='billing_post_code' value="<?php echo $register_data['billing_post_code'];?>" name="billing_post_code" placeholder="<?php echo $placeholder['post_code']; ?>"  />
                                                    <!--   <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                   <span class="form-text text-muted"><?php echo $helptext['post_code']; ?></span>
                                                </div>

                                                <div class="form-group col-lg-6 w-full">
                                                   <label class="form-label">Country</label>
                                                    <input type="hidden" name="billing_country" id="selectedbillcountry" value="<?php echo $register_data['billing_country']?>">
                                                    <input type="hidden" name="billing_state" id="selectedbillstate" value="<?php echo $selectedbillstate?>">
                                                    <input type="hidden" name="billing_city" id="selectedbillcity" value="<?php echo $selectedbillcity;?>">
                                                   
                                                   <select id="kt_select2_102" class="form-control form-select js-example-basic-single"  onchange="selectedcategory(this.value,'kt_select2_101','states','country_id','selectedbillcountry')">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($countrieslist as $countryvalue) { ?>
                                                      <option <?php echo $register_data['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'].'|'.$countryvalue['country_name'];?>"><?php echo $countryvalue['country_name']; ?></option>
                                                      <?php } ?>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['country']; ?></span>
                                                   
                                                </div>


                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">State </label>
                                                    <select id="kt_select2_101" class="form-control form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_103','cities','state_id','selectedbillstate')">
                                                      <?php
                                                            if($selectedbillstatename!='') { ?>
                                                              <option><?php echo $selectedbillstatename; ?></option>
                                                            <?php } ?>
                                                        <option value="">Select Any One</option>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['state']; ?></span>
                                                   
                                                </div>

                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">City</label>
                                                    <select id="kt_select2_103" class="form-control form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
                                                      <?php
                                                            if($selectedbillcityname!='') { ?>
                                                              <option><?php echo $selectedbillcityname; ?></option>
                                                            <?php } ?>
                                                        <option value="">Select Any One</option>
                                                      </select>
                                                      <span class="form-text text-muted"><?php echo $helptext['city']; ?></span>
                                                   
                                                </div>
                                                <div class="form-group col-lg-12 mt-10"> 
                                                          <div class="checkbox-fade fade-in-primary">
                                            <label class="form-label">
                                                            <input type="checkbox" <?php if($register_data['shipping_address']==$register_data['billing_address'] && $rowid!="") { echo 'checked'; } else { echo ''; } ?> id="is_same" onclick="sameaddress()">   <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span> &nbsp;  Billing Address same as Shipping Address
                                                          </label>
                                                    </div> </div>

                                                 <div class="form-group col-lg-12">
                                                    <label class="form-label">Shipping Address</label>
                                                    <textarea type="text" class="form-control" name="shipping_address" id="shipping_address"><?php echo $register_data['shipping_address'];?></textarea>
                                                </div>

                                                 <div class="form-group col-lg-6">
                                                   <label class="form-label">Post Code</label>
                                                     <div class="input-group input-icon-right">
                                                      <input type="text" class="form-control" name="shipping_post_code" id="shipping_post_code" value="<?php echo $register_data['shipping_post_code'];?>" placeholder="<?php echo $placeholder['post_code']; ?>"  />
                                                     <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div> --> </div>
                                                   <span class="form-text text-muted"><?php echo $helptext['post_code']; ?></span>
                                                </div>

                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">Country</label>
                                                    <input type="hidden" name="shipping_country" id="selectedshipcountry" value="<?php echo $register_data['shipping_country']?>"> 
                                                    <input type="hidden" name="shipping_state" id="selectedshipstate" value="<?php echo $selectedshipstate?>">
                                                    <input type="hidden" name="shipping_city" id="selectedshipcity" value="<?php echo $selectedshipcity?>">
                                                   
                                                   <select id="kt_select2_104" class="form-control form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_105','states','country_id','selectedshipcountry')">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($countrieslist as $countryvalue) { ?>
                                                      <option <?php echo $register_data['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'].'|'.$countryvalue['country_name'];?>"><?php echo $countryvalue['country_name']; ?></option>
                                                      <?php } ?>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['country']; ?></span>
                                                   
                                                </div>


                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">State</label>
                                                    <select id="kt_select2_105" class="form-control form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_106','cities','state_id','selectedshipstate')">
                                                           <?php
                                                            if($selectedshipstatename!='') { ?>
                                                              <option><?php echo $selectedshipstatename; ?></option>
                                                            <?php } ?>
                                                        <option value="">Select Any One</option>
                                                      </select>
                                                   <span class="form-text text-muted"><?php echo $helptext['state']; ?></span>
                                                   
                                                </div>

                                                <div class="form-group col-xl-6 w-full">
                                                   <label class="form-label">City</label>
                                                    <select id="kt_select2_106" class="form-control form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
                                                      <?php
                                                            if($selectedshipcityname!='') { ?>
                                                              <option><?php echo $selectedshipcityname; ?></option>
                                                            <?php } ?>
                                                        <option value="">Select Any One</option>
                                                      </select>
                                                      <span class="form-text text-muted"><?php echo $helptext['city']; ?></span>
                                                   
                                                </div>



                                             </div>

                                    </fieldset>
                                     <h3> Tax Information </br><small>Vendor tax details</small></h3>
                                    <fieldset>
                                     <!--end: Wizard Step 3-->
                                             <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                               
                                  
                                      <div class="card-header mb-3"> <h5>Vendor Tax Information</h5></div>
                                                <!--end::Input-->
                                                <div class="row" <?php echo $disablecss; ?>>

                                                <div class="form-group col-lg-6">
                                               <label class="form-label">PAN Number<span class="requried">*</span></label>
                                               <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control" value="<?php echo $register_data['pan_no'];?>" name="pan_no" placeholder="<?php echo $placeholder['pan_no'];?>">
                                                   <!--  <div class="input-group-append"><span class="input-group-text"><span><i class="la la-id-card-o"></i></span></span></div> -->
                                                 </div>
                                                 <span class="form-text text-muted"><?php echo $helptext['pan_no'];?> </span>

                                             </div>
                                                   
                                                <div class="form-group col-lg-6">
                                               <label class="form-label">VAT Number<span class="requried">*</span></label>
                                               <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control"  name="vat_number" value="<?php echo $register_data['vat_number'];?>" placeholder="<?php echo $placeholder['vat_number'];?>">
                                                  <!--   <div class="input-group-append"><span class="input-group-text"><span><i class="la la-id-card-o"></i></span></span></div> -->
                                                 </div>
                                                 <span class="form-text text-muted"><?php echo $helptext['vat_number'];?> </span>

                                             </div>
                                             <div class="form-group col-lg-6">
                                               <label class="form-label">TIN Number</label>
                                               <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control" value="<?php echo $register_data['tin_number'];?>" name="tin_number" placeholder="<?php echo $placeholder['tin_number'];?>">
                                              <!--       <div class="input-group-append"><span class="input-group-text"><span><i class="la la-id-card-o"></i></span></span></div> -->
                                                 </div>
                                                 <span class="form-text text-muted"><?php echo $helptext['tin_number'];?> </span>

                                             </div>
                                             <div class="form-group col-lg-6">
                                               <label class="form-label">CST Number<span class="requried">*</span></label>
                                               <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control"  value="<?php echo $register_data['cst_reg_no'];?>" name="cst_reg_no" placeholder="<?php echo $placeholder['cst_reg_no'];?>">
                                               <!--      <div class="input-group-append"><span class="input-group-text"><span><i class="la la-id-card-o"></i></span></span></div> -->
                                                 </div>
                                                 <span class="form-text text-muted"><?php echo $helptext['cst_reg_no'];?> </span>

                                             </div>
                                             <div class="form-group col-lg-6">
                                               <label class="form-label">GST Number</label>
                                               <div class="input-group input-icon-right">
                                                    <input type="text" class="form-control" value="<?php echo $register_data['gstin'];?>"  name="gstin" placeholder="<?php echo $placeholder['gstin'];?>">
                                                 <!--    <div class="input-group-append"><span class="input-group-text"><span><i class="la la-id-card-o"></i></span></span></div> -->
                                                 </div>
                                                 <span class="form-text text-muted"><?php echo $helptext['gstin'];?> </span>

                                             </div>


                                                 


                                                </div>
                                             </div>
                                            
                                      

                                           
                                    </fieldset>
									<h3> Bank Details </br><small>Vendor bank details</small></h3>
                                    <fieldset>
                                      <!-- end wizard 3 -->
                                      <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                        <h4 class="mb-10 font-weight-bold text-dark">Vendor Bank Details</h4>
                                        <!--end::Input-->
                                        <div class="row" <?php echo $disablecss; ?>>

                                          <div class="form-group col-lg-6">
                                           <label>Bank Account Name<span class="requried">*</span></label>
                                           <div class="input-group input-icon-right">
                                            <input type="text" class="form-control required" value="<?php echo $register_data['bank_account_name'];?>" name="bank_account_name" placeholder="<?php echo $placeholder['bank_ac_name'];?>">
                                            
                                          </div>
                                          <!--    <span class="form-text text-muted"><?php echo $helptext['bank_ac_name'];?> </span> -->

                                        </div>
                                        
                                        <div class="form-group col-lg-6">
                                         <label>Bank Name<span class="requried">*</span></label>
                                         <div class="input-group input-icon-right">
                                          <input type="text" class="form-control required"  name="bank_name" value="<?php echo $register_data['bank_name'];?>" placeholder="<?php echo $placeholder['bank_name'];?>">
                                          
                                        </div>
                                        <!--    <span class="form-text text-muted"><?php echo $helptext['bank_name'];?> </span> -->

                                      </div>
                                 <?php /** ?>     <div class="form-group col-lg-6">
                                       <label>Account Type</label>
                                       <div class="input-group input-icon-right">
                                        <input type="text" class="form-control" value="<?php echo $register_data['account_type'];?>" name="account_type" placeholder="<?php echo $placeholder['account_type'];?>">
                                        
                                      </div>
                                      <!--   <span class="form-text text-muted"><?php echo $helptext['account_type'];?> </span> -->

                                    </div>
									<?php **/ ?>
									
									  <div class="form-group col-xl-6 w-full">
                             <label>Account Type</label></br>
                             <select id="kt" class="form-select js-example-basic-single" name="account_type">
							    <option value="">Select Any One</option>
                           
                               <?php
                               foreach($genc_code_list as $genc_code_lists) { ?>
                                <option value="<?php echo $genc_code_lists['genc_detail_code']; ?>" <?php echo $register_data['account_type']==$genc_code_lists['genc_detail_code']?"selected":""; ?> ><?php echo $genc_code_lists['genc_detail_name']; ?></option>
                              <?php } ?>
                            </select>  
                          </div>

									
									
                                    <div class="form-group col-lg-6">
                                     <label>Bank Account Number<span class="requried">*</span></label>
                                     <div class="input-group input-icon-right">
                                      <input type="text" class="form-control required"  value="<?php echo $register_data['bank_account_no'];?>" name="bank_account_no" placeholder="<?php echo $placeholder['bank_account_no'];?>">
                                      
                                    </div>
                                    <!--     <span class="form-text text-muted"><?php echo $helptext['bank_account_no'];?> </span> -->

                                  </div>
                                  <div class="form-group col-lg-6">
                                   <label>IFSC Code</label>
                                   <div class="input-group input-icon-right">
                                    <input type="text" class="form-control" value="<?php echo $register_data['ifsc_code'];?>"  name="ifsc_code" placeholder="<?php echo $placeholder['ifsc_code'];?>">
                                    
                                  </div>
                                  <!--   <span class="form-text text-muted"><?php echo $helptext['ifsc_code'];?> </span> -->

                                </div>
                                <div class="form-group col-lg-6">
                                 <label>Bank Address</label>
                                 <div class="input-group input-icon-right">
                                   <textarea type="text" class="form-control" id="bank_address" name="bank_address" ><?php echo $register_data['bank_address'];?></textarea>
                                   
                                 </div>
                                 <!-- <span class="form-text text-muted"><?php echo $helptext['bank_address'];?> </span> -->

                               </div>
                             </div>
                           </div>
                           
                           
                         </fieldset>
                                     <h3> Upload Documents </br><small>Please upload necessary documents</small></h3>
                                    <fieldset>
                                    <div class="pb-5" data-wizard-type="step-content">
                                              
                                          <div class="card-header mb-3"> <h5>List of Documents to be Submitted</h5></div>
                                              <div class="row">
												
					 <?php
					foreach ($document_lists as $dockey => $docvalue){ 
						$vendor_documen = $vendor_documents[$dockey];
						$filestatus = $_SESSION['status'][$vendor_documen['current_status']];
					?>							
<div class="col-md-6 col-sm-6 col-xs-12 mb-10">
<input type="hidden" name="document_id[]" value="<?php echo $dockey;?>" >
<input type="hidden" name="document_file_id[]" value="<?php echo $vendor_documen['id'];?>" >
  <div class="pricing custom-ribbon-accordian">
			<?php /**if($vendor_documen['current_status'] ==ST_APPROVAL_PENDING){ ?>
			<div class="ribbon ribbon-clip ribbon-right ribbanid_<?php echo $vendor_documen['id']; ?>">
            <div class="ribbon-target f-10 bg-warning" >
              <span class="ribbon-inner" ></span> <?php echo "Approval pending"; ?> 
            </div>
          </div> 
			<?php } **/?>
 <div class="title " style="border-radius: 5px;">
      <a  class="text-white" <?php if($vendor_documen['filename']!=""){ ?>  href="<?php  echo $File_Uploads_Path.$vendor_documen['filename'];   ?>"  target="_blank" <?php } ?> > 
        <h6 class="f-14 m-l-10"> <?php if($vendor_documen['filename']!=""){ echo '<i class="ti-cloud-down"></i>'; } ?> <?php echo ucwords($docvalue['document_name']); ?>
         <?php if($vendor_documen['filename']!="" ){ ?><label class="form-label label label-info"    style="color: <?php echo "#". $filestatus['color']; ?>;    background:  <?php echo "#".strval($filestatus['background_color'] ); ?>; border-color: <?php echo "#".strval($filestatus['background_color']); ?>;"><?php echo $filestatus['name']; ?></label><?php } ?>
       </h6> 
     </a>
   </div>
    <div class="x_content">
      <div class="">
        <div class="pricing_features">    
          <div style="min-height: 38px; box-shadow: 0px 1px 9px #cccccc; padding: 2px 10px 2px 10px;" class="alert alert-custom alert-white fade show" role="alert">
                <div class="alert-text">

	   <div class="d-flex align-items-center mb-0">
		  <div class="symbol symbol-35 symbol-success mr-3 mt-1">
						<?php if($vendor_documen['filename']!=""){ if($vendor_documen['current_status'] == ST_REJECTED){ ?> 
							<i class="fa fa-times text-danger"></i> 
						<?php }elseif($vendor_documen['current_status'] == ST_APPROVED){ ?>  
							<i class="fa fa-thumbs-up"></i>
						<?php }else{  ?>
						   <i class="fa fa-hourglass-half"></i>
						<?php } }else{ ?>
          <i class="fa fa-upload"></i>
        <?php }     ?>
		  </div>
		  <div class="d-flex flex-column font-weight-bold w-100 p-l-20 p-r-20 p-t-5 p-b-5 custom_select_docs w-full">
		  <select name="document_categoryid<?php echo $dockey;?>" class="form-control js-example-basic-single" disabled>
			<?php if(count((array)$doctypes[$dockey]) == 0){ ?>
				<option value="0"><?php echo ucwords($docvalue['document_name']); ?></option>
			<?php }else{ ?>
			<option value="">Select Any One</option>
			<?php foreach($doctypes[$dockey] as $docltype) { ?>
				<option value="<?php echo $docltype['id']?>" <?php echo $vendor_documen['document_id']==$docltype['id'] ? 'selected':'' ?>><?php echo $docltype['document_type']; ?></option>
			<?php } } ?>
		  </select>
		  </div>
			<div class="d-flex font-weight-bold">   
		
   <?php if($vendor_documen['current_status'] == ST_REJECTED){  
		$statu =  "Rejected"; 
		$cls = "bg-danger";
	}elseif($vendor_documen['current_status'] == ST_APPROVED){ 
		$statu = "Approved"; 
		$cls = "bg-success";
	}else{
		$cls = "";
		$statu = "Active"; 
	} ?>
<?php if($vendor_documen['filename']!=""){ ?>
				<div class="dropdown-primary dropdown open">
					<button class="label label-sm <?php echo $cls; ?> stausbtn_<?php echo $vendor_documen['id']; ?>" style=" padding: 5px 5px; border-radius: 30px;" title="<?php echo $statu; ?>"  data-bs-toggle="dropdown"  aria-haspopup="true"  aria-expanded="true" type="button" data-content="<?php echo $statu; ?>"> 
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
						<div class="dropdown-item  status_app_<?php echo $vendor_documen['id']; ?> <?php echo $vendor_documen['current_status'] == ST_APPROVED?"active":""; ?>" onclick="statsonlychange(<?php echo $vendor_documen['id']; ?>,<?php echo ST_APPROVED; ?>,'<?php echo $rowid; ?>')"  name="status" id="status" value="<?php echo ST_APPROVED; ?>)">Approved</div>
						<div class="dropdown-item status_rej_<?php echo $vendor_documen['id']; ?> <?php echo $vendor_documen['current_status'] == ST_REJECTED?"active":""; ?>" onclick="statsonlychange(<?php echo $vendor_documen['id']; ?>,<?php echo ST_REJECTED; ?>,'<?php echo $rowid; ?>')"  name="status" id="status" value="<?php echo ST_REJECTED; ?>)">Rejected</div>
					</div>
				</div>
<?php } ?>
			</div>
</div>
                </div>
            </div>




      </div>
          </div>
         
        </div>
      </div>
    </div>
												
												
												
						<?php } ?>										
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												<?php /** ?>
												<table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center ">
                                                <tr>
                                                    <th  class="thheader" width="2%">#</th>
                                                    <th  class="thheader" width="30%">Document</th>
                                                    <th  class="thheader" width="25%">Category</th>
                                                    <th  class="thheader" width="36%">Attach Files</th>
                                                    <?php if($rowid!='') { ?>
                                                    <th  class="thheader" width="7%">View</th>
                                                    <?php } ?>

                                                </tr>
                                                <?php
                                                $sno=0;
                                                foreach ($document_lists as $dockey => $docvalue) { $sno++;?>
                                                <tr>
                                                    <td> <?php echo $sno; ?> </td>
                                                    <td align="left"> <input type="hidden" name="document_id[]" value="<?php echo $dockey;?>" ><?php echo $docvalue['document_name']; ?> </td>
                                                    <td ><select name="document_categoryid<?php echo $dockey;?>" class="form-control form-select js-example-basic-single">
                                                        <option value="">Select Any One</option>
                                                        <?php foreach($doctypes[$dockey] as $docltype) { ?>
                                                      <option value="<?php echo $docltype['id']?>" <?php echo $attach_doccategory[$dockey]==$docltype['id'] ? 'selected':'' ?>><?php echo $docltype['document_type']; ?></option>
                                                      <?php } ?>
                                                      </select></td>
                                                    <td> 
                                                     <!--  <div class="uppy" id="kt_uppy_5">
                                                      <div class="uppy-wrapper"></div>
                                                      <div class="uppy-list"></div>
                                                      <div class="uppy-status"></div>
                                                      <div class="uppy-informer uppy-informer-min"></div>
                                                    </div> -->
                                                   <div class="custom-file">
                                                    <input type="hidden" name="hidden_fileToUpload<?php echo $dockey;?>" value="<?php echo $attach_filename[$dockey]; ?>">
                                                     <input type="file" class="custom-file-input" id="customFile" name="fileToUpload<?php echo $dockey;?>" />
                                                     <label class="custom-file-label" for="customFile"></label>
                                                    </div>

                                                  </td>
                                                    <?php if($rowid!='') { ?>
                                                   <td> <?php if($attach_filename[$dockey]!='') { ?> <a href="<?php echo $File_Uploads_Path.$attach_filename[$dockey];?>" target="_blank"> <i class="ti-cloud-down text-primary f-18"></i>   </a>
                                                   <?php } ?> </td>
                                                 <?php } ?>

                                                </tr>
                                              <?php } ?>
                                            </table>
<?php **/ ?>
                                             </div>
                                           </div>
                                    </fieldset>

                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">
            
        </div>
    </div>
</div>
      <script src="assets/js/pages/myscrpit/wizard2_vendorsetup.js"></script>
      <script src="assets/js/pages/crud/file-upload/image-input.js"></script>

      <!--end::Page Scripts-->
      <script src="assets/js/pages/myscrpit/selecter.js"></script>
      <script src="assets/js/pages/crud/forms/widgets/bootstrap-timepicker.js"></script>
