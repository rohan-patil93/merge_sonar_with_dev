<?php
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['save'])) {
    $result = array();
  $table_name = 'exit_clearance_assignments';
  $assign_types = ['admin','it_admin','it_team','final'];
  $rid = 0;
  $is_active = 'is_active';
  // Step 1: mark all old rows inactive
  $updateAll = [
    'is_active'    => 1,
    'updated_by'   => $_SESSION['userid'],
    'updated_date' => date('Y-m-d H:i:s')
  ];
  $url = API . "?mode=updaterowtabledata&table=$table_name&method=post&rid=$rid&fid=$is_active";
  $result['update'] = file_post_contents($url, $updateAll, 'update');

  foreach ($assign_types as $type) {
    $locs = $_POST[$type.'_location_id'] ?? [];
    $emps = $_POST[$type.'_employee_id'] ?? [];
    $ids  = $_POST[$type.'_record_id'] ?? []; // hidden inputs per row

    for ($i=0; $i<count($locs); $i++) {
      if (!empty($locs[$i]) && !empty($emps[$i])) {
        $data = [
          'assign_type'    => $type,
          'location_id'    => intval($locs[$i]),
          'employee_id'    => intval($emps[$i]),
          'company_id'     => $_SESSION['company_id'],
          'current_status' => 0,
          'is_active'      => 0,
          'status_date'    => date('Y-m-d H:i:s')
        ];

        if (!empty($ids[$i])) {
          $data['updated_by'] = $_SESSION['userid'];
          $data['updated_date'] = date('Y-m-d H:i:s');
          $url = API . "?mode=updaterowtabledata&rid={$ids[$i]
}&table=$table_name&method=post";
          $result[$type][] = file_post_contents($url, $data, 'update');
        } else {
          $data['created_by'] = $_SESSION['userid'];
          $data['created_date'] = date('Y-m-d H:i:s');
          $url = API . "?mode=adddata&table=$table_name&method=post";
          $result[$type][] = file_post_contents($url, $data, 'insert');
        }
      }
    }
  }

}

$rid = 1;
$url = API . "?mode=getworkflowassigndata&rid=" . $rid;
$response = callAPI($url);
$data = json_decode($response, true);
$locations = $data['locations'] ?? [];
$return = $data['data'] ?? [];

$admin_data    = $return['admin'] ?? array();
$it_admin_data = $return['it_admin'] ?? array();
$it_team_data  = $return['it_team'] ?? array();
$final_data    = $return['final'] ?? array();

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
<?php if ($error_message != '') { ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<form id="assignForm" method="POST">
    <input type="hidden" name="record_id" value="<?php echo $return['id'] ?? ''; ?>">
    <input type="hidden" name="save" value="<?php echo !empty($return) ? 'update' : 'insert'; ?>">
    <div class="row">
        <!-- =====================================================
         ADMIN
         ===================================================== -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>ADMIN</b>
                    <button type="button" class="btn btn-success btn-sm addRowBtn" data-section="admin"><i class="fa fa-plus"></i> Row</button>
                </div>
                <div class="card-body">
                    <div class="rows-wrap" id="admin_wrap">
                        <?php if (count((array)$admin_data)>0) {
                            foreach ($admin_data as $r) { ?>
                                <input type="hidden" name="admin_record_id[]" value="<?php echo $r['id'] ?? ''; ?>">

                                <div class="row mb-3 align-items-end one-row">
                                    <div class="col-6">
                                        <span>Location</span>
                                        <select aria-label="Admin Location []" class="form-select js-example-basic-single loc" name="admin_location_id[]" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $loc) { ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($r['location_id'] == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['location_full_name']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="99" <?php echo ($r['location_id'] == '99') ? 'selected' : ''; ?>> ALL</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <span>Employee</span>
                                        <select aria-label="Admin Employee []" class="form-select js-example-basic-single emp" name="admin_employee_id[]" required>
                                            <option value="<?php echo $r['employee_id']; ?>">Loading...</option>
                                        </select>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="row mb-3 align-items-end one-row">
                                <input type="hidden" name="admin_record_id[]" >

                                <div class="col-6">
                                    <span>Location</span>
                                    <select aria-label="Admin Location []" class="form-select js-example-basic-single loc" name="admin_location_id[]" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $loc) { ?>
                                            <option value="<?php echo $loc['id']; ?>"><?php echo $loc['location_full_name']; ?></option>
                                        <?php } ?>
                                        <option value="99" >ALL</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <span>Employee</span>
                                    <select aria-label="Admin Employee []" class="form-select js-example-basic-single emp" name="admin_employee_id[]" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
         IT ADMIN
         ===================================================== -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>IT ADMIN</b>
                    <button type="button" class="btn btn-success btn-sm addRowBtn" data-section="it_admin"><i class="fa fa-plus"></i> Row</button>
                </div>
                <div class="card-body">
                    <div class="rows-wrap" id="it_admin_wrap">
                        <?php if (count((array)$it_admin_data)>0) {
                            foreach ($it_admin_data as $r) { ?>
                                <input type="hidden" name="it_admin_record_id[]" value="<?php echo $r['id'] ?? ''; ?>">
                                <div class="row mb-3 align-items-end one-row">
                                    <div class="col-6">
                                        <span>Location</span>
                                        <select aria-label="It Admin Location []" class="form-select js-example-basic-single loc" name="it_admin_location_id[]" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $loc) { ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($r['location_id'] == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['location_full_name']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="99" <?php echo ($r['location_id'] == '99') ? 'selected' : ''; ?>>ALL</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <span>Employee</span>
                                        <select aria-label="It Admin Employee []" class="form-select js-example-basic-single emp" name="it_admin_employee_id[]" required>
                                            <option value="<?php echo $r['employee_id']; ?>">Loading...</option>
                                        </select>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <input type="hidden" name="it_admin_record_id[]" >
                            <div class="row mb-3 align-items-end one-row">
                                <div class="col-6">
                                    <span>Location</span>
                                    <select aria-label="It Admin Location []" class="form-select js-example-basic-single loc" name="it_admin_location_id[]" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $loc) { ?>
                                            <option value="<?php echo $loc['id']; ?>"><?php echo $loc['location_full_name']; ?></option>
                                        <?php } ?>
                                        <option value="99">ALL</option>
                                    </select>

                                </div>
                                <div class="col-5">
                                    <span>Employee</span>
                                    <select aria-label="It Admin Employee []" class="form-select js-example-basic-single emp" name="it_admin_employee_id[]" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
         IT TEAM
         ===================================================== -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>IT TEAM</b>
                    <button type="button" class="btn btn-success btn-sm addRowBtn" data-section="it_team"><i class="fa fa-plus"></i> Row</button>
                </div>
                <div class="card-body">
                    <div class="rows-wrap" id="it_team_wrap">
                        <?php if (count((array)$it_team_data)>0) {
                            foreach ($it_team_data as $r) { ?>
                                <input type="hidden" name="it_team_record_id[]" value="<?php echo $r['id'] ?? ''; ?>">
                                <div class="row mb-3 align-items-end one-row">
                                    <div class="col-6">
                                        <span>Location</span>
                                        <select aria-label="It Team Location []" class="form-select js-example-basic-single loc" name="it_team_location_id[]" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $loc) { ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($r['location_id'] == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['location_full_name']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="99" <?php echo ($r['location_id'] == '99') ? 'selected' : ''; ?>>ALL</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <span>Employee</span>
                                        <select aria-label="It Team Employee []" class="form-select js-example-basic-single emp" name="it_team_employee_id[]" required>
                                            <option value="<?php echo $r['employee_id']; ?>">Loading...</option>
                                        </select>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <input type="hidden" name="it_team_record_id[]" >
                            <div class="row mb-3 align-items-end one-row">
                                <div class="col-6">
                                    <span>Location</span>
                                    <select aria-label="It Team Location []" class="form-select js-example-basic-single loc" name="it_team_location_id[]" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $loc) { ?>
                                            <option value="<?php echo $loc['id']; ?>"><?php echo $loc['location_full_name']; ?></option>
                                        <?php } ?>
                                        <option value="99" >ALL</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <span>Employee</span>
                                    <select aria-label="It Team Employee []" class="form-select js-example-basic-single emp" name="it_team_employee_id[]" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
         FINAL SUBMISSION
         ===================================================== -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>FINAL SUBMISSION</b>
                    <button type="button" class="btn btn-success btn-sm addRowBtn" data-section="final"><i class="fa fa-plus"></i> Row</button>
                </div>
                <div class="card-body">
                    <div class="rows-wrap" id="final_wrap">
                        <?php if (count((array)$final_data)>0) {
                            foreach ($final_data as $r) { ?>
                                <input type="hidden" name="final_record_id[]" value="<?php echo $r['id'] ?? ''; ?>">
                                <div class="row mb-3 align-items-end one-row">
                                    <div class="col-6">
                                        <span>Location</span>
                                        <select aria-label="Final Location []" class="form-select js-example-basic-single loc" name="final_location_id[]" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $loc) { ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($r['location_id'] == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['location_full_name']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="99" <?php echo ($r['location_id'] == '99') ? 'selected' : ''; ?>>ALL</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <span>Employee</span>
                                        <select aria-label="Final Employee []" class="form-select js-example-basic-single emp" name="final_employee_id[]" required>
                                            <option value="<?php echo $r['employee_id']; ?>">Loading...</option>
                                        </select>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <input type="hidden" name="final_record_id[]">
                            <div class="row mb-3 align-items-end one-row">
                                <div class="col-6">
                                    <span>Location</span>
                                    <select aria-label="Final Location []" class="form-select js-example-basic-single loc" name="final_location_id[]" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $loc) { ?>
                                            <option value="<?php echo $loc['id']; ?>"><?php echo $loc['location_full_name']; ?></option>
                                        <?php } ?>
                                        <option value="99">ALL</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <span>Employee</span>
                                    <select aria-label="Final Employee []" class="form-select js-example-basic-single emp" name="final_employee_id[]" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12 text-left border-top">
            <button type="button" id="saveAssign" class="btn btn-primary px-4">Save</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // remove row
        $(document).on('click', '.removeRowBtn', function() {
            const $wrap = $(this).closest('.rows-wrap');
            if ($wrap.find('.one-row').length > 1) {
                $(this).closest('.one-row').remove();
            } else {
                Swal.fire('Note', 'At least one row is required in this section.', 'info');
            }
        });
        var rowCounters = {
            admin: 1,
            it_admin: 1,
            it_team: 1,
            final: 1
        };
        // Template row generator
        function getRowHtml(section, index) {
            return `
                <input type="hidden" name="${section}_record_id[]" value="">

                <div class="row mb-3 align-items-end one-row" data-row="${index}">
                  <div class="col-6">
                    <span>Location</span>
                    <select aria-label="${section} Location []" class="form-select js-example-basic-single loc" name="${section}_location_id[]" required>
                      <option value="">Select Location</option>
                      <?php foreach ($locations as $loc) { ?>
                        <option value="<?php echo $loc['id']; ?>"><?php echo $loc['location_full_name']; ?></option>
                      <?php } ?>
                      <option value="99">ALL</option>
                    </select>
                  </div>
                  <div class="col-5">
                    <span>Employee</span>
                    <select aria-label="${section} Employee []" class="form-select js-example-basic-single emp" name="${section}_employee_id[]" required>
                      <option value="">Select Employee</option>
                    </select>
                  </div>
                  <div class="col-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm removeRowBtn">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>`;
        }
        // Add-row button handler
        $('.addRowBtn').click(function() {
            const section = $(this).data('section');
            const wrapId = '#' + section + '_wrap';
            rowCounters[section]++;
            $(wrapId).append(getRowHtml(section, rowCounters[section]));
            $('.js-example-basic-single').select2(); // re-initialize select2
        });

        // load employees when location changes
        $(document).on('change', 'select.loc', function() {
            const locId = $(this).val();
            const $row = $(this).closest('.one-row');
            const $emp = $row.find('select.emp');
            if (!locId) {
                $emp.html('<option value="">Select Employee</option>');
                return;
            }
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'getEmployeesByLocation',
                    location_id: locId == '99' ? '' : locId
                },
                beforeSend: function() {
                    $emp.html('<option>Loading...</option>');
                },
                success: function(res) {
                    try {
                        const data = JSON.parse(res) || [];
                        let opts = '<option value="">Select Employee</option>';
                        data.forEach(emp => {
                            opts += `<option value="${emp.id}">${emp.display_name} (${emp.emp_code||'-'})</option>`;
                        });
                        $emp.html(opts);
                    } catch (e) {
                        $emp.html('<option>Error</option>');
                    }
                }
            });
        });
        $('select.loc').each(function() {
            const locId = $(this).val();
            const $row = $(this).closest('.one-row');
            const $emp = $row.find('select.emp');
            const selectedEmp = $emp.find('option:selected').val();
            if (locId) {
                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: {
                        page: 'getEmployeesByLocation',
                        location_id: locId
                    },
                    success: function(res) {
                        try {
                            const data = JSON.parse(res) || [];
                            let opts = '<option value="">Select Employee</option>';
                            data.forEach(emp => {
                                opts += `<option value="${emp.id}" ${emp.id==selectedEmp?'selected':''}>
                        ${emp.display_name} (${emp.emp_code||'-'})
                      </option>`;
                            });
                            $emp.html(opts);
                        } catch (e) {
                            $emp.html('<option>Error</option>');
                        }
                    }
                });
            }
        });
        // validation before submit
        $('#saveAssign').on('click', function() {
            const sections = ['#admin_wrap', '#it_admin_wrap', '#it_team_wrap', '#final_wrap'];
            let valid = true,
                message = '';
                sections.forEach(id => {
                let hasValid = false;
                $(id).find('.one-row').each(function() {
                    const loc = $(this).find('select.loc').val();
                    const emp = $(this).find('select.emp').val();
                    if (loc && emp) hasValid = true;
                });
                if (!hasValid) {
                    valid = false;
                    message = id.replace('_wrap', '').replace('#', '').toUpperCase() + ' section incomplete.';
                }
            });
            if (!valid) {
                Swal.fire('Validation Error', message, 'warning');
                return false;
            }
            $('#assignForm').submit();
        });
    });
</script>
