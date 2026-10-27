<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vendor_id = $_POST['vendor_id'];
$transaction_id = $_POST['candidate_id'];
$transaction_table = 'candidates';
$url = API."?mode=getcandidatemaster&candidate_id=".$transaction_id;
$response = callAPI($url);
$result = json_decode($response,1);
$candidates = $result['candidates'];
$api_hit = array();
foreach($api_hits as $ah){
    $api_hit[$ah['api_master_id']][] = $ah;
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

$transaction_activity_schedule = $result['transaction_activity_schedule_list'];

?>
<script type="text/javascript">
    function panVerification(btn_id,btn_text,tab_id){
        var pan_no = $("#pan_no_popup").val();
        var transaction_table = '<?php echo $transaction_table; ?>';
        var transaction_id = '<?php echo $transaction_id; ?>';
        var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
        $('#'+btn_id).html(htmloader);
        $('#'+btn_id).addClass("disabled");
        $('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
        jQuery.ajax({
            url: "apis/call_api.php",
            data:'&panNumber='+pan_no+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+"&api_code=PANEXTENSIVE&getStatusInfo=true",
            type: "POST",
            success:function(data){
                var res = JSON.parse(data);
                console.log(res);
                try {
                    if(res && res.response_code =="200"){
                        var pangstlist = "";
                        pangstlist +='<h4>PAN Details</h4>';
                        pangstlist +='<table class="table table-hover">';
                        pangstlist +='<tbody>';

                        if(res.isValid == true && res.panStatusCode == 'E'){
                            var htmloader = '<i class="ti-check-box"></i>  Check PAN';
                            $('#'+btn_id).html(htmloader);
                            $('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
                            $('#'+btn_id).removeClass('btn-danger');
                            $('#'+btn_id).addClass('btn-success');
                            $('#'+btn_id).removeAttr("onclick");

                            pangstlist +='<tr><th scope="col" class="p-2">Name</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['display_name']); ?></td><td class="p-2 w-b-w-s text-danger">'+res.name+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Pan Number</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['pan_number']); ?></td><td class="p-2 w-b-w-s text-danger">'+res.number+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">First Name</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['first_name']); ?></td><td class="p-2 w-b-w-s text-danger">'+res.firstName+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Middle Name</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['middle_name']); ?></td><td class="p-2 w-b-w-s text-danger">'+res.middleName+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Last Name</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['last_name']); ?></td><td class="p-2 w-b-w-s  text-danger">'+res.lastName+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Gender</th><td class="w-b-w-s p-2">-</td><td class="p-2 w-b-w-s  text-danger">'+res.gender+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Date Of Birth</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['date_of_birth']); ?></td><td class="p-2 w-b-w-s  text-danger">'+res.dateOfBirth+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">EmailId</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['email_id']); ?></td><td class="p-2 w-b-w-s  text-danger">'+res.emailId+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Address</th><td class="w-b-w-s p-2"><?php echo preg_replace('/\r|\n/', ' ', $candidates['address']); ?></td><td class="p-2 w-b-w-s  text-danger">'+res.address.fullAddress+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Address</th><td class="p-2">-</td><td class="p-2 w-b-w-s text-danger">'+res.address.addressLineOne+'</td></tr>';
                            pangstlist +='<tr><th scope="col" class="p-2">Country</th><td class="p-2">-</td><td class="p-2 w-b-w-s  text-danger">'+res.address.country+'</td></tr>';

                            pangstlist +='</tbody>';
                            pangstlist +='</table>';
                            $("#pan_response_popup").html(pangstlist);
                            var vergst = "PAN";
                            var verfybuttn = '<button class="btn btn-primary mb-3" type="button" onclick="verificationPANSuccess(\''+vergst+'\','+res.api_hit_id+');" >Click To Confirm PAN Detail</button>';
                            $("#pan_verification_btn").html(verfybuttn);

                        }else{
                            $('.pan_verification_error').text(res.message);
                            $('#'+btn_id).text(btn_text);
                            $('#'+btn_id).removeClass("disabled");
                            $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
                        }
                    }else if(res && res.response_code =="400"){
                        $('.pan_verification_error').text(res.error.message);
                        $('#'+btn_id).text(btn_text);
                        $('#'+btn_id).removeClass("disabled");
                        $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
                    }else{
                        $('.pan_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>');
                            $('#'+btn_id).text(btn_text);
                            $('#'+btn_id).removeClass("disabled");
                            $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');

                    }
                } catch (e) {
                    if(res.error.message !=""){
                        var errormsg = res.error.message;
                    }else{
                        var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                    }
                    $('.pan_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
                }
            },
            error:function (){
                $('.pan_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>');
                $('#'+btn_id).text(btn_text);
                $('#'+btn_id).removeClass("disabled");
                $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
            }
          });
    }
    function verificationPANSuccess(verification_type,api_hit_id){
        $('.tan_verification_error').text('<?php echo $_SESSION['error_message']['9136']; ?>');
        verificationSuccess(verification_type,api_hit_id,'<?php echo $_SESSION['error_message']['9138']; ?>');
    }
    function adhaarVerification(btn_id,btn_text,tab_id){
        var adhaar_no_popup = $("#adhaar_no_popup").val();
        var transaction_table = '<?php echo $transaction_table; ?>';
        var transaction_id = '<?php echo $vendor_id; ?>';
        var htmloader = 'Loading... <i class="fa fa-spinner fa-spin"></i> ';
        $('#'+btn_id).html(htmloader);
        $('.adhaar_verification_error').text('');
        $('#'+btn_id).addClass("disabled");
        $('#'+tab_id).attr('class', 'fa fa-spinner fa-spin f-30 align-middle');
        jQuery.ajax({
            url: "apis/call_api.php",
            data:'&uid='+adhaar_no_popup+'&transaction_table='+transaction_table+'&transaction_id='+transaction_id+"&api_code=AADHAARBASICVERIFICATION",
            type: "POST",
            success:function(data){
                console.log("data -"+data);
                var res = JSON.parse(data);
                console.log("return result -"+res);
                try {
                    if(res && res.response_code =="200"){
                        if(res.result.verified == true){
                            $('.adhaar_verification_error').text('<?php echo $_SESSION['error_message']['9139']; ?>');
                            var htmloader = '<i class="ti-check-box"></i>  Verified Successfully';
                            $('#'+btn_id).html(htmloader);
                            $('#'+tab_id).attr('class', 'ti-check-box f-30 align-middle');
                            $('#'+btn_id).removeClass('btn-danger');
                            $('#'+btn_id).addClass('btn-success');
                            $('#'+btn_id).removeAttr("onclick");
                            verificationSuccess("AADHAAR",res.api_hit_id,'<?php echo $_SESSION['error_message']['9139']; ?>');
                        }else{
                            $('.adhaar_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>');
                            $('#'+btn_id).text(btn_text);
                            $('#'+btn_id).removeClass("disabled");
                            $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
                            console.log("else 9135 - "+res.error.message);
                        }
                    }else{
                        $('.adhaar_verification_error').text(res.result.message);
                        $('#'+btn_id).text(btn_text);
                        $('#'+btn_id).removeClass("disabled");
                        $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
                        console.log("respons error - "+res.error.message);
                    }
                } catch (e) {
                    if(res.error.message !=""){
                        var errormsg = res.error.message;
                    }else{
                        var errormsg = '<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>';
                    }
                    $('.adhaar_verification_error').text(errormsg);
                    $('#'+btn_id).text(btn_text);
                    $('#'+btn_id).removeClass("disabled");
                    $('#'+tab_id).attr('class', 'ti-alert text-danger f-30');
                }
            },
            error:function (){
                $('.adhaar_verification_error').text('<?php echo $_SESSION['error_message']['9135']." ".$_SESSION['config']['admin_email']; ?>');
                $('#'+btn_id).text(btn_text);
                $('#'+btn_id).removeClass("disabled");
                $('#'+tab_id).attr('class', 'ti-alert text-danger f-30 align-middle');
            }
          });
    }
        function verificationSuccess(verification_type,api_hit_id,msg=""){
        var transaction_table = '<?php echo $transaction_table; ?>';
        var transaction_id = '<?php echo $vendor_id; ?>';
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatevendorverifications&transaction_table='+transaction_table+'&transaction_id='+transaction_id+"&verification_type="+verification_type+'&api_hit_id='+api_hit_id,
            type: "POST",
            success:function(data11){
                if(msg ==""){
                    msg = '<?php echo $_SESSION['error_message']['3001']; ?>';
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
                verificationVendorDetails(verification_type);
            }
        });
    }
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
                <li class="nav-item text-start p-2">
                    <a class="nav-link p-3 border <?php echo ($verification_type == "AADHAAR")?"active":""; ?>" data-bs-toggle="tab" href="#adhaarverification" role="tab"><?php echo $vendor_verifications['AADHAAR']['status'] == "Y"?'<i class="ti-check-box f-30 align-middle" id="adhaarverification_tabs"></i>':'<i class="ti-alert text-danger f-30 align-middle" id="adhaarverification_tabs_2"></i>'; ?> <span class="ml-2 d-inline-block align-middle">Aadhaar Number</span></a>
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
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
                    if($vendor_verifications['PAN']['status'] == "Y"){
                        $onclickfun = "";
                        $label = '<i class="ti-check-box"></i> Verified Successfully';
                        $class = "btn-success";
                        $spanmsg = $_SESSION['error_message']['9136'];
                        $disbled_btn = "disabled";
                    }else{
                        $onclickfun = "panVerification('btn_pan_no_popup','Check PAN','panverification_tabs')";
                        $label = "Check PAN";
                        $class = "btn-danger";
                        $spanmsg = "PAN verification is pending";
                    }
                    if($candidates['pan_number'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <span class="form-label">PAN Number</span>
                            <?php echo $candidates['pan_number'] ==""?"<span class='text-danger d-block f-12'>Please Update PAN Number</span>":""; ?>
                            <div class="input-group input-group-button mb-0">
                                <input aria-label="Pan No Popup" type="text" class="form-control width1" id="pan_no_popup" value="<?php  echo $candidates['pan_number']; ?>" autocomplete="off" readonly>
                                <button class="btn <?php echo $class; ?>" type="button" id="btn_pan_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                            </div>
                            <span class="f-12 text-danger pan_verification_error" ><?php echo $spanmsg; ?></span>
                        </div>
                    </div>
                    <div id="pan_response_popup" class="mt-3"></div>
                    <div id="pan_verification_btn" class="mt-3"></div>

                    <?php if(count((array)$api_hit['1']) != 0){ ?>
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
                        <?php foreach($api_hit['1'] as $pand){
                            $api_request_parameters = json_decode($pand['api_request_parameters'],1);
                            $api_response_details = json_decode($pand['api_response_details'],1);
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
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                </div>

                <div class="tab-pane <?php echo $verification_type == "AADHAAR"?"active":""; ?>" id="adhaarverification" role="tabpanel">
                    <?php
                    $onclickfun = $label = $class = $spanmsg = $disbled_btn = "";
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
                    if($candidates['adhar_no'] ==""){
                        $disbled_btn = "disabled";
                    }
                    ?>
                    <div class="row">
                        <span class="form-label d-block">Aadhaar Number</span>
                        <?php echo $candidates['adhar_no'] ==""?"<span class='text-danger d-block f-12'>Please Update Aadhaar Number</span>":""; ?>
                        <div class="input-group input-group-button mb-0">
                               <input aria-label="Adhaar No Popup" type="text" class="form-control width1" id="adhaar_no_popup" value="<?php  echo $candidates['adhar_no']; ?>" autocomplete="off" readonly>
                            <button class="btn <?php echo $class; ?>" type="button" id="btn_adhaar_no_popup" onclick="<?php echo $onclickfun; ?>" <?php echo $disbled_btn; ?>><?php echo $label; ?></button>
                        </div>
                        <span class="f-12 text-danger adhaar_verification_error"><?php echo $spanmsg; ?></span>
                    </div>
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

                        <div class="col-xl-4 col-md-6" onclick="dynamicFilePriviews('<?php echo $path; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <h2 class="text-facebook m-b-5">
                                        <?php if(in_array(strtolower(pathinfo($clist['attachment_name'], PATHINFO_EXTENSION)), $imageExtensions)){ ?>
                                            <img src="<?php echo $path; ?>" style="height: 100px; width: 100px;" alt="Details">
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

                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>

<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
