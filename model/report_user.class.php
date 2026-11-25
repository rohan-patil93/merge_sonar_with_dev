<?php

interface ReportConstants {
    public const SQL_WHERE_COMPANY_ID = " WHERE company_id = '";
    public const SQL_AND_APP_TABLE = "' AND application_table_name = '";
    public const SQL_AND_IS_ACTIVE_0 = "' AND is_active = 0";
    public const SQL_AND_QUOTE = "' AND '";
    public const SQL_AND_TRANSA = " AND transa.";
    public const SQL_SELECT_DMT = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '";
    public const SQL_AND_TRANSA_REF_TYPE = " AND transa.ref_type = '";
    public const SQL_AND_TRANSA_REF_ID = "' AND transa.ref_id = '";
    public const SQL_AND_TRANSA_TX_DATE = " AND transa.transaction_date BETWEEN '";
    public const SQL_ORDER_BY_TX = " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";
    public const SQL_TRANSA_WHERE_CO = " transa WHERE transa.company_id = '";
    public const SQL_AND_TRANSA_IS_ACTIVE = "' AND transa.is_active = 0 ";
    public const SQL_AND_TX_ID = "' AND transaction_id = '";
    public const SQL_SELECT_TAX_NAME_AMOUNT = "SELECT tax_name, tax_amount FROM ";
    public const SQL_AND_TX_ITEM_ID = "' AND transaction_item_id = '";
    public const SQL_VISITOR_IDCARD_LIKE = "%' OR vl.visitor_idcard_no LIKE '%";
    public const SQL_VISITOR_BADGE_LIKE = "%' OR EXISTS (SELECT 1 FROM visitor_badges vb3 WHERE vb3.visitor_log_id = vl.id AND vb3.full_code LIKE '%";
    public const SQL_VISITOR_ORDER_BY_IN = "\n            ORDER BY vl.in_datetime DESC";
    public const SQL_CLOSE_PAREN_2 = "%')) ";
}

trait ReportCouriersTrait {
    public function getCourierReportFilterLookups($get){
        global $callConfig;
        $return = array();
        $return['locations'] = array();
        $return['employees'] = array();
        $return['statuses'] = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == ''){
            return $return;
        }

        $locsql = "SELECT id, location_name FROM locations" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . " ORDER BY location_name ASC";
        $locres = $callConfig->getAllRows($locsql);
        $return['locations'] = $locres;

        $empsql = "SELECT id, display_name, emp_code FROM employees" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . " ORDER BY display_name ASC";
        $empres = $callConfig->getAllRows($empsql);
        $return['employees'] = $empres;

        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . "' AND genc_code='COURIER_STATUS' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['statuses'] = $res;

        return $return;
    }

    public function buildCouriersReportWhere($get, $type){
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $where = " company_id = '".$company_id."' AND is_active = 0 AND courier_type = '".$type."' AND courier_date >= '".$from_date."' AND courier_date <= '".$to_date."' ";

        if(!empty($get['location_id'])){
            $where .= " AND location_id = '".$get['location_id']."' ";
        }
        if(!empty($get['employee_id'])){
            $where .= " AND requested_by = '".$get['employee_id']."' ";
        }
        if(!empty($get['status_id'])){
            $where .= " AND current_status = '".$get['status_id']."' ";
        }
        $search = isset($get['search']) ? trim($get['search']) : '';
        if($search != ''){
            $search_esc = addslashes($search);
            $where .= " AND (
                courier_name LIKE '%".$search_esc."%'
                OR courier_ref_no LIKE '%".$search_esc."%'
                OR from_detail LIKE '%".$search_esc."%'
                OR to_detail LIKE '%".$search_esc."%'
                OR description LIKE '%".$search_esc."%'
                OR remarks LIKE '%".$search_esc."%'
            ) ";
        }
        return $where;
    }

    public function getCouriersEmployeeNameMap($company_id, $req_ids){
        global $callConfig;
        $map = array();
        if(count((array)$req_ids) == 0){
            return $map;
        }
        $clean_ids = array();
        foreach($req_ids as $id){
            $id = trim($id);
            if($id != ''){
                $clean_ids[] = "'".addslashes($id)."'";
            }
        }
        $clean_ids = array_unique($clean_ids);
        if(count($clean_ids) == 0){
            return $map;
        }
        $sql = "SELECT id, display_name FROM employees" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . "' AND id IN (".implode(',', $clean_ids).")" . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $res = $callConfig->getAllRows($sql);
        if(count((array)$res) > 0){
            foreach($res as $r){
                $row = is_array($r) ? $r : (array)$r;
                $map[$row['id']] = $row['display_name'];
            }
        }
        return $map;
    }

    private function collectCourierRequestedByIds(array $rows): array {
        $req_ids = array();
        foreach($rows as $row){
            $r = is_array($row) ? $row : (array)$row;
            if(!empty($r['requested_by'])){
                $req_ids[] = $r['requested_by'];
            }
        }
        return $req_ids;
    }

    private function formatInwardCourierRow(array $r, array $emp_map): array {
        $received_by_id = $r['requested_by'] ?? '';
        return array(
            'coming_date' => $r['courier_date'] ?? '',
            'coming_time' => $r['courier_time'] ?? '',
            'tracking_no' => $r['courier_ref_no'] ?? '',
            'vehicle_no' => '',
            'coming_from' => $r['from_detail'] ?? '',
            'to_address' => $r['to_detail'] ?? '',
            'description' => $r['description'] ?? '',
            'qty' => $r['courier_qty'] ?? '',
            'brought_by_name' => $r['courier_name'] ?? '',
            'received_by_name' => $emp_map[$received_by_id] ?? '',
            'received_date' => $r['courier_date'] ?? '',
            'received_time' => $r['courier_time'] ?? '',
            'security_sign' => '',
            'remarks' => $r['remarks'] ?? ''
        );
    }

    public function getInwardCouriersReport($get){
        global $callConfig;
        $return = $this->getCourierReportFilterLookups($get);
        $return['rows'] = array();

        if(($get['filters_only'] ?? '') == 'Y'){
            return $return;
        }

        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $company_id = $get['company_id'] ?? '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildCouriersReportWhere($get, 2);
        $sql = "SELECT * FROM couriers WHERE ".$where." ORDER BY courier_date DESC, id DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $req_ids = $this->collectCourierRequestedByIds((array)$rows);
        $emp_map = $this->getCouriersEmployeeNameMap($company_id, $req_ids);

        foreach((array)$rows as $row){
            $return['rows'][] = $this->formatInwardCourierRow((array)$row, $emp_map);
        }
        return $return;
    }

    private function formatOutwardCourierRow(array $r): array {
        return array(
            'courier_date' => $r['courier_date'] ?? '',
            'to_address' => $r['to_detail'] ?? '',
            'from_address' => $r['from_detail'] ?? '',
            'tracking_no' => $r['courier_ref_no'] ?? '',
            'vehicle_no' => '',
            'description' => $r['description'] ?? '',
            'qty' => $r['courier_qty'] ?? '',
            'taken_by_name' => $r['courier_name'] ?? '',
            'taken_by_date' => $r['courier_date'] ?? '',
            'taken_by_time' => $r['courier_time'] ?? '',
            'taken_by_contact' => $r['courier_mobile'] ?? '',
            'security_sign' => '',
            'remarks' => $r['remarks'] ?? ''
        );
    }

    public function getOutwardCouriersReport($get){
        global $callConfig;
        $return = $this->getCourierReportFilterLookups($get);
        $return['rows'] = array();

        if(($get['filters_only'] ?? '') == 'Y'){
            return $return;
        }

        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $company_id = $get['company_id'] ?? '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildCouriersReportWhere($get, 1);
        $sql = "SELECT * FROM couriers WHERE ".$where." ORDER BY courier_date DESC, id DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);

        foreach((array)$rows as $row){
            $return['rows'][] = $this->formatOutwardCourierRow((array)$row);
        }
        return $return;
    }
}

trait ReportGateVisitorsTrait {
    public function getGateReportFilterLookups($get){
        global $callConfig;
        $return = array();
        $return['locations'] = array();
        $return['employees'] = array();
        $return['departments'] = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == ''){
            return $return;
        }

        $locsql = "SELECT id, location_name FROM locations" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . " ORDER BY location_name ASC";
        $locres = $callConfig->getAllRows($locsql);
        $return['locations'] = $locres;

        $empsql = "SELECT id, display_name, emp_code FROM employees" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . " ORDER BY display_name ASC";
        $empres = $callConfig->getAllRows($empsql);
        $return['employees'] = $empres;

        $deptsql = "SELECT id, department_name FROM departments" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . " ORDER BY department_name ASC";
        $deptres = $callConfig->getAllRows($deptsql);
        $return['departments'] = $deptres;

        return $return;
    }

    public function getGateReportEmployeeMap($company_id){
        global $callConfig;
        $map = array();
        $sql = "SELECT id, display_name FROM employees" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $res = $callConfig->getAllRows($sql);
        if(count((array)$res) > 0){
            foreach($res as $r){
                $row = is_array($r) ? $r : (array)$r;
                $map[$row['id']] = $row['display_name'];
            }
        }
        return $map;
    }

    public function getGateReportUserMap($company_id){
        global $callConfig;
        $map = array();
        $sql = "SELECT id, display_name FROM application_users" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $res = $callConfig->getAllRows($sql);
        if(count((array)$res) > 0){
            foreach($res as $r){
                $row = is_array($r) ? $r : (array)$r;
                $map[$row['id']] = $row['display_name'];
            }
        }
        return $map;
    }

    public function resolveGateReportPersonName($id, $emp_map, $user_map){
        if(isset($emp_map[$id]) && $emp_map[$id] != ''){
            return $emp_map[$id];
        }
        if(isset($user_map[$id]) && $user_map[$id] != ''){
            return $user_map[$id];
        }
        return '';
    }

    private function buildVisitorSearchCondition($visit_type, $search_esc): string {
        $c1 = ReportConstants::SQL_VISITOR_IDCARD_LIKE . $search_esc . "'";
        $c2 = ReportConstants::SQL_VISITOR_BADGE_LIKE . $search_esc . ReportConstants::SQL_CLOSE_PAREN_2;
        if($visit_type == 'vendor_supplier'){
            $c1 = "%' OR vl.vendor_supplier_driver_name LIKE '%".$search_esc."%' OR vl.vendor_supplier_driver_mobile LIKE '%".$search_esc."%' OR vl.vendor_supplier_proof_document_no LIKE '%".$search_esc."'";
        }elseif($visit_type == 'employee'){
            $c1 = "%' OR vl.employee_code LIKE '%".$search_esc."%' OR vl.employee_name LIKE '%".$search_esc."%' OR vl.emp_temp_name LIKE '%".$search_esc."'";
        }
        return " AND (vl.name LIKE '%".$search_esc."%' OR vl.phone LIKE '%".$search_esc."%'" . $c1 . $c2;
    }

    private function buildMovementCondition($movement): string {
        if($movement == 'inside'){
            return " AND (vl.out_datetime IS NULL OR vl.out_datetime = '' OR vl.out_datetime = '0000-00-00 00:00:00') ";
        }
        if($movement == 'exited'){
            return " AND vl.out_datetime IS NOT NULL AND vl.out_datetime != '' AND vl.out_datetime != '0000-00-00 00:00:00' ";
        }
        return "";
    }

    private function buildCardReturnCondition($card_return): string {
        if($card_return == 'returned'){
            return " AND EXISTS (SELECT 1 FROM visitor_badges vb2 WHERE vb2.visitor_log_id = vl.id AND vb2.company_id = vl.company_id AND vb2.returned_at IS NOT NULL AND vb2.returned_at != '' AND vb2.returned_at != '0000-00-00 00:00:00') ";
        }
        if($card_return == 'not_returned'){
            return " AND NOT EXISTS (SELECT 1 FROM visitor_badges vb2 WHERE vb2.visitor_log_id = vl.id AND vb2.company_id = vl.company_id AND vb2.returned_at IS NOT NULL AND vb2.returned_at != '' AND vb2.returned_at != '0000-00-00 00:00:00') ";
        }
        return "";
    }

    public function buildVisitorMovementsWhere($get, $visit_type){
        $company_id = $get['company_id'] ?? '';
        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $where = " vl.company_id = '".$company_id."' AND vl.is_active = 0 AND vl.visit_type = '".$visit_type."' AND DATE(vl.in_datetime) >= '".$from_date."' AND DATE(vl.in_datetime) <= '".$to_date."' ";

        if(!empty($get['location_id'])){
            $where .= " AND vl.location_id = '".$get['location_id']."' ";
        }

        $where .= $this->buildMovementCondition($get['movement'] ?? '');

        if(!empty($get['whom_to_meet'])){
            $where .= " AND vl.whom_to_meet = '".$get['whom_to_meet']."' ";
        }

        if(!empty($get['department_id'])){
            $where .= " AND vl.vendor_supplier_department_id = '".$get['department_id']."' ";
        }

        if(!empty($get['employee_id'])){
            $empId = $get['employee_id'];
            $where .= " AND (vl.whom_to_meet = '".$empId."' OR vl.emp_temp_whom_to_meet = '".$empId."' OR vl.employee_code IN (SELECT emp_code FROM employees WHERE id = '".$empId."'" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . ReportConstants::SQL_AND_IS_ACTIVE_0 . ")) ";
        }

        if(!empty($get['temp_reason'])){
            $where .= " AND vl.emp_temp_reason = '".$get['temp_reason']."' ";
        }

        $where .= $this->buildCardReturnCondition($get['card_return'] ?? '');

        $search = isset($get['search']) ? trim($get['search']) : '';
        if($search != ''){
            $where .= $this->buildVisitorSearchCondition($visit_type, addslashes($search));
        }

        return $where;
    }

    private function resolveVisitorComingFrom(array $r): string {
        if(!empty($r['company_name'])){
            return $r['company_name'];
        }
        if(!empty($r['address'])){
            return $r['address'];
        }
        return '';
    }

    private function resolveVisitorCardNo(array $r): string {
        if(!empty($r['visitor_idcard_no'])){
            return $r['visitor_idcard_no'];
        }
        if(!empty($r['badge_code'])){
            return $r['badge_code'];
        }
        return '';
    }

    private function formatVisitorMovementRow($r, $emp_map, $user_map) {
        $whom_id = $r['whom_to_meet'] ?? '';
        $sec_id = $r['in_security_id'] ?? '';
        $purpose = $r['purpose'] ?? '';
        if($purpose == 'others' && !empty($r['purpose_other'])){
            $purpose = $r['purpose_other'];
        }

        return array(
            'name' => $r['name'] ?? '',
            'contact_no' => $r['phone'] ?? '',
            'government_id' => $r['idcard_number'] ?? '',
            'coming_from' => $this->resolveVisitorComingFrom((array)$r),
            'purpose' => $purpose,
            'whom_to_meet' => $emp_map[$whom_id] ?? '',
            'card_no' => $this->resolveVisitorCardNo((array)$r),
            'in_time' => $r['in_datetime'] ?? '',
            'out_time' => $r['out_datetime'] ?? '',
            'security_sign' => $user_map[$sec_id] ?? '',
            'remarks' => $r['remarks'] ?? ''
        );
    }

    public function getVisitorsMovementsReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        if(($get['filters_only'] ?? '') == 'Y'){
            return $return;
        }

        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $company_id = $get['company_id'] ?? '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'visitor');
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code
            FROM visitor_logs vl
            WHERE ".$where . ReportConstants::SQL_VISITOR_ORDER_BY_IN;
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $emp_map = $this->getGateReportEmployeeMap($company_id);
        $user_map = $this->getGateReportUserMap($company_id);

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $return['rows'][] = $this->formatVisitorMovementRow($r, $emp_map, $user_map);
            }
        }
        return $return;
    }

    private function formatVendorMovementRow($r, $emp_map, $user_map, $vendors) {
        $whom_id = $r['whom_to_meet'] ?? '';
        $sec_id = $r['in_security_id'] ?? '';
        $name = '';
        if(!empty($r['vendor_supplier_driver_name'])){
            $name = $r['vendor_supplier_driver_name'];
        } elseif(!empty($r['name'])){
            $name = $r['name'];
        }

        $contact = '';
        if(!empty($r['vendor_supplier_driver_mobile'])){
            $contact = $r['vendor_supplier_driver_mobile'];
        } elseif(!empty($r['phone'])){
            $contact = $r['phone'];
        }

        $vendor_id = $r['vendor_supplier_vendor_id'] ?? '';
        $coming_from = (!empty($vendor_id) && isset($vendors[$vendor_id])) ? $vendors[$vendor_id] : '';

        $card_no = '';
        if(!empty($r['visitor_idcard_no'])){
            $card_no = $r['visitor_idcard_no'];
        } elseif(!empty($r['badge_code'])){
            $card_no = $r['badge_code'];
        }

        $gov_id = $r['vendor_supplier_proof_document_no'] ?? '';
        if(is_array($gov_id) || $gov_id == 'Array'){
            $gov_id = '';
        }

        return array(
            'name' => $name,
            'contact_no' => $contact,
            'government_id' => $gov_id,
            'coming_from' => $coming_from,
            'purpose' => $r['vendor_supplier_purpose'] ?? '',
            'whom_to_meet' => $emp_map[$whom_id] ?? '',
            'card_no' => $card_no,
            'in_time' => $r['in_datetime'] ?? '',
            'out_time' => $r['out_datetime'] ?? '',
            'security_sign' => $user_map[$sec_id] ?? '',
            'remarks' => $r['remarks'] ?? ''
        );
    }

    public function getVendorsMovementsReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        if(($get['filters_only'] ?? '') == 'Y'){
            return $return;
        }

        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $company_id = $get['company_id'] ?? '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'vendor_supplier');
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code
            FROM visitor_logs vl
            WHERE ".$where . ReportConstants::SQL_VISITOR_ORDER_BY_IN;
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $emp_map = $this->getGateReportEmployeeMap($company_id);
        $user_map = $this->getGateReportUserMap($company_id);

        $query = "SELECT id, company, display_name FROM vendors" . ReportConstants::SQL_WHERE_COMPANY_ID . $company_id . "' AND is_active=0";
        $vendors_rows = $callConfig->getAllRows($query);
        $vendors = array();
        foreach ($vendors_rows as $row) {
            $vendors[$row->id] = $row->company ?? $row->display_name;
        }

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $return['rows'][] = $this->formatVendorMovementRow($r, $emp_map, $user_map, $vendors);
            }
        }
        return $return;
    }

    private function formatEmployeeTempIdRow(array $r, array $user_map): array {
        $sec_id = $r['in_security_id'] ?? '';
        $name = '';
        if(!empty($r['employee_name'])){
            $name = $r['employee_name'];
        } elseif(!empty($r['emp_temp_name'])){
            $name = $r['emp_temp_name'];
        } elseif(!empty($r['name'])){
            $name = $r['name'];
        }

        $card_no = '';
        if(!empty($r['badge_code'])){
            $card_no = $r['badge_code'];
        } elseif(!empty($r['visitor_idcard_no'])){
            $card_no = $r['visitor_idcard_no'];
        }

        $in_dt = $r['in_datetime'] ?? '';
        $visit_date = ($in_dt != '' && $in_dt != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($in_dt)) : '';
        $card_submitted = (!empty($r['badge_returned_at']) && $r['badge_returned_at'] != '0000-00-00 00:00:00') ? $r['badge_returned_at'] : '';

        return array(
            'visit_date' => $visit_date,
            'id_no' => $r['employee_code'] ?? '',
            'name' => $name,
            'card_no' => $card_no,
            'access_no' => '',
            'in_time' => $in_dt,
            'access_enable_date' => '',
            'out_time' => $r['out_datetime'] ?? '',
            'access_disable_date' => '',
            'card_submitted_date' => $card_submitted,
            'security_sign' => $this->resolveGateReportPersonName($sec_id, array(), $user_map),
            'remarks' => $r['remarks'] ?? ''
        );
    }

    public function getEmployeeTempIdReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        if(($get['filters_only'] ?? '') == 'Y'){
            return $return;
        }

        $from_date = $get['from_date'] ?? '';
        $to_date = $get['to_date'] ?? '';
        $company_id = $get['company_id'] ?? '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'employee');
        $where .= " AND vl.emp_temp_enable = 1 ";
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code,
            (SELECT vb.returned_at FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_returned_at
            FROM visitor_logs vl
            WHERE ".$where . ReportConstants::SQL_VISITOR_ORDER_BY_IN;
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $user_map = $this->getGateReportUserMap($company_id);

        foreach((array)$rows as $row){
            $return['rows'][] = $this->formatEmployeeTempIdRow((array)$row, $user_map);
        }
        return $return;
    }
}

trait ReportGateCourierTrait {
    use ReportCouriersTrait;
    use ReportGateVisitorsTrait;
}

class ReportUserClass extends userClass {
    use ReportCouriersTrait;
    use ReportGateVisitorsTrait;

    public function getDynamicDownloadPDFInvoices($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $query = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . "' AND is_active = 0";
        $dmts = $callConfig->getAllRows($query);
        $return['dynamic_mapping_tables'] = $dmts;

        if (!empty($get['from_date']) && !empty($get['to_date']) && !empty($get['table'])) {
            $dmtsql = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . ReportConstants::SQL_AND_APP_TABLE . $get['table'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $dmtres = $callConfig->getRow($dmtsql);
            $return['table_details'] = $dmtres;

            $listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . ReportConstants::SQL_AND_TRANSA_TX_DATE . $this->dateconvertdbformat($get['from_date']) . ReportConstants::SQL_AND_QUOTE . $this->dateconvertdbformat($get['to_date']) . "'" . ReportConstants::SQL_ORDER_BY_TX;
            $listres = $callConfig->getAllRows($listsql);
            $return['listres'] = $listsql;

            $list = array();
            foreach ($listres as $listre) {
                $itemsql = "SELECT id as item_row_id, item_id, account_code, item_name, description, item_quantity, purchase_uom, item_price, pending_qty, item_amount, tax_amount, discount_amount, total_amount FROM " . $dmtres->item_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_IS_ACTIVE_0;
                $itemres = $callConfig->getAllRows($itemsql);
                $listre->items = array();
                foreach ($itemres as $item) {
                    $taxsql = ReportConstants::SQL_SELECT_TAX_NAME_AMOUNT . $dmtres->tax_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_TX_ITEM_ID . $item->item_row_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
                    $taxres = $callConfig->getAllRows($taxsql);
                    $item->tax = $taxres;
                    $listre->items[] = $item;
                }
                $list[] = $listre;
            }
            $return['transaction_list'] = $list;
        }
        return $return;
    }

    public function togetlistamendedorders($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = "";
        if (!empty($get['ref_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_ID . $get['ref_id'] . "'";
        }
        if (!empty($get['ref_type'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_TYPE . $get['ref_type'] . "'";
        }
        $sql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $get['table'] . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . ReportConstants::SQL_ORDER_BY_TX;
        $res = $callConfig->getAllRows($sql);
        $return['sql'] = $sql;
        $return['res'] = $res;
        return $return;
    }

    private function buildSalesFilterCondition($get) {
        $condition = "";
        if (!empty($get['from_date']) && !empty($get['to_date'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_TX_DATE . $this->dateconvertdbformat($get['from_date']) . ReportConstants::SQL_AND_QUOTE . $this->dateconvertdbformat($get['to_date']) . "'";
        }
        if (!empty($get['customer_id'])) {
            $condition .= " AND transa.party_id = '" . $get['customer_id'] . "'";
        }
        if (!empty($get['project_id'])) {
            $condition .= " AND transa.project_id = '" . $get['project_id'] . "'";
        }
        if (!empty($get['vendor_id'])) {
            $condition .= " AND transa.party_id = '" . $get['vendor_id'] . "'";
        }
        if (!empty($get['user_id'])) {
            $condition .= " AND transa.originator = '" . $get['user_id'] . "'";
        }
        if (!empty($get['status_id'])) {
            $condition .= " AND transa.current_status = '" . $get['status_id'] . "'";
        }
        if (!empty($get['ref_type'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_TYPE . $get['ref_type'] . "'";
        }
        if (!empty($get['ref_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_ID . $get['ref_id'] . "'";
        }
        return $condition;
    }

    private function resolveTransactionPoInfo($refId, $companyId) {
        global $callConfig;
        $poNumber = '';
        $poDate = '';
        if (!empty($refId)) {
            $posql = "SELECT transaction_number, transaction_date FROM purchase_orders WHERE company_id = '" . $companyId . "' AND id = '" . $refId . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $pores = $callConfig->getRow($posql);
            if (!empty($pores)) {
                $poNumber = $pores->transaction_number;
                $poDate = $pores->transaction_date;
            }
        }
        return array('po_number' => $poNumber, 'po_date' => $poDate);
    }

    private function calculateTaxSplit($taxres, $taxState) {
        $hsnSac = '';
        $cgst = 0;
        $sgst = 0;
        foreach ($taxres as $tax) {
            if (empty($hsnSac) && !empty($tax->hsn_sac)) {
                $hsnSac = $tax->hsn_sac;
            }
            if (empty($taxState) || strtolower($taxState) == 's') {
                $cgst += $tax->tax_amount / 2;
                $sgst += $tax->tax_amount / 2;
            } else {
                $sgst += $tax->tax_amount;
            }
        }
        return array(
            'hsn_sac' => $hsnSac,
            'cgst' => $cgst,
            'sgst' => $sgst,
        );
    }

    public function getSalesSummaryReport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = $this->buildSalesFilterCondition($get);

        $get['table'] = $get['transaction_table'];
        $status_progress = $this->getStatusProgressList($get);
        foreach ($status_progress as $statusprogress) {
            $return['status_progress'][$statusprogress->status] = $statusprogress;
        }

        $dmtsql = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . ReportConstants::SQL_AND_APP_TABLE . $get['transaction_table'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $dmtres = $callConfig->getRow($dmtsql);
        $return['dynamic_mapping_tables'] = $dmtres;

        $listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . ReportConstants::SQL_ORDER_BY_TX;
        $listres = $callConfig->getAllRows($listsql);
        $return['listres'] = $listsql;

        $list = array();
        foreach ($listres as $listre) {
            $listre->gstin = '';
            if (!empty($listre->party_additional_details)) {
                $party_details = json_decode($listre->party_additional_details, true);
                if (isset($party_details['gstin'])) {
                    $listre->gstin = $party_details['gstin'];
                }
            }

            $po = $this->resolveTransactionPoInfo($listre->ref_id, $get['company_id']);
            $listre->po_number = $po['po_number'];
            $listre->po_date = $po['po_date'];

            $taxsql = ReportConstants::SQL_SELECT_TAX_NAME_AMOUNT . $dmtres->tax_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . "' AND (transaction_item_id IS NULL OR transaction_item_id = '' OR transaction_item_id = '0')" . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $taxres = $callConfig->getAllRows($taxsql);
            $taxSplit = $this->calculateTaxSplit($taxres, $listre->tax_state);
            $listre->hsn_sac = $taxSplit['hsn_sac'];
            $listre->cgst = $taxSplit['cgst'];
            $listre->sgst = (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') ? $taxSplit['sgst'] : 0;
            $listre->igst = (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') ? 0 : $taxSplit['sgst'];
            $listre->taxable_value = $listre->amount;
            $listre->gross_value = $listre->total_amount;

            $list[] = $listre;
        }

        $return['transaction_list'] = $list;
        $return['sql'] = $listsql;
        return $return;
    }

    private function loadOrderSalesTransactionItems($dmtres, $companyId, $txId, $taxState, array $uomList): array {
        global $callConfig;
        $items = array();
        $itemsql = "SELECT id as item_row_id, item_id, account_code, item_name, description, item_quantity, purchase_uom, item_price, pending_qty, item_amount, tax_amount, discount_amount, total_amount FROM " . $dmtres->item_table . ReportConstants::SQL_WHERE_COMPANY_ID . $companyId . ReportConstants::SQL_AND_TX_ID . $txId . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $itemres = $callConfig->getAllRows($itemsql);

        foreach ($itemres as $item) {
            $item->uom_name = $uomList[$item->purchase_uom] ?? $item->purchase_uom;
            $taxsql = ReportConstants::SQL_SELECT_TAX_NAME_AMOUNT . $dmtres->tax_table . ReportConstants::SQL_WHERE_COMPANY_ID . $companyId . ReportConstants::SQL_AND_TX_ID . $txId . ReportConstants::SQL_AND_TX_ITEM_ID . $item->item_row_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $taxres = $callConfig->getAllRows($taxsql);
            $taxSplit = $this->calculateTaxSplit($taxres, $taxState);
            $item->hsn_sac = $taxSplit['hsn_sac'];
            $item->cgst = $taxSplit['cgst'];
            $isStateS = (!empty($taxState) && strtolower($taxState) == 's');
            $item->sgst = $isStateS ? $taxSplit['sgst'] : 0;
            $item->igst = $isStateS ? 0 : $taxSplit['sgst'];
            $item->taxable_value = $item->item_amount;
            $item->gross_value = $item->item_amount + $item->tax_amount;
            $items[] = $item;
        }
        return $items;
    }

    public function getordersalesreport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = $this->buildSalesFilterCondition($get);

        $get['table'] = $get['transaction_table'];
        $status_progress = $this->getStatusProgressList($get);
        foreach ($status_progress as $statusprogress) {
            $return['status_progress'][$statusprogress->status] = $statusprogress;
        }

        $dmtsql = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . ReportConstants::SQL_AND_APP_TABLE . $get['transaction_table'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $dmtres = $callConfig->getRow($dmtsql);
        $return['dynamic_mapping_tables'] = $dmtres;

        $uomsql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements" . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $uomres = $callConfig->getAllRows($uomsql);
        $uom_list = array();
        foreach ($uomres as $uom) {
            $uom_list[$uom->id] = $uom->uom_name;
        }

        $listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . ReportConstants::SQL_ORDER_BY_TX;
        $listres = $callConfig->getAllRows($listsql);
        $return['listres'] = $listsql;

        $list = array();
        foreach ($listres as $listre) {
            $listre->gstin = '';
            if (!empty($listre->party_additional_details)) {
                $party_details = json_decode($listre->party_additional_details, true);
                if (isset($party_details['gstin'])) {
                    $listre->gstin = $party_details['gstin'];
                }
            }

            $po = $this->resolveTransactionPoInfo($listre->ref_id, $get['company_id']);
            $listre->po_number = $po['po_number'];
            $listre->po_date = $po['po_date'];

            $listre->items = $this->loadOrderSalesTransactionItems($dmtres, $get['company_id'], $listre->id, $listre->tax_state, $uom_list);
            $list[] = $listre;
        }

        $return['transaction_list'] = $list;
        $return['sql'] = $listsql;
        return $return;
    }

    public function getSalesRegisterReport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = $this->buildSalesFilterCondition($get);

        $get['table'] = $get['transaction_table'];
        $status_progress = $this->getStatusProgressList($get);
        foreach ($status_progress as $statusprogress) {
            $return['status_progress'][$statusprogress->status] = $statusprogress;
        }

        $dmtsql = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . ReportConstants::SQL_AND_APP_TABLE . $get['transaction_table'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $dmtres = $callConfig->getRow($dmtsql);
        $return['dynamic_mapping_tables'] = $dmtres;

        $uomsql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements" . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $uomres = $callConfig->getAllRows($uomsql);
        $uom_list = array();
        foreach ($uomres as $uom) {
            $uom_list[$uom->id] = $uom->uom_name;
        }

        $listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . ReportConstants::SQL_ORDER_BY_TX;
        $listres = $callConfig->getAllRows($listsql);
        $return['listres'] = $listsql;

        $list = array();
        foreach ($listres as $listre) {
            $listre->gstin = '';
            if (!empty($listre->party_additional_details)) {
                $party_details = json_decode($listre->party_additional_details, true);
                if (isset($party_details['gstin'])) {
                    $listre->gstin = $party_details['gstin'];
                }
            }

            $po = $this->resolveTransactionPoInfo($listre->ref_id, $get['company_id']);
            $listre->po_number = $po['po_number'];
            $listre->po_date = $po['po_date'];

            $itemsql = "SELECT id as item_row_id, item_id, account_code, item_name, description, item_quantity, purchase_uom, item_price, pending_qty, item_amount, tax_amount, discount_amount, total_amount FROM " . $dmtres->item_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $itemres = $callConfig->getAllRows($itemsql);
            $listre->items = array();

            foreach ($itemres as $item) {
                $item->uom_name = isset($uom_list[$item->purchase_uom]) ? $uom_list[$item->purchase_uom] : $item->purchase_uom;
                $taxsql = ReportConstants::SQL_SELECT_TAX_NAME_AMOUNT . $dmtres->tax_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_TX_ITEM_ID . $item->item_row_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
                $taxres = $callConfig->getAllRows($taxsql);
                $taxSplit = $this->calculateTaxSplit($taxres, $listre->tax_state);
                $item->hsn_sac = $taxSplit['hsn_sac'];
                $item->cgst = $taxSplit['cgst'];
                $item->sgst = (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') ? $taxSplit['sgst'] : 0;
                $item->igst = (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') ? 0 : $taxSplit['sgst'];
                $item->taxable_value = $item->item_amount;
                $item->gross_value = $item->item_amount + $item->tax_amount;
                $listre->items[] = $item;
            }
            $list[] = $listre;
        }

        $return['transaction_list'] = $list;
        $return['sql'] = $listsql;
        return $return;
    }

    public function toGetStandardTransactionDetailsReports($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = "";
        if (!empty($get['from_date']) && !empty($get['to_date'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_TX_DATE . $this->dateconvertdbformat($get['from_date']) . ReportConstants::SQL_AND_QUOTE . $this->dateconvertdbformat($get['to_date']) . "'";
        }
        if (!empty($get['customer_id'])) {
            $condition .= " AND transa.party_id = '" . $get['customer_id'] . "'";
        }
        if (!empty($get['project_id'])) {
            $condition .= " AND transa.project_id = '" . $get['project_id'] . "'";
        }
        if (!empty($get['vendor_id'])) {
            $condition .= " AND transa.party_id = '" . $get['vendor_id'] . "'";
        }
        if (!empty($get['user_id'])) {
            $condition .= " AND transa.originator = '" . $get['user_id'] . "'";
        }
        if (!empty($get['status_id'])) {
            $condition .= " AND transa.current_status = '" . $get['status_id'] . "'";
        }
        if (!empty($get['ref_type'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_TYPE . $get['ref_type'] . "'";
        }
        if (!empty($get['ref_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA_REF_ID . $get['ref_id'] . "'";
        }
        $get['table'] = $get['transaction_table'];
        $status_progress = $this->getStatusProgressList($get);
        foreach ($status_progress as $statusprogress) {
            $return['status_progress'][$statusprogress->status] = $statusprogress;
        }
        $dmtsql = ReportConstants::SQL_SELECT_DMT . $get['company_id'] . ReportConstants::SQL_AND_APP_TABLE . $get['transaction_table'] . ReportConstants::SQL_AND_IS_ACTIVE_0;
        $dmtres = $callConfig->getRow($dmtsql);
        $return['dynamic_mapping_tables'] = $dmtres;

        $listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . ReportConstants::SQL_TRANSA_WHERE_CO . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . ReportConstants::SQL_ORDER_BY_TX;
        $listres = $callConfig->getAllRows($listsql);
        $return['listres'] = $listsql;

        $list = array();
        foreach ($listres as $listre) {
            $itemsql = "SELECT id as item_row_id, item_id, account_code, item_name, description, item_quantity, purchase_uom, item_price, pending_qty, item_amount, tax_amount, discount_amount, total_amount FROM " . $dmtres->item_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_IS_ACTIVE_0;
            $itemres = $callConfig->getAllRows($itemsql);
            $listre->items = array();
            foreach ($itemres as $item) {
                $taxsql = ReportConstants::SQL_SELECT_TAX_NAME_AMOUNT . $dmtres->tax_table . ReportConstants::SQL_WHERE_COMPANY_ID . $get['company_id'] . ReportConstants::SQL_AND_TX_ID . $listre->id . ReportConstants::SQL_AND_TX_ITEM_ID . $item->item_row_id . ReportConstants::SQL_AND_IS_ACTIVE_0;
                $taxres = $callConfig->getAllRows($taxsql);
                $item->tax = $taxres;
                $listre->items[] = $item;
            }
            $list[] = $listre;
        }
        $return['transaction_list'] = $list;
        return $return;
    }

    public function toGetStandardDayStockBalanceReport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $sql = "SELECT ssb.id, ssb.item_id, ssb.opening_balance, ssb.receipts, ssb.issues, ssb.closing_balance, ssb.created_date, ssb.is_active, ssb.closing_rate, itm.item_name, itm.item_code, itm.purchase_uom, uom.uom_name FROM standard_day_stock_balance ssb, items itm, unit_of_measurements uom WHERE ssb.company_id = '" . $get['company_id'] . "' AND ssb.company_id = itm.company_id AND ssb.company_id = uom.company_id AND ssb.item_id = itm.id AND itm.purchase_uom = uom.id AND ssb.is_active = 0 AND itm.is_active = 0 AND uom.is_active = 0 AND ssb.created_date = '" . $this->dateconvertdbformat($get['date']) . "' GROUP BY ssb.item_id";
        $res = $callConfig->getAllRows($sql);
        $return['sql'] = $sql;
        $return['res'] = $res;
        return $return;
    }

    public function getProjectTaskTimeLogReport($get) {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = "";
        if (!empty($get['user_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA . "user_id = '" . $get['user_id'] . "'";
        }
        if (!empty($get['project_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA . "project_id = '" . $get['project_id'] . "'";
        }
        if (!empty($get['task_id'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA . "task_id = '" . $get['task_id'] . "'";
        }
        if (!empty($get['from_date']) && !empty($get['to_date'])) {
            $condition .= ReportConstants::SQL_AND_TRANSA . "time_date BETWEEN '" . $this->dateconvertdbformat($get['from_date']) . ReportConstants::SQL_AND_QUOTE . $this->dateconvertdbformat($get['to_date']) . "'";
        }
        $dmtsql = "SELECT transa.id, transa.time_date, transa.user_id, transa.project_id, transa.task_id, transa.time_spent, (SELECT usr.display_name FROM application_users usr WHERE usr.company_id = transa.company_id AND usr.id = transa.user_id AND usr.is_active = 0) as user_name, (SELECT pro.project_name FROM projects pro WHERE pro.company_id = transa.company_id AND pro.id = transa.project_id AND pro.is_active = 0) as project_name, (SELECT tsk.task_name FROM project_tasks tsk WHERE tsk.company_id = transa.company_id AND tsk.id = transa.task_id AND tsk.is_active = 0) as task_name, (SELECT count(pttl.id) FROM project_task_time_log pttl WHERE pttl.company_id = transa.company_id AND pttl.is_active = transa.is_active AND pttl.user_id = transa.user_id AND pttl.time_date = transa.time_date) as day_log_cnt, (SELECT sum(pttl.time_spent) FROM project_task_time_log pttl WHERE pttl.company_id = transa.company_id AND pttl.is_active = transa.is_active AND pttl.user_id = transa.user_id AND pttl.time_date = transa.time_date) as day_total_time_spent FROM project_task_time_log transa WHERE transa.company_id = '" . $get['company_id'] . ReportConstants::SQL_AND_TRANSA_IS_ACTIVE . $condition . " ORDER BY transa.time_date ASC, transa.user_id ASC";
        $dmtres = $callConfig->getAllRows($dmtsql);

        $project_task_time_log = array();
        foreach ($dmtres as $ptl) {
            $task_sql = "SELECT pttl.time_spent, (SELECT tsk.task_name FROM project_tasks tsk WHERE tsk.company_id = pttl.company_id AND tsk.id = pttl.task_id AND tsk.is_active = 0) as task_name FROM project_task_time_log pttl WHERE pttl.company_id = '" . $get['company_id'] . "' AND pttl.user_id = '" . $ptl->user_id . "' AND pttl.time_date = '" . $ptl->time_date . "' AND pttl.is_active = 0";
            $task_res = $callConfig->getAllRows($task_sql);
            $task_names = array();
            foreach ($task_res as $task) {
                $task_names[] = $task->task_name . " (" . $task->time_spent . " hrs)";
            }
            $ptl->task_names = $task_names;
            $project_task_time_log[$ptl->user_id][$ptl->time_date] = $ptl;
        }
        $return['project_task_time_log_Sql'] = $dmtsql;
        $return['project_task_time_log'] = $project_task_time_log;

        return $return;
    }
}
if (!class_exists('reportUserClass', false)) {
    class_alias('ReportUserClass', 'reportUserClass');
}
