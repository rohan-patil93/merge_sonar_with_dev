<?php

include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$data = $_POST['sendData'];
$dataString = json_encode($data);
$dataString = urlencode($dataString);
$cudata = '';
$url = API . "?mode=getComplaintsData&method=get&data=$dataString";
$cudata = callAPI($url);
$cudata = json_decode($cudata, 1);
$tableData = $cudata['table'];
?>

<table class="table table-striped table-inverse nowrap " >
    <thead>
        <tr>
            <th scope="col">Sl No.</th>
            <th scope="col">Complaint Name</th>
            <th scope="col">Complaint Type</th>
            <th scope="col">Priority</th>
            <th scope="col">Date</th>
            <th scope="col">Item</th>
            <th scope="col">Asset</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if($tableData && !empty($tableData)) {?>
            <?php foreach($tableData as $i => $row): ?>
                <tr>
                    <td><?php echo $i; ?> </td>
                    <td><?php echo $row['complaint_name']; ?> </td>
                    <td><?php echo $row['complaint_type']; ?> </td>

                    <td style="font-weight: 900; font-size: 1rem;<?php
                    if($row['complaint_priority']=='L'){
                        echo 'color: green;';
                    } elseif($row['complaint_priority']=='M'){
                        echo 'color: yellow; ';
                    } else {
                        echo 'color: red;';
                    }
                ?>">
                <?php
                if($row['complaint_priority']=='L'){
                    echo 'Low';
                } elseif($row['complaint_priority']=='M'){
                    echo 'Medium';
                } else {
                    echo "High";
                }
                ?>
            </td>
            <td><?php echo !empty($row['transaction_date'])?fromDate($row['transaction_date']): ''; ?> </td>
            <td><?php echo $row['item_name']; ?> </td>
            <td><?php echo $row['asset_code'].'-'.$row['asset_description']; ?> </td>
            <td><span class="form-label label label-lg  label-primary label-inline f-w-900 " style="background-color:<?php echo $row['background_color']; ?> ; color:<?php echo $row['color']; ?> ;"><?php echo $row['name']; ?></span> </td>
            <td>
                <a class=" m-r-5"
                href="index.php?option=COM_INSERT_COMPLAINTS&Rid=<?php echo deocdeId('id:'.$row['id']); ?>" title="Edit Details">
                <i class="fa fa-edit  p-2 b-radius-5"></i>
            </a>
            <a class=" m-r-5"
            href="index.php?option=COM_VIEW_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:'.$row['id']); ?>" title="View Details">
            <i class="fa fa-eye  p-2 b-radius-5"></i>
        </a>
        <a class=" m-r-5"
        href="index.php?option=COM_INSERT_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="Copy Details">
        <i class="fa fa-clone  p-2 b-radius-5"></i>
    </a>
</td>
</tr>

<?php endforeach; ?>

<?php }else{ ?>
    <tr>
        <td colspan="9">
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
