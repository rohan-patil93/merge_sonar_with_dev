<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = $_POST['table'];
$trnid = $_POST['trnid'];
$item_id = $_POST['item_id'];
$quality_type = $_POST['quality_type'];
$empurl = API."?mode=getitemqualitychecknewdetails&quality_type=".$quality_type."&table=".$table."&trnid=".$trnid."&item_id=".$item_id;
$empres = callAPI($empurl);
$data = json_decode($empres,1);
$type = array('1'=>'Serial','2'=>'Lot');
$status = "Active";
$spfulljson = json_decode($data['quality_parameters_mapping']['specifications'],1);
if($data['items']['tracking_type']==1){
    $listdata = $data['item_serial_nos'];
}
if($data['items']['tracking_type']==2){
    $listdata = $data['item_lot_nos'];
}
$listdata = $data['item_serial_nos'];
$attributes = $data['quality_parameters'];
$groups = $data['quality_parameters_groups'];
$listparameters = $data['quality_parameters'];
foreach( $groups as $group){
    $groupsbaseid[$group['id']] = $group['group_name'];
}
$reschk = $data['item_quality_check'];
foreach($reschk as $resc){
    $resdata[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['specification_result'];
    $resremarkdata[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['remarks'];
    $resquality[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['quality_check'];
}
$spjsonlist = $spfulljson['parameters'];
foreach($spjsonlist as $paramlist){
    $spj_ids[] = $paramlist['id'];
    $spchkp[$paramlist['id']] = $paramlist;
    $reslutlist[$paramlist['id']] =  $paramlist['res'];
}
$listgroup = $spfulljson['group'];

?>
<div class="modal-header">
    <h5 class="modal-title"><?= __('Quality Check'); ?></h5>

    <button type="button"><i class="ti-printer f-18 text-success"></i> </button>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body p-5">
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <h5><?= __('Simple Method - 1: Measuring Quality Performance in Defective Percentageqq'); ?></h5>
                <div  class="card-header-right">
                    <button type="button" class="btn btn-link p-0 border-0" onclick="printview()" aria-label="<?= htmlspecialchars(__('Print'), ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="ti-printer f-18 text-success"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    $tunt =  count((array)$listdata);
                    $dfunt = 2;
                    $punt = (($dfunt/$tunt)*100);
                    ?>
                    <table class="table table-hover table-sm">
                        <tr>
                            <th scope="col" style="background:#cbebd5ba"><?= __('Line/Scetion No.'); ?></th>
                            <th scope="col"><?php echo $data['item']['item_name']; ?></th>
                            <th scope="col" style="background:#cbebd5ba"><?= __('Checker Name'); ?></th>
                            <th scope="col">
                                <?php echo ucwords($_SESSION['users'][$data['qc_head']['created_by']]); ?>
                            </th>
                            <th scope="col" style="background:#cbebd5ba"><?= __('Total Unit Checked'); ?></th>
                            <th scope="col"><?php echo $tunt; ?></th>
                        </tr>
                        <tr>
                            <td style="background:#cbebd5ba"><?= __('Style No.'); ?></td>
                            <td>#<?php echo $data['item']['sku']; ?></td>
                            <td style="background:#cbebd5ba"><?= __('Date'); ?></td>
                            <td><?php echo fromDate($data['qc_head']['created_date']); ?></td>
                            <td style="background:#cbebd5ba"><?= __('Total Defective Unit'); ?></td>
                            <td><?php echo $dfunt; ?></td>
                        </tr>
                        <tr>
                            <td style="background:#cbebd5ba"><?= __('Transaction No'); ?> </td>
                            <td><?php echo $data['transaction_data']['transaction_number']; ?></td>
                            <td style="background:#cbebd5ba"><?= __('Transaction Date'); ?></td>
                            <td><?php echo fromDate($data['transaction_data']['transaction_date']); ?></td>
                            <td style="background:#cbebd5ba"><?= __('% Defective'); ?></td>
                            <td><?php echo $punt; ?></td>
                        </tr>
                        <tr>
                            <td style="background:#cbebd5ba"><?= __('Transaction Qty'); ?></td>
                            <td></td>
                            <td style="background:#cbebd5ba"><?= __('Qty checked'); ?></td>
                            <td></td>
                            <td style="background:#cbebd5ba"></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <?php $k=1; foreach($listdata as $tabs){
                $listval = $tabs; $slid = $tabs['id'];  ?>
                <div class="tab-pane <?php if($k==1){  echo "active";
} ?> " id="tab<?php echo $k; ?>" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <tr>
                                        <th scope="col" style="width: 60%;"><?php echo $tabs['serial_no']; ?></th>
                                        <th scope="col" style="width: 20%;"><?= __('Result'); ?> </th>
                                        <th scope="col" style="width: 20%;"><?= __('Interval'); ?></th>
                                        <th scope="col" style="width: 20%;"><?= __('Remarks'); ?></th>
                                    </tr>
                                    <?php $m=1; foreach ($listgroup as $lisgroup) { ?>
                                        <tr style="background-color: #f5f6f7;">
                                            <td colspan="4" class="f-w-600"><?php echo $groupsbaseid[$lisgroup]; ?></td>
                                        </tr>
                                        <?php $n=1; foreach($listparameters[$lisgroup] as $param){
                                            if(in_array($param['id'],$spj_ids)){
                                                $att = $param; $spj_id = $param['id'];
                                                ?>
                                                <tr>
                                                    <td style="width: 60%;">
                                                        <?php echo $param['name']; ?>
                                                    </td>
                                                    <td class="f-w-600" style="width: 20%;">
                                                        <?php
                                                        if($att['display_type']=="1" || $att['display_type']=="2"||$att['display_type']=="3" ){
                                                            $selectdata = json_decode($att['quality_parameters'],1);
                                                            echo $selectdata[$resdata[$slid][$spj_id]];
                                                        }
                                                        if($att['display_type']=="4" || $att['display_type']=="5"){
                                                            echo $resdata[$slid][$spj_id];
                                                        }
                                                        if($resquality[$slid][$spj_id]==0){ ?>
                                                            <i class="fa fa-check-circle text-success f-18"></i>
                                                        <?php }elseif($resquality[$slid][$spj_id]==1){?>
                                                            <i class="fa  fa-times-circle text-danger f-18"></i>
                                                        <?php } ?>

                                                    </td>
                                                    <td style="width: 20%;">
                                                        <?php
                                                        if($att['display_type']=="1" || $att['display_type']=="2"||$att['display_type']=="3" ){
                                                            $selectdata = json_decode($att['quality_parameters'],1);
                                                            echo implode(" / ", (array)$selectdata);
                                                        }
                                                        if($att['display_type']=="4"){
                                                            echo "1 to ".$att['quality_parameters'];
                                                        }
                                                        if($att['display_type']=="5"){
                                                            $selectdata = json_decode($att['quality_parameters'],1);
                                                            echo implode(" - ", (array)$selectdata);
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <?php echo $resremarkdata[$slid][$spj_id]; ?>

                                                    </td>

                                                </tr>
                                                <?php $n++; ?>
                                                <?php } ?>
                                                <?php } ?>
                                                <?php $m++;
}?>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php $k++;
} ?>
                        </div>
                        <div class="border mt-4 text-center b-radius-5 p-3">
                                            <h4 class="f-w-600"><?= __('Reviewed By'); ?> -</h4>
                                            <span class="d-inline-block"><?= __('Quality'); ?><br /><?= __('Control Manager'); ?></span>
                                            <span class="d-inline-block m-l-40"><?= __('Quality'); ?><br /><?= __('Assurance Representative'); ?></span>
                                        </div>
                    </div>
                </div>
            </div>
