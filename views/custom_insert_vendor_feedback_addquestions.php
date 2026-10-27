<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

?>

<script type="text/javascript">

    var i = 2;
 function addoptionrows() {
        i++;
        $("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"><input aria-label="Option" type="text" class="form-control display-inline"  name="survey_question_option[]" placeholder="Option" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><div class="input-group-append"><span class="input-group-text bg-primary"><span><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-white" onclick="deloptionrows(\'s1rowid'+i+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></span></span></div></div></div></div>');
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

        var pass_data = "&title="+title+"&type="+qtype+"&question="+survey_question+"&require_response="+require_response+"&allow_multiple_selections="+allow_multiple_selections+"&option_data="+survey_question_option+"&question_id="+question_id+"&rid=";
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
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="card-body">
        <div class="form-group text-left">
            <span>Question Text: <span class="text-danger">*</span></span>
            <textarea aria-label="Survey Question" id="survey_question" name="survey_question" class="form-control" placeholder=""> </textarea>
        </div>
        <div class="form-group text-left">
            <div class="checkbox-inline">
                <?php if($_GET['type']==1 || $_GET['type']==3)    { ?>
                    <label class="checkbox checkbox-primary">
                        <input aria-label="Allow Multiple Selections" type="checkbox" name="allow_multiple_selections" id="allow_multiple_selections" />
                        <span></span>
                        Allow multiple selections
                    </label>
                <?php } ?>
                <label class="checkbox checkbox-primary">
                    <input aria-label="Require Response" type="checkbox" value="require_response" id="require_response">
                    <span></span>
                    Require a Response
                </label>
            </div>
        </div>
        <?php if($_GET['type']==1 || $_GET['type']==3)    { ?>
            <div id="optionrow">
                <div class="form-group row" id="s1rowid1">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8">
                        <div class="input-group input-icon-right">
                        <input aria-label="Option" type="text" class="form-control display-inline width1" name="survey_question_option[]"  placeholder="Option">
                                    <span class="input-group-text addon-style">
    <span class=""><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash" onclick="deloptionrows('s1rowid1')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>    </span>
</span></div>
                    </div>
                </div>
                <div class="form-group row" id="s1rowid2">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8">
                        <div class="input-group input-icon-right">
                        <input aria-label="Option" type="text" class="form-control display-inline width1" name="survey_question_option[]"  placeholder="Option">
                        <span class="input-group-text addon-style">
    <span class=""><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash" onclick="deloptionrows('s1rowid2')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>    </span>
</span></div>
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
        <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary mr-2" onclick="questionsubmit()" >Save</button>

    </div>

</div>
