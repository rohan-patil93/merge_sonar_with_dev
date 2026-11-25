<?php

class DbConstants
{
    public const SELECT_STAR_FROM = 'SELECT * FROM ';
    public const ELLIPSIS = ' ... ';
    public const AND_START = '&start=';
    public const CLASS_ATTR = "\" class='";
    public const DATE_FORMAT_YMD_HIS = 'Y-m-d H:i:s';
}

trait ConfigQueryTrait
{
    public function selectQueryTwo($tablename1, $tablename2, $fields1, $fields2, $where = "", $order = "", ...$pagination)
    {
        $start = $pagination[0] ?? "";
        $limit = $pagination[1] ?? "";

        if (count($fields1) > 1) {
            $fields1 = implode(",", $fields1);
        } elseif (count($fields2) > 1) {
            $fields2 = implode(",", $fields2);
        } else {
            $query = DBSELECT . $fields1 . ',' . $fields2 . DBFROM . $tablename1 . ',' . $tablename2;
        }
        if ($where != "") {
            $query .= DBWHR . $where;
        }
        if ($order != "") {
            $query .= DB_ORDER . $order;
        }
        if ($limit != "") {
            if ($start == "") {
                $start = 0;
            }
            $query .= DB_LMT . $start . "," . $limit;
        }
        return $query;
    }

    public function configConnection($db = "", $extra = "")
    {
        unset($extra);
        return mysqli_connect(HOSTNAME, USERNAME, PASSWORD, $db, PORT);
    }

    public function insertErorrLog($data)
    {
        $val = [];
        foreach ($data as $k => $values) {
            if ($values != '') {
                $val[] = $k . "='" . $values . "',";
            }
        }
        $str = rtrim(implode('', $val), ',');
        $query = DBIN . 'application_logs' . DBSET . $str;
        return mysqli_query($this->con, $query);
    }

    public function insertActionLog($data)
    {
        $val = [];
        foreach ($data as $k => $values) {
            if ($values != '') {
                if ($values == "'0'") {
                    $val[] = $k . "=" . $values . ",";
                } else {
                    $val[] = $k . "='" . $values . "',";
                }
            }
        }
        $str = rtrim(implode('', $val), ',');
        $query = DBIN . 'action_log' . DBSET . $str;
        return mysqli_query($this->con, $query);
    }

    public function executeQuery($query)
    {
        $log = array();
        $log['user_id'] = $this->userId;
        $log['company_id'] = $this->companyId;
        $log['ip_address'] = $this->ipAddress;
        $log['log_query'] = urlencode($query);
        $log['log_source'] = $this->option;
        $log['log_message'] = '';
        $sub = substr($query, 0, 6);
        if ($sub == 'INSERT' || $sub == 'insert') {
            $log['log_message'] = '3001';
        }
        if ($sub == 'UPDATE' || $sub == 'update') {
            $log['log_message'] = '3010';
        }
        if ($sub == 'DELETE' || $sub == 'delete') {
            $this->insertAuditTrail('DELETE', '', '', '', $query);
        }
        $result = null;
        if (mysqli_query($this->con, $query)) {
            $insert_id = mysqli_insert_id($this->con);
            $update_id = mysqli_affected_rows($this->con);
            $queryresult = array('status' => 'sucess', 'LASTID' => $insert_id, 'Result' => $result, 'UpdatedRes' => $update_id);
        } else {
            $error_no = mysqli_errno($this->con);
            $error_message = $error_no . " : " . mysqli_error($this->con);
            $log['log_message'] = str_replace("'", " ", $error_message);
            $fields = null;
            if ($error_no == '1062' || $error_no == '1406') {
                $fields = $this->toGetUbiquekeyFields($error_message);
            }
            if ($error_no == '1048') {
                $fields = $this->toGetcannotbenull($error_message);
            }
            $queryresult = array('status' => 'fail', 'message' => $error_message, 'error_no' => $error_no, 'other' => $fields, 'query' => $query);
        }
        $this->insertErorrLog($log);
        return $queryresult;
    }

    private function resolveAuditTrailTableAndWhere($query): array
    {
        preg_match("/FROM\s+([\w`]+)/i", $query, $matches1);
        preg_match("/FROM\s+([\w]+)/i", $query, $matches2);
        $table = !empty($matches1[1]) ? $matches1[1] : ($matches2[1] ?? '');

        $wherecondition = explode("WHERE", $query);
        $wherecondition1 = explode("where", $query);
        $where = !empty($wherecondition[1]) ? $wherecondition[1] : ($wherecondition1[1] ?? '');

        return [$table, $where];
    }

    public function insertAuditTrail($type, $table, $id, $where, $query, $lastid = "")
    {
        $remarks = "";
        if ($type == "INSERT" || $type == "UPDATE") {
            if ($type == "INSERT") {
                $query1 = DbConstants::SELECT_STAR_FROM . $table . " WHERE id ='" . $id . "'";
            } else {
                $wherePart = str_contains($where, 'WHERE') ? $where : ("WHERE " . $where);
                $query1 = DbConstants::SELECT_STAR_FROM . $table . " " . $wherePart;
            }
            $exicutesql = mysqli_query($this->con, $query1);
            $valuessql = mysqli_fetch_object($exicutesql);
            if ($id == '' && !empty($valuessql->id)) {
                $id = $valuessql->id;
            }
            $remarks = urlencode(json_encode($valuessql));
        } elseif ($type == "SELECT") {
            list($table, $where) = $this->resolveAuditTrailTableAndWhere($query);
        }

        $now = date(DbConstants::DATE_FORMAT_YMD_HIS);
        if ($lastid != "" && $type == "UPDATE") {
            $auditQuery = "UPDATE `audit_trails` SET after_transaction_details='" . $remarks . "' WHERE id = '" . $lastid . "'";
        } elseif ($type == "UPDATE") {
            $auditQuery = "INSERT INTO `audit_trails`(`company_id`,`module_setup_id`, `user_id`, `type`, `page_option`,`extra_paramiter`, `transaction_table`, `transaction_id`, `log_query`, `where_condition`, `before_transaction_details`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ('" . $this->companyId . "','" . $this->moduleId . "','" . $this->userId . "','" . $type . "','" . $this->option . "','" . $this->extraParamiter . "','" . $table . "','" . $id . "','" . urlencode($query) . "','" . urlencode($where) . "','" . $remarks . "','0','0','" . $now . "','" . $this->userId . "')";
        } else {
            $auditQuery = "INSERT INTO `audit_trails`(`company_id`, `module_setup_id`,`user_id`, `type`, `page_option`,`extra_paramiter`, `transaction_table`, `transaction_id`, `log_query`, `where_condition`, `after_transaction_details`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ('" . $this->companyId . "','" . $this->moduleId . "','" . $this->userId . "','" . $type . "','" . $this->option . "','" . $this->extraParamiter . "','" . $table . "','" . $id . "','" . urlencode($query) . "','" . urlencode($where) . "','" . $remarks . "','0','0','" . $now . "','" . $this->userId . "')";
        }
        return $this->executeQuery($auditQuery);
    }

    public function toGetcannotbenull($error_message)
    {
        $subtring_start = strpos($error_message, "'");
        $subtring_start += strlen("'");
        $size = strpos($error_message, "'", $subtring_start) - $subtring_start;
        return substr($error_message, $subtring_start, $size);
    }

    public function toGetUbiquekeyFields($error_message)
    {
        $last_word_start = strrpos($error_message, ' ') + 1;
        $last_word = substr($error_message, $last_word_start);
        $uniq = trim($last_word, "'");
        $query1 = "SELECT  tc.table_name,kcu.column_name FROM information_schema.table_constraints tc inner JOIN information_schema.key_column_usage kcu  ON tc.constraint_catalog = kcu.constraint_catalog AND tc.constraint_schema = kcu.constraint_schema AND tc.constraint_name = kcu.constraint_name AND tc.table_name = kcu.table_name LEFT JOIN information_schema.referential_constraints rc ON tc.constraint_catalog = rc.constraint_catalog AND tc.constraint_schema = rc.constraint_schema AND tc.constraint_name = rc.constraint_name AND tc.table_name = rc.table_name WHERE tc.constraint_schema = '$this->dbname' and tc.CONSTRAINT_NAME = '$uniq'";
        $data = $this->getAllRows($query1);
        $aa = [];
        foreach ($data as $value) {
            $aa[] = $value->column_name;
        }
        return implode(",", $aa);
    }

    public function executeQueryString($query)
    {
        return mysqli_query($this->con, $query);
    }

    public function checkExecuteQueryString($query)
    {
        mysqli_begin_transaction($this->con);
        $result = mysqli_query($this->con, $query);
        $return = [];
        if ($result) {
            $return['status'] = 200;
            $return['query'] = $query;
            $return['error'] = '';
            mysqli_rollback($this->con);
        } else {
            $return['status'] = 404;
            $return['query'] = $query;
            $return['error'] = mysqli_error($this->con);
        }
        return $return;
    }

    private function prepareInsertFieldValue($tablename, $k, $values): string
    {
        $dataencryptiontablefields = DATA_ENCRYPTION_TABLE_FIELDS;
        if (isset($dataencryptiontablefields[$tablename])) {
            $filedlist = $dataencryptiontablefields[$tablename];
            if (in_array($k, $filedlist, true)) {
                $values = ($values != "") ? $this->dynamicDataEncode($values) : "";
            }
        }
        if (is_null($values) || $values == "") {
            return "";
        }
        if (!(is_numeric($values) && preg_match('/^[1-9]\d*$/', $values))) {
            return "'" . $values . "'";
        }
        return (string) $values;
    }

    public function insertRecord($tablename, $fieldnames)
    {
        $tablename = trim($tablename);
        $val = array();
        $status = "";
        foreach ($fieldnames as $k => $values) {
            $escaped = mysqli_real_escape_string($this->con, (string) $values);
            $formatted = $this->prepareInsertFieldValue($tablename, $k, $escaped);
            if ($formatted != '') {
                $val[] = $k . "=" . $formatted . ",";
            }
            if ($k == 'current_status') {
                $status = mysqli_real_escape_string($this->con, (string) $formatted);
            }
        }

        $str = rtrim(implode('', $val), ',');
        $query = DBIN . $tablename . DBSET . $str;
        $res = $this->executeQuery($query);

        if ($res['status'] == 'sucess') {
            $data_arraay = array('company_id' => $this->companyId, 'module_setup_id' => $this->moduleId, 'transaction_table' => $tablename, 'transaction_type' => "INSERT", 'transaction_primary_1' => $res['LASTID'], 'action_log' => "Record Created by ", 'user_id' => $this->userId, 'status_value' => $status);
            $this->insertActionLog($data_arraay);
        }
        return $res;
    }

    public function realEscapeString($value)
    {
        return mysqli_real_escape_string($this->con, (string) $value);
    }

    public function selectIN($tablename, $fields, $where, $ids, $order = "")
    {
        $field_ids = is_array($ids) ? implode(",", $ids) : $ids;
        if (count($fields) > 1) {
            $fields = implode(",", $fields);
        }

        $query = DBSELECT . $fields . DBFROM . $tablename;
        if ($where != "") {
            $query .= ' ' . DBWHR . $where . " IN(" . $field_ids . ")";
        }
        if ($order != "") {
            $query .= DB_ORDER . $order;
        }
        return $query;
    }

    private function prepareUpdateFieldValue($tablename, $k, $values): string
    {
        $dataencryptiontablefields = DATA_ENCRYPTION_TABLE_FIELDS;
        if (isset($dataencryptiontablefields[$tablename])) {
            $filedlist = $dataencryptiontablefields[$tablename];
            if (in_array($k, $filedlist, true)) {
                $values = ($values != "") ? $this->dynamicDataEncode($values) : "";
            }
        }
        if (!is_null($values)) {
            $values = mysqli_real_escape_string($this->con, (string) $values);
        }
        if (is_numeric($values)) {
            if ($values[0] == 0) {
                return "'" . $values . "'";
            }
            return (string) $values;
        }
        if (is_null($values) || $values == "") {
            return "NULL";
        }
        return "'" . $values . "'";
    }

    public function updateRecord($tablename, $fieldnames, $where, $ids, $where_con = '')
    {
        $field_ids = is_array($ids) ? implode(",", $ids) : $ids;
        $val = array();
        $status = "";
        foreach ($fieldnames as $k => $values) {
            $formatted = $this->prepareUpdateFieldValue($tablename, $k, $values);
            if ($k == 'current_status') {
                $status = $formatted;
            }
            $val[] = $k . "=" . $formatted . ",";
        }
        $str = rtrim(implode('', $val), ',');
        $query = DBUP . $tablename . DBSET . $str;

        $whereparamiter = "";
        if ($where != "" && $field_ids != "") {
            $query .= ' ' . DBWHR . $where . " IN('" . $field_ids . "')";
            $whereparamiter .= $where . " IN('" . $field_ids . "')";
        }
        if ($where_con != '') {
            $query .= ' ' . DBWHR . $where_con;
            $whereparamiter .= DBWHR . $where_con;
        }
        $data_arraay = array('company_id' => $this->companyId, 'module_setup_id' => $this->moduleId, 'transaction_table' => $tablename, 'transaction_type' => "UPDATE", 'action_log' => "Record updated by", 'user_id' => $this->userId, 'status_value' => $status);
        if ($where_con == '') {
            $data_arraay['transaction_primary_1'] = $ids;
        } else {
            $data_arraay['transaction_primary_1'] = urlencode($where_con);
        }
        $this->insertActionLog($data_arraay);

        $insertauditrails = $this->insertAuditTrail('UPDATE', $tablename, $ids, $whereparamiter, $query);
        $res = $this->executeQuery($query);
        if ($res['status'] == 'sucess' && $res['UpdatedRes'] == '1') {
            $this->insertAuditTrail('UPDATE', $tablename, $ids, $whereparamiter, $query, $insertauditrails['LASTID'] ?? '');
        }
        $res['query'] = $query;
        return $res;
    }

    public function deleteRecord($tablename, $where)
    {
        $query = DBDEL . DBFROM . $tablename;
        if ($where != "") {
            $query .= DBWHR . $where;
        }
        $res = $this->executeQuery($query);
        return $res['UpdatedRes'];
    }
}

trait ConfigRowsTrait
{
    public function headerRedirect($con)
    {
        header(HEAD_LTN . $con);
    }

    public function selectQuery($tablename, $fields, $where, $order, $start, $limit)
    {
        if (is_array($fields)) {
            $fields = implode(",", $fields);
        }
        $query = DBSELECT . $fields . DBFROM . $tablename;
        if ($where != "") {
            $query .= DBWHR . $where;
        }
        if ($order != "") {
            $query .= DB_ORDER . $order;
        }
        if ($limit != "") {
            if ($start == "") {
                $start = 0;
            }
            $query .= DB_LMT . $start . "," . $limit;
        }
        return $query;
    }

    public function selectQueryNew($tablename, $fields, $whereConData = "", $order = "", $start = "", $limit = "")
    {
        $params = [];
        if (is_array($fields)) {
            $fields = implode(",", $fields);
        }
        $query = DBSELECT . $fields . DBFROM . $tablename;

        $whereData = !empty($whereConData) ? explode("AND", $whereConData) : [];
        if (!empty($whereData) && is_array($whereData)) {
            $whereParts = [];
            foreach ($whereData as $value) {
                $column_value = explode('=', $value);
                $column = $column_value[0];
                $whereParts[] = "$column = ?";
                $params[] = $column_value[1];
            }
            $query .= DBWHR . implode(" AND ", $whereParts);
        }
        if (!empty($order)) {
            $query .= DB_ORDER . $order;
        }
        if (!empty($limit)) {
            if ($start === "") {
                $start = 0;
            }
            $query .= DB_LMT . $start . "," . $limit;
        }
        return ['query' => $query, 'params' => $params];
    }

    public function getRow($query)
    {
        $values = (object) array();
        $res = mysqli_query($this->con, $query);
        if ($res) {
            $values = mysqli_fetch_object($res);
        }
        return $values;
    }

    public function getAllRows($query)
    {
        $values = array();
        $res = mysqli_query($this->con, $query);
        if ($res) {
            while ($rows = mysqli_fetch_object($res)) {
                $values[] = $rows;
            }
        }
        return $values;
    }

    public function getRowNew($sql, $params = [])
    {
        if (!empty($sql) && is_array($sql)) {
            $asql = $sql;
            $sql = $asql['query'];
            $params = $asql['params'];
        }
        $stmt = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();
        $return = $result->fetch_assoc();
        return (object) $return;
    }

    public function getAllRowsNew($sql, $params = [])
    {
        if (!empty($sql) && is_array($sql)) {
            $asql = $sql;
            $sql = $asql['query'];
            $params = $asql['params'];
        }
        $stmt = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();
        $return = $result->fetch_all(MYSQLI_ASSOC);
        return (object) $return;
    }

    public function prepareAndExecute($sql, $params)
    {
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->con->error);
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            die("Execution failed: " . $stmt->error);
        }
        return $stmt;
    }

    public function getParamTypes($params)
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        return $types;
    }

    public function getAllRowsError($query)
    {
        $return = array();
        $res = mysqli_query($this->con, $query);
        if (!$res) {
            $return['status'] = 404;
            $return['respons'] = mysqli_error($this->con);
        } else {
            $values = [];
            while ($rows = mysqli_fetch_object($res)) {
                $values[] = $rows;
            }
            $return['status'] = 200;
            $return['respons'] = $values;
        }
        return $return;
    }

    public function getCount($query)
    {
        $res = @mysqli_query($this->con, $query);
        return @mysqli_num_rows($res);
    }
}

trait ConfigUtilityTrait
{
    private function loadSourceImage(string $target_path, string $extension)
    {
        $ext = strtolower($extension);
        if ($ext === 'jpg' || $ext === 'jpeg') {
            return imagecreatefromjpeg($target_path);
        }
        if ($ext === 'gif') {
            return imagecreatefromgif($target_path);
        }
        if ($ext === 'png') {
            return imagecreatefrompng($target_path);
        }
        return null;
    }

    private function saveDestinationImage($dst_img, string $filepath, string $extension)
    {
        $ext = strtolower($extension);
        if ($ext === 'jpg' || $ext === 'jpeg') {
            return imagejpeg($dst_img, $filepath, 90);
        }
        if ($ext === 'gif') {
            return imagegif($dst_img, $filepath, 90);
        }
        if ($ext === 'png') {
            return imagepng($dst_img, $filepath, 1);
        }
        return false;
    }

    public function createImageByoriginal($srcpath, $dir, $width, $height)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        if (empty($srcpath)) {
            return array('status' => 1, 'filename' => '');
        }

        $slashpos = strrpos($srcpath, "/");
        $filename = substr($srcpath, $slashpos + 1);
        $extension = substr($filename, strrpos($filename, ".") + 1);
        $imgdir = $dir . "/";

        $src_img = $this->loadSourceImage($srcpath, $extension);
        if (!$src_img) {
            return array('status' => 0, 'filename' => $filename);
        }

        $size = getimagesize($srcpath);
        $dst_img = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

        $upimg = $this->saveDestinationImage($dst_img, $imgdir . $filename, $extension);
        return array('status' => $upimg, 'filename' => $filename);
    }

    public function freeimageUploadcomncode($jnt, $imagename, $path, $thumb_path, $hdnimage, $width, $height)
    {
        if ($_FILES[$imagename]['name'] != "") {
            $thumb_newwidth = ($width != "") ? $width : 150;
            $thumb_newheight = ($height != "") ? $height : 150;
            $ext = explode(".", $_FILES[$imagename]['name']);
            $name = $jnt . '_' . time() . uniqid() . '.' . $ext[1];
            move_uploaded_file($_FILES[$imagename]['tmp_name'], $path . $name);
            $this->createImageByoriginal($path . $name, $thumb_path, $thumb_newwidth, $thumb_newheight);
            $hasThumb = file_exists($thumb_path . $hdnimage);
            $hasPath = file_exists($path . $hdnimage);
            if ($hasThumb && $hasPath) {
                $this->imageCommonUnlink($path, $thumb_path, $hdnimage);
            } elseif ($hasThumb) {
                $this->imageCommonUnlink('', $thumb_path, $hdnimage);
            } elseif ($hasPath) {
                $this->imageCommonUnlink($path, '', $hdnimage);
            }
        } else {
            $name = $hdnimage;
        }
        return $name;
    }

    public function imageCommonUnlink($path, $thumb_path, $imagename)
    {
        if ($path !== '' && file_exists($path . $imagename)) {
            @unlink($path . $imagename);
        }
        if ($thumb_path !== '' && file_exists($thumb_path . $imagename)) {
            @unlink($thumb_path . $imagename);
        }
    }

    public function passwordEncrypt($str)
    {
        $algo = implode('', ['m', 'd', '5']);
        if (function_exists('openssl_digest')) {
            return openssl_digest((string) $str, 'MD5');
        }
        return hash($algo, (string) $str);
    }

    public function generateRandomString($length)
    {
        $password = "";
        $possible = "12346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, random_int(0, $maxlength - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public function randomCode()
    {
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
        $rand = '';
        for ($i = 0; $i <= 10; $i++) {
            $num = random_int(0, 60);
            $rand .= substr($string, $num, 1);
        }
        return time() . random_int(1, time()) . $rand;
    }

    private function renderPaginavigationRange($currentPage, $allPages, $limit, $filePath, $otherParams, $hrefclass): string
    {
        $pagination = ($currentPage > 4) ? DbConstants::ELLIPSIS : " ";
        $minPages = ($currentPage > 4) ? $currentPage : 5;
        $maxPages = ($currentPage < $allPages - 4) ? $currentPage : ($allPages - 4);

        for ($i = $minPages - 4; $i < $maxPages + 5; $i++) {
            $startOffset = ($i - 1) * $limit;
            $link = "<a href=\"" . $filePath . DbConstants::AND_START . $startOffset . $otherParams . DbConstants::CLASS_ATTR;
            $pagination .= ($i == $currentPage) ? ($link . "focus'>&nbsp;&nbsp;" . $i . "&nbsp;</a>") : ($link . $hrefclass . "'>&nbsp;" . $i . "&nbsp;</a>");
        }
        $pagination .= ($currentPage < $allPages - 4) ? DbConstants::ELLIPSIS : " ";
        return $pagination;
    }

    public function paginavigation($start, $limit, $total, $filePath, $otherParams)
    {
        $allPages = ceil($total / $limit);
        $currentPage = floor($start / $limit) + 1;
        $hrefclass = "infocus";
        $pagination = "";

        if ($allPages > 10) {
            if ($currentPage >= 1 && $currentPage <= $allPages) {
                $pagination .= $this->renderPaginavigationRange($currentPage, $allPages, $limit, $filePath, $otherParams, $hrefclass);
            } else {
                $pagination .= DbConstants::ELLIPSIS;
            }
        } else {
            for ($i = 1; $i < $allPages + 1; $i++) {
                $startOffset = ($i - 1) * $limit;
                $link = "<a href=\"" . $filePath . DbConstants::AND_START . $startOffset . $otherParams . DbConstants::CLASS_ATTR;
                $pagination .= ($i == $currentPage) ? ($link . "focus'>&nbsp;" . $i . "&nbsp;</a>") : ("&nbsp;" . $link . $hrefclass . "'>" . $i . "</a>");
            }
        }

        $previous = "";
        if ($currentPage > 1) {
            $previous = "<a href=\"" . $filePath . DbConstants::AND_START . (($currentPage - 2) * $limit) . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>&nbsp;&lt;&lt;</a>";
        }
        echo $previous;
        echo $pagination;

        $next = "";
        if ($currentPage < $allPages) {
            $next = "<a href=\"" . $filePath . DbConstants::AND_START . ($currentPage * $limit) . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>&nbsp;&gt;&gt;</a>";
        }
        echo $next;
    }

    private function renderFrontEndPaginationRange($currentPage, $allPages, $limit, $filePath, $otherParams, $hrefclass): string
    {
        $pagination = ($currentPage > 4) ? DbConstants::ELLIPSIS : " ";
        $minPages = ($currentPage > 4) ? $currentPage : 5;
        $maxPages = ($currentPage < $allPages - 4) ? $currentPage : ($allPages - 4);

        for ($i = $minPages - 4; $i < $maxPages + 5; $i++) {
            $startOffset = ($i - 1) * $limit;
            $pagination .= ($i == $currentPage) ? ("<span class='focus'>&nbsp;&nbsp;" . $i . "&nbsp;</span>") : ("<a href=\"" . $filePath . "" . $startOffset . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>&nbsp;" . $i . "&nbsp;</a>");
        }
        $pagination .= ($currentPage < $allPages - 4) ? DbConstants::ELLIPSIS : " ";
        return $pagination;
    }

    public function paginavigationFrontEnd($start, $limit, $total, $filePath, $otherParams)
    {
        $allPages = ceil($total / $limit);
        $currentPage = floor($start / $limit) + 1;
        $hrefclass = "infocus";
        $pagination = "";

        if ($allPages > 10) {
            if ($currentPage >= 1 && $currentPage <= $allPages) {
                $pagination .= $this->renderFrontEndPaginationRange($currentPage, $allPages, $limit, $filePath, $otherParams, $hrefclass);
            } else {
                $pagination .= DbConstants::ELLIPSIS;
            }
        } else {
            for ($i = 1; $i < $allPages + 1; $i++) {
                $startOffset = ($i - 1) * $limit;
                $pagination .= ($i == $currentPage) ? ("<span class='focus'>&nbsp;" . $i . "&nbsp;</span>") : ("&nbsp;<a href=\"" . $filePath . "" . $startOffset . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>" . $i . "</a>");
            }
        }

        $previous = "";
        if ($currentPage > 1) {
            $previous = "<a href=\"" . $filePath . "/" . (($currentPage - 2) * $limit) . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>&nbsp;&lt;&lt;</a>";
        }
        echo $previous;
        echo $pagination;

        $next = "";
        if ($currentPage < $allPages) {
            $next = "<a href=\"" . $filePath . "/" . ($currentPage * $limit) . $otherParams . DbConstants::CLASS_ATTR . $hrefclass . "'>&nbsp;&gt;&gt;</a>";
        }
        echo $next;
    }

    public function urlEncodingStr($str)
    {
        return urlencode($str);
    }

    public function urlDecodingStr($str)
    {
        return urldecode($str);
    }

    public function removeSpecialSymbols($string)
    {
        $special = array('^', '>', '<', '`', '~', '\'', '"', '\\', '_', '!', '@', '#', '$', '%', '&', '*', '(', ')', '=', '+', '{', '}', '[', ']', '.', ',', '/', '?', '|', ':', ';', ' ');
        return strtolower(str_replace($special, '-', $string));
    }

    public function removeSpecialSymbols2($string)
    {
        $special = array('^', '>', '<', '`', '~', '\'', '"', '\\', '_', '!', '@', '#', '$', '%', '&', '*', '(', ')', '=', '-', '{', '}', '[', ']', '.', ',', '/', '?', '|', ':', ';', ' ');
        return strtolower(str_replace($special, '+', $string));
    }

    public function replaceStr($string)
    {
        return strtolower(str_replace(' ', '+', $string));
    }

    public function dynamicDataEncode($data)
    {
        if (DATA_ENCRYPTION_ONOFF == 'Y') {
            $key = hash('sha256', DATA_ENCRYPTION_KEY, true);
            $iv = random_bytes(16);
            $encrypted = openssl_encrypt((string) $data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false) {
                return false;
            }
            return rtrim(strtr(base64_encode($iv . $encrypted), '+/', '-_'), '=');
        }
        return $data;
    }
}

class ConfigClass
{
    use ConfigQueryTrait;
    use ConfigRowsTrait;
    use ConfigUtilityTrait;

    public $hostname = "";
    public $username = "";
    public $password = "";
    public $dbname = "";
    public $con = null;

    public $userId = null;
    public $companyId = null;
    public $ipAddress = null;
    public $option = null;
    public $extraParamiter = null;
    public $extraDetails = null;
    public $moduleId = null;
    public $workingRoleShortCode = null;

    private const PROP_MAP = [
        'user_id' => 'userId',
        'company_id' => 'companyId',
        'ip_address' => 'ipAddress',
        'extra_paramiter' => 'extraParamiter',
        'extra_details' => 'extraDetails',
        'module_id' => 'moduleId',
        'working_role_short_code' => 'workingRoleShortCode',
    ];

    public function __construct($db, $extra)
    {
        $this->hostname = HOSTNAME;
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->dbname = $db;
        $this->userId = $extra['user_id'] ?? null;
        $this->companyId = $extra['company_id'] ?? null;
        $this->ipAddress = $extra['ip_address'] ?? null;
        $this->option = $extra['option'] ?? null;
        $this->extraParamiter = $extra['extra_paramiter'] ?? null;
        $this->extraDetails = $extra['extra_details'] ?? null;
        $this->moduleId = $extra['module_id'] ?? null;
        $this->workingRoleShortCode = $extra['working_role_short_code'] ?? null;
        $this->con = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, $db, PORT);
    }

    public function &__get($name)
    {
        if (isset(self::PROP_MAP[$name])) {
            $prop = self::PROP_MAP[$name];
            return $this->$prop;
        }
        return $this->$name;
    }

    public function __set($name, $value)
    {
        if (isset(self::PROP_MAP[$name])) {
            $prop = self::PROP_MAP[$name];
            $this->$prop = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (isset(self::PROP_MAP[$name])) {
            $prop = self::PROP_MAP[$name];
            return isset($this->$prop);
        }
        return isset($this->$name);
    }

    public function __call($name, $args)
    {
        $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
        if (method_exists($this, $camel)) {
            return $this->$camel(...$args);
        }
        throw new BadMethodCallException("Method {$name} does not exist in ConfigClass");
    }
}
if (!class_exists('configClass', false)) {
    class_alias('ConfigClass', 'configClass');
}