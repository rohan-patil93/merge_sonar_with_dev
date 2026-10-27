<?php
$_SESSION['apply_js']['multiselest'] = '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sku_generator_name'])) {
    $sku_generator_name     = $_POST['sku_generator_name'] ?? '';
    $serial_number          = $_POST['serial_number'] ?? '';
    $serial_number_length   = $_POST['serial_number_length'] ?? '';
    $separation_symbol      = $_POST['separation_symbol'] ?? '';
    $sku_generator_detail_json = $_POST['sku_generator_detail'] ?? '{}';
    $rid                    = $_POST['rid'] ?? ''; 

    $sku_generator_detail = json_decode($sku_generator_detail_json, true);  
    $postData = [ 'sku_generator_name' => $sku_generator_name, 'serial_number' => $serial_number, 'serial_number_length' => $serial_number_length, 'separation_symbol' => $separation_symbol, 'sku_generator_detail' => $sku_generator_detail, 'rid' => $rid ];  
   echo  $url = API . "?mode=insertskucode&method=post";
    $response = file_post_contents($url, $postData, 'insert');
    $response = json_decode($response, 1); 
    print_r($response);
    if (strtolower(trim($response['status'])) === 'success') {
        $_SESSION['sku_msg_type'] = 'success';
        $_SESSION['sku_msg'] = $response['message'] ?? "SKU saved successfully.";
        // header("location: index.php?option=sku_generator");
        // exit;
    } else {
        $_SESSION['sku_msg_type'] = 'error';
        $_SESSION['sku_msg'] = $response['message'] ?? "Something went wrong while saving SKU.";
    }
}

$Rid = encodeId($_GET['Rid']);
$rid = explode(":", $Rid);
$rid = $rid['1'];
$url = API . "?mode=getskudetails&method=get&rid=$rid";
$response = callAPI($url);
$res = json_decode($response, true); 
// echo"<pre>";
// print_r($url);
// echo"</pre>"; 
$sku = $res['sku'][0] ?? [];
$sku_detail = [];
if (!empty($sku['sku_generator_detail'])) {
    $sku_detail = json_decode($sku['sku_generator_detail'], true);
} 


if (!empty($_SESSION['sku_msg'])) {
    $msgType = $_SESSION['sku_msg_type'];
    $msg = addslashes($_SESSION['sku_msg']);
 
    $_SESSION['sku_msg'] = '';
    $_SESSION['sku_msg_type'] = '';
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify(
                    'top',
                    'right',
                    'fa <?php echo ($msgType === "success") ? "fa-check-circle" : "fa-exclamation-circle"; ?>',
                    'form-label label label-<?php echo ($msgType === "success") ? "success" : "danger"; ?>',
                    'animated flipInX',
                    'animated flipOutX',
                    '<?php echo $msg; ?>',
                    ''
                );
            }
        };
    </script>
<?php
}
?>
<div class="card ">
    <div class="card-header">
                    <h5>SKU Code Generator</h5>
                    <div class="card-header-right table-card-header ">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>
                    </div>
                </div>
    <div class="card-body p-4">
        <form id="skuForm" method="post" onsubmit="return prepareAndSubmit(event)">
            <input type="hidden" name="rid" id="rid" value="<?= htmlspecialchars($rid ?? '') ?>">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SKU Generator Name <span class="text-danger">*</span></label>
                            <input type="text" name="sku_generator_name" id="sku_generator_name"
                                class="form-control form-control-lg shadow-sm" 
                                value="<?= htmlspecialchars($sku['sku_generator_name'] ?? '') ?>" 
                                placeholder="Enter name" required>
                        </div>
                        <div class=" col-lg-6 mb-3">
                            <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                            <input type="number" name="serial_number" id="serial_number"
                                class="form-control form-control-lg shadow-sm"
                                value="<?= htmlspecialchars($sku['serial_number'] ?? '') ?>" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Serial Number Length <span class="text-danger">*</span></label>
                            <input type="number" name="serial_number_length" id="serial_number_length"
                                class="form-control form-control-lg shadow-sm" 
                                value="<?= htmlspecialchars($sku['serial_number_length'] ?? '') ?>" min="1" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Separation Symbol <span class="text-danger">*</span></label>
                            <input type="text" name="separation_symbol" id="separation_symbol"
                                class="form-control form-control-lg shadow-sm" maxlength="1"
                                value="<?= htmlspecialchars($sku['separation_symbol'] ?? '') ?>" required>
                            <div id="sepError" class="invalid-feedback d-none">
                                Please enter a valid symbol (not letter/number).
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                            <h6 class="text-secondary">Live Demo SKU</h6>
                            <div id="demoskugenerator"
                                class="bg-light border border-dashed rounded-3 px-4 py-3 fw-bold text-primary fs-5 shadow-sm">
                                <!-- SKU will appear here -->
                            </div>
                            <small class="text-muted mt-2">Preview updates in real-time as you type</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-2"></i> Configure Components</h6>
                <div class="row g-4">
                    <?php
                    $components = [
                        'category_name' => 'Category',
                        'item_type' => 'Item Type',
                        'warehouse_name' => 'Warehouse',
                        'location_name' => 'Location'
                    ];
                    foreach ($components as $key => $label): 
                        $enabled = $sku_detail[$key]['enabled'] ?? 0;
                        $sequence = $sku_detail[$key]['sequence'] ?? '';
                        ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body text-center">
                                    <div class="form-group col-lg-12">
                                        <label><?= $label ?></label>
                                        <div class="switch-field">
                                            <input class="js-switch component-toggle" type="checkbox" id="<?= $key ?>"
                                                data-key="<?= $key ?>" <?= ($enabled ? 'checked' : '') ?>>
                                        </div>
                                    </div>
                                    <label class="form-label small">Sequence</label>
                                    <select class="form-select sequence-select js-example-basic-single" 
                                            id="<?= $key ?>_seq" data-key="<?= $key ?>" 
                                            onchange="handleSequenceChange(this)">
                                        <option value="">Select</option>
                                        <?php for($i=1; $i<=4; $i++): ?>
                                            <option value="<?= $i ?>" <?= ($sequence == $i ? 'selected' : '') ?>>
                                                <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mt-4">
                <input type="hidden" id="sku_generator_detail" name="sku_generator_detail" value="">
                <button type="submit" id="kt_login_signin_submit float-left" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Generate SKU
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    const componentMap = { 
        'category_name': 'CATEGORY', 
        'item_type': 'ITEM', 
        'warehouse_name': 'WAREHOUSE', 
        'location_name': 'LOCATION' 
    };

    function getComponentState() {
        const result = {};
        Object.keys(componentMap).forEach(key => {
            const enabled = !!document.getElementById(key).checked;
            const seqEl = document.getElementById(key + '_seq');
            const sequence = seqEl ? (seqEl.value || "") : "";
            result[key] = { enabled, sequence };
        });
        return result;
    }

    function updateDemoAndJson() {
        const serial = document.getElementById('serial_number').value || '';
        const serialLen = parseInt(document.getElementById('serial_number_length').value || 0, 10) || 0;
        const sepInput = document.getElementById('separation_symbol');
        const sepError = document.getElementById('sepError');
        let sep = sepInput.value || '-';

        if (!/^[^a-zA-Z0-9]$/.test(sep)) {
            sepError.classList.remove('d-none');
            sepInput.classList.add('is-invalid');
            sep = '-';
        } else {
            sepError.classList.add('d-none');
            sepInput.classList.remove('is-invalid');
        }

        const compState = getComponentState();
        const comps = [];
        for (const k in compState) {
            if (compState[k].enabled) {
                const seq = compState[k].sequence ? parseInt(compState[k].sequence, 10) : 99;
                comps.push({ key: k, label: componentMap[k], seq });
            }
        }
        comps.sort((a, b) => a.seq - b.seq);

        let demo = comps.map(c => c.label).join(sep);
        if (demo) demo += sep;

        const padLen = Math.max(0, serialLen - String(serial).length);
        demo += '0'.repeat(padLen) + String(serial);

        document.getElementById('demoskugenerator').innerText = demo || '';
    }

    // Always keep options enabled (no disabling)
    function enforceUniqueSequences() {
        const selects = document.querySelectorAll('.sequence-select');
        selects.forEach(sel => {
            Array.from(sel.options).forEach(opt => {
                opt.disabled = false;
            });
        });
    }

    // Auto-switch logic (now takes element directly)
    function handleSequenceChange(changedSelect) {
        const newValue = changedSelect.value;
        if (!newValue) return;

        const selects = document.querySelectorAll('.sequence-select');
 
        selects.forEach(sel => {
            if (sel !== changedSelect && sel.value === newValue) { 
                let freeValue = "";
                for (let i = 1; i <= 4; i++) {
                    const taken = Array.from(selects).some(s => s.value === String(i));
                    if (!taken) {
                        freeValue = String(i);
                        break;
                    }
                }
                sel.value = freeValue;
            }
        });

        updateDemoAndJson(); 
        $(".js-example-basic-single").select2();
    } 

    function prepareAndSubmit(e) {
        const compState = getComponentState();
        const payload = {};
        for (const k in compState) {
            payload[k] = {
                enabled: !!compState[k].enabled,
                sequence: compState[k].sequence ? String(compState[k].sequence) : ""
            };
        }
        document.getElementById('sku_generator_detail').value = JSON.stringify(payload);
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input').forEach(el => {
            el.addEventListener('input', updateDemoAndJson);
            el.addEventListener('change', updateDemoAndJson);
        });
        updateDemoAndJson();
    });
</script>