<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if (isset($_POST['month'])) {
    $url = API . "?mode=getCustomerBillingData&method=get&month={$_POST['month']
}";
    $response = callAPI($url);
    $filteredData = json_decode($response, true);
    $data = $filteredData['data'] ?? array();
}
?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-light">
            <tr>
                <th scope="col"><?= __('Sr.No'); ?></th>
                <th scope="col"><?= __('Customer Name'); ?></th>
                <th scope="col"><?= __('No. Employees'); ?></th>
                <th scope="col"><?= __('Total Working Days'); ?></th>
                <th scope="col"><?= __('Total Customer Billing Amount (INR)'); ?></th>
                <th scope="col"><?= __('Total Actual Billing Amount (INR)'); ?></th>
                <th scope="col"><?= __('Total Client Payroll'); ?></th>
                <th scope="col"><?= __('Action'); ?></th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php if(count((array)$data)>0){
                $i = 1;
                foreach ($data as $row){
                    ?>
                    <tr>
                        <td> <?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['display_name'] ?? '') ?></td>
                        <td><?php echo htmlspecialchars($row['no_of_employees'] ?? '') ?></td>
                        <td><?php echo htmlspecialchars($row['total_working_days'] ?? '') ?></td>
                        <td><?php echo htmlspecialchars($row['total_customer_billing_amount'] ?? '') ?></td>
                        <td><?php echo htmlspecialchars($row['total_actual_billing_amount'] ?? '') ?></td>
                        <td><?php echo htmlspecialchars($row['total_client_payroll'] ?? '') ?></td>
                        <td>
                            <?php if($row['no_of_employees']>0 && ($row['total_working_days'] == '' || $row['total_working_days'] == null)){ ?>
                            <a class='btn btn-success m-r-5'
                                href='index.php?option=COM_INSERT_CUSTOMER_EMPLOYEE_BILLINGS&Rid=<?php echo  deocdeId('id:' . $row['id']) ?>&month=<?php echo $_POST["month"]; ?>'                                >
                                <?php echo __('ADD');  ?>
                            </a>
                            <?php }elseif($row['no_of_employees']>0 && ($row['total_working_days'] != '')){ ?>
                            <a class='btn btn-primary m-r-5'
                                href='index.php?option=COM_INSERT_CUSTOMER_EMPLOYEE_BILLINGS&Rid=<?php echo  deocdeId('id:' . $row['id']) ?>&month=<?php echo $_POST["month"]; ?>'>
                                <?php echo __('EDIT');  ?>
                            </a>
                            <?php } ?>
                        </td>
                    </tr>
            <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Records'); ?></p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <!-- Data will be injected here -->
        </tbody>
    </table>
</div>
