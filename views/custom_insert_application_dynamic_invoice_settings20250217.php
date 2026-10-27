<?php

$_SESSION['apply_js']['multiselest'] = 1;


if (isset($_POST['submitinvoice'])) {
  $_POST['company_id'] = $_SESSION['company_id'];
  $_POST['user_id'] = $_SESSION['userid'];

  $url = API . "?mode=insertdynamicinvoice&method=post";
  $response1 = file_post_contents($url, $_POST, 'insert');
  $response33 = json_decode($response1, 1);
	$response = $response33['res'];
  if ($response['status'] == 'sucess') {
    if ($response['LASTID'] == 0) {
      $msgcode = 3001; //update
    } else {
      $msgcode = 3000; //insert
      $_SESSION['insert_msg'] = $msgcode;
      header("location:index?option=COM_LIST_RENTAL_INVOICES");
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
// $rid = 14;
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

$File_Uploads_Path = "uploads/customer/";

if ($userData['user_photo'] != '') {
  $image = $File_Uploads_Path . $userData['user_photo'];
} else {
  $image = $File_Uploads_Path . "no-image.jpg";
}

?>
<?php if ($error_message != '') { ?>
  <div class="alert alert-danger background-danger alert-dismissible">
    <strong><?php echo $error_message; ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php } ?>
<div class="card">
  <div class="card-header">
    <h5 class>Invoice</h5>
    <div class="card-header-right">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
        <i class="icofont icofont-reply"></i> Back
      </a>
    </div>
  </div>
  <div class="card-body m-r-50 m-l-50">
    <div class="container mt-4">
      <div class="row ml-20 mr-20">
        <div class="col-lg-3 p-3 bg-light border">
          <h5 class="text-center mb-4">Customization Options</h5>
          <div class="mb-3">
            <!-- Template Name Input -->
            <div>
              <div class="card card-body mt-2">
                <label for="template_name" class="form-label">Template Name</label>
                <div class="form-group">
                  <input class="form-control mb-1" type="text" id="template_name" value="<?= $invoice_options ?>"
                    placeholder="Enter template name">
                  <input class="form-control mb-1" type="hidden" id="rid" value="<?= $rid ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Border Options </h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-primary btn-sm mb-2" data-target="borderOptions">
                <i class="bi bi-border"></i> Border
              </a>
              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="colorOptions">
                <i class="bi bi-palette"></i> Color
              </a>
              <a class="btn btn-outline-info btn-sm mb-2" data-target="widthOptions">
                <i class="bi bi-border-width"></i> Width
              </a>
            </div>
            <div id="borderOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderStyle" class="form-label">Border Style</label>
                <select class="form-select" id="borderStyle" name="borderStyle">
                  <option value="solid" <?php echo $borderstyle == 'solid' ? 'selected' : ''; ?>>Solid</option>
                  <option value="dashed" <?php echo $borderstyle == 'dashed' ? 'selected' : ''; ?>>Dashed</option>
                  <option value="dotted" <?php echo $borderstyle == 'dotted' ? 'selected' : ''; ?>>Dotted</option>
                  <option value="none" <?php echo $borderstyle == 'none' ? 'selected' : ''; ?>>None</option>
                </select>
              </div>
            </div>
            <div id="colorOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderColor" class="form-label">Border Color</label>
                <input type="color" class="form-control form-control-color" style="max-width:100%;" id="borderColor"
                  title="Choose border color" value="<?php echo $bordercolor ?>" name="borderColor">
              </div>
            </div>
            <div id="widthOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderWidth" class="form-label">Border Width</label>
                <input type="number" class="form-control" id="borderWidth" min="1" max="10"
                  placeholder="Enter width (in px)" value="<?php echo $borderwidth ?>" name="borderWidth">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Basic Options</h6>
            <div class="btn-group d-flex flex-wrap">
              <!-- Text Options -->
              <a class="btn btn-outline-danger btn-sm mb-2" data-target="textStyleOptions">
                <i class="bi bi-fonts"></i> Text Style
              </a>
              <!-- Margin Options -->
              <a class="btn btn-outline-primary btn-sm mb-2" data-target="marginOptions">
                <i class="bi bi-arrows-expand"></i> Margin
              </a>
              <!-- Padding Options -->
              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="paddingOptions">
                <i class="bi bi-box"></i> Padding
              </a>
              <!-- Height Options -->
              <a class="btn btn-outline-info btn-sm mb-2" data-target="heightOptions">
                <i class="bi bi-arrow-up-down"></i> Height
              </a>
            </div>

            <!-- Text Style Options -->
            <div id="textStyleOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="fontFamily" class="form-label">Font Family</label>
                <select class="form-select" id="fontFamily" name="fontFamily">
                  <option value="Arial" <?php echo $fontFamily == 'Arial' ? 'selected' : ''; ?>>Arial</option>
                  <option value="Times New Roman" <?php echo $fontFamily == 'Times New Roman' ? 'selected' : ''; ?>>Times
                    New Roman</option>
                  <option value="Courier New" <?php echo $fontFamily == 'Courier New' ? 'selected' : ''; ?>>Courier New
                  </option>
                  <option value="Verdana" <?php echo $fontFamily == 'Verdana' ? 'selected' : ''; ?>>Verdana</option>
                  <option value="Georgia" <?php echo $fontFamily == 'Georgia' ? 'selected' : ''; ?>>Georgia</option>
                  <option value="Trebuchet MS" <?php echo $fontFamily == 'Trebuchet MS' ? 'selected' : ''; ?>>Trebuchet MS
                  </option>
                  <option value="Tahoma" <?php echo $fontFamily == 'Tahoma' ? 'selected' : ''; ?>>Tahoma</option>
                  <option value="Roboto" <?php echo $fontFamily == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                </select>
              </div>
            </div>

            <!-- Margin Options -->
            <div id="marginOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="marginInput" class="form-label">Margin (in px)</label>
                <input type="number" class="form-control" id="marginInput" name="marginInput"
                  value="<?php echo $margin; ?>" min="0" placeholder="Enter margin">
              </div>
            </div>

            <!-- Padding Options -->
            <div id="paddingOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="paddingInput" class="form-label">Padding (in px)</label>
                <input type="number" class="form-control" id="paddingInput" name="paddingInput"
                  value="<?php echo $padding; ?>" min="0" placeholder="Enter padding">
              </div>
            </div>

            <!-- Height Options -->
            <div id="heightOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="heightInput" class="form-label">Height (in px)</label>
                <input type="number" class="form-control" id="heightInput" name="heightInput"
                  value="<?php echo $height; ?>" min="100" placeholder="Enter height">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Logo Options</h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-danger btn-sm mb-2" data-target="logoVisibility">
                <i class="bi bi-eye"></i> Visibility
              </a>
              <a class="btn btn-outline-warning btn-sm mb-2" data-target="logoPosition">
                <i class="bi bi-layout-sidebar"></i> Position
              </a>
              <a class="btn btn-outline-primary btn-sm mb-2" data-target="logoSize">
                <i class="bi bi-arrows-fullscreen"></i> Size
              </a>
              <a class="btn btn-outline-success btn-sm mb-2" data-target="topheaderoptions">
                <i class="bi bi-pencil"></i> Top Header Options
              </a>
            </div>

            <?php
            $logoOptions = json_decode($invdata['logo_options'], true);
            ?>

            <!-- Toggle Logo -->
            <div id="logoVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Logo</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showLogo" name="showLogo" <?php echo $logoOptions['showLogo'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showLogo">Show Logo</label>
                </div>
              </div>
            </div>

            <!-- Logo Position -->
            <div id="logoPosition" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="logoPositionSelect" class="form-label">Logo Position</label>
                <select class="form-select" id="logoPositionSelect" name="logoPosition">
                  <option value="left" <?php echo (empty($logoOptions['logoPosition']) || $logoOptions['logoPosition'] === 'left') ? 'selected' : ''; ?>>Left
                  </option>
                  <option value="center" <?php echo $logoOptions['logoPosition'] === 'center' ? 'selected' : ''; ?>>Center
                  </option>
                  <option value="right" <?php echo $logoOptions['logoPosition'] === 'right' ? 'selected' : ''; ?>>Right
                  </option>
                </select>
              </div>
            </div>

            <!-- Logo Size -->
            <div id="logoSize" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="logoSizeSelect" class="form-label">Logo Size</label>
                <select class="form-select" id="logoSizeSelect" name="logoSize">
                  <option value="small" <?php echo $logoOptions['logoSize'] === 'small' ? 'selected' : ''; ?>>Small
                  </option>
                  <option value="medium" <?php echo (empty($logoOptions['logoSize']) || $logoOptions['logoSize'] === 'medium') ? 'selected' : ''; ?>>Medium
                  </option>
                  <option value="large" <?php echo $logoOptions['logoSize'] === 'large' ? 'selected' : ''; ?>>Large
                  </option>
                  <option value="custom" <?php echo !empty($logoOptions['customLogoSize']) ? 'selected' : ''; ?>>Custom
                  </option>
                </select>
                <div id="customLogoSizeInputContainer" class="mt-2"
                  style="<?php echo !empty($logoOptions['customLogoSize']) ? '' : 'display: none;'; ?>">
                  <label for="customLogoSizeInput" class="form-label">Custom Logo Size (px)</label>
                  <input type="number" id="customLogoSizeInput" class="form-control" name="customLogoSize"
                    value="<?php echo htmlspecialchars($logoOptions['customLogoSize']); ?>"
                    placeholder="Enter size in px" />
                </div>
              </div>
            </div>


            <div id="topheaderoptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Top Header Options</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showinvoiceno" name="showInvoiceNo" <?php echo $logoOptions['showInvoiceNo'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showinvoiceno">Show Invoice No.</label>
                  <select class="form-select" id="invoicenoSelect" style="display: none;">
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'id' ? 'selected' : ''; ?> value="id">ID
                    </option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo (empty($logoOptions['invoiceNoValue']) || $logoOptions['invoiceNoValue'] === 'transaction_number') ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_location_id' ? 'selected' : ''; ?>
                      value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'transaction_warehouse_id' ? 'selected' : ''; ?>
                      value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'originator_currency_id' ? 'selected' : ''; ?>
                      value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'notes' ? 'selected' : ''; ?> value="notes">
                      Notes</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'amount' ? 'selected' : ''; ?> value="amount">
                      Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'type' ? 'selected' : ''; ?> value="type">Type
                    </option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_id' ? 'selected' : ''; ?> value="ref_id">
                      Ref Id</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_no' ? 'selected' : ''; ?> value="ref_no">
                      Ref No</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'party_additional_details' ? 'selected' : ''; ?>
                      value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      value="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'cancel_invoice_reason' ? 'selected' : ''; ?>
                      value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'owner_additional_details' ? 'selected' : ''; ?>
                      value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $logoOptions['invoiceNoValue'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showdate" <?php echo $logoOptions['showDate'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showdate">Show Date</label>
                  <select class="form-select" id="invoicedateSelect" style="display: none;">
                    <option <?php echo $logoOptions['dateValue'] === 'id' ? 'selected' : ''; ?> value="id">ID</option>
                    <option <?php echo $logoOptions['dateValue'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo (empty($logoOptions['dateValue']) || $logoOptions['dateValue'] === 'transaction_date') ? 'selected' : ''; ?> value="transaction_date">
                      Transaction Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'transaction_location_id' ? 'selected' : ''; ?>
                      value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'transaction_warehouse_id' ? 'selected' : ''; ?>
                      value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'originator_currency_id' ? 'selected' : ''; ?>
                      value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $logoOptions['dateValue'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_id' ? 'selected' : ''; ?> value="party_id">
                      Party Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $logoOptions['dateValue'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $logoOptions['dateValue'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $logoOptions['dateValue'] === 'notes' ? 'selected' : ''; ?> value="notes">Notes
                    </option>
                    <option <?php echo $logoOptions['dateValue'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $logoOptions['dateValue'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'amount' ? 'selected' : ''; ?> value="amount">Amount
                    </option>
                    <option <?php echo $logoOptions['dateValue'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'type' ? 'selected' : ''; ?> value="type">Type
                    </option>
                    <option <?php echo $logoOptions['dateValue'] === 'tax_type' ? 'selected' : ''; ?> value="tax_type">Tax
                      Type</option>
                    <option <?php echo $logoOptions['dateValue'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $logoOptions['dateValue'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $logoOptions['dateValue'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $logoOptions['dateValue'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'tax_state' ? 'selected' : ''; ?> value="tax_state">
                      Tax State</option>
                    <option <?php echo $logoOptions['dateValue'] === 'opex_cpex' ? 'selected' : ''; ?> value="opex_cpex">
                      Opex Cpex</option>
                    <option <?php echo $logoOptions['dateValue'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $logoOptions['dateValue'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'due_date' ? 'selected' : ''; ?> value="due_date">Due
                      Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'ref_type' ? 'selected' : ''; ?> value="ref_type">Ref
                      Type</option>
                    <option <?php echo $logoOptions['dateValue'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $logoOptions['dateValue'] === 'ref_id' ? 'selected' : ''; ?> value="ref_id">Ref Id
                    </option>
                    <option <?php echo $logoOptions['dateValue'] === 'ref_no' ? 'selected' : ''; ?> value="ref_no">Ref No
                    </option>
                    <option <?php echo $logoOptions['dateValue'] === 'ref_date' ? 'selected' : ''; ?> value="ref_date">Ref
                      Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $logoOptions['dateValue'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $logoOptions['dateValue'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'party_additional_details' ? 'selected' : ''; ?>
                      value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $logoOptions['dateValue'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $logoOptions['dateValue'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      value="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $logoOptions['dateValue'] === 'cancel_invoice_reason' ? 'selected' : ''; ?>
                      value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $logoOptions['dateValue'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $logoOptions['dateValue'] === 'pdf_name' ? 'selected' : ''; ?> value="pdf_name">Pdf
                      Name</option>
                    <option <?php echo $logoOptions['dateValue'] === 'owner_additional_details' ? 'selected' : ''; ?>
                      value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $logoOptions['dateValue'] === 'serial_transaction_number' ? 'selected' : ''; ?>
                      value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showduedate" <?php echo $logoOptions['showDueDate'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showduedate">Show Due Date</label>
                  <select class="form-select" id="invoiceduedateSelect" style="display: none;">
                    <option <?php echo $logoOptions['dueDateValue'] === 'id' ? 'selected' : ''; ?> value="id">ID</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'transaction_location_id' ? 'selected' : ''; ?>
                      value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'transaction_warehouse_id' ? 'selected' : ''; ?>
                      value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'originator_currency_id' ? 'selected' : ''; ?>
                      value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_id' ? 'selected' : ''; ?> value="party_id">
                      Party Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'notes' ? 'selected' : ''; ?> value="notes">Notes
                    </option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'amount' ? 'selected' : ''; ?> value="amount">
                      Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'type' ? 'selected' : ''; ?> value="type">Type
                    </option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'tax_type' ? 'selected' : ''; ?> value="tax_type">
                      Tax Type</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo (empty($logoOptions['dueDateValue']) || $logoOptions['dueDateValue'] === 'due_date') ? 'selected' : ''; ?> value="due_date">Due Date
                    </option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'ref_type' ? 'selected' : ''; ?> value="ref_type">
                      Ref Type</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'ref_id' ? 'selected' : ''; ?> value="ref_id">Ref
                      Id</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'ref_no' ? 'selected' : ''; ?> value="ref_no">Ref
                      No</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'ref_date' ? 'selected' : ''; ?> value="ref_date">
                      Ref Date</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'party_additional_details' ? 'selected' : ''; ?>
                      value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      value="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'cancel_invoice_reason' ? 'selected' : ''; ?>
                      value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'pdf_name' ? 'selected' : ''; ?> value="pdf_name">
                      Pdf Name</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'owner_additional_details' ? 'selected' : ''; ?>
                      value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $logoOptions['dueDateValue'] === 'serial_transaction_number' ? 'selected' : ''; ?>
                      value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpo_num" <?php echo $logoOptions['showPoNum'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showpo_num">Show P.O. Number</label>
                  <select class="form-select" id="invoiceponumSelect" style="display: none;">
                    <option <?php echo $logoOptions['poNumValue'] === 'id' ? 'selected' : ''; ?> value="id">ID</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'transaction_location_id' ? 'selected' : ''; ?>
                      value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'transaction_warehouse_id' ? 'selected' : ''; ?>
                      value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'originator_currency_id' ? 'selected' : ''; ?>
                      value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_id' ? 'selected' : ''; ?> value="party_id">
                      Party Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'notes' ? 'selected' : ''; ?> value="notes">Notes
                    </option>
                    <option <?php echo $logoOptions['poNumValue'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'amount' ? 'selected' : ''; ?> value="amount">Amount
                    </option>
                    <option <?php echo $logoOptions['poNumValue'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'type' ? 'selected' : ''; ?> value="type">Type
                    </option>
                    <option <?php echo $logoOptions['poNumValue'] === 'tax_type' ? 'selected' : ''; ?> value="tax_type">
                      Tax Type</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'tax_state' ? 'selected' : ''; ?> value="tax_state">
                      Tax State</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'opex_cpex' ? 'selected' : ''; ?> value="opex_cpex">
                      Opex Cpex</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'due_date' ? 'selected' : ''; ?> value="due_date">
                      Due Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'ref_type' ? 'selected' : ''; ?> value="ref_type">
                      Ref Type</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'ref_id' ? 'selected' : ''; ?> value="ref_id">Ref Id
                    </option>
                    <option <?php echo (empty($logoOptions['poNumValue']) || $logoOptions['poNumValue'] === 'ref_no') ? 'selected' : ''; ?> value="ref_no">Ref No</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'ref_date' ? 'selected' : ''; ?> value="ref_date">
                      Ref Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'party_additional_details' ? 'selected' : ''; ?>
                      value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      value="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'cancel_invoice_reason' ? 'selected' : ''; ?>
                      value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'pdf_name' ? 'selected' : ''; ?> value="pdf_name">
                      Pdf Name</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'owner_additional_details' ? 'selected' : ''; ?>
                      value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $logoOptions['poNumValue'] === 'serial_transaction_number' ? 'selected' : ''; ?>
                      value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpo_date" <?php echo $logoOptions['showPoDate'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showpo_date">Show P.O Date</label>
                  <select class="form-select" id="invoicepodateSelect" style="display: none;">
                    <option <?php echo $logoOptions['poDateValue'] === 'id' ? 'selected' : ''; ?> value="id">ID</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'transaction_location_id' ? 'selected' : ''; ?>
                      value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'transaction_warehouse_id' ? 'selected' : ''; ?>
                      value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'originator_currency_id' ? 'selected' : ''; ?>
                      value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_id' ? 'selected' : ''; ?> value="party_id">
                      Party Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'notes' ? 'selected' : ''; ?> value="notes">Notes
                    </option>
                    <option <?php echo $logoOptions['poDateValue'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'amount' ? 'selected' : ''; ?> value="amount">
                      Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'type' ? 'selected' : ''; ?> value="type">Type
                    </option>
                    <option <?php echo $logoOptions['poDateValue'] === 'tax_type' ? 'selected' : ''; ?> value="tax_type">
                      Tax Type</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'due_date' ? 'selected' : ''; ?> value="due_date">
                      Due Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'ref_type' ? 'selected' : ''; ?> value="ref_type">
                      Ref Type</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'ref_id' ? 'selected' : ''; ?> value="ref_id">Ref
                      Id</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'ref_no' ? 'selected' : ''; ?> value="ref_no">Ref
                      No</option>
                    <option <?php echo (empty($logoOptions['poDateValue']) || $logoOptions['poDateValue'] === 'ref_date') ? 'selected' : ''; ?> value="ref_date">Ref Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'party_additional_details' ? 'selected' : ''; ?>
                      value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      value="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'cancel_invoice_reason' ? 'selected' : ''; ?>
                      value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'pdf_name' ? 'selected' : ''; ?> value="pdf_name">
                      Pdf Name</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'owner_additional_details' ? 'selected' : ''; ?>
                      value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $logoOptions['poDateValue'] === 'serial_transaction_number' ? 'selected' : ''; ?>
                      value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Header Options</h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-danger btn-sm mb-2" data-target="addressVisibility">
                <i class="bi bi-eye"></i> Visibility
              </a>
              <a class="btn btn-outline-dark btn-sm mb-2" data-target="addresssecVisibility">
                <i class="bi bi-aspect-ratio"></i> Address Visibility
              </a>
              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="addressPosition">
                <i class="bi bi-aspect-ratio"></i> Address Position
              </a>
              <a class="btn btn-outline-success btn-sm mb-2" data-target="addressSize">
                <i class="bi bi-pencil"></i> Text Size
              </a>
            </div>
            <?php
            $headerOptions = json_decode($invdata['header_options'], true);
            ?>
            <!-- Visibility Options -->
            <div id="addressVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Header</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showAddress" <?php echo $headerOptions['showAddress'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showAddress">Show Header</label>
                </div>
              </div>
            </div>

            <!-- Address Visibility Options -->
            <div id="addresssecVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="addressvisibilitySelect" class="form-label">Header Address Visibility</label>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showFirstAddress" <?php echo $headerOptions['showFirstAddress'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showFirstAddress">Show Bill From Address</label>
                  <input class="form-control mb-1" type="text" id="inputheading1"
                    value="<?php echo !empty($headerOptions['firstAddressHeading']) ? $headerOptions['firstAddressHeading'] : 'Invoiced To'; ?>">
                  <select class="form-select" id="address1Select" style="display: none;">
                    <option <?php echo (empty($headerOptions['firstAddressValue']) || $headerOptions['firstAddressValue'] === 'party_address') ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $headerOptions['firstAddressValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Shipping Address</option>
                    <option <?php echo $headerOptions['firstAddressValue'] === 'company_address' ? 'selected' : ''; ?>
                      value="company_address">Company Address</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showSecondAddress" <?php echo $headerOptions['showSecondAddress'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showSecondAddress">Show Bill TO Address</label>
                  <input class="form-control mb-1" type="text" id="inputheading2"
                    value="<?php echo !empty($headerOptions['secondAddressHeading']) ? $headerOptions['secondAddressHeading'] : 'Bill To'; ?>">
                  <select class="form-select" id="address2Select" style="display: none;">
                    <option <?php echo $headerOptions['secondAddressValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo (empty($headerOptions['secondAddressValue']) || $headerOptions['secondAddressValue'] === 'party_ship_adderss') ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Shipping Address</option>
                    <option <?php echo $headerOptions['secondAddressValue'] === 'company_address' ? 'selected' : ''; ?>
                      value="company_address">Company Address</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showThirdAddress" <?php echo $headerOptions['showThirdAddress'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showThirdAddress">Show Company Address</label>
                  <input class="form-control mb-1" type="text" id="inputheading3"
                    value="<?php echo !empty($headerOptions['thirdAddressHeading']) ? $headerOptions['thirdAddressHeading'] : 'Company'; ?>">
                  <select class="form-select" id="address3Select" style="display: none;">
                    <option <?php echo $headerOptions['thirdAddressValue'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $headerOptions['thirdAddressValue'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Shipping Address</option>
                    <option <?php echo (empty($headerOptions['thirdAddressValue']) || $headerOptions['thirdAddressValue'] === 'company_address') ? 'selected' : ''; ?>
                      value="company_address">Company Address</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Address Position Options -->
            <div id="addressPosition" class="dynamic-options">
              <div class="card card-body mt-2">
                <div class="form-group">
                  <label for="address1position" class="form-label">Address 1</label>
                  <select class="form-select" id="address1position">
                    <option <?php echo (empty($headerOptions['firstAddressPosition']) || $headerOptions['firstAddressPosition'] === 'left') ? 'selected' : ''; ?> value="left">Left</option>
                    <option <?php echo $headerOptions['firstAddressPosition'] === 'center' ? 'selected' : ''; ?>
                      value="center">Center</option>
                    <option <?php echo $headerOptions['firstAddressPosition'] === 'right' ? 'selected' : ''; ?>
                      value="right">Right</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="address2position" class="form-label">Address 2</label>
                  <select class="form-select" id="address2position">
                    <option <?php echo (empty($headerOptions['secondAddressPosition']) || $headerOptions['secondAddressPosition'] === 'left') ? 'selected' : ''; ?> value="left">Left</option>
                    <option <?php echo $headerOptions['secondAddressPosition'] === 'center' ? 'selected' : ''; ?>
                      value="center">Center</option>
                    <option <?php echo $headerOptions['secondAddressPosition'] === 'right' ? 'selected' : ''; ?>
                      value="right">Right</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="address3position" class="form-label">Address 3</label>
                  <select class="form-select" id="address3position">
                    <option <?php echo (empty($headerOptions['thirdAddressPosition']) || $headerOptions['thirdAddressPosition'] === 'left') ? 'selected' : ''; ?> value="left">Left</option>
                    <option <?php echo $headerOptions['thirdAddressPosition'] === 'center' ? 'selected' : ''; ?>
                      value="center">Center</option>
                    <option <?php echo $headerOptions['thirdAddressPosition'] === 'right' ? 'selected' : ''; ?>
                      value="right">Right</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Size Options -->
            <div id="addressSize" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="addressSizeSelect" class="form-label">Text Size</label>
                <select class="form-select" id="addressSizeSelect">
                  <option value="small" <?php echo $headerOptions['addressTextSize'] === 'small' ? 'selected' : ''; ?>>
                    Small</option>
                  <option value="medium" <?php echo (empty($headerOptions['addressTextSize']) || $headerOptions['addressTextSize'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                  <option value="large" <?php echo $headerOptions['addressTextSize'] === 'large' ? 'selected' : ''; ?>>
                    Large</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Table Options</h6>
            <div class="btn-group d-flex flex-wrap">
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="tableVisibility">
                <i class="bi bi-eye"></i> Visibility
              </button>
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="tableAppearance">
                <i class="bi bi-palette"></i> Appearance
              </button>
              <button class="btn btn-outline-danger btn-sm mb-2" data-target="colsVisibility">
                <i class="bi bi-list"></i> Table Options
              </button>
              <button class="btn btn-outline-danger btn-sm mb-2" data-target="itemsshow">
                <i class="bi bi-list"></i> Items Show
              </button>
            </div>
            <?php
            $tableOptions = json_decode($invdata['table_options'], true);
            ?>
            <!-- Visibility Options -->
            <div id="tableVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Table</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showTable" readonly disabled checked>
                  <label class="form-check-label" for="showTable">Show Table</label>
                </div>
              </div>
            </div>

            <!-- Table Appearance Options -->
            <div id="tableAppearance" class="dynamic-options">
              <div class="card card-body mt-2">
                <h6>Customize Table Appearance</h6>

                <!-- Table Header Background -->
                <div class="mb-1">
                  <label class="form-label">Table Header Background</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableHeaderbgColor" value="<?php echo $tableOptions['headerBgColor'] ?? '#f0f0f0'; ?>">
                </div>

                <!-- Table Header color -->
                <div class="mb-1">
                  <label class="form-label">Table Header Text Color</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableHeaderColor" value="<?php echo $tableOptions['headerTextColor'] ?? '#000'; ?>">
                </div>

                <!-- Table Body Background -->
                <div class="mb-1">
                  <label class="form-label">Table Body Background</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableBodybgColor" value="<?php echo $tableOptions['bodyBgColor'] ?? '#ffffff'; ?>">
                </div>

                <!-- Table body color -->
                <div class="mb-1">
                  <label class="form-label">Table body Text Color</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableBodyColor" value="<?php echo $bodyTextColor['bodyBgColor'] ?? '#000'; ?>">
                </div>

                <!-- Table Footer Background -->
                <div class="mb-1">
                  <label class="form-label">Table Footer Background</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableFooterbgColor" value="<?php echo $bodyTextColor['footerBgColor'] ?? '#f8f9fa'; ?>">
                </div>

                <!-- Table Header color -->
                <div class="mb-1">
                  <label class="form-label">Table Footer Text Color</label>
                  <input type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableFooterColor" value="<?php echo $bodyTextColor['footerTextColor'] ?? '#000000'; ?>">
                </div>

                <!-- Row Height -->
                <div class="mb-1">
                  <label class="form-label">Table Row Height</label>
                  <input type="number" class="form-control" id="rowHeight" min="20" placeholder="Enter Row Height (px)"
                    value="<?php echo $bodyTextColor['rowHeight'] ?? '40'; ?>">
                </div>

                <!-- Heading Height -->
                <div class="mb-1">
                  <label class="form-label">Table Heading Height</label>
                  <input type="number" class="form-control" id="headingHeight" min="20"
                    placeholder="Enter Heading Height (px)"
                    value="<?php echo $bodyTextColor['headingHeight'] ?? '40'; ?>">
                </div>

                <!-- Font Size -->
                <div class="mb-1">
                  <label class="form-label">Table Font Size</label>
                  <input type="number" class="form-control" id="fontSize" min="8" max="36"
                    placeholder="Enter Font Size (px)" value="<?php echo $bodyTextColor['fontSize'] ?? '16'; ?>">
                </div>
              </div>
            </div>

            <!-- Cols Visibility Options -->
            <div id="colsVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Columns</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtaxsumcol" <?php echo $tableOptions['showColumns']['taxsum'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtaxsumcol">Show Tax Summary</label>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showimagecol" <?php echo $tableOptions['showColumns']['img'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showimagecol">Show Image Columns</label>
                  <input type="text" id="imagecolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_img']) ? $tableOptions['heading_img'] : 'Img'; ?>">
                  <select class="form-select mt-1" id="itemimageSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_img'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_img'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_img'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_id' ? 'selected' : ''; ?> value="item_id">Item
                      Id</option>
                    <option <?php echo $tableOptions['value_img'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_name' ? 'selected' : ''; ?> value="item_name">
                      Item Name</option>
                    <option <?php echo (empty($tableOptions['value_img']) || $tableOptions['value_img'] === 'item_image') ? 'selected' : ''; ?> value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_img'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_img'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_img'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_img'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_img'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_img'] === 'reason_id' ? 'selected' : ''; ?> value="reason_id">
                      Reason Id</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_img'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_img'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_img'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_img'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_img'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_img'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_img'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_img'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_img'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_img'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_img'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showitemcol" <?php echo $tableOptions['showColumns']['item'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showitemcol">Show Item Name Columns</label>
                  <input type="text" id="itemcolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_item']) ? $tableOptions['heading_item'] : 'Item'; ?>">
                  <select class="form-select mt-1" id="itemnameSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_item'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_item'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_item'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_id' ? 'selected' : ''; ?> value="item_id">
                      Item Id</option>
                    <option <?php echo $tableOptions['value_item'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo (empty($tableOptions['value_item']) || $tableOptions['value_item'] === 'item_name') ? 'selected' : ''; ?> value="item_name">Item Name</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_item'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_item'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_item'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_item'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_item'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_item'] === 'reason_id' ? 'selected' : ''; ?>
                      value="reason_id">Reason Id</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_item'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_item'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_item'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_item'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_item'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_item'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_item'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_item'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_item'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_item'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_item'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showdescol" <?php echo $tableOptions['showColumns']['des'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showitemcol">Show Item Description</label>
                  <input type="text" id="descolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_des']) ? $tableOptions['heading_des'] : 'Description'; ?>">
                  <select class="form-select mt-1" id="itemdescriptionSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_des'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_des'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_des'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_id' ? 'selected' : ''; ?> value="item_id">Item
                      Id</option>
                    <option <?php echo $tableOptions['value_des'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_name' ? 'selected' : ''; ?> value="item_name">
                      Item Name</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo (empty($tableOptions['value_des']) || $tableOptions['value_des'] === 'description') ? 'selected' : ''; ?> value="description">Description</option>
                    <option <?php echo $tableOptions['value_des'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_des'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_des'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_des'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_des'] === 'reason_id' ? 'selected' : ''; ?> value="reason_id">
                      Reason Id</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_des'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_des'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_des'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_des'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_des'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_des'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_des'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_des'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_des'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_des'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_des'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showhsnsaccol" <?php echo $tableOptions['showColumns']['hsnsac'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showhsnsaccol">Show HSN/SAC Columns</label>
                  <input type="text" id="hsnsaccolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_hsnsac']) ? $tableOptions['heading_hsnsac'] : 'Hsn/Sac'; ?>">
                  <select class="form-select mt-1" id="itemhsnsacSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_hsnsac'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo (empty($tableOptions['value_hsnsac']) || $tableOptions['value_hsnsac'] === 'hsn_and_sac_code_id') ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_id' ? 'selected' : ''; ?> value="item_id">
                      Item Id</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_name' ? 'selected' : ''; ?>
                      value="item_name">Item Name</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'reason_id' ? 'selected' : ''; ?>
                      value="reason_id">Reason Id</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'req_date' ? 'selected' : ''; ?>
                      value="req_date">Req Date</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_hsnsac'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showuomcol" <?php echo $tableOptions['showColumns']['uom'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showuomcol">Show UOM Columns</label>
                  <input type="text" id="uomcolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_uom']) ? $tableOptions['heading_uom'] : 'UOM'; ?>">
                  <select class="form-select mt-1" id="itemuomSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_uom'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'hsn_and_sac_code_id' ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_id' ? 'selected' : ''; ?> value="item_id">Item
                      Id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_name' ? 'selected' : ''; ?> value="item_name">
                      Item Name</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_uom'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo (empty($tableOptions['value_uom']) || $tableOptions['value_uom'] === 'purchase_uom') ? 'selected' : ''; ?> value="purchase_uom">UOM
                    </option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_uom'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_uom'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_uom'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_uom'] === 'reason_id' ? 'selected' : ''; ?> value="reason_id">
                      Reason Id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_uom'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_uom'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_uom'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_uom'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_uom'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_uom'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_uom'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_uom'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_uom'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_uom'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_uom'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtaxcol" <?php echo $tableOptions['showColumns']['tax'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtaxcol">Show TAX Columns</label>
                  <input type="text" id="taxcolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_tax']) ? $tableOptions['heading_tax'] : 'Tax'; ?>">
                  <select class="form-select mt-1" id="itemtaxSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_tax'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'hsn_and_sac_code_id' ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_id' ? 'selected' : ''; ?> value="item_id">Item
                      Id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_name' ? 'selected' : ''; ?> value="item_name">
                      Item Name</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_tax'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_tax'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo (empty($tableOptions['value_tax']) || $tableOptions['value_tax'] === 'tax_amount') ? 'selected' : ''; ?> value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_tax'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_tax'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_tax'] === 'reason_id' ? 'selected' : ''; ?> value="reason_id">
                      Reason Id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_tax'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_tax'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_tax'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_tax'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_tax'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_tax'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_tax'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_tax'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_tax'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_tax'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_tax'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showratecol" <?php echo $tableOptions['showColumns']['rate'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showratecol">Show Rate Columns</label>
                  <input type="text" id="ratecolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_rate']) ? $tableOptions['heading_rate'] : 'Rate'; ?>">
                  <select class="form-select mt-1" id="itemrateSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_rate'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'hsn_and_sac_code_id' ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_id' ? 'selected' : ''; ?> value="item_id">
                      Item Id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_name' ? 'selected' : ''; ?>
                      value="item_name">Item Name</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_rate'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_rate'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo (empty($tableOptions['value_rate']) || $tableOptions['value_rate'] === 'item_price') ? 'selected' : ''; ?> value="item_price">Item Price
                    </option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_rate'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_rate'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_rate'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_rate'] === 'reason_id' ? 'selected' : ''; ?>
                      value="reason_id">Reason Id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_rate'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_rate'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_rate'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_rate'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_rate'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_rate'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_rate'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_rate'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_rate'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_rate'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_rate'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showqtycol" <?php echo $tableOptions['showColumns']['qty'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showqtycol">Show QTY Columns</label>
                  <input type="text" id="qtycolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_qty']) ? $tableOptions['heading_qty'] : 'QTY'; ?>">
                  <select class="form-select mt-1" id="itemqtySelect" style="display: none;">
                    <option <?php echo $tableOptions['value_qty'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'hsn_and_sac_code_id' ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_id' ? 'selected' : ''; ?> value="item_id">Item
                      Id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_name' ? 'selected' : ''; ?> value="item_name">
                      Item Name</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_qty'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_qty'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo (empty($tableOptions['value_qty']) || $tableOptions['value_qty'] === 'item_quantity') ? 'selected' : ''; ?> value="item_quantity">Quantity
                    </option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_qty'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_qty'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $tableOptions['value_qty'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $tableOptions['value_qty'] === 'reason_id' ? 'selected' : ''; ?> value="reason_id">
                      Reason Id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_qty'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_qty'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_qty'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_qty'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_qty'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_qty'] === 'req_date' ? 'selected' : ''; ?> value="req_date">
                      Req Date</option>
                    <option <?php echo $tableOptions['value_qty'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_qty'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_qty'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_qty'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_qty'] === 'parent_id' ? 'selected' : ''; ?> value="parent_id">
                      Parent Id</option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showamountcol" <?php echo $tableOptions['showColumns']['amount'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showamountcol">Show Amount Columns</label>
                  <input type="text" id="amountcolumnInput" class="form-control" style="display: none;"
                    placeholder="Enter Feild Name"
                    value="<?php echo !empty($tableOptions['heading_amount']) ? $tableOptions['heading_amount'] : 'Amount'; ?>">
                  <select class="form-select mt-1" id="itemamountSelect" style="display: none;">
                    <option <?php echo $tableOptions['value_amount'] === 'id' ? 'selected' : ''; ?> value="id">id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'transaction_id' ? 'selected' : ''; ?>
                      value="transaction_id">Transaction Id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'hsn_and_sac_code_id' ? 'selected' : ''; ?>
                      value="hsn_and_sac_code_id" selected>HSN/SAC ID</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_table_name' ? 'selected' : ''; ?>
                      value="item_table_name">Item Table Name</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_id' ? 'selected' : ''; ?> value="item_id">
                      Item Id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'account_code' ? 'selected' : ''; ?>
                      value="account_code">Account Code</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_name' ? 'selected' : ''; ?>
                      value="item_name">Item Name</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_image' ? 'selected' : ''; ?>
                      value="item_image">Item Image</option>
                    <option <?php echo $tableOptions['value_amount'] === 'description' ? 'selected' : ''; ?>
                      value="description">Description</option>
                    <option <?php echo $tableOptions['value_amount'] === 'purchase_uom' ? 'selected' : ''; ?>
                      value="purchase_uom">UOM</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_quantity' ? 'selected' : ''; ?>
                      value="item_quantity">Quantity</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_price' ? 'selected' : ''; ?>
                      value="item_price">Item Price</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_amount' ? 'selected' : ''; ?>
                      value="item_amount">Item Amount</option>
                    <option <?php echo $tableOptions['value_amount'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $tableOptions['value_amount'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo (empty($tableOptions['value_amount']) || $tableOptions['value_amount'] === 'total_amount') ? 'selected' : ''; ?> value="total_amount">Total
                      Amount</option>
                    <option <?php echo $tableOptions['value_amount'] === 'reason_id' ? 'selected' : ''; ?>
                      value="reason_id">Reason Id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_wa_cost' ? 'selected' : ''; ?>
                      value="item_wa_cost">Item Wa Cost</option>
                    <option <?php echo $tableOptions['value_amount'] === 'item_st_cost' ? 'selected' : ''; ?>
                      value="item_st_cost">Item St Cost</option>
                    <option <?php echo $tableOptions['value_amount'] === 'doc_ref_type' ? 'selected' : ''; ?>
                      value="doc_ref_type">Doc Ref Type</option>
                    <option <?php echo $tableOptions['value_amount'] === 'doc_ref_id' ? 'selected' : ''; ?>
                      value="doc_ref_id">Doc Ref Id</option>
                    <option <?php echo $tableOptions['value_amount'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $tableOptions['value_amount'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $tableOptions['value_amount'] === 'req_date' ? 'selected' : ''; ?>
                      value="req_date">Req Date</option>
                    <option <?php echo $tableOptions['value_amount'] === 'approved_quantity' ? 'selected' : ''; ?>
                      value="approved_quantity">Approved Quantity</option>
                    <option <?php echo $tableOptions['value_amount'] === 'transaction_type' ? 'selected' : ''; ?>
                      value="transaction_type">Transaction Type</option>
                    <option <?php echo $tableOptions['value_amount'] === 'transaction_qty' ? 'selected' : ''; ?>
                      value="transaction_qty">Transaction Qty</option>
                    <option <?php echo $tableOptions['value_amount'] === 'pending_qty' ? 'selected' : ''; ?>
                      value="pending_qty">Pending Qty</option>
                    <option <?php echo $tableOptions['value_amount'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Height Options -->
            <div id="itemsshow" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="itemsInput" class="form-label">Items In Each Page Show</label>
                <input type="number" class="form-control" id="itemsInput"
                  value="<?php echo $tableOptions['itemstable'] ?? '20'; ?>"
                  placeholder="Enter No. Of Items In Each Page Show">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6>Footer Options</h6>
            <div class="btn-group d-flex flex-wrap">
              <button class="btn btn-outline-danger btn-sm mb-2" data-target="page_footerVisibility">
                <i class="bi bi-eye"></i> Visibility
              </button>
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="page_footer_optionsVisibility">
                <i class="bi bi-aspect-ratio"></i> Footer Options
              </button>
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="page_footer_positions">
                <i class="bi bi-aspect-ratio"></i> Footer Positions
              </button>
            </div>
            <?php
            $footerOptions = json_decode($invdata['footer_options'], true);
            ?>
            <!-- Visibility Fotter -->
            <div id="page_footerVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label class="form-label mb-2">Toggle Page Footer</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpagefooter" <?php echo $footerOptions['showFooter'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showpagefooter">Show Page Footer</label>
                </div>
              </div>
            </div>

            <!-- Footer options Visibility -->
            <div id="page_footer_optionsVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="page_footer_optionsVisibility" class="form-label">Footer Options Visibility</label>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalinalpha" checked>
                  <label class="form-check-label" for="showtotalinalpha">Show Total In Alphabet</label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showsignature" checked>
                  <label class="form-check-label" for="showsignature">Show Signature</label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showqr" checked>
                  <label class="form-check-label" for="showqr">Show QR</label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalbeforetax" <?php echo $footerOptions['totalbeforetax']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtotalbeforetax">Show Total Before Tax</label>
                  <input class="form-control mb-1" type="text" id="heading1"
                    value="<?php echo !empty($footerOptions['totalbeforetax']['heading']) ? $footerOptions['totalbeforetax']['heading'] : 'Total Before Tax'; ?>">
                  <select class="form-select" id="value1" style="display: none;">
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'id' ? 'selected' : ''; ?>
                      value="id">ID </option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cost_center_id' ? 'selected' : ''; ?> value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_number' ? 'selected' : ''; ?> value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_date' ? 'selected' : ''; ?> value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_tax_number' ? 'selected' : ''; ?> value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?> value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'originator_state_id' ? 'selected' : ''; ?> value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'supplier_state_id' ? 'selected' : ''; ?> value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'place_of_supply' ? 'selected' : ''; ?> value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'printing_notes' ? 'selected' : ''; ?> value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo (empty($footerOptions['totalbeforetax']['value']) || $footerOptions['totalbeforetax']['value'] === 'amount') ? 'selected' : ''; ?> value="amount"> Amount
                    </option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'type' ? 'selected' : ''; ?>
                      value="type">Type </option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_value' ? 'selected' : ''; ?> value="discount_value">Discount Value</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'discount_amount' ? 'selected' : ''; ?> value="discount_amount">Discount Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_amount' ? 'selected' : ''; ?> value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?> value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?> value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'pending_amount' ? 'selected' : ''; ?> value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice' ? 'selected' : ''; ?> alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?> value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'additional_charges' ? 'selected' : ''; ?> value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['totalbeforetax']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showdiscount" <?php echo $footerOptions['discount']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showdiscount">Show Discount</label>
                  <input class="form-control mb-1" type="text" id="heading2"
                    value="<?php echo !empty($footerOptions['discount']['heading']) ? $footerOptions['totalbeforetax']['heading'] : 'Discount'; ?>">
                  <select class="form-select" id="value2" style="display: none;">
                    <option <?php echo $footerOptions['discount']['value'] === 'id' ? 'selected' : ''; ?> value="id">ID
                    </option>
                    <option <?php echo $footerOptions['discount']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'amount' ? 'selected' : ''; ?>
                      value="amount"> Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'type' ? 'selected' : ''; ?> value="type">
                      Type </option>
                    <option <?php echo $footerOptions['discount']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo (empty($footerOptions['discount']['value']) || $footerOptions['discount']['value'] === 'discount_amount') ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['discount']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotaltaxamount" <?php echo $footerOptions['totaltaxamount']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtotaltaxamount">Show Total Tax Amount</label>
                  <input class="form-control mb-1" type="text" id="heading3"
                    value="<?php echo !empty($footerOptions['totaltaxamount']['heading']) ? $footerOptions['totalbeforetax']['heading'] : 'Total Tax Amount'; ?>">
                  <select class="form-select" id="value3" style="display: none;">
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'id' ? 'selected' : ''; ?>
                      value="id">ID </option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cost_center_id' ? 'selected' : ''; ?> value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_number' ? 'selected' : ''; ?> value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_date' ? 'selected' : ''; ?> value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_tax_number' ? 'selected' : ''; ?> value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?> value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'originator_state_id' ? 'selected' : ''; ?> value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'supplier_state_id' ? 'selected' : ''; ?> value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'place_of_supply' ? 'selected' : ''; ?> value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'printing_notes' ? 'selected' : ''; ?> value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'amount' ? 'selected' : ''; ?>
                      value="amount"> Amount</option>
                    <option <?php echo (empty($footerOptions['totaltaxamount']['value']) || $footerOptions['totaltaxamount']['value'] === 'tax_amount') ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'type' ? 'selected' : ''; ?>
                      value="type">Type </option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_value' ? 'selected' : ''; ?> value="discount_value">Discount Value</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'discount_amount' ? 'selected' : ''; ?> value="discount_amount">Discount Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_amount' ? 'selected' : ''; ?> value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?> value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?> value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'pending_amount' ? 'selected' : ''; ?> value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice' ? 'selected' : ''; ?> alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?> value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'additional_charges' ? 'selected' : ''; ?> value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['totaltaxamount']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showroundedoff" <?php echo $footerOptions['roundedoff']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showroundedoff">Show Rounded Off</label>
                  <input class="form-control mb-1" type="text" id="heading4"
                    value="<?php echo !empty($footerOptions['roundedoff']['heading']) ? $footerOptions['totalbeforetax']['heading'] : 'Rounded Off'; ?>">
                  <select class="form-select" id="value4" style="display: none;">
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'id' ? 'selected' : ''; ?> value="id">ID
                    </option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_number' ? 'selected' : ''; ?> value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?> value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'originator_state_id' ? 'selected' : ''; ?> value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'amount' ? 'selected' : ''; ?>
                      value="amount"> Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'type' ? 'selected' : ''; ?>
                      value="type">Type </option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?> value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?> value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?> value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'additional_charges' ? 'selected' : ''; ?> value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['roundedoff']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalamount" <?php echo $footerOptions['totalamount']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtotalamount">Show Total Amount</label>
                  <input class="form-control mb-1" type="text" id="heading5"
                    value="<?php echo !empty($footerOptions['totalamount']['heading']) ? $footerOptions['totalamount']['heading'] : 'Total Amount'; ?>">
                  <select class="form-select" id="value5" style="display: none;">
                    <option <?php echo $footerOptions['totalamount']['value'] === 'id' ? 'selected' : ''; ?> value="id">ID
                    </option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_number' ? 'selected' : ''; ?> value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?> value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'originator_state_id' ? 'selected' : ''; ?> value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'supplier_state_id' ? 'selected' : ''; ?> value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'amount' ? 'selected' : ''; ?>
                      value="amount"> Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'type' ? 'selected' : ''; ?>
                      value="type">Type </option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo (empty($footerOptions['totalamount']['value']) || $footerOptions['totalamount']['value'] === 'total_amount') ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?> value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?> value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'pending_amount' ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?> value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'additional_charges' ? 'selected' : ''; ?> value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['totalamount']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotaldue" <?php echo $footerOptions['totaldue']['visible'] ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="showtotaldue">Show Total Due</label>
                  <input class="form-control mb-1" type="text" id="heading6"
                    value="<?php echo !empty($footerOptions['totaldue']['heading']) ? $footerOptions['totaldue']['heading'] : 'Total Due'; ?>">
                  <select class="form-select" id="value6" style="display: none;">
                    <option <?php echo $footerOptions['totaldue']['value'] === 'id' ? 'selected' : ''; ?> value="id">ID
                    </option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'company_id' ? 'selected' : ''; ?>
                      value="company_id">Company Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'cost_center_id' ? 'selected' : ''; ?>
                      value="cost_center_id">Cost Center Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_number' ? 'selected' : ''; ?>
                      value="transaction_number">Transaction Number</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_date' ? 'selected' : ''; ?>
                      value="transaction_date">Transaction Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_location_id' ? 'selected' : ''; ?> value="transaction_location_id">Transaction Location Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'transaction_warehouse_id' ? 'selected' : ''; ?> value="transaction_warehouse_id">Transaction Warehouse Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'originator_currency_id' ? 'selected' : ''; ?> value="originator_currency_id">Originator Currency Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'currency_id' ? 'selected' : ''; ?>
                      value="currency_id">Currency Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'currency_rate' ? 'selected' : ''; ?>
                      value="currency_rate">Currency Rate</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'originator' ? 'selected' : ''; ?>
                      value="originator">Originator</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_id' ? 'selected' : ''; ?>
                      value="party_id">Party Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_name' ? 'selected' : ''; ?>
                      value="party_name">Party Name</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_email' ? 'selected' : ''; ?>
                      value="party_email">Party Email</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_tax_number' ? 'selected' : ''; ?>
                      value="party_tax_number">Party Tax Number</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_phone' ? 'selected' : ''; ?>
                      value="party_phone">Party Phone</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_address' ? 'selected' : ''; ?>
                      value="party_address">Party Address</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_ship_adderss' ? 'selected' : ''; ?>
                      value="party_ship_adderss">Party Ship Adderss</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'originator_state_id' ? 'selected' : ''; ?>
                      value="originator_state_id">Originator State Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'supplier_state_id' ? 'selected' : ''; ?>
                      value="supplier_state_id">Supplier State Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'place_of_supply' ? 'selected' : ''; ?>
                      value="place_of_supply">Place Of Supply</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'payment_terms' ? 'selected' : ''; ?>
                      value="payment_terms">Payment Terms</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'notes' ? 'selected' : ''; ?>
                      value="notes"> Notes</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'printing_notes' ? 'selected' : ''; ?>
                      value="printing_notes">Printing Notes</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'parent_id' ? 'selected' : ''; ?>
                      value="parent_id">Parent Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'amount' ? 'selected' : ''; ?>
                      value="amount"> Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'tax_amount' ? 'selected' : ''; ?>
                      value="tax_amount">Tax Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'type' ? 'selected' : ''; ?> value="type">
                      Type </option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'tax_type' ? 'selected' : ''; ?>
                      value="tax_type">Tax Type</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'discount_type' ? 'selected' : ''; ?>
                      value="discount_type">Discount Type</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'discount_value' ? 'selected' : ''; ?>
                      value="discount_value">Discount Value</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'discount_amount' ? 'selected' : ''; ?>
                      value="discount_amount">Discount Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'total_amount' ? 'selected' : ''; ?>
                      value="total_amount">Total Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_tax' ? 'selected' : ''; ?>
                      value="shipping_tax">Shipping Tax</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_amount' ? 'selected' : ''; ?>
                      value="shipping_amount">Shipping Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'shipping_tax_amount' ? 'selected' : ''; ?>
                      value="shipping_tax_amount">Shipping Tax Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'tax_state' ? 'selected' : ''; ?>
                      value="tax_state">Tax State</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'opex_cpex' ? 'selected' : ''; ?>
                      value="opex_cpex">Opex Cpex</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'doc_ref_no' ? 'selected' : ''; ?>
                      value="doc_ref_no">Doc Ref No</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'doc_ref_date' ? 'selected' : ''; ?>
                      value="doc_ref_date">Doc Ref Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'due_date' ? 'selected' : ''; ?>
                      value="due_date">Due Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'ref_type' ? 'selected' : ''; ?>
                      value="ref_type">Ref Type</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'parent_ref_id' ? 'selected' : ''; ?>
                      value="parent_ref_id">Rarent Ref Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'ref_id' ? 'selected' : ''; ?>
                      value="ref_id"> Ref Id</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'ref_no' ? 'selected' : ''; ?>
                      value="ref_no"> Ref No</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'ref_date' ? 'selected' : ''; ?>
                      value="ref_date">Ref Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'ref_bill_type' ? 'selected' : ''; ?>
                      value="ref_bill_type">Ref Bill Type</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'old_ref_no' ? 'selected' : ''; ?>
                      value="old_ref_no">Old Ref No</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'old_ref_date' ? 'selected' : ''; ?>
                      value="old_ref_date">Old Ref Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'party_additional_details' ? 'selected' : ''; ?> value="party_additional_details">Party Additional Details</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'follow_up_level_ids' ? 'selected' : ''; ?>
                      value="follow_up_level_ids">Follow Up Level Ids</option>
                    <option <?php echo (empty($footerOptions['totaldue']['value']) || $footerOptions['totaldue']['value'] === 'pending_amount') ? 'selected' : ''; ?>
                      value="pending_amount">Pending Amount</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice' ? 'selected' : ''; ?>
                      alue="cancel_invoice">Cancel Invoice</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice_date' ? 'selected' : ''; ?>
                      value="cancel_invoice_date">Cancel Invoice Date</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'cancel_invoice_reason' ? 'selected' : ''; ?> value="cancel_invoice_reason">Cancel Invoice Reason</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'additional_charges' ? 'selected' : ''; ?>
                      value="additional_charges">Additional Charges</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'pdf_name' ? 'selected' : ''; ?>
                      value="pdf_name">Pdf Name</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'owner_additional_details' ? 'selected' : ''; ?> value="owner_additional_details">Owner Additional Details</option>
                    <option <?php echo $footerOptions['totaldue']['value'] === 'serial_transaction_number' ? 'selected' : ''; ?> value="serial_transaction_number">Serial Transaction Number</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Address Position Options -->
            <div id="page_footer_positions" class="dynamic-options">
              <div class="card card-body mt-2">
                <div class="form-group">
                  <label for="address1position" class="form-label">Signature & QR</label>
                  <select class="form-select" id="signatureqr1position">
                    <option value="left" <?php echo (empty($footerOptions['positions']['signatureqr']) || $footerOptions['positions']['signatureqr'] === 'left') ? 'selected' : ''; ?>>Left</option>
                    <option value="right" <?php echo $footerOptions['positions']['signatureqr'] === 'right' ? 'selected' : ''; ?>>Right</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="address2position" class="form-label">Invoice Summary</label>
                  <select class="form-select" id="invoicesummary2position">
                    <option value="left" <?php echo $footerOptions['positions']['invoicesummary'] === 'left' ? 'selected' : ''; ?>>Left</option>
                    <option value="right" <?php echo (empty($footerOptions['positions']['invoicesummary']) || $footerOptions['positions']['invoicesummary'] === 'right') ? 'selected' : ''; ?>>Right</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="card-header p-0 text-center">
            <h3 class="card-title">Invoice Preview</h3>
          </div>
          <form action="" method="post">
            <div class="invoice-preview bg-light bordered" id="invoicePreview"
              style="min-height: 400px; position: relative;height: <?php echo $height; ?>px;font-family:<?php echo $fontFamily; ?>;margin:<?php echo $margin; ?>px;padding:<?php echo $padding; ?>px;
              border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;">
              <div class="top-header logosec" style="position:relative;width: 100%;">
                <div class="head-one">
                  <img id="logoPreview" src="<?php echo $image; ?>" alt="<?php echo $image; ?>"
                    style="display: block;position: absolute; max-height: 80px; left: 0; top: 0;<?php echo !$logoOptions['showLogo'] ? 'display: none;' : ''; ?>">
                  <div class="invoice-no" style="display: block; text-align: right;">
                    <p class="invoice" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;">Invoice
                        No</span></p>
                    <p class="date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;">Date</span>
                    </p>
                    <p class="due_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;">Due
                        Date</span></p>
                    <p class="po_num" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;">P.O.
                        Number</span></p>
                    <p class="po_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;">P.O
                        Date</span></p>
                  </div>
                </div>
              </div>
              <div class="address-sec bordered headersec"
                style="padding: .5rem;margin-top: 5px;border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;">
                <div class="address-sec"
                  style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                  <div class="first_address">
                    <strong class="address1heading" style="font-weight: 600;">Invoiced To:</strong>
                    <address class="address1data">
                      xxxx
                    </address>
                  </div>
                  <div class="second_address">
                    <strong class="address2heading" style="font-weight: 600;">Bill To:</strong>
                    <address class="address2data">
                      xxxx
                    </address>
                  </div>
                  <div class="third_address">
                    <strong class="address3heading" style="font-weight: 600;">Company</strong>
                    <address class="address3data">
                      xxxx
                    </address>
                  </div>
                </div>
              </div>
              <div class="items tablesec" style="margin-top: .5rem;">
                <div class="table-responsive">
                  <table class="table bordered mb-10"
                    style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;">
                    <thead>
                      <tr class="">
                        <td
                          style="<?php echo (!$tableOptions['showColumns']['item']) ? 'display: none;' : ''; ?> border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered col-1"><strong>S.No.</strong></td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered img col-2">
                          <strong><?php echo !empty($tableOptions['heading_img']) ? $tableOptions['heading_img'] : 'Img'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered item col-2">
                          <strong><?php echo !empty($tableOptions['heading_item']) ? $tableOptions['heading_item'] : 'Item'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered des col-2">
                          <strong><?php echo !empty($tableOptions['heading_des']) ? $tableOptions['heading_des'] : 'Description'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered hsnsac col-2 text-end">
                          <strong><?php echo !empty($tableOptions['heading_hsnsac']) ? $tableOptions['heading_hsnsac'] : 'Hsn/Sac'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered uom col-2">
                          <strong><?php echo !empty($tableOptions['heading_uom']) ? $tableOptions['heading_uom'] : 'UOM'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered tax col-2 text-end">
                          <strong><?php echo !empty($tableOptions['heading_tax']) ? $tableOptions['heading_tax'] : 'Tax'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered rate col-2 text-end">
                          <strong><?php echo !empty($tableOptions['heading_rate']) ? $tableOptions['heading_rate'] : 'Rate'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered qty col-1 text-end">
                          <strong><?php echo !empty($tableOptions['heading_qty']) ? $tableOptions['heading_qty'] : 'QTY'; ?></strong>
                        </td>
                        <td
                          style="border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;"
                          class="bordered amount col-2 text-end">
                          <strong><?php echo !empty($tableOptions['heading_amount']) ? $tableOptions['heading_amount'] : 'Amount'; ?></strong>
                        </td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="data">
                        <td class="col-1"
                          style="<?php echo (!$tableOptions['showColumns']['item']) ? 'display: none;' : ''; ?>">
                          1</td>
                        <td class="img col-2">
                          <img width="35px" src="<?= $image ?>" alt="<?= $image ?>">
                        </td>
                        <td class="item col-3 text-1">
                          xxxx
                        </td>
                        <td class="des col-3 text-1">
                          xxxx
                        </td>
                        <td class="hsnsac col-3 text-end">
                          xxxx
                        </td>
                        <td class="uom col-3 text-1">
                          xxxx
                        </td>
                        <td class="tax col-3 text-1 text-end" data-original="23.00">xxxx</td>
                        <td class="rate col-2 text-end">
                          xxxx
                        </td>
                        <td class="qty col-1 text-end">
                          xxxx
                        </td>
                        <td class="amount col-2 text-end">
                          xxxx
                        </td>
                      </tr>
                      <tr class="tax-summary"
                        style="<?php echo !$tableOptions['showColumns']['taxsum'] ? 'display: none;' : ''; ?>">
                        <td style="<?php echo (!$tableOptions['showColumns']['item']) ? 'display: none;' : ''; ?><?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered col-1"></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered img col-2"></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered item col-3 text-end"> <br> <br><span
                            style="font-weight: 700;">Total</span></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered des col-3 text-1"></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered hsnsac col-3 text-end">@xx% <br> @xx%</td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered uom col-3 text-1"></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered tax col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered rate col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered qty col-1 text-end"></td>
                        <td style="<?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;" class="bordered amount col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="page_footer bordered footersec"
                style="margin-top: .5rem;border-style: <?php echo $borderstyle; ?>; border-color: <?php echo $bordercolor; ?>; border-width: <?php echo $borderwidth; ?>px;">
                <div style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                  <div class="first_col signatureqr">
                    <p class="totalinwords">Total : Twenty Five Thousand Seven Hundred Seventy Eight</p>
                    <div style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                      <div class="signature">
                        <h6 style="text-decoration: underline;margin-bottom: 5px;">Autherized Signature</h6>
                        <img width="180px"
                          src="https://onlinepngtools.com/images/examples-onlinepngtools/george-walker-bush-signature.png"
                          alt="https://onlinepngtools.com/images/examples-onlinepngtools/george-walker-bush-signature.png">
                      </div>
                      <div class="qr">
                        <img width="120px" src="https://pngimg.com/uploads/qr_code/small/qr_code_PNG33.png"
                          alt="https://pngimg.com/uploads/qr_code/small/qr_code_PNG33.png">
                      </div>
                    </div>
                  </div>
                  <div class="invoicesummary"
                    style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px; padding: 5px;">
                    <div class="sec_col">
                      <h6 class="totalbeforetax heading1"
                        style="text-align: right; <?php echo isset($footerOptions['totalbeforetax']['visible']) && !$footerOptions['totalbeforetax']['visible'] ? 'display: none;' : ''; ?>">
                        Total Before Tax
                      </h6>
                      <h6 class="discount heading2"
                        style="text-align: right; <?php echo isset($footerOptions['discount']['visible']) && !$footerOptions['discount']['visible'] ? 'display: none;' : ''; ?>">
                        Discount
                      </h6>
                      <h6 class="totaltaxamount heading3"
                        style="text-align: right; <?php echo isset($footerOptions['totaltaxamount']['visible']) && !$footerOptions['totaltaxamount']['visible'] ? 'display: none;' : ''; ?>">
                        Total Tax Amount
                      </h6>
                      <h6 class="roundedoff heading4"
                        style="text-align: right; <?php echo isset($footerOptions['roundedoff']['visible']) && !$footerOptions['roundedoff']['visible'] ? 'display: none;' : ''; ?>">
                        Rounded Off
                      </h6>
                      <h6 class="totalamount heading5"
                        style="text-align: right; <?php echo isset($footerOptions['totalamount']['visible']) && !$footerOptions['totalamount']['visible'] ? 'display: none;' : ''; ?>">
                        Total Amount
                      </h6>
                      <h6 class="totaldue heading6"
                        style="text-align: right; <?php echo isset($footerOptions['totaldue']['visible']) && !$footerOptions['totaldue']['visible'] ? 'display: none;' : ''; ?>">
                        Total Due
                      </h6>
                    </div>
                    <div class="third_col">
                      <h6 class="totalbeforetax value1"
                        style="text-align: right;<?php echo isset($footerOptions['totalbeforetax']['visible']) && !$footerOptions['totalbeforetax']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                      <h6 class="discount value1"
                        style="text-align: right;<?php echo isset($footerOptions['discount']['visible']) && !$footerOptions['discount']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                      <h6 class="totaltaxamount value3"
                        style="text-align: right;<?php echo isset($footerOptions['totaltaxamount']['visible']) && !$footerOptions['totaltaxamount']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                      <h6 class="roundedoff value4"
                        style="text-align: right;<?php echo isset($footerOptions['roundedoff']['visible']) && !$footerOptions['roundedoff']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                      <h6 class="totalamount value5"
                        style="text-align: right;<?php echo isset($footerOptions['totalamount']['visible']) && !$footerOptions['totalamount']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                      <h6 class="totaldue value6"
                        style="text-align: right;<?php echo isset($footerOptions['totaldue']['visible']) && !$footerOptions['totaldue']['visible'] ? 'display: none;' : ''; ?>">
                        xxxx</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-2">
              <button name="submitinvoice" type="submit" class="btn btn-primary mt-3 w-100" <?= !empty($invoice_options) ? '' : 'disabled'; ?>>Save Changes</button>
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
  };

  const saveAddressSettings = () => {
    const settings = {
      showFirstAddress: document.getElementById('showFirstAddress').checked,
      firstAddressValue: document.getElementById('address1Select').value,
      firstAddressHeading: document.getElementById('showFirstAddress').checked
        ? document.getElementById('inputheading1').value
        : '',
      firstAddressPosition: document.getElementById('address1position').value,

      showSecondAddress: document.getElementById('showSecondAddress').checked,
      secondAddressValue: document.getElementById('address2Select').value,
      secondAddressHeading: document.getElementById('showSecondAddress').checked
        ? document.getElementById('inputheading2').value
        : '',
      secondAddressPosition: document.getElementById('address2position').value,

      showThirdAddress: document.getElementById('showThirdAddress').checked,
      thirdAddressValue: document.getElementById('address3Select').value,
      thirdAddressHeading: document.getElementById('showThirdAddress').checked
        ? document.getElementById('inputheading3').value
        : '',
      thirdAddressPosition: document.getElementById('address3position').value,
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

  document.addEventListener('DOMContentLoaded', () => {
    saveAddressSettings();
    updateAddressPreview();
  });


  // buttons
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
      invoiceNoValue: document.getElementById('invoicenoSelect').value,
      showDate: document.getElementById('showdate').checked,
      dateValue: document.getElementById('invoicedateSelect').value,
      showDueDate: document.getElementById('showduedate').checked,
      dueDateValue: document.getElementById('invoiceduedateSelect').value,
      showPoNum: document.getElementById('showpo_num').checked,
      poNumValue: document.getElementById('invoiceponumSelect').value,
      showPoDate: document.getElementById('showpo_date').checked,
      poDateValue: document.getElementById('invoicepodateSelect').value,
    };

    localStorage.setItem('topHeaderSettings', JSON.stringify(settings));
  };

  const updateTopHeaderPreview = () => {
    const elements = [
      { checkboxId: 'showinvoiceno', selectId: 'invoicenoSelect', previewSelector: '.invoice-no .invoice span', label: 'Invoice No' },
      { checkboxId: 'showdate', selectId: 'invoicedateSelect', previewSelector: '.invoice-no .date span', label: 'Date' },
      { checkboxId: 'showduedate', selectId: 'invoiceduedateSelect', previewSelector: '.invoice-no .due_date span', label: 'Due Date' },
      { checkboxId: 'showpo_num', selectId: 'invoiceponumSelect', previewSelector: '.invoice-no .po_num span', label: 'P.O. Number' },
      { checkboxId: 'showpo_date', selectId: 'invoicepodateSelect', previewSelector: '.invoice-no .po_date span', label: 'P.O Date' },
    ];

    elements.forEach(({ checkboxId, selectId, previewSelector, label }) => {
      const checkbox = document.getElementById(checkboxId);
      const select = document.getElementById(selectId);
      const preview = document.querySelector(previewSelector);

      select.style.display = checkbox.checked ? 'block' : 'none';

      if (checkbox.checked) {
        preview.textContent = `${label}: xxxx`;
        preview.parentElement.style.display = 'block';
      } else {
        preview.parentElement.style.display = 'none';
      }
    });

    saveTopHeaderSettings();
  };

  const setupEventListeners = () => {
    document.querySelectorAll('.form-check-input').forEach((checkbox) => {
      checkbox.addEventListener('change', updateTopHeaderPreview);
    });

    document.querySelectorAll('.form-select').forEach((select) => {
      select.addEventListener('change', updateTopHeaderPreview);
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

    // Load stored values on page load
    document.addEventListener('DOMContentLoaded', () => {
      const storedHeaders = JSON.parse(localStorage.getItem('tableSelectHeaders')) || {};

      Object.keys(selectElements).forEach((key) => {
        const checkbox = document.getElementById(key);
        const select = document.getElementById(selectElements[key].selectId);

        if (checkbox && select) {
          select.style.display = checkbox.checked ? 'block' : 'none';

          // Set saved value
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

      saveSelectHeadersToLocalStorage(); // Save when page loads to ensure consistency
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

    // Load stored headers on page load
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

          // Set saved value
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

      saveHeaderToLocalStorage(); // Save when page loads
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

  // footer

  document.addEventListener('DOMContentLoaded', () => {
    // Checkbox elements
    const showTotalAlphaCheckbox = document.getElementById('showtotalinalpha');
    const showSignatureCheckbox = document.getElementById('showsignature');
    const showQrCheckbox = document.getElementById('showqr');
    const showFooterCheckbox = document.getElementById('showpagefooter');

    // Elements to toggle
    const totalAlpha = document.querySelector('.totalinwords');
    const signature = document.querySelector('.signature');
    const qr = document.querySelector('.qr');
    const footer = document.querySelector('.page_footer');

    // Function to toggle visibility
    const toggleVisibility = (element, isChecked) => {
      if (element) {
        element.style.display = isChecked ? 'block' : 'none';
      }
    };

    // Event listeners for checkboxes
    showTotalAlphaCheckbox.addEventListener('change', () => toggleVisibility(totalAlpha, showTotalAlphaCheckbox.checked));
    showSignatureCheckbox.addEventListener('change', () => toggleVisibility(signature, showSignatureCheckbox.checked));
    showQrCheckbox.addEventListener('change', () => toggleVisibility(qr, showQrCheckbox.checked));
    showFooterCheckbox.addEventListener('change', () => toggleVisibility(footer, showFooterCheckbox.checked));

    // Initialize visibility on page load
    toggleVisibility(totalAlpha, showTotalAlphaCheckbox.checked);
    toggleVisibility(signature, showSignatureCheckbox.checked);
    toggleVisibility(qr, showQrCheckbox.checked);
    toggleVisibility(footer, showFooterCheckbox.checked);

    // Footer settings key
    const footerKey = 'footerSettings';

    // Fields to manage
    const fields = {
      totalbeforetax: { elements: document.querySelectorAll('.totalbeforetax'), headingInput: 'heading1', valueSelect: 'value1', checkbox: 'showtotalbeforetax' },
      discount: { elements: document.querySelectorAll('.discount'), headingInput: 'heading2', valueSelect: 'value2', checkbox: 'showdiscount' },
      totaltaxamount: { elements: document.querySelectorAll('.totaltaxamount'), headingInput: 'heading3', valueSelect: 'value3', checkbox: 'showtotaltaxamount' },
      roundedoff: { elements: document.querySelectorAll('.roundedoff'), headingInput: 'heading4', valueSelect: 'value4', checkbox: 'showroundedoff' },
      totalamount: { elements: document.querySelectorAll('.totalamount'), headingInput: 'heading5', valueSelect: 'value5', checkbox: 'showtotalamount' },
      totaldue: { elements: document.querySelectorAll('.totaldue'), headingInput: 'heading6', valueSelect: 'value6', checkbox: 'showtotaldue' },
    };

    // Position settings
    const positions = {
      signatureqr: { element: document.querySelector('.signatureqr'), positionSelect: 'signatureqr1position' },
      invoicesummary: { element: document.querySelector('.invoicesummary'), positionSelect: 'invoicesummary2position' },
    };

    // Save settings to localStorage
    const saveFooterSettings = () => {
      const settings = {
        showFooter: showFooterCheckbox.checked,
        showFields: Object.keys(fields).reduce((acc, key) => {
          acc[key] = {
            visible: document.getElementById(fields[key].checkbox).checked,
            heading: document.getElementById(fields[key].headingInput).value,
            value: document.getElementById(fields[key].valueSelect).value,
          };
          return acc;
        }, {}),
        positions: Object.keys(positions).reduce((acc, key) => {
          acc[key] = document.getElementById(positions[key].positionSelect).value;
          return acc;
        }, {}),
      };
      localStorage.setItem(footerKey, JSON.stringify(settings));
    };

    // Update field preview
    const updatePreview = (key) => {
      const field = fields[key];
      const headingValue = document.getElementById(field.headingInput).value;
      field.elements.forEach((el) => {
        const headingClass = Array.from(el.classList).find((cls) => cls.startsWith('heading'));
        if (headingClass) el.textContent = headingValue;
      });
      saveFooterSettings();
    };

    // Toggle field visibility
    const toggleFieldVisibility = (key, isVisible) => {
      const field = fields[key];
      field.elements.forEach((el) => {
        el.style.display = isVisible ? 'block' : 'none';
      });
      document.getElementById(field.headingInput).style.display = isVisible ? 'block' : 'none';
      document.getElementById(field.valueSelect).style.display = isVisible ? 'block' : 'none';
    };

    // Update position of elements
    const updatePosition = (key) => {
      const position = positions[key];
      const positionValue = document.getElementById(position.positionSelect).value;
      position.element.style.order = positionValue === 'left' ? '0' : '1';
      saveFooterSettings();
    };

    // Event listeners for footer fields
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      document.getElementById(field.headingInput).addEventListener('input', () => updatePreview(key));
      document.getElementById(field.valueSelect).addEventListener('input', () => updatePreview(key));
      document.getElementById(field.checkbox).addEventListener('change', (e) => {
        toggleFieldVisibility(key, e.target.checked);
        saveFooterSettings();
      });
    });

    // Event listeners for positions
    Object.keys(positions).forEach((key) => {
      const position = positions[key];
      document.getElementById(position.positionSelect).addEventListener('change', () => updatePosition(key));
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    saveFooterSettings();
  });

  document.addEventListener('DOMContentLoaded', () => {
    const templateNameInput = document.getElementById('template_name');
    const saveButton = document.querySelector('button[name="submitinvoice"]');

    templateNameInput.addEventListener('input', () => {
      const currentValue = templateNameInput.value.trim();
      if (currentValue) {
        localStorage.setItem('templateName', currentValue);
        saveButton.disabled = false;
      } else {
        localStorage.removeItem('templateName');
        saveButton.disabled = true;
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
        const { showInvoiceNo, invoiceNoValue, showDate, dateValue, showDueDate, dueDateValue, showPoNum, poNumValue, showPoDate, poDateValue, } = topHeaderSettings;
        Object.assign(combinedLogoOptions, { showInvoiceNo, invoiceNoValue, showDate, dateValue, showDueDate, dueDateValue, showPoNum, poNumValue, showPoDate, poDateValue, });
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
        showamountcol, showtaxcol, showdescol, showhsnsaccol, showimagecol, showitemcol, showqtycol, showratecol, showuomcol,
      } = TableHeaders;

      Object.assign(tableOptions, {
        heading_amount: showamountcol,
        heading_tax: showtaxcol,
        heading_des: showdescol,
        heading_hsnsac: showhsnsaccol,
        heading_img: showimagecol,
        heading_item: showitemcol,
        heading_qty: showqtycol,
        heading_rate: showratecol,
        heading_uom: showuomcol,
      });
    }

    if (savedTableSelectHeaders) {
      const TableSelectHeaders = JSON.parse(savedTableSelectHeaders);
      const {
        showamountcol, showtaxcol, showdescol, showhsnsaccol, showimagecol, showitemcol, showqtycol, showratecol, showuomcol,
      } = TableSelectHeaders;

      Object.assign(tableOptions, {
        value_amount: showamountcol,
        value_tax: showtaxcol,
        value_des: showdescol,
        value_hsnsac: showhsnsaccol,
        value_img: showimagecol,
        value_item: showitemcol,
        value_qty: showqtycol,
        value_rate: showratecol,
        value_uom: showuomcol,
      });
    }

    if (savedTableSettings) {
      const TableSettings = JSON.parse(savedTableSettings);

      const {
        showTable, amount, des, hsnsac, tax, img, item, qty, rate, taxsum, uom,
      } = TableSettings;
      Object.assign(tableOptions, {
        showTable, amount, des, hsnsac, tax, img, item, qty, rate, taxsum, uom
      });
      if (TableSettings.showColumns) {
        const {
          amount, tax, des, hsnsac, img, item, qty, rate, taxsum, uom
        } = TableSettings.showColumns;
        tableOptions.showColumns = {
          amount, tax, des, hsnsac, img, item, qty, rate, taxsum, uom
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
          "totalinalpha",
          "signature",
          "qr",
          "totalbeforetax",
          "discount",
          "totaltaxamount",
          "roundedoff",
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


    const ridValue = document.getElementById('rid').value; // Get the value from the hidden input field

    const ridField = document.createElement("input");
    ridField.type = "hidden";
    ridField.name = "rid"; // Set the name of the input field
    ridField.value = ridValue; // Set the value of the input field
    ridField.classList.add("dynamic-hidden");

    form.appendChild(ridField);
  };

  document.querySelector("form").addEventListener("submit", (e) => {
    createHiddenFields();
  });
</script>