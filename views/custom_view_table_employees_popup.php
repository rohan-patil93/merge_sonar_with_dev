<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$urlemp = urldecode($_POST['empids']);
$empids = json_decode($urlemp,1);
$emp_ids = implode(',',(array)$empids);
$post['empids'] = $emp_ids;
$vurl = API."?mode=togetemployeedetaila&method=post";
$empdata = file_post_contents($vurl,$post);
$empdata = json_decode($empdata,1);
?>
<div class="modal-header">
    <h5 class="modal-title">
        <small><b class="f-w-600"> <?php echo "Employees"; ?> </b> </small>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
        <div class="form-group row">
            <div class="col-lg-12">
                <table  class="table table-striped table-bordered nowrap">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Employee Code</th>
                        <th scope="col">Employee Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Reporting Manager</th>
                    </tr>
                    <?php  $k=1; foreach($empdata as $list){ ?>
                    <tr>
                        <td><?php echo $k; ?></td>
                        <td><?php echo $list['emp_code']; ?></td>
                        <td><?php echo $list['display_name']; ?></td>
                        <td><?php echo $list['email_id']; ?></td>
                        <td><?php echo $_SESSION['users'][$list['reports_to']]; ?></td>

                    </tr>
                    <?php $k++;
} ?>
                </table>
            </div>
        </div>
    </div>
</div>
