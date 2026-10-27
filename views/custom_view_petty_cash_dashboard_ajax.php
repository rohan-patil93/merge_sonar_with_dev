<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$startdate = "";
$enddate = "";
$url1 = API."?mode=pettycashdashboard&startdate=".$startdate."&enddate=".$enddate."&location_transaction=Y&location_id=".$_POST['loc_id'];
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
$petty_cash_boxes = $result['petty_cash_boxes'];
foreach($petty_cash_boxes as $pettycash_boxes){
    $pettycashboxes[$pettycash_boxes['id']] = $pettycash_boxes['pc_box_description'];
}
$location_pettycashboxs = $result['location_pettycashbox'];
foreach($location_pettycashboxs as $locationpettycashbox){
    $locationpettycashboxlist[$locationpettycashbox['pc_box_id']][] =$locationpettycashbox;
}
$statuse = ViewStatusData(); ?>

<ul class="nav md-tabs infisuite_tab">
    <?php $mk=0; foreach($locationpettycashboxlist as $pt_id=>$location_pettycashbox){ ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $mk==0?"active":"";?>" data-bs-toggle="tab" href="#appraisal_setting_<?php echo $pt_id; ?>" role="tab"><?php echo $pettycashboxes[$pt_id]; ?> <span class="form-label label label-sm label-warning"><?php echo count((array)$locationpettycashboxlist); ?></span></a>
    </li>
    <?php $mk++;
} ?>
</ul>
<div class="tab-content card-block">
    <?php $mk=0; foreach($locationpettycashboxlist as $pt_id=>$location_pettycashbox){  ?>
            <div class="tab-pane <?php echo $mk==0?"active":"";?>" id="appraisal_setting_<?php echo $pt_id; ?>" role="tabpanel">
 <?php if(count((array)$location_pettycashboxs) !=0){
?>
<table class="table table-hover m-b-0" style="font-size:13px;">
    <thead>
        <tr><th scope="col">Transaction No.</th>
            <th scope="col">Originator</th>
            <th scope="col">Date</th>
            <th scope="col">Party Name</th>
            <th scope="col" class="text-right">Amount</th>
            <th scope="col">Satus</th>
        </tr></thead>
        <tbody>
            <?php foreach($location_pettycashbox as $list){ ?>
                <tr>
                    <td><a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>"><?php echo $list['transaction_number']; ?></a></td>
                    <td class="w-b-w-s"><?php echo $list['display_name']; ?></td>
                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                    <td >
                        <span class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $statuse[$list['current_status']]['name']; ?></span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Transactions</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php }?>
</div>
<?php $mk++;
} ?>
</div>
