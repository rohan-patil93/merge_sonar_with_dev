<?php

$startingdate  = $_GET['SDATE']!=""?$_GET['SDATE']:date("Y-m-d", strtotime('monday this week', strtotime(date('Y-m-d'))));
$endingdate = $_GET['EDATE']!=""?$_GET['EDATE']:date("Y-m-d", strtotime('sunday this week', strtotime(date('Y-m-d'))));
  $approval_url = API."?mode=getweecklybulkapprovaltimesheet&startingdate=".$startingdate."&endingdate=".$endingdate;
$app_data = callAPI($approval_url);
$app_data = json_decode($app_data,1);
$startDate = new DateTime($startingdate);
$endDate = new DateTime($endingdate);
$linkstartDate = new DateTime($startingdate);
$monthstartdate = $_GET['SDATE']!=""?$_GET['SDATE']:date('Y-m'.'-1');
$monthenddate = $_GET['EDATE']!=""?$_GET['EDATE']:date('Y-m-t');
$bb = 1; $dd_check=0; $dd_last_check=0;
while($startDate <= $endDate){
    if(strtotime($monthstartdate) <= strtotime($startDate->format('Y-m-d')) && strtotime($monthenddate) >= strtotime($startDate->format('Y-m-d'))){

        if($bb==1){
            $dd_check_val = date('N', strtotime($startDate->format('Y-m-d')));
            if($dd_check_val == 1){
                $dd_check=1;
            }
            if($dd_check_val == 7){
                $dd_last_check=1;
            }
        }

        $datearraylist[] = $startDate->format('jS M y')."</br>". $startDate->format('l');

    }
    $startDate->modify('+1 day');
}
$approval_pending_array = array();
foreach($app_data['res'] as $row){
    if($row['current_status'] == "8"){
        $approval_pending_array[]= $row;
    }
    if($row['current_status'] >= "10" && $row['current_status']!=20){
        $approval_array[]= $row;
    }
    if($row['current_status'] == "0"){
            $yet_to_submit[]= $row;
    }
}
foreach($app_data['yattosubmit'] as $row2){
    $yet_to_submit[]= $row2;
}
$vdata1["where"] = array('table_name'=> "project_timesheets",'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$vdata;
$vdatas = callAPI($vurl);
$status_progresss = json_decode($vdatas,1);
foreach($status_progresss as $status_progress){
    $statu_progress[$status_progress['status']]['name']=$status_progress['name'];
    $statu_progress[$status_progress['status']]['action_label']=$status_progress['action_label'];
}

?>
<script type="text/javascript">
    function projecttimesheetsubmittohr(){
        var ick=0;
        $.each($("input[name='checkbox1']:not(:checked)"), function(){
            ick=ick+1;
        });
        if(ick>0){
            var msg = <?php echo "'".$_SESSION['error_message']['9023']."'" ?>;
            swal.fire({
                title: msg,
                type: "success",
                buttonsStyling: false,
                confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, Submit!",
                confirmButtonClass: "btn btn-success",
                showCancelButton: true,
                cancelButtonText: "<i class='la la-hand-o-right'></i> No, Stop",
                cancelButtonClass: "btn btn-danger"
            }).then(function(result) {
                if (result.value) {
                    projecttimesheetsubmittohrs();
                }
            });
        }else{
            projecttimesheetsubmittohrs();
        }
    }
      function projecttimesheetsubmittohrs(){
        var favorite = [];
        var timesheetid = [];
        var ick=0;
        var weekstartdate = $("#weekstartdate").val();
        var weekenddate = $("#weekenddate").val();
        $.each($("input[name='checkbox1']:checked"), function(){
            var timesheets_details = $(this).val().split("||");
            favorite.push(timesheets_details[0]);
            timesheetid.push(timesheets_details[1]);
            ick=ick+1;
        });
        if(ick=='0'){
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX',"Please Select Any One Employee",'');
            return false;
        }
        var table_name = 'project_timesheets';
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updateprojecttimesheetsblukforhrweekly&table='+table_name+'&status=102&extra='+$('#extra').val()+'&emp_id='+favorite+'&weekstartdate='+weekstartdate+'&weekenddate='+weekenddate+'&timesheetid='+timesheetid,
            type: "POST",
            success:function(data){
                console.log(data);
                var msg = <?php echo "'".$_SESSION['error_message']['9024']."'" ?>;
                console.log(msg);
                swal.fire({
                    title: msg,
                    type: "success",
                    buttonsStyling: false,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                    confirmButtonClass: "btn btn-info",
                    showCancelButton: false,
                    cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                    cancelButtonClass: "btn btn-default"
                }).then(function(result) {
                    if (result.value) {
                        window.location.reload();
                    }
                });
            },
            error:function (){}
        });
    }
    function statuspopup(status,id){
        var favorite = [];
        var timesheetid = [];
        var weekstartdate = $("#weekstartdate").val();
        var weekenddate = $("#weekenddate").val();
        $.each($("input[name='checkbox2']:checked"), function(){
            var timesheets_details = $(this).val().split("||");
            favorite.push(timesheets_details[0]);
            timesheetid.push(timesheets_details[1]);
        });

        var table_name = 'project_timesheets';
        jQuery.ajax({
            url: "views/custom_insert_status_weekly_bulk_approval_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+favorite+'&extra='+$('#extra').val()+'&weekstartdate='+weekstartdate+'&weekenddate='+weekenddate+'&timesheetid='+timesheetid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });


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

function select_all_approval_pending(){
    if($('#select_all_approval_pending').is(':checked')){
        $('.checkbox2').each(function(){
            this.checked = true;
        });
        $("#status_dropdown").show();
    }else{
        $('.checkbox2').each(function(){
            this.checked = false;
        });
        $("#status_dropdown").hide();
    }
}

function select_approval_pending(val){
    tocheckdrodown();
}

function select_approval_check(){
    var len=0;
         $(".checkbox1:checked").each(function () {
         len=len+1;
         });
         console.log(len);
        if(len>0){
              $(".approval_status").show();
        }else{
              $(".approval_status").hide();
          }
}

function select_all_approval(){
    if($('#select_all_approval').is(':checked')){
        $('.checkbox1').each(function(){
            this.checked = true;
        });
        $(".approval_status").show();
    }else{
        $('.checkbox1').each(function(){
            this.checked = false;
        });
        $(".approval_status").hide();
    }
}

  function tocheckdrodown(){
        var len=0;
         $(".checkbox2:checked").each(function () {
         len=len+1;
         });
        if(len>0){
              $("#status_dropdown").show();
        }else{
              $("#status_dropdown").hide();
          }
      }

      function tocheckallboxes(){
          console.log("Data");
          var val = $('#val_select_all').val();
          console.log(val);
          if(val==0){
              $('#val_select_all').val('1');
              $('.checkbox1').each(function(){
                  this.checked = true;
              });
          }else{
              $('#val_select_all').val('0');
              $('.checkbox1').each(function(){
                  this.checked = false;
              });
          }
          tocheckdrodown();
      }


      function notifyuser(id){
      jQuery.ajax({
          url: "views/custom_insert_user_notify.php",
          data:'id='+id+_2'&comments=Please submit your timesheet ',
          type: "POST",
          success:function(data){
              $('#popup1').html(data);
        $("#kt_modal_KTDatatable_remote").modal("show");
              },
              error:function (){}
            });
    }
    function checkboxwisecheck(){
        var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){
            favorite.push($(this).val());
        });
        console.log(favorite);
        var table_name = 'project_timesheets';
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getbugetamountesignvendorwise&table='+table_name+'&rid='+favorite+'&extra='+$('#extra').val(),
            type: "POST",
            success:function(data){
                console.log("man"+data);
                if(data==1){
                    var msg = <?php echo "'".$_SESSION['error_message']['9026']."'" ?>;
                    swal.fire({
                        title: msg,
                        type: "success",
                        buttonsStyling: false,
                        confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                        confirmButtonClass: "btn btn-info",
                        showCancelButton: false,
                        cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                        cancelButtonClass: "btn btn-default"
                    });
                }
            },
            error:function (){}
        });
    }

    function getWeekInMonth(year,month,classs,thisval){
        console.log(thisval);
        $("."+classs).removeClass("bg-primary btn btn-light");
        $(thisval).addClass("bg-primary");
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getweekinmonth&year='+year+'&month='+month,
            type: "POST",
            success:function(data){
                console.log(data);
                $(".getajaxweektab").html(data);
            }
        });

    }

 </script>
  <div class="d-flex flex-column-fluid">
      <div class="container-fluid">
        <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header table-card-header">
                      <h5>Weekly Bulk TimeSheet Approval</h5><button type="button" class="btn p-0 border-0 bg-transparent ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Take A Tour" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                      <div class="card-header-right table-card-header ">

                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
    </div>

          <!--begin::Row-->
    <div class="p-3">
           <div class="row
          </div>
          <!--end::Row-->
          <!--begin::Row-->
          <div class="row col-lg-12">
              <div class="col-lg-12">
                  <!--begin::Advance Table Widget 2-->
                  <div class="card card-custom card-stretch gutter-b">


<div class="row p-20">
    <div class="col-lg-12 row">
        <div class=" infisuite_tab col-lg-12">
            <?php
            $month = strtotime(date('Y').'-'.date('m').'-01 - 11 months');
            $end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 0 months');
            while($month <= $end){
                $disss = date('M Y',$month);
                $dis_year = date('Y',$month);
                $dis_month = date('m',$month);
                if(date("m",strtotime($startingdate)) == $dis_month){
                    $class = 'nav-link bg-primary me-6 active';
                }else{
                    $class = 'btn btn-light';
                }
                $month = strtotime("+1 month", $month);
                ?>
                <div class="float-left m-r-5 b-radius-5 nav-item ">
                    <a  class="<?php echo $class; ?> nav-link  me-6 active yearselectpan" style="font-size: 12px !important;" href="#" onclick="getWeekInMonth('<?php echo $dis_year; ?>','<?php echo $dis_month; ?>','yearselectpan',this)"> <?php echo $disss; ?></a>
                </div>
            <?php  } ?>
        </div>

    <div class=" col-lg-12" style="margin-bottom: 0px !important;">
            <div class="card-body" >
                <div class="row">
                    <div class="row col-lg-12" style="padding:0px !important;" name="select_month">
                        <div class="col-lg-12">
                            <div class=" infisuite_tab getajaxweektab">
                                <?php

                                $weekly_date = get_weeks(date("Y",strtotime($startingdate)),date("m",strtotime($startingdate)));
                                foreach($weekly_date as $keyweek=>$weekly_date){
                                    if(date("dmY",strtotime($startingdate)) == date("dmY",strtotime($weekly_date[0]))){
                                        $class = 'nav-link bg-primary me-6 active';
                                    }else{
                                        $class = 'btn btn-light';
                                    }
                                    ?>
                                    <div class="float-left m-r-5 b-radius-5 nav-item ">
                                        <a class="<?php echo $class; ?> nav-link  me-6 active " style="font-size: 12px !important;" href="index.php?option=COM_LIST_WEEKLY_BULK_APPROVAL_TIMESHEETS&SDATE=<?php echo $weekly_date[0]; ?>&EDATE=<?php echo $weekly_date[count((array)$weekly_date)-1]; ?>"> <?php echo "Week - ".$keyweek; ?></br><span class="f-10"><?php echo date("d D",strtotime($weekly_date[0]));?></span></a>
                                    </div>
                                <?php  } ?>
                            </div>
                        </div>
                        <input type="hidden" name="extra" id="extra_3" value="<?php echo $_GET['PAY_YEAR']."-".$_GET['PAY_MONTH']."-01" ?>">
                    </div>
                </div>
            </div>
        </div>
    <div class="col-lg-12 mt-5">
        <ul class="nav  md-tabs infisuite_tab">
            <li class="nav-item" name="approved_tab">
                <a class="nav-link active" data-bs-toggle="tab" href="#home3" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved
                        &nbsp;<span class="badge badge-warning"><?php echo count((array)$approval_array); ?></span>
                </a>
            </li>
            <li class="nav-item" name="approval_pending_tab">
                <a class="nav-link" data-bs-toggle="tab" href="#profile3" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approvals Pending
                    &nbsp;    <span class="badge badge-warning"><?php echo count((array)$approval_pending_array); ?></span>
                </a>

            </li>
            <li class="nav-item" name="yat_to_submit_tab">
                <a class="nav-link" data-bs-toggle="tab" href="#messages3" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Yet to Submit
                    &nbsp;<span class="badge badge-warning"><?php echo count((array)$yet_to_submit); ?></span></a>
            </li>
        </ul>

            <input type="hidden" id="weekstartdate" value="<?php echo $startingdate; ?>">
            <input type="hidden" id="weekenddate" value="<?php echo $endingdate; ?>">
    </div>

    </div>

</div>


                      <div class="card-toolbar">
                          <div class="card-block">

                <div class="col-lg-12 row">


                    <!-- Tab panes -->
                    <div class="tab-content card-block">
                        <div class="tab-pane active" id="home3" role="tabpanel">

                <?php if(count((array)$approval_array)!='0'){?>
                <div class="col-lg-12 approval_status" style="display:none">
                                      <div class="col-lg-3 float-right"  >
                                          <button <?php if($btncheck!=1){?> onclick="projecttimesheetsubmittohr()" <?php
}else{ echo "disabled";
} ?> class="btn btn-primary font-size-sm float-right
 <?php if($btncheck==1){ echo "disabled";
} ?>" ><?php echo $statu_progress['10']['name']; ?></button>
                                      </div>
                                    </div>
                                  <?php } ?>
                            <div class="table-responsive">
                                                        <table class="table table-borderless table-vertical-center">
                                                            <thead>
                                                                <tr>
                                                    <th scope="col" class="f-14"> 1</th>
                                                    <th scope="col" class="f-14" name="approval_pending_employee_details">Employee Details</th>

                                                    <?php foreach($datearraylist as $date2){?>
                                                        <th scope="col" class="f-14  text-right" name="approval_pending_billable_hours"><?php echo $date2; ?></th>
                                                <?php    }  ?>
                                                    <th scope="col" class=" text-right" name="approval_pending_status">Total</th>

                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $num1=1;
                                                $cnl =1;
                                                if(count((array)$datearraylist)!=7 && $dd_check!=1 ){
                                                    $cnl = 8-count((array)$datearraylist);
                                                }
                                                if(count((array)$datearraylist)!=7 && $dd_last_check==0){
                                                                                                        $cnl = 1;
                                                                                                }
                                                                                                if(count((array)$approval_array)!='0'){
 foreach((array)$approval_array as $approval_array){ $totalhrs1 = 0;?>
                                                    <tr>
                                                        <td><?php echo $num1; ?></td>
                                                        <td class="f-14">
                                                        <div class="align-middle">
                                                            <?php if($approval_array['image']) {?>
                                                                <img src="uploads/all/<?php echo $approval_array['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($approval_array['display_name']); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                <h6><?php echo $approval_array['display_name']; ?></h6>
                                                                <p class="text-muted m-b-0"><?php echo $approval_array['emp_code']; ?></p>
                                                            </div>
                                                        </div>
                                                        </td>

                                                        <?php $n1=$cnl;
                                                         foreach($datearraylist as $date22){ $totalhrs1 += $approval_array['d'.$n1]; ?>
                                                        <td class="f-14 text-right view1  <?php echo 'd'.$n1; ?> "><?php echo customeummarmonyformateround($approval_array['d'.$n1]); ?></td>
                                                        <?php $n1++;
} ?>
                                                        <td class="text-right"><a href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $linkstartDate->format('Y-m-d'); ?>&EMP=<?php echo $approval_array['emp_id']; ?>" target="_blank"   class="form-label label label-info text-white "><?php echo $totalhrs1; ?></a></td>

                                                    </tr>
                                                <?php    $num1++;
}
}else{ ?>
                                                    <tr class="text-center">
                                                        <td colspan="10" class="text-center">Record Not Found</td>
                                                    </tr>
                                                    <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                        </div>
                        <div class="tab-pane" id="profile3" role="tabpanel">
                        <div class="col-lg-12">
                          <?php if(count((array)$approval_pending_array)!='0'){?>
                          <div class="d-flex align-items-center flex-lg-fill mb-2 float-right">
                            <div class="btn-group" id="status_dropdown" style="display:none;">
                                <button class="btn btn-primary dropdown-toggle font-size-sm" style="color: <?php echo "#".$stausresult['8']['color']; ?>;    background:  <?php echo "#".strval($stausresult['8']['background_color']); ?>; border-color: <?php echo "#".strval($stausresult['8']['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $statu_progress['8']['name']; ?> </button>
                                <div class="dropdown-menu" id="dropdownlist_<?php echo $fid; ?>">
                                    <div class="dropdown-item cursor-pointer" onclick="statuspopup('10','2')" name="status" id="status" value="10" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo $statu_progress['10']['action_label']; ?>  </div>
                                    <div class="dropdown-item cursor-pointer" onclick="statuspopup('20','2')" name="status" id="status_2" value="20" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo $statu_progress['20']['action_label']; ?>  </div>
                                </div>
                            </div>
                        </div>
                        <?php }  ?>
                                    </div>
                            <div class="table-responsive">
                                        <table class="table table-borderless table-vertical-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="f-14"> 2</th>
                                                    <th scope="col" class="f-14" name="approval_pending_employee_details">Employee Details</th>

                                                    <?php foreach($datearraylist as $date1){?>
                                                        <th scope="col" class="f-14  text-right" name="approval_pending_billable_hours"><?php echo $date1; ?></th>
                                                <?php    }  ?>
                                                    <th scope="col" class=" text-right" name="approval_pending_status">Total</th>
                                                    <th scope="col" class=" text-center" >
                                                        <div class="checkbox-fade fade-in-primary">
                                                            <label class="checkbox checkbox-success">
                                                                <input aria-label="Ect All Approval Pending" type="checkbox" id="select_all_approval_pending" class="itemRow" onclick="select_all_approval_pending()" onkeydown="">
                                                                <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                                            </label>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $num2=1;
                                        if(count((array)$approval_pending_array)!='0'){
                                            foreach((array)$approval_pending_array as $approving_pendi_list){ $totalhrs2 = 0; ?>

                                                    <tr>
                                                        <td><?php echo $num2; ?></td>
                                                        <td class="f-14">
                                                        <div class="align-middle">
                                                            <?php if($approving_pendi_list['image']) {?>
                                                                <img src="uploads/all/<?php echo $approving_pendi_list['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($approving_pendi_list['display_name']); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                <h6><?php echo $approving_pendi_list['display_name']; ?></h6>
                                                                <p class="text-muted m-b-0"><?php echo $approving_pendi_list['emp_code']; ?></p>
                                                            </div>
                                                        </div>

                                                        <?php $n2=$cnl;
                                                         foreach($datearraylist as $date2){ $totalhrs2 += $approving_pendi_list['d'.$n2]; ?>
                                                        <td class="f-14 text-right view2 <?php echo 'd'.$n2 ?>"><?php echo customeummarmonyformateround($approving_pendi_list['d'.$n2]); ?></td>
                                                        <?php $n2++;
} ?>
                                                        <td class="text-right"> <a href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo $linkstartDate->format('Y-m-d'); ?>&EMP=<?php echo $approval_array['emp_id']; ?>" target="_blank"   class="form-label label label-info text-white "><?php echo $totalhrs2; ?></a></td>
                                                        <td class="text-center pr-0">
                                                    <div class="checkbox-fade fade-in-primary" name="individual_approval_pending_checkbox">
                                                        <label class="checkbox checkbox-success">
                                                            <input aria-label="Checkbox2" type="checkbox" class="itemRow checkbox2" name="checkbox2" onclick="select_approval_pending(this.checked)" value="<?php echo $approving_pendi_list['emp_id']."||".$approving_pendi_list['min_timesheet_id']; ?>" onkeydown="">
                                                            <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                    <?php $num2++;
}
}else{ ?>
                                                    <tr class="text-center">
                                                        <td colspan="10" class="text-center">Record Not Found</td>
                                                    </tr>
                                                    <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                        </div>
                        <div class="tab-pane" id="messages3" role="tabpanel">
                            <div class="table-responsive">
                                        <table class="table table-borderless table-vertical-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="f-14"> 3</th>
                                                    <th scope="col" class="f-14" name="approval_pending_employee_details">Employee Details</th>

                                                    <?php foreach((array)$datearraylist as $date1){?>
                                                        <th scope="col" class="f-14  text-right" name="approval_pending_billable_hours"><?php echo $date1; ?></th>
                                                <?php    }  ?>
                                                    <th scope="col" class=" text-right" name="approval_pending_status">Total</th>

                                                    </tr>
                                            </thead>
                                            <tbody>
                                                <?php  $num3=1;
                                                if(count((array)$yet_to_submit)!='0'){
                                                    foreach($yet_to_submit as $yet_to_submit){
                                                        $totalhrs3 = 0; ?>
                                                    <tr>
                                                        <td><?php echo $num3;?></td>
                                                        <td class="f-14">
                                                        <div class="align-middle">
                                                            <?php if($yet_to_submit['image']) {?>
                                                                <img src="uploads/all/<?php echo $yet_to_submit['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($yet_to_submit['display_name']); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                <h6><?php echo $yet_to_submit['display_name']; ?></h6>
                                                                <p class="text-muted m-b-0"><?php echo $yet_to_submit['emp_code']; ?></p>
                                                            </div>
                                                        </div>

                                                        <?php $n3=$cnl;
                                                         foreach($datearraylist as $date2){  $totalhrs3 += $yet_to_submit['d'.$n3];?>
                                                        <td class="f-14 text-right view3 <?php echo 'd'.$n3; ?>"><?php echo customeummarmonyformateround($yet_to_submit['d'.$n3]); ?></td>
                                                        <?php $n3++;
} ?>
                                                        <td class="text-right"><span class="form-label label label-info"><?php echo $totalhrs3; ?></span></td>
                                                    </tr>
                                                <?php    $num3++;
}
}else{ ?>
                                                    <tr class="text-center">
                                                        <td colspan="10" class="text-center">Record Not Found</td>
                                                    </tr>
                                                    <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
                      </div>
                  </div>
                  <!--end::Body-->
              </div>
              <!--end::Advance Table Widget 2-->
          </div>
