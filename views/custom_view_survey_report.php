<?php
//$survey_id = 3;
$vurl = API."?mode=getsurveyreport&company_id=".$company_id."&is_active=0&survey_id=".$survey_id;
$configesponse = callAPI($vurl);
$surveyData = json_decode($configesponse,1);
$maxoptions = isset($surveyData['maxoptions']) ? (int)$surveyData['maxoptions'] : 0;
//echo "<pre>";print_r($surveyData); exit;
?>

<div class="col-lg-12">
	<table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1">
		<!-- <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1"> -->
			<thead>
				<tr>
					<th></th>
					<th> </th>
					<td class="text-primary text-center font-size-h5" colspan="<?php echo $maxoptions; ?>">Option  </td>
				</tr>
			</thead>
			<thead>
				<tr>
					<th class="font-size-h5">#</th>
					<th width="30%" class="font-size-h5">Questions </th>
					<?php for($op_count=1; $op_count<=$maxoptions; $op_count++) { ?>
					<th class="font-size-h5"><?php echo $op_count; ?> </th>
					<?php } ?>
				</tr>
			</thead>
			<tbody> 
				<?php if(count((array)$surveyData['survey_question']) > 0){
					$id = 0;
					foreach($surveyData['survey_question'] as $qkey=>$que) { $id++; ?>
				<tr>
					<td><?php echo $id; ?> </td>
					<td><?php echo $que; ?> </td>
					<?php if(count((array)$surveyData['option'][$qkey]['option']) > 0){
						foreach($surveyData['option'][$qkey]['option'] as $optionvalue) { 
						?>
					<td ><span class="text-muted font-size-h6"><?php echo $optionvalue; ?></span>  </td>
					<?php }
					} ?>
				</tr>

				<tr>
					<td></td>
					<td><span class="text-success">Number of Responded </span></td>
					<?php if(count((array)$surveyData['option'][$qkey]['answer']) > 0){
						foreach($surveyData['option'][$qkey]['answer'] as $optionans) { 
						?>
					<td> <span class="font-weight-bolder label label-xl label-success "  data-toggle="tooltip" title="Number Of Responded" style="vertical-align: middle;"><?php echo $optionans; ?> </span> </td>
					<?php }
					} ?>
				</tr>
				<?php }
				}else{ ?>
				<tr>
					<td colspan="<?php echo $maxoptions + 2; ?>" class="text-center text-muted">No report data available</td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
