<?php
if (isset($_POST['submit_vendor'])) {
    $transaction_table = ($_POST['transaction_table'] == 'vendor_registers') ? 'vendor_registers' : 'vendors';
    $data        = $_POST;
    $data_agree  = $_POST;
    $files       = $_FILES;
    $image = imageupload('all', 'image', $_POST['hid_image'] ?? '');
    $data['company_id']   = $_SESSION['company_id'] ?? '';
    $data['image']        = $image;
    $data['display_name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    $data['asof']         = !empty($data['asof']) ? dateconvertdbformat($data['asof']) : '';
    $data['hiring_clause'] = (!empty($_POST['hiring_clause']) && $_POST['hiring_clause'] == 'on') ? '0' : '1';
    $data['tds_apply'] = ($_POST['tds_apply'] == 'on') ? '0' : '1';
    $data['msme_register_yes_no'] = ($_POST['msme_register_yes_no'] == 'Y') ? 'Y' : 'N';
    $data['subsidiary_vendor_check'] = ($_POST['subsidiary_vendor_check'] == 'Y') ? 'Y' : 'N';
    $data['other_contact_information'] = json_encode($_POST['other_contact_information'] ?? []);
    $data['vendor_questions']          = json_encode($_POST['vendor_questions'] ?? []);
    $drop_common = ['submit_vendor','submit_value','transaction_table','hid','hid_image', 'vendor_user_id','ag_type','ag_number', 'ag_ex_date','ag_sin_date','ag_rowid','hidden_agreement_file','customer_vendor_gst_billing_details_id', 'is_default', 'customer_id'];
    foreach ($drop_common as $k) {
        unset($data[$k]);
    }
    $is_default = $_POST['is_default'];
    $data['gsttype'] = $_POST['gsttype'][$is_default]  ?? '';
    $data['gstin'] = $_POST['gstin'][$is_default]  ?? '';
    $data['billing_address'] = $_POST['billing_address'][$is_default]  ?? '';
    $data['billing_country'] = $_POST['billing_country'][$is_default]  ?? '';
    $data['billing_state'] = $_POST['billing_state'][$is_default]  ?? '';
    $data['billing_city'] = $_POST['billing_city'][$is_default]  ?? '';
    $data['billing_post_code'] = $_POST['billing_post_code'][$is_default]  ?? '';
    $data['contact_person'] = $_POST['contact_person'][$is_default]  ?? '';
    $data['mobile'] = $_POST['mobile'][$is_default]  ?? '';
    $data['fax'] = $_POST['fax'][$is_default]  ?? '';
    $data['shipping_address'] = $_POST['shipping_address'][$is_default]  ?? '';
    $data['shipping_country'] = $_POST['shipping_country'][$is_default]  ?? '';
    $data['shipping_state'] = $_POST['shipping_state'][$is_default]  ?? '';
    $data['shipping_city'] = $_POST['shipping_city'][$is_default]  ?? '';
    $data['shipping_post_code'] = $_POST['shipping_post_code'][$is_default]  ?? '';
    unset($data['vendor_id']);
    foreach ($data as $k => $v) {
        if ($v == '') { $data[$k] = '';
}
    }
    $vendor_id_for_children = '';
    $data['location_id'] = json_encode((array)$_POST['location_id']);

    foreach ($_POST['customer_vendor_gst_billing_details_id'] as $i => $val) {
        if($_POST['billing_address'][$i] !=""){
            $row = array(
                'company_id' => $_SESSION['company_id']??'',
                'is_active' => 0,
                'current_status' => 0,
                'gsttype' => $_POST['gsttype'][$i] ?? '',
                'gstin'   => $_POST['gstin'][$i]   ?? '',
                'billing_address'   => $_POST['billing_address'][$i]   ?? '',
                'billing_country'   => $_POST['billing_country'][$i]   ?? '',
                'billing_state'     => $_POST['billing_state'][$i]     ?? '',
                'billing_city'      => $_POST['billing_city'][$i]      ?? '',
                'billing_post_code' => $_POST['billing_post_code'][$i] ?? '',
                'shipping_address'   => $_POST['shipping_address'][$i]   ?? '',
                'shipping_country'   => $_POST['shipping_country'][$i]   ?? '',
                'shipping_state'     => $_POST['shipping_state'][$i]     ?? '',
                'shipping_city'      => $_POST['shipping_city'][$i]      ?? '',
                'shipping_post_code' => $_POST['shipping_post_code'][$i] ?? '',
                'contact_person' => $_POST['contact_person'][$i] ?? '',
                'mobile'         => $_POST['mobile'][$i]         ?? '',
                'fax'            => $_POST['fax'][$i]            ?? '',
            );
            $row['is_default'] = $is_default == $i ? 'Y' : 'N';
            $row['customer_vendor_gst_billing_details_id']  = $_POST['customer_vendor_gst_billing_details_id'][$i];
            if($row_id == ''){
                $row['created_date']        = date('Y-m-d H:i:s');
                $row['created_by']          = $_SESSION['userid'] ?? '';
                $row['transaction_table']   = $transaction_table ?? '';
                $row['customer_vendor_id']  = $vendor_id_for_children ?? '';
            }else{
                $row['updated_date'] = date('Y-m-d H:i:s');
                $row['updated_by']   = $_SESSION['userid'] ?? '';
            }
            $data['customer_vendor_gst_billing_details'][] = $row;
        }
    }
    // GST Details

    //Documnet uploading Start
    if($_POST['vendor_id'] !="" && $transaction_table == 'vendors'){
        foreach($_POST['doc_rowid'] as $key=>$docname){
            $File_Uploads_Path= "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/vendors/".$_POST['vendor_id']."/Documents/";
            if (!file_exists($File_Uploads_Path)) {
                mkdir($File_Uploads_Path, 0750, true);
            }else{
                chmod($File_Uploads_Path, 0750);
            }
            if($_FILES['doc_filename']['error'][$key] != "4"){
                $temp = explode(".", $_FILES['doc_filename']['name'][$key]);
                $Filename=  "file_".time()."_".uniqid();
                $newfilename = $Filename.'.' .end($temp);
                if(move_uploaded_file($_FILES['doc_filename']["tmp_name"][$key], $File_Uploads_Path.$newfilename)){
                    $data['doc_filename'][$key] = $newfilename;
                }else{
                    $data['doc_filename'][$key] = '';
                }
            }else{
                $data['doc_filename'][$key] = $_POST['hidden_doc_filename'][$key];
            }
        }
    }
    //Documnet uploading End
    if ($_POST['submit_vendor'] == 'Add') {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by']   = $_SESSION['userid'] ?? '';
        $data['is_active']   = 0;
        $data['current_status'] = ST_DRAFT;
        $data['originator'] = $_SESSION['userid'];
        $data['send_verification_profile_status'] = "N";
         $url = API."?mode=insertvendorsandvendorregisters&transaction_table={$transaction_table}&method=post";
        $res = file_post_contents($url, $data, 'insert');
        $response = json_decode($res, true);
        if (is_array($response) && $response['trans_res']['status'] == 'sucess') {
            $vendor_id_for_children = $response['trans_res']['LASTID'];
            $_SESSION['insert_msg'] = 3000;
        ?>
            <input type="hidden" id="hide_error_message_code" value="3000">
            <script type="text/javascript" src="../assets/js/tostarnotify_message.js"></script>
        <?php
            $rid = deocdeId("id:".$vendor_id_for_children);
            header("location:index.php?option=".$page_option."&Rid=".$rid);
        }else{
            if ($response['status'] == 'fail') {
                $dis_err = explode(",", $response['other']);
                foreach ($dis_err as $va) {
                    $dis_er[] = $dis_labels[$va];
                }
                $error_message = $response['error_no'].":".$_SESSION['error'][$response['error_no']] . " : " . implode(", ", (array) $dis_er);
            } else {
                $error_message = $response['message'];
            }
        }
    } else { // UPDATE
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by']   = $_SESSION['userid'] ?? '';
        $vendor_id_for_children = $_POST['vendor_id'] ?? '';
        $ridParam = $_POST['hid'] ?? '';
        $data['rid'] = $vendor_id_for_children;
        $url = API."?mode=insertvendorsandvendorregisters&transaction_table={$transaction_table}&method=post";
        $res = file_post_contents($url, $data, 'update');
        $response = json_decode($res, true);
        if (is_array($response) && $response['trans_res']['status'] == 'sucess') {
        ?>
            <input type="hidden" id="hide_error_message_code_2" value="3001">
            <script type="text/javascript" src="../assets/js/tostarnotify_message.js"></script>
            <?php
        }
    }
    // 9) CHILD: Agreements (only when the main table is vendors)


}

if ($_GET['VTYPE'] != "VR") {
    if ($_GET['Rid'] != "") {
        $rid1 = $_GET['Rid'];
    } else {
        $rid1 = deocdeId('id:' . $_SESSION['vendor_id']);
    }
    $rid =  encodeId($rid1);
    $rid = urldecode($rid);
    $vendorid = explode(":", $rid);
    $vendor_id = $vendorid['1'];
    $transaction_table = "vendors";
} else {
    if ($_GET['Rid'] != "") {
        $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $vendorid = explode(":", $rid);
        $vendor_id = $vendorid['1'];
    }
    $transaction_table = "vendor_registers";
}

if($_GET['VRRid'] !=""){
    $VRid =  encodeId($_GET['VRRid']);
    $VRid = urldecode($VRid);
    $VRRid = explode(":", $VRid);
    $VRRid = $VRRid['1'];
}
$url = API . "?mode=getvendorsdetails&transaction_table=" . $transaction_table . "&id=" . $vendor_id . "&admin_type=" . $_SESSION['admin_type']."&working_role_short_code=".$_SESSION['working_role_short_code']."&VRRid=".$VRRid;
$response = callAPI($url);
$result = json_decode($response, 1);
$vendordetail = $result['vendors'];

$accounttype = $result['accounttyperes'];
$companytype = $result['companytyperes'];
$vendor_id = $vendordetail['id'];
$aggrements = $result['vendor_agreements'];
$payment_method = $result['payment_method'];
$salestype = $result['salestype'];
$emplist = $result['employess'];
$countrieslist = $result['countries'];
$states = $result['states'];
$cities = $result['cities'];
$vendorslist = $result['vendorslist'];
$paymenttype = $result['paymenttype'];
$vendor_type = $result['vendor_type'];
$countrylist = $stateslist = $citieslist =  array();
foreach ($countrieslist as $country_list) {
    $countrylist[$country_list['id']] = $country_list['country_name'];
}
foreach ($states as $states_list) {
    $stateslist[$states_list['id']] = $states_list['state_name'];
}
foreach ($cities as $cities_list) {
    $citieslist[$cities_list['id']] = $cities_list['city_name'];
}
$vendor_contact_info_type = $result['vendor_contact_info_type'];
$bank_acoount_type = $result['bank_acoount_type'];
$other_contact_information = json_decode($vendordetail['other_contact_information'], 1);
$vendor_questions = json_decode($vendordetail['vendor_questions'], 1);
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
$stausresult = ViewStatusData();
$taskscategories = array();
foreach ($result['tasks_categories'] as $tasks_categories) {
    $taskscategories[$tasks_categories['id']] = $tasks_categories['category_name'];
}
$transactionactivityschedule = array();
$transactionactivityschedulecompletedstatus = array();
$rejetcedcnt = $totaltasks = $yettostartcnt = $totalinprogresstask = $completedcnt = $totalapprovedtask = $totaldelayedcnttask = $totaltaskreview_cnt =  0;
foreach ($result['transaction_activity_schedule'] as $transaction_activity_schedule) {
    if ($transaction_activity_schedule['current_status'] == ST_COMPLETE) {
        $transactionactivityschedulecompletedstatus[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
        $totaltaskreview_cnt++;
    } else {
        $transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    }
    if ($transaction_activity_schedule['current_status'] == ST_APPROVED) {
        $complitedtask[$transaction_activity_schedule['process_id']]++;
        $totalapprovedtask++;
    } elseif ($transaction_activity_schedule['current_status'] == ST_COMPLETE) {
        $completedcnt++;
    } elseif ($transaction_activity_schedule['current_status'] == ST_REJECTED) {
        $rejetcedcnt++;
    } elseif ($transaction_activity_schedule['current_status'] == ST_INPROGRESS) {
        $totalinprogresstask++;
    } elseif ($transaction_activity_schedule['current_status'] == ST_ACTIVE) {
        $yettostartcnt++;
    }
    if ($transaction_activity_schedule['link_page'] == '0') {
        $linktransactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
        if ($transaction_activity_schedule['current_status'] == ST_APPROVED) {
            $linkcomplitedtask[$transaction_activity_schedule['process_id']]++;
        }
    }
    $totaltask[$transaction_activity_schedule['process_id']]++;
    $totaltasks++;
}
$File_Uploads_Path = "uploads/all/";
if ($vendordetail['image'] != '') {
    $image = $File_Uploads_Path . $vendordetail['image'];
} else {
    $image = $File_Uploads_Path . "no-image.jpg";
}

$profilereadiness = array("Business Information" => array('company', 'website_url', 'company_trading_name', 'company_subsidiary_division', 'first_name', 'middle_name', 'last_name', 'phone', 'email', 'phone', 'billing_address', 'billing_country', 'billing_state', 'billing_city', 'billing_post_code', 'contact_person', 'mobile', 'shipping_address', 'shipping_country', 'shipping_state', 'shipping_city', 'shipping_post_code'), "Company Information" => array('vendor_tax_reg_no', 'business_type', 'duns_no', 'company_registration_number', 'company_registration_country', 'payment_terms', 'gstin', 'pan_no', 'tan_number', 'incoterm_id', 'image', 'msme_number'), "Banking Information" => array('bank_name', 'bank_address', 'bank_country', 'bank_state', 'bank_city', 'bank_post_code', 'bank_beneficiary_name', 'bank_account_no', 'account_type', 'routing_aba_dfi_ach', 'routing_wire_Code', 'swift_bic_code', 'iban_code', 'bank_query_person_name', 'bank_query_contact_no', 'bank_query_email_id', 'ifsc_code'), "Note" => array('vendor_po_notes', 'vendor_other_notes'));

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
$vendor_questions = json_decode($vendordetail['vendor_questions'], 1);
$verification_profile_users = json_decode($vendordetail['verification_profile_users'], 1);
$transaction_activity_schedule_cnt = $result['transaction_activity_schedule_cnt'];
$vendor_verifications = json_decode($vendordetail['vendor_verifications'], 1);
$vendor_display_fields = $result['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'], 1);

if($vendordetail['msme_register_yes_no'] =="Y"){
    $required_document_verifications[] = "MSME";
}

if($_SESSION['insert_msg'] !=""){
    $_SESSION['insert_msg'] = "";
    ?>
    <input type="hidden" id="hide_error_message_code_3" value="3000">
    <script type="text/javascript" src="../assets/js/tostarnotify_message.js"></script>
<?php
}

?>

<script type="text/javascript" src="../assets/js/custom_insert_vendors_and_vendor_registers.js"></script>
<script type="text/javascript" src="../assets/js/custom_insert_transaction_activity_schedules.js"></script>
<script type="text/javascript" src="../assets/js/custom_insert_dynamic_status_popup.js"></script>
<input type="hidden" id="hide_get_tab" value="<?php echo $_GET['T1']; ?>">
<input type="hidden" id="hid_vendor_id" value="<?php echo $isAdd ? '' : $vendor_id; ?>" />
<input type="hidden" id="hid_transaction_table" value="<?php echo $transaction_table; ?>" />
<input type="hidden" id="hid_vtype" value="<?php echo $_GET['VTYPE']; ?>" />
<input type="hidden" id="hid_required_document_verifications" value='<?php echo json_encode($required_document_verifications); ?>' />

<?php

if ($vendordetail['instruction_status'] == "N" && $transaction_table == "vendor_registers" && $vendordetail['user_id'] == $_SESSION['userid']) {
    include_once 'views/custom_view_vendor_instructions.php';
} else { ?>

    <?php if ($error_message != '') { ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
    <div class="card mb-1 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <div class="banner">
                <img src="assets/images/user-profile/bg-img1.jpg" class="w-100 b-radius-20" alt="">
            </div>
        </div>
        <div class="profile-img" style="margin:-40px 0 0 0;">
            <div class="row align-items-bottom">
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <div class="image-input image-input-empty image-input-outline" id="kt_image_5">
                        <form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
                            <label class="btn-hover-text-primary position-absolute  text-white" data-action="remove" data-bs-toggle="tooltip" title="Edit Profile Pic" style="left: -3px; top: -2px;">
                                <span><i class="ti-pencil-altj"></i></span>
                                <input aria-label="Profile Pic File" type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg" onchange="updateProfilePic()" />
                            </label>
                            <?php if ($vendordetail['image'] != "") { ?>
                                <img src="<?php echo $image; ?>" class="img-radius img-90" alt="">
                            <?php  } else { ?>
                                <div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
                                    <span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40" style=" border-radius: 50%; line-height: 1.1;"><?php echo $vendordetail['display_name'] != "" ? initials($vendordetail['display_name']) : "NA"; ?></span>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                    <div class="mt-3 d-block mb-3">
                        <div class="d-flex mr-3 mb-2">
                            <?php if ($vendordetail['company'] != "") { ?>
                                <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
                                    <?php echo $vendordetail['company']; ?>
                                    <small class="d-block f-14"><?php echo $vendordetail['display_name']; ?></small>
                                </a>
                            <?php } else {  ?>
                                <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
                                    <?php echo $vendordetail['display_name']; ?></a>
                            <?php } ?>
                        </div>
                        <?php if ($vendordetail['email'] != "") { ?>
                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $vendordetail['email']; ?></a>
                        <?php }
                        if ($vendordetail['phone'] != "") {  ?>
                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                <i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo DynamicPhoneNumberFormt($vendordetail['phone']); ?></a>
                        <?php }  ?>
                        <?php if ($vendordetail['billing_address'] != "") { ?>
                            <a class="font-weight-bold text-dark-50 d-block mb-2 mt-2"><i class="ti-location-pin text-primary f-18"></i>&nbsp;<?php echo $vendordetail['billing_address'] . " " . $vendordetail['post_code']; ?> </a>
                        <?php } ?>
                        <div class="font-weight-bold text-dark-50 mt-1 f-14">
                            <?php if ($vendordetail['established_year'] != "" && $vendordetail['established_year'] != "0") {  ?>
                                <b class="f-w-600">Established Year : </b><?php echo $vendordetail['established_year']; ?>&nbsp;
                            <?php }  ?>
                        </div>
                    </div>

                    <?php
                    if ($verification_profile_users[$_SESSION['userid']]['complete'] != '' && ($transaction_table == "vendor_registers" || $transaction_table == "vendors")) {
                        $total_approved = $transaction_activity_schedule_cnt['total_approved'] != "" ? $transaction_activity_schedule_cnt['total_approved'] : 0;
                        if ($transaction_activity_schedule_cnt['total_task'] != $total_approved) {
                            echo "<span class='text-danger'><i class='ti-alert'></i>" . $_SESSION['error_message']['9125'] . "</span>";
                            $verification_profile_users_status = 'event_disabled';
                        } elseif ($verification_profile_users[$_SESSION['userid']]['status'] == 'Y') {
                            $verifivcatiostatus_y = "checked";
                            $verification_profile_users_status = 'event_disabled';
                            $lablestauss = '';
                            $lablestauss .= '<span class="d-block"> <span class="f-w-700">Comment : </span>' . $verification_profile_users[$_SESSION['userid']]['comment'] . '</span>';
                            $lablestauss .= '<span class="d-block"> <span class="f-w-700">Reviewed Date Time : </span>' . $verification_profile_users[$_SESSION['userid']]['updatedatetime'] . '</span>';
                        } elseif ($verification_profile_users[$_SESSION['userid']]['status'] == 'N') {
                            $verifivcatiostatus_n = "checked";
                            $verification_profile_users_status = 'event_disabled';
                            $lablestauss .= '<span class="d-block"> comment : ' . $verification_profile_users[$_SESSION['userid']]['comment'] . '</span>';
                            $lablestauss .= '<span class="d-block"> Reviewed Date Time : ' . $verification_profile_users[$_SESSION['userid']]['updatedatetime'] . '</span>';
                        }
                    ?>
                        <div class="card <?php echo $verification_profile_users_status; ?>" >
                            <div class="card-body">
                                <span class="text-info "><i class="ti-alert"></i> I have reviewed the information provided for this vendor and am confident in their ability to successfully fulfill the agreed-upon terms of the contract</span>
                                <div class="mt-3" id="dispabled_div">
                                    <div class="checkbox-fade fade-in-primary showComment" data-showCommenttype="Y">
                                        <label class="form-label">
                                            <input aria-label="Checkbox" type="checkbox" value="" <?php echo $verifivcatiostatus_y; ?> class="coomment_check_box">
                                            <span class="cr">
                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                            </span>
                                            <span>I Recommend</span>
                                        </label>
                                    </div>
                                    <div class="checkbox-fade fade-in-primary text-danger showComment" data-showCommenttype="N">
                                        <label class="form-label">
                                            <input aria-label="Checkbox" type="checkbox" value="" <?php echo $verifivcatiostatus_n; ?> class="coomment_check_box">
                                            <span class="cr">
                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                            </span>
                                            <span>I Object</span>
                                        </label>
                                    </div>
                                    <?php echo $lablestauss; ?>
                                </div>
                                <div class="mt-3 row d-none" id="profile_review_comment_div" >
                                    <div class="col-lg-8">
                                        <span class="form-label">Comment <span class="text-danger">*</span></span>
                                        <input type="hidden" class="form-control" id="selected_check_box">
                                        <textarea aria-label="Profile Review Comments" class="form-control" id="profile_review_comments"></textarea>
                                        <span class="text-danger d-none" id="profile_review_comments_error" >Please Enter Comment</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <button class="btn btn-warning waves-effect waves-light mt-4 vendorProfileUpdateStatus" > Save</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <button class="btn btn-danger waves-effect waves-light mt-4 cancelComment"> Cancel</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php  } ?>
                </div>
                <div class="col-lg-6 p-3">


                </div>

            </div>
            <div class="row p-l-10 p-r-10">
                <div class="col-lg-6 p-3 text-left">
                    <?php $allaproveprofile = 0;
                    if ($vendordetail['send_verification_profile_status'] == "Y") { ?>
                        <span class="text-warning d-block">* Profile Verification In-Process</span>
                        <?php if ($vendordetail['user_id'] != $_SESSION['userid']) {
                            echo "<div class=' mt-2'>";
                            foreach ($verification_profile_users as $uidkey => $vpu) {
                                $bgcolor = "";
                                $label_title = $_SESSION['users'][$uidkey] . " : ";
                                if ($vpu['status'] == 'Y') {
                                    $label_title .=  " recommend";
                                    $bgcolor = "bg-success";
                                } elseif ($vpu['status'] == 'N') {
                                    $label_title .=  " Object";
                                    $bgcolor = "bg-danger";
                                    $allaproveprofile = 1;
                                } else {
                                    $label_title .=  " Pending";
                                    $allaproveprofile = 1;
                                }

                        ?>
                                <span alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round f-12 text-center <?php echo $bgcolor; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $label_title; ?>"><?php echo initials($_SESSION['users'][$uidkey]); ?></span>
                        <?php }
                            echo "</div>";
                        } ?>
                    <?php } ?>
                </div>
                <div class="col-lg-6 p-3 text-right">
                    <?php if ($transaction_table == "vendor_registers") { ?>
                        <span class="text-info">* Registration in-progress</span>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
                        <li class="nav-item">
                            <a class="nav-link updateUrlParameter_tab <?php echo ($_GET['T1'] == 'overview_tab' || $_GET['T1'] == "") ? "active" : ""; ?>" data-bs-toggle="tab" href="#overview_tab" data-param="T1" data-paramvalue ="overview_tab" role="tab" >Overview</a>
                            <div class="slide"></div>
                        </li>
                        <?php if($vendor_id !=""){ ?>
                        <li class="nav-item">
                            <a class="getAjaxDynamicPage nav-link <?php echo $_GET['T1'] == 'task_tab' ? "active" : ""; ?>" data-trans-table="custom_insert_transaction_activity_schedules" data-trans-tab="task_tab" data-bs-toggle="tab" href="#task_tab" role="tab" >Task</a>
                            <div class="slide"></div>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
                <div class="col-lg-6 text-right">
                    <?php
                    if (($vendordetail['user_id'] == $_SESSION['userid'] || $vendordetail['originator'] == $_SESSION['userid']) && $vendordetail['send_verification_profile_status'] == "N" && $transaction_table == "vendor_registers"){ ?>
                        <button class="btn btn-danger waves-effect waves-light SubmitForVendorCreation" ><i class="ti-thumb-up"></i> Submit For Approval</button>
                    <?php } ?>

                    <?php if (($vendor_id !="" && $vendordetail['vendor_id'] == "" && $_SESSION['admin_type'] == '1' && $totalapprovedtask == $totaltasks && $transaction_table == "vendor_registers" && $vendordetail['send_verification_profile_status'] == "Y" && $allaproveprofile == '0' && $vendordetail['vendor_type_id'] == "1") || ($vendordetail['vendor_type_id'] != "1" && $vendordetail['vendor_id'] == "" && ($vendor_id !="" && $_SESSION['admin_type'] == '1' || $vendordetail['originator'] == $_SESSION['userid'])) && $transaction_table == "vendor_registers") { ?>
                        <button class="btn btn-primary waves-effect waves-light createVendorRegisterToVendor" ><i class="ti ti-user"></i> Create Vendor</button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content card-block p-0">
        <div class="tab-pane <?php echo ($_GET['T1'] == 'overview_tab' || $_GET['T1'] == "") ? "active" : ""; ?>" id="overview_tab" role="tabpanel">
            <div class="row">
                <div class="col-lg-3">
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
                                        $complete_field =  $total_field = 0;
                                        $total_field = count((array)$profilereadines);
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
                                                            <div class="progress-bar progress-bar-<?php echo round($percentage) >= 50 ? "success" : "danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>Overview</h5>
                            <div class="card-header-right" id="chnagebtndiv">

                                <?php

// )
// ) {
    if(($vendor_id == "" || $vendordetail['send_verification_profile_status'] == "N") && ($vendordetail['originator'] == $_SESSION['userid'] || $vendordetail['user_id'] == $_SESSION['userid'] || $vendordetail['created_by'] == $_SESSION['userid']) || $transaction_table == "vendors"){
                                ?>
                                    <button class="btn btn-primary editVendorProfile" data-functiontype = "EDIT"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body" id="ajaxvendorprofile">
                            <?php  include_once 'views/custom_insert_vendors_and_vendor_registers_ajax.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane <?php echo $_GET['T1'] == 'task_tab' ? "active" : ""; ?>" id="task_tab" role="tabpanel">
            ?>
        </div>
    </div>
<?php } ?>
