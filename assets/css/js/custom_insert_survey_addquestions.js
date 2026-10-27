(function() {
    'use strict';

    var i = 2;

    function getPhpData() {
        var dataContainer = document.querySelector('.survey-addquestions-php-data');
        if (!dataContainer) return {};
        return {
            qtype: dataContainer.getAttribute('data-qtype'),
            questionId: dataContainer.getAttribute('data-question-id'),
            title: dataContainer.getAttribute('data-title')
        };
    }

    function addoptionrows() {
        i++;
        $("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"><input type="text" class="form-control display-inline"  name="survey_question_option[]" placeholder="Option"><div class="input-group-append"><span class="input-group-text bg-primary"><span><i class="fa fa-trash text-white survey-del-option-row" data-rowid="s1rowid'+i+'"></i></span></span></div></div></div></div>');
    }

    function deloptionrows(rowid) {
        $('#'+rowid).remove();
    }

    function questionsubmit() {
        var qtype = document.getElementById('qtype').value;
        var title = document.getElementById('title').value;
        var survey_question = document.getElementById('survey_question').value;
        var require_response = document.getElementById('require_response').checked == true ? '0' : '1';
        
        var allow_multiple_selections = '1';
        if(qtype==1 || qtype==3){
            allow_multiple_selections = document.getElementById('allow_multiple_selections').checked == true ? '0':'1';
        }
        
        var question_id = document.getElementById('question_id').value;
        var survey_question_option_array = [];
        var survey_question_option = [];
        survey_question_option_array = document.getElementsByName('survey_question_option[]');
        
        for (var j = 0; j < survey_question_option_array.length; j++) { 
            var survey_question_option_value = survey_question_option_array[j].value; 
            survey_question_option.push(survey_question_option_value);
        } 

        var pass_data = "&title="+title+"&type="+qtype+"&question="+survey_question+"&require_response="+require_response+"&allow_multiple_selections="+allow_multiple_selections+"&option_data="+survey_question_option+"&question_id="+question_id+"&rid=";
        
        $.ajax({
            url: "ajax.php",
            data:'page=formsurveyquestions'+pass_data,
            type: "POST",
            success: function(response) {
                $('#kt_modal_KTDatatable_setup').modal('hide');
                $("#survey_questions").html(response);
            }
        });
    }

    function bindEvents() {
        $(document).on('click', '.survey-add-option-btn', function(e) {
            e.preventDefault();
            addoptionrows();
        });

        $(document).on('click', '.survey-del-option-row', function(e) {
            e.preventDefault();
            var rowid = $(this).data('rowid');
            deloptionrows(rowid);
        });

        $(document).on('click', '.survey-del-option-static', function(e) {
            e.preventDefault();
            var rowid = $(this).data('rowid');
            deloptionrows(rowid);
        });

        $(document).on('click', '.survey-question-submit-btn', function(e) {
            e.preventDefault();
            questionsubmit();
        });
    }

    function init() {
        bindEvents();
    }

    $(document).ready(function() {
        init();
    });

})();
