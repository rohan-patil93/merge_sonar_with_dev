<?php
if($_POST['viewpost'] =="Y"){ // Normal popup open time call
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $callapi = "Y";
}else{
    include_once 'dbconfig.php';
}
if($_POST['viewpost'] =='Y'){ // Normal popup open time call
    $url = API."?mode=getvendorregistrationrequests&rid=".$vrrid."&country_id=".$_POST['country_id']."&viewpage=popup";
    $apicall = callAPI($url);
    $result = json_decode($apicall,1);
    $vendor_legal_application_users =  $result['vendor_legal_application_users'];
    $countries = $result['countries'];
    $states = $result['states'];
    $config = $result['config'];
    $vendorrequest =  $_POST;
    $registration_questions = $_POST['registration_questions'];

}else{
    $vendorrequest = $_POST['vendor_registration_requests'];
    $vendor_legal_application_users =  $_POST['vendor_legal_application_users'];
    $countries = $_POST['countries'];
    $states = $_POST['states'];
    $config = $_POST['config'];
    $registration_questions = json_decode($vendorrequest['registration_questions'],1);
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
if($_POST['viewpost'] =="Y"){
?>
<script type="text/javascript">
function printDiv(divId) {
       var printContents = $("#" + divId).html();
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html lang="en"><head><title>Vendor Registration Request</title></head><body>' + printContents + '</body></html>');
    printWindow.document.close(); // close the document stream
    printWindow.print();
    printWindow.onafterprint = function() {
        printWindow.close();
    };
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
<div class="modal-header">
    <h5 class="modal-title"> Vendor Registration Request Details </h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<?php } ?>
<div class="modal-body" id="printDiv_popup">
    <link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendor_registration_request_popup.css">
    <div class="body">
        <div class="w-100">
            <div class="f-24 text-dark bg-gray"><h3 class="mb-0">Vendor Registration Request</h3></div>
        </div>
        <div class="w-100 mt-3" style="width: 100%; margin-top: 15px;">
            <table class="table table-bordered mt-3 f-14">
                <tr>
                    <th scope="col" class="w-b-w-s" style="width: 60%;">Company Name</td>
                    <td class="w-b-w-s" style="width: 40%;"><?php echo $vendorrequest['company_name']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" class="">Company Website</th>
                    <td class="w-b-w-s" class=""><?php echo $vendorrequest['web_site']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Company Facsimile Number</th>
                    <td class="w-b-w-s"><?php echo $vendorrequest['facsimile_number']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Organization Country</th>
                    <td class="w-b-w-s"><?php echo $countries[$vendorrequest['country_id']]['country_name']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Organization State</th>
                    <td class="w-b-w-s"><?php echo $states[$vendorrequest['state_id']]['state_name']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Legal Connect</th>
                    <td class="w-b-w-s"><?php echo ucwords($vendor_legal_application_users[$vendorrequest['legal_connect']]['display_name']); ?> </td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Contact Person First Name</th>
                    <td><?php echo $vendorrequest['first_name']; ?></td>
                </tr>
                <tr>
                    <th scope="col">Contact Person Last Name</th>
                    <td class="w-b-w-s"><?php echo $vendorrequest['last_name']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Contact Person Email Id</th>
                    <td class="w-b-w-s"><?php echo $vendorrequest['user_email']; ?></td>
                </tr>
                <tr >
                    <th scope="col" class="w-b-w-s">Contact Person Mobile No.</th>
                    <td class="w-b-w-s"><?php echo $vendorrequest['user_mobile']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s">Note</th>
                    <td class="w-b-w-s"><?php echo $vendorrequest['note']; ?></td>
                </tr>
            </table>
        </div>

        <div class="w-100 mt-3">
            <div class="f-24 text-dark bg-gray"><h3 class="mb-0">Vendor Related Questions:-</h3></div>
            <table class="table table-bordered mt-3 f-14">
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left; width: 70%;">I have read <?php echo ucwords($config['name']); ?></th>
                    <td class="w-b-w-s" style="width: 30%;"><?php echo answerYesOrNo($registration_questions['read_policy']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">1. The Consulting Company has been proposed on the basis that it can provide professional services solely for legitimate business purposes.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">2. To the best of my knowledge, the Consulting Company has a good business and ethical reputation in the country(ies) in which the Consulting Company will provide services to <?php echo ucwords($config['name']); ?>.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">3. The Consulting Company has not promised, offered, or given to me, or to anyone else that I am aware of at <?php echo ucwords($config['name']); ?>, any kind of gift, money or anything else of value, in exchange for proposing it as a Consulting Company.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">4. I have asked the proposed company and to the best of my knowledge, the Consulting Company is not currently suspended or debarred by any government in any part of the world from doing business.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">5. For engagements with a Consulting Company who supports accounts outside the U.S., I confirm that I have taken the Compliance Training within the last 12 months.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" colspan="2" style="text-align: left;">6. I agree to promptly notify (1) my local legal representative or (2) contact <?php echo $config['admin_email']; ?> of any information that would conflict with any of the points addressed in this certification or if any other concerns are later raised regarding the Consulting Company’s activities or qualifications.</th>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">7. I have reviewed and agree that the existing Preferred Consulting Company able to carry out the proposed consulting services?</th>
                    <td class="w-b-w-s"><?php echo answerYesOrNo($registration_questions['questions'][1]); ?></td>
                </tr>
                <tr <?php echo $registration_questions['questions'][1] =="Y"?"":"d-none"; ?>>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">a. I If YES what are the specific business justifications for entering into this Consulting Company relationship?</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['questions']['Y']; ?></td>
                </tr>
                <tr <?php echo $registration_questions['questions'][1] =="N"?"":"d-none"; ?>>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">a.  If NO what were the main reasons why, during Consulting Company Selection, you could not find an existing Consulting Company partner that fits your need: For example, geographical, customer requirement, security clearance, technical skills, etc.</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['questions']['N']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">8. Describe the nature of services and deliverables that the proposed Consulting Company will provide. Please include as much detail as possible, including any technology details or skills sets. Also, please go to the Technology List table at the foot of this document and check each and every Technology Area that you recommend this Consulting Company is capable of:</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['questions']['8']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">9. What were the selection criteria that helped you choose this particular Consulting Company?</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['questions']['9']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">I Agree</th>
                    <td class="w-b-w-s"><?php echo answerYesOrNo($registration_questions['aggree']['check']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Name</th>
                    <td class="w-b-w-s"><?php echo ucwords($registration_questions['aggree']['name']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Date</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['aggree']['date']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Business Unit</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['aggree']['business_unit']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Business Unit Vice President</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['aggree']['business_unit_vice_president']; ?></td>
                </tr>
            </table>

        </div>
        <div class="w-100 mt-3">
            <div class="f-24 text-dark bg-gray"><h3 class="mb-0">Commercial Terms:-</h3></div>
            <p class="m-l-10 mt-2">What are the Consulting Company’s indicative day rates for services (paste list if applicable)?</p>
            <table class="table table-bordered mt-3 f-14">
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left; width: 70%;">US$ / INR per day</th>
                    <td class="w-b-w-s" style="width: 30%;"><?php echo $registration_questions['incoterms']['inr_per_day']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Total Spend for this project</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['incoterms']['total_spend_project']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Will you use this Consulting Company for another project</th>
                    <td class="w-b-w-s"><?php echo answerYesOrNo($registration_questions['incoterms']['another_project']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Total Spend in next 12 months</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['incoterms']['total_spend_12_month']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Where will the services be performed</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['incoterms']['services_be_performed']; ?></td>
                </tr>
            </table>
        </div>
        <div class="w-100 mt-3">
            <div class="f-24 text-dark bg-gray"><h6 class="mb-0 f-w-600">Consulting Company's Notices Contact (person to receive all official notices under the Consulting Agreement):-</h6></div>
            <table class="table table-bordered mt-3 f-14">
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left; width: 70%;">Name or Title</th>
                    <td class="w-b-w-s" style="width: 30%;"><?php echo ucwords($registration_questions['official_notices']['name_title']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Mailing Address</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['official_notices']['address']; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Facsimile Number</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['official_notices']['facsimile_number']; ?></td>
                </tr>
            </table>
        </div>
        <div class="w-100 mt-3">
            <div class="f-24 text-dark bg-gray"><h6 class="mb-0 f-w-600">Consulting Company Contact with authority to sign the Consulting Agreement on behalf of the Consulting Company:-</h6></div>
            <table class="table table-bordered mt-3 f-14">
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left; width: 70%;">Name</th>
                    <td class="w-b-w-s" style="width: 30%;"><?php echo ucwords($registration_questions['authority_sign_behalf']['name']); ?></td>
                </tr>
                <tr>
                    <th scope="col" class="w-b-w-s" style="text-align: left;">Email Address</th>
                    <td class="w-b-w-s"><?php echo $registration_questions['authority_sign_behalf']['email']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php if($_POST['viewpost'] =="Y"){ ?>
<div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-danger waves-effect waves-light submittopupproductrfrom"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="">Submit</button>


</div>
<?php } ?>
