<?php 
class manfacturingForecastUserClass extends userClass{
    function getmanufacturingforecastedata($get){
		global $callConfig;
        $return = array();
        $company_id = intval($get['company_id']);
        $year = intval($get['year']);

        $itemsQuery = "SELECT id AS product_id, item_name FROM items WHERE company_id='{$company_id}' AND stocking_type='F' AND is_active=0 ORDER BY item_name ASC";
        $items = $callConfig->getAllRows($itemsQuery);

        $headerQuery = "SELECT * FROM manufacturing_forecast WHERE company_id='{$company_id}' AND year='{$year}' AND is_active=0 LIMIT 1";
        $forecastHeader = $callConfig->getRow($headerQuery);
        $forecastHeaderId = !empty($forecastHeader->id) ? intval($forecastHeader->id) : 0;

        if ($forecastHeaderId > 0) {
            $forecastQuery = "SELECT * FROM manufacturing_forecast_items WHERE company_id='{$company_id}' AND manufacturing_forecast_id='{$forecastHeaderId}' AND is_active=0";
        } else {
            $forecastQuery = "SELECT * FROM manufacturing_forecast_items WHERE company_id='{$company_id}' AND year='{$year}' AND is_active=0";
        }
        $forecastRows = $callConfig->getAllRows($forecastQuery);
        $return['forecast_status'] = '';
        $return['forecast_transaction_id'] = '';
        $return['forecast_no'] = '';
        $return['forecast_date'] = date('Y-m-d');
        $return['remarks'] = '';
        $return['forecast_header'] = array();
        if ($forecastHeaderId > 0) {
            $forecastHeaderArray = (array)$forecastHeader;
            $return['forecast_header'] = $forecastHeaderArray;
            $return['forecast_status'] = $forecastHeaderArray['current_status'] ?? '';
            $return['forecast_transaction_id'] = $forecastHeaderArray['id'] ?? '';
            $return['forecast_no'] = $forecastHeaderArray['forecast_no'] ?? '';
            $return['forecast_date'] = $forecastHeaderArray['forecast_date'] ?? date('Y-m-d');
            $return['remarks'] = $forecastHeaderArray['remarks'] ?? '';
        }

        $forecastMap = array();
        foreach ((array)$forecastRows as $forecast) {
            $forecastMap[$forecast->product_id] = (array)$forecast;
        }

        $data = array();
        foreach ((array)$items as $item) {
            $itemArray = (array)$item;
            $row = isset($forecastMap[$item->product_id]) ? $forecastMap[$item->product_id] : array();
            $row['product_id'] = $item->product_id;
            $row['item_name'] = $item->item_name;
            for ($i = 1; $i <= 12; $i++) {
                $key = "forecast_month_" . $i;
                if (!isset($row[$key])) {
                    $row[$key] = 0;
                }
            }
            $data[] = array_merge($itemArray, $row);
        }

        $return['data'] = $data;
        $return['forecast_rows'] = $forecastRows;

		return $return;
	}

    function insertandupdatemanufacturingforecaste($get, $post){
        global $callConfig;
        $return = array();
        $return['post']=$post;
        $return['get']=$get;
        
        $company_id = intval($get['company_id']);
        $user_id = intval($get['user_id']);
        $year = intval($post['year'] ?? 0);
        $forecast_date = !empty($post['forecast_date']) ? $this->dateconvertdbformat($post['forecast_date']) : date('Y-m-d');
        $remarks = trim($post['remarks'] ?? '');

        if ($year == 0 || empty($post['rows']) || !is_array($post['rows'])) {
            return array(array('status' => 'fail', 'message' => 'Forecast year and product rows are required.'));
        }

        $activeProducts = array();
        $products = $callConfig->getAllRows("SELECT id FROM items WHERE company_id='{$company_id}' AND stocking_type='F' AND is_active=0");
        foreach ((array)$products as $product) {
            $activeProducts[$product->id] = $product->id;
        }

        $forecastHeader = $callConfig->getRow("SELECT * FROM manufacturing_forecast WHERE company_id='{$company_id}' AND year='{$year}' AND is_active=0 LIMIT 1");
        $editableStatuses = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '', null);
        if (!empty($forecastHeader->id)) {
            if (!in_array($forecastHeader->current_status, $editableStatuses)) {
                return array(array('status' => 'fail', 'message' => 'Approved forecast cannot be edited.'));
            }
        }
        
        if (!empty($forecastHeader->id)) {
            $forecastHeaderId = intval($forecastHeader->id);
            $headerRecord = array(
                'forecast_date' => $forecast_date,
                'remarks' => $remarks,
                'updated_by' => $user_id,
                'updated_date' => date('Y-m-d H:i:s')
            );
            $return['forecast_header'] = $callConfig->updateRecord('manufacturing_forecast', $headerRecord, 'id', $forecastHeaderId);
        } else {
            $forecastNo = $this->toGetApplicationSerialNumber('manufacturing_forecast', array(
                'company_id' => $company_id,
                'user_id' => $user_id,
                'transaction_date' => $forecast_date
            ));
            $headerRecord = array(
                'company_id' => $company_id,
                'forecast_no' => $forecastNo,
                'forecast_date' => $forecast_date,
                'year' => $year,
                'remarks' => $remarks,
                'is_active' => 0,
                'current_status' => 0,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            );
            $headerResult = $callConfig->insertRecord('manufacturing_forecast', $headerRecord);
            if (($headerResult['status'] ?? '') != 'sucess') {
                return array(array('status' => 'fail', 'message' => 'Unable to create forecast header.'));
            }
            $forecastHeaderId = intval($headerResult['LASTID']);
            $return['forecast_header'] = $headerResult;
        }

        $existingRecords = $callConfig->getAllRows("SELECT id, product_id FROM manufacturing_forecast_items WHERE company_id='{$company_id}' AND (manufacturing_forecast_id='{$forecastHeaderId}' OR (year='{$year}' AND (manufacturing_forecast_id IS NULL OR manufacturing_forecast_id=0))) AND is_active=0");

        $existingMap = array();
        if (!empty($existingRecords) && is_array($existingRecords)) {
            foreach ($existingRecords as $r) {
                $existingMap[$r->product_id] = $r->id;
            }
        }

        foreach ($post['rows'] as $product_id => $row) {
            $product_id = intval($product_id);
            if (!isset($activeProducts[$product_id])) {
                return array(array('status' => 'fail', 'message' => 'Invalid finished good item selected.'));
            }
            $record = [
                'manufacturing_forecast_id' => $forecastHeaderId,
                'company_id' => $company_id,
                'year' => $year,
                'product_id' => $product_id,
                'interval_type' => 'monthly',
                'remarks' => $remarks,
                'current_status' => 0,
                'is_active' => 0
            ];

            $total = 0;
            for ($i = 1; $i <= 12; $i++) {
                $key = "forecast_month_$i";
                $rawValue = $row[$key] ?? '';
                if ($rawValue === '' || !is_numeric($rawValue)) {
                    return array(array('status' => 'fail', 'message' => 'Forecast quantity must be numeric.'));
                }
                $val = floatval($rawValue);
                if ($val < 0) {
                    return array(array('status' => 'fail', 'message' => 'Forecast quantity cannot be negative.'));
                }
                $record[$key] = $val;
                $total += $val;
            }

            $record['forecast_quarterly_1'] = $record['forecast_month_1'] + $record['forecast_month_2'] + $record['forecast_month_3'];
            $record['forecast_quarterly_2'] = $record['forecast_month_4'] + $record['forecast_month_5'] + $record['forecast_month_6'];
            $record['forecast_quarterly_3'] = $record['forecast_month_7'] + $record['forecast_month_8'] + $record['forecast_month_9'];
            $record['forecast_quarterly_4'] = $record['forecast_month_10'] + $record['forecast_month_11'] + $record['forecast_month_12'];
            $record['forecast_yearly'] = $total;
            $record['total_forecast'] = $total;
        

            if (isset($existingMap[$product_id])) {
                $record['updated_by'] = $user_id;
                $record['updated_date'] = date('Y-m-d H:i:s');
                $return['forecast_rows'][] = $callConfig->updateRecord('manufacturing_forecast_items', $record, 'id', $existingMap[$product_id]);
            } else {
                $record['created_by'] = $user_id;
                $record['created_date'] = date('Y-m-d H:i:s');
                $return['forecast_rows'][] = $callConfig->insertRecord('manufacturing_forecast_items', $record);
            }
        }
        // return $return;
        return array(array('status' => 'sucess', 'LASTID' => $forecastHeaderId, 'message' => 'Forecast saved successfully.'));

    }

    function getmanufacturingfirmordersdata($get){
        global $callConfig;
        $return = array();
        $company_id = intval($get['company_id']);
        $year = intval($get['year'] ?? 0);
        $month = intval($get['month'] ?? 0);
        $return['data'] = array();
        $return['firm_order_id'] = '';
        $return['firm_order_status'] = '';
        $return['firm_order_no'] = '';
        $return['firm_order_date'] = date('Y-m-d');
        $return['remarks'] = '';
        $return['message'] = '';

        if ($year == 0 || $month < 1 || $month > 12) {
            $return['message'] = 'Please select a valid year and month.';
            return $return;
        }

        $selectedDate = date("Y-m-d", strtotime($year . "-" . $month . "-01"));
        $yearMonth = date("Y-m", strtotime($selectedDate));
        $monthDates = array();
        for ($i = 0; $i < 3; $i++) {
            $monthDates[$i + 1] = date("Y-m", strtotime($selectedDate . " +{$i} month"));
        }

        $header = $callConfig->getRow("SELECT * FROM manufacturing_firm_orders WHERE company_id='{$company_id}' AND `year_month`='{$yearMonth}' AND is_active=0 LIMIT 1");
        if (!empty($header->id)) {
            $return['firm_order_id'] = $header->id;
            $return['firm_order_status'] = $header->current_status;
            $return['firm_order_no'] = $header->firm_order_no;
            $return['firm_order_date'] = $header->firm_order_date;
            $return['remarks'] = $header->remarks;
            $query = "SELECT item.id, item.product_id, item.firm_month_1, item.firm_qty_1, item.firm_month_2, item.firm_qty_2, item.firm_month_3, item.firm_qty_3, i.item_name, item.current_status
                FROM manufacturing_firm_order_items item
                INNER JOIN items i ON i.company_id=item.company_id AND i.id=item.product_id AND i.is_active=0
                WHERE item.company_id='{$company_id}' AND item.manufacturing_firm_order_id='{$header->id}' AND item.is_active=0
                ORDER BY i.item_name ASC";
            $return['data'] = $callConfig->getAllRows($query);
            return $return;
        }

        if ($month == 1) {
            $forecastHeader = $callConfig->getRow("SELECT id FROM manufacturing_forecast WHERE company_id='{$company_id}' AND year='{$year}' AND current_status=".ST_APPROVED." AND is_active=0 LIMIT 1");
            if (empty($forecastHeader->id)) {
                $return['message'] = 'Please approve the '.$year.' manufacturing forecast before creating firm orders.';
                return $return;
            }
            $query = "SELECT fi.product_id, i.item_name, fi.forecast_month_1 AS firm_qty_1, fi.forecast_month_2 AS firm_qty_2, fi.forecast_month_3 AS firm_qty_3,
                    '{$monthDates[1]}' AS firm_month_1, '{$monthDates[2]}' AS firm_month_2, '{$monthDates[3]}' AS firm_month_3
                FROM manufacturing_forecast_items fi
                INNER JOIN items i ON i.company_id=fi.company_id AND i.id=fi.product_id AND i.stocking_type='F' AND i.is_active=0
                WHERE fi.company_id='{$company_id}' AND fi.manufacturing_forecast_id='{$forecastHeader->id}' AND fi.is_active=0
                ORDER BY i.item_name ASC";
            $return['data'] = $callConfig->getAllRows($query);
            return $return;
        }

        $previousYearMonth = date("Y-m", strtotime($selectedDate . " -1 month"));
        $previousHeader = $callConfig->getRow("SELECT id FROM manufacturing_firm_orders WHERE company_id='{$company_id}' AND `year_month`='{$previousYearMonth}' AND current_status=".ST_APPROVED." AND is_active=0 LIMIT 1");
        if (empty($previousHeader->id)) {
            $return['message'] = 'Please approve the '.date('F Y', strtotime($previousYearMonth . '-01')).' firm order before creating '.date('F Y', strtotime($selectedDate)).'.';
            return $return;
        }

        $thirdDate = strtotime($selectedDate . " +2 month");
        $thirdYear = intval(date('Y', $thirdDate));
        $thirdMonth = intval(date('n', $thirdDate));
        $thirdForecastField = "forecast_month_" . $thirdMonth;
        $thirdForecastHeader = $callConfig->getRow("SELECT id FROM manufacturing_forecast WHERE company_id='{$company_id}' AND year='{$thirdYear}' AND current_status=".ST_APPROVED." AND is_active=0 LIMIT 1");
        if (empty($thirdForecastHeader->id)) {
            $return['message'] = 'Please approve the '.$thirdYear.' manufacturing forecast before creating this firm order.';
            return $return;
        }

        $query = "SELECT prev.product_id, i.item_name, prev.firm_qty_2 AS firm_qty_1, prev.firm_qty_3 AS firm_qty_2, IFNULL(fi.{$thirdForecastField},0) AS firm_qty_3,
                '{$monthDates[1]}' AS firm_month_1, '{$monthDates[2]}' AS firm_month_2, '{$monthDates[3]}' AS firm_month_3
            FROM manufacturing_firm_order_items prev
            INNER JOIN items i ON i.company_id=prev.company_id AND i.id=prev.product_id AND i.stocking_type='F' AND i.is_active=0
            LEFT JOIN manufacturing_forecast_items fi ON fi.company_id=prev.company_id AND fi.product_id=prev.product_id AND fi.manufacturing_forecast_id='{$thirdForecastHeader->id}' AND fi.is_active=0
            WHERE prev.company_id='{$company_id}' AND prev.manufacturing_firm_order_id='{$previousHeader->id}' AND prev.is_active=0
            ORDER BY i.item_name ASC";
        $return['data'] = $callConfig->getAllRows($query);

        return $return;
    }
    function insertandupdatemanufacturingfirmorder($get, $post){
        global $callConfig;
        $return = array();
        $company_id = intval($get['company_id']);
        $user_id = intval($get['user_id']);
        $year = intval($post['year'] ?? 0);
        $month = intval($post['month'] ?? 0);
        $firmOrderDate = !empty($post['firm_order_date']) ? $this->dateconvertdbformat($post['firm_order_date']) : date('Y-m-d');
        $remarks = trim($post['remarks'] ?? '');

        if ($year == 0 || $month < 1 || $month > 12 || empty($post['rows']) || !is_array($post['rows'])) {
            return array('data' => array(array('status' => 'fail', 'message' => 'Firm order year, month, and product rows are required.')));
        }

        $new_date = date("Y-m-d", strtotime($year . "-" . $month . "-01"));
        $m_y = date("Y-m", strtotime($new_date));
        $n_y = date("Y-m", strtotime($new_date . " +1 month"));
        $p_y = date("Y-m", strtotime($new_date . " +2 month"));

        $firmOrder = $callConfig->getRow("SELECT * FROM manufacturing_firm_orders WHERE company_id='{$company_id}' AND `year_month`='{$m_y}' AND is_active=0 LIMIT 1");
        $editableStatuses = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '', null);
        if (!empty($firmOrder->id) && !in_array($firmOrder->current_status, $editableStatuses)) {
            return array('data' => array(array('status' => 'fail', 'message' => 'Approved firm order cannot be edited.')));
        }

        if ($month == 1) {
            $forecastYear = $year;
        } else {
            $forecastYear = intval(date('Y', strtotime($new_date . " +2 month")));
        }
        $forecastCheck = $callConfig->getRow("SELECT id FROM manufacturing_forecast WHERE company_id='{$company_id}' AND year='{$forecastYear}' AND current_status=".ST_APPROVED." AND is_active=0 LIMIT 1");
        if (empty($forecastCheck->id)) {
            return array('data' => array(array('status' => 'fail', 'message' => 'Approved forecast is required before saving this firm order.')));
        }

        if ($month > 1) {
            $previousYearMonth = date("Y-m", strtotime($new_date . " -1 month"));
            $previousFirmOrder = $callConfig->getRow("SELECT id FROM manufacturing_firm_orders WHERE company_id='{$company_id}' AND `year_month`='{$previousYearMonth}' AND current_status=".ST_APPROVED." AND is_active=0 LIMIT 1");
            if (empty($previousFirmOrder->id)) {
                return array('data' => array(array('status' => 'fail', 'message' => 'Previous month firm order must be approved first.')));
            }
        }

        if (!empty($firmOrder->id)) {
            $firmOrderId = intval($firmOrder->id);
            $headerRecord = array(
                'firm_order_date' => $firmOrderDate,
                'remarks' => $remarks,
                'updated_by' => $user_id,
                'updated_date' => date('Y-m-d H:i:s')
            );
            $return['firm_order'] = $callConfig->updateRecord('manufacturing_firm_orders', $headerRecord, 'id', $firmOrderId);
        } else {
            $firmOrderNo = $this->toGetApplicationSerialNumber('manufacturing_firm_orders', array(
                'company_id' => $company_id,
                'user_id' => $user_id,
                'transaction_date' => $firmOrderDate
            ));
            $headerRecord = array(
                'company_id' => $company_id,
                'firm_order_no' => $firmOrderNo,
                'firm_order_date' => $firmOrderDate,
                '`year_month`' => $m_y,
                'remarks' => $remarks,
                'is_active' => 0,
                'current_status' => 0,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            );
            $headerResult = $callConfig->insertRecord('manufacturing_firm_orders', $headerRecord);
            if (($headerResult['status'] ?? '') != 'sucess') {
                return array('data' => array(array('status' => 'fail', 'message' => 'Unable to create firm order.')));
            }
            $firmOrderId = intval($headerResult['LASTID']);
            $return['firm_order'] = $headerResult;
        }

        $activeProducts = array();
        $products = $callConfig->getAllRows("SELECT id FROM items WHERE company_id='{$company_id}' AND stocking_type='F' AND is_active=0");
        foreach ((array)$products as $product) {
            $activeProducts[$product->id] = $product->id;
        }

        $existingRows = $callConfig->getAllRows("SELECT id, product_id FROM manufacturing_firm_order_items WHERE company_id='{$company_id}' AND manufacturing_firm_order_id='{$firmOrderId}' AND is_active=0");
        $existingMap = array();
        foreach ((array)$existingRows as $existingRow) {
            $existingMap[$existingRow->product_id] = $existingRow->id;
        }

        foreach ($post['rows'] as $product_id => $row) {
            $product_id = intval($product_id);
            if (!isset($activeProducts[$product_id])) {
                return array('data' => array(array('status' => 'fail', 'message' => 'Invalid finished good item selected.')));
            }
            for ($i = 1; $i <= 3; $i++) {
                if (($row["firm_qty_$i"] ?? '') === '' || !is_numeric($row["firm_qty_$i"])) {
                    return array('data' => array(array('status' => 'fail', 'message' => 'Firm order quantity must be numeric.')));
                }
                if (floatval($row["firm_qty_$i"]) < 0) {
                    return array('data' => array(array('status' => 'fail', 'message' => 'Firm order quantity cannot be negative.')));
                }
            }
            $record = [
                'manufacturing_firm_order_id' => $firmOrderId,
                'company_id' => $company_id,
                '`year_month`' => $m_y,
                'product_id' => $product_id,
                'firm_month_1' => $m_y,
                'firm_qty_1' => floatval($row['firm_qty_1']),
                'firm_month_2' => $n_y,
                'firm_qty_2' => floatval($row['firm_qty_2']),
                'firm_month_3' => $p_y,
                'firm_qty_3' => floatval($row['firm_qty_3']),
                'current_status' => '0',
                'is_active' => 0
            ];
            if (isset($existingMap[$product_id])) {
                $record['updated_by'] = $user_id;
                $record['updated_date'] = date('Y-m-d H:i:s');
                $return['firm_order_items'][] = $callConfig->updateRecord('manufacturing_firm_order_items', $record, 'id', $existingMap[$product_id]);
            } else {
                $record['created_by'] = $user_id;
                $record['created_date'] = date('Y-m-d H:i:s');
                $return['firm_order_items'][] = $callConfig->insertRecord('manufacturing_firm_order_items', $record);
            }
        }
        $return['data'][] = array('status' => 'sucess', 'LASTID' => $firmOrderId, 'message' => 'Firm order saved successfully.');
        return $return;
    }
    function getmfgdashboarddetails($get){
        global $callConfig;
        $return = [];
        $company_id = $get['company_id'];

        $query = "SELECT w.id AS work_order_id, w.transaction_no, w.transaction_date,  w.work_order_item_id AS product_id, w.work_order_item_qty AS planned_qty, w.completed_qty, ROUND( IF(w.work_order_item_qty > 0, (w.completed_qty / w.work_order_item_qty) * 100, 0),  2) AS progress_percent, w.current_status AS status_value,  s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color,   p.sku AS product_sku, p.item_name AS product_name, p.item_description AS product_description, p.sale_uom AS product_uom, p.item_image AS product_image, p.sale_price AS product_sale_price, p.purchase_price AS product_purchase_price, w.planned_start_date, w.planned_end_date, w.actual_start_date, w.actual_end_date, CONCAT('[',  IFNULL(( SELECT GROUP_CONCAT( CONCAT( '{', '\"item_id\":\"', i.item_id, '\",', '\"item_name\":\"', IFNULL(it.item_name, ''), '\",', '\"uom\":\"', IFNULL(u.uom_short_code, ''), '\",', '\"request_qty\":\"', i.request_qty, '\",', '\"bom_qty\":\"', i.bom_qty, '\",', '\"stock_request_qty\":\"', i.stock_request_qty, '\",', '\"stock_receipts_qty\":\"', i.stock_receipts_qty, '\"', '}' ) ) FROM manufacturing_work_order_items i LEFT JOIN items it  ON it.company_id = i.company_id  AND it.id = i.item_id   AND it.is_active = 0 LEFT JOIN unit_of_measurements u ON u.company_id = i.company_id  AND u.id = i.uom  AND u.is_active = 0 WHERE i.company_id = w.company_id  AND i.transaction_id = w.id  AND i.is_active = 0 ), '') , ']') AS materials, CONCAT('[', IFNULL(( SELECT GROUP_CONCAT(
                CONCAT(
                    '{',
                        '\"operation_name\":\"', IFNULL(mo.operation_name, ''), '\",',
                        '\"operation_time\":\"', o.operation_time, '\",',
                        '\"operation_manpower\":\"', o.operation_manpower, '\",',
                        '\"operation_note\":\"', REPLACE(o.operation_note, '\"', '\\\"'), '\",',
                        '\"status\":\"', o.current_status, '\"',
                    '}'
                )
            )
            FROM manufacturing_work_order_operations o
            LEFT JOIN manufacturing_operations mo
                ON mo.id = o.manufacturing_operation_id
                AND mo.company_id = o.company_id
                AND mo.is_active = 0
            WHERE o.company_id = w.company_id  
              AND o.transaction_id = w.id
              AND o.is_active = 0 ), ''), ']') AS operations FROM manufacturing_work_orders w LEFT JOIN status s ON s.company_id = w.company_id AND s.name_value = w.current_status AND s.is_active = 0  LEFT JOIN items p  ON p.id = w.work_order_item_id  AND p.company_id = w.company_id  AND p.is_active = 0  WHERE w.company_id = '" . $company_id . "'  AND w.is_active = 0 ";
        $results = $callConfig->getAllRows($query);
        $return['query'] = $query;
        $return['work_orders'] = $results;
        $return['work_orders_total'] = count($results);

        $itemquery = "SELECT id, stocking_type, opening_quantity, quantity_check FROM items WHERE company_id = '" . $company_id . "' AND is_active = 0 ";
        $itemrows = $callConfig->getAllRows($itemquery);
        $summary = ['raw' => ['type' => 'Raw Material', 'count' => 0, 'total' => 0], 'wip' => ['type' => 'WIP', 'count' => 0, 'total' => 0], 'fg' => ['type' => 'Finished Goods', 'count' => 0, 'total' => 0], ];
        $shortages = 0;
        foreach ($itemrows as $item) {
            $qty = (float) $item->opening_quantity;
            $check = (float) $item->quantity_check;

            switch ($item->stocking_type) {
                case 'M':
                    $summary['raw']['count']++;
                    $summary['raw']['total'] += $qty;
                    break;
                case 'W':
                    $summary['wip']['count']++;
                    $summary['wip']['total'] += $qty;
                    break;
                case 'F':
                    $summary['fg']['count']++;
                    $summary['fg']['total'] += $qty;
                    break;
            }

            if ($qty < $check) {
                $shortages++;
            }
        }
        $return['itemquery'] = $itemquery;
        $return['itemrows'] = $itemrows;
        $return['summary'] = $summary;
        $return['shortages'] = $shortages;
        
        
        $mpnQuery = "SELECT YEAR(transaction_date) AS year, SUM(request_qty) AS total_request_qty, SUM(produced_qty) AS total_produced_qty FROM manufacturing_product_notes WHERE company_id = '".$company_id."' AND is_active = 0 GROUP BY YEAR(transaction_date) ORDER BY YEAR(transaction_date) ASC ";
        $mpnRows = $callConfig->getAllRows($mpnQuery);
        $productNotesTotals = [];
        foreach ($mpnRows as $row) {
            $productNotesTotals[$row->year] = ['total_request_qty' => $row->total_request_qty ?? 0, 'total_produced_qty' => $row->total_produced_qty ?? 0  ];
        } 
        $return['product_notes'] = $productNotesTotals; 
        return $return;
    }

    function getmfgadmindashboarddetails($get){
        $company_id = intval($get['company_id']);
        $workOrders = $this->getManufacturingDashboardWorkOrders($company_id, '', "ORDER BY exception_rank ASC, w.planned_end_date ASC, w.transaction_date DESC, w.id DESC", "LIMIT 8");
        $allWorkOrders = $this->getManufacturingDashboardWorkOrders($company_id, '', '', '');
        $production = $this->buildManufacturingProductionStatusSummary($allWorkOrders);
        $approval = $this->getManufacturingApprovalStatusSummary($company_id);
        $material = $this->getManufacturingMaterialDashboardSummary($company_id, '');
        $resources = $this->getManufacturingResourceDashboardSummary($company_id, '');
        $qc = $this->getManufacturingQcDashboardSummary($company_id, '');
        $trend = $this->getManufacturingProductionTrendSummary($company_id);

        return array(
            'work_orders' => $workOrders,
            'summary' => array(
                'total_work_orders' => count((array)$allWorkOrders),
                'approval' => $approval,
                'production' => $production,
                'material' => $material,
                'resources' => $resources,
                'qc' => $qc,
                'delayed_orders' => $this->countManufacturingDashboardRows($allWorkOrders, 'is_delayed'),
                'exception_orders' => count((array)$workOrders),
                'planned_qty' => $this->sumManufacturingDashboardRows($allWorkOrders, 'planned_qty'),
                'completed_qty' => $this->sumManufacturingDashboardRows($allWorkOrders, 'completed_qty')
            ),
            'chart_data' => array(
                'statusLabels' => array('Not Started', 'Active', 'Hold', 'Completed'),
                'statusSeries' => array($production['not_started'], $production['active'], $production['hold'], $production['completed']),
                'stockLabels' => array('Required', 'Received', 'Shortage', 'Manpower'),
                'stockSeries' => array($material['required_qty'], $material['received_qty'], $material['shortage_qty'], $resources['manpower_required']),
                'trendLabels' => $trend['labels'],
                'trendRequest' => $trend['request'],
                'trendProduced' => $trend['produced'],
                'qcLabels' => array('Pending', 'Passed', 'Failed'),
                'qcSeries' => array($qc['pending'], $qc['passed'], $qc['failed'])
            )
        );
    }

    function getmfgplannerdashboarddetails($get){
        $company_id = intval($get['company_id']);
        $planningWhere = " AND IFNULL(w.work_log_status, -1) != 199 ";
        $workOrders = $this->getManufacturingDashboardWorkOrders($company_id, $planningWhere, "ORDER BY planning_risk_rank ASC, w.planned_start_date ASC, w.planned_end_date ASC, w.id DESC", "LIMIT 8");
        $allPlanningWorkOrders = $this->getManufacturingDashboardWorkOrders($company_id, $planningWhere, '', '');
        $conversion = $this->getManufacturingPlanningConversionSummary($company_id);
        $material = $this->getManufacturingMaterialDashboardSummary($company_id, $planningWhere);
        $resources = $this->getManufacturingResourceDashboardSummary($company_id, $planningWhere);
        $topProducts = $this->getManufacturingTopPlannedProducts($company_id);
        $trend = $this->getManufacturingProductionTrendSummary($company_id);
        $plannedQty = $this->sumManufacturingDashboardRows($allPlanningWorkOrders, 'planned_qty');
        $completedQty = $this->sumManufacturingDashboardRows($allPlanningWorkOrders, 'completed_qty');

        return array(
            'work_orders' => $workOrders,
            'summary' => array(
                'work_orders_planned' => count((array)$allPlanningWorkOrders),
                'planned_qty' => $plannedQty,
                'completed_qty' => $completedQty,
                'balance_qty' => max($plannedQty - $completedQty, 0),
                'material' => $material,
                'resources' => $resources,
                'conversion' => $conversion,
                'planning_risks' => count((array)$workOrders)
            ),
            'top_products' => $topProducts,
            'chart_data' => array(
                'planningLabels' => array('Forecast', 'Firm Order', 'Work Order', 'Produced'),
                'planningSeries' => array($conversion['forecast_qty'], $conversion['firm_order_qty'], $conversion['work_order_qty'], $conversion['produced_qty']),
                'trendLabels' => $trend['labels'],
                'trendRequest' => $trend['request'],
                'trendProduced' => $trend['produced'],
                'readinessLabels' => array('Required', 'Received', 'Shortage'),
                'readinessSeries' => array($material['required_qty'], $material['received_qty'], $material['shortage_qty']),
                'productLabels' => $topProducts['labels'],
                'productSeries' => $topProducts['series']
            )
        );
    }

    function getmfgsupervisordashboarddetails($get){
        $company_id = intval($get['company_id']);
        $supervisorWhere = " AND (w.work_log_status IN (0,1) OR w.planned_start_date <= CURDATE() OR DATE(w.actual_start_date) = CURDATE()) AND IFNULL(w.work_log_status, -1) != 199 ";
        $workOrders = $this->getManufacturingDashboardWorkOrders($company_id, $supervisorWhere, "ORDER BY supervisor_rank ASC, w.planned_start_date ASC, w.id DESC", "LIMIT 8");
        $activeWorkOrders = $this->getManufacturingDashboardWorkOrders($company_id, $supervisorWhere, '', '');
        $production = $this->buildManufacturingProductionStatusSummary($activeWorkOrders);
        $material = $this->getManufacturingMaterialDashboardSummary($company_id, $supervisorWhere);
        $resources = $this->getManufacturingResourceDashboardSummary($company_id, $supervisorWhere);
        $qc = $this->getManufacturingQcDashboardSummary($company_id, " AND wo.work_log_status = 199 ");
        $plannedQty = $this->sumManufacturingDashboardRows($activeWorkOrders, 'planned_qty');
        $completedQty = $this->sumManufacturingDashboardRows($activeWorkOrders, 'completed_qty');

        return array(
            'work_orders' => $workOrders,
            'summary' => array(
                'production' => $production,
                'planned_qty' => $plannedQty,
                'completed_qty' => $completedQty,
                'balance_qty' => max($plannedQty - $completedQty, 0),
                'completion_percent' => $plannedQty > 0 ? round(($completedQty / $plannedQty) * 100, 2) : 0,
                'material' => $material,
                'resources' => $resources,
                'qc' => $qc
            ),
            'chart_data' => array(
                'statusLabels' => array('Active', 'Hold', 'Completed'),
                'statusSeries' => array($production['active'], $production['hold'], $production['completed']),
                'orderLabels' => $this->pluckManufacturingDashboardRows($workOrders, 'transaction_no'),
                'plannedSeries' => $this->pluckManufacturingDashboardRows($workOrders, 'planned_qty'),
                'completedSeries' => $this->pluckManufacturingDashboardRows($workOrders, 'completed_qty'),
                'completionLabels' => array('Completed', 'Balance'),
                'completionSeries' => array($completedQty, max($plannedQty - $completedQty, 0)),
                'materialLabels' => array('Required', 'Received', 'Shortage'),
                'materialSeries' => array($material['required_qty'], $material['received_qty'], $material['shortage_qty'])
            )
        );
    }

    function getManufacturingDashboardWorkOrders($company_id, $extraWhere = '', $orderBy = '', $limit = ''){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT w.id AS work_order_id, w.transaction_no, w.transaction_date, w.work_order_item_id AS product_id,
            w.work_order_item_qty AS planned_qty, w.completed_qty,
            ROUND(IF(IFNULL(w.work_order_item_qty,0) > 0, (IFNULL(w.completed_qty,0) / w.work_order_item_qty) * 100, 0), 2) AS progress_percent,
            w.current_status AS approval_status_value, IFNULL(s.name, 'Not Updated') AS approval_status_name,
            w.work_log_status,
            CASE WHEN w.work_log_status = 0 THEN 'Active' WHEN w.work_log_status = 1 THEN 'Hold' WHEN w.work_log_status = 199 THEN 'Completed' ELSE 'Not Started' END AS production_status_name,
            i.sku AS product_sku, i.item_name AS product_name,
            s.background_color AS status_bg, s.color AS status_text_color,
            w.planned_start_date, w.planned_end_date, w.actual_start_date, w.actual_end_date,
            IFNULL(mat.required_qty,0) AS material_required_qty, IFNULL(mat.received_qty,0) AS material_received_qty,
            GREATEST(IFNULL(mat.required_qty,0) - IFNULL(mat.received_qty,0), 0) AS material_shortage_qty,
            IFNULL(ops.manpower_required,0) AS manpower_required,
            IFNULL(ops.machine_count,0) AS machine_count,
            IFNULL(ops.operation_minutes,0) AS operation_minutes,
            IFNULL(ops.operation_cost,0) AS operation_cost,
            IFNULL(mat.materials, '[]') AS materials,
            IFNULL(ops.operations, '[]') AS operations,
            CASE WHEN w.planned_end_date < CURDATE() AND IFNULL(w.completed_qty,0) < IFNULL(w.work_order_item_qty,0) THEN 1 ELSE 0 END AS is_delayed,
            CASE
                WHEN w.work_log_status = 1 THEN 1
                WHEN w.planned_end_date < CURDATE() AND IFNULL(w.completed_qty,0) < IFNULL(w.work_order_item_qty,0) THEN 2
                WHEN GREATEST(IFNULL(mat.required_qty,0) - IFNULL(mat.received_qty,0), 0) > 0 THEN 3
                WHEN w.current_status != ".ST_APPROVED." THEN 4
                ELSE 9
            END AS exception_rank,
            CASE
                WHEN GREATEST(IFNULL(mat.required_qty,0) - IFNULL(mat.received_qty,0), 0) > 0 THEN 1
                WHEN w.current_status != ".ST_APPROVED." THEN 2
                WHEN w.planned_start_date < CURDATE() AND IFNULL(w.completed_qty,0) < IFNULL(w.work_order_item_qty,0) THEN 3
                ELSE 9
            END AS planning_risk_rank,
            CASE
                WHEN w.work_log_status = 1 THEN 1
                WHEN w.work_log_status = 0 THEN 2
                WHEN w.planned_start_date <= CURDATE() THEN 3
                ELSE 9
            END AS supervisor_rank
            FROM manufacturing_work_orders w
            LEFT JOIN items i ON i.company_id = w.company_id AND i.id = w.work_order_item_id AND i.is_active = 0
            LEFT JOIN status s ON s.company_id = w.company_id AND s.name_value = w.current_status AND s.is_active = 0
            LEFT JOIN (
                SELECT wi.company_id, wi.transaction_id, SUM(IFNULL(wi.request_qty,0)) AS required_qty, SUM(IFNULL(wi.stock_receipts_qty,0)) AS received_qty,
                    CONCAT('[', IFNULL(GROUP_CONCAT(
                        CONCAT(
                            '{',
                                '\"item_id\":\"', wi.item_id, '\",',
                                '\"item_name\":\"', REPLACE(IFNULL(it.item_name, ''), '\"', '\\\"'), '\",',
                                '\"uom\":\"', REPLACE(IFNULL(u.uom_short_code, ''), '\"', '\\\"'), '\",',
                                '\"request_qty\":\"', IFNULL(wi.request_qty,0), '\",',
                                '\"bom_qty\":\"', IFNULL(wi.bom_qty,0), '\",',
                                '\"stock_request_qty\":\"', IFNULL(wi.stock_request_qty,0), '\",',
                                '\"stock_receipts_qty\":\"', IFNULL(wi.stock_receipts_qty,0), '\"',
                            '}'
                        ) ORDER BY it.item_name SEPARATOR ','
                    ), ''), ']') AS materials
                FROM manufacturing_work_order_items wi
                LEFT JOIN items it ON it.company_id = wi.company_id AND it.id = wi.item_id AND it.is_active = 0
                LEFT JOIN unit_of_measurements u ON u.company_id = wi.company_id AND u.id = wi.uom AND u.is_active = 0
                WHERE wi.company_id = '{$company_id}' AND wi.is_active = 0
                GROUP BY wi.company_id, wi.transaction_id
            ) mat ON mat.company_id = w.company_id AND mat.transaction_id = w.id
            LEFT JOIN (
                SELECT o.company_id, o.transaction_id, SUM(IFNULL(o.operation_manpower,0)) AS manpower_required,
                    COUNT(DISTINCT CASE WHEN IFNULL(o.manufacturing_operation_machine_id,0) > 0 THEN o.manufacturing_operation_machine_id END) AS machine_count,
                    SUM(IFNULL(o.operation_time,0)) AS operation_minutes, SUM(IFNULL(o.operation_total,0)) AS operation_cost,
                    CONCAT('[', IFNULL(GROUP_CONCAT(
                        CONCAT(
                            '{',
                                '\"operation_name\":\"', REPLACE(IFNULL(mo.operation_name, ''), '\"', '\\\"'), '\",',
                                '\"operation_time\":\"', IFNULL(o.operation_time,0), '\",',
                                '\"operation_manpower\":\"', IFNULL(o.operation_manpower,0), '\",',
                                '\"operation_note\":\"', REPLACE(IFNULL(o.operation_note, ''), '\"', '\\\"'), '\",',
                                '\"status\":\"', IFNULL(o.current_status,''), '\"',
                            '}'
                        ) ORDER BY o.id SEPARATOR ','
                    ), ''), ']') AS operations
                FROM manufacturing_work_order_operations o
                LEFT JOIN manufacturing_operations mo ON mo.company_id = o.company_id AND mo.id = o.manufacturing_operation_id AND mo.is_active = 0
                WHERE o.company_id = '{$company_id}' AND o.is_active = 0
                GROUP BY o.company_id, o.transaction_id
            ) ops ON ops.company_id = w.company_id AND ops.transaction_id = w.id
            WHERE w.company_id = '{$company_id}' AND w.is_active = 0 {$extraWhere} {$orderBy} {$limit}";
        return $callConfig->getAllRows($query);
    }

    function getManufacturingApprovalStatusSummary($company_id){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT IFNULL(s.name, CONCAT('Status ', w.current_status)) AS status_name, COUNT(*) AS total
            FROM manufacturing_work_orders w
            LEFT JOIN status s ON s.company_id = w.company_id AND s.name_value = w.current_status AND s.is_active = 0
            WHERE w.company_id = '{$company_id}' AND w.is_active = 0
            GROUP BY w.current_status, s.name
            ORDER BY total DESC";
        $rows = $callConfig->getAllRows($query);
        $labels = array();
        $series = array();
        foreach ((array)$rows as $row) {
            $labels[] = $row->status_name;
            $series[] = (int)$row->total;
        }
        return array('rows' => $rows, 'labels' => $labels, 'series' => $series);
    }

    function getManufacturingMaterialDashboardSummary($company_id, $workOrderWhere = ''){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT SUM(IFNULL(wi.request_qty,0)) AS required_qty, SUM(IFNULL(wi.stock_receipts_qty,0)) AS received_qty,
            SUM(GREATEST(IFNULL(wi.request_qty,0) - IFNULL(wi.stock_receipts_qty,0), 0)) AS shortage_qty,
            SUM(CASE WHEN IFNULL(wi.stock_receipts_qty,0) < IFNULL(wi.request_qty,0) THEN 1 ELSE 0 END) AS shortage_lines
            FROM manufacturing_work_order_items wi
            INNER JOIN manufacturing_work_orders w ON w.company_id = wi.company_id AND w.id = wi.transaction_id AND w.is_active = 0
            WHERE wi.company_id = '{$company_id}' AND wi.is_active = 0 {$workOrderWhere}";
        $row = $callConfig->getRow($query);
        return array(
            'required_qty' => (float)($row->required_qty ?? 0),
            'received_qty' => (float)($row->received_qty ?? 0),
            'shortage_qty' => (float)($row->shortage_qty ?? 0),
            'shortage_lines' => (int)($row->shortage_lines ?? 0)
        );
    }

    function getManufacturingResourceDashboardSummary($company_id, $workOrderWhere = ''){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT SUM(IFNULL(o.operation_manpower,0)) AS manpower_required,
            COUNT(DISTINCT CASE WHEN IFNULL(o.manufacturing_operation_machine_id,0) > 0 THEN o.manufacturing_operation_machine_id END) AS machine_required,
            SUM(IFNULL(o.operation_time,0)) AS operation_minutes,
            SUM(IFNULL(o.operation_total,0)) AS operation_cost
            FROM manufacturing_work_order_operations o
            INNER JOIN manufacturing_work_orders w ON w.company_id = o.company_id AND w.id = o.transaction_id AND w.is_active = 0
            WHERE o.company_id = '{$company_id}' AND o.is_active = 0 {$workOrderWhere}";
        $row = $callConfig->getRow($query);
        return array(
            'manpower_required' => (float)($row->manpower_required ?? 0),
            'machine_required' => (int)($row->machine_required ?? 0),
            'operation_minutes' => (float)($row->operation_minutes ?? 0),
            'operation_cost' => (float)($row->operation_cost ?? 0)
        );
    }

    function getManufacturingQcDashboardSummary($company_id, $workOrderWhere = ''){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT
            SUM(CASE WHEN IFNULL(qc.quality_check,0) = 0 THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN qc.quality_check = 1 THEN 1 ELSE 0 END) AS passed,
            SUM(CASE WHEN qc.quality_check = 2 THEN 1 ELSE 0 END) AS failed
            FROM application_table_quality_check qc
            INNER JOIN manufacturing_product_notes pn ON pn.company_id = qc.company_id AND pn.id = qc.transaction_id AND pn.is_active = 0
            LEFT JOIN manufacturing_work_orders wo ON wo.company_id = pn.company_id AND wo.id = pn.work_order_id AND wo.is_active = 0
            WHERE qc.company_id = '{$company_id}' AND qc.is_active = 0 AND qc.application_table_name = 'manufacturing_product_notes' {$workOrderWhere}";
        $row = $callConfig->getRow($query);
        return array(
            'pending' => (int)($row->pending ?? 0),
            'passed' => (int)($row->passed ?? 0),
            'failed' => (int)($row->failed ?? 0)
        );
    }

    function getManufacturingProductionTrendSummary($company_id){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS period_label,
            SUM(IFNULL(request_qty,0)) AS total_request_qty, SUM(IFNULL(produced_qty,0)) AS total_produced_qty
            FROM manufacturing_product_notes
            WHERE company_id = '{$company_id}' AND is_active = 0
            GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
            ORDER BY period_label ASC";
        $rows = $callConfig->getAllRows($query);
        $labels = array();
        $request = array();
        $produced = array();
        foreach ((array)$rows as $row) {
            $labels[] = $row->period_label;
            $request[] = (float)$row->total_request_qty;
            $produced[] = (float)$row->total_produced_qty;
        }
        if (empty($labels)) {
            $labels = array('No Production');
            $request = array(0);
            $produced = array(0);
        }
        return array('labels' => $labels, 'request' => $request, 'produced' => $produced);
    }

    function getManufacturingPlanningConversionSummary($company_id){
        global $callConfig;
        $company_id = intval($company_id);
        $forecast = $callConfig->getRow("SELECT SUM(IFNULL(fi.total_forecast,0)) AS qty
            FROM manufacturing_forecast_items fi
            INNER JOIN manufacturing_forecast f ON f.company_id = fi.company_id AND f.id = fi.manufacturing_forecast_id AND f.is_active = 0
            WHERE fi.company_id = '{$company_id}' AND fi.is_active = 0");
        $firm = $callConfig->getRow("SELECT SUM(IFNULL(foi.firm_qty_1,0) + IFNULL(foi.firm_qty_2,0) + IFNULL(foi.firm_qty_3,0)) AS qty
            FROM manufacturing_firm_order_items foi
            INNER JOIN manufacturing_firm_orders fo ON fo.company_id = foi.company_id AND fo.id = foi.manufacturing_firm_order_id AND fo.is_active = 0
            WHERE foi.company_id = '{$company_id}' AND foi.is_active = 0");
        $workOrder = $callConfig->getRow("SELECT SUM(IFNULL(work_order_item_qty,0)) AS planned_qty, SUM(IFNULL(completed_qty,0)) AS completed_qty FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND is_active = 0");
        return array(
            'forecast_qty' => (float)($forecast->qty ?? 0),
            'firm_order_qty' => (float)($firm->qty ?? 0),
            'work_order_qty' => (float)($workOrder->planned_qty ?? 0),
            'produced_qty' => (float)($workOrder->completed_qty ?? 0)
        );
    }

    function getManufacturingTopPlannedProducts($company_id){
        global $callConfig;
        $company_id = intval($company_id);
        $query = "SELECT IFNULL(i.item_name, 'Product') AS product_name, SUM(IFNULL(w.work_order_item_qty,0)) AS planned_qty
            FROM manufacturing_work_orders w
            LEFT JOIN items i ON i.company_id = w.company_id AND i.id = w.work_order_item_id AND i.is_active = 0
            WHERE w.company_id = '{$company_id}' AND w.is_active = 0
            GROUP BY w.work_order_item_id, i.item_name
            ORDER BY planned_qty DESC
            LIMIT 6";
        $rows = $callConfig->getAllRows($query);
        $labels = array();
        $series = array();
        foreach ((array)$rows as $row) {
            $labels[] = $row->product_name;
            $series[] = (float)$row->planned_qty;
        }
        return array('rows' => $rows, 'labels' => $labels, 'series' => $series);
    }

    function buildManufacturingProductionStatusSummary($rows){
        $summary = array('not_started' => 0, 'active' => 0, 'hold' => 0, 'completed' => 0);
        foreach ((array)$rows as $row) {
            if ((string)($row->work_log_status ?? '') === '0') {
                $summary['active']++;
            } else if ((string)($row->work_log_status ?? '') === '1') {
                $summary['hold']++;
            } else if ((string)($row->work_log_status ?? '') === '199') {
                $summary['completed']++;
            } else {
                $summary['not_started']++;
            }
        }
        return $summary;
    }

    function sumManufacturingDashboardRows($rows, $field){
        $total = 0;
        foreach ((array)$rows as $row) {
            $total += (float)($row->$field ?? 0);
        }
        return $total;
    }

    function countManufacturingDashboardRows($rows, $field){
        $total = 0;
        foreach ((array)$rows as $row) {
            if (!empty($row->$field)) {
                $total++;
            }
        }
        return $total;
    }

    function pluckManufacturingDashboardRows($rows, $field){
        $values = array();
        foreach ((array)$rows as $row) {
            $values[] = $row->$field ?? '';
        }
        return $values;
    }

    function getManufacturingProductionSummaryReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'pn.transaction_date');
        $query = "SELECT pn.id, pn.transaction_no AS production_note_no, pn.transaction_date,
            wo.transaction_no AS work_order_no, i.item_name AS product_name, i.sku AS product_sku,
            pn.request_qty AS planned_qty, pn.produced_qty, pn.wastage_qty,
            GREATEST(IFNULL(pn.request_qty,0) - IFNULL(pn.produced_qty,0), 0) AS pending_qty,
            ROUND(IF(IFNULL(pn.request_qty,0) > 0, (IFNULL(pn.produced_qty,0) / pn.request_qty) * 100, 0), 2) AS achievement_percent,
            ROUND(IF(IFNULL(pn.produced_qty,0) > 0, (IFNULL(pn.wastage_qty,0) / pn.produced_qty) * 100, 0), 2) AS wastage_percent,
            pn.remarks, pn.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_product_notes pn
            LEFT JOIN manufacturing_work_orders wo ON wo.company_id = pn.company_id AND wo.id = pn.work_order_id AND wo.is_active = 0
            LEFT JOIN items i ON i.company_id = pn.company_id AND i.id = pn.item_id AND i.is_active = 0
            LEFT JOIN status s ON s.company_id = pn.company_id AND s.name_value = pn.current_status AND s.is_active = 0
            WHERE pn.company_id = '{$company_id}' AND pn.is_active = 0 {$dateWhere}
            ORDER BY pn.transaction_date DESC, pn.id DESC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows, 'summary' => $this->buildManufacturingProductionNoteSummary($rows));
    }

    function getManufacturingWorkOrderStatusReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'w.transaction_date');
        $query = "SELECT w.id, w.transaction_no, w.transaction_date, i.item_name AS product_name, i.sku AS product_sku,
            w.work_order_item_qty AS planned_qty, w.completed_qty,
            ROUND(IF(IFNULL(w.work_order_item_qty,0) > 0, (IFNULL(w.completed_qty,0) / w.work_order_item_qty) * 100, 0), 2) AS progress_percent,
            w.planned_start_date, w.planned_end_date, w.actual_start_date, w.actual_end_date, w.work_log_status,
            w.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_work_orders w
            LEFT JOIN items i ON i.company_id = w.company_id AND i.id = w.work_order_item_id AND i.is_active = 0
            LEFT JOIN status s ON s.company_id = w.company_id AND s.name_value = w.current_status AND s.is_active = 0
            WHERE w.company_id = '{$company_id}' AND w.is_active = 0 {$dateWhere}
            ORDER BY w.current_status ASC, w.transaction_date DESC, w.id DESC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows, 'summary' => $this->buildManufacturingWorkOrderSummary($rows));
    }

    function getManufacturingMaterialRequirementConsumptionReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'wo.transaction_date');
        $query = "SELECT pnc.production_note_no, pnc.production_note_date AS transaction_date, wo.transaction_no AS work_order_no,
            fg.item_name AS product_name, rm.item_name AS material_name,
            wi.request_qty AS required_qty, wi.bom_qty, wi.stock_request_qty, wi.stock_receipts_qty,
            IFNULL(pnc.production_received_qty,0) AS production_received_qty, IFNULL(pnc.consumed_qty,0) AS consumed_qty, IFNULL(pnc.wastage_qty,0) AS wastage_qty,
            GREATEST(IFNULL(wi.request_qty,0) - IFNULL(pnc.consumed_qty,0), 0) AS variance_qty,
            CONCAT(IFNULL(u.uom_short_code,''), IF(u.uom_name IS NULL OR u.uom_name = '', '', CONCAT(' - ', u.uom_name))) AS uom_name,
            s.name AS work_order_status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_work_order_items wi
            INNER JOIN manufacturing_work_orders wo ON wo.company_id = wi.company_id AND wo.id = wi.transaction_id AND wo.is_active = 0
            LEFT JOIN (
                SELECT pni.company_id, pni.work_order_id, pni.item_id,
                    GROUP_CONCAT(DISTINCT pn.transaction_no ORDER BY pn.transaction_date DESC SEPARATOR ', ') AS production_note_no,
                    MAX(pn.transaction_date) AS production_note_date,
                    SUM(IFNULL(pni.received_qty,0)) AS production_received_qty,
                    SUM(IFNULL(pni.produced_qty,0)) AS consumed_qty,
                    SUM(IFNULL(pni.wastage_qty,0)) AS wastage_qty
                FROM manufacturing_product_note_items pni
                INNER JOIN manufacturing_product_notes pn ON pn.company_id = pni.company_id AND pn.id = pni.transaction_id AND pn.is_active = 0
                WHERE pni.company_id = '{$company_id}' AND pni.is_active = 0
                GROUP BY pni.company_id, pni.work_order_id, pni.item_id
            ) pnc ON pnc.company_id = wi.company_id AND pnc.work_order_id = wi.transaction_id AND pnc.item_id = wi.item_id
            LEFT JOIN items fg ON fg.company_id = wo.company_id AND fg.id = wo.work_order_item_id AND fg.is_active = 0
            LEFT JOIN items rm ON rm.company_id = wi.company_id AND rm.id = wi.item_id AND rm.is_active = 0
            LEFT JOIN unit_of_measurements u ON u.company_id = wi.company_id AND u.id = wi.uom AND u.is_active = 0
            LEFT JOIN status s ON s.company_id = wo.company_id AND s.name_value = wo.current_status AND s.is_active = 0
            WHERE wi.company_id = '{$company_id}' AND wi.is_active = 0 {$dateWhere}
            ORDER BY wo.transaction_date DESC, wo.id DESC, rm.item_name ASC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows);
    }

    function getManufacturingStockRequestTransferReceiptReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'sr.transaction_date');
        $query = "SELECT sr.id AS stock_request_id, sr.transaction_number AS stock_request_no, sr.transaction_date AS stock_request_date,
            sr.ref_no AS work_order_no, sri.id AS stock_request_item_id, sri.item_name AS material_name,
            sri.item_quantity AS requested_qty,
            IFNULL(trn.transfer_qty,0) AS transferred_qty,
            IFNULL(rec.receipt_qty,0) AS received_qty,
            GREATEST(IFNULL(sri.item_quantity,0) - IFNULL(trn.transfer_qty,0), 0) AS pending_transfer_qty,
            GREATEST(IFNULL(trn.transfer_qty,0) - IFNULL(rec.receipt_qty,0), 0) AS pending_receipt_qty,
            trn.transfer_numbers, trn.transfer_dates, rec.receipt_numbers, rec.receipt_dates,
            CONCAT(IFNULL(u.uom_short_code,''), IF(u.uom_name IS NULL OR u.uom_name = '', '', CONCAT(' - ', u.uom_name))) AS uom_name,
            s.name AS request_status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM stock_requests sr
            INNER JOIN stkreq_items sri ON sri.company_id = sr.company_id AND sri.transaction_id = sr.id AND sri.is_active = 0
            LEFT JOIN (
                SELECT st.company_id, st.ref_id AS stock_request_id, sti.ref_transaction_item_table_id AS stock_request_item_id,
                    SUM(sti.item_quantity) AS transfer_qty,
                    GROUP_CONCAT(DISTINCT st.transaction_number ORDER BY st.transaction_date DESC SEPARATOR ', ') AS transfer_numbers,
                    GROUP_CONCAT(DISTINCT DATE_FORMAT(st.transaction_date, '%d-%m-%Y') ORDER BY st.transaction_date DESC SEPARATOR ', ') AS transfer_dates
                FROM stock_transfers st
                INNER JOIN stktrn_items sti ON sti.company_id = st.company_id AND sti.transaction_id = st.id AND sti.is_active = 0
                WHERE st.company_id = '{$company_id}' AND st.ref_type = 'stock_requests' AND st.is_active = 0
                GROUP BY st.company_id, st.ref_id, sti.ref_transaction_item_table_id
            ) trn ON trn.company_id = sr.company_id AND trn.stock_request_id = sr.id AND trn.stock_request_item_id = sri.id
            LEFT JOIN (
                SELECT strc.company_id, st.ref_id AS stock_request_id, sti.ref_transaction_item_table_id AS stock_request_item_id,
                    SUM(sri2.item_quantity) AS receipt_qty,
                    GROUP_CONCAT(DISTINCT strc.transaction_number ORDER BY strc.transaction_date DESC SEPARATOR ', ') AS receipt_numbers,
                    GROUP_CONCAT(DISTINCT DATE_FORMAT(strc.transaction_date, '%d-%m-%Y') ORDER BY strc.transaction_date DESC SEPARATOR ', ') AS receipt_dates
                FROM stock_transfer_receipts strc
                INNER JOIN stkrec_items sri2 ON sri2.company_id = strc.company_id AND sri2.transaction_id = strc.id AND sri2.is_active = 0
                INNER JOIN stktrn_items sti ON sti.company_id = sri2.company_id AND sti.id = sri2.ref_transaction_item_table_id AND sti.is_active = 0
                INNER JOIN stock_transfers st ON st.company_id = strc.company_id AND st.id = strc.ref_id AND st.is_active = 0
                WHERE strc.company_id = '{$company_id}' AND strc.ref_type = 'stock_transfers' AND strc.is_active = 0
                GROUP BY strc.company_id, st.ref_id, sti.ref_transaction_item_table_id
            ) rec ON rec.company_id = sr.company_id AND rec.stock_request_id = sr.id AND rec.stock_request_item_id = sri.id
            LEFT JOIN unit_of_measurements u ON u.company_id = sri.company_id AND u.id = sri.purchase_uom AND u.is_active = 0
            LEFT JOIN status s ON s.company_id = sr.company_id AND s.name_value = sr.current_status AND s.is_active = 0
            WHERE sr.company_id = '{$company_id}' AND sr.ref_type = 'manufacturing_work_orders' AND sr.is_active = 0 {$dateWhere}
            ORDER BY sr.transaction_date DESC, sr.id DESC, sri.id ASC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows);
    }

    function getManufacturingProductionPerformanceReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'pn.transaction_date');
        $query = "SELECT pn.transaction_date, pn.transaction_no AS production_note_no, wo.transaction_no AS work_order_no,
            i.item_name AS product_name, i.sku AS product_sku, pn.request_qty AS planned_qty, pn.produced_qty, pn.wastage_qty,
            ROUND(IF(IFNULL(pn.request_qty,0) > 0, (IFNULL(pn.produced_qty,0) / pn.request_qty) * 100, 0), 2) AS performance_percent,
            ROUND(IF(IFNULL(pn.produced_qty,0) > 0, (IFNULL(pn.wastage_qty,0) / pn.produced_qty) * 100, 0), 2) AS wastage_percent,
            DATE_FORMAT(pn.transaction_date, '%Y-%m') AS production_month,
            s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_product_notes pn
            LEFT JOIN manufacturing_work_orders wo ON wo.company_id = pn.company_id AND wo.id = pn.work_order_id AND wo.is_active = 0
            LEFT JOIN items i ON i.company_id = pn.company_id AND i.id = pn.item_id AND i.is_active = 0
            LEFT JOIN status s ON s.company_id = pn.company_id AND s.name_value = pn.current_status AND s.is_active = 0
            WHERE pn.company_id = '{$company_id}' AND pn.is_active = 0 {$dateWhere}
            ORDER BY pn.transaction_date DESC, pn.id DESC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows, 'summary' => $this->buildManufacturingProductionNoteSummary($rows));
    }

    function getManufacturingBomReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'b.transaction_date');
        $query = "SELECT b.id, b.transaction_no, b.transaction_date, fg.item_name AS product_name,
            b.bom_quantity, b.bom_amount, rm.item_name AS material_name, bi.quantity AS material_qty,
            bi.rate, bi.amount, bi.scrap,
            CONCAT(IFNULL(u.uom_short_code,''), IF(u.uom_name IS NULL OR u.uom_name = '', '', CONCAT(' - ', u.uom_name))) AS uom_name,
            s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_boms b
            LEFT JOIN items fg ON fg.company_id = b.company_id AND fg.id = b.bom_item_id AND fg.is_active = 0
            LEFT JOIN manufacturing_bom_items bi ON bi.company_id = b.company_id AND bi.manufacturing_bom_id = b.id AND bi.is_active = 0
            LEFT JOIN items rm ON rm.company_id = bi.company_id AND rm.id = bi.item_id AND rm.is_active = 0
            LEFT JOIN unit_of_measurements u ON u.company_id = bi.company_id AND u.id = bi.uom AND u.is_active = 0
            LEFT JOIN status s ON s.company_id = b.company_id AND s.name_value = b.current_status AND s.is_active = 0
            WHERE b.company_id = '{$company_id}' AND b.is_active = 0 {$dateWhere}
            ORDER BY b.transaction_date DESC, b.id DESC, rm.item_name ASC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows);
    }

    function getManufacturingQcSummaryRejectionReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'pn.transaction_date');
        $query = "SELECT qc.id AS qc_id, qc.application_table_name, qc.transaction_id, qc.quality_check,
            qc.remarks AS qc_remarks, qci.serial_lot_no, qci.lot_qty, qci.qc_status, qci.required_count,
            qci.checked_count, qci.failed_count, qci.inspected_date, qci.remarks AS item_remarks,
            pn.transaction_no AS production_note_no, pn.transaction_date AS production_note_date, pn.produced_qty, pn.wastage_qty,
            wo.transaction_no AS work_order_no, fg.item_name AS product_name,
            s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM application_table_quality_check qc
            LEFT JOIN application_table_quality_check_items qci ON qci.company_id = qc.company_id AND qci.application_table_quality_check_id = qc.id AND qci.is_active = 0
            INNER JOIN manufacturing_product_notes pn ON pn.company_id = qc.company_id AND pn.id = qc.transaction_id AND pn.is_active = 0
            LEFT JOIN manufacturing_work_orders wo ON wo.company_id = pn.company_id AND wo.id = pn.work_order_id AND wo.is_active = 0
            LEFT JOIN items fg ON fg.company_id = pn.company_id AND fg.id = pn.item_id AND fg.is_active = 0
            LEFT JOIN status s ON s.company_id = qc.company_id AND s.name_value = qc.current_status AND s.is_active = 0
            WHERE qc.company_id = '{$company_id}' AND qc.is_active = 0 AND qc.application_table_name = 'manufacturing_product_notes' {$dateWhere}
            ORDER BY qc.id DESC, qci.id ASC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows);
    }

    function getManufacturingWorkOrderTimelineDelayReport($get){
        global $callConfig;
        $company_id = intval($get['company_id']);
        $dateWhere = $this->getManufacturingReportDateWhere($get, 'w.transaction_date');
        $query = "SELECT w.id, w.transaction_no, w.transaction_date, i.item_name AS product_name,
            w.planned_start_date, w.planned_end_date, w.actual_start_date, w.actual_end_date,
            DATEDIFF(IFNULL(NULLIF(w.actual_end_date,'0000-00-00'), CURDATE()), w.planned_end_date) AS delay_days,
            MIN(CASE WHEN p.production_type = 'START' THEN p.date_time END) AS production_start_time,
            MAX(CASE WHEN p.production_type = 'HOLD' THEN p.date_time END) AS last_hold_time,
            MAX(CASE WHEN p.production_type = 'COMPLETED' THEN p.date_time END) AS production_complete_time,
            sr.stock_request_no, sr.stock_request_date,
            st.first_transfer_no, st.first_transfer_date, st.transfer_count,
            strc.first_receipt_no, strc.first_receipt_date, strc.receipt_count,
            pn.production_note_no, pn.production_note_date,
            qc.qc_id, qc.qc_date, qc.qc_status,
            s.name AS status_name, s.background_color AS status_bg, s.color AS status_text_color
            FROM manufacturing_work_orders w
            LEFT JOIN items i ON i.company_id = w.company_id AND i.id = w.work_order_item_id AND i.is_active = 0
            LEFT JOIN manufacturing_production p ON p.company_id = w.company_id AND p.work_order_id = w.id AND p.is_active = 0
            LEFT JOIN (
                SELECT company_id, ref_id AS work_order_id, MIN(transaction_number) AS stock_request_no, MIN(transaction_date) AS stock_request_date
                FROM stock_requests
                WHERE company_id = '{$company_id}' AND ref_type = 'manufacturing_work_orders' AND is_active = 0
                GROUP BY company_id, ref_id
            ) sr ON sr.company_id = w.company_id AND sr.work_order_id = w.id
            LEFT JOIN (
                SELECT st.company_id, sr.ref_id AS work_order_id, MIN(st.transaction_number) AS first_transfer_no, MIN(st.transaction_date) AS first_transfer_date, COUNT(DISTINCT st.id) AS transfer_count
                FROM stock_transfers st
                INNER JOIN stock_requests sr ON sr.company_id = st.company_id AND sr.id = st.ref_id AND sr.ref_type = 'manufacturing_work_orders' AND sr.is_active = 0
                WHERE st.company_id = '{$company_id}' AND st.ref_type = 'stock_requests' AND st.is_active = 0
                GROUP BY st.company_id, sr.ref_id
            ) st ON st.company_id = w.company_id AND st.work_order_id = w.id
            LEFT JOIN (
                SELECT strc.company_id, sr.ref_id AS work_order_id, MIN(strc.transaction_number) AS first_receipt_no, MIN(strc.transaction_date) AS first_receipt_date, COUNT(DISTINCT strc.id) AS receipt_count
                FROM stock_transfer_receipts strc
                INNER JOIN stock_transfers st ON st.company_id = strc.company_id AND st.id = strc.ref_id AND st.ref_type = 'stock_requests' AND st.is_active = 0
                INNER JOIN stock_requests sr ON sr.company_id = st.company_id AND sr.id = st.ref_id AND sr.ref_type = 'manufacturing_work_orders' AND sr.is_active = 0
                WHERE strc.company_id = '{$company_id}' AND strc.ref_type = 'stock_transfers' AND strc.is_active = 0
                GROUP BY strc.company_id, sr.ref_id
            ) strc ON strc.company_id = w.company_id AND strc.work_order_id = w.id
            LEFT JOIN (
                SELECT company_id, work_order_id, MIN(transaction_no) AS production_note_no, MIN(transaction_date) AS production_note_date
                FROM manufacturing_product_notes
                WHERE company_id = '{$company_id}' AND is_active = 0
                GROUP BY company_id, work_order_id
            ) pn ON pn.company_id = w.company_id AND pn.work_order_id = w.id
            LEFT JOIN (
                SELECT pn.company_id, pn.work_order_id, MIN(qc.id) AS qc_id, MIN(qc.created_date) AS qc_date, GROUP_CONCAT(DISTINCT qc.quality_check ORDER BY qc.quality_check SEPARATOR ', ') AS qc_status
                FROM manufacturing_product_notes pn
                INNER JOIN application_table_quality_check qc ON qc.company_id = pn.company_id AND qc.transaction_id = pn.id AND qc.application_table_name = 'manufacturing_product_notes' AND qc.is_active = 0
                WHERE pn.company_id = '{$company_id}' AND pn.is_active = 0
                GROUP BY pn.company_id, pn.work_order_id
            ) qc ON qc.company_id = w.company_id AND qc.work_order_id = w.id
            LEFT JOIN status s ON s.company_id = w.company_id AND s.name_value = w.current_status AND s.is_active = 0
            WHERE w.company_id = '{$company_id}' AND w.is_active = 0 {$dateWhere}
            GROUP BY w.id
            ORDER BY w.transaction_date DESC, w.id DESC";
        $rows = $callConfig->getAllRows($query);
        return array('data' => $rows);
    }

    function getManufacturingReportDateWhere($get, $fieldName){
        $where = '';
        if (!empty($get['from_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $get['from_date'])) {
            $where .= " AND {$fieldName} >= '".$get['from_date']."'";
        }
        if (!empty($get['to_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $get['to_date'])) {
            $where .= " AND {$fieldName} <= '".$get['to_date']."'";
        }
        return $where;
    }

    function buildManufacturingWorkOrderSummary($rows){
        $summary = array('total_work_orders' => 0, 'planned_qty' => 0, 'produced_qty' => 0, 'pending_qty' => 0);
        foreach ((array)$rows as $row) {
            $row = (array)$row;
            $planned = floatval($row['planned_qty'] ?? 0);
            $produced = floatval($row['produced_qty'] ?? $row['completed_qty'] ?? 0);
            $summary['total_work_orders']++;
            $summary['planned_qty'] += $planned;
            $summary['produced_qty'] += $produced;
            $summary['pending_qty'] += max($planned - $produced, 0);
        }
        $summary['achievement_percent'] = $summary['planned_qty'] > 0 ? round(($summary['produced_qty'] / $summary['planned_qty']) * 100, 2) : 0;
        return $summary;
    }

    function buildManufacturingProductionNoteSummary($rows){
        $summary = array('total_work_orders' => 0, 'planned_qty' => 0, 'produced_qty' => 0, 'pending_qty' => 0, 'wastage_qty' => 0);
        foreach ((array)$rows as $row) {
            $row = (array)$row;
            $planned = floatval($row['planned_qty'] ?? $row['request_qty'] ?? 0);
            $produced = floatval($row['produced_qty'] ?? 0);
            $summary['total_work_orders']++;
            $summary['planned_qty'] += $planned;
            $summary['produced_qty'] += $produced;
            $summary['pending_qty'] += max($planned - $produced, 0);
            $summary['wastage_qty'] += floatval($row['wastage_qty'] ?? 0);
        }
        $summary['achievement_percent'] = $summary['planned_qty'] > 0 ? round(($summary['produced_qty'] / $summary['planned_qty']) * 100, 2) : 0;
        $summary['wastage_percent'] = $summary['produced_qty'] > 0 ? round(($summary['wastage_qty'] / $summary['produced_qty']) * 100, 2) : 0;
        return $summary;
    }

    // added by koti start
    function getmanufacturingworkordersdetails($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $work_order_id = $get['work_order_id'] ?? 0;
        // Fetch all active work orders for the company
        $query = "SELECT mwo.id,mwo.transaction_no
            FROM manufacturing_work_orders mwo
            WHERE mwo.company_id = '{$company_id}' 
                AND mwo.is_active = 0 AND current_status=199
                AND IFNULL(mwo.completed_qty, 0) < IFNULL(mwo.work_order_item_qty, 0)
            ORDER BY mwo.id DESC";
        $res = $callConfig->getAllRows($query);
        $return['data'] = $res ?? array();

        $production_note_id = $get['production_note_id'] ?? 0;
        
        if($production_note_id != '' && $production_note_id != 0){
            $query = "SELECT mpn.transaction_no, mpn.transaction_date, mpn.work_order_id, mpn.item_id, mpn.request_qty, mpn.produced_qty, mpn.wastage_qty, mpn.remarks, i.item_name
                FROM manufacturing_product_notes mpn
                LEFT JOIN items i 
                    ON i.id = mpn.item_id 
                    AND i.company_id = mpn.company_id
                WHERE mpn.company_id = '{$company_id}'
                    AND mpn.id = '{$production_note_id}'
                    AND mpn.is_active = 0";
            $production_note_res = $callConfig->getRow($query);
            $return['production_note_res'] = $production_note_res;
            $work_order_id = $production_note_res->work_order_id;

            $query2 = "SELECT mpni.id, mpni.transaction_id, mpni.work_order_id, mpni.item_id, mpni.received_qty, mpni.produced_qty, mpni.wastage_qty, i.item_name
                FROM manufacturing_product_note_items mpni
                LEFT JOIN items i ON i.id = mpni.item_id 
                    AND i.company_id = mpni.company_id
                WHERE mpni.company_id = '{$company_id}' 
                    AND mpni.transaction_id = '{$production_note_id}' 
                    AND mpni.is_active = 0";
            $items = $callConfig->getAllRows($query2);
            $return['product_note_items'] = $items;
        }
        if($work_order_id != '' && $work_order_id != 0){
            // ===== 1. Get Work Order Master =====
            $query = "SELECT mwo.id,mwo.transaction_no,mwo.transaction_date,mwo.work_order_item_qty,mwo.completed_qty,mwo.planned_start_date,mwo.planned_end_date,mwo.work_log_status,i.item_name, mwo.work_order_item_id
                FROM manufacturing_work_orders mwo
                LEFT JOIN items i 
                    ON i.id = mwo.work_order_item_id 
                    AND i.company_id = mwo.company_id
                WHERE mwo.company_id = '{$company_id}'
                    AND mwo.id = '{$work_order_id}'
                    AND mwo.is_active = 0";
            $work_order = $callConfig->getRow($query);
            $return['work_order'] = $work_order;

            // ===== 2. Get Work Order Items =====
            $query2 = "SELECT mwoi.id,i.item_name,mwoi.request_qty,mwoi.stock_request_qty, mwoi.stock_receipts_qty, mwoi.completed_qty, i.item_name, concat(uom.uom_short_code,'-',uom.uom_name) as unit, mwoi.item_id
                FROM manufacturing_work_order_items mwoi
                LEFT JOIN items i ON i.id = mwoi.item_id 
                    AND i.company_id = mwoi.company_id
                LEFT JOIN unit_of_measurements uom ON uom.id = mwoi.uom 
                AND i.company_id = mwoi.company_id
                WHERE mwoi.company_id = '{$company_id}' 
                    AND mwoi.transaction_id = '{$work_order_id}' 
                    AND mwoi.is_active = 0";
            $items = $callConfig->getAllRows($query2);
            $return['work_order_items'] = $items;
        }
        return $return;
    }
    function insertmanufacturingproductnote($get, $post){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'] ?? $post['company_id'] ?? 0;
        $user_id    = $get['user_id'] ?? $post['created_by'] ?? $post['user_id'] ?? 0;
        $note_id    = intval($post['production_note_id'] ?? 0);
        $skip_work_order_updates = !empty($post['skip_work_order_updates']);
        $validation = $this->validateManufacturingProductNote($company_id, $post, $note_id);
        if ($validation['status'] != 'success') {
            return array(array('status' => 'fail', 'message' => $validation['message']));
        }
        // ===== CASE 1: UPDATE EXISTING RECORD =====
        if ($note_id > 0) {
            // --- Update master record ---
            $record = array(
                'request_qty'   => floatval($post['request_qty']),
                'produced_qty'  => floatval($post['produced_qty']),
                'wastage_qty'   => floatval($post['wastage_qty']),
                'remarks'       => $post['remarks'],
                'updated_by'    => $user_id,
                'updated_date'  => date('Y-m-d H:i:s')
            );

            $callConfig->updateRecord('manufacturing_product_notes', $record, 'id', $note_id);
            // --- Soft delete existing child records ---
            // $callConfig->executeQuery("UPDATE manufacturing_product_note_items 
            //                    SET is_active = NULL 
            //                    WHERE company_id = '{$company_id}' 
            //                    AND transaction_id = '{$note_id}'");
            // --- Reinsert / Update child items ---
            if (!empty($post['note_items']) && is_array($post['note_items'])) {
                foreach ($post['note_items'] as $row) {
                    $item_id = intval($row['id'] ?? 0);
                    $child = array(
                        'received_qty'   => floatval($row['received_qty']),
                        'produced_qty'   => floatval($row['produced_qty']),
                        'wastage_qty'    => floatval($row['wastage_qty']),
                        'updated_by'     => $user_id,
                        'updated_date'   => date('Y-m-d H:i:s')
                    );
                    // --- Update existing item record ---
                    $callConfig->updateRecord('manufacturing_product_note_items', $child, 'id', $item_id); 
                }
            }
            $return[] = array('status' => 'success', 'message' => 'Product Note updated successfully');
        }
        // ===== CASE 2: INSERT NEW RECORD =====
        else {
            $whr = "application_table_name = 'manufacturing_product_notes' ";
            $query = $callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS, '*', $whr, '', '', '');
            // $return['query'] = $query;
            $query_res = $callConfig->getRow($query);
            $res = $query_res;
            $le = $res->doc_no_length;
            $var = "%0" . $le . "d";
            $data = $res->pre_fix . sprintf($var, $res->serial_number+1) . $res->post_fix;
            $return['serialCode'] = trim($data);

            $transaction_no = trim($data);
            // ===== Insert into manufacturing_product_notes (Master) =====
            $record = array(
                'company_id'       => $company_id,
                'transaction_no'   => $transaction_no,
                'transaction_date' => date('Y-m-d'),
                'work_order_id'    => $post['work_order_id'],
                'item_id'          => $post['item_id'],
                'request_qty'      => floatval($post['request_qty']),
                'produced_qty'     => floatval($post['produced_qty']),
                'wastage_qty'      => floatval($post['wastage_qty']),
                'remarks'          => $post['remarks'],
                'is_active'        => 0,
                'current_status'   => ST_APPROVED,
                'created_by'       => $user_id,
                'created_date'     => date('Y-m-d H:i:s')
            );

            $result = $callConfig->insertRecord('manufacturing_product_notes', $record);
            $return[] = $result;
            $note_id = $result['LASTID'];
            $return['product_note_id'] = $note_id;

            if (isset($res->id)) {
                $update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS, array('serial_number' => $res->serial_number + 1), 'id', $res->id);
            }
            // ===== Insert into manufacturing_product_note_items (Child Loop) =====
            if (count((array)$post['note_items'])>0) {
                foreach ($post['note_items'] as $row) {
                    $child = array(
                        'company_id'     => $company_id,
                        'transaction_id' => $note_id,
                        'work_order_id'  => $post['work_order_id'],
                        'item_id'        => $row['item_id'],
                        'received_qty'   => floatval($row['received_qty']),
                        'produced_qty'   => floatval($row['produced_qty']),
                        'wastage_qty'    => floatval($row['wastage_qty']),
                        'is_active'      => 0,
                        'current_status' => ST_APPROVED,
                        'created_by'     => $user_id,
                        'created_date'   => date('Y-m-d H:i:s')
                    );

                   $res =  $callConfig->insertRecord('manufacturing_product_note_items', $child);
                   $return[] = $res;
                }
            }

            if (($post['qc_required'] ?? '0') == '1') {
                $this->saveManufacturingProductNoteQcHeader($company_id, $user_id, $note_id, $post['remarks'] ?? '');
            }

            $this->saveManufacturingProductNoteSerialLot($company_id, $user_id, $note_id, intval($post['item_id']), $post);
            $return['inventory_posting'] = $this->postManufacturingProductNoteInventory($company_id, $user_id, $note_id, $post);

            if (!$skip_work_order_updates) {
                $this->applyManufacturingProductNoteWorkOrderProgress($company_id, $user_id, $post);
            }
        } 

        return $return;
    }

    function postManufacturingProductNoteInventory($company_id, $user_id, $note_id, $post){
        global $callConfig;
        $return = array('status' => 'skipped', 'message' => 'Inventory posting not required');
        $work_order_id = intval($post['work_order_id'] ?? 0);
        $product_id = intval($post['item_id'] ?? 0);
        $produced_qty = floatval($post['produced_qty'] ?? 0);
        if ($company_id <= 0 || $note_id <= 0 || $work_order_id <= 0 || $product_id <= 0 || $produced_qty <= 0) {
            return $return;
        }

        $qc_header = $callConfig->getRow("SELECT quality_check FROM application_table_quality_check WHERE company_id = '{$company_id}' AND application_table_name = 'manufacturing_product_notes' AND transaction_id = '{$note_id}' AND is_active = 0 LIMIT 0,1");
        if (!empty($qc_header) && intval($qc_header->quality_check ?? 0) != 1) {
            return array('status' => 'skipped', 'message' => 'Inventory posting waits for QC pass');
        }

        $finished_document_type = 'mfg_product_note';
        $material_document_type = 'mfg_product_mat';
        $existing = $callConfig->getRow("SELECT id FROM item_ledger_wa WHERE company_id = '{$company_id}' AND document_no = '{$note_id}' AND document_type IN ('{$finished_document_type}', '{$material_document_type}', 'manufacturing_produc', 'manufacturing_product_notes', 'manufacturing_product_note_items') AND is_active = 0 LIMIT 0,1");
        if (!empty($existing->id)) {
            return array('status' => 'skipped', 'message' => 'Inventory already posted for this product note');
        }

        $note = $callConfig->getRow("SELECT transaction_no, transaction_date FROM manufacturing_product_notes WHERE company_id = '{$company_id}' AND id = '{$note_id}' AND is_active = 0");
        $work_order = $callConfig->getRow("SELECT warehouse FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND id = '{$work_order_id}' AND is_active = 0");
        $user = $callConfig->getRow("SELECT location_id, default_warehouse_id FROM application_users WHERE company_id = '{$company_id}' AND id = '{$user_id}' AND is_active = 0");
        $location_id = intval($post['transaction_location_id'] ?? ($user->location_id ?? 0));
        $warehouse_id = intval($post['transaction_warehouse_id'] ?? ($work_order->warehouse ?? 0));
        if ($warehouse_id <= 0) {
            $warehouse_id = intval($user->default_warehouse_id ?? 0);
        }
        if ($location_id <= 0) {
            return array('status' => 'skipped', 'message' => 'User location is required for inventory posting');
        }

        $transaction_date = !empty($note->transaction_date) ? $note->transaction_date : date('Y-m-d');
        $document_ref_no = $note->transaction_no ?? ('MPN' . $note_id);
        $posted = 0;

        $product_item = $callConfig->getRow("SELECT id, purchase_price, purchase_uom, sale_uom, base_uom FROM items WHERE company_id = '{$company_id}' AND id = '{$product_id}' AND is_active = 0");
        if (!empty($product_item->id)) {
            $product_rate = floatval($this->togetitemwadayrate($product_id, $company_id));
            if ($product_rate <= 0) {
                $product_rate = floatval($product_item->purchase_price ?? 0);
            }
            $product_value = $product_rate * $produced_qty;
            $this->invoiceUpdates($finished_document_type, $this->buildManufacturingInventoryPostingRow(
                $company_id,
                $location_id,
                $warehouse_id,
                $transaction_date,
                $product_item,
                $note_id,
                $finished_document_type,
                $document_ref_no,
                $produced_qty,
                0,
                $product_value,
                0,
                $product_rate
            ));
            $posted++;
        }

        foreach ((array)($post['note_items'] ?? array()) as $row) {
            $material_item_id = intval($row['item_id'] ?? 0);
            $consumed_qty = floatval($row['produced_qty'] ?? 0);
            if ($material_item_id <= 0 || $consumed_qty <= 0) {
                continue;
            }
            $material_item = $callConfig->getRow("SELECT id, purchase_price, purchase_uom, sale_uom, base_uom FROM items WHERE company_id = '{$company_id}' AND id = '{$material_item_id}' AND is_active = 0");
            if (empty($material_item->id)) {
                continue;
            }
            $material_rate = floatval($this->togetitemwadayrate($material_item_id, $company_id));
            if ($material_rate <= 0) {
                $material_rate = floatval($material_item->purchase_price ?? 0);
            }
            $material_value = $material_rate * $consumed_qty;
            $this->invoiceUpdates($material_document_type, $this->buildManufacturingInventoryPostingRow(
                $company_id,
                $location_id,
                $warehouse_id,
                $transaction_date,
                $material_item,
                $note_id,
                $material_document_type,
                $document_ref_no,
                0,
                $consumed_qty,
                0,
                $material_value,
                $material_rate
            ));
            $posted++;
        }

        return array('status' => 'success', 'message' => 'Inventory posted for product note', 'posted_rows' => $posted);
    }

    function postManufacturingProductNoteInventoryById($company_id, $user_id, $note_id){
        global $callConfig;
        $note_id = intval($note_id);
        $company_id = intval($company_id);
        $note = $callConfig->getRow("SELECT * FROM manufacturing_product_notes WHERE company_id = '{$company_id}' AND id = '{$note_id}' AND is_active = 0");
        if (empty($note->id)) {
            return array('status' => 'skipped', 'message' => 'Product note not found');
        }
        $items = $callConfig->getAllRows("SELECT item_id, received_qty, produced_qty, wastage_qty FROM manufacturing_product_note_items WHERE company_id = '{$company_id}' AND transaction_id = '{$note_id}' AND is_active = 0");
        $note_items = array();
        foreach ((array)$items as $item) {
            $note_items[] = array(
                'item_id' => $item->item_id,
                'received_qty' => $item->received_qty,
                'produced_qty' => $item->produced_qty,
                'wastage_qty' => $item->wastage_qty
            );
        }
        $post = array(
            'company_id' => $company_id,
            'created_by' => $user_id,
            'work_order_id' => $note->work_order_id,
            'item_id' => $note->item_id,
            'request_qty' => $note->request_qty,
            'produced_qty' => $note->produced_qty,
            'wastage_qty' => $note->wastage_qty,
            'note_items' => $note_items
        );
        return $this->postManufacturingProductNoteInventory($company_id, $user_id, $note_id, $post);
    }

    function buildManufacturingInventoryPostingRow($company_id, $location_id, $warehouse_id, $transaction_date, $item, $document_no, $document_type, $document_ref_no, $in_qty, $out_qty, $in_value, $out_value, $rate){
        $qty = ($in_qty > 0) ? $in_qty : $out_qty;
        $base_uom = $item->base_uom ?? '';
        $purchase_uom = $item->purchase_uom ?? $base_uom;
        $sales_uom = $item->sale_uom ?? $base_uom;
        return array(
            'company_id' => $company_id,
            'location_id' => $location_id,
            'transaction_warehouse_id' => $warehouse_id,
            'transaction_date' => $transaction_date,
            'item_id' => $item->id,
            'document_no' => $document_no,
            'document_type' => $document_type,
            'document_ref_no' => $document_ref_no,
            'in_qty' => $in_qty,
            'in_value' => $in_value,
            'out_qty' => $out_qty,
            'out_value' => $out_value,
            'item_wa_cost' => $rate,
            'item_st_cost' => floatval($item->purchase_price ?? 0),
            'day_value' => $in_value - $out_value,
            'day_rate' => $rate,
            'purchase_uom' => $purchase_uom,
            'purchase_qty' => $qty,
            'purchase_base_uom' => $base_uom,
            'sales_uom' => $sales_uom,
            'sales_qty' => $qty,
            'sale_base_uom' => $base_uom,
            'reason_id' => 0,
            'sale_price' => 0,
            'sale_value' => 0,
            'is_active' => 0,
            'current_status' => 0
        );
    }

    function validateManufacturingProductNote($company_id, $post, $note_id = 0){
        global $callConfig;
        $work_order_id = intval($post['work_order_id'] ?? 0);
        $product_id = intval($post['item_id'] ?? 0);
        $produced_qty = floatval($post['produced_qty'] ?? 0);
        if ($company_id <= 0 || $work_order_id <= 0 || $product_id <= 0) {
            return array('status' => 'fail', 'message' => 'Company, work order and product are required.');
        }
        if ($produced_qty <= 0) {
            return array('status' => 'fail', 'message' => 'Produced quantity must be greater than zero.');
        }
        $work_order = $callConfig->getRow("SELECT id, work_order_item_id, work_order_item_qty, completed_qty, current_status, work_log_status FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND id = '{$work_order_id}' AND is_active = 0");
        if (empty($work_order->id)) {
            return array('status' => 'fail', 'message' => 'Work order not found.');
        }
        if ($work_order->current_status != ST_APPROVED) {
            return array('status' => 'fail', 'message' => 'Work order must be approved before creating a product note.');
        }
        if (intval($work_order->work_order_item_id) != $product_id) {
            return array('status' => 'fail', 'message' => 'Product note product does not match the selected work order.');
        }
        $product = $callConfig->getRow("SELECT IFNULL(tracking_type, 0) AS tracking_type FROM items WHERE company_id = '{$company_id}' AND id = '{$product_id}' AND is_active = 0");
        $tracking_type = intval($post['tracking_type'] ?? ($product->tracking_type ?? 0));
        if ($tracking_type == 1) {
            if (floor($produced_qty) != $produced_qty) {
                return array('status' => 'fail', 'message' => 'Serial tracked product note quantity must be a whole number.');
            }
            $serials = array();
            foreach ((array)($post['serial_no'] ?? array()) as $serial_no) {
                $serial_no = trim($serial_no);
                if ($serial_no != '') {
                    $serials[] = $serial_no;
                }
            }
            if (count($serials) != intval($produced_qty)) {
                return array('status' => 'fail', 'message' => 'Please enter one serial number for each produced quantity.');
            }
            if (count($serials) != count(array_unique($serials))) {
                return array('status' => 'fail', 'message' => 'Duplicate serial numbers are not allowed.');
            }
            foreach ($serials as $serial_no) {
                $serial_no_esc = $callConfig->real_escape_string($serial_no);
                $existing_serial = $callConfig->getRow("SELECT id FROM item_serial_nos WHERE company_id = '{$company_id}' AND item_id = '{$product_id}' AND serial_no = '{$serial_no_esc}' AND is_active = 0 AND NOT (transaction_table = 'manufacturing_product_notes' AND transaction_id = '{$note_id}') LIMIT 0,1");
                if (!empty($existing_serial->id)) {
                    return array('status' => 'fail', 'message' => 'Serial number ' . $serial_no . ' already exists for this item.');
                }
            }
        }
        if ($tracking_type == 2) {
            $lot_total = 0;
            $lot_nos = array();
            foreach ((array)($post['lot_no'] ?? array()) as $seq => $lot_no) {
                $lot_no = trim($lot_no);
                $lot_qty = floatval($post['lot_qty'][$seq] ?? 0);
                if ($lot_no == '' || $lot_qty <= 0) {
                    return array('status' => 'fail', 'message' => 'Lot number and quantity are required for lot tracked product notes.');
                }
                $lot_total += $lot_qty;
                $lot_nos[] = $lot_no;
            }
            if (count($lot_nos) == 0) {
                return array('status' => 'fail', 'message' => 'Please enter at least one lot number.');
            }
            if (count($lot_nos) != count(array_unique($lot_nos))) {
                return array('status' => 'fail', 'message' => 'Duplicate lot numbers are not allowed.');
            }
            if (abs($lot_total - $produced_qty) > 0.0001) {
                return array('status' => 'fail', 'message' => 'Total lot quantity must equal produced quantity.');
            }
            foreach ($lot_nos as $lot_no) {
                $lot_no_esc = $callConfig->real_escape_string($lot_no);
                $existing_lot = $callConfig->getRow("SELECT id FROM item_lot_nos WHERE company_id = '{$company_id}' AND item_id = '{$product_id}' AND lot_no = '{$lot_no_esc}' AND is_active = 0 AND NOT (transaction_table = 'manufacturing_product_notes' AND transaction_id = '{$note_id}') LIMIT 0,1");
                if (!empty($existing_lot->id)) {
                    return array('status' => 'fail', 'message' => 'Lot number ' . $lot_no . ' already exists for this item.');
                }
            }
        }
        $existing_note_qty = 0;
        if ($note_id > 0) {
            $existing_note = $callConfig->getRow("SELECT produced_qty FROM manufacturing_product_notes WHERE company_id = '{$company_id}' AND id = '{$note_id}' AND work_order_id = '{$work_order_id}' AND is_active = 0");
            $existing_note_qty = floatval($existing_note->produced_qty ?? 0);
        }
        if (empty($post['skip_work_order_updates'])) {
            $remaining_qty = floatval($work_order->work_order_item_qty ?? 0) - (floatval($work_order->completed_qty ?? 0) - $existing_note_qty);
            if ($produced_qty > $remaining_qty) {
                return array('status' => 'fail', 'message' => 'Produced quantity cannot exceed remaining work order quantity.');
            }
        }
        if (empty($post['skip_work_order_updates'])) {
            foreach ((array)($post['note_items'] ?? array()) as $row) {
                $material_item_id = intval($row['item_id'] ?? 0);
                $material_qty = floatval($row['produced_qty'] ?? 0);
                if ($material_item_id <= 0 || $material_qty <= 0) {
                    continue;
                }
                $mwo_item = $callConfig->getRow("SELECT id, request_qty, stock_receipts_qty, completed_qty FROM manufacturing_work_order_items WHERE company_id = '{$company_id}' AND transaction_id = '{$work_order_id}' AND item_id = '{$material_item_id}' AND is_active = 0 LIMIT 1");
                if (empty($mwo_item->id)) {
                    return array('status' => 'fail', 'message' => 'Product note contains material that is not part of the work order.');
                }
                $available_material_qty = floatval($mwo_item->stock_receipts_qty ?? 0);
                if ($available_material_qty <= 0) {
                    $available_material_qty = floatval($mwo_item->request_qty ?? 0);
                }
                if ((floatval($mwo_item->completed_qty ?? 0) + $material_qty) > $available_material_qty) {
                    return array('status' => 'fail', 'message' => 'Material consumption cannot exceed received/required material quantity.');
                }
            }
        }
        return array('status' => 'success');
    }

    function applyManufacturingProductNoteWorkOrderProgress($company_id, $user_id, $post){
        global $callConfig;
        $work_order_id = intval($post['work_order_id'] ?? 0);
        $produced_qty = floatval($post['produced_qty'] ?? 0);
        $work_order = $callConfig->getRow("SELECT completed_qty, work_order_item_qty FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND id = '{$work_order_id}' AND is_active = 0");
        if (empty($work_order) || $produced_qty <= 0) {
            return;
        }
        $total_completed_qty = floatval($work_order->completed_qty ?? 0) + $produced_qty;
        $ordered_qty = floatval($work_order->work_order_item_qty ?? 0);
        $wo_update = array(
            'completed_qty' => $total_completed_qty,
            'work_log_status' => ($total_completed_qty >= $ordered_qty) ? 199 : 0,
            'updated_by' => $user_id,
            'updated_date' => date('Y-m-d H:i:s')
        );
        if ($total_completed_qty >= $ordered_qty) {
            $wo_update['actual_end_date'] = date('Y-m-d H:i:s');
        }
        $callConfig->updateRecord('manufacturing_work_orders', $wo_update, 'id', $work_order_id);

        foreach ((array)($post['note_items'] ?? array()) as $row) {
            $material_item_id = intval($row['item_id'] ?? 0);
            $material_qty = floatval($row['produced_qty'] ?? 0);
            if ($material_item_id <= 0 || $material_qty <= 0) {
                continue;
            }
            $mwo_item = $callConfig->getRow("SELECT id, completed_qty FROM manufacturing_work_order_items WHERE company_id = '{$company_id}' AND transaction_id = '{$work_order_id}' AND item_id = '{$material_item_id}' AND is_active = 0 LIMIT 1");
            if (!empty($mwo_item->id)) {
                $callConfig->updateRecord('manufacturing_work_order_items', array(
                    'completed_qty' => floatval($mwo_item->completed_qty ?? 0) + $material_qty,
                    'updated_by' => $user_id,
                    'updated_date' => date('Y-m-d H:i:s')
                ), 'id', $mwo_item->id);
            }
        }
    }

    function saveManufacturingProductNoteQcHeader($company_id, $user_id, $note_id, $remarks = ''){
        global $callConfig;
        $existing = $callConfig->getRow("SELECT id FROM application_table_quality_check WHERE company_id = '{$company_id}' AND application_table_name = 'manufacturing_product_notes' AND transaction_id = '{$note_id}' AND is_active = 0");
        if (!empty($existing->id)) {
            return $existing->id;
        }

        $insert = array(
            'company_id' => $company_id,
            'application_table_name' => 'manufacturing_product_notes',
            'transaction_id' => $note_id,
            'quality_check' => 0,
            'remarks' => $remarks,
            'is_active' => 0,
            'current_status' => 0,
            'created_by' => $user_id,
            'created_date' => date('Y-m-d H:i:s')
        );
        $result = $callConfig->insertRecord('application_table_quality_check', $insert);
        return $result['LASTID'] ?? 0;
    }

    function saveManufacturingProductNoteSerialLot($company_id, $user_id, $note_id, $item_id, $post, $random_no = ''){
        global $callConfig;
        if ($note_id <= 0 || $item_id <= 0) {
            return array();
        }

        $tracking_type = intval($post['tracking_type'] ?? 0);
        $random_no = trim($random_no);
        if ($random_no == '') {
            $random_no = date('YmdHis') . $note_id . mt_rand(1000, 9999);
        }
        $return = array();

        if ($tracking_type == 1 && isset($post['serial_no']) && is_array($post['serial_no'])) {
            $callConfig->executeQuery("UPDATE item_serial_nos SET is_active = 1, updated_by = '{$user_id}', updated_date = '".date('Y-m-d H:i:s')."' WHERE company_id = '{$company_id}' AND transaction_table = 'manufacturing_product_notes' AND transaction_id = '{$note_id}' AND item_id = '{$item_id}' AND is_active = 0");
            foreach ($post['serial_no'] as $serial_no) {
                $serial_no = trim($serial_no);
                if ($serial_no == '') {
                    continue;
                }
                $return[] = $callConfig->insertRecord('item_serial_nos', array(
                    'company_id' => $company_id,
                    'transaction_id' => $note_id,
                    'transaction_table' => 'manufacturing_product_notes',
                    'random_no' => $random_no,
                    'item_id' => $item_id,
                    'serial_no' => $serial_no,
                    'serial_entry_type' => 'PRODUCTION',
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_by' => $user_id,
                    'created_date' => date('Y-m-d H:i:s')
                ));
            }
        }

        if ($tracking_type == 2 && isset($post['lot_no']) && is_array($post['lot_no'])) {
            $callConfig->executeQuery("UPDATE item_lot_nos SET is_active = 1, updated_by = '{$user_id}', updated_date = '".date('Y-m-d H:i:s')."' WHERE company_id = '{$company_id}' AND transaction_table = 'manufacturing_product_notes' AND transaction_id = '{$note_id}' AND item_id = '{$item_id}' AND is_active = 0");
            foreach ($post['lot_no'] as $seq => $lot_no) {
                $lot_no = trim($lot_no);
                $lot_qty = floatval($post['lot_qty'][$seq] ?? 0);
                if ($lot_no == '' || $lot_qty <= 0) {
                    continue;
                }
                $return[] = $callConfig->insertRecord('item_lot_nos', array(
                    'company_id' => $company_id,
                    'transaction_id' => $note_id,
                    'transaction_table' => 'manufacturing_product_notes',
                    'random_no' => $random_no,
                    'item_id' => $item_id,
                    'lot_no' => $lot_no,
                    'lot_entry_type' => 'PRODUCTION',
                    'lot_quantity' => $lot_qty,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_by' => $user_id,
                    'created_date' => date('Y-m-d H:i:s')
                ));
            }
        }

        return $return;
    }

    function getmanufacturingworkorderslogs($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $work_order_id = $get['work_order_id'] ?? 0;
        $query = "SELECT mwo.id,mwo.transaction_no
            FROM manufacturing_work_orders mwo
            WHERE mwo.company_id = '{$company_id}' 
                AND mwo.is_active = 0 AND current_status=199 ORDER BY mwo.id DESC";
        $res = $callConfig->getAllRows($query);
        $return['data'] = $res ?? array();

        if($work_order_id != '' && $work_order_id != 0){
            // ===== 1. Get Work Order Master =====
            $query = "SELECT mwo.id,mwo.transaction_no,mwo.transaction_date,mwo.work_order_item_qty,mwo.completed_qty,mwo.planned_start_date,mwo.planned_end_date,mwo.work_log_status,i.item_name, mwo.work_order_item_id
                FROM manufacturing_work_orders mwo
                LEFT JOIN items i 
                    ON i.id = mwo.work_order_item_id 
                    AND i.company_id = mwo.company_id
                WHERE mwo.company_id = '{$company_id}'
                    AND mwo.id = '{$work_order_id}'
                    AND mwo.is_active = 0";
            $work_order = $callConfig->getRow($query);
            $return['work_order'] = $work_order;

            // ===== 2. Get Work Order Items =====
            $query2 = "SELECT mwoi.id,i.item_name,mwoi.request_qty,mwoi.stock_request_qty, mwoi.stock_receipts_qty, mwoi.completed_qty, i.item_name, concat(uom.uom_short_code,'-',uom.uom_name) as unit, mwoi.item_id
                FROM manufacturing_work_order_items mwoi
                LEFT JOIN items i ON i.id = mwoi.item_id 
                    AND i.company_id = mwoi.company_id
                LEFT JOIN unit_of_measurements uom ON uom.id = mwoi.uom 
                AND i.company_id = mwoi.company_id
                WHERE mwoi.company_id = '{$company_id}' 
                    AND mwoi.transaction_id = '{$work_order_id}' 
                    AND mwoi.is_active = 0";
            $items = $callConfig->getAllRows($query2);
            $return['work_order_items'] = $items;

            $query = "SELECT id,work_order_id,log_date,log_qty,remarks FROM manufacturing_work_order_logs 
                WHERE company_id = '{$company_id}' AND work_order_id = '{$work_order_id}' AND is_active = 0";
            $work_order_logs = $callConfig->getAllRows($query);
            $return['work_order_logs'] = $work_order_logs;

            $query = "SELECT mo.operation_name, mwoo.operation_time, mwoo.operation_manpower, mwoo.operation_note  FROM manufacturing_work_order_operations mwoo 
            LEFT JOIN manufacturing_operations mo ON mo.id = mwoo.manufacturing_operation_id AND mo.company_id = mwoo.company_id AND  mo.is_active = 0
                WHERE mwoo.company_id = '{$company_id}' AND mwoo.transaction_id = '{$work_order_id}' AND mwoo.is_active = 0";
            $work_order_operations = $callConfig->getAllRows($query);
            $return['work_order_operations'] = $work_order_operations;
        }
        return $return;
    }
    // added by koti end
    function getstockrequests($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $stock_request_id = $get['stock_request_id'] ?? 0;

        // ===== Always get stock requests with pending manufacturing transfer qty =====
        $query = "SELECT id, transaction_number, transaction_date, party_name, transaction_location_id, request_warehouse_id, total_amount, ref_type, ref_no
            FROM stock_requests
            WHERE company_id = '{$company_id}' 
                AND is_active = 0
                AND ref_type = 'manufacturing_work_orders'
                AND current_status = '".ST_APPROVED."'
                AND id IN (
                    SELECT transaction_id
                    FROM stkreq_items
                    WHERE company_id = '{$company_id}'
                        AND is_active = 0
                        AND pending_qty > 0
                )
            ORDER BY id DESC";
        $res = $callConfig->getAllRows($query);
        $return['data'] = $res ?? array();

        // ===== If stock_request_id provided, also get specific request details =====
        if($stock_request_id != '' && $stock_request_id != 0) {
            // Get stock request master
            $query = "SELECT id, transaction_number, transaction_date, party_name, transaction_location_id, request_warehouse_id, total_amount, ref_type, ref_id, ref_no, notes
                FROM stock_requests
                WHERE company_id = '{$company_id}'
                    AND id = '{$stock_request_id}'
                    AND ref_type = 'manufacturing_work_orders'
                    AND is_active = 0";
            $stock_request = $callConfig->getRow($query);
            $return['stock_request'] = $stock_request;
            $work_order_id = !empty($stock_request->id) && !empty($stock_request->ref_id) ? intval($stock_request->ref_id) : 0;
            $return['work_order_id'] = $work_order_id;
            $return['work_order_no'] = !empty($stock_request->id) ? ($stock_request->ref_no ?? '') : '';

            // Get stock request items
            $query2 = "SELECT sri.id, sri.item_id, sri.item_name, sri.item_quantity, sri.item_price, sri.item_amount, 
                        sri.approved_quantity, sri.transaction_qty, sri.pending_qty, 
                        i.sku, i.purchase_uom, i.sale_uom,
                        concat(uom.uom_short_code,'-',uom.uom_name) as unit
                FROM stkreq_items sri
                LEFT JOIN items i ON i.id = sri.item_id 
                    AND i.company_id = sri.company_id
                LEFT JOIN unit_of_measurements uom ON uom.id = i.purchase_uom 
                    AND uom.company_id = sri.company_id
                WHERE sri.company_id = '{$company_id}' 
                    AND sri.transaction_id = '{$stock_request_id}' 
                    AND sri.is_active = 0";
            $items = $callConfig->getAllRows($query2);
            $return['stock_request_items'] = $items ?? array();

            // Calculate total_amount as sum of all items
            $calculated_total = 0;
            if(!empty($items) && is_array($items)) {
                foreach($items as $item) {
                    $calculated_total += (floatval($item->pending_qty ?? 0) * floatval($item->item_price ?? 0));
                }
            }
            $return['total_amount'] = $calculated_total;

            // ===== Get stock transfer history for selected stock request =====
            $stock_transfer_history = array();
            if (!empty($stock_request->id)) {
                $history_stock_request_id = intval($stock_request_id);
                if ($history_stock_request_id > 0) {
                    $history_query = "SELECT st.id, st.transaction_number, st.transaction_date, st.ref_id, st.ref_no, st.ref_date, st.total_amount, st.notes,
                            st.ref_no AS stock_request_no
                        FROM stock_transfers st
                        WHERE st.company_id = '{$company_id}'
                            AND st.ref_type = 'stock_requests'
                            AND st.ref_id = '{$history_stock_request_id}'
                            AND st.is_active = 0
                        ORDER BY st.transaction_date DESC, st.id DESC";
                    $history_res = $callConfig->getAllRows($history_query);

                    $transfer_ids = array();
                    foreach ((array)$history_res as $history) {
                        $history->items = array();
                        $history->item_count = 0;
                        $history->total_transfer_qty = 0;
                        $history->calculated_total_amount = 0;
                        $stock_transfer_history[$history->id] = $history;
                        $transfer_ids[] = intval($history->id);
                    }

                    if (!empty($transfer_ids)) {
                        $transfer_ids_str = implode(',', array_unique($transfer_ids));
                        $history_items_query = "SELECT sti.id, sti.transaction_id, sti.item_id, sti.item_name, sti.purchase_uom,
                                sti.item_quantity, sti.transaction_qty, sti.pending_qty, sti.item_price, sti.item_amount, sti.total_amount,
                                i.sku,
                                concat(uom.uom_short_code,'-',uom.uom_name) as unit
                            FROM stktrn_items sti
                            LEFT JOIN items i ON i.id = sti.item_id
                                AND i.company_id = sti.company_id
                            LEFT JOIN unit_of_measurements uom ON uom.id = sti.purchase_uom
                                AND uom.company_id = sti.company_id
                            WHERE sti.company_id = '{$company_id}'
                                AND sti.transaction_id IN ({$transfer_ids_str})
                                AND sti.is_active = 0
                            ORDER BY sti.transaction_id DESC, sti.id ASC";
                        $history_items_res = $callConfig->getAllRows($history_items_query);

                        foreach ((array)$history_items_res as $history_item) {
                            if (isset($stock_transfer_history[$history_item->transaction_id])) {
                                $stock_transfer_history[$history_item->transaction_id]->items[] = $history_item;
                                $stock_transfer_history[$history_item->transaction_id]->item_count++;
                                $stock_transfer_history[$history_item->transaction_id]->total_transfer_qty += floatval($history_item->item_quantity ?? 0);
                                $stock_transfer_history[$history_item->transaction_id]->calculated_total_amount += floatval($history_item->item_amount ?? 0);
                            }
                        }
                    }
                }
            }
            $return['stock_transfer_history'] = array_values($stock_transfer_history);
        }
        
        return $return;
    }
    function insertstocktransfer($get, $post){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $user_id = $get['user_id'];
        $stock_request_id = intval($post['stock_request_id'] ?? 0);

        // ===== Get stock request master data =====
        if ($stock_request_id <= 0) {
            $return[] = array('status' => 'fail', 'message' => 'Please select a valid stock request.');
            return $return;
        }

        $stock_request_sql = "SELECT * FROM stock_requests WHERE company_id = '{$company_id}' AND id = '{$stock_request_id}' AND ref_type = 'manufacturing_work_orders' AND current_status = '".ST_APPROVED."' AND is_active = 0";
        $stock_request_res = $callConfig->getRow($stock_request_sql);
        $return['stock_request'] = $stock_request_res;

        if(!empty($stock_request_res->id)){
            // ===== Get stock request items =====
            $stock_request_items_sql = "SELECT * FROM stkreq_items WHERE company_id = '{$company_id}' AND transaction_id = '{$stock_request_id}' AND is_active = 0 AND pending_qty > 0";
            $stock_request_items_res = $callConfig->getAllRows($stock_request_items_sql);
            $return['stock_request_items'] = $stock_request_items_res;

            // Create items array keyed by id for quick lookup
            $items_array = array();
            foreach($stock_request_items_res as $item){
                $items_array[$item->id] = $item;
            }

            $selected_items = array();
            $validation_errors = array();
            if (!empty($post['items']) && is_array($post['items'])) {
                foreach ($post['items'] as $row) {
                    $item_id = intval($row['item_id'] ?? 0);
                    $stock_transfer_qty = floatval($row['stock_transfer_qty'] ?? 0);

                    if ($stock_transfer_qty <= 0) {
                        continue;
                    }

                    if (!isset($items_array[$item_id])) {
                        $validation_errors[] = 'Invalid or completed stock request item selected.';
                        continue;
                    }

                    $stkreq_item = $items_array[$item_id];
                    $current_pending = floatval($stkreq_item->pending_qty ?? 0);

                    if ($stock_transfer_qty > $current_pending) {
                        $validation_errors[] = ($stkreq_item->item_name ?? 'Item').' transfer qty cannot exceed pending qty '.$current_pending.'.';
                        continue;
                    }

                    $selected_items[] = array(
                        'stock_request_item' => $stkreq_item,
                        'stock_transfer_qty' => $stock_transfer_qty
                    );
                }
            }

            if (!empty($validation_errors)) {
                $return[] = array('status' => 'fail', 'message' => implode(' ', array_unique($validation_errors)));
                return $return;
            }

            if (empty($selected_items)) {
                $return[] = array('status' => 'fail', 'message' => 'Please enter at least one valid stock transfer quantity.');
                return $return;
            }

            $request_warehouse_id = intval($stock_request_res->request_warehouse_id ?? 0);
            $transaction_warehouse_id = intval($stock_request_res->transaction_warehouse_id ?? 0);
            if ($transaction_warehouse_id <= 0) {
                $user_warehouse_sql = "SELECT default_warehouse_id FROM application_users WHERE company_id = '{$company_id}' AND id = '{$user_id}' AND is_active = 0";
                $user_warehouse_res = $callConfig->getRow($user_warehouse_sql);
                $transaction_warehouse_id = intval($user_warehouse_res->default_warehouse_id ?? 0);
            }
            if ($request_warehouse_id <= 0 || $transaction_warehouse_id <= 0) {
                $return[] = array('status' => 'fail', 'message' => 'Source or request warehouse is missing. Please update the stock request, work order warehouse, or user default warehouse before transfer.');
                return $return;
            }

            // ===== Generate transaction number =====
            $get['transaction_date'] = date('Y-m-d');
            $transaction_number = $this->toGetApplicationSerialNumber('stock_transfers', $get);

            // ===== Insert into stock_transfers (Master) =====
            $record = array(
                'company_id' => $company_id,
                'transaction_number' => $transaction_number,
                'transaction_date' => date('Y-m-d'),
                'transaction_location_id' => $stock_request_res->transaction_location_id,
                'total_amount' => 0,
                'request_warehouse_id' => $request_warehouse_id,
                'transaction_warehouse_id' => $transaction_warehouse_id,
                'originator' => $user_id,
                'ref_type' => 'stock_requests',
                'ref_id' => $stock_request_res->id,
                'ref_no' => $stock_request_res->transaction_number,
                'ref_date' => $stock_request_res->transaction_date,
                'is_active' => 0,
                'current_status' => 0,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            );

            $result = $callConfig->insertRecord('stock_transfers', $record);
            $return[] = $result;
            if (!isset($result['status']) || $result['status'] != 'sucess') {
                return $return;
            }
            $transfer_id = $result['LASTID'];

            // ===== Insert into stktrn_items (Child Loop) =====
            $stock_transfer_amount = 0;
            $stock_transfer_tax_amount = 0;
            $stock_transfer_total_amount = 0;
            foreach ($selected_items as $selected_item) {
                $stkreq_item = $selected_item['stock_request_item'];
                $stock_transfer_qty = $selected_item['stock_transfer_qty'];

                $item_price = floatval($stkreq_item->item_price ?? 0);
                $item_amount = $item_price * $stock_transfer_qty;
                $tax_amount = 0;
                $total_amount = $item_amount + $tax_amount;

                $child = array(
                    'company_id' => $company_id,
                    'transaction_id' => $transfer_id,
                    'item_id' => $stkreq_item->item_id,
                    'item_name' => $stkreq_item->item_name,
                    'description' => $stkreq_item->description,
                    'purchase_uom' => $stkreq_item->purchase_uom,
                    'invoice_item_quantity' => $stock_transfer_qty,
                    'item_quantity' => $stock_transfer_qty,
                    'transaction_qty' => $stock_transfer_qty,
                    'item_price' => $item_price,
                    'item_amount' => $item_amount,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $total_amount,
                    'pending_qty' => $stock_transfer_qty,
                    'ref_transaction_table_id' => $stock_request_res->id,
                    'ref_transaction_item_table_id' => $stkreq_item->id,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_by' => $user_id,
                    'created_date' => date('Y-m-d H:i:s')
                );

                $res = $callConfig->insertRecord('stktrn_items', $child);
                $return[] = $res;
                if (isset($res['status']) && $res['status'] == 'sucess') {
                    $stock_transfer_amount += $item_amount;
                    $stock_transfer_tax_amount += $tax_amount;
                    $stock_transfer_total_amount += $total_amount;
                }
            }
            $callConfig->updateRecord('stock_transfers', array('amount' => $stock_transfer_amount, 'tax_amount' => $stock_transfer_tax_amount, 'total_amount' => $stock_transfer_total_amount, 'pending_amount' => $stock_transfer_total_amount, 'updated_by' => $user_id, 'updated_date' => date('Y-m-d H:i:s')), 'id', $transfer_id);
            $get['transaction_table'] = 'stock_transfers';
            $transaction_details = $callConfig->getRow("SELECT * FROM stock_transfers WHERE company_id = '".$company_id."' AND id = '".$transfer_id."' AND is_active = 0");
            if (!empty($transaction_details->id)) {
                $this->updateTransactionItemsQty($get,$transaction_details,"-");
                try {
                    $this->tocreatejounalvoucherbaseallcases('stock_transfers',$transfer_id);
                } catch (Throwable $e) {
                    error_log('Stock transfer journal voucher creation failed for transfer '.$transfer_id.': '.$e->getMessage());
                    $return[] = array('status' => 'warning', 'accounting_status' => 'pending', 'message' => 'Stock transfer saved and stock moved, but journal voucher was not created. Please check stock transfer account configuration.');
                }
            } else {
                $return[] = array('status' => 'fail', 'message' => 'Stock transfer saved, but stock movement could not be processed.');
            }
        } else {
            $return[] = array('status' => 'fail', 'message' => 'Approved manufacturing stock request not found.');
        }

        return $return;
    }
    function getstocktransfers($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $stock_request_id = $get['stock_request_id'] ?? 0;
        $stock_transfer_id = $get['stock_transfer_id'] ?? 0;

        // ===== Always get list of stock requests for initial selection =====
        $stock_requests_query = "SELECT id, transaction_number, transaction_date, ref_type, ref_no
            FROM stock_requests
            WHERE company_id = '{$company_id}' 
                AND is_active = 0
                AND ref_type = 'manufacturing_work_orders'
            ORDER BY id DESC";
        $stock_requests_res = $callConfig->getAllRows($stock_requests_query);
        $return['stock_requests'] = $stock_requests_res ?? array();

        // ===== If stock_request_id provided, get stock transfers for that request =====
        if($stock_request_id != '' && $stock_request_id != 0) {
            $query = "SELECT st.id, st.transaction_number, st.transaction_date, st.total_amount, st.notes,
                    SUM(IFNULL(sti.pending_qty, 0)) AS total_pending_qty
                FROM stock_transfers st
                INNER JOIN stktrn_items sti ON sti.company_id = st.company_id
                    AND sti.transaction_id = st.id
                    AND sti.is_active = 0
                    AND IFNULL(sti.pending_qty, 0) > 0
                WHERE st.company_id = '{$company_id}'
                    AND st.ref_type = 'stock_requests'
                    AND st.ref_id = '{$stock_request_id}'
                    AND st.is_active = 0
                GROUP BY st.id, st.transaction_number, st.transaction_date, st.total_amount, st.notes
                ORDER BY st.id DESC";
            $transfers = $callConfig->getAllRows($query);
            $return['data'] = $transfers ?? array();
        }

        // ===== If stock_transfer_id provided, get transfer and items details =====
        if($stock_transfer_id != '' && $stock_transfer_id != 0) {
            // Get stock transfer master
            $query = "SELECT id, transaction_number, transaction_date, total_amount, notes
                FROM stock_transfers
                WHERE company_id = '{$company_id}'
                    AND id = '{$stock_transfer_id}'
                    AND ref_type = 'stock_requests'
                    ".($stock_request_id != '' && $stock_request_id != 0 ? " AND ref_id = '{$stock_request_id}' " : "")."
                    AND is_active = 0";
            $stock_transfer = $callConfig->getRow($query);
            $return['stock_transfer'] = $stock_transfer;

            // Get stock transfer items
            $query2 = "SELECT id, item_id, item_name, item_quantity, pending_qty, item_price, item_amount,
                        purchase_uom
                FROM stktrn_items
                WHERE company_id = '{$company_id}' 
                    AND transaction_id = '{$stock_transfer_id}' 
                    AND IFNULL(pending_qty, 0) > 0
                    AND is_active = 0";
            $items = $callConfig->getAllRows($query2);
            $return['stock_transfer_items'] = $items ?? array();

            // Calculate total_amount as sum of all items
            $calculated_total = 0;
            if(!empty($items) && is_array($items)) {
                foreach($items as $item) {
                    $calculated_total += floatval($item->item_amount ?? 0);
                }
            }
            $return['total_amount'] = $calculated_total;
        }

        return $return;
    }
    function insertstocktransferreceipt($get, $post){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
        $user_id = $get['user_id'];
        $stock_transfer_id = intval($post['stock_transfer_id'] ?? 0);
        $stock_request_id = intval($post['stock_request_id'] ?? 0);

        if ($stock_transfer_id <= 0) {
            $return[] = array('status' => 'fail', 'message' => 'Please select a valid stock transfer.');
            return $return;
        }

        // ===== Get stock transfer master data =====
        $stock_transfer_sql = "SELECT * FROM stock_transfers WHERE company_id = '{$company_id}' AND id = '{$stock_transfer_id}' AND ref_type = 'stock_requests' ".($stock_request_id > 0 ? " AND ref_id = '{$stock_request_id}' " : "")." AND is_active = 0";
        $stock_transfer_res = $callConfig->getRow($stock_transfer_sql);
        $return['stock_transfer'] = $stock_transfer_res;

        if(!empty($stock_transfer_res)){
            // ===== Get stock transfer items =====
            $stock_transfer_items_sql = "SELECT * FROM stktrn_items WHERE company_id = '{$company_id}' AND transaction_id = '{$stock_transfer_id}' AND is_active = 0 AND IFNULL(pending_qty, 0) > 0";
            $stock_transfer_items_res = $callConfig->getAllRows($stock_transfer_items_sql);
            $return['stock_transfer_items'] = $stock_transfer_items_res;

            // Create items array keyed by id for quick lookup
            $items_array = array();
            foreach($stock_transfer_items_res as $item){
                $items_array[$item->id] = $item;
            }

            $selected_items = array();
            $validation_errors = array();
            if (!empty($post['items']) && is_array($post['items'])) {
                foreach ($post['items'] as $row) {
                    $item_id = intval($row['item_id'] ?? 0);
                    $receipt_qty = floatval($row['receipt_qty'] ?? 0);

                    if ($receipt_qty <= 0) {
                        continue;
                    }

                    if (!isset($items_array[$item_id])) {
                        $validation_errors[] = 'Invalid or fully received stock transfer item selected.';
                        continue;
                    }

                    $stktrn_item = $items_array[$item_id];
                    $current_pending = floatval($stktrn_item->pending_qty ?? 0);
                    if ($receipt_qty > $current_pending) {
                        $validation_errors[] = ($stktrn_item->item_name ?? 'Item').' receipt qty cannot exceed pending qty '.$current_pending.'.';
                        continue;
                    }

                    $selected_items[] = array(
                        'stock_transfer_item' => $stktrn_item,
                        'receipt_qty' => $receipt_qty
                    );
                }
            }

            if (!empty($validation_errors)) {
                $return[] = array('status' => 'fail', 'message' => implode(' ', array_unique($validation_errors)));
                return $return;
            }

            if (empty($selected_items)) {
                $return[] = array('status' => 'fail', 'message' => 'Please enter at least one valid receipt quantity.');
                return $return;
            }

            // ===== Pre-fetch all related stock request items and manufacturing work order items in one query =====
            // Get all ref_transaction_item_table_ids from stock transfer items
            $ref_item_ids = array();
            foreach($stock_transfer_items_res as $item) {
                if (!empty($item->ref_transaction_item_table_id)) {
                    $ref_item_ids[] = $item->ref_transaction_item_table_id;
                }
            }

            // Get all stock request items and their references in one query
            $stkreq_items_map = array(); // keyed by stkreq_item.id
            $mfg_items_map = array();     // keyed by manufacturing_work_order_items.id
            
            if (!empty($ref_item_ids)) {
                $ref_ids_str = implode(',', $ref_item_ids);
                
                // Get all stock request items
                $stkreq_query = "SELECT * FROM stkreq_items WHERE company_id = '{$company_id}' AND id IN ({$ref_ids_str}) AND is_active = 0";
                $stkreq_items_res = $callConfig->getAllRows($stkreq_query);
                foreach($stkreq_items_res as $item) {
                    $stkreq_items_map[$item->id] = $item;
                }

                // Get all manufacturing work order item IDs that need updating
                $mfg_item_ids = array();
                foreach($stkreq_items_res as $item) {
                    if (!empty($item->ref_transaction_item_table_id)) {
                        $mfg_item_ids[] = $item->ref_transaction_item_table_id;
                    }
                }

                // Get all manufacturing work order items in one query
                if (!empty($mfg_item_ids)) {
                    $mfg_ids_str = implode(',', $mfg_item_ids);
                    $mfg_query = "SELECT id, stock_receipts_qty FROM manufacturing_work_order_items WHERE company_id = '{$company_id}' AND id IN ({$mfg_ids_str}) AND is_active = 0";
                    $mfg_items_res = $callConfig->getAllRows($mfg_query);
                    foreach($mfg_items_res as $item) {
                        $mfg_items_map[$item->id] = $item;
                    }
                }
            }

            // ===== Generate transaction number =====
            $get['transaction_date'] = date('Y-m-d');
            $transaction_number = $this->toGetApplicationSerialNumber('stock_transfer_receipts', $get);

            // ===== Insert into stock_transfer_receipts (Master) =====
            $record = array(
                'company_id' => $company_id,
                'transaction_number' => $transaction_number,
                'transaction_date' => date('Y-m-d'),
                'total_amount' => 0,
                'request_warehouse_id' => $stock_transfer_res->request_warehouse_id,
                'transaction_warehouse_id' => $stock_transfer_res->transaction_warehouse_id,
                'originator' => $user_id,
                'ref_type' => 'stock_transfers',
                'ref_id' => $stock_transfer_res->id,
                'ref_no' => $stock_transfer_res->transaction_number,
                'ref_date' => $stock_transfer_res->transaction_date,
                'is_active' => 0,
                'current_status' => 0,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            );

            $result = $callConfig->insertRecord('stock_transfer_receipts', $record);
            $return[] = $result;
            if (!isset($result['status']) || $result['status'] != 'sucess') {
                return $return;
            }
            $receipt_id = $result['LASTID'];

            // ===== Insert into stktrnrec_items (Child Loop) =====
            $receipt_amount = 0;
            $receipt_tax_amount = 0;
            $receipt_total_amount = 0;
            foreach ($selected_items as $selected_item) {
                $stktrn_item = $selected_item['stock_transfer_item'];
                $receipt_qty = $selected_item['receipt_qty'];
                        
                $item_price = floatval($stktrn_item->item_price ?? 0);
                $item_amount = $item_price * $receipt_qty;
                $tax_amount = 0;
                $total_amount = $item_amount + $tax_amount;

                $child = array(
                    'company_id' => $company_id,
                    'transaction_id' => $receipt_id,
                    'item_id' => $stktrn_item->item_id,
                    'item_name' => $stktrn_item->item_name,
                    'description' => $stktrn_item->description,
                    'purchase_uom' => $stktrn_item->purchase_uom,
                    'invoice_item_quantity' => $receipt_qty,
                    'item_quantity' => $receipt_qty,
                    'transaction_qty' => $receipt_qty,
                    'item_price' => $item_price,
                    'item_amount' => $item_amount,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $total_amount,
                    'pending_qty' => $receipt_qty,
                    'ref_transaction_table_id' => $stock_transfer_res->id,
                    'ref_transaction_item_table_id' => $stktrn_item->id,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_by' => $user_id,
                    'created_date' => date('Y-m-d H:i:s')
                );

                $res = $callConfig->insertRecord('stkrec_items', $child);
                $return[] = $res;
                if (isset($res['status']) && $res['status'] == 'sucess') {
                    $receipt_amount += $item_amount;
                    $receipt_tax_amount += $tax_amount;
                    $receipt_total_amount += $total_amount;

                    // ===== Update manufacturing_work_order_items with receipt details =====
                    // Use pre-fetched data instead of querying in loop
                    if (!empty($stktrn_item->ref_transaction_item_table_id) && isset($stkreq_items_map[$stktrn_item->ref_transaction_item_table_id])) {
                        $stkreq_item = $stkreq_items_map[$stktrn_item->ref_transaction_item_table_id];
                            
                        if (!empty($stkreq_item->ref_transaction_item_table_id) && isset($mfg_items_map[$stkreq_item->ref_transaction_item_table_id])) {
                            $mfg_item = $mfg_items_map[$stkreq_item->ref_transaction_item_table_id];
                                
                            $current_stock_receipts_qty = floatval($mfg_item->stock_receipts_qty ?? 0);
                            $updated_stock_receipts_qty = $current_stock_receipts_qty + $receipt_qty;
                                
                            // Update manufacturing_work_order_items
                            $callConfig->updateRecord('manufacturing_work_order_items', array(
                                'stock_receipts_id' => $receipt_id,
                                'stock_receipts_qty' => $updated_stock_receipts_qty,
                                'updated_by' => $user_id,
                                'updated_date' => date('Y-m-d H:i:s')
                            ), 'id', $stkreq_item->ref_transaction_item_table_id);
                            $mfg_items_map[$stkreq_item->ref_transaction_item_table_id]->stock_receipts_qty = $updated_stock_receipts_qty;
                        }
                    }
                }
            }

            $callConfig->updateRecord('stock_transfer_receipts', array('amount' => $receipt_amount, 'tax_amount' => $receipt_tax_amount, 'total_amount' => $receipt_total_amount, 'pending_amount' => $receipt_total_amount, 'updated_by' => $user_id, 'updated_date' => date('Y-m-d H:i:s')), 'id', $receipt_id);
            try {
                $this->tocreatejounalvoucherbaseallcases('stock_transfer_receipts',$receipt_id);
            } catch (Throwable $e) {
                error_log('Stock transfer receipt journal voucher creation failed for receipt '.$receipt_id.': '.$e->getMessage());
                $return[] = array('status' => 'warning', 'accounting_status' => 'pending', 'message' => 'Stock transfer receipt saved, but journal voucher was not created. Please check stock transfer receipt account configuration.');
            }
            $get['transaction_table'] = 'stock_transfer_receipts';
            $transaction_details = $callConfig->getRow("SELECT * FROM stock_transfer_receipts WHERE company_id = '".$company_id."' AND id = '".$receipt_id."' AND is_active = 0");
            if (!empty($transaction_details->id)) {
                $this->updateTransactionItemsQty($get,$transaction_details,"-");
            } else {
                $return[] = array('status' => 'fail', 'message' => 'Stock transfer receipt saved, but stock movement could not be processed.');
            }
        } else {
            $return[] = array('status' => 'fail', 'message' => 'Stock transfer not found.');
        }

        return $return;
    }
    
    function insertManufacturingProductionStart($get, $post) {
        global $callConfig;
        
        $work_order_id = $get['work_order_id'] ?? $post['work_order_id'] ?? 0;
        $company_id = $get['company_id'] ?? $post['company_id'] ?? 0;
        $created_by = $get['created_by'] ?? $post['created_by'] ?? 0;
        $remarks = $post['remarks'] ?? '';

        if (!$work_order_id || !$company_id) {
            return array(
                'status' => 'error',
                'message' => 'Work Order ID and Company ID are required',
                'code' => 'MISSING_PARAMETERS'
            );
        }

        $workOrder = $callConfig->getRow("SELECT id, current_status, work_log_status FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND id = '{$work_order_id}' AND is_active = 0");
        if (empty($workOrder->id)) {
            return array('status' => 'error', 'message' => 'Work Order not found');
        }
        if ($workOrder->current_status != ST_APPROVED) {
            return array('status' => 'error', 'message' => 'Work Order must be approved before starting production');
        }
        if ($workOrder->work_log_status !== null && $workOrder->work_log_status !== '') {
            return array('status' => 'error', 'message' => 'Production already started for this work order');
        }

        $data = array(
            'work_order_id' => $work_order_id,
            'production_type' => 'START',
            'originator' => $created_by,
            'date_time' => date('Y-m-d H:i:s'),
            'company_id' => $company_id,
            'is_active' => 0,
            'current_status' => '0',
            'remarks' => $remarks,
            'created_by' => $created_by,
            'created_date' => date('Y-m-d H:i:s')
        );

        $result = $callConfig->insertRecord('manufacturing_production', $data);

        if ($result['status'] == 'sucess') {
            // Update manufacturing_work_orders with production start details
            $wo_update = array(
                'work_log_status' => 0,
                'actual_start_date' => date('Y-m-d H:i:s')
            );
            $callConfig->updateRecord('manufacturing_work_orders', $wo_update, 'id', $work_order_id);

            return array(
                'status' => 'success',
                'message' => 'Production started successfully',
                'data' => $result
            );
        } else {
            return array(
                'status' => 'error',
                'message' => 'Failed to start production',
                'error' => $result
            );
        }
    }
    function getManufacturingProductionDetails($get) {
        global $callConfig;
        $return = array();
        
        $work_order_id = $get['work_order_id'] ?? 0;
        $company_id = $get['company_id'] ?? 0;

        if (!$work_order_id || !$company_id) {
            return array(
                'status' => 'error',
                'message' => 'Work Order ID and Company ID are required'
            );
        }

        // Fetch manufacturing work order
        $wo_query = "SELECT mwo.id, mwo.transaction_no, mwo.transaction_date, mwo.work_order_item_qty, mwo.completed_qty, 
                     mwo.planned_start_date, mwo.planned_end_date, mwo.actual_start_date, mwo.actual_end_date, mwo.work_log_status,
                     mwo.work_order_item_id, i.item_name AS product_name, i.sku AS product_sku, IFNULL(i.quality_check, 0) AS quality_check,
                     IFNULL(i.tracking_type, 0) AS tracking_type, u.uom_short_code AS product_uom
                     FROM manufacturing_work_orders mwo
                     LEFT JOIN items i ON i.company_id = mwo.company_id AND i.id = mwo.work_order_item_id
                     LEFT JOIN unit_of_measurements u ON u.company_id = i.company_id AND u.id = i.sale_uom AND u.is_active = i.is_active
                     WHERE mwo.id = '{$work_order_id}' AND mwo.company_id = '{$company_id}' AND mwo.is_active = 0";
        $wo_result = $callConfig->getRow($wo_query);
        
        if (empty($wo_result)) {
            return array(
                'status' => 'error',
                'message' => 'Work Order not found'
            );
        }

        // Fetch manufacturing work order items
        $items_query = "SELECT mwoi.id, mwoi.item_id, mwoi.uom, mwoi.request_qty, mwoi.stock_receipts_qty, mwoi.completed_qty, 
                        i.item_name, i.sku, u.uom_short_code
                        FROM manufacturing_work_order_items mwoi
                        LEFT JOIN items i ON i.id = mwoi.item_id AND i.company_id = '{$company_id}'
                        LEFT JOIN unit_of_measurements u ON u.id = mwoi.uom AND u.company_id = '{$company_id}'
                        WHERE mwoi.transaction_id = '{$work_order_id}' AND mwoi.company_id = '{$company_id}' AND mwoi.is_active = 0";
        $items_result = $callConfig->getAllRows($items_query);

        $return['status'] = 'success';
        $return['work_order'] = $wo_result;
        $return['items'] = $items_result ?? array();

        $query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE genc_code='MANHOLDRES' AND company_id = '{$company_id}' AND is_active = 0";
        $hold_reasons = $callConfig->getAllRows($query);
        $return['hold_reasons'] = $hold_reasons;
        
        return $return;
    }

    function updateManufacturingProductionData($get, $post) {
        global $callConfig;
        $return = array();
        
        $work_order_id = $post['work_order_id'] ?? 0;
        $company_id = $post['company_id'] ?? 0;
        $created_by = $post['created_by'] ?? 0;
        $production_type = $post['production_type'] ?? ''; // HOLD or COMPLETED
        $completed_qty = $post['completed_qty'] ?? 0;
        $remarks = $post['remarks'] ?? '';
        $hold_reason = $post['hold_reason'] ?? '';

        if (!$work_order_id || !$company_id) {
            return array(
                'status' => 'error',
                'message' => 'Work Order ID and Company ID are required'
            );
        }

        if (!in_array($production_type, array('HOLD', 'COMPLETED'))) {
            return array(
                'status' => 'error',
                'message' => 'Invalid production type'
            );
        }

        $current_completed_row = $callConfig->getRow(
            "SELECT mwo.completed_qty, mwo.work_order_item_id, mwo.work_order_item_qty, mwo.current_status, mwo.work_log_status,
                i.item_name, IFNULL(i.quality_check, 0) AS quality_check, IFNULL(i.tracking_type, 0) AS tracking_type
             FROM manufacturing_work_orders mwo
             LEFT JOIN items i ON i.company_id = mwo.company_id AND i.id = mwo.work_order_item_id
             WHERE mwo.id = '{$work_order_id}' AND mwo.company_id = '{$company_id}' AND mwo.is_active = 0"
        );
        if (empty($current_completed_row->work_order_item_id)) {
            return array('status' => 'error', 'message' => 'Work Order not found');
        }
        if ($current_completed_row->current_status != ST_APPROVED) {
            return array('status' => 'error', 'message' => 'Work Order must be approved before production updates');
        }
        if ($production_type === 'HOLD' && $current_completed_row->work_log_status != 0) {
            return array('status' => 'error', 'message' => 'Only in-progress production can be put on hold');
        }
        if ($production_type === 'COMPLETED' && !in_array($current_completed_row->work_log_status, array(0, '0', 1, '1'))) {
            return array('status' => 'error', 'message' => 'Production must be started before completion');
        }
        $current_completed_qty = floatval($current_completed_row->completed_qty ?? 0);
        $session_completed_qty = floatval($completed_qty);
        $ordered_qty = floatval($current_completed_row->work_order_item_qty ?? 0);
        if ($production_type === 'COMPLETED' && $session_completed_qty <= 0) {
            return array('status' => 'error', 'message' => 'Completed quantity must be greater than zero');
        }
        if ($production_type === 'HOLD' && $session_completed_qty < 0) {
            return array('status' => 'error', 'message' => 'Completed quantity cannot be negative');
        }
        if (($current_completed_qty + $session_completed_qty) > $ordered_qty) {
            return array('status' => 'error', 'message' => 'Completed quantity cannot exceed work order quantity');
        }

        $product_quality_check = intval($current_completed_row->quality_check ?? 0);
        $tracking_type = intval($current_completed_row->tracking_type ?? 0);
        $qc_required = ($product_quality_check == 1 && ($post['qc_required'] ?? '0') == '1' && $session_completed_qty > 0) ? 1 : 0;
        if ($product_quality_check != 1) {
            $qc_required = 0;
        }
        if ($qc_required == 1 && $tracking_type == 1) {
            if (floor($session_completed_qty) != $session_completed_qty) {
                return array('status' => 'error', 'message' => 'Serial tracked production quantity must be a whole number');
            }
            $serials = array();
            foreach ((array)($post['serial_no'] ?? array()) as $serial_no) {
                $serial_no = trim($serial_no);
                if ($serial_no != '') {
                    $serials[] = $serial_no;
                }
            }
            if (count($serials) != intval($session_completed_qty)) {
                return array('status' => 'error', 'message' => 'Please enter one serial number for each completed quantity');
            }
            if (count($serials) != count(array_unique($serials))) {
                return array('status' => 'error', 'message' => 'Duplicate serial numbers are not allowed');
            }
            foreach ($serials as $serial_no) {
                $serial_no_esc = $callConfig->real_escape_string($serial_no);
                $existing_serial = $callConfig->getRow("SELECT id FROM item_serial_nos WHERE company_id = '{$company_id}' AND item_id = '{$current_completed_row->work_order_item_id}' AND serial_no = '{$serial_no_esc}' AND is_active = 0 LIMIT 0,1");
                if (!empty($existing_serial)) {
                    return array('status' => 'error', 'message' => 'Serial number ' . $serial_no . ' already exists for this item');
                }
            }
        }
        if ($qc_required == 1 && $tracking_type == 2) {
            $lot_total = 0;
            $lot_count = 0;
            $lot_nos = array();
            foreach ((array)($post['lot_no'] ?? array()) as $seq => $lot_no) {
                $lot_no = trim($lot_no);
                $lot_qty = floatval($post['lot_qty'][$seq] ?? 0);
                if ($lot_no == '' || $lot_qty <= 0) {
                    return array('status' => 'error', 'message' => 'Lot number and quantity are required for lot tracked production');
                }
                $lot_total += $lot_qty;
                $lot_count++;
                $lot_nos[] = $lot_no;
            }
            if ($lot_count == 0) {
                return array('status' => 'error', 'message' => 'Please enter at least one lot number');
            }
            if (count($lot_nos) != count(array_unique($lot_nos))) {
                return array('status' => 'error', 'message' => 'Duplicate lot numbers are not allowed');
            }
            if (abs($lot_total - $session_completed_qty) > 0.0001) {
                return array('status' => 'error', 'message' => 'Total lot quantity must equal completed quantity');
            }
            foreach ($lot_nos as $lot_no) {
                $lot_no_esc = $callConfig->real_escape_string($lot_no);
                $existing_lot = $callConfig->getRow("SELECT id FROM item_lot_nos WHERE company_id = '{$company_id}' AND item_id = '{$current_completed_row->work_order_item_id}' AND lot_no = '{$lot_no_esc}' AND is_active = 0 LIMIT 0,1");
                if (!empty($existing_lot)) {
                    return array('status' => 'error', 'message' => 'Lot number ' . $lot_no . ' already exists for this item');
                }
            }
        }
        if ($production_type === 'COMPLETED') {
            $child_orders_query = "SELECT child.id, child.transaction_no, child.work_order_item_id, child.work_order_item_qty, child.completed_qty, child.work_log_status, itm.item_name,
                IFNULL((
                    SELECT iwb.closing_qty
                    FROM item_wa_balances iwb
                    WHERE iwb.company_id = child.company_id
                        AND iwb.item_id = child.work_order_item_id
                        AND iwb.balance_date = (
                            SELECT MAX(iwb2.balance_date)
                            FROM item_wa_balances iwb2
                            WHERE iwb2.company_id = iwb.company_id
                                AND iwb2.item_id = iwb.item_id
                        )
                    LIMIT 0,1
                ), 0) AS available_qty
                FROM manufacturing_work_orders child
                LEFT JOIN items itm ON itm.company_id = child.company_id AND itm.id = child.work_order_item_id
                WHERE child.company_id = '{$company_id}'
                    AND child.parent_manufacturing_work_order_id = '{$work_order_id}'
                    AND child.is_active = 0";
            $child_orders = $callConfig->getAllRows($child_orders_query);
            foreach ((array)$child_orders as $child_order) {
                $child_order_qty = floatval($child_order->work_order_item_qty ?? 0);
                $child_completed_qty = floatval($child_order->completed_qty ?? 0);
                $child_available_qty = floatval($child_order->available_qty ?? 0);
                $child_completed = ($child_order->work_log_status == 199 || $child_completed_qty >= $child_order_qty);
                $child_available = ($child_available_qty >= $child_order_qty);
                if (!$child_completed && !$child_available) {
                    $child_item_name = $child_order->item_name ?: ('Item #' . $child_order->work_order_item_id);
                    return array(
                        'status' => 'error',
                        'message' => 'Cannot complete parent work order. Child work order ' . $child_order->transaction_no . ' for ' . $child_item_name . ' must be completed or available in stock.'
                    );
                }
            }
        }
        if (isset($post['item_id']) && is_array($post['item_id'])) {
            foreach ($post['item_id'] as $seq => $item_id) {
                $mwoi_id = intval($post['manufacturing_work_order_item_id'][$seq] ?? 0);
                $consumed_qty = floatval($post['consumed_qty'][$seq] ?? 0);
                if ($mwoi_id > 0 && $consumed_qty > 0) {
                    $current_item_row = $callConfig->getRow(
                        "SELECT completed_qty, request_qty, stock_receipts_qty FROM manufacturing_work_order_items WHERE id = '{$mwoi_id}' AND company_id = '{$company_id}' AND is_active = 0"
                    );
                    $current_item_completed = floatval($current_item_row->completed_qty ?? 0);
                    $available_material_qty = floatval($current_item_row->stock_receipts_qty ?? 0);
                    if ($available_material_qty <= 0) {
                        $available_material_qty = floatval($current_item_row->request_qty ?? 0);
                    }
                    if (($current_item_completed + $consumed_qty) > $available_material_qty) {
                        return array('status' => 'error', 'message' => 'Consumed quantity cannot exceed received material quantity');
                    }
                }
            }
        }

        try {
            // 1. Insert new record in manufacturing_production for audit trail
            $production_data = array(
                'work_order_id' => $work_order_id,
                'production_type' => $production_type,
                'originator' => $created_by,
                'date_time' => date('Y-m-d H:i:s'),
                'company_id' => $company_id,
                'hold_reason' => $hold_reason,
                'is_active' => 0,
                'current_status' => '0',
                'remarks' => $remarks,
                'created_by' => $created_by,
                'created_date' => date('Y-m-d H:i:s')
            );
            
            $prod_result = $callConfig->insertRecord('manufacturing_production', $production_data);

            // 2. Update manufacturing_work_orders
            $total_completed_qty = $current_completed_qty + $session_completed_qty;
            $work_log_status = ($production_type === 'HOLD') ? 1 : (($total_completed_qty >= $ordered_qty) ? 199 : 0);
            $wo_update = array(
                'work_log_status' => $work_log_status,
                'completed_qty' => $total_completed_qty
            );

            // Add actual_end_date if COMPLETED
            if ($production_type === 'COMPLETED' && $work_log_status == 199) {
                $wo_update['actual_end_date'] = date('Y-m-d H:i:s');
            }

            $wo_result = $callConfig->updateRecord('manufacturing_work_orders', $wo_update, 'id', $work_order_id);

            $production_post = array(
                'company_id' => $company_id,
                'created_by' => $created_by,
                'work_order_id' => $work_order_id,
                'item_id'       => $current_completed_row->work_order_item_id,
                'request_qty'   => $current_completed_row->work_order_item_qty,
                'produced_qty'  => $completed_qty,
                'wastage_qty'   => 0,
                'remarks' => $remarks,
                'qc_required' => $qc_required,
                'tracking_type' => $tracking_type,
                'serial_no' => $post['serial_no'] ?? array(),
                'lot_no' => $post['lot_no'] ?? array(),
                'lot_qty' => $post['lot_qty'] ?? array(),
                'skip_work_order_updates' => 1,
            );

            // 3. Update manufacturing_work_order_items with consumed quantities
            if (isset($post['item_id']) && is_array($post['item_id'])) {
                foreach ($post['item_id'] as $seq => $item_id) {
                    $mwoi_id = $post['manufacturing_work_order_item_id'][$seq] ?? 0;
                    $consumed_qty = $post['consumed_qty'][$seq] ?? 0;
                    
                    if ($mwoi_id > 0 && $consumed_qty > 0) {
                        $current_item_row = $callConfig->getRow(
                            "SELECT completed_qty,item_id, request_qty FROM manufacturing_work_order_items WHERE id = '{$mwoi_id}' AND company_id = '{$company_id}' AND is_active = 0"
                        );
                        $current_item_completed = floatval($current_item_row->completed_qty ?? 0);
                        $total_item_completed = $current_item_completed + floatval($consumed_qty);
                        $item_update = array(
                            'completed_qty' => $total_item_completed
                        );
                        $callConfig->updateRecord('manufacturing_work_order_items', $item_update, 'id', $mwoi_id);

                        $production_post['note_items'][] = array(
                            'item_id'      => $current_item_row->item_id ?? 0,
                            'received_qty' => $current_item_row->request_qty ?? 0,
                            'produced_qty' => $consumed_qty ?? 0,
                            'wastage_qty'  => 0,
                        );
                    }

                }
            }
            $production_res = array();
            if ($session_completed_qty > 0) {
                $production_res = $this->insertmanufacturingproductnote($get, $production_post);
            }

            return array(
                'status' => 'success',
                'message' => 'Production ' . strtolower($production_type) . ' successfully',
                'production_type' => $production_type,
                'work_log_status' => $work_log_status,
                'qc_required' => $qc_required,
                'production_res' => $production_res
            );

        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => 'Error updating production data: ' . $e->getMessage()
            );
        }
    }

    function resumeManufacturingProduction($get, $post) {
        global $callConfig;
        
        $work_order_id = $get['work_order_id'] ?? $post['work_order_id'] ?? 0;
        $company_id = $get['company_id'] ?? $post['company_id'] ?? 0;
        $created_by = $get['created_by'] ?? $post['created_by'] ?? 0;
        $remarks = $post['remarks'] ?? '';

        if (!$work_order_id || !$company_id) {
            return array(
                'status' => 'error',
                'message' => 'Work Order ID and Company ID are required',
                'code' => 'MISSING_PARAMETERS'
            );
        }

        $workOrder = $callConfig->getRow("SELECT id, current_status, work_log_status FROM manufacturing_work_orders WHERE company_id = '{$company_id}' AND id = '{$work_order_id}' AND is_active = 0");
        if (empty($workOrder->id)) {
            return array('status' => 'error', 'message' => 'Work Order not found');
        }
        if ($workOrder->current_status != ST_APPROVED) {
            return array('status' => 'error', 'message' => 'Work Order must be approved before resuming production');
        }
        if ($workOrder->work_log_status != 1) {
            return array('status' => 'error', 'message' => 'Only held production can be resumed');
        }

        // Insert record in manufacturing_production for audit trail
        $data = array(
            'work_order_id' => $work_order_id,
            'production_type' => 'RESUME',
            'originator' => $created_by,
            'date_time' => date('Y-m-d H:i:s'),
            'company_id' => $company_id,
            'is_active' => 0,
            'current_status' => '0',
            'remarks' => $remarks,
            'created_by' => $created_by,
            'created_date' => date('Y-m-d H:i:s')
        );

        $result = $callConfig->insertRecord('manufacturing_production', $data);

        if ($result['status'] == 'sucess') {
            // Update manufacturing_work_orders - set work_log_status to 0 (resumed/in-progress)
            $wo_update = array(
                'work_log_status' => 0
            );
            $callConfig->updateRecord('manufacturing_work_orders', $wo_update, 'id', $work_order_id);

            return array(
                'status' => 'success',
                'message' => 'Production resumed successfully',
                'data' => $result
            );
        } else {
            return array(
                'status' => 'error',
                'message' => 'Failed to resume production',
                'error' => $result
            );
        }
    }
    function getManufacturingWorkOrderQcData($get){
        global $callConfig;
        $company_id = intval($get['company_id'] ?? $callConfig->company_id ?? 0);
        $query = "SELECT mpn.id, mpn.transaction_no, mpn.transaction_date, mpn.work_order_id, mpn.item_id, mpn.produced_qty,
                mwo.transaction_no AS work_order_no, i.item_name, i.sku,
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM item_lot_nos iln
                        WHERE iln.company_id = mpn.company_id
                            AND iln.transaction_table = 'manufacturing_product_notes'
                            AND iln.transaction_id = mpn.id
                            AND iln.item_id = mpn.item_id
                            AND iln.is_active = 0
                    ) THEN 2
                    WHEN EXISTS (
                        SELECT 1 FROM item_serial_nos isn
                        WHERE isn.company_id = mpn.company_id
                            AND isn.transaction_table = 'manufacturing_product_notes'
                            AND isn.transaction_id = mpn.id
                            AND isn.item_id = mpn.item_id
                            AND isn.is_active = 0
                    ) THEN 1
                    ELSE 0
                END AS tracking_type,
                atqc.id AS qc_header_id, atqc.current_status AS qc_status,
                IFNULL((
                    SELECT COUNT(1) FROM item_quality_check iqc
                    WHERE iqc.company_id = mpn.company_id
                        AND iqc.application_table_name = 'manufacturing_product_notes'
                        AND iqc.transaction_id = mpn.id
                        AND iqc.item_id = mpn.item_id
                        AND iqc.is_active = 0
                ), 0) AS qc_result_count
            FROM manufacturing_product_notes mpn
            INNER JOIN application_table_quality_check atqc ON atqc.company_id = mpn.company_id
                AND atqc.application_table_name = 'manufacturing_product_notes'
                AND atqc.transaction_id = mpn.id
                AND atqc.is_active = 0
            LEFT JOIN manufacturing_work_orders mwo ON mwo.company_id = mpn.company_id AND mwo.id = mpn.work_order_id
            LEFT JOIN items i ON i.company_id = mpn.company_id AND i.id = mpn.item_id
            WHERE mpn.company_id = '{$company_id}' AND mpn.is_active = 0
            ORDER BY mpn.id DESC";
        $res = $callConfig->getAllRows($query);
        $return['data'] = $res ?? array();

        return $return;

    }

}
?>
