<?php
$_SESSION['apply_js']['multiselest'] = '1';
$url = API."?mode=getapplicationserialnumbers";
$responseres = callAPI($url);
$result = json_decode($responseres,1);
//echo "<pre>"; print_r($result); echo "</pre>";
$dynamic_mapping_tables = $result['dynamic_mapping_tables'];

?>
<script>
	function selectProcessName(process_name, rid=""){
		var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    	$("#transaction_serial_numbers_templates").html(loader);
		jQuery.ajax({
			url: "views/custom_insert_application_serial_number_ajax.php",
			data:'process_name='+process_name+'&rid='+rid,
			type: "POST",
			success:function(data){
				$("#transaction_serial_numbers_templates").html(data);
				$(".js-example-basic-single").select2();
				var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-finanaceyear'));
				elem.forEach(function(html) {
					var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
				});
			},
			error:function (){}
		});	
	}




const optionsList = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"]; // All possible numbers
let lastValues = new WeakMap(); // store last value per dropdown

function positionChange(select) {
  const allSelects = document.querySelectorAll(".position_dropdown");
  const newValue = select.value;

  // find duplicates
  for (let i = 0; i < allSelects.length; i++) {
    if (allSelects[i] !== select && allSelects[i].value === newValue && newValue !== "") {
      const confirmReplace = confirm(
        `This number is already selected in position ${i + 1}. Do you want to replace it?`
      );

     // if (confirmReplace) {
        // Find remaining available value
        const usedValues = Array.from(allSelects).map(s => s.value).filter(v => v !== "");
        const availableValues = optionsList.filter(v => !usedValues.includes(v));

        if (availableValues.length > 0) {
 			$(allSelects[i]).val(availableValues[0]).trigger("change");
          //allSelects[i].value = availableValues[0]; // auto replace with first free value
        } else {
          allSelects[i].value = ""; // fallback to blank if nothing left
        }
     // } else {
       // select.value = lastValues.get(select) || ""; // revert back to old
      //}
      break;
    }
  }

  // update old value for this select
  lastValues.set(select, newValue);
  serialNumberPreview();
}
function padNumber(num, length) {
    return num.toString().padStart(length, '0');
}
function serialNumberPreview() {
   // let preFix = $('#pre_fix').val();
   // let postFix = $('#post_fix').val();
    let serial = $('#serial_number').val();
    let serial_number_position = $('#serial_number_position').val();
    let numLength = parseInt($('#doc_no_length').val()) || serial.length;
    let sep = $('#separator_symbols').val() || '';
   // let fy = $('select[name="financial_year"] option:selected').text();
    let fy = "2025-26";
     let financial_year_check = $('#financial_year_check').is(':checked');
     let financial_year_position = $('#financial_year_position').val();

    serial = padNumber(serial, numLength);
    let parts = [];


    let additional_data = {};
    $(".columns_sequince").each(function (){
        let seqid = $(this).val();
        var display_type = $("#display_type_"+seqid).val();
        var static_value = $("#static_value_"+seqid).val();
        var dynamic_value = $("#dynamic_value_"+seqid).val();
        var positions = $("#positions_"+seqid).val();
        if(display_type =="STATIC"){
            if(static_value != ""){
                additional_data[positions] = static_value;
            }
        }else{
            if(dynamic_value != ""){
                additional_data[positions] = dynamic_value;
            }
        }
    });
    for($m=1; $m<=10; $m++){
        if (serial  && serial_number_position == $m) parts.push(serial);
        if (fy && financial_year_check && financial_year_position == $m) parts.push(fy);
        if (additional_data[$m] && additional_data[$m]!="") parts.push(additional_data[$m]);
    }
    $('#serialnumberoutput').text(parts.join(sep));
/**
    let pre_fix_position = $('#pre_fix_position').val();
    let post_fix_position = $('#post_fix_position').val();
    let serial_number_position = $('#serial_number_position').val();
    let financial_year_position = $('#financial_year_position').val();

    let pre_fix_check = $('#pre_fix_check').is(':checked');
    let post_fix_check = $('#post_fix_check').is(':checked');
    let financial_year_check = $('#financial_year_check').is(':checked');

    let post_fix_type = $('#post_fix_type').val();
    let pre_fix_type = $('#pre_fix_type').val();
    console.log(" --- post_fix_type ---"+post_fix_type+" --- pre_fix_type ---"+pre_fix_type)
    // Basic order: Prefix → Serial → Postfix → Financial Year
    let parts = [];
    for($m=1; $m<=4; $m++){
        if (pre_fix_check && pre_fix_position == $m){ 
            if(pre_fix_type == "STATIC" && preFix){
                parts.push(preFix);
            }else if(pre_fix_type == "DYNAMIC"){
                parts.push('FIELDVALUE');
            }
        }
        if (serial  && serial_number_position == $m) parts.push(serial);
        if (post_fix_check && post_fix_position == $m){ 
            if(post_fix_type == "STATIC" && postFix){
                parts.push(postFix);
            }else if(post_fix_type == "DYNAMIC"){
                parts.push('FIELDVALUE');
            }
        }
        if (fy && financial_year_check && financial_year_position == $m) parts.push(fy);
	} 
    **/
    
}


function preFixonChange(){
	if ($('#pre_fix_check').is(':checked')) {
	    $(".pre_fix_check_css").removeClass("event_disabled");
	}else{
	    $(".pre_fix_check_css").addClass("event_disabled");
	}
	serialNumberPreview();
}
function postFixonChange(){
	if ($('#post_fix_check').is(':checked')) {
	   	$(".post_fix_check_css").removeClass("event_disabled");
	}else{
	    $(".post_fix_check_css").addClass("event_disabled");	
	}
	serialNumberPreview();
}
function financeYearonChange(){
	if ($('#financial_year_check').is(':checked')) {
	    $(".financial_year_check_css").removeClass("event_disabled");
	}else{
	    $(".financial_year_check_css").addClass("event_disabled");
	}
	serialNumberPreview();
}




function formSubmitBTN(){
    var error_seq = 0;
    //console.log('start ---- error_seq --'+error_seq);
    $(".empty_error").text(' ');
    var doc_no_length = $("#doc_no_length").val();
    console.log('doc_no_length -------'+ doc_no_length);
    if(doc_no_length =="" || doc_no_length ==0){
        error_seq = 1;
        $("#error_doc_no_length").text('Please Enter Number Length');
    }
    $(".columns_sequince").each(function () {
        let seqid = $(this).val();
        console.log("Value:", seqid);
        var display_type = $("#display_type_"+seqid).val();
        var static_value = $("#static_value_"+seqid).val();
        var dynamic_value = $("#dynamic_value_"+seqid).val();
        if(display_type =="STATIC"){
            if(static_value == ""){
                //console.log('static_value----------');
                error_seq = 1;
                $("#error_static_value_"+seqid).text('Please Enter Value');
            }
        }else{
            if(dynamic_value == ""){
                //console.log('dynamic_value----------');
                error_seq = 1;
                $("#error_dynamic_value_"+seqid).text('Please Select Any one');
            }
        }

    });
    
    if(error_seq =='0'){
       // console.log("okkkkkkkkkkkkkkkk");

	var process_name = $("#process_name").val();
	var rid = $("#application_serial_numbers_id").val();
	var formData = new FormData();
    formData.append("page", "insertapplicationserialnumbers"); 
    var dataArray = $("#serial_number_form").serializeArray();
    $(dataArray).each(function(i, field){
        formData.append(field.name, field.value); 

    });
    $.ajax({
        url: "ajax.php",
        data: formData,
        type: "POST",
       	contentType: false,
        processData: false,
        success:function(data){
        	console.log(data);
            var result = JSON.parse(data);
            if(result.res.status =="fail"){
                tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.res.message,'');
            }else{
                if(result.res.LASTID == '0'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
                }  
                selectProcessName(process_name, rid);
            }
        },
        error:function (){}
    });
    }else{
        console.log("not okkkkkkkkkkk");
    }
}
</script>
<div class="card">
	<div class="card-header">
        <h5>Transaction Serial Numbers</h5>
        <div class="card-header-right">
          	<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back </a>
        </div>
    </div>
	<div class="card-body">
		<div class="form-group col-lg-6">
			<label class="form-label">Process Name</label>
			<select class="form-select js-example-basic-single" name="process_name" id="process_name" onchange="selectProcessName(this.value)">
				<option value="">Select Any One</option>
				<?php foreach($dynamic_mapping_tables as $process){ ?>
					<option value="<?php echo $process['application_table_name']; ?>" <?php echo $workflowconfiguration['process_name']==$process['application_table_name']?"selected":"" ?> ><?php echo ucwords(str_replace("_"," ",($process['application_table_name']))); ?></option>
				<?php } ?>
			</select>    
		</div>
	</div>
</div>

<div class="row" id="transaction_serial_numbers_templates"></div>
