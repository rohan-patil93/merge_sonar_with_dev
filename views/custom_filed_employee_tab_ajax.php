<?php
$year_month = date("Y-m");
if (isset($_POST['year_month'])){
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
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
    <h5>Visit</h5>
    <div class="card-header-right">
        <div class="input-group input-group-button"> 
            <input type="month" name="from_date" id="att_month_year" class="form-control form-control-sm width1" value="<?php echo $year_month; ?>">  
        </div>
    </div>
</div>
<div class="m-t-10">
    <div >
        <ul class="nav md-tabs infisuite_tab mt-2 m-l-15" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#planned" role="tab">Planned
                    <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$field_tabs['user_all_daily_plan']); ?></label>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#visited" role="tab">Visited
                    <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$list_vist); ?></label>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#extra" role="tab">Extra
                    <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$list_extra); ?></label>
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
                        <th>Company </th>
                        <th>Start Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(count((array)$field_tabs['user_all_daily_plan'])>0){
                        foreach ((array)$field_tabs['user_all_daily_plan'] as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['map_title']; ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td>
                                <?php if(isset($vist_ids[$value['id']])  ){ ?>
                                <label class="form-label label label-success">Visited</label>
                                 <?php }else{ ?> 
                                    <label class="form-label label label-warning">Pending</label>
                                 <?php } ?>
                            </td>
                        </tr>
                    <?php }
                    }else{ ?> 
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30">No Data</p>   
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
                        <th>Company </th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(count((array)$list_vist)>0){
                        foreach ((array)$list_vist as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['map_title']; ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td><label class="form-label label label-success">Visited</label></td>
                        </tr>
                    <?php }
                    }else{ ?> 
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30">No Data</p>   
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
                        <th>Company </th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
               <tbody>
                    <?php 
                        if(count((array)$list_extra)>0){
                        foreach ((array)$list_extra as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['map_title']; ?></td>
                            <td><?php echo  fromDate($value['map_date']); ?></td>
                            <td><?php echo  $value['map_time']; ?></td>
                            <td><label class="form-label label label-success">Visited</label></td>
                        </tr>
                    <?php }
                    }else{ ?> 
                        <tr>
                            <td colspan="4">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30">No Data</p>   
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