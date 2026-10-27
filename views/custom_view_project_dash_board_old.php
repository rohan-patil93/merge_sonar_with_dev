<?php 
$url = API."?mode=togetprojectdashboarddetails";
if(in_array('TASKDA_USER',$_SESSION['role_ids'])){
    $url = API."?mode=togetprojectdashboarddetails&role_id=employee&userid=".$_SESSION['userid']; 
}
if(in_array('TASKDA_PROJECT_MANAGER',$_SESSION['role_ids'])){
    $url = API."?mode=togetprojectdashboarddetails&role_id=manager&userid=".$_SESSION['userid'];
}
//echo $url;
$data_details = callAPI($url);
$details = json_decode($data_details,1);
//echo "<pre>"; print_r($details); echo "</pre>";
$top = $details['toptotal'];
$emps = $details['emps'];
//$sprogress = ViewStatusData();
$_SESSION['apply_js']['project_dashboard'] = 1;
$verdue =0; $yet = 0; $compp = 0; $ing =0;
foreach($details['allprojects'] as $allp){
    if(strtotime(date('Y-m-d')) >strtotime($allp['planned_finish']) && $allp['current_status']!=199 ){
        $verdue = $verdue+1;
    }
    if($allp['current_status']==0){
        $yet = $yet+1;
    }elseif($allp['current_status']==199){
        $compp = $compp+1; 
    }else{
        $ing = $ing+1;
    }
}
$cdaa[] = "Yet to start:".$yet;
$cdaa[] = "Inprogress:".$ing ;
$cdaa[] = "Completed:".$compp;
$cdaa[] = "Overdue:".$verdue;
$cdaatotal = $verdue + $ing + $compp + $verdue; 
$userprojects = $details['userprojects'];
//echo "<pre>"; print_r($details['emloyee_tasks']); echo "</pre>";
foreach($details['kanprojects'] as $kanproject){
    $allproject[$kanproject['id']] = $kanproject;
}

$procttaskstatus = $details['project_task_status'];
$project_assign = $details['project_assign'];
$projcetroleuser =  $details['projcetroleuser'];
//echo "<pre>"; print_r($details['project_task_status']); echo "</pre>";
?>
<style type="text/css">
.selector {
    display: flex;
    height: 36px;
    width: 315px;
    align-items: center;
    justify-content: center;
}
.selector .selection {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    height: 100%;
    width: 100%;
    margin-left: -1px;
    background-clip: border-box;
    transform: scale3d(1, 1, 1);
    /*background: rgba(32, 45, 59, 0.2);*/
    border: 1px solid #485769;
    color: #000000;
    transition: 0.15s ease-in-out background-color;
    position: relative;
}
.selector .selection:first-child {
    margin-left: 0;
    border-radius: 3px 0 0 3px;
}
.selector .selection:hover {
    color: #2196f3;
    background: rgba(91, 150, 213, 0.05);
}
.selector .selection:last-child {
    border-radius: 0 3px 3px 0;
}
.selector .selection.selected {
    background: rgba(91, 150, 213, 0.2) !important;
    border-color: #5b96d5;
    color: #2196f3 !important;
    z-index: 2;
    cursor: text;
}
.selection input[type="radio"]{    width: 100%; height: 36px; opacity: 0 !important; position: absolute; cursor: pointer !important;}
.user-name {
    background: #cccccc;
    border-radius: 50%;
    padding: 6px 0;
    font-size: 12px;
    font-weight: bold;
    width: 30px;
    display: inline-block;
    text-align: center;
}
.f-13{ font-size: 13px; }
</style>
<script type="text/javascript">
    function addprojecttaskstouser(userid){
        jQuery.ajax({
            url: "views/custom_view_add_project_task_popup.php",
            data:'option=noption&userid='+userid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function addprojectassign(userid){
        jQuery.ajax({
            url: "views/custom_view_add_project_teams.php",
            data:'option=noption&userid='+userid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function usertaskhistory(id){
        jQuery.ajax({
            url: "views/custom_view_user_task_history_popup.php",
            data:'option=noption&assign_id='+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
    }
    function taskdetailspopup(id){
        console.log("check task"+id);
        jQuery.ajax({
            url: "views/custom_insert_project_task_detail_popup.php",
            data:'option=noption&task_id='+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
    }
    function viegraph(id,type){
        if(type == 'G'){
            $("#graph_"+id).show();
            $("#view_"+id).hide();
        }
        if(type == 'L'){
            $("#graph_"+id).hide();
            $("#view_"+id).show();
        }
    }
    function projectchangestatus(update_id, val, field, table_name){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name='+table_name+'&field='+field+'&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Upadate Successfully','');
            window.location.reload();
        },
        error:function (){}
    });
    }
    
    function listtaskdetails(ids){
        console.log(ids);
        jQuery.ajax({
            url: "views/custom_view_list_project_task_details.php",
            data:'option=noption&task_ids='+ids,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            },
            error:function (){}
        });
    }
    function assetsaddupdatte(){
        jQuery.ajax({
            url: "views/custom_insert_allocate_assets_popup.php",
            data:'option=noption',
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            },
            error:function (){}
        });
    }

    function hassetFilter(val){
        //alert("data"+val);
        $("#rdi0").removeClass("selected");
        $("#rdi31").removeClass("selected");
        $("#rdi32").removeClass("selected");
        $("#rdi"+val).addClass("selected");
        jQuery.ajax({
            url: "views/custom_list_asset_employees_popup.php",
            data: "ownership="+val,
            type: "POST",
            success:function(data){ 
                $('#allocatereturn').html(data);
                //$('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function dynamicinsertprojectpopup(){
        console.log("Data");
        jQuery.ajax({
            url: "views/custom_insert_projects_popup.php",
            data:'option=',
            type: "POST",
            success:function(data){

                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            },
            error:function (){}
        });
    }
    function dynamicinsertworkspacepopup(){
        console.log("Data");
        jQuery.ajax({
            url: "views/custom_insert_work_space_popup.php",
            data:'option=',
            type: "POST",
            success:function(data){

                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            },
            error:function (){}
        });
    }
    function filterApllay(id){
        if(id!='-0'){
          $("#current_status").val(id); 
      }
      $("#custom_frm_submit").submit();
  }
  function dyanamicviewpage(id){
    jQuery.ajax({   
        url: "views/custom_insert_emp_assets_popup.php",    
        data:'emp_id='+id,  
        type: "POST",   
        success:function(data){ 
            $('#kt_modal_KTDatatable_remotem').modal('hide');
            $('#dynamic_popupxxl').html(data);  
            $('#kt_modal_dynamic_popupxxl').modal("show");  
        },  
        error:function (){} 
    });
}
function dashchange(val){
    //alert("wdas"+val);
    if(val==1){
        $("#checkdiv1").show();
        $("#checkdiv2").hide();
    }
    if(val==2){
        //alert("2 yes"+val);
        $("#checkdiv1").hide();
        $("#checkdiv2").show();
    }
}
</script>
<div class="pcoded-content">
    <div class="pcoded-inner-content mob-pd">
        <div class="main-body">
            <div class="page-wrapper p-0">
                <div class="row" style="">
                    <div class="col-lg-6 mb-3">
                        <?php if(!in_array('TASKDA_USER',$_SESSION['role_ids'])){ ?>                    
                        <ul class="nav md-tabs infisuite_tab" role="tablist">
                            <li class="nav-item" onclick="dashchange(1)">
                                <a class="nav-link active" data-bs-toggle="tab" href="#check1" role="tab" aria-selected="true">Dash Board</a>
                            </li>
                            <li class="nav-item"  onclick="dashchange(2)">
                                <a class="nav-link  " data-bs-toggle="tab" href="#check2" role="tab" aria-selected="true">Team</a>
                            </li>
                        </ul>                    
                        <?php } ?>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="float-right ">
                            <div class="dropdown-primary dropdown btn-group dropdown-split-primary">
                                <button class="btn btn-success dropdown-toggle waves-light f-13 v-t-c custom-dropdown-icon" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true"  aria-expanded="true">View Tasks </button>

                                <div class="dropdown-menu" aria-labelledby="dropdown-2"
                                data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <?php if(count((array)$details['kanprojects'])>0){ foreach((array)$details['kanprojects'] as $kanprojects){ ?> 
                                <a class="dropdown-item waves-light waves-effect"
                                href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo $kanprojects['id']; ?>"><?php echo $kanprojects['project_name']; ?></a>
                               <?php } }else{?>
                                 <a class="dropdown-item waves-light waves-effect"
                                href="#"> <h6 class="text-center mt-3"> No Data</h6>
                                                <img class="mw-100 mh-50px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></a>
                               <?php } ?>
                                </div>
                            </div>
                            <?php if(in_array('ADMIN',$_SESSION['role_ids'])){ ?>
                            <button class="btn btn-primary user-in"><i class="icofont icofont-plus icon-white"></i><a class="text-white" href="index.php?option=COM_LIST_APPLICATION_USER_INVITATIONS"> Invite User</a></button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div  id="checkdiv1" >
                        <div class="page-body">
                            <div class="row" style="">
                                <div class="col-lg-3 col-md-6">
                                    <div class="card prod-p-card card-blue">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col close" onclick="location.href='index.php?option=COM_VIEW_PROJECT_TOP_DASH_BOARD_DETAILS&TAB=0';">
                                                    <h6 class="m-b-5 text-white">Total Projects</h6>
                                                    <h3 class="m-b-0 f-w-700 text-white">
                                                        <?php  $total = $top['total'];
                                                        echo $total!="" ? $total: 0; ?>                        
                                                    </h3>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fa fa-trophy text-c-blue f-18"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card prod-p-card card-info">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col close" onclick="location.href='index.php?option=COM_VIEW_PROJECT_TOP_DASH_BOARD_DETAILS&TAB=3';">
                                                    <h6 class="m-b-5 text-white">Yet To Start Projects</h6>
                                                    <h3 class="m-b-0 f-w-700 text-white">
                                                        <?php echo $top['0']!="" ? $top['0']: 0; ?>
                                                    </h3>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fa fa-battery-empty text-c-green f-18"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card prod-p-card card-yellow">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col close" onclick="location.href='index.php?option=COM_VIEW_PROJECT_TOP_DASH_BOARD_DETAILS&TAB=2';">
                                                    <h6 class="m-b-5 text-white">Inprogress Projects</h6>
                                                    <h3 class="m-b-0 f-w-700 text-white">
                                                        <?php echo $top['total']-($top['4']+$top['0']); ?>
                                                    </h3>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fa fa-battery-half text-c-yellow f-18"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card prod-p-card card-green">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col close" onclick="location.href='index.php?option=COM_VIEW_PROJECT_TOP_DASH_BOARD_DETAILS&TAB=1';">
                                                    <h6 class="m-b-5 text-white">Completed Projects</h6>
                                                    <h3 class="m-b-0 f-w-700 text-white">
                                                        <?php echo $top['4']!="" ? $top['4'] :0; ?>
                                                    </h3>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fa fa-battery-full text-c-green f-18"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3">
                                    <div class="card sale-card mb-2" style="min-height:354px;">
                                        <div class="card-header">
                                            <h5>Work Spaces</h5>
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""> <?php echo count((array)$details['workspace']); ?> </label>
                                            </div>
                                        </div>
                                        <div class="custom-scroll" style="min-height: 200px;max-height: 214px;">
                                            <?php foreach((array)$details['workspace'] as $workspace){ ?>
                                                <div class="card-block" style="padding-top: 0px;">
                                                    <div class="row mt-1 pb-3 close" onclick="location.href='index.php?option=COM_VIEW_PROJECT_WORK_SPACE_DETAILS&TAB=<?php echo $workspace['id']; ?>';" style="border-bottom:1px dashed #cccccc;">
                                                        <div class="col-auto">
                                                            <div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                                <i class="fa fa-flag f-w-600"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col p-0">
                                                            <h6 class="f-w-600 mb-0"><?php echo $workspace['name']; ?></h6>
                                                            <span class="text-muted f-12"><?php echo date("d-m-Y", strtotime($workspace['created_date'])); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn waves-effect waves-light hor-grd btn-grd-primary mb-4 mt-4 f-13 " onclick="dynamicinsertworkspacepopup()">
                                                <i class="icofont icofont-plus icon-white"></i> Work Space
                                            </button>
                                            <button class="btn waves-effect waves-light hor-grd btn-grd-success mb-4 mt-4 f-13 m-l-10" onclick="dynamicinsertprojectpopup()">
                                                <i class="icofont icofont-plus icon-white"></i>  Project 
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card sale-card mb-2" style="min-height:354px;">
                                        <div class="card-header">
                                            <h5>Tasks</h5>
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""><?php echo $cdaatotal; ?> </label>
                                            </div>
                                        </div>                                    
                                        <div id="chart_Donut_Project" style="width: 100%; height: 270px;">
                                        </div>
                                        <input type="hidden" name="chart_project" id="chart_project" value="<?php echo implode("||",$cdaa); ?>" >
                                    </div>
                                </div>  
                                <div class="col-lg-2">
                                    <div class="card mb-2" style="min-height:354px;">
                                        <div class="card-header">
                                            <h5>Project Tasks</h5>
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""><?php echo $cdaatotal; ?>  </label>
                                            </div>
                                        </div>

                                        <div class="card-body custom-scrollw c-row">
                                            <?php $k = 0; $ccolor = array('#00bcd4', '#11c15b', '#ffe100', '#FE8A7D');  foreach($cdaa as $cdata){ 

                                                $tcda = explode(":",$cdata);
                                                ?>
                                                <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:<?php  echo $ccolor[$k]; ?>;">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <h6 class="m-b-5 text-white"><?php echo $tcda['0']; ?> <span class="f-w-600 float-right p-0 f-18"><?php echo $tcda['1']; ?> </span></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row mt-3 pb-3 p-l-10" style="border-bottom:1px dashed #cccccc;">
                                                    <div class="col-lg-9 p-0">
                                                        <div class="f-w-600 f-14">
                                                            <?php //echo $tcda['0']; ?>                            
                                                        </div>                        
                                                    </div>    
                                                    <div class="col-lg-3 p-0">
                                                        <span class="text-muted f-16">
                                                            <?php //echo $tcda['1']; ?> 
                                                        </span> 
                                                    </div>                     
                                                </div> -->
                                            <?php $k++; } ?>
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-lg-3">
                                    <div class="card mb-2" style="min-height:354px;">
                                        <div class="card-header">
                                            <h5>Yet To Start</h5>
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""> <?php echo count((array)$details['recentlycreated']); ?> </label>
                                            </div>
                                        </div>
                                        <div class="card-body custom-scroll" style="max-height:270px;min-height:270px;">
                                            <?php 
                                            if(count((array)$details['recentlycreated'])>0){
                                                foreach((array)$details['recentlycreated'] as $rcent){
                                                    $vieopt = "index.php?option=COM_VIEW_PROJECT&RID=".$rcent['id'];
                                                    if(in_array($_SESSION['userid'],(array)$projcetroleuser[$rcent['id']][2])){
                                                       $vieopt = "index.php?option=COM_VIEW_PROJECT_KANBAN&RID=".$rcent['id'];
                                                   }
                                                   ?>
                                                   <div   class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                                        <div class="col-auto">
                                                            <?php if($rcent['project_logo']!=""){ ?> 
                                                                <img  class="left-icon-width" src="uploads/all/<?php echo $rcent['project_logo']; ?>">
                                                            <?php }else{ ?> 
                                                                <div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                                    <i class="fa fa-trophy f-w-600"></i>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col p-0">
                                                            <div class="f-w-600 f-14 close m-0" onclick="location.href='<?php echo $vieopt; ?>';" >
                                                                <?php echo $rcent['project_name']; ?>                            
                                                            </div>
                                                            <span class="text-muted f-12">
                                                                <?php echo date("d-m-Y", strtotime($rcent['planned_start'])); ?>
                                                            </span>
                                                            <?php $btnname = "btn-success";
                                                            if(strtotime(date('Y-m-d')) > strtotime($rcent['planned_start'])){
                                                                $btnname = "btn-danger";
                                                            }
                                                            ?>
                                                            <?php if(in_array('TASKDA_PROJECT_MANAGER',(array)$_SESSION['role_ids'])){ ?> 
                                                                <button type="button" onclick="projectchangestatus(<?php echo $rcent['id'].",3,'current_status','projects'"; ?>)" class="btn btn-sm  m-l-10 <?php echo $btnname; ?>" style="padding : 0px 10px;">
                                                                    Start
                                                                </button>
                                                            <?php } ?>
                                                        </div>                          
                                                    </div>
                                                    
                                                <?php } 
                                            } else{ ?>
                                                <h6 class="text-center mt-3"> No Data</h6>
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="card mb-2" >
                                        <div class="card-header">
                                            <h5 >Project Tasks</h5>
                                        </div> 
                                        <div class="card-body">                                   
                                            <div id="allocatereturn">
                                                <div class="p-2" >
                                                    <ul class="nav md-tabs infisuite_tab" role="tablist">
                                                        <?php $i=1; foreach($details['projects'] as $cat){ ?>
                                                            <li class="nav-item">
                                                                <a class="nav-link <?php if($i==1){ echo "active"; } ?>" data-bs-toggle="tab" href="#check<?php echo $i; ?>" role="tab" aria-selected="true"><?php echo $cat['project_name']; ?></a>
                                                            </li>
                                                        <?php $i++; } ?>
                                                    </ul>
                                                </div>
                                                <div class="tab-content tabs f-13">
                                                    <?php $i=1;
                                                    if(count((array)$details['projects'])>0){
                                                    foreach((array)$details['projects'] as $cat){ 
                                                        $emp_asst = $details['task'][$cat['id']];  ?>
                                                         <div class="tab-pane <?php if($i==1){ echo "active"; } ?>" id="check<?php echo $i; ?>" role="tabpanel">
                                                            <div class="table-responsive custom-scroll" style="max-height:380px">
                                                                <table class="table table-hover table-sm">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Owner</th>
                                                                       <!--  <th>Project Name</th> -->
                                                                        <th>Task Name</th>
                                                                        <th>Due Date</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                    <?php $m=1; 
                                                                    if(count((array)$emp_asst)>0){
                                                                        foreach((array)$emp_asst as $emp_ass){ 
                                                                        if(($project_assign[$emp_ass['project_id']]==$_SESSION['userid'])|| ($emp_ass['assigen_id'] ==$_SESSION['userid']) || in_array($_SESSION['userid'],(array)$projcetroleuser[$emp_ass['project_id']][3]) || in_array('ADMIN',(array)$_SESSION['role_ids']) ){
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <?php echo $m; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if($emp_ass['assigen_id']==""){ 
                                                                                        echo "-"; 
                                                                                    }else if($emps[$emp_ass['assigen_id']]['image']!=""){?>
                                                                                        <img class="user-name p-0 m-0" style="width:30px" src="/uploads/all/<?php echo $emps[$emp_ass['assigen_id']]['image']; ?>">
                                                                                    <?php }else{ ?>
                                                                                        <span class="user-name mb-0"><?php echo initials($emps[$emp_ass['assigen_id']]['display_name']); ?></span>

                                                                                    <?php } ?>
                                                                                    <span> <?php echo $emps[$emp_ass['assigen_id']]['display_name']."-".$emp_ass['assigen_id']; ?> </span>
                                                                                </td>
                                                                                <!-- <td >
                                                                                    <a class="f-12 f-w-600" href="">
                                                                                        <?php echo $cat['project_name'];  ?>           
                                                                                    </a>
                                                                                </td> -->
                                                                                <td style="white-space: normal;">
                                                                                    <?php echo $emp_ass['task_name'];  ?>  
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo fromDate($emp_ass['planned_finish']); ?>
                                                                                </td>
                                                                                <td>
                                                                                    <label class="label label-md label-primary" style="background-color: <?php echo $_SESSION['status'][$emp_ass['current_status']]['background_color']; ?>">
                                                                                        <?php echo $procttaskstatus[$emp_ass['project_id']][$emp_ass['current_status']]['name']; ?>
                                                                                    </label>
                                                                                </td>
                                                                            </tr>
                                                                        <?php $m++; }  }
                                                                    }else{ ?> 
                                                                            <tr>
                                                                                <td colspan="6" class="text-center">
                                                                                    <h6 class="text-center mt-3"> No Data</h6>
                                                                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                                                </td>
                                                                            </tr>
                                                                    <?php } ?>
                                                                    <?php if($m==1){ ?>
                                                                        <tr>
                                                                            <td colspan="6" class="text-center">
                                                                                <h6 class="text-center mt-3"> No Data</h6>
                                                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>

                                                                </table>
                                                            </div>
                                                        </div>
                                                    <?php $i++; } 
                                                    }else{ ?>
                                                        <h6 class="text-center mt-3"> No Data</h6>
                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(!in_array('TASKDA_USER',$_SESSION['role_ids'])){ ?>
                                    <div class="card mb-2"  >
                                        <div class="card-header">
                                            <h5>Users</h5>
                                            <div  class="card-header-right ">
                                                <ul class="nav md-tabs infisuite_tab " role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link <?php $i=1; if($i==1){ echo "active"; } ?>" data-bs-toggle="tab" href="#unassign" role="tab" aria-selected="true"><?php echo "Unassigned"; ?></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link " data-bs-toggle="tab" href="#assign" role="tab" aria-selected="true"><?php echo "Assigned"; ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-content tabs f-13">
                                            <div class="tab-pane <?php  $i=1; if($i==1){ echo "active"; } ?>" id="unassign" role="tabpanel">
                                                <?php $i=1;  if(count((array)$details['unassign'])>0){ ?>
                                                    <div class="table-responsive custom-scroll" style="max-height:380px">
                                                        <table class="table table-hover table-sm">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>User </th>                                                
                                                                <th>Projects</th>
                                                                <th>Assign Projects</th>
                                                                <th>Assign Tasks</th>
                                                                <td></td>
                                                            </tr>
                                                            <?php $m=1; 
                                                            foreach($details['unassign'] as $alluser){
                                                                if($alluser['user_email']!=" "){ ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $m; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php if($alluser['user_photo']!=""){?>
                                                                                <img class="user-name p-0 m-0" style="width:30px" src="/uploads/all/<?php echo $alluser['user_photo']; ?>">
                                                                            <?php }else{ ?>
                                                                                <span class="user-name mb-0"><?php echo initials($alluser['display_name']); ?></span>
                                                                            <?php } ?>
                                                                            <span> <?php echo $alluser['display_name']; ?> </span>
                                                                        </td>
                                                                        <td style="white-space: normal;">
                                                                           <?php  foreach($userprojects[$alluser['id']] as $pprj){ ?>
                                                                            <a href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo $pprj['project_id']; ?>" class="cursor-pointer">
                                                                                <?php if($allproject[$pprj['project_id']]['project_logo']!=""){?>
                                                                                    <img data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $allproject[$pprj['project_id']]['project_name']; ?>" class="user-name p-0 m-0 " style="width:30px" src="/uploads/all/<?php echo $allproject[$pprj['project_id']]['project_logo']; ?>">
                                                                                <?php }else{ ?>
                                                                                    <span   data-placement="bottom" title="<?php echo $allproject[$pprj['project_id']]['project_name']; ?>" class="user-name cursor-pointer mb-0"><?php echo initials($allproject[$pprj['project_id']]['project_name']); ?></span>

                                                                                <?php } ?>
                                                                            </a>
                                                                        <?php } ?> 
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="addprojectassign(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            Add Project Team 
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="addprojecttaskstouser(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            Assign Task
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="usertaskhistory(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            History
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                <?php $m++; }  
                                                            }  ?> 
                                                        </table>
                                                    </div>
                                                <?php }else{ ?>
                                                    <div class="text-center mt-3">                    
                                                        <h6 > No Data</h6>
                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                    </div>
                                                <?php } ?> 
                                            </div>
                                            <div class="tab-pane " id="assign" role="tabpanel">
                                                <?php $i=1;  if(count((array)$details['assign'])>0){ ?>
                                                    <div class="table-responsive custom-scroll" style="max-height:380px">
                                                        <table class="table table-hover table-sm">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>User </th>
                                                                <th>Projects</th>
                                                                <th>Assign Projects</th>
                                                                <th>Assign Tasks</th>
                                                                <td></td>
                                                            </tr>
                                                            <?php $m=1; foreach($details['assign'] as $alluser){
                                                                if($alluser['user_email']!=" "){ ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $m; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php if($alluser['user_photo']!=""){?>
                                                                                <img class="user-name p-0 m-0" style="width:30px" src="/uploads/all/<?php echo $alluser['user_photo']; ?>">
                                                                            <?php }else{ ?>
                                                                                <span class="user-name mb-0"><?php echo initials($alluser['display_name']); ?></span>

                                                                            <?php } ?>
                                                                            <span> <?php echo $alluser['display_name']; ?> </span>
                                                                        </td>
                                                                        <td style="white-space: normal;">
                                                                         <?php  foreach((array)$userprojects[$alluser['id']] as $pprj){ ?>
                                                                            <a href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo $pprj['project_id']; ?>" class="cursor-pointer">
                                                                                <?php if($allproject[$pprj['project_id']]['project_logo']!=""){?>
                                                                                    <img data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $allproject[$pprj['project_id']]['project_name']; ?>" class="user-name p-0 m-0 " style="width:30px" src="/uploads/all/<?php echo $allproject[$pprj['project_id']]['project_logo']; ?>">
                                                                                <?php }else{ ?>
                                                                                    <span   data-placement="bottom" title="<?php echo $allproject[$pprj['project_id']]['project_name']; ?>" class="user-name cursor-pointer mb-0"><?php echo initials($allproject[$pprj['project_id']]['project_name']); ?></span>

                                                                                <?php } ?>
                                                                            </a>
                                                                        <?php } ?> 
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="addprojectassign(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            Add Project Team 
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="addprojecttaskstouser(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            Assign Task
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <label class="label label-md label-info" onclick="usertaskhistory(<?php echo $alluser['id']; ?>)" style="cursor: pointer;">
                                                                            History
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php $m++; }  }  ?>
                                                        </table>
                                                    </div>
                                                <?php }else{ ?>
                                                    <div class="text-center mt-3">                    
                                                        <h6 > No Data</h6>
                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                    </div>

                                                <?php } ?> 
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-3">
                                    <div class="card mb-2">
                                        <div class="card-header">
                                            <h5>In-Progress</h5>     
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""> <?php echo count((array)$details['recentlyinprogress']); ?>  </label>
                                            </div>                               
                                        </div>
                                        
                                        <div class="card-body custom-scroll" style="max-height:270px;">
                                            <?php if(count((array)$details['recentlyinprogress'])>0){
                                                foreach((array)$details['recentlyinprogress'] as $rcent){
                                                    $vieopt = "index.php?option=COM_VIEW_PROJECT&RID=".$rcent['id'];
                                                    if(in_array($_SESSION['userid'],(array)$projcetroleuser[$rcent['id']][2])){
                                                     $vieopt = "index.php?option=COM_VIEW_PROJECT_KANBAN&RID=".$rcent['id'];
                                                 } ?>
                                                 <div class="row mt-3 pb-3 close" onclick="location.href='<?php echo $vieopt; ?>';"  style="border-bottom:1px dashed #cccccc;">
                                                    <div class="col-auto">
                                                        <?php if($rcent['project_logo']!=""){ ?> 
                                                            <img  class="img-50" src="uploads/all/<?php echo $rcent['project_logo']; ?>">
                                                        <?php }else{ ?> 
                                                            <div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                                <i class="fa fa-trophy f-w-600"></i>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col p-0">
                                                        <div class="f-w-600 f-14"><?php echo $rcent['project_name']; ?></div>
                                                        <span class="text-muted f-12"><?php echo date("d-m-Y", strtotime($rcent['planned_finish'])); ?></span>
                                                    </div>
                                                </div>
                                            <?php } 
                                            } else{ ?>
                                            <h6 class="text-center mt-3"> No Data</h6>
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="card mb-2 ">
                                        <div class="card-header">
                                            <h5>Recently Completed</h5> 
                                            <div class="card-header-right">
                                                <label class="label label-md label-primary" style=""><?php echo count((array)$details['recentlycompleted']); ?>  </label>
                                            </div>                                   
                                        </div>
                                        <div class="card-body custom-scroll" style="max-height:270px;" >
                                            <?php if(count((array)$details['recentlycompleted'])>0){
                                                foreach((array)$details['recentlycompleted'] as $rcent){ 
                                                    $vieopt = "index.php?option=COM_VIEW_PROJECT&RID=".$rcent['id'];
                                                    if(in_array($_SESSION['userid'],(array)$projcetroleuser[$rcent['id']][2])){
                                                       $vieopt = "index.php?option=COM_VIEW_PROJECT_KANBAN&RID=".$rcent['id'];
                                                   } ?>
                                                   <div class="row mt-1 pb-3 close" onclick="location.href='<?php echo $vieopt; ?>';"  style="border-bottom:1px dashed #cccccc;">
                                                    <div class="col-lg-3">
                                                        <?php if($rcent['project_logo']!=""){ ?> 
                                                            <img  class="img-50" src="uploads/all/<?php echo $rcent['project_logo']; ?>">
                                                        <?php }else{ ?> 
                                                            <div class="p-2 bg-primary b-radius-10 text-center" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                                <i class="ti-calendar f-w-600"></i>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-lg-9 p-0">
                                                        <div class="f-w-600 f-14"><?php echo $rcent['project_name']; ?></div>
                                                        <span class="text-muted f-12"><?php echo date("d-m-Y", strtotime($rcent['planned_finish'])); ?></span>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else{ ?>
                                            <h6 class="text-center mt-3"> No Data</h6>
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div></div>
                    <div  id="checkdiv2" style="display:none;" >
                        <div class="page-body"> 
                            <div class="row">
                               <?php $conlist = array('Today','Week','Month','All');
                               $week_start = date("Y-m-d", strtotime('monday this week'));
                               $week_end = date("Y-m-d", strtotime('sunday this week'));
                               $month_start = date('Y-m-01');
                               $mont_last = date('Y-m-t');
                               foreach($details['project_employees'] as $pemp){ ?>
                                <div class="col-lg-3">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h5><?php echo $pemp['display_name']; ?></h5>
                                            <div class="card-header-right" style="width:15%;">
                                                <span class="float-left" onclick="viegraph(<?php echo $pemp['id']; ?>,'G')">
                                                    <i class="ti-bar-chart"></i>
                                                </span>
                                                <span class=""  onclick="viegraph(<?php echo $pemp['id']; ?>,'L')">
                                                    <i class="ti-view-list-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body" id="graph_<?php echo $pemp['id']; ?>">
                                            <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                                                <?php $i=1;
                                                foreach($conlist as $cat){ 
                                                    ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link f-12 <?php if($i==1){ echo "active"; } ?>" data-bs-toggle="tab" href="#checkcongr_<?php echo $pemp['id']."_".$i; ?>" role="tab" aria-selected="true"><?php echo $cat; ?></a>
                                                    </li>
                                                 <?php $i++; } ?>
                                            </ul>
                                            <div class="tab-content tabs f-13">
                                                <?php  $i=1; foreach((array)$conlist as $cat){ $totalchhck=0; ?>
                                                    <div class="tab-pane <?php if($i==1){ echo "active"; } ?>" id="checkcongr_<?php echo $pemp['id']."_".$i; ?>" role="tabpanel">
                                                        <?php $listtask = array(); $totalgrah= array(); $due =0; $over_due =0;
                                                            $listtask_array= array(); $due_array= array(); $over_due_array= array();
                                                        foreach($details['emloyee_tasks'][$pemp['id']] as $ttt){ 
                                                            $viewlisttask_array = array(); 
                                                            $chhck =0; 
                                                            if($i==4){ 
                                                                $chhck =1;
                                                            }
                                                            if($i==1){ 
                                                                if(strtotime(date('Y-m-d'))==strtotime($rcent['planned_finish'])){
                                                                    $chhck =1;
                                                                } 
                                                            }
                                                            if($i==2){ 
                                                                if((strtotime($week_start)<=strtotime($rcent['planned_finish'])) && (strtotime($week_end)>=strtotime($rcent['planned_finish'])) ){
                                                                    $chhck =1;
                                                                } 
                                                            }
                                                            if($i==3){ 
                                                                if((strtotime($month_start)<=strtotime($rcent['planned_finish'])) && (strtotime($mont_last)>=strtotime($rcent['planned_finish'])) ){
                                                                    $chhck =1;
                                                                } 
                                                            }
                                                            if($chhck ==1){
                                                                
                                                                if(strtotime(date('Y-m-d')) > strtotime($rcent['planned_finish']) ){
                                                                    $over_due = $over_due+1;
                                                                    $over_due_array[] = $ttt['id'];
                                                                }else{
                                                                     $due = $due+1;
                                                                    $due_array[] = $ttt['id'];
                                                                }
                                                                if($ttt['current_status']==0){
                                                                    $viewlisttask_array[0][] = $ttt['id'];
                                                                }elseif($ttt['current_status']==199){
                                                                    $viewlisttask_array[199][] = $ttt['id'];
                                                                }else{
                                                                    $viewlisttask_array['101'][] = $ttt['id'];
                                                                }
                                                                $listtask[$ttt['current_status']] = $listtask[$ttt['current_status']] +1;
                                                                $listtask_array[$ttt['current_status']][] = $ttt['id'];
                                                                if($ttt['current_status']==4){
                                                                    $totalgrah['completed'] =  $totalgrah['completed']+1;
                                                                }else{
                                                                    $totalgrah['remain'] =  $totalgrah['remain']+1;
                                                                }
                                                                $percntagemngemp = ($totalgrah['completed']/($totalgrah['completed']+$totalgrah['remain']))*100;
                                                            } 
                                                        } //print_r($viewlisttask_array)?>
                                                         <?php $viewstatus = array('0'=>0,'101'=>0, '199'=>0);
                                                                //echo "<pre>"; print_r($listtask); echo "</pre>";
                                                                $wisetotal = 0; $cmtotal=0;
                                                                foreach($listtask as $pkey=>$pstatus){
                                                                    if($pkey == 0){
                                                                        $viewstatus['0'] = $pstatus;

                                                                    }elseif($pkey == 199){
                                                                        $viewstatus['199'] = $pstatus;
                                                                        $cmtotal = $pstatus;
                                                                    }else{
                                                                        $viewstatus['101'] = $viewstatus['101']+ $pstatus;
                                                                    }
                                                                     $wisetotal  =  $wisetotal + $pstatus;
                                                                } 
                                                                $starrv = array('0'=>'To Do','101'=>'In-Progress', '199'=>'Completed');
                                                                $percntagemngemp = 100;
                                                                if($cmtotal ==0 && $wisetotal >0 ){
                                                                 $percntagemngemp = 0;
                                                                }
                                                                if($cmtotal!=0){
                                                                     $percntagemngemp = ($cmtotal/$wisetotal)*100;
                                                                }
                                                               //echo "cmtotal:".$cmtotal.",wisetotal".$wisetotal.",percntagemngemp".$percntagemngemp;
                                                            ?>
                                                            <div class="m-b-10 m-t-20" style="text-align: -webkit-center;">
                                                                <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation($percntagemngemp*3.6).'deg';?>; --p1_color: #2ed8b6;--p2_color: #dc3545;">
                                                                    <?php if($pemp['image']!=""){ ?>
                                                                        <img src="uploads/all/<?php echo $pemp['image']; ?>" class="percentag-img-radius img-90" alt="image" style="border-radius: 50%;border: 8px solid #ffffff;">
                                                                    <?php }else{ ?>
                                                                        <span class="text-dark f-16 f-w-600 d-block" style="background-color: #ded1d1 !important;">
                                                                            <?php echo initials($pemp['display_name']); ?>                    
                                                                        </span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <div class="row p-l-15 p-r-15 mb-2 text-muted">
                                                                <div class="col-lg-6 mt-2 f-12" style="cursor: pointer;" onclick="listtaskdetails('<?php echo implode(",",(array)$due_array); ?>')">                    
                                                                    <div class="s-cont d-inline-block" style="width:90%" >                        
                                                                        <span class="m-b-0" >Due</span>
                                                                        <span class="f-w-700 m-b-0 m-l-5 float-right">
                                                                            <?php echo $due; ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mt-2 f-12" style="cursor: pointer;" onclick="listtaskdetails('<?php echo implode(",",$over_due_array); ?>')">                    
                                                                    <div class="s-cont d-inline-block" style="width:90%">                        
                                                                        <span class="m-b-0" >Over Due</span>
                                                                        <span class="f-w-700 m-b-0 m-l-5 float-right"  style="color: #e74242;">
                                                                            <?php echo $over_due; ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                               <?php
                                                                foreach($starrv as $vkey=>$vsta){ ?>
                                                                    <div class="col-lg-6 mt-2 f-12" style="cursor: pointer;" onclick="listtaskdetails('<?php echo implode(",",(array)$viewlisttask_array[$vkey]); ?>')">                    
                                                                        <div class="s-cont d-inline-block" style="width:90%">                        
                                                                            <span class="m-b-0" ><?php echo $vsta;  ?></span>
                                                                            <span class="f-w-700 m-b-0 m-l-5 float-right">
                                                                                <?php echo $viewstatus[$vkey]; ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                <?php }  ?>
                                                            </div>
                                                        </div>
                                                <?php $i++; } ?>
                                            </div>
                                        </div>
                                         <div class="card-body custom-scroll" id="view_<?php echo $pemp['id']; ?>" style="min-height:270px;max-height:270px; display: none;">
                                            <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                                                <?php $i=1; foreach((array)$conlist as $cat){ 
                                                    ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link f-12 <?php if($i==1){ echo "active"; } ?>" data-bs-toggle="tab" href="#checkcon_<?php echo $pemp['id']."_".$i; ?>" role="tab" aria-selected="true"><?php echo $cat; ?></a>
                                                    </li>
                                                <?php $i++; } ?>
                                            </ul>
                                            <div class="tab-content tabs f-13">
                                                <?php  $i=1; foreach((array)$conlist as $cat){ $totalchhck=0;  ?>
                                                    <div class="tab-pane <?php if($i==1){ echo "active"; } ?>" id="checkcon_<?php echo $pemp['id']."_".$i; ?>" role="tabpanel">
                                                        <?php 
                                                        if(count($details['emloyee_tasks'][$pemp['id']])>0){ 
                                                            $nodata = 0;
                                                            foreach($details['emloyee_tasks'][$pemp['id']] as $rcent){
                                                                 $chhck =0; 
                                                                if($i==4){ 
                                                                    $chhck =1;
                                                                }
                                                                if($i==1){ 
                                                                    if(strtotime(date('Y-m-d'))==strtotime($rcent['planned_finish'])){
                                                                        $chhck =1;
                                                                    } 
                                                                }
                                                                if($i==2){ 
                                                                    if((strtotime($week_start)<=strtotime($rcent['planned_finish'])) && (strtotime($week_end)>=strtotime($rcent['planned_finish'])) ){
                                                                        $chhck =1;
                                                                    } 
                                                                }
                                                                if($i==3){ 
                                                                    if((strtotime($month_start)<=strtotime($rcent['planned_finish'])) && (strtotime($mont_last)>=strtotime($rcent['planned_finish'])) ){
                                                                        $chhck =1;
                                                                    } 
                                                                }
                                                                if($chhck ==1){ $totalchhck = $totalchhck+1; $nodata = 1; ?>
                                                                    <div  onclick="taskdetailspopup(<?php echo $rcent['id']; ?>)"  class="row mt-1 pb-3 pt-3 close" style="border-bottom:1px dashed #cccccc;">
                                                                        <div class="col-auto">
                                                                            <div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                                                <i class="fa fa-trophy f-w-600"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col p-0">
                                                                            <h6 class="f-w-600 mb-0"><?php echo $rcent['task_name']; ?></h6>
                                                                            <span class="text-muted f-12"><?php echo displayDate($rcent['planned_finish']); ?></span>
                                                                        </div>                          
                                                                    </div>
                                                                <?php }
                                                            } ?>
                                                            <?php  if($nodata==0){ ?>
                                                                        <h6 class="text-center mt-3 ccew"> No Data</h6>
                                                                         <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                                <?php }
                                                        }  ?>
                                                    </div>
                                                <?php $i++; }  ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>