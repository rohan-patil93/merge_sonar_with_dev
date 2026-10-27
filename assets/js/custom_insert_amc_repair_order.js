function amcRepairGetItems() {
    var el = document.getElementById('amcRepairItemsJson');
    if (!el) {
        return [];
    }
    try {
        return JSON.parse(el.textContent || '[]');
    } catch (e) {
        return [];
    }
}

function amcRepairGetAssets() {
    var el = document.getElementById('amcRepairAssetsJson');
    if (!el) {
        return [];
    }
    try {
        return JSON.parse(el.textContent || '[]');
    } catch (e) {
        return [];
    }
}

function amcRepairGetLocations() {
    var el = document.getElementById('amcRepairLocationsJson');
    if (!el) {
        return [];
    }
    try {
        return JSON.parse(el.textContent || '[]');
    } catch (e) {
        return [];
    }
}

function amcRepairSetAssetsJson(assets) {
    var el = document.getElementById('amcRepairAssetsJson');
    if (el) {
        el.textContent = JSON.stringify(assets || []);
    }
}

function amcRepairSetLocationsJson(locations) {
    var el = document.getElementById('amcRepairLocationsJson');
    if (el) {
        el.textContent = JSON.stringify(locations || []);
    }
}

function amcRepairGetHeaderLocationId() {
    var $loc = jQuery('#amc_repair_location_id');
    if ($loc.length) {
        return parseInt($loc.val(), 10) || 0;
    }
    var $hidden = jQuery('#amcRepairOrderForm input[name="location_id"]');
    if ($hidden.length) {
        return parseInt($hidden.val(), 10) || 0;
    }
    return 0;
}

function amcRepairGetContractId() {
    var $sel = jQuery('#amc_repair_contract_id');
    if ($sel.length) {
        return $sel.val() || '';
    }
    return jQuery('#amcRepairOrderForm input[name="contract_id"]').val() || '';
}

function amcRepairDestroySelect2IfNeeded($sel) {
    if ($sel.length && $sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
}

function amcRepairInitSelect2On($sel) {
    if (typeof jQuery.fn.select2 != 'function' || !$sel.length || $sel.prop('disabled')) {
        return;
    }
    $sel.select2({ width: '100%' });
}

function amcRepairBuildAssetOptionsForLocation(locationId, selectedId, assetsOverride) {
    selectedId = parseInt(selectedId, 10) || 0;
    if (!locationId) {
        return '<option value="">Select location first</option>';
    }
    var html = '<option value="">Select</option>';
    var assets = assetsOverride || amcRepairGetAssets();
    var count = 0;
    for (var i = 0; i < assets.length; i++) {
        var a = assets[i];
        var locId = parseInt(a.location_id, 10) || 0;
        if (locId != locationId) {
            continue;
        }
        count++;
        var id = parseInt(a.asset_id, 10) || 0;
        var label = ((a.asset_code || '') + ' ' + (a.asset_description || a.asset_name || '')).trim();
        html += '<option value="' + id + '"' + (id == selectedId ? ' selected' : '') + '>' + jQuery('<div>').text(label).html() + '</option>';
    }
    if (count == 0) {
        return '<option value="">No assets at this location</option>';
    }
    return html;
}

function amcRepairBuildItemOptions(selectedId, itemsOverride) {
    selectedId = parseInt(selectedId, 10) || 0;
    var html = '<option value="">Select</option>';
    var items = itemsOverride || amcRepairGetItems();
    for (var i = 0; i < items.length; i++) {
        var it = items[i];
        var id = parseInt(it.id, 10) || 0;
        if (id <= 0) {
            continue;
        }
        var label = ((it.sku || '') + ' ' + (it.item_name || '')).trim();
        if (!label) {
            label = it.item_description || ('Item ' + id);
        }
        html += '<option value="' + id + '"' + (id == selectedId ? ' selected' : '') + '>' + jQuery('<div>').text(label).html() + '</option>';
    }
    return html;
}

function amcRepairBuildLocationOptions(selectedId, locationsOverride) {
    selectedId = parseInt(selectedId, 10) || 0;
    var html = '<option value="">Select</option>';
    var locs = locationsOverride || amcRepairGetLocations();
    for (var i = 0; i < locs.length; i++) {
        var l = locs[i];
        var id = parseInt(l.location_id, 10) || 0;
        if (id <= 0) {
            continue;
        }
        var label = l.location_label || ('Location ' + id);
        html += '<option value="' + id + '"' + (id == selectedId ? ' selected' : '') + '>' + jQuery('<div>').text(label).html() + '</option>';
    }
    return html;
}

function amcRepairSyncContractAmcLocationId(locationId) {
    var locs = amcRepairGetLocations();
    var calId = 0;
    for (var i = 0; i < locs.length; i++) {
        if (parseInt(locs[i].location_id, 10) == locationId) {
            calId = parseInt(locs[i].contract_amc_location_id, 10) || 0;
            break;
        }
    }
    jQuery('#amcRepairOrderForm input[name="contract_amc_location_id"]').val(calId);
}

function amcRepairRefreshDefaultAssetDropdown(locationId, selectedAssetId, assetsOverride) {
    var $asset = jQuery('#amc_repair_default_asset_id');
    if (!$asset.length) {
        return;
    }
    amcRepairDestroySelect2IfNeeded($asset);
    $asset.html(amcRepairBuildAssetOptionsForLocation(locationId, selectedAssetId || 0, assetsOverride));
    amcRepairInitSelect2On($asset);
}

function amcRepairRefreshHeaderDropdowns(selectedLocationId, selectedAssetId, locationsOverride, assetsOverride) {
    var $loc = jQuery('#amc_repair_location_id');
    if (!$loc.length) {
        return;
    }
    amcRepairDestroySelect2IfNeeded($loc);
    amcRepairDestroySelect2IfNeeded(jQuery('#amc_repair_default_asset_id'));
    $loc.html(amcRepairBuildLocationOptions(selectedLocationId || 0, locationsOverride));
    amcRepairInitSelect2On($loc);
    amcRepairRefreshDefaultAssetDropdown(selectedLocationId || 0, selectedAssetId || 0, assetsOverride);
    if (selectedLocationId > 0) {
        amcRepairSyncContractAmcLocationId(selectedLocationId);
    }
}

function amcRepairInitRowSelect2($row) {
    $row.find('.js-example-basic-single').each(function () {
        var $sel = jQuery(this);
        amcRepairDestroySelect2IfNeeded($sel);
        amcRepairInitSelect2On($sel);
    });
}

function amcRepairReindexLines($tbody) {
    $tbody.find('.amc-repair-line-row, .contract-amc-repair-line-row').each(function (idx) {
        jQuery(this).find('[name^="repair_lines"]').each(function () {
            var n = jQuery(this).attr('name');
            if (n) {
                jQuery(this).attr('name', n.replace(/repair_lines\[\d+\]/, 'repair_lines[' + idx + ']'));
            }
        });
    });
}

function amcRepairApplyDefaultPoToEmptyLines() {
    var defPo = (jQuery('#repair_default_po, #amc_repair_default_po').val() || '').trim();
    if (!defPo) {
        return;
    }
    jQuery('.repair-line-po').each(function () {
        if ((jQuery(this).val() || '').trim() === '') {
            jQuery(this).val(defPo);
        }
    });
}

function amcRepairAddLineRow($tbody, data) {
    data = data || {};
    var idx = $tbody.find('tr').length;
    var row = jQuery('<tr class="amc-repair-line-row contract-amc-repair-line-row"></tr>');
    row.append(
        '<td><input type="hidden" name="repair_lines[' + idx + '][line_id]" value="' + (parseInt(data.line_id, 10) || 0) + '">' +
        '<select class="form-select form-select-sm js-example-basic-single repair-line-item" name="repair_lines[' + idx + '][item_id]">' + amcRepairBuildItemOptions(parseInt(data.item_id, 10) || 0) + '</select></td>' +
        '<td><input type="text" class="form-control form-control-sm" name="repair_lines[' + idx + '][part_description]" value="' + jQuery('<div>').text(data.part_description || '').html() + '"></td>' +
        '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="repair_lines[' + idx + '][qty]" value="' + (data.qty || '1') + '"></td>' +
        '<td><input type="text" class="form-control form-control-sm repair-line-po" name="repair_lines[' + idx + '][po_number]" value="' + jQuery('<div>').text(data.po_number || '').html() + '"></td>' +
        '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="repair_lines[' + idx + '][amount]" value="' + (data.amount || '0') + '"></td>' +
        '<td class="text-center"><button type="button" class="btn btn-link text-danger btn-sm contract-amc-repair-remove-line p-0"><i class="ti-trash"></i></button></td>'
    );
    $tbody.append(row);
    amcRepairApplyDefaultPoToEmptyLines();
    amcRepairInitRowSelect2(row);
}

function amcRepairParseContractAssetsResponse(resp) {
    var r = resp;
    if (typeof resp === 'string') {
        try {
            r = JSON.parse(resp);
        } catch (e) {
            return { assets: [], locations: [] };
        }
    }
    if (r && r.assets) {
        return { assets: r.assets || [], locations: r.locations || [] };
    }
    if (r && r.data && (r.data.assets || r.data.locations)) {
        return { assets: r.data.assets || [], locations: r.data.locations || [] };
    }
    return { assets: [], locations: [] };
}

function amcRepairLoadContractAssets(contractId, callback) {
    if (!contractId) {
        amcRepairSetAssetsJson([]);
        amcRepairSetLocationsJson([]);
        if (callback) {
            callback({ assets: [], locations: [] });
        }
        return;
    }
    jQuery.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: { page: 'getamcrepairordercontractassets', contract_id: contractId },
        success: function (resp) {
            var parsed = amcRepairParseContractAssetsResponse(resp);
            amcRepairSetAssetsJson(parsed.assets);
            amcRepairSetLocationsJson(parsed.locations);
            if (callback) {
                callback(parsed);
            }
        },
        error: function () {
            amcRepairSetAssetsJson([]);
            amcRepairSetLocationsJson([]);
            if (typeof tostarnotify === 'function') {
                tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Could not load contract locations. Please try again.', '');
            }
            if (callback) {
                callback({ assets: [], locations: [] });
            }
        }
    });
}

function amcRepairSyncVendorFromContract($select) {
    var $opt = $select.find('option:selected');
    var vendorId = $opt.attr('data-vendor-id') || $opt.data('vendor-id') || '';
    var vendorName = $opt.attr('data-vendor-name') || $opt.data('vendor-name') || '';
    jQuery('#amc_repair_vendor_id').val(vendorId);
    jQuery('#amc_repair_vendor_name').val(vendorName);
}

function amcRepairOnContractChanged(contractId) {
    amcRepairLoadContractAssets(contractId, function (parsed) {
        amcRepairRefreshHeaderDropdowns(0, 0, parsed.locations, parsed.assets);
    });
}

function amcRepairBootstrapDropdowns() {
    var cid = amcRepairGetContractId();
    if (!cid) {
        return;
    }
    var locId = amcRepairGetHeaderLocationId();
    var assetId = 0;
    var $defaultAsset = jQuery('#amc_repair_default_asset_id');
    if ($defaultAsset.length) {
        assetId = parseInt($defaultAsset.val(), 10) || 0;
    }
    amcRepairLoadContractAssets(cid, function (parsed) {
        if (jQuery('#amc_repair_location_id').length) {
            amcRepairRefreshHeaderDropdowns(locId, assetId, parsed.locations, parsed.assets);
        } else if (locId > 0) {
            amcRepairSyncContractAmcLocationId(locId);
        }
    });
}

function amcRepairCollectFormData($form) {
    var data = {};
    $form.serializeArray().forEach(function (item) {
        var m = item.name.match(/^repair_lines\[(\d+)\]\[(\w+)\]$/);
        if (m) {
            if (!data.repair_lines) {
                data.repair_lines = [];
            }
            var i = parseInt(m[1], 10);
            if (!data.repair_lines[i]) {
                data.repair_lines[i] = {};
            }
            data.repair_lines[i][m[2]] = item.value;
            return;
        }
        data[item.name] = item.value;
    });
    if (data.repair_lines) {
        data.repair_lines = data.repair_lines.filter(function (x) { return x; });
    }
    data.page = 'saveamcrepairorder';
    return data;
}

function amcRepairSaveOrder($form, onSuccess) {
    var payload = amcRepairCollectFormData($form);
    jQuery.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: payload,
        success: function (resp) {
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (r.status == 'success') {
                if (r.repair_order_id) {
                    $form.find('[name="repair_order_id"]').val(r.repair_order_id);
                }
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', r.message || 'Saved', '');
                if (onSuccess) {
                    onSuccess(r);
                }
            } else {
                tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || 'Save failed', '');
            }
        }
    });
}

window.addEventListener('load', function () {
    var $form = jQuery('#amcRepairOrderForm');
    if (!$form.length) {
        return;
    }

    amcRepairBootstrapDropdowns();

    jQuery(document).off('change select2:select', '#amc_repair_contract_id').on('change select2:select', '#amc_repair_contract_id', function () {
        var $contract = jQuery('#amc_repair_contract_id');
        amcRepairSyncVendorFromContract($contract);
        amcRepairOnContractChanged($contract.val());
    });

    jQuery(document).off('change select2:select', '#amc_repair_location_id').on('change select2:select', '#amc_repair_location_id', function () {
        var $location = jQuery('#amc_repair_location_id');
        var locationId = parseInt($location.val(), 10) || 0;
        amcRepairSyncContractAmcLocationId(locationId);
        amcRepairRefreshDefaultAssetDropdown(locationId, 0);
    });

    jQuery(document).off('click', '#amcRepairAddLine').on('click', '#amcRepairAddLine', function () {
        amcRepairAddLineRow(jQuery('#amcRepairLinesBody'), {});
    });

    jQuery(document).off('click', '.contract-amc-repair-remove-line').on('click', '.contract-amc-repair-remove-line', function () {
        var $tbody = jQuery(this).closest('tbody');
        jQuery(this).closest('tr').remove();
        if ($tbody.find('tr').length === 0) {
            amcRepairAddLineRow($tbody, {});
        }
        amcRepairReindexLines($tbody);
    });

    jQuery(document).off('blur', '#repair_default_po, #amc_repair_default_po').on('blur', '#repair_default_po, #amc_repair_default_po', amcRepairApplyDefaultPoToEmptyLines);

    if ($form.find('button[type="submit"]').length) {
        jQuery(document).off('submit', '#amcRepairOrderForm').on('submit', '#amcRepairOrderForm', function (e) {
            e.preventDefault();
            amcRepairSaveOrder($form, function () {
                setTimeout(function () {
                    window.location.href = 'index.php?option=COM_LIST_AMC_REPAIR_ORDERS';
                }, 600);
            });
        });
    }
});
