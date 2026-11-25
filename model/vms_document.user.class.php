<?php
class VmsDocumentClass extends userClass
{
    private const WHERE_COMPANY_ID = "\n            WHERE company_id = '";
    private const AND_COMPANY_ID = "'\n                AND company_id = '";
    private const DM_COMPANY_ID = "dm.company_id = '";
    private const SUBQUERY_FILE_COUNT_FROM = " dmf\n                    WHERE dmf.document_management_id = dm.id\n                    AND dmf.company_id = dm.company_id\n                    AND dmf.is_active = 0) AS file_count\n            FROM ";
    private const DM_LEFT_JOIN = " dm\n            LEFT JOIN ";
    private const DT_ON_WHERE = " dt ON dm.document_type_id = dt.id\n            WHERE ";

    public function getDocumentManagementData($get)
    {
        global $callConfig;

        $return = array(
            'status' => 'sucess',
            'document_types' => array(),
            'document_data' => null
        );

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $rid = isset($get['rid']) ? (int)$get['rid'] : 0;

        $typesQuery = "SELECT id, type_name FROM " . TBL_DOCUMENT_TYPES . self::WHERE_COMPANY_ID . $company_id . "'
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
                WHERE document_management_id = '" . $rid . self::AND_COMPANY_ID . $company_id . "'
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

    private function buildDocManagementListWhere(int $companyId, int $documentTypeId, string $documentName): string
    {
        $where = self::DM_COMPANY_ID . $companyId . "' AND dm.is_active = 0";
        if ($documentTypeId > 0) {
            $where .= " AND dm.document_type_id = '" . $documentTypeId . "'";
        }
        if ($documentName != '') {
            $where .= " AND dm.document_name LIKE '%" . addslashes($documentName) . "%'";
        }
        return $where;
    }

    private function formatDocManagementRows($rows): array
    {
        $formatted = array();
        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $formatted[] = array(
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
        return $formatted;
    }

    public function getDocumentManagementList($get)
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
        $page = isset($get['page']) ? max((int)$get['page'], 1) : 1;
        $limit = isset($get['limit']) ? max((int)$get['limit'], 20) : 20;

        if ($company_id <= 0) {
            $return['status'] = 'error';
            $return['message'] = 'Invalid company';
            return $return;
        }

        $typesQuery = "SELECT id, type_name FROM " . TBL_DOCUMENT_TYPES . self::WHERE_COMPANY_ID . $company_id . "'
            AND is_active = 0
            ORDER BY type_name ASC";
        $return['document_types'] = $callConfig->getAllRows($typesQuery);
        $return['page'] = $page;
        $return['limit'] = $limit;

        $where = $this->buildDocManagementListWhere($company_id, $document_type_id, $document_name);
        $countQuery = "SELECT COUNT(*) AS total_count FROM " . TBL_DOCUMENT_MANAGEMENT . " dm WHERE " . $where;
        $countRow = $callConfig->getRow($countQuery);
        $total = !empty($countRow) ? (int)(((array)$countRow)['total_count'] ?? 0) : 0;

        $return['total'] = $total;
        $return['total_pages'] = $total > 0 ? (int)ceil($total / $limit) : 0;

        $offset = ($page - 1) * $limit;
        $listQuery = "SELECT dm.id, dm.document_type_id, dm.document_name,
                dm.created_date, dm.created_by, dt.type_name,
                (SELECT COUNT(*) FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . self::SUBQUERY_FILE_COUNT_FROM . TBL_DOCUMENT_MANAGEMENT . self::DM_LEFT_JOIN . TBL_DOCUMENT_TYPES . self::DT_ON_WHERE . $where . "
            ORDER BY dm.id DESC
            LIMIT " . $offset . ", " . $limit;
        $rows = $callConfig->getAllRows($listQuery);
        $return['rows'] = $this->formatDocManagementRows($rows);

        return $return;
    }

    private function parseValidFiles(array $fileNames, array $files, array $fileIds, array $actualFileNames): array
    {
        $validFiles = array();
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
        return $validFiles;
    }

    private function validateSaveInputs(int $companyId, int $documentTypeId, string $documentName, array $validFiles): ?string
    {
        global $callConfig;

        if ($documentTypeId <= 0) {
            return 'Document type is required';
        }
        if ($documentName == '') {
            return 'Document name is required';
        }
        if (empty($validFiles)) {
            return 'At least one valid file is required';
        }

        $typeCheckQuery = "SELECT id FROM " . TBL_DOCUMENT_TYPES . "
            WHERE id = '" . $documentTypeId . self::AND_COMPANY_ID . $companyId . "'
            AND is_active = 0
            LIMIT 1";
        $typeRow = $callConfig->getRow($typeCheckQuery);
        return empty($typeRow) ? 'Invalid document type' : null;
    }

    private function updateParentDoc(int $rid, int $companyId, string $userId, int $documentTypeId, string $documentName, string $now): array
    {
        global $callConfig;
        $checkQuery = "SELECT id FROM " . TBL_DOCUMENT_MANAGEMENT . "
            WHERE id = '" . $rid . self::AND_COMPANY_ID . $companyId . "'
            AND is_active = 0
            LIMIT 1";
        $existingRow = $callConfig->getRow($checkQuery);
        if (empty($existingRow)) {
            return array('status' => 'error', 'message' => 'Document record not found');
        }

        $updateData = array(
            'document_type_id' => $documentTypeId,
            'document_name' => $documentName,
            'updated_by' => $userId != '' ? $userId : null,
            'updated_date' => $now
        );
        $updateRes = $callConfig->updateRecord(TBL_DOCUMENT_MANAGEMENT, $updateData, 'id', $rid);
        if (!$updateRes) {
            return array('status' => 'error', 'message' => 'Unable to update document');
        }

        $inactiveQuery = "UPDATE " . TBL_DOCUMENT_MANAGEMENT_FILES . "
            SET is_active = NULL, updated_by = '" . $userId . "', updated_date = '" . $now . "'
            WHERE document_management_id = '" . $rid . "'
            AND company_id = '" . $companyId . "' AND is_active = 0";
        $callConfig->executeQuery($inactiveQuery);

        return array('status' => 'sucess', 'rid' => $rid);
    }

    private function persistParentDoc(int $rid, int $companyId, string $userId, int $documentTypeId, string $documentName, string $now): array
    {
        global $callConfig;

        if ($rid > 0) {
            return $this->updateParentDoc($rid, $companyId, $userId, $documentTypeId, $documentName, $now);
        }

        $insertData = array(
            'company_id' => $companyId,
            'is_active' => '0',
            'current_status' => '0',
            'status_date' => $now,
            'created_date' => $now,
            'created_by' => $userId != '' ? $userId : null,
            'document_type_id' => $documentTypeId,
            'document_name' => $documentName
        );
        $insertRes = $callConfig->insertRecord(TBL_DOCUMENT_MANAGEMENT, $insertData);
        if (!is_array($insertRes) || ($insertRes['status'] ?? '') != 'sucess') {
            return array('status' => 'error', 'message' => 'Unable to save document');
        }

        return array('status' => 'sucess', 'rid' => (int)($insertRes['LASTID'] ?? 0));
    }

    private function persistDocFiles(int $parentId, array $validFiles, int $companyId, string $userId, string $now): void
    {
        global $callConfig;

        foreach ($validFiles as $fileRow) {
            $fileId = (int)($fileRow['file_id'] ?? 0);
            if ($fileId > 0) {
                $childUpdate = array(
                    'file_name' => $fileRow['file_name'],
                    'file' => $fileRow['file'],
                    'actual_file_name' => $fileRow['actual_file_name'],
                    'is_active' => '0',
                    'updated_by' => $userId != '' ? $userId : null,
                    'updated_date' => $now
                );
                $callConfig->updateRecord(TBL_DOCUMENT_MANAGEMENT_FILES, $childUpdate, 'id', $fileId);
            } else {
                $childInsert = array(
                    'company_id' => $companyId,
                    'is_active' => '0',
                    'current_status' => '0',
                    'status_date' => $now,
                    'created_date' => $now,
                    'created_by' => $userId != '' ? $userId : null,
                    'document_management_id' => $parentId,
                    'file_name' => $fileRow['file_name'],
                    'file' => $fileRow['file'],
                    'actual_file_name' => $fileRow['actual_file_name']
                );
                $callConfig->insertRecord(TBL_DOCUMENT_MANAGEMENT_FILES, $childInsert);
            }
        }
    }

    public function saveDocumentManagement($get, $post)
    {
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : (int)($post['company_id'] ?? 0);
        $user_id = isset($get['user_id']) ? $get['user_id'] : ($post['user_id'] ?? '');
        $rid = isset($post['rid']) ? (int)$post['rid'] : 0;
        $document_type_id = isset($post['document_type_id']) ? (int)$post['document_type_id'] : 0;
        $document_name = isset($post['document_name']) ? trim($post['document_name']) : '';

        $validFiles = $this->parseValidFiles(
            isset($post['file_name']) ? (array)$post['file_name'] : array(),
            isset($post['file']) ? (array)$post['file'] : array(),
            isset($post['file_id']) ? (array)$post['file_id'] : array(),
            isset($post['actual_file_name']) ? (array)$post['actual_file_name'] : array()
        );

        $validationError = $this->validateSaveInputs($company_id, $document_type_id, $document_name, $validFiles);
        if ($validationError !== null) {
            return array('status' => 'error', 'message' => $validationError);
        }

        $now = date('Y-m-d H:i:s');
        $parentResult = $this->persistParentDoc($rid, $company_id, $user_id, $document_type_id, $document_name, $now);
        if (($parentResult['status'] ?? '') !== 'sucess') {
            return $parentResult;
        }

        $parentId = (int)$parentResult['rid'];
        $this->persistDocFiles($parentId, $validFiles, $company_id, $user_id, $now);

        $message = ($rid > 0) ? 'Update successfully' : 'Insert successfully';
        return array(
            'status' => 'sucess',
            'message' => $message,
            'rid' => $parentId,
            'LASTID' => $parentId
        );
    }

    private function getDashboardCounts(int $companyId, string $extraWhere = ''): array
    {
        global $callConfig;

        $countsQuery = "SELECT
            COUNT(*) AS total_all,
            SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS total_active,
            SUM(CASE WHEN current_status > 100 AND current_status < 199 THEN 1 ELSE 0 END) AS total_inprogress,
            SUM(CASE WHEN current_status = 199 THEN 1 ELSE 0 END) AS total_approved
            FROM " . TBL_DOCUMENT_MANAGEMENT . self::WHERE_COMPANY_ID . $companyId . "' AND is_active = 0" . $extraWhere;
        $countRow = $callConfig->getRow($countsQuery);
        $countRow = is_array($countRow) ? $countRow : (array)$countRow;

        return array(
            'total_all'        => (int)($countRow['total_all']        ?? 0),
            'total_active'     => (int)($countRow['total_active']     ?? 0),
            'total_inprogress' => (int)($countRow['total_inprogress'] ?? 0),
            'total_approved'   => (int)($countRow['total_approved']   ?? 0),
        );
    }

    private function fetchDashboardRows(string $where): array
    {
        global $callConfig;

        $listQuery = "SELECT dm.id, dm.document_type_id, dm.document_name, dm.current_status,
                dm.created_date, dm.created_by, dt.type_name,
                (SELECT COUNT(*) FROM " . TBL_DOCUMENT_MANAGEMENT_FILES . self::SUBQUERY_FILE_COUNT_FROM . TBL_DOCUMENT_MANAGEMENT . self::DM_LEFT_JOIN . TBL_DOCUMENT_TYPES . self::DT_ON_WHERE . $where . "
            ORDER BY dm.id DESC";
        $rows = $callConfig->getAllRows($listQuery);

        $result = array();
        if (count((array)$rows) > 0) {
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $result[] = array(
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
        return $result;
    }

    public function getDocumentManagementDashboard($get)
    {
        $company_id    = isset($get['company_id'])    ? (int)$get['company_id']    : 0;
        $status_filter = isset($get['status_filter']) ? trim($get['status_filter']) : '';

        $counts = $this->getDashboardCounts($company_id);

        $return = array(
            'status'           => 'sucess',
            'total_all'        => $counts['total_all'],
            'total_active'     => $counts['total_active'],
            'total_inprogress' => $counts['total_inprogress'],
            'total_approved'   => $counts['total_approved'],
            'rows'             => array(),
        );

        $where = self::DM_COMPANY_ID . $company_id . "' AND dm.is_active = 0";
        if ($status_filter == 'inprogress') {
            $where .= " AND dm.current_status > 100 AND dm.current_status < 199";
        } elseif ($status_filter != '') {
            $where .= " AND dm.current_status = '" . (int)$status_filter . "'";
        }

        $return['rows'] = $this->fetchDashboardRows($where);

        return $return;
    }

    private function fetchApprovalRows(int $companyId, string $userId): array
    {
        global $callConfig;

        $approvalQuery = "SELECT dm.id, dm.document_name, dm.current_status, dm.created_date, dt.type_name
            FROM " . TBL_TRANSACTION_APPROVAL_LOGS . " tal
            LEFT JOIN " . TBL_DOCUMENT_MANAGEMENT . " dm ON dm.id = tal.transaction_id
            LEFT JOIN " . TBL_DOCUMENT_TYPES . " dt ON dt.id = dm.document_type_id
            WHERE tal.user_id = '" . $userId . "'
            AND tal.transaction_table = 'document_management'
            AND tal.company_id = '" . $companyId . "'
            AND dm.is_active = '0'
            ORDER BY dm.id DESC";
        $approvalRows = $callConfig->getAllRows($approvalQuery);
        $result = array();
        if (count((array)$approvalRows) > 0) {
            foreach ($approvalRows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $result[] = array(
                    'id'             => $row['id'],
                    'document_name'  => $row['document_name'],
                    'current_status' => $row['current_status'],
                    'created_date'   => $row['created_date'],
                    'type_name'      => $row['type_name'],
                );
            }
        }
        return $result;
    }

    public function getDocumentManagementUserDashboard($get)
    {
        $company_id    = isset($get['company_id'])    ? (int)$get['company_id']    : 0;
        $user_id       = isset($get['user_id'])       ? trim($get['user_id'])       : '';
        $status_filter = isset($get['status_filter']) ? trim($get['status_filter']) : '';

        $counts = $this->getDashboardCounts($company_id, " AND created_by = '" . $user_id . "'");

        $return = array(
            'status'           => 'sucess',
            'total_all'        => $counts['total_all'],
            'total_active'     => $counts['total_active'],
            'total_inprogress' => $counts['total_inprogress'],
            'total_approved'   => $counts['total_approved'],
            'rows'             => array(),
            'approval_rows'    => array(),
        );

        $where = self::DM_COMPANY_ID . $company_id . "' AND dm.is_active = 0 AND dm.created_by = '" . $user_id . "'";
        if ($status_filter == 'inprogress') {
            $where .= " AND dm.current_status > 100 AND dm.current_status < 199";
        } elseif ($status_filter != '') {
            $where .= " AND dm.current_status = '" . (int)$status_filter . "'";
        }

        $return['rows'] = $this->fetchDashboardRows($where);
        $return['approval_rows'] = $this->fetchApprovalRows($company_id, $user_id);

        return $return;
    }
}
