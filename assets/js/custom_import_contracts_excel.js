(function ($) {
	var cexState = {
		batchId: 0,
		file: null,
		preview: null,
		importSummary: null
	};

	function parseAjaxJson(raw) {
		if (raw == null) {
			return null;
		}
		if (typeof raw == 'object') {
			return raw;
		}
		var text = String(raw).replace(/^\uFEFF/, '').trim();
		if (text == '') {
			return null;
		}
		try {
			return JSON.parse(text);
		} catch (e) {
			var start = text.indexOf('{');
			var end = text.lastIndexOf('}');
			if (start >= 0 && end > start) {
				try {
					return JSON.parse(text.substring(start, end + 1));
				} catch (e2) {
					return null;
				}
			}
			return null;
		}
	}

	function notify(msg, type) {
		type = type || 'danger';
		if (typeof tostarnotify == 'function') {
			var icon = type == 'success' ? 'fa fa-thumbs-up' : 'fa fa-warning';
			var cls = type == 'success' ? 'form-label label label-success' : 'form-label label label-danger';
			tostarnotify('top', 'right', icon, cls, 'animated flipInX', 'animated flipOutX', msg, '');
			return;
		}
		alert(msg);
	}

	function setStep(stepIndex) {
		$('.cc-wizard-step').removeClass('active completed');
		$('.cc-wizard-step').each(function () {
			var s = parseInt($(this).attr('data-cex-step'), 10);
			if (s < stepIndex) {
				$(this).addClass('completed');
			}
			if (s == stepIndex) {
				$(this).addClass('active');
			}
		});
		$('.cex-step-panel').addClass('d-none');
		$('.cex-step-panel[data-cex-step="' + stepIndex + '"]').removeClass('d-none');
	}

	function downloadTemplate() {
		var $btns = $('#cex-download-template-btn, #cex-download-template-btn-2');
		$btns.prop('disabled', true);
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			dataType: 'text',
			data: { page: 'download_contracts_excel_template' },
			success: function (raw) {
				var res = parseAjaxJson(raw);
				if (!res || res.status != 'success' || !res.download_url) {
					notify((res && res.message) ? res.message : 'Failed to generate template.');
					return;
				}
				window.location.href = res.download_url;
				setStep(1);
			},
			error: function () {
				notify('Failed to generate template.');
			},
			complete: function () {
				$btns.prop('disabled', false);
			}
		});
	}

	function renderStats($target, items) {
		var html = '';
		$.each(items, function (i, item) {
			html += '<div class="col-md-3"><div class="cex-stat-box"><div class="cex-stat-value">' +
				$('<div>').text(item.value).html() + '</div><div class="cex-stat-label">' +
				$('<div>').text(item.label).html() + '</div></div></div>';
		});
		$target.html(html);
	}

	function fillTable($table, rows, columns) {
		var $thead = $table.find('thead').empty();
		var $tbody = $table.find('tbody').empty();
		if (!rows || !rows.length) {
			$tbody.append('<tr><td colspan="' + (columns.length || 1) + '" class="text-muted">No rows</td></tr>');
			return;
		}
		var headHtml = '<tr>';
		$.each(columns, function (i, c) {
			headHtml += '<th>' + $('<div>').text(c).html() + '</th>';
		});
		headHtml += '</tr>';
		$thead.html(headHtml);
		$.each(rows, function (i, row) {
			var cls = '';
			var okVal = row.ok;
			if (okVal == false || okVal == 0 || okVal == 'Error') {
				cls = 'cex-row-error';
			} else if (okVal == true || okVal == 1 || okVal == 'OK') {
				cls = 'cex-row-ok';
			}
			var tr = '<tr class="' + cls + '">';
			$.each(columns, function (j, c) {
				var val = row[c];
				if (val == null) {
					val = '';
				}
				if ($.isArray(val)) {
					val = val.join('; ');
				}
				tr += '<td>' + $('<div>').text(String(val)).html() + '</td>';
			});
			tr += '</tr>';
			$tbody.append(tr);
		});
	}

	function flattenPreviewRows(rows, extraKeys) {
		var out = [];
		extraKeys = extraKeys || [];
		if (!rows || !rows.length) {
			return out;
		}
		$.each(rows, function (i, item) {
			var base = $.extend({}, item.row || {});
			base.import_ref = item.import_ref || base.import_ref || '';
			base.ok = item.ok ? 'OK' : 'Error';
			base.error = (item.errors && item.errors.length) ? item.errors.join('; ') : '';
			$.each(extraKeys, function (j, k) {
				if (item.resolved && item.resolved[k] != null && (base[k] == null || base[k] == '')) {
					base[k] = item.resolved[k];
				}
			});
			out.push(base);
		});
		return out;
	}

	function renderPreview(res) {
		cexState.preview = res;
		cexState.batchId = parseInt(res.batch_id || 0, 10);
		var contracts = flattenPreviewRows(res.contracts || [], ['contract_type', 'payment_plan', 'billing_cycle']);
		var errors = res.errors || [];
		var warnings = res.warnings || [];
		var validCount = (res.valid_import_refs || []).length;
		var errorCount = errors.length;

		renderStats($('#cex-preview-stats'), [
			{ value: contracts.length, label: 'Contracts in file' },
			{ value: validCount, label: 'Valid' },
			{ value: errorCount, label: 'Errors' },
			{ value: 'Batch #' + cexState.batchId, label: 'Audit saved' }
		]);

		var alerts = '';
		if (errorCount > 0) {
			alerts += '<div class="alert alert-warning">Fix errors before import, or confirm to import valid rows only (best-effort).</div>';
		}
		if (warnings.length > 0) {
			alerts += '<div class="alert alert-info"><strong>Warnings:</strong><ul class="mb-0">';
			$.each(warnings, function (i, w) {
				alerts += '<li>' + $('<div>').text(w).html() + '</li>';
			});
			alerts += '</ul></div>';
		}
		$('#cex-preview-alerts').html(alerts);

		fillTable($('#cex-table-contracts'), contracts, [
			'import_ref', 'contract_type', 'contract_name', 'party_type', 'department_name',
			'payment_plan', 'billing_cycle', 'contract_amount', 'ok', 'error'
		]);
		fillTable($('#cex-table-locations'), flattenPreviewRows(res.locations || []), [
			'import_ref', 'location_name', 'service_start_date', 'service_end_date', 'ok', 'error'
		]);
		fillTable($('#cex-table-assets'), flattenPreviewRows(res.assets || []), [
			'import_ref', 'location_name', 'asset_code', 'service_frequency', 'visits_per_interval', 'ok', 'error'
		]);
		fillTable($('#cex-table-milestones'), flattenPreviewRows(res.milestones || []), [
			'import_ref', 'milestone_name', 'milestone_date', 'milestone_amount', 'ok', 'error'
		]);

		var $errBody = $('#cex-table-errors tbody').empty();
		if (errors.length > 0) {
			$.each(errors, function (i, e) {
				$errBody.append('<tr class="cex-row-error"><td>' + $('<div>').text(e).html() + '</td></tr>');
			});
		} else {
			$errBody.append('<tr><td class="text-muted">No errors</td></tr>');
		}

		$('#cex-import-btn').prop('disabled', validCount == 0);
		setStep(2);
	}

	function validateUpload() {
		if (!cexState.file) {
			notify('Please choose an Excel file first.');
			return;
		}
		var fd = new FormData();
		fd.append('page', 'validate_contracts_excel');
		fd.append('excel_file', cexState.file);
		fd.append('file_name', cexState.file.name);
		$('#cex-validate-btn').prop('disabled', true).text('Validating...');
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			dataType: 'text',
			success: function (raw) {
				var res = parseAjaxJson(raw);
				if (!res || (res.status == 'error' && !res.batch_id)) {
					notify((res && res.message) ? res.message : 'Validation failed.');
					return;
				}
				renderPreview(res);
			},
			error: function () {
				notify('Validation request failed.');
			},
			complete: function () {
				$('#cex-validate-btn').prop('disabled', !cexState.file).text('Validate & Preview');
			}
		});
	}

	function confirmImport() {
		if (!cexState.batchId) {
			notify('No validated batch to import.');
			return;
		}
		$('#cex-import-btn').prop('disabled', true).text('Importing...');
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			dataType: 'text',
			data: { page: 'import_contracts_excel', batch_id: cexState.batchId },
			success: function (raw) {
				var res = parseAjaxJson(raw);
				if (!res || res.status == 'error') {
					notify((res && res.message) ? res.message : 'Import failed.');
					$('#cex-import-btn').prop('disabled', false).text('Confirm Import');
					return;
				}
				cexState.importSummary = res.summary || {};
				var items = (res.summary && res.summary.items) ? res.summary.items : [];
				renderStats($('#cex-result-stats'), [
					{ value: (res.summary && res.summary.success) ? res.summary.success : 0, label: 'Imported' },
					{ value: (res.summary && res.summary.failed) ? res.summary.failed : 0, label: 'Failed' },
					{ value: 'Batch #' + cexState.batchId, label: 'Audit id' }
				]);
				var $tbody = $('#cex-table-result tbody').empty();
				if (items.length > 0) {
					$.each(items, function (i, item) {
						var cls = item.status == 'success' ? 'cex-row-ok' : 'cex-row-error';
						$tbody.append(
							'<tr class="' + cls + '"><td>' + $('<div>').text(item.import_ref || '').html() +
							'</td><td>' + $('<div>').text(item.status || '').html() +
							'</td><td>' + $('<div>').text(item.contract_id || '—').html() +
							'</td><td>' + $('<div>').text(item.message || '').html() + '</td></tr>'
						);
					});
				} else {
					$tbody.append('<tr><td colspan="4" class="text-muted">No import items</td></tr>');
				}
				setStep(3);
				notify('Import finished: ' + (res.status || ''), 'success');
			},
			error: function () {
				notify('Import request failed.');
				$('#cex-import-btn').prop('disabled', false).text('Confirm Import');
			}
		});
	}

	function viewAudit() {
		if (!cexState.batchId) {
			return;
		}
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			dataType: 'text',
			data: { page: 'get_contracts_excel_batch', batch_id: cexState.batchId },
			success: function (raw) {
				var res = parseAjaxJson(raw);
				if (!res || res.status != 'success' || !res.batch) {
					notify((res && res.message) ? res.message : 'Unable to load audit batch.');
					return;
				}
				var b = res.batch;
				var html = '<p><strong>File:</strong> ' + $('<div>').text(b.file_name || '').html() +
					' &nbsp; <strong>Status:</strong> ' + $('<div>').text(b.status || '').html() +
					' &nbsp; <strong>Uploaded:</strong> ' + $('<div>').text(b.uploaded_at || '').html() + '</p>';
				html += '<p class="mb-1"><strong>Raw sheet snapshots (4 columns)</strong></p>';
				html += '<ul>';
				html += '<li>contracts_data length: ' + String((b.contracts_data || '').length) + '</li>';
				html += '<li>locations_data length: ' + String((b.locations_data || '').length) + '</li>';
				html += '<li>assets_data length: ' + String((b.assets_data || '').length) + '</li>';
				html += '<li>milestones_data length: ' + String((b.milestones_data || '').length) + '</li>';
				html += '</ul>';
				if (b.import_summary_decoded) {
					html += '<pre class="bg-light p-2 border rounded" style="max-height:240px;overflow:auto;">' +
						$('<div>').text(JSON.stringify(b.import_summary_decoded, null, 2)).html() + '</pre>';
				}
				$('#cex-audit-content').html(html);
				$('#cex-audit-panel').removeClass('d-none');
			},
			error: function () {
				notify('Failed to load audit batch.');
			}
		});
	}

	$(document).ready(function () {
		if (!$('#cex-excel-file').length) {
			return;
		}

		$('#cex-download-template-btn, #cex-download-template-btn-2').on('click', function (e) {
			e.preventDefault();
			downloadTemplate();
		});

		$('#cex-upload-zone').on('click', function () {
			$('#cex-excel-file').trigger('click');
		});

		$('#cex-excel-file').on('change', function () {
			var file = this.files && this.files[0] ? this.files[0] : null;
			cexState.file = file;
			if (file) {
				$('#cex-upload-filename').text(file.name);
				$('#cex-validate-btn').prop('disabled', false);
			} else {
				$('#cex-upload-filename').text('');
				$('#cex-validate-btn').prop('disabled', true);
			}
		});

		$('#cex-validate-btn').on('click', function () {
			validateUpload();
		});

		$('#cex-reupload-btn').on('click', function () {
			cexState.file = null;
			cexState.preview = null;
			$('#cex-excel-file').val('');
			$('#cex-upload-filename').text('');
			$('#cex-validate-btn').prop('disabled', true);
			setStep(1);
		});

		$('#cex-import-btn').on('click', function () {
			confirmImport();
		});

		$('#cex-view-audit-btn').on('click', function () {
			viewAudit();
		});

		$('.cc-wizard-step').on('click', function (e) {
			e.preventDefault();
			var step = parseInt($(this).attr('data-cex-step'), 10);
			if (step == 2 && !cexState.preview) {
				return;
			}
			if (step == 3 && !cexState.importSummary) {
				return;
			}
			setStep(step);
		});
	});
})(jQuery);
