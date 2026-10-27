
<?php

$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['page']) && $_POST['page'] == 'updateApprovalStatus'){
        $url = API."?mode=updateapprovalstatus&method=post";
        $_POST['updated_by'] = $_SESSION['userid'];
    $_POST['updated_date'] = date('Y-m-d H:i:s');
       $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1, 1);
    if ($res) {
        header("location:index.php?option=E_EXIT_CONTRACTOR_EMPLOYEE_DASHBOARD");
    }
}

$return = array();
$url = API . "?mode=geteexitcontrantractoremployeedata". "&role_id=" . $_SESSION['role_id'];
$che = callAPI($url);
$return = json_decode($che, 1);

$active_emp_res = $return['active_emp_res'] ?? array();
$emp_resign_approval_peding_re = $return['emp_resign_approval_peding_re'] ?? array();
$resigned_emp_res = $return['resigned_emp_res'] ?? array();
$clearance_process_res = $return['clearance_process_res'] ?? array();
$approval_completed_res = $return['approval_completed_res'] ?? array();
$approval_pending_res = $return['approval_pending_res'] ?? array();

?>

<style>
table td, table th {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 180px; /* adjust as per your design */
}
</style>

<div class="page-body">
    <!-- [ page content ] start -->
    <div class="row">

                <!-- product profit start -->
        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card card-blue">
                <div class="card-body">
                    <div class="row align-items-center m-b-20">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Active</h6>
                            <h3 class="m-b-0 f-w-700 text-white pt-2"><?php echo count((array)$active_emp_res); ?> </h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-user text-c-blue f-18"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card card-yellow">
                <div class="card-body">
                    <div class="row align-items-center m-b-20">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Pending</h6>
                            <h3 class="m-b-0 f-w-700 text-white pt-2"><?php echo count((array)$emp_resign_approval_peding_re); ?> </h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-hourglass-half text-c-yellow f-18"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card card-red">
                <div class="card-body">
                    <div class="row align-items-center m-b-20">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Exited</h6>
                            <h3 class="m-b-0 f-w-700 text-white pt-2"><?php echo count((array)$resigned_emp_res); ?> </h3>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-upload text-c-red f-18" style="transform:rotate(90deg);"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card card-yellow">

            </div>
        </div>
        <!-- =====================================================
     APPROVAL PENDING
        ===================================================== -->
        <div class="col-xl-6 col-md-12">
          <div class="card latest-update-card">
            <div class="card-header">
              <h5>Approval Pending
                <span class="form-label label label-info">
                  <?php echo count((array)$approval_pending_res); ?>
                </span>
              </h5>
              <div class="card-header-right">
                <div class="form-group">
                  <div class="input-group">
                    <input aria-label="Search..." type="text" id="approval_pending_search" class="form-control" placeholder="Search...">
                    <span class="input-group-text addon-style mt-0" id="basic-addon3">
                      <span class="fa fa-search"></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-block table-border-style custom-scroll" style="height: 500px">
              <div class="table-responsive">
                <table class="table table-hover nowrap">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Name</th>
                      <th scope="col">Resign Date</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody id="approval_pending_table_body">
                    <?php if(count((array)$approval_pending_res)>0){
                      $i=1; foreach ($approval_pending_res as $row){ ?>
                    <tr>
                      <th scope="col"><?php echo $i++; ?></th>
                      <td><?php echo htmlspecialchars($row['display_name']); ?><p class="mb-0 f-12"><?php echo $row['emp_code'] ?? ''; ?></p></td>
                      <td><?php echo !empty($row['resignation_date']) ? date('d-m-Y', strtotime($row['resignation_date'])) : '-'; ?></td>
                      <td>
                              <button onclick='approvalOrReject("<?php echo $row['tranaction_approval_id']; ?>", "<?php echo $row['doa_id']; ?>", 199, "APPROVAL")'
                                class="btn btn-success btn-sm rounded-3 " >
                                 Approve
                              </button>
                              <button  onclick='approvalOrReject("<?php echo $row['tranaction_approval_id']; ?>", "<?php echo $row['doa_id']; ?>", 201, "APPROVAL")' class="btn btn-danger btn-sm rounded-3 " >
                               Reject
                              </button>
                            </td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr><td colspan="4" class="text-center text-muted">No Records Found</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- =====================================================
             APPROVAL COMPLETED
        ===================================================== -->
        <div class="col-xl-6 col-md-12">
          <div class="card latest-update-card">
            <div class="card-header">
              <h5>Approval Completed
                <span class="form-label label label-info">
                  <?php echo count((array)$approval_completed_res); ?>
                </span>
              </h5>
              <div class="card-header-right">
                <div class="form-group">
                  <div class="input-group">
                    <input aria-label="Search..." type="text" id="approval_completed_search" class="form-control" placeholder="Search...">
                    <span class="input-group-text addon-style mt-0" id="basic-addon3_2">
                      <span class="fa fa-search"></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-block table-border-style custom-scroll" style="height: 500px">
              <div class="table-responsive">
                <table class="table table-hover nowrap">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Name</th>
                      <th scope="col">Resign Date</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody id="approval_completed_table_body">
                    <?php if(count((array)$approval_completed_res)>0){
                      $i=1; foreach ($approval_completed_res as $row){ ?>
                    <tr>
                      <th scope="col"><?php echo $i++; ?></th>
                      <td class="text-primary cursor-pointer" onclick='approvalOrReject("<?php echo $row['tranaction_approval_id']; ?>", "<?php echo $row['doa_id']; ?>", "1", "COMPLETED")' role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo htmlspecialchars($row['display_name']); ?><p class="mb-0 f-12"><?php echo $row['emp_code'] ?? ''; ?></p></td>
                      <td><?php echo !empty($row['resignation_date']) ? date('d-m-Y', strtotime($row['resignation_date'])) : '-'; ?>
                      </td>
                      <?php
                                      $encoded = deocdeId("id:" . $row['emp_id']);
                                    ?>
                                    <td class="text-center">
                                      <a
                                        href="index.php?option=IDC_NDC_EXIT_FORM&Rid=<?php echo $encoded; ?>"
                                        target="_blank"
                                        class="btn btn-light btn-sm"
                                        title="View No Due Clearance Form"
                                      >
                                        <i class="fa fa-file-pdf text-danger"></i>
                                      </a>
                                    </td>
                      </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr><td colspan="4" class="text-center text-muted">No Records Found</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- product profit end -->


        <div class="col-xl-6 col-md-12">
            <div class="card latest-update-card">
                <div class="card-header">
                    <h5>Active Employees <span class="form-label label label-info"><?php echo count((array)$active_emp_res); ?></span></h5>
                    <div class="card-header-right">
                        <div class="form-group">
                            <div class="input-group">
                                <input aria-label="Search..." type="text" id="active_search" class="form-control" placeholder="Search...">
                                <span class="input-group-text addon-style mt-0" id="basic-addon3_3">
                                    <span class="fa fa-search"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-block table-border-style custom-scroll" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" >
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="active_table_body">
                                <?php if(count((array)$active_emp_res)>0){
                                    $i = 1;
                                    foreach ($active_emp_res as $row) { ?>
                                <tr>
                                    <th scope="row"><?php echo $i; ?> </th>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['display_name']; ?>"  ><?php echo $row['display_name'] ?? ''; ?>
                                        <p class="mb-0 f-12"><?php echo $row['emp_code'] ?? '-'; ?></p>
                                    </td>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['email_id']; ?>" ><?php echo $row['email_id'] ?? '-'; ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm rounded-3 exit-btn" data-id="<?php echo $row['id']; ?>">E-exit</button>

                                    </td>
                                </tr>
                                <?php $i++;
}
                                 } else{?>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <p class="text-muted f-16 fw-bold m-b-30">
                                                <i class="fa fa-warning"></i> No Records
                                            </p>
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                 <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card new-cust-card">
                <div class="card-header">
                    <h5>Pending <span class="form-label label label-info"><?php echo count((array)$emp_resign_approval_peding_re); ?></span></h5>
                    <div class="card-header-right">
                        <div class="form-group">
                            <div class="input-group">
                                <input aria-label="Search..." type="text" id="pending_search" class="form-control" placeholder="Search...">
                                <span class="input-group-text addon-style mt-0" id="basic-addon3_4">
                                    <span class="fa fa-search"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-block table-border-style custom-scroll" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pending_table_body">
                                <?php if(count((array)$emp_resign_approval_peding_re) > 0){
                                    $i = 1;
                                    foreach ($emp_resign_approval_peding_re as $row) { ?>
                                <tr>
                                    <th scope="row"><?php echo $i; ?></th>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['display_name']; ?>" ><?php echo $row['display_name'] ?? '-'; ?>
                                        <p class="mb-0 f-12"><?php echo $row['emp_code'] ?? ''; ?></p>
                                    </td>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['email_id']; ?>" ><?php echo $row['email_id'] ?? '-'; ?></td>
                                    <td>
  <?php
    $roles = [
      1 => 'Admin',
      2 => 'IT Admin',
      3 => 'IT Team'
    ];

    foreach ($roles as $id => $label) {
        $approved = false;
        if ($id == 1 && $row['admin_approved'] > 0) { $approved = true;
}
        if ($id == 2 && $row['it_admin_approved'] > 0) { $approved = true;
}
        if ($id == 3 && $row['it_team_approved'] > 0) { $approved = true;
}

        if ($approved) {
            echo '<span class="form-label label label-success">
                    <i class="fa fa-check m-r-5"></i>'.$label.'
                  </span><br>';
        } else {
            echo '<span class="form-label label label-danger">'.$label.'</span><br>';
        }
    }
  ?>

</td>
                        <?php
                              $encoded = deocdeId("id:" . $row['emp_id']);
                            ?>
                            <td class="text-center">
                              <a
                                href="index.php?option=IDC_NDC_EXIT_FORM&Rid=<?php echo $encoded; ?>"
                                target="_blank"
                                class="btn btn-light btn-sm"
                                title="View No Due Clearance Form"
                              >
                                <i class="fa fa-file-pdf text-danger"></i>
                              </a>
                            </td>

                                </tr>
                                <?php $i++;
}
                                 } else { ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <p class="text-muted f-16 fw-bold m-b-30">
                                                <i class="fa fa-warning"></i> No Records
                                            </p>
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12">
            <div class="card new-cust-card">
                <div class="card-header">
                    <h5>Exited <span class="form-label label label-info"><?php echo count((array)$resigned_emp_res); ?></span></h5>
                    <div class="card-header-right">
                        <div class="form-group">
                            <div class="input-group">
                                <input aria-label="Search..." type="text" id="terminated_search" class="form-control" placeholder="Search...">
                                <span class="input-group-text addon-style mt-0" id="basic-addon3_5">
                                    <span class="fa fa-search"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-block table-border-style custom-scroll" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table " >
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="terminated_table_body">
                                <?php if(count((array)$resigned_emp_res) > 0){
                                    $i = 1;
                                    foreach ($resigned_emp_res as $row) { ?>
                                <tr>
                                    <th scope="row"><?php echo $i; ?></th>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['display_name']; ?>" ><?php echo $row['display_name'] ?? '-'; ?>
                                        <p class="mb-0 f-12"><?php echo $row['emp_code'] ?? ''; ?></p>
                                    </td>
                                    <td data-toggle="tooltip" data-placement="top"  data-bs-original-title="<?php echo $row['email_id']; ?>" ><?php echo $row['email_id'] ?? '-'; ?></td>
                                    <td>
                                        <?php echo !empty($row['last_working_date']) ? date('d-m-Y', strtotime($row['last_working_date'])) : '-'; ?>
                                    </td>
                                    <?php
                              $encoded = deocdeId("id:" . $row['emp_id']);
                            ?>
                            <td class="text-center">
                              <a
                                href="index.php?option=IDC_NDC_EXIT_FORM&Rid=<?php echo $encoded; ?>"
                                target="_blank"
                                class="btn btn-light btn-sm"
                                title="View No Due Clearance Form"
                              >
                                <i class="fa fa-file-pdf text-danger"></i>
                              </a>
                            </td>
                                </tr>
                                <?php $i++;
}
                                 } else { ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <p class="text-muted f-16 fw-bold m-b-30">
                                                <i class="fa fa-warning"></i> No Records
                                            </p>
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </div>
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
    <!-- [ page content ] end -->
</div>
<!-- E-Exit Modal -->
<div class="modal fade" id="exitModal" tabindex="-1" aria-labelledby="exitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="exitModalLabel">Employee E-Exit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="exitForm">
          <input type="hidden" id="emp_id" name="emp_id">

          <div class="mb-3 w-full">
            <label for="termination_type" class="form-label">Clearance Process Type</label>
                    <select class="form-select js-example-basic-single" id="termination_type" name="termination_type" required>
                        <option value="">Select Clearance Process</option>
                        <?php foreach ($clearance_process_res as $loc){ ?>
                        <option value="<?= $loc['genc_detail_code'] ?>" ><?= htmlspecialchars($loc['genc_detail_name']) ?></option>
                        <?php } ?>
                    </select>
          </div>

          <div class="mb-3">
            <label for="resign_date" class="form-label">Resigning Date</label>
            <input type="date" class="form-control" id="resign_date" name="resign_date" required>
          </div>

          <div class="mb-3">
            <label for="last_working_date" class="form-label">Last Working Date</label>
            <input type="date" class="form-control" id="last_working_date" name="last_working_date" required>
          </div>

          <div class="mb-3">
            <label for="remarks" class="form-label">Reason / Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter reason..." required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="saveExit" class="btn btn-primary">Save</button>
      </div>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>

<script>
    function approvalOrReject(transaction_approval_id,doa_id,status,type){
    $.ajax({
        url: 'views/e_exit_contractor_employee_dashboard_popup.php',
        type: 'POST',
        data: {
            transaction_approval_id: transaction_approval_id,
            doa_id: doa_id,
            status: status,
            type: type
        },
        success: function(response) {
            $('#popup1').html(response);
            $('#kt_modal_KTDatatable_remote').modal("show");

        },
        error: function(xhr) {
            alert('Failed to load data');
        }
    });
    }
$(document).ready(function() {

  $(document).on('click', '.exit-btn', function() {
    var empId = $(this).data('id');
    $('#emp_id').val(empId);
    $('#exitModal').modal('show');
  });

  // Handle Save click
  $('#saveExit').on('click', function() {
    const emp_id = $('#emp_id').val();
    const resign_date = $('#resign_date').val();
    const last_working_date = $('#last_working_date').val();
    const remarks = $('#remarks').val();
    const termination_type = $('#termination_type').val();

    if (!termination_type || !resign_date || !last_working_date || !remarks) {
      Swal.fire('Missing fields', 'Please fill all fields before saving.', 'warning');
      return;
    }

    Swal.fire({
      title: 'Confirm E-Exit?',
      text: "Are you sure you want to proceed with employee exit?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Confirm',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#d33'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'ajax.php', // your backend API endpoint
          type: 'POST',
          data: {
              'page': 'saveemployeeexit',
            'termination_type': termination_type,
            'emp_id': emp_id,
            'resign_date': resign_date,
            'last_working_date': last_working_date,
            'remarks': remarks
          },
          success: function(response) {
            try {
              const result = JSON.parse(response);
              var res = result.res;

              if (res.status == 'sucess') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Employee exit saved successfully!',
                    allowOutsideClick: false,   //  prevent closing by outside click
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire('Error', res.message || 'Something went wrong.', 'error');
              }
            } catch (e) {
              Swal.fire('Error', 'Invalid response from server.', 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Unable to connect to server.', 'error');
          }
        });
      }
    });
  });

});
</script>
<script>
$(document).ready(function(){

  $('#active_search').on('keyup', function(e) {
    const search = $(this).val().trim();

    if (e.keyCode === 13 || search.length >= 2 || search.length === 0) {
      $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
        'page': 'geteexitcontrantractoremployeedata',
        'status': 'ACTIVE',
        'search': search
        },
        beforeSend: function() {
          $('#active_table_body').html('<tr><td colspan="4" class="text-center text-muted">Searching...</td></tr>');
        },
        success: function(response) {
          try {
            const res = JSON.parse(response);
            const data = res.active_emp_res || [];
            let html = '';

            if (data.length > 0) {
              let i = 1;
              data.forEach(row => {
                html += `
                  <tr>
                    <th scope="row">${i++}</th>
                    <td>${row.display_name ?? '-'}
                      <p class="mb-0 f-12">${row.emp_code ?? ''}</p>
                    </td>
                    <td>${row.email_id ?? '-'}</td>
                    <td>
                      <button class="btn btn-danger btn-sm rounded-3 exit-btn" data-id="${row.id}">E-exit</button>
                    </td>
                  </tr>`;
              });
              $('#active_table_body').html(html);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Records Found',
                    text: `No employee found with code "${search}". Do you want to add a new employee?`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Add Employee',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // DYNAMIC REDIRECT WITH SEARCH CODE
                        window.location.href =
                          `index.php?option=COM_VIEW_VMS_EMPLOYEE&ECD=${encodeURIComponent(search)}`;
                    }
                });

                $('#active_table_body').html(`
                  <tr><td colspan="4" class="text-center text-muted">No Records</td></tr>
                `);
            }


          } catch (err) {
            $('#active_table_body').html('<tr><td colspan="4" class="text-center text-danger">Invalid server response</td></tr>');
          }
        },
        error: function() {
          $('#active_table_body').html('<tr><td colspan="4" class="text-center text-danger">Server Error</td></tr>');
        }
      });
    }
  });

//  Search - Clearance Pending Employees
  $('#pending_search').on('keyup', function(e) {
    const search = $(this).val().trim();
    if (e.keyCode === 13 || search.length >= 2 || search.length === 0) {

      $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
          'page': 'geteexitcontrantractoremployeedata',
          'status': 'PENDING',
          'search': search
        },
        beforeSend: function() {
          $('#pending_table_body').html('<tr><td colspan="4" class="text-center text-muted">Searching...</td></tr>');
        },
        success: function(response) {
          try {
            const res = JSON.parse(response);
            const data = res.emp_resign_approval_peding_re || [];
            let html = '';

            if (data.length > 0) {
              let i = 1;
              data.forEach(row => {
                html += `
                  <tr>
                    <th scope="row">${i++}</th>
                    <td>${row.display_name ?? '-'}
                      <p class="mb-0 f-12">${row.emp_code ?? ''}</p>
                    </td>
                    <td>${row.email_id ?? '-'}</td>
                    <td>
                      <span class="form-label label label-warning">
                        <i class="fa fa-hourglass-half m-r-5"></i> Pending
                      </span>
                      <br>
                      <span class="form-label label label-info">
                        Last Working: ${row.last_working_date ? formatDate(row.last_working_date) : '-'}
                      </span>
                    </td>
                  </tr>`;
              });
            } else {
              html = noRecordsRow();
            }

            $('#pending_table_body').html(html);

          } catch (err) {
            $('#pending_table_body').html('<tr><td colspan="4" class="text-center text-danger">Invalid server response</td></tr>');
          }
        },
        error: function() {
          $('#pending_table_body').html('<tr><td colspan="4" class="text-center text-danger">Server Error</td></tr>');
        }
      });
    }
  });

  //  Search - Terminated Employees
  $('#terminated_search').on('keyup', function(e) {
    const search = $(this).val().trim();
    if (e.keyCode === 13 || search.length >= 2 || search.length === 0) {

      $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
          'page': 'geteexitcontrantractoremployeedata',
          'status': 'APPROVED',
          'search': search
        },
        beforeSend: function() {
          $('#terminated_table_body').html('<tr><td colspan="4" class="text-center text-muted">Searching...</td></tr>');
        },
        success: function(response) {
          try {
            const res = JSON.parse(response);
            const data = res.resigned_emp_res || [];
            let html = '';

            if (data.length > 0) {
              let i = 1;
              data.forEach(row => {
                html += `
                  <tr>
                    <th scope="row">${i++}</th>
                    <td>${row.display_name ?? '-'}
                      <p class="mb-0 f-12">${row.emp_code ?? ''}</p>
                    </td>
                    <td>${row.email_id ?? '-'}</td>
                    <td>${row.last_working_date ? formatDate(row.last_working_date) : '-'}</td>
                  </tr>`;
              });
            } else {
              html = noRecordsRow();
            }

            $('#terminated_table_body').html(html);

          } catch (err) {
            $('#terminated_table_body').html('<tr><td colspan="4" class="text-center text-danger">Invalid server response</td></tr>');
          }
        },
        error: function() {
          $('#terminated_table_body').html('<tr><td colspan="4" class="text-center text-danger">Server Error</td></tr>');
        }
      });
    }

});
});

  function noRecordsRow() {
    return `
      <tr>
        <td colspan="5">
          <div class="text-center">
            <p class="text-muted f-16 fw-bold m-b-30">
              <i class="fa fa-warning"></i> No Records Found
            </p>
            <div class="text-center px-4">
              <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
          </div>
        </td>
      </tr>`;
  }

  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}-${month}-${year}`;
  }

</script>

