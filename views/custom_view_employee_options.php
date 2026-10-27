<style type="text/css">
    *{
    margin:0;
    padding: 0;
    list-style: none;
    text-decoration: none;
}

.pb-5{
    padding-bottom: 5px;
}
.mb-5{
    margin-bottom: 5px;
}
.pb-10{
    padding-bottom: 10px;
}
.mb-10{
    margin-bottom: 10px;
}
.pt-5{
    padding-top: 5px;
}
.mt-5{
    margin-top: 5px;
}
.pt-10{
    padding-top: 10px;
}
.mt-10{
    margin-top: 10px;
}
.text-dark{
    color: #000000;
}
.position-relative{
    position: relative;
}

.box {
    width: 16%;
    display: inline-block;
    vertical-align: top;
    margin-right: 1%;
}
.table{
    margin: 0;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
    border: none; padding: 5px;
}
table tr td, table tr th{
    font-size: 11px;
}
.border-bottom{
    border-bottom:1px solid #cccccc !important;
}
.font-weight-bold{
    font-weight: bold;
}
.border{
    border: 1px solid #eeeeee;
}
.align-center{
    text-align: center;
}
.align-right{
    text-align: right;
}
.align-middle{
    vertical-align: middle !important;
}

.outer-wrapper {
    max-width: 100vw;
    overflow-x: scroll;
    position: relative;
    scrollbar-color: #d5ac68 #f1db9d;
    scrollbar-width: thin;
    -ms-overflow-style: none;
    margin-bottom: 20px;
}
.pseduo-track {
    background-color: #f1db9d;
    height: 2px;
    width: 100%;
    position: relative;
    top: -3px;
    z-index: -10;
}

@media (any-hover: none) {
  .pseduo-track {
    display: none;
  }
}

.outer-wrapper::-webkit-scrollbar {
    height: 5px;
}

.outer-wrapper::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 0px rgba(0, 0, 0, 0);
}
.outer-wrapper::-webkit-scrollbar-thumb {
    height: 5px;
    background-color: #d5ac68;
}
.outer-wrapper::-webkit-scrollbar-thumb:hover {
    background-color: #000000;
}
.outer-wrapper::-webkit-scrollbar:vertical {
    display: none;
}
.inner-wrapper {
    display: flex;
    padding-bottom: 10px;
}
.pseudo-item {
    width: 15.4%;
    margin-right: 1%;
    flex-shrink: 0;
}
.pseudo-item-1{
    position: fixed;
    background-color: #ffffff;
    z-index: 1;
}
.pseudo-item:first-child{
     width: 18%;
}
.pseudo-item:nth-of-type(2n) {
    background-color: #fbfbfb;
}
.pseudo-item:nth-of-type(2) {
    margin-left: 19.3%;
}

.pseudo-item.addwidth{
    width: 6%;
    overflow: scroll;
}

.collapsible-link {
  width: 100%;
  position: relative;
  text-align: left;
}

.collapsible-link::before {
  content: "\f107";
  position: absolute;
  top: 50%;
  right: 0.8rem;
  transform: translateY(-50%);
  display: block;
  font-family: "FontAwesome";
  font-size: 1.1rem;
}

.collapsible-link[aria-expanded="true"]::before {
  content: "\f106";
}
.collapse.in{
    display: none !important;
}
.collapse{
    height: auto !important;
}

.float-right{
    float: right;
}
.fa-plus{
    font-size: 12px;
}
.card a{
    display: block;
}

.hide-box {
    position: absolute;
    right: -7px;
    top: -2px;
}

</style>
<div class="container-fluid">
    <h1>Employee Stock Option Plan (ESOP) Model</h1>
    <div class="outer-wrapper">
        <div class="inner-wrapper">
            <div class="pseudo-item pseudo-item-1">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <td colspan="3">&nbsp</td>
                        </tr>
                        <tr>
                            <td>&nbsp<br />&nbsp</td>
                            <th scope="col" style="text-align: right;">&nbsp<br />&nbsp</th>
                            <th scope="col" style="text-align: right;">&nbsp<br />&nbsp</th>
                        </tr>
                        <tr>
                            <td></td>
                            <th scope="col" style="text-align: right;">Salary</th>
                            <th scope="col" style="text-align: right;">Options<br />Vultiplier</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">Total<br />Non-Founder</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Ownership</th>
                        </tr>
                    </table>

                    <div id="accordionExample" class="accordion mt-5">
                        <!-- Card 1 -->
                        <div class="card">
                            <div id="headingOne" class="font-weight-bold">
                                <a href="#" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                    aria-controls="collapseOne" class="border-bottom pb-5 mb-5 text-dark collapsible-link" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Founding Team
                                </a>
                            </div>
                            <div id="collapseOne" aria-labelledby="headingOne" data-parent="#accordionExample" class="collapse show">
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th scope="col"><a href="">Pradeep Maitray</a></th>
                                            <td style="text-align: right;">40000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Mukesh Kumar</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Rohan Patil</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Ravishankar Tangudu</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="card">
                            <div id="headingTwo" class="font-weight-bold">
                                <a href="#" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo" class="border-bottom pb-5 mb-5 text-dark collapsible-link" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Year 1 Hires
                                </a>
                            </div>
                            <div id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordionExample" class="collapse show">
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th scope="col"><a href="">Pradeep Maitray</a></th>
                                            <td style="text-align: right;">40000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Mukesh Kumar</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Rohan Patil</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Ravishankar Tangudu</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="card">
                            <div id="headingThree" class="font-weight-bold">
                                <a href="#" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                    aria-controls="collapseThree" class="border-bottom pb-5 mb-5 text-dark collapsible-link" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Year 2 Hires
                                </a>
                            </div>
                            <div id="collapseThree" aria-labelledby="headingThree" data-parent="#accordionExample" class="collapse show">
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th scope="col"><a href="">Pradeep Maitray</a></th>
                                            <td style="text-align: right;">40000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Mukesh Kumar</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Rohan Patil</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Ravishankar Tangudu</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="card">
                            <div id="headingFour" class="font-weight-bold">
                                <a href="#" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                                    aria-controls="collapseFour" class="border-bottom pb-5 mb-5 text-dark collapsible-link" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Year 3 Hires
                                </a>
                            </div>
                            <div id="collapseFour" aria-labelledby="headingThree" data-parent="#accordionExample" class="collapse show">
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th scope="col"><a href="">Pradeep Maitray</a></th>
                                            <td style="text-align: right;">40000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Mukesh Kumar</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Rohan Patil</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                        <tr>
                                            <th scope="col"><a href="">Ravishankar Tangudu</a></th>
                                            <td style="text-align: right;">50000rs.</td>
                                            <td style="text-align: right;">1.00x</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pseudo-item border position-relative">
                <div class="hide-box"><i class="fa-solid fa-circle-minus"></i></div>
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 0</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee; text-align: center;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="pseudo-item border">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 1</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="pseudo-item border">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 2</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="pseudo-item border">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 3</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="pseudo-item border">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 4</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="pseudo-item border">
                <div class="table-box">
                    <table class="table">
                        <tr>
                            <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3">Year 5</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Value<br />NA</th>
                            <th scope="col" class="align-center">Shares<br />10000</th>
                            <th scope="col" class="align-center">Shr. Price<br />100</th>
                        </tr>
                        <tr style="text-align: center;">
                            <th scope="col" class="align-center">Grand Total</th>
                            <th scope="col" class="align-center">Total</th>
                            <th scope="col" class="align-center">Equity<br />(%)</th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">&nbsp<br />&nbsp</th>
                            <th scope="col"></th>
                            <th scope="col" class="align-center align-middle">6.50%</th>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                          <th scope="col" class="h5 font-weight-bold border-bottom" colspan="3">&nbsp</th>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr style="text-align: center;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
