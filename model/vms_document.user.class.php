<?php
class VmsDocumentClass extends userClass
{
    function getDocumentManagementData($get)
    {
        global $callConfig;

        $return = array(
            'status' => 'sucess',
            'document_types' => array(),
            'document_data' => null
        );

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $rid = isset($get['rid']) ? (int)$get['rid'] : 0;

        $typesQuery = "SELECT id, type_name FROM " . TBL_DOCUMENT_TYPES . "
            WHERE company_id = '" . $company_id . "'
            AND is_active = 0
            ORDER BY type_name ASC";
        $return['document_types'] = $callConfig->getAllRows($typesQuery);

        if ($rid > 0) {
            $query = "SELECT dm.id, dm.company_id, dm.document_type_id, dm.document_name,
                    dm.current_status,
                    dm.created_date, dm.created_by, dm.updated_date, dm.updated_by,
                    dt.type_name
                FROM " . TBL_DOCUMENT_MANAGEMENT . " dm
                LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dm.document_type_id = dt.id
                WHERE dm.id = '" . $rid . "'
                AND dm.company_id = '" . $company_id . "'
                AND dm.is_active = 0
                LIMIT 1";
            $row = $callConfig->getRow($query);
            if (empty($row)) {
                $return['status'] = 'error';
                $return['message'] = 'Document record not found';
                return $return;
            }

            $row = is_array($row) ? $row : (array)$row;

            $filesQuery = "SELECT id, file_name, file, actual_file_name
                FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . "
                WHERE document_management_id = '" . $rid . "'
                AND company_id = '" . $company_id . "'
                AND is_active = 0
                ORDER BY id ASC";
            $files = $callConfig->getAllRows($filesQuery);

            $return['document_data'] = array(
                'id' => $row['id'],
                'document_type_id' => $row['document_type_id'],
                'document_name' => $row['document_name'],
                'current_status' => $row['current_status'] ?? 0,
                'type_name' => $row['type_name'],
                'files' => $files,
                'created_date' => $row['created_date'],
                'created_by' => $row['created_by'],
                'updated_date' => $row['updated_date'],
                'updated_by' => $row['updated_by']
            );
        }

        return $return;
    }

    function getDocumentManagementList($get)
    {
        global $callConfig;

        $return = array(
            'status' => 'sucess',
            'document_types' => array(),
            'rows' => array(),
            'total' => 0,
            'page' => 1,
            'limit' => 20,
            'total_pages' => 0
        );

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $document_type_id = isset($get['document_type_id']) ? (int)$get['document_type_id'] : 0;
        $document_name = isset($get['document_name']) ? trim($get['document_name']) : '';
        $page = isset($get['page']) ? (int)$get['page'] : 1;
        $limit = isset($get['limit']) ? (int)$get['limit'] : 20;

        if ($company_id <= 0) {
            $return['status'] = 'error';
            $return['message'] = 'Invalid company';
            return $return;
        }

        if ($page <= 0) {
            $page = 1;
        }
        if ($limit <= 0) {
            $limit = 20;
        }

        $typesQuery = "SELECT id, type_name FROM " . TBL_DOCUMENT_TYPES . "
            WHERE company_id = '" . $company_id . "'
            AND is_active = 0
            ORDER BY type_name ASC";
        $return['document_types'] = $callConfig->getAllRows($typesQuery);
        $return['page'] = $page;
        $return['limit'] = $limit;

        $where = "dm.company_id = '" . $company_id . "' AND dm.is_active = 0";
        if ($document_type_id > 0) {
            $where .= " AND dm.document_type_id = '" . $document_type_id . "'";
        }
        if ($document_name != '') {
            $where .= " AND dm.document_name LIKE '%" . addslashes($document_name) . "%'";
        }

        $countQuery = "SELECT COUNT(*) AS total_count
            FROM " . TBL_DOCUMENT_MANAGEMENT . " dm
            WHERE " . $where;
        $countRow = $callConfig->getRow($countQuery);
        $total = 0;
        if (!empty($countRow)) {
            $countRow = is_array($countRow) ? $countRow : (array)$countRow;
            $total = (int)($countRow['total_count'] ?? 0);
        }

        $return['total'] = $total;
        $return['total_pages'] = $total > 0 ? (int)ceil($total / $limit) : 0;

        $offset = ($page - 1) * $limit;
        $listQuery = "SELECT dm.id, dm.document_type_id, dm.document_name,
                dm.created_date, dm.created_by, dt.type_name,
                (SELECT COUNT(*) FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . " dmf
                    WHERE dmf.document_management_id = dm.id
                    AND dmf.company_id = dm.company_id
                    AND dmf.is_active = 0) AS file_count
            FROM " . TBL_DOCUMENT_MANAGEMENT . " dm
            LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dm.document_type_id = dt.id
            WHERE " . $where . "
            ORDER BY dm.id DESC
            LIMIT " . $offset . ", " . $limit;
        $rows = $callConfig->getAllRows($listQuery);

        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $return['rows'][] = array(
                    'id' => $row['id'],
                    'document_type_id' => $row['document_type_id'],
                    'type_name' => $row['type_name'],
                    'document_name' => $row['document_name'],
                    'file_count' => (int)($row['file_count'] ?? 0),
                    'created_date' => $row['created_date'],
                    'created_by' => $row['created_by']
                );
            }
        }

        return $return;
    }

    function saveDocumentManagement($get, $post)
    {
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : (int)($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $rid = isset($post['rid']) ? (int)$post['rid'] : 0;
        $document_type_id = isset($post['document_type_id']) ? (int)$post['document_type_id'] : 0;
        $document_name = isset($post['document_name']) ? trim($post['document_name']) : '';
        $fileIds = isset($post['file_id']) ? $post['file_id'] : array();
        $fileNames = isset($post['file_name']) ? $post['file_name'] : array();
        $files = isset($post['file']) ? $post['file'] : array();
        $actualFileNames = isset($post['actual_file_name']) ? $post['actual_file_name'] : array();

        if ($document_type_id <= 0) {
            return array('status' => 'error', 'message' => 'Document type is required');
        }
        if ($document_name == '') {
            return array('status' => 'error', 'message' => 'Document name is required');
        }

        $validFiles = array();
        if (count((array)$fileNames) > 0) {
            foreach ($fileNames as $index => $fileLabel) {
                $fileLabel = trim((string)$fileLabel);
                $storedFile = trim((string)($files[$index] ?? ''));
                if ($fileLabel == '' || $storedFile == '') {
                    continue;
                }
                $validFiles[] = array(
                    'file_id' => (int)($fileIds[$index] ?? 0),
                    'file_name' => $fileLabel,
                    'file' => $storedFile,
                    'actual_file_name' => trim((string)($actualFileNames[$index] ?? $storedFile))
                );
            }
        }

        if (count((array)$validFiles) == 0) {
            return array('status' => 'error', 'message' => 'At least one valid file is required');
        }

        $typeCheckQuery = "SELECT id FROM " . TBL_DOCUMENT_TYPES . "
            WHERE id = '" . $document_type_id . "'
            AND company_id = '" . $company_id . "'
            AND is_active = 0
            LIMIT 1";
        $typeRow = $callConfig->getRow($typeCheckQuery);
        if (empty($typeRow)) {
            return array('status' => 'error', 'message' => 'Invalid document type');
        }

        $now = date('Y-m-d H:i:s');
        $parentId = 0;

        if ($rid > 0) {
            $checkQuery = "SELECT id FROM " . TBL_DOCUMENT_MANAGEMENT . "
                WHERE id = '" . $rid . "'
                AND company_id = '" . $company_id . "'
                AND is_active = 0
                LIMIT 1";
            $existingRow = $callConfig->getRow($checkQuery);
            if (empty($existingRow)) {
                return array('status' => 'error', 'message' => 'Document record not found');
            }

            $updateData = array(
                'document_type_id' => $document_type_id,
                'document_name' => $document_name,
                'updated_by' => $user_id != '' ? $user_id : null,
                'updated_date' => $now
            );
            $updateRes = $callConfig->updateRecord(TBL_DOCUMENT_MANAGEMENT, $updateData, 'id', $rid);
            if (!$updateRes) {
                return array('status' => 'error', 'message' => 'Unable to update document');
            }
            $parentId = $rid;

            $inactiveQuery = "UPDATE " . TBL_DOCUMENT_MANAGEMENT_FILES . "
                SET is_active  = NULL, updated_by = '" . $user_id . "', updated_date = '" . $now . "'
                WHERE document_management_id = '" . $parentId . "'
                AND company_id = '" . $company_id . "' AND is_active = 0";
            $callConfig->executeQuery($inactiveQuery);
        } else {
            $insertData = array(
                'company_id' => $company_id,
                'is_active' => '0',
                'current_status' => '0',
                'status_date' => $now,
                'created_date' => $now,
                'created_by' => $user_id != '' ? $user_id : null,
                'document_type_id' => $document_type_id,
                'document_name' => $document_name
            );
            $insertRes = $callConfig->insertRecord(TBL_DOCUMENT_MANAGEMENT, $insertData);
            if (!is_array($insertRes) || ($insertRes['status'] ?? '') != 'sucess') {
                return array('status' => 'error', 'message' => 'Unable to save document');
            }
            $parentId = (int)($insertRes['LASTID'] ?? 0);
        }

        if ($parentId > 0 && count((array)$validFiles) > 0) {
            foreach ($validFiles as $fileRow) {
                $fileId = (int)($fileRow['file_id'] ?? 0);
                if ($fileId > 0) {
                    $childUpdate = array(
                        'file_name' => $fileRow['file_name'],
                        'file' => $fileRow['file'],
                        'actual_file_name' => $fileRow['actual_file_name'],
                        'is_active' => '0',
                        'updated_by' => $user_id != '' ? $user_id : null,
                        'updated_date' => $now
                    );
                    $callConfig->updateRecord(TBL_DOCUMENT_MANAGEMENT_FILES, $childUpdate, 'id', $fileId);
                } else {
                    $childInsert = array(
                        'company_id' => $company_id,
                        'is_active' => '0',
                        'current_status' => '0',
                        'status_date' => $now,
                        'created_date' => $now,
                        'created_by' => $user_id != '' ? $user_id : null,
                        'document_management_id' => $parentId,
                        'file_name' => $fileRow['file_name'],
                        'file' => $fileRow['file'],
                        'actual_file_name' => $fileRow['actual_file_name']
                    );
                    $callConfig->insertRecord(TBL_DOCUMENT_MANAGEMENT_FILES, $childInsert);
                }
            }
        }

        $message = ($rid > 0) ? 'Update successfully' : 'Insert successfully';
        return array(
            'status' => 'sucess',
            'message' => $message,
            'rid' => $parentId,
            'LASTID' => $parentId
        );
    }

    function getDocumentManagementDashboard($get)
    {
        global $callConfig;

        $company_id    = isset($get['company_id'])    ? (int)$get['company_id']    : 0;
        $status_filter = isset($get['status_filter']) ? trim($get['status_filter']) : '';

        $countsQuery = "SELECT
            COUNT(*) AS total_all,
            SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS total_active,
            SUM(CASE WHEN current_status > 100 AND current_status < 199 THEN 1 ELSE 0 END) AS total_inprogress,
            SUM(CASE WHEN current_status = 199 THEN 1 ELSE 0 END) AS total_approved
            FROM " . TBL_DOCUMENT_MANAGEMENT . "
            WHERE company_id = '" . $company_id . "' AND is_active = 0";
        $countRow = $callConfig->getRow($countsQuery);
        $countRow = is_array($countRow) ? $countRow : (array)$countRow;

        $return = array(
            'status'           => 'sucess',
            'total_all'        => (int)($countRow['total_all']        ?? 0),
            'total_active'     => (int)($countRow['total_active']     ?? 0),
            'total_inprogress' => (int)($countRow['total_inprogress'] ?? 0),
            'total_approved'   => (int)($countRow['total_approved']   ?? 0),
            'rows'             => array(),
        );

        $where = "dm.company_id = '" . $company_id . "' AND dm.is_active = 0";
        if ($status_filter == 'inprogress') {
            $where .= " AND dm.current_status > 100 AND dm.current_status < 199";
        } elseif ($status_filter != '') {
            $where .= " AND dm.current_status = '" . (int)$status_filter . "'";
        }

        $listQuery = "SELECT dm.id, dm.document_type_id, dm.document_name, dm.current_status,
                dm.created_date, dm.created_by, dt.type_name,
                (SELECT COUNT(*) FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . " dmf
                    WHERE dmf.document_management_id = dm.id
                    AND dmf.company_id = dm.company_id
                    AND dmf.is_active = 0) AS file_count
            FROM " . TBL_DOCUMENT_MANAGEMENT . " dm
            LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dm.document_type_id = dt.id
            WHERE " . $where . "
            ORDER BY dm.id DESC";
        $rows = $callConfig->getAllRows($listQuery);

        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $return['rows'][] = array(
                    'id'               => $row['id'],
                    'document_type_id' => $row['document_type_id'],
                    'type_name'        => $row['type_name'],
                    'document_name'    => $row['document_name'],
                    'current_status'   => $row['current_status'],
                    'file_count'       => (int)($row['file_count'] ?? 0),
                    'created_date'     => $row['created_date'],
                    'created_by'       => $row['created_by'],
                );
            }
        }

        return $return;
    }

    function getDocumentManagementUserDashboard($get)
    {
        global $callConfig;

        $company_id    = isset($get['company_id'])    ? (int)$get['company_id']    : 0;
        $user_id       = isset($get['user_id'])       ? trim($get['user_id'])       : '';
        $status_filter = isset($get['status_filter']) ? trim($get['status_filter']) : '';

        $countsQuery = "SELECT
            COUNT(*) AS total_all,
            SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS total_active,
            SUM(CASE WHEN current_status > 100 AND current_status < 199 THEN 1 ELSE 0 END) AS total_inprogress,
            SUM(CASE WHEN current_status = 199 THEN 1 ELSE 0 END) AS total_approved
            FROM " . TBL_DOCUMENT_MANAGEMENT . "
            WHERE company_id = '" . $company_id . "' AND is_active = 0 AND created_by = '" . $user_id . "'";
        $countRow = $callConfig->getRow($countsQuery);
        $countRow = is_array($countRow) ? $countRow : (array)$countRow;

        $return = array(
            'status'           => 'sucess',
            'total_all'        => (int)($countRow['total_all']        ?? 0),
            'total_active'     => (int)($countRow['total_active']     ?? 0),
            'total_inprogress' => (int)($countRow['total_inprogress'] ?? 0),
            'total_approved'   => (int)($countRow['total_approved']   ?? 0),
            'rows'             => array(),
        );

        $where = "dm.company_id = '" . $company_id . "' AND dm.is_active = 0 AND dm.created_by = '" . $user_id . "'";
        if ($status_filter == 'inprogress') {
            $where .= " AND dm.current_status > 100 AND dm.current_status < 199";
        } elseif ($status_filter != '') {
            $where .= " AND dm.current_status = '" . (int)$status_filter . "'";
        }

        $listQuery = "SELECT dm.id, dm.document_type_id, dm.document_name, dm.current_status,
                dm.created_date, dm.created_by, dt.type_name,
                (SELECT COUNT(*) FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . " dmf
                    WHERE dmf.document_management_id = dm.id
                    AND dmf.company_id = dm.company_id
                    AND dmf.is_active = 0) AS file_count
            FROM " . TBL_DOCUMENT_MANAGEMENT . " dm
            LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dm.document_type_id = dt.id
            WHERE " . $where . "
            ORDER BY dm.id DESC";
        $rows = $callConfig->getAllRows($listQuery);

        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $return['rows'][] = array(
                    'id'               => $row['id'],
                    'document_type_id' => $row['document_type_id'],
                    'type_name'        => $row['type_name'],
                    'document_name'    => $row['document_name'],
                    'current_status'   => $row['current_status'],
                    'file_count'       => (int)($row['file_count'] ?? 0),
                    'created_date'     => $row['created_date'],
                    'created_by'       => $row['created_by'],
                );
            }
        }

        $approvalQuery = "SELECT dm.id, dm.document_name, dm.current_status, dm.created_date, dt.type_name
            FROM " . TBL_TRANSACTION_APPROVAL_LOGS . " tal
            LEFT JOIN " . TBL_DOCUMENT_MANAGEMENT . " dm ON dm.id = tal.transaction_id
            LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dt.id = dm.document_type_id
            WHERE tal.user_id = '" . $user_id . "'
            AND tal.transaction_table = 'document_management'
            AND tal.company_id = '" . $company_id . "'
            AND dm.is_active = '0'
            ORDER BY dm.id DESC";
        $approvalRows = $callConfig->getAllRows($approvalQuery);
        $return['approval_rows'] = array();
        if (count((array)$approvalRows) > 0) {
            foreach ($approvalRows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $return['approval_rows'][] = array(
                    'id'             => $row['id'],
                    'document_name'  => $row['document_name'],
                    'current_status' => $row['current_status'],
                    'created_date'   => $row['created_date'],
                    'type_name'      => $row['type_name'],
                );
            }
        }

        return $return;
    }
}
