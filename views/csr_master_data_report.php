<?php

// ===============================
// 📅 Date Filters
// ===============================
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

// ===============================
// 🌐 API Call (to be implemented later)
// ===============================
$url = API . "?mode=getCsrMasterData&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['project_financial_list'] ?? [];
// echo $result['sql'] ;
$_SESSION['download_data'] = [];
?>
<link rel="stylesheet" href="assets/css/csr_master_data_report.css">
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>CSR MASTER DATA</h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=ProjectFinancial&format=csv" target="_blank">CSV</a>
                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=ProjectFinancial&format=excel" target="_blank">Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?php if (count((array)$list) != 0) { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm  align-middle">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-right">SL No</th>
                                <th>Entity</th>
                                <th>Project Name</th>
                                <th>Type of Project</th>
                                <th>Nature of Work Done</th>
                                <th>Type of Work</th>
                                <th>Project Related to FY</th>
                                <th>PO Issued Year</th>
                                <th>PO No</th>
                                <th>PO Date</th>
                                <th class="text-right">PO Value (Excl. Tax)</th>
                                <th class="text-right">GST @ 18%</th>
                                <th class="text-right">PO Value (Incl. Tax)</th>
                                <th>Business Partner Code</th>
                                <th>Business Partner Name</th>
                                <th>Invoice Accounting Year</th>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th class="text-right">Taxable Value</th>
                                <th class="text-right">GST Amt</th>
                                <th class="text-right">Other Cost</th>
                                <th class="text-right">Total Spent (Incl. GST)</th>
                                <th class="text-right">TDS Deducted</th>
                                <th class="text-right">Any Other Deduction</th>
                                <th class="text-right fw-bold">Net Payable</th>
                                <th class="text-right">Spent %</th>
                                <th class="text-right">Unspent %</th>
                                <th class="text-right">Paid Amount</th>
                                <th class="text-right">Delta</th>
                                <th>Paid Date</th>
                                <th>Paid From Which Bank</th>
                                <th>Project Completion Date</th>
                                <th>Project Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 1;
                            $cssdata = [];
                            foreach ($list as $r) {
                            ?>
                                <tr>
                                    <td class="text-right "><?php echo $sl; ?></td>
                                    <td class=""><?php echo $r['entity'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['project_name'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['type_of_project'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['nature_of_work_done'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['type_of_work'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['project_related_to_fy'] != '' ? 'FY '.$r['project_related_to_fy']  : '-'; ?></td>
                                    <td class=""><?php echo $r['po_issued_year'] != '' ? 'FY '.$r['po_issued_year']  : '-'; ?></td>
                                    <td class=""><?php echo $r['po_no'] ?? '-'; ?></td>
                                    <td class=""><?php echo fromDate($r['po_date']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['po_value_excl_tax']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['gst_18']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['po_value_incl_tax']) ?? '-'; ?></td>
                                    <td class=""><?php echo $r['bp_code'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['bp_name'] ?? '-'; ?></td>
                                    <td class=""><?php echo $r['invoice_accounting_year'] != '' ? 'FY '.$r['invoice_accounting_year'] : '-'; ?></td>
                                    <td class=""><?php echo $r['invoice_number'] ?? '-'; ?></td>
                                    <td class=""><?php echo fromDate($r['inv_date']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['taxable_value']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['gst_amount']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['other_cost']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['total_spent']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['tds_deducted']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['any_other_deduction']) ?? '-'; ?></td>
                                    <td class="text-right  fw-bold"><?php echo customeummarmonyformate($r['net_payable']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo $r['spent_percent'] ?? '-'; ?></td>
                                    <td class="text-right "><?php echo $r['unspent_percent'] ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['paid_amount']) ?? '-'; ?></td>
                                    <td class="text-right "><?php echo customeummarmonyformate($r['delta']) ?? '-'; ?></td>
                                    <td class=""><?php echo fromDate($r['paid_date']) ?? '-'; ?></td>
                                    <td class=""><?php echo $r['paid_from_bank'] ?? '-'; ?></td>
                                    <td class=""><?php echo fromDate($r['project_completion_date']) ?? '-'; ?></td>
                                    <td class=""><?php echo $r['project_status'] ?? '-'; ?></td>
                                </tr>
                            <?php
                                $cssdata[] = [
                                    'Sl No' => $sl++,
                                    'Entity' => $r['entity'] ?? '-',
                                    'Project Name' => $r['project_name'] ?? '-',
                                    'Type of Project' => $r['type_of_project'] ?? '-',
                                    'Nature of Work Done' => $r['nature_of_work_done'] ?? '-',
                                    'Type of Work' => $r['type_of_work'] ?? '-',
                                    'Project Related to FY' => $r['project_related_to_fy'] != '' ? 'FY '.$r['project_related_to_fy']  : '-',
                                    'PO Issued Year' => $r['po_issued_year'] != '' ? 'FY '.$r['po_issued_year']  : '-',
                                    'PO No' => $r['po_no'] ?? '-',
                                    'PO Date' => fromDate($r['po_date']) ?? '-',
                                    'PO Value (Excl. Tax)' => customeummarmonyformate($r['po_value_excl_tax']) ?? '-',
                                    'GST @ 18%' => customeummarmonyformate($r['gst_18']) ?? '-',
                                    'PO Value (Incl. Tax)' => customeummarmonyformate($r['po_value_incl_tax']) ?? '-',
                                    'BP Code' => $r['bp_code'] ?? '-',
                                    'BP Name' => $r['bp_name'] ?? '-',
                                    'Invoice Accounting Year' => $r['invoice_accounting_year'] != '' ? 'FY '.$r['invoice_accounting_year'] : '-',
                                    'Invoice Number' => $r['invoice_number'] ?? '-',
                                    'Invoice Date' => fromDate($r['inv_date']) ?? '-',
                                    'Taxable Value' => customeummarmonyformate($r['taxable_value']) ?? '-',
                                    'GST Amt' => customeummarmonyformate($r['gst_amount']) ?? '-',
                                    'Other Cost' => customeummarmonyformate($r['other_cost']) ?? '-',
                                    'Total Spent' => customeummarmonyformate($r['total_spent']) ?? '-',
                                    'TDS Deducted' => customeummarmonyformate($r['tds_deducted']) ?? '-',
                                    'Any Other Deduction' => customeummarmonyformate($r['any_other_deduction']) ?? '-',
                                    'Net Payable' => customeummarmonyformate($r['net_payable']) ?? '-',
                                    'Spent %' => $r['spent_percent'] ?? '-',
                                    'Unspent %' => $r['unspent_percent'] ?? '-',
                                    'Paid Amount' => customeummarmonyformate($r['paid_amount']) ?? '-',
                                    'Delta' => customeummarmonyformate($r['delta']) ?? '-',
                                    'Paid Date' => fromDate($r['paid_date']) ?? '-',
                                    'Paid From Bank' => $r['paid_from_bank'] ?? '-',
                                    'Project Completion Date' => fromDate($r['project_completion_date']) ?? '-',
                                    'Project Status' => $r['project_status'] ?? '-'
                                ];
                            }
                            $_SESSION['download_data'] = $cssdata;
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-4">
                    <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Data Available</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px max-h-300" alt="" src="assets/images/work.png" >
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>