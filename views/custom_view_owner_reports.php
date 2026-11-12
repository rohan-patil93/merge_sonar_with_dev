<?php 
$_SESSION['apply_js']['multiselest'] = "1";

$url = API."?mode=getownerreports&property_owner_id=".$_POST['property_owner_id']; 
$api = callAPI($url);
$result = json_decode($api,1);	
//echo "<pre>";print_r($result); echo "</pre>";
$property_owners = $result['property_owners'];
$property_owners_all = $result['property_owners_all'];
$banks = $result['banks'];
$property_owner_details = $result['property_owner_details'];
$property_types = $result['property_types'];
$property_owner_detail_areas = $result['property_owner_detail_areas'];
?>
<style type="text/css">.data{ display: none; }</style>
<script type="text/javascript">
	function openSubTable(id,levelcheck,levelchideshow){
		var showhide = $("#"+levelcheck+"_"+id).val();
		console.log(showhide);
		if(showhide=='0'){
			$("."+levelchideshow+"_"+id).show('slow');
			$("#"+levelcheck+"_"+id).val('1');
		}else{
			$("."+levelchideshow+"_"+id).hide('slow');
			$("#"+levelcheck+"_"+id).val('0');
		}
	}
</script>
<div class="card">
		<form method="post">
		<div class="card-header  row">
	        <div class="col-lg-8">
	            <h5>Owner Reports</h5>
	        </div>
	        <div class="col-lg-2">
	            <div class="form-group w-full table-card-header">
	                <label class="form-label ">Owner List<span class="text-danger">*</span></label>
					<select name="property_owner_id" class="form-control form-select js-example-basic-single" >
						<option value="">Select Any One </option>
						<?php foreach($property_owners_all as $po){ ?>
							<option value="<?php echo $po['id']; ?>" <?php echo $po['id'] == $_POST['property_owner_id']?"selected":""; ?>><?php echo $po['owner_name']; ?></option>
						<?php } ?>
					</select>
	            </div>
	        </div>
	        <div class="col-lg-1">
	             <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
	        </div>
	        <div class="col-lg-1">
	         	<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>  
	         </div>
	    </div>
	</form>
<!-- 	<div class="card-header  row">
        <div class="col-lg-7">
            <h5>Owner Reports</h5>
        </div>
        <div class="col-lg-2">
            <div class="form-group w-full table-card-header">
                <label class="form-label ">Owner List<span class="text-danger">*</span></label>
				<select name="property_owner_detail_id" class="form-control form-select js-example-basic-single" >
					<option value="">Select Any One </option>
				</select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class=" form-group">
                <label class="form-label"> Owner Name</label>
                <input type="text" class="form-control" name="month" id="month" value="" >
            </div>
        </div>
         <div class="col-lg-1">
         	<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>  
         </div>
    </div> -->
<!-- 	<div class="card-header">

		<h5>Owner Reports</h5>
		<div class="card-header-right table-card-header">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>  
		</div>
	</div> -->
	<div class="card-body">
		<?php if(count((array)$property_owners) !=0){  ?>
			<table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >	
				<tr> 
					<th></th>	
					<th>#</th>		
					<th>Owner Name</th>	
					<th>Address</th>					
					<th>GST No</th>	
					<th>Bank Nam</th>	
					<th>Bank Account No</th>
					<th>No. Property</th>	
					<th></th>
				</tr>
				<?php $parenttb = 1; foreach($property_owners as $po){ 
					?>
					<tr class="main" onclick="openSubTable('<?php echo $parenttb; ?>','firstlevelchildcheck','firstlevelchildhideshow')">
						<td class="cursor-pointer">
							<i class="ti-angle-double-down" ></i>
							<input type="hidden" id="firstlevelchildcheck_<?php echo $parenttb; ?>" value="0">
						</td>
						<td><?php echo $parenttb; ?></td>
						<td><?php echo $po['owner_name']; ?></td>
						<td><?php echo emptytextdashShow($po['address']); ?></td>
						<td><?php echo emptytextdashShow($po['gst_no']); ?></td>
						<td><?php echo emptytextdashShow($banks[$po['bank_id']]); ?></td>
						<td><?php echo emptytextdashShow($po['bank_account_no']); ?></td>
						<td><label class="form-label label label-md label-primary"><?php echo count((array)$property_owner_details[$po['id']]); ?></label></td>
						<td>
						 	<a href="index?option=COM_INSERT_PROPERTY_OWNERS&Rid=<?php echo deocdeId('id:'.$po['id']); ?>" target="_blank"><button type="button" class="cursor-pointer float-left btn btn-primary"> View </button></a>
						</td>
					</tr>

					<tr class="data firstlevelchildhideshow_<?php echo $parenttb; ?>">
						<td></td>
						<td colspan="10" class="p-0">
							<table class="table table-separate">
								<thead>
									<tr> 
										<th></th>	
										<th>#</th>	
										<th>Property Name</th>	
										<th>Property Address</th>					
										<th>Property Type</th>	
										<th>Property Sub Type</th>	
										<th>No. Area</th>
									</tr>
								</thead>
								<tbody>
								<?php $pods = $property_owner_details[$po['id']]; 
								$parentchildtb = 1;
									foreach($pods as $pod){
								?>
									<tr onclick="openSubTable('<?php echo $parentchildtb; ?>','secondlevelchildcheck','secondlevelchildhideshow')"> 
										<td class="cursor-pointer">
											<i class="ti-angle-double-down" ></i>
											<input type="hidden" id="secondlevelchildcheck_<?php echo $parentchildtb; ?>" value="0">
										</td>
										<td><?php echo $parentchildtb; ?></td>		
										<td><?php echo $pod['property_name']; ?></td>	
										<td class="w-b-w-s"><?php echo emptytextdashShow($pod['property_address']); ?></td>	
										<td><?php echo $property_types[$pod['property_type']]; ?></td>	
										<td><?php echo $property_types[$pod['property_sub_type']]; ?></td>	
										<td><label class="form-label label label-md label-primary"><?php echo count((array)$property_owner_detail_areas[$pod['id']]); ?></label></td>
										
									</tr>
									<tr class="data secondlevelchildhideshow_<?php echo $parentchildtb; ?>">
										<td></td>
										<td colspan="10" class="p-0">
										<table class="table table-separate">
											<thead>
												<tr> 
													<th></th>	
													<th>#</th>	
													<th>Area Name</th>	
													<th class="text-right">Area</th>					
													<th class="text-right">Per Sq. Ft. Rate</th>	
													<th>Status</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$parentchildtb_2 = 1; 
												$podas = $property_owner_detail_areas[$pod['id']];
												foreach($podas as $poda){ ?>
												<tr> 
													<td></td>	
													<td><?php echo $parentchildtb_2; ?></td>			
													<td><?php echo $poda['property_area_name']; ?></td>	
													<td class="text-right"><?php echo $poda['property_area']; ?></td>	
													<td class="text-right"><?php echo $poda['per_sq_ft_rate']; ?></td>	
													<td> - </td>
													
												</tr>
											<?php $parentchildtb_2++; } ?>
											</tbody>
										</table>
									</td>
									</tr>
								<?php $parentchildtb++; }  ?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php $parenttb++;} ?>

			</table>
		<?php  }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Owner Reports</p>   
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>   
                </div>
            <?php } ?>




	</div>
</div>