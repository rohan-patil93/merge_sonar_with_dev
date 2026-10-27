<?php
    $petty_url = API."?mode=togetpettycashsummary";
    $petty_data = callAPI($petty_url);
    $petty_data = json_decode($petty_data,1);
    $header = $petty_data['header'];
    $data = $petty_data['data'];
    $sum_arry = $petty_data['sum'];
    $message = $petty_data['message'];

 ?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Petty Cash Summary
                        </h3>
                    </div>
                </div>
                <form class="kt-form kt-form--fit kt-form--label-right">
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="kt-invoice__items">
                                <div class="kt-invoice__item">
                                    <span class="kt-invoice__subtitle">Branch</span>
                                    <span class="kt-invoice__text">Delhi Branch</span>
                                </div>
                                <div class="kt-invoice__item">
                                    <span class="kt-invoice__subtitle">As on</span>
                                    <span class="kt-invoice__text">June 11, 2020</span>
                                </div>
                                <div class="kt-invoice__item">
                                    <span class="kt-invoice__subtitle">Printed By</span>
                                    <span class="kt-invoice__text"><?php echo $_SESSION['us_name']; ?></span>
                                </div>
                                <div class="kt-invoice__item">
                                    <span class="kt-invoice__subtitle">Printed On</span>
                                    <span class="kt-invoice__text"><?php echo date('M d, Y  h:i:s A'); ?></span>
                                </div>
                            </div>
                            <div class="table-over">
                                <table style="width: 100%; margin: 20px 0 0 0;">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="text-align:center;">Cash Recieved</th>
                                            <th scope="col" style="text-align:center;">Tr#</th>
                                            <th scope="col" style="text-align:center;">Tr Date</th>

                                            <th scope="col" style="text-align:center;">Total Payment</th>
                                            <th scope="col" colspan="3" rowspan="1">Category</th>
                                        </tr>
                                        <tr role="row">
                                            <td rowspan="1"></td>
                                            <td rowspan="1"></td>
                                            <td rowspan="1"></td>

                                            <td rowspan="1"></td>
                                            <td rowspan="1" style="padding: 0;">
                                                <table role="presentation" style="width: 100%;">
                                                    <tr style="text-align: center;">
                                                        <?php foreach ($header as  $head) {
                                                            $array_sum[] = $sum_arry[$head['id']];
                                                        ?>
                                                        <td style="border-right:1px solid #eee;"><?php echo $head['name']; ?></td>
                                                    <?php } ?>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                        <?php $grand = 0;
                                        foreach ($message as $key=>$mes) { ?>
                                        <tr role="row">
                                            <td></td>
                                            <td><?php echo $mes['tr_no']; ?></td>
                                            <td><?php echo $mes['tr_date']; ?></td>
                                            <td><?php echo customeummarmonyformate($mes['total']); ?></td>

                                            <td style="padding: 0;">
                                                <table style="width: 100%;">
                                                    <thead>
                                                        <tr style="text-align: center;">
                                                            <?php $grand = $grand+$mes['total'];

                                                            foreach ($header as  $head) {  //print_r($head);?>
                                                            <th scope="col"><?php echo customeummarmonyformate($data[$key][$head['id']]); ?></th>
                                                        <?php } ?>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </th>
                                        </tr>
                                         <?php } ?>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="col">Grand Total</th>
                                            <td></td>
                                            <td></td>

                                            <th scope="col" style="text-align:center;"><?php echo customeummarmonyformate($grand); ?></th>
                                            <td style="padding: 0;">
                                                <table style="width: 100%;">
                                                    <tbody>
                                                        <tr style="text-align: center;">
                                                            <?php foreach ($array_sum as $ss) { ?>
                                                            <th scope="col"><?php echo customeummarmonyformate($ss); ?></th>

                                                        <?php } ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </form>


            </div>
        </div>
    </div>
</div>
