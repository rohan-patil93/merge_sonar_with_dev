<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$crurl = API."?mode=gettabledetailswithconditions&table=earnings ";
$cr_data = callAPI($crurl);
$earnings = json_decode($cr_data,1);

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
    function addstatickRowsadataq(){
        var count = $(".sfitemRow").length;
        count++;
        var htmlRows = '';

        htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;"><div class="col-lg-3"><select aria-label="Earning Component []" class="form-control form-select js-example-basic-single" name="earning_component_id[]" id="earning_component_id_'+count+'"><option value=""> Select Any One</option><?php foreach($earnings as $earn){ ?><option  value="<?php echo $earn['id']; ?>"> <?php echo $earn['name']; ?></option><?php
} ?></select></div>';
         htmlRows += '<div class="col-lg-3"><input aria-label="Transaction Date[]" type="text"class="form-control datepicker"  id="transaction_date_'+count+'"  value=""   name="transaction_date[]"> </div>';
        htmlRows += '<div class="col-lg-3"><input aria-label="0.00" type="text" class="form-control text-right"  id="amount_'+count+'"  value=""  name="amount[]" placeholder="0.00"> </div><div class="col-lg-2"><textarea aria-label="Comments" name="comments" ></textarea></div>';
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
                            <span><b>Earning Component</b></span>
                        </div>
                        <div class="col-lg-3">
                            <span><b>Transaction Date</b></span>
                        </div>
                        <div class="col-lg-3">
                            <span><b>Amount</b></span>
                        </div>
                        <div class="col-lg-2">
                            <span><b>Comments</b></span>
                        </div>
                        <div class="col-lg-1">
                            <span><b></b></span>
                        </div>
                    </div>
                    <?php $i=1;?>
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10" id="append_staticks">
                        <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                            <div class="col-lg-3">
                                <select aria-label="Earning Component []" class="form-control form-select js-example-basic-single" name="earning_component_id[]" id="earning_component_id_<?php echo $i; ?>">
                                    <option value=""> Select Any One</option>
                                    <?php foreach($earnings as $earn){ ?>
                                        <option <?php if($data['earning_component_id'] == $earn['id']){ echo "selected";
} ?> value="<?php echo $earn['id']; ?>"> <?php echo $earn['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php $fromdate = $data['transaction_date']!="" ? $data['transaction_date'] : date("Y-m-d") ; ?>
                            <div class="col-lg-3">
                                <input aria-label="Input field" type="text"class="form-control datepicker"  id="transaction_date_<?php echo $i; ?>"  value="<?php echo fromDate($fromdate); ?>"   name="transaction_date[]">
                            </div>
                            <div class="col-lg-3">
                                <input aria-label="Input field" type="text" class="form-control text-right"  id="amount_<?php echo $i; ?>"  value="<?php echo $data['amount']; ?>"  name="amount[]" placeholder="0.00">
                            </div>
                            <div class="col-lg-2">
                                <textarea aria-label="Comments" name="comments" ><?php echo $data['comments']; ?></textarea>
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

