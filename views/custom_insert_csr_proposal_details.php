<?php
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
        <h5>CSR</h5>
    </div>
    <div class="card-body">

        <?php
        $url = API . "?mode=getcsrsetting&method=get";
        $response = callAPI($url);
        $response = json_decode($response, 1);
        ?>
        <div class="general_instructions">
            <div class="card-header mb-3">
                <h5>General Instructions</h5>
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
                    <button class="btn btn-primary waves-effect waves-light  m-r-30 mt-3 general_step_1">Next</button>
                </div>
            </div>
        </div>

        <div class="general_instructions_2 m-l-40 mt-4 d-none" id="printableArea">

            <form id="myForm" action="" method="post">
                <div class="row">
                    <div class="col-lg-1">
                        <p class="f-w-600">Date: -</p>
                    </div>
                    <div class="col-lg-6">

        <input aria-label='Date' name="date" class="form-control" type="date" id="date" value="<?php echo isset($return['date']) ? $return['date'] : date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Financial Year: -</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='Financial Year' name="financial_year" id="financial_year" class="form-control form-select js-example-basic-single" required>
                            <option value=""> Financial Year</option>

                            <?php foreach($financial_years as $row){
                                $selected = (isset($return['financial_year']) && $return['financial_year'] == $row['genc_detail_code']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $selected; ?>> <?php echo $row['genc_detail_name']; ?></option>

                            <?php  } ?>

                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Entity Name: -</p>
                    </div>
                    <div class="col-lg-6 w-full">

                        <select aria-label='Institue Name' class="form-control js-example-basic-single requried_field" name="institue_name" id="institue_name">
                            <option value="">Entity Name</option>
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
                        <p class="f-w-600">Type Of Work</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='Type Of Work' class="form-control js-example-basic-single requried_field" name="type_of_work" id="type_of_work" >
                            <option value="">Select Any One</option>
                            <?php foreach($csr_type_of_works as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['type_of_work'] == $row['id'] ? "selected":"";?>><?php echo $row['type_of_work']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Type Of Project</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='Type Of Project' class="form-control js-example-basic-single requried_field" name="type_of_project" id="type_of_project" >
                            <option value="">Select Any One</option>
                            <?php foreach($csr_type_of_projects as $row){ ?>
                                <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $return['type_of_project'] == $row['genc_detail_code'] ? "selected":"";?>><?php echo $row['genc_detail_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">CSR Working Area</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='Working Area' class="form-control js-example-basic-single requried_field" name="working_area" id="working_area" >
                            <option value="">Select Any One</option>
                            <?php foreach($csr_areas as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['working_area'] == $row['id'] ? "selected":"";?>><?php echo $row['csr_area']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Location</p>
                    </div>
                    <div class="col-lg-6 w-full">
                        <select aria-label='Csr Location' class="form-control js-example-basic-single requried_field" name="csr_location" id="csr_location" >
                            <option value="">Select Any One</option>
                            <?php foreach($locations as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $return['csr_location'] == $row['id'] ? "selected":"";?>><?php echo $row['location_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Project Name</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <textarea aria-label='Proposal for...' name="subject" id="subject" class="form-control height-80" placeholder="Proposal for...">
                                <?php echo htmlspecialchars($return['subject']) ?? ''; ?>
                            </textarea>
                            <span class="f-12">(Subject for CSR).</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">1.0 About the Organisation</h3>
                    <div class="form-group col-lg-6">

                        <textarea aria-label='About Organisation' name="about_organisation" id="about_organisation" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['about_organisation']) ?? ''; ?></textarea>
                        <span class="f-12">(Organisational information- Historical overview/ past experience/ type of projects executed/ reach and presence within country & abroad/ staff strength/ success stories/accolades/awards received if any. Also give website link if available).</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">2.0 Background</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Background' name="background" id="background" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['background']) ?? ''; ?></textarea>
                        <span class="f-12">(Give details of project background elaborating on the need of proposed project. Kindly mention details of need assessment/appraisal carried out)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">2.1 Thematic Area</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Thematic Area' name="thematic_area" id="thematic_area" class="form-control height-80" placeholder="">
                            <?php echo htmlspecialchars($return['thematic_area']) ?? ''; ?></textarea>
                        <span class="f-12">(Mention the thrust area/s, Schedule VII item and Sustainable Development Goal (SDG) under which the proposed project would be categorised)</span>
                    </div>
                    <div class="col-lg-10">
                        <table class="table table-bordered">
                            <tr>
                                <th scope="col">Thrust Area</th>
                                <th scope="col">Schedule VII item</th>
                            </tr>
                            <tr>
                                <td>Education</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                <input name="education" id="education" class="border-checkbox csr-checkbox" type="checkbox" value="0" <?php echo isset($return['education'])?'checked':''; ?>
                                            >
                                            <label class="form-label border-checkbox-label" for="education">(ii) promotion of education including special education</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Environment Sustainability</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="env_sustian" class="border-checkbox csr-checkbox" type="checkbox" id="env_sustian" value="0" <?php echo isset($return['env_sustian'])?'checked':''; ?>>
                                            <label class="form-label border-checkbox-label" for="env_sustian">(iv) Ensuring environmental sustainability</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Health/Hygiene</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="health" class="border-checkbox csr-checkbox" type="checkbox" id="health" value="0" <?php echo isset($return['health'])?'checked':''; ?>>
                                            <label class="form-label border-checkbox-label" for="health">(i) promoting healthcare including preventive healthcare and sanitation</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Skill Development</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="skill_development" class="border-checkbox csr-checkbox" type="checkbox" id="skill_development" value="0" <?php echo isset($return['skill_development'])?'checked':''; ?>>
                                            <label class="form-label border-checkbox-label" for="skill_development">(ii) employment enhancing vocational skills and livelihood enhancement projects</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Community Development</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input name="comu_development" class="border-checkbox csr-checkbox" type="checkbox" id="comu_development" value="0" <?php echo isset($return['comu_development'])?'checked':''; ?>>
                                            <label class="form-label border-checkbox-label" for="comu_development">(x) Rural development projects/ Multiple categories</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Others or More than one of the above (Please Specify)</td>
                                <td>
                                    <textarea aria-label='Other Thrust Area' name="other_thrust_area" id="other_thrust_area" class="form-control height-80" placeholder="">
                                        <?php echo isset($return['other_thrust_area'])?$return['other_thrust_area']:''; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-22 f-w-600">Please specify the SDG/s in addition to the above.</h3>
                    <h3 class="f-18 f-w-600 mt-3">3.0 Proposal</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='SDGproposal' name="SDGproposal" id="SDGproposal" class="form-control height-80" placeholder="">
                            <?php echo $return['SDGproposal'] ??''; ?></textarea>
                        <span class="f-12">(Mention the proposal objectives. Explain the activities planned in order to achieve the specific objective/s along with the time frame)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.1 Methodology</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Methodology' name="methodology" id="methodology" class="form-control height-80" placeholder="">
                            <?php echo $return['methodology'] ??''; ?></textarea>
                        <span class="f-12">(Define the methodology for implementation of the program such as implementation schedule, execution plan, selection criteria for specific beneficiaries etc.)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.2 Target Group</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Target Group' name="target_group" id="target_group" class="form-control height-80" placeholder="">
                            <?php echo $return['target_group'] ??''; ?></textarea>
                        <span class="f-12">(Mention Geographical area/location of the project and the profile of the beneficiaries - details of Male/Female, minority or vulnerable group/others etc)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.3 Team for the project</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Team Project' name="team_project" id="team_project" class="form-control height-80" placeholder="">
                            <?php echo $return['team_project'] ??''; ?></textarea>
                        <span class="f-12">(Mention job profile & eligibility criteria for selection of staff/team members for the project i.e. qualifications & experience, Roles & responsibilities, Organization structure etc.)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.4 Monitoring Mechanism</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Monitoring Mechanism' name="monitoring_mechanism" id="monitoring_mechanism" class="form-control height-80" placeholder="">
                            <?php echo $return['monitoring_mechanism'] ??''; ?></textarea>
                        <span class="f-12">(Mention the monitoring mechanism such as frequency of visits, plan for collection of data, monitoring of activity timeline & frequency of submission of reports)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.1 Outcomes/Result expected</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Outcomes Expected' name="outcomes_expected" id="outcomes_expected" class="form-control height-80" placeholder="">
                            <?php echo $return['outcomes_expected'] ??''; ?></textarea>
                        <span class="f-12">(Mention the Quantitative & Qualitative outcomes expected from the project, outcomes should be directly linked to proposed objectives; specific targets should be benchmarked. It is recommended to use frameworks such as project matrix, verifiable indicators, data analysis.)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.2 Execution Plan</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Execution Plan' name="execution_plan" id="execution_plan" class="form-control height-80" placeholder="">
                            <?php echo $return['execution_plan'] ??''; ?></textarea>
                        <span class="f-12">(Gantt chart with time line against activities may be given in tabular/other form)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.3 Impact Assessment</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Impact Assessment' name="impact_assessment" id="impact_assessment" class="form-control height-80" placeholder="">
                            <?php echo $return['impact_assessment'] ??''; ?></textarea>
                        <span class="f-12">(Mention the plan to measure the impact of planned interventions of this project as defined in the scope of work)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">5.1 Financial Implications</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Financial Implications' class="form-control height-80" id="financial_implications" name="financial_implications" placeholder="">
                            <?php echo $return['financial_implications'] ??''; ?></textarea>
                        <span class="f-12">(Format given below is indicative. Based on the nature of the project, kindly use appropriate format)</span>
                    </div>
                    <div class="general_instructions">
                        <ul>
                            <li>Breakup for each line item should be given. Separate annexure to be provided if needed</li>
                            <li>Any procurement items has to be supported with documents proving competitive rate in the market</li>
                            <li>List down items covered under Administrative expenses.</li>
                        </ul>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="financial_table">
                            <thead>
                                <tr>
                                    <th scope="col">Sr. No</th>
                                    <th scope="col" class="text-center">Details</th>
                                    <th scope="col" class="text-center">Per unit Cost<br />(A)</th>
                                    <th scope="col" class="text-center">Total Number of Units<br />(B)</th>
                                    <th scope="col" class="text-center">Total Budget for 1 month/year<br />(C=A*B)</th>
                                    <th scope="col" class="text-center">No. of months/years</th>
                                    <th scope="col" class="text-center">Budget For Months/Years<br />(D=C*No. of months/years)</th>
                                    <th scope="col" class="text-center">Remarks<br />(Justification of cost element)</th>
                                    <th scope="col" class="text-center">Actions</th>
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
            echo '<td><input aria-label="Groups" type="text" class="form-control" id="details_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][details][' . ($index ) . ']" value="' . htmlspecialchars($detail) . '"></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control unit-cost text_align_right" id="unit_cost_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][unit_cost][' . ($index ) . ']" value="' . htmlspecialchars($unitCost[$index] ?? '') . '" data-group="group_' . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control num-units text_align_right" id="num_units_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][num_units][' . ($index) . ']" value="' . htmlspecialchars($numUnits[$index] ?? '') . '" data-group="group_' . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control budget text_align_right" id="budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][budget][' . ($index) . ']" value="' . htmlspecialchars($budget[$index] ?? '') . '" readonly></td>';
            echo '<td><input aria-label="Groups" type="number" class="form-control num-months text_align_right" id="num_months_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][num_months][' . ($index) . ']" value="' . htmlspecialchars($numMonths[$index] ?? '') . '" data-group="group_' . htmlspecialchars($groupId) . '"></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control total-budget text_align_right" id="total_budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][total_budget][' . ($index) . ']" value="' . htmlspecialchars($totalBudget[$index] ?? '') . '" readonly></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control" id="remarks_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][remarks][' . ($index) . ']" value="' . htmlspecialchars($remarks[$index] ?? '') . '"></td>';
            echo '<td ></td>';
            echo '</tr>';
        }

            // Subtotal Row
            echo '<tr data-group="group_' . htmlspecialchars($groupId) . '" class="subtotal-row bg-light-row" >';
            echo '<td></td>';
            echo '<th scope="col" class="text-center">Sub Total (' . htmlspecialchars($groupId) . ')</th>';
            echo '<td></td><td></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control budget-subtotal text_align_right" id="budget_sub_total_' . htmlspecialchars($groupId) . '" name="groups[' . htmlspecialchars($groupId) . '][subtotal][' . htmlspecialchars($groupId) . ']" value="' . htmlspecialchars($subtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td><input aria-label="Groups" type="text" class="form-control total-budget-subtotal text_align_right" id="total_budget_sub_total_' . htmlspecialchars($groupId) . '" name="groups[' . htmlspecialchars($groupId) . '][total_subtotal][' . htmlspecialchars($groupId) . ']" value="' . htmlspecialchars($totalSubtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td class="text-center"> <i class="fas fa-trash-alt text-danger cursor-pointer remove-row data-delete-group" data-delete-group="group_' . htmlspecialchars($groupId) . '"></i></td>';
            echo '</tr>';
    }
}

?>

                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-primary waves-effect waves-light" type="button" id="add_row" >Add more</button>
                                    </td>
                                </tr>
                                <tr class="bg-light-row" >
                                    <td></td>
                                    <th scope="col" class="text-center"><strong>Grand Total</strong></th>
                                    <td></td>
                                    <td></td>
                                    <td><input aria-label='Budget Grand Total' type="text" class="form-control text_align_right" id="budget_grand_total" name="budget_grand_total" value="<?php echo $return['budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td><input aria-label='Total Budget Grand Total' type="text" class="form-control text_align_right" id="total_budget_grand_total" name="total_budget_grand_total" value="<?php echo $return['total_budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>

                        <div class="mt-3">
                            <h3 class="f-18 f-w-600 mt-3">5.2 Tentative payment schedule</h3>
                            <div class="form-group col-lg-6">
                                <textarea aria-label='Payment Schedule' class="form-control height-80" id="payment_schedule" name="payment_schedule" placeholder="">
                                    <?php echo $return['payment_schedule'] ??''; ?></textarea>
                                <span class="f-12">(Link payment schedule with project deliverables and timeline. It will be reviewed by Infisuite and the final payment schedule might differ significantly as per nature of the project & Infisuite guidelines. For final payment, 10% of the total budget will be kept on hold until the submission of the project completion report and audited fund utilisation certificate)</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentScheduleTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Sr.No</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">% of Total Project Budget</th>
                                            <th scope="col">Amount in Rs</th>
                                            <th scope="col">Deliverables to be achieved</th>
                                            <th scope="col">Actions</th>
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
        echo '<td><input aria-label="Payment Schedule Description" type="text" class="form-control" id="payment_schedule_description_' . ($payId) . '" name="payment_schedule_description[' . ($payId ) . ']" value="' . htmlspecialchars($description[$payId] ?? '') . '"></td>';
        echo '<td><input aria-label="Payment Schedule Percent" type="number" class="form-control percent-input text_align_right" id="percent_' . ($payId) . '" name="payment_schedule_percent[' . ($payId) . ']" value="' . htmlspecialchars($percentValue) . '" data-row="' . $payId . '"></td>';
        echo '<td><input aria-label="Payment Schedule Amount" type="text" class="form-control amount-input text_align_right" id="amount_' . ($payId + 1) . '" name="payment_schedule_amount[' . ($payId) . ']" value="' . htmlspecialchars($amount[$payId] ?? '') . '" readonly></td>';
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
                                                <button class="btn btn-primary waves-effect waves-light" type="button" id="add_payment_schedule_row">Add more</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Grand Total</th>
                                            <th scope="col"></th>
                                            <th scope="col"><input aria-label='Payment Grand Total' type="text" class="form-control text_align_right" id="payment_grand_total" name="payment_grand_total" value="<?php echo $return['payment_grand_total'] ??''; ?>" readonly></th>
                                            <th scope="col" colspan="2"><input aria-label='In Words Only...' type="text" class="form-control" id="payment_grand_total_in_words" name="payment_grand_total_in_words" placeholder="In Words Only..." value="<?php echo $return['payment_grand_total_in_words'] ??''; ?>"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">6.0 Sustainability Plan</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Sustainability Plan' class="form-control height-80" id="sustainability_plan" name="sustainability_plan" placeholder="">
                            <?php echo $return['sustainability_plan'] ??''; ?></textarea>
                        <span class="f-12">(Provide an action plan giving details of how the project will be sustainable after exit of Infisuite & the implementing partner. This must include maintenance and ownership of assets where applicable)</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">7.0 Conclusion</h3>
                    <div class="form-group col-lg-6">
                        <textarea aria-label='Conclusion' class="form-control height-80" id="conclusion" name="conclusion" placeholder=""><?php echo $return['conclusion'] ??''; ?></textarea>
                    </div>
                </div>
                <div class="mt-3 enclosures">
                    <h3 class="f-18 f-w-600 mt-3">8.0 Enclosures</h3>
                    <div class="general_instructions">
                        <ul>
                            <li>
                                <span class="f-w-600">Form A :- CHECKLIST FOR SUBMISSION OF DOCUMENTS</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox csr-form-radio" type="radio" id="form_a_yes" name="form_a" value="1"  <?php echo $return['form_a']=='1'?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_a_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox  csr-form-radio" type="radio" id="form_a_no" name="form_a" value="0"  <?php echo isset($return['form_a'])&&($return['form_a']=='0' || $return['form_a'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_a_no">No</label>
                                    </div>

                                    <?php if (!empty($return['form_a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_a_file']); ?>"  class="mb-2 " download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>

                                </div>

                                <div class="col-lg-3 mt-2">
                                    <input aria-label='Form A File' type="file" id="form_a_file" name="form_a_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_a_file_old"  value="<?php echo $return['form_a_file']; ?>" />

                                </div>
                            </li>
                            <li>
                                <span class="f-w-600">Form B :- CHECKLIST FOR INFRASTRUCTURE PROJECTS</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_b_yes" name="form_b" value="1"  <?php echo $return['form_b']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_b_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_b_no" name="form_b" value="0"  <?php echo isset($return['form_b'])&&($return['form_b']=='0' || $return['form_b'] == '') ?'checked':''  ?>  />
                                        <label class="form-label border-checkbox-label" for="form_b_no">No</label>
                                    </div>
                                    <?php if (!empty($return['form_b_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_b_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>

                                <div class="col-lg-3 mt-2">
                                    <input aria-label='Form B File' type="file" id="form_b_file" name="form_b_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_b_file_old"  value="<?php echo $return['form_b_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600">Form C :- CHECKLIST FOR SUBMISSION OF PROPOSAL</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_c_yes" name="form_c" value="1"  <?php echo $return['form_c']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_c_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_c_no" name="form_c" value="0"  <?php echo isset($return['form_c'])&&($return['form_c']=='0' || $return['form_c'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_c_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_c_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_c_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='Form C File' type="file" id="form_c_file" name="form_c_file" class="form-control d-none csr-form-file" />
                                    <input type="hidden" name="form_c_file_old"  value="<?php echo $return['form_c_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600">Form D :- SUMMARY SHEET FOR THE PROPOSAL</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_d_yes" name="form_d" value="1" <?php echo $return['form_d']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_d_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_d_no" name="form_d" value="0"  <?php echo isset($return['form_d'])&&($return['form_d']=='0' || $return['form_d'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_d_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_d_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_d_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='Form D File' type="file" id="form_d_file" name="form_d_file" class="form-control d-none csr-form-file" />
                                    <input type="hidden" name="form_d_file_old"  value="<?php echo $return['form_d_file']; ?>" />
                                </div>
                            </li>

                            <li>
                                <span class="f-w-600">Form E :- TRACK RECORD OF PROJECTS</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_e_yes" name="form_e" value="1"  <?php echo $return['form_e']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_e_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox csr-form-radio" type="radio" id="form_e_no" name="form_e" value="0"   <?php echo isset($return['form_e'])&&($return['form_e']=='0' || $return['form_e'] == '') ?'checked':''  ?>/>
                                        <label class="form-label border-checkbox-label" for="form_e_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_e_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_e_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input aria-label='Form E File' type="file" id="form_e_file" name="form_e_file" class="form-control d-none csr-form-file"  />
                                    <input type="hidden" name="form_e_file_old"  value="<?php echo $return['form_e_file']; ?>" />
                                </div>
                            </li>

                        </ul>
                        <span class="f-16 f-w-600 d-block mt-4">Kindly ensure the Organisation Seal/Stamp & Sign on each page.</span>
                    </div>
                </div>

                <div id="form_a" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="4">Form A</th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="4">CHECKLIST FOR SUBMISSION OF DOCUMENTS</th>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 5%;">Sr. No</th>
                                    <th scope="col" style="width: 50%;">Description of documents</th>
                                    <th scope="col" class="text-center" style="width: 25%;">Attached (Yes / No)</th>
                                    <th scope="col" class="text-center" style="width: 20%;">Remark</th>
                                </tr>
                                <tr>
                                    <td>1*</td>
                                    <td>Trust deed</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Trust Deed' type="radio" id="trust_deed_yes" data-file-id="trust_deed_file" name="trust_deed" value="1"  <?php echo $return['trust_deed']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Trust Deed' type="radio" id="trust_deed_no" data-file-id="trust_deed_file_2" name="trust_deed" value="0"  <?php echo isset($return['trust_deed'])&&($return['trust_deed']=='0' || $return['trust_deed'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                            <?php if (!empty($return['trust_deed_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['trust_deed_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Trust Deed File' type="file" id="trust_deed_file_3" name="trust_deed_file" class="form-control d-none"  />
                                            <input type="hidden" name="trust_deed_file_old"  value="<?php echo $return['trust_deed_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Trust Deed Remark' class="form-control height-50" id="trust_deed_remark" name="trust_deed_remark"><?php echo $return['trust_deed_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2*</td>
                                    <td>Certificate of incorporation/Registration certificate</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Incorporation' type="radio" id="incorporation_yes" name="incorporation" value="1" data-file-id="incorporation_file"  <?php echo $return['incorporation']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Incorporation' type="radio" id="incorporation_no" name="incorporation" value="0" data-file-id="incorporation_file_2"  <?php echo isset($return['incorporation'])&&($return['incorporation']=='0' || $return['incorporation'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['incorporation_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['incorporation_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Incorporation File' type="file" id="incorporation_file_3" name="incorporation_file" class="form-control d-none"  />
                                            <input type="hidden" name="incorporation_file_old"  value="<?php echo $return['incorporation_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Incorporation Remark' class="form-control height-50" id="incorporation_remark" name="incorporation_remark">
                                            <?php echo $return['incorporation_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3*</td>
                                    <td>CSR 1 Registration certificate</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Csr1' type="radio" id="csr1_yes" name="csr1" value="1" data-file-id="csr1_file"  <?php echo $return['csr1']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Csr1' type="radio" id="csr1_no" name="csr1" value="0" data-file-id="csr1_file_2"  <?php echo isset($return['csr1'])&&($return['csr1']=='0' || $return['csr1'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['csr1_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['csr1_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Csr1 File' type="file" id="csr1_file_3" name="csr1_file" class="form-control d-none"  />
                                            <input type="hidden" name="csr1_file_old"  value="<?php echo $return['csr1_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Csr1 Remark' class="form-control height-50" id="csr1_remark" name="csr1_remark"><?php echo $return['csr1_remark']??'';  ?> </textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4*</td>
                                    <td>Form 12 A</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form12a' type="radio" id="form12a_yes" name="form12a" value="1" data-file-id="form12a_file"  <?php echo $return['form12a']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form12a' type="radio" id="form12a_no" name="form12a" value="0" data-file-id="form12a_file_2"  <?php echo isset($return['form12a'])&&($return['form12a']=='0' || $return['form12a'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form12a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form12a_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Form12a File' type="file" id="form12a_file_3" name="form12a_file" class="form-control d-none"  />
                                        <input type="hidden" name="form12a_file_old"  value="<?php echo $return['form12a_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Form12a Remark' class="form-control height-50" id="form12a_remark" name="form12a_remark"><?php echo $return['form12a_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5*</td>
                                    <td>Form 80 G</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form80g' type="radio" id="form80g_yes" name="form80g" value="1" data-file-id="form80g_file"  <?php echo $return['form80g']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form80g' type="radio" id="form80g_no" name="form80g" value="0" data-file-id="form80g_file_2"  <?php echo isset($return['form80g'])&&($return['form80g']=='0' || $return['form80g'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form80g_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form80g_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Form80g File' type="file" id="form80g_file_3" name="form80g_file" class="form-control d-none"  />
                                        <input type="hidden" name="form80g_file_old"  value="<?php echo $return['form80g_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Form80g Remark' class="form-control height-50" id="form80g_remark" name="form80g_remark"><?php echo $return['form80g_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6*</td>
                                    <td>35 AC registration if any</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form35ac' type="radio" id="form35ac_yes" name="form35ac" value="1" data-file-id="form35ac_file"  <?php echo $return['form35ac']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Form35ac' type="radio" id="form35ac_no" name="form35ac" value="0" data-file-id="form35ac_file_2"  <?php echo isset($return['form35ac'])&&($return['form35ac']=='0' || $return['form35ac'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form35ac_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form35ac_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Form35ac File' type="file" id="form35ac_file_3" name="form35ac_file" class="form-control d-none"  />
                                        <input type="hidden" name="form35ac_file_old"  value="<?php echo $return['form35ac_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Form35ac Remark' class="form-control height-50" id="form35ac_remark" name="form35ac_remark"><?php echo $return['form35ac_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>7*</td>
                                    <td>PAN card</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Pan' type="radio" id="pan_yes" name="pan" value="1" data-file-id="pan_file"  <?php echo $return['pan']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Pan' type="radio" id="pan_no" name="pan" value="0" data-file-id="pan_file_2"  <?php echo isset($return['pan'])&&($return['pan']=='0' || $return['pan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['pan_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['pan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Pan File' type="file" id="pan_file_3" name="pan_file" class="form-control d-none"  />
                                        <input type="hidden" name="pan_file_old"  value="<?php echo $return['pan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Pan Remark' class="form-control height-50" id="pan_remark" name="pan_remark"><?php echo $return['pan_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>8*</td>
                                    <td>Last 3 financial years audited statements & ITR proof</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Financial Statements' type="radio" id="financial_statements_yes" name="financial_statements" value="1" data-file-id="financial_statements_file"  <?php echo $return['financial_statements']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Financial Statements' type="radio" id="financial_statements_no" name="financial_statements" value="0" data-file-id="financial_statements_file_2"  <?php echo isset($return['financial_statements'])&&($return['financial_statements']=='0' || $return['financial_statements'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['financial_statements_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['financial_statements_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Financial Statements File' type="file" id="financial_statements_file_3" name="financial_statements_file" class="form-control d-none"  />
                                        <input type="hidden" name="financial_statements_file_old"  value="<?php echo $return['financial_statements_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Financial Statements Remark' class="form-control height-50" id="financial_statements_remark" name="financial_statements_remark"><?php echo $return['financial_statements_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>9*</td>
                                    <td>Detailed proposal as per format given</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Proposal' type="radio" id="proposal_yes" name="proposal" value="1" data-file-id="proposal_file"  <?php echo $return['proposal']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Proposal' type="radio" id="proposal_no" name="proposal" value="0" data-file-id="proposal_file_2"  <?php echo isset($return['proposal'])&&($return['proposal']=='0' || $return['proposal'] == '' ?'checked':'')  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['proposal_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['proposal_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Proposal File' type="file" id="proposal_file_3" name="proposal_file" class="form-control d-none"  />
                                        <input type="hidden" name="proposal_file_old"  value="<?php echo $return['proposal_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Proposal Remark' class="form-control height-50" id="proposal_remark" name="proposal_remark"><?php echo $return['proposal_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>FCRA approval</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Fcra' type="radio" id="fcra_yes" name="fcra" value="1" data-file-id="fcra_file"  <?php echo $return['fcra']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Fcra' type="radio" id="fcra_no" name="fcra" value="0" data-file-id="fcra_file_2"  <?php echo isset($return['fcra'])&&($return['fcra']=='0' || $return['fcra'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['fcra_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['fcra_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Fcra File' type="file" id="fcra_file_3" name="fcra_file" class="form-control d-none"  />
                                        <input type="hidden" name="fcra_file_old"  value="<?php echo $return['fcra_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Fcra Remark' class="form-control height-50" id="fcra_remark" name="fcra_remark"><?php echo $return['fcra_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>TDS exemption certificate if applicable</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Tds Exemption' type="radio" id="tds_exemption_yes" name="tds_exemption" value="1" data-file-id="tds_exemption_file"  <?php echo $return['tds_exemption']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Tds Exemption' type="radio" id="tds_exemption_no" name="tds_exemption" value="0" data-file-id="tds_exemption_file_2"  <?php echo isset($return['tds_exemption'])&&($return['tds_exemption']=='0' || $return['tds_exemption'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['tds_exemption_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['tds_exemption_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Tds Exemption File' type="file" id="tds_exemption_file_3" name="tds_exemption_file" class="form-control d-none"  />
                                        <input type="hidden" name="tds_exemption_file_old"  value="<?php echo $return['tds_exemption_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Tds Exemption Remark' class="form-control height-50" id="tds_exemption_remark" name="tds_exemption_remark"><?php echo $return['tds_exemption_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>GST Registration certificate if applicable</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Gst' type="radio" id="gst_yes" name="gst" value="1" data-file-id="gst_file"  <?php echo $return['gst']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Gst' type="radio" id="gst_no" name="gst" value="0" data-file-id="gst_file_2"  <?php echo isset($return['gst'])&&($return['gst']=='0' || $return['gst'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['gst_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['gst_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Gst File' type="file" id="gst_file_3" name="gst_file" class="form-control d-none"  />
                                        <input type="hidden" name="gst_file_old"  value="<?php echo $return['gst_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Gst Remark' class="form-control height-50" id="gst_remark" name="gst_remark"><?php echo $return['gst_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>13*</td>
                                    <td>2 Reference letters of past or present supporters for CSR project</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Reference' type="radio" id="reference_yes" name="reference" value="1" data-file-id="reference_file"  <?php echo $return['reference']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Reference' type="radio" id="reference_no" name="reference" value="0" data-file-id="reference_file_2"  <?php echo isset($return['reference'])&&($return['reference']=='0' || $return['reference'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['reference_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['reference_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Reference File' type="file" id="reference_file_3" name="reference_file" class="form-control d-none"  />
                                        <input type="hidden" name="reference_file_old"  value="<?php echo $return['reference_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Reference Remark' class="form-control height-50" id="reference_remark" name="reference_remark"><?php echo $return['reference_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                            </table>
                            <span class="f-w-600 f-14">*Sr. Nos. are mandatory documents</span>
                        </div>
                    </div>

                    <div class="mt-3 enclosures">
                        <h3 class="f-18 f-w-600 mt-4 text-center">
                            Additional Information in respect of Audited Accounts as mentioned in Serial No. 8 of<br />Form-A of the Proposal Format for submission of CSR Proposals.
                        </h3>
                        <h3 class="f-16 f-w-600 mt-3">1. Please give the sources of income for each of the last 3 Financial Years for which audited accounts have been submitted with the application</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="income_table">
                                <?php
                                $currentYear = date("Y"); // Get the current year
                                $years = [$currentYear - 1, $currentYear - 2, $currentYear - 3]; // Calculate past 3 years
                                ?>

                                <thead>
                                    <tr>
                                        <th scope="col">Sr. No</th>
                                        <th scope="col" class="text-center">Sources of Funds</th>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center">Action</th>
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
                    <input aria-label='Input field' type="text" class="form-control" id="source_<?= $index ?>"
                           name="source_income[<?= $index  ?>]source[<?= $index ?>]"
                           value="<?= $source ?>">
                </td>
                <td>
                    <input aria-label='Enter number' type="number" min="0" class="form-control text_align_right income-input" id="fy1_<?= $index  ?>"
                           name="source_income[<?= $index  ?>]fy1[<?= $index  ?>]"
                           value="<?= $data['fy1'][$index] ?>" >
                </td>
                <td>
                    <input aria-label='Enter number' type="number" min="0" class="form-control text_align_right income-input" id="fy2_<?= $index ?>"
                           name="source_income[<?= $index  ?>]fy2[<?= $index  ?>]"
                           value="<?= $data['fy2'][$index] ?>" >
                </td>
                <td>
                    <input aria-label='Enter number' type="number" min="0" class="form-control text_align_right income-input" id="fy3_<?= $index  ?>"
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
                                        <th scope="col" class="text-end">Total</th>
                                        <td><input aria-label='Total Fy1' type="text" class="form-control text_align_right" id="total_fy1" name="total_fy1" value="<?php echo $return['total_fy1'] ?? '' ; ?>" readonly></td>
                                        <td><input aria-label='Total Fy2' type="text" class="form-control text_align_right" id="total_fy2" name="total_fy2" value="<?php echo $return['total_fy2'] ?? '' ; ?>" readonly></td>
                                        <td><input aria-label='Total Fy3' type="text" class="form-control text_align_right" id="total_fy3" name="total_fy3" value="<?php echo $return['total_fy3'] ?? '' ; ?>" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-start">
                                            <button type="button" class="btn btn-primary" id="add_row_income" >Add More</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 enclosures">
                        <h3 class="f-16 f-w-600 mt-3">2. Provide details of income, expenditure, assets and liabilities during the last 3 Financial Years for which audited accounts are being submitted along with the application. </h3>

                        <h3 class="f-16 f-w-600 mt-4">Total Income:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="total_income_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center">FY (<?= $year; ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                if ($return['income_data']) {
                    $income_data = json_decode($return['income_data'], true);
                    foreach ($income_data as $id => $income) {
                        echo "<tr>";
                        echo "<td><input aria-label='Income' type='number' class='form-control text_align_right' id='income_fy1_{$id
}' name='income[{$id
}]fy1' value='{$income['fy1']
}'></td>";
                        echo "<td><input aria-label='Income' type='number' class='form-control text_align_right' id='income_fy2_{$id
}' name='income[{$id
}]fy2' value='{$income['fy2']
}'></td>";
                        echo "<td><input aria-label='Income' type='number' class='form-control text_align_right' id='income_fy3_{$id
}' name='income[{$id
}]fy3' value='{$income['fy3']
}'></td>";
                        echo "<td><input aria-label='Income' type='text' class='form-control' id='income_var1_{$id
}' name='income[{$id
}]var1' value='{$income['var1']
}'></td>";
                        echo "<td><input aria-label='Income' type='text' class='form-control' id='income_var2_{$id
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
        data-section="income">
    Add more
</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Expenditure:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="expenditure_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                <?php
                if ($return['expenditure_data']) {
                    $expenditure_data = json_decode($return['expenditure_data'], true);
                    foreach ($expenditure_data as $id => $expenditure) {
                        echo "<tr>";
                        echo "<td><input aria-label='Expenditure' type='number' class='form-control text_align_right' id='expenditure_fy1_{$id
}' name='expenditure[{$id
}]fy1' value='{$expenditure['fy1']
}'></td>";
                        echo "<td><input aria-label='Expenditure' type='number' class='form-control text_align_right' id='expenditure_fy2_{$id
}' name='expenditure[{$id
}]fy2' value='{$expenditure['fy2']
}'></td>";
                        echo "<td><input aria-label='Expenditure' type='number' class='form-control text_align_right' id='expenditure_fy3_{$id
}' name='expenditure[{$id
}]fy3' value='{$expenditure['fy3']
}'></td>";
                        echo "<td><input aria-label='Expenditure' type='text' class='form-control' id='expenditure_var1_{$id
}' name='expenditure[{$id
}]var1' value='{$expenditure['var1']
}'></td>";
                        echo "<td><input aria-label='Expenditure' type='text' class='form-control' id='expenditure_var2_{$id
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
        data-section="expenditure">
    Add more
</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Assets:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="assets_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                            <?php
                if ($return['assets_data']) {
                    $assets_data = json_decode($return['assets_data'], true);
                    foreach ($assets_data as $id => $assets) {
                        echo "<tr>";
                        echo "<td><input aria-label='Assetsfy1' type='number' class='form-control text_align_right' id='assets_fy1_{$id
}' name='assets[{$id
}]fy1' value='{$assets['fy1']
}'></td>";
                        echo "<td><input aria-label='Assetsfy2' type='number' class='form-control text_align_right' id='assets_fy2_{$id
}' name='assets[{$id
}]fy2' value='{$assets['fy2']
}'></td>";
                        echo "<td><input aria-label='Assetsfy3' type='number' class='form-control text_align_right' id='assets_fy3_{$id
}' name='assets[{$id
}]fy3' value='{$assets['fy3']
}'></td>";
                        echo "<td><input aria-label='Assetsvar1' type='text' class='form-control' id='assets_var1_{$id
}' name='assets[{$id
}]var1' value='{$assets['var1']
}'></td>";
                        echo "<td><input aria-label='Assetsvar2' type='text' class='form-control' id='assets_var2_{$id
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
        data-section="assets">
    Add more
</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Liabilities:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="liabilities_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th scope="col" class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th scope="col" class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php
                if ($return['liabilities_data']) {
                    $liabilities_data = json_decode($return['liabilities_data'], true);
                    foreach ($liabilities_data as $id => $liabilities) {
                        echo "<tr>";
                        echo "<td><input aria-label='Liabilitiesfy1' type='number' class='form-control text_align_right' id='liabilities_fy1_{$id
}' name='liabilities[{$id
}]fy1' value='{$liabilities['fy1']
}'></td>";
                        echo "<td><input aria-label='Liabilitiesfy2' type='number' class='form-control text_align_right' id='liabilities_fy2_{$id
}' name='liabilities[{$id
}]fy2' value='{$liabilities['fy2']
}'></td>";
                        echo "<td><input aria-label='Liabilitiesfy3' type='number' class='form-control text_align_right' id='liabilities_fy3_{$id
}' name='liabilities[{$id
}]fy3' value='{$liabilities['fy3']
}'></td>";
                        echo "<td><input aria-label='Liabilitiesvar1' type='text' class='form-control' id='liabilities_var1_{$id
}' name='liabilities[{$id
}]var1' value='{$liabilities['var1']
}'></td>";
                        echo "<td><input aria-label='Liabilitiesvar2' type='text' class='form-control' id='liabilities_var2_{$id
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
        data-section="liabilities">
    Add more
</button>

                    </div>
                </div>
                <div id="form_b" class="d-none">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive form_B">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col" class="text-start" colspan="4">Form B</th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="4">CHECKLIST FOR INFRASTRUCTURE PROJECTS</th>
                                </tr>
                                <tr>
                                    <th scope="col" class="w-5">Sr. No</th>
                                    <th scope="col" class="w-50">Documents</th>
                                    <th scope="col" class="w-25 ">Attached (Yes / No)</th>
                                    <th scope="col" class="w-25 ">Remark</th>
                                </tr>
                                <tr>
                                    <th scope="col">A</th>
                                    <th scope="col">LAND RELATED</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Land Ownership document</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Land Ownership' type="radio" id="land_ownership_yes" name="land_ownership" value="1" data-file-id="land_ownership_file"  <?php echo $return['land_ownership']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Land Ownership' type="radio" id="land_ownership_no" name="land_ownership" value="0" data-file-id="land_ownership_file_2"  <?php echo isset($return['land_ownership'])&&($return['land_ownership']=='0' || $return['land_ownership'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['land_ownership_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['land_ownership_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Land Ownership File' type="file" id="land_ownership_file_3" name="land_ownership_file" class="form-control d-none"  />
                                        <input type="hidden" name="land_ownership_file_old"  value="<?php echo $return['land_ownership_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Land Ownership Remark' class="form-control height-50" id="land_ownership_remark" name="land_ownership_remark"><?php echo $return['land_ownership_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Property Card (Commercial/ Vastu/Wetland)</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Property Card' type="radio" id="property_card_yes" name="property_card" value="1" data-file-id="property_card_file"  <?php echo $return['property_card']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Property Card' type="radio" id="property_card_no" name="property_card" value="0" data-file-id="property_card_file_2"  <?php echo isset($return['property_card'])&&($return['property_card']=='0' || $return['property_card'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['property_card_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['property_card_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Property Card File' type="file" id="property_card_file_3" name="property_card_file" class="form-control d-none"  />
                                        <input type="hidden" name="property_card_file_old"  value="<?php echo $return['property_card_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Property Card Remark' class="form-control height-50" id="property_card_remark" name="property_card_remark"><?php echo $return['property_card_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">B</th>
                                    <th scope="col">ESTIMATES & APPROVALS</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td class="text-wrap">
                                        <p class="mb-1">Drawings- Site Plan, Building Plan, Elevation, foundation / structural details. All drawings needs to be signed & stamped by Licensed Engineer/ Architect & Client.</p>
                                        <p class="mb-0">License no. / Registration no. of Architect/Engineer needs to be mentioned.</p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Drawings' type="radio" id="drawings_yes" name="drawings" value="1" data-file-id="drawings_file"  <?php echo $return['drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Drawings' type="radio" id="drawings_no" name="drawings" value="0" data-file-id="drawings_file_2"   <?php echo isset($return['drawings'])&&($return['drawings']=='0' || $return['drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Drawings File' type="file" id="drawings_file_3" name="drawings_file" class="form-control d-none"  />
                                        <input type="hidden" name="drawings_file_old"  value="<?php echo $return['drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Drawings Remark' class="form-control height-50" id="drawings_remark" name="drawings_remark"><?php echo $return['drawings_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Drawings sanctioned by Govt. Authorities</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Govt Drawings' type="radio" id="govt_drawings_yes" name="govt_drawings" value="1" data-file-id="govt_drawings_file"  <?php echo $return['govt_drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Govt Drawings' type="radio" id="govt_drawings_no" name="govt_drawings" value="0" data-file-id="govt_drawings_file_2"   <?php echo isset($return['govt_drawings'])&&($return['govt_drawings']=='0' || $return['govt_drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['govt_drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['govt_drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Govt Drawings File' type="file" id="govt_drawings_file_3" name="govt_drawings_file" class="form-control d-none"  />
                                        <input type="hidden" name="govt_drawings_file_old"  value="<?php echo $return['govt_drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Govt Drawings Remark' class="form-control height-50" id="govt_drawings_remark" name="govt_drawings_remark"><?php echo $return['govt_drawings_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="text-wrap">
                                        <p class="mb-1">Basis of Estimate -</p>
                                        <p class="mb-1">For schedule items - Preferably to be made from latest CPWD/ DSR /State Govt published rate along with GST if any.</p>
                                        <p class="mb-1">Brought out items- Preferably to be made from Quotations of supplier / rate charts of Manufacturers. Estimated needs to be vetted by Govt. Engineer/ Retd. CPWD/ Retd State Govt. Engineer</p>
                                        <p class="mb-0">Estimate also to be signed & stamped by licensed Engineer & Client</p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Basis Estimate' type="radio" id="basis_estimate_yes" name="basis_estimate" value="1" data-file-id="basis_estimate_file"  <?php echo $return['basis_estimate']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Basis Estimate' type="radio" id="basis_estimate_no" name="basis_estimate" value="0" data-file-id="basis_estimate_file_2"   <?php echo isset($return['basis_estimate'])&&($return['basis_estimate']=='0' || $return['basis_estimate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['basis_estimate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['basis_estimate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Basis Estimate File' type="file" id="basis_estimate_file_3" name="basis_estimate_file" class="form-control d-none"  />
                                        <input type="hidden" name="basis_estimate_file_old"  value="<?php echo $return['basis_estimate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Basis Estimate Remark' class="form-control height-50" id="basis_estimate_remark" name="basis_estimate_remark"><?php echo $return['basis_estimate_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td class="text-wrap">
                                        <p class="mb-1">Stability certificate needs to be attached issued by licensed engineer in case of expansion of existing building.</p>
                                    </td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Stability Certificate' type="radio" id="stability_certificate_yes" name="stability_certificate" value="1" data-file-id="stability_certificate_file"  <?php echo $return['stability_certificate']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                            <input aria-label='Stability Certificate' type="radio" id="stability_certificate_no" name="stability_certificate" value="0" data-file-id="stability_certificate_file_2"   <?php echo isset($return['stability_certificate'])&&($return['stability_certificate']=='0' || $return['stability_certificate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                    <?php if (!empty($return['stability_certificate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['stability_certificate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Stability Certificate File' type="file" id="stability_certificate_file_3" name="stability_certificate_file" class="form-control d-none"  />
                                        <input type="hidden" name="stability_certificate_file_old"  value="<?php echo $return['stability_certificate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Stability Certificate Remark' class="form-control height-50" id="stability_certificate_remark" name="stability_certificate_remark"><?php echo $return['stability_certificate_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">C</th>
                                    <th scope="col">EXECUTION WORKS</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col">1</th>
                                    <td class="text-wrap">
                                        <p class="mb-1">Execution Plan (Mention in briefly, separate annexure can be attached)</p>
                                    </td>
                                    <td>
                                        <div class="form-radio">

                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Execution Works Plan' type="radio" id="execution_works_plan_yes" name="execution_works_plan" value="1" data-file-id="execution_works_plan_file"  <?php echo $return['execution_works_plan']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Execution Works Plan' type="radio" id="execution_works_plan_no" name="execution_works_plan" value="0" data-file-id="execution_works_plan_file_2"   <?php echo isset($return['execution_works_plan'])&&($return['execution_works_plan']=='0' || $return['execution_works_plan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['execution_works_plan_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['execution_works_plan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                    <?php endif; ?>

                                        </div>
                                        <div><input aria-label='Execution Works Plan File' type="file" id="execution_works_plan_file_3" name="execution_works_plan_file" class="form-control d-none"  />
                                        <input type="hidden" name="execution_works_plan_file_old"  value="<?php echo $return['execution_works_plan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea aria-label='Execution Works Plan Remark' class="form-control height-50" id="execution_works_plan_remark" name="execution_works_plan_remark"><?php echo $return['execution_works_plan_remark']??'';  ?></textarea>
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
                                    <th scope="col" class="text-start" colspan="3">Form C</th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center" colspan="3">CHECKLIST FOR SUBMISSION OF PROPOSAL</th>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 5%;">Sr. No</th>
                                    <th scope="col" class="text-center" style="width: 65%;">Points</th>
                                    <th scope="col" class="text-center" style="width: 30%;">Details</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Address of registered office of the Organisation (in India)</td>
                                    <td><textarea aria-label='Address Registered Office' class="form-control height-50" id="address_registered_office" name="address_registered_office"><?php echo $return['address_registered_office']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Any branches of the Organisation? Kindly mention</td>
                                    <td><textarea aria-label='Branches Organisation' class="form-control height-50" id="branches_organisation" name="branches_organisation"><?php echo $return['branches_organisation']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Registered as a trust/society/section 8 company or Other</td>
                                    <td><textarea aria-label='Registered As' class="form-control height-50" id="registered_as" name="registered_as"><?php echo $return['registered_as']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>How long has the Organisation been in existence in years?</td>
                                    <td><textarea aria-label='Years Of Existence' class="form-control height-50" id="years_of_existence" name="years_of_existence"><?php echo $return['years_of_existence']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Which is the Organisation’s geographical area of work?</td>
                                    <td><textarea aria-label='Geographical Area Of Work' class="form-control height-50" id="geographical_area_of_work" name="geographical_area_of_work"><?php echo $return['geographical_area_of_work']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>
                                        <p class="mb-1">What is/are your Organisation's expertise area/s?</p>
                                        <p class="mb-1">a. Skill Development</p>
                                        <p class="mb-1">b. Health & Hygiene</p>
                                        <p class="mb-1">c. Education</p>
                                        <p class="mb-1">d. Community Development</p>
                                        <p class="mb-1">e. Environment Sustainability</p>
                                        <p class="mb-0">f. Other/Multiple (Kindly mention)</p>
                                    </td>
                                    <td><textarea aria-label='Expertise Areas' class="form-control height-50" id="expertise_areas" name="expertise_areas"><?php echo $return['expertise_areas']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Names of Board Members/Trustees and CEO.</td>
                                    <td><textarea aria-label='Board Members' class="form-control height-50" id="board_members" name="board_members"><?php echo $return['board_members']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Are any of the trustees/board members affiliated to any political/religious Organisation?</td>
                                    <td>
                                        <textarea aria-label='Affiliated Political Religious' class="form-control height-50" id="affiliated_political_religious" name="affiliated_political_religious"><?php echo $return['affiliated_political_religious']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>What are the sources of funding of the Organisation?</td>
                                    <td><textarea aria-label='Sources Of Funding' class="form-control height-50" id="sources_of_funding" name="sources_of_funding"><?php echo $return['sources_of_funding']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Has the Organisation received any awards/recognition for its work? Kindly mention</td>
                                    <td><textarea aria-label='Awards Recognition' class="form-control height-50" id="awards_recognition" name="awards_recognition"><?php echo $return['awards_recognition']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>Does it receive funds/implement programs for the government? If yes, details</td>
                                    <td><textarea aria-label='Government Funds Programs' class="form-control height-50" id="government_funds_programs" name="government_funds_programs"><?php echo $return['government_funds_programs']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Has the Organisation been successful in handing over projects/making projects sustainable? Give example</td>
                                    <td><textarea aria-label='Projects Sustainability' class="form-control height-50" id="projects_sustainability" name="projects_sustainability"><?php echo $return['projects_sustainability']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>Has the Organisation been blacklisted by government/PSU or any corporate? If yes, reasons for it</td>
                                    <td><textarea aria-label='Blacklisted Details' class="form-control height-50" id="blacklisted_details" name="blacklisted_details"><?php echo $return['blacklisted_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td>Is the Organisation empanelled by any reputed institute or agency? If yes, give details</td>
                                    <td><textarea aria-label='Empanelled Details' class="form-control height-50" id="empanelled_details" name="empanelled_details"><?php echo $return['empanelled_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td>Is the Organisation associated with any other corporate for implementing CSR projects? Mention names</td>
                                    <td><textarea aria-label='Associated Csr Projects' class="form-control height-50" id="associated_csr_projects" name="associated_csr_projects"><?php echo $return['associated_csr_projects']??'';  ?></textarea></td>
                                </tr>

                                <tr>
                                    <td>16</td>
                                    <td>Strength of staff (number of full-time employees)</td>
                                    <td><textarea aria-label='Staff Strength' class="form-control height-50" id="staff_strength" name="staff_strength"><?php echo $return['staff_strength']??'';  ?></textarea></td>
                                </tr>


                                <tr>
                                    <td>17</td>
                                    <td>Please provide two references of past funders of your projects (Email id/Contact Person with Designation/Contact Number)</td>
                                    <td><textarea aria-label='References Past Funders' class="form-control height-50" id="references_past_funders" name="references_past_funders"><?php echo $return['references_past_funders']??'';  ?></textarea></td>
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
                                    <th scope="col" class="text-start" colspan="3" style="width: 100%;">Form D</th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="1" style="width: 5%;">Sr. No</th>
                                    <th scope="col" class="text-center" colspan="2" style="width: 95%;">Summary Sheet for the Proposal</th>
                                </tr>
                                <tr>
                                    <td >1</td>
                                    <td >Title of the project</td>
                                    <td ><input aria-label='Title Of Project' class="form-control" id="title_of_project" name="title_of_project"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Project Description</td>
                                    <td>
                                        <textarea aria-label='(Write a brief description of project)' class="form-control" id="formd_project_description" name="formd_project_description" placeholder="(Write a brief description of project)"><?php echo $return['formd_project_description']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>3</td>

                                    <td>Name of Executing Organisation</td>
                                    <td>
                                        <textarea aria-label='Executing Organisation' class="form-control" id="executing_organisation" name="executing_organisation"><?php echo $return['executing_organisation']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>4</td>

                                    <td>Address of Organisation (Registered address & address of branch from which proposal is given)</td>
                                    <td>
                                        <textarea aria-label='Organisation Address' class="form-control" id="organisation_address" name="organisation_address"><?php echo $return['organisation_address']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>5</td>

                                    <td>Contact Person</td>
                                    <td>
                                        <textarea aria-label='Contact Person' class="form-control" id="contact_person" name="contact_person"><?php echo $return['contact_person']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>6</td>

                                    <td>Contact Number & email address</td>
                                    <td>
                                        <textarea aria-label='Contact Details' class="form-control" id="contact_details" name="contact_details"><?php echo $return['contact_details']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>7</td>

                                    <td>Experience of the Organisation</td>
                                    <td>
                                        <textarea aria-label='(Mention no. of years of experience &amp; short brief about other projects executed)' class="form-control" id="organisation_experience" name="organisation_experience" placeholder="(Mention no. of years of experience & short brief about other projects executed)"><?php echo $return['organisation_experience']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>8</td>

                                    <td>Place of execution of project</td>
                                    <td>
                                        <textarea aria-label='Execution Place' class="form-control" id="execution_place" name="execution_place"><?php echo $return['execution_place']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>9</td>

                                    <td>Reason for choice of place</td>
                                    <td>
                                        <textarea aria-label='Place Reason' class="form-control" id="place_reason" name="place_reason"><?php echo $return['place_reason']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>10</td>

                                    <td>Tenure of the project</td>
                                    <td>
                                        <textarea aria-label='Project Tenure' class="form-control" id="project_tenure" name="project_tenure"><?php echo $return['project_tenure']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>11</td>

                                    <td>Cost of the project (enclose a detailed budget)</td>
                                    <td>
                                        <textarea aria-label='Project Cost' class="form-control" id="project_cost" name="project_cost"><?php echo $return['project_cost']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>12</td>

                                    <td>Funds requested from Infisuite</td>
                                    <td>
                                        <textarea aria-label='Funds Infisuite' class="form-control" id="funds_infisuite" name="funds_infisuite"><?php echo $return['funds_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>13</td>

                                    <td>Funds requested from any other sources if any</td>
                                    <td>
                                        <textarea aria-label='Funds Other Sources' class="form-control" id="funds_other_sources" name="funds_other_sources"><?php echo $return['funds_other_sources']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>14</td>

                                    <td>Objective of the project</td>
                                    <td>
                                        <textarea aria-label='Project Objective' class="form-control" id="project_objective" name="project_objective"><?php echo $return['project_objective']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>15</td>

                                    <td>Reason of Organisation preferring Infisuite to be part of this project</td>
                                    <td>
                                        <textarea aria-label='Reason Infisuite' class="form-control" id="reason_infisuite" name="reason_infisuite" ><?php echo $return['reason_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>16</td>

                                    <td>Benefits/Outcomes expected from the project</td>
                                    <td>
                                        <textarea aria-label='Project Benefits' class="form-control" id="project_benefits" name="project_benefits"><?php echo $return['project_benefits']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr><td>17</td>

                                    <td>Quantitative (Mention as per parameter suitable, but make sure to mention number of beneficiaries)</td>
                                    <td>
                                        <input aria-label='1.' class="form-control" type="text" id="quantitative_1" name="quantitative_1" placeholder="1." value="<?php echo $return['quantitative_1']??'';  ?>" />
                                        <input aria-label='2.' class="form-control mt-1" type="text" id="quantitative_2" name="quantitative_2" placeholder="2." value="<?php echo $return['quantitative_2']??'';  ?>" />
                                        <input aria-label='3.' class="form-control mt-1" type="text" id="quantitative_3" name="quantitative_3" placeholder="3." value="<?php echo $return['quantitative_3']??'';  ?>" />
                                        <input aria-label='4.' class="form-control mt-1" type="text" id="quantitative_4" name="quantitative_4" placeholder="4." value="<?php echo $return['quantitative_4']??'';  ?>" />
                                        <input aria-label='5.' class="form-control mt-1" type="text" id="quantitative_5" name="quantitative_5" placeholder="5." value="<?php echo $return['quantitative_5']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>18</td>
                                    <td>Qualitative (parameters which are difficult to capture in numbers)</td>
                                    <td>
                                        <input aria-label='1.' class="form-control" type="text" id="qualitative_1" name="qualitative_1" placeholder="1." value="<?php echo $return['qualitative_1']??'';  ?>"/>
                                        <input aria-label='2.' class="form-control mt-1" type="text" id="qualitative_2" name="qualitative_2" placeholder="2." value="<?php echo $return['qualitative_2']??'';  ?>" />
                                        <input aria-label='3.' class="form-control mt-1" type="text" id="qualitative_3" name="qualitative_3" placeholder="3." value="<?php echo $return['qualitative_3']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>19</td>
                                    <td>Requirement of any approval from local bodies/government/municipality or any statutory body</td>
                                    <td>
                                        <input aria-label='Yes or No, If yes, give details in short' class="form-control" type="text" id="approval_requirement" name="approval_requirement" placeholder="Yes or No, If yes, give details in short" value="<?php echo $return['approval_requirement']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>20</td>
                                    <td>Whether similar projects executed earlier?</td>
                                    <td>
                                        <input aria-label='Yes or No, If yes, give details in short' class="form-control" type="text" id="similar_projects" name="similar_projects" placeholder="Yes or No, If yes, give details in short"  value="<?php echo $return['similar_projects']??'';  ?>"/>
                                    </td>
                                </tr>
                                <tr><td>21</td>
                                    <td>In case the proposal is approved, the organisation is okay to open separate bank account for Infisuite? (Yes/No)</td>
                                    <td>
                                        <input aria-label='Bank Account Infisuite' class="form-control" type="text" id="bank_account_infisuite" name="bank_account_infisuite" value="<?php echo $return['bank_account_infisuite']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr><td>22</td>
                                    <td>Information given above is true and we can provide the documentary proof of it in case of enquiry</td>
                                    <td>
                                    <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Information True' type="radio" id="information_true_yes" data-file-id="information_true_file" name="information_true" value="1"  <?php echo $return['information_true']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input aria-label='Information True' type="radio" id="information_true_no" data-file-id="information_true_file_2" name="information_true" value="0"  <?php echo isset($return['information_true'])&&($return['information_true']=='0' || $return['information_true'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                            <?php if (!empty($return['information_true_file'])): ?>
                                                <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['information_true_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-18 f-w-600"></i></a>
                                            <?php endif; ?>
                                        </div>
                                        <div><input aria-label='Information True File' type="file" id="information_true_file_3" name="information_true_file" class="form-control d-none"  />
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
                                        <th scope="col" class="text-start" colspan="8">Form E</th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="text-start" colspan="8">
                                            <h3 class="f-18 f-w-600 mt-4 text-center text-underline">Track Record of Projects</h3>
                                            <p class="text-center f-14">(Kindly mention projects undertaken in the last 3 years.<br />If an organisation is existing for 3 years, track record from inception till date is required)</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Sr. No.</th>
                                        <th scope="col">Project Description</th>
                                        <th scope="col">Focus Area</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Period (To - From)</th>
                                        <th scope="col">Value in INR Lakhs</th>
                                        <th scope="col">Number of Beneficiaries</th>
                                        <th scope="col">Partner Name (Funding Agency)</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                            <?php if (!empty($return['project_data'])): ?>
            <?php
            $project_data = json_decode($return['project_data'], true);
            $srNo = 1;
            foreach ($project_data as $project):
            ?>
                <tr>
                    <td><?= $srNo++; ?></td>
                    <td><input aria-label='Input field' type="text" class="form-control" id="project_description_<?= $srNo; ?>" name="project_description[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['project_description'] ?? '-') ?>"></td>
                    <td><input aria-label='Input field' type="text" class="form-control" id="focus_area_<?= $srNo; ?>" name="focus_area[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['focus_area'] ?? '-') ?>"></td>
                    <td><input aria-label='Input field' type="text" class="form-control" id="location_<?= $srNo; ?>" name="location[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['location'] ?? '-') ?>"></td>
                    <td><input aria-label='Input field' type="text" class="form-control" id="period_<?= $srNo; ?>" name="period[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['period'] ?? '-') ?>"></td>
                    <td><input aria-label='Enter number' type="number" class="form-control text_align_right" id="value_<?= $srNo; ?>" name="value[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['value'] ?? '-') ?>"></td>
                    <td><input aria-label='Enter number' type="number" class="form-control text_align_right" id="beneficiaries_<?= $srNo; ?>" name="beneficiaries[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['beneficiaries'] ?? '-') ?>"></td>
                    <td><input aria-label='Input field' type="text" class="form-control" id="partner_name_<?= $srNo; ?>" name="partner_name[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['partner_name'] ?? '-') ?>"></td>
                    <td class="text-center">
                        <i class='fas fa-trash-alt text-danger cursor-pointer remove-row delete-form-e-row' ></i>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="add_form_e_row">Add More</button>
                        </div>
                    </div>
                </div>

                <div id="form_f" >
                <div class="mt-3 enclosures">

                    <h3 class="f-18 f-w-600 mt-4 text-center text-underline mb-4">Declaration</h3>
                    <p class="f-14">I / We hereby declare and certify that all the information given in the application form and in the documents submitted are true and correct to the best of my knowledge.</p>
                    <p class="f-14">I / We acknowledge that in case of misrepresentation of information or furnishing of false information, my application is liable to be rejected and /or approved project is be liable to be terminated by BPCL at any stage.</p>
                    <p class="f-14">I / We further declare and certify that our organisation / company / group of companies has / have not been blacklisted or been put on the holiday list by any of the Public Sector Undertakings in India or Statutory authority or Government.</p>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Signature</span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='Signature...' type="text" class="form-control" id="signature" name="signature" placeholder="Signature..." value="<?php echo $return['signature']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Name</span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='Name...' type="text" class="form-control" id="name" name="name" placeholder="Name..." value="<?php echo $return['name']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Designation</span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='Designation...' type="text" class="form-control" id="designation" name="designation" placeholder="Designation..." value="<?php echo $return['designation']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Seal of the Organisation</span>
                        </div>
                        <div class="col-lg-3">
                            <input aria-label='Seal of the Organisation...' type="file" class="form-control" id="organisation_seal" name="organisation_seal" placeholder="Seal of the Organisation...">
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
                        <button type="button" class="btn btn-primary waves-effect waves-light m-r-30 mt-3 previous_open">Previous</button>
                    </div>
                    <div class="col-lg-6 mt-5">
                        <button class="btn btn-primary mt-3 float-end" type="submit">Save</button>
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
