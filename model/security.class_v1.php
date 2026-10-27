<?php
date_default_timezone_set('Asia/Kolkata');
class SecurityClass
{
    function getVisitorsReportData($get){
        global $callConfig;
        $return = array();
        $from = $get['in_datetime'] ?? '';
        $to = $get['out_datetime'] ?? '';
        $whr = '';
        if ($from != '' && $to != '') {
            $whr = " AND in_datetime >= '{$from}' AND  out_datetime <='{$to}' ";
        }
        $visit_type = strtolower(trim($get['visit_type'] ?? ''));
        $allowed_visit_types = array('visitor', 'delivery', 'service', 'employee', 'doctor', 'others');
        if ($visit_type != '' && in_array($visit_type, $allowed_visit_types, true)) {
            $whr .= " AND visit_type = '{$visit_type}' ";
        }
        $query = "SELECT * FROM visitor_logs WHERE company_id = " . $get['company_id'] . " AND is_active = 0 " . $whr . " ORDER BY in_datetime DESC";
        $return['query'] = $query;
        $data = $callConfig->getAllRows($query);
        $return['visitor_logs_res'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='DELIVERY_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $delivery_company_res = array();
        foreach ($data as $row) {
            $delivery_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['delivery_company_res'] = $delivery_company_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='SERVICE_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $service_company_res = array();
        foreach ($data as $row) {
            $service_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['service_company_res'] = $service_company_res;

        $query = "SELECT id, display_name  FROM employees WHERE company_id = " . $get['company_id'] . "  AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $employees_res = array();
        foreach ($data as $row) {
            $employees_res[$row->id] = $row->display_name;
        }
        $return['employees_res'] = $employees_res;
        return $return;
    }


    function getVisitorsDashboardData($get){
        global $callConfig;
        // ---------- Existing totals (unchanged) ----------
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND location_id='.$get['location_id'];
        }
        
        $queryTotals = "SELECT  
                COUNT(*) AS total_visitors,
                SUM(in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS today_visitors,
                SUM(in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE()) AS yesterday_visitors,
                SUM(in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY) AS last_7days_visitors
            FROM visitor_logs
            WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id";
        $data = (array)$callConfig->getRow($queryTotals);

        $return = [];
        $return['total_visitors']       = (int)($data['total_visitors'] ?? 0);
        $return['today_visitors']       = (int)($data['today_visitors'] ?? 0);
        $return['yesterday_visitors']   = (int)($data['yesterday_visitors'] ?? 0);
        $return['last_7days_visitors']  = (int)($data['last_7days_visitors'] ?? 0);

        // ---------- NEW: per-type breakdown for all 4 periods ----------
        // Definitions:
        // TOTAL   = arrivals (in_datetime) in that period
        // OUT     = departures (out_datetime) in that period
        // IN ONLY = people still inside now (out_datetime IS NULL) and (optionally) who entered in that period
        // For clarity to users, we’ll count IN ONLY as:
        //   - TODAY: in in [today..tomorrow) AND out IS NULL
        //   - YESTERDAY: in in [yesterday..today) AND out IS NULL
        //   - LAST7DAYS: in in [today-7d..tomorrow) AND out IS NULL
        //   - ALL: out IS NULL (irrespective of in time)

        $q = "SELECT
                LOWER(visit_type) AS vt,
                -- TODAY
                SUM((in_datetime >= CURDATE() AND in_datetime  < CURDATE() + INTERVAL 1 DAY) OR (out_datetime >= CURDATE() AND out_datetime < CURDATE() + INTERVAL 1 DAY)) AS t_total,
                SUM(out_datetime >= CURDATE() AND out_datetime < CURDATE() + INTERVAL 1 DAY) AS t_out,
                SUM(in_datetime  >= CURDATE() AND in_datetime  < CURDATE() + INTERVAL 1 DAY AND out_datetime IS NULL) AS t_in_only,
                -- YESTERDAY
                SUM((in_datetime  >= CURDATE() - INTERVAL 1 DAY AND in_datetime  < CURDATE()) OR (out_datetime >= CURDATE() - INTERVAL 1 DAY AND out_datetime < CURDATE())) AS y_total,
                SUM(out_datetime >= CURDATE() - INTERVAL 1 DAY AND out_datetime < CURDATE()) AS y_out,
                SUM(in_datetime  >= CURDATE() - INTERVAL 1 DAY AND in_datetime  < CURDATE() AND out_datetime IS NULL) AS y_in_only,
                -- LAST 7 DAYS
                SUM((in_datetime  >= CURDATE() - INTERVAL 7 DAY AND in_datetime  < CURDATE() + INTERVAL 1 DAY) OR (out_datetime >= CURDATE() - INTERVAL 7 DAY AND out_datetime < CURDATE() + INTERVAL 1 DAY)) AS l7_total,
                SUM(out_datetime >= CURDATE() - INTERVAL 7 DAY AND out_datetime < CURDATE() + INTERVAL 1 DAY) AS l7_out,
                SUM(in_datetime  >= CURDATE() - INTERVAL 7 DAY AND in_datetime  < CURDATE() + INTERVAL 1 DAY AND out_datetime IS NULL) AS l7_in_only,
                -- ALL-TIME
                COUNT(*) AS a_total,
                SUM(out_datetime IS NULL) AS a_in_only,
                SUM(out_datetime IS NOT NULL) AS a_out

            FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id GROUP BY vt";
            $rows = (array)$callConfig->getAllRows($q);

            $types = ['visitor','delivery','service','employee','doctor','others'];
            // seed empty buckets
            $mk = function() { return ['total'=>0,'in_only'=>0,'out'=>0]; };
            $breakdown = [
                'TODAY'      => array_fill_keys($types, $mk()),
                'YESTERDAY'  => array_fill_keys($types, $mk()),
                'LAST7DAYS'  => array_fill_keys($types, $mk()),
                'ALL'        => array_fill_keys($types, $mk()),
            ];

            foreach ($rows as $r) {
                $t = $r->vt ?? 'others';
                if (!in_array($t, $types, true)) $t = 'others';

                $breakdown['TODAY'][$t]['total']   = (int)$r->t_total;
                $breakdown['TODAY'][$t]['out']     = (int)$r->t_out;
                $breakdown['TODAY'][$t]['in_only'] = (int)$r->t_in_only;

                $breakdown['YESTERDAY'][$t]['total']   = (int)$r->y_total;
                $breakdown['YESTERDAY'][$t]['out']     = (int)$r->y_out;
                $breakdown['YESTERDAY'][$t]['in_only'] = (int)$r->y_in_only;

                $breakdown['LAST7DAYS'][$t]['total']   = (int)$r->l7_total;
                $breakdown['LAST7DAYS'][$t]['out']     = (int)$r->l7_out;
                $breakdown['LAST7DAYS'][$t]['in_only'] = (int)$r->l7_in_only;

                $breakdown['ALL'][$t]['total']   = (int)$r->a_total;
                $breakdown['ALL'][$t]['out']     = (int)$r->a_out;
                $breakdown['ALL'][$t]['in_only'] = (int)$r->a_in_only;
            }

            $return['breakdown'] = $breakdown;

            // ---------- NEW (Requested): Recent 5 IN and Recent 5 OUT ----------
        // Minimal, safe fields (you can add more columns if available: name, badge_no, gate_no, etc.)
        $recentInSql  = "SELECT id, visit_type, in_datetime, out_datetime, image_path, visitor_idcard_no, name, phone, email, whom_to_meet, service_company,  service_person, authorized_employee, employee_code, employee_department,  delivery_company_person, delivery_phone, deliver_to FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0  AND out_datetime IS NULL $location_id ORDER BY in_datetime DESC LIMIT 10";
        $recent_in_rows  = $callConfig->getAllRows($recentInSql);
        $return['recent_in'] = $recent_in_rows;

        $recentOutSql = "SELECT id, visit_type, in_datetime, out_datetime, image_path, visitor_idcard_no, name, phone, email, whom_to_meet, service_company,  service_person, authorized_employee, employee_code, employee_department,  delivery_company_person, delivery_phone, deliver_to FROM visitor_logs WHERE company_id = {$get['company_id']} AND is_active = 0 AND out_datetime IS NOT NULL $location_id ORDER BY out_datetime DESC LIMIT 10";
        $recent_out_rows = $callConfig->getAllRows($recentOutSql);
        $return['recent_out'] =  $recent_out_rows;

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id";
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        foreach($recent_out_rows as $row){
            $emp_arr[$row->emp_code] = $row;
        }
        $return['emp_arr'] =  $emp_arr;

        return $return;
    }

    // --- Add/replace your function with this compatible superset ---
    function getVisitorsDataBasedOnDate($get){
        global $callConfig;
        $return = array();

        $cid      = (int)($get['company_id'] ?? 0);
        $day_type = $get['in_datetime'] ?? 'TODAY';        // TODAY | YESTERDAY | LAST7DAYS | ALL
        $q        = trim($get['q'] ?? '');                 // search text
        $offset   = max(0, (int)($get['offset'] ?? 0));    // start row
        $limit    = min(50, max(5, (int)($get['limit'] ?? 10))); // default 10, max 50
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND location_id='.$get['location_id'];
        }

        // ---------- Date filter ---------
        $whr = '';
        if ($day_type == 'TODAY') {
            $whr = " AND in_datetime >= CURDATE() AND in_datetime < CURDATE() + INTERVAL 1 DAY ";
        } else if ($day_type == 'YESTERDAY') {
            $whr = " AND in_datetime >= CURDATE() - INTERVAL 1 DAY AND in_datetime < CURDATE() ";
        } else if ($day_type == 'LAST7DAYS') {
            $whr = " AND in_datetime >= CURDATE() - INTERVAL 7 DAY AND in_datetime < CURDATE() + INTERVAL 1 DAY ";
        } else if ($day_type == 'ALL') {
            $whr = "";
        }

        $query = "SELECT * FROM visitor_logs WHERE company_id = " . $get['company_id'] . " AND is_active = 0 " . $whr . " $location_id ORDER BY out_datetime ASC, in_datetime ASC";
        $data = $callConfig->getAllRows($query);
        $return['visitor_logs_res'] = $data;

        // Build luggage map keyed by visitor_log_id
        $logIds = array_map(function($r){ return (int)$r->id; }, $data);
        $luggageMap = [];

        if (!empty($logIds)) {
            $idsCsv = implode(',', array_unique(array_filter($logIds)));
            $lugSql = "SELECT * FROM visitor_luggages WHERE company_id = {$get['company_id']} AND is_active = 0 AND visitor_log_id IN ($idsCsv) ORDER BY visitor_log_id, id ";
            $lugRows = $callConfig->getAllRows($lugSql);
            foreach ($lugRows as $lr) {
                $luggageMap[$lr->visitor_log_id][] = $lr;
            }
        }
        $return['luggage_map'] = $luggageMap; 


        // ---------- Search ----------
        $searchSql = '';
        if ($q !== '') {
            $like = addcslashes($q, '%_');
            $like = '%' . $like . '%';
            $like = $callConfig->real_escape_string($like);
            $searchSql = " AND (name LIKE '$like'  OR email LIKE '$like'   OR visitor_idcard_no LIKE '$like'  OR employee_code LIKE '$like' OR visit_type LIKE '$like' OR service_person LIKE '$like' OR doctor_name LIKE '$like' OR emp_temp_name LIKE '$like' OR doctor_phone LIKE '$like' OR emp_temp_phone LIKE '$like' OR employee_name LIKE '$like' OR phone LIKE '$like' OR service_phone LIKE '$like' OR delivery_phone LIKE '$like' )";
        }

        // ---------- All Visitors ----------
        $sqlAll = "SELECT *  FROM visitor_logs  WHERE company_id = $cid AND is_active = 0  $whr  $searchSql  $location_id ORDER BY out_datetime ASC, in_datetime ASC LIMIT $offset, $limit";
        $allLogs = $callConfig->getAllRows($sqlAll);

        // ---------- Pending Visitors (in but not out) ----------
        $sqlPending = "SELECT * FROM visitor_logs  WHERE company_id = $cid  AND is_active = 0  AND in_datetime IS NOT NULL  AND out_datetime IS NULL  $whr   $searchSql $location_id ORDER BY in_datetime ASC LIMIT $offset, $limit";
        $pendingLogs = $callConfig->getAllRows($sqlPending);

        // ---------- Completed Visitors (in and out) ----------
        $sqlCompleted = "SELECT *  FROM visitor_logs WHERE company_id = $cid  AND is_active = 0  AND in_datetime IS NOT NULL  AND out_datetime IS NOT NULL  $whr  $searchSql $location_id ORDER BY out_datetime ASC LIMIT $offset, $limit";
        $completedLogs = $callConfig->getAllRows($sqlCompleted);

        $return['visitor_logs_all']       = $allLogs;
        $return['visitor_logs_pending']   = $pendingLogs;
        $return['visitor_logs_completed'] = $completedLogs;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='IDENTITY_ID_CARD' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $id_card_types_res = array();
        foreach ($data as $row) {
            $id_card_types_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['id_card_types_res'] = $id_card_types_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='DELIVERY_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $delivery_company_res = array();
        foreach ($data as $row) {
            $delivery_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['delivery_company_res'] = $delivery_company_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='SERVICE_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $service_company_res = array();
        foreach ($data as $row) {
            $service_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['service_company_res'] = $service_company_res;

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$get['company_id']} AND is_active = 0 $location_id";
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        foreach($recent_out_rows as $row){
            $emp_arr[$row->emp_code] = $row;
        }
        $return['emp_arr'] =  $emp_arr;

        return $return;
    } 


    function getVisitorPassDetails($get){
        global $callConfig;
        $return = array();
        $company_id = (int)($get['company_id'] ?? 0);
        $type     = trim($get['type']     ?? '');   // visitor|delivery|service|employee|others
        $gate_id  = trim($get['gate_id']  ?? '');   // '0' or numeric string
        $searchId = trim($get['id']       ?? '');   // typed ID (ID card / employee id)
        // -----------------------------
        // 1) Build LIKE search on visitor_logs
        // -----------------------------
        $whr = " WHERE v.company_id = {$company_id} AND v.is_active = 0 AND v.current_Status = 0 ";  // only active (not yet out) visits
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
        
        // -----------------------------
        // 2) If a row is found → fetch luggages & badges by visitor_log_id
        // -----------------------------
        $visitor_log_id = (int)($row->id ?? 0);
        if ($visitor_log_id > 0) {
            // Luggages
            $qLug = "SELECT * FROM visitor_luggages WHERE company_id = {$company_id} AND visitor_log_id = {$visitor_log_id} AND is_active = 0 ORDER BY id ASC";
            $return['visitor_luggages_res'] = $callConfig->getAllRows($qLug) ?: array();
            
            // Badges (adjust table/columns if yours differ)
            $qBadge = "SELECT * FROM visitor_badges WHERE company_id = {$company_id} AND visitor_log_id = {$visitor_log_id} AND is_active = 0
                    ORDER BY id DESC";
            $return['visitor_badges_res'] = $callConfig->getRow($qBadge) ?: array();
            
        } else {
            // No match
            $return['visitor_luggages_res'] = array();
            $return['visitor_badges_res']   = array();
        }
        // -----------------------------
        // 3) If type = employee → also fetch employee details
        // -----------------------------
        if (strtolower($type) === 'employee') {
            
            $emp = array();
            // Try by employee_code = front-end ID
            if ($row->employee_code != '') {
                
                $employee_code = $row->employee_code;
                $qEmpByCode = "SELECT id, emp_code, department_id, display_name  FROM employees WHERE company_id = {$company_id} AND is_active = 0 AND emp_code = '{$employee_code}' LIMIT 1";
                $emp = $callConfig->getRow($qEmpByCode);
                
            }
            $return['employee_res'] = $emp ?: array();
        }
        // -----------------------------
        // 4) Gates (direct)
        // -----------------------------
        $qGates = "SELECT id, name FROM building_gates WHERE company_id = {$company_id} AND is_active = 0";
        $return['building_gates_res'] = $callConfig->getAllRows($qGates) ?: array();

                $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='IDENTITY_ID_CARD' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $id_card_types_res = array();
        foreach ($data as $row) {
            $id_card_types_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['id_card_types_res'] = $id_card_types_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='DELIVERY_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $delivery_company_res = array();
        foreach ($data as $row) {
            $delivery_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['delivery_company_res'] = $delivery_company_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='SERVICE_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $service_company_res = array();
        foreach ($data as $row) {
            $service_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['service_company_res'] = $service_company_res;
        return $return;
    }


    function getVisitorInsertRelatedData($get){
        global $callConfig;
        $return = array();
        $company_id = (int)($get['company_id'] ?? 0);
        $rid        = trim($get['rid'] ?? '');
        if(isset($get['vendor_supplier_document_type']) AND $get['vendor_supplier_document_type']!=""){
            $row = $callConfig->getAllRows("SELECT id, transaction_number, transaction_date, party_name FROM ".$get['vendor_supplier_document_type']." WHERE company_id={$company_id} AND (cancel_invoice = 'N' || cancel_invoice IS NULL) AND  current_status=199 AND is_active=0");
            $return['document_transactions'] = $row;
            return $return;
        }
        if($get['transaction_id']!="" AND $get['transaction_table']!=""){
            $userObj=new userClass();
            $transaction_id = $get['transaction_id'];
            $transaction_table = $get['transaction_table'];
            $tables = $userObj->toGetInvoicetabledata($transaction_table);
            
            $rental_invoicessql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$transaction_id."'  AND is_active =0";
            $rental_invoicesres=$callConfig->getRow($rental_invoicessql);
            $return['transaction'] = $rental_invoicesres;
                
            $transaction_itemssql = "SELECT * FROM ".$tables['item']." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction_id."'  AND is_active =0";
            $transaction_itemsres=$callConfig->getAllRows($transaction_itemssql);
            $return['transaction_items'] = $transaction_itemsres;
           
            $transaction_item_taxessql = "SELECT * FROM ".$tables['tax']." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction_id."'  AND is_active =0";
            $transaction_item_taxesres=$callConfig->getAllRows($transaction_item_taxessql);
            $return['transaction_item_taxes'] = $transaction_item_taxesres;

            $unit_of_measurementssql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id = '".$get['company_id']."' AND is_active =0";
            $unit_of_measurementsres=$callConfig->getAllRows($unit_of_measurementssql);
            $unit_of_measurements = array();
            foreach($unit_of_measurementsres as $uom){
                $unit_of_measurements[$uom->id] = $uom;
            }
            $return['unit_of_measurements'] = $unit_of_measurements;
            return $return;
        }
        if ($rid !== '') {
            // existing visitor log
            $row = $callConfig->getRow("SELECT * FROM visitor_logs WHERE company_id={$company_id} AND id={$rid} AND is_active=0");
            $return['visitor_logs_res'] = $row;

            // taged visitors
            $rows = $callConfig->getAllRows("SELECT * FROM visitor_logs WHERE company_id={$company_id} AND tag_id={$rid} AND is_active=0");
            $return['service_tag_children_res'] = $rows;

            // his luggage
            $rows = $callConfig->getAllRows("SELECT * FROM visitor_luggages WHERE company_id={$company_id} AND visitor_log_id={$rid} AND is_active=0");
            $return['visitor_luggages_res'] = $rows;

            // his badge (already issued)
            $badge = $callConfig->getRow("SELECT * FROM visitor_badges WHERE company_id={$company_id} AND visitor_log_id={$rid} AND is_active=0 LIMIT 1");
            $return['visitor_badge_res'] = $badge;
        }

        $query = "SELECT id, display_name, emp_code FROM employees WHERE company_id = " . $get['company_id'] . " AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['employees_res'] = $data;

        $query = "SELECT id, name FROM building_gates WHERE company_id = " . $get['company_id'] . " AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['building_gates_res'] = $data;


        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='VISIT_TYPE' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['visit_type_res'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='IDENTITY_ID_CARD' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['id_card_types_res'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='DELIVERY_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['delivery_company_res'] = $data;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='SERVICE_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['service_company_res'] = $data;

        if ($rid == '' && $get['gate_id'] != ''){
            $return['next_badge'] = $this->getNextBadgeForGate($get);
        }


        $query = "SELECT id, display_name, company FROM vendors WHERE company_id = " . $get['company_id'] . " AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['vendors'] = $data;

        $query = "SELECT id, department_name FROM departments WHERE company_id = " . $get['company_id'] . " AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['departments'] = $data;
        
        $query = "SELECT id, sku, item_name FROM items WHERE company_id = " . $get['company_id'] . " AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $return['items'] = $data;

        return $return;
    }
    function getNextBadgeForGate($get) {

        global $callConfig;
        if ($get['gate_id'] <= 0) {
            return array('status' => 'error', 'message' => 'Missing company or gate');
        }
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND location_id='.$get['location_id'];
        }
        // load gate config
        $cfgSql = "SELECT visit_code, range_start, range_end, padding, is_active FROM gate_badge_configs WHERE company_id = {$get['company_id']} AND  gate_id = {$get['gate_id']}   LIMIT 1";

        $cfg = $callConfig->getRow($cfgSql);
        $cfg = (array)$cfg;
        if (!$cfg || (int)$cfg['is_active'] != 0) {
            return array('status' => 'error', 'message' => 'Gate not configured or deactivated');
        }

        $start   = (int)$cfg['range_start'];
        $end     = (int)$cfg['range_end'];
        $padding = (int)$cfg['padding'];
        $visit_code = trim($cfg['visit_code']) != '' ? $cfg['visit_code'] : 'VISIT';

        // Find MAX numeric usage from visitor_badges.full_code for this gate.
        // We rely on padding: RIGHT(full_code, padding) is the numeric part.
        $maxSql = "SELECT COALESCE(MAX(CAST(RIGHT(full_code, {$padding}) AS UNSIGNED)), " . ($start - 1) . ") AS maxnum
                FROM visitor_badges WHERE company_id = {$get['company_id']} AND gate_id = {$get['gate_id']} AND badge_date = CURDATE()AND is_active=0 $location_id";
        $maxRow = $callConfig->getRow($maxSql);
        $maxRow = (array)$maxRow;
        $maxnum = isset($maxRow['maxnum']) ? (int)$maxRow['maxnum'] : ($start - 1);

        $nextNum = $maxnum + 1;
        if ($nextNum > $end) {
            return array(
                'status'  => 'exhausted',
                'message' => "All IDs used for gate {$get['gate_id']} ({$start}-{$end}).",
                'gate_id' => $get['gate_id'],
                'range_start' => $start,
                'range_end'   => $end,
                'padding'     => $padding
            );
        }
        // Build full code like VISIT003 (prefix fixed as VISIT)
        $fullCode = $visit_code . str_pad((string)$nextNum, $padding, '0', STR_PAD_LEFT);
        return array(
            'status'       => 'success',
            'full_code'    => $fullCode,
            'next_number'  => $nextNum,
            'gate_id'      => $get['gate_id'],
            'range_start'  => $start,
            'range_end'    => $end,
            'padding'      => $padding
        );
    }

    function getEmployeePassDetails($get){
        global $callConfig;
        $return = array();
        $whr = '';
        if (isset($get['emp_code']) &&  $get['emp_code'] != '') {
            $whr .= " AND emp_code = '" . urldecode($get['emp_code']) . "' ";
        }
        if (isset($get['rid']) &&  $get['rid'] != '') {
            $whr .= " AND id = '" . $get['rid'] . "' ";
        }
        $query = "SELECT id, `display_name`, `phone`, `email_id`,  `emp_code`, address, is_active, current_Status FROM employees 
        WHERE  company_id = " . $get['company_id'] . "    $whr  AND is_active = 0 ORDER BY id DESC LIMIT 1";
        $data = $callConfig->getRow($query);
        $return['employee_logs_res'] = $data;

        $query = "SELECT id, name FROM building_gates WHERE company_id = " . $get['company_id'] . " AND is_active = 0";
        $data = $callConfig->getAllRows($query);
        $return['building_gates_res'] = $data;

        return $return;
    }

    function getVisitorsByPhoneAndEmpCode($get){
        global $callConfig;
        $return = array();
        $phone = urldecode($get['phone']) ?? '';
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND location_id='.$get['location_id'];
        }
        if ($phone != '') {
            $query = "SELECT id, `name`, `phone`, `email`, `address`, `whom_to_meet`, `purpose`, `idcard_type`, `idcard_number`, `visit_type`, `image_path`, `visitor_idcard_no`, `in_gate_no`, `in_datetime`, `in_security_id`, `out_security_id`, `out_datetime`, `out_gate_no`, `remarks`, is_active, current_Status FROM visitor_logs 
            WHERE company_id = " . $get['company_id'] . " AND phone = '" . $phone . "' AND is_active = 0 $location_id ORDER BY id DESC LIMIT 5";
            // $return['query'] = $query;
            $data = $callConfig->getAllRows($query);
            $return['visitor_logs_res'] = $data;
        }
        $employee_code = urldecode($get['employee_code']) ?? '';
        if ($employee_code != '') {
            $query = "SELECT emp.id, emp.emp_code, emp.display_name, dept.department_name FROM employees emp, departments dept
            WHERE emp.department_id=dept.id AND emp.company_id = " . $get['company_id'] . " AND emp.emp_code = '" . $employee_code . "' AND emp.is_active = 0  $location_id";
            // $return['query'] = $query;
            $data = $callConfig->getRow($query);
            $return['emp_res'] = $data;
        }

        return $return;
    }


    function makeCode($name,$company_id){
        global $callConfig;
        $row = $callConfig->getRow("SELECT genc_detail_code FROM general_code_details WHERE company_id={$company_id} AND genc_code='{$name}' AND is_active=0 ORDER BY id DESC LIMIT 1");
        $code = $row->genc_detail_code ? (int)$row->genc_detail_code + 1 : 1;
        return $code;
    }
    function insertVisitorBundle($get, $post) {
        global $callConfig;
        $company_id = (int)($get['company_id'] ?? 0);
        $user_id    = (int)($get['user_id'] ?? 0);

        // Are we updating?
        $rid = (int)($post['rid'] ?? 0); // visitor_log_id if editing
        $isUpdate = $rid > 0;

        // ---------- common mapper ----------
        $visit_type = $post['visit_type'] ?? ''; // visitor|delivery|service|employee|others
        
        // -------- normalize "__new__" selections (Delivery / Service) --------
        // DELIVERY: delivery_company_person
        if (($post['visit_type'] ?? '') === 'delivery') {
            if (!empty($post['delivery_company_person']) && $post['delivery_company_person'] === '__new__') {
                $newName = trim($post['new_delivery_company'] ?? '');
                if ($newName !== '') {
                    $genc_detail_code = $this->makeCode('DELIVERY_COMPANY',$company_id);
                    $ins = [
                        'company_id'        => $company_id,
                        'genc_code'         => 'DELIVERY_COMPANY',
                        'genc_detail_name'  => $newName,
                        'genc_detail_code'  => $genc_detail_code,
                        'is_active'         => 0,
                        'current_status'    => 0,
                        'status_date'       => date('Y-m-d H:i:s'),
                        'created_date'      => date('Y-m-d H:i:s'),
                        'created_by'        => $user_id ?: null,
                    ];
                    $res = $callConfig->insertRecord('general_code_details', $ins);
                    $newId = (int)($res['LASTID'] ?? 0);
                    if ($newId > 0) {
                        $post['delivery_company_person'] = (string)$genc_detail_code; // use inserted id as value
                    }
                }
            }
        }

        // SERVICE: service_company
        if (($post['visit_type'] ?? '') === 'service') {
            if (!empty($post['service_company']) && $post['service_company'] === '__new__') {
                $newName = trim($post['new_service_company'] ?? '');
                if ($newName !== '') {
                    $genc_detail_code = $this->makeCode('SERVICE_COMPANY',$company_id);
                    $ins = [
                        'company_id'        => $company_id,
                        'genc_code'         => 'SERVICE_COMPANY',
                        'genc_detail_name'  => $newName,
                        'genc_detail_code'  => $genc_detail_code,
                        'is_active'         => 0,
                        'current_status'    => 0,
                        'status_date'       => date('Y-m-d H:i:s'),
                        'created_date'      => date('Y-m-d H:i:s'),
                        'created_by'        => $user_id ?: null,
                    ];
                    $res = $callConfig->insertRecord('general_code_details', $ins);
                    $newId = (int)($res['LASTID'] ?? 0);
                    if ($newId > 0) {
                        $post['service_company'] = (string)$genc_detail_code; // use inserted id as value
                    }
                }
            }
        }


        $logData = [
            'company_id'            => $company_id,
            'visit_type'            => $visit_type,
            'location_id'           => $post['location_id'] ?? null,
            'visit_type_other'      => $post['visit_type_other'] ?? null,
            'name'                  => trim($post['name'])  ?? '' ?:  trim(($post['emp_temp_name'] ?? ''))  ?: null,
            'phone'                 => ($post['phone'] ?? '')  ?:  ($post['emp_temp_phone'] ?? '') ?: null,
            'email'                 => $post['email'] ?? null,
            'address'               => $post['address'] ?? null,
            'company_name'          => $post['company_name'] ?? null,
            'whom_to_meet'          =>  (!empty($post['whom_to_meet']) && $post['whom_to_meet'] != '0')  ? (int)$post['whom_to_meet']  :  null,
            'purpose'               => $post['purpose'] ?? null,        // official|personal|others
            'purpose_other'         => $post['purpose_other'] ?? null,
            'idcard_type'           => (!empty($post['idcard_type']) && $post['idcard_type'] != '0') ?  (int)$post['idcard_type']  :  null,
            'idcard_number'         => ($post['idcard_number'] ?? "")  ?:  ($post['doctor_idcard_number'] ?? '') ?: null,

            // service
            'idcard_type_service'   => ($post['idcard_type_service'] ?? '0') == '0' ? null : $post['idcard_type_service'],
            'idcard_number_service' => $post['idcard_number_service'] ?? null,
            'service_company'       => $post['service_company'] ?? null,
            'service_person'        => $post['service_person'] ?? null,
            'service_phone'         => $post['service_phone'] ?? null,
            'authorized_employee'   => empty($post['authorized_employee']) || $post['authorized_employee']=='0' ? null : (int)$post['authorized_employee'],
            'service_tag_enable'    => !empty($post['service_tag_enable']) ? 1 : 0,    // NEW
            'service_tag_count'     => (int)($post['service_tag_count'] ?? 0),         // NEW
            'new_service_company'   => $post['new_service_company'] ?? null,

            // delivery
            'delivery_company_person' => $post['delivery_company_person'] ?? null,
            'delivery_phone'          => $post['delivery_phone'] ?? null,
            'deliver_to'              => empty($post['deliver_to']) || $post['deliver_to']=='0' ? null : (int)$post['deliver_to'],
            'delivery_awb_no'         => $post['delivery_awb_no'] ?? null,
            'new_delivery_company'    => $post['new_delivery_company'] ?? null,

            // employee
            'employee_code'    => $post['employee_code'] ?? null,
            'employee_name'    => $post['employee_name'] ?? null,
            'employee_department'    => $post['employee_department'] ?? null,

            'emp_temp_enable'    => $post['emp_temp_enable']  ? 1 : 0 ?? null,
            'emp_temp_reason'    => $post['emp_temp_reason'] ?? null,
            'emp_temp_name'    => $post['emp_temp_name'] ?? null,
            'emp_temp_phone'    => $post['emp_temp_phone'] ?? null,
            'emp_temp_whom_to_meet'    => (!empty($post['emp_temp_whom_to_meet']) && $post['emp_temp_whom_to_meet'] != '0') ? (int)$post['emp_temp_whom_to_meet'] : null,

            //doctor
            'doctor_name'    => $post['doctor_name'] ?? null,
            'doctor_phone'    => $post['doctor_phone'] ?? null,
            'doctor_idcard_type'    => (!empty($post['doctor_idcard_type']) && $post['doctor_idcard_type'] != '0') ? (int)$post['doctor_idcard_type'] : null,
            'doctor_idcard_number'    => $post['doctor_idcard_number'] ?? null,


            // common
            'visitor_idcard_no'     => $post['visiting_company_id'] ?? null,
            'visiting_company_id'   => $post['visiting_company_id'] ?? null,
            'in_gate_no'            => empty($post['in_gate_no']) || $post['in_gate_no']=='0' ? null : (int)$post['in_gate_no'],
            'image_path'            => $post['image_path'] ?? null,
            'is_active'             => 0,
            'current_Status'        => 0,
            'location_id'           => $post['location_id'] ?? 1,
        ];
        $in_datetime_input = $_POST['in_datetime'] ?? '';
        $in_datetime = !empty($in_datetime_input)  ? date('Y-m-d H:i:s', strtotime($in_datetime_input)) : date('Y-m-d H:i:s');
        $logData += ['in_datetime' => $in_datetime];

        if (!$isUpdate) {
            // ---------------------- INSERT ----------------------
            $logData += [
                'system_in_datetime' => date('Y-m-d H:i:s'),
                'in_security_id' => $user_id ?: null,
                'status_date'    => date('Y-m-d H:i:s'),
                'created_date'   => date('Y-m-d H:i:s'),
                'created_by'     => $user_id ?: null,
            ];

            $resLog = $callConfig->insertRecord('visitor_logs', $logData);
            $visitor_log_id = $resLog['LASTID'] ?? 0;
            if (!$visitor_log_id) {
                return ['status'=>'error','message'=>'Failed to insert visitor_logs'];
            }

        } else {
            // ---------------------- UPDATE ----------------------
            $visitor_log_id = $rid;
            $logData += [
                'updated_date' => date('Y-m-d H:i:s'),
                'updated_by'   => $user_id ?: null,
            ];
            $callConfig->updateRecord('visitor_logs', $logData, 'id', $visitor_log_id);
        }

        // ========== LUGGAGE (upsert semantics) ==========
        // STRATEGY:
        //  • Get current active luggage IDs from DB.
        //  • Build a list of submitted IDs (from POST rows that contain [id]).
        //  • Soft-delete (is_active=1) any DB-ID NOT submitted (user removed).
        //  • For submitted rows with id -> (optional) update.
        //  • For submitted rows WITHOUT id -> insert.

        $dbIds = [];
        $rows = $callConfig->getAllRows(
            "SELECT id FROM visitor_luggages WHERE company_id={$company_id} AND visitor_log_id={$visitor_log_id} AND is_active=0"
        );
        foreach ($rows as $r) { $dbIds[] = (int)$r->id; }

        $submittedIds = []; // to know which ones to keep
        if (!empty($post['luggage']) && is_array($post['luggage'])) {
            foreach ($post['luggage'] as $row) {
                // detect if row has an existing id
                $rowId = isset($row['id']) ? (int)$row['id'] : 0;

                // Skip empty rows
                $hasAny = false;
                foreach (['item_key','item_name','brand_name','qty','serial_or_remarks','returnable','return_date','id'] as $k) {
                    if (!empty($row[$k])) { $hasAny = true; break; }
                }
                if (!$hasAny) continue;

                $lug = [
                    'company_id'        => $company_id,
                    'visitor_log_id'    => (int)$visitor_log_id,
                    'item_key'          => $row['item_key'] ?? null,
                    'item_name'         => $row['item_name'] ?? null,
                    'brand_name'        => $row['brand_name'] ?? null,
                    'qty'               => (int)($row['qty'] ?? 1),
                    'serial_or_remarks' => $row['serial_or_remarks'] ?? null,
                    'returnable'        => (isset($row['returnable']) && $row['returnable'] === 'no') ? 0 : 1,
                    'return_date'       => (!empty($row['return_date']) ? $row['return_date'] : null),
                    'visitor_type'       => 'IN',
                    'is_active'         => 0,
                    'current_Status'    => 0,
                ];

                if ($rowId > 0) {
                    // this row existed earlier → mark as "kept"
                    $submittedIds[] = $rowId;

                    // If you want to allow editing existing (brand/qty/remarks/etc),
                    // update it here:
                    $lug += [
                        'updated_date' => date('Y-m-d H:i:s'),
                        'updated_by'   => $user_id ?: null,
                    ];
                    $callConfig->updateRecord('visitor_luggages', $lug, 'id', $rowId);

                } else {
                    // new row → insert
                    $lug += [
                        'status_date'  => date('Y-m-d H:i:s'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by'   => $user_id ?: null,
                    ];
                    $callConfig->insertRecord('visitor_luggages', $lug);
                }
            }
        }

        // Soft-delete any luggage IDs removed by user
        if (!empty($dbIds)) {
            $toDelete = array_diff($dbIds, $submittedIds); // present in DB, not in POST
            if (!empty($toDelete)) {
                foreach ($toDelete as $id) { 
                    $id = (int)$id; 
                    $lug = array(
                        'is_active'    => 1,
                        'updated_date' => date('Y-m-d H:i:s'),
                        'updated_by'   => $user_id ?: null,
                    );
                $callConfig->updateRecord('visitor_luggages', $lug, 'id', $id);
                }
            }
        }

        // ========== BADGE ==========
        $full_code = trim($post['visiting_company_id'] ?? '');
        $gate_id   = !empty($post['in_gate_no']) ? (int)$post['in_gate_no'] : 0;

        // check if there’s already a badge for this visitor
        $oldBadge = $callConfig->getRow("SELECT id FROM visitor_badges WHERE company_id={$company_id} AND visitor_log_id={$visitor_log_id} AND is_active=0 LIMIT 1");

        if ($full_code !== '' && $gate_id > 0) {
            if (empty($oldBadge)) {
                // insert new
                $badge = [
                    'company_id'     => $company_id,
                    'gate_id'        => $gate_id,
                    'badge_date'     => date('Y-m-d'),
                    'visitor_log_id' => (int)$visitor_log_id,
                    'full_code'      => $full_code,
                    'issued_at'      => date('Y-m-d H:i:s'),
                    'returned_at'    => null,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'status_date'    => date('Y-m-d H:i:s'),
                    'created_date'   => date('Y-m-d H:i:s'),
                    'created_by'     => $user_id ?: null,
                    'location_id'    => $post['location_id'] ?? 1
                ];
                $callConfig->insertRecord('visitor_badges', $badge);
            } else {
                // update existing (if changed)
                $upd = [
                    'gate_id'      => $gate_id,
                    'full_code'    => $full_code,
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by'   => $user_id ?: null,
                ];
                $callConfig->updateRecord('visitor_badges', $upd, 'id', $oldBadge->id);
            }
        } 
        
        // ========== SERVICE TAG DETAILS (diff-based upsert) ==========
        if ($visit_type === 'service') {
            // Fetch existing active child tags (current state in DB)
            $existingRows = $callConfig->getAllRows(
                "SELECT id, service_person, visiting_company_id, visitor_idcard_no FROM visitor_logs WHERE company_id={$company_id} AND tag_id={$visitor_log_id} AND is_active=0" );
            $existingByCode = [];
            if (!empty($existingRows)) {
                foreach ($existingRows as $r) {
                    // We store code in both columns; pick whichever is present
                    $code = $r->visiting_company_id ?: $r->visitor_idcard_no;
                    if ($code === null) continue;
                    $existingByCode[$code] = [
                        'id'   => (int)$r->id,
                        'service_person' => (string)($r->service_person ?? '')
                    ];
                }
            }
            // Build posted map: code => name
            $postedByCode = [];
            if (!empty($post['service_tag_enable']) && !empty($post['service_tags']) && is_array($post['service_tags'])) {
                foreach ($post['service_tags'] as $t) {
                    $code = trim($t['visitor_id'] ?? '');
                    $name = trim($t['name'] ?? '');
                    if ($code === '') continue;
                    $postedByCode[$code] = $name;
                }
            }
            // Helper: soft-delete a child log and its badge
            $softDeleteChild = function(int $childId) use ($callConfig, $company_id, $user_id) {
                // child log
                $callConfig->updateRecord('visitor_logs', [
                    'is_active'    => 1,
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by'   => $user_id ?: null,
                ], 'id', $childId);

                // badge (if exists)
                $cb = $callConfig->getRow(
                    "SELECT id FROM visitor_badges WHERE company_id={$company_id} AND visitor_log_id={$childId} AND is_active=0 LIMIT 1"
                );
                if (!empty($cb)) {
                    $callConfig->updateRecord('visitor_badges', [
                        'is_active'    => 1,
                        'updated_date' => date('Y-m-d H:i:s'),
                        'updated_by'   => $user_id ?: null,
                    ], 'id', (int)$cb->id);
                }
            };
            if ($isUpdate && empty($post['service_tag_enable'])) {
                // Feature toggled OFF → remove all existing children
                foreach ($existingByCode as $code => $info) {
                    $softDeleteChild($info['id']);
                }

            } else {
                // UPSERT when enabled (or on insert)
                // 1) Update-kept rows & insert new rows
                foreach ($postedByCode as $code => $name) {
                    if (isset($existingByCode[$code])) {
                        // Keep this child; update name if changed
                        $cid  = $existingByCode[$code]['id'];
                        $prev = $existingByCode[$code]['service_person'];
                        if ($name !== '' && $name !== $prev) {
                            $callConfig->updateRecord('visitor_logs', [
                                'service_person' => $name,
                                'updated_date' => date('Y-m-d H:i:s'),
                                'updated_by'   => $user_id ?: null,
                            ], 'id', $cid);
                        }
                        // mark as processed so it won't be deleted
                        unset($existingByCode[$code]);

                    } else {
                        // New child → insert row + badge
                        // $child = $logData; // copy fields prepared for main
                        unset($child['updated_date'], $child['updated_by']); // safety for update path
                        $child = [
                            'visit_type'            => $visit_type,
                            'idcard_type_service'   => ($post['idcard_type_service'] ?? '0') === '0' ? null : $post['idcard_type_service'],
                            'idcard_number_service' => $post['idcard_number_service'] ?? null,
                            'service_company' => $post['service_company'] ?? null,
                            'service_phone' => $post['service_phone'] ?? null,
                            'authorized_employee'   => empty($post['authorized_employee']) || $post['authorized_employee']=='0' ? null : (int)$post['authorized_employee'],
                            'service_tag_enable'    => !empty($post['service_tag_enable']) ? 1 : 0,    // NEW
                            'service_tag_count'     => (int)($post['service_tag_count'] ?? 0),         // NEW
                            'new_service_company'    => $post['new_service_company'] ?? null,
                            'service_person'       => $name,
                            'visitor_idcard_no'    => $code,
                            'visiting_company_id'  => $code,
                            'tag_id'               => (int)$visitor_log_id,
                            'in_datetime'          => date('Y-m-d H:i:s'),
                            'in_security_id'       => $user_id ?: null,
                            'status_date'          => date('Y-m-d H:i:s'),
                            'created_date'         => date('Y-m-d H:i:s'),
                            'created_by'           => $user_id ?: null,
                            'is_active'            => 0,
                            'current_Status'       => 0,
                            'location_id'    => $post['location_id'] ?? 1
                        ];

                        $resChild = $callConfig->insertRecord('visitor_logs', $child);
                        $child_id = (int)($resChild['LASTID'] ?? 0);

                        if ($child_id && $code !== '' && $gate_id > 0) {
                            $callConfig->insertRecord('visitor_badges', [
                                'company_id'     => $company_id,
                                'location_id'    => $post['location_id'] ?? 1,
                                'gate_id'        => $gate_id,
                                'badge_date'     => date('Y-m-d'),
                                'visitor_log_id' => $child_id,
                                'full_code'      => $code,
                                'issued_at'      => date('Y-m-d H:i:s'),
                                'returned_at'    => null,
                                'is_active'      => 0,
                                'current_status' => 0,
                                'status_date'    => date('Y-m-d H:i:s'),
                                'created_date'   => date('Y-m-d H:i:s'),
                                'created_by'     => $user_id ?: null,
                            ]);
                        }
                    }
                }

                // 2) Any remaining existing (not in posted) → soft-delete
                if (!empty($existingByCode)) {
                    foreach ($existingByCode as $code => $info) {
                        $softDeleteChild($info['id']);
                    }
                }
            }
        }

        return [
            'status'  => 'success',
            'LASTID'  => (int)$visitor_log_id,
            'message' => $isUpdate
                ? 'Updated visitor, luggage, badges, and service details (if any)'
                : 'Inserted visitor, luggage, badges, and service details (if any)',
        ];
    }

    function saveVisitorOut($get, $post){
        global $callConfig;
        $company_id = (int)($get['company_id'] ?? 0);
        $user_id    = (int)($get['user_id'] ?? 0);
        $nowDT = date('Y-m-d H:i:s');
        // ---------------------------
        // 1) Update visitor_logs
        // ---------------------------
        $visitor_log_id = (int)($post['pass_return']['visitor_log_id'] ?? 0);
        if ($visitor_log_id <= 0) {
            return array('status'=>'error','message'=>'Missing visitor_log_id');
        }

        $logData = [
            'out_datetime'    => !empty($post['out_datetime']) ? $post['out_datetime'] : $nowDT,
            'out_gate_no'     => (empty($post['out_gate_no']) || $post['out_gate_no']=='0') ? null : (int)$post['out_gate_no'],
            'out_security_id' => $user_id ?: null,
            'remarks'         => $post['pass_return']['remarks'] ?? null,
            'current_Status'  => 199, // OUT
            'updated_date'    => $nowDT,
            'updated_by'      => $user_id ?: null,
            'system_out_datetime' => $nowDT
        ];
        $callConfig->updateRecord('visitor_logs', $logData, 'id', $visitor_log_id);

        // ---------------------------
        // 2) Process IN-luggage returns
        //    $post['luggage'][<id>] = { id, item_name, returnable, returned_at, return_remarks }
        // ---------------------------
        $inLugUpdated = 0;
        if (!empty($post['luggage']) && is_array($post['luggage'])) {
            foreach ($post['luggage'] as $lugId => $row) {
                $lugId = (int)$lugId; // trust the key as id
                $isReturnable = (int)($row['returnable'] ?? 0) === 1;
                if (!$isReturnable || $lugId <= 0) continue;

                // Build update for returned items
                $upd = [
                    'actual_return_date' => !empty($row['returned_at']) ? $row['returned_at'] : date('Y-m-d'),
                    'return_remarks'     => $row['return_remarks'] ?? null,
                    'current_Status'     => 199, // OUT
                    'updated_date'       => $nowDT,
                    'updated_by'         => $user_id ?: null,
                ];
                $callConfig->updateRecord('visitor_luggages', $upd, 'id', $lugId);
                $inLugUpdated++;
            }
        }

        // ---------------------------
        // 3) Process Visitor Pass / Badge return
        //    $post['pass_return'] = { returned_flag, returned_at, remarks, visitor_log_id, pass_no }
        // ---------------------------
        $badgeUpdated = false;
        if (!empty($post['pass_return']) && !empty($post['pass_return']['returned_flag'])) {
            $pr = $post['pass_return'];
            $returnedAt = !empty($pr['returned_at']) ? $pr['returned_at'] : date('Y-m-d H:i:s');

            $badgeUpd = [
                'returned_at'  => $returnedAt,
                'return_notes' => $pr['remarks'] ?? null,
                'current_Status'     => 199, // OUT
                'updated_date' => $nowDT,
                'updated_by'   => $user_id ?: null,
            ];
            // Update attempt:
            $callConfig->updateRecord('visitor_badges', $badgeUpd, 'visitor_log_id', $visitor_log_id);
            $badgeUpdated = true;
        }

        // ---------------------------
        // 4) Insert OUT-luggage (items being taken out now)
        //    $post['taking_out'] == 'yes' and $post['out_luggage'] array
        // ---------------------------
        $outLugInserted = 0;
        if (!empty($post['taking_out']) && $post['taking_out'] === 'yes' && !empty($post['out_luggage'])) {
            foreach ($post['out_luggage'] as $rowKey => $row) {
                // Skip empty rows
                $hasAny = false;
                foreach (['item_key','item_name','brand_name','qty','serial_or_remarks','returnable','return_date','id'] as $k) {
                    if (!empty($row[$k])) { $hasAny = true; break; }
                }
                if (!$hasAny) continue;


                $lug = [
                    'company_id'        => $company_id,
                    'visitor_log_id'    => (int)$visitor_log_id,
                    'item_key'          => $row['item_key'] ?? null,
                    'item_name'         => $row['item_name'] ?? null,
                    'brand_name'        => $row['brand_name'] ?? null,
                    'qty'               => (int)($row['qty'] ?? 1),
                    'serial_or_remarks' => $row['serial_or_remarks'] ?? null,
                    'returnable'        => $row['returnable'] ?? 0,
                    'return_date'       => (!empty($row['return_date']) ? $row['return_date'] : null),
                    'visitor_type'       => $row['vistor_type'] ?? '',          // <-- OUT (not IN)
                    'is_active'         => 0,              // mark as active row
                    'current_Status'    => 0,              // keep as-is unless you have a specific code
                    'status_date'       => $nowDT,
                    'created_date'      => $nowDT,
                    'created_by'        => $user_id ?: null,
                ];

                $callConfig->insertRecord('visitor_luggages', $lug);
                $outLugInserted++;
            }
        }

        // ---------------------------
        // 5) Done
        // ---------------------------
        return array(
            'status'          => 'success',
            'message'         => 'Visitor checked out',
            'visitor_log_id'  => $visitor_log_id,
            'in_luggage_updated' => $inLugUpdated,
            'badge_updated'      => $badgeUpdated ? 1 : 0,
            'out_luggage_inserted'=> $outLugInserted
        );
    }

    function getVisitorsSearch($get){
        global $callConfig;
        $out = ['visitor_logs_res' => []];

        // Company guard
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        if ($company_id <= 0) {
            return $out;
        }
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND location_id='.$get['location_id'];
        }

        // Inputs
        $text = isset($get['text']) ? trim($get['text']) : '';
        $date = isset($get['date']) ? trim($get['date']) : '';

        // If nothing provided, return empty
        if ($text === '' && $date === '') {
            return $out;
        }

        // Basic sanitization for LIKE (since project uses string concatenation)
        // If you have $callConfig->escape(), use that instead.
        $like = function($s){
            $s = addslashes($s);
            // escape % and _ to avoid wildcard injection
            $s = str_replace(['%', '_'], ['\%','\_'], $s);
            return $s;
        };

        $where = [];
        $where[] = "company_id = {$company_id}";
        $where[] = "is_active = 0";
        $where[] = "current_status = 0";

        // TEXT filter across multiple columns
        if ($text !== '') {
            $t = $like($text);
            $ors = [];
            $ors[] = "name LIKE '%{$t}%'";
            $ors[] = "phone LIKE '%{$t}%'";
            $ors[] = "visitor_idcard_no LIKE '%{$t}%'";
            $ors[] = "idcard_number LIKE '%{$t}%'";
            // if numeric, allow id exact
            if (ctype_digit($text)) {
                $ors[] = "id = " . (int)$text;
            }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        }

        // DATE filter (YYYY-MM-DD)
        if ($date !== '') {
            // basic date check
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $where[] = "DATE(in_datetime) = '{$date}'";
            }
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT * FROM visitor_logs WHERE {$whereSql}  $location_id ORDER BY id DESC";
        $out['debug_sql'] = $sql; // uncomment if you need to debug
        $rows = $callConfig->getAllRows($sql);
        $out['visitor_logs_res'] = $rows ?: [];

        return $out;
    }

    function getVisitorsList($get) {
        global $callConfig;
        $return = array();
        $company_id = (int)($get['company_id'] ?? 0);
        $location_id = '';
        if(isset($get['location_id']) && $get['location_id'] >0){
            $location_id = ' AND vl.location_id='.$get['location_id'];
        }
        // --- sanitize inputs (allowlists) ---
        $period = strtoupper(trim($get['period'] ?? 'TODAY'));
        $type   = strtolower(trim($get['type']   ?? 'visitor'));
        $bucket = strtolower(trim($get['bucket'] ?? 'total'));

        $periodAllow = ['TODAY','YESTERDAY','LAST7DAYS','ALL'];
        $typeAllow   = ['visitor','delivery','service','employee','doctor','others'];
        $bucketAllow = ['total','in_only','out'];

        if (!in_array($period, $periodAllow, true)) $period = 'TODAY';
        if (!in_array($type,   $typeAllow,   true)) $type   = 'visitor';
        if (!in_array($bucket, $bucketAllow, true)) $bucket = 'total';

        $where = [];
        $where[] = "vl.company_id = {$company_id}";
        $where[] = "vl.is_active = 0";
        $where[] = "vl.visit_type = '{$type}'";

        $inWin  = '';
        $outWin = '';
        if ($period === 'TODAY') {
            $inWin  = "vl.in_datetime  >= CURDATE() AND vl.in_datetime  < CURDATE() + INTERVAL 1 DAY";
            $outWin = "vl.out_datetime >= CURDATE() AND vl.out_datetime < CURDATE() + INTERVAL 1 DAY";
        } elseif ($period === 'YESTERDAY') {
            $inWin  = "vl.in_datetime  >= CURDATE() - INTERVAL 1 DAY AND vl.in_datetime  < CURDATE()";
            $outWin = "vl.out_datetime >= CURDATE() - INTERVAL 1 DAY AND vl.out_datetime < CURDATE()";
        } elseif ($period === 'LAST7DAYS') {
            $inWin  = "vl.in_datetime  >= CURDATE() - INTERVAL 7 DAY AND vl.in_datetime  < CURDATE() + INTERVAL 1 DAY";
            $outWin = "vl.out_datetime >= CURDATE() - INTERVAL 7 DAY AND vl.out_datetime < CURDATE() + INTERVAL 1 DAY";
        } else { // ALL
            $inWin  = ""; // all rows
            $outWin = "";
        }

        $orderBy = "vl.in_datetime DESC";

        if ($bucket === 'total') {
            if ($period !== 'ALL') $where[] = '(' . $inWin . ' OR ' . $outWin . ')'; // activity = IN or OUT
            $orderBy = "vl.in_datetime DESC"; 
        } elseif ($bucket === 'out') {
            $where[] = "vl.out_datetime IS NOT NULL";
            if ($period !== 'ALL') $where[] = $outWin;
            $orderBy = "vl.out_datetime DESC";
        } else { // in_only
            $where[] = "vl.out_datetime IS NULL";
            if ($period !== 'ALL') $where[] = $inWin; // for ALL we include everyone still inside
            $orderBy = "vl.in_datetime DESC";
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // --- main SELECT ---
        // Join employees for whom_to_meet_name; join badges for full_code (active one)
        $sql = "SELECT vl.* FROM visitor_logs vl $whereSql $location_id ORDER BY $orderBy ";
        $rows = $callConfig->getAllRows($sql);
        $return['list'] = $rows;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='DELIVERY_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $delivery_company_res = array();
        foreach ($data as $row) {
            $delivery_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['delivery_company_res'] = $delivery_company_res;

        $query = "SELECT genc_detail_code, genc_detail_name  FROM general_code_details WHERE company_id = " . $get['company_id'] . " AND genc_code='SERVICE_COMPANY' AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        $service_company_res = array();
        foreach ($data as $row) {
            $service_company_res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['service_company_res'] = $service_company_res;

        $emp_sql = "SELECT id, emp_code, display_name, phone, email_id FROM employees WHERE company_id = {$get['company_id']} AND is_active = 0 ";
        $recent_out_rows = $callConfig->getAllRows($emp_sql);
        $emp_arr = array();
        foreach($recent_out_rows as $row){
            $emp_arr[$row->emp_code] = $row;
        }
        $return['emp_arr'] =  $emp_arr;


        return $return;
    }
    function getvisitormaterialgatepassdata($get){
        global $callConfig;
        $return = array();
        if($get['rid'] != ''){
            $query = "SELECT * FROM visitor_logs WHERE id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res = $callConfig->getRow($query);
            $return['visitor_data'] =  $res;
        

            $query = "SELECT * FROM visitor_luggages WHERE visitor_log_id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res1 = $callConfig->getAllRows($query);
            $return['visitor_luggage_data'] =  $res1;
            
            $emp_code = $res->employee_code ?? '';
            if($emp_code != ''){
                $query = "SELECT * FROM employees WHERE emp_code='{$emp_code}' AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res = $callConfig->getRow($query);
                $return['employees_data'] =  $res;
            }
        }
        return $return;
    }

    function getvisitorworkpermitdata($get){
        global $callConfig;
        $return = array();
        if($get['rid'] != ''){
            $query = "SELECT * FROM visitor_logs WHERE id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res = $callConfig->getRow($query);
            
            $query = "SELECT * FROM visitor_luggages WHERE visitor_log_id={$get['rid']} AND company_id = {$get['company_id']} AND is_active = 0 ";
            $res1 = $callConfig->getAllRows($query);
            $return['visitor_luggage_data'] =  $res1;

            $tag_id = $res->id ?? 0;
            if($tag_id != '' && $tag_id != 0){
                $query = "SELECT * FROM visitor_logs WHERE tag_id={$tag_id} AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res2 = $callConfig->getAllRows($query);
                $return['visitor_workers_data'] =  $res2;

            }

            $location_id = $res->location_id ?? 0;
            if($location_id != 0){
                $query = "SELECT id,location_name FROM locations WHERE id={$location_id} AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res3 = $callConfig->getRow($query);
                $res->location_name = $res3->location_name;

            }
            $service_company = $res->service_company ?? 0;
            if($service_company != 0){
                $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE genc_code='SERVICE_COMPANY' AND genc_detail_code={$service_company}  AND company_id = {$get['company_id']} AND is_active = 0 ";
                $res4 = $callConfig->getRow($query);
                $res->service_company_name = $res4->genc_detail_name;

            }

            $return['visitor_data'] =  $res;
        }
        return $return;
    }
        function getStationeryAvailableItemsData($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id, name 
            FROM stationery_categories 
            WHERE company_id = ".$get['company_id']." 
            AND is_active = 0
            ORDER BY name ASC";
        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;
        if(isset($get['ajax']) && $get['ajax'] =='Y'){
            $whr = "";
            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr = " AND si.category_id IN (".$get['category_ids'].") ";
            }

            $query = "SELECT 
                    sc.name AS category_name,
                    si.item_name,
                    IFNULL(SUM(sil.in_qty),0) AS received,
                    si.available_qty AS current_stock,
                    IFNULL(SUM(sil.out_qty),0) AS consumed,
                    (IFNULL(SUM(sil.in_qty),0) - IFNULL(SUM(sil.out_qty),0)) AS balance
                FROM stationery_items si
                INNER JOIN stationery_categories sc 
                    ON sc.id = si.category_id AND sc.company_id = si.company_id AND sc.is_active=0
                LEFT JOIN stationery_item_ledger sil 
                    ON sil.item_id = si.id AND sil.company_id = si.company_id 
                    AND sil.is_active = 0
                WHERE si.company_id = ".$get['company_id']."
                AND si.is_active = 0
                ".$whr."
                GROUP BY si.id
                ORDER BY sc.name, si.item_name";

            $data = $callConfig->getAllRows($query);

            $return['available_items_res'] = $data;
        }
        return $return;
    }
    function getEmployeeWiseIssuanceDetails($get){
        global $callConfig;
        $return = array();
        /* Employees for filter */
        $query = "SELECT id, user_id, display_name,emp_code
            FROM employees
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY display_name ASC";

        $data = $callConfig->getAllRows($query);
        $return['employee_res'] = $data;

        /* Report data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){
            $whr = "";
            if(isset($get['employee_ids']) && $get['employee_ids'] != ""){
                $whr = " AND sr.emp_id IN (".$get['employee_ids'].") ";
            }
            $query = "SELECT sr.request_no, sr.other_name, sr.emp_id,
                    emp.display_name AS employee_name, emp.emp_code,
                    si.item_name,
                    sc.qty,
                    sr.request_date,
                    emp2.display_name AS issued_by, emp2.emp_code AS issued_code
                FROM stationery_requests sr
                INNER JOIN stationery_cart sc
                    ON (sc.request_id = sr.id OR sc.request_no = sr.request_no)
                    AND sc.is_active = 0
                INNER JOIN stationery_items si
                    ON si.id = sc.item_id
                    AND si.is_active = 0
                INNER JOIN employees emp
                    ON emp.user_id = sr.user_id
                    AND emp.is_active = 0
                INNER JOIN employees emp2
                    ON emp2.user_id = sr.created_by
                    AND emp2.is_active = 0
                WHERE sr.company_id = ".$get['company_id']."
                AND sr.is_active = 0 AND sr.current_status=199
                ".$whr."
                ORDER BY sr.request_date DESC";

            $data = $callConfig->getAllRows($query);
            $return['issuance_res'] = $data;
            $return['query'] = $query;
        }

        return $return;
    }
    function getMonthlyStationeryReport($get){
        global $callConfig;

        $return = array();

        /* Categories for filter */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";

        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;

        /* Report data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){
            /* Default dates */
            $from_date = $get['from_date'] ?? '';
            $to_date   = $get['to_date'] ?? '';
            if($from_date == ''){
                $from_date = date('Y-m-01');
            }
            if($to_date == ''){
                $to_date = date('Y-m-d');
            }
            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr .= " AND si.category_id IN (".$get['category_ids'].") ";
            }

            $whr .= " AND DATE(sr.request_date) BETWEEN '".$from_date."' AND '".$to_date."' ";

            $query = "SELECT
                    sc.name AS category_name,
                    si.item_name,
                    sca.qty,
                    sr.request_date,
                    emp.display_name AS issued_by, emp.emp_code AS issued_code
                FROM stationery_requests sr
                INNER JOIN stationery_cart sca
                    ON (sca.request_id = sr.id OR sca.request_no = sr.request_no)
                    AND sca.is_active = 0
                INNER JOIN stationery_items si
                    ON si.id = sca.item_id
                    AND si.is_active = 0
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.is_active = 0
                INNER JOIN employees emp
                    ON emp.user_id = sr.created_by
                    AND emp.is_active = 0
                WHERE sr.company_id = ".$get['company_id']."
                AND sr.is_active = 0  AND sr.current_status=199
                ".$whr."
                ORDER BY sr.request_date DESC";

            $data = $callConfig->getAllRows($query);
            $return['monthly_res'] = $data;
            $return['query'] = $query;
        }

        return $return;
    }
    function getStationeryStockBalance($get){
        global $callConfig;

        $return = array();

        /* Categories for filter */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";

        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;

        /* Stock balance data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){

            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr = " AND si.category_id IN (".$get['category_ids'].") ";
            }

            $query = "SELECT
                    sc.name AS category_name,
                    si.item_name,
                    si.available_qty,
                    si.minmum_qty
                FROM stationery_items si
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.company_id = si.company_id
                    AND sc.is_active = 0
                WHERE si.company_id = ".$get['company_id']."
                AND si.is_active = 0
                ".$whr."
                ORDER BY sc.name, si.item_name";

            $data = $callConfig->getAllRows($query);
            $return['stock_res'] = $data;
        }

        return $return;
    }
    function getTotalItemsIssuedCategoryWise($get){
        global $callConfig;

        $return = array();

        /* Categories */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";
        $return['category_res'] = $callConfig->getAllRows($query);

        /* Items */
        $query = "SELECT id, item_name
            FROM stationery_items
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY item_name ASC";
        $return['item_res'] = $callConfig->getAllRows($query);

        /* Report data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){

            $from_date = $get['from_date'] ?? '';
            $to_date   = $get['to_date'] ?? '';

            if($from_date == ''){
                $from_date = date('Y-m-01');
            }
            if($to_date == ''){
                $to_date = date('Y-m-d');
            }

            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr .= " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['item_ids']) && $get['item_ids'] != ""){
                $whr .= " AND si.id IN (".$get['item_ids'].") ";
            }

            $whr .= " AND DATE(sr.request_date)
                      BETWEEN '".$from_date."' AND '".$to_date."' ";

            $query = "SELECT
                    sc.name AS category_name,
                    si.item_name,
                    SUM(sca.qty) AS total_qty
                FROM stationery_requests sr
                INNER JOIN stationery_cart sca
                    ON (sca.request_id = sr.id OR sca.request_no = sr.request_no)
                    AND sca.is_active = 0
                INNER JOIN stationery_items si
                    ON si.id = sca.item_id
                    AND si.is_active = 0
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.is_active = 0 
                WHERE sr.company_id = ".$get['company_id']."
                AND sr.is_active = 0  AND sr.current_status=199
                ".$whr."
                GROUP BY si.id
                ORDER BY sc.name, si.item_name";

            $return['issued_res'] = $callConfig->getAllRows($query);
        }

        return $return;
    }
    // Get employee list for dropdown (emp_code - display_name)
    function getEmployeeListForExitOnly($get) {
        global $callConfig;
        $company_id = (int)($get['company_id'] ?? ($_SESSION['company_id'] ?? 0));
        $sql = "SELECT id, emp_code, display_name FROM employees WHERE company_id = {$company_id} AND is_active = 0 ORDER BY display_name ASC";
        $rows = $callConfig->getAllRows($sql);
         $query = "SELECT id, name FROM building_gates WHERE company_id ={$company_id} AND is_active = 0 ";
        $data = $callConfig->getAllRows($query);
        return ['employees' => $rows, 'building_gates_res' => $data];
    }

    // Employee Exit Only (Work-from-Home Asset Out)
    function employeeExitOnlyOut($get, $post) {
        global $callConfig;
        $company_id = (int)($get['company_id'] ?? ($_SESSION['company_id'] ?? 0));
        $user_id    = (int)($get['user_id'] ?? ($_SESSION['userid'] ?? 0));
        $employee_id = (int)($post['employee_id'] ?? 0);
        if ($employee_id <= 0) {
            return ['status' => 'error', 'message' => 'Employee is required'];
        }
        // Fetch employee details
        $emp = $callConfig->getRow("SELECT emp_code, display_name FROM employees WHERE id = {$employee_id} AND company_id = {$company_id} AND is_active = 0");
        if (!$emp) {
            return ['status' => 'error', 'message' => 'Employee not found'];
        }
        // Prepare visitor_logs row
        $logData = [
            'company_id' => $company_id,
            'visit_type' => 'employee',
            'employee_code' => $emp->emp_code,
            'employee_name' => $emp->display_name,
            'image_path' => $post['image_path'] ?? null,
            'is_active' => 0,
            'current_Status' => 0, // OUT
            'out_datetime' => date('Y-m-d H:i:s'),
            'out_gate_no' => $post['out_gate_no'] ?? null,
            'out_security_id' => $user_id ?: null,
            'status_date' => date('Y-m-d H:i:s'),
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => $user_id ?: null,
        ];
        $resLog = $callConfig->insertRecord('employee_visitor_logs', $logData);
        $visitor_log_id = $resLog['LASTID'] ?? 0;
        if (!$visitor_log_id) {
            return ['status'=>'error','message'=>'Failed to insert employee_visitor_logs'];
        }
        // Insert luggage/items if provided
        if (!empty($post['luggage']) && is_array($post['luggage'])) {
            foreach ($post['luggage'] as $row) {
                $hasAny = false;
                foreach (['item_key','item_name','brand_name','qty','serial_or_remarks','returnable','return_date','id'] as $k) {
                    if (!empty($row[$k])) { $hasAny = true; break; }
                }
                if (!$hasAny) continue;
                $lug = [
                    'company_id'        => $company_id,
                    'visitor_log_id'    => (int)$visitor_log_id,
                    'item_key'          => $row['item_key'] ?? null,
                    'item_name'         => $row['item_name'] ?? null,
                    'brand_name'        => $row['brand_name'] ?? null,
                    'qty'               => (int)($row['qty'] ?? 1),
                    'material_type_id'  => !empty($row['material_type_id']) ? (int)$row['material_type_id'] : null,
                    'item_code'         => $row['item_code'] ?? null,
                    'serial_or_remarks' => $row['serial_or_remarks'] ?? null,
                    'description'       => $row['description'] ?? null,
                    'returnable'        => (isset($row['returnable']) && $row['returnable'] === 'no') ? 0 : 1,
                    'image_path'        => $row['image_path'] ?? null,
                    'return_date'       => (!empty($row['return_date']) ? $row['return_date'] : null),
                    'visitor_type'      => 'OUT',
                    'is_active'         => 0,
                    'current_Status'    => 0,
                    'status_date'       => date('Y-m-d H:i:s'),
                    'created_date'      => date('Y-m-d H:i:s'),
                    'created_by'        => $user_id ?: null,
                ];
                $callConfig->insertRecord('employee_visitor_luggages', $lug);
            }
        }
        return [
            'status'  => 'success',
            'LASTID'  => (int)$visitor_log_id,
            'message' => 'Employee exit-only recorded',
        ];
    }

    function getEmployeeExitOnlyReturnData($get) {
        global $callConfig;
        $company_id  = (int)($get['company_id'] ?? ($_SESSION['company_id'] ?? 0));
        $employee_id    = (int)($get['employee_id'] ?? 0);
        $out_date       = trim($get['out_date'] ?? ($get['date'] ?? ''));

        $empSql = "SELECT id, emp_code, display_name FROM employees WHERE company_id = {$company_id} AND is_active = 0 ORDER BY display_name ASC";
        $employees = $callConfig->getAllRows($empSql);
        $return['employees'] = $employees ?: [];

        $gatesSql = "SELECT id, name FROM building_gates WHERE company_id = {$company_id} AND is_active = 0 ORDER BY name ASC";
        $return['building_gates'] = $callConfig->getAllRows($gatesSql) ?: [];

        $where = [];
        $where[] = "ev.company_id = {$company_id}";
        $where[] = "ev.is_active = 0";
        $where[] = "ev.current_Status = 0";
        $where[] = "ev.out_datetime IS NOT NULL";

        if ($employee_id > 0) {
            $selectedEmployee = $callConfig->getRow("SELECT id, emp_code, display_name FROM employees WHERE id = {$employee_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1");
            $return['selected_employee'] = $selectedEmployee;
            $safe_emp_code = addslashes($selectedEmployee->emp_code);
            $where[] = "ev.employee_code = '{$safe_emp_code}'";
        }

        if ($out_date != '') {
            $where[] = "DATE(ev.out_datetime) = '{$out_date}'";
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT ev.*,  bg.name AS out_gate_name,
                    au.display_name AS out_security_name,
                    COUNT(el.id) AS active_items_count
                FROM employee_visitor_logs ev
                LEFT JOIN building_gates bg 
                    ON bg.id = ev.out_gate_no 
                    AND bg.company_id = ev.company_id 
                    AND bg.is_active = 0
                LEFT JOIN application_users au 
                    ON au.id = ev.out_security_id 
                    AND au.company_id = ev.company_id 
                    AND au.is_active = 0
                LEFT JOIN employee_visitor_luggages el 
                    ON el.visitor_log_id = ev.id 
                    AND el.company_id = ev.company_id 
                    AND el.is_active = 0 
                    AND el.current_Status = 0
                WHERE {$whereSql}
                GROUP BY ev.id
                ORDER BY ev.out_datetime DESC, ev.id DESC";

        $rows = $callConfig->getAllRows($sql);
        $return['list'] = $rows ?: [];
        $return['active_out_count'] = count((array)$return['list']);

        if (count((array)$rows)>0) {
            $logIds = [];
            foreach ($rows as $row) {
                $logIds[] = (int)$row->id;
            }

            $logIds = array_values(array_unique(array_filter($logIds)));
            if (count((array)$logIds)>0) {
                $idsCsv = implode(',', $logIds);
                $lugSql = "SELECT * 
                    FROM employee_visitor_luggages 
                    WHERE company_id = {$company_id} 
                    AND is_active = 0 
                    AND current_Status = 0 AND returnable = 1
                    AND visitor_log_id IN ({$idsCsv})
                    ORDER BY visitor_log_id DESC, id ASC";
                $lugRows = $callConfig->getAllRows($lugSql);
                if (count((array)$lugRows)>0) {
                    foreach ($lugRows as $lugRow) {
                        $return['luggage_map'][$lugRow->visitor_log_id][] = $lugRow;
                    }
                }
            }
        }
        $return['status'] = 'success';

        return $return;
    }

    function saveEmployeeExitOnlyReturn($get, $post) {
        global $callConfig;
        $company_id = (int)($post['company_id']  ?? 0);
        $user_id = (int)($get['user_id']  ?? 0);
        $visitor_log_id = (int)($post['visitor_log_id'] ?? 0);
        $return_gate_no = (int)($post['return_gate_no'] ?? 0);
        $overall_remarks = trim($post['overall_remarks'] ?? '');
        $item_ids = $post['item_ids'] ?? [];
        $item_remarks = $post['item_remarks'] ?? [];

        if ($return_gate_no <= 0) {
            return ['status' => 'error', 'message' => 'Please select a return gate'];
        }

        $logRecord = $callConfig->getRow("SELECT id, employee_code FROM employee_visitor_logs WHERE id = {$visitor_log_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1");
        if (!$logRecord) {
            return ['status' => 'error', 'message' => 'Visitor log record not found'];
        }

        $now = date('Y-m-d H:i:s');

        $updateLog = [
            'return_date_time' => $now,
            'return_gate_no' => $return_gate_no,
            'return_security_id' => $user_id,
            'remarks' => $overall_remarks,
            'current_Status' => 199,
            'updated_date' => $now,
            'updated_by' => $user_id,
        ];

        $updateLogResult = $callConfig->updateRecord('employee_visitor_logs', $updateLog, 'id', $visitor_log_id);
        if (!$updateLogResult) {
            return ['status' => 'error', 'message' => 'Failed to update visitor log record'];
        }

        if (count((array)$item_ids) > 0) {
            foreach ($item_ids as $item_id) {
                $item_id = (int)$item_id;
                if ($item_id <= 0) continue;

                $itemRemark = isset($item_remarks[$item_id]) ? trim($item_remarks[$item_id]) : '';

                $updateItem = [
                    'actual_return_date' => date('Y-m-d'),
                    'return_remarks' => $itemRemark,
                    'current_Status' => 199,
                    'updated_date' => $now,
                    'updated_by' => $user_id,
                ];

                $callConfig->updateRecord('employee_visitor_luggages', $updateItem, 'id', $item_id);
            }
        }

        return ['status' => 'success', 'message' => 'Return processed successfully', 'visitor_log_id' => $visitor_log_id];
    }
    function getTextToDyanmicSearching($get, $post){
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $company_id = $post['company_id'];
        $value = $post['value'];
        $serachtype = $post['serachtype'];
        $transaction_table= $post['transaction_table'];
        if($serachtype == 'vendortransaction'){
            $sql = "SELECT id, transaction_number, transaction_date FROM  ".$transaction_table." WHERE company_id = ".$company_id." AND transaction_number LIKE '%".$value."%' AND is_active = 0";
           $sqlres = $callConfig->getAllRows($sql);
        }
        $return['result_sql'] = $sql;
        $return['result'] = $sqlres;
        return $return;
    }


}
