<?php

if(isset($_POST['save'])){

  $last_id = '';
  if ($_POST['save'] == 'SAVE') {
    $data['complaint_id'] = $_POST['complaint_id'];
    $data['transaction_date'] = date('Y-m-d');
    $data['remarks'] = $_POST['remarks'];
    $data['user_id'] = $_SESSION['userid'];
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    $data['status_date'] = date('Y-m-d H:i:s');
    $data['company_id'] = $_SESSION['company_id'];
    $data['created_date'] = date('Y-m-d H:i:s');
    $data['created_by'] = $_SESSION['userid'];
    $url = API . "?mode=adddata&table=complaint_responses&method=post";
    $response = file_post_contents($url, $data, 'insert');
    $response = json_decode($response, 1);
        $last_id = $response['LASTID'];
    if ($response["status"] == "sucess") {
    }
  }
    // ===============================
    // 📂 MULTIPLE FILE UPLOAD & SAVE
    // ===============================

    if (!empty($_FILES['response_images']['name'][0])) {
        $transaction_id = $last_id; // last inserted ID from complaint save
        $table_name = 'application_attachments';
        $company_id = $_SESSION['company_id'];
        $user_id = $_SESSION['userid'];
        $upload_path = 'all'; // folder name inside /uploads/

        foreach ($_FILES['response_images']['name'] as $key => $original_name) {
            if (empty($original_name)) { continue;
}
            $_FILES['single_file']['name'] = $_FILES['response_images']['name'][$key];
            $_FILES['single_file']['type'] = $_FILES['response_images']['type'][$key];
            $_FILES['single_file']['tmp_name'] = $_FILES['response_images']['tmp_name'][$key];
            $_FILES['single_file']['error'] = $_FILES['response_images']['error'][$key];
            $_FILES['single_file']['size'] = $_FILES['response_images']['size'][$key];
            // Upload file
            $saved_file = imageupload($upload_path, 'single_file', '', 'complaint_responses');
            $file_size_mb = round($_FILES['single_file']['size'] / (1024 * 1024), 2);
            // Save file info into application_attachments
            $file_data = array(
                'company_id'              => $company_id,
                'application_table_name'  => 'complaint_responses',
                'transaction_id'          => $transaction_id,
                'temp_id'                 => '',
                'file_name'               => $saved_file,
                'actual_file_name'        => $original_name,
                'email_attachement'       => '0',
                'file_size_mb'            => $file_size_mb,
                'is_active'               => '0',
                'current_status'          => '0',
                'status_date'             => date('Y-m-d H:i:s'),
                'created_date'            => date('Y-m-d H:i:s'),
                'created_by'              => $user_id
            );
            $url_attach = API . "?mode=adddata&table=" . $table_name . "&method=post";
            $resp_attach = file_post_contents($url_attach, $file_data, 'insert');
        }
    }

    header("location:index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=".deocdeId('id:' . $_POST['complaint_id']));

}
if ($_GET['Rid']) {
  $rid = encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $rid = explode(':', $rid);
  $rid = $rid[1];

  $url = API . "?mode=getamccomplaintslistdetails&method=get&rid=$rid";
  $che = callAPI($url);
  $result = json_decode($che, true);

  $return = $result['table'];
  $img_result = $result['complaints_images_data'];
}
?>

<div class="page-body">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
      <h4 class="mb-0"><i class="fa fa-tools-alt-2"></i> AMC Complaint View</h4>
      <div>
        <a href="index.php?option=COM_INSERT_AMC_REPAIR_ORDER&complaint_id=<?php echo (int) ($return['id'] ?? $rid); ?>" class="btn btn-warning btn-sm fw-bold me-2">
          <i class="fa fa-wrench"></i> Create Repair Order
        </a>
        <a href="index.php?option=COM_LIST_AMC_COMPLAINTS" class="btn btn-light btn-sm fw-bold me-2">
          <i class="fa fa-list"></i> Complaints List
        </a>
        <a href="javascript:history.go(-1)" class="btn btn-outline-light btn-sm fw-bold">
          <i class="fa fa-reply"></i> Back
        </a>
      </div>
    </div>

    <div class="card-body p-4 bg-light">

      <!-- Complaint Details -->
      <div class="row mb-4">
        <div class="col-12 mb-3">
          <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">
            <i class="fa fa-info-circle"></i> Complaint Details
          </h5>
        </div>

        <div class="col-md-6">
          <div class="detail-box">
            <span class="label">AMC Contract:</span>
            <span class="value"><?php echo htmlspecialchars($return['contract_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">ServiceNow Ref:</span>
            <span class="value"><?php echo htmlspecialchars($return['servicenow_ref'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Vendor:</span>
            <span class="value"><?php echo htmlspecialchars($return['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Complaint Name:</span>
            <span class="value"><?php echo $return['complaint_name']; ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Type:</span>
            <span class="value text-muted"><?php echo $return['complaint_type']; ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Priority:</span>
            <span class="value">
              <?php
              $priority = $return['complaint_priority'];
              $badgeClass = ($priority == 'H') ? 'danger' : (($priority == 'M') ? 'warning' : 'success');
              $label = ($priority == 'H') ? 'High' : (($priority == 'M') ? 'Medium' : 'Low');
              ?>
              <span class="badge bg-<?php echo $badgeClass; ?> px-3 py-1 fw-bold"><?php echo $label; ?></span>
            </span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="detail-box">
            <span class="label">Date:</span>
            <span class="value"><?php echo !empty($return['transaction_date']) ? fromDate($return['transaction_date']) : ''; ?></span>
          </div>
          <div class="detail-box">
            <span class="label">Status:</span>
            <span class="value"><span class="badge bg-primary fw-bold"><?php echo $return['name']; ?></span></span>
          </div>
          <div class="detail-box">
            <span class="label">Complaint User:</span>
            <span class="value"><?php echo $return['display_name'] ?? ''; ?></span>
          </div>
        </div>
      </div>

      <!-- Asset / Location Section -->
      <?php if (!empty($return['item_name']) || !empty($return['asset_code'])) { ?>
        <div class="row mb-4">
          <div class="col-12 mb-3">
            <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">
              <i class="fa fa-building"></i> Asset / Location Details
            </h5>
          </div>

          <?php if (!empty($return['item_name'])) { ?>
            <div class="col-md-6">
              <div class="detail-box">
                <span class="label">Item:</span>
                <span class="value"><?php echo $return['item_name']; ?></span>
              </div>
            </div>
          <?php } ?>

          <?php if (!empty($return['asset_code'])) { ?>
            <div class="col-md-6">
              <div class="detail-box">
                <span class="label">Asset:</span>
                <span class="value"><?php echo $return['asset_code'] . ' - ' . $return['asset_description']; ?></span>
              </div>
            </div>
          <?php } ?>

          <?php if (!empty($return['building_name'])) { ?>
            <div class="row">
              <div class="col-md-4">
                <div class="detail-box">
                  <span class="label">Building:</span>
                  <span class="value"><?php echo $return['building_name']; ?></span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="detail-box">
                  <span class="label">Floor:</span>
                  <span class="value"><?php echo $return['floor_name']; ?></span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="detail-box">
                  <span class="label">Space:</span>
                  <span class="value"><?php echo $return['cabin_name']; ?></span>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>

      <!-- Metadata -->
      <div class="row mb-4">
        <div class="col-12 mb-3">
          <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">
            <i class="fa fa-clock"></i> Metadata
          </h5>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <span class="label">Created Time:</span>
            <span class="value"><?php echo $return['created_date'] ?? ''; ?></span>
          </div>
        </div>
      </div>

      <!-- Images -->
      <?php if (!empty($img_result)) { ?>
        <div class="row mb-3">
          <div class="col-12 mb-3">
            <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">
              <i class="fa fa-image"></i> Attached Images
            </h5>
          </div>
          <div class="col-12 d-flex flex-wrap gap-3">
            <?php foreach ($img_result as $row) { ?>
              <a href="uploads/all/<?php echo $row['file_name']; ?>" target="_blank" class="hover-zoom">
                <img src="uploads/all/<?php echo $row['file_name']; ?>" class="rounded-3 border shadow-sm"
                  style="width: 100px; height: 100px; object-fit: cover;" alt="Attachment Preview">
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>
  <!-- Complaint Responses -->
<?php if (!empty($result['complaint_responses_data'])) { ?>
  <div class="row mb-4">
    <div class="col-12 mb-3">
      <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">
        <i class="fa fa-comment"></i> Complaint Responses
      </h5>
    </div>

    <?php foreach ($result['complaint_responses_data'] as $key => $res) { ?>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-body">
            <p class="mb-1 text-muted small">
              <i class="fa fa-user"></i>
              <strong><?php echo $res['display_name']; ?></strong>
            </p>
            <p class="mb-2 small text-secondary">
              <i class="fa fa-clock"></i>
              <?php echo fromDate($res['transaction_date']); ?>
            </p>
            <p class="fw-semibold"><?php echo nl2br($res['remarks']); ?></p>

            <?php if (!empty($result['complaint_responses_images_data'][$res['id']])) { ?>
              <div class="d-flex flex-wrap gap-2 mt-2">
                <?php foreach ($result['complaint_responses_images_data'][$res['id']] as $img) { ?>
                  <a href="uploads/all/<?php echo $img['file_name']; ?>" target="_blank" class="hover-zoom">
                    <img src="uploads/all/<?php echo $img['file_name']; ?>" class="rounded border shadow-sm"
                      style="width: 80px; height: 80px; object-fit: cover;" alt="Attachment Preview">
                  </a>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
<?php } ?>

<?php if($_GET['RS'] == 1){ ?>

<div class="text-end mb-3">
  <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addResponseModal">
    <i class="fa fa-plus-circle"></i> Add Response
  </button>
</div>
<?php } ?>

<!-- Add Response Modal -->
<div class="modal fade" id="addResponseModal" tabindex="-1" aria-labelledby="addResponseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title fw-bold" id="addResponseModalLabel">
          <i class="fa fa-comment"></i> Add Complaint Response
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="post" action="" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="complaint_id" value="<?php echo $rid; ?>">

          <div class="mb-3">
            <span class="form-label fw-semibold">Comments / Remarks</span>
            <textarea aria-label="Remarks" name="remarks" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <span class="form-label fw-semibold">Upload Images</span>
            <input aria-label="Response Images[]" type="file" name="response_images[]" class="form-control" accept="image/*" multiple>
            <small class="text-muted">You can select multiple images</small>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <input type="hidden" name="save" value="SAVE">
          <button type="submit" class="btn btn-primary fw-bold">Submit Response</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .detail-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 6px;
    padding: 6px 12px;
    margin-bottom: 8px;
  }

  .detail-box .label {
    color: #555;
    font-size: 0.9rem;
    font-weight: 600;
  }

  .detail-box .value {
    color: #222;
    font-size: 0.95rem;
    font-weight: 500;
    text-align: right;
    flex: 1;
  }

  .badge {
    font-size: 0.8rem;
  }

  .hover-zoom img {
    transition: transform .3s ease, box-shadow .3s ease;
  }

  .hover-zoom:hover img {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
  }

  .bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
  }
</style>
