<?php
$contract_agreement_section = $contract_agreement_section ?? '';
?>
<?php if ($contract_agreement_section == 'classification') { ?>
                        <div class="col-md-4" style="<?= $fieldDisplay['typeofaggrements']['style'] ?>">
                            <label class="form-label">Type of Agreements <?= $fieldDisplay['typeofaggrements']['star'] ?></label>
                            <select class="form-control js-example-basic-single" name="typeofaggrements" <?= $fieldDisplay['typeofaggrements']['required'] ?>>
                                <option value="">Select Type of Agreements</option>
                                <?php if (count((array)$genc) > 0) {
                                    foreach ($genc as $item) {
                                        $gi = is_array($item) ? $item : json_decode(json_encode($item), true);
                                        $sel = (isset($formData['typeofaggrements']) && $formData['typeofaggrements'] == ($gi['genc_detail_code'] ?? '')) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($gi['genc_detail_code'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars($gi['genc_detail_name'] ?? '') ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['description_id']['style'] ?>">
                            <label class="form-label">Description <?= $fieldDisplay['description_id']['star'] ?></label>
                            <select class="form-control js-example-basic-single" name="description_id" <?= $fieldDisplay['description_id']['required'] ?>>
                                <option value="">Select Description</option>
                                <?php if (count((array)$des) > 0) {
                                    foreach ($des as $d) {
                                        $dr = is_array($d) ? $d : json_decode(json_encode($d), true);
                                        $sel = (($formData['description_id'] ?? '') == ($dr['id'] ?? '')) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($dr['id'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars($dr['description_type'] ?? '') ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
<?php } if ($contract_agreement_locations_section == 'Y') { ?> 
<div class="col-md-12 " style="<?= $fieldDisplay['locations']['style'] ?>">
                            <label class="form-label">Locations <?= $fieldDisplay['locations']['star'] ?></label>
                            <select name="locations[]" multiple class="form-control js-example-basic-multiple" id="unified_locations_multiselect" <?= $fieldDisplay['locations']['required'] ?>>
                                <option value="0">Select Locations</option>
                                <?php if (count((array)$locations) > 0) {
                                    foreach ($locations as $location) {
                                        $loc = is_array($location) ? $location : json_decode(json_encode($location), true);
                                        $sel = in_array($loc['id'], (array)$selectedLocations) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($loc['id'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars($loc['location_name'] ?? '') ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>

<?php } if ($contract_agreement_section == 'extended') { ?>
                        
                        <div class="col-md-12 cc-amc-only">
                            <p class="text-muted small mb-0"><em>Locations are configured in Step 2 — Locations, Assets &amp; Service Periods.</em></p>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['additional_notes']['style'] ?>">
                            <label class="form-label">Additional Notes <?= $fieldDisplay['additional_notes']['star'] ?></label>
                            <textarea class="form-control" name="additional_notes" rows="2" placeholder="Additional notes"><?= htmlspecialchars($formData['additional_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['termination_condition']['style'] ?>">
                            <label class="form-label">Termination Condition <?= $fieldDisplay['termination_condition']['star'] ?></label>
                            <textarea class="form-control" name="termination_condition" rows="2"><?= htmlspecialchars($formData['termination_condition'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['isp']['style'] ?>">
                            <label class="form-label">ISP <?= $fieldDisplay['isp']['star'] ?></label>
                            <input type="text" class="form-control" name="isp" value="<?= htmlspecialchars($formData['isp'] ?? '') ?>" placeholder="Enter ISP" <?= $fieldDisplay['isp']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['paid_by']['style'] ?>">
                            <label class="form-label">Paid By <?= $fieldDisplay['paid_by']['star'] ?></label>
                            <input type="text" class="form-control" name="paid_by" value="<?= htmlspecialchars($formData['paid_by'] ?? '') ?>" placeholder="Enter Paid By" <?= $fieldDisplay['paid_by']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['internet_charges']['style'] ?>">
                            <label class="form-label">Internet Charges <?= $fieldDisplay['internet_charges']['star'] ?></label>
                            <input type="number" step="any" class="form-control text-end" name="internet_charges" value="<?= htmlspecialchars($formData['internet_charges'] ?? '') ?>" placeholder="Enter Internet Charges" <?= $fieldDisplay['internet_charges']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['hub_spoc_name']['style'] ?>">
                            <label class="form-label">Hub SPOC Name <?= $fieldDisplay['hub_spoc_name']['star'] ?></label>
                            <input type="text" class="form-control" name="hub_spoc_name" value="<?= htmlspecialchars($formData['hub_spoc_name'] ?? '') ?>" placeholder="Enter Hub SPOC Name">
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['hub_spoc_number']['style'] ?>">
                            <label class="form-label">Hub SPOC Number <?= $fieldDisplay['hub_spoc_number']['star'] ?></label>
                            <input type="tel" class="form-control" name="hub_spoc_number" value="<?= htmlspecialchars($formData['hub_spoc_number'] ?? '') ?>" placeholder="Enter Hub SPOC Number" <?= $fieldDisplay['hub_spoc_number']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['wifi_provider']['style'] ?>">
                            <label class="form-label">WiFi Provider <?= $fieldDisplay['wifi_provider']['star'] ?></label>
                            <input type="text" class="form-control" name="wifi_provider" value="<?= htmlspecialchars($formData['wifi_provider'] ?? '') ?>" placeholder="Enter WiFi Provider">
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['wifi_pass']['style'] ?>">
                            <label class="form-label">WiFi Pass <?= $fieldDisplay['wifi_pass']['star'] ?></label>
                            <input type="text" class="form-control" name="wifi_pass" value="<?= htmlspecialchars($formData['wifi_pass'] ?? '') ?>" placeholder="Enter WiFi Pass">
                        </div>
                        <div class="col-md-12" style="<?= $fieldDisplay['is_oem']['style'] ?>">
                            <label class="form-label">OEM (Original Equipment Manufacturer) <?= $fieldDisplay['is_oem']['star'] ?></label>
                            <div class="radio radio-outline radio-inline">
                                <input id="unified_oem_yes" type="radio" name="is_oem" value="Yes" <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? 'checked' : '' ?>>
                                <label class="form-label align-top" for="unified_oem_yes">Yes</label>
                            </div>
                            <div class="radio radio-outline radio-inline ms-3">
                                <input id="unified_oem_no" type="radio" name="is_oem" value="No" <?= (!isset($formData['is_oem']) || empty($formData['is_oem']) || strtoupper($formData['is_oem']) == 'NO' || strtoupper($formData['is_oem']) == 'N') ? 'checked' : '' ?>>
                                <label class="form-label align-top" for="unified_oem_no">No</label>
                            </div>
                        </div>
                        <div class="col-md-12 oem-details <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? '' : 'd-none' ?>" id="unified_oem_details">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">OEM Registration Number</label>
                                    <input type="text" class="form-control" name="oem_registration_number" value="<?= htmlspecialchars($formData['oem_registration_number'] ?? '') ?>" placeholder="Enter OEM Registration/Authorization Number">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Authorized Distributor / Dealer Name</label>
                                    <input type="text" class="form-control" name="oem_distributor_name" value="<?= htmlspecialchars($formData['oem_distributor_name'] ?? '') ?>" placeholder="Enter Authorized Distributor/Dealer Name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">OEM Product Category</label>
                                    <input type="text" class="form-control" name="oem_product_category" value="<?= htmlspecialchars($formData['oem_product_category'] ?? '') ?>" placeholder="Enter OEM Product Category">
                                </div>
                            </div>
                        </div>
<?php } ?>
