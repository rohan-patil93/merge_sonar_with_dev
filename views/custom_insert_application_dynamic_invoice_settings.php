<?php
if (!defined('STYLE_DISPLAY_NONE_SP')) {
    define('STYLE_DISPLAY_NONE_SP', 'display: none;');
}

defined('KEY_COMPANY_ID') || define('KEY_COMPANY_ID', 'company_id');
defined('LBL_ENTER_TEMPLATE_NAME') || define('LBL_ENTER_TEMPLATE_NAME', 'Enter template name');
defined('KEY_LOGO_POSITION') || define('KEY_LOGO_POSITION', 'logoPosition');
defined('KEY_LOGO_SIZE') || define('KEY_LOGO_SIZE', 'logoSize');
defined('ATTR_SELECTED') || define('ATTR_SELECTED', 'selected');
$_SESSION['apply_js']['multiselest'] = 1;


if (isset($_POST['submitinvoice'])) {
  $_POST[KEY_COMPANY_ID] = $_SESSION[KEY_COMPANY_ID];
  $_POST['user_id'] = $_SESSION['userid'];
  $url = API . "?mode=insertdynamicinvoice&method=post";
  $response1 = file_post_contents($url, $_POST, 'insert');
  $response33 = json_decode($response1, 1);
  $response = $response33['res'];
  if ($response['status'] == 'sucess') {
    if ($response['LASTID'] == 0) {
      $msgcode = 3001;
    } else {
      $msgcode = 3000;
      $_SESSION['insert_msg'] = $msgcode;
    }
    ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
          tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
        }
      }
    </script>
    <?php
  } else {
    if ($response['status'] == 'fail') {
      $dis_err = explode(",", $response['other']);
      foreach ($dis_err as $va) {
        $dis_er[] = $dis_labels[$va];
      }
      $error_message = $response['error_no'] . " : " . $_SESSION['error'][$response['error_no']] . " : " . implode(", ", (array) $dis_er);
    } else {
      $error_message = $response['message'];
    }
  }
}

$rid1 = $_GET['Rid'];
$rid2 = encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = $rrid['1'];
$url = API . "?mode=getdynamicinvocedetails&rid=" . $rid;
$response = callAPI($url);
$result = json_decode($response, 1);
$invdata = $result['application_dynamic_invoice_settings'];

$basic_options = json_decode($invdata['basic_options'], true);
$fontFamily = $basic_options['fontFamily'] ?? '';
$margin = $basic_options['margin'] ?? '';
$padding = $basic_options['padding'] ?? '';
$height = $basic_options['height'] ?? '';

$border_options = json_decode($invdata['border_options'], true);
$borderstyle = $border_options['borderStyle'] ?? '';
$bordercolor = $border_options['borderColor'] ?? '';
$borderwidth = $border_options['borderWidth'] ?? '';

$invoice_options = $invdata['invoice_options'];

$File_Uploads_Path = "uploads/all/";

if ($userData['user_photo'] != '') {
  $image = $File_Uploads_Path . $userData['user_photo'];
} else {
  $image = $File_Uploads_Path . "no-image.jpg";
}

?>
<?php if ($error_message != '') { ?>
  <div class="alert alert-danger background-danger alert-dismissible">
    <strong><?php echo $error_message; ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
  </div>
<?php } ?>
<div class="card">
  <div class="card-header">
    <h5 class><?= __('Custom Invoice with preview'); ?></h5>
    <div class="card-header-right">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
        <i class="icofont icofont-reply"></i> <?= __('Back') ?>
      </a>
    </div>
  </div>
  <div class="card-body m-r-50 m-l-50">
    <div class="container mt-4">
      <div class="row ml-20 mr-20">
        <div class="col-lg-3 p-3 bg-light border custom-scroll" style="height:650px;">
          <h5 class="text-center mb-4"><?= __('Customization Options') ?></h5>
          <div class="">
            <div class="mb-3">
              
              <div id="logoVisibility" class="dynamic-options">
                <div class="card card-body mt-2">
                  <span class="form-label mb-2 d-inline-block fw-bold"><?= __('Toggle Logo') ?></span>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showLogo" name="showLogo" <?php echo $logoOptions['showLogo'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Showlogo'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Showlogo'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showLogo"><?= __('Show Logo') ?></label>
                  </div>
                </div>
              </div>

              
              <div id="logoSize" class="dynamic-options">
                <div class="card card-body mt-2">
                  <label for="logoSizeSelect" class="form-label"><?= __('Logo Size') ?></label>
                  <select class="form-select" id="logoSizeSelect" name="logoSize" title="<?= htmlspecialchars(__('Logosize'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Logosize'), ENT_QUOTES, 'UTF-8'); ?>">
                    <option value="small" <?php echo (empty($logoOptions[KEY_LOGO_SIZE]) || $logoOptions[KEY_LOGO_SIZE] === 'small') ? ATTR_SELECTED : ''; ?>> <?= __('Small') ?> </option>
                    <option value="medium" <?php echo $logoOptions[KEY_LOGO_SIZE] === 'medium' ? ATTR_SELECTED : ''; ?>> <?= __('Medium') ?> </option>
                    <option value="large" <?php echo $logoOptions[KEY_LOGO_SIZE] === 'large' ? ATTR_SELECTED : ''; ?>> <?= __('Large') ?> </option>
                    <option value="custom" <?php echo !empty($logoOptions['customLogoSize']) ? ATTR_SELECTED : ''; ?>> <?= __('Custom') ?> </option>
                  </select>
                  <div id="customLogoSizeInputContainer" class="mt-2"
                    style="<?php echo !empty($logoOptions['customLogoSize']) ? '' : STYLE_DISPLAY_NONE_SP; ?>">
                    <label for="customLogoSizeInput" class="form-label"><?= __('Custom Logo Size (px)') ?></label>
                    <input type="number" id="customLogoSizeInput" class="form-control" name="customLogoSize"
                      value="<?php echo htmlspecialchars($logoOptions['customLogoSize']); ?>"
                      placeholder="<?= htmlspecialchars(__('Enter size in px'), ENT_QUOTES, 'UTF-8'); ?>" / title="<?= htmlspecialchars(__('Enter size in px'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter size in px'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                </div>
              </div>


              <div id="topheaderoptions" class="dynamic-options">
                <div class="card card-body mt-2">
                  <span class="form-label mb-2 d-inline-block fw-bold"><?= __('Toggle Top Header Options') ?></span>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showinvoiceno" name="showInvoiceNo" <?php echo $logoOptions['showInvoiceNo'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Showinvoiceno'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Showinvoiceno'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showinvoiceno"><?= __('Show Invoice No.') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="invoicenoheading"
                      value="<?php echo !empty($logoOptions['invoiceNoHeading']) ? $logoOptions['invoiceNoHeading'] : 'Invoice No.'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoicenoSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo (empty($logoOptions['invoiceNoValue']) || $logoOptions['invoiceNoValue'] === 'transaction_number') ? ATTR_SELECTED : ''; ?>
                        value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?>
                        value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_no' ? ATTR_SELECTED : ''; ?> value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?>
                        value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'additional_charges' ? ATTR_SELECTED : ''; ?>
                        value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $logoOptions['invoiceNoValue'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                    </select>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showdate" <?php echo $logoOptions['showDate'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showdate"><?= __('Show Date') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="dateheading"
                      value="<?php echo !empty($logoOptions['dateHeading']) ? $logoOptions['dateHeading'] : 'Date'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoicedateSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['dateValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo (empty($logoOptions['dateValue']) || $logoOptions['dateValue'] === 'transaction_date') ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_id' ? ATTR_SELECTED : ''; ?> value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?>
                        value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'tax_type' ? ATTR_SELECTED : ''; ?> value="tax_type">
                        <?= __('Tax Type'); ?></option>
                      <option <?php echo $logoOptions['dateValue'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'due_date' ? ATTR_SELECTED : ''; ?> value="due_date">
                        <?= __('Due Date'); ?></option>
                      <option <?php echo $logoOptions['dateValue'] === 'ref_type' ? ATTR_SELECTED : ''; ?> value="ref_type">
                        <?= __('Ref Type'); ?></option>
                      <option <?php echo $logoOptions['dateValue'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id"><?= __('Ref Id'); ?>
                      </option>
                      <option <?php echo $logoOptions['dateValue'] === 'ref_no' ? ATTR_SELECTED : ''; ?> value="ref_no"><?= __('Ref No'); ?>
                      </option>
                      <option <?php echo $logoOptions['dateValue'] === 'ref_date' ? ATTR_SELECTED : ''; ?> value="ref_date">
                        <?= __('Ref Date'); ?></option>
                      <option <?php echo $logoOptions['dateValue'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?>
                        value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'additional_charges' ? ATTR_SELECTED : ''; ?>
                        value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'pdf_name' ? ATTR_SELECTED : ''; ?> value="pdf_name">
                        <?= __('Pdf Name'); ?></option>
                      <option <?php echo $logoOptions['dateValue'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $logoOptions['dateValue'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                    </select>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showduedate" <?php echo $logoOptions['showDueDate'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showduedate"><?= __('Show Due Date') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="duedateheading"
                      value="<?php echo !empty($logoOptions['dueDateHeading']) ? $logoOptions['dueDateHeading'] : 'Due Date'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoiceduedateSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['dueDateValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?>
                        value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo (empty($logoOptions['dueDateValue']) || $logoOptions['dueDateValue'] === 'due_date') ? ATTR_SELECTED : ''; ?> value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id">
                        <?= __('Ref Id'); ?></option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'ref_no' ? ATTR_SELECTED : ''; ?> value="ref_no">
                        <?= __('Ref No'); ?></option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?>
                        value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'additional_charges' ? ATTR_SELECTED : ''; ?>
                        value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $logoOptions['dueDateValue'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                    </select>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showpo_num" <?php echo $logoOptions['showPoNum'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showpo_num"><?= __('Show P.O. Number') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="po_numheading"
                      value="<?php echo !empty($logoOptions['poNumHeading']) ? $logoOptions['poNumHeading'] : 'P.O. Number'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoiceponumSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['poNumValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_id' ? ATTR_SELECTED : ''; ?> value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?>
                        value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'tax_type' ? ATTR_SELECTED : ''; ?> value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'due_date' ? ATTR_SELECTED : ''; ?> value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'ref_type' ? ATTR_SELECTED : ''; ?> value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id"><?= __('Ref Id'); ?>
                      </option>
                      <option <?php echo (empty($logoOptions['poNumValue']) || $logoOptions['poNumValue'] === 'ref_no') ? ATTR_SELECTED : ''; ?> value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'ref_date' ? ATTR_SELECTED : ''; ?> value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?>
                        value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'additional_charges' ? ATTR_SELECTED : ''; ?>
                        value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'pdf_name' ? ATTR_SELECTED : ''; ?> value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $logoOptions['poNumValue'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                    </select>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showpo_date" <?php echo $logoOptions['showPoDate'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showpo_date"><?= __('Show P.O Date') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="po_dateheading"
                      value="<?php echo !empty($logoOptions['poDateHeading']) ? $logoOptions['poDateHeading'] : 'P.O Date'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoicepodateSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['poDateValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?>
                        value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?>
                        value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?>
                        value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id"><?= __('Ref Id'); ?></option>
                      <option <?php echo $logoOptions['poDateValue'] === 'ref_no' ? ATTR_SELECTED : ''; ?> value="ref_no"><?= __('Ref No'); ?></option>
                      <option <?php echo (empty($logoOptions['poDateValue']) || $logoOptions['poDateValue'] === 'ref_date') ? ATTR_SELECTED : ''; ?> value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?>
                        value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?>
                        value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'additional_charges' ? ATTR_SELECTED : ''; ?>
                        value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?>
                        value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $logoOptions['poDateValue'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?>
                        value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                    </select>
                  </div>
                  
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showemail" <?php echo $logoOptions['showEmail'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showemail"><?= __('Show Email') ?></label>
                    <input class="form-control mb-1 mt-2" type="text" id="emailheading"
                      value="<?php echo !empty($logoOptions['emailHeading']) ? $logoOptions['emailHeading'] : 'Email'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="invoiceemailSelect" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $logoOptions['emailValue'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $logoOptions['emailValue'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo (empty($logoOptions['emailValue']) || $logoOptions['emailValue'] === 'party_email') ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $logoOptions['emailValue'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $logoOptions['emailValue'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $logoOptions['emailValue'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                    </select>
                  </div>
                  
              </div>
              <?php
              $headerOptions = json_decode($invdata['header_options'], true);
              ?>
              
              <div id="addresssecVisibility" class="dynamic-options">
                <div class="card card-body mt-2">

                  
                    <div class="mt-3">
                      <h6 class="text-muted"><?= __('Address Field Visibility'); ?></h6>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showFirstAddressField" <?php echo ($headerOptions['showFirstAddressField'] ?? true) ? 'checked' : ''; ?> disabled title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showFirstAddressField"><?= __('Show Address Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showFirstPincodeField" <?php echo ($headerOptions['showFirstPincodeField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showFirstPincodeField"><?= __('Show Phone Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showFirstGstField" <?php echo ($headerOptions['showFirstGstField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showFirstGstField"><?= __('Show GST Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showFirstPanField" <?php echo ($headerOptions['showFirstPanField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showFirstPanField"><?= __('Show PAN Field') ?></label>
                      </div>
                    </div>
                  </div>

                  
                    <div class="mt-3">
                      <h6 class="text-muted"><?= __('Address Field Visibility'); ?></h6>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showSecondAddressField" <?php echo ($headerOptions['showSecondAddressField'] ?? true) ? 'checked' : ''; ?> disabled title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showSecondAddressField"><?= __('Show Address Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showSecondPincodeField" <?php echo ($headerOptions['showSecondPincodeField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showSecondPincodeField"><?= __('Show Phone Number') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showSecondGstField" <?php echo ($headerOptions['showSecondGstField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showSecondGstField"><?= __('Show GST Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showSecondPanField" <?php echo ($headerOptions['showSecondPanField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showSecondPanField"><?= __('Show PAN Field') ?></label>
                      </div>
                    </div>
                  </div>

                  
                    <div class="mt-3">
                      <h6 class="text-muted"><?= __('Address Field Visibility'); ?></h6>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showThirdAddressField" <?php echo ($headerOptions['showThirdAddressField'] ?? true) ? 'checked' : ''; ?> disabled title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showThirdAddressField"><?= __('Show Address Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showThirdPincodeField" <?php echo ($headerOptions['showThirdPincodeField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showThirdPincodeField"><?= __('Show Phone Number') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showThirdGstField" <?php echo ($headerOptions['showThirdGstField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showThirdGstField"><?= __('Show GST Field') ?></label>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showThirdPanField" <?php echo ($headerOptions['showThirdPanField'] ?? true) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-check-label" for="showThirdPanField"><?= __('Show PAN Field') ?></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              
              <div id="tableVisibility" class="dynamic-options">
                <div class="card card-body mt-2">
                  <span class="form-label mb-2 d-inline-block fw-bold"><?= __('Toggle Table') ?></span>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showTable" readonly disabled checked title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showTable"><?= __('Show Table') ?></label>
                  </div>
                </div>
              </div>

              
                  <div class="mb-1">
                    <span class="form-label d-inline-block fw-bold"><?= __('Table Header Background') ?></span>
                    <input type="color" class="form-control form-control-color" style="max-width:100%;"
                      id="tableHeaderbgColor" value="<?php echo $tableOptions['headerBgColor'] ?? '#f0f0f0'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>

                  
                  <div class="mb-1">
                    <span class="form-label d-inline-block fw-bold"><?= __('Table Body Background') ?></span>
                    <input type="color" class="form-control form-control-color" style="max-width:100%;"
                      id="tableBodybgColor" value="<?php echo $tableOptions['bodyBgColor'] ?? '#ffffff'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>

                  
                  <div class="mb-1">
                    <span class="form-label d-inline-block fw-bold"><?= __('Table Footer Background') ?></span>
                    <input type="color" class="form-control form-control-color" style="max-width:100%;"
                      id="tableFooterbgColor" value="<?php echo $bodyTextColor['footerBgColor'] ?? '#f8f9fa'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>

                  
                  <div class="mb-1">
                    <span class="form-label d-inline-block fw-bold"><?= __('Table Row Height') ?></span>
                    <input type="number" class="form-control" id="rowHeight" min="20"
                      placeholder="<?= htmlspecialchars(__('Enter Row Height (px)'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo $bodyTextColor['rowHeight'] ?? '40'; ?>" title="<?= htmlspecialchars(__('Enter Row Height (px)'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter Row Height (px)'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>

                  
                  <div class="mb-1">
                    <span class="form-label d-inline-block fw-bold"><?= __('Table Font Size') ?></span>
                    <input type="number" class="form-control" id="fontSize" min="8" max="36"
                      placeholder="<?= htmlspecialchars(__('Enter Font Size (px)'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo $bodyTextColor['fontSize'] ?? '16'; ?>" title="<?= htmlspecialchars(__('Enter Font Size (px)'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter Font Size (px)'), ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                </div>
              </div>

              
              <div id="itemsshow" class="dynamic-options">
                <div class="card card-body mt-2">
                  <label for="itemsInput" class="form-label"><?= __('Items In Each Page Show') ?></label>
                  <input type="number" class="form-control" id="itemsInput"
                    value="<?php echo $tableOptions['itemstable'] ?? '0'; ?>"
                    placeholder="<?= htmlspecialchars(__('Enter No. Of Items In Each Page Show'), ENT_QUOTES, 'UTF-8'); ?>" title="<?= htmlspecialchars(__('Enter No. Of Items In Each Page Show'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter No. Of Items In Each Page Show'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <h6><?= __('Footer Options') ?></h6>
              <div class="btn-group d-flex flex-wrap">
                <button class="btn btn-outline-danger btn-sm mb-2" data-target="page_footerVisibility">
                  <i class="bi bi-eye"></i> <?= __('Visibility'); ?>
                </button>
                <button class="btn btn-outline-dark btn-sm mb-2" data-target="page_footer_optionsVisibility">
                  <i class="bi bi-aspect-ratio"></i> <?= __('Footer Options'); ?>
                </button>
              </div>
              <?php
              $footerOptions = json_decode($invdata['footer_options'], true);
              ?>
              <div id="page_footerVisibility" class="dynamic-options">
                <div class="card card-body mt-2">
                  <span class="form-label mb-2 d-inline-block fw-bold"><?= __('Toggle Page Footer') ?></span>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showpagefooter" <?php echo $footerOptions['showFooter'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showpagefooter"><?= __('Show Page Footer') ?></label>
                  </div>
                </div>
              </div>

              <div id="page_footer_optionsVisibility" class="dynamic-options">
                <div class="card card-body mt-2">
                  <label for="page_footer_optionsVisibility" class="form-label"><?= __('Footer Options Visibility') ?></label>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showtotalinalpha" <?php echo $footerOptions['showTotalAlpha'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showtotalinalpha"><?= __('Show Total In Alphabet') ?></label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showsignature" <?php echo $footerOptions['showSignature'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showsignature"><?= __('Show Signature') ?></label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showqr" <?php echo $footerOptions['showQr'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showqr"><?= __('Show QR') ?></label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showbankdetails" <?php echo isset($footerOptions['showBankDetails']) && $footerOptions['showBankDetails'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showbankdetails"><?= __('Show Bank Details') ?></label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showadditionaldetails" <?php echo isset($footerOptions['showAdditionalDetails']) && $footerOptions['showAdditionalDetails'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showadditionaldetails"><?= __('Show Additional Details') ?></label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showtotalbeforetax" <?php echo $footerOptions['totalbeforetax']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showtotalbeforetax"><?= __('Show Sub Total') ?></label>
                    <input class="form-control mb-1" type="text" id="heading1"
                      value="<?php echo !empty($footerOptions['totalbeforetax']['heading']) ? $footerOptions['totalbeforetax']['heading'] : 'Sub Total'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value1" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'id' ? ATTR_SELECTED : ''; ?>
                        value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?> value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?> value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?> value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?> value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?> value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?> value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?> value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?> value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo (empty($footerOptions['totalbeforetax']['value']) || $footerOptions['totalbeforetax']['value'] === 'amount') ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?> value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?> value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?> value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?> value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?> value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?> value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?> value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?> value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?> value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?> value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?> value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?> alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'rounding_off' ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>
                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showdiscount" <?php echo (isset($footerOptions['showFields']['discount']['visible']) && $footerOptions['showFields']['discount']['visible']) || (isset($footerOptions['discount']['visible']) && $footerOptions['discount']['visible']) ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showdiscount"><?= __('Show Discount') ?></label>
                    <input class="form-control mb-1" type="text" id="heading2"
                      value="<?php
                      $discountHeading = 'Discount';
                      if (!empty($footerOptions['showFields']['discount']['heading'])) {
                          $discountHeading = $footerOptions['showFields']['discount']['heading'];
                      } elseif (!empty($footerOptions['discount']['heading'])) {
                          $discountHeading = $footerOptions['discount']['heading'];
                      }
                      echo $discountHeading;
                    ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value2" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <?php
                      $discountValue = '';
                      if (isset($footerOptions['showFields']['discount']['value'])) {
                          $discountValue = $footerOptions['showFields']['discount']['value'];
                      } elseif (isset($footerOptions['discount']['value'])) {
                          $discountValue = $footerOptions['discount']['value'];
                      }
                      ?>
                      <option <?php echo $discountValue === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $discountValue === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'amount' ? ATTR_SELECTED : ''; ?>
                        value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $discountValue === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo (empty($discountValue) || $discountValue === 'discount_amount') ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['discount']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'rounding_off' ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>

                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showtotaltaxamount" <?php echo $footerOptions['totaltaxamount']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showtotaltaxamount"><?= __('Show Total Tax Amount') ?></label>
                    <input class="form-control mb-1" type="text" id="heading3"
                      value="<?php echo !empty($footerOptions['totaltaxamount']['heading3']) ? $footerOptions['totalbeforetax']['heading'] : 'Total Tax Amount'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value3" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'id' ? ATTR_SELECTED : ''; ?>
                        value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?> value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?> value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?> value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?> value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?> value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?> value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?> value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?> value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'amount' ? ATTR_SELECTED : ''; ?>
                        value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo (empty($footerOptions['totaltaxamount']['value']) || $footerOptions['totaltaxamount']['value'] === 'tax_amount') ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?> value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?> value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?> value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?> value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?> value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?> value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?> value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?> value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?> value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?> value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?> value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?> alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totaltaxamount']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'rounding_off' ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>

                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showroundedoff" <?php echo $footerOptions['roundedoff']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showroundedoff"><?= __('Show Rounded Off') ?></label>
                    <input class="form-control mb-1" type="text" id="heading4"
                      value="<?php echo !empty($footerOptions['roundedoff']['heading4']) ? $footerOptions['totalbeforetax']['heading'] : 'Rounded Off'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value4" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?> value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?> value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'amount' ? ATTR_SELECTED : ''; ?>
                        value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['roundedoff']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo (empty($footerOptions['rounding_off']['value']) || $footerOptions['rounding_off']['value'] === 'rounding_off') ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>
                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showtotalamount" <?php echo $footerOptions['totalamount']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showtotalamount"><?= __('Show Total Amount') ?></label>
                    <input class="form-control mb-1" type="text" id="heading5"
                      value="<?php echo !empty($footerOptions['totalamount']['heading5']) ? $footerOptions['totalamount']['heading'] : 'Total Amount'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value5" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['totalamount']['value'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?> value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?> value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?> value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'amount' ? ATTR_SELECTED : ''; ?>
                        value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?> value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo (empty($footerOptions['totalamount']['value']) || $footerOptions['totalamount']['value'] === 'total_amount') ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?> value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totalamount']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'rounding_off' ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>

                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showsecurityamount" <?php echo $footerOptions['securityamount']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showsecurityamount"><?= __('Show Security Amount') ?></label>
                    <input class="form-control mb-1" type="text" id="heading4_5"
                      value="<?php echo !empty($footerOptions['securityamount']['heading']) ? $footerOptions['securityamount']['heading'] : 'Security Amount'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value4_5" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['securityamount']['value'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?> value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?> value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?> value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?> value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?> value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'originator' ? ATTR_SELECTED : ''; ?> value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?> value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?> value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?> value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?> value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?> value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?> value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?> value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?> value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?> value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'notes' ? ATTR_SELECTED : ''; ?> value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?> value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?> value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'amount' ? ATTR_SELECTED : ''; ?> value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?> value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'type' ? ATTR_SELECTED : ''; ?> value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?> value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?> value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?> value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?> value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?> value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?> value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?> value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?> value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?> value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?> value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?> value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?> value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?> value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?> value="parent_ref_id"> <?= __('Parent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?> value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?> value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?> value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?> value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?> value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?> value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'pending_amount' ? ATTR_SELECTED : ''; ?> value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?> value="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?> value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['securityamount']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo (empty($footerOptions['securityamount']['value']) || $footerOptions['securityamount']['value'] === 'security_amount') ? ATTR_SELECTED : ''; ?> value="security_amount"> <?= __('Security Amount') ?> </option>
                    </select>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showtotaldue" <?php echo $footerOptions['totaldue']['visible'] ? 'checked' : ''; ?> title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label" for="showtotaldue"><?= __('Show Total Due') ?></label>
                    <input class="form-control mb-1" type="text" id="heading6"
                      value="<?php echo !empty($footerOptions['totaldue']['heading6']) ? $footerOptions['totaldue']['heading'] : 'Total Due'; ?>" title="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                    <select class="form-select" id="value6" style="display: none;" title="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                      <option <?php echo $footerOptions['totaldue']['value'] === 'id' ? ATTR_SELECTED : ''; ?> value="id"> <?= __('ID') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === KEY_COMPANY_ID ? ATTR_SELECTED : ''; ?>
                        value="company_id"> <?= __('Company Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'cost_center_id' ? ATTR_SELECTED : ''; ?>
                        value="cost_center_id"> <?= __('Cost Center Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_number' ? ATTR_SELECTED : ''; ?> value="transaction_number"> <?= __('Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_date' ? ATTR_SELECTED : ''; ?>
                        value="transaction_date"> <?= __('Transaction Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_location_id' ? ATTR_SELECTED : ''; ?> value="transaction_location_id"> <?= __('Transaction Location Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_warehouse_id' ? ATTR_SELECTED : ''; ?> value="transaction_warehouse_id"> <?= __('Transaction Warehouse Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'originator_currency_id' ? ATTR_SELECTED : ''; ?> value="originator_currency_id"> <?= __('Originator Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'currency_id' ? ATTR_SELECTED : ''; ?>
                        value="currency_id"> <?= __('Currency Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'currency_rate' ? ATTR_SELECTED : ''; ?>
                        value="currency_rate"> <?= __('Currency Rate') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'originator' ? ATTR_SELECTED : ''; ?>
                        value="originator"> <?= __('Originator') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_id' ? ATTR_SELECTED : ''; ?>
                        value="party_id"> <?= __('Party Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_name' ? ATTR_SELECTED : ''; ?>
                        value="party_name"> <?= __('Party Name') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_email' ? ATTR_SELECTED : ''; ?>
                        value="party_email"> <?= __('Party Email') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_tax_number' ? ATTR_SELECTED : ''; ?>
                        value="party_tax_number"> <?= __('Party Tax Number') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_phone' ? ATTR_SELECTED : ''; ?>
                        value="party_phone"> <?= __('Party Phone') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_address' ? ATTR_SELECTED : ''; ?>
                        value="party_address"> <?= __('Party Address') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_ship_adderss' ? ATTR_SELECTED : ''; ?> value="party_ship_adderss"> <?= __('Party Ship Adderss') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'originator_state_id' ? ATTR_SELECTED : ''; ?> value="originator_state_id"> <?= __('Originator State Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'supplier_state_id' ? ATTR_SELECTED : ''; ?>
                        value="supplier_state_id"> <?= __('Supplier State Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'place_of_supply' ? ATTR_SELECTED : ''; ?>
                        value="place_of_supply"> <?= __('Place Of Supply') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'payment_terms' ? ATTR_SELECTED : ''; ?>
                        value="payment_terms"> <?= __('Payment Terms') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'notes' ? ATTR_SELECTED : ''; ?>
                        value="notes"> <?= __('Notes') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'printing_notes' ? ATTR_SELECTED : ''; ?>
                        value="printing_notes"> <?= __('Printing Notes') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'parent_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_id"> <?= __('Parent Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'amount' ? ATTR_SELECTED : ''; ?>
                        value="amount"> <?= __('Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'tax_amount' ? ATTR_SELECTED : ''; ?>
                        value="tax_amount"> <?= __('Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'type' ? ATTR_SELECTED : ''; ?>
                        value="type"> <?= __('Type') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'tax_type' ? ATTR_SELECTED : ''; ?>
                        value="tax_type"> <?= __('Tax Type') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'discount_type' ? ATTR_SELECTED : ''; ?>
                        value="discount_type"> <?= __('Discount Type') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'discount_value' ? ATTR_SELECTED : ''; ?>
                        value="discount_value"> <?= __('Discount Value') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'discount_amount' ? ATTR_SELECTED : ''; ?>
                        value="discount_amount"> <?= __('Discount Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'total_amount' ? ATTR_SELECTED : ''; ?>
                        value="total_amount"> <?= __('Total Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_tax' ? ATTR_SELECTED : ''; ?>
                        value="shipping_tax"> <?= __('Shipping Tax') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_amount' ? ATTR_SELECTED : ''; ?>
                        value="shipping_amount"> <?= __('Shipping Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_tax_amount' ? ATTR_SELECTED : ''; ?> value="shipping_tax_amount"> <?= __('Shipping Tax Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'tax_state' ? ATTR_SELECTED : ''; ?>
                        value="tax_state"> <?= __('Tax State') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'opex_cpex' ? ATTR_SELECTED : ''; ?>
                        value="opex_cpex"> <?= __('Opex Cpex') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'doc_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_no"> <?= __('Doc Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'doc_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="doc_ref_date"> <?= __('Doc Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'due_date' ? ATTR_SELECTED : ''; ?>
                        value="due_date"> <?= __('Due Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'ref_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_type"> <?= __('Ref Type') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'parent_ref_id' ? ATTR_SELECTED : ''; ?>
                        value="parent_ref_id"> <?= __('Rarent Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'ref_id' ? ATTR_SELECTED : ''; ?>
                        value="ref_id"> <?= __('Ref Id') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'ref_no' ? ATTR_SELECTED : ''; ?>
                        value="ref_no"> <?= __('Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'ref_date' ? ATTR_SELECTED : ''; ?>
                        value="ref_date"> <?= __('Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'ref_bill_type' ? ATTR_SELECTED : ''; ?>
                        value="ref_bill_type"> <?= __('Ref Bill Type') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'old_ref_no' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_no"> <?= __('Old Ref No') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'old_ref_date' ? ATTR_SELECTED : ''; ?>
                        value="old_ref_date"> <?= __('Old Ref Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'party_additional_details' ? ATTR_SELECTED : ''; ?> value="party_additional_details"> <?= __('Party Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'follow_up_level_ids' ? ATTR_SELECTED : ''; ?> value="follow_up_level_ids"> <?= __('Follow Up Level Ids') ?> </option>
                      <option <?php echo (empty($footerOptions['totaldue']['value']) || $footerOptions['totaldue']['value'] === 'pending_amount') ? ATTR_SELECTED : ''; ?>
                        value="pending_amount"> <?= __('Pending Amount') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice' ? ATTR_SELECTED : ''; ?>
                        alue="cancel_invoice"> <?= __('Cancel Invoice') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice_date' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_date"> <?= __('Cancel Invoice Date') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice_reason' ? ATTR_SELECTED : ''; ?> value="cancel_invoice_reason"> <?= __('Cancel Invoice Reason') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'additional_charges' ? ATTR_SELECTED : ''; ?> value="additional_charges"> <?= __('Additional Charges') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'pdf_name' ? ATTR_SELECTED : ''; ?>
                        value="pdf_name"> <?= __('Pdf Name') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'owner_additional_details' ? ATTR_SELECTED : ''; ?> value="owner_additional_details"> <?= __('Owner Additional Details') ?> </option>
                      <option <?php echo $footerOptions['totaldue']['value'] === 'serial_transaction_number' ? ATTR_SELECTED : ''; ?> value="serial_transaction_number"> <?= __('Serial Transaction Number') ?> </option>
                      <option <?php echo $footerOptions['totalbeforetax']['value'] === 'rounding_off' ? ATTR_SELECTED : ''; ?> value="rounding_off"> <?= __('Rounded Off') ?> </option>
                    </select>
                  </div>
                </div>
              </div>

              
                <a class="btn btn-outline-danger btn-sm mb-2" data-target="textStyleOptions">
                  <i class="bi bi-fonts"></i> <?= __('Text Style') ?>
                </a>
                
                <a class="btn btn-outline-secondary btn-sm mb-2" data-target="paddingOptions">
                  <i class="bi bi-box"></i> <?= __('Padding') ?>
                </a>
                
              </div>

              
              <div id="marginOptions" class="dynamic-options">
                <div class="card card-body mt-2">
                  <label for="marginInput" class="form-label"><?= __('Margin (in px)') ?></label>
                  <input type="number" class="form-control" id="marginInput" name="marginInput"
                    value="<?php echo $margin; ?>" min="0" placeholder="<?= htmlspecialchars(__('Enter margin'), ENT_QUOTES, 'UTF-8'); ?>" title="<?= htmlspecialchars(__('Enter margin'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter margin'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>

              
              <div id="heightOptions" class="dynamic-options">
                <div class="card card-body mt-2">
                  <label for="heightInput" class="form-label"><?= __('Height (in %)') ?></label>
                  <input type="number" class="form-control" id="heightInput" name="heightInput"
                    value="<?php echo isset($height) && $height !== '' ? $height : 100; ?>" min="100" placeholder="<?= htmlspecialchars(__('Enter height (Minimum 100%)'), ENT_QUOTES, 'UTF-8'); ?>" title="<?= htmlspecialchars(__('Enter height (Minimum 100%)'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Enter height (Minimum 100%)'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <form action="" method="post">
            <div class="invoice-preview bg-light bordered" id="invoicePreview"
              style="min-height: 500px; position: relative;height: <?php echo $height; ?>%;font-family:<?php echo $fontFamily; ?>;margin:<?php echo $margin; ?>px;padding:<?php echo $padding; ?>px;
              border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;">
              <div class="top-header logosec" style="position:relative;width: 100%;">
                <div class="head-one">
                  <?php
                  $defaultSize = isset($logoOptions[KEY_LOGO_SIZE]) ? $logoOptions[KEY_LOGO_SIZE] : 'medium';
                  $sizeMap = [
                    'small' => 40,
                    'medium' => 80,
                    'large' => 95
                  ];

                  $finalSize = ($defaultSize === 'custom' && !empty($logoOptions['customLogoSize']))
                    ? intval($logoOptions['customLogoSize'])
                    : ($sizeMap[$defaultSize] ?? 85);
                  ?>

                  <img id="logoPreview" src="<?php echo $image; ?>" alt="" style="display: block; position: absolute; max-height: <?php echo $finalSize; ?>px; left: 0; top: 0;
                  <?php echo empty($logoOptions['showLogo']) ? STYLE_DISPLAY_NONE_SP : ''; ?>">
                  <div class="invoice-no" style="display: block; text-align: right;">
                    <p class="invoice" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Invoice No'); ?></span></p>
                    <p class="date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Date'); ?></span>
                    </p>
                    <p class="due_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Due Date'); ?></span></p>
                    <p class="po_num" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('P.O. Number'); ?></span></p>
                    <p class="po_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('P.O Date'); ?></span></p>
                    
                    <div class="bankdetails"
                      style="<?php echo !isset($footerOptions['showBankDetails']) || !$footerOptions['showBankDetails'] ? STYLE_DISPLAY_NONE_SP : ''; ?> margin-bottom: 10px;">
                      <h6 style="text-decoration: underline;margin-bottom: 5px;"><?= __('Bank Details'); ?> :</h6>
                      <div
                        style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px; padding: 5px; width: 100%;">
                        <div>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('Bank Name'); ?>:</h6>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('Account Holder'); ?>:</h6>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('Account Number'); ?>:</h6>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('IFSC Number'); ?>:</h6>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('Account Type'); ?>:</h6>
                          <h6 style="text-align: left; white-space: nowrap; font-weight: 600;"><?= __('UPI'); ?>:</h6>
                        </div>
                        <div>
                          <h6 class="bank_name">xxxx</h6>
                          <h6 class="bank_contact_person">xxxx</h6>
                          <h6 class="bank_account_number">xxxx</h6>
                          <h6 class="ifsc_code">xxxx</h6>
                          <h6 class="account_type">xxxx</h6>
                          <h6 class="upi_id">xxxx</h6>
                        </div>
                      </div>
                    </div>

                    
                    <p class="totalinwords"><?= __('Total : Twenty Five Thousand Seven Hundred Seventy Eight'); ?></p>

                    <!-- Signature and QR -->
                    <div style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                      <div class="qr">
                        <img width="120px" src="https://pngimg.com/uploads/qr_code/small/qr_code_PNG33.png" alt="<?= htmlspecialchars(__('qr'), ENT_QUOTES, 'UTF-8'); ?>">
                      </div>
                      <div class="signature">
                        <h6 style="text-decoration: underline;margin-bottom: 5px;"><?= __('Autherized Signature') ?> :</h6>
                        <img width="180px"
                          src="https://onlinepngtools.com/images/examples-onlinepngtools/george-walker-bush-signature.png"
                          alt="<?= htmlspecialchars(__('signature'), ENT_QUOTES, 'UTF-8'); ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-2">
              <button name="submitinvoice" type="submit" class="btn btn-primary mt-3 w-100" ><?= __('Save Changes') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const saveSettings = () => {
    const settings = {
      fontFamily: fontFamilySelect.value,
      margin: marginInput.value,
      padding: paddingInput.value,
      height: heightInput.value,
      borderStyle: borderStyleSelect.value,
      borderColor: borderColorInput.value,
      borderWidth: borderWidthInput.value,
      showLogo: showLogoCheckbox.checked,
      logoPosition: logoPositionSelect.value,
      logoSize: logoSizeSelect.value,
      customLogoSize: customLogoSizeInput.value,
      showAddress: showAddressCheckbox.checked,
      addressTextSize: addressSizeSelect.value,
      showFirstAddress: showFirstAddressCheckbox.checked,
      showSecondAddress: showSecondAddressCheckbox.checked,
      showThirdAddress: showThirdAddressCheckbox.checked,
    };
    localStorage.setItem('invoiceSettings', JSON.stringify(settings));
  };

  const updatePreview = () => {
    previewDiv.style.fontFamily = fontFamilySelect.value || 'Arial';
    previewDiv.style.margin = `${marginInput.value || '0'}px`;
    previewDiv.style.padding = `${paddingInput.value || '20'}px`;
    previewDiv.style.minHeight = `${heightInput.value || '400'}px`;

    document.querySelectorAll('.bordered').forEach((el) => {
      el.style.borderStyle = borderStyleSelect.value || 'none';
      el.style.borderColor = borderColorInput.value || '#000000';
      el.style.borderWidth = `${borderWidthInput.value || '1'}px`;
    });

    addressSection.style.display = showAddressCheckbox.checked ? 'block' : 'none';
    addressSection.style.fontSize =
      addressSizeSelect.value === 'small'
        ? '12px'
        : addressSizeSelect.value === 'medium'
          ? '16px'
          : '20px';

    firstAddress.style.display = showFirstAddressCheckbox.checked ? 'block' : 'none';
    secondAddress.style.display = showSecondAddressCheckbox.checked ? 'block' : 'none';
    thirdAddress.style.display = showThirdAddressCheckbox.checked ? 'block' : 'none';

    logoPreview.style.display = showLogoCheckbox.checked ? 'block' : 'none';

    if (logoSizeSelect.value === 'custom') {
      customLogoSizeInputContainer.style.display = 'block';
      const customSize = parseInt(customLogoSizeInput.value, 10) || 80;
      logoPreview.style.maxHeight = `${customSize}px`;
    } else {
      customLogoSizeInputContainer.style.display = 'none';
      logoPreview.style.maxHeight =
        logoSizeSelect.value === 'small'
          ? '40px'
          : logoSizeSelect.value === 'medium'
            ? '80px'
            : '95px';
    }

    updateLogoPosition();
    saveSettings();
  };

  const updateLogoPosition = () => {
    const position = logoPositionSelect.value;
    logoPreview.style.position = 'absolute';
    const invoiceElement = document.querySelector('.top-header .invoice-no');

    switch (position) {
      case 'left':
        logoPreview.style.left = '0';
        logoPreview.style.right = 'auto';
        logoPreview.style.transform = 'none';
        invoiceElement.style.display = 'block';
        invoiceElement.style.textAlign = 'right';
        break;

      case 'center':
        logoPreview.style.left = '50%';
        logoPreview.style.transform = 'translateX(-50%)';
        invoiceElement.style.display = 'block';
        invoiceElement.style.textAlign = 'center';
        logoPreview.style.position = 'relative';
        logoPreview.style.padding = '0 0 10px 0';
        break;

      case 'right':
        logoPreview.style.left = 'auto';
        logoPreview.style.right = '0';
        logoPreview.style.transform = 'none';
        invoiceElement.style.display = 'block';
        invoiceElement.style.textAlign = 'left';
        break;
    }
  };

  const initializeCustomLogoSize = () => {
    logoSizeSelect.addEventListener('change', () => {
      if (logoSizeSelect.value === 'custom') {
        customLogoSizeInputContainer.style.display = 'block';
      } else {
        customLogoSizeInputContainer.style.display = 'none';
      }
      updatePreview();
    });

    customLogoSizeInput.addEventListener('input', updatePreview);
  };

  const previewDiv = document.querySelector('.invoice-preview');
  const fontFamilySelect = document.getElementById('fontFamily');
  const marginInput = document.getElementById('marginInput');
  const paddingInput = document.getElementById('paddingInput');
  const heightInput = document.getElementById('heightInput');
  const borderStyleSelect = document.getElementById('borderStyle');
  const borderColorInput = document.getElementById('borderColor');
  const borderWidthInput = document.getElementById('borderWidth');
  const logoPreview = document.getElementById('logoPreview');
  const showLogoCheckbox = document.getElementById('showLogo');
  const logoPositionSelect = document.getElementById('logoPositionSelect');
  const logoSizeSelect = document.getElementById('logoSizeSelect');
  const customLogoSizeInput = document.getElementById('customLogoSizeInput');
  const customLogoSizeInputContainer = document.getElementById('customLogoSizeInputContainer');
  const showAddressCheckbox = document.getElementById('showAddress');
  const addressSizeSelect = document.getElementById('addressSizeSelect');
  const addressSection = document.querySelector('.address-sec');
  const firstAddress = document.querySelector('.first_address');
  const secondAddress = document.querySelector('.second_address');
  const thirdAddress = document.querySelector('.third_address');
  const showFirstAddressCheckbox = document.getElementById('showFirstAddress');
  const showSecondAddressCheckbox = document.getElementById('showSecondAddress');
  const showThirdAddressCheckbox = document.getElementById('showThirdAddress');

  fontFamilySelect.addEventListener('change', updatePreview);
  marginInput.addEventListener('input', updatePreview);
  paddingInput.addEventListener('input', updatePreview);
  heightInput.addEventListener('input', updatePreview);
  borderStyleSelect.addEventListener('change', updatePreview);
  borderColorInput.addEventListener('input', updatePreview);
  borderWidthInput.addEventListener('input', updatePreview);
  showLogoCheckbox.addEventListener('change', updatePreview);
  logoPositionSelect.addEventListener('change', updatePreview);
  showAddressCheckbox.addEventListener('change', updatePreview);
  addressSizeSelect.addEventListener('change', updatePreview);
  showFirstAddressCheckbox.addEventListener('change', updatePreview);
  showSecondAddressCheckbox.addEventListener('change', updatePreview);
  showThirdAddressCheckbox.addEventListener('change', updatePreview);

  window.onload = () => {
    initializeCustomLogoSize();
    updateLogoPosition();
  };

  const saveAddressSettings = () => {
    const settings = {
      showFirstAddress: document.getElementById('showFirstAddress').checked,
      firstAddressValue: document.getElementById('address1Select').value,
      firstAddressHeading: document.getElementById('inputheading1').value,
      firstAddressPosition: document.getElementById('address1position').value,
      // om address part
      showFirstAddressField: document.getElementById('showFirstAddressField').checked,
      showFirstPincodeField: document.getElementById('showFirstPincodeField').checked,
      showFirstGstField: document.getElementById('showFirstGstField').checked,
      showFirstPanField: document.getElementById('showFirstPanField').checked,

      showSecondAddress: document.getElementById('showSecondAddress').checked,
      secondAddressValue: document.getElementById('address2Select').value,
      secondAddressHeading: document.getElementById('inputheading2').value,
      secondAddressPosition: document.getElementById('address2position').value,
      // om address part
      showSecondAddressField: document.getElementById('showSecondAddressField').checked,
      showSecondPincodeField: document.getElementById('showSecondPincodeField').checked,
      showSecondGstField: document.getElementById('showSecondGstField').checked,
      showSecondPanField: document.getElementById('showSecondPanField').checked,

      showThirdAddress: document.getElementById('showThirdAddress').checked,
      thirdAddressValue: document.getElementById('address3Select').value,
      thirdAddressHeading: document.getElementById('inputheading3').value,
      thirdAddressPosition: document.getElementById('address3position').value,
      // om address part
      showThirdAddressField: document.getElementById('showThirdAddressField').checked,
      showThirdPincodeField: document.getElementById('showThirdPincodeField').checked,
      showThirdGstField: document.getElementById('showThirdGstField').checked,
      showThirdPanField: document.getElementById('showThirdPanField').checked,
    };

    localStorage.setItem('addressSettings', JSON.stringify(settings));
  };

  const updateAddressPreview = () => {
    const showFirst = document.getElementById('showFirstAddress').checked;
    const showSecond = document.getElementById('showSecondAddress').checked;
    const showThird = document.getElementById('showThirdAddress').checked;

    document.querySelector('.first_address').style.display = showFirst ? 'block' : 'none';
    document.getElementById('address1Select').style.display = showFirst ? 'block' : 'none';
    document.getElementById('inputheading1').style.display = showFirst ? 'block' : 'none';
    document.querySelector('.first_address .address1heading').style.display = showFirst ? 'block' : 'none';
    document.querySelector('.first_address .address1heading').textContent = showFirst
      ? document.getElementById('inputheading1').value
      : '';
    const firstAddress = document.querySelector('.first_address');
    firstAddress.style.display = showFirst ? 'block' : 'none';
    firstAddress.style.order =
      document.getElementById('address1position').value === 'left'
        ? '0'
        : document.getElementById('address1position').value === 'center'
          ? '1'
          : '2';

    document.querySelector('.second_address').style.display = showSecond ? 'block' : 'none';
    document.getElementById('address2Select').style.display = showSecond ? 'block' : 'none';
    document.getElementById('inputheading2').style.display = showSecond ? 'block' : 'none';
    document.querySelector('.second_address .address2heading').style.display = showSecond ? 'block' : 'none';
    document.querySelector('.second_address .address2heading').textContent = showSecond
      ? document.getElementById('inputheading2').value
      : '';
    const secondAddress = document.querySelector('.second_address');
    secondAddress.style.display = showSecond ? 'block' : 'none';
    secondAddress.style.order =
      document.getElementById('address2position').value === 'left'
        ? '0'
        : document.getElementById('address2position').value === 'center'
          ? '1'
          : '2';

    document.querySelector('.third_address').style.display = showThird ? 'block' : 'none';
    document.getElementById('address3Select').style.display = showThird ? 'block' : 'none';
    document.getElementById('inputheading3').style.display = showThird ? 'block' : 'none';
    document.querySelector('.third_address .address3heading').style.display = showThird ? 'block' : 'none';
    document.querySelector('.third_address .address3heading').textContent = showThird
      ? document.getElementById('inputheading3').value
      : '';
    const thirdAddress = document.querySelector('.third_address');
    thirdAddress.style.display = showThird ? 'block' : 'none';
    thirdAddress.style.order =
      document.getElementById('address3position').value === 'left'
        ? '0'
        : document.getElementById('address3position').value === 'center'
          ? '1'
          : '2';
    saveAddressSettings();
  };

  document.getElementById('showFirstAddress').addEventListener('change', updateAddressPreview);
  document.getElementById('showSecondAddress').addEventListener('change', updateAddressPreview);
  document.getElementById('showThirdAddress').addEventListener('change', updateAddressPreview);

  document.getElementById('address1Select').addEventListener('change', updateAddressPreview);
  document.getElementById('address2Select').addEventListener('change', updateAddressPreview);
  document.getElementById('address3Select').addEventListener('change', updateAddressPreview);

  document.getElementById('inputheading1').addEventListener('input', updateAddressPreview);
  document.getElementById('inputheading2').addEventListener('input', updateAddressPreview);
  document.getElementById('inputheading3').addEventListener('input', updateAddressPreview);

  document.getElementById('address1position').addEventListener('change', updateAddressPreview);
  document.getElementById('address2position').addEventListener('change', updateAddressPreview);
  document.getElementById('address3position').addEventListener('change', updateAddressPreview);

   // om address part
  document.getElementById('showFirstAddressField').addEventListener('change', saveAddressSettings);
  document.getElementById('showFirstPincodeField').addEventListener('change', saveAddressSettings);
  document.getElementById('showFirstGstField').addEventListener('change', saveAddressSettings);
  document.getElementById('showFirstPanField').addEventListener('change', saveAddressSettings);

  // om address part
  document.getElementById('showSecondAddressField').addEventListener('change', saveAddressSettings);
  document.getElementById('showSecondPincodeField').addEventListener('change', saveAddressSettings);
  document.getElementById('showSecondGstField').addEventListener('change', saveAddressSettings);
  document.getElementById('showSecondPanField').addEventListener('change', saveAddressSettings);

  // om address part
  document.getElementById('showThirdAddressField').addEventListener('change', saveAddressSettings);
  document.getElementById('showThirdPincodeField').addEventListener('change', saveAddressSettings);
  document.getElementById('showThirdGstField').addEventListener('change', saveAddressSettings);
  document.getElementById('showThirdPanField').addEventListener('change', saveAddressSettings);

  document.addEventListener('DOMContentLoaded', () => {
    saveAddressSettings();
    updateAddressPreview();
  });

  const dynamicOptions = document.querySelectorAll('.dynamic-options');
  dynamicOptions.forEach((option) => {
    option.style.display = 'none';
  });

  document.querySelectorAll('.btn[data-target]').forEach((button) => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-target');
      const targetOption = document.getElementById(targetId);

      if (targetOption) {
        const isVisible = targetOption.style.display === 'block';
        targetOption.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
          dynamicOptions.forEach((option) => {
            if (option.id !== targetId) {
              option.style.display = 'none';
            }
          });
        }
      }
    });
  });

  const saveTopHeaderSettings = () => {
    const settings = {
      showInvoiceNo: document.getElementById('showinvoiceno').checked,
      invoiceNoValue: document.getElementById('invoicenoSelect')?.value,
      invoiceNoHeading: document.getElementById('invoicenoheading').value,

      showDate: document.getElementById('showdate').checked,
      dateValue: document.getElementById('invoicedateSelect')?.value,
      dateHeading: document.getElementById('dateheading').value,

      showDueDate: document.getElementById('showduedate').checked,
      dueDateValue: document.getElementById('invoiceduedateSelect')?.value,
      dueDateHeading: document.getElementById('duedateheading').value,

      showPoNum: document.getElementById('showpo_num').checked,
      poNumValue: document.getElementById('invoiceponumSelect')?.value,
      poNumHeading: document.getElementById('po_numheading').value,

      showPoDate: document.getElementById('showpo_date').checked,
      poDateValue: document.getElementById('invoicepodateSelect')?.value,
      poDateHeading: document.getElementById('po_dateheading').value,

      // om new
      showName: document.getElementById('showname').checked,
      nameValue: document.getElementById('invoicenameSelect')?.value,
      nameHeading: document.getElementById('nameheading').value,

      showEmail: document.getElementById('showemail').checked,
      emailValue: document.getElementById('invoiceemailSelect')?.value,
      emailHeading: document.getElementById('emailheading').value,

      showPhone: document.getElementById('showphone').checked,
      phoneValue: document.getElementById('invoicephoneSelect')?.value,
      phoneHeading: document.getElementById('phoneheading').value,

    };

    localStorage.setItem('topHeaderSettings', JSON.stringify(settings));
  };

  const updateTopHeaderPreview = () => {
    const elements = [
      {
        checkboxId: 'showinvoiceno',
        selectId: 'invoicenoSelect',
        headingId: 'invoicenoheading',
        previewSelector: '.invoice-no .invoice span',
        defaultLabel: 'Invoice No',
      },
      {
        checkboxId: 'showdate',
        selectId: 'invoicedateSelect',
        headingId: 'dateheading',
        previewSelector: '.invoice-no .date span',
        defaultLabel: 'Date',
      },
      {
        checkboxId: 'showduedate',
        selectId: 'invoiceduedateSelect',
        headingId: 'duedateheading',
        previewSelector: '.invoice-no .due_date span',
        defaultLabel: 'Due Date',
      },
      {
        checkboxId: 'showpo_num',
        selectId: 'invoiceponumSelect',
        headingId: 'po_numheading',
        previewSelector: '.invoice-no .po_num span',
        defaultLabel: 'P.O. Number',
      },
      {
        checkboxId: 'showpo_date',
        selectId: 'invoicepodateSelect',
        headingId: 'po_dateheading',
        previewSelector: '.invoice-no .po_date span',
        defaultLabel: 'P.O Date',
      },
      // om new
      {
        checkboxId: 'showname',
        selectId: 'invoicenameSelect',
        headingId: 'nameheading',
        previewSelector: '.invoice-no .name span',
        defaultLabel: 'Name',
      },
      {
        checkboxId: 'showemail',
        selectId: 'invoiceemailSelect',
        headingId: 'emailheading',
        previewSelector: '.invoice-no .email span',
        defaultLabel: 'Email',
      },
      {
        checkboxId: 'showphone',
        selectId: 'invoicephoneSelect',
        headingId: 'phoneheading',
        previewSelector: '.invoice-no .phone span',
        defaultLabel: 'Phone',
      },

    ];

    elements.forEach(({ checkboxId, selectId, headingId, previewSelector, defaultLabel }) => {
      const checkbox = document.getElementById(checkboxId);
      const select = document.getElementById(selectId);
      const headingInput = document.getElementById(headingId);
      const preview = document.querySelector(previewSelector);

      if (select) select.style.display = checkbox.checked ? 'block' : 'none';
      if (checkbox.checked) {
        const heading = headingInput?.value?.trim() || defaultLabel;
        preview.textContent = `${heading}: xxxx`;
        preview.parentElement.style.display = 'block';
      } else {
        preview.parentElement.style.display = 'none';
      }
    });

    saveTopHeaderSettings();
  };

  const setupEventListeners = () => {
    document.querySelectorAll('.form-check-input, .form-select, input[type="text"]').forEach((el) => {
      el.addEventListener('input', updateTopHeaderPreview);
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    updateTopHeaderPreview();
  });

  const toggleTableVisibility = () => {
    const table = document.querySelector('.table');
    const showTableCheckbox = document.getElementById('showTable');
    table.style.display = showTableCheckbox.checked ? 'table' : 'none';
  };

  const toggleColumnVisibility = () => {
    const columns = {
      image: document.querySelectorAll('.table .img'),
      item: document.querySelectorAll('.table .item'),
      des: document.querySelectorAll('.table .des'),
      rate: document.querySelectorAll('.table .rate'),
      qty: document.querySelectorAll('.table .qty'),
      discount: document.querySelectorAll('.table .discount'),
      amount: document.querySelectorAll('.table .amount'),
      hsnsac: document.querySelectorAll('.table .hsnsac'),
      uom: document.querySelectorAll('.table .uom'),
      tax: document.querySelectorAll('.table .tax'),
      taxsum: document.querySelectorAll('.table .tax-summary'),
    };

    const columnCheckboxes = [
      { id: 'showimagecol', key: 'image' },
      { id: 'showitemcol', key: 'item' },
      { id: 'showdescol', key: 'des' },
      { id: 'showratecol', key: 'rate' },
      { id: 'showqtycol', key: 'qty' },
      { id: 'showdiscountcol', key: 'discount' },
      { id: 'showamountcol', key: 'amount' },
      { id: 'showhsnsaccol', key: 'hsnsac' },
      { id: 'showuomcol', key: 'uom' },
      { id: 'showtaxcol', key: 'tax' },
      { id: 'showtaxsumcol', key: 'taxsum' }
    ];

    columnCheckboxes.forEach(({ id, key }) => {
      const checkbox = document.getElementById(id);
      if (checkbox) {
        checkbox.addEventListener('change', (e) => {
          columns[key].forEach((col) => (col.style.display = e.target.checked ? '' : 'none'));
        });
      }
    });
  };

  const saveTableSettings = () => {
    const settings = {
      showTable: document.getElementById('showTable').checked,
      showColumns: {
        img: document.getElementById('showimagecol').checked,
        item: document.getElementById('showitemcol').checked,
        rate: document.getElementById('showratecol').checked,
        qty: document.getElementById('showqtycol').checked,
        discount: document.getElementById('showdiscountcol').checked,
        amount: document.getElementById('showamountcol').checked,
        des: document.getElementById('showdescol').checked,
        hsnsac: document.getElementById('showhsnsaccol').checked,
        uom: document.getElementById('showuomcol').checked,
        tax: document.getElementById('showtaxcol').checked,
        taxsum: document.getElementById('showtaxsumcol').checked,
      },
    };
    localStorage.setItem('tableSettings', JSON.stringify(settings));
  };

  document.getElementById('showTable').addEventListener('change', toggleTableVisibility);
  toggleColumnVisibility();

  document.querySelectorAll('#showTable, .form-check-input').forEach((checkbox) => {
    checkbox.addEventListener('change', saveTableSettings);
  });

  document.addEventListener('DOMContentLoaded', () => {
    toggleTableVisibility();
    toggleColumnVisibility();
    saveTableSettings();
  });

  const updateBodyContent = (columnClass, newValue) => {
    document.querySelectorAll(`tbody tr.data .${columnClass}`).forEach((cell) => {
      const originalValue = cell.getAttribute('data-original');
      cell.textContent = newValue || originalValue;
    });
  };

  const manageDynamicColumnsSelect = () => {
    const selectElements = {
      showimagecol: { columnClass: 'img', selectId: 'itemimageSelect' },
      showitemcol: { columnClass: 'item', selectId: 'itemnameSelect' },
      showdescol: { columnClass: 'des', selectId: 'itemdescriptionSelect' },
      showhsnsaccol: { columnClass: 'hsnsac', selectId: 'itemhsnsacSelect' },
      showuomcol: { columnClass: 'uom', selectId: 'itemuomSelect' },
      showtaxcol: { columnClass: 'tax', selectId: 'itemtaxSelect' },
      showratecol: { columnClass: 'rate', selectId: 'itemrateSelect' },
      showqtycol: { columnClass: 'qty', selectId: 'itemqtySelect' },
      showdiscountcol: { columnClass: 'discount', selectId: 'itemdiscountSelect' },
      showamountcol: { columnClass: 'amount', selectId: 'itemamountSelect' },
    };

    const saveSelectHeadersToLocalStorage = () => {
      const headers = {};
      Object.keys(selectElements).forEach((key) => {
        const select = document.getElementById(selectElements[key].selectId);
        if (select) {
          headers[key] = select.value || '';
        }
      });
      localStorage.setItem('tableSelectHeaders', JSON.stringify(headers));
    };

    document.addEventListener('DOMContentLoaded', () => {
      const storedHeaders = JSON.parse(localStorage.getItem('tableSelectHeaders')) || {};

      Object.keys(selectElements).forEach((key) => {
        const checkbox = document.getElementById(key);
        const select = document.getElementById(selectElements[key].selectId);

        if (checkbox && select) {
          select.style.display = checkbox.checked ? 'block' : 'none';
          if (storedHeaders[key]) {
            select.value = storedHeaders[key];
          }
          checkbox.addEventListener('change', () => {
            select.style.display = checkbox.checked ? 'block' : 'none';
            saveSelectHeadersToLocalStorage();
          });
          select.addEventListener('change', saveSelectHeadersToLocalStorage);
        }
      });
      saveSelectHeadersToLocalStorage();
    });
  };

  manageDynamicColumnsSelect();

  const manageDynamicColumns = () => {
    const columnInputs = {
      showimagecol: { columnClass: 'img', inputId: 'imagecolumnInput' },
      showitemcol: { columnClass: 'item', inputId: 'itemcolumnInput' },
      showdescol: { columnClass: 'des', inputId: 'descolumnInput' },
      showhsnsaccol: { columnClass: 'hsnsac', inputId: 'hsnsaccolumnInput' },
      showuomcol: { columnClass: 'uom', inputId: 'uomcolumnInput' },
      showigstcol: { columnClass: 'igst', inputId: 'igstcolumnInput' },
      showtaxcol: { columnClass: 'tax', inputId: 'taxcolumnInput' },
      showratecol: { columnClass: 'rate', inputId: 'ratecolumnInput' },
      showqtycol: { columnClass: 'qty', inputId: 'qtycolumnInput' },
      showdiscountcol: { columnClass: 'discount', inputId: 'discountcolumnInput' },
      showamountcol: { columnClass: 'amount', inputId: 'amountcolumnInput' },
    };

    const theadCells = document.querySelectorAll('thead td');

    const saveHeaderToLocalStorage = () => {
      const headers = {};
      Object.keys(columnInputs).forEach((key) => {
        const input = document.getElementById(columnInputs[key].inputId);
        if (input) {
          headers[key] = input.value || '';
        }
      });
      localStorage.setItem('tableHeaders', JSON.stringify(headers));
    };
    document.addEventListener('DOMContentLoaded', () => {
      const storedHeaders = JSON.parse(localStorage.getItem('tableHeaders')) || {};

      Object.keys(columnInputs).forEach((key) => {
        const checkbox = document.getElementById(key);
        const input = document.getElementById(columnInputs[key].inputId);
        const columnClass = columnInputs[key].columnClass;

        if (checkbox && input) {
          input.style.display = checkbox.checked ? 'block' : 'none';
          document.querySelectorAll(`.${columnClass}`).forEach((col) => {
            col.style.display = checkbox.checked ? '' : 'none';
          });

          if (storedHeaders[key]) {
            input.value = storedHeaders[key];
            theadCells.forEach((cell) => {
              if (cell.classList.contains(columnClass)) {
                cell.querySelector('strong').textContent = storedHeaders[key];
              }
            });
          }

          checkbox.addEventListener('change', () => {
            input.style.display = checkbox.checked ? 'block' : 'none';
            document.querySelectorAll(`.${columnClass}`).forEach((col) => {
              col.style.display = checkbox.checked ? '' : 'none';
            });
            saveHeaderToLocalStorage();
          });

          input.addEventListener('input', () => {
            theadCells.forEach((cell) => {
              if (cell.classList.contains(columnClass)) {
                cell.querySelector('strong').textContent = input.value;
              }
            });
            saveHeaderToLocalStorage();
          });
        }
      });
      saveHeaderToLocalStorage();
    });
  };

  manageDynamicColumns();

  const applyTableAppearance = () => {
    const table = document.querySelector('.table');
    if (!table) return;
    const tableHeader = table.querySelector('thead');
    const tableBody = table.querySelector('tbody');
    const tableFooter = table.querySelector('tfoot');
    const rows = table.querySelectorAll('tr');

    const headerBgColor = document.getElementById('tableHeaderbgColor').value;
    const headerTextColor = document.getElementById('tableHeaderColor').value;
    const bodyBgColor = document.getElementById('tableBodybgColor').value;
    const bodyTextColor = document.getElementById('tableBodyColor').value;
    const footerBgColor = document.getElementById('tableFooterbgColor').value;
    const footerTextColor = document.getElementById('tableFooterColor').value;
    const rowHeight = document.getElementById('rowHeight').value || 0;
    const headingHeight = document.getElementById('headingHeight').value || 0;
    const fontSize = document.getElementById('fontSize').value || 0;

    if (headerBgColor && tableHeader) tableHeader.style.backgroundColor = headerBgColor;
    if (headerTextColor && tableHeader) tableHeader.querySelectorAll('td, th').forEach(td => (td.style.color = headerTextColor));

    if (bodyBgColor && tableBody) tableBody.style.backgroundColor = bodyBgColor;
    if (bodyTextColor && tableBody) tableBody.querySelectorAll('td').forEach(td => (td.style.color = bodyTextColor));

    if (footerBgColor && tableFooter) tableFooter.style.backgroundColor = footerBgColor;
    if (footerTextColor && tableFooter) tableFooter.querySelectorAll('td').forEach(td => (td.style.color = footerTextColor));

    if (rowHeight > 0) rows.forEach(row => (row.style.height = `${rowHeight}px`));
    if (headingHeight > 0 && tableHeader) tableHeader.style.height = `${headingHeight}px`;

    if (fontSize > 0) table.style.fontSize = `${fontSize}px`;

    const appearanceSettings = {
      headerBgColor,
      headerTextColor,
      bodyBgColor,
      bodyTextColor,
      footerBgColor,
      footerTextColor,
      rowHeight,
      headingHeight,
      fontSize,
    };
    localStorage.setItem('tableAppearance', JSON.stringify(appearanceSettings));
  };

  document.addEventListener('DOMContentLoaded', () => {
    applyTableAppearance();
    const inputs = document.querySelectorAll('#tableAppearance input');
    inputs.forEach(input => input.addEventListener('input', applyTableAppearance));
  });

  document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody .data');
    const input = document.getElementById('itemsInput');

    const updateRowVisibility = (count) => {
      rows.forEach((row, index) => {
        row.style.display = index < count ? '' : 'none';
      });
    };

    const savedCount = localStorage.getItem('tableItemsVisibility');
    const initialCount = savedCount ? parseInt(savedCount, 10) : rows.length;
    input.value = initialCount;
    updateRowVisibility(initialCount);

    input.addEventListener('input', () => {
      const count = parseInt(input.value, 10) || rows.length;
      updateRowVisibility(count);
      localStorage.setItem('tableItemsVisibility', count);
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const showTotalAlphaCheckbox = document.getElementById('showtotalinalpha');
    const showSignatureCheckbox = document.getElementById('showsignature');
    const showQrCheckbox = document.getElementById('showqr');
    const showFooterCheckbox = document.getElementById('showpagefooter');
    const showBankDetailsCheckbox = document.getElementById('showbankdetails');
    const showAdditionalDetailsCheckbox = document.getElementById('showadditionaldetails');
    const totalAlpha = document.querySelector('.totalinwords');
    const signature = document.querySelector('.signature');
    const qr = document.querySelector('.qr');
    const footer = document.querySelector('.page_footer');
    const bankDetailsSection = document.querySelector('.bankdetails');
    const additionalNotesSection = document.querySelector('.additionalnotes');
    const toggleVisibility = (element, isChecked) => {
      if (element) {
        element.style.display = isChecked ? 'block' : 'none';
      }
    };
    showTotalAlphaCheckbox.addEventListener('change', () => toggleVisibility(totalAlpha, showTotalAlphaCheckbox.checked));
    showSignatureCheckbox.addEventListener('change', () => toggleVisibility(signature, showSignatureCheckbox.checked));
    showQrCheckbox.addEventListener('change', () => toggleVisibility(qr, showQrCheckbox.checked));
    showFooterCheckbox.addEventListener('change', () => toggleVisibility(footer, showFooterCheckbox.checked));

    toggleVisibility(totalAlpha, showTotalAlphaCheckbox.checked);
    toggleVisibility(signature, showSignatureCheckbox.checked);
    toggleVisibility(qr, showQrCheckbox.checked);
    toggleVisibility(footer, showFooterCheckbox.checked);

    const footerKey = 'footerSettings';
    const fields = {
      totalbeforetax: { elements: document.querySelectorAll('.totalbeforetax'), headingInput: 'heading1', valueSelect: 'value1', checkbox: 'showtotalbeforetax' },
      discount: { elements: document.querySelectorAll('.fdiscount'), headingInput: 'heading2', valueSelect: 'value2', checkbox: 'showdiscount' },
      totaltaxamount: { elements: document.querySelectorAll('.totaltaxamount'), headingInput: 'heading3', valueSelect: 'value3', checkbox: 'showtotaltaxamount' },
      roundedoff: { elements: document.querySelectorAll('.roundedoff'), headingInput: 'heading4', valueSelect: 'value4', checkbox: 'showroundedoff' },
      securityamount: { elements: document.querySelectorAll('.securityamount'), headingInput: 'heading4_5', valueSelect: 'value4_5', checkbox: 'showsecurityamount' },
      totalamount: { elements: document.querySelectorAll('.totalamount'), headingInput: 'heading5', valueSelect: 'value5', checkbox: 'showtotalamount' },
      totaldue: { elements: document.querySelectorAll('.totaldue'), headingInput: 'heading6', valueSelect: 'value6', checkbox: 'showtotaldue' },
    };
    const positions = {
      signatureqr: { element: document.querySelector('.signatureqr'), positionSelect: 'signatureqr1position' },
      invoicesummary: { element: document.querySelector('.invoicesummary'), positionSelect: 'invoicesummary2position' },
    };
    const loadFooterSettings = () => {
      const savedSettings = localStorage.getItem(footerKey);
      if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        if (settings.showFields) {
          Object.keys(settings.showFields).forEach((key) => {
            const fieldSettings = settings.showFields[key];
            const checkbox = document.getElementById(fields[key]?.checkbox);
            const headingInput = document.getElementById(fields[key]?.headingInput);
            const valueSelect = document.getElementById(fields[key]?.valueSelect);

            if (checkbox && fieldSettings.visible !== undefined) {
              checkbox.checked = fieldSettings.visible;
              toggleFieldVisibility(key, fieldSettings.visible);
            }
            if (headingInput && fieldSettings.heading) {
              headingInput.value = fieldSettings.heading;
              updatePreview(key);
            }
            if (valueSelect && fieldSettings.value) {
              valueSelect.value = fieldSettings.value;
            }
          });
        }
      }
    };

    const saveFooterSettings = () => {
      const settings = {
        showTotalAlpha: showTotalAlphaCheckbox.checked,
        showSignature: showSignatureCheckbox.checked,
        showQr: showQrCheckbox.checked,
        showBankDetails: showBankDetailsCheckbox.checked,
        showAdditionalDetails: showAdditionalDetailsCheckbox.checked,
        showFooter: showFooterCheckbox.checked,
        showFields: Object.keys(fields).reduce((acc, key) => {
          const checkbox = document.getElementById(fields[key].checkbox);
          const headingInput = document.getElementById(fields[key].headingInput);
          const valueSelect = document.getElementById(fields[key].valueSelect);

          if (checkbox && headingInput && valueSelect) {
            acc[key] = {
              visible: checkbox.checked,
              heading: headingInput.value,
              value: valueSelect.value,
            };
          }

          return acc;
        }, {}),
        positions: Object.keys(positions).reduce((acc, key) => {
          const positionSelect = document.getElementById(positions[key].positionSelect);

          if (positionSelect) {
            acc[key] = positionSelect.value;
          }

          return acc;
        }, {}),
      };
      localStorage.setItem(footerKey, JSON.stringify(settings));
    };

    showTotalAlphaCheckbox.addEventListener('change', () => {
      toggleVisibility(totalAlpha, showTotalAlphaCheckbox.checked);
      saveFooterSettings();
    });

    showSignatureCheckbox.addEventListener('change', () => {
      toggleVisibility(signature, showSignatureCheckbox.checked);
      saveFooterSettings();
    });

    showQrCheckbox.addEventListener('change', () => {
      toggleVisibility(qr, showQrCheckbox.checked);
      saveFooterSettings();
    });

    showBankDetailsCheckbox.addEventListener('change', () => {
      toggleVisibility(bankDetailsSection, showBankDetailsCheckbox.checked);
      saveFooterSettings();
    });

    showAdditionalDetailsCheckbox.addEventListener('change', () => {
      toggleVisibility(additionalNotesSection, showAdditionalDetailsCheckbox.checked);
      saveFooterSettings();
    });
    const updatePreview = (key) => {
      const field = fields[key];
      const headingValue = document.getElementById(field.headingInput).value;

      field.elements.forEach((el) => {
        const headingClass = Array.from(el.classList).find((cls) => cls.startsWith('heading'));
        if (headingClass) {
          el.textContent = headingValue + ':';
        }
      });

      saveFooterSettings();
    };

    const toggleFieldVisibility = (key, isVisible) => {
      const field = fields[key];
      field.elements.forEach((el) => {
        el.style.display = isVisible ? 'block' : 'none';
      });
      const headingInput = document.getElementById(field.headingInput);
      const valueSelect = document.getElementById(field.valueSelect);
      if (headingInput) {
        headingInput.style.display = isVisible ? 'block' : 'none';
      }
      if (valueSelect) {
        valueSelect.style.display = isVisible ? 'block' : 'none';
        if (isVisible) {
          valueSelect.style.visibility = 'visible';
          valueSelect.style.opacity = '1';
        }
      }
    };

    const updatePosition = (key) => {
      const position = positions[key];
      const positionValue = document.getElementById(position.positionSelect).value;
      position.element.style.order = positionValue === 'left' ? '0' : '1';
      saveFooterSettings();
    };

    loadFooterSettings();
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const checkbox = document.getElementById(field.checkbox);
      if (checkbox) {
        const valueSelect = document.getElementById(field.valueSelect);
        if (valueSelect && checkbox.checked) {
          valueSelect.style.display = 'block';
        }
        toggleFieldVisibility(key, checkbox.checked);
      }
    });
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const headingInput = document.getElementById(field.headingInput);
      const valueSelect = document.getElementById(field.valueSelect);
      const checkbox = document.getElementById(field.checkbox);

      if (headingInput) {
        headingInput.addEventListener('input', () => updatePreview(key));
      }
      if (valueSelect) {
        valueSelect.addEventListener('input', () => saveFooterSettings());
      }
      if (checkbox) {
        checkbox.addEventListener('change', (e) => {
          toggleFieldVisibility(key, e.target.checked);
          saveFooterSettings();
        });
      }
    });

    Object.keys(positions).forEach((key) => {
      const position = positions[key];
      document.getElementById(position.positionSelect).addEventListener('change', () => updatePosition(key));
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
      Object.keys(fields).forEach((key) => {
        const checkbox = document.getElementById(fields[key].checkbox);
        if (checkbox && checkbox.checked) {
          updatePreview(key);
        }
      });
      saveFooterSettings();
    }, 100);
  });
  // Template validation functions
  const validateTemplateName = () => {
    const templateNameInput = document.getElementById('template_name');
    const currentValue = templateNameInput.value.trim();

    if (!currentValue) {
      showValidationError('Please fill the template name before submit');
      return false;
    }

    hideValidationError();
    return true;
  };

  const validateShowLogo = (showError = true) => {
    const showLogoToggle = document.getElementById('showLogo');

    if (!showLogoToggle.checked) {
      if (showError) {
        showValidationError('Show Logo must be Show');
      }
      return false;
    }

    return true;
  };

  const validateTopHeaderOptions = (showError = true) => {
    const topHeaderToggles = [
      document.getElementById('showinvoiceno'),
      document.getElementById('showdate'),
      document.getElementById('showduedate'),
      document.getElementById('showpo_num'),
      document.getElementById('showpo_date'),
       // om new
      document.getElementById('showname'),
      document.getElementById('showemail'),
      document.getElementById('showphone'),
    ];

    const checkedCount = topHeaderToggles.filter(toggle => toggle && toggle.checked).length;

    if (checkedCount < 2) {
      if (showError) {
        showValidationError('Any two options must have to be Show in header details');
      }
      return false;
    }
    return true;
  };

  const validateHeaderOptions = (showError = true) => {
    const showHeaderToggle = document.getElementById('showAddress');

    if (!showHeaderToggle.checked) {
      if (showError) {
        showValidationError('Show Header must be enabled in Header Options');
      }
      return false;
    }

    return true;
  };

  const validateAddressOptions = (showError = true) => {
    const addressToggles = [
      document.getElementById('showFirstAddress'),
      document.getElementById('showSecondAddress'),
      document.getElementById('showThirdAddress')
    ];

    const checkedCount = addressToggles.filter(toggle => toggle && toggle.checked).length;

    if (checkedCount < 1) {
      if (showError) {
        showValidationError('At least one address must be selected from Billing Address, Shipping Address, or Company Address');
      }
      return false;
    }
    return true;
  };

  const validateTableOptions = (showError = true) => {
    const tableColumnToggles = [
      document.getElementById('showimagecol'),
      document.getElementById('showitemcol'),
      document.getElementById('showdescol'),
      document.getElementById('showhsnsaccol'),
      document.getElementById('showuomcol'),
      document.getElementById('showqtycol'),
      document.getElementById('showdiscountcol'),
      document.getElementById('showtaxcol'),
      document.getElementById('showratecol'),
      document.getElementById('showamountcol'),
      document.getElementById('showtaxsumcol')
    ];

    const checkedCount = tableColumnToggles.filter(toggle => toggle && toggle.checked).length;

    if (checkedCount < 4) {
      if (showError) {
        showValidationError('At least 4 table column options must be selected from the available table options');
      }
      return false;
    }
    return true;
  };

  const showValidationError = (message) => {
    const templateNameInput = document.getElementById('template_name');
    tostarnotify('top', 'right', 'fa fa-exclamation-triangle', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', message, '');
  };

  const hideValidationError = () => {
    // This function can be used to hide validation errors if needed
    // For now, we'll just clear any existing error states
  };


  document.addEventListener('DOMContentLoaded', () => {
    const templateNameInput = document.getElementById('template_name');
    const saveButton = document.querySelector('button[name="submitinvoice"]');

    // Real-time validation on input
    templateNameInput.addEventListener('input', () => {
      const currentValue = templateNameInput.value.trim();
      if (currentValue) {
        localStorage.setItem('templateName', currentValue);
        hideValidationError();
      } else {
        localStorage.removeItem('templateName');
      }
    });

    // Real-time validation for toggles
    const showLogoToggle = document.getElementById('showLogo');
    const topHeaderToggles = [
      document.getElementById('showinvoiceno'),
      document.getElementById('showdate'),
      document.getElementById('showduedate'),
      document.getElementById('showpo_num'),
      document.getElementById('showpo_date')
    ];

    const showHeaderToggle = document.getElementById('showAddress');
    const addressToggles = [
      document.getElementById('showFirstAddress'),
      document.getElementById('showSecondAddress'),
      document.getElementById('showThirdAddress')
    ];

    const tableColumnToggles = [
      document.getElementById('showimagecol'),
      document.getElementById('showitemcol'),
      document.getElementById('showdescol'),
      document.getElementById('showhsnsaccol'),
      document.getElementById('showuomcol'),
      document.getElementById('showqtycol'),
      document.getElementById('showdiscountcol'),
      document.getElementById('showtaxcol'),
      document.getElementById('showratecol'),
      document.getElementById('showamountcol'),
      document.getElementById('showtaxsumcol')
    ];

    // Add event listeners for all toggles
    if (showLogoToggle) {
      showLogoToggle.addEventListener('change', () => {
        validateFormState();
      });
    }

    topHeaderToggles.forEach(toggle => {
      if (toggle) {
        toggle.addEventListener('change', () => {
          validateFormState();
        });
      }
    });

    if (showHeaderToggle) {
      showHeaderToggle.addEventListener('change', () => {
        validateFormState();
      });
    }

    addressToggles.forEach(toggle => {
      if (toggle) {
        toggle.addEventListener('change', () => {
          validateFormState();
        });
      }
    });

    tableColumnToggles.forEach(toggle => {
      if (toggle) {
        toggle.addEventListener('change', () => {
          validateFormState();
        });
      }
    });

    // Function to validate overall form state
    const validateFormState = () => {
      const templateNameInput = document.getElementById('template_name');
      const templateValid = templateNameInput.value.trim() !== '';
      const logoValid = validateShowLogo(false);
      const headerValid = validateTopHeaderOptions(false);
      const showHeaderValid = validateHeaderOptions(false);
      const addressValid = validateAddressOptions(false);
      const tableValid = validateTableOptions(false);

      if (templateValid && logoValid && headerValid && showHeaderValid && addressValid && tableValid) {
        hideValidationError();
      }
    };

    // Initial validation check on page load
    validateFormState();

    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', (e) => {
      if (!validateTemplateName()) {
        e.preventDefault();
        return false;
      }

      if (!validateShowLogo()) {
        e.preventDefault();
        return false;
      }

      if (!validateTopHeaderOptions()) {
        e.preventDefault();
        return false;
      }

      if (!validateHeaderOptions()) {
        e.preventDefault();
        return false;
      }

      if (!validateAddressOptions()) {
        e.preventDefault();
        return false;
      }

      if (!validateTableOptions()) {
        e.preventDefault();
        return false;
      }
    });
  });
  const createHiddenFields = () => {
    const savedAddressSettings = localStorage.getItem("addressSettings");
    const savedFooterSettings = localStorage.getItem("footerSettings");
    const savedinvoiceData = localStorage.getItem("templateName");
    const savedinvoicePreview = localStorage.getItem("invoicePreview");
    const savedInvoiceSettings = localStorage.getItem("invoiceSettings");
    const savedTableAppearance = localStorage.getItem("tableAppearance");
    const savedTableHeaders = localStorage.getItem("tableHeaders");
    const savedTableSettings = localStorage.getItem("tableSettings");
    const savedTopHeaderSettings = localStorage.getItem("topHeaderSettings");
    const savedTableSelectHeaders = localStorage.getItem("tableSelectHeaders");
    const savedtableItemsVisibility = localStorage.getItem("tableItemsVisibility");
    const form = document.querySelector("form");
    document.querySelectorAll(".dynamic-hidden").forEach((el) => el.remove());
    const addHiddenFields = (settings, prefix = "") => {
      Object.keys(settings).forEach((key) => {
        const hiddenField = document.createElement("input");
        hiddenField.type = "hidden";
        hiddenField.name = prefix + key;
        hiddenField.value = settings[key];
        hiddenField.classList.add("dynamic-hidden");
        form.appendChild(hiddenField);
      });
    };

    if (savedInvoiceSettings) {
      const invoiceSettings = JSON.parse(savedInvoiceSettings);
      if (
        invoiceSettings.borderStyle &&
        invoiceSettings.borderColor &&
        invoiceSettings.borderWidth
      ) {
        const borderOptions = JSON.stringify({
          borderStyle: invoiceSettings.borderStyle,
          borderColor: invoiceSettings.borderColor,
          borderWidth: invoiceSettings.borderWidth,
        });
        const borderField = document.createElement("input");
        borderField.type = "hidden";
        borderField.name = "border_options";
        borderField.value = borderOptions;
        borderField.classList.add("dynamic-hidden");
        form.appendChild(borderField);
      }
      const basicOptionsKeys = ["fontFamily", "margin", "padding", "height"];
      const basicOptions = {};
      basicOptionsKeys.forEach((key) => {
        if (invoiceSettings[key] !== undefined) {
          basicOptions[key] = invoiceSettings[key];
        }
      });
      const basicOptionsField = document.createElement("input");
      basicOptionsField.type = "hidden";
      basicOptionsField.name = "basic_options";
      basicOptionsField.value = JSON.stringify(basicOptions);
      basicOptionsField.classList.add("dynamic-hidden");
      form.appendChild(basicOptionsField);
      const combinedLogoOptions = {};
      if (savedInvoiceSettings) {
        const invoiceSettings = JSON.parse(savedInvoiceSettings);
        const { showLogo, logoPosition, logoSize, customLogoSize } = invoiceSettings;
        Object.assign(combinedLogoOptions, { showLogo, logoPosition, logoSize, customLogoSize });
      }
      if (savedTopHeaderSettings) {
        const topHeaderSettings = JSON.parse(savedTopHeaderSettings);
        const {
          showInvoiceNo, invoiceNoValue, invoiceNoHeading,
          showDate, dateValue, dateHeading,
          showDueDate, dueDateValue, dueDateHeading,
          showPoNum, poNumValue, poNumHeading,
          showPoDate, poDateValue, poDateHeading,
          // om new
          showName, nameValue, nameHeading,
          showEmail, emailValue, emailHeading,
          showPhone, phoneValue, phoneHeading,
        } = topHeaderSettings || {};
        Object.assign(combinedLogoOptions, {
          showInvoiceNo, invoiceNoValue, invoiceNoHeading,
          showDate, dateValue, dateHeading,
          showDueDate, dueDateValue, dueDateHeading,
          showPoNum, poNumValue, poNumHeading,
          showPoDate, poDateValue, poDateHeading,
          // om new
          showName, nameValue, nameHeading,
          showEmail, emailValue, emailHeading,
          showPhone, phoneValue, phoneHeading,
        });
      }
      const logoOptionsField = document.createElement("input");
      logoOptionsField.type = "hidden";
      logoOptionsField.name = "logo_options";
      logoOptionsField.value = JSON.stringify(combinedLogoOptions);
      logoOptionsField.classList.add("dynamic-hidden");
      form.appendChild(logoOptionsField);
    }
    if (savedAddressSettings && savedInvoiceSettings) {
      const addressSettings = JSON.parse(savedAddressSettings);
      const invoiceSettings = JSON.parse(savedInvoiceSettings);
      const headerOptions = {
        showAddress: invoiceSettings.showAddress,
        showFirstAddress: invoiceSettings.showFirstAddress,
        showSecondAddress: invoiceSettings.showSecondAddress,
        showThirdAddress: invoiceSettings.showThirdAddress,
        firstAddressHeading: addressSettings.firstAddressHeading,
        firstAddressPosition: addressSettings.firstAddressPosition,
        firstAddressValue: addressSettings.firstAddressValue,
        secondAddressHeading: addressSettings.secondAddressHeading,
        secondAddressPosition: addressSettings.secondAddressPosition,
        secondAddressValue: addressSettings.secondAddressValue,
        thirdAddressHeading: addressSettings.thirdAddressHeading,
        thirdAddressPosition: addressSettings.thirdAddressPosition,
        thirdAddressValue: addressSettings.thirdAddressValue,
        // om address part
        showFirstAddressField: addressSettings.showFirstAddressField,
        showFirstPincodeField: addressSettings.showFirstPincodeField,
        showFirstGstField: addressSettings.showFirstGstField,
        showFirstPanField: addressSettings.showFirstPanField,
        // om address part
        showSecondAddressField: addressSettings.showSecondAddressField,
        showSecondPincodeField: addressSettings.showSecondPincodeField,
        showSecondGstField: addressSettings.showSecondGstField,
        showSecondPanField: addressSettings.showSecondPanField,
        // om address part
        showThirdAddressField: addressSettings.showThirdAddressField,
        showThirdPincodeField: addressSettings.showThirdPincodeField,
        showThirdGstField: addressSettings.showThirdGstField,
        showThirdPanField: addressSettings.showThirdPanField,

      };
      const headerOptionsField = document.createElement("input");
      headerOptionsField.type = "hidden";
      headerOptionsField.name = "header_options";
      headerOptionsField.value = JSON.stringify(headerOptions);
      headerOptionsField.classList.add("dynamic-hidden");
      form.appendChild(headerOptionsField);
    }
    const tableOptions = {};
    if (savedTableAppearance) {
      const tableAppearance = JSON.parse(savedTableAppearance);
      const { bodyBgColor, bodyTextColor, fontSize, footerBgColor, footerTextColor, headerBgColor, headerTextColor, headingHeight, rowHeight } = tableAppearance;
      Object.assign(tableOptions, { bodyBgColor, bodyTextColor, fontSize, footerBgColor, footerTextColor, headerBgColor, headerTextColor, headingHeight, rowHeight });
    }
    if (savedtableItemsVisibility) {
      const itemstable = savedtableItemsVisibility;
      Object.assign(tableOptions, { itemstable });
    }
    if (savedTableHeaders) {
      const TableHeaders = JSON.parse(savedTableHeaders);
      const {
        showamountcol, showtaxcol, showdescol, showhsnsaccol, showimagecol, showitemcol, showqtycol, showdiscountcol, showratecol, showuomcol,
      } = TableHeaders;
      Object.assign(tableOptions, {
        heading_amount: showamountcol,
        heading_tax: showtaxcol,
        heading_des: showdescol,
        heading_hsnsac: showhsnsaccol,
        heading_img: showimagecol,
        heading_item: showitemcol,
        heading_qty: showqtycol,
        heading_discount: showdiscountcol,
        heading_rate: showratecol,
        heading_uom: showuomcol,
      });
    }
    if (savedTableSelectHeaders) {
      const TableSelectHeaders = JSON.parse(savedTableSelectHeaders);
      const {
        showamountcol, showtaxcol, showdescol, showhsnsaccol, showimagecol, showitemcol, showqtycol, showdiscountcol, showratecol, showuomcol,
      } = TableSelectHeaders;
      Object.assign(tableOptions, {
        value_amount: showamountcol,
        value_tax: showtaxcol,
        value_des: showdescol,
        value_hsnsac: showhsnsaccol,
        value_img: showimagecol,
        value_item: showitemcol,
        value_qty: showqtycol,
        value_discount: showdiscountcol,
        value_rate: showratecol,
        value_uom: showuomcol,
      });
    }
    if (savedTableSettings) {
      const TableSettings = JSON.parse(savedTableSettings);
      const {
        showTable, amount, des, hsnsac, tax, img, item, qty, discount, rate, taxsum, uom,
      } = TableSettings;
      Object.assign(tableOptions, {
        showTable, amount, des, hsnsac, tax, img, item, qty, discount, rate, taxsum, uom
      });
      if (TableSettings.showColumns) {
        const {
          amount, tax, des, hsnsac, img, item, qty, discount, rate, taxsum, uom
        } = TableSettings.showColumns;
        tableOptions.showColumns = {
          amount, tax, des, hsnsac, img, item, qty, discount, rate, taxsum, uom
        };
      }
    }
    const tableOptionsField = document.createElement("input");
    tableOptionsField.type = "hidden";
    tableOptionsField.name = "table_options";
    tableOptionsField.value = JSON.stringify(tableOptions);
    tableOptionsField.classList.add("dynamic-hidden");
    form.appendChild(tableOptionsField);
    if (savedFooterSettings) {
      const footerSettings = JSON.parse(savedFooterSettings);
      if (footerSettings.showFields) {
        const footerOptionsKeys = [
          "showFooter",
          "showTotalAlpha",
          "showAdditionalDetails",
          "showBankDetails",
          "showSignature",
          "showQr",
          "totalbeforetax",
          "discount",
          "totaltaxamount",
          "roundedoff",
          "securityamount",
          "totalamount",
          "totaldue",
        ];
        const footerOptions = {};
        footerOptionsKeys.forEach((key) => {
          if (footerSettings[key] !== undefined) {
            footerOptions[key] = footerSettings[key];
          } else if (footerSettings.showFields[key] !== undefined) {
            footerOptions[key] = footerSettings.showFields[key];
          }
        });
        if (footerSettings.positions) {
          footerOptions.positions = footerSettings.positions;
        }
        if (footerSettings.showFields) {
          footerOptions.showFields = footerSettings.showFields;
        }
        const footerOptionsField = document.createElement("input");
        footerOptionsField.type = "hidden";
        footerOptionsField.name = "footer_options";
        footerOptionsField.value = JSON.stringify(footerOptions);
        footerOptionsField.classList.add("dynamic-hidden");
        form.appendChild(footerOptionsField);
      }
    }

    if (savedinvoiceData) {
      const invoiceData = savedinvoiceData;
      const invoiceOptionsField = document.createElement("input");
      invoiceOptionsField.type = "hidden";
      invoiceOptionsField.name = "invoice_options";
      invoiceOptionsField.value = invoiceData;
      invoiceOptionsField.classList.add("dynamic-hidden");
      form.appendChild(invoiceOptionsField);
    }

    if (savedinvoicePreview) {
      const invpreview = savedinvoicePreview;
      const tableOptionsField = document.createElement("input");
      tableOptionsField.type = "hidden";
      tableOptionsField.name = "invpreview";
      tableOptionsField.value = invpreview;
      tableOptionsField.classList.add("dynamic-hidden");
      form.appendChild(tableOptionsField);
    }

    const ridValue = document.getElementById('rid').value;
    const ridField = document.createElement("input");
    ridField.type = "hidden";
    ridField.name = "rid";
    ridField.value = ridValue;
    ridField.classList.add("dynamic-hidden");

    form.appendChild(ridField);
  };

  document.querySelector("form").addEventListener("submit", (e) => {
    createHiddenFields();
  });
</script>
