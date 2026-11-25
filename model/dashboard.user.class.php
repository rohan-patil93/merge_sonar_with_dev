<?php

class DashboardUserClass
{
    private function getAdminAttendanceAndLeaves($company_id, $today, $year, $total_employees, $present, $absent)
    {
        global $callConfig;
        $return = array();

        // On Leave
        $query = "SELECT COUNT(DISTINCT employee_id) AS total
        FROM employee_leaves
        WHERE company_id = {$company_id}
        AND '{$today}' BETWEEN leave_from AND leave_to
        AND is_active = 0";
        $row = $callConfig->getRow($query);
        $on_leave = isset($row->total) ? (int)$row->total : 0;
        $return['on_leave'] = $on_leave;

        // Weekend / Holiday / Unmarked
        $others = $total_employees - ($present + $absent + $on_leave);
        if ($others < 0) {
            $others = 0;
        }
        $return['others'] = $others;
        $return['attendance_pct'] = ($total_employees > 0)
            ? round(($present / $total_employees) * 100, 2)
            : 0;

        // Upcoming Birthdays (Next 30 Days)
        $query = "
        SELECT
        id,
        emp_code,
        display_name,
        image,
        date_of_birth,
        DATE_FORMAT(date_of_birth, '%d-%b') AS birthday
        FROM employees
        WHERE company_id = {$company_id}
        AND is_active = 0
        AND date_of_birth IS NOT NULL
        AND DATE_FORMAT(date_of_birth, '%m-%d')
        BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
        AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 30 DAY), '%m-%d')
        ORDER BY DATE_FORMAT(date_of_birth, '%m-%d') ";
        $upcoming_birthdays = $callConfig->getAllRows($query);
        $return['count_upcoming_birthdays'] = count((array)$upcoming_birthdays);
        $return['upcoming_birthdays'] = $upcoming_birthdays;

        // Leave Summary
        $query = "
        SELECT
            lt.id AS leave_type_id,
            lt.leave_type_name,
            COALESCE(SUM(elb.applicable_leaves), 0) AS allowed_leave_days,
            COALESCE(SUM(elb.consumed_leaves), 0) AS utilized
        FROM leave_types lt
        LEFT JOIN emp_leave_balances elb
            ON elb.leave_type = lt.id
           AND elb.company_id = {$company_id}
           AND elb.is_active = 0
           AND elb.leave_year = {$year}
        WHERE lt.company_id = {$company_id}
          AND lt.is_active = 0
        GROUP BY
            lt.id,
            lt.leave_type_name,
            lt.allowed_leave_days
        ORDER BY lt.leave_type_name ASC";

        $leave_summary = $callConfig->getAllRows($query);
        $return['leave_summary_query'] = $query;
        $return['leave_summary'] = $leave_summary;

        return $return;
    }

    private function getAdminTrendsAndReports($company_id, $today)
    {
        global $callConfig;
        $return = array();

        // Employee Headcount - Last 6 Months
        $extraWhere = '';
        $employee_headcount_last_6_months = array();
        for ($i = 5; $i >= 0; $i--) {
            $month_date  = date('Y-m-01', strtotime("-{$i} months", strtotime($today)));
            $month_end   = date('Y-m-t', strtotime($month_date));
            $month_label = date('M Y', strtotime($month_date));

            $query = "
            SELECT COUNT(*) AS total
            FROM employees
            WHERE company_id = {$company_id}
            {$extraWhere}
            AND joining_date <= '{$month_end}'
            AND (
            date_of_leaving IS NULL
            OR date_of_leaving = '0000-00-00'
            OR date_of_leaving > '{$month_end}') ";

            $row = $callConfig->getRow($query);
            $employee_headcount_last_6_months[] = array(
                'month' => $month_label,
                'count' => isset($row->total) ? (int)$row->total : 0
            );
        }
        $return['employee_headcount_last_6_months'] = $employee_headcount_last_6_months;

        // Department-wise Headcount
        $query = "
        SELECT
        d.id AS department_id,
        d.department_name,
        COUNT(e.id) AS headcount
        FROM departments d
        LEFT JOIN employees e
        ON e.department_id = d.id
        AND e.company_id = {$company_id}
        {$extraWhere}
        AND e.joining_date <= '{$today}'
        AND (
        e.date_of_leaving IS NULL
        OR e.date_of_leaving = '0000-00-00'
        OR e.date_of_leaving > '{$today}'
        )
        WHERE d.company_id = {$company_id}
        AND d.is_active = 0
        GROUP BY d.id, d.department_name
        ORDER BY headcount DESC, d.department_name ASC";
        $return['department_wise_headcount'] = $callConfig->getAllRows($query);

        // Transaction Approval Logs - Pending Count by Table
        $query = "
            SELECT
            transaction_table,
            COUNT(*) AS pending_count
            FROM transaction_approval_logs
            WHERE company_id = {$company_id}
            AND (
            response_type IS NULL
            OR response_type = 0
            OR response_type = '0'
            OR response_type = ''
            )
            GROUP BY transaction_table
            ORDER BY pending_count DESC, transaction_table ASC";
        $return['pending_approvals_by_table'] = $callConfig->getAllRows($query);

        // Recent Announcements
        $query = "   SELECT
            id,
            announcement_heading,
            announcement_message,
            start_date,
            expiry_date,
            button_label,
            button_url,
            image_url,
            video_url
            FROM company_announcements
            WHERE company_id = {$company_id}
            AND is_active = 0
            ORDER BY created_date DESC, id DESC
            LIMIT 10";
        $return['recent_announcements'] = $callConfig->getAllRows($query);

        // Attendance Last 6 Months
        $attendance_last_6_months = array();
        for ($i = 5; $i >= 0; $i--) {
            $month_start = date('Y-m-01', strtotime("-{$i} months"));
            $month_end   = date('Y-m-t', strtotime($month_start));
            $month_label = date('M Y', strtotime($month_start));

            $query = "
                SELECT
                    COUNT(*) AS total_present
                FROM employee_daily_attendances
                WHERE company_id = {$company_id}
                  AND is_active = 0
                  AND attendace_date BETWEEN '{$month_start}' AND '{$month_end}'
                  AND attendance_key_short_code = 'P' ";

            $row = $callConfig->getRow($query);
            $attendance_last_6_months[] = array(
                'month'         => $month_label,
                'month_key'     => date('Y-m', strtotime($month_start)),
                'total_present' => isset($row->total_present) ? (int)$row->total_present : 0
            );
        }
        $return['attendance_last_6_months'] = $attendance_last_6_months;

        return $return;
    }

    public function togetHRAdminDashboardData($get)
    {
        global $callConfig;

        $company_id = $get['company_id'];
        $month = $get['month'];
        $year = $get['year'];
        if ($month == '') {
            $month = date('m');
        }
        if ($year == '') {
            $year = date('Y');
        }
        $month      = str_pad((int)$month, 2, '0', STR_PAD_LEFT);
        $year       = (int)$year;
        $start_date = $year . '-' . $month . '-01';
        $end_date   = date('Y-m-t', strtotime($start_date));
        $today      = date('Y-m-d');
        $detail     = $get;
        $detail['start_date'] = $start_date;
        $detail['end_date'] = $end_date;
        $detail['today'] = $today;
        $detail['month'] = $month;
        $detail['year'] = $year;
        $return['detail'] = $detail;

        // New Joiners
        $query = "SELECT
        id, emp_code, display_name, image, joining_date, designation_id, department_id
        FROM employees
        WHERE company_id = {$company_id}
        AND is_active = 0
        AND joining_date BETWEEN '{$start_date}' AND '{$end_date}'
        ORDER BY joining_date ASC ";
        $new_joiners = $callConfig->getAllRows($query);
        $return['count_new_joiners'] = count((array)$new_joiners);
        $return['new_joiners'] = $new_joiners;

        // Upcoming Exits (Next 30 Days)
        $query = "
        SELECT e.id, e.emp_code, e.display_name, e.image, t.employee_leaving_date
        FROM employee_terminations t
        INNER JOIN employees e ON e.id = t.emp_id
        WHERE t.company_id = {$company_id}
        AND t.is_active = 0
        AND t.employee_leaving_date BETWEEN CURDATE()
        AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY t.employee_leaving_date ASC  ";
        $upcoming_exits = $callConfig->getAllRows($query);
        $return['count_upcoming_exits'] = count((array)$upcoming_exits);
        $return['upcoming_exits'] = $upcoming_exits;

        // Attendance Summary (Today)
        $query = "
        SELECT COUNT(*) AS total
        FROM employees
        WHERE company_id = {$company_id}
        AND is_active = 0
        AND joining_date <= '{$today}'
        AND (
        date_of_leaving IS NULL
        OR date_of_leaving = '0000-00-00'
        OR date_of_leaving > '{$today}'
        )";
        $row = $callConfig->getRow($query);
        $total_employees = isset($row->total) ? (int)$row->total : 0;
        $return['total_employees'] = $total_employees;

        // Present
        $query = "
        SELECT COUNT(DISTINCT employee_id) AS total
        FROM employee_daily_attendances
        WHERE company_id = {$company_id}
        AND attendace_date = '{$today}'
        AND is_active = 0";
        $row = $callConfig->getRow($query);
        $present = isset($row->total) ? (int)$row->total : 0;
        $return['present'] = $present;

        // Absent
        $absent = $total_employees - $present;
        $return['absent'] = $absent;

        // Merge Attendance & Leaves breakdown
        $attLeaves = $this->getAdminAttendanceAndLeaves($company_id, $today, $year, $total_employees, $present, $absent);
        foreach ($attLeaves as $k => $v) {
            $return[$k] = $v;
        }

        // Merge Trends and Reports
        $trends = $this->getAdminTrendsAndReports($company_id, $today);
        foreach ($trends as $k => $v) {
            $return[$k] = $v;
        }

        return $return;
    }

    private function getEmployeeDirectoryAndTasks($company_id, $user_id, $emp_id, $monthCondition)
    {
        global $callConfig;
        $return = array();

        // Company Directory (Last 3 Active Employees)
        $query = "
        SELECT `name`, `designation_name`, `mobile`, `email_id`, `department_name`, `profile_image`
        FROM company_directories
        WHERE company_id = {$company_id}
        AND is_active = 0
        ORDER BY id ASC
        LIMIT 3";
        $return['company_directory'] = $callConfig->getAllRows($query);
        $return['company_directory_query'] = $query;

        // Upcoming Birthdays (Next 10)
        $query = "
        SELECT id, display_name, image, DATE_FORMAT(date_of_birth, '%d %b') AS birthday
        FROM employees
        WHERE company_id = {$company_id}
        AND is_active = 0
        AND date_of_birth IS NOT NULL
        ORDER BY MONTH(date_of_birth), DAY(date_of_birth)
        LIMIT 10";
        $return['upcoming_birthdays'] = $callConfig->getAllRows($query);

        // My Tasks List
        $query = "
        SELECT id, transaction_table, transaction_id, previous_status, response_type, request_date, comments, note, ref_field
        FROM transaction_approval_logs
        WHERE company_id = {$company_id}
          AND assigner_user_id = {$user_id}
          AND (
                response_type IS NULL
                OR response_type = ''
                OR response_type = 0
                OR response_type = '0'
              )
        ORDER BY request_date DESC, id DESC
        LIMIT 50";
        $return['my_tasks_list'] = $callConfig->getAllRows($query);

        // Payslips
        $query = "
        SELECT pay_year, pay_month, salary_component_actual AS net_payable
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND employee_id = {$emp_id}
          AND component_code = 'NETPAYABLE'
          AND is_active = 0
          {$monthCondition}
        ORDER BY pay_year DESC, pay_month DESC";
        $return['payslips'] = $callConfig->getAllRows($query);

        return $return;
    }

    private function getEmployeeMonthAttendanceStats($company_id, $emp_id, $start_date, $end_date)
    {
        global $callConfig;
        $return = array();

        // Present Days
        $query = "
        SELECT COUNT(DISTINCT attendace_date) AS present_days
        FROM employee_daily_attendances
        WHERE company_id = {$company_id}
          AND employee_id = {$emp_id}
          AND is_active = 0
          AND attendace_date BETWEEN '{$start_date}' AND '{$end_date}'
          AND clock_in IS NOT NULL
          AND clock_in <> ''
          AND attendance_key_short_code NOT IN ('A')";
        $row = $callConfig->getRow($query);
        $present_days = isset($row->present_days) ? (int)$row->present_days : 0;
        $total_days_in_month = (int)date('t', strtotime($start_date));

        $query = "
        SELECT COUNT(DISTINCT holiday_date) AS holiday_days
        FROM holidays
        WHERE company_id = {$company_id}
          AND is_active = 0
          AND holiday_date BETWEEN '{$start_date}' AND '{$end_date}'";
        $row = $callConfig->getRow($query);
        $holiday_days = isset($row->holiday_days) ? (int)$row->holiday_days : 0;
        $working_days = $total_days_in_month - $holiday_days;
        if ($working_days < 1) {
            $working_days = 1;
        }
        $nowdays = date('d');
        $attendance_percentage = round(($present_days / $nowdays) * 100, 2);
        $return['present_days'] = $present_days;
        $return['holidays'] = array();
        $return['total_days_in_month'] = $total_days_in_month;
        $return['working_days '] = $nowdays;
        $return['attendance_percentage'] = $attendance_percentage;

        $last_month = date('n', strtotime('first day of last month'));
        $last_year  = date('Y', strtotime('first day of last month'));

        $query = "
        SELECT COALESCE(SUM(salary_component_actual), 0) AS net_payable
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND employee_id = {$emp_id}
          AND component_code = 'NETPAYABLE'
          AND pay_month = {$last_month}
          AND pay_year = {$last_year}
          AND is_active = 0";
        $row = $callConfig->getRow($query);
        $return['last_month'] = $last_month;
        $return['last_year'] = $last_year;
        $return['last_net_payable '] = isset($row->net_payable) ? $row->net_payable : 0;

        $query = "SELECT `document_name`, `process_is_checklist`, `document_url`, `process_file` FROM ess_process_documents WHERE company_id = " . $company_id . " AND is_active =0";
        $return['company_links'] = $callConfig->getAllRows($query);

        return $return;
    }

    public function getEmployeeSelfDashboard($get)
    {
        global $callConfig;

        $company_id = $get['company_id'];
        $user_id = $get['user_id'];
        $emp_id = $get['se_emp_id'];

        $month = $get['month'];
        $year = $get['year'];
        if ($month == '') {
            $month = date('m');
        }
        if ($year == '') {
            $year = date('Y');
        }
        $start_date = $year . '-' . $month . '-01';
        $end_date   = date('Y-m-t', strtotime($start_date));
        $today      = date('Y-m-d');
        $detail     = $get;
        $detail['start_date'] = $start_date;
        $detail['end_date'] = $end_date;
        $detail['today'] = $today;
        $detail['month'] = $month;
        $detail['year'] = $year;
        $return['detail'] = $detail;
        $monthCondition = '';

        // Employee Profile
        $query = "
        SELECT id, emp_code, display_name, first_name, last_name, image, email_id, phone, designation_id, department_id, joining_date, date_of_birth
        FROM employees
        WHERE company_id = {$company_id}
        AND id = {$emp_id}
        AND is_active = 0
        LIMIT 1";
        $return['employee_profile'] = $callConfig->getRow($query);

        // Available Leave Balance
        $query = "
        SELECT COALESCE(SUM(leave_balance), 0) AS available_leave
        FROM leave_types
        WHERE company_id = {$company_id}
        AND is_active = 0";
        $row = $callConfig->getRow($query);
        $return['available_leave'] = isset($row->available_leave) ? (float)$row->available_leave : 0;

        // Today's Attendance Status
        $query = "
        SELECT attendance_key_short_code, clock_in, clock_out, attendace_date
        FROM employee_daily_attendances
        WHERE company_id = {$company_id}
        AND employee_id = {$emp_id}
        AND attendace_date = '{$today}'
        AND is_active = 0
        LIMIT 1";
        $row = $callConfig->getRow($query);

        $today_status = array(
            'status'    => 'Not Marked',
            'clock_in'  => '',
            'clock_out' => '',
            'date'      => $today
        );
        if (!empty($row)) {
            $today_status = array(
                'status'    => $row->attendance_key_short_code,
                'clock_in'  => $row->clock_in,
                'clock_out' => $row->clock_out,
                'date'      => $row->attendace_date
            );
        }
        $return['today_status'] = $today_status;

        // My Pending Tasks
        $query = "SELECT COUNT(*) AS total
        FROM transaction_approval_logs
        WHERE company_id = {$company_id}
        AND user_id = {$user_id}
        AND (
        response_type IS NULL
        OR response_type = ''
        OR response_type = 0
        OR response_type = '0'
        )";
        $row = $callConfig->getRow($query);
        $return['my_pending_tasks'] = isset($row->total) ? (float)$row->total : 0;

        // Used Leave This Month (Approved)
        $query = "SELECT COALESCE(SUM(leave_days), 0) AS total
        FROM employee_leaves
        WHERE company_id = {$company_id}
        AND employee_id = {$emp_id}
        AND current_status = 'Approved'
        AND leave_from BETWEEN '{$start_date}' AND '{$end_date}'
        AND is_active = 0 ";
        $row = $callConfig->getRow($query);
        $return['used_leave_this_month'] = isset($row->total) ? (float)$row->total : 0;

        // My Leave Requests (Last 5)
        $query = "
        SELECT el.id, el.leave_type_id, lt.leave_type_name, el.leave_from, el.leave_to, el.current_status, el.leave_days
        FROM employee_leaves el
        LEFT JOIN leave_types lt
            ON lt.id = el.leave_type_id
           AND lt.company_id = el.company_id
        WHERE el.company_id = {$company_id}
          AND el.employee_id = {$emp_id}
          AND el.is_active = 0
        ORDER BY el.leave_from ASC";
        $return['my_leave_requests'] = $callConfig->getAllRows($query);

        // Upcoming Events (Future Announcements)
        $query = "
        SELECT id, announcement_heading, announcement_message, start_date, expiry_date
        FROM company_announcements
        WHERE company_id = {$company_id}
        AND is_active = 0
        AND start_date >= CURDATE()
        ORDER BY start_date ASC
        LIMIT 5 ";
        $return['upcoming_events'] = $callConfig->getAllRows($query);

        // Recent Announcements
        $query = "
        SELECT id, announcement_heading, announcement_message, start_date
        FROM company_announcements
        WHERE company_id = {$company_id}
        AND is_active = 0
        ORDER BY created_date DESC, id DESC
        LIMIT 10";
        $return['announcements'] = $callConfig->getAllRows($query);

        // Merge Directory & Tasks
        $dirTasks = $this->getEmployeeDirectoryAndTasks($company_id, $user_id, $emp_id, $monthCondition);
        foreach ($dirTasks as $k => $v) {
            $return[$k] = $v;
        }

        // Merge Month Attendance Stats
        $stats = $this->getEmployeeMonthAttendanceStats($company_id, $emp_id, $start_date, $end_date);
        foreach ($stats as $k => $v) {
            $return[$k] = $v;
        }

        return $return;
    }

    public function getPayrollDashboardSummary($get)
    {
        global $callConfig;

        $company_id = $get['company_id'];
        $pay_month = $get['pay_month'];
        $pay_year = $get['pay_year'];
        if ($pay_month == '') {
            $pay_month = date('m');
        }
        if ($pay_year == '') {
            $pay_year = date('Y');
        }
        $start_date = $pay_year . '-' . $pay_month . '-01';
        $end_date   = date('Y-m-t', strtotime($start_date));
        $today      = date('Y-m-d');
        $detail     = $get;
        $detail['start_date'] = $start_date;
        $detail['end_date'] = $end_date;
        $detail['today'] = $today;
        $detail['month'] = $pay_month;
        $detail['year'] = $pay_year;
        $return['detail'] = $detail;

        // Total Employees in Payroll
        $query = "
        SELECT COUNT(id) AS total_employees
        FROM employees
        WHERE company_id = {$company_id}
          AND joining_date <= '" . $end_date . "'
          AND is_active = 0";
        $row = $callConfig->getRow($query);
        $total_employees = isset($row->total_employees) ? (int)$row->total_employees : 0;
        $return['total_employees'] = $total_employees;

        // Gross Payroll
        $query = "
        SELECT COALESCE(SUM(salary_component_actual), 0) AS gross_payroll
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND pay_month = {$pay_month}
          AND pay_year = {$pay_year}
          AND component_code = 'GS'
          AND is_active = 0";
        $row = $callConfig->getRow($query);
        $gross_payroll = isset($row->gross_payroll) ? round((float)$row->gross_payroll, 2) : 0;
        $return['gross_payroll'] = $gross_payroll;

        // Total Deductions
        $query = "
        SELECT COALESCE(SUM(salary_component_actual), 0) AS total_deductions
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND pay_month = {$pay_month}
          AND pay_year = {$pay_year}
          AND compnent_type = 1
          AND is_active = 0";
        $row = $callConfig->getRow($query);
        $total_deductions = isset($row->total_deductions) ? round((float)$row->total_deductions, 2) : 0;
        $return['total_deductions'] = $total_deductions;

        // Net Payroll
        $query = "
        SELECT COALESCE(SUM(salary_component_actual), 0) AS net_payroll
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND pay_month = {$pay_month}
          AND pay_year = {$pay_year}
          AND component_code = 'NETPAYABLE'
          AND is_active = 0";
        $row = $callConfig->getRow($query);
        $net_payroll = isset($row->net_payroll) ? round((float)$row->net_payroll, 2) : 0;
        $return['net_payroll'] = $net_payroll;

        // Payroll Summary by Component
        $query = "
        SELECT
            component_code,
            compnent_type,
            COALESCE(SUM(salary_component_actual), 0) AS amount
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND pay_month = {$pay_month}
          AND pay_year = {$pay_year}
          AND is_active = 0
        GROUP BY component_code, compnent_type
        ORDER BY compnent_type ASC";
        $component_summary = $callConfig->getAllRows($query);
        $return['component_summary'] = $component_summary;

        $query = "SELECT
            pay_year,
            pay_month,
            COUNT(DISTINCT employee_id) AS employee_count,
            COALESCE(SUM(
                CASE
                    WHEN component_code = 'GS'
                    THEN salary_component_actual
                    ELSE 0
                END
            ), 0) AS gross_salary,
            COALESCE(SUM(
                CASE
                    WHEN component_code = 'NETPAYABLE'
                    THEN salary_component_actual
                    ELSE 0
                END
            ), 0) AS net_payable
        FROM pay_run_summary_components
        WHERE company_id = {$company_id}
          AND is_active = 0
        GROUP BY pay_year, pay_month ";
        $all_summary = $callConfig->getAllRows($query);
        $return['all_summary'] = $all_summary;

        return $return;
    }
}
