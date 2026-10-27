<?php

$url = API . "?mode=getallproductdetails";
$response = callAPI($url);
$response = json_decode($response, 1);
$customers = $response['cust_details'];

?>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>AMC Customer Details</h4>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-primary waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm f-12">
                <tr class="f-14">
                    <th scope="col">SL NO</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Addresss</th>
                    <th scope="col">Pincode</th>
                    <th scope="col">Edit</th>
                </tr>
                <?php $i = 1;
                foreach ($customers as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td>
                            <?php echo $row['customer_name']; ?>
                        </td>
                        <td><?php echo $row['email_id']; ?></td>
                        <td><?php echo $row['mobile_no']; ?></td>
                        <td><?php echo $row['address_line1'] . $row['address_line2']; ?></td>
                        <td><?php echo $row['pin_code']; ?></td>

                        <td style="text-align: center;">
                            <a class="text-primary m-r-5"
                                href="index.php?option=COM_INSERT_AMC_CUSTOMER_DETAILS&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
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
