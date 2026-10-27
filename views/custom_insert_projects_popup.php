<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$toplist = array('Project', 'Team', 'Task');
$url = API."?mode=togetprojectinsertdata&project_id=";
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$list_project = array('0'=>'Work Space','1'=>'Project Type', '2'=>'Projeject Settings', '3'=>'Project Team');
?>
<style type="text/css">
.project .modal-lg{max-width: 1450px;}
ul.pro-menu{margin: 0; padding: 0; text-align: center;}
ul.pro-menu li{ display: inline-block; }
ul.pro-menu li a{font-size: 16px; font-weight: 600; margin: 0 20px; padding: 16px 0;}
ul.pro-menu li.active a{border-bottom: 3px solid #4099ff;}
.project h3{font-size: 21px; font-weight: 600;}
.project p{font-size: 15px; color: #a1a5b7; font-weight: 600;}
.project p a{color: #00b2ff; font-weight: 600; font-size: 16px;}
.personal-pro{ background: #f1faff; border: 1px dashed #00b2ff; padding: 20px 20px; border-radius: 10px;}
.personal-pro i{color: #00b2ff; font-size: 33px; transform: rotate(-45deg);}
.probtn{ border-radius: 10px; font-size: 16px; padding: 10px 25px; font-weight: 600;}

.usage{ border: 1px dashed #00b2ff; padding: 20px 20px; border-radius: 10px; cursor: pointer;}
.usage p{font-size: 12px; cursor: pointer;}
.usage h5{cursor: pointer;}
.bg-pop-white { background-color: #FFFFFF; }
.bg-pop-active { background-color: #f1faff; }
.teamdrop { width: 80px; background-color: #f9f9f9 !important; padding: 5px !important; border: #f9f9f9;border-radius: 5px;font-size: 12px; }
</style>
<script type="text/javascript">
  function dynamicinsertprojectpopup(){
        console.log("Data");
        $('#kt_modal_dynamic_popup2').modal("hide");
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
  function addtoemployees(datax){
    var x = datax.split("#");
    var htmlRows = '';
    htmlRows += '<tr><td><div class="d-inline-block align-middle"><img src="/assets/images/avatar-blank.jpg" alt="user" class="img-radius img-40 align-top m-r-15"><div class="d-inline-block"><h6 class="mb-0">'+x[1]+'</h6><p class="text-muted m-b-0">'+x[2]+'</p> </div> </div></td>';
    htmlRows += '<td  class="w-full"><input type="hidden" name="project_teams[]" value="'+x[0]+'" > <select aria-label="Teamrole []" class="teamdrop" name="teamrole_id[]" ><?php foreach($data['roles'] as $role){ if($role['id']!="1"){ ?> <option value="<?php echo $role['id']; ?>"> <?php echo $role['role_name']; ?> </option> <?php
}
} ?></select></td></tr>';
     $('#addemployess').append(htmlRows);
  }
  function addtasklist(){
    console.log("check");
    var htmlRows = '';
    htmlRows += '<div class="col-lg-12 row mb-2">';
    htmlRows += '<div class="col-lg-3"><input aria-label="Task Name[]" type="text" class="form-control" name="task_name[]"></div>';
    htmlRows += '<div class="col-lg-2"><select aria-label="Task Priority[]" class="js-example-basic-single" name="task_priority[]"><option>Select any one</option><?php foreach($data['priorits'] as $pry){ ?><option value="<?php echo $pry['genc_detail_code'] ?>"><?php echo ucwords($pry['genc_detail_name']); ?></option><?php
} ?> </select></div>';
    htmlRows += '<div class="col-lg-2 w-full"><select aria-label="Task Assigner[]" class="js-example-basic-single" name="task_assigner[]"><option>Select</option> <?php foreach($data['employees'] as $emp){ ?><option value="<?php echo $emp['id'] ?>"><?php echo $emp['id']; ?></option><?php
} ?> </select></div>';
    htmlRows += '<div class="col-lg-5"><textarea aria-label="Tesk Descrption[]" class="form-control" name="tesk_descrption[]"></textarea> </div></div>';
    console.log(htmlRows);
    $('#invoiceItem').append(htmlRows);
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
  }
  function submitnext(){
    var formvalue =  $("#submitnextform").serialize();
    console.log(formvalue);

    var project_name = $('#project_name').val();
    if(project_name !=""){
          jQuery.ajax({
            url: "ajax.php",
            data: "page=submitsprojectinsert&"+formvalue,
            type: "POST",
            success:function(data){
              console.log(data);

              window.location = "index.php?option=COM_VIEW_PROJECT_KANBAN&RID="+data;
              tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Project Created Successfully','');
            },
            error:function (){}
          });
    }else{
      tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Please Fill Project Name','');
    }
  }
  function checknext(val){
        console.log(val);
      for(var i=0; i < 4; i++){
        if(val == i){
           $("#checknext"+i).show();
           $("#link"+i).addClass("active");
        }else{
          $("#checknext"+i).hide();
          $("#link"+i).removeClass("active");
        }
      }
  }
  function changeprojecttype(val){
    console.log(val);
    var oldval = $("#project_type").val();
    $("#changeprojecttype"+oldval).removeClass("bg-pop-active");
    $("#changeprojecttype"+oldval).addClass("bg-pop-white");
    $("#changeprojecttype"+val).removeClass("bg-pop-white");
    $("#changeprojecttype"+val).addClass("bg-pop-active");
    $("#project_type").val(val);


  }
</script>

<div class="modal-header">
  <h5 class="modal-title"> Create Project</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form--label-right" action="" id="submitnextform" method="post">
  <div class="modal-body" >
    <div class="pcoded-content">
      <div class="pcoded-inner-content">
        <div class="main-body">
          <div class="page-wrapper">
            <div class="page-body">
              <div class="">
                <div class="">
                  <ul class="pro-menu">
                    <?php foreach((array)$list_project as $topkey=>$topproject){ ?>
                        <li id="link<?php echo $topkey; ?>" class="<?php if($topkey == 0){ echo "active";
} ?>"><a href="#"><?php echo $topproject ?></a></li>
                    <?php } ?>
                  </ul>
                  <div class="row mt-5" id="checknext0" >
                    <div class="col-lg-5 mx-auto" >
                      <h3>Project Work Space</h3>
                      <div class="form-group mt-4 w-full">
                        <span class="f-w-600 mb-2">Work Space</span></br>
                        <select aria-label="Work Space" class="form-control js-example-basic-single" name="work_space_id">
                          <option value="">Select Any One</option>
                          <?php foreach((array)$data['project_work_spaces'] as $cum){ ?>
                            <option value="<?php echo $cum['id'] ?>"><?php echo $cum['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="text-center">
                        <span class="f-w-600 mb-2 f-16"> OR </span></br>
                      </div>
                      <div class="form-group mt-4 w-full">
                        <span class="f-w-600 mb-2">New Work Space</span></br>
                        <input aria-label="Work Space" class="form-control " type="work_space" placeholder="" name="work_space">
                      </div>
                      <button class="btn btn-primary btn-round waves-effect waves-light float-end mt-4 btn-md probtn" type="button" onclick="checknext(1)" ><?php echo $list_project['1']; ?></button>
                    </div>
                  </div>

                  <div class="row mt-5" id="checknext1" style="display: none;" >
                    <div class="col-lg-5 mx-auto" >
                      <input type="hidden" name="project_type" id="project_type" value="0">
                      <h3>Project Type</h3>
                      <p>If you need more info, please check out <a href="">FAQ Page</a></p>
                      <div class="personal-pro mt-5 close bg-pop-active " id="changeprojecttype0" onclick="changeprojecttype(0)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <div class="d-inline-block align-top">
                          <img src="/assets/images/feature.jpg" class="img-radius img-40" alt="">
                        </div>
                        <div class="d-inline-block align-top m-l-10">
                          <h5 class="mb-2 f-w-600">Features</h5>
                          <p class="m-0">Visualize and advance your project forward </br>using issues on a powerful board.</p>
                        </div>
                      </div>
                       <div class="personal-pro mt-4 close bg-pop-white " id="changeprojecttype1"  onclick="changeprojecttype(1)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <div class="d-inline-block align-top">
                          <img src="/assets/images/support.png" class="img-radius img-40" alt="">
                        </div>
                        <div class="d-inline-block align-top m-l-10">
                          <h5 class="mb-2 f-w-600">Support / Operations</h5>
                          <p class="m-0">Sprint toward your project goals with a board,</br> backlog, and roadmap.</p>
                        </div>
                      </div>
                      <div class="personal-pro mt-4 close bg-pop-white " id="changeprojecttype2"  onclick="changeprojecttype(2)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <div class="d-inline-block align-top">
                          <img src="/assets/images/bug.jpg" class="img-radius img-40" alt="">
                        </div>
                        <div class="d-inline-block align-top m-l-10">
                          <h5 class="mb-2 f-w-600">Bug tracking</h5>
                          <p class="m-0">Manage a list of development tasks and bugs.</p>
                        </div>
                      </div>
                       <button class="btn bg-gray btn-round waves-effect waves-light probtn mt-4" type="button" onclick="checknext(0)"><?php echo $list_project['0']; ?></button>
                      <button class="btn btn-primary btn-round waves-effect waves-light float-end mt-4 btn-md probtn" type="button" onclick="checknext(2)" ><?php echo $list_project['2']; ?></button>
                    </div>
                  </div>

                   <div class="row mt-5" id="checknext2" style="display: none;">
                    <div class="col-lg-5 mx-auto">
                      <h3>Project Settings</h3>
                      <p>If you need more info, please check <a href="">Project Guidelines</a></p>

                      <div class="form-group mt-4">
                        <span class="f-w-600 mb-2">Project Name</span>
                        <input aria-label="Project Name" class="form-control" type="text" name="project_name" id="project_name" placeholder="Project Name" >
                      </div>
                      <div class="form-group mt-4">
                        <span class="f-w-600 mb-2">Project Description</span>
                        <textarea aria-label="Project Description" class="form-control" name=" description" placeholder="Project Description" ></textarea>
                      </div>
                      <div class="form-group mt-4 w-full">
                        <span class="f-w-600 mb-2">Customer</span></br>
                        <select aria-label="Customer" class="form-control js-example-basic-single" name="customer_id">
                          <option>Select Any One</option>
                          <?php foreach((array)$data['customers'] as $cum){ ?>
                          <option value="<?php echo $cum['id'] ?>"><?php echo $cum['display_name']; ?></option>
                          <?php } ?>
                        </select>
                        <span class="text-danger f-12" style="display: none;">Customer is required</span>
                      </div>
                      <div class="form-group mt-4 w-full">
                        <span class="f-w-600 mb-2">Project Owner</span>
                        <select aria-label="Assigner" class="form-control js-example-basic-single" name="assigner_id">
                          <option value="">Select Any One</option>
                          <?php foreach((array)$data['employees'] as $emp){ ?>
                          <option value="<?php echo $emp['id'] ?>"><?php echo $emp['display_name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group ">
                            <span class="f-w-600 mb-2">Planed Start</span>
                            <input aria-label="Planned Start" class="form-control datepicker"  name="planned_start"  type="text" placeholder="" id="planned_start" value="<?php echo fromDate(); ?>" >
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group ">
                            <span class="f-w-600 mb-2">Planed Finish</span>
                            <input aria-label="Planned Finish" class="form-control datepicker" id="planned_finish" name="planned_finish" type="text" placeholder="" value="<?php echo fromDate( date('Y-m-d',strtotime(date("Y-m-d") . " + 31 days"))); ?>" >
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group ">
                            <span class="f-w-600 mb-2">Release Date</span>
                            <input aria-label="Release Date" class="form-control datepicker" id="release_date" type="text" placeholder="" name="release_date">
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group ">
                            <span class="f-w-600 mb-2">Number Of Resources</span>
                            <input aria-label="Actual Resources" class="form-control text-right" type="text" placeholder="" name="actual_resources">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group ">
                            <span class="f-w-600 mb-2">Project Buget</span>
                            <input aria-label="Project Cost" class="form-control text-right" type="text" placeholder="" name="project_cost">
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group w-full">
                            <span class="f-w-600 mb-2">Risk</span>
                            <select aria-label="Priority" class="form-control js-example-basic-single" name="priority">
                              <option>Select Any One</option>
                              <?php foreach((array)$data['priorits'] as $pry){ ?>
                              <option value="<?php echo $pry['genc_detail_code'] ?>"><?php echo ucwords($pry['genc_detail_name']); ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <button class="btn bg-gray btn-round waves-effect waves-light probtn mt-4" type="button" onclick="checknext(1)"><?php echo $list_project['1']; ?></button>
                      <button class="btn btn-primary btn-round waves-effect waves-light float-end mt-4 btn-md probtn" type="button" onclick="checknext(3)"><?php echo $list_project['3']; ?></button>
                    </div>
                  </div>
                 <div class="row mt-5" id="checknext3" style="display: none;" >
                    <div class="col-lg-5 mx-auto">
                      <h3>Build a Team</h3>
                      <p>If you need more info, please check <a href="">Project Guidelines</a></p>
                      <div class="form-group w-full">
                        <span class="mb-2">Invite Teammates</span>

                        <select aria-label="Select option" class="form-control js-example-basic-single" onchange="addtoemployees(this.value)">
                          <option value="0">Select Any One</option>
                          <?php foreach((array)$data['employees'] as $cum){ ?>
                          <option value="<?php echo $cum['id']."#".$cum['display_name']."#".$cum['email_id'] ?>"><?php echo $cum['display_name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <h6 class="f-w-600">Team Members</h6>
                      <div class="table-responsive mt-3">
                        <table class="table table-hover table-sm" id="addemployess"><thead><tr class="visually-hidden"><th scope="col">Data</th></tr></thead>

                        </table>
                      </div>

                      <button class="btn bg-gray btn-round waves-effect waves-light probtn mt-4" type="button" onclick="checknext(2)"><?php echo $list_project['2']; ?></button>
                      <button class="btn btn-primary btn-round waves-effect waves-light float-end mt-4 btn-md probtn" type="button" onclick="submitnext()">Submit</button>
                    </div>
                  </div>
<script type="text/javascript" src="assets/js/dropzone.js"></script>
  <script type="text/javascript" src="assets/js/custom_file_uploading.js"></script>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
