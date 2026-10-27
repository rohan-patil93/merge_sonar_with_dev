<script type="text/javascript">
    function addmfquestion(){
        var count = $(".ppmf").length;
        count++;
        var qrow ="";
        var type="Question";
        var idd = 'delque_'+count;
        qrow +='<div class="row mt-2 ppmf" id= "delque_'+count+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class=" col-lg-11"><div class="float-left ml-2 mt-4"> '+count+'.</div><div class="float-left ml-2 col-lg-11"> <textarea aria-label="Question" class="form-control" name="questions['+count+']" placeholder="Question"></textarea> </div><div class="float-left cursor-pointer text-danger" onclick="removedds(\''+idd+ '\',\''+type+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div>    </div><div class="ml-15 col-lg-6" id="qmf'+count+'"></div><div class="mt-2"><button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="addmfoption('+count+')">ADD Option</button></div></div>';
        $('#addmqf').append(qrow);
    }
    function addmfoption(dd){
        var type="Question";
        var lowerAlph = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"]
        var count = $(".qmf"+dd).length;

        count++;
        var oprow ="";
        var idd = 'qpmf'+dd+'_'+count;
        oprow +='<div class=" mt-2 qmf'+dd+'" id="qpmf'+dd+'_'+count+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class="float-left ml-2 mt-4">'+String.fromCharCode(count+96)+'.</div>    <div class="float-left ml-2 col-lg-10"><input aria-label="Option" type="text" class="form-control" name="emp_question_'+dd+'[]" placeholder="Option"></div><div class="float-left cursor-pointer text-danger" onclick="removedds(\''+idd+ '\',\''+type+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div></div>';
        $('#qmf'+dd).append(oprow);

    }
    function removedds(rd,type) {
        // Swal.fire({
        //   showDenyButton: true,
        //   showCancelButton: false,
        //   confirmButtonText: 'Yes',
        //   denyButtonText: `No`,

        swal({
    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
    text: 'Do you want to Delete the '+type+'?',
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
        <div class="col-lg-12 " id="addmqf">
            <?php $loweralph = array("K","a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
             if(count((array)$data['3'])==0 || $data['3'] =""){
                 $data['3'][] =  array('assessment_question' => "");
             }
             $m=1; foreach($data['3'] as $empques){
                     if(isset($empques['question_option'])){
                        $options= json_decode($empques['question_option'],1);
                    }

                ?>
            <div class="row mt-2 ppmf " id= "delque_<?php echo  $m; ?>">
                <div class=" col-lg-11 mt-2 ">
                    <div class="float-left ml-2 mt-4"> <?php echo $m; ?>.</div>
                    <div class="float-left ml-2 col-lg-11"> <textarea aria-label="Text area" class="form-control" name="questions[<?php echo $m; ?>]" placeholder="Question"><?php echo $empques['assessment_question']; ?></textarea>
                    </div>
                    <div class="float-left  cursor-pointer text-danger"  onclick="removedds('delque_<?php echo  $m; ?>','Question')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div>

                </div>


                <div class="ml-15 col-lg-12 row" id="qmf<?php echo $m; ?>">
                    <?php

                    $n=1; foreach($options as $option){ ?>
                    <div class=" mt-2  qmf<?php echo $m; ?>" id="qpmf1_<?php echo $m.$n; ?>">
                        <div class="float-left ml-2 mt-4"> <?php echo $loweralph[$n]; ?>.</div>
                        <div class="float-left ml-2 col-lg-10">
                            <input aria-label="Input field" type="text" class="form-control" name="emp_question_<?php echo $m; ?>[]" placeholder="Option" value="<?php echo $option; ?>">
                        </div>
                        <div class="float-left cursor-pointer text-danger" onclick="removedds('<?php echo 'qpmf1_'.$m.$n; ?>','Option')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i></div>
                    </div>
                    <?php $n++;
} ?>
                </div>

                <div class="mt-2">
                    <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="addmfoption(<?php echo $m; ?>)">ADD Option</button>
                </div>
            </div>
            <input type="hidden" name="rid_<?php echo $m; ?>" value="<?php echo $empques['id']; ?>">
            <?php $m++;
}  ?>

        </div>
        <div class="col-lg-12 mb-4">
            <button  type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 mt-2" onclick="addmfquestion()"> ADD Question</button>
        </div>
