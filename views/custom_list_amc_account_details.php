<?php
$url = API."?mode=getamcaccountdetails&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$response = $return['amc_details'];

?>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h5>Account Management</h5>
        <div class="card-header-right">
            <a href="index.php?option=COM_INSERT_AMC_ACCOUNT_DETAILS" class="btn btn-success mb-2 desktop-view">
                <i class="icofont icofont-plus icon-white "></i>  Account
            </a>

        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">

            </div>
            <div class="col-lg-4 text-end">
                <div class="input-group">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="fa fa-search"></i></span>
                    </span>
                    <input aria-label="Serach By Account Name" type="text" class="form-control" placeholder="Serach By Account Name">
                </div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm f-12">
                <tr class="f-14">
                    <th scope="col">SL NO</th>
                    <th scope="col">Account Name</th>
                    <th scope="col">Address</th>
                    <th scope="col">State</th>
                    <th scope="col">City</th>
                    <th scope="col">Pin No</th>
                    <th scope="col">Phone No</th>
                    <th scope="col">GST No</th>
                    <th scope="col">Account Group</th>
                    <th scope="col">Edit</th>
                </tr>
                <?php $i=1;foreach ($response as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td>
                            <?php echo $row['account_name']; ?>
                        </td>
                        <td><?php echo $row['address_line1'] . $row['address_line2']; ?></td>
                        <td><?php echo $row['state_id']; ?></td>
                        <td><?php echo $row['city_id']; ?></td>
                        <td><?php echo $row['pin']; ?></td>
                        <td><?php echo $row['mobile_no']; ?></td>
                        <td><?php echo $row['gst_no']; ?></td>
                        <td><?php echo $row['account_group_id']; ?></td>
                        <td style="text-align: center;">
                            <a class="text-primary"
                                href="index.php?option=COM_INSERT_AMC_ACCOUNT_DETAILS&Rid=<?php echo deocdeId('id:'.$row['id']); ?>">
                                <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                            </a>
                        </td>
                    </tr>
                <?php $i++;
} ?>
            </table>
        </div>
    </div>
</div>
<!-- Pradeep HTML End here -->
