<?php
$answertype = array('T'=>'Text','O'=>'Option','MC'=>'Multiple Choice');
$inputanswer = array('EMP'=>'Employee','RO'=>'RO','HOD'=>'HOD');
?>
<script type="text/javascript">
    function getAnswerType(row_sq){
         var answer_type = $("#answer_type_"+row_sq).val();
         $("#enter_option_"+row_sq).hide();
        $("#enter_ratting_"+row_sq).hide();
         if(answer_type =="O" || answer_type =="MC"){
            $("#enter_option_"+row_sq).show();
        }else if(answer_type =="R"){
            $("#enter_ratting_"+row_sq).show();
        }
    }
    var maxoption = 10000;
    function addMoreQuestionOption(row_sq){
        maxoption++;
        var html = '<div class="input-group input-group-button" id="deletoption_'+maxoption+'"><input aria-label="Question Options[" type="text" name="question_options['+row_sq+'][]" class="form-control width1" value="" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><span class="input-group-text addon-style bg-danger"><span class=""><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-white" onclick="deletRowOption('+maxoption+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </span></span></div>';
        $("#options_"+row_sq).append(html);
    }
    function deletRowOption(option_seq){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Do you want to Delete the Option?',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#deletoption_"+option_seq).remove();
            }
        });

    }
    function deletQuestion(row_sq) {
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Do you want to Delete the Question?',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#deletquestion_"+row_sq).remove();
            }
        });
    }
    var maxquestion = 50000;
    function addMoreQuestion(){
        maxquestion++;
        var html = '<div class="card" id="deletquestion_'+maxquestion+'"><div class="card-header"><h5>Questions</h5><div class="card-header-right"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" onclick="deletQuestion('+maxquestion+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';

        html += '<div class="card-body"><div class="row"><div class="col-lg-8"><div class="row">';
        html += '<div class="form-group col-lg-5"><span class="form-label">Question<span class="text-danger">*</span></span><textarea aria-label="Question[" class="form-control" name="question['+maxquestion+']" spellcheck="false"></textarea></div>';

        html += '<div class="form-group col-lg-5"><span class="form-label">Description<span class="text-danger">*</span></span><textarea aria-label="Description[" class="form-control" name="description['+maxquestion+']" spellcheck="false"></textarea></div>';
        html += '<div class="form-group col-lg-2"><span class="form-label">Answer Type<span class="text-danger">*</span></span><select aria-label="Answer Type[" class="form-select js-example-basic-single" name="answer_type['+maxquestion+']" id="answer_type_'+maxquestion+'" onchange="getAnswerType('+maxquestion+')"><?php foreach($answertype as $key=>$keyval){ ?><option value="<?php echo $key; ?>"><?php echo $keyval; ?></option><?php
} ?></select></div>';
        html += '<div class="form-group col-lg-5"><span class="form-label d-block">Display To Enter Answer<span class="text-danger">*</span></span><?php foreach($inputanswer as $key=>$keyval){ ?><div class="checkbox-fade fade-in-primary mr-5"><label class="form-label"><input aria-label="Checkbox" type="checkbox" value="<?php echo $key; ?>" name="display_answer['+maxquestion+'][]"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $keyval; ?></span></label></div><?php
} ?></div>';

        html += '<div class="form-group col-lg-5 "><span class="form-label d-block">Display Question<span class="text-danger">*</span></span><?php foreach($inputanswer as $key=>$keyval){ ?><div class="checkbox-fade fade-in-primary mr-5"><label class="form-label"><input aria-label="Checkbox" type="checkbox" value="<?php echo $key; ?>" name="display_question['+maxquestion+'][]"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $keyval; ?></span></label></div><?php
} ?></div></div></div>';

        html += '<div class="col-lg-4" id="enter_option_'+maxquestion+'" style="display: none"><span class="form-label d-block">Option<span class="text-danger">*</span></span><div id="options_'+maxquestion+'"></div>';
        html += '<button type="button" class="btn btn-success waves-effect waves-light float-left mt-2" onclick="addMoreQuestionOption('+maxquestion+')"><i class="icofont icofont-plus icon-white m-r-10"></i>Add More</button></div>'
        html += '<div class="col-lg-4" id="enter_ratting_'+maxquestion+'" style="display: none"><span class="form-label d-block">Ratting From 1 To<span class="text-danger">*</span></span><input aria-label="Max Ratting[" type="text" name="max_ratting['+maxquestion+']" class="form-control" value="" ></div></div></div></div>';
        $("#addmorequestion").append(html);
        addMoreQuestionOption(maxquestion);
        $(".js-example-basic-single").select2();
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Performance Appraisal Questions</h5>
        <div class="card-header-right"></div>
    </div>
    <div class="p-0">
         <form method="post" enctype="multipart/form-data" >
             <div id="addmorequestion">
            <?php $paqseq = 1; $optionseq = 1;
            if(count((array)$performance_appraisal_questions) ==0){
                $performance_appraisal_questions[] = array();
            }
            foreach($performance_appraisal_questions as $paq){ ?>

            <div class="card" id="deletquestion_<?php echo $paqseq; ?>">
                <div class="card-header">
                    <h5>Questions</h5>
                    <div class="card-header-right">
                        <i class="ti-trash text-danger"  onclick="deletQuestion('<?php echo $paqseq; ?>','<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                    </div>
                </div>
                <input type="hidden" name="performance_appraisal_question_id[<?php echo $paqseq; ?>]" value="<?php echo $paq['id']; ?>">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="form-group col-lg-5">
                                    <span class="form-label">Question<span class="text-danger">*</span></span>
                                    <textarea aria-label="Text area" class="form-control header_comment_text" name="question[<?php echo $paqseq; ?>]" spellcheck="false"><?php echo $paq['question']; ?></textarea>

                                </div>
                                <div class="form-group col-lg-5">
                                    <span class="form-label">Description<span class="text-danger">*</span></span>
                                    <textarea aria-label="Text area" class="form-control header_comment_text" name="description[<?php echo $paqseq; ?>]" spellcheck="false"><?php echo $paq['description']; ?></textarea>
                                </div>
                                <div class="form-group col-lg-2">
                                    <span class="form-label">Answer Type<span class="text-danger">*</span></span>
                                    <select aria-label="Select option" class="form-select js-example-basic-single" name="answer_type[<?php echo $paqseq; ?>]" id="answer_type_<?php echo $paqseq; ?>" onchange="getAnswerType('<?php echo $paqseq; ?>')">
                                        <?php foreach($answertype as $key=>$keyval){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $paq['answer_type'] ==$key?"selected":""; ?>><?php echo $keyval; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-5">
                                    <span class="form-label d-block">Display To Enter Answer<span class="text-danger">*</span></span>
                                    <?php
                                    $display_answer = json_decode($paq['display_answer'],1);
                                    foreach($inputanswer as $key=>$keyval){ ?>
                                    <div class="checkbox-fade fade-in-primary mr-5">
                                        <label class="form-label">
                                            <input aria-label="Checkbox" type="checkbox" value="<?php echo $key; ?>" name="display_answer[<?php echo $paqseq; ?>][]" <?php echo (in_array($key, (array)$display_answer))?"checked":"";?>>
                                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $keyval; ?></span>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group col-lg-5 ">
                                    <span class="form-label d-block">Display Question<span class="text-danger">*</span></span>
                                    <?php
                                    $display_question = json_decode($paq['display_answer'],1);
                                    foreach($inputanswer as $key=>$keyval){ ?>
                                    <div class="checkbox-fade fade-in-primary mr-5">
                                        <label class="form-label">
                                            <input aria-label="Checkbox" type="checkbox" value="<?php echo $key; ?>" name="display_question[<?php echo $paqseq; ?>][]" <?php echo (in_array($key, (array)$display_question))?"checked":"";?>>
                                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $keyval; ?></span>
                                        </label>
                                    </div>
                                    <?php  } ?>
                                </div>
                                <div class="form-group col-lg-2">
                                    <span class="form-label d-block">Attachments</span>
                                    <input aria-label="Checkbox" class="js-primary-sm" type="checkbox" name="attachment[<?php echo $paqseq; ?>]" <?php echo ($paq['attachment']=='0')?"checked":"";?> value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4" id="enter_option_<?php echo $paqseq; ?>" style="<?php echo ($paq['answer_type'] =='O')?"":"display: none"; ?>">
                            <span class="form-label d-block">Option<span class="text-danger">*</span></span>
                            <div id="options_<?php echo $paqseq; ?>">
                            <?php
                            $paoption = json_decode($paq['question_options'],1);
                            if(count((array)$paoption) =='0'){
                                $paoption[] = array();
                            } foreach($paoption as $paopt){ ?>
                                <div class="input-group input-group-button" id="deletoption_<?php echo $optionseq; ?>">
                                    <input aria-label="Input field" type="text" name="question_options[<?php echo $paqseq; ?>][]" class="form-control width1" value="<?php echo $paopt; ?>">
                                    <span class="input-group-text addon-style bg-danger">
                                        <span class="cursor-pointer"><i class="ti-trash text-white"  onclick="deletRowOption('<?php echo $optionseq; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </span>
                                    </span>
                                </div>
                                <?php $optionseq++;
} ?>
                            </div>
                            <button type="button" class="btn btn-success waves-effect waves-light float-left mt-2" onclick="addMoreQuestionOption(<?php echo $paqseq; ?>)"><i class="icofont icofont-plus icon-white m-r-10"></i>Add More</button>
                        </div>
                        <div class="col-lg-4" id="enter_ratting_<?php echo $paqseq; ?>" style="<?php echo ($paq['answer_type'] =='R')?"":"display: none"; ?>">
                            <span class="form-label d-block">Ratting From 1 To<span class="text-danger">*</span></span>
                            <input aria-label="Input field" type="text" name="max_ratting[<?php echo $paqseq; ?>]" class="form-control" value="<?php echo $paq['max_ratting']; ?>" >
                        </div>
                    </div>
                </div>

        </div>
            <?php $paqseq++;
} ?>
</div>
            <div class="">
                <button type="button" class="btn btn-success waves-effect waves-light col-lg-3  m-l-20" onclick="addMoreQuestion()"><i class="icofont icofont-plus icon-white m-r-10"></i>Add More</button>
            </div>
            <div class="">
                <input type="hidden" name="appraisal_id" value="<?php echo $performance_appraisal_setup_id; ?>">
                <input type="hidden" name="parent_tab" value="otherquestion">
                <button type="submit" class="btn btn-primary waves-effect waves-light mt-5  m-l-20" name="submitperformance_appraisal_questions"><i class="fa fa-check m-r-10"></i>Submit</button>
            </div>
        </form>
    </div>
</div>
