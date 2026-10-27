<?php
$vendor_id = $_POST['vendor_id'] ?? ($vendor_id ?? '');
$transaction_table = $_POST['transaction_table'] ?? ($transaction_table ?? 'vendors');
$vtype = $_POST['vtype'] ?? ($_GET['VTYPE'] ?? '');
// - non-empty id      -> VIEW (read-only summary)
$edittype = $_POST['edittype'] ?? (empty($vendor_id) ? 'ADD' : '');
$isAdd = ($edittype == 'ADD');
if ($_POST['ajaxcall'] == "Y") {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $vendor_id = $_POST['vendor_id'];
    $transaction_table = $_POST['transaction_table'];
    $edittype = $_POST['edittype'];
}
$url = API . "?mode=getvendorsdetails&transaction_table=" . $transaction_table . "&id=" . $vendor_id . "&admin_type=" . $_SESSION['admin_type'] . "&edittype=" . $edittype."&working_role_short_code=".$_SESSION['working_role_short_code']."&VRRid=".$VRRid;
$response = callAPI($url);
$result = json_decode($response, true);

$vendor_types = $result['vendor_types'];
$role = $result['vendors'];
$vendordetail = $result['vendors'];

$vendor_registration_requests = $result['vendor_registration_requests'];

$vendordetail = (array) $vendordetail;
// ensure all fields exist
$ensure = [
    'company',
    'website_url',
    'company_trading_name',
    'company_subsidiary_division',
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'phone',
    'mobile',
    'image',
    'billing_address',
    'billing_country',
    'billing_state',
    'billing_city',
    'billing_post_code',
    'shipping_address',
    'shipping_country',
    'shipping_state',
    'shipping_city',
    'shipping_post_code',
    'vendor_tax_reg_no',
    'business_type',
    'duns_no',
    'company_registration_number',
    'company_registration_country',
    'gstin',
    'pan_no',
    'tan_number',
    'msme_register_yes_no',
    'msme_number',
    'incoterm_id',
    'bank_name',
    'bank_address',
    'bank_country',
    'bank_state',
    'bank_city',
    'bank_post_code',
    'bank_beneficiary_name',
    'bank_account_no',
    'account_type',
    'ifsc_code',
    'routing_aba_dfi_ach',
    'routing_wire_Code',
    'swift_bic_code',
    'iban_code',
    'bank_query_person_name',
    'bank_query_contact_no',
    'bank_query_email_id',
    'title',
    'suffix',
    'gsttype',
    'parent_vendor',
    'cst_reg_no',
    'pref_payment_method',
    'preferred_deliver_method',
    'opening_balance',
    'asof',
    'pay_with_me',
    'credit_limit',
    'default_discount_perc',
    'payment_discount_perc',
    'sales_type',
    'vendor_po_notes',
    'vendor_other_notes',
    'subsidiary_vendor_check',
    'tds_apply'
];

foreach ($ensure as $k) {
    if (!array_key_exists($k, $vendordetail)) {
        $vendordetail[$k] = '';
    }
}

// default toggles
if ($isAdd) {
    $vendordetail['msme_register_yes_no'] = 'N';
    $vendordetail['subsidiary_vendor_check'] = 'N';
    $vendordetail['tds_apply'] = 1;
}

// normalize vendor_questions
$vendor_questions = $vendordetail['vendor_questions'];
if (!is_array($vendor_questions)) {
    $vendor_questions = json_decode((string) $vendor_questions, true) ?: [];
}
if ($isAdd && empty($vendor_questions)) {
    $vendor_questions = [
        'radio_associate' => 'N',
        'associated_team_detail' => '',
        'quality_system' => 'N',
        'quality_system_certificate' => [
            'name' => ['1' => '', '2' => ''],
            'fromdate' => ['1' => '', '2' => ''],
            'todate' => ['1' => '', '2' => ''],
        ],
        'small_business_registerd' => ['100' => '', '101' => '', '102' => ''],
        'supply_Chain_responsibility' => [
            '1' => 'N',
            '2' => 'N',
            '3' => 'N',
            '4' => 'N',
            '4_a' => 'N',
            '4_b' => 'N',
            '4_c' => 'N',
            '5' => 'N',
            '5_a' => 'N',
            '6' => 'N',
            '7' => 'N'
        ],
        'terms_conditions' => ['1' => 'N', '2' => 'N'],
    ];
}

$vendordetail['vendor_questions'] = $vendor_questions;

$vendor_id = $vendordetail['id'];
$aggrements = $result['vendor_agreements'];
$vendor_documents = $result['vendor_documents'];
$document_lists = $result['document_lists'];

$payment_method = $result['payment_method'];
$salestype = $result['salestype'];
$emplist = $result['employess'];
$countrieslist = $result['countries'];
$states = $result['states'];
$cities = $result['cities'];
foreach ($result['vendorslist'] as $vend) {
    $vendorslist[$vend['id']] = $vend;
}
$paymenttype = $result['paymenttype'];
$vendor_type = $result['vendor_type'];
$countrylist = $stateslist = $citieslist = array();
foreach ($countrieslist as $country_list) {
    $countrylist[$country_list['id']] = $country_list['country_name'];
}
foreach ($states as $states_list) {
    $stateslist[$states_list['id']] = $states_list['state_name'];
}
foreach ($cities as $cities_list) {
    $citieslist[$cities_list['id']] = $cities_list['city_name'];
}

$customer_vendor_gst_billing_details = $result['customer_vendor_gst_billing_details'];
$gstregistrationtype = $result['gstregistrationtype'];
$vendor_contact_info_type = $result['vendor_contact_info_type'];
$bank_acoount_type = $result['bank_acoount_type'];
$other_contact_information = json_decode($vendordetail['other_contact_information'], 1);
$company_types = array();
foreach ($result['company_types'] as $ctype) {
    $company_types[$ctype['id']] = $ctype;
}

$payment_terms = array();
foreach ($result['payment_terms'] as $payt) {
    $payment_terms[$payt['id']] = $payt; //payment_term_name
}
$incoterms = array();
foreach ($result['incoterms'] as $payt) {
    $incoterms[$payt['id']] = $payt; //incoterm_name
}

$transaction_activity_schedule = $result['transaction_activity_schedule'];
$vendor_verifications = json_decode($vendordetail['vendor_verifications'], 1);
$vendor_display_fields = $result['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'], 1);

$item_categories = $result['item_categories'];
$items = $result['items'];

function answerYesOrNo($answ)
{
    $return = "<span class='text-danger'>Pending<span>";
    if ($answ == "Y") {
        $return = "Yes";
    } elseif ($answ == "N") {
        $return = "No";
    }
    return $return;
}
$profilereadiness = array("Business Information" => array('company', 'website_url', 'company_trading_name', 'company_subsidiary_division', 'first_name', 'middle_name', 'last_name', 'phone', 'email', 'phone', 'billing_address', 'billing_country', 'billing_state', 'billing_city', 'billing_post_code', 'contact_person', 'mobile', 'shipping_address', 'shipping_country', 'shipping_state', 'shipping_city', 'shipping_post_code'), "Company Information" => array('vendor_tax_reg_no', 'business_type', 'duns_no', 'company_registration_number', 'company_registration_country', 'payment_terms', 'gstin', 'pan_no', 'tan_number', 'incoterm_id', 'image', 'msme_number'), "Banking Information" => array('bank_name', 'bank_address', 'bank_country', 'bank_state', 'bank_city', 'bank_post_code', 'bank_beneficiary_name', 'bank_account_no', 'account_type', 'routing_aba_dfi_ach', 'routing_wire_Code', 'swift_bic_code', 'iban_code', 'bank_query_person_name', 'bank_query_contact_no', 'bank_query_email_id', 'ifsc_code'), "Note" => array('vendor_po_notes', 'vendor_other_notes'));

$File_Uploads_Path = "uploads/all/";

$document_uploads_Path= "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_table."/".$vendor_id."/Documents/";
$vendor_admin_application_users =  $result['vendor_admin_application_users'];
$vendor_hr_application_users =  $result['vendor_hr_application_users'];
$vendor_finanace_application_users =  $result['vendor_finanace_application_users'];
$vendor_project_application_users =  $result['vendor_project_application_users'];
$vendor_location_application_users =  $result['vendor_location_application_users'];
$vendor_purchase_application_users =  $result['vendor_purchase_application_users'];
$vendor_legal_application_users =  $result['vendor_legal_application_users'];
$locations = $result['locations'];

?>

<script type="text/javascript" src="../assets/js/custom_insert_vendors_and_vendor_registers_ajax.js"></script>
<script src="../assets/js/signature_pad.umd.js"></script>
<?php if ($_POST['edittype'] == 'SendVerificationProfile') {
    $notsendtoapprove = 0; ?>
    <div class="modal-header">
        <h5 class="modal-title">Vendor details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Profile Completion</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-md">
                            <tbody>
                                <?php foreach ($profilereadiness as $keylabel => $profilereadines) {
                                    $complete_field = $total_field = 0;
                                    $total_field = count((array) $profilereadines);
                                    foreach ($profilereadines as $profilereadines) {
                                        if ($vendordetail[$profilereadines] != "") {
                                            $complete_field++;
                                        }
                                    }
                                    $percentage = (($complete_field / $total_field) * 100);
                                    ?>
                                    <tr>
                                        <th scope="col">
                                            <h6 class="mb-0 f-14"><?php echo $keylabel; ?></h6>
                                        </th>
                                        <th scope="col" style="text-align: right;">
                                            <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
                                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                    <span class="f-12 f-w-600">Completion </span>
                                                    <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar progress-bar-<?php echo round($percentage) >= 50 ? "success" : "danger"; ?>"
                                                            role="progressbar"
                                                            style="width:  <?php echo round($percentage); ?>%;"
                                                            aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <?php
                $popuprdv = json_decode($_POST['required_document_verifications'],1);
                $transactionactivityschedule = $pendingtask = array();
                foreach ($transaction_activity_schedule as $tas) {
                    if ($tas['current_status'] == ST_ACTIVE) {
                        $notsendtoapprove = 1;
                        $transactionactivityschedule[ST_ACTIVE][] = $tas;
                        $pendingtask[] = $tas;
                    } else {
                        $check_list = json_decode($tas['check_list'], 1);

                        $alldone = $requerdcnt = $mandatory = 0;
                        foreach ($check_list as $check_lits) {
                            if($tas['task_id'] == "61"){

                                if ($vendordetail['msme_register_yes_no'] == "Y" && strpos(trim($check_lits['name']), "MSME Certificate") !== false && ($check_lits['checklist_completed'] != 'Y' || !isset($check_lits['checklist_completed']))) {
                                    $check_lits['mandatory_checklist'] = "Y";
                                    $tas['task_name'] = $tas['task_name']." -- ".$check_lits['name'];
                                }
                                if ($vendor_questions['quality_system'] == "Y" && strpos(trim($check_lits['name']), "Quality Certificates") !== false && ($check_lits['checklist_completed'] != 'Y'  || !isset($check_lits['checklist_completed']))) {
                                    $check_lits['mandatory_checklist'] = "Y";
                                    $tas['task_name'] = $tas['task_name']." -- ".$check_lits['name'];
                                }
                                if ($vendordetail['business_type'] == "5" && strpos(trim($check_lits['name']), "Establishment Act") !== false && ($check_lits['checklist_completed'] != 'Y'  || !isset($check_lits['checklist_completed']))) {
                                    $check_lits['mandatory_checklist'] = "Y";
                                    $tas['task_name'] = $tas['task_name']." -- ".$check_lits['name'];
                                }
                            }

                            if ($check_lits['checklist_completed'] == 'Y') {
                                $alldone++; // solve count
                            }
                            if ($check_lits['mandatory_checklist'] == 'Y') {
                                $requerdcnt++; // mandatory count
                            }
                            if ($check_lits['mandatory_checklist'] == 'Y' && $check_lits['checklist_completed'] != 'Y') {
                                $mandatory++; // all mandatory solve requerd
                            }

                        }
                        if ($requerdcnt <= $alldone && $mandatory == '0') {
                            $transactionactivityschedule['SEND_FOR_APPROVE'][] = $tas;
                        } else {
                            $transactionactivityschedule['PENDDING_TASK'][] = $tas;
                            $pendingtask[] = $tas;
                            $notsendtoapprove = 1;
                        }
                    }
                }

                ?>
                <div class="row">
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo count((array) $transaction_activity_schedule); ?></h5>
                            <p class="m-b-0 f-12">Total Task</p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo count((array) $transactionactivityschedule[ST_ACTIVE]); ?>
                            </h5>
                            <p class="m-b-0 f-12">Yet To Start</p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0">
                                <?php echo count((array) $transactionactivityschedule['PENDDING_TASK']); ?>
                            </h5>
                            <p class="m-b-0  f-12">In Progress</p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0">
                                <?php echo count((array) $transactionactivityschedule['SEND_FOR_APPROVE']); ?>
                            </h5>
                            <p class="m-b-0 f-12">Ready For Review</p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="card">
                        <div class="card-header">
                            <h5>Pending Tasks</h5>
                            <div class="card-header-right">

                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            if ($vendor_questions['terms_conditions']['1'] != "Y" || $vendor_questions['terms_conditions']['2'] != "Y") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Accept Terms And Conditions In Questions Tab');
                            }
                            if ($vendordetail['pan_no'] == "" && in_array('PAN',(array)$popuprdv)) {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide PAN No.');
                            }
                            if ($vendordetail['gstin'] == "" && in_array('GSTIN',(array)$popuprdv)) {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide GSTIN No.');
                            }
                            if ($vendordetail['bank_account_no'] == "" && in_array('BANK',(array)$popuprdv)) {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide Bank Account No.');
                            }
                            if ($vendordetail['ifsc_code'] == "" && in_array('BANK',(array)$popuprdv)) {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide IFSC Code');
                            }
                            if ($vendordetail['msme_register_yes_no'] == "Y" && $vendordetail['msme_number'] == "") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide MSME Number');
                            }
                            if ($vendordetail['billing_address'] == "") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide Billing Address');
                            }
                            if ($vendordetail['shipping_address'] == "") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide Shipping Address');
                            }
                            if ($vendordetail['billing_post_code'] == "") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide Billing Post Code');
                            }
                            if ($vendordetail['shipping_post_code'] == "") {
                                $notsendtoapprove = 1;
                                $pendingtask[] = array('task_name' => '<span class="text-danger">*</span> Please Provide Shipping Post Code');
                            }
                            ?>
                            <?php if (count((array) $pendingtask) != 0) { ?>
                                <table class="table table-hover m-b-0 ">
                                    <?php foreach ($pendingtask as $pendingtask) { ?>
                                        <tr>
                                            <th scope="col" class="p-2"><?php echo $pendingtask['task_name']; ?></th>
                                        </tr>
                                    <?php } ?>
                                </table>
                            <?php } else { ?>
                                <div class="card-px text-center py-20 mt-2">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Pending Tasks
                                    </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                            style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
        <?php if ($notsendtoapprove == 0) { ?>
            <button type="button" class="btn btn-primary waves-effect waves-light UpdateSendVerificationProfileStatus" id="popsubmitforreview_btn"
                >Submit For Review</button>
        <?php } else {
            echo "<span class='text-danger'><i class='ti-alert'></i>" . $_SESSION['error_message']['9126'] . "</span>";
        } ?>
    </div>

<?php } elseif ($edittype == 'EDIT' || $edittype == 'ADD') {  ?>
    <div class="card">
        <?php
        if ($isAdd): ?>
            <style>
                /* scope to the form so other pages aren’t affected */
                #submitvendordetailform .custom_readonly {
                    pointer-events: auto !important;
                    background-color: #fff !important;
                }

                /* Simple form styling */
                .vendor-form-content h5 {
                    color: #007bff;
                    font-weight: 600;
                    margin-top: 2rem;
                    margin-bottom: 1rem;
                    padding-bottom: 0.5rem;
                    border-bottom: 2px solid #e9ecef;
                }

                .vendor-form-content h5:first-child {
                    margin-top: 0;
                }

                .vendor-form-content .row {
                    margin-bottom: 1rem;
                }

                /* Hide optional sections by default */
                .attachments-section,
                .employees-section,
                .no-hiring-section {
                    display: none !important;
                }

                /* Show items section */
                .items-section {
                    display: block !important;
                }

                /* Optional fields that can be hidden */
                .optional-field {
                    display: none;
                }

                /* Show required fields */
                .required-field {
                    display: block;
                }
            </style>
        <?php endif; ?>
        <form method="post" id="submitvendordetailform" enctype="multipart/form-data">
            <div class="text-right">
                <?php
                $submitText = $isAdd ? 'Save' : 'Update';
                $submitIntent = $isAdd ? 'Add' : 'Update';
                ?>
                <input type="hidden" name="submit_vendor" value="<?php echo $submitIntent; ?>" />
                <input type="hidden" name="vendor_user_id" value="<?php echo $vendordetail['user_id']; ?>" />
                <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>" />
                <input type="hidden" name="transaction_table" value="<?php echo $transaction_table; ?>" />
                <input type="hidden" name="hid" value="<?php echo $vendor_id; ?>" />

                <button type="button" class="btn btn-primary m-r-20 m-b-20 checkVendorValidationEmpty">
                    <i class="ti-check"></i> <?php echo $submitText; ?>
                </button>
                <span class="text-danger terms_conditions d-none" >* Please Accept Terms And Conditions In
                    Questions Tab</span>
            </div>
            <div class="card-block">

                <div class="vendor-form-content">

                    <h5 class="text-primary mb-3 border-bottom pb-2">Business Information</h5>
                    <div class="row">
                        <?php

                        $fieldaccesright = $vafield['business_information'];
                        ?>
                        <div class="form-group  col-lg-6">
                            <span class="form-label">Company Name <span class="text-danger">*</span></span>
                            <input aria-label="Input field" class="form-control checkCompanyNameAllReadyAvilableOrNot <?php echo $isAdd ? '' : 'custom_readonly'; ?>" type="text"
                                id="company" name="company"
                                value="<?php echo $vendordetail['company']; ?>" autocomplete="off" <?php echo $isAdd ? '' : 'readonly'; ?> required>
                            <span class="text-danger f-12 d-none" id="allreadycompanyname" ><span
                                    id="companynameerror" class="f-w-600"> </span> Company Name Already Register</span>
                            <div class="d-none text-danger" id="company_name_err"> Please Enter Company Name </div>
                        </div>
                        <?php
                        if ($fieldaccesright['field']['website_url'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['website_url'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-6 form-group ">
                                <span class="form-label">Corporate Website</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="website_url"
                                    name="website_url" value="<?php echo $vendordetail['website_url']; ?>" autocomplete="off">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['company_trading_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['company_trading_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-6 form-group ">
                                <span class="form-label">DBA(Trading Name)</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="company_trading_name"
                                    name="company_trading_name" value="<?php echo $vendordetail['company_trading_name']; ?>"
                                    autocomplete="off">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['company_subsidiary_division'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['company_subsidiary_division'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-6 form-group ">
                                <span class="form-label">Subsidiary / Division Of</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text"
                                    id="company_subsidiary_division" name="company_subsidiary_division"
                                    value="<?php echo $vendordetail['company_subsidiary_division']; ?>" autocomplete="off">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['first_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['first_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-2 ">
                                <span class="form-label">First Name <span class="text-danger">*</span></span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="first_name"
                                        name="first_name" value="<?php echo $vendordetail['first_name']; ?>" autocomplete="off"
                                        required>
                                </div>
                                <div class="d-none text-danger" id="first_name_err"> Please Enter First Name </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['middle_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['middle_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-2">
                                <span class="form-label">Middle Name</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="middle_name"
                                        name="middle_name" value="<?php echo $vendordetail['middle_name']; ?>"
                                        autocomplete="off">
                                </div>
                                <div class="d-none text-danger" id="middle_name_err"> Please enter middle name </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['last_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['last_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-2">
                                <span class="form-label">Last Name <span class="text-danger">*</span></span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="last_name"
                                        name="last_name" value="<?php echo $vendordetail['last_name']; ?>" autocomplete="off"
                                        required>
                                </div>
                                <div class="d-none text-danger" id="last_name_err"> Please Enter Last Name </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Email <span class="text-danger">*</span></span>
                            <div class="kt-input-icon">
                                <input aria-label="Input field" class="form-control checkalreadyavailableornotcustomerorvendordetails  <?php echo ($vendordetail['email'] == "") ? '' : 'custom_readonly'; ?>" id="email" name="email" type="email" data-type="EMAIL"
                                    value="<?php echo $vendordetail['email']; ?>" autocomplete="off" <?php echo ($vendordetail['email'] == "") ? '' : 'readonly'; ?> required>
                            </div>
                            <span class="text-danger f-14 d-none" id="emailerror" ></span>
                            <div class="d-none text-danger" id="email_err"> Please Enter Email </div>
                        </div>
                        <?php
                        if ($fieldaccesright['field']['phone'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['phone'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3">
                                <span class="form-label">Phone</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="number" id="phone"
                                        name="phone" value="<?php echo $vendordetail['phone']; ?>" autocomplete="off">
                                </div>
                                <div class="d-none text-danger" id="phone_err"> Please enter phone </div>
                            </div>
                        <?php } if ($fieldaccesright['field']['vendor_type_id'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['vendor_type_id'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3">
                                <span class="form-label">Vendor Type</span>
                                <div class="kt-input-icon">
                                    <select aria-label="Vendor Type"  name="vendor_type_id"     class="form-select js-example-basic-single selectVendorTypes">
                                        <?php foreach ($vendor_types as $vendortypes) { ?>
                                            <option <?php echo ($vendordetail['vendor_type_id'] == $vendortypes['id'] || ($vendordetail['vendor_type_id'] == "" && $vendordetail['vendor_type_id'] == 2)) ? 'selected' : '' ?> value="<?php echo $vendortypes['id']; ?>">
                                                <?php echo $vendortypes['type_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                </div>
                                <div class="d-none text-danger" id="vendor_type_id_err"> Please Vendor Type </div>
                            </div>
                        <?php } if ($fieldaccesright['field']['service_types'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['service_types'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3">
                                <span class="form-label">Service Type</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?> " type="text" id="service_types"
                                        name="service_types" value="<?php echo $vendordetail['service_types']; ?>" autocomplete="off">

                                </div>
                                <div class="d-none text-danger" id="phone_err_2"> Please Service Types</div>
                            </div>
                        <?php }
                         if ($fieldaccesright['field']['bp_code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bp_code'] != "E" ? "custom_readonly" : "";
                            ?>
                        <div class="form-group col-lg-3">
                            <span class="form-label">BP Code</span>
                            <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bp_code" name="bp_code" value="<?php echo $vendordetail['bp_code']; ?>" autocomplete="off">
                        </div>
                        <?php } if ($fieldaccesright['field']['location_id'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['location_id'] != "E" ? "custom_readonly" : "";
                            ?>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Locations</span>
                            <select aria-label="Location []"  name="location_id[]" class="form-select js-example-basic-multiple" multiple>
                                <?php $locationsid = json_decode($vendordetail['location_id'] ,1);
                                     foreach($locations as $locval) { ?>
                                    <option <?php echo (in_array($locval['id'],(array)$locationsid))?'selected':''; ?> value="<?php echo $locval['id']; ?>"><?php echo $locval['location_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php }  ?>

            <?php if($vendordetail['id'] == ''){ ?>
            <div class="form-group row ml-20 mr-20">
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendordetail['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block">HR Connect</span>
                     <span class="d-block error-message text-danger"></span>
                    <select aria-label="Hr Connect" class="form-control js-example-basic-single 3rdpartyvendortypeselect
                    " name="hr_connect" >
                        <option value="">Not Applicable</option>
                        <?php foreach($vendor_hr_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendordetail['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block">Finance Connect</span>
                    <select aria-label="Finance Connect" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="finance_connect" >
                        <option value="">Not Applicable</option>
                        <?php foreach($vendor_finanace_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full ">
                    <span class="form-label  d-block">Admin Connect &nbsp;<span class="requried">*</span></span>
                    <select aria-label="Admin Connect" class="form-control js-example-basic-single all_connect_check" id="user_admin_connect" name="admin_connect" >
                        <?php foreach($vendor_admin_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>" <?php echo $_SESSION['userid'] == $auser['id']?"selected":""?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                            <option value="" >Not Applicable</option>
                    </select>
                    <div class="d-none text-danger" id="user_admin_connect_err"> Please Select Admin Connect </div>
                </div>
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendordetail['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block">Project Connect</span>
                    <select aria-label="Project Connect" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="project_connect" >
                        <option value="">Not Applicable</option>
                        <?php foreach($vendor_project_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full ">
                    <span class="form-label  d-block">Location Connect</span>
                    <select aria-label="Location Connect" class="form-control js-example-basic-single all_connect_check " name="location_connect" >
                        <option value="">Not Applicable</option>
                         <?php foreach($vendor_location_application_users as $auser){  ?>
                          <option value="<?php echo $auser['id']; ?>" <?php echo $_SESSION['userid'] == $auser['id']?"selected":""?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>

                    </select>
                </div>
                <div class="form-group col-lg-3  w-full notrequird3rdpartyvendortype <?php echo ($vendordetail['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label">Purchase Connect</span>
                   <select aria-label="Purchase Connect" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="purchase_connect" >
                         <option value="">Not Applicable</option>
                      <?php foreach($vendor_purchase_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>" ><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                 <div class="form-group col-lg-3 w-full <?php echo $eventdisbale; ?>" >
                    <span class="form-label  d-block">Legal Connect</span>
                       <select aria-label="Legal Connect" class="form-control js-example-basic-single all_connect_check " name="legal_connect" >
                        <option value="">Not Applicable</option>
                      <?php foreach($vendor_legal_application_users as $auser){
                        $selected = "";
                        if($vendor_registration_requests['legal_connect'] == $auser['id']){
                           $selected = "selected";
                        }
                       ?>
                       <option value="<?php echo $auser['id']; ?>" <?php echo $selected; ?>><?php echo $auser['display_name']; ?></option>
                       <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>

                    </div>


                    <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">GST & Billing Details</h5>
                    <div id="gst-tab-details">
                        <?php
                        if (count((array) $customer_vendor_gst_billing_details) == '0') {
                            $customer_vendor_gst_billing_details[] = array();
                        }
                        $mkseq = 1;
                        foreach ($customer_vendor_gst_billing_details as $cvgst) { ?>
                            <div class="row sub-title" id="removediv_<?php echo $mkseq; ?>">
                                <div class="col-lg-6">
                                    <input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]"
                                        value="<?php echo $cvgst['id']; ?>">
                                    <div class="row">
                                        <div class="col-lg-6 p-r-5 w-full f-left" >
                                            <span class="form-label d-block">GST Registration type</span>
                                            <select aria-label="Select option" id="gsttype_<?php echo $mkseq; ?>" name="gsttype[]"
                                                class="form-select js-example-basic-single">
                                                <option value="">Select Any One</option>
                                                <?php foreach ($gstregistrationtype as $gstkey => $gstregtype) { ?>
                                                    <option <?php echo $cvgst['gsttype'] == $gstkey ? 'selected' : '' ?>
                                                        value="<?php echo $gstkey; ?>"><?php echo $gstregtype; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 f-left" >
                                            <span class="form-label">GST No.</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Gstin[]" class="form-control checkalreadyavailableornotcustomerorvendordetails" type="text" id="gstin" name="gstin[]" data-type="GSTIN" value="<?php echo $cvgst['gstin']; ?>" >
                                                <span class="text-danger f-14 gstinerror" style="display:none"></span>
                                            </div>

                                            <div class="d-none text-danger" id="gstin_err"> Please enter GST IN </div>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span class="form-label">Billing Adderss</span>
                                            <div class="kt-input-icon">
                                                <textarea aria-label="Billing Adderss" type="text" class="form-control" placeholder="Billing Adderss"
                                                    name="billing_address[]"
                                                    id="billing_address_<?php echo $mkseq; ?>"><?php echo $cvgst['billing_address']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3 w-full">
                                            <span class="form-label">Country</span>
                                            <select aria-label="Select option" id="billing_country_<?php echo $mkseq; ?>"
                                                class="form-select js-example-basic-single selectedcategory" name="billing_country[]" data-element_id="billing_state_<?php echo $mkseq; ?>" data-table_name="states" data-field_name="country_id" data-targetelement = "selectedbillcountry">
                                                <option value="">Select Any One</option>
                                                <?php foreach ($countrieslist as $countryvalue) { ?>
                                                    <option <?php echo $cvgst['billing_country'] == $countryvalue['id'] ? 'selected' : '' ?> value="<?php echo $countryvalue['id']; ?>">
                                                        <?php echo $countryvalue['country_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3  w-full">
                                            <span class="form-label">State</span>
                                            <select aria-label="Select option" id="billing_state_<?php echo $mkseq; ?>" name="billing_state[]"
                                                class="form-select js-example-basic-single selectedcategory" data-element_id="billing_city_<?php echo $mkseq; ?>" data-table_name="cities" data-field_name="state_id" data-targetelement = "selectedbillstate">
                                                <option value="">Select Any One</option>
                                                <?php foreach ($states as $states1) { ?>
                                                    <option <?php echo $cvgst['billing_state'] == $states1['id'] ? 'selected' : '' ?>
                                                        value="<?php echo $states1['id']; ?>"><?php echo $states1['state_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5  w-full">
                                            <span class="form-label">City/Town</span>
                                            <select aria-label="Select option" id="billing_city_<?php echo $mkseq; ?>" name="billing_city[]"
                                                class="form-select js-example-basic-single selectedcategory" data-element_id="" data-table_name="city" data-field_name="" data-targetelement = "selectedbillcity">
                                                <option value="">Select Any One</option>
                                                <?php foreach ($cities as $cities1) { ?>
                                                    <option <?php echo $cvgst['billing_city'] == $cities1['id'] ? 'selected' : '' ?>
                                                        value="<?php echo $cities1['id']; ?>"><?php echo $cities1['city_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5">
                                            <span class="form-label">Pincode</span>
                                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number"
                                                id="billing_post_code_<?php echo $mkseq; ?>" name="billing_post_code[]"
                                                value="<?php echo $cvgst['billing_post_code']; ?>">

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5 f-right">
                                            <span class="form-label">Contact Person</span>
                                            <input aria-label="Contact Person" class="form-control" placeholder="Contact Person" type="text" id=""
                                                name="contact_person[]" value="<?php echo $cvgst['contact_person']; ?>">

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5  f-right">
                                            <span class="form-label">Mobile</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Mobile[]" class="form-control" type="number" id="mobile" name="mobile[]"
                                                    value="<?php echo $cvgst['mobile']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5  f-right">
                                            <span class="form-label">Fax</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Fax[]" class="form-control" type="number" id="fax" name="fax[]"
                                                    value="<?php echo $cvgst['fax']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="float: left;">

                                    <div class="row">
                                        <div class="form-group col-lg-9">
                                            <span class="form-label d-block"></span>
                                            <div class="border-checkbox-section  ">
                                                <div class="border-checkbox-group border-checkbox-group-primary m-t-30">
                                                    <input aria-label="Input field" class="border-checkbox cloneShipping" data-seq="<?php echo $mkseq; ?>"  type="checkbox"
                                                        id="checkbox<?php echo $mkseq; ?>"
                                                        >
                                                    <label class="form-label border-checkbox-label"
                                                        for="checkbox<?php echo $mkseq; ?>">My shipping address is the same as
                                                        my billing address</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 d-flex flex-column justify-content-between  text-right">
                                            <span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer removeGSTDiv" data-gstdiv="<?php echo $mkseq; ?>" >
                                                <i class="ti-trash text-danger"></i> Remove
                                            </span>
                                            <div class="form-group ">
                                                <input aria-label="Is Default" type="radio" id="test" name="is_default"
                                                    value="<?php echo $mkseq - 1; ?>" <?php echo ($cvgst['is_default'] == 'Y') || ($mkseq - 1 == 0) ? 'checked' : ''; ?>>
                                                <span>Make Default</span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <span class="form-label">Shipping Adderss</span>
                                            <div class="kt-input-icon">
                                                <textarea aria-label="Street" type="text" class="form-control" placeholder="Street"
                                                    name="shipping_address[]"
                                                    id="shipping_address_<?php echo $mkseq; ?>"><?php echo $cvgst['shipping_address']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3  w-full">
                                            <span class="form-label">Country</span>
                                            <select aria-label="Select option" id="shipping_country_<?php echo $mkseq; ?>"
                                                class="form-select js-example-basic-single selectedcategory" name="shipping_country[]" data-element_id="shipping_state_<?php echo $mkseq; ?>" data-table_name="states" data-field_name="country_id" data-targetelement = "selectedshipcountry" >
                                                <option value="">Select Any One</option>
                                                <?php foreach ($countrieslist as $countryvalue) { ?>
                                                    <option <?php echo $cvgst['shipping_country'] == $countryvalue['id'] ? 'selected' : '' ?> value="<?php echo $countryvalue['id']; ?>">
                                                        <?php echo $countryvalue['country_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 w-full">
                                            <span class="form-label">State</span>
                                            <select aria-label="Select option" id="shipping_state_<?php echo $mkseq; ?>" name="shipping_state[]"
                                                class="form-select js-example-basic-single selectedcategory" data-element_id="shipping_city_<?php echo $mkseq; ?>" data-table_name="cities" data-field_name="state_id" data-targetelement = "selectedshipstate" >
                                                <option value="">Select Any One</option>
                                                <?php foreach ($states as $states1) { ?>
                                                    <option <?php echo $cvgst['shipping_state'] == $states1['id'] ? 'selected' : '' ?>
                                                        value="<?php echo $states1['id']; ?>"><?php echo $states1['state_name']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5 w-full">
                                            <span class="form-label">City/Town</span>
                                            <select aria-label="Select option" id="shipping_city_<?php echo $mkseq; ?>" name="shipping_city[]"
                                                class="form-select js-example-basic-single selectedcategory" data-element_id="" data-table_name="city" data-field_name="" data-targetelement = "selectedshipcity" >
                                                <option value="">Select Any One</option>
                                                <?php foreach ($cities as $cities1) { ?>
                                                    <option <?php echo $cvgst['shipping_city'] == $cities1['id'] ? 'selected' : '' ?>
                                                        value="<?php echo $cities1['id']; ?>"><?php echo $cities1['city_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span class="form-label">Pincode</span>
                                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number"
                                                id="shipping_post_code_<?php echo $mkseq; ?>" name="shipping_post_code[]"
                                                value="<?php echo $cvgst['shipping_post_code']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $mkseq++;
                        } ?>
                    </div>
                    <div class="">
                        <button class="btn btn-success  col-lg-4 addMoreGSTBillingDetails"  type="button">+ Add
                            More GST & Billing Details</button>
                    </div>
                </div>


                <?php
                $fieldaccesright = $vafield['company_information'];
                if ($fieldaccesright['access'] != "NA") { ?>
                    <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">Company Information</h5>
                    <div class="row ">
                        <?php
                        $fieldaccesright = $vafield['company_information'];
                        if ($fieldaccesright['field']['vendor_tax_reg_no'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['vendor_tax_reg_no'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 optional-field mb-2" >
                                <span class="form-label">Tax/Vat Reg.No.</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="vendor_tax_reg_no"
                                        name="vendor_tax_reg_no" value="<?php echo $vendordetail['vendor_tax_reg_no']; ?>"
                                        autocomplete="off">
                                </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['business_type'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['business_type'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">Legal Structure</span>
                                <select aria-label="Business Type" class="form-select js-example-basic-single" name="business_type">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($company_types as $ctype) { ?>
                                        <option <?php echo $vendordetail['business_type'] == $ctype['id'] ? 'selected' : '' ?>
                                            value="<?php echo $ctype['id']; ?>"><?php echo $ctype['company_type_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['duns_no'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['duns_no'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 form-group optional-field">
                                <span class="form-label">DUNS</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="duns_no"
                                        name="duns_no" value="<?php echo $vendordetail['duns_no']; ?>" autocomplete="off">
                                </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['company_registration_number'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['company_registration_number'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 mb-2" >
                                <span class="form-label">Company Registration Number</span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text"
                                        name="company_registration_number"
                                        value="<?php echo $vendordetail['company_registration_number']; ?>" autocomplete="off">
                                </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['company_registration_country'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['company_registration_country'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">Company Registration Country</span>
                                <select aria-label="Company Registration Country" class="form-select js-example-basic-single" name="company_registration_country">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($countrieslist as $countryvalue) { ?>
                                        <option <?php echo $vendordetail['company_registration_country'] == $countryvalue['id'] ? 'selected' : '' ?> value="<?php echo $countryvalue['id']; ?>">
                                            <?php echo $countryvalue['country_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['payment_terms'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['payment_terms'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">Payment Terms</span>
                                <select aria-label="Payment Terms" name="payment_terms" class="form-control form-select js-example-basic-single">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($payment_terms as $payt) { ?>
                                        <option value="<?php echo $payt['id']; ?>" <?php echo $vendordetail['payment_terms'] == $payt['id'] ? 'selected' : ''; ?>>
                                            <?php echo $payt['payment_term_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['gstin'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['gstin'] != "E" ? "custom_readonly" : "";
                            ?>

                            <?php
                        }
                        if ($fieldaccesright['field']['pan_no'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['pan_no'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 form-group">
                                <span class="form-label">PAN No. <span class="text-danger">*</span></span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control checkalreadyavailableornotcustomerorvendordetails <?php echo $accessreadonly; ?>" data-type="PAN_NO" type="text" id="pan_no" name="pan_no"
                                        value="<?php echo $vendordetail['pan_no']; ?>" autocomplete="off">
                                    <span class="text-danger f-12"><?php if($vendordetail['pan_no'] !=""){ echo $vendor_verifications['PAN']['status'] == "Y" ? $_SESSION['error_message']['9136'] : "PAN verification is pending";
} ?></span>
                                </div>
                                <span class="text-danger f-14 d-block" id="pannoerror" ></span>
                                <div class="d-none text-danger" id="pan_no_err"> Please Enter PAN No. </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['tan_number'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['tan_number'] != "E" ? "custom_readonly" : "";
                            $accessreadonly =  $vendordetail['tan_number']!=""?"custom_readonly":"";

                            ?>
                            <div class="col-lg-4 form-group optional-field">
                                <span class="form-label">TAN No. </span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="tan_number"
                                        name="tan_number" value="<?php echo $vendordetail['tan_number']; ?>" autocomplete="off">

                                </div>
                            </div>
                            <?php
                        } ?>
                        <div class="col-lg-4 form-group">
                            <span class="form-label d-block">Subsidiary Vendor Check</span>
                            <input aria-label="Subsidiary Vendor Check" type="checkbox" class="js-switch-vendor" value="Y" name="subsidiary_vendor_check"
                                id="subsidiary_vendor_check" <?php echo $vendordetail['subsidiary_vendor_check'] == "Y" ? "checked" : ""; ?>>
                        </div>

                        <?php
                        if ($fieldaccesright['field']['msme_number'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['msme_number'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 form-group <?php echo $accessreadonly; ?>">
                                <span class="form-label d-block">Udyam Registration/MSME</span>
                                <input aria-label="Msme Register Yes No" type="checkbox" class="js-switch-vendor msme_register_yes_no" value="Y" name="msme_register_yes_no"
                                    id="msme_register_yes_no" <?php echo $vendordetail['msme_register_yes_no'] == "Y" ? "checked" : ""; ?> >
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['msme_number'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['msme_number'] != "E" ? "custom_readonly" : "";
                            $accessreadonly =  $vendordetail['msme_number']!=""?"custom_readonly":"";

                            ?>
                            <div class="col-lg-4 form-group <?php echo $vendordetail['msme_register_yes_no'] == "Y" ? "" : "d-none"; ?>" id="msme_number_id" >
                                <span class="form-label">Udyam Registration/MSME Number <span class="text-danger">*</span></span>
                                <div class="kt-input-icon ">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="msme_number"
                                        name="msme_number" value="<?php echo $vendordetail['msme_number']; ?>" autocomplete="off">
                                    <?php echo $vendor_verifications['MSME']['status'] == "Y" ? "The MSME number is valid." : ""; ?>
                                </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['incoterm_id'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['incoterm_id'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">ENCO Terms/ Shipping Terms</span>
                                <select aria-label="Incoterm" class="form-select js-example-basic-single" name="incoterm_id">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($incoterms as $incotermvalue) { ?>
                                        <option <?php echo $vendordetail['incoterm_id'] == $incotermvalue['id'] ? 'selected' : '' ?>
                                            value="<?php echo $incotermvalue['id']; ?>"><?php echo $incotermvalue['incoterm_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['image'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['image'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 <?php echo $accessreadonly; ?>">
                                <span class="form-label">Logo</span>
                                <div class="col-lg-9 col-xl-6 ">
                                    <div class="profile-pic">
                                        <label class="-label" for="display_logo">
                                            <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                        </label>
                                        <input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" />
                                        <img src="<?php echo "uploads/all/".$vendordetail['image'] ; ?>" id="outputdisplay_logo" width="200" alt="Logo" />
                                        <input type="hidden" name="hid_image" value="<?php echo $vendordetail['image'] ;  ?>">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>


                <?php
                $fieldaccesright = $vafield['bank_information'];
                if ($fieldaccesright['access'] != "NA") { ?>
                    <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">Banking Information</h5>
                    <div class="row ">
                        <?php
                        $fieldaccesright = $vafield['bank_information'];
                        if ($fieldaccesright['field']['bank_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Bank name <span class="text-danger">*</span></span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_name"
                                    name="bank_name" value="<?php echo $vendordetail['bank_name']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_address'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_address'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-8">
                                <span class="form-label">Bank Address</span>
                                <textarea aria-label="Text area" type="text" class="form-control <?php echo $accessreadonly; ?>"
                                    name="bank_address"><?php echo $vendordetail['bank_address']; ?></textarea>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_country'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_country'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">Country</span>
                                <select aria-label="Bank Country" class="form-select js-example-basic-single selectedcategory" name="bank_country" id="bank_country" data-element_id="bank_state" data-table_name="states" data-field_name="country_id" data-targetelement = "selectedshipcountry"
                                    >
                                    <option value="">Select Any One</option>
                                    <?php foreach ($countrieslist as $countryvalue) { ?>
                                        <option <?php echo $vendordetail['bank_country'] == $countryvalue['id'] ? 'selected' : '' ?>
                                            value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_state'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_state'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3  w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">State</span>
                                <select aria-label="Bank State" name="bank_state" id="bank_state" class="form-select js-example-basic-single selectedcategory"
                                data-element_id="bank_city" data-table_name="cities" data-field_name="state_id" data-targetelement = "selectedbillstate">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($states as $states1) { ?>
                                        <option <?php echo $vendordetail['bank_state'] == $states1['id'] ? 'selected' : '' ?>
                                            value="<?php echo $states1['id']; ?>"><?php echo $states1['state_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_city'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_city'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">City/Town</span>
                                <select aria-label="Bank City" name="bank_city" id="bank_city" class="form-select js-example-basic-single selectedcategory" data-element_id="" data-table_name="city" data-field_name="" data-targetelement = "selectedbillcity">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($cities as $cities1) { ?>
                                        <option <?php echo $vendordetail['bank_city'] == $cities1['id'] ? 'selected' : '' ?>
                                            value="<?php echo $cities1['id']; ?>"><?php echo $cities1['city_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_post_code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_post_code'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-3">
                                <span class="form-label">Pincode</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" placeholder="Pincode" type="text"
                                    id="bank_post_code" name="bank_post_code"
                                    value="<?php echo $vendordetail['bank_post_code']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_beneficiary_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_beneficiary_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Company Bank Beneficiary Name </span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_beneficiary_name"
                                    name="bank_beneficiary_name" value="<?php echo $vendordetail['bank_beneficiary_name']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_account_no'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_account_no'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Bank Account Number <span class="text-danger">*</span></span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="number" id="bank_account_no"
                                    name="bank_account_no" value="<?php echo $vendordetail['bank_account_no']; ?>">
                                <span
                                    class="text-danger f-12"><?php if($vendordetail['bank_account_no'] !=""){ echo $vendor_verifications['BANK']['status'] == "Y" ? $_SESSION['error_message']['9140'] : "Bank Account verification is pending";
} ?></span>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['account_type'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['account_type'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                                <span class="form-label">Type Of Bank Account</span>
                                <select aria-label="Account Type" name="account_type" id="account_type" class="form-select js-example-basic-single">
                                    <option value="">Select Any One</option>
                                    <?php foreach ($bank_acoount_type as $batypekey => $batypeval) { ?>
                                        <option <?php echo $vendordetail['account_type'] == $batypekey ? 'selected' : '' ?>
                                            value="<?php echo $batypekey; ?>"><?php echo $batypeval; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['ifsc_code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['ifsc_code'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="col-lg-4 form-group">
                                <span class="form-label">IFSC Code <span class="text-danger">*</span></span>
                                <div class="kt-input-icon">
                                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="ifsc_code"
                                        name="ifsc_code" value="<?php echo $vendordetail['ifsc_code']; ?>" autocomplete="off">
                                </div>
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['routing_aba_dfi_ach'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['routing_aba_dfi_ach'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 optional-field">
                                <span class="form-label">Routing-ABA/DFI/ACH</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_aba_dfi_ach"
                                    name="routing_aba_dfi_ach" value="<?php echo $vendordetail['routing_aba_dfi_ach']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['routing_wire_Code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['routing_wire_Code'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 optional-field">
                                <span class="form-label">Routing Wire Code</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_wire_Code"
                                    name="routing_wire_Code" value="<?php echo $vendordetail['routing_wire_Code']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['swift_bic_code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['swift_bic_code'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 optional-field">
                                <span class="form-label">SWIFT/BIC Code</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="swift_bic_code"
                                    name="swift_bic_code" value="<?php echo $vendordetail['swift_bic_code']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['iban_code'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['iban_code'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4 optional-field">
                                <span class="form-label">IBAN</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="iban_code"
                                    name="iban_code" value="<?php echo $vendordetail['iban_code']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_query_person_name'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_query_person_name'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Contact Person Name (Bank related queries)</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_person_name"
                                    name="bank_query_person_name" value="<?php echo $vendordetail['bank_query_person_name']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_query_contact_no'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_query_contact_no'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Contact no</span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_contact_no"
                                    name="bank_query_contact_no" value="<?php echo $vendordetail['bank_query_contact_no']; ?>">
                            </div>
                            <?php
                        }
                        if ($fieldaccesright['field']['bank_query_email_id'] != "NA") {
                            $accessreadonly = $fieldaccesright['field']['bank_query_email_id'] != "E" ? "custom_readonly" : "";
                            ?>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Contact Email </span>
                                <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="email" id="bank_query_email_id"
                                    name="bank_query_email_id" value="<?php echo $vendordetail['bank_query_email_id']; ?>">
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>





                <?php
                $fieldaccesright = $vafield['other_contact_information'];
                if ($fieldaccesright['access'] != "NA") {
                    if ($fieldaccesright['field']['other_contact_information'] != "NA") {
                        $accessreadonly = $fieldaccesright['field']['other_contact_information'] != "E" ? "custom_readonly" : "";
                        ?>
                        <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">Other Contact Information</h5>
                        <table class="table table-hover <?php echo $accessreadonly; ?>">

                            <thead>
                                <tr>
                                    <th scope="col">Type</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Number</th>
                                    <th scope="col">Email Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vendor_contact_info_type as $vcikey => $vcival) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $vcival; ?>
                                        </td>
                                        <td>
                                            <input aria-label="Input field" class="form-control" type="text"
                                                name="other_contact_information[<?php echo $vcikey; ?>][name]"
                                                value="<?php echo $other_contact_information[$vcikey]['name']; ?>">
                                        </td>
                                        <td>
                                            <input aria-label="Enter number" class="form-control" type="number"
                                                name="other_contact_information[<?php echo $vcikey; ?>][number]"
                                                value="<?php echo $other_contact_information[$vcikey]['number']; ?>">
                                        </td>
                                        <td>
                                            <input aria-label="Email address" class="form-control" type="email"
                                                name="other_contact_information[<?php echo $vcikey; ?>][email_id]"
                                                value="<?php echo $other_contact_information[$vcikey]['email_id']; ?>">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                <?php } ?>

                <!-- Vendor Questions Section -->


                <!-- Agreemets/Documents Section -->
                <?php if($transaction_table == "vendors" && $vendor_id !=""){ ?>
                <div class="p-3">
                    <div class="d-none" id="hide_doc_document_select_list">

                            <?php foreach ($document_lists as $doclist) { ?>
                                <option value="<?php echo $doclist['id']; ?>" <?php echo $doclist['id'] == $inst['document_id'] ? 'selected' : ''; ?>><?php echo $doclist['document_name']; ?></option>
                            <?php } ?>

                    </div>
                <h4 class="mb-10 font-weight-bold text-dark">Documents</h4>
                    <div class="form-group col-lg-12">
                        <table class="table table-bordered " id="addDocuments">
                            <tr>
                                <th scope="col" class="" style="width: 5%;">Sl NO</th>
                                <th scope="col" class="" style="width: 30%;">Doc Name</th>
                                <th scope="col" class="" style="width: 20%;"> Start Date</th>
                                <th scope="col" class="thheader" style="width: 20%;">End Date </th>
                                <th scope="col" class="thheader" style="width: 20%;">Upl</th>
                                <th scope="col" class="thheader" style="width: 5%;">Down</th>
                            </tr>
                        <?php $k=0;
                        if(count((array)$vendor_documents) == '0'){
                            $vendor_documents[] = array();
                        }

                        foreach ($vendor_documents as $inst) { $k++;?>
                            <tr class="rowcount p-1">
                                <td><?php echo $k; ?>
                                <input type="hidden" name="doc_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>">
                            </td>

                            <td class="p-1 w-full">
                                <select aria-label="Doc Document []" name="doc_document_id[]" class="form-control form-select js-example-basic-single">
                                    <?php foreach ($document_lists as $doclist) { ?>
                                        <option value="<?php echo $doclist['id']; ?>" <?php echo $doclist['id'] == $inst['document_id'] ? 'selected' : ''; ?>><?php echo $doclist['document_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="p-1">
                                <input aria-label="Doc From Date[]" type="text" name="doc_from_date[]" class="form-control datepicker "  value="<?php echo fromDate($inst['from_date']); ?>"  autocomplete="off">
                            </td>
                            <td class="p-1">
                                <input aria-label="Doc To Date[]" type="text" name="doc_to_date[]" class="form-control datepicker"  value="<?php echo fromDate($inst['to_date']); ?>" autocomplete="off">
                            </td>
                            <td class="p-1">
                                <input aria-label="Doc Filename[]" type="file" name="doc_filename[]" class="form-control"/>
                                <input type="hidden" name="hidden_doc_filename[]" value="<?php echo $inst['filename']; ?>"/>
                            </td>
                            <td class="p-1">
                                <?php if($inst['filename']!=''){ ?>
                                    <div class="col-lg-3">
                                        <a href="<?php echo $document_uploads_Path."/".$inst['filename']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
                                            <i class="ti-cloud-down f-20 text-warning f-30 f-w-800"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                            </td>

                        </tr>
                        <?php }  ?>
                    </table>
                    <div class="row mt-2">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button class="btn btn-success float-left addDocumentRows" type="button">+ Add More</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
            <!-- Agreemets/Documents Section -->

                <?php

                $fieldaccesright = $vafield['questions'];
                if ($fieldaccesright['access'] != "NA" && $transaction_table == "vendor_registers") { ?>
                    <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">Vendor Questions</h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body <?php echo $accessreadonly; ?>">
                                <?php if($vendordetail['vendor_type_id'] == '1' || $vendordetail['vendor_type_id'] ==""){ //3 party show only ?>
                                <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
                                <div class="row mt-2">
                                    <h6>A) Previously have you or any of your subsidiaries ever associated with
                                        <?php echo ucwords($_SESSION['config']['name']); ?> (USA or any other region)?
                                    </h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[radio Associate]" type="radio" class="onchangeAssociatedAnswer" name="vendor_questions[radio_associate]" value="Y" <?php echo $vendor_questions['radio_associate'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[radio Associate]" type="radio" class="onchangeAssociatedAnswer" name="vendor_questions[radio_associate]" value="N" <?php echo ($vendor_questions['radio_associate'] == "N" || $vendor_questions['radio_associate'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="associated_team form-group mt-2 <?php echo $vendor_questions['radio_associate'] == "Y" ? "" : "d-none"; ?>" >
                                    <span class="form-label">Please provide details</span>
                                    <textarea aria-label="Vendor Questions[associated Team Detail]" type="text" name="vendor_questions[associated_team_detail]"
                                        class="form-control"><?php echo $vendor_questions['associated_team_detail']; ?></textarea>
                                </div>
                                <div class="row mt-2">
                                    <h6>B) Does your company has a quality system ?(If Answer is Yes Then attachs the Quality
                                        Certificates)</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[quality System]" type="radio" class="onchangeVRQAnswer" name="vendor_questions[quality_system]" value="Y" <?php echo $vendor_questions['quality_system'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[quality System]" type="radio" class="onchangeVRQAnswer" name="vendor_questions[quality_system]" value="N" <?php echo ($vendor_questions['quality_system'] == "N" || $vendor_questions['quality_system'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="vrq_extra mt-2 <?php echo $vendor_questions['quality_system'] == "Y" ? "" : "d-none"; ?>" >
                                    <div class="row">
                                        <div class="form-group col-lg-1">
                                            <span class="form-label">1</span>
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <span class="form-label">Certificate Name</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][name][1]" class="form-control" type="text"
                                                name="vendor_questions[quality_system_certificate][name][1]"
                                                value="<?php echo $vendor_questions['quality_system_certificate']['name']['1']; ?>">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span class="form-label">Effective From Date</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][fromdate][1]" class="form-control datepicker" type="text"
                                                name="vendor_questions[quality_system_certificate][fromdate][1]"
                                                value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?>">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span class="form-label">Effective To Date</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][todate][1]" class="form-control datepicker" type="text"
                                                name="vendor_questions[quality_system_certificate][todate][1]"
                                                value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-1">
                                            <span class="form-label">2</span>
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <span class="form-label">Certificate Name</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][name][2]" class="form-control" type="text"
                                                name="vendor_questions[quality_system_certificate][name][2]"
                                                value="<?php echo $vendor_questions['quality_system_certificate']['name']['2']; ?>">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span class="form-label">Effective From Date</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][fromdate][2]" class="form-control datepicker" type="text"
                                                name="vendor_questions[quality_system_certificate][fromdate][2]"
                                                value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?>">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span class="form-label">Effective To Date</span>
                                            <input aria-label="Vendor Questions[quality System Certificate][todate][2]" class="form-control datepicker" type="text"
                                                name="vendor_questions[quality_system_certificate][todate][2]"
                                                value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="f-15 f-w-600 text-info mb-2 mt-2" style="clear: both;">Small Business Registerd
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-10">

                                        <?php $smallbussinesary = array('1' => 'Self Certified', '2' => 'Sam Govt Registered', '3' => 'Small Disadvantage', '4' => 'Women Owned', '5' => 'Veteran Owned', '6' => 'Service - Disabled Veteran', '7' => 'Small Business');
                                        foreach ($smallbussinesary as $keybuss => $smallbus) {
                                            ?>
                                            <div class="checkbox-fade fade-in-primary">
                                                <label class="form-label">
                                                    <input aria-label="Checkbox" type="checkbox" value="Y"
                                                        name="vendor_questions[small_business_registerd][<?php echo $keybuss; ?>]"
                                                        <?php echo $vendor_questions['small_business_registerd'][$keybuss] == "Y" ? "checked" : ""; ?>>
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span><?php echo $smallbus; ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <span class="form-label">Others</span>
                                            <input aria-label="Vendor Questions[small Business Registerd][100]" class="form-control" type="text"
                                                name="vendor_questions[small_business_registerd][100]"
                                                value="<?php echo $vendor_questions['small_business_registerd']['100']; ?>">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <span class="form-label">Unique Entity</span>
                                                <input aria-label="Vendor Questions[small Business Registerd][101]" class="form-control" type="text"
                                                    name="vendor_questions[small_business_registerd][101]"
                                                    value="<?php echo $vendor_questions['small_business_registerd']['101']; ?>">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span class="form-label">Cage Code</span>
                                                <input aria-label="Vendor Questions[small Business Registerd][102]" class="form-control" type="text"
                                                    name="vendor_questions[small_business_registerd][102]"
                                                    value="<?php echo $vendor_questions['small_business_registerd']['102']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } //3 party show only ?>
                                <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Supply Chain Responsibility</div>
                                <div class="row mt-2">
                                    <h6>1) Is Your Company a Member of RBA ( Responsible Business Alliance)</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][1]" type="radio" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][1]" <?php echo $vendor_questions['supply_Chain_responsibility']['1'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][1]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][1]" <?php echo ($vendor_questions['supply_Chain_responsibility']['1'] == "N" || $vendor_questions['supply_Chain_responsibility']['1'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <h6>2) Are all employees of your company atleast 18 years of age</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][2]" type="radio" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][2]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][2]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][2]" <?php echo ($vendor_questions['supply_Chain_responsibility']['2'] == "N" || $vendor_questions['supply_Chain_responsibility']['2'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <h6>3) Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4
                                    </h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][3]" type="radio" value="Y" class="onchangeSupplyChainResponsibilityAnswer" data-seqval="3"
                                                    name="vendor_questions[supply_Chain_responsibility][3]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] == "Y" ? "checked" : ""; ?> >
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][3]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][3]" <?php echo ($vendor_questions['supply_Chain_responsibility']['3'] == "N" || $vendor_questions['supply_Chain_responsibility']['3'] == "") ? "checked" : ""; ?> class="onchangeSupplyChainResponsibilityAnswer" data-seqval="3">
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row supplychainresponsibility_4 mt-2">
                                    <h6>4) Do you have hourly employees that works at manufacturer facility? For the purpose of
                                        this
                                        form, hourly employees are employees that are subject to local laws governing overtime
                                        pay
                                        or subject to hourly increased or decreased in compensation based on volume of product
                                        if
                                        Yes (Please answer a, b and c, if No then skip to Question no 5)</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][4]" type="radio" class="onchangeWorkManufracturerFacilityAnswer" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][4]"  <?php echo $vendor_questions['supply_Chain_responsibility']['4'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][4]" type="radio" value="N" class="onchangeWorkManufracturerFacilityAnswer"
                                                    name="vendor_questions[supply_Chain_responsibility][4]"
                                                     <?php echo ($vendor_questions['supply_Chain_responsibility']['4'] == "N" || $vendor_questions['supply_Chain_responsibility']['4'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-l-20 work_manufacturer_facility supplychainresponsibility_4 mt-2 <?php echo $vendor_questions['supply_Chain_responsibility'][4] == "Y" ? "" : "d-none"; ?>" >
                                    <div class="row">
                                        <h6>a) Do all hourly workers work less than 60 hours per week?</h6>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 A]" type="radio" value="Y"
                                                        name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_a'] == "Y" ? "checked" : ""; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 A]" type="radio" value="N"
                                                        name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_a'] == "N" || $vendor_questions['supply_Chain_responsibility']['4_a'] == "") ? "checked" : ""; ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <h6>b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or
                                            slave labor?</h6>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 B]" type="radio" value="Y"
                                                        name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_b'] == "Y" ? "checked" : ""; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 B]" type="radio" value="N"
                                                        name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_b'] == "N" || $vendor_questions['supply_Chain_responsibility']['4_b'] == "") ? "checked" : ""; ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <h6>c) Do you expect your supplier to have a policy prohibiting forced, involuntory,
                                            indentured, trafficked, or slave labor</h6>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 C]" type="radio" value="Y"
                                                        name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_c'] == "Y" ? "checked" : ""; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 C]" type="radio" value="N"
                                                        name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_c'] == "N" || $vendor_questions['supply_Chain_responsibility']['4_c'] == "") ? "checked" : ""; ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row supplychainresponsibility_5 mt-2 <?php echo ($vendor_questions['supply_Chain_responsibility'][4] == "N" || $vendor_questions['supply_Chain_responsibility'][4] == "") ? "" : "d-none"; ?>" >
                                    <h6>5) Do You retain or hold your employees original passport or other original personal
                                        identification documents?</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][5]" type="radio" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][5]"
                                                    class="onchangeHoldOriginalPassportAnswer" <?php echo $vendor_questions['supply_Chain_responsibility']['5'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][5]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][5]" checked="checked"
                                                    class="onchangeHoldOriginalPassportAnswer" <?php echo ($vendor_questions['supply_Chain_responsibility']['5'] == "N" || $vendor_questions['supply_Chain_responsibility']['5'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-l-20 hold_original_passport supplychainresponsibility_5 mt-2 <?php echo ($vendor_questions['supply_Chain_responsibility'][4] == "N" || $vendor_questions['supply_Chain_responsibility'][4] == "" && $vendor_questions['supply_Chain_responsibility'][5] == "Y") ? "" : "d-none"; ?>" >
                                    <div class="row">
                                        <h6>a) Do you return original passport or other original personal identification
                                            documents
                                            upon the employee's request?</h6>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][5 A]" type="radio" value="Y"
                                                        name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['5_a'] == "Y" ? "checked" : ""; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label="Vendor Questions[supply Chain Responsibility][5 A]" type="radio" value="N"
                                                        name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['5_a'] == "N" || $vendor_questions['supply_Chain_responsibility']['5_a'] == "") ? "checked" : ""; ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row supplychainresponsibility_6 mt-2">
                                    <h6>6) Do any employee have to reimburse recruitment fee to your company?</h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][6]" type="radio" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][6]" <?php echo $vendor_questions['supply_Chain_responsibility']['6'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][6]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][6]" <?php echo ($vendor_questions['supply_Chain_responsibility']['6'] == "N" || $vendor_questions['supply_Chain_responsibility']['6'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row supplychainresponsibility_7 mt-2 <?php echo $vendor_questions['supply_Chain_responsibility'][3] == "N" ? "" : "d-none"; ?>" >
                                    <h6>7) Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)
                                    </h6>
                                    <div class="form-radio">
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][7]" type="radio" value="Y"
                                                    name="vendor_questions[supply_Chain_responsibility][7]" <?php echo $vendor_questions['supply_Chain_responsibility']['7'] == "Y" ? "checked" : ""; ?>>
                                                <i class="helper"></i>Yes
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="Vendor Questions[supply Chain Responsibility][7]" type="radio" value="N"
                                                    name="vendor_questions[supply_Chain_responsibility][7]" <?php echo ($vendor_questions['supply_Chain_responsibility']['7'] == "N" || $vendor_questions['supply_Chain_responsibility']['7'] == "") ? "checked" : ""; ?>>
                                                <i class="helper"></i>No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="f-15 f-w-600 text-info mb-2 text-center" style="clear: both;">Terms & Conditions
                                </div>
                                <span class="d-block">1. Providing information in the "Vendor Information Form" does not
                                    constitute
                                    acceptance as an approved & registered vendor. The registration of vendor is the sole
                                    discretion
                                    of AM/NS India.</span>
                                <span class="d-block mt-2">2. Failure to enter all of the mandatory information will not enable
                                    us
                                    to post your vendor file to our database..</span>
                                <span class="d-block mt-2">3. Being a registered vendor does NOT guarantee the receipt of
                                    notification for all applicable solicitations or invitation to offer or the award of any
                                    purchase contract or award of any invitation for bid or request for proposal or request for
                                    qualifications.</span>
                                <span class="d-block mt-2">4. Vendor is responsible for the content of the information and its
                                    vendors responsibility to keep all information current and provide any subsequent up dates,
                                    additional information as and when required and when desired by AM/NS India.</span>
                                <span class="d-block mt-2">5. AM/NS India reserves the rights to evaluate the vendor on
                                    periodical
                                    basis and decide the continuation / discontinuation of business with them based on the
                                    evaluation report.</span>
                                <span class="d-block mt-2">6. Representative of AM/NS India OR their nominated agency has the
                                    right
                                    to visit vendors premises for the verification of information with a prior intimation to
                                    them.</span>
                                <span class="d-block mt-2">7. At later date if it is found that the information furnished is
                                    untrue,
                                    inaccurate, not current, or is incomplete, we reserve the right to suspend or terminate the
                                    registration and recover the dues (if any) from you</span>

                                <div class="border-checkbox-section mt-4">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input aria-label="Checkbox" class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['1'] == "Y" ? "checked" : ""; ?>
                                            id="termsandcondition_1" value="Y" name="vendor_questions[terms_conditions][1]">
                                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_1">
                                            <span class="d-block">I / We have read the terms & conditions and privacy
                                                policy.</span>
                                            <span class="d-block">I/We hereby declare my / our acceptance for the same and
                                                certify
                                                that the information submitted in this vendor registration application is true
                                                and
                                                correct to the best of my/our knowledge.</span>
                                            <span class="d-block">I / We further understand that any false statements observed
                                                at
                                                the later stage may result in denial or revocation of our vendor registration
                                                with
                                                AM/NS India group.</span></label>
                                    </div>
                                </div>

                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input aria-label="Checkbox" class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['2'] == "Y" ? "checked" : ""; ?>
                                            id="termsandcondition_2" value="Y" name="vendor_questions[terms_conditions][2]">
                                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_2">
                                            <span class="d-block">I / We agree, covenant and undertake that I / We have reviewed
                                                the
                                                policies of AMNSIL.</span>
                                            <span class="d-block">I / We will comply with the principles contained in these
                                                Policies
                                                and these Policies will continue to be applicable and binding on the me/us, to
                                                the
                                                extent relevant.</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- question part close -->

            </div>
            <button type="button" class="btn btn-primary m-r-20 m-b-20 checkVendorValidationEmpty">
                <i class="ti-check"></i> <?php echo $submitText; ?>
            </button>
            <span class="text-danger terms_conditions d-none" >* Please Accept Terms And Conditions In
                Questions Tab</span>

        </form>
    </div>

<?php } else { ?>

    <div class="page-body">
        <div class="" id="printableArea">
            <link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendors_and_vendor_registers_ajax.css">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th scope="col" colspan="3" class="text-center" style="">
                            <?php if($_SESSION['config']['display_logo']!=""){ ?>
                                <img class="img-fluid img-100" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                            <?php }else { ?>
                              <div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
                                  <span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40"
                                      style=" border-radius: 50%; line-height: 1.1;"><?php echo $_SESSION['config']['name'] != "" ? initials($_SESSION['config']['name']) : "NA"; ?></span>
                              </div>
                              <?php } ?>
                        </th>
                        <th scope="col" colspan="3" class="h3 text-center">Vendor/BP Request Form</th>
                    </tr>
                </thead>
                <tr>
                    <th scope="col" colspan="6" class="f-w-600 text-start">Microchip Legal Entity:- <?php echo $vendordetail['company'] ?? '-'; ?></th>
                </tr>
                <tr>
                    <th scope="col" colspan="6" class="f-w-600 text-start">Microchip Requester:-</th>
                </tr>
                <tr style="background-color: #eeeeee;">
                    <th scope="col" colspan="3" class="text-left f-w-600 f-16">Business Information:</th>
                    <th scope="col" colspan="3" class="text-left f-w-600 f-16">Required</th>
                </tr>
                <tr>
                    <td colspan="3" class="f-14">
                        Company / Name <span class="f-10">(Legal Name)</span>*: <?php echo $vendordetail['company']; ?>
                    </td>
                    <td colspan="3" class="f-14">
                        Corporate Website: <?php echo $vendordetail['website_url']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="f-14">
                        DBA <span class="f-10">(Doing business as or trading name)</span>*: <?php echo $vendordetail['company_trading_name']; ?>
                    </td>
                    <td colspan="3" class="f-14">
                        Subsidiary or Division of: <?php echo $vendordetail['company_subsidiary_division']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        First Name: <?php echo $vendordetail['first_name']!=""?$vendordetail['first_name']:"-"; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Middle Name: <?php echo $vendordetail['middle_name']!=""?$vendordetail['middle_name']:"-"; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Last Name: <?php echo $vendordetail['last_name']!=""?$vendordetail['last_name']:"-"; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Email: <?php echo $vendordetail['email']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Phone: <?php echo $vendordetail['phone']!=""?$vendordetail['phone']:"-"; ?>
                    </td>
                    <td colspan="2" class="f-14">
                         Vendor Type: <?php echo $vendor_types[$vendordetail['vendor_type_id']]['type_name'];
                         ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Service Types: <?php echo $vendordetail['service_types']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        BP Code: <?php echo $vendordetail['bp_code']!=""?$vendordetail['bp_code']:"-"; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Location: <?php $locationsid = json_decode($vendordetail['location_id'] ,1);
                         foreach($locationsid as $locid){ echo $locations[$locid]['location_name'].", ";
} ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="6" class="f-14">
                        1) Remit to Address*: <?php echo $vendordetail['billing_address']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        City*: <?php echo isset($citieslist[$vendordetail['billing_city']]) ? $citieslist[$vendordetail['billing_city']] : $vendordetail['billing_city']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Province / State*: <?php echo isset($stateslist[$vendordetail['billing_state']]) ? $stateslist[$vendordetail['billing_state']] : $vendordetail['billing_state']; ?>
                    </td>
                    <td style="f-14">
                        Postal / Zip Code*: <?php echo $vendordetail['billing_post_code']; ?>
                    </td>
                    <td class="f-14">
                        Country*: <?php echo isset($countrylist[$vendordetail['billing_country']]) ? $countrylist[$vendordetail['billing_country']] : $vendordetail['billing_country']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        2) Order Address*: <?php echo $vendordetail['shipping_address']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        City*: <?php echo isset($citieslist[$vendordetail['shipping_city']]) ? $citieslist[$vendordetail['shipping_city']] : $vendordetail['shipping_city']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Province / State*: <?php echo isset($stateslist[$vendordetail['shipping_state']]) ? $stateslist[$vendordetail['shipping_state']] : $vendordetail['shipping_state']; ?>
                    </td>
                    <td class="f-14">
                        Postal / Zip Code*: <?php echo $vendordetail['shipping_post_code']; ?>
                    </td>
                    <td class="f-14">
                        Country*: <?php echo isset($countrylist[$vendordetail['shipping_country']]) ? $countrylist[$vendordetail['shipping_country']] : $vendordetail['shipping_country']; ?>
                    </td>
                </tr>
                <tr style="background-color: #eeeeee;">
                    <th scope="col" colspan="6" class="f-14">
                        Company Information
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Tax / VAT <span class="f-10">(Specify Type)</span>*: <?php echo $vendordetail['vendor_tax_reg_no']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Legal Structure <span class="f-10"></span>*: <?php echo isset($company_types[$vendordetail['business_type']]) ? $company_types[$vendordetail['business_type']]['company_type_name'] : $vendordetail['business_type']; ?>
                    </td>
                    <td class="f-14">
                        Country: <?php echo isset($countrylist[$vendordetail['company_registration_country']]) ? $countrylist[$vendordetail['company_registration_country']] : $vendordetail['company_registration_country']; ?>
                    </td>
                    <td class="f-14">
                        DUNS: <?php echo $vendordetail['duns_no']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Company Registration: <?php echo $vendordetail['company_registration_number']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Company Registration Country: <?php echo isset($countrylist[$vendordetail['company_registration_country']]) ? $countrylist[$vendordetail['company_registration_country']] : $vendordetail['company_registration_country']; ?>
                    </td>
                    <td class="f-14">
                        Payment Term: <?php echo isset($payment_terms[$vendordetail['payment_terms']]) ? $payment_terms[$vendordetail['payment_terms']]['payment_term_name'] : $vendordetail['payment_terms']; ?>
                    </td>
                    <td class="f-14">
                        INCO Terms / Shipping Terms <span class="f-10">(Point of)</span>: <?php echo isset($incoterms[$vendordetail['incoterm_id']]) ? $incoterms[$vendordetail['incoterm_id']]['incoterm_name'] : $vendordetail['incoterm_id']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        PAN No*: <?php echo emptytextdashShow($vendordetail['pan_no']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        GSTIN No*: <?php echo emptytextdashShow($vendordetail['gstin']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        TAN No: <?php echo emptytextdashShow($vendordetail['tan_number']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="f-14">
                        MSME Registered: <?php echo $vendordetail['msme_register_yes_no'] == 'Y' ? 'Yes' : 'No'; ?>
                    </td>
                    <td colspan="3" class="f-14">
                        MSME Number: <?php echo $vendordetail['msme_register_yes_no'] == 'Y' ? emptytextdashShow($vendordetail['msme_number']) : 'N/A'; ?>
                    </td>
                </tr>
                <tr style="background-color: #eeeeee;">
                    <th scope="col" colspan="6" class="f-14">
                        Company EFT / ACH / Wire Banking Information <span style="font-size:12px;">(Please attach support): </span>
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Bank Name: <?php echo emptytextdashShow($vendordetail['bank_name']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Country: <?php echo isset($countrylist[$vendordetail['bank_country']]) ? $countrylist[$vendordetail['bank_country']] : emptytextdashShow($vendordetail['bank_country']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Remit Currency <span class="f-10">: <?php echo emptytextdashShow($vendordetail['currency_id']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Bank Address: <?php echo emptytextdashShow($vendordetail['bank_address']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        City / Province / State: <?php echo isset($citieslist[$vendordetail['bank_city']]) ? $citieslist[$vendordetail['bank_city']] : emptytextdashShow($vendordetail['bank_city']); ?> / <?php echo isset($stateslist[$vendordetail['bank_state']]) ? $stateslist[$vendordetail['bank_state']] : emptytextdashShow($vendordetail['bank_state']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Postal / Zip Code: <?php echo emptytextdashShow($vendordetail['bank_post_code']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Company Bank Beneficiary Name <span class="f-10">(Name to appear on payment)</span>: <?php echo emptytextdashShow($vendordetail['bank_beneficiary_name']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Bank Account Number: <?php echo emptytextdashShow($vendordetail['bank_account_no']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Type of Bank Account: <?php echo isset($bank_acoount_type[$vendordetail['account_type']]) ? $bank_acoount_type[$vendordetail['account_type']] : emptytextdashShow($vendordetail['account_type']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        IFSC Code: <?php echo emptytextdashShow($vendordetail['ifsc_code']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        SWIFT/BIC Code: <?php echo emptytextdashShow($vendordetail['swift_bic_code']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        IBAN: <?php echo emptytextdashShow($vendordetail['iban_code']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Routing - ABA / DFI # / Sort Code <span class="f-10">(ACH)</span>: <?php echo emptytextdashShow($vendordetail['routing_aba_dfi_ach']); ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Routing - Wire Code: <?php echo $vendordetail['routing_wire_Code']; ?>
                    </td>
                    <td class="f-14">
                        Swift / BIC Code: <?php echo $vendordetail['swift_bic_code']; ?>
                    </td>
                    <td class="f-14">
                        IBAN <span class="f-10">(if Applicable)</span>: <?php echo $vendordetail['iban_code']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Contact within your company for Banking Questions: <?php echo $vendordetail['bank_query_person_name']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        *Tel #: <?php echo $vendordetail['bank_query_contact_no']; ?>
                    </td>
                    <td colspan="2" class="f-14">
                        Email Address: <?php echo$vendordetail['bank_query_email_id']; ?>
                    </td>
                </tr>

                <tr style="background-color: #eeeeee;">
                    <th scope="col" colspan="6" class="f-14">
                        Contact Information:
                    </th>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;" colspan="2">Type</th>
                    <th scope="col" style="text-align: left;">Name</th>
                    <th scope="col" style="text-align: left;">Number</th>
                    <th scope="col" style="text-align: left;">Email</th>
                </tr>
                <?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
                    <tr>
                        <td colspan="2">
                            <?php echo $vcival; ?>
                        </td>
                        <td>
                            <?php echo emptytextdashShow($other_contact_information[$vcikey]['name']); ?>
                        </td>
                        <td>
                            <?php echo emptytextdashShow($other_contact_information[$vcikey]['number']); ?>
                        </td>
                        <td>
                            <?php echo emptytextdashShow($other_contact_information[$vcikey]['email_id']); ?>
                        </td>
                    </tr>
                    <?php } ?>



            <?php if($transaction_table == "vendor_registers"){ ?>
                <tr style="background-color: #eeeeee;">
                    <th scope="col" colspan="6" class="f-14">
                        All Vendors to Reply:
                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="6" class="f-14">
                        Quality System:
                    </th>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <?php echo answerYesOrNo($vendor_questions['quality_system']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['quality_system']) == 'No' ? '☑' : '☐'; ?> No
                        Does your company have a quality system? if yes, please provide type and expiration. **Attach copy of certificates when submitting vendor request form.
                        <?php if ($vendor_questions['quality_system'] == 'Y') { ?>
                            <br><strong>Certificate Details:</strong>
                            <?php for ($i = 1; $i <= 2; $i++) {
                                if (!empty($vendor_questions['quality_system_certificate']['name'][$i])) { ?>
                                    <br><?php echo $i; ?>. <?php echo $vendor_questions['quality_system_certificate']['name'][$i]; ?>
                                    (<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate'][$i]); ?> - <?php echo fromDate($vendor_questions['quality_system_certificate']['todate'][$i]); ?>)
                                <?php }
                            } ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="col" colspan="6" class="f-14">
                        Small Business Registered:
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="f-14">
                        Self Certified: <?php echo ($vendor_questions['small_business_registerd']['1'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Sam.gov Registered: <?php echo ($vendor_questions['small_business_registerd']['2'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="font-size: 15px; border: 1px solid #eeeeee; text-align: left; font-weight: bold; white-space: normal;">
                        Not Applicable: <?php echo (empty($vendor_questions['small_business_registerd'])) ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Unique Entity : <?php echo $vendor_questions['small_business_registerd']['101']; ?>
                    </td>
                    <td class="f-14">
                        Cage Code: <?php echo $vendor_questions['small_business_registerd']['102']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="f-14">
                        Small Disadvantaged: <?php echo ($vendor_questions['small_business_registerd']['3'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Women Owned: <?php echo ($vendor_questions['small_business_registerd']['4'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="font-size: 15px; border: 1px solid #eeeeee; text-align: left; font-weight: bold; white-space: normal;">
                        Veteran Owned: <?php echo ($vendor_questions['small_business_registerd']['5'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Service-Disabled Veteran: <?php echo ($vendor_questions['small_business_registerd']['6'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Small Business: <?php echo ($vendor_questions['small_business_registerd']['7'] == 'Y') ? '☑' : '☐'; ?>
                    </td>
                    <td class="f-14">
                        Other: <?php echo $vendor_questions['small_business_registerd']['100']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="col" colspan="6" class="f-14">
                        Supply Chain Responsibility:
                    </th>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        1) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['1']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['1']) == 'No' ? '☑' : '☐'; ?> No
                        is your company a member of the RBA (Responsible Business Alliance)?
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        2) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['2']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['2']) == 'No' ? '☑' : '☐'; ?> No
                        Are all employees of your company at least 18 years of age?
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        3) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['3']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['3']) == 'No' ? '☑' : '☐'; ?> No
                        Does your business have only 1 employee? if "Yes" skip no. 7. if "No" go to question no. 4.
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <p>4) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4']) == 'No' ? '☑' : '☐'; ?> No
                        Do you have hourly employees that work at a manufacturing facility? For the purposes of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increase or decrease in compensation based on volume of production. If "Yes", please answer "a.", "b.", and "c." below. If "No," skip to question no. 5.</p>
                        <?php if ($vendor_questions['supply_Chain_responsibility']['4'] == 'Y') { ?>
                            <p class="margin-left: 20px;">a). <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']) == 'Yes' ? '☑' : '☐'; ?> Yes
                            <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']) == 'No' ? '☑' : '☐'; ?> No
                            Do all hourly workers work less than 60 hours per week?</p>
                            <p class="margin-left: 20px;">b). <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_b']) == 'Yes' ? '☑' : '☐'; ?> Yes
                            <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_b']) == 'No' ? '☑' : '☐'; ?> No
                            Do you have a policy prohibiting forced, involuntary, indentured, trafficked, or slave labor?</p>
                            <p class="margin-left: 20px;">c). <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_c']) == 'Yes' ? '☑' : '☐'; ?> Yes
                            <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_c']) == 'No' ? '☑' : '☐'; ?> No
                            Do you expect your suppliers to have a policy prohibiting forced, involuntary, indentured, trafficked, or slave labor?</p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <p>5) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5']) == 'No' ? '☑' : '☐'; ?> No
                        Do you retain or hold your employees' original passports or other original personal identification documents? If "Yes", please answer "a." below. If "No", skip to question no. 6.</p>
                        <?php if ($vendor_questions['supply_Chain_responsibility']['5'] == 'Y') { ?>
                            <p class="margin-left: 20px;">a). <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5_a']) == 'Yes' ? '☑' : '☐'; ?> Yes
                            <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5_a']) == 'No' ? '☑' : '☐'; ?> No
                            Do you return original passports or other original personal identification documents upon the employee's request?</p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <p>6) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['6']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['6']) == 'No' ? '☑' : '☐'; ?> No
                        Do any employees have to reimburse recruitment fees to your company?</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <p>7) <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['7']) == 'Yes' ? '☑' : '☐'; ?> Yes
                        <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['7']) == 'No' ? '☑' : '☐'; ?> No
                        Is your company (or your sub-contractor) in the Xinjiang Uygur Autonomous Region (XUAR)?</p>
                    </td>
                </tr>
                <tr>
                    <th scope="col" colspan="6" class="f-14">
                        Additional documentation required, where applicable:
                    </th>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            Banking Details on Company letterhead (matching information provided on page 1) - Required.
                        </p>
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            USA Businesses - Current W9 (Dated within the previous 12 months) - Required.
                        </p>
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            Quote/contract/SOW – Where Applicable
                        </p>
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            ISO90xx, AS9xxs, or other Quality Systems information - Where applicable
                        </p>
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            Businesses located outside of the USA - Current W-8BEN-E, (Dated within the previous 12 months) - Where applicable.
                        </p>
                        <p>
                            <span class="width:5px; height: 5px; border-radius: 100%; background: #000000; margin-right: 10px; margin-left: 20px; display: inline-block; vertical-align: middle;"></span>
                            Local Tax Documents – BIR, Por Por, ITA, PAN, GST - Where applicable.
                        </p>
                    </td>
                </tr>
            <?php } ?>
                <tr>
                    <td colspan="6" class="f-14">
                        Comments/Special instructions: <?php echo$vendordetail['vendor_other_notes']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="f-14">
                        *Name : <?php echo $vendordetail['first_name'] . ' ' . $vendordetail['last_name']; ?>
                    </td>
                    <td colspan="3" class="f-14">
                        *Title : <?php echo $vendordetail['title']; ?>
                    </td>
                </tr>
                <tr>
                    <td  class="f-14">
                        *Signature or digital signature:
                    </td>
                    <td colspan="2" class="f-14">
                        <?php if($vendordetail['signature'] !=""){
                            ?>
                            <img class="img-100" src='uploads/all/<?php echo htmlspecialchars($vendordetail['signature']); ?>' alt="signature" />
                        <?php }elseif($vendordetail['user_id'] == $_SESSION['userid']){ ?>
                            <span class="form-label label label-primary vendoresignpopup cursor-pointer">Click To eSign</span>
                        <?php } ?>
                    </td>
                    <td  class="f-14">
                        *Date:
                    </td>
                    <td colspan="2" class="f-14">
                        <?php echo $vendordetail['signature_date'] !=""?fromDate($vendordetail['signature_date']):"-"; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f-14">
                        Return the completed/signed form to the original <b>Requester</b> and <b><?php echo $_SESSION['config']['admin_email'] ?></b>
                    </td>
                </tr>
            </table>
        </div>
         <button class="btn waves-effect waves-light btn-grd-success mt-3 printDivVendor" >
            <i class="fa fa-print"></i>  Print
        </button>
    </div>

<?php } ?>

<input type="hidden" id="ajaxcall_yes_no" value="<?php echo $_POST['ajaxcall']; ?>">

<input type="hidden" id="customer_vendor_gst_billing_details_cnt" value="<?php echo count((array) $customer_vendor_gst_billing_details) ?? 1; ?>">
<div class="d-none" id='gsttype_id'>
    <option value="">Select Any One</option>
    <?php foreach ($gstregistrationtype as $gstkey => $gstregtype) { ?>
        <option <?php echo $customers['gsttype'] == $gstkey ? 'selected' : '' ?> value="<?php echo $gstkey; ?>"><?php echo $gstregtype; ?></option>
    <?php } ?>
</div>
<div class="d-none" id='countrieslist_id'>
    <option value="">Select Any One</option>
    <?php foreach ($countrieslist as $countryvalue) { ?>
        <option value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
    <?php } ?>
</div>
