<?php $from = fromDate($role['from_date']);
     $to= toDate($role['to_date']); ?>
</div>
<div class="row">
<div class="col-lg-6 col-xs-12 form-group m-b-10">
    <span>From Date <span class="text-danger">*</span></span>
      <input aria-label="From Date" class="form-control startdatepicker_1  rangedatepicker" data-id="1" type="text" name="from_date" value="<?php echo $from; ?>"  <?php  if( $role['current_status'] == 0 ||  $role['current_status'] == 1) { ?> readonly     <?php
} ?> required /><div class="d-none text-danger" id="<?php echo 'from_date_err'; ?>"> Please Enter From Date </div>
</div>
<div class="col-lg-6 col-xs-12 form-group" >
    <span>To Date <span class="text-danger">*</span></span>
    <input aria-label="To Date" class="form-control enddatepicker_1" type="text"  name="to_date" value="<?php echo $to; ?>"  <?php if( $role['current_status'] == 1) { ?> readonly id="" <?php
} ?> /><div class="d-none text-danger" id="<?php echo 'to_date_err'; ?>"> Please Enter To Date </div>
</div>


</div>

<div class="card-footer p-0">
    <div class="row">

        <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
        <div class="col-lg-9 col-md-6 col-sm-6 col-6">
            <input type="hidden" name="hid" value=<?php echo $rid; ?> />
            <input type="hidden" name="submit" value="Update" />
            <button id="kt_llogin_signup_submit" <?php if($status_res == '1'){ ?> disabled="disabled" <?php
} ?> class="btn btn-primary mr-2 btn_mobile" >
                <i class="la la-check"></i>Update</button>

            <?php } else{ ?>
            <div class="col-lg-9">
                <input type="hidden" name="submit" value="Add" />
                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Save & Exit</button>
                <?php if($_POST['option'] ==""){ ?>
                <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & Continue</button>
                <?php } ?>
                <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2 btn_mobile"><i class="la la-close"></i>Cancel</button>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
</div>
</div>
</div>
</form>

</div>
</div>
</div>
</div>
</div>

