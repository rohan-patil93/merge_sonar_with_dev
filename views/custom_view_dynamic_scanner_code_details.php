<?php
$_SESSION['apply_js']['multiselest'] = '1';

$url = API . "?mode=getscannerqrdetails&method=get";
$response = callAPI($url);
$data = json_decode($response, true);

$processes = [];
$process_tables = [];

if (!empty($data['process_list']) && is_array($data['process_list'])) {
    foreach ($data['process_list'] as $index => $process) {
        $process_id = $index + 1;
        $processes[$process_id] = $process['process_name'];

        $field_mappings = [];
        if (!empty($process['field_mappings'])) {
            $mappings = json_decode($process['field_mappings'], true);
            if (is_array($mappings)) {
                foreach ($mappings as $mapping) {
                    $field_mappings[] = [
                        'original_field' => $mapping['original_field'],
                        'custom_label' => $mapping['custom_label'],
                        'order_number' => $mapping['order_number']
                    ];
                }
            }
        }

        $process_tables[$process_id] = [
            'table' => $process['process_table'],
            'field_mappings' => $field_mappings,
            'process_name' => $process['process_name']
        ];
    }
}

$print_sizes = [
    'small' => '2x2 inches (50x50mm)',
    'medium' => '3x3 inches (75x75mm)',
    'large' => '4x4 inches (100x100mm)',
    'xlarge' => '5x5 inches (125x125mm)',
    'custom' => 'Custom Size'
];

$selected_process = null;
$selected_table_info = null;

// Debug: Show POST data
if (isset($_GET['debug'])) {
    echo "<div class='alert alert-info'><strong>POST Data:</strong><pre>" . print_r($_POST, true) . "</pre></div>";
    if (isset($_POST['generated_image_data'])) {
        echo "<div class='alert alert-warning'><strong>Image Data Length:</strong> " . strlen($_POST['generated_image_data']) . " characters</div>";
        echo "<div class='alert alert-info'><strong>Image Data Preview:</strong> " . substr($_POST['generated_image_data'], 0, 100) . "...</div>";
    } else {
        echo "<div class='alert alert-danger'><strong>No Image Data Found in POST</strong></div>";
    }

    $uploadDir = "uploads/scanner_codes/";
    echo "<div class='alert alert-info'>";
    echo "<strong>Upload Directory Status:</strong><br>";
    echo "Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "<br>";
    echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";
    echo "Full path: " . realpath($uploadDir) . "<br>";
    echo "</div>";
}

if (isset($_POST['selected_process']) && $_POST['selected_process']) {
    $selected_process = $_POST['selected_process'];
    $selected_table_info = $process_tables[$selected_process] ?? null;
}
?>

<div class="page-body">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ti-qrcode me-2 text-primary"></i>QR/Barcode Generator</h5>
                    <a href="javascript:history.go(-1)" class="btn btn-outline-primary btn-sm">
                        <i class="icofont icofont-reply"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="progress mb-4" style="height:25px;border-radius:50px;">
                        <div class="progress-bar bg-primary text-white" id="formProgress" style="width:20%;border-radius:50px;">Step 1/5</div>
                    </div>

                    <form id="qrForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="generated_code_data" id="generatedCodeData" value="">
                        <input type="hidden" name="generated_image_data" id="generatedImageData" value="">
                        <input type="hidden" name="saved_image_filename" id="savedImageFilename" value="">


                        <div class="step" id="step1">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="ti-qrcode me-2 text-primary"></i>Select Code Type
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border-2 code-type-card" data-type="qr">
                                        <div class="card-body text-center p-4">
                                            <h6 class="fw-bold">QR Code</h6>
                                            <p class="text-muted small">QR code for mobile scanning</p>
                                            <input aria-label="Code Types[]" type="checkbox" name="code_types[]" value="qr" class="form-check-input d-none" id="qr_checkbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-2 code-type-card" data-type="barcode">
                                        <div class="card-body text-center p-4">
                                            <h6 class="fw-bold">Barcode</h6>
                                            <p class="text-muted small">Barcode for traditional scanning</p>
                                            <input aria-label="Code Types[]" type="checkbox" name="code_types[]" value="barcode" class="form-check-input d-none" id="barcode_checkbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-2 code-type-card" data-type="both">
                                        <div class="card-body text-center p-4">
                                            <h6 class="fw-bold">Both</h6>
                                            <p class="text-muted small">Generate both QR and Barcode</p>
                                            <input aria-label="Code Types[]" type="checkbox" name="code_types[]" value="both" class="form-check-input d-none" id="both_checkbox">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="step d-none" id="step2">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="ti-database me-2 text-primary"></i>Select Table and Process
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Application Table <span class="text-danger">*</span></span> </br>
                                    <select aria-label="Ected Table" class="form-select js-example-basic-single required" name="selected_table" id="tableSelect" onchange="onTableSelect(this.value)">
                                        <option value="">Select a table...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Choose Process <span class="text-danger">*</span></span> </br>
                                    <select aria-label="Ected Process" class="form-select js-example-basic-single required" name="selected_process" id="processSelect" onchange="onProcessSelect(this.value)">
                                        <option value="">Select a process...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Process Details -->
                        <div class="step d-none" id="step3">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="ti-database me-2 text-primary"></i>Process Details
                            </h5>
                            <div id="processDetailsContainer">
                                <div class="text-center p-4">
                                    <i class="fa fa-info-circle text-muted fa-2x mb-2"></i>
                                    <p class="text-muted">Select a process to view details</p>
                                </div>
                            </div>
                        </div>


                        <div class="step d-none" id="step4">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="ti-printer me-2 text-primary"></i>Print Settings
                            </h5>


                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <span class="form-label fw-bold">Print Size <span class="text-danger">*</span></span>
                                            <div class="size-options">
                                                <?php foreach ($print_sizes as $size_key => $size_label): ?>
                                                    <div class="form-check mb-2">
                                                        <input aria-label="Print Size" class="form-check-input" type="radio" name="print_size"
                                                               value="<?php echo $size_key; ?>" id="size_<?php echo $size_key; ?>">
                                                        <label class="form-check-label" for="size_<?php echo $size_key; ?>">
                                                            <?php echo $size_label; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <!-- Custom Size Fields -->
                                            <div id="customSizeFields" class="mt-3 d-none">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <span class="form-label small">Width (mm)</span>
                                                        <input aria-label="50" type="number" class="form-control form-control-sm"
                                                               name="custom_width" placeholder="50" min="20" max="200">
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="form-label small">Height (mm)</span>
                                                        <input aria-label="50" type="number" class="form-control form-control-sm"
                                                               name="custom_height" placeholder="50" min="20" max="200">
                                                    </div>
                                                </div>
                                                <small class="text-muted mt-1 d-block">
                                                    <i class="fa fa-info-circle"></i>
                                                    Recommended range: 20-200mm. Larger sizes may affect print quality.
                                                </small>
                                            </div>

                                            <!-- Size Preview Indicator -->
                                            <div id="sizePreviewIndicator" class="mt-3 p-3 bg-light rounded border">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <h6 class="mb-1 text-primary">
                                                            <i class="fa fa-ruler me-1"></i>Dynamic Size Preview
                                                        </h6>
                                                        <div id="sizeInfo" class="small text-muted">
                                                            QR codes and barcodes will automatically scale based on your print size selection
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="badge bg-info text-white px-3 py-2">
                                                            <i class="fa fa-magic me-1"></i>Auto-Scale
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step d-none" id="step5">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="ti-list me-2 text-primary"></i>Table Data
                            </h5>


                            <div id="topBulkActionButtons" class="mb-3 d-none">
                                <div class="card bg-light border-primary">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-0 text-primary">
                                                    <i class="fa fa-tasks me-2"></i>Multi Download/Print
                                                </h6>
                                                <small class="text-muted">Select rows below to perform multi actions</small>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button type="button" class="btn btn-primary btn-sm me-2" onclick="bulkDownload()">
                                                    <i class="fa fa-download me-1"></i>Download Selected (<span id="topSelectedCount">0</span>)
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm" onclick="bulkPrint()">
                                                    <i class="fa fa-print me-1"></i>Print Selected (<span id="topSelectedCountPrint">0</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="tableDataContainer">
                                <div class="text-center p-4">
                                    <i class="fa fa-info-circle text-muted fa-2x mb-2"></i>
                                    <p class="text-muted">Loading table data...</p>
                                </div>
                            </div>
                        </div>


                        <div class="d-flex justify-content-between pt-3">
                            <div class="d-flex w-100 gap-3">
                                <button type="button" id="prevBtn" class="btn btn-outline-secondary step-btn d-none">Back</button>
                                <button type="button" id="nextBtn" class="btn btn-primary step-btn">Next</button>
                                <button type="submit" name="submit_qr_generation" id="generateBtn" class="btn btn-success step-btn d-none" style="display:none" onclick="return validateFinalSubmission();">
                                    <i class="fa fa-save me-1"></i>Save & Download
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold" id="previewHeader">Configuration Preview</div>
                <div class="card-body" id="previewCard">
                    <p class="text-muted">Select options to see preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.code-type-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border-color: #dee2e6 !important;
}

.code-type-card:hover {
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.code-type-card.selected {
    border-color: #007bff !important;
    background-color: #f8f9ff;
}

.placeholder-code {
    padding: 40px 20px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.size-options .form-check {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.size-options .form-check:hover {
    background-color: #f8f9fa;
}

.size-options .form-check-input:checked + .form-check-label {
    font-weight: bold;
    color: #007bff;
}

.qr-code-preview canvas, .barcode-preview canvas {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white;
    padding: 10px;
    max-width: 100%;
    height: auto;
}

.code-preview-container {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.code-container {
    max-width: 100%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

#generateCodeBtn.d-none {
    display: none !important;
}

#codePreview {
    min-height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mapping-item {
    transition: all 0.2s ease;
}

.mapping-item:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff !important;
}

.field-mappings {
    max-height: 300px;
    overflow-y: auto;
}

.field-mappings::-webkit-scrollbar {
    width: 6px;
}

.field-mappings::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.field-mappings::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.field-mappings::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Bulk selection styles */
.row-checkbox {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    font-weight: 500;
}

#selectAllRows {
    cursor: pointer;
}

#bulkActionButtons .btn {
    transition: all 0.2s ease;
}

#bulkActionButtons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table tbody tr:has(.row-checkbox:checked) {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

@media print {
    .codes-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px !important;
    }

    .code-item {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
        margin-bottom: 15px !important;
    }
}

/* Bulk operations layout improvements */
.bulk-layout-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 100%;
    overflow: hidden;
}

.bulk-codes-wrapper {
    display: flex;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    padding: 10px;
}

.bulk-code-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 48%;
    min-width: 200px;
}

.bulk-code-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.bulk-code-canvas {
    max-width: 100%;
    height: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    background: white;
}

@media (max-width: 768px) {
    .bulk-codes-wrapper {
        flex-direction: column;
        gap: 20px;
    }

    .bulk-code-section {
        max-width: 100%;
        min-width: auto;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js" integrity="sha384-Izc791esqyEy3BEIC42q7jbE0AaOkACziN+dyyXgYeDmpeMCLz0xA+xYN3aCd5zz" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js" integrity="sha384-tOUygabpHGzWXpKv3qJM5f9tSgU6p5f4ooCayrNDwzm3/3/CDMzgHMLQZiMMGghV" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

<script>
function generateQRCodeFallback(container, data, codeData) {
    console.log('Using fallback QR generation');

    if (typeof QRCode !== 'undefined') {
        console.log('QRCode available in fallback, trying alternative methods');

        if (typeof QRCode.create === 'function') {
            try {
                console.log('Trying QRCode.create method');
                const qr = QRCode.create(data, { errorCorrectionLevel: 'M' });

                const printSize = document.querySelector('input[name="print_size"]:checked');
                const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 150, height: 150 };

                const canvas = document.createElement('canvas');
                canvas.width = qrSize.width;
                canvas.height = qrSize.height;
                const ctx = canvas.getContext('2d');

                // Draw QR code modules
                const moduleCount = qr.modules.size;
                const moduleSize = Math.min(canvas.width, canvas.height) / moduleCount;

                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';

                for (let row = 0; row < moduleCount; row++) {
                    for (let col = 0; col < moduleCount; col++) {
                        if (qr.modules.get(row, col)) {
                            ctx.fillRect(col * moduleSize, row * moduleSize, moduleSize, moduleSize);
                        }
                    }
                }

                container.innerHTML = `
                    <div class="qr-code-preview text-center">
                        <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                        <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                        <small class="text-muted d-block">Table: ${codeData.table}</small>
                        <small class="text-muted d-block">Generated: ${new Date(codeData.timestamp).toLocaleString()}</small>
                    </div>
                `;
                container.querySelector('.code-container').appendChild(canvas);

                const imageData = canvas.toDataURL('image/png');
                document.getElementById('generatedImageData').value = imageData;
                console.log('Fallback QR (create method) image data stored, length:', imageData.length);

                return;
            } catch (error) {
                console.error('QRCode.create method failed:', error);
            }
        }
    }

    try {
        console.log('Using external QR API as final fallback');

        const printSize = document.querySelector('input[name="print_size"]:checked');
        const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 150, height: 150 };

        const canvas = document.createElement('canvas');
        canvas.width = qrSize.width;
        canvas.height = qrSize.height;
        const ctx = canvas.getContext('2d');

        // Create a simple pattern that represents QR-like structure
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';

        // Calculate module size based on canvas dimensions
        const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));

        // Draw QR-like pattern
        for (let x = 0; x < canvas.width; x += moduleSize) {
            for (let y = 0; y < canvas.height; y += moduleSize) {
                if ((x + y) % (moduleSize * 2) === 0) {
                    ctx.fillRect(x, y, moduleSize, moduleSize);
                }
            }
        }

        const cornerSize = Math.max(20, canvas.width * 0.23);
        const innerCornerSize = cornerSize * 0.7;
        const centerCornerSize = cornerSize * 0.4;

        // Top-left corner
        ctx.fillRect(0, 0, cornerSize, cornerSize);
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(cornerSize * 0.15, cornerSize * 0.15, innerCornerSize, innerCornerSize);
        ctx.fillStyle = '#000000';
        ctx.fillRect(cornerSize * 0.3, cornerSize * 0.3, centerCornerSize, centerCornerSize);

        // Top-right corner
        const topRightX = canvas.width - cornerSize;
        ctx.fillRect(topRightX, 0, cornerSize, cornerSize);
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(topRightX + cornerSize * 0.15, cornerSize * 0.15, innerCornerSize, innerCornerSize);
        ctx.fillStyle = '#000000';
        ctx.fillRect(topRightX + cornerSize * 0.3, cornerSize * 0.3, centerCornerSize, centerCornerSize);

        // Bottom-left corner
        const bottomLeftY = canvas.height - cornerSize;
        ctx.fillRect(0, bottomLeftY, cornerSize, cornerSize);
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(cornerSize * 0.15, bottomLeftY + cornerSize * 0.15, innerCornerSize, innerCornerSize);
        ctx.fillStyle = '#000000';
        ctx.fillRect(cornerSize * 0.3, bottomLeftY + cornerSize * 0.3, centerCornerSize, centerCornerSize);

        container.innerHTML = `
            <div class="qr-code-preview text-center">
                <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                <small class="text-muted d-block">Table: ${codeData.table}</small>
                <small class="text-muted d-block">Generated: ${new Date(codeData.timestamp).toLocaleString()}</small>
                <small class="text-warning d-block">Simulated QR pattern (fallback mode)</small>
            </div>
        `;
        container.querySelector('.code-container').appendChild(canvas);

        const imageData = canvas.toDataURL('image/png');
        document.getElementById('generatedImageData').value = imageData;
        console.log('Fallback QR image data stored for form submission, length:', imageData.length);

        // Add visual indicator that image is ready
        const indicator = document.createElement('div');
        indicator.className = 'alert alert-warning mt-2';
        indicator.innerHTML = '<i class="fa fa-check"></i> Fallback image ready for saving';
        container.appendChild(indicator);

    } catch (error) {
        console.error('Fallback QR generation error:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i>
                Unable to generate QR code. Error: ${error.message}
            </div>
        `;
    }
}

function generateBarcodeFromData(container, data, codeData) {
    console.log('Using barcode fallback generation');

    try {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 250, height: 80 };

        const canvas = document.createElement('canvas');
        canvas.width = barcodeSize.width;
        canvas.height = barcodeSize.height;
        const ctx = canvas.getContext('2d');

        // Create a simple barcode-like pattern
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';

        // Calculate dynamic barcode parameters
        const barWidth = Math.max(1, canvas.width / 150);
        const spacing = Math.max(1, barWidth * 0.8);
        const margin = canvas.width * 0.05;
        let x = margin;

        // Generate pattern based on data hash
        const dataHash = data.split('').reduce((a, b) => {
            a = ((a << 5) - a) + b.charCodeAt(0);
            return a & a;
        }, 0);

        const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
        const maxBarHeight = canvas.height * 0.6;
        const minBarHeight = canvas.height * 0.3;

        for (let i = 0; i < barsCount; i++) {
            const height = minBarHeight + ((dataHash + i) % (maxBarHeight - minBarHeight));
            if ((dataHash + i) % 3 !== 0) {
                ctx.fillRect(x, margin, barWidth, height);
            }
            x += barWidth + spacing;
        }

        // Add text with dynamic font size
        const fontSize = Math.max(8, Math.min(12, canvas.width / 25));
        ctx.font = `${fontSize}px Arial`;
        ctx.textAlign = 'center';
        ctx.fillText('FALLBACK-BARCODE-' + Date.now().toString().slice(-6), canvas.width / 2, canvas.height - margin);

        container.innerHTML = `
            <div class="barcode-preview text-center">
                <div class="code-container mb-2" style="display: flex; justify-content: center; overflow: hidden;"></div>
                <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                <small class="text-muted d-block">Table: ${codeData.table}</small>
                <small class="text-muted d-block">Generated: ${new Date(codeData.timestamp).toLocaleString()}</small>
                <small class="text-warning d-block">Fallback barcode pattern</small>
            </div>
        `;
        container.querySelector('.code-container').appendChild(canvas);

        const imageData = canvas.toDataURL('image/png');
        document.getElementById('generatedImageData').value = imageData;
        console.log('Fallback barcode image data stored, length:', imageData.length);


    } catch (error) {
        console.error('Fallback barcode generation error:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i>
                Unable to generate barcode. Error: ${error.message}
            </div>
        `;
    }
}

const processData = <?php echo json_encode($process_tables); ?>;
let currentStep = 1;
const totalSteps = 5;
let generatedCodeData = null;
let availableTables = {};
let tableProcesses = {};

function showStep(step) {
    document.querySelectorAll('.step').forEach(x => x.classList.add('d-none'));
    document.getElementById('step' + step).classList.remove('d-none');

    const progressBar = document.getElementById('formProgress');
    progressBar.style.width = (step / totalSteps * 100) + '%';
    progressBar.innerText = `Step ${step}/${totalSteps}`;

    document.getElementById('prevBtn').classList.toggle('d-none', step === 1);
    document.getElementById('nextBtn').classList.toggle('d-none', step === totalSteps);
    document.getElementById('generateBtn').classList.toggle('d-none', step !== totalSteps);

    // Load table data when reaching step 5
    if (step === 5) {
        loadTableData();
    }

    updatePreview();
}

function validateStep(step) {
    console.log('Validating step:', step);
    let isValid = true;

    if (step === 1) {
        const codeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
        if (codeTypes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Code Type Required',
                text: 'Please select at least one code type',
                confirmButtonColor: '#007bff'
            });
            return false;
        }
        console.log('Step 1 validation passed - Code types:', Array.from(codeTypes).map(c => c.value));
    }

    if (step === 2) {
        const tableSelect = document.getElementById('tableSelect');
        const processSelect = document.getElementById('processSelect');

        if (!tableSelect.value) {
            tableSelect.classList.add('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Table Required',
                text: 'Please select a table',
                confirmButtonColor: '#007bff'
            });
            return false;
        }

        if (!processSelect.value) {
            processSelect.classList.add('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Process Required',
                text: 'Please select a process',
                confirmButtonColor: '#007bff'
            });
            return false;
        }

        tableSelect.classList.remove('is-invalid');
        processSelect.classList.remove('is-invalid');
        console.log('Step 2 validation passed - Table:', tableSelect.value, 'Process:', processSelect.value);
    }

    if (step === 4) {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        if (!printSize) {
            Swal.fire({
                icon: 'warning',
                title: 'Print Size Required',
                text: 'Please select a print size',
                confirmButtonColor: '#007bff'
            });
            return false;
        }

        if (printSize.value === 'custom') {
            const width = document.querySelector('input[name="custom_width"]').value;
            const height = document.querySelector('input[name="custom_height"]').value;
            if (!width || !height) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Custom Size Required',
                    text: 'Please enter custom width and height',
                    confirmButtonColor: '#007bff'
                });
                return false;
            }
        }

        console.log('Step 4 validation passed - Print size:', printSize.value);
    }

    return isValid;
}

// Code type selection
document.querySelectorAll('.code-type-card').forEach(card => {
    card.addEventListener('click', function() {
        const type = this.dataset.type;
        const checkbox = document.getElementById(type + '_checkbox');

        // Toggle selection
        if (this.classList.contains('selected')) {
            this.classList.remove('selected');
            checkbox.checked = false;
        } else {
            this.classList.add('selected');
            checkbox.checked = true;
        }

        // Handle "both" option
        if (type === 'both') {
            if (checkbox.checked) {
                document.querySelectorAll('.code-type-card').forEach(c => {
                    if (c.dataset.type !== 'both') {
                        c.classList.add('selected');
                        document.getElementById(c.dataset.type + '_checkbox').checked = true;
                    }
                });
            } else {
                // Deselect all
                document.querySelectorAll('.code-type-card').forEach(c => {
                    c.classList.remove('selected');
                    document.getElementById(c.dataset.type + '_checkbox').checked = false;
                });
            }
        } else {
            const qrSelected = document.getElementById('qr_checkbox').checked;
            const barcodeSelected = document.getElementById('barcode_checkbox').checked;
            const bothCard = document.querySelector('[data-type="both"]');
            const bothCheckbox = document.getElementById('both_checkbox');

            if (qrSelected && barcodeSelected) {
                bothCard.classList.add('selected');
                bothCheckbox.checked = true;
            } else {
                bothCard.classList.remove('selected');
                bothCheckbox.checked = false;
            }
        }

        if (window.tableData && window.tableData.length > 0) {
            const processId = document.getElementById('processSelect').value;
            if (processId && processData[processId]) {
                displayTableData(window.tableData, processData[processId].field_mappings);
            }
        }

        updatePreview();
    });
});

// Table selection
function onTableSelect(tableName) {
    console.log('Table selected:', tableName);
    if (tableName) {
        loadProcessesForTable(tableName);
    } else {
        const processSelect = document.getElementById('processSelect');
        processSelect.innerHTML = '<option value="">Select a process...</option>';
    }
    updatePreview();
}

// Process selection
function onProcessSelect(processId) {
    if (processId && processData[processId]) {
        loadProcessDetails(processId);

        const codeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
        const printSize = document.querySelector('input[name="print_size"]:checked')?.value;

        if (codeTypes.length > 0 && printSize) {
            console.log('Process selected, auto-generating code with existing selections');
            setTimeout(() => {
                const firstCodeType = codeTypes[0].value === 'both' ? 'qr' : codeTypes[0].value;
                generateCodePreview(firstCodeType, processId);
            }, 200);
        }
    }
    updatePreview();
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'codeQuantity') {
            updateQuantityText();
        }
    });
});

function loadAvailableTables() {
    console.log('Loading available tables from process data:', processData);

    if (!processData || typeof processData !== 'object') {
        console.error('ProcessData is not valid:', processData);
        return;
    }

    const uniqueTables = {};
    Object.values(processData).forEach((process, index) => {
        console.log(`Processing item ${index}:`, process);
        if (process && process.table) {
            const tableName = process.table;
            const readableName = tableName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            uniqueTables[tableName] = readableName;
            console.log(`Added table: ${tableName} -> ${readableName}`);
        }
    });

    availableTables = uniqueTables;
    console.log('Final available tables:', availableTables);
    populateTableDropdown();
}

// Populate table dropdown
function populateTableDropdown() {
    const tableSelect = document.getElementById('tableSelect');
    tableSelect.innerHTML = '<option value="">Select a table...</option>';

    console.log('Populating table dropdown with:', availableTables);

    Object.entries(availableTables).forEach(([tableName, displayName]) => {
        const option = document.createElement('option');
        option.value = tableName;
        option.textContent = displayName;
        tableSelect.appendChild(option);
    });

    console.log('Table dropdown populated with', Object.keys(availableTables).length, 'tables');
}

function loadProcessesForTable(tableName) {
    const processSelect = document.getElementById('processSelect');
    processSelect.innerHTML = '<option value="">Select a process...</option>';

    console.log('Loading processes for table:', tableName);
    console.log('Available process data:', processData);

    // Filter processes by table
    const filteredProcesses = {};
    Object.entries(processData).forEach(([processId, processInfo]) => {
        console.log(`Checking process ${processId}:`, processInfo.table, 'vs', tableName);
        if (processInfo.table === tableName) {
            filteredProcesses[processId] = processInfo.process_name;
        }
    });

    console.log('Filtered processes for table', tableName, ':', filteredProcesses);

    // Populate process dropdown
    Object.entries(filteredProcesses).forEach(([processId, processName]) => {
        const option = document.createElement('option');
        option.value = processId;
        option.textContent = processName;
        processSelect.appendChild(option);
    });

    if (Object.keys(filteredProcesses).length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No processes available for this table';
        option.disabled = true;
        processSelect.appendChild(option);
    }

    console.log('Process dropdown populated with', Object.keys(filteredProcesses).length, 'processes');
}

function loadTableData() {
    const tableSelect = document.getElementById('tableSelect');
    const processSelect = document.getElementById('processSelect');
    const container = document.getElementById('tableDataContainer');

    if (!tableSelect.value || !processSelect.value) {
        container.innerHTML = '<div class="alert alert-warning">Please select table and process first</div>';
        return;
    }

    const processInfo = processData[processSelect.value];
    if (!processInfo) {
        container.innerHTML = '<div class="alert alert-danger">Process information not found</div>';
        return;
    }

    container.innerHTML = '<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading table data...</p></div>';

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            page: 'getfulltabledetails',
            table_name: tableSelect.value,
            process_id: processSelect.value,
            field_mappings: JSON.stringify(processInfo.field_mappings || [])
        },
        success: function(response) {
            try {
                let result;
                if (typeof response === 'string') {
                    result = JSON.parse(response);
                } else if (typeof response === 'object') {
                    result = response;
                } else {
                    throw new Error('Unexpected response type: ' + typeof response);
                }
                if (result.details && Array.isArray(result.details)) {
                    displayTableData(result.details, processInfo.field_mappings);
                } else if (result.status === 'success' && result.data) {
                    displayTableData(result.data, result.field_mappings);
                } else if (Array.isArray(result)) {
                    displayTableData(result, processInfo.field_mappings);
                } else {
                    container.innerHTML = '<div class="alert alert-warning">No data found. Response structure: ' + JSON.stringify(Object.keys(result)) + '</div>';
                }
            } catch (e) {
                if (typeof response === 'string' && response.includes('details')) {
                    try {
                        const jsonMatch = response.match(/\{.*\}/s);
                        if (jsonMatch) {
                            const cleanJson = JSON.parse(jsonMatch[0]);
                            if (cleanJson.details && Array.isArray(cleanJson.details)) {
                                displayTableData(cleanJson.details, processInfo.field_mappings);
                                return;
                            }
                        }
                    } catch (e2) {
                        console.error('Failed to extract JSON from mixed response:', e2);
                    }
                }
                container.innerHTML = '<div class="alert alert-danger">Invalid response from server. Check console for details.<br><small>Error: ' + e.message + '</small></div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading table data:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading table data: ' + error + '</div>';
        }
    });
}

// Display table data
function displayTableData(data, fieldMappings) {
    const container = document.getElementById('tableDataContainer');

    console.log('displayTableData called with:', {
        dataType: typeof data,
        dataLength: Array.isArray(data) ? data.length : 'not array',
        data: data,
        fieldMappings: fieldMappings
    });

    if (!data || data.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No data found in this table</div>';
        return;
    }

    console.log('Displaying table data:', data);
    console.log('Field mappings:', fieldMappings);
    console.log('First row keys:', Object.keys(data[0]));
    console.log('First row data:', data[0]);

    if (fieldMappings && fieldMappings.length > 0) {
        console.log('Checking field mapping matches:');
        fieldMappings.forEach(mapping => {
            const exists = data[0].hasOwnProperty(mapping.original_field);
            console.log(`Field "${mapping.original_field}" exists in data:`, exists);
            if (exists) {
                console.log(`Sample value for "${mapping.original_field}":`, data[0][mapping.original_field]);
            }
        });
    }

    // Sort field mappings by order
    const sortedMappings = (fieldMappings || []).sort((a, b) =>
        parseInt(a.order_number || 0) - parseInt(b.order_number || 0)
    );

    let html = `
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
    `;

    // Determine which columns to show
    let columnsToShow = [];

    if (sortedMappings.length > 0) {
        const dataKeys = Object.keys(data[0]);
        const validMappings = sortedMappings.filter(mapping => {
            const exists = dataKeys.includes(mapping.original_field);
            if (!exists) {
                console.warn(`Field mapping "${mapping.original_field}" not found in data. Available keys:`, dataKeys);
            }
            return exists;
        });

        if (validMappings.length > 0) {
            columnsToShow = validMappings.map(mapping => ({
                key: mapping.original_field,
                label: mapping.custom_label || mapping.original_field
            }));
            console.log('Using valid field mappings:', columnsToShow);
        } else {
            console.log('No valid field mappings found, falling back to auto-detection');
            sortedMappings.length = 0; // Force fallback
        }
    }

    if (sortedMappings.length === 0) {
        const firstRow = data[0];
        const keys = Object.keys(firstRow);

        console.log('No field mappings found, using fallback with keys:', keys);

        const priorityFields = ['code', 'designation', 'emp_code', 'designation_id', 'product', 'price', 'name', 'id', 'description'];
        const foundFields = [];

        priorityFields.forEach(field => {
            if (keys.includes(field)) {
                foundFields.push({
                    key: field,
                    label: field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
                });
                console.log(`Added priority field: ${field}`);
            }
        });

        keys.forEach(key => {
            if (!priorityFields.includes(key)) {
                foundFields.push({
                    key: key,
                    label: key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
                });
                console.log(`Added remaining field: ${key}`);
            }
        });

        columnsToShow = foundFields;
        console.log('Final columns to show:', columnsToShow);
    }

    // Debug: Log final columns to show
    console.log('Final columnsToShow before rendering:', columnsToShow);

    // Add column headers
    html += `<th scope="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllRows" onchange="toggleAllRows(this)">
                    <label class="form-check-label" for="selectAllRows">All</label>
                </div>
             </th>`;

    columnsToShow.forEach(col => {
        html += `<th scope="col">${col.label}</th>`;
    });

    html += `
                                <th scope="col" style="width: 150px;">Scanner</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

    // Add data rows
    data.forEach((row, index) => {
        html += `<tr>
            <td>
                <div class="form-check">
                    <input class="form-check-input row-checkbox" type="checkbox" value="${index}" id="row_${index}" onchange="updateBulkButtons()">
                    <label class="form-check-label" for="row_${index}">${index + 1}</label>
                </div>
            </td>`;

        columnsToShow.forEach(col => {
            let value = row[col.key];

            console.log(`Processing column ${col.key}:`, value, typeof value);

            if (value === null || value === undefined) {
                value = '<span class="text-muted">N/A</span>';
            } else if (value === '') {
                value = '<span class="text-muted">Empty</span>';
            } else {
                const originalValue = value;
                value = String(value);

                // Format specific fields
                if (col.key === 'price' && originalValue !== null && originalValue !== undefined) {
                    const numValue = parseFloat(originalValue);
                    if (!isNaN(numValue)) {
                        value = numValue.toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            minimumFractionDigits: 2
                        });
                    }
                } else if (col.key === 'code' && originalValue !== null && originalValue !== undefined) {
                    value = `<code class="text-primary">${value}</code>`;
                } else {
                    // Truncate long text
                    if (value.length > 30) {
                        value = `<span title="${value}">${value.substring(0, 30)}...</span>`;
                    }
                }
            }

            html += `<td>${value}</td>`;
        });

        // Add scanner buttons based on Step 1 selection
        const selectedCodeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
        const codeTypeValues = Array.from(selectedCodeTypes).map(cb => cb.value);

        let scannerButtons = '';

        if (codeTypeValues.includes('both')) {
            // Show only "Both" button when user selected "both" option
            scannerButtons = `
                <button type="button" class="btn btn-outline-info btn-sm" onclick="generateRowBoth(${index})" title="Generate Both QR & Barcode">
                    <i class="fa fa-th"></i> Both
                </button>
            `;
        } else if (codeTypeValues.includes('qr') && codeTypeValues.includes('barcode')) {
            scannerButtons = `
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateRowQR(${index})" title="Generate QR Code">
                        <i class="fa fa-qrcode"></i> QR
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="generateRowBarcode(${index})" title="Generate Barcode">
                        <i class="fa fa-barcode"></i> Bar
                    </button>
                </div>
            `;
        } else if (codeTypeValues.includes('qr')) {
            // Show QR code option only
            scannerButtons = `
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateRowQR(${index})" title="Generate QR Code">
                    <i class="fa fa-qrcode"></i> QR Code
                </button>
            `;
        } else if (codeTypeValues.includes('barcode')) {
            // Show Barcode option only
            scannerButtons = `
                <button type="button" class="btn btn-outline-success btn-sm" onclick="generateRowBarcode(${index})" title="Generate Barcode">
                    <i class="fa fa-barcode"></i> Barcode
                </button>
            `;
        } else {
            scannerButtons = `
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateRowQR(${index})" title="Generate QR Code">
                    <i class="fa fa-qrcode"></i> QR Code
                </button>
            `;
        }

        html += `
            <td>
                ${scannerButtons}
            </td>
        </tr>`;
    });

    html += `
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Showing all ${data.length} rows. Select rows for Multi Download/Print</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div id="bulkActionButtons" class="d-none">
                            <button type="button" class="btn btn-primary btn-sm me-2" onclick="bulkDownload()">
                                <i class="fa fa-download me-1"></i>Download Selected (<span id="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="bulkPrint()">
                                <i class="fa fa-print me-1"></i>Print Selected (<span id="selectedCountPrint">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    console.log('Generated HTML length:', html.length);
    console.log('Columns to show:', columnsToShow);
    console.log('Container element:', container);
    console.log('Sample HTML preview:', html.substring(0, 500));
    console.log('Full HTML being set:', html);

    if (data.length > 0) {
        const testHtml = `
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-table me-2"></i>Table Data (${data.length} rows)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.slice(0, 10).map((row, index) => `
                                    <tr>
                                        <td><span class="badge bg-secondary">${index + 1}</span></td>
                                        <td><code class="text-primary">${row.code || 'N/A'}</code></td>
                                        <td>${row.product || 'N/A'}</td>
                                        <td>$${parseFloat(row.price || 0).toFixed(2)}</td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectRowForCode(${index})" title="Select for QR/Barcode">
                                                <i class="fa fa-qrcode"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                                ${data.length > 10 ? `<tr><td colspan="5" class="text-center text-muted">... and ${data.length - 10} more rows</td></tr>` : ''}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small><i class="fa fa-info-circle"></i> Showing first 10 of ${data.length} rows. Click on a row to select it for QR/Barcode generation</small>
                </div>
            </div>
        `;

        container.innerHTML = html;
        console.log('Full HTML applied successfully - showing all', data.length, 'rows');
    } else {
        container.innerHTML = html;
    }

    console.log('HTML updated in container');

    window.tableData = data;
}

function loadProcessDetails(processId) {
    const processInfo = processData[processId];
    const container = document.getElementById('processDetailsContainer');

    if (processInfo) {
        let html = `
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="fw-bold text-secondary mb-2">Field Mappings:</span>
                            <div class="field-mappings">
        `;

        if (processInfo.field_mappings && processInfo.field_mappings.length > 0) {
            // Sort by order number
            const sortedMappings = processInfo.field_mappings.sort((a, b) =>
                parseInt(a.order_number) - parseInt(b.order_number)
            );

            sortedMappings.forEach((mapping, index) => {
                html += `
                    <div class="mapping-item mb-2 p-2 border rounded bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <span class="badge bg-primary">${mapping.order_number}</span>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted">Original Field:</small><br>
                                <code>${mapping.original_field}</code>
                            </div>
                            <div class="col-md-1 text-center">
                                <i class="fa fa-arrow-right text-muted"></i>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted">Display Label:</small><br>
                                <strong>${mapping.custom_label}</strong>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    No field mappings configured for this process.
                </div>
            `;
        }

        html += `
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-white rounded border">
                        <small class="text-muted">
                            <i class="fa fa-info-circle me-1"></i>
                            This process will generate QR/Barcode containing data from the <strong>${processInfo.table}</strong> table
                            using the field mappings shown above. The generated code will include all mapped fields in the specified order.
                        </small>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;
    } else {
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle me-2"></i>
                Process information not found.
            </div>
        `;
    }
}

document.querySelectorAll('input[name="print_size"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const customFields = document.getElementById('customSizeFields');

        if (this.value === 'custom') {
            customFields.classList.remove('d-none');
        } else {
            customFields.classList.add('d-none');
        }

        console.log('Print size selected:', this.value);
        updateSizeInfo();
        updatePreview();

        const processSelect = document.getElementById('processSelect');
        const codeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');

        if (processSelect.value && codeTypes.length > 0) {
            console.log('Print size changed, regenerating code preview...');
            setTimeout(() => {
                const firstCodeType = codeTypes[0].value === 'both' ? 'qr' : codeTypes[0].value;
                generateCodePreview(firstCodeType, processSelect.value);
            }, 200);
        }
    });
});

// Custom size input listeners
document.addEventListener('DOMContentLoaded', function() {
    const customWidthInput = document.querySelector('input[name="custom_width"]');
    const customHeightInput = document.querySelector('input[name="custom_height"]');

    if (customWidthInput && customHeightInput) {
        [customWidthInput, customHeightInput].forEach(input => {
            input.addEventListener('input', function() {
                const customRadio = document.querySelector('input[name="print_size"][value="custom"]');
                if (customRadio && customRadio.checked) {
                    updateSizeInfo();
                    updatePreview();

                    const processSelect = document.getElementById('processSelect');
                    const codeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');

                    if (processSelect.value && codeTypes.length > 0 && this.value) {
                        console.log('Custom size changed, regenerating code preview...');
                        setTimeout(() => {
                            const firstCodeType = codeTypes[0].value === 'both' ? 'qr' : codeTypes[0].value;
                            generateCodePreview(firstCodeType, processSelect.value);
                        }, 500);
                    }
                }
            });
        });
    }
});

function calculateCodeSize(printSizeValue) {
    const customWidth = document.querySelector('input[name="custom_width"]')?.value;
    const customHeight = document.querySelector('input[name="custom_height"]')?.value;

    let dimensions = { width: 150, height: 150 }; // Default size

    switch(printSizeValue) {
        case 'small':
            dimensions = { width: 120, height: 120 };
            break;
        case 'medium':
            dimensions = { width: 180, height: 180 };
            break;
        case 'large':
            dimensions = { width: 240, height: 240 };
            break;
        case 'xlarge':
            dimensions = { width: 300, height: 300 };
            break;
        case 'custom':
            if (customWidth && customHeight) {
                const pixelRatio = 3.78;
                dimensions = {
                    width: Math.max(80, Math.min(500, parseInt(customWidth) * pixelRatio)),
                    height: Math.max(80, Math.min(500, parseInt(customHeight) * pixelRatio))
                };
            }
            break;
    }

    return dimensions;
}

function updateSizeInfo() {
    const printSize = document.querySelector('input[name="print_size"]:checked');
    const sizeInfo = document.getElementById('sizeInfo');

    if (!printSize || !sizeInfo) return;

    const qrSize = calculateCodeSize(printSize.value);
    const barcodeSize = calculateBarcodeSize(printSize.value);

    let sizeText = '';
    if (printSize.value === 'custom') {
        const width = document.querySelector('input[name="custom_width"]')?.value;
        const height = document.querySelector('input[name="custom_height"]')?.value;
        if (width && height) {
            sizeText = `Custom ${width}x${height}mm → QR: ${qrSize.width}x${qrSize.height}px | Barcode: ${barcodeSize.width}x${barcodeSize.height}px`;
        } else {
            sizeText = 'Enter custom dimensions to see calculated pixel sizes';
        }
    } else {
        const sizeLabels = {
            'small': '2x2 inches (50x50mm)',
            'medium': '3x3 inches (75x75mm)',
            'large': '4x4 inches (100x100mm)',
            'xlarge': '5x5 inches (125x125mm)'
        };
        sizeText = `${sizeLabels[printSize.value]} → QR: ${qrSize.width}x${qrSize.height}px | Barcode: ${barcodeSize.width}x${barcodeSize.height}px`;
    }

    sizeInfo.innerHTML = `<i class="fa fa-info-circle text-primary me-1"></i>${sizeText}`;
}

function calculateBarcodeSize(printSizeValue) {
    const customWidth = document.querySelector('input[name="custom_width"]')?.value;
    const customHeight = document.querySelector('input[name="custom_height"]')?.value;

    let dimensions = { width: 250, height: 80 }; // Default size

    switch(printSizeValue) {
        case 'small':
            dimensions = { width: 200, height: 60 };
            break;
        case 'medium':
            dimensions = { width: 280, height: 90 };
            break;
        case 'large':
            dimensions = { width: 350, height: 110 };
            break;
        case 'xlarge':
            dimensions = { width: 420, height: 130 };
            break;
        case 'custom':
            if (customWidth && customHeight) {
                const pixelRatio = 3.78;
                const width = Math.max(150, Math.min(600, parseInt(customWidth) * pixelRatio));
                const height = Math.max(50, Math.min(200, parseInt(customHeight) * pixelRatio * 0.6)); // Barcode height is typically 60% of width
                dimensions = { width, height };
            }
            break;
    }

    return dimensions;
}

function generateCodePreview(codeType, processId) {
    const previewContainer = document.getElementById('codePreview');
    const printSize = document.querySelector('input[name="print_size"]:checked');

    if (!printSize) {
        previewContainer.innerHTML = `
            <div class="placeholder-code">
                <i class="ti-qrcode fa-3x text-muted"></i>
                <p class="text-muted mt-2">Select print size to generate code</p>
            </div>
        `;
        return;
    }

    const codeSize = codeType === 'barcode' ? calculateBarcodeSize(printSize.value) : calculateCodeSize(printSize.value);

    // Show loading
    previewContainer.innerHTML = `
        <div class="text-center p-3">
            <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="text-muted mt-2">Generating ${codeType}...</p>
        </div>
    `;

    // Generate actual code data with complete field mapping information
    const processInfo = processData[processId];
    const timestamp = Date.now();

    const fieldMappings = [];
    if (processInfo.field_mappings && processInfo.field_mappings.length > 0) {
        processInfo.field_mappings.forEach(mapping => {
            fieldMappings.push({
                field: mapping.original_field,
                label: mapping.custom_label,
                order: mapping.order_number
            });
        });
    }

    const codeData = {
        process_id: processId,
        process_name: processInfo.process_name || document.getElementById('processSelect').options[document.getElementById('processSelect').selectedIndex].text,
        table: processInfo.table,
        field_mappings: fieldMappings,
        timestamp: timestamp,
        generated_at: new Date(timestamp).toISOString(),
        code_type: codeType
    };

    const dataString = JSON.stringify(codeData);
    generatedCodeData = dataString;

    document.getElementById('generatedCodeData').value = dataString;

    setTimeout(() => {
        if (codeType === 'qr') {
            console.log('QRCode library available:', typeof QRCode !== 'undefined');
            console.log('QRCode object:', QRCode);
            console.log('QRCode.toCanvas available:', typeof QRCode !== 'undefined' && typeof QRCode.toCanvas === 'function');
            console.log('QRCode.toDataURL available:', typeof QRCode !== 'undefined' && typeof QRCode.toDataURL === 'function');

            if (typeof QRCode === 'undefined') {
                console.log('QRCode library not available, using fallback');
                generateQRCodeFallback(previewContainer, dataString, codeData);
                return;
            }

            // Generate QR Code using qrcode.js with callback
            const canvas = document.createElement('canvas');
            canvas.style.maxWidth = '100%';
            canvas.style.height = 'auto';

            console.log('Generating QR code with data:', dataString);

            try {
                if (typeof QRCode === 'function') {
                    console.log('Using QRCode constructor method');

                    // Create a temporary div to hold the QR code
                    const tempDiv = document.createElement('div');
                    tempDiv.style.width = '150px';
                    tempDiv.style.height = '150px';
                    document.body.appendChild(tempDiv);

                    try {
                        // Create QR code instance
                        const qr = new QRCode(tempDiv, {
                            text: dataString,
                            width: codeSize.width,
                            height: codeSize.height,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
                        });

                        setTimeout(() => {
                            try {
                                const qrCanvas = tempDiv.querySelector('canvas');
                                const qrImg = tempDiv.querySelector('img');

                                if (qrCanvas) {
                                    console.log('QR Code generated successfully with constructor (canvas)');

                                    // Clone the canvas
                                    const canvas = document.createElement('canvas');
                                    canvas.width = 150;
                                    canvas.height = 150;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(qrCanvas, 0, 0);

                                    previewContainer.innerHTML = `
                                        <div class="qr-code-preview text-center">
                                            <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                                            <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                                            <small class="text-muted d-block">Table: ${codeData.table}</small>
                                            <small class="text-muted d-block">Generated: ${new Date(timestamp).toLocaleString()}</small>
                                        </div>
                                    `;
                                    previewContainer.querySelector('.code-container').appendChild(canvas);

                                    const imageData = canvas.toDataURL('image/png');
                                    document.getElementById('generatedImageData').value = imageData;
                                    console.log('QR Image data stored successfully, length:', imageData.length);
                                    console.log('Image data preview:', imageData.substring(0, 50));

                                } else if (qrImg && qrImg.src && qrImg.src.startsWith('data:')) {
                                    console.log('QR Code generated successfully with constructor (image)');

                                    const img = document.createElement('img');
                                    img.src = qrImg.src;
                                    img.style.maxWidth = '100%';
                                    img.style.height = 'auto';
                                    img.style.border = '1px solid #dee2e6';
                                    img.style.borderRadius = '8px';
                                    img.style.background = 'white';
                                    img.style.padding = '10px';

                                    previewContainer.innerHTML = `
                                        <div class="qr-code-preview text-center">
                                            <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                                            <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                                            <small class="text-muted d-block">Table: ${codeData.table}</small>
                                            <small class="text-muted d-block">Generated: ${new Date(timestamp).toLocaleString()}</small>
                                        </div>
                                    `;
                                    previewContainer.querySelector('.code-container').appendChild(img);

                                    // Store the data URL directly
                                    document.getElementById('generatedImageData').value = qrImg.src;
                                    console.log('QR Image data stored successfully, length:', qrImg.src.length);
                                    console.log('Image data preview:', qrImg.src.substring(0, 50));

                                } else {
                                    console.log('QR Code generated but no canvas/image found, using fallback');
                                    generateQRCodeFallback(previewContainer, dataString, codeData);
                                }

                                // Clean up temp div
                                document.body.removeChild(tempDiv);

                            } catch (error) {
                                console.error('Error processing QR code result:', error);
                                document.body.removeChild(tempDiv);
                                generateQRCodeFallback(previewContainer, dataString, codeData);
                            }
                        }, 500);

                    } catch (error) {
                        console.error('QRCode constructor error:', error);
                        document.body.removeChild(tempDiv);
                        generateQRCodeFallback(previewContainer, dataString, codeData);
                    }

                } else if (QRCode.toCanvas && typeof QRCode.toCanvas === 'function') {
                    console.log('Using QRCode.toCanvas method');
                    QRCode.toCanvas(canvas, dataString, {
                        width: codeSize.width,
                        height: codeSize.height,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function (error) {
                        if (error) {
                            console.error('QR Code toCanvas error:', error);
                            generateQRCodeFallback(previewContainer, dataString, codeData);
                        } else {
                            console.log('QR Code generated successfully with toCanvas');
                            previewContainer.innerHTML = `
                                <div class="qr-code-preview text-center">
                                    <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                                    <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                                    <small class="text-muted d-block">Table: ${codeData.table}</small>
                                    <small class="text-muted d-block">Generated: ${new Date(timestamp).toLocaleString()}</small>
                                </div>
                            `;
                            previewContainer.querySelector('.code-container').appendChild(canvas);

                            const imageData = canvas.toDataURL('image/png');
                            document.getElementById('generatedImageData').value = imageData;

                        }
                    });
                } else if (QRCode.toDataURL) {
                    QRCode.toDataURL(dataString, {
                        width: codeSize.width,
                        height: codeSize.height,
                        margin: 2
                    }, function (error, url) {
                        if (error) {
                            console.error('QR Code toDataURL error:', error);
                            generateQRCodeFallback(previewContainer, dataString, codeData);
                        } else {
                            const img = document.createElement('img');
                            img.src = url;
                            img.style.maxWidth = '100%';
                            img.style.height = 'auto';
                            img.style.border = '1px solid #dee2e6';
                            img.style.borderRadius = '8px';
                            img.style.background = 'white';
                            img.style.padding = '10px';

                            previewContainer.innerHTML = `
                                <div class="qr-code-preview text-center">
                                    <div class="code-container mb-2" style="display: flex; justify-content: center;"></div>
                                    <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                                    <small class="text-muted d-block">Table: ${codeData.table}</small>
                                    <small class="text-muted d-block">Generated: ${new Date(timestamp).toLocaleString()}</small>
                                </div>
                            `;
                            previewContainer.querySelector('.code-container').appendChild(img);
                            document.getElementById('generatedImageData').value = url;

                        }
                    });
                } else {
                    generateQRCodeFallback(previewContainer, dataString, codeData);
                }
            } catch (error) {
                console.error('QR Code generation exception:', error);
                generateQRCodeFallback(previewContainer, dataString, codeData);
            }
        } else {
            if (typeof JsBarcode === 'undefined') {
                console.error('JsBarcode library not available');
                generateBarcodeFromData(previewContainer, dataString, codeData);
                return;
            }

            try {
                const processName = processInfo.process_name || 'PROC';
                const tableShort = processInfo.table.substring(0, 4).toUpperCase();
                const timeShort = timestamp.toString().slice(-6);
                const barcodeData = `${processName.substring(0, 4).toUpperCase()}-${tableShort}-${timeShort}`;

                const canvas = document.createElement('canvas');
                canvas.width = codeSize.width;
                canvas.height = codeSize.height;

                // Calculate dynamic barcode parameters based on size
                const barcodeWidth = Math.max(1, codeSize.width / 200); // Scale width based on canvas size
                const barcodeHeight = Math.max(30, codeSize.height * 0.6); // Height is 60% of canvas height
                const fontSize = Math.max(8, Math.min(14, codeSize.width / 25)); // Scale font size

                // Generate barcode with error handling
                try {
                    JsBarcode(canvas, barcodeData, {
                        format: "CODE128",
                        width: barcodeWidth,
                        height: barcodeHeight,
                        displayValue: true,
                        fontSize: fontSize,
                        textMargin: 5,
                        margin: 10,
                        background: '#FFFFFF',
                        lineColor: '#000000'
                    });

                    previewContainer.innerHTML = `
                        <div class="barcode-preview text-center">
                            <div class="code-container mb-2" style="display: flex; justify-content: center; overflow: hidden;"></div>
                            <small class="text-muted d-block">Process: ${codeData.process_name}</small>
                            <small class="text-muted d-block">Table: ${codeData.table}</small>
                            <small class="text-muted d-block">Code: ${barcodeData}</small>
                            <small class="text-muted d-block">Generated: ${new Date(timestamp).toLocaleString()}</small>
                        </div>
                    `;
                    previewContainer.querySelector('.code-container').appendChild(canvas);

                    try {
                        const imageData = canvas.toDataURL('image/png');
                        document.getElementById('generatedImageData').value = imageData;

                    } catch (canvasError) {
                        console.error('Canvas toDataURL error for barcode:', canvasError);
                        const indicator = document.createElement('div');
                        indicator.className = 'alert alert-warning mt-2';
                        indicator.innerHTML = '<i class="fa fa-exclamation-triangle"></i> Barcode generated but image data could not be captured';
                        previewContainer.appendChild(indicator);
                    }

                } catch (barcodeError) {
                    console.error('JsBarcode generation error:', barcodeError);
                    generateBarcodeFromData(previewContainer, dataString, codeData);
                }

            } catch (error) {
                console.error('Barcode generation error:', error);
                generateBarcodeFromData(previewContainer, dataString, codeData);
            }
        }
    }, 1000);
}
document.getElementById('nextBtn').onclick = () => {
    if (!validateStep(currentStep)) return;

    currentStep++;
    showStep(currentStep);
};

document.getElementById('prevBtn').onclick = () => {
    currentStep = Math.max(1, currentStep - 1);
    showStep(currentStep);
};

function updatePreview() {
    let html = '<div class="list-group list-group-flush">';
    let hasData = false;

    // Code types
    const codeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
    if (codeTypes.length > 0) {
        hasData = true;
        const typeLabels = Array.from(codeTypes).map(ct => {
            if (ct.value === 'qr') return '📱 QR Code';
            if (ct.value === 'barcode') return '📊 Barcode';
            if (ct.value === 'both') return '🔄 Both';
            return ct.value;
        });
        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Code Types</span>
                    <span class="badge bg-primary text-white px-3 py-2">${typeLabels.join(', ')}</span>
                 </div>`;
    }

    // Table selection
    const tableSelect = document.getElementById('tableSelect');
    if (tableSelect.value) {
        hasData = true;
        const tableName = tableSelect.options[tableSelect.selectedIndex].text;
        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">🗃️ Table</span>
                    <span class="badge bg-secondary text-white px-3 py-2">${tableName}</span>
                 </div>`;
    }

    // Process selection
    const processSelect = document.getElementById('processSelect');
    if (processSelect.value) {
        hasData = true;
        const processName = processSelect.options[processSelect.selectedIndex].text;
        const processInfo = processData[processSelect.value];

        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">⚙️ Process</span>
                    <span class="badge bg-info text-white px-3 py-2">${processName}</span>
                 </div>`;
        if (processInfo && processInfo.field_mappings && processInfo.field_mappings.length > 0) {
            html += `<div class="list-group-item">
                        <span class="fw-bold">📋 Field Mappings</span>
                        <div class="mt-2">`;

            const sortedMappings = processInfo.field_mappings.sort((a, b) =>
                parseInt(a.order_number) - parseInt(b.order_number)
            );

            sortedMappings.slice(0, 3).forEach((mapping, index) => {
                html += `<small class="d-block text-muted">
                            ${mapping.order_number}. ${mapping.custom_label} (${mapping.original_field})
                         </small>`;
            });

            if (sortedMappings.length > 3) {
                html += `<small class="d-block text-muted">
                            ... and ${sortedMappings.length - 3} more fields
                         </small>`;
            }

            html += `</div></div>`;
        }
    }
    const printSize = document.querySelector('input[name="print_size"]:checked');
    if (printSize) {
        hasData = true;
        let sizeLabel = printSize.nextElementSibling.textContent;
        let pixelInfo = '';

        if (printSize.value === 'custom') {
            const width = document.querySelector('input[name="custom_width"]').value;
            const height = document.querySelector('input[name="custom_height"]').value;
            if (width && height) {
                sizeLabel = `Custom: ${width}x${height}mm`;
                const pixelWidth = Math.round(parseInt(width) * 3.78);
                const pixelHeight = Math.round(parseInt(height) * 3.78);
                pixelInfo = `<small class="d-block">≈ ${pixelWidth}x${pixelHeight}px</small>`;
            }
        } else {
            const qrSize = calculateCodeSize(printSize.value);
            const barcodeSize = calculateBarcodeSize(printSize.value);
            pixelInfo = `<small class="d-block">QR: ${qrSize.width}x${qrSize.height}px | Barcode: ${barcodeSize.width}x${barcodeSize.height}px</small>`;
        }

        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">📏 Print Size</span>
                    <span class="badge bg-success text-white px-3 py-2">${sizeLabel}${pixelInfo}</span>
                 </div>`;
    }

    html += '</div>';

    document.getElementById('previewCard').innerHTML = hasData ? html : '<p class="text-muted">Select options to see preview...</p>';
}
function downloadCode() {
    const codeType = document.querySelector('input[name="code_type"]:checked')?.value;
    const processId = document.getElementById('processSelect').value;
    const printSize = document.querySelector('input[name="print_size"]:checked')?.value;

    if (!codeType || !processId || !printSize || !generatedCodeData) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Setup',
            text: 'Please complete all steps before downloading',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    let canvasSize = { width: 300, height: 300 };
    switch(printSize) {
        case 'small': canvasSize = { width: 200, height: 200 }; break;
        case 'medium': canvasSize = { width: 300, height: 300 }; break;
        case 'large': canvasSize = { width: 400, height: 400 }; break;
        case 'xlarge': canvasSize = { width: 500, height: 500 }; break;
        case 'custom':
            const width = document.querySelector('input[name="custom_width"]').value;
            const height = document.querySelector('input[name="custom_height"]').value;
            canvasSize = { width: parseInt(width) * 4, height: parseInt(height) * 4 };
            break;
    }

    canvas.width = canvasSize.width;
    canvas.height = canvasSize.height;

    if (codeType === 'qr') {
        if (typeof QRCode === 'function') {
            const tempDiv = document.createElement('div');
            tempDiv.style.width = canvasSize.width + 'px';
            tempDiv.style.height = canvasSize.height + 'px';
            document.body.appendChild(tempDiv);

            try {
                const qr = new QRCode(tempDiv, {
                    text: generatedCodeData,
                    width: canvasSize.width,
                    height: canvasSize.height,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
                });

                setTimeout(() => {
                    const qrCanvas = tempDiv.querySelector('canvas');
                    const qrImg = tempDiv.querySelector('img');

                    if (qrCanvas) {
                        downloadCanvasAsImage(qrCanvas, `qr-code-${processId}-${Date.now()}.png`);
                    } else if (qrImg && qrImg.src && qrImg.src.startsWith('data:')) {
                        const link = document.createElement('a');
                        link.download = `qr-code-${processId}-${Date.now()}.png`;
                        link.href = qrImg.src;
                        link.click();
                    }

                    document.body.removeChild(tempDiv);
                }, 500);

            } catch (error) {
                console.error('QR download generation error:', error);
                document.body.removeChild(tempDiv);
            }
        }
    } else {
        const processInfo = processData[processId];
        const processName = processInfo.process_name || 'PROC';
        const tableShort = processInfo.table.substring(0, 4).toUpperCase();
        const timeShort = Date.now().toString().slice(-6);
        const barcodeData = `${processName.substring(0, 4).toUpperCase()}-${tableShort}-${timeShort}`;
        try {
            canvas.width = canvasSize.width;
            canvas.height = Math.max(100, canvasSize.height / 3);

            JsBarcode(canvas, barcodeData, {
                format: "CODE128",
                width: Math.max(2, canvasSize.width / 150),
                height: canvas.height * 0.6,
                displayValue: true,
                fontSize: Math.max(12, canvasSize.width / 25),
                margin: 20,
                background: '#FFFFFF',
                lineColor: '#000000'
            });

            downloadCanvasAsImage(canvas, `barcode-${processId}-${Date.now()}.png`);
        } catch (error) {
            console.error('Barcode download generation error:', error);
        }
    }
}

function downloadCanvasAsImage(canvas, filename) {
    const link = document.createElement('a');
    link.download = filename;
    link.href = canvas.toDataURL();
    link.click();
}

function downloadSavedImage() {
    const savedFilename = document.getElementById('savedImageFilename').value;

    if (savedFilename) {
        const link = document.createElement('a');
        link.download = savedFilename;
        link.href = 'uploads/scanner_codes/' + savedFilename;
        link.click();
    } else {

        downloadCode();
    }
}

// Form submission
document.getElementById('qrForm').addEventListener('submit', function(e) {

    if (!validateStep(4)) {
        e.preventDefault();
        return false;
    }
    setTimeout(() => {
        downloadSavedImage();
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Code saved successfully and download started',
            confirmButtonColor: '#28a745',
            timer: 2000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
    }, 1000);
});

function validateFinalSubmission() {
    if (!validateStep(4)) {
        return false;
    }

    if (!generatedCodeData) {
        Swal.fire({
            icon: 'error',
            title: 'Code Not Generated',
            text: 'Please generate the code first by selecting all options',
            confirmButtonColor: '#007bff'
        });
        return false;
    }

    const imageData = document.getElementById('generatedImageData').value;

    if (!imageData) {
        Swal.fire({
            icon: 'error',
            title: 'Image Data Missing',
            text: 'No image data found. Please regenerate the code.',
            confirmButtonColor: '#007bff'
        });
        return false;
    }

    document.getElementById('generatedCodeData').value = generatedCodeData;
    return true;
}

function checkLibraries() {
    if (typeof QRCode === 'undefined') {
        console.error('QRCode library not loaded - will use fallback');
    } else {
        console.log('QRCode library loaded successfully');
        console.log('QRCode methods:', Object.getOwnPropertyNames(QRCode));
    }

    if (typeof JsBarcode === 'undefined') {
        console.error('JsBarcode library not loaded');
        return false;
    }

    return true;
}

function checkImageData() {
    const imageDataField = document.getElementById('generatedImageData');
    const codeDataField = document.getElementById('generatedCodeData');
    const imageData = imageDataField ? imageDataField.value : null;
    const codeData = codeDataField ? codeDataField.value : null;
    if (imageData) {
        Swal.fire({
            icon: 'success',
            title: 'Image Data Found!',
            html: `
                <div class="text-left">
                    <p><strong>Length:</strong> ${imageData.length} characters</p>
                    <p><strong>Code Data Length:</strong> ${codeData ? codeData.length : 'Not found'} characters</p>
                    <p><strong>Preview:</strong></p>
                    <code style="word-break: break-all; font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px; display: block;">${imageData.substring(0, 100)}...</code>
                </div>
            `,
            confirmButtonColor: '#28a745',
            width: '600px'
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'No Image Data Found',
            text: 'Please regenerate the code to capture image data.',
            confirmButtonColor: '#dc3545'
        });
    }
}

function generateRowQR(rowIndex) {
    if (!window.tableData || !window.tableData[rowIndex]) {
        Swal.fire({
            icon: 'error',
            title: 'Row Not Found',
            text: 'Unable to find row data. Please refresh the table.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const rowData = window.tableData[rowIndex];
    const processId = document.getElementById('processSelect').value;
    const processInfo = processData[processId];

    const qrData = {
        row_index: rowIndex + 1,
        process: processInfo.process_name,
        table: processInfo.table,
        data: rowData,
        generated_at: new Date().toISOString(),
        type: 'QR'
    };

    const dataString = JSON.stringify(qrData);

    // Show modal with QR code
    showScannerModal('QR Code', dataString, 'qr', rowData);
}

function generateRowBarcode(rowIndex) {
    if (!window.tableData || !window.tableData[rowIndex]) {
        Swal.fire({
            icon: 'error',
            title: 'Row Not Found',
            text: 'Unable to find row data. Please refresh the table.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const rowData = window.tableData[rowIndex];
    const processId = document.getElementById('processSelect').value;
    const processInfo = processData[processId];

    const barcodeData = rowData.code || rowData.id || rowData.product_code || `ROW${rowIndex + 1}${Date.now().toString().slice(-4)}`;

    // Show modal with Barcode
    showScannerModal('Barcode', barcodeData, 'barcode', rowData);
}

function generateRowBoth(rowIndex) {
    if (!window.tableData || !window.tableData[rowIndex]) {
        Swal.fire({
            icon: 'error',
            title: 'Row Not Found',
            text: 'Unable to find row data. Please refresh the table.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const rowData = window.tableData[rowIndex];
    const processId = document.getElementById('processSelect').value;
    const processInfo = processData[processId];

    const qrData = {
        row_index: rowIndex + 1,
        process: processInfo.process_name,
        table: processInfo.table,
        data: rowData,
        generated_at: new Date().toISOString(),
        type: 'QR'
    };

    const barcodeData = rowData.code || rowData.id || rowData.product_code || `ROW${rowIndex + 1}${Date.now().toString().slice(-4)}`;

    // Show modal with both codes
    showScannerModal('QR Code & Barcode', JSON.stringify(qrData), 'both', rowData, barcodeData);
}

function showScannerModal(title, data, type, rowData, barcodeData = null) {
    let modalContent = `
        <div class="container-fluid mt3">
            <div class="row g-3">
                <!-- Row Data Section -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="fa fa-table me-2"></i>Row Data</h6>
                        </div>
                        <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <tbody>
    `;

    // Show row data in table format
    Object.entries(rowData).forEach(([key, value]) => {
        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        modalContent += `
            <tr>
                <th scope="col" class="fw-bold text-secondary" style="width: 40%;">${label}:</th>
                <th scope="col" class="text-break">${value || '<span class="text-muted">N/A</span>'}</th>
            </tr>
        `;
    });

    modalContent += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scanner Code Section -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body p-3">
                            <div id="scannerCodeContainer" class="text-center p-4 border rounded bg-light" style="min-height: 250px;">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mb-0">Generating ${title.toLowerCase()}...</p>
                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-md-8 offset-md-2">
                                    <div class="card border-warning">
                                        <div class="card-body p-3">
                                            <span class="form-label fw-bold text-warning">
                                                <i class="fa fa-copy me-1"></i>Quantity to Generate
                                            </span>
                                            <div class="input-group">
                                                <input aria-label="Enter quantity" type="number" class="form-control" id="codeQuantity"
                                                       value="1" min="1" max="100" placeholder="Enter quantity"
                                                       onchange="updateQuantityText()">
                                                <span class="input-group-text">copies</span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle me-1"></i>
                                                Generate multiple copies with the same data (Max: 100)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <button type="button" class="btn btn-primary" onclick="downloadGeneratedCodeWithQuantity()">
                                    <i class="fa fa-download me-1"></i> Download <span id="downloadQuantityText">1 Copy</span>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info" onclick="printGeneratedCodeWithQuantity()">
                                        <i class="fa fa-print me-1"></i> Print <span id="printQuantityText">1 Copy</span>
                                    </button>
                                    <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="printWithDetailsOption(); return false;" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <i class="fa fa-list me-2"></i>Choose Print Options
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="printWithDetailsOption(false); return false;" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <i class="fa fa-qrcode me-2"></i>Quick Print (Codes Only)
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: `<i class="fa fa-qrcode me-2"></i>${title} Generator`,
        html: modalContent,
        width: '95%',
        maxWidth: '1200px',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'swal-wide-popup',
            title: 'swal-title-custom'
        },
        didOpen: () => {
            // Add custom styles
            const style = document.createElement('style');
            style.textContent = `
                .swal-wide-popup {
                    border-radius: 10px !important;
                }
                .swal-title-custom {
                    font-size: 1.25rem !important;
                    color: #495057 !important;
                }
                .swal2-html-container {
                    padding: 0 !important;
                    margin: 0 !important;
                }
            `;
            document.head.appendChild(style);

            window.currentModalData = { data, type, barcodeData };

            // Generate the actual code after modal opens
            setTimeout(() => {
                generateCodeInModal(data, type, barcodeData);
            }, 500);
        }
    });
}

function generateCodeInModal(data, type, barcodeData = null) {
    const container = document.getElementById('scannerCodeContainer');

    if (type === 'qr') {
        generateQRInContainer(container, data);
    } else if (type === 'barcode') {
        generateBarcodeInContainer(container, data);
    } else if (type === 'both') {
        generateBothInContainer(container, data, barcodeData);
    }
}

function generateQRInContainer(container, data) {
    try {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 200, height: 200 };

        if (typeof QRCode !== 'undefined' && typeof QRCode === 'function') {
            const tempDiv = document.createElement('div');
            tempDiv.style.width = qrSize.width + 'px';
            tempDiv.style.height = qrSize.height + 'px';
            container.appendChild(tempDiv);

            const qr = new QRCode(tempDiv, {
                text: data,
                width: qrSize.width,
                height: qrSize.height,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
            });

            container.innerHTML = '';
            container.appendChild(tempDiv);

            window.generatedModalCode = { type: 'qr', element: tempDiv };

        } else {
            // Fallback QR generation
            const canvas = document.createElement('canvas');
            canvas.width = qrSize.width;
            canvas.height = qrSize.height;
            const ctx = canvas.getContext('2d');

            // Simple QR-like pattern
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#000000';

            // Calculate module size based on canvas dimensions
            const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));

            // Draw QR-like pattern
            for (let x = 0; x < canvas.width; x += moduleSize) {
                for (let y = 0; y < canvas.height; y += moduleSize) {
                    if ((x + y) % (moduleSize * 2) === 0) {
                        ctx.fillRect(x, y, moduleSize, moduleSize);
                    }
                }
            }

            const cornerSize = Math.max(20, canvas.width * 0.2);
            ctx.fillRect(0, 0, cornerSize, cornerSize);
            ctx.fillRect(canvas.width - cornerSize, 0, cornerSize, cornerSize);
            ctx.fillRect(0, 160, 40, 40);

            container.innerHTML = '';
            container.appendChild(canvas);

            window.generatedModalCode = { type: 'qr', element: canvas };
        }
    } catch (error) {
        console.error('QR generation error:', error);
        container.innerHTML = '<div class="alert alert-danger">Error generating QR code</div>';
    }
}

function generateBarcodeInContainer(container, data) {
    try {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 300, height: 100 };

        if (typeof JsBarcode !== 'undefined') {
            const canvas = document.createElement('canvas');
            canvas.width = barcodeSize.width;
            canvas.height = barcodeSize.height;

            // Calculate dynamic barcode parameters
            const barcodeWidth = Math.max(1, barcodeSize.width / 200);
            const barcodeHeight = Math.max(30, barcodeSize.height * 0.6);
            const fontSize = Math.max(8, Math.min(14, barcodeSize.width / 25));

            JsBarcode(canvas, data, {
                format: "CODE128",
                width: barcodeWidth,
                height: barcodeHeight,
                displayValue: true,
                fontSize: fontSize,
                margin: 10,
                background: '#FFFFFF',
                lineColor: '#000000'
            });

            container.innerHTML = '';
            container.appendChild(canvas);

            window.generatedModalCode = { type: 'barcode', element: canvas };

        } else {
            // Fallback barcode
            const canvas = document.createElement('canvas');
            canvas.width = barcodeSize.width;
            canvas.height = barcodeSize.height;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#000000';

            // Calculate dynamic barcode parameters
            const barWidth = Math.max(1, canvas.width / 150);
            const spacing = Math.max(1, barWidth * 0.8);
            const margin = canvas.width * 0.05;
            let x = margin;

            const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
            const maxBarHeight = canvas.height * 0.6;
            const minBarHeight = canvas.height * 0.3;

            // Simple barcode pattern
            for (let i = 0; i < barsCount; i++) {
                const height = minBarHeight + (i % (maxBarHeight - minBarHeight));
                if (i % 2 === 0) {
                    ctx.fillRect(x, margin, barWidth, height);
                }
                x += barWidth + spacing;
            }

            // Add text with dynamic font size
            const fontSize = Math.max(8, Math.min(12, canvas.width / 25));
            ctx.font = `${fontSize}px Arial`;
            ctx.textAlign = 'center';
            ctx.fillText(data, canvas.width / 2, canvas.height - margin);

            container.innerHTML = '';
            container.appendChild(canvas);

            window.generatedModalCode = { type: 'barcode', element: canvas };
        }
    } catch (error) {
        console.error('Barcode generation error:', error);
        container.innerHTML = '<div class="alert alert-danger">Error generating barcode</div>';
    }
}

function generateBothInContainer(container, qrData, barcodeData) {
    container.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white text-center py-2">
                        <h6 class="mb-0"><i class="fa fa-qrcode me-1"></i>QR Code</h6>
                    </div>
                    <div class="card-body p-2">
                        <div id="qrContainer" class="text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white text-center py-2">
                        <h6 class="mb-0"><i class="fa fa-barcode me-1"></i>Barcode</h6>
                    </div>
                    <div class="card-body p-2">
                        <div id="barcodeContainer" class="text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;"></div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Generate both codes
    setTimeout(() => {
        const qrContainer = document.getElementById('qrContainer');
        const barcodeContainer = document.getElementById('barcodeContainer');

        if (qrContainer && barcodeContainer) {
            generateQRInContainer(qrContainer, qrData);
            generateBarcodeInContainer(barcodeContainer, barcodeData);

            window.generatedModalCode = {
                type: 'both',
                qrContainer: qrContainer,
                barcodeContainer: barcodeContainer,
                qrData: qrData,
                barcodeData: barcodeData
            };
        }
    }, 100);
}

function downloadGeneratedCode() {
    if (!window.generatedModalCode) {
        Swal.fire({
            icon: 'warning',
            title: 'No Code Generated',
            text: 'Please generate a code first.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const { type } = window.generatedModalCode;

    if (type === 'both') {
        const { qrContainer, barcodeContainer } = window.generatedModalCode;

        // Download QR Code
        const qrCanvas = qrContainer.querySelector('canvas');
        const qrImg = qrContainer.querySelector('img');
        if (qrCanvas) {
            const link1 = document.createElement('a');
            link1.download = `qr-code-${Date.now()}.png`;
            link1.href = qrCanvas.toDataURL();
            link1.click();
        } else if (qrImg && qrImg.src.startsWith('data:')) {
            const link1 = document.createElement('a');
            link1.download = `qr-code-${Date.now()}.png`;
            link1.href = qrImg.src;
            link1.click();
        }

        setTimeout(() => {
            const barcodeCanvas = barcodeContainer.querySelector('canvas');
            const barcodeImg = barcodeContainer.querySelector('img');
            if (barcodeCanvas) {
                const link2 = document.createElement('a');
                link2.download = `barcode-${Date.now()}.png`;
                link2.href = barcodeCanvas.toDataURL();
                link2.click();
            } else if (barcodeImg && barcodeImg.src.startsWith('data:')) {
                const link2 = document.createElement('a');
                link2.download = `barcode-${Date.now()}.png`;
                link2.href = barcodeImg.src;
                link2.click();
            }
        }, 500);

        Swal.fire({
            icon: 'success',
            title: 'Download Started',
            text: 'Both QR Code and Barcode downloads have started.',
            timer: 2000,
            showConfirmButton: false
        });

    } else {
        // Handle single code download
        const { element } = window.generatedModalCode;

        if (element && element.tagName === 'CANVAS') {
            const link = document.createElement('a');
            link.download = `${type}-code-${Date.now()}.png`;
            link.href = element.toDataURL();
            link.click();
        } else if (element) {
            const canvas = element.querySelector('canvas');
            const img = element.querySelector('img');

            if (canvas) {
                const link = document.createElement('a');
                link.download = `${type}-code-${Date.now()}.png`;
                link.href = canvas.toDataURL();
                link.click();
            } else if (img && img.src.startsWith('data:')) {
                const link = document.createElement('a');
                link.download = `${type}-code-${Date.now()}.png`;
                link.href = img.src;
                link.click();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Unable to download the generated code.',
                    confirmButtonColor: '#007bff'
                });
            }
        }
    }
}

function printGeneratedCode() {
    if (!window.generatedModalCode) {
        Swal.fire({
            icon: 'warning',
            title: 'No Code Generated',
            text: 'Please generate a code first.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const { type } = window.generatedModalCode;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html lang="en">
            <head>
                <title>Print Scanner Code</title>
                <style>
                    body {
                        margin: 0;
                        padding: 20px;
                        text-align: center;
                        font-family: Arial, sans-serif;
                    }
                    canvas, img {
                        width: 100%;
                        height: auto;
                        margin: 0px;
                        border: 1px solid #ddd;
                        padding: 0px;
                    }
                    .code-container {
                        display: inline-block;
                        margin: 20px;
                        text-align: center;
                    }
                    h3 {
                        color: #333;
                        margin-bottom: 10px;
                    }
                </style>
            </head>
            <body>
                <h2>Scanner Code Print</h2>
    `);

    if (type === 'both') {
        const { qrContainer, barcodeContainer } = window.generatedModalCode;

        printWindow.document.write('<div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">');

        // Add QR Code
        const qrCanvas = qrContainer.querySelector('canvas');
        const qrImg = qrContainer.querySelector('img');
        if (qrCanvas) {
            const qrImgData = qrCanvas.toDataURL();
            printWindow.document.write(`
                <div class="code-container">
                    <h3>QR Code</h3>
                    <img src="${qrImgData}"  alt="QR Code" />
                </div>
            `);
        } else if (qrImg) {
            printWindow.document.write(`
                <div class="code-container">
                    <h3>QR Code</h3>
                    <img src="${qrImg.src}"  alt="QR Code" />
                </div>
            `);
        }

        // Add Barcode
        const barcodeCanvas = barcodeContainer.querySelector('canvas');
        const barcodeImg = barcodeContainer.querySelector('img');
        if (barcodeCanvas) {
            const barcodeImgData = barcodeCanvas.toDataURL();
            printWindow.document.write(`
                <div class="code-container">
                    <h3>Barcode</h3>
                    <img src="${barcodeImgData}"  alt="Preview" />
                </div>
            `);
        } else if (barcodeImg) {
            printWindow.document.write(`
                <div class="code-container">
                    <h3>Barcode</h3>
                    <img src="${barcodeImg.src}"  alt="Preview" />
                </div>
            `);
        }

        printWindow.document.write('</div>');

    } else {
        const { element } = window.generatedModalCode;

        if (element && element.tagName === 'CANVAS') {
            const imgData = element.toDataURL();
            printWindow.document.write(`
                <div class="code-container">
                    <h3>${type.toUpperCase()} Code</h3>
                    <img src="${imgData}"  alt="Preview" />
                </div>
            `);
        } else if (element) {
            const canvas = element.querySelector('canvas');
            const img = element.querySelector('img');

            if (canvas) {
                const imgData = canvas.toDataURL();
                printWindow.document.write(`
                    <div class="code-container">
                        <h3>${type.toUpperCase()} Code</h3>
                        <img src="${imgData}"  alt="Preview" />
                    </div>
                `);
            } else if (img) {
                printWindow.document.write(`
                    <div class="code-container">
                        <h3>${type.toUpperCase()} Code</h3>
                        <img src="${img.src}"  alt="Preview" />
                    </div>
                `);
            }
        }
    }

    printWindow.document.write(`
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Code data copied to clipboard.',
            timer: 1500,
            showConfirmButton: false
        });
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Copy Failed',
            text: 'Unable to copy to clipboard. Please copy manually.',
            confirmButtonColor: '#007bff'
        });
    }

    document.body.removeChild(textArea);
}

function updateQuantityText() {
    const quantity = document.getElementById('codeQuantity').value || 1;
    const downloadText = document.getElementById('downloadQuantityText');
    const printText = document.getElementById('printQuantityText');

    if (downloadText) {
        downloadText.textContent = quantity == 1 ? '1 Copy' : `${quantity} Copies`;
    }
    if (printText) {
        printText.textContent = quantity == 1 ? '1 Copy' : `${quantity} Copies`;
    }
}

function downloadGeneratedCodeWithQuantity() {
    if (!window.generatedModalCode || !window.currentModalData) {
        Swal.fire({
            icon: 'warning',
            title: 'No Code Generated',
            text: 'Please generate a code first.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const quantity = parseInt(document.getElementById('codeQuantity').value) || 1;
    const { type } = window.generatedModalCode;
    const { data, barcodeData } = window.currentModalData;

    if (quantity < 1 || quantity > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Quantity',
            text: 'Please enter a quantity between 1 and 100.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    if (quantity > 1) {
        Swal.fire({
            title: 'Generating Multiple Copies',
            html: `
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 0%" id="quantityDownloadProgress"></div>
                </div>
                <p id="quantityDownloadStatus">Preparing ${quantity} copies for download...</p>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                processQuantityDownload(quantity, type, data, barcodeData);
            }
        });
    } else {
        downloadGeneratedCode();
    }
}

function processQuantityDownload(quantity, type, data, barcodeData) {
    const progressBar = document.getElementById('quantityDownloadProgress');
    const statusText = document.getElementById('quantityDownloadStatus');
    let completed = 0;

    const downloadPromises = [];

    for (let i = 0; i < quantity; i++) {
        const promise = new Promise((resolve) => {
            setTimeout(() => {
                try {
                    generateAndDownloadSingleCode(data, type, barcodeData, i + 1);
                    completed++;

                    const progress = (completed / quantity) * 100;
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    if (statusText) {
                        statusText.textContent = `Generated ${completed} of ${quantity} copies...`;
                    }

                    resolve();
                } catch (error) {
                    console.error('Error generating copy', i + 1, ':', error);
                    completed++;
                    resolve();
                }
            }, i * 500);
        });

        downloadPromises.push(promise);
    }

    Promise.all(downloadPromises).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Download Complete!',
            text: `Successfully generated ${quantity} copies for download.`,
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    }).catch((error) => {
        console.error('Error in quantity download:', error);
        Swal.fire({
            icon: 'warning',
            title: 'Download Completed with Issues',
            text: `Some downloads may have failed. Please check your downloads folder.`,
            confirmButtonColor: '#ffc107'
        });
    });
}

function generateAndDownloadSingleCode(data, type, barcodeData, copyNumber) {
    try {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 200, height: 200 };
        const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 300, height: 100 };

        if (type === 'qr' || type === 'both') {
            // Generate QR code
            if (typeof QRCode !== 'undefined' && typeof QRCode === 'function') {
                const canvas = document.createElement('canvas');
                canvas.width = qrSize.width;
                canvas.height = qrSize.height;

                const tempDiv = document.createElement('div');
                tempDiv.style.width = qrSize.width + 'px';
                tempDiv.style.height = qrSize.height + 'px';
                document.body.appendChild(tempDiv);

                const qr = new QRCode(tempDiv, {
                    text: data,
                    width: qrSize.width,
                    height: qrSize.height,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
                });

                setTimeout(() => {
                    const qrCanvas = tempDiv.querySelector('canvas');
                    const qrImg = tempDiv.querySelector('img');

                    if (qrCanvas) {
                        const link = document.createElement('a');
                        link.download = `qr-code-copy-${copyNumber}-${Date.now()}.png`;
                        link.href = qrCanvas.toDataURL();
                        link.click();
                    } else if (qrImg && qrImg.src.startsWith('data:')) {
                        const link = document.createElement('a');
                        link.download = `qr-code-copy-${copyNumber}-${Date.now()}.png`;
                        link.href = qrImg.src;
                        link.click();
                    }

                    document.body.removeChild(tempDiv);
                }, 200);
            } else {
                // Fallback QR generation
                const canvas = document.createElement('canvas');
                canvas.width = qrSize.width;
                canvas.height = qrSize.height;
                const ctx = canvas.getContext('2d');

                // Generate fallback QR pattern
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';

                const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));
                for (let x = 0; x < canvas.width; x += moduleSize) {
                    for (let y = 0; y < canvas.height; y += moduleSize) {
                        if ((x + y) % (moduleSize * 2) === 0) {
                            ctx.fillRect(x, y, moduleSize, moduleSize);
                        }
                    }
                }

                const cornerSize = Math.max(20, canvas.width * 0.2);
                ctx.fillRect(0, 0, cornerSize, cornerSize);
                ctx.fillRect(canvas.width - cornerSize, 0, cornerSize, cornerSize);
                ctx.fillRect(0, canvas.height - cornerSize, cornerSize, cornerSize);

                const link = document.createElement('a');
                link.download = `qr-code-copy-${copyNumber}-${Date.now()}.png`;
                link.href = canvas.toDataURL();
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        if (type === 'barcode' || type === 'both') {
            const delay = type === 'both' ? 150 : 0;

            setTimeout(() => {
                if (typeof JsBarcode !== 'undefined') {
                    const canvas = document.createElement('canvas');
                    canvas.width = barcodeSize.width;
                    canvas.height = barcodeSize.height;

                    const barcodeWidth = Math.max(1, barcodeSize.width / 200);
                    const barcodeHeight = Math.max(30, barcodeSize.height * 0.6);
                    const fontSize = Math.max(8, Math.min(14, barcodeSize.width / 25));

                    JsBarcode(canvas, barcodeData, {
                        format: "CODE128",
                        width: barcodeWidth,
                        height: barcodeHeight,
                        displayValue: true,
                        fontSize: fontSize,
                        margin: 10,
                        background: '#FFFFFF',
                        lineColor: '#000000'
                    });

                    const link = document.createElement('a');
                    link.download = `barcode-copy-${copyNumber}-${Date.now()}.png`;
                    link.href = canvas.toDataURL();
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    // Fallback barcode
                    const canvas = document.createElement('canvas');
                    canvas.width = barcodeSize.width;
                    canvas.height = barcodeSize.height;
                    const ctx = canvas.getContext('2d');

                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#000000';

                    const barWidth = Math.max(1, canvas.width / 150);
                    const spacing = Math.max(1, barWidth * 0.8);
                    const margin = canvas.width * 0.05;
                    let x = margin;

                    const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
                    const maxBarHeight = canvas.height * 0.6;
                    const minBarHeight = canvas.height * 0.3;

                    for (let i = 0; i < barsCount; i++) {
                        const height = minBarHeight + (i % (maxBarHeight - minBarHeight));
                        if (i % 2 === 0) {
                            ctx.fillRect(x, margin, barWidth, height);
                        }
                        x += barWidth + spacing;
                    }

                    // Add text with dynamic font size
                    const fontSize = Math.max(8, Math.min(12, canvas.width / 25));
                    ctx.font = `${fontSize}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.fillText(barcodeData, canvas.width / 2, canvas.height - margin);

                    const link = document.createElement('a');
                    link.download = `barcode-copy-${copyNumber}-${Date.now()}.png`;
                    link.href = canvas.toDataURL();
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }, delay);
        }
    } catch (error) {
        console.error('Error generating code copy:', copyNumber, error);
    }
}

function printGeneratedCodeWithQuantity() {
    printWithDetailsOption(true);
}

function printWithDetailsOption(skipDialog = null) {
    if (!window.generatedModalCode || !window.currentModalData) {
        Swal.fire({
            icon: 'warning',
            title: 'No Code Generated',
            text: 'Please generate a code first.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const quantity = parseInt(document.getElementById('codeQuantity').value) || 1;
    const { type } = window.generatedModalCode;
    const { data, barcodeData } = window.currentModalData;

    if (quantity < 1 || quantity > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Quantity',
            text: 'Please enter a quantity between 1 and 100.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    let codeTypeText = '';
    if (type === 'both') {
        codeTypeText = 'QR code and barcode';
    } else if (type === 'qr') {
        codeTypeText = 'QR code';
    } else if (type === 'barcode') {
        codeTypeText = 'barcode';
    } else {
        codeTypeText = 'code';
    }

    if (skipDialog === true) {
        printSingleRowCodes(quantity, type, data, barcodeData, true);
        return;
    } else if (skipDialog === false) {
        printSingleRowCodes(quantity, type, data, barcodeData, false);
        return;
    }

    Swal.fire({
        title: 'Print Options',
        html: `
            <div class="text-center mb-3">
                <i class="fa fa-print fa-3x text-primary mb-3"></i>
                <p class="mb-3">You are about to print <strong>${quantity}</strong> ${quantity === 1 ? 'copy' : 'copies'} of this ${codeTypeText}.</p>
                <p class="text-muted">Would you like to include row details and headings in the print, or print only the codes?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-list me-1"></i> With Details',
        cancelButtonText: '<i class="fa fa-qrcode me-1"></i> Codes Only',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary me-2',
            cancelButton: 'btn btn-outline-secondary'
        }
    }).then((result) => {
        const includeDetails = result.isConfirmed;
        printSingleRowCodes(quantity, type, data, barcodeData, includeDetails);
    });
}

function printSingleRowCodes(quantity, type, data, barcodeData, includeDetails = true) {
    const printSize = document.querySelector('input[name="print_size"]:checked');
    const sizeInfo = printSize ? printSize.nextElementSibling.textContent : 'Default size';

    const printWindow = window.open('', '_blank');

    const headerStyle = includeDetails ? `
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }` : `
        .print-header {
            display: none;
        }`;

    const gridStyle = includeDetails ? `
        .codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }` : `
        .codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 0;
            padding: 10px;
        }`;

    const itemStyle = includeDetails ? `
        .code-item {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
            background: #f9f9f9;
            border-radius: 8px;
        }` : `
        .code-item {
            border: none;
            padding: 5px;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
            background: transparent;
        }`;

    printWindow.document.write(`
        <html>
            <head>
                <title>Print Scanner Codes (${quantity} Copies)</title>
                <style>
                    body {
                        margin: 0;
                        padding: ${includeDetails ? '20px' : '5px'};
                        font-family: Arial, sans-serif;
                    }
                    ${headerStyle}
                    ${gridStyle}
                    ${itemStyle}
                    .code-item h4 {
                        margin: 0 0 10px 0;
                        color: #333;
                        font-size: ${includeDetails ? '14px' : '12px'};
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    canvas, img {
                        max-width: 100%;
                        height: auto;
                        margin: ${includeDetails ? '5px 0' : '2px 0'};
                        border: ${includeDetails ? '1px solid #ddd' : 'none'};
                        padding: ${includeDetails ? '5px' : '0px'};
                        background: ${includeDetails ? 'white' : 'transparent'};
                        border-radius: ${includeDetails ? '4px' : '0px'};
                    }
                    .copy-number {
                        font-size: ${includeDetails ? '12px' : '10px'};
                        color: #666;
                        margin-top: ${includeDetails ? '10px' : '5px'};
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    .code-container {
                        display: flex;
                        flex-direction: ${type === 'both' ? 'column' : 'column'};
                        align-items: center;
                        gap: ${includeDetails ? '10px' : '5px'};
                    }
                    .code-wrapper {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        margin: ${includeDetails ? '5px' : '2px'};
                    }
                    .code-label {
                        font-size: ${includeDetails ? '12px' : '10px'};
                        font-weight: bold;
                        color: #333;
                        margin-bottom: ${includeDetails ? '5px' : '2px'};
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    @media print {
                        .codes-grid {
                            grid-template-columns: ${includeDetails ? 'repeat(2, 1fr)' : 'repeat(4, 1fr)'} !important;
                            gap: ${includeDetails ? '15px' : '5px'};
                        }
                        .code-item {
                            margin-bottom: ${includeDetails ? '20px' : '5px'};
                        }
                        body {
                            padding: ${includeDetails ? '20px' : '2px'} !important;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>Scanner Codes - ${quantity} Copies</h2>
                    <p>Print Size: <strong>${sizeInfo}</strong></p>
                    <p>Generated on ${new Date().toLocaleString()}</p>
                    <p>Print Mode: <strong>${includeDetails ? 'With Details' : 'Codes Only'}</strong></p>
                </div>
                <div class="codes-grid" id="codesContainer">
                </div>
            </body>
        </html>
    `);

    printWindow.document.close();

    generateCodesForSingleRowPrint(printWindow, quantity, type, data, barcodeData, includeDetails);
}

function generateCodesForPrint(printWindow, quantity, type, data, barcodeData) {
    const container = printWindow.document.getElementById('codesContainer');
    const printSize = document.querySelector('input[name="print_size"]:checked');
    const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 200, height: 200 };
    const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 300, height: 100 };

    for (let i = 0; i < quantity; i++) {
        const codeItem = printWindow.document.createElement('div');
        codeItem.className = 'code-item';

        if (type === 'both') {
            codeItem.innerHTML = `
                <h4>QR Code & Barcode</h4>
                <div id="qr-${i}" style="margin-bottom: 15px;"></div>
                <div id="barcode-${i}"></div>
                <div class="copy-number">Copy ${i + 1} of ${quantity}</div>
            `;
        } else {
            codeItem.innerHTML = `
                <h4>${type.toUpperCase()} Code</h4>
                <div id="code-${i}"></div>
                <div class="copy-number">Copy ${i + 1} of ${quantity}</div>
            `;
        }

        container.appendChild(codeItem);

        // Generate the actual codes
        setTimeout(() => {
            if (type === 'qr' || type === 'both') {
                const qrContainer = printWindow.document.getElementById(type === 'both' ? `qr-${i}` : `code-${i}`);
                generateQRForPrint(qrContainer, data, qrSize);
            }

            if (type === 'barcode' || type === 'both') {
                const barcodeContainer = printWindow.document.getElementById(type === 'both' ? `barcode-${i}` : `code-${i}`);
                generateBarcodeForPrint(barcodeContainer, barcodeData, barcodeSize);
            }

            if (i === quantity - 1) {
                setTimeout(() => {
                    printWindow.focus();
                    printWindow.print();
                }, 1000);
            }
        }, i * 100);
    }
}

function generateCodesForSingleRowPrint(printWindow, quantity, type, data, barcodeData, includeDetails = true) {
    const container = printWindow.document.getElementById('codesContainer');
    const printSize = document.querySelector('input[name="print_size"]:checked');
    const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 200, height: 200 };
    const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 300, height: 100 };

    for (let i = 0; i < quantity; i++) {
        const codeItem = printWindow.document.createElement('div');
        codeItem.className = 'code-item';

        if (type === 'both') {
            codeItem.innerHTML = `
                <h4>QR Code & Barcode</h4>
                <div class="code-container">
                    <div class="code-wrapper">
                        <div class="code-label">QR Code</div>
                        <div id="qr-${i}"></div>
                    </div>
                    <div class="code-wrapper">
                        <div class="code-label">Barcode</div>
                        <div id="barcode-${i}"></div>
                    </div>
                </div>
                <div class="copy-number">Copy ${i + 1} of ${quantity}</div>
            `;
        } else {
            codeItem.innerHTML = `
                <h4>${type.toUpperCase()} Code</h4>
                <div class="code-container">
                    <div class="code-wrapper">
                        <div class="code-label">${type.toUpperCase()} Code</div>
                        <div id="code-${i}"></div>
                    </div>
                </div>
                <div class="copy-number">Copy ${i + 1} of ${quantity}</div>
            `;
        }

        container.appendChild(codeItem);

        // Generate the actual codes
        setTimeout(() => {
            if (type === 'qr' || type === 'both') {
                const qrContainer = printWindow.document.getElementById(type === 'both' ? `qr-${i}` : `code-${i}`);
                generateQRForPrint(qrContainer, data, qrSize);
            }

            if (type === 'barcode' || type === 'both') {
                const barcodeContainer = printWindow.document.getElementById(type === 'both' ? `barcode-${i}` : `code-${i}`);
                generateBarcodeForPrint(barcodeContainer, barcodeData, barcodeSize);
            }

            if (i === quantity - 1) {
                setTimeout(() => {
                    printWindow.focus();
                    printWindow.print();
                }, 1000);
            }
        }, i * 100);
    }
}

function generateQRForPrint(container, data, size) {
    if (typeof QRCode !== 'undefined' && typeof QRCode === 'function') {
        const qr = new QRCode(container, {
            text: data,
            width: size.width,
            height: size.height,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
        });
    } else {
        // Fallback
        const canvas = document.createElement('canvas');
        canvas.width = size.width;
        canvas.height = size.height;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';

        const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));
        for (let x = 0; x < canvas.width; x += moduleSize) {
            for (let y = 0; y < canvas.height; y += moduleSize) {
                if ((x + y) % (moduleSize * 2) === 0) {
                    ctx.fillRect(x, y, moduleSize, moduleSize);
                }
            }
        }

        container.appendChild(canvas);
    }
}

function generateBarcodeForPrint(container, data, size) {
    if (typeof JsBarcode !== 'undefined') {
        const canvas = document.createElement('canvas');
        canvas.width = size.width;
        canvas.height = size.height;

        const barcodeWidth = Math.max(1, size.width / 200);
        const barcodeHeight = Math.max(30, size.height * 0.6);
        const fontSize = Math.max(8, Math.min(14, size.width / 25));

        JsBarcode(canvas, data, {
            format: "CODE128",
            width: barcodeWidth,
            height: barcodeHeight,
            displayValue: true,
            fontSize: fontSize,
            margin: 10,
            background: '#FFFFFF',
            lineColor: '#000000'
        });

        container.appendChild(canvas);
    } else {
        // Fallback
        const canvas = document.createElement('canvas');
        canvas.width = size.width;
        canvas.height = size.height;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';

        const barWidth = Math.max(1, canvas.width / 150);
        const spacing = Math.max(1, barWidth * 0.8);
        const margin = canvas.width * 0.05;
        let x = margin;

        const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
        const maxBarHeight = canvas.height * 0.6;
        const minBarHeight = canvas.height * 0.3;

        for (let i = 0; i < barsCount; i++) {
            const height = minBarHeight + (i % (maxBarHeight - minBarHeight));
            if (i % 2 === 0) {
                ctx.fillRect(x, margin, barWidth, height);
            }
            x += barWidth + spacing;
        }

        container.appendChild(canvas);
    }
}

function toggleAllRows(selectAllCheckbox) {
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateBulkButtons();
}

function updateBulkButtons() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkButtons = document.getElementById('bulkActionButtons');
    const topBulkButtons = document.getElementById('topBulkActionButtons');
    const selectedCount = document.getElementById('selectedCount');
    const selectedCountPrint = document.getElementById('selectedCountPrint');
    const topSelectedCount = document.getElementById('topSelectedCount');
    const topSelectedCountPrint = document.getElementById('topSelectedCountPrint');

    if (selectedCheckboxes.length > 0) {
        if (bulkButtons) {
            bulkButtons.classList.remove('d-none');
            selectedCount.textContent = selectedCheckboxes.length;
            selectedCountPrint.textContent = selectedCheckboxes.length;
        }
        if (topBulkButtons) {
            topBulkButtons.classList.remove('d-none');
            topSelectedCount.textContent = selectedCheckboxes.length;
            topSelectedCountPrint.textContent = selectedCheckboxes.length;
        }
    } else {
        if (bulkButtons) bulkButtons.classList.add('d-none');
        if (topBulkButtons) topBulkButtons.classList.add('d-none');
    }

    const allCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllRows');
    if (selectAllCheckbox) {
        if (selectedCheckboxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (selectedCheckboxes.length === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
}

function getSelectedRowsData() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedRows = [];

    selectedCheckboxes.forEach(checkbox => {
        const rowIndex = parseInt(checkbox.value);
        if (window.tableData && window.tableData[rowIndex]) {
            selectedRows.push({
                index: rowIndex,
                data: window.tableData[rowIndex]
            });
        }
    });

    return selectedRows;
}

function bulkDownload() {
    const selectedRows = getSelectedRowsData();

    if (selectedRows.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Rows Selected',
            text: 'Please select at least one row to download.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const processId = document.getElementById('processSelect').value;
    const processInfo = processData[processId];
    const selectedCodeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
    const codeTypeValues = Array.from(selectedCodeTypes).map(cb => cb.value);

    const printSize = document.querySelector('input[name="print_size"]:checked');
    const sizeInfo = printSize ? printSize.nextElementSibling.textContent : 'Default size';

    // Show progress modal
    Swal.fire({
        title: 'Generating Codes for Download',
        html: `
            <div class="alert alert-info mb-3">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Print Size:</strong> ${sizeInfo}
            </div>
            <div class="progress mb-3">
                <div class="progress-bar" role="progressbar" style="width: 0%" id="downloadProgress"></div>
            </div>
            <p id="downloadStatus">Preparing download for ${selectedRows.length} rows...</p>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            processBulkDownload(selectedRows, processInfo, codeTypeValues);
        }
    });
}

function processBulkDownload(selectedRows, processInfo, codeTypeValues) {
    const progressBar = document.getElementById('downloadProgress');
    const statusText = document.getElementById('downloadStatus');
    let completed = 0;

    const downloadPromises = [];

    selectedRows.forEach((rowItem, index) => {
        const { data: rowData, index: rowIndex } = rowItem;

        // Determine what codes to generate based on step 1 selection
        const codesToGenerate = [];

        if (codeTypeValues.includes('both')) {
            codesToGenerate.push('qr', 'barcode');
        } else {
            if (codeTypeValues.includes('qr')) codesToGenerate.push('qr');
            if (codeTypeValues.includes('barcode')) codesToGenerate.push('barcode');
        }

        if (codesToGenerate.length === 0) {
            codesToGenerate.push('qr');
        }

        codesToGenerate.forEach(codeType => {
            const promise = new Promise((resolve) => {
                setTimeout(() => {
                    generateAndDownloadRowCode(rowData, rowIndex, codeType, processInfo, index);
                    completed++;

                    const progress = (completed / (selectedRows.length * codesToGenerate.length)) * 100;
                    progressBar.style.width = progress + '%';
                    statusText.textContent = `Generated ${completed} of ${selectedRows.length * codesToGenerate.length} codes...`;

                    resolve();
                }, index * codesToGenerate.length * 200); // Stagger downloads
            });

            downloadPromises.push(promise);
        });
    });

    Promise.all(downloadPromises).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Download Complete!',
            text: `Successfully generated and downloaded codes for ${selectedRows.length} rows.`,
            confirmButtonColor: '#28a745'
        });
    });
}

function generateAndDownloadRowCode(rowData, rowIndex, codeType, processInfo, fileIndex) {
    try {
        const printSize = document.querySelector('input[name="print_size"]:checked');
        const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 200, height: 200 };
        const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 300, height: 100 };

        if (codeType === 'qr') {
            // Generate QR code data
            const qrData = {
                row_index: rowIndex + 1,
                process: processInfo.process_name,
                table: processInfo.table,
                data: rowData,
                generated_at: new Date().toISOString(),
                type: 'QR',
                print_size: printSize ? printSize.value : 'medium'
            };

            const dataString = JSON.stringify(qrData);

            if (typeof QRCode !== 'undefined' && typeof QRCode === 'function') {
                const canvas = document.createElement('canvas');
                canvas.width = qrSize.width;
                canvas.height = qrSize.height;

                const tempDiv = document.createElement('div');
                tempDiv.style.width = qrSize.width + 'px';
                tempDiv.style.height = qrSize.height + 'px';
                document.body.appendChild(tempDiv);

                const qr = new QRCode(tempDiv, {
                    text: dataString,
                    width: qrSize.width,
                    height: qrSize.height,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
                });

                setTimeout(() => {
                    const qrCanvas = tempDiv.querySelector('canvas');
                    const qrImg = tempDiv.querySelector('img');

                    if (qrCanvas) {
                        const link = document.createElement('a');
                        link.download = `qr-row-${rowIndex + 1}-${Date.now()}-${fileIndex}.png`;
                        link.href = qrCanvas.toDataURL();
                        link.click();
                    } else if (qrImg && qrImg.src.startsWith('data:')) {
                        const link = document.createElement('a');
                        link.download = `qr-row-${rowIndex + 1}-${Date.now()}-${fileIndex}.png`;
                        link.href = qrImg.src;
                        link.click();
                    }

                    document.body.removeChild(tempDiv);
                }, 300);
            } else {
                // Fallback QR generation with dynamic size
                const canvas = document.createElement('canvas');
                canvas.width = qrSize.width;
                canvas.height = qrSize.height;
                const ctx = canvas.getContext('2d');

                // Generate fallback QR pattern
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';

                const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));
                for (let x = 0; x < canvas.width; x += moduleSize) {
                    for (let y = 0; y < canvas.height; y += moduleSize) {
                        if ((x + y) % (moduleSize * 2) === 0) {
                            ctx.fillRect(x, y, moduleSize, moduleSize);
                        }
                    }
                }

                // Add corner squares with dynamic sizing
                const cornerSize = Math.max(20, canvas.width * 0.2);
                ctx.fillRect(0, 0, cornerSize, cornerSize);
                ctx.fillRect(canvas.width - cornerSize, 0, cornerSize, cornerSize);
                ctx.fillRect(0, canvas.height - cornerSize, cornerSize, cornerSize);

                const link = document.createElement('a');
                link.download = `qr-row-${rowIndex + 1}-${Date.now()}-${fileIndex}.png`;
                link.href = canvas.toDataURL();
                link.click();
            }

        } else if (codeType === 'barcode') {
            // Generate barcode with dynamic size
            const barcodeData = rowData.code || rowData.id || rowData.product_code || `ROW${rowIndex + 1}${Date.now().toString().slice(-4)}`;

            if (typeof JsBarcode !== 'undefined') {
                const canvas = document.createElement('canvas');
                canvas.width = barcodeSize.width;
                canvas.height = barcodeSize.height;

                // Calculate dynamic barcode parameters
                const barcodeWidth = Math.max(1, barcodeSize.width / 200);
                const barcodeHeight = Math.max(30, barcodeSize.height * 0.6);
                const fontSize = Math.max(8, Math.min(14, barcodeSize.width / 25));

                JsBarcode(canvas, barcodeData, {
                    format: "CODE128",
                    width: barcodeWidth,
                    height: barcodeHeight,
                    displayValue: true,
                    fontSize: fontSize,
                    margin: 10,
                    background: '#FFFFFF',
                    lineColor: '#000000'
                });

                const link = document.createElement('a');
                link.download = `barcode-row-${rowIndex + 1}-${Date.now()}-${fileIndex}.png`;
                link.href = canvas.toDataURL();
                link.click();
            } else {
                // Fallback barcode with dynamic size
                const canvas = document.createElement('canvas');
                canvas.width = barcodeSize.width;
                canvas.height = barcodeSize.height;
                const ctx = canvas.getContext('2d');

                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';

                // Calculate dynamic barcode parameters
                const barWidth = Math.max(1, canvas.width / 150);
                const spacing = Math.max(1, barWidth * 0.8);
                const margin = canvas.width * 0.05;
                let x = margin;

                const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
                const maxBarHeight = canvas.height * 0.6;
                const minBarHeight = canvas.height * 0.3;

                // Simple barcode pattern
                for (let i = 0; i < barsCount; i++) {
                    const height = minBarHeight + (i % (maxBarHeight - minBarHeight));
                    if (i % 2 === 0) {
                        ctx.fillRect(x, margin, barWidth, height);
                    }
                    x += barWidth + spacing;
                }

                // Add text with dynamic font size
                const fontSize = Math.max(8, Math.min(12, canvas.width / 25));
                ctx.font = `${fontSize}px Arial`;
                ctx.textAlign = 'center';
                ctx.fillText(barcodeData, canvas.width / 2, canvas.height - margin);

                const link = document.createElement('a');
                link.download = `barcode-row-${rowIndex + 1}-${Date.now()}-${fileIndex}.png`;
                link.href = canvas.toDataURL();
                link.click();
            }
        }
    } catch (error) {
        console.error('Error generating code for row:', rowIndex, error);
    }
}

function bulkPrint() {
    const selectedRows = getSelectedRowsData();

    if (selectedRows.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Rows Selected',
            text: 'Please select at least one row to print.',
            confirmButtonColor: '#007bff'
        });
        return;
    }

    const processId = document.getElementById('processSelect').value;
    const processInfo = processData[processId];
    const selectedCodeTypes = document.querySelectorAll('input[name="code_types[]"]:checked');
    const codeTypeValues = Array.from(selectedCodeTypes).map(cb => cb.value);

    const printSize = document.querySelector('input[name="print_size"]:checked');
    const sizeInfo = printSize ? printSize.nextElementSibling.textContent : 'Default size';

    let codeTypeText = '';
    if (codeTypeValues.includes('both')) {
        codeTypeText = 'QR codes and barcodes';
    } else if (codeTypeValues.includes('qr') && codeTypeValues.includes('barcode')) {
        codeTypeText = 'QR codes and barcodes';
    } else if (codeTypeValues.includes('qr')) {
        codeTypeText = 'QR codes';
    } else if (codeTypeValues.includes('barcode')) {
        codeTypeText = 'barcodes';
    } else {
        codeTypeText = 'codes';
    }

    Swal.fire({
        title: 'Print Options',
        html: `
            <div class="text-center mb-3">
                <i class="fa fa-print fa-3x text-primary mb-3"></i>
                <p class="mb-3">You are about to print <strong>${selectedRows.length}</strong> ${codeTypeText}.</p>
                <p class="text-muted">Would you like to include row details and headings in the print, or print only the codes?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-list me-1"></i> With Details',
        cancelButtonText: '<i class="fa fa-qrcode me-1"></i> Codes Only',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary me-2',
            cancelButton: 'btn btn-outline-secondary'
        }
    }).then((result) => {
        const includeDetails = result.isConfirmed;

        // Show progress modal
        Swal.fire({
            title: 'Generating Codes for Print',
            html: `
                <div class="alert alert-info mb-3">
                    <i class="fa fa-info-circle me-2"></i>
                    <strong>Print Size:</strong> ${sizeInfo}<br>
                    <strong>Print Mode:</strong> ${includeDetails ? 'With Details' : 'Codes Only'}
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 0%" id="printProgress"></div>
                </div>
                <p id="printStatus">Preparing print layout for ${selectedRows.length} rows...</p>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                processBulkPrint(selectedRows, processInfo, codeTypeValues, includeDetails);
            }
        });
    });
}

function processBulkPrint(selectedRows, processInfo, codeTypeValues, includeDetails = true) {
    const progressBar = document.getElementById('printProgress');
    const statusText = document.getElementById('printStatus');

    const printWindow = window.open('', '_blank');

    // Conditional styles based on includeDetails
    const headerStyle = includeDetails ? `
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }` : `
        .print-header {
            display: none;
        }`;

    const gridStyle = includeDetails ? `
        .codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }` : `
        .codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 0;
            padding: 10px;
        }`;

    const itemStyle = includeDetails ? `
        .code-item {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            page-break-inside: avoid;
            background: #f9f9f9;
            border-radius: 8px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }` : `
        .code-item {
            border: none;
            padding: 5px;
            text-align: center;
            page-break-inside: avoid;
            background: transparent;
            min-height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }`;

    const rowInfoStyle = includeDetails ? `
        .row-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            text-align: left;
        }
        .row-info strong {
            color: #333;
        }` : `
        .row-info {
            display: none;
        }`;

    printWindow.document.write(`
        <html>
            <head>
                <title>Bulk Print Scanner Codes</title>
                <style>
                    body {
                        margin: 0;
                        padding: ${includeDetails ? '20px' : '5px'};
                        font-family: Arial, sans-serif;
                    }
                    ${headerStyle}
                    ${gridStyle}
                    ${itemStyle}
                    .code-container {
                        margin: ${includeDetails ? '15px 0' : '5px 0'};
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: ${includeDetails ? '15px' : '5px'};
                    }
                    .code-container.both-codes {
                        flex-direction: row;
                        justify-content: space-around;
                        flex-wrap: wrap;
                    }
                    .code-wrapper {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        margin: ${includeDetails ? '10px' : '2px'};
                    }
                    .code-label {
                        font-size: ${includeDetails ? '12px' : '10px'};
                        font-weight: bold;
                        color: #333;
                        margin-bottom: ${includeDetails ? '5px' : '2px'};
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    canvas, img {
                        max-width: 100%;
                        height: auto;
                        margin: ${includeDetails ? '5px' : '2px'};
                        border: ${includeDetails ? '1px solid #ddd' : 'none'};
                        padding: ${includeDetails ? '5px' : '0px'};
                        background: ${includeDetails ? 'white' : 'transparent'};
                        border-radius: ${includeDetails ? '4px' : '0px'};
                    }
                    ${rowInfoStyle}
                    h2 {
                        color: #333;
                        margin: 0;
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    h3 {
                        color: #555;
                        margin: 10px 0 5px 0;
                        font-size: 14px;
                        ${includeDetails ? '' : 'display: none;'}
                    }
                    @media print {
                        .codes-grid {
                            grid-template-columns: ${includeDetails ? 'repeat(2, 1fr)' : 'repeat(4, 1fr)'};
                            gap: ${includeDetails ? '15px' : '5px'};
                        }
                        .code-item {
                            break-inside: avoid;
                            page-break-inside: avoid;
                            margin-bottom: ${includeDetails ? '20px' : '5px'};
                        }
                        .code-container.both-codes {
                            flex-direction: ${includeDetails ? 'column' : 'row'};
                            gap: ${includeDetails ? '10px' : '2px'};
                        }
                        .code-wrapper {
                            margin: ${includeDetails ? '5px 0' : '1px'};
                        }
                        body {
                            padding: ${includeDetails ? '20px' : '2px'} !important;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>Scanner Codes Print</h2>
                    <p>Process: <strong>${processInfo.process_name}</strong> | Table: <strong>${processInfo.table}</strong></p>
                    <p>Generated: <strong>${new Date().toLocaleString()}</strong> | Total Rows: <strong>${selectedRows.length}</strong></p>
                    <p>Print Mode: <strong>${includeDetails ? 'With Details' : 'Codes Only'}</strong></p>
                </div>
                <div class="codes-grid" id="codesGrid">
                </div>
            </body>
        </html>
    `);

    const codesGrid = printWindow.document.getElementById('codesGrid');
    let completed = 0;
    const totalCodes = selectedRows.length;

    selectedRows.forEach((rowItem, index) => {
        const { data: rowData, index: rowIndex } = rowItem;

        const rowContainer = printWindow.document.createElement('div');
        rowContainer.className = 'code-item';

        let rowInfoHtml = '';
        if (includeDetails) {
            const rowInfo = Object.entries(rowData).slice(0, 3).map(([key, value]) => {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                return `<strong>${label}:</strong> ${value || 'N/A'}`;
            }).join(' | ');

            rowInfoHtml = `
                <div class="row-info">
                    <small>Row ${rowIndex + 1}: ${rowInfo}</small>
                </div>
            `;
        }

        rowContainer.innerHTML = `
            ${rowInfoHtml}
            <div class="code-container" id="codeContainer_${index}">
                <div style="padding: ${includeDetails ? '20px' : '5px'}; color: #666; font-size: ${includeDetails ? '14px' : '10px'};">Generating codes...</div>
            </div>
        `;

        codesGrid.appendChild(rowContainer);

        setTimeout(() => {
            generateCodesForPrint(rowData, rowIndex, codeTypeValues, processInfo, printWindow, index, includeDetails);
            completed++;

            const progress = (completed / totalCodes) * 100;
            progressBar.style.width = progress + '%';
            statusText.textContent = `Generated ${completed} of ${totalCodes} code sets...`;

            if (completed === totalCodes) {
                setTimeout(() => {
                    Swal.close();
                    printWindow.focus();
                    printWindow.print();
                }, 1000);
            }
        }, index * 500); // Stagger generation
    });
}

function generateCodesForPrint(rowData, rowIndex, codeTypeValues, processInfo, printWindow, containerIndex, includeDetails = true) {
    const container = printWindow.document.getElementById(`codeContainer_${containerIndex}`);

    const printSize = document.querySelector('input[name="print_size"]:checked');
    const qrSize = printSize ? calculateCodeSize(printSize.value) : { width: 150, height: 150 };
    const barcodeSize = printSize ? calculateBarcodeSize(printSize.value) : { width: 250, height: 80 };

    // Determine what codes to generate
    const codesToGenerate = [];
    if (codeTypeValues.includes('both')) {
        codesToGenerate.push('qr', 'barcode');
    } else {
        if (codeTypeValues.includes('qr')) codesToGenerate.push('qr');
        if (codeTypeValues.includes('barcode')) codesToGenerate.push('barcode');
    }

    if (codesToGenerate.length === 0) {
        codesToGenerate.push('qr'); // Default to QR
    }

    container.innerHTML = '';

    if (codesToGenerate.length > 1) {
        container.className = 'code-container both-codes';
    } else {
        container.className = 'code-container';
    }

    codesToGenerate.forEach(codeType => {
        if (codeType === 'qr') {
            // Generate QR code with dynamic size
            const qrData = {
                row_index: rowIndex + 1,
                process: processInfo.process_name,
                table: processInfo.table,
                data: rowData,
                generated_at: new Date().toISOString(),
                type: 'QR',
                print_size: printSize ? printSize.value : 'medium'
            };

            const dataString = JSON.stringify(qrData);

            const qrWrapper = printWindow.document.createElement('div');
            qrWrapper.className = 'code-wrapper';

            if (includeDetails) {
                const qrLabel = printWindow.document.createElement('div');
                qrLabel.className = 'code-label';
                qrLabel.textContent = 'QR Code';
                qrWrapper.appendChild(qrLabel);
            }

            if (typeof QRCode !== 'undefined' && typeof QRCode === 'function') {
                const qrDiv = printWindow.document.createElement('div');
                qrDiv.style.width = qrSize.width + 'px';
                qrDiv.style.height = qrSize.height + 'px';
                qrDiv.style.margin = '0px';

                try {
                    const qr = new QRCode(qrDiv, {
                        text: dataString,
                        width: qrSize.width,
                        height: qrSize.height,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel ? QRCode.CorrectLevel.M : 1
                    });

                    qrWrapper.appendChild(qrDiv);
                    container.appendChild(qrWrapper);
                } catch (error) {
                    console.error('QR generation error in print:', error);
                    // Fallback with dynamic size
                    const canvas = printWindow.document.createElement('canvas');
                    canvas.width = qrSize.width;
                    canvas.height = qrSize.height;
                    const ctx = canvas.getContext('2d');

                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#000000';

                    const moduleSize = Math.max(3, Math.min(8, canvas.width / 25));
                    for (let x = 0; x < canvas.width; x += moduleSize) {
                        for (let y = 0; y < canvas.height; y += moduleSize) {
                            if ((x + y) % (moduleSize * 2) === 0) {
                                ctx.fillRect(x, y, moduleSize, moduleSize);
                            }
                        }
                    }

                    qrWrapper.appendChild(canvas);
                    container.appendChild(qrWrapper);
                }
            }
        }

        if (codeType === 'barcode') {
            // Generate barcode with dynamic size
            const barcodeData = rowData.code || rowData.id || rowData.product_code || `ROW${rowIndex + 1}${Date.now().toString().slice(-4)}`;

            const barcodeWrapper = printWindow.document.createElement('div');
            barcodeWrapper.className = 'code-wrapper';

            if (includeDetails) {
                const barcodeLabel = printWindow.document.createElement('div');
                barcodeLabel.className = 'code-label';
                barcodeLabel.textContent = 'Barcode';
                barcodeWrapper.appendChild(barcodeLabel);
            }

            if (typeof JsBarcode !== 'undefined') {
                const canvas = printWindow.document.createElement('canvas');
                canvas.width = barcodeSize.width;
                canvas.height = barcodeSize.height;

                // Calculate dynamic barcode parameters
                const barcodeWidth = Math.max(1, barcodeSize.width / 200);
                const barcodeHeight = Math.max(30, barcodeSize.height * 0.6);
                const fontSize = Math.max(8, Math.min(14, barcodeSize.width / 25));

                try {
                    JsBarcode(canvas, barcodeData, {
                        format: "CODE128",
                        width: barcodeWidth,
                        height: barcodeHeight,
                        displayValue: true,
                        fontSize: fontSize,
                        margin: 5,
                        background: '#FFFFFF',
                        lineColor: '#000000'
                    });

                    barcodeWrapper.appendChild(canvas);
                    container.appendChild(barcodeWrapper);
                } catch (error) {
                    console.error('Barcode generation error in print:', error);
                    // Fallback with dynamic size
                    const canvas = printWindow.document.createElement('canvas');
                    canvas.width = barcodeSize.width;
                    canvas.height = barcodeSize.height;
                    const ctx = canvas.getContext('2d');

                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#000000';

                    // Calculate dynamic barcode parameters
                    const barWidth = Math.max(1, canvas.width / 150);
                    const spacing = Math.max(1, barWidth * 0.8);
                    const margin = canvas.width * 0.05;
                    let x = margin;

                    const barsCount = Math.floor((canvas.width - 2 * margin) / (barWidth + spacing));
                    const maxBarHeight = canvas.height * 0.6;
                    const minBarHeight = canvas.height * 0.3;

                    for (let i = 0; i < barsCount; i++) {
                        const height = minBarHeight + (i % (maxBarHeight - minBarHeight));
                        if (i % 2 === 0) {
                            ctx.fillRect(x, margin, barWidth, height);
                        }
                        x += barWidth + spacing;
                    }

                    const fontSize = Math.max(8, Math.min(12, canvas.width / 25));
                    ctx.font = `${fontSize}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.fillText(barcodeData, canvas.width / 2, canvas.height - margin);

                    barcodeWrapper.appendChild(canvas);
                    container.appendChild(barcodeWrapper);
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    console.log('Process Data:', processData);

    showStep(currentStep);
    updatePreview();
    loadAvailableTables();
    updateSizeInfo();

    // Add event listeners
    document.getElementById('tableSelect').addEventListener('change', function() {
        onTableSelect(this.value);
    });

    document.getElementById('processSelect').addEventListener('change', function() {
        onProcessSelect(this.value);
    });

    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
        // Bootstrap 5 initialization
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    } else if (typeof $ !== 'undefined' && $.fn.dropdown) {
        // Bootstrap 4 initialization
        $('.dropdown-toggle').dropdown();
    }

    setTimeout(checkLibraries, 1000);
});
</script>
