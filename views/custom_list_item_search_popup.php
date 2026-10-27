<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$usertype = $_POST['usertype'];
$item_id = $_POST['item_id'];
$url = API."?mode=getitemsearch&usertype=".$usertype."&item_id=".$item_id;
$apires = callAPI($url);
$result = json_decode($apires,1);
?>
<?php if($usertype == "U"){
    $user_items = $result['user_items'];
    ?>
<div class="modal-header">
    <h4 class="modal-title">Users</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <?php if(count((array)$user_items)!=0){ ?>
    <table class=" table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">User Name</th>
            </tr>
        </thead>
        <body>
            <?php $i=1; foreach($user_items as $itm){ ?>
            <tr>
                <td class="p-2"><?php echo $i; ?></td>
                <td class="p-2"><?php echo $itm['display_name']; ?></td>
            </tr>
            <?php $i++;
} ?>
        </body>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Users</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<?php }elseif($usertype == "V"){
    $vendor_items = $result['vendor_items'];
    ?>

<div class="modal-header">
    <h4 class="modal-title">Vendors</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <?php if(count((array)$vendor_items)!=0){ ?>
    <table class=" table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Part No</th>
                <th scope="col">Part Name</th>
            </tr>
        </thead>
        <body>
            <?php $i=1; foreach($vendor_items as $itm){ ?>
            <tr>
                <td class="p-2"><?php echo $i; ?></td>
                <td class="p-2"><?php echo $itm['company']; ?></td>
                <td class="p-2"><?php echo $itm['vendor_part_no']; ?></td>
                <td class="p-2"><?php echo $itm['vendor_part_name']; ?></td>
            </tr>
            <?php $i++;
} ?>
        </body>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Vendors</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<?php }elseif($usertype == "C"){
    $customer_items = $result['customer_items']; ?>
<div class="modal-header">
    <h4 class="modal-title">Customers</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <?php if(count((array)$customer_items)!=0){ ?>
    <table class=" table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Part No</th>
                <th scope="col">Part Name</th>
            </tr>
        </thead>
        <body>
            <?php $i=1; foreach($customer_items as $itm){ ?>
            <tr>
                <td class="p-2"><?php echo $i; ?></td>
                <td class="p-2"><?php echo $itm['company']; ?></td>
                <td class="p-2"><?php echo $itm['customer_part_no']; ?></td>
                <td class="p-2"><?php echo $itm['customer_part_name']; ?></td>
            </tr>
            <?php $i++;
} ?>
        </body>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Vendors</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<?php }else{ ?>

<?php } ?>
<div class="modal-footer" >
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
