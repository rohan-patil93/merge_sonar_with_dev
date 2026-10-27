<?php
$_SESSION['apply_js']['multiselest'] = '1';
if(isset($_POST['submittour'])){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertapplicationtours&method=post";
 	$response = file_post_contents($url, $_POST);
    $return = json_decode($response,1);
}


$table_name = (isset($_POST['table_name_txt']))?$_POST['table_name_txt']:$_GET['table'];
$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'application_table_name'=>$table_name);
$wh1['order'] = "sequence ASC";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=application_tours&data=".$data1; 
$data_details1 = callAPI($url1);
$resarray = json_decode($data_details1,1);








// $tdurl = API."?mode=showtables";
// $showtablesdata = callAPI($tdurl);
// $showtables = json_decode($showtablesdata,1);


$showtableswh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$showtableswh["field"] = "DISTINCT application_table_name";
$showtableswhdecid = urlencode(json_encode($showtableswh));
$showtablesurl = API."?mode=gettabledetailswithconditions&table=application_tours&data=".$showtableswhdecid; 
$showtables = callAPI($showtablesurl);
$showtables = json_decode($showtables,1);
//print_r($showtables);

$filed_array = array('text' => 'Simple Text', 'touchspin' => 'Touch Spin', 'date_pick' => 'Date','month_year'=>'Month','time_pick'=>'Time' ,'color_pick' => 'Color', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Quantity','currency'=> 'Currency', 'password'=> 'Password', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List','auto_select'=>'Auto Select' ,'multi_select'=> 'Multi Select','rating'=>'Rating','image' => 'Photo','editor' => 'Editor','slider'=>'Slider','number_type'=> 'Number','read_only'=> 'Read Only Text','disable_select'=> 'Disable List','hidden'=> 'Hidden', 'serial_no'=>'Serial Number', 'button'=>'Button','array'=>'Array');

$wh5["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wh5["field"] = "id,module_id,module_name";
$data5 = urlencode(json_encode($wh5));
$url5 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data5; 
$mopdule_setup = callAPI($url5);
$mopdulesetup = json_decode($mopdule_setup,1);

?>
<script>
	
	function addmorerow(){
     	var count = $(".itemRow").length;
     	var htmlRows = '';
        htmlRows += '<tr class="itemRow cardid'+count+'">';  
        htmlRows += '<td><div class="row"><div class="col-lg-2"><span class="deleteiconcustom text-danger f-22" onclick="deleteitem('+count+')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Delete VRC Card"><i class="ti-trash"></i></span></div><div class="col-lg-10"><input type="hidden" name="rid[]" ><input type="text" name="field_name[]"  class="form-control"  required ></div></div></td>';
        htmlRows += '<td><input type="text" name="menu_path[]"  class="form-control"  ></td>';
        htmlRows += '<td><input type="text" name="reference_field_name[]" class="form-control"  required ></td>';
        htmlRows += '<td><textarea  name="help_text[]" class="form-control" required ></textarea></td>';
        htmlRows += '<td> <select class="form-select js-example-basic-single"  name="field_type[]" ><?php foreach($filed_array as $key=>$value){ ?>
<option value="<?php echo $key; ?>"  <?php echo $respons['field_type']==$key?"selected":""; ?>><?php echo $value; ?></option><?php } ?></select></td>';
   htmlRows += '<td><input type="text" name="sequence[]"  class="form-control"  required></td>';
   htmlRows += '<td><input type="checkbox" class="js-switch'+count+'" name="display['+count+']" value="1" /></td>';
        htmlRows += '</tr>';
        $('#toureaddmore').append(htmlRows);
         $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        var elemdefault = document.querySelector(".js-switch"+count);
	 	var switchery = new Switchery(elemdefault, { color: '#d6d6d6', jackColor: '#fff' });
	}

function selecttablename(table){
	if(table=="-1"){

window.location.href = 'index.php?option=COM_INSERT_APPLICATION_TOURS&table='+table;
	}else{
		window.location.href = 'index.php?option=COM_INSERT_APPLICATION_TOURS&table='+table;
	}
}

function deleteitem(cardid){
    $(".cardid"+cardid).remove();
}

</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid ">
		<div class="card card-custom card-sticky " id="kt_page_sticky_card">
			<div class="card-header">
				<h5>Take A Tours</h5><i class="ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Go Toure"></i> 
				<div class="card-header-right">
				       <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                     <i class="icofont icofont-reply"></i> Back</a>
				</div>
			</div>
			<div class=" card-custom mt-2">
				<div class="card-body">
<form method="post" action="">
<div class="row">
	<label class="col-lg-2 f-w-700 ">Select table name</label>
	<div class="col-lg-3">
	  <select class="form-select js-example-basic-single" name="application_table_name" onchange="selecttablename(this.value)">
			<option value = "0"> Select Any One</option>
			<?php foreach ($showtables as $data) {  ?>
				<option  <?php if($resarray[0]['application_table_name'] == $data['application_table_name']){ ?> selected  <?php } ?> value = <?php echo $data['application_table_name'] ?>   > <?php echo ucwords(str_replace('_',' ',$data['application_table_name'])); ?> </option>
		<?php	} ?>
				<option value = "-1"  <?php echo $_GET['table']=="-1"?"selected":""?>>Other Table</option>
		</select>
		<?php if($_GET['table']=="-1"){ ?>
		<input type="text" name="table_name_txt" class="mt-2" id="table_name_txt" value="<?php echo $resarray[0]['application_table_name']; ?>" placeholder="Enter Table/Page Name"> <?php } ?>

	</div>
	<!-- <label class="col-lg-2 f-w-700 ">Select Module</label>
<div class="form-group col-lg-3 w-full">
		<select class="form-select js-example-basic-single" id="application_module" name="application_module">
			<?php foreach($mopdulesetup as $modulesetup){ ?>
				<option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['application_module'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
			<?php } ?>
		</select>
	</div> -->

<?php  /*if($Rid ==""){ ?>
									<div class="form-group col-lg-3 w-full">
										<select class="form-select js-example-basic-multiple" id="application_module" name="application_module[]" multiple>
											<?php foreach($mopdulesetup as $modulesetup){ ?>
												<option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['application_module'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
											<?php } ?>
										</select>
									</div>
									<?php  }else{ ?>
										<div class="form-group col-lg-3 w-full">
										<select class="form-select js-example-basic-single" id="application_module" name="application_module">
											<?php foreach($mopdulesetup as $modulesetup){ ?>
												<option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['application_module'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
											<?php } ?>
										</select>
									</div>
									
									<?php } */ ?>	


</div>

<hr>

<?php if($table_name !=""){ ?>
<table class="table table-hover table-custom-responsive"  id="toureaddmore"> 
    <thead>
	  <tr>	
		  <th width="15%">Field Name</th>
		  <th width="15%">Menu Path</th>
		  <th width="15%">Ref Field Name</th>
		  <th width="30%">Help Text</th>
		  <th width="15%">Field  Type</th>
		  <th width="5%">Sequence</th>
		  <th width="5%">Display</th> 
	  </tr>
	 </thead>
	 <tbody>
	  <?php $i=0; foreach($resarray as $respons){ ?>
	  <tr class="itemRow">
		<td> 
			<input type="hidden" name="rid[]" value="<?php echo $respons['id']; ?>">
	  		<input type="text" name="field_name[]" id="field_name<?php echo $i; ?>" class="form-control" value="<?php echo $respons['field_name']; ?>"  readonly>
	  	</td>
	  	<td> 
	  		<input type="text" name="menu_path[]" id="menu_path<?php echo $i; ?>" class="form-control" value="<?php echo $respons['menu_path']; ?>"  >
	  	</td>
		<td> 
	  		<input type="text" name="reference_field_name[]" id="reference_field_name<?php echo $i; ?>" class="form-control" value="<?php echo $respons['reference_field_name']; ?>"  >
	  	</td>
		<td><textarea  name="help_text[]" class="form-control" ><?php echo $respons['help_text']; ?></textarea></td>
		<td> 

 <select class="form-select js-example-basic-single" id="" name="field_type[]"  id="field_type<?php echo $i; ?>">
               
               <?php foreach($filed_array as $key=>$value){ ?>
<option value="<?php echo $key; ?>"  <?php echo $respons['field_type']==$key?"selected":""; ?>><?php echo $value; ?></option>

<?php } ?>
            </select>

	  	</td>
	  	<td> 
	  		<input type="text" name="sequence[]" id="sequence<?php echo $i; ?>" class="form-control" value="<?php echo $respons['sequence']; ?>"  >
	  	</td>
	  	<td>
	  		<input type="checkbox" class="js-switch" name="display[<?php echo $i; ?>]" value="1" <?php echo $respons['display']=='1'?"checked":""; ?>  />

	  	</td>

	  	
	  </tr>
	<?php  $i++; } ?>
	 </tbody>

</table>

<div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <button class="btn btn-success" id="addRows" type="button" onclick="addmorerow()">+ Add More</button>
        </div>
        <div class="col-lg-6"></div>
         <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-right">
            <button type="submit" class="btn btn-primary " name="submittour" type="button" >Submit</button>
        </div>
    </div>
<?php } ?>
</form>
				</div>
			</div>
		</div>
	</div>
</div>	
						

