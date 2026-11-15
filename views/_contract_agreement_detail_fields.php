<?php
$contract_agreement_section = $contract_agreement_section ?? '';
?>
<?php if ($contract_agreement_section == 'classification') { ?>
                        <div class="col-md-4" style="<?= $fieldDisplay['typeofaggrements']['style'] ?>">
                            <label for="fld_typeofaggrements" class="form-label"><?= __('Type of Agreements '.$fieldDisplay['typeofaggrements']['star']) ?></label>
                            <select id="fld_typeofaggrements" class="form-control js-example-basic-single" name="typeofaggrements" <?= $fieldDisplay['typeofaggrements']['required'] ?>>
                                <option value=""><?= __('Select Type of Agreements') ?></option>
                                <?php if (count((array)$genc) > 0) {
                                    foreach ($genc as $item) {
                                        $gi = is_array($item) ? $item : json_decode(json_encode($item), true);
                                        $sel = (isset($formData['typeofaggrements']) && $formData['typeofaggrements'] == ($gi['genc_detail_code'] ?? '')) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($gi['genc_detail_code'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars(__($gi['genc_detail_name'] ?? '')) ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['description_id']['style'] ?>">
                            <label for="fld_description_id" class="form-label"><?= __('Description '.$fieldDisplay['description_id']['star']) ?></label>
                            <select id="fld_description_id" class="form-control js-example-basic-single" name="description_id" <?= $fieldDisplay['description_id']['required'] ?>>
                                <option value=""><?= __('Select Description') ?></option>
                                <?php if (count((array)$des) > 0) {
                                    foreach ($des as $d) {
                                        $dr = is_array($d) ? $d : json_decode(json_encode($d), true);
                                        $sel = (($formData['description_id'] ?? '') == ($dr['id'] ?? '')) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($dr['id'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars(__($dr['description_type'] ?? '')) ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
<?php }
if ($contract_agreement_locations_section == 'Y') { ?>
<div class="col-md-12 " style="<?= $fieldDisplay['locations']['style'] ?>">
                            <label for="unified_locations_multiselect" class="form-label"><?= __('Locations '.$fieldDisplay['locations']['star']) ?></label>
                            <select name="locations[]" multiple class="form-control js-example-basic-multiple" id="unified_locations_multiselect" <?= $fieldDisplay['locations']['required'] ?>>
                                <option value="0"><?= __('Select Locations') ?></option>
                                <?php if (count((array)$locations) > 0) {
                                    foreach ($locations as $location) {
                                        $loc = is_array($location) ? $location : json_decode(json_encode($location), true);
                                        $sel = in_array($loc['id'], (array)$selectedLocations) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($loc['id'] ?? '') ?>" <?= $sel ?>><?= htmlspecialchars(__($loc['location_name'] ?? '')) ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>

<?php }
if ($contract_agreement_section == 'extended') { ?>

                        <div class="col-md-12 cc-amc-only">
                            <p class="text-muted small mb-0"><em><?= __('Locations are configured in Step 2 — Locations, Assets & Service Periods.') ?></em></p>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['additional_notes']['style'] ?>">
                            <label for="fld_additional_notes" class="form-label"><?= __('Additional Notes '.$fieldDisplay['additional_notes']['star']) ?></label>
                            <textarea id="fld_additional_notes" class="form-control" name="additional_notes" rows="2" placeholder="<?= __('Additional Notes') ?>"><?= htmlspecialchars($formData['additional_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['termination_condition']['style'] ?>">
                            <label for="fld_termination_condition" class="form-label"><?= __('Termination Condition '.$fieldDisplay['termination_condition']['star']) ?></label>
                            <textarea id="fld_termination_condition" class="form-control" name="termination_condition" rows="2"><?= htmlspecialchars($formData['termination_condition'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['isp']['style'] ?>">
                            <label for="fld_isp" class="form-label"><?= __('ISP '.$fieldDisplay['isp']['star']) ?></label>
                            <input id="fld_isp" type="text" class="form-control" name="isp" value="<?= htmlspecialchars($formData['isp'] ?? '') ?>" placeholder="<?= __('Enter ISP') ?>" <?= $fieldDisplay['isp']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['paid_by']['style'] ?>">
                            <label for="fld_paid_by" class="form-label"><?= __('Paid By '.$fieldDisplay['paid_by']['star']) ?></label>
                            <input id="fld_paid_by" type="text" class="form-control" name="paid_by" value="<?= htmlspecialchars($formData['paid_by'] ?? '') ?>" placeholder="<?= __('Enter Paid By') ?>" <?= $fieldDisplay['paid_by']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['internet_charges']['style'] ?>">
                            <label for="fld_internet_charges" class="form-label"><?= __('Internet Charges '.$fieldDisplay['internet_charges']['star']) ?></label>
                            <input id="fld_internet_charges" type="number" step="any" class="form-control text-end" name="internet_charges" value="<?= htmlspecialchars($formData['internet_charges'] ?? '') ?>" placeholder="<?= __('Enter Internet Charges') ?>" <?= $fieldDisplay['internet_charges']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['hub_spoc_name']['style'] ?>">
                            <label for="fld_hub_spoc_name" class="form-label"><?= __('Hub SPOC Name '.$fieldDisplay['hub_spoc_name']['star']) ?></label>
                            <input id="fld_hub_spoc_name" type="text" class="form-control" name="hub_spoc_name" value="<?= htmlspecialchars($formData['hub_spoc_name'] ?? '') ?>" placeholder="<?= __('Enter Hub SPOC Name') ?>">
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['hub_spoc_number']['style'] ?>">
                            <label for="fld_hub_spoc_number" class="form-label"><?= __('Hub SPOC Number '.$fieldDisplay['hub_spoc_number']['star']) ?></label>
                            <input id="fld_hub_spoc_number" type="tel" class="form-control" name="hub_spoc_number" value="<?= htmlspecialchars($formData['hub_spoc_number'] ?? '') ?>" placeholder="<?= __('Enter Hub SPOC Number') ?>" <?= $fieldDisplay['hub_spoc_number']['required'] ?>>
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['wifi_provider']['style'] ?>">
                            <label for="fld_wifi_provider" class="form-label"><?= __('WiFi Provider '.$fieldDisplay['wifi_provider']['star']) ?></label>
                            <input id="fld_wifi_provider" type="text" class="form-control" name="wifi_provider" value="<?= htmlspecialchars($formData['wifi_provider'] ?? '') ?>" placeholder="<?= __('Enter WiFi Provider') ?>">
                        </div>
                        <div class="col-md-4" style="<?= $fieldDisplay['wifi_pass']['style'] ?>">
                            <label for="fld_wifi_pass" class="form-label"><?= __('WiFi Pass '.$fieldDisplay['wifi_pass']['star']) ?></label>
                            <input id="fld_wifi_pass" type="text" class="form-control" name="wifi_pass" value="<?= htmlspecialchars($formData['wifi_pass'] ?? '') ?>" placeholder="<?= __('Enter WiFi Pass') ?>">
                        </div>
                        <div class="col-md-12" style="<?= $fieldDisplay['is_oem']['style'] ?>">
                            <span class="form-label d-inline-block fw-bold"><?= __('OEM (Original Equipment Manufacturer) '.$fieldDisplay['is_oem']['star']) ?></span>
                            <div class="radio radio-outline radio-inline">
                                <input id="unified_oem_yes" type="radio" name="is_oem" value="Yes" <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? 'checked' : '' ?>>
                                <label class="form-label align-top" for="unified_oem_yes"><?= __('Yes') ?></label>
                            </div>
                            <div class="radio radio-outline radio-inline ms-3">
                                <input id="unified_oem_no" type="radio" name="is_oem" value="No" <?= (!isset($formData['is_oem']) || empty($formData['is_oem']) || strtoupper($formData['is_oem']) == 'NO' || strtoupper($formData['is_oem']) == 'N') ? 'checked' : '' ?>>
                                <label class="form-label align-top" for="unified_oem_no"><?= __('No') ?></label>
                            </div>
                        </div>
                        <div class="col-md-12 oem-details <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? '' : 'd-none' ?>" id="unified_oem_details">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fld_oem_registration_number" class="form-label"><?= __('OEM Registration Number') ?></label>
                                    <input id="fld_oem_registration_number" type="text" class="form-control" name="oem_registration_number" value="<?= htmlspecialchars($formData['oem_registration_number'] ?? '') ?>" placeholder="<?= __('Enter OEM Registration/Authorization Number') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="fld_oem_distributor_name" class="form-label"><?= __('Authorized Distributor / Dealer Name') ?></label>
                                    <input id="fld_oem_distributor_name" type="text" class="form-control" name="oem_distributor_name" value="<?= htmlspecialchars($formData['oem_distributor_name'] ?? '') ?>" placeholder="<?= __('Enter Authorized Distributor/Dealer Name') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="fld_oem_product_category" class="form-label"><?= __('OEM Product Category') ?></label>
                                    <input id="fld_oem_product_category" type="text" class="form-control" name="oem_product_category" value="<?= htmlspecialchars($formData['oem_product_category'] ?? '') ?>" placeholder="<?= __('Enter OEM Product Category') ?>">
                                </div>
                            </div>
                        </div>
<?php } ?>
