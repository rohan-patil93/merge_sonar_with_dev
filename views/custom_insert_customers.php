<?php 
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['dropzone'] = 1;
// $page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);
// print_r($page_details);
$File_Uploads_Path = "uploads/customer/";
// $content = "Help Text"; 
// $add_url = $page_details['list_page_name'];
// $view_url = $page_details['list_page_name'];
// $table_name = $page_details['application_table_name'];
// $table_name = "vendors";
//echo $table_name; exit;
if(isset($_POST['submit'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $image = imageupload('customer','image',$_POST['hid_image']);
	$_POST['image'] = $image;
  	$url = API."?mode=insertcustomers&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
	//echo "<pre>";print_r($res); echo "</pre>";
	for ($i = 0; $i < count((array)$_POST['lut_no']); $i++) {
	 	if(($_POST['lut_no'][$i] !="" OR $_POST['bond_no'][$i] !="") AND $_POST['lock_status'][$i] =='1'){
			$additional_notice = "Please lock your row(s) in Tax Info. Otherwise, it may not be used anywhere.";
		}
	}
    $response = $res['res'];
    if($response['status'] == 'sucess'){
    if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode; 
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode; 
            $rid = deocdeId("id:".$response['LASTID']);	
        	header("location:index?option=COM_INSERT_CUSTOMERS&Rid=".$rid);
        }   
    }else{
    	

        //($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}
//else{
	//echo "insert_msg"; print_r($_SESSION['insert_msg']);
    if($_SESSION['insert_msg'] !=""){ 
        $msgcode = $_SESSION['insert_msg'];
        $_SESSION['insert_msg'] = "";
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]." ".$additional_notice; ?>','');
                } 
            }
        </script>
        <?php
      
    }
//}

/**
if (isset($_POST['submit'])){
	$data = $_POST;
	$data_agreements = $_POST;
	$image = imageupload('customer','image',$_POST['hid_image']);
	//echo "<pre>".$image; print_r($_FILES); exit;
	unset($data['submit']);
	unset($data['submit_value']);
	unset($data['image']);
	unset($data['hid_image']);
	unset($data['hid']);
	unset($data['ag_type']);
	unset($data['ag_number']);
	unset($data['file_url']);
	unset($data['ag_ex_date']);
	unset($data['ag_sin_date']);
	unset($data['ag_rowid']);
	unset($data['ag_rowid']);
	unset($data['hidden_agreement_file']);
	$data['hiring_clause'] = $data['hiring_clause'] =='on'?'0':'1';
	$data['tds_apply'] = $data['tds_apply'] =='on'?'0':'1';
	$data['asof'] = dateconvertdbformat($data['asof']);
	$data['image'] = $image;
	if($_POST['submit'] == 'Add'){
		$data['created_date'] = nowDate();
		$data['created_by'] = $_SESSION['userid'];
		$data['company_id'] = $_SESSION['company_id'];
		$data['is_active'] = 0;
		$data['current_status'] = 0;
		$url = API."?mode=adddata&table=".$table_name."&method=post";
		//print_r($data); exit;
		//echo $url; exit;
		$response = file_post_contents($url, $data, 'insert');
		$postdata['vendor_id'] = $response['LASTID'];
		$postdata['company_id'] = $data['company_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		//print_r($postdata);  exit;

		$count = count((array)$_FILES["agreement_file"]["name"]);
		for($f=0; $f<$count; $f++){
			$temp = explode(".", basename($_FILES["agreement_file"]["name"][$f]));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$data_agreements['file_url'][$f] = $newfilename;
			move_uploaded_file($_FILES["agreement_file"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
		}
		foreach ($data_agreements['ag_type'] as $agkey => $ag) { 
			$postdata['agreement_name'] = $data_agreements['ag_type'][$agkey];
			$postdata['agreement_ref_no'] = $data_agreements['ag_number'][$agkey];
			$postdata['signed_date'] = date('Y-m-d', strtotime($data_agreements['ag_sin_date'][$agkey]));

			$postdata['expiry_date'] = date('Y-m-d', strtotime($data_agreements['ag_ex_date'][$agkey]));
			$postdata['file_url'] = $data_agreements['file_url'][$agkey];
			if(!empty($data_agreements['ag_rowid'][$agkey]))
			{
	    		// update
				$row_id = $data_agreements['ag_rowid'][$agkey];
				$postdata['updated_date'] = $data['created_date'];
				$postdata['updated_by'] = $data['created_by'];
				$url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'update');
			}else {
	    		//insert
				//echo "<pre>";print_r($postdata);
				$postdata['created_date'] = $data['created_date'];
				$postdata['created_by'] = $data['created_by'];
				$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'insert');
			}

		}

		/*$urldata = urlencode(json_encode($data,true));
		$url = API."?mode=adddata&table=".$table_name."&data=$urldata";
		$response = callAPI($url);//file_get_contents($url, false, $context);*//**
		if ($_POST['submit_value'] == 'continue'){
			//header("location:index.php?option=$add_url&error=3000");
		}else{
			//header("location:index.php?option=$view_url&error=3000");
		}
	}
	if($_POST['submit'] == 'Update'){	
		//$_SESSION['check'] = 1;
		$data_agreements = $_POST;
		$data_agreements['created_date'] = nowDate();
		$data_agreements['created_by'] = $_SESSION['userid'];
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$vendor_id1 = $_POST['vendor_id'];
		unset($data['vendor_id']);
		$data_agreements['vendor_id'] = $vendor_id1;
		//print_r($_POST); exit;
		// print_r($_FILES["agreement_file"]["name"]);
		
		$url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
		$response = file_post_contents($url, $data, 'update');
		$postdata['company_id'] = $_SESSION['company_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		$postdata['vendor_id'] = $vendor_id1;

		$count = count((array)$_FILES["agreement_file"]["name"]);
		for($f=0; $f<$count; $f++) {
			$temp = explode(".", basename($_FILES["agreement_file"]["name"][$f]));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$data_agreements['file_url'][$f] = $newfilename;
			move_uploaded_file($_FILES["agreement_file"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
		}
		//print_r($postdata['file_url']);

		foreach ($data_agreements['ag_type'] as $agkey => $ag) { 
			$postdata['agreement_name'] = $data_agreements['ag_type'][$agkey];
			$postdata['agreement_ref_no'] = $data_agreements['ag_number'][$agkey];
			$postdata['signed_date'] = date('Y-m-d', strtotime($data_agreements['ag_sin_date'][$agkey]));
			$postdata['expiry_date'] = date('Y-m-d', strtotime($data_agreements['ag_ex_date'][$agkey]));
			$postdata['file_url'] = $data_agreements['file_url'][$agkey];
			
			if(!empty($data_agreements['ag_rowid'][$agkey]))
			{
	    		// update
				$row_id = $data_agreements['ag_rowid'][$agkey];
				$postdata['updated_date'] = $data_agreements['created_date'];
				$postdata['updated_by'] = $data_agreements['created_by'];
			//	print_r($postdata);
				$url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'update');
			}else {
	    		//insert
				//echo "<pre>";print_r($postdata);
				$postdata['created_date'] = $data['created_date'];
				$postdata['created_by'] = $data['created_by'];
				$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
				$response = file_post_contents($url, $postdata, 'insert');
			}

		}
		//exit;
		if ($response) {
			  //header("location:index.php?option=$view_url");
		}
	}
}  

**/
//if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ 
	//$rid =  encodeId($_GET['Rid']);
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API."?mode=getcustomersdetails&id=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$customers = $result['customers'];
$property_tenant_details  = $result['property_tenant_details'];
$property_details = $result['property_owner_details'];
$property_owners =$result['property_owners'];
$payment_method =$result['payment_method'];
$salestype =$result['salestype'];
$gstregistrationtype = $result['gstregistrationtype'];
	// $emplist = $result['employess'];
$countrieslist = $result['countries'];
$states = $result['states'];
$cities = $result['cities'];
$customers_list = $result['customers_list'];
$paymenttype = $result['paymenttype'];
$lutbond = $result['lutbond'];
$customer_vendor_gst_billing_details = $result['customer_vendor_gst_billing_details'];
$countrylist =$stateslist = $citieslist =  array();
foreach($countrieslist as $country_list){
	$countrylist[$country_list['id']] = $country_list['country_name'];
}
foreach($states as $states_list){
	$stateslist[$states_list['id']] = $states_list['state_name'];
}
foreach($cities as $cities_list){
	$citieslist[$cities_list['id']] = $cities_list['city_name'];
}
//echo "<pre>";print_r($cities); //print_r($property_details); print_r($property_owners); 
//echo "</pre>";

//print_r($['customer_vendor_gst_billing_details_sql']);
if($customers['image'] !=''){
	$image = $File_Uploads_Path.$customers['image']; 
}else{
	$image = $File_Uploads_Path."no-image.jpg";
} 
?>


<script type="text/javascript">
	function addedurows(){
		var date_format = "yy-mm-dd";
		var count = $(".itemRow").length;
		count = count + 1;
		jQuery(document).ready(function() {    
			KTBootstrapDatepicker.init();
		});
		var htmlRows = '';
		htmlRows += '<tr class="">'; 

		htmlRows += '<td>'+count+'</td>'; 
		htmlRows += '<td><input type="text" name="ag_type[]"  class="form-control rowcount itemRow " autocomplete="off">  </td>'; 
		htmlRows += '<td><input type="text" name="ag_number[]"  class="form-control " autocomplete="off"> </td>'; 
		htmlRows += '<td><input type="text" name="ag_sin_date[]"  class="form-control datepicker" id="kt_datepicker_35" autocomplete="off">  </td>'; 
		htmlRows += '<td><input type="text" name="ag_ex_date[]"  class="form-control datepicker"  id="kt_datepicker_35" autocomplete="off">  </td>'; 

       //htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
       htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><span class="btn-hover-text-primary mt-2" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" > <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i></div></td>'

       htmlRows += '</tr>';
	//datepickercall();
	
	$('#edutable').append(htmlRows);
	$( ".datepicker" ).datepicker({
		showOn: "both",
		buttonText: "<i class='ti-calendar'></i>",
		dateFormat: date_format,
		changeMonth: true,
		changeYear: true
	});
}


function selectedcategory(selected_id,element_id,table_name,field_name,targetelement) {
	var selectedvalues = selected_id;
                //alert(selected_id);

                if(table_name=='states' || table_name=='cities'){
                	$.ajax({
                		url: "ajax.php",
                		data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
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
                					el.value = response[i].id;

                				}
                				if(table_name=='cities'){
                					el.textContent = response[i].city_name;
                					el.value = response[i].id;

                				}
                				select.add(el);
                			}
                		}
                	});
                }

            }
        </script>
        <script>
        	function checkboxclick(){
	//$('input[name="hiring_cluse"]').click(function() { 
		if ($('input[name="hiring_clause"]').is(':checked')) {
			$(".coolingmonth").show();
		} else {
			$(".coolingmonth").hide();
		}
	}


function selectItemsWithParty(item_id,type,party_item_id=""){
	var transaction_table = 'customers';
	var transaction_id = '<?php echo $rid; ?>';
	if(type == "REMOVE"){
		 swal({
		    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		    text: "<?php echo $_SESSION['error_message']['9167']; ?>",
		    type: "warning",
		    showCancelButton: true,
		    confirmButtonClass: "btn-danger",
		    confirmButtonText: "Yes",
		    cancelButtonText: "No",
		    closeOnConfirm: true,
		    closeOnCancel: true
		},
		function(isConfirm) {
		    if (isConfirm) {
				$.ajax({
					url: "ajax.php",
					type: "POST",
					data: 'page=insertvendorcustomeritems&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&party_item_id='+party_item_id+'&type='+type,
					success: function(response) {
						console.log(response);
						tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message']['9165']; ?>",'');
						getVendorCustomerItems();
					}
				});
			}
		});
	
	}else{
		jQuery.ajax({
		    url: "views/custom_insert_vendor_customer_item_popup.php",
		    data:'transaction_table='+transaction_table+"&transaction_id="+transaction_id+"&item_id="+item_id+'&type='+type,
		    type: "POST",
		    success:function(data){
		    	$('#dynamic_popup1').html(data);
	    		$('#kt_modal_dynamic_popup').modal("show");
		    }
		});	
	}

}
function getVendorCustomerItems(){
	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#vendor_items").html(loader);
    var transaction_table = 'customers';
	var transaction_id = '<?php echo $rid; ?>';
	jQuery.ajax({
	    url: "views/custom_list_vendor_customer_items.php",
	    data:'transaction_table='+transaction_table+"&transaction_id="+transaction_id,
	    type: "POST",
	    success:function(data){
	    	$('#vendor_items').html(data);
	    	$(".js-example-basic-single").select2();
			$('[data-bs-toggle="tooltip"]').tooltip();
      	}
	});	
}
function getCustomerContracts(){
	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
	$("#customer_contracts").html(loader);
	var customer_id = '<?php echo $rid; ?>';
	jQuery.ajax({
	    url: "views/custom_list_customer_contracts.php",
	    data: 'customer_id=' + customer_id,
	    type: "POST",
	    success: function(data){
	    	$('#customer_contracts').html(data);
			$('[data-bs-toggle="tooltip"]').tooltip();
	    }
	});
}


// omkar LUT/Bond Number
// adding and removing tax rows

var maxrows12 = 10000;

function addTaxRow() {
	maxrows12 = maxrows12 + 1;
	var lastRow = $(".tax-row:last");
	var lutNoInput = lastRow.find("input[name='lut_no[]']");
	var bondNoInput = lastRow.find("input[name='bond_no[]']");
	var finYearSelect = lastRow.find("select[name='finance_year[]']");
	
	// Check if last row is locked
	var lockButton = lastRow.find(".lock-btn");
	var isLocked = lockButton.hasClass('locked');
	if (!isLocked) {
		tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please lock the current row before adding a new one', '');
		return false;
	}
	
	var lutNo = lutNoInput.val();
	var bondNo = bondNoInput.val();
	var finYear = finYearSelect.val();
	
	lutNoInput.removeClass('is-invalid');
	bondNoInput.removeClass('is-invalid');
	finYearSelect.removeClass('is-invalid');
	
	var hasError = false;
	if (!lutNo) {
		lutNoInput.addClass('is-invalid');
		hasError = true;
	}
	if (!bondNo) {
		bondNoInput.addClass('is-invalid');
		hasError = true;
	}
	if (!finYear) {
		finYearSelect.addClass('is-invalid');
		hasError = true;
	}
	
	if (hasError) {
		tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please fill all fields in the current row before adding a new one', '');
		return false;
	}
	
	var selectedYears = [];
	$(".tax-row").each(function() {
		var year = $(this).find("select[name='finance_year[]']").val();
		if (year) {
			selectedYears.push(year);
		}
	});
	
	var maxRowId = 1;
	$(".tax-row").each(function() {
		var rowId = parseInt($(this).attr("data-row-id")) || 0;
		if (rowId > maxRowId) {
			maxRowId = rowId;
		}
	});
	var newRowId = maxRowId + 1;
	
	var clonedRow = $(".tax-row:first").clone(false, false);
	
	clonedRow.attr("data-row-id", newRowId);
	clonedRow.attr("data-added", "true");
	
	clonedRow.find("input").val("");
	clonedRow.find("input").removeClass('is-invalid');
	
	clonedRow.find("input[name='lutbond_id[]']").val("");
	
	clonedRow.find("select").prop('selectedIndex', 0);
	clonedRow.find("select").removeClass('is-invalid');
	
	// Reset lock button state
	clonedRow.find(".lock-btn").removeClass('locked permanently-locked').addClass('btn-warning').html('<i class="fa fa-unlock"></i> Lock');
	clonedRow.find("input[name='lock_status[]']").val(1);

	clonedRow.removeAttr('style');
	clonedRow.removeClass('locked-row');
	
	clonedRow.find('input:not([name="lock_status[]"])').attr('readonly', false);
	clonedRow.find('select').prop('disabled', false);
	
	clonedRow.find('.select2-container').remove();
	
	$(".tax-row:last").after(clonedRow);
	clonedRow.find(".remove-btn").removeClass("d-none");
	
	var newSelect = clonedRow.find(".js-example-basic-single");
	newSelect.select2();
	newSelect.find("option").each(function () {
	    if (selectedYears.includes($(this).val()) && $(this).val() !== "") {
	        $(this).attr('disabled', true);
	    }
	});
	newSelect.select2('destroy').select2();
}

function validateAllTaxRows() {
    let isValid = true;

    $(".tax-row").each(function () {
        let lutNo = $(this).find("input[name='lut_no[]']");
        let bondNo = $(this).find("input[name='bond_no[]']");
        let finYear = $(this).find("select[name='finance_year[]']");

        // Remove previous error classes
        lutNo.removeClass("is-invalid");
        bondNo.removeClass("is-invalid");
        finYear.removeClass("is-invalid");

        // Check if each field is filled
        if (!lutNo.val()) {
            lutNo.addClass("is-invalid");
            isValid = false;
        }

        if (!bondNo.val()) {
            bondNo.addClass("is-invalid");
            isValid = false;
        }

        if (!finYear.val()) {
            finYear.addClass("is-invalid");
            isValid = false;
        }
    });

    if (!isValid) {
        tostarnotify(
            'top', 'right', 'fa fa-warning',
            'form-label label label-danger',
            'animated flipInX', 'animated flipOutX',
            'Please fill all fields in all rows before submitting', ''
        );
    }

    return isValid;
}

function removeTaxRow(button) {
	var rowToRemove = $(button).closest(".tax-row");
	
	var lockButton = rowToRemove.find(".lock-btn");
	var isLocked = lockButton.hasClass('locked');
	var isPermanentlyLocked = lockButton.hasClass('permanently-locked');
	
	if (isLocked || isPermanentlyLocked) {
		tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Locked rows cannot be removed', '');
		return false;
	}
	
	if ($(".tax-row").length > 1) {
		rowToRemove.remove();
		chnageFinanceTaxYear();
	} else {
		rowToRemove.find("input:not([name='lock_status[]'])").val("");
		rowToRemove.find("select").prop('selectedIndex', 0);
		
		// Clear date fields
		rowToRemove.find(".start-date-field").val("");
		rowToRemove.find(".end-date-field").val("");
		
		chnageFinanceTaxYear();
	}
}

function chnageFinanceTaxYear() {
    var selectedYears = [];
    $(".tax-row").each(function() {
        var select = $(this).find("select[name='finance_year[]']");
        var year = select.val();
        if (year) {
            selectedYears.push({
                value: year,
                element: select
            });
        }
    });
    
    $(".tax-row").each(function() {
        var currentSelect = $(this).find("select[name='finance_year[]']");
        var currentValue = currentSelect.val();
        
        currentSelect.find("option").attr('disabled', false);
        
        selectedYears.forEach(function(item) {
            if (item.element[0] !== currentSelect[0] && item.value) {
                currentSelect.find("option[value='" + item.value + "']").attr('disabled', true);
            }
        });
        
        currentSelect.select2('destroy').select2();
    });
}

function validateTaxRows() {
    var isValid = true;
    var hasUnlockedRows = false;
    
    $(".tax-row").find("input:not([name='lock_status[]']), select").removeClass('is-invalid');
    
    $(".tax-row").each(function(index) {
        var row = $(this);
        var lockButton = row.find(".lock-btn");
        var isLocked = lockButton.hasClass('locked');
        var isPermanentlyLocked = lockButton.hasClass('permanently-locked');
        
        var lutNoInput = row.find("input[name='lut_no[]']");
        var bondNoInput = row.find("input[name='bond_no[]']");
        var finYearSelect = row.find("select[name='finance_year[]']");
        var lutNo = lutNoInput.val();
        var bondNo = bondNoInput.val();
        var finYear = finYearSelect.val();
        console.log(lutNo,' --- ',bondNo,' -- ',finYear);
        // If row is not locked, mark as invalid
        if (!isLocked && !isPermanentlyLocked && (lutNo !="" || bondNo !="" || finYear !="")) {
            hasUnlockedRows = true;
            
            // var lutNoInput = row.find("input[name='lut_no[]']");
            // var bondNoInput = row.find("input[name='bond_no[]']");
            // var finYearSelect = row.find("select[name='finance_year[]']");
            
            // var lutNo = lutNoInput.val();
            // var bondNo = bondNoInput.val();
            // var finYear = finYearSelect.val();
            
            // If fields are filled but not locked, require locking
            if (lutNo || bondNo || finYear) {
                tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please lock row ' + (index + 1) + ' before submitting', '');
                isValid = false;
                return false;
            }
        }
    });
    
    // If there are unlocked rows, prevent submission
    if (hasUnlockedRows && isValid) {
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'All rows must be locked before submission', '');
        return false;
    }
    
    return isValid;
}

	$(document).ready(function() {
		$("#kt_form").submit(function(e) {
			if (!validateTaxRows()) {
				e.preventDefault();
			}
		});
		
		chnageFinanceTaxYear();
		
		// Initialize row lock buttons correctly
		$(".lock-btn").each(function() {
			var lockStatusValue = $(this).closest('.tax-row').find("input[name='lock_status[]']").val();
			var lutbondId = $(this).closest('.tax-row').find("input[name='lutbond_id[]']").val();
			
			// If database value is 0, it means the row is locked
			if (lockStatusValue == 0) {
				$(this).addClass('locked btn-success').removeClass('btn-warning').html('<i class="fa fa-lock"></i> Locked');
				
				// If it has an ID, make it permanently locked
				if (lutbondId) {
					$(this).addClass('permanently-locked').html('<i class="fa fa-lock"></i> Permanent');
				}
				
				var row = $(this).closest('.tax-row');
				row.addClass('locked-row');
				row.find('input:not([name="lock_status[]"])').attr('readonly', true);
				row.find('select').prop('disabled', true);
				row.find('select').select2('destroy').select2();
			} else {
				// Unlocked rows
				$(this).removeClass('locked btn-success').addClass('btn-warning').html('<i class="fa fa-unlock"></i> Lock');
			}
		});
		
		// Initialize financial dates for existing rows
		$(".tax-row").each(function() {
			var select = $(this).find("select[name='finance_year[]']");
			if (select.val()) {
				updateFinancialDates(select[0]);
			}
		});
	});

function updateFinancialDates(selectElement) {
    // Get the selected year
    var year = $(selectElement).val();
    var row = $(selectElement).closest('.tax-row');
    
    if (year) {
        // Calculate start date (April 1st of selected year)
        var startDate = year + '-04-01';
        
        // Calculate end date (March 31st of next year)
        var nextYear = parseInt(year) + 1;
        var endDate = nextYear + '-03-31';
        
        // Set the hidden field values
        row.find('.start-date-field').val(startDate);
        row.find('.end-date-field').val(endDate);
    } else {
        // Clear dates if no year selected
        row.find('.start-date-field').val('');
        row.find('.end-date-field').val('');
    }
    
    // Also call the original function to maintain duplicate year prevention
    chnageFinanceTaxYear();
}

function toggleRowLock(button) {
    var row = $(button).closest('.tax-row');
    var lockButton = $(button);
    var isLocked = lockButton.hasClass('locked');
    var isPermanentlyLocked = lockButton.hasClass('permanently-locked');
    
    // Prevent unlocking permanently locked rows
    if (isPermanentlyLocked) {
        tostarnotify('top', 'right', 'fa fa-lock', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'This row is permanently locked and cannot be unlocked', '');
        return false;
    }
    
    var inputs = row.find('input:not([name="lock_status[]"])');
    var selects = row.find('select');
    var lockStatusInput = row.find("input[name='lock_status[]']");
    
    if (!isLocked) {
        // Locking the row - validate all fields are filled
        var lutNoInput = row.find("input[name='lut_no[]']");
        var bondNoInput = row.find("input[name='bond_no[]']");
        var finYearSelect = row.find("select[name='finance_year[]']");
        
        var lutNo = lutNoInput.val();
        var bondNo = bondNoInput.val();
        var finYear = finYearSelect.val();
        
        var hasError = false;
        if (!lutNo) {
            lutNoInput.addClass('is-invalid');
            hasError = true;
        }
        if (!bondNo) {
            bondNoInput.addClass('is-invalid');
            hasError = true;
        }
        if (!finYear) {
            finYearSelect.addClass('is-invalid');
            hasError = true;
        }
        
        if (hasError) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please fill all fields before locking the row', '');
            return false;
        }
        
        // Set lock status to 0 (locked)
        lockStatusInput.val(0);
        
        // Check if this is an existing record with id
        var lutbondId = row.find("input[name='lutbond_id[]']").val();
        if (lutbondId) {
            // This is an existing record, mark as permanently locked
            lockButton.addClass('permanently-locked').html('<i class="fa fa-lock"></i> Permanent');
        } else {
            lockButton.html('<i class="fa fa-lock"></i> Locked');
        }
        
        // Update button state
        lockButton.addClass('locked btn-success').removeClass('btn-warning');
        
        // Lock all fields in the row
        inputs.attr('readonly', true);
        selects.prop('disabled', true);
        row.addClass('locked-row');
        selects.select2('destroy').select2();
        
        tostarnotify('top', 'right', 'fa fa-lock', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', 'Row has been locked', '');
    } else {
        // Unlocking the row (only for new rows without IDs)
        lockStatusInput.val(1);
        
        // Update button state
        lockButton.removeClass('locked btn-success').addClass('btn-warning').html('<i class="fa fa-unlock"></i> Lock');
        
        // Unlock all fields in the row
        inputs.attr('readonly', false);
        selects.prop('disabled', false);
        row.removeClass('locked-row');
        selects.select2('destroy').select2();
        
        tostarnotify('top', 'right', 'fa fa-unlock', 'form-label label label-info', 'animated flipInX', 'animated flipOutX', 'Row has been unlocked', '');
    }
}



</script>
<?php if($content !=''){ ?>
	<div class="alert alert-warning alert-dismissible">
		<strong><?php echo $content; ?></strong> 
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php } ?>
</script>
    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>  
<?php } ?>
<form class="" id="kt_form" method="post" action=""  name="myForm" enctype="multipart/form-data">
	<div class="card card-custom card-sticky">
		<div class="cover-profile" style="position: relative;">
			<div class="m-b-25">
				<img class="m-b-25 img-fluid" src="assets/images/user-profile/bg-img1.jpg" alt="bg-img" >
				<div class="card-block user-info d-flex justify-content-center">
					<div class="col-md-11">
						<div class="media-left">
							<a href="#" class="profile-image">
								<img src="<?php echo $image; ?>" class=" img-120 user-img b-radius-10" alt="User-Profile-Image" onerror="this.src='uploads/all/no-image.jpg';">
							</a>
						</div>
						<div class="media-body row">
							<div class="col-lg-12">
								<div class="user-title">
									<h4 class="text-white"><?php echo ucwords($customers['display_name'])." (".ucwords($customers['company']).")"; ?></h4>
									<span class="text-white"><?php echo $customers['email']; ?></span>
								</div>
								<?php /** if($customers['hiring_clause']==0){?> 
									<div>No Hiring Clause Applicable, Cooling off Period of <?php echo $customers['cooling_months'];?> Months </div>
								<?php }else{ ?>
									<div>No Hiring Clause Not Applicable	</div>
								<?php }  **/?>
							</div>
							<div>
								<div class="float-right cover-btn">
									<input type="hidden" name="vendor_id" value=<?php echo $customers['id']; ?> />
									<button  type="submit" name="submit" class="btn btn-primary m-r-20 m-b-20">
										<?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i>
									Update <?php }else{  ?><i class="icofont icofont-plus"></i>
									Submit<?php } ?> </button> 
									<!-- <button type="button" class="btn btn-primary m-b-20"><i class="icofont icofont-ui-messaging"></i>
									Message</button> -->
									<input type="hidden" name="rid" value="<?php echo $rid; ?>"> 
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row m-b-30 mt-5 card-body">
			<div class="col-lg-12 col-xl-12">
				<ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
					<li class="nav-item">
						<a class="nav-link  <?php echo $customers['id']!=""?"active":""; ?>" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $customers['id']!=""?"":"active"; ?>" data-bs-toggle="tab" href="#basic-info" role="tab">Basic Info</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#contact-details" role="tab">Contact Details</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#note" role="tab">Note</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#tax-info" role="tab">Tax Info</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#payment-billing" role="tab">Payment and Billing</a>
						<div class="slide"></div>
					</li>
					<!-- <li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#sale-discount" role="tab">Sale / Discount</a>
						<div class="slide"></div>
					</li> -->
					<!-- <li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#agreements" role="tab">Agreements</a>
						<div class="slide"></div>
					</li> -->
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#attachments" role="tab">Attachments</a>
						<div class="slide"></div>
					</li>
					<!-- <li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#no-hiring" role="tab">No hiring clause</a>
						<div class="slide"></div>
					</li> -->
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#reantal_properties" role="tab">Rental Properties</a>
						<div class="slide"></div>
					</li>
					<?php if($rid !=""){ ?>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#vendor_items" role="tab" onclick="getVendorCustomerItems()">Items</a>
						<div class="slide"></div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#customer_contracts" role="tab" onclick="getCustomerContracts()">Contracts</a>
						<div class="slide"></div>
					</li>
				<?php } ?>
					<!-- <li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" href="#reantal_properties_2" role="tab">Rental Properties - 2</a>
						<div class="slide"></div>
					</li> -->
					
				</ul>
				<!-- Tab panes -->
				<div class="tab-content card-block">
					<div class="tab-pane" id="vendor_items" role="tabpanel">

		</div>
					<div class="tab-pane" id="customer_contracts" role="tabpanel">

		</div>
		
					<div class="tab-pane <?php echo $customers['id']!=""?"active":""; ?>"  id="overview" role="tabpanel">
						<div class="row">   
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
									<table class="table m-0">
										<tbody>
											<!-- <tr>
												<th scope="row">Title</th>
												<td><?php echo emptytextdashShow($customers['title']); ?></td>
											</tr> -->
											<tr>
												<th scope="row">Name</th>
												<td><?php echo $customers['first_name'].$customers['middle_name']." ".$customers['last_name']; ?></td>
											</tr>
											<tr>
												<th scope="row">Contact</th>
												<td><?php echo $customers['mobile']; ?></td>
											</tr>
											<tr>
												<th scope="row">Email</th>
												<td><?php echo emptytextdashShow($customers['email']); ?></td>
											</tr>
											<tr>
												<th scope="row">Phone</th>
												<td><?php echo emptytextdashShow($customers['phone']); ?></td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<div class="card-header"><h5 class="text-primary">Rental Properties</h5></div>
											<?php if(count((array)$property_tenant_details)>0){  ?>
												<table class="table table-hover m-b-0">
													<thead>
														<tr>
															<th>Property Name</th>
															<th class="w-b-w-s">Address</th>
															<th>Start Date</th>
															<th>End Date</th>
														</tr>
													</thead>
													<tbody>
														<?php $k=0;  
														foreach ($property_tenant_details as $inst) { $k++;?>
															<tr>
																<td><?php echo $property_details[$inst['property_owner_detail_id']]['property_name']; ?></td>
																<td class="w-b-w-s"><?php echo emptytextdashShow($property_details[$inst['property_owner_detail_id']]['property_address']); ?></td>
																<td><?php echo fromDate($inst['rent_commencement_date']); ?></td>
																<td><?php echo fromDate($inst['lease_period_end_date']); ?></td>
															</tr>

														<?php }  ?>
													</tbody>
												</table>
											<?php }else{ ?>
												<div class="card-px text-center ">
													<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Rental Properties</p>   
													<div class="text-center px-4">
														<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
													</div>   
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<div class="card-header"><h5 class="text-primary">Agreements</h5></div>
											<?php if(count((array)$aggrements)>0){  ?>
												<table class="table table-hover m-b-0">
													<thead>
														<tr>
															<th>Type</th>
															<th>Number</th>
															<th>Sign Date</th>
															<th>Expiry Date</th>
														</tr>
													</thead>
													<tbody>
														<?php $k=0;  
														foreach ($aggrements as $inst) { $k++;?>
															<tr>
																<td><?php echo $inst['agreement_name']; ?></td>
																<td><?php echo $inst['agreement_ref_no']; ?></td>
																<td><?php echo $inst['signed_date']; ?></td>
																<td><label class="label label-success"><?php echo $inst['expiry_date']; ?></span></td>
															</tr>

														<?php }  ?>
													</tbody>
												</table>
											<?php }else{ ?>
												<div class="card-px text-center ">
													<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Aggrements</p>   
													<div class="text-center px-4">
														<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
													</div>   
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Shipping Details</h5></div>
									<table class="table m-0">
										<tbody>
											<tr>
												<th scope="row">Shipping  Adderss</th>
												<td class="address-basic"><?php if($customers['shipping_address']!=""){ echo trim($customers['shipping_address']).",".$customers['shipping_city']; } ?></td>
											</tr>
											<tr>
												<th scope="row">Country</th>
												<td class="address-basic"><?php echo emptytextdashShow($countrylist[$customers['shipping_country']]); ?></td>
											</tr>
											<tr>
												<th scope="row">State</th>
												<td class="address-basic"><?php echo emptytextdashShow($stateslist[$customers['shipping_state']]); ?></td>
											</tr>
											<tr>
												<th scope="row">City</th>
												<td class="address-basic"><?php echo emptytextdashShow($citieslist[$customers['shipping_city']]); ?></td>
											</tr>

											<tr>
												<th scope="row">Pincode</th>
												<td><?php echo emptytextdashShow($customers['shipping_post_code']); ?></td>
											</tr>                                 
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Tax Info</h5></div>
									<table class="table m-0">
										<tbody>
											<tr>
												<th scope="row">GST Registration type</th>
												<td><?php echo emptytextdashShow($gstregistrationtype[$customers['gsttype']]); ?></td>
											</tr>
											<tr>
												<th scope="row">GSTIN</th>
												<td><?php echo emptytextdashShow($customers['gstin']); ?></td>
											</tr>
											<tr>
												<th scope="row">Tax Reg.No.</th>
												<td><?php echo emptytextdashShow($customers['customer_tax_reg_no']); ?></td>
											</tr>
											<tr>
												<th scope="row">CST Reg.No</th>
												<td><?php echo emptytextdashShow($customers['cst_reg_no']); ?></td>
											</tr>
											<tr>
												<th scope="row">PAN</th>
												<td><?php echo emptytextdashShow($customers['pan_no']); ?></td>
											</tr>                                 
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Billing Adderss</h5></div>
									<table class="table m-0">
										<tbody>
											<tr>
												<th scope="row">Billing Adderss</th>
												<td class="address-basic"><?php echo $customers['billing_address'].",".$customers['billing_city']; ?></td>
											</tr>
											<tr>
												<th scope="row">Country</th>
												<td class="address-basic"><?php echo emptytextdashShow($countrylist[$customers['billing_country']]); ?></td>
											</tr>
											<tr>
												<th scope="row">State</th>
												<td class="address-basic"><?php echo emptytextdashShow($stateslist[$customers['billing_state']]); ?></td>
											</tr>
											<tr>
												<th scope="row">City</th>
												<td class="address-basic"><?php echo emptytextdashShow($citieslist[$customers['billing_city']]); ?></td>
											</tr>
											<tr>
												<th scope="row">Pincode</th>
												<td><?php echo emptytextdashShow($customers['billing_post_code']); ?></td>
											</tr>
											<tr>
												<th scope="row">Contact Person</th>
												<td><?php echo emptytextdashShow($customers['contact_person']); ?></td> 
											</tr>
											<tr>
												<th scope="row">Contact Person Mobile</th>
												<td><?php echo emptytextdashShow($customers['mobile']); ?></td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Payment & Billing</h5></div>
									<table class="table m-0">
										<tbody>
											<tr>
												<th scope="row">Preferred Payment Method</th>
												<td><?php echo emptytextdashShow($customers['pref_payment_method']); ?></td>
											</tr>
											<tr>
												<th scope="row">Payment Trems</th>
												<td><?php echo emptytextdashShow($paymenttype[$customers['payment_terms']]); ?></td>
											</tr>
											<tr>
												<th scope="row">Preferred Delivery Method</th>
												<td><?php echo emptytextdashShow($customers['preferred_deliver_method']); ?></td>
											</tr>
											<tr>
												<th scope="row">Opening Blance</th>
												<td class="text-right"><?php echo emptytextdashShow(customeummarmonyformate($customers['opening_balance'])); ?></td>
											</tr>
											<tr>
												<th scope="row">The Customer Pays Me With</th>
												<td class="text-right"><?php echo emptytextdashShow(customeummarmonyformate($customers['pay_with_me'])); ?></td>
											</tr>                                 
											<tr>
												<th scope="row">Credit Limit</th>
												<td class="text-right"><?php echo emptytextdashShow(customeummarmonyformate($customers['credit_limit'])); ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<?php /**?>
							<div class="col-lg-12 col-xl-6">
								<div class="table-responsive">
									<div class="card-header"><h5 class="text-primary">Sale & Discount</h5></div>
									<table class="table m-0">
										<tbody>
											<tr>
												<th scope="row">Default Discount Percentage</th>
												<td><?php echo emptytextdashShow(customeummarmonyformate($customers['default_discount_perc'])); ?></td>
											</tr>
											<tr>
												<th scope="row">Payment Discount Percentage</th>
												<td><?php echo emptytextdashShow(customeummarmonyformate($customers['payment_discount_perc'])); ?></td>
											</tr>
											<!-- <tr>
												<th scope="row">Sales Type</th>
												<td><?php echo emptytextdashShow($customers['sales_type']); ?></td>
											</tr>  -->                             
										</tbody>
									</table>
								</div>
							</div>
							<?php **/ ?>
						</div>
					</div>
					<div class="tab-pane <?php echo $customers['id']!=""?"":"active"; ?>" id="basic-info" role="tabpanel">
						<div class="row">
							<div class="form-group row">
								<!-- <div class="col-lg-3 p-r-5" style="float: left;">
									<span class="form-label">Title</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="title" name="title" value="<?php echo $customers['title']; ?>" >	
									</div>
									<div style="display: none;color: red;" id="title_err"> Please enter title </div> 
								</div> -->
								<div class="form-group col-lg-3 p-r-5" style="float: left;">
									<span class="form-label">First Name <span class="text-danger">*</span></span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="first_name" name="first_name" value="<?php echo $customers['first_name']; ?>" required>
									</div>
									<div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div> 
								</div>
								<div class="form-group col-lg-3 p-r-5" style="float: left;">
									<span class="form-label">Middle Name</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="middle_name" name="middle_name" value="<?php echo $customers['middle_name']; ?>" >
									</div>
									<div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div> 
								</div>
								<div class="form-group col-lg-3 p-r-5" style="float: left;">
									<span class="form-label">Last Name <span class="text-danger">*</span></span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="last_name" name="last_name" value="<?php echo $customers['last_name']; ?>" required>
									</div>
									<div style="display: none;color: red;" id="last_name_err"> Please enter last name </div> 
								</div>
								<div class="form-group col-lg-3">
									<span class="form-label">Email <span class="text-danger">*</span></span>
									<div class="kt-input-icon">
										<input class="form-control" id="email" name="email" type="email" onblur="checkalreadyavailableornotcustomerorvendordetails('EMAIL')" value="<?php echo $customers['email']; ?>" required>
									</div>
									<span class="text-danger f-14" id="emailerror"  style="display:none"></span>
									<div style="display: none;color: red;" id="email_err"> Please enter email </div> 
								</div>
								<div class="form-group col-lg-3">
									<span class="form-label">Phone</span>
									<div class="kt-input-icon">
										<input class="form-control" type="number" id="phone" name="phone" value="<?php echo $customers['phone']; ?>" >	
									</div>
									<div style="display: none;color: red;" id="phone_err"> Please enter phone </div>  
								</div>
								<div class="form-group col-lg-3 p-r-5" style="float: left;">
									<span class="form-label">Suffix</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="suffix" name="suffix" value="<?php echo $customers['suffix']; ?>" >
									</div>
									<div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div> 
								</div>
								<div class="form-group col-lg-3 ">
									<span class="form-label">Company <span class="text-danger">*</span></span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="company" name="company" value="<?php echo $customers['company']!=""?$customers['company']:$_GET['COMPNAME']; ?>" required>	
									</div>
									<div style="display: none;color: red;" id="company_err"> Please enter company </div>  
								</div>
								<div class="form-group col-lg-3 ">
									<span class="form-label">Display Name As</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="display_name" name="display_name" value="<?php echo $customers['display_name']; ?>" >	
									</div>
									<div style="display: none;color: red;" id="display_name_err"> Please enter display name </div>
								</div>
								<div class="form-group col-lg-3 w-full">
									<span class="form-label">Parent group</span>
									<select name="parent_customer" class="form-select js-example-basic-single">
										<option value="">Select Any One</option>
										<?php foreach($customers_list as $venlist) { ?>
											<option <?php echo $customers['parent_customer']==$venlist['id'] ? 'selected':'' ?> value="<?php echo $venlist['id'];?>"><?php echo $venlist['company']; ?></option>
										<?php } ?>
									</select>

									<div style="display: none;color: red;" id="email_err1"> Please enter parent_customer </div> 
								</div>
								<!-- <div class="form-group col-lg-3">
									<span class="form-label">Bill With Parent</span>
									<div class="kt-input-icon">
										<input class="form-control" type="number" id="bill_with_parent" name="bill_with_parent" value="<?php echo $customers['bill_with_parent']; ?>" >	
									</div>
									<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
								</div> -->
								<!-- <div class="form-group col-lg-3">
									<span class="form-label">Language</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="language_id" name="language_id" value="<?php echo $customers['language_id']; ?>" >	
									</div>
									<div style="display: none;color: red;" id="company_err"> Please enter bill_with_parent </div>  
								</div>
								<div class="col-lg-3" style="float: left;">
									<span class="form-label">Hold Reason</span>
									<div class="kt-input-icon">
										<input class="form-control" type="text" id="hold_reason" name="hold_reason" value="<?php echo $customers['hold_reason']; ?>" >
									</div>
								</div> -->
								<div class="form-group col-lg-3 p-r-5" style="float: right;">
										<span class="form-label">Website</span>
										<div class="kt-input-icon">
											<input class="form-control" type="text" id="website_url" name="website_url" value="<?php echo $customers['website_url']; ?>" >
										</div>
										<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
									</div>
									<div class="form-group col-lg-3 p-r-5" style="float: right;">
										<span class="form-label">Short Code</span>
										<div class="kt-input-icon">
											<input class="form-control" type="text" id="short_name" name="short_name" value="<?php echo $customers['short_name']; ?>" >
										</div>
										<div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>  
									</div>
								<div class="col-lg-3" style="float: left;">
									<span class="form-label">Photo</span>
									<div class="col-lg-9 col-xl-6">
										<div class="profile-pic">
											<label class="-label" for="display_logo">
												<span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
											</label>
											<input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
											<img src="<?php echo $image; ?>" id="outputdisplay_logo" width="200"  onerror="this.onerror=null;this.src='uploads/all/no-image.jpg';"/>
											<input type="hidden" name="hid_image" value="<?php echo $customers['image'] ;  ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div>
					<div class="tab-pane" id="contact-details" role="tabpanel">
						<div id="gst-tab-details">
							<?php 
							if(count((array)$customer_vendor_gst_billing_details) =='0'){
								$customer_vendor_gst_billing_details[] = array();
							}
							$mkseq = 1;
							foreach($customer_vendor_gst_billing_details as $cvgst){ ?>
							<div class="row sub-title" id="removediv_<?php echo $mkseq; ?>">
								<div class="col-lg-6">
									<input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]" value="<?php echo $cvgst['id']; ?>" >
									<div class="row">
										<div class="col-lg-6 p-r-5 w-full" style="float: left;">
											<span class="form-label d-block">GST  Registration type</span>
											<select id="gsttype" name="gsttype[]" class="form-select js-example-basic-single">
												<option value="">Select Any One</option>
												<?php foreach($gstregistrationtype as $gstkey=>$gstregtype) { ?>
													<option <?php echo $cvgst['gsttype']==$gstkey? 'selected':'' ?> value="<?php echo $gstkey;?>"><?php echo $gstregtype; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-lg-6" style="float: left;">
											<span class="form-label">GST No.</span>
											<div class="kt-input-icon">
												<input class="form-control" type="text" id="gstin" name="gstin[]" onblur="checkalreadyavailableornotcustomerorvendordetails('GSTIN', this)"  value="<?php echo $cvgst['gstin']; ?>" >
											<span class="text-danger f-14 gstinerror" style="display:none"></span>	
											
											</div>
											<div style="display: none;color: red;" id="gstin_err"> Please enter GST IN </div> 
										</div>
										<div class="form-group col-lg-12">
											<span class="form-label">Billing Adderss</span>
											<div class="kt-input-icon">
												<textarea type="text" class="form-control" placeholder="Billing Adderss" name="billing_address[]" id="billing_address_<?php echo $mkseq; ?>"><?php echo $cvgst['billing_address']; ?></textarea>
											</div> 
										</div>
										<div class="form-group col-lg-3 w-full" >
											<span class="form-label">Country</span>
											<select id="billing_country_<?php echo $mkseq; ?>" class="form-select js-example-basic-single" name="billing_country[]"  onchange="selectedcategory(this.value,'billing_state_<?php echo $mkseq; ?>','states','country_id','selectedbillcountry')">
												<option value="">Select Any One</option>
												<?php foreach($countrieslist as $countryvalue) { ?>
													<option <?php echo $cvgst['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-lg-3  w-full" >
											<span class="form-label">State</span>
											<select id="billing_state_<?php echo $mkseq; ?>" name="billing_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'billing_city_<?php echo $mkseq; ?>','cities','state_id','selectedbillstate')">
												<option value="">Select Any One</option>
												<?php foreach($states as $states1) { ?>
													<option <?php echo $cvgst['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
												<?php } ?>
											</select>

										</div>
										<div class="form-group col-lg-3 p-r-5  w-full" >
											<span class="form-label">City/Town</span>
											<select id="billing_city_<?php echo $mkseq; ?>" name="billing_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
												<option value="">Select Any One</option>
												<?php foreach($cities as $cities1) { ?>
													<option <?php echo $cvgst['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-lg-3 p-r-5" >
											<span  class="form-label">Pincode</span>
											<input class="form-control" placeholder="Pincode" type="number" id="billing_post_code_<?php echo $mkseq; ?>" name="billing_post_code[]" value="<?php echo $cvgst['billing_post_code']; ?>" >

										</div>
										<div class="form-group col-lg-3 p-r-5" style="float: right;">
											<span class="form-label">Contact Person</span>
											<input class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person[]" value="<?php echo $cvgst['contact_person']; ?>" >

										</div>
										<div class="form-group col-lg-3 p-r-5" style="float: left;">
											<span class="form-label">Mobile</span>
											<div class="kt-input-icon">
												<input class="form-control" type="number" id="mobile" name="mobile[]" value="<?php echo $cvgst['mobile']; ?>" >	
											</div>
										</div>
										<div class="form-group col-lg-3 p-r-5" style="float: right;">
											<span class="form-label">Fax</span>
											<div class="kt-input-icon">
												<input class="form-control" type="number" id="fax" name="fax[]" value="<?php echo $cvgst['fax']; ?>" >
											</div>
										</div>
									</div>
								</div>	
								<div class="col-lg-6" style="float: left;">

									<div class="row">
										<div class="form-group col-lg-9" >
											<span class="form-label d-block"></span>
											<div class="border-checkbox-section  ">
		                                      	<div class="border-checkbox-group border-checkbox-group-primary m-t-30">
		                                        	<input class="border-checkbox" type="checkbox" id="checkbox<?php echo $mkseq; ?>" onclick="cloneShipping(this,'<?php echo $mkseq; ?>')">
		                                        	<label class="form-label border-checkbox-label" for="checkbox<?php echo $mkseq; ?>">My shipping address is the same as my billing address</label>
		                                      	</div>
		                                    </div>
										</div>
										<div class="col-lg-3 text-right d-flex flex-column justify-content-between ">
											<span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer" onclick="removeGSTDiv(<?php echo $mkseq; ?>)">
												<i class="ti-trash text-danger"></i> Remove
											</span>
											<div class="form-group ">
                                            <input type="radio" id="is_default"  name="is_default" value="<?php echo $mkseq-1; ?>" <?php 
                                                echo ($cvgst['is_default'] == 'Y' || ($mkseq-1==0) ) ? 'checked' : '';?> ><span>Make Default</span></div>
										</div>
										<!-- <div class="col-lg-3 text-right">
											<span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer" onclick="removeGSTDiv(<?php echo $mkseq; ?>)">
												<i class="ti-trash text-danger"></i> Remove
											</span>
										</div> -->
										<div class="form-group col-lg-12" >
											<span class="form-label">Shipping Adderss</span>
											<div class="kt-input-icon">
												<textarea type="text" class="form-control" placeholder="Street" name="shipping_address[]" id="shipping_address_<?php echo $mkseq; ?>"><?php echo $cvgst['shipping_address']; ?></textarea>
											</div> 
										</div>
										<div class="form-group col-lg-3  w-full" >
											<span class="form-label">Country</span>
											<select id="shipping_country_<?php echo $mkseq; ?>" class="form-select js-example-basic-single"  name="shipping_country[]" onchange="selectedcategory(this.value,'shipping_state_<?php echo $mkseq; ?>','states','country_id','selectedshipcountry')">
												<option value="">Select Any One</option>
												<?php foreach($countrieslist as $countryvalue) { ?>
													<option <?php echo $cvgst['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-lg-3 w-full">
											<span class="form-label">State</span>
											<select id="shipping_state_<?php echo $mkseq; ?>" name="shipping_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'shipping_city_<?php echo $mkseq; ?>','cities','state_id','selectedshipstate')">
												<option value="">Select Any One</option>
												<?php foreach($states as $states1) { ?>
													<option <?php echo $cvgst['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
												<?php } ?>

											</select>
										</div>
										<div class="form-group col-lg-3 p-r-5 w-full">
											<span class="form-label">City/Town</span>
											<select id="shipping_city_<?php echo $mkseq; ?>" name="shipping_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
												<option value="">Select Any One</option>
												<?php foreach($cities as $cities1) { ?>
													<option <?php echo $cvgst['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-lg-3" >
											<span class="form-label">Pincode</span>
											<input class="form-control" placeholder="Pincode" type="number" id="shipping_post_code_<?php echo $mkseq; ?>" name="shipping_post_code[]" value="<?php echo $cvgst['shipping_post_code']; ?>" >
										</div>									
									</div>
								</div>
							</div>
						<?php $mkseq++; } ?>
						</div>
						<div class="">
							<button class="btn btn-success  col-lg-3" onclick="addMoreGSTBillingDetails()" type="button">+ Add More GST & Billing Details</button>
						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div>
					<div class="tab-pane" id="note" role="tabpanel">
						<div class="row ">
							<div class="col-lg-6 p-r-5" style="float: left;">
								<span class="form-label">PO Notes</span>
								<textarea type="text" name="customer_inv_notes"  class="form-control" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $customers['customer_inv_notes']; ?></textarea>
							</div>
							<div class="col-lg-6" style="float: left;">
								<span class="form-label">Other Notes</span>
								<textarea type="text" name="customer_stmnt_notes"   class="form-control" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $customers['customer_stmnt_notes']; ?></textarea>
							</div>
						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div>
					<div class="tab-pane" id="tax-info" role="tabpanel">
						<div class="from row">
							<div class="col-lg-12" style="border-right: dotted 1px solid;">
								<div class="from row">
									<div class="col-lg-6" style="margin-bottom: 2rem;">
										<span class="form-label">Tax Reg. No.</span>
										<div class="kt-input-icon">
											<input class="form-control" type="text" id="customer_tax_reg_no" name="customer_tax_reg_no" value="<?php echo $customers['customer_tax_reg_no']; ?>" >
										</div> 
									</div>
									<div class="col-lg-6" style="margin-bottom: 2rem;">
										<span class="form-label">VAT/CST Reg. No.</span>
										<div class="kt-input-icon">
											<input class="form-control" type="text" id="cst_reg_no" name="cst_reg_no" value="<?php echo $customers['cst_reg_no']; ?>" >
										</div> 
									</div>
									<div class="col-lg-6" style="margin-bottom: 2rem;">
										<span class="form-label">PAN No.</span>
										<div class="kt-input-icon">
											<input class="form-control" type="text" id="pan_no" name="pan_no" onblur="checkalreadyavailableornotcustomerorvendordetails('PAN_NO')" value="<?php echo $customers['pan_no']; ?>" >
										</div>
										<span class="text-danger f-14" id="pannoerror"  style="display:none"></span>
										<div style="display: none;color: red;" id="pan_no_err"> Please Enter PAN No. </div> 
									</div>
									<div class="col-lg-6" style="margin-bottom: 2rem;">
										<span class="form-label">  </span>
										<div class="kt-checkbox-inline">
											<span class="kt-checkbox">
												<input type="checkbox" name="tds_apply" class="js-switch" <?php echo $customers['tds_apply'] == "0"?"checked":"";?>> Apply TDS for this customer
												<span></span>
											</span>
										</div>
									</div>
								</div>
							</div>
<?php /**/ ?>
							<div class="col-lg-12">
							
								<div class="row">
									<div class="col-lg-4">
										<span class="form-label">LUT No.</span>
									</div>
									<div class="col-lg-4 ">
										<span class="form-label">Bond No.</span>
									</div>
									<div class="col-lg-4">
										<span class="form-label">Finance Year</span>
									</div>
								</div>
								<?php 
								// Check if lutbond has data
								if (is_array($lutbond) && !empty($lutbond)) {
									// Loop through lutbond records
									foreach ($lutbond as $index => $bond) {
								?>
								<div class="row mb-2 tax-row" data-row-id="<?php echo $index + 1; ?>" <?php echo ($bond['lock_status'] == 0) ? "style='pointer-events: none; opacity: 0.7;' " : ""; ?>>
									<div class="col-lg-4 ">
										<input type="hidden" name="alreadyupdated[<?php echo $bond['id']; ?>]" value="<?php echo $bond['lock_status']; ?>">
										<input  class="form-control" type="text" 
											name="lut_no[]"
											value="<?php echo $bond['lut_no']; ?>">
										<input type="hidden" name="lutbond_id[]" value="<?php echo $bond['id']; ?>">
									</div>
									<div class="col-lg-4">
										<input  class="form-control" type="text" name="bond_no[]"	value="<?php echo $bond['bond_no']; ?>">
									</div>
									<div class="col-lg-2 w-full">
										<select  class="form-select js-example-basic-single"
											name="finance_year[]" onchange="updateFinancialDates(this)">
											<option value="">Select Any One</option>
											<?php for ($i = date("Y") - 10; $i <= date("Y"); $i++) { 
												// Extract year from Start_date if available
												$financeYear = '';
												if (!empty($bond['start_date'])) {
													$financeYear = date('Y', strtotime($bond['start_date']));
												}
											?>
												<option value="<?php echo $i; ?>" <?php echo ($i == $financeYear) ? "selected" : ""; ?>>
													<?php echo "Apr " . $i . " - Mar " . ($i + 1); ?>
												</option>
											<?php } ?>
										</select>
										<input type="hidden" name="start_date[]" class="start-date-field" value="<?php echo $bond['start_date']; ?>">
										<input type="hidden" name="end_date[]" class="end-date-field" value="<?php echo $bond['end_date']; ?>">
									</div>
									<div class="col-lg-2 d-flex align-items-center">
										<button type="button" class="btn btn-sm lock-btn <?php echo ($bond['lock_status'] == 0) ? 'btn-success locked' . ($bond['id'] ? ' permanently-locked' : '') : 'btn-warning'; ?>" onclick="toggleRowLock(this)">
											<?php if ($bond['lock_status'] == 0) { ?>
												<?php if ($bond['id']) { ?>
													<i class="fa fa-lock"></i> Permanent
												<?php } else { ?>
													<i class="fa fa-lock"></i> Locked
												<?php } ?>
											<?php } else { ?>
												<i class="fa fa-unlock"></i> Lock
											<?php } ?>
										</button>
										<input type="hidden" name="lock_status[]" value="<?php echo $bond['lock_status']; ?>">
										<button type="button" class="btn btn-danger btn-sm remove-btn d-none ms-2" onclick="removeTaxRow(this)">
										    <i class="fa fa-minus"></i>
										</button>
									</div>
								</div>
								<?php 
									} // End foreach
								} else {
									// Create one empty row if no data
								?>
								<div class="row mb-2 tax-row" data-row-id="1">
									<div class="col-lg-4 ">
										<input  class="form-control" type="text" 
											name="lut_no[]"
											value="">
										<input type="hidden" name="lutbond_id[]" value="">
									</div>
									<div class="col-lg-4">
										<input  class="form-control" type="text" 
											name="bond_no[]"
											value="">
									</div>
									<div class="col-lg-2  w-full">
										<select  class="form-select js-example-basic-single"
											name="finance_year[]" onchange="updateFinancialDates(this)">
											<option value="">Select Any One</option>
											<?php for ($i = date("Y") - 10; $i <= date("Y"); $i++) { ?>
												<option value="<?php echo $i; ?>">
													<?php echo "Apr " . $i . " - Mar " . ($i + 1); ?>
												</option>
											<?php } ?>
										</select>
										<input type="hidden" name="start_date[]" class="start-date-field" value="">
										<input type="hidden" name="end_date[]" class="end-date-field" value="">
									</div>
									<div class="col-lg-2 d-flex align-items-center">
										<button type="button" class="btn btn-sm btn-warning lock-btn" onclick="toggleRowLock(this)">
											<i class="fa fa-unlock"></i> Lock
										</button>
										<input type="hidden" name="lock_status[]" value="1">
										<button type="button" class="btn btn-danger btn-sm ms-2" onclick="removeTaxRow(this)"><i class="fa fa-minus"></i></button>
									</div>
								</div>
								<?php } ?>
								<div class="col-12 text-start">
									<button type="button" class="btn btn-success btn-sm" onclick="addTaxRow()"><i class="fa fa-plus"></i> Add New Row</button>
								</div>
							



							</div>
							<?php /**/ ?>
						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div>
					<div class="tab-pane" id="payment-billing" role="tabpanel">
						<div class="from row">
							<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
								<span class="form-label d-block">Preferred Payment Method</span>
								<select name="pref_payment_method" class="form-control form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($payment_method as $key=>$pm) { ?>
										<option value="<?php echo $key; ?>" <?php echo $customers['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
								<span class="form-label">Payment Trems</span>
								<select name="payment_terms" class="form-control form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($paymenttype as $key=>$st) { ?>
										<option value="<?php echo $key; ?>" <?php echo $customers['payment_terms']==$key?'selected':'';?> ><?php echo $st; ?></option>
									<?php } ?>
								</select>
							</div>
							<!-- <div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
								<span class="form-label">Preferred Payment Method</span>
								<select name="payment_terms" class="form-control form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($payment_method as $key=>$pm) { ?>
										<option value="<?php echo $key; ?>" <?php echo $vendordetail['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
									<?php } ?>
								</select>
							</div> -->
							<div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
								<span class="form-label d-block">Preferred Delivery Method</span>
								<select name="preferred_deliver_method" class="form-control form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($payment_method as $key=>$pm) { ?>
										<option value="<?php echo $key; ?>" <?php echo $customers['preferred_deliver_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-lg-4" style="margin-bottom: 2rem;">
								<span class="form-label">Opening Blance</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="number" id="opening_balance" name="opening_balance" value="<?php echo $customers['opening_balance']; ?>" >
								</div>
							</div>
							<div class="col-lg-2" style="margin-bottom: 2rem;">
								<span class="form-label">As of</span>
								<div class="kt-input-icon">
									<input class="form-control datepicker" type="text" id="asof" name="asof" value="<?php echo fromDate($customers['asof']); ?>" >
								</div>
							</div>
							<div class="col-lg-6" style="margin-bottom: 2rem;">
								<span class="form-label">The Vendor Pays Me With</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="text" id="pay_with_me" name="pay_with_me" value="<?php echo $customers['pay_with_me']; ?>" >
								</div>
							</div>
							<div class="col-lg-3" style="margin-bottom: 2rem;">
								<span class="form-label">Credit Limit</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="number" id="credit_limit" name="credit_limit" value="<?php echo $customers['credit_limit']; ?>" >
								</div>
							</div>
							<div class="col-lg-3" style="margin-bottom: 2rem;">
								<span class="form-label">Account Number</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="text" id="account_code" name="account_code" value="<?php echo $customers['account_code']; ?>" >
								</div>
							</div>


						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div>
					<div class="tab-pane" id="sale-discount" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-6" style="margin-bottom: 2rem;">
								<span class="form-label">Default Discount Percentage (%)</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="number" id="default_discount_perc" name="default_discount_perc" value="<?php echo $customers['default_discount_perc']; ?>" >
								</div> 
							</div>
							<div class="col-lg-6" style="margin-bottom: 2rem;">
								<span class="form-label">Payment Discount Percentage (%)</span>
								<div class="kt-input-icon">
									<input class="form-control text-right" type="number" id="payment_discount_perc" name="payment_discount_perc" value="<?php echo $customers['payment_discount_perc']; ?>" >
								</div> 
							</div>
							<!-- <div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
								<span class="form-label">Sales Type</span>
								<select name="sales_type" class="form-control form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($salestype as $key=>$st) { ?>
										<option value="<?php echo $key; ?>" <?php echo $customers['sales_type']==$key?'selected':'';?> ><?php echo $st; ?></option>
									<?php } ?>
								</select>
							</div> -->
						</div>
						<button  type="submit" name="submit" class="btn btn-primary m-t-20"> <?php if($customers['id'] !=""){ ?><i class="fa fa-check-square"></i> Update <?php }else{  ?><i class="icofont icofont-plus"></i> Submit<?php } ?> </button>
					</div><?php /** ?>
					<div class="tab-pane" id="agreements" role="tabpanel">
						<div class="p-3">
							<h4 class="mb-10 font-weight-bold text-dark">Agreemets</h4>
							<div class="row">
								<div class="form-group col-lg-12">
									<table class="table table-bordered " id="edutable">
										<tr>
											<th class="" width="2%">Sl NO</th>
											<th class="" width="25%">Type</th>
											<th class="thheader" width="20%">Number</th>
											<th class="" width="20%"> Sign Date</th>
											<th class="thheader" width="20%">Expiry Date </th>
											<th class="thheader" width="15%">Files</th>
										</tr>
									<?php $k=0;  if(count((array)$aggrements) != '0'){ $aggrements[] = array('agreement_name'=>''); }
									foreach ($aggrements as $inst) { $k++;?>
										<tr class="rowcount">

											<td><?php echo $k; ?>

											<input type="hidden" name="ag_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>" autocomplete="off">
										</td>
										<td><input type="text" name="ag_type[]" class="form-control" value="<?php echo $inst['agreement_name']; ?>" autocomplete="off">  </td>
										<td>
											<input type="text" name="ag_number[]" class="form-control" value="<?php echo $inst['agreement_ref_no']; ?>" autocomplete="off">
										</td>
										<td><input type="text" name="ag_sin_date[]" class="form-control datepicker "  id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo fromDate($inst['signed_date']); ?>"  autocomplete="off">  </td>
										<td><input type="text" name="ag_ex_date[]" class="form-control datepicker" id="kt_datepicker_3<?php echo $k; ?>"  value="<?php echo fromDate($inst['expiry_date']); ?>" autocomplete="off">  </td>
										<td>
											<div class="row m-t-15">
												<div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;">
													<span class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" >
														<i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i>
														<input type="file" name="agreement_file<?php echo $dockey;?>[]" />
														<input type="hidden" name="hidden_agreement_file<?php echo $dockey;?>" value="<?php echo $inst['file_url'][$dockey]; ?>"/>
													</span>
												</div>
												<?php if($inst['file_url']!=''){ ?>
													<div class="col-lg-3">
														<a href="<?php echo $File_Uploads_Path."/".$inst['file_url']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
															<i class="ti-cloud-down f-20 text-warning f-30 f-w-800"></i>
														</a></div>
													<?php } ?>
												</div>
											</td>
										</tr>
									<?php }  ?>
								</table>
								<div class="row">
									<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
										<!--  <button class="btn btn-danger float-left delete " id="removeRows" onclick="removerows()"  type="button">Delete</button> -->
										<button class="btn btn-success float-left" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> <?php **/ ?>
				<div class="tab-pane" id="attachments" role="tabpanel">
					<div class="form-group row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<?php fileUplodingUi(['application_table_name'=>'customers','transaction_id'=>$rid ,'is_active'=>'0','width'=>'12','deletewidth'=>'6','deleteview'=>'0','file_size'=>'597152','file_limit'=>'2']);  ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="reantal_properties" role="tabpanel">
					<div class="row">
					<div class="col-lg-12" id="tablshrink">
						<div class="table-responsive">
							<div class="card-header"><h5 class="text-primary">Rental Properties</h5></div>
							<?php if(count((array)$property_tenant_details)>0){  ?>
								<table class="table table-hover m-b-0">
									<thead>
										<tr>
											<th>Owner Name</th>
											<th>Property Name</th>
											<th>Address</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th>Rented Area</th>
											<th>Rent</th>
											<th>Invoice</th>
											<th>Last Paid</th>
										</tr>
									</thead>
									<tbody>
										<?php $k=0;  
										foreach ($property_tenant_details as $inst) { 
											$k++;
											 $areaary = json_decode($inst['property_owner_detail_areas'],1);
											 $totalamt = 0;
											 foreach($areaary as $rentar){
											 	$totalamt +=$rentar['rent']; 
											 }
											 ?>
											<tr onclick="getProperty('<?php echo deocdeId("id:".$inst['id']); ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
												
												<td><?php echo ucwords($property_owners[$inst['property_owner_id']]['owner_name']); ?></td>
												<td><?php echo ucwords($property_details[$inst['property_owner_detail_id']]['property_name']); ?></td>
												<td><?php echo emptytextdashShow($property_details[$inst['property_owner_detail_id']]['property_address']); ?></td>
												<td><?php echo fromDate($inst['rent_commencement_date']); ?></td>
												<td><?php echo fromDate($inst['lease_period_end_date']); ?></td>
												<td><?php echo count($areaary); ?></td>
												<td class="text-right"><?php echo customeummarmonyformateifine($totalamt); ?></td>
												<td class="text-center"> - </td>
												<td><?php echo fromDate($inst['rent_commencement_date']); ?></td>
											</tr>

										<?php }  ?>
									</tbody>
								</table>
							<?php }else{ ?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Rental Properties</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
									</div>   
								</div>
							<?php } ?>
						</div>
					</div>
					<div id="showdivpagehtml">
						<?php //include('custom_insert_property_tenant_details.php'); ?>
					</div>
				</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
</form>

<script type="text/javascript">
	//var mkseq = 100000;
	var mkseq = <?php echo count((array)$customer_vendor_gst_billing_details) ?? 1;?>;
	console.log('mkseq', mkseq)
	function addMoreGSTBillingDetails(){
		mkseq++;
		console.log(mkseq);
		var html =`<div class="row sub-title" id="removediv_`+mkseq+`">
					<div class="col-lg-6">
						<div class="row"><input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]" value="" >
							<div class="col-lg-6 p-r-5 w-full" style="float: left;">
								<span class="form-label d-block">GST  Registration type</span>
								<select  name="gsttype[]" class="form-select js-example-basic-single">
									<option value="">Select Any One</option>
									<?php foreach($gstregistrationtype as $gstkey=>$gstregtype) { ?>
										<option <?php echo $customers['gsttype']==$gstkey? 'selected':'' ?> value="<?php echo $gstkey;?>"><?php echo $gstregtype; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-lg-6" style="float: left;">
								<span class="form-label">GSTIN</span>
								<div class="kt-input-icon">
									<input class="form-control" type="text" name="gstin[]" onblur="checkalreadyavailableornotcustomerorvendordetails('GSTIN', this)" value="" >
									<span class="text-danger f-14 gstinerror" style="display:none"></span>
								</div>
							</div>
							<div class="form-group col-lg-12">
								<span class="form-label">Billing Adderss</span>
								<div class="kt-input-icon">
									<textarea type="text" class="form-control" placeholder="Billing Adderss" name="billing_address[]" id="billing_address_`+mkseq+`"></textarea>
								</div> 
							</div>
							<div class="form-group col-lg-3 w-full" >
								<span class="form-label">Country</span>
								<select id="billing_country_`+mkseq+`" class="form-select js-example-basic-single" name="billing_country[]"  onchange="selectedcategory(this.value,'billing_state_`+mkseq+`','states','country_id','selectedbillcountry')">
									<option value="">Select Any One</option>
									<?php foreach($countrieslist as $countryvalue) { ?>
										<option value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-lg-3  w-full" >
								<span class="form-label">State</span>
								<select id="billing_state_`+mkseq+`" name="billing_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'billing_city_`+mkseq+`','cities','state_id','selectedbillstate')">
									<option value="">Select Any One</option>
								</select>

							</div>
							<div class="form-group col-lg-3 p-r-5  w-full" >
								<span class="form-label">City/Town</span>
								<select id="billing_city_`+mkseq+`" name="billing_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
									<option value="">Select Any One</option>
								</select>
							</div>
							<div class="form-group col-lg-3 p-r-5" >
								<span  class="form-label">Pincode</span>
								<input class="form-control" placeholder="Pincode" type="number" id="billing_post_code_`+mkseq+`" name="billing_post_code[]" value="" >

							</div>
							<div class="form-group col-lg-3 p-r-5" style="float: right;">
								<span class="form-label">Contact Person</span>
								<input class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person[]" value="" >

							</div>
							<div class="form-group col-lg-3 p-r-5" style="float: left;">
								<span class="form-label">Mobile</span>
								<div class="kt-input-icon">
									<input class="form-control" type="number" id="mobile" name="mobile[]" value="" >	
								</div>
							</div>
							<div class="form-group col-lg-3 p-r-5" style="float: right;">
								<span class="form-label">Fax</span>
								<div class="kt-input-icon">
									<input class="form-control" type="number" id="fax" name="fax[]" value="" >
								</div>
							</div>
						</div>
					</div>	
					<div class="col-lg-6" style="float: left;">
						<div class="row">
							<div class="form-group col-lg-9" >
								<span class="form-label d-block"></span>
								<div class="border-checkbox-section  ">
									<div class="border-checkbox-group border-checkbox-group-primary m-t-30">
										<input class="border-checkbox" type="checkbox" id="checkbox`+mkseq+`" onclick="cloneShipping(this,`+mkseq+`)">
										<label class="form-label border-checkbox-label" for="checkbox`+mkseq+`">My shipping address is the same as my billing address</label>
									</div>
								</div>
							</div>
							<div class="col-lg-3 text-right d-flex flex-column justify-content-between text-right">
								<span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer" onclick="removeGSTDiv(`+mkseq+`)">
									<i class="ti-trash text-danger"></i> Remove
								</span>
								<div class="form-group ">
									<input type="radio"  name="is_default" value="`+(mkseq-1)+`" >
									<span>Make Default</span>
								</div>
							</div>        	
							<div class="form-group col-lg-12" >
								<span class="form-label">Shipping Adderss</span>
								<div class="kt-input-icon">
									<textarea type="text" class="form-control" placeholder="Street" name="shipping_address[]" id="shipping_address_`+mkseq+`"></textarea>
								</div> 
							</div>
							<div class="form-group col-lg-3  w-full" >
								<span class="form-label">Country</span>
								<select id="shipping_country_`+mkseq+`" class="form-select js-example-basic-single"  name="shipping_country[]" onchange="selectedcategory(this.value,'shipping_state_`+mkseq+`','states','country_id','selectedshipcountry')">
									<option value="">Select Any One</option>
									<?php foreach($countrieslist as $countryvalue) { ?>
										<option  value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-lg-3 w-full">
								<span class="form-label">State</span>
								<select id="shipping_state_`+mkseq+`" name="shipping_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'shipping_city_`+mkseq+`','cities','state_id','selectedshipstate')">
									<option value="">Select Any One</option>
								</select>
							</div>
							<div class="form-group col-lg-3 p-r-5 w-full">
								<span class="form-label">City/Town</span>
								<select id="shipping_city_`+mkseq+`" name="shipping_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
									<option value="">Select Any One</option>
								</select>
							</div>
							<div class="form-group col-lg-3" >
								<span class="form-label">Pincode</span>
								<input class="form-control" placeholder="Pincode" type="number" id="shipping_post_code_`+mkseq+`" name="shipping_post_code[]" value="" >
							</div>									
						</div>
					</div>
				</div>`;
				$("#gst-tab-details").append(html);
				$(".js-example-basic-single").select2();


				// <div class="col-lg-3 text-right">
				// 				<span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer" onclick="removeGSTDiv(`+mkseq+`)">
				// 					<i class="ti-trash text-danger"></i> Remove
				// 				</span>
				// 			</div>

	}

	function removeGSTDiv(seq) {
		var block = $("#removediv_" + seq);
		var radio = block.find("input[name='is_default']");
		if (radio.is(":checked")) {
			var others = $("input[name='is_default']").not(radio);
			if (others.length === 0) {
				tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "At least one GST & Billing detail must be default. Cannot delete this.", '');
				return false;
			}
			if (!others.is(":checked")) {
				$(others[0]).prop("checked", true);
				// alert("Another address has been made default automatically.");
			}
		}
		block.remove();
		// ✅ Renumber remaining radios sequentially
		$("div[id^='removediv_']").each(function(index) {
			var newIndex = index + 0; // start from 1
			$(this).find("input[name='is_default']").val(newIndex);
		});
		// ✅ Ensure at least one remains default
		var checked = $("input[name='is_default']:checked");
		if (checked.length === 0 && $("input[name='is_default']").length > 0) {
			$("input[name='is_default']").first().prop("checked", true);
		}

		var rid   = block.find("input[name='customer_vendor_gst_billing_details_id[]']").val();
        // --- If existing record, soft delete via AJAX ---
        if (rid && rid !== "") {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: {
                    page: "softdeletedrecord",
                    id: rid,
                    table: "customer_vendor_gst_billing_details"
                },
                success: function (data) {
                    console.log("Soft delete response:", data);
                },
                error: function () {
                    console.error("Soft delete failed");
                }
            });
        }
	}

	function cloneShipping(ei,seq){
		if(ei.checked){
			// Country
			var billing_country_val = $("#billing_country_"+seq).val();
			var billing_country_text = $("#billing_country_"+seq+" option:selected").text();
			var newOption1 = new Option(billing_country_text, billing_country_val, true, true);
			$("#shipping_country_"+seq).append(newOption1);

			// Wait until states are loaded
				var billing_state_val = $("#billing_state_"+seq).val();
				var billing_state_text = $("#billing_state_"+seq+" option:selected").text();
				var newOption2 = new Option(billing_state_text, billing_state_val, true, true);
				$("#shipping_state_"+seq).append(newOption2);

			// Wait until cities are loaded
				var billing_city_val = $("#billing_city_"+seq).val();
				var billing_city_text = $("#billing_city_"+seq+" option:selected").text();
				var newOption3 = new Option(billing_city_text, billing_city_val, true, true);
				$("#shipping_city_"+seq).append(newOption3);


			$("#shipping_address_"+seq).val($("#billing_address_"+seq).val());
			$("#shipping_post_code_"+seq).val($("#billing_post_code_"+seq).val());
		}
	}
	function getProperty(property_tenant_detail_id){
		console.log("work - "+property_tenant_detail_id);
		var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
		$('#showdivpagehtml').html(htmloader);

		$("#tablshrink").removeClass('col-lg-12');
		$("#tablshrink").addClass('col-lg-3',100);
		jQuery.ajax({
			url: "views/custom_insert_property_tenant_details.php",
			data:'ajaxcall=Y&Rid='+property_tenant_detail_id,
			type: "GET",
			success:function(htmlRows){
				console.log(htmlRows);
				$('#showdivpagehtml').html(htmlRows);
				$("#showdivpagehtml").addClass('col-lg-9',100);
			}
		});
	}

	function checkalreadyavailableornotcustomerorvendordetails(field, el = null){
		$('#emailerror').hide();
		$('.gstinerror').hide();
		$('#pannoerror').hide();
		var fieldName = '';
		var fieldValue = '';
		if(field  == 'EMAIL'){
			fieldName = 'email';
			fieldValue = $("#email").val().trim();
		}else if (field == 'GSTIN') {
			fieldName = 'gstin';
			fieldValue = $(el).val().trim(); // GST input value of this row
		}else if(field == 'PAN_NO'){
			fieldName = 'pan_no';
			fieldValue = $("#pan_no").val().trim();
		}
		console.log('fieldValue', (fieldValue));
		// $("#allreadycompanyname").hide();
        if(fieldValue !== ""){
			console.log('Start');
            $.ajax({
                    url: "ajax.php",
                    data:`page=checkalreadyavailableornotcustomerorvendordetails&table_name=customers&field_name=${fieldName}&field_value=${fieldValue}`,
                    type: "POST",
                    async: false,
                    success:function(data){                        
                    var res = JSON.parse(data);
					if(res.result != ''  &&  res.result != null){
						console.log(res.result);
						if(field  == 'EMAIL'){
							$('#emailerror').text(fieldValue+" Already Register");
							$('#emailerror').show();
							$("#email").val('');
						}
						if (field === 'GSTIN' && el) {
							$(el).siblings(".gstinerror")
								.text(fieldValue + " Already Registered")
								.show();
							$(el).val('');
						}
						if(field  == 'PAN_NO'){
							$('#pannoerror').text(fieldValue+" Already Register");
							$('#pannoerror').show();
							$("#pan_no").val('');
						}
					}
                    },
                    error:function (){}
                });
        
        }
		console.log('end');
    }
</script>