<?php
$vurl = API."?mode=getassessmentreport&company_id=".$company_id."&is_active=0&assessment_id=".$assessment_id;
$configesponse = callAPI($vurl);
$assessmentData = json_decode($configesponse,1);
?>

<div class="col-lg-12">
    <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1">

            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"> </th>
                    <td class="text-primary text-center font-size-h5" colspan="<?php echo $assessmentData['maxoptions']; ?>">Option  </td>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th scope="col" class="font-size-h5">#</th>
                    <th scope="col" class="font-size-h5" style="width: 30%;">Questions </th>
                    <?php for($op_count=1; $op_count<=$assessmentData['maxoptions']; $op_count++) { ?>
                    <th scope="col" class="font-size-h5"><?php echo $op_count; ?> </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $id = 0; foreach($assessmentData['assessment_question'] as $qkey=>$que) { $id++; ?>
                <tr>
                    <td><?php echo $id; ?> </td>
                    <td><?php echo $que; ?> </td>
                    <?php foreach($assessmentData['option'][$qkey]['option'] as $optionvalue) {
                        ?>
                    <td ><span class="text-muted font-size-h6"><?php echo $optionvalue; ?></span>  </td>
                    <?php } ?>
                </tr>

                <tr>
                    <td></td>
                    <td><span class="text-success">Number of Responded </span></td>
                    <?php foreach($assessmentData['option'][$qkey]['answer'] as $optionans) {
                        ?>
                    <td> <span class="font-weight-bolder label label-xl label-success "  data-toggle="tooltip" title="Number Of Responded" style="vertical-align: middle;"><?php echo $optionans; ?> </span> </td>
                    <?php } ?>
                </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
