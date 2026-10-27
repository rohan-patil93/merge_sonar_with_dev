<div class="form-group row">
    <div class="col-lg-2">
        <span>Field Names</span>
    </div>
    <div class="col-lg-2">
        <span class="">Lable </span>
    </div>
    <div class="col-lg-1">
        <span class="">   Display</span>
    </div>
    <div class="col-lg-1">
        <span class="">   Postion</span>
    </div>
    <div class="col-lg-2">
        <span class=""> Input type </span>
    </div>
    <div class="col-lg-2">
        <span class=""> Mandatory </span>
    </div>
    <div class="col-lg-1">
        <span class=""> Editable </span>
    </div>
    <div class="col-lg-1">
        <span class=""> Protact </span>
    </div>
</div>
<?php foreach ($attribute as $key => $value) { ?>
<div class="form-group row">
    <div class="col-lg-2">
        <input aria-label="Db[]" type="text" class="form-control" name="db[]" value=<?php echo $key;?> >
    </div>
    <div class="col-lg-2">
        <input aria-label="Insert Lable[]" type="text" class="form-control"  name="insert_lable[]" value="<?php echo str_replace('EDI_', ' ', $value['lable']);?>">
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="Insert Display List[]" type="checkbox"  name="insert_display_list[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <input aria-label="Insert Display Postion[]" type="text" class="form-control" name="insert_display_postion[]" value=<?php echo $value['display_postion'];?>>
    </div>
    <div class="col-lg-2">
        <?php $filed_array = array('text' => 'Simple Text', 'date' => 'Date', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Number','currency'=> 'Currency', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List'); ?>
        <select aria-label="Select option" class="form-control" id="<?php echo $table."#".$key; ?>" onchange="Getinserttype(this.id,this.value)" name= "type[]">
                <?php foreach ($filed_array as $typekey => $typevalue) {?>
                    <option value = "<?php echo $typekey; ?>" <?php if($value['type'] == $typekey) { ?> selected <?php
} ?> ><?php echo $typevalue; ?></option>
                <?php } ?>
        </select>
    </div>
    <div class="col-lg-2">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="Mandatory[]" type="checkbox"  name="mandatory[]"  <?php if($value['mandatory'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="Insert Edit[]" type="checkbox"  name="insert_edit[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
    <div class="col-lg-1">
        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
            <label>
                <input aria-label="Insert Protact[]" type="checkbox"  name="insert_protact[]"  <?php if($value['insert_display_list'] == 1){ ?> checked <?php
} ?> >
                <span></span>
            </label>
        </span>
    </div>
</div>
<?php } ?>
