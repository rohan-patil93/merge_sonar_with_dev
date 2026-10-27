<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetvendorfeedbackpopdetails&qid=".$_POST['question_id'];
$data_details = callAPI($url);
$data_details = json_decode($data_details,1);
$qtype = $data_details['gen'];
$ques = $data_details['question'];
$opt = json_decode($ques['question_option'],1);
$resqtype = $ques['options_display_type'] ? $ques['options_display_type'] : 4;
$cat_id = $_POST['cat_id'];
?>
<script type="text/javascript">

function addoptionrows() {
    var  i = $(".crow").length;
    i++;
    $("#optionrow").append('<div class="form-group row" id="s1rowid'+i+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"><input aria-label="Option" type="text" class="form-control display-inline crow"  name="question_option[3]['+i+']" placeholder="Option" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><div class="input-group-append"><span class="input-group-text bg-primary"><span><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-white" onclick="deloptionrows(\'s1rowid'+i+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></span></span></div></div></div></div>');
}
function deloptionrows(rowid) {
    $('#'+rowid).remove();
}
function changequestiontype(val){
    $('#quse_2').hide();
    $('#quse_3').hide();
    $('#quse_4').hide();
    $('#quse_'+val).show();
}
function questionsubmit(){
    var str = $( "form" ).serialize();
    console.log("Ravi:"+str);
    jQuery.ajax({
      url: "ajax.php",
      data:'page=addupdatequestionfeddback&'+str,
      type: "POST",
      success:function(data){
       console.log(data);
       $('#kt_modal_KTDatatable_setup').modal("hide");
       var question_id = $('#question_id').val();
       var change_id = $('#change_id').val();
       var cat_id = $('#cat_id').val();
       if(question_id!=''){
               console.log('checks1');
           $("#ques_id_"+change_id).html(data);
       }else{
           console.log('checks2');
           $("#cat_que_id_"+cat_id).append(data);
       }

      },
      error:function (){}
    });
}
</script>
<div class="modal-header">
    <h5 class="modal-title">ADD / Update Question </h5>
    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-5" >
    <form action="" method="post" class="" id="form">
        <div class="form-group row">
            <div class="col-lg-12">
                <span>Question Type </span>
                <?php foreach($qtype as $qkey=>$type){?>
                    <b>
                        <div class="<?php echo $option_cat;?>-group required float-left ml-2">
                                  <input aria-label="Radio option" type="radio" onclick="changequestiontype(this.value)" value="<?php echo $qkey;?>" name="question_type"   <?php  if($qkey == $resqtype){ echo  'Checked';
} ?> onkeydown="">
                                  <label for="q1"><?php echo $type; ?> </label> </div>
                            </b>
                <?php } ?>
            </div>
            <input type="hidden" name="cat_id" id="cat_id" value="<?php echo $_POST['cat_id']; ?>">
            <input type="hidden" name="question_id" id="question_id" value="<?php echo $_POST['question_id']; ?>">
            <input type="hidden" name="change_id" id="change_id" value="<?php echo $_POST['change_id']; ?>">
            <div class="mt-3">
                <div class="form-group text-left">
                    <span>Question Text: <span class="text-danger">*</span></span>
                    <textarea aria-label="Please Enter Question" id="question" name="question" class="form-control" placeholder="Please Enter Question"><?php echo $ques['question']; ?> </textarea>
                </div>
                <div id="quse_4" <?php  if($resqtype!=4){ ?> style="display:none;" <?php
} ?>>
                    <input type="hidden" name="question_option[4][0]"  class="form-control" value="">
                </div>
                <div id="quse_2" <?php  if($resqtype!=2){ ?> style="display:none;" <?php
} ?> >
                    <div class="form-group text-left mt-10">
                        <span>Scale from 1 to: <span class="text-danger">*</span></span>
                        <input aria-label="Question Option[2][0]" type="number" name="question_option[2][0]" value="<?php echo $opt['0'] ?>" class="form-control" placeholder="10">
                    </div>
                </div>
                <div id="quse_3" <?php  if($resqtype!=3){ ?> style="display:none;" <?php
} ?>>
<div id="optionrow">
    <?php
      if(count((array)$opt)==0){
          $opt = array('0'=>'','1'=>'');
      }
      foreach($opt as $kkey=>$op){
    ?>
    <div class="form-group row" id="s1rowid<?php echo $kkey; ?>">
        <div class="col-lg-1"></div>
        <div class="col-lg-8">
            <div class="input-group input-icon-right">
                <input aria-label="Input field" type="text" class="form-control display-inline width1 crow" name="question_option[3][<?php echo $kkey; ?>]"  value="<?php echo $op; ?>" placeholder="Option">
                <span class="input-group-text addon-style">
                    <span class=""><i class="fa fa-trash" onclick="deloptionrows('s1rowid<?php echo $kkey; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>    </span>
                </span>
            </div>
        </div>
    </div>
    <?php } ?>

</div>
        <div class="form-group">
            <button class="btn btn-success"  onclick="addoptionrows()" id="addRows" type="button">+ Add More Options</button>
        </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary mr-2" onclick="questionsubmit()" >Save</button>
</div>
    </form>
</div>
