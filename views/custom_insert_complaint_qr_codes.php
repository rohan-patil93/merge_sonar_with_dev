<?php
$_SESSION['apply_js']['multiselest'] = '1';

$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getqrgenerationdetails&method=get&id=" . $rid;
$response = callAPI($url);
$data = json_decode($response, true) ?: [];
$editRow = $data['amc_qr_data'] ?? [];
$buildings = $data['buildings'] ?? [];
$locations = $data['locations'] ?? [];
$assets = $data['assets'] ?? [];
$items = $data['items'] ?? [];
?>

<div class="page-body">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ti-qr-code me-2 text-primary"></i> Fill Details for QR Code Generation
                    </h5>
                    <a href="javascript:history.go(-1)" class="btn btn-outline-primary btn-sm">
                        <i class="icofont icofont-reply"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="progress mb-4" style="height:25px;border-radius:50px;">
                        <div class="progress-bar bg-success text-white" id="formProgress"
                            style="width:33.3%;border-radius:50px;">Step 1/3</div>
                    </div>

                    <form id="assetForm" onsubmit="return false;">
                        <input type="hidden" id="record_id" name="record_id" value="<?= htmlspecialchars($editRow['id'] ?? '') ?>">

                        <div class="step" id="step1">
                            <h5 class="fw-bold text-secondary mb-3"><i class="ti-map me-2 text-primary"></i> Location & Building</h5>
                            <div class="row g-3">
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Location <span class="text-danger">*</span></span>
                                    <select aria-label="Location" class="form-select js-example-basic-single required" id="location" name="location"
                                        onchange="onLocationChange(this.value)">
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $loc): ?>
                                            <option
                                                value="<?= htmlspecialchars($loc['id'] ?? '') ?>"
                                                <?= ($editRow && $editRow['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loc['short_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6  w-full">
                                    <span class="form-label">Building <span class="text-danger">*</span></span>
                                    <select aria-label="Building" class="form-select js-example-basic-single required" id="building" name="building"
                                        onchange="onBuildingChange(this.value)">
                                        <option value="">Select Building</option>
                                    </select>
                                </div>

                                <div class="col-md-6 w-full">
                                    <span class="form-label">Floor </span>
                                    <select aria-label="Floor" class="form-select js-example-basic-single " id="floor" name="floor"
                                        onchange="onFloorChange(this.value)">
                                        <option value="">Select Floor</option>
                                    </select>
                                </div>

                                <div class="col-md-6 w-full">
                                    <span class="form-label">Space </span>
                                    <select aria-label="Space" class="form-select js-example-basic-single " id="space" name="space"
                                        onchange="updatePreview()">
                                        <option value="">Select Space</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="step d-none" id="step2">
                            <h5 class="fw-bold text-secondary mb-3"><i class="ti-package me-2 text-primary"></i> Select
                                Type / Asset</h5>

                            <div class="mb-3">
                                <span class="form-label d-block">What do you want to generate?<span
                                        class="text-danger">*</span></span>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input required" type="radio" name="asset_type"
                                            value="Assets" id="typeAssets" onchange="onTypeChange('Assets')"  <?= ($editRow && $editRow['code_type'] == 'Assets') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="typeAssets">Assets</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input required" type="radio" name="asset_type"
                                            value="Item" id="typeItem" onchange="onTypeChange('Item')" <?= ($editRow && $editRow['code_type'] == 'Item') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="typeItem">Items</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Please choose a type.</div>
                            </div>

                            <!-- Asset fields -->
                            <div class="row g-3 mb-4 d-none" id="assetFields">
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Asset <span class="text-danger">*</span></span>
                                    <select aria-label="Asset Description" class="form-select js-example-basic-single" id="assetDesc" name="asset_description"
                                        onchange="onAssetChange(this.value)">
                                        <option value="">Select Asset</option>
                                        <?php foreach ($assets as $a): ?>
                                            <option value="<?= htmlspecialchars($a['id'] ?? '') ?>"
                                                data-location="<?= htmlspecialchars($a['asset_location']  ?? '') ?>"
                                                data-code="<?= htmlspecialchars($a['asset_code'] ?? '') ?>"
                                                 <?= ($editRow && $editRow['asset_id'] == $a['id']) ? 'selected' : '' ?> >
                                                <?= htmlspecialchars($a['asset_description']  ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please choose an asset.</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="form-label">Asset Code </span>
                                    <input aria-label="Asset Code" type="text" class="form-control" id="assetCode" name="asset_code" readonly value="<?= $editRow ? htmlspecialchars($editRow['asset_code']) : '' ?>">
                                </div>
                            </div>

                            <!-- Item fields -->
                            <div class="row g-3 mb-4 d-none" id="itemFields">
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Item <span class="text-danger">*</span></span>
                                    <select aria-label="Item Name" class="form-select js-example-basic-single" id="itemName" name="item_name"
                                        onchange="onItemChange(this.value)">
                                        <option value="">Select Item</option>
                                        <?php foreach ($items as $it): ?>
                                            <option value="<?= htmlspecialchars($it['id'] ?? '') ?>"
                                                data-location="<?= htmlspecialchars($it['item_location'] ?? $it['location_id'] ?? '') ?>"
                                                data-sku="<?= htmlspecialchars($it['sku'] ?? '') ?>"
                                                <?= ($editRow && $editRow['item_id'] == $it['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($it['item_name'] ?? $it['sku'] ?? '(item)') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please choose an item.</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="form-label">SKU</span>
                                    <input aria-label="Sku" type="text" class="form-control" id="sku" name="sku" readonly value="<?= $editRow ? htmlspecialchars($editRow['sku']) : '' ?>">
                                </div>
                            </div>

                        </div>

                        <!-- STEP 3: Additional Notes + Generate -->
                        <div class="step d-none" id="step3">
                            <h5 class="fw-bold text-secondary mb-3"><i class="ti-notepad me-2 text-primary"></i>
                                Additional Notes</h5>
                            <div class="mb-4">
                                <textarea aria-label="Any additional details..." class="form-control" id="remarks" name="remarks" rows="5"
                                    placeholder="Any additional details..."><?= $editRow ? htmlspecialchars($editRow['remarks']) : '' ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <div class="d-flex w-100 gap-3">
                                <button type="button" id="prevBtn"
                                    class="btn btn-outline-secondary step-btn d-none">Back</button>
                                <button type="button" id="nextBtn" class="btn btn-primary step-btn">Next</button>
                                <button type="submit" id="saveBtn" class="btn btn-warning step-btn d-none">Save
                                    Now</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- preview column -->
        <div class="col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold" id="previewHeader">Live Details Preview</div>
                <div class="card-body custom-scroll" id="previewCard">
                    <p class="text-muted">Fill the form to see details in preview of every step...</p>
                </div>
                <div class="card-footer bg-white border-0">
                    <!-- small QR holder (appended on Generate) -->
                    <div id="smallQrHolder" class="text-center">
                        <?php if ($editRow && $editRow['qr_code']){ ?>
                            <div class="text-center mb-3">
                                <img src="uploads/all/<?= htmlspecialchars($editRow['qr_code']) ?>"
                                    alt="QR Code" style="max-width:140px;">
                                <div class="small text-muted mt-1">Existing QR</div>
                            </div>
                            <?php } ?>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha384-3zSEDfvllQohrq0PHL1fOXJuC/jSOO34H46t6UQfobFOmxE5BpjjaIJY5F2/bMnU" crossorigin="anonymous"></script>

<script>
    let currentStep = 1, totalSteps = 3;
    let selectedType = '';
    let selectedLocation = '';
    const ALL_ASSETS = <?= json_encode($assets ?: []) ?>;

    function isVisible(el) { return !!(el && (el.offsetWidth || el.offsetHeight || el.getClientRects().length)); }

    function showStep(s) {
        document.querySelectorAll('.step').forEach(x => x.classList.add('d-none'));
        document.getElementById('step' + s).classList.remove('d-none');
        const pb = document.getElementById('formProgress');
        pb.style.width = (s / totalSteps * 100) + '%';
        pb.innerText = `Step ${s}/${totalSteps}`;
        document.getElementById('prevBtn').classList.toggle('d-none', s === 1);
        document.getElementById('nextBtn').innerText = s === totalSteps ? 'Generate QR' : 'Next';
    }

    function validateStep(s) {
        let ok = true;
        const stepEl = document.getElementById('step' + s);
        if (!stepEl) return false;
        const requireds = stepEl.querySelectorAll('.required');
        requireds.forEach(el => {
            if (!isVisible(el)) { el.classList.remove('is-invalid'); return; }
            let inv = false;
            if (el.type === 'radio') inv = !document.querySelector(`input[name="${el.name}"]:checked`);
            else inv = !String(el.value || '').trim();
            el.classList.toggle('is-invalid', inv);
            if (inv) ok = false;
        });
        return ok;
    }

    document.getElementById('nextBtn').onclick = () => {
        if (currentStep < totalSteps) {
            if (!validateStep(currentStep)) return;
            currentStep++;
            showStep(currentStep);
            updatePreview();
        } else {
            if (!validateStep(currentStep)) return;
            generateSmallQR();
            document.getElementById('saveBtn').classList.remove('d-none');
            document.getElementById('prevBtn').disabled = true;
            document.getElementById('remarks').disabled = true;
        }
    };

    document.getElementById('prevBtn').onclick = () => {
        currentStep = Math.max(1, currentStep - 1);
        showStep(currentStep);
    };

    function onLocationChange(locId) {
        const buildingSel = $('#building');
        buildingSel.empty().append ('<option value="">Select Building</option>');

        if (!locId) return;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                    page: 'getselectedlist',
                    id: locId,
                    field: 'location_id',
                    table_name: 'buildings'
                },
            success: function (d) {
                try {
                    if (d) {
                        JSON.parse(d).forEach(function(r) {
                            buildingSel.append('<option value="' + r.id + '">' + r.building_name + '</option>');
                        });

                        // ✅ Preselect BUILDING here (no timeouts)
                        <?php if (!empty($editRow)) { ?>
                        if (String(locId) === "<?= htmlspecialchars($editRow['location_id'] ?? '') ?>") {
                            const bId = "<?= htmlspecialchars($editRow['building_id'] ?? '') ?>";
                            if (bId) {
                                buildingSel.val(bId);
                                // also invoke the cascade explicitly
                                onBuildingChange(bId);
                            }
                        }
                    <?php } ?>
                    }
                } catch (e) {
                    console.error("Invalid JSON from server", e, d);
                }
            }
        });
        document.getElementById('floor').innerHTML = '<option value="">Select Floor</option>';
        document.getElementById('space').innerHTML = '<option value="">Select Space</option>';
        selectedLocation = locId;
        rebuildAssetOptions(locId, "<?= htmlspecialchars($editRow['asset_id'] ?? '') ?>");
        updatePreview();
    }

    function onBuildingChange(bId) {
        const floorSel = $('#floor');
        floorSel.empty().append ('<option value="">Select Floor</option>');

        if (!bId) return;

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                    page: 'getselectedlist',
                    id: bId,
                    field: 'building_id',
                    table_name: 'building_floors'
                },
            success: function (d) {
                try {
                    if (d) {
                        JSON.parse(d).forEach(function(r) {
                            floorSel.append('<option value="' + r.id + '">' + r.floor_name + '</option>');
                        });
                        // ✅ Preselect FLOOR here (no timeouts)
                        <?php if (!empty($editRow)) { ?>
                            if (String(bId) === "<?= htmlspecialchars($editRow['building_id'] ?? '') ?>") {
                                const fId = "<?= htmlspecialchars($editRow['building_floor_id'] ?? '') ?>";
                                if (fId) {
                                    floorSel.val(fId);
                                    // cascade to spaces
                                    onFloorChange(fId);
                                }
                            }
                        <?php } ?>
                    }
                } catch (e) {
                    console.error("Invalid JSON from server (floor)", e, d);
                }
            }
        });
        document.getElementById('space').innerHTML = '<option value="">Select Space</option>';
        updatePreview();
    }

    function onFloorChange(fId) {
        const spaceSel = $('#space');
        spaceSel.empty().append('<option value="">Select Any One</option>');

        if (!fId) return;

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'getselectedlist',
                id: fId,
                field: 'building_floor_id',
                table_name: 'building_floor_spaces'
            },
            success: function(d) {
                if (d) {
                    JSON.parse(d).forEach(function(r) {
                        spaceSel.append('<option value="' + r.id + '">' + r.cabin_name + '</option>');
                    });
                     // ✅ Preselect SPACE here (no timeouts)
                     <?php if (!empty($editRow)) { ?>
                        if (String(fId) === "<?= htmlspecialchars($editRow['building_floor_id'] ?? '') ?>") {
                            const sId = "<?= htmlspecialchars($editRow['building_floor_space_id'] ?? '') ?>";
                            if (sId) {
                                spaceSel.val(sId).trigger('change');
                            }
                        }
                    <?php } ?>
                }
            }
        });
        updatePreview();
    }

    function onTypeChange(type) {
        selectedType = type;
        // clear previous selections to avoid stale preview
        document.getElementById('assetFields').classList.add('d-none');
        document.getElementById('itemFields').classList.add('d-none');
        if (type === 'Assets') {
            document.getElementById('assetFields').classList.remove('d-none');
        } else if (type === 'Item') {
            document.getElementById('itemFields').classList.remove('d-none');
        }
        // Reset asset/item inputs
        const ac = document.getElementById('assetDesc'); if (ac) { ac.value = ''; document.getElementById('assetCode').value = ''; }
        const it = document.getElementById('itemName'); if (it) { it.value = ''; document.getElementById('sku').value = ''; }
        document.querySelectorAll('#step2 .required').forEach(r => r.classList.remove('is-invalid'));
        updatePreview();
    }

    function onAssetChange() {
        const sel = document.getElementById('assetDesc');
        document.getElementById('assetCode').value = sel.options[sel.selectedIndex]?.dataset.code || '';
        updatePreview();
    }

    function onItemChange() {
        const sel = document.getElementById('itemName');
        document.getElementById('sku').value = sel.options[sel.selectedIndex]?.dataset.sku || '';
        updatePreview();
    }

    function buildPayload() {
        const form = document.getElementById('assetForm');
        const type = selectedType || (form.querySelector('input[name="asset_type"]:checked')?.value || '');
        const payload = {};
        payload['location_id'] = document.getElementById('location')?.value || '';
        payload['building_id'] = document.getElementById('building')?.value || '';
        payload['building_floor_id'] = document.getElementById('floor')?.value || '';
        payload['building_floor_space_id'] = document.getElementById('space')?.value || '';
        if (type === 'Item') {
            payload['code_type'] = 'Item';
            payload['item_id'] = document.getElementById('itemName')?.value || '';
            payload['sku'] = document.getElementById('sku')?.value || '';
        } else if (type === 'Assets') {
            payload['code_type'] = 'Assets';
            payload['asset_id'] = document.getElementById('assetDesc')?.value || '';
            payload['asset_code'] = document.getElementById('assetCode')?.value || '';
        } else {
            payload['code_type'] = type || '';
        }
        const remarks = document.getElementById('remarks')?.value;
        if (remarks) payload['remarks'] = remarks;
        return payload;
    }

    function updatePreview() {
        let html = '<div class="list-group list-group-flush">';
        const form = document.getElementById('assetForm');

        const fields = [
            { name: 'location', label: '📍 Location' },
            { name: 'asset_type', label: '📦 Type' },
            { name: 'building', label: '🏢 Building' },
            { name: 'floor', label: '🧱 Floor' },
            { name: 'space', label: '📐 Space' },
            { name: 'item_name', label: '📌 Item' },
            { name: 'sku', label: '🔖 SKU' },
            { name: 'asset_description', label: '📌 Asset' },
            { name: 'asset_code', label: '🏷️ Asset Code' },
            { name: 'remarks', label: '📝 Notes' }
        ];

        let hasData = false;

        fields.forEach(f => {
            const el = form.querySelector(`[name="${f.name}"]`);
            if (!el) return;
            let display = '';
            if (el.type === 'radio') {
                const checked = document.querySelector(`input[name="${el.name}"]:checked`);
                display = checked ? checked.value : '';
            } else if (el.tagName === 'SELECT') {
                const opt = el.options[el.selectedIndex];
                display = opt && opt.value ? (opt.text || opt.value) : '';
            } else {
                display = el.value ? el.value.trim() : '';
            }
            if (display) {
                hasData = true;
                html += `<div class="list-group-item d-flex justify-content-between align-items-center"><span class="fw-bold">${f.label}</span><span class="badge bg-light text-dark px-3 py-2">${display}</span></div>`;
            }
        });

        html += '</div>';

        document.getElementById('previewCard').innerHTML = hasData ? html : '<p class="text-muted">Fill the form to see details in preview of every step...</p>';
        document.getElementById('previewHeader').innerText = hasData ? 'Live Details Preview' : 'Preview';
    }

    function generateSmallQR() {
        const payload = buildPayload();

        const holder = document.getElementById('smallQrHolder');
        holder.innerHTML = '';
        // UI: visible QR container + a download button
        const qrWrap = document.createElement('div');
        qrWrap.className = 'd-flex flex-column align-items-center';
        qrWrap.innerHTML = `
            <div id="smallQr" style="padding:8px;background:#fff;border-radius:12px;display:inline-block;"></div>
            <button id="downloadSmallQr" class="btn btn-sm btn-success mt-2">Download QR</button>
        `;
        holder.appendChild(qrWrap);
        const sizeDisplay = 120;
        const qrContainer = qrWrap.querySelector('#smallQr');
        new QRCode(qrContainer, {
            text: JSON.stringify(payload),     // keep as-is since your scanner app works
            width: sizeDisplay,
            height: sizeDisplay,
            correctLevel: QRCode.CorrectLevel.L
        });
        // 2) After the small QR is rendered, wire up the download button
        setTimeout(() => {
            document.getElementById('downloadSmallQr').onclick = () => {
            const tmp = document.createElement('div');
            const sizeDownload = 360;

            new QRCode(tmp, {
                text: JSON.stringify(payload),
                width: sizeDownload,
                height: sizeDownload,
                correctLevel: QRCode.CorrectLevel.L
            });
            // Give the library a moment to inject the canvas
            setTimeout(() => {
                const canvas = tmp.querySelector('canvas');
                if (!canvas) return;
                // Download the high-res PNG
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'qr-code.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                // Cleanup
                tmp.remove();
            }, 150);
            };
        }, 150);
    }

    document.getElementById('assetForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const submitBtn = document.getElementById('saveBtn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;

        try {
            const payload = buildPayload();

            // ✅ Convert QR canvas to file (blob)
            const smallCanvas = document.querySelector('#smallQrHolder canvas');
            let blobFile = null;
            if (smallCanvas) {
                const dataUrl = smallCanvas.toDataURL('image/png');
                const res = await fetch(dataUrl);
                blobFile = await res.blob();
            }

            const formData = new FormData();
            formData.append('page', 'insertqrgeneration'); // your page name
            formData.append('table_name', 'complaint_qr_codes'); // your table name
            formData.append('record_id', $('#record_id').val()); // your table name
            formData.append('qr_code_data', JSON.stringify(payload));
            if (blobFile) {
                formData.append('qr_file', blobFile, 'qr.png'); // <-- this is what backend expects
            }

            // append the payload fields
            Object.keys(payload).forEach(key => {
                formData.append(key, payload[key] ?? '');
            });

            // ✅ Send to backend
            const response = await fetch('ajax.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('HTTP error: ' + response.status);

            const text = await response.text();
            let resJson;
            try {
                resJson = JSON.parse(text);
            } catch (err) {
                throw new Error('Invalid JSON response: ' + text.substring(0, 200));
            }
            console.log('resJson:', resJson.status);
            if (resJson.status && resJson.status.toLowerCase() == 'sucess') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: resJson.message || 'Saved successfully!',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'OK',
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resJson.message || 'Failed to save.'
                });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message });
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        ['location', 'building', 'floor', 'space', 'itemName', 'assetDesc', 'remarks'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', updatePreview);
        });
        document.getElementById('assetForm').addEventListener('input', updatePreview);
        showStep(currentStep);

    const recordId = document.getElementById('record_id')?.value || '';
    if (recordId) {
        const locId   = "<?= $editRow['location_id'] ?? '' ?>";
        if (locId) {
            $('#location').val(locId);
            onLocationChange(locId);
        }
        var assetType =  '<?php echo $editRow['code_type'] ?? '';?>';
        if (assetType === 'Assets') {
            document.getElementById('assetFields').classList.remove('d-none');
        }
        if (assetType === 'Item') {
            document.getElementById('itemFields').classList.remove('d-none');
        }

        setTimeout(updatePreview, 800);
    }
    });

    function rebuildAssetOptions(locId, preselectId = '') {
        const sel = $('#assetDesc');
        sel.empty().append('<option value="">Select Asset</option>');
        const rows = ALL_ASSETS.filter(a => String(a.asset_location || '') === String(locId));
        rows.forEach(a => {
            sel.append(
            `<option value="${a.id}" data-location="${a.asset_location||''}" data-code="${a.asset_code||''}">
                ${a.asset_description || a.asset_code || '(asset)'}
            </option>`
            );
        });
        if (preselectId) sel.val(String(preselectId));
        if (sel.hasClass('js-example-basic-single')) sel.trigger('change');
    }

</script>
