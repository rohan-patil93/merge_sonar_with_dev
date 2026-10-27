<?php
$weekdays = array('Sunday'=>'Sunday','Monday'=>'Monday','Tuesday'=>'Tuesday','Wednesday'=>'Wednesday','Thursday'=>'Thursday','Friday'=>'Friday','Saturday'=>'Saturday');
$deduction_cycle = array('M'=>'Monthly','Q'=>'Quaterly','H'=>'Half Yearly','Y'=>'Yearly');
$statelist = $result['states'];
$attendance_settings = $result['attendance_settings'];
$companies = $result['companies'];
$config = $result['config'];
$verticals = $result['verticals'];
$locations = $result['locations'];
?>
<script type="text/javascript">
    var maxverticals = 100;
    function addMoreVerticals(){
        maxverticals = maxverticals+1;
        var id = 'vertical_'+maxverticals;
        var html ="";
        html += '<div class="row mt-3" id="'+id+'">';
        html += '<div class="col-lg-6"><input aria-label="Vertical Name[]" type="text" class="form-control" name="vertical_name[]" value="<?php echo $wareh['vertical_name']; ?>"></div>';
        html += '<div class="col-lg-5"><textarea aria-label="Vertical Description[]" class="form-control" name="vertical_description[]" value="<?php echo $wareh['vertical_description']; ?>"></textarea></div>';
        html += '<div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''+id+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>';
        html += '</div>';
        $("#newaddmorevertical").append(html);
    }


</script>
<div id="setup<?php echo $pagedetail[0]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[0]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">HR Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button"  onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>Start HR Setup</button>
                    </div>
                </div>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $previousseqnumber; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>  Start HR Setup</button>
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
                <h2 class="f-50 f-w-600">HR</br> Setup</h2>
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
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $pagedetail['0']['sequence']; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
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
<div id="setup<?php echo $pagedetail[2]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[2]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Vertical</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[2]['sequence']; ?>" enctype='multipart/form-data'>
                    <div class="row" >
                        <div class="col-lg-6">
                            <span class="form-label">Vertical Name</span>
                        </div>
                        <div class="col-lg-5">
                            <span class="form-label">Description</span>
                        </div>

                    </div>
                    <div id="newaddmorevertical">
                            <?php $im=1;
                        if(count((array)$verticals) =='0'){
                            $verticals[] = array();
                        }
                             foreach($verticals as $wareh){
                                 $rowid = 'vertical_'.$im;  ?>
                    <div class="row mt-3" id="<?php echo $rowid; ?>">
                        <div class="col-lg-6">
                            <input aria-label="Vertical Name[]" type="text" class="form-control" name="vertical_name[]" value="<?php echo $wareh['vertical_name']; ?>">
                        </div>
                        <div class="col-lg-5">
                            <textarea aria-label="Vertical Description[]" class="form-control" name="vertical_description[]"><?php echo $wareh['vertical_description']; ?></textarea>
                        </div>
                        <div class="col-lg-1 ">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>
                    </div>
                <?php $im++;
} ?>
                    </div>
                    <div class="mt-3 mb-5"  >
                        <button type="button"  class="btn btn-primary" onclick="addMoreVerticals()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="verticals">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['2']['sequence']; ?>">
                </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail['1']['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $nextseqstartnumber; ?>','<?php echo $pagedetail['2']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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
                    <span class="f-30 f-w-600">Setup</span>
                    <h2 class="f-50 f-w-600">Vertical</h2>
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
