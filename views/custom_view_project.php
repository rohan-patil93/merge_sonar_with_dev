<?php
if(isset($_POST['submit'])){
    if(!empty($_FILES["project_logo"]["name"])){
        $temp = explode(".", $_FILES["project_logo"]["name"]);
        $newfilename =  "project_logo_".time()."_".uniqid().".".end($temp);
        move_uploaded_file($_FILES["project_logo"]["tmp_name"], "uploads/all/" . $newfilename);
    }else{
       $newfilename = $_POST['hid_image'];
   }
   $_POST['project_logo'] = $newfilename;
   $hid = $_POST['hid'];
   $_POST['planned_start'] = date("Y-m-d",strtotime(str_replace('/"', '-', $_POST['planned_start'])));
   $_POST['planned_finish'] = date("Y-m-d",strtotime(str_replace('/"', '-', $_POST['planned_finish'])));
   $_POST['release_date'] = date("Y-m-d",strtotime(str_replace('/"', '-', $_POST['release_date'])));
   $old_assinger_id = $_POST['old_assinger_id'];
   unset($_POST['old_assinger_id']);
   unset($_POST['hid_image']);
   unset($_POST['submit']);
   unset($_POST['hid']);
   $_POST['updated_date'] = nowDate();
   $_POST['updated_by'] = $_SESSION['userid'];
   $url = API."?mode=updaterowtabledata&rid=".$hid."&table=projects&method=post";
   $response = file_post_contents($url, $_POST, 'update');
   if($response) {
   }
   if($old_assinger_id != $_POST['assinger_id']){
        $url = API."?mode=tochangeprojectowner&old_assinger_id=".$old_assinger_id."&assinger_id=".$_POST['assinger_id']."&rid=".$hid;
        $respons = callAPI($url);
   }

}
$project_id = $_GET['RID'];
$url = API."?mode=togetprojectviewalldetails&project_id=".$project_id;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$emps = $data['emps'];
$project = $data['project'];
$emp_phaase_tasks = $data['emp_phaase_tasks'];
$list_tasks = $data['list_task'];
$task_attachment = $data['task_attachment'];
$listtask = array(); $statusemp_tasks = array(); $empwisetotaltask = array();
$team_member = array();
foreach($data['project_teams'] as $team){
    $team_member[]=$team['user_id'];
    $statusemp_tasks[$team['user_id']] = array('yet'=>0, 'open'=>0, 'completed'=>0, 'total'=>0);
}
foreach($data['project_task_details'] as $listtask){
    if($listtask['current_status']==0){
        $yocp['yet'] = $yocp['yet']+1;
        $statusemp_tasks[$listtask['assigen_id']]['yet'] = $statusemp_tasks[$listtask['assigen_id']]['yet']+1;
    }elseif($listtask['current_status']==199 ){
        $yocp['completed'] = $yocp['completed']+1;
        $statusemp_tasks[$listtask['assigen_id']]['completed'] = $statusemp_tasks[$listtask['assigen_id']]['completed']+1;
    }else{
        $yocp['open'] = $yocp['open']+1;
        $statusemp_tasks[$listtask['assigen_id']]['open'] = $statusemp_tasks[$listtask['assigen_id']]['open']+1;
    }
    if(strtotime(date('Y-m-d')) >strtotime($listtask['planned_finish']) && $listtask['current_status'] !=199 ){
        $verdue = $verdue+1;
    }
    $datewisetask[$listtask['planned_finish']][] = $listtask;
    $statusemp_tasks[$listtask['assigen_id']]['total'] = $statusemp_tasks[$listtask['assigen_id']]['total']+1;
}
foreach($data['project_status'] as $pst){
    $pstd[$pst['status']] = $pst;
}
foreach($data['task_status'] as $pst){
    $tsd[$pst['status']] = $pst;
}


$cdaa[] = "Yet to start:".($yocp['yet']!="" ? $yocp['yet'] : 0);
$cdaa[] = "Inprogress:".($yocp['open'] !="" ? $yocp['open'] : 0 );
$cdaa[] = "Completed:".($yocp['completed'] !="" ? $yocp['completed'] : 0);
$cdaa[] = "Overdue:".$verdue;
$total_cdaa = $yocp['yet']+$yocp['open']+$yocp['completed'];
$_SESSION['apply_js']['project_dashboard'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['dropzone'] = 1;
$primarystatus = array('0','101','5','199');
foreach($data['customers'] as $datacus){
    $customer[$datacus['id']] = $datacus;
}
foreach($data['project_teams'] as $pteam){
    $disteam[$pteam['role_id']][] = $pteam;
}

?>
<style type="text/css">
.personal-pro{ background: #f1faff; border: 1px dashed #00b2ff; padding: 20px 20px; border-radius: 10px;}
.personal-pro i{color: #00b2ff; font-size: 33px; transform: rotate(-45deg);}
.probtn{ border-radius: 10px; font-size: 16px; padding: 10px 25px; font-weight: 600;}

.usage{ border: 1px dashed #00b2ff; padding: 20px 20px; border-radius: 10px; cursor: pointer;}
.usage p{font-size: 12px; cursor: pointer;}
.usage h5{cursor: pointer;}
.bg-pop-white { background-color: #FFFFFF; }
.bg-pop-active { background-color: #f1faff; }
.teamdrop { width: 80px; background-color: #f9f9f9 !important; padding: 5px !important; border: #f9f9f9;border-radius: 5px;font-size: 12px; }
.t-color{
    width: 150px;
    height: 40px;
    line-height: 40px;
    border-radius: 5px;
    margin-right: 5px;
    display: inline-block;
}
.f-11{
    font-size: 11px;
}
.design-sec{position: absolute; background: #ffffff; z-index: 999999; border: 1px solid #cccccc; padding: 10px; border-radius: 5px; width: 320px;}
.wd-p-49{
    width: 49%;
}
.wd-p-100{
    width: 100%;
}
</style>

<script>
    function showptaskwise(val){
         var idval = $("#filetaskdis_"+val).val();
         if(idval==0){
            $("#filetask_"+val).show('slow');
            $("#filetaskdis_"+val).val('1');
        }
        if(idval==1){
            $("#filetask_"+val).hide('slow');
            $("#filetaskdis_"+val).val('0');
        }

    }
    function showphase(val){
        var idval = $("#phasedis_"+val).val();
        if(idval==0){
            $("#filephase_"+val).show('slow');
            $("#phasedis_"+val).val('1');
        }else{
            $("#filephase_"+val).hide('slow');
            $("#phasedis_"+val).val('0');
        }
    }
    function addphases(project_id){
        var count = $(".itemRow").length;
        var htmlRows = '';
        jQuery.ajax({
              url: "ajax.php",
              data:'page=addupdateprojectphases&project_id='+project_id,
              type: "POST",
              success:function(data){
                console.log("Responce:"+data);
               htmlRows ='<div class="col-lg-12 row" id="ptphdiv_'+data+'"><div class="col-lg-8 mt-3 itemRow"><input aria-label="Phase[]" type="text" name="phase[]" id="phase_'+data+'" value=""  onclick="updatephasettusval('+data+',1111,this.value)" class="form-control" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> </div><div class="col-lg-1 mt-4"> <button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger cursor-pointer" onclick="updatephasettusval('+data+',2222,1)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div></div>';
                $('#AddItem').append(htmlRows);

            },
            error:function (){}
        });
    }

    function saveprojectphases(project_id){
        var count = $(".itemRow").length;
        var str = "";
        for(var i=1; i<=count; i++){
            str+= '&phase_'+i+'='+$("#phase_"+i).val();
        }
        jQuery.ajax({
              url: "ajax.php",
              data:'page=addupdateprojectphases'+str+'&count='+count,
              type: "POST",
              success:function(data){
                console.log("Responce:"+data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Type Upadate Successfully','');
            },
            error:function (){}
        });
    }

    function deletethestatus(id){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatedynamicfiedrojecttaskdetail&table_name=project_task_status&field=is_active&fielddata=1&update_id='+id,
            type: "POST",
            success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project  status deleted Successfully','');
            window.location.href += "&TAB=8";
            },
            error:function (){}
        });
    }
    function removeteamember(teamid){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=tocheckprojectteamuncompletetasks&rowid='+teamid,
            type: "POST",
            success:function(data){
                console.log(data);
                if (confirm(data) == true) {
                    $.ajax({
                        url: "ajax.php",
                        data:'page=toaddupdatetheprojectteamdata&table_name=project_teams&is_active=1&id='+teamid,
                        type: "POST",
                        success:function(data){
                            console.log(data);
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Member is removed to this project','');
                    },
                    error:function (){}
                });
                }
            },
            error:function (){}
        });
    }
    function removeteamemberq(teamid){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=tocheckprojectteamuncompletetasks&rowid='+teamid,
            type: "POST",
            success:function(data){
                console.log(data);

            },
            error:function (){}
        });

        SweetAlert.fire({
          title: data,
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Yes',
          denyButtonText: 'No',
      }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
                url: "ajax.php",
                data:'page=updatedynamicfiedrojecttaskdetail&table_name=project_teams&field=is_active&fielddata=1&update_id='+teamid,
                type: "POST",
                success:function(data){
                    console.log(data);
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Member is added to this project','');
                       },
                       error:function (){}
                   });
        }
    })
  }
  function addprojectmembers(project_id, company_id){
    var emp_id = $("#new_emp_id").val();
    var role_id = $("#new_role_id").val();
    var cost = $("#cost").val();
    var budget = $("#budget").val();

    jQuery.ajax({
        url: "ajax.php",
        data:'page=toaddupdatetheprojectteamdata&table_name=project_teams&company_id='+company_id+'&project_id='+project_id+'&role_id='+role_id+'&user_id='+emp_id+'&rate_hours='+cost+'&budget_rate_hours='+budget,
        type: "POST",
        success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Team Member Added Successfully','');
            window.location.href += "&TAB=3";
        },
        error:function (){}
    });
}
function memberdisplay(){
    console.log("working");
    $(".popup-member").show('slow');
}
function closebtn(){
    console.log("close");
    $(".popup-member").hide('slow');
}
$(document).ready(function(){

  $(".filter-option").click(function(){
    $(".filter-option-open").toggle('slideDown');
});
  $(".close-filter").click(function(){
    $(".filter-option-open").hide('slideUp');
});
});
function changeprojecttype(val){
    console.log(val);
    var oldval = $("#project_type").val();
    var count = $(".itemRow").length;
    $("#changeprojecttype_"+oldval).removeClass("bg-pop-active");
    $("#changeprojecttype_"+oldval).addClass("bg-pop-white");
    $("#changeprojecttype_"+val).removeClass("bg-pop-white");
    $("#changeprojecttype_"+val).addClass("bg-pop-active");
    $("#project_type").val(val);
}
function saveprojecttype(update_id){
    var type_id = $("#project_type").val();
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name=projects&field=project_type&fielddata='+type_id+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Type Upadate Successfully','');
        },
        error:function (){}
    });
    }
    function checkandsave(project_id, label_id, name, code ){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=addupdatelabeldata&project_id='+project_id+'&label_id='+label_id+'&name='+name+'&code='+code,
          type: "POST",
          success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Label Upadate Successfully','');
        },
        error:function (){}
    });
    }
    function updatelabelval(project_id, label_id, name){
        console.log(name);
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatelabelval&project_id='+project_id+'&label_id='+label_id+'&name='+name,
          type: "POST",
          success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Label Upadate Successfully','');
        },
        error:function (){}
    });
    }
    function updatetasksttusval(id, filed,val){
        console.log(name);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dynmicupdatetable&id='+id+'&'+filed+'='+val+'&table=project_task_status',
            type: "POST",
            success:function(data){
                console.log("Responce:"+data);
                var msg = 'Project  Status Upadate Successfully';
                if(filed == 'is_active'){
                    msg = 'Project  Status deleted Successfully';
                    $("#statusdiv_"+id).hide('slow');
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
            },
            error:function (){}
        });
    }
    function updatephasettusval(id, filed,val){
        console.log(val);
        if(filed == 1111){
            filed = 'project_phase_name';
        }
        if(filed == 2222){
            filed = 'is_active';
        }
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dynmicupdatetable&id='+id+'&'+filed+'='+val+'&table=project_phases',
            type: "POST",
            success:function(data){
                console.log("Responce:"+data);
                var msg = 'Project  Status Upadate Successfully';
                if(filed == 'is_active'){
                    msg = 'Project  Phase deleted Successfully';
                    $("#ptphdiv_"+id).hide('slow');
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
            },
            error:function (){}
        });
    }

</script>
<div class="page-body">
    <div class="card mb-3">
        <div class="card-body p-4">
            <div class="row pb-4">
                <div class="col-lg-2 col-md-3 col-sm-12 col-12 text-center">

                    <?php $project_logo = $project['project_logo']!="" ? $project['project_logo'] : 'no-image.jpg'; ?>

                    <?php $pp = 100; if($data['graph']['total_task'] !=0){
                        $pp = round(($data['graph']['complete_task']/$data['graph']['total_task'])*100);
                    } ?>

                    <div class="border p-2 d-inline-block position-absolute bg-primary" style="left: 60px;top: 10px;border-radius: 30px; padding: 9px 6px !important;">
                        <span><?php echo round($pp); ?>%</span>
                    </div>



                    <div class="m-b-10" style="text-align: -webkit-center;">
                        <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation((($pp)*3.6)); ?>deg; --p2_color: #ff5370;--p1_color: #2ed8b6;">
                                <?php
                        $disname = $project['project_name'];
                        if($project_logo!=""){ ?>
                          <img src="<?php echo "uploads/all/".$project_logo; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
                       <?php }elseif($_SESSION['employee']['gender'] !=""){ ?>
                                <img src="uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
                        <?php }else{ ?>
                          <span class="text-dark f-16 f-w-600 d-block" style="background-color: white !important;"><?php echo initials($disname); ?></span>
                      <?php } ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">

                    </div>

                    <div class="card-body custom-scrollw c-row">
                      <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:#00bcd4;">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col">
                              <h6 class="m-b-5 text-white">Yet to start <span class="f-w-600 float-right p-0 f-18"><?php echo $yocp['yet']!="" ? $yocp['yet']: 0; ?> </span>
                              </h6>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:#11c15b;">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col">
                              <h6 class="m-b-5 text-white">Inprogress <span class="f-w-600 float-right p-0 f-18"><?php echo $yocp['open']!="" ? $yocp['open']: 0; ?> </span>
                              </h6>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:#ffe100;">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col">
                              <h6 class="m-b-5 text-white">Completed <span class="f-w-600 float-right p-0 f-18"><?php echo $yocp['completed']!="" ? $yocp['completed']: 0; ?> </span>
                              </h6>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:#FE8A7D;">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col">
                              <h6 class="m-b-5 text-white">Overdue <span class="f-w-600 float-right p-0 f-18"><?php echo $yocp['completed']!="" ? $yocp['completed']: 0; ?> </span>
                              </h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                </div>
                <div class="col-lg-7 col-md-9 col-sm-12 col-12 mb-4">
                    <div class="m-l-15 m-r-15">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <h5 class="f-w-600"><?php echo $project['project_name']; ?>
                                    <span  class="form-label label label-success f-14 m-l-10" style="font-size:10px; background-color: <?php echo $_SESSION['status'][$project['current_status']]['background_color']; ?>;">
                                        <?php echo  $pstd[$project['current_status']]['name']; ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="dropdown-primary dropdown open float-right mob-float-left" style="" >
                                    <a href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo $project_id; ?>" class="btn btn-success">  View Task </a>
                                </div>
                            </div>
                        </div>

                        <p class="f-w-500 text-muted mt-2"><?php echo $project['description']; ?></p>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <h5 class="f-w-600 d-inline-block">Customer : <span class="text-muted">
                                    <?php echo  $customer[$project['customer_id']]['display_name']; ?></span>
                                </h5>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 owner-left">
                                <h5 class="float-end f-w-600">Owner :
                                    <span class="text-muted"><?php echo  $emps[$project['assigner_id']]['display_name']; ?></span>
                                </h5>
                            </div>
                        </div>

                        <div class="row mt-3">




                            <div class="mt-3">
                                <div class="row">
                                   <?php foreach($data['roles'] as $role){ ?>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-12 mb-3">
                                            <div class="f-w-600"> <?php echo $role['role_name']; ?> </div>
                                            <div class=" multi-user">
                                            <?php foreach($disteam[$role['id']] as $team) {?>

                                                <?php if($emps[$team['user_id']]['image']!=""){ ?>
                                                    <a href=""><img  src="/uploads/all/<?php echo $emps[$team['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                <?php }else{ ?>
                                                    <a href=""><span class=""><?php echo initials($emps[$team['user_id']]['display_name']); ?></span></a>
                                                <?php } ?>

                                            <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 col-12 text-center">
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width wd-p-100" style="">
                        <span class="f-14 text-muted">Due Date</span>
                        <h5 class="f-w-600 f-16 text-danger"><?php echo fromDate($project['planned_finish']); ?></h5>
                    </div>
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width-2 wd-p-49" style="">
                        <span class="f-14 text-muted">Planned Start</span>
                        <h5 class="f-w-600 f-16 text-success"><?php echo fromDate($project['planned_start']); ?></h5>
                    </div>
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width-2 wd-p-49" style="">
                        <span class="f-14 text-muted">Planned Finish</span>
                        <h5 class="f-w-600 f-16 text-success"><?php echo fromDate($project['planned_finish']); ?></h5>
                    </div>
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width-2 wd-p-49" style="">
                        <span class="f-14 text-muted">Actual Start</span>
                        <h5 class="f-w-600 f-16 text-success"><?php echo fromDate($project['actual_start']); ?></h5>
                    </div>
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width-2 wd-p-49" style="">
                        <span class="f-14 text-muted">Actual Finish</span>
                        <h5 class="f-w-600 f-16 text-success"><?php echo fromDate($project['actual_finish']); ?></h5>
                    </div>
                    <div class="bdr-dashed p-3 mb-1 text-center d-inline-block dated-width wd-p-100" style="">
                        <span class="f-14 text-muted">Budget</span>
                        <h5 class="f-w-600 f-16 text-danger"><?php echo customeummarmonyformateifinecheck($project['project_cost']); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <?php $listactive = $_GET['TAB']!="" ? $_GET['TAB'] : 6; ?>
            <div class="row">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs md-tabs mt-3 project_tab" style="border-bottom: none;">
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==0){ echo "active";
} ?>" data-bs-toggle="tab" href="#dashboard" role="tab">Dash Board</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600" <?php if($listactive ==2){ echo "active";
} ?> data-bs-toggle="tab" href="#project-type" role="tab">Project Type</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600" <?php if($listactive ==9){ echo "active";
} ?> data-bs-toggle="tab" href="#project-phases" role="tab">Project Phases</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==3){ echo "active";
} ?>" data-bs-toggle="tab" href="#project-team" role="tab">Project Team</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==4){ echo "active";
} ?>" data-bs-toggle="tab" href="#files" role="tab">Files</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==5){ echo "active";
} ?>" data-bs-toggle="tab" href="#activity" role="tab">Activity</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==6){ echo "active";
} ?>" data-bs-toggle="tab" href="#project-setting" role="tab">Project Setting</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==7){ echo "active";
} ?>" data-bs-toggle="tab" href="#project-label" role="tab">Project Labels</a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link f-w-600 <?php if($listactive ==8){ echo "active";
} ?>" data-bs-toggle="tab" href="#project-task-status" role="tab">Project Task Status</a>
                            <div class="slide"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">

        <div class="tab-pane <?php if($listactive ==0){ echo "active";
} ?>" id="dashboard" role="tabpanel">
        </div>
        <div class="tab-pane <?php if($listactive ==1){ echo "active";
} ?>" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tasks Summary</h5>
                            <b class="mt-4 f-w-600 d-block text-muted f-14">Total Tasks &nbsp; &nbsp;
                                <span class="form-label label label-success f-14 m-l-10">
                                <?php echo $total_cdaa; ?>      </span>
                            </b>

                        </div>
                        <div class="card-body">
                            <div id="chart_Donut_Project" class="float-left" style="width: 60%; height: 235px;"></div>
                            <input type="hidden" name="chart_project" id="chart_project" value="<?php echo implode("||",$cdaa); ?>" >
                            <div class="four-column">
                                <?php $stcolor = array('#448aff',  '#ffe100', '#11c15b', '#FE8A7D');
                                 foreach($cdaa as $kk=>$cdata){
                                    $tcda = explode(":",$cdata);
                                    ?>
                                <div class="row mt-2 pb-2 p-l-5 " style="border-bottom:1px dashed #cccccc;">
                                    <div class="col-lg-9 col-md-9 col-sm-12 col-12 p-0" style="color: <?php echo $stcolor[$kk]; ?>;">
                                        <div class="f-w-600 f-14">
                                            <?php echo $tcda['0']; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12 col-12 p-0">
                                        <span class="text-muted f-16">
                                            <?php echo $tcda['1']; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Project Tasks</h5>
                            <b class="mt-4 f-w-600 d-block text-muted f-14">Total <?php echo $yocp['yet']; ?> tasks in Yet to start</b>
                        </div>
                        <div class="card-body my-tasks custom-scroll" style="max-height: 300px;">
                            <?php if(count((array)$data['project_task_details'])>0){
                                foreach((array)$data['project_task_details'] as $task){
                            ?>
                            <div class="row align-items-center mb-3 m-l-10">

                                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                                    <p class="f-14 f-w-600 mb-0"><?php echo $task['task_name']; ?></p>
                                    <span class="text-muted f-14">Due in <?php echo notificationdatesdiff($task['planned_finish'],'N') ?> day</span>
                                    <a class="text-primary" href=""><?php echo $emps[$task['assigen_id']]['display_name']; ?></a>
                                    <span  class="form-label label label-success f-14 m-l-10" style="font-size:10px; background-color: <?php echo $_SESSION['status'][$task['current_status']]['background_color']; ?>;">
                                        <?php echo  $tsd[$task['current_status']]['name']; ?>
                                    </span>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-2 text-end" style="display: none;">
                                    <div class="filter-option">
                                        <i class="ti-layout-grid2 b-radius-5"></i>
                                    </div>
                                    <div class="filter-option-open text-start">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Filter Options</h5>
                                                <div class="card-header-right">
                                                    <i class="ti-close close-filter"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label class="mb-1" for="status">Status</label>
                                                    <select aria-label="Select option" class="form-control border" style="-webkit-appearence:auto;">
                                                        <option>Select Option</option>
                                                        <option>Approved</option>
                                                        <option>Pending</option>
                                                        <option>In Process</option>
                                                        <option>Rejected</option>
                                                    </select>
                                                </div>
                                                <span class="mb-1">Member Type:</span>
                                                <div class="border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input class="border-checkbox" type="checkbox" id="author">
                                                        <label class="form-label border-checkbox-label" for="author">Author</label>
                                                    </div>
                                                    <div class="border-checkbox-group border-checkbox-group-success">
                                                        <input class="border-checkbox" type="checkbox" id="customer">
                                                        <label class="form-label border-checkbox-label" for="customer">Customer</label>
                                                    </div>
                                                </div>
                                                <span class="d-block mb-1 mt-2">Notification</span>
                                                <input aria-label="Checkbox" type="checkbox" class="js-single" checked />
                                                <div class="text-end">
                                                    <button class="btn btn-link waves-effect waves-light">Reset</button>
                                                    <button class="btn btn-primary waves-effect waves-light">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="text-center">
                                <h6 class="text-center mt-3"> No Data</h6>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 160px!important;">
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>What's on the road?</h5>
                        </div>
                        <div class="card-body whats-on">
                            <ul class="nav">
                                <?php $loopdate = array(); $ldata = date('Y-m-d');
                                for($i=1; $i<11; $i++){
                                    $loopdate[] = $ldata;
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if($i==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#date<?php echo $ldata; ?>" role="tab" aria-selected="false"><?php echo date('D d',strtotime($ldata)); ?></a>
                                </li>
                                <?php
                                $ldata =  date('Y-m-d',strtotime(date("Y-m-d").", + ".$i." day"));
                                } ?>
                            </ul>
                            <div class="tab-content whats-on mt-3">
                                <?php $k=1; foreach ((array)$loopdate as $lpdate){ ?>
                                <div class="tab-pane custom-scroll <?php if($k==1){ echo "active";
} ?>" id="date<?php echo $lpdate; ?>" style="height: 215px;" role="tabpanel">
                                    <?php  if(count((array)$datewisetask[$lpdate])>0){
                                    foreach((array)$datewisetask[$lpdate] as $lptask){ ?>
                                    <div>
                                        <div class="row align-items-center mb-3 m-l-5">
                                            <div class="col-lg-9">
                                                <p class="f-14 f-w-600 mb-0"><?php echo $lptask['task_name']; ?></p>
                                                <span class="text-muted f-14">Lead by</span>
                                                <a class="text-primary" href=""><?php echo $emps[$lptask['assigen_id']]['display_name']; ?></a>

                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <span  class="form-label label label-success f-16 m-l-10" style="font-size:10px; background-color: <?php echo $_SESSION['status'][$lptask['current_status']]['background_color']; ?>;">
                                                    <?php echo  $tsd[$lptask['current_status']]['name']; ?>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                    <?php } ?>
                                    <?php }else{ ?>
                                    <div class="text-center">
                                        <h6 class="text-center mt-3"> No Data</h6>
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 160px!important;">
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php $k++;
} ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Latest Files</h5>
                        </div>
                        <div class="card-body my-tasks custom-scroll" style="height:300px">
                            <?php if(count((array)$data['project_files'])>0){
                                foreach ((array)$data['project_files'] as $file) { ?>
                            <div class="row align-items-center mb-3 m-l-10">
                                <div class="col-lg-1">
                                    <img src="/assets/images/pdf.png" class="img-30" alt="">
                                </div>
                                <div class="col-lg-9">
                                    <p class="f-14 f-w-600 mb-0"><?php echo $file['name']; ?></p>
                                    <span class="text-muted f-14">Uploaded by</span>
                                    <a class="text-primary" href="">><?php echo $emps[$file['created_by']]['display_name']; ?></a>
                                </div>
                                <div class="col-lg-2 text-end">
                                    <div class="filter-option">
                                        <i class="ti-layout-grid2 b-radius-5"></i>
                                    </div>
                                    <div class="filter-option-open text-start">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Filter Options</h5>
                                                <div class="card-header-right">
                                                    <i class="ti-close close-filter"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label class="mb-1" for="status">Status</label>
                                                    <select aria-label="Select option" class="form-control border" style="-webkit-appearence:auto;">
                                                        <option>Select Option</option>
                                                        <option>Approved</option>
                                                        <option>Pending</option>
                                                        <option>In Process</option>
                                                        <option>Rejected</option>
                                                    </select>
                                                </div>
                                                <span class="mb-1">Member Type:</span>
                                                <div class="border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input class="border-checkbox" type="checkbox" id="author_2">
                                                        <label class="form-label border-checkbox-label" for="author_2">Author</label>
                                                    </div>
                                                    <div class="border-checkbox-group border-checkbox-group-success">
                                                        <input class="border-checkbox" type="checkbox" id="customer_2">
                                                        <label class="form-label border-checkbox-label" for="customer_2">Customer</label>
                                                    </div>
                                                </div>
                                                <span class="d-block mb-1 mt-2">Notification</span>
                                                <input aria-label="Checkbox" type="checkbox" class="js-single" checked />
                                                <div class="text-end">
                                                    <button class="btn btn-link waves-effect waves-light">Reset</button>
                                                    <button class="btn btn-primary waves-effect waves-light">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <?php }else{ ?>
                                <div class="text-center">
                                    <h6 class="text-center mt-3"> No Data</h6>
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==2){ echo "active";
} ?>" id="project-type" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Type</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3" ></div>
                        <div class="col-lg-6" >

                            <?php $typeval =  $project['project_type']!="" ? $project['project_type'] :$data['project_types']['0']['id']; ?>
                            <?php $m=1; foreach($data['project_types'] as $project_type){ ?>
                            <input type="hidden" name="project_type" id="project_type" value="<?php echo $typeval; ?>">
                            <div class="personal-pro mt-5 close counttype <?php if($typeval==$project_type['id']){ echo "bg-pop-active";
}else{ echo "bg-pop-white";
} ?> " id="changeprojecttype_<?php echo $project_type['id']; ?>" onclick="changeprojecttype(<?php echo $project_type['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-2">
                                        <div class="d-inline-block">
                                            <?php if($project_type['image']!=""){ ?>
                                            <img src="/assets/images/<?php echo $project_type['image']; ?>" class="img-radius img-40" alt="">
                                            <?php }else{ ?>
                                                <span class="user-name" ><?php echo initials($project_type['type_name']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-11 col-md-11 col-sm-11 col-10">
                                        <div class="d-inline-block">
                                            <h5 class="mb-2 f-w-600 m-0"><?php echo $project_type['type_name']; ?></h5>
                                            <p class="m-0"><?php echo $project_type['description']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $m++;
} ?>
                            <button type="button" class="btn btn-primary mt-5 float-right" onclick="saveprojecttype(<?php echo $project_id; ?>)"> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==3){ echo "active";
} ?>" id="project-team" role="tabpanel">
            <?php
                $rolemana = 0;
                foreach((array)$data['project_teams'] as $project_team){
                    if($project_team['role_id']==1){
                        $rolemana =1; $manager_id = $project_team['user_id'];
                        break;
                    }
                }
            ?>
            <div class="card">
                <div class="card-header">
                    <h5>Users (<?php echo count((array)$data['project_teams']); ?>)</h5>
                    <div class="card-header-right" >
                        <button type="button" onclick="memberdisplay()" class="btn btn-primary waves-effect waves-light member-btn bth" ><i class="ti-plus m-r-10 text-white"></i>Team Member</button>
                        <div class="popup-member design-sec" style="right: 0; display: none;">
                            <div class="text-left mb-1" style="text-align: left;">Members
                            <button type="" class="float-end member-close design-close" onclick="closebtn()"><i class="ti-close"></i></button>
                            </div>
                            <div class="form-group w-full" style="text-align: left;">
                                <select aria-label="New Emp" class="form-control js-example-basic-single" id="new_emp_id">
                                    <option value="0"> Select Member</option>
                                    <?php foreach($emps as $emmp){ ?>
                                        <option <?php if(in_array($emmp['id'],(array)$team_member)){ echo "disabled";
} ?> value="<?php echo $emmp['id']; ?>"> <?php echo $emmp['display_name']; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group w-full mt-2" style="text-align: left;">
                                <select aria-label="New Role" class="form-control js-example-basic-single " id="new_role_id">
                                    <option value="0"> Select Role</option>
                                    <?php foreach((array)$data['roles'] as $roll){
                                        $disrole[$roll['id']] = $roll;
                                    ?>
                                        <option <?php if($rolemana == 1 && $roll['id']==1){ echo "disabled";
} ?>  value="<?php echo $roll['id']; ?>"> <?php echo $roll['role_name']; ?> </option>
                                    <?php } ?>
                                </select>

                            </div>
                             <div class="form-group w-full mt-2 row" style="text-align: left;">
                                <div class="col-lg-8"><input aria-label="Budget" type="text" class="form-control text-right" id="budget" name="budget"></div>
                                <div class="col-lg-4">Budget Rate Per Hours  </div>

                            </div>
                            <div class="form-group w-full mt-2 row" style="text-align: left;">
                                <div class="col-lg-8"><input aria-label="Cost" type="text" class="form-control text-right" id="cost" name="cost"></div>
                                <div class="col-lg-4"> Rate Per Hours  </div>

                            </div>
                            <button type="button" class="btn btn-primary float-right mt-2" onclick="addprojectmembers(<?php echo $project_id.",".$_SESSION['company_id']; ?>)"> Save</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 m-l-5 m-r-5">
                    <?php
                        if(count((array)$data['project_teams'] )>0){
                     foreach((array)$data['project_teams'] as $team){
                        $percntagemngemp =0; $total_empt = 0;$total_emptc = 0;
                        $total_emptc = $statusemp_tasks[$team['user_id']]['completed'] !="" ? $statusemp_tasks[$team['user_id']]['completed'] : 0;
                        $total_empt = $statusemp_tasks[$team['user_id']]['total'];
                         $lable = "success";
                         if($team['role_id']==1){
                            $lable = "warning";
                        }
                        $percntagemngemp = 100;
                        if($total_empt!=0){
                        $percntagemngemp = ($total_emptc/$total_empt)*100;
                        }
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <div class="card sale-card ">
                                <div class="card-header">
                                    <h5><?php echo $emps[$team['user_id']]['display_name']; ?></h5>
                                    <div class="card-header-right">

                                        <span class="label label-<?php echo $lable; ?>"><?php echo $disrole[$team['role_id']]['role_name']; ?></span>

                                        <i class="ti-trash text-danger cursor-pointer" onclick="removeteamember(<?php echo $team['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    </div>
                                    <div class="text-center pt-0">
                                        <div class="mb-2 mt-4 img-center" style="">
                                            <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation($percntagemngemp*3.6).'deg';?>; --p1_color: #2ed8b6;--p2_color: #dc3545;">
                                                <?php if($emps[$team['user_id']]['image']!=""){ ?>
                                                    <img src="uploads/all/<?php echo $emps[$team['user_id']]['image']; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
                                                <?php }else{ ?>
                                                    <span class="text-dark f-16 f-w-600 d-block" style="background-color: #ded1d1 !important;margin-top: 0px ;">
                                                        <?php echo initials($emps[$team['user_id']]['display_name']); ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <b class="d-block f-w-600 text-muted f-14"><?php echo $emps[$team['emp_id']]['email_id']; ?></b>
                                        <div class="row text-start mt-4">
                                            <div class="col-lg-4 col-md-12 col-sm-4 col-4 mb-2 p-1">
                                                <div class="s-cont bdr-dashed p-2">
                                                    <h6 class="f-w-700 m-b-0 text-primary f-24">
                                                        <?php echo $statusemp_tasks[$team['user_id']]['yet']!=""?$statusemp_tasks[$team['user_id']]['yet']: 0; ?>
                                                    </h6>
                                                    <p class="m-b-0 f-11 f-w-600">Yet to Submit</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-4 col-4 mb-2 p-1">
                                                <div class="s-cont bdr-dashed p-2">
                                                    <h6 class="f-w-700 m-b-0 text-info f-24">
                                                        <?php echo $statusemp_tasks[$team['user_id']]['open']!=""?$statusemp_tasks[$team['user_id']]['open']: 0; ?>
                                                    </h6>
                                                    <p class="m-b-0 f-11 f-w-600">Inprogress</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-4 col-4 mb-2 p-1">
                                                <div class="s-cont bdr-dashed p-2">
                                                    <h6 class="f-w-700 m-b-0 text-success f-24">
                                                        <?php echo $statusemp_tasks[$team['user_id']]['completed']!=""?$statusemp_tasks[$team['user_id']]['completed']: 0; ?>
                                                    </h6>
                                                    <p class="m-b-0 f-11 f-w-600">Completed</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==4){ echo "active";
} ?>" id="files" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Files (<?php echo count((array)$data['project_files']); ?>)</h5>
                </div>
                <div class="card-body row mt-3 col-lg-12">
                    <div class="col-lg-6">
                        <?php  fileUplodingUi(['application_table_name'=>'projects','lable'=>'File Upload', 'transaction_id'=>$project_id ,'is_active'=>'0', 'width'=>'12', 'deletewidth'=>'0', 'deleteview'=>'', 'file_size'=>'597152', 'file_limit'=>'5','transaction_number'=>'']); ?>

                        <?php if(count((array)$data['project_files'])>0){
                            foreach ((array)$data['project_files'] as $file) { ?>
                                <div class="col-lg-3">
                                    <div class="card sale-card ">
                                        <div class="card-block text-center pt-4 pb-4">
                                            <img src="../files/assets/images/pdf.png" class="img-50" alt="">
                                            <h5 class="f-w-600 mt-3 mb-2"><?php echo $file['name']; ?></h5>
                                            <b class="d-block f-w-600 text-muted f-14"><?php echo notificationdatesdiff($file['created_date'],'N') ?> days ago Uploaded by <?php echo $emps[$file['created_by']]['display_name']; ?></b>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-5">
                            <h6>Task Files</h6>
                        </div>
                        <div>
                            <?php $m=1;
                                if(count((array)$data['project_phases'])>0){
                                    foreach((array)$data['project_phases'] as $project_phase){ ?>
                                        <div class="p-2 mt-1 cursor-pointer" style="background-color: #977373;color:#FFFFFF;" onclick="showphase(<?php  echo $project_phase['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            <div class="float-left">
                                                <h6><?php echo $project_phase['project_phase_name'];  ?> </h6>
                                            </div>
                                            <div class="text-right">
                                                <span class="label label-md label-primary" style="background-color: #FFFFFF;color:#977373;"><?php echo count((array)$emp_phaase_tasks[$project_phase['id']]); ?>  </span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="" value="0" id="phasedis_<?php echo $project_phase['id']; ?>">
                                        <div  class="p-10" style="background-color: #FFFFFF; display: none;" id="filephase_<?php echo $project_phase['id']; ?>">
                                                <?php foreach($emp_phaase_tasks[$project_phase['id']] as $phtas){ ?>
                                                    <div class="p-1 mt-1 cursor-pointer " style="background-color: #f1eaea;" onclick="showptaskwise(<?php echo $phtas; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                        <?php echo $list_tasks[$phtas]['task_name']. $phtas; ?> &nbsp; &nbsp; &nbsp;
                                                        <span class="label label-md label-info" style=""><?php echo count((array)$task_attachment[$phtas]); ?>  </span>
                                                    </div>
                                                    <input type="hidden" name="" value="0" id="filetaskdis_<?php echo $phtas; ?>">
                                                    <?php if(count((array)$task_attachment[$phtas])>0){ ?>
                                                        <div class="row col-lg-12 p-2 cursor-pointer" style="display: none;" id="filetask_<?php echo $phtas; ?>" >
                                                        <?php foreach((array)$task_attachment[$phtas] as $att){ ?>
                                                                <div class="col-lg-2" id="rem_17">
                                                                    <a href="uploads/attachments/<?php echo $att['actual_file_name']; ?> " target="_blank"><i class="ti-file f-30 text-info"></i></a>
                                                                     <div> <?php echo $att['file_name']; ?> </div>
                                                                </div>
                                                        <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                        </div>
                            <?php }
                                    } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==5){ echo "active";
} ?>" id="activity" role="tabpanel">
            <div class="row col-lg-12">
                <div class="col-lg-6">
                    <div class="card latest-update-card">
                        <div class="card-header">
                            <h5><i class="ti-calendar m-r-10 text-primary"></i>Project Task Activity</h5>
                        </div>
                        <div class="card-block mt-3 color-accordion-block">
                            <div class="color-accordion" id="color-accordion">
                                <?php if(count((array)$data['project_task_details'])>0){
                                foreach((array)$data['project_task_details'] as $actask){ ?>

                                <a class="accordion-msg b-none waves-effect waves-light mt-1" style="background-color: #d0d4dd5c; color: #000000;">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 p-1"><?php echo $actask['task_name']; ?> </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-6 text-end mob-text-end p-1">
                                            <span>Due &nbsp; <?php echo fromDate($actask['planned_finish']); ?></span>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-6 text-end p-1">
                                            <span class="">
                                                <span  class="form-label label label-success f-14 m-l-10" style="font-size:10px; background-color: <?php echo $_SESSION['status'][$actask['current_status']]['background_color']; ?>;">
                                                     <?php echo  $tsd[$actask['current_status']]['name']; ?>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <div class="accordion-desc p-0">
                                    <div class="accordion-desc p-0">
                                        <div class="latest-update-box">
                                            <?php foreach($data['project_task_activities'][$actask['id']] as $activ){ ?>
                                            <div class="row p-b-30">
                                                <div class="col-auto text-end update-meta p-r-0">
                                                    <?php if($emps[$activ['created_by']]['image']!=""){ ?>
                                                        <img src="/uploads/all/<?php echo $emps[$activ['created_by']]['image']; ?>" alt="User" class="img-radius img-40 align-top m-r-15 update-icon">
                                                    <?php }else{?>
                                                        <span class="float-end user-name m-l-5" style="background-color: #ded1d1 !important;">
                                                            <?php echo initials($emps[$activ['created_by']]['display_name']); ?>
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col p-l-5">
                                                    <a href="#!"><h6><?php echo $activ['comments']; ?></h6></a>
                                                    <p class="text-muted m-b-0"><?php echo $emps[$activ['created_by']]['display_name'] ?></p>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==6){ echo "active";
} ?>" id="project-setting" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Settings</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="" enctype="multipart/form-data" >
                        <div class="m-l-50 m-r-50 m-l-m-r">
                            <div class="row form-group">
                                <div class="col-lg-3 col-md-3">
                                    <label for="project-logo">Project Logo</label>
                                </div>
                                <?php $project_logo = $project['project_logo'] !="" ? $project['project_logo']: "no-image.jpg";  ?>
                                <div class="col-lg-6 col-md-6">
                                    <div class="profile-pic">
                                        <label class="-label" for="display_logo">
                                            <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                        </label>

                                        <input type="file" name="project_logo" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)" data-sizeinkb="15" data-heightinpx="150" data-widthinpx="150">
                                        <img src="uploads/all/<?php echo $project_logo; ?>" id="outputdisplay_logo" width="200" alt="Logo">
                                        <input type="hidden" name="hid_image" value="<?php echo $project['project_logo']; ?>">
                                    </div>
                                    <span class="text-danger f-12" id="display_logo_error"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-4 col-lg-3 col-md-3">
                                    <span class="f-w-600 mb-2">Project Work Space</span>
                                    <select aria-label="Project Work Space" class="form-control js-example-basic-single" name="project_work_space_id">
                                        <option value="0">Select Any One</option>
                                        <?php foreach($data['work_spaces'] as $wrk){ ?>
                                        <option <?php if($wrk['id'] == $project['project_work_space_id']){ echo "selected";
} ?> value="<?php echo $wrk['id'] ?>"><?php echo $wrk['name']; ?></option>
                                      <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group mt-4 col-lg-3 col-md-3">
                                    <span class="f-w-600 mb-2">Project Name</span>
                                    <input aria-label="Project Name" class="form-control" type="text" name="project_name" placeholder="Project Name" value="<?php echo $project['project_name']; ?>" >
                                </div>
                                <div class="form-group mt-4 col-lg-6 col-md-6">
                                    <span class="f-w-600 mb-2">Project Description</span>
                                    <textarea aria-label="Description" class="form-control" name="description"><?php echo $project['description']; ?></textarea>
                                </div>
                                <div class="form-group mt-4 w-full col-lg-6 col-md-6">
                                    <span class="f-w-600 mb-2 d-block">Customer</span>
                                    <select aria-label="Customer" class="form-control js-example-basic-single" name="customer_id">
                                        <option value="0">Select Any One</option>
                                        <?php foreach($data['customers'] as $cum){ ?>
                                        <option <?php if($cum['id'] == $project['customer_id']){ echo "selected";
} ?> value="<?php echo $cum['id'] ?>"><?php echo $cum['display_name']; ?></option>
                                      <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group mt-4 w-full col-lg-6 col-md-6">
                                    <span class="f-w-600 mb-2">Project Manager
                                        <span class="f-11 text-muted"> ( Assign to project teams.)</span>
                                     </span>
                                    <select aria-label="Assigner" class="form-control js-example-basic-single"  name="assigner_id">
                                      <option value="0">Select Any One</option>
                                      <?php foreach($data['employees'] as $emp){
                                            $jrole = json_decode($emp['role_id'],1);
                                            if(in_array('TASKDA_PROJECT_MANAGER',(array)$jrole)){
                                      ?>
                                          <option <?php if($emp['id'] == $project['assigner_id']){ echo "selected";
} ?> value="<?php echo $emp['id'] ?>"><?php echo ucwords($emp['display_name']); ?></option>
                                      <?php } ?>
                                      <?php } ?>
                                    </select>
                                </div>
                                <input type="hidden" name="old_assinger_id" id="old_assinger_id" value="<?php echo $project['assigner_id']; ?>">
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Planed Start</span>
                                        <input aria-label="Planned Start" class="form-control datepicker"  name="planned_start"  type="text" placeholder="" value="<?php echo fromDate($project['planned_start']); ?>" id="planned_start" >
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Planed Finish</span>
                                        <input aria-label="Input field" class="form-control datepicker"  value="<?php echo fromDate($project['planned_finish']); ?>" id="planned_finish" name="planned_finish" type="text" placeholder="" >
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Release Date</span>
                                        <input aria-label="Input field" class="form-control datepicker" value="<?php echo fromDate($project['release_date']); ?>" id="release_date" type="text" placeholder="" name="release_date">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Number Of Resources</span>
                                        <input aria-label="Input field" class="form-control text-right" value="<?php echo $project['actual_resources']; ?>"  type="text" placeholder="" name="actual_resources">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Project Budget</span>
                                        <input aria-label="Input field" class="form-control text-right" value="<?php echo $project['project_cost']; ?>"  type="text" placeholder="" name="project_budget">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group ">
                                        <span class="f-w-600 mb-2">Project Cost</span>
                                        <input aria-label="Input field" class="form-control text-right" value="<?php echo $project['project_cost']; ?>"  type="text" placeholder="" name="project_cost">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group w-full">
                                        <span class="f-w-600 mb-2">Risk</span>
                                        <select aria-label="Priority" class="form-control js-example-basic-single" name="priority">
                                            <option value="0">Select Any One</option>
                                            <?php foreach($data['priorits'] as $pry){ ?>
                                            <option <?php if($pry['genc_detail_code'] == $project['priority']){ echo "selected";
} ?> value="<?php echo $pry['genc_detail_code']; ?>"><?php echo ucwords($pry['genc_detail_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group w-full">
                                        <span class="f-w-600 mb-2">Task Assign Each Other</span>
                                        <select aria-label="Assign Each Other" class="form-control js-example-basic-single" name=" assign_each_other">

                                            <option <?php if($project['assign_each_other'] == 0){ echo "selected";
} ?> value="0">Yes</option>
                                            <option <?php if($project['assign_each_other'] == 1){ echo "selected";
} ?> value="1">No</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row float-right">
                                <div class="col-lg-12">
                                    <input type="hidden" name="hid" value="<?php echo $project_id; ?>">
                                    <input type="hidden" name="submit" value="save">
                                    <button  class="btn btn-primary"> Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==7){ echo "active";
} ?>" id="project-label" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Labels</h5>
                </div>
                <div class="card-body">
                    <?php foreach ((array)$data['labels'][$project_id] as $key => $value) {
                        $checkprojlab[] = $value['parent_id'];
                        $checklabname[$value['work_id']][$value['parent_id']] = $value['label_name'];
                    }
                    ?>
                    <?php foreach((array)$data['labels']['0'] as $lab){
                           $viewlab = $project_id.",".$lab['id'].",'".$lab['label_name']."','".$lab['label_code']."'";
                    ?>
                        <div class="row mt-2 align-items-center">
                            <div class="col-lg-1 col-md-1 col-sm-1 col-2">
                                <div class="checkbox-zoom zoom-primary mt-1">
                                    <label class="form-label">
                                        <input aria-label="Checkbox" type="checkbox"  <?php if(in_array($lab['id'], (array)$checkprojlab)){ ?> checked <?php
} ?>  onclick="checkandsave(<?php echo $viewlab; ?>)" name="" value= "" onkeydown="">
                                        <span class="cr">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-5">
                               <div class="t-color" style="background-color:<?php echo $lab['label_code']; ?>";>
                                <div class="text-white m-l-5 f-16"><?php echo ucwords($lab['label_name']); ?></div>
                               </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-5">
                                <input aria-label="Input field" type="text" class="form-control" onblur="updatelabelval(<?php echo $project_id.",".$lab['id']; ?>,this.value)" name="" value="<?php echo
                                 ucwords($checklabname[$project_id][$lab['id']]); ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==8){ echo "active";
} ?>" id="project-task-status" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Task Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-3"> Status Name </div>
                        <div class="col-lg-3"> Display Position</div>
                        <div class="col-lg-1"></div>
                    </div>
                    <?php $colorarray = array('199'=>'#2fbb47','0'=>'#131560','5'=>'#ced928','30'=>'#94a3a2','101'=>'#2650af');
                        foreach ($data['task_status'] as $key => $sta) {  ?>
                        <div class="row mt-2 align-items-center" id="statusdiv_<?php echo $sta['id']; ?>">
                            <div class="col-lg-1 col-md-3 col-sm-3 col-4 mt-2">
                               <div class="t-color" style="width: 80px;background-color:<?php echo $sta['background_color']; ?>;color:<?php echo $sta['color']; ?> ";>
                                    <div class="text-white m-l-5 f-12"><?php echo  ucwords($sta['action_label']); ?> </div>
                               </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-4 mt-2">
                                <input aria-label="Input field" type="text" class="form-control" onblur="updatetasksttusval(<?php echo $sta['id']; ?>,'name',this.value)" name="" value="<?php echo  ucwords($sta['name']); ?>">
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-4 w-full mt-2">
                                <select aria-label="Select option" class="js-example-basic-single" onchange="updatetasksttusval(<?php echo $sta['id']; ?>,'display_priority',this.value)">
                                    <option value=""></option>
                                    <?php for($m=0; $m<12; $m=$m+1){ ?>
                                        <option <?php if($m==$sta['display_priority']){ echo "selected";
} ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
                                    <?php } ?>
                                </select>

                            </div>
                            <?php if(!in_array($sta['status'], (array)$primarystatus)){ ?>
                            <div class="col-lg-1 ">
                                <i class="ti-trash text-danger  cursor-pointer" onclick="updatetasksttusval(<?php echo $sta['id']; ?>,'is_active','1')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                            </div>
                            <?php }  ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="tab-pane <?php if($listactive ==8){ echo "active";
} ?>" id="project-phases" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Project Phases</h5>
                </div>
                <div class="card-body">
                    <div class="col-lg-12 row">
                        <div class="col-lg-3" ></div>
                        <div class="col-lg-6" >
                            <div class="row col-lg-12" id="AddItem">
                                <?php $m=1; if(count((array)$data['project_phases'])>0){
                                    foreach((array)$data['project_phases'] as $project_phase){ ?>
                                        <div class="col-lg-12 row" id="ptphdiv_<?php echo $project_phase['id']; ?>">
                                            <div class="col-lg-8 mt-3 itemRow">
                                                <input aria-label="Phase[]" type="text" name="phase[]" id="phase_<?php echo $m; ?>" value="<?php echo $project_phase['project_phase_name'];  ?>"  onclick="updatephasettusval(<?php echo $project_phase['id'] ?>,'project_phase_name',this.value)" class="form-control" onkeydown="">
                                            </div>
                                            <div class="col-lg-1 mt-4">
                                               <i class="ti-trash text-danger  cursor-pointer" onclick="updatephasettusval(<?php echo $project_phase['id']; ?>,'is_active','1')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                            </div>
                                        </div>
                                <?php $m++;
}
                                }?>
                            </div>
                            <div class="row col-lg-12">
                                <div class="col-lg-8">
                                    <button type="button" class="btn btn-warning mt-3" onclick="addphases(<?php echo $project_id; ?>)"> ADD</button>
                                </div>
                                <div class="col-lg-8 float-end">
                                    <button type="button" class="btn btn-primary mt-3 float-right" onclick="saveprojectphases(<?php echo $project_id; ?>)"> Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3" ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
