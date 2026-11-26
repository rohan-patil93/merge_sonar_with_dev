<?php
trait AttendanceSummaryTrait
{
    public function attendaceswdatainsertNew($get, $post)
    {
        global $callConfig;
        $insert = array();
        $data = json_decode($post['data'], 1);
        $query = "SELECT id, emp_code From employees";
        $emp_datta = $callConfig->getAllRows($query);

        $emp_code = array();
        foreach ($emp_datta as $emp_dat) {
            $emp_code[$emp_dat->emp_code] = $emp_dat->id;
        }
        foreach ($data as $dat) {
            $emp_id = $get['employee_id'] ?? 190;
            $clock_in = $dat['in_time'] ?? "";
            $clock_out = $dat['out_time'] ?? "";
            $attendance_key_short_code = $this->determineAttendanceKeyShortCode($get['company_id'], $emp_id, $clock_in, $clock_out);
            $insert_array = $this->buildDailyAttendanceInsertArray($get['company_id'], $emp_id, $clock_in, $clock_out, $dat, $attendance_key_short_code);
            $insert[] = $callConfig->insertRecord('employee_daily_attendances', $insert_array);
        }
        return $insert;
    }

    private function determineAttendanceKeyShortCode($company_id, $emp_id, $clock_in, $clock_out)
    {
        global $callConfig;
        $attendance_key_short_code = "";
        $isClockInValid = ($clock_in != '' && $clock_in != self::TIME_ZERO_SEC && $clock_in != self::TIME_ZERO);
        $isClockOutEmpty = ($clock_out == "" || $clock_out == self::TIME_ZERO_SEC || $clock_out == self::TIME_ZERO);

        if ($isClockInValid && $isClockOutEmpty) {
            return 'MP';
        }

        if ($isClockInValid && !$isClockOutEmpty) {
            $query = "SELECT id, shift_id FROM emp_shifts WHERE emp_id = {$emp_id} AND company_id={$company_id} AND is_active=0";
            $data1 = $callConfig->getRow($query);

            if (!empty($data1) && $data1->shift_id > 0) {
                $query = "SELECT id, shift_setting_id FROM shifts WHERE id = {$data1->shift_id} AND company_id={$company_id} AND is_active=0";
                $data2 = $callConfig->getRow($query);

                if (!empty($data2) && $data2->shift_setting_id > 0) {
                    $query = "SELECT * FROM shift_settings WHERE id = {$data2->shift_setting_id} AND company_id={$company_id} AND is_active=0";
                    $data3 = $callConfig->getRow($query);

                    if (!empty($data3)) {
                        $in = strtotime($clock_in);
                        $out = strtotime($clock_out);
                        if ($out > $in) {
                            $workedMinutes = ($out - $in) / 60;
                            list($fd_h, $fd_m) = explode(':', $data3->full_day_hours);
                            $fullDayMinutes = ((int)$fd_h * 60) + (int)$fd_m;

                            list($hd_h, $hd_m) = explode(':', $data3->half_day_hours);
                            $halfDayMinutes = ((int)$hd_h * 60) + (int)$hd_m;

                            $graceMinutes = (int)$data3->grace_perieod;

                            if ($workedMinutes < ($fullDayMinutes - $graceMinutes) && $workedMinutes >= $halfDayMinutes) {
                                $attendance_key_short_code = 'HD';
                            } elseif ($workedMinutes < $halfDayMinutes) {
                                $attendance_key_short_code = 'A';
                            } else {
                                $attendance_key_short_code = 'P';
                            }
                        }
                    }
                }
            }
        }
        return $attendance_key_short_code;
    }

    private function buildDailyAttendanceInsertArray($company_id, $emp_id, $clock_in, $clock_out, $dat, $attendance_key_short_code)
    {
        return array(
            'company_id' => $company_id,
            'employee_id' => $emp_id,
            'attendance_key_short_code' => $attendance_key_short_code,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'attendace_date' => $dat['new_date'] ?? null,
            'clock_in_date' => $dat['new_date'] ?? null,
            'check_in_type' => 1,
            'check_out_type' => 1,
            'clock_out_date' => $dat['out_date'] ?? null,
            'in_out' => 0,
            'device_type' => 'biometric',
            'shift_code' => $dat['d_ind'] ?? null,
            'swipes' => $dat['biometric_code'] ?? null,
            'is_active' => '0',
            'current_status' => '0'
        );
    }

    public function attendaceswdatainsert($get, $post)
    {
        global $callConfig;
        unset($get);
        $data = json_decode($post['data'], 1);
        $query = "SELECT id, emp_code From employees";
        $emp_datta = $callConfig->getAllRows($query);

        $emp_code = array();
        foreach ($emp_datta as $emp_dat) {
            $emp_code[$emp_dat->emp_code] = $emp_dat->id;
        }
        $insert = array();
        foreach ($data as $dat) {
            $emp_id = 11;
            $insert_array = array('company_id' => '1', 'employee_id' => $emp_id, 'attendace_date' => $dat['new_date'], 'clock_in_date' => $dat['new_date'], 'clock_in' => $dat['in_time'], 'check_in_type' => 1, 'check_out_type' => 1, 'clock_out_date' => $dat['out_date'], 'clock_out' => $dat['out_time'], 'in_out' => 0, 'device_type' => 'biometric', 'shift_code' => $dat['d_ind'], 'swipes' => $dat['biometric_code'], 'is_active' => '0', 'current_status' => '0');
            $insert[] = $callConfig->insertRecord('employee_daily_attendances', $insert_array);
        }
        return $insert;
    }

    public function getEmployeeDailyAttendanceSummary($get)
    {
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $employee_id = isset($get['employee_id']) ? (int)$get['employee_id'] : 0;

        $from_date = !empty($get['from_date']) ? date('Y-m-d', strtotime($get['from_date'])) : date('Y-m-01');
        $to_date = !empty($get['to_date']) ? date('Y-m-d', strtotime($get['to_date'])) : date('Y-m-t');

        if ($from_date > $to_date) {
            $tmp = $from_date;
            $from_date = $to_date;
            $to_date = $tmp;
        }

        list($attendance_keys, $leave_type_keys, $legend, $working_days, $week_off_days) = $this->loadAttendanceKeysAndSettings($company_id);
        list($employee_list, $employee_map, $attendance_data, $holidays) = $this->loadEmployeesAndHolidays($company_id, $employee_id, $from_date, $to_date);
        $attendance_map = $this->loadAttendanceMap($company_id, $employee_id, $from_date, $to_date);
        $date_headers = $this->generateDateHeaders($from_date, $to_date);

        $calculateWorkedHours = function ($clock_in, $clock_out) {
            if (empty($clock_in) || empty($clock_out)) {
                return '-';
            }
            $in = strtotime($clock_in);
            $out = strtotime($clock_out);
            if ($in === false || $out === false || $out <= $in) {
                return '-';
            }
            return gmdate('H:i', $out - $in);
        };

        $grid_rows = array();
        foreach ($attendance_data as $emp_id => $emp_data) {
            $location_id = (int)$emp_data['employee']['location_id'];
            $present_count = 0;
            $absent_count = 0;
            $days = array();

            foreach ($date_headers as $header) {
                $date_key = $header['date'];
                $row = isset($attendance_map[$emp_id][$date_key]) ? $attendance_map[$emp_id][$date_key] : null;
                $days[$date_key] = $this->buildGridRowDayStatus($header, $row, $location_id, $holidays, $week_off_days, $attendance_keys, $leave_type_keys, $present_count, $absent_count, $calculateWorkedHours);
            }

            $grid_rows[$emp_id] = array(
                'employee' => $emp_data['employee'],
                'present_count' => $present_count,
                'absent_count' => $absent_count,
                'days' => $days
            );
        }

        return array(
            'status' => 'success',
            'filters' => array(
                'employee_id' => $employee_id,
                'from_date' => $from_date,
                'to_date' => $to_date
            ),
            'attendance_settings' => array(
                'working_days' => $working_days,
                'week_off_days' => $week_off_days
            ),
            'legend' => $legend,
            'attendance_key_types' => $attendance_keys,
            'employees' => $employee_list,
            'date_headers' => $date_headers,
            'attendance_data' => $grid_rows
        );
    }

    private function loadAttendanceKeysAndSettings($company_id)
    {
        global $callConfig;
        $attendance_key_qry = "SELECT id, key_short_code, display_value, color_code, key_name, leave_category_id
            FROM attendance_key_types
            WHERE company_id = {$company_id}
              AND is_active = 0";
        $attendance_key_rows = $callConfig->getAllRows($attendance_key_qry);

        $attendance_keys = array();
        $leave_type_keys = array();
        $legend = array();

        foreach ((array)$attendance_key_rows as $row) {
            $key_short_code = (string)$row->key_short_code;
            $attendance_keys[$key_short_code] = array(
                'key_short_code' => $key_short_code,
                'display_value' => (string)$row->display_value,
                'key_name' => (string)$row->key_name,
                'color_code' => (string)$row->color_code,
                'leave_category_id' => $row->leave_category_id
            );

            if (!empty($row->leave_category_id)) {
                $leave_type_keys[(string)$row->leave_category_id] = $attendance_keys[$key_short_code];
            }

            $legend[] = $attendance_keys[$key_short_code];
        }

        $settings_qry = "SELECT working_days
            FROM attendance_settings
            WHERE company_id = {$company_id}
              AND is_active = 0
            ORDER BY id ASC
            LIMIT 1";
        $settings_row = $callConfig->getRow($settings_qry);

        $working_days = array();
        if (!empty($settings_row) && !empty($settings_row->working_days)) {
            $decoded_days = json_decode($settings_row->working_days, true);
            if (is_array($decoded_days)) {
                $working_days = $decoded_days;
            }
        }

        $all_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $week_off_days = array_values(array_diff($all_days, $working_days));

        return array($attendance_keys, $leave_type_keys, $legend, $working_days, $week_off_days);
    }

    private function loadEmployeesAndHolidays($company_id, $employee_id, $from_date, $to_date)
    {
        global $callConfig;
        $employee_where = "";
        if ($employee_id > 0) {
            $employee_where = " AND id = {$employee_id}";
        }

        $employees_qry = "SELECT id, emp_code, display_name, image, joining_date, date_of_leaving, location_id
            FROM employees
            WHERE company_id = {$company_id}
              AND employee_type = 1
              AND joining_date <= '{$to_date}'
              AND (date_of_leaving >= '{$from_date}' OR date_of_leaving IS NULL)
              AND is_active = 0
              {$employee_where}
            ORDER BY display_name ASC";
        $employees = $callConfig->getAllRows($employees_qry);

        $employee_list = array();
        $employee_map = array();
        $attendance_data = array();

        foreach ((array)$employees as $emp) {
            $employee_item = array(
                'id' => (int)$emp->id,
                'employee_id' => (int)$emp->id,
                'emp_code' => (string)$emp->emp_code,
                'display_name' => (string)$emp->display_name,
                'name' => (string)$emp->display_name,
                'image' => (string)$emp->image,
                'location_id' => isset($emp->location_id) ? (int)$emp->location_id : 0
            );

            $employee_list[] = array(
                'id' => (int)$emp->id,
                'emp_code' => (string)$emp->emp_code,
                'display_name' => (string)$emp->display_name
            );

            $employee_map[(int)$emp->id] = $employee_item;
            $attendance_data[(int)$emp->id] = array(
                'employee' => $employee_item,
                'present_count' => 0,
                'absent_count' => 0,
                'days' => array()
            );
        }

        $holiday_qry = "SELECT holiday_date, holiday_name, location_id
            FROM holidays
            WHERE company_id = {$company_id}
              AND holiday_date BETWEEN '{$from_date}' AND '{$to_date}'
              AND is_active = 0";
        $holiday_rows = $callConfig->getAllRows($holiday_qry);

        $holidays = array();
        foreach ((array)$holiday_rows as $holiday_row) {
            $date_key = (string)$holiday_row->holiday_date;
            if (!isset($holidays[$date_key])) {
                $holidays[$date_key] = array();
            }
            $holidays[$date_key][] = array(
                'holiday_name' => (string)$holiday_row->holiday_name,
                'location_id' => (int)$holiday_row->location_id
            );
        }

        return array($employee_list, $employee_map, $attendance_data, $holidays);
    }

    private function loadAttendanceMap($company_id, $employee_id, $from_date, $to_date)
    {
        global $callConfig;
        $attendance_where = "";
        if ($employee_id > 0) {
            $attendance_where = " AND eda.employee_id = {$employee_id}";
        }

        $attendance_qry = "SELECT
                eda.id,
                eda.employee_id,
                eda.attendace_date,
                eda.clock_in,
                eda.clock_out,
                eda.check_in_type,
                eda.check_out_type,
                eda.in_out,
                eda.attendance_key_short_code,
                eda.shift_code,
                eda.leave_type_id
            FROM pull_employee_attendance eda
            WHERE eda.company_id = {$company_id}
              AND eda.is_active = 0
              AND eda.attendace_date BETWEEN '{$from_date}' AND '{$to_date}'
              {$attendance_where}";
        $attendance_rows = $callConfig->getAllRows($attendance_qry);

        $attendance_map = array();
        foreach ((array)$attendance_rows as $attendance_row) {
            $emp_id = (int)$attendance_row->employee_id;
            $date_key = (string)$attendance_row->attendace_date;
            if (!isset($attendance_map[$emp_id])) {
                $attendance_map[$emp_id] = array();
            }
            $attendance_map[$emp_id][$date_key] = $attendance_row;
        }
        return $attendance_map;
    }

    private function generateDateHeaders($from_date, $to_date)
    {
        $date_headers = array();
        $cursor_ts = strtotime($from_date);
        $to_ts = strtotime($to_date);

        while ($cursor_ts <= $to_ts) {
            $date_key = date('Y-m-d', $cursor_ts);
            $date_headers[] = array(
                'date' => $date_key,
                'day_number' => date('d', $cursor_ts),
                'day_name' => date('D', $cursor_ts),
                'weekday' => date('l', $cursor_ts)
            );
            $cursor_ts = strtotime('+1 day', $cursor_ts);
        }
        return $date_headers;
    }

    private function buildGridRowDayStatus($header, $row, $location_id, $holidays, $week_off_days, $attendance_keys, $leave_type_keys, &$present_count, &$absent_count, $calculateWorkedHours)
    {
        $date_key = $header['date'];
        $weekday = $header['weekday'];

        $status_code = 'A';
        $status_label = 'Absent';
        $display_value = $attendance_keys['A']['display_value'];
        $color_code = $attendance_keys['A']['color_code'];
        $worked_hours = '-';
        $shift_code = '-';
        $clock_in = '-';
        $clock_out = '-';
        $location_status = '-';
        $location_badge = 'secondary';
        $is_editable = false;
        $attendance_id = '';

        if (!empty($row)) {
            $attendance_id = (int)$row->id;
            $clock_in = !empty($row->clock_in) ? (string)$row->clock_in : '-';
            $clock_out = !empty($row->clock_out) ? (string)$row->clock_out : '-';
            $worked_hours = $calculateWorkedHours($row->clock_in, $row->clock_out);
            $shift_code = !empty($row->shift_code) ? (string)$row->shift_code : '-';

            if (isset($row->in_out) && (int)$row->in_out === 1) {
                $location_status = 'Outside';
                $location_badge = 'danger';
            } else {
                $location_status = 'Inside';
                $location_badge = 'success';
            }

            if (!empty($row->leave_type_id) && isset($leave_type_keys[(string)$row->leave_type_id])) {
                $leave_key = $leave_type_keys[(string)$row->leave_type_id];
                $status_code = $leave_key['key_short_code'];
                $status_label = $leave_key['key_name'];
                $display_value = $leave_key['display_value'];
                $color_code = $leave_key['color_code'];
            } elseif (!empty($row->attendance_key_short_code) && isset($attendance_keys[(string)$row->attendance_key_short_code])) {
                $att_key = $attendance_keys[(string)$row->attendance_key_short_code];
                $status_code = $att_key['key_short_code'];
                $status_label = $att_key['key_name'];
                $display_value = $att_key['display_value'];
                $color_code = $att_key['color_code'];

                if ($status_code === 'P') {
                    $present_count++;
                } elseif ($status_code === 'A') {
                    $absent_count++;
                }
            } else {
                $status_code = 'P';
                $status_label = $attendance_keys['P']['key_name'];
                $display_value = $attendance_keys['P']['display_value'];
                $color_code = $attendance_keys['P']['color_code'];
                $present_count++;
            }

            $is_editable = in_array($status_code, array('P', 'A'), true);
        } elseif (isset($holidays[$date_key])) {
            $is_holiday = false;
            $holiday_name = 'Holiday';

            foreach ($holidays[$date_key] as $holiday_item) {
                if ((int)$holiday_item['location_id'] === 0 || (int)$holiday_item['location_id'] === $location_id) {
                    $is_holiday = true;
                    $holiday_name = $holiday_item['holiday_name'] != '' ? $holiday_item['holiday_name'] : 'Holiday';
                    break;
                }
            }

            if ($is_holiday) {
                $status_code = 'H';
                $status_label = $holiday_name;
                $display_value = $attendance_keys['H']['display_value'];
                $color_code = $attendance_keys['H']['color_code'];
            } else {
                if (in_array($weekday, $week_off_days)) {
                    $status_code = 'O';
                    $status_label = $attendance_keys['O']['key_name'];
                    $display_value = $attendance_keys['O']['display_value'];
                    $color_code = $attendance_keys['O']['color_code'];
                } else {
                    $absent_count++;
                }
            }
        } elseif (in_array($weekday, $week_off_days)) {
            $status_code = 'O';
            $status_label = $attendance_keys['O']['key_name'];
            $display_value = $attendance_keys['O']['display_value'];
            $color_code = $attendance_keys['O']['color_code'];
        } else {
            $absent_count++;
        }

        return array(
            'attendance_id' => $attendance_id,
            'date' => $date_key,
            'status_code' => $status_code,
            'status_label' => $status_label,
            'display_value' => $display_value,
            'color_code' => $color_code,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'worked_hours' => $worked_hours,
            'shift_code' => $shift_code,
            'location_status' => $location_status,
            'location_badge' => $location_badge,
            'is_editable' => $is_editable
        );
    }
}

trait AttendanceBiometricTrait
{
    public function saveEmployeeBiometricRawData($get, $post)
    {
        $payload = isset($post['data']) ? trim((string)$post['data']) : '';
        if ($payload === '') {
            return array(
                'status' => 'error',
                'message' => 'Biometric data is required'
            );
        }

        $process_post = $post;
        $process_post['_skip_raw_save'] = 1;
        $process_res = $this->savePullEmployeeAttendance($get, $process_post);

        if (!isset($process_res['message']) || $process_res['message'] === '') {
            $process_res['message'] = 'Biometric data saved successfully';
        }
        return $process_res;
    }

    public function getPullEmployeeAttendanceKey($company_id, $emp_id, $clock_in, $clock_out, $clock_in_date, $clock_out_date)
    {
        if (empty($clock_in) && empty($clock_out)) {
            return '';
        }
        if (empty($clock_in) || empty($clock_out)) {
            return 'MP';
        }
        return $this->computeWorkedShiftAttendanceKey($company_id, $emp_id, $clock_in, $clock_out, $clock_in_date, $clock_out_date);
    }

    private function computeWorkedShiftAttendanceKey($company_id, $emp_id, $clock_in, $clock_out, $clock_in_date, $clock_out_date)
    {
        $shiftInfo = $this->loadEmpShiftAndSettings($company_id, $emp_id);
        if ($shiftInfo === null) {
            return '';
        }
        list($data2, $data3) = $shiftInfo;

        $in  = new DateTime($clock_in_date . ' ' . $clock_in);
        $out = new DateTime($clock_out_date . ' ' . $clock_out);
        if ($out <= $in) {
            return '';
        }

        return $this->evaluateShiftAttendanceKey($clock_in, $in, $out, $data2, $data3, $emp_id);
    }

    private function loadEmpShiftAndSettings($company_id, $emp_id)
    {
        global $callConfig;
        $data1 = $callConfig->getRow(
            "SELECT id, shift_id FROM emp_shifts
             WHERE emp_id = {$emp_id} AND company_id = {$company_id} AND is_active = 0"
        );
        if (empty($data1) || $data1->shift_id <= 0) {
            return null;
        }

        $data2 = $callConfig->getRow(
            "SELECT * FROM shifts
             WHERE id = {$data1->shift_id} AND company_id = {$company_id} AND is_active = 0"
        );
        if (empty($data2) || $data2->shift_setting_id <= 0) {
            return null;
        }

        $data3 = $callConfig->getRow(
            "SELECT * FROM shift_settings
             WHERE id = {$data2->shift_setting_id} AND company_id = {$company_id} AND is_active = 0"
        );
        if (empty($data3)) {
            return null;
        }
        return array($data2, $data3);
    }

    private function evaluateShiftAttendanceKey($clock_in, $in, $out, $data2, $data3, $emp_id)
    {
        global $callConfig;
        $workedMinutes = ($out->getTimestamp() - $in->getTimestamp()) / 60;

        list($fd_h, $fd_m) = explode(':', $data3->minimum_hours_required);
        $fullDayMinutes = ((int)$fd_h * 60) + (int)$fd_m;

        list($hd_h, $hd_m) = explode(':', $data3->half_day_hours);
        $halfDayMinutes = ((int)$hd_h * 60) + (int)$hd_m;

        $graceParts   = explode(':', $data3->grace_perieod);
        $graceMinutes = (isset($graceParts[0]) ? (int)$graceParts[0] : 0) * 60
                      + (isset($graceParts[1]) ? (int)$graceParts[1] : 0);

        $nclockIn    = $data2->clock_in;
        $ngracePerid = $data3->grace_perieod;
        $seconds     = (strtotime($nclockIn) - strtotime('today'))
                     + (strtotime($ngracePerid) - strtotime('today'));
        $check_grace = gmdate('H:i:s', $seconds);
        $cccheck     = strtotime($check_grace);
        $ccin        = strtotime($clock_in);

        $emp_row = $callConfig->getRow("SELECT designation_id FROM employees WHERE id = {$emp_id}");
        $des     = $emp_row->designation_id ?? null;

        $key = 'P';
        if ($workedMinutes < ($fullDayMinutes - $graceMinutes)) {
            $key = 'Z';
        }

        if ($des != 5 && $cccheck < $ccin) {
            $key = 'Z';
        }

        return $key;
    }

    public function savePullEmployeeAttendance($get, $post)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;

        $payload = isset($post['data']) ? trim((string)$post['data']) : '';
        if ($payload == '') {
            return array('status' => 'error', 'message' => 'Biometric data is required');
        }

        $data = json_decode($payload, true);
        if (!is_array($data) || empty($data)) {
            return array('status' => 'error', 'message' => 'Invalid or empty biometric data');
        }

        $emp_rows = $callConfig->getAllRows("SELECT id, designation_id, b_machine_id FROM employees WHERE company_id = {$company_id} AND is_active = 0");
        $emp_code = array();
        foreach ($emp_rows as $emp_dat) {
            if (!empty($emp_dat->b_machine_id)) {
                $emp_code[(string)$emp_dat->b_machine_id] = (int)$emp_dat->id;
            }
        }

        $results = array();
        $not_found_rows = array();
        foreach ($data as $dat) {
            $biometric_code = trim((string)($dat['employee_code'] ?? ''));
            $machine_code = isset($dat['machine_number']) ? (string)$dat['machine_number'] : null;

            $id_parts = explode('|', (string)($dat['id'] ?? ''));
            $direction_part = $id_parts[0] ?? '';
            $in_out_mode = str_ends_with($direction_part, '-out') ? 1 : 0;
            $emp_id = $emp_code[$biometric_code] ?? null;
            $shift_code = $dat['verify_mode'] ?? null;

            if ($biometric_code == '') {
                $not_found_rows[] = array('reason' => 'employee_not_mapped', 'employee_code' => $biometric_code, 'data' => $dat);
                continue;
            }

            list($event_date, $event_time) = $this->parseBiometricEventDateTime($dat, $id_parts);
            if ($event_date == '' || $event_time == '') {
                $not_found_rows[] = array('reason' => 'invalid_event_datetime', 'employee_code' => $biometric_code, 'data' => $dat);
                continue;
            }

            $clock_in = ($in_out_mode == 0) ? $event_time : null;
            $clock_out = ($in_out_mode == 1) ? $event_time : null;
            $clock_in_date = ($in_out_mode == 0) ? $event_date : null;
            $clock_out_date = ($in_out_mode == 1) ? $event_date : null;

            $att_date = $event_date;
            $latt_date = $event_date;
            if ($in_out_mode == 1 && strtotime($clock_out) < strtotime("09:00:00")) {
                $latt_date = date('Y-m-d', strtotime($latt_date . ' -1 day'));
            }

            $existing = $callConfig->getRow(
                "SELECT id, clock_in, clock_out, clock_in_date, clock_out_date
                 FROM pull_employee_attendance
                 WHERE employee_id = {$emp_id}
                   AND attendace_date = '{$latt_date}'
                   AND company_id = {$company_id}
                   AND is_active = 0
                 LIMIT 1"
            );

            if (count((array)$existing) > 0) {
                list($final_clock_in, $final_clock_in_date, $final_clock_out, $final_clock_out_date) = $this->mergeBiometricClockTimes($existing, $clock_in, $clock_in_date, $clock_out, $clock_out_date);
                $final_key = ($in_out_mode == 1) ? $this->getPullEmployeeAttendanceKey($company_id, $emp_id, $final_clock_in ?: '', $final_clock_out ?: '', $final_clock_in_date ?: $att_date, $final_clock_out_date ?: $att_date) : '';
                $this->updateExistingPullAttendance($company_id, $emp_id, $existing, $final_clock_in, $final_clock_in_date, $final_clock_out, $final_clock_out_date, $final_key);
                $results[] = array('action' => 'updated', 'id' => $existing->id, 'emp_id' => $emp_id, 'date' => $att_date, 'key' => $final_key);
            } else {
                $att_key = ($in_out_mode == 1) ? $this->getPullEmployeeAttendanceKey($company_id, $emp_id, $clock_in, $clock_out, $clock_in_date ?: $att_date, $clock_out_date ?: $att_date) : '';
                $res = $this->insertNewPullAttendance($company_id, $emp_id, $clock_in, $clock_out, $att_date, $clock_in_date, $clock_out_date, $att_key, $biometric_code, $machine_code, $shift_code);
                $results[] = array('action' => 'inserted', 'id' => $res['LASTID'] ?? null, 'emp_id' => $emp_id, 'date' => $att_date, 'key' => $att_key);
            }

            $detail_notes = json_encode($dat);
            $callConfig->insertRecord('pull_employee_attendance_details', array(
                'company_id'     => $company_id,
                'employee_id'    => $emp_id,
                'attendace_date' => $att_date,
                'clock_in'       => $clock_in,
                'check_in_type'  => !empty($clock_in) ? 1 : null,
                'check_out_type' => !empty($clock_out) ? 1 : null,
                'clock_out'      => $clock_out,
                'swipes'         => $biometric_code,
                'machine_code'   => $machine_code,
                'description'    => $detail_notes,
                'is_active'      => 0,
                'current_status' => 0,
                'status_date'    => date(self::DATE_FORMAT_HIS),
                'created_date'   => date(self::DATE_FORMAT_HIS),
                'created_by'     => isset($get['user_id']) ? (int)$get['user_id'] : 0
            ));
        }

        return array(
            'status'  => 'success',
            'message' => 'Attendance processed successfully',
            'total'   => count($results),
            'results' => $results,
            'not_found_total' => count($not_found_rows),
            'not_found_rows' => $not_found_rows
        );
    }

    private function parseBiometricEventDateTime($dat, $id_parts)
    {
        $event_date = '';
        if (!empty($dat['year']) && !empty($dat['month']) && !empty($dat['day'])) {
            $event_date = sprintf('%04d-%02d-%02d', (int)$dat['year'], (int)$dat['month'], (int)$dat['day']);
        }
        $event_time = '';
        if (isset($dat['hour']) && isset($dat['minute']) && isset($dat['second'])) {
            $event_time = sprintf('%02d:%02d:%02d', (int)$dat['hour'], (int)$dat['minute'], (int)$dat['second']);
        }
        if (($event_date == '' || $event_time == '') && !empty($dat['id']) && isset($id_parts[2])) {
            $id_ts = strtotime($id_parts[2]);
            if ($id_ts !== false) {
                if ($event_date == '') {
                    $event_date = date('Y-m-d', $id_ts);
                }
                if ($event_time == '') {
                    $event_time = date('H:i:s', $id_ts);
                }
            }
        }
        return array($event_date, $event_time);
    }

    private function mergeBiometricClockTimes($existing, $clock_in, $clock_in_date, $clock_out, $clock_out_date)
    {
        $final_clock_in      = $existing->clock_in;
        $final_clock_in_date = $existing->clock_in_date;
        $final_clock_out      = $existing->clock_out;
        $final_clock_out_date = $existing->clock_out_date;

        if ($clock_in != null && $clock_in != '') {
            if ($final_clock_in == null || $final_clock_in == '' || $final_clock_in_date == null || $final_clock_in_date == '') {
                $final_clock_in = $clock_in;
                $final_clock_in_date = $clock_in_date;
            } else {
                $old_in_ts = strtotime($final_clock_in_date . ' ' . $final_clock_in);
                $new_in_ts = strtotime($clock_in_date . ' ' . $clock_in);
                if ($new_in_ts && (!$old_in_ts || $new_in_ts < $old_in_ts)) {
                    $final_clock_in = $clock_in;
                    $final_clock_in_date = $clock_in_date;
                }
            }
        }

        if ($clock_out != null && $clock_out != '') {
            if ($final_clock_out == null || $final_clock_out == '' || $final_clock_out_date == null || $final_clock_out_date == '') {
                $final_clock_out = $clock_out;
                $final_clock_out_date = $clock_out_date;
            } else {
                $old_out_ts = strtotime($final_clock_out_date . ' ' . $final_clock_out);
                $new_out_ts = strtotime($clock_out_date . ' ' . $clock_out);
                if ($new_out_ts && (!$old_out_ts || $new_out_ts > $old_out_ts)) {
                    $final_clock_out = $clock_out;
                    $final_clock_out_date = $clock_out_date;
                }
            }
        }

        return array($final_clock_in, $final_clock_in_date, $final_clock_out, $final_clock_out_date);
    }

    private function updateExistingPullAttendance($company_id, $emp_id, $existing, $final_clock_in, $final_clock_in_date, $final_clock_out, $final_clock_out_date, $final_key)
    {
        global $callConfig;
        $callConfig->updateRecord('pull_employee_attendance', array(
            'clock_in'                  => $final_clock_in,
            'clock_in_date'             => $final_clock_in_date,
            'clock_out'                 => $final_clock_out,
            'clock_out_date'            => $final_clock_out_date,
            'attendance_key_short_code' => $final_key,
            'check_in_type'             => !empty($final_clock_in) ? 1 : null,
            'check_out_type'            => !empty($final_clock_out) ? 1 : null,
            'updated_date'              => date(self::DATE_FORMAT_HIS)
        ), 'id', $existing->id);
    }

    private function insertNewPullAttendance($company_id, $emp_id, $clock_in, $clock_out, $att_date, $clock_in_date, $clock_out_date, $att_key, $biometric_code, $machine_code, $shift_code)
    {
        global $callConfig;
        return $callConfig->insertRecord('pull_employee_attendance', array(
            'company_id'                => $company_id,
            'employee_id'               => $emp_id,
            'attendance_key_short_code' => $att_key,
            'clock_in'                  => $clock_in,
            'clock_out'                 => $clock_out,
            'attendace_date'            => $att_date,
            'clock_in_date'             => $clock_in_date,
            'check_in_type'             => !empty($clock_in) ? 1 : null,
            'check_out_type'            => !empty($clock_out) ? 1 : null,
            'clock_out_date'            => $clock_out_date,
            'device_type'               => 'biometric',
            'biometric_code'            => $biometric_code,
            'machine_code'              => $machine_code,
            'shift_code'                => $shift_code,
            'swipes'                    => $biometric_code,
            'is_active'                 => '0',
            'current_status'            => '0',
            'created_date'              => date(self::DATE_FORMAT_HIS)
        ));
    }

    public function toCallPullEmployeeAttendance($get)
    {
        global $callConfig;
        $completed_ids = array();
        $failed_ids = array();
        $result = array();

        $rows = $callConfig->getAllRows("SELECT id, data FROM employee_biometric_raw_data WHERE id > 15 AND id < 250 ORDER BY id ASC");

        if (!$rows || count((array)$rows) === 0) {
            echo "No records found";
            return array(
                'status' => 'success',
                'message' => 'No records found',
                'completed_ids' => $completed_ids,
                'failed_ids' => $failed_ids
            );
        }

        foreach ($rows as $row) {
            $post = array('data' => $row->data ?? '');
            $res = $this->savePullEmployeeAttendance($get, $post);

            if (isset($res['status']) && $res['status'] == 'success') {
                $completed_ids[] = (int)$row->id;
            } else {
                $failed_ids[] = (int)$row->id;
            }
            $result[] = $res;
            break;
        }

        return array(
            'status' => 'success',
            'completed_ids' => $completed_ids,
            'failed_ids' => $failed_ids,
            'total' => count($rows),
            'result' => $result
        );
    }

    public function getBiometricMachineLastUsedReport($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $machine_number = isset($get['machine_number']) ? trim((string)$get['machine_number']) : '';
        $machine_condition = '';
        if ($machine_number != '') {
            $machine_condition = " AND MachineNumber='" . addslashes($machine_number) . "'";
        }

        $machine_query = "SELECT Id, biomatric_machine_name, MachineNumber, Location, IpAddress, Port, InOutIndicator
            FROM biomatric_machines
            WHERE company_id = " . $company_id . " AND is_active = 0" . $machine_condition . "
            ORDER BY biomatric_machine_name ASC, MachineNumber ASC";
        $machine_rows = $callConfig->getAllRows($machine_query);

        $machines = array();
        if (count((array)$machine_rows) > 0) {
            foreach ($machine_rows as $machine_row) {
                $machines[] = array(
                    'id' => $machine_row->Id ?? '',
                    'biomatric_machine_name' => $machine_row->biomatric_machine_name ?? '',
                    'MachineNumber' => $machine_row->MachineNumber ?? '',
                    'Location' => $machine_row->Location ?? '',
                    'IpAddress' => $machine_row->IpAddress ?? '',
                    'Port' => $machine_row->Port ?? '',
                    'InOutIndicator' => $machine_row->InOutIndicator
                );
            }
        }

        $latest_query = "SELECT
                pa.machine_code,
                pa.employee_id,
                pa.attendace_date,
                pa.clock_in_date,
                pa.clock_in,
                pa.clock_out_date,
                pa.clock_out,
                emp.display_name AS last_employee_name,
                emp.emp_code AS last_employee_code
            FROM pull_employee_attendance pa
            INNER JOIN (
                SELECT machine_code, MAX(id) AS last_id
                FROM pull_employee_attendance
                WHERE company_id = " . $company_id . "
                    AND is_active = 0
                    AND machine_code IS NOT NULL
                    AND machine_code != ''
                GROUP BY machine_code
            ) latest ON latest.last_id = pa.id
            LEFT JOIN employees emp ON emp.id = pa.employee_id
                AND emp.company_id = " . $company_id . "
                AND emp.is_active = 0
            WHERE pa.company_id = " . $company_id . "
                AND pa.is_active = 0";

        $latest_rows = $callConfig->getAllRows($latest_query);
        $latest_rows_by_machine = array();
        if (count((array)$latest_rows) > 0) {
            foreach ($latest_rows as $latest_row) {
                $machine_key = (string)($latest_row->machine_code ?? '');
                if ($machine_key == '') {
                    continue;
                }
                $latest_rows_by_machine[$machine_key] = array(
                    'machine_no' => $machine_key,
                    'employee_id' => $latest_row->employee_id ?? '',
                    'attendace_date' => $latest_row->attendace_date ?? '',
                    'clock_in_date' => $latest_row->clock_in_date ?? '',
                    'clock_in' => $latest_row->clock_in ?? '',
                    'clock_out_date' => $latest_row->clock_out_date ?? '',
                    'clock_out' => $latest_row->clock_out ?? '',
                    'last_employee_name' => $latest_row->last_employee_name ?? '',
                    'last_employee_code' => $latest_row->last_employee_code ?? ''
                );
            }
        }

        return array(
            'status' => 'success',
            'message' => 'Biometric machine last used report fetched successfully',
            'machines' => $machines,
            'latest_rows_by_machine' => $latest_rows_by_machine
        );
    }

    public function fixWrongInPullEmployeeAttendance($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $user_id    = isset($get['user_id']) ? (int)$get['user_id'] : 0;
        if ($company_id <= 0) {
            return array('status' => 'error', 'message' => 'company_id is required');
        }
        $updated_details = 0;
        $updated_main    = 0;
        $skipped         = array();
        $rows = $callConfig->getAllRows("
            SELECT * FROM pull_employee_attendance_details WHERE JSON_EXTRACT(description, '$.in_out_mode') >1 AND JSON_UNQUOTE(JSON_EXTRACT(description, '$.id')) LIKE '%-in|%' AND description IS NOT NULL AND description != '' ");
        foreach ($rows as $row) {
            $desc = json_decode($row->description, true);
            if (!is_array($desc)) {
                $skipped[] = array('detail_id' => $row->id, 'reason' => 'invalid_json');
                continue;
            }

            $id_parts = explode('|', (string)($desc['id'] ?? ''));
            $direction_part = $id_parts[0] ?? '';
            $derived_in_out_mode = (substr($direction_part, -4) == '-out') ? 1 : ((substr($direction_part, -3) == '-in') ? 0 : null);
            if ($derived_in_out_mode == 1) {
                continue;
            }

            list($event_date, $event_time) = $this->parseBiometricEventDateTime($desc, $id_parts);
            if ($event_date == '' || $event_time == '') {
                $skipped[] = array('detail_id' => $row->id, 'reason' => 'invalid_event_datetime');
                continue;
            }

            $callConfig->updateRecord('pull_employee_attendance_details', array(
                'clock_in'       => $event_time,
                'check_in_type'  => 1,
                'clock_out'      => null,
                'check_out_type' => null,
                'updated_by'     => $user_id,
                'updated_date'   => date(self::DATE_FORMAT_HIS)
            ), 'id', $row->id);
            $updated_details++;

            $this->fixWrongMainPullAttendanceRecord($company_id, $event_date, $event_time, $row->id, $skipped, $updated_main);
        }

        return array(
            'status'          => 'success',
            'message'         => 'Wrong IN records fixed successfully',
            'updated_details' => $updated_details,
            'updated_main'    => $updated_main,
            'skipped_total'   => count($skipped),
            'skipped'         => $skipped
        );
    }

    private function fixWrongMainPullAttendanceRecord($company_id, $event_date, $event_time, $detailRowId, &$skipped, &$updated_main)
    {
        global $callConfig;
        $main = $callConfig->getRow("
            SELECT id, employee_id, attendace_date, clock_in, clock_in_date, clock_out, clock_out_date, machine_code
            FROM pull_employee_attendance
            WHERE company_id = {$company_id}
              AND is_active = 0
              AND clock_out_date = '" . addslashes($event_date) . "'
              AND clock_out = '" . addslashes($event_time) . "'
            LIMIT 1
        ");

        if (count((array)$main) <= 0) {
            $skipped[] = array(
                'detail_id'  => $detailRowId,
                'reason'     => 'main_attendance_not_found',
                'event_date' => $event_date,
                'event_time' => $event_time
            );
            return;
        }

        $should_update_clock_in = false;
        if (empty($main->clock_in) || empty($main->clock_in_date)) {
            $should_update_clock_in = true;
        } else {
            $old_in_ts = strtotime($main->clock_in_date . ' ' . $main->clock_in);
            $new_in_ts = strtotime($event_date . ' ' . $event_time);
            if ($new_in_ts && (!$old_in_ts || $new_in_ts < $old_in_ts)) {
                $should_update_clock_in = true;
            }
        }

        if ($should_update_clock_in) {
            $final_clock_in      = $event_time;
            $final_clock_in_date = $event_date;
            $final_clock_out     = $main->clock_out;
            $final_clock_out_date = $main->clock_out_date;

            $should_clear_clock_out = (!empty($main->clock_out) && !empty($main->clock_out_date) && $main->clock_out_date == $event_date && $main->clock_out == $event_time);
            if ($should_clear_clock_out) {
                $final_clock_out = null;
                $final_clock_out_date = null;
            }

            $final_key = !empty($final_clock_out) ? $this->getPullEmployeeAttendanceKey($company_id, $main->employee_id, $final_clock_in, $final_clock_out, $final_clock_in_date ?: $main->attendace_date, $final_clock_out_date ?: $main->attendace_date) : '';

            $update_data = array(
                'clock_in'      => $final_clock_in,
                'clock_in_date' => $final_clock_in_date,
                'check_in_type' => 1,
                'updated_date'  => date(self::DATE_FORMAT_HIS)
            );
            if ($should_clear_clock_out) {
                $update_data['clock_out']      = null;
                $update_data['clock_out_date'] = null;
                $update_data['check_out_type'] = null;
            }
            if ($final_key != '') {
                $update_data['attendance_key_short_code'] = $final_key;
            }

            $callConfig->updateRecord('pull_employee_attendance', $update_data, 'id', $main->id);
            $updated_main++;
        }
    }

    public function updateAttendanceDataoOfThreeBiometricMachines($get)
    {
        global $callConfig;
        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $clock_in_start = '2026-06-30';
        $clock_in_end = '2026-08-01';

        $select_query = "SELECT * FROM sm_biometric_entry
            WHERE new_date > '" . $clock_in_start . "' AND new_date < '" . $clock_in_end . "'";
        $biometric_data = $callConfig->getAllRows($select_query);

        $query = "SELECT id, designation_id, b_machine_id From employees_suwalka";
        $emp_data = $callConfig->getAllRows($query);
        $emp_code = array();
        foreach ($emp_data as $emp_dat) {
            $emp_code[$emp_dat->b_machine_id] = $emp_dat->id;
        }

        $insert = array();
        if (count((array)$biometric_data) > 0) {
            foreach ($biometric_data as $data) {
                $this->processSingleMachineBiometricEntry($data, $emp_code, $company_id, $insert);
            }
        }
        return array(
            'status' => 'success',
            'message' => 'Records deleted successfully',
            'result' => $insert
        );
    }

    private function processSingleMachineBiometricEntry($data, $emp_code, $company_id, &$insert)
    {
        global $callConfig;
        $emp_id = $emp_code[$data->biometric_code] ?? null;
        $clock_in = $data->in_time ?? "";
        $clock_out = $data->out_time ?? "";
        $att_date = $data->new_date ?? '';
        $clock_in_date = $data->new_date ?? '';
        $clock_out_date = $data->out_date ?? '';
        $machine_code = $data->i_mc ?? $data->o_mc ?? null;
        $shift_code   = $data->d_ind ?? null;
        $biometric_code = $data->biometric_code ?? null;

        $existing = $callConfig->getRow("
            SELECT id, clock_in, clock_out, clock_in_date, clock_out_date
            FROM client_employee_daily_attendances_6_26
            WHERE company_id = '" . $company_id . "'
              AND biometric_code = '" . $biometric_code . "'
              AND attendace_date = '" . $att_date . "'
              AND is_active = 0
            LIMIT 1
        ");

        if (count((array)$existing) > 0) {
            $final_clock_in      = $existing->clock_in;
            $final_clock_in_date = $existing->clock_in_date;
            $final_clock_out     = $existing->clock_out;
            $final_clock_out_date = $existing->clock_out_date;

            if (!empty($clock_in)) {
                if (empty($final_clock_in) || empty($final_clock_in_date)) {
                    $final_clock_in = $clock_in;
                    $final_clock_in_date = $clock_in_date;
                } else {
                    $old_in_ts = strtotime($final_clock_in_date . ' ' . $final_clock_in);
                    $new_in_ts = strtotime($clock_in_date . ' ' . $clock_in);
                    if ($new_in_ts !== false && ($old_in_ts === false || $new_in_ts < $old_in_ts)) {
                        $final_clock_in = $clock_in;
                        $final_clock_in_date = $clock_in_date;
                    }
                }
            }

            if (!empty($clock_out)) {
                if (empty($final_clock_out) || empty($final_clock_out_date)) {
                    $final_clock_out = $clock_out;
                    $final_clock_out_date = $clock_out_date;
                } else {
                    $old_out_ts = strtotime($final_clock_out_date . ' ' . $final_clock_out);
                    $new_out_ts = strtotime($clock_out_date . ' ' . $clock_out);
                    if ($new_out_ts !== false && ($old_out_ts === false || $new_out_ts > $old_out_ts)) {
                        $final_clock_out = $clock_out;
                        $final_clock_out_date = $clock_out_date;
                    }
                }
            }

            $update_array = array(
                'clock_in'       => $final_clock_in,
                'clock_out'      => $final_clock_out,
                'clock_in_date'  => $final_clock_in_date,
                'clock_out_date' => $final_clock_out_date,
                'check_in_type'  => !empty($final_clock_in) ? 1 : null,
                'check_out_type' => !empty($final_clock_out) ? 1 : null,
                'shift_code'     => $shift_code,
                'biometric_code' => $biometric_code,
                'machine_code'   => $machine_code,
                'updated_date'   => date(self::DATE_FORMAT_HIS)
            );

            $insert['attendance']['update'][] = $callConfig->updateRecord('client_employee_daily_attendances_7_26', $update_array, 'id', $existing->id);
            $insert['attendance']['update'][] = $update_array;
        } else {
            $insert_array = array(
                'company_id'      => $company_id,
                'employee_id'     => $emp_id,
                'clock_in'        => $clock_in ?? '',
                'clock_out'       => $clock_out ?? '',
                'attendace_date'  => $att_date,
                'clock_in_date'   => $clock_in_date,
                'check_in_type'   => !empty($clock_in) ? 1 : null,
                'check_out_type'  => !empty($clock_out) ? 1 : null,
                'clock_out_date'  => $clock_out_date,
                'in_out'          => 0,
                'device_type'     => 'biometric',
                'shift_code'      => $shift_code,
                'biometric_code'  => $biometric_code,
                'machine_code'    => $machine_code,
                'swipes'          => $biometric_code,
                'is_active'       => '0',
                'current_status'  => '0'
            );
            $insert['attendance']['insert'][] = $callConfig->insertRecord('client_employee_daily_attendances_7_26', $insert_array);
            $insert['attendance']['insert'][] = $insert_array;
        }
    }

    public function torunnewattendancecodes($get)
    {
        global $callConfig;
        $query = "SELECT * FROM `sm_biometric_entry` WHERE `new_date` > '2026-03-31'";
        $att_datta = $callConfig->getAllRows($query);
        $data = (array)$att_datta;
        $query = "SELECT id, b_machine_id From employees";
        $emp_datta = $callConfig->getAllRows($query);
        $emp_code = array();
        foreach ($emp_datta as $emp_dat) {
            $emp_code[$emp_dat->b_machine_id] = $emp_dat->id;
        }
        $insert = array();
        $allinsert_array = array();
        foreach ($data as $dat) {
            $this->processNewAttendanceRecord((array)$dat, $emp_code, $get['company_id'], $insert, $allinsert_array);
        }
        return $insert;
    }

    private function processNewAttendanceRecord($dat, $emp_code, $company_id, &$insert, &$allinsert_array)
    {
        global $callConfig;
        $emp_id = $emp_code[$dat['biometric_code']] ?? null;
        $clock_in = $dat['in_time'] ?? "";
        $clock_out = $dat['out_time'] ?? "";
        $clock_in_date = $dat['new_date'] ?? null;
        $clock_out_date = $dat['out_date'] ?? null;

        $query = "SELECT * FROM employee_daily_attendances WHERE attendace_date='" . $dat['new_date'] . "' AND employee_id = " . (int)$emp_id . " AND is_active = 0";
        $cgtgdata = $callConfig->getRow($query);
        if (count((array)$cgtgdata) > 0) {
            $final_clock_in      = $cgtgdata->clock_in;
            $final_clock_in_date = $cgtgdata->clock_in_date;
            $final_clock_out     = $cgtgdata->clock_out;
            $final_clock_out_date = $cgtgdata->clock_out_date;

            if ($clock_in != null && $clock_in != '' && $final_clock_in != null && $final_clock_in != '' && $final_clock_in_date != null && $final_clock_in_date != '') {
                $old_in_ts = strtotime($final_clock_in_date . ' ' . $final_clock_in);
                $new_in_ts = strtotime($clock_in_date . ' ' . $clock_in);
                if ($new_in_ts && (!$old_in_ts || $new_in_ts > $old_in_ts)) {
                    $clock_in = $final_clock_in;
                    $clock_in_date = $final_clock_in_date;
                }
            }

            if ($clock_out != null && $clock_out != '' && $final_clock_out != null && $final_clock_out != '' && $final_clock_out_date != null && $final_clock_out_date != '') {
                $old_out_ts = strtotime($final_clock_out_date . ' ' . $final_clock_out);
                $new_out_ts = strtotime($clock_out_date . ' ' . $clock_out);
                if ($new_out_ts && (!$old_out_ts || $new_out_ts < $old_out_ts)) {
                    $clock_out = $final_clock_out;
                    $clock_out_date = $final_clock_out_date;
                }
            }
        }

        if ($clock_in != self::TIME_ZERO_SEC && $clock_out != self::TIME_ZERO_SEC) {
            $attendance_key_short_code = $this->calculateNewAttendanceShiftKey($emp_id, $company_id, $clock_in, $clock_out);
            $insert_array = array(
                'company_id' => $company_id,
                'employee_id' => $emp_id,
                'attendance_key_short_code' => $attendance_key_short_code,
                'clock_in' => $clock_in,
                'clock_out' => $clock_out,
                'attendace_date' => $dat['new_date'] ?? null,
                'clock_in_date' => $clock_in_date,
                'check_in_type' => 1,
                'check_out_type' => 1,
                'clock_out_date' => $clock_out_date,
                'in_out' => 0,
                'device_type' => 'biometric',
                'shift_code' => $dat['d_ind'] ?? null,
                'swipes' => $dat['biometric_code'] ?? null,
                'is_active' => '0',
                'current_status' => '0'
            );

            if (count((array)$cgtgdata) > 0) {
                $whrcon = "id=" . $cgtgdata->id;
                $insert[] = $callConfig->updateRecord('employee_daily_attendances', $insert_array, '', '', $whrcon);
            } else {
                $insert[] = $callConfig->insertRecord('employee_daily_attendances', $insert_array);
            }
            $allinsert_array[] = $insert_array;
        }
    }

    private function calculateNewAttendanceShiftKey($emp_id, $company_id, $clock_in, $clock_out)
    {
        global $callConfig;
        $attendance_key_short_code = "";
        $isClockInValid = ($clock_in != '' && ($clock_in != self::TIME_ZERO_SEC || $clock_in != self::TIME_ZERO));
        $isClockOutEmpty = ($clock_out == "" || $clock_out == self::TIME_ZERO_SEC || $clock_out == self::TIME_ZERO);

        if ($isClockInValid && $isClockOutEmpty) {
            return 'MP';
        }

        if ($isClockInValid && !$isClockOutEmpty) {
            $query = "SELECT id, shift_id FROM emp_shifts WHERE emp_id = {$emp_id} AND company_id={$company_id} AND is_active=0";
            $data1 = $callConfig->getRow($query);

            if (!empty($data1) && $data1->shift_id > 0) {
                $query = "SELECT id, shift_setting_id FROM shifts WHERE id = {$data1->shift_id} AND company_id={$company_id} AND is_active=0";
                $data2 = $callConfig->getRow($query);

                if (!empty($data2) && $data2->shift_setting_id > 0) {
                    $query = "SELECT * FROM shift_settings WHERE id = {$data2->shift_setting_id} AND company_id={$company_id} AND is_active=0";
                    $data3 = $callConfig->getRow($query);

                    if (!empty($data3)) {
                        $in  = strtotime($clock_in);
                        $out = strtotime($clock_out);
                        if ($out > $in) {
                            $workedMinutes = ($out - $in) / 60;
                            list($fd_h, $fd_m) = explode(':', $data3->full_day_hours);
                            $fullDayMinutes = ((int)$fd_h * 60) + (int)$fd_m;

                            list($hd_h, $hd_m) = explode(':', $data3->half_day_hours);
                            $halfDayMinutes = ((int)$hd_h * 60) + (int)$hd_m;

                            $graceParts = explode(':', $data3->grace_perieod);
                            $g_h = isset($graceParts[0]) ? (int)$graceParts[0] : 0;
                            $g_m = isset($graceParts[1]) ? (int)$graceParts[1] : 0;
                            $graceMinutes = ($g_h * 60) + $g_m;

                            if ($workedMinutes < ($fullDayMinutes - $graceMinutes) && $workedMinutes >= $halfDayMinutes) {
                                $attendance_key_short_code = 'Z';
                            } elseif ($workedMinutes < $halfDayMinutes) {
                                $attendance_key_short_code = 'A';
                            } else {
                                $attendance_key_short_code = 'P';
                            }
                        }
                    }
                }
            }
        }
        return $attendance_key_short_code;
    }
}

class AttendanceUserClass extends userClass
{
    use AttendanceSummaryTrait;
    use AttendanceBiometricTrait;

    public const TIME_ZERO_SEC = "00:00:00";
    public const TIME_ZERO = "00:00";
    public const DATE_FORMAT_HIS = "Y-m-d H:i:s";
}
