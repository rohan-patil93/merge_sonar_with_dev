<script type="text/javascript">
    function printDiv(divName) {
        window.print();
    }
</script>
<div id="printDiv" style="width: 800px; display: block; margin: 0 auto !important; background: #ffffff;">



    <table
        style="border: 1px solid #cccccc; border-collapse: collapse; border-spacing: 0; background-color: #ffffff; margin: 0 auto;">

        <!-- HEADER SECTION -->
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #cccccc;">
                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0;">
                    <tr>
                        <td style="font-size: 16px; font-weight: bold; font-family: Arial, sans-serif;">
                            <?= __('DELIVERY CHALLAN'); ?>
                        </td>
                        <td style="text-align: right; vertical-align: top;">
                            <span style="font-size: 20px; font-weight: bold; color: #cc0000;"><?= __('BION'); ?></span>
                            <span style="font-size: 20px; font-weight: bold; color: #000000;"><?= __('EAU'); ?></span>
                            <span style="font-size: 16px; font-weight: bold; color: #000000;">&reg;</span>
                            <div style="font-size: 9px; color: #555555; margin-top: -2px;">
                                <?= __('aspire to think beyond'); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- CONTACT PERSON & CHALLAN INFO -->
        <tr>
            <td style="padding: 10px 10px 5px 10px;">
                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0;">
                    <tr>
                        <!-- Left Info Box -->
                        <td style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 11px;">
                                <tr>
                                    <td style="width: 35%; font-weight: bold;"><?= __('Contact Person'); ?></td>
                                    <td style="width: 5%;">:</td>
                                    <td><?= __('Shobhna'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Email ID'); ?></td>
                                    <td>:</td>
                                    <td>shobhna@nri.infistaq.in</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Phone Number'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('SOB No./Date'); ?></td>
                                    <td>:</td>
                                    <td>- / -</td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 4%;">&nbsp;</td>

                        <!-- Right Info Box -->
                        <td style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 11px;">
                                <tr>
                                    <td style="width: 35%; font-weight: bold;"><?= __('Challan No.'); ?></td>
                                    <td style="width: 5%;">:</td>
                                    <td><?= __('DC/127191'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Challan Date'); ?></td>
                                    <td>:</td>
                                    <td>03 Aug 2026</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('PO Number'); ?></td>
                                    <td>:</td>
                                    <td>127191</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('PO Date'); ?></td>
                                    <td>:</td>
                                    <td>03 Aug 2026</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Due Date'); ?></td>
                                    <td>:</td>
                                    <td>03 Aug 2026</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ADDRESSES SECTION -->
        <tr>
            <td style="padding: 5px 10px;">
                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0;">
                    <tr>
                        <!-- Billed by -->
                        <td style="width: 31%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                                <?= __('Billed by (Supplier)'); ?>
                            </div>
                            <div style="font-weight: bold; font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>
                            <div style="font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>

                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%;">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 3.5%;">&nbsp;</td>

                        <!-- Billed to -->
                        <td
                            style="width: 31%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; white-space: normal;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                                <?= __('Billed to (Consignee)'); ?>
                            </div>
                            <div style="font-weight: bold; font-size: 12px;"><?= __('National Rubber Industries'); ?>
                            </div>
                            <div style="font-size: 12px;">
                                <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                            </div>

                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%;">:</td>
                                    <td><?= __('36AFXPG6150R1ZJ'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 3.5%;">&nbsp;</td>

                        <!-- Shipped to -->
                        <td
                            style="width: 31%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; white-space: normal;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                                <?= __('Shipped to (Delivery Address)'); ?>
                            </div>
                            <div style="font-weight: bold; font-size: 12px;"><?= __('National Rubber Industries'); ?>
                            </div>
                            <div style="font-size: 12px;">
                                <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                            </div>

                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%;">:</td>
                                    <td><?= __('36AFXPG6150R1ZJ'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ITEMS TABLE -->
        <tr>
            <td style="padding: 5px 10px;">
                <table
                    style="width: 100%; border-collapse: collapse; border-spacing: 0; font-size: 10px; border: 1px solid #cccccc;">
                    <thead>
                        <tr style="font-weight: bold; text-align: center; border: 1px solid #cccccc;">
                            <th scope="col" style="width: 5%; border: 1px solid #cccccc;">#</th>
                            <th scope="col" style="width: 12%; border: 1px solid #cccccc;"><?= __('Item Code'); ?></th>
                            <th scope="col" style="width: 35%; border: 1px solid #cccccc;"><?= __('Description'); ?>
                            </th>
                            <th scope="col" style="width: 13%; border: 1px solid #cccccc;"><?= __('HSN/SAC'); ?></th>
                            <th scope="col" style="width: 8%; border: 1px solid #cccccc;"><?= __('UOM'); ?></th>
                            <th scope="col" style="width: 7%; border: 1px solid #cccccc;"><?= __('QTY.'); ?></th>
                            <th scope="col" style="width: 12%; border: 1px solid #cccccc;">
                                <?= __('Unit Description'); ?>
                            </th>
                            <th scope="col" style="width: 8%; border: 1px solid #cccccc;"><?= __('Remarks'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="border: 1px solid #cccccc;">1</td>
                            <td class="text-center" style="font-weight: bold; border: 1px solid #cccccc;">
                                <?= __('N-01-AX308'); ?>
                            </td>
                            <td style="white-space: normal; border: 1px solid #cccccc;">
                                <?= __('Neauseal US 2560, Autoclave Door Gasket, US2500 FDA, SIL Pt Red Colour, Square Solid Type, 2950 * 21 * 21'); ?>
                            </td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('NO-HSN-CODE'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('NOS'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;">4.00</td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('Pieces'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;">-</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="border: 1px solid #cccccc;">2</td>
                            <td class="text-center" style="font-weight: bold; border: 1px solid #cccccc;">
                                <?= __('N-01-AX310'); ?>
                            </td>
                            <td style="white-space: normal; border: 1px solid #cccccc;">
                                <?= __('Neauseal US 2560 Autoclave Door Gasket US2500 FDA SIL Pt Red Colour Square Solid Type 2350 * 21 * 21 Chamber Size: 600 * 600'); ?>
                            </td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('NO-HSN-CODE'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('NOS'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;">4.00</td>
                            <td class="text-center" style="border: 1px solid #cccccc;"><?= __('Pieces'); ?></td>
                            <td class="text-center" style="border: 1px solid #cccccc;">-</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <!-- PACKAGES & REMARKS SECTION -->
        <tr>
            <td style="padding: 5px 10px;">
                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 1px solid #cccccc;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; border-right: 1px solid #cccccc; padding: 6px;">
                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 10px;">
                                <tr>
                                    <td style="width: 45%; font-weight: bold;"><?= __('Total Packages / Boxes'); ?></td>
                                    <td style="width: 5%;">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Transporter'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Vehicle No.'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('LR / GR No.'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;"><?= __('Dispatched Through'); ?></td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 6px;">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 5px;">
                                <?= __('Remarks / Instructions'); ?>
                            </div>
                            <div style="font-size: 10px;">-</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- FOOTER SECTION -->
        <tr>
            <td style="padding: 5px 10px 10px 10px;">
                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0;">
                    <tr>
                        <!-- Left: Acknowledgment Box -->
                        <td style="width: 35%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 12px;">
                                <?= __('Received the above goods in good condition'); ?>
                            </div>
                            <table
                                style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0; font-size: 10px;">
                                <tr>
                                    <td style="width: 30%;"><?= __('Name'); ?></td>
                                    <td style="width: 5%;">:</td>
                                    <td style="border-bottom: 1px solid #cccccc;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?= __('Designation'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?= __('Signature'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?= __('Date'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 2%;">&nbsp;</td>

                        <!-- Middle: Schedule & Payment Terms -->
                        <td style="width: 28%; vertical-align: top;">
                            <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 0;">
                                <tr>
                                    <td style="border: 1px solid #cccccc; padding: 4px; text-align: center;">
                                        <div style="font-weight: bold; font-size: 10px;"><?= __('Delivery Schedule'); ?>
                                        </div>
                                        <div style="font-size: 10px; margin-top: 4px;">-</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 6px;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #cccccc; padding: 4px; text-align: center;">
                                        <div style="font-weight: bold; font-size: 10px;"><?= __('Payment Terms'); ?>
                                        </div>
                                        <div style="font-size: 10px; margin-top: 4px;"><?= __('30 DAYS'); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 6px;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table
                                            style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 1px solid #cccccc; font-size: 10px; text-align: center;">
                                            <tr>
                                                <td style="width: 50%; border: 1px solid #cccccc;">02 Sep 2026</td>
                                                <td style="width: 50%; border: 1px solid #cccccc;">&#8377;35,966.40</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 2%;">&nbsp;</td>

                        <!-- Right: Signature Box -->
                        <td style="width: 33%; vertical-align: top; text-align: center; padding-top: 10px;">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 40px;">
                                <?= __('For National Rubber Industries'); ?>
                            </div>
                            <div style="border-top: 1px solid #cccccc; width: 80%; margin: 0 auto;"></div>
                            <div style="font-weight: bold; font-size: 10px; margin-top: 4px;">
                                <?= __('Authorised Signature'); ?>
                            </div>
                            <div style="font-size: 9px; margin-top: 10px;">
                                <?= __('This is a computer generated document'); ?><br /><?= __('no signature required'); ?>
                            </div>
                            <div style="font-size: 10px; margin-top: 8px;">
                                <?= htmlspecialchars(__('E. & O.E.'), ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

</div>

<button class="print-btn" style="margin: 15px 0; padding: 8px 16px; cursor: pointer;"
    onclick="printDiv('printableArea')"><?= __('Print Invoice'); ?></button>