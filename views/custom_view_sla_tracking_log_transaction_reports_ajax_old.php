<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//print_r($_POST);
$_POST['transaction_table'] = "sales_invoices";
$_POST['transaction_id'] = "1";
$getapi = API."?mode=getslatrackinglogreports&ajaxcll=Y&transaction_table=".$_POST['transaction_table']."&transaction_id=".$_POST['transaction_id']."&from_date=".$_POST['from_date']."&to_date=".$_POST['to_date'];
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);


//echo "<pre>"; print_r($result); echo "</pre>";

$transaction_result = $result['transaction_result'];
$sla_tracking_logs = $result['sla_tracking_logs'];
$statuse = ViewStatusData();
// $usercoulumns = array();
// foreach($transaction_result as $tres){
// 	$slatracklog = $sla_tracking_logs[$tres['id']];
// 	foreach($slatracklog as $oguid=>$slalg){
// 		//print_r($slalg);
// 		$usercoulumns[$oguid] = $oguid;
// 	}

// }
$salesinvoicetransactiontable = array('sales_invoices','sales_returns','delivery_orders','sales_orders','estimates','enquiries');
$sla_transaction_tracking_result = $result['sla_transaction_tracking_result'];
$usercoulumns = array();
foreach($sla_transaction_tracking_result as $tres){
	foreach($salesinvoicetransactiontable as $transinvoice){
		$slatracklog = $tres[$transinvoice];
		//$slatracklog = $sla_tracking_logs[$tres['id']];
		foreach($slatracklog['sla_tracking_logs'] as $oguid=>$slalg){
			$usercoulumns[$transinvoice][$oguid] = $oguid;
		}
	}
}

//print_r($usercoulumns);
?>

<?php  if(count((array)$sla_transaction_tracking_result) != 0){ ?>
<table class="table table-hover m-b-0  mt-3" style="font-size:13px;" id="costcenterfilter">
	<thead>
		<tr>
			<th rowspan="2" class="border">Transaction No.</th>
			<th rowspan="2" class="border">Originator</th>
			<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>
			<th colspan="<?php echo count((array)$usercoulumns[$transinvoice])+1; ?>" class="text-center border"><?php echo ucwords(str_replace('_',' ',$transinvoice)); ?></th>
			<?php } ?>
			<th rowspan="2" class="border">Transaction Total Time</th>
		</tr>
		<tr>
		<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>	
			<?php foreach($usercoulumns[$transinvoice] as $cuserid){ ?>
			<th class="text-center"><?php echo $_SESSION['users'][$cuserid]!=""?ucwords($_SESSION['users'][$cuserid]):"-"; ?></th>
			<?php } ?>
			<th class="text-center">Total Time</th>
		<?php } ?>	
		</tr>
	</thead>
	<tbody>
		<?php  foreach($sla_transaction_tracking_result as $tres){
			$transactiontotaltime = array();
		 ?>
		<tr>
			<td class="p-2 text-center"><a class="d-block text-primary" href="index.php?option=COM_QO_PRINT&rid=<?php echo $tres['sales_invoices']['id']; ?>&table=sales_invoices&title=Sales+Invoices" <?php echo $edit_permissionbtn; ?>> <?php echo $tres['sales_invoices']['transaction_number']." - ".$tres['sales_invoices']['transaction_date']; ?> </a></td>
			<td class="p-2 text-center"><?php echo ucwords($_SESSION['users'][$tres['sales_invoices']['originator']]); ?></td>
			<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>
			<?php 
				$slatracklog = $tres[$transinvoice];
				$totaltimetransaction = array();
				foreach($usercoulumns[$transinvoice] as $uidd){ 
					
					$userdateTimes  = array();
					$userdateTimes[] = end($totaltimetransaction);
					$starttimeary ="";
					if($starttimeary !=""){
						$userdateTimes[] = $starttimeary;
					}
					foreach($slatracklog['sla_tracking_logs'][$uidd] as $subslalg){
						$userdateTimes[] = $subslalg['request_datetime'];
						$totaltimetransaction[] = $subslalg['request_datetime']; 
						$starttimeary= $subslalg['request_datetime'];
						$transactiontotaltime[] = $subslalg['request_datetime'];
					}
					$usertime = totalHoursAndMinutesInDateTimeArray($userdateTimes);
					?>
			<td class="p-2 text-center">
				<!-- <span class="f-10">(<?php //echo $usertime['hours']." Hours : ".$usertime['minutes']." Minutes : ".$usertime['seconds']." seconds"; ?>)</span> -->
				<span class="f-10">(<?php echo $usertime['hours']." Hours : ".$usertime['minutes']." Minutes"; ?>)</span>
			</td>
			<?php } ?>
			<td class="p-2 text-center">
				<?php 
				if(count((array)$totaltimetransaction) != 0){ 
					$transactiontime = totalHoursAndMinutesInDateTimeArray($totaltimetransaction); 
					//echo $transactiontime['hours']." Hours : ".$transactiontime['minutes']." Minutes : ".$transactiontime['seconds']." Seconds";
					echo $transactiontime['hours']." Hours : ".$transactiontime['minutes']." Minutes";
					}else{
						echo "-";
					} 
				?>
			</td>
			<?php } ?>	

			<td class="p-2 text-center">
				<?php 
				if(count((array)$transactiontotaltime) != 0){ 
					$transactiontotaltimes = totalHoursAndMinutesInDateTimeArray($transactiontotaltime); 
					//echo $transactiontotaltimes['hours']." Hours : ".$transactiontotaltimes['minutes']." Minutes : ".$transactiontotaltimes['seconds']." Seconds";
					echo $transactiontotaltimes['hours']." Hours : ".$transactiontotaltimes['minutes']." Minutes";
					}else{
						echo "-";
					} 
				?>
			</td>


		</tr>
		<?php } ?>
	</tbody>
</table>
<?php }else{  ?>
	<div class="card-px text-center ">
		<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Transactions</p>   
		<div class="text-center px-4">
			<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		</div>   
	</div>

<?php } ?>


















<?php
// $dateIntervals = [
//     ['start' => '2023-01-01 10:00:43', 'end' => '2023-01-01 10:00:45'],
//     ['start' => '2023-01-01 10:00:45', 'end' => '2023-01-01 10:00:50'],
//     ['start' => '2023-01-01 10:00:50', 'end' => '2023-01-01 10:00:00'],
// ];

// $totalSeconds = 0;
// foreach ($dateIntervals as $interval) {
//     $start = new DateTime($interval['start']);
//     $end = new DateTime($interval['end']);
//     $duration = $end->getTimestamp() - $start->getTimestamp();
//     if ($duration > 0) {
//         $totalSeconds += $duration;
//     }
// }
// $days = floor($totalSeconds / 86400);
// $hours = floor(($totalSeconds % 86400) / 3600);
// $minutes = floor(($totalSeconds % 3600) / 60);
// $seconds = $totalSeconds % 60;
// echo "Total time: {$days} days, {$hours} hours, {$minutes} minutes, {$seconds} seconds\n";



// $sla_transaction_tracking_result = $result['sla_transaction_tracking_result']['tresult'];
// $selected_transaction_table = $result['selected_transaction_table'];
// $all_transaction_tables = $result['sla_transaction_tracking_result']['all_transaction_tables'];
//echo "<pre>"; print_r($all_transaction_tables); echo "</pre>";
//$all_transaction_tables = array_reverse($all_transaction_tables);
//echo "<pre>"; print_r($result['transaction_result_sql']); echo "</pre>";
?>
<?php /** ?>
<table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
	<thead>
		<tr>
			<th>Transaction No.</th>
			<th>Originator</th>
			<th>In-Progress</th>
			<th>Total Time</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($transaction_result as $tres){
			$slatracklog = $sla_tracking_logs[$tres['id']];
		 ?>
		<tr>
			<td class="p-2"><?php echo $tres['transaction_number']." - ".$tres['transaction_date']; ?></td>
			<td class="p-2"><?php echo $_SESSION['users'][$tres['originator']]; ?></td>
			<td class="p-2">
				<?php 
				//if(count((array)$slatracklog)!='0'){
					$starttimeary = "";
					$totaltimetransaction = array();
					foreach($slatracklog as $oguid=>$slalg){
						$userdateTimes  = array();
						if($starttimeary !=""){
							$userdateTimes[] = $starttimeary;
						}
						foreach($slalg as $subslalg){
							$userdateTimes[] = $subslalg['request_datetime'];
							$totaltimetransaction[] = $subslalg['request_datetime']; 
							$starttimeary= $subslalg['request_datetime'];
							
						
						}
						$usertime = totalHoursAndMinutesInDateTimeArray($userdateTimes);

				 ?>
				<div class="p-10 z-depth-top-0" >
                    <h6><?php echo $_SESSION['users'][$oguid]; ?> <span class="f-10">(<?php echo $usertime['hours']." Hours : ".$usertime['minutes']." Minutes"; ?>)</span></h6> 
                    <table>
                    	<tr>
	                    <?php foreach($slalg as $subslalg){ ?>
	                    	<td class="text-left p-2">
	                    	<span class="d-block"><?php echo $subslalg['request_datetime']; ?></span>
	                    	<span class="d-block">
	                    		<?php echo $statuse[$subslalg['previous_status']]['name'];
	                    		if($subslalg['response_status'] !=""){	
	                    			echo " --> ".$statuse[$subslalg['response_status']]['name']; 
	                    		}
	                    	?></span>
	                    	</td>
	                	<?php } ?>
	                    </tr>
                    </table>
                </div>
                <?php } //}else{ echo "<span class='text-danger'><i class='ti-alert'></i> No Data</span>"; }  ?>
			</td>
			<td class="p-2">
				<?php 
					$transactiontime = totalHoursAndMinutesInDateTimeArray($totaltimetransaction); 
					echo $transactiontime['hours']." Hours : ".$transactiontime['minutes']." Minutes"; 
				?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>



<?php ** ?>
<table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
	<thead>
		<tr>
			<th>Transaction No.</th>
			<th>Originator</th>
			<?php foreach($all_transaction_tables as $attable){ ?>
				<th colspan="2" class="text-center"><?php echo $attable; ?></th>
			<?php } ?>
			<th>Transaction Time</th>
		</tr>
	</thead>
	<tbody>
		<?php $totaltimetransactionary = array(); foreach($sla_transaction_tracking_result[$selected_transaction_table] as $tres){ ?>
		<tr>
			<td class="p-2"><?php echo $tres['transaction_number']." - ".$tres['transaction_date']; ?></td>
			<td class="p-2"><?php echo $_SESSION['users'][$tres['originator']]; ?></td>
			<?php foreach($all_transaction_tables as $attable){ 
					$totaltimetransaction = array();
					if($totaltimetransactionfirstentr !=""){
						$totaltimetransaction[] = $totaltimetransactionfirstentr;
					}
				?>
				<?php foreach($sla_transaction_tracking_result[$attable] as $tres111){  ?>
					<td>

				<?php 
					$sla_tracking_logs = $tres111['sla_tracking_logs'];
					//echo "<pre>"; print_r($sla_tracking_logs); echo "</pre>";
					$starttimeary = "";
					foreach($sla_tracking_logs as $oguid=>$slalg){	
						$userdateTimes  = array();
						if($starttimeary !=""){
							$userdateTimes[] = $starttimeary;
						}
						foreach($slalg as $subslalg){
							$userdateTimes[] = $subslalg['request_datetime'];
							$totaltimetransaction[] = $subslalg['request_datetime'];
							$totaltimetransactionary[] =  $subslalg['request_datetime'];
							$starttimeary= $subslalg['request_datetime'];
							$totaltimetransactionfirstentr= $subslalg['request_datetime'];
						}
						$usertime = totalHoursAndMinutesInDateTimeArray($userdateTimes);
					?>
					

						<div class="p-10 z-depth-top-0" >
		                    <h6><?php echo $_SESSION['users'][$oguid]; ?> <span class="f-10">(<?php echo $usertime['hours']." Hours : ".$usertime['minutes']." Minutes"; ?>)</span></h6> 
		                    <table>
		                    	<tr>
			                    <?php foreach($slalg as $subslalg){ ?>
			                    	<td class="text-left p-2">
			                    	<span class="d-block"><?php echo $subslalg['request_datetime']; ?></span>
			                    	<span class="d-block">
			                    		<?php echo $statuse[$subslalg['previous_status']]['name'];
			                    		if($subslalg['response_status'] !=""){	
			                    			echo " --> ".$statuse[$subslalg['response_status']]['name']; 
			                    		}
			                    	?></span>
			                    	</td>
			                	<?php } ?>
			                    </tr>
		                    </table>
		                </div>

						<?php } //print_r($tres111); ?>
							</td>
						<td> 
							<?php 
							$transactiontime = totalHoursAndMinutesInDateTimeArray($totaltimetransaction); 
							
						?>
							<span class="d-block">Total Time</span> 
							<span class="d-block"><?php echo $transactiontime['hours']." Hours : ".$transactiontime['minutes']." Minutes"; ?> </span>
						</td>
				
				
			<?php } } ?>
				<td> 
					<?php 
					$transactiontimeq = totalHoursAndMinutesInDateTimeArray($totaltimetransactionary); 
					
				?>
					<span class="d-block"><?php echo $transactiontimeq['hours']." Hours : ".$transactiontimeq['minutes']." Minutes"; ?> </span>
				</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php **/ ?>
<?php /** sinngle table data show  if(count((array)$transaction_result) != 0){ ?>
<table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
	<thead>
		<tr>
			<th rowspan="2">Transaction No.</th>
			<th rowspan="2">Originator</th>
			<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>
			<th colspan="<?php echo count((array)$usercoulumns)+1; ?>" class="text-center"><?php echo ucwords(str_replace('_',' ',$transinvoice)); ?></th>
			<?php } ?>
			<!-- <th rowspan="2">Total Time</th> -->
		</tr>
		<tr>
		<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>	
			<?php foreach($usercoulumns as $cuserid){ ?>
			<th><?php echo $_SESSION['users'][$cuserid]; ?></th>
			<?php } ?>
			<th>Total Time</th>
		<?php } ?>	
		</tr>
	</thead>
	<tbody>
		<?php foreach($transaction_result as $tres){
			$slatracklog = $sla_tracking_logs[$tres['id']];
		 ?>
		<tr>
			<td class="p-2"><?php echo $tres['transaction_number']." - ".$tres['transaction_date']; ?></td>
			<td class="p-2"><?php echo $_SESSION['users'][$tres['originator']]; ?></td>
			<?php foreach($salesinvoicetransactiontable as $transinvoice){ ?>
			<?php 
				$totaltimetransaction = array();
				foreach($usercoulumns as $uidd){ 
					
					$userdateTimes  = array();
					$userdateTimes[] = end($totaltimetransaction);
					$starttimeary ="";
					if($starttimeary !=""){
						$userdateTimes[] = $starttimeary;
					}
					foreach($slatracklog[$uidd] as $subslalg){
						$userdateTimes[] = $subslalg['request_datetime'];
						$totaltimetransaction[] = $subslalg['request_datetime']; 
						$starttimeary= $subslalg['request_datetime'];
					}
					$usertime = totalHoursAndMinutesInDateTimeArray($userdateTimes);
					?>
			<td class="p-2">
				<span class="f-10">(<?php echo $usertime['hours']." Hours : ".$usertime['minutes']." Minutes : ".$usertime['seconds']." seconds"; ?>)</span>
				<?php /** ?>
				 <div class="p-10 z-depth-top-0" >
                    <table>
                    	<tr>
	                    <?php foreach($slatracklog[$uidd] as $subslalg){ ?>
	                    	<td class="text-left p-2">
	                    	<span class="d-block"><?php echo $subslalg['request_datetime']; ?></span>
	                    	<span class="d-block">
	                    		<?php echo $statuse[$subslalg['previous_status']]['name'];
	                    		if($subslalg['response_status'] !=""){	
	                    			echo " --> ".$statuse[$subslalg['response_status']]['name']; 
	                    		}
	                    	?></span>
	                    	</td>
	                	<?php } ?>
	                    </tr>
                    </table>
                </div>  <?php **//** ?>
			</td>
			<?php }  ?>
			<td class="p-2">
				<?php 
					$transactiontime = totalHoursAndMinutesInDateTimeArray($totaltimetransaction); 
					echo $transactiontime['hours']." Hours : ".$transactiontime['minutes']." Minutes : ".$transactiontime['seconds']." Seconds"; 
				?>
			</td>
			<?php } ?>	

		</tr>
		<?php } ?>
	</tbody>
</table>
<?php }else{  ?>
	<div class="card-px text-center ">
		<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Transactions</p>   
		<div class="text-center px-4">
			<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		</div>   
	</div>

<?php } ?>
<?php sinngle table data show **/  ?>