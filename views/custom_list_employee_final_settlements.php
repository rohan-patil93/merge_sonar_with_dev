<?php
$_SESSION['apply_js']['table']= 1;
$url = API."?mode=togetemployeefinalsettlements";
$apires = callAPI($url);
$result = json_decode($apires, 1);
$title = "Employees  Full & Final Settlement"

?>
<div class="pcoded-inner-content mob-pd">
  <div class="main-body">
    <div class="page-wrapper mob-pd p-0">
       <div class="page-body">
        <div class="card">
            <div class="card-header table-card-header">
               <h5 class=""><?php echo $title;  ?></h5>
                <a href="javascript:history.go(-1)" class="mb-2 mob-import ml-1 float-right">
                  <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icofont icofont-reply text-primary"></i></button>
               </a>
               <div class="card-header-right table-card-header col-lg-6 text-right">
                    <a target="_blank"  href="index.php?option=COM_INSERT_TAX_SLABS"  class="btn btn-success mb-2 desktop-view" <?php echo $insert_permissionbtn; ?>>
                       <i class="icofont icofont-plus icon-white"></i>
                       <?php echo $title;  ?>
                    </a>
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                      <i class="icofont icofont-reply"></i>
                      Back
                   </a>
                </div>
            </div>
            <div class="card-block table-responsive" id="dyanmictableshow">
                <table id="basic-btn" class="table table-striped table-bordered nowrap">
                    <thead>
                        <tr>
                            <th scope="col">SL NO.</th>
                            <th scope="col">Employee Code</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Termination Type</th>
                            <th scope="col">Settle Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($result['data'] as $tax){ ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $tax['emp_code']; ?></td>
                                <td><?php echo $tax['display_name']; ?></td>
                                <td><?php echo $tax['email_id']; ?></td>
                                <td><?php echo ucwords($tax['termination_type']); ?></td>
                                <td><?php echo fromDate($tax['settle_on']); ?></td>
                                <td>

                                    <a href="index.php?option=COM_VIEW_FULL_FINAL_SETTLEMENT_OF_ACCOUNTS_REPORT&EMPID=<?php echo $tax['emp_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

