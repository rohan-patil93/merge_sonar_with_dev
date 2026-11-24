<script>
    function printDiv(divName) {
        window.print();
    }
</script>
<div id="printDiv" style="width: 800px; display: block; margin: 0 auto !important; background: #ffffff;">



    <table role="presentation"
        style="border: 1px solid #cccccc; border-collapse: collapse; background-color: #ffffff; margin-left: auto; margin-right: auto;">

        <!-- HEADER SECTION -->
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #cccccc;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 16px; font-weight: bold; font-family: Arial, sans-serif;">
                            <?= __('DELIVERY CHALLAN'); ?>
                        </td>
                        <td style="text-align: right; vertical-align: top;">
                            <span style="font-size: 20px; font-weight: bold; color: #cc0000;"><?= __('BION'); ?></span>
                            <span style="font-size: 20px; font-weight: bold; color: #000000;"><?= __('EAU'); ?></span>
                            <span style="font-size: 16px; font-weight: bold; color: #000000;">&reg;</span>
                            <div style="font-size: 9px; color: #555555; margin-top: -2px;">
                                <?= __('aspire to think beyond'); ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- CONTACT PERSON & CHALLAN INFO -->
        <tr>
            <td style="padding: 10px 10px 5px 10px;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- Left Info Box -->
                        <td style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 35%; font-weight: bold; padding: 2px;"><?= __('Contact Person'); ?></td>
                                    <td style="width: 5%; padding: 2px;">:</td>
                                    <td style="padding: 2px;"><?= __('Shobhna'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Email ID'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">shobhna@nri.infistaq.in</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Phone Number'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('SOB No./Date'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">- / -</td>
                                </tr>
                            </table>
                        </td>

                <div style="width: 4%;">&nbsp;</div>

                        <!-- Right Info Box -->
                        <td style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 35%; font-weight: bold; padding: 2px;"><?= __('Challan No.'); ?></td>
                                    <td style="width: 5%; padding: 2px;">:</td>
                                    <td style="padding: 2px;"><?= __('DC/127191'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Challan Date'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">03 Aug 2026</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('PO Number'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">127191</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('PO Date'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">03 Aug 2026</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Due Date'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">03 Aug 2026</td>
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
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- Billed by -->
                        <td style="width: 31%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                                <?= __('Billed by (Supplier)'); ?></div>
                            <div style="font-weight: bold; font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>
                            <div style="font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%; padding: 1px;">:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                            </table>
                        </td>

                <div style="width: 3.5%;">&nbsp;</div>

                <!-- Billed to -->
                <div style="width: 31%; border: 1px solid #cccccc; padding: 6px; white-space: normal; box-sizing: border-box;">
                    <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                        <?= __('Billed to (Consignee)'); ?></div>
                    <div style="font-weight: bold; font-size: 12px;"><?= __('National Rubber Industries'); ?>
                    </div>
                    <div style="font-size: 12px;">
                        <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                    </div>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%; padding: 1px;">:</td>
                                    <td style="padding: 1px;"><?= __('36AFXPG6150R1ZJ'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                            </table>
                        </td>

                <div style="width: 3.5%;">&nbsp;</div>

                <!-- Shipped to -->
                <div style="width: 31%; border: 1px solid #cccccc; padding: 6px; white-space: normal; box-sizing: border-box;">
                    <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                        <?= __('Shipped to (Delivery Address)'); ?></div>
                    <div style="font-weight: bold; font-size: 12px;"><?= __('National Rubber Industries'); ?>
                    </div>
                    <div style="font-size: 12px;">
                        <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                    </div>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="width: 30%; font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></td>
                                    <td style="width: 10%; padding: 1px;">:</td>
                                    <td style="padding: 1px;"><?= __('36AFXPG6150R1ZJ'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ITEMS TABLE -->
        <div style="padding: 5px 10px;">
            <table style="width: 100%; border: 1px solid #cccccc; border-collapse: collapse; font-size: 10px;">
                <thead>
                    <tr style="font-weight: bold; text-align: center; border: 1px solid #cccccc;">
                        <th scope="col" style="width: 5%; border: 1px solid #cccccc; padding: 5px;">#</th>
                        <th scope="col" style="width: 12%; border: 1px solid #cccccc; padding: 5px;"><?= __('Item Code'); ?></th>
                        <th scope="col" style="width: 35%; border: 1px solid #cccccc; padding: 5px;"><?= __('Description'); ?>
                        </th>
                        <th scope="col" style="width: 13%; border: 1px solid #cccccc; padding: 5px;"><?= __('HSN/SAC'); ?></th>
                        <th scope="col" style="width: 8%; border: 1px solid #cccccc; padding: 5px;"><?= __('UOM'); ?></th>
                        <th scope="col" style="width: 7%; border: 1px solid #cccccc; padding: 5px;"><?= __('QTY.'); ?></th>
                        <th scope="col" style="width: 12%; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('Unit Description'); ?></th>
                        <th scope="col" style="width: 8%; border: 1px solid #cccccc; padding: 5px;"><?= __('Remarks'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">1</td>
                        <td style="text-align: center; font-weight: bold; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('N-01-AX308'); ?></td>
                        <td style="white-space: normal; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('Neauseal US 2560, Autoclave Door Gasket, US2500 FDA, SIL Pt Red Colour, Square Solid Type, 2950 * 21 * 21'); ?>
                        </td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('NO-HSN-CODE'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('NOS'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">4.00</td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('Pieces'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">-</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">2</td>
                        <td style="text-align: center; font-weight: bold; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('N-01-AX310'); ?></td>
                        <td style="white-space: normal; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('Neauseal US 2560 Autoclave Door Gasket US2500 FDA SIL Pt Red Colour Square Solid Type 2350 * 21 * 21 Chamber Size: 600 * 600'); ?>
                        </td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('NO-HSN-CODE'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('NOS'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">4.00</td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;"><?= __('Pieces'); ?></td>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PACKAGES & REMARKS SECTION -->
        <tr>
            <td style="padding: 5px 10px;">
                <table role="presentation" style="width: 100%; border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; border-right: 1px solid #cccccc; padding: 6px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 10px;">
                                <tr>
                                    <td style="width: 45%; font-weight: bold; padding: 2px;"><?= __('Total Packages / Boxes'); ?></td>
                                    <td style="width: 5%; padding: 2px;">:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Transporter'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Vehicle No.'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('LR / GR No.'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 2px;"><?= __('Dispatched Through'); ?></td>
                                    <td>:</td>
                                    <td style="padding: 2px;">-</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 6px;">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 5px;">
                                <?= __('Remarks / Instructions'); ?></div>
                            <div style="font-size: 10px;">-</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- FOOTER SECTION -->
        <tr>
            <td style="padding: 5px 10px 10px 10px;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- Left: Acknowledgment Box -->
                        <td style="width: 35%; vertical-align: top; border: 1px solid #cccccc; padding: 6px;">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 12px;">
                                <?= __('Received the above goods in good condition'); ?></div>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 10px;">
                                <tr>
                                    <td style="width: 30%; padding: 4px;"><?= __('Name'); ?></td>
                                    <td style="width: 5%; padding: 2px;">:</td>
                                    <td style="border-bottom: 1px solid #cccccc; padding: 4px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px;"><?= __('Designation'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc; padding: 4px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px;"><?= __('Signature'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc; padding: 4px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px;"><?= __('Date'); ?></td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px solid #cccccc; padding: 4px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>

                <div style="width: 2%;">&nbsp;</div>

                        <!-- Middle: Schedule & Payment Terms -->
                        <td style="width: 28%; vertical-align: top;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
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
                                        <table role="presentation" style="width: 100%; border: 1px solid #cccccc; border-collapse: collapse; font-size: 10px; text-align: center;">
                                            <tr>
                                                <td style="width: 50%; border: 1px solid #cccccc; padding: 4px;">02 Sep 2026</td>
                                                <td style="width: 50%; border: 1px solid #cccccc; padding: 4px;">&#8377;35,966.40</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 2%;">&nbsp;</td>

                <!-- Right: Signature Box -->
                <div style="width: 33%; text-align: center; padding-top: 10px; box-sizing: border-box;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 40px;">
                        <?= __('For National Rubber Industries'); ?></div>
                    <div style="border-top: 1px solid #cccccc; width: 80%; margin: 0 auto;"></div>
                    <div style="font-weight: bold; font-size: 10px; margin-top: 4px;">
                        <?= __('Authorised Signature'); ?></div>
                    <div style="font-size: 9px; margin-top: 10px;">
                        <?= __('This is a computer generated document'); ?><br><?= __('no signature required'); ?>
                    </div>
                    <div style="font-size: 10px; margin-top: 8px;">
                        <?= htmlspecialchars(__('E. & O.E.'), ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>

    </div>

</div>

<button type="button" class="print-btn" style="margin: 15px 0; padding: 8px 16px; cursor: pointer;"
    onclick="printDiv('printableArea')"><?= __('Print Invoice'); ?></button>