<?php
$_SESSION['apply_js']['multiselest'] = "1";
$url = API . "?mode=getkittingdekittinglist&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$items = $return['items'] ?? $return['item'] ?? [];
$uom = $return['uom'] ?? $return;
$kitting_data1 = $return['kitting'] ?? [];
foreach($kitting_data1 as $kitdata){
    $kitdata['transaction_date'] = fromDate($kitdata['transaction_date']);
    $kitting_data[] = $kitdata;
}
$kitting_items = $return['kittingitems'] ?? [];
$dekitting_data1 = $return['dekitting'] ?? [];
foreach($dekitting_data1 as $kitdata1){
    $kitdata1['transaction_date'] = fromDate($kitdata1['transaction_date']);
    $dekitting_data[] = $kitdata1;
}
$dekitting_items = $return['dekittingitems'] ?? [];
// Helper functions
function findItemName($id, $items) {
    $item = $item ?? [];
    foreach ($items as $item) {
        if ($item['id'] == $id) {
            return $item['item_name'];
        }
    }
    return 'Unknown Item';
}
function findUomName($id, $uoms) {
    $uom_item = $uom_item ?? [];
    foreach ($uoms as $uom_item) {
        if ($uom_item['id'] == $id) {
            return $uom_item['uom_short_code'];
        }
    }
    return 'Unknown UOM';
}
?>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Search Kitting Process</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body m-r-50 m-l-50">
        <form class="kt-form" id="searchKittingForm">

            <div class="row">
                <div class="form-group col-lg-4">
                    <span class="form-label">Process Type</span>
                    <div class="d-flex">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="process_type" id="kitting" value="kitting" checked>
                            <label class="form-check-label" for="kitting">Kitting</label>
                        </div>
                        <div class="form-check form-check-inline ml-4">
                            <input class="form-check-input" type="radio" name="process_type" id="dekitting" value="dekitting">
                            <label class="form-check-label" for="dekitting">Dekitting</label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label" id="product_label">Select Product</span>
                    <select aria-label="Product Selection" class="form-select js-example-basic-single" id="product_selection">
                        <option value="">Select a product</option>
                    </select>
                </div>


                <div class="form-group col-lg-2">
                    <div class="col-lg-12 text-right">
                        <button type="button" id="search_btn" class="btn btn-primary">
                            <i class="fa fa-search mr-1"></i> Search
                        </button>
                    </div>
                </div>
            <div>
        </form>


        <div id="results_section" style="display: none;">
            <hr class="my-4">

            <h6 class="mb-3" id="result_heading">Components</h6>


            <div id="kitting_results">
                <div id="kitting_process_details" class="row mb-3">

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">Component Name</th>
                                <th scope="col">UOM</th>
                                <th scope="col">Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="kitting_components">

                        </tbody>
                    </table>
                </div>
            </div>


            <div id="dekitting_results" style="display: none;">
                <div id="dekitting_process_details" class="row mb-3">

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">Component Name</th>
                                <th scope="col">UOM</th>
                                <th scope="col">Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="dekitting_components">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = <?= json_encode($items) ?>;
        const uomList = <?= json_encode($uom) ?>;
        const kittingData = <?= json_encode($kitting_data) ?>;
        const kittingItems = <?= json_encode($kitting_items) ?>;
        const dekittingData = <?= json_encode($dekitting_data) ?>;
        const dekittingItems = <?= json_encode($dekitting_items) ?>;

        // Debugging: log data arrays to inspect content
        console.log('items:', items);
        console.log('uomList:', uomList);
        console.log('kittingData:', kittingData);
        console.log('kittingItems:', kittingItems);
        console.log('dekittingData:', dekittingData);
        console.log('dekittingItems:', dekittingItems);

        // Helper functions
        function findItemName(id) {
            const item = items.find(item => item.id == id);
            return item ? item.item_name : 'Unknown Item';
        }

        function findUomName(id) {
            const uomItem = uomList.find(uom => uom.id == id);
            return uomItem ? uomItem.uom_short_code : 'Unknown UOM';
        }

        function getAvailableProducts(processType) {
            const ids = processType === 'kitting'
                ? [...new Set(kittingData.map(k => k.parent_item_id))]
                : [...new Set(dekittingData.map(d => d.parent_item_id))];
            return ids.map(id => items.find(i => i.id == id)).filter(Boolean);
        }

        function populateProductOptions() {
            const processType = document.querySelector('input[name="process_type"]:checked').value;
            const available = getAvailableProducts(processType);
            const select = document.getElementById('product_selection');
            select.innerHTML = '<option value="">Select a product</option>';
            available.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.item_name;
                select.appendChild(opt);
            });
        }

        // Process type radio buttons
        const kittingRadio = document.getElementById('kitting');
        const dekittingRadio = document.getElementById('dekitting');
        const productLabel = document.getElementById('product_label');
        const resultHeading = document.getElementById('result_heading');

        function updateLabels() {
            if (kittingRadio.checked) {
                productLabel.textContent = 'Select Final Product';
                resultHeading.textContent = 'Components Required for Assembly';
            } else {
                productLabel.textContent = 'Select Parent Item';
                resultHeading.textContent = 'Components Extracted from Disassembly';
            }
            populateProductOptions();
        }

        kittingRadio.addEventListener('change', updateLabels);
        dekittingRadio.addEventListener('change', updateLabels);

        updateLabels();

        // Search button click handler
        document.getElementById('search_btn').addEventListener('click', function() {
            const processType = document.querySelector('input[name="process_type"]:checked').value;
            const productId = document.getElementById('product_selection').value;
            console.log(productId);
            if (!productId) {
                alert('Please select a product to search');
                return;
            }

            // Show results section
            document.getElementById('results_section').style.display = 'block';

            // Show appropriate results based on process type
            if (processType === 'kitting') {
                document.getElementById('kitting_results').style.display = 'block';
                document.getElementById('dekitting_results').style.display = 'none';
                displayKittingData(productId);
            } else {
                document.getElementById('kitting_results').style.display = 'none';
                document.getElementById('dekitting_results').style.display = 'block';
                displayDekittingData(productId);
            }
        });

        function displayKittingData(productId) {
            const relevantKittingData = kittingData.filter(kitting => kitting.parent_item_id == productId);

            if (relevantKittingData.length === 0) {
                document.getElementById('kitting_process_details').innerHTML = `
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            No kitting processes found for this product.
                        </div>
                    </div>
                `;
                document.getElementById('kitting_components').innerHTML = '';
                return;
            }

            const kittingProcess = relevantKittingData[0];

            // Display process details
            document.getElementById('kitting_process_details').innerHTML = `
                <div class="col-md-12">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Process Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Process Name:</div>
                                <div class="col-md-7">${kittingProcess.process_name}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Final Product:</div>
                                <div class="col-md-7">${findItemName(kittingProcess.parent_item_id)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Quantity:</div>
                                <div class="col-md-7">${kittingProcess.parent_quantity} </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 font-weight-bold">Kitting Date:</div>
                                <div class="col-md-7">${kittingProcess.transaction_date}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const components = kittingItems.filter(item => item.transaction_id == kittingProcess.id);

            // Display components
            if (components.length > 0) {
                let html = '';
                components.forEach((component, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${findItemName(component.item_id)}</td>
                            <td>${findUomName(component.uom_id)}</td>
                            <td>${component.quantity}</td>
                        </tr>
                    `;
                });
                document.getElementById('kitting_components').innerHTML = html;
            } else {
                document.getElementById('kitting_components').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">No components found for this kitting process.</td>
                    </tr>
                `;
            }
        }

        function displayDekittingData(productId) {
            const relevantDekittingData = dekittingData.filter(dekitting => dekitting.parent_item_id == productId);

            if (relevantDekittingData.length === 0) {
                document.getElementById('dekitting_process_details').innerHTML = `
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            No dekitting processes found for this product.
                        </div>
                    </div>
                `;
                document.getElementById('dekitting_components').innerHTML = '';
                return;
            }

            const dekittingProcess = relevantDekittingData[0];
            // Display process details
            document.getElementById('dekitting_process_details').innerHTML = `
                <div class="col-md-12">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Process Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Process Name:</div>
                                <div class="col-md-7">${dekittingProcess.process_name}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Parent Item:</div>
                                <div class="col-md-7">${findItemName(dekittingProcess.parent_item_id)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 font-weight-bold">Quantity:</div>
                                <div class="col-md-7">${dekittingProcess.parent_quantity} </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 font-weight-bold">Dekitting Date:</div>
                                <div class="col-md-7">${dekittingProcess.transaction_date}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const components = dekittingItems.filter(item => item.transaction_id == dekittingProcess.id);

            // Display components
            if (components.length > 0) {
                let html = '';
                components.forEach((component, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${findItemName(component.item_id)}</td>
                            <td>${findUomName(component.uom_id)}</td>
                            <td>${component.quantity}</td>
                        </tr>
                    `;
                });
                document.getElementById('dekitting_components').innerHTML = html;
            } else {
                document.getElementById('dekitting_components').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">No components found for this dekitting process.</td>
                    </tr>
                `;
            }
        }
    });
</script>
