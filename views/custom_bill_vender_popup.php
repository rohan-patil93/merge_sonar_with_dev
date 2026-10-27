<?php //print_r($_POST); 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//print_r($_POST);
$url = API."?mode=togetreportvillbaseidvendors&val=".$_POST['val'];
$apires = callAPI($url); 
$datas = json_decode($apires,1);

?>

<div class="modal-header">
	<h4 class="modal-title">Vendor Bills</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
	<div class="j-content">
		<form id="assetsallocationform" method="post" >	
			<div class="row ">
				<div class="dt-responsive table-responsive">
					<table  class="table table-striped table-bordered nowrap"> 
						<thead>
							<tr>
								<th>Sl No</th>
								<th>Vendor</th>
								<th>Invoice Number</th>
								<th>Invoice Date</th>
								<th>Due Date</th>
								<th>Amount</th>
								<th>Due Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php $i=1; foreach($datas['bills'] as $bill){ 
								$pam = $bill['pending_amount']!="" ? $bill['pending_amount'] : $bill['total_amount'];

							?>
								<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $datas['vendors'][$bill['party_id']]['display_name'] ; ?></td>
								<td><?php echo $bill['transaction_number']; ?></td>
								<td><?php echo  fromDate($bill['transaction_date']); ?></td>
								<td ><?php echo  fromDate(date("Y-m-d", strtotime($bill['transaction_date'] . ' + ' . $bill['payment_terms'] . 'day'))); ?></td>
								<td class="text-"><?php echo customeummarmonyformate($bill['total_amount']); ?></td>
								<td><?php echo customeummarmonyformate($pam); ?></td><td><?php echo customeummarmonyformate($value); ?></td>
                <td><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['15']); ?></td>  
                <td class="text-right"><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['30']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['45']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['60']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['90']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($returnvendorind[$vendor_id]['120']); ?></td>
							</tr>
							<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>