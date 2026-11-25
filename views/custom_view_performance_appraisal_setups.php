<?php
defined('PREFIX_FILED_BRACKET') || define('PREFIX_FILED_BRACKET', 'filed[');
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$performance_appraisal_setup_id = $_POST['appraisal_id'];
$apiurl = API."?mode=getperformanceappraisalsetups&rid=".$performance_appraisal_setup_id."&preview=Y";
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$akra = $result['appraisal_key_result_areas'];
$appraisal_key_performance_indicators = $result['appraisal_key_performance_indicators'];
$performance_appraisal_questions = $result['performance_appraisal_questions'];
$paheadersres  = $result['performance_appraisal_headers'];
?>
<div class="card">
    <div class="card-header">
        <h5><?= __('Preview Appraisal'); ?></h5>
        <div class="card-header-right"></div>
    </div>
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab ml-2 mb-2">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#preview_employee" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('Employee'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#preview_ro" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('RO'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#preview_hod" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?= __('HOD'); ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="preview_employee" role="tabpanel">
                <div class="accordion-block color-accordion-block m-t-5">
                    <div class="color-accordion" id="color-accordion">
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="row">
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Department'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Designation'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>

                                                <tr>
                                                    <th scope="row"><?= __('Emp ID'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Date of Join'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Qualification'); ?>:</th>
                                                    <td class="address-basic">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('RO\'s Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('RO Designation'); ?>:</th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col"><?= __('Key Result Area'); ?></th>
                                            <th scope="col"><?= __('Key Performance Indicators'); ?></th>
                                            <th scope="col"><?= __('Weightage'); ?></th>
                                            <th scope="col"><?= __('Target / Achieved (self Score)'); ?></th>
                                            <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(self)'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $mk=1; foreach($appraisal_key_performance_indicators as $akpi){ ?>
                                            <tr>
                                                <td><?php echo $mk; ?></td>
                                                <td><?php echo $akra[$akpi['key_reslut_id']]['area_name']; ?></td>
                                                <td><?php echo $akpi['indicator_name']; ?></td>
                                                <td>
                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="<?php echo $akpi['weightage']; ?>" readonly>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" readonly>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" >
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0" ></td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                            <tr>
                                                <th scope="col" colspan="4" class="text-center"><?= __('Total'); ?></th>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly="">
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="">
                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0"  readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="col" colspan="3" class="text-center"><?= __('Total Percentage(%)'); ?></th>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_employee" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user"></i></span>
                                                                </span>
                                                            </div>

                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_manager" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user-secret"></i></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Performance Appraisal Questions'); ?></a>
                            <div class="accordion-desc">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%;">#</th>
                                            <th scope="col" class="w-b-w-s" style="width: 30%;"><?= __('Question'); ?></th>
                                            <th scope="col" style="width: 5%;"><?= __('Attch'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer Employee'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $queseq = 1;
                                        foreach($performance_appraisal_questions as $paq){
                                            $question_options = json_decode($paq['question_options'],1);
                                            $display_answer = json_decode($paq['display_answer'],1);
                                            ?>
                                            <tr>
                                                <th scope="col"><?php echo $queseq; ?></th>
                                                <th scope="col" class="w-b-w-s"><p><?php echo $paq['question']; ?><small class="d-block"><?php echo $paq['description']; ?></small></p></th>
                                                    <th scope="col">
                                                        <div class="row">
                                                            <div class="image-input col-lg-3" style="display: inherit; margin-top: -2px;">
                                                                <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>" >
                                                                    <i class="ti-cloud-up text-primary f-18 f-w-800" data-toggle="tooltip" title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                                    <input aria-label="<?= htmlspecialchars(__('File upload'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="attchments[<?php echo $paq['id']; ?>]" />
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td>
                                                        <?php
                                                        if(in_array('EMP', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $queseq++;
} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php foreach($paheadersres as $paheaders){
                        $loginuser_type = "RO";
                            $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                            $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                            if($header_display_setting[$loginuser_type]== "Y"){ ?>
                                <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?php echo $paheaders['header_name']; ?>
                                <p class="f-w-400"><?php echo $paheaders['header_comment']; ?></p>
                            </a>
                            <div class="accordion-desc">
                            <?php if($paheaders['dynamic_page']=='0'){   // custom page call
                                include_once $paheaders['page_name'].".php";
                            }else{  //dyanamic table generated
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                        <thead>
                                            <tr>
                                                <?php foreach($sub_header_name as $sub_headerres){ ?>
                                                    <th scope="col"><?php echo $sub_headerres['field_name']; ?></th>
                                                <?php }
                                                if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                    <th scope="col"><?= __('Action'); ?></th>
                                                <?php }  ?>
                                            </tr>
                                        </thead>
                                        <tbody data-repeater-list="">
                                            <?php $hedcnt[$paheadersrow] = 1; for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){ ?>
                                                <tr >
                                                    <?php $fieldcnt = 1;
                                                    foreach($sub_header_name as $sub_headerres){
                                                        $fieldname = PREFIX_FILED_BRACKET.$paheaders['id']."][".$fieldcnt."][]";
                                                        $fieldvalue = $epappraisal[$paheaders['id']][$hed]['filed_'.$fieldcnt];
                                                        ?>
                                                        <td>
                                                            <div class="">
                                                                <?php if($sub_headerres['type']=="S" && $sub_headerres[$loginuser_type] == "Y"){ ?>
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single" name="<?php echo $fieldname; ?>">
                                                                        <option value="Yes" ><?= __('Yes'); ?></option>
                                                                        <option value="No" ><?= __('No'); ?></option>
                                                                        <option value="Dont_know" ><?= __('i don\'t know'); ?></option>
                                                                    </select>
                                                                <?php }else{ ?>
                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?>>
                                                                <?php } ?>

                                                            </div>
                                                        </td>
                                                        <?php  $fieldcnt++;
} ?>
                                                        <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                            <td>
                                                                <button type="button" class="btn p-0 border-0 bg-transparent text-danger" onclick="appraiserDeleteRow(<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>)" aria-label="<?= htmlspecialchars(__('Delete Row'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php $hedcnt[$paheadersrow]++;
} ?>
                                                </tbody>
                                            </table>
                                            <?php if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary mt-2" onclick="addMoreRow('<?php echo $paheaders['id']; ?>')">
                                                    <i class="fa fa-plus"></i><?= __('Add'); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    <?php  } ?>
                                </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="preview_ro" role="tabpanel">
                    <div class="accordion-block color-accordion-block m-t-5">
                    <div class="color-accordion" id="color-accordion-ro">
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="row">
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Department'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Designation'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>

                                                <tr>
                                                    <th scope="row"><?= __('Emp ID'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Date of Join'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Qualification'); ?>:</th>
                                                    <td class="address-basic">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('RO\'s Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('RO Designation'); ?>:</th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col"><?= __('Key Result Area'); ?></th>
                                            <th scope="col"><?= __('Key Performance Indicators'); ?></th>
                                            <th scope="col"><?= __('Weightage'); ?></th>
                                            <th scope="col"><?= __('Target / Achieved (self Score)'); ?></th>
                                            <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(self)'); ?></th>
                                            <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(RO\'s Score)'); ?></th>
                                            <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(RO)'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $mk=1; foreach($appraisal_key_performance_indicators as $akpi){ ?>
                                            <tr>
                                                <td><?php echo $mk; ?></td>
                                                <td><?php echo $akra[$akpi['key_reslut_id']]['area_name']; ?></td>
                                                <td><?php echo $akpi['indicator_name']; ?></td>
                                                <td>
                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="<?php echo $akpi['weightage']; ?>" readonly>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" readonly>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  readonly></td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  ></td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  readonly></td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                            <tr>
                                                <th scope="col" colspan="4" class="text-center"><?= __('Total'); ?></th>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly="">
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="">
                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0"  readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="col" colspan="3" class="text-center"><?= __('Total Percentage(%)'); ?></th>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_employee_2" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user"></i></span>
                                                                </span>
                                                            </div>

                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_manager_2" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user-secret"></i></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Performance Appraisal Questions'); ?></a>
                            <div class="accordion-desc">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%;">#</th>
                                            <th scope="col" class="w-b-w-s" style="width: 30%;"><?= __('Question'); ?></th>
                                            <th scope="col" style="width: 5%;"><?= __('Attch'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer Employee'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer RO'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $queseq = 1;
                                        foreach($performance_appraisal_questions as $paq){
                                            $question_options = json_decode($paq['question_options'],1);
                                            $display_answer = json_decode($paq['display_answer'],1);
                                            ?>
                                            <tr>
                                                <th scope="col"><?php echo $queseq; ?></th>
                                                <th scope="col" class="w-b-w-s"><p><?php echo $paq['question']; ?><small class="d-block"><?php echo $paq['description']; ?></small></p></th>
                                                    <th scope="col">
                                                        <div class="row">
                                                            <div class="image-input col-lg-3" style="display: inherit; margin-top: -2px;">
                                                                <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>" >
                                                                    <i class="ti-cloud-up text-primary f-18 f-w-800" data-toggle="tooltip" title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                                    <input aria-label="<?= htmlspecialchars(__('File upload'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="attchments[<?php echo $paq['id']; ?>]" />
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td style="opacity: 0.5">
                                                        <?php
                                                        if(in_array('EMP', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if(in_array('RO', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $queseq++;
} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php foreach($paheadersres as $paheaders){
                                        $loginuser_type = "HOD";
                                    $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                                    $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                                    if($header_display_setting[$loginuser_type]== "Y"){ ?>
                                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?php echo $paheaders['header_name']; ?>
                                        <p class="f-w-400"><?php echo $paheaders['header_comment']; ?></p>
                                    </a>
                                    <div class="accordion-desc">
                                    <?php if($paheaders['dynamic_page']=='0'){   // custom page call
                                        include_once $paheaders['page_name'].".php";
                                    }else{  //dyanamic table generated
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                        <thead>
                                            <tr>
                                                <?php foreach($sub_header_name as $sub_headerres){ ?>
                                                    <th scope="col"><?php echo $sub_headerres['field_name']; ?></th>
                                                <?php }
                                                if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                    <th scope="col"><?= __('Action'); ?></th>
                                                <?php }  ?>
                                            </tr>
                                        </thead>
                                        <tbody data-repeater-list="">
                                            <?php $hedcnt[$paheadersrow] = 1; for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){ ?>
                                                <tr >
                                                    <?php $fieldcnt = 1;
                                                    foreach($sub_header_name as $sub_headerres){
                                                        $fieldname = PREFIX_FILED_BRACKET.$paheaders['id']."][".$fieldcnt."][]";
                                                        $fieldvalue = $epappraisal[$paheaders['id']][$hed]['filed_'.$fieldcnt];
                                                        ?>
                                                        <td>
                                                            <div class="">
                                                                <?php if($sub_headerres['type']=="S" && $sub_headerres[$loginuser_type] == "Y"){ ?>
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single" name="<?php echo $fieldname; ?>">
                                                                        <option value="Yes" ><?= __('Yes'); ?></option>
                                                                        <option value="No" ><?= __('No'); ?></option>
                                                                        <option value="Dont_know" ><?= __('i don\'t know'); ?></option>
                                                                    </select>
                                                                <?php }else{ ?>
                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?>>
                                                                <?php } ?>

                                                            </div>
                                                        </td>
                                                        <?php  $fieldcnt++;
} ?>
                                                        <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                            <td>
                                                                <button type="button" class="btn p-0 border-0 bg-transparent text-danger" onclick="appraiserDeleteRow(<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>)" aria-label="<?= htmlspecialchars(__('Delete Row'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php $hedcnt[$paheadersrow]++;
} ?>
                                                </tbody>
                                            </table>
                                            <?php if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary mt-2" onclick="addMoreRow('<?php echo $paheaders['id']; ?>')">
                                                    <i class="fa fa-plus"></i><?= __('Add'); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    <?php  } ?>
                                </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="preview_hod" role="tabpanel">
                    <div class="accordion-block color-accordion-block m-t-5">
                    <div class="color-accordion" id="color-accordion-hod">
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="row">
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Department'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Designation'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>

                                                <tr>
                                                    <th scope="row"><?= __('Emp ID'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Date of Join'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('Qualification'); ?>:</th>
                                                    <td class="address-basic">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?= __('RO\'s Name'); ?></th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?= __('RO Designation'); ?>:</th>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Professional Excellence'); ?></a>
                        <div class="accordion-desc">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col"><?= __('Key Result Area'); ?></th>
                                            <th scope="col"><?= __('Key Performance Indicators'); ?></th>
                                            <th scope="col"><?= __('Weightage'); ?></th>
                                            <th scope="col"><?= __('Target / Achieved (self Score)'); ?></th>
                                            <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(self)'); ?></th>
                                            <th scope="col"><?= __('Percentage achieved'); ?><br /><?= __('(RO\'s Score)'); ?></th>
                                            <th scope="col"><?= __('Points Scored'); ?><br /><?= __('(RO)'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $mk=1; foreach($appraisal_key_performance_indicators as $akpi){ ?>
                                            <tr>
                                                <td><?php echo $mk; ?></td>
                                                <td><?php echo $akra[$akpi['key_reslut_id']]['area_name']; ?></td>
                                                <td><?php echo $akpi['indicator_name']; ?></td>
                                                <td>
                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="<?php echo $akpi['weightage']; ?>" readonly>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" readonly>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="-" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  readonly></td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  readonly></td>
                                                <td><input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="0"  readonly></td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                            <tr>
                                                <th scope="col" colspan="4" class="text-center"><?= __('Total'); ?></th>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly="">
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0" readonly>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="">
                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="0"  readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="col" colspan="3" class="text-center"><?= __('Total Percentage(%)'); ?></th>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_employee_3" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Employee Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user"></i></span>
                                                                </span>
                                                            </div>

                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group input-group-button">
                                                                <input aria-label="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control width1" id="finalkra_total_manager_3" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Manager Total'), ENT_QUOTES, 'UTF-8'); ?>" value="0" >
                                                                <span class="input-group-text addon-style">
                                                                    <span class=""><i class="fa fa-user-secret"></i></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?= __('Performance Appraisal Questions'); ?></a>
                            <div class="accordion-desc">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%;">#</th>
                                            <th scope="col" class="w-b-w-s" style="width: 30%;"><?= __('Question'); ?></th>
                                            <th scope="col" style="width: 5%;"><?= __('Attch'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer Employee'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer RO'); ?></th>
                                            <th scope="col" style="width: 20%;"><?= __('Ratting / Answer HOD'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $queseq = 1;
                                        foreach($performance_appraisal_questions as $paq){
                                            $question_options = json_decode($paq['question_options'],1);
                                            $display_answer = json_decode($paq['display_answer'],1);
                                            ?>
                                            <tr>
                                                <th scope="col"><?php echo $queseq; ?></th>
                                                <th scope="col" class="w-b-w-s"><p><?php echo $paq['question']; ?><small class="d-block"><?php echo $paq['description']; ?></small></p></th>
                                                    <th scope="col">
                                                        <div class="row">
                                                            <div class="image-input col-lg-3" style="display: inherit; margin-top: -2px;">
                                                                <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>" >
                                                                    <i class="ti-cloud-up text-primary f-18 f-w-800" data-toggle="tooltip" title="<?= htmlspecialchars(__('Upload'), ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                                    <input aria-label="<?= htmlspecialchars(__('File upload'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="attchments[<?php echo $paq['id']; ?>]" />
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td style="opacity: 0.5">
                                                        <?php
                                                        if(in_array('EMP', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td style="opacity: 0.5">
                                                        <?php
                                                        if(in_array('RO', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if(in_array('HOD', $display_answer)){  ?>
                                                            <div class="row">
                                                                <div class="col-lg-3 p-1">
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                        <?php for($im=1;$im<=10;$im++){?>
                                                                            <option value="<?php echo $im; ?>"><?php echo $im; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-9 p-1">
                                                                    <?php
                                                                    if($paq['answer_type'] == 'MC'){?>
                                                                        <?php foreach($question_options as $qo){ ?>
                                                                            <div class="checkbox-fade fade-in-primary mr-5">
                                                                                <label class="form-label">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox">
                                                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php }elseif($paq['answer_type'] == 'O'){?>
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single">
                                                                            <option value=""><?= __('Select Any One'); ?></option>
                                                                            <?php foreach($question_options as $qo){ ?>
                                                                                <option value="<?php echo $qo; ?>"><?php echo $qo; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $queseq++;
} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php foreach($paheadersres as $paheaders){
                                        $loginuser_type = "HOD";
                                    $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                                    $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                                    if($header_display_setting[$loginuser_type]== "Y"){ ?>
                                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?php echo $paheaders['header_name']; ?>
                                        <p class="f-w-400"><?php echo $paheaders['header_comment']; ?></p>
                                    </a>
                                    <div class="accordion-desc">
                                    <?php if($paheaders['dynamic_page']=='0'){   // custom page call
                                        include_once $paheaders['page_name'].".php";
                                    }else{  //dyanamic table generated
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                        <thead>
                                            <tr>
                                                <?php foreach($sub_header_name as $sub_headerres){ ?>
                                                    <th scope="col"><?php echo $sub_headerres['field_name']; ?></th>
                                                <?php }
                                                if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                    <th scope="col"><?= __('Action'); ?></th>
                                                <?php }  ?>
                                            </tr>
                                        </thead>
                                        <tbody data-repeater-list="">
                                            <?php $hedcnt[$paheadersrow] = 1; for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){ ?>
                                                <tr >
                                                    <?php $fieldcnt = 1;
                                                    foreach($sub_header_name as $sub_headerres){
                                                        $fieldname = PREFIX_FILED_BRACKET.$paheaders['id']."][".$fieldcnt."][]";
                                                        $fieldvalue = $epappraisal[$paheaders['id']][$hed]['filed_'.$fieldcnt];
                                                        ?>
                                                        <td>
                                                            <div class="">
                                                                <?php if($sub_headerres['type']=="S" && $sub_headerres[$loginuser_type] == "Y"){ ?>
                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single" name="<?php echo $fieldname; ?>">
                                                                        <option value="Yes" ><?= __('Yes'); ?></option>
                                                                        <option value="No" ><?= __('No'); ?></option>
                                                                        <option value="Dont_know" ><?= __('i don\'t know'); ?></option>
                                                                    </select>
                                                                <?php }else{ ?>
                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?>>
                                                                <?php } ?>

                                                            </div>
                                                        </td>
                                                        <?php  $fieldcnt++;
} ?>
                                                        <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                            <td>
                                                                <button type="button" class="btn p-0 border-0 bg-transparent text-danger" onclick="appraiserDeleteRow(<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>)" aria-label="<?= htmlspecialchars(__('Delete Row'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php $hedcnt[$paheadersrow]++;
} ?>
                                                </tbody>
                                            </table>
                                            <?php if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary mt-2" onclick="addMoreRow('<?php echo $paheaders['id']; ?>')">
                                                    <i class="fa fa-plus"></i><?= __('Add'); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    <?php  } ?>
                                </div>
                            <?php } ?>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><?= __('Employee Comment'); ?></h5>
                        </div>
                        <div class="card-body">
                            <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="4" cols="50"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><?= __('RO Comment'); ?></h5>
                        </div>
                        <div class="card-body">
                            <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="8" cols="50"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><?= __('HOD Comment'); ?></h5>
                        </div>
                        <div class="card-body">
                            <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" rows="40" cols="50"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
