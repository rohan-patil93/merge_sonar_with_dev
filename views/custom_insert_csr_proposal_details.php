<?php
defined('ATTR_NAME_GROUPS') || define('ATTR_NAME_GROUPS', '" name="groups[');
defined('ATTR_VAL_EMPTY_GROUP') || define('ATTR_VAL_EMPTY_GROUP', ']" value="');
defined('ATTR_DATA_GROUP') || define('ATTR_DATA_GROUP', '" data-group="');
$_SESSION['apply_js']['multiselest'] = 1;
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'csr_proposal_data';
    $url = API . "?mode=getCsrViewRelatedData&method=get&rid=" . $rid . "&table=" . $table_name;
    $che = callAPI($url);
    $data = json_decode($che, 1);
    $return = $data['csr_data'] ?? array();
    $csr_type_of_works = $data['csr_type_of_works'] ?? array();
    $csr_areas = $data['csr_areas'] ?? array();
    $csr_type_of_projects = $data['csr_type_of_projects'] ?? array();
    $locations = $data['locations'] ?? array();
    $entity_names = $data['entity_names'] ?? array();
    $financial_years = $data['financial_years'] ?? array();

 ?>

<link rel="stylesheet" href="assets/css/custom_insert_csr_proposal_details.css">
<div class="card mt-4">
    <div class="card-header general_instructions_3 d-none">
        <h5><?= __('CSR') ?></h5>
    </div>
    <div class="card-body">

        <?php
        $url = API . "?mode=getcsrsetting&method=get";
        $response = callAPI($url);
        $response = json_decode($response, 1);
        ?>
        <div class="general_instructions">
            <div class="card-header mb-3">
                <h5><?= __('General Instructions') ?></h5>
            </div>
            <div class="card-body">
                <?php
                $description = $response['description'];
                $pattern = '/(\d+\)\s)/'; // Matches the number with closing parenthesis and space
                $list_items = preg_split($pattern, $description, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

                // Display the list
                echo "<ul>";
                for ($i = 0; $i < count($list_items); $i += 2) {
                    $number = isset($list_items[$i]) ? $list_items[$i] : '';
                    $text = isset($list_items[$i + 1]) ? $list_items[$i + 1] : '';
                    echo "<li class='p-2'>" . trim($number . $text) . "</li>";
                }
                echo "</ul>";
                ?>
            </div>
            <div class="row">
                <div class="col-lg-12 text-end">
                    <button class="btn btn-primary waves-effect waves-light  m-r-30 mt-3 general_step_1"><?= __('Next') ?></button>
                </div>
            </div>
        </div>

        <div class="general_instructions_2 m-l-40 mt-4 d-none" id="printableArea">

            <form id="myForm" action="" method="post">
                <div class="row">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Date'); ?>: -</p>
                    </div>
                    <div class="col-lg-6">

        <input aria-label='<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>' name="date" class="form-control" type="date" id="date" value="<?php echo isset($return['date']) ? $return['date'] : date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Financial Year'); ?>: -</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='<?= htmlspecialchars(__('Financial Year'), ENT_QUOTES, 'UTF-8'); ?>' name="financial_year" id="financial_year" class="form-control form-select js-example-basic-single" required>
                            <option value=""><?= __('Financial Year') ?></option>

                            <?php foreach($financial_years as $row){
                                $selected = (isset($return['financial_year']) && $return['financial_year'] == $row['genc_detail_code']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $selected; ?>> <?php echo __($row['genc_detail_name']); ?></option>

                            <?php  } ?>

                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Entity Name'); ?>: -</p>
                    </div>
                    <div class="col-lg-6 w-full">

                        <select aria-label='<?= htmlspecialchars(__('Institue Name'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control js-example-basic-single requried_field" name="institue_name" id="institue_name">
                            <option value=""><?= __('Entity Name') ?></option>
                            <?php if(count((array)$entity_names)>0){
                                foreach ($entity_names as $row) {
                                    $selected=$row['entity_code']==$return['institue_name']?'selected':''
                                    ?>
                                    <option value="<?php echo $row['entity_code']; ?>" <?php echo $selected; ?>><?php echo $row['entity_code']; ?></option>
                            <?php } ?>
                            <?php } ?>

                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Type Of Work'); ?></p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='<?= htmlspecialchars(__('Type Of Work'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control js-example-basic-single requried_field" name="type_of_work" id="type_of_work" >
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach($csr_type_of_works as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['type_of_work'] == $row['id'] ? "selected":"";?>><?php echo $row['type_of_work']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Type Of Project'); ?></p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='<?= htmlspecialchars(__('Type Of Project'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control js-example-basic-single requried_field" name="type_of_project" id="type_of_project" >
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach($csr_type_of_projects as $row){ ?>
                                <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $return['type_of_project'] == $row['genc_detail_code'] ? "selected":"";?>><?php echo __($row['genc_detail_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('CSR Working Area'); ?></p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='<?= htmlspecialchars(__('Working Area'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control js-example-basic-single requried_field" name="working_area" id="working_area" >
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach($csr_areas as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['working_area'] == $row['id'] ? "selected":"";?>><?php echo $row['csr_area']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Location'); ?></p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='<?= htmlspecialchars(__('Csr Location'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control js-example-basic-single requried_field" name="csr_location" id="csr_location" >
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach($locations as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['csr_location'] == $row['id'] ? "selected":"";?>><?php echo $row['location_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600"><?= __('Project Name'); ?></p>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <textarea aria-label='<?= htmlspecialchars(__('Proposal for...'), ENT_QUOTES, 'UTF-8'); ?>' name="subject" id="subject" class="form-control height-80" placeholder="<?= __('Proposal for...') ?>">
                                <?php echo htmlspecialchars($return['subject']) ?? ''; ?>
                            </textarea>
                            <span class="f-12"><?= __('(Subject for CSR).'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600"><?= __('1.0 About the Organisation') ?></h3>
                    <div class="form-group col-lg-6">

                        <textarea aria-label='<?= htmlspecialchars(__('About Organisation'), ENT_QUOTES, 'UTF-8'); ?>' name="about_organisation" id="about_organisation" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['about_organisation']) ?? ''; ?></textarea>
                        <span class="f-12"><?= htmlspecialchars(__('(Organisational information- Historical overview/ past experience/ type of projects executed/ reach and presence within country & abroad/ staff strength/ success stories/accolades/awards received if any. Also give website link if available).'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600"><?= __('2.0 Background') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Background'), ENT_QUOTES, 'UTF-8'); ?>' name="background" id="background" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['background']) ?? ''; ?></textarea>
                        <span class="f-12"><?= __('(Give details of project background elaborating on the need of proposed project. Kindly mention details of need assessment/appraisal carried out)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600"><?= __('2.1 Thematic Area') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Thematic Area'), ENT_QUOTES, 'UTF-8'); ?>' name="thematic_area" id="thematic_area" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['thematic_area']) ?? ''; ?></textarea>
                        <span class="f-12"><?= __('(Mention the thrust area/s, Schedule VII item and Sustainable Development Goal (SDG) under which the proposed project would be categorised)'); ?></span>
                    </div>
                    <div class="col-lg-10">
                        <table class="table table-bordered">
                            <tr>
                                <th scope="col"><?= __('Thrust Area') ?></th>
                                <th scope="col"><?= __('Schedule VII item') ?></th>
                            </tr>
                            <tr>
                                <td><?= __('Education'); ?></td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                <input name="education" id="education" class="border-checkbox csr-checkbox" type="checkbox" value="0" <?php echo isset($return['education'])?'checked':''; ?> title="<?= __('Education') ?>" aria-label="<?= htmlspecialchars(__('Education'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <label class="form-label border-checkbox-label" for="education"><?= __('(ii) promotion of education including special education') ?></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('Environment Sustainability'); ?></td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="env_sustian" class="border-checkbox csr-checkbox" type="checkbox" id="env_sustian" value="0" <?php echo isset($return['env_sustian'])?'checked':''; ?> title="<?= __('Env Sustian') ?>" aria-label="<?= htmlspecialchars(__('Env Sustian'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <label class="form-label border-checkbox-label" for="env_sustian"><?= __('(iv) Ensuring environmental sustainability') ?></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('Health/Hygiene'); ?></td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="health" class="border-checkbox csr-checkbox" type="checkbox" id="health" value="0" <?php echo isset($return['health'])?'checked':''; ?> title="<?= __('Health') ?>" aria-label="<?= htmlspecialchars(__('Health'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <label class="form-label border-checkbox-label" for="health"><?= __('(i) promoting healthcare including preventive healthcare and sanitation') ?></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('Skill Development'); ?></td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="skill_development" class="border-checkbox csr-checkbox" type="checkbox" id="skill_development" value="0" <?php echo isset($return['skill_development'])?'checked':''; ?> title="<?= __('Skill Development') ?>" aria-label="<?= htmlspecialchars(__('Skill Development'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <label class="form-label border-checkbox-label" for="skill_development"><?= __('(ii) employment enhancing vocational skills and livelihood enhancement projects') ?></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('Community Development'); ?></td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="comu_development" class="border-checkbox csr-checkbox" type="checkbox" id="comu_development" value="0" <?php echo isset($return['comu_development'])?'checked':''; ?> title="<?= __('Comu Development') ?>" aria-label="<?= htmlspecialchars(__('Comu Development'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <label class="form-label border-checkbox-label" for="comu_development"><?= __('(x) Rural development projects/ Multiple categories') ?></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('Others or More than one of the above (Please Specify)'); ?></td>
                                <td>
                                    <textarea aria-label='<?= htmlspecialchars(__('Other Thrust Area'), ENT_QUOTES, 'UTF-8'); ?>' name="other_thrust_area" id="other_thrust_area" class="form-control height-80" placeholder="">
                                        <?php echo isset($return['other_thrust_area'])?$return['other_thrust_area']:''; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-22 f-w-600"><?= __('Please specify the SDG/s in addition to the above.') ?></h3>
                    <h3 class="f-18 f-w-600 mt-3"><?= __('3.0 Proposal') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('SDGproposal'), ENT_QUOTES, 'UTF-8'); ?>' name="SDGproposal" id="SDGproposal" class="form-control height-80" placeholder="">
                            <?php echo $return['SDGproposal'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Mention the proposal objectives. Explain the activities planned in order to achieve the specific objective/s along with the time frame)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('3.1 Methodology') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Methodology'), ENT_QUOTES, 'UTF-8'); ?>' name="methodology" id="methodology" class="form-control height-80" placeholder="">
                            <?php echo $return['methodology'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Define the methodology for implementation of the program such as implementation schedule, execution plan, selection criteria for specific beneficiaries etc.)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('3.2 Target Group') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Target Group'), ENT_QUOTES, 'UTF-8'); ?>' name="target_group" id="target_group" class="form-control height-80" placeholder="">
                            <?php echo $return['target_group'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Mention Geographical area/location of the project and the profile of the beneficiaries - details of Male/Female, minority or vulnerable group/others etc)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('3.3 Team for the project') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Team Project'), ENT_QUOTES, 'UTF-8'); ?>' name="team_project" id="team_project" class="form-control height-80" placeholder="">
                            <?php echo $return['team_project'] ??''; ?></textarea>
                        <span class="f-12"><?= htmlspecialchars(__('(Mention job profile & eligibility criteria for selection of staff/team members for the project i.e. qualifications & experience, Roles & responsibilities, Organization structure etc.)'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('3.4 Monitoring Mechanism') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Monitoring Mechanism'), ENT_QUOTES, 'UTF-8'); ?>' name="monitoring_mechanism" id="monitoring_mechanism" class="form-control height-80" placeholder="">
                            <?php echo $return['monitoring_mechanism'] ??''; ?></textarea>
                        <span class="f-12"><?= htmlspecialchars(__('(Mention the monitoring mechanism such as frequency of visits, plan for collection of data, monitoring of activity timeline & frequency of submission of reports)'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('4.1 Outcomes/Result expected') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Outcomes Expected'), ENT_QUOTES, 'UTF-8'); ?>' name="outcomes_expected" id="outcomes_expected" class="form-control height-80" placeholder="">
                            <?php echo $return['outcomes_expected'] ??''; ?></textarea>
                        <span class="f-12"><?= htmlspecialchars(__('(Mention the Quantitative & Qualitative outcomes expected from the project, outcomes should be directly linked to proposed objectives; specific targets should be benchmarked. It is recommended to use frameworks such as project matrix, verifiable indicators, data analysis.)'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('4.2 Execution Plan') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Execution Plan'), ENT_QUOTES, 'UTF-8'); ?>' name="execution_plan" id="execution_plan" class="form-control height-80" placeholder="">
                            <?php echo $return['execution_plan'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Gantt chart with time line against activities may be given in tabular/other form)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('4.3 Impact Assessment') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Impact Assessment'), ENT_QUOTES, 'UTF-8'); ?>' name="impact_assessment" id="impact_assessment" class="form-control height-80" placeholder="">
                            <?php echo $return['impact_assessment'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Mention the plan to measure the impact of planned interventions of this project as defined in the scope of work)'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('5.1 Financial Implications') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Financial Implications'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-80" id="financial_implications" name="financial_implications" placeholder="">
                            <?php echo $return['financial_implications'] ??''; ?></textarea>
                        <span class="f-12"><?= __('(Format given below is indicative. Based on the nature of the project, kindly use appropriate format)'); ?></span>
                    </div>
                    <div class="general_instructions">
                        <ul>
                            <li><?= __('Breakup for each line item should be given. Separate annexure to be provided if needed'); ?></li>
                            <li><?= __('Any procurement items has to be supported with documents proving competitive rate in the market'); ?></li>
                            <li><?= __('List down items covered under Administrative expenses.'); ?></li>
                        </ul>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="financial_table">
                            <thead>
                                <tr>
                                    <th scope="col"><?= __('Sr. No') ?></th>
                                    <th scope="col" class="text-center"><?= __('Details') ?></th>
                                    <th scope="col" class="text-center"><?= __('Per unit Cost') ?><br />(A)</th>
                                    <th scope="col" class="text-center"><?= __('Total Number of Units') ?><br />(B)</th>
                                    <th scope="col" class="text-center"><?= __('Total Budget for 1 month/year') ?><br /><?= __('(C=A*B)'); ?></th>
                                    <th scope="col" class="text-center"><?= __('No. of months/years') ?></th>
                                    <th scope="col" class="text-center"><?= __('Budget For Months/Years') ?><br /><?= __('(D=C*No. of months/years)'); ?></th>
                                    <th scope="col" class="text-center"><?= __('Remarks') ?><br />(<?= __('Justification of cost element') ?>)</th>
                                    <th scope="col" class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
<?php
if (!empty($return['groups_data'])) {
    $groupsData = json_decode($return['groups_data'], true);
} else {
    $groupsData = [];
}

if (!empty($groupsData)) {
    foreach ($groupsData as $groupId => $groupDetails) {
        $details = $groupDetails['details'] ?? [];
        $unitCost = $groupDetails['unit_cost'] ?? [];
        $numUnits = $groupDetails['num_units'] ?? [];
        $budget = $groupDetails['budget'] ?? [];
        $numMonths = $groupDetails['num_months'] ?? [];
        $totalBudget = $groupDetails['total_budget'] ?? [];
        $remarks = $groupDetails['remarks'] ?? [];
        $subtotal = $groupDetails['subtotal'] ?? 0;
        $totalSubtotal = $groupDetails['total_subtotal'] ?? 0;

        foreach ($details as $index => $detail) {
            echo '<tr data-group="group_' . htmlspecialchars($groupId) . '">';
            echo '<td>' . htmlspecialchars($index) . '</td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control" id="details_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][details][' . ($index ) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($detail) . '"></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control unit-cost text_align_right" id="unit_cost_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][unit_cost][' . ($index ) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($unitCost[$index] ?? '') . ATTR_DATA_GROUP . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control num-units text_align_right" id="num_units_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][num_units][' . ($index) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($numUnits[$index] ?? '') . ATTR_DATA_GROUP . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control budget text_align_right" id="budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][budget][' . ($index) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($budget[$index] ?? '') . '" readonly></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control num-months text_align_right" id="num_months_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][num_months][' . ($index) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($numMonths[$index] ?? '') . ATTR_DATA_GROUP . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control total-budget text_align_right" id="total_budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][total_budget][' . ($index) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($totalBudget[$index] ?? '') . '" readonly></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control" id="remarks_group_' . htmlspecialchars($groupId) . '_' . ($index) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][remarks][' . ($index) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($remarks[$index] ?? '') . '"></td>';
            echo '<td ></td>';
            echo '</tr>';
        }

            // Subtotal Row
            echo '<tr data-group="group_' . htmlspecialchars($groupId) . '" class="subtotal-row bg-light-row" >';
            echo '<td></td>';
            echo '<th scope="col" class="text-center">' . __('Sub Total') . ' (' . htmlspecialchars($groupId) . ')</th>';
            echo '<td></td><td></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control budget-subtotal text_align_right" id="budget_sub_total_' . htmlspecialchars($groupId) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][subtotal][' . htmlspecialchars($groupId) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($subtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control total-budget-subtotal text_align_right" id="total_budget_sub_total_' . htmlspecialchars($groupId) . ATTR_NAME_GROUPS . htmlspecialchars($groupId) . '][total_subtotal][' . htmlspecialchars($groupId) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($totalSubtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td class="text-center"> <i class="fas fa-trash-alt text-danger cursor-pointer remove-row data-delete-group" data-delete-group="group_' . htmlspecialchars($groupId) . '"></i></td>';
            echo '</tr>';
    }
}

?>

                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-primary waves-effect waves-light" type="button" id="add_row" ><?= __('Add more') ?></button>
                                    </td>
                                </tr>
                                <tr class="bg-light-row" >
                                    <td></td>
                                    <th scope="col" class="text-center"><strong><?= __('Grand Total') ?></strong></th>
                                    <td></td>
                                    <td></td>
                                    <td><input aria-label='<?= htmlspecialchars(__('Budget Grand Total'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="budget_grand_total" name="budget_grand_total" value="<?php echo $return['budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td><input aria-label='<?= htmlspecialchars(__('Total Budget Grand Total'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="total_budget_grand_total" name="total_budget_grand_total" value="<?php echo $return['total_budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>

                        <div class="mt-3">
                            <h3 class="f-18 f-w-600 mt-3"><?= __('5.2 Tentative payment schedule') ?></h3>
                            <div class="form-group col-lg-6">
                                <textarea aria-label='<?= htmlspecialchars(__('Payment Schedule'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-80" id="payment_schedule" name="payment_schedule" placeholder="">
                                    <?php echo $return['payment_schedule'] ??''; ?></textarea>
                                <span class="f-12"><?= htmlspecialchars(__('(Link payment schedule with project deliverables and timeline. It will be reviewed by Infisuite and the final payment schedule might differ significantly as per nature of the project & Infisuite guidelines. For final payment, 10% of the total budget will be kept on hold until the submission of the project completion report and audited fund utilisation certificate)'), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentScheduleTable">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('Sr.No') ?></th>
                                            <th scope="col"><?= __('Description') ?></th>
                                            <th scope="col"><?= __('% of Total Project Budget') ?></th>
                                            <th scope="col"><?= __('Amount in Rs') ?></th>
                                            <th scope="col"><?= __('Deliverables to be achieved') ?></th>
                                            <th scope="col"><?= __('Actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

    $paymentScheduleData = json_decode($return['tentative_payment_schedule'], true);

    $description = $paymentScheduleData['payment'] ?? $paymentScheduleData['description'] ?? [];

    $percent = $paymentScheduleData['percent'] ?? [];
    $amount = $paymentScheduleData['amount'] ?? [];
    $deliverables = $paymentScheduleData['deliverables'] ?? [];

if (!empty($percent)) {
    foreach ($percent as $payId => $percentValue) {
        echo '<tr>';
        echo '<td>' . ($payId) . '</td>';
        echo '<td><input aria-label="Payment Schedule Description" type="text" class="form-control" id="payment_schedule_description_' . ($payId) . '" name="payment_schedule_description[' . ($payId ) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($description[$payId] ?? '') . '"></td>';
        echo '<td><input aria-label="Payment Schedule Percent" type="number" class="form-control percent-input text_align_right" id="percent_' . ($payId) . '" name="payment_schedule_percent[' . ($payId) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($percentValue) . '" data-row="' . $payId . '"></td>';
        echo '<td><input aria-label="Payment Schedule Amount" type="text" class="form-control amount-input text_align_right" id="amount_' . ($payId + 1) . '" name="payment_schedule_amount[' . ($payId) . ATTR_VAL_EMPTY_GROUP . htmlspecialchars($amount[$payId] ?? '') . '" readonly></td>';
        echo '<td><textarea aria-label="Payment Schedule Deliverables" class="form-control" id="deliverables_' . ($payId) . '" name="payment_schedule_deliverables[' . ($payId) . ']">' . htmlspecialchars($deliverables[$payId] ?? '') . '</textarea></td>';
        echo '<td class="text-center" ><i class="fas fa-trash-alt text-danger cursor-pointer remove-row delete-payment-row" data-delete-payment-row="1" ></i></td>';
        echo '</tr>';
    }
}
?>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button class="btn btn-primary waves-effect waves-light" type="button" id="add_payment_schedule_row"><?= __('Add more') ?></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col"><?= __('Grand Total') ?></th>
                                            <th scope="col"></th>
                                            <th scope="col"><input aria-label='<?= htmlspecialchars(__('Payment Grand Total'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="payment_grand_total" name="payment_grand_total" value="<?php echo $return['payment_grand_total'] ??''; ?>" readonly></th>
                                            <th scope="col" colspan="2"><input aria-label='<?= htmlspecialchars(__('In Words Only...'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="payment_grand_total_in_words" name="payment_grand_total_in_words" placeholder="<?= __('In Words Only...') ?>" value="<?php echo $return['payment_grand_total_in_words'] ??''; ?>"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('6.0 Sustainability Plan') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Sustainability Plan'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-80" id="sustainability_plan" name="sustainability_plan" placeholder="">
                            <?php echo $return['sustainability_plan'] ??''; ?></textarea>
                        <span class="f-12"><?= htmlspecialchars(__('(Provide an action plan giving details of how the project will be sustainable after exit of Infisuite & the implementing partner. This must include maintenance and ownership of assets where applicable)'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('7.0 Conclusion') ?></h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='<?= htmlspecialchars(__('Conclusion'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-80" id="conclusion" name="conclusion" placeholder=""><?php echo $return['conclusion'] ??''; ?></textarea>
                    </div>
                </div>
                <div class="mt-3 enclosures">
                    <h3 class="f-18 f-w-600 mt-3"><?= __('8.0 Enclosures') ?></h3>
                    <div class="general_instructions">
                        <ul>
                            <li>
                                <span class="f-w-600"><?= __('Form A :- CHECKLIST FOR SUBMISSION OF DOCUMENTS'); ?></span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox csr-form-radio" type="radio" id="form_a_yes" name="form_a" value="1"  <?php echo $return['form_a']=='1'?'checked':''  ?> / title="<?= __('Form A') ?>" aria-label="<?= htmlspecialchars(__('Form A'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_a_yes"><?= __('Yes') ?></label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox  csr-form-radio" type="radio" id="form_a_no" name="form_a" value="0"  <?php echo isset($return['form_a'])&&($return['form_a']=='0' || $return['form_a'] == '') ?'checked':''  ?> / title="<?= __('Form A') ?>" aria-label="<?= htmlspecialchars(__('Form A'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_a_no"><?= __('No') ?></label>
                                    </div>

                                    <?php if (!empty($return['form_a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_a_file']); ?>"  class="mb-2 " download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>

                                </div>

                                <div class="col-lg-3 mt-2">
                                    <input aria-label='<?= htmlspecialchars(__('Form A File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form_a_file" name="form_a_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_a_file_old"  value="<?php echo $return['form_a_file']; ?>" />

                                </div>
                            </li>
                            <li>
                                <span class="f-w-600"><?= __('Form B :- CHECKLIST FOR INFRASTRUCTURE PROJECTS'); ?></span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_b_yes" name="form_b" value="1"  <?php echo $return['form_b']?'checked':''  ?> / title="<?= __('Form B') ?>" aria-label="<?= htmlspecialchars(__('Form B'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_b_yes"><?= __('Yes') ?></label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_b_no" name="form_b" value="0"  <?php echo isset($return['form_b'])&&($return['form_b']=='0' || $return['form_b'] == '') ?'checked':''  ?>  / title="<?= __('Form B') ?>" aria-label="<?= htmlspecialchars(__('Form B'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_b_no"><?= __('No') ?></label>
                                    </div>
                                    <?php if (!empty($return['form_b_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_b_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>

                                <div class="col-lg-3 mt-2">
                                    <input aria-label='<?= htmlspecialchars(__('Form B File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form_b_file" name="form_b_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_b_file_old"  value="<?php echo $return['form_b_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600"><?= __('Form C :- CHECKLIST FOR SUBMISSION OF PROPOSAL'); ?></span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_c_yes" name="form_c" value="1"  <?php echo $return['form_c']?'checked':''  ?> / title="<?= __('Form C') ?>" aria-label="<?= htmlspecialchars(__('Form C'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_c_yes"><?= __('Yes') ?></label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_c_no" name="form_c" value="0"  <?php echo isset($return['form_c'])&&($return['form_c']=='0' || $return['form_c'] == '') ?'checked':''  ?> / title="<?= __('Form C') ?>" aria-label="<?= htmlspecialchars(__('Form C'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_c_no"><?= __('No') ?></label>
                                    </div>
                                <?php if (!empty($return['form_c_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_c_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='<?= htmlspecialchars(__('Form C File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form_c_file" name="form_c_file" class="form-control d-none csr-form-file" />
                                    <input type="hidden" name="form_c_file_old"  value="<?php echo $return['form_c_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600"><?= __('Form D :- SUMMARY SHEET FOR THE PROPOSAL'); ?></span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_d_yes" name="form_d" value="1" <?php echo $return['form_d']?'checked':''  ?> / title="<?= __('Form D') ?>" aria-label="<?= htmlspecialchars(__('Form D'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_d_yes"><?= __('Yes') ?></label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_d_no" name="form_d" value="0"  <?php echo isset($return['form_d'])&&($return['form_d']=='0' || $return['form_d'] == '') ?'checked':''  ?> / title="<?= __('Form D') ?>" aria-label="<?= htmlspecialchars(__('Form D'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_d_no"><?= __('No') ?></label>
                                    </div>
                                <?php if (!empty($return['form_d_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_d_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='<?= htmlspecialchars(__('Form D File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form_d_file" name="form_d_file" class="form-control d-none csr-form-file" />
                                    <input type="hidden" name="form_d_file_old"  value="<?php echo $return['form_d_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600"><?= __('Form E :- TRACK RECORD OF PROJECTS'); ?></span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_e_yes" name="form_e" value="1"  <?php echo $return['form_e']?'checked':''  ?> / title="<?= __('Form E') ?>" aria-label="<?= htmlspecialchars(__('Form E'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_e_yes"><?= __('Yes') ?></label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_e_no" name="form_e" value="0"   <?php echo isset($return['form_e'])&&($return['form_e']=='0' || $return['form_e'] == '') ?'checked':''  ?>/ title="<?= __('Form E') ?>" aria-label="<?= htmlspecialchars(__('Form E'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <label class="form-label border-checkbox-label" for="form_e_no"><?= __('No') ?></label>
                                    </div>
                                <?php if (!empty($return['form_e_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_e_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='<?= htmlspecialchars(__('Form E File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form_e_file" name="form_e_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_e_file_old"  value="<?php echo $return['form_e_file']; ?>" />
                                </div>
                            </li>

                        </ul>
                        <span class="f-16 f-w-600 d-block mt-4"><?= htmlspecialchars(__('Kindly ensure the Organisation Seal/Stamp & Sign on each page.'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <div id="form_a" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="4"><?= __('Form A') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="4"><?= __('CHECKLIST FOR SUBMISSION OF DOCUMENTS') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 5%;"><?= __('Sr. No') ?></th>
                                    <th scope="col" style="width: 50%;"><?= __('Description of documents') ?></th>
                                    <th scope="col" class="text-center" style="width: 25%;"><?= __('Attached (Yes / No)') ?></th>
                                    <th scope="col" class="text-center" style="width: 20%;"><?= __('Remark') ?></th>
                                </tr>
                                <tr>
                                    <td>1*</td>
                                    <td><?= __('Trust deed'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Trust Deed'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="trust_deed_yes" data-file-id="trust_deed_file" name="trust_deed" value="1"  <?php echo $return['trust_deed']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Trust Deed'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="trust_deed_no" data-file-id="trust_deed_file_2" name="trust_deed" value="0"  <?php echo isset($return['trust_deed'])&&($return['trust_deed']=='0' || $return['trust_deed'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                            <?php if (!empty($return['trust_deed_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['trust_deed_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Trust Deed File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="trust_deed_file_3" name="trust_deed_file" class="form-control d-none"  />
                                            <input type="hidden" name="trust_deed_file_old"  value="<?php echo $return['trust_deed_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Trust Deed Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="trust_deed_remark" name="trust_deed_remark"><?php echo $return['trust_deed_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2*</td>
                                    <td><?= __('Certificate of incorporation/Registration certificate'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Incorporation'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="incorporation_yes" name="incorporation" value="1" data-file-id="incorporation_file"  <?php echo $return['incorporation']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Incorporation'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="incorporation_no" name="incorporation" value="0" data-file-id="incorporation_file_2"  <?php echo isset($return['incorporation'])&&($return['incorporation']=='0' || $return['incorporation'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['incorporation_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['incorporation_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Incorporation File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="incorporation_file_3" name="incorporation_file" class="form-control d-none"  />
                                            <input type="hidden" name="incorporation_file_old"  value="<?php echo $return['incorporation_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Incorporation Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="incorporation_remark" name="incorporation_remark">
                                            <?php echo $return['incorporation_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3*</td>
                                    <td><?= __('CSR 1 Registration certificate'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Csr1' type="radio" id="csr1_yes" name="csr1" value="1" data-file-id="csr1_file"  <?php echo $return['csr1']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Csr1' type="radio" id="csr1_no" name="csr1" value="0" data-file-id="csr1_file_2"  <?php echo isset($return['csr1'])&&($return['csr1']=='0' || $return['csr1'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['csr1_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['csr1_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Csr1 File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="csr1_file_3" name="csr1_file" class="form-control d-none"  />
                                            <input type="hidden" name="csr1_file_old"  value="<?php echo $return['csr1_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Csr1 Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="csr1_remark" name="csr1_remark"><?php echo $return['csr1_remark']??'';  ?> </textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4*</td>
                                    <td><?= __('Form 12 A'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form12a'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form12a_yes" name="form12a" value="1" data-file-id="form12a_file"  <?php echo $return['form12a']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form12a'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form12a_no" name="form12a" value="0" data-file-id="form12a_file_2"  <?php echo isset($return['form12a'])&&($return['form12a']=='0' || $return['form12a'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form12a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form12a_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Form12a File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form12a_file_3" name="form12a_file" class="form-control d-none"  />
                                        <input type="hidden" name="form12a_file_old"  value="<?php echo $return['form12a_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Form12a Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="form12a_remark" name="form12a_remark"><?php echo $return['form12a_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5*</td>
                                    <td><?= __('Form 80 G'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form80g'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form80g_yes" name="form80g" value="1" data-file-id="form80g_file"  <?php echo $return['form80g']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form80g'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form80g_no" name="form80g" value="0" data-file-id="form80g_file_2"  <?php echo isset($return['form80g'])&&($return['form80g']=='0' || $return['form80g'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form80g_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form80g_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Form80g File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form80g_file_3" name="form80g_file" class="form-control d-none"  />
                                        <input type="hidden" name="form80g_file_old"  value="<?php echo $return['form80g_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Form80g Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="form80g_remark" name="form80g_remark"><?php echo $return['form80g_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6*</td>
                                    <td><?= __('35 AC registration if any'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form35ac'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form35ac_yes" name="form35ac" value="1" data-file-id="form35ac_file"  <?php echo $return['form35ac']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Form35ac'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="form35ac_no" name="form35ac" value="0" data-file-id="form35ac_file_2"  <?php echo isset($return['form35ac'])&&($return['form35ac']=='0' || $return['form35ac'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form35ac_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form35ac_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Form35ac File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="form35ac_file_3" name="form35ac_file" class="form-control d-none"  />
                                        <input type="hidden" name="form35ac_file_old"  value="<?php echo $return['form35ac_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Form35ac Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="form35ac_remark" name="form35ac_remark"><?php echo $return['form35ac_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>7*</td>
                                    <td><?= __('PAN card'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Pan'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="pan_yes" name="pan" value="1" data-file-id="pan_file"  <?php echo $return['pan']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Pan'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="pan_no" name="pan" value="0" data-file-id="pan_file_2"  <?php echo isset($return['pan'])&&($return['pan']=='0' || $return['pan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['pan_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['pan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Pan File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="pan_file_3" name="pan_file" class="form-control d-none"  />
                                        <input type="hidden" name="pan_file_old"  value="<?php echo $return['pan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Pan Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="pan_remark" name="pan_remark"><?php echo $return['pan_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>8*</td>
                                    <td><?= htmlspecialchars(__('Last 3 financial years audited statements & ITR proof'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Financial Statements'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="financial_statements_yes" name="financial_statements" value="1" data-file-id="financial_statements_file"  <?php echo $return['financial_statements']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Financial Statements'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="financial_statements_no" name="financial_statements" value="0" data-file-id="financial_statements_file_2"  <?php echo isset($return['financial_statements'])&&($return['financial_statements']=='0' || $return['financial_statements'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['financial_statements_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['financial_statements_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Financial Statements File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="financial_statements_file_3" name="financial_statements_file" class="form-control d-none"  />
                                        <input type="hidden" name="financial_statements_file_old"  value="<?php echo $return['financial_statements_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Financial Statements Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="financial_statements_remark" name="financial_statements_remark"><?php echo $return['financial_statements_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>9*</td>
                                    <td><?= __('Detailed proposal as per format given'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Proposal'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="proposal_yes" name="proposal" value="1" data-file-id="proposal_file"  <?php echo $return['proposal']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Proposal'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="proposal_no" name="proposal" value="0" data-file-id="proposal_file_2"  <?php echo isset($return['proposal'])&&($return['proposal']=='0' || $return['proposal'] == '' ?'checked':'')  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['proposal_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['proposal_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Proposal File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="proposal_file_3" name="proposal_file" class="form-control d-none"  />
                                        <input type="hidden" name="proposal_file_old"  value="<?php echo $return['proposal_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Proposal Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="proposal_remark" name="proposal_remark"><?php echo $return['proposal_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td><?= __('FCRA approval'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Fcra'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="fcra_yes" name="fcra" value="1" data-file-id="fcra_file"  <?php echo $return['fcra']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Fcra'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="fcra_no" name="fcra" value="0" data-file-id="fcra_file_2"  <?php echo isset($return['fcra'])&&($return['fcra']=='0' || $return['fcra'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['fcra_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['fcra_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Fcra File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="fcra_file_3" name="fcra_file" class="form-control d-none"  />
                                        <input type="hidden" name="fcra_file_old"  value="<?php echo $return['fcra_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Fcra Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="fcra_remark" name="fcra_remark"><?php echo $return['fcra_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td><?= __('TDS exemption certificate if applicable'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Tds Exemption'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="tds_exemption_yes" name="tds_exemption" value="1" data-file-id="tds_exemption_file"  <?php echo $return['tds_exemption']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Tds Exemption'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="tds_exemption_no" name="tds_exemption" value="0" data-file-id="tds_exemption_file_2"  <?php echo isset($return['tds_exemption'])&&($return['tds_exemption']=='0' || $return['tds_exemption'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['tds_exemption_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['tds_exemption_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Tds Exemption File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="tds_exemption_file_3" name="tds_exemption_file" class="form-control d-none"  />
                                        <input type="hidden" name="tds_exemption_file_old"  value="<?php echo $return['tds_exemption_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Tds Exemption Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="tds_exemption_remark" name="tds_exemption_remark"><?php echo $return['tds_exemption_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td><?= __('GST Registration certificate if applicable'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Gst'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="gst_yes" name="gst" value="1" data-file-id="gst_file"  <?php echo $return['gst']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Gst'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="gst_no" name="gst" value="0" data-file-id="gst_file_2"  <?php echo isset($return['gst'])&&($return['gst']=='0' || $return['gst'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['gst_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['gst_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Gst File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="gst_file_3" name="gst_file" class="form-control d-none"  />
                                        <input type="hidden" name="gst_file_old"  value="<?php echo $return['gst_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Gst Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="gst_remark" name="gst_remark"><?php echo $return['gst_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>13*</td>
                                    <td><?= __('2 Reference letters of past or present supporters for CSR project'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Reference'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="reference_yes" name="reference" value="1" data-file-id="reference_file"  <?php echo $return['reference']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Reference'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="reference_no" name="reference" value="0" data-file-id="reference_file_2"  <?php echo isset($return['reference'])&&($return['reference']=='0' || $return['reference'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['reference_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['reference_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Reference File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="reference_file_3" name="reference_file" class="form-control d-none"  />
                                        <input type="hidden" name="reference_file_old"  value="<?php echo $return['reference_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Reference Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="reference_remark" name="reference_remark"><?php echo $return['reference_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                            </table>
                            <span class="f-w-600 f-14">*<?= __('Sr. Nos. are mandatory documents'); ?></span>
                        </div>
                    </div>

                    <div class="mt-3 enclosures">
                        <h3 class="f-18 f-w-600 mt-4 text-center">
                            <?= __('Additional Information in respect of Audited Accounts as mentioned in Serial No. 8 of'); ?><br /><?= __('Form-A of the Proposal Format for submission of CSR Proposals.'); ?>
                        </h3>
                        <h3 class="f-16 f-w-600 mt-3"><?= __('1. Please give the sources of income for each of the last 3 Financial Years for which audited accounts have been submitted with the application'); ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="income_table">
                                <?php
                                $currentYear = date("Y"); // Get the current year
                                $years = [$currentYear - 1, $currentYear - 2, $currentYear - 3]; // Calculate past 3 years
                                ?>

                                <thead>
                                    <tr>
                                        <th scope="col"><?= __('Sr. No') ?></th>
                                        <th scope="col" class="text-center"><?= __('Sources of Funds') ?></th>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center"><?= __('Action') ?></th>
                                    </tr>
                                </thead>

                                <tbody id="income_table_body">
    <?php

    $source_income_data = json_decode($return['source_income_data'], true);
     if (!empty($source_income_data)) : ?>
        <?php foreach ($source_income_data as $index => $data) :
            foreach ($data['source'] as $index => $source) :

         ?>
            <tr>
                <td><?= $index ?></td>
                <td>
                    <input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="source_<?= $index ?>"
                           name="source_income[<?= $index  ?>]source[<?= $index ?>]"
                           value="<?= $source ?>">
                </td>
                <td>
                    <input aria-label='<?= htmlspecialchars(__('Enter number'), ENT_QUOTES, 'UTF-8'); ?>' type="number" min="0" class="form-control text_align_right income-input" id="fy1_<?= $index  ?>"
                           name="source_income[<?= $index  ?>]fy1[<?= $index  ?>]"
                           value="<?= $data['fy1'][$index] ?>" >
                </td>
                <td>
                    <input aria-label='<?= htmlspecialchars(__('Enter number'), ENT_QUOTES, 'UTF-8'); ?>' type="number" min="0" class="form-control text_align_right income-input" id="fy2_<?= $index ?>"
                           name="source_income[<?= $index  ?>]fy2[<?= $index  ?>]"
                           value="<?= $data['fy2'][$index] ?>" >
                </td>
                <td>
                    <input aria-label='<?= htmlspecialchars(__('Enter number'), ENT_QUOTES, 'UTF-8'); ?>' type="number" min="0" class="form-control text_align_right income-input" id="fy3_<?= $index  ?>"
                           name="source_income[<?= $index  ?>]fy3[<?= $index  ?>]"
                           value="<?= $data['fy3'][$index] ?>" >
                </td>
                <td class="text-center">
                    <i class="fas fa-trash-alt text-danger cursor-pointer delete-income-row" ></i>
                </td>
            </tr>
        <?php
        endforeach;
    endforeach; ?>
    <?php endif; ?>
</tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <th scope="col" class="text-end"><?= __('Total') ?></th>
                                        <td><input aria-label='<?= htmlspecialchars(__('Total Fy1'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="total_fy1" name="total_fy1" value="<?php echo $return['total_fy1'] ?? '' ; ?>" readonly></td>
                                        <td><input aria-label='<?= htmlspecialchars(__('Total Fy2'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="total_fy2" name="total_fy2" value="<?php echo $return['total_fy2'] ?? '' ; ?>" readonly></td>
                                        <td><input aria-label='<?= htmlspecialchars(__('Total Fy3'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control text_align_right" id="total_fy3" name="total_fy3" value="<?php echo $return['total_fy3'] ?? '' ; ?>" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-start">
                                            <button type="button" class="btn btn-primary" id="add_row_income" ><?= __('Add More') ?></button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 enclosures">
                        <h3 class="f-16 f-w-600 mt-3"><?= __('2. Provide details of income, expenditure, assets and liabilities during the last 3 Financial Years for which audited accounts are being submitted along with the application.'); ?> </h3>

                        <h3 class="f-16 f-w-600 mt-4"><?= __('Total Income:') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="total_income_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center"><?= __('FY ('); ?><?= $year; ?> <?= __('in Rs.)'); ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col"><?= __('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                if ($return['income_data']) {
                    $income_data = json_decode($return['income_data'], true);
                    foreach ($income_data as $id => $income) {
                        echo "<tr>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Income'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='income_fy1_{$id
}' name='income[{$id
}]fy1' value='{$income['fy1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Income'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='income_fy2_{$id
}' name='income[{$id
}]fy2' value='{$income['fy2']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Income'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='income_fy3_{$id
}' name='income[{$id
}]fy3' value='{$income['fy3']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Income'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='income_var1_{$id
}' name='income[{$id
}]var1' value='{$income['var1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Income'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='income_var2_{$id
}' name='income[{$id
}]var2' value='{$income['var2']
}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-table-row' data-table-id='total_income_table_2'   data-section='income' ></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light add-row-btn"
        type="button"
        data-table-id="total_income_table_3"
        data-section="income"><?= __('Add more') ?></button>

                        <h3 class="f-16 f-w-600 mt-4"><?= __('Total Expenditure:') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="expenditure_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col"><?= __('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                <?php
                if ($return['expenditure_data']) {
                    $expenditure_data = json_decode($return['expenditure_data'], true);
                    foreach ($expenditure_data as $id => $expenditure) {
                        echo "<tr>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Expenditure'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='expenditure_fy1_{$id
}' name='expenditure[{$id
}]fy1' value='{$expenditure['fy1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Expenditure'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='expenditure_fy2_{$id
}' name='expenditure[{$id
}]fy2' value='{$expenditure['fy2']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Expenditure'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='expenditure_fy3_{$id
}' name='expenditure[{$id
}]fy3' value='{$expenditure['fy3']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Expenditure'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='expenditure_var1_{$id
}' name='expenditure[{$id
}]var1' value='{$expenditure['var1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Expenditure'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='expenditure_var2_{$id
}' name='expenditure[{$id
}]var2' value='{$expenditure['var2']
}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-table-row' data-table-id='expenditure_table_2'   data-section='expenditure'></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light add-row-btn"
        type="button"
        data-table-id="expenditure_table_3"
        data-section="expenditure"><?= __('Add more') ?></button>

                        <h3 class="f-16 f-w-600 mt-4"><?= __('Total Assets:') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="assets_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col"><?= __('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                            <?php
                if ($return['assets_data']) {
                    $assets_data = json_decode($return['assets_data'], true);
                    foreach ($assets_data as $id => $assets) {
                        echo "<tr>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Assetsfy1'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='assets_fy1_{$id
}' name='assets[{$id
}]fy1' value='{$assets['fy1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Assetsfy2'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='assets_fy2_{$id
}' name='assets[{$id
}]fy2' value='{$assets['fy2']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Assetsfy3'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='assets_fy3_{$id
}' name='assets[{$id
}]fy3' value='{$assets['fy3']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Assetsvar1'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='assets_var1_{$id
}' name='assets[{$id
}]var1' value='{$assets['var1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Assetsvar2'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='assets_var2_{$id
}' name='assets[{$id
}]var2' value='{$assets['var2']
}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-table-row' data-table-id='assets_table_2'
       data-section='assets' ></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light add-row-btn"
        type="button"
        data-table-id="assets_table_3"
        data-section="assets"><?= __('Add more') ?></button>

                        <h3 class="f-16 f-w-600 mt-4"><?= __('Total Liabilities:') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="liabilities_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center"><?= __('Major Reasons for variation'); ?> <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col"><?= __('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php
                if ($return['liabilities_data']) {
                    $liabilities_data = json_decode($return['liabilities_data'], true);
                    foreach ($liabilities_data as $id => $liabilities) {
                        echo "<tr>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Liabilitiesfy1'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='liabilities_fy1_{$id
}' name='liabilities[{$id
}]fy1' value='{$liabilities['fy1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Liabilitiesfy2'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='liabilities_fy2_{$id
}' name='liabilities[{$id
}]fy2' value='{$liabilities['fy2']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Liabilitiesfy3'), ENT_QUOTES, 'UTF-8')."' type='number' class='form-control text_align_right' id='liabilities_fy3_{$id
}' name='liabilities[{$id
}]fy3' value='{$liabilities['fy3']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Liabilitiesvar1'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='liabilities_var1_{$id
}' name='liabilities[{$id
}]var1' value='{$liabilities['var1']
}'></td>";
                        echo "<td><input aria-label='".htmlspecialchars(__('Liabilitiesvar2'), ENT_QUOTES, 'UTF-8')."' type='text' class='form-control' id='liabilities_var2_{$id
}' name='liabilities[{$id
}]var2' value='{$liabilities['var2']
}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-table-row'  data-table-id='liabilities_table_2'
       data-section='liabilities' ></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light add-row-btn"
        type="button"
        data-table-id="liabilities_table_3"
        data-section="liabilities"><?= __('Add more') ?></button>

                    </div>
                </div>
                <div id="form_b" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive form_B">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="4"><?= __('Form B') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="4"><?= __('CHECKLIST FOR INFRASTRUCTURE PROJECTS') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="w-5"><?= __('Sr. No') ?></th>
                                    <th scope="col" class="w-50"><?= __('Documents') ?></th>
                                    <th scope="col" class="w-25 "><?= __('Attached (Yes / No)') ?></th>
                                    <th scope="col" class="w-25 "><?= __('Remark') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col">A</th>
                                    <th scope="col"><?= __('LAND RELATED') ?></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td><?= __('Land Ownership document'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Land Ownership'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="land_ownership_yes" name="land_ownership" value="1" data-file-id="land_ownership_file"  <?php echo $return['land_ownership']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Land Ownership'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="land_ownership_no" name="land_ownership" value="0" data-file-id="land_ownership_file_2"  <?php echo isset($return['land_ownership'])&&($return['land_ownership']=='0' || $return['land_ownership'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['land_ownership_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['land_ownership_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Land Ownership File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="land_ownership_file_3" name="land_ownership_file" class="form-control d-none"  />
                                        <input type="hidden" name="land_ownership_file_old"  value="<?php echo $return['land_ownership_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Land Ownership Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="land_ownership_remark" name="land_ownership_remark"><?php echo $return['land_ownership_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><?= __('Property Card (Commercial/ Vastu/Wetland)'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Property Card'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="property_card_yes" name="property_card" value="1" data-file-id="property_card_file"  <?php echo $return['property_card']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Property Card'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="property_card_no" name="property_card" value="0" data-file-id="property_card_file_2"  <?php echo isset($return['property_card'])&&($return['property_card']=='0' || $return['property_card'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['property_card_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['property_card_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Property Card File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="property_card_file_3" name="property_card_file" class="form-control d-none"  />
                                        <input type="hidden" name="property_card_file_old"  value="<?php echo $return['property_card_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Property Card Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="property_card_remark" name="property_card_remark"><?php echo $return['property_card_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">B</th>
                                    <th scope="col"><?= __('ESTIMATES & APPROVALS') ?></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td class="text-wrap">
                                        <p class="mb-1"><?= htmlspecialchars(__('Drawings- Site Plan, Building Plan, Elevation, foundation / structural details. All drawings needs to be signed & stamped by Licensed Engineer/ Architect & Client.'), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="mb-0"><?= __('License no. / Registration no. of Architect/Engineer needs to be mentioned.'); ?></p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Drawings'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="drawings_yes" name="drawings" value="1" data-file-id="drawings_file"  <?php echo $return['drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Drawings'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="drawings_no" name="drawings" value="0" data-file-id="drawings_file_2"   <?php echo isset($return['drawings'])&&($return['drawings']=='0' || $return['drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Drawings File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="drawings_file_3" name="drawings_file" class="form-control d-none"  />
                                        <input type="hidden" name="drawings_file_old"  value="<?php echo $return['drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Drawings Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="drawings_remark" name="drawings_remark"><?php echo $return['drawings_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><?= __('Drawings sanctioned by Govt. Authorities'); ?></td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Govt Drawings'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="govt_drawings_yes" name="govt_drawings" value="1" data-file-id="govt_drawings_file"  <?php echo $return['govt_drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Govt Drawings'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="govt_drawings_no" name="govt_drawings" value="0" data-file-id="govt_drawings_file_2"   <?php echo isset($return['govt_drawings'])&&($return['govt_drawings']=='0' || $return['govt_drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['govt_drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['govt_drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Govt Drawings File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="govt_drawings_file_3" name="govt_drawings_file" class="form-control d-none"  />
                                        <input type="hidden" name="govt_drawings_file_old"  value="<?php echo $return['govt_drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Govt Drawings Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="govt_drawings_remark" name="govt_drawings_remark"><?php echo $return['govt_drawings_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="text-wrap">
                                        <p class="mb-1"><?= __('Basis of Estimate'); ?> -</p>
                                        <p class="mb-1"><?= __('For schedule items - Preferably to be made from latest CPWD/ DSR /State Govt published rate along with GST if any.'); ?></p>
                                        <p class="mb-1"><?= __('Brought out items- Preferably to be made from Quotations of supplier / rate charts of Manufacturers. Estimated needs to be vetted by Govt. Engineer/ Retd. CPWD/ Retd State Govt. Engineer'); ?></p>
                                        <p class="mb-0"><?= htmlspecialchars(__('Estimate also to be signed & stamped by licensed Engineer & Client'), ENT_QUOTES, 'UTF-8'); ?></p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Basis Estimate'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="basis_estimate_yes" name="basis_estimate" value="1" data-file-id="basis_estimate_file"  <?php echo $return['basis_estimate']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Basis Estimate'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="basis_estimate_no" name="basis_estimate" value="0" data-file-id="basis_estimate_file_2"   <?php echo isset($return['basis_estimate'])&&($return['basis_estimate']=='0' || $return['basis_estimate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['basis_estimate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['basis_estimate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Basis Estimate File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="basis_estimate_file_3" name="basis_estimate_file" class="form-control d-none"  />
                                        <input type="hidden" name="basis_estimate_file_old"  value="<?php echo $return['basis_estimate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Basis Estimate Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="basis_estimate_remark" name="basis_estimate_remark"><?php echo $return['basis_estimate_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td class="text-wrap">
                                        <p class="mb-1"><?= __('Stability certificate needs to be attached issued by licensed engineer in case of expansion of existing building.'); ?></p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Stability Certificate'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="stability_certificate_yes" name="stability_certificate" value="1" data-file-id="stability_certificate_file"  <?php echo $return['stability_certificate']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                            <input aria-label='<?= htmlspecialchars(__('Stability Certificate'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="stability_certificate_no" name="stability_certificate" value="0" data-file-id="stability_certificate_file_2"   <?php echo isset($return['stability_certificate'])&&($return['stability_certificate']=='0' || $return['stability_certificate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                    <?php if (!empty($return['stability_certificate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['stability_certificate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Stability Certificate File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="stability_certificate_file_3" name="stability_certificate_file" class="form-control d-none"  />
                                        <input type="hidden" name="stability_certificate_file_old"  value="<?php echo $return['stability_certificate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Stability Certificate Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="stability_certificate_remark" name="stability_certificate_remark"><?php echo $return['stability_certificate_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">C</th>
                                    <th scope="col"><?= __('EXECUTION WORKS') ?></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col">1</th>
                                    <td class="text-wrap">
                                        <p class="mb-1"><?= __('Execution Plan (Mention in briefly, separate annexure can be attached)'); ?></p>
                                    </td>
                                    <td>
                                        <div class="form-radio">

                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Execution Works Plan'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="execution_works_plan_yes" name="execution_works_plan" value="1" data-file-id="execution_works_plan_file"  <?php echo $return['execution_works_plan']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Execution Works Plan'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="execution_works_plan_no" name="execution_works_plan" value="0" data-file-id="execution_works_plan_file_2"   <?php echo isset($return['execution_works_plan'])&&($return['execution_works_plan']=='0' || $return['execution_works_plan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                        <?php if (!empty($return['execution_works_plan_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['execution_works_plan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>

                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Execution Works Plan File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="execution_works_plan_file_3" name="execution_works_plan_file" class="form-control d-none"  />
                                        <input type="hidden" name="execution_works_plan_file_old"  value="<?php echo $return['execution_works_plan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Execution Works Plan Remark'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="execution_works_plan_remark" name="execution_works_plan_remark"><?php echo $return['execution_works_plan_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_c" class="d-none">

                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="3"><?= __('Form C') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="3"><?= __('CHECKLIST FOR SUBMISSION OF PROPOSAL') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 5%;"><?= __('Sr. No') ?></th>
                                    <th scope="col" class="text-center" style="width: 65%;"><?= __('Points') ?></th>
                                    <th scope="col" class="text-center" style="width: 30%;"><?= __('Details') ?></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td><?= __('Address of registered office of the Organisation (in India)'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Address Registered Office'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="address_registered_office" name="address_registered_office"><?php echo $return['address_registered_office']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><?= __('Any branches of the Organisation? Kindly mention'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Branches Organisation'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="branches_organisation" name="branches_organisation"><?php echo $return['branches_organisation']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><?= __('Registered as a trust/society/section 8 company or Other'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Registered As'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="registered_as" name="registered_as"><?php echo $return['registered_as']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td><?= __('How long has the Organisation been in existence in years?'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Years Of Existence'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="years_of_existence" name="years_of_existence"><?php echo $return['years_of_existence']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td><?= __('Which is the Organisation’s geographical area of work?'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Geographical Area Of Work'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="geographical_area_of_work" name="geographical_area_of_work"><?php echo $return['geographical_area_of_work']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>
                                        <p class="mb-1"><?= __('What is/are your Organisation\'s expertise area/s?'); ?></p>
                                        <p class="mb-1"><?= __('a. Skill Development'); ?></p>
                                        <p class="mb-1"><?= htmlspecialchars(__('b. Health & Hygiene'), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="mb-1"><?= __('c. Education'); ?></p>
                                        <p class="mb-1"><?= __('d. Community Development'); ?></p>
                                        <p class="mb-1"><?= __('e. Environment Sustainability'); ?></p>
                                        <p class="mb-0"><?= __('f. Other/Multiple (Kindly mention)'); ?></p>
                                    </td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Expertise Areas'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="expertise_areas" name="expertise_areas"><?php echo $return['expertise_areas']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td><?= __('Names of Board Members/Trustees and CEO.'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Board Members'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="board_members" name="board_members"><?php echo $return['board_members']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td><?= __('Are any of the trustees/board members affiliated to any political/religious Organisation?'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Affiliated Political Religious'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="affiliated_political_religious" name="affiliated_political_religious"><?php echo $return['affiliated_political_religious']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td><?= __('What are the sources of funding of the Organisation?'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Sources Of Funding'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="sources_of_funding" name="sources_of_funding"><?php echo $return['sources_of_funding']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td><?= __('Has the Organisation received any awards/recognition for its work? Kindly mention'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Awards Recognition'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="awards_recognition" name="awards_recognition"><?php echo $return['awards_recognition']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td><?= __('Does it receive funds/implement programs for the government? If yes, details'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Government Funds Programs'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="government_funds_programs" name="government_funds_programs"><?php echo $return['government_funds_programs']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td><?= __('Has the Organisation been successful in handing over projects/making projects sustainable? Give example'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Projects Sustainability'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="projects_sustainability" name="projects_sustainability"><?php echo $return['projects_sustainability']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td><?= __('Has the Organisation been blacklisted by government/PSU or any corporate? If yes, reasons for it'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Blacklisted Details'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="blacklisted_details" name="blacklisted_details"><?php echo $return['blacklisted_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td><?= __('Is the Organisation empanelled by any reputed institute or agency? If yes, give details'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Empanelled Details'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="empanelled_details" name="empanelled_details"><?php echo $return['empanelled_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td><?= __('Is the Organisation associated with any other corporate for implementing CSR projects? Mention names'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Associated Csr Projects'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="associated_csr_projects" name="associated_csr_projects"><?php echo $return['associated_csr_projects']??'';  ?></textarea></td>
                                </tr>

                                <tr>
                                    <td>16</td>
                                    <td><?= __('Strength of staff (number of full-time employees)'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('Staff Strength'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="staff_strength" name="staff_strength"><?php echo $return['staff_strength']??'';  ?></textarea></td>
                                </tr>


                                <tr>
                                    <td>17</td>
                                    <td><?= __('Please provide two references of past funders of your projects (Email id/Contact Person with Designation/Contact Number)'); ?></td>
                                    <td><textarea aria-label='<?= htmlspecialchars(__('References Past Funders'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control height-50" id="references_past_funders" name="references_past_funders"><?php echo $return['references_past_funders']??'';  ?></textarea></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_d" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="3" style="width: 100%;"><?= __('Form D') ?></th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="1" style="width: 5%;"><?= __('Sr. No') ?></th>
                                    <th scope="col" class="text-center" colspan="2" style="width: 95%;"><?= __('Summary Sheet for the Proposal') ?></th>
                                </tr>
                                <tr>
                                    <td >1</td>
                                    <td ><?= __('Title of the project'); ?></td>
                                    <td ><input aria-label='<?= htmlspecialchars(__('Title Of Project'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="title_of_project" name="title_of_project"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><?= __('Project Description'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('(Write a brief description of project)'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="formd_project_description" name="formd_project_description" placeholder="<?= __('(Write a brief description of project)') ?>"><?php echo $return['formd_project_description']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>3</td>

                                    <td><?= __('Name of Executing Organisation'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Executing Organisation'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="executing_organisation" name="executing_organisation"><?php echo $return['executing_organisation']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>4</td>

                                    <td><?= htmlspecialchars(__('Address of Organisation (Registered address & address of branch from which proposal is given)'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Organisation Address'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="organisation_address" name="organisation_address"><?php echo $return['organisation_address']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>5</td>

                                    <td><?= __('Contact Person'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Contact Person'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="contact_person" name="contact_person"><?php echo $return['contact_person']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>6</td>

                                    <td><?= htmlspecialchars(__('Contact Number & email address'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Contact Details'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="contact_details" name="contact_details"><?php echo $return['contact_details']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>7</td>

                                    <td><?= __('Experience of the Organisation'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('(Mention no. of years of experience & short brief about other projects executed)'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="organisation_experience" name="organisation_experience" placeholder="<?= __('(Mention no. of years of experience & short brief about other projects executed)') ?>"><?php echo $return['organisation_experience']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>8</td>

                                    <td><?= __('Place of execution of project'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Execution Place'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="execution_place" name="execution_place"><?php echo $return['execution_place']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>9</td>

                                    <td><?= __('Reason for choice of place'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Place Reason'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="place_reason" name="place_reason"><?php echo $return['place_reason']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>10</td>

                                    <td><?= __('Tenure of the project'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Project Tenure'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="project_tenure" name="project_tenure"><?php echo $return['project_tenure']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>11</td>

                                    <td><?= __('Cost of the project (enclose a detailed budget)'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Project Cost'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="project_cost" name="project_cost"><?php echo $return['project_cost']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>12</td>

                                    <td><?= __('Funds requested from Infisuite'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Funds Infisuite'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="funds_infisuite" name="funds_infisuite"><?php echo $return['funds_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>13</td>

                                    <td><?= __('Funds requested from any other sources if any'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Funds Other Sources'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="funds_other_sources" name="funds_other_sources"><?php echo $return['funds_other_sources']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>14</td>

                                    <td><?= __('Objective of the project'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Project Objective'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="project_objective" name="project_objective"><?php echo $return['project_objective']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>15</td>

                                    <td><?= __('Reason of Organisation preferring Infisuite to be part of this project'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Reason Infisuite'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="reason_infisuite" name="reason_infisuite" ><?php echo $return['reason_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>16</td>

                                    <td><?= __('Benefits/Outcomes expected from the project'); ?></td>
                                    <td>
                                        <textarea aria-label='<?= htmlspecialchars(__('Project Benefits'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" id="project_benefits" name="project_benefits"><?php echo $return['project_benefits']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>17</td>

                                    <td><?= __('Quantitative (Mention as per parameter suitable, but make sure to mention number of beneficiaries)'); ?></td>
                                    <td>
                                        <input aria-label='1.' class="form-control" type="text" id="quantitative_1" name="quantitative_1" placeholder="1." value="<?php echo $return['quantitative_1']??'';  ?>" />
                                        <input aria-label='2.' class="form-control mt-1" type="text" id="quantitative_2" name="quantitative_2" placeholder="2." value="<?php echo $return['quantitative_2']??'';  ?>" />
                                        <input aria-label='3.' class="form-control mt-1" type="text" id="quantitative_3" name="quantitative_3" placeholder="3." value="<?php echo $return['quantitative_3']??'';  ?>" />
                                        <input aria-label='4.' class="form-control mt-1" type="text" id="quantitative_4" name="quantitative_4" placeholder="4." value="<?php echo $return['quantitative_4']??'';  ?>" />
                                        <input aria-label='5.' class="form-control mt-1" type="text" id="quantitative_5" name="quantitative_5" placeholder="5." value="<?php echo $return['quantitative_5']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>18</td>
                                    <td><?= __('Qualitative (parameters which are difficult to capture in numbers)'); ?></td>
                                    <td>
                                        <input aria-label='1.' class="form-control" type="text" id="qualitative_1" name="qualitative_1" placeholder="1." value="<?php echo $return['qualitative_1']??'';  ?>"/>
                                        <input aria-label='2.' class="form-control mt-1" type="text" id="qualitative_2" name="qualitative_2" placeholder="2." value="<?php echo $return['qualitative_2']??'';  ?>" />
                                        <input aria-label='3.' class="form-control mt-1" type="text" id="qualitative_3" name="qualitative_3" placeholder="3." value="<?php echo $return['qualitative_3']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>19</td>
                                    <td><?= __('Requirement of any approval from local bodies/government/municipality or any statutory body'); ?></td>
                                    <td>
                                        <input aria-label='<?= htmlspecialchars(__('Yes or No, If yes, give details in short'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" type="text" id="approval_requirement" name="approval_requirement" placeholder="<?= __('Yes or No, If yes, give details in short') ?>" value="<?php echo $return['approval_requirement']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>20</td>
                                    <td><?= __('Whether similar projects executed earlier?'); ?></td>
                                    <td>
                                        <input aria-label='<?= htmlspecialchars(__('Yes or No, If yes, give details in short'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" type="text" id="similar_projects" name="similar_projects" placeholder="<?= __('Yes or No, If yes, give details in short') ?>"  value="<?php echo $return['similar_projects']??'';  ?>"/>
                                    </td>
                                </tr>
                                <tr><td>21</td>
                                    <td><?= __('In case the proposal is approved, the organisation is okay to open separate bank account for Infisuite? (Yes/No)'); ?></td>
                                    <td>
                                        <input aria-label='<?= htmlspecialchars(__('Bank Account Infisuite'), ENT_QUOTES, 'UTF-8'); ?>' class="form-control" type="text" id="bank_account_infisuite" name="bank_account_infisuite" value="<?php echo $return['bank_account_infisuite']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>22</td>
                                    <td><?= __('Information given above is true and we can provide the documentary proof of it in case of enquiry'); ?></td>
                                    <td>
                                    <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Information True'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="information_true_yes" data-file-id="information_true_file" name="information_true" value="1"  <?php echo $return['information_true']?'checked':'' ; ?>>
                                                    <i class="helper"></i><?= __('Yes'); ?>
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='<?= htmlspecialchars(__('Information True'), ENT_QUOTES, 'UTF-8'); ?>' type="radio" id="information_true_no" data-file-id="information_true_file_2" name="information_true" value="0"  <?php echo isset($return['information_true'])&&($return['information_true']=='0' || $return['information_true'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i><?= __('No'); ?>
                                                </label>
                                            </div>
                                            <?php if (!empty($return['information_true_file'])): ?>
                                                <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['information_true_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                            <?php endif; ?>
                                        </div>
                                        <div><input aria-label='<?= htmlspecialchars(__('Information True File'), ENT_QUOTES, 'UTF-8'); ?>' type="file" id="information_true_file_3" name="information_true_file" class="form-control d-none"  />
                                        <input type="hidden" name="information_true_file_old"  value="<?php echo $return['information_true_file']; ?>" /></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_e" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="form_e_table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-start" colspan="8"><?= __('Form E') ?></th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="text-start" colspan="8">
                                            <h3 class="f-18 f-w-600 mt-4 text-center text-underline"><?= __('Track Record of Projects') ?></h3>
                                            <p class="text-center f-14"><?= __('(Kindly mention projects undertaken in the last 3 years.'); ?><br /><?= __('If an organisation is existing for 3 years, track record from inception till date is required)'); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?= __('Sr. No.') ?></th>
                                        <th scope="col"><?= __('Project Description') ?></th>
                                        <th scope="col"><?= __('Focus Area') ?></th>
                                        <th scope="col"><?= __('Location') ?></th>
                                        <th scope="col"><?= __('Period (To - From)') ?></th>
                                        <th scope="col"><?= __('Value in INR Lakhs') ?></th>
                                        <th scope="col"><?= __('Number of Beneficiaries') ?></th>
                                        <th scope="col"><?= __('Partner Name (Funding Agency)') ?></th>
                                        <th scope="col"><?= __('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                            <?php if (!empty($return['project_data'])): ?>
            <?php
            $project_data = json_decode($return['project_data'], true);
            $srNo = 1;
            foreach ($project_data as $project) {
            ?>
                <tr>
                    <td><?= $srNo++; ?></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="project_description_<?= $srNo; ?>" name="project_description[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['project_description'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="focus_area_<?= $srNo; ?>" name="focus_area[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['focus_area'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="location_<?= $srNo; ?>" name="location[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['location'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="period_<?= $srNo; ?>" name="period[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['period'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Enter number'), ENT_QUOTES, 'UTF-8'); ?>' type="number" class="form-control text_align_right" id="value_<?= $srNo; ?>" name="value[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['value'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Enter number'), ENT_QUOTES, 'UTF-8'); ?>' type="number" class="form-control text_align_right" id="beneficiaries_<?= $srNo; ?>" name="beneficiaries[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['beneficiaries'] ?? '-') ?>"></td>
                    <td><input aria-label='<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="partner_name_<?= $srNo; ?>" name="partner_name[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['partner_name'] ?? '-') ?>"></td>
                    <td class="text-center">
                        <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-form-e-row' ></i>

                    </td>
                </tr>
            <?php } ?>
        <?php endif; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="add_form_e_row"><?= __('Add More') ?></button>
                        </div>
                    </div>
                </div>

                <div id="form_f" >
                <div class="mt-3 enclosures">

                    <h3 class="f-18 f-w-600 mt-4 text-center text-underline mb-4"><?= __('Declaration') ?></h3>
                    <p class="f-14"><?= __('I / We hereby declare and certify that all the information given in the application form and in the documents submitted are true and correct to the best of my knowledge.'); ?></p>
                    <p class="f-14"><?= __('I / We acknowledge that in case of misrepresentation of information or furnishing of false information, my application is liable to be rejected and /or approved project is be liable to be terminated by BPCL at any stage.'); ?></p>
                    <p class="f-14"><?= __('I / We further declare and certify that our organisation / company / group of companies has / have not been blacklisted or been put on the holiday list by any of the Public Sector Undertakings in India or Statutory authority or Government.'); ?></p>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600"><?= __('Signature'); ?></span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='<?= htmlspecialchars(__('Signature...'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="signature" name="signature" placeholder="<?= __('Signature...') ?>" value="<?php echo $return['signature']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600"><?= __('Name'); ?></span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='<?= htmlspecialchars(__('Name...'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="name" name="name" placeholder="<?= __('Name...') ?>" value="<?php echo $return['name']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600"><?= __('Designation'); ?></span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='<?= htmlspecialchars(__('Designation...'), ENT_QUOTES, 'UTF-8'); ?>' type="text" class="form-control" id="designation" name="designation" placeholder="<?= __('Designation...') ?>" value="<?php echo $return['designation']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600"><?= __('Seal of the Organisation'); ?></span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='<?= htmlspecialchars(__('Seal of the Organisation...'), ENT_QUOTES, 'UTF-8'); ?>' type="file" class="form-control" id="organisation_seal" name="organisation_seal" placeholder="<?= __('Seal of the Organisation...') ?>">
                            <input type="hidden" name="organisation_seal_old"  value="<?php echo $return['organisation_seal']; ?>" />
                        </div>
                        <div class="col-lg-1">
                         <?php if (!empty($return['organisation_seal'])): ?>
                            <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['organisation_seal']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mt-5">
                        <button type="button" class="btn btn-primary waves-effect waves-light m-r-30 mt-3 previous_open"><?= __('Previous') ?></button>
                    </div>
                    <div class="col-lg-6 mt-5">
                        <button class="btn btn-primary mt-3 float-end" type="submit"><?= __('Save') ?></button>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>
</div>

<div
    id="csr_config"
    data-subtotal-count="<?= count($groupsData) ?>"
    data-income-count="<?= count($income_data ?? []) ?>"
    data-expenditure-count="<?= count($expenditure_data ?? []) ?>"
    data-assets-count="<?= count($assets_data ?? []) ?>"
    data-liabilities-count="<?= count($liabilities_data ?? []) ?>"
    data-form-e-row-counter="<?= isset($srNo) ? ($srNo - 1) : 0 ?>"
    data-old-id="<?= isset($rid) ? htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') : 0 ?>"
    hidden>
</div>

<div
    id="csr_config1"
    data-old-id="<?= !empty($rid) ? htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') : '' ?>"
    hidden>
</div>

       <?php
echo $todisplaythree = todisplaythree(array('table'=>$table_name,'originator'=>$return['created_by'],'encode'=>'no','id'=>$return['id'],'dis_query'=>'no','dis_timeline'=>'yes','dis_comments'=>'no','dis_track'=>'no'));
        ?>
<script src="assets/js/custom_view_csr_proposal_details.js"></script>
<script src="assets/js/custom_insert_csr_proposal_details.js"></script>
