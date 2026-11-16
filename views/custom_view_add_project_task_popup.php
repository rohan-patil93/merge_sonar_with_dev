<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

    $data1["where"] = array('company_id'=> $_SESSION['company_id']);
    $data = urlencode(json_encode($data1));
    $vurl = API."?mode=togetprojecteamaddetails&userid=".$_POST['userid'];
    $cudata = callAPI($vurl);
    $data = json_decode($cudata,1);
    foreach($data['projects'] as $proj){
        $project[$proj['id']] = $proj['project_name'];
    }

?>
<script type="text/javascript">
    function dispalyprojectwisetasks(project_id){
            jQuery.ajax({
              url: "ajax.php",
              data:'page=customunassignprjecttaskdetails&project_id='+project_id,
              type: "POST",
              success:function(data){
                  console.log(data);
                  document.getElementById('task_id').innerHTML = data;
              },
              error:function (){}
          });
    }
    function submitform(){
          jQuery.ajax({
              url: "ajax.php",
              data:'page=setupcomponents&table_name=project_task_details&id='+$('#task_id').val()+'&assigen_id='+$('#user_id').val(),
              type: "POST",
              success:function(data){
                  console.log(data);
                  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',<?php echo json_encode(__('Upadate Project Task  Successfully'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,'');
                  window.location.reload();
              },
              error:function (){}
          });
      }
</script>
<div class="modal-header">
    <h5 class="modal-title"><?= __('Add Project Task to User'); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <form id="assetsinsertform" method="post" >
        <input type="hidden" name="user_id" id="user_id" value="<?php echo $_POST['userid']; ?>">
        <div class="row ">
            <div class="col-lg-12 row ">
                <?php if(count((array)$data['exist_projects'])==0){ ?>
                    <div class="col-lg-12 p-2 text-center">
                            <span class="text-danger f-16 f-w-600 "> <?= __('Please Add Project First'); ?></span>
                    </div>
                <?php } ?>
                <div class="form-group col-lg-12 w-full">
                    <span><?= __('Project'); ?></span>
                     <select aria-label="<?= htmlspecialchars(__('Project'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single " name="project_id" id="project_id" onchange="dispalyprojectwisetasks(this.value)">
                        <option value=""><?= __('Select Any One'); ?></option>
                        <?php foreach ((array)$data['exist_projects'] as $pro) { ?>
                            <option value="<?php echo $pro; ?>"  ><?php echo ucwords($project[$pro]); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-12 w-full">
                    <span><?= __('Project Task'); ?></span>
                    <select aria-label="<?= htmlspecialchars(__('Task'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single " name="task_id" id="task_id">
                        <option value=""><?= __('Select Any One'); ?></option>

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
