<?php 
$_SESSION['apply_js']['multiselest'] = "1";

if(isset($_POST['submitemptytransactions'])){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertapplicationemptytransactions&method=post";
	$response1 = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response1,1);
	$response = $res['result'];
	//echo "<pre>"; print_r($res); echo "</pre>"; 
	if($response['status'] == 'sucess'){ 
		if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
           	header("location:index?option=COM_LIST_APPLICATION_EMPTY_TRANSACTIONS");
        }   
        ?>
        <script type="text/javascript">
        	document.onreadystatechange = function(){
        		if(document.readyState == "complete") {
        			tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
        		} 
        	}
        </script>
        <?php    
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

    //header("location:index?option=COM_INSERT_COMPANY_VALUATIONS");
}


$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getapplicationemptytransactions&rid=".$rid;  
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);

$module_setup = $result['module_setup'];
$application_pages = $result['application_pages'];
$application_empty_transactions = $result['application_empty_transactions'];
$transaction_table = $result['transaction_table'];
$application_messages = $result['application_messages'];
$transaction_tables_json =  json_decode($application_empty_transactions['transaction_tables'],1);
$option_type = array('list_page_name'=>'List Option','insert_page_name'=>'Insert Option');
//echo "<pre>"; print_r($application_empty_transactions);echo "</pre>"; 
?>
<script type="text/javascript">
	var maxsar = 100;
	function addMoreApplicationEmptyTransactions(){
		maxsar = maxsar+1;
		var id = 'rowapplicationemptytransactions_'+maxsar; 
	    var html ="";
	    html += '<div class="row mt-3" id="'+id+'">';
	    html += '<div class="col-lg-3"><select name="transaction_table[]" class="form-control form-select js-example-basic-single"><option value="">Select Any One </option><?php foreach($transaction_table as $tableval) {  ?><option value="<?php echo $tableval; ?>"><?php echo $tableval;?></option><?php } ?></select></div>';
	    html += '<div class="col-lg-2"><select name="option_type[]" class="form-control form-select js-example-basic-single"> <?php foreach($option_type as $optkey=>$optype) {  ?><option value="<?php echo $optkey; ?>" <?php echo $optkey ==$ttj['transaction_table']?"selected":""; ?>><?php echo $optype;?></option><?php } ?></select></div>';

	    html += '<div class="col-lg-3"><select name="application_message_code[]" class="form-control form-select js-example-basic-single"><option value="">Select Any One </option><?php foreach($application_messages as $amessage) {  ?><option value="<?php echo $amessage['message_number']; ?>" ><?php echo preg_replace("/\s+/", " ", $amessage['message_number']." - ".$amessage['message_description']);?></option><?php } ?></select></div>';
	    
	    html += '<div class="col-lg-3"><textarea type="text" class="form-control" name="extra_message[]" ></textarea> </div>';

	    html += '<div class="col-lg-1"><i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeRow(\''+id+ '\')"></i></div>';
	    html += '</div>';

		$("#newaddmoreapplicationemptytransactions").append(html);
	    $(".js-example-basic-single").select2();
	}
	function removeRow(idd){
		$("#"+idd).remove();
	}
	function getTransactionTable(rowseq){
		var transaction_table = $("#transaction_table_"+rowseq).val();
		$("#extra_message_"+rowseq).val(transaction_table);
	}
	


</script>
<div class="card custom-card ">
    <div class="card-header">
        <h5> Empty Transactions </h5>
        <div class="card-header-right table-card-header">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>                  
        </div>
    </div>
    <div class="card-body">
    	<form method="post">
    	<div class="row">
	    	<div class="form-group col-lg-5">
		        <label class="form-label">Module Setup </label>
		        <select name="module_setup_id" class="form-control form-select js-example-basic-single">
		            <option value="">Select Any One </option>
		            <?php foreach($module_setup as $mods) {  ?>
		              <option value="<?php echo $mods['module_id']; ?>" <?php echo $mods['module_id'] ==$application_empty_transactions['module_setup_id']?"selected":""; ?>><?php echo $mods['display_name'];?></option>
		            <?php } ?>
		        </select>  
	        </div>
	        <div class="form-group col-lg-2 text-center ">
	        	<label class="form-label  mt-4"> OR </label>
	        </div>
	        <div class="form-group col-lg-5">
	          	<label class="form-label"> Option <span class="text-danger">*</span></label>
	          	<select name="page_option" class="form-control form-select js-example-basic-single">
		            <option value="">Select Any One </option>
		            <?php foreach($application_pages as $optionkey=>$val) {  ?>
		              <option value="<?php echo $optionkey; ?>" <?php echo $optionkey ==$application_empty_transactions['page_option']?"selected":""; ?>><?php echo $val;?></option>
		            <?php } ?>
		        </select>  
	        </div>
	    </div>
	    <div class="row">
	    	<div class="col-lg-3">
	    		<label class="form-label"> Transaction Table <span class="text-danger">*</span></label>
	    	</div>
	    	<div class="col-lg-2">
	    		<label class="form-label"> Option Type <span class="text-danger">*</span></label>
	    	</div>
	    	<div class="col-lg-3">
	    		<label class="form-label"> Application Message <span class="text-danger">*</span></label>
	    	</div>
	    	<div class="col-lg-3">
	    		<label class="form-label"> Extra Message <span class="text-danger">*</span></label>
	    	</div>
	    </div>
	    <div id="newaddmoreapplicationemptytransactions">
	    	<?php 
	    		$im = 1; 
	    		foreach($transaction_tables_json as $ttj){ 
	    			$rowid = 'rowapplicationemptytransactions_'.$im; 
	    	?>
			    <div class="row mt-2" id="<?php echo $rowid; ?>">
			    	<div class="col-lg-3">
			    		<select name="transaction_table[]"  id="transaction_table_<?php echo $im; ?>" class="form-control form-select js-example-basic-single" onchange="getTransactionTable('<?php echo $im; ?>')">
				            <option value="">Select Any One </option>
				            <?php foreach($transaction_table as $tableval) {  ?>
				              <option value="<?php echo $tableval; ?>" <?php echo $tableval ==$ttj['transaction_table']?"selected":""; ?>><?php echo $tableval;?></option>
				            <?php } ?>
				        </select>  
			    	</div>
			    	<div class="col-lg-2">
			    		<select name="option_type[]" class="form-control form-select js-example-basic-single">
				            <?php foreach($option_type as $optkey=>$optype) {  ?>
				              <option value="<?php echo $optkey; ?>" <?php echo $optkey ==$ttj['option_type']?"selected":""; ?>><?php echo $optype;?></option>
				            <?php } ?>
				        </select>  
			    	</div>
			    	<div class="col-lg-3">
			    		<select name="application_message_code[]" class="form-control form-select js-example-basic-single">
				            <option value="">Select Any One </option>
				            <?php foreach($application_messages as $amessage) {  ?>
				              <option value="<?php echo $amessage['message_number']; ?>" <?php echo $amessage['message_number'] ==$ttj['application_message_code']?"selected":""; ?>><?php echo $amessage['message_number']." - ".$amessage['message_description'];?></option>
				            <?php } ?>
				        </select>  
			    	</div>
			    	<div class="col-lg-3">
			    		<textarea type="text" class="form-control" name="extra_message[]" id="extra_message_<?php echo $im; ?>"><?php echo $ttj['extra_message']; ?></textarea> 
			    	</div>
			    	<div class="col-lg-1">
			    		<i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeRow('<?php echo $rowid; ?>')"></i>
			    	</div>
			    </div>
			<?php $im++; } ?>
		</div>
	    <div class="mt-3 mb-5">
			<button type="button"  class="btn btn-success" onclick="addMoreApplicationEmptyTransactions()"><i class="ti-plus"></i>  Add More Transaction</button>
		</div>
		<div>
			<input type="hidden" name="rid" value="<?php echo $rid; ?>">  
			<button type="submit" class="btn btn-primary waves-effect waves-light" name="submitemptytransactions"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button> 
		</div>
	</form>
    </div>
</div>