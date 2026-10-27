<div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Key Result Area</th>
                                    <th scope="col">KRA Score EMP/RO's</th>
                                    <th scope="col">Key Performance Indicators</th>
                                    <th scope="col">Weightage</th>
                                    <th scope="col">Target / Achieved (self Score)</th>
                                    <th scope="col">Points Scored<br />(self)</th>
                                    <th scope="col">Percentage achieved<br />(RO's Score)</th>
                                    <th scope="col">Points Scored<br />(RO)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $mk = 1; $totaltarget=0; $totalweightage = 0; $kratype='2';
                                $totalself_achievement = 0;
                                $totalachievement_percent = 0;
                                $totalapproved_achievement = 0;
                                $totalapproved_score =  0;
                                $totalemp_kra_Score = 0;
                                $totalro_kra_Score = $kraweightageshow =  0;
                                foreach($kra_list[2] as $rakey=>$kra_details){
                                    $cntrow = count((array)$kra_details);
                                    $totalweightage += $kraweightage[$rakey];
                                    $kraweightageshow = ($cntrow !="" && $cntrow !=0)?$kraweightage[$rakey]/$cntrow:$kraweightage[$rakey]/1;
                                    foreach($kra_details as $kra_detai){
                                        $empsumkpi[$kra_detai['key_reslut_id']] += $kra_detai['achievement_percent'];
                                        $rosumkpi[$kra_detai['key_reslut_id']] += $kra_detai['approved_score'];
                                    }
                                    $emp_kra_Score = ($kraweightageshow*$empsumkpi[$kra_detai['key_reslut_id']]/100);
                                    $ro_kra_Score = ($kraweightageshow*$rosumkpi[$kra_detai['key_reslut_id']]/100);
                                    $totalemp_kra_Score += $emp_kra_Score;
                                    $totalro_kra_Score    += $ro_kra_Score;
                                    ?>
                                    <tr>
                                        <th scope="col" rowspan="<?php echo $cntrow; ?>"><?php echo $mk; ?></th>
                                        <td rowspan="<?php echo $cntrow; ?>">
                                            <?php echo $kraname[$rakey]."(".$kraweightage[$rakey].")"; ?>
                                            <input type="hidden" id="kraweightage_<?php echo $rakey; ?>" value="<?php echo $cntrow !="" && $cntrow !=0?$kraweightage[$rakey]/$cntrow:$kraweightage[$rakey]/1;?>">
                                        </td>
                                        <td rowspan="<?php echo $cntrow; ?>">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input aria-label="Input field" type="text" class="form-control kraabsoluterating_<?php echo $kratype;?>" value="<?php echo $emp_kra_Score; ?>" id="kraabsoluterating_<?php echo $rakey; ?>" value="" readonly>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input aria-label="Input field" type="text" class="form-control managerkraabsoluterating_<?php echo $kratype;?>" value="<?php echo $ro_kra_Score; ?>" id="managerkraabsoluterating_<?php echo $rakey; ?>" value="" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <?php foreach($kra_details as $kra_detai){
                                            $totalself_achievement += $kra_detai['self_achievement'];
                                            $totalachievement_percent += $kra_detai['achievement_percent'];
                                            $totalapproved_achievement += $kra_detai['approved_achievement'];
                                            $totalapproved_score += $kra_detai['approved_score'];
                                            $totaltarget += $kra_detai['traget']; ?>
                                        <td><?php echo $kra_detai['indicator_name']; ?></td>
                                        <td>
                                            <div class="">
                                                <input type="hidden" class="form-control" name="emp_goal_id[]"  value="<?php echo $kra_detai['id']; ?>" >
                                                <input aria-label="Input field" type="text" class="form-control" id="weightage_<?php echo $kra_detai['id']; ?>"  value="<?php echo $kra_detai['weightage']; ?>" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <input aria-label="Input field" type="text" class="form-control " id="self_target_<?php echo $kra_detai['id']; ?>" value="<?php echo $kra_detai['traget']; ?>" readonly>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <input aria-label="Enter number" type="number" class="form-control col-lg-6 achived_emp_<?php echo $kratype;?>"  id="self_achieved_<?php echo $kra_detai['id']; ?>" onblur="getAbsoluteRatting(<?php echo $kra_detai['id']; ?>,'<?php echo $rakey; ?>','<?php echo $kratype;?>','emp')" name="self_achievement[]" value="<?php echo $kra_detai['self_achievement']; ?>"  <?php echo $loginuser_type != "EMP"?"readonly":""; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <input aria-label="Input field" type="text" class="form-control absoluterating_<?php echo $rakey; ?> sumabsoluterating_<?php echo $kratype;?>"  id="self_scored_<?php echo $kra_detai['id']; ?>"  name="achievement_percent[]" value="<?php echo $kra_detai['achievement_percent']; ?>"  readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <input aria-label="Enter number" type="number" class="form-control achived_RO_<?php echo $kratype;?>" id="RO_achieved_<?php echo $kra_detai['id']; ?>"  onblur="getAbsoluteRattingManager(<?php echo $kra_detai['id']; ?>,'<?php echo $rakey; ?>','<?php echo $kratype;?>','RO')" name="approved_achievement[]" value="<?php echo $kra_detai['approved_achievement']; ?>" <?php echo $loginuser_type != "RO"?"readonly":""; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <input aria-label="Input field" type="text" class="form-control RO_absoluterating_<?php echo $rakey; ?> RO_sumabsoluterating_<?php echo $kratype;?>" id="RO_scored_<?php echo $kra_detai['id']; ?>" name="approved_score[]" value="<?php echo $kra_detai['approved_score']; ?>" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php $mk++;
} ?>

                                <tr><th scope="col" ></th>
                                    <th scope="col" ><input aria-label="Input field" type="text" class="form-control"  value="<?php echo $totalweightage; ?>" readonly></th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input aria-label="Input field" type="text" class="form-control" id="kratotal_<?php echo $kratype;?>" value="<?php echo $totalemp_kra_Score; ?>" readonly>
                                            </div>
                                            <div class="col-lg-6">
                                                <input aria-label="Input field" type="text" class="form-control"  id="managerkratotal_<?php echo $kratype;?>" value="<?php echo $totalro_kra_Score; ?>"  readonly>
                                            </div>
                                        </div>
                                    </td>
                                    <th scope="col" colspan="2" class="text-center">Total</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo $totaltarget; ?>" readonly="">
                                            </div>
                                            <div class="col-lg-6">
                                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo $totalself_achievement; ?>" id="emp_achive_total_<?php echo $kratype;?>" readonly>
                                            </div>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="">
                                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $totalachievement_percent; ?>" id="emp_absoluteratin_total_<?php echo $kratype;?>"  readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $totalapproved_achievement; ?>"  id="RO_achive_total_<?php echo $kratype;?>" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input aria-label="Input field" type="text" class="form-control" id="RO_absoluteratin_total_<?php echo $kratype;?>" value="<?php echo $totalapproved_score; ?>" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                        $emptotalachived += $totalself_achievement;
                                        $emptotaltarget += $totaltarget;
                                        $rototalachived += $totalapproved_achievement;
                                        if($emptotaltarget !=0 && $emptotaltarget !=""){
                                            $emptotalshow  = ($emptotalachived*(100/1));
                                            $rototalshow  = ($rototalachived*(100/1));

                                        }else{
                                            $emptotalshow  = ($emptotalachived*(100/$emptotaltarget));
                                            $rototalshow  = ($rototalachived*(100/$emptotaltarget));
                                        }

                                    ?>
                                    <th scope="col" colspan="3" class="text-center">Total Percentage(%)</th>
                                    <td colspan="2">
                                        <div class="row">

                                        <input type="hidden" class="form-control" id="text_finalkra_total_employee" name="text_finalkra_total_employee" value="<?php echo $emptotalshow; ?>">
                                        <input type="hidden" class="form-control" id="text_finalkra_total_manager" name="text_finalkra_total_manager" value="<?php echo $rototalshow; ?>">
                                            <div class="col-lg-6">
                                                <div class="input-group input-group-button">
                                                    <input aria-label="Employee Total" type="text" class="form-control width1" id="finalkra_total_employee" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="Employee Total" value="<?php echo round($emptotalshow,2)."%"; ?>">
                                                    <span class="input-group-text addon-style">
                                                        <span class=""><i class="fa fa-user"></i></span>
                                                    </span>
                                                </div>

                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group input-group-button">
                                                    <input aria-label="Manager Total" type="text" class="form-control width1" id="finalkra_total_manager" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="Manager Total" value="<?php echo round($rototalshow,2)."%"; ?>">
                                                    <span class="input-group-text addon-style">
                                                        <span class=""><i class="fa fa-user-secret"></i></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <th scope="col" colspan="3" ></th>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="grade-span">
                                            <h4>Grade</h4>
                                            <?php foreach($arslabs as $arslab){ ?>
                                                <span class="label label-lg  label-inline" style="background-color:<?php echo $arslab['background_color'];?> "><b><?php echo $arslab['slabs_name'];?></b></span>
                                            <?php } ?>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
