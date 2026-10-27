<?php
$_SESSION['apply_js']['multiselest'] = 1;

$company_id = isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '';
$apiResponse = array();
$missEmployees = array();
$missingCounts = array(
    'email' => 0,
    'b_machine_id' => 0,
    'user' => 0,
    'salary' => 0,
    'pay_area' => 0
);


    $url = API . "?method=get&mode=getEmployeeMissingDetails&company_id=".$company_id;
    $response = callAPI($url);
    $apiResponse = json_decode($response, true);

    if (is_array($apiResponse)) {
        $missEmployees = (isset($apiResponse['missing_employees']) && is_array($apiResponse['missing_employees'])) ? $apiResponse['missing_employees'] : array();
    }

    foreach ($missEmployees as $empRow) {
        $flags = isset($empRow['missing']) && is_array($empRow['missing']) ? $empRow['missing'] : array();
        foreach ($missingCounts as $flagKey => $flagCount) {
            if (isset($flags[$flagKey])) {
                $missingCounts[$flagKey]++;
            }
        }
    }


function emdEsc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<link rel="stylesheet" href="assets/css/custom_employee_missing_details.css">

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Employee Missing Details</h5>
            <span>Employees missing email, user mapping, salary, or pay area.</span>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="employeeMissingDetailsTable">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>emp_code</th>
                            <th>Employee Name</th>
                            <th>Email Missing <span class="label label-primary"><?php echo (int)$missingCounts['email']; ?></span></th>
                            <th>B-Machine ID Missing <span class="label label-primary"><?php echo (int)$missingCounts['b_machine_id']; ?></span></th>
                            <th>User Missing <span class="label label-primary"><?php echo (int)$missingCounts['user']; ?></span></th>
                            <th>Salary Missing <span class="label label-primary"><?php echo (int)$missingCounts['salary']; ?></span></th>
                            <th>Pay Area Missing <span id="missingCountPayArea" class="label label-primary"><?php echo (int)$missingCounts['pay_area']; ?></span></th>
                        </tr>
                    </thead>
                    <tbody id="employeeMissingDetailsTbody">
                        <?php if ($company_id == '') { ?>
                            <tr>
                                <td colspan="8" class="text-center employee-missing-empty">Company context missing in session.</td>
                            </tr>
                        <?php } else if (!is_array($apiResponse)) { ?>
                            <tr>
                                <td colspan="8" class="text-center employee-missing-empty">Unable to load employee details.</td>
                            </tr>
                        <?php } else if (count($missEmployees) == 0) { ?>
                            <tr>
                                <td colspan="8" class="text-center employee-missing-empty">No missing employee details found.</td>
                            </tr>
                        <?php } else {
                            $srNo = 1;
                            foreach ($missEmployees as $row) {
                                $empId = isset($row['id']) ? (string)$row['id'] : '';
                                $missingFlags = isset($row['missing']) && is_array($row['missing']) ? $row['missing'] : array();
                                $displayName = isset($row['display_name']) && trim((string)$row['display_name']) != '' ? $row['display_name'] : ('Employee #' . $empId);
                                $empCode = isset($row['emp_code']) ? emdEsc($row['emp_code']) : emdEsc($empId);
                                ?>
                                <tr>
                                    <td><?php echo $srNo; ?></td>
                                    <td><?php echo $empCode; ?></td>
                                    <td><?php echo emdEsc($displayName); ?></td>
                                    <td>
                                        <?php if (isset($missingFlags['email'])) { ?>
                                            <button type="button" class="btn btn-sm btn-warning employee-missing-btn js-missing-action" data-type="Email" data-missing-key="email" data-empid="<?php echo emdEsc($empId); ?>">Email</button>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($missingFlags['b_machine_id'])) { ?>
                                            <a class="btn btn-sm btn-warning employee-missing-btn" href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES&Rid=<?php echo rawurlencode(deocdeId('id:' . $empId)); ?>" target="_blank" rel="noopener noreferrer">B-Machine ID</a>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($missingFlags['user'])) { ?>
                                            <button type="button" class="btn btn-sm btn-warning employee-missing-btn js-missing-action" data-type="User" data-missing-key="user" data-empid="<?php echo emdEsc($empId); ?>">User</button>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($missingFlags['salary'])) { ?>
                                            <a class="btn btn-sm btn-warning employee-missing-btn" href="index.php?option=COM_INSERT_SALARY&newempid=<?php echo rawurlencode($empId); ?>" target="_blank" rel="noopener noreferrer">Salary</a>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($missingFlags['pay_area'])) { ?>
                                            <button type="button" class="btn btn-sm btn-warning employee-missing-btn js-missing-action" data-type="Pay Area" data-missing-key="pay_area" data-empid="<?php echo emdEsc($empId); ?>">Pay Area</button>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                </tr>
                            <?php $srNo++;
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_employee_missing_details.js"></script>
