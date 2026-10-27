<script>
	function dyanamicreferFunc(val,referfield,table){
	$.post("ajax.php",{'page':'dyanamicreferfunc','val':val,'referfield':referfield,'table':table},function(data){
		$("#"+referfield).html(data);
			console.log(data);
			})
	}
	function dynamicfunction(field,values){
		$.ajax({
            type: 'post',
            url: 'ajax.php',
        	data: "page=checkhsncodegst&values="+values,
            success:function(data){
            	var result = JSON.parse(data);
            	var gst = result.gst;
            	$("#sgst").val(gst/2);
            	$("#cgst").val(gst/2);
            	$("#igst").val(gst);
                console.log(data);
               }
            });

	}
</script>
<?php 
$rattig = 1;
foreach ($disarray as $diskey => $disvalue) {  // print_r($disvalue);
	if($diskey != 'normal'){ if($diskey == 'right' || !isset($disarray['right'])){ ?>
	<div class="row">
	<?php } ?>
    	<div class="form-group col-lg-6">
	        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6"> 
	            <div class="card">
	                <div class="card-header" id="headingTwo6">
	                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo6<?php echo $diskey; ?>" aria-expanded="false" aria-controls="collapseTwo6">
	                      <?php if($accord[$diskey] !=""){ echo  $accord[$diskey]; } else{ echo "Additional Information-".$lle;} ?>
	                   
	                    </div>
	                </div>
	                <div id="collapseTwo6<?php echo $diskey; ?>" class="collapse" aria-labelledby="headingTwo6" data-parent="#accordionExample6">
	                    <div class="card-body">
	                    	<div class="kt-portlet__body">
								<div class="form-group row ">
<?php	}

foreach($disvalue as  $fullvalue){
	$key = $fullvalue['table'];
//print_r($fullvalue); echo $fullvalue['lable'];
	$after = $fullvalue['after'];
	$value =  $fullvalue['lable'];
	$class = "form-group col-lg-".$fullvalue['width'];
	$sch = 0;
	/*if($diskey == 'normal'){ $class = "form-group col-lg-".$fullvalue['width']; }*/
	?>
	<?php if($fullvalue['type'] == 'text' || $fullvalue['type'] == 'read_only' ){ $read_only=""; if($fullvalue['type'] == 'read_only'){ $read_only= "readonly"; }?>
	<div class="<?php echo $class; ?>">
		<label class=""><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
		<input class="form-control"  type="text" <?php echo $read_only; ?>  id="<?php echo $key; ?>"  name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
		<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please enter <?php echo $value; ?> </div> 
	</div>
	<?php } else  if($fullvalue['type'] == 'serial_no'){ $read_only= "readonly"; ?>
<?php if( $role[$key] =="") { $role[$key] = dyanamicserialnumber($table_name); } ?>
	<div class="<?php echo $class; ?>">
		<label class=""><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
		<div class="input-group input-icon-right">
			<input class="form-control"  type="text" <?php echo $read_only; ?> id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
			<div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
		</div>
		<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please enter <?php echo $value; ?> </div> 
	</div>
<?php } else if($fullvalue['type']=='select' || $fullvalue['type']=='disable_select' ) {  $read_only="";  $_SESSION['apply_js']['multiselest'] = 1; if($fullvalue['type'] == 'disable_select'){ $read_only= "disabled"; }?>	
<?php 
$attribute_types_list = dyanamicSelectDataTablenameAllFileds($table_name,$key);
if($attribute_types_list['refer_by_action']=='0'){	
	$selectdata = dyanamicreferFunc($role[$attribute_types_list['refer_by_field']],$key,$table_name); 
}else{
	$selectdata = dyanamicselectdata($table_name,$key); 
}
if($key == "hsn_and_sac_code_id"){
	//$dynamicfunction = "onchange='dynamicfunction(/''.$key.'/',this.value)'";
	$dynamicfunction =  "onchange=\"dynamicfunction('".$key."', this.value)\"";
}
?>
		<div class="<?php echo $class; ?>">
		<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
		<select class="form-select js-example-basic-single" id="<?php echo $key; ?>" <?php echo $read_only; ?> name="<?php echo $key; ?>" <?php if($attribute_types_list['refer_action'] == '0'){ ?>onchange="dyanamicreferFunc(this.value,'<?php echo $attribute_types_list['refer_field']; ?>','<?php echo $attribute_types_list['application_table_name']; ?>')" <?php } ?> <?php echo $dynamicfunction; ?>>
			<option value="0"> Select Any one <?php echo $value; ?></option>
		<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
				<option <?php if($selectkey == $role[$key] ){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
			</optgroup>
		<?php  } ?>		
	</select>
	    <?php if($fullvalue['type']=='disable_select'){ ?>
	        <input type="hidden" name="<?php echo $key?>" value=<?php echo $role[$key]; ?> />
	    <?php } ?>
		<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
	</div>
	<?php $select++; } else if($fullvalue['type']=='auto_select' || $fullvalue['type']=='disable_select' ) {  $read_only=""; if($fullvalue['type'] == 'disable_select'){ $read_only= "disabled"; } ?>
<?php ///$selectdata = dyanamicselectdata($table_name,$key);

$attribute_types_list = dyanamicSelectDataTablenameAllFileds($table_name,$key);
if($attribute_types_list['refer_by_action']=='0'){	
	$selectdata = dyanamicreferFunc($role[$attribute_types_list['refer_by_field']],$key,$table_name); 
}else{
	$selectdata = dyanamicselectdata($table_name,$key); 
}
 ?>
		<div class="<?php echo $class; ?>">
		<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
		<select class="form-select js-data-example-ajax-Comman-form  col-lg-4" <?php echo $read_only; ?>  data-tablename="<?php echo $table_name; ?>"  data-fieldsname="<?php echo $key; ?>" data-value="" data-pagename="comman_form" name="<?php echo $key?>" <?php if($attribute_types_list['refer_action'] == '0'){ ?>onchange="dyanamicreferFunc(this.value,'<?php echo $attribute_types_list['refer_field']; ?>','<?php echo $attribute_types_list['application_table_name']; ?>')" <?php } ?>>
        <option value="0"> Select Any one <?php echo $value; ?></option>
        <?php foreach ($selectdata as $selectkey => $selectvalue) {  if($selectkey == $role[$key] ){ ?> 
				<option  selected value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
		<?php  } } ?>
    </select>
	    <?php if($fullvalue['type']=='disable_select'){ ?>
	        <input type="hidden" name="<?php echo $key?>" value=<?php echo $role[$key]; ?> />
	    <?php } ?>
		<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
	</div>
	<?php $select++; }else if($fullvalue['type']=='radio') {  ?>
	<?php $selectdata = dyanamicselectdata($table_name,$key); ?>
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="radio radio-outline  mt-1">
			<?php foreach ($selectdata as $selectkey => $selectvalue) {  
				if($sch==0){ $role[$key] = $role[$key] !="" ? $role[$key] : $selectkey; } ?> 
			<label class="radio radio-inline"> <input type="radio"  <?php if($selectkey == $role[$key]){ ?>checked <?php } ?> name="<?php echo $key; ?>" value='<?php echo $selectkey; ?>'>	<span></span><?php echo $selectvalue; ?> </label>
			<?php $sch++; } ?>
			</div>
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
		</div>
<?php }elseif($fullvalue['type']=='textarea') { ?>
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="kt-radio-inline">
			<textarea type="text" class="form-control " rows="5" cols="5" id="<?php echo $key; ?>" name="<?php echo $key; ?>" ><?php echo $role[$key];?></textarea>
			</div>
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
		</div>

<?php }elseif($fullvalue['type']=='touchspin') { ?>
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="">
				<input id="kt_touchspin_1" type="text" class="form-control" value="1" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Select <?php echo $value; ?>">
			</div>
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
		</div>
<?php }elseif($fullvalue['type']=='switch') { ?>
		<?php $selectdata = dyanamicselectdata($table_name,$key); 
		$k=1;
		//print_r($selectdata); 
		foreach ($selectdata as $selectkey => $selectvalue) { 
			if($k < 3){
				$one = $k==1 ? $selectkey : $one;
				$sen = $k==2 ? $selectkey : $sen;
			}else{
				break;
			}
		$k++; } //echo $one."##".$sen; exit;?>
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="">
				<input type="hidden"   name="<?php echo $key; ?>"  value= "<?php echo $one; ?>">
               	<input type="checkbox"  class="js-switch" value="<?php echo $sen; ?>"  name="<?php echo $key; ?>" <?php if($selectkey == $role[$key] ){ ?> checked <?php } ?> />
		</div>
		</div>

<?php } else if($fullvalue['type']=='checkbox') { ?>
		<?php $selectdata = dyanamicselectdata($table_name,$key);
		if(is_array($role[$key])){ $mulcheck = $role[$key]; }else{  $mulcheck = json_decode($role[$key],1); }
		?>
		<div class="<?php echo $class; ?>">
			
				<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label></br>
				
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
						<div class="checkbox-zoom zoom-primary mt-1">
					<label class="form-label">
						<input type="checkbox"  <?php if(in_array($selectkey, (array)$mulcheck)){ ?> checked <?php } ?>  name="<?php echo $key."[]"; ?>" value= "<?php echo $selectkey; ?>" >
						<span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
        </span>  <span><?php echo $selectvalue; ?> </span>
					</label></div>
					<?php } ?>	
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
		</div>					
<?php } else if($fullvalue['type']=='multi_select') {  
if(is_array($role[$key])){ $mulcheck = $role[$key]; }else{  $mulcheck = json_decode($role[$key],1); } ?>
	<div class="<?php echo $class; ?>">
		<?php $selectdata = dyanamicselectdata($table_name,$key); ?>
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			

			<?php if($key  == 'module_setup_id'){   //Show only module_setup_id fields
				if($_GET['Rid'] ==""){ //insert time show multi select ?>	
				<select class="form-select js-example-basic-multiple" id="kt_select2_3<?php echo $mutiselet; ?>" name="<?php echo $key."[]"?>" multiple="multiple">
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
							<option <?php if(in_array($selectkey, (array)$mulcheck)){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
					<?php  } ?>
				</select>	
            <?php }else{  //update time show signle select ?>
              	<select class="form-select js-example-basic-single" id="kt_select2_3<?php echo $mutiselet; ?>" name="<?php echo $key?>">
					<option value=""> Select Any one <?php echo $value; ?></option>
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
							<option <?php if($selectkey==$mulcheck){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
					<?php  } ?>
				</select>
			<?php } }else{ //noraml show multiselect ?>	
				<select class="form-select js-example-basic-multiple" id="kt_select2_3<?php echo $mutiselet; ?>" name="<?php echo $key."[]"?>" multiple="multiple">
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
							<option <?php if(in_array($selectkey, (array)$mulcheck)){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
					<?php  } ?>
				</select>
			<?php } ?>	

			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 
	</div>
<?php $mutiselet++;
}else if($fullvalue['type']=='date_pick') {
		$date_pick[] = $key;
		 ?>	
		<div class="<?php echo $class; ?>" >
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
				<?php $date_pic = fromDate($role[$key]); ?>
<input class="form-control datepicker" type="text" value="<?php echo  $date_pic; ?>"  name="<?php echo $key; ?>" >
		</div>
<?php $dateid++; }else if($fullvalue['type']=='month_year') {
		$month = $role[$key]!="" ? $role[$key] : date('Y-m');?>
		<div class="<?php echo $class; ?>" >
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			 <div >
		     	<input class="form-control" type="month" value="<?php echo  $month; ?>"  name="<?php echo $key; ?>" id="example-month-input"/>
		    </div>
		</div>
<?php  }else if($fullvalue['type']=='time_pick') {
	
		$time_pic = $role[$key]!="" ? $role[$key] : date('h:i:s');
		 ?>	
		<div class="<?php echo $class; ?>" >
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
<input class="form-control" type="time" step="1" value="<?php echo  $time_pic; ?>"  name="<?php echo $key; ?>">
  <div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> </div> 

		</div>
<?php $dateid++; } else if($fullvalue['type']=='email'){ ?>

		<div  class="<?php echo $class; ?>" >
				<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div>
				<input class="form-control" id="kt_inputmask_9" type="text" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
			</div>
		</div>
<?php } else if($fullvalue['type']=='phone'){  $in_phone_fileds[] = $key;?> 
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div>
				<!-- <input class="form-control" id="kt_inputmask_3" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" /> -->
 <input type="text" class="form-control mob_no" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" data-mask="9999-999-999">
				<!-- <span class="form-text text-muted">Phone number mask: <code>(999) 999-9999</code></span> -->
			</div>
		</div>
<?php } else if($fullvalue['type']=='currency'){ $in_currency_fileds[] = $key; ?> 
		<div class="<?php echo $class; ?>">
			<label><?php   echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div>
			<!-- 	<input class="form-control" id="kt_inputmask_7" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" /> -->
<input type="text" class="form-control autonumber text-right" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>"  name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
				<!-- <span class="form-text text-muted">Phone number mask: <code>(999) 999-9999</code></span> -->
			</div>
		</div>
<?php } else if($fullvalue['type']=='number' || $fullvalue['type']=='number_type'){ ?> 
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div>
					<input class="form-control text-right" type="text"  id ="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]!="" ? $role[$key] :0; ?>" />
				<!-- <span class="form-text text-muted">Phone number mask: <code>(999) 999-9999</code></span> -->
			</div>
		</div>




<?php } else if($fullvalue['type']=='rating'){ ?> 
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>

	<?php $selectdata = dyanamicSelectDataTablenameAllFileds($table_name,$key);
	$limitmax = $selectdata['value_field']==""?'10':$selectdata['value_field']; ?>	
   <div class="stars stars-example-fontawesome-o mt-3">
        <select class="rating_star example-fontawesome-o<?php echo $rattig; ?>" name="rating" onchange="rattingchanges(this.value,1)"  data-current-rating="<?php echo $role[$key]!="" ? $role[$key] :0; ?>"  autocomplete="off"  data-id="<?php echo $rattig; ?>">
            <option value="" label="0"></option>
          <?php for($mj=1;$mj <= $limitmax;$mj++){ ?>
				<option value="<?php echo $mj; ?>" ><?php echo $mj; ?></option>
            
            <?php } ?>
        </select>
    	<input type="hidden" class="form-control m-l-10"  name="<?php echo $key; ?>" id="star_value_input<?php echo $rattig; ?>"  value="<?php echo $role[$key]!="" ? $role[$key] :0; ?>"/>
</div>
		</div>
<?php $rattig++; } else if($fullvalue['type']=='password'){ ?> 
	
		<div class="<?php echo $class; ?>">
			<label><?php echo $value.$key; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="input-group input-icon-right">
				<input class="form-control"  type="Password" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
				<div class="input-group-append"><span class="input-group-text" ><span><i toggle="#password-field" id= "<?php echo $key; ?>" onclick="eywview(this.id)" class="la la-eye"></i></span></span></div>
			</div>
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please enter <?php echo $value; ?> </div> 
		</div>	
<?php } else if($fullvalue['type']=='color_pick'){ ?>
		<div class="<?php echo $class; ?>">
			<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
			<div class="input-group">
		<!-- 		<div class="input-group-prepend"><span class="input-group-text"><i class="la la-paint-brush" aria-hidden="true"></i></span></div>
				<input class="form-control jscolor" type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>"> -->

		 <input type="text" id="text-field<?php echo $key; ?>"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" class="form-control demo colorpickertcustom" value="#70c24a">
		 	</div>

		</div>
<?php } else if($fullvalue['type']=='image'){ $bimage = $role[$key]; $bimage = $bimage!="" ?'uploads/all/'.$bimage: 'uploads/all/no-image.jpg';  ?>
		 <div class="<?php echo $class; ?>">
                 <label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
                <div class="col-lg-9 col-xl-6 mt-2">
                    <!-- <div class="image-input image-input-outline" id="kt_image_6<?php echo $img; ?>">
                        <div class="image-input-wrapper" style="background-image: url(<?php echo $bimage ; ?>)"></div>

						<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                            <i class="fa fa-pen icon-sm text-muted"></i>
                            <input type="file" name="<?php echo $key; ?>" id="<?php echo $key; ?>" accept=".png, .jpg, .jpeg">
						<input type="hidden" name="<?php echo "hid".$key; ?>" value="<?php echo $role[$key]; ?>">
                        </label>

						<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                            <i class="ki ki-bold-close icon-xs text-muted"></i>
                        </span>
                    </div> -->

<div class="col-lg-6">
  <div class="profile-pic">
    <label class="-label" for="<?php echo $key; ?>">
      
      <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
    </label>
    <input  type="file" name="<?php echo $key; ?>" id="<?php echo $key; ?>" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
    	<input type="hidden" name="<?php echo "hid".$key; ?>" value="<?php echo $role[$key]; ?>">
                      
    <img src="<?php echo $bimage ; ?>" id="output<?php echo $key; ?>" width="200"  />
  </div>
</div>


                </div>
            </div>		
<?php $img++; }else if($fullvalue['type']=='file_upload'){  ?>

						<div class="<?php echo $class; ?>">
   						  <label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?></label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $role[$key]; ?>">
                                        </div>
                                    </div>	
<?php $img++; } else if($fullvalue['type']=='editor'){ ?>
		<div class="<?php echo $class; ?>">
		<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?>
			</label>
			<!-- <textarea class="summernote" name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $role[$key];?></textarea> -->

                <textarea class="form-control textediters" name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $role[$key];?> 
                  

                </textarea>
                                                 

			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> 
	</div></div>
<?php } else if($fullvalue['type']=='slider'){ ?>
		<div class="<?php echo $class; ?>">
		<label><?php echo $value; if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="requried">*</span><?php } ?>
			</label>
			<div>
				<div class="col-4 float-left">
					<input type="text" class="form-control" name="<?php echo $key; ?>" id="kt_nouislider_<?php echo $slider; ?>_input"   />
				</div>
				<div class="col-8 float-left">
					<div id="kt_nouislider_<?php echo $slider; ?>" class="nouislider nouislider-handle-primary"></div>
				</div>
			</div>
			<div style="display: none;color: red;" id="<?php echo $key.'_err'; ?>"> Please Select <?php echo $value; ?> 
	</div></div>
<?php $slider++; }else if($fullvalue['type']=='hidden'){ 
        //echo $key."//".$role[$key]; print_r($role);
	    $hidden_key = $role[$key] !="" ? $role[$key] : $_SEESSION[$key];
		
	?>
	<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $hidden_key; ?>" >
<?php } ?>

	<?php if($after == 1){?> 
		<?php $afterdata = dyanamicafterdata($table_name,$key);
		//print_r($afterdata); 
		if($afterdata['display'] != 'list'){
		$new_class = "form-group col-lg-".$afterdata['width']; ?>
		<div class="<?php echo $new_class; ?>">
		<label><?php echo $afterdata['lable'];  ?> &nbsp;</label>
		<div class="kt-input-icon">
			<?php if($afterdata['value'] !=""){ ?>
				<!-- <input type="text" name="" class="form-control" value="<?php echo urldecode($afterdata['value']); ?>"> -->
				<label style="text-align: left;"><?php echo  urldecode($afterdata['value']);  ?> </label>
			<?php } ?>
		</div>
	</div>
	<?php } } ?>					
	<?php } 
if($list['file_upload']=='0' && $rid!=""){ // rohan changes old check $list['file_upload']==0
	$ridss = explode(':',$rid);
	$transation_id = $ridss['1'];
	fileUplodingUi(['application_table_name'=>$table_name,'transaction_id'=>$transation_id ,'is_active'=>'0','width'=>'6','deletewidth'=>'6','deleteview'=>'1','file_size'=>'597152','file_limit'=>'2']); 
}
if($diskey != 'normal'){?>
								</div>
							</div>
						 </div>
	                </div>
	            </div>
        	</div>
    	</div>
   	
	 <?php }elseif(isset($disarray['right']) || isset($disarray['left'])){?>  </div> <?php } $lle++; }?>