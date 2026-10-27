<?php
$return = array();
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getvisitormaterialgatepassdata&rid=$rid";
    $che = callAPI($url);
    $return = json_decode($che, 1);
}

// Extract data safely
$visitor = $return['visitor_data'] ?? [];
$luggages  = $return['visitor_luggage_data'] ?? [];
$employees = $return['employees_data'] ?? [];

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-PPIZEGYM1v8zp5Py7UjFb79S58UeqCL9pYVnVPURKEqvioPROaVAJKKLzvH2rDnI" crossorigin="anonymous">
<link rel="stylesheet" href="assets/css/custom_view_visitor_material_gate_pass.css">

<div class="page-body">
    <div class="card">
        <div class="card-body">
            <div class="" id="printableArea">
                <table class="gatepass-table" >
                    <tr>
                        <th scope="col" colspan="4" class="gp-header-logo">
                            <img src="assets/images/logo_vms.png" alt="logo">
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="4" class="gp-title">Material Gate Pass</th>
                    </tr>
                    <tr>
                        <td class="gp-td">
                            <i class="fa-regular fa-square gp-icon" ></i>
                            Returnable
                        </td>
                        <td class="gp-td">
                            <i class="fa-regular fa-square gp-icon"></i>
                            Non-Returnable
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="gp-th">Expected Date of Return:</th>
                        <td class="gp-td">
                            <?= htmlspecialchars($visitor['to_date'] ?? '') ?>&nbsp;
                        </td>
                        <th scope="col" class="gp-th">Employee ID:</th>
                        <td class="gp-td">
                            <?= htmlspecialchars($visitor['employee_code'] ?? ($employees['emp_code'] ?? '')) ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="gp-th">Employee Name:</th>
                        <td class="gp-td">
                            <?= htmlspecialchars($visitor['employee_name'] ?? ($employees['emp_name'] ?? '')) ?>&nbsp;
                        </td>
                        <th scope="col" class="gp-th">Dept Name:</th>
                        <td class="gp-td">
                            <?= htmlspecialchars($visitor['employee_department'] ?? ($employees['department'] ?? '')) ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" colspan="4" class="gp-th">Address:</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td">
                            <?= htmlspecialchars($employees['address'] ?? $employees['contact_address'] ?? '') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" colspan="4" class="gp-th">Reason for Material Movement:</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td">
                            <?= htmlspecialchars($visitor['purpose'] ?? '') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td">
                            Are these goods Bonded:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td">
                            If Yes, then necessary approval is obtained from customs: YES/ NO
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td gp-no-padding">
                            <table class="gatepass-inner-table">
                                <tr>
                                    <th scope="col" class="gp-th">Sl No</th>
                                    <th scope="col" class="gp-th">Description</th>
                                    <th scope="col" class="gp-th">Bond No</th>
                                    <th scope="col" class="gp-th">Qty</th>
                                    <th scope="col" class="gp-th">Value</th>
                                </tr>
                                <?php
                                if (!empty($luggages)) {
                                    $i = 1;
                                    foreach ($luggages as $row) {
                                        echo '<tr>
                                            <td class="gp-td">' . $i . '</td>
                                            <td class="gp-td">' . htmlspecialchars($row['item_name'].' - '.$row['brand_name'] ?? '') . '</td>
                                            <td class="gp-td">' . htmlspecialchars($row['serial_or_remarks'] ?? '') . '</td>
                                            <td class="gp-td">' . htmlspecialchars($row['qty'] ?? '') . '</td>
                                            <td class="gp-td">' . htmlspecialchars($row['value'] ?? '') . '</td>
                                        </tr>';
                                        $i++;
                                    }
                                } else {
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo '<tr>
                                            <td class="gp-td">' . $i . '</td>
                                            <td class="gp-td">&nbsp;</td>
                                            <td class="gp-td">&nbsp;</td>
                                            <td class="gp-td">&nbsp;</td>
                                            <td class="gp-td">&nbsp;</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr style="background-color: #eeeeee;">
                        <td colspan="4" class="gp-td">&nbsp;</td>
                    </tr>
                    <tr>
                        <th scope="col" class="gp-th">Employee Signature</th>
                        <th scope="col" class="gp-th">Security Check & Sign</th>
                        <th scope="col" colspan="2" class="gp-th">Admin Signature</th>
                    </tr>
                    <tr>
                        <th scope="col" class="gp-th gp-sign"></th>
                        <th scope="col" class="gp-th gp-sign"></th>
                        <th scope="col" colspan="2" class="gp-th gp-sign"></th>
                    </tr>
                    <tr>
                        <td class="gp-td">
                            <b class="gp-block">Gatepass No:</b>
                            <span class="gp-small">(Security to update Gatepass No & Date)</span>
                        </td>
                        <td class="gp-td">
                            <?= htmlspecialchars($visitor['id'] ?? '') ?>&nbsp;
                        </td>
                        <th scope="col" class="gp-th">Gatepass Date:</th>
                        <td class="gp-td">
                            <?= !empty($visitor['in_datetime']) ? date('d-m-Y', strtotime($visitor['in_datetime'])) : date('d-m-Y') ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="gp-td">
                            Note: Employee will be deemed responsible for movement of goods. Also, employees ensure the goods are returned within date mentioned and inform security on return/receipt of goods.
                        </td>
                    </tr>
                </table>
            </div>

            <button  type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1 gp-print-btn js-print-gatepass" data-print-target="printableArea" >Print Gatepass</button>
        </div>
    </div>
</div>
<script src="assets/js/custom_view_visitor_material_gate_pass.js"></script>
