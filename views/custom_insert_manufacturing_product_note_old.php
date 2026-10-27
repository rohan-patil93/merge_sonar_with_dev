<?php
$_SESSION['apply_js']['multiselest'] = 1;



// echo "<pre>"; print_r($_POST); echo "</pre>";
if (isset($_POST['submit']) && $_POST['submit'] == 'save') {
	$url = API . "?mode=insertmanufacturingproductnote&method=post";
	$response1 = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response1, true);
	header("location:index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_OLD");
}

$work_order_id = '';
if ($_GET['work_order_id']) {
    $rid = encodeId($_GET['work_order_id']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $work_order_id = $rid['1'];
}
$production_note_id = '';
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $production_note_id = $rid['1'];
}
// ===== FETCH LOGIC =====
$url = API . "?mode=getmanufacturingworkordersdetails&method=get&work_order_id=".$work_order_id."&production_note_id=".$production_note_id;
// echo $url;
$result = json_decode(callAPI($url), true);
// print_r($result);
$workorders = $result['data'] ?? array();
$data = $result ?? array();
$production_note_res = $result['production_note_res'] ?? array();
$product_note_items = $result['product_note_items'] ?? array();
?>

<div class="page-body">
	<div class="card shadow-sm border-0">
		<div class="card-header">
			<h5 class="mb-0">Manufacturing Product Note</h5>
		</div>

		<div class="card-body p-4">
			<?php if ($production_note_id == '' && empty($data['production_note_res'])) { ?>
			<form method="get" action="index.php" class="row mb-4">
				<input type="hidden" name="option" value="COM_INSERT_MANUFACTURING_PRODUCT_NOTE_OLD">
				<!-- Work Order Selection -->
				<div class="col-md-6 mb-3 w-full">
					<label class="fw-semibold">Work Order</label>
					<select name="work_order_id" class="js-example-basic-single" onchange="this.form.submit()" required>
						<option value="">Select Work Order</option>
						<?php
						if (count((array)$workorders) > 0) {
							foreach ($workorders as $row) {
								$selected = ($work_order_id == $row['id']) ? 'selected' : '';
								$rid = deocdeId("id:".$row['id']);
								echo "<option value='{$rid}'  {$selected}>{$row['transaction_no']} </option>";
							}
						} else {
							echo "<option value=''>No Work Orders Found</option>";
						}
						?>
					</select>
				</div>
			</form>
			<?php } ?>
			<?php if (($work_order_id != '' || $production_note_id != '') && !empty($data['work_order'])) { ?>
				<div class="accordion mt-4" id="workOrderAccordion">
					<!-- WORK ORDER DETAILS -->
					<div class="accordion-item mb-3 shadow-sm border-0">
						<h2 class="accordion-header" id="headingDetails">
							<button class="accordion-button collapsed  fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
								<i class="bi bi-gear-wide-connected me-2"></i> Work Order Details (<?php echo htmlspecialchars($data['work_order']['transaction_no']); ?>)
							</button>
						</h2>
						<div id="collapseDetails" class="accordion-collapse collapse " aria-labelledby="headingDetails" >
							<div class="accordion-body">
								<div class="row mb-3">
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Transaction No</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars($data['work_order']['transaction_no']); ?></span>
									</div>
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Item Name</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars($data['work_order']['item_name']); ?></span>
									</div>
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Planned Quantity</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars(customeummarmonyformate($data['work_order']['work_order_item_qty'])); ?></span>
									</div>
								</div>

								<div class="row mb-3">
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Completed Quantity</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars(customeummarmonyformate($data['work_order']['completed_qty'])); ?></span>
									</div>
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Planned Start Date</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($data['work_order']['planned_start_date'])); ?></span>
									</div>
									<div class="col-md-4">
										<label class="text-muted fw-semibold d-block">Planned End Date</label>
										<span class="fs-6 text-dark"><?php echo htmlspecialchars(fromDate($data['work_order']['planned_end_date'])); ?></span>
									</div>
								</div>
								
							</div>
						</div>
					</div>

					<!-- WORK ORDER ITEMS -->
					<div class="accordion-item shadow-sm border-0">
						<h2 class="accordion-header" id="headingItems">
							<button class="accordion-button collapsed fw-bold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems" aria-expanded="false" aria-controls="collapseItems">
								<i class="bi bi-box-seam me-2"></i> Work Order Items (<?php echo count($data['work_order_items']); ?>)
							</button>
						</h2>
						<div id="collapseItems" class="accordion-collapse collapse" aria-labelledby="headingItems" >
							<div class="accordion-body p-0">
								<div class="table-responsive">
									<table class="table table-hover align-middle mb-0">
										<thead class="table-light text-center">
											<tr>
												<th style="width: 50px;">#</th>
												<th class='text-start'>Item Name</th>
												<th>UOM</th>
						                        <th>Work Order Qty</th>
						                        <th>Stock Request Qty</th>
						                        <th>Stok Issued Qty</th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($data['work_order_items'])) { ?>
												<?php $i = 1;
												foreach ($data['work_order_items'] as $item) { ?>
													<tr class="text-center">
														<td><?php echo $i; ?></td>
														<td class="text-start fw-semibold text-dark"><?php echo $item['item_name']; ?></td>
														<td><?php echo $item['unit']; ?></td>
							                            <td><?php echo customeummarmonyformate($item['request_qty']); ?></td>
							                            <td><?php echo customeummarmonyformate($item['stock_request_qty']); ?></td>
							                            <td><?php echo customeummarmonyformate($item['stock_receipts_qty']); ?></td>
													</tr>
												<?php $i++;
												}
											} else { ?>
												<tr>
													<td colspan="6" class="text-center text-muted py-3">No items found.</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			<?php if ($production_note_id == '' && empty($data['production_note_res'])) { ?>
			<form method="POST" class="mt-4">
				<input type="hidden" name="work_order_id" value="<?php echo $work_order_id; ?>">

				<div class="card border-0 shadow-sm mb-4">
					<div class="card-header bg-light">
						<h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i> Manufacturing Product Note</h6>
					</div>
					<div class="card-body">
						<div class="row mb-3">
							<div class="col-md-3">
								<label class="fw-semibold">Item</label>
								<input type="hidden" name="item_id" value="<?php echo $data['work_order']['work_order_item_id']; ?>">
								<input type="text" class="form-control-plaintext fw-semibold" readonly value="<?php echo $data['work_order']['item_name']; ?>">
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Request Qty</label>
								<input type="number" name="request_qty" step="any" min="0" value="<?php echo ($data['work_order']['work_order_item_qty']); ?>" class="form-control text-end" required readonly>
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Produced Qty</label>
								<input type="number" name="produced_qty" step="any" min="0" value="<?php echo ($data['work_order']['completed_qty']); ?>" class="form-control text-end" required>
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Wastage Qty</label>
								<input type="number" name="wastage_qty" step="any" min="0" class="form-control text-end" value="0"  required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label class="fw-semibold">Remarks</label>
								<textarea name="remarks" rows="3" class="form-control" placeholder="Enter remarks..."></textarea>
							</div>
						</div>
					</div>
					<!-- </div> -->

					<!-- PRODUCT NOTE ITEMS LOOP -->
					<!-- <div class="card border-0 shadow-sm mb-4"> -->
					<div class="card-header bg-light">
						<h6 class="fw-bold mb-0 text-primary"><i class="bi bi-boxes me-2"></i> Product Note Items</h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-bordered align-middle mb-0">
								<thead class="table-light text-center">
									<tr>
										<th style="width:50px;">#</th>
										<th>Item Name</th>
										<th>Received Qty</th>
										<th>Used Qty</th>
										<th>Wastage Qty</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($data['work_order_items'])) {
										$i = 1; ?>
										<?php foreach ($data['work_order_items'] as $item) { ?>
											<tr>
												<td class="text-center"><?php echo $i; ?></td>
												<td>
													<input type="hidden" name="note_items[<?php echo $i; ?>][item_id]" value="<?php echo $item['item_id']; ?>">
													<input type="text" class="form-control-plaintext fw-semibold" readonly value="<?php echo $item['item_name']; ?>">
												</td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][received_qty]" step="any" min="0" class="form-control text-end" value="<?php echo ($item['request_qty']); ?>"></td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][produced_qty]" step="any" min="0" class="form-control text-end" value="<?php echo ($item['completed_qty']); ?>"></td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][wastage_qty]" step="any" min="0" value="0" class="form-control text-end"></td>
											</tr>
										<?php $i++;
										} ?>
									<?php } else { ?>
										<tr>
											<td colspan="5" class="text-center text-muted py-3">No items found.</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="text-start">
					<button type="submit" name="submit" value="save" class="btn btn-primary px-4">
						<i class="bi bi-save me-2"></i>Save
					</button>
				</div>
			</form>
			<?php } ?>
			<?php } ?>

			<?php if ($production_note_id != '' && !empty($production_note_res)) { ?>
			<form method="POST" class="mt-4">
				<input type="hidden" name="production_note_id" value="<?php echo $production_note_id; ?>">

				<div class="card border-0 shadow-sm mb-4">
					<div class="card-header bg-light">
						<h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i> Manufacturing Product Note (<?php echo $production_note_res['transaction_no']; ?>)</h6>
					</div>
					<div class="card-body">
						<div class="row mb-3">
							<div class="col-md-3">
								<label class="fw-semibold">Item</label>
								<input type="hidden" name="item_id" value="<?php echo $production_note_res['item_id']; ?>">
								<input type="text" class="form-control-plaintext fw-semibold" readonly value="<?php echo $production_note_res['item_name']; ?>">
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Request Qty</label>
								<input type="number" name="request_qty" step="any" min="0" value="<?php echo ($production_note_res['request_qty']); ?>" class="form-control text-end" required readonly>
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Produced Qty</label>
								<input type="number" name="produced_qty" step="any" min="0" value="<?php echo ($production_note_res['produced_qty']); ?>" class="form-control text-end" required>
							</div>
							<div class="col-md-3">
								<label class="fw-semibold">Wastage Qty</label>
								<input type="number" name="wastage_qty" step="any" min="0" class="form-control text-end" value="<?php echo ($production_note_res['wastage_qty']); ?>"  required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label class="fw-semibold">Remarks</label>
								<textarea name="remarks" rows="3" class="form-control" placeholder="Enter remarks..."><?php echo $production_note_res['remarks']; ?></textarea>
							</div>
						</div>
					</div>
					<!-- </div> -->

					<!-- PRODUCT NOTE ITEMS LOOP -->
					<!-- <div class="card border-0 shadow-sm mb-4"> -->
					<div class="card-header bg-light">
						<h6 class="fw-bold mb-0 text-primary"><i class="bi bi-boxes me-2"></i> Product Note Items</h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-bordered align-middle mb-0">
								<thead class="table-light text-center">
									<tr>
										<th style="width:50px;">#</th>
										<th>Item Name</th>
										<th>Received Qty</th>
										<th>Used Qty</th>
										<th>Wastage Qty</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($product_note_items)) {
										$i = 1; ?>
										<?php foreach ($product_note_items as $item) { ?>
											<tr>
												<input type="hidden" name="note_items[<?php echo $i; ?>][id]" value="<?php echo $item['id']; ?>">
												<td class="text-center"><?php echo $i; ?></td>
												<td>
													<input type="hidden" name="note_items[<?php echo $i; ?>][item_id]" value="<?php echo $item['item_id']; ?>">
													<input type="text" class="form-control-plaintext fw-semibold" readonly value="<?php echo $item['item_name']; ?>">
												</td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][received_qty]" step="any" min="0" class="form-control text-end" value="<?php echo ($item['received_qty']); ?>"></td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][produced_qty]" step="any" min="0" class="form-control text-end" value="<?php echo ($item['produced_qty']); ?>"></td>
												<td><input type="number" name="note_items[<?php echo $i; ?>][wastage_qty]" step="any" min="0" value="<?php echo ($item['wastage_qty']); ?>" class="form-control text-end"></td>
											</tr>
										<?php $i++;
										} ?>
									<?php } else { ?>
										<tr>
											<td colspan="5" class="text-center text-muted py-3">No items found.</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="text-start">
					<button type="submit" name="submit" value="save" class="btn btn-primary px-4">
						<i class="bi bi-save me-2"></i>Save
					</button>
				</div>
			</form>
			<?php } ?>
		</div>
	</div>
</div>


