<?php

$url = CRMAPI . "?mode=getsalesinvoicedynamicexcellist&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$excel_data = $return['excel_data'];

?>
<div class="card">
    <div class="card-header">
        <h5>Sales Invoice Uploaded Excel List</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Sr.No</th>
                    <th scope="col">File Name</th>
                    <th scope="col">Upload Name</th>
                    <th scope="col">Upload Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($excel_data)) {
                    $i = 1;
                    foreach ($excel_data as $row) {
                ?>
                        <tr>
                            <td class="p-2"><?php echo  $i; ?></td>
                            <td class="p-2"><?php echo  $row['file_name']; ?></td>
                            <td class="p-2"><?php echo  $row['import_name']; ?></td>
                            <td class="p-2"><?php echo  fromDate($row['created_date']); ?></td>
                            <td class="p-2">
                        <a class="btn btn-primary"
                            href="index.php?option=COM_LIST_SALES_INVOICE_DYNAMIC&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">View

                        </a>

                    </td>                            </tr>
                    <?php $i++;
                    }
                } else { ?>
                    <tr>
                        <td colspan="5">
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
