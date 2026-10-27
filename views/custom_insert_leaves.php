<?php
$_SESSION['apply_js']['multiselest'] = 1;















if($_GET['Rid']!=''){
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $rid = explode(":", $rid);
  $rid= $rid[1];
}

if(isset($_POST['Save'])){
  $_POST['half_day'] = (isset($_POST['half_day']))?"0":"1";
  $_POST['employee_filter'] = $_POST['fieldlist'];
  $updaterow = $_POST['rid'];
  if($updaterow!=""){
    $_POST['updated_date'] = nowDate();
    $_POST['updated_by'] = $_SESSION['userid'];
    $url = API."?mode=saveleaveapplication&rid=".$updaterow."&table=leave_type_levels&method=post";
    $response = file_post_contents($url, $_POST, 'update');
  }else{
    $_POST['created_date'] = nowDate();
    $_POST['created_by'] = $_SESSION['userid'];
    $url = API."?mode=saveleaveapplication&table=leave_type_levels&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
  }

header("location:index.php?option=COM_LIST_LEAVE_TYPES");





}


$url = API."?mode=togetleaveform&company_id=".$company_id."&is_active=0&id=".$rid;
$response = callAPI($url);
$setup_details = json_decode($response,1);
$lcdata = $setup_details['leave_categories'];
$ltdata = $setup_details['leave_types'];
$ltldata = $setup_details['leave_type_levels'];

$filte_data = $setup_details['filter_data'];
$filter_id = $filte_data['id'];

if(empty($ltldata)){
 $ltldata[0]['action_number']= 10;
}
$employee_filter = $ltdata['employee_filter'];
$first_accruals = array('P'=>'Prorate','F'=>'Full Amount');
$first_accrual_list = array('D'=>'Days','W'=>'Weeks','M'=>'Months','Y'=>'Years');
$accruals_happen_list = array('E'=>'At the end of period','B'=>'At the beginning of period');
$accruals_freq_list = array('Daily','Monthly','Monthly','Yearly');
$percentage_list =  array('P'=>'Percentage','D'=>'Days');
$accured_action_numberlist= array('D'=>'Daily','W'=>'Weekly','M'=>'Monthly','Y'=>'Yearly');
$carryover_list= array('N'=>'None','U'=>'Unlimited','5'=>'5','10'=>'10','15'=>'15','20'=>'20');
$accruals_befaf_list =  array('B'=>'Before Hire Date','A'=>'After Hire Date');

$dataedu["where"] = array('is_active'=> '0','apply_table_name'=>'leave_type');
$costdataedu = urlencode(json_encode($dataedu));
$eduurl = API."?mode=gettabledetailswithconditions&table=application_filters&data=".$costdataedu;
$edutypedata = callAPI($eduurl);
$appfilter = json_decode($edutypedata,1);
foreach($appfilter as $appfilt){
  $field[] = $appfilt['fieldname'];
}
$fieldlist = implode(',', (array)$field);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=employees";
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$lists = json_decode($list['list_attributes'],1);
foreach($lists as $kee => $dislist){
  if(in_array($kee, (array)$field)){
    $fileds[$kee] = $dislist;
    $fileds[$kee]['insert_view_width'] =12;
    $fileds[$kee]['table'] = $kee;
    $disarray['normal']=$fileds;
  }
}
$class_tpe = 'leave';
$table_name = "employees";


$montharray =$months = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July ','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
$weekdays = ['Sunday'=>'Sunday','Monday'=>'Monday','Tuesday'=>'Tuesday','Wednesday'=>'Wednesday','Thursday'=>'Thursday','Friday'=>'Friday','Saturday'=>'Saturday'];

?>
<style>
.stage{
  margin-left: -46px;background-color: #3699FF; width: 60px;height: 32px;border-radius: 2px;margin-top: -8px;color: #FFF;font-size: 14px;padding: 2px;
}
.radio-inline .form-label input[type="radio"]{
 display: none;
}
</style>

<script type="text/javascript">


  function addrows(){
   var htrow = "";
   htrow+='<div style="clear: both"></div>';
   htrow+='<div class="col-xl-9 t-3 p-2"><div class="stage label font-weight-boldest label-rounded label-primary" >Stage 2 </div>  <h6> Starts ..</h6> <input type="hidden" name="levelrowid[]" value=""> <div class="col-lg-2  group mb-5 p-1" style="float: left;">  <input aria-label="10" class=" form-control" type="text" name="action_number[]"  placeholder="10" value="" required />  </div>';
   htrow+=' <div class="col-lg-6 p-1" style="float: left;"><select aria-label="Action Frequency[]" class="form-control  form-select js-example-basic-single" id="kt_select2_48"  name="action_frequency[]"><option value="">Select Any One </option><?php foreach ($first_accrual_list as $falkey => $falvalue) { ?> <option value="<?php echo $falkey; ?>" ><?php echo $falvalue;?></option><?php
} ?></select></div>';
   htrow+='  <div class="col-lg-3  p-1" style="float: left;"><select aria-label="Before Or After[]" class="form-control  form-select js-example-basic-single" id="kt_select2_49"  name="before_or_after[]"><option value="">Select Any One </option><?php foreach ($accruals_befaf_list as $bakey => $bavalue) { ?><option value="<?php echo $bakey; ?>" ><?php echo $bavalue;?></option><?php
} ?></select></div>';
   htrow+=' <div style="clear: both"></div> <div class="col-lg-12 " > <h6> Amount Accrued ..</h6><div class="col-lg-2 p-1  group mb-5" style="float: left;"> <input aria-label="10" class="form-control min-w-50px" type="text" name="base_on[]"  placeholder="10" value="" required />  </div> <div class="col-lg-6 p-1" style="float: left;"> <select aria-label="Accured Action Based[]" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_31"  name="accured_action_based[]"><option value="">Select Any One </option><?php foreach ($accured_action_numberlist as $falkey => $falvalue) { ?><option value="<?php echo $falkey; ?>" <?php echo $acvalue['accured_action_number']==$falkey ? 'selected':'';?> ><?php echo $falvalue;?></option><?php
} ?></select> </div> </div>';
   htrow+=' <div style="clear: both"></div>    <div class="col-lg-12 mt-2"> <div class="col-lg-4 p-1 group mb-5" style="float: left;"><span class="">  Max Accrual  </span>   <input aria-label="Max Accured Limit[]" class="form-control " type="text" name="max_accured_limit[]"   />  </div> <div class="col-lg-4 p-1" style="float: left;"><span>Carryover </span> <select aria-label="Carry Over Limit[]" class="form-control selectlist form-select js-example-basic-single"  name="carry_over_limit[]"><option value="">Select Any One </option><?php foreach ($carryover_list as $coakey => $coavalue) { ?><option value="<?php echo $coakey; ?>"><?php echo $coavalue;?></option><?php
} ?></select></div><div class="col-lg-4 p-1" style="float: left;"><span>Carryover Type</span>  <select aria-label="Carry Over Limit Type[]" class="form-control selectlist form-select js-example-basic-single"  name="carry_over_limit_type[]"<option value="">Select Any One </option><?php foreach ($percentage_list as $plkey => $plvalue) { ?><option value="<?php echo $plkey; ?>" ><?php echo $plvalue;?></option> <?php
} ?></select>  </div>  </div>';
    $('#lavel').append(htrow);
  }
function showpopup(){

    var filter_id = document.getElementById('filtertbl_rowid').value;
    var process_type = document.getElementById('process_type').value;
    var application_table = document.getElementById('application_table').value;
    var fieldlist = document.getElementById('fieldlist').value;
    jQuery.ajax({
        url: "views/custom_insert_filters.php",
        data:'filter_id='+filter_id+'&process_type='+process_type+"&tablename="+application_table+"&fieldlist="+fieldlist,
        type: "GET",
        success:function(data){
            $('#dynamic_popup_task_settings').html(data);
            $("#kt_modal_dynamic_popup_task_setting").modal("show");
        },
        error:function (){}
    });




  }
function getnamelist(emplist){
    console.log(emplist);
    document.getElementById('filterby').value = emplist;
    $('#kt_modal_dynamic_popup').modal('hide');
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
    }else if(selectscehduleaction == "Y"){
        $("#yearlyschedule").show('slow');
    }
}

</script>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid" >
    <div class="card card-custom <?php echo $card_sticky; ?> j-content" id="kt_page_sticky_card">


      <div class="card-header mb-3">
        <h5>Leave Type</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>


      <div class="card-body p-4">
        <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
          <div class="row justify-content-center py-10 px-8 py-lg-12 px-lg-10">
            <div class="col-xl-12 col-xxl-7">
              <form class="form" id="kt_form" method="post" action="" enctype="multipart/form-data">
                <?php


                ?>

                <input type="hidden" name="process_type" id="process_type" value="C">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">
                <input type="hidden" name="application_table" id="application_table"  value="leave_types">
                <input type="hidden" name="fieldlist" id="fieldlist" value='<?php echo $ltdata['employee_filter'];?>'>
                <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">

                <div class="row">
                  <div class="form-group col-lg-9 ">
                    <span>Leave Category</span><?php //print_r($lcdata);?>
                    <select aria-label="Category Name" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_47"  name="category_name">
                      <option value=""> Select Any one</option>
                      <?php foreach ($lcdata as $lc) {  ?>
                        <option value="<?php echo $lc['id'];?>" <?php if($ltdata['leave_category_id'] == $lc['id']){ ?>selected <?php
} ?> ><?php echo $lc['name'];?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-lg-9">
                   <span> Leave Policy Name </span>
                   <input aria-label="Leave Type Name" class="form-control" type="text" name="leave_type_name"  value="<?php echo $ltdata['leave_type_name'];?>" required />
                 </div>
                 <div class=" col-xl-9 separator separator-dashed my-10"></div>
                 <div class="col-xl-9"><h4>Accrual Schedule</h4></div>
                 <?php
                 $stage_index = 0;
                 foreach ($ltldata as $ackey => $acvalue) {
                   $stage_index++;
                   ?>
                   <div style="border-left: 3px solid #3699FF;" id="lavel" class="m-l-35">
                    <div class="col-xl-9 p-2 form-group">
                      <div class="stage label font-weight-boldest label-rounded label-primary" >Stage <?php echo $stage_index; ?> </div>
                      <span class="form-label col-lg-12"> leave Starts</span>
                      <input type="hidden" name="levelrowid[]" value="<?php echo $acvalue['id'];?>" >
                      <div class="col-lg-2  group mb-5 p-1" style="float: left;">

                        <input aria-label="Action Number[]" class="form-control" type="text" name="action_number[]" value="<?php echo $acvalue['action_number'];?>" placeholder="10" required />

                      </div>
                      <div class="col-lg-6 p-1" style="float: left;">


                        <select aria-label="Action Frequency[]" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_48_2"  name="action_frequency[]">
                          <option value="">Select Any One </option>

                          <?php foreach ($first_accrual_list as $falkey => $falvalue) { ?>
                            <option value="<?php echo $falkey; ?>" <?php echo $acvalue['action_frequency']==$falkey ? 'selected':'';?> ><?php echo $falvalue;?></option>
                          <?php } ?>

                        </select>
                      </div>
                      <div class="col-lg-3 p-1" style="float: left;">
                        <select aria-label="Before Or After[]" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_49_2"  name="before_or_after[]">
                          <?php foreach ($accruals_befaf_list as $bakey => $bavalue) { ?>
                            <option value="<?php echo $bakey; ?>" <?php echo $acvalue['before_or_after']==$bakey ? 'selected':'';?> ><?php echo $bavalue;?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div style="clear: both"></div>
                    <div class="col-xl-12 row form-group" >
                     <span class="form-label ">  Amount Accrued</span>
                     <div class="col-lg-1  group mb-5 p-1  m-l-15" style="float: left;">
                      <input aria-label="10" class="form-control" type="text" name="base_on[]"  placeholder="10" value="<?php echo $acvalue['base_on'];?>" required />
                    </div>
                    <div class="col-lg-4 p-1" style="float: left;">
                      <select aria-label="Accured Action Based[]" class="form-control selectlist form-select js-example-basic-single"   name="accured_action_based[]"   id="selectscehduleaction" onchange="scehduleaction()">
                        <option value="">Select Any One </option>
                        <?php foreach ($accured_action_numberlist as $falkey => $falvalue) { ?>
                          <option value="<?php echo $falkey; ?>" <?php echo $acvalue['accured_action_based']==$falkey ? 'selected':'';?> ><?php echo $falvalue;?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-lg-6" id="monthlyschedule" style="<?php echo $acvalue['accured_action_based']=="M"?'':'display:none'; ?>">
                        <div class="row form-group">
                          <span class="form-label col-lg-8  text-center mt-3">Select Day Of The Month</span>
                          <div class="col-lg-4"><input aria-label="On The Day[]" type="number" class="form-control col-lg-4" name="on_the_day[]" value="<?php echo  $acvalue['day']; ?>" ></div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="yearlyschedule" style="<?php echo $acvalue['accured_action_based']=="Y"?'':'display:none'; ?>">
                        <div class="row form-group">
                            <span class="form-label col-lg-3  text-center mt-3">Select Month</span>
                            <div class="col-lg-4">
                                <select aria-label="On The Month[]" name="on_the_month[]"  style="width:150px;" class="form-control form-select js-example-basic-single">
                                  <?php foreach ($montharray as $mont=>$montname) {    ?>
                                    <option value="<?php echo $mont;?>" <?php if($mont == $acvalue['month_year_weeck']){ ?>selected <?php
} ?> ><?php echo $montname;?></option>
                                  <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <input aria-label="On The Day Year[]" type="text" class="form-control col-lg-4" name="on_the_day_year[]" value="<?php echo $acvalue['day']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="weeklyschedule" style="<?php echo $acvalue['accured_action_based']=="W"?'':'display:none'; ?>">
                        <div class="row form-group">
                            <span class="form-label col-lg-4  text-center mt-3">Select Week Day</span>
                            <div class="col-lg-8">
                                <select aria-label="On The Week[]" name="on_the_week[]"  style="width:150px;" class="form-control form-select js-example-basic-single">
                                  <?php foreach ($weekdays as $weekkey=>$weekvalue) {    ?>
                                    <option value="<?php echo $weekkey;?>" <?php if($weekkey == $acvalue['month_year_weeck']){ ?>selected <?php
} ?> ><?php echo $weekvalue;?></option>
                                  <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>





                  </div>
                  <div style="clear: both"></div>
                  <div class="col-xl-9 mt-2">
                    <div class="col-xl-4 p-1 group mb-5 form-group" style="float: left;">
                     <span class="form-label ">  Max Accrual  </span>
                     <input aria-label="Max Accured Limit[]" class="form-control" type="text" name="max_accured_limit[]"  value="<?php echo $acvalue['max_accured_limit'];?>" required />


                   </div>
                   <div class="col-xl-4 p-1 form-group" style="float: left;">
                     <span class="form-label ">Carryover </span>
                     <select aria-label="Carry Over Limit[]" class="form-control  form-select js-example-basic-single"  name="carry_over_limit[]">
                      <option value="">Select Any One </option>
                      <?php foreach ($carryover_list as $coakey => $coavalue) { ?>
                        <option value="<?php echo $coakey; ?>" <?php echo $acvalue['carry_over_limit']==$coakey ? 'selected':'';?> ><?php echo $coavalue;?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-xl-4 p-1 form-group" style="float: left;">
                   <span class="form-label ">Carryover Type</span>
                   <select aria-label="Carry Over Limit Type[]" class="form-control selectlist form-select js-example-basic-single"  name="carry_over_limit_type[]">
                    <option value="">Select Any One </option>
                    <?php foreach ($percentage_list as $plkey => $plvalue) { ?>
                      <option value="<?php echo $plkey; ?>" <?php echo $acvalue['carry_over_limit_type']==$plkey ? 'selected':'';?> ><?php echo $plvalue;?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
               <div style="clear: both"></div>
                  <div class="col-xl-9 mt-2">
                    <div class="col-xl-4 p-1 group mb-5 form-group" style="float: left;">
                     <span class="form-label ">  Leave Span  </span>
                     <input aria-label="Leave Span[]" class="form-control" type="text" name="leave_span[]"  value="<?php echo $acvalue['leave_span'];?>" required />
                   </div>
                 </div>


            </div>
          <?php } ?>

          <div class=" col-xl-9 separator separator-dashed my-10"></div>
          <div><button type="button" class="btn btn-success float-right" onclick="addrows()">Add Stage</button></div>

          <div class="col-xl-9"><h4>Accrual Options</h4></div>

          <div class="col-lg-4 mt-5 form-group">
            <span class="form-label ">First Accrualy</span>
            <select aria-label="First Accrual" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_4555"   name="first_accrual">
              <option value="">Select Any One </option>

              <?php foreach ($first_accruals as $key => $value) {
               ?>
               <option value="<?php echo $key;?>" <?php if($key == $ltdata['first_accrual']){ ?> selected <?php
} ?>  ><?php echo $value;?></option>
             <?php } ?>

           </select>
         </div>
         <div class="col-lg-5 mt-5 form-group">
          <span class="form-label ">Accruals Happen </span>
          <select aria-label="Accruals Happen" class="form-control selectlist form-select js-example-basic-single"   name="accruals_happen">
            <option value="">Select Any One </option>
            <?php foreach ($accruals_happen_list as $ahlkey => $ahlvalue) {
             ?>
             <option value="<?php echo $ahlkey;?>" <?php if($ahlkey == $ltdata['accruals_happen']){ ?> selected <?php
} ?>  ><?php echo $ahlvalue;?></option>
           <?php } ?>
         </select>
       </div>
       <div class="form-group col-lg-4 mt-2">
        <span class=""> Encashment </span>
        <input aria-label="Encashment" class="form-control" type="text" name="encashment"  value="<?php echo $ltdata['encashment'];?>" required />


      </div>

      <div class="col-lg-5 mt-2">
        <span>&nbsp;</span>
        <select aria-label="Percentage" class="form-control selectlist form-select js-example-basic-single" id="kt_select2_46"  name="percentage">
         <?php foreach ($percentage_list as $key => $value) {
           ?>
           <option value="<?php echo $key;?>" <?php if($key == $ltdata['percentage']){ ?> selected <?php
} ?>  ><?php echo $value;?></option>
         <?php } ?>
         <option value="">Select Any One </option>
       </select>
     </div>
     <div class=" col-xl-9 separator separator-dashed my-10"></div>
     <div class="col-xl-9"><h4>Policy Restrictions</h4></div>
     <div class=" col-xl-9 form-group">
      <div>
       <span class="">Weekends Between Leave Period:</span>
       <div class="ml-10 mb-2 mt-2">
        <div style="float:left;">
          <div class="form-radio">
            <div class="radio radio-inline">
              <label class="form-label">
                <input aria-label="Radio option" class="form-check-input" style="display:none" type="radio" <?php if($ltdata['weekends_between_leave_period'] == '0'){ ?>checked <?php
} ?> name="weekends_between_leave_period" value='0' />
                <i class="helper"></i> <span> Count as Leave: Count after</span>
              </label>
            </div>
          </div>
        </div>
        <div style="float:left;">
          <input aria-label="Weekend Leave Count" class="form-control" type="text" name="weekend_leave_count"  value="<?php echo $ltdata['weekend_leave_count'];?>" style="width:50px;height:30px;margin-top:-5px" />
        </div><div >&nbsp; Days </div>
        <span class="circle">
          <span class="check"></span>
        </span>
      </div>
    </label>
  </div></br>
  <div class="ml-10">

    <div class="form-radio">
      <div class="radio radio-inline">
        <label class="form-label">
         <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['weekends_between_leave_period'] == '1'){ ?>checked <?php
} ?> name="weekends_between_leave_period" value='1' />
         <i class="helper"></i> <span>Don't count as leave</span>
       </label>
     </div>
   </div>
 </div>
</div>

<div class=" col-xl-9 mt-4">
  <div>
   <span>Holidays Between Leave Period:</span>
   <div class="ml-10 mb-2 mt-2">
    <div style="float:left;">
      <div class="form-radio">
        <div class="radio radio-inline">
          <label class="form-label">
            <input aria-label="Radio option" class="form-check-input" type="radio"  <?php if($ltdata['holidays_between_leave_period'] == '0'){ ?>checked <?php
} ?> name="holidays_between_leave_period" value='0' />
            <i class="helper"></i> <span>Count as Leave: Count after</span>
          </label>
        </div>
      </div>
    </div>
    <div style="float:left;">
      <input aria-label="Holiday Leave Count" class="form-control" type="text" name="holiday_leave_count"  value="<?php echo $ltdata['holiday_leave_count'];?>" style="width:50px;height:30px;margin-top:-5px" />
    </div><div >&nbsp; Days </div>
    <span class="circle">
      <span class="check"></span>
    </span>
  </div>
</label>
</div></br>
<div class="ml-10">
  <div class="form-radio">
    <div class="radio radio-inline">
      <label class="form-label">
       <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['holidays_between_leave_period'] == '1'){ ?>checked <?php
} ?> name="holidays_between_leave_period" value='1' />
       <i class="helper"></i> <span>Don't count as leave</span>
     </label>
   </div>
 </div>

</div>
</div>





                        <div class=" col-xl-9 mt-4">
                          <span>While Applying Leave,Exceed Leave Blance: </span>
                          <div class="ml-10 mt-2">





                            <div class="col-sm-12">

                              <div class="form-radio">

                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['exceed_leave_balance'] == '0'){ ?>checked <?php
} ?> name="exceed_leave_balance" value='0' />
                                    <i class="helper"></i>Don't Allow
                                  </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                   <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['exceed_leave_balance'] == '1'){ ?>checked <?php
} ?> name="exceed_leave_balance" value='1' />
                                   <i class="helper"></i>Allow
                                 </label>
                               </div>
                               <div class="radio radio-inline">
                                <label class="form-label">
                                 <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['exceed_leave_balance'] == '2'){ ?>checked <?php
} ?> name="exceed_leave_balance" value='2' />
                                 <i class="helper"></i> Allow and Mark as LOP
                               </label>
                             </div>


                           </div>
                         </div>


                       </div>

                     </div>

                     <div class=" col-xl-9 mt-4">
                       <span>Duration(s) Allowed:</span>
                       <div class="ml-10 mb-2 mt-2">
                        <div style="float:left;">
                         <div class="form-radio">
                           <div class="radio radio-inline">
                            <label class="form-label">
                              <input aria-label="Radio option" class="form-check-input" type="radio"  <?php if($ltdata['duration_allowed'] == '0'){ ?>checked <?php
} ?> name="duration_allowed" value='0' />
                              <i class="helper"></i> Count as Leave: Count after
                            </label>
                          </div>
                        </div>
                      </div>
                      <div style="float:left;">
                        <input aria-label="Duration Leave Count" class="form-control" type="text" name="duration_leave_count"  value="<?php echo $ltdata['duration_leave_count'];?>" style="width:50px;height:30px;margin-top:-5px" />
                      </div><div >&nbsp; Days </div>
                      <span class="circle">
                        <span class="check"></span>
                      </span>
                    </div>
              </br>
                <div class="ml-10">
                 <div class="form-radio">
                   <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Radio option" class="form-check-input" type="radio" <?php if($ltdata['duration_allowed'] == '1'){ ?>checked <?php
} ?> name="duration_allowed" value='1' />
                      <i class="helper"></i> Don't count as leave
                    </label>
                  </div>
                </div>
              </div>
            </div>
              <div class=" col-xl-9 mt-4">
                       <span>Half Day Allowed:</span>
                       <div class="ml-10 mb-2 mt-2">
                        <div style="float:left;">
                              <input aria-label="Checkbox" type="checkbox" class="js-switch" <?php if($ltdata['half_day'] == '0'){ ?>checked <?php
} ?> name="half_day" value="0">
                      </div>
                    </div>
                  </div>

            <div class=" col-xl-9 separator separator-dashed my-10"></div>
            <div class="col-xl-9 mt-3"><h4>Leave Applicable To</h4>  <button type="button" class="btn btn-outline-secondary btn-success " onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Add Applicable Criteria</button></div>
                      </div>
                      <button name="Save" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 mb-5 m-t-50">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

<div id="kt_modal_dynamic_popup_task_setting" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="dynamic_popup_task_settings">
        </div>
    </div>
</div>
