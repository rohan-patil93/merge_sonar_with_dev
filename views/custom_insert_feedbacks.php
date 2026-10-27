<?php
$_SESSION['apply_js']['multiselest'] = 1;
 print_r($_POST);
if(isset($_POST['start_feedback'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['start_feedback'] = "Y";
    $url = API."?mode=insertfeedbacks&method=post";
    echo $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    print_r($response2);
    ?>
     <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }
            }
        </script>
<?php
}
$answertype = array('T'=>'Text','O'=>'Option','MC'=>'Multiple Choice');
if(isset($_POST['insert_feedback'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertfeedbacks&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index.php?option=COM_LIST_FEEDBACKS"); exit;
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid = $erid['1'];
$apiurl = API."?mode=getfeedbacks&rid=".$rid;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$employees = $result['employees'];
$frequency = $result['frequency'];
$feedbacks = $result['feedbacks'];
?>
<script type="text/javascript">
    function getAnswerType(){
         var answer_type = $("#answer_type").val();
         $("#enter_option").hide();
        $("#enter_ratting").hide();
         if(answer_type =="O" || answer_type =="MC"){
            $("#enter_option").show();
        }else if(answer_type =="R"){
            $("#enter_ratting").show();
        }
    }
    var maxoption = 10000;
    function addMoreQuestionOption(){
        maxoption++;
        var html = '<div class="input-group input-group-button" id="deletoption_'+maxoption+'"><input aria-label="Question Options[]" type="text" name="question_options[]" class="form-control width1" value="" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><span class="input-group-text addon-style bg-danger"><span class=""><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-white" onclick="deletRowOption('+maxoption+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </span></span></div>';
        $("#options").append(html);
    }
</script>
<div class="card" <?php if($feedbacks['shared'] =='0'){ ?>style="pointer-events: none; opacity: 0.7;"<?php
} ?>>
    <form method="post">
        <div class="card-header">
            <h5>Feed Backs</h5>
            <div class="card-header-right table-card-header">
                <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                <?php if($feedbacks['shared'] =='1'){ ?>
                <button type="submit" name="start_feedback" class="btn btn-primary mb-2 desktop-view"> Share FeedBack</button>
                <?php } ?>
                <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
    </form>
    <div class="card-body m-r-50 m-l-50">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <span class="form-label">Question<span class="text-danger">*</span></span>
                            <textarea aria-label="Question" class="form-control header_comment_text" name="question" spellcheck="false"><?php echo $feedbacks['question']; ?></textarea>

                        </div>
                        <div class="form-group col-lg-5">
                            <span class="form-label">Description<span class="text-danger">*</span></span>
                            <textarea aria-label="Description" class="form-control header_comment_text" name="description" spellcheck="false"><?php echo $feedbacks['description']; ?></textarea>
                        </div>
                        <div class="form-group col-lg-2">
                            <span class="form-label">Answer Type<span class="text-danger">*</span></span>
                            <select aria-label="Answer Type" class="form-select js-example-basic-single" name="answer_type" id="answer_type" onchange="getAnswerType()">
                                <?php foreach($answertype as $key=>$keyval){ ?>
                                <option value="<?php echo $key; ?>" <?php echo $feedbacks['answer_type'] ==$key?"selected":""; ?>><?php echo $keyval; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-lg-5 w-full">
                            <span class="form-label">Frequency</span>
                            <select aria-label="Action Frequency" class="form-control js-example-basic-single" name="action_frequency" >
                                <option value="0">Select Any One</option>
                                <?php foreach ($frequency as  $uom) { ?>
                                    <option <?php if($uom['genc_detail_code'] == $feedbacks['action_frequency']){ ?> selected <?php
} ?> value="<?php echo $uom['genc_detail_code']; ?>"><?php echo $uom['genc_detail_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-lg-5">
                            <?php $employee_ids = json_decode($feedbacks['employee_ids'],1);  ?>
                            <span class="form-label">Employees<span class="text-danger">*</span></span>
                            <select aria-label="Employee Ids[]" class="form-select js-example-basic-multiple" name="employee_ids[]"  multiple>
                                <?php foreach($employees as $empid=>$dname){ ?>
                                <option value="<?php echo $empid; ?>" <?php echo (in_array($empid, (array)$employee_ids))?"selected":"";?> <?php echo $empid==$_SESSION['emp_id']?"disabled":""; ?>><?php echo $dname; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4" id="enter_option" style="<?php echo ($feedbacks['answer_type'] =='O')?"":"display: none"; ?>">
                    <span class="form-label d-block">Option<span class="text-danger">*</span></span>
                    <div id="options">
                    <?php
                    $paoption = json_decode($feedbacks['question_options'],1);
                    if(count((array)$paoption) =='0'){
                        $paoption[] = "";
                    } foreach($paoption as $paopt){ ?>
                        <div class="input-group input-group-button" id="deletoption_<?php echo $optionseq; ?>">
                            <input aria-label="Question Options[]" type="text" name="question_options[]" class="form-control width1" value="<?php echo $paopt; ?>">
                            <span class="input-group-text addon-style bg-danger">
                                <span class="cursor-pointer"><i class="ti-trash text-white"  onclick="deletRowOption('<?php echo $optionseq; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </span>
                            </span>
                        </div>
                        <?php $optionseq++;
} ?>
                    </div>
                    <button type="button" class="btn btn-success waves-effect waves-light float-left mt-2" onclick="addMoreQuestionOption()"><i class="icofont icofont-plus icon-white m-r-10"></i>Add More</button>
                </div>
                <div class="col-lg-4" id="enter_ratting" style="<?php echo ($feedbacks['answer_type'] =='R')?"":"display: none"; ?>">
                    <span class="form-label d-block">Ratting From 1 To<span class="text-danger">*</span></span>
                    <input aria-label="Max Ratting" type="text" name="max_ratting" class="form-control" value="<?php echo $feedbacks['max_ratting']; ?>" >
                </div>
            </div>
            <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
            <button type="submit" class="btn btn-primary waves-effect waves-light mt-5" name="insert_feedback"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
        </form>
    </div>
</div>
