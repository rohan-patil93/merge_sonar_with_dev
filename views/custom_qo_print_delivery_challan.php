<script>
    function printDiv(divName) {
        window.print();
    }
</script>
<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #printDiv {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }
        .print-btn {
            display: none !important;
        }
    }
</style>
<div id="printDiv" style="width: 800px; display: block; margin: 0 auto !important; background: #ffffff;">

    <div style="border: 1px solid #cccccc; background-color: #ffffff; margin-left: auto; margin-right: auto; box-sizing: border-box; font-family: Arial, sans-serif;">

        <!-- HEADER SECTION -->
        <div style="padding: 10px; border-bottom: 1px solid #cccccc; display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="font-size: 16px; font-weight: bold; font-family: Arial, sans-serif;">
                <?= __('DELIVERY CHALLAN'); ?>
            </div>
            <div style="text-align: right; vertical-align: top;">
                <span
                    style="font-size: 20px; font-weight: bold; color: #cc0000;"><?= __('BION'); ?></span>
                <span
                    style="font-size: 20px; font-weight: bold; color: #000000;"><?= __('EAU'); ?></span>
                <span style="font-size: 16px; font-weight: bold; color: #000000;">&reg;</span>
                <div style="font-size: 9px; color: #555555; margin-top: -2px;">
                    <?= __('aspire to think beyond'); ?>
                </div>
            </div>
        </div>

        <!-- CONTACT PERSON & CHALLAN INFO -->
        <div style="padding: 10px 10px 5px 10px; display: flex; justify-content: space-between; box-sizing: border-box;">
            <!-- Left Info Box -->
            <div style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; box-sizing: border-box; font-size: 11px;">
                <div style="display: grid; grid-template-columns: 35% 5% 60%; row-gap: 2px;">
                    <div style="font-weight: bold; padding: 2px;"><?= __('Contact Person'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;"><?= __('Shobhna'); ?></div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('Email ID'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">shobhna@nri.infistaq.in</div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('Phone Number'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">-</div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('SOB No./Date'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">- / -</div>
                </div>
            </div>

            <!-- Right Info Box -->
            <div style="width: 48%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; box-sizing: border-box; font-size: 11px;">
                <div style="display: grid; grid-template-columns: 35% 5% 60%; row-gap: 2px;">
                    <div style="font-weight: bold; padding: 2px;"><?= __('Challan No.'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;"><?= __('DC/127191'); ?></div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('Challan Date'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">03 Aug 2026</div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('PO Number'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">127191</div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('PO Date'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">03 Aug 2026</div>

                    <div style="font-weight: bold; padding: 2px;"><?= __('Due Date'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="padding: 2px;">03 Aug 2026</div>
                </div>
            </div>
        </div>

        <!-- ADDRESSES SECTION -->
        <div style="padding: 5px 10px; display: flex; justify-content: space-between; box-sizing: border-box;">
            <!-- Billed by -->
            <div style="width: 31%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; box-sizing: border-box; font-size: 12px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                    <?= __('Billed by (Supplier)'); ?>
                </div>
                <div style="font-weight: bold; font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>
                <div style="font-size: 12px;"><?= __('Suresh Enterprises'); ?></div>

                <div style="display: grid; grid-template-columns: 30% 10% 60%; font-size: 11px; margin-top: 4px;">
                    <div style="font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 2px;">-</div>

                    <div style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 2px;">-</div>
                </div>
            </div>

            <!-- Billed to -->
            <div style="width: 31%; border: 1px solid #cccccc; padding: 6px; white-space: normal; box-sizing: border-box; font-size: 12px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                    <?= __('Billed to (Consignee)'); ?>
                </div>
                <div style="font-weight: bold; font-size: 12px;">
                    <?= __('National Rubber Industries'); ?>
                </div>
                <div style="font-size: 12px;">
                    <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                </div>

                <div style="display: grid; grid-template-columns: 30% 10% 60%; font-size: 11px; margin-top: 4px;">
                    <div style="font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 1px;"><?= __('36AFXPG6150R1ZJ'); ?></div>

                    <div style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 2px;">-</div>
                </div>
            </div>

            <!-- Shipped to -->
            <div style="width: 31%; border: 1px solid #cccccc; padding: 6px; white-space: normal; box-sizing: border-box; font-size: 12px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px;">
                    <?= __('Shipped to (Delivery Address)'); ?>
                </div>
                <div style="font-weight: bold; font-size: 12px;"><?= __('National Rubber Industries'); ?></div>
                <div style="font-size: 12px;">
                    <?= __('NATIONAL RUBBER INDUSTRIES, Corp.Office:305,Ashoka Chambers, Adarshnagar,Hill Fort Road, Hyderabad,500063'); ?>
                </div>

                <div style="display: grid; grid-template-columns: 30% 10% 60%; font-size: 11px; margin-top: 4px;">
                    <div style="font-weight: bold; padding: 1px;"><?= __('GSTIN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 1px;"><?= __('36AFXPG6150R1ZJ'); ?></div>

                    <div style="font-weight: bold; padding: 1px;"><?= __('PAN'); ?></div>
                    <div style="padding: 1px;">:</div>
                    <div style="padding: 2px;">-</div>
                </div>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <div style="padding: 5px 10px;">
            <table style="width: 100%; border: 1px solid #cccccc; border-collapse: collapse; font-size: 10px;">
                <caption style="position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0,0,0,0); border: 0;"><?= __('Delivery Challan Items'); ?></caption>
                <thead>
                    <tr style="font-weight: bold; text-align: center; border: 1px solid #cccccc;">
                        <th scope="col" style="width: 5%; border: 1px solid #cccccc; padding: 5px;">#</th>
                        <th scope="col" style="width: 12%; border: 1px solid #cccccc; padding: 5px;"><?= __('Item Code'); ?></th>
                        <th scope="col" style="width: 35%; border: 1px solid #cccccc; padding: 5px;"><?= __('Description'); ?></th>
                        <th scope="col" style="width: 13%; border: 1px solid #cccccc; padding: 5px;"><?= __('HSN/SAC'); ?></th>
                        <th scope="col" style="width: 8%; border: 1px solid #cccccc; padding: 5px;"><?= __('UOM'); ?></th>
                        <th scope="col" style="width: 7%; border: 1px solid #cccccc; padding: 5px;"><?= __('QTY.'); ?></th>
                        <th scope="col" style="width: 12%; border: 1px solid #cccccc; padding: 5px;"><?= __('Unit Description'); ?></th>
                        <th scope="col" style="width: 8%; border: 1px solid #cccccc; padding: 5px;"><?= __('Remarks'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; border: 1px solid #cccccc; padding: 5px;">1</td>
                        <td style="text-align: center; font-weight: bold; border: 1px solid #cccccc; padding: 5px;">
                            <?= __('N-01-AX308'); ?>
                        </td>
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
                            <?= __('N-01-AX310'); ?>
                        </td>
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
        <div style="padding: 5px 10px;">
            <div style="width: 100%; border: 1px solid #cccccc; display: flex; box-sizing: border-box;">
                <div style="width: 50%; vertical-align: top; border-right: 1px solid #cccccc; padding: 6px; box-sizing: border-box; font-size: 10px;">
                    <div style="display: grid; grid-template-columns: 45% 5% 50%; row-gap: 2px;">
                        <div style="font-weight: bold; padding: 2px;"><?= __('Total Packages / Boxes'); ?></div>
                        <div style="padding: 2px;">:</div>
                        <div style="padding: 2px;">-</div>

                        <div style="font-weight: bold; padding: 2px;"><?= __('Transporter'); ?></div>
                        <div style="padding: 2px;">:</div>
                        <div style="padding: 2px;">-</div>

                        <div style="font-weight: bold; padding: 2px;"><?= __('Vehicle No.'); ?></div>
                        <div style="padding: 2px;">:</div>
                        <div style="padding: 2px;">-</div>

                        <div style="font-weight: bold; padding: 2px;"><?= __('LR / GR No.'); ?></div>
                        <div style="padding: 2px;">:</div>
                        <div style="padding: 2px;">-</div>

                        <div style="font-weight: bold; padding: 2px;"><?= __('Dispatched Through'); ?></div>
                        <div style="padding: 2px;">:</div>
                        <div style="padding: 2px;">-</div>
                    </div>
                </div>
                <div style="width: 50%; vertical-align: top; padding: 6px; box-sizing: border-box;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 5px;">
                        <?= __('Remarks / Instructions'); ?>
                    </div>
                    <div style="font-size: 10px;">-</div>
                </div>
            </div>
        </div>

        <!-- FOOTER SECTION -->
        <div style="padding: 5px 10px 10px 10px; display: flex; justify-content: space-between; align-items: flex-start; box-sizing: border-box;">
            <!-- Left: Acknowledgment Box -->
            <div style="width: 35%; vertical-align: top; border: 1px solid #cccccc; padding: 6px; box-sizing: border-box; font-size: 10px;">
                <div style="font-weight: bold; font-size: 10px; margin-bottom: 12px;">
                    <?= __('Received the above goods in good condition'); ?>
                </div>
                <div style="display: grid; grid-template-columns: 30% 5% 65%; row-gap: 8px;">
                    <div style="padding: 2px 4px;"><?= __('Name'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="border-bottom: 1px solid #cccccc; padding: 2px 4px;">&nbsp;</div>

                    <div style="padding: 2px 4px;"><?= __('Designation'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="border-bottom: 1px solid #cccccc; padding: 2px 4px;">&nbsp;</div>

                    <div style="padding: 2px 4px;"><?= __('Signature'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="border-bottom: 1px solid #cccccc; padding: 2px 4px;">&nbsp;</div>

                    <div style="padding: 2px 4px;"><?= __('Date'); ?></div>
                    <div style="padding: 2px;">:</div>
                    <div style="border-bottom: 1px solid #cccccc; padding: 2px 4px;">&nbsp;</div>
                </div>
            </div>

            <!-- Middle: Schedule & Payment Terms -->
            <div style="width: 28%; vertical-align: top; box-sizing: border-box; display: flex; flex-direction: column; gap: 6px;">
                <div style="border: 1px solid #cccccc; padding: 4px; text-align: center;">
                    <div style="font-weight: bold; font-size: 10px;">
                        <?= __('Delivery Schedule'); ?>
                    </div>
                    <div style="font-size: 10px; margin-top: 4px;">-</div>
                </div>

                <div style="border: 1px solid #cccccc; padding: 4px; text-align: center;">
                    <div style="font-weight: bold; font-size: 10px;"><?= __('Payment Terms'); ?></div>
                    <div style="font-size: 10px; margin-top: 4px;"><?= __('30 DAYS'); ?></div>
                </div>

                <div style="border: 1px solid #cccccc; display: flex; font-size: 10px; text-align: center; box-sizing: border-box;">
                    <div style="width: 50%; border-right: 1px solid #cccccc; padding: 4px; box-sizing: border-box;">
                        02 Sep 2026
                    </div>
                    <div style="width: 50%; padding: 4px; box-sizing: border-box;">
                        &#8377;35,966.40
                    </div>
                </div>
            </div>

            <!-- Right: Signature Box -->
            <div style="width: 33%; vertical-align: top; text-align: center; padding-top: 10px; box-sizing: border-box;">
                <div style="font-weight: bold; font-size: 10px; margin-bottom: 40px;">
                    <?= __('For National Rubber Industries'); ?>
                </div>
                <div style="border-top: 1px solid #cccccc; width: 80%; margin: 0 auto;"></div>
                <div style="font-weight: bold; font-size: 10px; margin-top: 4px;">
                    <?= __('Authorised Signature'); ?>
                </div>
                <div style="font-size: 9px; margin-top: 10px;">
                    <?= __('This is a computer generated document'); ?><br><?= __('no signature required'); ?>
                </div>
                <div style="font-size: 10px; margin-top: 8px;">
                    <?= htmlspecialchars(__('E. & O.E.'), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        </div>

    </div>

</div>

<button type="button" class="print-btn" style="margin: 15px 0; padding: 8px 16px; cursor: pointer;"
    onclick="printDiv('printDiv')"><?= __('Print Invoice'); ?></button>
