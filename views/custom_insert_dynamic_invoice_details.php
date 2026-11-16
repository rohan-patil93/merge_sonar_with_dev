<?php
$_SESSION['apply_js']['multiselest'] = 1;

if (isset($_POST['submitinvoice'])) {
  $_POST['company_id'] = $_SESSION['company_id'];
  $_POST['user_id'] = $_SESSION['userid'];
  $_POST['rid'] = 5;
  $url = API . "?mode=insertdynamicinvoice&method=post";
  $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
  $response = $response2['res'];
  if($response['status'] == 'sucess'){
    if($response['LASTID'] == 0){
        $msgcode = 3001; //update
    }else{
        $msgcode = 3000; //insert
        $_SESSION['insert_msg'] = $msgcode;
        header("location:index?option=COM_LIST_DYNAMIC_INVOICE_DETAILS");
    }
    ?>
      <script type="text/javascript">
        document.onreadystatechange = function(){
          if(document.readyState == "complete") {
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
          }
        }
      </script>
      <?php
}else{
    if($response['status'] == 'fail'){
        $dis_err = explode(",", $response['other']);
        foreach ($dis_err as $va) {
          $dis_er [] = $dis_labels[$va];
        }
        $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
      }else{
        $error_message = $response['message'];
      }
}
}

$rid1 =$_GET['Rid'];
$rid2 =  encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = $rrid['1'];

$url = API . "?mode=getinvocedetails&rid=" . $rid;
$response = callAPI($url);
$result = json_decode($response, 1);
$userData = $result['application_users'];
$invoiceData = $result['rental_invoices'];
$invoiceitemsData = $result['rental_invoice_items'];
$invoiceItemsTaxData = $result['rental_invoice_item_taxes'];
$hsnSacCodesData = $result['hsn_and_sac_codes'];
$File_Uploads_Path = "uploads/customer/";

if ($userData['user_photo'] != '') {
  $image = $File_Uploads_Path . $userData['user_photo'];
} else {
  $image = $File_Uploads_Path . "no-image.jpg";
}

?>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
<?php } ?>
<div class="card">
  <div class="card-header">
    <h5 class><?= __('Invoice') ?></h5>
    <div class="card-header-right">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
        <i class="icofont icofont-reply"></i> <?= __('Back'); ?>
      </a>
    </div>
  </div>
  <div class="card-body m-r-50 m-l-50">
    <div class="container mt-4">
      <div class="row ml-20 mr-20">
        <div class="col-lg-3 p-3 bg-light border">
          <h5 class="text-center mb-4"><?= __('Customization Options') ?></h5>
          <div class="mb-3">
            <h6><?= __('Template Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="template_name_container">
                <i class="bi bi-aspect-ratio"></i> <?= __('Template Name'); ?>
              </button>
            </div>


            <div id="template_name_container" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="template_name" class="form-label"><?= __('Template Name') ?></label>
                <div class="form-group">
                  <input class="form-control mb-1" type="text" id="template_name" placeholder="<?= __('Enter template name') ?>" aria-label="<?= htmlspecialchars(__('Enter template name'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6><?= __('Border Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-primary btn-sm mb-2" data-target="borderOptions">
                <i class="bi bi-border"></i> <?= __('Border'); ?>
              </a>
              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="colorOptions">
                <i class="bi bi-palette"></i> <?= __('Color'); ?>
              </a>
              <a class="btn btn-outline-info btn-sm mb-2" data-target="widthOptions">
                <i class="bi bi-border-width"></i> <?= __('Width'); ?>
              </a>
            </div>
            <div id="borderOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderStyle" class="form-label"><?= __('Border Style') ?></label>
                <select class="form-select" id="borderStyle" name="borderStyle" aria-label="<?= htmlspecialchars(__('Borderstyle'), ENT_QUOTES, 'UTF-8'); ?>">
                  <option value="solid"><?= __('Solid') ?></option>
                  <option value="dashed"><?= __('Dashed') ?></option>
                  <option value="dotted"><?= __('Dotted') ?></option>
                  <option value="none"><?= __('None') ?></option>
                </select>
              </div>
            </div>
            <div id="colorOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderColor" class="form-label"><?= __('Border Color') ?></label>
                <input type="color" class="form-control form-control-color" style="max-width:100%;" id="borderColor"
                  title="<?= __('Choose border color') ?>" value="" name="borderColor" aria-label="<?= htmlspecialchars(__('Choose border color'), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>
            <div id="widthOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="borderWidth" class="form-label"><?= __('Border Width') ?></label>
                <input type="number" class="form-control" id="borderWidth" min="1" max="10"
                  placeholder="<?= __('Enter width (in px)') ?>" value="" name="borderWidth" aria-label="<?= htmlspecialchars(__('Enter width (in px)'), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6><?= __('Basic Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">

              <a class="btn btn-outline-danger btn-sm mb-2" data-target="textStyleOptions">
                <i class="bi bi-fonts"></i> <?= __('Text Style'); ?>
              </a>

              <a class="btn btn-outline-primary btn-sm mb-2" data-target="marginOptions">
                <i class="bi bi-arrows-expand"></i> <?= __('Margin'); ?>
              </a>

              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="paddingOptions">
                <i class="bi bi-box"></i> <?= __('Padding'); ?>
              </a>

              <a class="btn btn-outline-info btn-sm mb-2" data-target="heightOptions">
                <i class="bi bi-arrow-up-down"></i> <?= __('Height'); ?>
              </a>
            </div>


            <div id="textStyleOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="fontFamily" class="form-label"><?= __('Font Family') ?></label>
                <select class="form-select" id="fontFamily" name="fontFamily" aria-label="<?= htmlspecialchars(__('Fontfamily'), ENT_QUOTES, 'UTF-8'); ?>">
                  <option value="Arial"><?= __('Arial') ?></option>
                  <option value="Times New Roman"><?= __('Times New Roman') ?></option>
                  <option value="Courier New"><?= __('Courier New') ?></option>
                  <option value="Verdana"><?= __('Verdana') ?></option>
                  <option value="Georgia"><?= __('Georgia') ?></option>
                  <option value="Trebuchet MS"><?= __('Trebuchet MS') ?></option>
                  <option value="Tahoma"><?= __('Tahoma') ?></option>
                  <option value="Roboto"><?= __('Roboto') ?></option>
                </select>
              </div>
            </div>


            <div id="marginOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="marginInput" class="form-label"><?= __('Margin (in px)') ?></label>
                <input type="number" class="form-control" id="marginInput" value="" name="marginInput" min="0"
                  placeholder="<?= __('Enter margin') ?>" aria-label="<?= htmlspecialchars(__('Enter margin'), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>


            <div id="paddingOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="paddingInput" class="form-label"><?= __('Padding (in px)') ?></label>
                <input type="number" class="form-control" id="paddingInput" value="" name="paddingInput" min="0"
                  placeholder="<?= __('Enter padding') ?>" aria-label="<?= htmlspecialchars(__('Enter padding'), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>


            <div id="heightOptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="heightInput" class="form-label"><?= __('Height (in px)') ?></label>
                <input type="number" class="form-control" id="heightInput" value="" name="heightInput" min="100"
                  placeholder="<?= __('Enter height') ?>" aria-label="<?= htmlspecialchars(__('Enter height'), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6><?= __('Logo Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-danger btn-sm mb-2" data-target="logoVisibility">
                <i class="bi bi-eye"></i> <?= __('Visibility'); ?>
              </a>
              <a class="btn btn-outline-warning btn-sm mb-2" data-target="logoPosition">
                <i class="bi bi-layout-sidebar"></i> <?= __('Position'); ?>
              </a>
              <a class="btn btn-outline-primary btn-sm mb-2" data-target="logoSize">
                <i class="bi bi-arrows-fullscreen"></i> <?= __('Size'); ?>
              </a>
              <a class="btn btn-outline-success btn-sm mb-2" data-target="topheaderoptions">
                <i class="bi bi-pencil"></i> <?= __('Top Header Options'); ?>
              </a>
            </div>

            <div id="logoVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Logo') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showLogo" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showLogo"><?= __('Show Logo') ?></label>
                </div>
              </div>
            </div>

            <div id="logoPosition" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="logoPositionSelect" class="form-label"><?= __('Logo Position') ?></label>
                <select class="form-select" id="logoPositionSelect" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                  <option value="left"><?= __('Left') ?></option>
                  <option value="center"><?= __('Center') ?></option>
                  <option value="right"><?= __('Right') ?></option>
                </select>
              </div>
            </div>

            <div id="logoSize" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="logoSizeSelect" class="form-label"><?= __('Logo Size') ?></label>
                <select class="form-select" id="logoSizeSelect" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                  <option value="small"><?= __('Small') ?></option>
                  <option value="medium"><?= __('Medium') ?></option>
                  <option value="large"><?= __('Large') ?></option>
                  <option value="custom"><?= __('Custom') ?></option>
                </select>
                <div id="customLogoSizeInputContainer" class="mt-2" style="display: none;">
                  <label for="customLogoSizeInput" class="form-label"><?= __('Custom Logo Size (px)') ?></label>
                  <input type="number" id="customLogoSizeInput" class="form-control" placeholder="<?= __('Enter size in px') ?>" / aria-label="<?= htmlspecialchars(__('Enter size in px'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>
            </div>

            <div id="topheaderoptions" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Top Header Options') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showinvoiceno" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showinvoiceno"><?= __('Show Invoice No.') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoiceno Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoicenoSelect" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showdate" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showdate"><?= __('Show Date') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoicedate Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoicedateSelect" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number"><?= __('Transaction Number') ?></option>
                    <option value="transaction_date" selected><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showduedate" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showduedate"><?= __('Show Due Date') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoiceduedate Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoiceduedateSelect" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number"><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date" selected><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpo_num" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showpo_num"><?= __('Show P.O. Number') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoiceponum Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoiceponumSelect" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number"><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no" selected><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpo_date" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showpo_date"><?= __('Show P.O Date') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoicepodate Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoicepodateSelect" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number"><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date" selected><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6><?= __('Header Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">
              <a class="btn btn-outline-danger btn-sm mb-2" data-target="addressVisibility">
                <i class="bi bi-eye"></i> <?= __('Visibility'); ?>
              </a>
              <a class="btn btn-outline-dark btn-sm mb-2" data-target="addresssecVisibility">
                <i class="bi bi-aspect-ratio"></i> <?= __('Address Visibility'); ?>
              </a>
              <a class="btn btn-outline-secondary btn-sm mb-2" data-target="addressPosition">
                <i class="bi bi-aspect-ratio"></i> <?= __('Address Position'); ?>
              </a>
              <a class="btn btn-outline-success btn-sm mb-2" data-target="addressSize">
                <i class="bi bi-pencil"></i> <?= __('Text Size'); ?>
              </a>
            </div>


            <div id="addressVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Header') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showAddress" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showAddress"><?= __('Show Header') ?></label>
                </div>
              </div>
            </div>


            <div id="addresssecVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="addressvisibilitySelect" class="form-label"><?= __('Header Address Visibility') ?></label>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showFirstAddress" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showFirstAddress"><?= __('Show Bill From Address') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Inputheading1'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="inputheading1" value="Invoiced To">
                  <select aria-label="<?= htmlspecialchars(__('Address1Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="address1Select" style="display: none;">
                    <option value="party_address" selected><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Shipping Address') ?></option>
                    <option value="company_address"><?= __('Company Address') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showSecondAddress" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showSecondAddress"><?= __('Show Bill TO Address') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Inputheading2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="inputheading2" value="Bill To">
                  <select aria-label="<?= htmlspecialchars(__('Address2Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="address2Select" style="display: none;">
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss" selected><?= __('Party Shipping Address') ?></option>
                    <option value="company_address"><?= __('Company Address') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showThirdAddress" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showThirdAddress"><?= __('Show Company Address') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Inputheading3'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="inputheading3" value="Company">
                  <select aria-label="<?= htmlspecialchars(__('Address3Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="address3Select" style="display: none;">
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Shipping Address') ?></option>
                    <option value="company_address" selected><?= __('Company Address') ?></option>
                  </select>
                </div>
              </div>
            </div>


            <div id="addressPosition" class="dynamic-options">
              <div class="card card-body mt-2">
                <div class="form-group">
                  <label for="address1position" class="form-label"><?= __('Address 1') ?></label>
                  <select class="form-select" id="address1position" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                    <option value="left" selected><?= __('Left') ?></option>
                    <option value="center"><?= __('Center') ?></option>
                    <option value="right"><?= __('Right') ?></option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="address2position" class="form-label"><?= __('Address 2') ?></label>
                  <select class="form-select" id="address2position" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                    <option value="left"><?= __('Left') ?></option>
                    <option value="center" selected><?= __('Center') ?></option>
                    <option value="right"><?= __('Right') ?></option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="address3position" class="form-label"><?= __('Address 3') ?></label>
                  <select class="form-select" id="address3position" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                    <option value="left"><?= __('Left') ?></option>
                    <option value="center"><?= __('Center') ?></option>
                    <option value="right" selected><?= __('Right') ?></option>
                  </select>
                </div>
              </div>
            </div>


            <div id="addressSize" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="addressSizeSelect" class="form-label"><?= __('Text Size') ?></label>
                <select class="form-select" id="addressSizeSelect" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                  <option value="small"><?= __('Small') ?></option>
                  <option value="medium" selected><?= __('Medium') ?></option>
                  <option value="large"><?= __('Large') ?></option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <h6><?= __('Table Options') ?></h6>
            <div class="btn-group d-flex flex-wrap">
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="tableVisibility">
                <i class="bi bi-eye"></i> <?= __('Visibility'); ?>
              </button>
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="tableAppearance">
                <i class="bi bi-palette"></i> <?= __('Appearance'); ?>
              </button>
              <button class="btn btn-outline-danger btn-sm mb-2" data-target="colsVisibility">
                <i class="bi bi-list"></i> <?= __('Table Options'); ?>
              </button>
              <button class="btn btn-outline-danger btn-sm mb-2" data-target="itemsshow">
                <i class="bi bi-list"></i> <?= __('Items Show'); ?>
              </button>
            </div>


            <div id="tableVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Table') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showTable" readonly disabled checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showTable"><?= __('Show Table') ?></label>
                </div>
              </div>
            </div>


            <div id="tableAppearance" class="dynamic-options">
              <div class="card card-body mt-2">
                <h6><?= __('Customize Table Appearance') ?></h6>

                <!-- Table Header Background -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Header Background') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Headerbg Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableHeaderbgColor" value="#f8f9fa">
                </div>

                <!-- Table Header color -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Header Text Color') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Header Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableHeaderColor" value="#ffffff">
                </div>

                <!-- Table Body Background -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Body Background') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Bodybg Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableBodybgColor" value="#ffffff">
                </div>

                <!-- Table body color -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table body Text Color') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Body Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableBodyColor" value="#ffffff">
                </div>

                <!-- Table Footer Background -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Footer Background') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Footerbg Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableFooterbgColor" value="#f8f9fa">
                </div>

                <!-- Table Header color -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Footer Text Color') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Table Footer Color'), ENT_QUOTES, 'UTF-8'); ?>" type="color" class="form-control form-control-color" style="max-width:100%;"
                    id="tableFooterColor" value="#ffffff">
                </div>

                <!-- Row Height -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Row Height') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Enter Row Height (px)'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control" id="rowHeight" min="20" placeholder="<?= __('Enter Row Height (px)') ?>">
                </div>

                <!-- Heading Height -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Heading Height') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Enter Heading Height (px)'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control" id="headingHeight" min="20"
                    placeholder="<?= __('Enter Heading Height (px)') ?>">
                </div>

                <!-- Font Size -->
                <div class="mb-1">
                  <span class="form-label"><?= __('Table Font Size') ?></span>
                  <input aria-label="<?= htmlspecialchars(__('Enter Font Size (px)'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control" id="fontSize" min="8" max="36"
                    placeholder="<?= __('Enter Font Size (px)') ?>">
                </div>
              </div>
            </div>


            <div id="colsVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Columns') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtaxsumcol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtaxsumcol"><?= __('Show Tax Summary') ?></label>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showimagecol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showimagecol"><?= __('Show Image Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="imagecolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemimage Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemimageSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="item_image" selected><?= __('Item Image') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showitemcol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showitemcol"><?= __('Show Item Name Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="itemcolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemname Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemnameSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name" selected><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input aria-label="<?= htmlspecialchars(__('Showdescol'), ENT_QUOTES, 'UTF-8'); ?>" class="form-check-input" type="checkbox" id="showdescol" checked>
                  <label class="form-check-label" for="showitemcol"><?= __('Show Item Description') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="descolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemdescription Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemdescriptionSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description" selected><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showhsnsaccol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showhsnsaccol"><?= __('Show HSN/SAC Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="hsnsaccolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemhsnsac Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemhsnsacSelect" style="display: none;">
                    <option value="hsn_and_sac_code_id" selected><?= __('HSN/SAC ID') ?></option>
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showuomcol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showuomcol"><?= __('Show UOM Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="uomcolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemuom Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemuomSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom" selected><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtaxcol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtaxcol"><?= __('Show TAX Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="taxcolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemtax Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemtaxSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount" selected><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showratecol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showratecol"><?= __('Show Rate Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="ratecolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemrate Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemrateSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price" selected><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showqtycol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showqtycol"><?= __('Show QTY Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="qtycolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemqty Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemqtySelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity" selected><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showamountcol" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showamountcol"><?= __('Show Amount Columns') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Enter Feild Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="amountcolumnInput" class="form-control" style="display: none;"
                    placeholder="<?= __('Enter Feild Name') ?>" />
                  <select aria-label="<?= htmlspecialchars(__('Itemamount Select'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select mt-1" id="itemamountSelect" style="display: none;">
                    <option value="id"><?= __('id') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="transaction_id"><?= __('Transaction Id') ?></option>
                    <option value="item_table_name"><?= __('Item Table Name') ?></option>
                    <option value="item_id"><?= __('Item Id') ?></option>
                    <option value="account_code"><?= __('Account Code') ?></option>
                    <option value="item_name"><?= __('Item Name') ?></option>
                    <option value="description"><?= __('Description') ?></option>
                    <option value="purchase_uom"><?= __('UOM') ?></option>
                    <option value="item_quantity"><?= __('Quantity') ?></option>
                    <option value="item_price"><?= __('Item Price') ?></option>
                    <option value="item_amount"><?= __('Item Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount" selected><?= __('Total Amount') ?></option>
                    <option value="reason_id"><?= __('Reason Id') ?></option>
                    <option value="item_wa_cost"><?= __('Item Wa Cost') ?></option>
                    <option value="item_st_cost"><?= __('Item St Cost') ?></option>
                    <option value="doc_ref_type"><?= __('Doc Ref Type') ?></option>
                    <option value="doc_ref_id"><?= __('Doc Ref Id') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="req_date"><?= __('Req Date') ?></option>
                    <option value="approved_quantity"><?= __('Approved Quantity') ?></option>
                    <option value="transaction_type"><?= __('Transaction Type') ?></option>
                    <option value="transaction_qty"><?= __('Transaction Qty') ?></option>
                    <option value="pending_qty"><?= __('Pending Qty') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                  </select>
                </div>
              </div>
            </div>


            <div id="itemsshow" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="itemsInput" class="form-label"><?= __('Items In Each Page Show') ?></label>
                <input type="number" class="form-control" id="itemsInput" min="100"
                  placeholder="<?= __('Enter No. Of Items In Each Page Show') ?>" aria-label="<?= htmlspecialchars(__('Enter No. Of Items In Each Page Show'), ENT_QUOTES, 'UTF-8'); ?>">
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
              <button class="btn btn-outline-dark btn-sm mb-2" data-target="page_footer_positions">
                <i class="bi bi-aspect-ratio"></i> <?= __('Footer Positions'); ?>
              </button>
            </div>


            <div id="page_footerVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <span class="form-label mb-2"><?= __('Toggle Page Footer') ?></span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showpagefooter" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showpagefooter"><?= __('Show Page Footer') ?></label>
                </div>
              </div>
            </div>


            <div id="page_footer_optionsVisibility" class="dynamic-options">
              <div class="card card-body mt-2">
                <label for="page_footer_optionsVisibility" class="form-label"><?= __('Footer Options Visibility') ?></label>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalinalpha" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtotalinalpha"><?= __('Show Total In Alphabet') ?></label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showsignature" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showsignature"><?= __('Show Signature') ?></label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showqr" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showqr"><?= __('Show QR') ?></label>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalbeforetax" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtotalbeforetax"><?= __('Show Total Before Tax') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading1'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading1" value="Total Before Tax">
                  <select aria-label="<?= htmlspecialchars(__('Value1'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value1" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount" selected><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showdiscount" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showdiscount"><?= __('Show Discount') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading2" value="Discount">
                  <select aria-label="<?= htmlspecialchars(__('Value2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value2" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount" selected><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotaltaxamount" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtotaltaxamount"><?= __('Show Total Tax Amount') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading3'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading3" value="Total Tax Amount">
                  <select aria-label="<?= htmlspecialchars(__('Value3'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value3" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount" selected><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showroundedoff" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showroundedoff"><?= __('Show Rounded Off') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading4'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading4" value="Rounded Off">
                  <select aria-label="<?= htmlspecialchars(__('Value4'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value4" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotalamount" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtotalamount"><?= __('Show Total Amount') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading5'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading5" value="Total Amount">
                  <select aria-label="<?= htmlspecialchars(__('Value5'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value5" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount" selected><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>

                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="showtotaldue" checked aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                  <label class="form-check-label" for="showtotaldue"><?= __('Show Total Due') ?></label>
                  <input aria-label="<?= htmlspecialchars(__('Heading6'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control mb-1" type="text" id="heading6" value="Total Due">
                  <select aria-label="<?= htmlspecialchars(__('Value6'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="value6" style="display: none;">
                    <option value="id"><?= __('ID') ?></option>
                    <option value="company_id"><?= __('Company Id') ?></option>
                    <option value="cost_center_id"><?= __('Cost Center Id') ?></option>
                    <option value="transaction_number" selected><?= __('Transaction Number') ?></option>
                    <option value="transaction_date"><?= __('Transaction Date') ?></option>
                    <option value="transaction_location_id"><?= __('Transaction Location Id') ?></option>
                    <option value="transaction_warehouse_id"><?= __('Transaction Warehouse Id') ?></option>
                    <option value="originator_currency_id"><?= __('Originator Currency Id') ?></option>
                    <option value="currency_id"><?= __('Currency Id') ?></option>
                    <option value="currency_rate"><?= __('Currency Rate') ?></option>
                    <option value="originator"><?= __('Originator') ?></option>
                    <option value="party_id"><?= __('Party Id') ?></option>
                    <option value="party_name"><?= __('Party Name') ?></option>
                    <option value="party_email"><?= __('Party Email') ?></option>
                    <option value="party_tax_number"><?= __('Party Tax Number') ?></option>
                    <option value="party_phone"><?= __('Party Phone') ?></option>
                    <option value="party_address"><?= __('Party Address') ?></option>
                    <option value="party_ship_adderss"><?= __('Party Ship Adderss') ?></option>
                    <option value="originator_state_id"><?= __('Originator State Id') ?></option>
                    <option value="supplier_state_id"><?= __('Supplier State Id') ?></option>
                    <option value="place_of_supply"><?= __('Place Of Supply') ?></option>
                    <option value="payment_terms"><?= __('Payment Terms') ?></option>
                    <option value="notes"><?= __('Notes') ?></option>
                    <option value="printing_notes"><?= __('Printing Notes') ?></option>
                    <option value="parent_id"><?= __('Parent Id') ?></option>
                    <option value="amount"><?= __('Amount') ?></option>
                    <option value="tax_amount"><?= __('Tax Amount') ?></option>
                    <option value="type"><?= __('Type') ?></option>
                    <option value="tax_type"><?= __('Tax Type') ?></option>
                    <option value="discount_type"><?= __('Discount Type') ?></option>
                    <option value="discount_value"><?= __('Discount Value') ?></option>
                    <option value="discount_amount"><?= __('Discount Amount') ?></option>
                    <option value="total_amount"><?= __('Total Amount') ?></option>
                    <option value="shipping_tax"><?= __('Shipping Tax') ?></option>
                    <option value="shipping_amount"><?= __('Shipping Amount') ?></option>
                    <option value="shipping_tax_amount"><?= __('Shipping Tax Amount') ?></option>
                    <option value="tax_state"><?= __('Tax State') ?></option>
                    <option value="opex_cpex"><?= __('Opex Cpex') ?></option>
                    <option value="doc_ref_no"><?= __('Doc Ref No') ?></option>
                    <option value="doc_ref_date"><?= __('Doc Ref Date') ?></option>
                    <option value="due_date"><?= __('Due Date') ?></option>
                    <option value="ref_type"><?= __('Ref Type') ?></option>
                    <option value="parent_ref_id"><?= __('Rarent Ref Id') ?></option>
                    <option value="ref_id"><?= __('Ref Id') ?></option>
                    <option value="ref_no"><?= __('Ref No') ?></option>
                    <option value="ref_date"><?= __('Ref Date') ?></option>
                    <option value="ref_bill_type"><?= __('Ref Bill Type') ?></option>
                    <option value="old_ref_no"><?= __('Old Ref No') ?></option>
                    <option value="old_ref_date"><?= __('Old Ref Date') ?></option>
                    <option value="party_additional_details"><?= __('Party Additional Details') ?></option>
                    <option value="follow_up_level_ids"><?= __('Follow Up Level Ids') ?></option>
                    <option value="pending_amount"><?= __('Pending Amount') ?></option>
                    <option value="cancel_invoice"><?= __('Cancel Invoice') ?></option>
                    <option value="cancel_invoice_date"><?= __('Cancel Invoice Date') ?></option>
                    <option value="cancel_invoice_reason"><?= __('Cancel Invoice Reason') ?></option>
                    <option value="additional_charges"><?= __('Additional Charges') ?></option>
                    <option value="pdf_name"><?= __('Pdf Name') ?></option>
                    <option value="owner_additional_details"><?= __('Owner Additional Details') ?></option>
                    <option value="serial_transaction_number"><?= __('Serial Transaction Number') ?></option>
                  </select>
                </div>
              </div>
            </div>


            <div id="page_footer_positions" class="dynamic-options">
              <div class="card card-body mt-2">
                <div class="form-group">
                  <label for="signatureqr1position" class="form-label"><?= __('Signature & QR') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Signatureqr1position'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="signatureqr1position">
                    <option value="left" selected><?= __('Left') ?></option>
                    <option value="right"><?= __('Right') ?></option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="invoicesummary2position" class="form-label"><?= __('Invoice Summary') ?></label>
                  <select aria-label="<?= htmlspecialchars(__('Invoicesummary2position'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select" id="invoicesummary2position">
                    <option value="left"><?= __('Left') ?></option>
                    <option value="right"><?= __('Right') ?></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <form action="" method="post">
            <div class="invoice-preview bg-light bordered" id="invoicePreview"
              style="min-height: 400px; position: relative;">
              <div class="top-header logosec" style="position:relative;width: 100%;">
                <div class="head-one">
                  <img id="logoPreview" src="<?php echo $image; ?>" alt=""
                    style="display: block;position: absolute; max-height: 80px; left: 0; top: 0;">
                  <div class="invoice-no" style="display: block; text-align: right;">
                    <p class="invoice" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Invoice No'); ?></span></p>
                    <p class="date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Date'); ?></span>
                    </p>
                    <p class="due_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('Due Date'); ?></span></p>
                    <p class="po_num" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('P.O. Number'); ?></span></p>
                    <p class="po_date" style="font-weight: 100;margin: 0;"><span style="font-weight: 600;"><?= __('P.O Date'); ?></span></p>
                  </div>
                </div>
              </div>
              <div class="address-sec bordered headersec" style="padding: .5rem;margin-top: 5px;">
                <div class="address-sec"
                  style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                  <div class="first_address">
                    <strong class="address1heading" style="font-weight: 600;"><?= __('Invoiced To'); ?>:</strong>
                    <address class="address1data">
                      <?= htmlspecialchars($invoiceData['party_address'] ?? '') ?>
                    </address>
                  </div>
                  <div class="second_address">
                    <strong class="address2heading" style="font-weight: 600;"><?= __('Bill To'); ?>:</strong>
                    <address class="address2data">
                      <?= htmlspecialchars($invoiceData['party_ship_adderss'] ?? '') ?>
                    </address>
                  </div>
                  <div class="third_address">
                    <strong class="address3heading" style="font-weight: 600;"><?= __('Company'); ?></strong>
                    <address class="address3data">
                      <?= htmlspecialchars($invoiceData['company_address'] ?? '') ?>
                    </address>
                  </div>
                </div>
              </div>
              <div class="items tablesec" style="margin-top: .5rem;">
                <div class="table-responsive">
                  <table class="table bordered mb-10">
                    <thead>
                      <tr class="">
                        <th scope="col" class="bordered col-1"><strong><?= __('S.No.') ?></strong></th>
                        <th scope="col" class="bordered img col-2"><strong><?= __('Image') ?></strong></th>
                        <th scope="col" class="bordered item col-2"><strong><?= __('Item Name') ?></strong></th>
                        <th scope="col" class="bordered des col-2"><strong><?= __('Description') ?></strong></th>
                        <th scope="col" class="bordered hsnsac col-2 text-end"><strong><?= __('HSN/SAC') ?></strong></th>
                        <th scope="col" class="bordered uom col-2"><strong><?= __('UOM') ?></strong></th>
                        <th scope="col" class="bordered tax col-2 text-end"><strong><?= __('Tax') ?></strong></th>
                        <th scope="col" class="bordered rate col-2 text-end"><strong><?= __('Rate') ?></strong></th>
                        <th scope="col" class="bordered qty col-1 text-end"><strong><?= __('QTY') ?></strong></th>
                        <th scope="col" class="bordered amount col-2 text-end"><strong><?= __('Amount') ?></strong></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="data">
                        <td class="col-1">1</td>
                        <td class="img col-2">
                          <img width="35px" src="<?= $image ?>" alt="">
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
                      <?php $i = 1;
                      foreach ($invoiceitemsData as $item): ?>
                        <tr class="data">
                          <td class="col-1"><?= $i ?></td>
                          <td class="img col-2">
                            <img width="35px" src="<?= $image ?>" alt="">
                          </td>
                          <td class="item col-3 text-1">
                            <?= mb_strimwidth($item['item_name'], 0, 20, '...') ?>
                          </td>
                          <td class="des col-3 text-1">
                            <?= $item['description'] ?>
                          </td>
                          <td class="hsnsac col-3 text-end">
                            <?= $hsnSacCodesData['code'] ?>
                          </td>
                          <td class="uom col-3 text-1">
                            <?= $item['purchase_uom'] ?>
                          </td>
                          <td class="tax col-3 text-1 text-end" data-original="23.00">23.00</td>
                          <td class="rate col-2 text-end">
                            <?= number_format($item['item_price'], 2, '.', '') ?>
                          </td>
                          <td class="qty col-1 text-end">
                            <?= number_format($item['item_quantity'], 2, '.', '') ?>
                          </td>
                          <td class="amount col-2 text-end">
                            <?= number_format($item['item_amount'], 2, '.', '') ?>
                          </td>
                        </tr>
                        <?php $i++; endforeach; ?>
                      <tr class="tax-summary">
                        <td class="bordered col-1"></td>
                        <td class="bordered img col-2"></td>
                        <td class="bordered item col-3 text-end"> <br> <br><span style="font-weight: 700;"><?= __('Total'); ?></span>
                        </td>
                        <td class="bordered des col-3 text-1"></td>
                        <td class="bordered hsnsac col-3 text-end">@xx% <br> @xx%</td>
                        <td class="bordered uom col-3 text-1"></td>
                        <td class="bordered tax col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                        <td class="bordered rate col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                        <td class="bordered qty col-1 text-end"></td>
                        <td class="bordered amount col-2 text-end">xxxx <br> xxxx <br><span
                            style="font-weight: 700;">xxxx</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="page_footer bordered footersec" style="margin-top: .5rem;">
                <div style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                  <div class="first_col signatureqr">
                    <p class="totalinwords"><?= __('Total : Twenty Five Thousand Seven Hundred Seventy Eight'); ?></p>
                    <div style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                      <div class="signature">
                        <h6 style="text-decoration: underline;margin-bottom: 5px;"><?= __('Autherized Signature'); ?></h6>
                        <img width="180px"
                          src="https://onlinepngtools.com/images/examples-onlinepngtools/george-walker-bush-signature.png"
                          alt="https://onlinepngtools.com//examples-onlinepngtools/george-walker-bush-signature.png">
                      </div>
                      <div class="qr">
                        <img width="120px" src="https://pngimg.com/uploads/qr_code/small/qr_code_PNG33.png"
                          alt="https://pngimg.com/uploads/qr_code/small/qr_code_PNG33.png">
                      </div>
                    </div>
                  </div>
                  <div class="invoicesummary"
                    style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                    <div class="sec_col">
                      <h6 class="totalbeforetax heading1" style="text-align: right;"><?= __('Total Before Tax'); ?></h6>
                      <h6 class="discount heading2" style="text-align: right;"><?= __('Discount'); ?></h6>
                      <h6 class="totaltaxamount heading3" style="text-align: right;"><?= __('Total Tax Amount'); ?></h6>
                      <h6 class="roundedoff heading4" style="text-align: right;"><?= __('Rounded Off'); ?></h6>
                      <h6 class="totalamount heading5" style="text-align: right;"><?= __('Total Amount'); ?></h6>
                      <h6 class="totaldue heading6" style="text-align: right;"><?= __('Total Due'); ?></h6>
                    </div>
                    <div class="third_col">
                      <h6 class="totalbeforetax value1" style="text-align: right;">xxxx</h6>
                      <h6 class="discount value1" style="text-align: right;">xxxx</h6>
                      <h6 class="totaltaxamount value3" style="text-align: right;">xxxx</h6>
                      <h6 class="roundedoff value4" style="text-align: right;">xxxx</h6>
                      <h6 class="totalamount value5" style="text-align: right;">xxxx</h6>
                      <h6 class="totaldue value6" style="text-align: right;">xxxx</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-2">
              <button name="submitinvoice" type="submit" class="btn btn-primary mt-3 w-100" disabled><?= __('Save Changes') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const invoiceData = <?= json_encode($invoiceData) ?>;
</script>
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

  const loadSettings = () => {
    const savedSettings = localStorage.getItem('invoiceSettings');
    if (savedSettings) {
      const settings = JSON.parse(savedSettings);

      fontFamilySelect.value = settings.fontFamily || 'Arial';
      marginInput.value = settings.margin || '0';
      paddingInput.value = settings.padding || '20';
      heightInput.value = settings.height || '400';
      borderStyleSelect.value = settings.borderStyle || 'none';
      borderColorInput.value = settings.borderColor || '#000000';
      borderWidthInput.value = settings.borderWidth || '1';
      showLogoCheckbox.checked = settings.showLogo !== false;
      logoPositionSelect.value = settings.logoPosition || 'left';
      logoSizeSelect.value = settings.logoSize || 'medium';
      customLogoSizeInput.value = settings.customLogoSize || '';
      showAddressCheckbox.checked = settings.showAddress !== false;
      addressSizeSelect.value = settings.addressTextSize || 'medium';
      showFirstAddressCheckbox.checked = settings.showFirstAddress !== false;
      showSecondAddressCheckbox.checked = settings.showSecondAddress !== false;
      showThirdAddressCheckbox.checked = settings.showThirdAddress !== false;

      updatePreview();
    }
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
    const topHeader = document.querySelector('.top-header');

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
        invoiceElement.style.display = 'none';
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
    loadSettings();
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

  const loadAddressSettings = () => {
    const savedSettings = localStorage.getItem('addressSettings');
    if (savedSettings) {
      const settings = JSON.parse(savedSettings);

      document.getElementById('showFirstAddress').checked = settings.showFirstAddress !== false;
      document.getElementById('address1Select').value = settings.firstAddressValue || 'party_address';
      document.getElementById('inputheading1').value = settings.firstAddressHeading || 'Invoiced To';
      document.getElementById('address1position').value = settings.firstAddressPosition || 'left';

      document.getElementById('showSecondAddress').checked = settings.showSecondAddress !== false;
      document.getElementById('address2Select').value = settings.secondAddressValue || 'party_ship_adderss';
      document.getElementById('inputheading2').value = settings.secondAddressHeading || 'Bill To';
      document.getElementById('address2position').value = settings.secondAddressPosition || 'center';

      document.getElementById('showThirdAddress').checked = settings.showThirdAddress !== false;
      document.getElementById('address3Select').value = settings.thirdAddressValue || 'company_address';
      document.getElementById('inputheading3').value = settings.thirdAddressHeading || 'Company';
      document.getElementById('address3position').value = settings.thirdAddressPosition || 'right';

      updateAddressPreview();
    }
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
    document.querySelector('.first_address .address1data').textContent = showFirst
      ? invoiceData[document.getElementById('address1Select').value] || 'N/A'
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
    document.querySelector('.second_address .address2data').textContent = showSecond
      ? invoiceData[document.getElementById('address2Select').value] || 'N/A'
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
    document.querySelector('.third_address .address3data').textContent = showThird
      ? invoiceData[document.getElementById('address3Select').value] || 'N/A'
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

  document.addEventListener('DOMContentLoaded', () => {
    loadAddressSettings();
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

  const loadTopHeaderSettings = () => {
    const savedSettings = localStorage.getItem('topHeaderSettings');
    if (savedSettings) {
      const settings = JSON.parse(savedSettings);

      updateElementState('showinvoiceno', 'invoicenoSelect', settings.showInvoiceNo, settings.invoiceNoValue);
      updateElementState('showdate', 'invoicedateSelect', settings.showDate, settings.dateValue);
      updateElementState('showduedate', 'invoiceduedateSelect', settings.showDueDate, settings.dueDateValue);
      updateElementState('showpo_num', 'invoiceponumSelect', settings.showPoNum, settings.poNumValue);
      updateElementState('showpo_date', 'invoicepodateSelect', settings.showPoDate, settings.poDateValue);

      updateTopHeaderPreview();
    }
  };

  const updateElementState = (checkboxId, selectId, show, value) => {
    const checkbox = document.getElementById(checkboxId);
    const select = document.getElementById(selectId);
    checkbox.checked = show !== false;
    select.value = value || select.options[0].value;
    select.style.display = checkbox.checked ? 'block' : 'none';
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

      if (checkbox.checked) {
        preview.textContent = `${label}: ${invoiceData[select.value] || 'N/A'}`;
        preview.parentElement.style.display = 'block';
        select.style.display = 'block';
      } else {
        preview.parentElement.style.display = 'none';
        select.style.display = 'none';
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
    loadTopHeaderSettings();
    setupEventListeners();
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

  const loadTableSettings = () => {
    const savedSettings = localStorage.getItem('tableSettings');
    if (savedSettings) {
      const settings = JSON.parse(savedSettings);

      const showTableCheckbox = document.getElementById('showTable');
      showTableCheckbox.checked = settings.showTable;
      showTableCheckbox.dispatchEvent(new Event('change'));

      Object.keys(settings.showColumns).forEach((col) => {
        const checkbox = document.getElementById(`show${col}col`);
        if (checkbox) {
          checkbox.checked = settings.showColumns[col];
          checkbox.dispatchEvent(new Event('change'));
        }
      });
    }
  };

  document.getElementById('showTable').addEventListener('change', toggleTableVisibility);
  toggleColumnVisibility();

  document.querySelectorAll('#showTable, .form-check-input').forEach((checkbox) => {
    checkbox.addEventListener('change', saveTableSettings);
  });

  document.addEventListener('DOMContentLoaded', () => {
    loadTableSettings();
    toggleTableVisibility();
    toggleColumnVisibility();
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

    Object.keys(selectElements).forEach((key) => {
      const checkbox = document.getElementById(key);
      const select = document.getElementById(selectElements[key].selectId);

      if (checkbox && select) {
        checkbox.addEventListener('change', () => {
          select.style.display = checkbox.checked ? 'block' : 'none';
        });

        select.addEventListener('change', () => {
          saveSelectHeadersToLocalStorage();
        });
      }
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

    const defaultHeaders = {
      showimagecol: 'Image',
      showitemcol: 'Item',
      showdescol: 'Description',
      showhsnsaccol: 'HSN/SAC',
      showuomcol: 'UOM',
      showtaxcol: 'TAX',
      showratecol: 'Rate',
      showqtycol: 'Quantity',
      showamountcol: 'Amount',
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

    const loadHeadersFromLocalStorage = () => {
      const savedHeaders = JSON.parse(localStorage.getItem('tableHeaders')) || defaultHeaders;
      Object.keys(savedHeaders).forEach((key) => {
        const input = document.getElementById(columnInputs[key].inputId);
        const columnClass = columnInputs[key].columnClass;
        if (input) {
          input.value = savedHeaders[key] || defaultHeaders[key];
          theadCells.forEach((cell) => {
            if (cell.classList.contains(columnClass)) {
              cell.querySelector('strong').textContent = savedHeaders[key];
            }
          });
        }
      });
    };

    Object.keys(columnInputs).forEach((key) => {
      const checkbox = document.getElementById(key);
      const input = document.getElementById(columnInputs[key].inputId);
      const columnClass = columnInputs[key].columnClass;

      if (checkbox && input) {
        checkbox.addEventListener('change', () => {
          input.style.display = checkbox.checked ? 'block' : 'none';
          document.querySelectorAll(`.${columnClass}`).forEach((col) => {
            col.style.display = checkbox.checked ? '' : 'none';
          });
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

    loadHeadersFromLocalStorage();
  };

  manageDynamicColumns();

  const applyTableAppearance = () => {
    const table = document.querySelector('.table');
    if (!table) return; // Ensure the table exists
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

  const loadTableAppearance = () => {
    const savedSettings = localStorage.getItem('tableAppearance');
    if (savedSettings) {
      const settings = JSON.parse(savedSettings);
      document.getElementById('tableHeaderbgColor').value = settings.headerBgColor || '#f8f9fa';
      document.getElementById('tableHeaderColor').value = settings.headerTextColor || '#ffffff';
      document.getElementById('tableBodybgColor').value = settings.bodyBgColor || '#ffffff';
      document.getElementById('tableBodyColor').value = settings.bodyTextColor || '#ffffff';
      document.getElementById('tableFooterbgColor').value = settings.footerBgColor || '#f8f9fa';
      document.getElementById('tableFooterColor').value = settings.footerTextColor || '#ffffff';
      document.getElementById('rowHeight').value = settings.rowHeight || '';
      document.getElementById('headingHeight').value = settings.headingHeight || '';
      document.getElementById('fontSize').value = settings.fontSize || '';
      applyTableAppearance();
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    loadTableAppearance();

    const inputs = document.querySelectorAll('#tableAppearance input');
    inputs.forEach(input => input.addEventListener('input', applyTableAppearance));
  });

  document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody .data');
    const input = document.getElementById('itemsInput');
    const tableItemsKey = 'tableItemsVisibility';

    const updateRowVisibility = (count) => {
      rows.forEach((row, index) => {
        row.style.display = index < count ? '' : 'none';
      });
    };

    const saveRowCount = (count) => {
      localStorage.setItem(tableItemsKey, count);
    };

    const loadRowCount = () => {
      const savedCount = localStorage.getItem(tableItemsKey);
      return savedCount ? parseInt(savedCount, 10) : rows.length;
    };

    const initialCount = loadRowCount();
    input.value = initialCount;
    updateRowVisibility(initialCount);

    input.addEventListener('input', () => {
      const count = parseInt(input.value, 10) || rows.length;
      updateRowVisibility(count);
      saveRowCount(count);
    });
  });

  // footer

  document.addEventListener('DOMContentLoaded', () => {
    const footerKey = 'footerSettings';
    const footer = document.querySelector('.page_footer');
    const showFooterCheckbox = document.getElementById('showpagefooter');

    const fields = {
      totalbeforetax: { elements: document.querySelectorAll('.totalbeforetax'), headingInput: 'heading1', valueSelect: 'value1', checkbox: 'showtotalbeforetax' },
      discount: { elements: document.querySelectorAll('.discount'), headingInput: 'heading2', valueSelect: 'value2', checkbox: 'showdiscount' },
      totaltaxamount: { elements: document.querySelectorAll('.totaltaxamount'), headingInput: 'heading3', valueSelect: 'value3', checkbox: 'showtotaltaxamount' },
      roundedoff: { elements: document.querySelectorAll('.roundedoff'), headingInput: 'heading4', valueSelect: 'value4', checkbox: 'showroundedoff' },
      totalamount: { elements: document.querySelectorAll('.totalamount'), headingInput: 'heading5', valueSelect: 'value5', checkbox: 'showtotalamount' },
      totaldue: { elements: document.querySelectorAll('.totaldue'), headingInput: 'heading6', valueSelect: 'value6', checkbox: 'showtotaldue' },
    };

    const positions = {
      signatureqr: { element: document.querySelector('.signatureqr'), positionSelect: 'signatureqr1position' },
      invoicesummary: { element: document.querySelector('.invoicesummary'), positionSelect: 'invoicesummary2position' },
    };

    const defaultSettings = {
      showFooter: true,
      showFields: Object.keys(fields).reduce((acc, key) => {
        acc[key] = { visible: true, heading: '', value: '' };
        return acc;
      }, {}),
      positions: Object.keys(positions).reduce((acc, key) => {
        acc[key] = 'left';
        return acc;
      }, {}),
    };

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

    const loadFooterSettings = () => {
      const savedSettings = JSON.parse(localStorage.getItem(footerKey)) || defaultSettings;

      showFooterCheckbox.checked = savedSettings.showFooter;
      footer.style.display = savedSettings.showFooter ? 'block' : 'none';

      Object.keys(fields).forEach((key) => {
        const field = fields[key];
        const settings = savedSettings.showFields[key];

        document.getElementById(field.checkbox).checked = settings.visible;

        let headingElement = document.getElementById(field.headingInput);
        headingElement.value = headingElement.value || settings.heading;

        document.getElementById(field.valueSelect).value = settings.value;

        updatePreview(key);
        toggleVisibility(key, settings.visible);
      });

      Object.keys(positions).forEach((key) => {
        const position = positions[key];
        document.getElementById(position.positionSelect).value = savedSettings.positions[key];
        updatePosition(key);
      });

      if (!localStorage.getItem(footerKey)) {
        saveFooterSettings();
      }
    };

    const updatePreview = (key) => {
      const field = fields[key];
      const headingValue = document.getElementById(field.headingInput).value;

      field.elements.forEach((el) => {
        const headingClass = Array.from(el.classList).find((cls) => cls.startsWith('heading'));
        if (headingClass) el.textContent = headingValue;
      });

      saveFooterSettings();
    };

    const toggleVisibility = (key, isVisible) => {
      const field = fields[key];
      field.elements.forEach((el) => {
        el.style.display = isVisible ? 'block' : 'none';
      });
      document.getElementById(field.headingInput).style.display = isVisible ? 'block' : 'none';
      document.getElementById(field.valueSelect).style.display = isVisible ? 'block' : 'none';
    };

    const updatePosition = (key) => {
      const position = positions[key];
      const positionValue = document.getElementById(position.positionSelect).value;

      position.element.style.order = positionValue === 'left' ? '0' : '1';
      saveFooterSettings();
    };

    showFooterCheckbox.addEventListener('change', () => {
      footer.style.display = showFooterCheckbox.checked ? 'block' : 'none';
      saveFooterSettings();
    });

    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      document.getElementById(field.headingInput).addEventListener('input', () => updatePreview(key));
      document.getElementById(field.valueSelect).addEventListener('input', () => updatePreview(key));
      document.getElementById(field.checkbox).addEventListener('change', (e) => {
        toggleVisibility(key, e.target.checked);
        saveFooterSettings();
      });
    });

    Object.keys(positions).forEach((key) => {
      const position = positions[key];
      document.getElementById(position.positionSelect).addEventListener('change', () => updatePosition(key));
    });

    loadFooterSettings();
  });

  document.addEventListener('DOMContentLoaded', () => {
    const templateNameInput = document.getElementById('template_name');
    const saveButton = document.querySelector('button[name="submitinvoice"]');

    const sectionKeys = {
      logosecposition: "logosec",
      headersecposition: "headersec",
      tablesecposition: "tablesec",
      footersecposition: "footersec",
    };

    function updateSelectDropdowns() {
      Object.keys(sectionKeys).forEach((key) => {
        const storedData = JSON.parse(localStorage.getItem(key));
        if (storedData && storedData.order) {
          const selectElement = document.getElementById(key);
          if (selectElement) {
            selectElement.value = storedData.order;
          }
        }
      });
    }

    function applySectionOrder() {
      const container = document.getElementById('invoicePreview');
      const sections = [];

      Object.keys(sectionKeys).forEach((key) => {
        const storedData = JSON.parse(localStorage.getItem(key));
        const sectionId = sectionKeys[key];
        if (storedData && storedData.order) {
          const sectionElement = document.querySelector(`.${sectionId}`);
          if (sectionElement) {
            sections.push({ order: storedData.order, element: sectionElement });
          }
        }
      });

      sections.sort((a, b) => a.order - b.order);

      sections.forEach(({ element }) => {
        container.appendChild(element);
      });
    }

    function setupSectionListeners() {
      Object.keys(sectionKeys).forEach((key) => {
        const selectElement = document.getElementById(key);
        selectElement.addEventListener('change', () => {
          const selectedValue = parseInt(selectElement.value, 10);
          localStorage.setItem(key, JSON.stringify({ order: selectedValue }));
          applySectionOrder();
        });
      });
    }

    const savedTemplateName = localStorage.getItem('templateName');
    if (savedTemplateName) {
      templateNameInput.value = savedTemplateName;
      saveButton.disabled = false;
    } else {
      saveButton.disabled = true;
    }

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

    updateSelectDropdowns();
    applySectionOrder();
    setupSectionListeners();
  });

  const createHiddenFields = () => {
    const savedAddressSettings = localStorage.getItem("addressSettings");
    const savedFooterSettings = localStorage.getItem("footerSettings");
    const savedinvoiceData = localStorage.getItem("invoiceData");
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
      const invoiceData = JSON.parse(savedinvoiceData);

      const invoiceOptions = {};

      if (invoiceData.templateName) {
        invoiceOptions.templateName = invoiceData.templateName;
      }

      if (invoiceData.logosecposition) {
        invoiceOptions.logosecposition = invoiceData.logosecposition;
      }

      if (invoiceData.headersecposition) {
        invoiceOptions.headersecposition = invoiceData.headersecposition;
      }

      if (invoiceData.tablesecposition) {
        invoiceOptions.tablesecposition = invoiceData.tablesecposition;
      }

      if (invoiceData.footersecposition) {
        invoiceOptions.footersecposition = invoiceData.footersecposition;
      }

      const invoiceOptionsField = document.createElement("input");
      invoiceOptionsField.type = "hidden";
      invoiceOptionsField.name = "invoice_options";
      invoiceOptionsField.value = JSON.stringify(invoiceOptions);
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
  };

  document.querySelector("form").addEventListener("submit", (e) => {
    createHiddenFields();
  });
</script>
