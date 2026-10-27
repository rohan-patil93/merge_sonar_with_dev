 <link rel="stylesheet" type="text/css" href="assets/css/invoice.css">

<style type="text/css">
        @media print{
            @page{size: landscape;}
            .invoice-2 table tr th, .invoice-2 table tr td{white-space: inherit !important;}
            .invoice-2 table tr{background-color: transparent !important !important;}


            .invoice-headar{background: none !important;}
            .invoice-2 .invoice-top, .invoice-2 .invoice-center, .invoice-2 .order-summary, .invoice-2 .account-transfer{
                border: none !important; padding: 20px !important;
            }
        }
    </style>
    <script type="text/javascript">
   function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html lang=\"en\"><head><link rel='stylesheet' type='text/css' href='https:
       window.print();
       document.body.innerHTML = originalContents;
   }
</script>
<div class="invoice-2 invoice-content" id="printDiv">

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner-4">
                    <div class="invoice-info" id="invoice_wrapper">
                        <div class="invoice-inner">
                            <div class="invoice-top">
                                <table style="width: 100%;">
                                    <tr>
                                        <th scope="col">
                                            <h3 class="inv-header-2">Invoice: #943249</h3>
                                        </th>
                                        <th scope="col" style="text-align: right;">
                                            <div class="logo">
                                                <a href="#">
                                                    <img src="uploads/all/file_1621841737_60ab5749e72dfnm.png" alt="logo">
                                                </a>
                                            </div>
                                            <p>Bandung, West Java, Indonesia<br>Fax 621113</p>
                                        </th>
                                    </tr>
                                    <tr>

                                    </tr>
                                </table>
                            </div>
                            <div class="invoice-center">
                                <table style="width: 100%;">
                                    <tr>
                                        <th scope="col">
                                            <address class="address-info">
                                                <p class="strong"><b>Date</b></p>
                                                <p>Invoice Data: Aug 27, 2020</p>
                                                <p>Due Data: Aug 27, 2020</p>
                                            </address>
                                        </th>
                                        <th scope="col">
                                           <address class="address-info">
                                                <p class="strong"><b>Invoice To</b></p>
                                                <p class="invo-addr-1">
                                                    Theme Vessel <br/>
                                                    info@themevessel.com <br/>
                                                    21-12 Green Street, Meherpur, Bangladesh <br/>
                                                </p>
                                            </address>
                                        </th>
                                        <th scope="col">
                                            <address class="address-info ai2 text-end">
                                                <p class="strong"><b>Bill To</b></p>
                                                <p class="invo-addr-1">
                                                    Apexo Inc  <br/>
                                                    billing@apexo.com <br/>
                                                    169 Teroghoria, Bangladesh <br/>
                                                </p>
                                            </address>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                            <div class="order-summary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Order summary</h3>
                                        <div class="table-responsive">
                                            <table class="table invoice-table">
                                                <thead class="bg-active">
                                                <tr>
                                                    <th scope="col">Item Item</th>
                                                    <th scope="col" class="text-center">Price</th>
                                                    <th scope="col" class="text-center">Quantity</th>
                                                    <th scope="col" class="text-right">Totals</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="item-desc-1">
                                                            <span>BS-200</span>
                                                            <small>Customize web application</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">$10.99</td>
                                                    <td class="text-center">1</td>
                                                    <td class="text-right">$10.99</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="item-desc-1">
                                                            <span>BS-201</span>
                                                            <small>Website development and customization for themevessel</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">$20.00</td>
                                                    <td class="text-center">3</td>
                                                    <td class="text-right">$60.00</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="item-desc-1">
                                                            <span>BS-233</span>
                                                            <small>Website SEO improvement of themevessel.com</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">$640.00</td>
                                                    <td class="text-center">1</td>
                                                    <td class="text-right">$640.00</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end">SubTotal</td>
                                                    <td class="text-right">$710.99</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end">Tax</td>
                                                    <td class="text-right">$85.99</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                                    <td class="text-right fw-bold">$795.99</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="account-transfer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="bank-transfer">
                                            <h3 class="inv-title-1">Bank Transfer</h3>
                                            <ul class="bank-transfer-list-1">
                                                <li><strong>Account Name:</strong>Syamsuddin</li>
                                                <li><strong>Account Number:</strong>1234567890934</li>
                                                <li><strong>Code:</strong>BARC0032UK</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="total-amount text-end">
                                            <h3 class="inv-title-1">Total Amount</h3>
                                            <h1>$795.99</h1>
                                            <p class="mb-0">Taxes Included</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="note">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4 class="inv-title-1">Notes</h4>
                                        <p class="text-muted">We really appreciate your business and if there's anything else we can do, please let us know! Also, should you need us to add VAT or anything else to this order!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-btn-section clearfix d-print-none">
                        <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
                            <i class="fa fa-print"></i> &nbsp Print Invoice
                        </button>
                        <button class="btn waves-effect waves-light btn-grd-info" onclick="printDiv('printDiv');">
                            <i class="fa fa-download"></i> &nbsp Download Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
