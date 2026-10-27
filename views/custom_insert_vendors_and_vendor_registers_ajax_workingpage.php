<?php

$vendor_id          = $_POST['vendor_id']          ?? ($vendor_id          ?? '');
$transaction_table  = $_POST['transaction_table']  ?? ($transaction_table  ?? 'vendors');
$vtype              = $_POST['vtype']              ?? ($_GET['VTYPE']      ?? '');

// - non-empty id      -> VIEW (read-only summary)
$edittype = $_POST['edittype'] ?? (empty($vendor_id) ? 'ADD' : '');

$isAdd  = ($edittype == 'ADD');
if($_POST['ajaxcall'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $vendor_id = $_POST['vendor_id'];
    $transaction_table = $_POST['transaction_table'];
    $edittype = $_POST['edittype'];
}
    $url = API
        . "?mode=getvendorsdetails"
        . "&transaction_table=" . $transaction_table
        . "&id=" . $vendor_id
        . "&admin_type=" . $_SESSION['admin_type']
        . "&edittype=" . $edittype;
    $response = callAPI($url);
    $result   = json_decode($response, true);

    $role = $result['vendors'];
    $vendor_registers = $result['vendor_registers'];

    $vendordetail = $vendor_registers;
    if($_POST['vtype'] !="VR"){
        $vendordetail = $result['vendors'];
    }else{
        $vendordetail = $result['vendor_registers'];
    }

    $vendordetail = (array)$vendordetail;


    // ensure all fields exist
    $ensure = ['company','website_url','company_trading_name','company_subsidiary_division',
    'first_name','middle_name','last_name','email','phone','mobile','image',
    'billing_address','billing_country','billing_state','billing_city','billing_post_code',
    'shipping_address','shipping_country','shipping_state','shipping_city','shipping_post_code',
    'vendor_tax_reg_no','business_type','duns_no','company_registration_number','company_registration_country',
    'gstin','pan_no','tan_number','msme_register_yes_no','msme_number','incoterm_id',
    'bank_name','bank_address','bank_country','bank_state','bank_city','bank_post_code',
    'bank_beneficiary_name','bank_account_no','account_type','ifsc_code','routing_aba_dfi_ach',
    'routing_wire_Code','swift_bic_code','iban_code','bank_query_person_name','bank_query_contact_no',
    'bank_query_email_id','title','suffix','gsttype','parent_vendor','cst_reg_no',
    'pref_payment_method','preferred_deliver_method','opening_balance','asof','pay_with_me',
    'credit_limit','default_discount_perc','payment_discount_perc','sales_type',
    'vendor_po_notes','vendor_other_notes','subsidiary_vendor_check','tds_apply'
    ];

    foreach ($ensure as $k) {
        if (!array_key_exists($k, $vendordetail)) {
            $vendordetail[$k] = '';
        }
    }

    // default toggles
    if ($isAdd) {
        $vendordetail['msme_register_yes_no']  = 'N';
        $vendordetail['subsidiary_vendor_check'] = 'N';
        $vendordetail['tds_apply'] = 1;
    }

    // normalize vendor_questions
    $vendor_questions = $vendordetail['vendor_questions'];
    if (!is_array($vendor_questions)) {
        $vendor_questions = json_decode((string)$vendor_questions, true) ?: [];
    }
    if ($isAdd && empty($vendor_questions)) {
        $vendor_questions = [
            'radio_associate' => 'N',
            'associated_team_detail' => '',
            'quality_system' => 'N',
            'quality_system_certificate' => [
                'name' => ['1'=>'','2'=>''],
                'fromdate' => ['1'=>'','2'=>''],
                'todate' => ['1'=>'','2'=>''],
            ],
            'small_business_registerd' => ['100'=>'','101'=>'','102'=>''],
            'supply_Chain_responsibility' => [
                '1'=>'N','2'=>'N','3'=>'N','4'=>'N','4_a'=>'N','4_b'=>'N','4_c'=>'N','5'=>'N','5_a'=>'N','6'=>'N','7'=>'N'
            ],
            'terms_conditions' => ['1'=>'N','2'=>'N'],
        ];
    }

    $vendordetail['vendor_questions'] = $vendor_questions;

    $vendor_id = $vendordetail['id'];
    $aggrements =$result['vendor_agreements'];

    $payment_method =$result['payment_method'];
    $salestype =$result['salestype'];
    $emplist = $result['employess'];
    $countrieslist = $result['countries'];
    $states = $result['states'];
    $cities = $result['cities'];
    foreach($result['vendorslist'] as $vend){
        $vendorslist[$vend['id']] = $vend;
    }
    $paymenttype = $result['paymenttype'];
    $vendor_type = $result['vendor_type'];
    $countrylist =$stateslist = $citieslist =  array();
    foreach($countrieslist as $country_list){
        $countrylist[$country_list['id']] = $country_list['country_name'];
    }
    foreach($states as $states_list){
        $stateslist[$states_list['id']] = $states_list['state_name'];
    }
    foreach($cities as $cities_list){
        $citieslist[$cities_list['id']] = $cities_list['city_name'];
    }
    $customer_vendor_gst_billing_details = $result['customer_vendor_gst_billing_details'];
    $gstregistrationtype = $result['gstregistrationtype'];
    $vendor_contact_info_type = $result['vendor_contact_info_type'];
    $bank_acoount_type = $result['bank_acoount_type'];
    $other_contact_information = json_decode($vendordetail['other_contact_information'],1);
    $company_types = array();
    foreach($result['company_types'] as $ctype) {
        $company_types[$ctype['id']] = $ctype;
    }

    $payment_terms = array();
    foreach($result['payment_terms'] as $payt) {
        $payment_terms[$payt['id']] = $payt; //payment_term_name
    }
    $incoterms = array();
    foreach($result['incoterms'] as $payt) {
        $incoterms[$payt['id']] = $payt; //incoterm_name
    }

    $transaction_activity_schedule = $result['transaction_activity_schedule'];
    $vendor_verifications = json_decode($vendordetail['vendor_verifications'],1);
    $vendor_display_fields = $result['vendor_display_fields'];
    $vafield = json_decode($vendor_display_fields['field_list'],1);

    $item_categories = $result['item_categories'];
    $items = $result['items'];

function answerYesOrNo($answ){
    $return = "<span class='text-danger'>Pending<span>";
    if($answ == "Y"){
        $return = "Yes";
    }elseif($answ == "N"){
        $return = "No";
    }
    return $return;
}
$profilereadiness = array("Business Information"=>array('company','website_url','company_trading_name','company_subsidiary_division','first_name','middle_name','last_name','phone','email','phone','billing_address','billing_country','billing_state','billing_city','billing_post_code','contact_person','mobile','shipping_address','shipping_country','shipping_state','shipping_city','shipping_post_code'),"Company Information"=>array('vendor_tax_reg_no','business_type','duns_no','company_registration_number','company_registration_country','payment_terms','gstin','pan_no','tan_number','incoterm_id','image','msme_number'),"Banking Information"=>array('bank_name','bank_address','bank_country','bank_state','bank_city','bank_post_code','bank_beneficiary_name','bank_account_no','account_type','routing_aba_dfi_ach','routing_wire_Code','swift_bic_code','iban_code','bank_query_person_name','bank_query_contact_no','bank_query_email_id','ifsc_code'),"Note"=>array('vendor_po_notes','vendor_other_notes'));

$File_Uploads_Path = "uploads/all/";

?>

<script type="text/javascript">
function checkVendorValidationEmpty(){
    var company  = $.trim($("#company").val());
    var first    = $.trim($("#first_name").val());
    var last     = $.trim($("#last_name").val());
    var email    = $.trim($("#email").val());
    var pan_no    = $.trim($("#pan_no").val());
    $('#company_name_err').hide();
    $('#first_name_err').hide();
    $('#last_name_err').hide();
    $('#email_err').hide();
    $("#allreadycompanyname").hide();
    $("#pan_no_err").hide();

    if (company === "") {
        console.log('company');
        $('#company_name_err').show();
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter company.", '');
        return false; // stop submit
    }
    if (first === "") {
        console.log('first');
        $('#first_name_err').show();
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter First Name.", '');
        return false; // stop submit
    }
    if (last === "") {
        console.log('last');
        $('#last_name_err').show();
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter Last Name.", '');
        return false; // stop submit
    }
    if (email === "") {
        console.log('email');
        $('#email_err').show();
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter Email Id.", '');
        return false; // stop submit
    }
    if (pan_no === "") {
        console.log('pan_no');
        $('#pan_no_err').show();
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Please Enter PAN No.", '');
        return false; // stop submit
    }
    console.log('ceck');
    $("#submitvendordetailform").submit();
}
function getmSMENumber(){
    if($("#msme_register_yes_no").is(":checked")) {
        $("#msme_number_id").show('slow');
    }else{
        $("#msme_number_id").hide('slow');
    }
}

</script>
<?php if($_POST['edittype'] == 'SendVerificationProfile'){ $notsendtoapprove = 0; ?>
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
                        <?php foreach($profilereadiness as $keylabel=>$profilereadines){
                          $complete_field =  $total_field = 0;
                          $total_field = count((array)$profilereadines);
                          foreach($profilereadines as $profilereadines){
                            if($vendordetail[$profilereadines] != ""){
                              $complete_field++;
                            }
                          }
                          $percentage = (($complete_field/$total_field)*100);
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
                                        <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
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

                $transactionactivityschedule = $pendingtask =  array();
                foreach($transaction_activity_schedule as $tas){
                    if($tas['current_status'] == ST_ACTIVE){
                        $notsendtoapprove = 1;
                        $transactionactivityschedule[ST_ACTIVE][] = $tas;
                        $pendingtask[] = $tas;
                    }else{
                        $check_list = json_decode($tas['check_list'], 1);
                        $alldone = $requerdcnt =$mandatory = 0;
                        foreach($check_list as $check_lits){
                            if($check_lits['checklist_completed'] =='Y'){
                                $alldone++; // solve count
                            }
                            if($check_lits['mandatory_checklist'] =='Y'){
                                $requerdcnt++; // mandatory count
                            }
                            if($check_lits['mandatory_checklist']=='Y' && $check_lits['checklist_completed'] !='Y'){
                                $mandatory++; // all mandatory solve requerd
                            }
                        }
                        if($requerdcnt <= $alldone && $mandatory == '0'){
                            $transactionactivityschedule['SEND_FOR_APPROVE'][] = $tas;
                        }else{
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
                        <h5 class="f-w-700 m-b-0"><?php  echo count((array)$transaction_activity_schedule); ?></h5>
                        <p class="m-b-0 f-12">Total Task</p>
                    </div>
                </div>
                <div class="col-3 b-l-default text-center">
                    <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule[ST_ACTIVE]); ?></h5>
                        <p class="m-b-0 f-12">Yet To Start</p>
                    </div>
                </div>
                <div class="col-3 b-l-default text-center">
                    <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule['PENDDING_TASK']); ?></h5>
                        <p class="m-b-0  f-12">In Progress</p>
                    </div>
                </div>
                <div class="col-3 b-l-default text-center">
                    <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo count((array)$transactionactivityschedule['SEND_FOR_APPROVE']); ?></h5>
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
                        if($vendor_questions['terms_conditions']['1'] !="Y" || $vendor_questions['terms_conditions']['2'] !="Y"){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Accept Terms And Conditions In Questions Tab');
                        }
                        if($vendordetail['pan_no'] ==""){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide PAN No.');
                        }
                        if($vendordetail['gstin'] ==""){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide GSTIN No.');
                        }
                        if($vendordetail['bank_account_no'] ==""){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide Bank Account No.');
                        }
                        if($vendordetail['ifsc_code'] ==""){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide IFSC Code');
                        }
                        if($vendordetail['msme_register_yes_no'] =="Y" && $vendordetail['msme_number'] ==""){
                            $notsendtoapprove = 1;
                            $pendingtask[]= array('task_name'=>'<span class="text-danger">*</span> Please Provide MSME Number');
                        }
                ?>
                        <?php if(count((array)$pendingtask) !=0){ ?>
                        <table class="table table-hover m-b-0 " >
                            <?php foreach($pendingtask as $pendingtask){ ?>
                            <tr >
                                <th scope="col" class="p-2"><?php echo $pendingtask['task_name']; ?></th>
                            </tr>
                            <?php } ?>
                        </table>
                        <?php }else{ ?>
                        <div class="card-px text-center py-20 mt-2">
                            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Pending Tasks </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
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
    <?php if($notsendtoapprove == 0){ ?>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="popsubmitforreview_btn" onclick="UpdateSendVerificationProfileStatus()">Submit For Review</button>
<?php }else{ echo "<span class='text-danger'><i class='ti-alert'></i>".$_SESSION['error_message']['9126']."</span>";
} ?>
</div>

<?php }elseif($edittype == 'EDIT' || $edittype == 'ADD'){ ?>
<div class="card">
<?php
if ($isAdd) : ?>
<style>
  /* scope to the form so other pages aren’t affected */
  #submitvendordetailform .custom_readonly {
    pointer-events: auto !important;
    background-color: #fff !important;
  }
</style>
<?php endif; ?>
    <form method="post" id="submitvendordetailform" enctype="multipart/form-data">
        <div class="text-right">
            <?php
                $submitText   = $isAdd ? 'Save'  : 'Update';
                $submitIntent = $isAdd ? 'Add'   : 'Update';
                ?>
                <input type="hidden" name="submit_vendor" value="<?php echo $submitIntent; ?>" />
                <input type="hidden" name="vendor_user_id" value="<?php echo $isAdd ? '' : $vendordetail['user_id']; ?>" />
                <input type="hidden" name="vendor_id" value="<?php echo $isAdd ? '' : $vendor_id; ?>" />
                <input type="hidden" name="transaction_table" value="<?php echo $transaction_table; ?>" />
                <input type="hidden" name="hid" value="<?php echo $isAdd ? '' : $vendor_id; ?>" />

                <button type="button" class="btn btn-primary m-r-20 m-b-20" onclick="checkVendorValidationEmpty()">
                <i class="ti-check"></i> <?php echo $submitText; ?>
                </button>
                <span class="text-danger terms_conditions" style="display: none">* Please Accept Terms And Conditions In Questions Tab</span>
        </div>
        <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#basic-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Business Information</a>
                <div class="slide"></div>
            </li>
            <?php
                $fieldaccesright = $vafield['company_information'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#company-information" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Company Info</a>
                <div class="slide"></div>
            </li>
            <?php } $fieldaccesright = $vafield['bank_information'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#company-bank-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Banking Info</a>
                <div class="slide"></div>
            </li>
            <?php } $fieldaccesright = $vafield['other_contact_information'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#other-contact-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Other Contact Info</a>
                <div class="slide"></div>
            </li>


            <?php } $fieldaccesright = $vafield['note'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#note" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Note</a>
                <div class="slide"></div>
            </li>
        <?php } ?>

            <?php if($transaction_table == "vendors"){ ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#agreements" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Agreements</a>
                    <div class="slide"></div>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" vendor_items" role="tab" onclick="getVendorCustomerItems()">Items</button>
                    <div class="slide"></div>
                </li>


            <?php } ?>
            <?php  $fieldaccesright = $vafield['other_information'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#vendor-other-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Other Info</a>
                <div class="slide"></div>
            </li>
            <?php } $fieldaccesright = $vafield['questions'];
                  if($fieldaccesright['access'] !="NA"){ ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" id="vendor-questions-id" href="#vendor-questions" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Questions</a>
                <div class="slide"></div>
            </li>
            <?php } ?>

        </ul>
    <div class="tab-content card-block">
        <div class="tab-pane" id="vendor_items" role="tabpanel">

        </div>
        <div class="tab-pane active" id="basic-info" role="tabpanel">
            <div class="row">
                <?php

                    $fieldaccesright = $vafield['business_information'];
                ?>
                <div class="form-group  col-lg-6">
                    <span class="form-label">Company Name <span class="text-danger">*</span></span>
                    <input aria-label="Input field" class="form-control <?php echo $isAdd ? '' : 'custom_readonly'; ?>" type="text" id="company" name="company" onblur="checkCompanyNameAllReadyAvilableOrNot()" value="<?php echo $vendordetail['company']; ?>" autocomplete="off" <?php echo $isAdd ? '' : 'readonly'; ?> required>
                    <span class="text-danger f-12" id="allreadycompanyname"  style="display:none"><span id="companynameerror" class="f-w-600"> </span> Company Name Already Register</span>
                    <div style="display: none;color: red;" id="company_name_err"> Please Enter Company Name </div>
                </div>
                <?php
                   if($fieldaccesright['field']['website_url']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['website_url'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 form-group ">
                    <span class="form-label">Corporate Website</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="website_url" name="website_url" value="<?php echo $vendordetail['website_url']; ?>" autocomplete="off" >
                </div>
                <?php
                   }
                   if($fieldaccesright['field']['company_trading_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['company_trading_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 form-group ">
                    <span class="form-label">DBA(Trading Name)</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="company_trading_name" name="company_trading_name" value="<?php echo $vendordetail['company_trading_name']; ?>" autocomplete="off">
                </div>
                <?php
                   }
                   if($fieldaccesright['field']['company_subsidiary_division']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['company_subsidiary_division'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 form-group ">
                    <span class="form-label">Subsidiary / Division Of</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="company_subsidiary_division" name="company_subsidiary_division" value="<?php echo $vendordetail['company_subsidiary_division']; ?>" autocomplete="off">
                </div>
                <?php
                   }
                   if($fieldaccesright['field']['first_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['first_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-2 " >
                    <span class="form-label">First Name <span class="text-danger">*</span></span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="first_name" name="first_name" value="<?php echo $vendordetail['first_name']; ?>" autocomplete="off" required>
                    </div>
                    <div style="display: none;color: red;" id="first_name_err"> Please Enter First Name </div>
                </div>
                <?php
                   }
                   if($fieldaccesright['field']['middle_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['middle_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-2" >
                    <span class="form-label">Middle Name</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="middle_name" name="middle_name" value="<?php echo $vendordetail['middle_name']; ?>" autocomplete="off">
                    </div>
                    <div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div>
                </div>
                <?php
                   }
                   if($fieldaccesright['field']['last_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['last_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-2">
                    <span class="form-label">Last Name <span class="text-danger">*</span></span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="last_name" name="last_name" value="<?php echo $vendordetail['last_name']; ?>" autocomplete="off" required>
                    </div>
                    <div style="display: none;color: red;" id="last_name_err"> Please Enter Last Name </div>
                </div>
                <?php
                   }
                ?>
                <div class="form-group col-lg-3">
                    <span class="form-label">Email <span class="text-danger">*</span></span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control " id="email" name="email" type="email" onblur="checkalreadyavailableornotcustomerorvendordetails('EMAIL')" value="<?php echo $vendordetail['email']; ?>" autocomplete="off" <?php echo ($vendordetail['email'] =="") ? '' : 'readonly'; ?> required>
                    </div>
                    <span class="text-danger f-14" id="emailerror"  style="display:none"></span>
                    <div style="display: none;color: red;" id="email_err"> Please Enter Email </div>
                </div>
                <?php
                   if($fieldaccesright['field']['phone']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['phone'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-3">
                    <span class="form-label">Phone</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="number" id="phone" name="phone" value="<?php echo $vendordetail['phone']; ?>" autocomplete="off">
                    </div>
                    <div style="display: none;color: red;" id="phone_err"> Please enter phone </div>
                </div>
            <?php } ?>
            <div class="form-group col-lg-3">
                    <span class="form-label">Short Code</span>
                    <div class="kt-input-icon">
                        <input aria-label="Short Name" class="form-control" type="text" id="short_name" name="short_name" value="<?php echo $vendordetail['short_name']; ?>" autocomplete="off">
                    </div>
                    <div style="display: none;color: red;" id="phone_err_2"> Please enter phone </div>
                </div>
            </div>
                        <div id="gst-tab-details">
                            <?php
                            if(count((array)$customer_vendor_gst_billing_details) =='0'){
                                $customer_vendor_gst_billing_details[] = array();
                            }
                            $mkseq = 1;
                            foreach($customer_vendor_gst_billing_details as $cvgst){ ?>
                            <div class="row sub-title" id="removediv_<?php echo $mkseq; ?>">
                                <div class="col-lg-6">
                                    <input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]" value="<?php echo $cvgst['id']; ?>" >
                                    <div class="row">
                                        <div class="col-lg-6 p-r-5 w-full" style="float: left;">
                                            <span class="form-label d-block">GST  Registration type</span>
                                            <select aria-label="Select option" id="gsttype_<?php echo $mkseq; ?>" name="gsttype[]" class="form-select js-example-basic-single">
                                                <option value="">Select Any One</option>
                                                <?php foreach($gstregistrationtype as $gstkey=>$gstregtype) { ?>
                                                    <option <?php echo $cvgst['gsttype']==$gstkey? 'selected':'' ?> value="<?php echo $gstkey;?>"><?php echo $gstregtype; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6" style="float: left;">
                                            <span class="form-label">GST No.</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Gstin[]" class="form-control" type="text" id="gstin" name="gstin[]" onblur="checkalreadyavailableornotcustomerorvendordetails('GSTIN', this)" value="<?php echo $cvgst['gstin']; ?>" >
                                            <span class="text-danger f-14 gstinerror" style="display:none"></span>
                                            </div>

                                            <div style="display: none;color: red;" id="gstin_err"> Please enter GST IN </div>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span class="form-label">Billing Adderss</span>
                                            <div class="kt-input-icon">
                                                <textarea aria-label="Billing Adderss" type="text" class="form-control" placeholder="Billing Adderss" name="billing_address[]" id="billing_address_<?php echo $mkseq; ?>"><?php echo $cvgst['billing_address']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3 w-full" >
                                            <span class="form-label">Country</span>
                                            <select aria-label="Select option" id="billing_country_<?php echo $mkseq; ?>" class="form-select js-example-basic-single" name="billing_country[]"  onchange="selectedcategory(this.value,'billing_state_<?php echo $mkseq; ?>','states','country_id','selectedbillcountry')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($countrieslist as $countryvalue) { ?>
                                                    <option <?php echo $cvgst['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3  w-full" >
                                            <span class="form-label">State</span>
                                            <select aria-label="Select option" id="billing_state_<?php echo $mkseq; ?>" name="billing_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'billing_city_<?php echo $mkseq; ?>','cities','state_id','selectedbillstate')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($states as $states1) { ?>
                                                    <option <?php echo $cvgst['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5  w-full" >
                                            <span class="form-label">City/Town</span>
                                            <select aria-label="Select option" id="billing_city_<?php echo $mkseq; ?>" name="billing_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($cities as $cities1) { ?>
                                                    <option <?php echo $cvgst['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5" >
                                            <span  class="form-label">Pincode</span>
                                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number" id="billing_post_code_<?php echo $mkseq; ?>" name="billing_post_code[]" value="<?php echo $cvgst['billing_post_code']; ?>" >

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5" style="float: right;">
                                            <span class="form-label">Contact Person</span>
                                            <input aria-label="Contact Person" class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person[]" value="<?php echo $cvgst['contact_person']; ?>" >

                                        </div>
                                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                                            <span class="form-label">Mobile</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Mobile[]" class="form-control" type="number" id="mobile" name="mobile[]" value="<?php echo $cvgst['mobile']; ?>" >
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5" style="float: right;">
                                            <span class="form-label">Fax</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Fax[]" class="form-control" type="number" id="fax" name="fax[]" value="<?php echo $cvgst['fax']; ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="float: left;">

                                    <div class="row">
                                        <div class="form-group col-lg-9" >
                                            <span class="form-label d-block"></span>
                                            <div class="border-checkbox-section  ">
                                                  <div class="border-checkbox-group border-checkbox-group-primary m-t-30">
                                                    <input aria-label="Checkbox" class="border-checkbox" type="checkbox" id="checkbox<?php echo $mkseq; ?>" onclick="cloneShipping(this,'<?php echo $mkseq; ?>')" onkeydown="">
                                                    <label class="form-label border-checkbox-label" for="checkbox<?php echo $mkseq; ?>">My shipping address is the same as my billing address</label>
                                                  </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 d-flex flex-column justify-content-between  text-right">
                                            <span data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="cursor-pointer" onclick="removeGSTDiv(<?php echo $mkseq; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <i class="ti-trash text-danger"></i> Remove
                                            </span>
                                            <div class="form-group ">
                                                <input aria-label="Is Default" type="radio" id="test"  name="is_default" value="<?php echo $mkseq-1; ?>"<?php  echo ($cvgst['is_default'] == 'Y') || ($mkseq-1==0) ? 'checked' : '';?> >
                                                <span>Make Default</span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12" >
                                            <span class="form-label">Shipping Adderss</span>
                                            <div class="kt-input-icon">
                                                <textarea aria-label="Street" type="text" class="form-control" placeholder="Street" name="shipping_address[]" id="shipping_address_<?php echo $mkseq; ?>"><?php echo $cvgst['shipping_address']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3  w-full" >
                                            <span class="form-label">Country</span>
                                            <select aria-label="Select option" id="shipping_country_<?php echo $mkseq; ?>" class="form-select js-example-basic-single"  name="shipping_country[]" onchange="selectedcategory(this.value,'shipping_state_<?php echo $mkseq; ?>','states','country_id','selectedshipcountry')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($countrieslist as $countryvalue) { ?>
                                                    <option <?php echo $cvgst['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 w-full">
                                            <span class="form-label">State</span>
                                            <select aria-label="Select option" id="shipping_state_<?php echo $mkseq; ?>" name="shipping_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'shipping_city_<?php echo $mkseq; ?>','cities','state_id','selectedshipstate')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($states as $states1) { ?>
                                                    <option <?php echo $cvgst['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3 p-r-5 w-full">
                                            <span class="form-label">City/Town</span>
                                            <select aria-label="Select option" id="shipping_city_<?php echo $mkseq; ?>" name="shipping_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
                                                <option value="">Select Any One</option>
                                                <?php foreach($cities as $cities1) { ?>
                                                    <option <?php echo $cvgst['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3" >
                                            <span class="form-label">Pincode</span>
                                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number" id="shipping_post_code_<?php echo $mkseq; ?>" name="shipping_post_code[]" value="<?php echo $cvgst['shipping_post_code']; ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $mkseq++;
} ?>
                        </div>
                        <div class="">
                            <button class="btn btn-success  col-lg-4" onclick="addMoreGSTBillingDetails()" type="button">+ Add More GST & Billing Details</button>
                        </div>
        </div>
        <div class="tab-pane" id="company-information" role="tabpanel">
            <div class="row ">
                <?php
                    $fieldaccesright = $vafield['company_information'];
                    if($fieldaccesright['field']['vendor_tax_reg_no']!="NA"){
                        $accessreadonly = $fieldaccesright['field']['vendor_tax_reg_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4" style="margin-bottom: 2rem;">
                    <span class="form-label">Tax/Vat Reg.No.</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="vendor_tax_reg_no" name="vendor_tax_reg_no" value="<?php echo $vendordetail['vendor_tax_reg_no']; ?>" autocomplete="off">
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['business_type']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['business_type'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
                    <span class="form-label">Legal Structure</span>
                    <select aria-label="Business Type" class="form-select js-example-basic-single" name="business_type" >
                        <option value="">Select Any One</option>
                        <?php foreach($company_types as $ctype) { ?>
                            <option <?php echo $vendordetail['business_type']==$ctype['id'] ? 'selected':'' ?> value="<?php echo $ctype['id']; ?>"><?php echo $ctype['company_type_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['duns_no']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['duns_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">DUNS Id</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="duns_no" name="duns_no" value="<?php echo $vendordetail['duns_no']; ?>" autocomplete="off">
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['company_registration_number']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['company_registration_number'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4" style="margin-bottom: 2rem;">
                    <span class="form-label">Company Registration Number</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" name="company_registration_number" value="<?php echo $vendordetail['company_registration_number']; ?>" autocomplete="off">
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['company_registration_country']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['company_registration_country'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
                    <span class="form-label">Company Registration Country</span>
                    <select aria-label="Company Registration Country" class="form-select js-example-basic-single" name="company_registration_country" >
                        <option value="">Select Any One</option>
                        <?php foreach($countrieslist as $countryvalue) { ?>
                            <option <?php echo $vendordetail['company_registration_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['payment_terms']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['payment_terms'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 w-full <?php echo $accessreadonly; ?>">
                    <span class="form-label">Payment Terms</span>
                    <select aria-label="Payment Terms" name="payment_terms" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($payment_terms as $payt) { ?>
                            <option value="<?php echo $payt['id']; ?>" <?php echo $vendordetail['payment_terms']==$payt['id']?'selected':'';?> ><?php echo $payt['payment_term_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['gstin']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['gstin'] !="E"?"custom_readonly":"";
                ?>

                <?php
                       }
                       if($fieldaccesright['field']['pan_no']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['pan_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">PAN No. <span class="text-danger">*</span></span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="pan_no" name="pan_no" onblur="checkalreadyavailableornotcustomerorvendordetails('PAN_NO')" value="<?php echo $vendordetail['pan_no']; ?>" autocomplete="off">
                        <span class="text-danger f-12"><?php echo $vendor_verifications['PAN']['status'] =="Y"?$_SESSION['error_message']['9136']:"PAN verification is pending"; ?></span>
                    </div>
                    <span class="text-danger f-14" id="pannoerror"  style="display:none"></span>
                    <div style="display: none;color: red;" id="pan_no_err"> Please Enter PAN No. </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['tan_number']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['tan_number'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">TAN No. </span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="tan_number" name="tan_number" value="<?php echo $vendordetail['tan_number']; ?>" autocomplete="off">

                    </div>
                </div>
                <?php
                       } ?>
                   <div class="col-lg-4 form-group" >
                    <span class="form-label d-block">Subsidiary Vendor Check</span>
                    <input aria-label="Subsidiary Vendor Check" type="checkbox" class="js-switch-vendor" value="Y" name="subsidiary_vendor_check" id="subsidiary_vendor_check" <?php echo $vendordetail['subsidiary_vendor_check'] =="Y"?"checked":""; ?> >
                </div>

                  <?php
                  if($fieldaccesright['field']['msme_number']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['msme_number'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group <?php echo $accessreadonly; ?>" >
                    <span class="form-label d-block">Udyam Registration/MSME</span>
                    <input aria-label="Msme Register Yes No" type="checkbox" class="js-switch-vendor" value="Y" name="msme_register_yes_no" id="msme_register_yes_no" <?php echo $vendordetail['msme_register_yes_no'] =="Y"?"checked":""; ?> onchange="getmSMENumber()">
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['msme_number']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['msme_number'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" id="msme_number_id" <?php echo $vendordetail['msme_register_yes_no'] =="Y"?"":"style='display:none'"; ?>>
                    <span class="form-label">Udyam Registration/MSME Number <span class="text-danger">*</span></span>
                    <div class="kt-input-icon ">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="msme_number" name="msme_number" value="<?php echo $vendordetail['msme_number']; ?>" autocomplete="off">
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['incoterm_id']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['incoterm_id'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>" >
                    <span class="form-label">ENCO Terms/ Shipping Terms</span>
                    <select aria-label="Incoterm" class="form-select js-example-basic-single" name="incoterm_id" >
                        <option value="">Select Any One</option>
                        <?php foreach($incoterms as $incotermvalue) { ?>
                            <option <?php echo $vendordetail['incoterm_id']==$incotermvalue['id'] ? 'selected':'' ?> value="<?php echo $incotermvalue['id']; ?>"><?php echo $incotermvalue['incoterm_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['image']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['image'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 <?php echo $accessreadonly; ?>">
                    <span class="form-label">Logo</span>
                    <div class="col-lg-9 col-xl-6 ">
                        <div class="profile-pic">
                            <label class="-label" for="display_logo">
                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                            </label>
                            <input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                            <img src="<?php echo "uploads/all/".$vendordetail['image'] ; ?>" id="outputdisplay_logo" width="200" alt="Logo" />
                            <input type="hidden" name="hid_image" value="<?php echo $vendordetail['image'] ;  ?>">
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="tab-pane" id="company-bank-info" role="tabpanel">
            <div class="row ">
                <?php
                    $fieldaccesright = $vafield['bank_information'];
                    if($fieldaccesright['field']['bank_name']!="NA"){
                        $accessreadonly = $fieldaccesright['field']['bank_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Bank name <span class="text-danger">*</span></span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_name" name="bank_name" value="<?php echo $vendordetail['bank_name']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_address']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_address'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-8">
                    <span class="form-label">Bank Address</span>
                    <textarea aria-label="Text area" type="text" class="form-control <?php echo $accessreadonly; ?>" name="bank_address"><?php echo $vendordetail['bank_address']; ?></textarea>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_country']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_country'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>">
                    <span class="form-label">Country</span>
                    <select aria-label="Bank Country" class="form-select js-example-basic-single" name="bank_country" id="bank_country" onchange="selectedcategory(this.value,'bank_state','states','country_id','selectedshipcountry')">
                        <option value="">Select Any One</option>
                        <?php foreach($countrieslist as $countryvalue) { ?>
                            <option <?php echo $vendordetail['bank_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_state']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_state'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-3  w-full <?php echo $accessreadonly; ?>" >
                    <span class="form-label">State</span>
                    <select aria-label="Bank State"  name="bank_state" id="bank_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'bank_city','cities','state_id','selectedbillstate')">
                        <option value="">Select Any One</option>
                        <?php foreach($states as $states1) { ?>
                            <option <?php echo $vendordetail['bank_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_city']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_city'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-3 w-full <?php echo $accessreadonly; ?>" >
                    <span class="form-label">City/Town</span>
                    <select aria-label="Bank City"  name="bank_city"  id="bank_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
                        <option value="">Select Any One</option>
                        <?php foreach($cities as $cities1) { ?>
                            <option <?php echo $vendordetail['bank_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_post_code']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_post_code'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-3" >
                    <span  class="form-label">Pincode</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" placeholder="Pincode" type="text" id="bank_post_code" name="bank_post_code" value="<?php echo $vendordetail['bank_post_code']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_beneficiary_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_beneficiary_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Company Bank Beneficiary Name </span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_beneficiary_name" name="bank_beneficiary_name" value="<?php echo $vendordetail['bank_beneficiary_name']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_account_no']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_account_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Bank Account Number <span class="text-danger">*</span></span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="number" id="bank_account_no" name="bank_account_no" value="<?php echo $vendordetail['bank_account_no']; ?>" >
                    <span class="text-danger f-12"><?php echo $vendor_verifications['BANK']['status'] =="Y"?$_SESSION['error_message']['9140']:"Bank Account verification is pending"; ?></span>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['account_type']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['account_type'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                    <span class="form-label">Type Of Bank Account</span>
                    <select aria-label="Account Type"  name="account_type"  id="account_type" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($bank_acoount_type as $batypekey=>$batypeval) { ?>
                            <option <?php echo $vendordetail['account_type']==$batypekey ? 'selected':'' ?> value="<?php echo $batypekey;?>"><?php echo $batypeval; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['ifsc_code']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['ifsc_code'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">IFSC Code <span class="text-danger">*</span></span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="ifsc_code" name="ifsc_code" value="<?php echo $vendordetail['ifsc_code']; ?>" autocomplete="off">
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['routing_aba_dfi_ach']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['routing_aba_dfi_ach'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Routing-ABA/DFI/ACH</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_aba_dfi_ach" name="routing_aba_dfi_ach" value="<?php echo $vendordetail['routing_aba_dfi_ach']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['routing_wire_Code']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['routing_wire_Code'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Routing Wire Code</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="routing_wire_Code" name="routing_wire_Code" value="<?php echo $vendordetail['routing_wire_Code']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['swift_bic_code']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['swift_bic_code'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">SWIFT/BIC Code</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="swift_bic_code" name="swift_bic_code" value="<?php echo $vendordetail['swift_bic_code']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['iban_code']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['iban_code'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">IBAN</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="iban_code" name="iban_code" value="<?php echo $vendordetail['iban_code']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_query_person_name']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_query_person_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Contact Person Name (Bank related queries)</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_person_name" name="bank_query_person_name" value="<?php echo $vendordetail['bank_query_person_name']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_query_contact_no']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_query_contact_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Contact no</span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="bank_query_contact_no" name="bank_query_contact_no" value="<?php echo $vendordetail['bank_query_contact_no']; ?>" >
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['bank_query_email_id']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['bank_query_email_id'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-4">
                    <span class="form-label">Contact Email </span>
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="email" id="bank_query_email_id" name="bank_query_email_id" value="<?php echo $vendordetail['bank_query_email_id']; ?>" >
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="tab-pane" id="vendor-other-info" role="tabpanel">
            <div class="row">
            <?php
                $fieldaccesright = $vafield['other_information'];
                if($fieldaccesright['field']['vendor_type']!="NA"){
                    $accessreadonly = $fieldaccesright['field']['vendor_type'] !="E"?"custom_readonly":"";
            ?>
            <div class="form-group  col-lg-4 w-full <?php echo $accessreadonly; ?>" >
                <span class="form-label">Vendor Type</span>
                <select aria-label="Vendor Type" name="vendor_type" class="form-select js-example-basic-single">
                    <option value="">Select Any One</option>
                    <?php foreach($vendor_type as $vtypekey=>$vtypeval) { ?>
                        <option <?php echo ($vendordetail['vendor_type']==$vtypekey || $vendordetail['vendor_type'] =="")?'selected':'' ?> value="<?php echo $vtypekey;?>"><?php echo $vtypeval; ?></option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="title_err"> Please enter title </div>
            </div>
            <?php
                   }
                   if($fieldaccesright['field']['title']!="NA"){
                   $accessreadonly = $fieldaccesright['field']['title'] !="E"?"custom_readonly":"";
            ?>
            <div class="col-lg-4 form-group " >
                <span class="form-label">Title</span>
                <div class="kt-input-icon">
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="title" name="title" value="<?php echo $vendordetail['title']; ?>">
                </div>
                <div style="display: none;color: red;" id="title_err_2"> Please enter title </div>
            </div>
            <?php
                   }
                   if($fieldaccesright['field']['suffix']!="NA"){
                   $accessreadonly = $fieldaccesright['field']['suffix'] !="E"?"custom_readonly":"";
            ?>
            <div class="form-group col-lg-4" style="float: left;">
                <span class="form-label">Suffix</span>
                <div class="kt-input-icon">
                    <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="suffix" name="suffix" value="<?php echo $vendordetail['suffix']; ?>" >
                </div>
                <div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div>
            </div>
            <?php
                   }
                   if($fieldaccesright['field']['gsttype']!="NA"){
                   $accessreadonly = $fieldaccesright['field']['gsttype'] !="E"?"custom_readonly":"";
            ?>

            <?php
                   }
                   if($fieldaccesright['field']['parent_vendor']!="NA"){
                   $accessreadonly = $fieldaccesright['field']['parent_vendor'] !="E"?"custom_readonly":"";
            ?>
            <div class="form-group col-lg-4 w-full <?php echo $accessreadonly; ?>">
                <span class="form-label">Parent Vendor</span>
                <select aria-label="Parent Vendor" name="parent_vendor" class="form-select js-example-basic-single">
                    <option value="">Select Any One</option>
                    <?php foreach($vendorslist as $venlist) { ?>
                        <option <?php echo $vendordetail['parent_vendor']==$venlist['id'] ? 'selected':'' ?> value="<?php echo $venlist['id'];?>"><?php echo $venlist['display_name']; ?></option>
                    <?php } ?>
                </select>

                <div style="display: none;color: red;" id="email_err1"> Please enter parent Customer </div>
            </div>
            <?php
                   }
                   if($fieldaccesright['field']['cst_reg_no']!="NA"){
                   $accessreadonly = $fieldaccesright['field']['cst_reg_no'] !="E"?"custom_readonly":"";
            ?>
            <div class="col-lg-4 form-group" >
                    <span class="form-label">CST Reg.No</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="cst_reg_no" name="cst_reg_no" value="<?php echo $vendordetail['cst_reg_no']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['tds_apply']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['tds_apply'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group <?php echo $accessreadonly; ?>" >
                    <span class="form-label">  </span>
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox">
                            <input aria-label="Tds Apply" type="checkbox" name="tds_apply" class="js-switch-vendor" <?php echo $vendordetail['tds_apply'] == "0"?"checked":"";?>> Apply TDS for this vendor
                            <span></span>
                        </label>
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['pref_payment_method']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['pref_payment_method'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 w-full form-group <?php echo $accessreadonly; ?>">
                    <span class="form-label">Preferred Payment Method</span>
                    <select aria-label="Pref Payment Method" name="pref_payment_method" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($payment_method as $key=>$pm) { ?>
                            <option value="<?php echo $key; ?>" <?php echo $vendordetail['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['preferred_deliver_method']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['preferred_deliver_method'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">Preferred Delivery Method</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="preferred_deliver_method" name="preferred_deliver_method" value="<?php echo $vendordetail['preferred_deliver_method']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['opening_balance']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['opening_balance'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4" style="margin-bottom: 2rem;">
                    <span class="form-label">Opening Blance</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="opening_balance" name="opening_balance" value="<?php echo $vendordetail['opening_balance']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['asof']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['asof'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">As of</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control datepicker <?php echo $accessreadonly; ?>" type="text" id="asof" name="asof" value="<?php echo fromDate($vendordetail['asof']); ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['pay_with_me']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['pay_with_me'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">The Vendor Pays Me With</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control <?php echo $accessreadonly; ?>" type="text" id="pay_with_me" name="pay_with_me" value="<?php echo $vendordetail['pay_with_me']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['credit_limit']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['credit_limit'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">Credit Limit</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="credit_limit" name="credit_limit" value="<?php echo $vendordetail['credit_limit']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['default_discount_perc']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['default_discount_perc'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">Default Discount Percentage (%)</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="default_discount_perc" name="default_discount_perc" value="<?php echo $vendordetail['default_discount_perc']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['payment_discount_perc']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['payment_discount_perc'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                    <span class="form-label">Payment Discount Percentage (%)</span>
                    <div class="kt-input-icon">
                        <input aria-label="Input field" class="form-control text-right <?php echo $accessreadonly; ?>" type="text" id="payment_discount_perc" name="payment_discount_perc" value="<?php echo $vendordetail['payment_discount_perc']; ?>" >
                    </div>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['sales_type']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['sales_type'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 w-full form-group <?php echo $accessreadonly; ?>">
                    <span class="form-label">Sales Type</span>
                    <select aria-label="Sales Type" name="sales_type" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($salestype as $key=>$st) { ?>
                            <option value="<?php echo $key; ?>" <?php echo $vendordetail['sales_type']==$key?'selected':'';?> ><?php echo $st; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="tab-pane" id="other-contact-info" role="tabpanel">
            <?php
                $fieldaccesright = $vafield['other_contact_information'];
                if($fieldaccesright['field']['other_contact_information']!="NA"){
                    $accessreadonly = $fieldaccesright['field']['other_contact_information'] !="E"?"custom_readonly":"";
            ?>
            <table class="table table-hover <?php echo $accessreadonly;?>">
                <thead>
                    <tr>
                        <th scope="col">Type</th>
                          <th scope="col">Name</th>
                          <th scope="col">Number</th>
                          <th scope="col">Email Id</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
                    <tr>
                        <td>
                            <?php echo $vcival; ?>
                        </td>
                        <td>
                            <input aria-label="Input field" class="form-control" type="text" name="other_contact_information[<?php echo $vcikey; ?>][name]" value="<?php echo $other_contact_information[$vcikey]['name']; ?>">
                        </td>
                        <td>
                            <input aria-label="Enter number" class="form-control" type="number" name="other_contact_information[<?php echo $vcikey; ?>][number]" value="<?php echo $other_contact_information[$vcikey]['number']; ?>">
                        </td>
                        <td>
                            <input aria-label="Email address" class="form-control" type="email" name="other_contact_information[<?php echo $vcikey; ?>][email_id]" value="<?php echo $other_contact_information[$vcikey]['email_id']; ?>">
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        </div>
        <div class="tab-pane" id="note" role="tabpanel">
            <div class="row ">
                <?php
                    $fieldaccesright = $vafield['note'];
                    if($fieldaccesright['field']['vendor_po_notes']!="NA"){
                        $accessreadonly = $fieldaccesright['field']['vendor_po_notes'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 p-r-5" style="float: left;">
                    <span class="form-label">PO Notes</span>
                    <textarea aria-label="Vendor Po Notes" type="text" name="vendor_po_notes"  class="form-control <?php echo $accessreadonly; ?>" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $vendordetail['vendor_po_notes']; ?></textarea>
                </div>
                <?php
                       }
                       if($fieldaccesright['field']['vendor_other_notes']!="NA"){
                       $accessreadonly = $fieldaccesright['field']['vendor_other_notes'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6" style="float: left;">
                    <span class="form-label">Other Notes</span>
                    <textarea aria-label="Vendor Other Notes" type="text" name="vendor_other_notes"   class="form-control <?php echo $accessreadonly; ?>" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $vendordetail['vendor_other_notes']; ?></textarea>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="tab-pane" id="agreements" role="tabpanel">
            <div class="p-3">
                <h4 class="mb-10 font-weight-bold text-dark">Agreemets</h4>
                    <div class="form-group col-lg-12">
                        <table class="table table-bordered " id="edutable">
                            <tr>
                                <th scope="col" class="" style="width: 2%;">Sl NO</th>
                                <th scope="col" class="" style="width: 25%;">Name</th>
                                <th scope="col" class="thheader" style="width: 20%;">Number</th>
                                <th scope="col" class="" style="width: 20%;"> Sign Date</th>
                                <th scope="col" class="thheader" style="width: 20%;">Expiry Date </th>
                                <th scope="col" class="thheader" style="width: 15%;">Files</th>
                            </tr>
                        <?php $k=0;
                            $aggrements = (array)$aggrements;
                            if ($isAdd) {
                                $aggrements = [[
                                    'id'=>'','agreement_name'=>'','agreement_ref_no'=>'',
                                    'signed_date'=>'','expiry_date'=>'','file_url'=>''
                                ]];
                            } else {
                                if (empty($aggrements)) {
                                    $aggrements[] = ['agreement_name' => ''];
                                }
                            }

                        foreach ($aggrements as $inst) { $k++;?>
                            <tr class="rowcount">
                                <td><?php echo $k; ?>
                                <input type="hidden" name="ag_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>">
                            </td>

                            <td><input aria-label="Ag Type[]" type="text" name="ag_type[]" class="form-control" value="<?php echo $inst['agreement_name']; ?>" autocomplete="off">  </td>
                            <td>
                                <input aria-label="Ag Number[]" type="text" name="ag_number[]" class="form-control" value="<?php echo $inst['agreement_ref_no']; ?>" autocomplete="off">
                            </td>
                            <td><input aria-label="Ag Sin Date[]" type="text" name="ag_sin_date[]" class="form-control datepicker "  id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo fromDate($inst['signed_date']); ?>"  autocomplete="off">  </td>
                            <td><input aria-label="Ag Ex Date[]" type="text" name="ag_ex_date[]" class="form-control datepicker" id="kt_datepicker_3_2<?php echo $k; ?>"  value="<?php echo fromDate($inst['expiry_date']); ?>" autocomplete="off">  </td>
                            <td>
                                <div class="row m-t-15">
                                    <div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;">
                                        <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" >
                                            <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i>
                                            <input aria-label="Agreement File[]" type="file" name="agreement_file[]" />
                                            <input type="hidden" name="hidden_agreement_file[]" value="<?php echo $inst['file_url']; ?>"/>
                                        </label>
                                    </div>
                                    <?php if($inst['file_url']!=''){ ?>
                                        <div class="col-lg-3">
                                            <a href="<?php echo $File_Uploads_Path."/".$inst['file_url']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
                                                <i class="ti-cloud-down f-20 text-warning f-30 f-w-800"></i>
                                            </a></div>
                                        <?php } ?>
                                    </div>
                                </td>

                            </tr>
                        <?php }  ?>
                    </table>
                    <div class="row">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button class="btn btn-success float-left" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="attachments" vendordetail="tabpanel">
            <div class="form-group row">
                <div class="col-lg-4 col-md-9 col-sm-12">
                    <div class="dropzone dropzone-default dropzone-brand dz-clickable" id="kt_dropzone_2">
                        <div class="dropzone-msg dz-message needsclick">
                            <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                            <span class="dropzone-msg-desc">Upload up to 10 files</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="employees" vendordetail="tabpanel">
            <div class="card-body">
                <?php
                if(count((array)$emplist) != 0){
                    $i=1; ?>
                <table class="table table-bordered" id="kt_datatable1">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email Id</th>
                            <th scope="col">Billable Status</th>
                            <th scope="col">Esgin transaction</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($emplist as $emplist){ ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $emplist['display_name']; ?> </td>
                                <td><?php echo  $emplist['email_id']; ?> </td>
                                <td><?php echo $emplist['billable']==0? "Billable":"Non Billable"; ?></td>
                                <td><?php echo $emplist['transaction_number']; ?> </td>
                            </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                <?php }else{ ?>

                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Employees</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
                <!--end: Datatable-->
            </div>
        </div>
        <div class="tab-pane" id="no-hiring" role="tabpanel">
            <h4 class="mb-10 font-weight-bold text-dark">No Hiring clause</h4>
            <div class="form-group row">
                <div class="col-lg-2 "></div>
                <div class="col-lg-4 col-md-6 col-sm-12 row mt-3">
                    <div class="col-7">
                        <span class="switch switch-outline switch-icon switch-info">
                            <label>
                                <input aria-label="Checkbox" type="checkbox" onclick="checkboxclick()" <?php if($vendordetail['hiring_clause']==0){ echo "checked";
} ?> id="hiring_clause" name="hiring_clause" class="js-switch" onkeydown="">
                                <span></span>
                            </label>
                        </span>
                        <span class="col-form-label">No Hiring Clause </span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 coolingmonth">
                    <span class="form-label">Cooling Period of Months</span>
                    <div class="kt-input-icon">
                        <input aria-label="Cooling Months" class="form-control"   type="text" id="cooling_months" name="cooling_months"   value="<?php echo $vendordetail['cooling_months']; ?>">
                    </div>
                </div>
                <div class="col-lg-2 "></div>
            </div>
        </div>
        <div class="tab-pane" id="vendor-questions" role="tabpanel">
            <?php
                $fieldaccesright = $vafield['questions'];
                $accessreadonly = $fieldaccesright['field']['questions'] !="E"?"custom_readonly":"";
            ?>
            <div class="card-body <?php echo $accessreadonly; ?>" >
                <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
                <div class="row mt-2">
                    <h6>A) Previously have you or any of your subsidiaries ever associated with <?php echo ucwords($_SESSION['config']['name']); ?> (USA or any other region)?</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[radio Associate]" type="radio" name="vendor_questions[radio_associate]" value="Y"  onchange="onchangeAssociatedAnswer(this.value)" <?php echo $vendor_questions['radio_associate'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[radio Associate]" type="radio" name="vendor_questions[radio_associate]" value="N" onchange="onchangeAssociatedAnswer(this.value)" <?php echo ($vendor_questions['radio_associate']=="N" || $vendor_questions['radio_associate']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="associated_team form-group mt-2" <?php echo $vendor_questions['radio_associate'] =="Y"?"":"style='display: none'"; ?> >
                    <span class="form-label">Please provide details</span>
                    <textarea aria-label="Vendor Questions[associated Team Detail]" type="text" name="vendor_questions[associated_team_detail]" class="form-control"><?php echo $vendor_questions['associated_team_detail']; ?></textarea>
                </div>
                <div class="row mt-2">
                    <h6>B) Does your company has a quality system ?(If Answer is Yes Then attachs the Quality Certificates)</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[quality System]" type="radio" name="vendor_questions[quality_system]" value="Y"  onchange="onchangeVRQAnswer(this.value)" <?php echo $vendor_questions['quality_system'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[quality System]" type="radio" name="vendor_questions[quality_system]" value="N" onchange="onchangeVRQAnswer(this.value)" <?php echo ($vendor_questions['quality_system']=="N" || $vendor_questions['quality_system']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="vrq_extra mt-2" <?php echo $vendor_questions['quality_system'] =="Y"?"":"style='display: none'"; ?>>
                       <div class="row">
                           <div class="form-group col-lg-1">
                               <span class="form-label">1</span>
                           </div>
                        <div class="form-group col-lg-5">
                            <span class="form-label">Certificate Name</span>
                            <input aria-label="Vendor Questions[quality System Certificate][name][1]" class="form-control" type="text"  name="vendor_questions[quality_system_certificate][name][1]" value="<?php echo $vendor_questions['quality_system_certificate']['name']['1']; ?>">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Effective From Date</span>
                            <input aria-label="Vendor Questions[quality System Certificate][fromdate][1]" class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][fromdate][1]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?>">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Effective To Date</span>
                            <input aria-label="Vendor Questions[quality System Certificate][todate][1]" class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][todate][1]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?>">
                        </div>
                       </div>
                       <div class="row">
                           <div class="form-group col-lg-1">
                               <span class="form-label">2</span>
                           </div>
                        <div class="form-group col-lg-5">
                            <span class="form-label">Certificate Name</span>
                            <input aria-label="Vendor Questions[quality System Certificate][name][2]" class="form-control" type="text"  name="vendor_questions[quality_system_certificate][name][2]" value="<?php echo $vendor_questions['quality_system_certificate']['name']['2']; ?>">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Effective From Date</span>
                            <input aria-label="Vendor Questions[quality System Certificate][fromdate][2]" class="form-control datepicker" type="text" name="vendor_questions[quality_system_certificate][fromdate][2]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?>">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Effective To Date</span>
                            <input aria-label="Vendor Questions[quality System Certificate][todate][2]" class="form-control datepicker" type="text"  name="vendor_questions[quality_system_certificate][todate][2]" value="<?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?>">
                        </div>
                       </div>
               </div>

                   <div class="f-15 f-w-600 text-info mb-2 mt-2" style="clear: both;">Small Business Registerd</div>
                <div class="row mt-2">
                    <div class="col-lg-10">

                        <?php $smallbussinesary = array('1'=>'Self Certified','2'=>'Sam Govt Registered','3'=>'Small Disadvantage','4'=>'Women Owned','5'=>'Veteran Owned','6'=>'Service - Disabled Veteran','7'=>'Small Business');
                                foreach($smallbussinesary as $keybuss=>$smallbus){
                            ?>
                            <div class="checkbox-fade fade-in-primary">
                                <label class="form-label">
                                    <input aria-label="Checkbox" type="checkbox" value="Y" name="vendor_questions[small_business_registerd][<?php echo $keybuss;?>]" <?php echo $vendor_questions['small_business_registerd'][$keybuss] =="Y"?"checked":""; ?>>
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
                            <input aria-label="Vendor Questions[small Business Registerd][100]" class="form-control" type="text"  name="vendor_questions[small_business_registerd][100]" value="<?php echo $vendor_questions['small_business_registerd']['100']; ?>">
                        </div>
                    </div>

                   </div>
                   <div class="row">
                       <div class="col-lg-6">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Unique Entity ID</span>
                                <input aria-label="Vendor Questions[small Business Registerd][101]" class="form-control" type="text"  name="vendor_questions[small_business_registerd][101]" value="<?php echo $vendor_questions['small_business_registerd']['101']; ?>">
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Cage Code</span>
                                <input aria-label="Vendor Questions[small Business Registerd][102]" class="form-control" type="text"  name="vendor_questions[small_business_registerd][102]" value="<?php echo $vendor_questions['small_business_registerd']['102']; ?>">
                            </div>
                        </div>
                    </div>
                   </div>
                   <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Supply Chain Responsibility</div>
                <div class="row mt-2">
                    <h6>1) Is Your Company a Member of RBA ( Responsible Business Alliance)</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][1]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][1]" <?php echo $vendor_questions['supply_Chain_responsibility']['1'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][1]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][1]" <?php echo ($vendor_questions['supply_Chain_responsibility']['1']=="N" || $vendor_questions['supply_Chain_responsibility']['1']=="")?"checked":""; ?>>
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
                                <input aria-label="Vendor Questions[supply Chain Responsibility][2]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][2]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][2]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][2]" <?php echo ($vendor_questions['supply_Chain_responsibility']['2']=="N" || $vendor_questions['supply_Chain_responsibility']['2']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="row mt-2">
                    <h6>3) Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][3]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][3]" <?php echo $vendor_questions['supply_Chain_responsibility']['2'] =="Y"?"checked":""; ?>   onchange="onchangeSupplyChainResponsibilityAnswer(this.value,3)" >
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][3]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][3]" <?php echo ($vendor_questions['supply_Chain_responsibility']['3']=="N" || $vendor_questions['supply_Chain_responsibility']['3']=="")?"checked":""; ?> onchange="onchangeSupplyChainResponsibilityAnswer(this.value,3)">
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="row supplychainresponsibility_4 mt-2">
                    <h6>4) Do you have hourly employees that works at manufacturer facility? For the purpose of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increased or decreased in compensation based on volume of product if Yes (Please answer a, b and c, if No then skip to Question no 5)</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][4]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4]" onchange="onchangeWorkManufracturerFacilityAnswer(this.value)" <?php echo $vendor_questions['supply_Chain_responsibility']['4'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][4]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4]" onchange="onchangeWorkManufracturerFacilityAnswer(this.value)" <?php echo ($vendor_questions['supply_Chain_responsibility']['4']=="N" || $vendor_questions['supply_Chain_responsibility']['4']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="m-l-20 work_manufacturer_facility supplychainresponsibility_4 mt-2" <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"style='display: none'"; ?>>
                       <div class="row">
                        <h6>a) Do all hourly workers work less than 60 hours per week?</h6>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 A]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_a'] =="Y"?"checked":""; ?>>
                                    <i class="helper"></i>Yes
                                  </label>
                            </div>
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 A]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_a']=="N" || $vendor_questions['supply_Chain_responsibility']['4_a']=="")?"checked":""; ?>>
                                    <i class="helper"></i>No
                                  </label>
                            </div>
                        </div>
                       </div>
                       <div class="row mt-2">
                        <h6>b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor?</h6>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 B]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_b'] =="Y"?"checked":""; ?>>
                                    <i class="helper"></i>Yes
                                  </label>
                            </div>
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 B]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_b]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_b']=="N" || $vendor_questions['supply_Chain_responsibility']['4_b']=="")?"checked":""; ?>>
                                    <i class="helper"></i>No
                                  </label>
                            </div>
                        </div>
                       </div>
                       <div class="row mt-2">
                        <h6>c) Do you expect your supplier to have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor</h6>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 C]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo $vendor_questions['supply_Chain_responsibility']['4_c'] =="Y"?"checked":""; ?>>
                                    <i class="helper"></i>Yes
                                  </label>
                            </div>
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][4 C]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][4_c]" <?php echo ($vendor_questions['supply_Chain_responsibility']['4_c']=="N" || $vendor_questions['supply_Chain_responsibility']['4_c']=="")?"checked":""; ?>>
                                    <i class="helper"></i>No
                                  </label>
                            </div>
                        </div>
                       </div>
                   </div>
                   <div class="row supplychainresponsibility_5 mt-2" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="")?"":"style='display: none'"; ?>>
                    <h6>5) Do You retain or hold your employees original passport or other original personal identification documents?</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][5]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][5]" onchange="onchangeHoldOriginalPassportAnswer(this.value)" <?php echo $vendor_questions['supply_Chain_responsibility']['5'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][5]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][5]" checked="checked" onchange="onchangeHoldOriginalPassportAnswer(this.value)" <?php echo ($vendor_questions['supply_Chain_responsibility']['5']=="N" || $vendor_questions['supply_Chain_responsibility']['5']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="m-l-20 hold_original_passport supplychainresponsibility_5 mt-2" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="" && $vendor_questions['supply_Chain_responsibility'][5] =="Y")?"":"style='display: none'"; ?> >
                       <div class="row">
                        <h6>a) Do you return original passport or other original personal identification documents upon the employee's request?</h6>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][5 A]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo $vendor_questions['supply_Chain_responsibility']['5_a'] =="Y"?"checked":""; ?>>
                                    <i class="helper"></i>Yes
                                  </label>
                            </div>
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Vendor Questions[supply Chain Responsibility][5 A]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][5_a]" <?php echo ($vendor_questions['supply_Chain_responsibility']['5_a']=="N" || $vendor_questions['supply_Chain_responsibility']['5_a']=="")?"checked":""; ?>>
                                    <i class="helper"></i>No
                                  </label>
                            </div>
                        </div>
                       </div>
                </div>
                <div class="row supplychainresponsibility_6 mt-2" >
                    <h6>6) Do any employee have to reimburse recruitment fee to your company?</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][6]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][6]" <?php echo $vendor_questions['supply_Chain_responsibility']['6'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][6]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][6]" <?php echo ($vendor_questions['supply_Chain_responsibility']['6']=="N" || $vendor_questions['supply_Chain_responsibility']['6']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="row supplychainresponsibility_7 mt-2" <?php echo $vendor_questions['supply_Chain_responsibility'][3] =="N"?"":"style='display: none'"; ?>>
                    <h6>7) Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)</h6>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][7]" type="radio" value="Y" name="vendor_questions[supply_Chain_responsibility][7]" <?php echo $vendor_questions['supply_Chain_responsibility']['7'] =="Y"?"checked":""; ?>>
                                <i class="helper"></i>Yes
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Vendor Questions[supply Chain Responsibility][7]" type="radio" value="N" name="vendor_questions[supply_Chain_responsibility][7]" <?php echo ($vendor_questions['supply_Chain_responsibility']['7']=="N" || $vendor_questions['supply_Chain_responsibility']['7']=="")?"checked":""; ?>>
                                <i class="helper"></i>No
                              </label>
                        </div>
                    </div>
                   </div>
                   <div class="f-15 f-w-600 text-info mb-2 text-center" style="clear: both;">Terms & Conditions</div>
                <span class="d-block">1. Providing information in the "Vendor Information Form" does not constitute acceptance as an approved & registered vendor. The registration of vendor is the sole discretion of AM/NS India.</span>
                <span class="d-block mt-2">2. Failure to enter all of the mandatory information will not enable us to post your vendor file to our database..</span>
                <span class="d-block mt-2">3. Being a registered vendor does NOT guarantee the receipt of notification for all applicable solicitations or invitation to offer or the award of any purchase contract or award of any invitation for bid or request for proposal or request for qualifications.</span>
                <span class="d-block mt-2">4. Vendor is responsible for the content of the information and its vendors responsibility to keep all information current and provide any subsequent up dates, additional information as and when required and when desired by AM/NS India.</span>
                <span class="d-block mt-2">5. AM/NS India reserves the rights to evaluate the vendor on periodical basis and decide the continuation / discontinuation of business with them based on the evaluation report.</span>
                <span class="d-block mt-2">6. Representative of AM/NS India OR their nominated agency has the right to visit vendors premises for the verification of information with a prior intimation to them.</span>
                <span class="d-block mt-2">7. At later date if it is found that the information furnished is untrue, inaccurate, not current, or is incomplete, we reserve the right to suspend or terminate the registration and recover the dues (if any) from you</span>

                <div class="border-checkbox-section mt-4" >
                    <div class="border-checkbox-group border-checkbox-group-primary">
                        <input aria-label="Checkbox" class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['1'] =="Y"?"checked":""; ?> id="termsandcondition_1" value="Y" name="vendor_questions[terms_conditions][1]">
                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_1">
                            <span class="d-block">I / We have read the terms & conditions and privacy policy.</span>
                            <span class="d-block">I/We hereby declare my / our acceptance for the same and certify that the information submitted in this vendor registration application is true and correct to the best of my/our knowledge.</span>
                            <span class="d-block">I / We further understand that any false statements observed at the later stage may result in denial or revocation of our vendor registration with AM/NS India group.</span></label>
                    </div>
                </div>

                <div class="border-checkbox-section mt-2" >
                    <div class="border-checkbox-group border-checkbox-group-primary">
                        <input aria-label="Checkbox" class="border-checkbox" type="checkbox" <?php echo $vendor_questions['terms_conditions']['2'] =="Y"?"checked":""; ?> id="termsandcondition_2" value="Y" name="vendor_questions[terms_conditions][2]">
                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_2">
                            <span class="d-block">I / We agree, covenant and undertake that I / We have reviewed the policies of AMNSIL.</span>
                        <span class="d-block">I / We will comply with the principles contained in these Policies and these Policies will continue to be applicable and binding on the me/us, to the extent relevant.</span></label>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <button type="button" class="btn btn-primary m-r-20 m-b-20" onclick="checkVendorValidationEmpty()">
                <i class="ti-check"></i> <?php echo $submitText; ?>
                </button>
    <span class="text-danger terms_conditions" style="display: none">* Please Accept Terms And Conditions In Questions Tab</span>

    </form>
</div>

<?php }else{ ?>

<div class="row">
    <?php
        $fieldaccesright = $vafield['business_information'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Business Information</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php
                            if($fieldaccesright['field']['company']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Company Name</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['company']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['website_url']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Corporate Website</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['website_url']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['company_trading_name']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">DBA(Trading Name)</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['company_trading_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['company_subsidiary_division']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Subsidiary / Division Of</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['company_subsidiary_division']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['middle_name']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Name</th>
                            <td class="p-2"><?php echo $vendordetail['first_name'].$vendordetail['middle_name']." ".$vendordetail['last_name']; ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['phone']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Contact</th>
                            <td class="p-2"><?php echo $vendordetail['phone']; ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['email']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Email</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['email']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['phone']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Phone</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['phone']); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        $fieldaccesright = $vafield['company_information'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Company Information</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php

                            if($fieldaccesright['field']['vendor_tax_reg_no']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Vendor Tax Reg. No.</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_tax_reg_no']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['business_type']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Legal Structure</th>
                            <td class="p-2"><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['duns_no']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">DUNS No.</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['duns_no']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['company_registration_number']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Company Registration Number</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['company_registration_number']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['company_registration_country']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Company Registration Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['company_registration_country']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['payment_terms']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Payment Terms</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($payment_terms[$vendordetail['payment_terms']]['payment_term_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['gstin']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">GSTN No.</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['gstin']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['pan_no']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">PAN No</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['pan_no']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['tan_number']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">TAN No</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['tan_number']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['incoterm_id']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Incoterm </th>
                            <td class="p-2 "><?php echo emptytextdashShow($incoterms[$vendordetail['incoterm_id']]['incoterm_name']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        $fieldaccesright = $vafield['business_information'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Billing Adderss</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php
                            if($fieldaccesright['field']['billing_address']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Billing Adderss</th>
                            <td class="address-basic p-2 w-b-w-s"><?php echo $vendordetail['billing_address'].",".$citieslist[$vendordetail['billing_city']]; ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['billing_country']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['billing_country']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['billing_state']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['billing_city']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">City</th>
                            <td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['billing_post_code']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Pincode</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['billing_post_code']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['contact_person']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['contact_person']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['mobile']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Mobile</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['mobile']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        $fieldaccesright = $vafield['business_information'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Shipping Details</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php
                            if($fieldaccesright['field']['shipping_address']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Shipping  Adderss</th>
                            <td class="address-basic p-2 w-b-w-s"><?php if($vendordetail['shipping_address']!=""){ echo trim($vendordetail['shipping_address']).",".$citieslist[$vendordetail['shipping_city']];
}else{ echo "-";
} ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['shipping_country']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['shipping_country']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['shipping_state']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['shipping_state']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['shipping_city']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">City</th>
                            <td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['shipping_city']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['shipping_post_code']!="NA"){
                        ?>

                        <tr>
                            <th scope="row" class="p-2">Pincode</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['shipping_post_code']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        $fieldaccesright = $vafield['bank_information'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Banking Informations</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php
                            if($fieldaccesright['field']['bank_name']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Bank name</th>
                            <td class="address-basic p-2"><?php echo emptytextdashShow($vendordetail['bank_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_address']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Bank Address</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_address']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_country']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['bank_country']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_state']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['bank_state']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_city']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">City</th>
                            <td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['bank_city']]); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_post_code']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Pincode</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['bank_post_code']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_beneficiary_name']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Company Bank Beneficiary Name</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_beneficiary_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_account_no']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Bank Account Number</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_account_no']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['account_type']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Type Of Bank Account</th>
                            <td class="p-2 w-b-w-s"><?php echo $bank_acoount_type[$vendordetail['account_type']]; ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['routing_aba_dfi_ach']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Routing-ABA/DFI/ACH</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['routing_aba_dfi_ach']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['routing_wire_Code']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Routing Wire Code</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['routing_wire_Code']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['swift_bic_code']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">SWIFT/BIC Code</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['swift_bic_code']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['iban_code']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">IBAN</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['iban_code']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_query_person_name']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Name (Bank related queries)</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_person_name']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_query_contact_no']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact no</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_contact_no']); ?></td>
                        </tr>
                        <?php
                               }
                               if($fieldaccesright['field']['bank_query_email_id']!="NA"){
                        ?>
                        <tr>
                            <th scope="row" class="p-2">Contact Email</th>
                            <td class="p-2 w-b-w-s"><?php echo emptytextdashShow($vendordetail['bank_query_email_id']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        $fieldaccesright = $vafield['other_contact_information'];
        if($fieldaccesright['field']['other_contact_information']!="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Other Contact Info</h5></div>
                <div class="card-body">
                    <table class="table table-hover m-b-0" >
                        <tr>
                            <th scope="col" style="text-align: left;" >Type</th>
                            <th scope="col" style="text-align: left;" >Name</th>
                            <th scope="col" style="text-align: left;" >Number</th>
                            <th scope="col" style="text-align: left;" >Email ID</th>
                        </tr>
                        <?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
                        <tr>
                            <td >
                                <?php echo $vcival; ?>
                            </td>
                            <td >
                                <?php echo emptytextdashShow($other_contact_information[$vcikey]['name']); ?>
                            </td>
                            <td >
                                <?php echo emptytextdashShow($other_contact_information[$vcikey]['number']); ?>
                            </td>
                            <td >
                                <?php echo emptytextdashShow($other_contact_information[$vcikey]['email_id']); ?>
                            </td>
                        </tr>
                        <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php $fieldaccesright = $vafield['note'];
        if($fieldaccesright['access'] !="NA"){
    ?>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Notes</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <?php if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
                        <tr>
                            <th scope="row" class="p-2">PO Notes</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_po_notes']); ?></td>
                        </tr>
                        <?php }
if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
                        <tr>
                            <th scope="row" class="p-2">Other Notes</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_other_notes']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if($transaction_table == "vendors"){ ?>
    <div class="col-lg-12 col-xl-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <div class="card-header"><h5 class="text-primary">Agreements</h5></div>
                    <div class="card-body">
                        <?php if(count((array)$aggrements)>0){  ?>
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Number</th>
                                        <th scope="col">Sign Date</th>
                                        <th scope="col">Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $k=0;
                                    foreach ($aggrements as $inst) { $k++;?>
                                        <tr>
                                            <td><?php echo $inst['agreement_name']; ?></td>
                                            <td><?php echo $inst['agreement_ref_no']; ?></td>
                                            <td><?php echo $inst['signed_date']; ?></td>
                                            <td><span class="label label-success"><?php echo $inst['expiry_date']; ?></span></td>
                                        </tr>
                                    <?php }  ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Aggrements</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>


<?php $fieldaccesright = $vafield['questions'];
      if($fieldaccesright['field']['questions']!="NA"){ ?>
<div class="col-lg-12 col-xl-12">
    <div class="card">
        <div class="card-header">
            <h5 class="text-primary">Questions</h5>
        </div>
        <div class="card-body">
                    <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
                    <div class="row">
                        <h6>A) Previously have you or any of your subsidiaries ever associated with <?php echo ucwords($_SESSION['config']['name']); ?> (USA or any other region)?</h6>
                        <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['radio_associate']); ?></span>
                       </div>
                       <div class="associated_team form-group mt-3" <?php echo $vendor_questions['radio_associate'] =="Y"?"":"style='display: none'"; ?> >
                         <h6>a) Please provide details </h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo $vendor_questions['associated_team_detail']; ?></span>
                    </div>
                    <div class="row mt-3">
                        <h6>B) Does your company has a quality system ?(If Answer is Yes Then attachs the Quality Certificates)</h6>
                        <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['quality_system']); ?></span>
                       </div>
                       <div class="vrq_extra" <?php echo $vendor_questions['quality_system'] =="Y"?"":"style='display: none'"; ?>>
                           <div class="row mt-3">
                               <div class="form-group col-lg-1">
                                   <span class="form-label">1</span>
                               </div>
                            <div class="form-group col-lg-5">
                                <span class="form-label d-block">Certificate Name</span>
                                <span><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['1']); ?></span>
                            </div>
                            <div class="form-group col-lg-3">
                                <span class="form-label d-block">Effective From Date</span>
                                <span><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?></span>
                            </div>
                            <div class="form-group col-lg-3 ">
                                <span class="form-label d-block">Effective To Date</span>
                                <span><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?></span>
                            </div>
                           </div>
                           <div class="row mt-2">
                               <div class="form-group col-lg-1">
                                   <span class="form-label">2</span>
                               </div>
                            <div class="form-group col-lg-5">
                                <span><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['2']); ?></span>
                            </div>
                            <div class="form-group col-lg-3">
                                <span><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?></span>
                            </div>
                            <div class="form-group col-lg-3">
                                <span><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?></span>
                            </div>
                           </div>
                   </div>

                       <div class="f-15 f-w-600 text-info mb-2 mt-3" style="clear: both;">Small Business Registerd</div>
                    <div class="row mt-3">
                        <div class="col-lg-12" style="pointer-events: none;">
                            <?php $smallbussinesary = array('1'=>'Self Certified','2'=>'Sam Govt Registered','3'=>'Small Disadvantage','4'=>'Women Owned','5'=>'Veteran Owned','6'=>'Service - Disabled Veteran','7'=>'Small Business');
                                foreach($smallbussinesary as $keybuss=>$smallbus){
                            ?>
                            <div class="checkbox-fade fade-in-primary">
                                <label class="form-label">
                                    <input aria-label="Checkbox" type="checkbox" value="Y" name="vendor_questions[small_business_registerd][<?php echo $keybuss; ?>]" <?php echo $vendor_questions['small_business_registerd'][$keybuss] =="Y"?"checked":""; ?>>
                                    <span class="cr">
                                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                    <span><?php echo $smallbus; ?></span>
                               </label>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                       <div class="row mt-3">
                        <div class="form-group col-lg-6">
                            <span class="form-label d-block">Others</span>
                            <span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['100']); ?></span>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label d-block">Unique Entity ID</span>
                            <span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['101']); ?></span>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label d-block">Cage Code</span>
                            <span><?php echo emptytextdashShow($vendor_questions['small_business_registerd']['102']); ?></span>
                        </div>
                      </div>
                       <div class="f-15 f-w-600 text-info mb-2 mt-3" style="clear: both;">Supply Chain Responsibility</div>
                    <div class="row">
                        <h6>1) Is Your Company a Member of RBA ( Responsible Business Alliance)</h6>
                        <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['1']); ?></span>
                       </div>
                       <div class="row mt-3">
                        <h6>2) Are all employees of your company atleast 18 years of age</h6>
                        <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['2']); ?></span>

                       </div>
                       <div class="row mt-3">
                        <h6>3) Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4</h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['3']); ?></span>
                       </div>
                       <div class="row supplychainresponsibility_4 mt-3">
                        <h6>4) Do you have hourly employees that works at manufacturer facility? For the purpose of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increased or decreased in compensation based on volume of product if Yes (Please answer a, b and c, if No then skip to Question no 5)</h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4']); ?></span>
                       </div>
                       <div class="m-l-20 work_manufacturer_facility supplychainresponsibility_4 mt-3" <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"style='display: none'"; ?>>
                           <div class="row mt-3">
                            <h6>a) Do all hourly workers work less than 60 hours per week?</h6>
                            <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']); ?></span>
                           </div>
                           <div class="row mt-3">
                            <h6>b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor?</h6>
                             <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_b']); ?></span>
                           </div>
                           <div class="row mt-3">
                            <h6>c) Do you expect your supplier to have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor</h6>
                            <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_c']); ?></span>
                           </div>
                       </div>
                       <div class="row supplychainresponsibility_5 mt-3" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="")?"":"style='display: none'"; ?>>
                        <h6>5) Do You retain or hold your employees original passport or other original personal identification documents?</h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5']); ?></span>
                       </div>
                       <div class="m-l-20 hold_original_passport supplychainresponsibility_5 mt-3" <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="" && $vendor_questions['supply_Chain_responsibility'][5] =="Y")?"":"style='display: none'"; ?>>
                           <div class="row">
                            <h6>a) Do you return original passport or other original personal identification documents upon the employee's request?</h6>
                             <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['5_a']); ?></span>
                           </div>
                    </div>
                    <div class="row supplychainresponsibility_6 mt-3" >
                        <h6>6) Do any employee have to reimburse recruitment fee to your company?</h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['6']); ?></span>
                       </div>
                       <div class="row supplychainresponsibility_7 mt-3" <?php echo $vendor_questions['supply_Chain_responsibility'][3] =="N"?"":"style='display: none'"; ?>>
                        <h6>7) Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)</h6>
                         <span> <span class="f-w-700">Answer :</span> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['7']); ?></span>
                       </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php } ?>

<?php if (($_POST['ajaxcall'] ?? '') !== 'Y') { ?>
<script>
window.addEventListener('load', function () {

  // Select2
  if ($.fn.select2) {
    $('.js-example-basic-single').select2();
  }

  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: (window.date_format || "mm/dd/yy"),
      changeMonth: true,
      changeYear: true
    });
  }

  if (window.Switchery) {
    document.querySelectorAll('.js-switch-vendor').forEach(function (el) {
      if (!el._switchery) el._switchery = new Switchery(el, { color: '#4099ff', jackColor: '#fff' });
    });
  }
});
</script>

<?php } ?>

<script>
    var mkseq = <?php echo count((array)$customer_vendor_gst_billing_details) ?? 1;?>;
    function addMoreGSTBillingDetails(){
        mkseq++;
        console.log(mkseq);
        var html =`<div class="row sub-title" id="removediv_`+mkseq+`">
                <div class="col-lg-6">
                    <div class="row"><input class="form-control" type="hidden" name="customer_vendor_gst_billing_details_id[]" value="" >
                        <div class="col-lg-6 p-r-5 w-full" style="float: left;">
                            <span class="form-label d-block">GST  Registration type</span>
                            <select aria-label="Gsttype[]"  name="gsttype[]" class="form-select js-example-basic-single">
                                <option value="">Select Any One</option>
                                <?php foreach($gstregistrationtype as $gstkey=>$gstregtype) { ?>
                                    <option <?php echo $customers['gsttype']==$gstkey? 'selected':'' ?> value="<?php echo $gstkey;?>"><?php echo $gstregtype; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6" style="float: left;">
                            <span class="form-label">GSTIN</span>
                            <div class="kt-input-icon">
                                <input aria-label="Gstin[]" class="form-control" type="text" name="gstin[]" onblur="checkalreadyavailableornotcustomerorvendordetails('GSTIN', this)" value="" >
                                <span class="text-danger f-14 gstinerror" style="display:none"></span>
                            </div>

                        </div>
                        <div class="form-group col-lg-12">
                            <span class="form-label">Billing Adderss</span>
                            <div class="kt-input-icon">
                                <textarea aria-label="Billing Adderss" type="text" class="form-control" placeholder="Billing Adderss" name="billing_address[]" id="billing_address_`+mkseq+`"></textarea>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 w-full" >
                            <span class="form-label">Country</span>
                            <select aria-label="Billing Country[]" id="billing_country_`+mkseq+`" class="form-select js-example-basic-single" name="billing_country[]"  onchange="selectedcategory(this.value,'billing_state_`+mkseq+`','states','country_id','selectedbillcountry')">
                                <option value="">Select Any One</option>
                                <?php foreach($countrieslist as $countryvalue) { ?>
                                    <option value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3  w-full" >
                            <span class="form-label">State</span>
                            <select aria-label="Billing State[]" id="billing_state_`+mkseq+`" name="billing_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'billing_city_`+mkseq+`','cities','state_id','selectedbillstate')">
                                <option value="">Select Any One</option>
                            </select>

                        </div>
                        <div class="form-group col-lg-3 p-r-5  w-full" >
                            <span class="form-label">City/Town</span>
                            <select aria-label="Billing City[]" id="billing_city_`+mkseq+`" name="billing_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
                                <option value="">Select Any One</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" >
                            <span  class="form-label">Pincode</span>
                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number" id="billing_post_code_`+mkseq+`" name="billing_post_code[]" value="" >

                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: right;">
                            <span class="form-label">Contact Person</span>
                            <input aria-label="Contact Person" class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person[]" value="" >

                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Mobile</span>
                            <div class="kt-input-icon">
                                <input aria-label="Mobile[]" class="form-control" type="number" id="mobile_2" name="mobile[]" value="" >
                            </div>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: right;">
                            <span class="form-label">Fax</span>
                            <div class="kt-input-icon">
                                <input aria-label="Fax[]" class="form-control" type="number" id="fax_2" name="fax[]" value="" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="float: left;">
                    <div class="row">
                        <div class="form-group col-lg-9" >
                            <span class="form-label d-block"></span>
                            <div class="border-checkbox-section  ">
                                <div class="border-checkbox-group border-checkbox-group-primary m-t-30">
                                    <input class="border-checkbox" type="checkbox" id="checkbox`+mkseq+`" onclick="cloneShipping(this,`+mkseq+`)" onkeydown="">
                                    <label class="form-label border-checkbox-label" for="checkbox`+mkseq+`">My shipping address is the same as my billing address</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 d-flex flex-column justify-content-between text-right">
                            <button type="button" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Cliek To Remove" class="btn p-0 border-0 bg-transparent cursor-pointer" onclick="removeGSTDiv(`+mkseq+`)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <i class="ti-trash text-danger"></i> Remove
                            </button>
                            <div class="form-group ">
                                <input aria-label="Is Default" type="radio"  name="is_default" value="`+(mkseq-1)+`" >
                                <span>Make Default</span>
                            </div>
                        </div>
                        <div class="form-group col-lg-12" >
                            <span class="form-label">Shipping Adderss</span>
                            <div class="kt-input-icon">
                                <textarea aria-label="Street" type="text" class="form-control" placeholder="Street" name="shipping_address[]" id="shipping_address_`+mkseq+`"></textarea>
                            </div>
                        </div>
                        <div class="form-group col-lg-3  w-full" >
                            <span class="form-label">Country</span>
                            <select aria-label="Shipping Country[]" id="shipping_country_`+mkseq+`" class="form-select js-example-basic-single"  name="shipping_country[]" onchange="selectedcategory(this.value,'shipping_state_`+mkseq+`','states','country_id','selectedshipcountry')">
                                <option value="">Select Any One</option>
                                <?php foreach($countrieslist as $countryvalue) { ?>
                                    <option  value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3 w-full">
                            <span class="form-label">State</span>
                            <select aria-label="Shipping State[]" id="shipping_state_`+mkseq+`" name="shipping_state[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'shipping_city_`+mkseq+`','cities','state_id','selectedshipstate')">
                                <option value="">Select Any One</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3 p-r-5 w-full">
                            <span class="form-label">City/Town</span>
                            <select aria-label="Shipping City[]" id="shipping_city_`+mkseq+`" name="shipping_city[]" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
                                <option value="">Select Any One</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3" >
                            <span class="form-label">Pincode</span>
                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="number" id="shipping_post_code_`+mkseq+`" name="shipping_post_code[]" value="" >
                        </div>
                    </div>
                </div>
            </div>`;
            $("#gst-tab-details").append(html);
            $(".js-example-basic-single").select2();

    }
    function removeGSTDiv(seq) {
        var block = $("#removediv_" + seq);
        var radio = block.find("input[name='is_default']");
        if (radio.is(":checked")) {
            var others = $("input[name='is_default']").not(radio);
            if (others.length === 0) {
                tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "At least one GST & Billing detail must be default. Cannot delete this.", '');
                return false;
            }
            if (!others.is(":checked")) {
                $(others[0]).prop("checked", true);
            }
        }
        block.remove();
        // ✅ Renumber remaining radios sequentially
        $("div[id^='removediv_']").each(function(index) {
            var newIndex = index + 0; // start from 1
            $(this).find("input[name='is_default']").val(newIndex);
        });
        // ✅ Ensure at least one remains default
        var checked = $("input[name='is_default']:checked");
        if (checked.length === 0 && $("input[name='is_default']").length > 0) {
            $("input[name='is_default']").first().prop("checked", true);
        }

        var rid   = block.find("input[name='customer_vendor_gst_billing_details_id[]']").val();
        if (rid && rid !== "") {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: {
                    page: "softdeletedrecord",
                    id: rid,
                    table: "customer_vendor_gst_billing_details"
                },
                success: function (data) {
                    console.log("Soft delete response:", data);
                },
                error: function () {
                    console.error("Soft delete failed");
                }
            });
        }
    }

    function cloneShipping(ei,seq){
        if(ei.checked){
            // Country
            var billing_country_val = $("#billing_country_"+seq).val();
            var billing_country_text = $("#billing_country_"+seq+" option:selected").text();
            var newOption1 = new Option(billing_country_text, billing_country_val, true, true);
            $("#shipping_country_"+seq).append(newOption1);

            // Wait until states are loaded
                var billing_state_val = $("#billing_state_"+seq).val();
                var billing_state_text = $("#billing_state_"+seq+" option:selected").text();
                var newOption2 = new Option(billing_state_text, billing_state_val, true, true);
                $("#shipping_state_"+seq).append(newOption2);

            // Wait until cities are loaded
                var billing_city_val = $("#billing_city_"+seq).val();
                var billing_city_text = $("#billing_city_"+seq+" option:selected").text();
                var newOption3 = new Option(billing_city_text, billing_city_val, true, true);
                $("#shipping_city_"+seq).append(newOption3);

            $("#shipping_address_"+seq).val($("#billing_address_"+seq).val());
            $("#shipping_post_code_"+seq).val($("#billing_post_code_"+seq).val());
        }
    }
    function checkCompanyNameAllReadyAvilableOrNot(){
        $("#allreadycompanyname").hide();
        var company_name = $("#company").val();
        if(company_name !=""){
            $.ajax({
                    url: "ajax.php",
                    data:'page=checkcompanynameallreadyavilableornot&company_name='+company_name,
                    type: "POST",
                    async: false,
                    success:function(data){
                        console.log(data);
                    var res = JSON.parse(data);
                    if(res.company_name == '1'){
                        console.log('already');
                        $("#companynameerror").text(company_name);
                        $("#allreadycompanyname").show();
                        $("#company").val('');
                    }else{
                        console.log('frash');
                        $("#company").val();
                    }
                    },
                    error:function (){}
                });

        }

    }
    function checkalreadyavailableornotcustomerorvendordetails(field, el = null){
        $('#emailerror').hide();
        $('.gstinerror').hide();
        $('#pannoerror').hide();
        var fieldName = '';
        var fieldValue = '';
        if(field  == 'EMAIL'){
            fieldName = 'email';
            fieldValue = $("#email").val().trim();
        }else if (field == 'GSTIN') {
            fieldName = 'gstin';
            fieldValue = $(el).val().trim(); // GST input value of this row
        }else if(field == 'PAN_NO'){
            fieldName = 'pan_no';
            fieldValue = $("#pan_no").val().trim();
        }
        console.log('fieldValue', (fieldValue));
        if(fieldValue !== ""){
            console.log('Start');
            $.ajax({
                    url: "ajax.php",
                    data:`page=checkalreadyavailableornotcustomerorvendordetails&table_name=vendors&field_name=${fieldName}&field_value=${fieldValue}`,
                    type: "POST",
                    async: false,
                    success:function(data){
                    var res = JSON.parse(data);
                    if(res.result != ''  &&  res.result != null){
                        console.log(res.result);
                        if(field  == 'EMAIL'){
                            $('#emailerror').text(fieldValue+" Already Register");
                            $('#emailerror').show();
                            $("#email").val('');
                        }
                        if (field === 'GSTIN' && el) {
                            $(el).siblings(".gstinerror")
                                .text(fieldValue + " Already Registered")
                                .show();
                            $(el).val('');
                        }
                        if(field  == 'PAN_NO'){
                            $('#pannoerror').text(fieldValue+" Already Register");
                            $('#pannoerror').show();
                            $("#pan_no").val('');
                        }
                    }
                    },
                    error:function (){}
                });

        }
        console.log('end');
    }

</script>
