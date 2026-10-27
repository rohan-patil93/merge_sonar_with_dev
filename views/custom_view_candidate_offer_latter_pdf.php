<?php
if($_GET['print'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $callapi = "Y";
}else{
    include_once 'dbconfig.php';
}
if($_POST['callapi'] =='Y'){
    $config = $_POST['config'];
    $data  = $_POST['candidates'];
    $candiddata = $_POST['candidates'];
    $offer_details  = $_POST['offer_details'];
    $jobcodedata  = $_POST['job_titles'];
    $jobpaydata  = $_POST['job_grade_templates'];
    $relatypedata  = $_POST['letters'];
    $hrempdata  = $_POST['hrempdata'];
    $salarydata  = $_POST['salary_template'];
    $salarytmpdata  = $_POST['salary_template_components'];
    $salarycatdata  = $_POST['salary_categories'];
    $candhistrydata  = $_POST['candidate_offer_histories'];
    $emailres  = $_POST['email_content'];
    $admin_application_users = $_POST['admin_application_users'];
    $rm_application_users = $_POST['rm_application_users'];
    foreach ($offer_details as  $offer_detail) {
        if($offer_detail['parent_id']=='0'){
            $catdetails[$offer_detail['category_id']] = $offer_detail;
        }else{
            $subcatdetails[$offer_detail['parent_id']][] = $offer_detail;
        }
    }
    $callapi = "Y";
}else{
   $config = $return['config'];

}
function customeummarmonyformate1($number,$config){
    if($number !=""){
        $expoldform = explode('.',$config['number_format']);
        if($expoldform[0]== 1){
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            $formattedNumber = $formatter->formatCurrency($number, 'INR');
             return $formattedNumber;
        }else{
             return number_format($number, $expoldform[1], '.', ',');
        }
    }
}
function emaildatareplaceNew1($data,$val){
    $inputFildsArray = $inputFildsArray ?? '';
    foreach($data as $fieldkey=>$result1){
        $inputFildsArray["{".trim($fieldkey)."}"] = trim($result1);
    }
    $str = "";
    foreach($inputFildsArray as $key => $value){
        $repl =  str_replace(trim($key), trim($value), trim($val));
        $val = $repl;
    }
    return $val;
}
?>
<?php if($callapi !='Y'){ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Candidate Offer Letter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <style type="text/css">
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        td, th{

            font-size: 12px;
        }
        @media (max-width:772px){
            .scan{width: 36%;}
        }
        table{
            border-collapse : collapse;
        }
    </style>
    <script type="text/javascript">
        function printDiv(divId) {
           var printContents = document.getElementById(divId).innerHTML;
           var originalContents = document.body.innerHTML;
           document.body.innerHTML = "<html><head><title>Candidate Offer Letter</title></head><body>" + printContents + "</body>";
           window.print();
           document.body.innerHTML = originalContents;
        }
        function convertHTMLToPDF() {
            var  title ='Offer Latter';
            var htmlContent = $('#printDiv').html();
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
</head>
<body style="margin: 0;padding: 0;font-family: Arial;font-size: 12px;">
    <?php } ?>
    <div id="printDiv" >
        <div style="word-break: break-word; white-space: initial;">

<?php
           $slarytempate = '<div  class="col-lg-12" style="page-break-before: always;">
                        <strong style="font-size: 14px; color: #000;  border-bottom: 2px solid #000; padding-bottom: 5px;">
                            COST TO COMPANY CALCULATIONS w.e.f '.date('M d, Y',strtotime($data['reporting_date'])).' </strong>
                            <div class="table-responsive">
                                <table style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px; margin-left: auto; margin-right: auto;" role="presentation">
                                    <tr style="height: 50px;border: 1px solid #000;">
                                        <td style="border:1px solid #000;padding: 5px;"><b>Name</b></td>
                                        <td  style="border:1px solid #000;padding: 5px;"colspan="2">&nbsp;'.$send_data['display_name'].'</td>
                                    </tr>
                                    <tr style="height: 50px;border: 1px solid #000;">
                                        <td style="border:1px solid #000;padding: 5px;"><b>Designation</b></td>
                                        <td style="border:1px solid #000;padding: 5px;" colspan="2">&nbsp;'.$send_data['job_title'].'</td>
                                    </tr>
                                    <tr style="border: 1px solid #000;">
                                        <td style="border:1px solid #000;padding: 5px;"><b>COMPENSATION DETAILS</b>                </td>
                                        <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>MONTHLY</b>        </td>
                                        <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>YEARLY</b>    </td>
                                    </tr>';
                                 $nm=0; foreach ($salarytmpdata as  $stvalue) {
                                             if($stvalue['component_cat_id'] !=$nm ){
                                            $nm =$stvalue['component_cat_id'];
                                    $slarytempate .= '<tr style="border: 1px solid #000;" >
                                        <td style="font-size: 14px;border:1px solid #000;padding: 5px;">&nbsp; '.$cat[$nm].'</td>
                                        <td style="border:1px solid #000;padding: 5px;"> </td>
                                        <td style="border:1px solid #000;padding: 5px;">  </td>
                                    </tr>';
                                 }
                                $slarytempate .= '<tr style="border: 1px solid #000;" >
                                        <td style="border:1px solid #000;padding: 5px;" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$stvalue['name'].'</td>
                                        <td style="text-align: center;border:1px solid #000;padding: 5px;">'.customeummarmonyformate1($stvalue['monthly_amount'],$config).'</td>
                                        <td style="text-align: center;border:1px solid #000;padding: 5px;">'.customeummarmonyformate1($stvalue['yearly_amount'],$config).'</td>
                                    </tr>';
                                }

                                $slarytempate .= '<tr style="border: 1px solid #000;" >
                                        <td colspan="3" style="border:1px solid #000;padding: 5px;">&nbsp;  <span class="d-block">Notes:</span>
                                            <p style="padding: 0px 5%;">'.$salarydata['note'].'</p>
                                        </td>

                                    </tr>
                                </table>
                            </div>
                    </div>';
                    $signature = '<img src="'.$data['signature'].'" alt="Preview" />';

          $replacearray = array("admin_company_name"=>$_SESSION['config']['name'],"admin_address"=>$_SESSION['config']['contact_address'],"admin_display_name"=>$admin_application_users['display_name'],"admin_display_name"=>$admin_application_users['display_name'],"date"=>date("d M Y"),"candidates.display_name"=>$data['display_name'],"candidates.job_title"=>$jobcodedata->job_title,"candidates.expected_ctc"=>$data['offer_ctc'],"candidates.reporting_date"=>date("d M Y",strtotime($data['reporting_date'])),"candidates.email_id"=>$data['email_id'],"candidates.offer_validity"=>date("d M Y",strtotime($data['offer_validity'])) ,"rm_display_name"=>$rm_application_users['display_name'],"rm_contact_number"=>$rm_application_users['user_mobile'],"rm_email_id"=>$rm_application_users['user_email'],'candidates.salary_structure_template'=>$slarytempate,"candidates.signature"=>$signature);

                        echo emaildatareplaceNew1($replacearray,$emailres['email_content_to']);
                        ?>
</div></div>

    <?php if($callapi !='Y'){ ?>


</body>
</html>
<?php } ?>
