                        <div class="col-md-4">
                            <label class="form-label"><?= __('RT Number '.$fieldDisplay['rt_number']['star']) ?></label>
                            <input type="text" class="form-control" name="rt_number" placeholder="<?= __('Enter RT Number') ?>" value="<?= htmlspecialchars($formData['rt_number'] ?? '') ?>" <?= $fieldDisplay['rt_number']['required'] ?> <?= $amc_renew_readonly ?? '' ?> aria-label="<?= __('Enter RT Number') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= __('Invoice Number '.$fieldDisplay['invoice_number']['star']) ?></label>
                            <input type="text" class="form-control" name="invoice_number" placeholder="<?= __('Enter Invoice Number') ?>" value="<?= htmlspecialchars($formData['invoice_number'] ?? '') ?>" <?= $fieldDisplay['invoice_number']['required'] ?> aria-label="<?= __('Enter Invoice Number') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= __('Business Code '.$fieldDisplay['business_code']['star']) ?></label>
                            <input type="text" class="form-control" name="business_code" placeholder="<?= __('Enter Business Code') ?>" value="<?= htmlspecialchars($formData['business_code'] ?? '') ?>" <?= $fieldDisplay['business_code']['required'] ?> aria-label="<?= __('Enter Business Code') ?>">
                        </div>
