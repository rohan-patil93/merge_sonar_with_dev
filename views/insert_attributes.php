<div class="form-group row">
    <div class="col-lg-2">
        <span><?= __('Field Names'); ?></span>
    </div>
    <div class="col-lg-2">
        <span class=""><?= __('Lable'); ?> </span>
    </div>
    <div class="col-lg-1">
        <span class="">   <?= __('Display'); ?></span>
    </div>
    <div class="col-lg-1">
        <span class="">   <?= __('Postion'); ?></span>
    </div>
    <div class="col-lg-2">
        <span class=""> <?= __('Input type'); ?> </span>
    </div>
    <div class="col-lg-2">
        <span class=""> <?= __('Mandatory'); ?> </span>
    </div>
    <div class="col-lg-1">
        <span class=""> <?= __('Editable'); ?> </span>
    </div>
    <div class="col-lg-1">
        <span class=""> <?= __('Protact'); ?> </span>
    </div>
</div>
<?php foreach ($attribute as $key => $value) { ?>
<div class="form-group row">
    <div class="col-lg-2">
        <input aria-label="<?= htmlspecialchars(__('Db'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" class="form-control" name="db[]" value=<?php echo $key;?> >
    </div>
    <div class="col-lg-2">
        <input aria-label="<?= htmlspecialchars(__('Insert Lable'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" class="form-control"  name="insert_lable[]" value="<?php echo str_replace('EDI_', ' ', $value['lable']);?>">
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="<?= htmlspecialchars(__('Insert Display List'), ENT_QUOTES, 'UTF-8'); ?>[]" type="checkbox"  name="insert_display_list[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <input aria-label="<?= htmlspecialchars(__('Insert Display Postion'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" class="form-control" name="insert_display_postion[]" value=<?php echo $value['display_postion'];?>>
    </div>
    <div class="col-lg-2">
        <?php $filed_array = array('text' => 'Simple Text', 'date' => 'Date', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Number','currency'=> 'Currency', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List'); ?>
        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="<?php echo $table."#".$key; ?>" onchange="Getinserttype(this.id,this.value)" name= "type[]">
                <?php foreach ($filed_array as $typekey => $typevalue) {?>
                    <option value = "<?php echo $typekey; ?>" <?php if($value['type'] == $typekey) { ?> selected <?php
} ?> ><?php echo $typevalue; ?></option>
                <?php } ?>
        </select>
    </div>
    <div class="col-lg-2">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="<?= htmlspecialchars(__('Mandatory'), ENT_QUOTES, 'UTF-8'); ?>[]" type="checkbox"  name="mandatory[]"  <?php if($value['mandatory'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="<?= htmlspecialchars(__('Insert Edit'), ENT_QUOTES, 'UTF-8'); ?>[]" type="checkbox"  name="insert_edit[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="<?= htmlspecialchars(__('Insert Protact'), ENT_QUOTES, 'UTF-8'); ?>[]" type="checkbox"  name="insert_protact[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
</div>
<?php } ?>
