<?php
$project_id = $_GET['RID'];
$lemps = ""; $laemp =array("0"=>"99999");
if(in_array('TASKDA_USER',$_SESSION['role_ids'])){
    $lemps = $_SESSION['userid'];
}
if(isset($_POST['submit'])){
    if(count((array)$_POST['emps'])>0){
        $laemp = (array)$_POST['emps'];
        $lemps = implode(",",(array)$_POST['emps']);
        foreach($laemp as $lae){
            if($lae == '99999'){
                $lemps = "";
                break;
            }
        }
    }
    $stt = "";
    if($_POST['filter_day']!="0"){
        $stt.="&filter_day=".$_POST['filter_day'];
    }
    if($_POST['filter_due']!="0"){
        $stt.="&filter_due=".$_POST['filter_due'];
    }
    if(count((array)$_POST['filter_lable'])>0){
        $stt.="&filter_lable=".implode(",",$_POST['filter_lable']);
    }
    $chckfilter =$_POST;
}
$url = API."?mode=togetprojectkanbanboarddetails&project_id=".$project_id."&emps=".$lemps."".$stt;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$statusprogress = $data['status'];
$workspace = $data['project_work_spaces'];
$project = $data['projects'];
$tasks = $data['taskdetils'];
$project_teams = $data['project_teams'];
if(in_array('TASKDA_USER',$_SESSION['role_ids'])){
    $project_teams[] = array($_SESSION['userid']=>$_SESSION['display_name']);
}
foreach($tasks as $task){
    $ptask[$task['current_status']][] = $task;
    $idtask[$task['id']] = $task['task_name'];
}
$_SESSION['apply_js']['kanban'] = "1";
$_SESSION['apply_js']['multiselest'] = '1';
$change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen');
?>
<style type="text/css">
    .exdate{
        background-color: #db2929;
        color: #FFF;
        border-radius: 5px;
    }
    .phasecss{
        background-color: #6b74e1;
        color: #FFF;
        border-radius: 5px;
        font-size: 10px;
        padding: 1px 3px;
    }
</style>
<script type="text/javascript">
    function submitaddlist(project_id){
        var listname = $('#listname').val();
        var poss = $('#add_possition').val();
        console.log('listname:'+listname+'&poss='+poss+'&project_id='+project_id);
        if(listname!="" && poss!=""){
            jQuery.ajax({
              url: "ajax.php",
              data: "page=insertstatusprocesss&table_name=project_task_details&listname="+listname+"&position="+poss+"&project_id="+project_id,
              type: "POST",
              success:function(data){
                console.log(data);
              },
              error:function (){}
            });
        }else{
            var msg = <?php echo "'".$_SESSION['error_message']['2102']."'" ?>;
            swal.fire({
                title: msg,
                type: "danger",
                buttonsStyling: true,
                confirmButtonText: "OK, Close! ",
                confirmButtonClass: "btn btn-danger",
                showCancelButton: false,
                cancelButtonText: " No, continue",
                cancelButtonClass: "btn btn-warning"
            });
        }
    }
    function statusprogressname(name, id, val){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatemoretabledata&table_name=status_progress&name='+val+'&action_label='+val+'&update_id='+id,
          type: "POST",
          success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Title Upadated Successfully','');
          },
          error:function (){}
        });
    }
    function shwgraph(valid){
        var val = $('#shoegraphval_'+valid).val();
        if(val==0){
            $("#shoegraph_"+valid).show();
            $('#shoegraphval_'+valid).val('1');
        }
        if(val==1){
            $("#shoegraph_"+valid).hide();
            $('#shoegraphval_'+valid).val('0');
        }

    }
    function taskdetailspopup(id){
        console.log("check task"+id);
        jQuery.ajax({
            url: "views/custom_insert_project_task_detail_popup.php",
            data:'option=noption&task_id='+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#modal_status").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#kt_modal_dynamic_popup').modal("show");
                checkllll();
            },
            error:function (){}
        });
    }
    function checkllll(){
        console.log('YYYYYYYYYYYYYYY');
    }
    function submitaddcard(val,project_id){
        var taskname = $('#taskname_'+val).val();
        var assigner = $('#assigne_'+val).val();
        var phase = $('#phase_'+val).val();
        if(taskname!="" && assigner!=""){
             var msg = <?php echo "'".$_SESSION['error_message']['2102']."'" ?>;
            jQuery.ajax({
              url: "ajax.php",
              data: "page=insertprojecttaskdetails&task_type=2&project_id="+project_id+"&task_name="+taskname+"&assigen_id="+assigner+"&current_status="+val+"&project_phase_id="+phase,
              type: "POST",
              success:function(data){
                console.log(data);
                $("#addcard_"+val).hide();
                $('#append_'+val).prepend(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
              },
              error:function (){}
            });
        }else{
            var msg = <?php echo "'".$_SESSION['error_message']['2101']."'" ?>;
            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',msg,'');

        }
    }
    function addcard(id){
        $("#addcard_"+id).show();
        $("#taskname_"+id).val("");
        $("#assigne_"+id).val("");
    }
    function addlist(){
        $("#addlist").show();
    }
</script>

<script type="text/javascript">
    function filterbtn(){
        $("#filter_dropdown").toggle('slow');
    }
    function filterclose(){
        $("#filter_dropdown").hide('slow');
    }

    function user_img(){
        $(".login-popup").toggle('slow');
    }
    function user_close(){
        $(".login-popup").hide('slow');
    }
</script>
<div class="page-body">
    <div class="row align-items-center mrgn-tp">
        <div class="col-lg-12 col-md-12">
            <form method="post" action="">
                <div class="page-header-title">
                    <div class="d-inline d-blk-mob">
                        <span class="f-20 f-w-600"><?php echo $workspace[$project['project_work_space_id']]; ?></span>
                        <span class="ti-star m-l-20"></span>
                        <div class="dropdown-primary dropdown open">
                            <button class="btn btn-primary btn-round waves-effect waves-light m-l-20 daily-btn dropdown-toggle waves-effect waves-light custom-after" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $project['project_name']; ?></button>
                            <div class="dropdown-menu p-3" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="min-width: 20rem;">
                                <?php foreach($data['work_project'] as $key_work=>$work_project){ ?>
                                <h5 class="f-14 f-w-600 mb-2"><?php echo $workspace[$key_work]; ?></h5>
                                <?php foreach($work_project as $wproject){ ?>
                                    <a class="dropdown-item waves-light waves-effect p-l-2 mb-1 <?php if($wproject['id']==$project['id']){ echo "bg-primary text-white";
} ?>" href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo $wproject['id']; ?>"><?php echo $wproject['project_name']; ?></a>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="dropdown_dorp border d-inline-block position-relative">
                            <div class="dropbtn text-start w-100">Board Members</div>
                            <div class="dropdown-content">
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" class="border-checkbox"<?php if(in_array('99999',$laemp)){ echo "checked";
} ?> name="emps[]" type="checkbox" value="99999" id="all">
                                        <label class="form-label border-checkbox-label mb-0" for="all">All</label>
                                    </div>
                                </div>
                                <?php foreach($project_teams as $team){ ?>
                                    <div class="border-checkbox-section p-2">
                                        <div class="border-checkbox-group border-checkbox-group-warning">
                                            <input aria-label="Input field" class="border-checkbox" <?php if(in_array($team['id'],$laemp)){ echo "checked";
} ?> name="emps[]" type="checkbox" value="<?php echo $team['id']; ?>" id="<?php echo $team['display_name']; ?>">
                                            <label class="form-label border-checkbox-label mb-0" for="<?php echo $team['display_name']; ?>"><?php echo $team['display_name']; ?></label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="m-l-10 d-inline-block position-relative mob-p-re">
                            <div onclick="filterbtn()" id="filterbtn" class="filterbtn text-start w-100" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><span class="ti-filter m-r-10"></span> Filter</div>
                            <div id="filter_dropdown" class="filter_dropdown p-3 custom-scroll" style="max-height: 500px;">
                                <div class="">
                                    <div class="card-header p-1 mb-4">
                                        <h5>Filter</h5>
                                        <div class="card-header-right filter-close" onclick="filterclose()" style="top:0;cursor:pointer;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            <span class="ti-close"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <span class="f-12 f-w-600">Dates</span>
                                    <select aria-label="Filter Day" class="form-control border" style="-webkit-appearance: auto;" name="filter_day">
                                        <option <?php if($chckfilter['filter_day'] == '0' ){ echo "selected";
} ?> value="0">ALL</option>
                                        <option <?php if($chckfilter['filter_day'] == 'today' ){ echo "selected";
} ?> value="today">Today</option>
                                        <option <?php if($chckfilter['filter_day'] == 'week' ){ echo "selected";
} ?> value="week">Week</option>
                                        <option <?php if($chckfilter['filter_day'] == 'month' ){ echo "selected";
} ?> value="month">Month</option>
                                        <option <?php if($chckfilter['filter_day'] == 'quater' ){ echo "selected";
} ?> value="quater">Quater</option>
                                        <option <?php if($chckfilter['filter_day'] == 'half' ){ echo "selected";
} ?> value="half">Half Yearly</option>
                                        <option <?php if($chckfilter['filter_day'] == 'yearly' ){ echo "selected";
} ?> value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <span class="f-12 f-w-600">Due Dates</span>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field"  <?php if($chckfilter['filter_due'] == '0' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="0" type="radio" id="All">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="All">ALL</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'overdue' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="overdue" type="radio" id="overdue">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="overdue">Overdue</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'nextday' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="nextday" type="radio" id="nextday">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="nextday">Due in the next day</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'nextweek' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="nextweek" type="radio" id="nextweek">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="nextweek">Due in the next week</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'nextmonth' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="nextmonth" type="radio" id="nextmonth">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="nextmonth">Due in the next month</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'complete' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="complete" type="radio" id="complete">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="complete">Marked as complete</label>
                                    </div>
                                </div>
                                <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field" <?php if($chckfilter['filter_due'] == 'noncomplete' ){ echo "checked";
} ?> class="border-checkbox" name="filter_due" value="noncomplete" type="radio" id="n-complete">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="n-complete">Not marked as complete</label>
                                    </div>
                                </div>
                                <span class="f-12 f-w-600 mt-3">Projrect Phases</span>
                                <?php
                                    if(count((array)$data['phases'])>0){
                                    foreach((array)$data['phases'] as $phases){ ?>
                                        <div class="border-checkbox-section p-2">
                                    <div class="border-checkbox-group border-checkbox-group-warning">
                                        <input aria-label="Input field"  <?php if(in_array($phases['id'],(array)$chckfilter['filter_phases'])){ echo "checked";
} ?>   class="border-checkbox"  name="filter_phases[]" value="<?php  echo $phases['id']; ?>" type="checkbox" id="n-complete<?php  echo $phases['id']; ?>">
                                        <label class="form-label border-checkbox-label mb-0 f-14" for="n-complete<?php  echo $phases['id']; ?>"> <?php  echo $phases['project_phase_name']; ?></label>
                                    </div>
                                </div>


                                <?php } ?>
                                <?php } ?>

                                <span class="f-12 f-w-600 mt-3">Labels</span>
                                <?php
                                    if(count((array)$data['project_label'])>0){
                                    foreach((array)$data['project_label'] as $project_label){ ?>
                                    <div class="form-group mt-3 mb-0 label-design">
                                        <input aria-label="Input field"  <?php if(in_array($project_label['id'],(array)$chckfilter['filter_lable'])){ echo "checked";
} ?>  class="mt-2" type="checkbox" name="filter_lable[]" value="<?php  echo $project_label['id']; ?>" id="check1">
                                        <label for="check1" class="label-check w-75" style="background:<?php  echo $project_label['label_code']; ?>;">
                                            <?php  echo $project_label['label_name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <input type="hidden" name="submit" value="search" >
                        <button type="submit" class="m-l-10 btn btn-primary mob-mrgn"> Search </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="todo-container">
            <?php $tota_task = 0; $counttask= array();
            foreach((array)$statusprogress as $progress){ ?>
                <div class="box">
                    <div class="card status card-body pb-0 mb-0 prs-1" id="row_<?php echo $progress['status']; ?>">
                        <div class="card-header p-0">
                            <div class="f-18">
                                <input aria-label="Input field" class="task_1" type="text" placeholder="<?php echo $task['task_name']; ?>" name="task_name" value="<?php echo $progress['name']; ?>" onblur="statusprogressname('name', <?php echo $progress['id']; ?> ,this.value)" >
                            </div>
                            <?php if($progress['status'] ==199){ ?>
                                <div class="card-header-right" style="top:0px;">

                                    <i class="fa fa-check-square text-success f-w-600 f-20 " style="font-weight: bolder;"></i>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="mt-4 custom-scroll" style="max-height: 350px;" id="append_<?php echo $progress['status']; ?>">
                            <?php $cl = 1; foreach($ptask[$progress['status']] as $pts){
                                $colors = $data['task_label'][$pts['id']];
                                $counttask['total']['tota_task'] = $counttask['total']['tota_task']+1;
                                $counttask['total'][$progress['status']] = $counttask['total'][$progress['status']]+1;
                                $counttask[$pts['assigen_id']][$progress['status']] = $counttask[$pts['assigen_id']][$progress['status']]+1;
                                $counttask[$pts['assigen_id']]['total'] = $counttask[$pts['assigen_id']]['total']+1;
                            ?>
                                <div class="todo p-2" id="tsakid_<?php echo $pts['id']; ?>" draggable="true"   onclick="taskdetailspopup(<?php echo $pts['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                    <i class="ti-pencil float-end f-12"></i>
                                    <div class="col-lg-12" id="clc_<?php echo $cl; ?>" >
                                        <?php foreach((array)$colors as $color){ ?>
                                            <span class="kcolor color-1" style="background-color: <?php echo $color; ?> !important"></span>
                                        <?php } ?>
                                    </div>
                                    <div class="col-lg-12" id="cll_<?php echo $cl; ?>" style="display: none;">
                                        <?php foreach((array)$colors as $color){ ?>
                                            <span class="lcolor f-10" style="background-color: <?php echo $color; ?> !important">Label 1</span>
                                        <?php } ?>
                                    </div>
                                    <p class="mb-2 mt-2"><?php echo $pts['task_name']; ?></p>
                                    <?php if($data['phases'][$pts['project_phase_id']]['project_phase_name']!=''){ ?>
                                    <p><span class="mb-2 mt-2 phasecss"><?php echo $data['phases'][$pts['project_phase_id']]['project_phase_name']; ?></span></p>
                                    <?php } ?>

                                    <div class="align-items-center  mb-3">
                                        <?php if($pts['allow_watch']==0){ ?>
                                            <i class="ti-eye m-r-5 text-muted" data-bs-toggle="tooltip" data-placement="bottom" title="You are watching this card"></i>
                                        <?php } ?>
                                        <?php if(count( (array)$data['projec_task_dependencies'][$pts['id']])>0){
                                                foreach((array)$data['projec_task_dependencies'][$pts['id']] as $ttdp){
                                                     $viewdpids[] =  $idtask[$ttdp];
                                                }


                                            ?>
                                            <i class="ti-link m-r-5 text-muted" data-bs-toggle="tooltip" data-placement="bottom" title=" Dependencies : <?php echo implode(",", (array)$viewdpids); ?>"></i>
                                        <?php } ?>

                                        <?php if($pts['planned_finish']!=""){ ?>
                                            <span class=" p-1  d-inline-block f-10 m-r-5 <?php if(strtotime($pts['planned_finish'])< strtotime(date('Y-m-d'))){ echo "exdate";
} ?> ">
                                                <i class="ti-time f-10"></i> &nbsp; <?php echo date('M d', strtotime($pts['planned_finish'])); ?>
                                            </span>
                                        <?php } ?>
                                        <?php if($pts['description']!=""){ ?>
                                            <i class="ti-align-left m-r-5 f-12" data-bs-toggle="tooltip" data-placement="bottom" title="This card has a description."></i>
                                        <?php } ?>
                                        <?php if(count((array)$data['checklist'][$pts['id']])>0){ ?>
                                            <i class="ti-check-box f-12" data-bs-toggle="tooltip" data-placement="bottom" title="Check items"> <?php echo $data['checklist'][$pts['id']]['comp']."/".$data['checklist'][$pts['id']]['entotal']; ?></i>
                                        <?php } ?>
                                        <?php if($pts['assigen_id']!=""){ ?>
                                           <?php if($data['emps'][$pts['assigen_id']]['image']!=""){ ?>
                                                <img class="float-end user-name p-0 m-0" style="/*width:20px*/" src="/uploads/all/<?php echo $data['emps'][$pts['assigen_id']]['image']; ?>" alt="User Profile">
                                            <?php }else{ ?>
                                                <span class="float-end user-name"><?php echo initials($data['emps'][$pts['assigen_id']]['display_name']); ?></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php $cl++;
} ?>
                        </div>
                    </div>
                     <div class="card" style="box-shadow: none;">
                        <div class="card-body pt-0 pb-0 mt-2">
                            <div class="todo pt-3 pb-3 mb-3  m-l-0 col-lg-12 row" id="addcard_<?php echo $progress['status']; ?>" style="display: none;">
                                <div class="col-lg-12">
                                    <textarea aria-label="Text area" class="form-control" id="taskname_<?php echo $progress['status']; ?>" placeholder="Task Name"></textarea>
                                </div>
                                <div class="col-lg-12 w-full mt-2">
                                    <select aria-label="Select option" class="form-control js-example-basic-single" id="assigne_<?php echo $progress['status']; ?>">
                                        <option value=""> Select Task Owner</option>
                                        <?php foreach($project_teams as $team){ ?>
                                            <option value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-12 w-full mt-2">
                                    <select aria-label="Select option" class="form-control js-example-basic-single" id="phase_<?php echo $progress['status']; ?>">
                                        <option value=""> Select Project Phases</option>
                                        <?php foreach($data['phases'] as $phases){ ?>
                                            <option value="<?php echo $phases['id']; ?>"> <?php echo $phases['project_phase_name']; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-12 text-right mt-2">
                                    <button class="btn btn-primary" type="button" onclick="submitaddcard(<?php echo $progress['status'].",".$project_id; ?>)"> Save </button>
                                </div>
                            </div>
                            <div class="text-primary p-2 float-right" onclick="addcard(<?php echo $progress['status']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                <i class="ti-plus "></i> &nbsp; Add a Card
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="box">
               <div class="card p-2">
                <div class="todo pt-3 pb-3 mb-3  m-l-0 col-lg-12 row" id="addlist" style="display: none;">
                    <div class="close mb-1" onclick="closelist()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-close float-end"> </i></div>
                    <div class="col-lg-12">
                        <input aria-label="Enter List Name" class="form-control" id="listname" placeholder="Enter List Name">
                    </div>
                    <div class="col-lg-12 w-full mt-2">
                        <select aria-label="Add Possition" class="form-control js-example-basic-single" id="add_possition">
                            <option value=""> Select Display Possition</option>
                            <?php for($m=1; $m<11; $m++){ ?>
                                <option value="<?php echo $m*9; ?>"> <?php echo $m; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 text-right mt-2">
                        <button class="btn btn-primary cursor-pointer" type="button" onclick="submitaddlist(<?php echo $project_id; ?>)"> Save </button>
                    </div>
                </div>
                <div class="text-primary p-2 float-right" onclick="addlist()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-plus "></i> &nbsp; Add another list
               </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Task Progress</h5>
                </div>
                <div class="card-body">
                    <?php if(count((array)$statusprogress)>0){ foreach((array)$statusprogress as $progress){
                        $sval =0;
                        if($counttask['total'][$progress['status']] !="" &&$counttask['total'][$progress['status']]>0){
                            $sval = ($counttask['total'][$progress['status']]/$counttask['total']['tota_task'])*100;
                        }

                    ?>
                        <h6 class="f-14 f-w-600"><?php echo $progress['name']; ?></h6>
                        <div class="progress mb-3 progress-xs">
                            <div class="progress-bar progress-bar-primary" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo "Number of Task : ".$counttask['total'][$progress['status']]; ?>"  style="background-color: <?php echo $_SESSION['status'][$progress['status']]['color']; ?>; width: <?php echo $sval; ?>%;" role="progressbar"  aria-valuenow="<?php echo $sval; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="card">
            <div class="card-header">
                <h5>Team</h5>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    <div>
                        <?php foreach((array)$counttask as $emp_key => $employee){
                            if($emp_key != 'total'){ ?>
                                <div class="mt-3">
                                    <div class=" row col-lg-12" style="cursor: pointer;" onclick="shwgraph('<?php echo $emp_key; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <input type="hidden" name="" id="shoegraphval_<?php echo $emp_key; ?>" value="0">
                                        <div class="col-lg-3">
                                            <span class="user-name ">
                                            <?php echo initials($project_teams[$emp_key]['display_name']); ?>
                                            </span>
                                        </div>
                                        <div class="col-lg-9"> <?php echo $project_teams[$emp_key]['display_name']; ?> </div>
                                    </div>
                                    <div class="mt-3" id="shoegraph_<?php echo $emp_key; ?>" style="display:none;">
                                        <?php foreach($statusprogress as $progress){
                                            $counttask[$emp_key][$progress['status']] = $counttask[$emp_key][$progress['status']]!="" ?$counttask[$emp_key][$progress['status']] :0;
                                            $sval = ($counttask[$emp_key][$progress['status']]/$counttask[$emp_key]['total'])*100;
                                            ?>
                                            <h6 class="f-14 f-w-600"><?php echo $progress['name']; ?></h6>
                                            <div class="progress mb-3 progress-xs">
                                                <div class="progress-bar progress-bar-primary" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo "Number of Task : ".$counttask[$emp_key][$progress['status']]; ?>" style="background-color: <?php echo $_SESSION['status'][$progress['status']]['color']; ?>; width: <?php echo $sval; ?>%;" role="progressbar"  aria-valuenow="<?php echo $sval; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="overlay"></div>
</div>
