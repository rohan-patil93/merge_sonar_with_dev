<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vendor_id = $_POST['transaction_id'];
$transaction_table = $_POST['transaction_table'];
$url = API."?mode=getvendorsdetails&transaction_table=".$transaction_table."&id=".$vendor_id."&admin_type=".$_SESSION['admin_type'];
$response = callAPI($url);
$result = json_decode($response,1);
$role = $result['vendors'];
$vendor_registers = $result['vendor_registers'];
$vendordetail = $vendor_registers;
if($_POST['vtype'] !="VR"){
    $vendordetail = $result['vendors'];
}else{
    $vendordetail = $result['vendor_registers'];
}
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
$vendor_contact_info_type = $result['vendor_contact_info_type'];
$bank_acoount_type = $result['bank_acoount_type'];
$other_contact_information = json_decode($vendordetail['other_contact_information'],1);
$vendor_questions = json_decode($vendordetail['vendor_questions'],1);
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
function answerYesOrNo($answ){
    $return = "<span class='text-danger'>Pending<span>";
    if($answ == "Y"){
        $return = "Yes";
    }elseif($answ == "N"){
        $return = "No";
    }
    return $return;
}
$vendor_display_fields = $result['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'],1);

// ananthapadmanabha.pai@microchip.com
?>
<script type="text/javascript">
function printDiv(divId) {
   var printContents = document.getElementById(divId).innerHTML;
   var originalContents = document.body.innerHTML;
   document.body.innerHTML = "<html lang=\"en\"><head><title>Vendors Details</title></head><body>" + printContents + "</body>";
   window.print();
   document.body.innerHTML = originalContents;
}
function convertHTMLToPDF() {
    var  title ='Vendor Registration Details';
    var htmlContent = $('#printDiv_popup').html();
    $.ajax({
        url: 'convert_html_to_pdf.php',
        type: 'POST',
        data: { html_content: htmlContent,title:title },
        success: function(pdfdetail) {
            var result = JSON.parse(pdfdetail);
            var link = document.createElement('a');
            link.href = result.pdf_url;
            link.download =result.pdf_name;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            $.ajax({
                url: 'convert_html_to_pdf.php',
                type: 'POST',
                data: 'removepdf='+result.pdf_name,
                success: function(res) {
                    console.log(res);
                }
            });
        }
    });
}
</script>

<style type="text/css">
    td, th { white-space: normal;}
</style>
<div class="modal-header">
    <h5 class="modal-title"> Vendor Summary </h5>

    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body" id="printDiv_popup">
    <link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendors_and_vendor_registers_ajax.css">
    <div class="body">
         <?php if ($transaction_table == "vendor_registers") { ?>
        <div style="width: 100%;">
            <div style="font-size:21px; color: #000000; background: #eeeeee; padding: 10px 20px;">Important Instructions</div>
            <div style="font-size:13px; margin:10px 0 0 20px;"><b>1. </b>Fields marked with Asterisk (*) sign are mandatory</div>
            <div style="font-size:13px; margin:10px 0 0 20px"><b>2. </b>All Attachments should be in PDF or JPG format ONLY.</div>
            <div style="font-size:13px; margin:10px 0 0 20px;"><b>3. </b>PAN Card copy compulsory for vendor registration.</div>
            <div style="font-size:13px; margin:10px 0 0 20px;"><b>4. </b>Only PDF, jpeg files of maximum size 1 MB are allowed to be uploaded</div>
            <div style="font-size:13px; margin:10px 0 0 20px;"><b>5. </b>For any technical queries, please write admin@vendorstaq.com</div>
            <div style="font-size:13px; margin:10px 0 0 20px;"><b>6. </b>Attach Scan copy of cheque (With vendor name printed on it) AND bank details on vendor letter head stamped/verify by bankers.</div>
            <div style="margin:10px 0 0 20px;">
                <label for="registration"><i class="ti-check-box"></i> I / We have read the instructions, will follow them during registration</label>
            </div>
        </div>
    <?php } ?>
        <?php     $fieldaccesright = $vafield['business_information'];
                if($fieldaccesright['access'] !="NA"){
        ?>
        <div class="mt-3 w-100" style="">
            <div class="bg-gray"><h3 class="m-0">Business Information</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <?php
                    if($fieldaccesright['field']['company']!="NA"){
                ?>
                <tr>
                    <th scope="col">Company Name</th>
                    <td><?php echo emptytextdashShow($vendordetail['company']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['website_url']!="NA"){
                ?>
                <tr>
                    <th scope="col">Corporate Website</th>
                    <td><?php echo emptytextdashShow($vendordetail['website_url']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['company_trading_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">DBA(Trading Name)</th>
                    <td><?php echo emptytextdashShow($vendordetail['company_trading_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['company_subsidiary_division']!="NA"){
                ?>
                <tr>
                    <th scope="col">Subsidiary / Division Of</th>
                    <td><?php echo emptytextdashShow($vendordetail['company_subsidiary_division']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['first_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">First Name</th>
                    <td><?php echo emptytextdashShow($vendordetail['first_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['middle_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">Middle Name</th>
                    <td><?php echo emptytextdashShow($vendordetail['middle_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['last_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">Last Name</th>
                    <td><?php echo emptytextdashShow($vendordetail['last_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['email']!="NA"){
                ?>
                <tr>
                    <th scope="col">Email</th>
                    <td><?php echo emptytextdashShow($vendordetail['email']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['phone']!="NA"){
                ?>
                <tr>
                    <th scope="col">Phone</th>
                    <td><?php echo emptytextdashShow($vendordetail['phone']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['billing_address']!="NA"){
                ?>
                <tr style="background-color: #e8e9ff;">
                    <th scope="col">Billing Adderss</th>
                    <td><?php echo emptytextdashShow($vendordetail['billing_address']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['billing_country']!="NA"){
                ?>
                <tr>
                    <th scope="col">Country</th>
                    <td><?php echo emptytextdashShow($countrylist[$vendordetail['billing_country']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['billing_state']!="NA"){
                ?>
                <tr>
                    <th scope="col">State</th>
                    <td><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['billing_city']!="NA"){
                ?>
                <tr>
                    <th scope="col">City/Town</th>
                    <td><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['billing_post_code']!="NA"){
                ?>
                <tr>
                    <th scope="col">Pincode</th>
                    <td><?php echo emptytextdashShow($vendordetail['billing_post_code']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['contact_person']!="NA"){
                ?>
                <tr>
                    <th scope="col">Contact Person</th>
                    <td><?php echo emptytextdashShow($vendordetail['contact_person']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['mobile']!="NA"){
                ?>
                <tr>
                    <th scope="col">Mobile</th>
                    <td><?php echo emptytextdashShow($vendordetail['mobile']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['shipping_address']!="NA"){
                ?>
                <tr style="background-color: #e8e9ff;">
                    <th scope="col">Shipping Adderss</th>
                    <td><?php if($vendordetail['shipping_address']!=""){ echo trim($vendordetail['shipping_address'])."-".$vendordetail['shipping_city'];
}else{ echo "-";
} ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['shipping_country']!="NA"){
                ?>
                <tr>
                    <th scope="col">Country</th>
                    <td><?php echo emptytextdashShow($countrylist[$vendordetail['shipping_country']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['shipping_state']!="NA"){
                ?>
                <tr>
                    <th scope="col">State</th>
                    <td><?php echo emptytextdashShow($stateslist[$vendordetail['shipping_state']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['shipping_city']!="NA"){
                ?>
                <tr>
                    <th scope="col">City/Town</th>
                    <td><?php echo emptytextdashShow($citieslist[$vendordetail['shipping_city']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['shipping_post_code']!="NA"){
                ?>
                <tr>
                    <th scope="col">Pincode</th>
                    <td><?php echo emptytextdashShow($vendordetail['shipping_post_code']); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php     $fieldaccesright = $vafield['company_information'];
                if($fieldaccesright['access'] !="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Company Information</h3></div>
            <table class="table table-bordered mt-3 w-100">
                <?php
                    if($fieldaccesright['field']['vendor_tax_reg_no']!="NA"){
                ?>
                <tr>
                    <th scope="col" style="width: 40%;">Tax/Vat Reg.No.</th>
                    <td style="width: 60%;"><?php echo emptytextdashShow($vendordetail['vendor_tax_reg_no']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['business_type']!="NA"){
                ?>
                <tr>
                    <th scope="col">Legal Structure</th>
                    <td><?php echo emptytextdashShow($company_types[$vendordetail['business_type']]['company_type_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['duns_no']!="NA"){
                ?>
                <tr>
                    <th scope="col">DUNS No.</th>
                    <td><?php echo emptytextdashShow($vendordetail['duns_no']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['company_registration_number']!="NA"){
                ?>
                <tr>
                    <th scope="col">Company Registration Number</th>
                    <td><?php echo emptytextdashShow($vendordetail['company_registration_number']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['company_registration_country']!="NA"){
                ?>
                <tr>
                    <th scope="col">Company Registration Country</th>
                    <td style="font-size:13px; border:1px solid #eeeeee; padding: 10px 20px;"><?php echo emptytextdashShow($countrylist[$vendordetail['company_registration_country']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['payment_terms']!="NA"){
                ?>
                <tr>
                    <th scope="col">Payment Terms</th>
                    <td><?php echo emptytextdashShow($payment_terms[$vendordetail['payment_terms']]['payment_term_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['incoterm_id']!="NA"){
                ?>
                <tr>
                    <th scope="col">ENCO Terms/ Shipping Terms</th>
                    <td><?php echo emptytextdashShow($incoterms[$vendordetail['incoterm_id']]['incoterm_name']); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php     $fieldaccesright = $vafield['bank_information'];
                if($fieldaccesright['access'] !="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Banking Info</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <?php
                    if($fieldaccesright['field']['bank_name']!="NA"){
                ?>
                <tr>
                    <th scope="col" style="width: 40%;">Bank Name</th>
                    <td style="width: 60%;"><?php echo emptytextdashShow($vendordetail['bank_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_address']!="NA"){
                ?>
                <tr>
                    <th scope="col">Bank Address</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_address']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_country']!="NA"){
                ?>
                <tr>
                    <th scope="col">Country</th>
                    <td><?php echo emptytextdashShow($countrylist[$vendordetail['bank_country']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_state']!="NA"){
                ?>
                <tr>
                    <th scope="col">State</th>
                    <td><?php echo emptytextdashShow($stateslist[$vendordetail['bank_state']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_city']!="NA"){
                ?>
                <tr>
                    <th scope="col">City/Town</th>
                    <td><?php echo emptytextdashShow($citieslist[$vendordetail['bank_city']]); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_post_code']!="NA"){
                ?>
                <tr>
                    <th scope="col">Pincode</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_post_code']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_beneficiary_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">Company Bank Beneficiary Name</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_beneficiary_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_account_no']!="NA"){
                ?>
                <tr>
                    <th scope="col">Bank Account Number</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_account_no']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['account_type']!="NA"){
                ?>
                <tr>
                    <th scope="col">Type Of Bank Account</th>
                    <td><?php echo $bank_acoount_type[$vendordetail['account_type']]; ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['routing_aba_dfi_ach']!="NA"){
                ?>
                <tr>
                    <th scope="col">Routing-ABA/DFI/ACH</th>
                    <td><?php echo emptytextdashShow($vendordetail['routing_aba_dfi_ach']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['routing_wire_Code']!="NA"){
                ?>
                <tr>
                    <th scope="col">Routing Wire Code</th>
                    <td><?php echo emptytextdashShow($vendordetail['routing_wire_Code']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['swift_bic_code']!="NA"){
                ?>
                <tr>
                    <th scope="col">SWIFT/BIC Code</th>
                    <td><?php echo emptytextdashShow($vendordetail['swift_bic_code']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['iban_code']!="NA"){
                ?>
                <tr>
                    <th scope="col">IBAN</th>
                    <td><?php echo emptytextdashShow($vendordetail['iban_code']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_query_person_name']!="NA"){
                ?>
                <tr>
                    <th scope="col">Contact Person Name (Bank related queries)</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_query_person_name']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_query_contact_no']!="NA"){
                ?>
                <tr>
                    <th scope="col">Contact no</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_query_contact_no']); ?></td>
                </tr>
                <?php
                   }
                   if($fieldaccesright['field']['bank_query_email_id']!="NA"){
                ?>
                <tr>
                    <th scope="col">Contact Email</th>
                    <td><?php echo emptytextdashShow($vendordetail['bank_query_email_id']); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php
            $fieldaccesright = $vafield['bank_information'];
            if($fieldaccesright['field']['bank_name']!="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Other Contact Info</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <tr>
                    <th scope="col" style="text-align: left;">Type</th>
                    <th scope="col" style="text-align: left;">Name</th>
                    <th scope="col" style="text-align: left;">Number</th>
                    <th scope="col" style="text-align: left;">Email ID</th>
                </tr>
                <?php foreach($vendor_contact_info_type as $vcikey=>$vcival){ ?>
                    <tr>
                        <td>
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
            </table>
        </div>
        <?php } ?>
        <?php
            $fieldaccesright = $vafield['note'];
            if($fieldaccesright['access'] !="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Note</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <tr>
                <?php if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
                    <th scope="col" style="text-align: left; width: 40%;">PO Notes</th>
                <?php }
if($fieldaccesright['field']['vendor_other_notes']!="NA"){ ?>
                    <th scope="col" style="text-align: left; width: 60%;">Other Notes</th>
                <?php } ?>
                </tr>
                <tr>
                <?php if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
                    <td style="font-size:13px; border:1px solid #eeeeee; padding: 10px 20px;"><?php echo emptytextdashShow($vendordetail['vendor_po_notes']); ?></td>
                <?php }
if($fieldaccesright['field']['vendor_other_notes']!="NA"){ ?>
                    <td><?php echo emptytextdashShow($vendordetail['vendor_other_notes']); ?></td>
                <?php } ?>
                </tr>
            </table>
        </div>
        <?php } ?>
        <?php
            $fieldaccesright = $vafield['other_information'];
            if($fieldaccesright['access'] !="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Other Info</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <?php if($fieldaccesright['field']['vendor_type']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left; width: 40%;">Vendor Type</th>
                    <td style="width: 60%;"><?php echo $vendor_type[$vendordetail['vendor_type']]; ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['title']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Title</th>
                    <td><?php echo emptytextdashShow($vendordetail['title']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['suffix']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Suffix</th>
                    <td><?php echo emptytextdashShow($vendordetail['suffix']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['gsttype']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">GST Registration type</th>
                    <td><?php echo emptytextdashShow($vendordetail['gsttype']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['gstin']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">GSTIN</th>
                    <td><?php echo emptytextdashShow($vendordetail['gstin']); ?></td>
                </tr>
                <td><?php echo emptytextdashShow($vendorslist[$vendordetail['parent_vendor']]['display_name']); ?></td>
                <?php }
if($fieldaccesright['field']['vendor_po_notes']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Parent Vendor</th>
                    <td><?php echo emptytextdashShow($vendorslist[$vendordetail['parent_vendor']]['display_name']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['cst_reg_no']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">CST Reg.No</th>
                    <td><?php echo emptytextdashShow($vendordetail['cst_reg_no']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['pan_no']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">PAN No.</th>
                    <td><?php echo emptytextdashShow($vendordetail['pan_no']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['tds_apply']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Apply TDS for this vendor</th>
                    <td><?php echo $vendordetail['tds_apply'] == "0"?"Yes":"No";?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['pref_payment_method']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Preferred Payment Method</th>
                    <td><?php echo emptytextdashShow($payment_method[$vendordetail['pref_payment_method']]); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['preferred_deliver_method']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Preferred Delivery Method</th>
                    <td><?php echo emptytextdashShow($vendordetail['preferred_deliver_method']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['opening_balance']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Opening Balance</th>
                    <td><?php echo $vendordetail['opening_balance']; ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['asof']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">As of</th>
                    <td><?php echo fromDate($vendordetail['asof']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['pay_with_me']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">The Vendor Pays Me With</th>
                    <td><?php echo emptytextdashShow($vendordetail['pay_with_me']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['credit_limit']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Credit Limit</th>
                    <td><?php echo emptytextdashShow($vendordetail['credit_limit']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['default_discount_perc']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Default Discount Percentage (%)</th>
                    <td><?php echo emptytextdashShow($vendordetail['default_discount_perc']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['payment_discount_perc']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Payment Discount Percentage (%)</th>
                    <td><?php echo emptytextdashShow($vendordetail['payment_discount_perc']); ?></td>
                </tr>
                <?php }
if($fieldaccesright['field']['sales_type']!="NA"){ ?>
                <tr>
                    <th scope="col" style="text-align: left;">Sales Type</th>
                    <td><?php echo emptytextdashShow($salestype[$vendordetail['sales_type']]); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php
            $fieldaccesright = $vafield['questions'];
            if($fieldaccesright['field']['questions']!="NA"){
        ?>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Questions</h3></div>
            <?php if($vendordetail['vendor_type_id'] == '1' || $vendordetail['vendor_type_id'] ==""){ //3 party show only ?>
            <div class="mt-3"><h4>Vendor Related Questions:-</h4></div>
            <table class="table table-bordered w-100 mt-3">
                <tr>
                    <th scope="col" style="text-align: left;">Previously have you or any of your subsidiaries ever associated with Microchip (USA or any other region)?</th>
                    <td><?php echo answerYesOrNo($vendor_questions['radio_associate']); ?></td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">Please provide details</th>
                    <td><?php echo $vendor_questions['associated_team_detail']; ?> </td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">Does your company has a quality system ?(If Answer is Yes Then attachs the Quality Certificates)</th>
                    <td><?php echo answerYesOrNo($vendor_questions['quality_system']); ?></td>
                </tr>
            </table>
            <table class="table table-bordered w-100 mt-3 <?php echo $vendor_questions['quality_system'] =="Y"?"":"d-none"; ?>">
                <tr>
                    <th scope="col" style="text-align: left;"></th>
                    <th scope="col" style="text-align: left;">Certificate Name</th>
                    <th scope="col" style="text-align: left;">Effective From Date</th>
                    <th scope="col" style="text-align: left;">Effective To Date</th>
                </tr>
                <tr>
                    <th scope="col">1</th>
                    <td><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['1']); ?></td>
                    <td><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['1']); ?></td>
                    <td><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['1']); ?></td>
                </tr>
                <tr>
                    <th scope="col">2</th>
                    <td><?php echo emptytextdashShow($vendor_questions['quality_system_certificate']['name']['2']); ?></td>
                    <td><?php echo fromDate($vendor_questions['quality_system_certificate']['fromdate']['2']); ?></td>
                    <td><?php echo fromDate($vendor_questions['quality_system_certificate']['todate']['2']); ?></td>
                </tr>
            </table>
            <div class="mt-3"><h4>Small Businesses Registerd:-</h4></div>
            <?php $smallbussinesary = array('1'=>'Self Certified','2'=>'Sam Govt Registered','3'=>'Small Disadvantage','4'=>'Women Owned','5'=>'Veteran Owned','6'=>'Service - Disabled Veteran','7'=>'Small Business');
            $mk= 1; foreach($smallbussinesary as $keybuss=>$smallbus){
                if($vendor_questions['small_business_registerd'][$keybuss] =="Y"){
            ?>
            <div><b><?php echo $mk; ?>.</b> <?php echo $smallbus; ?></div>
        <?php $mk++; ?>
        <?php } ?>
        <?php } ?>

        </div>
        <div class="w-100">
            <div><b>Others: </b> <?php echo emptytextdashShow($vendor_questions['small_business_registerd']['10']); ?></div>
            <div><b>Unique Entity ID: </b> <?php echo emptytextdashShow($vendor_questions['small_business_registerd']['3']); ?></div>
            <div><b>Cage Code: </b> <?php echo emptytextdashShow($vendor_questions['small_business_registerd']['4']); ?></div>
        </div>
        <?php } //3 party show only ?>
        <div class="w-100 mt-3">
            <table class="table table-bordered w-100 mt-3">
                <tr>
                    <th scope="col" style="text-align: left;">Supply Chain Responsibility:-</th>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">1. Is Your Company a Member of RBA ( Responsible Business Alliance)</th>
                    <td> <?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['1']); ?></td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">2. Are all employees of your company atleast 18 years of age</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['2']); ?></td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">3. Does Your Company has 1 Employee? If "Yes" Skip To No. 7 If "No" Go To Question No. 4</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['3']); ?></td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">4. Do you have hourly employees that works at manufacturer facility? For the purpose of this form, hourly employees are employees that are subject to local laws governing overtime pay or subject to hourly increased or decreased in compensation based on volume of product if Yes (Please answer a, b and c, if No then skip to Question no 5)</th>
                    <td style="font-size:13px; color: #000000; padding: 10px 20px; border: 1px solid #eeeeee;"><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4']); ?></td>
                </tr>
                <tr <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"class='d-none'"; ?>>
                    <th scope="col" style="text-align: left;">a) Do all hourly workers work less than 60 hours per week?</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']); ?></td>
                </tr>
                <tr <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"class='d-none'"; ?>>
                    <th scope="col" style="text-align: left;">b) Do you have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor?</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_b']); ?></td>
                </tr>
                <tr <?php echo $vendor_questions['supply_Chain_responsibility'][4] =="Y"?"":"class='d-none'"; ?>>
                    <th scope="col" style="text-align: left;">c) Do you expect your supplier to have a policy prohibiting forced, involuntory, indentured, trafficked, or slave labor</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_c']); ?></td>
                </tr>

                <tr <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="")?"":"class='d-none'"; ?>>
                    <th scope="col" style="text-align: left;">5. Do any employee have to reimburse recruitment fee to your company?</th>
                    <td>Yes</td>
                </tr>

                <tr <?php echo ($vendor_questions['supply_Chain_responsibility'][4] =="N" || $vendor_questions['supply_Chain_responsibility'][4] =="" && $vendor_questions['supply_Chain_responsibility'][5] =="Y")?"":"class='d-none"; ?>>
                    <th scope="col" style="text-align: left;">a) Do you return original passport or other original personal identification documents upon the employee's request?</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['4_a']); ?></td>
                </tr>

                <tr>
                    <th scope="col" style="text-align: left;">6. Do any employee have to reimburse recruitment fee to your company?</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['6']); ?></td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;">7. Is your company (or your sub-contractor) in Xinjiang Uygur Autonomous Regision (XUAR)</th>
                    <td><?php echo answerYesOrNo($vendor_questions['supply_Chain_responsibility']['7']); ?></td>
                </tr>
            </table>
        </div>
        <div class="w-100 mt-3">
            <div class="bg-gray"><h3 class="m-0">Terms & Conditions:-</h3></div>
            <table class="table table-bordered w-100 mt-3">
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>1.</b> Providing information in the "Vendor Information Form" does not constitute acceptance as an approved & registered vendor. The registration of vendor is the sole discretion of AM/NS India.</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>2.</b> Failure to enter all of the mandatory information will not enable us to post your vendor file to our database..</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>3.</b> Being a registered vendor does NOT guarantee the receipt of notification for all applicable solicitations or invitation to offer or the award of any purchase contract or award of any invitation for bid or request for proposal or request for qualifications.</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>4.</b> Vendor is responsible for the content of the information and its vendors responsibility to keep all information current and provide any subsequent up dates, additional information as and when required and when desired by AM/NS India.</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>5.</b> AM/NS India reserves the rights to evaluate the vendor on periodical basis and decide the continuation / discontinuation of business with them based on the evaluation report.</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>6.</b> Representative of AM/NS India OR their nominated agency has the right to visit vendors premises for the verification of information with a prior intimation to them.</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="text-align: left;"><b>7.</b> At later date if it is found that the information furnished is untrue, inaccurate, not current, or is incomplete, we reserve the right to suspend or terminate the registration and recover the dues (if any) from you</th>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left; width: 90%;"><?php echo $vendor_questions['terms_conditions']['1'] =="Y"?"<i class='ti-check-box'></i>":""; ?> I / We have read the terms & conditions and privacy policy.<br />
                    I/We hereby declare my / our acceptance for the same and certify that the information submitted in this vendor registration application is true and correct to the best of my/our knowledge.<br />
                    I / We further understand that any false statements observed at the later stage may result in denial or revocation of our vendor registration with AM/NS India group.</th>
                    <td style="width: 10%;">Yes</td>
                </tr>
                <tr>
                    <th scope="col" style="text-align: left;"><?php echo $vendor_questions['terms_conditions']['2'] =="Y"?"<i class='ti-check-box'></i>":""; ?> I / We agree, covenant and undertake that I / We have reviewed the policies of AMNSIL.<br />
                    I / We will comply with the principles contained in these Policies and these Policies will continue to be applicable and binding on the me/us, to the extent relevant.</th>
                    <td>Yes</td>
                </tr>
            </table>
        </div>
    <?php } ?>
    </div>
</div>
<div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>


</div>
