<?php 
if(isset($_POST['process_name']) AND $_POST['process_name'] !=""){
	include_once('../includes/session.php');
	include_once('../dbconfig.php');
	include_once('../comm.php');
	$process_name = $_POST['process_name'];
	$rid = $_POST['rid'];
	$url = API."?mode=getapplicationserialnumbers&process_name=".$process_name."&rid=".$rid;
	$responseres = callAPI($url);
	$result = json_decode($responseres,1);
	//echo "<pre>"; print_r($asnres); echo "</pre>";
	$application_pages = $result['application_pages'];
	$application_serial_numbers_list = $result['application_serial_numbers_list'];
	$asnres = $result['application_serial_numbers'];
	$rid = $asnres['id'];
	//echo "<pre>"; print_r($asnres); echo "</pre>";
}
$staticpoint = array(1,2,3,4,5,6,7,8,9,10);



function serialNumberPreview($data) {
    // Extract values from array (simulate form inputs)
    $preFix      = $data['pre_fix'] ?? '';
    $postFix     = $data['post_fix'] ?? '';
    $serial      = $data['serial_number'] ?? '';
    $numLength   = !empty($data['doc_no_length']) ? (int)$data['doc_no_length'] : strlen($serial);
    $sep         = $data['separator_symbols'] ?? '';
    $fy          = $data['financial_year'] ?? '';

    $serial = str_pad((string)$serial, $numLength, '0', STR_PAD_LEFT);
    $pre_fix_position       = $data['pre_fix_position'] ?? 0;
    $post_fix_position      = $data['post_fix_position'] ?? 0;
    $serial_number_position = $data['serial_number_position'] ?? 0;
    $financial_year_position= $data['financial_year_position'] ?? 0;

    $pre_fix_check          = $data['pre_fix_check'] == "Y"?true:false;
    $post_fix_check         = $data['post_fix_check'] == "Y"?true:false;
    $financial_year_check   = $data['financial_year_check'] == "Y"?true:false;

    $pre_fix_type = $data['pre_fix_type'];
    $post_fix_type = $data['post_fix_type'];
    // Build output in order
    $parts = [];
    for ($m = 1; $m <= 4; $m++) {
        if ($pre_fix_check && $pre_fix_position == $m) {
            if($pre_fix_type == "STATIC" && !empty($preFix)){
            	$parts[] = $preFix;
        	}elseif($pre_fix_type == "DYNAMIC"){
        		$parts[] = 'FIELDVALUE';
        	}
        }
        if (!empty($serial) && $serial_number_position == $m) {
            $parts[] = $serial;
        }
        if ($post_fix_check && $post_fix_position == $m) {
        	if($post_fix_type == "STATIC" && !empty($postFix)){
            	$parts[] = $postFix;
        	}elseif($post_fix_type == "DYNAMIC"){
        		$parts[] = 'FIELDVALUE';
        	}
        }
        if (!empty($fy) && $financial_year_check && $financial_year_position == $m) {
            $parts[] = $fy;
        }
    }

    return implode($sep, (array)$parts);
}

?>
<script>
	
// function preFixTypeChange(){
// 	var pre_fix_type = $("#pre_fix_type").val();
// 	if(pre_fix_type == "STATIC"){
// 		$("#pre_fix_type_static").show();
// 		$("#pre_fix_type_dynamic").hide();
// 	}else{
// 		$("#pre_fix_type_dynamic").show();
// 		$("#pre_fix_type_static").hide();
// 	}
// }







function selectDisplayType(seq){
	var display_type = $("#display_type_"+seq).val();
	if(display_type == "STATIC"){
		$("#static_value_div_"+seq).show();
		$("#dynamic_value_div_"+seq).hide();
		
	}else{
		$("#static_value_div_"+seq).hide();
		$("#dynamic_value_div_"+seq).show();
	}
	serialNumberPreview();
}

function removeDiv(divid){
	$('#'+divid).remove();
	serialNumberPreview();

}
var seqrow = 1000;
function addMoreAdditionalSettings(){
	seqrow = seqrow+1;
	var htmlview = "";
	
	let selected = [];

	$(".position_dropdown option:selected").each(function() {
	    selected.push(parseInt($(this).val()));

	});
	const optionsList = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
	let firstAvailable = optionsList.find(v => !selected.includes(parseInt(v)));
	console.log("------------"+firstAvailable);
	var htmlviewoption = "";
	optionsList.forEach(function(value) {
		var  isSelected = "";
	    if (value == firstAvailable) {
	        isSelected = "selected";
	    }

	    htmlviewoption +=`<option value="`+value+`" `+isSelected+`>`+value+`- Position</option>`;
	});


	htmlview +=`<div class="row" id="additional_rowid_`+seqrow+`">`;
					htmlview +=`<div class="col-lg-3 " >
					<input type="hidden" class="form-control columns_sequince"  value="`+seqrow+`" >
						<select class ="form-control form-control-lg form-select js-example-basic-single " name="additional_settings[display_type][]" id="display_type_`+seqrow+`" onchange="selectDisplayType('`+seqrow+`')"> 
							<option value="STATIC" <?php echo $asettings['display_type'] =="STATIC"?"selected":""; ?>>Static</option>
							<option value="DYNAMIC" <?php echo $asettings['display_type'] =="DYNAMIC"?"selected":""; ?>>Dynamic</option>
				        </select>
				    </div>`;
				    
				    htmlview +=`<div class="form-group col-lg-3" id="static_value_div_`+seqrow+`" >
				    <span class="text-danger empty_error" id="error_static_value_`+seqrow+`"></span>

						<input type="text" class="form-control" name="additional_settings[static_value][]" id="static_value_`+seqrow+`" value="<?php echo $asettings['static_value']; ?>" onblur="serialNumberPreview()">
					</div>`;

					htmlview +=`<div class="form-group col-lg-3 w-full" id="dynamic_value_div_`+seqrow+`" style="display: none;" >
					<span class="text-danger empty_error" id="error_dynamic_value_`+seqrow+`"></span>
						<select class ="form-control form-control-lg form-select js-example-basic-single" id="dynamic_value_`+seqrow+`" name="additional_settings[dynamic_value][]" onchange="serialNumberPreview()"> 
							<option value="PARTY_NAME" >Party</option>
							<option value="TRANSACTION_TYPE" >Document Type</option>
							<option value="TRANSACTION_LOCATION" >Location</option>
							<option value="TRANSACTION_COST_CENTER">Cost Center</option>
						</select>
					</div>`;
					htmlview +=`<div class="form-group col-lg-3 ">
						<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="additional_settings[positions][]" id="positions_`+seqrow+`" onmousedown="storeOld(this)" onchange="positionChange(this)" >`;
					htmlview += htmlviewoption;
				    htmlview +=`</select>
					</div>`;

					htmlview +=`<div class="col-lg-3 text-center">
						<i class="ti-trash text-danger" onclick="removeDiv('additional_rowid_`+seqrow+`')"></i>
					</div>`;

			htmlview +=`</div>`;
			$("#addmorerow").append(htmlview);
			$(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
}

</script>
<?php /** ?>
<div class="col-lg-4">
	<div class="card">
		<div class="card-header">
	        <h5><?php echo ucwords(str_replace("_"," ",($process_name))); ?> List</h5>
	        <div class="card-header-right">
	        </div>
	    </div>
		<div class="card-body">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Finance Year</th>
						<th>Format</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($application_serial_numbers_list as $lst){ ?>
					<tr class="">
						<th class="p-2 w-b-w-s">
						<a href="#!" class="text-primary" data-bs-toggle="tooltip" data-placement="bottom" title="" d;ata-bs-original-title="Please Click To Open Owner Details" onclick="selectProcessName('<?php echo $process_name; ?>','<?php echo $lst['id']; ?>')"><?php echo date("M-Y", strtotime($lst['from_date']))."-".date("M-Y", strtotime($lst['to_date'])); ?></a> 
						</th>
						<th class="p-2 w-b-w-s"></th>
					</tr>
					<?php } ?>
				</tbody>	
			</table>
		</div>
	</div>
</div>
<?php **/ ?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-header">
	        <h5><?php echo ucwords(str_replace("_"," ",($process_name))); ?></h5>
	        <div class="card-header-right"></div>
	    </div>
		<div class="card-body">
			<form method="post" id="serial_number_form">
				<input type="hidden" class="form-control" name="rid" id="application_serial_numbers_id" value="<?php echo $rid; ?>">
				<input type="hidden" class="form-control" name="process_name" value="<?php echo $process_name; ?>">
				<div class="row">
					<div class="form-group col-lg-12">
						<label class="form-label d-block">Output</label>
						<span id="serialnumberoutput"> <?php echo serialNumberPreview($asnres);  ?>	</span>
					</div>

					<div class="form-group col-lg-3">
						<label class="form-label d-block">Number Length</label>
						<span class="text-danger empty_error" id="error_doc_no_length"></span>
						<input type="number" class="form-control" name="doc_no_length" id="doc_no_length" value="<?php echo $asnres['doc_no_length']!=""?$asnres['doc_no_length']:3; ?>" onblur="serialNumberPreview()">
					</div>
					<div class="form-group col-lg-3">
						<label class="form-label d-block">separator symbols</label>
						<select class ="form-control form-control-lg form-select js-example-basic-single" id="separator_symbols" name="separator_symbols"  onchange="serialNumberPreview()"> 
							<option value="-" <?php echo $asnres['separator_symbols'] =="-"?"selected":""; ?>>-</option>
							<option value="/" <?php echo $asnres['separator_symbols'] =="/"?"selected":""; ?>>/</option>
							<option value="_" <?php echo $asnres['separator_symbols'] =="_"?"selected":""; ?>>_</option>
						</select>
					</div>

					<div class="form-group col-lg-3">
						<label class="form-label d-block">Start Serial Number</label>

						<input type="text" class="form-control  mt-2" name="serial_number" id="serial_number"  value="<?php echo $asnres['serial_number']!=""?$asnres['serial_number']:1; ?>" onblur="serialNumberPreview()" readonly>

						<div class="form-group  mt-2">
							<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="serial_number_position"  id="serial_number_position" onmousedown="storeOld(this)" onchange="positionChange(this)" > 
								<?php foreach($staticpoint as $i){ ?>
					                <option value="<?php echo $i; ?>" <?php if($asnres['serial_number_position'] ==$i){ echo "selected"; }elseif($asnres['serial_number_position'] == "" AND $i=='1'){ echo "selected"; } ?>>
					                <?php echo $i."- Position"; ?>
					                </option>
					          <?php } ?> 
					        </select>
						</div>

					</div>
					<div class="form-group col-lg-3">
						<label class="form-label d-block">Financial Year</label>
						<input type="checkbox" class="js-switch-finanaceyear" value="Y" name="financial_year_check" id="financial_year_check"  onchange="financeYearonChange()" <?php echo ($asnres['financial_year_check'] =="" || $asnres['financial_year_check'] =="Y")?"checked":""; ?> readonly>

						<div class="form-group mt-2 financial_year_check_css <?php echo $asnres['financial_year_check'] =="N"?"event_disabled":""; ?>">
							<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="financial_year_position"  id="financial_year_position" onchange="positionChange(this)"> 
								<?php foreach($staticpoint as $i){ ?>
					                <option value="<?php echo $i; ?>" <?php if($asnres['financial_year_position'] ==$i){ echo "selected"; }elseif($asnres['financial_year_position'] == "" AND  $i=='2'){ echo "selected"; } ?>>
					                <?php echo $i."- Position"; ?>
					                </option>
					          <?php } ?> 
					        </select>
						</div>

					</div>

				</div>
				<div class="row">
					<div class="form-group col-lg-3">
						<label class="form-label d-block">Reset Serial Number Type</label>
						<select class ="form-control form-control-lg form-select js-example-basic-single" name="reset_type"  > 
							<option value="MT" <?php echo $asnres['reset_type'] =="MT"?"selected":""; ?>>Monthly Reset</option>
							<option value="YR" <?php echo $asnres['reset_type'] =="YR"?"selected":""; ?>>Yearly Reset</option>
							<option value="LT" <?php echo $asnres['reset_type'] =="LT"?"selected":""; ?>>Life Time Same Serial Number</option>
						</select>
					</div>
				</div>
					
				<div class="card">
					<div class="card-header">
				        <h5>Additional Settings</h5>
				        <div class="card-header-right"></div>
				    </div>	
				    <div class="card-body">
				    		<div class="row">
								<div class="col-lg-3">
									<label class="form-label d-block">Display Type</label>
								</div>
								<div class="col-lg-3">
									<label class="form-label d-block">Value/Field</label>
								</div>
								<div class="col-lg-3 text-left">
									<label class="form-label d-block">Position</label>
								</div>
								<div class="col-lg-3">
								
								</div>
							</div>
							<div id="addmorerow">
						<?php 
						$additional_settings = json_decode($asnres['additional_settings'],1);
						if(count((array)$additional_settings) ==0){
							$additional_settings[] = array();
						}
						$seq = 1;
						//echo "<pre>"; print_r($additional_settings); echo "</pre>";
						foreach($additional_settings as $asettings){ ?>	
							<div class="row" id="additional_rowid_<?php echo $seq; ?>">
								<div class="col-lg-3 " >
									<input type="hidden" class="form-control columns_sequince"  value="<?php echo $seq; ?>" >
									<select class ="form-control form-control-lg form-select js-example-basic-single " name="additional_settings[display_type][]" id="display_type_<?php echo $seq; ?>" onchange="selectDisplayType('<?php echo $seq; ?>')"> 
										<option value="STATIC" <?php echo $asettings['display_type'] =="STATIC"?"selected":""; ?>>Static</option>
										<option value="DYNAMIC" <?php echo $asettings['display_type'] =="DYNAMIC"?"selected":""; ?>>Dynamic</option>
							        </select>
							    </div>
							    
							    <div class="form-group col-lg-3" id="static_value_div_<?php echo $seq; ?>" <?php if($asettings['display_type']=="DYNAMIC"){ ?>style="display: none;" <?php } ?>>
							    	<span class="text-danger empty_error" id="error_static_value_<?php echo $seq; ?>"></span>
									<input type="text" class="form-control" name="additional_settings[static_value][]" id="static_value_<?php echo $seq; ?>" value="<?php echo $asettings['static_value']; ?>" onblur="serialNumberPreview()">
								</div>

								<div class="form-group col-lg-3 w-full" id="dynamic_value_div_<?php echo $seq; ?>" <?php if($asettings['display_type']=="STATIC" OR $asettings['display_type']==""){ ?>style="display: none;" <?php } ?>>
									<span class="text-danger empty_error" id="error_dynamic_value_<?php echo $seq; ?>"></span>
									<select class ="form-control form-control-lg form-select js-example-basic-single" id="dynamic_value_<?php echo $seq; ?>" name="additional_settings[dynamic_value][]" onchange="serialNumberPreview()"> 
										<option value="PARTY_NAME" <?php echo $asettings['dynamic_value'] =="PARTY_NAME"?"selected":""; ?>>Party</option>
										<option value="TRANSACTION_TYPE" <?php echo $asettings['dynamic_value'] =="TRANSACTION_TYPE"?"selected":""; ?>>Document Type</option>
										<option value="TRANSACTION_LOCATION" <?php echo $asettings['dynamic_value'] =="TRANSACTION_LOCATION"?"selected":""; ?>>Location</option>
										<option value="TRANSACTION_COST_CENTER" <?php echo $asettings['dynamic_value'] =="TRANSACTION_COST_CENTER"?"selected":""; ?>>Cost Center</option>
									</select>
								</div>
								<div class="form-group col-lg-3 ">
									<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="additional_settings[positions][]" id="positions_<?php echo $seq; ?>"  onchange="positionChange(this)"> 
										<?php foreach($staticpoint as $i){ ?>
							                <option value="<?php echo $i; ?>" <?php if($asettings['positions'] ==$i){ echo "selected"; }elseif($asettings['positions'] == "" AND  $i=='3'){ echo "selected"; } ?> >
							                <?php echo $i."- Position"; ?>
							                </option>
							          <?php } ?> 
							        </select>
								</div>

								<div class="col-lg-3 text-center">
									<i class="ti-trash text-danger" onclick="removeDiv('additional_rowid_<?php echo $seq; ?>')"></i>
								</div>

						</div>


					<?php $seq++; } ?>
				</div>
						<div class="row">
							<div class="col-lg-3 ">
								<button class="btn btn-info" type="button" onclick="addMoreAdditionalSettings()"><i class="icofont icofont-plus icon-white "></i> Add More</button>
							</div>
						</div>
					</div>
				</div>
					
					


					<?php /** ?> 
					<div class="form-group col-lg-3">
						<label class="form-label d-block">Post Fix</label>
						<div class="row">
							<div class="col-lg-4">
								<input type="checkbox" class="js-switch-finanaceyear" value="Y" name="post_fix_check" id="post_fix_check" onchange="postFixonChange()" <?php echo ($asnres['post_fix_check'] =="Y" || $asnres['post_fix_check'] =="")?"checked":""; ?>>
							</div>
							<div class="col-lg-8 post_fix_check_css <?php echo $asnres['post_fix_check'] =="N"?"event_disabled":""; ?>" >
								<select class ="form-control form-control-lg form-select js-example-basic-single " name="post_fix_type" id="post_fix_type" onchange="postFixTypeChange()"> 
									<option value="STATIC" <?php echo $asnres['post_fix_type'] =="STATIC"?"selected":""; ?>>Static</option>
									<option value="DYNAMIC" <?php echo $asnres['post_fix_type'] =="DYNAMIC"?"selected":""; ?>>Dynamic</option>
						        </select>
						    </div>
						</div> 
					</div>
					<div class="form-group col-lg-3 pre_fix_check_css <?php echo $asnres['pre_fix_check'] =="N"?"event_disabled":""; ?>" id="pre_fix_type_static" <?php if($asnres['pre_fix_type']=="DYNAMIC"){ ?>style="display: none;" <?php } ?>>
						<input type="text" class="form-control" name="pre_fix" id="pre_fix" value="<?php echo $asnres['pre_fix']; ?>" onblur="serialNumberPreview()">
					</div>
					<div class="form-group col-lg-3 w-full pre_fix_check_css <?php echo $asnres['pre_fix_check'] =="N"?"event_disabled":""; ?>" id="pre_fix_type_dynamic" <?php if($asnres['pre_fix_type'] =="" || $asnres['pre_fix_type']=="STATIC"){ ?>style="display: none;" <?php } ?>>
						<select class ="form-control form-control-lg form-select js-example-basic-single" id="pre_fix_field" name="pre_fix_field" > 
							<option value="party" <?php echo $asnres['pre_fix_field'] =="party"?"selected":""; ?>>Party</option>
							<option value="type" <?php echo $asnres['pre_fix_field'] =="type"?"selected":""; ?>>Document Type</option>
							<option value="location" <?php echo $asnres['pre_fix_field'] =="location"?"selected":""; ?>>Location</option>
							<option value="cost_center" <?php echo $asnres['pre_fix_field'] =="cost_center_id"?"selected":""; ?>>Cost Center</option>
						</select>
					</div>
					

					<div class="form-group col-lg-3 post_fix_check_css <?php echo $asnres['post_fix_check'] =="N"?"event_disabled":""; ?>" id="post_fix_type_static"  <?php if($asnres['post_fix_type']=="DYNAMIC"){ ?>style="display: none;" <?php } ?>>
						<input type="text" class="form-control"  name="post_fix" id="post_fix" value="<?php echo $asnres['post_fix']; ?>" onblur="serialNumberPreview()">
					</div>
					<div class="form-group col-lg-3 w-full post_fix_check_css <?php echo $asnres['post_fix_check'] =="N"?"event_disabled":""; ?>" id="post_fix_type_dynamic" <?php if($asnres['post_fix_type'] =="" || $asnres['post_fix_type']=="STATIC"){ ?>style="display: none;" <?php } ?>>
						
						<select class ="form-control form-control-lg form-select js-example-basic-single" id="post_fix_field" name="post_fix_field" > 
							<option value="party" <?php echo $asnres['post_fix_field'] =="party"?"selected":""; ?>>Party</option>
							<option value="type" <?php echo $asnres['post_fix_field'] =="type"?"selected":""; ?>>Document Type</option>
							<option value="location" <?php echo $asnres['post_fix_field'] =="location"?"selected":""; ?>>Location</option>
							<option value="cost_center" <?php echo $asnres['post_fix_field'] =="cost_center_id"?"selected":""; ?>>Cost Center</option>
						</select>

					</div>
					


					

					<div class="form-group col-lg-3 pre_fix_check_css <?php echo $asnres['pre_fix_check'] =="N"?"event_disabled":""; ?>">
						<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="pre_fix_position" id="pre_fix_position" onchange="positionChange(this)" > 
							<?php foreach($staticpoint as $i){ ?>
				                <option value="<?php echo $i; ?>" <?php if($asnres['pre_fix_position'] ==$i){ echo "selected"; }elseif($asnres['pre_fix_position'] == "" AND $i=='1'){ echo "selected"; } ?>>
				                <?php echo $i."- Position"; ?>
				                </option>
				          <?php } ?> 
				        </select>
					</div>
					<div class="form-group col-lg-3 post_fix_check_css <?php echo $asnres['post_fix_check'] =="N"?"event_disabled":""; ?>" >
						<select class ="form-control form-control-lg form-select js-example-basic-single position_dropdown" name="post_fix_position" id="post_fix_position" onchange="positionChange(this)" > 
							<?php foreach($staticpoint as $i){ ?>
				                <option value="<?php echo $i; ?>" <?php if($asnres['post_fix_position'] == $i){ echo "selected"; }elseif($asnres['post_fix_position'] == "" AND $i=='2'){ echo "selected"; } ?>>
				                <?php echo $i."- Position"; ?>
				                </option>
				          <?php } ?> 
				        </select>
					</div> 
					<?php **/ ?>
					

		
			</form>
			
		</div>
		<div class="card-footer">
		    <div class="row">
		        <div class="col-lg-12">
		            <button class="btn btn-primary" onclick="formSubmitBTN()"><?php echo $rid ==""?"Submit":"Update"; ?></button>
		        </div> 
		    </div>
		</div>
	</div>
</div>