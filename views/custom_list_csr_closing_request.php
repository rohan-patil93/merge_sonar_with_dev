<?php
$url = API . "?mode=getCompletedProjects&method=get";
$response = callAPI($url);
$response = json_decode($response, 1);
?>

<link rel="stylesheet" href="assets/css/custom_list_csr_closing_request.css">

<div class="page-body">
    <div class="row">
        <div class="col-xl-12 col-md-12">
            <div class="card table-card">
                <div class="card-header">
                    <h5>CSR Closing Request</h5>
                </div>
                <div class="card-block custom-scroll csr-closing-scroll" >
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col">S No.</th>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Project Requestor</th>
                                    <th scope="col">Count Of Phases</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Cost</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($response){
                                    $i = 1;
                                    foreach ($response as $row):
                                ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><a class="text-primary m-r-5"
                                                    href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo deocdeId('id:' . $row['proposal_id']) ?>'><?php echo $row['subject']; ?> </a></td>

                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['proposal_count']; ?></td>

                                            <td><?php echo $row['date']; ?></td>
                                            <td><?php echo $row['date']; ?></td>
                                            <td class="text-end"><?php echo $row['total_budget_grand_total']; ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-primary waves-effect waves-light btn-s csr-closing-btn"
                                                    type="button"
                                                    data-id="<?php echo $row['proposal_id']; ?>">
                                                Completed
                                            </button>

                                            </td>
                                        </tr>

                                <?php
                                        $i++;
                                    endforeach;
                                }else{
                                    echo '<tr><td colspan="8"> <div class="text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png">
                                        </div>
                                    </div> </td> </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/custom_list_csr_closing_request.js"></script>
