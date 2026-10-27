<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

?>

<script type="text/javascript">

    var i = 2;
 function addoptionrows() {
        i++;
        $("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-group-button "> <input aria-label="Option" type="text" name="survey_question_option[]"  placeholder="Option" class="form-control text-right width1"> <button class="btn btn-danger" type="button" onclick="deloptionrows(\'s1rowid'+i+'\')"><i class="ti-trash"></i></button> </div></div></div>');
    }
    function deloptionrows(rowid) {
        $('#'+rowid).remove();
    }

    function questionsubmit(){
        var qtype = document.getElementById('qtype').value;
        var title = document.getElementById('title').value;

        var survey_question = document.getElementById('survey_question').value;
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
        console.log(survey_question_option_array.length);
        for (var i = 0; i < survey_question_option_array.length; i++) {
            var survey_question_option_value = survey_question_option_array[i].value;
            console.log(survey_question_option_value);
            survey_question_option.push(survey_question_option_value);
        }

        var pass_data = "&title="+title+"&type="+qtype+"&question="+survey_question+"&require_response="+require_response+"&allow_multiple_selections="+allow_multiple_selections+"&option_data="+survey_question_option+"&question_id="+question_id+"&requir_ratting="+$("#requir_ratting").val()+"&rid=";
        console.log(pass_data);
        $.ajax({
            url: "ajax.php",
            data:'page=formcandidatefeedbackquestions'+pass_data,
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

<div class="col-lg-12 j-content" id="<?php echo $question_id;?>">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo $_GET['question_title']; ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    </div>
    <div class="card-body">
        <div class="row col-lg-12">
        <div class="form-group col-lg-7 text-left">
            <span>Question Text: <span class="text-danger">*</span></span>
            <textarea aria-label="Survey Question" id="survey_question" name="survey_question" class="form-control" placeholder=""> </textarea>
        </div>
        <div class="form-group col-lg-5 text-left">
            <span>Required Ratting(Out of 5): <span class="text-danger">*</span></span>
            <input aria-label="Requir Ratting" type="text" class="form-control" name="requir_ratting" id="requir_ratting">
        </div>
        </div>
        <div class="form-group text-left">

                <?php if($_GET['type']==1 || $_GET['type']==3)    { ?>
                    <div class="checkbox-fade fade-in-primary">
  <label class="form-label">
                        <input aria-label="Allow Multiple Selections" type="checkbox" name="allow_multiple_selections" id="allow_multiple_selections" />
                         <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span>
                        Allow multiple selections
                    </label></div>
                <?php } ?>
                <div class="checkbox-fade fade-in-primary">
  <label class="form-label">
                    <input aria-label="Require Response" type="checkbox" value="require_response" id="require_response">
                     <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span>
                    Require a Response
                </label></div>

        </div>
        <?php if($_GET['type']==1 || $_GET['type']==3)    { ?>
            <div id="optionrow">
                <div class=" row" id="s1rowid1">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8 form-group">
  <div class="input-group input-group-button ">
      <input aria-label="Option" type="text" name="survey_question_option[]" placeholder="Option" class="form-control text-right width1"> <button class="btn btn-danger" type="button" onclick="deloptionrows('s1rowid1')"><i class="ti-trash"></i></button> </div>

                    </div>
                </div>
                <div class="form-group row" id="s1rowid2">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8">


  <div class="input-group input-group-button ">
      <input aria-label="Option" type="text" name="survey_question_option[]"  placeholder="Option" class="form-control text-right width1"> <button class="btn btn-danger" type="button" onclick="deloptionrows('s1rowid2')"><i class="ti-trash"></i></button> </div>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-success"  onclick="addoptionrows()" id="addRows" type="button">+ Add More Options</button>
            </div>
        <?php } ?>
        <?php if($_GET['type']==2)    { ?>
            <div class="form-group text-left mt-10">
                <span>Scale from 1 to: <span class="text-danger">*</span></span>
                <input aria-label="10" type="number" name="survey_question_option[]"  class="form-control" placeholder="10">
            </div>
        <?php } ?>
        <?php if($_GET['type']==4)    { ?>
            <input type="hidden" name="survey_question_option[]"  class="form-control" value="">
        <?php } ?>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary mr-2" onclick="questionsubmit()" >Save</button>

    </div>

</div>
