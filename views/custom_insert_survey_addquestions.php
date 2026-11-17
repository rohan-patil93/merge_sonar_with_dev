<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

?>

<script type="text/javascript">

	var i = 2;
 function addoptionrows() {
        i++;
        $("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"><input aria-label="<?php echo __('Question Option'); ?>" type="text" class="form-control display-inline" name="survey_question_option[]" placeholder="<?php echo __('Option'); ?>"><div class="input-group-append"><span class="input-group-text bg-primary"><span><i role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="fa fa-trash text-white" onclick="deloptionrows(\'s1rowid'+i+'\')"></i></span></span></div></div></div></div>');
    }
	function deloptionrows(rowid) {
		$('#'+rowid).remove();
	}

	function questionsubmit(){
		var qtype = document.getElementById('qtype').value;
		var title = document.getElementById('title').value;

		var survey_question = document.getElementById('survey_question').value;
		if(survey_question.trim() == ''){
			swal(<?php echo json_encode(__('Validation Error')); ?>, <?php echo json_encode(__('Question text is required.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, 'error');
			return;
		}
		var require_response = document.getElementById('require_response').checked == true ? '0' : '1';
		if(qtype==1 || qtype==3){
			var allow_multiple_selections = document.getElementById('allow_multiple_selections').checked == true ? '0':'1';
		}else{
			var allow_multiple_selections = '1';
		}
		var question_id = document.getElementById('question_id').value;
		var survey_question_option_array = [];
		var survey_question_option = [];
		survey_question_option_array = document.getElementsByName('survey_question_option[]');
		for (var i = 0; i < survey_question_option_array.length; i++) {
			var survey_question_option_value = survey_question_option_array[i].value;
			survey_question_option.push(survey_question_option_value);
		}
		if(qtype == '1' || qtype == '3'){
			var nonemptyOptions = 0;
			for (var j = 0; j < survey_question_option.length; j++) {
				if(survey_question_option[j].trim() != ''){
					nonemptyOptions++;
				}
			}
			if(nonemptyOptions < 2){
				swal(<?php echo json_encode(__('Validation Error')); ?>, <?php echo json_encode(__('Please enter at least 2 options.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, 'error');
				return;
			}
		}
		if(qtype == '2'){
			var scaleVal = survey_question_option.length > 0 ? parseInt(survey_question_option[0], 10) : 0;
			if(isNaN(scaleVal) || scaleVal <= 0){
				swal(<?php echo json_encode(__('Validation Error')); ?>, <?php echo json_encode(__('Rating scale must be greater than 0.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, 'error');
				return;
			}
		}

		var pass_data = "&title="<?php echo __('+title+'); ?>"&type="+qtype+"&question="+survey_question+"&require_response="+require_response+"&allow_multiple_selections="+allow_multiple_selections+"&option_data="+survey_question_option+"&question_id="+question_id+"&rid=";
		console.log(pass_data);
		$.ajax({
			url: "ajax.php",
			data:'page=formsurveyquestions'+pass_data,
			type: "POST",
			success: function(response) {
				console.log(response);
					$('#kt_modal_KTDatatable_setup').modal('hide');
					$("#survey_questions").html(response);
			}
		});
	}
</script>
	<input type="hidden" id="qtype" value="<?php echo $_GET['type'];?>">
	<input type="hidden" id="question_id" value="<?php echo $question_id;?>">

	<input type="hidden" id="title" value="<?php echo $_GET['question_title'];?>">

<div class="col-lg-12" id="<?php echo $question_id;?>">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel"><?php echo $_GET['question_title']; ?></h5>
		 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>"></button>
	</div>
	<div class="card-body">
		<div class="form-group text-left">
			<span class="d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Question Text:'); ?> <span class="text-danger">*</span></span>
			<textarea id="survey_question" name="survey_question" class="form-control" placeholder="" aria-label="<?php echo __('Survey Question'); ?>"> </textarea>
		</div>
		<div class="form-group text-left">
			<div class="checkbox-inline">
				<?php if($_GET['type']==1 || $_GET['type']==3)	{ ?>
					<label class="checkbox checkbox-primary">
						<input type="checkbox" name="allow_multiple_selections" id="allow_multiple_selections" / aria-label="<?php echo __('Allow Multiple Selections'); ?>">
						<span></span>
						<?php echo __('Allow multiple selections'); ?>
					</label>
				<?php } ?>
				<label class="checkbox checkbox-primary">
					<input type="checkbox" value="require_response" id="require_response" aria-label="<?php echo __('Input'); ?>">
					<span></span>
					<?php echo __('Require a Response'); ?>
				</label>
			</div>
		</div>
		<?php if($_GET['type']==1 || $_GET['type']==3)	{ ?>
			<div id="optionrow">
				<div class="form-group row" id="s1rowid1">
					<div class="col-lg-1"></div>
					<div class="col-lg-8">
					    <div class="input-group input-icon-right">
						<input type="text" class="form-control display-inline width1" name="survey_question_option[]"  placeholder="<?php echo __('Option'); ?>" aria-label="<?php echo __('Option'); ?>">
									<span class="input-group-text addon-style">
    <span class=""><i role="button" tabindex="0" aria-label="<?= htmlspecialchars(__('Deloptionrows'), ENT_QUOTES, 'UTF-8'); ?>" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="fa fa-trash" onclick="deloptionrows('s1rowid1')"></i>	</span>
</span></div>
					</div>
				</div>
				<div class="form-group row" id="s1rowid2">
					<div class="col-lg-1"></div>
					<div class="col-lg-8">
					    <div class="input-group input-icon-right">
						<input type="text" class="form-control display-inline width1" name="survey_question_option[]"  placeholder="<?php echo __('Option'); ?>" aria-label="<?php echo __('Option'); ?>">
						<span class="input-group-text addon-style">
    <span class=""><i role="button" tabindex="0" aria-label="<?= htmlspecialchars(__('Deloptionrows'), ENT_QUOTES, 'UTF-8'); ?>" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="fa fa-trash" onclick="deloptionrows('s1rowid2')"></i>	</span>
</span></div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<button class="btn btn-success"  onclick="addoptionrows()" id="addRows" type="button"><?php echo __('+ Add More Options'); ?></button>
			</div>
		<?php } ?>
		<?php if($_GET['type']==2)	{ ?>
			<div class="form-group text-left mt-10">
				<span class="d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Scale from 1 to:'); ?> <span class="text-danger">*</span></span>
				<input type="number" name="survey_question_option[]"  class="form-control" placeholder="10" aria-label="10">
			</div>
		<?php } ?>
		<?php if($_GET['type']==4)	{ ?>
			<input type="hidden" name="survey_question_option[]"  class="form-control" value="">
		<?php } ?>
	</div>
	<div class="modal-footer">
	    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal"><?php echo __('Close'); ?></button>
		<button type="button" class="btn btn-primary mr-2" onclick="questionsubmit()" ><?php echo __('Save'); ?></button>
	</div>

</div>
