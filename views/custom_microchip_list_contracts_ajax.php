<?php
$is_pdf = ($_POST['pdfexport'] == "Y");
if($_POST['filterajax'] == "Y" || $is_pdf){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $current_page = $_POST['current_page'];
    $post = $_POST;
    $post['admin_type'] = $_SESSION['admin_type'];
    $post['user_id'] = $_SESSION['userid'];
    $post['company_id'] = $_SESSION['company_id'];
    $post['api_type'] = 'listpage';
    $url = API."?mode=getcontractslistpost&method=post";
    $api = file_post_contents($url, $post, 'update');
    $result = json_decode($api,1);
    $payment_terms = $result['payment_terms'];
    $locations= $result['locations'];
    $agreement_type= $result['agreement_type'];
    $departments= $result['departments'];
    $contract_types= $result['contract_types'];
    $contract_services= $result['contract_services'];
    $vendors = $result['vendors'];

}
$total_pages = $result['total_pages'];
$stausresult = ViewStatusData();
$_SESSION['download_data'] = array();
$contracttodaydate = new DateTime();
$contracts = $result['contracts'];

if ($is_pdf) {
    $company_name = $_SESSION['config']['name'] ?? '';
    $generated_at = date('d-m-Y H:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Microchip Contracts - InfiSuite</title>
    <meta charset="UTF-8">
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; padding: 10px; color: #333; }
        h3 { margin: 0 0 4px 0; font-size: 14px; }
        .meta { margin: 0 0 10px 0; font-size: 9px; color: #666; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; color: #fff; font-size: 8px; }
        .badge-amc { background: #17a2b8; }
        .badge-standard { background: #6c757d; }
        .badge-active { background: #17a2b8; }
        .badge-expired { background: #dc3545; }
        .empty-msg { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <h3>Contracts List</h3>
    <?php if ($company_name != "") { ?>
        <p class="meta">Company: <?php echo htmlspecialchars($company_name); ?> | Generated: <?php echo $generated_at; ?></p>
    <?php } else { ?>
        <p class="meta">Generated: <?php echo $generated_at; ?></p>
    <?php } ?>
<?php }

if(count((array)($contracts)) != 0){  ?>
<table class="<?php echo $is_pdf ? '' : 'table table-hover m-b-0 register_vendor_filter f-13'; ?>">
    <thead>
        <tr>
            <th scope="col" class="p-2">#</th>
            <th scope="col" class="p-2">Contract No.</th>
            <th scope="col" class="p-2">Contract Name</th>
            <th scope="col" class="p-2">Vendor</th>
            <th scope="col" class="p-2">Start Date</th>
            <th scope="col" class="p-2">End Date</th>

            <th scope="col" class="p-2">Services</th>
            <th scope="col" class="p-2">Location</th>
            <th scope="col" class="p-2">Department</th>

            <th scope="col" class="p-2">Payment Terms</th>
            <th scope="col" class="p-2">Status</th>
            <th scope="col" class="p-2">Approval Status</th>
        </tr>
    </thead>
    <tbody >
        <?php $sq = 1; foreach ($contracts as $contra){

             $locationsid = json_decode($contra['locations'] ,1);
             $locationnames = "";
            foreach($locationsid as $locid){ $locationnames .= $locations[$locid]['location_name'].", ";
}

            $servicesnames = "";
            $contract_services_id = json_decode($contra['contract_services_id'] ,1);
            foreach($contract_services_id as $servid){ $servicesnames .= $contract_services[$servid]['service_name'].", ";
}
            if (!$is_pdf) {
             $cssdata[] = array("Sr. No."=>$sq, "Contract No"=>emptytextdashShow($contra['transaction_no']),
                "Contract Name"=>emptytextdashShow($contra['contract_name']),
                "Vendor"=>emptytextdashShow($vendors[$contra['party_id']]['company']),
                "Start Date"=>fromDate($contra['start_date']),
                "End Date"=>fromDate($contra['end_date']),
                "Agreements Type"=>emptytextdashShow($agreement_type[$contra['typeofaggrements']]['genc_detail_name']),
                "Location"=>emptytextdashShow($locationnames),
                "Department"=>emptytextdashShow($departments[$contra['department_id']]['department_name']),
                "Contract Category"=>emptytextdashShow($contract_types[$contra['type_of_contract_id']]['genc_detail_name']),
                "Payment Terms"=>emptytextdashShow($payment_terms[$contra['contract_payment_terms']]['payment_term_name']),
                "Status"=> ($contra['end_date'] && new DateTime($contra['end_date']) < $contracttodaydate)?"Expired":"Active");
            }

         ?>
        <tr>
            <td class="p-2"><?php echo $sq; ?></td>
            <td class="p-2">
                <?php if ($is_pdf) { ?>
                    <?php echo htmlspecialchars($contra['transaction_no'] != "" ? $contra['transaction_no'] : "-"); ?>
                <?php } else { ?>
                <a class="text-primary" href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo deocdeId("id:".$contra['id']);?>"><i class="ti-pencil-alt"></i> <?php echo $contra['transaction_no']!=""?$contra['transaction_no']:"-"; ?></a>
                <?php } ?>
            </td>
            <td class="p-2"><?php echo $contra['contract_name']!=""?$contra['contract_name']:"-"; ?></td>
            <td class="p-2"><?php echo $vendors[$contra['party_id']]['company']!=""?$vendors[$contra['party_id']]['company']:"-"; ?></td>
            <td class="p-2"><?php echo fromDate($contra['start_date']); ?></td>
            <td class="p-2"><?php echo fromDate($contra['end_date']); ?></td>

            <td class="p-2">
                <?php  echo $servicesnames;  ?>
            </td>
            <td class="p-2">
                <?php  echo $locationnames;  ?>
            </td>
            <td class="p-2"><?php echo $departments[$contra['department_id']]['department_name']!=""?$departments[$contra['department_id']]['department_name']:"-"; ?></td>

            <td class="p-2"><?php echo $payment_terms[$contra['contract_payment_terms']]['payment_term_name']!=""?$payment_terms[$contra['contract_payment_terms']]['payment_term_name']:"-"; ?></td>
            <td class="p-2">
                <?php
                if ($contra['end_date'] && new DateTime($contra['end_date']) < $contracttodaydate) {
                   if ($is_pdf) {
                        echo '<span class="badge badge-expired">Expired</span>';
                    } else {
                        echo '<span class="form-label label label-danger">Expired</span>';
                    }
                }else{
                    if ($is_pdf) {
                        echo '<span class="badge badge-active">Active</span>';
                    } else {
                        echo '<span class="form-label label label-info">Active</span>';
                    }
                }
                ?>
            </td>
            <td class="p-2">
                <?php if ($is_pdf) { ?>
                    <span class="badge" style="color: <?php echo $stausresult[$contra['current_status']]['color']; ?>; background: <?php echo $stausresult[$contra['current_status']]['background_color']; ?>;"><?php echo htmlspecialchars($stausresult[$contra['current_status']]['name']); ?></span>
                <?php } else { ?>
                <span class="form-label label label-md label-warning float-right" style="color: <?php echo $stausresult[$contra['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$contra['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$contra['current_status']]['background_color']; ?>;"><?php echo $stausresult[$contra['current_status']]['name']; ?> </span>
                <?php } ?>
            </td>
        </tr>
        <?php $sq++;
}
        if (!$is_pdf) {
            $_SESSION['download_data'] = $cssdata;
        }
        ?>
    </tbody>
</table>
<?php if (!$is_pdf) { ?>
<div class="mt-5  custom-scroll height-75" >
    <nav aria-label="Page navigation example p-2" >
        <ul class="pagination">
          <li class="page-item <?php echo ($current_page =="1" || $current_page =="")?"disabled":""; ?>">
              <a class="page-link dynamicContractSelectPage"  data-current_status = "" data-current_page="<?php echo $current_page-1; ?>" href="#!" tabindex="-1" >Previous</a>
          </li>
        <?php
          for ($i = 1; $i <= $total_pages; $i++){
              if ($i == $current_page){ ?>
                  <li class="page-item active"><a class="page-link active dynamicContractSelectPage"  data-current_status = "" data-current_page="<?php echo $i; ?>"   href="#!" ><?php echo $i; ?></a></li>
            <?php }else{ ?>
                  <li class="page-item"><a class="page-link dynamicContractSelectPage"  data-current_status = "" data-current_page="<?php echo $i; ?>"  href="#!"><?php echo $i; ?></a></li>
            <?php } ?>
            <?php } ?>

            <li class="page-item <?php echo ($total_pages <= $current_page)?"disabled":""; ?>">
                <a class="page-link dynamicContractSelectPage"  data-current_status = "" data-current_page="<?php echo $current_page+1; ?>"  href="#!" tabindex="-1">Next</a>
            </li>
        </ul>
    </nav>
</div>
<?php } ?>

<?php  }else{
    if ($is_pdf) { ?>
    <p class="empty-msg">No Contracts</p>
<?php } else { ?>
    <div class="card-px text-center py-20 mt-2">
        <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Contracts </p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
        </div>
    </div>
<?php }
}
if ($is_pdf) { ?>
</body>
</html>
<?php } ?>
