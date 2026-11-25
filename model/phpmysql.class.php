<?php
class PhpMysqlClass
{
    /**
     * Server-side RBAC gate for the raw SQL endpoints. Must be called by every
     * controller/route BEFORE any method of this class is dispatched.
     * Allows only an authenticated session whose role list contains SUPERADMIN_ROLE;
     * anything else gets HTTP 403 and the request is terminated.
     */
    public static function requireSuperAdmin()
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        $allowed = false;
        if (isset($_SESSION) && !empty($_SESSION['userid'])) {
            $allowed = is_array($_SESSION['role_ids'] ?? null) && in_array(SUPERADMIN_ROLE, $_SESSION['role_ids'], true);
        }
        if (!$allowed) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'Fail', 'code' => 'FORBIDDEN', 'msg' => 'Access denied'));
            exit;
        }
    }

    public function getPHPMYSQLDetails($post)
    {
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $table_list = array();
        $return['dbname'] = $callConfig->dbname;
        $row = $callConfig->getAllRows("SHOW TABLES");
        $rowdb = 'Tables_in_' . $callConfig->dbname;
        foreach ($row as $table) {
            $table_list[] = $table->$rowdb;
        }
        $return['table_list'] = $table_list;
        return $return;
    }

    private function paginateSql($sql_textarea, $limit_select, $current_page, &$return)
    {
        global $callConfig;
        $removellimtsql = $sql_textarea;
        if (strpos($sql_textarea, 'LIMIT') !== false) {
            $removellimt = explode("LIMIT", $sql_textarea);
            $removellimtsql = $removellimt[0];
            $removellimtres = $callConfig->getAllRowsError($removellimtsql);
            $return['removellimtsql'] = $removellimtsql;
            $respons_data_cnt = ($removellimtres['status'] == '200') ? count((array) $removellimtres['respons']) : 0;
        } else {
            $sql_rewsult = $callConfig->getAllRowsError($sql_textarea);
            $return['else_removellimtsql'] = $sql_textarea;
            $respons_data_cnt = ($sql_rewsult['status'] == '200') ? count((array) $sql_rewsult['respons']) : 0;
        }
        $return['total_record_cnt'] = $respons_data_cnt;

        if ($respons_data_cnt != 0) {
            $total_pages = ceil($respons_data_cnt / $limit_select);
            $page = max(1, min((int) $current_page, $total_pages));
            $start_from = ($page - 1) * $limit_select;
            $return['total_pages'] = $total_pages;
            return $removellimtsql . " LIMIT " . $start_from . ", " . $limit_select;
        }
        return $sql_textarea;
    }

    public function postPHPMYSQLDetails($get, $post)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $return['post'] = $post;
        $sql_textarea = $post['sql_textarea'];
        $return['requist_data_sql'] = $sql_textarea;
        $limit_select = max(0, (int) ($post['limit_select'] ?? 0));

        if ($limit_select != 0) {
            $current_page = isset($post['current_page']) ? (int) $post['current_page'] : 1;
            $sql_textarea = $this->paginateSql($sql_textarea, $limit_select, $current_page, $return);
        }

        $sql_rewsult = $callConfig->getAllRowsError($sql_textarea);
        $return['error_status'] = $sql_rewsult['status'];
        $return['respons_data_cnt'] = ($sql_rewsult['status'] == '200') ? count((array) $sql_rewsult['respons']) : 0;
        $return['respons_data'] = $sql_rewsult['respons'];
        $return['final_data_sql'] = $sql_textarea;

        return $return;
    }

    public function getfieldlist($get)
    {
        global $callConfig;
        $return = array();
        if ($get['table_name'] != "") {
            $getfiledsql = "SHOW COLUMNS FROM " . $get['table_name'];
            $colums_list = $callConfig->getAllRows($getfiledsql);
            $return['colums_list'] = $colums_list;
        }
        return $return;
    }

    public function exicutePHPMYSQL()
    {
        global $callConfig;
        $return = array();
        $exicute_sql_textarea = $_POST['exicute_sql_textarea'] ?? '';
        if ($exicute_sql_textarea != "") {
            $sqlresult = $callConfig->executeQuery($exicute_sql_textarea);
            $return['exicute_sql_res'] = $sqlresult;
        } else {
            return false;
        }
        return $return;
    }
}
