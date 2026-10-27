
<?php
$vurl = API."?mode=getmonthlyctcreports";
$configesponse = callAPI($vurl);
$resulta = json_decode($configesponse,1);
$unique = $resulta['componant'];
function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}

?>
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card j-pro">
                <div class="card-header">
                    <h5>Salary Components</h5>
                    <div class="card-header-right">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="dt-responsive table-responsive mt-2">
                    <table id="basic-btn" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col"> SL NO </th>
                                <th scope="col">Display name</th>
                                <th scope="col">Emp Code</th>
                                <th scope="col">DOJ</th>
                                <th scope="col">Department</th>
                                <th scope="col">Designation</th>

                                <?php foreach($unique as $names){ ?>
                                    <th scope="col"><?php echo "S_".$names; ?></th>
                                <?php } ?>
                                <th scope="col">Month Days </th>
                                <th scope="col">Payable Days</th>
                                <?php foreach($unique as $names){ ?>
                                    <th scope="col"><?php echo "A_".$names; ?></th>
                                <?php } ?>
                                <th scope="col">Net Payable</th>
                                <th scope="col"> EPF Wages     </th>
                                <th scope="col"> ESIC Wages </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($resulta['list'] as $key=>$rows){   $rows = $rows[0];?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo valueemtpydash($rows['Employee Name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['emp_code']); ?></td>
                                    <td><?php echo valueemtpydash($rows['DOJ']); ?></td>
                                    <td><?php echo valueemtpydash($rows['Department']); ?></td>
                                    <td><?php echo valueemtpydash($rows['Designation']); ?></td>
                                    <?php foreach($unique as $names1){ ?>
                                        <td><?php
                                        $listvalue = filterArrayValue($resulta['list'][$key],'name',$names1);
                                        $vall = $listvalue[0]['salary_component_standard']==""?"-":$listvalue[0]['salary_component_standard'];

                                        echo customeummarmonyformate(round($vall))
                                        ?></td>
                                    <?php } ?>
                                    <td><?php echo valueemtpydash($rows['Total Days']); ?></td>
                                    <td><?php echo valueemtpydash($rows['Payable Days']); ?></td>

                                    <?php $npb=0; $epf_w=0; $esic_w=0; foreach($unique as $names1){ ?>
                                        <td><?php
                                        $listvalue = filterArrayValue($resulta['list'][$key],'name',$names1);
                                        $vall = $listvalue[0]['salary_component_actual']==""?"-":$listvalue[0]['salary_component_actual'];
                                        if($listvalue[0]['name']=='Net Salary'){ $npb=$npb+$vall;
}
                                        if($listvalue[0]['name']=='Advance'){ $npb=$npb-$vall;
}
                                        if($listvalue[0]['name']=='TDS'){ $npb=$npb-$vall;
}
                                        if($listvalue[0]['name']=='Provident Fund'){ $epf_w=$listvalue[0]['base_amount'];
}
                                        if($listvalue[0]['name']=='ESI'){ $esic_w=$listvalue[0]['base_amount'];
}
                                        echo customeummarmonyformate(round($vall));
                                        ?></td>
                                    <?php } ?>
                                    <td> <?php echo customeummarmonyformate(round($npb)); ?></td>
                                    <td> <?php echo customeummarmonyformate(round($epf_w)); ?></td>
                                    <td> <?php echo customeummarmonyformate(round($esic_w)); ?></td>
                                </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
