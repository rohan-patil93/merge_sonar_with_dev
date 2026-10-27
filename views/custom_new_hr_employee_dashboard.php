<?php
 $se_emp_id = $_SESSION['emp_id'];
 $se_emp_id = 1;
 $url = API."?mode=getEmployeeSelfDashboard&se_emp_id=".$se_emp_id;
 $calurl = callAPI($url);
 $data = json_decode($calurl,1);
 $employee_profile = $data['employee_profile'];
?>
<div class="row mt-3">
  <div class="col-lg-8 col-md-8 col-sm-12 col-12">
    <h3 class="f-w-600 mb-0">Good Morning,  <?php echo $employee_profile['display_name']; ?></h3>
    <p>Here's what's happning with you today.</p>
  </div>
</div>
<div class="user-details-sec mt-3">
  <div class="details-box">
    <div class="card b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fas fa-calendar-alt f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Available Leave</span>
            <h4 class="f-30 f-w-600"><?php echo $data['available_leave']; ?></h4>
            <span class="f-14 text-dark f-w-600">Till Now</span>


          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-check-circle f-24 text-success"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Today's Status</span>
            <h4 class="f-30 f-w-600 text-success"><?php echo $data['today_status']['status']; ?></h4>
            <span class="f-14 text-dark f-w-600">Checked in at <?php echo $data['today_status']['clock_in']!="" ? $data['today_status']['clock_in'] : "--:--:--"; ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-yellow-50 p-2 b-radius-15 text-center me-3">
            <i class="far fa-file-alt f-24 text-warning"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">My Tasks</span>
            <h4 class="f-30 f-w-600"><?php echo $data['my_pending_tasks']; ?></h4>
            <span class="f-14 f-w-600">Pending Tasks</span>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-briefcase f-24 text-purple"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">My Payslip<span class="f-8">  (<?php echo date('F', mktime(0, 0, 0, $data['last_month'], 1))." ".$data['last_year'];?>)</span></span>
            <h4 class="f-30 f-w-600"><?php echo $data['last_net_payable']!="" ? customeummarmonyformateifinecheck($data['last_net_payable']) : "--"; ?></h4>
            <i class="ti-arrow-up f-14 text-primary f-w-600"></i>
            <span class="f-14 f-w-600">Net Pay</span>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
            <i class="fas fa-calendar-alt f-24 text-success"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Attendance<span class="f-8">(<?php echo date('M Y'); ?>)</span></span>
            <h4 class="f-30 f-w-600"><?php echo $data['attendance_percentage']; ?>%</h4>
            <span class="f-14 f-w-600"><?php echo $data['present_days']; ?> Present/<?php echo $data['working_days']; ?> Absent</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card my-leave-requests">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">My Leave Requests</h3>
          <a href="" class="text-primary f-12 f-w-600">View All</a>
        </div>
        <div class="table-responsive mt-3 height-300">
          <table class="table table-borderless table-sm align-middle pedding-approvals" role="presentation">
            <?php if(count((array)$data['my_leave_requests'])>0){
                foreach ((array)$data['my_leave_requests'] as $leave) { ?>
                  <tr>
                    <td>
                      <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center m-r-5">
                        <i class="fas fa-calendar-alt f-16 text-primary f-w-600"></i>
                      </span>
                    </td>
                    <td class="f-12 f-w-600"><?php echo $leave['leave_type_name']; ?>
                      <p class="f-12 text-muted m-0"><?php echo date('d M Y', strtotime($leave['leave_from'])) ?> to <?php echo date('d M Y', strtotime($leave['leave_to'])) ?></p>
                    </td>
                    <td style="text-align: right;">
                      <span class="form-label label label-success">Approved</span>
                    </td>
                  </tr>

               <?php }

            }else{ ?>

                <tr>
                      <td class="text-center text-muted">No  data found</td>
                    </tr>
           <?php } ?>


          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card my-tasks">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">My Tasks</h3>

        </div>
        <div class="my-tasks height-300">
          <?php if(count((array)$data['upcoming_events'])>0){
                foreach ((array)$data['upcoming_events'] as $event) { ?>
                  <div class="mt-3 border-bottom pb-3">
                    <div class="d-flex">
                      <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center m-r-15">
                        <i class="fa fa-check-square f-16 text-primary f-w-600"></i>
                      </span>
                      <div class="d-inline-block">
                        <h6 class="f-14 f-w-600 m-0">Submit Monthly Report</h6>
                        <p class="f-12 text-muted m-0">Due on 12 May 20XX</p>
                      </div>
                    </div>
                  </div>
          <?php } ?>
          <?php } else { ?>
            <div class="mt-3  pb-3">
              No Events data found
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card upcoming-events">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Upcoming Events</h3>
          <a href="" class="text-primary f-12 f-w-600">View Calendar</a>
        </div>
        <div class="my-tasks height-300">
           <?php if(count((array)$data['upcoming_events'])>0){
                foreach ((array)$data['upcoming_events'] as $event) { ?>
                  <div class="mt-3 border-bottom pb-3">
                    <div class="d-flex">
                      <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center m-r-15">
                        <i class="fa fa-check-square f-16 text-primary f-w-600"></i>
                      </span>
                      <div class="d-inline-block">
                        <h6 class="f-14 f-w-600 m-0">Submit Monthly Report</h6>
                        <p class="f-12 text-muted m-0">Due on 12 May 20XX</p>
                      </div>
                    </div>
                  </div>
          <?php } ?>
          <?php } else { ?>
            <div class="mt-3  pb-3">
              No Tasks data found
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Quick Actions</h3>
        </div>
        <div class="quick-action-box text-center">
          <a href="index.php?option=COM_INSERT_LEAVE_REQUEST">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-green-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="fas fa-calendar-alt f-20 text-success"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">Apply Leave</h6>
          </div>
          </a>
          <a href="index.php?option=COM_INSERT_REQUEST_WORK_FROM_HOME">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-green-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="far fa-clock f-20 text-success"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">Work From Home</h6>
          </div>
          </a>
          <a href="index.php?option=COM_INSERT_EMPLOYEE_RECOGNITION">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-yellow-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="fa fa-briefcase f-20 text-warning"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">Recognition</h6>
          </div>
        </a>
          <a href="index.php?option=COM_INSERT_PAYSLIP">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="fa fa-download f-20 text-purple"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">Download Payslip</h6>
          </div>
         </a>
          <a href="index.php?option=COM_VIEW_PROFILE_APPLICATION_USERS">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-red-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="fa fa-user f-20 text-danger"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">Update Profile</h6>
          </div>
          </a>
          <a href="index.php?option=COM_INSERT_HR_SERVICES">
          <div class="action-box border b-radius-20 p-2">
            <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center d-inline-block">
              <i class="far fa-file-alt f-20 text-primary"></i>
            </span>
            <h6 class="f-14 f-w-600 mt-2">HR Request</h6>
          </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card my-payslips">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">My Payslips</h3>

        </div>
        <div class="table-responsive mt-3 height-230">
          <table class="table table-sm align-middle pedding-approvals f-12">
            <tr>
              <th scope="col">Month</th>
              <th scope="col">Net Pay</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-end">Action</th>
            </tr>
            <?php if(count((array)$data['payslips'])>0){

                foreach ((array)$data['payslips'] as $payslip) { ?>

            <tr>
              <td><?php echo date('F', mktime(0, 0, 0, $payslip['pay_month'], 1))." ".$payslip['pay_year'] ;   ?></td>
              <td><?php echo customeummarmonyformateifinecheck($payslip['net_payable']); ?></td>
              <td><span class="form-label label label-success">Paid</span></td>
              <td style="text-align: right;" class="text-primary"><a href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE3&employe_id=<?php echo $se_emp_id; ?>&month=<?php echo $payslip['pay_month']; ?>&year=<?php echo $payslip['pay_year'] ; ?>">  View </a></td>
            </tr>

            <?php } ?>
          <?php } else { ?>
            <tr > <td colspan="5">
              No  data found </td>
            </tr>
          <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card my-requests">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">My Requests</h3>

        </div>
        <div class="table-responsive mt-3 height-230">
          <table class="table table-sm align-middle pedding-approvals f-12">
            <tr>
              <th scope="col">Request Type</th>
              <th scope="col">Date</th>
              <th scope="col">Status</th>
            </tr>

            <?php if(count((array)$data['my_tasks_list'])>0){

              foreach ((array)$data['my_tasks_list'] as $my_tasks) { ?>
                <tr>
              <td><?php echo ucwords(str_replace('_', ' ', $my_tasks['transaction_table'])); ?></td>
              <td><?php echo date('d M Y', strtotime($my_tasks['request_date']));   ?></td>
              <td><span class="form-label label label-warning">Pending</span></td>
            </tr>


            <?php } ?>
          <?php } else { ?>
            <tr > <td colspan="5">
              No  data found </td>
            </tr>
          <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Announcements</h3>

        </div>
        <div class="table-responsive m-t-5 mb-2 height-230">
          <table class="table table-borderless table-sm align-middle pedding-approvals f-12">

              <?php if(count((array)$data['announcements'])>0){

              foreach ((array)$data['announcements'] as $announcement) { ?>
                <tr>
              <td>
                <span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center me-3">
                  <i class="fa fa-volume-up f-16 text-primary f-w-600"></i>
                </span>
              </td>
              <td>
                <h6 class="f-w-600 f-12 mb-0"><?php echo $announcement['announcement_heading']; ?></h6>
                <span class="text-muted"><?php echo $announcement['announcement_message']; ?></span>
              </td>
              <th scope="col"><?php echo date('d M Y', strtotime($announcement['start_date']));   ?></th>
            </tr>

             <?php } ?>
             <?php } else { ?>
            <tr > <td colspan="3">
              No  data found </td>
            </tr>
          <?php } ?>


          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7 col-md-12 col-sm-12 col-12">
    <div class="card company-directory">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Company Directory</h3>

        </div>
        <div class="company-directory-box mt-3 mb-3">
          <div class="border p-2 b-radius-10">
            <div class="d-block text-end">
              <a href="" class="text-primary f-14 m-r-10"><i class="fa fa-phone f-w-600"></i></a>
              <a href="" class="text-primary f-14"><i class="fa fa-envelope"></i></a>
            </div>
            <img src="uploads/all/1.jpg" alt="" class="img-radius img-50 align-top m-r-5">
            <div class="d-inline-block mb-2">
              <h6 class="f-14 f-w-600 m-0"><?php echo $data['company_directory']['0']['name']; ?></h6>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['0']['designation_name']; ?></p>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['0']['email_id']; ?></p>
            </div>
          </div>
          <div class="border p-2 b-radius-10">
            <div class="d-block text-end">
              <a href="" class="text-primary f-14 m-r-10"><i class="fa fa-phone f-w-600"></i></a>
              <a href="" class="text-primary f-14"><i class="fa fa-envelope"></i></a>
            </div>
            <img src="uploads/all/1.jpg" alt="" class="img-radius img-50 align-top m-r-5">
            <div class="d-inline-block mb-2">
              <h6 class="f-14 f-w-600 m-0"><?php echo $data['company_directory']['1']['name']; ?></h6>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['1']['designation_name']; ?></p>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['1']['email_id']; ?></p>
            </div>
          </div>
          <div class="border p-2 b-radius-10">
            <div class="d-block text-end">
              <a href="" class="text-primary f-14 m-r-10"><i class="fa fa-phone f-w-600"></i></a>
              <a href="" class="text-primary f-14"><i class="fa fa-envelope"></i></a>
            </div>
            <img src="uploads/all/1.jpg" alt="" class="img-radius img-50 align-top m-r-5">
            <div class="d-inline-block mb-2">
              <h6 class="f-14 f-w-600 m-0"><?php echo $data['company_directory']['2']['name']; ?></h6>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['2']['designation_name']; ?></p>
              <p class="f-12 text-muted m-0"><?php echo $data['company_directory']['2']['email_id']; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5 col-md-12 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Important Links</h3>

        </div>
        <div class="mt-3">
          <div class="important-links">
            <?php foreach ($data['company_links'] as $company_link) {
                $link = $company_link['process_file']!="" ? "uploads/all/".$company_link['process_file']: ($company_link['document_url']!="" ? $company_link['document_url'] : "#");
              ?>
            <div class="text-center">
              <a target="_blank" href="<?php echo $link; ?>">
              <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center d-inline-block">
                <i class="far fa-file-alt f-20 text-primary"></i>
              </span>
              <h6 class="f-14 f-w-600 mt-2"><?php echo $company_link['document_name'];  ?></h6>
              </a>
            </div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>

</div>
