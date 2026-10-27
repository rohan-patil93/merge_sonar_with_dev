<?php
// include('comm.php');
?>
<?php
class CSRClass
{
    function insertData($get){
        global $callConfig;
        if ($get['rid'] != "") {
            $query = "INSERT INTO csr_proposal_data (
            company_id, date, institue_name, subject, about_organisation, background, thematic_area, education, env_sustian,
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
            to_date, created_by, created_date, updated_by, updated_date,incorporation_file, csr1_file, form12a_file, form80g_file, form35ac_file, pan_file, financial_statements_file, proposal_file, fcra_file, tds_exemption_file, gst_file, reference_file, land_ownership_file, trust_deed_file, drawings_file, govt_drawings_file, basis_estimate_file, stability_certificate_file, execution_works_plan_file, orgininaor
            )
            SELECT
            company_id, date, institue_name, subject, about_organisation, background, thematic_area, education, env_sustian,
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
            to_date, created_by, created_date, updated_by, updated_date,incorporation_file, csr1_file, form12a_file, form80g_file, form35ac_file, pan_file, financial_statements_file, proposal_file, fcra_file, tds_exemption_file, gst_file, reference_file, land_ownership_file, trust_deed_file, drawings_file, govt_drawings_file, basis_estimate_file, stability_certificate_file, execution_works_plan_file, orgininaor
            FROM temp_csr_proposal_data
            WHERE id = '" . $get['rid'] . "'";
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
    function getTracckingPerentage($get){
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
    function getCommitteMembers($get){
        global $callConfig;
        $query = "SELECT au.id, au.display_name, au.user_email FROM application_user_module_roles aumr LEFT JOIN application_users au ON au.id = aumr.user_id 
        WHERE aumr.role_short_code='".CSR_COMMITTEE_MEMBER."'
        AND aumr.company_id='". $get['company_id']. "' 
        AND aumr.is_active='0'";
        $res = $callConfig->getAllRows($query);
        return $res;
    }
    function getCsrData($get){
        global $callConfig;
        $id = $get['id'];
        $table = $get['table'];
        $query = "SELECT cpd.* FROM transaction_approval_logs tal 
        LEFT JOIN ".$table." cpd ON cpd.id = tal.transaction_id 
        WHERE tal.user_id = '".$id."' AND tal.transaction_table='".$table."'
        AND tal.company_id='". $get['company_id']. "' 
        AND cpd.is_active='0'"; 
        $res = $callConfig->getAllRows($query);
        return $res;
    }
    function getApprovedMembers($get){
        global $callConfig;
        $id = $get['id'];
        $table = $get['table'];
        $query = "SELECT au.id, au.display_name, au.user_email FROM transaction_approval_logs tal
        LEFT JOIN application_users au ON au.id = tal.user_id 
        WHERE tal.transaction_id = '".$id."' AND tal.transaction_table='".$table."'
        AND tal.company_id='". $get['company_id']. "' 
        AND au.is_active='0'";
        $res = $callConfig->getAllRows($query);
        return $res;
    }
    function getCsrSetting($get){
        global $callConfig;
        $id = $get['id'];
        $table = $get['table'];
        $query = "SELECT * FROM  csr_setting WHERE company_id='". $get['company_id']. "' AND is_active='0'";
        $res = $callConfig->getRow($query);
        return $res;
    }
    // MAP Point 
    function getCompaniesData($get){
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
            $query1 = "SELECT id,user_id,  plan_date, plan_longitude as lng, plan_latitude as lat , plan_location as name  From field_work_daily_plans Where user_id='" . $userid . "' AND plan_date='" . $map_date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
            // return $query1;
            $query1 = $callConfig->getAllRows($query1);
            $assignedLocations  = array();
            $i = 1;
            foreach ($query1 as $row) {
                $assignedLocations[$i] = $row;
                $i++;
            }
            $return['assignedLocations'] = $assignedLocations;
        }

        $query2 = "SELECT user_id, map_latitude as lat, map_longitude as lng, map_date, map_title as name,  map_time,field_work_daily_plan_id From field_work_user_map_points Where user_id='" . $userid . "' AND map_date='" . $map_date . "' AND company_id='" . $get['company_id'] . "' AND is_active =0 ";
        $query2 = $callConfig->getAllRows($query2);
        $visitedLocations   = array();
        $i = 1;
        foreach ($query2 as $row) {
            $visitedLocations[$i] = $row;
            $i++;
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
        $userids_array = explode(',', $userids_string);
    
        foreach ($userids_array as $userid) {
            $query = "SELECT display_name, user_email,  user_field_work_type FROM application_users where id='" . $userid . "'";
            $res = $callConfig->getRow($query);
            if ($res->user_field_work_type == '1' || $res->user_field_work_type == '2') {
                $query1 = "SELECT fwdp.user_id AS id, au.display_name AS display_name, plan_location as  location, plan_latitude as lat ,  plan_longitude as lng,  plan_date AS from_date, '' AS to_date  
                FROM field_work_daily_plans fwdp
                JOIN application_users au on au.id = fwdp.user_id
                where user_id='".$userid."' AND plan_date BETWEEN '".$from_date."' AND '".$to_date."' AND fwdp.company_id='" . $get['company_id'] . "' AND fwdp.is_active =0
                GROUP BY fwdp.plan_date ";
                $result = $callConfig->getAllRows($query1);
                if ($result) {
                    foreach ($result as $row) {
                        $return[] = $row;
                    }
                }
            } else {
                // $query1 = "SELECT user_id,  location_name as  location, work_latitude as lat, work_longitude as lng,  '" . $from_date . "' AS plan_date FROM user_field_work_details WHERE user_id='".$userid . "' AND  '".$from_date."' >= work_from_date AND '".$to_date."' <= work_to_date AND company_id='".$get['company_id'] . "' AND is_active =0 ";
                $query1="SELECT 
                    ufwd.user_id AS id, 
                    au.display_name AS display_name ,
                    location_name AS location, 
                    work_latitude AS lat, 
                    work_longitude AS lng, 
                    work_from_date AS from_date, 
                    work_to_date AS to_date
                FROM     user_field_work_details ufwd
                JOIN application_users au on au.id = ufwd.user_id
                WHERE 
                    ufwd. user_id = '".$userid . "'
                    AND ufwd. company_id = '".$get['company_id'] . "'
                    AND ufwd.is_active = 0
                    AND (
                        ('".$from_date."' BETWEEN work_from_date AND work_to_date) OR
                        ('".$to_date."' BETWEEN work_from_date AND work_to_date) OR
                        (work_from_date BETWEEN '".$from_date."' AND '".$to_date."') OR
                        (work_to_date BETWEEN '".$from_date."' AND '".$to_date."')
                    )";
                $result = $callConfig->getAllRows($query1);
                if ($result) {
                    foreach ($result as $row) {
                        $return[] = $row;
                    }
                }
            } 
            
        }
        return $return;
    }

    function fetchFieldWorkDailyPlans($get){
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
    function updateFieldWorkDailyPlans($get){
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

    function getApplicationUsersData($get){
        global $callConfig;
        $return = array();
        $admin_type = $get['admin_type'];

        $whr = "";
        if ($admin_type == '2') {
            $whr = "reports_to ='" . $get['user_id'] . "' AND ";
        } elseif ($admin_type == '3') {
            $whr = "id = '" . $get['user_id'] . "' AND ";
        }
        $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE company_id='" . $get['company_id'] . "' AND  " . $whr . " is_active =0";
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
}
?>