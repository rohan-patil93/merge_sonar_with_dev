<?php
include('includes/session.php');
include('dbconfig.php');
include('comm.php');

if (isset($_GET['month'])) {
    $month = $_GET['month'];

    $curl = API . "?mode=createExcelDataForEmployeeMonthlyAttedance&month=" . $month;
    // exit($curl);
    $response = callAPI($curl);
    $response = json_decode($response, true);
    
    if (isset($response['data']) && !empty($response['data'])) {
        // Parse month to get year and month number
        list($year, $month_num) = explode('-', $month);
        $year = (int)$year;
        $month_num = (int)$month_num;
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);
        
        // Pre-build date strings
        $dates = array();
        for ($day = 1; $day <= $days_in_month; $day++) {
            $dates[$day] = sprintf('%04d-%02d-%02d', $year, $month_num, $day);
        }
        
        // Build headers
        $header_arr = array('S.No.', 'Emp Code', 'Designation', 'Department', 'Employee Name', 'Division', 'Location', 'Shift', 'Timing');
        for ($day = 1; $day <= $days_in_month; $day++) {
            $header_arr[] = date('d-m-Y', strtotime($dates[$day]));
            $header_arr[] = '';
            $header_arr[] = '';
        }
        
        $header_arr_2 = array('', '', '', '', '', '', '', '', '');
        for ($day = 1; $day <= $days_in_month; $day++) {
            $header_arr_2[] = 'Status';
            $header_arr_2[] = 'In Time';
            $header_arr_2[] = 'Out Time';
        }
        
        // Combine headers with data
        $data_arr = array($header_arr, $header_arr_2);
        
        // Build shifts lookup array
        $shifts_lookup = array();
        if (isset($response['shifts']) && is_array($response['shifts'])) {
            foreach ($response['shifts'] as $shift_id => $shift_info) {
                $short_code = $shift_info['short_code'] ?? '';
                $clock_in = $shift_info['clock_in'] ?? '';
                $clock_out = $shift_info['clock_out'] ?? '';
                
                // Format timing as clock_in - clock_out
                $timing = '';
                if (!empty($clock_in) && !empty($clock_out)) {
                    $timing = $clock_in . ' - ' . $clock_out;
                }
                
                $shifts_lookup[$shift_id] = array(
                    'short_code' => $short_code,
                    'timing' => $timing
                );
            }
        }
        
        // Add employee attendance data
        foreach ($response['data'] as $employee_row) {
            // Shift ID is at index 7 (after S.No., Emp Code, Designation, Department, Employee Name, Division, Location)
            $sid = $employee_row[7] ?? null;
            if ($sid && isset($shifts_lookup[$sid])) {
                $employee_row[7] = $shifts_lookup[$sid]['short_code'];
                $employee_row[8] = $shifts_lookup[$sid]['timing'];
            }
            
            $data_arr[] = $employee_row;
        }
        
        // Generate CSV filename
        $filename = 'employee_attendance_' . $year . '_' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '.csv';
        
        // Build CSV string
        $data = "";
        foreach ($data_arr as $row) {
            if ($data != "") {
                $data .= "\n";
            }
            $data .= implode(',', $row);
        }
        
        // Set headers for CSV download
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $data;
        exit;
    } else {
        echo "No data found for the selected month.";
        exit;
    }
} else {
    echo "Invalid request. Month parameter is required.";
    exit;
}
?>
