<?php

$urlwe = API."?mode=getapprovalmanagers";
$response2 = callAPI($urlwe);
$empdata2 = json_decode($response2,1);

function getArrayListData($lists,$filedname){
    $return  = array();
    foreach($lists as $list){
        $return[$list['id']] = $list[$filedname];
    }
    return $return;
}

$departwh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$departwh["field"] = "id,department_name";
$departdata = urlencode(json_encode($departwh));
$departurl = API."?mode=gettabledetailswithconditions&table=departments&data=".$departdata;
$departlist = callAPI($departurl);
$departlist = json_decode($departlist,1);

$designationswh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$designationswh["field"] = "id,designation_name ";
$designationsdata = urlencode(json_encode($designationswh));
$designationsurl = API."?mode=gettabledetailswithconditions&table=designations&data=".$designationsdata;
$designationslist = callAPI($designationsurl);
$designationslist = json_decode($designationslist,1);

$locationwh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$locationwh["field"] = "id,location_name";
$locationdata = urlencode(json_encode($locationwh));
$locationurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$locationdata;
$locationlist = callAPI($locationurl);
$locationlist = json_decode($locationlist,1);

$locationlist = getArrayListData($locationlist,'location_name');
$designationslist = getArrayListData($designationslist,'designation_name');
$departlist = getArrayListData($departlist,'department_name');

?>
<script type="text/javascript">
   function displayitemdata(option,id){
      $.ajax({
           url: "views/view_roll_back1.php",
           data:'option='+option+'&feild=emp_id&value='+$('#employee_id').val(),
           type: "POST",
           success:function(data){
            console.log(data);
               $("#"+id).html(data);
           },
           error:function (){}
      });
   }
   function statuspopup(status,id){
     var table_name = document.getElementById('table_name').value;
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
   function changebillable(id,name){
      var billable=  $("#billable_"+id).val();
      var status = "";
      var color = "";
      if(billable==0){ billable =1; status = "Non Billable"; color="red";  }else{ billable = 0; status = "Billable";  }
      $(".modallabel").text("Billable OR Non-Billable");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("billable");
      $("#field_value").val(billable);
      $("#modal_status").modal({
                    backdrop: 'static',
                    keyboard: false
                });
       $('#modal_status').modal('show');
      $("#billable_"+id).val(billable);
      $(".billable_lable"+id).text(status);
       $(".billable_lable"+id).css("color",color);

  }
function changeonsite(id,name){
     var onsite=  $("#onsite_"+id).val();
      var status = "";
      var color = "";
      if(onsite==0){ onsite =1;  status = "Off Site"; color="red"; }else{ onsite = 0; status = "On Site";}
      $(".modallabel").text("On Site OR Off Site");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("on_off_site");
      $("#field_value").val(onsite);
      $("#modal_status").modal({
                    backdrop: 'static',
                    keyboard: false
                });
       $('#modal_status').modal('show');
      $("#onsite_"+id).val(onsite);
    $(".onsite_lable"+id).text(status);
      $(".onsite_lable"+id).css("color",color);
   }
function save_statuse_bilable_onsite(){
   $.ajax({
   type: 'post',
   url: 'ajax.php',
   data: $('#insertemphistries').serialize()+"&page=insertemphistries",
   success: function (response) {
    console.log(response);
      $('#modal_status').modal('hide');

      }
   });
}

function changestatusworkfromhome(id,name){
      var workformhome=  $("#workfromhome"+id).is(":checked");
      var value = '';
      if(workformhome){
          $(".modal_view_status").text('Work From home On');
          value = 0;
        }else{
          $(".modal_view_status").text('Work From home Off');
          value = 1;
        }
      $(".modallabel").text("Work From home");
      $(".modal_view_employee_name").text(name);
      $("#emp_id").val(id);
      $("#field_name").val("work_from_home");
       $("#field_value").val(value);
       $("#modal_status").modal({
                    backdrop: 'static',
                    keyboard: false
                });
        $('#modal_status').modal('show');
}
function cancelpopuphistry(){
  var id = $("#emp_id").val();
  $("#workfromhome"+id).attr('checked', false);
}
function refrashpage(){
  location.reload();
}
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom ">
      <div class="card-header table-card-header mb-2">
       <h5>Approval Manager Teams </h5>
       <div class="card-header-right table-card-header ">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
          <i class="icofont icofont-reply"></i>
          Back
        </a>
        &nbsp;
      </div>
    </div>
            <div class="card-body">
                <ul class="nav md-tabs  tabs infisuite_tab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#my_team_gride_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-view-grid"></i> Grid</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#my_team_list_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="ti-list"></i>  List</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs card-block">
                    <div class="tab-pane  active" id="my_team_gride_tab" role="tabpanel">
                    <div class="row">
                    <?php foreach ($empdata2 as $team) {  ?>
                        <div class="col-xl-3 col-md-3">
                            <div class="card sale-card ">
                                <div class="card-header">
                                    <h5><?php echo ucwords($team['display_name']);?></h5>
                                </div>
                                <div class="card-block text-center">
                                    <div data-label="<?php echo initials($team['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger">
                                        <?php if($team['image']!=""){ ?>
                                            <img src="uploads/all/<?php echo $team['image']; ?>" alt="Attachment Preview">
                                        <?php } ?>
                                    </div>
                                    <div class="row  mb-3">
                                        <span class="text-muted font-weight-bold f-14">&nbsp;<?php  echo $designationslist[$team['designation_id']]; ?></span>
                                        <span class="text-muted font-weight-bold f-14"><?php echo $team['email_id']!=""?'<i class="ti-email"></i> '.$team['email_id']:"";?></span>
                                        <span class="text-muted font-weight-bold f-14">&nbsp;<?php echo $team['phone']!=""?'<i class="fa fa-phone"></i> '.$team['phone']:"";?></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="s-cont d-inline-block">
                                                <input aria-label="Checkbox" type="checkbox" <?php if($_SESSION['admin_type'] != '1'){ ?> disabled="" readonly="" <?php
} ?>  class="js-success-sm"
 <?php if($team['on_off_site']==0){ echo "checked";
} ?> onchange="changeonsite(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;<span style="font-size: 12px;
 <?php if($team['on_off_site']==1){ echo "color: red";
} ?>" class="col-form-label onsite_lable<?php echo $team['id']; ?>">
<?php if($team['on_off_site']==1){ echo "Off-Site";
}else{ echo "On-Site";
} ?> </span>
                                                <input type="hidden" id="onsite_<?php echo $team['id']; ?>" value="<?php echo $team['on_off_site']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-6 b-l-default">
                                            <div class="s-cont d-inline-block">
                                                <input aria-label="Checkbox" type="checkbox" <?php if($_SESSION['admin_type'] != '1'){ ?> disabled="" readonly="" <?php
} ?> class="js-primary-sm"
 <?php if($team['billable']=='0'){ echo "checked";
} ?> onchange="changebillable(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;

                                                <span style="font-size: 12px; <?php if($team['billable']=='1'){ echo "color: red";
} ?>" class="col-form-label billable_lable<?php echo $team['id']; ?>" >
 <?php if($team['billable']=='1'){ echo "Non Billable";
}else{ echo "Billable";
} ?> </span>

                                                <input type="hidden" id="billable_<?php echo $team['id']; ?>" value="<?php echo $team['billable']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane" id="my_team_list_tab" role="tabpanel">
<div class="dt-responsive table-responsive">
<table id="basic-btn" class="table table-striped table-bordered nowrap">
<thead>
    <tr>
        <th scope="col">Name </th>
        <th scope="col">phone</th>
        <th scope="col">Department </th>
        <th scope="col">designation</th>
        <th scope="col">location</th>
    <th scope="col">Bilable</th>
    <th scope="col">On-Site</th>
    </tr>
        </thead>
        <tbody>
<?php foreach ($empdata2 as $teams){ ?>
<tr>
    <td>
        <div class="align-middle">
        <?php if($teams['image']!="") {?>
            <img src="uploads/all/<?php echo $teams['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
        <?php } else { ?>
            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($teams['display_name']); ?></span>
        <?php } ?>
        <div class="d-inline-block width73">
            <a href="#!" class="text-danger text-capitalize"><h6>  <?php echo $teams['display_name']; ?></h6></a>
      <p><?php echo $teams['email_id']; ?></p>
        </div>
    </div>
    </td>
    <td><?php echo $teams['phone']!=""?$teams['phone']:'-'; ?></td>
    <td><?php echo $departlist[$teams['department_id']]; ?></td>
    <td><?php echo $designationslist[$teams['designation_id']]; ?></td>
    <td><?php echo $locationlist[$teams['location_id']]; ?></td>
   <td>
      <div class="s-cont d-inline-block">
         <input aria-label="Checkbox" type="checkbox" <?php if($_SESSION['admin_type'] != '1'){ ?> disabled="" readonly="" <?php
} ?> class="js-primary-sm"
 <?php if($teams['billable']=='0'){ echo "checked";
} ?> onchange="changebillable(<?php echo $teams['id']; ?>,'<?php echo ucwords($teams['display_name']);?>')" value="1"/>
        </div>
  </td>
  <td>
      <div class="s-cont d-inline-block">
          <input aria-label="Checkbox" type="checkbox" <?php if($_SESSION['admin_type'] != '1'){ ?> disabled="" readonly="" <?php
} ?> class="js-primary-sm"
 <?php if($teams['on_off_site']==0){ echo "checked";
} ?> onchange="changeonsite(<?php echo $teams['id']; ?>,'<?php echo ucwords($teams['display_name']);?>')" value="1"/>
        </div>
  </td>
</tr>
<?php }  ?>
        </tbody>
    </table>
</div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal_status" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title modallabel" id="exampleModalLabel">Effort Share Status</h5>

      </div>
<form class="form" id="insertemphistries">
      <input type="hidden" name="emp_id" id="emp_id">
      <input type="hidden" name="field_name" id="field_name">
      <input type="hidden" name="field_value" id="field_value">
      <div class="modal-body j-pro">
      <div class="alert alert-custom alert-default" role="alert">
          <div class="row" style="width: 100%;">
             <span class="col-lg-4 text-black"><b>Employee name : </b></span>
             <div class="col-lg-8 text-black">
                <p class="modal_view_employee_name"></p>
             </div>
             <span class="col-lg-4 text-black"><b>Status : </b></span>
             <div class="col-lg-8 text-black">
                <p class="modal_view_status"></p>
             </div>
          </div>
      </div>
      <div class="card-body">
<div class="form-group row">
   <label for="example-date-input-from" class="col-4 col-form-label">Effective From</label>
   <div class="col-5">
      <input class="form-control datepicker" type="text" name="effective_from" value="2011-08-19" id="example-date-input-from">
   </div>
   <label for="example-date-input-to" class="col-4 col-form-label mt-2">Effective To</label>
   <div class="col-5 mt-2">
      <input class="form-control datepicker" type="text" name="effective_to" value="2011-08-19" id="example-date-input-to">
   </div>
</div>
      </div>
   </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="refrashpage()">Cancel</button>
        <button type="button" class="btn btn-primary " onclick="save_statuse_bilable_onsite()" >Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>

