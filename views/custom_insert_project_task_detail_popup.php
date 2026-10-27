<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$task_id = $_POST['task_id'];
$url = API."?mode=togetprojecttaskdetailspopup&task_id=".$task_id;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$task = $data['taskdetils'];
$project_teams = $data['project_teams'];
$emps = $data['emps'];
$labels = $data['labels'];
$atchlabel = $data['attach_labels'];
$follower_ids  = explode(",",$task['follower_ids']);
$currenttimelog = $data['project_task_time_log']['task_start'];
$projec_tasks = $data['projec_tasks'];
$projec_phases = $data['phases'];
foreach((array)$data['task_team'] as $task_team){
    $ptrm[$task_team['project_role_id']][] = $task_team['user_id'];
}
$montharray =$months = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July ','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
$weekdays = ['Sun'=>'Sunday','Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday'];
?>
<style type="text/css">
  .phasecss{
        background-color: #6b74e1;
        color: #FFF;
        border-radius: 5px;
        font-size: 12px;
        padding: 1px 3px;
    }
     .exdate{
        background-color: #db2929;
        color: #FFF;
        border-radius: 5px;
    }
</style>
<script>
  let startTime, timerInterval, pausedTime;

  function startTimer() {
    const timerDisplay = document.getElementById('timer');
    const startButton = document.getElementById('startButton');
    const pauseButton = document.getElementById('pauseButton');
    const stopButton = document.getElementById('stopButton');
    var oldtime = $("#oldtime").val();
    var currenttimelog = $("#currenttimelog").val();
    console.log('timerDisplay:'+timerDisplay+'&startButton:'+startButton+'&pauseButton'+pauseButton+'&stopButton='+stopButton);
    console.log('now'+Date.now());
    if(oldtime !=0 && !startTime){
      if(currenttimelog!="" ){
        var noldtime = Date.now() - currenttimelog;
        oldtime = parseInt(oldtime) + parseInt(noldtime);
        console.log('currenttimelog'+oldtime);
      }
      startTime =  Date.now() - oldtime;
      console.log('restart'+startTime);

      pausedTime = Date.now();
      console.log('now pase'+pausedTime);
    }
    if (!startTime) {
        // Start the timer
        console.log('startTime');
        startTime = Date.now();
        $('#btntime').hide();
        $('#btnpas').show();
        sessionstartandinsertimelog('start',startTime,'');
      timerInterval = setInterval(updateTimer, 1000);
    } else if (pausedTime) {
      console.log('pay Time:'+Date.now());
      console.log('pausedTime:'+pausedTime);
        // Resume the timer after pause
        startTime += Date.now() - pausedTime;
        pausedTime = null;
       timerInterval = setInterval(updateTimer, 1000);
       $('#btntime').hide();
       $('#btnpas').show();
       sessionstartandinsertimelog('start',Date.now(),'');
     }

     function updateTimer() {
        const currentTime = Date.now();
        const elapsedMilliseconds = currentTime - startTime;

        const hours = Math.floor(elapsedMilliseconds / 3600000);
        const minutes = Math.floor((elapsedMilliseconds % 3600000) / 60000);
        const seconds = Math.floor((elapsedMilliseconds % 60000) / 1000);

        const formattedTime = `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`;
        timerDisplay.textContent = formattedTime;
      }

      function padZero(number) {
         return number.toString().padStart(2, '0');
       }
     }

     function pauseTimer() {
      console.log('padZero paused');

      if (startTime && !pausedTime) {
        clearInterval(timerInterval);
        pausedTime = Date.now();
        console.log(pausedTime);
        $('#btnpas').hide();
        $('#btntime').show();
        sessionstartandinsertimelog('pause',startTime,pausedTime);
      }
    }

    function stopTimer() {
      console.log('padZero');
      clearInterval(timerInterval);
      startTime = null;
      pausedTime = null;
      document.getElementById('timer').textContent = '00:00:00';
      document.getElementById('startButton').textContent = 'Start11';
      document.getElementById('pauseButton').textContent = 'Pause';
    }

    document.getElementById('startButton').addEventListener('click', startTimer);
    document.getElementById('pauseButton').addEventListener('click', pauseTimer);
    document.getElementById('stopButton').addEventListener('click', stopTimer);

    function sessionstartandinsertimelog(type,starttime,endtime){
      var task_id = $('#task_id').val();
      jQuery.ajax({
        url: "ajax.php",
        data:'page=toinsertupdateprojecttasktimlog&task_id='+task_id+'&type='+type+'&starttime='+starttime+'&endtime='+endtime,
        type: "POST",
        success:function(data){
          console.log(data);
  },
  error:function (){}
});
    }
  </script>
  <script>
    $(document).ready(function() {
      $("#imageUploadForm").submit(function(e) {
        e.preventDefault();

        let formData = new FormData();
        formData.append('image', $('#imageInput')[0].files[0]);
        console.log(formData);
        $.ajax({
            url: 'ajaxfileupload.php', // The PHP script to handle the uploaded file
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
              console.log(response);
              var response1 = $.parseJSON(response);
              console.log(response1.actual_file_name);
              console.log(response1['actual_file_name']);
              var update_id = $('#task_id').val();
              jQuery.ajax({
                url: "ajax.php",
                data:'page=setupcomponents&table_name=application_attachments&application_table_name=project_task_details&transaction_id='+update_id+'&actual_file_name='+response1.actual_file_name+'&file_name='+response1.file_name+'&file_size_mb='+response1.file_size_mb,
                type: "POST",
                success:function(data){
                  console.log(data);
                  var otr = $.parseJSON(data);
                  $('#imagePreview').append('<div class="col-lg-2" id="rem_'+otr.LASTID+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><a href="uploads/attachments/'+response1.actual_file_name+'" target="_blank"><i class="ti-file f-30 text-info"></i></a><div> '+response1.file_name+' </div><div class="text-danger cursor-pointer" onclick="removeattacchment('+otr.LASTID+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> Remove </div>');
                },
                error:function (){}
              });
            },
            error: function(xhr, status, error) {
              console.log(xhr.responseText);
            }
          });
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      $("#fileInput").change(function() {
        if (this.files && this.files[0]) {
          var formData = new FormData();
          formData.append('file', this.files[0]);

          $.ajax({
                url: 'ftest.php', // The PHP script to handle the uploaded file
                type: 'POST',
                data: formData+'&page',
                processData: false,
                contentType: false,
                success: function(response) {
                    $("#fileInfo").text(response); // Display the server response
                  },
                  error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                  }
                });
        }
      });
    });
  </script>
  <script type="text/javascript">
    function addactivity(){
      var task_id = $('#task_id').val();
        console.log("check task"+task_id);
        jQuery.ajax({
            url: "views/custom_insert_project_sub_task_popup.php",
            data:'option=noption&task_id='+task_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);

                $('#kt_modal_dynamic_popup').modal("show");

            },
            error:function (){}
        });

    }
   function removeattacchment(val){
    console.log(val);
    jQuery.ajax({
      url: "ajax.php",
      data:'page=remove_file&file_id='+val,
      type: "POST",
      success:function(data){
            $("#rem_"+val).hide();
          },
          error:function (){}
        });
  }
  function adddeliverable(){
    var task_id = $('#task_id').val();
     var project_id = $('#project_id').val();
     var fielda ="'deliverable'";
     var fieldb ="'deliverable_date'";
     var fieldc ="'deliverable_owner'";
    jQuery.ajax({
      url: "ajax.php",
      data:'page=setupcomponents&table_name=project_task_deliverables&project_id='+project_id+'&project_task_id='+task_id,
      type: "POST",
      success:function(data){
        console.log(data);
         $("#project_task_deliverables").show();
         data = JSON.parse(data);
         var str ="";
         str= '<div class="col-lg-6 mt-2"><input aria-label="Input field" type="text" class="form-control" onblur="updatedeliverabledetail(this.value,'+data.LASTID+','+fielda+')" > </div>';
         str+='<div class="col-lg-3 mt-2"><input aria-label="Input field" type="text" name="" class=" form-control datepicker" value="" onchange="updatedeliverabledetail(this.value,'+data.LASTID+','+fieldb+')" > </div>';
         str+='<div class="col-lg-3 mt-2 w-full"><select aria-label="Select option" class="form-control js-example-basic-single " onchange="updatedeliverabledetail(this.value,'+data.LASTID+','+fieldc+')"> <option> Select Any One</option> <?php foreach((array)$project_teams as $team){ ?><option  value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?></option>  <?php
} ?> </select> </div>';
          console.log(str);
         $('#append_deliverable').append(str);
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
      },
      error:function (){}
    });
  }

    function updatedeliverabledetail(val,update_id,field){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name=project_task_deliverables&field='+field+'&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
             tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Task Deliverable Upadate Successfully','');
          },
          error:function (){}
        });
    }
   function movecard(){
      var val = $('#movestatus').val();
      var oldval = $('#oldstatus').val();
      var update_id = $('#task_id').val();
      if(val != oldval){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatestatusdynamicprojecttask&table_name=project_task_details&field=current_status&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Task Status Upadate Successfully','');
          },
          error:function (){}
        });
      }
    }
  function copynewcard(){
      var formvalue =  $("#copyfrom").serialize();
      console.log(formvalue);
        jQuery.ajax({
          url: "ajax.php",
          data:'page=copynewcard&'+formvalue,
          type: "POST",
          success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Task Status Upadate Successfully','');
          },
          error:function (){}
        });
    }
     function updatecheckitem(val,update_id, field){
        jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name=application_check_lists&field='+field+'&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
            if(field=='is_active'){
                $("#chlisttt_"+update_id).hide();
            }
          },
          error:function (){}
        });
    }

     function submitactivty(){
      var checkd = $('#addcheckdata').val();
      var task_id = $('#task_id').val();
    }
    function submitactivty(){
      var activty = $('#activty').val();
      var task_id = $('#task_id').val();
      var initial = $('#initial').val();
      var fullname = $('#fullname').val();
      jQuery.ajax({
          url: "ajax.php",
          data: "page=insertprojecttaskdetails&table_name=project_task_activities&application_table_name=project_task_details&activity_type=activity&transaction_id="+task_id+"&comments="+activty,
          type: "POST",
          success:function(data){
            console.log(data);
            $('#activty').val('');
            var str ='<div class=" mt-2"> <span class="user-name  float-left m-r-5">'+initial+'</span><span class="f-14 f-w-600">'+fullname+'</span>'+activty+'</br><span class="text-muted f-12 m-l-50"> 1 min ago </span> </div> <div style="clear:both;"></div>';
            console.log(str);
            $('#someactydetail').prepend(str);

          },
          error:function (){}
        });
    }
     function viewdescrption(){
      $("#hidedescription").hide();
      $("#description").show();
    }

    function checkuncheckcolor(val,code){
      var task_id = $('#task_id').val();
      jQuery.ajax({
          url: "ajax.php",
          data: "page=checkandupdatecolorprojecttask&table_name=application_attach_labels&application_tablename=project_task_details&transaction_id="+task_id+"&application_label_id="+val,
          type: "POST",
          success:function(data){
             if(data==1){
              console.log("yes");
              var str ="";
              str= '<div class="llt-color" id="label_'+val+'" style="background-color: '+code+';"></div>';
               console.log(str);
              $('#labelids').append(str);
            }else{
              console.log("no");
               $('#label_'+val).remove();
            }
          },
          error:function (){}
        });
    }

     function addchecklists(val,parent_id){
      var task_id = $('#task_id').val();
      if(parent_id =="0"){
        val = $('#checkdata').val();
      }
      jQuery.ajax({
          url: "ajax.php",
          data: "page=insertprojecttaskdetails&table_name=application_check_lists&application_tablename=project_task_details&transaction_id="+task_id+"&parent_id="+parent_id+"&check_name="+val,
          type: "POST",
          success:function(data){
            console.log(data);
            data = JSON.parse(data);
            if(parent_id =="0"){
              var issefield ="'is_active'";
            var str='<div class="mt-4" id="chlisttt_'+data.LASTID+'"><h4 class=""><i class="ti-check-box m-r-10"></i> '+val+' <button class="btn border waves-effect waves-light float-end" type="button" onclick="updatecheckitem(1,'+data.LASTID+','+issefield+')" >Delete</button></h4><div class="m-l-40"><div class="progress mt-4 mb-2"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div> </div><div id="addcheckitem_'+data.LASTID+'"> </div><button class="btn btn-primary waves-effect waves-light mt-3" onclick="addchecklists(0,'+data.LASTID+')">Add an Item</button></div></div>';
            console.log(str);
            $('#addcheckitem').append(str);
          }else{
            var infield ="'check_name'";
            var sefield ="'assign_id'";
            var str = '<div class="mt-2"><div class="col-lg-12 row"><div class="col-lg-4"><input aria-label="Checkbox"  type="checkbox"><label for="check1" class="m-l-10" ><input aria-label="Input field" type="text" class="form-control" onblur="updatecheckitem(this.value,'+data.LASTID+','+infield+')"></label></div><div class="col-lg-4 w-full"><select aria-label="Select option" class="form-select js-example-basic-single" onchange="updatecheckitem(this.value,'+data.LASTID+','+sefield+')">  <option value="">Select Assigner</option> <?php foreach($project_teams as $team){ ?><option value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?></option><?php
} ?> </select>  </div> </div></div>';
            console.log(str);
            $('#addcheckitem_'+parent_id).append(str);
          }
          },
          error:function (){}
        });
    }

    function watchtheallow(){
    var wat = $('#allowwatch').val();
    var task_id = $('#task_id').val();
    var passwat = 1;
    if(wat==0){
       $("#wat").hide();
    }
    if(wat==1){
      passwat = 0;
       $("#wat").show();
    }
    $('#allowwatch').val(passwat);
    updatedetail('allow_watch',task_id,passwat);
  }
  function updatefollowersdetail(field, update_id,val){
    console.log('field:'+field+"-update_id:"+update_id+"-val:"+val);
    var select_button_text = $('#follower_ids option:selected')
                .toArray().map(item => item.value);
    console.log('follower_ids:'+select_button_text);
    updatedetail(field,update_id,select_button_text);
  }
  function addupdaterolememberdetail(field, update_id,val,role_id){
    console.log('field:'+field+"-update_id:"+update_id+"-val:"+val);
    var select_button_text = $('#'+field+' option:selected')
                .toArray().map(item => item.value);
    console.log('#'+field+':'+select_button_text);
    var task_id = $('#task_id').val();
    var project_id = $('#project_id').val();
    jQuery.ajax({
          url: "ajax.php",
          data: "page=addupdaterolememberdetail&task_id="+task_id+"&project_id="+project_id+"&role_id="+role_id+"&member_ids="+select_button_text,
          type: "POST",
          success:function(data){
             console.log(data);
          },
          error:function (){}
        });
  }
  function updateprojectdependencydetail(){
    var task_id = $('#task_id').val();
    var project_id = $('#project_id').val();
    var select_button_text = $('#project_dependency option:selected').toArray().map(item => item.value);
    console.log('dependency_ids:'+select_button_text);
        jQuery.ajax({
          url: "ajax.php",
          data: "page=updateprojectdependencydetail&task_id="+task_id+"&project_id="+project_id+"&dependency_ids="+select_button_text,
          type: "POST",
          success:function(data){
             console.log(data);
          },
          error:function (){}
        });
  }

   function updatedetail(field, update_id,val){
    console.log(val);
    if(val!=""){
     jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name=project_task_details&field='+field+'&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
            if(field=='description'){
              $("#hidedescription").show();
              $("#description").hide();
              $("#hidedescription").text(val);
            }
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Task Upadate Successfully','');
            if(field == 'assigen_id'){
            }
          },
          error:function (){}
      });
    }else{
      $("#hidedescription").show();
      $("#description").hide();
    }
  }
  function closeopenpopup(str,type){
    console.log("str:"+str+":Type"+type)
    var closid = $("#closeopenpopup").val();
    if(type == 'open'){
      if(closid!=str && closid!=""){
        $("."+closid).hide('slow');
      }
      $("#closeopenpopup").val(str);
      $("."+str).show('slow');
    }
    if(type == 'close'){
      $("."+str).hide('slow');
    }
  }
   $(document).ready( function () {
    var currenttimelog = $("#currenttimelog").val();
    if(currenttimelog!=""){
    startTimer();
    }
  });
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
    function scehduleaction(){
     var selectscehduleaction = $("#selectscehduleaction").val();
     $("#monthlyschedule").hide();
     $("#weeklyschedule").hide();
     $("#yearlyschedule").hide();
     if(selectscehduleaction == "M"){
      $("#monthlyschedule").show('slow');
    }else if(selectscehduleaction == "W"){
      $("#weeklyschedule").show('slow');
    }else if(selectscehduleaction == "Y" || selectscehduleaction == "Q"){
      $("#yearlyschedule").show('slow');
    }
  }
  function editcomment(val){

    $("#comment_"+val).hide();
     $("#textarea_comment_"+val).show('slow');

  }
  function updatedynamicfiledtable(val,update_id, field, table){
      jQuery.ajax({
        url: "ajax.php",
        data:'page=updatedynamicfiedrojecttaskdetail&table_name='+table+'&field='+field+'&fielddata='+val+'&update_id='+update_id,
        type: "POST",
        success:function(data){
          console.log(data);
          $("#textarea_comment_"+update_id).hide('slow');
          $("#comment_"+update_id).show('slow');
           $('#comment_data_'+update_id).html(val);
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Task Comments Upadate Successfully','');
        },
        error:function (){}
      });
  }

</script>
<div class="modal-header">
  <h5 class="modal-title"> Task Details</h5>
  <button type="button" class="btn-close btn_white" onclick="window.location.reload();"  aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="modal-body task-popup">
    <div class="row">
      <div class="col-lg-9">
        <input type="hidden" id="task_id" name="" value="<?php echo $task_id; ?>">
        <input type="hidden" id="project_id" name="" value="<?php echo $task['project_id']; ?>">
        <i class="ti-credit-card m-r-20 f-20"></i>
        <input aria-label="Input field" class="task_1" type="text" placeholder="<?php echo $task['task_name']; ?>" name="task_name" value="<?php echo $task['task_name']; ?>" onblur="updatedetail('task_name', <?php echo $task_id; ?> ,this.value)" >
      </div>
      <div class="col-lg-3">
        <button class="btn waves-effect waves-light border" onclick="watchtheallow()">
          <input type="hidden" name="allowwatch" id="allowwatch" value="<?php echo $task['allow_watch']; ?>">
          <i class="ti-eye m-r-5"></i> Notify Comments <span id="wat" <?php if($task['allow_watch']!=0){ ?> style="display: none;" <?php
} ?> > <i class="ti-check-box m-l-5"></i></span>
        </button>
      </div>
    </div>

      <?php if($task['project_phase_id']!='' && $projec_phases[$task['project_phase_id']]['project_phase_name']!=""){ ?>
        <div class="m-l-50">
          <span class="mb-2 mt-2 p-2 phasecss"><?php echo $projec_phases[$task['project_phase_id']]['project_phase_name']; ?></span>
        </div>
       <?php } ?>
    <div class="row mt-2">
      <div class="col-lg-9">
          <div style="clear: both;"></div>
            <div class="row col-lg-12 mb-4">
              <?php foreach((array)$data['roles'] as $role){?>
              <div class="col-lg-3 col-md-3 col-sm-6 col-12 mt-4">
                <div><?php echo $role['role_name']; ?></div>
                <div class=" multi-user">
                  <?php  if(count((array)$ptrm[$role['id']])>0 ){
                foreach ((array)$ptrm[$role['id']] as  $follower_id) { ?>
                  <?php if($project_teams[$follower_id]['image']!=""){ ?>
                     <a href=""><img  src="/uploads/all/<?php echo $project_teams[$follower_id]['image']; ?>" class="img-radius img-40" alt=""></a>
                  <?php }else{ ?>
                     <a href=""><span class=""><?php echo initials($project_teams[$follower_id]['display_name']); ?></span></a>
                  <?php } ?>
                <?php } ?>
                <?php } ?>
                </div>

              </div>
              <?php } ?>
          </div>
           <!----------------- Lables --------------------------->
          <?php if(count((array)$atchlabel)>0){ ?>
           <div class="mt-4">
            <h6 class=""> <i class="ti-bookmark m-r-20"></i> Labels </h6>
            <div id="labelids">
              <?php foreach((array)$atchlabel as $ala){ ?>
                <div class="llt-color" id="label_<?php echo $ala;  ?>" style="background-color: <?php echo $labels[$ala]['label_code']; ?>;"></div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
          <!----------------- DESCRPTION --------------------------->
          <div class="mt-4">
            <h6 class=""><i class="ti-align-left m-r-20"></i> Description</h6>
            <div class="m-l-40 mt-3 bg-gray" id="hidedescription" onclick="viewdescrption()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
              <?php echo $task['description']!="" ? $task['description'] : "Add a more detailed description…"; ?>
            </div>
            <div class="m-l-40 mt-4" id="description" style="display:none;">
              <textarea aria-label="Text area" class="w-100 mb-2 text-start toptextarea" onblur="updatedetail('description', <?php echo $task_id; ?> ,this.value)" placeholder=""><?php echo $task['description']; ?></textarea>
            </div>
          </div>
          <!----------------- COMMENTS --------------------------->
          <div class="mt-4">
            <h6 class=""><i class="ti-check-box m-r-10"></i> Comments
              <button class="btn border waves-effect waves-light float-end " onclick="closeopenpopup('showactiveites','open')">Show Details</button>
            </h6>
            <div class="m-l-40 mt-4">
              <div>
                <textarea aria-label="Activty" class="w-100 mb-2 text-start toptextarea" name="activty" id="activty" placeholder=""></textarea>
                <input type="hidden" name="initial" id="initial" value="<?php echo initials($_SESSION['user']['display_name']);?>">
                <input type="hidden" name="fullname" id="fullname" value="<?php echo $_SESSION['user']['display_name'];?>">
              </div>
              <div class="float-right">
                <button class="btn btn-primary" onclick="submitactivty()"> Save</button>
              </div>
              <div id="someacty" class="mt-3">
                <h7> Comment History :</h7>
                <div id="someactydetail" style="word-wrap: break-word;">
                  <?php foreach((array)$data['activty'] as $act){
                    if($act['activity_type']=="activity"){ ?>
                      <div class=" mt-2" id="comment_<?php echo $act['id']; ?>">
                        <span class="user-name float-left m-r-5"><?php echo initials($emps[$act['created_by']]['display_name']); ?></span>
                        <span class="f-14 f-w-600" > <?php echo $emps[$act['created_by']]['display_name']; ?> </span>
                        <span id="comment_data_<?php echo $act['id']; ?>"><?php echo $act['comments']; ?></span> <span onclick="editcomment(<?php echo $act['id']; ?>>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-edit"></i></span>
                        </br>
                        <span class="text-muted f-12 m-l-50"> <?php echo  notificationdatesdiff($act['created_date']);  ?></span>
                      </div>
                      <div class=" mt-2" id="textarea_comment_<?php echo $act['id']; ?>" style="display: none;">
                        <textarea aria-label="Text area" class="form-control" onblur="updatedynamicfiledtable(this.value, <?php echo $act['id']; ?>, 'comments', 'project_task_activities')" ><?php echo $act['comments']; ?></textarea>
                      </div>
                      <div style="clear:both;"></div>
                    <?php }
                  } ?>
                </div>
              </div>
              <div id="showactiveites" class="mt-2 showactiveites" style="display:none;word-wrap: break-word;">
                <h7> Task Activities :</h7>
                <?php foreach((array)$data['activty'] as $act){
                  if($act['activity_type']=="task_details"){
                    ?>
                    <div class=" mt-2">
                      <span class="user-name float-left m-r-5"><?php echo initials($emps[$act['created_by']]['display_name']); ?></span>
                      <span class="f-14 f-w-600"> <?php echo $emps[$act['created_by']]['display_name']; ?> </span> <?php echo $act['comments']; ?>
                    </br><span class="text-muted f-12 m-l-50"> <?php echo  notificationdatesdiff($act['created_date']); ?></span>
                  </div>
                  <div style="clear:both;"></div>
                <?php }
              } ?>
            </div>
            </div>
        </div>
        <!----------------- Sub Task --------------------------->
            <?php if(count((array)$data['sub_tasks'])>0){ ?>
              <div class="mt-4">
                 <h6 class=""><i class="ti-harddrive m-r-20"></i> Sub Tasks </h6>
              <div class="row col-lg-12 mt-3">
                 <?php foreach((array)$data['sub_tasks'] as $subtask){ ?>
                    <div class="col-lg-1">
                       <?php if($project_teams[$subtask['assigen_id']]['image']!=""){ ?>
                    <img class=" user-name p-0 m-0" src="/uploads/all/<?php echo $project_teams[$subtask['assigen_id']]['image']; ?>" alt="User Profile">
                  <?php }else{ ?>
                    <span class="user-name"><?php echo initials($project_teams[$subtask['assigen_id']]['display_name']); ?></span>
                  <?php } ?>
                    </div>
                    <div class="col-lg-6 cursor-pointer mt-2" onclick="taskdetailspopup(<?php echo $subtask['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo substr($subtask['task_name'],0,90)."..."; ?></div>
                    <div class="col-lg-2 " >
                        <?php if($subtask['planned_finish']!=""){ ?>
                          <span class=" p-2  d-inline-block f-12 m-r-5 <?php if(strtotime($subtask['planned_finish'])< strtotime(date('Y-m-d'))){ echo "exdate";
} ?> ">
                              <i class="ti-time f-10"></i> &nbsp; <?php echo date('d M Y', strtotime($subtask['planned_finish'])); ?>
                          </span>
                          <?php } ?>
                    </div>
                    <div class="col-lg-2 ">
                      <span class="phasecss p-2" style="background-color: <?php echo $data['project_status'][$subtask['current_status']]['background_color'] ?>; color:<?php echo $data['project_status'][$subtask['current_status']]['color'] ?> ;">
                      <?php echo $data['project_status'][$subtask['current_status']]['name'] ?> </span></div>
              <?php } ?>
                </div>
              </div>
              <?php } ?>


           <div class="mt-4 " id="project_task_deliverables"
            <?php if(count( (array)$data['append_deliverables'])<1){ ?> style="display: none;" <?php
} ?>>
                 <h6 class=""><i class="ti-file m-r-20"></i> Deliverables </h6>
                 <div class="row col-lg-12 mt-3" id="append_deliverable">
            <?php if(count((array)$data['append_deliverables'])>0){ ?>

                 <?php foreach((array)$data['append_deliverables'] as $append_deliverable){
                  if($append_deliverable['deliverable']!=""){ ?>

                   <div class="col-lg-6 mt-2"><input aria-label="Input field" type="text" class="form-control" value="<?php echo $append_deliverable['deliverable']; ?>" onblur="updatedeliverabledetail(this.value,<?php echo $append_deliverable['id']; ?>,'deliverable')">  </div>
                   <div class="col-lg-3 mt-2"><input aria-label="Input field" type="text" name="" class=" form-control datepicker" value="" onchange="updatedeliverabledetail(this.value,<?php echo $append_deliverable['id']; ?>,'deliverable_date')" value="<?php echo displayDate($append_deliverable['deliverable_date']) ?>"> </div>
                   <div class="col-lg-3 mt-2 w-full"><select aria-label="Select option" class="form-control js-example-basic-single " onchange="updatedeliverabledetail(this.value,<?php echo $append_deliverable['id']; ?>,'deliverable_owner')"> <option> Select Any One</option> <?php foreach((array)$project_teams as $team){ ?><option <?php if($append_deliverable['deliverable_owner'] ==$team['id']){ echo "selected";
} ?> value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?></option>  <?php
} ?> </select> </div>
            <?php }
          }  ?>

            <?php } ?>
            </div>
          </div>

          <!----------------- Attachments--------------------------->
          <?php if(count((array)$data['attachments'])>0){ ?>
           <div id="attachmentid"  class="mt-4">
            <h6 class=""><i class="ti-clip m-r-20"></i> Attachments</h6>
            <div id="imagePreview" class="col-lg-12 row mt-3">
             <?php foreach((array)$data['attachments'] as $attach){?>
              <div class="col-lg-2" id="rem_<?php echo $attach['id']; ?>">
               <a href="uploads/attachments/<?php echo $attach['actual_file_name']; ?>" target="_blank"><i class="ti-file f-30 text-info"></i></a>
               <div> <?php echo $attach['file_name']; ?> </div>
               <div class="text-danger cursor-pointer" onclick="removeattacchment(<?php echo $attach['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> Remove </div>
             </div>
           <?php } ?>
         </div>
       </div>
     <?php } ?>


          <?php if(count((array)$data['checklist'])>0){ ?>
              <div id="addcheckitem" class="mt-4">
                <h6 class=""><i class="ti-list m-r-20"></i> Check List</h6>
              <?php foreach((array)$data['checklist'] as $mcheck){ ?>
            <div class="mt-3"  id="chlisttt_<?php  echo $mcheck['id']; ?>">
              <h4 class=""><i class="ti-check-box m-r-10"></i> <?php echo $mcheck['check_name']; ?>
              <button class="btn border waves-effect waves-light float-end" type="button" onclick="updatecheckitem(1,<?php echo $mcheck['id'] ?>,'is_active')">Delete</button>
            </h4>
            <div class="m-l-40">
              <div class="progress mt-4 mb-2">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div id="addcheckitem_<?php echo $mcheck['id']; ?>">
                <?php foreach((array)$data['subchecklist'][$mcheck['id']] as $subc){ ?>
                  <div class="mt-2">
                    <div class="col-lg-12 row">
                      <div class="col-lg-4">
                        <input aria-label="Checkbox"  type="checkbox" id=""> <label for="check1" class="m-l-5" >
                          <input aria-label="Input field" type="text" class="form-control" onblur="updatecheckitem(this.value,<?php echo $subc['id'] ?>,'check_name')" value="<?php echo $subc['check_name'] ?>">
                        </label>
                      </div>
                      <div class="col-lg-4 w-full">
                        <select aria-label="Select option" class="form-select js-example-basic-single" onchange="updatecheckitem(this.value,<?php echo $subc['id'] ?>,'assign_id')">
                          <option value="">Select Assigner</option>
                          <?php foreach((array)$project_teams as $team){ ?>
                            <option <?php if($subc['assign_id'] ==$team['id']){ echo "selected";
} ?> value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
              <button class="btn btn-primary waves-effect waves-light mt-3" onclick="addchecklists('',<?php echo $mcheck['id'] ?>)">Add an Item</button>
            </div>
          </div>
        <?php } ?>
        </div>

        <?php } ?>


</div>
      <div class="col-lg-3">
        <div class="mt-0 bg-gray">
          <div class="p-3">
              <div class="float-left m-r-2" id="btntime">
                <button id="startButton" class="btn-primary"><i class="ti-control-play"></i></button>
              </div>
              <div class="float-left m-r-2" id="btnpas" style="display:none;">
                <button id="pauseButton_2" class="btn-danger"><i class="ti-control-record"></i></button>
              </div>
              <span id="timer" class="m-l-5"><?php echo convertSecondsToHMS($task['work_time']); ?></span>

              <input type="hidden" name="oldtime" id="oldtime" value="<?php echo $task['work_time']*1000;  ?>">
              <input type="hidden" name="currenttimelog" id="currenttimelog" value="<?php echo $currenttimelog;  ?>">
          </div>

          <div class="p-3">
            <h5 class="f-14 f-w-600">Add to card</h5>
          </div>
          <div class="card-body pt-0">
            <input type="hidden" name="" id="closeopenpopup" value="">
            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start member-btn" onclick="closeopenpopup('popup-member','open')"><i class="ti-user m-r-5"></i> Members
              </button>
              <div class="popup-member design-sec">
                <h5>Members</h5>
                <button type="" class="float-end member-close design-close" onclick="closeopenpopup('popup-member','close')"><i class="ti-close"></i></button>
                <div class="form-group w-full">
                  <span>Accountable (Reassign) </span>
                  ?>
                  <select aria-label="Select option" class="form-control js-example-basic-single" <?php if(in_array('TASKDA_USER',$_SESSION['role_ids']) && $data['projects']['assign_each_other'] == 1 ){ echo "disabled";
} ?> onchange="updatedetail('assigen_id',<?php echo $task_id;?>,this.value)"  name="assigen_id" id="assigne">
                    <option value="0"> Select Assigner</option>
                    <?php foreach($project_teams as $team){ ?>
                      <option <?php if($team['id'] == $task['assigen_id'] ){ echo "selected";
} ?> value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?> </option>
                    <?php } ?>
                  </select>
                </div>
                <?php foreach($data['roles'] as $role){
                        if($role['id']!=1){
                 ?>
                  <div class="form-group w-full">
                    <span> <?php echo $role['role_name']; ?> </span>
                   <select aria-label="Work Members" class="form-control js-example-basic-multiple" multiple="multiple" name="work_members" id="<?php echo 'flow_'.$role['id']; ?>"  onchange="addupdaterolememberdetail(<?php echo "'flow_".$role['id']."'"; ?>,<?php echo $task_id;?>,this.value,<?php echo $role['id']; ?>)">
                    <option value="0"> Select <?php echo $role['role_name']; ?></option>
                    <?php foreach($project_teams as $team){ ?>
                      <option <?php if(in_array($team['id'],(array)$ptrm[$role['id']])){ echo "selected";
} ?> value="<?php echo $team['id']; ?>"> <?php echo $team['display_name']; ?> </option>
                    <?php } ?>
                  </select>
                </div>
                <?php } ?>
                <?php } ?>

            </div>
          </div>
          <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start label-btn"  onclick="closeopenpopup('popup-label','open')"><i class="ti-tag m-r-5"></i> Labels
              </button>
              <div class="popup-label design-sec">
                <h5>Labels</h5>
                <button type="" class="float-end label-close design-close" onclick="closeopenpopup('popup-label','close')"><i class="ti-close"></i></button>

                <?php if(count((array)$labels)>0){ foreach((array)$labels as $label){ ?>
                <div class="form-group mt-2 mb-0">
                  <input aria-label="Input field" onclick="checkuncheckcolor(this.value,'<?php echo $label['label_code']; ?>')" type="checkbox" name="checkcolor" <?php if(in_array($label['id'],(array)$atchlabel)){ echo "checked";
}?> id="check<?php echo $label['id']; ?>" value="<?php echo $label['id']; ?>" onkeydown="">
                  <label for="check1"  style="background-color:<?php echo $label['label_code']; ?>" class="label-check w-75"><?php echo $label['label_name']; ?></label>

                </div>
                <?php } ?>
                <?php } ?>
              </div>
            </div>
            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start checklist-btn" onclick="closeopenpopup('popup-checklist','open')"><i class="ti-check-box m-r-5" ></i> Checklist
              </button>
              <div class="popup-checklist design-sec">
                <h5>Checklist</h5>
                <button type="" class="float-end checklist-close design-close" onclick="closeopenpopup('popup-checklist','close')"><i class="ti-close"></i></button>
                <div class="form-group mt-3">
                  <span>Title</span>
                  <input aria-label="Checklist" class="form-control" type="text" name="checkdata" id="checkdata" placeholder="Checklist">
                </div>
                <button class="btn btn-primary waves-effect waves-light" onclick="addchecklists(0,0)">Add</button>
              </div>
            </div>

            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start date-btn" onclick="closeopenpopup('popup-date','open')"><i class="ti-alarm-clock m-r-5" ></i> Dates
              </button>
              <div class="popup-date design-sec">
                <h5>Dates</h5>
                <button type="" class="float-end date-close design-close" onclick="closeopenpopup('popup-date','close')"><i class="ti-close"></i></button>
                <div class="form-group mt-3">
                  <span>Start Date</span>
                  <input aria-label="Input field" class="form-control datepicker" onchange="updatedetail('planned_start',<?php echo $task_id;?>,this.value)" value="<?php echo fromDate($task['planned_start']); ?>" id="planned_start" type="text">
                </div>
                <div class="form-group mt-3">
                  <span>Due Date</span>
                  <input aria-label="Input field" class="form-control datepicker"onchange="updatedetail('planned_finish',<?php echo $task_id;?>,this.value)"  value="<?php echo fromDate($task['planned_finish']); ?>" id="planned_finish" type="text">
                </div>

              </div>
            </div>


            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start date-btn" onclick="closeopenpopup('popup-recurrence','open')">    <i class="fa fa-tasks m-r-5" ></i> Task Recurrence
              </button>
              <div class="popup-recurrence design-sec" style="display: none;">
                <h5>Recurrence</h5>
                <button type="" class="float-end date-close design-close" onclick="closeopenpopup('popup-recurrence','close')"><i class="ti-close"></i></button>
                <div class="form-group w-full mt-5">

                  <div class="row col-lg-12 mt-2 ">

                   <div class="col-lg-12 row">
                    <span class="form-label col-lg-6 f-w-600 mt-2">Repeat Every</span>
                    <div class="col-lg-6 form-group"><input aria-label="Action No" type="text" class="form-control col-lg-2" name="action_no" value="1" readonly/></div>
                  </div>
                  <div class="col-lg-12 row">
                    <span class="form-label col-lg-6 f-w-600 mt-2">Type</span>
                    <div class="col-lg-6 form-group">
                   <select aria-label="Action Frequency" name="action_frequency" class="form-control form-select js-example-basic-single" id="selectscehduleaction" onchange="scehduleaction()">
                     <?php foreach ((array)$data['task_frequency'] as $due) {    ?>
                      <option value="<?php echo $due['genc_detail_code'];?>" <?php if($compliancedetail['action_frequency'] == $due['genc_detail_code']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                    <?php } ?>

                  </select>
                </div>
                </div>
                <div class=" " id="monthlyschedule" style="<?php echo $compliancedetail['action_frequency']=="MN"?'':'display:none'; ?>">
                  <div class="col-lg-12 row">
                    <span class="form-label col-lg-6 f-w-600 mt-2">On Day</span>
                    <div class="col-lg-6 form-group"><input aria-label="On The Day" type="number" class="form-control col-lg-4" name="on_the_day" value="<?php echo $compliancedetail['on_the_day']; ?>" ></div>
                  </div>
                </div>
                <div class="form-group" id="yearlyschedule" style="<?php echo $compliancedetail['action_frequency']=="YR"?'':'display:none'; ?>">
                  <div class="col-lg-12 row ">
                   <span class="form-label col-lg-6 f-w-600 mt-2">On Month</span>
                   <div class="col-lg-6">
                    <select aria-label="On The Month" name="on_the_month"  style="width:150px;" class="form-control form-select js-example-basic-single">
                      <?php foreach ($montharray as $mont=>$montname) {    ?>
                       <option value="<?php echo $mont;?>" <?php if($mont == $compliancedetail['on_the_month']){ ?>selected <?php
} ?> ><?php echo $montname;?></option>
                     <?php } ?>
                   </select>
                 </div>
               </div>
                 <div class="col-lg-12 row mt-2">
                  <span class="form-label col-lg-6 f-w-600 mt-2">On Day</span>
                  <div class="col-lg-6">
                  <input aria-label="On The Day Year" type="text" class="form-control" name="on_the_day_year" value="<?php echo $compliancedetail['on_the_day_year'] !=""?$compliancedetail['on_the_day_year']:1; ?>"  placeholder="Ex : 1 to 31"/>
                </div>
                </div>

            </div>
            <div class="" id="weeklyschedule" style="<?php echo $compliancedetail['action_frequency']=="WK"?'':'display:none'; ?>">
              <div class="row col-lg-12 ">
               <span class="form-label col-lg-6 f-w-600 mt-2">On Week Day</span>
               <div class="col-lg-6 form-group">
                <select aria-label="On The Week" name="on_the_week"  style="width:150px;" class="form-control form-select js-example-basic-single">
                  <?php foreach ($weekdays as $weekkey=>$weekvalue) {    ?>
                   <option value="<?php echo $weekkey;?>" <?php if($weekkey == $compliancedetail['on_the_week']){ ?>selected <?php
} ?> ><?php echo $weekvalue;?></option>
                 <?php } ?>
               </select>
             </div>
           </div>
         </div>
         <div class="col-lg-12 row">
          <span class="form-label col-lg-6 f-w-600 mt-2">Planned Day</span>
          <div class="col-lg-6 form-group"><input aria-label="Planned Day" type="text" class="form-control col-lg-2" name="planned_day" value="<?php echo $compliancedetail['planned_day'] !=""?$compliancedetail['planned_day']:'0'; ?>"/></div>
        </div>
      </div>

                </div>
             </div>
           </div>

            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start date-btn" onclick="closeopenpopup('popup-act','open')"><i class="ti-files m-r-5" ></i> Sub Tasks
              </button>
              <div class="popup-act design-sec" style="display: none;">
                <h5>Sub Tasks</h5>
                <button type="" class="float-end date-close design-close" onclick="closeopenpopup('popup-act','close')"><i class="ti-close"></i></button>
                <div class="form-group w-full mt-5">
                  <button type="" class="btn btn-primary text-center" onclick="addactivity()"><i class="ti-plus"></i> Add Sub Tasks</button>
                </div>
             </div>
           </div>

           <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start date-btn" onclick="closeopenpopup('popup-deliverable','open')"><i class="ti-share-alt m-r-5" ></i> Deliverable
              </button>
              <div class="popup-deliverable design-sec" style="display: none;">
                <h5>Deliverable</h5>
                <button type="" class="float-end date-close design-close" onclick="closeopenpopup('popup-deliverable','close')"><i class="ti-close"></i></button>
                <div class="form-group w-full mt-5">
                  <button type="" class="btn btn-primary text-center" onclick="adddeliverable()"><i class="ti-plus"></i> Add Deliverable</button>
                </div>
             </div>
           </div>

            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start member-btn" onclick="closeopenpopup('popup-dependency','open')"><i class="ti-layers m-r-5"></i> Task Predecessor
              </button>
              <div class="popup-dependency design-sec " style="display: none;">
                <h5> Task Predecessor </h5>
                <button type="" class="float-end member-close design-close" onclick="closeopenpopup('popup-dependency','close')"><i class="ti-close"></i></button>

                <div class="form-group w-full">
                 <span> Tasks </span>
                 <select aria-label="Project Dependency" class="form-control js-example-basic-multiple" multiple="multiple" name="project_dependency" id="project_dependency"  onchange="updateprojectdependencydetail()">
                  <option value="0"> Select Tasks</option>
                  <?php foreach((array)$projec_tasks as $projec_task){ ?>
                    <option <?php if(in_array($projec_task['id'],(array)$data['projec_task_dependencies'])){ echo "selected";
} ?> value="<?php echo $projec_task['id']; ?>"> <?php echo $projec_task['task_name']; ?> </option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

            <div class="position-relative mb-2">
              <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start member-btn" onclick="closeopenpopup('popup-phases','open')"><i class="ti-agenda m-r-5"></i> Project Phases
              </button>
              <div class="popup-phases design-sec " style="display: none;">
                <h5>  Phases </h5>
                <button type="" class="float-end member-close design-close" onclick="closeopenpopup('popup-phases','close')"><i class="ti-close"></i></button>

                <div class="form-group w-full">
                 <span> Tasks </span>
                 <select aria-label="Project Phases" class="form-control js-example-basic-single"  name="project_phases" id="project_phases"  onchange="updatedetail('project_phase_id',<?php echo $task_id;?>,this.value)" value="<?php echo fromDate($task['planned_start']); ?>">
                  <option value="0"> Select Tasks</option>
                  <?php foreach((array)$projec_phases as $projec_phase){ ?>
                    <option <?php if($projec_phase['id']==$task['project_phase_id']){ echo "selected";
} ?> value="<?php echo $projec_phase['id']; ?>"> <?php echo $projec_phase['project_phase_name']; ?> </option>
                  <?php } ?>
                </select>
              </div>
            </div>

             <span class="f-12 f-w-600 mt-3">Actions</span>
            <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start move-btn mb-2" onclick="closeopenpopup('popup-movecard','open')"><i class="ti-move m-r-5"></i> Move  </button>
              <div class="popup-movecard design-sec" style="display: none;">
                <h5>Move Card</h5>
                <button type="" class="float-end move-close design-close" onclick="closeopenpopup('popup-movecard','close')"><i class="ti-close"></i></button>
                <div class="form-group mt-3 w-full">
                  <span>Suggested</span>
                  <div>
                  <input type="hidden" name="oldstatus" id="oldval" value="<?php echo $task['current_status'];  ?>">
                  <select aria-label="Movestatus" name="movestatus" id="movestatus" class="form-select js-example-basic-single">
                    <?php foreach($data['status'] as $status){ ?>
                      <option <?php if($status['status'] == $task['current_status']){ echo "selected";
} ?> value="<?php echo $status['status']; ?>"><?php echo $status['name']; ?></option>
                    <?php } ?>
                  </select>
                  <button class="btn btn-primary mt-2" type="button" onclick="movecard()">Move</button>
                </div>
                </div>

              </div>
            <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start copy-btn mb-2" onclick="closeopenpopup('popup-copycard','open')"><i class="ti-files m-r-5"></i> Copy
            </button>
            <div class="popup-copycard design-sec" style="display: none;">
                <h5>Copy Card</h5>
                <button type="" class="float-end copy-close design-close" onclick="closeopenpopup('popup-copycard','close')"><i class="ti-close"></i></button>
                <div class="form-group mt-3 w-full">
                  <form action="" method="post" id="copyfrom">
                    <input type="hidden" name="copycardid" value="<?php echo $task_id; ?>">
                  <span>Title</span>
                  <div>
                    <textarea aria-label="Newtaskname" class="form-control" name="newtaskname"></textarea>
                  </div>
                  <div>
                  <span class="mt-2">Keep...</span>
                  </div>
                  <div class="form-group mt-2 mb-0">
                  <input aria-label="Newchecklist" type="checkbox" name="newchecklist" >
                  <label for="check1"  class="bg-white w-75">Check List</label>
                </div>
                <div class="form-group mt-2 mb-0">
                  <input aria-label="Newlabel" type="checkbox" name="newlabel">
                  <label for="check1"  class="bg-white w-75">Labels </label>
                </div>
                <div class="form-group mt-2 mb-0">
                  <input aria-label="Newattachment" type="checkbox" name="newattachment">
                  <label for="check1"  class="bg-white w-75">Attachments </label>
                </div>
                </div>
                <button class="btn btn-primary waves-effect waves-light" onclick="copynewcard()">Create Card </button>
                </form>
              </div>

               <span class="f-12 f-w-600">Attachments</span>
            <button class="btn waves-effect waves-light bg-white pt-2 pb-2 w-100 text-start move-btn" onclick="closeopenpopup('popup-attachment','open')"><i class="ti-upload m-r-5"></i> Attachments  </button>
              <div class="popup-attachment design-sec" style="display: none;">
                <h5>Attachments </h5>
                <button type="" class="float-end move-close design-close" onclick="closeopenpopup('popup-attachment','close')"><i class="ti-close"></i></button>
                <div class="form-group mt-3 ">

                 <form id="imageUploadForm" enctype="multipart/form-data">
                  <input aria-label="Image" type="file" id="imageInput" name="image" >
                  <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                  <input type="hidden" name="page" value="<?php echo "fileattachupload"; ?>">
                  <input class="btn btn-primary mt-2 float-right" type="submit" value="Upload">
                </form>
                </div>
              </div>

        </div>
      </div>

          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
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
<script type="text/javascript" src="assets/js/dropzone.js"></script>
<script type="text/javascript" src="assets/js/custom_file_uploading.js"></script>
