                        <div class="col-md-4">
                            <label class="form-label">RT Number <?= $fieldDisplay['rt_number']['star'] ?></label>
                            <input type="text" class="form-control" name="rt_number" placeholder="Enter RT Number" value="<?= htmlspecialchars($formData['rt_number'] ?? '') ?>" <?= $fieldDisplay['rt_number']['required'] ?> <?= $amc_renew_readonly ?? '' ?>>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Invoice Number <?= $fieldDisplay['invoice_number']['star'] ?></label>
                            <input type="text" class="form-control" name="invoice_number" placeholder="Enter Invoice Number" value="<?= htmlspecialchars($formData['invoice_number'] ?? '') ?>" <?= $fieldDisplay['invoice_number']['required'] ?>>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Business Code <?= $fieldDisplay['business_code']['star'] ?></label>
                            <input type="text" class="form-control" name="business_code" placeholder="Enter Business Code" value="<?= htmlspecialchars($formData['business_code'] ?? '') ?>" <?= $fieldDisplay['business_code']['required'] ?>>
                        </div>
