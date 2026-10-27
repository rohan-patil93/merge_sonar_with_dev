<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submititem'])){
    $url = API."?mode=insertitems&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    //print_r($res);
    $response = $res['result'];
    if($response['status'] == 'sucess'){ 
        if($response['LASTID'] == 0){
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
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
              } 
            }
          </script>
          <?php    
    }else{
        if($response['status'] == 'fail'){
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
$rid1 =	$_GET['Rid'];
$rid2 = encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = $rrid['1'];
$url = API."?mode=getbreakingdownitemprocess&rid=".$rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);
//print_r($result);
$items = $result['items']; 
$unit_of_measurements = $result['unit_of_measurements'];
?>
<div class="card">
	<div class="card-header">
	    <h5>Breaking Down Item Process</h5>
		<div class="card-header-right">
	        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i> Back </a>
	    </div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4">
	            <div class="form-group">
	                <label class="form-label">Process Name &nbsp;<span class="text-danger">*</span></label>
	                 <span class="d-block error-message text-danger"></span>
	                <input class="form-control requried_field" type="text" id="process_name" name="process_name" value="<?php echo $items['process_name'] ?>">
	            </div>
	            <div class="form-group">
	                <label class="form-label">Breaking Down Item &nbsp;<span class="text-danger">*</span></label>
	                <span class="d-block error-message text-danger"></span>
	                <select class="form-select border js-example-basic-single requried_field" id="breaking_down_item_id" name="breaking_down_item_id">
	                    <option value="0">Select Any one </option>
	                    <?php foreach ($items as $itm) { ?>
	                        <option value="<?php echo $itm['id']; ?>" <?php  if($items['breaking_down_item_id']==$itm['id']){echo "selected=selected";}  ?>><?php echo $itm['sku']." - ".$itm['item_name'] ?></option>
	                    <?php } ?>
	                </select>
	            </div>
	            <div class="form-group">
	                <label class="form-label">Date</label>
	                <input class="form-control datepicker" type="text"  name="breaking_down_item_date" value="<?php echo $items['breaking_down_item_date'] !=""?fromDate($items['breaking_down_item_date']):''; ?>">
	            </div>
	            <div class="form-group">
	                <label class="form-label">Quantity</label>
	                <input class="form-control" type="text" id="quantity" name="quantity" value="<?php echo $items['quantity'] !=""?$items['quantity']:1; ?>" readonly>
	            </div>
	        </div>
	        <div class="col-lg-8">
	        	<div class="row">
	        		<div class="col-lg-5">
	        			<label class="form-label">Item Name</label>
		            </div>
	        		<div class="col-lg-3">
	        			<label class="form-label">UOM </label>
		            </div>
	        		<div class="col-lg-2">
	        			<label class="form-label">Quantity</label>
	        		</div>
	        		<div class="col-lg-2">
	        		</div>
	        	</div>
	        	<div class="row">
	        		<div class="form-group col-lg-5">
		                <select class="form-select border js-example-basic-single"  name="purchase_uom">
		                    <option value="0">Select Any one </option>
		                    <?php foreach ($items as $itm) { ?>
		                        <option value="<?php echo $itm['id']; ?>" <?php  if($items['purchase_uom']==$itm['id']){echo "selected=selected";}  ?>><?php echo $itm['sku']." - ".$itm['item_name'] ?></option>
		                    <?php } ?>
		                </select>
		            </div>
		            <div class="form-group col-lg-3">
		            	<select class="form-select border js-example-basic-single" name="purchase_uom">
		                    <option value="0">Select Any one</option>
		                    <?php foreach ($unit_of_measurements as $uomr) { ?>
		                        <option value="<?php echo $uomr['id']; ?>" <?php  if($items['purchase_uom']==$uomr['id']){echo "selected=selected";}  ?>><?php echo $uomr['uom_name']." - ".$uomr['uom_short_code'] ?></option>
		                    <?php } ?>
		                </select>
		            </div>
		            <div class="form-group col-lg-2">
		                <input class="form-control" type="text" id="quantity" name="quantity" value="<?php echo $items['quantity'] !=""?$items['quantity']:1; ?>" >
		            </div>	
		            <div class="form-group col-lg-1">
		            	<i class='ti ti-trash'></i>
		            </div>
	        	</div>
	        </div>

        </div>
	</div>
</div>