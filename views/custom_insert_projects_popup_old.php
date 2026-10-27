<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
//$nm = ViewStatusValue($_POST['status']);
$toplist = array('Project', 'Team', 'Task');
?>
<script type="text/javascript">
  function submitnext(){
    var formvalue =  $("#submitnextform").serialize();
    console.log(formvalue);
   /* jQuery.ajax({
      url: "ajax.php",
      data: formvalue+"&page=submitsprojectinsert",
      type: "POST",
      contentType: false,
      processData: false,
      success:function(data){
        //$('#kt_modal_dynamic_popup2').modal("hide");
        console.log(data);               
      }
    });*/
    //var dataArray = $("#assetsallocationform").serialize()
    jQuery.ajax({
      url: "ajax.php",
      data: "page=submitsprojectinsert&"+formvalue,
      type: "POST",
      success:function(data){
        console.log(data);
        $('#kt_modal_dynamic_popup2').modal("hide");
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Asset Allocation Successfully','');
      },
      error:function (){}
    });
  }
</script>
<div class="modal-header">
  <h5 class="modal-title"> </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form--label-right" action="" id="submitnextform" method="post">
  <div class="modal-body" > 
    <div class="row col-lg-12">
      <?php $i=1; $colorcss = "btn-primary";
      foreach($toplist as $top){ ?>
      <div class="col-lg-1">
        <button type="button" class="btn <?php if($i==1){ echo $colorcss; } ?> waves-effect waves-light"><?php echo $top; ?></button>
      </div>
      <?php $i++; } ?>
    </div>
    <div>
      <div class="card-body">
        <div id="project-details">
          <div class="form-group row ">
            <div class="row col-lg-12">
              <div class="col-lg-6">
                <label class="">Project Name</label>
                <input class="form-control"  type="text"  id="" name="project_name"  value="">
              </div>
              <div class="col-lg-6">
                <label class="">Priority</label>
                <select name="priority"  class="form-select js-example-basic-single">
                  <option>Select Any one</option>
                </select>
              </div>
              <div class="col-lg-6">
                <label class="">Plannned Start</label>
                <input class="form-control datepicker"  type="text"  id="planned_start" name="planned_start"  value="">
              </div>
              <div class="col-lg-6">
                <label class="">Plannned Finish</label>
                <input class="form-control datepicker"  type="text"  id="planned_finish" name="planned_finish"  value="">
              </div>
              <div class="col-lg-6">
                <label class="">Descrption</label>
                <textarea class="form-control" name="descrption"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div id="project-team" style="display : none;">
          <div class="row col-lg-12">
             <div class="col-lg-6">
                <label class="">Project Name</label>
                <input class="form-control"  type="text"  id="" name="project_team"  value="12to2">
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary mr-2" onclick="submitnext()" >Submit</button>
  </div>
</form>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
<!-- Multiselect js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>

