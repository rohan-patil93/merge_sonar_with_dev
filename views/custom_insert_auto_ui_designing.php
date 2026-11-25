<?php

if (isset($_POST['submit'])){

    $data= array();
    $serval = array();
    function servalnumber($serval, $val){
        if(in_array($val, (array)$serval)){
            $display_postion = servalnumber($serval, $val+2);
        }else{
            $display_postion = $val;
        }
        return $display_postion;
    }
    foreach ($_POST['fieldname'] as $postkey => $postvalue) {
        $display_postion = servalnumber($serval, $_POST['display_postion'][$postkey]);
        $serval[] = $display_postion;
        $data1[$postvalue]['display_postion'] = $display_postion;
        $data1[$postvalue]['fieldname'] = $_POST['fieldname'][$postkey];
        $data1[$postvalue]['elementorfieldtype'] = $_POST['elementorfieldtype'][$postkey];
        $data1[$postvalue]['labelandinputineline'] = (isset($_POST['labelandinputineline'][$postkey]))? 0:1;
        $data1[$postvalue]['type'] = $_POST['elementorytexttype'][$postkey];
        $data1[$postvalue]['lable'] = $_POST['elementorylabel'][$postkey];
        $data1[$postvalue]['elementorplacehilder'] = $_POST['elementorplacehilder'][$postkey];
        $data1[$postvalue]['mandatory'] = (isset($_POST['elementoryrequerd'][$postkey]))? 1:2;
        $data1[$postvalue]['insert_view_width'] = $_POST['elementorycolunmwidth'][$postkey];
        $data1[$postvalue]['elementoryid'] = $_POST['elementoryid'][$postkey];
        $data1[$postvalue]['add_exter'] = 0;
        $data1[$postvalue]['add_custom_field'] = "";
        $data1[$postvalue]['accordion'] = $_POST['accordion'][$postkey];
        $data1[$postvalue]['valuesdefoult'] = $_POST['valuesdefoult'][$postkey];
        $data1[$postvalue]['display_list'] = isset($_POST['display'][$postkey])? 1:2;
        $data1[$postvalue]['insert_display_list'] = (isset($_POST['insert_display_list'][$postkey]))? 1:2;
        $data1[$postvalue]['insert_view_wizard'] = $_POST['insert_view_wizard'][$postkey];
    }
    foreach ($_POST['functiontype'] as $postkey => $postvalue) {
        $button[$postvalue]['functiontype'] = $_POST['functiontype'][$postkey];
        $button[$postvalue]['buttonsize'] = $_POST['buttonsize'][$postkey];
        $button[$postvalue]['buttonwidth'] = $_POST['buttonwidth'][$postkey];
        $button[$postvalue]['buttonname'] = $_POST['buttonname'][$postkey];
        $button[$postvalue]['buttonclass'] = $_POST['buttonclass'][$postkey];
        $button[$postvalue]['buttonposition'] = $_POST['buttonposition'][$postkey];
    }
    $fileuplodingdisplay = (isset($_POST['fileuplodingdisplay']))? 0:1;
    $fileuplodingdeleteddisplay = (isset($_POST['fileuplodingdeleteddisplay']))? 0:1;
    $fileupload = array("file_upload"=>$fileuplodingdisplay,"fileuplodinglabel"=>$_POST['fileuplodinglabel'],"fileuplodingwidth"=>$_POST['fileuplodingwidth'],"fileuplodingdeleteddisplay"=>$fileuplodingdeleteddisplay,"fileuplodingdleetewidth"=>$_POST['fileuplodingdleetewidth']);
    $data_json = json_encode($data1);
    $button_json = json_encode($button);
    $fileupload_json = json_encode($fileupload);
    $data['application_table_name'] = $_POST['tablename'];
    $data['list_attributes'] = $data_json;
    $data['button_attributes'] = $button_json;
    $data['file_upload'] = $fileupload_json;
    $data['form_name'] = $_POST['formname'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['id'] = $_POST['id'];
    $url = API."?mode=insertdisplayattribute&method=post";
    $response = file_post_contents($url, $data);

}
if((isset($_GET['table']) && $_GET['table'] !='') || $_GET['Rid']){
    $rid = $_GET['Rid'];
    $tablename = $_GET['tablename'];
    $tableurl = API."?mode=getattribute&rid=$rid&tablename=$tablename";
    $response = callAPI($tableurl);
    $role = json_decode($response,1);
    $attribute = json_decode($role['list_attributes'],1);
    $button_attribute = json_decode($role['button_attributes'],1);
    $file_upload = json_decode($role['file_upload'],1);
}

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">&nbsp;<?php echo __("Build Your Template");  ?> </h3>
                </div>
                <div class="card-toolbar">
                    <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                        <i class="ki ki-long-arrow-back icon-xs"></i>
                        <?= __('Back'); ?>
                    </a>
                </div>
            </div>
            <form method="POST" class="formclass" onsubmit="return toSubmit();" action="index.php?option=<?php echo $_GET['option'];?>">
                <input type="hidden" name="id" value="<?php echo $_GET['Rid']; ?>">


            <div class="card-body">
                <div class="col-lg-12 row ">
                    <div class="col-lg-4">
                        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
                            <div class="card">
                                <div class="card-header" id="headingOne3">
                                    <div class="card-title" data-toggle="collapse" data-target="#collapseOne3"><?= __('Form Fields'); ?></div>
                                </div>
                                <div id="collapseOne3" class="collapse show" data-parent="#accordionExample3">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <span  class="col-6 col-form-label"><?= __('Table Name'); ?></span>
                                            <div class="col-6">
                                                <input aria-label="<?= htmlspecialchars(__('Tablename'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control space tablname" name="tablename" type="text" value="<?php echo $role['application_table_name']; ?>" id="" <?php if(isset($_GET['Rid'])){ echo "readonly";
} ?> Required/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <span  class="col-6 col-form-label"><?= __('Page Title'); ?></span>
                                            <div class="col-6">
                                                <input aria-label="<?= htmlspecialchars(__('Formname'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control formname" name="formname" type="text" value="<?php echo $role['page_title']; ?>" id="" Required/>
                                            </div>
                                        </div>
                                        <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5">
                                            <div class="new_fields">

                                                <?php

                                                $m=0;
                                                foreach($attribute as $result){

                                                    ?>
                                                    <div class="card card_count deleterow<?php echo $m; ?>">
                                                        <div class="card-header" id="headingOne<?php echo $m; ?>">
                                                            <div class="card-title collapsed textboxclass<?php echo $m; ?>" data-toggle="collapse" data-target="#collapseOnee<?php echo $m; ?>">
                                                                <button type="button" class="btn btn-link p-0 border-0" onclick="deleterowinputtype(<?php echo $m; ?>)" aria-label="<?= htmlspecialchars(__('Delete row'), ENT_QUOTES, 'UTF-8'); ?>"><i class="flaticon2-cancel-music"></i></button> <?php echo $result['lable'];?>
                                                            </div>
                                                        </div>
                                                        <div id="collapseOnee<?php echo $m; ?>" class="collapse" data-parent="#accordionExample5">
                                                            <div class="card-body">
                                                                <div class="example-preview">
                                                                    <ul class="nav nav-pills nav-fill">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="content_<?php echo $m; ?>" data-toggle="tab" href="#content-<?php echo $m; ?>"   >
                                                                                <span class="nav-text"><?= __('Content'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="advanced_<?php echo $m; ?>" data-toggle="tab" href="#advanced-<?php echo $m; ?>"  aria-controls="profile" >
                                                                                <span class="nav-text"><?= __('Advanced'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="tab-content mt-5" id="myTabContent4">
                                                                        <div class="tab-pane fade show active" id="content-<?php echo $m; ?>" role="tabpanel" aria-labelledby="content_1">
                                                                            <div class="form-group row">
                                                                                <span class="col-6 col-form-label"><?= __('Field Name'); ?></span>
                                                                                <div class="col-6">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementor_fieldname space elementor_fieldname<?php echo $m; ?>" data-id="<?php echo $m; ?>"  type="text" name="fieldname[]" value="<?php echo $result['fieldname'];?>"  <?php if(isset($_GET['Rid'])){ echo "readonly";
} ?>/>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label "><?= __('Field Type'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementor_fieldtype elementor_fieldtype<?php echo $m; ?>" name="elementorfieldtype[]" data-elementory_id="inputbox<?php echo $m; ?>" data-id="<?php echo $m; ?>"  data-select2-id="" tabindex="-1">
                                                                                        <option value="varchar" data-select2-id="42" <?php echo $result['elementorfieldtype']=="varchar"? "selected":""; ?>><?= __('String'); ?></option>
                                                                                        <option value="text" <?php echo $result['elementorfieldtype']=="text"? "selected":""; ?>><?= __('Description'); ?></option>
                                                                                        <option value="int" <?php echo  $result['elementorfieldtype']=="int"? "selected":""; ?>><?= __('Number'); ?></option>
                                                                                        <option value="decimal" <?php echo  $result['elementorfieldtype']=="double"? "selected":""; ?> ><?= __('Decimal'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-6 col-form-label"><?= __('Position'); ?></span>
                                                                                <div class="col-6">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Display Postion'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control" type="text" name="display_postion[]" value="<?php echo $result['display_postion']; ?>" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-6 col-form-label"><?= htmlspecialchars(__('Label & Input One Line'), ENT_QUOTES, 'UTF-8'); ?></span>
                                                                                <div class="col-6">
                                                                                    <span class="switch switch-icon">
                                                                                        <label>
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" data-id="<?php echo $m; ?>" class="elementory_labelandinputtype" data-elementory_id="inputbox<?php echo $m; ?>" data-label="inputlabel<?php echo $m; ?>" data-div="divbox<?php echo $m; ?>" <?php echo  $result['labelandinputineline']=="0"? "checked":""; ?>  name="labelandinputineline[<?php echo $m;?>]" />
                                                                                            <span></span>
                                                                                        </label>
                                                                                    </span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label "><?= __('Type'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Elementorytexttype'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control elementor_text_box_type" onchange="Getinserttype(this.id,this.value)" id="type" name="elementorytexttype[]" data-elementory_id="inputbox<?php echo $m; ?>" data-id="<?php echo $m; ?>"  data-select2-id="" tabindex="-1">
                                                                                        <?php echo optiontype($result['type'],$filed_array); ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Label'); ?></span>
                                                                                <div class="col-6">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementory_lable elementory_lable<?php echo $m; ?>" data-labletext="labletext<?php echo $m; ?>" data-elementory_id="inputlabel<?php echo $m; ?>" data-id="<?php echo $m; ?>"  type="text"  value="<?php echo $result['lable'];?>" name="elementorylabel[]" />
                                                                                </div>
                                                                            </div>

        <div class="form-group row">
            <span class="col-6 col-form-label"><?= __('Display'); ?></span>
            <div class="col-6">
                <span class="switch switch-icon">
                    <label>
                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"  <?php echo  $result['display_list']=="1"? "checked":""; ?>  name="display[<?php echo $m;?>]" />
                        <span></span>
                    </label>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <span class="col-6 col-form-label"><?= __('Insert Display List'); ?></span>
            <div class="col-6">
                <span class="switch switch-icon">
                    <label>
                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="display_status" data-id="<?php echo $m; ?>" <?php echo  $result['insert_display_list']=="1"? "checked":""; ?>  name="insert_display_list[<?php echo $m;?>]" />
                        <span></span>
                    </label>
                </span>
            </div>
        </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Accordion'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Accordion'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control" name="accordion[]" >
                                                                                        <option value="N" <?php echo $result['accordion']=="N"? "selected":""; ?>><?= __('Normal'); ?></option>
                                                                                        <option value="R" <?php echo $result['accordion']=="R"? "selected":""; ?>><?= __('Rigth'); ?></option>
                                                                                        <option value="L" <?php echo $result['accordion']=="L"? "selected":""; ?>><?= __('Left'); ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Wizard'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Insert View Wizard'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control" id="exampleSelect1" name="insert_view_wizard[]">
                                                                                        <option value="1" <?php echo $result['insert_view_wizard']=="1"? "selected":""; ?>><?= __('1- Wizard'); ?> </option>
                                                                                        <option value="2" <?php echo $result['insert_view_wizard']=="2"? "selected":""; ?>><?= __('2- Wizard'); ?> </option>
                                                                                        <option value="3" <?php echo $result['insert_view_wizard']=="3"? "selected":""; ?>><?= __('3- Wizard'); ?> </option>
                                                                                        <option value="4" <?php echo $result['insert_view_wizard']=="4"? "selected":""; ?>><?= __('4- Wizard'); ?> </option>
                                                                                        <option value="5" <?php echo $result['insert_view_wizard']=="5"? "selected":""; ?>><?= __('5- Wizard'); ?> </option>
                                                                                        <option value="6" <?php echo $result['insert_view_wizard']=="6"? "selected":""; ?>><?= __('6- Wizard'); ?> </option>
                                                                                        <option value="7" <?php echo $result['insert_view_wizard']=="7"? "selected":""; ?>><?= __('7- Wizard'); ?> </option>
                                                                                        <option value="8" <?php echo $result['insert_view_wizard']=="8"? "selected":""; ?>><?= __('8- Wizard'); ?> </option>
                                                                                        <option value="9" <?php echo $result['insert_view_wizard']=="9"? "selected":""; ?>><?= __('9- Wizard'); ?> </option>
                                                                                        <option value="10" <?php echo $result['insert_view_wizard']=="10"? "selected":""; ?>><?= __('10- Wizard'); ?> </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Placeholder'); ?></span>
                                                                                <div class="col-6">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementory_placeholder  elementory_placeholder<?php echo $m; ?>" type="text" value="<?php echo $result['elementorplacehilder'];?>" name="elementorplacehilder[]" data-elementory_id="inputbox<?php echo $m; ?>" id=""/>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-6 col-form-label"><?= __('Required'); ?></span>
                                                                                <div class="col-6">
                                                                                    <span class="switch switch-icon">
                                                                                        <label>
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" data-requerd= "labletextrequerd<?php echo $m; ?>" data-label="inputlabel<?php echo $m; ?>" data-div="divbox<?php echo $m; ?>" class="elementory_required" data-elementory_id="inputbox<?php echo $m; ?>" name="elementoryrequerd[<?php echo $m;?>]"  <?php echo $result['mandatory']=="1"? "checked":""; ?>/>
                                                                                            <span></span>
                                                                                        </label>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Column Width'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementory_width" data-id="<?php echo $m; ?>" data-elementory_id="divbox<?php echo $m; ?>" name="elementorycolunmwidth[]"  data-select2-id="" tabindex="-1">
                                                                                        <?php echo  optiontype($result['insert_view_width'],$widtharray); ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="advanced-<?php echo $m; ?>" role="tabpanel" aria-labelledby="advanced_1">
                                                                            <div class="form-group row">
                                                                                <span  class="col-5 col-form-label"><?= __('Default Value'); ?></span>
                                                                                <div class="col-7">
                                                                                    <div class="input-group">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control valuesdefoult" data-id="<?php echo $m; ?>" name="valuesdefoult[]" value="<?php echo $result['valuesdefoult']; ?>" placeholder="<?= htmlspecialchars(__('Search for...'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                        <div class="input-group-append">
                                                                                            <button class="btn btn-secondary "  type="button">
                                                                                                <i class="flaticon-coins"></i></button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span  class="col-5 col-form-label"><?= __('ID Name'); ?></span>
                                                                                    <div class="col-7">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Elementoryid'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control elementory_id" name="elementoryid[]" type="text" data-elementory_id="inputbox<?php echo $m; ?>" value="<?php echo $result['elementoryid'];?>" id=""/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-12">
                                                                                        <p class="text-muted font-size-xs"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s'); ?></p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span  class="col-5 col-form-label"><?= __('Shortcode'); ?></span>
                                                                                    <div class="col-7">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="" id=""/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div></div></div></div>
                                                                <?php $m++;
} //}    ?>
    </div>
    <div class="col-12 mt-3">
        <button type="button" class="btn btn-primary mr-3 addnewfillds"><i class="flaticon2-plus"></i> <?= __('ADD Item') ?></button>
    </div>
</div>
</div>
</div>
</div>
<div class="card">
    <div class="card-header" id="headingThreefileupload3">
        <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThreefileupload3"><?= __('File Uploads'); ?>
        </div>
    </div>
    <div id="collapseThreefileupload3" class="collapse" data-parent="#accordionExample3">
        <div class="card-body">
            <div class="form-group row mt-5">
                <span class="col-5 col-form-label"><?= __('File uploading Display'); ?></span>
                <div class="col-7">
                    <span class="switch switch-icon">
                        <label>
                            <input aria-label="<?= htmlspecialchars(__('Fileuplodingdisplay'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"  name="fileuplodingdisplay" class="fileuplodingdisplay" <?php echo $file_upload['file_upload']=="0"? "checked":""; ?> > <span></span>
                        </label>
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <span  class="col-5 col-form-label"><?= __('Label'); ?></span>
                <div class="col-7">
                    <input aria-label="<?= htmlspecialchars(__('Fileuplodinglabel'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodinglablel" type="text" name="fileuplodinglabel"  value="<?php echo $file_upload['fileuplodinglabel']!=""?$file_upload['fileuplodinglabel']:"File Upload"; ?>" />
                </div>
            </div>
            <div class="form-group row">
                <span  class="col-5 col-form-label"><?= __('Width'); ?></span>
                <div class="col-7">
                    <select aria-label="<?= htmlspecialchars(__('Fileuplodingwidth'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodingwidth" name="fileuplodingwidth">


                        <?php echo  optiontype($file_upload['fileuplodingwidth']!=""?$file_upload['fileuplodingwidth']:"6",$widtharray); ?>
                    </select>
                </div>
            </div>
            <div class="form-group row mt-5">
                <span class="col-5 col-form-label"><?= __('Delete Files Display'); ?></span>
                <div class="col-7">
                    <span class="switch switch-icon">
                        <label>
                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"    <?php if(isset($_GET['Rid'])){ echo $file_upload['fileuplodingdeleteddisplay']=="0"? "checked":"";
}else{ echo "checked";
} ?>  class="fileuplodingdeleteddisplay" name="fileuplodingdeleteddisplay" />
                            <span></span>
                        </label>
                    </span>
                </div>
            </div>
            <div class="form-group row">
                <span  class="col-5 col-form-label"><?= __('Delete width'); ?></span>
                <div class="col-7">
                    <select aria-label="<?= htmlspecialchars(__('Fileuplodingdleetewidth'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodingdleetewidth"  name="fileuplodingdleetewidth">
                        <?php echo optiontype($file_upload['fileuplodingdleetewidth']!=""?$file_upload['fileuplodingdleetewidth']:"6",$widtharray); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header" id="headingTwo3">
        <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo3"><?= __('Buttons'); ?>
        </div>
    </div>
    <div id="collapseTwo3" class="collapse" data-parent="#accordionExample3">
        <div class="new_buttons">

            <?php
            $k=1;
            foreach($button_attribute as $row){
                $k++;
                ?>
                <div class="card buttons_count mt-3 deletebutton<?php echo $k; ?>">
                    <div class="card-header" id="headingOnebutton<?php echo $k; ?>">
                        <div class="card-title collapsed title<?php echo $k; ?>" data-toggle="collapse" data-target="#collapseOneebutton<?php echo $k; ?>" aria-expanded="false">
                            <button type="button" class="btn btn-link p-0 border-0" onclick="deletebutton(<?php echo $k; ?>)" aria-label="<?= htmlspecialchars(__('Delete button'), ENT_QUOTES, 'UTF-8'); ?>"><i class="flaticon2-cancel-music"></i></button> <?php echo  $row['buttonname']; ?>
                        </div>
                    </div>
                    <div id="collapseOneebutton<?php echo $k; ?>" class="collapse" data-parent="#accordionExample5" style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Function Type'); ?></span>
                                <div class="col-7">
                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control function_type function_type<?php echo $k; ?>" data-id="<?php echo $k; ?>" data-select2-id="" name="functiontype[]" tabindex="-1">
                                        <option value="save" <?php echo $row['functiontype']=="2"? "selected":""; ?> ><?= __('save'); ?></option>
                                        <option value="saveandcontinue" <?php echo $row['functiontype']=="saveandcontinue"? "selected":""; ?>><?= htmlspecialchars(__('Save & continue'), ENT_QUOTES, 'UTF-8'); ?></option>
                                        <option value="update" <?php echo $row['functiontype']=="update"? "selected":""; ?> ><?= __('Update'); ?></option>
                                        <option value="print" <?php echo $row['functiontype']=="print"? "selected":""; ?>><?= __('Print'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Size'); ?></span>
                                <div class="col-7">
                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control button_size button_sizes<?php echo $k; ?>" id="" data-id="<?php echo $k; ?>" name="buttonsize[]" data-select2-id="" tabindex="-1">
                                        <option value="btn-xs" <?php echo $row['buttonsize']=="btn-xs"? "selected":""; ?>><?= __('Extra Small'); ?></option>
                                        <option value="btn-sm" <?php echo $row['buttonsize']=="btn-sm"? "selected":""; ?>><?= __('Small'); ?></option>
                                        <option value="" <?php echo $row['buttonsize']==""? "selected":""; ?>><?= __('Medium'); ?></option>
                                        <option value="btn-lg" <?php echo $row['buttonsize']=="btn-lg"? "selected":""; ?>><?= __('Large'); ?></option>
                                        <option value="btn-lg btn-block" <?php echo $row['buttonsize']=="btn-lg btn-block"? "selected":""; ?>><?= __('Extra Large'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Column Width'); ?></span>
                                <div class="col-7">
                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control buttonwidth buttonwidth<?php echo $k; ?>" id="" data-id="<?php echo $k; ?>" name="buttonwidth[]" data-select2-id="" tabindex="-1">
                                        <option value="12" <?php echo $row['buttonwidth']=="12"? "selected":""; ?>><?= __('Default'); ?></option>
                                        <?php echo  optiontype($row['buttonwidth'],$widtharray); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Button Position'); ?></span>

                                <input type="hidden"  id="buttonposition<?php echo $k; ?>" name="buttonposition[]" value="<?php echo  $row['buttonwidth']; ?>">

                                <div class="col-7">
                                    <button type="button"  value="text-left" data-id="<?php echo $k; ?>" class="btn btn-outline-secondary">
                                        <i class="flaticon2-soft-icons"></i>
                                    </button>
                                    <button type="button" value="text-center" data-id="<?php echo $k; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <button type="button" value="text-right" data-id="<?php echo $k; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-align-right"></i>
                                    </button>

                                </div>
                            </div>
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Button Name'); ?></span>
                                <div class="col-7">
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control buttonname buttonname<?php echo $k; ?>" name="buttonname[]" data-id="<?php echo $k; ?>" type="text" value="<?php echo  $row['buttonname']; ?>" id=""/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span  class="col-5 col-form-label"><?= __('Form Class'); ?></span>
                                <div class="col-7">
                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control button_class button_class<?php echo $k; ?>" name="buttonclass[]" data-id="<?php echo $k; ?>" type="text"  value="<?php echo  $row['buttonclass']; ?>" id=""/>
                                </div>
                            </div>
                        </div></div></div>
                    <?php } ?>
                </div>
                <div class="col-12 mt-3">
                    <button type="button" class="btn btn-primary mr-3 addnewbutton"><i class="flaticon2-plus"></i> <?= __('Add Button') ?></button>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree3">
                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree3"><?= __('Email'); ?>
                </div>
            </div>
            <div id="collapseThree3" class="collapse" data-parent="#accordionExample3">
                <div class="card-body">
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('To'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="abc@gmail.com" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Subject'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="Hi this is text" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Mesage'); ?></span>
                        <div class="col-8">
                            <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="" rows="3" style="height: 76px;"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <p class="text-muted font-size-xs"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s'); ?></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('From Email'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="abc@gmail.com" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('From Name'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="Infisuite" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Reply-To'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="abc@gmail.com" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Cc'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Bcc'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="abc@gmail.com" id=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree4">
                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseFour3"><?= __('Steps Settings'); ?>
                </div>
            </div>
            <div id="collapseFour3" class="collapse" data-parent="#accordionExample3">
                <div class="card-body">
                    <div class="form-group row">
                        <span  class="col-5 col-form-label"><?= __('Type'); ?></span>
                        <div class="col-7">
                            <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="" name="param" data-select2-id="" tabindex="-1">
                                <option value="" data-select2-id=""><?= htmlspecialchars(__('Number & Text'), ENT_QUOTES, 'UTF-8'); ?></option>
                                <option value="" data-select2-id=""><?= __('None'); ?></option>
                                <option value="" data-select2-id=""><?= __('Text'); ?></option>
                                <option value="" data-select2-id=""><?= __('Icon'); ?></option>
                                <option value="" data-select2-id=""><?= __('Number'); ?></option>
                                <option value="" data-select2-id=""><?= __('Progress Bar'); ?></option>
                                <option value="" data-select2-id=""><?= htmlspecialchars(__('Number & Text'), ENT_QUOTES, 'UTF-8'); ?></option>
                                <option value="" data-select2-id=""><?= htmlspecialchars(__('Icon & Text'), ENT_QUOTES, 'UTF-8'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span  class="col-5 col-form-label"><?= __('Shape'); ?></span>
                        <div class="col-7">
                            <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="" name="param" data-select2-id="" tabindex="-1">
                                <option value="" data-select2-id=""><?= __('Circle'); ?></option>
                                <option value="" data-select2-id=""><?= __('Square'); ?></option>
                                <option value="" data-select2-id=""><?= __('Rounded'); ?></option>
                                <option value="" data-select2-id=""><?= __('None'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree5">
                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseFive5"><?= __('Additional Options'); ?>
                </div>
            </div>
            <div id="collapseFive5" class="collapse" data-parent="#accordionExample3">
                <div class="card-body">
                    <div class="form-group row">
                        <span  class="col-4 col-form-label"><?= __('Form ID'); ?></span>
                        <div class="col-8">
                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="new_form_id" id=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <p class="text-muted font-size-xs"><?= __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s'); ?></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span class="col-6 col-form-label"><?= __('Custom Message'); ?></span>
                        <div class="col-6">
                            <span class="switch switch-icon">
                                <label>
                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="" />
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-8">
    <div class="col-12 ">
        <!--begin::Wizard Body-->
        <div class="row ">
            <div class="col-xl-12">

                <div class="form" id="kt_contact_add_form">

                    <div class="pb-5 " data-wizard-type="step-content">

                        <h4 class=" textcapital font-weight-bold formnamehedimg text-dark"><?= __('Form Name'); ?></h4>
                        <div class=" form-group row body_fields">
                            <?php $s=0; foreach($attribute as $result){   ?>
                                <div class="divbox<?php echo $s; ?>  col-lg-<?php echo $result['insert_view_width']; ?> <?php echo ($result['labelandinputineline']==0)? "row":""; ?>  mt-5"  style="display: <?php echo $result['insert_display_list']==2?"none":"block"; ?>"  id="divid<?php echo $s; ?>" >
                                    <span class="textcapital  col-form-label <?php echo ($result['labelandinputineline']==0)? "col-lg-3":""; ?> text-lg-right inputlabel<?php echo $s; ?>"><span class="labletext<?php echo $s; ?>"><?php echo $result['lable'];?></span><span class="text-danger labletextrequerd<?php echo $s; ?>" style="<?php echo $result['mandatory']==1? 'display:inherit':'display:none'; ?>" >*</span></span>
                                    <div class="inputtypediv<?php echo $s; ?> <?php echo ($result['labelandinputineline']==0)? "col-lg-9":""; ?>" >
                                        <?php  textboxes($s,$result['type'],$result['elementorplacehilder'],$result['valuesdefoult']); ?>
                                    </div>
                                </div>
                            <?php $s++;
} ?>
                        </div>
                        <div class="fileuploadUig form-group row">
                            <?php if($file_upload['file_upload']==0 && isset($_GET['Rid'])){  ?>
                                <div class="col-lg-<?php echo $file_upload['fileuplodingwidth']; ?>  fileuploadui" id="fileuploaduiid">
                                    <span class="fileuplodinglablelui textcapital" ><?php echo $file_upload['fileuplodinglabel']; ?> </span>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
                                            <div class="dropzone-msg dz-message needsclick">
                                                <h3 class="dropzone-msg-title"><?= __('Drop files here or click to upload.'); ?></h3>
                                                <span class="dropzone-msg-desc"><?= __('Upload up to 10 files'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-<?php echo $file_upload['fileuplodingdleetewidth']; ?> fileuploaddeleteui" style="<?php echo  $file_upload['fileuplodingdeleteddisplay']==0?'display: block':'display: none'; ?>" id="fileuploaddeleteuiid">
                                    <span class="textcapital"><?= __('Delete list'); ?></span>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2_2"></div>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="row buttonlist">

                            <?php
                            $z=1; foreach($button_attribute as $row){ $z++;
                            ?>    <div class="col-lg-<?php echo $row['buttonwidth']; ?> <?php echo $row['buttonposition']; ?>"  id="buttondiv<?php echo $z; ?>">
                                <?php buttonsList($z,$row['functiontype'],$row['buttonname'],$row['buttonclass'],$row['buttonsize']); ?>
                            </div>

                        <?php }  $maxnu = $s+$z;?>

                        <input type="hidden" id="max" value="<?php echo $maxnu; ?>">
                        <input type="hidden" id="incrimenttxetbox" value="<?php echo $s; ?>">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Wizard Body-->
</div>
</div>

<div class="card-footer">
    <div class="row">
        <input type="hidden" name="option" value="COM_AUTO_UI_DESIGNING">
        <button type="submit" class="btn btn-primary mr-2" name="submit" value="submit" ><i class="la la-check">    </i><?= __('Save Attributes') ?></button>
    </div>
</div>
</div>
</div>

</form>
</div>
<div id="kt_modal_KTDatatable_remote" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="min-height: 590px;" id="popup1">

        </div>
    </div>
</div>
