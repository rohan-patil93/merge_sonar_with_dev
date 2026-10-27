<?php

    $url = API."?mode=getassetallocationvms&employee=".$_SESSION['emp_id'];
    $data_details = callAPI($url);
    $asedetails = json_decode($data_details,1);
    $aaetlist = $asedetails['list_assets'];
    $asset_allocation_status= array('0'=>'allocate','1'=>'Accepted','2'=>'Rejected','3'=>'Return');
$rid = encodeId($_GET['Rid']);
    $rid = explode(":", $rid);
    $rid = $rid[1];

?>

<script>
function assetaccept(assetid,id,admin_budddy){
    jQuery.ajax({
        url: "views/custon_accept_asset_popup.php",
        data: "assetid="+assetid+"&id="+id+"&admin_budddy="+admin_budddy,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
        }
    });
}
</script>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom ">
      <div class="card-header table-card-header mb-2">
       <h5>Asset Alloation List</h5>
       <div class="card-header-right table-card-header ">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
          <i class="icofont icofont-reply"></i>
          Back
        </a>
      </div>
    </div>

<div class="row mt-4 p-2">
<div class="dt-responsive table-responsive">
    <table id="basic-btn" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col">
                </th>
                <th scope="col">Asset Code</th>
                <th scope="col">Asset Name</th>
                <th scope="col">End Date</th>
                <th scope="col">Remarks</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $im = 1; foreach($aaetlist as $list_asset){
                ?>
                    <tr class="<?php echo $list_asset['emp_asset_id']==$rid?'bg-info':''; ?>">
                        <td class="text-center">

                            <?php echo $im; ?>
                        </td>
                        <td><button type="button" class="text-primary" onclick="AddAssets(<?php echo $list_asset['id']; ?>)"><?php echo $list_asset['asset_code']; ?></button></td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                        <td><?php echo $list_asset['end_date']; ?></td>
                        <td><?php echo $list_asset['remarks']; ?></td>
                        <td><?php echo $list_asset['allocation_type'] != ""?'<span class="form-label label label-warning">'.$asset_allocation_status[$list_asset['allocation_type']].'</span>':""; ?>
                    </td>
                        <td>
                        <?php if($list_asset['allocation_type'] == "0"){ ?>
                        <button class="btn btn-primary" type="button"  onclick="assetaccept('<?php echo $list_asset['id']; ?>','<?php echo $list_asset['emp_asset_id']; ?>','<?php echo $list_asset['admin_buddy']; ?>')">View</button>
            <?php } ?>
                        </td>
                </tr>
            <?php $im++;
} ?>

            </tbody>
        </table>
    </div>
 </div>
</div>
</div>
</div>
