<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=togetuseralldetailsforuser&userid=".$_POST['assign_id'];
$datas = callAPI($vurl);
$data = json_decode($datas,1);
$status = $data['status'];
?>

<div class="modal-header">
    <h5 class="modal-title">User Tasks Histories</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body ">
    <div class="table-responsive custom-scroll" style="max-height:380px">
        <table class="table table-hover table-sm">
            <tr>
                <th scope="col">#</th>
                <th scope="col">User </th>
                <th scope="col">Project Name</th>
                <th scope="col">Task Name</th>
                <th scope="col">Start</th>
                <th scope="col">Finish</th>
                <th scope="col">Status</th>
            </tr>
            <?php $m=1;
            if(!empty($data['data'])){
                foreach($data['data'] as $dat){ ?>
                    <tr>
                        <td>
                            <?php echo $m; ?>
                        </td>
                        <td>
                            <?php if($dat['user_photo']!=""){?>
                                <img class="user-name p-0 m-0" style="width:20px" src="/uploads/all/<?php echo $dat['user_photo']; ?>" alt="User Profile">
                            <?php }else{ ?>
                                <span class="user-name"><?php echo initials($dat['display_name']); ?></span>

                            <?php } ?>
                            <span> <?php echo $dat['display_name']; ?> </span>
                        </td>
                        <td >
                            <?php echo $dat['project_name'];  ?>
                        </td>
                        <td >
                            <?php echo $dat['task_name'];  ?>
                        </td>
                        <td >
                            <?php echo fromDate($dat['planned_start']);  ?>
                        </td>
                        <td >
                            <?php echo fromDate($dat['planned_finish']);  ?>
                        </td>
                        <td >
                            <?php echo $status[$dat['current_status']]['name'];  ?>
                        </td>
                    </tr>
                <?php $m++;
}
                }else{ ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <h6 class=" mt-3"> No Data</h6>
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </td>
                    </tr>
                <?php } ?>
        </table>
    </div>
</div>
