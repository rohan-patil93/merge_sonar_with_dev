<?php

$_SESSION['apply_js']['multiselest'] = '1';
$_POST['company_id']=$_SESSION['company_id'];

    $rid = encodeId($_GET['Rid']);
    $rid = explode(":", $rid);
    $rid = $rid[1];

if(in_array('3',$_SESSION['role_ids'])){
    $_POST['user_id']=$_SESSION['emp_id'];
    $_POST['field']="emp_id";
}elseif(in_array('2',$_SESSION['role_ids'])){
    $_POST['user_id']=$_SESSION['userid'];
    $_POST['field']="client_id";
}else{
    $_POST['user_id']=$_SESSION['userid'];
    $_POST['notdisplaycurrent_status']='2';
}

$url = API."?mode=getemployeetickets&method=post";
$response = file_post_contents($url,$_POST);
$projecttasklistss = json_decode($response,1);
$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$wh2["field"] = "id,display_name,image,user_id";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=employees&data=".$data2;
$emplist = callAPI($url2);
$emplist = json_decode($emplist,1);
$stausresult = ViewStatusData();

$ststutwhere["where"] = array('table_name '=>'project_tasks','is_active'=>0,'company_id'=>$_SESSION['company_id']);
$ststutwhere["field"] = "id,name,status";
$statusdata = urlencode(json_encode($ststutwhere));
$statusurl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$statusdata;
$statusjsondecode = callAPI($statusurl);
$statusjsondecode = json_decode($statusjsondecode,1);

?>

<script type="text/javascript">

    function addtaskpopup(id=''){
     jQuery.ajax({
        url: "views/custom_insert_ticket_popup.php",
        data: "id="+id,
        type: "POST",
        success:function(data){
         $('#popupm').html(data);
         $('#kt_modal_KTDatatable_remotem').modal("show");
     },
     error:function (){}
 });
 }
  function statuspopup(status,id){
    var table_name = "project_tasks";
    alert(status);
    if(status == "<?php echo ST_ACTIVE; ?>"){
        var  str = '';
          str = 'table='+table_name+'&status='+status+'&hid='+id;
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatus&'+str,
            success: function(response) {
                window.location.reload();
            }
        });
    }else{
    jQuery.ajax({
      url: "views/custom_insert_status_popup.php",
      data:'table='+table_name+'&status='+status+'&rid='+id,
      type: "POST",
      success:function(data){
        $('#popup1').html(data);
        $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
    });
    }
  }

 function displaystausdrop(id,table,status_id){
    $.ajax({
      url: "ajax.php",
      data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
      type: "POST",
      success:function(data){
        console.log(data);
        $("#dropdownlist_"+id).html(data);
    },
    error:function (){}
});
}

function updateticketdetails(id,filed_name,value,pri=''){
    var values = value;
    var uid = '';
    if(filed_name == 'emp_id'){
        var emp = value.split('||');
        values =emp[0];
        uid = emp[3];
        $(".emp_id"+id).show();
        if(emp[1]!=""){
          $(".emp_id"+id).html('<img src="uploads/all/'+emp[1]+'"   class="img-radius img-50 align-top m-r-15" data-fallback="\"   alt="Attachment Preview" />');
        }else{
            $(".emp_id"+id).html('<span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" >'+emp[2]+'</span>');
        }
    }
    if(filed_name == 'priority'){
        $(".priority"+id).text(pri);
    }
    $.ajax({
      url: "ajax.php",
      data:'page=updateticketdetails&id='+id+'&filed_name='+filed_name+'&value='+values+'&uid='+uid,
      type: "POST",
      success:function(data){
        console.log(data);
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message']['3001']; ?>",'');
        location.reload();
    },
    error:function (){}
});
}
<?php if($rid !=""){ ?>
document.onreadystatechange = function () {
  if (document.readyState == "complete") {
    chnagessttts();
  }
}
function chnagessttts(){
    var element = document.getElementById("rowid<?php echo $rid; ?>");
    $("#rowid<?php echo $rid; ?>").addClass("bg-info");
    element.scrollIntoView();
    element.scrollIntoView(false);
    element.scrollIntoView({block: "end"});
    element.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
}
<?php } ?>
</script>
<div class="page-body p-2">
    <div class="card table-card">
        <div class="card-header">
            <h5>Tickets </h5>
            <div class="card-header-right">
                <?php  if(in_array('2',$_SESSION['role_ids'])){  ?>
                <button type="button" class="btn btn-primary waves-effect" onclick="addtaskpopup()"><i class="ti-plus text-white"></i> Add Task</button>
            <?php } ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
            </div>
        </div>
    <?php  if(in_array('2',$_SESSION['role_ids'])){
 ?>
  <form method="post" action="">
            <div class="row card-body pt-0 mt-0">
                <div class="col-lg-2">
                    <span class="form-label col-form-label">Status</span>
                    <select aria-label="Current Status" class="form-control form-select js-example-basic-single" name="current_status">
                     <option value="">All</option>
                     <?php foreach($statusjsondecode as $arststuslist){ ?>
                         <option value="<?php echo $arststuslist['status']; ?>" <?php echo $arststuslist['status']==$_POST['current_status']?"selected":""; ?>><?php echo $arststuslist['name']; ?></option>
                     <?php } ?>
                 </select>
             </div>
             <div class="col-lg-2">
                <span class="form-label col-form-label">Priority</span>
                <select aria-label="Priority" class="form-control form-select js-example-basic-single" name="priority">
                    <option value="">All</option>
                    <option <?php echo $_POST['priority']=='2'?"selected":""; ?> value="2">High</option>
                    <option <?php echo $_POST['priority']=='0'?"selected":""; ?> value="0">Low</option>
                    <option <?php echo $_POST['priority']=='1'?"selected":""; ?> value="1">Medium</option>
                </select>
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label">From</span>
                <input aria-label="Filterfromdate" type="text" class="form-control datepicker" name="filterfromdate" value="<?php echo $_POST['filterfromdate']; ?>">
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label">To</span>
                <input aria-label="Filtertodate" type="text" class="form-control datepicker" name="filtertodate" value="<?php echo $_POST['filtertodate']; ?>">
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label"></span>
                <button  type="submit" class="btn waves-effect waves-light hor-grd btn-grd-success d-block w-100 m-t-15">Search</button>
            </div>
        </div>
    </form>
        <div class="table-responsive dt-responsive p-2">
        <table id="basic-btn" class="table table-striped table-bordered nowrap">
            <thead>
                <tr>
                    <th scope=\"col\">#</th>
                    <th scope=\"col\">Ticket ID</th>
                    <th scope=\"col\">Ticket Subject</th>
                    <th scope=\"col\">Created Date</th>
                    <th scope=\"col\">Last Reply</th>
                    <th scope=\"col\">Priority</th>
                    <th scope=\"col\">Status</th>
                    <th scope=\"col\">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if(count((array)$projecttasklistss['tickets']) !="0"){ $mk=1; foreach($projecttasklistss['tickets'] as $projecttasklists){
                    $priority = "Low";
                    $bg_color = "btn-success";
                    if($projecttasklists['priority']=="1"){
                        $priority = "Medium";
                        $bg_color = "btn-info";
                    }elseif($projecttasklists['priority']=="2"){
                        $priority = "High";
                        $bg_color = "btn-danger";
                    }
                    ?>
                    <tr >
                        <th scope=\"col\"><?php echo $mk;?></th>
                        <td><a href="index.php?option=COM_VIEW_TICKETS&Rid=<?php echo deocdeId($projecttasklists['id']); ?>"><span class="text-primary"><?php echo $projecttasklists['ticket_id']; ?></span></a></td>
                        <td><?php echo $projecttasklists['task_name']; ?></td>
                        <td><?php echo date('d M Y h:i A',strtotime($projecttasklists['created_date'])); ?></td>
                        <td><?php echo date('d M Y h:i A',strtotime($projecttasklists['updated_date'])); ?></td>
                        <td>
                            <div class="dropdown-success dropdown open">
                                <button class="btn <?php echo $bg_color; ?> dropdown-toggle waves-effect waves-light btn-round priority<?php echo $projecttasklists['id']; ?>" type="button" id="dropdown-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true" <?php echo $projecttasklists['client_id'] == $_SESSION['userid']?'':'disabled'; ?>><?php echo $priority; ?></button>
                                <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block"  onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','0','High')">High</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','1','Medium')">Medium</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','2','Low')">Low</button>
                                </div>
                            </div>
                        </td>
                      <td>
                            <div class="dropdown-success dropdown open">
                             <button   onclick="displaystausdrop(<?php echo $projecttasklists['id']; ?>,'project_tasks',<?php echo $projecttasklists['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$projecttasklists['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$projecttasklists['current_status']]['name']; ?> </button>
                             <div class="dropdown-menu" id="dropdownlist_<?php echo $projecttasklists['id']; ?>">
                             </div>
                         </div>
                     </td>
                     <td>
                        <div class="dropdown-success dropdown open">
                            <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-3_2" data-bs-toggle="dropdown"  <?php echo ($projecttasklists['current_status'] <= '3' && $projecttasklists['client_id'] == $_SESSION['userid'])?'':'disabled'; ?> aria-haspopup="true" aria-expanded="true">Edit</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="addtaskpopup('<?php echo $projecttasklists['id']; ?>')">Edit</button>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php $mk++;
}
}else{  ?>

<tr><td colspan="12">

                                        <h4 class='p-40 text-center'><i class='fa fa-warning text-warning'></i> No Tickets Available</h4>
                          </td> </tr>

        <?php     } ?>
        </tbody>
    </table>
</div>
    <?php }elseif(in_array('3',$_SESSION['role_ids']) || in_array('49',$_SESSION['role_ids']) || in_array('50',$_SESSION['role_ids']) || in_array('51',$_SESSION['role_ids'])){   ?>
  <form method="post" action="">
            <div class="row card-body pt-0 mt-0">
                <div class="col-lg-2">
                    <span class="form-label col-form-label">Status</span>
                    <select aria-label="Current Status" class="form-control form-select js-example-basic-single" name="current_status">
                     <option value="">All</option>
                     <?php foreach($statusjsondecode as $arststuslist){ ?>
                         <option value="<?php echo $arststuslist['status']; ?>" <?php echo $arststuslist['status']==$_POST['current_status']?"selected":""; ?>><?php echo $arststuslist['name']; ?></option>
                     <?php } ?>
                 </select>
             </div>
             <div class="col-lg-2">
                <span class="form-label col-form-label">Priority</span>
                <select aria-label="Priority" class="form-control form-select js-example-basic-single" name="priority">
                    <option value="">All</option>
                    <option <?php echo $_POST['priority']=='2'?"selected":""; ?> value="2">High</option>
                    <option <?php echo $_POST['priority']=='0'?"selected":""; ?> value="0">Low</option>
                    <option <?php echo $_POST['priority']=='1'?"selected":""; ?> value="1">Medium</option>
                </select>
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label">From</span>
                <input aria-label="Filterfromdate" type="text" class="form-control datepicker" name="filterfromdate" value="<?php echo $_POST['filterfromdate']; ?>">
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label">To</span>
                <input aria-label="Filtertodate" type="text" class="form-control datepicker" name="filtertodate" value="<?php echo $_POST['filtertodate']; ?>">
            </div>
            <div class="col-lg-2">
                <span class="form-label col-form-label"></span>
                <button  type="submit" class="btn waves-effect waves-light hor-grd btn-grd-success d-block w-100 m-t-15">Search</button>
            </div>
        </div>
    </form>
        <div class="table-responsive dt-responsive p-2">
        <table id="basic-btn_2" class="table table-striped table-bordered nowrap">
            <thead>
                <tr>
                    <th scope=\"col\">#</th>
                    <th scope=\"col\">Ticket ID</th>
                    <th scope=\"col\">Ticket Subject</th>
                    <th scope=\"col\">Created Date</th>
                    <th scope=\"col\">Last Reply</th>
                    <th scope=\"col\">Priority</th>
                    <th scope=\"col\">Status</th>
                    <th scope=\"col\">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if(count((array)$projecttasklistss['tickets']) !="0"){  $mk=1; foreach($projecttasklistss['tickets'] as $projecttasklists){

                    $priority = "Low";
                    $bg_color = "btn-success";
                    if($projecttasklists['priority']=="1"){
                        $priority = "Medium";
                        $bg_color = "btn-info";
                    }elseif($projecttasklists['priority']=="2"){
                        $priority = "High";
                        $bg_color = "btn-danger";
                    }
                    ?>
                    <tr>
                        <th scope=\"col\"><?php echo $mk;?></th>
                        <td><a href="index.php?option=COM_VIEW_TICKETS&Rid=<?php echo deocdeId($projecttasklists['id']); ?>"><span class="text-primary"><?php echo $projecttasklists['ticket_id']; ?></span></a></td>
                        <td><?php echo $projecttasklists['task_name']; ?></td>
                        <td><?php echo date('d M Y h:i A',strtotime($projecttasklists['created_date'])); ?></td>
                        <td><?php echo  date('d M Y h:i A',strtotime($projecttasklists['updated_date'])); ?></td>
                        <td>
                            <div class="dropdown-success dropdown open">
                                <button class="btn <?php echo $bg_color; ?> dropdown-toggle waves-effect waves-light btn-round priority<?php echo $projecttasklists['id']; ?>" disabled type="button" id="dropdown-3_3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true" ><?php echo $priority; ?></button>
                                <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block"  onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','0','High')">High</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','1','Medium')">Medium</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','2','Low')">Low</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown-success dropdown open">
                             <button <?php echo $projecttasklists['emp_id'] != $_SESSION['emp_id']?"disabled":"";?> onclick="displaystausdrop(<?php echo $projecttasklists['id']; ?>,'project_tasks',<?php echo $projecttasklists['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$projecttasklists['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$projecttasklists['current_status']]['name']; ?> </button>
                             <div class="dropdown-menu" id="dropdownlist_<?php echo $projecttasklists['id']; ?>">
                             </div>
                         </div>
                     </td>
                     <td>
                        <div class="dropdown-success dropdown open">
                            <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-3_4" data-bs-toggle="dropdown"  disabled  aria-haspopup="true" aria-expanded="true">Edit</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="addtaskpopup('<?php echo $projecttasklists['id']; ?>')">Edit</button>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php $mk++;
}
}else{  ?>

<tr><td colspan="12">

                                        <h4 class='p-40 text-center'><i class='fa fa-warning text-warning'></i> No Tickets Available</h4>
                          </td> </tr>

        <?php     } ?>
        </tbody>
    </table>
</div>
    <?php }else{ ?>
<?php
  $opentickets = 0;
    $inprogresstickets = 0;
    $fixedtickets = 0;
    $closetickets = 0;
foreach($projecttasklistss['dashboard'] as $dash){
switch($dash['current_status']){
  case ST_APPROVAL_PENDING:
  $totaltickets++;
    $opentickets++;
    break;
  case ST_INPROGRESS:
    $totaltickets++;
    $inprogresstickets++;
    break;
  case ST_COMPLETE:
  $totaltickets++;
    $fixedtickets++;
    break;
  case ST_APPROVED:
  $totaltickets++;
    $closetickets++;
    break;

                        default:
                            break;
}
}
?>
      <div class="row card-body mt-3">
            <div class="col-xl-3 col-md-12">
                <div class="card proj-t-card">
                    <div class="card-body">
                        <div class="align-items-center m-b-30">
                            <div class="col p-l-0">
                                <h6 class="m-b-5">Open Tickets</h6>
                            </div>
                        </div>
                        <div class="align-items-center text-left">
                          <?php
                          if($totaltickets !=0 && $totaltickets!=""){
                                $progersspercentage = round(($opentickets / $totaltickets) * 100,2);
                          }else{
                            $progersspercentage= 0;
                          }
                          ?>
                            <div class="col">
                                <h3 class="m-b-0 f-w-600"><?php echo $opentickets;?></h3>
                            </div>
                            <div class="col mt-4 proj-progress-card">
                                <div class="progress">
                                    <div class="progress-bar bg-c-red" style="width:<?php echo $progersspercentage; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="pt-badge bg-c-green"><?php echo $progersspercentage; ?>%</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-12">
                <div class="card proj-t-card">
                    <div class="card-body">
                        <div class="align-items-center m-b-30">
                            <div class="col p-l-0">
                                <h6 class="m-b-5">In-progress Tickets</h6>
                            </div>
                        </div>
                        <div class="align-items-center text-left">
                          <?php
                          if($totaltickets !=0 && $totaltickets!=""){
                                $inprogreepregressbar = round(($inprogresstickets / $totaltickets) * 100,2);
                          }else{
                            $inprogreepregressbar= 0;
                          }
                          ?>
                            <div class="col">
                                <h3 class="m-b-0 f-w-600"><?php echo $inprogresstickets;?></h3>
                            </div>
                            <div class="col mt-4 proj-progress-card">
                                <div class="progress">
                                    <div class="progress-bar bg-c-red" style="width:<?php echo $inprogreepregressbar; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="pt-badge bg-c-green"><?php echo $inprogreepregressbar; ?>%</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-12">
                <div class="card proj-t-card">
                    <div class="card-body">
                        <div class="align-items-center m-b-30">

                            <div class="col p-l-0">
                                <h6 class="m-b-5">Fixed Tickets</h6>

                            </div>
                        </div>
                        <div class="align-items-center text-left">
                          <?php
                          if($totaltickets !=0 && $totaltickets!=""){
                                $fixedpregressbar = round(($fixedtickets / $totaltickets) * 100,2);
                          }else{
                            $fixedpregressbar= 0;
                          }
                          ?>

                            <div class="col">
                                <h3 class="m-b-0 f-w-600"><?php echo $fixedtickets; ?></h3>
                            </div>
                            <div class="col mt-4 proj-progress-card">
                                <div class="progress">
                                    <div class="progress-bar bg-c-red" style="width:<?php echo $fixedpregressbar; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="pt-badge bg-c-red"><?php echo $fixedpregressbar; ?>%</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-12">
                <div class="card proj-t-card">
                    <div class="card-body">
                        <div class="align-items-center m-b-30">

                            <div class="col p-l-0">
                                <h6 class="m-b-5">Close Tickets</h6>

                            </div>
                        </div>
                        <div class="align-items-center text-left">
                          <?php
                          if($totaltickets !=0 && $totaltickets!=""){
                                $closedpregressbar = round(($closetickets / $totaltickets) * 100,2);
                          }else{
                            $closedpregressbar= 0;
                          }
                          ?>
                            <div class="col">
                                <h3 class="m-b-0 f-w-600"><?php echo $closetickets;?></h3>
                            </div>
                            <div class="col mt-4 proj-progress-card">
                                <div class="progress">
                                    <div class="progress-bar bg-c-red" style="width:<?php echo $closedpregressbar; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="pt-badge bg-c-red"><?php echo $closedpregressbar; ?>%</h6>
                    </div>
                </div>
            </div>
        </div>
        <form method="post" action="">
            <div class="row card-body pt-0 mt-0">
                <div class="col-lg-2 form-group">
                    <span class="form-label col-form-label">Employee</span>
                    <select aria-label="Emp" class="form-select js-example-basic-single col-sm-12" name="emp_id">
                        <option value="">Select Employee</option>
                        <?php foreach($emplist as $list22){ ?>
                            <option value="<?php echo $list22['id']; ?>" <?php echo $list22['id']==$_POST['emp_id']?"selected":""; ?>><?php echo $list22['display_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group">
                    <span class="form-label col-form-label">Status</span>
                    <select aria-label="Current Status" class="form-control form-select js-example-basic-single" name="current_status">
                     <option value="">All</option>
                     <?php foreach($statusjsondecode as $arststuslist){ ?>
                         <option value="<?php echo $arststuslist['status']; ?>" <?php echo $arststuslist['status']==$_POST['current_status']?"selected":""; ?>><?php echo $arststuslist['name']; ?></option>
                     <?php } ?>
                 </select>
             </div>
             <div class="col-lg-2 form-group">
                <span class="form-label col-form-label">Priority</span>
                <select aria-label="Priority" class="form-control form-select js-example-basic-single" name="priority">
                    <option value="">All</option>
                    <option <?php echo $_POST['priority']=='2'?"selected":""; ?> value="2">High</option>
                    <option <?php echo $_POST['priority']=='0'?"selected":""; ?> value="0">Low</option>
                    <option <?php echo $_POST['priority']=='1'?"selected":""; ?> value="1">Medium</option>
                </select>
            </div>
            <div class="col-lg-2 form-group">
                <span class="form-label col-form-label">From</span>
                <input aria-label="Filterfromdate" type="text" class="form-control datepicker" name="filterfromdate" value="<?php echo $_POST['filterfromdate']; ?>">
            </div>
            <div class="col-lg-2 form-group">
                <span class="form-label col-form-label">To</span>
                <input aria-label="Filtertodate" type="text" class="form-control datepicker" name="filtertodate" value="<?php echo $_POST['filtertodate']; ?>">
            </div>
            <div class="col-lg-1">
                <button  type="submit" class="btn btn-primary m-t-45 btn-md">Search</button>
            </div>
        </div>
    </form>ss
    <div class="table-responsive dt-responsive p-3">
        <table id="basic-btn_3" class="table table-striped table-bordered nowrap">
            <thead>
                <tr>
                    <th scope=\"col\">#</th>
                    <th scope=\"col\">Ticket ID</th>
                    <th scope=\"col\">Ticket Subject</th>
                    <th scope=\"col\">Assigned Staff</th>
                    <th scope=\"col\">Created Date</th>
                    <th scope=\"col\">Last Reply</th>
                    <th scope=\"col\">Priority</th>
                    <th scope=\"col\">Status</th>
                    <th scope=\"col\">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if(count((array)$projecttasklistss['tickets']) !="0"){  $mk=1; foreach($projecttasklistss['tickets'] as $projecttasklists){
                    $priority = "Low";
                    $bg_color = "btn-success";
                    if($projecttasklists['priority']=="1"){
                        $priority = "Medium";
                        $bg_color = "btn-info";
                    }elseif($projecttasklists['priority']=="2"){
                        $priority = "High";
                        $bg_color = "btn-danger";
                    }
                    ?>
                    <tr id="rowid<?php echo $projecttasklists['id']; ?>">
                        <th scope=\"col\"><?php echo $mk;?></th>
                        <td><a href="index.php?option=COM_VIEW_TICKETS&Rid=<?php echo deocdeId($projecttasklists['id']); ?>"><span class="text-primary"><?php echo $projecttasklists['ticket_id']; ?></span></a></td>
                        <td><?php echo $projecttasklists['task_name']; ?></td>
                        <td class="text-left">
                           <?php if($projecttasklists['current_status'] != ST_DRAFT){?>
                            <div class="d-inline-block align-middle ">
                                <span class="emp_id<?php echo $projecttasklists['id'];?>" style="display:<?php echo $projecttasklists['emp_id']=='0'?'none':'';?>">
                                <?php  $empdata = filterArrayValue($emplist,'id',$projecttasklists['emp_id']);
                                if($empdata[0]['image']!="") {?>
                                    <img src="uploads/all/<?php echo $empdata[0]['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($empdata[0]['display_name']); ?></span>
                                <?php } ?>
                                </span>
                                <div class="d-inline-block">
                                    <select aria-label="Select option" class="form-select js-example-basic-single col-sm-12" onchange="updateticketdetails('<?php echo $projecttasklists['id'] ?>','emp_id',this.value)">
                                        <option>Select Any One</option>
                                        <?php foreach($emplist as $list2){ ?>
                                            <option value="<?php echo $list2['id'].'||'.$list2['image'].'||'.initials($list2['display_name']).'||'.$list2['user_id']; ?>" <?php echo $list2['id']==$projecttasklists['emp_id']?"selected":""; ?>><?php echo $list2['display_name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php }else{ echo "-- ";
} ?>
                        </td>
                        <td><?php echo date('d M Y h:i A',strtotime($projecttasklists['created_date'])); ?></td>
                        <td><?php echo  date('d M Y h:i A',strtotime($projecttasklists['updated_date'])); ?></td>
                        <td>
                            <div class="dropdown-success dropdown open">
                                <button class="btn <?php echo $bg_color; ?> dropdown-toggle waves-effect waves-light btn-round priority<?php echo $projecttasklists['id']; ?>" type="button" id="dropdown-3_5" data-bs-toggle="dropdown" aria-haspopup="true" <?php echo $projecttasklists['client_id'] == $_SESSION['userid']?'':'disabled'; ?> aria-expanded="true"><?php echo $priority; ?></button>
                                <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block"  onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','0','High')">High</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','1','Medium')">Medium</button>
                                    <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="updateticketdetails('<?php echo $projecttasklists['id'] ?>','priority','2','Low')">Low</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown-success dropdown open">
                             <button onclick="displaystausdrop(<?php echo $projecttasklists['id']; ?>,'project_tasks',<?php echo $projecttasklists['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$projecttasklists['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$projecttasklists['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled><?php echo $stausresult[$projecttasklists['current_status']]['name']; ?> </button>
                             <div class="dropdown-menu" id="dropdownlist_<?php echo $projecttasklists['id']; ?>">
                             </div>
                         </div>
                     </td>
                     <td>
                        <div class="dropdown-success dropdown open">
                            <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-3_6" data-bs-toggle="dropdown"  <?php echo ($projecttasklists['current_status'] <= '3' && $projecttasklists['client_id'] == $_SESSION['userid'])?'':'disabled'; ?> aria-haspopup="true" aria-expanded="true">Edit</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <button type="button" class="dropdown-item waves-light waves-effect d-block" onclick="addtaskpopup('<?php echo $projecttasklists['id']; ?>')">Edit</button>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php $mk++;
}
}else{  ?>

<tr><td colspan="12">

                                        <h4 class='p-40 text-center'><i class='fa fa-warning text-warning'></i> No Tickets Available</h4>
                          </td> </tr>

        <?php     } ?>
        </tbody>
    </table>
</div>
</div>
<?php } ?>
</div>
</div>
