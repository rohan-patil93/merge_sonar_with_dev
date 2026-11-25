<?php
if (!defined('STR_CHECK_ICON_CLASS')) {
    define('STR_CHECK_ICON_CLASS', ' text-primary fa-solid fa-square-check');
}
if(isset($_GET['Rid']) && $_GET['Rid'] != ''){
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}
$url = API . "?mode=getidcndcexitformdata&method=get&employee_id=" . $rid;
$response = callAPI($url);
$data = json_decode($response, true) ?: [];

$employee = $data['employee'] ?? [];
$termination = $data['termination'] ?? [];
$clearances = $data['clearances'] ?? [];
$signature = $data['signature'] ?? [];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-PPIZEGYM1v8zp5Py7UjFb79S58UeqCL9pYVnVPURKEqvioPROaVAJKKLzvH2rDnI" crossorigin="anonymous">
  <style type="text/css">
      @media (orientation: portrait) {
          .container {width: 100%; border: none; padding: 0;}
      }
      @print{
          .container{100%;padding:0;border:none;}
      }
      td, th{font-size: 13px; padding: 10px !important; vertical-align: middle;white-space: nowrap;}
      @media (max-width:772px){
          .scan{width: 36%;}
      }
      table{
          border-collapse : collapse;
      }
  </style>
<div class="page-body">
    <div class="card">
        <div class="card-body">


<div  id="printableArea">
    <table style="width:100%;border-collapse:collapse;border: 1px solid #cccccc;font-family: Tahoma;">

        <tr>
            <th scope="col" colspan="4" style="font-size: 30px; border: 1px solid #eeeeee; text-align: center;">
                <img src="images/logo.png" alt="<?= htmlspecialchars(__('Logo'), ENT_QUOTES, 'UTF-8'); ?>">
            </th>
        </tr>
        <tr>
            <th scope="col" colspan="4" style="font-size: 30px; border: 1px solid #eeeeee; text-align: center; white-space: normal;"><?= __('No due clearance form for termination / international or IDC location transfer'); ?></th>
        </tr>
        <tr>
            <th scope="col" colspan="4" style="font-size: 15px; border: 1px solid #eeeeee; text-align: left;"><?= __('Employment Type'); ?>:</th>
        </tr>
        <tr>
            <td colspan="4" style="font-size: 15px; border: 1px solid #eeeeee; text-align: left;">
                <span><i class="fa-regular fa-square<?php echo ($employee['employee_type']=='Full Time Employee')?STR_CHECK_ICON_CLASS:''; ?>"></i> <?= __('Full Time Employee'); ?></span>&nbsp;&nbsp;
                <span><i class="fa-regular fa-square<?php echo ($employee['employee_type']=='Intern')?STR_CHECK_ICON_CLASS:''; ?>"></i> <?= __('Intern'); ?></span>&nbsp;&nbsp;
                <span><i class="fa-regular fa-square<?php echo ($employee['employee_type']=='Direct Contractor')?STR_CHECK_ICON_CLASS:''; ?>"></i> <?= __('Direct Contractor'); ?></span>&nbsp;&nbsp;
                <span><i class="fa-regular fa-square<?php echo ($employee['employee_type'] == 'Third Party Contractor') ? STR_CHECK_ICON_CLASS : ''; ?>"></i> <?= __('Third Party Contractor'); ?></span>
            </td>
        </tr>

        <tr>
            <th scope="col"><?= __('Name (Full Name)'); ?></th>
            <td><?php echo htmlspecialchars($employee['display_name'] ?? '-'); ?></td>
            <th scope="col"><?= __('Employee ID'); ?>:</th>
            <td><?php echo htmlspecialchars($employee['emp_code'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Designation/Department'); ?></th>
            <td><?php echo htmlspecialchars( $employee['department_name'] ?? $employee['designation_id'] ?? '-'); ?></td>
            <th scope="col"><?= __('Business Unit'); ?></th>
            <td><?php echo htmlspecialchars($employee['vertical_id'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Cubical Number'); ?></th>
            <td><?php echo htmlspecialchars($employee['seat_location'] ?? '-'); ?></td>
            <th scope="col"><?= __('Date of Joining'); ?></th>
            <td><?php echo !empty($employee['joining_date']) ? date('d-m-Y', strtotime($employee['joining_date'])) : '-'; ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Date of Resignation'); ?></th>
            <td><?php echo !empty($termination['resignation_date']) ? date('d-m-Y', strtotime($termination['resignation_date'])) : '-'; ?></td>
            <th scope="col"><?= __('Last Working Date'); ?></th>
            <td><?php echo !empty($termination['leaving_date']) ? date('d-m-Y', strtotime($termination['leaving_date'])) : '-'; ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Address with pin code'); ?></th>
            <td><?php echo htmlspecialchars($employee['address'] ?? '-'); ?></td>
            <th scope="col"><?= htmlspecialchars(__('Bank A/C Number & IFSC code'), ENT_QUOTES, 'UTF-8'); ?></th>
            <td><?php echo renderSensitiveDataWithToggle($employee['bank_account_no'] ?? '', 'bank_account', false) . ' ' . renderSensitiveDataWithToggle($employee['ifsc_code'] ?? '', 'ifsc', false); ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Personal Email ID'); ?></th>
            <td><?php echo htmlspecialchars($employee['email_id'] ?? '-'); ?></td>
            <th scope="col"><?= __('Mobile Number'); ?></th>
            <td><?php echo renderSensitiveDataWithToggle($employee['phone'] ?? '', 'mobile', false); ?></td>
        </tr>
        <tr>
            <th scope="col" colspan="4"><?= __('Please select the appropriate process'); ?>:</th>
        </tr>
        <tr>
            <td colspan="4" style="font-size: 15px; border: 1px solid #eeeeee;">
                <span><i class="fa-regular fa-square<?php echo ($termination['termination_type'] == 'Termination') ? STR_CHECK_ICON_CLASS : ''; ?>"></i> <?= __('Termination'); ?></span>&nbsp;&nbsp;
                <span><i class="fa-regular fa-square<?php echo ($termination['termination_type'] == 'International Transfer') ? STR_CHECK_ICON_CLASS : ''; ?>"></i> <?= __('International Transfer'); ?></span>&nbsp;&nbsp;
                <span><i class="fa-regular fa-square<?php echo ($termination['termination_type'] == 'IDC Location Transfer') ? STR_CHECK_ICON_CLASS : ''; ?>"></i> <?= __('IDC Location Transfer'); ?></span>
            </td>
        </tr>

        <tr><th scope="col" colspan="4"><?= __('For Third-Party Contractors use only'); ?></th></tr>
        <tr><th scope="col" colspan="4"><?= __('Section'); ?></th></tr>
        <tr><th scope="col" colspan="4"><?= htmlspecialchars(__('Clearance from Business/Function Manager & Department Representative'), ENT_QUOTES, 'UTF-8'); ?>:</th></tr>

        <tr>
            <td colspan="4" style="padding: 0 !important;">
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <th scope="col"><?= __('Third-Party Contractor Parental Company Name'); ?></th>
                        <th scope="col" colspan="3"></th>
                    </tr>
                    <tr>
                        <th scope="col"><?= htmlspecialchars(__('Business / Function & Department Clearance'), ENT_QUOTES, 'UTF-8'); ?></th>
                        <th scope="col"><?= __('Approval'); ?></th>
                        <th scope="col"><?= __('Name'); ?></th>
                        <th scope="col"><?= __('Signature'); ?></th>
                    </tr>

                    <?php
                    foreach ($clearances as $clr) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($clr['section']); ?></td>
                        <td>
                            <?php echo $clr['approved']==1 ? '✔' : '✖'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($clr['approver_name'] ?? '-'); ?></td>
                        <td>-</td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>

        <tr>
            <th scope="col" colspan="4" style="white-space:normal"><?= __('Third-Party Contractors should hand over this form to Microchip third party coordinator A-block ground floor Finance Bay once the Sign-off is completed.'); ?></th>
        </tr>
        <tr>
            <th scope="col" colspan="4"><?= __('Declaration'); ?>:</th>
        </tr>
        <tr>
            <td colspan="4" style="white-space: normal;">
                <?= __('I hereby agree and acknowledge that all Intellectual Property (including but not limited to copyrights, designs, trademarks, patents, logos) whatsoever in my possession or created by me for Microchip Technology (India) Pvt. Ltd during the course of my employment with Microchip are exclusive property of Microchip and I shall not, at any time, assert or claim any right or interest thereto. I also agree that if proof of investment is not given at the time of final settlement of dues, the entire income will be taken as taxable.'); ?>
            </td>
        </tr>

        <tr>
            <th scope="col"><?= __('Date'); ?></th>
            <td><?php echo !empty($signature['date']) ? date('d-m-Y', strtotime($signature['date'])) : '-'; ?></td>
            <th scope="col"><?= __('(Signature of the Employee/ Intern/ Contractor)'); ?></th>
            <td></td>
        </tr>

    </table>
</div>

        </div>
    </div>
</div>
<div class="text-start">
    <button style="margin-top: 20px;" type="button" class="btn btn-primary font-weight-bolder mr-3 my-1" onclick="printDiv('printableArea')"><?= __('Print'); ?></button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
