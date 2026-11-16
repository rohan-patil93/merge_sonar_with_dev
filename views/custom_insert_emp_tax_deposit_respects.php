<?php
$url = API."?mode=togetinsertemptaxdepositdetails";
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$_SESSION['apply_js']['multiselest'] = 1;
$selectdata = array('1'=>'Content', '2'=>'File');
$selectdatas = array('1'=>'Single Row ', '2'=>'Multiple Rows (Import file)');
?>
<form method="post">
<div class="row p-30" >
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5><?= __('Employee Tax Deposit Respect') ?></h5>
                <div class="card-header-right table-card-header">
                </div>
            </div>
            <div class="card-body">    784291680
                <div class="row col-lg-12">
                    <div class="form-group col-lg-6">
                        <span class="form-label"><?= __('Document Type') ?></span>
                        <select aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>" name="type" id="type" class="form-control form-select js-example-basic-single">
                            <option value="0"><?= __('Select Any One') ?></option>
                            <?php foreach($data['document_lists'] as $document_lists) {  ?>
                                <option value="<?php echo $document_lists['id']; ?>" <?php echo $document_lists['id'] ==$row['type']?"selected":""; ?>><?php echo $document_lists['document_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label"><?= __('Assessment Year') ?></span>
                        <select aria-label="<?= htmlspecialchars(__('Assessment Year'), ENT_QUOTES, 'UTF-8'); ?>" name="assessment_year" id="assessment_year" class="form-control form-select js-example-basic-single">
                            <option value="0"><?= __('Select Any One') ?></option>
                            <?php $sty = date('Y')-4;
                                for($i=1; $i<6; $i++) {
                                    $vew = $sty."-".($sty+1);
                            ?>
                                <option value="<?php echo $vew; ?>" <?php echo $vew ==$row['assessment_year']?"selected":""; ?>><?php echo $$vew; ?></option>
                            <?php $sty++;
} ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label"><?= __('Upload Type') ?></span>
                        <div class="radio radio-outline  mt-1">
                            <?php foreach ($selectdata as $selectkey => $selectvalue) {   ?>
                                <label class="radio radio-inline"> <input aria-label="<?= htmlspecialchars(__('Radio option'), ENT_QUOTES, 'UTF-8'); ?>" type="radio"  <?php if($selectkey == $role[$key]){ ?>checked <?php
} ?> name="upload_type" value='<?php echo $selectkey; ?>'>    <span></span><?php echo $selectvalue; ?> </label>
                            <?php $sch++;
} ?>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label"><?= __('Insert Type') ?></span>
                        <div class="radio radio-outline  mt-1">
                            <?php foreach ($selectdatas as $selectkey => $selectvalue) {   ?>
                                <label class="radio radio-inline"> <input aria-label="<?= htmlspecialchars(__('Radio option'), ENT_QUOTES, 'UTF-8'); ?>" type="radio"  <?php if($selectkey == $role[$key]){ ?>checked <?php
} ?> name="insert_type" value='<?php echo $selectkey; ?>'>    <span></span><?php echo $selectvalue; ?> </label>
                            <?php $sch++;
} ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <span class="form-label"><?= __('Employee') ?></span>
                            <select aria-label="<?= htmlspecialchars(__('Emp'), ENT_QUOTES, 'UTF-8'); ?>" name="emp_id" id="emp_id" class="form-control form-select js-example-basic-single">
                            <option value="0"><?= __('Select Any One') ?></option>
                            <?php foreach($data['employees'] as $employee) {  ?>
                                <option value="<?php echo $employee['id']; ?>" <?php echo $employee['id'] ==$row['emp_id']?"selected":""; ?>><?php echo $employee['display_name'];?></option>
                            <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Certificate No'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Certificate No'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="certificate_no" name="certificate_no" value="">
                            <div style="display: none;color: red;" id="certificate_no_err"> <?= __('Please enter Certificate No'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Certificate Date'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Certificate Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker " type="text" value="" name="certificate_date" id="certificate_date">
                        </div>
                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Bsr Code'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Bsr Code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="bsr_code" name="bsr_code" value="">
                            <div style="display: none;color: red;" id="bsr_code_err"> <?= __('Please enter Bsr Code'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Challan Serial No'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Challan Serial No'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="challan_serial_no" name="challan_serial_no" value="">
                            <div style="display: none;color: red;" id="challan_serial_no_err"> <?= __('Please enter Challan Serial No'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Deposit (Date)'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Deposit Amont Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker " type="text" value="" name="deposit_amont_date" id="deposit_amont_date">
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Tax Deposit Date'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Tax Deposit Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker " type="text" value="" name="tax_deposit_date" id="tax_deposit_date">
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Oltas'); ?></span>
                            <select aria-label="<?= htmlspecialchars(__('Oltas'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" id="oltas" name="oltas" >
                                <option value="0"><?= __('Select Any One Oltas') ?></option>
                                <option value="U"><?= __('Unmatched') ?></option>
                                <option value="p"><?= __('Provisional') ?></option>
                                <option value="F"><?= __('Final') ?></option>
                                <option value="O"><?= __('Overbooked') ?></option>
                            </select>
                            <div style="display: none;color: red;" id="oltas_err"> <?= __('Please Select Oltas'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Taxble Amount'); ?></span>
                            <div>
                                <input aria-label="<?= htmlspecialchars(__('Taxble Amount'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control text-right" type="text" id="taxble_amount" name="taxble_amount" value="0">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Tax Deducted Amount'); ?></span>
                            <div>
                                <input aria-label="<?= htmlspecialchars(__('Tax Deducted'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control text-right" type="text" id="tax_deducted" name="tax_deducted" value="0">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Tax Deposited Amount'); ?></span>
                            <div>
                                <input aria-label="<?= htmlspecialchars(__('Tax Deposited'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control text-right" type="text" id="tax_deposited" name="tax_deposited" value="0">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Quarterly Receipt No'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Quarterly Receipt No'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="quarterly_receipt_no" name="quarterly_receipt_no" value="">
                            <div style="display: none;color: red;" id="quarterly_receipt_no_err"> <?= __('Please enter Quarterly Receipt No'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Receipt No 24g'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Receipt No 24g'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="receipt_no_24g" name="receipt_no_24g" value="">
                            <div style="display: none;color: red;" id="receipt_no_24g_err"> <?= __('Please enter Receipt No 24g'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class=""><?= __('Ddo No'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Ddo No'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" id="ddo_no" name="ddo_no" value="">
                            <div style="display: none;color: red;" id="ddo_no_err"> <?= __('Please enter Ddo No'); ?> </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span><?= __('Bin Date'); ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Bin Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker " type="text" value="" name="bin_date" id="bin_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer pt-0 mt-3">
                 <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>
            </div>
        </div>
    </div>
</div>

</form>
