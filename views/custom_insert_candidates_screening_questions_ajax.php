<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$job_posting_candidate_id = $_POST['job_posting_candidate_id'];
$gettype = $_POST['gettype'];
$candidate_id = $_POST['candidate_id'];
$job_posting_id = $_POST['job_posting_id'];

$url = API."?mode=getcandidatescreeningquestions&job_posting_candidate_id=".$job_posting_candidate_id."&candidate_id=".$candidate_id."&job_posting_id=".$job_posting_id."&gettype=".$gettype; 
$api = callAPI($url);
$result = json_decode($api,1); 
 //echo "<pre>"; print_r($result); echo "</pre>";
if($gettype =="QuestionsAnswers"){

    $candidate_screening_questions_and_answers = $result['candidate_screening_questions_and_answers'];
    ?>
    <table class="table table-bordered table-md">
        <tr>
            <th>#</th>
            <th>Questions</th>
            <th>Answers</th>
        </tr>
    <?php $seq=1; foreach($candidate_screening_questions_and_answers as $qa){ ?>
        <tr>
        	<td><?php echo $seq; ?></td>
            <td class="p-2">
                <p class="text-primar  w-b-w-s"><?php echo $qa['questions']; ?></p>
            </td>
            <td class="p-2">
                <p class="text-primar  w-b-w-s"><?php echo $qa['answers']; ?></p>
            </td>
        </tr>
    <?php $seq++; } ?>
    </table>
    <?php

}elseif($gettype =="ManualQuestions"){
    $job_postings = $result['job_postings'];
    $question = $job_postings['question'];
    $questions = json_decode($question,1); 
    //echo "<pre>"; print_r($questions); echo "</pre>";
    ?>
   <form id="questionform"> 
        <div class="col-lg-12" id="videoquestiontextfileds"> 
        <?php  $seq=1; foreach($questions as $que){ ?>
            <div class="row rows<?php echo $ij; ?> mt-2"> 
                <div class="col-lg-10">
                    <input class="form-control" type="hidden" name="screening_key[]" value="<?php echo bin2hex(random_bytes(16)); ?>" placeholder="Eg. Tell me about yourself">
                    <input class="form-control" type="text" name="screening_question[]" value="<?php echo $que['question']; ?>" placeholder="Eg. Tell me about yourself">
                </div>
                <div class="col-lg-2">
                    <i title="Delete Question" onclick="deleteQuestion(<?php echo $ij; ?>)" class="fa fa-trash text-danger cursor-pointer"></i> 
                </div>
            </div>   
        <?php $seq++; } ?>
        </div>
    </form>
    <div class="row">
        <button type="button" onclick="addQuestionsTextFileds()" class="btn btn-success col-lg-4 btn-sm mt-2">
           <i class="fas fa-plus"></i> Add Question
        </button>
        <div class="col-lg-4"></div>
        <button type="button" onclick="AIStartSceening('<?php echo $candidate_id; ?>','<?php echo $job_posting_id; ?>')" class="btn btn-primary col-lg-4 btn-sm mt-2 float-right">
           <i class="fas fa-plus"></i> Start Sceening
        </button>
    </div>


<?php }else{ ?>





<?php } ?>