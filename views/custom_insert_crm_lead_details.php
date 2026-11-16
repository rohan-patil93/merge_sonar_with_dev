<div class="card">
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab">
            <?php if($crm_history_id !=""){ ?>
            <li class="nav-item">
                <a class="nav-link active removeallactivetab" data-bs-toggle="tab" href="#comment" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Note'); ?></a>
            </li>
            <?php }else{ ?>
            <li class="nav-item">
                <a class="nav-link active  activehomecomments removeallactivetab" data-bs-toggle="tab" href="#comment" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Comment') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#wait" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Wait'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#call" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Call'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#sms" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('SMS'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#email" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('E-mail'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#task" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Task'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab" data-bs-toggle="tab" href="#meeting" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Meeting'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link removeallactivetab " data-bs-toggle="tab" href="#visit" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Visit'); ?></a>
            </li>
        <?php } ?>
            <li class="nav-item " id="feasibilityWrapper" <?php echo $transaction['feasibility_check'] =="Y"?'':'style="display: none;"'; ?>>
                <a class="nav-link removeallactivetab activefeasbility" data-bs-toggle="tab" href="#feasibility_type" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Feasibility Type'); ?></a>
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
                            <span class="form-label d-block"><?= __('Date') ?></span>
                            <span><?php echo $task_crm_history['date']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Time') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Comment') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                   <?php  }elseif($task_crm_history['lead_type'] == '7' || $task_crm_history['lead_type'] == '6'){ ?>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Date') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['date']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Time') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Comment') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <span class="form-label d-block"><?= __('Members') ?></span>
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
                            <span class="form-label d-block"><?= __('Comment') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['comment']; ?></span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Date') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['date']; ?></span>
                        </div>

                        <div class="col-lg-4">
                            <span class="form-label d-block"><?= __('Time') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['time']; ?></span>
                        </div>
                        <div class="col-lg-4 mt-2">
                            <span class="form-label d-block"><?= __('Address') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['address']; ?></span>
                        </div>
                        <div class="col-lg-4 mt-2">
                            <span class="form-label d-block"><?= __('Post Code') ?></span>
                            <span class="form-label"><?php echo $task_crm_history['post_code']; ?></span>
                        </div>

                   <?php } ?>
                    </div>
                    <input type="hidden" name="rid" value="<?php echo $task_crm_history['id']; ?>">
                    <div class="form-group mt-4">
                        <span class="form-label"><?= __('Note'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_comment')"><?= __('Submit') ?></button>
                    </div>
                </form>
            </div>
            <?php } ?>
            <div class="tab-pane <?php if($crm_history_id ==""){ ?>active
            <?php } ?> activehomecomments removeallactivetab" id="comment_2" role="tabpanel">
                <form id="form_comment_2">
                <input type="hidden" name="lead_type" value="1">
                <div class="form-group">
                    <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                    <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_comment')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="wait" role="tabpanel">
                <form id="form_wait">
                <input type="hidden" name="lead_type" value="2">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Date'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Time'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_wait')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="call" role="tabpanel">
                <form id="form_call">
                <input type="hidden" name="lead_type" value="3">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Date'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Time'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="time" name="time"  value="">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_call')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="sms" role="tabpanel">
                <form id="form_sms">
                <input type="hidden" name="lead_type" value="4">
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Mobile No.'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Phone'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="phone" value="<?php echo $transaction['phone']; ?>">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('SMS Text'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Sms Text'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="sms_text"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_sms')"><?= __('Submit') ?></button>
                    <span class="text-info f-12">*<?= __('SMS service is not activated, so SMS could not be sent.'); ?></span>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="email" role="tabpanel">
                <form id="form_email">
                <input type="hidden" name="lead_type" value="5">
                <div class="row">
                    <div class="col-lg-6 form-group w-full">
                        <span class="form-label"><?= __('Email Template'); ?> <span class="requried text-danger">*</span></span>
                        <select aria-label="<?= htmlspecialchars(__('Email Content'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single resetmulyiselect" name="email_content_id" >

                        <?php foreach($email_content as $cval){ ?>
                            <option value="<?php echo $cval['template_reference_id']; ?>"><?php echo $cval['template_name']; ?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label d-block"><?= __('Recipient Email'); ?> <span class="requried text-danger">*</span></span>
                        <span class="errorshowcrmcomment text-danger"></span>
                        <input aria-label="<?= htmlspecialchars(__('Recipient Email'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="recipient_email_id" value="<?php echo $transaction['email_id']; ?>">
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Email Content'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="8" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_email')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="task" role="tabpanel">
                <form id="form_task">
                    <input type="hidden" name="lead_type" value="6">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Date'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Time'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group w-full">
                        <span class="form-label"><?= __('User'); ?> <span class="requried text-danger">*</span></span>
                        <select aria-label="<?= htmlspecialchars(__('Member'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control js-example-basic-single resetmulyiselect" name="member[]">
                            <option value=""><?= __('Select Any One') ?></option>
                       <?php foreach($application_users_role as $uval){ ?>
                            <option value="<?php echo $uval['id']; ?>" <?php echo $responsible == $uval['id']?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_task')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="meeting" role="tabpanel">
                <form id="form_meeting">
                    <input type="hidden" name="lead_type" value="7">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Date'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" type="text" name="date" value="<?php echo fromDate(); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Time'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="time" name="time" value="">
                    </div>
                    <div class="col-lg-12 form-group w-full">
                        <span class="form-label"><?= __('Member'); ?> <span class="requried text-danger">*</span></span>
                        <select aria-label="<?= htmlspecialchars(__('Member'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control js-example-basic-multiple resetmulyiselect" name="member[]" multiple>
                       <?php foreach($application_users_role as $uval){ ?>
                            <option value="<?php echo $uval['id']; ?>" <?php echo $responsible == $uval['id']?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_meeting')"><?= __('Submit') ?></button>
                </div>
                </form>
            </div>
            <div class="tab-pane removeallactivetab" id="visit" role="tabpanel">
                <form id="form_visit">
                    <input type="hidden" name="lead_type" value="8">
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Date'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" type="text" name="date" value="<?php  echo fromDate();?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <span class="form-label"><?= __('Time'); ?> <span class="requried text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="time" name="time" value="">
                    </div>

                    <div class="col-lg-12 form-group">
                    <span class="form-label"><?= __('Address'); ?> <span class="requried text-danger">*</span></span>
                    <div class="grow-wrap">
                        <textarea aria-label="<?= htmlspecialchars(__('Address'), ENT_QUOTES, 'UTF-8'); ?>" name="address" placeholder="<?= __('Address') ?>" class="border form-control" rows="3" name="address" oninput="this.parentNode.dataset.replicatedValue = this.value" ><?php echo $transaction['address']; ?></textarea>
                    </div>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label"><?= __('Country'); ?> <span class="requried text-danger">*</span></span>
                    <select aria-label="<?= htmlspecialchars(__('Country'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single resetmulyiselect" name="country_id" onchange="selectedcategory(this.value,'state_idd','states','country_id')">
                        <option value=""><?= __('Select Any One') ?></option>
                        <?php foreach($countries as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['country_id'] == $cval['id']?"selected":"";?>><?php echo $cval['country_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label"><?= __('State'); ?> <span class="requried text-danger">*</span></span>
                    <select aria-label="<?= htmlspecialchars(__('State Idd'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single resetmulyiselect" name="state_idd" id="state_idd" onchange="selectedcategory(this.value,'city_idd','cities','state_id')">
                        <option value=""><?= __('Select Any One') ?></option>
                        <?php foreach($states as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['state_id'] == $cval['id']?"selected":"";?>><?php echo $cval['state_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group w-full">
                    <span class="form-label"><?= __('City'); ?> <span class="requried text-danger">*</span></span>
                    <select aria-label="<?= htmlspecialchars(__('City'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single resetmulyiselect" name="city_id" id="city_idd">
                        <option value=""><?= __('Select Any One') ?></option>
                        <?php foreach($cities as $cval){ ?>
                            <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['city_id'] == $cval['id']?"selected":"";?>><?php echo $cval['city_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-6 form-group">
                    <span class="form-label"><?= __('Post Code'); ?> <span class="requried text-danger">*</span></span>
                    <input aria-label="<?= htmlspecialchars(__('Postal Code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="post_code" placeholder="<?= __('Postal Code') ?>" value="<?php echo $transaction['post_code']; ?>">
                </div>
                    <div class="col-lg-12 form-group">
                        <span class="form-label"><?= __('Comment'); ?> <span class="requried text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value" name="comment"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitLeadFormDetail('form_visit')"><?= __('Submit') ?></button>
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
