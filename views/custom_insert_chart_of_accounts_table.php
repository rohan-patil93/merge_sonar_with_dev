<?php
if(isset($_POST['pageshow']) && $_POST['pageshow'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $vurl = API."?mode=getchartofaccounts";
    $resultapi = callAPI($vurl);
    $result = json_decode($resultapi,1);
    $chart_of_accounts = $result['chart_of_accounts'] ?? [];
    $account_type = $result['account_type'] ?? [];
    $chartofaccounts = array();

    $financereportgroupsreport = [];
    $financereportgroups = [];
    if (!empty($chart_of_accounts) && is_array($chart_of_accounts)) {
        foreach($chart_of_accounts as $finanacerepogrp){
            $financereportgroupsreport[$finanacerepogrp['parent_account_code']][] = $finanacerepogrp;
            $financereportgroups[$finanacerepogrp['account_code']] = $finanacerepogrp;
        }
    }
}
if (!isset($financereportgroupsreport) || !is_array($financereportgroupsreport)) {
    $financereportgroupsreport = [];
}
if (!isset($financereportgroups) || !is_array($financereportgroups)) {
    $financereportgroups = [];
}
$account_groups = $result['account_groups'] ?? [];
?>
<table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" colspan="2" style="width: 90%;"><?= __('Chart Of Account Name') ?></th>
                                <th scope="col" class="text-right" style="width: 5%;">
                                	<!-- <button class="btn bg-primary" href="#" onclick="editFinanaceGrp(0)"><i class="ti-plus"></i> COA</button> -->
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;
                            $level1_items = $financereportgroupsreport[0] ?? [];
                            foreach($level1_items as $level1){
                            $Alevel2 = $financereportgroupsreport[$level1['account_code']] ?? [];
                            ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level1['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level1['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td colspan="2" class="<?php echo count((array)$Alevel2) !='0'?"f-w-700":"NNN"; ?> w-b-w-s"><span class=""><?php echo $i." - ".$level1['account_name']." - ".$level1['account_short_name']." (".$level1['account_code'].")".((($level1['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level1['account_code'] ?? ''; ?>','<?php echo $level1['account_type'] ?? ''; ?>','<?php echo $level1['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level1['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>
                            </tr>
                    <?php $mk =1; foreach($Alevel2 as $level2){
                        $Alevel3 = $financereportgroupsreport[$level2['account_code']] ?? [];
                        ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level2['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level2['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td  colspan="2" class="<?php echo count((array)$Alevel3) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-10"><?php echo $i.".".$mk." - ".$level2['account_name']." - ".$level2['account_short_name']." (".$level2['account_code'].")".((($level2['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level2['account_code'] ?? ''; ?>','<?php echo $level2['account_type'] ?? ''; ?>','<?php echo $level2['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level2['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>
                            </tr>
                        <?php $mj =1; foreach($Alevel3 as $level3){
                            $Alevel4 = $financereportgroupsreport[$level3['account_code']] ?? [];
                            ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level3['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level3['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td colspan="2" class="<?php echo count((array)$Alevel4) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-20"><?php echo $i.".".$mk.".".$mj." - ".$level3['account_name']." - ".$level3['account_short_name']." (".$level3['account_code'].")".((($level3['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level3['account_code'] ?? ''; ?>','<?php echo $level3['account_type'] ?? ''; ?>','<?php echo $level3['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level3['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>

                            </tr>
                            <?php $mm =1; foreach($Alevel4 as $level4){
                            $Alevel5 = $financereportgroupsreport[$level4['account_code']] ?? [];
                            ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level4['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level4['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td colspan="2" class="<?php echo count((array)$Alevel5) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-30"><?php echo $i.".".$mk.".".$mj.".".$mm." - ".$level4['account_name']." - ".$level4['account_short_name']." (".$level4['account_code'].")".((($level4['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level4['account_code'] ?? ''; ?>','<?php echo $level4['account_type'] ?? ''; ?>','<?php echo $level4['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level4['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>
                            </tr>
                             <?php $jm =1; foreach($Alevel5 as $level5){
                            $Alevel6 = $financereportgroupsreport[$level5['account_code']] ?? [];
                            ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level5['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level5['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td colspan="2" class="<?php echo count((array)$Alevel6) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-40"><?php echo $i.".".$mk.".".$mj.".".$mm.".".$jm." - ".$level5['account_name']." - ".$level5['account_short_name']." (".$level5['account_code'].")".((($level5['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level5['account_code'] ?? ''; ?>','<?php echo $level5['account_type'] ?? ''; ?>','<?php echo $level5['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level5['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>
                            </tr>
                             <?php $jk =1; foreach($Alevel6 as $level6){
                            $Alevel7 = $financereportgroupsreport[$level6['account_code']] ?? [];
                            ?>
                            <tr>
                                <td><button type="button" class="btn p-0 border-0 bg-transparent" onclick="editFinanaceGrp('<?php echo $level6['id'] ?? ''; ?>')" aria-label="Edit <?php echo htmlspecialchars($level6['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-pencil-alt cursor-pointer" aria-hidden="true"></i></button></td>
                                <td colspan="2" class="<?php echo count((array)$Alevel7) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-50"><?php echo $i.".".$mk.".".$mj.".".$mm.".".$jm.".".$jk." - ".$level6['account_name']." - ".$level6['account_short_name']." (".$level6['account_code'].")".((($level6['lock_and_unlock_status'] ?? '') =="Y")?'<span class="pcoded-badge label label-danger m-l-10">' . __('Locked') . '</span>':''); ?></span></td>
                                <td><button type="button" class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level6['account_code'] ?? ''; ?>','<?php echo $level6['account_type'] ?? ''; ?>','<?php echo $level6['accgro_group_id'] ?? ''; ?>')" aria-label="Add sub-account to <?php echo htmlspecialchars($level6['account_name'] ?? '', ENT_QUOTES); ?>"><i class="ti-plus text-white m-l-5" aria-hidden="true"></i></button></td>
                            </tr>
                    <?php $jk++;
                    } ?>
                    <?php $jm++;
                    } ?>
                    <?php $mm++;
                    } ?>
                    <?php $mj++;
                    } ?>
                    <?php $mk++;
                    } ?>
                        <?php  $i++;
                        } //} ?>

                        </tbody> </table>
