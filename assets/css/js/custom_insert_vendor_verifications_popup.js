function panVerification(btn_id,btn_text,tab_id){
		var company_name = $("#company_name_popup").val();
		var pan_no = $("#pan_no_popup").val();
		var transaction_table = '<?php echo $transaction_table; ?>';
		var transaction_id = '<?php echo $vendor_id; ?>';
		var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
		var  consent_text= 'I hear by declare my consent agreement for fetching my information via ZOOP AP';
		$('#'+btn_id).html(htmloader);
		$('.pan_verification_error').text('');
		$('#'+btn_id).addClass("disabled");
		$('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
		
		jQuery.ajax({
		    url: "apis/call_api.php",
		    data:'pan_holder_name='+company_name+'&customer_pan_number='+pan_no+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&api_code=ZOOPONEPANLITE&consent=Y&consent_text='+consent_text+'&mode=sync',
		    type: "POST",
		    success:function(data){
		    	//console.loge(data);
		    	var res = JSON.parse(data);
		    	//console.loge(res);
		    	try {
		    		if(res && res.response_code =="200"){
		    			var pangstlist = "";
						pangstlist +='<h4>PAN Details</h4>';
						pangstlist +='<table class="table table-hover">';
						pangstlist +='<thead><tr><th class="p-2">Company Name</th><th class="p-2">PAN Number</th><th class="p-2">Validated Company Name</th></tr></thead>';
						pangstlist +='<tbody>';
						if(res.result == null){
							$('.pan_verification_error').text(res.response_message);
		    				$('#'+btn_id).text(btn_text);
		    				$('#'+btn_id).removeClass("disabled");
			    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
			    			pangstlist +='<tr><td class="p-2 w-b-w-s">'+company_name+'</td><td class="p-2 w-b-w-s">'+pan_no+'</td><td class="p-2 w-b-w-s text-danger">'+res.response_message+'</td></tr>';
		    			}else if(res.result.pan_status == "VALID"){
		    				//if(res.result.user_full_name.toUpperCase() == company_name.toUpperCase()){
		    					$('.pan_verification_error').text('<?php echo $_SESSION['error_message']['9136']; ?>');
			    				var htmloader = '<i class="ti-check-box"></i>  Verified Successfully';
								$('#'+btn_id).html(htmloader);
								$('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
								$('#'+btn_id).removeClass('btn-danger');
								$('#'+btn_id).addClass('btn-success');
								$('#'+btn_id).removeAttr("onclick");							
				    			pangstlist +='<tr><td class="p-2 w-b-w-s">'+company_name+'</td><td class="p-2 w-b-w-s">'+pan_no+'</td><td class="p-2 w-b-w-s text-danger">'+res.result.user_full_name+'</td></tr>';
								
								verificationSuccess("PAN",res.api_hit_id,'<?php echo $_SESSION['error_message']['9136']; ?>');
		    				/**}else{
		    					$('.pan_verification_error').text(res.response_message);
			    				$('#'+btn_id).text(btn_text);
			    				$('#'+btn_id).removeClass("disabled");
				    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
				    			pangstlist +='<tr><td class="p-2 w-b-w-s">'+company_name+'</td><td class="p-2 w-b-w-s">'+pan_no+'</td><td class="p-2 w-b-w-s text-danger">'+res.result.user_full_name+'</td></tr>';
		    				} **/
		    			}else{
			    			$('.pan_verification_error').text(res.response_message);
		    				$('#'+btn_id).text(btn_text);
		    				$('#'+btn_id).removeClass("disabled");
			    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
			    			pangstlist +='<tr><td class="p-2 w-b-w-s">'+company_name+'</td><td class="p-2 w-b-w-s">'+pan_no+'</td><td class="p-2 w-b-w-s text-danger">'+res.result.user_full_name+'</td></tr>';
		    			}
		    			pangstlist +='</tbody>';
						pangstlist +='</table>';
		    			$("#pan_response_popup").html(pangstlist); 
		    		}else if(res && res.response_code =="400"){	
		    			$('.pan_verification_error').text(res.response_message);
		    			$('#'+btn_id).text(btn_text);
	    				$('#'+btn_id).removeClass("disabled");
		    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    		}else{
		    			$('.pan_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
			    			$('#'+btn_id).text(btn_text);
			    			$('#'+btn_id).removeClass("disabled");
			    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    		}
                } catch (e) {
                	if(res.response_message !=""){
                		var errormsg = res.response_message;
                	}else{
                		var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                	}
                    $('.pan_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
                }
		    },
		    error:function (){
		    	$('.pan_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
    			$('#'+btn_id).text(btn_text);
    			$('#'+btn_id).removeClass("disabled");
    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    }
	  	});
	}
	function gstnVerification(btn_id,btn_text,tab_id){
		var gstin = $("#gstn_no_popup").val();
		// var pan_no = $("#gst_pan_number_popup").val();
		var  consent_text= 'I hear by declare my consent agreement for fetching my information via ZOOP AP';
		var transaction_table = '<?php echo $transaction_table; ?>';
		var transaction_id = '<?php echo $vendor_id; ?>';
		var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
		$('#'+btn_id).html(htmloader);
		//$('.gstn_verification_error').text('');
		$('#'+btn_id).addClass("disabled");
		$('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
		var htmloader11 = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
		$('#gstn_details_popup').html(htmloader11);
		jQuery.ajax({
		    url: "apis/call_api.php",
		    data:'business_gstin_number='+gstin+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&api_code=GSTVERIFICATIONLITE&consent=Y&consent_text='+consent_text+'&mode=sync',
		    type: "POST",
		    success:function(data){
		    	var res = JSON.parse(data);
		    	try {
		    		if(res && res.response_code =="200"){
		    				//$('.gstn_verification_error').text('<?php echo $_SESSION['error_message']['9137']; ?>');
		    				var htmloader = '<i class="ti-check-box"></i> Check GSTN';
							$('#'+btn_id).html(htmloader);
							$('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
							$('#'+btn_id).removeClass('btn-danger');
							$('#'+btn_id).addClass('btn-success');
							$('#'+btn_id).removeAttr("onclick");
							//$('#'+btn_id).removeClass("disabled");
						var gstn_details_popup ='';
						gstn_details_popup += '<table class="table table-hover">';
						gstn_details_popup += '<thead><tr><th class="p-2" width="19%"></th><th width="1%" class="p-2"></th><th width="40%" class="p-2">Vendor Details</th><th width="40%" class="p-2 text-danger">GST Details</th></tr>';
						gstn_details_popup += '<tbody><tr><th  class="p-2" >Company Name</th><td  class="p-2" >:</td><td class="w-b-w-s p-2" ><?php  echo $vendordetail['company']; ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.legal_name+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">Registration Date</th><td  class="p-2">:</td><td class="p-2">-</td><td class="w-b-w-s p-2 text-danger">'+res.result.register_date+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">Business Type</th><td  class="p-2">:</td><td class="p-2"><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.business_constitution+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">Address</th><td  class="p-2">:</td><td><?php echo preg_replace('/\r|\n/', ' ', $vendordetail['billing_address']); ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.primary_business_address.full_address+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">District</th><td  class="p-2">:</td><td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.primary_business_address.district+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">State</th><td  class="p-2">:</td><td class="p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.primary_business_address.state_code+'</td></tr>';
						gstn_details_popup += '<tr><th  class="p-2">City</th><td  class="p-2">:</td><td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td><td class="w-b-w-s p-2 text-danger">'+res.result.primary_business_address.city+'</td></tr>';		
						gstn_details_popup += '</table>';
						$("#gstn_details_popup").html(gstn_details_popup);
						var vergst = "GSTIN";
						var verfybuttn = '<button class="btn btn-primary mb-3" type="button" onclick="verificationGSTSuccess(\''+vergst+'\','+res.api_hit_id+');" >Click To Confirm GST Detail</button>';
						$("#gstn_verification_btn").html(verfybuttn); 

		    			// }else{
		    			// 	$('.gstn_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
			    		// 	$('#'+btn_id).text(btn_text);
			    		// 	$('#'+btn_id).removeClass("disabled");
			    		// 	$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
			    		// 	console.log("else 9135 - "+res.error.message);	
		    			// }
		    		}else{
		    			$('.gstn_verification_error').text(res.response_message);
	    				$('#'+btn_id).text(btn_text);
	    				$('#'+btn_id).removeClass("disabled");
	    				$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
	    				$('#gstn_details_popup').html('');
		    		}
                }catch(e) {
                	if(res.response_message !=""){
                		var errormsg = res.response_message;
                	}else{
                		var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                	}
                    $('.gstn_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
                    $('#gstn_details_popup').html('');
                }
		    },
		    error:function (){
		    	$('.gstn_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
    			$('#'+btn_id).text(btn_text);
    			$('#'+btn_id).removeClass("disabled");
    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
    			$('#gstn_details_popup').html('');
		    }
	  	});
	}
	function bankVerifications(btn_id,btn_text,tab_id){
		var ifsc = $("#ifsc_code_popup").val();
		var bank_account = $("#bank_account_no_popup").val();
		var transaction_table = '<?php echo $transaction_table; ?>';
		var transaction_id = '<?php echo $vendor_id; ?>';
		var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
		var  consent_text= 'I hear by declare my consent agreement for fetching my information via ZOOP AP';
		$('#'+btn_id).html(htmloader);
		$('.bank_verification_error').text('');
		$('#'+btn_id).addClass("disabled");
		$('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
		jQuery.ajax({
		    url: "apis/call_api.php",
		    data:'account_number='+bank_account+'&ifsc='+ifsc+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&api_code=BANKACVERIFICATIONLITE&consent=Y&consent_text='+consent_text+'&mode=sync',
		    type: "POST",
		    success:function(data){
		    	var res = JSON.parse(data);
		    	console.log(res);
		    	try {
		    		if(res && res.response_code =="200"){
		    			if(res.result.verification_status == 'VERIFIED'){
		    				$('.bank_verification_error').text('<?php echo $_SESSION['error_message']['9140']; ?>');
		    				var htmloader = '<i class="ti-check-box"></i>  Verified Successfully';
							$('#'+btn_id).html(htmloader);
							$('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
							$('#'+btn_id).removeClass('btn-danger');
							$('#'+btn_id).addClass('btn-success');
							$('#'+btn_id).removeAttr("onclick");
							//$('#'+btn_id).removeClass("disabled");
							console.log('sucesss');
							verificationSuccess("BANK",res.api_hit_id,'<?php echo $_SESSION['error_message']['9140']; ?>');

							var pangstlist = "";
						pangstlist +='<h4>Bank Details</h4>';
						pangstlist +='<table class="table table-hover">';
						pangstlist +='<thead><tr><th class="p-2">IFSC code</th><th class="p-2">Bank Account Number</th><th class="p-2">Name</th></tr></thead>';
						pangstlist +='<tbody>';
						pangstlist +='<tr><td class="p-2">'+ifsc+'</td><td class="p-2">'+bank_account+'</td><td class="p-2 text-danger">'+res.result.beneficiary_name+'</td></tr>';
						pangstlist +='</tbody>';
						pangstlist +='</table>';
		    			$("#Bank_response_popup").html(pangstlist);


		    			}else{
			    			$('.bank_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
			    			$('#'+btn_id).text(btn_text);
			    			$('#'+btn_id).removeClass("disabled");
			    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    			}
		    		}else{
		    			$('.bank_verification_error').text(res.response_message);
	    				$('#'+btn_id).text(btn_text);
	    				$('#'+btn_id).removeClass("disabled");
		    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    		}
                } catch (e) {
                	if(res.response_message !=""){
                		var errormsg = res.response_message;
                	}else{
                		var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                	}
                    $('.bank_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
                }
		    },
		    error:function (){
		    	$('.bank_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
    			$('#'+btn_id).text(btn_text);
    			$('#'+btn_id).removeClass("disabled");
    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    }
	  	});
	}
	function mSMEVerifications(btn_id,btn_text,tab_id){
		var msme_no_popup = $("#msme_no_popup").val();
		var transaction_table = '<?php echo $transaction_table; ?>';
		var transaction_id = '<?php echo $vendor_id; ?>';
		var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
		var consent_text= 'I hear by declare my consent agreement for fetching my information via ZOOP AP';
		$('#'+btn_id).html(htmloader);
		//$('.msme_verification_error').text('');
		$('#'+btn_id).addClass("disabled");
		$('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
		jQuery.ajax({
		    url: "apis/call_api.php",
		    data:'udyog_aadhaar='+msme_no_popup+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&api_code=UDYOGAADHAAR&consent=Y&consent_text='+consent_text+'&mode=sync',
		    type: "POST",
		    success:function(data){
		    	var res = JSON.parse(data);
		    	console.log(res);
		    	try {
		    		if(res && res.response_code =="200"){
	    				//$('.msme_verification_error').text('<?php echo $_SESSION['error_message']['9141']; ?>');
	    				var htmloader = '<i class="ti-check-box"></i> Check MSME';
						$('#'+btn_id).html(htmloader);
						$('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
						$('#'+btn_id).removeClass('btn-danger');
						$('#'+btn_id).addClass('btn-success');
						$('#'+btn_id).removeAttr("onclick");
						//$('#'+btn_id).removeClass("disabled");
						//console.log('sucesss');
						//verificationSuccess("BANK",res.api_hit_id);
	    				
						var pangstlist = "";
						pangstlist +='<h4>MSME Details</h4>';
						pangstlist +='<table class="table table-hover">';
						pangstlist +='<tbody>';
		    			pangstlist +='<tr><th class="p-2">Udyam Registration Number</th><td class="p-2 text-danger">'+msme_no_popup+'</td></tr>';
						pangstlist +='<tr><th class="p-2">Name Of Enterprise</th><td class="p-2 text-danger">'+res.result.enterprise_type+'</td></tr>';

						pangstlist +='<tr><th class="p-2">Major Activity</th><td class="p-2 text-danger">'+res.result.major_activity+'</td></tr>';
						pangstlist +='<tr><th class="p-2">Organisation Type</th><td class="p-2 text-danger">'+res.result.nic_code.organization_type+'</td></tr>';
						
						pangstlist +='<tr><th class="p-2">Social Category</th><td class="p-2 text-danger">'+res.result.social_category+'</td></tr>';
						//pangstlist +='<tr><th class="p-2">Date Of Incorporation</th><td class="p-2 text-danger">'+res.result.date_of_commencement+'</td></tr>';
						pangstlist +='<tr><th class="p-2">Date Of Commencement Of Production Business</th><td class="p-2 text-danger">'+res.result.date_of_commencement+'</td></tr>';
						pangstlist +='<tr><th class="p-2">DIC</th><td class="p-2 text-danger">'+res.result.dic_name+'</td></tr>';
						//pangstlist +='<tr><th class="p-2">MSMEDI</th><td class="p-2 text-danger">'+res.result.generalInfo.msmedi+'</td></tr>';
						pangstlist +='<tr><th class="p-2">Date Of Udyam Registration</th><td class="p-2 text-danger">'+res.result.applied_date+'</td></tr>';
						pangstlist +='<tr><th class="p-2">Status</th><td class="p-2 text-danger">'+res.response_message+'</td></tr>';

						//pangstlist +='<tr><th class="p-2" colspan="2"><a class="text-primary" href="'+res.result.pdfUrl+'" target="_blank" download>Certificate Click To Download</a></th></tr>';
						pangstlist +='</tbody>';
						pangstlist +='</table>';
						var vermsme = "MSME";
						var verfybuttn = '<button class="btn btn-primary mb-3" type="button"  onclick="verificationMSMESuccess(\''+vermsme+'\','+res.api_hit_id+');" >Click To Confirm MSME Detail</button>';
						$("#msme_verification_btn").html(verfybuttn); 
						$("#msme_details_list_popup").html(pangstlist);
		    		}else{
		    			$('.msme_verification_error').text(res.response_message);
	    				$('#'+btn_id).text(btn_text);
	    				$('#'+btn_id).removeClass("disabled");
		    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    		}
                } catch (e) {
                	if(res.response_message !=""){
                		var errormsg = res.response_message;
                	}else{
                		var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                	}
                    $('.msme_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
                }
		    },
		    error:function (){
		    	$('.msme_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>'); 
    			$('#'+btn_id).text(btn_text);
    			$('#'+btn_id).removeClass("disabled");
    			$('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle'); 
		    }
	  	});
	}	
	function verificationMSMESuccess(verification_type,api_hit_id){
		$('.msme_verification_error').text('<?php echo $_SESSION['error_message']['9141']; ?>');
		verificationSuccess(verification_type,api_hit_id,'<?php echo $_SESSION['error_message']['9141']; ?>');
	}		
	function verificationSuccess(verification_type,api_hit_id,msg=""){
		var transaction_table = '<?php echo $transaction_table; ?>';
		var transaction_id = '<?php echo $vendor_id; ?>';
		jQuery.ajax({
		    url: "ajax.php",
		    data:'page=updatevendorverifications&transaction_table='+transaction_table+'&transaction_id='+transaction_id+"&verification_type="+verification_type+'&api_hit_id='+api_hit_id,
		    type: "POST",
		    success:function(data11){
		    	if(msg ==""){
		    		msg = '<?php echo $_SESSION['error_message']['3001']; ?>';
		    	}
		    	tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
		    	verificationVendorDetails(verification_type);
		    }
		});
	}
	function verificationGSTSuccess(verification_type,api_hit_id){
		$('.gstn_verification_error').text('<?php echo $_SESSION['error_message']['9137']; ?>');
		verificationSuccess(verification_type,api_hit_id,'<?php echo $_SESSION['error_message']['9137']; ?>');
		
	}