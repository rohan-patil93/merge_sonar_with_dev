<?php
$modulwithid = array();
foreach ($modellist as  $model) {
    $modulwithid[$model['genc_detail_code']] = $model['genc_detail_name'];
}
$umlwithid = array();
foreach ($upmllist as  $uml) {
    $umlwithid[$uml['genc_detail_code']] = $uml['genc_detail_name'];
}
foreach($kralist as $kralist){ ?>
    <div class="card" id="deletekra_<?php echo $kralist['id']; ?>">
        <div class="card-body">
            <div class="card-header">
                <h5><?php echo $kralist['area_name']; ?></h5>
                <div class="card-header-right">
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <table class="table table-hover" id="invoiceItem_<?php echo $kralist['id']; ?>">
                        <tr>
                            <th scope="col" class="text-center" style="width: 20%;">Indicator Name </th>
                            <th scope="col" class="text-center" style="width: 20%;">Model </th>
                            <th scope="col" class="text-center" style="width: 15%;">UOM </th>
                            <th scope="col" class="text-center" style="width: 10%;">Weightage </th>
                            <th scope="col" class="text-center" style="width: 20%;">Frequency </th>
                            <th scope="col" class="text-center" style="width: 20%;">Target </th>
                        </tr>
                        <?php $i=1;  $total = 0;
                        if(count((array)$kpilist[$kralist['id']]) == '0'){
                            $kpilist[$kralist['id']][] = array();
                        }
                        foreach ($kpilist[$kralist['id']] as $res){
                            if($kpiderails !=""){
                                $resultkpi = $kpiderails[$res['id']];
                                $res['assessment_frequency'] = $resultkpi['assessment_frequency'];
                                $res['traget'] = $resultkpi['traget'];
                            }
                            ?>
                            <tr>
                                <td style="word-break: break-word;white-space: initial;">
                                    <?php echo $res['indicator_name']; ?>
                                </td>
                                <td>
                                    <?php echo $modulwithid[$res['model']]; ?>

                                </td>
                                <td>
                                    <?php echo $umlwithid[$res['uom']]; ?>

                                </td>
                                <td>
                                    <?php echo $res['weightage']; ?>

                                </td>
                                <td>
                                    <input type="hidden" name="goalsid[<?php echo $kralist['id']; ?>][<?php echo $res['id']; ?>]" value="<?php echo $resultkpi['ogid']; ?>">
                                    <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="assessment_frequency[<?php echo $kralist['id']; ?>][<?php echo $res['id']; ?>]"  required>
                                        <option value="">Select Any One</option>
                                        <?php foreach ($frequencyllist as  $frequency) { ?>
                                            <option <?php if($res['assessment_frequency'] == $frequency['genc_detail_code']){ ?> selected <?php
} ?> value="<?php echo $frequency['genc_detail_code']; ?>"><?php echo $frequency['genc_detail_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input aria-label="Input field" type="text" name="traget[<?php echo $kralist['id']; ?>][<?php echo $res['id']; ?>]"  onblur="weightagecheck(<?php echo $kralist['id']; ?>)"  class="form-control  traget_<?php echo $kralist['id']; ?>" value="<?php echo $res['traget']; ?>" required >
                                    <input type="hidden" name="kpi_hd_id[<?php echo $kralist['id']; ?>][]" value="<?php echo $res['id']; ?>">
                                </td>
                            </tr>
                            <?php $i++; $total = $total+$res['weightage'];
} ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
