<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$crurl = API."?mode=gettabledetailswithconditions&table=currencies";
$cr_data = callAPI($crurl);
$currency = json_decode($cr_data,1);
$dcurr = $_POST['dcurr'];
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
    function addstatickRowsadataq(){
        var count = $(".sfitemRow").length;
        count++;
        var htmlRows = '';

        htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;"><div class="col-lg-3"><select aria-label="From Currency[]" class="form-control form-select js-example-basic-single" name="from_currency[]" id="from_currency_'+count+'"><option value=""> Select Any One</option><?php foreach($currency as $currenc){ ?><option <?php if($dcurr == $currenc[id]){ echo "selected";
} ?> value="<?php echo $currenc[id]; ?>"> <?php echo $currenc[currency_name]." - ".$currenc[currency_short_code]; ?></option><?php
} ?></select></div>';
         htmlRows += '<div class="col-lg-3"><select aria-label="To Currency[]" class="form-control form-select js-example-basic-single" name="to_currency[]" id="to_currency_'+count+'"><option value=""> Select Any One</option><?php foreach($currency as $currenc){ ?><option   value="<?php echo $currenc[id]; ?>"> <?php echo $currenc[currency_name]." - ".$currenc[currency_short_code]; ?></option><?php
} ?></select></div>';
        htmlRows += '<div class="col-lg-3"><input aria-label="0.00" type="text" class="form-control"  id="ex_rate_'+count+'"  value="" placeholder="0.00"  name="ex_rate[]"></div><div class="col-lg-2"><input aria-label="Ex Date[]" type="text"class="form-control datepicker"  id="ex_date_'+count+'"  value=""   name="ex_date[]"></div>';
        htmlRows += '<div class="col-lg-1"><input type="hidden" value="0" id="static_delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deleteRows(this.id)">Delete</button></div></div>';
        console.log(htmlRows);
        $('#append_staticks').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();

    }
</script>
<div class="modal-header card-header">

    <h5 class="text-white">Currency Convertor</h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

<div class="modal-body" style="overflow-y: scroll;" >
    <form class="kt-form" action="" method="post">
        <div class="kt-scroll ps ps--active-x ps--active-y" data-scroll="true" data-scroll-y="true" style="max-height: 400px;">

                <div id='append_statick'>
                    <div class="row align-items-center" style="margin-top: 10px;">
                        <div class="col-lg-3">
                            <span><b>From Currency</b></span>
                        </div>
                        <div class="col-lg-3">
                            <span><b>To Currency</b></span>
                        </div>
                        <div class="col-lg-3">
                            <span><b>Exachange Rate </b></span>
                        </div>
                        <div class="col-lg-2">
                            <span><b>Date</b></span>
                        </div>
                        <div class="col-lg-1">
                            <span><b></b></span>
                        </div>
                    </div>
                    <?php $i=1;?>
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10" id="append_staticks">
                        <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                            <div class="col-lg-3">
                                <select aria-label="From Currency[]" class="form-control form-select js-example-basic-single" name="from_currency[]" id="from_currency_<?php echo $i; ?>">
                                    <option value=""> Select Any One</option>
                                    <?php foreach($currency as $currenc){ ?>
                                        <option <?php if($dcurr == $currenc['id']){ echo "selected";
} ?> value="<?php echo $currenc['id']; ?>"> <?php echo $currenc['currency_name']." - ".$currenc['currency_short_code']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select aria-label="To Currency[]" class="form-control form-select js-example-basic-single" name="to_currency[]" id="to_currency_<?php echo $i; ?>">
                                    <option value=""> Select Any One</option>
                                    <?php foreach($currency as $currenc){ ?>
                                        <option   value="<?php echo $currenc['id']; ?>"> <?php echo $currenc['currency_name']." - ".$currenc['currency_short_code']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <input aria-label="Input field" type="text" class="form-control text-right"  id="ex_rate_<?php echo $i; ?>"  value=""  name="ex_rate[]" placeholder="0.00">
                            </div>
                            <div class="col-lg-2">
                                <input aria-label="Input field" type="text"class="form-control datepicker"  id="ex_date_<?php echo $i; ?>"  value="<?php echo fromDate($fromdate); ?>"   name="ex_date[]">
                            </div>
                            <div class="col-lg-1">
                                <input type="hidden" value="0" id="static_delete_value_<?php echo $i; ?>" >
                                <button class="btn btn-warning" id="<?php echo $i; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row align-items-center m-l-5 mt-4  col-lg-2" >
                    <button class="btn btn-success" id="addstatickRows" type="button" onclick="addstatickRowsadataq()">+ Add More</button>&nbsp;&nbsp;
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
    </div>

</form>
</div>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script><!-- Switch component js -->
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>

