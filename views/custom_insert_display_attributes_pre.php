<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit'])){

	$data= array();
	$serval = array();
	function servalnumber($serval, $val){
		if(in_array($val, (array)$serval)){
			$display_postion = servalnumber($serval, $val+2);
		}else{
			$display_postion = $val;
		}
		return $display_postion;
	}
	foreach ($_POST['db'] as $postkey => $postvalue) {
		$display_postion = servalnumber($serval, $_POST['display_postion'][$postkey]);
		$serval[] = $display_postion;
		$data1[$postvalue]['display_postion'] = $display_postion;
		$data1[$postvalue]['display_list'] = $_POST['display_list'][$postkey]==1 ? 1: 2 ;
		$data1[$postvalue]['lable'] = $_POST['lable'][$postkey];
		$data1[$postvalue]['mandatory'] = $_POST['mandatory'][$postkey]==1 ? 1: 2 ;
		$data1[$postvalue]['type'] = $_POST['type'][$postkey];
		$data1[$postvalue]['insert_display_list'] = $_POST['insert_display_list'][$postkey]==1 ? 1: 2 ;
		$data1[$postvalue]['insert_view_width'] = $_POST['insert_view_width'][$postkey];
		$data1[$postvalue]['insert_view_wizard'] = $_POST['insert_view_wizard'][$postkey];
		$data1[$postvalue]['add_custom_field'] = $_POST['add_custom_field'][$postkey];
		$data1[$postvalue]['add_exter'] = $_POST['add_exter'][$postkey]==1 ? 1: 0 ;
		$data1[$postvalue]['accordions'] = $_POST['accordions'][$postkey];
	}
//print_r($_POST['add_db']);print_r($_POST['add_db']); /**
	if(count((array)$_POST['add_db'])>0){
		foreach ($_POST['add_db'] as $postkey => $postvalue) {
			$display_postion = servalnumber($serval, $_POST['add_display_postion'][$postkey]);
			$serval[] = $display_postion;
			$data1[$postvalue]['display_postion'] = $display_postion;
			//$data1[$postvalue]['display_postion'] = $_POST['display_postion'][$postkey];
			$data1[$postvalue]['display_list'] = $_POST['add_display_list'][$postkey]==1 ? 1: 2 ;
			$data1[$postvalue]['lable'] = $_POST['add_lable'][$postkey];
			$data1[$postvalue]['mandatory'] = $_POST['add_mandatory'][$postkey]==1 ? 1: 2 ;
			$data1[$postvalue]['type'] = $_POST['add_type'][$postkey];
			$data1[$postvalue]['insert_display_list'] = $_POST['add_insert_display_list'][$postkey]==1 ? 1: 2 ;
			$data1[$postvalue]['insert_view_width'] = $_POST['add_insert_view_width'][$postkey];
			$data1[$postvalue]['insert_view_wizard'] = $_POST['insert_view_wizard'][$postkey];
			$data1[$postvalue]['add_custom_field'] = $_POST['add_add_custom_field'][$postkey];
			$data1[$postvalue]['add_accordions'] = $_POST['add_accordions'][$postkey];
			$data1[$postvalue]['add_exter'] = '1';
		}
	}
	$data_json = json_encode($data1);
	$data['application_table_name'] = $_POST['table_name'];
	$data['list_attributes'] = $data_json;
	$data['wizard'] = isset($_POST['dis_wizard'])?'0':'1';
	$data['wizard_details'] =json_encode($_POST['wizard_name']);
	$data['display_list_type'] = $_POST['display_list_type'];
	$data['insert_page_type'] = $_POST['insert_page_type'];
	$data['help_text'] = $_POST['content'];
	$data['originator_check_required_or_not'] = $_POST['originator_check_required_or_not'];
	if($_POST['title_accordion'] !=""){
		$data['additional_attributes'] = json_encode($_POST['title_accordion']);
	}
	$data['company_id'] = $_SESSION['company_id'];
	$data['file_upload'] = json_encode($_POST['file_upload']);
	$data['current_status'] = 0;
	if($_POST['id'] !=''){
		$url = API."?mode=updaterowtabledata&method=post&rid=".$_POST['id']."&table=".TBL_DISPLAYATTRIBUTES;
	} else{
		$data['is_active'] = '0';
		$url = API."?mode=adddata&method=post&table=".TBL_DISPLAYATTRIBUTES;
		$checkurl = API."?mode=checkAndInsertapplicationpages&table=".$_POST['table_name']."&company_id=".$_SESSION['company_id']."&display_list_type=".$_POST['display_list_type'];
			//echo $checkurl; exit;
		$res =  callAPI($checkurl);
	}		
	$response = file_post_contents($url, $data);
	if ($response) {
		if ($_POST['submit_value'] == 'continue'){
			//header("location:index.php?option=".$_GET['option']."&table=".$_POST['table_name']."&&display_list=".$_POST['display_list_type']);
		}else{
			//header("location:index.php?option=COM_LIST_DISPLAY_ATTRIBUTES");
		}
		
	}
}

//$_GET['table'] = "accounts";
if((isset($_GET['table']) && $_GET['table'] !='') || $_GET['Rid']!=""){
	$display_list_type = $_GET['display_list'];
	$table = $_GET['table'];
	$rid= encodeId($_GET['Rid']);
	$rid = urldecode($rid);
	$rid = explode(':', $rid);
	$rid = $rid['1'];
	$company_id = $_SESSION['company_id'];
		//echo $rid; exit;
	$tableurl = API."?mode=displayattribute&dis_table=$table&rid=$rid&display_list=$display_list_type";
	//echo $tableurl; //exit;
	$response = callAPI($tableurl);
		//echo $response; exit;
	$role = json_decode($response,1);
		//print_r($role); exit;
	$table = $table!="" ? $table : $role['application_table_name'];
		//$display_list_type = $role['display_list_type'];
	$attribute = json_decode($role['list_attributes'],1);
		//echo "<pre>"; print_r($attribute); exit;
	$rtitle_accordiont = json_decode($role['additional_attributes'],1);
}

$tdurl = API."?mode=showtables";
$showtablesdata = callAPI($tdurl);
$showtables = json_decode($showtablesdata,1);
$title = viewTitle($_GET['option']);
$array = array('id', 'company_id' , 'current_status', 'status_date', 'from_date', 'to_date', 'created_by',  'created_date', 'updated_by', 'updated_date');
//print_r($showtables);exit;
foreach($_SESSION['field_secruity'] as $field_secruity){
	$viewfiled_array[$field_secruity['configuiration_code']] = $field_secruity['field_name']; 
}
$filed_array = array('text' => 'Simple Text', 'touchspin' => 'Touch Spin', 'date_pick' => 'Date','month_year'=>'Month','time_pick'=>'Time' ,'color_pick' => 'Color', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Quantity','currency'=> 'Currency', 'password'=> 'Password', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List','auto_select'=>'Auto Select' ,'multi_select'=> 'Multi Select','rating'=>'Rating','image' => 'Photo','file_upload' => 'Documents Upload','editor' => 'Editor','slider'=>'Slider','number_type'=> 'Number','read_only'=> 'Read Only Text','disable_select'=> 'Disable List','hidden'=> 'Hidden', 'serial_no'=>'Serial Number');
if(count((array)$viewfiled_array)>0){
	$filed_array = array_merge($filed_array,$viewfiled_array);
}


ksort($filed_array);
$accordion_array = array('N' => 'Normal','R' => 'Rigth','L' => 'Left');
?>
<script type="text/javascript">
	function displaywizard(val){
			//alert("hai"+val);
			if(val == 1){ 
				document.getElementById("wizard_display").style.display = "none";
			}else{
				document.getElementById("wizard_display").style.display = "block";
			}

		}
		function GetSelectedTextValue(key) {
    	//alert(key);
    	var table = $('#kt_select2_11').val();
    	//alert(table);
    	var display_list = $('#display_list').val();
    	if( display_list!=0 && table !=0){
    		window.location.href = 'index.php?option=COM_INSERT_DISPLAY_ATTRIBUTES_PRE&table='+table+'&display_list='+display_list;
    	}else{
    		if(table == 0){
    			document.getElementById('table_err').style.display = "block";
    		}else{
    			document.getElementById('table_err').style.display = "none";
    		}
    		if(display_list == 0){
    			document.getElementById('display_list_err').style.display = "block";
    		}else{
    			document.getElementById('display_list_err').style.display = "none";
    		}
    	}
    }
    function addwizard(){
    	//alert("hai");
    	var count = $(".wizardcount").length;
    	count++;
    	var htmlRows = '';
    	htmlRows += '<div class="form-group  row wizardcount">';  
    	htmlRows += '<span class="col-xl-3 col-lg-3 col-form-label">Wizard '+count+' <span class="requried"></span></span>';
    	htmlRows += '<div class="col-lg-9 col-xl-6"><input class="form-control" type="text"   name="wizard_name[]" value=""></div></div>';
    	$("#display_wizard").append(htmlRows);
    	
    }
    function Getinserttype(values,type){
		//alert(type);
		//alert(values);
		var res = values.split("#");
		var table = 	res[0];
		var field = 	res[1];
		if(type == 'select' || type == 'radio' || type == 'switch' || type == 'checkbox' || type == 'multi_select' || type =='disable_select' || type == 'auto_select' || type == 'rating'){
			jQuery.ajax({
				url: "views/custom_attributes_popup.php",
				data:'page=dis_att_type&table='+table+'&field='+field+'&type='+type,
				type: "POST",
				success:function(data){
					$('#popup1').html(data);
					$('#kt_modal_KTDatatable_remote').modal("show");
					$(".js-example-basic-single").select2();
					$(".js-example-basic-multiple").select2(); 
				},
				error:function (){}
			});
			var res = values.split("#");
			$('#table').val(res[0]);
			$('#field').val(res[1]);
			
		}
	}
	function custompop(values,type){
		alert(values);
		$('#field_names').val(values);
		$('#kt_modal_KTDatatable_remote').modal("show");
	}
	function addContentRows(){
		//alert('hai');
		var count = $(".itemRowadd").length;
		var excount = $(".extracount").length;
		//alert('count:'+count);
		count++;
		var htmlRows = '';
		htmlRows += '<div class="form-group row itemRowadd extracount">';  
		htmlRows += '<div class="col-lg-1"><input type="text" class="form-control" readonly name="add_db[]"  value="field_'+count+'" ></div>';    
		htmlRows += '<div class="col-lg-1">	<input type="text" class="form-control" name="add_display_postion[]" value="">	</div>';
		htmlRows += '<div class="col-lg-1"><span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning"><span><input type="checkbox" value="1"  name="add_display_list['+excount+']" ><span></span></span></span></div>';
		htmlRows += '<div class="col-lg-2"><input type="text" class="form-control"  name="add_lable[]" value=""></div>';
		htmlRows += '<div class="col-lg-1"><span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning"><span><input type="checkbox"  value="1"  name="add_insert_display_list['+excount+']"><span></span></span></span></div>';
		htmlRows += '<div class="col-lg-2"><select class="form-select js-example-basic-single" id="<?php echo $table."#field_"; ?>'+count+'" onchange="Getinserttype(this.id,this.value)"  name= "add_type[]"><?php foreach ($filed_array as $typekey => $typevalue) {?><option value = "<?php echo $typekey; ?>"  ><?php echo $typevalue; ?></option><?php } ?></select></div>';
		htmlRows += '<div class="col-lg-1"><span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning"><span><input type="checkbox" value="1"  name="add_mandatory['+excount+']" ><span></span></span></span></div>';
		htmlRows += '<div class="col-lg-1"><select class="form-select js-example-basic-single"  name= "add_insert_view_width[]"><?php for($i=1; $i<13;$i++){ $view_type = "col-lg-".$i; ?><option  value = "<?php echo $i; ?>" ><?php echo $view_type; ?> </option><?php } ?></select> </div>';
		//htmlRows +='<div class="col-lg-1-5"><select class="form-control"  id="" onchange="custompop(this.id,this.value)" name="add_add_custom_field[]"><option value = "2" >No </option><option value = "1" >Yes</option></select></div>';
		htmlRows +='<div class="col-lg-1"><select class="form-select js-example-basic-single"   name= "add_accordions[]"><?php foreach ($accordion_array as $akey => $aarray) { ?><option value = "<?php echo $akey; ?>" <?php if($value['add_custom_field'] == $akey) { ?> selected <?php } ?> ><?php echo $aarray; ?></option><?php } ?></select></div> ';
		htmlRows += '</div>';
		$("#display_content").append(htmlRows);
	}



function fileUplodingDisplay(){
	if($('.filuplodingdispayclass').is(":checked")){
 		 $(".hidedivfileuploding").show();
 	}else{
 		 $(".hidedivfileuploding").hide();
 	}
	//alert("out side");
 }
</script>



<!---- Frist popup scrpit  --- >

	<!----ecnd frist popup  ---->
	<div class="d-flex flex-column-fluid">
		<div class="container-fluid">

			<!--begin::Portlet-->
			<div class="alert alert-warning alert-dismissible">
				<strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong> 
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card card-custom card-sticky" id="kt_page_sticky_card">
						<div class="card-header">
							<h5>Display Attributes</h5>
							<div class="card-header-right">
								<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
									<i class="icofont icofont-reply"></i>
									Back
								</a>
							</div>
						</div>
						<div class="p-2 mt-2">

							
							<div class="row mb-3">
								<div class="col-lg-2">
									<span class="col-form-label ml-2" > Select Table<b class="requried">*</b>  </span>
				 			  </div>
							  <div class="col-lg-4">

							   <select class="form-select js-example-basic-single" id="kt_select2_11" name= "table_name" style="width: 300px;" onchange="GetSelectedTextValue(this.value)">
							   			<option value = "0"> Select Any One</option>
							   			<?php foreach ($showtables as $key => $data) {  ?>
							   				<option  <?php if($table == $data){ ?> selected  <?php } ?> value = <?php echo $data ?>   > <?php echo ucwords(str_replace('_',' ',$data)); ?> </option>
							   		<?php	} ?>
							   	</select>
							   	<div  style="display: none;color: red;" id="table_err"> Please Select Any List</div>
							  

							  </div>	
							  
							  <div class="col-lg-2">
									<span  class="col-form-label ml-2"> Select View Type <b class="requried">*</b> </span>
								<!-- <div class="col-lg-4" > </div> -->
				 			  </div>
							  <div class="col-lg-3">

							   <select class="form-select js-example-basic-single" id="display_list" name="display_list_type" style="width: 300px;" onchange="GetSelectedTextValue(this.value)">
							   			
							   			<option  <?php if($display_list_type == 1){ ?> selected  <?php } ?> value = "1"> Simple List </option>
							   			<option  <?php if($display_list_type == 2){ ?> selected  <?php } ?> value = "2"> Tab List </option>			
							   			<option  <?php if($display_list_type == 3){ ?> selected  <?php } ?> value = "3"> widgets List </option>
								</select>
								<div  style="display: none;color: red;" id="display_list_err"> Please Select Any List</div>
							  </div>	
					
					</div>
							

							<?php if(sizeof((array)$attribute)>0) { 

								$page_name = $role['page_name'] ? $role['page_name'] : str_replace('_',' ',str_replace('isac_', '', $table)); 
								$page_title = $role['page_title']!='' ? $role['page_title'] : ucwords(str_replace('_',' ',str_replace('isac_', '',  $table))); 
								?>
								<!--begin::Form-->
								<form class="kt-form kt-form--label-right j-pro" action="" method="post">

									<div class="card-body">
										<div class="form-group row">
											<span class="col-xl-3 col-lg-3 col-form-label">Page Title<span class="requried">*</span></span>
											<div class="col-lg-9 col-xl-6">									
												<input class="form-control" type="hidden" name="page_name" value="<?php echo $page_name; ?>">
												<input class="form-control" type="text" name="page_title" value="<?php echo $page_title; ?>">								
											</div>
										</div>
										<div class="form-group  row">
											<span class="col-xl-3 col-lg-3 col-form-label">Right Accordion Name<span class="requried">*</span></span>
											<div class="col-lg-9 col-xl-6">

												<input class="form-control" type="text" name="title_accordion[right]" value="<?php echo $rtitle_accordiont['right']; ?>">
											</div>	
										</div>
										<div class="form-group  row">
											<span class="col-xl-3 col-lg-3 col-form-label">Left Accordion Name<span class="requried">*</span></span>
											<div class="col-lg-9 col-xl-6">

												<input class="form-control" type="text" name="title_accordion[left]" value="<?php echo $rtitle_accordiont['left']; ?>">

											</div>
										</div>
										<div class="form-group  row">
											<span class="col-xl-3 col-lg-3 col-form-label" >Select Page Type</span>
											<div class="col-lg-9 col-xl-6">
												<select class="form-select js-example-basic-single" name="insert_page_type">
													<option <?php if($role['insert_page_type'] == 0){ ?> selected <?php } ?> value="0">Genral</option>
													<option <?php if($role['insert_page_type'] == 1){ ?> selected <?php } ?> value="1">POP UP</option>
													<option <?php if($role['insert_page_type'] == 2){ ?> selected <?php } ?> value="2">Open New Tab</option>
												</select>
											</div>
										</div>
										<div class="form-group  row">
											<span class="col-xl-3 col-lg-3 col-form-label" >Originator Check Required Or Not</span>
											<div class="col-lg-9 col-xl-6">
												<select class="form-select js-example-basic-single" name="originator_check_required_or_not">
													<option <?php if($role['originator_check_required_or_not'] == 'Y'){ ?> selected <?php } ?> value="Y">Yes</option>
													<option <?php if($role['originator_check_required_or_not'] == 'N'){ ?> selected <?php } ?> value="N">No</option>
												</select>
											</div>
										</div>

										<?php if(!isset($role['id'])){ $role['wizard']=1; } ?>
						<?php /** rohan ?>
						<div class="form-group  row">
							<span class="col-xl-3 col-lg-3 col-form-label" >Display As Tab?</span>
							<div class="col-lg-9 col-xl-6">
								<input class="js-switch" <?php if($role['wizard']==0){ ?> checked <?php } ?> type="checkbox" name="dis_wizard"  onchange="displaywizard(this.value)" value="0"  >	
								<!-- <select class="form-select js-example-basic-single" name="dis_wizard" onchange="displaywizard(this.value)">
									<option value="1">NO</option>
									<option  <?php //if($role['wizard']==0){ ?> selected <?php } ?> value="0">Yes</option>
								</select> -->
							</div>
						</div>
						<?php rohan**/ ?>
		<div class="form-group  row">
		<?php
			$help_content =  $role['help_text']!="" ?  $role['help_text'] : "This function is used for maintaining <strong>".viewTitle($table)."</strong>. * represents mandatory fields.";
		?>
		<span class="col-xl-3 col-lg-3 col-form-label">Content<span class="requried"></span></span>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control" type="text"   name="content" value="<?php echo $help_content; ?>">
		</div>
			</div> 
					<div class="col-lg-12"> 
						<div class="form-group row">
							<div class="col-lg-1">
								<span>Field Name</span>
							</div>
							<div class="col-lg-1">
								<span class=""> Position </span>
							</div>
							<div class="col-lg-1">
								<span class="">   Display?</span>
							</div>
							<div class="col-lg-2">
								<span class="">Lable </span>
							</div>
							<div class="col-lg-1">
								<span>Insertable  </span>
							</div>
							<div class="col-lg-2">
								<span class="">Display Type </span>
							</div>
							<div class="col-lg-1">
								<span class="">   Mandatory?</span>
							</div>
							<div class="col-lg-1">
								<span class="">Width </span>
							</div>
							<div class="col-lg-1">
								<span class="">Accordion</span>
							</div>
							<div class="col-lg-1">
								<span class="">wizard</span>
							</div>
							<!-- <div class="col-lg-1-5">
								<span class="">  Add Custom Field  </span>
							</div> -->
						</div>
					</div>

					<?php $len=0;  $add_extra_content = 0; foreach ($attribute as $key => $value) { 
						if($key != 'additional_attributes'){
							$itemRowadd ="";
							if($value['add_exter'] == 1){
								$itemRowadd = "itemRowadd";
							}
							//print_r($value);
							?>
							<div class="form-group row <?php echo $itemRowadd;  ?>">
								<div class="col-lg-1">
									<input type="text" class="form-control" name="db[]" readonly value=<?php echo $key;?> >
								</div>
								<div class="col-lg-1">
									<input type="text" class="form-control" name="display_postion[]" value=<?php echo $value['display_postion'];?>>
								</div>

								<div class="col-lg-1">			
									<?php// echo $value['display_list']; ?>
									<span class="switch switch-outline switch-icon switch-primary">
										<input class="js-switch" type="checkbox" <?php if($value['display_list'] == '1') { ?> checked="checked" <?php } ?>  value="1"  name="display_list[<?php echo $len; ?>]" >
									</span>
								</div>
								<div class="col-lg-2">
									<input type="text" class="form-control"  name="lable[]" value="<?php echo str_replace('EDI_', ' ', $value['lable']);?>">
								</div>
								<div class="col-lg-1">
									<span class="switch switch-outline switch-icon switch-primary">
										<span>			
											<?php if(in_array($key, (array)$array)){ ?> 
												<input class="js-switch" type="checkbox" disabled  <?php if($value['insert_display_list'] == '1') { ?> checked="checked" <?php } ?> >
												<input type="hidden" name="insert_display_list[<?php echo $len; ?>]" value="2">

											<?php }else{ ?>
												<input class="js-switch" type="checkbox" <?php if($value['insert_display_list'] == '1') { ?> checked="checked" <?php } ?>  value="1"  name="insert_display_list[<?php echo $len; ?>]" >
											<?php } ?>

											<span></span>
										</span>
									</span>

								</div>

								<div class="col-lg-2">	
									<select class="form-select js-example-basic-single" id="<?php echo $table."#".$key; ?>" onchange="Getinserttype(this.id,this.value)" name= "type[]">
										<option value = "<?php echo $value['type']; ?>"  ><?php echo $filed_array[$value['type']]; ?></option>
										<?php foreach ($filed_array as $typekey => $typevalue) {?>
											<option value = "<?php echo $typekey; ?>"  ><?php echo $typevalue; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-1">
									<span class="switch switch-outline switch-icon switch-primary">
										<span>			
											<input type="checkbox" class="js-switch" <?php if($value['mandatory'] == '1') { ?> checked <?php } ?>  value="1"  name="mandatory[<?php echo $len; ?>]" >
											<span></span>
										</span>	
									</span>	
								</div>
								<div class="col-lg-1">	<?php //echo $value['insert_view_width']; ?>

									<select class="form-select js-example-basic-single"  name= "insert_view_width[]">

										<?php for($i=1; $i<13;$i++){ 
											$view_type = "col-lg-".$i; ?>
											<option  value = "<?php echo $i; ?>" <?php if($value['insert_view_width']== $i){ ?> selected <?php }?>><?php echo $view_type; ?> </option>
										<?php } ?>	
									</select>
								</div>

								<?php /** ?> <div class="col-lg-1-5">	
									<select class="form-control"  id="<?php //echo $key; ?>" onchange="custompop(this.id,this.value)" name= "add_custom_field[]">
										<option value = "2" >No </option>
										<option value = "1" <?php //if($value['add_custom_field'] == '1') { ?> selected <?php } ?> >Yes</option>
									</select>
								</div> <?php **/ ?>

								<div class="col-lg-1">	
									<select class="form-select js-example-basic-single"   name= "accordions[]">
										<?php foreach ($accordion_array as $akey => $aarray) { ?>
											<option value = "<?php echo $akey; ?>" <?php if($value['accordions'] == $akey) { ?> selected <?php } ?> ><?php echo $aarray; ?></option>
										<?php } ?>
									</select>
								</div> 
								<div class="col-lg-1">
									<select class="form-select js-example-basic-single" name= "insert_view_wizard[]">
										<?php for($i=1; $i<11;$i++){ 
											$view_type = $i."- Wizard"; ?>
											<option  value = "<?php echo $i; ?>" <?php if($value['insert_view_wizard']== $i){ ?> selected <?php }?>><?php echo $view_type; ?> </option>
										<?php } ?>	
									</select>
								</div>
							</div>

							<input type="hidden" name="add_exter[]" value="<?php echo $value['add_exter'] ?>">
							<?php $len++; } else{ $add_cont = 1; } } 
							if($add_cont == 1){?>
								<div id="display_content"></div>
								<div > <div onclick="addContentRows()"  class="btn btn-success" type="button">+ Add More</div> </div>
							<?php } ?>

						</div>
						<?php /** ?>
						<!--<div class="form-group  row">
							<span class="col-xl-3 col-lg-3 col-form-label">View<span class="requried">*</span></span>
							<div class="col-lg-9 col-xl-6">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
									<input class="form-control" type="text"    name="view" value="<?php //echo $role['view']; ?>">
								</div>
							</div>
						</div>
					<div class="form-group  row">
							<span class="col-xl-3 col-lg-3 col-form-label">Add<span class="requried">*</span></span>
							<div class="col-lg-9 col-xl-6">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
									<input class="form-control" type="text" name="add_url"  value="<?php //echo $role['add_url']; ?>">
								</div>
							</div>
						</div>
					<div class="form-group  row">
							<span class="col-xl-3 col-lg-3 col-form-label">Insert View<span class="requried">*</span></span>
							<div class="col-lg-9 col-xl-6">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
									<input class="form-control" type="text"    name="insert_view" value="<?php //echo $role['insert_view']; ?>">
								</div>
							</div>
						</div> -->

					<!-- <div class="form-group  row">
						<span class="col-xl-3 col-lg-3 col-form-label">Add Custom Field<span class="requried"></span></span>
							<div class="col-lg-9 col-xl-6">
								<div class="input-group">
									<button type="button" class="btn btn-brand" data-toggle="modal" onclick="custompop()">Launch Modal</button>
								</div>
							</div>
						</div> --><?php **/ ?>
						<div class="card-body">
							<?php $classwizard =''; if($role['wizard']==1){ 
								$classwizard ='style="display: none;"';
							}
							//$wizard_data = json_decode($role['wizard_details'],1);
							?>
							<div <?php echo $classwizard; ?> id="wizard_display">
								<?php if(count((array)$wizard_data) >0 ) { $z=1;
								foreach ($wizard_data as $key => $wdata) { ?>
								<div class="form-group  row wizardcount">
									<span class="col-xl-3 col-lg-3 col-form-label">Wizard <?php echo $z; ?> <span class="requried"></span></span>
									<div class="col-lg-9 col-xl-6">
										<input class="form-control" type="text"   name="wizard_name[]" value="<?php echo $wdata; ?>">
									</div>
								</div>
								<?php $z++; } }else{ ?>
								<div class="form-group  row wizardcount">
									<span class="col-xl-3 col-lg-3 col-form-label">Wizard 1 <span class="requried"></span></span>
									<div class="col-lg-9 col-xl-6">
										<input class="form-control" type="text"   name="wizard_name[]" value="<?php echo ""; ?>">
									</div>
								</div>
								<?php } ?>
								<div id="display_wizard"></div>
								<div> 
									<div onclick="addwizard()"  class="btn btn-success" type="button">+ Add Wizard</div> 
								</div>
							</div>

							<div class="form-group row">
								<div class="col-lg-8 col-md-12 col-sm-12 col-12 mrgn-btm">
									<div class="col-lg-10 col-md-10 col-sm-12 col-12" id="accordionExample6"> 
										<div class="card">
											<div class="card-header" id="headingTwo6" style="padding: 10px !important;">
												<div class="accordion-msg waves-effect waves-dark scale_active collapsed" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseTwo6right" aria-expanded="true" aria-controls="collapseTwo6right" style="padding: 5px;border-top: 0px;">File Upload
													<!--<b class="f-24 float-right">+</b> -->
												</div>
											</div>
											<div id="collapseTwo6right" class="collapse" aria-labelledby="headingTwo6" data-parent="#accordionExample6">
												<div class="card-body">
													<?php $file_data = json_decode($role['file_upload'],1); ?>
													<div class="form-group row">  	
														<span class="col-xl-4 col-form-label">File Upload Allowed</span>
														<div class="col-lg-8 mt-2">
															<input class="js-switch filuplodingdispayclass"  <?php if($file_data['file_upload']==0){ ?> checked <?php } ?> type="checkbox"  name="file_upload[file_upload]" onchange="fileUplodingDisplay()" value="0"  >	
															<!--<select class="form-select js-example-basic-single" name="file_upload[file_upload]" >
																<option value="1">NO</option>
																<option  value="0">Yes</option>
															</select> -->
														</div>
													</div> 

													<div class="hidedivfileuploding" style="display: <?php echo $file_data['file_upload']==0?"":none; ?>">

														<div class="form-group row">  	
															<span class="col-lg-4 col-form-label">Title</span>
															<div class="col-lg-8 ">
																<input type="text" name="file_upload[fileuplodinglabel]" value="<?php echo $file_data['fileuplodinglabel']; ?>" class="form-control">
															</div>
														</div> 
														<div class="form-group row">  	
															<span class="col-xl-4 col-lg-4 col-form-label">Width</span>
															<div class="col-lg-8 col-xl-8">
																<select class="form-select js-example-basic-single" name="file_upload[fileuplodingwidth]" >
																	<?php for($i=1; $i<13;$i++){ 
																		$view_type = "col-lg-".$i; ?>
																		<option  value = "<?php echo $i; ?>" <?php if(($file_data['fileuplodingwidth']== $i) OR ($file_data['fileuplodingwidth'] =="" AND $file_data['fileuplodingwidth'] == '6')){ ?> selected <?php }?>><?php echo $view_type; ?> </option>
																	<?php } ?>
																</select>
															</div>
														</div> 	
														<div class="form-group row">  	
															<span class="col-xl-4 col-lg-4 col-form-label">Number Of Files</span>
															<div class="col-lg-8 col-xl-8">
																<input type="text" name="file_upload[number_of_files]" value="<?php echo $file_data['number_of_files']; ?>" class="form-control">
															</div>
														</div> 	
														<div class="form-group row">  	
															<span class="col-xl-4 col-lg-4 col-form-label">individual file size limit (bytes)</span>
															<div class="col-lg-8 col-xl-8">
																<input type="text" name="file_upload[file_size]" value="<?php echo $file_data['file_size']; ?>" class="form-control">
															</div>
														</div> 	
														<div class="form-group row">
															<?php $acceptetfilearray = array('image/*'=>'All Image Formats','.jpg'=>'JPG (Image)','.jpeg'=>'JPEG (Image)','.png'=>'PNG (Image)','.gif'=>'GIF (Image)','.bmp'=>'BMP (Image)','.tiff'=>'TIFF (Image)','.svg'=>'SVG (Image)','.docs'=>'DOCX','.pdf'=>'PDF','.odt'=>'ODT','.txt'=>'TXT','.rtf'=>'RTF','.doc'=>'DOC'); ?>
															<span class="col-xl-4 col-lg-4 col-form-label">Accepted Files</span>
															<div class="col-lg-8 col-xl-8">
															   	<select class="form-select js-example-basic-multiple"  name="file_upload[accepted_files][]" style="width: 300px;" multiple>
															   		<?php foreach ($acceptetfilearray as $accceptkeyname=>$accceptfilename) {  ?>
															   			<option <?php echo (in_array($accceptkeyname, (array)$file_data['accepted_files']))?"selected":""; ?> value = <?php echo $accceptkeyname ?>   > <?php echo $accceptfilename; ?> </option>
															   		<?php } ?>
															   	</select>
															</div>
														</div>
														<div class="form-group row">  	
															<span class="col-xl-4 col-lg-4 col-form-label">Display Deleted Files</span>
															<div class="col-lg-8 col-xl-8 mt-2">
																<input class="js-switch"  <?php if($file_data['file_upload']==0){ ?> checked <?php } ?> type="checkbox"  name="file_upload[fileuplodingdeleteddisplay]"  value="0"  >	
														
															</div>
														</div>	
														<div class="form-group row">  	
															<span class="col-xl-4 col-lg-4 col-form-label"> Deleted Files Box Width</span>
															<div class="col-lg-8 col-xl-8">
																<select class="form-select js-example-basic-single" name="file_upload[fileuplodingdleetewidth]" >
																	<?php for($i=1; $i<13;$i++){ 
																		$view_type = "col-lg-".$i; ?>
																		<option  value = "<?php echo $i; ?>" <?php if($file_data['fileuplodingdleetewidth']== $i){ ?> selected <?php }?>><?php echo $view_type; ?> </option>
																	<?php } ?>
																</select>
															</div>
														</div> 	
													</div>					
												</div>
											</div>
										</div>
									</div>
								</div>	

								<div class="card-footer">
									<div class="row">
										<div class="col-lg-6">
											<input type="hidden" name="submit" value="Add" />
											<input type="hidden" name="table_name" value="<?php echo $table; ?>" />
											<input type="hidden" name="display_list_type" value="<?php echo $display_list_type; ?>" />
											<input type="hidden" name="id" value="<?php echo $role['id']; ?>" />
											<button id="kt_llogin_signup_submitt" class="btn btn-primary mr-2 mb-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
											<button id="" class="btn btn-primary mr-2 mb-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
											<button type="reset" class="btn btn-secondary mb-2" ><i class="la la-close"></i>Cancel</button>

										</div>
										<div class="col-lg-6 text-right">
											<button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
										</div> 
									</div>
								</div>

									</form>
								</div>
							<?php } ?>

							<!--end::Form-->
						</div>

						<!--end::Portlet-->

						<!--begin::Portlet-->
					</div>
				</div>
				<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-xl">
						<div class="modal-content" style="min-height: 590px;" id="popup1">

						</div>
					</div>

				</div>

				<?php include_once('custom_add_field.php'); ?>
				<!--end::Portlet-->
			</div>
		</div>
	</div>
						

	<div class="modal fade" id="large-Modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Modal title</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<h5>Default Modal</h5>
					<p>This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary waves-effect waves-light ">Save changes</button>
				</div>
			</div>
		</div>
	</div>
