<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_ids'])) {
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];

    $url = CRMAPI . "?mode=createsalesinvoice&method=post"; // Customize as needed
    $response = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($response, 1);
    ?>
     <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }
        }
    </script>
    <?php
}
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

$sales = array();
if ($_GET['Rid'] != "") {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $vurl = CRMAPI . "?mode=getsalesinvoicedynamicdata&method=get&rid=".$rid;
    $che = callAPI($vurl);
    $return = json_decode($che, 1);
    $sales = $return['sales'];
}

?>
<script>
           function goToAnotherPageAndPrint(inv_id,transaction_table){
       $.ajax({
            url: "views/custom_qo_print.php",
            method: "GET",
            data:'rid='+inv_id+'&print=Y&table='+transaction_table,
            success: function (data) {
                var popup = window.open("", "_blank", "width=600,height=400");
                popup.document.write(data);
                popup.document.close();
                popup.print();
            },
            error: function () {
                alert(<?php echo json_encode(__('Error fetching data'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
            }
        });
    }
    function DownloadPDF(inv_id,transaction_table){
        $.ajax({
            url: "views/custom_qo_print.php",
            method: "GET",
            data:'rid='+inv_id+'&print=Y&table='+transaction_table,
            success: function (htmlContent){
                $.ajax({
                    url: 'convert_html_to_pdf.php',
                    type: 'POST',
                    data: { html_content: htmlContent,title:transaction_table },
                    success: function(pdfdetail) {
                      var result = JSON.parse(pdfdetail);
                        var link = document.createElement('a');
                          link.href = result.pdf_url;
                          link.download =result.pdf_name;
                          link.style.display = 'none';
                          document.body.appendChild(link);
                          link.click();
                          document.body.removeChild(link);
                          $.ajax({
                        url: 'convert_html_to_pdf.php',
                        type: 'POST',
                        data: 'removepdf='+result.pdf_name,
                        success: function(res) {
                          console.log(res);
                        }
                    });
                    }
                });
            },
            error: function () {
                alert(<?php echo json_encode(__('Error fetching data'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
            }
        });
    }

</script>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h5><?= __('Sales Invoice Excel Item List'); ?></h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                <?= __('Back'); ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="salesForm" action="" method="post">
            <input type="hidden" name="sales_invoice_data_parent_id" value="<?php echo $rid; ?>">
            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <tr class="f-14" style="background-color: #eeeeee;">
                        <th scope="col">#</th>
                        <th scope="col"><?= __('Sr.No'); ?></th>
                        <th scope="col"><?= __('Party Name'); ?></th>
                        <th scope="col"><?= __('Salesmen'); ?></th>
                        <th scope="col"><?= __('Mobile'); ?></th>
                        <th scope="col"><?= __('Expiry Date'); ?></th>
                        <th scope="col"><?= __('Bill No.'); ?></th>
                        <th scope="col"><?= __('Items And Count'); ?></th>
                        <th scope="col"><?= __('Action'); ?></th>
                    </tr>
                    <?php
                    if (count((array)$sales) > 0) {
                        $groupedSales = [];

                        foreach ($sales as $row) {
                            $billNo = $row['bill_no'];

                            if (!isset($groupedSales[$billNo])) {
                                $groupedSales[$billNo] = [
                                    'id' => $row['id'],
                                    'user_name' => $row['user_name'],
                                    'party_name' => $row['party_name'],
                                    'salesmen_name' => $row['user_name'],
                                    'mobile_no' => $row['mobile_no'],
                                    'expiry_date' => $row['expiry_date'],
                                    'bill_no' => $row['bill_no'],
                                    'plan_names' => [],
                                    'party_exists' => $row['party_exists'],
                                    'transaction_exists' => $row['transaction_exists'],
                                    'sales_invoice_id' => $row['sales_invoice_id'],
                                    'sales_invoice_transaction_number' => $row['sales_invoice_transaction_number'],
                                    'plan_exists' => [],
                                    'salesman_exists' => $row['salesman_exists'],
                                    'row_count' => 0,
                                ];
                            }

                            $groupedSales[$billNo]['plan_names'][] = $row['plan_name'];
                            $groupedSales[$billNo]['plan_exists'][] = $row['plan_exists'];
                            $groupedSales[$billNo]['row_count']++;
                        }

                        $i = 1;

                        foreach ($groupedSales as $bill){
                            $checkboxDisabled ='disabled';
                            if($bill['transaction_exists'] == 'No'){
                                $checkboxDisabled  = '';
                            }
                            $partyTag = $bill['party_exists'] === 'No' ? "<a href='index.php?option=COM_INSERT_CUSTOMERS&COMPNAME={$bill['party_name']
}' style='color:red'>{$bill['party_name']
}</a>" : $bill['party_name'];

                            $user_nameTag = $bill['salesman_exists'] == 'No' ? "<a href='index.php?option=COM_INSERT_SALESMEN&salesman_name={$bill['user_name']
}' style='color:red'>{$bill['user_name']
}</a>" : $bill['user_name'];

                            $planDisplay = [];
                            foreach ($bill['plan_names'] as $index => $planName) {
                                $planExists = $bill['plan_exists'][$index];
                                $planTag = $planExists === 'No' ? "<a href='index.php?option=COM_INSERT_ITEMS&ITMNAME={$planName
}' style='color:red'>{$planName
}</a>" : $planName;
                                $planDisplay[] = ($index + 1) . '. ' . $planTag;
                            }
                            $planHTML = implode('<br>', $planDisplay);
                    ?>
                            <tr>
                                <td>
                                    <input aria-label="<?= htmlspecialchars(__('Ected Ids'), ENT_QUOTES, 'UTF-8'); ?>[]" type="checkbox" name="selected_ids[]" value="<?php echo $bill['bill_no'] ?>" <?php echo $checkboxDisabled ?>></td>
                                <td><?php echo  $i; ?></td>
                                <td><?php echo  $partyTag ?></td>
                                <td><?php echo  $user_nameTag; ?></td>
                                <td><?php echo  $bill['mobile_no'] ?></td>
                                <td><?php echo  fromDate($bill['expiry_date']) ?></td>
                                <td><?php echo  $bill['bill_no'] ?></td>
                                <td><?php echo  $planHTML ?><br><small>(<?php echo $bill['row_count']; ?>  <?= __('Items)'); ?> </small></td>
                                <td><?php
                                if($bill['sales_invoice_id'] !=""){
                                    $nrid = "id:".$bill['sales_invoice_id'];
                                    $url = "index.php?option=COM_INSERT_SALES_INVOICES&Rid=".deocdeId($nrid);
                                    echo "<a href='".$url."' target='_blank' class='text-primary cursor-pointer m-r-10'><i class='ti ti-eye'></i>   ".$bill['sales_invoice_transaction_number']."</a>";
                                    ?>
                                    <button type="button" class="btn p-0 border-0 bg-transparent m-r-10 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= htmlspecialchars(__('Print'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Print'), ENT_QUOTES, 'UTF-8'); ?>" onclick="goToAnotherPageAndPrint('<?php echo $bill['sales_invoice_id']; ?>','sales_invoices')"><i class="ti-printer"></i></button>

                                    <button type="button" class="btn p-0 border-0 bg-transparent cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= htmlspecialchars(__('Download'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Download'), ENT_QUOTES, 'UTF-8'); ?>" onclick="DownloadPDF('<?php echo $bill['sales_invoice_id']; ?>','sales_invoices')"><i class="ti-cloud-down"></i></button>

                             <?php
                                }
                            ?></td>
                            </tr>
                        <?php $i++;
                        }
                    } else { ?>
                        <tr>
                            <td colspan="8">
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> <?= __('No Data Available'); ?></p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>


            <div class="text-right mt-3">
                <button type="submit" class="btn btn-success"><?= __('Submit'); ?></button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById("salesForm").addEventListener("submit", function(e) {
        const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
        if (checkboxes.length === 0) {
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", <?php echo json_encode(__('Please select at least one row.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, "warning");
            e.preventDefault(); // Prevent form submission
        }
    });
</script>
