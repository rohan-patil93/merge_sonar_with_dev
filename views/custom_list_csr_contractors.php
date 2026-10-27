<?php

// Initialize variables

$url = API . "?mode=getCSRContractors&method=get";
$response = callAPI($url);
$filteredData = json_decode($response, true)['csr_contractors'] ?? [];

?>

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header">
            <h5>CSR Contractors</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_INSERT_CSR_CONTRACTOR_REGISTRATION" class="btn btn-success mb-2 desktop-view">
                    <i class="icofont icofont-plus icon-white"></i>
                    Contractor
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">S.No.</th>
                            <th scope="col">Contractor Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">GST</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$filteredData) > 0) { ?>
                            <?php $i = 1;
                            foreach ($filteredData as $row) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['contractor_name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['phone']; ?></td>
                                    <td><?php echo $row['gst']; ?></td>
                                    <td>
                                        <a href="index.php?option=COM_INSERT_CSR_CONTRACTOR_REGISTRATION&Rid=<?php echo deocdeId('id:' . $row['id']) ?>&edit=1">
                                            <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                        </a>
                                        <a href="index.php?option=COM_INSERT_CSR_CONTRACTOR_REGISTRATION&Rid=<?php echo deocdeId('id:' . $row['id']) ?>&view=1">
                                            <i class="fa fa-eye btn-primary p-2 b-radius-5"></i>
                                        </a>
                                    </td>

                                </tr>
                        <?php }
                        } else {
                            echo '<tr> <td colspan="6" class="text-center">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </td> </tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
