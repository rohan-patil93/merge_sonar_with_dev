<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vendor_id = $_POST['vendor_id'];
$verification_type = $_POST['verification_type'];
$transaction_table = $_POST['transaction_table'];
$url = API."?mode=getvendorsdetails&transaction_table=".$transaction_table."&id=".$vendor_id."&admin_type=".$_SESSION['admin_type']."&edittype=".$_POST['edittype'];
$response = callAPI($url);
$result = json_decode($response,1);
if ($_POST['vtype'] != "VR") {
   $vendordetail = $result['vendors'];
} else {
    $vendordetail = $result['vendor_registers'];
}
$vendor_verifications = json_decode($vendordetail['vendor_verifications'],1);
$api_hits = $result['api_hits'];
$countrieslist = $result['countries'];
$states = $result['states'];
$cities = $result['cities'];
$api_hit = array();
$pancardresult = $gstinresult = $bankaccountresult = $msmenumberresult = array();
foreach($api_hits as $ah){
    $api_hit[$ah['api_master_id']][] = $ah;
    if($ah['api_master_id'] == 1 || $ah['api_master_id'] == 37){
        $pancardresult[] = $ah;
    }elseif($ah['api_master_id'] == 3 || $ah['api_master_id'] == 38){
        $gstinresult[] = $ah;
    }elseif($ah['api_master_id'] == 7 || $ah['api_master_id'] == 39){
        $bankaccountresult[] = $ah;
    }elseif($ah['api_master_id'] == 8 || $ah['api_master_id'] == 40){
        $msmenumberresult[] = $ah;
    }elseif($ah['api_master_id'] == 44){
        $tanresult[] = $ah;
    }
}
$countrylist =$stateslist = $citieslist =$company_types =  array();
foreach($countrieslist as $country_list){
    $countrylist[$country_list['id']] = $country_list['country_name'];
}
foreach($states as $states_list){
    $stateslist[$states_list['id']] = $states_list['state_name'];
}
foreach($cities as $cities_list){
    $citieslist[$cities_list['id']] = $cities_list['city_name'];
}
foreach($result['company_types'] as $ctype) {
    $company_types[$ctype['id']] = $ctype;
}
$transaction_activity_schedule = $result['transaction_activity_schedule_list'];
?>
<script type="text/javascript" src="../assets/js/custom_insert_vendor_verifications_popup.js"></script>
<script type="text/javascript">


</script>
<div class="modal-header">
    <h5 class="modal-title">Verifications</h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-20 m-r-20">
    <div class="row">
        <div class="col-lg-12">
        </div>
        <div class="col-lg-3">
            <ul class="nav nav-tabs md-tabs d-block bg-gray">
                <li class="nav-item text-start p-2">
                    <a class="nav-link p-3 border <?php echo ($verification_type =="" || $verification_type == "PAN")?"active":""; ?>" data-bs-toggle="tab" href="#panverification_tab"  role="tab" aria-selected="false"><?php echo $vendor_verifications['PAN']['status'] == "Y"?'<i class="ti-check-box f-30 align-middle" id="panverification_tabs"></i>':'<i class="ti-alert text-danger f-30 align-middle" id="panverification_tabs_2"></i>'; ?><span class="ml-2 d-inline-block align-middle">PAN Number</span></a>
                </li>
                <li class="nav-item text-start  p-2">
                    <a class="nav-link p-3 border <?php echo $verification_type == "GSTIN"?"active":""; ?>" data-bs-toggle="tab" href="#gstverification" role="tab" aria-selected="false" ><?php echo $vendor_verifications['GSTIN']['status'] == "Y"?'<i class="ti-check-box f-30 align-middle" id="gstverification_tabs"></i>':'<i class="ti-alert text-danger f-30 align-middle" id="gstverification_tabs_2"></i>'; ?><span class="ml-2 d-inline-block align-middle"> GSTN Number</span></a>
                </li>



                <li class="nav-item text-start p-2">
                    <a class="nav-link p-3 border <?php echo $verification_type == "BANK"?"active":""; ?>" data-bs-toggle="tab" href="#bankverification" role="tab"><?php echo $vendor_verifications['BANK']['status'] == "Y"?'<i class="ti-check-box f-30 align-middle" id="bankverification_tabs"></i>':'<i class="ti-alert text-danger f-30  align-middle" id="bankverification_tabs_2"></i>'; ?><span class="ml-2 d-inline-block align-middle">Bank Account</span></a>
                </li>
                <li class="nav-item text-start p-2">
                    <a class="nav-link p-3 border <?php echo $verification_type == "MSME"?"active":""; ?>" data-bs-toggle="tab" href="#msmeverification" role="tab" aria-selected="true"><?php echo $vendor_verifications['MSME']['status'] == "Y"?'<i class="ti-check-box f-30 align-middle" id="msmeverification_tabs"></i>':'<i class="ti-alert text-danger f-30  align-middle" id="msmeverification_tabs_2"></i>'; ?><span class="ml-2 d-inline-block align-middle">MSME Number</span></a>
                </li>
                <li class="nav-item text-start p-2">
                    <a class="nav-link p-3 border " data-bs-toggle="tab" href="#documents_tab" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-file f-30  align-middle" ></i><span class="ml-2 d-inline-block align-middle"> Documents</span></a>
                </li>
            </ul>
        </div>
        <div class="col-lg-9">
            <div class="tab-content card-block p-2">
                <div class="tab-pane <?php echo ($verification_type =="" || $verification_type == "PAN")?"active":""; ?>" id="panverification_tab" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['PAN']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9136'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "panVerification('btn_pan_no_popup','Click To Verify PAN','panverification_tabs')";
                        $label = "Click To Verify PAN";
                        $class = "btn-danger";
                        $spanmsg = "PAN verification is pending";
                    }
                    if($vendordetail['company'] =="" || $vendordetail['pan_no'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>

                    <div class="row">
                        <div class="col-lg-5">
                            <span class="form-label">Company Name</span>
                            <?php echo $vendordetail['company'] ==""?"<span class='text-danger d-block f-12'>Please Update Company Name</span>":""; ?>
                            <input aria-label="Company Name Popup" type="text" class="form-control" id="company_name_popup" value="<?php  echo $vendordetail['company']; ?>"  readonly>
                        </div>
                        <div class="col-lg-7">
                            <span class="form-label">PAN Number</span>
                            <?php echo $vendordetail['pan_no'] ==""?"<span class='text-danger d-block f-12'>Please Update PAN Number</span>":""; ?>
                            <div class="input-group input-group-button mb-0">
                                <input aria-label="Pan No Popup" type="text" class="form-control width1" id="pan_no_popup" value="<?php  echo $vendordetail['pan_no']; ?>" autocomplete="off" readonly>
                                <button class="btn <?php echo $class; ?>" type="button" id="btn_pan_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                            </div>
                            <span class="f-12 text-danger pan_verification_error" ><?php echo $spanmsg; ?></span>
                        </div>
                    </div>
                    <div id="pan_response_popup" class="mt-3"></div>
                    <?php if(count((array)$pancardresult) != 0){ ?>
                    <h3 class="mt-4 bg-gray">Details</h3>
                    <table class="table table-hover mt-3">
                        <thead>
                            <tr>
                                <th scope="col" class="">Company Name</th>
                                <th scope="col" class="">PAN Number</th>
                                <th scope="col" class="">Validated Company Name</th>
                                <th scope="col" class="">Date Time</th>
                                <th scope="col" class="">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($pancardresult as $pand){
                            $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                            $api_response_details = json_decode($pand['api_response_details'],1);
                        if($pand['api_master_id'] == "1"){
                            ?>
                            <tr>
                            <td class="w-b-w-s p-2"><?php echo $api_request_parameters['name']; ?></td>
                            <td class="w-b-w-s p-2"><?php echo $api_request_parameters['number']; ?></td>
                            <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['upstreamName']; ?></td>
                            <td class="w-b-w-s text-danger p-2"><?php echo $pand['response_date_time']; ?></td>
                            <td class="w-b-w-s text-danger p-2">
                                <?php
                                    if($api_response_details['result']['verified'] == "true"){
                                        echo $_SESSION['error_message']['9136'];
                                    }elseif($api_response_details['result']['verified'] == "false"){
                                        echo $api_response_details['result']['message'];
                                    }else{
                                        echo $api_response_details['error']['message'];
                                    }
                                ?>

                            </td>
                        </tr>
                        <?php }else{ ?>

                        <tr>
                            <td class="w-b-w-s p-2"><?php echo $api_request_parameters['data']['pan_holder_name']; ?></td>
                            <td class="w-b-w-s p-2"><?php echo $api_request_parameters['data']['customer_pan_number']; ?></td>
                            <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['user_full_name']; ?></td>
                            <td class="w-b-w-s text-danger p-2"><?php echo $pand['response_date_time']; ?></td>
                            <td class="w-b-w-s text-danger p-2">
                                <?php
                                    if($api_response_details['result']['pan_status'] == "VALID"){
                                        echo $_SESSION['error_message']['9136'];
                                    }else{
                                        echo $api_response_details['response_message'];
                                    }
                                ?>

                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                </div>
                <div class="tab-pane <?php echo $verification_type == "GSTIN"?"active":""; ?>" id="gstverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['GSTIN']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9137'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "gstnVerification('btn_gstn_no_popup','Check GSTN','gstverification_tabs')";
                        $label = "Check GSTN";
                        $class = "btn-danger";
                        $spanmsg = "GSTN verification is pending";
                    }
                    if($vendordetail['gstin'] =="" || $vendordetail['pan_no'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="row">

                        <div class="col-lg-12">
                            <span class="form-label">GSTN Number</span>
                            <?php echo $vendordetail['gstin'] ==""?"<span class='text-danger d-block f-12'>Please Update GSTIN Number</span>":""; ?>
                            <div class="input-group input-group-button mb-0">
                                <input aria-label="Gstn No Popup" type="text" class="form-control width1" id="gstn_no_popup" value="<?php  echo $vendordetail['gstin']; ?>" autocomplete="off" readonly>
                                <button class="btn <?php echo $class; ?>" type="button" id="btn_gstn_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                            </div>
                            <span class="f-12 text-danger gstn_verification_error"><?php echo $spanmsg; ?></span>
                        </div>
                        <div id="gstn_details_popup" class="mt-3"></div>
                        <div id="gstn_details_list_popup" class="mt-3"></div>
                        <div id="gstn_verification_btn" class="mt-3"></div>
                    </div>
                        <?php if(count((array)$gstinresult) != 0){ ?>

                    <h3 class="bg-gray">Details</h3>
                    <ul class="nav md-tabs infisuite_tab mt-3">
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#gstindetial" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">GSTNIN</a>
                        </li>

                    </ul>
                    <div class="tab-content tabs custom-scroll">
                        <?php  ?>
                        <div class="tab-pane card-body active p-0 mt-3" id="gstindetial" role="tabpanel">
                            <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                                <div class="color-accordion" id="color-accordion_2">
                                <?php foreach($gstinresult as $pand){
                                    $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                                    $api_response_details = json_decode($pand['api_response_details'],1);
                            ?>



                        <?php if($pand['api_master_id'] == "3"){ ?>
                        <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray">
                            <?php echo $pand['response_date_time']." - ( GSTIN Number -".$api_request_parameters['gstin']." )"; ?>
                        </a>
                        <div class="accordion-desc custom-scroll p-0">
                            <?php if($api_response_details['response_code'] =="200"){ ?>
                            <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" class="" style="width: 19%;"></th>
                                    <th scope="col" class="" style="width: 1%;"></th>
                                    <th scope="col" class="" style="width: 40%;">Vendor Details</th>
                                    <th scope="col" class="" style="width: 40%;">GST Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col"  class="p-2" >Company Name</th>
                                    <td  class="p-2" >:</td>
                                    <td class="w-b-w-s p-2" ><?php  echo $vendordetail['company']; ?></td>
                                    <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['legalNameOfBusiness']; ?></td>
                                </tr>
                            <tr>
                                <th scope="col"  class="p-2">Registration Date</th>
                                <td  class="p-2">:</td>
                                <td class="p-2">-</td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['registrationDate']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Business Type</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['constitutionOfBusiness']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Email ID</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($vendordetail['email']); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['emailId']; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Address</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo $vendordetail['billing_address'].",".$vendordetail['billing_city']; ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['address']; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Mobile</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($vendordetail['mobile']); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['mobile']; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">District</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['splitAddress']['district'][0]; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">State</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['splitAddress']['state'][0]; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">City</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['splitAddress']['city'][0]; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Pincode</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($vendordetail['billing_post_code']); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['gstnDetailed']['principalPlaceAddress']['splitAddress']['pincode']; ?></td>

                            </tr>
                            </tbody>
                            </table>
                        <?php if($vendor_verifications['GSTIN']['status'] == "N"){  ?>
                            <button class="btn btn-primary mb-3 mt-2 " type="button" id="btn_gstn_no_popup_2" onclick="verificationGSTSuccess('GSTIN','<?php echo $pand['id']; ?>');" >Click To Confirm GST Detail</button>
                        <?php } ?>
                        <?php }else{ ?>
                            <span class="text-danger f-12"><?php echo $api_response_details['error']['message']; ?></span>

                        <?php } ?>
                    </div>

                <?php }else{ ?>

                            <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray">
                                    <?php echo $pand['response_date_time']." - ( GSTIN Number -".$api_request_parameters['gstin']." )"; ?>
                            </a>
                            <div class="accordion-desc custom-scroll p-0">
                            <?php if($api_response_details['response_code'] =="200"){ ?>
                            <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" class="" style="width: 19%;"></th>
                                    <th scope="col" class="" style="width: 1%;"></th>
                                    <th scope="col" class="" style="width: 40%;">Vendor Details</th>
                                    <th scope="col" class="" style="width: 40%;">GST Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col"  class="p-2" >Company Name</th>
                                    <td  class="p-2" >:</td>
                                    <td class="w-b-w-s p-2" ><?php  echo $vendordetail['company']; ?></td>
                                    <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['legal_name']; ?></td>
                                </tr>
                            <tr>
                                <th scope="col"  class="p-2">Registration Date</th>
                                <td  class="p-2">:</td>
                                <td class="p-2">-</td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['register_date']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">Business Type</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['business_constitution']; ?></td>
                            </tr>

                            <tr>
                                <th scope="col"  class="p-2">Address</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo $vendordetail['billing_address'].",".$vendordetail['billing_city']; ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['primary_business_address']['full_address']; ?></td>

                            </tr>

                            <tr>
                                <th scope="col"  class="p-2">District</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['primary_business_address']['district']; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">State</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['primary_business_address']['state_code']; ?></td>

                            </tr>
                            <tr>
                                <th scope="col"  class="p-2">City</th>
                                <td  class="p-2">:</td>
                                <td class="w-b-w-s p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                                <td class="w-b-w-s text-danger p-2"><?php echo $api_response_details['result']['primary_business_address']['city']; ?></td>

                            </tr>


                        </tbody>
                            </table>
                        <?php if($vendor_verifications['GSTIN']['status'] == "N"){  ?>
                            <button class="btn btn-primary mb-3 mt-2 " type="button" id="btn_gstn_no_popup_3" onclick="verificationGSTSuccess('GSTIN','<?php echo $pand['id']; ?>');" >Click To Confirm GST Detail</button>
                        <?php } ?>
                        <?php }else{ ?>
                            <span class="text-danger f-12"><?php echo $api_response_details['response_message']; ?></span>

                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>
                </div>
                <div class="tab-pane <?php echo $verification_type == "TAN"?"active":""; ?>" id="tanverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['TAN']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9138'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "tanVerification('btn_tan_no_popup','Check TAN','tanverification_tabs')";
                        $label = "Check TAN";
                        $class = "btn-danger";
                        $spanmsg = "TAN verification is pending";
                    }
                    if($vendordetail['tan_number'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="row">

                        <div class="col-lg-12">
                            <span class="form-label">TAN Number</span>
                            <?php echo $vendordetail['tan_number'] ==""?"<span class='text-danger d-block f-12'>Please Update Tan Number</span>":""; ?>
                               <div class="input-group input-group-button mb-0">
                                <input aria-label="Tan No Popup" type="text" class="form-control width1"  id="tan_no_popup" value="<?php  echo $vendordetail['tan_number']; ?>" autocomplete="off" readonly>
                                <button class="btn <?php echo $class; ?>" type="button" id="btn_tan_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                            </div>
                            <span class="f-12 text-danger tan_verification_error"><?php echo $spanmsg; ?></span>
                        </div>
                    </div>
                    <div id="tan_response_popup" class="mt-3"></div>
                    <?php if(count((array)$api_hit['6']) != 0){ ?>
                    <h4>Tan Details</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" class="p-2">Company Name</th>
                                <th scope="col" class="p-2">Tan Number</th>
                                <th scope="col" class="p-2">status</th>
                                <th scope="col" class="p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($api_hit['6'] as $pand){
                            $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                            $api_response_details = json_decode($pand['api_response_details'],1);
                            ?>
                        <tr>
                            <td class="p-2 w-b-w-s"><?php echo $api_response_details['result']['companyName']; ?></td>
                            <td class="p-2 w-b-w-s"><?php echo $api_request_parameters['tanNumber']; ?></td>
                            <td class="p-2 text-danger w-b-w-s"><?php echo $api_response_details['result']['isValid'] == "true"?$_SESSION['error_message']['9138']:$api_response_details['error']['message']; ?></td>
                            <td class="p-2">
                                <?php if($vendor_verifications['TAN']['status'] != "Y"){  ?>
                                <button class="btn btn-primary mb-3" type="button" onclick="verificationTANSuccess('TAN','<?php echo $pand['id']; ?>')" >Click To Confirm TAN Detail</button>
                            <?php }else{ echo "-";
}?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                </div>
                <div class="tab-pane <?php echo $verification_type == "AADHAAR"?"active":""; ?>" id="adhaarverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['AADHAAR']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9139'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "adhaarVerification('btn_adhaar_no_popup','Click To Verify Aadhaar','adhaarverification_tabs')";
                        $label = "Click To Verify Aadhar";
                        $class = "btn-danger";
                        $spanmsg = "Aadhaar verification is pending";
                    }
                    if($vendordetail['aadhaar_no'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="input-group input-group-button mb-0">
                        <?php echo $vendordetail['aadhaar_no'] ==""?"<span class='text-danger d-block f-12'>Please Update Aadhaar Number</span>":""; ?>
                        <span class="form-label">Aadhaar Number</span>
                        <input aria-label="Adhaar No Popup" type="text" class="form-control width1" id="adhaar_no_popup" value="<?php  echo $vendordetail['aadhaar_no']; ?>" autocomplete="off" readonly>
                        <button class="btn <?php echo $class; ?>" type="button" id="btn_adhaar_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                    </div>
                    <span class="f-12 text-danger adhaar_verification_error"><?php echo $spanmsg; ?></span>
                </div>
                <div class="tab-pane <?php echo $verification_type == "BANK"?"active":""; ?>" id="bankverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['BANK']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9140'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "bankVerifications('btn_bank_account_no_popup','Click To Verify Bank Account','bankverification_tabs')";
                        $label = "Click To Verify Bank Account";
                        $class = "btn-danger";
                        $spanmsg = "Bank Account verification is pending";
                    }
                    if($vendordetail['ifsc_code'] =="" || $vendordetail['bank_account_no'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="row">
                        <div class="col-lg-5">
                            <span class="form-label">IFSC code</span>
                            <?php echo $vendordetail['ifsc_code'] ==""?"<span class='text-danger d-block f-12'>Please Update IFSC Code</span>":""; ?>
                            <input aria-label="Ifsc Code Popup" type="text" class="form-control" id="ifsc_code_popup" value="<?php  echo $vendordetail['ifsc_code']; ?>"  readonly>
                        </div>
                           <div class="col-lg-7">
                               <span class="form-label">Bank Account Number</span>
                               <?php echo $vendordetail['bank_account_no'] ==""?"<span class='text-danger d-block f-12'>Please Update Tan Number</span>":""; ?>
                               <div class="input-group input-group-button mb-0">
                                <input aria-label="Bank Account No Popup" type="text" class="form-control width1"  id="bank_account_no_popup" value="<?php  echo $vendordetail['bank_account_no']; ?>" autocomplete="off" readonly>
                                <button class="btn <?php echo $class; ?>" type="button" id="btn_bank_account_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                            </div>
                            <span class="f-12 text-danger bank_verification_error"><?php echo $spanmsg; ?></span>

                        </div>
                    </div>
                    <div id="Bank_response_popup" class="mt-3"></div>
                     <?php if(count((array)$bankaccountresult) != 0){ ?>
                    <h3 class="mt-4 bg-gray">Details</h3>
                    <table class="table table-hover mt-3">
                        <thead>
                            <tr>
                                <th scope="col" class="">IFSC code</th>
                                <th scope="col" class="">Bank Account Number</th>
                                <th scope="col" class="">Name</th>
                                <th scope="col" class="">Date Time</th>
                                <th scope="col" class="">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($bankaccountresult as $pand){
                            $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                            $api_response_details = json_decode($pand['api_response_details'],1);
                            ?>
                            <?php if($pand['api_master_id'] == "7"){ ?>
                        <tr>
                            <td class="p-2"><?php echo $api_request_parameters['beneficiaryIFSC']; ?></td>
                            <td class="p-2"><?php echo $api_request_parameters['beneficiaryAccount']; ?></td>
                            <td class="p-2 text-danger"><?php echo $api_response_details['result']['bankTransfer']['beneName']; ?></td>
                            <td class="p-2 text-danger"><?php echo $pand['response_date_time']; ?></td>
                            <td class="w-b-w-s p-2 text-danger"><?php echo $api_response_details['result']['active'] == "yes"?$_SESSION['error_message']['9140']:$api_response_details['error']['message']; ?></td>
                        </tr>

                        <?php }else{ ?>

                        <tr>
                            <td class="p-2"><?php echo $api_request_parameters['data']['ifsc']; ?></td>
                            <td class="p-2"><?php echo $api_request_parameters['data']['account_number']; ?></td>
                            <td class="p-2 text-danger"><?php echo $api_response_details['result']['beneficiary_name']; ?></td>
                            <td class="p-2 text-danger"><?php echo $pand['response_date_time']; ?></td>
                            <td class="w-b-w-s p-2 text-danger"><?php echo $api_response_details['result']['verification_status'] == "VERIFIED"?$_SESSION['error_message']['9140']:$api_response_details['error']['message']; ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>

                        </tbody>
                    </table>
                <?php } ?>
                </div>

                 <div class="tab-pane <?php echo $verification_type == "MSME"?"active":""; ?>" id="msmeverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    $disbled_btn = "disabled";
                    if($vendor_verifications['MSME']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9141'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "mSMEVerifications('btn_msme_no_popup','Click To Verify MSME','msmeverification_tabs')";
                        $label = "Check MSME";
                        $class = "btn-danger";
                        $spanmsg = "MSME verification is pending";
                    }
                    if($vendordetail['msme_number'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="row">
                           <span class="form-label">Udyam Registration/MSME Number</span>
                           <?php echo $vendordetail['msme_number'] ==""?"<span class='text-danger d-block f-12'>Please Update MSME Number</span>":""; ?>
                           <div class="input-group input-group-button mb-0">
                            <input aria-label="Msme No Popup" type="text" class="form-control width1"  id="msme_no_popup" value="<?php  echo $vendordetail['msme_number']; ?>" autocomplete="off" readonly>
                            <button class="btn <?php echo $class; ?>" type="button" id="btn_msme_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                        </div>
                        <span class="f-12 text-danger msme_verification_error"><?php echo $spanmsg; ?></span>
                    </div>
                    <div id="msme_details_list_popup" class="mt-3"></div>
                    <div id="msme_verification_btn" class="mt-3 mb-3"></div>


                    <?php if(count((array)$msmenumberresult) != 0){ ?>
                        <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                            <div class="color-accordion" id="color-accordion_3">
                            <?php foreach($msmenumberresult as $pand){
                                $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                                $api_response_details = json_decode($pand['api_response_details'],1);  ?>
                            <?php if($pand['api_master_id'] == "8"){ ?>

                                <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray">
                                        <?php echo $pand['response_date_time']." - ( MSME Number -".$api_request_parameters['udyamNumber']." )"; ?>
                                </a>
                                <div class="accordion-desc custom-scroll p-0">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr><th scope="col" class="p-2">Udyam Registration Number</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['udyamRegistrationNumber']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">Name Of Enterprise</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['nameOfEnterprise']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Major Activity</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['majorActivity']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Organisation Type</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['organisationType']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Social Category</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['socialCategory']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Date Of Incorporation</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['dateOfIncorporation']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Date Of Commencement Of Production Business</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['dateOfCommencementOfProductionBusiness']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">DIC</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['dic']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">MSMEDI</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['msmedi']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">Date Of Udyam Registration</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['dateOfUdyamRegistration']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">Type Of Enterprise</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['generalInfo']['typeOfEnterprise']; ?></td></tr>
                                            <tr><th scope="col" class="p-2" colspan="2"><a class="text-primary" href="<?php echo $api_response_details['result']['pdfUrl']; ?>" target="_blank" download>Certificate Click To Download</a></th></tr>
                                        </tbody>
                                    </table>

                                    <?php if($vendor_verifications['MSME']['status'] != "Y"){  ?>
                                        <button class="btn btn-primary mb-3 mt-2 " type="button" id="btn_gstn_no_popup_4" onclick="verificationMSMESuccess('MSME','<?php echo $pand['id']; ?>');" >Click To Confirm MSME Detail</button>
                                    <?php } ?>
                                </div>

                            <?php }else{ ?>


                            <?php     if($pand['response_status'] == 200){
                            ?>
                                <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray">
                                        <?php echo $pand['response_date_time']." - ( MSME Number -".$api_request_parameters['udyog_aadhaar']." )"; ?>
                                </a>
                                <div class="accordion-desc custom-scroll p-0">
                                    <table class="table table-hover">
                                        <tbody>

                                        <tr>

                                            <tr><th scope="col" class="p-2">Name Of Enterprise</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['enterprise_type']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Major Activity</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['major_activity']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Organisation Type</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['organization_type']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Social Category</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['social_category']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Date Of Incorporation</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['date_of_commencement']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">Date Of Commencement Of Production Business</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['date_of_commencement']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">DIC</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['dic_name']; ?></td></tr>

                                            <tr><th scope="col" class="p-2">MSMEDI</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['msme_dfo']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">Date Of Udyam Registration</th><td class="p-2 text-danger"><?php echo $api_response_details['result']['applied_date']; ?></td></tr>
                                            <tr><th scope="col" class="p-2">Status</th><td class="p-2 text-danger"><?php echo $api_response_details['response_message']; ?></td></tr>

                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php if($vendor_verifications['MSME']['status'] != "Y"){  ?>
                                        <button class="btn btn-primary mb-3 mt-2 " type="button" id="btn_gstn_no_popup_5" onclick="verificationMSMESuccess('MSME','<?php echo $pand['id']; ?>');" >Click To Confirm MSME Detail</button>
                                    <?php } ?>
                                </div>
                            <?php }else{ ?>

                                <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray">
                                        <?php echo $api_response_details['response_message']; ?>
                                </a>
                                <div class="accordion-desc custom-scroll p-0">
                                    <?php echo $api_response_details['response_message']; ?>
                                </div>

                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                 <div class="tab-pane" id="documents_tab" role="tabpanel">
                     <div class="row">
                     <?php
                         $docavl = 0;
                         foreach($transaction_activity_schedule as $tas){
                            $check_list = json_decode($tas['check_list'], 1);
                            foreach($check_list as $clist){
                                if($clist['attachment_name'] !=""){ $docavl++;
                                    $path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$tas['transaction_table']."/".$tas['assigned_to']."/Documents/".$clist['attachment_name'];

                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
                    ?>

                        <div class="col-xl-4 col-md-6 dynamicFilePriviews" data-pathurl="<?php echo $path; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <h2 class="text-facebook m-b-5">
                                        <?php if(in_array(strtolower(pathinfo($clist['attachment_name'], PATHINFO_EXTENSION)), $imageExtensions)){ ?>
                                            <img src="<?php echo $path; ?>" class="height-100 width-100" alt="Details">
                                        <?php }else{  ?>
                                                <i class="ti-file f-80 text-mute"></i>
                                        <?php } ?>

                                    </h2>
                                    <a  class="f-12 text-primary custom-limit-text mt-3 cursor-pointer" style="--linehide: 1;"><?php echo $clist['name']; ?></a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </div>
                    <?php if($docavl == 0){?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Documents</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                    <?php  ?>

                </div>



            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>

<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
