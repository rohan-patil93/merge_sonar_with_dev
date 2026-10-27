<div class="card">
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab">
            <?php if($crm_history_id !=""){ ?>
            <li class="nav-item">
                <a class="nav-link active removeallactivetab" data-bs-toggle="tab" href="#comment" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Note</a>
            </li>
            <?php }else{ ?>
            <li class="nav-item">
                <a class="nav-link active  activehomecomments removeallactivetab" data-bs-toggle="tab" href="#comment" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Comment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#wait" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Wait</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#call" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Call</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#sms" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">SMS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#email" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">E-mail</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#task" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Task</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#meeting" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Meeting</a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab " data-bs-toggle="tab" href="#visit" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Visit</a>
            </li>
        <?php } ?>
            <li class="nav-item " id="feasibilityWrapper" <?php echo $transaction['feasibility_check'] =="Y"?'':'style="display: none;"'; ?>>
                <a class="nav-link removeallactivetab activefeasbility" data-bs-toggle="tab" href="#feasibility_type" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Feasibility Type</a>
            </li>
        </ul>
        <div class="tab-content tabs card-block mt-3">
            <?php if($crm_history_id !=""){ ?>
            <div class="tab-pane active removeallactivetab" id="comment" role="tabpanel">
                <form id="form_comment">
                    <div class="row">
                    <?php
                    if($task_crm_history['lead_type'] == '2' || $task_crm_history['lead_type'] == '3'){ ?>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Date</span>
                            <span><?php echo $task_crm_history['date']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Time</span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Comment</span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                   <?php  }elseif($task_crm_history['lead_type'] == '7' || $task_crm_history['lead_type'] == '6'){ ?>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Date</span>
                            <span class="form-label"><?php echo $task_crm_history['date']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Time</span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Comment</span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <span class="form-label d-block">Members</span>
                            <?php
                            $member = json_decode($task_crm_history['member'],1);
                            $rr = 1;
                            foreach($member as $members){ ?>
                                <span class="d-block"><?php echo $rr.") ".$_SESSION['users'][$members]; ?></span>
                            <?php $rr++;
} ?>
                        </div>
                   <?php }elseif($task_crm_history['lead_type'] == '8'){ ?>
                    <div class="col-lg-4">
                            <span class="form-label d-block">Comment</span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block">Date</span>
                            <span class="form-label"><?php echo $task_crm_history['date']; ?></span>
                        </div>

                        <div class="col-lg-4">
                            <span class="form-label d-block">Time</span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4 mt-2">
                            <span class="form-label d-block">Address</span>
                            <span class="form-label"><?php echo $task_crm_history['address']; ?></span>
                        </div>
                        <div class="col-lg-4 mt-2">
                            <span class="form-label d-block">Post Code</span>
                            <span class="form-label"><?php echo $task_crm_history['post_code']; ?></span>
                        </div>

                   <?php } ?>
                    </div>
                    <input type="hidden" name="rid" value="<?php echo $task_crm_history['id']; ?>">
                    <div class="form-group mt-4">
                        <span class="form-label">Note <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_comment')">Submit</button>
                    </div>
                </form>
            </div>
            <?php } ?>
            <div class="tab-pane <?php if($crm_history_id ==""){ ?>active
            <?php } ?> activehomecomments removeallactivetab" id="comment_2" role="tabpanel">
                <form id="form_comment_2">
                <input type="hidden" name="lead_type" value="1">
                <div class="form-group">
                    <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                    <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_comment')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="wait" role="tabpanel">
                <form id="form_wait">
                <input type="hidden" name="lead_type" value="2">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Date <span class="requried text-danger">*</span></span>
                        <input aria-label="Date" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Time <span class="requried text-danger">*</span></span>
                        <input aria-label="Time" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_wait')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="call" role="tabpanel">
                <form id="form_call">
                <input type="hidden" name="lead_type" value="3">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Date <span class="requried text-danger">*</span></span>
                        <input aria-label="Date" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Time <span class="requried text-danger">*</span></span>
                        <input aria-label="Time" class="form-control" type="time" name="time"  value="">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_call')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="sms" role="tabpanel">
                <form id="form_sms">
                <input type="hidden" name="lead_type" value="4">
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Mobile No. <span class="requried text-danger">*</span></span>
                        <input aria-label="Phone" class="form-control" type="text" name="phone" value="<?php echo $transaction['phone']; ?>">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">SMS Text <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Sms Text" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="sms_text"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_sms')">Submit</button>
                    <span class="text-info f-12">*SMS service is not activated, so SMS could not be sent.</span>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="email" role="tabpanel">
                <form id="form_email">
                <input type="hidden" name="lead_type" value="5">
                <div class="row">
                    <div class="col-lg-6 form-group w-full">
                        <span class="form-label">Email Template <span class="requried text-danger">*</span></span>
                        <select aria-label="Email Content" class="form-control js-example-basic-single resetmulyiselect" name="email_content_id" >

                        <?php foreach($email_content as $cval){ ?>
                            <option value="<?php echo $cval['template_reference_id']; ?>"><?php echo $cval['template_name']; ?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label d-block">Recipient Email <span class="requried text-danger">*</span></span>
                        <span class="errorshowcrmcomment text-danger"></span>
                        <input aria-label="Recipient Email" class="form-control" type="text" name="recipient_email_id" value="<?php echo $transaction['email_id']; ?>">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Email Content <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="8" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_email')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="task" role="tabpanel">
                <form id="form_task">
                    <input type="hidden" name="lead_type" value="6">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Date <span class="requried text-danger">*</span></span>
                        <input aria-label="Date" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Time <span class="requried text-danger">*</span></span>
                        <input aria-label="Time" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group w-full">
                        <span class="form-label">User <span class="requried text-danger">*</span></span>
                        <select aria-label="Member[]" class="form-control js-example-basic-single resetmulyiselect" name="member[]">
                            <option value="">Select Any One</option>
                       <?php foreach($application_users_role as $uval){ ?>
                            <option value="<?php echo $uval['id']; ?>" <?php echo $responsible == $uval['id']?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_task')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="meeting" role="tabpanel">
                <form id="form_meeting">
                    <input type="hidden" name="lead_type" value="7">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Date <span class="requried text-danger">*</span></span>
                        <input aria-label="Date" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Time <span class="requried text-danger">*</span></span>
                        <input aria-label="Time" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group w-full">
                        <span class="form-label">Member <span class="requried text-danger">*</span></span>
                        <select aria-label="Member[]" class="form-control js-example-basic-multiple resetmulyiselect" name="member[]" multiple>
                       <?php foreach($application_users_role as $uval){ ?>
                            <option value="<?php echo $uval['id']; ?>" <?php echo $responsible == $uval['id']?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_meeting')">Submit</button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="visit" role="tabpanel">
                <form id="form_visit">
                    <input type="hidden" name="lead_type" value="8">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Date <span class="requried text-danger">*</span></span>
                        <input aria-label="Date" class="form-control datepicker" type="text" name="date" value="<?php  echo fromDate();?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label">Time <span class="requried text-danger">*</span></span>
                        <input aria-label="Time" class="form-control" type="time" name="time" value="">
                    </div>

                    <div class="col-lg-12 form-group">
                    <span class="form-label">Address <span class="requried text-danger">*</span></span>
                    <div class="grow-wrap">
                        <textarea aria-label="Address" name="address" placeholder="Address" class="border form-control" rows="3" name="address" oninput="this.parentNode.dataset.replicatedValue = this.value" ><?php echo $transaction['address']; ?></textarea>
                    </div>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label">Country <span class="requried text-danger">*</span></span>
                    <select aria-label="Country" class="form-control js-example-basic-single resetmulyiselect" name="country_id" onchange="selectedcategory(this.value,'state_idd','states','country_id')">
                        <option value="">Select Any One</option>
                        <?php foreach($countries as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['country_id'] == $cval['id']?"selected":"";?>><?php echo $cval['country_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label">State <span class="requried text-danger">*</span></span>
                    <select aria-label="State Idd" class="form-control js-example-basic-single resetmulyiselect" name="state_idd" id="state_idd" onchange="selectedcategory(this.value,'city_idd','cities','state_id')">
                        <option value="">Select Any One</option>
                        <?php foreach($states as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['state_id'] == $cval['id']?"selected":"";?>><?php echo $cval['state_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label">City <span class="requried text-danger">*</span></span>
                    <select aria-label="City" class="form-control js-example-basic-single resetmulyiselect" name="city_id" id="city_idd">
                        <option value="">Select Any One</option>
                        <?php foreach($cities as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['city_id'] == $cval['id']?"selected":"";?>><?php echo $cval['city_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group">
                    <span class="form-label">Post Code <span class="requried text-danger">*</span></span>
                    <input aria-label="Postal Code" class="form-control" type="text" name="post_code" placeholder="Postal Code" value="<?php echo $transaction['post_code']; ?>">
                </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label">Comment <span class="requried text-danger">*</span></span>
                        <textarea aria-label="Comment" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_visit')">Submit</button>
                </div>
             </form>
            </div>
            <div class="tab-pane activefeasbility removeallactivetab" id="feasibility_type" role="tabpanel">
                <div class="slides-container" id="feasibilitySlides">
                    <?php include_once 'custom_insert_feasibility_requirements.php'; ?>
                </div>
            </div>
        </div>
        <div class="card latest-update-card custom-scroll" style="height: 500px" id="crmleadhistories">
            <?php include_once "custom_insert_crm_lead_histories.php"; ?>
        </div>
    </div>
</div>
