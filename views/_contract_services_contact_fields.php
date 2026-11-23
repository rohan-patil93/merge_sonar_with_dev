<?php
if (!function_exists('_contract_services_render_select')) {
    function _contract_services_render_select($fieldDisplay, $amc_renew_disabled, $contract_services, $selectedcontractservices) {
        $selectedSet = array_map('strval', (array)$selectedcontractservices);
?>
                        <div class="col-md-12" style="<?= $fieldDisplay['contract_services_id']['style'] ?>">
                            <label for="unified_contract_services_multiselect" class="form-label d-block"><?= __('Services '.$fieldDisplay['contract_services_id']['star']) ?></label>
                            <select name="contract_services_id[]" multiple class="form-control js-example-basic-multiple" id="unified_contract_services_multiselect" <?= $fieldDisplay['contract_services_id']['required'] ?> <?= $amc_renew_disabled ?? '' ?> aria-label="<?= __('Contract Services Id') ?>">
                                <?php if (count((array)$contract_services) > 0) {
                                    foreach ($contract_services as $conservRow) {
                                        $conserv = is_array($conservRow) ? $conservRow : json_decode(json_encode($conservRow), true);
                                        $serviceId = $conserv['id'] ?? '';
                                        $isSelected = in_array((string)$serviceId, $selectedSet, true); ?>
                                        <option value="<?= htmlspecialchars($serviceId) ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars(__($conserv['service_name'] ?? '')) ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
<?php
    }
}

if (!function_exists('_contract_services_render_contact_persons')) {
    function _contract_services_render_contact_persons($contact_persons) {
?>
                        <div class="col-md-12 d-flex flex-column gap-3" id="MoreContactPerson">
                            <?php if (count((array)$contact_persons) > 0) {
                                $mk = 1;
                                foreach ($contact_persons as $conpers) {
                                    $conpers = is_array($conpers) ? $conpers : json_decode(json_encode($conpers), true); ?>
                            <div class="row g-2 unified-contact-person-row mb-0" id="contact_persons_remove_<?php echo $mk; ?>">
                                <div class="col-lg-4">
                                    <input type="text" name="contact_persons[name][]" value="<?php echo htmlspecialchars($conpers['name'] ?? ''); ?>" class="form-control" placeholder="<?= __('Contact Person Name') ?>" aria-label="<?= __('Contact Person Name') ?>">
                                </div>
                                <div class="col-lg-4">
                                    <input type="email" name="contact_persons[email][]" value="<?php echo htmlspecialchars($conpers['email'] ?? ''); ?>" class="form-control" placeholder="<?= __('Contact Person Email') ?>" aria-label="<?= __('Contact Person Email') ?>">
                                </div>
                                <div class="col-lg-3">
                                    <input type="tel" name="contact_persons[number][]" value="<?php echo htmlspecialchars($conpers['number'] ?? ''); ?>" class="form-control" placeholder="<?= __('Contact Person Number') ?>" aria-label="<?= __('Contact Person Number') ?>">
                                </div>
                                <div class="col-lg-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link p-0 unified-remove-contact-person text-danger" data-target-id="contact_persons_remove_<?php echo $mk; ?>" title="<?= __('Click to Remove') ?>">
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
                                <i class="fa fa-plus-circle me-1"></i> <?= __('Add More Contact Person') ?>
                            </button>
                        </div>
<?php
    }
}

if (!function_exists('render_contract_services_contact_fields')) {
    function render_contract_services_contact_fields($ctx) {
        $contract_services_contact_section = $ctx['contract_services_contact_section'] ?? '';
        $formData = $ctx['formData'] ?? array();
        $fieldDisplay = $ctx['fieldDisplay'] ?? array();
        $amc_renew_disabled = $ctx['amc_renew_disabled'] ?? '';
        $contract_services = $ctx['contract_services'] ?? array();
        $selectedcontractservices = !empty($formData['contract_services_id']) ? json_decode($formData['contract_services_id'], true) : array();
        if (!is_array($selectedcontractservices)) {
            $selectedcontractservices = array();
        }
        $contact_persons = !empty($formData['contact_persons']) ? json_decode($formData['contact_persons'], true) : array();
        if (!is_array($contact_persons)) {
            $contact_persons = array();
        }

        if ($contract_services_contact_section == 'services') {
            _contract_services_render_select($fieldDisplay, $amc_renew_disabled, $contract_services, $selectedcontractservices);
        } elseif ($contract_services_contact_section == 'contact_persons') {
            _contract_services_render_contact_persons($contact_persons);
        }
    }
}
?>
