

<?php include_once 'header.php';?>

    <div class="row">
        <div class="col-md-3">
            <div class="ess-1">
                <ul class="nav nav-tabs nav-tabs-line">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-plus"></i>
                            <span><?= __('Create New Service'); ?></span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon-star"></i>
                            <span><?= __('Organization'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-calendar-1"></i>
                            <span><?= __('Leave'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_4" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-calendar"></i>
                            <span><?= __('Attendence'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_5" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-chronometer"></i>
                            <span><?= __('Timesheet'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_6" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-checking"></i>
                            <span><?= __('Training'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_7" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-google-drive-file"></i>
                            <span><?= __('Files'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_8" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-cup"></i>
                            <span><?= __('Performance'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_9" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-paper-plane"></i>
                            <span><?= __('Travel Management'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_10" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon-security"></i>
                            <span><?= __('Recreational Activities'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_11" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon-price-tag"></i>
                            <span><?= __('ID Card Request'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_12" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-medical-records-1"></i>
                            <span><?= __('Group Mediclaim'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_13" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-rocket-1"></i>
                            <span><?= __('Transport Services'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_14" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon-home-2"></i>
                            <span><?= __('Conference Room Booking'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_15" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon-users-1"></i>
                            <span><?= __('Employee Onboarding'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_16" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <i class="flaticon2-document"></i>
                            <span><?= __('Asset Management'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ess-1">
                <div class="tab-content mt-5" id="myTabContent">
                    <div class="tab-pane desc fade show active" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_1">
                        <?= __('tab 1'); ?>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Organozation'); ?></h3>
                                    <span><?= __('Employees can enrol for organozation'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Organozation'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div id="wrapper">
                                                        <div id="container">
                                                            <ol class="organizational-chart">
                                                                <li>
                                                                    <div>
                                                                        <h3><?= __('Primary'); ?></h3>
                                                                    </div>
                                                                    <div>
                                                                        <h3><?= __('Primary'); ?></h3>
                                                                    </div>
                                                                    <div>
                                                                        <h3><?= __('Primary'); ?></h3>
                                                                    </div>
                                                                    <ol>
                                                                        <li>
                                                                            <div>
                                                                                <h4><?= __('Secondary'); ?></h4>
                                                                            </div>
                                                                            <ol>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                </li>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                    <ol>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                            <ol>
                                                                                                <li>
                                                                                                    <div>
                                                                                                        <h6><?= __('Quinary'); ?></h6>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li>
                                                                                                    <div>
                                                                                                        <h6><?= __('Quinary'); ?></h6>
                                                                                                    </div>
                                                                                                    <ol>
                                                                                                        <li>
                                                                                                            <div>
                                                                                                                <h6><?= __('Senary'); ?></h6>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    </ol>
                                                                                                </li>
                                                                                            </ol>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h5><?= __('Quaternary'); ?></h5>
                                                                                            </div>
                                                                                        </li>
                                                                                    </ol>
                                                                                </li>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                </li>
                                                                            </ol>
                                                                        </li>
                                                                        <li>
                                                                            <div>
                                                                                <h4><?= __('Secondary'); ?></h4>
                                                                            </div>
                                                                            <ol>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                </li>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                    <ol>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                        </li>
                                                                                    </ol>
                                                                                </li>
                                                                            </ol>
                                                                        </li>
                                                                        <li>
                                                                            <div>
                                                                                <h4><?= __('Secondary'); ?></h4>
                                                                            </div>
                                                                            <ol>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                    <ol>
                                                                                        <li>
                                                                                            <div>
                                                                                                <h6><?= __('Quaternary'); ?></h6>
                                                                                            </div>
                                                                                            <ol>
                                                                                                <li>
                                                                                                    <div>
                                                                                                        <h6><?= __('Quinary'); ?></h6>
                                                                                                    </div>
                                                                                                    <ol>
                                                                                                        <li>
                                                                                                            <div>
                                                                                                                <h6><?= __('Senary'); ?></h6>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <div>
                                                                                                                <h6><?= __('Senary'); ?></h6>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    </ol>
                                                                                                </li>
                                                                                                <li>
                                                                                                    <div>
                                                                                                        <h6><?= __('Quinary'); ?></h6>
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ol>
                                                                                        </li>
                                                                                    </ol>
                                                                                </li>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                </li>
                                                                                <li>
                                                                                    <div>
                                                                                        <h5><?= __('Tertiary'); ?></h5>
                                                                                    </div>
                                                                                </li>
                                                                            </ol>
                                                                        </li>
                                                                    </ol>
                                                                </li>
                                                            </ol>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_2">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_2">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_2" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel" aria-labelledby="kt_tab_pane_4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_3">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_3">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_3" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_2"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_2">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_2">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_2" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel" aria-labelledby="kt_tab_pane_5">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_4">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_4">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_4" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_3"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_3">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_3">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_3" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_6" role="tabpanel" aria-labelledby="kt_tab_pane_6">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_5">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_5">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_5" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_4"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_4">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_4">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_4" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_7" role="tabpanel" aria-labelledby="kt_tab_pane_7">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_6">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_6">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_6" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_5"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_5">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_5">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_5" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_8" role="tabpanel" aria-labelledby="kt_tab_pane_8">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_7">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_7">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_7" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_6"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_6">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_6">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_6" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_9" role="tabpanel" aria-labelledby="kt_tab_pane_9">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_8">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_8">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_8" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_7"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_7">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_7">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_7" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_10" role="tabpanel" aria-labelledby="kt_tab_pane_10">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_9">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_9">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_9" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_8"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_8">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_8">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_8" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_11" role="tabpanel" aria-labelledby="kt_tab_pane_11">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_10">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_10">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_10" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_9"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_9">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_9">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_9" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_12" role="tabpanel" aria-labelledby="kt_tab_pane_12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ess-bdr">
                                    <h3><?= __('Description'); ?></h3>
                                    <span><?= __('Employees can enrol for mediclaim'); ?></span>
                                    <h5><?= __('Service Owner'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ess-bdr">
                                    <a href=""><?= __('mediclaim'); ?> <i class="flaticon2-add-1 float-right"></i></a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ess-related">
                                    <a href=""><?= __('Related Documents'); ?></a>
                                    <a href=""><?= __('Create Case'); ?></a>
                                    <a href=""><?= __('FAQ'); ?></a>
                                    <i class="flaticon2-cross close-btn"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_13" role="tabpanel" aria-labelledby="kt_tab_pane_13">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_11">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_11">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_11" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_10"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_10">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_10">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_10" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_14" role="tabpanel" aria-labelledby="kt_tab_pane_14">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_12">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_12">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_12" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_11"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_11">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_11">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_11" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_15" role="tabpanel" aria-labelledby="kt_tab_pane_15">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_13">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_13">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_13" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_12"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_12">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_12">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_12" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_16" role="tabpanel" aria-labelledby="kt_tab_pane_16">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="ess-bdr">
                                    <h3><?= __('Leave'); ?></h3>
                                    <span><?= __('Employees can enrol for leaves'); ?></span>
                                    <h5><?= __('Employee'); ?></h5>
                                    <img src="assets/media/users/user5.jpg" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <b><?= __('Christina'); ?></b>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ess-bdr">
                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3_14">
                                        <div class="card">
                                            <div class="card-header" id="headingOne3_14">
                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">
                                                    <?= __('Leave'); ?>
                                                </div>
                                            </div>
                                            <div id="collapseOne3_14" class="collapse" data-parent="#accordionExample3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl_13"><?= __('Leave Type'); ?></label>
                                                        <select class="form-control form-control-lg" id="exampleSelectl_13">
                                                            <option><?= __('Casual'); ?></option>
                                                            <option><?= __('Medical'); ?></option>
                                                            <option><?= __('Comp off'); ?></option>
                                                            <option><?= __('Restricted'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('Title'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Title'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Start Date/End date'); ?></span>
                                                        <div class="">
                                                            <div class="input-daterange input-group" id="kt_datepicker_5_13">
                                                                <input aria-label="<?= htmlspecialchars(__('Start'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="start">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                </div>
                                                                <input aria-label="<?= htmlspecialchars(__('End'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="end">
                                                            </div>
                                                            <span class="form-text text-muted"><?= __('Linked pickers for date range selection'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class=""><?= __('Remarks'); ?></span>
                                                        <div class="">
                                                            <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2_13" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <span><?= __('No of Days'); ?> <span class="text-danger">*</span></span>
                                                        <input aria-label="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>" type="day" class="form-control" placeholder="<?= htmlspecialchars(__('Days'), ENT_QUOTES, 'UTF-8'); ?>">

                                                    </div>
                                                    <button type="button" class="btn btn-success"><?= __('Submit'); ?></button>
                                                    <button type="button" class="btn btn-success cancel"><?= __('Cancel'); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="ess-related">

                                    <button type="button" class="btn btn-success"><?= __('Related Documents'); ?></button><br /><br />
                                    <button type="button" class="btn btn-warning"><?= __('Create Case'); ?></button><br /><br />
                                    <button type="button" class="btn btn-info"><?= __('FAQ'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php include_once 'footer.php';?>

<style type="text/css">

</style>
