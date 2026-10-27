<?php
if($_POST['submit'] == 'go'){
 $from = date('Y-m-d',strtotime($_POST['from']));
 $to = date('Y-m-d',strtotime($_POST['to']));
 $account_code = implode(",", (array)$_POST['account_code']);
 $tablecat = API."?mode=togetgenralledgerlistdata&from=".$from."&to=".$to."&account_code=".$account_code;
 $textdata = callAPI($tablecat);

}else{
    $textdata =  json_encode($data,1);
    $tablecat = API."?mode=togetgenralledgerlistdata";
    $textdata = callAPI($tablecat);
}
$itemurl = API."?mode=gettabledetails&table=".TBL_CHART_OF_ACCOUNTS;
$item_data = callAPI($itemurl);
$selectdata = json_decode($item_data,1);

?>

<!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

                            <div class="kt-portlet kt-portlet--mobile">
                                <div class="kt-portlet__head kt-portlet__head--lg">
                                    <div class="kt-portlet__head-label">
                                        <span class="kt-portlet__head-icon">
                                            <i class="kt-font-brand flaticon2-line-chart"></i>
                                        </span>
                                        <h3 class="kt-portlet__head-title">
                                            General Ledger
                                        </h3>
                                    </div>
                                    <div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                             <a target="_blank" href="index.php?option=COM_QO_PRINT&rid=<?php echo $rid; ?>&table=<?php echo $table_name; ?>&title=<?php echo urlencode($title); ?>" class="btn btn-clean kt-margin-r-10"> <i class="flaticon2-print"></i></a>
                                            <a href="#" class="btn btn-clean btn-icon-sm">
                                                <i class="la la-long-arrow-left"></i>
                                                Back
                                            </a>
                                            &nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <form name="myForm" id="myForm" action="" method="post" enctype="multipart/form-data">

                                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                                        <div class="row align-items-center">
                                            <div class="col-xl-12 order-2 order-xl-1">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                                        <div class="kt-input-icon kt-input-icon--left">
                                                            <input aria-label="Search..." type="text" class="form-control" placeholder="Search..." id="generalSearch">
                                                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                                                <span><i class="la la-search"></i></span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                                        <div class="kt-form__group kt-form__group--inline">
                                                            <div class="kt-form__label">
                                                                <span>Account</span>
                                                            </div>
                                                            <div class="kt-form__control">
                                                                <select aria-label="Account Code[]" class="form-control kt-select2" id="kt_select2_3" name="account_code[]" multiple="multiple">
                                                                    <option value=""> Select Any one </option>
                                                                    <?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
                                                                            <option <?php if(in_array($selectkey, $mulcheck)){ ?> selected <?php
} ?> value="<?php echo $selectvalue['account_code']; ?>"><?php echo $selectvalue['account_name']; ?></option>
                                                                    <?php  } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                                        <div class="kt-form__group kt-form__group--inline">
                                                            <div class="kt-form__label">
                                                                <span>Dates:</span>
                                                            </div>
                                                            <div class="kt-form__control">
                                                                <div class="input-daterange input-group" id="kt_datepicker_5">
                                                                    <input aria-label="From" type="text" class="form-control kt-input" name="from" autocomplete="off" placeholder="From" data-col-index="5" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                                    </div>
                                                                    <input aria-label="To" type="text" class="form-control kt-input" autocomplete="off"  name="to" placeholder="To" data-col-index="5" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                                        <div class="kt-form__group kt-form__group--inline">
                                                            <input type="hidden" name="submit" value="go" />
                                                            <button class="btn btn-primary btn-brand--icon" id="kt_search">
                                                                <span>
                                                                    <i class="la la-arrow-circle-o-right"></i>

                                                                    <span>GO</span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            </form>
                                            <input type="hidden" name="" id='textdata' value='<?php echo $textdata; ?>'>

                                        </div>
                                    </div>


                                </div>
                                <div class="kt-portlet__body kt-portlet__body--fit">

                                    <!--begin: Datatable -->
                                    <div class="kt-datatable" id="child_data_local"></div>

                                    <!--end: Datatable -->
                                </div>
                            </div>
                        </div>

                        <!-- end:: Content -->
                    </div>
</div>
<script src="assets/js/pages/crud/metronic-datatable/child/data-local.js" type="text/javascript"></script>
