<?php
defined('DATE_FORMAT_DMY_HI') || define('DATE_FORMAT_DMY_HI', DATE_FORMAT_DMY_HI);

$_SESSION['apply_js']['multiselest'] = 1;
$result = array('data' => []);
$selected_type = '';
$vendors = array();
if (isset($_POST['type']) && $_POST['type'] != '') {
  $selected_type = $_POST['type'];
  if ($selected_type == 'vendors') {
    $vurl = API . "?mode=getvendorsdetails&transaction_table=vendors";
  } elseif ($selected_type == 'vendor_registers') {
    $vurl = API . "?mode=getvendorsdetails&transaction_table=vendor_registers";
  }
  $che = callAPI($vurl);
  $rows = json_decode($che, 1);
  $vendors = $rows['rows'];
}

$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr'] = 1;
?>
  <script type="text/javascript" src="../assets/js/custom_list_verification_vendors_and_vendor_registers.js"></script>
  <script type="text/javascript" src="../assets/js/custom_list_verification_vendors_and_vendor_registers_popup.js"></script>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5><?= __('Vendor Verification List'); ?></h5>
    </div>
    <div class="card-body">
      <form method="post" action="" id="typeForm">
        <div class="row col-lg-12 align-items-end">
          <div class="col-lg-4 mb-2">
            <span class="form-label"><?= __('Vendor Type'); ?> <span class="text-danger">*</span></span>
            <select aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single verificationFormSubmit" name="type" id="type" required>
              <option value=""><?= __('Select any Type'); ?></option>
              <option value="vendors" <?php echo ($selected_type == 'vendors') ? 'selected' : ''; ?>><?= __('Vendors'); ?></option>
              <option value="vendor_registers" <?php echo ($selected_type == 'vendor_registers') ? 'selected' : ''; ?>>
                <?= __('Vendor Registers'); ?></option>
            </select>
          </div>
        </div>
      </form>
      <hr>
    </div>
  </div>
  <?php if ($selected_type != '') { ?>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive mt-3">
          <table class="table table-hover table-bordered">
            <thead>
              <tr class="table-secondary">
                <th scope="col">#</th>
                <th scope="col"><?= __('Company Details'); ?></th>
                <th scope="col"><?= __('PAN'); ?></th>
                <th scope="col"><?= __('GST'); ?></th>
                <th scope="col"><?= __('Bank Details'); ?></th>
                <th scope="col"><?= __('TAN'); ?></th>
                <th scope="col"><?= __('MSME'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sl = 1;
              if (!empty($vendors) && is_array($vendors)) {
                foreach ($vendors as $row) {
                  $vendor_verifications = array();
                  if (isset($row['vendor_verifications']) && !empty($row['vendor_verifications'])) {
                    $vendor_verifications = json_decode($row['vendor_verifications'], true);
                  }
                  $company_name = 'N/A';
                  if (isset($row['company'])) {
                      $company_name = $row['company'];
                  } elseif (isset($row['display_name'])) {
                      $company_name = $row['display_name'];
                  }
                  $email = isset($row['email']) ? $row['email'] : 'N/A';
                  $pan_value = isset($row['pan_no']) ? $row['pan_no'] : '';
                  $gst_value = isset($row['gstin']) ? $row['gstin'] : '';
                  $bank_value = isset($row['bank_account_no']) ? $row['bank_account_no'] : '';
                  $ifsc_value = isset($row['ifsc_code']) ? $row['ifsc_code'] : '';
                  $tan_value = isset($row['tan_number']) ? $row['tan_number'] : '';
                  $msme_value = isset($row['msme_number']) ? $row['msme_number'] : '';
                  $pan_verified = isset($vendor_verifications['PAN']['status']) && $vendor_verifications['PAN']['status'] == 'Y';
                  $gst_verified = isset($vendor_verifications['GSTIN']['status']) && $vendor_verifications['GSTIN']['status'] == 'Y';
                  $bank_verified = isset($vendor_verifications['BANK']['status']) && $vendor_verifications['BANK']['status'] == 'Y';
                  $tan_verified = isset($vendor_verifications['TAN']['status']) && $vendor_verifications['TAN']['status'] == 'Y';
                  $msme_verified = isset($vendor_verifications['MSME']['status']) && $vendor_verifications['MSME']['status'] == 'Y';
                  ?>
                  <tr>
                    <td><?= $sl++; ?></td>
                    <td class="company-details">
                      <div class="fw-bold"><?= htmlspecialchars($company_name); ?></div>
                      <div class="text-muted small"><?= htmlspecialchars($email); ?></div>
                    </td>
                    <td>
                      <?php if (!empty($pan_value)) { ?>
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div><?= htmlspecialchars($pan_value); ?></div>
                            <?php if (isset($vendor_verifications['PAN']['verification_datetime'])) { ?>
                              <small class="text-muted"><?= __('Verified'); ?>:
                                <?= date(DATE_FORMAT_DMY_HI, strtotime($vendor_verifications['PAN']['verification_datetime'])); ?></small>
                            <?php } ?>
                          </div>
                          <div>
                            <?php if ($pan_verified) { ?>
                              <i class="fa fa-check-circle text-success"></i>
                            <?php } else { ?>
                              <i class="fa fa-times-circle text-danger"></i>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="verification-buttons mt-2">

                          <button type="button" class="btn btn-sm btn-primary verificationDetails" data-verification_type="pan" data-record_id="<?php echo $row['id']; ?>" data-action_type="change" data-table_name ="<?php echo  $selected_type; ?>" ><?= __('Change'); ?></button>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted"><?= __('N/A'); ?></span>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-info verificationDetails" data-verification_type="pan" data-record_id="<?php echo $row['id']; ?>" data-action_type="insert" data-table_name ="<?php echo  $selected_type; ?>"  ><?= __('Insert'); ?></button>
                        </div>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if (!empty($gst_value)) { ?>
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div><?= htmlspecialchars($gst_value); ?></div>
                            <?php if (isset($vendor_verifications['GSTIN']['verification_datetime'])) { ?>
                              <small class="text-muted"><?= __('Verified'); ?>:
                                <?= date(DATE_FORMAT_DMY_HI, strtotime($vendor_verifications['GSTIN']['verification_datetime'])); ?></small>
                            <?php } ?>
                          </div>
                          <div>
                            <?php if ($gst_verified) { ?>
                              <i class="fa fa-check-circle text-success"></i>
                            <?php } else { ?>
                              <i class="fa fa-times-circle text-danger"></i>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-primary me-1 verificationDetails" data-verification_type="gst" data-record_id="<?php echo $row['id']; ?>" data-action_type="change" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Change'); ?></button>
                          <button type="button" class="btn btn-sm btn-secondary  verificationDetails" data-verification_type="gst" data-record_id="<?php echo $row['id']; ?>" data-action_type="add_new" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Add New GST'); ?></button>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted"><?= __('N/A'); ?></span>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-info  verificationDetails" data-verification_type="gst" data-record_id="<?php echo $row['id']; ?>" data-action_type="insert" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Insert'); ?></button>
                        </div>
                      <?php } ?>
                    </td>
                    <td class="bank-details">
                      <?php if (!empty($bank_value)) { ?>
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div class="fw-bold"><?= htmlspecialchars($bank_value); ?></div>
                            <?php if (!empty($ifsc_value)) { ?>
                              <div class="text-muted small"><?= __('IFSC'); ?>: <?= htmlspecialchars($ifsc_value); ?></div>
                            <?php } ?>
                            <?php if (isset($vendor_verifications['BANK']['verification_datetime'])) { ?>
                              <small class="text-muted"><?= __('Verified'); ?>:
                                <?= date(DATE_FORMAT_DMY_HI, strtotime($vendor_verifications['BANK']['verification_datetime'])); ?></small>
                            <?php } ?>
                          </div>
                          <div>
                            <?php if ($bank_verified) { ?>
                              <i class="fa fa-check-circle text-success"></i>
                            <?php } else { ?>
                              <i class="fa fa-times-circle text-danger"></i>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-primary verificationDetails" data-verification_type="bank" data-record_id="<?php echo $row['id']; ?>" data-action_type="change" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Change'); ?></button>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted"><?= __('N/A'); ?></span>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-info verificationDetails" data-verification_type="bank" data-record_id="<?php echo $row['id']; ?>" data-action_type="insert" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Insert'); ?></button>
                        </div>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if (!empty($tan_value)) { ?>
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div><?= htmlspecialchars($tan_value); ?></div>
                            <?php if (isset($vendor_verifications['TAN']['verification_datetime'])) { ?>
                              <small class="text-muted"><?= __('Verified'); ?>:
                                <?= date(DATE_FORMAT_DMY_HI, strtotime($vendor_verifications['TAN']['verification_datetime'])); ?></small>
                            <?php } ?>
                          </div>
                          <div>
                            <?php if ($tan_verified) { ?>
                              <i class="fa fa-check-circle text-success"></i>
                            <?php } else { ?>
                              <i class="fa fa-times-circle text-danger"></i>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-primary verificationDetails" data-verification_type="tan" data-record_id="<?php echo $row['id']; ?>" data-action_type="change" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Change'); ?></button>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted"><?= __('N/A'); ?></span>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-info verificationDetails" data-verification_type="tan" data-record_id="<?php echo $row['id']; ?>" data-action_type="insert" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Insert'); ?></button>
                        </div>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if (!empty($msme_value)) { ?>
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div><?= htmlspecialchars($msme_value); ?></div>
                            <?php if (isset($vendor_verifications['MSME']['verification_datetime'])) { ?>
                              <small class="text-muted"><?= __('Verified'); ?>:
                                <?= date(DATE_FORMAT_DMY_HI, strtotime($vendor_verifications['MSME']['verification_datetime'])); ?></small>
                            <?php } ?>
                          </div>
                          <div>
                            <?php if ($msme_verified) { ?>
                              <i class="fa fa-check-circle text-success"></i>
                            <?php } else { ?>
                              <i class="fa fa-times-circle text-danger"></i>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-primary verificationDetails" data-verification_type="msme" data-record_id="<?php echo $row['id']; ?>" data-action_type="change" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Change'); ?></button>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted"><?= __('N/A'); ?></span>
                        <div class="verification-buttons mt-2">
                          <button type="button" class="btn btn-sm btn-info verificationDetails" data-verification_type="msme" data-record_id="<?php echo $row['id']; ?>" data-action_type="insert" data-table_name ="<?php echo  $selected_type; ?>"><?= __('Insert'); ?></button>
                        </div>
                      <?php } ?>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="7" class="text-center">
                    <div class="card-px text-center py-20 m-t-30">
                      <p class="text-muted f-16 fw-bold m-b-30">
                        <i class="ti-alert text-warning"></i> <?= __('No Items'); ?>
                      </p>
                      <div class="text-center px-4">
                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png"
                          style="max-height: 250px!important;">
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
  <?php } else { ?>
    <div class="card">
      <div class="card-body text-center">
        <div class="alert alert-info">
          <i class="ti-info-alt"></i> <?= __('Please select a type to view the verification list.'); ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<div class="modal fade" id="kt_modal_dynamic_popup" tabindex="-1" aria-labelledby="kt_modal_dynamic_popup_label"
  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="dynamic_popup1">
      </div>
    </div>
  </div>
</div>
