
<div id="printDiv">
    <table style="width:100%; border-collapse:collapse; border:1px solid #cccccc; font-family:'Tahoma'; background:#ffffff;">
        <tbody>
            <tr>
                <th style="padding: 5px;">
                    <h2 style="font-size: 14px; color: #000000; font-weight: 600; margin: 0;"><?php echo $bill_by_gst_number; ?></h2>
                    <h3 style="font-size: 13px; color: #000000; font-weight: 600; margin: 5px 0 0 0;"><?php echo $bill_by_name; ?></h3>
                </th>
                <th style="padding: 5px; text-align: right;">
                    <img style="width: 60px;" src="uploads/all/<?php echo $config['display_logo']; ?>">
                </th>
            </tr>
            <tr>
                <th bgcolor="#eeeeee" colspan="2" style="font-size: 13px; color: #000000; padding: 5px; margin: 0; border: 1px solid #cccccc;">
                    1. Client Details
                </th>
            </tr>
            <tr>
                <th colspan="2" style="padding: 0px;"> 
                    <table style="width:100%;">
                        <tr>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Customer Name</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Customer No.</th>
                            <!-- <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">IRN</th> -->
                            <!-- <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Ack.No.</th> -->
                            <!-- <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Ack. Date</th> -->
                        </tr>
                        <tr>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding:5px;font-weight:normal;"><?php echo $Billed_to_party_name; ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">NA</td>
                            <!-- <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">9830181afe95a9ded1086b9adbeb46fc2</td> -->
                            <!-- <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">112632054050630</td> -->
                            <!-- <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">21-08-2026 12:22:00</td> -->
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th bgcolor="#eeeeee" colspan="2" style="font-size: 13px; color: #000000; padding: 5px; margin: 0; border: 1px solid #cccccc;">
                    2. Transaction Details
                </th>
            </tr>
            <tr>
                <th colspan="2" style="padding: 0px;"> 
                    <table style="width:100%;">
                        <tr>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Supply Type Code</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Document No</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Place of Supply</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Document Type</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Document Date</th>
                        </tr>
                        <tr>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding:5px;font-weight:normal;">NA</td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;"><?php echo $transaction['transaction_number']; ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">NA</td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;">Delivery Challan</td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;font-weight:normal;"><?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></td>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2" style="font-size: 13px; color: #000000; padding: 5px; margin: 0; border: 1px solid #cccccc;">
                    IGST applicable despite Supplier and Recipient located in same State: <?php echo $transaction['originator_state_id'] == $transaction['supplier_state_id']?"Yes":"No";?>
                </th>
            </tr>

            <tr>
                <th bgcolor="#eeeeee" colspan="2" style="font-size: 13px; color: #000000; padding: 5px; margin: 0; border: 1px solid #cccccc;">
                    3. Party Details
                </th>
            </tr>
            <tr>
                <td style="padding: 0px; border-right: 1px solid #cccccc;">
                    <div style="font-size:14px; color: #000000; padding: 5px; display: block;">Billed By:</div>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0;">GSTIN: <?php echo $Billed_to_gstin; ?></p>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0; white-space: normal;"><?php echo $Billed_to_party_name; ?></p>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0; white-space: normal;"><?php echo $Billed_to_party_address; ?></p>
                </td>
                <td style="padding: 0px;">
                    <div style="font-size:14px; color: #000000; padding: 5px; display: block;">Ship To:</div>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0;">GSTIN: <?php echo $Shipped_to_gstin; ?></p>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0; white-space: normal;"><?php echo $Shipped_to_party_name; ?></p>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0; white-space: normal;"><?php echo $Shipped_to_party_ship_adderss; ?></p>
                </td>
            </tr>
            <tr>
                <th bgcolor="#eeeeee" colspan="2" style="font-size: 13px; color: #000000; padding: 5px; margin: 0; border: 1px solid #cccccc;">
                    4. Details of Goods / Services
                </th>
            </tr>
            <tr>
                <th colspan="2" style="padding: 0px;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">S.N.</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Part Number</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Description</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">HSN Code</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Quantity</th>
                            <th style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px;">Unit</th>
                        </tr>
                        <?php $seq=1; foreach($transaction_items as $ta){ ?>
                        <tr>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?php echo $seq; ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?php echo $items[$ta['item_id']]['sku']; ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?= nl2br(wordwrap(htmlspecialchars($ta['description']), 50, "\n", true)); ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?php echo emptyCheck($parentrowidwithtax[$ta['id']]['tax_name']); ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?php echo emptyCheck($ta['item_quantity']); ?></td>
                            <td style="font-size:12px; color: #333333; border: 1px solid #cccccc; padding: 5px; font-weight: normal;"><?php echo emptyCheck($printuom[$ta['purchase_uom']]); ?></td>
                        </tr>
                         <?php  $seq++; } ?>
                    </table>
                </th>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="padding: 0px; border-right: 1px solid #cccccc;">
                    <div style="font-size:14px; color: #000000; padding: 5px; display: block;">Requested By: <?php echo $config['name']; ?></div>
                    <div style="font-size:14px; color: #000000; padding: 5px; display: block;">Requested On: 26-08-2026</div>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0;">Remarks- <?php echo emptyCheck($transaction['printing_notes']); ?></p>
                </td>
                <td style="padding: 0px; vertical-align: top;">
                    <div style="font-size:14px; color: #000000; padding: 5px; display: block;">Sign:</div>
                    <p style="font-size: 13px; color: #000000; padding: 5px; margin: 0;"></p>
                </td>
            </tr>
        </tfoot>
    </table>
</div> 



