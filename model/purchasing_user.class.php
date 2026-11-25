<?php
interface PurchasingConstants
{
    public const AND_IS_ACTIVE_0_NOSPACE = "' AND is_active =0";
    public const AND_IS_ACTIVE_0 = "' AND is_active = 0";
    public const AND_COMPANY_ID = "' AND company_id = '";
    public const DATE_FORMAT_HIS = "Y-m-d H:i:s";
    public const ERROR_PREFIX = "Error: ";
    public const MSG_WORK_ORDER_NOT_FOUND = "Subcontract work order not found";
    public const MSG_SELECT_WORK_ORDER = "Please select a subcontract work order";
}

trait PurchasingSubcontractReceiptTrait
{
    public function getSubcontractWorkOrderReceiptMetrics($company_id, $work_order_id, $exclude_receipt_id = '')
    {
        global $callConfig;

        $receipt_where = "company_id = '" . $company_id . "' AND ref_id = '" . $work_order_id . "' AND is_active = 0 AND (current_status IS NULL OR current_status NOT IN ('" . ST_REJECTED . "', '" . ST_CANCEL . "', '" . ST_INACTIVE . "'))";
        if ($exclude_receipt_id != '') {
            $receipt_where .= " AND id != '" . $exclude_receipt_id . "'";
        }

        $receipt_qty_sql = "SELECT COALESCE(SUM(received_qty), 0) as total_received_qty FROM subcontract_work_order_receipts WHERE " . $receipt_where;
        $receipt_qty_res = $callConfig->getRow($receipt_qty_sql);
        $total_received_qty = isset($receipt_qty_res->total_received_qty) ? (float) $receipt_qty_res->total_received_qty : 0;

        return array(
            'total_received_qty' => $total_received_qty
        );
    }

    public function getSubcontractReceiptWorkOrderDetails($company_id, $work_order_id, $exclude_receipt_id = '')
    {
        global $callConfig;

        $work_order_sql = "SELECT swo.id, swo.transaction_number, swo.transaction_date, swo.product_id, swo.party_id, swo.party_name, swo.request_warehouse_id, swo.party_warehouse_id, swo.work_order_item_qty, swo.work_order_item_price, swo.work_order_total_amount as item_total_amount, ven.company as vendor_company, ven.display_name as vendor_display_name, ven.email as vendor_email, ven.phone as vendor_phone, ven.billing_address, ven.shipping_address, ven.billing_state, ven.shipping_state, ven.gstin, ven.pan_no, ownw.warehouse_name as requested_warehouse_name, venw.warehouse_name as vendor_warehouse_name, itm.item_name as product_name, itm.sku as product_sku, itm.purchase_uom as product_uom, uom.uom_name as product_uom_name
                           FROM subcontract_work_orders swo
                           LEFT JOIN vendors ven ON swo.party_id = ven.id AND ven.company_id = swo.company_id AND ven.is_active = 0
                           LEFT JOIN warehouses ownw ON swo.request_warehouse_id = ownw.id AND ownw.company_id = swo.company_id AND ownw.is_active = 0
                           LEFT JOIN warehouses venw ON swo.party_warehouse_id = venw.id AND venw.company_id = swo.company_id AND venw.is_active = 0
                           LEFT JOIN items itm ON swo.product_id = itm.id AND itm.company_id = swo.company_id AND itm.is_active = 0
                           LEFT JOIN unit_of_measurements uom ON itm.purchase_uom = uom.id AND uom.company_id = swo.company_id AND uom.is_active = 0
                           WHERE swo.id = '" . $work_order_id . self::AND_COMPANY_ID . $company_id . self::AND_IS_ACTIVE_0;
        $work_order_res = $callConfig->getRow($work_order_sql);

        if (isset($work_order_res->id)) {
            $metrics = $this->getSubcontractWorkOrderReceiptMetrics($company_id, $work_order_id, $exclude_receipt_id);
            $total_received_qty = isset($metrics['total_received_qty']) ? (float) $metrics['total_received_qty'] : 0;
            $work_order_qty = isset($work_order_res->work_order_item_qty) ? (float) $work_order_res->work_order_item_qty : 0;
            $pending_receipt_qty = $work_order_qty - $total_received_qty;
            if ($pending_receipt_qty < 0) {
                $pending_receipt_qty = 0;
            }

            $work_order_res->total_received_qty = $total_received_qty;
            $work_order_res->pending_receipt_qty = $pending_receipt_qty;
        }

        return $work_order_res;
    }

    public function getSubcontractWorkOrderReceipts($get)
    {
        global $callConfig;

        $return = array();
        $return['status'] = 'success';

        $dropdown_sql = "SELECT swo.id, swo.transaction_number
                         FROM subcontract_work_orders swo
                         WHERE swo.company_id = '" . $get['company_id'] . "'
                         AND swo.is_active = 0
                         AND swo.current_status = '" . ST_APPROVED . "'
                         AND swo.work_order_item_qty > COALESCE((SELECT SUM(swr.received_qty)
                                                                 FROM subcontract_work_order_receipts swr
                                                                 WHERE swr.company_id = swo.company_id
                                                                 AND swr.ref_id = swo.id
                                                                 AND swr.is_active = 0
                                                                 AND (swr.current_status IS NULL OR swr.current_status NOT IN ('" . ST_REJECTED . "', '" . ST_CANCEL . "', '" . ST_INACTIVE . "'))), 0)
                         ORDER BY swo.transaction_date DESC, swo.id DESC";
        $dropdown_rows = $callConfig->getAllRows($dropdown_sql);
        $return['subcontract_work_orders_list'] = (array) $dropdown_rows;

        $selected_work_order_id = isset($get['work_order_id']) ? $get['work_order_id'] : '';
        if ($selected_work_order_id != '') {
            $return['work_order_details'] = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $selected_work_order_id, '');
        }

        return $return;
    }

    public function calculateSubcontractWorkOrderReceiptTaxDetails($get, $work_order_id, $received_qty)
    {
        global $callConfig;

        $return = array(
            'status' => 'success',
            'amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'tax_type' => 'I',
            'gst_percentage' => 0,
            'hsn_code_id' => 0,
            'hsn_code' => '',
            'tax_split_details' => array()
        );

        $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
        if (count((array) $work_order_details) == 0) {
            $return['status'] = 'fail';
            $return['message'] = self::MSG_WORK_ORDER_NOT_FOUND;
            return $return;
        }

        $item_hsn_sql = "SELECT hsn.id as hsn_id, hsn.code, hsn.gst FROM items itm LEFT JOIN hsn_and_sac_codes hsn ON itm.hsn_and_sac_code_id = hsn.id AND hsn.company_id = itm.company_id AND hsn.is_active = 0 WHERE itm.id = '" . $work_order_details->product_id . "' AND itm.company_id = '" . $get['company_id'] . "' AND itm.is_active = 0";
        $item_hsn_res = $callConfig->getRow($item_hsn_sql);
        $gst_percentage = ($item_hsn_res && isset($item_hsn_res->gst) && $item_hsn_res->gst != '') ? (float) $item_hsn_res->gst : 0;
        $hsn_code_id = ($item_hsn_res && isset($item_hsn_res->hsn_id) && $item_hsn_res->hsn_id != '') ? (int) $item_hsn_res->hsn_id : 0;
        $hsn_code = ($item_hsn_res && isset($item_hsn_res->code) && $item_hsn_res->code != '') ? $item_hsn_res->code : '';
        $tax_type = 'I';

        $item_price = isset($work_order_details->work_order_item_price) ? (float) $work_order_details->work_order_item_price : 0;
        $amount = round($received_qty * $item_price, 2);
        $tax_amount = round(($amount * $gst_percentage) / 100, 2);
        $total_amount = round($amount + $tax_amount, 2);

        $total_tax_split_details = array();
        if ($gst_percentage > 0) {
            if ($tax_type == 'S') {
                $total_tax_split_details[] = array(
                    'tax_name' => 'SGST',
                    'tax_percentage' => round($gst_percentage * 0.5, 2),
                    'tax_amount' => round($tax_amount * 0.5, 2),
                    'account_code' => ''
                );
                $total_tax_split_details[] = array(
                    'tax_name' => 'CGST',
                    'tax_percentage' => round($gst_percentage * 0.5, 2),
                    'tax_amount' => round($tax_amount * 0.5, 2),
                    'account_code' => ''
                );
            } else {
                $total_tax_split_details[] = array(
                    'tax_name' => 'IGST',
                    'tax_percentage' => round($gst_percentage, 2),
                    'tax_amount' => round($tax_amount, 2),
                    'account_code' => ''
                );
            }
        }

        $return['amount'] = $amount;
        $return['tax_amount'] = $tax_amount;
        $return['total_amount'] = $total_amount;
        $return['tax_type'] = $tax_type;
        $return['gst_percentage'] = $gst_percentage;
        $return['hsn_code_id'] = $hsn_code_id;
        $return['hsn_code'] = $hsn_code;
        $return['tax_split_details'] = $total_tax_split_details;

        return $return;
    }

    public function getSubcontractWorkOrderReceiptTaxPreview($get, $post)
    {
        $work_order_id = isset($post['subcontract_work_order_id']) ? $post['subcontract_work_order_id'] : '';
        $received_qty = isset($post['received_qty']) ? (float) $post['received_qty'] : 0;

        if ($work_order_id == '') {
            return array('status' => 'fail', 'message' => self::MSG_SELECT_WORK_ORDER);
        }

        if ($received_qty <= 0) {
            return array('status' => 'fail', 'message' => 'Received qty should be greater than zero');
        }

        return $this->calculateSubcontractWorkOrderReceiptTaxDetails($get, $work_order_id, $received_qty);
    }

    private function validateReceiptInputs(string $work_order_id, float $received_qty, array $get): array
    {
        if ($work_order_id == '') {
            return array('status' => 'fail', 'parent_insert_update' => array('status' => 'fail', 'error_no' => '400', 'message' => self::MSG_SELECT_WORK_ORDER));
        }

        if ($received_qty <= 0) {
            return array('status' => 'fail', 'parent_insert_update' => array('status' => 'fail', 'error_no' => '400', 'message' => 'Received qty should be greater than zero'));
        }

        $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
        $metrics = $this->getSubcontractWorkOrderReceiptMetrics($get['company_id'], $work_order_id, '');
        $total_received_qty = isset($metrics['total_received_qty']) ? (float) $metrics['total_received_qty'] : 0;
        $work_order_item_qty = isset($work_order_details->work_order_item_qty) ? (float) $work_order_details->work_order_item_qty : 0;
        $allowed_pending_qty = max(0, $work_order_item_qty - $total_received_qty);

        if (empty($work_order_details)) {
            $result = array('status' => 'fail', 'parent_insert_update' => array('status' => 'fail', 'error_no' => '404', 'message' => self::MSG_WORK_ORDER_NOT_FOUND));
        } elseif ($received_qty > $allowed_pending_qty) {
            $result = array('status' => 'fail', 'parent_insert_update' => array('status' => 'fail', 'error_no' => '400', 'message' => 'Received qty cannot be greater than pending receipt qty'));
        } else {
            $result = array(
                'status' => 'success',
                'work_order_details' => $work_order_details,
                'total_received_qty' => $total_received_qty
            );
        }
        return $result;
    }

    private function resolvePartyName($work_order_details): string
    {
        if ($work_order_details->party_name != '') {
            return $work_order_details->party_name;
        }
        if ($work_order_details->vendor_company != '') {
            return $work_order_details->vendor_company;
        }
        return (string) $work_order_details->vendor_display_name;
    }

    private function parseTaxSplitDetails(?string $raw): array
    {
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return array();
    }

    private function prepareReceiptHeaderData(array $post, array $get, $work_order_details, float $received_qty, float $total_received_qty): array
    {
        $transaction_date = !empty($post['transaction_date']) ? date('Y-m-d', strtotime($post['transaction_date'])) : date('Y-m-d');
        $gst_percentage = (float) ($post['receipt_gst_percentage'] ?? 0);
        $hsn_code_id = (int) ($post['receipt_hsn_code_id'] ?? 0);
        $hsn_code = (string) ($post['receipt_hsn_code'] ?? '');
        $tax_type = !empty($post['receipt_tax_type']) ? $post['receipt_tax_type'] : 'I';
        $amount = (float) ($post['receipt_amount'] ?? 0);
        $tax_amount = (float) ($post['receipt_tax_amount'] ?? 0);
        $total_amount = (float) ($post['receipt_total_amount'] ?? 0);

        $work_order_item_qty = (float) ($work_order_details->work_order_item_qty ?? 0);
        $pending_qty = max(0, round(($work_order_item_qty - $total_received_qty) - $received_qty, 2));

        $total_tax_split_details = $this->parseTaxSplitDetails($post['receipt_tax_split_details'] ?? null);

        $party_additional_details = json_encode(array(
            'gstin' => $work_order_details->gstin,
            'pan_no' => $work_order_details->pan_no
        ));

        $party_name = $this->resolvePartyName($work_order_details);

        $warehouse_id = !empty($post['transaction_warehouse_id']) ? $post['transaction_warehouse_id'] : $work_order_details->request_warehouse_id;

        $postdata = array(
            'company_id' => $get['company_id'],
            'transaction_date' => $transaction_date,
            'transaction_warehouse_id' => $warehouse_id,
            'request_warehouse_id' => $work_order_details->party_warehouse_id,
            'originator' => $get['user_id'],
            'party_id' => $work_order_details->party_id,
            'party_name' => $party_name,
            'party_email' => $work_order_details->vendor_email,
            'party_tax_number' => $work_order_details->gstin,
            'party_phone' => $work_order_details->vendor_phone,
            'party_address' => $work_order_details->billing_address,
            'party_ship_adderss' => $work_order_details->shipping_address,
            'notes' => $post['notes'] ?? '',
            'amount' => $amount,
            'tax_amount' => $tax_amount,
            'tax_type' => $tax_type,
            'discount_type' => 'P',
            'discount_value' => 0,
            'discount_amount' => 0,
            'total_amount' => $total_amount,
            'pending_amount' => $total_amount,
            'tax_state' => $tax_type,
            'opex_cpex' => 0,
            'ref_type' => 'subcontract_work_orders',
            'ref_id' => $work_order_details->id,
            'ref_no' => $work_order_details->transaction_number,
            'ref_date' => $work_order_details->transaction_date,
            'received_qty' => $received_qty,
            'pending_qty' => $pending_qty,
            'additional_charges' => json_encode(array()),
            'party_additional_details' => $party_additional_details,
            'total_tax_split_details' => json_encode($total_tax_split_details),
            'discount_mode' => 'DO',
            'multi_reference' => 'N',
            'is_active' => 0,
            'current_status' => ST_APPROVED,
            'created_date' => date(self::DATE_FORMAT_HIS),
            'created_by' => $get['user_id']
        );

        return array(
            'postdata' => $postdata,
            'transaction_date' => $transaction_date,
            'amount' => $amount,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount,
            'gst_percentage' => $gst_percentage,
            'hsn_code_id' => $hsn_code_id,
            'hsn_code' => $hsn_code,
            'tax_type' => $tax_type,
            'pending_qty' => $pending_qty,
            'total_tax_split_details' => $total_tax_split_details
        );
    }

    private function saveReceiptItemAndTaxes($rid, $work_order_details, array $post, array $get, float $received_qty, array $headerData): array
    {
        global $callConfig;

        $item_account_code = '';
        $item_account_sql = "SELECT inventory_asset_account FROM items WHERE id = '" . $work_order_details->product_id . "' AND company_id = '" . $get['company_id'] . self::AND_IS_ACTIVE_0;
        $item_account_res = $callConfig->getRow($item_account_sql);
        if ($item_account_res && isset($item_account_res->inventory_asset_account)) {
            $item_account_code = $item_account_res->inventory_asset_account;
        }

        $receipt_item_data = array(
            'company_id' => $get['company_id'],
            'transaction_id' => $rid,
            'item_id' => $work_order_details->product_id,
            'account_code' => $item_account_code,
            'item_name' => isset($work_order_details->product_name) ? $work_order_details->product_name : '',
            'description' => isset($post['notes']) ? $post['notes'] : '',
            'purchase_uom' => isset($work_order_details->product_uom) ? $work_order_details->product_uom : '',
            'invoice_item_quantity' => $received_qty,
            'item_quantity' => $received_qty,
            'item_price' => isset($work_order_details->work_order_item_price) ? (float) $work_order_details->work_order_item_price : 0,
            'item_amount' => $headerData['amount'],
            'tax_amount' => $headerData['tax_amount'],
            'discount_amount' => 0,
            'total_amount' => $headerData['total_amount'],
            'doc_ref_type' => 'subcontract_work_orders',
            'doc_ref_id' => $work_order_details->id,
            'doc_ref_no' => $work_order_details->transaction_number,
            'doc_ref_date' => $work_order_details->transaction_date,
            'approved_quantity' => $received_qty,
            'transaction_qty' => $received_qty,
            'pending_qty' => $received_qty,
            'ref_transaction_item_table_id' => 0,
            'ref_transaction_table_id' => $work_order_details->id,
            'is_active' => 0,
            'current_status' => ST_APPROVED,
            'created_date' => date(self::DATE_FORMAT_HIS),
            'created_by' => $get['user_id']
        );
        $receipt_item_insert = $callConfig->insertRecord('subcontract_work_order_receipt_items', $receipt_item_data);
        $receipt_item_id = isset($receipt_item_insert['LASTID']) ? $receipt_item_insert['LASTID'] : 0;

        if ($receipt_item_id == 0) {
            return array('status' => 'fail', 'error_no' => '500', 'message' => 'Unable to save receipt item');
        }

        if ($headerData['gst_percentage'] > 0) {
            $taxfield = array(
                'company_id' => $get['company_id'],
                'transaction_id' => $rid,
                'transaction_item_id' => $work_order_details->product_id,
                'tax_name' => $headerData['hsn_code'],
                'tax_value' => $headerData['gst_percentage'],
                'tax_id' => $headerData['hsn_code_id'],
                'amount' => $headerData['amount'],
                'tax_amount' => $headerData['tax_amount'],
                'total_amount' => $headerData['amount'],
                'tax_type' => $headerData['tax_type'],
                'tax_split_details' => json_encode($headerData['total_tax_split_details']),
                'transaction_item_row_id' => $receipt_item_id,
                'is_active' => 0,
                'current_status' => 0,
                'created_date' => date(self::DATE_FORMAT_HIS),
                'created_by' => $get['user_id']
            );
            $callConfig->insertRecord('subcontract_work_order_receipt_item_taxes', $taxfield);
        }

        return array('status' => 'success');
    }

    public function saveSubcontractWorkOrderReceipt($get, $post)
    {
        global $callConfig;

        $return = array();

        try {
            $work_order_id = isset($post['subcontract_work_order_id']) ? $post['subcontract_work_order_id'] : '';
            $received_qty = isset($post['received_qty']) ? (float) $post['received_qty'] : 0;

            $validation = $this->validateReceiptInputs($work_order_id, $received_qty, $get);
            if (($validation['status'] ?? '') !== 'success') {
                return $validation;
            }

            $work_order_details = $validation['work_order_details'];
            $total_received_qty = $validation['total_received_qty'];

            $headerData = $this->prepareReceiptHeaderData($post, $get, $work_order_details, $received_qty, $total_received_qty);
            $get['transaction_date'] = $headerData['transaction_date'];
            $headerData['postdata']['transaction_number'] = $this->toGetApplicationSerialNumber('subcontract_work_order_receipts', $get);

            $insert_res = $callConfig->insertRecord('subcontract_work_order_receipts', $headerData['postdata']);
            $rid = isset($insert_res['LASTID']) ? $insert_res['LASTID'] : 0;
            if ($rid == 0) {
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '500', 'message' => 'Unable to save receipt');
            } else {
                $itemRes = $this->saveReceiptItemAndTaxes($rid, $work_order_details, $post, $get, $received_qty, $headerData);
                if (($itemRes['status'] ?? '') !== 'success') {
                    $return['parent_insert_update'] = $itemRes;
                } else {
                    if ($headerData['pending_qty'] <= 0) {
                        $callConfig->updateRecord('subcontract_work_orders', array('work_order_closed' => 'Y'), 'id', $work_order_details->id);
                    }
                    $return['parent_insert_update'] = array(
                        'status' => 'success',
                        'LASTID' => $rid,
                        'message' => 'Receipt saved successfully'
                    );
                }
            }
        } catch (Exception $e) {
            $return['parent_insert_update'] = array(
                'status' => 'fail',
                'error_no' => '500',
                'message' => self::ERROR_PREFIX . $e->getMessage()
            );
        }

        return $return;
    }

    private function fetchTaxSplitDetails($row, $company_id): array
    {
        global $callConfig;
        if (!empty($row->total_tax_split_details)) {
            $decoded = json_decode($row->total_tax_split_details, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        $tax_sql = "SELECT tax_name, tax_value as tax_percentage, tax_amount
                    FROM subcontract_work_order_receipt_item_taxes
                    WHERE company_id = '" . $company_id . "'
                    AND transaction_id = '" . $row->id . "'
                    AND is_active = 0
                    ORDER BY id ASC";
        $tax_rows = $callConfig->getAllRows($tax_sql);
        $tax_split_details = array();
        foreach ((array) $tax_rows as $tax_row) {
            $tax_split_details[] = array(
                'tax_name' => $tax_row->tax_name ?? '',
                'tax_percentage' => (float) ($tax_row->tax_percentage ?? 0),
                'tax_amount' => (float) ($tax_row->tax_amount ?? 0)
            );
        }
        return $tax_split_details;
    }

    private function formatReceiptHistoryRow($row, $company_id): array
    {
        return array(
            'id' => $row->id,
            'transaction_number' => $row->transaction_number,
            'transaction_date' => $row->transaction_date,
            'received_qty' => (float) ($row->received_qty ?? 0),
            'pending_qty' => (float) ($row->pending_qty ?? 0),
            'amount' => (float) ($row->amount ?? 0),
            'tax_amount' => (float) ($row->tax_amount ?? 0),
            'total_amount' => (float) ($row->total_amount ?? 0),
            'tax_type' => $row->tax_type ?? '',
            'tax_split_details' => $this->fetchTaxSplitDetails($row, $company_id),
            'notes' => $row->notes ?? '',
            'created_date' => $row->created_date ?? ''
        );
    }

    public function getSubcontractWorkOrderReceiptHistory($get)
    {
        global $callConfig;

        $return = array();
        $work_order_id = isset($get['work_order_id']) ? $get['work_order_id'] : '';

        if ($work_order_id == '') {
            $return['status'] = 'fail';
            $return['message'] = self::MSG_SELECT_WORK_ORDER;
            return $return;
        }

        $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
        if (count((array) $work_order_details) == 0) {
            $return['status'] = 'fail';
            $return['message'] = self::MSG_WORK_ORDER_NOT_FOUND;
            return $return;
        }

        $history_sql = "SELECT id, transaction_number, transaction_date, received_qty, pending_qty, amount, tax_amount, total_amount, tax_type, total_tax_split_details, notes, created_date
                        FROM subcontract_work_order_receipts
                        WHERE company_id = '" . $get['company_id'] . "'
                        AND ref_id = '" . $work_order_id . "'
                        AND is_active = 0
                        AND (current_status IS NULL OR current_status NOT IN ('" . ST_REJECTED . "', '" . ST_CANCEL . "', '" . ST_INACTIVE . "'))
                        ORDER BY transaction_date ASC, id ASC";
        $history_rows = $callConfig->getAllRows($history_sql);
        $history = array();

        foreach ((array) $history_rows as $row) {
            $history[] = $this->formatReceiptHistoryRow($row, $get['company_id']);
        }

        $return['status'] = 'success';
        $return['work_order'] = array(
            'transaction_number' => $work_order_details->transaction_number,
            'work_order_item_qty' => isset($work_order_details->work_order_item_qty) ? (float) $work_order_details->work_order_item_qty : 0,
            'total_received_qty' => isset($work_order_details->total_received_qty) ? (float) $work_order_details->total_received_qty : 0,
            'pending_receipt_qty' => isset($work_order_details->pending_receipt_qty) ? (float) $work_order_details->pending_receipt_qty : 0,
            'product_name' => isset($work_order_details->product_name) ? $work_order_details->product_name : '',
            'product_sku' => isset($work_order_details->product_sku) ? $work_order_details->product_sku : '',
            'party_name' => isset($work_order_details->party_name) ? $work_order_details->party_name : ''
        );
        $return['history'] = $history;

        return $return;
    }
}

class PurchasingUserClass extends userClass implements PurchasingConstants
{
    use PurchasingSubcontractReceiptTrait;

    public function getSubcontractWorkOrders($get)
    {
        global $callConfig;
        $return = array();

        $vendorssql = "SELECT id, company, display_name FROM vendors WHERE company_id = '" . $get['company_id'] . self::AND_IS_ACTIVE_0_NOSPACE;
        $return['vendors'] = $callConfig->getAllRows($vendorssql);

        $itemssql = "SELECT id, sku, item_name, stocking_type, purchase_price,purchase_uom FROM items WHERE company_id = '" . $get['company_id'] . self::AND_IS_ACTIVE_0_NOSPACE;
        $return['items'] = $callConfig->getAllRows($itemssql);

        $warehousessql = "SELECT id, warehouse_name, warehous_type FROM warehouses WHERE company_id = '" . $get['company_id'] . self::AND_IS_ACTIVE_0_NOSPACE;
        $warehousesres = $callConfig->getAllRows($warehousessql);

        $warehouseslist = array();
        foreach ($warehousesres as $warehs) {
            $warehouseslist[$warehs->warehous_type][] = $warehs;
        }
        $return['warehouses'] = $warehouseslist;

        $uomsql = "SELECT id, uom_name FROM unit_of_measurements WHERE company_id = '" . $get['company_id'] . self::AND_IS_ACTIVE_0;
        $return['unit_of_measurements'] = $callConfig->getAllRows($uomsql);

        $return['itemworktype'] = array('F' => 'FINISHED GOODS', 'W' => 'WIP', 'R' => 'RAW MATERIAL', 'C' => 'CHILD PART');

        if ($get['rid'] != "") {
            $subcontractsql = "SELECT * FROM subcontract_work_orders WHERE id = '" . $get['rid'] . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['subcontract_work_order'] = $callConfig->getRow($subcontractsql);

            $subcontractitemssql = "SELECT * FROM subcontract_work_order_items WHERE transaction_id = '" . $get['rid'] . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['subcontract_work_order_items'] = $callConfig->getAllRows($subcontractitemssql);
        }

        return $return;
    }

    private function saveSubcontractOrderItems($rid, array $post, array $get): float
    {
        global $callConfig;

        $total_amount = 0;
        if ($rid != "" && isset($post['item_id']) && is_array($post['item_id'])) {
            foreach ($post['item_id'] as $seq => $item_id) {
                if ($item_id == "") {
                    continue;
                }
                $itemsql = "SELECT purchase_price,item_name,inventory_asset_account FROM items WHERE id = '" . $item_id . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
                $itemres = $callConfig->getRow($itemsql);

                $item_price = $post['item_price'][$seq] ?? $itemres->purchase_price ?? 0;
                $item_quantity = $post['request_qty'][$seq] ? $post['request_qty'][$seq] : 0;
                $item_amount = $post['item_total_amount'][$seq] ?? ($item_quantity * $item_price);
                $total_amount += $item_amount;

                $itempostdata = array(
                    'company_id' => $get['company_id'],
                    'transaction_id' => $rid,
                    'item_id' => $item_id,
                    'purchase_uom' => $post['uom'][$seq],
                    'item_quantity' => $item_quantity,
                    'item_name' => $itemres->item_name,
                    'account_code' => $itemres->inventory_asset_account,
                    'item_price' => $item_price,
                    'item_amount' => $item_amount,
                    'total_amount' => $item_amount,
                    'description' => $post['description'][$seq],
                    'is_active' => 0,
                    'req_date' => date('Y-m-d'),
                    'approved_quantity' => $item_quantity,
                    'transaction_qty' => $item_quantity,
                    'pending_qty' => $item_quantity,
                );

                if (isset($post['subcontract_work_order_item_id'][$seq]) && $post['subcontract_work_order_item_id'][$seq] != "" && $post['form_updatetion_type'] == "update") {
                    $itempostdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                    $itempostdata['updated_by'] = $get['user_id'];
                    $callConfig->updateRecord('subcontract_work_order_items', $itempostdata, 'id', $post['subcontract_work_order_item_id'][$seq]);
                } else {
                    $itempostdata['current_status'] = '0';
                    $itempostdata['created_date'] = date(self::DATE_FORMAT_HIS);
                    $itempostdata['created_by'] = $get['user_id'];
                    $callConfig->insertRecord('subcontract_work_order_items', $itempostdata);
                }
            }
        }
        return $total_amount;
    }

    public function insertSubcontractWorkOrders($get, $post)
    {
        global $callConfig;
        $return = array();

        try {
            $vendorsql = "SELECT company, display_name, email, phone, billing_address, shipping_address FROM vendors WHERE id = '" . $post['party_id'] . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $vendorres = $callConfig->getRow($vendorsql);

            if (!$vendorres) {
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '404', 'message' => 'Vendor not found');
                return $return;
            }

            $party_name = $vendorres->company != "" ? $vendorres->company : $vendorres->display_name;

            $postdata = array(
                'company_id' => $get['company_id'],
                'transaction_date' => $post['transaction_date'] ? date('Y-m-d', strtotime($post['transaction_date'])) : date('Y-m-d'),
                'request_warehouse_id' => $post['request_warehouse_id'],
                'party_id' => $post['party_id'],
                'party_warehouse_id' => $post['party_warehouse_id'],
                'party_name' => $party_name,
                'party_email' => $vendorres->email,
                'party_phone' => $vendorres->phone,
                'party_address' => $vendorres->billing_address,
                'product_id' => $post['product_id'],
                'work_order_item_qty' => $post['work_order_item_qty'],
                'manufacturing_bom_id' => $post['manufacturing_bom_id'],
                'work_order_item_price' => $post['work_order_item_price'],
                'work_order_total_amount' => $post['work_order_total_amount'],
                'ref_date' => date('Y-m-d'),
                'originator' => $get['user_id'],
                'party_ship_adderss' => $vendorres->shipping_address
            );

            if ($post['rid'] != "") {
                $rid = $post['rid'];
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $get['user_id'];
                $res = $callConfig->updateRecord('subcontract_work_orders', $postdata, 'id', $rid);
                $return['parent_update'] = $res;

                $upary = array('is_active' => null);
                $callConfig->updateRecord('subcontract_work_order_items', $upary, 'transaction_id', $rid);
            } else {
                $get['transaction_date'] = $post['transaction_date'];
                $postdata['transaction_number'] = $this->toGetApplicationSerialNumber('subcontract_work_orders', $get);
                $postdata['is_active'] = '0';
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $get['user_id'];
                $res = $callConfig->insertRecord('subcontract_work_orders', $postdata);
                $return['parent_insert'] = $res;
                $rid = $res['LASTID'];
            }

            $total_amount = $this->saveSubcontractOrderItems($rid, $post, $get);

            if ($rid != "") {
                $amountdata = array('amount' => $total_amount, 'total_amount' => $total_amount);
                $callConfig->updateRecord('subcontract_work_orders', $amountdata, 'id', $rid);
            }

            $return['parent_insert_update'] = array(
                'status' => 'success',
                'LASTID' => $post['rid'] != "" ? 0 : $rid,
                'message' => 'Record saved successfully'
            );

        } catch (Exception $e) {
            $return['parent_insert_update'] = array(
                'status' => 'fail',
                'error_no' => '500',
                'message' => self::ERROR_PREFIX . $e->getMessage()
            );
        }

        return $return;
    }

    private function getIssueRawMaterialsDetails($get, $rid): array
    {
        global $callConfig;

        $parent_sql = "SELECT swo.*,
                               rw.warehouse_name as request_warehouse_name,
                               pw.warehouse_name as party_warehouse_name,
                               itm.item_name as product_name
                       FROM subcontract_work_orders swo
                       LEFT JOIN warehouses rw ON swo.request_warehouse_id = rw.id AND rw.is_active = 0
                       LEFT JOIN warehouses pw ON swo.party_warehouse_id = pw.id AND pw.is_active = 0
                       LEFT JOIN items itm ON swo.product_id = itm.id AND itm.company_id = '" . $get['company_id'] . "' AND itm.is_active = 0
                       WHERE swo.id = '" . $rid . "'
                       AND swo.company_id = '" . $get['company_id'] . "'
                       AND swo.is_active = 0";
        $parent = $callConfig->getRow($parent_sql);

        if (!$parent) {
            return array('status' => 'error', 'message' => 'Record not found');
        }

        $child_sql = "SELECT swoi.*, i.sku, i.item_name, u.uom_name, (swoi.item_quantity * swoi.item_price) as total_amount
                      FROM subcontract_work_order_items swoi
                      LEFT JOIN items i ON swoi.item_id = i.id AND i.company_id = '" . $get['company_id'] . "' AND i.is_active = 0
                      LEFT JOIN unit_of_measurements u ON swoi.purchase_uom = u.id AND u.company_id = '" . $get['company_id'] . "' AND u.is_active = 0
                      WHERE swoi.transaction_id = '" . $rid . "'
                      AND swoi.company_id = '" . $get['company_id'] . "'
                      AND swoi.is_active = 0
                      ORDER BY swoi.id ASC";
        $child_items = $callConfig->getAllRows($child_sql);

        $history_sql = "SELECT scwoi.id, scwoi.transaction_number, scwoi.transaction_date, scwoi.party_name, scwoi.work_order_item_qty, scwoi.work_order_total_amount, scwoi.current_status
                        FROM sub_contract_work_order_issues scwoi
                        WHERE scwoi.company_id = '" . $get['company_id'] . "'
                        AND scwoi.ref_id = '" . $rid . "'
                        AND scwoi.is_active = 0
                        ORDER BY scwoi.id DESC";
        $issues_history = $callConfig->getAllRows($history_sql);

        $issue_history_details = array();
        $history_issue_ids = array();
        foreach ((array) $issues_history as $history_row) {
            $history_issue_id = is_object($history_row) ? ($history_row->id ?? '') : ($history_row['id'] ?? '');
            if ($history_issue_id != '') {
                $history_issue_ids[] = $history_issue_id;
            }
        }

        if (!empty($history_issue_ids)) {
            $history_issue_ids = array_unique($history_issue_ids);
            $issue_ids_in = "'" . implode("','", $history_issue_ids) . "'";

            $history_items_sql = "SELECT scwoii.transaction_id, scwoii.item_quantity, scwoii.issued_quantity, scwoii.issued_amount, itm.sku, itm.item_name, u.uom_name
                                  FROM sub_contract_work_order_issue_items scwoii
                                  LEFT JOIN items itm ON scwoii.item_id = itm.id AND itm.company_id = '" . $get['company_id'] . "' AND itm.is_active = 0
                                  LEFT JOIN unit_of_measurements u ON scwoii.purchase_uom = u.id AND u.company_id = '" . $get['company_id'] . "' AND u.is_active = 0
                                  WHERE scwoii.company_id = '" . $get['company_id'] . "'
                                  AND scwoii.transaction_id IN (" . $issue_ids_in . ")
                                  AND scwoii.is_active = 0
                                  ORDER BY scwoii.transaction_id DESC, scwoii.id ASC";
            $history_items = $callConfig->getAllRows($history_items_sql);

            foreach ((array) $history_items as $history_item_row) {
                $history_transaction_id = is_object($history_item_row) ? ($history_item_row->transaction_id ?? '') : ($history_item_row['transaction_id'] ?? '');
                if ($history_transaction_id != '') {
                    $issue_history_details[$history_transaction_id][] = $history_item_row;
                }
            }
        }

        return array(
            'status' => 'success',
            'parent' => $parent,
            'child_items' => $child_items,
            'issues_history' => $issues_history,
            'issue_history_details' => $issue_history_details
        );
    }

    public function getIssueRawMaterialsToVendor($get)
    {
        global $callConfig;
        $return = array();

        $sql = "SELECT id, transaction_number
                FROM subcontract_work_orders
                WHERE company_id = '" . $get['company_id'] . "'
                AND is_active = 0
                AND current_status = '" . ST_APPROVED . "'
                ORDER BY transaction_date DESC";
        $return['subcontract_work_orders_list'] = $callConfig->getAllRows($sql);
        $return['status'] = 'success';

        if ($get['rid'] != "") {
            $details = $this->getIssueRawMaterialsDetails($get, $get['rid']);
            if (isset($details['status']) && $details['status'] === 'error') {
                return $details;
            }
            $return = array_merge($return, $details);
        }

        return $return;
    }

    private function validateIssueVendorInputs($rid, array $child_ids, array $get): array
    {
        global $callConfig;

        if (!$rid) {
            return array('status' => 'error', 'message' => 'Invalid work order ID');
        }

        $parent_sql = "SELECT * FROM subcontract_work_orders
                       WHERE id = '$rid'
                       AND company_id = '" . $get['company_id'] . "'
                       AND is_active = 0";
        $parent_record = $callConfig->getRow($parent_sql);

        $child_items = array();
        if (!empty($child_ids)) {
            $child_ids_str = "'" . implode("','", $child_ids) . "'";
            $child_sql = "SELECT * FROM subcontract_work_order_items
                          WHERE id IN ($child_ids_str)
                          AND transaction_id = '$rid'
                          AND company_id = '" . $get['company_id'] . "'
                          AND is_active = 0";
            $child_items = $callConfig->getAllRows($child_sql);
        }

        if (!$parent_record) {
            $result = array('status' => 'error', 'message' => 'Work order not found');
        } elseif (empty($child_items)) {
            $result = array('status' => 'error', 'message' => 'No items selected for issue');
        } else {
            $result = array(
                'status' => 'success',
                'parent_record' => $parent_record,
                'child_items' => $child_items
            );
        }
        return $result;
    }

    private function createSubcontractIssueParent($parent_record, $rid, array $get)
    {
        global $callConfig;

        $get['transaction_date'] = $parent_record->transaction_date;
        $transaction_number = $this->toGetApplicationSerialNumber('sub_contract_work_order_issues', $get);

        $issue_parent_data = array(
            'company_id' => $get['company_id'],
            'transaction_number' => $transaction_number ?? null,
            'ref_type' => 'subcontract_work_orders',
            'ref_id' => $rid,
            'ref_no' => $parent_record->transaction_number,
            'ref_date' => $parent_record->transaction_date,
            'transaction_date' => $parent_record->transaction_date,
            'request_warehouse_id' => $parent_record->request_warehouse_id,
            'party_id' => $parent_record->party_id,
            'party_warehouse_id' => $parent_record->party_warehouse_id,
            'party_name' => $parent_record->party_name,
            'party_email' => $parent_record->party_email,
            'party_phone' => $parent_record->party_phone,
            'party_address' => $parent_record->party_address,
            'product_id' => $parent_record->product_id,
            'manufacturing_bom_id' => $parent_record->manufacturing_bom_id,
            'work_order_item_price' => $parent_record->work_order_item_price,
            'work_order_item_qty' => $parent_record->work_order_item_qty,
            'originator' => $get['user_id'],
            'party_ship_adderss' => $parent_record->party_ship_adderss,
            'is_active' => ST_ACTIVE,
            'current_status' => ST_APPROVED,
            'created_date' => date(self::DATE_FORMAT_HIS),
            'created_by' => $get['user_id']
        );

        $issue_parent_insert = $callConfig->insertRecord('sub_contract_work_order_issues', $issue_parent_data);
        return $issue_parent_insert['LASTID'] ?? null;
    }

    private function insertSubcontractIssueItems($child_items, array $issues_qty, $issue_parent_id, $rid, array $get): array
    {
        global $callConfig;

        $total_issued_amount = 0;
        $inserted_items = 0;

        foreach ($child_items as $index => $child_item) {
            if (isset($issues_qty[$index]) && $issues_qty[$index] > 0) {
                $issued_qty = floatval($issues_qty[$index]);
                $issued_amount = $issued_qty * floatval($child_item->item_price);
                $total_issued_amount += $issued_amount;

                $issue_child_data = array(
                    'company_id' => $get['company_id'],
                    'transaction_id' => $issue_parent_id,
                    'ref_transaction_table_id' => $rid,
                    'ref_transaction_item_table_id' => $child_item->id,
                    'item_id' => $child_item->item_id,
                    'purchase_uom' => $child_item->purchase_uom,
                    'item_quantity' => $child_item->item_quantity,
                    'item_name' => $child_item->item_name,
                    'account_code' => $child_item->account_code,
                    'item_price' => $child_item->item_price,
                    'issued_quantity' => $issued_qty,
                    'issued_amount' => $issued_amount,
                    'description' => $child_item->description,
                    'is_active' => 0,
                    'current_status' => ST_APPROVED,
                    'created_date' => date(self::DATE_FORMAT_HIS),
                    'created_by' => $get['user_id']
                );

                $issue_child_insert = $callConfig->insertRecord('sub_contract_work_order_issue_items', $issue_child_data);
                if (($issue_child_insert['status'] ?? '') === 'success' || isset($issue_child_insert['LASTID'])) {
                    $inserted_items++;
                }
            }
        }

        return array('total_amount' => $total_issued_amount, 'inserted_items' => $inserted_items);
    }

    public function saveIssueRawMaterialsToVendor($get, $post)
    {
        global $callConfig;
        $return = array();

        try {
            $rid = $post['rid'] ?? '';
            $child_ids = isset($post['id']) && is_array($post['id']) ? $post['id'] : array();

            $validation = $this->validateIssueVendorInputs($rid, $child_ids, $get);
            if (isset($validation['status']) && $validation['status'] === 'error') {
                return $validation;
            }

            $parent_record = $validation['parent_record'];
            $child_items = $validation['child_items'];

            $issue_parent_id = $this->createSubcontractIssueParent($parent_record, $rid, $get);
            if (!$issue_parent_id) {
                return array('status' => 'error', 'message' => 'Failed to create issue record');
            }

            $issues_qty = isset($post['issues_qty']) && is_array($post['issues_qty']) ? $post['issues_qty'] : array();
            $issueSummary = $this->insertSubcontractIssueItems($child_items, $issues_qty, $issue_parent_id, $rid, $get);

            if ($issueSummary['inserted_items'] > 0) {
                $callConfig->updateRecord('sub_contract_work_order_issues', array('work_order_total_amount' => $issueSummary['total_amount']), 'id', $issue_parent_id);
            }

            $return['status'] = 'success';
            $return['message'] = 'Raw materials issued successfully';
            $return['issue_id'] = $issue_parent_id;
            $return['items_issued'] = $issueSummary['inserted_items'];
            $return['total_amount'] = $issueSummary['total_amount'];

            $this->updateSubcontractWorkOrderPendingQty(array(
                'transaction_id' => $issue_parent_id,
                'company_id' => $get['company_id']
            ));

        } catch (Exception $e) {
            $return['status'] = 'error';
            $return['message'] = self::ERROR_PREFIX . $e->getMessage();
        }

        return $return;
    }

    public function getSubContractWorkOrderIssues($get)
    {
        global $callConfig;
        $return = array();

        if ($get['rid'] != "") {
            $rid = $get['rid'];
            $parent_sql = "SELECT scwoi.*,
                                  swo.transaction_number as subcontract_work_order_number,
                                  rw.warehouse_name as request_warehouse_name,
                                  pw.warehouse_name as party_warehouse_name,
                                  i.item_name as product_name,
                                  i.sku as product_sku
                           FROM sub_contract_work_order_issues scwoi
                           LEFT JOIN subcontract_work_orders swo ON scwoi.ref_id = swo.id AND swo.company_id = '" . $get['company_id'] . "' AND swo.is_active = 0
                           LEFT JOIN warehouses rw ON scwoi.request_warehouse_id = rw.id AND rw.company_id = '" . $get['company_id'] . "' AND rw.is_active = 0
                           LEFT JOIN warehouses pw ON scwoi.party_warehouse_id = pw.id AND pw.company_id = '" . $get['company_id'] . "' AND pw.is_active = 0
                           LEFT JOIN items i ON scwoi.product_id = i.id AND i.company_id = '" . $get['company_id'] . "' AND i.is_active = 0
                           WHERE scwoi.id = '" . $rid . "'
                           AND scwoi.company_id = '" . $get['company_id'] . "'
                           AND scwoi.is_active = 0";
            $parent = $callConfig->getRow($parent_sql);

            if (!$parent) {
                return array('status' => 'error', 'message' => 'Issue record not found');
            }

            $child_sql = "SELECT scwoii.*, itm.sku, itm.item_name, u.uom_name, (scwoii.issued_quantity * scwoii.item_price) as total_amount
                          FROM sub_contract_work_order_issue_items scwoii
                          LEFT JOIN items itm ON scwoii.item_id = itm.id AND itm.company_id = '" . $get['company_id'] . "' AND itm.is_active = 0
                          LEFT JOIN unit_of_measurements u ON scwoii.purchase_uom = u.id AND u.company_id = '" . $get['company_id'] . "' AND u.is_active = 0
                          WHERE scwoii.transaction_id = '" . $rid . "'
                          AND scwoii.company_id = '" . $get['company_id'] . "'
                          AND scwoii.is_active = 0
                          ORDER BY scwoii.id ASC";
            $child_items = $callConfig->getAllRows($child_sql);

            $return['parent'] = $parent;
            $return['child_items'] = $child_items;
            $return['status'] = 'success';
            return $return;
        }

        $list_sql = "SELECT scwoi.id, scwoi.transaction_number, scwoi.ref_no, scwoi.ref_date, scwoi.transaction_date, scwoi.party_name, scwoi.work_order_item_qty, scwoi.work_order_total_amount, scwoi.current_status, rw.warehouse_name as request_warehouse_name, pw.warehouse_name as party_warehouse_name, swo.transaction_number as subcontract_work_order_number, itm.item_name as product_name
                     FROM sub_contract_work_order_issues scwoi
                     LEFT JOIN subcontract_work_orders swo ON scwoi.ref_id = swo.id AND swo.company_id = '" . $get['company_id'] . "' AND swo.is_active = 0
                     LEFT JOIN warehouses rw ON scwoi.request_warehouse_id = rw.id AND rw.company_id = '" . $get['company_id'] . "' AND rw.is_active = 0
                     LEFT JOIN warehouses pw ON scwoi.party_warehouse_id = pw.id AND pw.company_id = '" . $get['company_id'] . "' AND pw.is_active = 0
                     LEFT JOIN items itm ON scwoi.product_id = itm.id AND itm.company_id = '" . $get['company_id'] . "' AND itm.is_active = 0
                     WHERE scwoi.company_id = '" . $get['company_id'] . "'
                     AND scwoi.is_active = 0
                     ORDER BY scwoi.id DESC";
        $return['issues_list'] = $callConfig->getAllRows($list_sql);
        $return['status'] = 'success';
        return $return;
    }

    private function applyPendingQtyDeltas($issue_items, array $get): int
    {
        global $callConfig;

        $updated_count = 0;
        foreach ($issue_items as $issue_item) {
            $source_transaction_id = $issue_item->ref_transaction_table_id;
            $source_item_id = $issue_item->ref_transaction_item_table_id;
            $issued_qty = (float) $issue_item->issued_quantity;

            if ($source_item_id == '' || $issued_qty <= 0) {
                continue;
            }

            $source_child_sql = "SELECT id, pending_qty
                                 FROM subcontract_work_order_items
                                 WHERE id = '" . $source_item_id . "'
                                 AND transaction_id = '" . $source_transaction_id . "'
                                 AND company_id = '" . $get['company_id'] . "'
                                 AND is_active = 0";
            $source_child = $callConfig->getRow($source_child_sql);

            if (!$source_child) {
                continue;
            }

            $current_pending = (float) $source_child->pending_qty;
            $new_pending = max(0, $current_pending - $issued_qty);

            $up_data = array(
                'pending_qty' => $new_pending,
                'updated_date' => date(self::DATE_FORMAT_HIS),
                'updated_by' => $get['user_id']
            );
            $callConfig->updateRecord('subcontract_work_order_items', $up_data, 'id', $source_item_id);
            $updated_count++;
        }
        return $updated_count;
    }

    public function updateSubcontractWorkOrderPendingQty($get)
    {
        global $callConfig;
        $return = array();

        try {
            $transaction_id = $get['transaction_id'] ?? '';
            if ($transaction_id == '') {
                return array('status' => 'error', 'message' => 'Invalid transaction_id');
            }

            $issue_parent_sql = "SELECT id, ref_id
                                 FROM sub_contract_work_order_issues
                                 WHERE id = '" . $transaction_id . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $issue_parent = $callConfig->getRow($issue_parent_sql);

            if (!$issue_parent) {
                return array('status' => 'error', 'message' => 'Issue transaction not found');
            }

            $issue_items_sql = "SELECT id, ref_transaction_table_id, ref_transaction_item_table_id, issued_quantity
                                FROM sub_contract_work_order_issue_items
                                WHERE transaction_id = '" . $transaction_id . self::AND_COMPANY_ID . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $issue_items = $callConfig->getAllRows($issue_items_sql);

            if (empty($issue_items)) {
                $return['status'] = 'error';
                $return['message'] = 'No issue items found for this transaction';
            } else {
                $updated_count = $this->applyPendingQtyDeltas($issue_items, $get);
                $return['status'] = 'success';
                $return['message'] = 'Pending qty updated successfully';
                $return['transaction_id'] = $transaction_id;
                $return['updated_items'] = $updated_count;
            }

        } catch (Exception $e) {
            $return['status'] = 'error';
            $return['message'] = self::ERROR_PREFIX . $e->getMessage();
        }

        return $return;
    }
}
