<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$fingrpid = $_POST['id'];
$note_id ="";
$url = API."?mode=getnotes";
$urlres = callAPI($url);
$result = json_decode($urlres,1);
$notes  = $result['notes'];
$noteslist = array();
foreach($notes as $note){
    $noteslist[$note['parent_id']][] = $note;
}
 ?>
 <script type="text/javascript">
     function parentFormSubmitNote(){
        var postarray = $("#parentsubmit").serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertnotes&'+postarray,
            success: function(response) {
                console.log(response);
                var respon = JSON.parse(response);
                console.log(respon.res);
                var result = respon.res;
                if(result.status =="fail"){
                   tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',result.message,'');
                }else{
                    if(result.LASTID == '0'){
                       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                    }else{
                      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                    }
                    $("#parentsubmit").hide('hide');
                    showPopupNote(<?php echo $fingrpid; ?>);
                }
            }
        });
     }
     function editeNotes(id){
        $('#kt_modal_KTDatatable_remote').animate({ scrollTop: 0 }, 'slow');
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=geteditnotes&id='+id,
            success: function(response) {
                var res = JSON.parse(response);
                $("#parentsubmit").hide();
                $("#parentnotename").text("");
                $("#note_id").val(res.id);
                $("#note_number").val(res.note_number);
                $("#note_name").val(res.note_name);
                $("#description").val(res.description);
                $("#parent_id").val(res.parent_id);
                $("#parentsubmit").show('slow');
            }

        });
     }
     function addChildNotes(parent_id,name){
        $('#kt_modal_KTDatatable_remote').animate({ scrollTop: 0 }, 'slow');
        $("#parentsubmit").hide();
        $("#parentnotename").text("Parent Note: "+name);
        $("#parent_id").val(parent_id);
        $("#note_id").val('');
        $("#note_number").val('');
        $("#note_name").val('');
        $("#description").val('');
        $("#parentsubmit").show('slow');
     }
     function addNewNotes(){
        $('#kt_modal_KTDatatable_remote').animate({ scrollTop: 0 }, 'slow');
        $("#parentsubmit").hide();
        $("#parentnotename").text('');
        $("#parent_id").val('');
        $("#note_id").val('');
        $("#note_number").val('');
        $("#note_name").val('');
        $("#description").val('');
        $("#parentsubmit").show('slow');
     }

    function usedNoteNumber(note_number,fingrpid){
        $("#notenumberid_"+fingrpid).text(note_number);
        $('#popup1').html(' ');
        $('#kt_modal_KTDatatable_remote').modal('hide');
    }

 </script>
 <div class="modal-content" id="divtop">
    <div class="modal-header">
        <h4 class="modal-title">Notes</h4>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="text-left mb-2">
            <button type="button" class="btn-primary" onclick="addNewNotes()" data-bs-toggle="tooltip" data-placement="bottom" title="Add Parent Note">Add Parent</button>
        </div>
        <form id="parentsubmit" style="display:none">
            <div class="row">
                <p id="parentnotename"></p>
                <div class="form-group col-lg-2">
                    <span class="form-label">Note No.<span class="text-danger">*</span></span>
                    <input aria-label="Note Number" type="text"  class="form-control text-capitalize"  name="note_number" id="note_number" value="" />
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Lable<span class="text-danger">*</span></span>
                    <input aria-label="Note Name" type="text"  class="form-control text-capitalize"  name="note_name" id="note_name" value="" />
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Description<span class="text-danger">*</span></span>
                    <textarea aria-label="Description" type="text"  class="form-control text-capitalize"  name="description" id="description" ></textarea>
                </div>
                <div class="form-group col-lg-2">
                    <input type="hidden" name="parent_id" id="parent_id" value="0">
                    <input type="hidden" name="note_id" id="note_id" value="<?php echo $note_id; ?>">
                    <button  type="button" class="btn btn-primary btn-shadow-hover font-weight-bold m-t-30" onclick="parentFormSubmitNote()">Submit </button>
                </div>
            </div>
        </form>
       <div class="card-block pt-0">
       <?php foreach($noteslist[0] as $noteslists){ ?>
        <h4 class="f-16 f-w-600 mt-3"><?php echo $noteslists['note_number'].".".$noteslists['note_name'];?>
            <span class="float-right">
                <i class="ti-pencil-alt m-r-10" data-value="<?php echo $noteslists['description']; ?>" onclick="editeNotes('<?php echo $noteslists['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Edit Note" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                <i class="fa fa-plus-square m-r-10" onclick="addChildNotes('<?php echo $noteslists['id']; ?>','<?php echo $noteslists['note_name']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Add Child" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                <i class="ti-share" onclick="usedNoteNumber('<?php echo $noteslists['note_number']; ?>','<?php echo $fingrpid; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Used Note" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
            <span>
        </h4>
        <p class="pt-2"><?php echo $noteslists['description'];?></p>
        <div class="accordion-block color-accordion-block mt-3 custom-paragraph">
            <div class="color-accordion color-accordion" >
        <?php foreach($noteslist[$noteslists['id']] as $noteslists){ ?>
                <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray"><?php echo $noteslists['note_number'].".".$noteslists['note_name'];?>
                    <span class="float-right">
                    <i class="ti-pencil-alt m-r-10" onclick="editeNotes('<?php echo $noteslists['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Edit Note" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                     <i class="ti-share" onclick="usedNoteNumber('<?php echo $noteslists['note_number']; ?>','<?php echo $fingrpid; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Used Note" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                <span>
                </a>
                <div class="accordion-desc pt-2">
                    <p class="f-14 align-justify"><?php echo $noteslists['description'];?></p>
                </div>

        <?php } ?>
    </div>
        </div>
    <?php } ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>

        <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitform()">Save</button>
    </div>
</div>

   <!-- Accordion js -->
    <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
