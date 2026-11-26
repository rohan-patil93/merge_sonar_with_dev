<?php

class CsrConstants
{
    public const UPDATED_BY_APOS = "', updated_by='";
    public const SEL_APP_USERS_FIELD_WORK = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE company_id='";
    public const AND_IS_ACTIVE_0_SPACE = "' AND is_active =0 ";
    public const AND_COMPANY_ID_APOS = "' AND company_id='";
    public const APOS_AND_APOS = "' AND '";
    public const UPDATE_PREFIX = "UPDATE ";
    public const APOS_AND_SPACE = "' AND ";
    public const ID_EQ_APOS = "id = '";
    public const AND_COMPANY_ID_EQ_APOS = "' AND company_id = '";
    public const AND_IS_ACTIVE_0_NO_SPACE = "' AND is_active=0";
    public const DATE_FORMAT_YMD_HIS = "Y-m-d H:i:s";
    public const AND_IS_ACTIVE_0_DOUBLE_SPACE = "' AND  is_active=0";
    public const AND_IS_ACTIVE_0_STANDARD = "' AND is_active = 0";
    public const AND_PROJECT_ID_IN = " AND project_id IN (";
    public const AND_ONE_EQ_ZERO = " AND 1=0";
    public const AND_P_FIN_YEAR = " AND p.financial_year = '";
    public const IN_PROGRESS = "In Progress";
    public const PROPOSAL_NOT_FOUND = "Proposal not found";
    public const AND_MAP_DATE_LTE = "' AND map_date <= '";
    public const SEL_CC_ACTIVE = "SELECT COUNT(DISTINCT user_id) AS cc_active FROM field_work_user_map_points WHERE company_id = ";
    public const AND_MAP_DATE_EQ = " AND map_date = '";
    public const AND_FWDP_ACTIVE_0 = "' AND fwdp.is_active = 0";
    public const LIT_APPROVAL_PROPOSALS = "SELECT cpd.id, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal\n        LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id\n        WHERE tal.user_id = '";
    public const LIT_GENC_CSR_FIN_YEAR = "'\n            AND genc_code = 'CSR_FINANCIAL_YEAR'\n            AND is_active = 0\n            ORDER BY id ASC";
    public const LIT_IS_ACTIVE_0_NL = "'\n            AND is_active = 0";
    public const LIT_SEL_GENC_DETAILS = "SELECT id, genc_detail_code, genc_detail_name\n            FROM general_code_details\n            WHERE company_id = '";
    public const LIT_IS_ACTIVE_0_INDENT4 = "'\n                AND is_active = 0";
    public const LIT_CASE_INV_FIN_YEAR = " AND (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) = '";
    public const LIT_P_IS_ACTIVE_0_NL = "'\n            AND p.is_active = 0";
    public const LIT_P_IS_ACTIVE_0_STATUS = "'\n            AND p.is_active = 0\n            AND p.current_status = ";
    public const LIT_ORDER_P_ID_DESC = "\n            ORDER BY p.id DESC";
    public const LIT_FIN_YEAR_IS_NULL = "' AND is_active = 0 AND financial_year IS NULL";
    public const LIT_INV_IS_ACTIVE_0_NL = "'\n                AND inv.is_active = 0";
    public const LIT_P_IS_ACTIVE_0_STATUS_SPACE = "' AND p.is_active = 0 AND p.current_status = ";
    public const LIT_AND_COMPANY_ID_NL = "'\n            AND company_id = '";
}

trait CsrFieldWorkTrait
{
public function insertData($get)
    {
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
                $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS, array('serial_number' => $query_res->serial_number + 1), 'id', $query_res->id);
            }

        } else {
            // Get EXISTING transaction_no
            $q2 = "SELECT transaction_no FROM csr_proposal_data WHERE id = '" . $get['old_id'] . "'";
            $row2 = $callConfig->getRow($q2);
            $transaction_no = $row2->transaction_no;
        }

        if ($get['old_id'] && $get['old_id'] != '') {
            $id = $get['old_id'];
            $updated_date = date('Y-m-d h:i:s');
            $updated_by = $get['user_id'];

            $query = "UPDATE csr_proposal_data  SET is_active = 1 , updated_date='" . $updated_date . CsrConstants::UPDATED_BY_APOS . $updated_by . "'  WHERE id= '" . $id . "'";
            $callConfig->executeQuery($query);
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

            return $callConfig->executeQuery($query);
        }
    }

    public function getCompletedProjects($get)
    {
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
        return $callConfig->getAllRows($query);
    }

    public function getTracckingPerentage($get)
    {
        global $callConfig;
        $rid = $get['id'];
        $query = "SELECT
    COUNT(*) AS total_rows,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count
    FROM csr_project_tracking
    WHERE proposal_id=" . $rid . " AND company_id='" . $get['company_id'] . "'";
        return $callConfig->getRow($query);
    }

    public function getCommitteMembers($get)
    {
        global $callConfig;
        $query = "SELECT au.id, au.display_name, au.user_email FROM application_user_module_roles aumr LEFT JOIN application_users au ON au.id = aumr.user_id
    WHERE aumr.role_short_code='CSR_COMMITTEE_MEMBER'
    AND aumr.company_id='" . $get['company_id'] . "'
    AND aumr.is_active='0'";
        return $callConfig->getAllRows($query);
    }
    private function loadCsrProposalApprovals($id, $company_id, $current_status, array &$return): void
    {
        global $callConfig;
        $commonWhere = "WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data' AND tal.company_id='" . $company_id . "' AND cpd.is_active='0'";

        $qTotal = "SELECT cpd.id, cpd.date, cpd.project_start_date, cpd.project_end_date, cpd.subject, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id " . $commonWhere . " ORDER BY cpd.id DESC";
        $return['total_records'] = (array) ($callConfig->getAllRows($qTotal) ?: array());

        $qPending = CsrConstants::LIT_APPROVAL_PROPOSALS . $id . "' AND tal.transaction_table='csr_proposal_data' AND (tal.response_type = 0 OR tal.response_type IS NULL) AND tal.company_id='" . $company_id . "' AND cpd.is_active='0' AND cpd.current_status='" . $current_status . "' ORDER BY cpd.id DESC";
        $return['pending_approvals'] = (array) ($callConfig->getAllRows($qPending) ?: array());

        $qApprovedByMe = CsrConstants::LIT_APPROVAL_PROPOSALS . $id . "' AND tal.transaction_table='csr_proposal_data' AND tal.response_type != 0 AND tal.company_id='" . $company_id . "' AND cpd.is_active='0' ORDER BY cpd.id DESC";
        $return['approved_by_me'] = (array) ($callConfig->getAllRows($qApprovedByMe) ?: array());

        $qInprogress = CsrConstants::LIT_APPROVAL_PROPOSALS . $id . "' AND tal.transaction_table='csr_proposal_data' AND tal.previous_status < cpd.current_status AND tal.company_id='" . $company_id . "' AND cpd.is_active='0' AND cpd.current_status NOT IN ('199', '30', '20', '0') ORDER BY cpd.id DESC";
        $return['approval_inprogress'] = (array) ($callConfig->getAllRows($qInprogress) ?: array());

        $qApproved = "SELECT cpd.id, cpd.date, cpd.subject, cpd.project_start_date, cpd.project_end_date, cpd.institue_name, cpd.created_by, cpd.total_budget_grand_total, cpd.current_status, cpd.tracking_status FROM transaction_approval_logs tal LEFT JOIN csr_proposal_data cpd ON cpd.id = tal.transaction_id " . $commonWhere . " AND cpd.current_status = '199' ORDER BY cpd.id DESC";
        $return['approved'] = (array) ($callConfig->getAllRows($qApproved) ?: array());
    }

    private function loadCsrProjectTrackingSummary($id, $company_id, array &$return): void
    {
        global $callConfig;
        $qPending = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total,current_status, tracking_status from csr_proposal_data WHERE tracking_status=0 AND id in (SELECT cpt.proposal_id FROM transaction_approval_logs tal LEFT JOIN csr_project_tracking cpt ON cpt.id = tal.transaction_id WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_project_tracking' AND tal.response_type = 0 AND tal.previous_status = cpt.current_status AND tal.company_id='" . $company_id . "' AND cpt.is_active='0') ORDER BY id DESC";
        $return['tracking_pending'] = (array) ($callConfig->getAllRows($qPending) ?: array());

        $qInprogress = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total,current_status, tracking_status from csr_proposal_data WHERE id in (SELECT cpt.proposal_id FROM transaction_approval_logs tal LEFT JOIN csr_project_tracking cpt ON cpt.id = tal.transaction_id WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_project_tracking' AND tal.previous_status < cpt.current_status AND tal.company_id='" . $company_id . "' AND cpt.is_active='0') AND tracking_status = 0 ORDER BY id DESC";
        $return['tracking_inprogress'] = (array) ($callConfig->getAllRows($qInprogress) ?: array());

        $qCompleted = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status from csr_proposal_data WHERE tracking_status=1 AND id in (SELECT tal.transaction_id FROM transaction_approval_logs tal WHERE tal.user_id = '" . $id . "' AND tal.transaction_table='csr_proposal_data' AND tal.company_id='" . $company_id . "') ORDER BY id DESC";
        $return['tracking_completed'] = (array) ($callConfig->getAllRows($qCompleted) ?: array());

        $qPercent = "SELECT proposal_id, ROUND((SUM(IF(`status` = 'completed', 1, 0)) / COUNT(id)) * 100) AS percent FROM csr_project_tracking WHERE company_id = '" . $company_id . "' AND proposal_id IN (SELECT cpd.id FROM csr_proposal_data cpd INNER JOIN transaction_approval_logs tal ON cpd.id = tal.transaction_id WHERE tal.user_id = '" . $id . "' AND cpd.company_id = '" . $company_id . "' AND cpd.is_active = 0) AND is_active = 0 GROUP BY proposal_id ORDER BY id DESC";
        $percentRows = $callConfig->getAllRows($qPercent);
        $tracking_percentage = array();
        if (!empty($percentRows)) {
            foreach ($percentRows as $row) {
                $tracking_percentage[$row->proposal_id] = $row->percent;
            }
        }
        $return['tracking_percentage'] = $tracking_percentage;
    }

    public function getCsrData($get)
    {
        $return = array();
        $id = $get['id'] ?? '';
        $current_status = $get['current_status'] ?? '';
        $company_id = $get['company_id'] ?? '';

        $this->loadCsrProposalApprovals($id, $company_id, $current_status, $return);
        $this->loadCsrProjectTrackingSummary($id, $company_id, $return);

        return $return;
    }
    public function getApprovedMembers($get)
    {
        global $callConfig;
        $id = $get['id'];
        $table = $get['table'];
        $query = "SELECT au.id, au.display_name, au.user_email FROM transaction_approval_logs tal
    LEFT JOIN application_users au ON au.id = tal.user_id
    WHERE tal.transaction_id = '" . $id . "' AND tal.transaction_table='" . $table . "'
    AND tal.company_id='" . $get['company_id'] . "'
    AND au.is_active='0'";
        return $callConfig->getAllRows($query);
    }

    public function getCsrSetting($get)
    {
        global $callConfig;
        $query = "SELECT * FROM  csr_setting WHERE company_id='" . $get['company_id'] . "' AND is_active='0'";
        return $callConfig->getRow($query);
    }


    // MAP Point
    public function getCompaniesData($get)
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

        $application_users_details = CsrConstants::SEL_APP_USERS_FIELD_WORK . $get['company_id'] . "'
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

        $companies_details = "SELECT id, name  FROM  companies WHERE  company_id='" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
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

    public function getMapPoints($get)
    {
        global $callConfig;
        $return = array();
        $userid = $get['userid'];
        $map_date = $get['map_date'];
        $user_field_work_type = $get['user_field_work_type'];
        if ($user_field_work_type == '1') {
            $query1 = "SELECT id, user_id, plan_date, plan_longitude as lng, plan_latitude as lat, plan_location as name From field_work_daily_plans Where user_id='" . $userid . "' AND plan_date='" . $map_date . CsrConstants::AND_COMPANY_ID_APOS . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
            $query1 = $callConfig->getAllRows($query1);
            $assignedLocations = array();
            $i = 1;
            if (count((array) $query1) > 0) {
                foreach ($query1 as $row) {
                    $assignedLocations[$i] = $row;
                    $i++;
                }
            }
            $return['assignedLocations'] = $assignedLocations;
        }

        $query2 = "SELECT id, user_id, map_latitude as lat, map_longitude as lng, map_date, map_title as name, map_time, field_work_daily_plan_id From field_work_user_map_points Where user_id='" . $userid . "' AND map_date='" . $map_date . CsrConstants::AND_COMPANY_ID_APOS . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
        $query2 = $callConfig->getAllRows($query2);
        $visitedLocations = array();
        $i = 1;
        if (count((array) $query2) > 0) {
            foreach ($query2 as $row) {
                $visitedLocations[$i] = $row;
                $i++;
            }
        }
        $return['visitedLocations'] = $visitedLocations;
        return $return;
    }

    private function resolveFieldWorkUserIds($get): array
    {
        global $callConfig;
        $userids_string = $get['userids'] ?? '';
        $managerids_string = $get['manager_ids'] ?? '';
        $userids_array = array();

        if ($userids_string != '') {
            foreach (explode(',', $userids_string) as $userid) {
                if ($userid != '' && $userid != '0' && is_numeric($userid)) {
                    $userids_array[] = (int) $userid;
                }
            }
        }

        if ($managerids_string && empty($userids_string)) {
            $query = "SELECT id, display_name FROM application_users WHERE reports_to in (" . $managerids_string . ") AND company_id='" . $get['company_id'] . "' AND is_active =0";
            $result = $callConfig->getAllRows($query);
            if ($result) {
                foreach ($result as $row) {
                    if ($row->id != '' && $row->id != '0' && is_numeric($row->id)) {
                        $userids_array[] = (int) $row->id;
                    }
                }
            }
        }

        return $userids_array;
    }

    private function fetchFieldWorkPlansAndDetails(array $userids_array, $from_date, $to_date, $company_id, array &$return): void
    {
        global $callConfig;
        $ids = implode(',', $userids_array);

        $query1 = "SELECT fwdp.user_id AS id, au.display_name AS display_name, plan_location AS location, plan_latitude AS lat, plan_longitude AS lng, plan_date AS from_date, '' AS to_date FROM field_work_daily_plans fwdp JOIN application_users au ON au.id = fwdp.user_id WHERE fwdp.user_id IN (" . $ids . ") AND au.user_field_work_type IN ('1','2') AND fwdp.plan_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "' AND fwdp.company_id='" . $company_id . "' AND fwdp.is_active = 0 GROUP BY fwdp.user_id, fwdp.plan_date";
        $result1 = $callConfig->getAllRows($query1);
        if ($result1 && count((array) $result1) > 0) {
            foreach ($result1 as $row) {
                $return[] = $row;
            }
        }

        $query2 = "SELECT ufwd.user_id AS id, au.display_name AS display_name, location_name AS location, work_latitude AS lat, work_longitude AS lng, work_from_date AS from_date, work_to_date AS to_date FROM user_field_work_details ufwd JOIN application_users au ON au.id = ufwd.user_id WHERE ufwd.user_id IN (" . $ids . ") AND au.user_field_work_type = '3' AND ufwd.company_id = '" . $company_id . "' AND ufwd.is_active = 0 AND (('" . $from_date . "' BETWEEN work_from_date AND work_to_date) OR ('" . $to_date . "' BETWEEN work_from_date AND work_to_date) OR (work_from_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "') OR (work_to_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "'))";
        $result2 = $callConfig->getAllRows($query2);
        if ($result2 && count((array) $result2) > 0) {
            foreach ($result2 as $row) {
                $return[] = $row;
            }
        }
    }

    public function getFieldWorkDailyPlanData($get)
    {
        $return = array();
        $userids_array = $this->resolveFieldWorkUserIds($get);
        if (count($userids_array) == 0) {
            return $return;
        }

        $this->fetchFieldWorkPlansAndDetails($userids_array, $get['from_date'] ?? '', $get['to_date'] ?? '', $get['company_id'] ?? '', $return);
        return $return;
    }

    public function fetchFieldWorkDailyPlans($get)
    {
        $table = $get['table'];
        $userid = $get['userid'];
        $date = $get['date'];
        global $callConfig;
        $return = array();
        if ($table == 'field_work_daily_plans') {
            $query = "SELECT *  FROM field_work_daily_plans where user_id='" . $userid . "' AND plan_date= '" . $date . CsrConstants::AND_COMPANY_ID_APOS . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
            $result = $callConfig->getAllRows($query);
        } else {
            $query = "SELECT * FROM user_field_work_details WHERE user_id='" . $userid . "' AND  '" . $date . "' BETWEEN work_from_date AND work_to_date AND company_id='" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
            $result = $callConfig->getRow($query);
        }
        if ($result) {
            $return['qeury_reslut'] = $result;
        }
        return $return;
    }
    public function updateFieldWorkDailyPlans($get)
    {
        global $callConfig;
        $rid = $get['rid'];
        $date = $get['date'];
        $table = $get['table'];
        $updated_date = date('Y-m-d h:i:s');
        $updated_by = $get['user_id'];
        if ($table == 'field_work_daily_plans') {
            $query = CsrConstants::UPDATE_PREFIX . $table . "  SET is_active = NULL , updated_date='" . $updated_date . CsrConstants::UPDATED_BY_APOS . $updated_by . "'  WHERE user_id= '" . $rid . "' AND plan_date = '" . $date . "'";
        } else {
            $query = CsrConstants::UPDATE_PREFIX . $table . "  SET is_active = NULL , updated_date='" . $updated_date . CsrConstants::UPDATED_BY_APOS . $updated_by . "'  WHERE id = '" . $rid . "'";
        }

        return $callConfig->executeQuery($query);
    }

    public function getApplicationUsersData($get)
    {
        global $callConfig;
        $return = array();
        $admin_type = $get['admin_type'];

        $whr = "";
        if ($admin_type == '2' || $admin_type == '1') {
            $whr = "reports_to ='" . $get['user_id'] . CsrConstants::APOS_AND_SPACE;
        } elseif ($admin_type == '3') {
            $whr = CsrConstants::ID_EQ_APOS . $get['user_id'] . CsrConstants::APOS_AND_SPACE;
        }
        $application_users_details = CsrConstants::SEL_APP_USERS_FIELD_WORK . $get['company_id'] . "' AND  " . $whr . " is_active =0";
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

    public function getApplicationUsersDataforInsert($get)
    {
        global $callConfig;
        $return = array();
        $admin_type = $get['admin_type'];

        $whr = "";
        if ($admin_type == '2') {
            $whr = "reports_to ='" . $get['user_id'] . CsrConstants::APOS_AND_SPACE;
        } elseif ($admin_type == '3') {
            $whr = CsrConstants::ID_EQ_APOS . $get['user_id'] . CsrConstants::APOS_AND_SPACE;
        }
        $application_users_details = CsrConstants::SEL_APP_USERS_FIELD_WORK . $get['company_id'] . "' AND  " . $whr . " is_active =0";
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

    public function getFieldWorkUserMapPointsData($get)
    {
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
                    AND fwump.map_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "'
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
                WHERE au.id = '" . $userid . "' AND  fwump.map_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "'
                    AND fwump.company_id = '" . $get['company_id'] . "'  AND fwump.is_active = '0'
                GROUP BY  au.display_name,  au.user_email, au.user_field_work_type, fwump.map_date";
            }
            $result = $callConfig->getAllRows($query1);
            if ($result) {
                foreach ($result as $row) {
                    $return[] = $row;
                }
            }
        }
        return $return;
    }
    public function getUsersMapPointsData($get)
    {
        global $callConfig;
        $date = date('Y-m-d');
        $return = array();
        $query = "SELECT t1.id, t1.user_id,au.display_name, t1.map_date, t1.map_latitude as lat, t1.map_longitude as lng, t1.map_time
    FROM field_work_user_map_points t1
    JOIN (
        SELECT user_id, MAX(id) AS max_id
        FROM field_work_user_map_points
        WHERE map_date = '" . $date . CsrConstants::AND_COMPANY_ID_APOS . $get['company_id'] . "' AND is_active='0'
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
    public function getEmployeeData($get)
    {
        global $callConfig;
        $return = array();
        $userids_string = $get['userids'];
        $application_users_details = "SELECT id, display_name, user_field_work_type FROM  application_users WHERE reports_to in (" . $userids_string . ") AND company_id='" . $get['company_id'] . "' AND  is_active =0";
        $application_users_details = $callConfig->getAllRows($application_users_details);
        $return['qeury_reslut'] = $application_users_details;
        return $return;
    }

    public function checkExistingData($get)
    {
        global $callConfig;
        $return = array();
        $userid = $get['userid'];
        $date = $get['date'];
        $query1 = "SELECT user_id,  plan_date, plan_longitude as lng, plan_latitude as lat , plan_location as name  From field_work_daily_plans Where user_id='" . $userid . "' AND plan_date='" . $date . CsrConstants::AND_COMPANY_ID_APOS . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_SPACE;
        $query1 = $callConfig->getAllRows($query1);
        $assignedLocations = array();
        $i = 1;
        foreach ($query1 as $row) {
            $assignedLocations[$i] = $row;
            $i++;
        }
        $return['assignedLocations'] = $assignedLocations;
        return $return;
    }

    // adding new function
}

trait CsrSyncAndDataTrait
{
public function getCsrProjectTracking($get)
    {
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

    public function getcsrProposalData($get)
    {
        global $callConfig;
        $return = array();
        $orgininaor = $get['orgininaor'];

        $query = "SELECT * FROM  csr_proposal_data where orgininaor = '" . $orgininaor . CsrConstants::AND_COMPANY_ID_EQ_APOS . $get['company_id'] . "' AND is_active=0 ORDER BY id DESC";
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

    public function getAdminDashBordData($get)
    {
        global $callConfig;
        $return = array();
        $query = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM  csr_proposal_data where  company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_NO_SPACE;
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
    public function getCSRHistory($get)
    {
        global $callConfig;
        $return = array();
        $from_date = $get['from_date'];
        $to_date = $get['to_date'];

        $query1 = "SELECT  id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM  csr_proposal_data
        where project_start_date >= '" . $from_date . "'   AND project_start_date <= '" . $to_date . CsrConstants::AND_COMPANY_ID_EQ_APOS . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $query_res = $callConfig->getAllRows($query1);
        $csr_history = array();
        foreach ($query_res as $row) {
            $csr_history[] = $row;
        }
        $return['csr_history'] = $csr_history;
        return $return;

    }
    public function getCSRYearlyBudget($get)
    {
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';

        if (isset($rid) && $rid != '') {
            $query1 = "SELECT * FROM  csr_yearly_budget
            where company_id = '" . $get['company_id'] . "' AND id = " . $rid . " AND is_active=0";
            $query_res = $callConfig->getRow($query1);
            $csr_yearly_budget = array();
            if (!empty($query_res)) {
                $csr_yearly_budget = $query_res;
            }
            $return['csr_yearly_budget'] = $csr_yearly_budget;

            $query2 = "SELECT id, date, subject, project_start_date, project_end_date, institue_name, created_by, total_budget_grand_total, current_status, tracking_status FROM `csr_proposal_data` WHERE project_start_date like '%$query_res->base_year%'
             AND company_id = '" . $get['company_id'] . "' AND is_active=0 ";
            $return['query2'] = $query2;


            $query2_res = $callConfig->getAllRows($query2);
            $csr_proposal_arr = array();

            if (!empty($query2_res)) {
                foreach ($query2_res as $row) {
                    $csr_proposal_arr[] = $row;
                }
            }
            $return['csr_proposal_arr'] = $csr_proposal_arr;

        } else {
            $query1 = "SELECT * FROM  csr_yearly_budget
            where company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $query_res = $callConfig->getAllRows($query1);
            $csr_yearly_budget = array();
            if (!empty($query_res)) {
                foreach ($query_res as $row) {
                    $csr_yearly_budget[$row->id] = $row;
                }
            }
            $return['csr_yearly_budget'] = $csr_yearly_budget;
        }
        return $return;
    }

    public function getCSRContractors($get)
    {
        global $callConfig;
        $return = array();
        $id = $get['rid'] ?? '';
        $whr = '';
        if (isset($id) && $id != '') {
            $whr = " AND id = " . $id;
        }
        $query1 = "SELECT * FROM  csr_contractor_registations
        where company_id = '" . $get['company_id'] . "' " . $whr . " AND is_active=0";
        $query_res = $callConfig->getAllRows($query1);
        $csr_contractors = array();
        foreach ($query_res as $row) {
            $csr_contractors[] = $row;
        }
        $return['csr_contractors'] = $csr_contractors;

        return $return;
    }

    public function getAllCSRData($get)
    {
        global $callConfig;
        $return = array();
        $query = "SELECT * FROM  csr_proposal_data WHERE company_id = '" . $get['company_id'] . "' AND current_status>0  AND is_active=0";
        $query_res = $callConfig->getAllRows($query);
        $csr_proposal_arr = array();
        foreach ($query_res as $row) {
            $csr_proposal_arr[] = $row;
        }
        $return['csr_proposal_arr'] = $csr_proposal_arr;


        $query1 = "SELECT * FROM  csr_contractor_registations WHERE company_id = '" . $get['company_id'] . "' AND current_status>0  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $csr_contractor_arr = array();
        foreach ($query_res1 as $row) {
            $csr_contractor_arr[] = $row;
        }
        $return['csr_contractor_arr'] = $csr_contractor_arr;

        $query2 = "SELECT * FROM  csr_project_tracking WHERE company_id = '" . $get['company_id'] . "' AND current_status>0  AND is_active=0";
        $query_res2 = $callConfig->getAllRows($query2);
        $project_tracking_arr = array();
        foreach ($query_res2 as $row) {
            $project_tracking_arr[] = $row;
        }
        $return['project_tracking_arr'] = $project_tracking_arr;

        // --- transaction_approval_logs as [table][id][] ---
        $query5 = "SELECT * FROM transaction_approval_logs WHERE company_id = '" . $get['company_id'] . "' AND transaction_table IN ('csr_contractor_registations', 'csr_proposal_data', 'csr_project_tracking')";
        $query_res5 = $callConfig->getAllRows($query5);
        $transaction_approval_arr = array();
        foreach ($query_res5 as $row) {
            $row = (array) $row; // Ensure $row is an array
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
        $query3 = "SELECT * FROM notifications WHERE company_id = '" . $get['company_id'] . "' AND application_table_name IN ('csr_contractor_registations', 'csr_proposal_data', 'csr_project_tracking')";
        $query_res3 = $callConfig->getAllRows($query3);

        $notifications_arr = array();
        $notification_user_arr = array();
        foreach ($query_res3 as $row) {
            $row = (array) $row; // Ensure $row is an array
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
            $query4 = "SELECT * FROM notification_users WHERE company_id = '" . $get['company_id'] . "' AND notification_id = '" . $row['id'] . "'";
            $query_res4 = $callConfig->getRow($query4);
            if (!empty($query_res4)) {
                $notification_user_arr[$row['id']] = $query_res4;
            }
        }
        $return['notifications_arr'] = $notifications_arr;
        $return['notification_user_arr'] = $notification_user_arr;

        return $return;
    }


    private function syncCsrMainTables($company_id, array $mainTables, array &$return): void
    {
        global $callConfig;
        $primaryKey = 'id';
        $timestampColumn = 'updated_date';

        foreach ($mainTables as $tableName => $rows) {
            if (count((array) $rows) == 0) {
                continue;
            }
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
                    $escapedRow[$column] = is_null($value) ? "NULL" : (is_numeric($value) ? $value : "'" . $callConfig->real_escape_string($value) . "'");
                }

                if (isset($existingRows[$pkValue]) && $existingRows[$pkValue] != '') {
                    if (!empty($existingRows[$pkValue][$timestampColumn]) && !empty($timestampValue) && $existingRows[$pkValue][$timestampColumn] < $timestampValue) {
                        $set = [];
                        foreach ($escapedRow as $column => $value) {
                            $set[] = "`$column` = $value";
                        }
                        $updateQuery = "UPDATE `$tableName` SET " . implode(", ", $set) . " WHERE `$primaryKey` = '$pkValue'";
                        $return[$tableName]['updateQuery_' . $key] = $callConfig->executeQuery($updateQuery);
                    }
                } else {
                    $columns = implode(", ", array_keys($escapedRow));
                    $values = implode(", ", array_values($escapedRow));
                    $insertQuery = "INSERT INTO `$tableName` ($columns) VALUES ($values)";
                    $return[$tableName]['insertQuery_' . $key] = $callConfig->executeQueryString($insertQuery);
                }
            }
        }
    }

    private function syncCsrApprovalLogs($company_id, array $approvalArr, array &$return): void
    {
        global $callConfig;
        if (empty($approvalArr)) {
            return;
        }
        $logPairs = [];
        foreach ($approvalArr as $table => $idRows) {
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
                $row = (array) $row;
                $existingLogs[$row['transaction_table']][$row['transaction_id']] = true;
            }
        }
        $return['logPairs'] = $logPairs;
        $return['existingLogs'] = $existingLogs;
        foreach ($approvalArr as $table => $idRows) {
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
                        $return['res'][] = $callConfig->executeQueryString($insertQuery);
                    }
                }
            }
        }
    }

    private function syncCsrNotifications($company_id, array $notificationsArr, array $notifUserArr, array &$return): void
    {
        global $callConfig;
        if (empty($notificationsArr)) {
            return;
        }
        $notifPairs = [];
        foreach ($notificationsArr as $table => $idRows) {
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
                $row = (array) $row;
                $existingNotifs[$row['application_table_name']][$row['transaction_number']] = true;
            }
        }

        foreach ($notificationsArr as $table => $idRows) {
            foreach ($idRows as $tid => $rows) {
                if (!isset($existingNotifs[$table][$tid])) {
                    foreach ($rows as $row) {
                        $notificationId = $row['id'];
                        unset($row['id']);
                        $insertary = array();
                        foreach ($row as $column => $value) {
                            $insertary[$column] = $value;
                        }
                        $res = $callConfig->insertRecord('notifications', $insertary);
                        $lastId = $res['LASTID'] ?? null;
                        if (!empty($notifUserArr[$notificationId]) && $lastId) {
                            $userRow = $notifUserArr[$notificationId];
                            unset($userRow['id']);
                            $userRow['notification_id'] = $lastId;
                            $escapedUserRow = [];
                            foreach ($userRow as $col => $val) {
                                $escapedUserRow[$col] = is_null($val) ? "NULL" : "'" . $callConfig->real_escape_string($val) . "'";
                            }
                            $insertUserQuery = "INSERT INTO notification_users (" . implode(", ", array_keys($escapedUserRow)) . ") VALUES (" . implode(", ", array_values($escapedUserRow)) . ")";
                            $callConfig->executeQueryString($insertUserQuery);
                        }
                    }
                }
            }
        }
    }

    public function tovCSRObjOthrSideCornJob($get, $post)
    {
        $return = array();
        $data = json_decode($post['data'] ?? '', 1) ?: array();
        $company_id = $get['company_id'] ?? '';

        $mainTables = [
            'csr_project_tracking' => $data['project_tracking_arr'] ?? [],
            'csr_contractor_registations' => $data['csr_contractor_arr'] ?? [],
            'csr_proposal_data' => $data['csr_proposal_arr'] ?? [],
        ];

        $this->syncCsrMainTables($company_id, $mainTables, $return);
        $return['transaction_approval_arr'] = $data['transaction_approval_arr'] ?? [];
        $this->syncCsrApprovalLogs($company_id, $data['transaction_approval_arr'] ?? [], $return);
        $this->syncCsrNotifications($company_id, $data['notifications_arr'] ?? [], $data['notification_user_arr'] ?? [], $return);

        return $return;
    }

    public function updateCSRTableData($get)
    {
        global $callConfig;
        $updated_date = date(CsrConstants::DATE_FORMAT_YMD_HIS);
        $query = CsrConstants::UPDATE_PREFIX . $get['table_name'] . "  SET current_status = 199 , updated_date='" . $updated_date . CsrConstants::UPDATED_BY_APOS . $get['user_id'] . "'  WHERE id = '" . $get['transaction_id'] . "'";
        return $callConfig->executeQuery($query);

    }

    public function getUsersData($get)
    {
        global $callConfig;
        $return = array();
        $query = "SELECT * FROM  application_users WHERE company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $query_res = $callConfig->getAllRows($query);
        $application_users_arr = array();
        foreach ($query_res as $row) {
            $application_users_arr[] = $row;
        }
        $return['application_users_arr'] = $application_users_arr;


        $query1 = "SELECT * FROM  application_user_module_roles WHERE company_id = '" . $get['company_id'] . "'  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $user_module_roles_arr = array();
        foreach ($query_res1 as $row) {
            $user_module_roles_arr[] = $row;
        }
        $return['user_module_roles_arr'] = $user_module_roles_arr;


        return $return;
    }

    public function insertUsersData($get, $post)
    {
        global $callConfig;
        $return = array();
        $data = json_decode($post['data'], 1);

        $query = "SELECT id FROM  application_users WHERE company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $query_res = $callConfig->getAllRows($query);
        $application_users_arr = array();
        foreach ($query_res as $row) {
            $application_users_arr[] = $row->id;
        }

        $query1 = "SELECT id FROM  application_user_module_roles WHERE company_id = '" . $get['company_id'] . "'  AND is_active=0";
        $query_res1 = $callConfig->getAllRows($query1);
        $user_module_roles_arr = array();
        foreach ($query_res1 as $row) {
            $user_module_roles_arr[] = $row->id;
        }

        foreach ($data['application_users_arr'] as $row) {
            if (!in_array($row['id'], $application_users_arr)) {
                $insertary = array();
                foreach ($row as $column => $value) {
                    $insertary[$column] = $value;
                }
                $res = $callConfig->insertRecord('application_users', $insertary);
                $return['application_users'][] = $res;
            }
        }

        foreach ($data['user_module_roles_arr'] as $row) {
            if (!in_array($row['id'], $user_module_roles_arr)) {
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

    public function getCSRRelatedData($get)
    {
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

    private function syncNotificationsRows($company_id, array $rows, array &$return): void
    {
        global $callConfig;
        $existingIds = array_column(
            $callConfig->getAllRows("SELECT id FROM notifications WHERE company_id = '" . $company_id . "'") ?: array(),
            'id'
        );
        foreach ($rows as $row) {
            if (!in_array($row['id'], $existingIds)) {
                $return['notifications']['inserted'][] = $callConfig->insertRecord('notifications', $row);
            }
        }
    }

    private function syncGenericTableData($company_id, string $table, string $dateField, array $rows, array &$return): void
    {
        global $callConfig;
        $existingMap = [];
        $existingRows = $callConfig->getAllRows("SELECT id, $dateField FROM $table WHERE company_id = '" . $company_id . "'") ?: array();
        foreach ($existingRows as $r) {
            $existingMap[$r->id] = $r->$dateField;
        }

        foreach ($rows as $row) {
            $id = $row['id'];
            $incomingDate = $row[$dateField] ?? null;
            $existingDate = $existingMap[$id] ?? null;
            $dataArray = $row;
            if (array_key_exists($id, $existingMap)) {
                $incomingTime = strtotime($incomingDate);
                $existingTime = strtotime($existingDate);
                if ($incomingTime && (!$existingTime || $incomingTime > $existingTime)) {
                    unset($dataArray['id']);
                    $return[$table]['updated'][] = $callConfig->updateRecord($table, $dataArray, 'id', $id);
                } else {
                    $return[$table]['existed'][] = $id;
                }
            } else {
                $return[$table]['inserted'][] = $callConfig->insertRecord($table, $dataArray);
            }
        }
    }

    public function insertOrUpdateCSRData($get, $post)
    {
        $data = json_decode($post['data'] ?? '', true) ?: array();
        $return = array();
        $company_id = $get['company_id'] ?? '';
        $dateFieldMap = [
            'transaction_approval_logs' => 'response_date',
            'notification_user' => 'read_datetime',
        ];
        foreach ($data as $table => $rows) {
            if (empty($rows)) {
                continue;
            }
            if ($table === 'notifications') {
                $this->syncNotificationsRows($company_id, $rows, $return);
            } else {
                $dateField = $dateFieldMap[$table] ?? 'updated_date';
                $this->syncGenericTableData($company_id, $table, $dateField, $rows, $return);
            }
        }
        return $return;
    }

    public function getSingleTableData($get)
    {
        global $callConfig;
        $return = array();
        $table = $get['table_name'];
        $company_id = $get['company_id'];
        $from_db = $get['data_name'];
        $to_db = $get['target_db'] ?? '';
        if ($table == '') {
            return ['response' => []];
        }

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

    public function insertOrUpdateSingleTableData($get, $post)
    {
        $return = array();
        $table = $get['table_name'] ?? '';
        $data = json_decode($post['data'] ?? '', true) ?: array();
        if (empty($data['response'])) {
            return ['status' => 'No data'];
        }
        $company_id = $get['company_id'] ?? '';
        $dateFieldMap = [
            'transaction_approval_logs' => 'response_date',
            'notification_user' => 'read_datetime',
        ];

        if ($table === 'notifications') {
            $this->syncNotificationsRows($company_id, $data['response'], $return);
        } else {
            $dateField = $dateFieldMap[$table] ?? 'updated_date';
            $this->syncGenericTableData($company_id, $table, $dateField, $data['response'], $return);
        }

        return $return;
    }

    public function insertSyncLog($get, $post)
    {
        global $callConfig;

        $insertary = array(
            'company_id' => $get['company_id'],
            'from_db' => $get['data_name'],
            'to_db' => $get['target_db'],
            'table_names' => $post['table_names'],
            'last_sync_time' => date(CsrConstants::DATE_FORMAT_YMD_HIS),
            'is_active' => 0,
            'current_status' => 0,
            'created_date' => date(CsrConstants::DATE_FORMAT_YMD_HIS),
            'created_by' => $get['user_id'] ?? 1
        );

        return $callConfig->insertRecord('sync_logs', $insertary);
    }
    public function getVerticalsData($get)
    {
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

    public function getCsrViewRelatedData($get)
    {
        global $callConfig;
        $return = array();
        $rid = $get['rid'] ?? '';
        $table = $get['table'] ?? '';
        if ($rid != '' && $table != '') {
            $query = "SELECT * FROM $table WHERE company_id = '" . $get['company_id'] . "' AND id = '$rid' AND is_active=0";
            $rows = $callConfig->getRow($query);
            $return['csr_data'] = $rows;
        }
        $query1 = "SELECT id, type_of_work FROM csr_type_of_works WHERE company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $rows1 = $callConfig->getAllRows($query1);
        $csr_type_of_works = array();
        if (!empty($rows1)) {
            foreach ($rows1 as $row) {
                $csr_type_of_works[$row->id] = $row;
            }
        }
        $return['csr_type_of_works'] = $csr_type_of_works;

        $query2 = "SELECT id, csr_area FROM csr_areas WHERE company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
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

        $query = "SELECT * FROM csr_entity_name WHERE company_id = '" . $get['company_id'] . CsrConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $data = $callConfig->getAllRows($query);
        $return['entity_names'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '{$get['company_id']}' AND genc_code = 'CSR_FINANCIAL_YEAR' AND  is_active=0 ORDER BY id ASC";
        $rows = $callConfig->getAllRows($query);
        $return['financial_years'] = $rows;

        return $return;
    }

    public function getCsrInvoiceDetails($get)
    {
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
                    WHERE company_id = '" . $get['company_id'] . "'
                    AND genc_code = 'CSR_FINANCIAL_YEAR'
                    AND is_active = 0
                    ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);
        $return['fyRows'] = $fyRows;

        return $return;

    }
    public function getCsrMasterData($get)
    {
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
                    ELSE CsrConstants::IN_PROGRESS
                END AS project_status
            FROM csr_proposal_data p
            LEFT JOIN (SELECT proposal_id,COUNT(*) AS total_rows, SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) AS null_rows, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_rows FROM csr_project_tracking WHERE is_active = 0 GROUP BY proposal_id) ts ON ts.proposal_id = p.id
            LEFT JOIN csr_po_details po  ON po.project_id = p.id  AND po.is_active = 0
            LEFT JOIN csr_invoice_details inv ON inv.project_id = p.id AND inv.csr_po_details_id = po.id AND inv.company_id = p.company_id AND inv.is_active = 0
            LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work AND tow.company_id = p.company_id AND tow.is_active = 0
            LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0
            LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0
            WHERE p.current_status=" . ST_APPROVED . " AND p.is_active = 0 AND p.company_id = '" . $company_id . "' ORDER BY p.subject, po.po_no, inv.inv_date";
        //  Execute and fetch
        $res = $callConfig->getAllRows($query);
        $return['project_financial_list'] = $res;
        $return['sql'] = $query;

        return $return;
    }
}

trait CsrReportsTrait
{
    private function resolveCsrProjectSummaryYear($get, array $fyRows, array &$return): string
    {
        global $callConfig;
        $selected_year = isset($get['year']) ? $callConfig->real_escape_string($get['year']) : '';
        $todayYear = date("Y");
        $todayMonth = date("n");
        $startYear = ($todayMonth >= 4) ? $todayYear : ($todayYear - 1);
        $endYear = ($todayMonth >= 4) ? ($todayYear + 1) : $todayYear;
        $currentFY = $startYear . '-' . substr($endYear, -2);

        $selected_year_index = -1;
        if ($selected_year == '' && count($fyRows) > 0) {
            $selected_year = $currentFY;
        }
        if (count($fyRows) > 0) {
            foreach ($fyRows as $index => $fyRow) {
                if ($fyRow->genc_detail_code == $selected_year) {
                    $selected_year_index = $index;
                    break;
                }
            }
            if ($selected_year_index === -1) {
                foreach ($fyRows as $index => $fyRow) {
                    if ($fyRow->genc_detail_code == $currentFY) {
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
        return $selected_year;
    }

    private function executeCsrProjectSummaryQuery($company_id, string $selected_year): array
    {
        global $callConfig;
        $inv_fy_where = ($selected_year != '')
            ? " AND (CASE WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2)) ELSE financial_year END) = '" . $selected_year . "'"
            : "";

        $query = "SELECT loc.location_name AS location, p.subject AS project_name, p.SDGproposal AS nature_of_work_done, tow.type_of_work, ged.genc_detail_name AS type_of_project, cas.csr_area AS area, ROUND(IFNULL(po.total_po_excl_gst, 0), 2) AS total_po_excl_gst, ROUND(IFNULL(inv.total_spent_excl_gst, 0), 2) AS total_spent_excl_gst, ROUND(IFNULL(po.total_po_excl_gst, 0) - IFNULL(inv.total_spent_excl_gst, 0), 2) AS unspent_amount, CASE WHEN IFNULL(po.total_po_excl_gst, 0) > 0 THEN ROUND((IFNULL(inv.total_spent_excl_gst, 0) / po.total_po_excl_gst) * 100, 2) ELSE 0 END AS spent_percent, CASE WHEN IFNULL(po.total_po_excl_gst, 0) > 0 THEN ROUND(100 - ((IFNULL(inv.total_spent_excl_gst, 0) / po.total_po_excl_gst) * 100), 2) ELSE 0 END AS unspent_percent, CASE WHEN IFNULL(tr.total_tracking, 0) = 0 THEN 'Not Started' WHEN IFNULL(tr.null_status_count, 0) = tr.total_tracking THEN 'Not Started' WHEN p.tracking_status = 1 THEN 'Completed' ELSE CsrConstants::IN_PROGRESS END AS project_status FROM csr_proposal_data p LEFT JOIN (SELECT project_id, company_id, SUM(IFNULL(po_value_in, 0)) AS total_po_excl_gst FROM csr_po_details WHERE is_active = 0 GROUP BY project_id, company_id) po ON po.project_id = p.id AND po.company_id = p.company_id LEFT JOIN (SELECT project_id, company_id, SUM(IFNULL(total_spent, 0)) AS total_spent_excl_gst FROM csr_invoice_details WHERE is_active = 0" . $inv_fy_where . " GROUP BY project_id, company_id) inv ON inv.project_id = p.id AND inv.company_id = p.company_id LEFT JOIN (SELECT proposal_id, COUNT(id) AS total_tracking, SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) AS null_status_count FROM csr_project_tracking WHERE is_active = 0 GROUP BY proposal_id) tr ON tr.proposal_id = p.id LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0 LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work AND tow.company_id = p.company_id AND tow.is_active = 0 LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0 LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0 WHERE p.current_status = " . ST_APPROVED . " AND p.is_active = 0 AND p.company_id = $company_id ORDER BY p.csr_location, p.subject";
        return (array) ($callConfig->getAllRows($query) ?: array());
    }

    public function getCsrProjectSummaryReport($get)
    {
        global $callConfig;
        $return = array('get' => $get);
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        $fyQuery = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $company_id . CsrConstants::LIT_GENC_CSR_FIN_YEAR;
        $fyRows = (array) ($callConfig->getAllRows($fyQuery) ?: array());
        $return['financial_years'] = $fyRows;

        $selected_year = $this->resolveCsrProjectSummaryYear($get, $fyRows, $return);
        $return['project_summary_list'] = $this->executeCsrProjectSummaryQuery($company_id, $selected_year);

        return $return;
    }
    public function getCsrPoVsActualsSummary($get)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;
        $fyQuery = "SELECT genc_detail_code
            FROM general_code_details
            WHERE company_id = '" . $company_id . "'
            AND genc_code = 'CSR_FINANCIAL_YEAR' AND genc_detail_code NOT IN ('2013-14','2014-15','2015-16','2016-17', '2017-18', '2018-19', '2019-20')
            AND is_active = 0
            ORDER BY id ASC";

        $fyRows = $callConfig->getAllRows($fyQuery);
        // 🏗 Build dynamic columns for each FY
        $fyColumns = "";
        if (!empty($fyRows)) {
            foreach ($fyRows as $fyRow) {
                $fyText = trim($fyRow->genc_detail_code);      // e.g. 2013-14
                $fyKey = str_replace('-', '_', $fyText);      // e.g. 2013_14 (safe for alias)

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
            WHERE p.current_status=" . ST_APPROVED . " AND p.is_active = 0 AND p.company_id = '" . $company_id . "'
            ORDER BY p.subject, po.po_no";

        $res = $callConfig->getAllRows($query);
        $return['po_vs_actuals_list'] = $res;
        $return['sql'] = $query;

        $return['fy_list'] = $fyRows; // 📌 FY list for frontend headers
        return $return;
    }
    public function getCsrUnspentCalculationReport($get)
    {
        global $callConfig;
        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        // 📅 Fetch Financial Years from general_code_details
        $fyQuery = "SELECT genc_detail_code
            FROM general_code_details
            WHERE company_id = '" . $company_id . CsrConstants::LIT_GENC_CSR_FIN_YEAR;

        $fyRows = $callConfig->getAllRows($fyQuery);

        if (!$fyRows) {
            $return['report'] = [];
            return $return;
        }

        // 🧾 Fetch yearly budget data
        $budgetQuery = "SELECT financial_year, net_profit_before_tax_mtipl
            FROM csr_complaince
            WHERE company_id = '" . $company_id . CsrConstants::AND_IS_ACTIVE_0_STANDARD;

        $budgetData = $callConfig->getAllRows($budgetQuery);

        $budgets = [];
        foreach ($budgetData as $b) {
            $parts = explode('-', $b->financial_year);
            $endYear = (int) ('20' . $parts[1]);
            $budgets[$endYear] = (float) $b->net_profit_before_tax_mtipl;
        }

        // 💰 Fetch total spent by FY
        $spentQuery = "SELECT
            CAST(CONCAT('20', SUBSTRING_INDEX(financial_year, '-', -1)) AS UNSIGNED) AS fy,
            SUM(total_spent) as total_spent
        FROM csr_invoice_details
        WHERE company_id = '" . $company_id . "' AND is_active = 0
        GROUP BY fy";

        $spentData = $callConfig->getAllRows($spentQuery);

        $spent = [];
        foreach ($spentData as $s) {
            $spent[$s->fy] = (float) $s->total_spent;
        }

        $report = [];
        $carryForward = 0;

        foreach ($fyRows as $row) {

            $fyString = $row->genc_detail_code;  // 2019-20
            $parts = explode('-', $fyString);
            $year = (int) ('20' . $parts[1]);  // ending year

            $budget = $budgets[$year] ?? 0;
            $inMn = $budget / 1000000;

            $avg = 0;
            if (isset($budgets[$year - 1]) && isset($budgets[$year - 2]) && isset($budgets[$year - 3])) {
                $avg = round(($budgets[$year - 1] + $budgets[$year - 2] + $budgets[$year - 3]) / 3, 0);
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
    public function getCsrSummaryReport($get)
    {
        global $callConfig;
        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;

        // ✅ 1. Get yearly budgets (net profit)
        $budgets_sql = "SELECT financial_year AS year, net_profit_before_tax_mtipl AS finance_budget
                        FROM csr_complaince
                        WHERE company_id = '" . $company_id . "' AND is_active = 0
                        ORDER BY financial_year ASC";
        $budgets = $callConfig->getAllRows($budgets_sql);

        // ✅ 2. Get actual CSR spend per FY
        $spend_sql = "SELECT
                        CONCAT(YEAR(inv_date), '-', LPAD((YEAR(inv_date)+IF(MONTH(inv_date)>=4,1,0))%100,2,'0')) AS fy_label,
                        YEAR(inv_date) + IF(MONTH(inv_date)>=4,1,0) AS fy_year,
                        SUM(total_spent) AS total_spent
                    FROM csr_invoice_details
                    WHERE company_id = '" . $company_id . "' AND is_active = 0
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
                $avg = ($budgets[$i]->finance_budget + $budgets[$i - 1]->finance_budget + $budgets[$i - 2]->finance_budget) / 3;
            } elseif ($i >= 2) {
                $avg = ($budgets[$i]->finance_budget + $budgets[$i - 1]->finance_budget) / 2;
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
            if ($cf_balance_py < 0) {
                $cf_balance_py = 0;
            }

            $remaining_unspent = $current_unspent + $cf_balance_py;

            $carry_forward_prev = $remaining_unspent;

            $summary[] = [
                'year' => $year,
                'label' => ($year - 1) . '-' . substr($year, -2),
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
    private function calculateCsrYearlyAverages(array $fyRows, array $budgets, array $spent, string $selectedFY, array &$allYearsSummary): array
    {
        $carryForward = 0;
        $selectedSummary = array();

        foreach ($fyRows as $row) {
            $fyString = $row->genc_detail_code;
            $parts = explode('-', $fyString);
            $year = (int) ('20' . ($parts[1] ?? ''));

            $budget = $budgets[$year] ?? 0;
            $inMn = $budget / 1000000;

            $avg = 0;
            if (isset($budgets[$year - 1], $budgets[$year - 2], $budgets[$year - 3])) {
                $avg = round(($budgets[$year - 1] + $budgets[$year - 2] + $budgets[$year - 3]) / 3, 0);
            }

            $twoPercent = $avg > 0 ? round($avg * 0.02, 2) : 0;
            $actualSpent = $spent[$year] ?? 0;
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
                    'provision' => $provision,
                    'net_debit' => $netDebit,
                    'current_carry_forward' => $currentCarryForward
                );
            }

            $allYearsSummary[] = array(
                'year' => $fyString,
                'label' => $fyString,
                'budget' => $budget,
                'in_mn' => $inMn,
                'avg_net_profit' => $avg,
                'gross_required' => $twoPercent,
                'total_spent' => $actualSpent,
                'current_unspent' => $unspent,
                'carry_forward_previous' => $carryForwardUsed,
                'provision' => $provision,
                'net_debit' => $netDebit,
                'current_carry_forward' => $currentCarryForward
            );

            $carryForward = $currentCarryForward;
        }

        return $selectedSummary;
    }

    public function getCsrYearlySummaryReport($get)
    {
        global $callConfig;
        $return = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : 0;
        $selectedFY = isset($get['year']) ? $get['year'] : '';

        if (empty($selectedFY)) {
            $return['summary'] = array();
            return $return;
        }

        $fyQuery = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $company_id . CsrConstants::LIT_GENC_CSR_FIN_YEAR;
        $fyRows = (array) ($callConfig->getAllRows($fyQuery) ?: array());
        if (!$fyRows) {
            $return['summary'] = array();
            $return['financial_years'] = array();
            return $return;
        }

        $budgetQuery = "SELECT financial_year, net_profit_before_tax_mtipl FROM csr_complaince WHERE company_id = '" . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $budgetData = $callConfig->getAllRows($budgetQuery) ?: array();
        $budgets = array();
        foreach ($budgetData as $b) {
            $parts = explode('-', $b->financial_year);
            $endYear = (int) ('20' . ($parts[1] ?? ''));
            $budgets[$endYear] = (float) $b->net_profit_before_tax_mtipl;
        }

        $spentQuery = "SELECT CAST(CONCAT('20', SUBSTRING_INDEX(financial_year, '-', -1)) AS UNSIGNED) AS fy, SUM(total_spent) AS total_spent FROM csr_invoice_details WHERE company_id = '" . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL . " GROUP BY fy";
        $spentData = $callConfig->getAllRows($spentQuery) ?: array();
        $spent = array();
        foreach ($spentData as $s) {
            $spent[$s->fy] = (float) $s->total_spent;
        }

        $allYearsSummary = array();
        $selectedSummary = $this->calculateCsrYearlyAverages($fyRows, $budgets, $spent, $selectedFY, $allYearsSummary);

        $return['summary'] = $selectedSummary;
        $return['financial_years'] = $fyRows;

        return $return;
    }

    public function getCSRProjects($get)
    {
        global $callConfig;
        $query = "SELECT id, subject
            FROM csr_proposal_data
            WHERE company_id = {$get['company_id']}
              AND is_active = 0
            ORDER BY id ASC";

        $data = $callConfig->getAllRows($query);

        return array('table' => $data);
    }
    public function insertBulkCsrPoUpload($get, $post)
    {
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
            if ($po_available_value == 'no' && $resolved_po_no == '') {
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
            $data['status_date'] = date(CsrConstants::DATE_FORMAT_YMD_HIS);
            $data['created_date'] = date(CsrConstants::DATE_FORMAT_YMD_HIS);
            $data['company_id'] = $get['company_id'] ?? '';
            $data['created_by'] = $get['user_id'] ?? '';
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

        if (count((array) $errors) > 0) {
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

    private function processBulkInvoiceRow($i, $post, array $poMap, $commonFields, array &$missing_po_rows, array &$errors): bool
    {
        global $callConfig;
        $uploaded_po_no = trim($post['po_no'][$i] ?? '');
        $csr_po_details_id = '';

        if ($uploaded_po_no != '' && isset($poMap[$uploaded_po_no])) {
            $csr_po_details_id = $poMap[$uploaded_po_no];
        } elseif ($uploaded_po_no != '') {
            $missing_po_rows[] = array('row' => $i + 1, 'po_no' => $uploaded_po_no);
        }

        $data_array = array(
            'tracking_id' => $commonFields['tracking_id'],
            'project_id' => $commonFields['project_id'],
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
            return false;
        }

        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date'] = date(CsrConstants::DATE_FORMAT_YMD_HIS);
        $data['created_date'] = date(CsrConstants::DATE_FORMAT_YMD_HIS);
        $data['company_id'] = $commonFields['company_id'];
        $data['created_by'] = $commonFields['created_by'];
        $data['originator'] = $commonFields['originator'];

        $res = $callConfig->insertRecord('csr_invoice_details', $data);
        if (isset($res['status']) && $res['status'] == 'sucess') {
            return true;
        }
        $errors[] = array('row' => $i + 1, 'message' => $res['message'] ?? 'Failed to insert row');
        return false;
    }

    public function insertBulkCsrInvoiceUpload($get, $post)
    {
        global $callConfig;
        $tracking_id = $post['tracking_id'] ?? '';
        $project_id = $post['project_id'] ?? '';
        $company_id = $get['company_id'] ?? ($post['company_id'] ?? '');
        $created_by = $get['user_id'] ?? ($post['user_id'] ?? '');
        $originator = $get['user_id'] ?? ($post['originator'] ?? '');

        $poQuery = "SELECT id, po_no FROM csr_po_details WHERE company_id = '" . $company_id . "' AND tracking_id = '" . $tracking_id . "' AND project_id = '" . $project_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $poRows = $callConfig->getAllRows($poQuery) ?: array();
        $poMap = array();
        foreach ($poRows as $poRow) {
            $poMap[trim($poRow->po_no)] = $poRow->id;
        }

        $inserted = 0;
        $errors = array();
        $missing_po_rows = array();
        $commonFields = array(
            'tracking_id' => $tracking_id,
            'project_id' => $project_id,
            'company_id' => $company_id,
            'created_by' => $created_by,
            'originator' => $originator,
        );
        $rowCount = max(
            count($post['inv_no'] ?? array()),
            count($post['financial_year'] ?? array()),
            count($post['inv_date'] ?? array()),
            count($post['paid_date'] ?? array()),
            count($post['paid_amount'] ?? array())
        );

        for ($i = 0; $i < $rowCount; $i++) {
            if ($this->processBulkInvoiceRow($i, $post, $poMap, $commonFields, $missing_po_rows, $errors)) {
                $inserted++;
            }
        }

        if (count($errors) > 0) {
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
    public function getCsrDashboardProposalScope($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;

        // Client request: all CSR dashboard users see full company data (admin scope).
        // Set to false when role-based filtering is needed again.
        $force_admin_scope = true;
        if ($force_admin_scope) {
            $project_id_subquery = "SELECT id FROM csr_proposal_data WHERE company_id = '" . $company_id . CsrConstants::AND_IS_ACTIVE_0_STANDARD;
            return array(
                'is_admin' => true,
                'base_scope' => '',
                'proposal_where' => '',
                'project_id_subquery' => $project_id_subquery,
                'po_project_scope' => CsrConstants::AND_PROJECT_ID_IN . $project_id_subquery . ")"
            );
        }


        $user_id = isset($get['user_id']) ? $callConfig->real_escape_string($get['user_id']) : '';
        $role = isset($get['working_role_short_code']) ? $get['working_role_short_code'] : '';

        $is_admin = in_array($role, array('ADMIN', 'CSR_ADMIN'), true);
        $base_scope = '';

        if (!$is_admin) {
            if (in_array($role, array('CSR_CREATER', 'CSR_CREATOR'), true) && $user_id != '') {
                $base_scope = " AND orgininaor = '" . $user_id . "'";
            } elseif (in_array($role, array('CSR_COMMITTEE_GROUP', 'CSR_COMMITTEE_MEMBER', 'CSR_COMMITTEE_HEAD'), true) && $user_id != '') {
                $base_scope = " AND id IN (
                    SELECT tal.transaction_id FROM transaction_approval_logs tal
                    WHERE tal.user_id = '" . $user_id . "'
                    AND tal.transaction_table = 'csr_proposal_data'
                    AND tal.response_type != 0
                    AND tal.company_id = '" . $company_id . "'
                )";
            } else {
                $base_scope = CsrConstants::AND_ONE_EQ_ZERO;
            }
        }

        $project_id_subquery = "SELECT id FROM csr_proposal_data WHERE company_id = '" . $company_id . CsrConstants::AND_IS_ACTIVE_0_STANDARD . $base_scope;
        $proposal_where = '';
        if ($base_scope != '') {
            $proposal_where = str_replace('orgininaor', 'p.orgininaor', str_replace(' AND id IN', ' AND p.id IN', $base_scope));
        }

        return array(
            'is_admin' => $is_admin,
            'base_scope' => $base_scope,
            'proposal_where' => $proposal_where,
            'project_id_subquery' => $project_id_subquery,
            'po_project_scope' => CsrConstants::AND_PROJECT_ID_IN . $project_id_subquery . ")"
        );
    }
}

trait CsrDashboardTrait
{
    private function resolveCsrFirstDashboardFY($get, $company_id, array &$return): array
    {
        global $callConfig;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $fy_query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $company_id . "' AND genc_code = 'CSR_FINANCIAL_YEAR' AND is_active = 0 ORDER BY id ASC";
        $fy_rows = $callConfig->getAllRows($fy_query) ?: array();

        $selected_fy_index = -1;
        if ($selected_fy == '' && count($fy_rows) > 0) {
            $selected_fy = $fy_rows[count($fy_rows) - 1]->genc_detail_code;
        }
        foreach ($fy_rows as $index => $fy_row) {
            if ($fy_row->genc_detail_code == $selected_fy) {
                $selected_fy_index = $index;
                break;
            }
        }
        if ($selected_fy_index === -1 && count($fy_rows) > 0) {
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
            if (count($yearly_fy_codes) > 0) {
                $yearly_fy_where = " AND genc_detail_code IN (" . implode(',', $yearly_fy_codes) . ")";
            }
        }
        return array('fy_rows' => $fy_rows, 'selected_fy' => $selected_fy, 'yearly_fy_where' => $yearly_fy_where);
    }

    private function loadCsrFirstDashboardDistributions($company_id, $selected_fy, $base_scope, $po_project_scope, array &$return): void
    {
        global $callConfig;
        $proposal_base_scope = $selected_fy != '' ? " AND p.financial_year = '" . $selected_fy . "'" : "";
        $inv_fy_where = $selected_fy != '' ? CsrConstants::LIT_CASE_INV_FIN_YEAR . $selected_fy . "'" : "";

        $sector_query = "SELECT cas.id, cas.csr_area AS sector_name, COUNT(DISTINCT p.id) AS project_count, IFNULL(SUM(inv.spent_amount), 0) AS total_spent FROM csr_areas cas LEFT JOIN csr_proposal_data p ON p.working_area = cas.id AND p.company_id = cas.company_id AND p.is_active = 0 AND p.current_status = " . ST_APPROVED . $proposal_base_scope . $base_scope . " LEFT JOIN (SELECT project_id, company_id, SUM(total_spent) AS spent_amount FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $inv_fy_where . $po_project_scope . " GROUP BY project_id, company_id) inv ON inv.project_id = p.id AND inv.company_id = p.company_id WHERE cas.company_id = '" . $company_id . "' AND cas.is_active = 0 GROUP BY cas.id, cas.csr_area ORDER BY total_spent DESC, project_count DESC";
        $return['sector_distribution'] = $callConfig->getAllRows($sector_query) ?: array();

        $location_query = "SELECT loc.id, loc.location_name, COUNT(DISTINCT p.id) AS project_count, IFNULL(SUM(inv.spent_amount), 0) AS total_spent FROM locations loc LEFT JOIN csr_proposal_data p ON p.csr_location = loc.id AND p.company_id = loc.company_id AND p.is_active = 0 AND p.current_status = " . ST_APPROVED . $proposal_base_scope . $base_scope . " LEFT JOIN (SELECT project_id, company_id, SUM(total_spent) AS spent_amount FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $inv_fy_where . $po_project_scope . " GROUP BY project_id, company_id) inv ON inv.project_id = p.id AND inv.company_id = p.company_id WHERE loc.company_id = '" . $company_id . "' AND loc.is_active = 0 GROUP BY loc.id, loc.location_name ORDER BY total_spent DESC, project_count DESC";
        $return['location_distribution'] = $callConfig->getAllRows($location_query) ?: array();
    }

    private function loadCsrFirstDashboardKpis($company_id, $selected_fy, $proposal_scope, $po_project_scope, array &$return): void
    {
        global $callConfig;
        $proposal_fy_where = $selected_fy != '' ? " AND p.financial_year = '" . $selected_fy . "'" : "";
        $inv_fy_where = $selected_fy != '' ? CsrConstants::LIT_CASE_INV_FIN_YEAR . $selected_fy . "'" : "";

        $kpi_query = "SELECT (SELECT IFNULL(SUM(obligation_amount), 0) FROM csr_complaince WHERE company_id = '" . $company_id . "' AND is_active = 0" . ($selected_fy != '' ? " AND financial_year = '" . $selected_fy . "'" : "") . ") AS total_obligation, (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_STATUS_SPACE . ST_APPROVED . $proposal_fy_where . $proposal_scope . ") AS total_approved, (SELECT IFNULL(SUM(inv.total_spent), 0) FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $inv_fy_where . $po_project_scope . ") AS total_spent, (SELECT COUNT(*) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_STATUS_SPACE . ST_APPROVED . $proposal_fy_where . $proposal_scope . ") AS total_approved_projects, (SELECT COUNT(*) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . "' AND p.is_active = 0" . $proposal_fy_where . $proposal_scope . ") AS total_projects";
        $kpi_row = $callConfig->getRow($kpi_query);

        $obligation = isset($kpi_row->total_obligation) ? (float) $kpi_row->total_obligation : 0;
        $approved = isset($kpi_row->total_approved) ? (float) $kpi_row->total_approved : 0;
        $spent = isset($kpi_row->total_spent) ? (float) $kpi_row->total_spent : 0;
        $unspent = max($obligation - $spent, 0);
        $spent_percent = $obligation > 0 ? round(($spent / $obligation) * 100, 2) : 0;

        $return['kpis'] = array(
            'total_obligation' => $obligation,
            'total_approved' => $approved,
            'total_spent' => $spent,
            'unspent' => $unspent,
            'spent_percent' => $spent_percent,
            'total_approved_projects' => isset($kpi_row->total_approved_projects) ? (int) $kpi_row->total_approved_projects : 0,
            'total_projects' => isset($kpi_row->total_projects) ? (int) $kpi_row->total_projects : 0
        );
    }

    private function calculateCsrFirstDashboardProjectMetrics($company_id, $selected_fy, $proposal_scope, array &$return): void
    {
        global $callConfig;
        $proposal_fy_where = $selected_fy != '' ? " AND p.financial_year = '" . $selected_fy . "'" : "";
        $status_query = "SELECT p.id, p.subject, p.tracking_status, p.current_status, tr.total_tracking, tr.completed_tracking FROM csr_proposal_data p LEFT JOIN (SELECT proposal_id, COUNT(id) AS total_tracking, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_tracking FROM csr_project_tracking WHERE is_active = 0 GROUP BY proposal_id) tr ON tr.proposal_id = p.id WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_STATUS_SPACE . ST_APPROVED . $proposal_fy_where . $proposal_scope;
        $status_rows = $callConfig->getAllRows($status_query) ?: array();

        $completed = 0;
        $in_progress = 0;
        $not_started = 0;
        foreach ($status_rows as $row) {
            $total_tr = isset($row->total_tracking) ? (int) $row->total_tracking : 0;
            $completed_tr = isset($row->completed_tracking) ? (int) $row->completed_tracking : 0;
            if ((int) $row->tracking_status == 1 || ($total_tr > 0 && $completed_tr === $total_tr)) {
                $completed++;
            } elseif ($completed_tr > 0) {
                $in_progress++;
            } else {
                $not_started++;
            }
        }
        $return['project_status_counts'] = array('completed' => $completed, 'in_progress' => $in_progress, 'not_started' => $not_started);
    }

    public function getCsrFirstDashboardData($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);

        $fyData = $this->resolveCsrFirstDashboardFY($get, $company_id, $return);
        $fy_rows = $fyData['fy_rows'];
        $selected_fy = $fyData['selected_fy'];
        $yearly_fy_where = $fyData['yearly_fy_where'];

        $yearly_query = "SELECT fy.financial_year, IFNULL(ob.obligation, 0) AS obligation, IFNULL(po.approved_amount, 0) AS approved_amount, IFNULL(inv.spent_amount, 0) AS spent_amount FROM (SELECT genc_detail_code AS financial_year, id FROM general_code_details WHERE company_id = '" . $company_id . "' AND genc_code = 'CSR_FINANCIAL_YEAR' AND is_active = 0" . $yearly_fy_where . ") fy LEFT JOIN (SELECT financial_year, SUM(obligation_amount) AS obligation FROM csr_complaince WHERE company_id = '" . $company_id . "' AND is_active = 0 GROUP BY financial_year) ob ON ob.financial_year = fy.financial_year LEFT JOIN (SELECT p.financial_year, SUM(p.total_budget_grand_total) AS approved_amount FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . "' AND p.is_active = 0 AND p.current_status = " . ST_APPROVED . $scope['base_scope'] . " GROUP BY p.financial_year) po ON po.financial_year = fy.financial_year LEFT JOIN (SELECT (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END) AS financial_year, SUM(inv.total_spent) AS spent_amount FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $scope['po_project_scope'] . " GROUP BY (CASE WHEN inv.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(inv.financial_year, 4), '-', RIGHT(inv.financial_year, 2)) ELSE inv.financial_year END)) inv ON inv.financial_year = fy.financial_year ORDER BY fy.id ASC";
        $return['yearly_comparison'] = $callConfig->getAllRows($yearly_query) ?: array();

        $this->loadCsrFirstDashboardDistributions($company_id, $selected_fy, $scope['base_scope'], $scope['po_project_scope'], $return);
        $this->loadCsrFirstDashboardKpis($company_id, $selected_fy, $scope['proposal_where'], $scope['po_project_scope'], $return);
        $this->calculateCsrFirstDashboardProjectMetrics($company_id, $selected_fy, $scope['proposal_where'], $return);

        $return['financial_years'] = $fy_rows;
        $return['selected_financial_year'] = $selected_fy;
        $return['unmapped'] = $callConfig->getRow("SELECT COUNT(id) AS unmapped_count, IFNULL(SUM(total_spent), 0) AS unmapped_total_spent FROM csr_invoice_details WHERE company_id = '" . $company_id . "' AND is_active = 0 AND financial_year IS NULL");

        return $return;
    }

    private function loadProjectMonitoringRows($company_id, $proposal_fy_where, $proposal_scope, $selected_inv_fy_where, $po_project_scope): array
    {
        global $callConfig;
        $project_query = "SELECT p.id, p.subject, p.financial_year, p.project_start_date, p.project_end_date, p.total_budget_grand_total, p.current_status, p.tracking_status, IFNULL(tow.type_of_work, '-') AS type_of_work, IFNULL(ged.genc_detail_name, '-') AS type_of_project, IFNULL(cas.csr_area, '-') AS sector, IFNULL(NULLIF(loc.location_name, ''), '-') AS location_name, IFNULL(tr.total_tracking, 0) AS total_tracking, IFNULL(tr.completed_tracking, 0) AS completed_tracking, IFNULL(tr.yet_to_start_tracking, 0) AS yet_to_start_tracking, IFNULL(tr.inprogress_tracking, 0) AS inprogress_tracking, IFNULL(po.approved_budget, 0) AS approved_budget, IFNULL(inv.spent_budget, 0) AS spent_budget FROM csr_proposal_data p LEFT JOIN csr_type_of_works tow ON tow.id = p.type_of_work AND tow.company_id = p.company_id AND tow.is_active = 0 LEFT JOIN general_code_details ged ON ged.genc_detail_code = p.type_of_project AND ged.genc_code = 'CSR_PROJECT_TYPE' AND ged.company_id = p.company_id AND ged.is_active = 0 LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0 LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0 LEFT JOIN (SELECT proposal_id, COUNT(id) AS total_tracking, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_tracking, SUM(CASE WHEN status IS NULL OR status = '' OR status = 'yet_to_start' THEN 1 ELSE 0 END) AS yet_to_start_tracking, SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS inprogress_tracking FROM csr_project_tracking WHERE is_active = 0 GROUP BY proposal_id) tr ON tr.proposal_id = p.id LEFT JOIN (SELECT project_id, company_id, SUM(IFNULL(po_value_in, 0)) AS approved_budget FROM csr_po_details WHERE is_active = 0 GROUP BY project_id, company_id) po ON po.project_id = p.id AND po.company_id = p.company_id LEFT JOIN (SELECT project_id, company_id, SUM(IFNULL(total_spent, 0)) AS spent_budget FROM csr_invoice_details inv WHERE inv.is_active = 0" . $selected_inv_fy_where . $po_project_scope . " GROUP BY project_id, company_id) inv ON inv.project_id = p.id AND inv.company_id = p.company_id WHERE p.company_id = '" . $company_id . "' AND p.is_active = 0" . $proposal_fy_where . $proposal_scope . " ORDER BY p.id DESC";

        $project_rows = $callConfig->getAllRows($project_query) ?: array();
        $projects = array();
        foreach ($project_rows as $row) {
            $total_tr = (int) $row->total_tracking;
            $completed_tr = (int) $row->completed_tracking;
            $row->progress_percent = $total_tr > 0 ? round(($completed_tr / $total_tr) * 100) : 0;
            if ((int) $row->tracking_status == 1) {
                $row->status_label = 'Completed';
                $row->status_class = 'badge-success';
            } elseif ($completed_tr > 0) {
                $row->status_label = 'In Progress';
                $row->status_class = 'badge-primary';
            } else {
                $row->status_label = 'Not Started';
                $row->status_class = 'badge-secondary';
            }
            $projects[] = $row;
        }
        return $projects;
    }

    private function buildProjectMonitoringSummary($summary_row, $selected_obligation, array $projects, string $selected_fy, array &$insights): array
    {
        $selected_approval = isset($summary_row->selected_approval_amount) ? (float) $summary_row->selected_approval_amount : 0;
        $selected_spent = isset($summary_row->selected_spent_amount) ? (float) $summary_row->selected_spent_amount : 0;
        $unallocated = max($selected_approval - $selected_spent, 0);
        $utilization_percent = $selected_approval > 0 ? round(($selected_spent / $selected_approval) * 100, 2) : 0;

        $insights[] = "CSR utilization is at " . $utilization_percent . "% for " . ($selected_fy ?: "selected FY") . ".";
        foreach ($projects as $project_row) {
            if ($project_row->status_label == 'At Risk') {
                $insights[] = ($project_row->subject ?: 'A project') . " is at risk of delay. Monitor to avoid timeline slippage.";
                break;
            }
        }
        if ($unallocated > 0) {
            $insights[] = "Unallocated or unspent project funds are available for review.";
        }
        if (count($projects) == 0) {
            $insights[] = "No projects found for the selected financial year.";
        }

        return array(
            'total_project' => isset($summary_row->total_project) ? (int) $summary_row->total_project : 0,
            'total_obligation' => (float) $selected_obligation,
            'selected_approval_amount' => $selected_approval,
            'previous_approval_amount' => isset($summary_row->previous_approval_amount) ? (float) $summary_row->previous_approval_amount : 0,
            'selected_spent_amount' => $selected_spent,
            'previous_spent_amount' => isset($summary_row->previous_spent_amount) ? (float) $summary_row->previous_spent_amount : 0,
            'board_approval_pending' => isset($summary_row->board_approval_pending) ? (float) $summary_row->board_approval_pending : 0,
            'delayed_project' => isset($summary_row->delayed_project) ? (int) $summary_row->delayed_project : 0,
            'unallocated_funds' => $unallocated,
            'utilization_certificate_pending' => isset($summary_row->utilization_certificate_pending) ? (int) $summary_row->utilization_certificate_pending : 0,
            'utilization_percent' => $utilization_percent
        );
    }

    public function getCsrProjectMonitoringDashboardData($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);

        $fy_query = CsrConstants::LIT_SEL_GENC_DETAILS . $company_id . CsrConstants::LIT_GENC_CSR_FIN_YEAR;
        $fy_rows = (array) ($callConfig->getAllRows($fy_query) ?: array());

        $selected_fy_index = -1;
        if ($selected_fy == '' && count($fy_rows) > 0) {
            $selected_fy = $fy_rows[count($fy_rows) - 1]->genc_detail_code;
        }
        foreach ($fy_rows as $index => $fy_row) {
            if ($fy_row->genc_detail_code == $selected_fy) {
                $selected_fy_index = $index;
                break;
            }
        }
        if ($selected_fy_index === -1 && count($fy_rows) > 0) {
            $selected_fy_index = count($fy_rows) - 1;
            $selected_fy = $fy_rows[$selected_fy_index]->genc_detail_code;
        }

        $previous_fy = ($selected_fy_index > 0 && isset($fy_rows[$selected_fy_index - 1])) ? $fy_rows[$selected_fy_index - 1]->genc_detail_code : '';
        $proposal_fy_where = $selected_fy != '' ? CsrConstants::AND_P_FIN_YEAR . $selected_fy . "'" : "";
        $previous_proposal_fy_where = $previous_fy != '' ? CsrConstants::AND_P_FIN_YEAR . $previous_fy . "'" : CsrConstants::AND_ONE_EQ_ZERO;
        $selected_inv_fy_where = $selected_fy != '' ? CsrConstants::LIT_CASE_INV_FIN_YEAR . $selected_fy . "'" : "";
        $previous_inv_fy_where = $previous_fy != '' ? CsrConstants::LIT_CASE_INV_FIN_YEAR . $previous_fy . "'" : CsrConstants::AND_ONE_EQ_ZERO;

        $projects = $this->loadProjectMonitoringRows($company_id, $proposal_fy_where, $scope['proposal_where'], $selected_inv_fy_where, $scope['po_project_scope']);

        $selected_obligation = $callConfig->getRow("SELECT IFNULL(SUM(obligation_amount), 0) AS obligation FROM csr_complaince WHERE company_id = '" . $company_id . "' AND is_active = 0" . ($selected_fy != '' ? " AND financial_year = '" . $selected_fy . "'" : ""))->obligation ?? 0;
        $summary_query = "SELECT (SELECT COUNT(*) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . "' AND p.is_active = 0" . $proposal_fy_where . $scope['proposal_where'] . ") AS total_project, (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_STATUS_SPACE . ST_APPROVED . $proposal_fy_where . $scope['proposal_where'] . ") AS selected_approval_amount, (SELECT IFNULL(SUM(p.total_budget_grand_total), 0) FROM csr_proposal_data p WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_STATUS_SPACE . ST_APPROVED . $previous_proposal_fy_where . $scope['proposal_where'] . ") AS previous_approval_amount, (SELECT IFNULL(SUM(inv.total_spent), 0) FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $selected_inv_fy_where . $scope['po_project_scope'] . ") AS selected_spent_amount, (SELECT IFNULL(SUM(inv.total_spent), 0) FROM csr_invoice_details inv WHERE inv.company_id = '" . $company_id . "' AND inv.is_active = 0" . $previous_inv_fy_where . $scope['po_project_scope'] . ") AS previous_spent_amount";
        $summary_row = $callConfig->getRow($summary_query);

        $impact_fy_where = $selected_fy != '' ? " AND (CASE WHEN cis.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(cis.financial_year, 4), '-', RIGHT(cis.financial_year, 2)) ELSE cis.financial_year END) = '" . $selected_fy . "'" : "";
        $impact_query = "SELECT IFNULL(SUM(cis.student_supported), 0) AS student_supported, IFNULL(SUM(cis.villages_covered), 0) AS villages_covered, IFNULL(SUM(cis.health_camps_conducted), 0) AS health_camps_conducted, IFNULL(SUM(cis.trees_planted), 0) AS trees_planted, IFNULL(SUM(cis.women_benefited), 0) AS women_benefited FROM csr_impact_snapshot cis WHERE cis.company_id = '" . $company_id . "' AND cis.is_active = 0" . $impact_fy_where . " AND cis.project_id IN (" . $scope['project_id_subquery'] . ") GROUP BY (CASE WHEN cis.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(cis.financial_year, 4), '-', RIGHT(cis.financial_year, 2)) ELSE cis.financial_year END)";
        $impact_row = $callConfig->getRow($impact_query);

        $insights = array();
        $return['summary'] = $this->buildProjectMonitoringSummary($summary_row, $selected_obligation, $projects, $selected_fy, $insights);
        $return['financial_years'] = $fy_rows;
        $return['selected_financial_year'] = $selected_fy;
        $return['previous_financial_year'] = $previous_fy;
        $return['projects'] = $projects;
        $return['impact'] = array(
            'student_supported' => isset($impact_row->student_supported) ? (int) $impact_row->student_supported : 0,
            'villages_covered' => isset($impact_row->villages_covered) ? (int) $impact_row->villages_covered : 0,
            'health_camps_conducted' => isset($impact_row->health_camps_conducted) ? (int) $impact_row->health_camps_conducted : 0,
            'trees_planted' => isset($impact_row->trees_planted) ? (int) $impact_row->trees_planted : 0,
            'women_benefited' => isset($impact_row->women_benefited) ? (int) $impact_row->women_benefited : 0
        );
        $return['insights'] = $insights;

        $query = "SELECT p.financial_year, p.subject, p.total_budget_grand_total, c.financial_year AS c_financial_year, SUM(c.total_spent) AS total_spent FROM csr_invoice_details c INNER JOIN csr_proposal_data p ON c.project_id = p.id WHERE p.financial_year = '" . ($get['financial_year'] ?? '') . "' AND c.financial_year = '" . ($get['financial_year'] ?? '') . "' GROUP BY c.project_id, p.financial_year, p.subject, p.total_budget_grand_total, c.financial_year";
        $return['out_rows'] = $callConfig->getAllRows($query);

        return $return;
    }
    private function groupProjectsByFinancialYear(array $project_rows, array $financial_years, string &$selected_fy, string $requested_fy): array
    {
        $projects_by_year = array();
        $projects = array();

        foreach ($project_rows as $project_row) {
            $project_year = trim((string) $project_row->financial_year);
            $project_year_keys = array($project_year);

            if (preg_match('/^(\d{4})-(\d{4})$/', $project_year, $matches)) {
                $project_year_keys[] = $matches[1] . '-' . substr($matches[2], -2);
            } elseif (preg_match('/^(\d{4})-(\d{2})$/', $project_year, $matches)) {
                $project_year_keys[] = $matches[1] . '-20' . $matches[2];
            }

            foreach (array_unique($project_year_keys) as $project_year_key) {
                if ($project_year_key == '') {
                    continue;
                }
                $projects_by_year[$project_year_key][] = $project_row;
            }

            if ($selected_fy == '' || $project_year == $selected_fy) {
                $projects[] = $project_row;
            }
        }

        if ($requested_fy == '' && !isset($projects_by_year[$selected_fy])) {
            for ($fy_index = count($financial_years) - 1; $fy_index >= 0; $fy_index--) {
                $fy_row = $financial_years[$fy_index];
                $fy_code = is_array($fy_row) ? $fy_row['genc_detail_code'] : $fy_row->genc_detail_code;
                if (isset($projects_by_year[$fy_code])) {
                    $selected_fy = $fy_code;
                    break;
                }
            }
        }

        $matched_projects = ($selected_fy != '' && isset($projects_by_year[$selected_fy])) ? $projects_by_year[$selected_fy] : $projects;
        return array('projects' => $matched_projects, 'projects_by_year' => $projects_by_year);
    }

    public function getCsrImpactSnapshotData($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $rid = isset($get['rid']) ? (int) $get['rid'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $requested_fy = $selected_fy;
        $return = array();

        $fy_query = CsrConstants::LIT_SEL_GENC_DETAILS . $company_id . CsrConstants::LIT_GENC_CSR_FIN_YEAR;
        $financial_years = (array) ($callConfig->getAllRows($fy_query) ?: array());

        if (count($financial_years) == 0) {
            $current_fy_start = (int) date('n') >= 4 ? (int) date('Y') : ((int) date('Y') - 1);
            for ($year = $current_fy_start - 5; $year <= $current_fy_start; $year++) {
                $financial_years[] = array(
                    'id' => $year,
                    'genc_detail_code' => $year . '-' . substr((string) ($year + 1), -2),
                    'genc_detail_name' => $year . '-' . substr((string) ($year + 1), -2)
                );
            }
        }

        if ($selected_fy == '' && count($financial_years) > 0) {
            $last_fy = $financial_years[count($financial_years) - 1];
            $selected_fy = is_array($last_fy) ? $last_fy['genc_detail_code'] : $last_fy->genc_detail_code;
        }

        $project_query = "SELECT id, subject, financial_year, project_start_date, project_end_date, total_budget_grand_total FROM csr_proposal_data WHERE company_id = '" . $company_id . "' AND is_active = 0 AND current_status = " . ST_APPROVED . " ORDER BY subject ASC";
        $project_rows = (array) ($callConfig->getAllRows($project_query) ?: array());
        $grouped = $this->groupProjectsByFinancialYear($project_rows, $financial_years, $selected_fy, $requested_fy);

        $record_query = "SELECT cis.*, p.subject AS project_name FROM csr_impact_snapshot cis LEFT JOIN csr_proposal_data p ON p.id = cis.project_id AND p.company_id = cis.company_id WHERE cis.company_id = '" . $company_id . "' AND cis.is_active = 0 ORDER BY cis.id DESC";
        $record_rows = (array) ($callConfig->getAllRows($record_query) ?: array());
        $impact_snapshots = array();
        foreach ($record_rows as $record_row) {
            $impact_snapshots[$record_row->id] = $record_row;
        }

        $impact_snapshot = array();
        if ($rid > 0) {
            $single_query = "SELECT cis.*, p.subject AS project_name FROM csr_impact_snapshot cis LEFT JOIN csr_proposal_data p ON p.id = cis.project_id AND p.company_id = cis.company_id WHERE cis.company_id = '" . $company_id . "' AND cis.id = " . $rid . " AND cis.is_active = 0";
            $single_row = $callConfig->getRow($single_query);
            if (!empty($single_row)) {
                $impact_snapshot = $single_row;
                $selected_fy = $single_row->financial_year;
            }
        }

        $return['financial_years'] = $financial_years;
        $return['selected_financial_year'] = $selected_fy;
        $return['projects'] = $grouped['projects'];
        $return['projects_by_year'] = $grouped['projects_by_year'];
        $return['impact_snapshots'] = $impact_snapshots;
        $return['impact_snapshot'] = $impact_snapshot;

        return $return;
    }
    public function getCsrDashboardPopupProjects($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $selected_fy = isset($get['financial_year']) ? $callConfig->real_escape_string($get['financial_year']) : '';
        $type = isset($get['type']) ? $callConfig->real_escape_string($get['type']) : '';
        $return = array('projects' => array());

        if ($company_id == 0 || $type == '') {
            return $return;
        }

        $scope = $this->getCsrDashboardProposalScope($get);
        $proposal_scope = $scope['proposal_where'];
        $po_project_scope = $scope['po_project_scope'];
        $proposal_fy_where = $selected_fy != '' ? CsrConstants::AND_P_FIN_YEAR . $selected_fy . "'" : "";
        $po_fy_where = $selected_fy != '' ? " AND (CASE WHEN po.financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(po.financial_year, 4), '-', RIGHT(po.financial_year, 2)) ELSE po.financial_year END) = '" . $selected_fy . "'" : "";
        $inv_fy_where = $selected_fy != '' ? CsrConstants::LIT_CASE_INV_FIN_YEAR . $selected_fy . "'" : "";

        $where = "";
        if ($type == 'project_approved') {
            $where = " AND p.current_status = " . ST_APPROVED;
        } elseif ($type == 'approved_in_progress') {
            $where = " AND p.current_status > 100 AND p.current_status < " . ST_APPROVED;
        } elseif ($type == 'active_project') {
            $where = " AND p.current_status != 0";
        } elseif ($type == 'spent') {
            $where = " AND IFNULL(inv.spent_amount, 0) > 0";
        } elseif ($type == 'pending_obligation') {
            $where = " AND p.current_status = " . ST_APPROVED . " AND (IFNULL(po.approved_amount, 0) - IFNULL(inv.spent_amount, 0)) > 0";
        } else {
            $where = CsrConstants::AND_ONE_EQ_ZERO;
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
                WHERE po.company_id = '" . $company_id . "'
                AND po.is_active = 0" . $po_fy_where . $po_project_scope . "
                GROUP BY po.project_id
            ) po ON po.project_id = p.id
            LEFT JOIN (
                SELECT inv.project_id, SUM(IFNULL(inv.total_spent, 0)) AS spent_amount
                FROM csr_invoice_details inv
                WHERE inv.company_id = '" . $company_id . CsrConstants::LIT_INV_IS_ACTIVE_0_NL . $inv_fy_where . $po_project_scope . "
                GROUP BY inv.project_id
            ) inv ON inv.project_id = p.id
            WHERE p.company_id = '" . $company_id . CsrConstants::LIT_P_IS_ACTIVE_0_NL . $proposal_fy_where . $proposal_scope . $where . CsrConstants::LIT_ORDER_P_ID_DESC;

        $return['projects'] = $callConfig->getAllRows($query);
        $return['query'] = $query;
        return $return;
    }

    public function getCsrApprovedProjectsForDocuments($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;

        $query = "SELECT id, subject, institue_name, financial_year
            FROM csr_proposal_data
            WHERE company_id = '" . $company_id . "'
            AND is_active = 0
            AND current_status = " . ST_APPROVED . "
            ORDER BY id DESC";
        $rows = $callConfig->getAllRows($query);

        $return = array();
        $return['approved_projects'] = $rows;
        return $return;
    }

    public function getCsrProjectDocuments($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int) $get['proposal_id'] : 0;

        $query = "SELECT id, company_id, proposal_id, document_name, file_name, actual_file_name, access_type,
                is_active, current_status, status_date, created_date, created_by
            FROM csr_project_documents
            WHERE company_id = '" . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        if ($proposal_id > 0) {
            $query .= " AND proposal_id = '" . $proposal_id . "'";
        }
        $query .= " ORDER BY id DESC";
        $rows = $callConfig->getAllRows($query);

        $return = array();
        $return['project_documents'] = $rows;
        return $return;
    }

    public function insertCsrProjectDocument($get, $post = array())
    {
        global $callConfig;

        if (count((array) $post) == 0) {
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
            'status_date' => $post['status_date'] ?? date(CsrConstants::DATE_FORMAT_YMD_HIS),
            'created_date' => $post['created_date'] ?? date(CsrConstants::DATE_FORMAT_YMD_HIS),
            'created_by' => $created_by != '' ? $created_by : null
        );

        return $callConfig->insertRecord('csr_project_documents', $insertData);
    }

    public function updateCsrProjectDocument($get, $post = array())
    {
        global $callConfig;

        if (count((array) $post) == 0) {
            $post = $_POST;
        }
        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : (int) ($post['company_id'] ?? 0);
        $document_id = isset($post['id']) ? (int) $post['id'] : 0;

        if ($document_id <= 0 || $company_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid document details');
        }

        $checkQuery = "SELECT id FROM csr_project_documents
            WHERE id = '" . $document_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
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
        $updateData['updated_date'] = date(CsrConstants::DATE_FORMAT_YMD_HIS);
        return $callConfig->updateRecord('csr_project_documents', $updateData, 'id', $document_id);
    }
    public function getCsrProjectTrackingManage($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int) $get['proposal_id'] : 0;
        $approved_status = defined('ST_APPROVED') ? (int) ST_APPROVED : 199;

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
            WHERE id = '" . $proposal_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $proposal = $callConfig->getRow($proposalQuery);
        if (empty($proposal)) {
            $return['message'] = CsrConstants::PROPOSAL_NOT_FOUND;
            return $return;
        }

        $trackingQuery = "SELECT id, proposal_id, tracking_name, description, start_date, end_date,
                tracking_budget, status, current_status, is_active
            FROM csr_project_tracking
            WHERE proposal_id = '" . $proposal_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . "'
            AND is_active = 0
            ORDER BY id ASC";
        $tracking_rows = $callConfig->getAllRows($trackingQuery);

        $return['status'] = 'success';
        $return['proposal'] = $proposal;
        $return['tracking_rows'] = $tracking_rows;
        return $return;
    }

    private function processProjectTrackingRow($index, $tracking_name, $post, $proposal_id, $company_id, $user_id, $proposal_created_by, $approved_status, $now, int &$inserted, int &$updated, int &$skipped): void
    {
        global $callConfig;
        $tracking_name = trim((string) $tracking_name);
        if ($tracking_name == '') {
            return;
        }

        $tracking_ids = $post['tracking_id'] ?? array();
        $row_id = isset($tracking_ids[$index]) ? (int) $tracking_ids[$index] : 0;
        $description = trim((string) ($post['description'][$index] ?? ''));
        $start_date = trim((string) ($post['start_date'][$index] ?? ''));
        $end_date = trim((string) ($post['end_date'][$index] ?? ''));
        $tracking_budget = trim((string) ($post['tracking_budget'][$index] ?? ''));

        if ($row_id > 0) {
            $checkQuery = "SELECT id, current_status FROM csr_project_tracking WHERE id = '" . $row_id . "' AND proposal_id = '" . $proposal_id . "' AND company_id = '" . $company_id . "' AND is_active = 0";
            $existing = $callConfig->getRow($checkQuery);
            if (empty($existing) || (int) $existing->current_status == $approved_status) {
                $skipped++;
                return;
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
                'created_by' => $user_id != '' ? $user_id : $proposal_created_by
            );
            $callConfig->insertRecord('csr_project_tracking', $insertData);
            $inserted++;
        }
    }

    public function saveCsrProjectTracking($get, $post = array())
    {
        global $callConfig;
        if (count((array) $post) == 0) {
            $post = $_POST;
        }

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : (int) ($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $proposal_id = isset($post['proposal_id']) ? (int) $post['proposal_id'] : 0;
        $approved_status = defined('ST_APPROVED') ? (int) ST_APPROVED : 199;
        $now = date(CsrConstants::DATE_FORMAT_YMD_HIS);

        if ($company_id <= 0 || $proposal_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid proposal details');
        }

        $proposalQuery = "SELECT id, created_by FROM csr_proposal_data WHERE id = '" . $proposal_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $proposal = $callConfig->getRow($proposalQuery);
        if (empty($proposal)) {
            return array('status' => 'error', 'message' => CsrConstants::PROPOSAL_NOT_FOUND);
        }

        $tracking_names = $post['tracking_name'] ?? array();
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($tracking_names as $index => $tracking_name) {
            $this->processProjectTrackingRow($index, $tracking_name, $post, $proposal_id, $company_id, $user_id, $proposal->created_by ?? null, $approved_status, $now, $inserted, $updated, $skipped);
        }

        return array(
            'status' => 'success',
            'message' => 'Tracking details saved',
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped
        );
    }

    public function getCsrProposalProjectDates($get)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $proposal_id = isset($get['proposal_id']) ? (int) $get['proposal_id'] : 0;

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
            WHERE id = '" . $proposal_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $proposal = $callConfig->getRow($query);
        if (empty($proposal)) {
            $return['message'] = CsrConstants::PROPOSAL_NOT_FOUND;
            return $return;
        }

        $return['status'] = 'success';
        $return['proposal'] = $proposal;
        return $return;
    }

    public function saveCsrProposalProjectDates($get, $post = array())
    {
        global $callConfig;

        if (count((array) $post) == 0) {
            $post = $_POST;
        }

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : (int) ($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $proposal_id = isset($post['proposal_id']) ? (int) $post['proposal_id'] : 0;
        $project_start_date = trim((string) ($post['project_start_date'] ?? ''));
        $project_end_date = trim((string) ($post['project_end_date'] ?? ''));
        $now = date(CsrConstants::DATE_FORMAT_YMD_HIS);

        if ($company_id <= 0 || $proposal_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid proposal details');
        }

        $checkQuery = "SELECT id FROM csr_proposal_data
            WHERE id = '" . $proposal_id . CsrConstants::LIT_AND_COMPANY_ID_NL . $company_id . CsrConstants::LIT_IS_ACTIVE_0_NL;
        $proposal = $callConfig->getRow($checkQuery);
        if (empty($proposal)) {
            return array('status' => 'error', 'message' => CsrConstants::PROPOSAL_NOT_FOUND);
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
    private function buildDailyOpsSparklines($company_id, $today, $user_wh, array &$return): void
    {
        global $callConfig;
        $spark_q = "SELECT DATE(map_date) AS visit_day, COUNT(id) AS visit_count FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date >= '" . date('Y-m-d', strtotime('-6 day')) . CsrConstants::AND_MAP_DATE_LTE . $today . "' AND is_active = 0" . $user_wh . " GROUP BY DATE(map_date) ORDER BY visit_day ASC";
        $spark_rows = (array) ($callConfig->getAllRows($spark_q) ?: array());
        $employee_spark = array();
        foreach ($spark_rows as $spark_row) {
            $employee_spark[] = (int) $spark_row->visit_count;
        }
        while (count($employee_spark) < 7) {
            $employee_spark[] = 0;
        }
        $return['employee_sparkline'] = array_slice($employee_spark, -7);

        $extra_spark_q = "SELECT DATE(map_date) AS visit_day, COUNT(id) AS visit_count FROM field_work_user_map_points WHERE company_id = " . $company_id . " AND map_date >= '" . date('Y-m-d', strtotime('-6 day')) . CsrConstants::AND_MAP_DATE_LTE . $today . "' AND (field_work_daily_plan_id = '' OR field_work_daily_plan_id IS NULL) AND is_active = 0" . $user_wh . " GROUP BY DATE(map_date) ORDER BY visit_day ASC";
        $extra_spark_rows = (array) ($callConfig->getAllRows($extra_spark_q) ?: array());
        $extra_spark = array();
        foreach ($extra_spark_rows as $extra_spark_row) {
            $extra_spark[] = (int) $extra_spark_row->visit_count;
        }
        while (count($extra_spark) < 7) {
            $extra_spark[] = 0;
        }
        $return['extra_sparkline'] = array_slice($extra_spark, -7);
    }

    private function loadDailyOpsKpis($company_id, $today, $user_wh, $user_wh_au, array &$return): void
    {
        global $callConfig;
        $emp_query = "SELECT COUNT(id) AS cc_emp FROM application_users WHERE company_id = " . $company_id . " AND is_active = 0 AND user_field_work_type IN ('1','2')" . str_replace('user_id', 'id', $user_wh);
        $emp_row = $callConfig->getRow($emp_query);
        $return['employees_count'] = (int) ($emp_row->cc_emp ?? 0);

        $this_month_start = date('Y-m-01');
        $this_month_end = date('Y-m-t');
        $last_month_start = date('Y-m-01', strtotime('first day of last month'));
        $last_month_end = date('Y-m-t', strtotime('last day of last month'));

        $active_this_month = (int) ($callConfig->getRow(CsrConstants::SEL_CC_ACTIVE . $company_id . " AND map_date >= '" . $this_month_start . CsrConstants::AND_MAP_DATE_LTE . $this_month_end . CsrConstants::AND_IS_ACTIVE_0_STANDARD . $user_wh)->cc_active ?? 0);
        $active_last_month = (int) ($callConfig->getRow(CsrConstants::SEL_CC_ACTIVE . $company_id . " AND map_date >= '" . $last_month_start . CsrConstants::AND_MAP_DATE_LTE . $last_month_end . CsrConstants::AND_IS_ACTIVE_0_STANDARD . $user_wh)->cc_active ?? 0);
        $return['employees_growth_pct'] = $active_last_month > 0 ? round((($active_this_month - $active_last_month) / $active_last_month) * 100, 1) : ($active_this_month > 0 ? 100 : 0);

        $planned_today_q = "SELECT COUNT(id) AS cc_plan FROM field_work_daily_plans WHERE company_id = " . $company_id . " AND plan_date = '" . $today . "' AND current_status NOT IN (25,28) AND is_active = 0" . $user_wh;
        $return['planned_visits_today'] = (int) ($callConfig->getRow($planned_today_q)->cc_plan ?? 0);

        $map_today_q = "SELECT COUNT(id) AS cc_map, SUM(IF(field_work_daily_plan_id != '' AND field_work_daily_plan_id IS NOT NULL, 1, 0)) AS cc_linked FROM field_work_user_map_points WHERE company_id = " . $company_id . CsrConstants::AND_MAP_DATE_EQ . $today . CsrConstants::AND_IS_ACTIVE_0_STANDARD . $user_wh;
        $map_today_row = $callConfig->getRow($map_today_q);
        $return['completed_today'] = (int) ($map_today_row->cc_linked ?? 0);
        $return['extra_visits_today'] = max((int) ($map_today_row->cc_map ?? 0) - $return['completed_today'], 0);
        $return['remaining_today'] = max($return['planned_visits_today'] - $return['completed_today'], 0);
        $return['completion_rate_pct'] = $return['planned_visits_today'] > 0 ? round(($return['completed_today'] / $return['planned_visits_today']) * 100) : 0;
    }

    public function togetdailyoperationsdashboard($get)
    {
        $today = date('Y-m-d');
        $company_id = (int) $get['company_id'];
        $user_id = (int) $get['user_id'];
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
        $this->loadDailyOpsKpis($company_id, $today, $user_wh, $user_wh_au, $return);
        $this->buildDailyOpsSparklines($company_id, $today, $user_wh, $return);

        $return['today_date_label'] = date('d M Y');
        $return['greeting'] = $this->getDailyOpsGreeting();
        $return['map_data'] = $this->getDailyOpsMapPoints($today, $company_id, $admin_type, $user_wh, $user_wh_alias);

        return $return;
    }

    private function loadDailyOpsRawPoints($today, $company_id, $user_wh, $user_wh_alias): array
    {
        global $callConfig;
        $plan_q = "SELECT fwdp.id, fwdp.user_id, fwdp.plan_location AS location, fwdp.plan_latitude AS lat, fwdp.plan_longitude AS lng, fwdp.current_status, au.display_name AS employee_name FROM field_work_daily_plans fwdp JOIN application_users au ON au.id = fwdp.user_id WHERE fwdp.company_id = " . $company_id . " AND fwdp.plan_date = '" . $today . "' AND fwdp.current_status NOT IN (25,28) AND fwdp.is_active = 0" . $user_wh_alias;
        $plan_rows = (array) ($callConfig->getAllRows($plan_q) ?: array());

        $map_q = "SELECT mp.id, mp.user_id, mp.map_location AS location, mp.map_latitude AS lat, mp.map_longitude AS lng, mp.field_work_daily_plan_id, mp.map_date, au.display_name AS employee_name FROM field_work_user_map_points mp JOIN application_users au ON au.id = mp.user_id WHERE mp.company_id = " . $company_id . CsrConstants::AND_MAP_DATE_EQ . $today . CsrConstants::AND_IS_ACTIVE_0_STANDARD . $user_wh . " ORDER BY mp.id ASC";
        $map_rows = (array) ($callConfig->getAllRows($map_q) ?: array());

        return array('plan_rows' => $plan_rows, 'map_rows' => $map_rows);
    }

    private function formatDailyOpsModes(array $planned_points, array $visited_points, array $extra_points): array
    {
        $all_points = array_merge($visited_points, $extra_points, $planned_points);
        return array(
            'all' => array('points' => $all_points, 'count' => count($all_points)),
            'visited' => array('points' => $visited_points, 'count' => count($visited_points)),
            'planned' => array('points' => $planned_points, 'count' => count($planned_points)),
            'extra' => array('points' => $extra_points, 'count' => count($extra_points))
        );
    }

    public function getDailyOpsMapPoints($today, $company_id, $admin_type, $user_wh, $user_wh_alias)
    {
        $raw = $this->loadDailyOpsRawPoints($today, $company_id, $user_wh, $user_wh_alias);
        $planned_points = array();
        $visited_points = array();
        $extra_points = array();

        foreach ($raw['plan_rows'] as $row) {
            if ($row->lat && $row->lng) {
                $planned_points[] = array(
                    'id' => (int) $row->id,
                    'user_id' => (int) $row->user_id,
                    'title' => $row->employee_name . ' - ' . $row->location,
                    'lat' => (float) $row->lat,
                    'lng' => (float) $row->lng,
                    'type' => 'planned'
                );
            }
        }

        foreach ($raw['map_rows'] as $row) {
            if ($row->lat && $row->lng) {
                $isLinked = !empty($row->field_work_daily_plan_id);
                $point = array(
                    'id' => (int) $row->id,
                    'user_id' => (int) $row->user_id,
                    'title' => $row->employee_name . ' - ' . $row->location,
                    'lat' => (float) $row->lat,
                    'lng' => (float) $row->lng,
                    'type' => $isLinked ? 'visited' : 'extra'
                );
                if ($isLinked) {
                    $visited_points[] = $point;
                } else {
                    $extra_points[] = $point;
                }
            }
        }

        $modes = $this->formatDailyOpsModes($planned_points, $visited_points, $extra_points);
        $default_mode = count($visited_points) > 0 ? 'visited' : (count($planned_points) > 0 ? 'planned' : 'all');

        return array(
            'default_mode' => $default_mode,
            'modes' => $modes
        );
    }

    public function getDailyOpsGreeting()
    {
        $hour = (int) date('G');
        if ($hour < 12) {
            return 'Good Morning';
        }
        if ($hour < 17) {
            return 'Good Afternoon';
        }
        return 'Good Evening';
    }

    private function loadCsrObligationLookups($company_id, array &$return): void
    {
        global $callConfig;
        $locations = array();
        foreach ((array) ($callConfig->getAllRows("SELECT id, location_name FROM locations WHERE company_id = '" . $company_id . "' AND is_active=0") ?: array()) as $row) {
            $locations[$row->id] = $row;
        }
        $return['locations'] = $locations;
        $return['entity_names'] = $callConfig->getAllRows("SELECT * FROM csr_entity_name WHERE company_id = '" . $company_id . CsrConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE);
        $return['financial_years'] = $callConfig->getAllRows("SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '{$company_id}' AND genc_code = 'CSR_FINANCIAL_YEAR' AND is_active=0 ORDER BY id ASC");
    }

    private function resolveCsrObligationFYRange($fy_rows, string &$year_from, string &$year_to): array
    {
        $all_fy = array();
        foreach ($fy_rows as $row) {
            $all_fy[] = $row->genc_detail_code;
        }
        if (empty($year_from) && count($all_fy) > 0) {
            $year_from = $all_fy[max(0, count($all_fy) - 5)];
        }
        if (empty($year_to) && count($all_fy) > 0) {
            $year_to = $all_fy[count($all_fy) - 1];
        }

        $fy_range = array();
        $in_range = false;
        foreach ($all_fy as $fy) {
            if ($fy == $year_from) {
                $in_range = true;
            }
            if ($in_range) {
                $fy_range[] = $fy;
            }
            if ($fy == $year_to) {
                break;
            }
        }
        return count($fy_range) > 0 ? $fy_range : $all_fy;
    }

    private function loadCsrObligationYearlyData($company_id, array $fy_range, $entity_code, array &$yearly_rows, float &$total_obligation, float &$total_spent): void
    {
        global $callConfig;
        $total_obligation = 0;
        $total_spent = 0;
        $yearly_rows = array();

        foreach ($fy_range as $fy) {
            $entity_wh = $entity_code != '' ? " AND entity_code = '" . $entity_code . "'" : "";
            $ob_q = "SELECT IFNULL(SUM(obligation_amount), 0) AS ob_amt FROM csr_complaince WHERE company_id = '" . $company_id . "' AND financial_year = '" . $fy . "' AND is_active = 0" . $entity_wh;
            $ob_amt = (float) ($callConfig->getRow($ob_q)->ob_amt ?? 0);

            $sp_q = "SELECT IFNULL(SUM(total_spent), 0) AS sp_amt FROM csr_invoice_details WHERE company_id = '" . $company_id . "' AND is_active = 0 AND (CASE WHEN financial_year REGEXP '^[0-9]{4}-[0-9]{4}$' THEN CONCAT(LEFT(financial_year, 4), '-', RIGHT(financial_year, 2)) ELSE financial_year END) = '" . $fy . "'";
            $sp_amt = (float) ($callConfig->getRow($sp_q)->sp_amt ?? 0);

            $unspent = max($ob_amt - $sp_amt, 0);
            $util_pct = $ob_amt > 0 ? round(($sp_amt / $ob_amt) * 100, 1) : 0;

            $yearly_rows[] = array(
                'fy' => $fy,
                'obligation' => $ob_amt,
                'spent' => $sp_amt,
                'unspent' => $unspent,
                'utilization_pct' => $util_pct
            );
            $total_obligation += $ob_amt;
            $total_spent += $sp_amt;
        }
    }

    private function loadCsrObligationProjectAndLocationGroups($company_id, $location_id, $entity_code, array $fy_range, $proposal_scope, array &$project_groups, array &$location_rows): void
    {
        global $callConfig;
        $fy_in = "'" . implode("','", $fy_range) . "'";
        $loc_wh = $location_id != '' ? " AND p.csr_location = '" . $location_id . "'" : "";
        $entity_wh = $entity_code != '' ? " AND p.entity_name = '" . $entity_code . "'" : "";

        $proj_q = "SELECT p.id, p.subject, p.financial_year, p.total_budget_grand_total, p.current_status, p.tracking_status, p.csr_location, loc.location_name, cas.csr_area FROM csr_proposal_data p LEFT JOIN locations loc ON loc.id = p.csr_location AND loc.company_id = p.company_id AND loc.is_active = 0 LEFT JOIN csr_areas cas ON cas.id = p.working_area AND cas.company_id = p.company_id AND cas.is_active = 0 WHERE p.company_id = '" . $company_id . "' AND p.is_active = 0 AND p.current_status = " . ST_APPROVED . " AND p.financial_year IN (" . $fy_in . ")" . $loc_wh . $entity_wh . $proposal_scope . " ORDER BY p.id DESC";

        $proj_rows = (array) ($callConfig->getAllRows($proj_q) ?: array());
        $project_groups = array();
        $loc_accum = array();

        foreach ($proj_rows as $p) {
            $area = !empty($p->csr_area) ? $p->csr_area : 'Other';
            $project_groups[$area][] = $p;

            $loc_name = !empty($p->location_name) ? $p->location_name : 'Unknown';
            if (!isset($loc_accum[$loc_name])) {
                $loc_accum[$loc_name] = array('location' => $loc_name, 'amount' => 0, 'statuses' => array());
            }
            $loc_accum[$loc_name]['amount'] += (float) $p->total_budget_grand_total;
            $loc_accum[$loc_name]['statuses'][] = ((int) $p->tracking_status == 1) ? 'Completed' : 'In-Process';
        }

        $location_rows = array();
        foreach ($loc_accum as $loc_row) {
            $has_completed = in_array('Completed', $loc_row['statuses'], true);
            $has_process = in_array('In-Process', $loc_row['statuses'], true);
            $status = ($has_completed && !$has_process) ? 'Completed' : 'In-Process';
            $location_rows[] = array(
                'location' => $loc_row['location'],
                'amount' => $loc_row['amount'],
                'status' => $status
            );
        }
    }

    public function getCsrObligationDashboardData($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $year_from = isset($get['year_from']) ? $callConfig->real_escape_string($get['year_from']) : '';
        $year_to = isset($get['year_to']) ? $callConfig->real_escape_string($get['year_to']) : '';
        $location_id = isset($get['location_id']) ? $callConfig->real_escape_string($get['location_id']) : '';
        $entity_code = isset($get['entity_code']) ? $callConfig->real_escape_string($get['entity_code']) : '';
        $return = array();
        $scope = $this->getCsrDashboardProposalScope($get);

        $this->loadCsrObligationLookups($company_id, $return);
        $fy_range = $this->resolveCsrObligationFYRange($return['financial_years'], $year_from, $year_to);

        $yearly_rows = array();
        $total_obligation = 0.0;
        $total_spent = 0.0;
        $this->loadCsrObligationYearlyData($company_id, $fy_range, $entity_code, $yearly_rows, $total_obligation, $total_spent);

        $project_groups = array();
        $location_rows = array();
        $this->loadCsrObligationProjectAndLocationGroups($company_id, $location_id, $entity_code, $fy_range, $scope['proposal_where'], $project_groups, $location_rows);

        $final_unspent = max($total_obligation - $total_spent, 0);
        $return['selected'] = array('year_from' => $year_from, 'year_to' => $year_to, 'location_id' => $location_id, 'entity_code' => $entity_code);
        $return['fy_range'] = $fy_range;
        $return['summary'] = array(
            'total_obligation' => $total_obligation,
            'total_spent' => $total_spent,
            'overall_utilization' => $total_obligation > 0 ? round(($total_spent / $total_obligation) * 100, 1) : 0,
            'final_unspent' => $final_unspent,
            'total_unspent_pct' => $total_obligation > 0 ? round(($final_unspent / $total_obligation) * 100, 1) : 0
        );
        $return['yearly_rows'] = $yearly_rows;
        $return['project_groups'] = $project_groups;
        $return['location_rows'] = $location_rows;

        return $return;
    }
    private function loadFieldWorkScopedUsers($company_id, $user_id, $admin_type, array &$scoped_ids): array
    {
        global $callConfig;
        $whr = "";
        if ($admin_type == '2') {
            $whr = "reports_to = '" . $user_id . CsrConstants::APOS_AND_SPACE;
        } elseif ($admin_type == '3') {
            $whr = CsrConstants::ID_EQ_APOS . $user_id . CsrConstants::APOS_AND_SPACE;
        }

        $users_q = "SELECT id, display_name, user_field_work_type FROM application_users WHERE company_id = '" . $company_id . CsrConstants::APOS_AND_SPACE . $whr . " is_active = 0 AND user_field_work_type IN ('1','2') ORDER BY display_name ASC";
        $users_rows = (array) ($callConfig->getAllRows($users_q) ?: array());
        $scoped_users = array();
        $scoped_ids = array();
        foreach ($users_rows as $urow) {
            $scoped_users[] = array('id' => $urow->id, 'display_name' => $urow->display_name, 'user_field_work_type' => $urow->user_field_work_type);
            $scoped_ids[] = (int) $urow->id;
        }
        return $scoped_users;
    }

    private function buildFieldWorkOperationsRows($company_id, array $scoped_ids, $from_date, $to_date, $status_string): array
    {
        global $callConfig;
        if (empty($scoped_ids)) {
            return array('rows' => array(), 'planned' => 0, 'visited' => 0, 'pending' => 0, 'extra' => 0);
        }

        $ids_in = implode(',', $scoped_ids);
        $plan_q = "SELECT fwdp.id, fwdp.user_id, fwdp.plan_date, fwdp.plan_location, fwdp.current_status, au.display_name AS employee_name FROM field_work_daily_plans fwdp JOIN application_users au ON au.id = fwdp.user_id WHERE fwdp.company_id = " . $company_id . " AND fwdp.user_id IN (" . $ids_in . ") AND fwdp.plan_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "' AND fwdp.current_status NOT IN (25,28) AND fwdp.is_active = 0 ORDER BY fwdp.plan_date ASC";
        $plan_rows = (array) ($callConfig->getAllRows($plan_q) ?: array());

        $map_q = "SELECT mp.id, mp.user_id, mp.map_date, mp.map_location, mp.field_work_daily_plan_id, au.display_name AS employee_name FROM field_work_user_map_points mp JOIN application_users au ON au.id = mp.user_id WHERE mp.company_id = " . $company_id . " AND mp.user_id IN (" . $ids_in . ") AND mp.map_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . CsrConstants::AND_IS_ACTIVE_0_STANDARD;
        $map_rows = (array) ($callConfig->getAllRows($map_q) ?: array());

        $visited_plan_ids = array();
        $extra_count = 0;
        foreach ($map_rows as $m) {
            if (!empty($m->field_work_daily_plan_id)) {
                $visited_plan_ids[(int) $m->field_work_daily_plan_id] = true;
            } else {
                $extra_count++;
            }
        }

        $planned_count = count($plan_rows);
        $visited_count = count($visited_plan_ids);
        $pending_count = max($planned_count - $visited_count, 0);

        $rows = array();
        foreach ($plan_rows as $p) {
            $is_visited = isset($visited_plan_ids[(int) $p->id]);
            $status = $is_visited ? 'Visited' : 'Pending';
            if ($status_string != '' && $status_string != $status) {
                continue;
            }
            $rows[] = array(
                'id' => (int) $p->id,
                'user_id' => (int) $p->user_id,
                'employee_name' => $p->employee_name,
                'date' => $p->plan_date,
                'location' => $p->plan_location,
                'status' => $status
            );
        }

        return array(
            'rows' => $rows,
            'planned' => $planned_count,
            'visited' => $visited_count,
            'pending' => $pending_count,
            'extra' => $extra_count
        );
    }

    public function getFieldWorkOperationsReport($get)
    {
        $company_id = (int) $get['company_id'];
        $user_id = (int) $get['user_id'];
        $admin_type = isset($get['admin_type']) ? $get['admin_type'] : '1';
        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $status_string = isset($get['status']) ? $get['status'] : '';

        $return = array(
            'users' => array(),
            'summary' => array('planned' => 0, 'visited' => 0, 'pending' => 0, 'extra' => 0),
            'rows' => array()
        );

        $scoped_ids = array();
        $return['users'] = $this->loadFieldWorkScopedUsers($company_id, $user_id, $admin_type, $scoped_ids);

        if ($filters_only == 'Y' || $from_date == '' || $to_date == '') {
            return $return;
        }

        $res = $this->buildFieldWorkOperationsRows($company_id, $scoped_ids, $from_date, $to_date, $status_string);
        $return['rows'] = $res['rows'];
        $return['summary'] = array(
            'planned' => $res['planned'],
            'visited' => $res['visited'],
            'pending' => $res['pending'],
            'extra' => $res['extra']
        );

        return $return;
    }
    private function loadExpenseScopedEmployees($company_id, $user_id, $admin_type, array &$scoped_emp_ids): array
    {
        global $callConfig;
        $emp_whr = "";
        if ($admin_type == '2') {
            $emp_whr = " AND emp.user_id IN (SELECT id FROM application_users WHERE reports_to = '" . $user_id . CsrConstants::AND_COMPANY_ID_EQ_APOS . $company_id . "' AND is_active = 0)";
        } elseif ($admin_type == '3') {
            $emp_whr = " AND emp.user_id = '" . $user_id . "'";
        }

        $employees_q = "SELECT emp.id, emp.display_name, emp.user_id FROM employees emp WHERE emp.company_id = '" . $company_id . "' AND emp.is_active = 0" . $emp_whr . " ORDER BY emp.display_name ASC";
        $employees_rows = (array) ($callConfig->getAllRows($employees_q) ?: array());
        $scoped_employees = array();
        $scoped_emp_ids = array();
        foreach ($employees_rows as $erow) {
            $scoped_employees[] = array('id' => $erow->id, 'display_name' => $erow->display_name, 'user_id' => $erow->user_id);
            $scoped_emp_ids[] = (int) $erow->id;
        }
        return $scoped_employees;
    }

    private function buildEmpExpenseRows($company_id, array $scoped_emp_ids, $from_date, $to_date, $status_string, $expense_type_string): array
    {
        global $callConfig;
        if (empty($scoped_emp_ids)) {
            return array('rows' => array(), 'claims' => 0, 'line_items' => 0, 'total_amount' => 0, 'tax_amount' => 0, 'pending_amount' => 0);
        }

        $emp_ids_in = implode(',', $scoped_emp_ids);
        $claim_q = "SELECT ec.id, ec.employee_id, ec.claim_date, ec.total_amount, ec.tax_amount, ec.current_status, emp.display_name AS employee_name FROM employee_expense_claims ec JOIN employees emp ON emp.id = ec.employee_id WHERE ec.company_id = " . $company_id . " AND ec.employee_id IN (" . $emp_ids_in . ") AND ec.claim_date BETWEEN '" . $from_date . CsrConstants::APOS_AND_APOS . $to_date . "' AND ec.is_active = 0 ORDER BY ec.claim_date ASC";
        $claims = (array) ($callConfig->getAllRows($claim_q) ?: array());

        $summary_total = 0.0;
        $summary_tax = 0.0;
        $summary_pending = 0.0;
        $rows = array();

        foreach ($claims as $c) {
            $amount = (float) $c->total_amount;
            $tax = (float) $c->tax_amount;
            $summary_total += $amount;
            $summary_tax += $tax;
            if ((int) $c->current_status == 0) {
                $summary_pending += $amount;
            }

            $rows[] = array(
                'id' => (int) $c->id,
                'employee_id' => (int) $c->employee_id,
                'employee_name' => $c->employee_name,
                'date' => $c->claim_date,
                'amount' => $amount,
                'tax' => $tax,
                'status' => ((int) $c->current_status == 199) ? 'Approved' : 'Pending'
            );
        }

        return array(
            'rows' => $rows,
            'claims' => count($claims),
            'line_items' => count($rows),
            'total_amount' => $summary_total,
            'tax_amount' => $summary_tax,
            'pending_amount' => $summary_pending
        );
    }

    public function getEmpExpensesReport($get)
    {
        $company_id = (int) $get['company_id'];
        $user_id = (int) $get['user_id'];
        $admin_type = isset($get['admin_type']) ? $get['admin_type'] : '1';
        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $status_string = isset($get['status']) ? $get['status'] : '';
        $expense_type_string = isset($get['expense_type']) ? $get['expense_type'] : '';

        $return = array(
            'employees' => array(),
            'summary' => array('claims' => 0, 'line_items' => 0, 'total_amount' => 0, 'tax_amount' => 0, 'pending_amount' => 0),
            'rows' => array()
        );

        $scoped_emp_ids = array();
        $return['employees'] = $this->loadExpenseScopedEmployees($company_id, $user_id, $admin_type, $scoped_emp_ids);

        if ($filters_only == 'Y' || $from_date == '' || $to_date == '') {
            return $return;
        }

        $res = $this->buildEmpExpenseRows($company_id, $scoped_emp_ids, $from_date, $to_date, $status_string, $expense_type_string);
        $return['rows'] = $res['rows'];
        $return['summary'] = array(
            'claims' => $res['claims'],
            'line_items' => $res['line_items'],
            'total_amount' => $res['total_amount'],
            'tax_amount' => $res['tax_amount'],
            'pending_amount' => $res['pending_amount']
        );

        return $return;
    }
}

class CSRClass
{
    use CsrFieldWorkTrait;
    use CsrSyncAndDataTrait;
    use CsrReportsTrait;
    use CsrDashboardTrait;
}
