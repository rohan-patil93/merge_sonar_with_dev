<?php
date_default_timezone_set('Asia/Kolkata');

class SecurityConstants
{
    public const AND_IS_ACTIVE_0 = ' AND is_active = 0 ';
    public const AND_LOCATION_ID = ' AND location_id=';
    public const DATE_FORMAT_YMD_HIS = 'Y-m-d H:i:s';
    public const USER_CLASS_PATH = 'model/user.class.php';
    public const SEL_VISITOR_OPTIONS = 'SELECT option_value AS genc_detail_code, option_label AS genc_detail_name FROM visitor_options WHERE company_id = ';
    public const SEL_VISITOR_OPTIONS_SPACED = 'SELECT option_value AS genc_detail_code, option_label AS genc_detail_name  FROM visitor_options WHERE company_id = ';
    public const ORDER_BY_IN_DATETIME_DESC = 'vl.in_datetime DESC';
    public const OPT_IDENTITY_CARD = " AND option_type='identity_card'" . self::AND_IS_ACTIVE_0;
    public const OPT_DELIVERY_COMPANY = " AND option_type='delivery_company'" . self::AND_IS_ACTIVE_0;
    public const OPT_SERVICE_COMPANY = " AND option_type='service_company'" . self::AND_IS_ACTIVE_0;
    public const WHERE_COMPANY_ID_APOS = " WHERE company_id = '";
    public const APOS_AND_IS_ACTIVE_0 = "'  AND is_active =0";
    public const SELECT_STAR_FROM = 'SELECT * FROM ';
    public const SEL_EMPLOYEE_NAME = 'SELECT display_name FROM employees WHERE id = ';
    public const URL_PARAM = '?param=';
    public const AND_OP = ' AND ';
    public const REQ_NOT_FOUND = 'Request not found';
}

trait SecurityVisitorQueryTrait
{
    public function getVisitorsReportData($get)
    {
        global $callConfig;
        $return = array();
        $date = $get['date'] ?? '';
        $visit_type = $get['visit_type'] ?? '';

        $whr = '';
        if ($date != '') {
            $whr .= " AND DATE(in_datetime) = '" . $callConfig->real_escape_string($date) . "' ";
        } elseif (isset($get['in_datetime']) && isset($get['out_datetime']) && $get['in_datetime'] != '' && $get['out_datetime'] != '') {
            // Backward compatibility
            $from = $get['in_datetime'];
            $to = $get['out_datetime'];
            $whr .= " AND in_datetime >= '" . $callConfig->real_escape_string($from) . "' AND in_datetime <= '" . $callConfig->real_escape_string($to) . "' ";
        } else {
            // Default to today
            $whr .= " AND DATE(in_datetime) = CURDATE() ";
        }

        if ($visit_type != '' && strtolower($visit_type) !== 'all') {
            $whr .= " AND LOWER(visit_type) = '" . $callConfig->real_escape_string(strtolower($visit_type)) . "' ";
        }

        $query = SecurityConstants::SELECT_STAR_FROM . "visitor_logs WHERE company_id = " . (int) $get['company_id'] . SecurityConstants::AND_IS_ACTIVE_0 . $whr . " ORDER BY in_datetime DESC";
        $return['query'] = $query;
        $data = $callConfig->getAllRows($query);
        $return['visitor_logs_res'] = $data;
        return $return;
    }

    public function getVisitorsDashboardData($get)
    {
        global $callConfig;
        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = SecurityConstants::AND_LOCATION_ID . $get['location_id'];
        }

        $queryTotals = "SELECT
                COUNT(*) AS total_visitors,
                SUM(in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS today_visitors,
                SUM(in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE()) AS yesterday_visitors,
                SUM(in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS last_7days_visitors
            FROM visitor_logs
            WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id";
        $data = (array) $callConfig->getRow($queryTotals);

        $return = [];
        $return['total_visitors'] = (int) ($data['total_visitors'] ?? 0);
        $return['today_visitors'] = (int) ($data['today_visitors'] ?? 0);
        $return['yesterday_visitors'] = (int) ($data['yesterday_visitors'] ?? 0);
        $return['last_7days_visitors'] = (int) ($data['last_7days_visitors'] ?? 0);

        $q = "SELECT
                LOWER(COALESCE(visit_type, 'others')) AS vt,
                COUNT(*) AS a_total,
                SUM(out_datetime IS NOT NULL) AS a_out,
                SUM(out_datetime IS NULL AND current_Status != 10 AND current_Status != 199) AS a_in_only,
                SUM(in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS t_total,
                SUM(out_datetime >= CURDATE() AND out_datetime < CURDATE() + INTERVAL 1 DAY) AS t_out,
                SUM(in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY AND out_datetime IS NULL AND current_Status != 10 AND current_Status != 199) AS t_in_only,
                SUM(in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE()) AS y_total,
                SUM(out_datetime >= CURDATE() - INTERVAL 1 DAY AND out_datetime < CURDATE()) AS y_out,
                SUM(in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE() AND out_datetime IS NULL AND current_Status != 10 AND current_Status != 199) AS y_in_only,
                SUM(in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS l7_total,
                SUM(out_datetime >= CURDATE() - INTERVAL 7 DAY AND out_datetime < CURDATE() + INTERVAL 1 DAY) AS l7_out,
                SUM(in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY AND out_datetime IS NULL AND current_Status != 10 AND current_Status != 199) AS l7_in_only
            FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id GROUP BY vt";
        $rows = (array) $callConfig->getAllRows($q);

        $types = ['visitor', 'delivery', 'service', 'employee', 'doctor', 'vendor_supplier', 'others'];
        $mk = function () {
            return ['total' => 0, 'in_only' => 0, 'out' => 0];
        };
        $breakdown = [
            'TODAY' => array_fill_keys($types, $mk()),
            'YESTERDAY' => array_fill_keys($types, $mk()),
            'LAST7DAYS' => array_fill_keys($types, $mk()),
            'ALL' => array_fill_keys($types, $mk()),
        ];

        foreach ($rows as $r) {
            $t = $r->vt ?? 'others';
            if (!in_array($t, $types, true)) {
                $t = 'others';
            }

            $breakdown['TODAY'][$t]['total'] = (int) $r->t_total;
            $breakdown['TODAY'][$t]['out'] = (int) $r->t_out;
            $breakdown['TODAY'][$t]['in_only'] = (int) $r->t_in_only;

            $breakdown['YESTERDAY'][$t]['total'] = (int) $r->y_total;
            $breakdown['YESTERDAY'][$t]['out'] = (int) $r->y_out;
            $breakdown['YESTERDAY'][$t]['in_only'] = (int) $r->y_in_only;

            $breakdown['LAST7DAYS'][$t]['total'] = (int) $r->l7_total;
            $breakdown['LAST7DAYS'][$t]['out'] = (int) $r->l7_out;
            $breakdown['LAST7DAYS'][$t]['in_only'] = (int) $r->l7_in_only;

            $breakdown['ALL'][$t]['total'] = (int) $r->a_total;
            $breakdown['ALL'][$t]['out'] = (int) $r->a_out;
            $breakdown['ALL'][$t]['in_only'] = (int) $r->a_in_only;
        }

        $return['breakdown'] = $breakdown;

        $recentInSql = "SELECT id, visit_type, in_datetime, out_datetime, image_path, visitor_idcard_no, name, phone, email, whom_to_meet, service_company,  service_person, authorized_employee, employee_code, employee_department,  delivery_company_person, delivery_phone, deliver_to FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0  AND out_datetime IS NULL AND current_Status != 10 AND current_Status != 199 $location_id ORDER BY in_datetime DESC LIMIT 10";
        $return['recent_in'] = $callConfig->getAllRows($recentInSql);

        $recentOutSql = "SELECT id, visit_type, in_datetime, out_datetime, image_path, visitor_idcard_no, name, phone, email, whom_to_meet, service_company,  service_person, authorized_employee, employee_code, employee_department,  delivery_company_person, delivery_phone, deliver_to FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0 AND out_datetime IS NOT NULL $location_id ORDER BY out_datetime DESC LIMIT 10";
        $return['recent_out'] = $callConfig->getAllRows($recentOutSql);

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id";
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        $emp_idname = array();
        foreach ($recent_out_rows as $row) {
            $emp_arr[$row->emp_code] = $row;
            $emp_idname[$row->id] = $row->display_name;
        }
        $return['emp_arr'] = $emp_arr;
        $return['emp_arr_name'] = $emp_idname;

        return $return;
    }

    private function getVisitorDateFilter($day_type): string
    {
        $filters = array(
            'TODAY' => " AND in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY ",
            'YESTERDAY' => " AND in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE() ",
            'LAST7DAYS' => " AND in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY "
        );
        return $filters[$day_type] ?? '';
    }

    private function getVisitorLuggageMap($company_id, array $data): array
    {
        global $callConfig;
        $logIds = array_map(function ($r) {
            return (int) $r->id;
        }, $data);
        $luggageMap = [];

        if (!empty($logIds)) {
            $idsCsv = implode(',', array_unique(array_filter($logIds)));
            $lugSql = "SELECT * FROM visitor_luggages WHERE company_id = {$company_id} AND is_active = 0 AND visitor_log_id IN ($idsCsv) ORDER BY visitor_log_id, id ";
            $lugRows = $callConfig->getAllRows($lugSql);
            foreach ($lugRows as $lr) {
                $luggageMap[$lr->visitor_log_id][] = $lr;
            }
        }
        return $luggageMap;
    }

    private function getVisitorOptionsMap($company_id, $optionType): array
    {
        global $callConfig;
        $query = SecurityConstants::SEL_VISITOR_OPTIONS . (int) $company_id . " AND option_type='" . $callConfig->real_escape_string($optionType) . "'" . SecurityConstants::AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach ($data as $row) {
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        return $res;
    }

    public function getVisitorsDataBasedOnDate($get)
    {
        global $callConfig;
        $return = array();

        $cid = (int) ($get['company_id'] ?? 0);
        $day_type = $get['in_datetime'] ?? 'TODAY';
        $q = trim($get['q'] ?? '');
        $offset = max(0, (int) ($get['offset'] ?? 0));
        $limit = min(50, max(5, (int) ($get['limit'] ?? 10)));
        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = SecurityConstants::AND_LOCATION_ID . $get['location_id'];
        }

        $whr = $this->getVisitorDateFilter($day_type);

        $query = SecurityConstants::SELECT_STAR_FROM . "visitor_logs WHERE company_id = " . $cid . SecurityConstants::AND_IS_ACTIVE_0 . $whr . " $location_id ORDER BY out_datetime ASC, in_datetime ASC";
        $data = $callConfig->getAllRows($query);
        $return['visitor_logs_res'] = $data;
        $return['luggage_map'] = $this->getVisitorLuggageMap($cid, $data);

        $searchSql = '';
        if ($q !== '') {
            $like = addcslashes($q, '%_');
            $like = '%' . $like . '%';
            $like = $callConfig->real_escape_string($like);
            $searchSql = " AND (name LIKE '$like'  OR email LIKE '$like'   OR visitor_idcard_no LIKE '$like'  OR employee_code LIKE '$like' OR visit_type LIKE '$like' OR service_person LIKE '$like' OR doctor_name LIKE '$like' OR emp_temp_name LIKE '$like' OR doctor_phone LIKE '$like' OR emp_temp_phone LIKE '$like' OR employee_name LIKE '$like' OR phone LIKE '$like' OR service_phone LIKE '$like' OR delivery_phone LIKE '$like' )";
        }

        $sqlAll = "SELECT *  FROM visitor_logs  WHERE company_id = $cid AND is_active = 0  $whr  $searchSql  $location_id ORDER BY out_datetime ASC, in_datetime ASC LIMIT $offset, $limit";
        $return['visitor_logs_all'] = $callConfig->getAllRows($sqlAll);

        $sqlPending = "SELECT * FROM visitor_logs  WHERE company_id = $cid  AND is_active = 0  AND in_datetime IS NOT NULL  AND out_datetime IS NULL AND current_Status != 10 AND current_Status != 199  $whr   $searchSql $location_id ORDER BY in_datetime ASC LIMIT $offset, $limit";
        $return['visitor_logs_pending'] = $callConfig->getAllRows($sqlPending);

        $sqlCompleted = "SELECT *  FROM visitor_logs WHERE company_id = $cid  AND is_active = 0  AND in_datetime IS NOT NULL  AND out_datetime IS NOT NULL  $whr  $searchSql $location_id ORDER BY out_datetime ASC LIMIT $offset, $limit";
        $return['visitor_logs_completed'] = $callConfig->getAllRows($sqlCompleted);

        $return['id_card_types_res'] = $this->getVisitorOptionsMap($cid, 'identity_card');
        $return['delivery_company_res'] = $this->getVisitorOptionsMap($cid, 'delivery_company');
        $return['service_company_res'] = $this->getVisitorOptionsMap($cid, 'service_company');

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$cid} AND is_active = 0";
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        $employees_map = array();
        foreach ($recent_out_rows as $row) {
            $emp_arr[$row->emp_code] = $row;
            $employees_map[$row->id] = $row->display_name;
        }
        $return['emp_arr'] = $emp_arr;
        $return['employees_map'] = $employees_map;

        $user_map_sql = "SELECT id, display_name FROM application_users WHERE company_id = {$cid}";
        $user_map_rows = $callConfig->getAllRows($user_map_sql);
        $users_map = array();
        foreach ($user_map_rows as $row) {
            $users_map[$row->id] = $row->display_name;
        }
        $return['users_map'] = $users_map;

        $query = "SELECT id, display_name AS name FROM vendors WHERE company_id = " . $cid . SecurityConstants::AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        $vendors_res = array();
        foreach ($data as $row) {
            $vendors_res[$row->id] = $row->name;
        }
        $return['vendors_res'] = $vendors_res;

        return $return;
    }

    private function getVisitorPassLuggagesAndBadges($company_id, $visitor_log_id): array
    {
        global $callConfig;
        if ($visitor_log_id <= 0) {
            return array('luggages' => array(), 'badge' => array());
        }
        $qLug = "SELECT * FROM visitor_luggages WHERE company_id = {$company_id} AND visitor_log_id = {$visitor_log_id} AND is_active = 0 ORDER BY id ASC";
        $qBadge = "SELECT * FROM visitor_badges WHERE company_id = {$company_id} AND visitor_log_id = {$visitor_log_id} AND is_active = 0 ORDER BY id DESC";
        return array(
            'luggages' => $callConfig->getAllRows($qLug) ?: array(),
            'badge' => $callConfig->getRow($qBadge) ?: array()
        );
    }

    public function getVisitorPassDetails($get)
    {
        global $callConfig;
        $return = array();
        $company_id = (int) ($get['company_id'] ?? 0);
        $type = trim($get['type'] ?? '');
        $searchId = trim($get['id'] ?? '');

        $whr = " WHERE v.company_id = {$company_id} AND v.is_active = 0 AND v.current_Status = 0 ";
        if ($type !== '') {
            $safe_type = addslashes($type);
            $whr .= " AND v.visit_type = '{$safe_type}' ";
        }
        if ($searchId !== '') {
            $whr .= " AND v.id = '{$searchId}'";
        }
        $qMain = "SELECT * FROM visitor_logs v {$whr} ORDER BY v.id DESC LIMIT 1";
        $row = $callConfig->getRow($qMain);
        $return['visitor_logs_res'] = $row ?: array();

        $lugBadge = $this->getVisitorPassLuggagesAndBadges($company_id, (int) ($row->id ?? 0));
        $return['visitor_luggages_res'] = $lugBadge['luggages'];
        $return['visitor_badges_res'] = $lugBadge['badge'];

        $resolved_type = strtolower($type ?: ($row->visit_type ?? ''));
        if ($resolved_type === 'employee') {
            $emp = array();
            if ($row->employee_code != '') {
                $employee_code = $row->employee_code;
                $qEmpByCode = "SELECT id, emp_code, department_id, display_name  FROM employees WHERE company_id = {$company_id} AND is_active = 0 AND emp_code = '{$employee_code}' LIMIT 1";
                $emp = $callConfig->getRow($qEmpByCode);
            }
            $return['employee_res'] = $emp ?: array();
        }

        $qGates = "SELECT id, name FROM building_gates WHERE company_id = {$company_id} AND is_active = 0";
        $return['building_gates_res'] = $callConfig->getAllRows($qGates) ?: array();

        $return['id_card_types_res'] = $this->getVisitorOptionsMap($company_id, 'identity_card');
        $return['delivery_company_res'] = $this->getVisitorOptionsMap($company_id, 'delivery_company');
        $return['service_company_res'] = $this->getVisitorOptionsMap($company_id, 'service_company');

        $venquery = "SELECT id, company, display_name FROM vendors WHERE company_id = " . $company_id . "  AND is_active = 0 ";
        $venres = $callConfig->getAllRows($venquery);
        $vendors = array();
        foreach ($venres as $row) {
            $vendors[$row->id] = $row;
        }
        $return['vendors'] = $vendors;

        $depquery = "SELECT id, department_name AS display_name FROM departments WHERE company_id = " . $company_id . "  AND is_active = 0 ";
        $depres = $callConfig->getAllRows($depquery);
        $departments = array();
        foreach ($depres as $row) {
            $departments[$row->id] = $row;
        }
        $return['departments'] = $departments;

        return $return;
    }

    public function getVisitorInsertRelatedData($get)
    {
        global $callConfig;
        $return = array();
        $rid = trim($get['rid'] ?? '');
        $company_id = (int) ($get['company_id'] ?? 0);

        if ($get['transaction_id'] != "" && $get['transaction_table'] != "") {
            $userObj = new userClass();
            $transaction_id = $get['transaction_id'];
            $transaction_table = $get['transaction_table'];
            $tables = $userObj->toGetInvoicetabledata($transaction_table);

            $rental_invoicessql = SecurityConstants::SELECT_STAR_FROM . $transaction_table . SecurityConstants::WHERE_COMPANY_ID_APOS . $company_id . "' AND id = '" . $transaction_id . SecurityConstants::APOS_AND_IS_ACTIVE_0;
            $return['transaction'] = $callConfig->getRow($rental_invoicessql);

            $transaction_itemssql = SecurityConstants::SELECT_STAR_FROM . $tables['item'] . SecurityConstants::WHERE_COMPANY_ID_APOS . $company_id . "' AND transaction_id = '" . $transaction_id . SecurityConstants::APOS_AND_IS_ACTIVE_0;
            $return['transaction_items'] = $callConfig->getAllRows($transaction_itemssql);

            $transaction_item_taxessql = SecurityConstants::SELECT_STAR_FROM . $tables['tax'] . SecurityConstants::WHERE_COMPANY_ID_APOS . $company_id . "' AND transaction_id = '" . $transaction_id . SecurityConstants::APOS_AND_IS_ACTIVE_0;
            $return['transaction_item_taxes'] = $callConfig->getAllRows($transaction_item_taxessql);

            $unit_of_measurementssql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements" . SecurityConstants::WHERE_COMPANY_ID_APOS . $company_id . SecurityConstants::APOS_AND_IS_ACTIVE_0;
            $unit_of_measurementsres = $callConfig->getAllRows($unit_of_measurementssql);
            $unit_of_measurements = array();
            foreach ($unit_of_measurementsres as $uom) {
                $unit_of_measurements[$uom->id] = $uom;
            }
            $return['unit_of_measurements'] = $unit_of_measurements;

            $query = "SELECT id, sku, item_name, purchase_uom  FROM items WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
            $return['items'] = $callConfig->getAllRows($query);

            return $return;
        }

        if ($rid !== '') {
            $rid_int = (int) $rid;
            $return['visitor_logs_res'] = $callConfig->getRow("SELECT * FROM visitor_logs WHERE id={$rid_int} AND is_active=0");
            $return['service_tag_children_res'] = $callConfig->getAllRows("SELECT * FROM visitor_logs WHERE tag_id={$rid_int} AND is_active=0");
            $return['visitor_luggages_res'] = $callConfig->getAllRows("SELECT * FROM visitor_luggages WHERE visitor_log_id={$rid_int} AND is_active=0");
            $return['visitor_badge_res'] = $callConfig->getRow("SELECT * FROM visitor_badges WHERE visitor_log_id={$rid_int} AND is_active=0 LIMIT 1");
        }

        $query = "SELECT id, display_name FROM employees WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $return['employees_res'] = $callConfig->getAllRows($query);

        $query = "SELECT id, name FROM building_gates WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $return['building_gates_res'] = $callConfig->getAllRows($query);

        $query = SecurityConstants::SEL_VISITOR_OPTIONS . $company_id . " AND option_type='luggage_brand'" . SecurityConstants::AND_IS_ACTIVE_0;
        $return['luggage_brand_res'] = $callConfig->getAllRows($query);

        $query = SecurityConstants::SEL_VISITOR_OPTIONS_SPACED . $company_id . SecurityConstants::OPT_IDENTITY_CARD;
        $return['id_card_types_res'] = $callConfig->getAllRows($query);

        $query = SecurityConstants::SEL_VISITOR_OPTIONS_SPACED . $company_id . SecurityConstants::OPT_DELIVERY_COMPANY;
        $return['delivery_company_res'] = $callConfig->getAllRows($query);

        $query = SecurityConstants::SEL_VISITOR_OPTIONS_SPACED . $company_id . SecurityConstants::OPT_SERVICE_COMPANY;
        $return['service_company_res'] = $callConfig->getAllRows($query);

        $optTypes = ['expected_duration', 'vehicle_type', 'delivery_type', 'service_classification', 'vendor_supplier_entry_type', 'visit_purpose'];
        foreach ($optTypes as $ot) {
            $qOpt = SecurityConstants::SEL_VISITOR_OPTIONS . $company_id . " AND option_type='{$ot}'" . SecurityConstants::AND_IS_ACTIVE_0;
            $return[$ot . '_res'] = $callConfig->getAllRows($qOpt);
        }

        $query = "SELECT id, CONCAT(COALESCE(company, ''), ' - ', COALESCE(display_name, '')) AS name FROM vendors WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $return['vendors_res'] = $callConfig->getAllRows($query);

        $query = "SELECT id, department_name AS name FROM departments WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $return['departments_res'] = $callConfig->getAllRows($query);

        if ($rid == '' && !empty($get['gate_id'])) {
            $return['next_badge'] = $this->getNextBadgeForGate($get);
        }

        $unit_of_measurementssql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements" . SecurityConstants::WHERE_COMPANY_ID_APOS . $company_id . SecurityConstants::APOS_AND_IS_ACTIVE_0;
        $unit_of_measurementsres = $callConfig->getAllRows($unit_of_measurementssql);
        $return['unit_of_measurements'] = $unit_of_measurementsres;
        $return['noref_uom_res'] = $unit_of_measurementsres;

        $query = "SELECT id, sku, item_name, purchase_uom  FROM items WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        $return['noref_items_res'] = $data;
        $return['items'] = $data;
        return $return;
    }

    public function getNextBadgeForGate($get)
    {
        global $callConfig;
        if (($get['gate_id'] ?? 0) <= 0) {
            return array('status' => 'error', 'message' => 'Missing company or gate');
        }
        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = SecurityConstants::AND_LOCATION_ID . $get['location_id'];
        }

        $cfgSql = "SELECT visit_code, range_start, range_end, padding, is_active FROM gate_badge_configs WHERE company_id = {$get['company_id']} AND  gate_id = {$get['gate_id']}   LIMIT 1";
        $cfg = (array) $callConfig->getRow($cfgSql);
        if (!$cfg || (int) $cfg['is_active'] != 0) {
            return array('status' => 'error', 'message' => 'Gate not configured or deactivated');
        }

        $start = (int) $cfg['range_start'];
        $end = (int) $cfg['range_end'];
        $padding = (int) $cfg['padding'];
        $visit_code = trim($cfg['visit_code']) != '' ? $cfg['visit_code'] : 'VISIT';

        $maxSql = "SELECT COALESCE(MAX(CAST(RIGHT(full_code, {$padding}) AS UNSIGNED)), " . ($start - 1) . ") AS maxnum
                FROM visitor_badges WHERE company_id = {$get['company_id']} AND gate_id = {$get['gate_id']} AND badge_date = CURDATE()AND is_active=0 $location_id";
        $maxRow = (array) $callConfig->getRow($maxSql);
        $maxnum = isset($maxRow['maxnum']) ? (int) $maxRow['maxnum'] : ($start - 1);

        $nextNum = $maxnum + 1;
        $isExhausted = ($nextNum > $end);
        $fullCode = $visit_code . str_pad((string) $nextNum, $padding, '0', STR_PAD_LEFT);

        return array(
            'status' => $isExhausted ? 'exhausted' : 'success',
            'message' => $isExhausted ? "All IDs used for gate {$get['gate_id']} ({$start}-{$end})." : null,
            'full_code' => $isExhausted ? null : $fullCode,
            'next_number' => $isExhausted ? null : $nextNum,
            'gate_id' => $get['gate_id'],
            'range_start' => $start,
            'range_end' => $end,
            'padding' => $padding
        );
    }

    public function getEmployeePassDetails($get)
    {
        global $callConfig;
        $return = array();
        $whr = '';
        if (isset($get['emp_code']) && $get['emp_code'] != '') {
            $whr .= " AND emp_code = '" . urldecode($get['emp_code']) . "' ";
        }
        if (isset($get['rid']) && $get['rid'] != '') {
            $whr .= " AND id = '" . $get['rid'] . "' ";
        }
        $query = "SELECT id, `display_name`, `phone`, `email_id`,  `emp_code`, address, is_active, current_Status FROM employees
        WHERE  company_id = " . $get['company_id'] . "    $whr  AND is_active = 0 ORDER BY id DESC LIMIT 1";
        $return['employee_logs_res'] = $callConfig->getRow($query);

        $query = "SELECT id, name FROM building_gates WHERE company_id = " . $get['company_id'] . " AND is_active = 0";
        $return['building_gates_res'] = $callConfig->getAllRows($query);

        return $return;
    }

    public function getVisitorsByPhoneAndEmpCode($get)
    {
        global $callConfig;
        $return = array();
        $phone = urldecode($get['phone'] ?? '');
        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = SecurityConstants::AND_LOCATION_ID . $get['location_id'];
        }
        if ($phone != '') {
            $query = "SELECT id, `name`, `phone`, `email`, `address`, `whom_to_meet`, `purpose`, `idcard_type`, `idcard_number`, `visit_type`, `image_path`, `visitor_idcard_no`, `in_gate_no`, `in_datetime`, `in_security_id`, `out_security_id`, `out_datetime`, `out_gate_no`, `remarks`, is_active, current_Status FROM visitor_logs
            WHERE company_id = " . $get['company_id'] . " AND phone = '" . $phone . "' AND is_active = 0 $location_id ORDER BY id DESC LIMIT 5";
            $return['visitor_logs_res'] = $callConfig->getAllRows($query);
        }
        $employee_code = urldecode($get['employee_code'] ?? '');
        if ($employee_code != '') {
            $query = "SELECT emp.id, emp.emp_code, emp.display_name, dept.department_name FROM employees emp, departments dept
            WHERE emp.department_id=dept.id AND emp.company_id = " . $get['company_id'] . " AND emp.emp_code = '" . $employee_code . "' AND emp.is_active = 0  $location_id";
            $return['emp_res'] = $callConfig->getRow($query);
        }

        return $return;
    }

    private function getTransactionItemsAndVendor($company_id, $transaction_id, $transaction_table): array
    {
        global $callConfig;
        $vendor_id = 0;
        $txnItems = [];

        if ($transaction_id > 0 && in_array($transaction_table, ['purchase_orders', 'purchase_invoices'], true)) {
            $row = $callConfig->getRow("SELECT party_id FROM {$transaction_table} WHERE id = {$transaction_id} AND company_id = {$company_id} AND is_active = 0");
            if ($row) {
                $vendor_id = (int) ($row->party_id ?? 0);
            }
            $itemTable = ($transaction_table === 'purchase_orders') ? 'po_items' : 'pi_items';
            $txnItemsRows = $callConfig->getAllRows("SELECT item_id, item_name, description, purchase_uom, item_quantity FROM {$itemTable} WHERE transaction_id = {$transaction_id} AND company_id = {$company_id} AND is_active = 0");
            if (!empty($txnItemsRows)) {
                foreach ($txnItemsRows as $r) {
                    $txnItems[] = (array) $r;
                }
            }
        }
        return [$vendor_id, $txnItems];
    }

    public function getVendorSupplierTransactionItems($get)
    {
        global $callConfig;
        $company_id = (int) ($get['company_id'] ?? 1);
        $transaction_id = (int) ($get['transaction_id'] ?? 0);
        $transaction_table = $get['transaction_table'] ?? '';

        list($vendor_id, $txnItems) = $this->getTransactionItemsAndVendor($company_id, $transaction_id, $transaction_table);

        $allItems = [];
        $allItemsRows = $callConfig->getAllRows("SELECT id, sku, item_name, purchase_uom FROM items WHERE company_id = {$company_id} AND is_active = 0");
        if (!empty($allItemsRows)) {
            foreach ($allItemsRows as $row) {
                $allItems[] = (array) $row;
            }
        }

        $allUoms = [];
        $allUomsRows = $callConfig->getAllRows("SELECT id, uom_name, uom_short_code FROM unit_of_measurements WHERE company_id = {$company_id} AND is_active = 0");
        if (!empty($allUomsRows)) {
            foreach ($allUomsRows as $row) {
                $allUoms[] = (array) $row;
            }
        }

        return [
            'vendor_id' => $vendor_id,
            'txnItems' => $txnItems,
            'allItems' => $allItems,
            'allUoms' => $allUoms,
        ];
    }

    private function buildVisitorSearchWhere($company_id, $visit_type, $text, $date): array
    {
        $like = function ($s) {
            $s = addslashes($s);
            return str_replace(['%', '_'], ['\%', '\_'], $s);
        };

        $where = [];
        $where[] = "company_id = {$company_id}";
        $where[] = "is_active = 0";
        $where[] = "current_status = 0";

        if ($visit_type !== '') {
            $safeType = addslashes($visit_type);
            $where[] = "visit_type = '{$safeType}'";
        }

        if ($text !== '') {
            $t = $like($text);
            $ors = [];
            $ors[] = "name LIKE '%{$t}%'";
            $ors[] = "phone LIKE '%{$t}%'";
            $ors[] = "visitor_idcard_no LIKE '%{$t}%'";
            $ors[] = "idcard_number LIKE '%{$t}%'";
            if (ctype_digit($text)) {
                $ors[] = "id = " . (int) $text;
            }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        }

        if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $where[] = "DATE(in_datetime) = '{$date}'";
        }

        return $where;
    }

    public function getVisitorsSearch($get)
    {
        global $callConfig;
        $out = ['visitor_logs_res' => []];

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        $text = isset($get['text']) ? trim($get['text']) : '';
        $date = isset($get['date']) ? trim($get['date']) : '';
        $visit_type = isset($get['visit_type']) ? trim($get['visit_type']) : '';

        if ($company_id <= 0 || ($text === '' && $date === '')) {
            return $out;
        }

        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = SecurityConstants::AND_LOCATION_ID . $get['location_id'];
        }

        $where = $this->buildVisitorSearchWhere($company_id, $visit_type, $text, $date);
        $whereSql = implode(SecurityConstants::AND_OP, $where);
        $sql = "SELECT * FROM visitor_logs WHERE {$whereSql}  $location_id ORDER BY id DESC";
        $out['debug_sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $out['visitor_logs_res'] = $rows ?: [];

        return $out;
    }

    private function buildVisitorsListCondition($period, $bucket): array
    {
        $inWin = '';
        $outWin = '';
        if ($period === 'TODAY') {
            $inWin = "vl.in_datetime  >= CURDATE() AND vl.in_datetime  < CURDATE() + INTERVAL 1 DAY";
            $outWin = "vl.out_datetime >= CURDATE() AND vl.out_datetime < CURDATE() + INTERVAL 1 DAY";
        } elseif ($period === 'YESTERDAY') {
            $inWin = "vl.in_datetime  >= CURDATE() - INTERVAL 1 DAY AND vl.in_datetime  < CURDATE()";
            $outWin = "vl.out_datetime >= CURDATE() - INTERVAL 1 DAY AND vl.out_datetime < CURDATE()";
        } elseif ($period === 'LAST7DAYS') {
            $inWin = "vl.in_datetime  >= CURDATE() - INTERVAL 7 DAY AND vl.in_datetime  < CURDATE() + INTERVAL 1 DAY";
            $outWin = "vl.out_datetime >= CURDATE() - INTERVAL 7 DAY AND vl.out_datetime < CURDATE() + INTERVAL 1 DAY";
        }

        $where = [];
        $orderBy = SecurityConstants::ORDER_BY_IN_DATETIME_DESC;

        if ($bucket === 'total') {
            if ($period !== 'ALL') {
                $where[] = '(' . $inWin . ' OR ' . $outWin . ')';
            }
        } elseif ($bucket === 'out') {
            $where[] = "vl.out_datetime IS NOT NULL";
            if ($period !== 'ALL') {
                $where[] = $outWin;
            }
            $orderBy = "vl.out_datetime DESC";
        } else {
            $where[] = "vl.out_datetime IS NULL AND vl.current_Status != 10 AND vl.current_Status != 199";
            if ($period !== 'ALL') {
                $where[] = $inWin;
            }
        }
        return [$where, $orderBy];
    }

    public function getVisitorsList($get)
    {
        global $callConfig;
        $return = array();
        $company_id = (int) ($get['company_id'] ?? 0);
        $location_id = '';
        if (isset($get['location_id']) && $get['location_id'] > 0) {
            $location_id = ' AND vl.location_id=' . $get['location_id'];
        }

        $period = strtoupper(trim($get['period'] ?? 'TODAY'));
        $type = strtolower(trim($get['type'] ?? 'visitor'));
        $bucket = strtolower(trim($get['bucket'] ?? 'total'));

        $periodAllow = ['TODAY', 'YESTERDAY', 'LAST7DAYS', 'ALL'];
        $typeAllow = ['visitor', 'delivery', 'service', 'employee', 'doctor', 'vendor_supplier', 'others'];
        $bucketAllow = ['total', 'in_only', 'out'];

        if (!in_array($period, $periodAllow, true)) {
            $period = 'TODAY';
        }
        if (!in_array($type, $typeAllow, true)) {
            $type = 'visitor';
        }
        if (!in_array($bucket, $bucketAllow, true)) {
            $bucket = 'total';
        }

        $where = [];
        $where[] = "vl.company_id = {$company_id}";
        $where[] = "vl.is_active = 0";
        $where[] = "vl.visit_type = '{$type}'";

        list($extraWhere, $orderBy) = $this->buildVisitorsListCondition($period, $bucket);
        $where = array_merge($where, $extraWhere);

        $whereSql = $where ? ('WHERE ' . implode(SecurityConstants::AND_OP, $where)) : '';
        $sql = "SELECT vl.* FROM visitor_logs vl $whereSql $location_id ORDER BY $orderBy ";
        $return['list'] = $callConfig->getAllRows($sql);

        $return['delivery_company_res'] = $this->getVisitorOptionsMap($company_id, 'delivery_company');
        $return['service_company_res'] = $this->getVisitorOptionsMap($company_id, 'service_company');

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$company_id}" . SecurityConstants::AND_IS_ACTIVE_0;
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        $employees_map = array();
        foreach ($recent_out_rows as $row) {
            $emp_arr[$row->emp_code] = $row;
            $employees_map[$row->id] = $row->display_name;
        }
        $return['emp_arr'] = $emp_arr;
        $return['employees_map'] = $employees_map;

        $user_map_sql = "SELECT id, display_name FROM application_users WHERE company_id = {$company_id}";
        $user_map_rows = $callConfig->getAllRows($user_map_sql);
        $users_map = array();
        foreach ($user_map_rows as $row) {
            $users_map[$row->id] = $row->display_name;
        }
        $return['users_map'] = $users_map;

        $query = "SELECT id, display_name AS name FROM vendors WHERE company_id = " . $company_id . SecurityConstants::AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        $vendors_res = array();
        foreach ($data as $row) {
            $vendors_res[$row->id] = $row->name;
        }
        $return['vendors_res'] = $vendors_res;

        return $return;
    }

    public function getvisitormaterialgatepassdata($get)
    {
        global $callConfig;
        $return = array();
        if ($get['rid'] != '') {
            $query = "SELECT * FROM visitor_logs WHERE id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res = $callConfig->getRow($query);
            $return['visitor_data'] = $res;

            $query = "SELECT * FROM visitor_luggages WHERE visitor_log_id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $return['visitor_luggage_data'] = $callConfig->getAllRows($query);

            $emp_code = $res->employee_code ?? '';
            if ($emp_code != '') {
                $query = "SELECT * FROM employees WHERE emp_code='{$emp_code}' AND company_id = {$get['company_id']} AND is_active = 0 ";
                $return['employees_data'] = $callConfig->getRow($query);
            }
        }
        return $return;
    }

    public function getvisitorworkpermitdata($get)
    {
        global $callConfig;
        $return = array();
        if ($get['rid'] != '') {
            $query = "SELECT * FROM visitor_logs WHERE id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res = $callConfig->getRow($query);

            $query = "SELECT * FROM visitor_luggages WHERE visitor_log_id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $return['visitor_luggage_data'] = $callConfig->getAllRows($query);

            $tag_id = $res->id ?? 0;
            if ($tag_id != '' && $tag_id != 0) {
                $query = "SELECT * FROM visitor_logs WHERE tag_id={$tag_id} AND company_id = {$get['company_id']} AND is_active = 0 ";
                $return['visitor_workers_data'] = $callConfig->getAllRows($query);
            }

            $location_id = $res->location_id ?? 0;
            if ($location_id != 0) {
                $query = "SELECT id,location_name FROM locations WHERE id={$location_id} AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res3 = $callConfig->getRow($query);
                $res->location_name = $res3->location_name;
            }
            $service_company = $res->service_company ?? '';
            if ($service_company != '') {
                $query = "SELECT option_label AS genc_detail_name FROM visitor_options WHERE option_type='service_company' AND (option_value='" . $callConfig->real_escape_string($service_company) . "' OR id='" . $callConfig->real_escape_string($service_company) . "') AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res4 = $callConfig->getRow($query);
                $res->service_company_name = $res4 ? $res4->genc_detail_name : $service_company;
            }

            $return['visitor_data'] = $res;
        }
        return $return;
    }
}

trait SecurityVisitorEntryTrait
{
    public function makeCode($name, $company_id)
    {
        global $callConfig;
        $row = $callConfig->getRow("SELECT genc_detail_code FROM general_code_details WHERE company_id={$company_id} AND genc_code='{$name}' AND is_active=0 ORDER BY genc_detail_code DESC LIMIT 1");
        return $row->genc_detail_code ? (int) $row->genc_detail_code + 1 : 1;
    }

    private function registerNewVisitorOption($company_id, $user_id, $optionType, $newName): bool
    {
        global $callConfig;
        if ($newName === '') {
            return false;
        }
        $ins = array(
            'company_id' => $company_id,
            'option_type' => $optionType,
            'option_value' => $newName,
            'option_label' => $newName,
            'is_active' => 0,
            'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
            'created_by' => $user_id ?: null,
        );
        $res = $callConfig->insertRecord('visitor_options', $ins);
        return (int) ($res['LASTID'] ?? 0) > 0;
    }

    private function normalizeVisitorOptions($company_id, $user_id, array &$post): void
    {
        if (($post['visit_type'] ?? '') === 'delivery' && ($post['delivery_company_person'] ?? '') === '__new__') {
            $newName = trim($post['new_delivery_company'] ?? '');
            if ($this->registerNewVisitorOption($company_id, $user_id, 'delivery_company', $newName)) {
                $post['delivery_company_person'] = $newName;
            }
        }
        if (!empty($post['whom_to_meet_vendor']) && $post['whom_to_meet_vendor'] != '0') {
            $post['whom_to_meet'] = $post['whom_to_meet_vendor'];
        }
        if (($post['visit_type'] ?? '') === 'service' && ($post['service_company'] ?? '') === '__new__') {
            $newName = trim($post['new_service_company'] ?? '');
            if ($this->registerNewVisitorOption($company_id, $user_id, 'service_company', $newName)) {
                $post['service_company'] = $newName;
            }
        }
    }

    private function buildVisitorLogBaseData($company_id, $visit_type, array $post): array
    {
        $name = trim($post['name'] ?? '');
        if ($name === '') {
            $name = trim($post['emp_temp_name'] ?? '');
        }
        $phone = ($post['phone'] ?? '') ?: ($post['emp_temp_phone'] ?? '');

        return [
            'company_id' => $company_id,
            'visit_type' => $visit_type,
            'location_id' => $post['location_id'] ?? 1,
            'visit_type_other' => $post['visit_type_other'] ?? null,
            'name' => $name !== '' ? $name : null,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $post['email'] ?? null,
            'address' => $post['address'] ?? null,
            'company_name' => $post['company_name'] ?? null,
            'whom_to_meet' => (!empty($post['whom_to_meet']) && $post['whom_to_meet'] != '0') ? (int) $post['whom_to_meet'] : null,
            'purpose' => $post['purpose'] ?? null,
            'purpose_other' => $post['purpose_other'] ?? null,
            'gender' => $post['gender'] ?? null,
            'expected_duration' => $post['expected_duration'] ?? null,
            'vehicle_number' => ($visit_type === 'delivery') ? ($post['vehicle_number_delivery'] ?? null) : ($post['vehicle_number'] ?? null),
            'vehicle_type' => $post['vehicle_type'] ?? null,
            'visitor_idcard_no' => $post['visiting_company_id'] ?? null,
            'visiting_company_id' => $post['visiting_company_id'] ?? null,
            'in_gate_no' => empty($post['in_gate_no']) || $post['in_gate_no'] == '0' ? null : (int) $post['in_gate_no'],
            'image_path' => $post['image_path'] ?? null,
            'is_active' => 0,
            'current_Status' => 0,
        ];
    }

    private function buildVisitorLogServiceDeliveryData(array $post): array
    {
        $idCardNumber = ($post['idcard_number'] ?? '') ?: ($post['doctor_idcard_number'] ?? '');
        return [
            'idcard_type' => (!empty($post['idcard_type']) && $post['idcard_type'] != '0') ? (int) $post['idcard_type'] : null,
            'idcard_number' => $idCardNumber !== '' ? $idCardNumber : null,
            'idcard_type_service' => ($post['idcard_type_service'] ?? '0') == '0' ? null : $post['idcard_type_service'],
            'idcard_number_service' => $post['idcard_number_service'] ?? null,
            'service_company' => $post['service_company'] ?? null,
            'service_person' => $post['service_person'] ?? null,
            'service_phone' => $post['service_phone'] ?? null,
            'authorized_employee' => empty($post['authorized_employee']) || $post['authorized_employee'] == '0' ? null : (int) $post['authorized_employee'],
            'service_tag_enable' => !empty($post['service_tag_enable']) ? 1 : 0,
            'service_tag_count' => (int) ($post['service_tag_count'] ?? 0),
            'new_service_company' => $post['new_service_company'] ?? null,
            'delivery_company_person' => $post['delivery_company_person'] ?? null,
            'delivery_phone' => $post['delivery_phone'] ?? null,
            'deliver_to' => empty($post['deliver_to']) || $post['deliver_to'] == '0' ? null : (int) $post['deliver_to'],
            'delivery_awb_no' => $post['delivery_awb_no'] ?? null,
            'new_delivery_company' => $post['new_delivery_company'] ?? null,
            'delivery_type' => $post['delivery_type'] ?? null,
            'package_count' => isset($post['package_count']) ? (int) $post['package_count'] : 1,
            'security_cleared' => !empty($post['security_cleared']) ? 1 : 0,
            'work_permit_no' => $post['work_permit_no'] ?? null,
            'safety_briefing' => !empty($post['safety_briefing']) ? 1 : 0,
            'service_classification' => $post['service_classification'] ?? null,
        ];
    }

    private function buildVisitorLogEmpDoctorVendorData(array $post): array
    {
        return [
            'employee_code' => $post['employee_code'] ?? null,
            'employee_name' => $post['employee_name'] ?? null,
            'employee_department' => $post['employee_department'] ?? null,
            'emp_temp_enable' => !empty($post['emp_temp_enable']) ? 1 : 0,
            'emp_temp_reason' => $post['emp_temp_reason'] ?? null,
            'emp_temp_name' => $post['emp_temp_name'] ?? null,
            'emp_temp_phone' => $post['emp_temp_phone'] ?? null,
            'emp_temp_whom_to_meet' => (!empty($post['emp_temp_whom_to_meet']) && $post['emp_temp_whom_to_meet'] != '0') ? (int) $post['emp_temp_whom_to_meet'] : null,
            'doctor_name' => $post['doctor_name'] ?? null,
            'doctor_phone' => $post['doctor_phone'] ?? null,
            'doctor_idcard_type' => (!empty($post['doctor_idcard_type']) && $post['doctor_idcard_type'] != '0') ? (int) $post['doctor_idcard_type'] : null,
            'doctor_idcard_number' => $post['doctor_idcard_number'] ?? null,
            'doctor_specialization' => $post['doctor_specialization'] ?? null,
            'is_emergency' => !empty($post['is_emergency']) ? 1 : 0,
            'vendor_supplier_transaction_no' => $post['vendor_supplier_transaction_no'] ?? null,
            'vendor_supplier_entry_type' => $post['vendor_supplier_entry_type'] ?? null,
            'vendor_supplier_vehicle_type' => $post['vendor_supplier_vehicle_type'] ?? null,
            'vendor_supplier_vehicle_number' => $post['vendor_supplier_vehicle_number'] ?? null,
            'vendor_supplier_driver_name' => $post['vendor_supplier_driver_name'] ?? null,
            'vendor_supplier_driver_mobile' => $post['vendor_supplier_driver_mobile'] ?? null,
            'vendor_supplier_purpose' => $post['vendor_supplier_purpose'] ?? null,
            'vendor_supplier_proof_type' => $post['vendor_supplier_proof_type'] ?? null,
            'vendor_supplier_proof_document_no' => $post['vendor_supplier_proof_document_no'] ?? null,
            'vendor_supplier_proof_document_file' => $post['vendor_supplier_proof_document_file'] ?? null,
            'vendor_supplier_document_type' => $post['vendor_supplier_document_type'] ?? null,
            'vendor_supplier_transaction_id' => $post['vendor_supplier_transaction_id'] ?? null,
            'vendor_supplier_document_file' => $post['vendor_supplier_document_file'] ?? null,
            'vendor_supplier_vendor_id' => empty($post['vendor_supplier_vendor_id']) || $post['vendor_supplier_vendor_id'] == '0' ? null : (int) $post['vendor_supplier_vendor_id'],
            'vendor_supplier_department_id' => empty($post['vendor_supplier_department_id']) || $post['vendor_supplier_department_id'] == '0' ? null : (int) $post['vendor_supplier_department_id'],
            'vendor_supplier_loading_start' => !empty($post['vendor_supplier_loading_start']) ? str_replace('T', ' ', $post['vendor_supplier_loading_start']) : null,
            'vendor_supplier_loading_end' => !empty($post['vendor_supplier_loading_end']) ? str_replace('T', ' ', $post['vendor_supplier_loading_end']) : null,
            'vendor_supplier_vehicle_status' => $post['vendor_supplier_vehicle_status'] ?? null,
        ];
    }

    private function buildVisitorLogData($company_id, $visit_type, array $post): array
    {
        return array_merge(
            $this->buildVisitorLogBaseData($company_id, $visit_type, $post),
            $this->buildVisitorLogServiceDeliveryData($post),
            $this->buildVisitorLogEmpDoctorVendorData($post)
        );
    }

    private function buildDeliveryApprovalDetails($company_id, array $logData): array
    {
        global $callConfig;
        $visitTypeLabel = 'Delivery / Courier';
        $deliveryCompany = $logData['delivery_company_person'] ?? '—';
        $gcRow = $callConfig->getRow("SELECT option_label AS genc_detail_name FROM visitor_options WHERE company_id = {$company_id} AND option_type = 'delivery_company' AND (option_value = '" . $callConfig->real_escape_string($deliveryCompany) . "' OR id = '" . $callConfig->real_escape_string($deliveryCompany) . "')");
        if ($gcRow) {
            $deliveryCompany = $gcRow->genc_detail_name;
        }
        $subjectName = $deliveryCompany;
        $deliverToName = '—';
        if (!empty($logData['deliver_to'])) {
            $empRow = $callConfig->getRow(SecurityConstants::SEL_EMPLOYEE_NAME . (int) $logData['deliver_to']);
            if ($empRow) {
                $deliverToName = $empRow->display_name;
            }
        }
        $detailsHtml = '<div class="info-row"><span class="info-label">Delivery Company:</span> ' . htmlspecialchars($deliveryCompany) . '</div>'
            . '<div class="info-row"><span class="info-label">Delivery Phone:</span> ' . htmlspecialchars($logData['delivery_phone'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Deliver To:</span> ' . htmlspecialchars($deliverToName) . '</div>'
            . '<div class="info-row"><span class="info-label">AWB / Ref No:</span> ' . htmlspecialchars($logData['delivery_awb_no'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Package Count:</span> ' . htmlspecialchars($logData['package_count'] ?? '1') . '</div>'
            . '<div class="info-row"><span class="info-label">Vehicle No:</span> ' . htmlspecialchars($logData['vehicle_number'] ?? '—') . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildServiceApprovalDetails($company_id, array $logData): array
    {
        global $callConfig;
        $visitTypeLabel = 'Service Provider / Contractor';
        $serviceCompany = $logData['service_company'] ?? '—';
        $gcRow = $callConfig->getRow("SELECT option_label AS genc_detail_name FROM visitor_options WHERE company_id = {$company_id} AND option_type = 'service_company' AND (option_value = '" . $callConfig->real_escape_string($serviceCompany) . "' OR id = '" . $callConfig->real_escape_string($serviceCompany) . "')");
        if ($gcRow) {
            $serviceCompany = $gcRow->genc_detail_name;
        }
        $subjectName = $logData['service_person'] ?? $serviceCompany;
        $authorizedEmpName = '—';
        if (!empty($logData['authorized_employee'])) {
            $empRow = $callConfig->getRow(SecurityConstants::SEL_EMPLOYEE_NAME . (int) $logData['authorized_employee']);
            if ($empRow) {
                $authorizedEmpName = $empRow->display_name;
            }
        }
        $detailsHtml = '<div class="info-row"><span class="info-label">Service Company:</span> ' . htmlspecialchars($serviceCompany) . '</div>'
            . '<div class="info-row"><span class="info-label">Service Person:</span> ' . htmlspecialchars($logData['service_person'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Phone:</span> ' . htmlspecialchars($logData['service_phone'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Authorized Escort:</span> ' . htmlspecialchars($authorizedEmpName) . '</div>'
            . '<div class="info-row"><span class="info-label">Work Permit No:</span> ' . htmlspecialchars($logData['work_permit_no'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Safety Briefing:</span> ' . ($logData['safety_briefing'] ? 'Completed' : 'Not Done') . '</div>'
            . '<div class="info-row"><span class="info-label">Classification:</span> ' . htmlspecialchars($logData['service_classification'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Vehicle No:</span> ' . htmlspecialchars($logData['vehicle_number'] ?? '—') . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildEmployeeApprovalDetails(array $logData): array
    {
        global $callConfig;
        $visitTypeLabel = 'Employee Temp Access';
        $subjectName = $logData['employee_name'] ?? 'Employee';
        $whomToMeetName = '—';
        if (!empty($logData['emp_temp_whom_to_meet'])) {
            $empRow = $callConfig->getRow(SecurityConstants::SEL_EMPLOYEE_NAME . (int) $logData['emp_temp_whom_to_meet']);
            if ($empRow) {
                $whomToMeetName = $empRow->display_name;
            }
        }
        $detailsHtml = '<div class="info-row"><span class="info-label">Employee Code:</span> ' . htmlspecialchars($logData['employee_code'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Employee Name:</span> ' . htmlspecialchars($logData['employee_name'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Department:</span> ' . htmlspecialchars($logData['employee_department'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Reason:</span> ' . htmlspecialchars($logData['emp_temp_reason'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Whom to Meet:</span> ' . htmlspecialchars($whomToMeetName) . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildDoctorApprovalDetails(array $logData): array
    {
        global $callConfig;
        $visitTypeLabel = 'Doctor Visit';
        $subjectName = $logData['doctor_name'] ?? 'Doctor';
        $whomToMeetName = '—';
        if (!empty($logData['whom_to_meet'])) {
            $empRow = $callConfig->getRow(SecurityConstants::SEL_EMPLOYEE_NAME . (int) $logData['whom_to_meet']);
            if ($empRow) {
                $whomToMeetName = $empRow->display_name;
            }
        }
        $detailsHtml = '<div class="info-row"><span class="info-label">Doctor Name:</span> ' . htmlspecialchars($logData['doctor_name'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Phone:</span> ' . htmlspecialchars($logData['doctor_phone'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Specialization:</span> ' . htmlspecialchars($logData['doctor_specialization'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Emergency?</span> ' . ($logData['is_emergency'] ? 'Yes' : 'No') . '</div>'
            . '<div class="info-row"><span class="info-label">Whom to Meet:</span> ' . htmlspecialchars($whomToMeetName) . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildVendorSupplierApprovalDetails(array $logData): array
    {
        global $callConfig;
        $visitTypeLabel = 'Vendor / Supplier';
        $vendorName = '—';
        if (!empty($logData['vendor_supplier_vendor_id'])) {
            $vendorRow = $callConfig->getRow("SELECT company, display_name FROM vendors WHERE id = " . (int) $logData['vendor_supplier_vendor_id']);
            if ($vendorRow) {
                $vendorName = ($vendorRow->company ? $vendorRow->company . ' - ' : '') . $vendorRow->display_name;
            }
        }
        $subjectName = $logData['vendor_supplier_driver_name'] ?? $vendorName;
        $deptName = '—';
        if (!empty($logData['vendor_supplier_department_id'])) {
            $deptRow = $callConfig->getRow("SELECT department_name FROM departments WHERE id = " . (int) $logData['vendor_supplier_department_id']);
            if ($deptRow) {
                $deptName = $deptRow->department_name;
            }
        }
        $detailsHtml = '<div class="info-row"><span class="info-label">Driver Name:</span> ' . htmlspecialchars($logData['vendor_supplier_driver_name'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Driver Mobile:</span> ' . htmlspecialchars($logData['vendor_supplier_driver_mobile'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Vendor Company:</span> ' . htmlspecialchars($vendorName) . '</div>'
            . '<div class="info-row"><span class="info-label">Department:</span> ' . htmlspecialchars($deptName) . '</div>'
            . '<div class="info-row"><span class="info-label">Vehicle Number:</span> ' . htmlspecialchars($logData['vendor_supplier_vehicle_number'] ?? '—') . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildStandardVisitorApprovalDetails(array $logData): array
    {
        $visitTypeLabel = 'Visitor';
        $visitorName = trim($logData['name'] ?? 'Visitor');
        $visitorPhone = trim($logData['phone'] ?? '—');
        $purpose = trim($logData['purpose'] ?? '—');
        if ($purpose === 'others' && !empty($logData['purpose_other'])) {
            $purpose .= ' (' . $logData['purpose_other'] . ')';
        }
        $subjectName = $visitorName;
        $detailsHtml = '<div class="info-row"><span class="info-label">Visitor Name:</span> ' . htmlspecialchars($visitorName) . '</div>'
            . '<div class="info-row"><span class="info-label">Phone:</span> ' . htmlspecialchars($visitorPhone) . '</div>'
            . '<div class="info-row"><span class="info-label">Company:</span> ' . htmlspecialchars($logData['company_name'] ?? '—') . '</div>'
            . '<div class="info-row"><span class="info-label">Purpose:</span> ' . htmlspecialchars($purpose) . '</div>'
            . '<div class="info-row"><span class="info-label">Vehicle Type:</span> ' . htmlspecialchars(trim($logData['vehicle_type'] ?? 'None')) . '</div>'
            . '<div class="info-row"><span class="info-label">Vehicle No:</span> ' . htmlspecialchars(trim($logData['vehicle_number'] ?? '—')) . '</div>';
        return [$visitTypeLabel, $subjectName, $detailsHtml];
    }

    private function buildHostApprovalEmailHtml($company_id, $visit_type, array $logData, string $hostName, string $approveUrl, string $rejectUrl): array
    {
        if ($visit_type === 'delivery') {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildDeliveryApprovalDetails($company_id, $logData);
        } elseif ($visit_type === 'service') {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildServiceApprovalDetails($company_id, $logData);
        } elseif ($visit_type === 'employee') {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildEmployeeApprovalDetails($logData);
        } elseif ($visit_type === 'doctor') {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildDoctorApprovalDetails($logData);
        } elseif ($visit_type === 'vendor_supplier') {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildVendorSupplierApprovalDetails($logData);
        } else {
            list($visitTypeLabel, $subjectName, $detailsHtml) = $this->buildStandardVisitorApprovalDetails($logData);
        }

        $emailBody = '<!DOCTYPE html><html><head><style>body { font-family: Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 20px; }.card { background-color: #ffffff; border-radius: 8px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }.header { border-bottom: 1px solid #e1e4e8; padding-bottom: 16px; margin-bottom: 20px; }.title { color: #1e293b; font-size: 20px; font-weight: bold; }.visitor-info { background-color: #f8fafc; border-radius: 6px; padding: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; }.info-row { margin-bottom: 8px; font-size: 14px; color: #475569; }.info-label { font-weight: bold; color: #1e293b; display: inline-block; width: 130px; }.btn-container { text-align: center; margin-top: 24px; }.btn { display: inline-block; padding: 12px 24px; border-radius: 6px; font-weight: bold; text-decoration: none; margin: 0 10px; font-size: 14px; }.btn-approve { background-color: #10b981; color: #ffffff !important; }.btn-reject { background-color: #ef4444; color: #ffffff !important; }</style></head><body><div class="card"><div class="header"><div class="title">Visitor Authorization Request</div></div><p>Dear ' . htmlspecialchars($hostName) . ',</p><p>A visitor is waiting at the gate to meet you. Please review the details below and authorize their entry.</p><div class="visitor-info">' . $detailsHtml . '</div><p>Please click one of the buttons below to approve or reject this request:</p><div class="btn-container"><a href="' . $approveUrl . '" class="btn btn-approve">Approve Entry</a><a href="' . $rejectUrl . '" class="btn btn-reject">Reject Entry</a></div></div></body></html>';

        $subject = "Visitor Entry Approval Request [" . $visitTypeLabel . "]: " . $subjectName;
        return [$subject, $emailBody];
    }

    private function sendVisitorHostApprovalEmail($company_id, $visit_type, array $logData, array $post, int $visitor_log_id): void
    {
        global $callConfig;
        if (!empty($post['deliver_to']) && $post['deliver_to'] != '0') {
            $hostId = (int) $post['deliver_to'];
        } elseif (!empty($post['authorized_employee']) && $post['authorized_employee'] != '0') {
            $hostId = (int) $post['authorized_employee'];
        } elseif (!empty($post['whom_to_meet']) && $post['whom_to_meet'] != '0') {
            $hostId = (int) $post['whom_to_meet'];
        } else {
            $hostId = (int) ($post['emp_temp_whom_to_meet'] ?? 0);
        }

        $hostRow = $callConfig->getRow("SELECT display_name, email_id AS user_email FROM employees WHERE id = {$hostId} AND is_active = 0");
        if (!empty($hostRow) && !empty($hostRow->user_email)) {
            $hostEmail = $hostRow->user_email;
            $hostName = $hostRow->display_name;

            $approveparam = array('mode' => 'approveVisitor', 'rid' => $visitor_log_id, 'status' => 'approve', 'dbname' => $callConfig->dbname, 'company_id' => $callConfig->company_id);
            $rejectparam = array('mode' => 'approveVisitor', 'rid' => $visitor_log_id, 'status' => 'reject', 'dbname' => $callConfig->dbname, 'company_id' => $callConfig->company_id);
            $baseUrl = SITEURL . "/security_module_verification_page.php";
            $approveUrl = $baseUrl . SecurityConstants::URL_PARAM . base64_encode(json_encode($approveparam));
            $rejectUrl = $baseUrl . SecurityConstants::URL_PARAM . base64_encode(json_encode($rejectparam));

            list($subject, $emailBody) = $this->buildHostApprovalEmailHtml($company_id, $visit_type, $logData, $hostName, $approveUrl, $rejectUrl);

            if (!class_exists('userClass')) {
                include_once SecurityConstants::USER_CLASS_PATH;
            }
            $userObj = new userClass();
            $emailResult = $userObj->sendmailwithsmtp($hostEmail, $subject, $emailBody, '', '');
            error_log('[VMS Email] Attempted to: ' . $hostEmail . ' | Result: ' . ($emailResult['error_email_msg'] ?? 'unknown') . (!empty($emailResult['error_email_']) ? ' | Error: ' . $emailResult['error_email_'] : ''));
        }
    }

    private function registerLuggageBrandIfNew($company_id, $user_id, string $brandName): void
    {
        global $callConfig;
        if ($brandName === '' || $brandName === 'Other') {
            return;
        }
        $chk = $callConfig->getRow("SELECT id FROM visitor_options WHERE company_id = {$company_id} AND option_type = 'luggage_brand' AND (option_value = '" . $callConfig->real_escape_string($brandName) . "' OR option_label = '" . $callConfig->real_escape_string($brandName) . "') AND is_active = 0");
        if (!$chk) {
            $insOpt = [
                'company_id' => $company_id,
                'option_type' => 'luggage_brand',
                'option_value' => $brandName,
                'option_label' => $brandName,
                'is_active' => 0,
                'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'created_by' => $user_id ?: null,
            ];
            $callConfig->insertRecord('visitor_options', $insOpt);
        }
    }

    private function hasLuggageData(array $row): bool
    {
        foreach (['item_key', 'item_name', 'brand_name', 'qty', 'serial_or_remarks', 'returnable', 'return_date', 'id'] as $k) {
            if (!empty($row[$k])) {
                return true;
            }
        }
        return false;
    }

    private function processSingleLuggageRow(array $row, $company_id, $visitor_log_id, $user_id, array &$submittedIds): void
    {
        global $callConfig;
        if (!$this->hasLuggageData($row)) {
            return;
        }

        $rowId = isset($row['id']) ? (int) $row['id'] : 0;
        $brandName = trim($row['brand_name'] ?? '');
        $this->registerLuggageBrandIfNew($company_id, $user_id, $brandName);

        $lug = [
            'company_id' => $company_id,
            'visitor_log_id' => (int) $visitor_log_id,
            'item_key' => $row['item_key'] ?? null,
            'item_name' => $row['item_name'] ?? null,
            'brand_name' => $row['brand_name'] ?? null,
            'qty' => (int) ($row['qty'] ?? 1),
            'serial_or_remarks' => $row['serial_or_remarks'] ?? null,
            'returnable' => (isset($row['returnable']) && $row['returnable'] === 'no') ? 0 : 1,
            'return_date' => (!empty($row['return_date']) ? $row['return_date'] : null),
            'visitor_type' => 'IN',
            'is_active' => 0,
            'current_Status' => 0,
        ];

        if ($rowId > 0) {
            $submittedIds[] = $rowId;
            $lug += [
                'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'updated_by' => $user_id ?: null,
            ];
            $callConfig->updateRecord('visitor_luggages', $lug, 'id', $rowId);
        } else {
            $lug += [
                'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'created_by' => $user_id ?: null,
            ];
            $callConfig->insertRecord('visitor_luggages', $lug);
        }
    }

    private function upsertVisitorLuggage($company_id, $visitor_log_id, $user_id, array $post): void
    {
        global $callConfig;
        $dbIds = [];
        $rows = $callConfig->getAllRows("SELECT id FROM visitor_luggages WHERE company_id={$company_id} AND visitor_log_id={$visitor_log_id} AND is_active=0");
        foreach ($rows as $r) {
            $dbIds[] = (int) $r->id;
        }

        $submittedIds = [];
        if (!empty($post['luggage']) && is_array($post['luggage'])) {
            foreach ($post['luggage'] as $row) {
                $this->processSingleLuggageRow($row, $company_id, $visitor_log_id, $user_id, $submittedIds);
            }
        }

        if (!empty($dbIds)) {
            $toDelete = array_diff($dbIds, $submittedIds);
            if (!empty($toDelete)) {
                foreach ($toDelete as $id) {
                    $lug = array(
                        'is_active' => 1,
                        'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                        'updated_by' => $user_id ?: null,
                    );
                    $callConfig->updateRecord('visitor_luggages', $lug, 'id', (int) $id);
                }
            }
        }
    }

    private function saveVendorSupplierInwardItems($company_id, $visitor_log_id, $user_id, bool $isUpdate, array $post): void
    {
        global $callConfig;
        if ($isUpdate) {
            $callConfig->execute("UPDATE visitor_luggages SET is_active=1, updated_date=NOW(), updated_by={$user_id} WHERE visitor_log_id={$visitor_log_id} AND visitor_type='IN'");
        }
        if (!empty($post['item_id']) && is_array($post['item_id'])) {
            foreach ($post['item_id'] as $idx => $itemId) {
                if (empty($itemId)) {
                    continue;
                }
                $itemName = $post['item_name'][$idx] ?? '';
                $qty = (float) ($post['received_qty'][$idx] ?? 0);
                $desc = $post['description'][$idx] ?? '';
                $uom = $post['purchase_uom'][$idx] ?? '';

                $lug = [
                    'company_id' => $company_id,
                    'visitor_log_id' => (int) $visitor_log_id,
                    'item_key' => $itemId,
                    'item_name' => $itemName,
                    'brand_name' => $uom,
                    'qty' => $qty,
                    'serial_or_remarks' => $desc,
                    'returnable' => 0,
                    'visitor_type' => 'IN',
                    'is_active' => 0,
                    'current_Status' => 0,
                    'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'created_by' => $user_id ?: null,
                ];
                $callConfig->insertRecord('visitor_luggages', $lug);
            }
        }
    }

    private function upsertVisitorBadge($company_id, $visitor_log_id, $user_id, array $post): void
    {
        global $callConfig;
        $full_code = trim($post['visiting_company_id'] ?? '');
        $gate_id = !empty($post['in_gate_no']) ? (int) $post['in_gate_no'] : 0;

        $oldBadge = $callConfig->getRow("SELECT id FROM visitor_badges WHERE company_id={$company_id} AND visitor_log_id={$visitor_log_id} AND is_active=0 LIMIT 1");

        if ($full_code !== '' && $gate_id > 0) {
            if (empty($oldBadge)) {
                $badge = [
                    'company_id' => $company_id,
                    'gate_id' => $gate_id,
                    'badge_date' => date('Y-m-d'),
                    'visitor_log_id' => (int) $visitor_log_id,
                    'full_code' => $full_code,
                    'issued_at' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'returned_at' => null,
                    'is_active' => 0,
                    'current_status' => 0,
                    'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'created_by' => $user_id ?: null,
                    'location_id' => $post['location_id'] ?? 1,
                ];
                $callConfig->insertRecord('visitor_badges', $badge);
            } else {
                $upd = [
                    'gate_id' => $gate_id,
                    'full_code' => $full_code,
                    'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                    'updated_by' => $user_id ?: null,
                ];
                $callConfig->updateRecord('visitor_badges', $upd, 'id', $oldBadge->id);
            }
        }
    }

    private function softDeleteServiceTagChild($company_id, $user_id, int $childId): void
    {
        global $callConfig;
        $callConfig->updateRecord('visitor_logs', [
            'is_active' => 1,
            'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
            'updated_by' => $user_id ?: null,
        ], 'id', $childId);

        $cb = $callConfig->getRow("SELECT id FROM visitor_badges WHERE company_id={$company_id} AND visitor_log_id={$childId} AND is_active=0 LIMIT 1");
        if (!empty($cb)) {
            $callConfig->updateRecord('visitor_badges', [
                'is_active' => 1,
                'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'updated_by' => $user_id ?: null,
            ], 'id', (int) $cb->id);
        }
    }

    private function insertNewServiceTagChild($company_id, $visitor_log_id, $user_id, $gate_id, $code, $name, array $post): void
    {
        global $callConfig;
        $child = [
            'company_id' => $company_id,
            'visit_type' => 'service',
            'idcard_type_service' => ($post['idcard_type_service'] ?? '0') === '0' ? null : $post['idcard_type_service'],
            'idcard_number_service' => $post['idcard_number_service'] ?? null,
            'service_company' => $post['service_company'] ?? null,
            'service_phone' => $post['service_phone'] ?? null,
            'authorized_employee' => empty($post['authorized_employee']) || $post['authorized_employee'] == '0' ? null : (int) $post['authorized_employee'],
            'service_tag_enable' => !empty($post['service_tag_enable']) ? 1 : 0,
            'service_tag_count' => (int) ($post['service_tag_count'] ?? 0),
            'new_service_company' => $post['new_service_company'] ?? null,
            'service_person' => $name,
            'visitor_idcard_no' => $code,
            'visiting_company_id' => $code,
            'tag_id' => (int) $visitor_log_id,
            'in_datetime' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
            'in_security_id' => $user_id ?: null,
            'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
            'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
            'created_by' => $user_id ?: null,
            'is_active' => 0,
            'current_Status' => 0,
            'location_id' => $post['location_id'] ?? 1,
        ];

        $resChild = $callConfig->insertRecord('visitor_logs', $child);
        $child_id = (int) ($resChild['LASTID'] ?? 0);

        if ($child_id && $code !== '' && $gate_id > 0) {
            $callConfig->insertRecord('visitor_badges', [
                'company_id' => $company_id,
                'gate_id' => $gate_id,
                'badge_date' => date('Y-m-d'),
                'visitor_log_id' => $child_id,
                'full_code' => $code,
                'issued_at' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'returned_at' => null,
                'is_active' => 0,
                'current_status' => 0,
                'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'created_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'created_by' => $user_id ?: null,
            ]);
        }
    }

    private function upsertVisitorServiceTags($company_id, $visitor_log_id, $user_id, bool $isUpdate, array $post): void
    {
        global $callConfig;
        $gate_id = !empty($post['in_gate_no']) ? (int) $post['in_gate_no'] : 0;
        $existingRows = $callConfig->getAllRows(
            "SELECT id, service_person, visiting_company_id, visitor_idcard_no FROM visitor_logs WHERE company_id={$company_id} AND tag_id={$visitor_log_id} AND is_active=0"
        );
        $existingByCode = [];
        if (!empty($existingRows)) {
            foreach ($existingRows as $r) {
                $code = $r->visiting_company_id ?: $r->visitor_idcard_no;
                if ($code !== null) {
                    $existingByCode[$code] = [
                        'id' => (int) $r->id,
                        'service_person' => (string) ($r->service_person ?? '')
                    ];
                }
            }
        }

        $postedByCode = [];
        if (!empty($post['service_tag_enable']) && !empty($post['service_tags']) && is_array($post['service_tags'])) {
            foreach ($post['service_tags'] as $t) {
                $code = trim($t['visitor_id'] ?? '');
                $name = trim($t['name'] ?? '');
                if ($code !== '') {
                    $postedByCode[$code] = $name;
                }
            }
        }

        if ($isUpdate && empty($post['service_tag_enable'])) {
            foreach ($existingByCode as $info) {
                $this->softDeleteServiceTagChild($company_id, $user_id, $info['id']);
            }
            return;
        }

        foreach ($postedByCode as $code => $name) {
            if (isset($existingByCode[$code])) {
                $cid = $existingByCode[$code]['id'];
                $prev = $existingByCode[$code]['service_person'];
                if ($name !== '' && $name !== $prev) {
                    $callConfig->updateRecord('visitor_logs', [
                        'service_person' => $name,
                        'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                        'updated_by' => $user_id ?: null,
                    ], 'id', $cid);
                }
                unset($existingByCode[$code]);
            } else {
                $this->insertNewServiceTagChild($company_id, $visitor_log_id, $user_id, $gate_id, $code, $name, $post);
            }
        }

        if (!empty($existingByCode)) {
            foreach ($existingByCode as $info) {
                $this->softDeleteServiceTagChild($company_id, $user_id, $info['id']);
            }
        }
    }

    private function insertNewVisitorLog($company_id, $user_id, $visit_type, $hasHost, array &$logData, array $post)
    {
        global $callConfig;
        if ($hasHost) {
            $logData['current_Status'] = 10;
            if ($visit_type === 'vendor_supplier') {
                $logData['vendor_supplier_transaction_no'] = null;
            }
        } else {
            $logData['current_Status'] = 0;
        }

        if (!class_exists('userClass')) {
            include_once SecurityConstants::USER_CLASS_PATH;
        }
        $userObj = new userClass();
        $generatedCode = $userObj->toGetApplicationSerialNumber('visitor_logs', $post);
        $logData['visitor_idcard_no'] = $generatedCode;
        $logData['visiting_company_id'] = $generatedCode;
        if ($visit_type === 'vendor_supplier') {
            $logData['vendor_supplier_transaction_no'] = $generatedCode;
        }
        $post['visiting_company_id'] = $generatedCode;

        $now = date(SecurityConstants::DATE_FORMAT_YMD_HIS);
        $logData += [
            'in_datetime' => $now,
            'in_security_id' => $user_id ?: null,
            'status_date' => $now,
            'created_date' => $now,
            'created_by' => $user_id ?: null,
        ];
        $resLog = $callConfig->insertRecord('visitor_logs', $logData);
        $visitor_log_id = $resLog['LASTID'] ?? 0;
        if (!$visitor_log_id) {
            return ['status' => 'error', 'message' => 'Failed to insert visitor_logs', 'error' => $resLog];
        }

        if ($hasHost && $visitor_log_id > 0) {
            $this->sendVisitorHostApprovalEmail($company_id, $visit_type, $logData, $post, (int) $visitor_log_id);
        }

        return (int) $visitor_log_id;
    }

    public function insertVisitorBundle($get, $post)
    {
        global $callConfig;
        $company_id = (int) ($get['company_id'] ?? 0);
        $user_id = (int) ($get['user_id'] ?? 0);

        $rid = (int) ($post['rid'] ?? 0);
        $isUpdate = $rid > 0;
        $visit_type = $post['visit_type'] ?? '';

        $this->normalizeVisitorOptions($company_id, $user_id, $post);
        $logData = $this->buildVisitorLogData($company_id, $visit_type, $post);

        $hasHost = (!empty($post['whom_to_meet']) && $post['whom_to_meet'] != '0') ||
            (!empty($post['emp_temp_whom_to_meet']) && $post['emp_temp_whom_to_meet'] != '0') ||
            (!empty($post['deliver_to']) && $post['deliver_to'] != '0') ||
            (!empty($post['authorized_employee']) && $post['authorized_employee'] != '0');

        if (!$isUpdate) {
            $visitor_log_id = $this->insertNewVisitorLog($company_id, $user_id, $visit_type, $hasHost, $logData, $post);
            if (is_array($visitor_log_id)) {
                return $visitor_log_id;
            }
        } else {
            $visitor_log_id = $rid;
            $logData += [
                'updated_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS),
                'updated_by' => $user_id ?: null,
            ];
            $callConfig->updateRecord('visitor_logs', $logData, 'id', $visitor_log_id);
        }

        $this->upsertVisitorLuggage($company_id, (int) $visitor_log_id, $user_id, $post);

        if ($visit_type === 'vendor_supplier') {
            $this->saveVendorSupplierInwardItems($company_id, (int) $visitor_log_id, $user_id, $isUpdate, $post);
        }

        $this->upsertVisitorBadge($company_id, (int) $visitor_log_id, $user_id, $post);

        if ($visit_type === 'service') {
            $this->upsertVisitorServiceTags($company_id, (int) $visitor_log_id, $user_id, $isUpdate, $post);
        }

        return [
            'status' => 'success',
            'id' => (int) $visitor_log_id,
            'LASTID' => (int) $visitor_log_id,
            'rid' => (int) $visitor_log_id,
            'vendor_supplier_transaction_no' => $logData['vendor_supplier_transaction_no'] ?? null,
            'message' => $isUpdate
                ? 'Updated visitor, luggage, badges, and service details (if any)'
                : 'Inserted visitor, luggage, badges, and service details (if any)',
        ];
    }

    private function updateInLuggageReturns(array $post, $user_id, $nowDT): int
    {
        global $callConfig;
        $inLugUpdated = 0;
        if (!empty($post['luggage']) && is_array($post['luggage'])) {
            foreach ($post['luggage'] as $lugId => $row) {
                $lugId = (int) $lugId;
                $isReturnable = (int) ($row['returnable'] ?? 0) === 1;
                $isReturned = !empty($row['returned_flag']);
                if (!$isReturnable || !$isReturned || $lugId <= 0) {
                    continue;
                }

                $upd = [
                    'actual_return_date' => !empty($row['returned_at']) ? $row['returned_at'] : date('Y-m-d'),
                    'return_remarks' => $row['return_remarks'] ?? null,
                    'current_Status' => 199,
                    'updated_date' => $nowDT,
                    'updated_by' => $user_id ?: null,
                ];
                $callConfig->updateRecord('visitor_luggages', $upd, 'id', $lugId);
                $inLugUpdated++;
            }
        }
        return $inLugUpdated;
    }

    private function updateVendorSupplierLuggage(array $post, $user_id, $nowDT): int
    {
        global $callConfig;
        $count = 0;
        if (!empty($post['vendor_supplier_luggage']) && is_array($post['vendor_supplier_luggage'])) {
            foreach ($post['vendor_supplier_luggage'] as $lugId => $row) {
                $lugId = (int) $lugId;
                if ($lugId <= 0) {
                    continue;
                }
                $left_qty = $row['left_qty'] ?? '';
                $left_remarks = $row['left_remarks'] ?? '';
                $returnRemarks = "Left Qty: {$left_qty} | Details: {$left_remarks}";
                $upd = [
                    'return_remarks' => $returnRemarks,
                    'current_Status' => 199,
                    'updated_date' => $nowDT,
                    'updated_by' => $user_id ?: null,
                ];
                $callConfig->updateRecord('visitor_luggages', $upd, 'id', $lugId);
                $count++;
            }
        }
        return $count;
    }

    private function updatePassReturnBadge(array $post, int $visitor_log_id, $user_id, $nowDT): bool
    {
        global $callConfig;
        if (!empty($post['pass_return']) && !empty($post['pass_return']['returned_flag'])) {
            $pr = $post['pass_return'];
            $returnedAt = !empty($pr['returned_at']) ? str_replace('T', ' ', $pr['returned_at']) : date(SecurityConstants::DATE_FORMAT_YMD_HIS);
            if (strlen($returnedAt) === 16) {
                $returnedAt .= ':00';
            }

            $badgeUpd = [
                'returned_at' => $returnedAt,
                'return_notes' => $pr['remarks'] ?? null,
                'current_status' => 199,
                'updated_date' => $nowDT,
                'updated_by' => $user_id ?: null,
            ];
            $callConfig->updateRecord('visitor_badges', $badgeUpd, 'visitor_log_id', $visitor_log_id);
            return true;
        }
        return false;
    }

    private function insertOutLuggage(array $post, int $company_id, int $visitor_log_id, $user_id, $nowDT): int
    {
        global $callConfig;
        if (empty($post['taking_out']) || $post['taking_out'] !== 'yes' || empty($post['out_luggage'])) {
            return 0;
        }
        $outLugInserted = 0;
        foreach ($post['out_luggage'] as $row) {
            if (!$this->hasLuggageData($row)) {
                continue;
            }

                $lug = [
                    'company_id' => $company_id,
                    'visitor_log_id' => $visitor_log_id,
                    'item_key' => $row['item_key'] ?? null,
                    'item_name' => $row['item_name'] ?? null,
                    'brand_name' => $row['brand_name'] ?? null,
                    'qty' => (int) ($row['qty'] ?? 1),
                    'serial_or_remarks' => $row['serial_or_remarks'] ?? null,
                    'returnable' => $row['returnable'] ?? 0,
                    'return_date' => (!empty($row['return_date']) ? $row['return_date'] : null),
                    'visitor_type' => $row['vistor_type'] ?? '',
                    'is_active' => 0,
                    'current_Status' => 0,
                    'status_date' => $nowDT,
                    'created_date' => $nowDT,
                    'created_by' => $user_id ?: null,
                ];

                $callConfig->insertRecord('visitor_luggages', $lug);
                $outLugInserted++;
            }
        return $outLugInserted;
    }

    public function saveVisitorOut($get, $post)
    {
        global $callConfig;
        $company_id = (int) ($get['company_id'] ?? 0);
        $user_id = (int) ($get['user_id'] ?? 0);
        $nowDT = date(SecurityConstants::DATE_FORMAT_YMD_HIS);

        $visitor_log_id = (int) ($post['pass_return']['visitor_log_id'] ?? ($post['visitor_log_id'] ?? 0));
        if ($visitor_log_id <= 0) {
            return array('status' => 'error', 'message' => 'Missing visitor_log_id');
        }

        $out_datetime = !empty($post['out_datetime']) ? str_replace('T', ' ', $post['out_datetime']) : $nowDT;
        if (strlen($out_datetime) === 16) {
            $out_datetime .= ':00';
        }

        $logData = [
            'out_datetime' => $out_datetime,
            'out_gate_no' => (empty($post['out_gate_no']) || $post['out_gate_no'] == '0') ? null : (int) $post['out_gate_no'],
            'out_security_id' => $user_id ?: null,
            'remarks' => $post['pass_return']['remarks'] ?? null,
            'current_Status' => 199,
            'updated_date' => $nowDT,
            'updated_by' => $user_id ?: null,
        ];
        if (($post['visit_type'] ?? '') === 'vendor_supplier') {
            $logData['vendor_supplier_loading_start'] = !empty($post['vendor_supplier_loading_start']) ? str_replace('T', ' ', $post['vendor_supplier_loading_start']) : null;
            $logData['vendor_supplier_loading_end'] = !empty($post['vendor_supplier_loading_end']) ? str_replace('T', ' ', $post['vendor_supplier_loading_end']) : null;
            $logData['vendor_supplier_vehicle_status'] = $post['vendor_supplier_vehicle_status'] ?? null;
        }
        $callConfig->updateRecord('visitor_logs', $logData, 'id', $visitor_log_id);

        $inLugUpdated = $this->updateInLuggageReturns($post, $user_id, $nowDT);
        $inLugUpdated += $this->updateVendorSupplierLuggage($post, $user_id, $nowDT);
        $badgeUpdated = $this->updatePassReturnBadge($post, $visitor_log_id, $user_id, $nowDT);
        $outLugInserted = $this->insertOutLuggage($post, $company_id, $visitor_log_id, $user_id, $nowDT);

        return array(
            'status' => 'success',
            'message' => 'Visitor checked out',
            'visitor_log_id' => $visitor_log_id,
            'in_luggage_updated' => $inLugUpdated,
            'badge_updated' => $badgeUpdated ? 1 : 0,
            'out_luggage_inserted' => $outLugInserted
        );
    }

    public function approveVisitorByHost($rid, $status)
    {
        global $callConfig;
        $rid = (int) $rid;
        if ($rid <= 0) {
            return ['status' => 'error', 'message' => 'Invalid Request'];
        }
        $row = $callConfig->getRow("SELECT current_Status FROM visitor_logs WHERE id = {$rid} AND is_active = 0");
        if (empty($row)) {
            return ['status' => 'error', 'message' => SecurityConstants::REQ_NOT_FOUND];
        }

        if ($row->current_Status == 0 || $row->current_Status == 199) {
            $res = ['status' => 'success', 'message' => 'Already finalized'];
        } else {
            $newStatus = ($status === 'approve') ? 12 : 11;
            $upd = [
                'current_Status' => $newStatus,
                'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS)
            ];
            $res = $callConfig->updateRecord('visitor_logs', $upd, 'id', $rid);
        }
        return $res;
    }

    public function getVisitorStatus($rid)
    {
        global $callConfig;
        $rid = (int) $rid;
        $row = $callConfig->getRow("SELECT current_Status, whom_to_meet, emp_temp_whom_to_meet, deliver_to, authorized_employee FROM visitor_logs WHERE id = {$rid} AND is_active = 0");
        if (empty($row)) {
            return ['status' => 'error', 'message' => 'Not found'];
        }

        $hostId = (int) ($row->whom_to_meet ?? $row->emp_temp_whom_to_meet ?? $row->deliver_to ?? $row->authorized_employee ?? 0);
        $hostInfo = ['name' => '', 'email' => '', 'phone' => ''];
        if ($hostId > 0) {
            $emp = $callConfig->getRow("SELECT display_name, email_id, phone FROM employees WHERE id = {$hostId} AND is_active = 0");
            if (!empty($emp)) {
                $hostInfo['name'] = $emp->display_name ?? '';
                $hostInfo['email'] = $emp->email_id ?? '';
                $hostInfo['phone'] = $emp->phone ?? '';
            }
        }

        return [
            'status' => 'success',
            'current_Status' => (int) $row->current_Status,
            'host' => $hostInfo
        ];
    }

    public function resendApprovalEmail($rid)
    {
        global $callConfig;
        $rid = (int) $rid;
        $row = $callConfig->getRow("SELECT * FROM visitor_logs WHERE id = {$rid} AND is_active = 0");
        if (empty($row) || (int) ($row->current_Status ?? 0) !== 10) {
            $msg = empty($row) ? 'Visitor log not found' : 'Not in pending approval state';
            return ['status' => 'error', 'message' => $msg];
        }

        $hostId = (int) ($row->whom_to_meet ?? $row->emp_temp_whom_to_meet ?? $row->deliver_to ?? $row->authorized_employee ?? 0);
        $hostRow = $hostId > 0 ? $callConfig->getRow("SELECT display_name, email_id AS user_email FROM employees WHERE id = {$hostId} AND is_active = 0") : null;
        if (empty($hostRow) || empty($hostRow->user_email)) {
            $msg = ($hostId <= 0) ? 'No host assigned to this entry' : 'Host email not found';
            return ['status' => 'error', 'message' => $msg];
        }

        $hostEmail = $hostRow->user_email;
        $hostName = $hostRow->display_name;
        $visitorName = trim($row->name ?? ($row->emp_temp_name ?? 'Visitor'));
        $visitorPhone = trim($row->phone ?? ($row->emp_temp_phone ?? '—'));
        $purpose = trim($row->purpose ?? '—');
        $vehicleType = trim($row->vehicle_type ?? 'None');
        $vehicleNumber = trim($row->vehicle_number ?? '—');

        $approveparam = array('mode' => 'approveVisitor', 'rid' => $rid, 'status' => 'approve', 'dbname' => $callConfig->dbname, 'company_id' => $callConfig->company_id);
        $rejectparam = array('mode' => 'approveVisitor', 'rid' => $rid, 'status' => 'reject', 'dbname' => $callConfig->dbname, 'company_id' => $callConfig->company_id);
        $baseUrl = SITEURL . "/security_module_verification_page.php";
        $approveUrl = $baseUrl . SecurityConstants::URL_PARAM . base64_encode(json_encode($approveparam));
        $rejectUrl = $baseUrl . SecurityConstants::URL_PARAM . base64_encode(json_encode($rejectparam));

        $subject = "[Reminder] Visitor Entry Approval Request: " . $visitorName;
        $emailBody = '<!DOCTYPE html><html><head><style>body { font-family: Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 20px; }.card { background-color: #ffffff; border-radius: 8px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }.header { border-bottom: 1px solid #e1e4e8; padding-bottom: 16px; margin-bottom: 20px; }.title { color: #1e293b; font-size: 20px; font-weight: bold; }.reminder-badge { display:inline-block; background:#f59e0b; color:#fff; font-size:12px; font-weight:bold; padding:3px 10px; border-radius:20px; margin-bottom:12px; }.visitor-info { background-color: #f8fafc; border-radius: 6px; padding: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; }.info-row { margin-bottom: 8px; font-size: 14px; color: #475569; }.info-label { font-weight: bold; color: #1e293b; display: inline-block; width: 120px; }.btn-container { text-align: center; margin-top: 24px; }.btn { display: inline-block; padding: 12px 24px; border-radius: 6px; font-weight: bold; text-decoration: none; margin: 0 10px; font-size: 14px; }.btn-approve { background-color: #10b981; color: #ffffff !important; }.btn-reject { background-color: #ef4444; color: #ffffff !important; }</style></head><body><div class="card"><div class="header"><div class="reminder-badge">⏰ REMINDER</div><div class="title">Visitor Authorization Request</div></div><p>Dear ' . htmlspecialchars($hostName) . ',</p><p>This is a reminder that a visitor is still waiting at the gate for your authorization. Please review and respond.</p><div class="visitor-info"><div class="info-row"><span class="info-label">Visitor Name:</span> ' . htmlspecialchars($visitorName) . '</div><div class="info-row"><span class="info-label">Phone:</span> ' . htmlspecialchars($visitorPhone) . '</div><div class="info-row"><span class="info-label">Purpose:</span> ' . htmlspecialchars($purpose) . '</div><div class="info-row"><span class="info-label">Vehicle Type:</span> ' . htmlspecialchars($vehicleType) . '</div><div class="info-row"><span class="info-label">Vehicle No:</span> ' . htmlspecialchars($vehicleNumber) . '</div></div><p>Please click one of the buttons below to approve or reject this request:</p><div class="btn-container"><a href="' . $approveUrl . '" class="btn btn-approve">Approve Entry</a><a href="' . $rejectUrl . '" class="btn btn-reject">Reject Entry</a></div></div></body></html>';

        if (!class_exists('userClass')) {
            include_once SecurityConstants::USER_CLASS_PATH;
        }
        $userObj = new userClass();
        $sent = $userObj->sendmailwithsmtp($hostEmail, $subject, $emailBody, '', '');

        $hasError = !empty($sent['error_email_']) || (isset($sent['error_email_msg']) && strpos($sent['error_email_msg'], 'could not') !== false);
        return [
            'status' => $hasError ? 'error' : 'success',
            'message' => $hasError ? ('Email failed: ' . ($sent['error_email_'] ?? $sent['error_email_msg'] ?? 'Unknown error')) : ('Reminder email sent to ' . $hostEmail),
            'sent_log' => $hasError ? null : $sent
        ];
    }

    public function manualConfirm($rid)
    {
        global $callConfig;
        $rid = (int) $rid;
        $row = $callConfig->getRow("SELECT current_Status FROM visitor_logs WHERE id = {$rid} AND is_active = 0");
        if (empty($row)) {
            return ['status' => 'error', 'message' => SecurityConstants::REQ_NOT_FOUND];
        }

        $upd = [
            'current_Status' => 12,
            'status_date' => date(SecurityConstants::DATE_FORMAT_YMD_HIS)
        ];
        $callConfig->updateRecord('visitor_logs', $upd, 'id', $rid);
        return ['status' => 'success'];
    }

    private function generateAndAssignVisitorBadge($row, $rid, $gate_id, $company_id, $user_id, $location_id): array
    {
        global $callConfig;
        if (!class_exists('userClass')) {
            include_once SecurityConstants::USER_CLASS_PATH;
        }

        $userObj = new userClass();
        $postary = array('company_id' => $company_id, 'user_id' => $user_id);
        $generatedCode = $userObj->toGetApplicationSerialNumber('visitor_logs', $postary);
        if (empty($generatedCode)) {
            return ['status' => 'error', 'message' => 'Failed to generate serial number'];
        }

        $now = date(SecurityConstants::DATE_FORMAT_YMD_HIS);
        $updLog = [
            'visitor_idcard_no' => $generatedCode,
            'visiting_company_id' => $generatedCode,
            'current_Status' => 0,
            'in_datetime' => $now,
            'status_date' => $now
        ];

        if ($row->visit_type === 'vendor_supplier' && empty(trim($row->vendor_supplier_transaction_no ?? ''))) {
            $updLog['vendor_supplier_transaction_no'] = $generatedCode;
        }

        $callConfig->updateRecord('visitor_logs', $updLog, 'id', $rid);

        $badge = [
            'company_id' => $company_id,
            'gate_id' => $gate_id,
            'badge_date' => date('Y-m-d'),
            'visitor_log_id' => $rid,
            'full_code' => $generatedCode,
            'issued_at' => $now,
            'returned_at' => null,
            'is_active' => 0,
            'current_status' => 0,
            'status_date' => $now,
            'created_date' => $now,
            'created_by' => $user_id ?: null,
            'location_id' => $location_id,
        ];
        $callConfig->insertRecord('visitor_badges', $badge);

        return ['status' => 'success', 'rid' => $rid, 'badge' => $generatedCode];
    }

    public function finalizeCheckin($rid, $gate_id, $company_id, $user_id, $location_id)
    {
        global $callConfig;
        $rid = (int) $rid;
        $row = $callConfig->getRow("SELECT * FROM visitor_logs WHERE id = {$rid} AND is_active = 0");
        if (empty($row)) {
            return ['status' => 'error', 'message' => SecurityConstants::REQ_NOT_FOUND];
        }

        if ($row->current_Status == 0) {
            $badge = $callConfig->getRow("SELECT full_code FROM visitor_badges WHERE visitor_log_id = {$rid} AND is_active = 0 LIMIT 1");
            return ['status' => 'success', 'rid' => $rid, 'badge' => $badge->full_code ?? $row->visiting_company_id];
        }

        return $this->generateAndAssignVisitorBadge($row, $rid, $gate_id, $company_id, $user_id, $location_id);
    }
}

trait SecurityVisitorProfileTrait
{
    public function getReferenceTransactions($get)
    {
        global $callConfig;
        $doc_type = $get['doc_type'] ?? '';
        $company_id = (int) ($get['company_id'] ?? 0);
        if ($company_id <= 0) {
            return [];
        }

        $results = [];
        if (in_array($doc_type, ['purchase_orders', 'purchase_invoices'])) {
            $sql = "SELECT id, transaction_number FROM " . $doc_type . " WHERE company_id = '" . $company_id . "' AND is_active = 0 ORDER BY transaction_number ASC";
            $res = $callConfig->getAllRows($sql);
            if ($res) {
                foreach ($res as $row) {
                    $results[] = (array) $row;
                }
            }
        }
        return $results;
    }

    public function getHistoricalVisitorProfileSearch($get)
    {
        global $callConfig;
        $query = isset($get['query']) ? trim($get['query']) : '';
        $visit_type = isset($get['visit_type']) ? trim($get['visit_type']) : '';
        $company_id = (int) ($get['company_id'] ?? 1);
        $location_id = (int) ($get['location_id'] ?? 1);

        $results = [];
        if ($query !== '' && $visit_type !== '') {
            $q = $callConfig->real_escape_string($query);
            $q = str_replace(['%', '_'], ['\\%', '\\_'], $q);

            $where = [];
            $where[] = "company_id = " . $company_id;
            $where[] = "is_active = 0";

            if ($location_id > 0) {
                $where[] = "location_id = " . $location_id;
            }

            $ors = [];
            $ors[] = "name LIKE '%{$q}%'";
            $ors[] = "phone LIKE '%{$q}%'";
            $ors[] = "visitor_idcard_no LIKE '%{$q}%'";
            $ors[] = "idcard_number LIKE '%{$q}%'";
            $ors[] = "delivery_company_person LIKE '%{$q}%'";
            $ors[] = "delivery_phone LIKE '%{$q}%'";
            $ors[] = "service_person LIKE '%{$q}%'";
            $ors[] = "service_phone LIKE '%{$q}%'";
            $ors[] = "doctor_name LIKE '%{$q}%'";
            $ors[] = "doctor_phone LIKE '%{$q}%'";
            $ors[] = "vendor_supplier_driver_name LIKE '%{$q}%'";
            $ors[] = "vendor_supplier_driver_mobile LIKE '%{$q}%'";
            $ors[] = "vendor_supplier_vehicle_number LIKE '%{$q}%'";
            $where[] = '(' . implode(' OR ', $ors) . ')';
            $whereSql = implode(SecurityConstants::AND_OP, $where);
            $sql = "SELECT * FROM visitor_logs WHERE {$whereSql} ORDER BY id DESC LIMIT 15";
            $res = $callConfig->getAllRows($sql);
            if ($res) {
                foreach ($res as $row) {
                    $results[] = (array) $row;
                }
            }
        }
        return $results;
    }

    private function resolveHostName($log): string
    {
        global $callConfig;
        $whom_to_meet = (int) (($log->whom_to_meet > 0) ? $log->whom_to_meet : ($log->emp_temp_whom_to_meet ?? 0));
        if ($whom_to_meet > 0) {
            $hostRow = $callConfig->getRow(SecurityConstants::SEL_EMPLOYEE_NAME . $whom_to_meet);
            if ($hostRow) {
                return $hostRow->display_name;
            }
        }
        return '—';
    }

    private function resolveVendorAndDeptNames($log): array
    {
        global $callConfig;
        $dept_name = '—';
        $dept_id = (int) ($log->vendor_supplier_department_id ?? 0);
        if ($dept_id > 0) {
            $deptRow = $callConfig->getRow("SELECT department_name FROM departments WHERE id = " . $dept_id);
            if ($deptRow) {
                $dept_name = $deptRow->department_name;
            }
        }

        $vendor_name = '—';
        $vendor_id = (int) ($log->vendor_supplier_vendor_id ?? 0);
        if ($vendor_id > 0) {
            $vendorRow = $callConfig->getRow("SELECT company, display_name FROM vendors WHERE id = " . $vendor_id);
            if ($vendorRow) {
                $vendor_name = ($vendorRow->company ? $vendorRow->company . ' - ' : '') . $vendorRow->display_name;
            }
        }
        return [$dept_name, $vendor_name];
    }

    private function resolveGateName(int $gateId): string
    {
        global $callConfig;
        if ($gateId <= 0) {
            return '—';
        }
        $gateRow = $callConfig->getRow("SELECT name FROM building_gates WHERE id = " . $gateId);
        return $gateRow ? $gateRow->name : '—';
    }

    private function resolveSecurityGuardName(int $userId): string
    {
        global $callConfig;
        if ($userId <= 0) {
            return '—';
        }
        $userRow = $callConfig->getRow("SELECT display_name, user_name FROM application_users WHERE id = " . $userId);
        if ($userRow) {
            return !empty($userRow->display_name) ? $userRow->display_name : ($userRow->user_name ?? '—');
        }
        return '—';
    }

    private function resolveVisitorDetailsReferences($log): array
    {
        $host_name = $this->resolveHostName($log);
        list($dept_name, $vendor_name) = $this->resolveVendorAndDeptNames($log);
        $in_gate_name = $this->resolveGateName((int) ($log->in_gate_no ?? 0));
        $out_gate_name = $this->resolveGateName((int) ($log->out_gate_no ?? 0));
        $in_guard_name = $this->resolveSecurityGuardName((int) ($log->in_security_id ?? 0));
        $out_guard_name = $this->resolveSecurityGuardName((int) ($log->out_security_id ?? 0));

        return [$host_name, $dept_name, $vendor_name, $in_gate_name, $out_gate_name, $in_guard_name, $out_guard_name];
    }

    public function getVisitorDetails($get)
    {
        global $callConfig;
        $return = array();
        $log_id = isset($get['id']) ? (int) $get['id'] : 0;
        if ($log_id <= 0) {
            return array('status' => 'error', 'message' => 'Invalid visitor log ID');
        }

        $log = $callConfig->getRow("SELECT * FROM visitor_logs WHERE id = $log_id AND is_active = 0");
        if (!$log) {
            return array('status' => 'error', 'message' => 'Visitor log record not found');
        }
        $return['log'] = $log;

        list($return['host_name'], $return['dept_name'], $return['vendor_name'], $return['in_gate_name'], $return['out_gate_name'], $return['in_guard_name'], $return['out_guard_name']) = $this->resolveVisitorDetailsReferences($log);

        $luggages = $callConfig->getAllRows("SELECT * FROM visitor_luggages WHERE visitor_log_id = $log_id AND is_active = 0");
        $return['luggages'] = $luggages ?: array();

        $uom_map = [];
        $uoms = $callConfig->getAllRows("SELECT id, uom_name, uom_short_code FROM unit_of_measurements");
        if ($uoms) {
            foreach ($uoms as $u) {
                $uom_map[$u->id] = $u->uom_name . ($u->uom_short_code ? ' (' . $u->uom_short_code . ')' : '');
            }
        }
        $return['uom_map'] = $uom_map;

        return $return;
    }

    public function getVisitorProfilesReportData($get)
    {
        global $callConfig;
        $return = array();
        $company_id = (int) ($get['company_id'] ?? 1);
        $visit_type = $get['visit_type'] ?? 'all';
        $search = $get['search'] ?? '';

        $whr = '';
        if ($visit_type !== 'all' && $visit_type !== '') {
            $whr .= " AND visit_type = '" . $callConfig->real_escape_string($visit_type) . "'";
        }

        $coalescePhone = "COALESCE(NULLIF(phone, ''), NULLIF(emp_temp_phone, ''), NULLIF(service_phone, ''), NULLIF(vendor_supplier_driver_mobile, ''), NULLIF(delivery_phone, ''), NULLIF(doctor_phone, ''))";
        $coalesceName = "COALESCE(NULLIF(name, ''), NULLIF(emp_temp_name, ''), NULLIF(service_person, ''), NULLIF(vendor_supplier_driver_name, ''), NULLIF(delivery_company_person, ''), NULLIF(doctor_name, ''))";

        if ($visit_type === 'vendor_supplier') {
            $search_cond = '';
            if ($search !== '') {
                $s = $callConfig->real_escape_string($search);
                $search_cond = " AND (v.company LIKE '%{$s}%' OR v.display_name LIKE '%{$s}%' OR latest.vendor_supplier_vendor_id = '{$s}')";
            }

            $query = "SELECT
                        latest.id AS latest_log_id,
                        latest.company_id,
                        latest.visit_type,
                        latest.vendor_supplier_vendor_id,
                        latest.email,
                        latest.in_datetime AS latest_in_datetime,
                        COALESCE(NULLIF(v.company, ''), NULLIF(v.display_name, ''), '—') AS company_name,
                        v.display_name AS vendor_name,
                        total_visits.count AS total_visits
                      FROM (
                          SELECT id, company_id, visit_type, vendor_supplier_vendor_id, email, in_datetime
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr}
                      ) latest
                      INNER JOIN (
                          SELECT vendor_supplier_vendor_id AS group_vendor, MAX(id) AS max_id
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr} GROUP BY group_vendor
                      ) grp ON latest.id = grp.max_id
                      INNER JOIN (
                          SELECT vendor_supplier_vendor_id AS group_vendor, COUNT(*) AS count
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr} GROUP BY group_vendor
                      ) total_visits ON latest.vendor_supplier_vendor_id = total_visits.group_vendor
                      LEFT JOIN vendors v ON latest.vendor_supplier_vendor_id = v.id
                      WHERE latest.vendor_supplier_vendor_id IS NOT NULL AND latest.vendor_supplier_vendor_id != 0 {$search_cond}
                      ORDER BY latest.in_datetime DESC";
        } else {
            $search_cond = '';
            if ($search !== '') {
                $s = $callConfig->real_escape_string($search);
                $search_cond = " AND (latest.visitor_name LIKE '%{$s}%' OR latest.visitor_phone LIKE '%{$s}%' OR latest.company_name LIKE '%{$s}%')";
            }

            $query = "SELECT
                        latest.id AS latest_log_id,
                        latest.company_id,
                        latest.visit_type,
                        latest.company_name,
                        latest.email,
                        latest.in_datetime AS latest_in_datetime,
                        latest.visitor_phone,
                        latest.visitor_name,
                        total_visits.count AS total_visits
                      FROM (
                          SELECT id, company_id, visit_type, company_name, email, in_datetime, {$coalescePhone} AS visitor_phone, {$coalesceName} AS visitor_name
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr}
                      ) latest
                      INNER JOIN (
                          SELECT {$coalescePhone} AS group_phone, MAX(id) AS max_id
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr} GROUP BY group_phone
                      ) grp ON latest.id = grp.max_id
                      INNER JOIN (
                          SELECT {$coalescePhone} AS group_phone, COUNT(*) AS count
                          FROM visitor_logs WHERE is_active = 0 AND company_id = {$company_id} {$whr} GROUP BY group_phone
                      ) total_visits ON latest.visitor_phone = total_visits.group_phone
                      WHERE latest.visitor_phone IS NOT NULL AND latest.visitor_phone != '' {$search_cond}
                      ORDER BY latest.in_datetime DESC";
        }

        $return['query'] = $query;
        $rows = $callConfig->getAllRows($query) ?: [];
        $processed = [];
        foreach ($rows as $row) {
            $processed[] = (array) $row;
        }
        $return['visitor_profiles'] = $processed;
        return $return;
    }

    private function fetchEmployeeNameMap(array $ids): array
    {
        global $callConfig;
        if (empty($ids)) {
            return [];
        }
        $idsCsv = implode(',', array_unique($ids));
        $empRows = $callConfig->getAllRows("SELECT id, display_name FROM employees WHERE id IN ({$idsCsv}) AND is_active = 0") ?: [];
        $map = [];
        foreach ($empRows as $emp) {
            $map[(int) $emp->id] = $emp->display_name;
        }
        return $map;
    }

    private function fetchVendorNameMap(array $ids): array
    {
        global $callConfig;
        if (empty($ids)) {
            return [];
        }
        $idsCsv = implode(',', array_unique($ids));
        $vRows = $callConfig->getAllRows("SELECT id, company, display_name FROM vendors WHERE id IN ({$vIdsCsv})") ?: [];
        $map = [];
        foreach ($vRows as $v) {
            $map[(int) $v->id] = ($v->company ? $v->company . ' - ' : '') . $v->display_name;
        }
        return $map;
    }

    private function fetchDepartmentNameMap(array $ids): array
    {
        global $callConfig;
        if (empty($ids)) {
            return [];
        }
        $idsCsv = implode(',', array_unique($ids));
        $dRows = $callConfig->getAllRows("SELECT id, department_name FROM departments WHERE id IN ({$dIdsCsv})") ?: [];
        $map = [];
        foreach ($dRows as $d) {
            $map[(int) $d->id] = $d->department_name;
        }
        return $map;
    }

    private function resolveVisitorProfileMaps(array $logs): array
    {
        $whomToMeetIds = [];
        $vendorIds = [];
        $deptIds = [];

        foreach ($logs as $log) {
            $wtm = (int) ($log->whom_to_meet > 0 ? $log->whom_to_meet : ($log->emp_temp_whom_to_meet ?? 0));
            if ($wtm > 0) {
                $whomToMeetIds[] = $wtm;
            }
            $vId = (int) ($log->vendor_supplier_vendor_id ?? 0);
            if ($vId > 0) {
                $vendorIds[] = $vId;
            }
            $dId = (int) ($log->vendor_supplier_department_id ?? 0);
            if ($dId > 0) {
                $deptIds[] = $dId;
            }
        }

        $employeesMap = $this->fetchEmployeeNameMap($whomToMeetIds);
        $vendorsMap = $this->fetchVendorNameMap($vendorIds);
        $deptsMap = $this->fetchDepartmentNameMap($deptIds);

        return [$employeesMap, $vendorsMap, $deptsMap];
    }

    private function fetchLuggageItemsMap(array $luggagesList): array
    {
        global $callConfig;
        $itemKeys = [];
        foreach ($luggagesList as $lug) {
            if (empty($lug->item_name) && !empty($lug->item_key) && is_numeric($lug->item_key)) {
                $itemKeys[] = (int) $lug->item_key;
            }
        }
        if (empty($itemKeys)) {
            return [];
        }
        $itemKeysCsv = implode(',', array_unique($itemKeys));
        $itemRows = $callConfig->getAllRows("SELECT id, item_name FROM items WHERE id IN ({$itemKeysCsv})") ?: [];
        $itemsMap = [];
        foreach ($itemRows as $itm) {
            $itemsMap[(int) $itm->id] = $itm->item_name;
        }
        return $itemsMap;
    }

    private function resolveLuggageItemName($lug, array $itemsMap): string
    {
        $itemName = trim($lug->item_name ?? '');
        if ($itemName === '' && !empty($lug->item_key) && is_numeric($lug->item_key)) {
            $itemKeyInt = (int) $lug->item_key;
            if (isset($itemsMap[$itemKeyInt]) && trim($itemsMap[$itemKeyInt]) !== '') {
                $itemName = trim($itemsMap[$itemKeyInt]);
            }
        }
        if ($itemName === '') {
            $itemName = trim($lug->serial_or_remarks ?? $lug->description ?? '');
        }
        return $itemName !== '' ? $itemName : '—';
    }

    private function accumulateMaterialQuantities(array &$overallMaterials, $lug, string $itemName, bool $has_checked_out): void
    {
        $brandOrUom = trim($lug->brand_name);
        $key = strtolower($itemName . '||' . $brandOrUom);
        if (!isset($overallMaterials[$key])) {
            $overallMaterials[$key] = [
                'item_name' => $itemName,
                'brand_name' => $brandOrUom,
                'qty_in' => 0,
                'qty_out' => 0
            ];
        }

        $qty = (float) $lug->qty;
        if (strtoupper($lug->visitor_type) === 'IN') {
            $overallMaterials[$key]['qty_in'] += $qty;
            if ($has_checked_out) {
                $remarks = $lug->return_remarks ?? '';
                if (preg_match('/Left Qty:\s*([0-9.]+)/i', $remarks, $matches)) {
                    $left_qty = (float) $matches[1];
                    $overallMaterials[$key]['qty_out'] += max(0.0, $qty - $left_qty);
                } else {
                    $overallMaterials[$key]['qty_out'] += $qty;
                }
            }
        } elseif (strtoupper($lug->visitor_type) === 'OUT') {
            $overallMaterials[$key]['qty_out'] += $qty;
        }
    }

    private function resolveVisitorProfileMaterials(array $logs, array $logIds): array
    {
        global $callConfig;
        $luggagesList = [];
        $uom_map = [];

        if (!empty($logIds)) {
            $logIdsCsv = implode(',', $logIds);
            $luggagesList = $callConfig->getAllRows("SELECT * FROM visitor_luggages WHERE visitor_log_id IN ({$logIdsCsv}) AND is_active = 0") ?: [];

            $uoms = $callConfig->getAllRows("SELECT id, uom_name, uom_short_code FROM unit_of_measurements") ?: [];
            foreach ($uoms as $u) {
                $uom_map[$u->id] = $u->uom_name . ($u->uom_short_code ? ' (' . $u->uom_short_code . ')' : '');
            }
        }

        $itemsMap = $this->fetchLuggageItemsMap($luggagesList);

        $logsCheckoutMap = [];
        foreach ($logs as $log) {
            $logsCheckoutMap[(int) $log->id] = !empty($log->out_datetime) && $log->out_datetime !== '0000-00-00 00:00:00';
        }

        $luggagesByLog = [];
        $overallMaterials = [];

        foreach ($luggagesList as $lug) {
            $itemName = $this->resolveLuggageItemName($lug, $itemsMap);
            $lug->item_name = $itemName;
            $luggagesByLog[(int) $lug->visitor_log_id][] = (array) $lug;

            $this->accumulateMaterialQuantities(
                $overallMaterials,
                $lug,
                $itemName,
                $logsCheckoutMap[(int) $lug->visitor_log_id] ?? false
            );
        }

        return [$luggagesByLog, $overallMaterials, $uom_map];
    }

    public function getVisitorProfileDetails($get)
    {
        global $callConfig;
        $return = array();
        $company_id = (int) ($get['company_id'] ?? 1);
        $phone = $get['phone'] ?? '';
        $vendor_id = isset($get['vendor_id']) ? (int) $get['vendor_id'] : 0;

        if ($phone === '' && $vendor_id <= 0) {
            return ['status' => 'error', 'message' => 'Invalid or empty phone number or vendor id'];
        }

        $coalescePhone = "COALESCE(NULLIF(phone, ''), NULLIF(emp_temp_phone, ''), NULLIF(service_phone, ''), NULLIF(vendor_supplier_driver_mobile, ''), NULLIF(delivery_phone, ''), NULLIF(doctor_phone, ''))";
        $whereClause = ($vendor_id > 0) ? "vl.vendor_supplier_vendor_id = {$vendor_id}" : "{$coalescePhone} = '" . $callConfig->real_escape_string($phone) . "'";

        $queryLogs = "SELECT vl.*,
                      loc.short_name AS location_name,
                      in_gate.name AS in_gate_name,
                      out_gate.name AS out_gate_name,
                      in_user.display_name AS in_guard_name,
                      out_user.display_name AS out_guard_name
                      FROM visitor_logs vl
                      LEFT JOIN locations loc ON vl.location_id = loc.id AND loc.company_id = {$company_id}
                      LEFT JOIN building_gates in_gate ON vl.in_gate_no = in_gate.id
                      LEFT JOIN building_gates out_gate ON vl.out_gate_no = out_gate.id
                      LEFT JOIN application_users in_user ON vl.in_security_id = in_user.id
                      LEFT JOIN application_users out_user ON vl.out_security_id = out_user.id
                      WHERE {$whereClause}
                        AND vl.is_active = 0 AND vl.company_id = {$company_id}
                      ORDER BY " . SecurityConstants::ORDER_BY_IN_DATETIME_DESC . "";

        $logs = $callConfig->getAllRows($queryLogs) ?: [];
        if (empty($logs)) {
            $identifier = $vendor_id > 0 ? "vendor/supplier ID" : "phone number";
            return ['status' => 'error', 'message' => "No logs found for this {$identifier}"];
        }

        $logIds = [];
        foreach ($logs as $log) {
            $logIds[] = (int) $log->id;
        }

        list($employeesMap, $vendorsMap, $deptsMap) = $this->resolveVisitorProfileMaps($logs);
        list($luggagesByLog, $overallMaterials, $uom_map) = $this->resolveVisitorProfileMaterials($logs, $logIds);

        $logsList = [];
        foreach ($logs as $log) {
            $lArr = (array) $log;
            $wtm = (int) ($log->whom_to_meet > 0 ? $log->whom_to_meet : ($log->emp_temp_whom_to_meet ?? 0));
            $lArr['host_name'] = $employeesMap[$wtm] ?? '—';
            $vId = (int) ($log->vendor_supplier_vendor_id ?? 0);
            $lArr['vendor_name'] = $vendorsMap[$vId] ?? '—';
            $dId = (int) ($log->vendor_supplier_department_id ?? 0);
            $lArr['department_name'] = $deptsMap[$dId] ?? '—';
            $lArr['luggages'] = $luggagesByLog[(int) $log->id] ?? [];
            $logsList[] = $lArr;
        }

        $return['logs'] = $logsList;
        $return['overall_materials'] = array_values($overallMaterials);
        $return['uom_map'] = $uom_map;
        return $return;
    }
}

class SecurityClass
{
    use SecurityVisitorQueryTrait;
    use SecurityVisitorEntryTrait;
    use SecurityVisitorProfileTrait;
}
