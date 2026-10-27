<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_POST['user_id'] = $_SESSION['userid'];
$_POST['company_id'] = $_SESSION['company_id'];
$url = API."?mode=getitemsearch&method=post";
$return = file_post_contents($url, $_POST);
$result = json_decode($return,1);
$items = $result['items'];
foreach($result['item_types'] as $itype){
    $item_types[$itype['id']] =$itype;
}
foreach($result['item_categories'] as $icat){
    $item_categories[$icat['id']] =$icat;
}
foreach($result['locations'] as $locc){
    $locations[$locc['id']] =$locc;
}
foreach($result['warehouses'] as $ware){
    $warehouses[$ware['id']] =$ware;
}
?>
<?php if(count((array)$items)!=0){ ?>
<table class=" table table-hover">
<thead>
    <tr>
        <th scope="col">SKU </th>
        <th scope="col">Item </th>
        <th scope="col">Item Type</th>
        <th scope="col">category</th>
        <th scope="col">Location</th>
        <th scope="col">Warehous</th>
        <th scope="col">User Access</th>
        <th scope="col">Vendors Part Details</th>
        <th scope="col">Customers Part Details</th>
    </tr>
</thead>
<body>
    <?php foreach($items as $itm){ ?>
    <tr>
        <td class="p-2"><?php echo $itm['sku']; ?></td>
        <td class="p-2"><?php echo $itm['item_name']; ?></td>
        <td class="p-2"><?php echo $item_types[$itm['item_type']]['item_type_name']; ?></td>
        <td class="p-2"><?php echo $item_categories[$itm['category_id']]['category_description']; ?></td>
        <td class="p-2"><?php echo '-' ?></td>
        <td class="p-2"><?php echo $warehouses[$itm['warehouse_id']]['warehouse_name']; ?></td>
         <td class="p-2"><span class="cursor-pointer text-primary" onclick="getUserVendorCustomerItemList('U','<?php echo $itm['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-arrow-circle-down"></i> User</span></td>
        <td class="p-2"><span class="cursor-pointer text-primary" onclick="getUserVendorCustomerItemList('V','<?php echo $itm['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-arrow-circle-down"></i> Vendor</span></td>
        <td class="p-2"><span class="cursor-pointer text-primary" onclick="getUserVendorCustomerItemList('C','<?php echo $itm['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-arrow-circle-down"></i> Customer</span></td>
    </tr>
    <?php } ?>
</body>
<?php }else{ ?>
<div class="card-px text-center ">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Items</p>
    <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
</div>
<?php } ?>
