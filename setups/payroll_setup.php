<?php
$weekdays = array('Sunday'=>'Sunday','Monday'=>'Monday','Tuesday'=>'Tuesday','Wednesday'=>'Wednesday','Thursday'=>'Thursday','Friday'=>'Friday','Saturday'=>'Saturday');
$deduction_cycle = array('M'=>'Monthly','Q'=>'Quaterly','H'=>'Half Yearly','Y'=>'Yearly');
$statelist = $result['states'];
$attendance_settings = $result['attendance_settings'];
$companies = $result['companies'];
$config = $result['config'];
$emp_state_insaurance = $result['emp_state_insaurance'];
$professional_tax = $result['professional_tax'];
$emp_provident_funds = $result['emp_provident_funds'];
$labour_welfare_fund = $result['labour_welfare_fund'];
$locations =  $result['locations'];
$earnings_componentlist = $result['earnings_componentlist'];
$deduction_componentlist = $result['deduction_componentlist'];
$other_earnings_componentlist = $result['other_earnings_componentlist'];
//echo "nextseqstartnumber - ".$nextseqstartnumber;
//echo "<pre>"; print_r($result); echo "</pre>";
?>
<script type="text/javascript">
function  removeslabrows(argument) {
     $(".itemRow:checked").each(function() {
           console.log(this);
           $(this).closest('tr').remove();
     });
     $('#checkAll').prop('checked', false);
}
function addslabrows(){
    jQuery(document).ready(function() {
         KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>';
   htmlRows += '<td><div class="checkbox-fade fade-in-primary m-0"><label class="form-label m-0"><input aria-label="Checkbox" type="checkbox" value="" class="itemRow d-none"><span class="cr m-0"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td>';
   htmlRows += '<td>       <input aria-label="Start Range[]" type="number" class="form-control text-right" autocomplete="off" name="start_range[]" >  </td><td><input aria-label="End Range[]" type="number" class="form-control text-right"  autocomplete="off" name="end_range[]" ></td>  <td><input aria-label="Tax Amount[]" class="form-control text-right" autocomplete="off" type="text" name="tax_amount[]" > </td>';
   htmlRows += '</tr>';
   $('#slabtable').append(htmlRows);
   //     $( ".datepicker" ).datepicker({
   //        showOn: "both",
   //        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
   //        dateFormat: date_format,
   //        setDate: new Date(),
   //        changeMonth: true,
   //        changeYear: true,
   //        // beforeShow: function (input, inst) {
   //        //         var rect = input.getBoundingClientRect();
   //        //         setTimeout(function () {
   //        //             inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
   //        //         }, 0);
   //        //     }
   //    });


}
</script>
<div id="setup<?php echo $pagedetail[0]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[0]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">PayRoll Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button"  onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>Start PayRoll Setup</button>
                    </div>
                </div>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $previousseqnumber; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>  Start PayRoll Setup</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center position-relative">
            <div class="top-logo p-2">
                <a href="">
                    <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                </a>
            </div>
      <form id="submit_form_id_<?php echo $pagedetail[0]['sequence']; ?>" enctype='multipart/form-data'>
        <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['0']['sequence']; ?>">
      </form>
            <div class="text-position text-start">
                <span class="f-30 f-w-600">Setup</span>
                <h2 class="f-50 f-w-600">PayRoll </br> Setup</h2>
                <p class="f-14 f-w-600"><?php echo $system_message['welcome_tab']['description']; ?></p>
                <button class="btn btn-primary">infistaq.com</button>
            </div>
            <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[1]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[1]['sequence'])?'':'style="display:none;"'; ?> >
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Attendance Settings <small class="text-danger f-10 d-block">* Any Data Not Show Please Refrash The Page</small></h3>
                    <form id="submit_form_id_<?php echo $pagedetail[1]['sequence']; ?>" enctype='multipart/form-data'>
            <div class="row mb-5">
              <div class="form-group col-lg-12 mt-2">
                  <span class="form-label">Select days you worked in a calendar week.</span>
                  <?php $working_days = json_decode($attendance_settings['working_days'],1); ?>
                <select aria-label="Working Days[]" class="selectlist form-select js-example-basic-multiple"  name="working_days[]" multiple="multiple">
                      <?php foreach($weekdays as $weekkey=>$weekvalue) {    ?>
                   <option value="<?php echo $weekkey;?>" <?php if(in_array($weekkey, (array)$working_days)){ ?>selected <?php
} ?> ><?php echo $weekvalue;?></option>
                  <?php } ?>
                  </select>
              </div>
              <div class="form-group col-lg-6 mt-2">
                  <span class="form-label">Minimum Working Hours In A Day <span class="requried">*</span></span>
                  <div class="input-group input-icon-right">
                      <input aria-label="Minimum Hours Required" type="text" class="form-control hourandminut" data-mask="99:99" name="minimum_hours_required" value="<?php echo $attendance_settings['minimum_hours_required']; ?>">
                  </div>
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Maximum Hours Cap</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['maximum_hours_cap']; ?>" name="maximum_hours_cap">
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Full Day Working Hours</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['full_day_hours']; ?>" name="full_day_hours">
              </div>
               <div class="form-group col-lg-6">
                <span class="form-label">Half Day Working Hours</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['half_day_hours']; ?>" name="half_day_hours">
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Break Time</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['break_time']; ?>" name="break_time">
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Grace Perieod</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['grace_perieod']; ?>" name="grace_perieod">
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Entry Grace Perieod</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['entry_grace_perieod']; ?>" name="entry_grace_perieod">
              </div>
              <div class="form-group col-lg-6">
                <span class="form-label">Exit Perieod</span>
                <input aria-label="Input field" class="form-control hourandminut" type="text" value="<?php echo $attendance_settings['exit_perieod']; ?>" name="exit_perieod">
              </div>
              <div class="form-group col-lg-12 mt-2">
                  <span class="form-label">Locations</span>

                <select aria-label="Locations[]" class="selectlist form-select js-example-basic-multiple"  name="locations[]" multiple="multiple">
                      <?php foreach($locations as $loction){   ?>
                   <option value="<?php echo $loction['id'];?>" selected><?php echo $loction['location_name'];?></option>
                  <?php } ?>
                  </select>
              </div>

            </div>
            <input type="hidden" name="transaction_table" value="attendance_settings">
            <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['1']['sequence']; ?>">
          </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail['0']['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail['2']['sequence']; ?>','<?php echo $pagedetail['1']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center position-relative">
                <div class="top-logo p-2">
                    <a href="">
                        <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                    </a>
                </div>
                <div class="text-position text-start">
                    <span class="f-30 f-w-600">PayRoll Setup</span>
                    <h2 class="f-50 f-w-600">PayRoll Information</h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[2]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[2]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Statutory Components</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[2]['sequence']; ?>" enctype='multipart/form-data'>
                        <div class="row mb-5">
                            <div class="card-block accordion-block">
                                  <div id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="accordion-panel">
                                          <div class="accordion-heading" role="tab" id="headingOneEpf">
                                            <h3 class="card-title accordion-title">
                                                  <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse"data-parent="#accordion" href="#collapseOnedd"aria-expanded="true" aria-controls="collapseOne" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Employee's Provident Fund (EPF)</a>
                                              </h3>
                                        </div>
                                        <div id="collapseOnedd" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOneEpf">
                                            <div class="accordion-content accordion-desc">
                                               <div class="card-body">
                                                     <div class="form-group row">
                                                         <div class="form-group col-lg-6">
                                                            <span>EPF Number</span>
                                                            <div class="input-group input-icon-right">
                                                              <input aria-label="Epf Number" type="text" class="form-control"  name="epf_number" value="<?php echo $emp_provident_funds['epf_number'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                            <span>Deduction Cycle</span>
                                                            <select aria-label="Epf Deduction Cycle"  class="form-control form-select js-example-basic-single" name="epf_deduction_cycle" >
                                                             <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                                                               <option value="<?php echo $ckey; ?>" <?php echo $emp_provident_funds['deduction_cycle'] == $ckey?"selected":""; ?>><?php echo $cycle ?></option>
                                                             <?php } ?>
                                                           </select>
                                                        </div>
                                                         <div class="form-group col-lg-6">
                                                              <span>Employee Contribution (%):</span>
                                                              <div class="input-group input-icon-right">
                                                                <input aria-label="Epf Employee Contribution" type="text" class="form-control"  name="epf_employee_contribution" value="<?php echo $emp_provident_funds['employee_contribution'];?>">
                                                              </div>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                              <span>Employer Contribution (%):</span>
                                                              <div class="input-group input-icon-right">
                                                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo $emp_provident_funds['employer_contribution'];?>"  name="epf_employer_contribution">
                                                              </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                            </div>
                                          </div>
                                    </div>
                                    <div class="accordion-panel">
                                          <div class="accordion-heading" role="tab" id="headingOneEsi">
                                            <h3 class="card-title accordion-title"><a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseOne"aria-expanded="true" aria-controls="collapseOne" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Employee's State Insurance (ESI) </a></h3 onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOneEsi">
                                              <div class="accordion-content accordion-desc">
                                                   <div class="card-body">
                                                    <div class="form-group row">
                                                          <div class="form-group col-lg-6">
                                                            <span>ESI Number</span>
                                                            <div class="input-group input-icon-right">
                                                                 <input aria-label="Input field" type="text" class="form-control" value="<?php echo $emp_state_insaurance['esi_no'];?>" name="esi_no">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                            <span>Deduction Cycle</span>
                                                                  <select aria-label="Esi Deduction Cycle"  class="form-select js-example-basic-single" name="esi_deduction_cycle" >
                                                                   <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                                                                     <option value="<?php echo $ckey; ?>" <?php echo $emp_state_insaurance['deduction_cycle'] == $ckey?"selected":""; ?>><?php echo $cycle ?></option>
                                                                   <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                            <span>Employee Contribution (%):</span>
                                                            <div class="input-group input-icon-right">
                                                              <input aria-label="Esi Employee Contribution" type="text" class="form-control"  name="esi_employee_contribution" value="<?php echo $emp_state_insaurance['employee_contribution'];?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                            <span>Employer Contribution (%):</span>
                                                            <div class="input-group input-icon-right">
                                                                  <input aria-label="Input field" type="text" class="form-control"  value="<?php echo $emp_state_insaurance['employer_contribution'];?>" name="esi_employer_contribution">
                                                            </div>
                                                        </div>
                                                    </div>
                                                 </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-panel">
                                          <div class="accordion-heading" role="tab" id="headingTwo">
                                            <h3 class="card-title accordion-title"> <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Professional Tax (PT)</a> </h3 onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                              <div class="accordion-content accordion-desc">
                                                <div class="card-body">
                                                      <div class="form-group row">
                                                        <div class="form-group col-lg-6">
                                                              <span>PT Number</span>
                                                              <div class="input-group input-icon-right">
                                                                <input aria-label="Pt Number" type="text" class="form-control"  name="pt_number" value="<?php echo $professional_tax['pt_number'];?>">
                                                              </div>
                                                        </div>
                                                        <div class="form-group col-lg-6 w-full">
                                                            <span>Work Location</span>
                                        <select aria-label="Work Location"  class="form-select js-example-basic-single" name="work_location_id" >
                                            <?php foreach ($locations as $loction)  { ?>
                                              <option value="<?php echo $loction['id']; ?>" <?php echo $professional_tax['work_location_id'] == $loction['id']?"selected":""; ?>><?php echo $loction['location_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                                        </div>
                                                     <div class="form-group col-lg-6">
                                                              <input type="hidden" name="pt_state_id" id="pt_state_id">
                                                              <span>State</span>
                                                              <select aria-label="Pt State"  class="form-select js-example-basic-single" name="pt_state_id" >
                                                                   <?php foreach ($statelist as $statevalue)  { ?>
                                                                 <option value="<?php echo $statevalue['id']; ?>" <?php echo $professional_tax['state_id'] == $statevalue['id']?"selected":""; ?>><?php echo $statevalue['state_name']; ?></option>
                                                                   <?php } ?>
                                                              </select>
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                             <span>Deduction Cycle</span>
                                                             <select aria-label="Pt Deduction Cycle"  class="form-select js-example-basic-single" name="pt_deduction_cycle" >
                                                                   <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                                                                 <option value="<?php echo $ckey; ?>" <?php echo $professional_tax['deduction_cycle'] == $ckey?"selected":""; ?>><?php echo $cycle ?></option>
                                                                   <?php } ?>
                                                              </select>
                                                        </div>
                                                        <div class="form-group col-lg-12">

                                                            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="slabtable">
                                                                  <tr>
                                                                       <th scope="col" style="width: 1%;"></th>
                                                                       <th scope="col" class="" style="width: 33%;"> Start Range</th>
                                                                       <th scope="col" class="thheader" style="width: 33%;">End Range</th>
                                                                       <th scope="col" class="thheader" style="width: 33%;">Tax Amount</th>
                                                                 </tr>
                                          <?php
                                          $slabs = json_decode($professional_tax['slabs'],1);
                                          if(count((array)$slabs) ==0){
                                            $slabs[] = array();
                                          }
                                          foreach($slabs as $slab){ ?>
                                                                 <tr>
                                                                   <td>
                                                                        <div class="checkbox-fade fade-in-primary m-0">
                                                                          <label class="form-label m-0">
                                                                            <input aria-label="Checkbox" type="checkbox" value="" class="itemRow d-none">
                                                                            <span class="cr m-0">
                                                                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                                            </span>
                                                                          </label>
                                                                        </div>
                                                                    </td>
                                                                  <td>
                                                                        <input aria-label="Start Range[]" type="number" class="form-control text-right" autocomplete="off" name="start_range[]" value="<?php echo $slab['start_range']; ?>">
                                                                  </td>
                                                                  <td>
                                                                           <input aria-label="End Range[]" type="number" class="form-control text-right" autocomplete="off" name="end_range[]" value="<?php echo $slab['end_range']; ?>">
                                                                              </td>
                                                                   <td>
                                                                     <input aria-label="Tax Amount[]" type="text" class="form-control text-right" autocomplete="off" name="tax_amount[]" value="<?php echo $slab['tax_amount']; ?>">
                                                                </td>
                                                            </tr>
                                    <?php } ?>
                                                    </table>
                                                    <div class="form-group ">
                                                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                                                           <button class="btn btn-success"  onclick="addslabrows()" id="addRows" type="button">+ Add More</button>
                                                           <button class="btn btn-danger mt-2" id="removeRows" onclick="removeslabrows()"  type="button">- Delete</button> &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-panel">
                                <div class=" accordion-heading" role="tab" id="headingThree">
                                    <h3 class="card-title accordion-title">
                                    <a class="accordion-msg waves-effect waves-dark" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseThree"aria-expanded="false"aria-controls="collapseThree" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Labour Welfare Fund (LWF)  </a></h3 onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="accordion-content accordion-desc">
                                        <div class="card-body">
                                              <div class="form-group row">
                                                <div class="form-group col-lg-6">
                                                      <span>State</span>
                                                      <select aria-label="Lwf State" class="form-select js-example-basic-single" name="lwf_state_id" >
                                                        <option> Select Any One State</option>
                                                        <?php foreach ($statelist as $statevalue)  { ?>
                                                         <option value="<?php echo $statevalue['id']; ?>" <?php echo $labour_welfare_fund['state_id'] == $statevalue['id']?"selected":""; ?>><?php echo $statevalue['state_name']; ?></option>
                                                       <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                      <span>Deduction Cycle</span>
                                                    <select aria-label="Lwf Deduction Cycle"  class="form-control form-select js-example-basic-single" name="lwf_deduction_cycle" >
                                                       <?php foreach ($deduction_cycle as $ckey=>$cycle)  { ?>
                                                         <option value="<?php echo $ckey; ?>" <?php echo $labour_welfare_fund['deduction_cycle'] == $ckey?"selected":""; ?>><?php echo $cycle ?></option>
                                                       <?php } ?>
                                                      </select>
                                                  </div>
                                                <div class="form-group col-lg-6">
                                                    <span>Employer Contribution Amount</span>
                                                    <div class="input-group input-group-button ">
                                                      <button class="btn btn-info" type="button" onclick="deloptionrows('s1rowid1')">RS.</button>
                                                      <input aria-label="Lwf Employer Contribution" type="text" class="form-control width1 text-right"  name="lwf_employer_contribution" value="<?php echo $labour_welfare_fund['employer_contribution'];?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                   <input type="hidden" name="transaction_table" value="statutory_components">
            <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['2']['sequence']; ?>">
                </form>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $pagedetail[1]['sequence']; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview(<?php echo $pagedetail[3]['sequence']; ?>,'<?php echo $pagedetail['2']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center position-relative">
            <div class="top-logo p-2">
                <a href="">
                    <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                </a>
            </div>
            <div class="text-position text-start">
                <span class="f-30 f-w-600">HR Setup</span>
                <h2 class="f-50 f-w-600">HR Information</h2>
                <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                <button class="btn btn-primary">infistaq.com</button>
            </div>
            <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
        </div>
        </div>
    </div>
</div>













<div id="setup<?php echo $pagedetail[3]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[3]['sequence'])?'':'style="display:none;"'; ?> >
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Statutory Components</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[3]['sequence']; ?>" enctype='multipart/form-data'>
                        <ul class="nav md-tabs mt-2 infisuite_tab">
                              <li class="nav-item" style="display:block;" >
                                <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Earnings</a>
                              </li>
                              <li class="nav-item" style="display:block;" >
                                <a class="nav-link"  data-bs-toggle="tab" href="#kt_tab_pane_2" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Deductions</a>
                              </li>
                              <li class="nav-item" style="display:block;" >
                                <a class="nav-link"  data-bs-toggle="tab" href="#kt_tab_pane_3" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Static</a>
                              </li>
                        </ul>

                        <div class="tab-content mt-5" id="myTabContent">
                              <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_1">
                                <div class="text-right">
                                  <!--     <button type="button" onclick="showpopup('Earning','','system_earnings');" class="btn btn-success waves-effect waves-light"><i class="ti-plus"></i> New Earning</button> -->
                                </div>
                                <div class="row mt-2">
                                      <div class="col-xl-5">
                                        <h4 class="font-weight-bold text-dark">Component Name</h4>
                                      </div>
                                      <div class="col-xl-2">
                                        <h4 class="font-weight-bold text-dark"> Type</h4>
                                      </div>
                                      <div class="col-xl-5">
                                        <h4 class="mb-10 font-weight-bold text-dark">Calculation</h4>
                                      </div>
                                </div>

                                <div class="scroll-sec">
                                        <?php
                                        foreach ($earnings_componentlist as $scvalue) {

                                          ?>

                                      <div class="row" id="componentid_<?php echo $scvalue['id'];?>">
                                        <div class="col-xl-5 " >
                                              <div class="checkbox-fade fade-in-primary">
                                                <label class="form-label">
                                                  <input aria-label="Checkbox" class="itemRow d-none" type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist1[]" value="<?php echo $scvalue['id'];?>" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span style="display: table;"><a href="#"  onclick="showpopup('Earning','<?php echo $scvalue['id'];?>','system_earnings');">
                                                  <span class="f-12"><?php echo strtoupper($scvalue['name']); ?></span></a></span></label>
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <span class="f-12"><?php echo strtoupper($scvalue['code']); ?></span>
                                        </div>
                                        <div class="col-xl-5">
                                            <span class="f-12">
                                                  <?php
                                                  $scvalue['varible_pay']=='0' ?
                                                  $display_text_calculationtype = "Variable; " : $display_text_calculationtype = "Fixed; ";
                                                  if($scvalue['calculation_type']=='A')  {
                                                    $display_text_calculationtype .= "Flat Amount";
                                                  }
                                                  if($scvalue['calculation_number']!=0) {
                                                    if($scvalue['calculation_type']=='P')  {
                                                      $display_text_calculationtype .= $scvalue['calculation_number']."% of ".$scvalue['calculation_category'];
                                                    }else{
                                                      $display_text_calculationtype .=' of '.$scvalue['calculation_number'];
                                                    }
                                                  }
                                                  echo strtoupper($display_text_calculationtype); ?>
                                                </span>
                                              </div>
                                        </div>
                                        <?php
                                      } ?>
                                 </div>
                              </div>
                                    <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                         <div class="text-right">
                                          <!--     <button type="button" onclick="showpopup('Deduction','','system_earnings');" class="btn btn-success waves-effect waves-light"><i class="ti-plus "></i> New Deduction</button> -->
                                        </div>
                                        <div class="row mt-2">
                                              <div class="col-xl-5">
                                                <h4 class=" font-weight-bold text-dark">Component Name</h4>
                                              </div>
                                              <div class="col-xl-2">
                                                <h4 class=" font-weight-bold text-dark"> Type</h4>
                                              </div>
                                              <div class="col-xl-5">
                                                <h4 class="font-weight-bold text-dark">Calculation </h4>
                                              </div>
                                        </div>
                                        <?php
                                        foreach ($deduction_componentlist as $scvalue) {
                                          ?>
                                          <div class="row" id='componentid_<?php echo $scvalue['id'];?>' >
                                            <div class="col-xl-5" id="componentid_<?php echo $scvalue['id'];?>">
                                                  <div class="checkbox-fade fade-in-primary">
                                                <label class="form-label">
                                                  <input aria-label="Checkbox" type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist1[]" value="<?php echo $scvalue['id'];?>"  class="d-none">
                                                  <span class="cr">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                  </span>
                                                  <span style="display: table;">  <a href="#" onclick="showpopup('Deduction','<?php echo $scvalue['id'];?>','system_earnings');">
                                                    <span class="f-12"><?php echo strtoupper($scvalue['name']); ?></span> </a></span>
                                                  </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-2">
                                                <span class="f-12"><?php echo strtoupper($scvalue['code']); ?></span>
                                            </div>
                                            <div class="col-xl-5">
                                                <span class="f-12">
                                                  <?php   $scvalue['varible_pay']=='0' ?
                                                  $display_text_calculationtype = "Variable; " : $display_text_calculationtype = "Fixed; ";

                                                  if($scvalue['calculation_type']=='A')  {
                                                    $display_text_calculationtype .= "Flat Amount";
                                                  }

                                                  if($scvalue['calculation_number']!=0) {
                                                    if($scvalue['calculation_type']=='P')  {
                                                      $display_text_calculationtype .= $scvalue['calculation_number']."% of ".$scvalue['calculation_category'];
                                                    }else{
                                                      $display_text_calculationtype .=' of '.$scvalue['calculation_number'];

                                                    }

                                                  }
                                                  echo strtoupper($display_text_calculationtype); ?>
                                                </span>
                                              </div>
                                            </div>
                                            <?php
                                          } ?>
                                        </div>
                                        <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                                         <div class="text-right">
                                         <!--  <a href="#"onclick="showreimburse('Reimbursement','','system_other_earnings');" class="btn btn-success waves-effect waves-light" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <i class="ti-plus"></i> New Reimbursement
                                          </a> -->
                                        </div>
                                        <div class="row mt-2">
                                          <div class="col-xl-5">
                                            <h4 class=" font-weight-bold text-dark">Component Name</h4>
                                          </div>
                                          <div class="col-xl-2">
                                            <h4 class=" font-weight-bold text-dark">Type</h4>
                                          </div>
                                          <div class="col-xl-5">
                                            <h4 class="font-weight-bold text-dark">Maximum Amount</h4>
                                          </div>
                                        </div>
                                        <?php
                                        foreach ($other_earnings_componentlist as $scvalue) {
                                          ?>
                                          <div class="row" id="rcomponentid_<?php echo $scvalue['id'];?>">
                                           <div class="col-xl-5" >
                                                <div class="checkbox-fade fade-in-primary">
                                                  <label class="form-label">
                                                   <input aria-label="Checkbox" type="checkbox" <?php echo $scvalue['is_active']=='0'?'checked':'';?> name="selectedcomponentlist1[]" value="<?php echo $scvalue['id'];?>" class="d-none"/>
                                                   <span class="cr">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                  </span>
                                                  <span style="display: table;">   <a href="#" onclick="showreimburse('Reimbursement','<?php echo $scvalue['id'];?>','system_other_earnings');">
                                                    <span class="f-12"><?php echo strtoupper($scvalue['earning_name']); ?></span> </a></span>
                                                  </label>
                                                </div>
                                              </div>
                                              <div class="col-xl-2">
                                                <span class="f-12"><?php echo strtoupper($scvalue['system_code']); ?></span>
                                              </div>
                                              <div class="col-xl-5">
                                                <span class="f-12"> <?php echo strtoupper($scvalue['earning_upto_amount']) . " Per month";?></span>
                                              </div>
                                            </div>
                                            <?php
                                          } ?>
                                        </div>
                                      </div>
                        <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['3']['sequence']; ?>">
            <input type="hidden" name="transaction_table" value="system_earnings">

                    </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $pagedetail[2]['sequence']; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
            <!-- <button type="button"  class="btn btn-primary" onclick="nextsetupview(<?php //echo $pagedetail[4]['sequence']; ?>,'<?php //echo $pagedetail['3']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button> -->
            <button type="button"  class="btn btn-primary "   onclick="nextsetupview('<?php echo $nextseqstartnumber; ?>','<?php echo $pagedetail[3]['sequence']; ?>')"><i class="ti-arrow-right"></i>  <?php echo $nextseqstartnumber !=""?"Next Step":"Finish"; ?></button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center position-relative">
                <div class="top-logo p-2">
                    <a href="">
                        <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                    </a>
                </div>
                <div class="text-position text-start">
                    <span class="f-30 f-w-600">HR Setup</span>
                    <h2 class="f-50 f-w-600">HR Information</h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>
  <script src="assets/pages/form-masking/inputmask.js"></script>
  <script src="assets/pages/form-masking/jquery.inputmask.js"></script>
  <script src="assets/pages/form-masking/autoNumeric.js"></script>
  <script src="assets/pages/form-masking/form-mask.js"></script>
