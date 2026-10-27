
<script>
        $(document).ready(function(){
            consol
            $(".open-detail").click(function(){
                $(".close-row").toggle('slow');
                });
            $(".open-detail-2").click(function(){
                $(".close-row-2").toggle('slow');
                });
            $(".open-detail-3").click(function(){
                $(".close-row-3").toggle('slow');
                });
            $(".open-detail-4").click(function(){
                $(".close-row-4").toggle('slow');
                });
            });

    </script>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
            <?php echo $content; ?>
        </div>
    </div>
    <?php } ?>
    <div class="kt-portlet">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    <?php echo "General Ledger";  ?>
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <a href="#" class="btn btn-clean btn-icon-sm">
                        <i class="la la-long-arrow-left"></i>
                        Back
                    </a>
                    &nbsp;


                </div>
            </div>
        </div>
        <div class="kt-portlet__body">

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="account-table">

            <div class="table-account">

                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">ACCOUNT</th>
                        <th scope="col">DEBIT</th>
                        <th scope="col">CREDIT</th>
                        <th scope="col">BALANCE</th>
                    </tr>
                    <tr class="open-row">
                        <th scope="col" style="width: 40px;"><a class="open-detail" href="#"><i style="" class="fa fa-caret-right"></i></a></th>
                        <th scope="col">Accounts Payble</th>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                    </tr>
                    <tr style="display: none;">
                        <td colspan="5">
                            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1_2">
                                <tr>
                                    <th scope="col" style="width: 635px;">Account</th>
                                    <th scope="col">Debit</th>
                                    <th scope="col">Credit</th>
                                    <th scope="col">Balance</th>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" style="width: 40px;"><a class="open-detail-2" href="#" ><i style="" class="fa fa-caret-right"></i></a></th>
                        <th scope="col">Accounts Receivable</th>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <table class="close-row-2">
                                <tr style="background-color: #ccc;">
                                    <th scope="col" style="width: 635px;">Account</th>
                                    <th scope="col">Debit</th>
                                    <th scope="col">Credit</th>
                                    <th scope="col">Balance</th>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" style="width: 40px;"><a class="open-detail-3" href="#"><i style="" class="fa fa-caret-right"></i></a></th>
                        <th scope="col">Advance Tax</th>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <table class="close-row-3">
                                <tr style="background-color: #ccc;">
                                    <th scope="col" style="width: 635px;">Account</th>
                                    <th scope="col">Debit</th>
                                    <th scope="col">Credit</th>
                                    <th scope="col">Balance</th>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" style="width: 40px;"><a class="open-detail-4" href="#"><i style="" class="fa fa-caret-right"></i></a></th>
                        <th scope="col">Advertising & Marketing</th>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <table class="close-row-4">
                                <tr style="background-color: #ccc;">
                                    <th scope="col" style="width: 635px;">Account</th>
                                    <th scope="col">Debit</th>
                                    <th scope="col">Credit</th>
                                    <th scope="col">Balance</th>
                                </tr>
                                <tr>
                                    <th scope="col">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 635px;">Accounts Payble</th>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
