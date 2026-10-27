<?php
$_SESSION['apply_js']['multiselest'] = 1;
?>
<link rel="stylesheet" href="assets/css/custom_insert_create_contract.css?v=<?= @filemtime(__DIR__ . '/../assets/css/custom_insert_create_contract.css') ?: time() ?>">
<link rel="stylesheet" href="assets/css/custom_import_contracts_excel.css?v=<?= @filemtime(__DIR__ . '/../assets/css/custom_import_contracts_excel.css') ?: time() ?>">

<div class="card unified-contract-create-card">
	<div class="card-header">
		<h5>Contract Excel Upload</h5>
		<div class="card-header-right table-card-header">
			<a href="index.php?option=COM_LIST_CONTRACTS" class="btn waves-effect waves-light mb-2 desktop-view">
				<i class="icofont icofont-reply"></i> Back to List
			</a>
		</div>
	</div>
	<div class="card-block">
		<div id="unified-contract-page" class="cex-import-page">
			<div class="page-body cc-wizard-page">
				<div class="cc-wizard-header">
					<div class="d-flex align-items-center gap-3">
						<h5 class="mb-0">Import Contracts from Excel</h5>
					</div>
					<div class="cc-wizard-header-actions">
						<button type="button" class="btn btn-primary btn-sm" id="cex-download-template-btn">
							<i class="fa fa-download"></i> Download Template
						</button>
					</div>
				</div>

				<div class="cc-wizard-layout">
					<aside class="cc-wizard-sidebar">
						<div class="cc-wizard-steps-list">
							<a href="#" class="cc-wizard-step active" data-cex-step="0">
								<span class="cc-step-number">1</span>
								<div class="cc-step-info">
									<div class="cc-step-title">Download Template</div>
									<div class="cc-step-desc">4 data sheets + Ref sheets</div>
								</div>
							</a>
							<a href="#" class="cc-wizard-step" data-cex-step="1">
								<span class="cc-step-number">2</span>
								<div class="cc-step-info">
									<div class="cc-step-title">Upload File</div>
									<div class="cc-step-desc">Upload filled .xlsx</div>
								</div>
							</a>
							<a href="#" class="cc-wizard-step" data-cex-step="2">
								<span class="cc-step-number">3</span>
								<div class="cc-step-info">
									<div class="cc-step-title">Preview &amp; Validate</div>
									<div class="cc-step-desc">Review rows and errors</div>
								</div>
							</a>
							<a href="#" class="cc-wizard-step" data-cex-step="3">
								<span class="cc-step-number">4</span>
								<div class="cc-step-info">
									<div class="cc-step-title">Import Result</div>
									<div class="cc-step-desc">Success / failed per ref</div>
								</div>
							</a>
						</div>
						<div class="cc-sidebar-box help-box">
							<h6><i class="icofont icofont-question-circle"></i> Need Help?</h6>
							<p class="mb-0">Copy values from Ref_* sheets. Use billing_cycle for auto schedule, or milestones sheet for custom amounts. AMC needs Locations + Assets.</p>
						</div>
					</aside>

					<div class="cc-wizard-main">
						<div class="cc-section cex-step-panel" id="cex-step-1" data-cex-step="0">
							<div class="cc-section-header">
								<h6><span class="cc-section-num">1</span><i class="icofont icofont-file-excel"></i> Download Template</h6>
							</div>
							<div class="alert alert-info mb-3">
								Download the workbook with <strong>Contracts</strong>, <strong>Locations</strong>, <strong>Assets</strong>, <strong>Milestones</strong>, and live <strong>Ref_*</strong> sheets. Fill data sheets only; copy dropdown values from Ref sheets.
							</div>
							<button type="button" class="btn btn-primary" id="cex-download-template-btn-2">
								<i class="fa fa-download"></i> Download Template
							</button>
						</div>

						<div class="cc-section cex-step-panel d-none" id="cex-step-2" data-cex-step="1">
							<div class="cc-section-header">
								<h6><span class="cc-section-num">2</span><i class="icofont icofont-upload-alt"></i> Upload File</h6>
							</div>
							<div class="cex-upload-zone" id="cex-upload-zone">
								<i class="icofont icofont-cloud-upload"></i>
								<p class="mb-1">Click to upload or drag and drop</p>
								<small class="text-muted">Accepted: .xlsx only</small>
								<div class="cex-upload-filename text-primary mt-2" id="cex-upload-filename"></div>
							</div>
							<input type="file" id="cex-excel-file" class="d-none" accept=".xlsx">
							<div class="mt-3">
								<button type="button" class="btn btn-primary" id="cex-validate-btn" disabled>
									Validate &amp; Preview
								</button>
							</div>
						</div>

						<div class="cc-section cex-step-panel d-none" id="cex-step-3" data-cex-step="2">
							<div class="cc-section-header">
								<h6><span class="cc-section-num">3</span><i class="icofont icofont-eye"></i> Preview &amp; Validate</h6>
							</div>
							<div class="row g-2 mb-3" id="cex-preview-stats"></div>
							<div id="cex-preview-alerts"></div>
							<ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
								<li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#cex-tab-contracts">Contracts</a></li>
								<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-locations">Locations</a></li>
								<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-assets">Assets</a></li>
								<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-milestones">Milestones</a></li>
								<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#cex-tab-errors">Errors</a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="cex-tab-contracts">
									<div class="table-responsive"><table class="table table-sm table-bordered" id="cex-table-contracts"><thead></thead><tbody></tbody></table></div>
								</div>
								<div class="tab-pane fade" id="cex-tab-locations">
									<div class="table-responsive"><table class="table table-sm table-bordered" id="cex-table-locations"><thead></thead><tbody></tbody></table></div>
								</div>
								<div class="tab-pane fade" id="cex-tab-assets">
									<div class="table-responsive"><table class="table table-sm table-bordered" id="cex-table-assets"><thead></thead><tbody></tbody></table></div>
								</div>
								<div class="tab-pane fade" id="cex-tab-milestones">
									<div class="table-responsive"><table class="table table-sm table-bordered" id="cex-table-milestones"><thead></thead><tbody></tbody></table></div>
								</div>
								<div class="tab-pane fade" id="cex-tab-errors">
									<div class="table-responsive"><table class="table table-sm table-bordered" id="cex-table-errors"><thead><tr><th>Message</th></tr></thead><tbody></tbody></table></div>
								</div>
							</div>
							<div class="cc-wizard-footer d-flex gap-2 mt-3">
								<button type="button" class="btn btn-secondary" id="cex-reupload-btn">Re-upload</button>
								<button type="button" class="btn btn-primary" id="cex-import-btn" disabled>
									Confirm Import
								</button>
							</div>
						</div>

						<div class="cc-section cex-step-panel d-none" id="cex-step-4" data-cex-step="3">
							<div class="cc-section-header">
								<h6><span class="cc-section-num">4</span><i class="icofont icofont-check-circled"></i> Import Result</h6>
							</div>
							<div class="row g-2 mb-3" id="cex-result-stats"></div>
							<div class="table-responsive">
								<table class="table table-sm table-bordered" id="cex-table-result">
									<thead>
										<tr>
											<th>import_ref</th>
											<th>Status</th>
											<th>Contract ID</th>
											<th>Message</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="cc-wizard-footer d-flex gap-2 mt-3">
								<a href="index.php?option=COM_LIST_CONTRACTS" class="btn btn-primary">Go to Contracts List</a>
								<button type="button" class="btn btn-outline-secondary" id="cex-view-audit-btn">View Audit Batch</button>
							</div>
							<div class="mt-3 d-none" id="cex-audit-panel">
								<div class="cc-section-header">
									<h6><i class="icofont icofont-history"></i> Audit Batch (source of truth)</h6>
								</div>
								<div id="cex-audit-content" class="small"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="assets/js/custom_import_contracts_excel.js?v=<?= @filemtime(__DIR__ . '/../assets/js/custom_import_contracts_excel.js') ?: time() ?>"></script>
