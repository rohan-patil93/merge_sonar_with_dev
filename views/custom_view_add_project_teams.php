<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$vurl = API."?mode=togetprojecteamaddetails&userid=".$_POST['userid'];
$userdataa = callAPI($vurl);
$data = json_decode($userdataa,1);
?>
<script type="text/javascript">
    function submitform(){
          jQuery.ajax({
              url: "ajax.php",
              data:'page=setupcomponents&table_name=project_teams&user_id='+$('#user_id').val()+'&project_id='+$('#project_id').val()+'&role_id='+$('#role_id').val(),
              type: "POST",
              success:function(data){
                  console.log(data);
                  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',<?php echo json_encode(__('User Add Successfully'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,'');
                  window.location.reload();
              },
              error:function (){}
          });
      }
</script>
<div class="modal-header">
    <h5 class="modal-title"><?= __('Add Project to User'); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <form id="assetsinsertform" method="post" >
        <input type="hidden" name="user_id" id="user_id" value="<?php echo $_POST['userid']; ?>">
        <div class="row ">
            <div class="col-lg-12 row">
                <div class="form-group col-lg-12 w-full">
                    <span><?= __('Project'); ?></span>
                    <select aria-label="<?= htmlspecialchars(__('Project'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single " name="project_id" id="project_id">
                        <option value=""><?= __('Select Any One'); ?></option>
                        <?php foreach ((array)$data['projects'] as $project) { ?>
                            <option value="<?php echo $project['id']; ?>" <?php if(in_array($project['id'],(array)$data['exist_projects'])){ echo  "disabled";
} ?> ><?php echo ucwords($project['project_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-12 w-full">
                    <span><?= __('Roles'); ?></span>
                    <select aria-label="<?= htmlspecialchars(__('Role'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single " name="role_id" id="role_id">
                        <option value=""><?= __('Select Any One'); ?></option>
                        <?php foreach ($data['roles'] as $roles) {
                            if($roles['id']!=1){
                         ?>
                            <option value="<?php echo $roles['id']; ?>"><?php echo ucwords($roles['role_name']); ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitform()"><?= __('Submit'); ?></button>
</div>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-basic-multiple").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
