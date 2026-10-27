<?php
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$appraisal_id  = $erid['1'];
$assessment_year = $_GET['AY'];
if($_POST['submit']=='savemanagertoassessment'){
    $url = API."?mode=insertandupdateperformancesetupquestions&method=post";
    $response = file_post_contents($url, $_POST, 'update');
}
$data = array();
$vdata1["where"] = array('assessment_id'=> $appraisal_id,'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=performance_setup_questions&data=".$vdata;
$configesponse = callAPI($vurl);
$returns = json_decode($configesponse,1);
foreach($returns as $return){
    $data[$return['assign_type']][] = $return;
}
?>
<script type="text/javascript">
    function addmtquestion(){
        var count = $(".ppmt").length;
        count++;
        var qrow ="";
        var type="Question";
        var idd = 'mtqp'+count;
        qrow +='<div class="row mt-2 ppmt" id="mtqp'+count+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class=" col-lg-11"><div class="float-left ml-2 mt-4"> '+count+'.</div><div class="float-left ml-2 col-lg-11"> <textarea aria-label="Question" class="form-control" name="questions['+count+']" placeholder="Question"></textarea> </div><div class="float-left cursor-pointer text-danger" onclick="removeddshod(\''+idd+ '\',\''+type+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div><div class="checkbox-fade fade-in-primary m-l-30 mt-2"> <label class="form-label"><input aria-label="Display Remarks[" type="checkbox"  name="display_remarks['+count+']" value="Y"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>Display Remark</span></label></div>    </div><div class="ml-15 col-lg-12" id="qmt'+count+'"></div><div class="mt-2"><button type="button" class="btn btn-info btn-shadow-hover font-weight-bold mr-2" onclick="addmtoption22('+count+')"><i class="icofont icofont-plus icon-white"></i> ADD Option</button></div></div>';
        $('#addmqt').append(qrow);
    }
    function addmtoption22(dd){

        var lowerAlph = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"]
        var count = $(".qmt"+dd).length;
        count++;
        var oprow ="";
        var type="Option";
        var idd = 'qpmt'+dd+'_'+count;
        oprow +='<div class=" mt-2 m-l-20 row col-lg-12 qmt'+dd+'" id="qpmt'+dd+'_'+count+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class="col-lg-1 mt-2">'+String.fromCharCode(count+96)+'.</div>    <div class="col-lg-10"><input aria-label="Option" type="text" class="form-control" name="emp_question_'+dd+'[]" placeholder="Option"></div><div class="col-lg-1 cursor-pointer text-danger" onclick="removeddshod(\''+idd+ '\',\''+type+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div></div>';
        $('#qmt'+dd).append(oprow);

    }
    function removeddshod(rd,type) {
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Do you want to Delete the '+type+"?",
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
                $('#'+rd).remove();
            }
        });
    }

</script>
<div class="card card-custom">
    <div class="card-header">
        <h5>Question For HOD</h5>
        <div class="card-header-right"></div>
    </div>
    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
    <div class="card-body">
        <div class="col-lg-6 " id="addmqt">
            <?php $loweralph = array("K","a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
            if(count((array)$data[2])==0){
                 $data[2][] =  array("id"=>"","assessment_question" => "","question_option"=>"","display_remark"=>"");
             }
             $m=1; foreach($data[2] as $empques){
                 $options = array();
                  if(isset($empques['question_option']) && $empques['question_option'] !=""){
                    $options= json_decode($empques['question_option'],1);
                }
                ?>
                <input type="hidden" name="rid_<?php echo $m; ?>" value="<?php echo $empques['id']; ?>">
            <div class=" mt-2 ppmt" id="mtqp<?php echo $m; ?>">
                <div class="col-lg-12 mt-2 ">
                    <div class="float-left ml-2 mt-4"> <?php echo $m; ?>.</div>
                    <div class="float-left ml-2 mb-2 col-lg-10"> <textarea aria-label="Text area" class="form-control" name="questions[<?php echo $m; ?>]" placeholder="Question"><?php echo $empques['assessment_question']; ?></textarea>
                     </div>
                     <div class="col-lg-1 mt-2 float-left text-danger" onclick="removeddshod('mtqp<?php echo $m; ?>','Question')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div>
                     <div class="checkbox-fade fade-in-primary m-l-30 mt-2"> <label class="form-label"><input aria-label="Checkbox" type="checkbox"  name="display_remarks[<?php echo $m; ?>]" value="Y" <?php echo $empques['display_remark']=='Y'?'checked':''; ?>><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>Display Remark</span></label></div>
                </div>
                <div class="ml-15 col-lg-12" id="qmt<?php echo $m; ?>">
                    <?php
                    $n=1; foreach($options as $option){ ?>
                    <div class=" mt-2 row col-lg-12 m-l-20 qm<?php echo $m; ?>" id="qpmt<?php echo $m; ?>_<?php echo $n; ?>">
                        <div class="col-lg-1 mt-2"> <?php echo $loweralph[$n]; ?>.</div>
                        <div class="col-lg-10">
                            <input aria-label="Input field" type="text" class="form-control" name="emp_question_<?php echo $m; ?>[]" placeholder="Option" value="<?php echo $option; ?>">
                        </div>
                        <div class="col-lg-1 mt-2 text-danger" onclick="removeddshod('<?php echo "qpmt".$m."_".$n; ?>','Option')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div>
                    </div>
                    <?php $n++;
} ?>
                </div>

                <div class="mt-2">
                    <button type="button" class="btn btn-info btn-shadow-hover font-weight-bold mr-2" onclick="addmtoption22(<?php echo $m; ?>)"><i class="icofont icofont-plus icon-white"></i> ADD Option</button>
                </div>
            </div>

            <?php $m++;
} ?>

        </div>
        <div class="mb-3">
            <button  type="button" class="btn btn-success btn-shadow-hover font-weight-bold mr-2 mt-2" onclick="addmtquestion()"><i class="icofont icofont-plus icon-white"></i> ADD Question</button>
        </div>
        <input type="hidden" name="submit" value="savemanagertoassessment" />
    <input type="hidden" name="assign_type" value="2" />
    <input type="hidden" name="display_questions" value="0" />
    <input type="hidden" name="rid" value="<?php echo $appraisal_id; ?>" />
    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
    </div>

  </form>
</div>
