<?php
class CSRClass
{
    function insertData($get){
        global $callConfig;
        $transaction_no = '';
        if ($get['old_id'] == '' || !$get['old_id']) {
            // Generate NEW dynamic serial number
            $whr = "application_table_name = 'csr_proposal_data'";
            $query = $callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS, '*', $whr, '', '', '');
            $query_res = $callConfig->getRow($query);
            $le = $query_res->doc_no_length;
            $var = "%0" . $le . "d";
            $transaction_no = $query_res->pre_fix . sprintf($var, $query_res->serial_number + 1) . $query_res->post_fix;
            if (isset($query_res->id)) {
                $update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS, array('serial_number' => $query_res->serial_number + 1), 'id', $query_res->id);
            }

        } else {
            // Get EXISTING transaction_no
            $q2 = "SELECT transaction_no FROM csr_proposal_data WHERE id = '" . $get['old_id'] . "'";
            $row2 = $callConfig->getRow($q2);
            $transaction_no = $row2->transaction_no;
        }

        if($get['old_id'] && $get['old_id'] != ''){
            $id = $get['old_id']; 
            $updated_date = date('Y-m-d h:i:s');
            $updated_by = $get['user_id'];

            $query = "UPDATE csr_proposal_data  SET is_active = 1 , updated_date='" . $updated_date . "', updated_by='" . $updated_by . "'  WHERE id= '". $id ."'";
            $res = $callConfig->executeQuery($query);
        }
        if ($get['rid'] != "") {
            $query = "
            INSERT INTO csr_proposal_data (
                company_id, date, financial_year, institue_name, subject, about_organisation, background, thematic_area, education, env_sustian,
                health, skill_development, comu_development, other_thrust_area, SDGproposal, methodology, target_group,
                team_project, monitoring_mechanism, outcomes_expected, execution_plan, impact_assessment, financial_implications,
                budget_grand_total, total_budget_grand_total, form_a, form_a_file, form_b, form_b_file, form_c, form_c_file,
                form_d, form_d_file, form_e, form_e_file, form_f, form_f_file, payment_schedule, payment_grand_total, payment_grand_total_in_words, sustainability_plan, conclusion, trust_deed, trust_deed_remark, incorporation, incorporation_remark, csr1, csr1_remark, form12a, form12a_remark, form80g, form80g_remark, form35ac, form35ac_remark, pan, pan_remark, financial_statements, financial_statements_remark, proposal_remark,
                fcra, fcra_remark, tds_exemption, tds_exemption_remark, gst, gst_remark, reference, reference_remark,
                land_ownership, land_ownership_remark, property_card, property_card_remark, drawings, drawings_remark,
                govt_drawings, govt_drawings_remark, basis_estimate, basis_estimate_remark, stability_certificate,
                stability_certificate_remark, execution_works_plan, execution_works_plan_remark, address_registered_office,
                branches_organisation, registered_as, years_of_existence, geographical_area_of_work, expertise_areas,
                board_members, affiliated_political_religious, sources_of_funding, awards_recognition, government_funds_programs,
                projects_sustainability, blacklisted_details, empanelled_details, associated_csr_projects, total_turnover,
                staff_strength, past_projects_bpcl, staff_qualification_experience, references_past_funders,
                filled_all_information, title_of_project, formd_project_description, executing_organisation,
                organisation_address, contact_person, contact_details, organisation_experience, execution_place,
                place_reason, project_tenure, project_cost, funds_infisuite, funds_other_sources, project_objective,
                reason_infisuite, project_benefits, quantitative_1, quantitative_2, quantitative_3, quantitative_4,
                quantitative_5, qualitative_1, qualitative_2, qualitative_3, approval_requirement, similar_projects,
                bank_account_infisuite, information_true, signature, name, designation, organisation_seal, total_fy1,
                total_fy2, total_fy3, groups_data, source_income_data, tentative_payment_schedule, income_data, expenditure_data, assets_data,
                liabilities_data, project_data, tracking_data, proposal, is_active, current_status, status_date, from_date,
                to_date, created_by, created_date, updated_by, updated_date,incorporation_file, csr1_file, form12a_file, form80g_file, form35ac_file, pan_file, financial_statements_file, proposal_file, fcra_file, tds_exemption_file, gst_file, reference_file, land_ownership_file, trust_deed_file, drawings_file, govt_drawings_file, basis_estimate_file, stability_certificate_file, execution_works_plan_file, orgininaor, property_card_file, information_true_file, project_start_date, project_end_date,type_of_work, type_of_project, working_area,csr_location, transaction_no
                )
            SELECT
            company_id, date, financial_year, institue_name, subject, about_organisation, background, thematic_area, education, env_sustian,
            health, skill_development, comu_development, other_thrust_area, SDGproposal, methodology, target_group,
            team_project, monitoring_mechanism, outcomes_expected, execution_plan, impact_assessment, financial_implications,
            budget_grand_total, total_budget_grand_total, form_a, form_a_file, form_b, form_b_file, form_c, form_c_file,
            form_d, form_d_file, form_e, form_e_file, form_f, form_f_file, payment_schedule, payment_grand_total, payment_grand_total_in_words, sustainability_plan, conclusion, trust_deed, trust_deed_remark, incorporation, incorporation_remark, csr1, csr1_remark, form12a, form12a_remark, form80g, form80g_remark, form35ac, form35ac_remark, pan, pan_remark, financial_statements, financial_statements_remark, proposal_remark,
            fcra, fcra_remark, tds_exemption, tds_exemption_remark, gst, gst_remark, reference, reference_remark,
            land_ownership, land_ownership_remark, property_card, property_card_remark, drawings, drawings_remark,
            govt_drawings, govt_drawings_remark, basis_estimate, basis_estimate_remark, stability_certificate,
            stability_certificate_remark, execution_works_plan, execution_works_plan_remark, address_registered_office,
            branches_organisation, registered_as, years_of_existence, geographical_area_of_work, expertise_areas,
            board_members, affiliated_political_religious, sources_of_funding, awards_recognition, government_funds_programs,
            projects_sustainability, blacklisted_details, empanelled_details, associated_csr_projects, total_turnover,
            staff_strength, past_projects_bpcl, staff_qualification_experience, references_past_funders,
            filled_all_information, title_of_project, formd_project_description, executing_organisation,
            organisation_address, contact_person, contact_details, organisation_experience, execution_place,
            place_reason, project_tenure, project_cost, funds_infisuite, funds_other_sources, project_objective,
            reason_infisuite, project_benefits, quantitative_1, quantitative_2, quantitative_3, quantitative_4,
            quantitative_5, qualitative_1, qualitative_2, qualitative_3, approval_requirement, similar_projects,
            bank_account_infisuite, information_true, signature, name, designation, organisation_seal, total_fy1,
            total_fy2, total_fy3, groups_data, source_income_data, tentative_payment_schedule, income_data, expenditure_data, assets_data,
            liabilities_data, project_data, tracking_data, proposal, is_active, current_status, status_date, from_date,
            to_date, created_by, created_date, updated_by, updated_date,incorporation_file, csr1_file, form12a_file, form80g_file, form35ac_file, pan_file, financial_statements_file, proposal_file, fcra_file, tds_exemption_file, gst_file, reference_file, land_ownership_file, trust_deed_file, drawings_file, govt_drawings_file, basis_estimate_file, stability_certificate_file, execution_works_plan_file, orgininaor, property_card_file, information_true_file, project_start_date, project_end_date,type_of_work, type_of_project, working_area,csr_location, '" . $transaction_no . "'
            FROM temp_csr_proposal_data
            WHERE id = '" . $get['rid'] . "';
            ";

            $res = $callConfig->executeQuery($query);
            return $res;
        }
    }

    function getCompletedProjects($get){
        global $callConfig;
        $query = "SELECT DISTINCT c.proposal_id,  p.subject, p.date, p.total_budget_grand_total, p.name,
        (SELECT COUNT(*) 
            FROM csr_project_tracking 
            WHERE proposal_id = c.proposal_id) AS proposal_count
        FROM csr_project_tracking c
        JOIN csr_proposal_data p ON p.id = c.proposal_id
        WHERE p.tracking_status = '0' AND p.company_id='" . $get['company_id'] . "' 
        AND c.proposal_id IN (
        SELECT proposal_id
        FROM csr_project_tracking
        GROUP BY proposal_id
        HAVING COUNT(*) = SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END)
    )";
    $res = $callConfig->getAllRows($query);
    return $res;
}

function getTracckingPerentage($get)
{
    global $callConfig;
    $rid = $get['id'];
    $query = "SELECT 
    COUNT(*) AS total_rows, 
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count
    FROM csr_project_tracking
    WHERE proposal_id=" . $rid . " AND company_id='" . $get['company_id'] . "'";
    $res = $callConfig->getRow($query);
    return $res;
}

function getCommitteMembers($get)
{
    global $callConfig;
    $query = "SELECT au.id, au.display_name, au.user_email FROM application_user_module_roles aumr LEFT JOIN application_users au ON au.id = aumr.user_id 
    WHERE aumr.role_short_code='CSR_COMMITTEE_MEMBER'
    AND aumr.company_id='" . $get['company_id'] . "' 
    AND aumr.is_active='0'";
    $res = $callConfig->getAllRows($query);
    return $res;
}
// function getCsrData($get)
// {
//     global $callConfig;
//     $id = $get['id'];
//     $table = $get['table'];
//     $query = "SELECT cpd.* FROM transaction_approval_logs tal 
//     LEFT JOIN " . $table . " cpd ON cpd.id = tal.transaction_id 
//     WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='" . $table . "'
//     AND tal.company_id='" . $get['company_id'] . "' 
//     AND cpd.is_active='0'";
//     $res = $callConfig->getAllRows($query);
//     return $res;
// }
//changing this function

    function getCsrData($get){
        global $callConfig;
        $return = array();
        $id = $get['id'] ?? '';
        $current_status = $get['current_status'] ?? '';
        $table = $get['table'] ?? '';

        $query = "SELECT cpd.id, cpd.date, cpd.project_start_date, cpd.project_end_date, cpd.subject, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        AND tal.company_id='" . $get['company_id'] . "' 
        AND cpd.is_active='0' ORDER BY cpd.id DESC";
        $total_records_query = $callConfig->getAllRows($query);
        $total_records = array();
        if (!empty($total_records_query)) {
            foreach ($total_records_query as $row) {
                $total_records[] = $row;
            }
        }
        $return['total_records'] = $total_records;

        // changed by koti
        // $pending_approval_query = "SELECT cpd.id, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        // LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        // WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        // AND (tal.response_type = 0 OR tal.response_type IS NULL) AND tal.previous_status = cpd.current_status
        // AND tal.company_id='" . $get['company_id'] . "' 
        // AND cpd.is_active='0' AND cpd.current_status='" . $current_status . "'";
        $pending_approval_query = "SELECT cpd.id, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        AND (tal.response_type = 0 OR tal.response_type IS NULL) 
        AND tal.company_id='" . $get['company_id'] . "' 
        AND cpd.is_active='0' AND cpd.current_status='" . $current_status . "' ORDER BY cpd.id DESC";

        // $return['pending_approval_query'] = $pending_approval_query;

        $pending_approval_query = $callConfig->getAllRows($pending_approval_query);
        $pending_approvals = array();
        if (!empty($pending_approval_query)) {
            foreach ($pending_approval_query as $row) {
                $pending_approvals[] = $row;
            }
        }

        $return['pending_approvals'] = $pending_approvals;

        $approved_by_me_query = "SELECT cpd.id, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        AND tal.response_type != 0
        AND tal.company_id='" . $get['company_id'] . "' 
        AND cpd.is_active='0'  ORDER BY cpd.id DESC";
        $approved_by_me_query = $callConfig->getAllRows($approved_by_me_query);
        $approved_by_me = array();
        if (!empty($approved_by_me_query)) {
            foreach ($approved_by_me_query as $row) {
                $approved_by_me[] = $row;
            }
        }
        $return['approved_by_me'] = $approved_by_me;

        $approval_inprogress_query = "SELECT cpd.id, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        AND tal.previous_status < cpd.current_status
        AND tal.company_id='" . $get['company_id'] . "' 
        AND cpd.is_active='0' AND cpd.current_status NOT IN ('199', '30', '20', '0')  ORDER BY cpd.id DESC";
        $approval_inprogress_query = $callConfig->getAllRows($approval_inprogress_query);
        $approval_inprogress = array();
        if (!empty($approval_inprogress_query)) {
            foreach ($approval_inprogress_query as $row) {
                $approval_inprogress[] = $row;
            }
        }
        $return['approval_inprogress'] = $approval_inprogress;

        $approved_query = "SELECT cpd.id, cpd.date, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal 
        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'
        
        AND tal.company_id='" . $get['company_id'] . "' 
        AND cpd.is_active='0' AND cpd.current_status = '199' ORDER BY cpd.id DESC";
        $approved_query = $callConfig->getAllRows($approved_query);
        $approved = array();
        if (!empty($approved_query)) {
            foreach ($approved_query as $row) {
                $approved[] = $row;
            }
        }
        $return['approved'] = $approved;

        // traking queries
        $tracking_pending_query = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total,current_status, tracking_status from csr_proposal_data  
            WHERE tracking_status=0 AND id in (
            SELECT cpt.proposal_id  FROM transaction_approval_logs tal 
            LEFT JOIN csr_project_tracking cpt ON cpt.id = tal.transaction_id 
            WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_project_tracking'
            AND tal.response_type = 0 AND tal.previous_status = cpt.current_status
            AND tal.company_id='" . $get['company_id'] . "' 
            AND cpt.is_active='0')  ORDER BY id DESC";


        $tracking_pending_query = $callConfig->getAllRows($tracking_pending_query);
        $tracking_pending = array();
        if (!empty($tracking_pending_query)) {
            foreach ($tracking_pending_query as $row) {
                $tracking_pending[] = $row;
            }
        }
        $return['tracking_pending'] = $tracking_pending;

        $tracking_inprogress_query = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total,current_status, tracking_status from csr_proposal_data  
            WHERE id in (
            SELECT cpt.proposal_id  FROM transaction_approval_logs tal 
            LEFT JOIN csr_project_tracking cpt ON cpt.id = tal.transaction_id 
            WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_project_tracking'
            AND tal.previous_status < cpt.current_status
            AND tal.company_id='" . $get['company_id'] . "' 
            AND cpt.is_active='0') AND tracking_status = 0  ORDER BY id DESC";

        $tracking_inprogress_query = $callConfig->getAllRows($tracking_inprogress_query);
        $tracking_inprogress = array();
        if (!empty($tracking_inprogress_query)) {
            foreach ($tracking_inprogress_query as $row) {
                $tracking_inprogress[] = $row;
            }
        }

        $return['tracking_inprogress'] = $tracking_inprogress;

        $tracking_completed_query = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status from csr_proposal_data  
            WHERE tracking_status=1 AND id in (
            SELECT tal.transaction_id   FROM transaction_approval_logs tal 
            WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data'

            AND tal.company_id='" . $get['company_id'] . "')  ORDER BY id DESC";


        $tracking_completed_query = $callConfig->getAllRows($tracking_completed_query);
        $tracking_completed = array();
        if (!empty($tracking_completed_query)) {
            foreach ($tracking_completed_query as $row) {
                $tracking_completed[] = $row;
            }
        }
        $return['tracking_completed'] = $tracking_completed;

        //newly added
        $tracking_percentage_query = "SELECT proposal_id, 
                   ROUND((SUM(IF(`status` = 'completed', 1, 0)) / COUNT(id)) * 100) AS percent
            FROM csr_project_tracking 
            WHERE company_id = '" . $get['company_id'] . "' 
            AND proposal_id IN (
                SELECT cpd.id 
                FROM csr_proposal_data cpd 
                INNER JOIN transaction_approval_logs tal ON cpd.id = tal.transaction_id
                WHERE tal.user_id = '" . $id . "' 
                AND cpd.company_id = '" . $get['company_id'] . "'  
                AND cpd.is_active = 0
            ) 
            AND is_active = 0 
            GROUP BY proposal_id  ORDER BY id DESC";

        $tracking_percentage_query = $callConfig->getAllRows($tracking_percentage_query);
        $tracking_percentage = array();
        if (!empty($tracking_percentage_query)) {
            foreach ($tracking_percentage_query as $row) {
                $tracking_percentage[$row->proposal_id] = $row->percent;
            }
        }
        $return['tracking_percentage'] = $tracking_percentage;

        return $return;
    }
function getApprovedMembers($get)
{
    global $callConfig;
    $id = $get['id'];
    $table = $get['table'];
    $query = "SELECT au.id, au.display_name, au.user_email FROM transaction_approval_logs tal
    LEFT JOIN application_users au ON au.id = tal.user_id 
    WHERE tal.transaction_id = '" . $id . "' AND tal.transaction_table='" . $table . "'
    AND tal.company_id='" . $get['company_id'] . "' 
    AND au.is_active='0'";
    $res = $callConfig->getAllRows($query);
    return $res;
}

function getCsrSetting($get)
{
    global $callConfig;
    $id = $get['id'];
    $table = $get['table'];
    $query = "SELECT * FROM  csr_setting WHERE company_id='" . $get['company_id'] . "' AND is_active='0'";
    $res = $callConfig->getRow($query);
    return $res;
}


   // MAP Point 
function getCompaniesData($get)
{
    global $callConfig;
    $return = array();

    $countries_details = "SELECT id, country_name FROM  countries WHERE  company_id='" . $get['company_id'] . "' 
    AND  is_active =0 ";
    $countries_details = $callConfig->getAllRows($countries_details);
    $ct_details = array();
    $i = 1;
    foreach ($countries_details as $ct) {
        $ct_details[$i] = $ct;
        $i++;
    }
    $return['ct_details'] = $ct_details;

    $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE company_id='" . $get['company_id'] . "' 
    AND is_active =0";
    $application_users_details = $callConfig->getAllRows($application_users_details);
    $application_users = array();
    $i = 1;
    foreach ($application_users_details as $agd) {
        $application_users[$i] = $agd;
        $i++;
    }
    $return['application_users'] = $application_users;

    $currencies_details = "SELECT id, concat(currency_short_code,'-', currency_name) as currency FROM  currencies WHERE  company_id='" . $get['company_id'] . "' 
    AND is_active = 0 ";
    $currencies_details = $callConfig->getAllRows($currencies_details);
    $cr_details = array();
    $i = 1;
    foreach ($currencies_details as $st) {
        $cr_details[$i] = $st;
        $i++;
    }
    $return['cr_details'] = $cr_details;

    $companies_details = "SELECT id, name  FROM  companies WHERE  company_id='" . $get['company_id'] . "' AND is_active =0 ";
    $companies_details = $callConfig->getAllRows($companies_details);
    $cmp_details = array();
    $i = 1;
    foreach ($companies_details as $st) {
        $cmp_details[$i] = $st;
        $i++;
    }
    $return['cmp_details'] = $cmp_details;

    return $return;
}

function getMapPoints($get){
    global $callConfig;
    $return = array();
    $userid = $get['userid'];
    $map_date = $get['map_date'];
    $user_field_work_type = $get['user_field_work_type'];
    if ($user_field_work_type == '1') {
        $query1 = "SELECT id, user_id, plan_date, plan_longitude as lng, plan_latitude as lat, plan_location as name From field_work_daily_plans Where user_id='" . $userid . "' AND plan_date='" . $map_date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
            // return $query1;
        $query1 = $callConfig->getAllRows($query1);
        $assignedLocations  = array();
        $i = 1;
        if (count((array)$query1) > 0) {
            foreach ($query1 as $row) {
                $assignedLocations[$i] = $row;
                $i++;
            }
        }
        $return['assignedLocations'] = $assignedLocations;
    }

    $query2 = "SELECT id, user_id, map_latitude as lat, map_longitude as lng, map_date, map_title as name, map_time, field_work_daily_plan_id From field_work_user_map_points Where user_id='" . $userid . "' AND map_date='" . $map_date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
    $query2 = $callConfig->getAllRows($query2);
    $visitedLocations   = array();
    $i = 1;
    if (count((array)$query2) > 0) {
        foreach ($query2 as $row) {
            $visitedLocations[$i] = $row;
            $i++;
        }
    }
    $return['visitedLocations'] = $visitedLocations;
    return $return;
}

function getFieldWorkDailyPlanData($get){
    global $callConfig;
    $return = array();
    $from_date = $get['from_date'];
    $to_date = $get['to_date'];
    $userids_string = $get['userids'];
    $managerids_string = $get['manager_ids'];
    $userids_array = array();
    if ($userids_string != '') {
        $userids_array = explode(',', $userids_string);
    }
    if (count((array)$userids_array) > 0) {
        $filtered_userids = array();
        foreach ($userids_array as $userid) {
            if ($userid != '' && $userid != '0' && is_numeric($userid)) {
                $filtered_userids[] = (int)$userid;
            }
        }
        $userids_array = $filtered_userids;
    }

    if($managerids_string && empty($userids_string)){
        $userids_array = array();
        $query="SELECT id, display_name FROM application_users WHERE reports_to in (".$managerids_string.") AND company_id='".$get['company_id']. "' AND is_active =0";
        $result = $callConfig->getAllRows($query);
        if ($result) {
            foreach ($result as $row) {
                if ($row->id != '' && $row->id != '0' && is_numeric($row->id)) {
                    $userids_array[] = (int)$row->id;
                }
            }
        }
    }

    if (count((array)$userids_array) == 0) {
        return $return;
    }

    $ids = implode(',', $userids_array);
    $company_id = $get['company_id'];

    $query1 = "SELECT fwdp.user_id AS id, au.display_name AS display_name, plan_location AS location, plan_latitude AS lat, plan_longitude AS lng, plan_date AS from_date, '' AS to_date
        FROM field_work_daily_plans fwdp
        JOIN application_users au ON au.id = fwdp.user_id
        WHERE fwdp.user_id IN (".$ids.")
        AND au.user_field_work_type IN ('1','2')
        AND fwdp.plan_date BETWEEN '".$from_date."' AND '".$to_date."'
        AND fwdp.company_id='".$company_id."'
        AND fwdp.is_active = 0
        GROUP BY fwdp.user_id, fwdp.plan_date";
    $result1 = $callConfig->getAllRows($query1);
    if ($result1 && count((array)$result1) > 0) {
        foreach ($result1 as $row) {
            $return[] = $row;
        }
    }

    $query2 = "SELECT ufwd.user_id AS id, au.display_name AS display_name, location_name AS location, work_latitude AS lat, work_longitude AS lng, work_from_date AS from_date, work_to_date AS to_date
        FROM user_field_work_details ufwd
        JOIN application_users au ON au.id = ufwd.user_id
        WHERE ufwd.user_id IN (".$ids.")
        AND au.user_field_work_type = '3'
        AND ufwd.company_id = '".$company_id."'
        AND ufwd.is_active = 0
        AND (
            ('".$from_date."' BETWEEN work_from_date AND work_to_date) OR
            ('".$to_date."' BETWEEN work_from_date AND work_to_date) OR
            (work_from_date BETWEEN '".$from_date."' AND '".$to_date."') OR
            (work_to_date BETWEEN '".$from_date."' AND '".$to_date."')
        )";
    $result2 = $callConfig->getAllRows($query2);
    if ($result2 && count((array)$result2) > 0) {
        foreach ($result2 as $row) {
            $return[] = $row;
        }
    }

    return $return;
}

function fetchFieldWorkDailyPlans($get)
{
    $table = $get['table'];
    $userid = $get['userid'];
    $date = $get['date'];
    global $callConfig;
    $return = array();
    if ($table == 'field_work_daily_plans') {
        $query = "SELECT *  FROM field_work_daily_plans where user_id='" . $userid . "' AND plan_date= '" . $date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
        $result = $callConfig->getAllRows($query);
    } else {
        $query = "SELECT * FROM user_field_work_details WHERE user_id='" . $userid . "' AND  '" . $date . "' BETWEEN work_from_date AND work_to_date AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
        $result = $callConfig->getRow($query);
    }
    if ($result) {
        $return['qeury_reslut'] = $result;
    }
    return $return;
}
function updateFieldWorkDailyPlans($get)
{
    global $callConfig;
    $rid = $get['rid'];
    $date = $get['date'];
    $table = $get['table'];
    $updated_date = date('Y-m-d h:i:s');
    $updated_by = $get['user_id'];
    if ($table == 'field_work_daily_plans') {
        $query = "UPDATE " . $table . "  SET is_active = NULL , updated_date='" . $updated_date . "', updated_by='" . $updated_by . "'  WHERE user_id= '" . $rid . "' AND plan_date = '" . $date . "'";
    } else {
        $query = "UPDATE " . $table . "  SET is_active = NULL , updated_date='" . $updated_date . "', updated_by='" . $updated_by . "'  WHERE id = '" . $rid . "'";
    }

    $res = $callConfig->executeQuery($query);
    return $res;
}

function getApplicationUsersData($get)
{
    global $callConfig;
    $return = array();
    $admin_type = $get['admin_type'];

    $whr = "";
    if($admin_type == '2' || $admin_type == '1'){
        $whr = "reports_to ='". $get['user_id']."' AND ";
    }elseif($admin_type == '3'){
        $whr ="id = '".$get['user_id']."' AND ";
    }
    $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE company_id='" . $get['company_id'] . "' AND  ".$whr." is_active =0";
    $application_users_details = $callConfig->getAllRows($application_users_details);
    $application_users = array();
    $i = 1;
    foreach ($application_users_details as $agd) {
        $application_users[$i] = $agd;
        $i++;
    }
    $return['application_users'] = $application_users;

    return $return;
}

function getApplicationUsersDataforInsert($get)
{
    global $callConfig;
    $return = array();
    $admin_type = $get['admin_type'];

    $whr = "";
    if($admin_type == '2'){
        $whr = "reports_to ='". $get['user_id']."' AND ";
    }elseif($admin_type == '3'){
        $whr ="id = '".$get['user_id']."' AND ";
    }
    $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE company_id='" . $get['company_id'] . "' AND  ".$whr." is_active =0";
    //echo   $application_users_details;
    $application_users_details = $callConfig->getAllRows($application_users_details);
    $application_users = array();
    $i = 1;
    foreach ($application_users_details as $agd) {
        $application_users[$i] = $agd;
        $i++;
    }
    $return['application_users'] = $application_users;

    return $return;
}

function getFieldWorkUserMapPointsData($get){
        global $callConfig;
        $return = array();
        $from_date = $get['from_date'];
        $to_date = $get['to_date'];
        $userids_string = $get['userids'];
        $userids_array = explode(',', $userids_string);

        foreach ($userids_array as $userid) {
            $query = "SELECT id,  user_field_work_type FROM application_users where id='" . $userid . "'";
            $res = $callConfig->getRow($query);
            if ($res->user_field_work_type == 1) {
                $query1 = "SELECT au.id, au.display_name, au.user_email,  au.user_field_work_type, fwump.map_date AS map_date,
                  count(DISTINCT(fwump.id)) AS vall,
                  SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0))  AS visited,
                  count(DISTINCT(fwump.id)) - SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0)) AS extra,
                  count(DISTINCT(fwdp.id)) - SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0)) AS pending
                FROM 
                    field_work_user_map_points fwump
                LEFT JOIN application_users au ON fwump.user_id = au.id
                LEFT JOIN 
                    field_work_daily_plans fwdp 
                    ON fwdp.user_id = fwump.user_id 
                    AND fwdp.plan_date = fwump.map_date 
                    AND fwdp.company_id = fwump.company_id 
                    AND fwdp.is_active = '0'
                WHERE 
                    fwump.user_id = '" . $userid . "'
                    AND fwump.map_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    AND fwump.company_id = '" . $get['company_id'] . "'
                    AND fwump.is_active = '0'
                GROUP BY 
                    fwump.user_id, fwump.map_date
                ORDER BY 
                    fwump.map_date";
            } else {
                $query1 = "SELECT au.id, au.display_name, au.user_email,  au.user_field_work_type, fwump.map_date map_date, 
                COUNT(fwump.map_date) visited, '0' as extra, '0' as pending
                FROM     application_users au
                LEFT JOIN     field_work_user_map_points fwump ON  fwump.user_id = au.id
                WHERE au.id = '" . $userid . "' AND  fwump.map_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    AND fwump.company_id = '" . $get['company_id'] . "'  AND fwump.is_active = '0'
                GROUP BY  au.display_name,  au.user_email, au.user_field_work_type, fwump.map_date";
            }
            //echo $query1;
            $result = $callConfig->getAllRows($query1);
            if ($result) {
                foreach ($result as $row) {
                    $return[] = $row;
                }
            }
        }
        return $return;
    }
function getUsersMapPointsData($get){
    global $callConfig;
    $date = date('Y-m-d');
    $return = array();
    $query = "SELECT t1.id, t1.user_id,au.display_name, t1.map_date, t1.map_latitude as lat, t1.map_longitude as lng, t1.map_time
    FROM field_work_user_map_points t1
    JOIN (
        SELECT user_id, MAX(id) AS max_id
        FROM field_work_user_map_points
        WHERE map_date = '".$date."' AND company_id='".$get['company_id']."' AND is_active='0'
        GROUP BY user_id, map_date
        ) t2
        ON t1.user_id = t2.user_id AND t1.id = t2.max_id
        JOIN application_users au ON au.id =  t1.user_id AND au.user_field_work_type in('1','2')";
        $result = $callConfig->getAllRows($query);
        if ($result) {
            foreach ($result as $row) {
                $return[] = $row;
            }
        }
        return $return;
    }
    function getEmployeeData($get){
        global $callConfig;
        $return = array();
        $userids_string = $get['userids'];
        $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE reports_to in (".$userids_string.") AND company_id='" . $get['company_id'] . "' AND  is_active =0";
        // echo $application_users_details;
        $application_users_details = $callConfig->getAllRows($application_users_details);
        $return['qeury_reslut'] = $application_users_details;
        return $return;
    }

    function checkExistingData($get){
        global $callConfig;
        $return = array();
        $userid = $get['userid'];
        $date = $get['date'];
        $user_field_work_type = $get['user_field_work_type'];
        $query1 = "SELECT user_id,  plan_date, plan_longitude as lng, plan_latitude as lat , plan_location as name  From field_work_daily_plans Where user_id='" . $userid . "' AND plan_date='" . $date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
        $query1 = $callConfig->getAllRows($query1);
        $assignedLocations  = array();
        $i = 1;
        foreach ($query1 as $row) {
            $assignedLocations[$i] = $row;
            $i++;
        }
        $return['assignedLocations'] = $assignedLocations;
        return $return;
    }

    // adding new function
    function getCsrProjectTracking($get){
        global $callConfig;
        $return = array();
        $proposal_id = $get['rid'];
        $query1 = "SELECT * FROM csr_project_tracking WHERE status IN ('started', 'hold', 'completed') AND proposal_id = '{$proposal_id}' GROUP BY proposal_id";
        $query1 = $callConfig->getRow($query1);
        $return['inprogress'] = $query1;

        $query2 = "SELECT * FROM csr_project_tracking WHERE (status = '' or status IS NULL) AND proposal_id = '{$proposal_id}' GROUP BY proposal_id";
        $query2 = $callConfig->getRow($query2);
        $return['pending'] = $query2;

        return $return;
    }

    function getcsrProposalData($get)
    {
        global $callConfig;
        $return = array();
        $orgininaor = $get['orgininaor'];

        $query = "SELECT * FROM  csr_proposal_data where orgininaor = '" . $orgininaor . "' AND company_id = '" . $get['company_id'] . "' AND is_active=0 ORDER BY id DESC";
        $query_res = $callConfig->getAllRows($query);
        $csr_proposal_arr = array();
        foreach ($query_res as $row) {
            $csr_proposal_arr[] = $row;
        }
        $return['csr_proposal_arr'] = $csr_proposal_arr;

        $query1 = "SELECT proposal_id, 
        ROUND((sum(if(`status`='completed',1,0))/count(id))*100) as percent 
        FROM csr_project_tracking WHERE company_id = '" . $get['company_id'] . "' AND 
        proposal_id IN (SELECT id FROM csr_proposal_data WHERE company_id = '" . $get['company_id'] . "' AND orgininaor ='" . $orgininaor . "' AND is_active = 0) 
        AND is_active=0 GROUP BY proposal_id";

        $query1_res = $callConfig->getAllRows($query1);
        $tracking_percent_arr = array();
        foreach ($query1_res as $row) {
            $tracking_percent_arr[$row->proposal_id] = $row->percent;
        }
        $return['tracking_percent_arr'] = $tracking_percent_arr;

        $query2 = "SELECT DISTINCT proposal_id 
            FROM csr_project_tracking 
            WHERE status IN ('started', 'hold', 'completed') AND
            proposal_id IN (SELECT id FROM csr_proposal_data WHERE company_id = '" . $get['company_id'] . "' AND orgininaor = '" . $orgininaor . "' AND is_active = 0 AND current_status = 199  AND tracking_status=0)  ORDER BY proposal_id DESC";
        $query2_res = $callConfig->getAllRows($query2);
        $tracking_inprogress = array();
        foreach ($query2_res as $row) {
            $tracking_inprogress[] = $row->proposal_id;
        }
        $return['tracking_inprogress'] = $tracking_inprogress;

        // $query3 = "SELECT DISTINCT proposal_id 
        //     FROM csr_project_tracking 
        //     WHERE (status = '' OR status IS NULL) AND proposal_id IN (SELECT id FROM csr_proposal_data WHERE company_id = '" . $get['company_id'] . "' AND orgininaor = '" . $orgininaor . "' AND is_active = 0 AND current_status = 199  AND tracking_status=0)";
        $query3 = "SELECT id AS proposal_id FROM csr_proposal_data
        WHERE company_id = '" . $get['company_id'] . "'
            AND orgininaor = '" . $orgininaor . "'
            AND is_active = 0
            AND current_status = 199
            AND tracking_status = 0
            AND id NOT IN(
            SELECT DISTINCT proposal_id
            FROM csr_project_tracking
            WHERE (
                    STATUS != ''
                    OR STATUS IS NOT NULL
                    ) ) ORDER BY proposal_id DESC";
        $return['query3'] = $query3;
                
        $query3_res = $callConfig->getAllRows($query3);
        $tracking_pending = array();
        foreach ($query3_res as $row) {
            $tracking_pending[] = $row->proposal_id;
        }
        $return['tracking_pending'] = $tracking_pending;

        return $return;
    }

    function getAdminDashBordData($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM  csr_proposal_data where  company_id = '" . $get['company_id'] . "' AND is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $csr_proposal_arr = array();
        foreach ($query_res as $row) {
            $csr_proposal_arr[] = $row;
        }
        $return['csr_proposal_arr'] = $csr_proposal_arr;

        $query1 = "SELECT proposal_id, 
        ROUND((sum(if(`status`='completed',1,0))/count(id))*100) as percent 
        FROM csr_project_tracking WHERE company_id = '" . $get['company_id'] . "' AND 
        proposal_id IN (SELECT id FROM csr_proposal_data WHERE company_id = '" . $get['company_id'] . "'  AND is_active = 0) 
        AND is_active=0 GROUP BY proposal_id";


        $query1_res = $callConfig->getAllRows($query1);
        $tracking_percent_arr = array();
        foreach ($query1_res as $row) {
            $tracking_percent_arr[$row->proposal_id] = $row->percent;
        }
        $return['tracking_percent_arr'] = $tracking_percent_arr;

        // $query2 = "SELECT au.id, au.display_name, au.user_email FROM application_user_module_roles aumr LEFT JOIN application_users au ON au.id = aumr.user_id 
        // WHERE aumr.role_short_code='CSR_COMMITTEE_MEMBER'
        // AND aumr.company_id='" . $get['company_id'] . "' 
        // AND aumr.is_active='0'";

        // $query2_res = $callConfig->getAllRows($query2);
        // $users_arr = array();
        // foreach ($query2_res as $row) {
        //     $users_arr[] = $row;
        // }
        // $return['users_arr'] = $users_arr;
        $query2 = "SELECT au.id, au.display_name, au.user_email, aumr.role_short_code FROM application_user_module_roles aumr LEFT JOIN application_users au ON au.id = aumr.user_id 
        WHERE aumr.role_short_code IN ('CSR_CREATER', 'CSR_CREATOR', 'CSR_COMMITTEE_GROUP', 'CSR_COMMITTEE_MEMBER', 'CSR_COMMITTEE_HEAD')
        AND aumr.company_id='" . $get['company_id'] . "' 
        AND aumr.is_active='0'";

        $query2_res = $callConfig->getAllRows($query2);
        $csr_users_by_role = array(
            'CSR_CREATER' => array(),
            'CSR_COMMITTEE_GROUP' => array(),
            'CSR_COMMITTEE_MEMBER' => array(),
            'CSR_COMMITTEE_HEAD' => array(),
        );
        $csr_users_seen = array(
            'CSR_CREATER' => array(),
            'CSR_COMMITTEE_GROUP' => array(),
            'CSR_COMMITTEE_MEMBER' => array(),
            'CSR_COMMITTEE_HEAD' => array(),
        );
        foreach ($query2_res as $row) {
            $role_key = $row->role_short_code;
            if ($role_key == 'CSR_CREATOR') {
                $role_key = 'CSR_CREATER';
            }
            if (!isset($csr_users_by_role[$role_key])) {
                continue;
            }
            $user_id = $row->id;
            if (in_array($user_id, $csr_users_seen[$role_key])) {
                continue;
            }
            $csr_users_seen[$role_key][] = $user_id;
            $csr_users_by_role[$role_key][] = $row;
        }
        $return['csr_users_by_role'] = $csr_users_by_role;
        $return['users_arr'] = $csr_users_by_role['CSR_COMMITTEE_MEMBER'];

        $query = "SELECT id, proposal_id, start_date, end_date, tracking_budget, status FROM  csr_project_tracking where  company_id = '" . $get['company_id'] . "' AND is_active=0 AND proposal_id IN (SELECT id FROM csr_proposal_data WHERE company_id = '" . $get['company_id'] . "'  AND is_active = 0 AND current_status = 199 AND tracking_status = 0) ";
        $budget_tracking_res = $callConfig->getAllRows($query);

        $groupedArray = array(); 

        foreach ($budget_tracking_res as $row) {
            $id = $row->proposal_id; 

            if (!isset($groupedArray[$id])) {
                $groupedArray[$id] = [];
            }
            $groupedArray[$id][] = $row;
        }
        $return['grouped_array'] = $groupedArray;

        return $return;
    }
    function getCSRHistory($get){
        global $callConfig;
        $return = array();
        $from_date = $get['from_date'];
        $to_date = $get['to_date'];

        $query1 = "SELECT  id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM  csr_proposal_data
        where project_start_date >= '".$from_date."'   AND project_start_date <= '".$to_date."' AND company_id = '" . $get['company_id'] . "' AND is_active=0";
        // $return['query'] = $query;
        $query_res = $callConfig->getAllRows($query1);
        $csr_history = array();
        foreach ($query_res as $row) {
            $csr_history[] = $row;
        }
        $return['csr_history'] = $csr_history;
        return $return;
        
    }
    function getCSRYearlyBudget($get){
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
       
        if(isset($rid) && $rid != ''){            
            $query1 = "SELECT * FROM  csr_yearly_budget
            where company_id = '" . $get['company_id'] . "' AND id = ".$rid." AND is_active=0";
            // $return['query'] = $query1;
            $query_res = $callConfig->getRow($query1);
            $csr_yearly_budget = array();
            if(!empty($query_res)){
                $csr_yearly_budget = $query_res;
            }
            $return['csr_yearly_budget'] = $csr_yearly_budget;

            $query2 = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM `csr_proposal_data` WHERE project_start_date like '%$query_res->base_year%'
             AND company_id = '" . $get['company_id'] . "' AND is_active=0 ";
            $return['query2'] = $query2;
           

            $query2_res = $callConfig->getAllRows($query2);
            $csr_proposal_arr = array();

            if(!empty($query2_res)){
                foreach ($query2_res as $row) {
                    $csr_proposal_arr[] = $row;
                }    
            }
            $return['csr_proposal_arr'] = $csr_proposal_arr;

        }else{
            $query1 = "SELECT * FROM  csr_yearly_budget
            where company_id = '" . $get['company_id'] . "' AND is_active=0";
            // $return['query'] = $query;
            $query_res = $callConfig->getAllRows($query1);
            $csr_yearly_budget = array();
            if(!empty($query_res)){
                foreach ($query_res as $row) {
                    $csr_yearly_budget[$row->id] = $row;
                }    
            }
            $return['csr_yearly_budget'] = $csr_yearly_budget;
        }
        return $return;
    }
    
    function getCSRContractors($get){
        global $callConfig;
        $return = array();
        $id = $get['rid'] ?? '';
        $whr = '';
        if(isset($id) && $id != ''){
            $whr = " AND id = ".$id;
        }
        $query1 = "SELECT * FROM  csr_contractor_registations
        where company_id = '" . $get['company_id'] . "' ".$whr." AND is_active=0";
        // $return['query'] = $query;
        $query_res = $callConfig->getAllRows($query1);
        $csr_contractors = array();
        foreach ($query_res as $row) {
            $csr_contractors[] = $row;
        }
        $return['csr_contractors'] = $csr_contractors;
        
        return $return;
    }

    function getAllCSRData($get){
        global $callConfig; 
        $return = array();
        $query = "SELECT * FROM  csr_proposal_data WHERE company_id = '" .$get['company_id']. "' AND current_status>0  AND is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $csr_proposal_arr = array();
        foreach ($query_res as $row) {
            $csr_proposal_arr[] = $row;
        }
        $return['csr_proposal_arr'] = $csr_proposal_arr;
        

        $query1 = "SELECT * FROM  csr_contractor_registations WHERE company_id = '" .$get['company_id']. "' AND current_status>0  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $csr_contractor_arr = array();
        foreach ($query_res1 as $row) {
            $csr_contractor_arr[] = $row;
        }
        $return['csr_contractor_arr'] = $csr_contractor_arr;

        $query2 = "SELECT * FROM  csr_project_tracking WHERE company_id = '" .$get['company_id']. "' AND current_status>0  AND is_active=0";
        $query_res2 = $callConfig->getAllRows($query2);
        $project_tracking_arr = array();
        foreach ($query_res2 as $row) {
            $project_tracking_arr[] = $row;
        }
        $return['project_tracking_arr'] = $project_tracking_arr;

       // --- transaction_approval_logs as [table][id][] ---
        $query5 = "SELECT * FROM transaction_approval_logs WHERE company_id = '" .$get['company_id']. "' AND transaction_table IN ('csr_contractor_registations', 'csr_proposal_data', 'csr_project_tracking')";
        $query_res5 = $callConfig->getAllRows($query5);
        $transaction_approval_arr = array();
        foreach ($query_res5 as $row) {
            $row = (array)$row; // Ensure $row is an array
            $table = $row['transaction_table'];
            $tid = $row['transaction_id'];
            if (!isset($transaction_approval_arr[$table])) {
                $transaction_approval_arr[$table] = array();
            }
            if (!isset($transaction_approval_arr[$table][$tid])) {
                $transaction_approval_arr[$table][$tid] = array();
            }
            $transaction_approval_arr[$table][$tid][] = $row;
        }
        $return['transaction_approval_arr'] = $transaction_approval_arr;

        // --- notifications as [table][transaction_number][] ---
        $query3 = "SELECT * FROM notifications WHERE company_id = '" .$get['company_id']. "' AND application_table_name IN ('csr_contractor_registations', 'csr_proposal_data', 'csr_project_tracking')";
        $query_res3 = $callConfig->getAllRows($query3);

        $notifications_arr = array();
        $notification_user_arr = array();
        foreach ($query_res3 as $row) {
            $row = (array)$row; // Ensure $row is an array
            $table = $row['application_table_name'];
            $tid = $row['transaction_number'];
            if (!isset($notifications_arr[$table])) {
                $notifications_arr[$table] = array();
            }
            if (!isset($notifications_arr[$table][$tid])) {
                $notifications_arr[$table][$tid] = array();
            }
            $notifications_arr[$table][$tid][] = $row;

            // notification_users (optional, keep as before or adjust as needed)
            $query4 = "SELECT * FROM notification_users WHERE company_id = '" .$get['company_id']. "' AND notification_id = '".$row['id']."'";
            $query_res4 = $callConfig->getRow($query4);
            if(!empty($query_res4)){
                $notification_user_arr[$row['id']] = $query_res4;
            }
        }
        $return['notifications_arr'] = $notifications_arr;
        $return['notification_user_arr'] = $notification_user_arr;

        return $return;
    }

    
    function tovCSRObjOthrSideCornJob($get, $post) {
        global $callConfig;
        $return = array();
        $data = json_decode($post['data'], 1);
        $company_id = $get['company_id'];
        $primaryKey = 'id';
        $timestampColumn = 'updated_date';


        // --- Sync main 3 tables ---
        $mainTables = [
            'csr_project_tracking' => $data['project_tracking_arr'] ?? [], 
            'csr_contractor_registations' => $data['csr_contractor_arr'] ?? [],       
            'csr_proposal_data' => $data['csr_proposal_arr'] ?? [],
        ];

        foreach ($mainTables as $tableName => $rows) {

            if (count((array)$rows) == 0) continue;
            $ids = array_column($rows, $primaryKey);
            $idsList = implode(',', array_map('intval', $ids));
            $existingRows = array();
            if ($idsList) {
                $selectQuery = "SELECT `$primaryKey`, `$timestampColumn` FROM `$tableName` WHERE company_id = '$company_id' AND `$primaryKey` IN ($idsList)";
                $resultRows = $callConfig->getAllRows($selectQuery);
                foreach ($resultRows as $row) {
                    $existingRows[$row->$primaryKey] = $row;
                }
            }
            $return[$tableName]['existingRows'] = $existingRows;
            foreach ($rows as $key => $row) {
                $pkValue = $row[$primaryKey];
                $timestampValue = $row[$timestampColumn];
                $escapedRow = [];
                foreach ($row as $column => $value) {
                    if (is_null($value)) {
                        $escapedRow[$column] = "NULL";
                    } elseif (is_numeric($value)) {
                        $escapedRow[$column] = $value;
                    } else {
                        $escapedRow[$column] = "'" . $callConfig->real_escape_string($value) . "'";
                    }

                }
                // $return[$tableName]['row'.'****'.$key] = $row;

                if (isset($existingRows[$pkValue]) and $existingRows[$pkValue] != '') {
                    // $return[$tableName]['row'.'_'.$key] = $row;

                    if (!empty($existingRows[$pkValue][$timestampColumn]) && !empty($timestampValue)) {
                        if ($existingRows[$pkValue][$timestampColumn] < $timestampValue) {
                            $set = [];
                            foreach ($escapedRow as $column => $value) {
                                $set[] = "`$column` = $value";
                            }
                            $setClause = implode(", ", $set);
                            $updateQuery = "UPDATE `$tableName` SET $setClause WHERE `$primaryKey` = '$pkValue'";
                            // $return[$tableName]['updateQuery'.'_'.$key] = $updateQuery;

                            $res =  $callConfig->executeQuery($updateQuery);
                            $return[$tableName]['updateQuery'.'_'.$key] = $res;


                        }
                    }
                } else {
                    $columns = implode(", ", array_keys($escapedRow));
                    $values = implode(", ", array_values($escapedRow));
                    $insertQuery = "INSERT INTO `$tableName` ($columns) VALUES ($values)";
                            // $return[$tableName]['insertQuery'.'_'.$key] = $insertQuery;

                    $res = $callConfig->executeQueryString($insertQuery);
                     $return[$tableName]['insertQuery'.'_'.$key] = $res;

                }
            }
        }
        $return['transaction_approval_arr'] = $data['transaction_approval_arr'];
        // --- Sync transaction_approval_logs ---
        if (!empty($data['transaction_approval_arr'])) {
            $logPairs = [];
            foreach ($data['transaction_approval_arr'] as $table => $idRows) {
                foreach ($idRows as $tid => $rows) {
                    $logPairs[] = "('" . $callConfig->real_escape_string($table) . "', '" . intval($tid) . "')";
                }
            }
            $existingLogs = [];


            if ($logPairs) {
                $logPairsStr = implode(',', $logPairs);
                $selectLogs = "SELECT transaction_table, transaction_id FROM transaction_approval_logs WHERE company_id = '$company_id' AND (transaction_table, transaction_id) IN ($logPairsStr)";
                $resultLogs = $callConfig->getAllRows($selectLogs);
                foreach ($resultLogs as $row) {
                    $row = (array)$row;
                    $existingLogs[$row['transaction_table']][$row['transaction_id']] = true;
                }
            }
            $return['logPairs'] = $logPairs;
            $return['existingLogs'] = $existingLogs;
            foreach ($data['transaction_approval_arr'] as $table => $idRows) {
                foreach ($idRows as $tid => $rows) {
                    if (!isset($existingLogs[$table][$tid])) {
                        foreach ($rows as $row) {
                            unset($row['id']);
                            $escapedRow = [];
                            foreach ($row as $column => $value) {
                                $escapedRow[$column] = is_null($value) ? "NULL" : "'" . $callConfig->real_escape_string($value) . "'";
                            }
                            $columns = implode(", ", array_keys($escapedRow));
                            $values = implode(", ", array_values($escapedRow));
                            $insertQuery = "INSERT INTO transaction_approval_logs ($columns) VALUES ($values)";
                            $res  = $callConfig->executeQueryString($insertQuery);
                            $return['res'][] = $res;
                        }
                    }
                }
            }

        }


        // --- Sync notifications and notification_users ---
        if (!empty($data['notifications_arr'])) {
            $notifPairs = [];
            foreach ($data['notifications_arr'] as $table => $idRows) {
                foreach ($idRows as $tid => $rows) {
                    $notifPairs[] = "('" . $callConfig->real_escape_string($table) . "', '" . $callConfig->real_escape_string($tid) . "')";
                }
            }
            $existingNotifs = [];
            if ($notifPairs) {
                $notifPairsStr = implode(',', $notifPairs);
                $selectNotifs = "SELECT application_table_name, transaction_number FROM notifications WHERE company_id = '$company_id' AND (application_table_name, transaction_number) IN ($notifPairsStr)";
                $resultNotifs = $callConfig->getAllRows($selectNotifs);
                foreach ($resultNotifs as $row) {
                    $row = (array)$row;
                    $existingNotifs[$row['application_table_name']][$row['transaction_number']] = true;
                }
            }

            foreach ($data['notifications_arr'] as $table => $idRows) {
                foreach ($idRows as $tid => $rows) {
                    if (!isset($existingNotifs[$table][$tid])) {
                        foreach ($rows as $row) {
                            $notificationId = $row['id'];
                            unset($row['id']);
                            $escapedRow = [];
                            foreach ($row as $column => $value) {
                                $escapedRow[$column] = is_null($value) ? "NULL" : "'" . $callConfig->real_escape_string($value) . "'";
                            }
                            // $columns = implode(", ", array_keys($escapedRow));
                            // $values = implode(", ", array_values($escapedRow));
                            // $insertQuery = "INSERT INTO notifications ($columns) VALUES ($values)";
                            // $res = $callConfig->executeQuery($insertQuery);
                            // $lastId = $res['LASTID'] ?? null;
                            $insertary = array();
                            foreach ($row as $column => $value) {
                                $insertary[$column] = $value;
                            }
                            $res = $callConfig->insertRecord('notifications', $insertary);
                            $lastId = $res['LASTID'] ?? null;
                            // Insert notification_user if present
                            if (!empty($data['notification_user_arr'][$notificationId]) && $lastId) {
                                $userRow = $data['notification_user_arr'][$notificationId];
                                unset($userRow['id']);
                                $userRow['notification_id'] = $lastId;
                                $escapedUserRow = [];
                                foreach ($userRow as $col => $val) {
                                    $escapedUserRow[$col] = is_null($val) ? "NULL" : "'" . $callConfig->real_escape_string($val) . "'";
                                }
                                $columns = implode(", ", array_keys($escapedUserRow));
                                $values = implode(", ", array_values($escapedUserRow));
                                $insertUserQuery = "INSERT INTO notification_users ($columns) VALUES ($values)";
                                $res = $callConfig->executeQueryString($insertUserQuery);
                            }
                        }
                    }
                }
            }
        }

        return $return;

    }

    function updateCSRTableData($get){
        global $callConfig;
        $updated_date = date('Y-m-d H:i:s');
        $query = "UPDATE " . $get['table_name'] . "  SET current_status = 199 , updated_date='" . $updated_date . "', updated_by='" . $get['user_id'] . "'  WHERE id = '" . $get['transaction_id'] . "'";
        $res = $callConfig->executeQuery($query);
        return $res;

    }

    function getUsersData($get){
        global $callConfig; 
        $return = array();
        $query = "SELECT * FROM  application_users WHERE company_id = '" .$get['company_id']. "' AND is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $application_users_arr = array();
        foreach ($query_res as $row) {
            $application_users_arr[] = $row;
        }
        $return['application_users_arr'] = $application_users_arr;
        

        $query1 = "SELECT * FROM  application_user_module_roles WHERE company_id = '" .$get['company_id']. "'  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $user_module_roles_arr = array();
        foreach ($query_res1 as $row) {
            $user_module_roles_arr[] = $row;
        }
        $return['user_module_roles_arr'] = $user_module_roles_arr;

        
        return $return;
    }

    function insertUsersData($get, $post){
        global $callConfig; 
        $return = array();
        $data = json_decode($post['data'], 1);
        // return $data;

        $query = "SELECT id FROM  application_users WHERE company_id = '" .$get['company_id']. "' AND is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $application_users_arr = array();
        foreach ($query_res as $row) {
            $application_users_arr[] = $row->id;
        }

        $query1 = "SELECT id FROM  application_user_module_roles WHERE company_id = '" .$get['company_id']. "'  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $user_module_roles_arr = array();
        foreach ($query_res1 as $row) {
            $user_module_roles_arr[] = $row->id;
        }

        foreach($data['application_users_arr'] as $row){
            if(!in_array($row['id'], $application_users_arr)){
                $insertary = array();
                foreach ($row as $column => $value) {
                    $insertary[$column] = $value;
                }
                $res = $callConfig->insertRecord('application_users', $insertary);
                $return['application_users'][] = $res;
            }
        }

        foreach($data['user_module_roles_arr'] as $row){
            if(!in_array($row['id'], $user_module_roles_arr)){
                $insertary = array();
                foreach ($row as $column => $value) {
                    $insertary[$column] = $value;
                }
                $res = $callConfig->insertRecord('application_user_module_roles', $insertary);
                $return['user_module_roles'][] = $res;
            }
        }
        return $return;
    }

    function getCSRRelatedData($get){
        global $callConfig;
        $return = array();

        $tables = [
            'csr_contractor_registations',
            'csr_project_tracking',
            'csr_proposal_data',
            'csr_setting',
            'csr_yearly_budget',
            'temp_csr_proposal_data',
            'transaction_approval_logs',
            'notifications',
            'notification_user'
        ];
        $company_id = $get['company_id'];
        $from_db = $get['data_name'];
        $to_db = $get['target_db'] ?? ''; 
        // Get last sync time from sync_logs
        $syncQuery = "SELECT last_sync_time FROM sync_logs 
                    WHERE company_id = '$company_id' AND from_db = '$from_db' AND to_db = '$to_db'
                    ORDER BY id DESC LIMIT 1";
        $syncRow = $callConfig->getRow($syncQuery);
        $lastSync = $syncRow ? $syncRow->last_sync_time : null;
        foreach ($tables as $table) {
            if ($lastSync) {
                if ($table === 'transaction_approval_logs') {
                    $where = "WHERE company_id = '$company_id' AND (
                        response_date > '$lastSync'
                        OR (response_date IS NULL AND request_date > '$lastSync')
                    )";
                } elseif ($table === 'notifications') {
                    $where = "WHERE company_id = '$company_id' AND notification_datetime > '$lastSync'";
                } elseif ($table === 'notification_user') {
                    $where = "WHERE company_id = '$company_id' AND (
                        read_datetime IS NULL
                        OR read_datetime > '$lastSync'
                    )";
                } else {
                    $where = "WHERE company_id = '$company_id' AND (
                        updated_date > '$lastSync'
                        OR (updated_date IS NULL AND created_date > '$lastSync')
                    )";
                }
            } else {
                $where = "WHERE company_id = '$company_id'";
            }

            $query = "SELECT * FROM $table $where";
            $return[$table] = $callConfig->getAllRows($query);
        }

        return $return;

    }

    function insertOrUpdateCSRData($get, $post){
        global $callConfig;
        $data = json_decode($post['data'], true);
        $return = array();
        // Define custom date fields per table
        $dateFieldMap = [
            'transaction_approval_logs' => 'response_date',
            'notification_user' => 'read_datetime',
            // default: updated_date for others
        ];
        foreach ($data as $table => $rows) {
            if (empty($rows)) continue;
            // notifications: only insert if not exists
            if ($table === 'notifications') {
                $existingIds = array_column(
                    $callConfig->getAllRows("SELECT id FROM $table WHERE company_id = '" . $get['company_id'] . "'"),
                    'id'
                );
                foreach ($rows as $row) {
                    if (!in_array($row['id'], $existingIds)) {
                        $res = $callConfig->insertRecord($table, $row);
                        $return[$table]['inserted'][] = $res;

                    }
                }
                continue;
            }
            // Step 1: get date field
            $dateField = $dateFieldMap[$table] ?? 'updated_date';
            // Step 2: prepare existing map
            $existingMap = [];
            $existingRows = $callConfig->getAllRows("SELECT id, $dateField FROM $table WHERE company_id = '" . $get['company_id'] . "'");
            foreach ($existingRows as $r) {
                $existingMap[$r->id] = $r->$dateField;
            }
            // Step 3: process each row
            foreach ($rows as $row) {
                $id = $row['id'];
                $incomingDate = $row[$dateField] ?? null;
                $existingDate = $existingMap[$id] ?? null;
                $dataArray = $row; // all columns
                if (array_key_exists($id, $existingMap)) {
                    $incomingTime = strtotime($incomingDate);
                    $existingTime = strtotime($existingDate);
                    if ($incomingTime && (!$existingTime || $incomingTime > $existingTime)) {
                        unset($dataArray['id']);
                        $res = $callConfig->updateRecord($table, $dataArray, 'id', $id);
                        $return[$table]['updated'][] = $res;
                    } else {
                        $return[$table]['existed'][] = $id;
                    }
                } else {
                    $res = $callConfig->insertRecord($table, $dataArray);
                    $return[$table]['inserted'][] = $res;

                }
            }
        }
        return $return;
    }

    function getSingleTableData($get){
        global $callConfig;
        $return = array();
        $table = $get['table_name'];
        $company_id = $get['company_id'];
        $from_db = $get['data_name'];
        $to_db = $get['target_db'] ?? '';
        if ($table == '') return ['response' => []];

        // Get last sync time from sync_logs
        $syncQuery = "SELECT last_sync_time FROM sync_logs 
                    WHERE company_id = '$company_id' AND from_db = '$from_db' AND to_db = '$to_db'
                    ORDER BY id DESC LIMIT 1";
        $syncRow = $callConfig->getRow($syncQuery);
        $lastSync = $syncRow ? $syncRow->last_sync_time : null;
    
        if ($lastSync) {
            if ($table === 'transaction_approval_logs') {
                $where = "WHERE company_id = '$company_id' AND (
                    response_date > '$lastSync'
                    OR (response_date IS NULL AND request_date > '$lastSync')
                )";
            } elseif ($table === 'notifications') {
                $where = "WHERE company_id = '$company_id' AND notification_datetime > '$lastSync'";
            } elseif ($table === 'notification_user') {
                $where = "WHERE company_id = '$company_id' AND (
                    read_datetime IS NULL
                    OR read_datetime > '$lastSync'
                )";
            } else {
                $where = "WHERE company_id = '$company_id' AND (
                    updated_date > '$lastSync'
                    OR (updated_date IS NULL AND created_date > '$lastSync')
                )";
            }
        } else {
            $where = "WHERE company_id = '$company_id'";
        }

        $query = "SELECT * FROM $table $where";
        $rows = $callConfig->getAllRows($query);
        $return['response'] = $rows;
        return $return;

    }

    function insertOrUpdateSingleTableData($get, $post){
        global $callConfig;
        $return  = array();
        $table = $get['table_name'];
        $data = json_decode($post['data'], true);
        // return $data;
        if (empty($data['response'])) return ['status' => 'No data'];
        $dateFieldMap = [
            'transaction_approval_logs' => 'response_date',
            'notification_user' => 'read_datetime',
            // default: updated_date for others
        ];
        // notifications: only insert if not exists
        if ($table === 'notifications') {
            $existingIds = array_column(
                $callConfig->getAllRows("SELECT id FROM $table WHERE company_id = '" . $get['company_id'] . "'"),
                'id'
            );
            foreach ($data['response'] as $row) {
                if (!in_array($row['id'], $existingIds)) {
                    $res = $callConfig->insertRecord($table, $row);
                    $return[$table]['inserted'][] = $res;

                }
            }

        }else{
        // Step 1: get date field
            $dateField = $dateFieldMap[$table] ?? 'updated_date';
            // Step 2: prepare existing map
            $existingMap = [];
            $existingRows = $callConfig->getAllRows("SELECT id, $dateField FROM $table WHERE company_id = '" . $get['company_id'] . "'");
            foreach ($existingRows as $r) {
                $existingMap[$r->id] = $r->$dateField;
            }
            // Step 3: process each row
            foreach ($data['response'] as $row) {
                $id = $row['id'];
                $incomingDate = $row[$dateField] ?? null;
                $existingDate = $existingMap[$id] ?? null;
                $dataArray = $row; // all columns
                if (array_key_exists($id, $existingMap)) {
                    $incomingTime = strtotime($incomingDate);
                    $existingTime = strtotime($existingDate);
                    if ($incomingTime && (!$existingTime || $incomingTime > $existingTime)) {
                        unset($dataArray['id']);
                        $res = $callConfig->updateRecord($table, $dataArray, 'id', $id);
                        $return[$table]['updated'][] = $res;
                    } else {
                        $return[$table]['existed'][] = $id;
                    }
                } else {
                    $res = $callConfig->insertRecord($table, $dataArray);
                    $return[$table]['inserted'][] = $res;

                }
            }
        }


        return $return;
    }

    function insertSyncLog($get, $post) {
        // return $post;
        global $callConfig;

        $insertary = array(
            'company_id'      => $get['company_id'],
            'from_db'         => $get['data_name'],
            'to_db'           => $get['target_db'],
            'table_names'     => $post['table_names'],
            'last_sync_time'  => date("Y-m-d H:i:s"),
            'is_active'       => 0,
            'current_status'  => 0,
            'created_date'    => date("Y-m-d H:i:s"),
            'created_by'      => $get['user_id'] ?? 1
        );

        return $callConfig->insertRecord('sync_logs', $insertary);
    }
    function getVerticalsData($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id, vertical_name FROM verticals WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY vertical_name ASC"; 
        $data = $callConfig->getAllRows($query);
        $return['verticals'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE genc_code = 'JOBUOM' AND company_id = {$get['company_id']} AND is_active = 0"; 
        $data = $callConfig->getAllRows($query);
        $return['types'] = $data;

        $query = "SELECT id, display_name FROM vendors WHERE  company_id = {$get['company_id']} AND is_active = 0"; 
        $data = $callConfig->getAllRows($query);
        $return['vendors'] = $data;

        $query = "SELECT id, concat(currency_short_code, '-', currency_name) AS currency FROM currencies WHERE  company_id = {$get['company_id']} AND is_active = 0"; 
        $data = $callConfig->getAllRows($query);
        $return['currencies'] = $data;

        $query = "SELECT id, job_title FROM job_titles WHERE  company_id = {$get['company_id']} AND is_active = 0"; 
        $data = $callConfig->getAllRows($query);
        $return['job_titles'] = $data;

        return $return;
    }
    
    function getCsrViewRelatedData($get){
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
        $table = $get['table'] ?? '';
        if ($rid != '' && $table != '') {
            $query = "SELECT * FROM $table WHERE company_id = '" . $get['company_id'] . "' AND id = '$rid' AND is_active=0";
            $rows = $callConfig->getRow($query);
            $return['csr_data'] = $rows;
        }
        $query1 = "SELECT id, type_of_work FROM csr_type_of_works WHERE company_id = '" . $get['company_id'] . "' AND  is_active=0";
        $rows1 = $callConfig->getAllRows($query1);
        $csr_type_of_works = array();
        if (!empty($rows1)) {
            foreach ($rows1 as $row) {
                $csr_type_of_works[$row->id] = $row;
            }
        }
        $return['csr_type_of_works'] = $csr_type_of_works;

        $query2 = "SELECT id, csr_area FROM csr_areas WHERE company_id = '" . $get['company_id'] . "' AND  is_active=0";
        $rows2 = $callConfig->getAllRows($query2);
        $csr_areas = array();
        if (!empty($rows2)) {
            foreach ($rows2 as $row) {
                $csr_areas[$row->id] = $row;
            }
        }
        $return['csr_areas'] = $csr_areas;

        $query3 = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $get['company_id'] . "' AND genc_code = 'CSR_PROJECT_TYPE' AND  is_active=0";
        $rows3 = $callConfig->getAllRows($query3);
        $return['query3'] = $query3;

        $csr_type_of_projects = array();
        if (!empty($rows3)) {
            foreach ($rows3 as $row) {
                $csr_type_of_projects[$row->genc_detail_code] = $row;
            }
        }
        $return['csr_type_of_projects'] = $csr_type_of_projects;

        $query4 = "SELECT id, location_name FROM locations WHERE company_id = '" . $get['company_id'] . "'  AND  is_active=0";
        $rows4 = $callConfig->getAllRows($query4);
        $locations = array();
        if (!empty($rows4)) {
            foreach ($rows4 as $row) {
                $locations[$row->id] = $row;
            }
        }
        $return['locations'] = $locations;

        $query = "SELECT * FROM csr_entity_name WHERE company_id = '" . $get['company_id'] . "' AND  is_active=0";
        $data = $callConfig->getAllRows($query);
        $return['entity_names'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '{$get['company_id']}' AND genc_code = 'CSR_FINANCIAL_YEAR' AND  is_active=0 ORDER BY id ASC";
        $rows = $callConfig->getAllRows($query);
        $return['financial_years'] = $rows;

        return $return;
    }

    function getCsrInvoiceDetails($get){
        global $callConfig;
        $return = array();
        $tracking_id = $get['tracking_id'] ?? '';
        $project_id = $get['project_id'] ?? '';

        if ($tracking_id != '' && $project_id != '') {
            $query = "SELECT * FROM csr_invoice_details WHERE company_id = '{$get['company_id']}' AND tracking_id = '{$tracking_id}' AND project_id = '{$project_id}'  AND is_active=0";
            $csr_invoices = $callConfig->getAllRows($query);
            $return['csr_invoices'] = $csr_invoices;

            $query = "SELECT id, po_no FROM csr_po_details WHERE company_id = '{$get['company_id']}' AND tracking_id = '{$tracking_id}' AND project_id = '{$project_id}'  AND is_active=0";
            $csr_pos = $callConfig->getAllRows($query);
            $return['csr_pos'] = $csr_pos;
        }

        // Fetch Financial Years from general_code_details
        $fyQuery = "SELECT genc_detail_code 
                    FROM general_code_details 
                    WHERE company_id = '".$get['company_id']."' 
                    AND genc_code = 'CSR_FINANCIAL_YEAR' 
                    AND is_active = 0
                    ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);
        $return['fyRows'] = $fyRows;
                
        return $return;

    }
    function getCsrMasterData($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        //  Mandatory: company_id
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;
        //  Main SQL query
        $query = "SELECT p.id AS proposal_id, p.institue_name AS entity, p.subject AS project_name, ged.genc_detail_name AS type_of_project, tow.type_of_work, p.SDGproposal AS nature_of_work_done, p.csr_location,
                p.financial_year AS project_related_to_fy,
                po.financial_year AS po_issued_year,
                po.po_no, po.po_date, po.po_value_ex AS po_value_excl_tax, ROUND(po.po_value_ex * (po.gst / 100), 2) AS gst_18, po.po_value_in AS po_value_incl_tax, po.business_code AS bp_code, po.business_name AS bp_name,
                inv.financial_year AS invoice_accounting_year, 
                inv.inv_no AS invoice_number, inv.inv_date, inv.taxable_value, inv.gst_amount, 0 AS other_cost, inv.total_spent, inv.tds_deducted, 0 AS any_other_deduction, inv.net_payable,
                ROUND((IFNULL(inv.total_spent,0) / NULLIF(po.po_value_in,0)) * 100, 2) AS spent_percent,
                ROUND(100 - ((IFNULL(inv.total_spent,0) / NULLIF(po.po_value_in,0)) * 100), 2) AS unspent_percent,
                inv.paid_amount,
                (inv.paid_amount - inv.net_payable) AS delta,
                inv.paid_date,
                inv.paid_bank AS paid_from_bank,
                p.project_end_date AS project_completion_date,

                CASE 
                    WHEN COALESCE(ts.total_rows, 0) = 0 THEN 'Not Started'
                    WHEN COALESCE(ts.null_rows, 0) = ts.total_rows THEN 'Not Started'
                    WHEN  ts.completed_rows = ts.total_rows OR p.tracking_status = 1 THEN 'Completed'
                    ELSE 'In Progress'
                END AS project_status
            FROM csr_proposal_data p
            LEFT JOIN (SELECT proposal_id,COUNT(*) AS total_rows, SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) AS null_rows, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_rows FROM csr_project_tracking WHERE is_active = 0 GROUP BY proposal_id) ts ON ts.proposal_id = p.id
            LEFT JOIN csr_po_details po  ON po.project_id = p.id  AND po.is_active = 0
            LEFT JOIN csr_invoice_details inv ON inv.project_id = p.id AND inv.csr_po_details_id = po.id AND inv.company_id = p.company_id AND inv.is_active = 0 
            LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work AND tow.company_id = p.company_id AND tow.is_active = 0
            LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0
            LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0
            WHERE p.current_status=".ST_APPROVED." AND p.is_active = 0 AND p.company_id = '".$company_id."' ORDER BY p.subject, po.po_no, inv.inv_date";
        //  Execute and fetch
        $res = $callConfig->getAllRows($query);
        $return['project_financial_list'] = $res;
        $return['sql'] = $query;

        return $return;
    }
    function getCsrProjectSummaryReport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        $fyQuery = "SELECT genc_detail_code, genc_detail_name
            FROM general_code_details
            WHERE company_id = '".$company_id."'
            AND genc_code = 'CSR_FINANCIAL_YEAR'
            AND is_active = 0
            ORDER BY id ASC";
        $fyRows = $callConfig->getAllRows($fyQuery);
        $return['financial_years'] = $fyRows;

        $selected_year = isset($get['year']) ? $callConfig->real_escape_string($get['year']) : '';
        $todayYear = date("Y");
        $todayMonth = date("n");
        if ($todayMonth >= 4) {
            $startYear = $todayYear;
            $endYear = $todayYear + 1;
        } else {
            $startYear = $todayYear - 1;
            $endYear = $todayYear;
        }
        $currentFY = $startYear . '-' . substr($endYear, -2);

        $selected_year_index = -1;
        if ($selected_year == '' && count((array)$fyRows) > 0) {
            $selected_year = $currentFY;
        }
        if (count((array)$fyRows) > 0) {
            foreach ($fyRows as $index => $fyRow) {
                if ($fyRow->genc_detail_code == $selected_year) {
                    $selected_year_index = $index;
                    break;
                }
            }
            if ($selected_year_index === -1) {
                $fallback_year = $currentFY;
                foreach ($fyRows as $index => $fyRow) {
                    if ($fyRow->genc_detail_code == $fallback_year) {
                        $selected_year_index = $index;
                        $selected_year = $fyRow->genc_detail_code;
                        break;
                    }
                }
                if ($selected_year_index === -1) {
                    $selected_year_index = count($fyRows) - 1;
                    $selected_year = $fyRows[$selected_year_index]->genc_detail_code;
                }
            }
        }
        $return['selected_year'] = $selected_year;

        // $proposal_fy_where = $selected_year != '' ? " AND p.financial_year = '".$selected_year."'" : "";
        $proposal_fy_where = '';
        $inv_fy_where = $selected_year != ''
            ? " AND (CASE WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$'
                          THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                          ELSE financial_year END) = '".$selected_year."'"
            : "";

        $query = "SELECT 
            loc.location_name AS location, 
            p.subject AS project_name, 
            p.SDGproposal AS nature_of_work_done, 
            tow.type_of_work, 
            ged.genc_detail_name AS type_of_project, 
            cas.csr_area AS area, 

            ROUND(IFNULL(po.total_po_excl_gst, 0), 2) AS total_po_excl_gst, 
            ROUND(IFNULL(inv.total_spent_excl_gst, 0), 2) AS total_spent_excl_gst, 

            ROUND(
            IFNULL(po.total_po_excl_gst, 0) - IFNULL(inv.total_spent_excl_gst, 0), 
            2
            ) AS unspent_amount,

            CASE 
            WHEN IFNULL(po.total_po_excl_gst, 0) > 0 
            THEN ROUND((IFNULL(inv.total_spent_excl_gst, 0) / po.total_po_excl_gst) * 100, 2)
            ELSE 0 
            END AS spent_percent,

            CASE 
            WHEN IFNULL(po.total_po_excl_gst, 0) > 0 
            THEN ROUND(100 - ((IFNULL(inv.total_spent_excl_gst, 0) / po.total_po_excl_gst) * 100), 2)
            ELSE 0 
            END AS unspent_percent,

            CASE 
            WHEN IFNULL(tr.total_tracking, 0) = 0 THEN 'Not Started'
            WHEN IFNULL(tr.null_status_count, 0) = tr.total_tracking THEN 'Not Started'
            WHEN p.tracking_status = 1 THEN 'Completed'
            ELSE 'In Progress'
            END AS project_status

            FROM csr_proposal_data p 

            LEFT JOIN (
                SELECT 
                project_id,
                company_id,
                SUM(IFNULL(po_value_in, 0)) AS total_po_excl_gst
                FROM csr_po_details
                WHERE is_active = 0
                GROUP BY project_id, company_id
            ) po ON po.project_id = p.id
            AND po.company_id = p.company_id

            LEFT JOIN (
                SELECT 
                project_id,
                company_id,
                SUM(IFNULL(total_spent, 0)) AS total_spent_excl_gst
                FROM csr_invoice_details
                WHERE is_active = 0".$inv_fy_where."
                GROUP BY project_id, company_id
            ) inv ON inv.project_id = p.id
            AND inv.company_id = p.company_id

            LEFT JOIN (
                SELECT
                proposal_id,
                COUNT(id) AS total_tracking,
                SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) AS null_status_count
                FROM csr_project_tracking
                WHERE is_active = 0
                GROUP BY proposal_id
            ) tr ON tr.proposal_id = p.id

            LEFT JOIN locations loc ON loc.id = p.csr_location 
            AND loc.company_id = p.company_id 
            AND loc.is_active = 0 

            LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work 
            AND tow.company_id = p.company_id 
            AND tow.is_active = 0 

            LEFT JOIN csr_areas cas ON cas.id = p.working_area 
            AND cas.company_id = p.company_id 
            AND cas.is_active = 0 

            LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project 
            AND ged.genc_code = 'CSR_PROJECT_TYPE' 
            AND ged.company_id = p.company_id 
            AND ged.is_active = 0 

            WHERE 
            p.current_status = ".ST_APPROVED." 
            AND p.is_active = 0 
            AND p.company_id = $company_id".$proposal_fy_where." 

            ORDER BY 
            p.csr_location, 
            p.subject";
        $res = $callConfig->getAllRows($query);
        $return['project_summary_list'] = $res;

        return $return;
    }
    function getCsrPoVsActualsSummary($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $company_id  = isset($get['company_id']) ? $get['company_id'] : 0;
        $fyQuery = "SELECT genc_detail_code 
            FROM general_code_details 
            WHERE company_id = '".$company_id."' 
            AND genc_code = 'CSR_FINANCIAL_YEAR' AND genc_detail_code NOT IN ('2013-14','2014-15','2015-16','2016-17', '2017-18', '2018-19', '2019-20')
            AND is_active = 0 
            ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);
        // 🏗 Build dynamic columns for each FY
        $fyColumns = "";
        if(!empty($fyRows)){
            foreach($fyRows as $fyRow){
                $fyText = trim($fyRow->genc_detail_code);      // e.g. 2013-14
                $fyKey  = str_replace('-', '_', $fyText);      // e.g. 2013_14 (safe for alias)

                $fyColumns .= "
                    , CASE WHEN po.financial_year = '$fyText' THEN po.po_no END AS po_no_$fyKey
                    , CASE WHEN po.financial_year = '$fyText' THEN po.po_value_in ELSE 0 END AS po_value_$fyKey
                    , (SELECT IFNULL(SUM(inv.total_spent),0)
                        FROM csr_invoice_details inv
                        WHERE inv.csr_po_details_id = po.id
                          AND inv.financial_year = '$fyText'
                          AND inv.is_active = 0) AS spent_$fyKey
                ";
            }
        }
            $query = "SELECT 
                p.id AS project_id,
                p.subject AS project_name,
                p.SDGproposal AS nature_of_work_done,
                po.business_name AS business_partner_name,
                po.po_value_in AS total_po_amount,
                (SELECT IFNULL(SUM(inv.total_spent),0) FROM csr_invoice_details inv WHERE inv.csr_po_details_id = po.id AND inv.is_active = 0) AS total_spent_amount,
                ROUND(( (SELECT IFNULL(SUM(inv.total_spent),0) FROM csr_invoice_details inv WHERE inv.csr_po_details_id = po.id AND inv.is_active = 0) / NULLIF(po.po_value_in,0) ) * 100,2) AS spent_percent,
                ROUND(po.po_value_in - (SELECT IFNULL(SUM(inv.total_spent),0) FROM csr_invoice_details inv WHERE inv.csr_po_details_id = po.id AND inv.is_active = 0),2) AS unspent_amount,
                ROUND(100 - ( (SELECT IFNULL(SUM(inv.total_spent),0) FROM csr_invoice_details inv WHERE inv.csr_po_details_id = po.id AND inv.is_active = 0) / NULLIF(po.po_value_in,0) ) * 100,2) AS unspent_percent
                $fyColumns
            FROM csr_proposal_data p
            LEFT JOIN csr_po_details po 
                ON po.project_id = p.id AND po.is_active = 0
            LEFT JOIN csr_areas cas 
                ON cas.id = p.working_area 
                AND cas.company_id = p.company_id 
                AND cas.is_active = 0
            WHERE p.current_status=".ST_APPROVED." AND p.is_active = 0 AND p.company_id = '".$company_id."'
            ORDER BY p.subject, po.po_no";

        $res = $callConfig->getAllRows($query);
        $return['po_vs_actuals_list'] = $res;
        $return['sql'] = $query;

        $return['fy_list'] = $fyRows; // 📌 FY list for frontend headers
        return $return;
    }
    function getCsrUnspentCalculationReport($get) {
        global $callConfig;
        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        // 📅 Fetch Financial Years from general_code_details
        $fyQuery = "SELECT genc_detail_code 
            FROM general_code_details 
            WHERE company_id = '".$company_id."' 
            AND genc_code = 'CSR_FINANCIAL_YEAR' 
            AND is_active = 0 
            ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);

        if (!$fyRows) {
            $return['report'] = [];
            return $return;
        }

        // 🧾 Fetch yearly budget data
        $budgetQuery = "SELECT financial_year, net_profit_before_tax_mtipl
            FROM csr_complaince
            WHERE company_id = '".$company_id."' AND is_active = 0";

        $budgetData = $callConfig->getAllRows($budgetQuery);

        $budgets = [];
        foreach ($budgetData as $b) {
            $parts = explode('-', $b->financial_year);
            $endYear = (int)('20' . $parts[1]);
            $budgets[$endYear] = (float)$b->net_profit_before_tax_mtipl;
        }

        // 💰 Fetch total spent by FY
        $spentQuery = "SELECT 
            CAST(CONCAT('20', SUBSTRING_INDEX(financial_year, '-', -1)) AS UNSIGNED) AS fy,
            SUM(total_spent) as total_spent
        FROM csr_invoice_details
        WHERE company_id = '".$company_id."' AND is_active = 0
        GROUP BY fy";

        $spentData = $callConfig->getAllRows($spentQuery);

        $spent = [];
        foreach ($spentData as $s) {
            $spent[$s->fy] = (float)$s->total_spent;
        }

        $report = [];
        $carryForward = 0;

        foreach ($fyRows as $row) {

            $fyString = $row->genc_detail_code;  // 2019-20
            $parts = explode('-', $fyString);
            $year = (int)('20' . $parts[1]);  // ending year

            $budget = $budgets[$year] ?? 0;
            $inMn = $budget / 1000000;

            $avg = 0;
            if (isset($budgets[$year-1]) && isset($budgets[$year-2]) && isset($budgets[$year-3])) {
                $avg = round(($budgets[$year-1] + $budgets[$year-2] + $budgets[$year-3]) / 3, 0);
            }

            $twoPercent = $avg > 0 ? round($avg * 0.02, 2) : 0;
            $actualSpent = $spent[$year] ?? 0;
            $unspent = $twoPercent - $actualSpent;

            $carryForwardUsed = $carryForward < 0 ? abs($carryForward) : 0;
            $provision = $unspent - $carryForwardUsed;
            $netDebit = $actualSpent;

            $carryForward = $unspent;

            $report[] = [
                'year_label' => $fyString,
                'budget' => $budget,
                'in_mn' => $inMn,
                'avg_net_profit' => $avg,
                'two_percent' => $twoPercent,
                'actual_spent' => $actualSpent,
                'unspent' => $unspent,
                'carry_forward_used' => $carryForwardUsed,
                'provision' => $provision,
                'net_debit' => $netDebit
            ];
        }

        $return['report'] = $report;
        return $return;
    }
    function getCsrSummaryReport($get) {
        global $callConfig;
        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        // ✅ 1. Get yearly budgets (net profit)
        $budgets_sql = "SELECT financial_year AS year, net_profit_before_tax_mtipl AS finance_budget 
                        FROM csr_complaince 
                        WHERE company_id = '".$company_id."' AND is_active = 0
                        ORDER BY financial_year ASC";
        $budgets = $callConfig->getAllRows($budgets_sql);

        // ✅ 2. Get actual CSR spend per FY
        $spend_sql = "SELECT 
                        CONCAT(YEAR(inv_date), '-', LPAD((YEAR(inv_date)+IF(MONTH(inv_date)>=4,1,0))%100,2,'0')) AS fy_label,
                        YEAR(inv_date) + IF(MONTH(inv_date)>=4,1,0) AS fy_year,
                        SUM(total_spent) AS total_spent
                    FROM csr_invoice_details 
                    WHERE company_id = '".$company_id."' AND is_active = 0
                    GROUP BY fy_label, fy_year
                    ORDER BY fy_year ASC";
        $spend_data = $callConfig->getAllRows($spend_sql);

        // Convert spend to easy lookup
        $spend_map = [];
        foreach ($spend_data as $s) {
            $spend_map[$s->fy_year] = $s->total_spent;
        }

        // ✅ 3. Calculate required & summary per year
        $summary = [];
        $carry_forward_prev = 0;

        // Build array of all FY years (including those without invoices)
        $years = [];
        foreach ($budgets as $b) {
            $years[] = $b->year;
        }
        sort($years);

        foreach ($years as $i => $year) {
            // Average net profit for previous 3 years
            $avg = 0;
            if ($i >= 3) {
                $avg = ($budgets[$i]->finance_budget + $budgets[$i-1]->finance_budget + $budgets[$i-2]->finance_budget) / 3;
            } elseif ($i >= 2) {
                $avg = ($budgets[$i]->finance_budget + $budgets[$i-1]->finance_budget) / 2;
            } elseif ($i >= 1) {
                $avg = ($budgets[$i]->finance_budget);
            }

            $two_percent = round($avg * 0.02, 2);
            $actual_spent = isset($spend_map[$year]) ? $spend_map[$year] : 0;
            $current_unspent = $two_percent - $actual_spent;

            // Carry forward from previous year
            $cf_previous = $carry_forward_prev;
            $spent_from_py = 0; // (no separate tagging yet — can add later)
            $cf_balance_py = $cf_previous - $spent_from_py;
            if ($cf_balance_py < 0) $cf_balance_py = 0;

            $remaining_unspent = $current_unspent + $cf_balance_py;

            $carry_forward_prev = $remaining_unspent;

            $summary[] = [
                'year' => $year,
                'label' => ($year-1).'-'.substr($year, -2),
                'gross_required' => $two_percent,
                'total_spent' => $actual_spent,
                'current_unspent' => $current_unspent,
                'carry_forward_previous' => $cf_previous,
                'spent_from_py' => $spent_from_py,
                'cf_balance_py' => $cf_balance_py,
                'remaining_unspent' => $remaining_unspent
            ];
        }

        $return['summary'] = $summary;
        return $return;
    }
    function getCsrYearlySummaryReport($get) {
        global $callConfig;

        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;
        $selectedFY = isset($get['year']) ? $get['year'] : '';

        if (empty($selectedFY)) {
            $return['summary'] = array();
            return $return;
        }

        // Fetch FY dropdown
        $fyQuery = "SELECT genc_detail_code, genc_detail_name
            FROM general_code_details
            WHERE company_id = '".$company_id."'
            AND genc_code = 'CSR_FINANCIAL_YEAR'
            AND is_active = 0
            ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);

        if (!$fyRows) {
            $return['summary'] = array();
            $return['financial_years'] = array();
            return $return;
        }

        // Fetch budget data
        $budgetQuery = "SELECT financial_year, net_profit_before_tax_mtipl
            FROM csr_complaince
            WHERE company_id = '".$company_id."'
            AND is_active = 0";

        $budgetData = $callConfig->getAllRows($budgetQuery);

        $budgets = array();

        foreach ($budgetData as $b) {
            $parts = explode('-', $b->financial_year);
            $endYear = (int)('20' . $parts[1]);

            $budgets[$endYear] = (float)$b->net_profit_before_tax_mtipl;
        }

        // Fetch spent data
        $spentQuery = "SELECT 
                CAST(CONCAT('20', SUBSTRING_INDEX(financial_year, '-', -1)) AS UNSIGNED) AS fy,
                SUM(total_spent) AS total_spent
            FROM csr_invoice_details
            WHERE company_id = '".$company_id."'
            AND is_active = 0
            GROUP BY fy";

        $spentData = $callConfig->getAllRows($spentQuery);

        $spent = array();

        foreach ($spentData as $s) {
            $spent[$s->fy] = (float)$s->total_spent;
        }

        $carryForward = 0;
        $selectedSummary = array();

        foreach ($fyRows as $row) {

            $fyString = $row->genc_detail_code; // 2025-26

            $parts = explode('-', $fyString);
            $year = (int)('20' . $parts[1]); // 2026

            // Current FY budget
            $budget = isset($budgets[$year]) ? $budgets[$year] : 0;
            $inMn = $budget / 1000000;

            // IMPORTANT:
            // For 2025-26, year = 2026
            // CSR average should be 2024-25, 2023-24, 2022-23
            // Means: 2025, 2024, 2023
            $avg = 0;

            if (
                isset($budgets[$year - 1]) &&
                isset($budgets[$year - 2]) &&
                isset($budgets[$year - 3])
            ) {
                $avg = round(
                    (
                        $budgets[$year - 1] +
                        $budgets[$year - 2] +
                        $budgets[$year - 3]
                    ) / 3,
                    0
                );
            }

            $twoPercent = $avg > 0 ? round($avg * 0.02, 2) : 0;

            $actualSpent = isset($spent[$year]) ? $spent[$year] : 0;

            $unspent = $twoPercent - $actualSpent;

            $carryForwardUsed = $carryForward < 0 ? abs($carryForward) : 0;

            $provision = $unspent - $carryForwardUsed;

            $netDebit = $actualSpent;

            $currentCarryForward = $unspent;

            if ($fyString == $selectedFY) {
                $selectedSummary = array(
                    'year' => $fyString,
                    'label' => $fyString,
                    'budget' => $budget,
                    'in_mn' => $inMn,
                    'avg_net_profit' => $avg,
                    'gross_required' => $twoPercent,
                    'total_spent' => $actualSpent,
                    'current_unspent' => $unspent,
                    'carry_forward_previous' => $carryForwardUsed,
                    'spent_from_py' => 0,
                    'cf_balance_py' => $carryForwardUsed,
                    'remaining_unspent' => $provision,
                    'net_debit' => $netDebit
                );

                break;
            }

            $carryForward = $currentCarryForward;
        }

        $return['summary'] = $selectedSummary;
        $return['financial_years'] = $fyRows;

        return $return;
    }
    
    function getCSRProjects($get){
        global $callConfig;
        $query = "SELECT id, subject 
            FROM csr_proposal_data 
            WHERE company_id = {$get['company_id']} 
              AND is_active = 0
            ORDER BY id ASC";

        $data = $callConfig->getAllRows($query);

        return array('table' => $data);
    }
    function insertBulkCsrPoUpload($get, $post){
        global $callConfig;

        $tracking_id = $post['tracking_id'] ?? '';
        $project_id = $post['project_id'] ?? '';
        $poNos = $post['po_no'] ?? array();
        $poAvailable = $post['po_available'] ?? array();
        $inserted = 0;
        $errors = array();

        $rowCount = count($post['po_date'] ?? array());
        for ($i = 0; $i < $rowCount; $i++) {
            $po_available_value = strtolower(trim($poAvailable[$i] ?? ''));
            $resolved_po_no = trim($poNos[$i] ?? '');
            $po_toggle = '0';
            if ($po_available_value == 'no'  && $resolved_po_no == '') {
                $resolved_po_no = 'NON PO';
                $po_toggle = '1';
            }
            $data_array = array(
                'tracking_id' => $tracking_id,
                'project_id' => $project_id,
                'po_toggle' => $po_toggle,
                'financial_year' => $post['financial_year'][$i] ?? '',
                'po_no' => $resolved_po_no,
                'po_date' => isset($post['po_date'][$i]) && $post['po_date'][$i] != '' ? date('Y-m-d', strtotime($post['po_date'][$i])) : '',
                'po_amount' => $post['po_amount'][$i] ?? '',
                'po_value_ex' => $post['po_value_ex'][$i] ?? '',
                'gst' => $post['gst'][$i] ?? '',
                'po_value_in' => $post['po_value_in'][$i] ?? '',
                'business_code' => $post['business_code'][$i] ?? '',
                'business_name' => $post['business_name'][$i] ?? '',
                'po_details' => $post['po_details'][$i] ?? '',
                'remarks' => $post['remarks'][$i] ?? ''
            );

            $data = array_map('trim', $data_array);

            $data['current_status'] = '0';
            $data['is_active'] = '0';
            $data['status_date'] = date('Y-m-d H:i:s');
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['company_id'] = $get['company_id'] ?? '';
            $data['created_by'] =$get['user_id'] ?? '';
            $data['originator'] = $get['user_id'] ?? '';

            $res = $callConfig->insertRecord('csr_po_details', $data);
            if (isset($res['status']) && $res['status'] == 'sucess') {
                $inserted++;
            } else {
                $errors[] = array(
                    'row' => $i + 1,
                    'message' => $res['message'] ?? 'Failed to insert row'
                );
            }
        }

        if (count((array)$errors)>0) {
            return array(
                'status' => 'partial',
                'message' => $inserted . ' row(s) inserted. ' . count($errors) . ' row(s) failed.',
                'inserted' => $inserted,
                'errors' => $errors
            );
        }

        return array(
            'status' => 'success',
            'message' => $inserted . ' row(s) inserted successfully.',
            'inserted' => $inserted
        );
    }

    function insertBulkCsrInvoiceUpload($get, $post){
        global $callConfig;

        $tracking_id = $post['tracking_id'] ?? '';
        $project_id = $post['project_id'] ?? '';
        $company_id = $get['company_id'] ?? ($post['company_id'] ?? '');
        $created_by = $get['user_id'] ?? ($post['user_id'] ?? '');
        $originator = $get['user_id'] ?? ($post['originator'] ?? '');

        $poQuery = "SELECT id, po_no FROM csr_po_details
            WHERE company_id = '" . $company_id . "'
            AND tracking_id = '" . $tracking_id . "'
            AND project_id = '" . $project_id . "'
            AND is_active = 0";
        $poRows = $callConfig->getAllRows($poQuery);
        $poMap = array();
        if (count((array)$poRows)>0) {
            foreach ($poRows as $poRow) {
                $poMap[trim($poRow->po_no)] = $poRow->id;
            }
        }

        $inserted = 0;
        $errors = array();
        $missing_po_rows = array();
        $rowCount = max(
            count($post['inv_no'] ?? array()),
            count($post['financial_year'] ?? array()),
            count($post['inv_date'] ?? array()),
            count($post['paid_date'] ?? array()),
            count($post['paid_amount'] ?? array())
        );

        for ($i = 0; $i < $rowCount; $i++) {
            $uploaded_po_no = trim($post['po_no'][$i] ?? '');
            $csr_po_details_id = '';

            if ($uploaded_po_no != '' && isset($poMap[$uploaded_po_no])) {
                $csr_po_details_id = $poMap[$uploaded_po_no];
            } elseif ($uploaded_po_no != '') {
                $missing_po_rows[] = array(
                    'row' => $i + 1,
                    'po_no' => $uploaded_po_no
                );
            }

            $data_array = array(
                'tracking_id' => $tracking_id,
                'project_id' => $project_id,
                'financial_year' => $post['financial_year'][$i] ?? '',
                'inv_no' => $post['inv_no'][$i] ?? '',
                'csr_po_details_id' => $csr_po_details_id,
                'inv_date' => !empty($post['inv_date'][$i]) ? date('Y-m-d', strtotime($post['inv_date'][$i])) : '',
                'paid_date' => !empty($post['paid_date'][$i]) ? date('Y-m-d', strtotime($post['paid_date'][$i])) : '',
                'paid_amount' => $post['paid_amount'][$i] ?? '',
                'paid_bank' => $post['paid_bank'][$i] ?? '',
                'taxable_value' => $post['taxable_value'][$i] ?? '',
                'gst_amount' => $post['gst_amount'][$i] ?? '',
                'total_spent' => $post['total_spent'][$i] ?? '',
                'tds_deducted' => $post['tds_deducted'][$i] ?? '',
                'net_payable' => $post['net_payable'][$i] ?? ''
            );

            $data = array_map('trim', $data_array);
            if (empty(array_filter($data))) {
                continue;
            }

            $data['current_status'] = '0';
            $data['is_active'] = '0';
            $data['status_date'] = date('Y-m-d H:i:s');
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['company_id'] = $company_id;
            $data['created_by'] = $created_by;
            $data['originator'] = $originator;

            $res = $callConfig->insertRecord('csr_invoice_details', $data);
            if (isset($res['status']) && $res['status'] == 'sucess') {
                $inserted++;
            } else {
                $errors[] = array(
                    'row' => $i + 1,
                    'message' => $res['message'] ?? 'Failed to insert row'
                );
            }
        }

        if (count((array)$errors) > 0) {
            return array(
                'status' => 'partial',
                'message' => $inserted . ' row(s) inserted. ' . count($errors) . ' row(s) failed.',
                'inserted' => $inserted,
                'errors' => $errors,
                'missing_po_rows' => $missing_po_rows
            );
        }

        return array(
            'status' => 'success',
            'message' => $inserted . ' row(s) inserted successfully.',
            'inserted' => $inserted,
            'missing_po_rows' => $missing_po_rows
        );
    }
    function getCsrDashboardProposalScope($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        
        // Client request: all CSR dashboard users see full company data (admin scope).
        // Set to false when role-based filtering is needed again.
        $force_admin_scope = true;
        if ($force_admin_scope) {
            $project_id_subquery = "SELECT id FROM csr_proposal_data WHERE company_id = '".$company_id."' AND is_active = 0";
            return array(
                'is_admin' => true,
                'base_scope' => '',
                'proposal_where' => '',
                'project_id_subquery' => $project_id_subquery,
                'po_project_scope' => " AND project_id IN (".$project_id_subquery.")"
            );
        }


        $user_id = isset($get['user_id']) ? $callConfig->real_escape_string($get['user_id']) : '';
        $role = isset($get['working_role_short_code']) ? $get['working_role_short_code'] : '';

        $is_admin = in_array($role, array('ADMIN', 'CSR_ADMIN'), true);
        $base_scope = '';

        if (!$is_admin) {
            if (in_array($role, array('CSR_CREATER', 'CSR_CREATOR'), true) && $user_id != '') {
                $base_scope = " AND orgininaor = '".$user_id."'";
            } elseif (in_array($role, array('CSR_COMMITTEE_GROUP', 'CSR_COMMITTEE_MEMBER', 'CSR_COMMITTEE_HEAD'), true) && $user_id != '') {
                $base_scope = " AND id IN (
                    SELECT tal.transaction_id FROM transaction_approval_logs tal
                    WHERE tal.user_id = '".$user_id."'
                    AND tal.transaction_table = 'csr_proposal_data'
                    AND tal.response_type != 0
                    AND tal.company_id = '".$company_id."'
                )";
            } else {
                $base_scope = " AND 1=0";
            }
        }

        $project_id_subquery = "SELECT id FROM csr_proposal_data WHERE company_id = '".$company_id."' AND is_active = 0".$base_scope;
        $proposal_where = '';
        if ($base_scope != '') {
            $proposal_where = str_replace('orgininaor', 'p.orgininaor', str_replace(' AND id IN', ' AND p.id IN', $base_scope));
        }

        return array(
            'is_admin' => $is_admin,
            'base_scope' => $base_scope,
            'proposal_where' => $proposal_where,
            'project_id_subquery' => $project_id_subquery,
            'po_project_scope' => " AND project_id IN (".$project_id_subquery.")"
        );
    }
    
    function getCsrFirstDashboardData($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);
        $proposal_scope = $scope['proposal_where'];
        $base_scope = $scope['base_scope'];
        $po_project_scope = $scope['po_project_scope'];
        $is_admin_scope = $scope['is_admin'];

        $fy_query = "SELECT id, genc_detail_code, genc_detail_name
            FROM general_code_details
            WHERE company_id = '".$company_id."'
            AND genc_code = 'CSR_FINANCIAL_YEAR'
            AND is_active = 0
            ORDER BY id ASC";
        $fy_rows = $callConfig->getAllRows($fy_query);

        $selected_fy_index = -1;
        if ($selected_fy == '' && count((array)$fy_rows) > 0) {
            $selected_fy = $fy_rows[count($fy_rows) - 1]->genc_detail_code;
        }
        foreach ($fy_rows as $index => $fy_row) {
            if ($fy_row->genc_detail_code == $selected_fy) {
                $selected_fy_index = $index;
                break;
            }
        }
        if ($selected_fy_index === -1 && count((array)$fy_rows) > 0) {
            $selected_fy_index = count($fy_rows) - 1;
            $selected_fy = $fy_rows[$selected_fy_index]->genc_detail_code;
        }

        $yearly_fy_where = "";
        if ($selected_fy_index !== -1) {
            $yearly_fy_codes = array();
            $yearly_start_index = max($selected_fy_index - 4, 0);
            for ($i = $yearly_start_index; $i <= $selected_fy_index; $i++) {
                $yearly_fy_codes[] = "'" . $callConfig->real_escape_string($fy_rows[$i]->genc_detail_code) . "'";
            }
            if (count((array)$yearly_fy_codes) > 0) {
                $yearly_fy_where = " AND genc_detail_code IN (" . implode(',', $yearly_fy_codes) . ")";
            }
        }

        $yearly_query = "SELECT fy.financial_year,
                IFNULL(ob.obligation, 0) AS obligation,
                IFNULL(po.approved_amount, 0) AS approved_amount,
                IFNULL(inv.spent_amount, 0) AS spent_amount
            FROM (
                SELECT genc_detail_code AS financial_year, id
                FROM general_code_details
                WHERE company_id = '".$company_id."'
                AND genc_code = 'CSR_FINANCIAL_YEAR'
                AND is_active = 0".$yearly_fy_where."
            ) fy
            LEFT JOIN (
                SELECT financial_year, ROUND(AVG(net_profit_before_tax_mtipl) * 0.02, 2) AS obligation
                FROM (
                    SELECT c.financial_year, c.net_profit_before_tax_mtipl,
                        (SELECT COUNT(*) FROM csr_complaince c2
                            WHERE c2.company_id = c.company_id
                            AND c2.is_active = 0
                            AND c2.financial_year < c.financial_year
                            AND c2.financial_year >= CONCAT(CAST(SUBSTRING(c.financial_year, 1, 4) AS UNSIGNED) - 3, '-', LPAD((CAST(SUBSTRING(c.financial_year, 1, 4) AS UNSIGNED)) % 100, 2, '0'))
                        ) AS previous_year_count
                    FROM csr_complaince c
                    WHERE c.company_id = '".$company_id."'
                    AND c.is_active = 0
                ) c
                WHERE previous_year_count >= 0
                GROUP BY financial_year
            ) ob ON ob.financial_year COLLATE utf8mb4_general_ci = fy.financial_year COLLATE utf8mb4_general_ci
            LEFT JOIN (
                SELECT CASE
                        WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                        ELSE financial_year
                    END AS financial_year,
                    SUM(IFNULL(po_value_in, 0)) AS approved_amount
                FROM csr_po_details
                WHERE company_id = '".$company_id."'
                AND is_active = 0".$po_project_scope."
                GROUP BY CASE
                    WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                    ELSE financial_year
                END
            ) po ON po.financial_year COLLATE utf8mb4_general_ci = fy.financial_year COLLATE utf8mb4_general_ci
            LEFT JOIN (
                SELECT CASE
                        WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                        ELSE financial_year
                    END AS financial_year,
                    SUM(IFNULL(total_spent, 0)) AS spent_amount
                FROM csr_invoice_details
                WHERE company_id = '".$company_id."'
                AND is_active = 0".$po_project_scope."
                GROUP BY CASE
                    WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                    ELSE financial_year
                END
            ) inv ON inv.financial_year COLLATE utf8mb4_general_ci = fy.financial_year COLLATE utf8mb4_general_ci
            ORDER BY fy.id ASC";
        $yearly_rows = $callConfig->getAllRows($yearly_query);
        $return['yearly_query'] = $yearly_query; 

        if ($is_admin_scope) {
            $budget_query = "SELECT financial_year, net_profit_before_tax_mtipl
                FROM csr_complaince
                WHERE company_id = '".$company_id."'
                AND is_active = 0";
            $budget_rows = $callConfig->getAllRows($budget_query);
            $budgets = array();
            foreach ($budget_rows as $budget_row) {
                $parts = explode('-', $budget_row->financial_year);
                $end_year = isset($parts[1]) ? (int)('20' . substr($parts[1], -2)) : 0;
                if ($end_year > 0) {
                    $budgets[$end_year] = (float)$budget_row->net_profit_before_tax_mtipl;
                }
            }
            foreach ($yearly_rows as $yearly_row) {
                $parts = explode('-', $yearly_row->financial_year);
                $end_year = isset($parts[1]) ? (int)('20' . substr($parts[1], -2)) : 0;
                if (
                    isset($budgets[$end_year - 1]) &&
                    isset($budgets[$end_year - 2]) &&
                    isset($budgets[$end_year - 3])
                ) {
                    $yearly_row->obligation = round((($budgets[$end_year - 1] + $budgets[$end_year - 2] + $budgets[$end_year - 3]) / 3) * 0.02, 2);
                } else {
                    $yearly_row->obligation = 0;
                }
            }
        } else {
            foreach ($yearly_rows as $yearly_row) {
                $yearly_row->obligation = 0;
            }
        }

        if ($selected_fy == '') {
            for ($i = count($yearly_rows) - 1; $i >= 0; $i--) {
                if ((float)$yearly_rows[$i]->obligation > 0 || (float)$yearly_rows[$i]->approved_amount > 0 || (float)$yearly_rows[$i]->spent_amount > 0) {
                    $selected_fy = $yearly_rows[$i]->financial_year;
                    break;
                }
            }
            if ($selected_fy == '' && count((array)$fy_rows) > 0) {
                $selected_fy = $fy_rows[count($fy_rows) - 1]->genc_detail_code;
            }
        }

        $selected_row = array('obligation' => 0, 'approved_amount' => 0, 'spent_amount' => 0);
        $selected_year_index = -1;
        foreach ($yearly_rows as $index => $row) {
            if ($row->financial_year == $selected_fy) {
                $selected_row = array(
                    'obligation' => (float)$row->obligation,
                    'approved_amount' => (float)$row->approved_amount,
                    'spent_amount' => (float)$row->spent_amount
                );
                $selected_year_index = $index;
                break;
            }
        }
        $chart_yearly_rows = $yearly_rows;
        if ($selected_year_index !== -1) {
            $chart_yearly_rows = array_slice($yearly_rows, max($selected_year_index - 4, 0), 5);
        }

        $proposal_fy_where = $selected_fy != '' ? " AND p.financial_year = '".$selected_fy."'" : "";
        $po_fy_where = $selected_fy != '' ? " AND (CASE WHEN po.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(po.financial_year, 4), '-', RIGHT(po.financial_year, 2)) ELSE po.financial_year END) = '".$selected_fy."'" : "";
        $inv_fy_where = $selected_fy != '' ? " AND (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) = '".$selected_fy."'" : "";

        $status_query = "SELECT
                COUNT(1) AS total_project,
                SUM(CASE WHEN p.current_status = 0 THEN 1 ELSE 0 END) AS active_project,
                SUM(CASE WHEN p.current_status != 0 THEN IFNULL(p.total_budget_grand_total, 0) ELSE 0 END) AS active_project_amount,
                SUM(CASE WHEN p.current_status = ".ST_APPROVED." THEN IFNULL(p.total_budget_grand_total, 0) ELSE 0 END) AS project_approved_amount,
                SUM(CASE WHEN p.current_status > 100 AND p.current_status < ".ST_APPROVED." THEN IFNULL(p.total_budget_grand_total, 0) ELSE 0 END) AS approval_in_progress_amount,
                SUM(CASE WHEN p.current_status = ".ST_APPROVED." AND p.tracking_status = 0 THEN 1 ELSE 0 END) AS on_track_project,
                SUM(CASE WHEN p.current_status = ".ST_APPROVED." AND p.tracking_status = 1 THEN 1 ELSE 0 END) AS completed_project,
                COUNT(DISTINCT CASE WHEN p.working_area IS NOT NULL THEN p.working_area END) AS sectors_covered,
                COUNT(DISTINCT CASE WHEN p.csr_location IS NOT NULL THEN p.csr_location END) AS locations_covered
            FROM csr_proposal_data p
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0".$proposal_fy_where.$proposal_scope;
        $status_row = $callConfig->getRow($status_query);

        $sector_query = "SELECT IFNULL(cas.csr_area, 'Not Mapped') AS label,
                SUM(IFNULL(p.total_budget_grand_total, 0)) AS allocation_amount,
                SUM(IFNULL(po.po_value_in, 0)) AS approved_amount,
                SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
            FROM csr_proposal_data p
            LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0
            LEFT JOIN csr_po_details po ON po.project_id = p.id AND po.company_id = p.company_id AND po.is_active = 0".$po_fy_where."
            LEFT JOIN csr_invoice_details inv ON inv.csr_po_details_id = po.id AND inv.company_id = p.company_id AND inv.is_active = 0".$inv_fy_where."
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0
            AND p.current_status = ".ST_APPROVED.$proposal_fy_where.$proposal_scope."
            GROUP BY IFNULL(cas.csr_area, 'Not Mapped')
            HAVING allocation_amount > 0 OR approved_amount > 0 OR spent_amount > 0
            ORDER BY allocation_amount DESC";
        $sector_rows = $callConfig->getAllRows($sector_query);
        // $return['sector_query'] = $sector_query;

        $location_query = "SELECT IFNULL(NULLIF(loc.location_name, ''), 'Not Mapped') AS label,
                SUM(IFNULL(p.total_budget_grand_total, 0)) AS allocation_amount,
                SUM(IFNULL(po.po_value_in, 0)) AS approved_amount
            FROM csr_proposal_data p
            LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0
            LEFT JOIN csr_po_details po ON po.project_id = p.id AND po.company_id = p.company_id AND po.is_active = 0".$po_fy_where."
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0
            AND p.current_status = ".ST_APPROVED.$proposal_fy_where.$proposal_scope."
            GROUP BY IFNULL(NULLIF(loc.location_name, ''), 'Not Mapped')
            HAVING allocation_amount > 0 OR approved_amount > 0
            ORDER BY allocation_amount DESC";
        $location_rows = $callConfig->getAllRows($location_query);

        $at_risk_query = "SELECT COUNT(*) AS at_risk_project
            FROM csr_proposal_data p
            LEFT JOIN (
                SELECT proposal_id, ROUND((SUM(IF(status = 'completed', 1, 0)) / COUNT(id)) * 100) AS percent
                FROM csr_project_tracking
                WHERE company_id = '".$company_id."'
                AND is_active = 0
                AND proposal_id IN (".$scope['project_id_subquery'].")
                GROUP BY proposal_id
            ) tr ON tr.proposal_id = p.id
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0
            AND p.current_status = ".ST_APPROVED."
            AND p.tracking_status = 0
            AND IFNULL(tr.percent, 0) < 50".$proposal_fy_where.$proposal_scope;
        $at_risk_row = $callConfig->getRow($at_risk_query);

        $approved_projects_query = "SELECT p.id, p.transaction_no, p.subject, p.institue_name, p.financial_year, p.total_budget_grand_total
            FROM csr_proposal_data p
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0
            AND p.current_status = ".ST_APPROVED.$proposal_fy_where.$proposal_scope."
            ORDER BY p.id DESC";
        $approved_projects = $callConfig->getAllRows($approved_projects_query);

        $rejected_projects_query = "SELECT p.id, p.transaction_no, p.subject, p.institue_name, p.financial_year, p.total_budget_grand_total
            FROM csr_proposal_data p
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0
            AND p.current_status = ".ST_REJECTED.$proposal_fy_where.$proposal_scope."
            ORDER BY p.id DESC";
        $rejected_projects = $callConfig->getAllRows($rejected_projects_query);

        $unmapped_query = "SELECT
                (SELECT COUNT(*) FROM csr_proposal_data WHERE company_id = '".$company_id."' AND is_active = 0 AND financial_year IS NULL".$base_scope.") AS proposal_missing_fy,
                (SELECT COUNT(*) FROM csr_po_details WHERE company_id = '".$company_id."' AND is_active = 0 AND financial_year IS NULL".$po_project_scope.") AS po_missing_fy,
                (SELECT COUNT(*) FROM csr_invoice_details WHERE company_id = '".$company_id."' AND is_active = 0 AND financial_year IS NULL".$po_project_scope.") AS invoice_missing_fy";
        $unmapped_row = $callConfig->getRow($unmapped_query);

        $return['financial_years'] = $fy_rows;
        $return['selected_financial_year'] = $selected_fy;
        $project_approved_amount = isset($status_row->project_approved_amount) ? (float)$status_row->project_approved_amount : 0;
        if ($is_admin_scope) {
            $pending_obligation = max(((float)$selected_row['obligation'] - $project_approved_amount), 0);
            $unspent_amount = max(((float)$selected_row['obligation'] - (float)$selected_row['spent_amount']), 0);
            $compliance_status = ((float)$selected_row['obligation'] > 0 && (float)$selected_row['spent_amount'] >= (float)$selected_row['obligation']) ? 'Compliant' : 'Pending';
        } else {
            $pending_obligation = max(($project_approved_amount - (float)$selected_row['spent_amount']), 0);
            $unspent_amount = $pending_obligation;
            $compliance_status = ($project_approved_amount > 0 && (float)$selected_row['spent_amount'] >= $project_approved_amount) ? 'Compliant' : 'Pending';
        }
        $return['summary'] = array(
            'total_obligation' => $is_admin_scope ? (float)$selected_row['obligation'] : 0,
            'project_approved' => $project_approved_amount,
            'pending_obligation' => $pending_obligation,
            'approved_in_progress' => isset($status_row->approval_in_progress_amount) ? (float)$status_row->approval_in_progress_amount : 0,
            'spent' => (float)$selected_row['spent_amount'],
            'unspent' => $unspent_amount,
            'total_project' => isset($status_row->total_project) ? (int)$status_row->total_project : 0,
            'active_project' => isset($status_row->active_project) ? (int)$status_row->active_project : 0,
            'on_track_project' => isset($status_row->on_track_project) ? (int)$status_row->on_track_project : 0,
            'at_risk_project' => isset($at_risk_row->at_risk_project) ? (int)$at_risk_row->at_risk_project : 0,
            'sectors_covered' => count((array)$sector_rows),
            'locations_covered' => count((array)$location_rows),
            'compliance_status' => $compliance_status,
            'active_project_amount' => isset($status_row->active_project_amount) ? (float)$status_row->active_project_amount : 0,
        );
        $return['yearly_rows'] = $chart_yearly_rows;
        $return['sector_rows'] = $sector_rows;
        $return['location_rows'] = $location_rows;
        $return['approved_projects'] = $approved_projects;
        $return['rejected_projects'] = $rejected_projects;
        $return['unmapped'] = $unmapped_row;

        return $return;
    }

    function getCsrProjectMonitoringDashboardData($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);
        $is_admin_scope = $scope['is_admin'];
        $proposal_scope = $scope['proposal_where'];
        $po_project_scope = $scope['po_project_scope'];

        $fy_query = "SELECT id, genc_detail_code, genc_detail_name
            FROM general_code_details
            WHERE company_id = '".$company_id."'
            AND genc_code = 'CSR_FINANCIAL_YEAR'
            AND is_active = 0
            ORDER BY id ASC";
        $fy_rows = $callConfig->getAllRows($fy_query);

        $selected_fy_index = -1;
        if ($selected_fy == '' && count((array)$fy_rows) > 0) {
            $selected_fy = $fy_rows[count($fy_rows) - 1]->genc_detail_code;
        }
        foreach ($fy_rows as $index => $fy_row) {
            if ($fy_row->genc_detail_code == $selected_fy) {
                $selected_fy_index = $index;
                break;
            }
        }
        if ($selected_fy_index === -1 && count((array)$fy_rows) > 0) {
            $selected_fy_index = count($fy_rows) - 1;
            $selected_fy = $fy_rows[$selected_fy_index]->genc_detail_code;
        }

        $previous_fy = '';
        if ($selected_fy_index > 0 && isset($fy_rows[$selected_fy_index - 1])) {
            $previous_fy = $fy_rows[$selected_fy_index - 1]->genc_detail_code;
        }

        $proposal_fy_where = $selected_fy != '' ? " AND p.financial_year = '".$selected_fy."'" : "";
        $previous_proposal_fy_where = $previous_fy != '' ? " AND p.financial_year = '".$previous_fy."'" : " AND 1=0";
        $selected_inv_fy_where = $selected_fy != '' ? " AND (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) = '".$selected_fy."'" : "";
        $previous_inv_fy_where = $previous_fy != '' ? " AND (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) = '".$previous_fy."'" : " AND 1=0";

        $project_query = "SELECT
                p.id,
                p.subject,
                p.financial_year,
                p.project_start_date,
                p.project_end_date,
                p.total_budget_grand_total,
                p.current_status,
                p.tracking_status,
                IFNULL(tow.type_of_work, '-') AS type_of_work,
                IFNULL(ged.genc_detail_name, '-') AS type_of_project,
                IFNULL(cas.csr_area, '-') AS sector,
                IFNULL(NULLIF(loc.location_name, ''), '-') AS location_name,
                IFNULL(tr.total_tracking, 0) AS total_tracking,
                IFNULL(tr.completed_tracking, 0) AS completed_tracking,
                IFNULL(tr.yet_to_start_tracking, 0) AS yet_to_start_tracking,
                IFNULL(tr.inprogress_tracking, 0) AS inprogress_tracking,
                IFNULL(tr.hold_tracking, 0) AS hold_tracking,
                IFNULL(inv_selected.spent_amount, 0) AS selected_spent_amount,
                IFNULL(inv_previous.spent_amount, 0) AS previous_spent_amount
            FROM csr_proposal_data p
            LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work AND tow.company_id = p.company_id AND tow.is_active = 0
            LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0
            LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0
            LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0
            LEFT JOIN (SELECT proposal_id,
                    COUNT(id) AS total_tracking,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_tracking,
                    SUM(CASE WHEN status = 'started' THEN 1 ELSE 0 END) AS inprogress_tracking,
                    SUM(CASE WHEN status = 'hold' THEN 1 ELSE 0 END) AS hold_tracking,
                    SUM(CASE WHEN status IS NULL OR status = '' OR status = 'not_started' THEN 1 ELSE 0 END) AS yet_to_start_tracking
                FROM csr_project_tracking
                WHERE company_id = '".$company_id."'
                AND is_active = 0
                AND proposal_id IN (".$scope['project_id_subquery'].")
                GROUP BY proposal_id
            ) tr ON tr.proposal_id = p.id
            LEFT JOIN (
                SELECT inv.project_id, SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
                FROM csr_invoice_details inv
                WHERE inv.company_id = '".$company_id."'
                AND inv.is_active = 0".$selected_inv_fy_where.$po_project_scope."
                GROUP BY inv.project_id
            ) inv_selected ON inv_selected.project_id = p.id
            LEFT JOIN (
                SELECT inv.project_id, SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
                FROM csr_invoice_details inv
                WHERE inv.company_id = '".$company_id."'
                AND inv.is_active = 0".$previous_inv_fy_where.$po_project_scope."
                GROUP BY inv.project_id
            ) inv_previous ON inv_previous.project_id = p.id
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0".$proposal_fy_where.$proposal_scope."
            ORDER BY p.id DESC";
        $project_rows = $callConfig->getAllRows($project_query);

        $projects = array();
        $today = date('Y-m-d');
        $approved_status = (int)ST_APPROVED;
        $rejected_status = (int)ST_REJECTED;
        foreach ($project_rows as $project_row) {
            $current_status = (int)$project_row->current_status;
            $project_row->selected_approved_amount = ($current_status === $approved_status) ? (float)$project_row->total_budget_grand_total : 0;
            $project_row->previous_approved_amount = 0;
            if ($current_status === 0) {
                $project_row->progress = 0;
                $project_row->status_label = 'Draft';
                $project_row->status_class = 'btn-warning';
                $project_row->progress_class = 'bg-warning';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            if ($current_status === $rejected_status) {
                $project_row->progress = 0;
                $project_row->status_label = 'Rejected';
                $project_row->status_class = 'btn-danger';
                $project_row->progress_class = 'bg-danger';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            if ($current_status === (int)ST_CANCEL) {
                $project_row->progress = 0;
                $project_row->status_label = 'Cancelled';
                $project_row->status_class = 'btn-secondary';
                $project_row->progress_class = 'bg-secondary';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            if ($current_status === (int)ST_SEND_BACK) {
                $project_row->progress = 0;
                $project_row->status_label = 'Sent Back';
                $project_row->status_class = 'btn-warning';
                $project_row->progress_class = 'bg-warning';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            if ($current_status > 100 && $current_status < $approved_status) {
                $project_row->progress = 0;
                $project_row->status_label = 'In Progress';
                $project_row->status_class = 'btn-primary';
                $project_row->progress_class = 'bg-primary';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            if ($current_status !== $approved_status) {
                $project_row->progress = 0;
                $project_row->status_label = 'Pending Approval';
                $project_row->status_class = 'btn-warning';
                $project_row->progress_class = 'bg-warning';
                $project_row->has_tracking = 0;
                $projects[] = $project_row;
                continue;
            }
            $total_tracking = (int)$project_row->total_tracking;
            $completed_tracking = (int)$project_row->completed_tracking;
            $progress = $total_tracking > 0 ? round(($completed_tracking / $total_tracking) * 100) : ((int)$project_row->tracking_status == 1 ? 100 : 0);
            $progress = min(max($progress, 0), 100);
            $status_label = 'On Track';
            $status_class = 'btn-success';
            if ($progress < 50 || ($project_row->project_end_date != '' && $project_row->project_end_date < $today && $progress < 100)) {
                $status_label = 'At Risk';
                $status_class = 'btn-danger';
            } elseif ($progress < 75) {
                $status_label = 'On Watch';
                $status_class = 'btn-warning';
            }
            if ($progress >= 100 || (int)$project_row->tracking_status == 1) {
                $status_label = 'Completed';
                $status_class = 'btn-success';
                $progress = 100;
            }

            $project_row->progress = $progress;
            $project_row->status_label = $status_label;
            $project_row->status_class = $status_class;
            $project_row->progress_class = $status_class == 'btn-danger' ? 'progress-bar-danger' : ($status_class == 'btn-warning' ? 'bg-warning' : 'progress-bar-success');
            $project_row->has_tracking = 1;
            $projects[] = $project_row;
        }
        $selected_obligation = 0;
        if ($is_admin_scope) {
            $selected_fy_parts = explode('-', (string)$selected_fy);
            $selected_end_year = isset($selected_fy_parts[1]) ? (int)('20' . substr((string)$selected_fy_parts[1], -2)) : 0;
            if ($selected_end_year > 0) {
                $budget_query = "SELECT financial_year, net_profit_before_tax_mtipl
                    FROM csr_complaince
                    WHERE company_id = '".$company_id."'
                    AND is_active = 0";
                $budget_rows = $callConfig->getAllRows($budget_query);
                $budgets = array();
                if (count((array)$budget_rows) > 0) {
                    foreach ($budget_rows as $budget_row) {
                        $parts = explode('-', (string)$budget_row->financial_year);
                        $end_year = isset($parts[1]) ? (int)('20' . substr((string)$parts[1], -2)) : 0;
                        if ($end_year > 0) {
                            $budgets[$end_year] = (float)$budget_row->net_profit_before_tax_mtipl;
                        }
                    }
                }
                if (
                    isset($budgets[$selected_end_year - 1]) &&
                    isset($budgets[$selected_end_year - 2]) &&
                    isset($budgets[$selected_end_year - 3])
                ) {
                    $selected_obligation = round((($budgets[$selected_end_year - 1] + $budgets[$selected_end_year - 2] + $budgets[$selected_end_year - 3]) / 3) * 0.02, 2);
                }
            }
        }
        $summary_query = "SELECT
                (SELECT COUNT(*) FROM csr_proposal_data p WHERE p.company_id = '".$company_id."' AND p.is_active = 0".$proposal_fy_where.$proposal_scope.") AS total_project,

                (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '".$company_id."' AND p.is_active = 0 AND p.current_status = ".ST_APPROVED.$proposal_fy_where.$proposal_scope.") AS selected_approval_amount,
                (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '".$company_id."' AND p.is_active = 0 AND p.current_status = ".ST_APPROVED.$previous_proposal_fy_where.$proposal_scope.") AS previous_approval_amount,
                (SELECT IFNULL(SUM(inv.total_spent), 0) FROM csr_invoice_details inv WHERE inv.company_id = '".$company_id."' AND inv.is_active = 0".$selected_inv_fy_where.$po_project_scope.") AS selected_spent_amount,
                (SELECT IFNULL(SUM(inv.total_spent), 0) FROM csr_invoice_details inv WHERE inv.company_id = '".$company_id."' AND inv.is_active = 0".$previous_inv_fy_where.$po_project_scope.") AS previous_spent_amount,
                (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '".$company_id."' AND p.is_active = 0 AND p.current_status > 100 AND p.current_status < ".ST_APPROVED.$proposal_fy_where.$proposal_scope.") AS board_approval_pending,
                (SELECT COUNT(*) FROM csr_proposal_data p LEFT JOIN (SELECT proposal_id, COUNT(id) AS total_tracking, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_tracking FROM csr_project_tracking WHERE company_id = '".$company_id."' AND is_active = 0 AND proposal_id IN (".$scope['project_id_subquery'].") GROUP BY proposal_id) tr ON tr.proposal_id = p.id WHERE p.company_id = '".$company_id."' AND p.is_active = 0 AND p.current_status = ".ST_APPROVED." AND p.tracking_status = 0 AND (IFNULL(tr.total_tracking, 0) = 0 OR ROUND((IFNULL(tr.completed_tracking, 0) / tr.total_tracking) * 100) < 50)".$proposal_fy_where.$proposal_scope.") AS delayed_project,
                (SELECT COUNT(*) FROM csr_invoice_details inv WHERE inv.company_id = '".$company_id."' AND inv.is_active = 0 AND (inv.paid_amount IS NULL OR inv.paid_amount = 0)".$selected_inv_fy_where.$po_project_scope.") AS utilization_certificate_pending";
        $summary_row = $callConfig->getRow($summary_query);

        $impact_fy_where = $selected_fy != '' ? " AND (CASE WHEN cis.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(cis.financial_year, 4), '-', RIGHT(cis.financial_year, 2)) ELSE cis.financial_year END) = '".$selected_fy."'" : "";
        $impact_query = "SELECT
                IFNULL(SUM(cis.student_supported), 0) AS student_supported,
                IFNULL(SUM(cis.villages_covered), 0) AS villages_covered,
                IFNULL(SUM(cis.health_camps_conducted), 0) AS health_camps_conducted,
                IFNULL(SUM(cis.trees_planted), 0) AS trees_planted,
                IFNULL(SUM(cis.women_benefited), 0) AS women_benefited
            FROM csr_impact_snapshot cis
            WHERE cis.company_id = '".$company_id."'
            AND cis.is_active = 0".$impact_fy_where."
            AND cis.project_id IN (".$scope['project_id_subquery'].")
            GROUP BY (CASE WHEN cis.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(cis.financial_year, 4), '-', RIGHT(cis.financial_year, 2)) ELSE cis.financial_year END)";
        $impact_row = $callConfig->getRow($impact_query);

        $selected_approval = isset($summary_row->selected_approval_amount) ? (float)$summary_row->selected_approval_amount : 0;
        $selected_spent = isset($summary_row->selected_spent_amount) ? (float)$summary_row->selected_spent_amount : 0;
        $unallocated = max($selected_approval - $selected_spent, 0);
        $utilization_percent = $selected_approval > 0 ? round(($selected_spent / $selected_approval) * 100, 2) : 0;

        $insights = array();
        $insights[] = "CSR utilization is at ".$utilization_percent."% for ".($selected_fy ?: "selected FY").".";
        if (count((array)$projects) > 0) {
            foreach ($projects as $project_row) {
                if ($project_row->status_label == 'At Risk') {
                    $insights[] = ($project_row->subject ?: 'A project')." is at risk of delay. Monitor to avoid timeline slippage.";
                    break;
                }
            }
        }
        if ($unallocated > 0) {
            $insights[] = "Unallocated or unspent project funds are available for review.";
        }
        if (count((array)$projects) == 0) {
            $insights[] = "No projects found for the selected financial year.";
        }

        $return['financial_years'] = $fy_rows;
        $return['selected_financial_year'] = $selected_fy;
        $return['previous_financial_year'] = $previous_fy;
        $return['projects'] = $projects;
        $return['summary'] = array(
            'total_project' => isset($summary_row->total_project) ? (int)$summary_row->total_project : 0,
            'total_obligation' => (float)$selected_obligation,
            'selected_approval_amount' => $selected_approval,
            'previous_approval_amount' => isset($summary_row->previous_approval_amount) ? (float)$summary_row->previous_approval_amount : 0,
            'selected_spent_amount' => $selected_spent,
            'previous_spent_amount' => isset($summary_row->previous_spent_amount) ? (float)$summary_row->previous_spent_amount : 0,
            'board_approval_pending' => isset($summary_row->board_approval_pending) ? (float)$summary_row->board_approval_pending : 0,
            'delayed_project' => isset($summary_row->delayed_project) ? (int)$summary_row->delayed_project : 0,
            'unallocated_funds' => $unallocated,
            'utilization_certificate_pending' => isset($summary_row->utilization_certificate_pending) ? (int)$summary_row->utilization_certificate_pending : 0,
            'utilization_percent' => $utilization_percent
        );
        $return['impact'] = array(
            'student_supported' => isset($impact_row->student_supported) ? (int)$impact_row->student_supported : 0,
            'villages_covered' => isset($impact_row->villages_covered) ? (int)$impact_row->villages_covered : 0,
            'health_camps_conducted' => isset($impact_row->health_camps_conducted) ? (int)$impact_row->health_camps_conducted : 0,
            'trees_planted' => isset($impact_row->trees_planted) ? (int)$impact_row->trees_planted : 0,
            'women_benefited' => isset($impact_row->women_benefited) ? (int)$impact_row->women_benefited : 0
        );
        $return['insights'] = $insights;
        $query = "SELECT p.financial_year, p.subject, p.total_budget_grand_total, c.financial_year AS c_financial_year, SUM(c.total_spent) AS total_spent FROM csr_invoice_details c INNER JOIN csr_proposal_data p ON c.project_id = p.id WHERE p.financial_year = '".$get['financial_year']."'  AND c.financial_year = '".$get['financial_year']."' GROUP BY c.project_id, p.financial_year, p.subject, p.total_budget_grand_total, c.financial_year";
        $out_rows = $callConfig->getAllRows($query);
        $return['out_rows'] = $out_rows;

        return $return;
    }
    function getCsrImpactSnapshotData($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $rid = isset($get['rid']) ? (int)$get['rid'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $requested_fy = $selected_fy;
        $return = array();

        $fy_query = "SELECT id, genc_detail_code, genc_detail_name
            FROM general_code_details
            WHERE company_id = '".$company_id."'
            AND genc_code = 'CSR_FINANCIAL_YEAR'
            AND is_active = 0
            ORDER BY id ASC";
        $fy_rows = $callConfig->getAllRows($fy_query);
        $financial_years = $fy_rows;

        if (count((array)$financial_years) == 0) {
            $current_fy_start = (int)date('n') >= 4 ? (int)date('Y') : ((int)date('Y') - 1);
            for ($year = $current_fy_start - 5; $year <= $current_fy_start; $year++) {
                $financial_years[] = array(
                    'id' => $year,
                    'genc_detail_code' => $year . '-' . substr((string)($year + 1), -2),
                    'genc_detail_name' => $year . '-' . substr((string)($year + 1), -2)
                );
            }
        }

        if ($selected_fy == '' && count((array)$financial_years) > 0) {
            $last_fy = $financial_years[count($financial_years) - 1];
            $selected_fy = is_array($last_fy) ? $last_fy['genc_detail_code'] : $last_fy->genc_detail_code;
        }

        $project_query = "SELECT id, subject, financial_year, project_start_date, project_end_date, total_budget_grand_total
            FROM csr_proposal_data
            WHERE company_id = '".$company_id."'
            AND is_active = 0
            AND current_status = ".ST_APPROVED."
            ORDER BY subject ASC";
        $project_rows = $callConfig->getAllRows($project_query);
        $projects = array();
        $projects_by_year = array();

        foreach ($project_rows as $project_row) {
            $project_year = trim((string)$project_row->financial_year);
            $project_year_keys = array($project_year);

            if (preg_match('/^([0-9]{4})-([0-9]{4})$/', $project_year, $matches)) {
                $project_year_keys[] = $matches[1] . '-' . substr($matches[2], -2);
            } elseif (preg_match('/^([0-9]{4})-([0-9]{2})$/', $project_year, $matches)) {
                $project_year_keys[] = $matches[1] . '-20' . $matches[2];
            }

            foreach (array_unique($project_year_keys) as $project_year_key) {
                if ($project_year_key == '') {
                    continue;
                }
                if (!isset($projects_by_year[$project_year_key])) {
                    $projects_by_year[$project_year_key] = array();
                }
                $projects_by_year[$project_year_key][] = $project_row;
            }

            if ($selected_fy == '' || $project_year == $selected_fy) {
                $projects[] = $project_row;
            }
        }

        if ($requested_fy == '' && !isset($projects_by_year[$selected_fy])) {
            for ($fy_index = count((array)$financial_years) - 1; $fy_index >= 0; $fy_index--) {
                $fy_row = $financial_years[$fy_index];
                $fy_code = is_array($fy_row) ? $fy_row['genc_detail_code'] : $fy_row->genc_detail_code;
                if (isset($projects_by_year[$fy_code])) {
                    $selected_fy = $fy_code;
                    break;
                }
            }
        }
        $projects = $selected_fy != '' && isset($projects_by_year[$selected_fy]) ? $projects_by_year[$selected_fy] : $projects;

        $record_query = "SELECT cis.*, p.subject AS project_name
            FROM csr_impact_snapshot cis
            LEFT JOIN csr_proposal_data p ON p.id = cis.project_id AND p.company_id = cis.company_id
            WHERE cis.company_id = '".$company_id."'
            AND cis.is_active = 0
            ORDER BY cis.id DESC";
        $record_rows = $callConfig->getAllRows($record_query);
        $impact_snapshots = array();
        foreach ($record_rows as $record_row) {
            $impact_snapshots[$record_row->id] = $record_row;
        }

        $impact_snapshot = array();
        if ($rid > 0) {
            $single_query = "SELECT cis.*, p.subject AS project_name
                FROM csr_impact_snapshot cis
                LEFT JOIN csr_proposal_data p ON p.id = cis.project_id AND p.company_id = cis.company_id
                WHERE cis.company_id = '".$company_id."'
                AND cis.id = ".$rid."
                AND cis.is_active = 0";
            $single_row = $callConfig->getRow($single_query);
            if (!empty($single_row)) {
                $impact_snapshot = $single_row;
                $selected_fy = $single_row->financial_year;
            }
        }

        $return['financial_years'] = $financial_years;
        $return['selected_financial_year'] = $selected_fy;
        $return['projects'] = $projects;
        $return['projects_by_year'] = $projects_by_year;
        $return['impact_snapshots'] = $impact_snapshots;
        $return['impact_snapshot'] = $impact_snapshot;

        return $return;
    }
    function getCsrDashboardPopupProjects($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $type = isset($get['type']) ? $callConfig->real_escape_string($get['type']) : '';
        $return = array('projects' => array());

        if ($company_id == 0 || $type == '') {
            return $return;
        }

        $scope = $this->getCsrDashboardProposalScope($get);
        $proposal_scope = $scope['proposal_where'];
        $po_project_scope = $scope['po_project_scope'];
        $proposal_fy_where = $selected_fy != '' ? " AND p.financial_year = '".$selected_fy."'" : "";
        $po_fy_where = $selected_fy != '' ? " AND (CASE WHEN po.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(po.financial_year, 4), '-', RIGHT(po.financial_year, 2)) ELSE po.financial_year END) = '".$selected_fy."'" : "";
        $inv_fy_where = $selected_fy != '' ? " AND (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) = '".$selected_fy."'" : "";

            $where = "";
            if ($type == 'project_approved') {
                $where = " AND p.current_status = ".ST_APPROVED;
            } else if ($type == 'approved_in_progress') {
                $where = " AND p.current_status > 100 AND p.current_status < ".ST_APPROVED;
            } else if ($type == 'active_project') {
                $where = " AND p.current_status != 0";
            } else if ($type == 'spent') {
                $where = " AND IFNULL(inv.spent_amount, 0) > 0";
            } else if ($type == 'pending_obligation') {
                $where = " AND p.current_status = ".ST_APPROVED." AND (IFNULL(po.approved_amount, 0) - IFNULL(inv.spent_amount, 0)) > 0";
            } else {
                $where = " AND 1=0";
            }

        $query = "SELECT
                p.id,
                p.subject,
                p.created_by,
                IFNULL(po.approved_amount, 0) AS approved_amount,
                IFNULL(inv.spent_amount, 0) AS spent_amount,
                IFNULL(p.total_budget_grand_total, 0) AS total_budget_grand_total
            FROM csr_proposal_data p
            LEFT JOIN (
                SELECT po.project_id, SUM(IFNULL(po.po_value_in, 0)) AS approved_amount
                FROM csr_po_details po
                WHERE po.company_id = '".$company_id."'
                AND po.is_active = 0".$po_fy_where.$po_project_scope."
                GROUP BY po.project_id
            ) po ON po.project_id = p.id
            LEFT JOIN (
                SELECT inv.project_id, SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
                FROM csr_invoice_details inv
                WHERE inv.company_id = '".$company_id."'
                AND inv.is_active = 0".$inv_fy_where.$po_project_scope."
                GROUP BY inv.project_id
            ) inv ON inv.project_id = p.id
            WHERE p.company_id = '".$company_id."'
            AND p.is_active = 0".$proposal_fy_where.$proposal_scope.$where."
            ORDER BY p.id DESC";

        $return['projects'] = $callConfig->getAllRows($query);
        $return['query'] = $query;
        return $return;
    }

    function getCsrApprovedProjectsForDocuments($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;

        $query = "SELECT id, subject, institue_name, financial_year
            FROM csr_proposal_data
            WHERE company_id = '".$company_id."'
            AND is_active = 0
            AND current_status = ".ST_APPROVED."
            ORDER BY id DESC";
        $rows = $callConfig->getAllRows($query);

        $return = array();
        $return['approved_projects'] = $rows;
        return $return;
    }

    function getCsrProjectDocuments($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int)$get['proposal_id'] : 0;

        $query = "SELECT id, company_id, proposal_id, document_name, file_name, actual_file_name, access_type,
                is_active, current_status, status_date, created_date, created_by
            FROM csr_project_documents
            WHERE company_id = '".$company_id."'
            AND is_active = 0";
        if ($proposal_id > 0) {
            $query .= " AND proposal_id = '".$proposal_id."'";
        }
        $query .= " ORDER BY id DESC";
        $rows = $callConfig->getAllRows($query);

        $return = array();
        $return['project_documents'] = $rows;
        return $return;
    }

    function insertCsrProjectDocument($get, $post = array()) {
        global $callConfig;

        if (count((array)$post) == 0) {
            $post = $_POST;
        }
        $company_id = isset($get['company_id']) ? $get['company_id'] : ($post['company_id'] ?? '');
        $created_by = isset($get['user_id']) ? $get['user_id'] : ($post['created_by'] ?? '');

        $insertData = array(
            'company_id' => $company_id != '' ? $company_id : null,
            'proposal_id' => $post['proposal_id'] ?? null,
            'document_name' => $post['document_name'] ?? null,
            'file_name' => $post['file_name'] ?? null,
            'actual_file_name' => $post['actual_file_name'] ?? null,
            'access_type' => $post['access_type'] ?? 'internal',
            'is_active' => $post['is_active'] ?? '0',
            'current_status' => $post['current_status'] ?? '0',
            'status_date' => $post['status_date'] ?? date('Y-m-d H:i:s'),
            'created_date' => $post['created_date'] ?? date('Y-m-d H:i:s'),
            'created_by' => $created_by != '' ? $created_by : null
        );

        $res = $callConfig->insertRecord('csr_project_documents', $insertData);
        return $res;
    }

    function updateCsrProjectDocument($get, $post = array()) {
        global $callConfig;

        if (count((array)$post) == 0) {
            $post = $_POST;
        }
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : (int)($post['company_id'] ?? 0);
        $document_id = isset($post['id']) ? (int)$post['id'] : 0;

        if ($document_id <= 0 || $company_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid document details');
        }

        $checkQuery = "SELECT id FROM csr_project_documents
            WHERE id = '".$document_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0";
        $checkRow = $callConfig->getRow($checkQuery);
        if (empty($checkRow)) {
            return array('status' => 'error', 'message' => 'Document not found');
        }

        $updateData = array(
            'proposal_id' => $post['proposal_id'] ?? null,
            'document_name' => $post['document_name'] ?? null,
            'access_type' => $post['access_type'] ?? 'internal'
        );
        if (isset($post['file_name']) && $post['file_name'] != '') {
            $updateData['file_name'] = $post['file_name'];
        }
        if (isset($post['actual_file_name']) && $post['actual_file_name'] != '') {
            $updateData['actual_file_name'] = $post['actual_file_name'];
        }

        $updateData['updated_by'] = $get['user_id'];
        $updateData['updated_date'] = date('Y-m-d H:i:s');
        $res = $callConfig->updateRecord('csr_project_documents', $updateData, 'id', $document_id);
        return $res;
    }
    function getCsrProjectTrackingManage($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int)$get['proposal_id'] : 0;
        $approved_status = defined('ST_APPROVED') ? (int)ST_APPROVED : 199;

        $return = array(
            'status' => 'error',
            'proposal' => array(),
            'tracking_rows' => array(),
            'approved_status' => $approved_status
        );

        if ($company_id <= 0 || $proposal_id <= 0) {
            $return['message'] = 'Invalid request';
            return $return;
        }

        $proposalQuery = "SELECT id, subject, project_start_date, project_end_date, csr_location
            FROM csr_proposal_data
            WHERE id = '".$proposal_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0";
        $proposal = $callConfig->getRow($proposalQuery);
        if (empty($proposal)) {
            $return['message'] = 'Proposal not found';
            return $return;
        }

        $trackingQuery = "SELECT id, proposal_id, tracking_name, description, start_date, end_date,
                tracking_budget, status, current_status, is_active
            FROM csr_project_tracking
            WHERE proposal_id = '".$proposal_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0
            ORDER BY id ASC";
        $tracking_rows = $callConfig->getAllRows($trackingQuery);

        $return['status'] = 'success';
        $return['proposal'] = $proposal;
        $return['tracking_rows'] = $tracking_rows;
        return $return;
    }

    function saveCsrProjectTracking($get, $post = array()) {
        global $callConfig;

        if (count((array)$post) == 0) {
            $post = $_POST;
        }

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : (int)($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $proposal_id = isset($post['proposal_id']) ? (int)$post['proposal_id'] : 0;
        $approved_status = defined('ST_APPROVED') ? (int)ST_APPROVED : 199;
        $now = date('Y-m-d H:i:s');

        if ($company_id <= 0 || $proposal_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid proposal details');
        }

        $proposalQuery = "SELECT id, created_by FROM csr_proposal_data
            WHERE id = '".$proposal_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0";
        $proposal = $callConfig->getRow($proposalQuery);
        if (empty($proposal)) {
            return array('status' => 'error', 'message' => 'Proposal not found');
        }

        $tracking_ids = $post['tracking_id'] ?? array();
        $tracking_names = $post['tracking_name'] ?? array();
        $descriptions = $post['description'] ?? array();
        $start_dates = $post['start_date'] ?? array();
        $end_dates = $post['end_date'] ?? array();
        $tracking_budgets = $post['tracking_budget'] ?? array();

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        if (count((array)$tracking_names) > 0) {
            foreach ($tracking_names as $index => $tracking_name) {
                $tracking_name = trim((string)$tracking_name);
                if ($tracking_name == '') {
                    continue;
                }

                $row_id = isset($tracking_ids[$index]) ? (int)$tracking_ids[$index] : 0;
                $description = trim((string)($descriptions[$index] ?? ''));
                $start_date = trim((string)($start_dates[$index] ?? ''));
                $end_date = trim((string)($end_dates[$index] ?? ''));
                $tracking_budget = trim((string)($tracking_budgets[$index] ?? ''));

                if ($row_id > 0) {
                    $checkQuery = "SELECT id, current_status FROM csr_project_tracking
                        WHERE id = '".$row_id."'
                        AND proposal_id = '".$proposal_id."'
                        AND company_id = '".$company_id."'
                        AND is_active = 0";
                    $existing = $callConfig->getRow($checkQuery);
                    if (empty($existing)) {
                        $skipped++;
                        continue;
                    }
                    if ((int)$existing->current_status == $approved_status) {
                        $skipped++;
                        continue;
                    }

                    $updateData = array(
                        'tracking_name' => $tracking_name,
                        'description' => $description,
                        'start_date' => $start_date != '' ? $start_date : null,
                        'end_date' => $end_date != '' ? $end_date : null,
                        'tracking_budget' => $tracking_budget != '' ? $tracking_budget : null,
                        'updated_by' => $user_id != '' ? $user_id : null,
                        'updated_date' => $now
                    );
                    $callConfig->updateRecord('csr_project_tracking', $updateData, 'id', $row_id);
                    $updated++;
                } else {
                    $insertData = array(
                        'company_id' => $company_id,
                        'proposal_id' => $proposal_id,
                        'tracking_name' => $tracking_name,
                        'description' => $description,
                        'start_date' => $start_date != '' ? $start_date : null,
                        'end_date' => $end_date != '' ? $end_date : null,
                        'tracking_budget' => $tracking_budget != '' ? $tracking_budget : null,
                        'status' => '',
                        'current_status' => '0',
                        'status_date' => $now,
                        'is_active' => '0',
                        'created_date' => $now,
                        'created_by' => $user_id != '' ? $user_id : ($proposal->created_by ?? null)
                    );
                    $callConfig->insertRecord('csr_project_tracking', $insertData);
                    $inserted++;
                }
            }
        }

        return array(
            'status' => 'success',
            'message' => 'Tracking details saved',
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped
        );
    }

    function getCsrProposalProjectDates($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int)$get['proposal_id'] : 0;

        $return = array(
            'status' => 'error',
            'proposal' => array()
        );

        if ($company_id <= 0 || $proposal_id <= 0) {
            $return['message'] = 'Invalid request';
            return $return;
        }

        $query = "SELECT id, subject, project_start_date, project_end_date
            FROM csr_proposal_data
            WHERE id = '".$proposal_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0";
        $proposal = $callConfig->getRow($query);
        if (empty($proposal)) {
            $return['message'] = 'Proposal not found';
            return $return;
        }

        $return['status'] = 'success';
        $return['proposal'] = $proposal;
        return $return;
    }

    function saveCsrProposalProjectDates($get, $post = array()) {
        global $callConfig;

        if (count((array)$post) == 0) {
            $post = $_POST;
        }

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : (int)($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $proposal_id = isset($post['proposal_id']) ? (int)$post['proposal_id'] : 0;
        $project_start_date = trim((string)($post['project_start_date'] ?? ''));
        $project_end_date = trim((string)($post['project_end_date'] ?? ''));
        $now = date('Y-m-d H:i:s');

        if ($company_id <= 0 || $proposal_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid proposal details');
        }

        $checkQuery = "SELECT id FROM csr_proposal_data
            WHERE id = '".$proposal_id."'
            AND company_id = '".$company_id."'
            AND is_active = 0";
        $proposal = $callConfig->getRow($checkQuery);
        if (empty($proposal)) {
            return array('status' => 'error', 'message' => 'Proposal not found');
        }

        $updateData = array(
            'project_start_date' => $project_start_date != '' ? $project_start_date : null,
            'project_end_date' => $project_end_date != '' ? $project_end_date : null,
            'updated_by' => $user_id != '' ? $user_id : null,
            'updated_date' => $now
        );
        $callConfig->updateRecord('csr_proposal_data', $updateData, 'id', $proposal_id);

        return array('status' => 'success', 'message' => 'Project dates saved');
    }
    function togetdailyoperationsdashboard($get){
        global $callConfig;

        $today =  date('Y-m-d');
        $company_id = (int)$get['company_id'];
        $user_id = (int)$get['user_id'];
        $admin_type = $get['admin_type'];
        $user_wh = "";
        $user_wh_alias = "";
        $user_wh_au = "";

        if ($admin_type == '2') {
            $team_wh = " IN (SELECT id FROM application_users WHERE reports_to = " . $user_id . " AND is_active = 0)";
            $user_wh = " AND user_id" . $team_wh;
            $user_wh_alias = " AND fwdp.user_id" . $team_wh;
            $user_wh_au = " AND au.reports_to = " . $user_id;
        } elseif ($admin_type == '3') {
            $user_wh = " AND user_id = " . $user_id;
            $user_wh_alias = " AND fwdp.user_id = " . $user_id;
            $user_wh_au = " AND au.id = " . $user_id;
        }

        $return = array();

        $emp_query = "SELECT COUNT(id) AS cc_emp FROM application_users WHERE company_id = " . $company_id . " AND is_active = 0 AND user_field_work_type IN ('1','2')" . str_replace('user_id', 'id', $user_wh);
        $emp_row = $callConfig->getRow($emp_query);
        $return['employees_count'] = (int)($emp_row->cc_emp ?? 0);

        $this_month_start = date('Y-m-01');
        $this_month_end = date('Y-m-t');
        $last_month_start = date('Y-m-01', strtotime('first day of last month'));
        $last_month_end = date('Y-m-t', strtotime('last day of last month'));

        $active_this_month_q = "SELECT COUNT(DISTINCT user_id) AS cc_active FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date >= '" . $this_month_start . "' AND map_date <= '" . $this_month_end . "' AND is_active = 0" . $user_wh;
        $active_last_month_q = "SELECT COUNT(DISTINCT user_id) AS cc_active FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date >= '" . $last_month_start . "' AND map_date <= '" . $last_month_end . "' AND is_active = 0" . $user_wh;
        $active_this_month = (int)($callConfig->getRow($active_this_month_q)->cc_active ?? 0);
        $active_last_month = (int)($callConfig->getRow($active_last_month_q)->cc_active ?? 0);
        if ($active_last_month > 0) {
            $return['employees_growth_pct'] = round((($active_this_month - $active_last_month) / $active_last_month) * 100, 1);
        } else {
            $return['employees_growth_pct'] = $active_this_month > 0 ? 100 : 0;
        }

        $planned_today_q = "SELECT COUNT(id) AS cc_plan FROM field_work_daily_plans WHERE company_id = " . $company_id . " AND plan_date = '" . $today . "' AND current_status NOT IN (25,28) AND is_active = 0" . $user_wh;
        $return['planned_visits_today'] = (int)($callConfig->getRow($planned_today_q)->cc_plan ?? 0);

        $map_today_q = "SELECT COUNT(id) AS cc_map, SUM(IF(field_work_daily_plan_id != '' AND field_work_daily_plan_id IS NOT NULL, 1, 0)) AS cc_linked FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date = '" . $today . "' AND is_active = 0" . $user_wh;
        $map_today_row = $callConfig->getRow($map_today_q);
        $return['completed_today'] = (int)($map_today_row->cc_linked ?? 0);
        $return['extra_visits_today'] = (int)($map_today_row->cc_map ?? 0) - $return['completed_today'];
        if ($return['extra_visits_today'] < 0) {
            $return['extra_visits_today'] = 0;
        }

        $return['remaining_today'] = $return['planned_visits_today'] - $return['completed_today'];
        if ($return['remaining_today'] < 0) {
            $return['remaining_today'] = 0;
        }

        if ($return['planned_visits_today'] > 0) {
            $return['completion_pct'] = round(($return['completed_today'] / $return['planned_visits_today']) * 100);
            $return['planned_progress_pct'] = $return['completion_pct'];
        } else {
            $return['completion_pct'] = 0;
            $return['planned_progress_pct'] = 0;
        }

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $extra_yesterday_q = "SELECT COUNT(id) AS cc_map, SUM(IF(field_work_daily_plan_id != '' AND field_work_daily_plan_id IS NOT NULL, 1, 0)) AS cc_linked FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date = '" . $yesterday . "' AND is_active = 0" . $user_wh;
        $extra_yesterday_row = $callConfig->getRow($extra_yesterday_q);
        $extra_yesterday = (int)($extra_yesterday_row->cc_map ?? 0) - (int)($extra_yesterday_row->cc_linked ?? 0);
        if ($extra_yesterday < 0) {
            $extra_yesterday = 0;
        }
        $return['extra_visits_change_today'] = $return['extra_visits_today'] - $extra_yesterday;

        $active_emp_q = "SELECT COUNT(DISTINCT user_id) AS cc_active FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date = '" . $today . "' AND is_active = 0" . $user_wh;
        $return['active_employees_today'] = (int)($callConfig->getRow($active_emp_q)->cc_active ?? 0);
        $return['pending_today'] = $return['remaining_today'];

        $planned_visits_q = "SELECT fwdp.id, fwdp.contact_person, fwdp.plan_location, fwdp.plan_date, fwdp.plan_time, fwdp.user_id,
            IFNULL(au.display_name, '') AS employee_name,
            IF(fwump.id IS NOT NULL, 'completed', 'pending') AS visit_status
            FROM field_work_daily_plans fwdp
            LEFT JOIN application_users au ON au.id = fwdp.user_id AND au.is_active = 0
            LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id AND fwump.map_date = fwdp.plan_date AND fwump.is_active = 0
            WHERE fwdp.company_id = " . $company_id . " AND fwdp.plan_date = '" . $today . "' AND fwdp.is_active = 0" . $user_wh_alias . "
            ORDER BY fwdp.plan_time ASC";
        $return['planned_visits'] = $callConfig->getAllRows($planned_visits_q);

        $completed_visits_q = "SELECT fwump.id, fwump.map_title AS contact_person, IFNULL(fwdp.plan_location, '') AS plan_location,
            fwump.map_date AS plan_date, fwump.map_time AS plan_time, fwump.user_id,
            IFNULL(au.display_name, '') AS employee_name, 'completed' AS visit_status
            FROM field_work_user_map_points fwump
            LEFT JOIN application_users au ON au.id = fwump.user_id AND au.is_active = 0
            LEFT JOIN field_work_daily_plans fwdp ON fwdp.id = fwump.field_work_daily_plan_id AND fwdp.is_active = 0
            WHERE fwump.company_id = " . $company_id . " AND fwump.map_date = '" . $today . "'
            AND fwump.field_work_daily_plan_id != '' AND fwump.field_work_daily_plan_id IS NOT NULL AND fwump.is_active = 0" . str_replace('fwdp.user_id', 'fwump.user_id', $user_wh_alias) . "
            ORDER BY fwump.map_time ASC";
        $return['completed_visits'] = $callConfig->getAllRows($completed_visits_q);

        $extra_visits_q = "SELECT fwump.id, fwump.map_title AS contact_person, '' AS plan_location,
            fwump.map_date AS plan_date, fwump.map_time AS plan_time, fwump.user_id,
            IFNULL(au.display_name, '') AS employee_name, 'extra' AS visit_status
            FROM field_work_user_map_points fwump
            LEFT JOIN application_users au ON au.id = fwump.user_id AND au.is_active = 0
            WHERE fwump.company_id = " . $company_id . " AND fwump.map_date = '" . $today . "'
            AND (fwump.field_work_daily_plan_id = '' OR fwump.field_work_daily_plan_id IS NULL) AND fwump.is_active = 0" . str_replace('fwdp.user_id', 'fwump.user_id', $user_wh_alias) . "
            ORDER BY fwump.map_time ASC";
        $return['extra_visits'] = $callConfig->getAllRows($extra_visits_q);

        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));
        $weekly_q = "SELECT DATE(map_date) AS visit_date, COUNT(id) AS visit_count
            FROM field_work_user_map_points
            WHERE company_id = " . $company_id . " AND map_date >= '" . $week_start . "' AND map_date <= '" . $week_end . "' AND is_active = 0" . $user_wh . "
            GROUP BY DATE(map_date)";
        $weekly_rows = $callConfig->getAllRows($weekly_q);
        $weekly_map = array();
        if (count((array)$weekly_rows) > 0) {
            foreach ($weekly_rows as $weekly_row) {
                $weekly_map[$weekly_row->visit_date] = (int)$weekly_row->visit_count;
            }
        }
        $weekly_visits = array();
        $max_weekly = 0;
        for ($i = 0; $i < 7; $i++) {
            $day_date = date('Y-m-d', strtotime($week_start . ' +' . $i . ' day'));
            $day_count = (int)($weekly_map[$day_date] ?? 0);
            if ($day_count > $max_weekly) {
                $max_weekly = $day_count;
            }
            $weekly_visits[] = array(
                'label' => date('D', strtotime($day_date)),
                'date' => $day_date,
                'count' => $day_count
            );
        }
        if (count((array)$weekly_visits) > 0) {
            foreach ($weekly_visits as $wk => $weekly_item) {
                $weekly_visits[$wk]['height_pct'] = $max_weekly > 0 ? round(($weekly_item['count'] / $max_weekly) * 100) : 0;
                if ($weekly_visits[$wk]['height_pct'] < 8 && $weekly_item['count'] > 0) {
                    $weekly_visits[$wk]['height_pct'] = 8;
                }
            }
        }
        $return['weekly_visits'] = $weekly_visits;

        $success_this_q = "SELECT COUNT(fwdp.id) AS total_planned,
            SUM(IF(fwump.id IS NOT NULL, 1, 0)) AS completed_planned
            FROM field_work_daily_plans fwdp
            LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id AND fwump.map_date = fwdp.plan_date AND fwump.is_active = 0
            WHERE fwdp.company_id = " . $company_id . " AND fwdp.plan_date >= '" . $this_month_start . "' AND fwdp.plan_date <= '" . $this_month_end . "' AND fwdp.is_active = 0" . $user_wh_alias;
        $success_this_row = $callConfig->getRow($success_this_q);
        $total_planned_month = (int)($success_this_row->total_planned ?? 0);
        $completed_planned_month = (int)($success_this_row->completed_planned ?? 0);
        if ($total_planned_month > 0) {
            $return['success_rate'] = round(($completed_planned_month / $total_planned_month) * 100);
        } else {
            $return['success_rate'] = 0;
        }

        $success_last_q = "SELECT COUNT(fwdp.id) AS total_planned,
            SUM(IF(fwump.id IS NOT NULL, 1, 0)) AS completed_planned
            FROM field_work_daily_plans fwdp
            LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id AND fwump.map_date = fwdp.plan_date AND fwump.is_active = 0
            WHERE fwdp.company_id = " . $company_id . " AND fwdp.plan_date >= '" . $last_month_start . "' AND fwdp.plan_date <= '" . $last_month_end . "' AND fwdp.is_active = 0" . $user_wh_alias;
        $success_last_row = $callConfig->getRow($success_last_q);
        $total_planned_last = (int)($success_last_row->total_planned ?? 0);
        $completed_planned_last = (int)($success_last_row->completed_planned ?? 0);
        if ($total_planned_last > 0) {
            $last_success_rate = round(($completed_planned_last / $total_planned_last) * 100);
        } else {
            $last_success_rate = 0;
        }
        $return['success_rate_change'] = $return['success_rate'] - $last_success_rate;

        $active_list_q = "SELECT t1.user_id, au.display_name, t1.map_title, t1.map_time, t1.map_date
            FROM field_work_user_map_points t1
            INNER JOIN (
                SELECT user_id, MAX(id) AS max_id
                FROM field_work_user_map_points
                WHERE map_date = '" . $today . "' AND company_id = " . $company_id . " AND is_active = 0" . $user_wh . "
                GROUP BY user_id
            ) t2 ON t1.id = t2.max_id
            INNER JOIN application_users au ON au.id = t1.user_id
            WHERE 1=1 " . $user_wh_au . "
            ORDER BY t1.map_time DESC
            LIMIT 4";
        $return['active_employees'] = $callConfig->getAllRows($active_list_q);

        $spark_q = "SELECT DATE(map_date) AS visit_day, COUNT(id) AS visit_count
            FROM field_work_user_map_points
            WHERE company_id = " . $company_id . " AND map_date >= '" . date('Y-m-d', strtotime('-6 day')) . "' AND map_date <= '" . $today . "' AND is_active = 0" . $user_wh . "
            GROUP BY DATE(map_date)
            ORDER BY visit_day ASC";
        $spark_rows = $callConfig->getAllRows($spark_q);
        $employee_spark = array();
        $extra_spark = array();
        if (count((array)$spark_rows) > 0) {
            foreach ($spark_rows as $spark_row) {
                $employee_spark[] = (int)$spark_row->visit_count;
            }
        }
        while (count((array)$employee_spark) < 7) {
            $employee_spark[] = 0;
        }
        $return['employee_sparkline'] = array_slice($employee_spark, -7);

        $extra_spark_q = "SELECT DATE(map_date) AS visit_day, COUNT(id) AS visit_count
            FROM field_work_user_map_points
            WHERE company_id = " . $company_id . " AND map_date >= '" . date('Y-m-d', strtotime('-6 day')) . "' AND map_date <= '" . $today . "'
            AND (field_work_daily_plan_id = '' OR field_work_daily_plan_id IS NULL) AND is_active = 0" . $user_wh . "
            GROUP BY DATE(map_date)
            ORDER BY visit_day ASC";
        $extra_spark_rows = $callConfig->getAllRows($extra_spark_q);
        if (count((array)$extra_spark_rows) > 0) {
            foreach ($extra_spark_rows as $extra_spark_row) {
                $extra_spark[] = (int)$extra_spark_row->visit_count;
            }
        }
        while (count((array)$extra_spark) < 7) {
            $extra_spark[] = 0;
        }
        $return['extra_sparkline'] = array_slice($extra_spark, -7);

        $return['today_date_label'] = date('d M Y');
        $return['greeting'] = $this->getDailyOpsGreeting();
        $return['map_data'] = $this->getDailyOpsMapPoints($get, $today, $company_id, $user_id, $admin_type, $user_wh, $user_wh_alias);

        return $return;
    }

    function getDailyOpsMapPoints($get, $today, $company_id, $user_id, $admin_type, $user_wh, $user_wh_alias){
        global $callConfig;

        $role = 'admin';
        $default_mode = 'last_visit';
        $modes = array();

        if ($admin_type == '2') {
            $role = 'manager';
            $default_mode = 'last_visit';
        } elseif ($admin_type == '3') {
            $role = 'employee';
            $default_mode = 'planned';
        }

        $nowTime = date('H:i:s');

        if ($admin_type == '1' || $admin_type == '2') {
            $next_planned = array();
            $next_q = "SELECT fwdp.id, fwdp.user_id, fwdp.contact_person, fwdp.plan_location, fwdp.plan_date, fwdp.plan_time,
                fwdp.plan_latitude, fwdp.plan_longitude, au.display_name
                FROM field_work_daily_plans fwdp
                INNER JOIN application_users au ON au.id = fwdp.user_id
                LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id
                    AND fwump.map_date = fwdp.plan_date AND fwump.is_active = 0
                WHERE fwdp.company_id = " . (int)$company_id . "
                    AND fwdp.plan_date = '" . $today . "'
                    AND fwdp.is_active = 0
                    AND fwdp.current_status NOT IN (25,28)
                    AND fwump.id IS NULL
                    AND fwdp.plan_latitude IS NOT NULL AND fwdp.plan_latitude != ''
                    AND fwdp.plan_longitude IS NOT NULL AND fwdp.plan_longitude != ''
                    " . $user_wh_alias . "
                ORDER BY fwdp.user_id ASC, fwdp.plan_time ASC";
            $next_rows = $callConfig->getAllRows($next_q);
            $seen_users = array();
            if (count((array)$next_rows) > 0) {
                foreach ($next_rows as $row) {
                    $uid = (int)$row->user_id;
                    if (isset($seen_users[$uid])) {
                        continue;
                    }
                    $seen_users[$uid] = 1;
                    $lat = (float)$row->plan_latitude;
                    $lng = (float)$row->plan_longitude;
                    if ($lat == 0 && $lng == 0) {
                        continue;
                    }
                    $planTime = (string)($row->plan_time ?? '');
                    $status = ($planTime != '' && $planTime < $nowTime) ? 'overdue' : 'pending';
                    $label = $row->contact_person != '' ? $row->contact_person : 'Planned Visit';
                    $label = str_replace('DOD_TEST_', '', $label);
                    $next_planned[] = array(
                        'lat' => $lat,
                        'lng' => $lng,
                        'label' => $label,
                        'sub_label' => trim((string)($row->display_name ?? '') . ' · ' . $planTime),
                        'status' => $status,
                        'user_id' => $uid,
                        'time' => $planTime
                    );
                }
            }
            $modes['next_planned'] = $next_planned;

            $last_visit = array();
            $last_q = "SELECT t1.user_id, au.display_name, t1.map_title, t1.map_time, t1.map_date,
                t1.map_latitude, t1.map_longitude, t1.field_work_daily_plan_id
                FROM field_work_user_map_points t1
                INNER JOIN (
                    SELECT user_id, MAX(id) AS max_id
                    FROM field_work_user_map_points
                    WHERE map_date = '" . $today . "' AND company_id = " . (int)$company_id . " AND is_active = 0" . $user_wh . "
                    GROUP BY user_id
                ) t2 ON t1.id = t2.max_id
                INNER JOIN application_users au ON au.id = t1.user_id
                WHERE t1.map_latitude IS NOT NULL AND t1.map_latitude != ''
                    AND t1.map_longitude IS NOT NULL AND t1.map_longitude != ''
                ORDER BY t1.map_time DESC";
            $last_rows = $callConfig->getAllRows($last_q);
            if (count((array)$last_rows) > 0) {
                foreach ($last_rows as $row) {
                    $lat = (float)$row->map_latitude;
                    $lng = (float)$row->map_longitude;
                    if ($lat == 0 && $lng == 0) {
                        continue;
                    }
                    $title = strtolower(trim((string)($row->map_title ?? '')));
                    $titleClean = str_replace('DOD_TEST_', '', (string)($row->map_title ?? ''));
                    if ($title == 'check-in' || $title == 'check in' || strpos($title, 'check-in') !== false) {
                        $status = 'check_in';
                    } else {
                        $status = 'last_visit';
                    }
                    $last_visit[] = array(
                        'lat' => $lat,
                        'lng' => $lng,
                        'label' => (string)($row->display_name ?? 'Employee'),
                        'sub_label' => trim($titleClean . ' · ' . (string)($row->map_time ?? '')),
                        'status' => $status,
                        'user_id' => (int)$row->user_id,
                        'time' => (string)($row->map_time ?? '')
                    );
                }
            }
            $modes['last_visit'] = $last_visit;
        } else {
            $planned = array();
            $planned_q = "SELECT fwdp.id, fwdp.user_id, fwdp.contact_person, fwdp.plan_location, fwdp.plan_date, fwdp.plan_time,
                fwdp.plan_latitude, fwdp.plan_longitude,
                IF(fwump.id IS NOT NULL, 'completed', 'pending') AS visit_status
                FROM field_work_daily_plans fwdp
                LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id
                    AND fwump.map_date = fwdp.plan_date AND fwump.is_active = 0
                WHERE fwdp.company_id = " . (int)$company_id . "
                    AND fwdp.plan_date = '" . $today . "'
                    AND fwdp.is_active = 0
                    AND fwdp.current_status NOT IN (25,28)
                    AND fwdp.plan_latitude IS NOT NULL AND fwdp.plan_latitude != ''
                    AND fwdp.plan_longitude IS NOT NULL AND fwdp.plan_longitude != ''
                    " . $user_wh_alias . "
                ORDER BY fwdp.plan_time ASC";
            $planned_rows = $callConfig->getAllRows($planned_q);
            if (count((array)$planned_rows) > 0) {
                foreach ($planned_rows as $row) {
                    $lat = (float)$row->plan_latitude;
                    $lng = (float)$row->plan_longitude;
                    if ($lat == 0 && $lng == 0) {
                        continue;
                    }
                    $visitStatus = (string)($row->visit_status ?? 'pending');
                    $planTime = (string)($row->plan_time ?? '');
                    if ($visitStatus != 'completed' && $planTime != '' && $planTime < $nowTime) {
                        $status = 'overdue';
                    } elseif ($visitStatus == 'completed') {
                        $status = 'completed';
                    } else {
                        $status = 'pending';
                    }
                    $label = $row->contact_person != '' ? $row->contact_person : 'Planned Visit';
                    $label = str_replace('DOD_TEST_', '', $label);
                    $planned[] = array(
                        'lat' => $lat,
                        'lng' => $lng,
                        'label' => $label,
                        'sub_label' => trim((string)($row->plan_location ?? '') . ' · ' . $planTime),
                        'status' => $status,
                        'user_id' => (int)$row->user_id,
                        'time' => $planTime
                    );
                }
            }
            $modes['planned'] = $planned;

            $visits = array();
            $visits_q = "SELECT fwump.id, fwump.user_id, fwump.map_title, fwump.map_time, fwump.map_date,
                fwump.map_latitude, fwump.map_longitude, fwump.field_work_daily_plan_id,
                IFNULL(fwdp.plan_location, '') AS plan_location
                FROM field_work_user_map_points fwump
                LEFT JOIN field_work_daily_plans fwdp ON fwdp.id = fwump.field_work_daily_plan_id AND fwdp.is_active = 0
                WHERE fwump.company_id = " . (int)$company_id . "
                    AND fwump.map_date = '" . $today . "'
                    AND fwump.is_active = 0
                    AND fwump.map_latitude IS NOT NULL AND fwump.map_latitude != ''
                    AND fwump.map_longitude IS NOT NULL AND fwump.map_longitude != ''
                    " . str_replace('fwdp.user_id', 'fwump.user_id', $user_wh_alias) . "
                ORDER BY fwump.map_time ASC";
            $visit_rows = $callConfig->getAllRows($visits_q);
            if (count((array)$visit_rows) > 0) {
                foreach ($visit_rows as $row) {
                    $lat = (float)$row->map_latitude;
                    $lng = (float)$row->map_longitude;
                    if ($lat == 0 && $lng == 0) {
                        continue;
                    }
                    $title = strtolower(trim((string)($row->map_title ?? '')));
                    $titleClean = str_replace('DOD_TEST_', '', (string)($row->map_title ?? ''));
                    $hasPlan = ($row->field_work_daily_plan_id != '' && $row->field_work_daily_plan_id != null);
                    if ($title == 'check-in' || $title == 'check in' || strpos($title, 'check-in') !== false) {
                        $status = 'check_in';
                    } elseif ($hasPlan) {
                        $status = 'completed';
                    } else {
                        $status = 'extra';
                    }
                    $visits[] = array(
                        'lat' => $lat,
                        'lng' => $lng,
                        'label' => $titleClean != '' ? $titleClean : 'Visit',
                        'sub_label' => trim((string)($row->plan_location ?? '') . ' · ' . (string)($row->map_time ?? '')),
                        'status' => $status,
                        'user_id' => (int)$row->user_id,
                        'time' => (string)($row->map_time ?? '')
                    );
                }
            }
            $modes['visits'] = $visits;
        }

        return array(
            'role' => $role,
            'default_mode' => $default_mode,
            'modes' => $modes
        );
    }

    function getDailyOpsGreeting(){
        $hour = (int)date('G');
        if ($hour < 12) {
            return 'Good Morning';
        }
        if ($hour < 17) {
            return 'Good Afternoon';
        }
        return 'Good Evening';
    }

    function getCsrObligationDashboardData($get) {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $year_from = isset($get['year_from']) ? $callConfig->real_escape_string($get['year_from']) : '';
        $year_to = isset($get['year_to']) ? $callConfig->real_escape_string($get['year_to']) : '';
        $location_id = isset($get['location_id']) ? $callConfig->real_escape_string($get['location_id']) : '';
        $entity_code = isset($get['entity_code']) ? $callConfig->real_escape_string($get['entity_code']) : '';
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);
        $proposal_scope = $scope['proposal_where'];
        $is_admin_scope = $scope['is_admin'];

        // CSR working areas
        $query_areas = "SELECT id, csr_area FROM csr_areas WHERE company_id = '" . $company_id . "' AND  is_active=0";
        $rows_areas = $callConfig->getAllRows($query_areas);
        $csr_areas = array();
        if (count((array)$rows_areas) > 0) {
            foreach ($rows_areas as $row) {
                $csr_areas[$row->id] = $row;
            }
        }

        // No CSR project-type filter (show all approved project types)
        $csr_type_where = '';

        // Locations for filter
        $query_locations = "SELECT id, location_name FROM locations WHERE company_id = '" . $company_id . "'  AND  is_active=0";
        $rows_locations = $callConfig->getAllRows($query_locations);
        $locations = array();
        if (count((array)$rows_locations) > 0) {
            foreach ($rows_locations as $row) {
                $locations[$row->id] = $row;
            }
        }
        $return['locations'] = $locations;


        // Entities for filter
        $query_entities = "SELECT * FROM csr_entity_name WHERE company_id = '" . $company_id . "' AND  is_active=0";
        $entity_names = $callConfig->getAllRows($query_entities);
        $return['entity_names'] = $entity_names;

        // Financial years for filter
        $query_fy = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '{$company_id}' AND genc_code = 'CSR_FINANCIAL_YEAR' AND  is_active=0 ORDER BY id ASC";
        $fy_rows = $callConfig->getAllRows($query_fy);
        $return['financial_years'] = $fy_rows;

        $todayYear = (int)date('Y');
        $todayMonth = (int)date('n');
        if ($todayMonth >= 4) {
            $currentStart = $todayYear;
        } else {
            $currentStart = $todayYear - 1;
        }
        $currentFY = $currentStart . '-' . substr((string)($currentStart + 1), -2);
        $defaultFromStart = $currentStart - 5;
        $defaultFromFY = $defaultFromStart . '-' . substr((string)($defaultFromStart + 1), -2);

        if ($year_to == '') {
            $year_to = $currentFY;
        }
        if ($year_from == '') {
            $year_from = $defaultFromFY;
        }

        $fy_codes = array();
        $fy_index_map = array();
        if (count((array)$fy_rows) > 0) {
            foreach ($fy_rows as $index => $fy_row) {
                $fy_codes[] = $fy_row->genc_detail_code;
                $fy_index_map[$fy_row->genc_detail_code] = $index;
            }
        }

        if (!isset($fy_index_map[$year_from]) && count((array)$fy_codes) > 0) {
            $year_from = $fy_codes[0];
        }
        if (!isset($fy_index_map[$year_to]) && count((array)$fy_codes) > 0) {
            $year_to = $fy_codes[count($fy_codes) - 1];
        }
        if (isset($fy_index_map[$year_from]) && isset($fy_index_map[$year_to]) && $fy_index_map[$year_from] > $fy_index_map[$year_to]) {
            $tmp = $year_from;
            $year_from = $year_to;
            $year_to = $tmp;
        }

        $fy_range = array();
        $fy_range_sql = array();
        if (isset($fy_index_map[$year_from]) && isset($fy_index_map[$year_to])) {
            for ($i = $fy_index_map[$year_from]; $i <= $fy_index_map[$year_to]; $i++) {
                $fy_range[] = $fy_codes[$i];
                $fy_range_sql[] = "'" . $callConfig->real_escape_string($fy_codes[$i]) . "'";
            }
        }

        $filter_where = '';
        if ($location_id != '') {
            $filter_where .= " AND p.csr_location = '" . $location_id . "'";
        }
        if ($entity_code != '') {
            $filter_where .= " AND p.institue_name = '" . $entity_code . "'";
        }

        $filtered_project_ids = "SELECT p.id FROM csr_proposal_data p
            WHERE p.company_id = '" . $company_id . "'
            AND p.is_active = 0
            AND p.current_status = " . ST_APPROVED . $csr_type_where . $proposal_scope . $filter_where;
        $invoice_project_scope = " AND project_id IN (" . $filtered_project_ids . ")";

        $budgets = array();
        if ($is_admin_scope) {
            // Compliance profits for obligation
            $query_compliance = "SELECT financial_year, net_profit_before_tax_mtipl
                FROM csr_complaince
                WHERE company_id = '" . $company_id . "'
                AND is_active = 0";
            $budget_rows = $callConfig->getAllRows($query_compliance);
            if (count((array)$budget_rows) > 0) {
                foreach ($budget_rows as $budget_row) {
                    $parts = explode('-', $budget_row->financial_year);
                    $end_year = isset($parts[1]) ? (int)('20' . substr($parts[1], -2)) : 0;
                    if ($end_year > 0) {
                        $budgets[$end_year] = (float)$budget_row->net_profit_before_tax_mtipl;
                    }
                }
            }
        }

        $spent_by_fy = array();
        // YoY invoice spent
        $query_spent = "SELECT CASE
                    WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                    ELSE financial_year
                END AS financial_year,
                SUM(IFNULL(total_spent, 0)) AS spent_amount
            FROM csr_invoice_details
            WHERE company_id = '" . $company_id . "'
            AND is_active = 0" . $invoice_project_scope . "
            GROUP BY CASE
                WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2))
                ELSE financial_year
            END";
        $spent_rows = $callConfig->getAllRows($query_spent);
        if (count((array)$spent_rows) > 0) {
            foreach ($spent_rows as $spent_row) {
                $spent_by_fy[$spent_row->financial_year] = (float)$spent_row->spent_amount;
            }
        }

        $yearly_rows = array();
        $total_obligation = 0;
        $total_spent = 0;
        if (count((array)$fy_range) > 0) {
            foreach ($fy_range as $fy_code) {
                $obligation = 0;
                if ($is_admin_scope) {
                    $parts = explode('-', $fy_code);
                    $end_year = isset($parts[1]) ? (int)('20' . substr($parts[1], -2)) : 0;
                    if (
                        isset($budgets[$end_year - 1]) &&
                        isset($budgets[$end_year - 2]) &&
                        isset($budgets[$end_year - 3])
                    ) {
                        $obligation = round((($budgets[$end_year - 1] + $budgets[$end_year - 2] + $budgets[$end_year - 3]) / 3) * 0.02, 2);
                    }
                }
                $spent = isset($spent_by_fy[$fy_code]) ? (float)$spent_by_fy[$fy_code] : 0;
                $spent_pct = $obligation > 0 ? ($spent / $obligation) : 0;
                $unspent = $obligation - $spent;
                $unspent_pct = $obligation > 0 ? ($unspent / $obligation) : 0;
                $yearly_rows[] = array(
                    'financial_year' => $fy_code,
                    'obligation' => $obligation,
                    'spent_amount' => $spent,
                    'spent_pct' => $spent_pct,
                    'unspent_amount' => $unspent,
                    'unspent_pct' => $unspent_pct
                );
                $total_obligation += $obligation;
                $total_spent += $spent;
            }
        }

        $final_unspent = $total_obligation - $total_spent;
        $overall_utilization = $total_obligation > 0 ? ($total_spent / $total_obligation) : 0;
        $total_unspent_pct = $total_obligation > 0 ? ($final_unspent / $total_obligation) : 0;

        // Projects with area, location, status
        $query_projects = "SELECT
                p.id,
                p.subject AS project_name,
                p.institue_name AS entity,
                p.working_area,
                IFNULL(NULLIF(cas.csr_area, ''), 'Unassigned') AS working_area_name,
                IFNULL(NULLIF(loc.location_name, ''), '-') AS location_name,
                p.csr_location,
                ged.genc_detail_name AS type_of_project,
                CASE
                    WHEN IFNULL(tr.total_tracking, 0) = 0 THEN 'Not Started'
                    WHEN IFNULL(tr.null_status_count, 0) = tr.total_tracking THEN 'Not Started'
                    WHEN p.tracking_status = 1 THEN 'Completed'
                    ELSE 'In Progress'
                END AS project_status
            FROM csr_proposal_data p
            LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0
            LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0
            LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0
            LEFT JOIN (
                SELECT
                    proposal_id,
                    COUNT(*) AS total_tracking,
                    SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) AS null_status_count
                FROM csr_project_tracking
                WHERE is_active = 0
                AND company_id = '" . $company_id . "'
                GROUP BY proposal_id
            ) tr ON tr.proposal_id = p.id
            WHERE p.company_id = '" . $company_id . "'
            AND p.is_active = 0
            AND p.current_status = " . ST_APPROVED . $csr_type_where . $proposal_scope . $filter_where . "
            ORDER BY working_area_name ASC, p.subject ASC";
        $project_rows = $callConfig->getAllRows($query_projects);
        $return['query_projects'] = $query_projects;

        $fy_in_clause = '';
        if (count((array)$fy_range_sql) > 0) {
            $fy_in_clause = " AND (
                CASE
                    WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2))
                    ELSE inv.financial_year
                END
            ) IN (" . implode(',', $fy_range_sql) . ")";
        }

        // Project spent by FY
        $query_project_spent = "SELECT
                inv.project_id,
                CASE
                    WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2))
                    ELSE inv.financial_year
                END AS financial_year,
                SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
            FROM csr_invoice_details inv
            WHERE inv.company_id = '" . $company_id . "'
            AND inv.is_active = 0
            AND inv.project_id IN (" . $filtered_project_ids . ")" . $fy_in_clause . "
            GROUP BY inv.project_id, CASE
                WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2))
                ELSE inv.financial_year
            END";
        $project_spent_rows = $callConfig->getAllRows($query_project_spent);
        $project_spent_map = array();
        if (count((array)$project_spent_rows) > 0) {
            foreach ($project_spent_rows as $ps_row) {
                $pid = $ps_row->project_id;
                if (!isset($project_spent_map[$pid])) {
                    $project_spent_map[$pid] = array();
                }
                $project_spent_map[$pid][$ps_row->financial_year] = (float)$ps_row->spent_amount;
            }
        }

        $groups_map = array();
        if (count((array)$project_rows) > 0) {
            foreach ($project_rows as $p_row) {
                $area_name = $p_row->working_area_name != '' ? $p_row->working_area_name : 'Unassigned';
                if (!isset($groups_map[$area_name])) {
                    $groups_map[$area_name] = array(
                        'category' => $area_name,
                        'total' => 0,
                        'years' => array(),
                        'projects' => array()
                    );
                    if (count((array)$fy_range) > 0) {
                        foreach ($fy_range as $fy_code) {
                            $groups_map[$area_name]['years'][$fy_code] = 0;
                        }
                    }
                }

                $years = array();
                $project_total = 0;
                if (count((array)$fy_range) > 0) {
                    foreach ($fy_range as $fy_code) {
                        $amt = 0;
                        if (isset($project_spent_map[$p_row->id][$fy_code])) {
                            $amt = (float)$project_spent_map[$p_row->id][$fy_code];
                        }
                        $years[$fy_code] = $amt;
                        $project_total += $amt;
                        $groups_map[$area_name]['years'][$fy_code] += $amt;
                    }
                }

                $status = $p_row->project_status;
                if ($status == 'In Progress') {
                    $status = 'In-Process';
                }

                $groups_map[$area_name]['projects'][] = array(
                    'id' => $p_row->id,
                    'name' => $p_row->project_name,
                    'type' => $p_row->type_of_project != '' ? $p_row->type_of_project : 'CSR',
                    'entity' => $p_row->entity,
                    'location' => $p_row->location_name,
                    'status' => $status,
                    'total' => $project_total,
                    'years' => $years
                );
                $groups_map[$area_name]['total'] += $project_total;
            }
        }

        $project_groups = array();
        if (count((array)$groups_map) > 0) {
            foreach ($groups_map as $group) {
                $years_list = array();
                if (count((array)$fy_range) > 0) {
                    foreach ($fy_range as $fy_code) {
                        $years_list[] = isset($group['years'][$fy_code]) ? $group['years'][$fy_code] : 0;
                    }
                }
                if (count((array)$group['projects']) > 0) {
                    foreach ($group['projects'] as $p_idx => $project) {
                        $proj_years_list = array();
                        if (count((array)$fy_range) > 0) {
                            foreach ($fy_range as $fy_code) {
                                $proj_years_list[] = isset($project['years'][$fy_code]) ? $project['years'][$fy_code] : '';
                                if (isset($project['years'][$fy_code]) && $project['years'][$fy_code] == 0) {
                                    $proj_years_list[count($proj_years_list) - 1] = 0;
                                }
                            }
                        }
                        $group['projects'][$p_idx]['years'] = $proj_years_list;
                    }
                }
                $group['years'] = $years_list;
                $project_groups[] = $group;
            }
        }

        $location_rows = array();
        $location_agg = array();
        if (count((array)$project_rows) > 0) {
            foreach ($project_rows as $p_row) {
                $loc_key = $p_row->csr_location != '' ? $p_row->csr_location : '0';
                $loc_name = $p_row->location_name != '' ? $p_row->location_name : 'Not Mapped';
                if (!isset($location_agg[$loc_key])) {
                    $location_agg[$loc_key] = array(
                        'location' => $loc_name,
                        'amount' => 0,
                        'statuses' => array()
                    );
                }
                $project_total = 0;
                if (isset($project_spent_map[$p_row->id]) && count((array)$project_spent_map[$p_row->id]) > 0) {
                    foreach ($project_spent_map[$p_row->id] as $fy_code => $amt) {
                        if (in_array($fy_code, $fy_range)) {
                            $project_total += (float)$amt;
                        }
                    }
                }
                $location_agg[$loc_key]['amount'] += $project_total;
                $st = $p_row->project_status;
                if ($st == 'In Progress') {
                    $st = 'In-Process';
                }
                $location_agg[$loc_key]['statuses'][] = $st;
            }
        }
        if (count((array)$location_agg) > 0) {
            foreach ($location_agg as $loc_row) {
                $status = 'Not Started';
                $has_completed = 0;
                $has_process = 0;
                $has_not_started = 0;
                if (count((array)$loc_row['statuses']) > 0) {
                    foreach ($loc_row['statuses'] as $st) {
                        if ($st == 'Completed') {
                            $has_completed = 1;
                        } else if ($st == 'In-Process') {
                            $has_process = 1;
                        } else {
                            $has_not_started = 1;
                        }
                    }
                }
                if ($has_process == 1 || ($has_completed == 1 && $has_not_started == 1)) {
                    $status = 'In-Process';
                } else if ($has_completed == 1 && $has_process == 0 && $has_not_started == 0) {
                    $status = 'Completed';
                } else {
                    $status = 'Not Started';
                }
                $location_rows[] = array(
                    'location' => $loc_row['location'],
                    'amount' => $loc_row['amount'],
                    'status' => $status
                );
            }
        }

        $return['selected'] = array(
            'year_from' => $year_from,
            'year_to' => $year_to,
            'location_id' => $location_id,
            'entity_code' => $entity_code
        );
        $return['fy_range'] = $fy_range;
        $return['summary'] = array(
            'total_obligation' => $total_obligation,
            'total_spent' => $total_spent,
            'overall_utilization' => $overall_utilization,
            'final_unspent' => $final_unspent,
            'total_unspent_pct' => $total_unspent_pct
        );
        $return['yearly_rows'] = $yearly_rows;
        $return['project_groups'] = $project_groups;
        $return['location_rows'] = $location_rows;

        return $return;
    }
    function getFieldWorkOperationsReport($get){
        global $callConfig;

        $company_id = (int)$get['company_id'];
        $user_id = (int)$get['user_id'];
        $admin_type = isset($get['admin_type']) ? $get['admin_type'] : '1';
        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $userids_string = isset($get['userids']) ? $get['userids'] : '';
        $status_string = isset($get['status']) ? $get['status'] : '';

        $return = array(
            'users' => array(),
            'summary' => array(
                'planned' => 0,
                'visited' => 0,
                'pending' => 0,
                'extra' => 0
            ),
            'rows' => array()
        );

        $whr = "";
        if ($admin_type == '2') {
            $whr = "reports_to = '" . $user_id . "' AND ";
        } elseif ($admin_type == '3') {
            $whr = "id = '" . $user_id . "' AND ";
        }

        $users_q = "SELECT id, display_name, user_field_work_type FROM application_users
            WHERE company_id = '" . $company_id . "' AND " . $whr . " is_active = 0
            AND user_field_work_type IN ('1','2')
            ORDER BY display_name ASC";
        $users_rows = $callConfig->getAllRows($users_q);
        $scoped_users = array();
        $scoped_ids = array();
        if (count((array)$users_rows) > 0) {
            foreach ($users_rows as $urow) {
                $scoped_users[] = array(
                    'id' => $urow->id,
                    'display_name' => $urow->display_name,
                    'user_field_work_type' => $urow->user_field_work_type
                );
                $scoped_ids[] = (int)$urow->id;
            }
        }
        $return['users'] = $scoped_users;

        if ($filters_only == 'Y') {
            return $return;
        }

        if ($from_date == '' || $to_date == '') {
            return $return;
        }

        $selected_ids = array();
        if ($userids_string != '') {
            $raw_ids = explode(',', $userids_string);
            if (count((array)$raw_ids) > 0) {
                foreach ($raw_ids as $rid) {
                    $rid = trim($rid);
                    if ($rid != '' && $rid != '0' && is_numeric($rid)) {
                        $rid_int = (int)$rid;
                        if (in_array($rid_int, $scoped_ids)) {
                            $selected_ids[] = $rid_int;
                        }
                    }
                }
            }
        }
        if (count((array)$selected_ids) == 0) {
            $selected_ids = $scoped_ids;
        }
        if (count((array)$selected_ids) == 0) {
            return $return;
        }

        $ids = implode(',', $selected_ids);

        $status_filter = array();
        if ($status_string != '') {
            $raw_status = explode(',', $status_string);
            if (count((array)$raw_status) > 0) {
                foreach ($raw_status as $st) {
                    $st = strtolower(trim($st));
                    if ($st == 'visited' || $st == 'pending' || $st == 'extra') {
                        $status_filter[$st] = 1;
                    }
                }
            }
        }
        $filter_all = (count((array)$status_filter) == 0);

        $rows = array();

        if ($filter_all || isset($status_filter['pending'])) {
            $pending_q = "SELECT fwdp.plan_date AS visit_date, fwdp.plan_time AS visit_time,
                IFNULL(au.display_name, '') AS user_name, fwdp.user_id,
                IFNULL(fwdp.contact_person, '') AS customer,
                IFNULL(fwdp.mobile, '') AS contact,
                IFNULL(fwdp.plan_location, '') AS location,
                IFNULL(fwdp.plan_latitude, '') AS lat,
                IFNULL(fwdp.plan_longitude, '') AS lng,
                IFNULL(fwdp.plan_address, '') AS geo_location,
                'Pending' AS status
                FROM field_work_daily_plans fwdp
                LEFT JOIN application_users au ON au.id = fwdp.user_id AND au.is_active = 0
                LEFT JOIN field_work_user_map_points fwump ON fwump.field_work_daily_plan_id = fwdp.id
                    AND fwump.is_active = 0
                WHERE fwdp.company_id = '" . $company_id . "'
                    AND fwdp.user_id IN (" . $ids . ")
                    AND fwdp.plan_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    AND fwdp.is_active = 0
                    AND fwump.id IS NULL";
            $pending_rows = $callConfig->getAllRows($pending_q);
            if (count((array)$pending_rows) > 0) {
                foreach ($pending_rows as $prow) {
                    $rows[] = $prow;
                }
            }
        }

        if ($filter_all || isset($status_filter['visited'])) {
            $visited_q = "SELECT fwump.map_date AS visit_date, fwump.map_time AS visit_time,
                IFNULL(au.display_name, '') AS user_name, fwump.user_id,
                IFNULL(NULLIF(fwdp.contact_person, ''), IFNULL(fwump.map_title, '')) AS customer,
                IFNULL(fwdp.mobile, '') AS contact,
                IFNULL(NULLIF(fwdp.plan_location, ''), IFNULL(fwump.map_title, '')) AS location,
                IFNULL(fwump.map_latitude, '') AS lat,
                IFNULL(fwump.map_longitude, '') AS lng,
                IFNULL(fwdp.plan_address, '') AS geo_location,
                'Visited' AS status
                FROM field_work_user_map_points fwump
                LEFT JOIN application_users au ON au.id = fwump.user_id AND au.is_active = 0
                LEFT JOIN field_work_daily_plans fwdp ON fwdp.id = fwump.field_work_daily_plan_id AND fwdp.is_active = 0
                WHERE fwump.company_id = '" . $company_id . "'
                    AND fwump.user_id IN (" . $ids . ")
                    AND fwump.map_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    AND fwump.is_active = 0
                    AND fwump.field_work_daily_plan_id IS NOT NULL
                    AND fwump.field_work_daily_plan_id != ''
                    AND fwump.field_work_daily_plan_id != 0";
            $visited_rows = $callConfig->getAllRows($visited_q);
            if (count((array)$visited_rows) > 0) {
                foreach ($visited_rows as $vrow) {
                    $rows[] = $vrow;
                }
            }
        }

        if ($filter_all || isset($status_filter['extra'])) {
            $extra_q = "SELECT fwump.map_date AS visit_date, fwump.map_time AS visit_time,
                IFNULL(au.display_name, '') AS user_name, fwump.user_id,
                IFNULL(fwump.map_title, '') AS customer,
                '' AS contact,
                IFNULL(fwump.map_title, '') AS location,
                IFNULL(fwump.map_latitude, '') AS lat,
                IFNULL(fwump.map_longitude, '') AS lng,
                '' AS geo_location,
                'Extra' AS status
                FROM field_work_user_map_points fwump
                LEFT JOIN application_users au ON au.id = fwump.user_id AND au.is_active = 0
                WHERE fwump.company_id = '" . $company_id . "'
                    AND fwump.user_id IN (" . $ids . ")
                    AND fwump.map_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                    AND fwump.is_active = 0
                    AND (fwump.field_work_daily_plan_id IS NULL
                        OR fwump.field_work_daily_plan_id = ''
                        OR fwump.field_work_daily_plan_id = 0)";
            $extra_rows = $callConfig->getAllRows($extra_q);
            if (count((array)$extra_rows) > 0) {
                foreach ($extra_rows as $erow) {
                    $rows[] = $erow;
                }
            }
        }

        $normalized = array();
        $visited_count = 0;
        $pending_count = 0;
        $extra_count = 0;
        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $status = isset($row['status']) ? $row['status'] : '';
                if ($status == 'Visited') {
                    $visited_count++;
                } elseif ($status == 'Pending') {
                    $pending_count++;
                } elseif ($status == 'Extra') {
                    $extra_count++;
                }
                $normalized[] = array(
                    'visit_date' => isset($row['visit_date']) ? $row['visit_date'] : '',
                    'visit_time' => isset($row['visit_time']) ? $row['visit_time'] : '',
                    'user_name' => isset($row['user_name']) ? $row['user_name'] : '',
                    'user_id' => isset($row['user_id']) ? $row['user_id'] : '',
                    'customer' => isset($row['customer']) ? $row['customer'] : '',
                    'contact' => isset($row['contact']) ? $row['contact'] : '',
                    'location' => isset($row['location']) ? $row['location'] : '',
                    'lat' => isset($row['lat']) ? $row['lat'] : '',
                    'lng' => isset($row['lng']) ? $row['lng'] : '',
                    'geo_location' => isset($row['geo_location']) ? $row['geo_location'] : '',
                    'status' => $status
                );
            }
        }

        usort($normalized, function ($a, $b) {
            $d = strcmp((string)$a['visit_date'], (string)$b['visit_date']);
            if ($d != 0) {
                return $d;
            }
            $u = strcmp((string)$a['user_name'], (string)$b['user_name']);
            if ($u != 0) {
                return $u;
            }
            return strcmp((string)$a['visit_time'], (string)$b['visit_time']);
        });

        $return['rows'] = $normalized;
        $return['summary'] = array(
            'planned' => $visited_count + $pending_count,
            'visited' => $visited_count,
            'pending' => $pending_count,
            'extra' => $extra_count
        );

        return $return;
    }
    function getEmpExpensesReport($get){
        global $callConfig;

        $company_id = (int)$get['company_id'];
        $user_id = (int)$get['user_id'];
        $admin_type = isset($get['admin_type']) ? $get['admin_type'] : '1';
        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $empids_string = isset($get['empids']) ? $get['empids'] : '';
        $status_string = isset($get['status']) ? $get['status'] : '';
        $expense_type_string = isset($get['expense_type']) ? $get['expense_type'] : '';

        $return = array(
            'employees' => array(),
            'summary' => array(
                'claims' => 0,
                'line_items' => 0,
                'total_amount' => 0,
                'tax_amount' => 0,
                'pending_amount' => 0
            ),
            'rows' => array()
        );

        $emp_whr = "";
        if ($admin_type == '2') {
            $emp_whr = " AND emp.user_id IN (SELECT id FROM application_users WHERE reports_to = '" . $user_id . "' AND company_id = '" . $company_id . "' AND is_active = 0)";
        } elseif ($admin_type == '3') {
            $emp_whr = " AND emp.user_id = '" . $user_id . "'";
        }

        $employees_q = "SELECT emp.id, emp.display_name, emp.user_id
            FROM employees emp
            WHERE emp.company_id = '" . $company_id . "' AND emp.is_active = 0" . $emp_whr . "
            ORDER BY emp.display_name ASC";
        $employees_rows = $callConfig->getAllRows($employees_q);
        $scoped_employees = array();
        $scoped_emp_ids = array();
        if (count((array)$employees_rows) > 0) {
            foreach ($employees_rows as $erow) {
                $scoped_employees[] = array(
                    'id' => $erow->id,
                    'display_name' => $erow->display_name,
                    'user_id' => $erow->user_id
                );
                $scoped_emp_ids[] = (int)$erow->id;
            }
        }
        $return['employees'] = $scoped_employees;

        if ($filters_only == 'Y') {
            return $return;
        }

        if ($from_date == '' || $to_date == '') {
            return $return;
        }

        $selected_emp_ids = array();
        if ($empids_string != '') {
            $raw_ids = explode(',', $empids_string);
            if (count((array)$raw_ids) > 0) {
                foreach ($raw_ids as $rid) {
                    $rid = trim($rid);
                    if ($rid != '' && $rid != '0' && is_numeric($rid)) {
                        $rid_int = (int)$rid;
                        if (in_array($rid_int, $scoped_emp_ids)) {
                            $selected_emp_ids[] = $rid_int;
                        }
                    }
                }
            }
        }
        if (count((array)$selected_emp_ids) == 0) {
            $selected_emp_ids = $scoped_emp_ids;
        }
        if (count((array)$selected_emp_ids) == 0) {
            return $return;
        }

        $status_filter = array();
        if ($status_string != '') {
            $raw_status = explode(',', $status_string);
            if (count((array)$raw_status) > 0) {
                foreach ($raw_status as $st) {
                    $st = trim($st);
                    if ($st == '0' || $st == '2' || $st == '199') {
                        $status_filter[] = (int)$st;
                    }
                }
            }
        }
        if (count((array)$status_filter) == 0) {
            $status_filter = array(0, 2, 199);
        }

        $type_filter = array();
        if ($expense_type_string != '') {
            $raw_types = explode(',', $expense_type_string);
            if (count((array)$raw_types) > 0) {
                foreach ($raw_types as $tp) {
                    $tp = trim($tp);
                    if ($tp == '0' || $tp == '1' || $tp == '2') {
                        $type_filter[] = (int)$tp;
                    }
                }
            }
        }

        $emp_ids = implode(',', $selected_emp_ids);
        $status_ids = implode(',', $status_filter);

        $type_whr = "";
        if (count((array)$type_filter) > 0) {
            $type_whr = " AND eed.expense_type IN (" . implode(',', $type_filter) . ")";
        }

        $pay_map = array();
        $type_map = array(
            '0' => 'Expense',
            '1' => 'Mileage',
            '2' => 'Per Diem'
        );
        $lookup_q = "SELECT genc_code, genc_detail_code, genc_detail_name FROM general_code_details
            WHERE company_id = '" . $company_id . "' AND is_active = 0
            AND genc_code IN ('PAYMETHOD','EXPENSTYPE')";
        $lookup_rows = $callConfig->getAllRows($lookup_q);
        if (count((array)$lookup_rows) > 0) {
            foreach ($lookup_rows as $lrow) {
                if ($lrow->genc_code == 'PAYMETHOD') {
                    $pay_map[$lrow->genc_detail_code] = $lrow->genc_detail_name;
                } elseif ($lrow->genc_code == 'EXPENSTYPE') {
                    $type_map[$lrow->genc_detail_code] = $lrow->genc_detail_name;
                }
            }
        }

        $status_map = array(
            '0' => 'Active',
            '2' => 'Draft',
            '199' => 'Approved'
        );

        $data_q = "SELECT ee.id AS expense_id, ee.expense_code, ee.expense_date, ee.current_status, ee.pending_amount,
            ee.emp_id, IFNULL(emp.display_name, '') AS employee_name,
            eed.id AS detail_id, eed.expense_type, eed.merchant_name, eed.bill_no, eed.bill_date,
            eed.bill_file, eed.payment_mode, eed.location, eed.tax_amount, eed.expense_amount, eed.description
            FROM emp_expenses ee
            INNER JOIN emp_expenses_details eed ON eed.emp_expenses_id = ee.id AND eed.is_active = 0 AND eed.company_id = ee.company_id
            LEFT JOIN employees emp ON emp.id = ee.emp_id AND emp.is_active = 0
            WHERE ee.company_id = '" . $company_id . "'
                AND ee.is_active = 0
                AND ee.emp_id IN (" . $emp_ids . ")
                AND ee.current_status IN (" . $status_ids . ")
                AND ee.expense_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'
                " . $type_whr . "
            ORDER BY ee.expense_date DESC, ee.expense_code ASC, eed.id ASC";
        $data_rows = $callConfig->getAllRows($data_q);
        // $return['data_q'] = $data_q;

        $normalized = array();
        $claim_ids = array();
        $pending_sum = 0;
        $total_amount = 0;
        $tax_amount = 0;
        $line_items = 0;

        if (count((array)$data_rows) > 0) {
            foreach ($data_rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $expense_id = isset($row['expense_id']) ? (int)$row['expense_id'] : 0;
                $status_code = isset($row['current_status']) ? (string)$row['current_status'] : '';
                $expense_type = isset($row['expense_type']) ? (string)$row['expense_type'] : '';
                $payment_mode = isset($row['payment_mode']) ? $row['payment_mode'] : '';
                $line_tax = isset($row['tax_amount']) ? (float)$row['tax_amount'] : 0;
                $line_amount = isset($row['expense_amount']) ? (float)$row['expense_amount'] : 0;

                if (!isset($claim_ids[$expense_id])) {
                    $claim_ids[$expense_id] = 1;
                    $pending_sum += isset($row['pending_amount']) ? (float)$row['pending_amount'] : 0;
                }
                $line_items++;
                $total_amount += $line_amount;
                $tax_amount += $line_tax;

                $type_name = isset($type_map[$expense_type]) ? $type_map[$expense_type] : $expense_type;
                $pay_name = isset($pay_map[$payment_mode]) ? $pay_map[$payment_mode] : $payment_mode;
                $status_label = isset($status_map[$status_code]) ? $status_map[$status_code] : $status_code;

                $normalized[] = array(
                    'expense_date' => isset($row['expense_date']) ? $row['expense_date'] : '',
                    'expense_code' => isset($row['expense_code']) ? $row['expense_code'] : '',
                    'employee_name' => isset($row['employee_name']) ? $row['employee_name'] : '',
                    'expense_type_name' => $type_name,
                    'merchant_name' => isset($row['merchant_name']) ? $row['merchant_name'] : '',
                    'bill_no' => isset($row['bill_no']) ? $row['bill_no'] : '',
                    'payment_mode_name' => $pay_name,
                    'location' => isset($row['location']) ? $row['location'] : '',
                    'tax_amount' => $line_tax,
                    'expense_amount' => $line_amount,
                    'status_label' => $status_label,
                    'status_code' => $status_code,
                    'bill_file' => isset($row['bill_file']) ? $row['bill_file'] : ''
                );
            }
        }

        $return['rows'] = $normalized;
        $return['summary'] = array(
            'claims' => count((array)$claim_ids),
            'line_items' => $line_items,
            'total_amount' => round($total_amount, 2),
            'tax_amount' => round($tax_amount, 2),
            'pending_amount' => round($pending_sum, 2)
        );

        return $return;
    }
}
?>