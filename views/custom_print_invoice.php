

<head>
    <title><?= __('Tax Invoice'); ?></title>
    <style type="text/css">
        @media (orientation: portrait) {
            .container {width: 100%; border: none; padding: 0;}
        }
        @print{
            .container{100%;padding:0;border:none;}
        }
        td, th{font-size: 13px; padding: 5px !important; vertical-align: middle;white-space: nowrap;}
        @media (max-width:772px){
            .scan{width: 36%;}
        }
        table{
            border-collapse : collapse;
        }
    </style>

  <script>
        function printDiv(divName) {
             var printContents = document.getElementById(divName).innerHTML;
             var originalContents = document.body.innerHTML;
             document.body.innerHTML = printContents;

             window.print();

             document.body.innerHTML = originalContents;
        }
    </script>

</head>

<style>#printableArea table td, #printableArea table th { padding: 9px; }</style>
<div class="" id="printableArea">
    <table style="width:100%; border-collapse:collapse; margin:0 auto; font-size:14px; border: 1px solid #cccccc; background: #ffffff;">
        <tr>
            <td colspan="7" style="font-size:24px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                <img src="assets/images/logo.png" alt="<?= htmlspecialchars(__('Logo'), ENT_QUOTES, 'UTF-8'); ?>">
                <h5 style="font-size:16px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('eMudhra Limited'); ?></h5>
                <p style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                    <?= __('eMudhra Digital Campus'); ?><br />
                    <?= __('Plot No.12 P1 A and 12 P1 B Corner, Sy.No.95P and 7.'); ?><br />
                    <?= __('Block No.21, 22 and 24 Part, Bangalore IT Park Industrial Area,'); ?><br />
                    <?= __('B K Palya Village, Jala Hobli, Bangalore North,'); ?><br />
                    <?= __('Bengaluru Urban, Karnataka, 562149.'); ?>
                </p>
            </td>
            <td colspan="6" style="text-align: right;">
                <img style="width:200px;" src="assets/images/qrcode.png" alt="<?= htmlspecialchars(__('QR Code'), ENT_QUOTES, 'UTF-8'); ?>">
            </td>
        </tr>
        <tr>
            <td colspan="13" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border: 1px solid #cccccc; text-align: center;"><?= __('TAX - INVOICE'); ?></td>
        </tr>
        <tr>
            <td colspan="13" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border: 1px solid #cccccc;"><?= __('IRN - 53e3cbbca1203996279fd623c5615bf66db242a103ad1d89a75f2cc26198daff'); ?>
          </td>
        </tr>
        <tr>
            <td colspan="7" style="border: 1px solid #cccccc;">
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Billed To'); ?>:</span>
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;"><?= __('Infisuite Technologies LLP'); ?></span>
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;"><?= __('Contact Person: Naresh Audukoori'); ?></span>
                <p style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;display: block;margin: 0;"><?= __('812 Block-5, Manasarovar Heights -2, Tirumalagiri, Hyderabad,'); ?><br /><?= __('Telangana'); ?></p>
            </td>
            <td colspan="6" style="border: 1px solid #cccccc;padding: 0 !important;">
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Invoice No'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">29RT250905465</span>
                </div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Invoice Date'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">16-Sep-2025</span>
                </div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Customer GSTIN'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('36AAHFI6877Q1ZC'); ?></span>
                </div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Customer State Code'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('Telangana (TS 36)'); ?></span>
                </div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Place of Supply'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('Telangana'); ?></span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="7" style="border: 1px solid #cccccc;">
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Delivered To'); ?>:</span>
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;"><?= __('Infisuite Technologies LLP'); ?></span>
                <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;"><?= __('Contact Person: Naresh Audukoori'); ?></span>
                <p style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;display: block;margin: 0;"><?= __('812 Block-5, Manasarovar Heights -2, Tirumalagiri, Hyderabad,'); ?><br><?= __('Telangana'); ?></p>
            </td>
            <td colspan="6" style="border: 1px solid #cccccc;padding: 0 !important;">
                <div style="border-bottom: 1px solid #eeeeee; padding: 7px 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Buyer\'s Order no'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">-</span>
                </div>
                <div style="padding: 7px 5px !important;">
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Order Dated'); ?>:</span>
                    <span style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">-</span>
                </div>
            </td>
        </tr>
        <!-- Header Row 1 -->
        <tr style="background-color: #cccccc;">
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Sl.No'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Description of Goods/Services'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('SAC/HSN'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Quantity'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('UOM'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Rate'); ?></td>
            <td rowspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Gross Amount'); ?></td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('IGST'); ?></td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('CGST'); ?></td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('SGST'); ?></td>
        </tr>
        <!-- Header Row 2 -->
        <tr style="background-color: #cccccc;">
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Rate'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Amount'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Rate'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Amount'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Rate'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize; text-align: center;"><?= __('Amount'); ?></td>
        </tr>

        <!-- Data Row 1 -->
        <tr style="">
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">1</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;white-space: normal;"><?= __('eMudhra Secure SSL - emSign - SSL/TLS - DV Wildcard 1 Year Subscription'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">998399</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">10</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('Nos'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">6,150</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">6,150</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
        </tr>

        <!-- Data Row 2 -->
        <tr style="">
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">2</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;white-space: normal;"><?= __('eMudhra Secure SSL - emSign - SSL/TLS - DV Wildcard 1 Year Subscription'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">998399</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">10</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('Nos'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">6,150</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">6,150</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: center;">18%</td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize; text-align: right;">1,107</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: center;"><?= __('Total'); ?></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align: center;"></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align: center;"></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align: center;"></td>
            <td style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align: center;"></td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: right;">1,107</td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: right;">1,107</td>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: right;">1,107</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: center;"><?= __('Amount in words'); ?>:</td>
            <td colspan="4" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';white-space:normal;font-weight:600;text-transform:capitalize;"><?= __('Rupees Seven Thousand Two Hundred and Fifty Seven Only'); ?></td>
            <td colspan="4" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: right;padding: 0 !important;">
                <div style="border: 1px solid #eeeeee; padding: 5px !important;"><?= __('Gross Value'); ?></div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;"><?= __('Total Tax'); ?></div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;"><?= __('Total Invoice value'); ?></div>
            </td>
            <td colspan="3" style="border: 1px solid #eeeeee;font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: right;padding: 0 !important;">
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">6,150</div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">1,107</div>
                <div style="border: 1px solid #eeeeee; padding: 5px !important;">7,257</div>
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="6" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align: center;"><?= __('For eMudhra Limited'); ?></td>
        </tr>
        <tr>
            <td colspan="13">
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;"><?= __('Invoice Remarks'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;display: block;">
                    <?= __('Order No: 6535585453 | Domain Name: *.infisuite.in'); ?>
                </div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;margin-top: 10px;"><?= __('Declaration'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;display: block;">
                    <?= __('We declare that this invoice shows the actual price of the products/services described and that all particulars are true and correct.'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;margin-top: 10px;"><?= __('Note'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;display: block;">
                    <?= __('The services provided by us is in the nature of technical services as per explanation 2 to clause'); ?><br />
                    <?= __('(vii) of sub-section (1) of section 9 and the applicable TDS rate would be 2%.'); ?>
                </div>
            </td>
            <td colspan="6" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"><?= __('Signature'); ?></td>
        </tr>
        <tr>
            <td colspan="7">
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;display: block;margin-top: 10px;">E.&OE.</div>
            </td>
            <td colspan="6" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?= __('Authorized Signatory'); ?></td>
        </tr>
        <tr>
            <td colspan="13" style="text-align:center;border: 1px solid #cccccc;">
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;display: inline-block;"><?= __('eMudhra Limited, CIN'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;display: inline-block;"><?= __('L72900KA2008PLC060368,'); ?></div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;display: inline-block;"><?= __('PAN'); ?></div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;display: inline-block;"><?= __('AAACZ3347G,'); ?></div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;display: inline-block;"><?= __('GSTIN'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;display: inline-block;"><?= __('29AAACZ3347G1Z5,'); ?></div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:600;display: inline-block;"><?= __('State'); ?>:</div>
                <div style="font-size:14px;font-family:'Tahoma';font-weight:500;display: inline-block;"><?= __('Karnataka (Code: 29)'); ?></div>
            </td>
        </tr>
    </table>
</div>

<button style="margin-top: 20px; width: 200px;" type="button" class="btn btn-primary font-weight-bolder" onclick="printDiv('printableArea')" ><?= __('Print Payslip'); ?></button>

