<?php
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['CONTRACT_DASHBOARD']  =1;
// API call to get dashboard data
$url = API . "?mode=getdashboardreact&method=get&admin_type=".$_SESSION['admin_type']."&LOCID=".$_GET['LOCID'];
$response = callAPI($url);
$data = json_decode($response, true);


// Extract data arrays
$allcontracts = $data['allcontracts'] ?? [];
$allcustomers = $data['allcustomers'] ?? [];
$allvendors = $data['allvendors'] ?? [];
$allpermissions = $data['allpermissions'] ?? [];
$alldocuments = $data['alldocuments'] ?? [];
$allstatus = $data['allstatus'] ?? [];
$contracts = $data['contracts'] ?? [];
$permissions = $data['permissions'] ?? [];
$locations = $data['locations'] ?? [];

// bar chart

// ));



$contractsByYear = [];
if (count((array)$allcontracts) > 0) {
    foreach ($allcontracts as $contract) {
        $dateValue = '';
        if (!empty($contract['start_date'])) {
            $dateValue = $contract['start_date'];
        } elseif (!empty($contract['created_date'])) {
            $dateValue = $contract['created_date'];
        }
        if ($dateValue == '') {
            continue;
        }
        $year = date('Y', strtotime($dateValue));
        if (!isset($contractsByYear[$year])) {
            $contractsByYear[$year] = ['vendor' => 0, 'customer' => 0, 'inhouse' => 0];
        }
        $partyType = strtolower($contract['party_type'] ?? '');
        if ($partyType == 'vendor') {
            $contractsByYear[$year]['vendor']++;
        } elseif ($partyType == 'customer') {
            $contractsByYear[$year]['customer']++;
        } elseif ($partyType == 'inhouse') {
            $contractsByYear[$year]['inhouse']++;
        }
    }
}
ksort($contractsByYear);
$chartData = [];
if (count((array)$contractsByYear) > 0) {
    foreach ($contractsByYear as $year => $counts) {
        $chartData[] = [
            'y' => (string)$year,
            'vendor' => $counts['vendor'],
            'customer' => $counts['customer'],
            'inhouse' => $counts['inhouse'],
        ];
    }
}


// area chart
$contractsByParty = [];
foreach ($allcontracts as $c) {
    if (!empty($c['start_date'])) {
        $month = date("Y-m", strtotime($c['start_date']));
        if (!isset($contractsByParty[$month])) {
            $contractsByParty[$month] = ['vendor' => 0, 'customer' => 0];
        }
        if ($c['party_type'] === 'vendor') {
            $contractsByParty[$month]['vendor']++;
        } elseif ($c['party_type'] === 'customer') {
            $contractsByParty[$month]['customer']++;
        }
    }
}

$allMonthsContracts = array_keys($contractsByParty);
sort($allMonthsContracts);

$contractsOverviewData = [];
foreach ($allMonthsContracts as $m) {
    $contractsOverviewData[] = [
        "period" => date("Y-m", strtotime($m)),
        "vendor" => $contractsByParty[$m]['vendor'] ?? 0,
        "customer" => $contractsByParty[$m]['customer'] ?? 0,
    ];
}




// Helper functions
function isExpired($dateString)
{
    if (!$dateString)
        { return false;
}
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $endDate = new DateTime($dateString);
    return $endDate < $today;
}

function getContractsByExpiry($contractsList, $daysMin, $daysMax)
{
    if (!$contractsList || !is_array($contractsList))
        { return [];
}

    $today = new DateTime();
    $filtered = [];

    foreach ($contractsList as $contract) {
        if (!$contract['end_date'])
            { continue;
}

        $endDate = new DateTime($contract['end_date']);
        $daysLeft = ceil(($endDate->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24));

        if ($daysLeft >= $daysMin && $daysLeft <= $daysMax) {
            $filtered[] = $contract;
        }
    }

    return $filtered;
}
function calculateProgress($contract, $allpermissions, $alldocuments)
{
    $requiredFields = [
        "contract_name",
        "transaction_no",
        "party_id",
        "parent_contract_id",
        "start_date",
        "end_date",
        "description",
        "additional_notes",
        "payment_term_id",
        "termination_condition",
        "contract_status"
    ];

    $totalFields = count($requiredFields);
    $filledFields = 0;

    foreach ($requiredFields as $field) {
        if (!empty($contract[$field])) {
            $filledFields++;
        }
    }

    $contractPermissions = null;
    foreach ($allpermissions as $permission) {
        if ($permission['contract_id'] == $contract['id']) {
            $contractPermissions = $permission;
            break;
        }
    }

    $hasBasicPermissions = !empty($contractPermissions['basic_permissions']);
    $hasDocumentPermission = $hasBasicPermissions && strpos($contractPermissions['basic_permissions'], 'document') !== false;

    $baseProgress = ($filledFields / $totalFields) * 100;

    if (!$hasBasicPermissions)
        { $baseProgress -= 10;
}
    if (!$hasDocumentPermission)
        { $baseProgress -= 10;
}

    $documentCount = 0;
    foreach ($alldocuments as $doc) {
        if ($doc['contract_id'] == $contract['id']) {
            $documentCount++;
        }
    }

    $missingDocuments = 10 - $documentCount;
    $baseProgress -= $missingDocuments * 2;

    return max(0, round($baseProgress));
}
function filterAndSortContracts($contractsList, $partyType, $searchQuery = '', $sortConfig = ['key' => 'contract_name', 'direction' => 'asc'])
{
    $a = $a ?? '';
    $b = $b ?? '';
    $filtered = [];

    foreach ($contractsList as $contract) {
        if (strtolower($contract['party_type'] ?? '') !== strtolower($partyType))
            { continue;
}

        if (!empty($searchQuery)) {
            $found = false;
            foreach ($contract as $value) {
                if ($value && stripos($value, $searchQuery) !== false) {
                    $found = true;
                    break;
                }
            }
            if (!$found)
                { continue;
}
        }

        $filtered[] = $contract;
    }

    // Sort contracts
    usort($filtered, function ($a, $b) use ($sortConfig) {
        $filtered = $filtered ?? [];
        $sortConfig = $sortConfig ?? [];
        $aVal = $a[$sortConfig['key']] ?? '';
        $bVal = $b[$sortConfig['key']] ?? '';

        if (strpos($sortConfig['key'], 'date') !== false) {
            $aVal = strtotime($aVal);
            $bVal = strtotime($bVal);
        }

        $comparison = $aVal <=> $bVal;
        return $sortConfig['direction'] === 'desc' ? -$comparison : $comparison;
    });

    return $filtered;
}
function paginateArray($array, $page, $perPage)
{
    $startIndex = ($page - 1) * $perPage;
    return array_slice($array, $startIndex, $perPage);
}
$combinedIds = [];
if ($contracts) {
    foreach ($contracts as $contract) {
        $combinedIds[] = $contract['id'];
    }
}
if ($permissions) {
    foreach ($permissions as $permission) {
        $combinedIds[] = $permission['contract_id'];
    }
}
$combinedIds = array_unique($combinedIds);
$allowedContracts = $allowedContractsids = [];
if ($_SESSION['admin_type'] =='1') {
    $allowedContracts = $allcontracts;
    foreach ($allcontracts as $contract) {
        $allowedContractsids[] = $contract['id'];
    }
} else {
    foreach ($allcontracts as $contract) {
        if (in_array($contract['id'], $combinedIds)) {
            $allowedContracts[] = $contract;
            $allowedContractsids[] = $contract['id'];
        }
    }
}
$today = new DateTime();
$activeContracts = $activeContractsids = [];
$expiredContracts = $expiredContractsids =  [];
foreach ($allowedContracts as $contract) {
    if ($contract['is_active'] == 0 && (!$contract['end_date'] || new DateTime($contract['end_date']) >= $today)) {
            $activeContracts[] = $contract;
            $activeContractsids[] = $contract['id'];
        }

    if ($contract['end_date'] && new DateTime($contract['end_date']) < $today) {
        $expiredContracts[] = $contract;
        $expiredContractsids[] = $contract['id'];
    }
}
$filter = $_GET['filter'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';
$sortKey = $_GET['sort_key'] ?? 'contract_name';
$sortDirection = $_GET['sort_direction'] ?? 'asc';
$currentPage = (int) ($_GET['page'] ?? 1);
$itemsPerPage = 15;
$sortConfig = ['key' => $sortKey, 'direction' => $sortDirection];
$filteredContracts = filterAndSortContracts($allowedContracts, "customer", $searchQuery, $sortConfig);
$vendorContracts = filterAndSortContracts($allowedContracts, "vendor", $searchQuery, $sortConfig);


if ($filter === 'active') {
    $filteredContracts = array_filter($filteredContracts, function ($contract) use ($today) {
        return !$contract['end_date'] || new DateTime($contract['end_date']) >= $today;
    });
    $vendorContracts = array_filter($vendorContracts, function ($contract) use ($today) {
        return !$contract['end_date'] || new DateTime($contract['end_date']) >= $today;
    });
} elseif ($filter === 'expired') {
    $filteredContracts = array_filter($filteredContracts, function ($contract) use ($today) {
        $contract = $contract ?? [];
        return $contract['end_date'] && new DateTime($contract['end_date']) < $today;
    });
    $vendorContracts = array_filter($vendorContracts, function ($contract) use ($today) {
        $contract = $contract ?? [];
        return $contract['end_date'] && new DateTime($contract['end_date']) < $today;
    });
}
$paginatedFilteredContracts = paginateArray($filteredContracts, $currentPage, $itemsPerPage);
$paginatedVendorContracts = getLatestContracts($vendorContracts, 15);
$expiringCustomerContracts = getContractsByExpiry($filteredContracts, 60, 90);
$expiringVendorContracts = getContractsByExpiry($vendorContracts, 60, 90);
$totalCustomerPages = ceil(count((array)$filteredContracts) / $itemsPerPage);
$totalVendorPages = ceil(count((array)$vendorContracts) / $itemsPerPage);




function getLatestContracts($contracts, $limit = 15)
{
    $a = $a ?? '';
    $b = $b ?? '';
    usort($contracts, function ($a, $b) {
        $dateA = strtotime(!empty($a['updated_date']) ? $a['updated_date'] : $a['created_date']);
        $dateB = strtotime(!empty($b['updated_date']) ? $b['updated_date'] : $b['created_date']);
        return $dateB <=> $dateA;
    });
    return array_slice($contracts, 0, $limit);
}


?>

<div class="row">
    <from method="post" id="dashboard_form">
        <div class="col-lg-2">
            <select aria-label="LOCID"  name="LOCID" id="loc_id" class="form-select js-example-basic-single" onchange="changeLocations(this.value)">
                <option value="">All Locations</option>
                <?php foreach ($locations as $locn) { ?>
                    <option <?php echo ($locn['id'] == $_GET['LOCID'])?'selected' : '' ?> value="<?php echo $locn['id']; ?>">
                        <?php echo $locn['location_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </from>
</div>
<div class="mt-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
        <div class="col">
            <div class="card prod-p-card card-red">
                <div class="card-body cursor-pointer" onclick="getDynamicContractList('<?php echo urlencode(json_encode($allowedContractsids)); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <div class="row align-items-center m-b-30">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Contracts</h6>
                            <h3 class="m-b-0 f-w-700 text-white"><?= count((array)$allowedContracts) ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-file text-c-red f-18"></i>
                        </div>
                    </div>
                    <p class="m-b-0 text-white">Total Contracts</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card prod-p-card card-blue">
                <div class="card-body cursor-pointer" onclick="getDynamicContractList('<?php echo urlencode(json_encode($activeContractsids)); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <div class="row align-items-center m-b-30">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Active</h6>
                            <h3 class="m-b-0 f-w-700 text-white"><?= count((array)$activeContracts) ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-check text-c-blue f-18"></i>
                        </div>
                    </div>
                    <p class="m-b-0 text-white">Total Active Contracts</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card prod-p-card card-yellow">
                <div class="card-body cursor-pointer" onclick="getDynamicContractList('<?php echo urlencode(json_encode($expiredContractsids)); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <div class="row align-items-center m-b-30">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Expired</h6>
                            <h3 class="m-b-0 f-w-700 text-white"><?= count((array)$expiredContracts) ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-times text-c-yellow f-18"></i>
                        </div>
                    </div>
                    <p class="m-b-0 text-white">Total Expired Contracts</p>
                </div>
            </div>
        </div>
        <?php if(CONTRACT_CUSTOMERS != "N"){ ?>
        <div class="col">
            <a href="index.php?option=COM_LIST_CUSTOMERS">
            <div class="card prod-p-card card-green">
                <div class="card-body">
                    <div class="row align-items-center m-b-30">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Total Customers</h6>
                            <h3 class="m-b-0 f-w-700 text-white"><?= count((array)$allcustomers) ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-user text-c-green f-18"></i>
                        </div>
                    </div>
                    <p class="m-b-0 text-white">Total Customers</p>
                </div>
            </div>
        </a>
        </div>
    <?php } ?>
        <div class="col">
            <a href="index.php?option=COM_LIST_VENDORS">
            <div class="card prod-p-card card-cyan">
                <div class="card-body cursor-pointer">
                    <div class="row align-items-center m-b-30">
                        <div class="col">
                            <h6 class="m-b-5 text-dark">Total Vendors</h6>
                            <h3 class="m-b-0 f-w-700 text-dark"><?= count((array)$allvendors) ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-user text-c-dark f-18"></i>
                        </div>
                    </div>
                    <p class="m-b-0 text-dark">Total Vendors</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Overview</h5>
                </div>
                <div class="card-block">
                    <div id="morris-bar-chart"></div>
                </div>
            </div>
        </div>

    </div>
</div>
<ul class="nav md-tabs infisuite_tab mb-2">
    <?php if(CONTRACT_CUSTOMERS != "N"){ ?>
    <li class="nav-item">
        <a class="nav-link " data-bs-toggle="tab" href="#customers" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti ti-user"></i> Customers</a>
    </li>
<?php } ?>
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#vendors" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti ti-user"></i> Vendors</a>
    </li>
</ul>
<div class="tab-content tabs">
    <div class="tab-pane " id="customers" role="tabpanel">
        <div class="row mt-4">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5>Customer Contracts</h5>
                        <a href="index.php?option=COM_LIST_CONTRACTS"  class=" b-b-primary text-primary float-right">View all</a>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered" id="contractTable">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Contract</th>
                                    <th scope="col">Contract Type</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Contract ID</th>


                                    <th scope="col">Progress</th>
                                </tr>

                                <?php if (!empty($paginatedFilteredContracts)): ?>
                                    <?php foreach ($paginatedFilteredContracts as $index => $contract): ?>
                                        <?php
                                        $expired = isExpired($contract['end_date']);
                                        $statusMatch = null;
                                        foreach ($allstatus as $status) {
                                            if ($status['name'] === $contract['contract_status']) {
                                                $statusMatch = $status;
                                                break;
                                            }
                                        }
                                        $background = $statusMatch['background_color'] ?? '#28a745';
                                        $color = $statusMatch['color'] ?? '#fff';
                                        $isAmc = ($contract['is_amc_contract'] ?? 'N') === 'Y';
                                        ?>
                                        <tr>
                                            <td><?= ($currentPage - 1) * $itemsPerPage + $index + 1 ?></td>
                                            <td><a class="text-primary" href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo deocdeId("id:".$contract['id']);?>" ><?php echo htmlspecialchars($contract['contract_name']." (".$contract['contract_type'].")"); ?></a>
                                            </td>
                                            <td>
                                                <?php if ($isAmc) { ?>
                                                    <span class="badge bg-info">AMC</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-secondary">Standard</span>
                                                <?php } ?>
                                            </td>
                                            <td><?= fromDate($contract['start_date']) ?></td>
                                            <td>
                                                <?= fromDate($contract['end_date']) ?>
                                                <?php if ($expired): ?>
                                                    <span class="form-label label label-danger">Expired</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($contract['transaction_no'] ?? 'Not Provided') ?></td>
                                           <?php  ?>

                                            <td>
                                                <?php $progress = calculateProgress($contract, $allpermissions, $alldocuments); ?>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar"
                                                        style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        <?= $progress ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No vendor contracts found</td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 accordion-block">
                <div class="card">
                    <div class="card-header">
                        <h5>Expiring Contracts</h5>
                    </div>
                    <div class="card-body">
                        <div id="accordionExpiring" role="tablist" aria-multiselectable="true">
                            <?php foreach ($sections as $index => $section): ?>
                                <?php $contracts = getContractsByExpiry($vendorContracts, $section['min'], $section['max']); ?>
                                <div class="accordion-panel mt-4">
                                    <div class="accordion-heading" role="tab" id="headingExpiring<?= $index ?>">
                                        <h3 class="card-title accordion-title p-0 bg-gray">
                                            <a class="accordion-msg waves-effect waves-dark border-0"
                                                data-bs-toggle="collapse" data-parent="#accordionExpiring"
                                                href="#collapseExpiring<?= $index ?>"
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                                aria-controls="collapseExpiring<?= $index ?>">
                                                <?= htmlspecialchars($section['title']) ?>
                                            </a>
                                        </h3>
                                    </div>
                                    <?php
                                    $contractsWithoutRenew = array_filter((array)$contracts, function ($contract) {
                                        return empty($contract['renew_data']);
                                    });
                                    ?>
                                    <div id="collapseExpiring<?= $index ?>"
                                        class="panel-collapse collapse <?= $index === 0 ? 'show' : '' ?>" role="tabpanel"
                                        aria-labelledby="headingExpiring<?= $index ?>">
                                        <div class="accordion-content accordion-desc p-0 custom-scroll" style="max-height: 300px;">
                                            <div class="table-responsive">
                                                <?php if (!empty($contractsWithoutRenew)): ?>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th scope="col">Contract</th>
                                                            <th scope="col">Days Left</th>
                                                            <th scope="col">End Date</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                        <?php if (!empty((array)$contracts)): ?>
                                                            <?php foreach ($contracts as $contract): ?>
                                                                <?php
                                                                $endDate = new DateTime($contract['end_date']);
                                                                $today = new DateTime();
                                                                $daysLeft = ceil(($endDate->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24));
                                                                ?>
                                                                <tr>
                                                                    <td><a class="text-primary" href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo deocdeId("id:".$contract['id']);?>"><?php echo htmlspecialchars($contract['contract_name']." (".$contract['contract_type'].")") ?></a>
                                                                    </td>
                                                                    <td><?= $daysLeft ?> Days Left</td>
                                                                    <td><span
                                                                            class="badge bg-danger text-white"><?php //echo  fromDate($contract['end_date']) ?></span>
                                                                    </td>
                                                                    <td>
                                                                        <button onclick="openRenewPage('<?php echo  deocdeId('id:'.$contract['id']); ?>')"
                                                                            class="btn btn-primary btn-sm">
                                                                            Renew
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <?php  endforeach; ?>
                                                        <?php endif; ?>
                                                    </table>
                                                <?php else: ?>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th scope="col" colspan="4" class="text-center">No contracts Found</th>
                                                        </tr>
                                                    </table>
                                                <?php endif;  ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;  ?>
                        </div>


                        <h5 class="mt-4">Expired Contracts</h5>
                        <div id="accordionExpired_2" role="tablist" aria-multiselectable="true">
                            <?php foreach ($expiredSections as $index => $section): ?>
                                <?php
                                $today = new DateTime();
                                $today->setTime(0, 0, 0);
                                $pastDate = clone $today;
                                $pastDate->modify("-{$section['days']
} days");

                                $startDate = clone $today;
                                $startDate->modify("-{$section['start_days']
} days");

                                $expiredContracts = [];
                                foreach ($allowedContracts as $contract) {
                                    if (!$contract['end_date'] || strtolower($contract['party_type'] ?? '') !== 'vendor')
                                        { continue;
}
                                    $endDate = new DateTime($contract['end_date']);
                                    $endDate->setTime(0, 0, 0);
                                    if ($endDate < $startDate && $endDate >= $pastDate) {
                                        $expiredContracts[] = $contract;
                                    }
                                }
                                ?>
                                <div class="accordion-panel mt-4">
                                    <div class="accordion-heading" role="tab" id="headingExpired<?= $index ?>">
                                        <h3 class="card-title accordion-title p-0 bg-gray">
                                            <a class="accordion-msg waves-effect waves-dark border-0"
                                                data-bs-toggle="collapse" data-parent="#accordionExpired"
                                                href="#collapseExpired<?= $index ?>"
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                                aria-controls="collapseExpired<?= $index ?>">
                                                <?= htmlspecialchars($section['title']) ?>
                                                (<?= count((array)$expiredContracts) ?>)
                                            </a>
                                        </h3>
                                    </div>
                                    <div id="collapseExpired<?= $index ?>" class="panel-collapse collapse" role="tabpanel"
                                        aria-labelledby="headingExpired<?= $index ?>">
                                        <div class="accordion-content accordion-desc p-0 custom-scroll" style="max-height: 300px;">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th scope="col">Contract</th>
                                                        <th scope="col">Days Expired</th>
                                                        <th scope="col">End Date</th>
                                                    </tr>
                                                    <?php if (!empty($expiredContracts)): ?>
                                                        <?php foreach ($expiredContracts as $contract): ?>
                                                            <?php
                                                            $endDate = new DateTime($contract['end_date']);
                                                            $daysExpired = ceil(($today->getTimestamp() - $endDate->getTimestamp()) / (60 * 60 * 24));
                                                            ?>
                                                            <tr>
                                                                <td><a class="text-primary" href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo deocdeId("id:".$contract['id']);?>"><?= htmlspecialchars($contract['contract_name']." (".$contract['contract_type'].")") ?></a>
                                                                </td>
                                                                <td><?= $daysExpired ?> Days Ago</td>
                                                                <td><span
                                                                        class="badge bg-danger text-white"><?= fromDate($contract['end_date']) ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">No contracts expired in
                                                                this
                                                                range</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php  ?>
<script src="assets/js/custom_view_contract_dashboard.js"></script>
<script>
    var BarData = <?php echo json_encode($chartData); ?>;
    var AreaData = <?php echo json_encode($contractsOverviewData); ?>;


</script>
<script>
    function openRenewPage(contractId) {
        var currentUrl = window.location.href;
        currentUrl = currentUrl.replace(/([?&])renew=.*?(&|$)/i, '$1');
        currentUrl = currentUrl.replace(/([?&])Rid=.*?(&|$)/i, '$1');
        currentUrl = currentUrl.replace(/([?&])option=.*?(&|$)/i, '$1');
        var separator = currentUrl.includes('?') ? '&' : '?';
        var newUrl = currentUrl + separator + 'option=COM_INSERT_CONTRACTS&renew=Y&Rid=' + contractId;
        window.location.href = newUrl;
    }

    function changeLocations(value){
     window.location.href = "index.php?LOCID=" + value;
    }

    function getDynamicContractList(ids){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#dynamic_popupxxl').html(loader);
        $('#kt_modal_dynamic_popupxxl').modal("show");
        var loc_id = $("#loc_id").val();

    jQuery.ajax({
        url: "views/custom_view_contract_dashboard_popup.php",
        data:'contract_ids='+ids+'&LOCID='+loc_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popupxxl').html(data);
        },
        error:function (){}
    });

        console.log(ids);
    }
</script>
