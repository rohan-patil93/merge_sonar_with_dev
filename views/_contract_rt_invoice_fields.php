                        <div class="col-md-4">
                            <span class="form-label d-inline-block fw-bold"><?= __('RT Number '.$fieldDisplay['rt_number']['star']) ?></span>
                            <input type="text" class="form-control" name="rt_number" placeholder="<?= __('Enter RT Number') ?>" value="<?= htmlspecialchars($formData['rt_number'] ?? '') ?>" <?= $fieldDisplay['rt_number']['required'] ?> <?= $amc_renew_readonly ?? '' ?> aria-label="<?= __('Enter RT Number') ?>">
                        </div>
                        <div class="col-md-4">
                            <span class="form-label d-inline-block fw-bold"><?= __('Invoice Number '.$fieldDisplay['invoice_number']['star']) ?></span>
                            <input type="text" class="form-control" name="invoice_number" placeholder="<?= __('Enter Invoice Number') ?>" value="<?= htmlspecialchars($formData['invoice_number'] ?? '') ?>" <?= $fieldDisplay['invoice_number']['required'] ?> aria-label="<?= __('Enter Invoice Number') ?>">
                        </div>
                        <div class="col-md-4">
                            <span class="form-label d-inline-block fw-bold"><?= __('Business Code '.$fieldDisplay['business_code']['star']) ?></span>
                            <input type="text" class="form-control" name="business_code" placeholder="<?= __('Enter Business Code') ?>" value="<?= htmlspecialchars($formData['business_code'] ?? '') ?>" <?= $fieldDisplay['business_code']['required'] ?> aria-label="<?= __('Enter Business Code') ?>">
                        </div>
