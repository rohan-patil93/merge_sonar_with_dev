<?php
if($_POST['ajaxpage'] !="Y"){
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
}
$File_Uploads_Path = "uploads/reimbursement/";
$employee_id = $_POST['emp_id']!=""?$_POST['emp_id']:$_SESSION['emp_id'];
$url = API."?mode=getemployeeexpencesdetails&emp_id=".$employee_id."&exp_id=".$_POST['exp_id']."&sub_id=".$_POST['sub_id'];
$response = callAPI($url);
$response= json_decode($response,1);
$main_details = $response['main_details'];
//print_r($main_details);
$sub_details = $response['sub_details_single'];
$expense_list = $response['expense_list'];
$projects_list = $response['projects'];
$emp_details = $response['emp_details'];
foreach($response['expense_categories'] as $catdat){
    // if($catdat['parent_category_id']==0){
    //     $datacat[$catdat['id']]=$catdat;
    // }else{
    //     $datacat[$catdat['parent_category_id']]['data'][]=$catdat;
    // }
  $datacat[$catdat['category_id']]=$catdat;
}
foreach($response['expenses_mileages_perdiem'] as $catdat){
    if($catdat['expense_type']=='0'){
        $mileageslist[$catdat['id']]=$catdat;
    }else{
        $perdiamarray[$catdat['id']]=$catdat;
    }
}
//echo "<pre>"; print_r($sub_details);print_r($datacat); echo "</pre>";
$arraycurrncy = array("1"=>"INR","2"=>"USD");

foreach($response['general_code_details'] as $code){
    if($code['genc_code']=="PAYMETHOD"){
        $Payment_mode[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }elseif($code['genc_code']=="EXPENSTYPE"){
        $expense_typelist[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }
} 
if($sub_details['expense_type']=='1'){ 
    $expens_css = $Perdiem_css = "display:none"; 
}elseif($sub_details['expense_type']=='2'){
    $expens_css = $mileage_css = "display:none"; 
}else{
     $Perdiem_css = $mileage_css = "display:none";
}
?>
<script type="text/javascript">
    function calucateamount(){
        var expense_type = $('input[name="expense_type"]:checked').val();
        var id="exp";
        if(expense_type=='1'){ 
            id =  "mileage"; 
        }else if(expense_type=='2'){
            id = "perd"; 
        }
        var total = 0;
        var amount = $("#amount_popup_"+id).val();
        var qty = $("#qty_popup_"+id).val();
        var tax = $("#tax_popup").val();
        var sumamt = parseFloat(amount)*parseFloat(qty);
        var total = parseFloat(sumamt)+parseFloat(tax);
        if(!isNaN(total)){
          $("#total_popup").val(total);
        }else{
          alert("Please Select Mandatory Field");
        }
    }
    function itemChange(valuu,val_id){
        var expense_type = $('input[name="expense_type"]:checked').val();
       /** var id="exp";
        if(expense_type=='1'){ 
            id =  "mileage"; 
        }else if(expense_type=='2'){
            id = "perd"; 
        }
        var newString = valuu.replace(/\s+/g,'');
        newStringar = newString.split("(");
        newStringars = newStringar[1].split(")");
        $("#amount_popup_"+id).val(newStringars[0]);
        $("#amount_popup_span_"+id).text(" (Rs. "+newStringars[0]+")"); **/
        var id="exp";
        if(expense_type=='1'){
        id =  "mileage";  
            var mileageslistson = JSON.parse($("#mileageslistson").val());
            var newStringars = mileageslistson[val_id]['amount']
        }else if(expense_type=='2'){
           id = "perd"; 
          var perdiamarrayjson = JSON.parse($("#perdiamarrayjson").val());
          var newStringars = perdiamarrayjson[val_id]['amount']
        }
          $("#amount_popup_span_"+id).text(" (Rs. "+newStringars+")");
          $("#amount_popup_"+id).val(newStringars);


   }
   function checkFuntion(msg){

    expense_type = $('input[name="expense_type"]:checked').val();
    $(".expens_css").hide();
    $(".mileage_css").hide();
    $(".Perdiem_css").hide();
    if(expense_type=='1'){ 
        id =  "mileage"; 
        $(".mileage_css").show();
    }else if(expense_type=='2'){
        id = "perd"; 
        $(".Perdiem_css").show();
    }else{
        var id="exp";
        $(".expens_css").show();
    }
    $("#amount_popup_span_"+id).text("");
    $("#tax_popup").val("0");
    $("#total_popup").val("");

   }

function submitrom(){
	var expense_type = $('input[name="expense_type"]:checked').val();
    if(expense_type == 0){
        var reuirdcheck = ['category_exp','description','expense_date','rate_exp','merchant_name','payment_mode'];
     }else if(expense_type == 1){
        var reuirdcheck = ['category_mileage','description','expense_date','qty_mileage','merchant_name','payment_mode'];
    }else if(expense_type == 2){
        var reuirdcheck = ['category_perd','description','expense_date','qty_perd','merchant_name','payment_mode'];
    }
    var dataArray = $("#form_id").serializeArray();
    var isValid = true;
    $(dataArray).each(function(i, field) {
        if (reuirdcheck.includes(field.name)) {
            
        }else{
            return; 
        }
        var inputField = $("[name='" + field.name + "']"); 
        inputField.parent().find(".error-message").remove();
        if (inputField.hasClass("select2-hidden-accessible")) {
            inputField.next(".select2").find(".select2-selection").removeClass("error");
        }
        // check empty
        if ($.trim(field.value) === "" || field.value === "0") {
            if (inputField.hasClass("select2-hidden-accessible")) {
                // Select2 field
                inputField.next(".select2").find(".select2-selection").addClass("error");
                inputField.parent().append("<div class='error-message'>This field is required</div>");
            } else {
                // Normal input/text field
                inputField.after("<div class='error-message'>This field is required</div>");
            }
            isValid = false;
        }
    });
    if (isValid) {
        console.log("Form is valid ✅");
        //receipt_required_limit validation
    	if(expense_type=='0'){ 
    		var val_id = $("#select_category_exp").val();
    		var catgeoryarrayjson = JSON.parse($("#catgeoryarrayjson").val());
    		var pricereceptreq = catgeoryarrayjson[val_id].receipt_required_limit;
    		var totalamt = $("#total_popup").val();
    		if(totalamt > pricereceptreq){
    			var oldattachment = $("#oldattachment").val();
    			if(oldattachment == ""){
    				var popattachment = $("#popattachment").val();
    				if(popattachment ==""){
    					tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['1003']; ?>','');
    					return false;
    				}
    			}
    		}
    	}
    	//End receipt_required_limit validation
        var formData = new FormData();
        formData.append("page", "insertempexpenses"); 
        var dataArray = $("#form_id").serializeArray(),
        dataObj = {};
        $(dataArray).each(function(i, field){
          formData.append(field.name, field.value); 
        });
        var popattachment = document.getElementById('popattachment').files[0];
        formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                $('#kt_modal_dynamic_popup2').modal("hide");
                window.location.href = "index.php?option=COM_INSERT_EMP_EXPENSES&Rid="+data;
            },
            error:function (){}
        }); 
    }    
}

function changedateexpense(valu){
		var id="exp";
		var expense_type = $('input[name="expense_type"]:checked').val();
		if(expense_type=='1'){
			id =  "mileage";  
			var val_id = $("#select_category_mileage").val();
			if(val_id =="0"){
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Please Select Category", "error");  
				$(".dateexpens").val("");
				return false;
			}
			var mileageslistson = JSON.parse($("#mileageslistson").val());
			var startdate = mileageslistson[val_id]['start_date'];
			var enddate = mileageslistson[val_id]['end_date'];
			jQuery.ajax({
            url: "ajax.php",
            data: 'page=cehckdaterange&startdate='+startdate+'&enddate='+enddate+'&selectdate='+valu,
            type: "POST",
            success:function(data){
				console.log(data);
				if(data=='0'){
          //swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Oops...", "<?php echo $_SESSION['error_message']['1002']; ?>");  
          swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message']['1002']; ?>", "error");  
                
					$(".dateexpens").val("");
				}
			}
        });
        }
	/**	else if(expense_type=='2'){
			var val_id = $("#select_category_perd").val();
			if(val_id =="0"){
				alert('Please Select Category');
				$(".dateexpens").val("");
				return false;
			}
			var perdiamarrayjson = JSON.parse($("#perdiamarrayjson").val());
			var startdate = perdiamarrayjson[val_id]['start_date'];
			var enddate = perdiamarrayjson[val_id]['end_date'];
        } **/
		
		
} 

</script>
<style type="text/css">
    .drop-zone {
  height: 300px;
  padding: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-family: "Quicksand", sans-serif;
  font-weight: 500;
  font-size: 20px;
  cursor: pointer;
  color: #cccccc;
  border: 4px dashed #009578;
  border-radius: 10px;
}

.drop-zone--over {
  border-style: solid;
}

.drop-zone__input {
  display: none;
}

.drop-zone__thumb {
  width: 100%;
  height: 300px;
  border-radius: 10px;
  overflow: hidden;
  background-color: #cccccc;
  background-size: cover;
  position: relative;
}

.drop-zone__thumb::after {
  content: attr(data-label);
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 5px 0;
  color: #ffffff;
  background: rgba(0, 0, 0, 0.75);
  font-size: 14px;
  text-align: center;
}

.j-pro input:read-only {
	cursor: pointer important!;
}
</style>
    <div class="modal-header">
    <h4 class="modal-title">Expense Report</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
    <div class="modal-body" > 
       <input type="hidden" id="mileageslistson" value='<?php echo json_encode($mileageslist); ?>'>

       <input type="hidden" id="perdiamarrayjson" value='<?php echo json_encode($perdiamarray); ?>'>

       <input type="hidden" id="catgeoryarrayjson" value='<?php echo json_encode($datacat); ?>'>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="emp_id" value="<?php echo $employee_id; ?>">
            <input type="hidden" name="exp_detail_id" value="<?php echo $_POST['sub_id']; ?>">
            <input type="hidden" name="reference_date" value="<?php echo $main_details['reference_date'] !=""?$main_details['reference_date']:date("Y-m-d");?>">
         <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 form-radio">
                                  <?php $sub_details['expense_type']=$sub_details['expense_type']=!""?$sub_details['expense_type']:'0'; foreach($expense_typelist as $expense_typelkey => $expense_typel){ ?>
									<div class="radio radio-outline radio-inline" onclick="checkFuntion('test')" >
                                          <label class="form-label">
                                            <input type="radio"  <?php echo ($sub_details['expense_type']==$expense_typelkey)?"checked":""; ?> name="expense_type" id="gender-1" value="<?php echo $expense_typelkey; ?>" >
                                            <i class="helper"></i><?php echo $expense_typel; ?>
                                          </label>
                                        </div>
                              <?php } ?>
                                 

                                <?php /* ?>  <div class="form-check form-check-inline" >
                                    <label class="form-label form-check-label">
                                      <input class="form-check-input" type="radio" <?php echo $sub_details['expense_type']=="1"?"checked":""; ?> name="expense_type" id="gender-2" onclick="checkFun()" value="1"> mileage
                                    </label>
                                  </div>
                                   <div class="form-check form-check-inline" >
                                    <label class="form-label form-check-label">
                                      <input class="form-check-input" type="radio" <?php echo $sub_details['expense_type']=="2"?"checked":""; ?> name="expense_type" id="gender-2" onclick="checkFun()" value="2"> Per Diem
                                    </label>
                                  </div>
                                  <?php **/ ?>
                            </div>
                            <div class="col-lg-3">
                                 <label class="f-w-600">Ref. Number : <span class="f-w-400"><?php echo $main_details['expense_code']!=""?$main_details['expense_code']:"-";?></span></label>
                            </div> 
                            <div class="col-lg-3">
                                 <label class="f-w-600">Ref. Date : <span class="f-w-400"><?php echo $main_details['reference_date'] !=""?$main_details['reference_date']:date("Y-m-d");?></span></label>
                            </div>      


                        </div> 
                        <div class="form-group row">

                            <div class="col-lg-6 expens_css w-full" style="<?php echo $expens_css; ?>">
                                <label for="" class="mb-1">Category <span class="text-danger">*</span></label>
                                   <select class="form-control form-select js-example-basic-single" id="select_category_exp" name="category_exp"  >
                                           <option value="">Select Any One</option>
                                               <?php foreach($datacat as $dataca){ ?>
                                                <option <?php echo $sub_details['category_id']==$dataca['category_id'] ? "selected":"" ;?> value="<?php echo $dataca['category_id']; ?>"><?php echo $dataca['category_name']; ?></option>
                                            <?php } ?>
                                        
                                           <?php  /** foreach($datacat as $datacate){ ?>
                                            <optgroup label="<?php echo $datacate['category_name']; ?>">
                                               <?php foreach($datacate['data'] as $dataca){ ?>
                                                <option <?php echo $sub_details['category_id']==$dataca['id'] ? "selected":"" ;?> value="<?php echo $dataca['id']; ?>"><?php echo $dataca['category_name']; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } **/ ?>
                                </select>
                            </div> 
                            <div class="col-lg-6 mileage_css w-full" style="<?php echo $mileage_css; ?>">
                                <label for="" class="mb-1">Mileage <span class="text-danger">*</span></label>
                            <select class="form-control form-select js-example-basic-single" id="select_category_mileage" name="category_mileage" onchange="itemChange(this.options[this.selectedIndex].text,this.value)" >
                                       <option value="">Select Any One</option>
                                       <?php foreach($mileageslist as $mileageslists){ ?>
                                        <option value="<?php echo $mileageslists['id']; ?>" <?php echo $sub_details['category_id']==$mileageslists['id'] ? "selected":"" ;?>><?php echo $mileageslists['name']." (".$mileageslists['amount'].")"; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-lg-6 Perdiem_css w-full" style="<?php echo $Perdiem_css; ?>">
                                <label for="" class="mb-1">Per Diem <span class="text-danger">*</span></label>
                                 <select class="form-control form-select js-example-basic-single" id="select_category_perd" name="category_perd" onchange="itemChange(this.options[this.selectedIndex].text,this.value)"  >
                                        <option value="">Select Any One</option>
                                    <?php foreach($perdiamarray as $perdieam){ ?>
                                        <option value="<?php echo $perdieam['id']; ?>" <?php echo $sub_details['category_id']==$perdieam['id'] ? "selected":"" ;?>><?php echo $perdieam['name']." (".$perdieam['amount'].")"; ?></option>
                                    <?php } ?>
                                </select>
                            </div> 
                            <div class="col-lg-6">
                                <label for="" class="mb-1">Purpose <span class="text-danger">*</span></label>
                                <input type="text" name="description" class="form-control" placeholder="Enter purpose" value="<?php echo $sub_details['description']; ?>">
                            </div>  
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label for="" class="mb-1">Date of Spend <span class="text-danger">*</span></label>
                                <input type="text" name="expense_date" class="form-control datepicker dateexpens" onchange="changedateexpense(this.value)" placeholder="" value="<?php echo $sub_details['bill_date']; ?>">
                            </div>
                           
                            <div class="col-lg-2 align-cneter expens_css" style="<?php echo $expens_css; ?>">
                                <label for="" class="mb-1">Amount <span class="text-danger">*</span></label>
                                <input type="text" name="rate_exp" class="form-control text-right" value="<?php echo $sub_details['rate'];?>" onblur="calucateamount()" id="amount_popup_exp" >
                                <input type="hidden" name="qty_exp" value="1"  class="form-control text-right " readonly id="qty_popup_exp" >
                            </div> 
                            <div class="col-lg-2 align-cneter mileage_css" style="<?php echo $mileage_css; ?>">
                                <label for="" class="mb-1">KM <span class="text-danger">*</span><span id="amount_popup_span_mileage" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?><span></label>
                                <input type="hidden" name="rate_mileage" class="form-control text-right" value="<?php echo $sub_details['rate'];?>"  id="amount_popup_mileage" >
                                <input type="text" name="qty_mileage"  onblur="calucateamount()" class="form-control text-right "  id="qty_popup_mileage" value="<?php echo $sub_details['qty'];?>">
                            </div>
                            <div class="col-lg-2 align-cneter Perdiem_css" style="<?php echo $Perdiem_css; ?>">
                                <label for="" class="mb-1">Days <span class="text-danger">*</span><span id="amount_popup_span_perd" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?><span></label>
                                <input type="hidden" name="rate_perd"  class="form-control text-right" value="<?php echo $sub_details['rate'];?>"  id="amount_popup_perd" >
                                <input type="text" name="qty_perd"  onblur="calucateamount()" class="form-control text-right "  id="qty_popup_perd" value="<?php echo $sub_details['qty'];?>">
                            </div>

                            <div class="col-lg-2">
                                <label for="" class="mb-1">Tax </label>
                                <input type="text"  class="form-control text-right rowamt_mileage" onblur="calucateamount('perd')" value="<?php echo $sub_details['tax_amount']!=""?$sub_details['tax_amount']:"0";?>" name="tax" id="tax_popup" >
                            </div>
                            <div class="col-lg-2">
                                <label for="" class="mb-1">Total</label>
                                <input type="text"  class="form-control text-right rowamt_mileage" value="<?php echo $sub_details['expense_amount'];?>" name="amount" id="total_popup" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label for="" class="mb-1">Merchant Name <span class="text-danger">*</span></label>
                                <input type="text" name="merchant_name" class="form-control" placeholder="Walmart" value="<?php echo $sub_details['merchant_name']; ?>"> 
                            </div>
                            <div class="col-lg-6">
                                <label for="" class="mb-1">Bill Number <span class="text-danger">*</span></label>
                                <input type="text" name="bill_no" class="form-control" value="<?php echo $sub_details['bill_no']; ?>"> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 w-full">
                                <label for="" class="mb-1">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency_id">
                                      <option value="">Select Any One</option>
                                    <?php foreach($arraycurrncy as $currencykey => $arraycurrncys){ ?>
                                        <option value="<?php echo $currencykey; ?>" <?php echo $main_details['currency_id']==$currencykey?"selected":"" ;?>><?php echo $arraycurrncys; ?></option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                            <div class="col-lg-6 w-full">
                                <label for="" class="mb-1">Payment Mode <span class="text-danger">*</span></label>
                                <select class="form-control js-example-basic-single" name="payment_mode">
                                      <option value="">Select Any One</option>
                                    <?php foreach($Payment_mode  as  $Paymentkey => $Payment_mode){ ?>
                                        <option value="<?php echo $Paymentkey; ?>" <?php echo $sub_details['payment_mode']==$Paymentkey?"selected":"" ;?>><?php echo $Payment_mode; ?></option>
                                  <?php } ?>                                      
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 w-full">
                                <label for="" class="mb-1">Project</label> 
                                <select class="form-control js-example-basic-single" name="project_id">
                                      <option value="0">Select Any One</option>
                                <?php foreach($projects_list as $projects_list){ ?>
                                    <option value="<?php echo $projects_list['id']; ?>" <?php echo $sub_details['project_id']== $projects_list['id']?"selected":"" ;?>><?php echo $projects_list['name']; ?></option>
                                
                                <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label  class="mb-1">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="New York" value="<?php echo $sub_details['location']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 w-full">
                                <label for="" class="mb-1">Add to Report</label>
                               <select class="form-control js-example-basic-single" name="Rid">
                                  <option value="0">Select Any One</option>
                                <?php foreach($expense_list as $expense_lists){ ?>
                                    <option value="<?php echo $expense_lists['id']; ?>" <?php echo $expense_lists['id']== $main_details['id']?"selected":"";?>> <?php echo $expense_lists['expense_code']." (".$expense_lists['amount'].")"; ?></option>
                                
                                <?php } ?>
                                </select>

                                
                            </div>
                            <div class="col-lg-6">
                                <label for="" class="mb-1">Cost Center</label>
                                <input type="text" name="cost_center_name" class="form-control" placeholder="" value="<?php echo $emp_details['cost_center_name']; ?>" readonly>
                                <input type="hidden" name="cost_center_id" class="form-control" name="cost_ceter_id" placeholder="" value="<?php echo $emp_details['cost_center_id']; ?>" readonly>
                            </div>
                        </div>
                        <?php /** ?>
                        <div class="form-group row">
                            <div class="col-lg-6 w-full">
                                <select class="form-control js-example-basic-single">
                                    <option>Option</option>
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                </select>
                            </div>
                             <div class="col-lg-3">
                                <label for="" class="m-t-30"></label>
                                <div class="checkbox-fade fade-in-primary">
                                    <label class="form-label">
                                      <input type="checkbox" id="checkbox" name="Language" value="HTML">
                                      <span class="cr">
                                       <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                     </span>
                                     <span>Assign Expense</span>
                                   </label>
                                 </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="" class="m-t-30"></label>
                                <div class="checkbox-fade fade-in-primary">
                                    <label class="form-label">
                                      <input type="checkbox" id="checkbox" name="Language" value="HTML">
                                      <span class="cr">
                                       <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                     </span>
                                     <span>Billable</span>
                                   </label>
                                 </div>
                            </div>
                        </div>
                        <?php **/ ?>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="f-20 border p-2">Receipt</h3>
                        <div class="card-block">
						      <input type="hidden" id="oldattachment" name="oldattachment" value='<?php echo $sub_details['bill_file']; ?>'>
                            <div class="drop-zone">
                                <?php if($sub_details['bill_file'] !=""){ ?> 
                                    <div class="drop-zone__thumb" data-label="policy.png" style="background-image: url(<?php echo $File_Uploads_Path.$sub_details['bill_file']; ?>)"></div>
                                <?php  }else{ ?>
                                    <span class="drop-zone__prompt">Drop file here or click to upload</span>
                                <?php } ?>
                                <input type="file" name="myFile" class="drop-zone__input" id="popattachment" >
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="modal-footer">
        <?php //print_r($_POST); ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		<?php 
		//$disable = "disabled";
		//if($main_details['current_status']=="" || $main_details['current_status']==$statusList[0]['status']){ //AND $main_details['created_by'] == $_SESSION['userid'])
		//	$disable = "";
		//}
		?>
        <button type="button" class="btn btn-primary" onclick="submitrom()" <?php echo $disable; ?>>Submit</button>
    </div>

<script type="text/javascript">
         $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $( ".datepicker" ).datepicker({
            showOn: "both", 
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        });
  document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
  const dropZoneElement = inputElement.closest(".drop-zone");

  dropZoneElement.addEventListener("click", (e) => {
    inputElement.click();
  });

  inputElement.addEventListener("change", (e) => {
    if (inputElement.files.length) {
      updateThumbnail(dropZoneElement, inputElement.files[0]);
    }
  });

  dropZoneElement.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZoneElement.classList.add("drop-zone--over");
  });

  ["dragleave", "dragend"].forEach((type) => {
    dropZoneElement.addEventListener(type, (e) => {
      dropZoneElement.classList.remove("drop-zone--over");
    });
  });

  dropZoneElement.addEventListener("drop", (e) => {
    e.preventDefault();

    if (e.dataTransfer.files.length) {
      inputElement.files = e.dataTransfer.files;
      updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
    }

    dropZoneElement.classList.remove("drop-zone--over");
  });
});

/**
 * Updates the thumbnail on a drop zone element.
 *
 * @param {HTMLElement} dropZoneElement
 * @param {File} file
 */
function updateThumbnail(dropZoneElement, file) {
  let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

  // First time - remove the prompt
  if (dropZoneElement.querySelector(".drop-zone__prompt")) {
    dropZoneElement.querySelector(".drop-zone__prompt").remove();
  }

  // First time - there is no thumbnail element, so lets create it
  if (!thumbnailElement) {
    thumbnailElement = document.createElement("div");
    thumbnailElement.classList.add("drop-zone__thumb");
    dropZoneElement.appendChild(thumbnailElement);
  }

  thumbnailElement.dataset.label = file.name;

  // Show thumbnail for image files
  if (file.type.startsWith("image/")) {
    const reader = new FileReader();

    reader.readAsDataURL(file);
    reader.onload = () => {
      thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
    };
  } else {
    thumbnailElement.style.backgroundImage = null;
  }
}

</script>