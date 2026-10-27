<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$typeofquestion= array('1'=>'MCQ','2'=>'MCA','3'=>'FT');
if($_GET['type']== $typeofquestion['1']){
	$settype = 1;
} else if($_GET['type']== $typeofquestion['2']){
	$settype =2;
}
else{
	$settype =3;
}
?>

<script type="text/javascript">
	var i = 2;
	function addoptionrows() {
		i++;
		var quetype= document.getElementById('settype').value;
		if(quetype==1){
			var appenddiv = '<label class="radio radio-primary"><input type="radio"  name="singlechoicestatus" id="singlechoice'+i+'" ><span></span></label>';
			
		}else{
			var appenddiv = '<label class="checkbox checkbox-primary"><input type="checkbox" name="multiplechoicestatus" id="multiplechoice'+i+'" ><span></span></label>';
		}

		$("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1 mt-2">'+appenddiv+'</div><div class="col-lg-8">  <div class="input-group input-icon-right"> <input type="text" class="form-control display-inline"  name="assessment_question_option[]" placeholder="Option"><div class="input-group-append "><span class="input-group-text bg-primary width1"><span><i class="fa fa-trash" onclick="deloptionrows(\'s1rowid'+i+'\')"></i></span></span></div></div></div></div>');
	}
	function deloptionrows(rowid) {
		$('#'+rowid).remove();
	}

	/*function callsinglechoice(index){
		var marks_input = document.getElementById('singlechoice'+index);
		$("#singlechoice"+index).prop('checked', true);
	}
	function callmultichoice(index) {
		var marks_input = document.getElementById('multiplechoice'+index).checked;
	}*/
	function questionsubmit(){
		var qtype = document.getElementById('settype').value;
		var title = document.getElementById('title').value;

		var assessment_question = document.getElementById('assessment_question').value;
		var question_id = document.getElementById('question_id').value;
		var marksdata = document.getElementById('markfieldvalue').value;
		//alert(marksdata+title);
		
		var assessment_question_option_array = [];
		var assessment_question_option = [];
		var checkedstatus =[];
		var choicestatus;

		assessment_question_option_array = document.getElementsByName('assessment_question_option[]');
		singlechoicestatus = document.getElementsByName('singlechoicestatus');
		multiplechoicestatus = document.getElementsByName('multiplechoicestatus');

		
		console.log("----"+assessment_question_option_array.length);
		for (var i = 0; i < assessment_question_option_array.length; i++) { 

			var assessment_question_option_value = assessment_question_option_array[i].value; 
			console.log(assessment_question_option_value);
			assessment_question_option.push(assessment_question_option_value);
			var k = i+1;
			if(qtype==1){
				choicestatus = document.getElementById('singlechoice'+k).checked == true ? "checked" : "";
			}else if(qtype==2){
				choicestatus = document.getElementById('multiplechoice'+k).checked== true ? "checked" : "";
			}
			checkedstatus.push(choicestatus);
		} 

		var pass_data = "&title="+title+"&type="+qtype+"&question="+assessment_question+"&option_data="+assessment_question_option+"&marks="+marksdata+"&checkedstatus="+checkedstatus+"&question_id="+question_id+"&rid=";
		console.log(pass_data); 
		$.ajax({
			url: "ajax.php",
			data:'page=formassessmentquestions'+pass_data,
			type: "POST",
			success: function(response) {
				console.log(response);
				$('#kt_modal_KTDatatable_setup').modal('hide');
				$("#assessment_questions").html(response);
			}
		});
	}
</script>
<input type="hidden" id="qtype" value="<?php echo $_GET['type'];?>">
<input type="hidden" id="settype" value="<?php echo $settype;?>">

<input type="hidden" id="question_id" value="<?php echo $question_id;?>">


<input type="hidden" id="title" value="<?php echo $_GET['question_title'];?>">

<div class="col-lg-12" id="<?php echo $question_id;?>">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel"><?php echo $_GET['question_title']; ?></h5>
		   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   
	</div>
	<div class="custom-icon p-4">
		<div class="form-group text-left row">
			<div class="col-lg-8">
				<label>Question Text: <span class="text-danger">*</span></label>
				<textarea id="assessment_question" name="assessment_question" class="form-control" placeholder=""> </textarea>
			</div>
			<div class="col-lg-4">
				<label>Marks <span class="text-danger">*</span></label>
				<input type="text" class="form-control" id="markfieldvalue" name="marks"  placeholder="Enter Marks"  >
			</div>
		</div>
		
		<?php if($_GET['type']== $typeofquestion['1'] || $_GET['type']== $typeofquestion['2'])	{ ?>
			<div class="col-lg-12 mt-2 mb-4">	Mark the Correct Answer
		</div>
			<div id="optionrow">
				<div class="form-group row" id="s1rowid1">
					<div class="col-lg-1 mt-2">	<?php if($_GET['type']== $typeofquestion['1'] ) { ?>
						<label class="radio radio-primary">
							<input type="radio" name="singlechoicestatus"  id="singlechoice1" >
							<span></span>
						</label>
					<?php } else { ?>
						<label class="checkbox checkbox-primary">
							<input type="checkbox"  name="multiplechoicestatus" id="multiplechoice1">
							<span></span>
						</label>
					<?php } ?>
				</div>
				<div class="col-lg-8">  
					<div class="input-group input-icon-right">
					<input type="text" class="form-control  display-inline width1" name="assessment_question_option[]"  placeholder="Option">
					<span class="input-group-text addon-style">
    <label class=""><i class="fa fa-trash" onclick="deloptionrows('s1rowid1')"></i>	</label>
</span></div>
				</div>
			</div>
			<div class="form-group row" id="s1rowid2">
				<div class="col-lg-1 mt-2"> <?php if($_GET['type']== $typeofquestion['1'] ) { ?>
					<label class="radio radio-primary">
						<input type="radio"  name="singlechoicestatus"  id="singlechoice2" >
						<span></span>

					</label>
				<?php } else { ?>
					<label class="checkbox checkbox-primary">
						<input type="checkbox"  name="multiplechoicestatus" id="multiplechoice2" >
						<span></span>
					</label>
				<?php } ?>
			</div>
			<div class="col-lg-8">
				<div class="input-group input-icon-right">
				<input type="text" class="form-control  display-inline width1" name="assessment_question_option[]"   placeholder="Option">
				
						<span class="input-group-text addon-style">
    <label class=""><i class="fa fa-trash" onclick="deloptionrows('s1rowid1')"></i>	</label>
</span>
				
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<button class="btn btn-success"  onclick="addoptionrows()" id="addRows" type="button">+ Add More Options</button>
	</div>
<?php } 
else { ?>

	<input type="hidden" name="assessment_question_option[]"  class="form-control" value=""> 

<?php } ?>	
	<div class="modal-footer">
	    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary mr-2" onclick="questionsubmit()" >Save</button>
	<!--	<button class="btn btn-success"  onclick="questionsubmit()" type="button">Save</button>-->
	</div>

</div>