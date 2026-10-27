<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$data = $_GET['sendData'];
$data_string = json_encode($data);
$url = API . "?mode=getMobileUsersData&method=get&data=$data_string";
$result = callAPI($url);
$result = json_decode($result, 1);
$tableData = $result['table'];

?>

<table class="table table-striped table-inverse nowrap " >
    <thead>
        <tr>
            <th scope="col">Sl No.</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Phone No.</th>
            <th scope="col">Registered Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if($tableData && !empty($tableData)) {?>
            <?php foreach($tableData as $i => $row): ?>
                <tr>
                    <td><?php echo $i; ?> </td>
                    <td><?php echo $row['user_name']; ?> </td>
                    <td><?php echo $row['user_email']; ?> </td>
                    <td><?php echo $row['user_mobile']; ?> </td>
                    <td><?php echo !empty($row['created_date'])?fromDate($row['created_date']): ''; ?> </td>
                </tr>

            <?php endforeach; ?>

        <?php }else{ ?>
            <tr>
                <td colspan="5">
                    <div class="text-center">
                        <p class="text-muted f-16 fw-bold m-b-30">
                            <i class="fa fa-warning"></i> No Records
                        </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
