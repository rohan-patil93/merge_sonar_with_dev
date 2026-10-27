<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// $rid = '20';
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'csr_proposal_data';
    $url = API . "?mode=singlerowtabledata&method=get&rid=" . $rid . "&table=" . $table_name;
    $che = callAPI($url);
    $return = json_decode($che, 1);
 ?>
<div class="card mt-4">
    <div class="card-header general_instructions_3" style="display: none;">
        <h5>CSR</h5>
    </div>
    <div class="card-body">

        <?php 
        $url = API . "?mode=getcsrsetting&method=get";
        $response = callAPI($url);
        $response = json_decode($response, 1);
        ?>
        <div class="general_instructions">
            <div class="card-header">
                <h5>General Instructions</h5>
            </div>
            <?php
            $description = $response['description'];
            $pattern = '/(\d+\)\s)/'; // Matches the number with closing parenthesis and space
            $list_items = preg_split($pattern, $description, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            
            // Display the list
            echo "<ul>";
            for ($i = 0; $i < count($list_items); $i += 2) {
                // Combine the number and the corresponding text
                $number = isset($list_items[$i]) ? $list_items[$i] : '';
                $text = isset($list_items[$i + 1]) ? $list_items[$i + 1] : '';
                echo "<li>" . trim($number . $text) . "</li>";
            }
            echo "</ul>";
            ?>
            <div class="row">
                <div class="col-lg-12 text-end">
                    <button class="btn btn-primary waves-effect waves-light  m-r-30 mt-3 general_step_1">Next</button>
                </div>
            </div>
        </div>
        
        <div class="general_instructions_2 m-l-40 mt-4" id="printableArea" style="display: none;">


            <form id="myForm" action="" method="post">
                <div class="row">
                    <div class="col-lg-1">
                        <p class="f-w-600">Date: -</p>
                    </div>
                    <div class="col-lg-6">
                        <!-- <p class="f-w-600">19/11/2024</p> -->
        <input name="date" class="form-control" type="date" id="date" value="<?php echo isset($return['date']) ? $return['date'] : date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">To: -</p>
                    </div>
                    <div class="col-lg-6">
                        <!-- <p class="f-w-600">Infisuite Technologies</p> -->
                        <input name="institue_name" class="form-control" type="text" id="institue_name" value="<?php echo htmlspecialchars($return['institue_name']) ?? ''; ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">Start Date:</p>
                    </div>
                    <div class="col-lg-6">

                        <input name="project_start_date" class="form-control" type="date" id="project_start_date" value="<?php echo htmlspecialchars($return['project_start_date']) ?? ''; ?>">
                    </div>
                </div><div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">End Date:</p>
                    </div>
                    <div class="col-lg-6">
                        <input name="project_end_date" class="form-control" type="date" id="project_end_date" value="<?php echo htmlspecialchars($return['project_end_date']) ?? ''; ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-1">
                        <p class="f-w-600">CSR Name</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <textarea name="subject" id="subject" class="form-control" placeholder="Proposal for..." style="height:80px;"><?php echo htmlspecialchars($return['subject']) ?? ''; ?></textarea><label class="f-12">(Subject for CSR).</label>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">1.0 About the Organisation</h3>
                    <div class="form-group col-lg-6">

                        <textarea name="about_organisation" id="about_organisation" class="form-control" placeholder="" style="height:80px;"><?php echo htmlspecialchars($return['about_organisation']) ?? ''; ?></textarea>
                        <label class="f-12">(Organisational information- Historical overview/ past experience/ type of projects executed/ reach and presence within country & abroad/ staff strength/ success stories/accolades/awards received if any. Also give website link if available).</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">2.0 Background</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="background" id="background" class="form-control" placeholder="" style="height:80px;"><?php echo htmlspecialchars($return['background']) ?? ''; ?></textarea>
                        <label class="f-12">(Give details of project background elaborating on the need of proposed project. Kindly mention details of need assessment/appraisal carried out)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600">2.1 Thematic Area</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="thematic_area" id="thematic_area" class="form-control" placeholder="" style="height:80px;"><?php echo htmlspecialchars($return['thematic_area']) ?? ''; ?></textarea>
                        <label class="f-12">(Mention the thrust area/s, Schedule VII item and Sustainable Development Goal (SDG) under which the proposed project would be categorised)</label>
                    </div>
                    <div class="col-lg-10">
                        <table class="table table-bordered">
                            <tr>
                                <th>Thrust Area</th>
                                <th>Schedule VII item</th>
                            </tr>
                            <tr>
                                <td>Education</td>
                                <td>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                <input name="education" id="education" class="border-checkbox" type="checkbox" value="0" <?php echo isset($return['education'])?'checked':''; ?>
                                            onchange="updateCheckboxValue(this)">
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
                                            <input name="env_sustian" class="border-checkbox" type="checkbox" id="env_sustian" value="0" <?php echo isset($return['env_sustian'])?'checked':''; ?>
                                            onchange="updateCheckboxValue(this)">
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
                                            <input name="health" class="border-checkbox" type="checkbox" id="health" value="0" <?php echo isset($return['health'])?'checked':''; ?>
                                            onchange="updateCheckboxValue(this)">
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
                                            <input name="skill_development" class="border-checkbox" type="checkbox" id="skill_development" value="0" <?php echo isset($return['skill_development'])?'checked':''; ?>
                                            onchange="updateCheckboxValue(this)">
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
                                            <input name="comu_development" class="border-checkbox" type="checkbox" id="comu_development" value="0" <?php echo isset($return['comu_development'])?'checked':''; ?>
                                            onchange="updateCheckboxValue(this)">
                                            <label class="form-label border-checkbox-label" for="comu_development">(x) Rural development projects/ Multiple categories</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Others or More than one of the above (Please Specify)</td>
                                <td>
                                    <textarea name="other_thrust_area" id="other_thrust_area" class="form-control" placeholder="" style="height:60px;"><?php echo isset($return['other_thrust_area'])?$return['other_thrust_area']:''; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-22 f-w-600">Please specify the SDG/s in addition to the above.</h3>
                    <h3 class="f-18 f-w-600 mt-3">3.0 Proposal</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="SDGproposal" id="SDGproposal" class="form-control" placeholder="" style="height:80px;"><?php echo $return['SDGproposal'] ??''; ?></textarea>
                        <label class="f-12">(Mention the proposal objectives. Explain the activities planned in order to achieve the specific objective/s along with the time frame)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.1 Methodology</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="methodology" id="methodology" class="form-control" placeholder="" style="height:80px;"><?php echo $return['methodology'] ??''; ?></textarea>
                        <label class="f-12">(Define the methodology for implementation of the program such as implementation schedule, execution plan, selection criteria for specific beneficiaries etc.)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.2 Target Group</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="target_group" id="target_group" class="form-control" placeholder="" style="height:80px;"><?php echo $return['target_group'] ??''; ?></textarea>
                        <label class="f-12">(Mention Geographical area/location of the project and the profile of the beneficiaries - details of Male/Female, minority or vulnerable group/others etc)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.3 Team for the project</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="team_project" id="team_project" class="form-control" placeholder="" style="height:80px;"><?php echo $return['team_project'] ??''; ?></textarea>
                        <label class="f-12">(Mention job profile & eligibility criteria for selection of staff/team members for the project i.e. qualifications & experience, Roles & responsibilities, Organization structure etc.)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">3.4 Monitoring Mechanism</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="monitoring_mechanism" id="monitoring_mechanism" class="form-control" placeholder="" style="height:80px;"><?php echo $return['monitoring_mechanism'] ??''; ?></textarea>
                        <label class="f-12">(Mention the monitoring mechanism such as frequency of visits, plan for collection of data, monitoring of activity timeline & frequency of submission of reports)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.1 Outcomes/Result expected</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="outcomes_expected" id="outcomes_expected" class="form-control" placeholder="" style="height:80px;"><?php echo $return['outcomes_expected'] ??''; ?></textarea>
                        <label class="f-12">(Mention the Quantitative & Qualitative outcomes expected from the project, outcomes should be directly linked to proposed objectives; specific targets should be benchmarked. It is recommended to use frameworks such as project matrix, verifiable indicators, data analysis.)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.2 Execution Plan</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="execution_plan" id="execution_plan" class="form-control" placeholder="" style="height:80px;"><?php echo $return['execution_plan'] ??''; ?></textarea>
                        <label class="f-12">(Gantt chart with time line against activities may be given in tabular/other form)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">4.3 Impact Assessment</h3>
                    <div class="form-group col-lg-6">
                        <textarea name="impact_assessment" id="impact_assessment" class="form-control" placeholder="" style="height:80px;"><?php echo $return['impact_assessment'] ??''; ?></textarea>
                        <label class="f-12">(Mention the plan to measure the impact of planned interventions of this project as defined in the scope of work)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">5.1 Financial Implications</h3>
                    <div class="form-group col-lg-6">
                        <textarea class="form-control" id="financial_implications" name="financial_implications" placeholder="" style="height:80px;"><?php echo $return['financial_implications'] ??''; ?></textarea>
                        <label class="f-12">(Format given below is indicative. Based on the nature of the project, kindly use appropriate format)</label>
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
                                    <th>Sr. No</th>
                                    <th class="text-center">Details</th>
                                    <th class="text-center">Per unit Cost<br />(A)</th>
                                    <th class="text-center">Total Number of Units<br />(B)</th>
                                    <th class="text-center">Total Budget for 1 month/year<br />(C=A*B)</th>
                                    <th class="text-center">No. of months/years</th>
                                    <th class="text-center">Budget For Months/Years<br />(D=C*No. of months/years)</th>
                                    <th class="text-center">Remarks<br />(Justification of cost element)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
// Check if there is existing data
if (!empty($return['groups_data'])) {
    $groupsData = json_decode($return['groups_data'], true); 
} else {
    $groupsData = []; 
}


// Loop through the groups if data exists
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
            echo '<td><input type="text" class="form-control" id="details_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][details][' . ($index ) . ']" value="' . htmlspecialchars($detail) . '"></td>';
            echo '<td><input type="number" class="form-control unit-cost text_align_right" id="unit_cost_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][unit_cost][' . ($index ) . ']" value="' . htmlspecialchars($unitCost[$index] ?? '') . '" oninput="calculateRowBudget(\'group_' . htmlspecialchars($groupId) . '\')"></td>';
            echo '<td><input type="number" class="form-control num-units text_align_right" id="num_units_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][num_units][' . ($index) . ']" value="' . htmlspecialchars($numUnits[$index] ?? '') . '" oninput="calculateRowBudget(\'group_' . htmlspecialchars($groupId) . '\')"></td>';
            echo '<td><input type="text" class="form-control budget text_align_right" id="budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][budget][' . ($index) . ']" value="' . htmlspecialchars($budget[$index] ?? '') . '" readonly></td>';
            echo '<td><input type="number" class="form-control num-months text_align_right" id="num_months_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][num_months][' . ($index) . ']" value="' . htmlspecialchars($numMonths[$index] ?? '') . '" oninput="calculateRowBudget(\'group_' . htmlspecialchars($groupId) . '\')"></td>';
            echo '<td><input type="text" class="form-control total-budget text_align_right" id="total_budget_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][total_budget][' . ($index) . ']" value="' . htmlspecialchars($totalBudget[$index] ?? '') . '" readonly></td>';
            echo '<td><input type="text" class="form-control" id="remarks_group_' . htmlspecialchars($groupId) . '_' . ($index) . '" name="groups[' . htmlspecialchars($groupId) . '][remarks][' . ($index) . ']" value="' . htmlspecialchars($remarks[$index] ?? '') . '"></td>';
            echo '<td ></td>';
            echo '</tr>';
        }

            // Subtotal Row
            echo '<tr data-group="group_' . htmlspecialchars($groupId) . '" class="subtotal-row" bgcolor="#eeeeee">';
            echo '<td></td>';
            echo '<th class="text-center">Sub Total (' . htmlspecialchars($groupId) . ')</th>';
            echo '<td></td><td></td>';
            echo '<td><input type="text" class="form-control budget-subtotal text_align_right" id="budget_sub_total_' . htmlspecialchars($groupId) . '" name="groups[' . htmlspecialchars($groupId) . '][subtotal][' . htmlspecialchars($groupId) . ']" value="' . htmlspecialchars($subtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td><input type="text" class="form-control total-budget-subtotal text_align_right" id="total_budget_sub_total_' . htmlspecialchars($groupId) . '" name="groups[' . htmlspecialchars($groupId) . '][total_subtotal][' . htmlspecialchars($groupId) . ']" value="' . htmlspecialchars($totalSubtotal[$groupId]) . '" readonly></td>';
            echo '<td></td>';
            echo '<td class="text-center"> <i class="fas fa-trash-alt text-danger cursor-pointer remove-row" onclick="deleteGroup(\'group_' . htmlspecialchars($groupId) . '\')"></i></td>';
            echo '</tr>';
    }
}

?>

                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-primary waves-effect waves-light" type="button" id="add_row" onclick="addRows()">Add more</button>
                                    </td>
                                </tr>
                                <tr bgcolor="#eeeeee">
                                    <td></td>
                                    <th class="text-center"><strong>Grand Total</strong></th>
                                    <td></td>
                                    <td></td>
                                    <td><input type="text" class="form-control text_align_right" id="budget_grand_total" name="budget_grand_total" value="<?php echo $return['budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td><input type="text" class="form-control text_align_right" id="total_budget_grand_total" name="total_budget_grand_total" value="<?php echo $return['total_budget_grand_total'] ??''; ?>" readonly></td>
                                    <td></td>
                                    <td></td>
                                </tr>


                            </tfoot>
                        </table>
                    </div>
                </div>
                <script type="text/javascript">
                    let subtotalCount = <?php echo count($groupsData) ?? 0; ?>;
                    // Function to add new rows (empty rows for user input)
function addRows(groupId = `group_${++subtotalCount}`) {
    const tableBody = document.querySelector('#financial_table tbody');
    let rows = '';

    // Add 3 empty rows
    for (let i = 1; i <= 3; i++) {
        rows += `
        <tr data-group="${groupId}">
            <td>${i}</td>
            <td><input type="text" class="form-control" id="details_${groupId}_${i}" name="groups[${subtotalCount}][details][${i}]"></td>
            <td><input type="number" class="form-control unit-cost text_align_right" id="unit_cost_${groupId}_${i}" name="groups[${subtotalCount}][unit_cost][${i}]" oninput="calculateRowBudget('${groupId}')"></td>
            <td><input type="number" class="form-control num-units text_align_right" id="num_units_${groupId}_${i}" name="groups[${subtotalCount}][num_units][${i}]" oninput="calculateRowBudget('${groupId}')"></td>
            <td><input type="text" class="form-control budget text_align_right" id="budget_${groupId}_${i}" name="groups[${subtotalCount}][budget][${i}]" readonly></td>
            <td><input type="number" class="form-control num-months text_align_right" id="num_months_${groupId}_${i}" name="groups[${subtotalCount}][num_months][${i}]" oninput="calculateRowBudget('${groupId}')"></td>
            <td><input type="text" class="form-control total-budget text_align_right" id="total_budget_${groupId}_${i}" name="groups[${subtotalCount}][total_budget][${i}]" readonly></td>
            <td><input type="text" class="form-control" id="remarks_${groupId}_${i}" name="groups[${subtotalCount}][remarks][${i}]"></td>
            <td class="text-center"></td>
        </tr>`;
    }

    // Add subtotal row
    rows += `
    <tr data-group="${groupId}" class="subtotal-row" bgcolor="#eeeeee">
        <td></td>
        <th class="text-center">Sub Total (${subtotalCount})</th>
        <td></td>
        <td></td>
        <td><input type="text" class="form-control budget-subtotal text_align_right" id="budget_sub_total_${subtotalCount}" name="groups[${subtotalCount}][subtotal][${subtotalCount}]" readonly></td>
        <td></td>
        <td><input type="text" class="form-control total-budget-subtotal text_align_right" id="total_budget_sub_total_${subtotalCount}" name="groups[${subtotalCount}][total_subtotal][${subtotalCount}]" readonly></td>
        <td></td>
        <td class="text-center"><i class="fas fa-trash-alt text-danger cursor-pointer remove-row" onclick="deleteGroup('${groupId}')"></i></td>
    </tr>`;

    tableBody.insertAdjacentHTML('beforeend', rows);
}

// Function to calculate row budgets
function calculateRowBudget(groupId) {
    const groupRows = document.querySelectorAll(`[data-group="${groupId}"]:not(.subtotal-row)`);
    let budgetSum = 0;
    let totalBudgetSum = 0;

    groupRows.forEach(row => {
        const unitCost = parseFloat(row.querySelector('.unit-cost')?.value) || 0;
        const numUnits = parseFloat(row.querySelector('.num-units')?.value) || 0;
        const numMonths = parseFloat(row.querySelector('.num-months')?.value) || 0;

        const budget = unitCost * numUnits;
        const totalBudget = budget * numMonths;

        row.querySelector('.budget').value = budget.toFixed(2);
        row.querySelector('.total-budget').value = totalBudget.toFixed(2);

        budgetSum += budget;
        totalBudgetSum += totalBudget;
    });

    const subtotalRow = document.querySelector(`[data-group="${groupId}"].subtotal-row`);
    subtotalRow.querySelector('.budget-subtotal').value = budgetSum.toFixed(2);
    subtotalRow.querySelector('.total-budget-subtotal').value = totalBudgetSum.toFixed(2);

    calculateGrandTotal();
}

// Function to calculate grand totals
function calculateGrandTotal() {
    const subtotalRows = document.querySelectorAll('.subtotal-row');
    let budgetGrandTotal = 0;
    let totalBudgetGrandTotal = 0;

    subtotalRows.forEach(row => {
        budgetGrandTotal += parseFloat(row.querySelector('.budget-subtotal')?.value) || 0;
        totalBudgetGrandTotal += parseFloat(row.querySelector('.total-budget-subtotal')?.value) || 0;
    });

    document.getElementById('budget_grand_total').value = budgetGrandTotal.toFixed(2);
    document.getElementById('total_budget_grand_total').value = totalBudgetGrandTotal.toFixed(2);
}

// Function to delete a group of rows
function deleteGroup(groupId) {
    const groupRows = document.querySelectorAll(`[data-group="${groupId}"]`);
    groupRows.forEach(row => row.remove());
    calculateGrandTotal();
}
                        </script>
                        <div class="mt-3">
                            <h3 class="f-18 f-w-600 mt-3">5.2 Tentative payment schedule</h3>
                            <div class="form-group col-lg-6">
                                <textarea class="form-control" id="payment_schedule" name="payment_schedule" placeholder="" style="height:80px;"><?php echo $return['payment_schedule'] ??''; ?></textarea>
                                <label class="f-12">(Link payment schedule with project deliverables and timeline. It will be reviewed by Infisuite and the final payment schedule might differ significantly as per nature of the project & Infisuite guidelines. For final payment, 10% of the total budget will be kept on hold until the submission of the project completion report and audited fund utilisation certificate)</label>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentScheduleTable">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Description</th>
                                            <th>% of Total Project Budget</th>
                                            <th>Amount in Rs</th>
                                            <th>Deliverables to be achieved</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<!--        -->
                                        <?php

    $paymentScheduleData = json_decode($return['tentative_payment_schedule'], true);

        // Extract the individual arrays from $paymentScheduleData
    $description = $paymentScheduleData['payment'] ?? $paymentScheduleData['description'] ?? [];

    $percent = $paymentScheduleData['percent'] ?? [];
    $amount = $paymentScheduleData['amount'] ?? [];
    $deliverables = $paymentScheduleData['deliverables'] ?? [];

if (!empty($percent)) {
    foreach ($percent as $payId => $percentValue) {
        echo '<tr>';
        echo '<td>' . ($payId) . '</td>';
        echo '<td><input type="text" class="form-control" id="payment_schedule_description_' . ($payId) . '" name="payment_schedule_description[' . ($payId ) . ']" value="' . htmlspecialchars($description[$payId] ?? '') . '"></td>';
        echo '<td><input type="number" class="form-control percent-input text_align_right" id="percent_' . ($payId) . '" name="payment_schedule_percent[' . ($payId) . ']" value="' . htmlspecialchars($percentValue) . '" oninput="calculateAmount(' . ($payId) . ')"></td>';
        echo '<td><input type="text" class="form-control amount-input text_align_right" id="amount_' . ($payId + 1) . '" name="payment_schedule_amount[' . ($payId) . ']" value="' . htmlspecialchars($amount[$payId] ?? '') . '" readonly></td>';
        echo '<td><textarea class="form-control" id="deliverables_' . ($payId) . '" name="payment_schedule_deliverables[' . ($payId) . ']">' . htmlspecialchars($deliverables[$payId] ?? '') . '</textarea></td>';
        echo '<td class="text-center" ><i class="fas fa-trash-alt text-danger cursor-pointer remove-row" onclick="deletePaymentScheduleRow(this)"></i></td>';
        echo '</tr>';
    }
}
?>


                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button class="btn btn-primary waves-effect waves-light" type="button" onclick="addPaymentScheduleRows()">Add more</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Grand Total</th>
                                            <th></th>
                                            <th><input type="text" class="form-control text_align_right" id="payment_grand_total" name="payment_grand_total" value="<?php echo $return['payment_grand_total'] ??''; ?>" readonly></th>
                                            <th colspan="2"><input type="text" class="form-control" id="payment_grand_total_in_words" name="payment_grand_total_in_words" placeholder="In Words Only..." value="<?php echo $return['payment_grand_total_in_words'] ??''; ?>"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <script>
                    let rowCount = document.querySelectorAll("#paymentScheduleTable tbody tr").length;

                    function addPaymentScheduleRows() {
                        rowCount++;
                        const tableBody = document.querySelector("#paymentScheduleTable tbody");
                        const newRow = document.createElement("tr");

                        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" class="form-control" id="payment_schedule_description_${rowCount}" name="payment_schedule_description[${rowCount}]"></td>
            <td><input type="number"  class="form-control percent-input text_align_right" id="percent_${rowCount}" name="payment_schedule_percent[${rowCount}]"  oninput="calculateAmount(${rowCount})"></td>
            <td><input type="text" class="form-control amount-input text_align_right" id="amount_${rowCount}" name="payment_schedule_amount[${rowCount}]" readonly></td>
            <td><textarea  class="form-control" id="deliverables_${rowCount}" name="payment_schedule_deliverables[${rowCount}]"></textarea></td>
            <td class="text-center">
            <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deletePaymentScheduleRow(this)'></i>
            
            </td>
                        `;

                        tableBody.appendChild(newRow);
                        updateRows();
                    }

                    function deletePaymentScheduleRow(button) {
                        const row = button.closest("tr");
                        row.remove();
                        updateRows();
                        calculatePaymentScheduleGrandTotal()
                    }

                    function updateRows() {
                        const rows = document.querySelectorAll("#paymentScheduleTable tbody tr");
                        rowCount = rows.length;

                        rows.forEach((row, index) => {
                            const currentIndex = index + 1; // Start numbering from 1
                            row.children[0].textContent = currentIndex; // Update Sr.No

                            row.querySelectorAll("input").forEach(input => {
                                const baseName = input.getAttribute("id").split("_")[0];
                                input.id = `${baseName}_${currentIndex}`;
                                input.name = `payment_schedule_${baseName}[${currentIndex}]`;
                            });
                        });
                    }

                    function calculateAmount(rowIndex) {
                        const percentInput = document.getElementById(`percent_${rowIndex}`);
                        const amountInput = document.getElementById(`amount_${rowIndex}`);
                        const totalBudget = parseFloat(document.getElementById("total_budget_grand_total").value) || 0;

                        // Ensure percentage value is formatted to two decimal places
                        let percentValue = parseFloat(percentInput.value) || 0;
                        percentValue = Math.round(percentValue * 100) / 100;
                        percentInput.value = percentValue;

                        // Calculate the corresponding amount
                        const calculatedAmount = (percentValue / 100) * totalBudget;
                        amountInput.value = calculatedAmount.toFixed(2);

                        // Recalculate the grand total
                        calculatePaymentScheduleGrandTotal();
                    }

                    function calculatePaymentScheduleGrandTotal() {
                        const amountInputs = document.querySelectorAll(".amount-input");
                        let totalAmount = 0;

                        // Calculate total sum of all amount inputs
                        amountInputs.forEach(input => {
                            const value = parseFloat(input.value) || 0;
                            totalAmount += value;
                        });

                        // Update Grand Total field
                        const grandTotalInput = document.getElementById("payment_grand_total");
                        grandTotalInput.value = totalAmount;

                        // Check against total budget
                        const totalBudget = parseFloat(document.getElementById("total_budget_grand_total").value) || 0;

                        if (totalAmount === totalBudget) {
                            grandTotalInput.style.color = "black";
                        } else {
                            grandTotalInput.style.color = "red";
                        }
                    }
                </script>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">6.0 Sustainability Plan</h3>
                    <div class="form-group col-lg-6">
                        <textarea class="form-control" id="sustainability_plan" name="sustainability_plan" placeholder="" style="height:80px;"><?php echo $return['sustainability_plan'] ??''; ?></textarea>
                        <label class="f-12">(Provide an action plan giving details of how the project will be sustainable after exit of Infisuite & the implementing partner. This must include maintenance and ownership of assets where applicable)</label>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="f-18 f-w-600 mt-3">7.0 Conclusion</h3>
                    <div class="form-group col-lg-6">
                        <textarea class="form-control" id="conclusion" name="conclusion" placeholder="" style="height:80px;"><?php echo $return['conclusion'] ??''; ?></textarea>
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
                            <input class="border-checkbox" type="radio" id="form_a_yes" name="form_a" value="1" onchange="validateFile('form_a')" <?php echo $return['form_a']=='1'?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_a_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                <input class="border-checkbox" type="radio" id="form_a_no" name="form_a" value="0" onchange="validateFile('form_a')" <?php echo isset($return['form_a'])&&($return['form_a']=='0' || $return['form_a'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_a_no">No</label>
                                    </div>
                                    
                                    <?php if (!empty($return['form_a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_a_file']); ?>"  class="mb-2 " download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>

                                </div>
                                
                                <div class="col-lg-3 mt-2">
                                    <input type="file" id="form_a_file" name="form_a_file" class="form-control" onchange="validateFile('form_a')" style="display:none;"  />
                    <input type="hidden" name="form_a_file_old"  value="<?php echo $return['form_a_file']; ?>" />
                                    
                                </div>
                            </li>
                            <li>
                                <span class="f-w-600">Form B :- CHECKLIST FOR INFRASTRUCTURE PROJECTS</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_b_yes" name="form_b" value="1" onchange="validateFile('form_b')" <?php echo $return['form_b']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_b_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_b_no" name="form_b" value="0" onchange="validateFile('form_b')" <?php echo isset($return['form_b'])&&($return['form_b']=='0' || $return['form_b'] == '') ?'checked':''  ?>  />
                                        <label class="form-label border-checkbox-label" for="form_b_no">No</label>
                                    </div>
                                    <?php if (!empty($return['form_b_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_b_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                </div>
                                
                                <div class="col-lg-3 mt-2">
                                    <input type="file" id="form_b_file" name="form_b_file" class="form-control" onchange="validateFile('form_b')" style="display:none;" />
                                    <input type="hidden" name="form_b_file_old"  value="<?php echo $return['form_b_file']; ?>" />
                                </div>
                            </li>
                            <!-- Form C -->
                            <li>
                                <span class="f-w-600">Form C :- CHECKLIST FOR SUBMISSION OF PROPOSAL</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_c_yes" name="form_c" value="1" onchange="validateFile('form_c')" <?php echo $return['form_c']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_c_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_c_no" name="form_c" value="0" onchange="validateFile('form_c')" <?php echo isset($return['form_c'])&&($return['form_c']=='0' || $return['form_c'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_c_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_c_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_c_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input type="file" id="form_c_file" name="form_c_file" class="form-control" onchange="validateFile('form_c')" style="display:none;" />
                                    <input type="hidden" name="form_c_file_old"  value="<?php echo $return['form_c_file']; ?>" />
                                </div>
                            </li>
                            <!-- Form D -->
                            <li>
                                <span class="f-w-600">Form D :- SUMMARY SHEET FOR THE PROPOSAL</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_d_yes" name="form_d" value="1" onchange="validateFile('form_d')" <?php echo $return['form_d']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_d_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_d_no" name="form_d" value="0" onchange="validateFile('form_d')" <?php echo isset($return['form_d'])&&($return['form_d']=='0' || $return['form_d'] == '') ?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_d_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_d_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_d_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input type="file" id="form_d_file" name="form_d_file" class="form-control" onchange="validateFile('form_d')" style="display:none;" />
                                    <input type="hidden" name="form_d_file_old"  value="<?php echo $return['form_d_file']; ?>" />
                                </div>
                            </li>
                            <!-- Form E -->
                            <li>
                                <span class="f-w-600">Form E :- TRACK RECORD OF PROJECTS</span>
                                <div class="border-checkbox-section mt-2">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_e_yes" name="form_e" value="1" onchange="validateFile('form_e')" <?php echo $return['form_e']?'checked':''  ?> />
                                        <label class="form-label border-checkbox-label" for="form_e_yes">Yes</label>
                                    </div>
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox" type="radio" id="form_e_no" name="form_e" value="0" onchange="validateFile('form_e')"  <?php echo isset($return['form_e'])&&($return['form_e']=='0' || $return['form_e'] == '') ?'checked':''  ?>/>
                                        <label class="form-label border-checkbox-label" for="form_e_no">No</label>
                                    </div>
                                <?php if (!empty($return['form_e_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_e_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                </div>
                                <div class="col-lg-3 mt-2">
                                    <input type="file" id="form_e_file" name="form_e_file" class="form-control" onchange="validateFile('form_e')" style="display:none;" />
                                    <input type="hidden" name="form_e_file_old"  value="<?php echo $return['form_e_file']; ?>" />
                                </div>
                            </li>

                        </ul>
                        <span class="f-16 f-w-600 d-block mt-4">Kindly ensure the Organisation Seal/Stamp & Sign on each page.</span>
                    </div>
                </div>
                <script>
                    function validateFile(formId) {
                        const yesRadio = document.getElementById(`${formId}_yes`);
                        const noRadio = document.getElementById(`${formId}_no`);
                        const fileInput = document.getElementById(`${formId}_file`);

                        if (yesRadio.checked) {
                            $(`#${formId}`).show();
                            $(`#${formId}_file`).show();

                            if (!fileInput.value) {
                                fileInput.classList.add("is-invalid");
                                fileInput.classList.remove("is-valid");
                            } else {
                                fileInput.classList.remove("is-invalid");
                                fileInput.classList.add("is-valid");
                            }
                        } else if (noRadio.checked) {
                            $(`#${formId}`).hide();
                            $(`#${formId}_file`).hide();
                        }
                    }
                </script>
                <div id="form_a" style="display: none;">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-start" colspan="4">Form A</th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="4">CHECKLIST FOR SUBMISSION OF DOCUMENTS</th>
                                </tr>
                                <tr>
                                    <th width="5%">Sr. No</th>
                                    <th width="50%">Description of documents</th>
                                    <th width="25%" class="text-center">Attached (Yes / No)</th>
                                    <th width="20%" class="text-center">Remark</th>
                                </tr>
                                <tr>
                                    <td>1*</td>
                                    <td>Trust deed</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="trust_deed_yes" data-file-id="trust_deed_file" name="trust_deed" value="1" onclick="handleFileInput(this)" <?php echo $return['trust_deed']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="trust_deed_no" data-file-id="trust_deed_file" name="trust_deed" value="0" onclick="handleFileInput(this)" <?php echo isset($return['trust_deed'])&&($return['trust_deed']=='0' || $return['trust_deed'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                            <?php if (!empty($return['trust_deed_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['trust_deed_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="trust_deed_file" name="trust_deed_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                            <input type="hidden" name="trust_deed_file_old"  value="<?php echo $return['trust_deed_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="trust_deed_remark" name="trust_deed_remark" style="height:50px;"><?php echo $return['trust_deed_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2*</td>
                                    <td>Certificate of incorporation/Registration certificate</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="incorporation_yes" name="incorporation" value="1" data-file-id="incorporation_file" onclick="handleFileInput(this)" <?php echo $return['incorporation']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="incorporation_no" name="incorporation" value="0" data-file-id="incorporation_file" onclick="handleFileInput(this)" <?php echo isset($return['incorporation'])&&($return['incorporation']=='0' || $return['incorporation'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['incorporation_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['incorporation_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="incorporation_file" name="incorporation_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                            <input type="hidden" name="incorporation_file_old"  value="<?php echo $return['incorporation_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="incorporation_remark" name="incorporation_remark" style="height:50px;"><?php echo $return['incorporation_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3*</td>
                                    <td>CSR 1 Registration certificate</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="csr1_yes" name="csr1" value="1" data-file-id="csr1_file" onclick="handleFileInput(this)" <?php echo $return['csr1']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="csr1_no" name="csr1" value="0" data-file-id="csr1_file" onclick="handleFileInput(this)" <?php echo isset($return['csr1'])&&($return['csr1']=='0' || $return['csr1'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['csr1_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['csr1_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="csr1_file" name="csr1_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                            <input type="hidden" name="csr1_file_old"  value="<?php echo $return['csr1_file']; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="csr1_remark" name="csr1_remark" style="height:50px;"><?php echo $return['csr1_remark']??'';  ?> </textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4*</td>
                                    <td>Form 12 A</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form12a_yes" name="form12a" value="1" data-file-id="form12a_file" onclick="handleFileInput(this)" <?php echo $return['form12a']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form12a_no" name="form12a" value="0" data-file-id="form12a_file" onclick="handleFileInput(this)" <?php echo isset($return['form12a'])&&($return['form12a']=='0' || $return['form12a'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form12a_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form12a_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="form12a_file" name="form12a_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="form12a_file_old"  value="<?php echo $return['form12a_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="form12a_remark" name="form12a_remark" style="height:50px;"><?php echo $return['form12a_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5*</td>
                                    <td>Form 80 G</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form80g_yes" name="form80g" value="1" data-file-id="form80g_file" onclick="handleFileInput(this)" <?php echo $return['form80g']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form80g_no" name="form80g" value="0" data-file-id="form80g_file" onclick="handleFileInput(this)" <?php echo isset($return['form80g'])&&($return['form80g']=='0' || $return['form80g'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form80g_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form80g_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="form80g_file" name="form80g_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="form80g_file_old"  value="<?php echo $return['form80g_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="form80g_remark" name="form80g_remark" style="height:50px;"><?php echo $return['form80g_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6*</td>
                                    <td>35 AC registration if any</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form35ac_yes" name="form35ac" value="1" data-file-id="form35ac_file" onclick="handleFileInput(this)" <?php echo $return['form35ac']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="form35ac_no" name="form35ac" value="0" data-file-id="form35ac_file" onclick="handleFileInput(this)" <?php echo isset($return['form35ac'])&&($return['form35ac']=='0' || $return['form35ac'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['form35ac_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form35ac_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="form35ac_file" name="form35ac_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="form35ac_file_old"  value="<?php echo $return['form35ac_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="form35ac_remark" name="form35ac_remark" style="height:50px;"><?php echo $return['form35ac_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>7*</td>
                                    <td>PAN card</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="pan_yes" name="pan" value="1" data-file-id="pan_file" onclick="handleFileInput(this)" <?php echo $return['pan']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="pan_no" name="pan" value="0" data-file-id="pan_file" onclick="handleFileInput(this)" <?php echo isset($return['pan'])&&($return['pan']=='0' || $return['pan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['pan_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['pan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="pan_file" name="pan_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="pan_file_old"  value="<?php echo $return['pan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="pan_remark" name="pan_remark" style="height:50px;"><?php echo $return['pan_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>8*</td>
                                    <td>Last 3 financial years audited statements & ITR proof</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="financial_statements_yes" name="financial_statements" value="1" data-file-id="financial_statements_file" onclick="handleFileInput(this)" <?php echo $return['financial_statements']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="financial_statements_no" name="financial_statements" value="0" data-file-id="financial_statements_file" onclick="handleFileInput(this)" <?php echo isset($return['financial_statements'])&&($return['financial_statements']=='0' || $return['financial_statements'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['financial_statements_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['financial_statements_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="financial_statements_file" name="financial_statements_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="financial_statements_file_old"  value="<?php echo $return['financial_statements_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="financial_statements_remark" name="financial_statements_remark" style="height:50px;"><?php echo $return['financial_statements_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>9*</td>
                                    <td>Detailed proposal as per format given</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="proposal_yes" name="proposal" value="1" data-file-id="proposal_file" onclick="handleFileInput(this)" <?php echo $return['proposal']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="proposal_no" name="proposal" value="0" data-file-id="proposal_file" onclick="handleFileInput(this)" <?php echo isset($return['proposal'])&&($return['proposal']=='0' || $return['proposal'] == '' ?'checked':'')  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['proposal_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['proposal_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="proposal_file" name="proposal_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="proposal_file_old"  value="<?php echo $return['proposal_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="proposal_remark" name="proposal_remark" style="height:50px;"><?php echo $return['proposal_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>10*</td>
                                    <td>FCRA approval</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="fcra_yes" name="fcra" value="1" data-file-id="fcra_file" onclick="handleFileInput(this)" <?php echo $return['fcra']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="fcra_no" name="fcra" value="0" data-file-id="fcra_file" onclick="handleFileInput(this)" <?php echo isset($return['fcra'])&&($return['fcra']=='0' || $return['fcra'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['fcra_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['fcra_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="fcra_file" name="fcra_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="fcra_file_old"  value="<?php echo $return['fcra_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="fcra_remark" name="fcra_remark" style="height:50px;"><?php echo $return['fcra_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>11*</td>
                                    <td>TDS exemption certificate if applicable</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="tds_exemption_yes" name="tds_exemption" value="1" data-file-id="tds_exemption_file" onclick="handleFileInput(this)" <?php echo $return['tds_exemption']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="tds_exemption_no" name="tds_exemption" value="0" data-file-id="tds_exemption_file" onclick="handleFileInput(this)" <?php echo isset($return['tds_exemption'])&&($return['tds_exemption']=='0' || $return['tds_exemption'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['tds_exemption_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['tds_exemption_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="tds_exemption_file" name="tds_exemption_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="tds_exemption_file_old"  value="<?php echo $return['tds_exemption_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="tds_exemption_remark" name="tds_exemption_remark" style="height:50px;"><?php echo $return['tds_exemption_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>12*</td>
                                    <td>GST Registration certificate if applicable</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="gst_yes" name="gst" value="1" data-file-id="gst_file" onclick="handleFileInput(this)" <?php echo $return['gst']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="gst_no" name="gst" value="0" data-file-id="gst_file" onclick="handleFileInput(this)" <?php echo isset($return['gst'])&&($return['gst']=='0' || $return['gst'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['gst_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['gst_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="gst_file" name="gst_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="gst_file_old"  value="<?php echo $return['gst_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="gst_remark" name="gst_remark" style="height:50px;"><?php echo $return['gst_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>13*</td>
                                    <td>2 Reference letters of past or present supporters for CSR project</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="reference_yes" name="reference" value="1" data-file-id="reference_file" onclick="handleFileInput(this)" <?php echo $return['reference']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="reference_no" name="reference" value="0" data-file-id="reference_file" onclick="handleFileInput(this)" <?php echo isset($return['reference'])&&($return['reference']=='0' || $return['reference'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['reference_file'])): ?>
                                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['reference_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="reference_file" name="reference_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="reference_file_old"  value="<?php echo $return['reference_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="reference_remark" name="reference_remark" style="height:50px;"><?php echo $return['reference_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                            </table>
                            <span class="f-w-600 f-14">*Sr. Nos. are mandatory documents</span>
                        </div>
                    </div>

                    <div class="mt-3 enclosures">
                        <h3 class="f-18 f-w-600 mt-4 text-center">
                            Additional Information in respect of Audited Accounts as mentioned in Serial No. 7 of<br />Form-A of the Proposal Format for submission of CSR Proposals.
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
                                        <th>Sr. No</th>
                                        <th class="text-center">Sources of Funds</th>
                                        <?php foreach ($years as $year): ?>
                                            <th class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <!--  -->
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
                    <input type="text" class="form-control" id="source_<?= $index ?>" 
                           name="source_income[<?= $index  ?>]source[<?= $index ?>]" 
                           value="<?= $source ?>">
                </td>
                <td>
                    <input type="number" min="0" class="form-control text_align_right" id="fy1_<?= $index  ?>" 
                           name="source_income[<?= $index  ?>]fy1[<?= $index  ?>]" 
                           value="<?= $data['fy1'][$index] ?>" oninput="calculateTotals()">
                </td>
                <td>
                    <input type="number" min="0" class="form-control text_align_right" id="fy2_<?= $index ?>" 
                           name="source_income[<?= $index  ?>]fy2[<?= $index  ?>]" 
                           value="<?= $data['fy2'][$index] ?>" oninput="calculateTotals()">
                </td>
                <td>
                    <input type="number" min="0" class="form-control text_align_right" id="fy3_<?= $index  ?>" 
                           name="source_income[<?= $index  ?>]fy3[<?= $index  ?>]" 
                           value="<?= $data['fy3'][$index] ?>" oninput="calculateTotals()">
                </td>
                <td class="text-center">
                    <i class="fas fa-trash-alt text-danger cursor-pointer" onclick="removeIncomeRow(this)"></i>
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
                                        <th class="text-end">Total</th>
                                        <td><input type="text" class="form-control text_align_right" id="total_fy1" name="total_fy1" value="<?php echo $return['total_fy1'] ?? '' ; ?>" readonly></td>
                                        <td><input type="text" class="form-control text_align_right" id="total_fy2" name="total_fy2" value="<?php echo $return['total_fy2'] ?? '' ; ?>" readonly></td>
                                        <td><input type="text" class="form-control text_align_right" id="total_fy3" name="total_fy3" value="<?php echo $return['total_fy3'] ?? '' ; ?>" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-start">
                                            <button type="button" class="btn btn-primary" id="add_row_income" onclick="addIncomeRow()">Add More</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <script>
let incomeRowCount = document.querySelectorAll('#income_table_body tr').length || 0;

function addIncomeRow() {
    incomeRowCount++;
    const tableBody = document.querySelector('#income_table_body');
    console.log(incomeRowCount);
    const newRow = `
        <tr>
            <td>${incomeRowCount}</td>
            <td><input type="text" class="form-control" id="source_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]source[${incomeRowCount}]"></td>
            <td><input type="number" min="0" class="form-control text_align_right" id="fy1_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy1[${incomeRowCount}]" oninput="calculateTotals()"></td>
            <td><input type="number" min="0" class="form-control text_align_right" id="fy2_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy2[${incomeRowCount}]" oninput="calculateTotals()"></td>
            <td><input type="number" min="0" class="form-control text_align_right" id="fy3_${incomeRowCount}" 
                       name="source_income[${incomeRowCount}]fy3[${incomeRowCount}]" oninput="calculateTotals()"></td>
            <td class="text-center">
                <i class="fas fa-trash-alt text-danger cursor-pointer" onclick="removeIncomeRow(this)"></i>
            </td>
        </tr>`;
    tableBody.insertAdjacentHTML('beforeend', newRow);
}

function removeIncomeRow(button) {
    const row = button.closest('tr');
    row.remove();
    updateRowNumbers();
    calculateTotals();
}

function updateRowNumbers() {
    // const rows = document.querySelectorAll('#income_table_body tr');
    // rows.forEach((row, index) => {
    //     const rowNum = index + 1;
    //     row.querySelector('td:first-child').textContent = rowNum;

    //     row.querySelectorAll('input').forEach(input => {
    //         const baseName = input.name.replace(/\d+/g, rowNum);
    //         input.name = baseName;
    //         input.id = baseName.match(/\w+/)[0] + "_" + rowNum;
    //     });
    // });
    // incomeRowCount = rows.length;
    const rows = document.querySelectorAll('#income_table tbody tr');
    rowCounter = 0; // Reset counter
    rows.forEach((row, index) => {
        rowCounter = index + 1;
        row.querySelector('td:first-child').textContent = rowCounter; // Update Sr. No.
        row.querySelectorAll('input').forEach(input => {
            // Extract base name and keys from the current name
            const nameParts = input.name.match(/source_income\[(\d+)\](\w+)\[(\d+)\]/);
            if (nameParts) {
                const groupIndex = nameParts[1]; // Group ID
                const fieldName = nameParts[2]; // Field Name
                input.id = `${fieldName}_${rowCounter}`;
                input.name = `source_income[${rowCounter}]${fieldName}[${rowCounter}]`;
            }
        });
    });
}

function calculateTotals() {
    let totalFY1 = 0, totalFY2 = 0, totalFY3 = 0;

    document.querySelectorAll('input[name^="source_income"][name*="fy1"]').forEach(input => totalFY1 += parseFloat(input.value) || 0);
    console.log(totalFY1);
    document.querySelectorAll('input[name^="source_income"][name*="fy2"]').forEach(input => totalFY2 += parseFloat(input.value) || 0);
    document.querySelectorAll('input[name^="source_income"][name*="fy3"]').forEach(input => totalFY3 += parseFloat(input.value) || 0);

    document.getElementById('total_fy1').value = totalFY1.toFixed(2);
    document.getElementById('total_fy2').value = totalFY2.toFixed(2);
    document.getElementById('total_fy3').value = totalFY3.toFixed(2);
}

// Recalculate totals on page load
calculateTotals();

                    </script>

                    <div class="mt-3 enclosures">
                        <h3 class="f-16 f-w-600 mt-3">2. Provide details of income, expenditure, assets and liabilities during the last 3 Financial Years for which audited accounts are being submitted along with the application. </h3>

                        <h3 class="f-16 f-w-600 mt-4">Total Income:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="total_income_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th class="text-center">FY (<?= $year; ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--  -->
                                    <?php
                if ($return['income_data']) {
                    $income_data = json_decode($return['income_data'], true);
                    foreach ($income_data as $id => $income) {
                        echo "<tr>";
                        echo "<td><input type='number' class='form-control text_align_right' id='income_fy1_{$id}' name='income[{$id}]fy1' value='{$income['fy1']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='income_fy2_{$id}' name='income[{$id}]fy2' value='{$income['fy2']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='income_fy3_{$id}' name='income[{$id}]fy3' value='{$income['fy3']}'></td>";
                        echo "<td><input type='text' class='form-control' id='income_var1_{$id}' name='income[{$id}]var1' value='{$income['var1']}'></td>";
                        echo "<td><input type='text' class='form-control' id='income_var2_{$id}' name='income[{$id}]var2' value='{$income['var2']}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteRow(this, \"total_income_table\", \"income\")'></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light" type="button" onclick="addRow('total_income_table', 'income')">Add more</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Expenditure:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="expenditure_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
<!--                                     -->
                <?php
                if ($return['expenditure_data']) {
                    $expenditure_data = json_decode($return['expenditure_data'], true);
                    foreach ($expenditure_data as $id => $expenditure) {
                        echo "<tr>";
                        echo "<td><input type='number' class='form-control text_align_right' id='expenditure_fy1_{$id}' name='expenditure[{$id}]fy1' value='{$expenditure['fy1']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='expenditure_fy2_{$id}' name='expenditure[{$id}]fy2' value='{$expenditure['fy2']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='expenditure_fy3_{$id}' name='expenditure[{$id}]fy3' value='{$expenditure['fy3']}'></td>";
                        echo "<td><input type='text' class='form-control' id='expenditure_var1_{$id}' name='expenditure[{$id}]var1' value='{$expenditure['var1']}'></td>";
                        echo "<td><input type='text' class='form-control' id='expenditure_var2_{$id}' name='expenditure[{$id}]var2' value='{$expenditure['var2']}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteRow(this, \"expenditure_table\", \"expenditure\")'></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light" type="button" onclick="addRow('expenditure_table', 'expenditure')">Add more</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Assets:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="assets_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--  -->
                            <?php
                if ($return['assets_data']) {
                    $assets_data = json_decode($return['assets_data'], true);
                    foreach ($assets_data as $id => $assets) {
                        echo "<tr>";
                        echo "<td><input type='number' class='form-control text_align_right' id='assets_fy1_{$id}' name='assets[{$id}]fy1' value='{$assets['fy1']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='assets_fy2_{$id}' name='assets[{$id}]fy2' value='{$assets['fy2']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='assets_fy3_{$id}' name='assets[{$id}]fy3' value='{$assets['fy3']}'></td>";
                        echo "<td><input type='text' class='form-control' id='assets_var1_{$id}' name='assets[{$id}]var1' value='{$assets['var1']}'></td>";
                        echo "<td><input type='text' class='form-control' id='assets_var2_{$id}' name='assets[{$id}]var2' value='{$assets['var2']}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteRow(this, \"assets_table\", \"assets\")'></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light" type="button" onclick="addRow('assets_table', 'assets')">Add more</button>

                        <h3 class="f-16 f-w-600 mt-4">Total Liabilities:</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-1" id="liabilities_table">
                                <thead>
                                    <tr>
                                        <?php foreach ($years as $year): ?>
                                            <th class="text-center">FY (<?= $year ?> in Rs.)</th>
                                        <?php endforeach; ?>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-1).'-'.($currentYear-2) ?></th>
                                        <th class="text-center">Major Reasons for variation <?= ($currentYear-2).'-'.($currentYear-3) ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--  -->
                                <?php
                if ($return['liabilities_data']) {
                    $liabilities_data = json_decode($return['liabilities_data'], true);
                    foreach ($liabilities_data as $id => $liabilities) {
                        echo "<tr>";
                        echo "<td><input type='number' class='form-control text_align_right' id='liabilities_fy1_{$id}' name='liabilities[{$id}]fy1' value='{$liabilities['fy1']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='liabilities_fy2_{$id}' name='liabilities[{$id}]fy2' value='{$liabilities['fy2']}'></td>";
                        echo "<td><input type='number' class='form-control text_align_right' id='liabilities_fy3_{$id}' name='liabilities[{$id}]fy3' value='{$liabilities['fy3']}'></td>";
                        echo "<td><input type='text' class='form-control' id='liabilities_var1_{$id}' name='liabilities[{$id}]var1' value='{$liabilities['var1']}'></td>";
                        echo "<td><input type='text' class='form-control' id='liabilities_var2_{$id}' name='liabilities[{$id}]var2' value='{$liabilities['var2']}'></td>";
                        echo "<td class='text-center'>
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteRow(this, \"liabilities_table\", \"liabilities\")'></i>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light" type="button" onclick="addRow('liabilities_table', 'liabilities')">Add more</button>

                        <script>

                            const rowCounters = {
            income: <?= count($income_data ?? []); ?>,
            expenditure: <?= count($expenditure_data ?? []); ?>,
            assets: <?= count($assets_data ?? []); ?>,
            liabilities: <?= count($liabilities_data ?? []); ?>
        };

                            function addRow(tableId, section) {
                                rowCounters[section]++;
                                const tableBody = document.querySelector(`#${tableId} tbody`);
                                const newRow = `
            <tr>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy1_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy1"></td>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy2_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy2"></td>
                <td><input type="number" class="form-control text_align_right" id="${section}_fy3_${rowCounters[section]}" name="${section}[${rowCounters[section]}]fy3"></td>
                <td><input type="text" class="form-control" id="${section}_var1_${rowCounters[section]}" name="${section}[${rowCounters[section]}]var1"></td>
                <td><input type="text" class="form-control" id="${section}_var2_${rowCounters[section]}" name="${section}[${rowCounters[section]}]var2"></td>
                <td class="text-center">
                    <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick="deleteRow(this, '${tableId}', '${section}')"></i>
                    
                </td>
                                    </tr>`;
                                    tableBody.insertAdjacentHTML('beforeend', newRow);
                                }

                                function deleteRow(button, tableId, section) {
            const row = button.closest('tr');
            row.remove();
            updateRowNames(tableId, section);
        }

        function updateRowNames(tableId, section) {
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            rowCounters[section] = 0;

            rows.forEach((row, index) => {
                const newRowNumber = index + 1;
                rowCounters[section] = newRowNumber;

                row.querySelectorAll('input').forEach(input => {
                    const nameMatch = input.name.match(/(\w+)\[(\d+)\](\w+)/);
                    if (nameMatch) {
                        const baseName = nameMatch[1];
                        const fieldName = nameMatch[3];
                        input.id = `${baseName}_${fieldName}_${newRowNumber}`;
                        input.name = `${baseName}[${newRowNumber}]${fieldName}`;
                    }
                });
            });
        }
                        </script>
                    </div>
                </div>
                <div id="form_b" style="display: none;">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive form_B">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-start" colspan="4">Form B</th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="4">CHECKLIST FOR INFRASTRUCTURE PROJECTS</th>
                                </tr>
                                <tr>
                                    <th class="w-5">Sr. No</th>
                                    <th class="w-50">Documents</th>
                                    <th class="w-25 ">Attached (Yes / No)</th>
                                    <th class="w-25 ">Remark</th>
                                </tr>
                                <tr>
                                    <th>A</th>
                                    <th>LAND RELATED</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Land Ownership document</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="land_ownership_yes" name="land_ownership" value="1" data-file-id="land_ownership_file" onclick="handleFileInput(this)" <?php echo $return['land_ownership']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="land_ownership_no" name="land_ownership" value="0" data-file-id="land_ownership_file" onclick="handleFileInput(this)" <?php echo isset($return['land_ownership'])&&($return['land_ownership']=='0' || $return['land_ownership'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['land_ownership_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['land_ownership_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="land_ownership_file" name="land_ownership_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="land_ownership_file_old"  value="<?php echo $return['land_ownership_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="land_ownership_remark" name="land_ownership_remark" style="height:50px;"><?php echo $return['land_ownership_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Property Card (Commercial/ Vastu/Wetland)</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="property_card_yes" name="property_card" value="1" data-file-id="property_card_file" onclick="handleFileInput(this)" <?php echo $return['property_card']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="property_card_no" name="property_card" value="0" data-file-id="property_card_file" onclick="handleFileInput(this)" <?php echo isset($return['property_card'])&&($return['property_card']=='0' || $return['property_card'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['property_card_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['property_card_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="property_card_file" name="property_card_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="property_card_file_old"  value="<?php echo $return['property_card_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="property_card_remark" name="property_card_remark" style="height:50px;"><?php echo $return['property_card_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>B</th>
                                    <th>ESTIMATES & APPROVALS</th>
                                    <th></th>
                                    <th></th>
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
                                                    <input type="radio" id="drawings_yes" name="drawings" value="1" data-file-id="drawings_file" onclick="handleFileInput(this)" <?php echo $return['drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="drawings_no" name="drawings" value="0" data-file-id="drawings_file" onclick="handleFileInput(this)"  <?php echo isset($return['drawings'])&&($return['drawings']=='0' || $return['drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="drawings_file" name="drawings_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="drawings_file_old"  value="<?php echo $return['drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="drawings_remark" name="drawings_remark" style="height:50px;"><?php echo $return['drawings_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Drawings sanctioned by Govt. Authorities</td>
                                    <td>
                                        <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="govt_drawings_yes" name="govt_drawings" value="1" data-file-id="govt_drawings_file" onclick="handleFileInput(this)" <?php echo $return['govt_drawings']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="govt_drawings_no" name="govt_drawings" value="0" data-file-id="govt_drawings_file" onclick="handleFileInput(this)"  <?php echo isset($return['govt_drawings'])&&($return['govt_drawings']=='0' || $return['govt_drawings'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['govt_drawings_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['govt_drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="govt_drawings_file" name="govt_drawings_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="govt_drawings_file_old"  value="<?php echo $return['govt_drawings_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="govt_drawings_remark" name="govt_drawings_remark" style="height:50px;"><?php echo $return['govt_drawings_remark']??'';  ?></textarea>
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
                                                    <input type="radio" id="basis_estimate_yes" name="basis_estimate" value="1" data-file-id="basis_estimate_file" onclick="handleFileInput(this)" <?php echo $return['basis_estimate']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="basis_estimate_no" name="basis_estimate" value="0" data-file-id="basis_estimate_file" onclick="handleFileInput(this)"  <?php echo isset($return['basis_estimate'])&&($return['basis_estimate']=='0' || $return['basis_estimate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['basis_estimate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['basis_estimate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="basis_estimate_file" name="basis_estimate_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="basis_estimate_file_old"  value="<?php echo $return['basis_estimate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="basis_estimate_remark" name="basis_estimate_remark" style="height:50px;"><?php echo $return['basis_estimate_remark']??'';  ?></textarea>
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
                                                    <input type="radio" id="stability_certificate_yes" name="stability_certificate" value="1" data-file-id="stability_certificate_file" onclick="handleFileInput(this)" <?php echo $return['stability_certificate']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                            <input type="radio" id="stability_certificate_no" name="stability_certificate" value="0" data-file-id="stability_certificate_file" onclick="handleFileInput(this)"  <?php echo isset($return['stability_certificate'])&&($return['stability_certificate']=='0' || $return['stability_certificate'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                    <?php if (!empty($return['stability_certificate_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['stability_certificate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>
                                        </div>
                                        <div><input type="file" id="stability_certificate_file" name="stability_certificate_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="stability_certificate_file_old"  value="<?php echo $return['stability_certificate_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="stability_certificate_remark" name="stability_certificate_remark" style="height:50px;"><?php echo $return['stability_certificate_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>C</th>
                                    <th>EXECUTION WORKS</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td class="text-wrap">
                                        <p class="mb-1">Execution Plan (Mention in briefly, separate annexure can be attached)</p>
                                    </td>
                                    <td>
                                        <div class="form-radio">

                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="execution_works_plan_yes" name="execution_works_plan" value="1" data-file-id="execution_works_plan_file" onclick="handleFileInput(this)" <?php echo $return['execution_works_plan']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="execution_works_plan_no" name="execution_works_plan" value="0" data-file-id="execution_works_plan_file" onclick="handleFileInput(this)"  <?php echo isset($return['execution_works_plan'])&&($return['execution_works_plan']=='0' || $return['execution_works_plan'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                        <?php if (!empty($return['execution_works_plan_file'])): ?>
                                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['execution_works_plan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                    <?php endif; ?>

                                        </div>
                                        <div><input type="file" id="execution_works_plan_file" name="execution_works_plan_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="execution_works_plan_file_old"  value="<?php echo $return['execution_works_plan_file']; ?>" /></div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="execution_works_plan_remark" name="execution_works_plan_remark" style="height:50px;"><?php echo $return['execution_works_plan_remark']??'';  ?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_c" style="display: none;">

                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-start" colspan="3">Form C</th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="3">CHECKLIST FOR SUBMISSION OF PROPOSAL</th>
                                </tr>
                                <tr>
                                    <th width="5%">S.N</th>
                                    <th class="text-center" width="65%">Points</th>
                                    <th class="text-center" width="30%">Details</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Address of registered office of the Organisation (in India)</td>
                                    <td><textarea class="form-control" id="address_registered_office" name="address_registered_office" style="height:50px;"><?php echo $return['address_registered_office']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Any branches of the Organisation? Kindly mention</td>
                                    <td><textarea class="form-control" id="branches_organisation" name="branches_organisation" style="height:50px;"><?php echo $return['branches_organisation']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Registered as a trust/society/section 8 company or Other</td>
                                    <td><textarea class="form-control" id="registered_as" name="registered_as" style="height:50px;"><?php echo $return['registered_as']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>How long has the Organisation been in existence in years?</td>
                                    <td><textarea class="form-control" id="years_of_existence" name="years_of_existence" style="height:50px;"><?php echo $return['years_of_existence']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Which is the Organisation’s geographical area of work?</td>
                                    <td><textarea class="form-control" id="geographical_area_of_work" name="geographical_area_of_work" style="height:50px;"><?php echo $return['geographical_area_of_work']??'';  ?></textarea></td>
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
                                    <td><textarea class="form-control" id="expertise_areas" name="expertise_areas" style="height:50px;"><?php echo $return['expertise_areas']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Names of Board Members/Trustees and CEO.</td>
                                    <td><textarea class="form-control" id="board_members" name="board_members" style="height:50px;"><?php echo $return['board_members']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Are any of the trustees/board members affiliated to any political/religious Organisation?</td>
                                    <td><textarea class="form-control" id="affiliated_political_religious" name="affiliated_political_religious" style="height:50px;"><?php echo $return['affiliated_political_religious']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>What are the sources of funding of the Organisation?</td>
                                    <td><textarea class="form-control" id="sources_of_funding" name="sources_of_funding" style="height:50px;"><?php echo $return['sources_of_funding']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Has the Organisation received any awards/recognition for its work? Kindly mention</td>
                                    <td><textarea class="form-control" id="awards_recognition" name="awards_recognition" style="height:50px;"><?php echo $return['awards_recognition']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>Does it receive funds/implement programs for the government? If yes, details</td>
                                    <td><textarea class="form-control" id="government_funds_programs" name="government_funds_programs" style="height:50px;"><?php echo $return['government_funds_programs']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Has the Organisation been successful in handing over projects/making projects sustainable? Give example</td>
                                    <td><textarea class="form-control" id="projects_sustainability" name="projects_sustainability" style="height:50px;"><?php echo $return['projects_sustainability']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>Has the Organisation been blacklisted by government/PSU or any corporate? If yes, reasons for it</td>
                                    <td><textarea class="form-control" id="blacklisted_details" name="blacklisted_details" style="height:50px;"><?php echo $return['blacklisted_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td>Is the Organisation empanelled by any reputed institute or agency? If yes, give details</td>
                                    <td><textarea class="form-control" id="empanelled_details" name="empanelled_details" style="height:50px;"><?php echo $return['empanelled_details']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td>Is the Organisation associated with any other corporate for implementing CSR projects? Mention names</td>
                                    <td><textarea class="form-control" id="associated_csr_projects" name="associated_csr_projects" style="height:50px;"><?php echo $return['associated_csr_projects']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>16</td>
                                    <td>Total turnover in the last 3 financial years</td>
                                    <td><textarea class="form-control" id="total_turnover" name="total_turnover" style="height:50px;"><?php echo $return['total_turnover']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>17</td>
                                    <td>Strength of staff (number of full-time employees)</td>
                                    <td><textarea class="form-control" id="staff_strength" name="staff_strength" style="height:50px;"><?php echo $return['staff_strength']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>18</td>
                                    <td>Any past projects with BPCL/other PSU’s? If yes, then details thereof</td>
                                    <td><textarea class="form-control" id="past_projects_bpcl" name="past_projects_bpcl" style="height:50px;"><?php echo $return['past_projects_bpcl']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>19</td>
                                    <td>Qualification/experience of staff/project team</td>
                                    <td><textarea class="form-control" id="staff_qualification_experience" name="staff_qualification_experience" style="height:50px;"><?php echo $return['staff_qualification_experience']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>20</td>
                                    <td>Please provide two references of past funders of your projects (Email id/Contact Person with Designation/Contact Number)</td>
                                    <td><textarea class="form-control" id="references_past_funders" name="references_past_funders" style="height:50px;"><?php echo $return['references_past_funders']??'';  ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>Have you filled all the information in the proposal?</td>
                                    <td><textarea class="form-control" id="filled_all_information" name="filled_all_information" style="height:50px;"><?php echo $return['filled_all_information']??'';  ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_d" style="display: none;">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-start" colspan="2">Form D</th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="2">Summary Sheet for the Proposal</th>
                                </tr>
                                <tr>
                                    <td width="50%">Title of the project</td>
                                    <td width="50%"><input class="form-control" id="title_of_project" name="title_of_project"></td>
                                </tr>
                                <tr>
                                    <td>Project Description</td>
                                    <td>
                                        <textarea class="form-control" id="formd_project_description" name="formd_project_description" placeholder="(Write a brief description of project)"><?php echo $return['formd_project_description']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Name of Executing Organisation</td>
                                    <td>
                                        <textarea class="form-control" id="executing_organisation" name="executing_organisation"><?php echo $return['executing_organisation']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Address of Organisation (Registered address & address of branch from which proposal is given)</td>
                                    <td>
                                        <textarea class="form-control" id="organisation_address" name="organisation_address"><?php echo $return['organisation_address']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Person</td>
                                    <td>
                                        <textarea class="form-control" id="contact_person" name="contact_person"><?php echo $return['contact_person']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Number & email address</td>
                                    <td>
                                        <textarea class="form-control" id="contact_details" name="contact_details"><?php echo $return['contact_details']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Experience of the Organisation</td>
                                    <td>
                                        <textarea class="form-control" id="organisation_experience" name="organisation_experience" placeholder="(Mention no. of years of experience & short brief about other projects executed)"><?php echo $return['organisation_experience']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Place of execution of project</td>
                                    <td>
                                        <textarea class="form-control" id="execution_place" name="execution_place"><?php echo $return['execution_place']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reason for choice of place</td>
                                    <td>
                                        <textarea class="form-control" id="place_reason" name="place_reason"><?php echo $return['place_reason']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tenure of the project</td>
                                    <td>
                                        <textarea class="form-control" id="project_tenure" name="project_tenure"><?php echo $return['project_tenure']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cost of the project (enclose a detailed budget)</td>
                                    <td>
                                        <textarea class="form-control" id="project_cost" name="project_cost"><?php echo $return['project_cost']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Funds requested from Infisuite</td>
                                    <td>
                                        <textarea class="form-control" id="funds_infisuite" name="funds_infisuite"><?php echo $return['funds_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Funds requested from any other sources if any</td>
                                    <td>
                                        <textarea class="form-control" id="funds_other_sources" name="funds_other_sources"><?php echo $return['funds_other_sources']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Objective of the project</td>
                                    <td>
                                        <textarea class="form-control" id="project_objective" name="project_objective"><?php echo $return['project_objective']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reason of Organisation preferring Infisuite to be part of this project</td>
                                    <td>
                                        <textarea class="form-control" id="reason_infisuite" name="reason_infisuite" placeholder="(mention nearest BPCL Business unit, whether the proposal is in line with BPCL thrust area & justify why BPCL should consider the proposal)"><?php echo $return['reason_infisuite']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Benefits/Outcomes expected from the project</td>
                                    <td>
                                        <textarea class="form-control" id="project_benefits" name="project_benefits"><?php echo $return['project_benefits']??'';  ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Quantitative (Mention as per parameter suitable, but make sure to mention number of beneficiaries)</td>
                                    <td>
                                        <input class="form-control" type="text" id="quantitative_1" name="quantitative_1" placeholder="1." value="<?php echo $return['quantitative_1']??'';  ?>" />
                                        <input class="form-control mt-1" type="text" id="quantitative_2" name="quantitative_2" placeholder="2." value="<?php echo $return['quantitative_2']??'';  ?>" />
                                        <input class="form-control mt-1" type="text" id="quantitative_3" name="quantitative_3" placeholder="3." value="<?php echo $return['quantitative_3']??'';  ?>" />
                                        <input class="form-control mt-1" type="text" id="quantitative_4" name="quantitative_4" placeholder="4." value="<?php echo $return['quantitative_4']??'';  ?>" />
                                        <input class="form-control mt-1" type="text" id="quantitative_5" name="quantitative_5" placeholder="5." value="<?php echo $return['quantitative_5']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Qualitative (parameters which are difficult to capture in numbers)</td>
                                    <td>
                                        <input class="form-control" type="text" id="qualitative_1" name="qualitative_1" placeholder="1." value="<?php echo $return['qualitative_1']??'';  ?>"/>
                                        <input class="form-control mt-1" type="text" id="qualitative_2" name="qualitative_2" placeholder="2." value="<?php echo $return['qualitative_2']??'';  ?>" />
                                        <input class="form-control mt-1" type="text" id="qualitative_3" name="qualitative_3" placeholder="3." value="<?php echo $return['qualitative_3']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Requirement of any approval from local bodies/government/municipality or any statutory body</td>
                                    <td>
                                        <input class="form-control" type="text" id="approval_requirement" name="approval_requirement" placeholder="Yes or No, If yes, give details in short" value="<?php echo $return['approval_requirement']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Whether similar projects executed earlier?</td>
                                    <td>
                                        <input class="form-control" type="text" id="similar_projects" name="similar_projects" placeholder="Yes or No, If yes, give details in short"  value="<?php echo $return['similar_projects']??'';  ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>In case the proposal is approved, the organisation is okay to open separate bank account for Infisuite? (Yes/No)</td>
                                    <td>
                                        <input class="form-control" type="text" id="bank_account_infisuite" name="bank_account_infisuite" value="<?php echo $return['bank_account_infisuite']??'';  ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Information given above is true and we can provide the documentary proof of it in case of enquiry</td>
                                    <td>
                                    <div class="form-radio">
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="information_true_yes" data-file-id="information_true_file" name="information_true" value="1" onclick="handleFileInput(this)" <?php echo $return['information_true']?'checked':'' ; ?>>
                                                    <i class="helper"></i>Yes
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label class="form-label">
                                                    <input type="radio" id="information_true_no" data-file-id="information_true_file" name="information_true" value="0" onclick="handleFileInput(this)" <?php echo isset($return['information_true'])&&($return['information_true']=='0' || $return['information_true'] == '') ?'checked':''  ?>>
                                                    <i class="helper"></i>No
                                                </label>
                                            </div>
                                            <?php if (!empty($return['information_true_file'])): ?>
    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['information_true_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
<?php endif; ?>
                                        </div>
                                        <div><input type="file" id="information_true_file" name="information_true_file" class="form-control d-none" onchange="handleFileInput(this)" />
                                        <input type="hidden" name="information_true_file_old"  value="<?php echo $return['information_true_file']; ?>" /></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form_e" style="display: none;">
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="form_e_table">
                                <thead>
                                    <tr>
                                        <th class="text-start" colspan="8">Form E</th>
                                    </tr>
                                    <tr>
                                        <th class="text-start" colspan="8">
                                            <h3 class="f-18 f-w-600 mt-4 text-center text-underline">Track Record of Projects</h3>
                                            <p class="text-center f-14">(Kindly mention projects undertaken in the last 3 years.<br />If an organisation is existing for 3 years, track record from inception till date is required)</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Project Description</th>
                                        <th>Focus Area</th>
                                        <th>Location</th>
                                        <th>Period (To - From)</th>
                                        <th>Value in INR Lakhs</th>
                                        <th>Number of Beneficiaries</th>
                                        <th>Partner Name (Funding Agency)</th>
                                        <th>Action</th>
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
                    <td><input type="text" class="form-control" id="project_description_<?= $srNo; ?>" name="project_description[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['project_description'] ?? '-') ?>"></td>
                    <td><input type="text" class="form-control" id="focus_area_<?= $srNo; ?>" name="focus_area[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['focus_area'] ?? '-') ?>"></td>
                    <td><input type="text" class="form-control" id="location_<?= $srNo; ?>" name="location[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['location'] ?? '-') ?>"></td>
                    <td><input type="text" class="form-control" id="period_<?= $srNo; ?>" name="period[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['period'] ?? '-') ?>"></td>
                    <td><input type="number" class="form-control text_align_right" id="value_<?= $srNo; ?>" name="value[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['value'] ?? '-') ?>"></td>
                    <td><input type="number" class="form-control text_align_right" id="beneficiaries_<?= $srNo; ?>" name="beneficiaries[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['beneficiaries'] ?? '-') ?>"></td>
                    <td><input type="text" class="form-control" id="partner_name_<?= $srNo; ?>" name="partner_name[<?= $srNo; ?>]" value="<?= htmlspecialchars($project['partner_name'] ?? '-') ?>"></td>
                    <td class="text-center">
                        <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteFormERow(this)'></i>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" onclick="addFormERow()">Add More</button>
                        </div>
                    </div>
                </div>
                <script>
                    let rowCounter = <?= isset($srNo) ? $srNo - 1 : 0; ?>;

                    // Function to add a new row
                    function addFormERow() {
                        rowCounter++;
                        const tableBody = document.querySelector('#form_e_table tbody');
                        const newRow = `
            <tr>
                <td>${rowCounter}</td>
                <td><input type="text" class="form-control" id="project_description_${rowCounter}" name="project_description[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="focus_area_${rowCounter}" name="focus_area[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="location_${rowCounter}" name="location[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="period_${rowCounter}" name="period[${rowCounter}]"></td>
                <td><input type="number" class="form-control text_align_right" id="value_${rowCounter}" name="value[${rowCounter}]"></td>
                <td><input type="number" class="form-control text_align_right" id="beneficiaries_${rowCounter}" name="beneficiaries[${rowCounter}]"></td>
                <td><input type="text" class="form-control" id="partner_name_${rowCounter}" name="partner_name[${rowCounter}]"></td>
                <td class="text-center">
                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteFormERow(this)'></i>
                    
                </td>
                            </tr>`;
                            tableBody.insertAdjacentHTML('beforeend', newRow);
                        }

                    // Function to delete a row
                        function deleteFormERow(button) {
                            const row = button.closest('tr');
                            row.remove();
                            updateFormERowNumbers();
                        }

                    // Function to update row numbers after a row is removed
                        function updateFormERowNumbers() {
        const rows = document.querySelectorAll('#form_e_table tbody tr');
        rowCounter = 0;
        rows.forEach((row, index) => {
            rowCounter = index + 1;
            row.querySelector('td:first-child').textContent = rowCounter;
            row.querySelectorAll('input').forEach(input => {
                const nameParts = input.name.split('_');
                input.id = `${nameParts[0]}_${rowCounter}`;
                input.name = `${nameParts[0]}[${rowCounter}]`;
            });
        });
    }
                </script>
                <div id="form_f" >
                    <div class="mt-3 enclosures">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="project_tracking_table">
                                <thead>

                                    <tr>
                                        <th class="text-start" colspan="8">
                                            <h3 class="f-18 f-w-600 mt-4 text-center text-underline">Project Tracking</h3>

                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Tracking Name</th>
                                        <th>Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Budget</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php 
                                if ($return['tracking_data']) {
                                    $tracking_data = json_decode($return['tracking_data'], true);
                                    $rowCounter = 0;

                                    foreach ($tracking_data as $id => $project) {
                                        $rowCounter++;
                                        $tracking_name = htmlspecialchars($project['tracking_name'] ?? '');
                                        $description = htmlspecialchars($project['description'] ?? '');
                                        $start_date = htmlspecialchars($project['start_date'] ?? '');
                                        $end_date = htmlspecialchars($project['end_date'] ?? '');
                                        $tracking_budget = htmlspecialchars($project['tracking_budget'] ?? '');

                                        echo "
                                        <tr>
                                            <td>{$rowCounter}</td>
                                            <td><input type='text' class='form-control' id='tracking_name_{$rowCounter}' name='tracking_name[{$rowCounter}]' value='{$tracking_name}'></td>
                                            <td><textarea class='form-control' id='tracking_description_{$rowCounter}' name='description[{$rowCounter}]'>{$description}</textarea></td>
                                            <td><input type='date' class='form-control' id='start_date_{$rowCounter}' name='start_date[{$rowCounter}]' value='{$start_date}' onchange='updateEndDateMin({$rowCounter})'></td>
                                            <td><input type='date' class='form-control' id='end_date_{$rowCounter}' name='end_date[{$rowCounter}]' value='{$end_date}'></td>
                                            <td><input type='number' min='0' class='form-control text_align_right' id='tracking_budget_{$rowCounter}' name='tracking_budget[{$rowCounter}]' value='{$tracking_budget}'></td>
                                            <td class='text-center'>
                                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteTrackRow(this)'></i>
                                            </td>
                                        </tr>";
                                    }
                                }
                                ?>
                                </tbody>

                                </table>

                                <button type="button" class="btn btn-primary mt-3" onclick="addTrackRow()">Add More</button>
                            </div>
                        </div>
                    </div>
                    <script>
                        let trackRowCounter = <?php echo isset($tracking_data) ? count($tracking_data) : 0; ?>;

                    // Function to add a new row
                        function addTrackRow() {
                            trackRowCounter++;
                            const tableBody = document.querySelector('#project_tracking_table tbody');
                            const newRow = `
            <tr>
                <td>${trackRowCounter}</td>
                <td><input type="text" class="form-control" id="tracking_name_${trackRowCounter}" name="tracking_name[${trackRowCounter}]"></td>
                <td><textarea  class="form-control" id="tracking_description_${trackRowCounter}" name="description[${trackRowCounter}]"></textarea>
                <td><input type="date" class="form-control" id="start_date_${trackRowCounter}" name="start_date[${trackRowCounter}]" onchange="updateEndDateMin(${trackRowCounter})"></td>
                <td><input type="date" class="form-control" id="end_date_${trackRowCounter}" name="end_date[${trackRowCounter}]"></td>
                <td><input type="number" min="0" class="form-control" id="tracking_budget_${trackRowCounter}" name="tracking_budget[${trackRowCounter}]"></td>
                <td class="text-center">
                                <i class='fas fa-trash-alt text-danger cursor-pointer remove-row' onclick='deleteTrackRow(this)'></i>
                    
                </td>
                                </tr>`;
                                tableBody.insertAdjacentHTML('beforeend', newRow);
                            }

                    // Function to delete a row
                            function deleteTrackRow(button) {
                                const row = button.closest('tr');
                                row.remove();
                                updateTrackRowNumbers();
                            }

                    function updateTrackRowNumbers() {
                        const rows = document.querySelectorAll('#project_tracking_table tbody tr');
                        trackRowCounter = rows.length; // Update counter
                        rows.forEach((row, index) => {
                            const newIndex = index + 1;
                            row.querySelector('td:first-child').textContent = newIndex; // Update Sr. No.
                            row.querySelectorAll('input, textarea').forEach(input => {
                                const baseName = input.name.replace(/\[\d+\]/, '');
                                input.id = `${baseName}_${newIndex}`;
                                input.name = `${baseName}[${newIndex}]`;
                            });
                        });
                    }
                    function updateEndDateMin(rowNumber) {
                        const startDateInput = document.getElementById(`start_date_${rowNumber}`);
                        const endDateInput = document.getElementById(`end_date_${rowNumber}`);
                        endDateInput.min = startDateInput.value; // Set the min attribute of end_date to the value of start_date
                    }
                </script>

                <div class="mt-3 enclosures">
                    <!-- <h3 class="f-18 f-w-600 mt-3">Form G</h3> -->
                    <h3 class="f-18 f-w-600 mt-4 text-center text-underline mb-4">Declaration</h3>
                    <p class="f-14">I / We hereby declare and certify that all the information given in the application form and in the documents submitted are true and correct to the best of my knowledge.</p>
                    <p class="f-14">I / We acknowledge that in case of misrepresentation of information or furnishing of false information, my application is liable to be rejected and /or approved project is be liable to be terminated by BPCL at any stage.</p>
                    <p class="f-14">I / We further declare and certify that our organisation / company / group of companies has / have not been blacklisted or been put on the holiday list by any of the Public Sector Undertakings in India or Statutory authority or Government.</p>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Signature</span>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" id="signature" name="signature" placeholder="Signature..." value="<?php echo $return['signature']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Name</span>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name..." value="<?php echo $return['name']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Designation</span>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation..." value="<?php echo $return['designation']??'';  ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <span class="f-14 f-w-600">Seal of the Organisation</span>
                        </div>
                        <div class="col-lg-3">
                            <input type="file" class="form-control" id="organisation_seal" name="organisation_seal" placeholder="Seal of the Organisation...">
                            <input type="hidden" name="organisation_seal_old"  value="<?php echo $return['organisation_seal']; ?>" />
                        </div>
                        <div class="col-lg-1">   
                         <?php if (!empty($return['organisation_seal'])): ?>
                            <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['organisation_seal']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
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
            <!-- <button class="btn btn-primary mt-3 float-end" type="button" onclick="printDiv('printableArea')" >Save</button> -->
        </form>
    </div>
    
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const generalStep1 = document.querySelector(".general_step_1");
    var old_id = <?php echo $rid ?? '0';  ?>;
    console.log(old_id);
        if(old_id && old_id != '0')  { 
            document.querySelector(".general_instructions_2").style.display = "block";
            document.querySelector(".general_instructions_3").style.display = "block";
            document.querySelector(".general_instructions").style.display = "none";
        } 

        // Add the click event listener
        generalStep1.addEventListener("click", function () {
            document.querySelector(".general_instructions_2").style.display = "block";
            document.querySelector(".general_instructions_3").style.display = "block";
            document.querySelector(".general_instructions").style.display = "none";
        });
        document.querySelector(".previous_open").addEventListener("click", function() {
            document.querySelector(".general_instructions_2").style.display = "none";
            document.querySelector(".general_instructions_3").style.display = "none";
            document.querySelector(".general_instructions").style.display = "block";
        });

        const formIds = ['form_a', 'form_b', 'form_c', 'form_d', 'form_e'];
        // Loop through each form ID and check if the "Yes" radio button is pre-selected
        formIds.forEach(function (formId) {
            const yesRadio = document.getElementById(`${formId}_yes`);
            if (yesRadio && yesRadio.checked) {
                validateFile(formId); // Dynamically call the function for each form
            }
        });

        const radioButtons = document.querySelectorAll('input[type="radio"][data-file-id]');
        radioButtons.forEach(radio => {
            if (radio.checked && radio.value === "1") {
                handleFileInput(radio); // Trigger if the radio is set to "Yes"
            }
        });


    });

    function handleFileInput(element) {
        const fileInputId = element.getAttribute('data-file-id');
        const fileInput = document.getElementById(fileInputId);

        if (element.type === 'radio') {
            if (element.value === "1") {
                fileInput.classList.remove('d-none');
                fileInput.classList.add('is-invalid');
            } else {
                fileInput.classList.add('d-none');
                fileInput.value = '';
                fileInput.classList.remove('is-invalid');
            }
        }

        if (element.type === 'file') {
            if (element.files.length === 0) {
                element.classList.add('is-invalid');
            } else {
                element.classList.remove('is-invalid');
                element.classList.add('is-valid');
            }
        }
    }


    function updateCheckboxValue(element) {
        element.value = element.checked ? 1 : 0;
    }

    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();


        const formData = new FormData(this);
        formData.append('page', 'insertCSR');
        formData.append('table_name', 'temp_csr_proposal_data');
        const finalFormData = {
            groups_data: {},
            source_income_data: {},
            income_data: {},
            expenditure_data: {},
            assets_data: {},
            liabilities_data: {},
            project_data: {},
            tracking_data: {},
            tentative_payment_schedule: {}
        };
        // Process all form fields
        formData.forEach((value, key) => {
            // Handle dynamic fields
            if (key.startsWith('groups')) {
                const match = key.match(/groups\[(\d+)\]\[(\w+)\]\[(\d+)\]/);
                if (match) {
                    const [_, groupId, fieldName, itemIndex] = match;
                    if (!finalFormData.groups_data[groupId]) finalFormData.groups_data[groupId] = {};
                    if (!finalFormData.groups_data[groupId][fieldName]) finalFormData.groups_data[groupId][fieldName] = {};
                    finalFormData.groups_data[groupId][fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('payment_schedule')) {
                const match = key.match(/payment_schedule_(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemIndex] = match;
                    console.log('itemIndex:', itemIndex, "fieldName: ", fieldName)
                    if (!finalFormData.tentative_payment_schedule[fieldName]) finalFormData.tentative_payment_schedule[fieldName] = {};
                    finalFormData.tentative_payment_schedule[fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('source_income')) {
                const match = key.match(/source_income\[(\d+)\](\w+)\[(\d+)\]/);
                if (match) {
                    const [_, incomeId, fieldName, itemIndex] = match;
                    if (!finalFormData.source_income_data[incomeId]) finalFormData.source_income_data[incomeId] = {};
                    if (!finalFormData.source_income_data[incomeId][fieldName]) finalFormData.source_income_data[incomeId][fieldName] = {};
                    finalFormData.source_income_data[incomeId][fieldName][itemIndex] = value;
                }
            } else if (key.startsWith('income')) {
                const match = key.match(/income\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.income_data[itemId]) finalFormData.income_data[itemId] = {};
                    finalFormData.income_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('expenditure')) {
                const match = key.match(/expenditure\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.expenditure_data[itemId]) finalFormData.expenditure_data[itemId] = {};
                    finalFormData.expenditure_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('assets')) {
                const match = key.match(/assets\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.assets_data[itemId]) finalFormData.assets_data[itemId] = {};
                    finalFormData.assets_data[itemId][fieldName] = value;
                }
            } else if (key.startsWith('liabilities')) {
                const match = key.match(/liabilities\[(\d+)\](\w+)/);
                if (match) {
                    const [_, itemId, fieldName] = match;
                    if (!finalFormData.liabilities_data[itemId]) finalFormData.liabilities_data[itemId] = {};
                    finalFormData.liabilities_data[itemId][fieldName] = value;
                }
            } else if (
                key.startsWith('project_description') ||
                key.startsWith('focus_area') ||
                key.startsWith('location') ||
                key.startsWith('period') ||
                key.startsWith('value') ||
                key.startsWith('beneficiaries') ||
                key.startsWith('partner_name')
                ) {
                const match = key.match(/(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemId] = match;
                    if (!finalFormData.project_data[itemId]) finalFormData.project_data[itemId] = {};
                    finalFormData.project_data[itemId][fieldName] = value;
                }
            } else if (
                key.startsWith('tracking_name') ||
                key.startsWith('description') ||
                key.startsWith('start_date') ||
                key.startsWith('end_date') ||
                key.startsWith('tracking_budget')
                ) {
                const match = key.match(/(\w+)\[(\d+)\]/);
                if (match) {
                    const [_, fieldName, itemId] = match;
                    if (!finalFormData.tracking_data[itemId]) finalFormData.tracking_data[itemId] = {};
                    finalFormData.tracking_data[itemId][fieldName] = value;
                }
            } else {
                // Handle static fields
                finalFormData[key] = value;
            }
        });

     // Append serialized JSON objects to FormData
        formData.append('groups_data', JSON.stringify(finalFormData.groups_data));
        formData.append('source_income_data', JSON.stringify(finalFormData.source_income_data));
        formData.append('income_data', JSON.stringify(finalFormData.income_data));
        formData.append('expenditure_data', JSON.stringify(finalFormData.expenditure_data));
        formData.append('assets_data', JSON.stringify(finalFormData.assets_data));
        formData.append('liabilities_data', JSON.stringify(finalFormData.liabilities_data));
        formData.append('project_data', JSON.stringify(finalFormData.project_data));
        formData.append('tracking_data', JSON.stringify(finalFormData.tracking_data));
        formData.append('tentative_payment_schedule', JSON.stringify(finalFormData.tentative_payment_schedule));

        // console.log('Final Form Data:', JSON.stringify(formData, null, 2));
        
        $.ajax({          
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false, // Important: Prevent jQuery from processing FormData
            contentType: false, // Important: Prevent jQuery from setting content type header
            success: function(response) {
                console.log(response);
                const itemData = JSON.parse(JSON.parse(response));
                console.log(itemData['status']);
                if (itemData['status'] == 'sucess') {
                    var lastId = itemData['LASTID']; 
                    jQuery.ajax({
                        url: "views/custom_view_csr_proposal_details.php",
                        data:'lastId='+lastId<?php if (!empty($rid)) { echo "+'&oldId='+" . json_encode($rid); } ?>,
                        type: "POST",
                        success:function(data){
                            $('#dynamic_popupxxl').html(data);
                            $('#kt_modal_dynamic_popupxxl').modal("show");
                        },
                        error:function (){}
                    });
                }
            },
            error: function() {
                alert('An error occurred while saving item request items. Please try again.');
            }
    });
});
</script>