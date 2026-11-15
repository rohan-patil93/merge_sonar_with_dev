<?php
$year_month = date("Y-m");
if (isset($_POST['year_month'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $tburl = API . "?mode=togetoprationlistbasedonmonthly&year_month=".$_POST['year_month'];
    $field_res_call = callAPI($tburl);
    $field_res = json_decode($field_res_call, 1);
    $field_tabs = $field_res;
    $year_month = $_POST['year_month'];
}

// $field_tabs = ;
//echo "<pre>"; print_r($field_tabs); echo "</pre>";
$visted = array(); $extra = array();
foreach ($field_tabs['all_user_map_detail'] as $key => $value) {
    if($value['field_work_daily_plan_id']!=""){
        $list_vist[] = $value;
        $vist_ids[$value['field_work_daily_plan_id']] = $value['current_status'];
    }else{
        $list_extra[] = $value;
    }
}
?>
<!-- Include CSS and JS files -->
<link rel="stylesheet" href="assets/css/custom_filed_employee_tab_ajax.css">
<script src="assets/js/custom_filed_employee_tab_ajax.js"></script>
<div class="card-header">
    <h5><?php echo __('Visit'); ?></h5>
    <div class="card-header-right">
        <div class="input-group input-group-button">
            <input aria-label="<?= htmlspecialchars(__('From date'), ENT_QUOTES, 'UTF-8'); ?>" type="month" name="from_date" id="att_month_year" class="form-control form-control-sm width1" value="<?php echo $year_month; ?>">
        </div>
    </div>
</div>
<div class="m-t-10">
    <div >
        <ul class="nav md-tabs infisuite_tab mt-2 m-l-15">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#planned" role="tab"><?php echo __('Planned'); ?>
                    <span class="label label-xs label-success m-l-10"><?php echo count((array)$field_tabs['user_all_daily_plan']); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#visited" role="tab"><?php echo __('Visited'); ?>
                    <span class="label label-xs label-success m-l-10"><?php echo count((array)$list_vist); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#extra" role="tab"><?php echo __('Extra'); ?>
                    <span class="label label-xs label-success m-l-10"><?php echo count((array)$list_extra); ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content tabs card-block">
    <div class="tab-pane active" id="planned" role="tabpanel">
        <div class="table-responsive custom-scroll">
            <table class="table table-hover m-b-0">
                <thead>
                    <tr>
                        <th scope="col"><?php echo __('Company'); ?> </th>
                        <th scope="col"><?php echo __('Start Date'); ?></th>
                        <th scope="col"><?php echo __('Time'); ?></th>
                        <th scope="col"><?php echo __('Status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(count((array)$field_tabs['user_all_daily_plan'])>0){
                        foreach ((array)$field_tabs['user_all_daily_plan'] as $key => $value) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars(__($value['map_title'] ?? '')); ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td>
                                <?php if(isset($vist_ids[$value['id']])  ){ ?>
                                <span class="label label-success"><?php echo __('Visited'); ?></span>
                                 <?php }else{ ?>
                                    <span class="label label-warning"><?php echo __('Pending'); ?></span>
                                 <?php } ?>
                            </td>
                        </tr>
                    <?php }
                    }else{ ?>
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><?php echo __('No Data'); ?></p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="visited" role="tabpanel">
        <div class="table-responsive custom-scroll">
            <table class="table table-hover m-b-0">
                <thead>
                    <tr>
                        <th scope="col"><?php echo __('Company'); ?> </th>
                        <th scope="col"><?php echo __('Start Date'); ?></th>
                        <th scope="col"><?php echo __('End Date'); ?></th>
                        <th scope="col"><?php echo __('Status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(count((array)$list_vist)>0){
                        foreach ((array)$list_vist as $key => $value) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars(__($value['map_title'] ?? '')); ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td><span class="label label-success"><?php echo __('Visited'); ?></span></td>
                        </tr>
                    <?php }
                    }else{ ?>
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><?php echo __('No Data'); ?></p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="extra" role="tabpanel">
        <div class="table-responsive custom-scroll">
            <table class="table table-hover m-b-0">
                <thead>
                    <tr>
                        <th scope="col"><?php echo __('Company'); ?> </th>
                        <th scope="col"><?php echo __('Start Date'); ?></th>
                        <th scope="col"><?php echo __('End Date'); ?></th>
                        <th scope="col"><?php echo __('Status'); ?></th>
                    </tr>
                </thead>
               <tbody>
                    <?php
                        if(count((array)$list_extra)>0){
                        foreach ((array)$list_extra as $key => $value) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars(__($value['map_title'] ?? '')); ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td><span class="label label-success"><?php echo __('Visited'); ?></span></td>
                        </tr>
                    <?php }
                    }else{ ?>
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><?php echo __('No Data'); ?></p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>