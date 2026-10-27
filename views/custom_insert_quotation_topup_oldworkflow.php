<?php 
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table_name;
$page_response = callAPI($pageurl);
$ddlist = json_decode($page_response,1); 
$content = $ddlist['help_text'] != '' ? $ddlist['help_text'] : "Help Text"; 
$file_data = json_decode($ddlist['file_upload'],1);
$ddlists = json_decode($ddlist['list_attributes'],1);
$accord = json_decode($ddlist['additional_attributes'],1);
$image = array(); $insert_arr = array(); $add_insert_arr = array(); $right = array(); $left = array(); $normal = array();
foreach ($ddlists as $key => $value) {
	if($value['insert_display_list']== 1 ){ 
		if($value['accordions'] == 'L'){
			$left [$value['display_postion']]['lable'] = $value['lable'];
			$left [$value['display_postion']]['type'] = $value['type'];
			$left [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
			$left [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
			$left [$value['display_postion']]['table'] = $key; 
		}else if($value['accordions'] == 'R'){
			$right [$value['display_postion']]['lable'] = $value['lable'];
			$right [$value['display_postion']]['type'] = $value['type'];
			$right [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
			$right [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
			$right [$value['display_postion']]['table'] = $key;
		}else{
			$dis [$value['display_postion']]['lable'] = $value['lable'];
			$dis [$value['display_postion']]['type'] = $value['type'];
			$dis [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
			$dis [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
			$dis [$value['display_postion']]['table'] = $key;
		}
		

		if($value['mandatory']==1){
			$mandatory[]=$key;
		}
		if($value['type']=='image'){
			$image[] = $key;
		}
		if($value['add_exter'] != 1){
			$insert_arr[]=$key;
		}else{
			$add_insert_arr[]=$key;
		}	

		$dis_label[$key] = $value['lable'];
 	}
}
 
if(count((array)$dis) > 0){ ksort($dis);  $disarray['normal']=$dis; }
if(count((array)$right) > 0){ ksort($right);  $disarray['right']=$right; }
if(count((array)$left) > 0){ ksort($left);  $disarray['left']=$left; }
$add_mandatory_str = implode(',', (array)$add_insert_arr);
$role = $table1;
//echo "<pre>"; print_r($role); echo "</pre>";//exit;

?>
<script type="text/javascript">
	function selectchange(field, key, table){
		//alert("ravi");
		var option = '<?php echo $_GET['option']; ?>';
		var main_table = document.getElementById("status_values").value;
		if(key == '0'){
           // alert('good');
            document.getElementById('customer_name').value="";
            document.getElementById('customer_email').value="";
            document.getElementById('customer_phone').value="";
            document.getElementById('customer_tax_number').value="";
            document.getElementById('customer_address').value="";
            document.getElementById('customer_ship_adderss').value="";
            document.getElementById('cost_center').value="";
            document.getElementById('customer_payment_terms').value="0";
        }else{
			if(field == 'customer_id'){
				if(table == 'application_users'){ //alert("T");
					$.ajax({
				        url: "ajax.php",
				        data:'page=userDetails&user_id='+key,
				        type: "POST",
				        success:function(data){
				            console.log(data);
				            var obj = $.parseJSON(data);
				            //alert(obj.payment_terms);
				            document.getElementById('customer_name').value=obj.display_name;
				            document.getElementById('customer_email').value=obj.email;
				            document.getElementById('customer_phone').value=obj.phone;
				            document.getElementById('customer_tax_number').value=obj.gstin;
				            document.getElementById('currency_id').value=obj.currency_id;
				            document.getElementById('cost_center').value=obj.cost_center;
				            document.getElementById('customer_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
				            document.getElementById('customer_ship_adderss').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
				            document.getElementById("customer_payment_terms").value = obj.payment_terms;

				            duedate();
				        },
				        error:function (){}
				     })
				}
				if(table == 'vendors'){ //alert("V");
					$.ajax({
				        url: "ajax.php",
				        data:'page=vendor&ven_id='+key,
				        type: "POST",
				        success:function(data){
				            console.log(data);
				            var obj = $.parseJSON(data);
				            document.getElementById('customer_name').value=obj.display_name;
				            document.getElementById('customer_email').value=obj.email;
				            document.getElementById('customer_phone').value=obj.phone;
				            document.getElementById('customer_tax_number').value=obj.gstin;
				            document.getElementById('currency_id').value=obj.currency_id;
				            document.getElementById('customer_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
				            document.getElementById('customer_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
				            document.getElementById("customer_payment_terms").value = obj.payment_terms;
				        },
				        error:function (){}
				    });	
				}
				if(table == 'customers'){ //alert("V");
					$.ajax({
				        url: "ajax.php",
				        data:'page=bill_customer&cus_id='+key,
				        type: "POST",
				        success:function(data){
				            console.log(data);
				            var obj = $.parseJSON(data);
				            document.getElementById('customer_name').value=obj.display_name;
				            document.getElementById('customer_email').value=obj.email;
				            document.getElementById('customer_phone').value=obj.phone;
				            document.getElementById('customer_tax_number').value=obj.gstin;
				            if(obj.currency_id !=null){
				           		document.getElementById('currency_id').value=obj.currency_id;
				            }
				            //document.getElementById('currency_id').value=obj.currency_id;
				       		document.getElementById('customer_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
				            document.getElementById('customer_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
				            if(obj.payment_terms !=null){
				            	document.getElementById("customer_payment_terms").value = obj.payment_terms;
				            	//$("#customer_payment_terms").select2("val", obj.payment_terms);
				            	//$("#customer_payment_terms").select2().select2('val', obj.payment_terms);
				        	}
				        },
				        error:function (){}
				    });	
				}
				if(main_table == 'sales_invoices'){ 
					$.ajax({
				        url: "ajax.php",
				        data:'page=dynmictabledata&table=sales_orders&field=id&value=transaction_number&rid=customer_id&fid='+key,
				        type: "POST",
				        success:function(data){
				            console.log(data);
				            $("#grade").html("<select class='form-control' name='pay_template'>"+data+"</select>");
				        },
				        error:function (){}
				    });	
				}
			}
			if(field == 'ref_no'){
				//if(table == 'application_users'){ 
						window.location.href = 'index.php?option='+option+'&table='+table+'&Rid='+key;
				//}
			}
		}
	}
</script>
<div class="card-body">
	<?php //echo "<pre>"; print_r($disarray); echo "</pre>";  ?>
	<input type="hidden" name="" value="<?php echo $table_name; ?> " id="ajax_table_name">
	<?php $lle=0; $dateid=1;
	foreach ($disarray as $diskey => $disvalue) {
	if($diskey != 'normal'){ if($diskey == 'right' || !isset($disarray['right'])){ ?>
	<div class="form-group row">
	<?php } ?>
    	<div class="col-md-6 col-12 mrgn-btm">
	        <div class="col-md-10" id="accordionExample6"> 
	            <div class="card">
	                <div class="card-header" id="headingTwo6" style="padding: 10px !important;">
	                    <div class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseTwo6<?php echo $diskey; ?>"  aria-expanded="true" aria-controls="collapseTwo6<?php echo $diskey; ?>" style="padding: 5px;border-top: 0px;">
	                        <!-- <i class="flaticon2-notification"></i> -->
	                         <?php if($accord[$diskey] !=""){ echo  $accord[$diskey]; } else{ echo "Additional Information-".$lle;} ?>
	                         <b class="f-24 float-right">+</b>
	                    </div>
	                </div>
	                <div id="collapseTwo6<?php echo $diskey; ?>" class="collapse" aria-labelledby="headingTwo6" data-parent="#accordionExample6">
	                    <div class="card-body">
	                    	<!-- <div class="kt-portlet__body">
								<div class="form-group row "> -->
<?php	} $div=0; foreach ($disvalue as $key => $fullvalue) {  $key = $fullvalue['table'];?>
	<?php if( $diskey == 'normal' && $div%2==0){  ?>
	<div class="form-group row"> 
	<?php }if($diskey != 'normal'){ $class1 = "col-lg-4 col-sm-12 col-form-label "; 
		$class2 ="col-lg-8 col-md-8 col-sm-12";
	?><div class="form-group row">  <?php } else{  $class1 = "col-lg-2 col-form-label text-lg-right"; 
		$class2 ="col-lg-3";?> <?php } ?>	

		<?php if( $diskey == 'normal' && $div%2!=0){  ?>
		<div class="col-lg-1"></div>  
		<?php } ?>   
	    <label class="<?php echo $class1; ?>"><?php echo $fullvalue['lable']; ?></label>

	    <div class="<?php echo $class2; ?>">
	    	<?php if($fullvalue['type'] == 'text' || $fullvalue['type'] == 'read_only' || $fullvalue['type'] == 'serial_no' ){
	    	    $read_only=""; 
    	    	if($fullvalue['type'] == 'read_only'){ $read_only= "readonly"; }
    	    	if($fullvalue['type'] == 'serial_no'){
    	    	    $read_only= "readonly"; 
    	    	    if($role[$key]==""){ $role[$key] = dyanamicserialnumber($table_name); } 
    	    	}
	    	?>
	            <input  class="form-control"  type='text' <?php echo $read_only; ?> autocomplete="off" value='<?php  echo  $role[$key]; ?>' id="<?php  echo  $key; ?>" name="<?php echo $key?>"/>
	         <?php } else if($fullvalue['type']=='auto_select') { ?>
	         	<?php $selectdata = dyanamicselectdata($table_name,$key);
	         		if($key == 'customer_id'){ $select_table = dyanamicselectdatatablename($table_name,$key); }
	         		if($key == 'ref_no'){ $select_table = dyanamicselectdatatablename($table_name,$key); } ?>
<div class="w-full">	          	
<select class="form-select js-data-example-ajax-Comman-form  col-lg-4" <?php echo $read_only; ?>  data-tablename="<?php echo $table_name; ?>"  data-fieldsname="<?php echo $key; ?>" data-value="" data-pagename="comman_form" name="<?php echo $key?>">
        <option value="0"> Select Any one</option>
        <?php foreach ($selectdata as $selectkey => $selectvalue) {  if($selectkey == $role[$key] ){ ?> 
				<option  selected value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
		<?php  } } ?>
    </select>
</div>

	 		<?php }else if($fullvalue['type']=='select') { ?>
	    



	         	<?php $selectdata = dyanamicselectdata($table_name,$key);
	         		if($key == 'customer_id'){ $select_table = dyanamicselectdatatablename($table_name,$key); }
	         		if($key == 'ref_no'){ $select_table = dyanamicselectdatatablename($table_name,$key); } ?>
	          	<div class="w-full">
		          	<select class="form-select js-example-basic-single" onchange="selectchange(<?php echo "'".$key."'"; ?>,this.value,<?php echo "'".$select_table."'"; ?>)" id="kt_select2_4<?php echo $select; ?>"  name="<?php echo $key?>">
						<option value="0"> Select Any one </option>
						<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
								<option <?php if($selectkey == $role[$key] || ($selectkey == $_GET['Rid'] AND $key == 'ref_no')){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
						<?php  } ?>
					</select>
				</div>
			<?php $select++; } else if($fullvalue['type']=='radio') { ?>
				<?php $selectdata = dyanamicselectdata($table_name,$key); ?>
				<div class="col-9 mt-3">
				<div class="radio-inline">
				<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
				<label class="radio"> <input type="radio"  <?php if($selectkey == $role[$key]){ ?>checked <?php } ?> name="<?php echo $key; ?>" value='<?php echo $selectkey; ?>'>	<span></span><?php echo $selectvalue; ?></label>
				<?php } ?>
				</div>	
				</div>
			<?php }elseif($fullvalue['type']=='textarea') { ?>
				<textarea type="text" class="form-control" id="<?php echo $key; ?>" name="<?php echo $key; ?>" ><?php echo $role[$key];?></textarea>
			<?php }elseif($fullvalue['type']=='touchspin') { ?>
				<input id="kt_touchspin_1" type="text" class="form-control" value="1" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Select <?php echo $value; ?>">
			<?php }elseif($fullvalue['type']=='switch') { ?>
				<?php $selectdata = dyanamicselectdata($table_name,$key); 
				$k=1;
				//print_r($selectdata); 
				foreach ($selectdata as $selectkey => $selectvalue) { 
					if($k < 3){
						$one = $k==1 ? $selectvalue : $one;
						$sen = $k==2 ? $selectvalue : $sen;
					}else{
						break;
					}
				$k++; } ?>
				<span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
					<label>
						<input type="hidden"   name="<?php echo $key; ?>"  value= "<?php echo $one; ?>">
						<input type="checkbox"  value="<?php echo $sen; ?>"  name="<?php echo $key; ?>" <?php if($selectkey == $role[$key] ){ ?> checked <?php } ?> >
						<span></span>
					</label>
				</span>
			<?php } else if($fullvalue['type']=='checkbox') { ?>
				<div class="kt-checkbox-inline">
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
						<input type="checkbox"  name="<?php echo $key."[]"; ?>" value= "<?php echo $selectvalue; ?>" > <?php echo $selectvalue; ?>
						<span></span>
					</label>
					<?php } ?>
				</div>
			<?php } else if($fullvalue['type']=='multi_select') {  if(is_array($role[$key])){ $mulcheck = $role[$key]; }else{  $mulcheck = json_decode($role[$key],1); } ?>
			<div class="w-full">
				<select class="form-control kt-select2" id="kt_select2_3<?php echo $mutiselet; ?>" name="<?php echo $key."[]"?>" multiple="multiple">
					<option value=""> Select Any one <?php echo $value; ?></option>
					<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
							<option <?php if(in_array($selectkey, $mulcheck)){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
					<?php  } ?>
				</select>
			</div>	
			<?php $mutiselet++; } else if($fullvalue['type']=='date_pick') { $date_pick[] = $key; ?>	
				<div class="">
					<?php 
						$date_pic = $role[$key] !=''?  fromDate($role[$key]) : "";
						if($key=="transaction_date"){ $date_pic  = fromDate($date_pic); } 
					?>
					<input class="form-control datepicker" type="text" value="<?php echo  $date_pic; ?>" dateformat="d M y" id="kt_datepicker_3<?php echo $dateid; ?>"  name="<?php echo $key; ?>" >
					<!-- <input type="text" class="form-control"  value="<?php echo  date("m/d/Y",strtotime($date_pic)); ?>"  name="<?php echo $key; ?>"  id="kt_datepicker_3<?php echo $dateid; ?>" /> -->
					<!-- <div class="input-group-append">
						<span class="input-group-text">
							<i class="la la-calendar"></i>
						</span>
					</div> -->
				</div>
			<?php $dateid++; } else if($fullvalue['type']=='email'){ ?>
				<input class="form-control" id="kt_inputmask_9" id="<?php  echo  $key; ?>" type="text" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
			<?php } else if($fullvalue['type']=='phone'){ ?>
				<input class="form-control" id="kt_inputmask_3" type="text"  id="<?php  echo  $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
			<?php } else if($fullvalue['type']=='currency'){ ?> 
				<input class="form-control" id="kt_inputmask_7" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
			<?php } else if($fullvalue['type']=='number' || $fullvalue['type']=='number_type'){ ?>
				<input class="form-control" id="kt_inputmask_6" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
			<?php } else if($fullvalue['type']=='password'){ ?> 
				<div class="kt-input-icon">
					<input class="form-control"  type="Password" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
					<span class="kt-input-icon__icon kt-input-icon__icon--right" ><span><i toggle="#password-field" id= "<?php echo $key; ?>" onclick="eywview(this.id)" class="la la-eye"></i></span></span>
				</div>
			<?php } else if($fullvalue['type']=='color_pick'){ ?>
				<div class="input-group">
					<div class="input-group-prepend"><span class="input-group-text"><i class="la la-paint-brush" aria-hidden="true"></i></span></div>
					<input class="form-control jscolor" type="text"id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>">
				</div>
			<?php } ?>


	    </div>
	
	 <?php if($diskey == 'normal' && $div%2==1 ){ ?>   
	</div>
	<?php  }if($diskey == 'normal' && $div%2==0 && $div+1 == count((array)$disvalue)){ ?> </div> <?php } if($diskey != 'normal'){ ?> </div> <?php } ?>
<?php $div++; } if($diskey != 'normal'){?>
								<!-- </div>
							</div> -->
						 </div>
	                </div>
	            </div>
        	</div>
    	</div>
   	
	 <?php } $lle++; }?>

<input type='hidden'  name="company_id" id="company_id" class="form-control" readonly value="<?php echo $_SESSION['company_id']; ?>" />
<input type="hidden" name="location_id" value="<?php echo $table1['location_id']; ?>">

<input type='hidden' class="form-control" id="config_currency_id"  name=""  value="<?php echo $_SESSION['config']['default_currency_id']; ?>"/>
<input type="hidden" name="old_place" id="old_place" value="<?php echo $location_state;?>">
<input type="hidden" name="add_mandatory_str" value="<?php echo $add_mandatory_str; ?>">
<?php  if($table1['transaction_date'] !=""){ $transaction_date = $table1['transaction_date'];  }else{ $transaction_date = date('Y-m-d'); }  ?>

