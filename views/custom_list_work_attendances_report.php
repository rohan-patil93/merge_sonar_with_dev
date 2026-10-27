<?php
if (SHIFT_CHECK != 1) {
  $genral_shift = 1;
}
$datemonthyeare = $_POST['att_month_year'] != "" ? explode("-", $_POST['att_month_year']) : explode("-", $_GET['SUBDATE']);
$mnth = ($datemonthyeare[1] != "") ? $datemonthyeare[1] : date("m");
$yearr = ($datemonthyeare[0] != "") ? $datemonthyeare[0] : date("Y");
$yearmonth = $yearr . "-" . $mnth;
$timesheetstartdate = $_SESSION['config']['default_timesheet_date'] == "" ? '01' : $_SESSION['config']['default_timesheet_date'];
$workingentryurl = API . "?mode=getemployeeworkattendances_new&month=" . $mnth . "&year=" . $yearr . "&monthstartday=" . $timesheetstartdate . "&emp_id=" . $_SESSION['emp_id'] . "&admin_type=" . $_SESSION['admin_type'] . "&user_email=" . $_SESSION['user_email'];
$workingentrys = callAPI($workingentryurl);
$response = json_decode($workingentrys, 1);

$key_details = $response['attendance_key_types'];

$monthStart = $response['month_start']; // Y-m-d
$monthEnd   = $response['month_end'];   // Y-m-d

$start = new DateTime($monthStart);
$end   = new DateTime($monthEnd);
$end->modify('+1 day'); // inclusive
$attendanceData   = $response['attendance_data'];

$attendanceKeys   = $response['attendance_key_types'];
$holidays         = $response['holidays'];
$weekOffDays      = $response['attendance_settings']['week_off_days'];

if ($_SESSION['success_messages'] != "" && isset($_SESSION['success_messages'])) {
  $success_messages = $_SESSION['success_messages'];
  unset($_SESSION['success_messages']);
}

if ($_SESSION['insert_msg'] != "") {
  $msgcode = $_SESSION['insert_msg'];
  $_SESSION['insert_msg'] = "";
?>
  <script type="text/javascript">
    document.onreadystatechange = function() {
      if (document.readyState == "complete") {
        tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
      }
    }
  </script>
<?php
}
?>

<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <?php if ($success_messages != "") { ?>
      <div class="alert alert-info alert-dismissible">
        <strong><?php echo $success_messages; ?> </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php } ?>
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5>Work Attendance</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
      <div class="p-4">
        <form class="form" id="formsubmit" method="POST">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-3">
              <div class="row">
                <div class="ml-12">
                  <div class="form-group row">
                    <div class="col-10">

                      <input aria-label="Att Month Year" class="form-control" type="month" name="att_month_year" value="<?php echo $yearr . "-" . $mnth ?>" id="att_month_year" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="dropdown mt-1">
                <div class="form-radio">
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Recordtype" type="radio" name="recordtype" <?php echo $_POST['recordtype'] == 'discrepancy' ? "checked" : ""; ?> class="d-none" value="discrepancy" />
                      <i class="helper"></i>Discrepancy Records
                    </label>
                  </div>
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Recordtype" type="radio" name="recordtype" <?php echo $_POST['recordtype'] != 'discrepancy' ? "checked" : ""; ?> value="all" class="d-none" />
                      <i class="helper"></i>All Records
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-2">

            </div>
            <div class="col-lg-2"> <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="ti-filter"></i> Apply Search</button></div>
        </form>
        <div class="row mb-5">
          <div class="col-lg-3"></div>
          <div class="col-lg-9 mt-3">
            <?php
            foreach ($key_details as $attname) {
            ?>
              <span class="form-label label text-white mt-2 " style="background-color: <?php echo strtolower($attname['color_code']); ?>"><?php echo $attname['display_value']; ?></span>
              <span class="m-r-15"><?php echo $attname['key_name']; ?></span>
            <?php } ?>
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-lg-5">
        </div>
        <div class="col-lg-7 text-right">
          <?php
          $btndisable = "";
          if (isset($response['S'])) {
            $btndisable = "disabled";
          }
          ?>
          <a href="custom_csv_emp_attendance_download.php?month=<?php echo $yearr . '-' . str_pad($mnth, 2, '0', STR_PAD_LEFT); ?>"
             class="btn btn-primary waves-effect waves-light"
             target="_blank">
            <i class="ti-download"></i> Download CSV
          </a>


        </div>
      </div>
      <style>
        .attendance-table th,
        .attendance-table td {
          white-space: nowrap;
          font-size: 12px;
          padding: 6px;
        }

        /* Sticky employee column */
        .attendance-table .sticky-col {
          position: sticky;
          left: 0;
          background: #fff;
          z-index: 3;
          min-width: 220px;
        }

        /* Sticky P column */
        .attendance-table th:nth-child(2),
        .attendance-table td:nth-child(2) {
          position: sticky;
          left: 220px;
          background: #fff;
          z-index: 2;
          width: 40px;
        }

        /* Sticky A column */
        .attendance-table th:nth-child(3),
        .attendance-table td:nth-child(3) {
          position: sticky;
          left: 260px;
          background: #fff;
          z-index: 2;
          width: 40px;
        }

        .attendance-table thead th {
          position: sticky;
          top: 0;
          background: #f8f9fa;
          z-index: 4;
        }

        .attendance-table td {
          text-align: center;
          min-width: 34px;
          font-weight: 600;
        }

        .attendance-table th small {
          font-size: 10px;
        }
      </style>
      <div class="card">
        <div class="card-header">
          <h5>Work Entries</h5>
          <div class="text-center">
            <!-- Example: 01-Jan-26 to 31-Jan-26 -->
            <?= date('d-M-y', strtotime($response['month_start'])) ?>
            to
            <?= date('d-M-y', strtotime($response['month_end'])) ?>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive" style="overflow-x:auto;">
            <table class="table table-bordered attendance-table mb-0">
              <thead>
                <tr>
                  <th scope="col" class="sticky-col">Employee</th>
                  <th scope="col" class="text-center">P</th>
                  <th scope="col" class="text-center">A</th>

                  <?php
                  $period = new DatePeriod($start, new DateInterval('P1D'), $end);

                  foreach ($period as $dateObj) {
                    $dayNumber = $dateObj->format('d');   // 01, 02, ...
                    $dayName   = $dateObj->format('D');   // Mon, Tue, ...
                  ?>
                    <th scope="col" class="text-center">
                      <div><?= $dayNumber ?></div>
                      <small class="text-muted"><?= $dayName ?></small>
                    </th>
                  <?php } ?>
                </tr>
              </thead>

              <tbody>
                <?php
                if (count((array)$attendanceData) > 0) {
                  $mk = 1;
                  foreach ($attendanceData as $empId => $empData) { ?>
                    <?php
                    // Reset counters per employee
                    $presentCount = 0;
                    $absentCount  = 0;

                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
                    ?>

                    <tr>
                      <!-- Employee column -->
                      <td class="sticky-col">
                        <div class="align-middle float-left ml-2">
                          <span class="f-12"><?php echo $mk++; ?></span>
                          <?php if ($empData['employee']['image'] != "") { ?>
                            <img src="uploads/all/<?php echo $empData['employee']['image']; ?>" class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                          <?php } else { ?>
                            <span alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center"><?php echo initials($empData['employee']['name']); ?></span>
                          <?php } ?>
                          <div class="d-inline-block">
                            <p class="f-10"><?php echo $empData['employee']['emp_code'] . " - " . $empData['employee']['name']; ?></p>
                          </div>
                        </div>
                      </td>
                      <?php
                      $dailyCells = '';
                      foreach ($period as $dateObj) {
                        $date    = $dateObj->format('Y-m-d');
                        $weekday = $dateObj->format('l');
                        $cellText  = '';
                        $cellStyle = '';
                        $timesheet_id = '';
                        $save_type    = 'insert';

                        // 1. Attendance exists
                        if (isset($empData['days'][$date])) {
                              $timesheet_id = $empData['days'][$date]['id'] ?? '';
                              $save_type    = $timesheet_id ? 'update' : 'insert';

                          $att = $empData['days'][$date];

                          if (!empty($att['attendance_key']) && isset($attendanceKeys[$att['attendance_key']])) {
                            $key = $attendanceKeys[$att['attendance_key']];
                            $cellText  = $key['display_value'];
                            // Count as present
                            $presentCount++;
                          } else {
                            // Attendance exists but no key → Present
                            $cellText = 'P';
                            $presentCount++;
                          }
                        }

                        // 2. Holiday
                        elseif (isset($holidays[$date])) {
                          $cellText = 'H';
                        }
                        // 3. Weekly off
                        elseif (in_array($weekday, $weekOffDays)) {
                          $cellText = 'O';
                        }
                        // 4. Absent
                        else {
                          $cellText = 'A';
                          $absentCount++;
                        }

                        $key = $attendanceKeys[$cellText];
                        $cellStyle = "background-color:{$key['color_code']
}";
                        // Store cell HTML
                        $employee_id   = $empData['employee']['employee_id'];
                        $employee_name = $empData['employee']['name'];
                        $only_month    = $mnth;     // from backend response
                        $workinghrs    = '';         // as requested
                        $status        = $cellText;  // display value
                        $actionHtml = $cellText;
                        if (in_array($cellText, ['P', 'A'], true)) {

                            $actionHtml = '
                                <a href="javascript:void(0);"
                                   class="edit-timesheet"
                                   data-id="'.$timesheet_id.'"
                                   data-date="'.$date.'"
                                   data-emp-id="'.$employee_id.'"
                                   data-workinghrs="'.$workinghrs.'"
                                   data-emp-name="'.htmlspecialchars($employee_name, ENT_QUOTES).'"
                                   data-svaetype="'.$save_type.'"
                                   data-month="'.$only_month.'"
                                   data-status="'.$status.'"
                                   data-year="'.$yearr.'"> '.$cellText .'
                                </a>
                            ';
                        }

                        // final td

                        $dailyCells .= '<td class="text-center" style="' . $cellStyle . '">'.$actionHtml.'</td>';
                      } ?>

                      <!-- NOW print P & A (ONCE) -->
                      <td class="text-center"><?= $presentCount ?></td>
                      <td class="text-center"><?= $absentCount ?></td>
                      <!-- Print all date cells -->
                      <?= $dailyCells ?>

                    </tr>

                  <?php }
                } else { ?>

                  <tr>
                    <td colspan="40" class="text-center text-muted p-4">
                      No employees found for selected month
                    </td>
                  </tr>
                <?php } ?>

              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="timesheet_edit_modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content " id="workentrypop">

          </div>
        </div>
      </div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>

<script>
  $(document).on('click', '.edit-timesheet', function () {

    var data = {
        id: $(this).data('id'),
        timesheet_date: $(this).data('date'),
        emp_id: $(this).data('emp-id'),
        workinghrs: $(this).data('workinghrs'),
        emp_name: $(this).data('emp-name'),
        attendancepop: 1,
        year: $(this).data('year'),
        svaetype: $(this).data('svaetype'),
        month: $(this).data('month'),
        status: $(this).data('status')

    };

    $.ajax({
        url: "views/custom_insert_new_work_entry_popup.php",
        type: "POST",
        data: data,
        success: function (html) {
            $('#workentrypop').html(html);
            $('#timesheet_edit_modal').modal('show');
        }
    });

});

function submitattendance(month, year, monthstartday) {
  $('.submitattendancess').prop('disabled', true);
  swal({
      title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
      text: "<?php echo $_SESSION['error_message']['9034']; ?>",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Submit",
      cancelButtonText: "Don't Submit",
      closeOnConfirm: true,
      closeOnCancel: true
    },
    function(isConfirm) {
      if (isConfirm) {
        $.ajax({
          url: "ajax.php",
          data: 'page=insertemployeeworkattendances&month=' + month + '&year=' + year + '&monthstartday=' + monthstartday,
          type: "POST",
          success: function(respons) {
            console.log(respons);
            location.reload();
          },
          error: function() {}
        });
      } else {
        $('.submitattendancess').prop('disabled', false);
      }
    });
}

</script>
