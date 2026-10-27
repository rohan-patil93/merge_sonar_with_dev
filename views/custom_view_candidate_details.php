<!DOCTYPE html>
<html lang="en">
<head>
    <title>Candidate Form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <style type="text/css">
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        td, th{font-size: 12px;}
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
           document.body.innerHTML = "<html><head><title>Candidate Details</title></head><body>" + printContents + "</body>";
           window.print();
           document.body.innerHTML = originalContents;
        }
    function convertHTMLToPDF() {
            var htmlContent = $('#printDiv').html();
            $.ajax({
                url: 'dynamic_convert_html_to_pdf.php',
                type: 'POST',
                data: { html_content: htmlContent },
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
                        url: 'dynamic_convert_html_to_pdf.php',
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
<?php
    $rid = $_GET['Rid'];
    $table = $_GET['table'];
    $api = API."?mode=getcandidatedetails&Rid=".$rid;
    $apicall = callAPI($api);
    $alldata = json_decode($apicall, 1);
    $data = $alldata['candidates'];
    $configurl = API."?mode=singlerowtabledata&fid=company_id&rid=1&table=config";
    $configesponse = callAPI($configurl);
    $configdata = json_decode($configesponse,1);
    foreach($alldata['genders'] as $gen){
        $genders[$gen['genc_detail_code']] = $gen['genc_detail_name'];
    }
    foreach($alldata['maritalstatus'] as $gen){
        $maritalstatus[$gen['id']] = $gen['marital_status_name'];
    }
    foreach($alldata['religions'] as $gen){
        $religions[$gen['id']] = $gen['religion_name'];
    }

    foreach($alldata['blood_group'] as $gen){
        $blood_group[$gen['id']] = $gen['blood_group'];
    }
    foreach($alldata['relations'] as $relation){
        $relations[$relation['id']] = $relation['relation_name'];
    }

    foreach($alldata['educationtype'] as $gen){
        $educationtype[$gen['genc_detail_code']] = $gen['genc_detail_name'];
    }
    foreach($alldata['religions'] as $maritalstatus1){
  $religions[$maritalstatus1['id']] = $maritalstatus1['religion_name'];
}
foreach($alldata['castes'] as $maritalstatus1){
  $castes[$maritalstatus1['id']] = $maritalstatus1['caste_name'];
}

    $_SESSION['pdf_file_name'] = "Candidate";
?>
<body style="margin: 0;font-family: Arial;font-size: 10px;">
    <div id='printDiv' style="padding: 20px;">
    <div class="container-fluid">
        <table class="" style="width:100%;border-collapse : collapse;">
            <tr>
                <th scope="col">
                    <img style="width:100px;" src="uploads/all/<?php echo $configdata['display_logo']; ?>" alt="Logo">
                </th>
                <th scope="col">
                    <h3 style="font-size: 20px; text-align: center; text-transform: uppercase; margin: 0;"><?php echo $_SESSION['config']['name']; ?></h2>
                    <p style="font-size:14px; text-align: center; margin: 10px 0 0 0;">(An ISO 900-120-5 Certified Outsourcing Company)</p>
                </th>
                <th scope="col" style="text-align: right;"></th>
            </tr>
        </table>
        <table class="" style="width:100%; margin: 20px 0 0 0;border-collapse : collapse;">
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size: 10px; text-align: left;">Candidate Name</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><?php echo $data['first_name']; ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><?php echo $data['middle_name']; ?> </td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><?php echo $data['last_name']; ?></td>
                <th scope="col" rowspan="5" style="border: 1px solid #cccccc;">
                    <?php if($data['image']!=""){ ?>
                        <img style="width:150px;" src="uploads/<?php echo $_SESSION['DB_NAME']; ?>/1/candidates/<?php echo $rid; ?>/<?php echo $data['image']; ?>" alt="Attachment Preview">
                    <?php }else{?>
                    <img style="width:150px;" src="uploads2/dummy.jpg" alt="Attachment Preview">
                    <?php } ?>
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Father Name</th>
                <td colspan="4" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><?php echo $data['father_name']; ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Date of Birth</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo date("d M Y", strtotime($data['date_of_birth']));  ?></td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Age</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo dateyearcalucates($data['date_of_birth']); ?></td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Sex</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $genders[$data['gender']]; ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Religion</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $religions[$data['religion']]; ?></td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Caste</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <?php echo $castes[$data['caste']]; ?>
                </td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Present House</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"></td>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Marital Status</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $maritalstatus[$data['marital_status']]; ?></td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Identification Mark</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <?php echo $data['identification_mark']; ?>
                </td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Blood group</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $blood_group[$data['blood_group']]; ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Hobbies</th>
                <td colspan="6" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">
                     <?php echo $data['hobbies']; ?>
                </td>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">Permanent Address</th>
                <th scope="col" colspan="4" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">Present Address</th>
            </tr>
            <tr>
                <th scope="col" colspan="3" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><p style="min-height: 50px;">
                    <?php echo $data['address']; ?>
                </p></th>
                <th scope="col" colspan="4" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;"><p style="min-height: 50px;">
                    <?php echo $data['present_address']; ?>
                </p></th>
            </tr>
            <tr>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">E-mail ID</th>
                <td colspan="5" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $data['email_id']; ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Contact Number</th>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $data['phone']; ?></td>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Emergency Contact Number</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <?php echo $data['emergency_contact_number']; ?>
                </td>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Height</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <?php echo $data['height']; ?>
                </td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Weight</th>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <?php echo $data['weight']; ?>
                </td>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">State</th>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">-</td>
            </tr>
            <tr style="background-color: #eeeeee;">
                <th scope="col" colspan="7" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">Family Details</th>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Name</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Relation</th>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Age</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Occupation</th>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Monthly Income</th>
            </tr>

            <?php $famies = json_decode($data['employee_dependents'], 1);
                foreach($famies  as $fam){
            ?>
            <tr>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $fam['dependent_name'];  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $relations[$fam['dependent_relationship_type']];  ?></td>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo dateyearcalucates($fam['dependent_date_of_birth']);  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $fam['occupation'];  ?></td>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $fam['income'];  ?></td>
            </tr>
            <?php } ?>
            <tr style="background-color: #eeeeee;">
                <th scope="col" colspan="7" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">Educational Qualification</th>
            </tr>
            <tr>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Qualification</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Name of School/College/University/Institution</th>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Score</th>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Year Of</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Subject taken</th>
            </tr>
            <?php $quali = json_decode($data['candidate_qualifications'], 1);
                foreach($quali  as $qual){
            ?>
            <tr>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $educationtype[$qual['education_type']];  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $qual['institute_name'];  ?></td>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $qual['score'];  ?></td>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $qual['end_date'];  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $qual['subject'];  ?></td>
            </tr>
             <?php } ?>


            <tr style="background-color: #eeeeee;">
                <th scope="col" colspan="7" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">Experience if Any</th>
            </tr>
            <tr>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Name of Company</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">Designation</th>
                <th scope="col" colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">From</th>
                <th scope="col" colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">To</th>

            </tr>
            <?php $expe = json_decode($data['candidate_experiances'], 1);
                foreach($expe  as $exp){
            ?>
            <tr>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $exp['company_name'];  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $exp['job_descrption'];  ?></td>
                <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $exp['work_from'];  ?></td>
                <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;"><?php echo $exp['work_to'];  ?></td>
            </tr>
            <?php } ?>
            <tr style="background-color: #eeeeee;">
                <th scope="col" colspan="7" style="border: 1px solid #cccccc; padding: 5px;font-size:10px; text-align: left;">
                    <p style="margin: 0;">Declaration: I hereby declare  that the above furnished infomation is ture to the best of my knowledge and belief.</p><br />
                    <p style="margin: 0;">Date:</p>
                    <p style="width: 50%; float: left; margin: 10px 0 0 0;">Place:</p>
                    <p style="width: 50%; float: left; margin: 10px 0 0 0; text-align: right;">Signature of the Candidate</p>
                </th>
            </tr>
            <tr style="background-color: #eeeeee;">
                <th scope="col" colspan="7" style="border: 1px solid #cccccc; padding: 5px;font-size:10px;">
                    <p style="margin: 0; text-transform: uppercase; min-height: 80px;">For office use only</p>
                </th>
            </tr>
        </table>
    </div>
    </div>
    <div class="container-fluid clearfix d-print-none" style="margin-top: 5px;">
        <button class="btn waves-effect waves-light tn-grd-success" onclick="printDiv('printDiv');">
            <i class="fa fa-print"></i> &nbsp Print Invoice
        </button>
        <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
            <i class="fa fa-download"></i> &nbsp Download
        </button>
    </div>
</body>
</html>
