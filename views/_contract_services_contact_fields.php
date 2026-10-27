<?php
$contract_services_contact_section = $contract_services_contact_section ?? '';
$selectedcontractservices = array();
if (!empty($formData['contract_services_id'])) {
    $selectedcontractservices = json_decode($formData['contract_services_id'], true);
    if (!is_array($selectedcontractservices)) {
        $selectedcontractservices = array();
    }
}
$contact_persons = array();
if (!empty($formData['contact_persons'])) {
    $contact_persons = json_decode($formData['contact_persons'], true);
    if (!is_array($contact_persons)) {
        $contact_persons = array();
    }
}
?>
<?php if ($contract_services_contact_section == 'services') { ?>
                        <div class="col-md-12" style="<?= $fieldDisplay['contract_services_id']['style'] ?>">
                            <label class="form-label d-block">Services <?= $fieldDisplay['contract_services_id']['star'] ?></label>
                            <select name="contract_services_id[]" multiple class="form-control js-example-basic-multiple" id="unified_contract_services_multiselect" <?= $fieldDisplay['contract_services_id']['required'] ?> <?= $amc_renew_disabled ?? '' ?>>
                                <?php if (count((array)$contract_services) > 0) {
                                    foreach ($contract_services as $conservRow) {
                                        $conserv = is_array($conservRow) ? $conservRow : json_decode(json_encode($conservRow), true);
                                        $serviceId = $conserv['id'] ?? '';
                                        $isSelected = false;
                                        if (count((array)$selectedcontractservices) > 0) {
                                            foreach ($selectedcontractservices as $selId) {
                                                if ((string) $selId == (string) $serviceId) {
                                                    $isSelected = true;
                                                    break;
                                                }
                                            }
                                        } ?>
                                        <option value="<?= htmlspecialchars($serviceId) ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars($conserv['service_name'] ?? '') ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
<?php } elseif ($contract_services_contact_section == 'contact_persons') { ?>
                        <div class="col-md-12 d-flex flex-column gap-3" id="MoreContactPerson">
                            <?php if (count((array)$contact_persons) > 0) {
                                $mk = 1;
                                foreach ($contact_persons as $conpers) {
                                    $conpers = is_array($conpers) ? $conpers : json_decode(json_encode($conpers), true); ?>
                            <div class="row g-2 unified-contact-person-row mb-0" id="contact_persons_remove_<?php echo $mk; ?>">
                                <div class="col-lg-4">
                                    <input type="text" name="contact_persons[name][]" value="<?php echo htmlspecialchars($conpers['name'] ?? ''); ?>" class="form-control" placeholder="Contact Person Name">
                                </div>
                                <div class="col-lg-4">
                                    <input type="email" name="contact_persons[email][]" value="<?php echo htmlspecialchars($conpers['email'] ?? ''); ?>" class="form-control" placeholder="Contact Person Email">
                                </div>
                                <div class="col-lg-3">
                                    <input type="tel" name="contact_persons[number][]" value="<?php echo htmlspecialchars($conpers['number'] ?? ''); ?>" class="form-control" placeholder="Contact Person Number">
                                </div>
                                <div class="col-lg-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link p-0 unified-remove-contact-person text-danger" data-target-id="contact_persons_remove_<?php echo $mk; ?>" title="Click to Remove">
                                        <i class="ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                                <?php $mk++;
                                }
                            } ?>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="unified-add-contact-person-btn">
                                <i class="fa fa-plus-circle me-1"></i> Add More Contact Person
                            </button>
                        </div>
<?php } ?>
