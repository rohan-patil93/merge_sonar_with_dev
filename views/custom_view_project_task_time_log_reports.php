<?php
$_SESSION['apply_js']['multiselest'] = 1;
$select_month = $_POST['select_month'] ? $_POST['select_month']: date('Y-m');
$url = API . "?mode=getprojecttasktimelogreport&application_user_id=".$_POST['application_user_id']."&select_month=".$select_month;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$project_task_dates = $result['project_task_dates'];
$application_users = $result['application_users'];
$project_task_time_log = $result['project_task_time_log'];
$project_task_details = $result['project_task_details'];

$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5 class="">
                User Time Logs
            </h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                        data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=User Time Logs" target="_blank">CSV</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post"  >
                <div class="row ">
                    <div class="col-lg-4">
                        <span class="form-label d-block d-block">User Name</span>
                        <select aria-label="Application User" name="application_user_id" class="form-select js-example-basic-single" >
                        <option value="">All Users</option>
                        <?php
                            foreach ($application_users as $au){ ?>
                                <option value="<?= $au['id'] ?>" <?php echo $_POST['application_user_id']==$au['id']?"selected":""; ?>><?= htmlspecialchars($au['display_name']); ?> </option>
                        <?php } ?>
                     </select>
                    </div>
                    <div class="col-lg-4">
                        <span class="form-label d-block d-block">Month</span>
                        <input aria-label="Ect Month" type="month" class="form-control" name="select_month" value="<?php echo $select_month; ?>">
                    </div>
                    <div class="col-lg-2">
                        <span class="form-label"> &nbsp; </span>
                        <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text m-t-30">
                            Apply
                        </button>
                        <input type="hidden" name="submit" value="applyform">
                    </div>
                </div>
            </form>
        </div>

   <div class="card-body">
  <?php if(count((array)$application_users) !=0){ ?>
      <?php include_once "custom_standard_top_print.php"; ?>
  <div class="table-responsive">
    <table class="table  table-bordered" >
        <thead>
            <tr>
                <th scope="col" rowspan="3">#</th>
                <th scope="col" rowspan="3">User Name</th>
                <th scope="col" rowspan="1" colspan="<?php echo count((array)$project_task_dates)*2; ?>" ></th>
            </tr>
            <tr>
                <?php foreach($project_task_dates as $ptd){ ?>
                <th scope="col" colspan="2" class="text-center"><?php echo $ptd; ?></th>
                <?php } ?>
            </tr>
            <tr>
                <?php foreach($project_task_dates as $ptd){ ?>
                <th scope="col">Tasks</th>
                <th scope="col">Total Time</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php $seqno = 1;
                foreach($application_users as $auser){
                    if($_POST['application_user_id'] == $auser['id'] || $_POST['application_user_id']==""){
                    $projecttasktimelog = $project_task_time_log[$auser['id']];
                    $printary = array();
                    $printary['Sr.No.'] = $seqno;
                    $printary['User Name'] = $auser['display_name'];
                    foreach($project_task_dates as $ptd){
                        $printary[$ptd."(Task)"] = emptytextdashShow($projecttasktimelog[$ptd]['task_names']);
                        $printary[$ptd."(Time)"] = emptytextdashShow($projecttasktimelog[$ptd]['total_working_time']);
                    }
                    $cssdata[] = $printary;
                ?>
                <tr>
                    <td class="p-1" ><?php echo $seqno; ?></td>
                    <td class="p-1"><?php echo $auser['display_name']; ?></td>
                    <?php foreach($project_task_dates as $ptd){ ?>
                    <td class="text-right p-1 w-b-w-s">
                         <?php echo $projecttasktimelog[$ptd]['task_names'] !=""?$projecttasktimelog[$ptd]['task_names']:"-"; ?>
                    </td>
                    <td class="text-right p-1">
                        <?php echo $projecttasktimelog[$ptd]['task_names'] !=""?emptytextdashShow($projecttasktimelog[$ptd]['total_working_time']):"-"; ?>

                        </td>
                    <?php } ?>

                </tr>
             <?php $_SESSION['download_data'] = $cssdata;  $seqno++; ?>
             <?php } ?>
             <?php } ?>
        </tbody>
    </table>
</div>

<?php }else{ ?>

    <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Stock</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php } ?>
</div>
    </div>
</div>
