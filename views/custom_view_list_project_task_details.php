<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_ids = $_POST['task_ids'];
$url = API."?mode=togetlistprojecttaskdetailspopup&tasks=".$task_ids;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$task = $data['taskdetils'];
$project_teams = $data['project_teams'];
$labels = $data['labels'];
$atchlabel = $data['attach_labels'];
?>
<style type="text/css">
  .todo {
    background: #f6f7f8;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}
.todo .kcolor {
    width: 50px;
    height: 12px;
    border-radius: 20px;
    display: inline-block;
}
</style>
<div class="modal-header">
  <h5 class="modal-title"> Task Details</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="modal-body task-popup">
    <div class="row col-lg-12">
      <?php
      if(!empty($data['taskdetils'] )){
      foreach($data['taskdetils'] as $pts ){
        $colors = $data['task_label'][$pts['id']];
        ?>
      <div class="col-lg-4">
       <div class="todo p-2" id="tsakid_<?php echo $pts['id']; ?>"  onclick="taskdetailspopup(<?php echo $pts['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

        <div class="col-lg-12">
          <?php foreach($colors as $color){ ?>
            <span class="kcolor color-1" style="background-color: <?php echo $color; ?> !important"></span>
          <?php } ?>
        </div>
        <p class="mb-2 mt-2"><?php echo $pts['task_name']; ?></p>
        <div class="align-items-center">
          <?php if($pts['allow_watch']==0){ ?>
            <i class="ti-eye m-r-5 text-muted" data-bs-toggle="tooltip" data-placement="bottom" title="You are watching this card"></i>
          <?php } ?>
          <?php if($pts['planned_finish']!=""){ ?>
            <span class=" p-1  d-inline-block f-10 m-r-5">
              <i class="ti-time f-10"></i> &nbsp; <?php echo date('M d', strtotime($pts['planned_finish'])); ?>
            </span>
          <?php } ?>
          <?php if($pts['description']!=""){ ?>
            <i class="ti-align-left m-r-5 f-12" data-bs-toggle="tooltip" data-placement="bottom" title="This card has a description."></i>
          <?php } ?>
          <?php if(!empty($data['checklist'][$pts['id']])){ ?>
            <i class="ti-check-box f-12" data-bs-toggle="tooltip" data-placement="bottom" title="Check items"> <?php echo $data['checklist'][$pts['id']]['comp']."/".$data['checklist'][$pts['id']]['entotal']; ?></i>
          <?php } ?>

        </div>
      </div>
    </div>
    <?php } ?>
    <?php } else { ?>
       <h6 class="text-center mt-3"> No Data</h6>
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    <?php } ?>
    </div>
  </div>
</div>
