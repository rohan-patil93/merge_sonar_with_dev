<?php

if(isset($_POST['pageshow']) && $_POST['pageshow'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $fr_id = $_POST['fr_id'];
    $vurl = API."?mode=getfinanacereportgroups&fr_id=".$fr_id;
    $resultapi = callAPI($vurl);
    $result = json_decode($resultapi,1);
    $chart_of_accounts =$result['chart_of_accounts'];
    $finance_report_groups =$result['finance_report_groups'];
    $chartofaccounts = array();
    foreach($finance_report_groups as $finanacerepogrp){
        $financereportgroupsreport[$finanacerepogrp['parent_group_id']][$finanacerepogrp['id']] = $finanacerepogrp;
        $financereportgroups[$finanacerepogrp['id']] = $finanacerepogrp;
    }
}
?>
<table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" colspan="2" style="width: 90%;">Group Name</th>
                                <th scope="col" class="text-right" style="width: 5%;">
                                    <button class="btn bg-primary" href="#" onclick="editFinanaceGrp(0)"><i class="ti-plus"></i> FRG</button>
                                </th>

                            </tr>
                        </thead>
                        <tbody>

                        <?php $i = 1; foreach($financereportgroupsreport[0] as $level1){
                            $Alevel2 = $financereportgroupsreport[$level1['id']];
                            ?>
                            <tr>
                                <th scope="col" ><i class="ti-pencil-alt" onclick="editFinanaceGrp('<?php echo $level1['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></th>
                                <td colspan="2" class="<?php echo count((array)$Alevel2) !='0'?"f-w-700":"NNN"; ?> w-b-w-s"><span class=""><?php echo $i.") ".$level1['group_name']." - ".$level1['short_code']; ?></span></td>
                                <td><button class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level1['id']; ?>')"><i class="ti-plus text-white m-l-5"></i></button></td>
                            </tr>
                    <?php $mk ='A'; foreach($Alevel2 as $level2){
                        $Alevel3 = $financereportgroupsreport[$level2['id']];
                        ?>
                            <tr>
                                <th scope="col"> <i class="ti-pencil-alt" onclick="editFinanaceGrp('<?php echo $level2['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></th>
                                <td  colspan="2" class="<?php echo count((array)$Alevel3) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-20"><?php echo $mk.") ".$level2['group_name']." - ".$level2['short_code']; ?></span></td>
                                <td><button class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level2['id']; ?>')"><i class="ti-plus text-white m-l-5"></i></button></td>
                            </tr>
                        <?php $mj ="a"; foreach($Alevel3 as $level3){
                            $Alevel4 = $financereportgroupsreport[$level3['id']];
                            ?>
                            <tr>
                                <th scope="col" ><i class="ti-pencil-alt" onclick="editFinanaceGrp('<?php echo $level3['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></th>
                                <td colspan="2" class="<?php echo count((array)$Alevel4) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-40"><?php echo $mj.") ".$level3['group_name']." - ".$level3['short_code']; ?></span></td>
                                <td><button class="btn bg-primary p-0 pt-1"  onclick="editFinanaceGrp(0,'<?php echo $level3['id']; ?>')"><i class="ti-plus text-white m-l-5"></i></button></td>

                            </tr>
                            <?php $mm =1; foreach($Alevel4 as $level4){
                            $Alevel5 = $financereportgroupsreport[$level4['id']];
                            ?>
                            <tr>
                                <th scope="col" ><i class="ti-pencil-alt" onclick="editFinanaceGrp('<?php echo $level4['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></th>
                                <td colspan="2" class="<?php echo count((array)$Alevel5) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-60"><?php echo $mm.") ".$level4['group_name']." - ".$level4['short_code']; ?></span></td>
                                <td><button class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level4['id']; ?>')"><i class="ti-plus text-white m-l-5"></i></button></td>
                            </tr>
                             <?php $jm =1; foreach($Alevel5 as $level5){
                            $Alevel6 = $financereportgroupsreport[$level5['id']];
                            ?>
                            <tr>
                                <th scope="col" ><i class="ti-pencil-alt" onclick="editFinanaceGrp('<?php echo $level5['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></th>
                                <td colspan="2" class="<?php echo count((array)$Alevel6) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-80"><?php echo $jm.") ".$level5['group_name']." - ".$level5['short_code']; ?></span></td>
                                <td><button class="btn bg-primary p-0 pt-1" onclick="editFinanaceGrp(0,'<?php echo $level5['id']; ?>')"><i class="ti-plus text-white m-l-5"></i></button></td>
                            </tr>
                    <?php $jm++;
} ?>
                    <?php $mm++;
} ?>
                    <?php $mj++;
} ?>
                    <?php $mk++;
} ?>
                        <?php  $i++;
} ?>

                        </tbody> </table>
