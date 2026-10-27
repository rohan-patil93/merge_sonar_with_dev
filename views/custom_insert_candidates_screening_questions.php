<?php 
$_SESSION['apply_js']['todo'] = 1;
$_SESSION['apply_js']['multiselest'] = '1';
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$url = API."?mode=getcandidatescreeningquestions&candidate_id=".$candidate_id; 
$api = callAPI($url);
$result = json_decode($api,1); 
//echo "<pre>"; print_r($result); echo "</pre>";

$candidates = $result['candidates'];
$job_posting_candidates = $result['job_posting_candidates'];
//print_r($job_posting_candidates);
?>
<script>
/**function aIGenerateJobDescription() {
	var candidate_id = '<?php echo $candidate_id; ?>';
    var job_posting_id = '';
    $.ajax({
        url: "apis/recruitstac_screening_questions.php",
        type: "POST",
        method: "POST",
        data: post,
        success: function(response) {
            console.log(response);
            var datas = jQuery.parseJSON(response);
            
            $("#job_responsibility").val(datas.responsibilities);


            var tags = datas.tags;
            var select = $('#kt_select2_11');
            tags.forEach(function(tag){
                var option = new Option(tag, tag, true, true);
                select.append(option);
            });
            select.trigger('change');

            $("#summa_ifr").contents().find("#tinymce").html(datas.description.replace(/\n/g, '<br>'));
            $("#textediters").html(datas.description.replace(/\n/g, '<br>'));
            $("#noteguidelines").html(datas.description.replace(/\n/g, '<br>'));
        }
    });
}**/

function getScreeningResponse(job_posting_candidate_id){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Loading"></div>';
    $('#screeningresponse').html(htmloader);
    $.ajax({
        url: "views/custom_insert_candidates_screening_questions_ajax.php",
        type: "POST",
        data: 'job_posting_candidate_id='+job_posting_candidate_id+'&gettype=QuestionsAnswers',
        success: function(response) {
            $("#screeningresponse").html(response);
        }
    });
}    

function startScreening(candidate_id,job_posting_id){
    var company_id = '<?php echo $_SESSION['company_id']; ?>';
    var data_name = '<?php echo $_SESSION['DB_NAME']; ?>';
    var user_id = '<?php echo $_SESSION['userid']; ?>';
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Loading"></div>';
    $('#screeningresponse').html(htmloader);
    $.ajax({
        url: "apis/recruitstac_screening_questions.php",
        type: "POST",
        data: 'company_id='+company_id+'&data_name='+data_name+'&user_id='+user_id+'&candidate_id='+candidate_id+'&job_posting_id='+job_posting_id,
        success: function(response) {
            var res = jQuery.parseJSON(response);
            console.log(res);
            $("#screeningresponse").html(res.question);
        }
    });
}

function approvedstartScreening(screening_id){

    $("#ai_spproved_and_start").text("Please Wait........");
    $("#ai_spproved_and_start").addClass("disabled");
    var company_id = '<?php echo $_SESSION['company_id']; ?>';
    var data_name = '<?php echo $_SESSION['DB_NAME']; ?>';
    var user_id = '<?php echo $_SESSION['userid']; ?>';
    $.ajax({
        url: "apis/recruitstac_screening_questions_approved_and_start.php",
        type: "POST",
        data: 'company_id='+company_id+'&data_name='+data_name+'&user_id='+user_id+'&screening_id='+screening_id,
        success: function(response) {
            $("#ai_spproved_and_start").text("Screening started successfully");
            var res = jQuery.parseJSON(response);
            console.log(res);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Screening started successfully.','');   
            location.reload();
        }
    });
}
function createManualQuestions(candidate_id,job_posting_id){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Loading"></div>';
    $('#screeningresponse').html(htmloader);
    $.ajax({
        url: "views/custom_insert_candidates_screening_questions_ajax.php",
        type: "POST",
        data: 'candidate_id='+candidate_id+'&job_posting_id='+job_posting_id+'&gettype=ManualQuestions',
        success: function(response) {
            $("#screeningresponse").html(response);
        }
    });
}

var max = 500;
    function addQuestionsTextFileds(val=''){
        max++;
        var html = `<div class="row rows`+max+` mt-2"> 
                <div class="col-lg-10">
                    <input class="form-control" type="text" name="screening_question[`+max+`]" value="`+val+`" placeholder="Eg. Tell me about yourself">
                </div>
                <div class="col-lg-2">
                    <i title="Delete Question" onclick="deleteQuestion(`+max+`)" class="fa fa-trash text-danger cursor-pointer"></i> 
                </div>
            </div>`;
        $("#videoquestiontextfileds").append(html);

    }

   function AIStartSceening(candidate_id,job_posting_id){
        $("#ai_spproved_and_start").text("Please Wait........");
        $("#ai_spproved_and_start").addClass("disabled");
        var company_id = '<?php echo $_SESSION['company_id']; ?>';
        var data_name = '<?php echo $_SESSION['DB_NAME']; ?>';
        var user_id = '<?php echo $_SESSION['userid']; ?>';
        $.ajax({
            url: "apis/recruitstac_screening_questions_edit_and_start.php",
            type: "POST",
            data: $('#questionform').serialize()+'&company_id='+company_id+'&data_name='+data_name+'&user_id='+user_id+'&candidate_id='+candidate_id+'&job_posting_id='+job_posting_id,
            success: function(response) {
                console.log(res);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Screening started successfully.','');   
                location.reload();
            }
        });
   } 

</script>
<div class="card-body">
    <div class="row mt-3">
      	<div class="col-lg-6">
            <!-- <button type="button" class="btn btn-primary float-right mb-1" id="ai_generate_job_description" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click here to Start Screening" onclick="approvedstartScreening('6a673fcd490735e1850a9d7f')"><i class="ti-light-bulb"></i> Scre. Start</button> -->
        	<div class="card">
          		<div class="card-header">
            		<h5>Screening Questions</h5>
            		<div class="card-header-right table-card-header">
            		 	
            		</div> 	
          		</div>
          		<div class="card-block">
                    <table class="table table-bordered table-md">
                        <tr>
                            <th>Job Postings</th>
                            <th>Screening</th>
                            <th>AI Shortlisted</th>
                            <th>Status</th>
                        </tr>
                    <?php foreach($job_posting_candidates as $jpc){ ?>
                        <tr>
                            <td class="p-2 w-b-w-s">

                                <p <?php if($jpc['screening_status'] == "Y"){ ?> class="text-primary cursor-pointer" onclick="getScreeningResponse('<?php echo $jpc['id']; ?>')" <?php } ?>><?php echo $jpc['job_number']." - ".$jpc['job_name']; ?></p>
                                <p class="mb-0"><?php echo "Start -".$jpc['screening_start_datetime'];  ?></p>
                                <p class="mb-0"><?php echo "End -".$jpc['screening_end_datetime']; ?></p>
                                <p class="mb-0"><?php echo "Summary -".$jpc['ai_summary']; ?></p>
                            </td>
                            <td class="p-2"><?php echo $jpc['screening_status'] == "Y"?"Started":"Not-Started"; ?></td>
                            <td class="p-2"><?php echo $jpc['ai_shortlisted'] !=""?$jpc['ai_shortlisted']:"pending"; ?></td>
                            <td class="p-2">
                                <?php 
                                    if($jpc['is_active'] == "0"){
                                        if($jpc['screening_status'] == "N"){ 
                                            ?>
                                            <span class="form-label label label-primary text-white cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click Here to Create Manual Questions"  onclick="createManualQuestions('<?php echo $jpc['candidate_id']; ?>','<?php echo $jpc['job_posting_id']; ?>')"> <i class="ti-plus"></i> Ques. </span>
                                        

                                            <span class="form-label label label-primary text-white cursor-pointer" id="ai_generate_job_description" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click Here to Create AI Questions"  onclick="startScreening('<?php echo $jpc['candidate_id']; ?>','<?php echo $jpc['job_posting_id']; ?>')"><i class="ti-light-bulb"></i>AI Question Gen.</span>
                                            <?php
                                        }else{
                                             echo "Active";
                                        }
                                    }else{ 
                                        echo "Not-Active"; 
                                    }
                                ?>
                                    
                                </td>
                        </tr>
        			
        	       <?php } ?>
                   </table>
          		</div>
        	</div>
      	</div>
      	<div class="col-lg-6">
        	<div class="card">
          		<div class="card-header">
            		<h5>Screening Questions/Answers</h5>
          		</div>
          		<div class="card-block" id="screeningresponse">
            		  <div class="row " style=" height: 400px;">
                            <div class="card-px text-center py-20 mt-2">
                                <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> Please Select Job Postings </p>   
                                <div class="text-center px-4">
                                  <img class="mw-100 mh-300px" alt="No Data" src="assets/images/work.png" style="max-height: 300px!important;">
                              </div>   
                          </div>   
                    </div>
          		</div>
        	</div>
      	</div>
    </div>
</div>
