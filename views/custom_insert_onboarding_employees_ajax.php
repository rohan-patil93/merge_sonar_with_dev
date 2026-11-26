<?php
defined('CSS_DISPLAY_NONE') || define('CSS_DISPLAY_NONE', 'display: none');
defined('CSS_CUSTOM_READONLY') || define('CSS_CUSTOM_READONLY', 'custom_readonly');
defined('DATE_ZERO') || define('DATE_ZERO', '0000-00-00');
defined('ONB_SELECT_ANY_ONE') || define('ONB_SELECT_ANY_ONE', 'Select Any One');
defined('ONB_UPLOADS_DIR') || define('ONB_UPLOADS_DIR', 'uploads/all/');
defined('ONB_WORK_IMAGE') || define('ONB_WORK_IMAGE', 'assets/images/work.png');
defined('ONB_WORK_ALT') || define('ONB_WORK_ALT', 'Work in progress');
defined('ONB_ADD_MORE') || define('ONB_ADD_MORE', '+ Add More');
defined('ONB_ATTACHMENTS_PREVIEW') || define('ONB_ATTACHMENTS_PREVIEW', 'Attachments Preview');
defined('STR_DRIVING_LICENCE') || define('STR_DRIVING_LICENCE', 'Driving Licence');
defined('STR_COVER_UNDER_PF') || define('STR_COVER_UNDER_PF', 'Cover Under Pf');
if(isset($_POST['ajaxcallemployee']) && $_POST['ajaxcallemployee'] =="Y"){
    $employee_id = $_POST['employee_id'] ?? '';
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API . "?mode=getemployees&employee_id=" . urlencode((string)$employee_id) . "&working_role_short_code=" . urlencode((string)($_SESSION['working_role_short_code'] ?? ''));
    $response = callAPI($url);
    $result = json_decode((string)$response, true);
    $data = $result['employees'] ?? array();
}

if (!function_exists('onbEsc')) {
    function onbEsc($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('onbReadonlyClass')) {
    function onbReadonlyClass($fieldaccesright, $fieldKey) {
        return $fieldaccesright['field'][$fieldKey] != "E" ? CSS_CUSTOM_READONLY : "";
    }
}

if (!function_exists('onbFormatDate')) {
    function onbFormatDate($dateValue, $emptyFallback = '') {
        return ($dateValue != DATE_ZERO && $dateValue != "") ? fromDate($dateValue) : $emptyFallback;
    }
}

if (!function_exists('onbFormatDateNonEmpty')) {
    function onbFormatDateNonEmpty($dateValue, $emptyFallback = '') {
        return (!empty($dateValue) && $dateValue != DATE_ZERO) ? fromDate($dateValue) : $emptyFallback;
    }
}

if (!function_exists('renderOnbEmptyStateCard')) {
    function renderOnbEmptyStateCard($message) {
        ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> <?php echo __($message); ?> </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="<?= htmlspecialchars(__(ONB_WORK_ALT), ENT_QUOTES, 'UTF-8'); ?>" src="<?php echo ONB_WORK_IMAGE; ?>" style="max-height: 300px!important;">
            </div>
        </div>
        <?php
    }
}

if (!function_exists('renderOnbViewField')) {
    function renderOnbViewField($label, $valueHtml, $style = '') {
        $styleAttr = $style !== '' ? ' style="' . $style . '"' : '';
        ?>
        <div class="col-xl-6 form-group"<?php echo $styleAttr; ?>>
            <span class="form-label d-block fw-bold"><?php echo __($label); ?></span>
            <p><?php echo $valueHtml; ?></p>
        </div>
        <?php
    }
}

if (!function_exists('renderOnbSelectOptions')) {
    function renderOnbSelectOptions($items, $selectedValue, $valueKey, $labelKey, $translateLabel = false) {
        foreach ((array)$items as $item) {
            $selectedAttr = ($selectedValue == $item[$valueKey]) ? ' selected' : '';
            $label = $translateLabel ? __($item[$labelKey]) : $item[$labelKey];
            echo '<option value="' . onbEsc($item[$valueKey]) . '"' . $selectedAttr . '>' . onbEsc($label) . '</option>';
        }
    }
}

if (!function_exists('renderOnboardingEmployeesAjaxSection')) {
function renderOnboardingEmployeesAjaxSection($ctx) {

$result = $ctx['result'] ?? array();
$data = $ctx['data'] ?? array();
$employees_display_fields = $result['employees_display_fields'] ?? array();

$agecalc = '';
$dobRaw = $data['date_of_birth'] ?? '';
if (!empty($dobRaw) && $dobRaw !== DATE_ZERO) {
    try {
        $birthDate = new DateTime((string)$dobRaw);
        $current = new DateTime(date('Y-m-d'));
        $interval = $birthDate->diff($current);
        $agemonth = ($interval->m < 10 && $interval->m >= 0) ? '0'.$interval->m : (string)$interval->m;
        $agecalc = $interval->y.".".$agemonth;
    } catch (Throwable $e) {
        $agecalc = '';
    }
}

$onbVars = array_merge($ctx, array(
    'result' => $result,
    'data' => $data,
    'vafield' => json_decode((string)($employees_display_fields['field_list'] ?? ''), true),
    'departments' => $result['departments'] ?? array(),
    'designations' => $result['designations'] ?? array(),
    'divisions' => $result['divisions'] ?? array(),
    'locations' => $result['locations'] ?? array(),
    'employment_bands' => $result['employment_bands'] ?? array(),
    'employment_levels' => $result['employment_levels'] ?? array(),
    'employment_roles' => $result['employment_roles'] ?? array(),
    'marital_statuses' => $result['marital_statuses'] ?? array(),
    'blood_groups' => $result['blood_groups'] ?? array(),
    'verticals' => $result['verticals'] ?? array(),
    'gender_details' => $result['gender_details'] ?? array(),
    'employeement_types' => $result['employeement_types'] ?? array(),
    'education_type' => $result['education_type'] ?? array(),
    'banks' => $result['banks'] ?? array(),
    'emp_educations' => $result['emp_educations'] ?? array(),
    'emp_dependents' => $result['emp_dependents'] ?? array(),
    'emp_contacts' => $result['emp_contacts'] ?? array(),
    'emp_work_experiences' => $result['emp_work_experiences'] ?? array(),
    'relations' => $result['relations'] ?? array(),
    'application_users' => $result['application_users'] ?? array(),
    'all_employees' => $result['all_employees'] ?? array(),
    'countries' => $result['countries'] ?? array(),
    'emp_visa' => $result['emp_visa'] ?? array(),
    'emp_skills' => $result['emp_skills'] ?? array(),
    'skills' => $result['skills'] ?? array(),
    'emp_banks' => $result['emp_banks'] ?? array(),
    'emp_bank_status' => $result['emp_bank_status'] ?? array(),
    'statuse' => ViewStatusData(),
    'billable' => array("0"=>"Yes","1"=>"No"," "=>"Not Defined"),
    'yesno' => array('0'=>'Yes','1'=>'No'),
    'agecalc' => $agecalc,
    'bank_work_flow' => "Y",
));
?>
<script>
    function tochangedeparment(){
        // Intentionally empty placeholder for department change callback
    }
    function employementviewhistoryppup(emp_id) {
        jQuery.ajax({
            url: "views/custom_view_employment_type_details.php",
            data:'emp_id='+encodeURIComponent(emp_id),
            type: "GET",
            success:function(data){
                $('#popupsq').html(data);
                $('#kt_modal_KTDatatable_remotesq').modal("show");
            },
            error:function (){
                // No-op error handler
            }
        });
    }
    function employeementtypepopup(emp_id, val) {
        jQuery.ajax({
            url: "views/custom_insert_employment_type_details.php",
            data:'emp_id='+encodeURIComponent(emp_id)+'&type='+encodeURIComponent(val),
            type: "GET",
            success:function(data){
                $('#popupsq').html(data);
                $('#kt_modal_KTDatatable_remotesq').modal("show");
            },
            error:function (){
                // No-op error handler
            }
        });
    }
</script>
<?php
$pageType = $_POST['pagetype'] ?? ($_POST['showpagetype'] ?? '');
if (($_POST['edittype'] ?? '') === "VIEW") {
    $viewMap = array(
        'basic_information' => 'renderOnbViewBasicInformation',
        'work_details' => 'renderOnbViewWorkDetails',
        'personal_details' => 'renderOnbViewPersonalDetails',
        'bank_details' => 'renderOnbViewBankDetails',
        'proof_details' => 'renderOnbViewProofDetails',
        'experience_details' => 'renderOnbViewExperienceDetails',
        'employee_qualifications' => 'renderOnbViewEmployeeQualifications',
        'employee_dependents' => 'renderOnbViewEmployeeDependents',
        'employee_contacts' => 'renderOnbViewEmployeeContacts',
        'employee_buddies' => 'renderOnbViewEmployeeBuddies',
        'employee_access' => 'renderOnbViewEmployeeAccess',
        'employee_other_access' => 'renderOnbViewEmployeeOtherAccess',
        'emp_visa' => 'renderOnbViewEmpVisa',
        'emp_skills' => 'renderOnbViewEmpSkills',
    );
    if (isset($viewMap[$pageType]) && function_exists($viewMap[$pageType])) {
        $viewMap[$pageType]($onbVars);
    }
} else {
    $editMap = array(
        'basic_information' => 'renderOnbEditBasicInformation',
        'work_details' => 'renderOnbEditWorkDetails',
        'personal_details' => 'renderOnbEditPersonalDetails',
        'bank_details' => 'renderOnbEditBankDetails',
        'employee_buddies' => 'renderOnbEditEmployeeBuddies',
        'employee_other_access' => 'renderOnbEditEmployeeOtherAccess',
        'employee_access' => 'renderOnbEditEmployeeAccess',
        'proof_details' => 'renderOnbEditProofDetails',
        'experience_details' => 'renderOnbEditExperienceDetails',
        'employee_qualifications' => 'renderOnbEditEmployeeQualifications',
        'employee_dependents' => 'renderOnbEditEmployeeDependents',
        'employee_contacts' => 'renderOnbEditEmployeeContacts',
        'emp_visa' => 'renderOnbEditEmpVisa',
    );
    if (isset($editMap[$pageType]) && function_exists($editMap[$pageType])) {
        $editMap[$pageType]($onbVars);
    }
}
}
}

if (!function_exists('renderOnbViewBasicInformation')) {
function renderOnbViewBasicInformation($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['basic_information'];
?>
        <div class="row">
            <?php if($fieldaccesright['field']['emp_code']!="NA"){
                renderOnbViewField('Employee Code', emptytextdashShow($data['emp_code']));
            }
            if($fieldaccesright['field']['email_id']!="NA"){
                renderOnbViewField('Email', emptytextdashShow(renderSensitiveDataWithToggle($data['email_id'], 'email')));
            }
            if($fieldaccesright['field']['first_name']!="NA"){
                renderOnbViewField('First Name', emptytextdashShow($data['first_name']));
            }
            if($fieldaccesright['field']['middle_name']!="NA"){
                renderOnbViewField('Middle Name', emptytextdashShow($data['middle_name']));
            }
            if($fieldaccesright['field']['last_name']!="NA"){
                renderOnbViewField('Last Name', emptytextdashShow($data['last_name']));
            }
            if($fieldaccesright['field']['phone']!="NA"){
                renderOnbViewField('Contact', emptytextdashShow(renderSensitiveDataWithToggle($data['phone'], 'mobile')));
            }
            if($fieldaccesright['field']['address']!="NA"){
                renderOnbViewField('Permanent Address', emptytextdashShow($data['address']));
            }
            if($fieldaccesright['field']['contact_address']!="NA"){
                renderOnbViewField('Current Address', emptytextdashShow($data['contact_address']));
            }
            if($fieldaccesright['field']['post_code']!="NA"){
                renderOnbViewField('Postcode', emptytextdashShow($data['post_code']));
            }
            if($fieldaccesright['field']['fax']!="NA"){
                renderOnbViewField('Fax', emptytextdashShow($data['fax']));
            } ?>
        </div>
<?php
}
}

if (!function_exists('renderOnbViewWorkOrgFields')) {
function renderOnbViewWorkOrgFields($fieldaccesright, $data, $lookups = array()) {
    $departments = $lookups['departments'] ?? array();
    $designations = $lookups['designations'] ?? array();
    $divisions = $lookups['divisions'] ?? array();
    $employment_bands = $lookups['employment_bands'] ?? array();
    $employment_levels = $lookups['employment_levels'] ?? array();
    $verticals = $lookups['verticals'] ?? array();
    if($fieldaccesright['field']['department_id']!="NA"){
        renderOnbViewField('Department', emptytextdashShow($departments[$data['department_id']]['department_name']));
    }
    if($fieldaccesright['field']['designation_id']!="NA"){
        renderOnbViewField('Designation', emptytextdashShow($designations[$data['designation_id']]['designation_name']));
    }
    if($fieldaccesright['field']['division_id']!="NA"){
        renderOnbViewField('Division', emptytextdashShow($divisions[$data['division_id']]['division_name']));
    }
    if($fieldaccesright['field']['band_id']!="NA"){
        renderOnbViewField('Band', emptytextdashShow($employment_bands[$data['band_id']]['band_name']));
    }
    if($fieldaccesright['field']['level_id']!="NA"){
        renderOnbViewField('level', emptytextdashShow($employment_levels[$data['level_id']]['level_name']));
    }
    if($fieldaccesright['field']['vertical_id']!="NA"){
        renderOnbViewField('Vertical', emptytextdashShow($verticals[$data['vertical_id']]['vertical_name']));
    }
}
}

if (!function_exists('renderOnbViewWorkExperienceFields')) {
function renderOnbViewWorkExperienceFields($fieldaccesright, $data) {
    if($fieldaccesright['field']['hiring_source']!="NA"){
        renderOnbViewField('Hiring Source', emptytextdashShow($data['hiring_source']));
    }
    if($fieldaccesright['field']['joining_date']!="NA"){
        renderOnbViewField('Date of Joining', $data['joining_date'] !=""?fromDate($data['joining_date']):"-");
    }
    if($fieldaccesright['field']['experience_current_organization']!="NA"){
        renderOnbViewField('Current Organization Experience', emptytextdashShow($data['experience_current_organization']));
    }
    if($fieldaccesright['field']['total_experiance']!="NA"){
        renderOnbViewField('Total Experience', $data['total_experiance'] !=""?$data['total_experiance']."- Year(s)":"-");
    }
    if($fieldaccesright['field']['relevant_experience']!="NA"){
        renderOnbViewField('Relevant Experience', $data['relevant_experience'] !=""?$data['relevant_experience']."- Year(s)":"-");
    }
    if($fieldaccesright['field']['notice_period']!="NA"){
        renderOnbViewField('Notice Period', $data['notice_period'] !=""?$data['notice_period']."- Day(s)":" - ");
    }
}
}

if (!function_exists('renderOnbViewWorkLocationFields')) {
function renderOnbViewWorkLocationFields($fieldaccesright, $data, $locations, $employeement_types, $billable) {
    if($fieldaccesright['field']['location_id']!="NA"){
        renderOnbViewField('Work Location', emptytextdashShow($locations[$data['location_id']]['location_name']));
    }
    if($fieldaccesright['field']['employee_type']!="NA"){
        renderOnbViewField('Employment Type', $data['employee_type']!=""?emptytextdashShow($employeement_types[$data['employee_type']]['genc_detail_name']):"-");
    }
    if($fieldaccesright['field']['billable']!="NA"){
        renderOnbViewField('Billable', emptytextdashShow($billable[$data['billable']]));
    }
    if($fieldaccesright['field']['office_phone']!="NA"){
        renderOnbViewField('Office Phone', emptytextdashShow($data['office_phone']));
    }
    if($fieldaccesright['field']['office_ext']!="NA"){
        renderOnbViewField('Extension', emptytextdashShow($data['office_ext']));
    }
}
}

if (!function_exists('renderOnbViewWorkDetails')) {
function renderOnbViewWorkDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $departments = $vars['departments'] ?? null;
    $data = $vars['data'] ?? null;
    $designations = $vars['designations'] ?? null;
    $divisions = $vars['divisions'] ?? null;
    $employment_bands = $vars['employment_bands'] ?? null;
    $employment_levels = $vars['employment_levels'] ?? null;
    $verticals = $vars['verticals'] ?? null;
    $locations = $vars['locations'] ?? null;
    $employeement_types = $vars['employeement_types'] ?? null;
    $billable = $vars['billable'] ?? null;
    $fieldaccesright = $vafield['work'];
?>
        <div class="row">
            <?php
            renderOnbViewWorkOrgFields($fieldaccesright, $data, compact('departments', 'designations', 'divisions', 'employment_bands', 'employment_levels', 'verticals'));
            renderOnbViewWorkExperienceFields($fieldaccesright, $data);
            renderOnbViewWorkLocationFields($fieldaccesright, $data, $locations, $employeement_types, $billable);
            ?>
        </div>
<?php
}
}

if (!function_exists('renderOnbViewPersonalBioFields')) {
function renderOnbViewPersonalBioFields($fieldaccesright, $data, $agecalc, $marital_statuses, $gender_details, $blood_groups) {
    if($fieldaccesright['field']['date_of_birth']!="NA"){
        renderOnbViewField('Date of Birth', dateFormate($data['date_of_birth']));
    }
    if($fieldaccesright['field']['age']!="NA"){
        renderOnbViewField('Age', $agecalc);
    }
    if($fieldaccesright['field']['marital_status']!="NA"){
        renderOnbViewField('Marital Status', emptytextdashShow($marital_statuses[$data['marital_status']]['marital_status_name']));
    }
    if($fieldaccesright['field']['wedding_date']!="NA"){
        $weddingStyle = $data['marital_status'] == "2" ? "" : CSS_DISPLAY_NONE;
        renderOnbViewField('Marrige Date', $data['wedding_date'] !=""?fromDate($data['wedding_date']):"-", $weddingStyle);
    }
    if($fieldaccesright['field']['gender']!="NA"){
        renderOnbViewField('Gender', emptytextdashShow($gender_details[$data['gender']]['genc_detail_name']));
    }
    if($fieldaccesright['field']['blood_group']!="NA"){
        renderOnbViewField('Blood Group', $blood_groups[$data['blood_group']]['blood_group']!=""?$blood_groups[$data['blood_group']]['blood_group']:"-");
    }
}
}

if (!function_exists('renderOnbViewPersonalOtherFields')) {
function renderOnbViewPersonalOtherFields($fieldaccesright, $data, $yesno) {
    if($fieldaccesright['field']['disability']!="NA"){
        renderOnbViewField('Disability', $data['disability'] !=""?$yesno[$data['disability']]:"-");
    }
    if($fieldaccesright['field']['disability_percentage']!="NA"){
        $disabilityStyle = $data['disability'] == "0" ? "" : CSS_DISPLAY_NONE;
        renderOnbViewField('Disability percentage', $data['disability_percentage'] !=""?$data['disability_percentage']:"-", $disabilityStyle);
    }
    if($fieldaccesright['field']['work_textile_industry']!="NA"){
        renderOnbViewField('Working In Textile Industry', $data['work_textile_industry'] !=""?$yesno[$data['work_textile_industry']]:"-");
    }
    if($fieldaccesright['field']['physically_challenged_children']!="NA"){
        renderOnbViewField('Physically Challenged Children', $data['physically_challenged_children'] !=""?$yesno[$data['physically_challenged_children']]:"-");
    }
    if($fieldaccesright['field']['soc_facebook']!="NA"){
        renderOnbViewField('Facebook Link', emptytextdashShow($data['soc_facebook']));
    }
    if($fieldaccesright['field']['soc_twitter']!="NA"){
        renderOnbViewField('Twitter Link', emptytextdashShow($data['soc_twitter']));
    }
    if($fieldaccesright['field']['soc_linkedin']!="NA"){
        renderOnbViewField('Linkedin Link', emptytextdashShow($data['soc_linkedin']));
    }
}
}

if (!function_exists('renderOnbViewPersonalDetails')) {
function renderOnbViewPersonalDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $agecalc = $vars['agecalc'] ?? null;
    $marital_statuses = $vars['marital_statuses'] ?? null;
    $gender_details = $vars['gender_details'] ?? null;
    $blood_groups = $vars['blood_groups'] ?? null;
    $yesno = $vars['yesno'] ?? null;
    $fieldaccesright = $vafield['personal_details'];
    ?>
        <div class="row">
            <?php
            renderOnbViewPersonalBioFields($fieldaccesright, $data, $agecalc, $marital_statuses, $gender_details, $blood_groups);
            renderOnbViewPersonalOtherFields($fieldaccesright, $data, $yesno);
            ?>
        </div>
<?php
}
}

if (!function_exists('renderOnbViewBankStatusBadge')) {
function renderOnbViewBankStatusBadge($emp_bank, $fieldaccesright, $statuse, $emp_bank_status) {
    if ($fieldaccesright['access'] == "V" || $fieldaccesright['field']['bank_details'] == "V") {
        return;
    }
    if ($emp_bank['activation_status'] == 'Y') {
        ?>
        <span class="form-label label label-warning float-end d-inline-block fw-bold"><?php echo __('Active'); ?></span>
        <?php
        return;
    }
    $visiblebtnstatus = array(ST_ACTIVE, ST_SEND_BACK, ST_CALLBACK);
    if (in_array($emp_bank['current_status'], (array)$visiblebtnstatus)) {
        ?>
        <button type="button" class="btn btn-link p-0 border-0 float-end cursor-pointer align-baseline" aria-label="<?= htmlspecialchars(__('Add bank request'), ENT_QUOTES, 'UTF-8'); ?>" onclick="addBankRequest('<?php echo $emp_bank['id']; ?>')"><i class="ti-pencil"></i></button>
        <?php
        return;
    }
    $stausresult = $statuse[$emp_bank['current_status']];
    ?>
    <span class="form-label label float-end d-inline-block fw-bold" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $emp_bank_status[$emp_bank['current_status']]['name']; ?></span>
    <?php
}
}

if (!function_exists('renderOnbViewBankWorkflowCards')) {
function renderOnbViewBankWorkflowCards($emp_banks, $fieldaccesright, $statuse, $emp_bank_status, $banks, $data) {
    if (count((array)$emp_banks) == "0") {
        renderOnbEmptyStateCard('No Employee Banks');
        return;
    }
    foreach ($emp_banks as $emp_bank) { ?>
        <div class="col-xl-4 col-md-4" >
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block  fw-bold"><?php echo __('Bank'); ?>
                        <?php renderOnbViewBankStatusBadge($emp_bank, $fieldaccesright, $statuse, $emp_bank_status); ?>
                    </span>
                    <p><?php echo $banks[$emp_bank['bank_id']]['bank_name']!=""?$banks[$emp_bank['bank_id']]['bank_name']:"-";?></p>

                    <span class="form-label d-block fw-bold"><?php echo __('IFSC Code'); ?></span>
                    <p> <?php echo renderSensitiveDataWithToggle($data['bank_link'], 'ifsc', false); ?></p>

                    <span class="form-label d-block fw-bold"><?php echo __('Account No'); ?></span>
                    <p> <?php echo renderSensitiveDataWithToggle($data['bank_account_no'], 'bank_account', false); ?></p>
                </div>
            </div>
        </div>
    <?php }
}
}

if (!function_exists('renderOnbViewBankDetails')) {
function renderOnbViewBankDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $bank_work_flow = $vars['bank_work_flow'] ?? null;
    $emp_banks = $vars['emp_banks'] ?? null;
    $statuse = $vars['statuse'] ?? null;
    $emp_bank_status = $vars['emp_bank_status'] ?? null;
    $banks = $vars['banks'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['bank_details'];
    ?>
        <div class="row">
            <?php
            if($bank_work_flow == 'Y'){
                renderOnbViewBankWorkflowCards($emp_banks, $fieldaccesright, $statuse, $emp_bank_status, $banks, $data);
            }else{
             if($fieldaccesright['field']['bank_id']!="NA"){
                renderOnbViewField('Bank', $banks[$data['bank_id']]['bank_name']!=""?$banks[$data['bank_id']]['bank_name']:"-");
              }
              if($fieldaccesright['field']['bank_link']!="NA"){
                renderOnbViewField('IFSC Code', emptytextdashShow($data['bank_link']));
              }
              if($fieldaccesright['field']['bank_account_no']!="NA"){
                renderOnbViewField('Account No', emptytextdashShow($data['bank_account_no']));
              }
          } ?>
        </div>

<?php
}
}

if (!function_exists('renderOnbViewProofRow')) {
function renderOnbViewProofRow($fieldaccesright, $fieldKey, $label, $valueHtml, $expiryText, $attachment) {
    if ($fieldaccesright['field'][$fieldKey] == "NA") {
        return;
    }
    ?>
    <tr>
        <th scope="row" class="p-2"><?php echo __($label); ?></th>
        <td class="p-2"><?php echo $valueHtml; ?></td>
        <td class="p-2"><?php echo $expiryText; ?></td>
        <td class="p-2">
        <?php if ($attachment != "") { ?>
            <a href="<?php echo ONB_UPLOADS_DIR . onbEsc($attachment); ?>" class="text-primary mt-2 f-14" aria-label="<?php echo onbEsc(__($label) . ' ' . __('File')); ?>" download><i class="ti-cloud-down"></i> </a>
        <?php } else {
            echo "-";
        } ?>
        </td>
    </tr>
    <?php
}
}

if (!function_exists('renderOnbViewProofDetails')) {
function renderOnbViewProofDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['proof_details'];
    $attached_files = json_decode((string)($data['attached_files'] ?? ''), true);
    ?>
        <table class="table">
               <thead>
                  <tr>
                     <th scope="col"><?php echo __('Type'); ?></th>
                     <th scope="col"><?php echo __('Number'); ?></th>
                     <th scope="col"><?php echo __('Expiry Date'); ?></th>
                     <th scope="col"><?php echo __('File'); ?></th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                    renderOnbViewProofRow($fieldaccesright, 'adhar_no', 'Aadhar Card', renderSensitiveDataWithToggle($data['adhar_no'], 'aadhaar'), ' - ', $attached_files['aadhar_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'passport', 'Passport', renderSensitiveDataWithToggle($data['passport'], 'passport'), onbFormatDate($data['passport_expiry_date'], ' - '), $attached_files['passport_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'driving_licence', STR_DRIVING_LICENCE, renderSensitiveDataWithToggle($data['driving_licence'], 'driving_licence'), onbFormatDate($data['driving_licence_expiry_date'], ' - '), $attached_files['driving_licence_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'pan_number', 'PAN Number', renderSensitiveDataWithToggle($data['pan_number'], 'pan'), ' - ', $attached_files['pan_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'pf_no', 'PF Number', renderSensitiveDataWithToggle($data['pf_no'], 'pf_number'), ' - ', $attached_files['pf_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'pf_uan', 'UAN Number', renderSensitiveDataWithToggle($data['pf_uan'], 'uan_number'), ' - ', $attached_files['pf_uan_attachment']);
                    renderOnbViewProofRow($fieldaccesright, 'gosi_number', 'GOSI Number', renderSensitiveDataWithToggle($data['gosi_number'], 'gosi_number'), ' - ', $attached_files['gosi_attachment']);
                  ?>
               </tbody>
        </table>
<?php
}
}

if (!function_exists('renderOnbViewExperienceDetails')) {
function renderOnbViewExperienceDetails($vars) {
    $emp_work_experiences = $vars['emp_work_experiences'] ?? null;
    if(count((array)$emp_work_experiences) !="0"){
    ?>
    <div class="card-body border-bottom ">
        <div class="card-block">
            <div class="scroll-widget">
                  <div class="latest-update-box">
                  <?php
                  foreach ($emp_work_experiences as $expe) {  ?>
                    <div class="row p-t-20 p-b-30">
                        <div class="col-auto text-end update-meta p-r-0">
                            <i class="b-primary update-icon ring"></i>
                        </div>
                        <div class="col p-l-5">
                            <a href="#!"><h6><?php echo $expe['company_name']; ?></h6></a>
                            <p class="text-muted m-b-0"><?php echo fromDate($expe['work_from']); ?> <?php echo __('To'); ?> <?php echo fromDate($expe['work_to']); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>

                  </div>
            </div>
        </div>
    </div>
    <?php }else{
        renderOnbEmptyStateCard('No Employment History');
    }
}
}

if (!function_exists('renderOnbViewEmployeeQualifications')) {
function renderOnbViewEmployeeQualifications($vars) {
    $emp_educations = $vars['emp_educations'] ?? null;
    $education_type = $vars['education_type'] ?? null;
    if(count((array)$emp_educations) !="0"){
    ?>

    <div class="card-body border-bottom ">
        <div class="card-block">
            <div class="scroll-widget">
                  <div class="latest-update-box">
                      <?php
                      foreach ($emp_educations as $emp_edu) {  ?>
                    <div class="row p-t-20 p-b-30">
                        <div class="col-auto text-end update-meta p-r-0">
                            <i class="b-primary update-icon ring"></i>
                        </div>
                        <div class="col p-l-5">
                            <h6 class="mb-1"><?php echo emptytextdashShow($emp_edu['institute_name']); ?></h6>
                            <span class="text-muted m-b-0 f-w-600 f-12"><?php echo $education_type[$emp_edu['education_type']]['genc_detail_name']; ?> - (<?php echo $emp_edu['score']; ?>) </span>
                            <p class="text-muted m-b-0"><?php echo fromDate($emp_edu['start_date']); ?> <?php echo __('To'); ?> <?php echo fromDate($emp_edu['end_date']); ?>

                            </p>
                        </div>
                    </div>
                <?php } ?>
                  </div>
            </div>
        </div>
    </div>
    <?php }else{
        renderOnbEmptyStateCard('No Educational Details');
    }
}
}

if (!function_exists('renderOnbViewEmployeeDependents')) {
function renderOnbViewEmployeeDependents($vars) {
    $emp_dependents = $vars['emp_dependents'] ?? null;
    $relations = $vars['relations'] ?? null;
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_dependents) !="0"){
              foreach ($emp_dependents as $emp_dep) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Name'); ?></span>
                    <p><?php echo emptytextdashShow($emp_dep['dependent_name']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Relationship Type'); ?></span>
                       <p><?php echo $relations[$emp_dep['dependent_relationship_type']]['relation_name']; ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Date Of Birth'); ?></span>
                       <p><?php echo fromDate($emp_dep['dependent_date_of_birth']); ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php }else{
            renderOnbEmptyStateCard('No Dependents');
        } ?>
    </div>
<?php
}
}

if (!function_exists('renderOnbViewEmployeeContacts')) {
function renderOnbViewEmployeeContacts($vars) {
    $emp_contacts = $vars['emp_contacts'] ?? null;
    $relations = $vars['relations'] ?? null;
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_contacts) !="0"){
            foreach ($emp_contacts as $emp_con) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Name'); ?></span>
                    <p><?php echo emptytextdashShow($emp_con['contact_name']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Relationship Type'); ?></span>
                       <p><?php echo emptytextdashShow($relations[$emp_con['contact_relationship']]['relation_name']); ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Home No'); ?></span>
                       <p><?php echo emptytextdashShow($emp_con['contact_home_no']); ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Mobile No'); ?></span>
                       <p><?php echo emptytextdashShow($emp_con['contact_mobile_no']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{
        renderOnbEmptyStateCard('No Emergency Contact');
    } ?>
    </div>
<?php
}
}

if (!function_exists('renderOnbViewEmployeeBuddies')) {
function renderOnbViewEmployeeBuddies($vars) {
    $vafield = $vars['vafield'] ?? null;
    $employment_roles = $vars['employment_roles'] ?? null;
    $data = $vars['data'] ?? null;
    $application_users = $vars['application_users'] ?? null;
    $all_employees = $vars['all_employees'] ?? null;
    $fieldaccesright = $vafield['employee_buddies'];
    ?>
        <div class="row">
            <?php if($fieldaccesright['field']['role_id']!="NA"){
                renderOnbViewField('Employment Role', $employment_roles[$data['role_id']]['role_name']!=""?$employment_roles[$data['role_id']]['role_name']:"-");
              }
              if($fieldaccesright['field']['reporting_manager_id']!="NA"){
                renderOnbViewField('Repoting To', $application_users[$data['reporting_manager_id']]['display_name']!=""?$application_users[$data['reporting_manager_id']]['display_name']:"-");
              }
              if($fieldaccesright['field']['admin_buddy']!="NA"){
                renderOnbViewField('Admin Buddy', $all_employees[$data['admin_buddy']]['display_name']!=""?$all_employees[$data['admin_buddy']]['display_name']:"-");
              }
              if($fieldaccesright['field']['hr_buddy']!="NA"){
                renderOnbViewField('HR Buddy', $all_employees[$data['hr_buddy']]['display_name']!=""?$all_employees[$data['hr_buddy']]['display_name']:"-");
              }
              if($fieldaccesright['field']['finance_buddy']!="NA"){
                renderOnbViewField('Finance Buddy', $all_employees[$data['finance_buddy']]['display_name']!=""?$all_employees[$data['finance_buddy']]['display_name']:"-");
              }
              if($fieldaccesright['field']['project_buddy']!="NA"){
                renderOnbViewField('Project Buddy', $all_employees[$data['project_buddy']]['display_name']!=""?$all_employees[$data['project_buddy']]['display_name']:"-");
              }
              if($fieldaccesright['field']['loc_buddy']!="NA"){
                renderOnbViewField('Location Buddy', $all_employees[$data['loc_buddy']]['display_name']!=""?$all_employees[$data['loc_buddy']]['display_name']:"-");
              } ?>
        </div>

<!-- added by koti start -->
<?php
}
}

if (!function_exists('renderOnbViewEmployeeAccess')) {
function renderOnbViewEmployeeAccess($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['employee_access'];
    ?>
        <div class="row">
            <?php if($fieldaccesright['field']['cover_under_pf']!="NA"){
                renderOnbViewField('Cover Under PF', $data['cover_under_pf']!="" && $data['cover_under_pf'] == '0' ? "YES":"NO");
              }
              if($fieldaccesright['field']['cover_under_esi']!="NA"){
                renderOnbViewField('Cover Under ESI', $data['cover_under_esi']!="" && $data['cover_under_esi'] == '0'?"YES":"NO");
              }
              if($fieldaccesright['field']['clock_event_type']!="NA"){
                renderOnbViewField('ClockIn & ClockOut Access', $data['clock_event_type']!="" && $data['clock_event_type'] == 'Y'?"YES":"NO");
              }
              if($fieldaccesright['field']['b_machine_id']!="NA"){
                renderOnbViewField('B Machine Id', $data['b_machine_id']!="" ? $data['b_machine_id']:"-");
              } ?>
        </div>
<!-- added by koti end -->

<?php
}
}

if (!function_exists('renderOnbViewEmployeeOtherAccess')) {
function renderOnbViewEmployeeOtherAccess($vars) {
    $vafield = $vars['vafield'] ?? null;
    $designations = $vars['designations'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['employee_other_access']; ?>
        <div class="row">
            <?php if($fieldaccesright['field']['job_title_id']!="NA"){
                renderOnbViewField('Job Title', emptytextdashShow($designations[$data['job_title_id']]['job_title']));
              }
              if($fieldaccesright['field']['is_muslim']!="NA"){
                renderOnbViewField('Are You Muslim ?', $data['is_muslim']!="" && $data['is_muslim'] == '0'?"YES":"NO");
              }
              if($fieldaccesright['field']['is_local']!="NA"){
                renderOnbViewField('Are You Local ?', $data['is_local']!="" && $data['is_local'] == 'Y'?"YES":"NO");
              } ?>
        </div>
<?php
}
}

if (!function_exists('renderOnbViewEmpVisa')) {
function renderOnbViewEmpVisa($vars) {
    $emp_visa = $vars['emp_visa'] ?? null;
    $countries = $vars['countries'] ?? null;
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_visa) !="0"){
            foreach ($emp_visa as $emp_v) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('VISA Number'); ?></span>
                    <p><?php echo emptytextdashShow($emp_v['visa_number']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Country'); ?></span>
                       <p><?php echo emptytextdashShow($countries[$emp_v['country_id']]['country_name']); ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Issue Date'); ?></span>
                       <p><?php echo $emp_v['visa_issue_date'] !=""?fromDate($emp_v['visa_issue_date']):" - "; ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Expiry Date'); ?></span>
                       <p><?php echo $emp_v['visa_expiry_date'] !=""?fromDate($emp_v['visa_expiry_date']):"-"; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{
        renderOnbEmptyStateCard('No VISA Details');
    } ?>
    </div>

<?php
}
}

if (!function_exists('renderOnbViewEmpSkills')) {
function renderOnbViewEmpSkills($vars) {
    $vafield = $vars['vafield'] ?? null;
    $emp_skills = $vars['emp_skills'] ?? null;
    $skills = $vars['skills'] ?? null;
    $fieldaccesright = $vafield['emp_skills'];
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_skills) !="0"){
            foreach ($emp_skills as $emp_skil) {  ?>
        <div class="col-xl-6 col-md-6" >
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block fw-bold"><?php echo __('Skill'); ?> <?php if($fieldaccesright['field']['emp_skills']!="V"){  ?><button type="button" class="btn btn-link p-0 border-0 float-end cursor-pointer align-baseline" aria-label="<?= htmlspecialchars(__('Skillpopup new'), ENT_QUOTES, 'UTF-8'); ?>" onclick="skillpopupNew('<?php echo $emp_skil['id']; ?>')"><i class="ti-pencil"></i></button><?php } ?></span>
                       <p><?php echo emptytextdashShow($skills[$emp_skil['skill_id']]['skill']); ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('Years Of Experience'); ?></span>
                    <p><?php echo emptytextdashShow($emp_skil['years_of_exp']); ?></p>
                    <span class="form-label d-block fw-bold"><?php echo __('Rating'); ?></span>
                       <p><?php echo $skills[$emp_skil['skill_id']]['max_ratting']."/".emptytextdashShow($emp_skil['rating']); ?></p>
                       <span class="form-label d-block fw-bold"><?php echo __('comments'); ?></span>
                       <p><?php echo emptytextdashShow($emp_skil['comments']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{
        renderOnbEmptyStateCard('No Skills');
    } ?>
    </div>
    <?php
}
}

if (!function_exists('renderOnbEditBasicInformation')) {
function renderOnbEditBasicInformation($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['basic_information'];
    ?>
        <div class="row">
        <?php if($fieldaccesright['field']['emp_code']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'emp_code'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Employee Code'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Emp code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="emp_code" value="<?php echo onbEsc($data['emp_code'] ?? ''); ?>" readonly/>
            </div>
        <?php }
        if($fieldaccesright['field']['email_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'email_id'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Email'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Email id'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter email'); ?>" name="email_id" value="<?php echo onbEsc($data['email_id'] ?? ''); ?>" readonly/>
            </div>
        <?php }
        if($fieldaccesright['field']['first_name']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'first_name'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('First Name'); ?>  </span>
                <input aria-label="<?= htmlspecialchars(__('First name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter first Name'); ?>" name="first_name" value="<?php echo onbEsc($data['first_name'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['middle_name']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'middle_name'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Middle Name'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Middle name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter middle Name'); ?>" name="middle_name" value="<?php echo onbEsc($data['middle_name'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['last_name']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'last_name'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Last Name'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Last name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter last Name'); ?>" name="last_name" value="<?php echo onbEsc($data['last_name'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['phone']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'phone'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Contact'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Phone'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter Contact'); ?>" name="phone" value="<?php echo onbEsc($data['phone'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['address']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'address'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Permanent Address'); ?> </span>
                <textarea aria-label="<?= htmlspecialchars(__('Address'), ENT_QUOTES, 'UTF-8'); ?>" style="overflow:auto" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your permanent address'); ?>" name="address"><?php echo onbEsc($data['address'] ?? '');?></textarea>
            </div>
         <?php }
         if($fieldaccesright['field']['contact_address']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'contact_address'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Current Address'); ?> </span>
                <textarea aria-label="<?= htmlspecialchars(__('Contact address'), ENT_QUOTES, 'UTF-8'); ?>" style="overflow:auto" class="form-control <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your current address'); ?>" name="contact_address"><?php echo onbEsc($data['contact_address'] ?? '');?></textarea>
            </div>
        <?php }
        if($fieldaccesright['field']['post_code']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'post_code'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Postcode'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Post code'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control <?php echo $readonlycheck; ?>" placeholder="<?php echo __('Enter your postcode'); ?>" name="post_code" value="<?php echo onbEsc($data['post_code'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['fax']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'fax'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Fax'); ?> </span>
                <input aria-label="<?= htmlspecialchars(__('Fax'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter fax'); ?>" name="fax" value="<?php echo onbEsc($data['fax'] ?? ''); ?>"/>
            </div>
        <?php }
        if($fieldaccesright['field']['image']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'image'); ?>
            <div class="mt-2 col-lg-2 <?php echo $readonlycheck; ?>">
                <span class="form-label d-inline-block fw-bold"><?php echo __('Profile Image'); ?></span>
                <div class="col-lg-9 col-xl-6">
                    <div class="profile-pic">
                        <label class="-label" for="profile_image">
                            <span> <i class="fa fa-camera"></i> <?php echo __('&nbsp; Change Image'); ?></span>
                        </label>
                        <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                        <img alt="<?= htmlspecialchars(__('Employee'), ENT_QUOTES, 'UTF-8'); ?>" src="<?php echo ONB_UPLOADS_DIR . onbEsc($data['image'] ?? ''); ?>" id="outputprofile_image" width="200" data-onerror-src="uploads/all/no-image.jpg"/>
                        <input type="hidden" name="old_profile_image" value="<?php echo onbEsc($data['image'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        <?php } ?>


        </div>

<?php
}
}

if (!function_exists('renderOnbEditWorkOrgFields')) {
function renderOnbEditWorkOrgFields($fieldaccesright, $data, $lookups) {
    $departments = $lookups['departments'] ?? array();
    $designations = $lookups['designations'] ?? array();
    $divisions = $lookups['divisions'] ?? array();
    $employment_bands = $lookups['employment_bands'] ?? array();
    $employment_levels = $lookups['employment_levels'] ?? array();
    $verticals = $lookups['verticals'] ?? array();
    if($fieldaccesright['field']['department_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'department_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Department'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Department id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
                      <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                      <?php renderOnbSelectOptions($departments, $data['department_id'], 'id', 'department_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['designation_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'designation_id'); ?>
           <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Designation'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Designation id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="designation_id">
                      <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                      <?php renderOnbSelectOptions($designations, $data['designation_id'], 'id', 'designation_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['division_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'division_id'); ?>
           <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Division'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Designation id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" onclick="tochangedeparment(this.value)" onkeydown="if(event.key==='Enter'){tochangedeparment(this.value);}" name="designation_id">
                      <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                      <?php renderOnbSelectOptions($divisions, $data['division_id'], 'id', 'division_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['band_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'band_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Band'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Band id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="band_id" onchange="getBandid(this.value)">
                    <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                    <?php renderOnbSelectOptions($employment_bands, $data['band_id'], 'id', 'band_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['level_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'level_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Level'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Level id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control js-example-basic-single" name="level_id" id="employeement_level">
                      <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                      <?php renderOnbSelectOptions($employment_levels, $data['level_id'], 'id', 'level_name', true); ?>
              </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['vertical_id']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'vertical_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Vertical'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Vertical id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
                    <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                    <?php renderOnbSelectOptions($verticals, $data['vertical_id'], 'id', 'vertical_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
}
}

if (!function_exists('renderOnbEditWorkExperienceFields')) {
function renderOnbEditWorkExperienceFields($fieldaccesright, $data) {
    if($fieldaccesright['field']['hiring_source']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'hiring_source'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Hiring Source'); ?>  </span>
            <input aria-label="<?= htmlspecialchars(__('Hiring source'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="<?php echo __('Enter Hiring Source'); ?>" name="hiring_source" value="<?php echo $data['hiring_source']; ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['joining_date']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'joining_date'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Date of Joining'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                  <input aria-label="<?= htmlspecialchars(__('Joining date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" value="<?php echo onbFormatDate($data['joining_date']); ?>"  name="joining_date" placeholder="<?php echo __('Enter Date of Joining'); ?>"/>
              </div>
        </div>
        <?php }
        if($fieldaccesright['field']['experience_current_organization']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'experience_current_organization'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Experience in Current Organization'); ?> </span>
            <input aria-label="<?= htmlspecialchars(__('Experience current organization'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Experience in Current Organization'); ?>" name="experience_current_organization" value="<?php echo $data['experience_current_organization']; ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['total_experiance']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'total_experiance'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Total Experience'); ?> </span>
            <div class="input-group input-group-button">
                <input aria-label="<?= htmlspecialchars(__('Total experiance'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
                <span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Years'); ?></span></span>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['relevant_experience']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'relevant_experience'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Relevant Experience'); ?> </span>
            <div class="input-group input-group-button">
                <input aria-label="<?= htmlspecialchars(__('Relevant experience'), ENT_QUOTES, 'UTF-8'); ?>"   type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Relevant Experience'); ?>"  name="relevant_experience" value="<?php echo $data['relevant_experience']; ?>"/>
                <span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Years'); ?></span></span>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['notice_period']!="NA"){
          $readonlycheck = onbReadonlyClass($fieldaccesright, 'notice_period'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Notice Period'); ?> </span>
            <div class="input-group input-group-button">
               <input aria-label="<?= htmlspecialchars(__('Notice period'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control width1 <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Notice Period'); ?>" name="notice_period" value="<?php echo $data['notice_period']; ?>" />
               <span class="input-group-text addon-style"><span class=" d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Days'); ?></span></span>
            </div>
        </div>
        <?php }
}
}

if (!function_exists('renderOnbEditLocationSelect')) {
function renderOnbEditLocationSelect($fieldaccesright, $data, $locations) {
    if ($fieldaccesright['field']['location_id'] == "NA") {
        return;
    }
    $readonlycheck = onbReadonlyClass($fieldaccesright, 'location_id'); ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __('Work Location'); ?> </span>
        <div class="<?php echo $readonlycheck; ?>">
            <select aria-label="<?= htmlspecialchars(__('Location id'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control js-example-basic-single" name="location_id">
                <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                  <?php foreach ($locations as $loc) {
                    if($loc['parent_location_id'] =='') { ?>
                    <option <?php if($data['location_id'] == $loc['id']){ ?>selected <?php } ?> value="<?php echo onbEsc($loc['id']); ?>" >
                      <?php echo onbEsc($loc['location_name']); ?>
                    </option>
                <?php }
                } ?>
            </select>
        </div>
    </div>
    <?php
}
}

if (!function_exists('renderOnbEditEmploymentTypeRadio')) {
function renderOnbEditEmploymentTypeRadio($fieldaccesright, $data, $employeement_types, $safeEmpIdJs, $text_color_ety) {
    if ($fieldaccesright['field']['employee_type'] == "NA") {
        return;
    }
    $readonlycheck = onbReadonlyClass($fieldaccesright, 'employee_type'); ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __('Employment Type'); ?></span>
        <button type="button" class="float-end btn btn-success" onclick="employementviewhistoryppup(<?php echo $safeEmpIdJs; ?>)"><?php echo __('History'); ?></button>
        <div class="form-radio <?php echo $readonlycheck; ?>">
        <?php
         foreach ($employeement_types as $emptypes) {
            $safeEmpTypeJs = onbEsc(json_encode($emptypes['genc_detail_code']));
        ?>
          <div class="radio radio-inline">
           <label class="form-label <?php echo $text_color_ety; ?>">
             <input aria-label="Employee field" type="radio" onclick="employeementtypepopup(<?php echo $safeEmpIdJs . ',' . $safeEmpTypeJs; ?>)" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?> value="<?php echo onbEsc($emptypes['genc_detail_code']); ?>" style="display:none"/>
             <i class="helper"></i><?php echo __($emptypes['genc_detail_name']);?> </label>
           </div>
        <?php } ?>
        </div>
    </div>
    <?php
}
}

if (!function_exists('renderOnbEditWorkLocationFields')) {
function renderOnbEditWorkLocationFields($fieldaccesright, $data, $locations, $employee_id, $employeement_types, $text_color_ety, $text_color_ebi) {
    $safeEmpIdJs = onbEsc(json_encode($employee_id));
    renderOnbEditLocationSelect($fieldaccesright, $data, $locations);
    renderOnbEditEmploymentTypeRadio($fieldaccesright, $data, $employeement_types, $safeEmpIdJs, $text_color_ety);
    if($fieldaccesright['field']['billable']!="NA"){
      $readonlycheck = onbReadonlyClass($fieldaccesright, 'billable'); ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __('Billable'); ?> </span>
        <div class="form-radio <?php echo $readonlycheck; ?>">
           <div class="radio radio-inline">
                   <label class="form-label <?php echo $text_color_ebi; ?>">
                 <input aria-label="Billable" type="radio" name="billable" <?php if($data['billable'] ==0) {?> checked="checked" <?php } ?> value="0" style="display:none" />
                 <i class="helper"></i><?php echo __('Yes'); ?> </label>
            </div>
            <div class="radio radio-inline">
                <label class="form-label <?php echo $text_color_ebi; ?>">
                   <input aria-label="Billable" type="radio" name="billable" <?php if($data['billable'] ==1) {?> checked="checked" <?php } ?> value="1" style="display:none" />
                   <i class="helper"></i><?php echo __('No'); ?> </label>
            </div>
        </div>
    </div>
    <?php }
    if($fieldaccesright['field']['office_phone']!="NA"){
      $readonlycheck = onbReadonlyClass($fieldaccesright, 'office_phone'); ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __('Office Phone'); ?>  </span>
        <input aria-label="<?= htmlspecialchars(__('Office phone'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_phone" placeholder="<?php echo __('Enter Office Phone'); ?>" value="<?php echo onbEsc($data['office_phone'] ?? ''); ?>"/>
    </div>
    <?php }
    if($fieldaccesright['field']['office_ext']!="NA"){
      $readonlycheck = onbReadonlyClass($fieldaccesright, 'office_ext'); ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __('Extension'); ?> </span>
        <input aria-label="<?= htmlspecialchars(__('Office ext'), ENT_QUOTES, 'UTF-8'); ?>"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_ext" placeholder="<?php echo __('Enter Extension'); ?>" value="<?php echo onbEsc($data['office_ext'] ?? ''); ?>"/>
    </div>
    <?php }
}
}

if (!function_exists('renderOnbEditWorkDetails')) {
function renderOnbEditWorkDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $departments = $vars['departments'] ?? null;
    $data = $vars['data'] ?? null;
    $designations = $vars['designations'] ?? null;
    $divisions = $vars['divisions'] ?? null;
    $employment_bands = $vars['employment_bands'] ?? null;
    $employment_levels = $vars['employment_levels'] ?? null;
    $verticals = $vars['verticals'] ?? null;
    $locations = $vars['locations'] ?? null;
    $employee_id = $vars['employee_id'] ?? null;
    $employeement_types = $vars['employeement_types'] ?? null;
    $text_color_ety = $vars['text_color_ety'] ?? null;
    $text_color_ebi = $vars['text_color_ebi'] ?? null;
    $fieldaccesright = $vafield['work'];
?>
        <div class="row">
            <?php
            renderOnbEditWorkOrgFields($fieldaccesright, $data, compact('departments', 'designations', 'divisions', 'employment_bands', 'employment_levels', 'verticals'));
            renderOnbEditWorkExperienceFields($fieldaccesright, $data);
            renderOnbEditWorkLocationFields($fieldaccesright, $data, $locations, $employee_id, $employeement_types, $text_color_ety, $text_color_ebi);
            ?>
        </div>
<?php
}
}

if (!function_exists('renderOnbEditPersonalDetails')) {
function renderOnbEditPersonalDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $marital_statuses = $vars['marital_statuses'] ?? null;
    $fieldaccesright = $vafield['personal_details'];
    ?>
    <div class="row">
     <?php if($fieldaccesright['field']['date_of_birth']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'date_of_birth'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Date of Birth'); ?> </span>
            <div class="<?php echo $readonlycheck; ?>">
                <input aria-label="<?= htmlspecialchars(__('Date of birth'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control form-control-lg datepicker"  value="<?php echo onbFormatDate($data['date_of_birth']); ?>"  name="date_of_birth" placeholder="<?php echo __('Enter Date of Birth'); ?>" onchange="selectdateOfBirth(this.value)" />
            </div>
        </div>
        <?php } // Age field handled in employee profile details ?>
        <?php if($fieldaccesright['field']['marital_status']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'marital_status'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Marital Status'); ?></span>
            <div class="form-radio <?php echo $readonlycheck; ?>">
            <?php
                   foreach ($marital_statuses as $marital_stat) {
             ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                           <input aria-label="Marital Status" onclick="showmarriagedate(this.value);" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" type="radio" name="marital_status" <?php if($data['marital_status'] == $marital_stat['id']) {?> checked="checked" <?php } ?> value="<?php echo $marital_stat['id']; ?>" style="display:none"/>
                           <i class="helper"></i>
                           <?php echo $marital_stat['marital_status_name']; ?>
                     </label>
                   </div>
             <?php } ?>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['wedding_date']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'wedding_date'); ?>
        <div class="col-lg-6 " style="<?php echo $data['marital_status'] =="2"?"":CSS_DISPLAY_NONE; ?>"  id="marriagedateshow">
            <div class="mt-2" id="marriagedateshow1">
                  <span  class="form-label d-inline-block fw-bold"><?php echo __('Marrige Date'); ?></span>
                  <div class="<?php echo $readonlycheck; ?>">
                      <input aria-label="<?= htmlspecialchars(__('Wedding date'), ENT_QUOTES, 'UTF-8'); ?>" type="text"  class="form-control datepicker" name="wedding_date" id="wedding_date" value="<?php echo onbFormatDate($data['wedding_date']); ?>" placeholder="<?php echo __('Enter Marrige Date'); ?>"/>
                </div>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['gender']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'gender'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Gender'); ?></span>
            <div class="form-radio <?php echo $readonlycheck; ?>">
                   <?php
                       foreach ($gender_details as $gender_data) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label <?php echo $text_color_gen; ?>">
                       <input aria-label="Gender"  type="radio" name="gender" <?php if($data['gender'] == $gender_data['genc_detail_code']){ ?>  checked="checked" <?php } ?> value="<?php echo $gender_data['genc_detail_code'];?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo __($gender_data['genc_detail_name']); ?>
                     </label>
               </div>
             <?php } ?>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['blood_group']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'blood_group'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Blood Group'); ?></span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Blood group'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-select js-example-basic-single" name="blood_group">
                      <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                      <?php renderOnbSelectOptions($blood_groups, $data['blood_group'], 'id', 'blood_group', false); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['disability']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'disability'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Disability'); ?></span>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
               foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                       <input type="radio" name="disability" id="disability" onclick="selectDisability()" onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}" <?php if($data['disability'] == $ynkey || ($data['disability'] =="" && $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                     </label>
                   </div>
             <?php } ?>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['disability_percentage']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'disability_percentage'); ?>
        <div class="col-xl-6 mt-2" id="disabilitypercentage_id" style="<?php echo $data['disability'] =="0"?"":CSS_DISPLAY_NONE; ?>">
            <label class="form-label"><?php echo __('Disability percentage'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="" name="disability_percentage" value="<?php echo $data['disability_percentage']; ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['work_textile_industry']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'work_textile_industry'); ?>
        <div class="col-xl-6 mt-2" >
            <label class="form-label"><?php echo __('Working In Textile Industry'); ?></label>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
                   foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                       <input aria-label="Work Textile Industry"   type="radio" name="work_textile_industry" <?php if($data['work_textile_industry '] == $ynkey || ($data['work_textile_industry'] =="" && $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                    </label>
                   </div>
                 <?php } ?>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['physically_challenged_children']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'physically_challenged_children'); ?>
        <div class="col-xl-6 mt-2" >
            <label class="form-label"><?php echo __('Physically Challenged Children'); ?></label>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
               foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                       <input aria-label="Physically Challenged Children"   type="radio" name="physically_challenged_children" <?php if($data['physically_challenged_children'] == $ynkey || ($data['work_textile_industry'] =="" && $ynkey=='1')) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                     </label>
               </div>
             <?php } ?>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['soc_facebook']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'soc_facebook'); ?>
        <div class="col-lg-6 mt-2" >
            <label  class="form-label"><?php echo __('Facebook Link'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Facebook URL'); ?>" name="soc_facebook" value="<?php echo $data['soc_facebook']; ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['soc_twitter']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'soc_twitter'); ?>
        <div class="col-lg-6 mt-2" >
            <label  class="form-label"><?php echo __('Twitter Link'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter Twitter URL'); ?>" name="soc_twitter" value="<?php echo $data['soc_twitter']; ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['soc_linkedin']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'soc_linkedin'); ?>
        <div class="col-lg-6 mt-2" >
            <label  class="form-label"><?php echo __('Linkedin Link'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="<?php echo __('Enter LinkedIn URL'); ?>" name="soc_linkedin" value="<?php echo $data['soc_linkedin']; ?>"/>
        </div>
        <?php } ?>
    </div>
<?php
}
}

if (!function_exists('renderOnbEditBankDetails')) {
function renderOnbEditBankDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $bank_work_flow = $vars['bank_work_flow'] ?? null;
    $banks = $vars['banks'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['bank_details'];
    ?>
    <div class="row">
        <?php if($bank_work_flow == 'N'){
         if($fieldaccesright['field']['bank_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'bank_id'); ?>
        <div class="col-xl-6 mt-2" >
            <label class="form-label"><?php echo __('Bank'); ?></label>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?= htmlspecialchars(__('Bank'), ENT_QUOTES, 'UTF-8'); ?>" class ="form-control form-control-lg form-select js-example-basic-single"  name="bank_id">
                    <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                    <?php renderOnbSelectOptions($banks, $data['bank_id'], 'id', 'bank_name', false); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['bank_link']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'bank_link'); ?>
        <div class="col-lg-6 mt-2" >
            <label  class="form-label"><?php echo __('IFSC Code'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_link" value="<?php echo $data['bank_link']; ?>" placeholder="<?php echo __('Enter IFSC Code'); ?>"/>
        </div>
        <?php }
        if($fieldaccesright['field']['bank_account_no']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'bank_account_no'); ?>
        <div class="col-lg-6 mt-2" >
            <label  class="form-label"><?php echo __('Account No'); ?></label>
            <input aria-label="Employee field" type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_account_no" value="<?php echo $data['bank_account_no']; ?>" placeholder="<?php echo __('Enter Account No'); ?>"/>
        </div>
        <?php } ?>
    </div>

    <?php } ?>
<?php
}
}

if (!function_exists('renderOnbBuddySelectField')) {
function renderOnbBuddySelectField($fieldaccesright, $fieldKey, $label, $selectName, $employees, $selectedId, $readOnlyLn = '') {
    if ($fieldaccesright['field'][$fieldKey] == "NA") {
        return;
    }
    $readonlycheck = onbReadonlyClass($fieldaccesright, $fieldKey);
    ?>
    <div class="col-xl-6 mt-2" >
        <span class="form-label d-inline-block fw-bold"><?php echo __($label); ?></span>
        <div class="<?php echo $readonlycheck; ?>">
            <select aria-label="<?php echo onbEsc(__($label)); ?>" class="form-select js-example-basic-single required" <?php echo $readOnlyLn; ?> name="<?php echo $selectName; ?>">
                <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                <?php renderOnbSelectOptions($employees, $selectedId, 'id', 'display_name', false); ?>
            </select>
        </div>
    </div>
    <?php
}
}

if (!function_exists('renderOnbEditEmployeeBuddies')) {
function renderOnbEditEmployeeBuddies($vars) {
    $vafield = $vars['vafield'] ?? null;
    $employment_roles = $vars['employment_roles'] ?? null;
    $data = $vars['data'] ?? null;
    $application_users = $vars['application_users'] ?? null;
    $read_only_ln = $vars['read_only_ln'] ?? null;
    $all_employees = $vars['all_employees'] ?? null;
    $fieldaccesright = $vafield['employee_buddies'];
    ?>
    <div class="row">
        <?php if($fieldaccesright['field']['role_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'role_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Employment Role'); ?></span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?php echo onbEsc(__('Employment Role')); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
                    <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                    <?php renderOnbSelectOptions($employment_roles, $data['role_id'], 'id', 'role_name', true); ?>
                </select>
            </div>
        </div>
        <?php }
        if($fieldaccesright['field']['reporting_manager_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'reporting_manager_id'); ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label d-inline-block fw-bold"><?php echo __('Repoting to'); ?></span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="<?php echo onbEsc(__('Repoting to')); ?>" class ="form-control  js-example-basic-single" name="reporting_manager_id">
                    <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                    <?php renderOnbSelectOptions($application_users, $data['reporting_manager_id'], 'id', 'display_name', false); ?>
                </select>
            </div>
        </div>
        <?php }
        renderOnbBuddySelectField($fieldaccesright, 'admin_buddy', 'Admin Buddy', 'admin_buddy', $all_employees, $data['admin_buddy'], $read_only_ln);
        renderOnbBuddySelectField($fieldaccesright, 'hr_buddy', 'HR Buddy', 'hr_buddy', $all_employees, $data['hr_buddy'], $read_only_ln);
        renderOnbBuddySelectField($fieldaccesright, 'finance_buddy', 'Finance Buddy', 'finance_buddy', $all_employees, $data['finance_buddy'], $read_only_ln);
        renderOnbBuddySelectField($fieldaccesright, 'project_buddy', 'Project Buddy', 'project_buddy', $all_employees, $data['project_buddy']);
        renderOnbBuddySelectField($fieldaccesright, 'loc_buddy', 'Location Buddy', 'loc_buddy', $all_employees, $data['loc_buddy']);
        ?>
    </div>

<!-- added by koti start -->
<?php
}
}

if (!function_exists('renderOnbYesNoRadioGroup')) {
function renderOnbYesNoRadioGroup($fieldaccesright, $fieldKey, $label, $ariaLabel, $inputName, $currentValue, $vals = ['0', '1']) {
    $yesVal = $vals[0] ?? '0';
    $noVal = $vals[1] ?? '1';
    if ($fieldaccesright['field'][$fieldKey] == "NA") {
        return;
    }
    $readonlycheck = onbReadonlyClass($fieldaccesright, $fieldKey);
    $yesId = $fieldKey . '_yes';
    $noId = $fieldKey . '_no';
    ?>
    <div class="col-xl-6 mt-2">
        <span class="form-label d-block fw-bold"><?php echo __($label); ?></span>
        <div class="<?php echo $readonlycheck; ?>">
            <div class="form-check form-check-inline">
                <input id="<?php echo onbEsc($yesId); ?>" aria-label="<?php echo $ariaLabel; ?>" class="form-check-input"
                       type="radio"
                       name="<?php echo $inputName; ?>"
                       value="<?php echo $yesVal; ?>"
                       <?php if (isset($currentValue) && $currentValue === $yesVal) { echo 'checked'; } ?>>
                <label class="form-check-label" for="<?php echo onbEsc($yesId); ?>"><?php echo __('YES'); ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input id="<?php echo onbEsc($noId); ?>" aria-label="<?php echo $ariaLabel; ?>" class="form-check-input"
                       type="radio"
                       name="<?php echo $inputName; ?>"
                       value="<?php echo $noVal; ?>"
                       <?php if (isset($currentValue) && $currentValue === $noVal) { echo 'checked'; } ?>>
                <label class="form-check-label" for="<?php echo onbEsc($noId); ?>"><?php echo __('NO'); ?></label>
            </div>
        </div>
    </div>
    <?php
}
}

if (!function_exists('renderOnbEditEmployeeOtherAccess')) {
function renderOnbEditEmployeeOtherAccess($vars) {
    $vafield = $vars['vafield'] ?? null;
    $job_titles = $vars['job_titles'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['employee_other_access']; ?>
        <div class="row">
        <?php if($fieldaccesright['field']['job_title_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'job_title_id'); ?>
               <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('Job Title'); ?> </span>
                <div class="<?php echo $readonlycheck; ?>">
                   <select aria-label="<?php echo onbEsc(__('Job Title')); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="job_title_id">
                          <option value=""><?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                          <?php renderOnbSelectOptions($job_titles, $data['job_title_id'], 'id', 'job_title', true); ?>
                    </select>
                </div>
            </div>
            <?php }
        renderOnbYesNoRadioGroup($fieldaccesright, 'is_muslim', 'Are You Muslim ?', STR_COVER_UNDER_PF, 'cover_under_pf', $data['is_muslim'] ?? null);
        renderOnbYesNoRadioGroup($fieldaccesright, 'is_local', 'Are You Local ?', STR_COVER_UNDER_PF, 'cover_under_pf', $data['is_local'] ?? null);
        ?>

<?php
}
}

if (!function_exists('renderOnbEditEmployeeAccess')) {
function renderOnbEditEmployeeAccess($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['employee_access'];

    ?>
    <div class="row">
        <!-- Cover Under PF -->
        <?php renderOnbYesNoRadioGroup($fieldaccesright, 'cover_under_pf', 'Cover Under PF', STR_COVER_UNDER_PF, 'cover_under_pf', $data['cover_under_pf'] ?? null); ?>

        <!-- Cover Under ESI -->
        <?php renderOnbYesNoRadioGroup($fieldaccesright, 'cover_under_esi', 'Cover Under ESI', 'Cover Under Esi', 'cover_under_esi', $data['cover_under_esi'] ?? null); ?>

        <!-- Clock In & Clock Out Access -->
        <?php renderOnbYesNoRadioGroup($fieldaccesright, 'clock_event_type', 'ClockIn & ClockOut Access', 'Clock Event Type', 'clock_event_type', $data['clock_event_type'] ?? null, ['Y', 'N']); ?>

        <?php if($fieldaccesright['field']['b_machine_id']!="NA"){
              $readonlycheck = onbReadonlyClass($fieldaccesright, 'b_machine_id'); ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label d-inline-block fw-bold"><?php echo __('B Machine Id'); ?></span>
                <input aria-label="Employee field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="b_machine_id" value="<?php echo onbEsc($data['b_machine_id'] ?? ''); ?>" />
            </div>
        <?php } ?>

    </div>



<!-- added by koti end -->

<?php
}
}

if (!function_exists('renderOnbEditProofRow')) {
function renderOnbEditProofRow($fieldaccesright, $fieldKey, $label, $proofConfig = array()) {
    if ($fieldaccesright['field'][$fieldKey] == "NA") {
        return;
    }
    $readonlycheck = onbReadonlyClass($fieldaccesright, $fieldKey);
    $ariaLabel = $proofConfig['ariaLabel'] ?? '';
    $inputName = $proofConfig['inputName'] ?? '';
    $value = $proofConfig['value'] ?? '';
    $placeholder = $proofConfig['placeholder'] ?? '';
    $expiryHtml = $proofConfig['expiryHtml'] ?? '';
    $fileId = $proofConfig['fileId'] ?? '';
    $oldAttachmentName = $proofConfig['oldAttachmentName'] ?? '';
    $attachmentFile = $proofConfig['attachmentFile'] ?? '';
    ?>
    <tr class="<?php echo $readonlycheck; ?>">
        <th scope="row" class="p-2"><?php echo __($label); ?></th>
        <td class="p-2">
            <input aria-label="<?php echo $ariaLabel; ?>" type="text" name="<?php echo $inputName; ?>" class="form-control" value="<?php echo onbEsc($value); ?>" placeholder="<?php echo __($placeholder); ?>">
        </td>
        <td class="p-2"><?php echo $expiryHtml; ?></td>
        <td class="p-2">
            <input aria-label="<?php echo onbEsc(__($label) . ' ' . __('File')); ?>" class="form-control" type="file" id="<?php echo $fileId; ?>" value="" accept=".jpg,.png,.pdf">
            <input class="form-control " type="hidden" name="<?php echo $oldAttachmentName; ?>" value="<?php echo onbEsc($attachmentFile); ?>" >
            <?php if ($attachmentFile != "") { ?>
            <a href="<?php echo ONB_UPLOADS_DIR . onbEsc($attachmentFile); ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo onbEsc($attachmentFile); ?></a>
            <?php } ?>
        </td>
    </tr>
    <?php
}
}

if (!function_exists('renderOnbEditProofDetails')) {
function renderOnbEditProofDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $data = $vars['data'] ?? null;
    $fieldaccesright = $vafield['proof_details'];
    $attached_files = json_decode((string)($data['attached_files'] ?? ''), true);
    $passportExpiryHtml = '<input aria-label="Passport Expiry Date" type="text" name="passport_expiry_date" class="form-control datepicker" value="' . onbFormatDate($data['passport_expiry_date']) . '" placeholder="' . __('Enter Expiry Date') . '">';
    $dlExpiryHtml = '<input aria-label="Driving Licence Expiry Date" type="text" name="driving_licence_expiry_date" class="form-control datepicker" value="' . onbFormatDate($data['driving_licence_expiry_date']) . '" placeholder="' . __('Enter Driving Licence Expiry Date') . '">';
    ?>

    <table class="table table-vertical-center">
        <thead class="thead-light">
            <tr>
                <th class="text-center" scope="col"><?php echo __('Type'); ?></th>
                <th class="text-center"  scope="col"><?php echo __('Number'); ?></th>
                <th  class="text-center" scope="col"><?php echo __('Expiry Date'); ?></th>
                <th  class="text-center" scope="col"><?php echo __('File'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            renderOnbEditProofRow($fieldaccesright, 'adhar_no', 'Aadhar Card', ['ariaLabel' => 'Adhar No', 'inputName' => 'adhar_no', 'value' => dynamicDataDecode($data['adhar_no']), 'placeholder' => 'Enter Aadhar Card', 'expiryHtml' => ' - ', 'fileId' => 'aadhar_attachment', 'oldAttachmentName' => 'old_aadhar_attachment', 'attachmentFile' => $attached_files['aadhar_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'passport', 'Passport', ['ariaLabel' => 'Passport', 'inputName' => 'passport', 'value' => dynamicDataDecode($data['passport']), 'placeholder' => 'Enter Passport No.', 'expiryHtml' => $passportExpiryHtml, 'fileId' => 'passport_attachment', 'oldAttachmentName' => 'old_passport_attachment', 'attachmentFile' => $attached_files['passport_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'driving_licence', STR_DRIVING_LICENCE, ['ariaLabel' => STR_DRIVING_LICENCE, 'inputName' => 'driving_licence', 'value' => dynamicDataDecode($data['driving_licence']), 'placeholder' => 'Enter Driving Licence', 'expiryHtml' => $dlExpiryHtml, 'fileId' => 'driving_licence_attachment', 'oldAttachmentName' => 'old_driving_licence_attachment', 'attachmentFile' => $attached_files['driving_licence_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'pan_number', 'PAN Number', ['ariaLabel' => 'Pan Number', 'inputName' => 'pan_number', 'value' => dynamicDataDecode($data['pan_number']), 'placeholder' => 'Enter PAN Number', 'expiryHtml' => ' - ', 'fileId' => 'pan_attachment', 'oldAttachmentName' => 'old_pan_attachment', 'attachmentFile' => $attached_files['pan_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'pf_no', 'PF Number', ['ariaLabel' => 'Pf No', 'inputName' => 'pf_no', 'value' => dynamicDataDecode($data['pf_no']), 'placeholder' => 'Enter PF Number', 'expiryHtml' => ' - ', 'fileId' => 'pf_attachment', 'oldAttachmentName' => 'old_pf_attachment', 'attachmentFile' => $attached_files['pf_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'pf_uan', 'UAN Number', ['ariaLabel' => 'Pf Uan', 'inputName' => 'pf_uan', 'value' => dynamicDataDecode($data['pf_uan']), 'placeholder' => 'Enter UAN Number', 'expiryHtml' => ' - ', 'fileId' => 'pf_uan_attachment', 'oldAttachmentName' => 'old_pf_uan_attachment', 'attachmentFile' => $attached_files['pf_uan_attachment']]);
            renderOnbEditProofRow($fieldaccesright, 'gosi_number', 'GOSI Number', ['ariaLabel' => 'Gosi Number', 'inputName' => 'gosi_number', 'value' => dynamicDataDecode($data['gosi_number']), 'placeholder' => 'Enter GOSI Number', 'expiryHtml' => ' - ', 'fileId' => 'gosi_attachment', 'oldAttachmentName' => 'old_gosi_attachment', 'attachmentFile' => $attached_files['gosi_attachment']]);
            ?>
        </tbody>
    </table>

<?php
}
}

if (!function_exists('renderOnbEditExperienceDetails')) {
function renderOnbEditExperienceDetails($vars) {
    $vafield = $vars['vafield'] ?? null;
    $readonlycheck = $vars['readonlycheck'] ?? null;
    $emp_work_experiences = $vars['emp_work_experiences'] ?? null;
    $fieldaccesright = $vafield['experience_details'];
    if($fieldaccesright['field']['employee_experiances']!="NA"){
        $readonlycheck = onbReadonlyClass($fieldaccesright, 'employee_experiances');
    }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="extable">
        <thead>
            <tr>
                <th scope="col" style="width: 30%;"><?php echo __('Company Name'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 30%;"><?php echo __('Job Description'); ?></th>
                <th scope="col" style="width: 16%;"> <?php echo __('From Date'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 16%;"> <?php echo __('To Date'); ?></th>
                <th scope="col" style="width: 8%;"> <?php echo __('File'); ?> <span class="f-10"><?php echo __('(jpg,pdf)'); ?></span></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $k=1;
        if(count((array)$emp_work_experiences)=='0'){
            $emp_work_experiences[] = array();
        }
        foreach ($emp_work_experiences as $expe) {
            ?>
            <tr id="exp_details_<?php echo $k; ?>">
                <td class="p-2">
                    <input type="hidden" name="expe_rowid[]" class="form-control" autocomplete="off" value="<?php echo onbEsc($expe['id'] ?? ''); ?>">
                    <input aria-label="Company Name" type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo onbEsc($expe['company_name'] ?? ''); ?>" placeholder="<?php echo __('Enter Company Name'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Job Descrption" type="text" name="job_descrption[]" class="form-control" value="<?php echo onbEsc($expe['job_descrption'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Job Description'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Work From" type="text" name="work_from[]" class="form-control datepicker d-block"  value="<?php echo onbFormatDate($expe['work_from'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter From Date'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Work To" type="text" name="work_to[]" class="form-control datepicker" value="<?php echo onbFormatDate($expe['work_to'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter To Date'); ?>">
                </td>
                <td class="p-2 w-b-w-s">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="image-input image-input-empty image-input-outline" id="kt_image_exp_<?php echo $k; ?>">
                                 <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="<?php echo __('Upload'); ?>">
                                  <span><i class="fa fa-upload"></i></span>
                                  <input aria-label="Work Experience Attachment" type="file" name="work_experience_attachment" class="form-control" onchange="getAttachedFileName(this,'workexperiencefilename_<?php echo $k; ?>')">
                                  <input type="hidden" name="old_work_experience_attachment[]" value="<?php echo onbEsc($expe['file'] ?? ''); ?>">
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6 p-0">
                            <?php if(!empty($expe['file'])){ ?>
                                <i class="ti-eye cursor-pointer" role="button" tabindex="0" aria-label="<?php echo onbEsc(__(ONB_ATTACHMENTS_PREVIEW)); ?>" onclick="dynamicFilePriviews('<?php echo ONB_UPLOADS_DIR . onbEsc($expe['file']); ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo __(ONB_ATTACHMENTS_PREVIEW); ?>"></i>
                              <?php } ?>
                         </div>
                     </div>
                      <span id="workexperiencefilename_<?php echo $k; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" role="button" tabindex="0" aria-label="<?php echo onbEsc(__('Remove')); ?>" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('exp_details_<?php echo $k; ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-original-title="<?php echo __('Remove'); ?>">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $k++;
        }  ?>
        </tbody>
        </table>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows" type="button"><?php echo __(ONB_ADD_MORE); ?></button>
            </div>
        </div>


<?php
}
}

if (!function_exists('renderOnbEditEmployeeQualifications')) {
function renderOnbEditEmployeeQualifications($vars) {
    $vafield = $vars['vafield'] ?? null;
    $readonlycheck = $vars['readonlycheck'] ?? null;
    $emp_educations = $vars['emp_educations'] ?? null;
    $education_type = $vars['education_type'] ?? null;
    $fieldaccesright = $vafield['employee_qualifications'];
    if($fieldaccesright['field']['employee_qualifications']!="NA"){
        $readonlycheck = onbReadonlyClass($fieldaccesright, 'employee_qualifications');
    }
    ?>

    <table class="table <?php echo $readonlycheck; ?>" id="edutable">
        <thead>
            <tr>
                <th scope="col" class=""  style="width: 20%;"><?php echo __('Institute Name'); ?> <span class="text-danger">*</span></th>
                <th scope="col" class="thheader"  style="width: 20%;"><?php echo __('Type'); ?> <span class="text-danger">*</span></th>
                <th scope="col" class="thheader"  style="width: 15%;"> <?php echo __('Start Date'); ?> <span class="text-danger">*</span></th>
                <th scope="col" class="thheader"  style="width: 15%;"> <?php echo __('End Date'); ?></th>
                <th scope="col" class="thheader"  style="width: 10%;"><?php echo __('Score'); ?></th>
                <th scope="col" style="width: 10%;"> <?php echo __('File'); ?> <span class="f-10"><?php echo __('(jpg,pdf)'); ?></span></th>
                <th scope="col" style="width: 10%;"> </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(count((array)$emp_educations)=='0'){
            $emp_educations[] = array();
        }
        $mk = 1;
        foreach($emp_educations as $inst) { ?>
            <tr id="emp_educations_<?php echo $mk; ?>">
                <td class="p-2">
                    <input type="hidden" name="institute_rowid[]" class="form-control " autocomplete="off" value="<?php echo onbEsc($inst['id'] ?? ''); ?>">
                    <input aria-label="Institute Name" type="text" name="institute_name[]" class="form-control" value="<?php echo onbEsc($inst['institute_name'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Institute Name'); ?>">
                </td>
                <td class="w-full p-2">
                    <select aria-label="<?php echo onbEsc(__('Type')); ?>" class="form-control form-select js-example-basic-single" name="education_type[]" >
                                                <option value=""> <?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                        <?php renderOnbSelectOptions($education_type, $inst['education_type'] ?? '', 'genc_detail_code', 'genc_detail_name', true); ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Start Date" type="text" name="start_date[]" class="form-control datepicker" value="<?php echo onbFormatDateNonEmpty($inst['start_date'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Start date'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="End Date" type="text" name="end_date[]" class="form-control datepicker" value="<?php echo onbFormatDateNonEmpty($inst['end_date'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter End Date'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Score" type="text" name="score[]" class="form-control" value="<?php echo onbEsc($inst['score'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Score'); ?>">
                </td>
                <td class="p-2 w-b-w-s">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="image-input image-input-empty image-input-outline" id="kt_image_edu_<?php echo $mk; ?>">
                                 <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="<?php echo __('Upload'); ?>">
                                  <span><i class="fa fa-upload"></i></span>
                                  <input aria-label="Educations Attachment" type="file" name="educations_attachment" class="form-control" onchange="getAttachedFileName(this,'educationsfilename_<?php echo $mk; ?>')">
                                  <input type="hidden" name="old_educations_attachment[]" value="<?php echo onbEsc($inst['file'] ?? ''); ?>">
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6 p-0">
                            <?php if(!empty($inst['file'])){ ?>
                                <i class="ti-eye cursor-pointer" role="button" tabindex="0" aria-label="<?php echo onbEsc(__(ONB_ATTACHMENTS_PREVIEW)); ?>" onclick="dynamicFilePriviews('<?php echo ONB_UPLOADS_DIR . onbEsc($inst['file']); ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo __(ONB_ATTACHMENTS_PREVIEW); ?>"></i>
                              <?php } ?>
                         </div>
                     </div>
                      <span id="educationsfilename_<?php echo $mk; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" role="button" tabindex="0" aria-label="<?php echo onbEsc(__('Remove')); ?>" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_educations_<?php echo $mk; ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-original-title="<?php echo __('Remove'); ?>">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $mk++;
        } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success" onclick="addEmpEducationsRows()" id="addRows" type="button"><?php echo __(ONB_ADD_MORE); ?></button>
        </div>
    </div>

<?php
}
}

if (!function_exists('renderOnbEditEmployeeDependents')) {
function renderOnbEditEmployeeDependents($vars) {
    $vafield = $vars['vafield'] ?? null;
    $emp_dependents = $vars['emp_dependents'] ?? null;
    $relations = $vars['relations'] ?? null;
    $fieldaccesright = $vafield['employee_dependents'];
    $readonlycheck = "";
    if(isset($fieldaccesright['field']['employee_dependents']) && $fieldaccesright['field']['employee_dependents']!="NA"){
        $readonlycheck = onbReadonlyClass($fieldaccesright, 'employee_dependents');
    }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="deptable">
        <thead>
            <tr>
                <th scope="col" style="width: 30%;"><?php echo __('Name'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 30%;"><?php echo __('Relationship Type'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 30%;"><?php echo __('Date Of Birth'); ?></th>
                <th scope="col" style="width: 10%;"> </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(count((array)$emp_dependents)=='0'){
            $emp_dependents[] = array();
        }
        $dk = 1;
        foreach($emp_dependents as $depe) { ?>
            <tr id="employee_dependents_<?php echo $dk; ?>">
                <td class="p-2">
                    <input type="hidden" name="dependent_rowid[]" class="form-control" autocomplete="off" value="<?php echo onbEsc($depe['id'] ?? ''); ?>">
                    <input aria-label="Dependent Name" type="text" name="dependent_name[]" class="form-control" value="<?php echo onbEsc($depe['dependent_name'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Dependent Name'); ?>">
                </td>
                <td class="w-full p-2">
                    <select aria-label="<?php echo onbEsc(__('Relationship Type')); ?>" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
                        <option value=""> <?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                        <?php foreach ((array)$relations as $reladata) { ?>
                            <option <?php if(($depe['dependent_relationship_type'] ?? '') == $reladata['id']){ ?> selected <?php } ?> value="<?php echo onbEsc($reladata['id']); ?>"> <?php echo onbEsc(__(replaceNetTabAndsinglequote($reladata['relation_name']))); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Dependent Date Of Birth" type="text" name="dependent_date_of_birth[]" class="form-control datepicker" value="<?php echo onbFormatDateNonEmpty($depe['dependent_date_of_birth'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Date Of Birth'); ?>">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" role="button" tabindex="0" aria-label="<?php echo onbEsc(__('Remove')); ?>" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('employee_dependents_<?php echo $dk; ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-original-title="<?php echo __('Remove'); ?>">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $dk++;
        } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success" onclick="adddeprows()" id="addRows" type="button"><?php echo __(ONB_ADD_MORE); ?></button>
        </div>
    </div>

<?php
}
}

if (!function_exists('renderOnbEditEmployeeContacts')) {
function renderOnbEditEmployeeContacts($vars) {
    $vafield = $vars['vafield'] ?? null;
    $emp_contacts = $vars['emp_contacts'] ?? null;
    $relations = $vars['relations'] ?? null;
    $fieldaccesright = $vafield['employee_contacts'];
    $readonlycheck = "";
    if(isset($fieldaccesright['field']['employee_contacts']) && $fieldaccesright['field']['employee_contacts']!="NA"){
        $readonlycheck = onbReadonlyClass($fieldaccesright, 'employee_contacts');
    }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="emergetable">
        <thead>
            <tr>
                <th scope="col" style="width: 25%;"><?php echo __('Name'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 25%;"><?php echo __('Relationship'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 20%;"><?php echo __('Home No'); ?></th>
                <th scope="col" style="width: 20%;"><?php echo __('Mobile No'); ?></th>
                <th scope="col" style="width: 10%;"> </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(count((array)$emp_contacts)=='0'){
            $emp_contacts[] = array();
        }
        $ck = 1;
        foreach($emp_contacts as $eno) { ?>
            <tr id="emp_contacts_<?php echo $ck; ?>">
                <td class="p-2">
                    <input type="hidden" name="contact_rowid[]" class="form-control" autocomplete="off" value="<?php echo onbEsc($eno['id'] ?? ''); ?>">
                    <input aria-label="Contact Name" type="text" name="contact_name[]" class="form-control" value="<?php echo onbEsc($eno['contact_name'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Name'); ?>">
                </td>
                <td class="w-full p-2">
                    <select aria-label="<?php echo onbEsc(__('Relationship')); ?>" class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                        <option value=""> <?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                        <?php foreach ((array)$relations as $reladata) { ?>
                            <option <?php if(($eno['contact_relationship'] ?? '') == $reladata['id']){ ?> selected <?php } ?> value="<?php echo onbEsc($reladata['id']); ?>"> <?php echo onbEsc(__(replaceNetTabAndsinglequote($reladata['relation_name']))); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Contact Home No" type="text" name="contact_home_no[]" class="form-control" value="<?php echo onbEsc($eno['contact_home_no'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Home No'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Contact Mobile No" type="text" name="contact_mobile_no[]" class="form-control" value="<?php echo onbEsc($eno['contact_mobile_no'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Contact Mobile No'); ?>">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" role="button" tabindex="0" aria-label="<?php echo onbEsc(__('Remove')); ?>" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_contacts_<?php echo $ck; ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-original-title="<?php echo __('Remove'); ?>">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $ck++;
        } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success" onclick="addEmployeeContactsRows()" id="addRows" type="button"><?php echo __(ONB_ADD_MORE); ?></button>
        </div>
    </div>

<?php
}
}

if (!function_exists('renderOnbEditEmpVisa')) {
function renderOnbEditEmpVisa($vars) {
    $vafield = $vars['vafield'] ?? null;
    $emp_visa = $vars['emp_visa'] ?? null;
    $countries = $vars['countries'] ?? null;
    $fieldaccesright = $vafield['emp_visa'];
    $readonlycheck = "";
    if(isset($fieldaccesright['field']['emp_visa']) && $fieldaccesright['field']['emp_visa']!="NA"){
        $readonlycheck = onbReadonlyClass($fieldaccesright, 'emp_visa');
    }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="emp_visatable">
        <thead>
            <tr>
                <th scope="col" style="width: 30%;"><?php echo __('Visa Number'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 30%;"><?php echo __('Country'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 15%;"><?php echo __('Issue Date'); ?> <span class="text-danger">*</span></th>
                <th scope="col" style="width: 15%;"><?php echo __('Expiry Date'); ?></th>
                <th scope="col" style="width: 10%;"> </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(count((array)$emp_visa)=='0'){
            $emp_visa[] = array();
        }
        $vk = 1;
        foreach($emp_visa as $visa) { ?>
            <tr id="emp_visa_<?php echo $vk; ?>">
                <td class="p-2">
                    <input type="hidden" name="visa_rowid[]" class="form-control" autocomplete="off" value="<?php echo onbEsc($visa['id'] ?? ''); ?>">
                    <input aria-label="Visa Number" type="text" name="visa_number[]" class="form-control" value="<?php echo onbEsc($visa['visa_number'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Number'); ?>">
                </td>
                <td class="w-full p-2">
                    <select aria-label="<?php echo onbEsc(__('Country')); ?>" class="form-control form-select js-example-basic-single" name="country_id[]" >
                        <option value=""> <?php echo __(ONB_SELECT_ANY_ONE); ?></option>
                        <?php foreach ((array)$countries as $countrys) { ?>
                            <option <?php if(($visa['country_id'] ?? '') == $countrys['id']){ ?> selected <?php } ?> value="<?php echo onbEsc($countrys['id']); ?>"> <?php echo onbEsc(__(replaceNetTabAndsinglequote($countrys['country_name']))); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Visa Issue Date" type="text" name="visa_issue_date[]" class="form-control datepicker" value="<?php echo onbFormatDateNonEmpty($visa['visa_issue_date'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Issue Date'); ?>">
                </td>
                <td class="p-2">
                    <input aria-label="Visa Expiry Date" type="text" name="visa_expiry_date[]" class="form-control datepicker" value="<?php echo onbFormatDateNonEmpty($visa['visa_expiry_date'] ?? ''); ?>" autocomplete="off" placeholder="<?php echo __('Enter Visa Expiry Date'); ?>">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" role="button" tabindex="0" aria-label="<?php echo onbEsc(__('Remove')); ?>" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_visa_<?php echo $vk; ?>')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" data-bs-original-title="<?php echo __('Remove'); ?>">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $vk++;
        } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success" onclick="addEmpVisatableRows()" id="addRows" type="button"><?php echo __(ONB_ADD_MORE); ?></button>
        </div>
    </div>

<?php
}
}

if (empty($onboarding_employees_ajax_define_only)) {
    renderOnboardingEmployeesAjaxSection(array(
        'result' => $result ?? array(),
        'data' => $data ?? array(),
        'employee_id' => $employee_id ?? '',
        'text_color_ety' => $text_color_ety ?? '',
        'text_color_ebi' => $text_color_ebi ?? '',
        'text_color_gen' => $text_color_gen ?? '',
        'read_only_ln' => $read_only_ln ?? '',
        'job_titles' => $job_titles ?? array(),
    ));
}
?>
