<?php
session_start();
$_SESSION['apply_js']['multiselest'] = 1;
$message = '';
if ($_POST['submit'] == 'insert') {

    $url  = API . "?mode=saveVisitorOut&method=post";
    $resp = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($resp, true);
    if (!empty($result) && ($result['status'] ?? '') === 'success') {
        $message = 'Checkout successfully';
        header("location:index.php?option=COM_LIST_VISITOR_LOGS");
    } else {
        $message =  'Checkout failed';
    }

}

$url = API . "?mode=getVisitorPassDetails";
$che = callAPI($url);
$data = json_decode($che, 1);
$building_gates_res = $data['building_gates_res'] ?? array();
$service_company_res = $data['service_company_res'] ?? array();
$delivery_company_res = $data['delivery_company_res'] ?? array();
$selected_gate_id   = $_SESSION['gate_id'] ?? 1;
?>

<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>
<link rel="stylesheet" href="assets/css/custom_view_visitor_out.css">

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Visitor OUT</h5>
            <div class="card-header-right">

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="card-infisuite mt-2">
            <!-- Visitor History Search -->
            <div class="card mt-3">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                            <span class="form-label">Name / Phone / Visitor ID / ID Card No.</span>
                            <input aria-label="e.g., Koteswara, 9123456789, VST-00123, AADHAAR-xxxx" type="text" class="form-control" id="historySearchText" placeholder="e.g., Koteswara, 9123456789, VST-00123, AADHAAR-xxxx">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <span class="form-label">Date</span>
                            <input aria-label="History Search Date" type="date" class="form-control" id="historySearchDate">
                        </div>
                        <input type="hidden" name="location_id" id="location_id" value="<?php echo $_SESSION['location_id'] ?? 1; ?>" >
                        <div class="col-lg-2 col-md-2 col-sm-6 col-12">
                            <button type="button" class="btn btn-primary w-100 p-2" id="historySearchBtn">
                                <i class="fa fa-search me-1"></i> Serach
                            </button>
                        </div>

                    </div>
                    <small class="text-muted d-block mt-2">
                        Tip: Enter any one field or just a date. If only date is given, results for that date are shown.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
            <div class="card">
                <div class="card-body">


                    <div id="visitorHistoryWrap" class="visitor-history-wrap">
                        <div class="bg-gray">
                            <div class="d-flex align-items-center">
                                <div class="col-auto">
                                    <span alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center">SW</span>
                                </div>
                                <div class="col-lg-10">
                                    <a class="f-w-600 f-18 text-capitalize d-block" href="">Swiggy</a>
                                    <p class="f-12 f-w-600 text-muted mb-0">VISIT002
                                        <b class="float-end d-inline-block">
                                            <a href="tel:12345678910">Mob:- 8909090767</a>
                                        </b>
                                    </p>
                                </div>
                            </div>
                            <div class="table-responsive mt-2 f-12">
                                <table class="table table-sm">
                                    <tr>
                                        <th scope="col">Visit Type</th>
                                        <th scope="col">Check in/Out</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    <tr>
                                        <td>Delivery</td>
                                        <td>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary js-view-visitor p-2 pt-1 pb-1" data-id="39" data-visit-type="delivery">
                                                <i class="fa fa-eye"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="bg-gray">
                            <div class="d-flex align-items-center">
                                <div class="col-auto">
                                    <span alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center">SV</span>
                                </div>
                                <div class="col-lg-10">
                                    <a class="f-w-600 f-18 text-capitalize d-block" href="">Sneha Varma</a>
                                    <p class="f-12 f-w-600 text-muted mb-0">VISIT002
                                        <b class="float-end d-inline-block">
                                            <a href="tel:12345678910">Mob:- 8909090767</a>
                                        </b>
                                    </p>
                                </div>
                            </div>
                            <div class="table-responsive mt-2 f-12">
                                <table class="table table-sm">
                                    <tr>
                                        <th scope="col">Visit Type</th>
                                        <th scope="col">Check in/Out</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    <tr>
                                        <td>Visitor</td>
                                        <td>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary js-view-visitor p-2 pt-1 pb-1" data-id="39" data-visit-type="delivery">
                                                <i class="fa fa-eye"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="bg-gray">
                            <div class="d-flex align-items-center">
                                <div class="col-auto">
                                    <span alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center">RA</span>
                                </div>
                                <div class="col-lg-10">
                                    <a class="f-w-600 f-18 text-capitalize d-block" href="">Rajesh</a>
                                    <p class="f-12 f-w-600 text-muted mb-0">VISIT002
                                        <b class="float-end d-inline-block">
                                            <a href="tel:12345678910">Mob:- 8909090767</a>
                                        </b>
                                    </p>
                                </div>
                            </div>
                            <div class="table-responsive mt-2 f-12">
                                <table class="table table-sm">
                                    <tr>
                                        <th scope="col">Visit Type</th>
                                        <th scope="col">Check in/Out</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    <tr>
                                        <td>Doctor</td>
                                        <td>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                            <p class="m-0 f-12">2026-02-03 15:01:05</p>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary js-view-visitor p-2 pt-1 pb-1" data-id="39" data-visit-type="delivery">
                                                <i class="fa fa-eye"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8 col-12">
            <div class="card">
                <div class="card-body">
                    <div id="visitorResult"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
<!-- ADD -->
<script type="application/json" id="visitor-out-config">
<?php echo json_encode([
    'serviceCompanyRes'  => $service_company_res ?? [],
    'deliveryCompanyRes' => $delivery_company_res ?? []
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
<script src="assets/js/custom_view_visitor_out.js"></script>

