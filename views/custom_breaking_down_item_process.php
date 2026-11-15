<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submititem'])){
    $url = API."?mode=insertitems&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    //print_r($res);
    $response = isset($res['result']) ? $res['result'] : array();
    if(isset($response['status']) && $response['status'] == 'sucess'){
        if(isset($response['LASTID']) && $response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_ITEMS");
        }
        ?>
          <script type="text/javascript">
            document.onreadystatechange = function(){
              if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo isset($_SESSION['error_message'][$msgcode]) ? $_SESSION['error_message'][$msgcode] : ''; ?>','');
              }
            }
          </script>
          <?php
    }else{
        if(isset($response['status']) && $response['status'] == 'fail'){
            $dis_err = isset($response['other']) ? explode(",", $response['other']) : array();
            $dis_er = array();
            foreach ($dis_err as $va) {
              if (isset($dis_labels[$va])) {
                $dis_er[] = $dis_labels[$va];
              }
            }
            $err_num = isset($response['error_no']) ? $response['error_no'] : '';
            $err_msg = isset($_SESSION['error'][$err_num]) ? $_SESSION['error'][$err_num] : '';
            $error_message = $err_num ." : ".$err_msg." : ".implode(", ", (array)$dis_er);
          }else{
            $error_message = isset($response['message']) ? $response['message'] : '';
          }
    }
}
$rid1 = isset($_GET['Rid']) ? $_GET['Rid'] : '';
$rid2 = function_exists('encodeId') ? encodeId($rid1) : $rid1;
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = isset($rrid[1]) ? $rrid[1] : '';
$url = API."?mode=getbreakingdownitemprocess&rid=".$rid;
$apires = callAPI($url);
$result = !empty($apires) ? json_decode($apires, 1) : array();
//print_r($result);
$items = isset($result['items']) && is_array($result['items']) ? $result['items'] : array();
$unit_of_measurements = isset($result['unit_of_measurements']) && is_array($result['unit_of_measurements']) ? $result['unit_of_measurements'] : array();
?>
<div class="card">
	<div class="card-header">
	    <h5><?= __("Breaking Down Item Process") ?></h5>
		<div class="card-header-right">
	        <button type="button" onclick="history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply" aria-hidden="true"></i> <?= __("Back") ?> </button>
	    </div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4">
	            <div class="form-group">
	                <label class="form-label" for="process_name"><?= __("Process Name") ?> &nbsp;<span class="text-danger">*</span></label>
	                 <span class="d-block error-message text-danger"></span>
	                <input class="form-control requried_field" type="text" id="process_name" name="process_name" value="<?php echo htmlspecialchars($items['process_name'] ?? '', ENT_QUOTES); ?>" aria-label="Process Name">
	            </div>
	            <div class="form-group">
	                <label class="form-label" for="breaking_down_item_id"><?= __("Breaking Down Item") ?> &nbsp;<span class="text-danger">*</span></label>
	                <span class="d-block error-message text-danger"></span>
	                <select class="form-select border js-example-basic-single requried_field" id="breaking_down_item_id" name="breaking_down_item_id" aria-label="Breaking Down Item Id">
	                    <option value="0"><?= __("Select Any one") ?> </option>
	                    <?php if (!empty($items) && is_array($items)) {
	                        foreach ($items as $itm) {
	                            if (!is_array($itm)) continue;
	                            $selected = (isset($items['breaking_down_item_id']) && isset($itm['id']) && $items['breaking_down_item_id'] == $itm['id']) ? 'selected="selected"' : '';
	                    ?>
	                        <option value="<?php echo htmlspecialchars((string)($itm['id'] ?? ''), ENT_QUOTES); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars(($itm['sku'] ?? '') . " - " . __($itm['item_name'] ?? ''), ENT_QUOTES); ?></option>
	                    <?php   }
	                    } ?>
	                </select>
	            </div>
	            <div class="form-group">
	                <label class="form-label" for="breaking_down_item_date"><?= __("Date") ?></label>
	                <input class="form-control datepicker" type="text" id="breaking_down_item_date" name="breaking_down_item_date" value="<?php echo (!empty($items['breaking_down_item_date']) && function_exists('fromDate')) ? fromDate($items['breaking_down_item_date']) : (!empty($items['breaking_down_item_date']) ? htmlspecialchars($items['breaking_down_item_date'], ENT_QUOTES) : ''); ?>" aria-label="Breaking Down Item Date">
	            </div>
	            <div class="form-group">
	                <label class="form-label" for="parent_quantity"><?= __("Quantity") ?></label>
	                <input class="form-control" type="text" id="parent_quantity" name="quantity" value="<?php echo !empty($items['quantity']) ? htmlspecialchars($items['quantity'], ENT_QUOTES) : 1; ?>" readonly aria-label="Quantity">
	            </div>
	        </div>
	        <div class="col-lg-8">
	        	<div class="row">
	        		<div class="col-lg-5">
	        			<label class="form-label" for="child_item_id"><?= __("Item Name") ?></label>
		            </div>
	        		<div class="col-lg-3">
	        			<label class="form-label" for="child_purchase_uom"><?= __("UOM") ?> </label>
		            </div>
	        		<div class="col-lg-2">
	        			<label class="form-label" for="item_quantity"><?= __("Quantity") ?></label>
	        		</div>
	        		<div class="col-lg-2">
	        		</div>
	        	</div>
	        	<div class="row">
	        		<div class="form-group col-lg-5">
		                <select class="form-select border js-example-basic-single" id="child_item_id" name="purchase_uom" aria-label="Item Name">
		                    <option value="0"><?= __("Select Any one") ?> </option>
		                    <?php if (!empty($items) && is_array($items)) {
		                        foreach ($items as $itm) {
		                            if (!is_array($itm)) continue;
		                            $selected = (isset($items['purchase_uom']) && isset($itm['id']) && $items['purchase_uom'] == $itm['id']) ? 'selected="selected"' : '';
		                    ?>
		                        <option value="<?php echo htmlspecialchars((string)($itm['id'] ?? ''), ENT_QUOTES); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars(($itm['sku'] ?? '') . " - " . __($itm['item_name'] ?? ''), ENT_QUOTES); ?></option>
		                    <?php   }
		                    } ?>
		                </select>
		            </div>
		            <div class="form-group col-lg-3">
		            	<select class="form-select border js-example-basic-single" id="child_purchase_uom" name="purchase_uom" aria-label="UOM">
		                    <option value="0"><?= __("Select Any one") ?></option>
		                    <?php if (!empty($unit_of_measurements) && is_array($unit_of_measurements)) {
		                        foreach ($unit_of_measurements as $uomr) {
		                            if (!is_array($uomr)) continue;
		                            $selected = (isset($items['purchase_uom']) && isset($uomr['id']) && $items['purchase_uom'] == $uomr['id']) ? 'selected="selected"' : '';
		                    ?>
		                        <option value="<?php echo htmlspecialchars((string)($uomr['id'] ?? ''), ENT_QUOTES); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars(__($uomr['uom_name'] ?? '') . " - " . ($uomr['uom_short_code'] ?? ''), ENT_QUOTES); ?></option>
		                    <?php   }
		                    } ?>
		                </select>
		            </div>
		            <div class="form-group col-lg-2">
		                <input class="form-control" type="text" id="item_quantity" name="quantity" value="<?php echo !empty($items['quantity']) ? htmlspecialchars($items['quantity'], ENT_QUOTES) : 1; ?>" aria-label="Quantity">
		            </div>
		            <div class="form-group col-lg-1">
		            	<i class="ti ti-trash" aria-hidden="true"></i>
		            </div>
	        	</div>
	        </div>

        </div>
	</div>
</div>
