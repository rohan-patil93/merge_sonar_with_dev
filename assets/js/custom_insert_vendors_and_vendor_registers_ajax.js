window.addEventListener("load", function() {
	$(".loader-bg").addClass('d-none');
  	$(document).on("click", ".checkVendorValidationEmpty", function () {
      	checkVendorValidationEmpty();
  	});

  	$(document).on("change", ".msme_register_yes_no", function () {
      	getmSMENumber();
  	});

  	$(document).on("change", ".onchangeVRQAnswer", function () {
  		var valuess = $(this).val();
      	onchangeVRQAnswer(valuess);
  	});

  	$(document).on("change", ".onchangeAssociatedAnswer", function () {
  		var valuess = $(this).val();
      	onchangeAssociatedAnswer(valuess);
  	});

  	$(document).on("click", ".addMoreGSTBillingDetails", function () {
  		addMoreGSTBillingDetails();
  	});

  	$(document).on("click", ".removeGSTDiv", function () {
  		var gstdiv = $(this).data("gstdiv");
  		removeGSTDiv(gstdiv);
  	});


  	$(document).on("change", ".cloneShipping", function () {
  		
  		var thiss = $(this);
  		var seq = $(this).data("seq");
  		cloneShipping(thiss, seq);
  	});
  	
  	$(document).on("blur", ".checkCompanyNameAllReadyAvilableOrNot", function () {
  		checkCompanyNameAllReadyAvilableOrNot();
  	});

  	$(document).on("blur", ".checkalreadyavailableornotcustomerorvendordetails", function () {
  		var thiss = $(this);
  		var type = $(this).data("type");
  		checkalreadyavailableornotcustomerorvendordetails(type, thiss);
  	});

  	$(document).on("click", ".printDivVendor", function () {
      	printDivVendor();
  	});

$(document).on("click", ".addDocumentRows", function () {
      	addDocumentRows();
  	});

//Vendor esign
  	$(document).on("click", ".vendoresignpopup", function () {
  		var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
	    jQuery.ajax({
	        url: "views/custom_insert_vendors_and_vendor_register_esign_popup.php",
	        data:'transaction_id='+transaction_id+'&transaction_table='+transaction_table,
	        type: "POST",
	        success:function(data){
	            $('#popup1').html(data);
	            $("#kt_modal_KTDatatable_remote").modal("show");
	            var signPadCanvas = document.querySelector('#signPadCanvas');      
	            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
	            signPadCanvas.getContext("2d").scale(ratio, ratio);
	            signaturePad = new SignaturePad(signPadCanvas, {backgroundColor: 'rgb(218, 220, 220)'});
	        },
	        error:function (){}
	    });
	})


	$(document).on("click", ".confirmSignature", function () {
	    if(signaturePad.isEmpty()) {
	    	tostarnotify('top','right','fa fa-thumbs-down','form-label label label-warning','animated flipInX','animated flipOutX','Signature Pad Is Empty.','');
            $("#submitpopup_btn").addClass("disabled");
        }else{
            $("#submitpopup_btn").removeClass("disabled");
            signBast64JPEGContent = signaturePad.toDataURL("image/jpeg");
           	$('#signPadCanvas').addClass("event_disabled");
           	//$("#my_sign").attr("src",signBast64JPEGContent);
    	    document.getElementById('pickedSignContent').value = signBast64JPEGContent;///testing line, to be removed in production.
	    }   
	});

	$(document).on("click", ".submitromsignForm", function () {
	     var error_message_json = JSON.parse($("#error_message_json").val());
	     var  str = '';
	     var formdata = $("#signature-popup").serialize();
	     $.ajax({
	         url: "ajax.php",
	         type: "POST",
	         data: 'page=insertvendoresign&'+formdata,
	         success: function(response) {
	             tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',error_message_json['9093'],'');
	             $('#kt_modal_KTDatatable_remote').modal('hide');
	             location.reload();      
	         }
	     });
	});

	$(document).on("click", ".enableSignaturePad", function () {
	    signaturePad.clear();
    	$('#signPadCanvas').removeClass("event_disabled");
    	$("#submitpopup_btn").addClass("disabled");
	});

//Vendor esign



  	if ($("#ajaxcall_yes_no").val() != 'Y') { 
		if ($.fn.select2) {
			$('.js-example-basic-single').select2();
		}
		if ($.fn.datepicker) {
			$('.datepicker').datepicker({
				showOn: "both",
				buttonText: "<i class='ti-calendar'></i>",
				dateFormat: (window.date_format || "mm/dd/yy"),
				changeMonth: true,
				changeYear: true
			}).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
		}
		if (window.Switchery) {
			document.querySelectorAll('.js-switch-vendor').forEach(function (el) {
				if (!el._switchery) el._switchery = new Switchery(el, { color: '#4099ff', jackColor: '#fff' });
			});
		}
	}


}); 


function checkVendorValidationEmpty() {
		var company = $.trim($("#company").val());
		var first = $.trim($("#first_name").val());
		var last = $.trim($("#last_name").val());
		var email = $.trim($("#email").val());
		//var pan_no = $.trim($("#pan_no").val());
		$('#company_name_err').addClass('d-none');
		$('#first_name_err').addClass('d-none');
		$('#last_name_err').addClass('d-none');
		$('#email_err').addClass('d-none');
		$("#allreadycompanyname").addClass('d-none');
		$("#pan_no_err").hide();

		if (company === "") {
			console.log('company');
			$('#company_name_err').removeClass('d-none');
			tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter company.", '');
			return false; // stop submit
		}
		if (first === "") {
			console.log('first');
			$('#first_name_err').removeClass('d-none');
			tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter First Name.", '');
			return false; // stop submit
		}
		if (last === "") {
			console.log('last');
			$('#last_name_err').removeClass('d-none');
			tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter Last Name.", '');
			return false; // stop submit
		}
		if (email === "") {
			console.log('email');
			$('#email_err').removeClass('d-none');
			tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter Email Id.", '');
			return false; // stop submit
		}
		// if (pan_no === "") {
		// 	console.log('pan_no');
		// 	$('#pan_no_err').removeClass('d-none');
		// 	tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter PAN No.", '');
		// 	return false; // stop submit
		// }

		if(document.getElementById("user_admin_connect")){
			$('#user_admin_connect_err').addClass('d-none');
			var user_admin_connect = $.trim($("#user_admin_connect").val());
			console.log('user_admin_connect -- '.user_admin_connect);
		    if (user_admin_connect === "") {
				$('#user_admin_connect_err').removeClass('d-none');
				tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Select Admin Connect.", '');
				return false; // stop submit
			}

		}

		
		console.log('ceck');
		$("#submitvendordetailform").submit();
	}
	function getmSMENumber() {
		if ($("#msme_register_yes_no").is(":checked")) {
			$("#msme_number_id").removeClass('d-none');
		} else {
			$("#msme_number_id").addClass('d-none');
		}
	}

	function onchangeVRQAnswer(valuess) {
		
		console.log("VRQ Answer changed: " + valuess);
		if (valuess == 'Y') {
			$(".vrq_extra").removeClass('d-none');
		} else {
			$(".vrq_extra").addClass('d-none');
		}
	}

	// function onchangeAssociatedAnswer(valuess) {
	// 	if (valuess == 'Y') {
	// 		$(".associated_team").removeClass('d-none');
	// 	} else {
	// 		$(".associated_team").addClass('d-none');
	// 	}
	// }

	function getVendorCustomerItems() {
		var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$("#vendor-items-content").html(loader);
		var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
		jQuery.ajax({
			url: "views/custom_list_vendor_customer_items.php",
			data: 'transaction_table=' + transaction_table + "&transaction_id=" + transaction_id,
			type: "POST",
			success: function (data) {
				$('#vendor-items-content').html(data);
				$(".js-example-basic-single").select2();
				$('[data-bs-toggle="tooltip"]').tooltip();
			},
			error: function () {
				$('#vendor-items-content').html('<p class="text-danger">Error loading items. Please try again.</p>');
			}
		});
	}

	// function onchangeAssociatedAnswer(value) {
	// 	if (value == 'Y') {
	// 		$(".associated_team").removeClass('d-none');
	// 	} else {
	// 		$(".associated_team").addClass('d-none');
	// 	}
	// }

	// function onchangeVRQAnswer(value) {
	// 	if (value == 'Y') {
	// 		$(".vrq_extra").removeClass('d-none');
	// 	} else {
	// 		$(".vrq_extra").addClass('d-none');
	// 	}
	// }

	function addedurows() {
		var table = document.getElementById("edutable");
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		row.innerHTML = `
			<td>${rowCount}
				<input type="hidden" name="ag_rowid[]" class="form-control itemRow" value="" autocomplete="off">
			</td>
			<td><input type="text" name="ag_type[]" class="form-control" value="" autocomplete="off"></td>
			<td><input type="text" name="ag_number[]" class="form-control" value="" autocomplete="off"></td>
			<td><input type="text" name="ag_sin_date[]" class="form-control datepicker" value="" autocomplete="off"></td>
			<td><input type="text" name="ag_ex_date[]" class="form-control datepicker" value="" autocomplete="off"></td>
			<td>
				<div class="row">
					<div class="col-lg-6">
						<label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="Upload">
							<i class="ti-cloud-up f-20 text-primary"></i>
							<input type="file" name="agreement_file[]" />
							<input type="hidden" name="hidden_agreement_file[]" value="" />
						</label>
					</div>
				</div>
			</td>
		`;
	}

	//var mkseq = $("#customer_vendor_gst_billing_details_cnt").val();
	var mkseq = 1000;
	function addMoreGSTBillingDetails() {
		var gsttype_id = $("#gsttype_id").html();
		var countrieslist_id= $("#countrieslist_id").html();
		mkseq++;
		var html = `<div class="row sub-title" id="removediv_` + mkseq + `">
				<div class="col-lg-6">
					<div class="row"><input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]" value="" >
						<div class="col-lg-6 p-r-5 w-full" style="float: left;">
							<label class="form-label d-block">GST  Registration type</label>
							<select  name="gsttype[]" class="form-select js-example-basic-single">`;
				html += gsttype_id;				
						html += `</select>
						</div>
						<div class="form-group col-lg-6" style="float: left;">
							<label class="form-label">GSTIN</label>
							<div class="kt-input-icon">
								<input class="form-control" type="text" name="gstin[]" onblur="checkalreadyavailableornotcustomerorvendordetails('GSTIN', this)" value="" >
								<span class="text-danger f-14 gstinerror" style="display:none"></span>	
							</div>
							
						</div>
						<div class="form-group col-lg-12">
							<label class="form-label">Billing Adderss</label>
							<div class="kt-input-icon">
								<textarea type="text" class="form-control" placeholder="Billing Adderss" name="billing_address[]" id="billing_address_`+ mkseq + `"></textarea>
							</div> 
						</div>
						<div class="form-group col-lg-3 w-full" >
							<label class="form-label">Country</label>
							<select id="billing_country_`+ mkseq + `" class="form-select js-example-basic-single selectedcategory" name="billing_country[]" data-element_id="billing_state_`+ mkseq + `" data-table_name="states" data-field_name="country_id" data-targetelement = "selectedbillcountry" name="billing_country[]" >`;
				html += countrieslist_id;				
						html += `</select>
						</div>
						<div class="form-group col-lg-3  w-full" >
							<label class="form-label">State</label>
							<select id="billing_state_`+ mkseq + `" name="billing_state[]" class="form-select js-example-basic-single selectedcategory" data-element_id="billing_city_`+ mkseq + `" data-table_name="cities" data-field_name="state_id" data-targetelement = "selectedbillstate" >
								<option value="">Select Any One</option>
							</select>

						</div>
						<div class="form-group col-lg-3 p-r-5  w-full" >
							<label class="form-label">City/Town</label>
							<select id="billing_city_`+ mkseq + `" name="billing_city[]" class="form-select js-example-basic-single selectedcategory" data-element_id="" data-table_name="city" data-field_name="" data-targetelement = "selectedbillcity">
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="form-group col-lg-3 p-r-5" >
							<label  class="form-label">Pincode</label>
							<input class="form-control" placeholder="Pincode" type="number" id="billing_post_code_`+ mkseq + `" name="billing_post_code[]" value="" >

						</div>
						<div class="form-group col-lg-3 p-r-5" style="float: right;">
							<label class="form-label">Contact Person</label>
							<input class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person[]" value="" >

						</div>
						<div class="form-group col-lg-3 p-r-5" style="float: left;">
							<label class="form-label">Mobile</label>
							<div class="kt-input-icon">
								<input class="form-control" type="number" id="mobile" name="mobile[]" value="" >	
							</div>
						</div>
						<div class="form-group col-lg-3 p-r-5" style="float: right;">
							<label class="form-label">Fax</label>
							<div class="kt-input-icon">
								<input class="form-control" type="number" id="fax" name="fax[]" value="" >
							</div>
						</div>
					</div>
				</div>	
				<div class="col-lg-6" style="float: left;">
					<div class="row">
						<div class="form-group col-lg-9" >
							<label class="form-label d-block"></label>
							<div class="border-checkbox-section  ">
								<div class="border-checkbox-group border-checkbox-group-primary m-t-30">
									<input class="border-checkbox cloneShipping" type="checkbox" id="checkbox`+ mkseq + `"  data-seq="`+mkseq+`" >
									<label class="form-label border-checkbox-label" for="checkbox`+ mkseq + `">My shipping address is the same as my billing address</label>
								</div>
							</div>
						</div>
						<div class="col-lg-3 d-flex flex-column justify-content-between text-right">
							<span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer removeGSTDiv" data-gstdiv="`+ mkseq + `">
								<i class="ti-trash text-danger"></i> Remove
							</span>
							<div class="form-group ">
								<input type="radio"  name="is_default" value="`+ (mkseq - 1) + `" >
								<label>Make Default</label>
							</div>
						</div>
						<div class="form-group col-lg-12" >
							<label class="form-label">Shipping Adderss</label>
							<div class="kt-input-icon">
								<textarea type="text" class="form-control placeholder="Street" name="shipping_address[]" id="shipping_address_`+ mkseq + `"></textarea>
							</div> 
						</div>
						<div class="form-group col-lg-3  w-full" >
							<label class="form-label">Country</label>
							<select id="shipping_country_`+ mkseq + `" class="form-select js-example-basic-single selectedcategory" name="shipping_country[]" data-element_id="shipping_state_`+ mkseq + `" data-table_name="states" data-field_name="country_id" data-targetelement = "selectedshipcountry" >`;
				html += countrieslist_id;				
						html += `</select></select>
						</div>
						<div class="form-group col-lg-3 w-full">
							<label class="form-label">State</label>
							<select id="shipping_state_`+ mkseq + `" name="shipping_state[]" class="form-select js-example-basic-single selectedcategory" data-element_id="shipping_city_`+ mkseq + `" data-table_name="cities" data-field_name="state_id" data-targetelement = "selectedshipstate" >
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="form-group col-lg-3 p-r-5 w-full">
							<label class="form-label">City/Town</label>
							<select id="shipping_city_`+ mkseq + `" name="shipping_city[]" class="form-select js-example-basic-single selectedcategory" data-element_id="" data-table_name="city" data-field_name="" data-targetelement = "selectedshipcity" >
								<option value="">Select Any One</option>
							</select>
						</div>
						<div class="form-group col-lg-3" >
							<label class="form-label">Pincode</label>
							<input class="form-control" placeholder="Pincode" type="number" id="shipping_post_code_`+ mkseq + `" name="shipping_post_code[]" value="" >
						</div>									
					</div>
				</div>
			</div>`;
		$("#gst-tab-details").append(html);
		$(".js-example-basic-single").select2();

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
		$("div[id^='removediv_']").each(function (index) {
			var newIndex = index + 0; // start from 1
			$(this).find("input[name='is_default']").val(newIndex);
		});
		// ✅ Ensure at least one remains default
		var checked = $("input[name='is_default']:checked");
		if (checked.length === 0 && $("input[name='is_default']").length > 0) {
			$("input[name='is_default']").first().prop("checked", true);
		}

		var rid = block.find("input[name='customer_vendor_gst_billing_details_id[]']").val();
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

	function cloneShipping(ei, seq) {
		if (ei.is(":checked")) {
			console.log('scsdcsdc');
			// Country
			var billing_country_val = $("#billing_country_" + seq).val();
			var billing_country_text = $("#billing_country_" + seq + " option:selected").text();
			var newOption1 = new Option(billing_country_text, billing_country_val, true, true);
			$("#shipping_country_" + seq).append(newOption1);

			// Wait until states are loaded
			var billing_state_val = $("#billing_state_" + seq).val();
			var billing_state_text = $("#billing_state_" + seq + " option:selected").text();
			var newOption2 = new Option(billing_state_text, billing_state_val, true, true);
			$("#shipping_state_" + seq).append(newOption2);

			// Wait until cities are loaded
			var billing_city_val = $("#billing_city_" + seq).val();
			var billing_city_text = $("#billing_city_" + seq + " option:selected").text();
			var newOption3 = new Option(billing_city_text, billing_city_val, true, true);
			$("#shipping_city_" + seq).append(newOption3);


			$("#shipping_address_" + seq).val($("#billing_address_" + seq).val());
			$("#shipping_post_code_" + seq).val($("#billing_post_code_" + seq).val());
		}
	}
	function checkCompanyNameAllReadyAvilableOrNot() {
		$("#allreadycompanyname").addClass("d-none");
		var company_name = $("#company").val();
		if (company_name != "") {
			$.ajax({
				url: "ajax.php",
				data: 'page=checkcompanynameallreadyavilableornot&company_name=' + company_name,
				type: "POST",
				async: false,
				success: function (data) {
					console.log(data);
					var res = JSON.parse(data);
					if (res.company_name == '1') {
						console.log('already');
						$("#companynameerror").text(company_name);
						$("#allreadycompanyname").removeClass("d-none");
						$("#company").val('');
					} else {
						console.log('frash');
						$("#company").val();
					}
				},
				error: function () { }
			});

		}

	}
	function checkalreadyavailableornotcustomerorvendordetails(field, el = null) {
		$('#emailerror').addClass("d-none");
		$('.gstinerror').addClass("d-none");
		$('#pannoerror').addClass("d-none");
		var fieldName = '';
		var fieldValue = '';
		if (field == 'EMAIL') {
			fieldName = 'email';
			fieldValue = $("#email").val().trim();
		} else if (field == 'GSTIN') {
			fieldName = 'gstin';
			fieldValue = $(el).val().trim(); // GST input value of this row
		} else if (field == 'PAN_NO') {
			fieldName = 'pan_no';
			fieldValue = $("#pan_no").val().trim();
		}
		console.log('fieldValue', (fieldValue));
		// $("#allreadycompanyname").hide();
		if (fieldValue !== "") {
			console.log('Start');
			$.ajax({
				url: "ajax.php",
				data: `page=checkalreadyavailableornotcustomerorvendordetails&table_name=vendors&field_name=${fieldName}&field_value=${fieldValue}`,
				type: "POST",
				async: false,
				success: function (data) {
					var res = JSON.parse(data);
					if (res.result != '' && res.result != null) {
						console.log(res.result);
						if (field == 'EMAIL') {
							$('#emailerror').text(fieldValue + " Already Register");
							$('#emailerror').removeClass("d-none");
							$("#email").val('');
						}
						if (field === 'GSTIN' && el) {
							$(el).siblings(".gstinerror")
								.text(fieldValue + " Already Registered")
								.removeClass("d-none");
							$(el).val('');
						}
						if (field == 'PAN_NO') {
							$('#pannoerror').text(fieldValue + " Already Register");
							$('#pannoerror').removeClass("d-none");
							$("#pan_no").val('');
						}
					}
				},
				error: function () { }
			});

		}
		console.log('end');
	}

 function printDivVendor() {
   var printContents = document.getElementById('printableArea').innerHTML;
   var originalContents = document.body.innerHTML;
   document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
   window.print();
   document.body.innerHTML = originalContents;
}


function addDocumentRows(){
	var htmlrow = "";
	var hide_doc_document_select_list = $("#hide_doc_document_select_list").html();
	htmlrow +=`<tr class="rowcount p-1">
								<td><?php echo $k; ?>
								<input type="hidden" name="doc_rowid[]" class="form-control itemRow" value="" autocomplete="off">
							</td>
							<td class="p-1 w-full"><select name="doc_document_id[]" class="form-control form-select js-example-basic-single">`+hide_doc_document_select_list+`							
							</select></td>
							<td class="p-1">
								<input type="text" name="doc_from_date[]" class="form-control datepicker "  value=""  autocomplete="off"> 
							</td>
							<td class="p-1">
								<input type="text" name="doc_to_date[]" class="form-control datepicker"  value="" autocomplete="off">  
							</td>
							<td class="p-1">
								<input type="file" name="doc_filename[]" class="form-control"/>
								<input type="hidden" name="hidden_doc_filename[]" value=""/>
							</td>
							<td class="p-1">
								
							</td>

						</tr>`;

	$('#addDocuments').append(htmlrow);
	$(".js-example-basic-single").select2(); 
	$( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        dateFormat: date_format,
        minDate: 0,
        maxDate: "+30D",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true,
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
}

