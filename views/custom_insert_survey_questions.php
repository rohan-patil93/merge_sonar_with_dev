<?php
print_r($_POST);
 $starray['0'] =array("options_display_type"=>$_POST['type'],"survey_question"=>"what is MCQ1 ","require_response"=>"1","allow_multiple_selections"=>"1","survey_question_option"=>array('0'=>"value1","1"=>"value2" ));

?>
<script type="text/javascript">
    var i = 2;
    function addoptionrows() {
        i++;
        $("#optionrow").append('<div class="form-group row" id="rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-11"><input aria-label="Option" type="text" class="form-control" name="survey_question_option1[]" placeholder="Option" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><button type="button" class="flaticon-delete btn-primary" onclick="deloptionrows(\'rowid'+i+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>');
    }
    function deloptionrows(rowid) {
        $('#'+rowid).remove();
    }
    function addoptionrows_rank() {
        i++;
        $("#optionrow_rank").append('<div class="form-group row" id="rankrow'+i+'"><div class="col-lg-1"></div><div class="col-lg-11"><input aria-label="Option" type="text" class="form-control" name="survey_question_option3[]" placeholder="Option" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><button type="button" class="flaticon-delete btn-primary" onclick="deloptionrows(\'rankrow'+i+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>');
    }
    function deloptionrows_rank(rowid) {
        $('#'+rowid).remove();
    }

    function questionsubmit_mcq() {
        var str ="";
        var survey_question_option=[];
        var options_display_type = document.getElementById('options_display_type1').value;
        var survey_question = document.getElementById('survey_question1').value;
        var qustion_idlist = document.getElementById('qustion_idlist').value;
        var require_response = 0;
        $('input[name="require_response1"]:checked').each(function() {
            require_response = 1;
        });
        var allow_multiple_selections = 0;
        $('input[name="allow_multiple_selections1"]:checked').each(function() {
            allow_multiple_selections = 1;
        });
        var survey_question_option_array = document.getElementsByName('survey_question_option1[]');
        for (var i = 0; i < survey_question_option_array.length; i++) {
            var survey_question_option_value = survey_question_option_array[i];
            survey_question_option.push(survey_question_option_value.value);
        }
        console.log(survey_question_option);

        var display_div=  '<div class="qlist"><h5>'+survey_question+'</h5> <div class="form-group">';
        if(allow_multiple_selections==0)  {
            display_div+='<div class="radio-list">';
            for (var i = 0; i < survey_question_option_array.length; i++) {
             survey_question_option_value = survey_question_option_array[i];

        display_div+='<label class="radio"><input aria-label="Survey Question Option1[]" type="radio" name="survey_question_option1[]"/><span></span>'+survey_question_option_value.value+'</label>';
            }
        }else {
            display_div+='<div class="checkbox-list">';
            for (var i = 0; i < survey_question_option_array.length; i++) {
             survey_question_option_value = survey_question_option_array[i];

        display_div+='<label class="checkbox"><input aria-label="Survey Question Option1[]" type="checkbox" name="survey_question_option1[]"/><span></span>'+survey_question_option_value.value+'</label>';
            }
        }
        display_div +='</div></div> </div>';
        $("#survey_questions").append(display_div);


  $('#kt_modal_KTDatatable_setup').modal('hide');
    }

function questionsubmit_rating() {
    var str ="";
    var survey_question_option=[];
    var options_display_type = document.getElementById('options_display_type2').value;
    var survey_question = document.getElementById('survey_question2').value;
    var qustion_idlist = document.getElementById('qustion_idlist').value;
    var require_response = 0;
    $('input[name="require_response2"]:checked').each(function() {
        require_response = 1;
    });
    var allow_multiple_selections = 0;

    var survey_question_option_array = document.getElementsByName('survey_question_option2[]');

    for (var i = 0; i < survey_question_option_array.length; i++) {
        var survey_question_option_value = survey_question_option_array[i];
        survey_question_option.push(survey_question_option_value.value);

    }

    console.log(survey_question_option);
    str = '&options_display_type='+options_display_type+'&survey_question='+survey_question+'&survey_question_option='+survey_question_option+'&qustion_idlist='+qustion_idlist+'&require_response='+require_response+'&allow_multiple_selections='+allow_multiple_selections;
    $.ajax({
    url: "ajax.php",
    data:'page=addsurveyquestions'+str,
    type: "POST",
    success: function(response) {
        console.log(response);
        document.getElementById('qustion_idlist').value = jsonresponse;
    }
});

}

function questionsubmit_ranking() {
    var str ="";
    var survey_question_option=[];
    var options_display_type = document.getElementById('options_display_type3').value;
    var survey_question = document.getElementById('survey_question3').value;
    var qustion_idlist = document.getElementById('qustion_idlist').value;
    var require_response = 0;
    $('input[name="require_response3"]:checked').each(function() {
        require_response = 1;
    });
    var allow_multiple_selections = 0;
    $('input[name="allow_multiple_selections2"]:checked').each(function() {
        allow_multiple_selections = 1;
    });
    var survey_question_option_array = document.getElementsByName('survey_question_option3');

    for (var i = 0; i < survey_question_option_array.length; i++) {
        var survey_question_option_value = survey_question_option_array[i];
        survey_question_option.push(survey_question_option_value.value);
    }

    console.log(survey_question_option);
    str = '&options_display_type='+options_display_type+'&survey_question='+survey_question+'&survey_question_option='+survey_question_option+'&qustion_idlist='+qustion_idlist+'&require_response='+require_response+'&allow_multiple_selections='+allow_multiple_selections;
    $.ajax({
        url: "ajax.php",
        data:'page=addsurveyquestions'+str,
        type: "POST",
        success: function(response) {
            console.log(response);
            document.getElementById('qustion_idlist').value = jsonresponse;
        }
});

}

function questionsubmit_freetext() {
    var str ="";
    var survey_question_option=[];
    var options_display_type = document.getElementById('options_display_type4').value;
    var survey_question = document.getElementById('survey_question4').value;
    var qustion_idlist = document.getElementById('qustion_idlist').value;
    var require_response = 0;
    $('input[name="require_response4"]:checked').each(function() {
        require_response = 1;
    });

    var allow_multiple_selections = 0;

    console.log(survey_question_option);
    str = '&options_display_type='+options_display_type+'&survey_question='+survey_question+'&survey_question_option='+survey_question_option+'&qustion_idlist='+qustion_idlist+'&require_response='+require_response+'&allow_multiple_selections='+allow_multiple_selections;
    $.ajax({
    url: "ajax.php",
    data:'page=addsurveyquestions'+str,
    type: "POST",
    success: function(response) {
        console.log(response);
        document.getElementById('qustion_idlist').value = jsonresponse;
    }
});

}

</script>

<div class="col-lg-12">


<?php
$rowid_question = 1;

foreach ($starray as $key => $survey_data) { ?>
<input type="hidden" id="count_questions" name="count_questions[]" value="<?php echo $rowid_question;?>">
<input type="hidden" id="question_id" name="question_id<?php echo $rowid_question; ?>" value="<?php echo $survey_data['question_id'];?>">

    <?php
if($survey_data['options_display_type']=="1") {
    ?>
    <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel_mc"> Multiple Choice Question</h5>
                <input type="hidden" name="options_display_type<?php echo $rowid_question; ?>" value="1" >

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="custom-icon">

                <div class="form-group text-left">
                    <span>Question Text: <span class="text-danger">*</span></span>
                    <textarea aria-label="Survey Question1" id="survey_question1" name="survey_question<?php echo $rowid_question; ?>" class="form-control" placeholder=""><?php echo $survey_data['survey_question'];?> </textarea>
                </div>
                <div class="form-group text-left">
                    <div class="checkbox-inline">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" name="allow_multiple_selections<?php echo $rowid_question; ?>" <?php if($survey_data['allow_multiple_selections'] == 1) {?> checked="checked" value="1" <?php
} else { ?> value="0" <?php
}?>  />
                            <span></span>
                            Allow multiple selections
                        </label>
                        <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" value="1" <?php if($survey_data['require_response'] == 1) {?> checked="checked" <?php
} ?>  name="require_response<?php echo $rowid_question; ?>">
                            <span></span>
                            Require a Response
                        </label>
                    </div>
                </div>
                <?php

                 foreach ($survey_data['survey_question_option'] as $options) {

                    ?>
                <div id="optionrow">
                    <div class="form-group row" id="rowid1">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-11">
                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $options;?>" name="survey_question_option<?php echo $rowid_question; ?>[]" placeholder="Option">
                            <i class="flaticon-delete btn-primary" onclick="deloptionrows('<?php echo $rowid;?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        </div>
                    </div>
                </div>

                <?php }
                ?>
                <div class="form-group">
                    <button class="btn btn-success"  onclick="addoptionrows()" id="addRows_mc" type="button">+ Add More</button>
                </div>
            </div>


<?php

}

if($survey_data['options_display_type']=="2") {
    ?>
    <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel_rating"> Rating Question</h5>
                <input type="hidden" name="options_display_type<?php echo $rowid_question; ?>"value="2" >

<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <div class="modal-body custom-icon">
                <div class="form-group text-left">
                    <span>Question Text: <span class="text-danger">*</span></span>
                    <textarea aria-label="Survey Question2" id="survey_question2" name="survey_question<?php echo $rowid_question; ?>" class="form-control" placeholder=""> <?php echo $survey_data['survey_question'];?> </textarea>
                </div>
                <div class="checkbox-inline">
                    <label class="checkbox checkbox-success">
                        <input aria-label="Checkbox" type="checkbox" value="1" name="require_response<?php echo $rowid_question; ?>" <?php if($survey_data['require_response'] == 1) {?> checked="checked" <?php
} ?>>
                        <span></span>
                        Require a Response
                    </label>
                </div>
                <?php foreach ($survey_data['survey_question_option'] as $key=>$options) {
                    $rowid = "rowid".$survey_data['key'].$key;
                    ?>
                <div class="form-group text-left mt-10">
                    <span>Scale from 1 to: <span class="text-danger">*</span></span>
                    <input aria-label="Enter number" type="number" value="<?php echo $options?>" name="survey_question_option<?php echo $rowid_question; ?>[]" class="form-control" placeholder="10">
                </div>
                <?php
            }?>
            </div>


    <?php
}

if($survey_data['options_display_type']=="3") {
    ?>


            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel_ranking"> Ranking Question</h5>
                <input type="hidden" name="options_display_type<?php echo $rowid_question; ?>"value="3" >

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body custom-icon">
                <div class="form-group text-left">
                    <span>Question Text: <span class="text-danger">*</span></span>
                    <textarea aria-label="Survey Question3" id="survey_question3" name="survey_question<?php echo $rowid_question; ?>" class="form-control" placeholder=""><?php echo $survey_data['survey_question'];?> </textarea>
                </div>
                <div class="form-group text-left">
                    <div class="checkbox-inline">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" name="allow_multiple_selections<?php echo $rowid_question; ?>" <?php if($survey_data['allow_multiple_selections'] == 1) {?> checked="checked" value="1" <?php
} else { ?> value="0" <?php
} ?>>
                            <span></span>
                            Allow multiple selections
                        </label>
                        <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" value="1" name="require_response<?php echo $rowid_question; ?>" <?php if($survey_data['require_response'] == 1) {?> checked="checked" <?php
} ?>>
                            <span></span>
                            Require a Response
                        </label>
                    </div>
                </div>

                <?php foreach ($survey_data['survey_question_option'] as $key=>$options) {
                    $rowid = "rowid".$survey_data['key'].$key;
                    ?>
                <div id="optionrow_rank">
                    <div class="form-group row" id="<?php echo $rowid;?>">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-11">
                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $options;?>" name="survey_question_option<?php echo $rowid_question; ?>[]" placeholder="Option">
                            <i class="flaticon-delete btn-primary" onclick="deloptionrows('<?php echo $rowid;?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        </div>
                    </div>
                </div>

                <?php }
                ?>
                <div class="form-group">
                    <button class="btn btn-success"  onclick="addoptionrows_rank()" id="addRows_rank" type="button">+ Add More</button>
                </div>
            </div>

    <?php
}

if($survey_data['options_display_type']=="4") {?>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel_freetext"> Free Text Question</h5>
                <input type="hidden" name="options_display_type<?php echo $rowid_question; ?>"value="4" >

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group text-left">
                    <span>Question Text: <span class="text-danger">*</span></span>
                    <textarea aria-label="Survey Question4" id="survey_question4" name="survey_question<?php echo $rowid_question; ?>" class="form-control" placeholder=""> <?php echo $survey_data['survey_question'];?> </textarea>
                </div>
                <div class="form-group text-left">
                    <div class="checkbox-inline">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Checkbox" type="checkbox" value="1" name="require_response<?php echo $rowid_question; ?>" <?php if($survey_data['require_response'] == 1) {?> checked="checked" <?php
} ?>>
                            <span></span>
                            Require a response
                        </label>
                    </div>
                </div>
            </div>


    <?php }

$rowid_question++;

}?>
</div>
