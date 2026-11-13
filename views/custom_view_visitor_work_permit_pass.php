<?php
$return = array();
$visitor = array();
$workers = array();
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getvisitorworkpermitdata&rid=$rid";
    $che = callAPI($url);
    $return = json_decode($che, 1);
       $visitor = $return['visitor_data'] ?? [];
    $workers = $return['visitor_workers_data'] ?? [];

}

?>

<!-- Font Awesome CDN -->

<link rel="stylesheet" href="assets/css/custom_view_visitor_work_permit_pass.css">

<div class="page-body">
    <div class="card">
        <div class="card-body">
            <div id="printableArea">
                <table class="wp-table">
                    <tr>
                        <th scope="col" colspan="5" class="wp-title"><?= __('Work Permit'); ?></th>
                    </tr>
                    <tr>
                        <td class="wp-td">
                            <?= __('To, Microchip Technology (India) Pvt Ltd, Semmancheri, Chennai -119'); ?>
                        </td>
                        <td class="wp-td"><?= __('Access Card No'); ?>:-</td>
                        <td class="wp-td">
                            <?= htmlspecialchars($visitor['visitor_idcard_no'] ?? '  ') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="wp-th"><?= htmlspecialchars(__('NAME & ADDRESS OF THE PARTY'), ENT_QUOTES, 'UTF-8'); ?>:-</th>
                        <td class="wp-td"><?= __('Mobile No'); ?>:-</td>
                        <td class="wp-td">
                            <?= htmlspecialchars($visitor['service_phone'] ?? '  ') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="wp-td wp-wrap">
                            <?= htmlspecialchars($visitor['service_person'].' From '.$visitor['service_company_name'] .$visitor['address'] ?? '  ') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" colspan="5" style="font-size:15px; border: 1px solid #eeeeee; text-align: left;"><?= __('NATURE OF WORK.'); ?></th>
                    </tr>
                    <tr>
                        <td colspan="5" class="wp-td wp-wrap">
                            <?= htmlspecialchars($visitor['purpose'] ?? '') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="wp-td wp-wrap">
                            <?= __('THIS IS TO INFORM YOU THAT THE FOLLOWING WORKERS OF THE ABOVE PARTY WILL BE CARRYING THE ABOVE SAID WORK'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" colspan="5" class="wp-td">
                            <?= __('WORK LOCATION'); ?>:- <?= htmlspecialchars($visitor['location_name'] ?? '  ') ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="5" class="wp-td"><?= __('NAME OF THE WORKERS'); ?>:</th>
                    </tr>

                    <?php
                    $total = count($workers);
                    for ($i = 0; $i < 6; $i++) {
                        $left = isset($workers[$i]) ? htmlspecialchars($workers[$i]['service_person'].' ('.$workers[$i]['visitor_idcard_no'].')' ?? '  ') : ' ';
                        $right = isset($workers[$i + 6]) ? htmlspecialchars($workers[$i + 6]['service_person'].' ('.$workers[$i+6]['visitor_idcard_no'].')' ?? '  ') : ' ';
                        echo '<tr>
                            <td colspan="2" class="wp-td"><b>' . ($i + 1) . '.</b> ' . $left . '</td>
                            <td colspan="3" class="wp-td"><b>' . ($i + 7) . '.</b> ' . $right . '</td>
                        </tr>';
                    }
                    ?>

                    <tr>
                        <th scope="col" style="background-color: #eeeeee;" colspan="5" class="wp-th">&nbsp;</th>
                    </tr>
                    <tr>
                        <th scope="col" class="wp-th"><?= __('IN'); ?></th>
                        <td class="wp-td"><?= __('Date'); ?></td>
                        <td class="wp-td">
                            <?= !empty($visitor['in_datetime']) ? date('d-m-Y', strtotime($visitor['in_datetime'])) : '  ' ?>
                        </td>
                        <td class="wp-td"><?= __('Time'); ?></td>
                        <td class="wp-td">
                            <?= !empty($visitor['in_datetime']) ? date('h:i A', strtotime($visitor['in_datetime'])) : '  ' ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="wp-th"><?= __('OUT'); ?></th>
                        <td class="wp-td"><?= __('Date'); ?></td>
                        <td class="wp-td">
                            <?= !empty($visitor['out_datetime']) ? date('d-m-Y', strtotime($visitor['out_datetime'])) : '  ' ?>
                        </td>
                        <td class="wp-td"><?= __('Time'); ?></td>
                        <td class="wp-td">
                            <?= !empty($visitor['out_datetime']) ? date('h:i A', strtotime($visitor['out_datetime'])) : ' ' ?>
                        </td>
                    </tr>
                    <tr class="wp-separator">
                        <th scope="col" style="background-color: #eeeeee;" colspan="5" class="wp-th">&nbsp;</th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="2" class="wp-th"><?= __('Veg'); ?></th>
                        <td class="wp-td"><?= __('______________Nos'); ?></td>
                        <th scope="col" class="wp-th"><?= __('Non Veg'); ?></th>
                        <td class="wp-td"><?= __('______________Nos'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="wp-td">
                            <?= __('PLEASE PERMIT THEM TO CARRY THE JOBS AND PROVIDE NECESSARY HELP GUIDANCE TO THEM.'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="wp-td"><?= __('RESPONSIBLE PERSON FROM SECURITY INCHARGE'); ?>:-</td>
                        <th scope="col" class="wp-th"><?= __('ASO SIGN'); ?>:-</th>
                        <td class="wp-td">&nbsp;  </td>
                    </tr>
                    <tr>
                        <th scope="col" class="wp-th"><?= __('End User Sign'); ?>:-</th>
                        <th scope="col" class="wp-th"><?= __('End User Name'); ?>:-</th>
                        <td class="wp-td">
                            <?= htmlspecialchars($visitor['service_person'] ?? ' ') ?>&nbsp;
                        </td>
                        <th scope="col" class="wp-th"><?= __('Department'); ?>:-</th>
                        <td class="wp-td">
                            <?= htmlspecialchars($visitor['employee_department'] ?? ' ') ?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #eeeeee;" colspan="5" class="wp-th">&nbsp;</th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="2" class="wp-th"><?= __('REQUESTED BY'); ?></th>
                        <th scope="col" colspan="2" class="wp-th"><?= __('F.M SIGNATURE'); ?></th>
                        <th scope="col" class="wp-th"><?= __('APPROVED BY ADMIN'); ?></th>
                    </tr>
                    <tr>
                        <td colspan="2" class="wp-td">&nbsp; </td>
                        <td colspan="2" class="wp-td">&nbsp; </td>
                        <td class="wp-td">&nbsp;</td>
                    </tr>
                </table>
            </div>

            <button type="button" class="btn btn-primary wp-print-btn js-print-workpermit" data-print-target="printableArea"> <?= __('Print Work Permit'); ?></button>

        </div>
    </div>
</div>

<script src="assets/js/custom_view_visitor_work_permit_pass.js"></script>
