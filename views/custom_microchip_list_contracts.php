<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['table']= 1;
$limit_select = 20;
$url = API . "?mode=getcontractslist&method=get&api_type=listpage&limit_select=".$limit_select;
$response = callAPI($url);
$result = json_decode($response, true);
$payment_terms = $result['payment_terms'];
$locations= $result['locations'];
$agreement_type= $result['agreement_type'];
$departments= $result['departments'];
$contract_types= $result['contract_types'];
$contract_services= $result['contract_services'];
$vendors = $result['vendors'];
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
<?php
}
?>
<script type="text/javascript" src="../assets/js/custom_microchip_list_contracts.js"></script>
<div class="card">
    <div class="card-header">
        <h5>Contracts</h5>
        <div class="card-header-right table-card-header">
             <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                    data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Contract List" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect" href="#!" id="downloadContractsPDF">PDF</a>
                </div>
            </div>
            <a href="index.php?option=COM_INSERT_CONTRACTS" class="btn btn-success">
              <i class="icofont icofont-plus icon-white "></i>
              Contract            </a>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>Back
            </a>
        </div>
    </div>

    <div class="card-body">
        <form id="formcontractlistpage" method="post">
            <input type="hidden" name="current_page" value="1" id="hide_current_page">
            <div class="row">
                <div class="col-lg-2">
                     <span class="form-label d-block d-block">Contract No/Name</span>
                     <input aria-label="Transaction No And Name" type="text" name="transaction_no_and_name" value="" class="form-control" >
                </div>
                <div class="col-lg-2">
                     <span class="form-label d-block d-block">Vendor</span>
                     <select aria-label="Party" name="party_id" class="form-select js-example-basic-single" >
                         <option value="">All Vendor</option>
                         <?php
                             foreach ($vendors as $ven){ ?>
                                <option value="<?= $ven['id'] ?>"><?= htmlspecialchars($ven['company']) ?> </option>
                        <?php } ?>
                     </select>
                </div>
                <div class="col-lg-2">
                    <span class="form-label d-block d-block">Services</span>
                     <select aria-label="Contract Services" name="contract_services_id" class="form-select js-example-basic-single" >
                         <option value="">All Services</option>
                         <?php
                             foreach ($contract_services as $conserv){ ?>
                                <option value="<?= $conserv['id'] ?>" ><?= htmlspecialchars($conserv['service_name']) ?> </option>
                        <?php } ?>
                     </select>

                </div>

                <div class="col-lg-2">
                    <span class="form-label d-block d-block">Location</span>
                    <select aria-label="Locations" name="locations" class="form-select js-example-basic-single" >
                        <option  value="">All Location</option>
                         <?php
                             foreach ($locations as $loc){ ?>
                                <option value="<?= $loc['id'] ?>" ><?= htmlspecialchars($loc['location_name']) ?> </option>
                        <?php } ?>
                     </select>
                </div>
                <div class="col-lg-2">
                    <span class="form-label d-block d-block">Department</span>
                    <select aria-label="Department" name="department_id" class="form-select js-example-basic-single" >
                        <option value="">All Department</option>
                         <?php
                             foreach ($departments as $dep){ ?>
                                <option value="<?= $dep['id'] ?>" ><?= htmlspecialchars($dep['department_name']) ?> </option>
                        <?php } ?>
                     </select>
                </div>

                <div class="col-lg-2">
                    <span class="form-label d-block d-block">Payment Terms</span>
                    <select aria-label="Contract Payment Terms" name="contract_payment_terms" class="form-select js-example-basic-single" >
                        <option value="">All Payment Terms</option>
                         <?php
                             foreach ($payment_terms as $payt){ ?>
                                <option value="<?= $payt['id'] ?>"><?= htmlspecialchars($payt['payment_term_name']) ?> </option>
                        <?php } ?>
                     </select>
                </div>
                <div class="col-lg-2">
                    <span class="form-label d-block d-block">Records</span>
                     <select aria-label="Limit Select" name="limit_select" class="form-select js-example-basic-single" >
                         <?php
                             for($i = 20; $i <= 1000; $i += 20) { ?>
                                <option value="<?php echo $i; ?>" ><?php echo htmlspecialchars($i); ?> </option>
                        <?php } ?>
                     </select>

                </div>
                <div class="col-lg-2">
                    <button type="button" name="applyform" value="apply" class=" btn btn-info waves-effect waves-light input-group-text m-t-30 submitfilervtn">Apply</button>
                </div>

            </div>
        </form>
    </div>
    <div class="card-body table-responsive" id="dyanmictableshow">

        <?php include_once 'custom_microchip_list_contracts_ajax.php'; ?>
    </div>
</div>
