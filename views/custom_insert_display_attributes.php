<?php
$_SESSION['apply_js']['custom_insert_display_attributes']='1';
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
        $data1[$postvalue]['mandatory'] = (isset($_POST['elementoryrequerd'][$postkey]))?1:2;
        $data1[$postvalue]['insert_view_width'] = $_POST['elementorycolunmwidth'][$postkey];
        $data1[$postvalue]['elementoryid'] = $_POST['elementoryid'][$postkey];
        $data1[$postvalue]['add_exter'] = 0;
        $data1[$postvalue]['add_custom_field'] = "";
        $data1[$postvalue]['accordion'] = $_POST['accordion'][$postkey];
        $data1[$postvalue]['valuesdefoult'] = $_POST['valuesdefoult'][$postkey];
        $data1[$postvalue]['display_list'] = isset($_POST['display'][$postkey])?1:2;
        $data1[$postvalue]['insert_display_list'] = (isset($_POST['insert_display_list'][$postkey]))?1:2;
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
    $tablename = $_GET['table'];
    if($tablename !=""){
        $fileds = "id";
        $where = "application_table_name='".$tablename."'";
        $url = API."?mode=gettablerow&tablename=display_attributes&fields=".$fileds."&where=".$where."&list=1";
        $collfunction = callAPI($url);
        $value = json_decode($collfunction,1);
        if($value!=null){
            $rid = $value['id'];
            $tableurl = API."?mode=getattribute&rid=$rid&tablename=$tablename";
            $response = callAPI($tableurl);
            $role = json_decode($response,1);
            $attribute = json_decode($role['list_attributes'],1);
            $button_attribute = json_decode($role['button_attributes'],1);
            $file_upload = json_decode($role['file_upload'],1);
        }else{
            $attributes = getListAttributes($tablename);
            $attribute =array();
            $role['application_table_name'] = $tablename;
            $role['page_title'] = "Page Title";
            $attribute =array();
            $pos = 10;
            foreach($attributes as $result){
                $type = "";
                $extype= explode("(",$result['Type']);
                if($extype[0]=="int"){
                    $type = "number_type";
                }elseif($extype[0]=="text"){
                    $type = "textarea";
                }elseif($extype[0]=="decimal"){
                    $type = "number_type";
                }elseif($extype[0]=="date" || $extype[0]=="datetime"){
                    $type = "date_pick";
                }else{
                    $type = "text";
                }
                $attribute[$result['Field']] = array("type"=>$type,"insert_view_width"=>"6","lable"=>$result['Field'],"display_postion"=>$pos,"insert_display_list"=>2,"elementorfieldtype"=>$extype[0]);
                $pos=$pos+10;
            }
        }
    }else{
        $rids = encodeId($_GET['Rid']);
        $rids = urldecode($rids);
        $rids = explode(":",$rids);
        $rid = $rids['1'];
        $tableurl = API."?mode=getattribute&rid=$rid&tablename=$tablename";
        $response = callAPI($tableurl);
        $role = json_decode($response,1);
        $attribute = json_decode($role['list_attributes'],1);
        $button_attribute = json_decode($role['button_attributes'],1);
        $file_upload = json_decode($role['file_upload'],1);
    }
}

?>
<script type="text/javascript">

    function fileuploadingcheck(){
    var label = $(".fileuplodinglablel").val();
    if($(".fileuplodingdisplay").is(":checked")){
        $.ajax({
            type: 'POST',
            url: 'views/custom_attributes_fileds_ajax.php',
            data: "page=fileuploadingstatusdisplay",
            success:function(data){
                $(".fileuploadUig").append(data);
                $(".fileuplodinglablelui").html(label);
            }
        });
    }else{
        $(".fileuploadui").remove();
        $(".fileuploaddeleteui").remove();
    }
}

</script>
<style>
.j-custom_class{
   outline: none;
    padding: 40px 15px;
    -webkit-box-sizing: content-box;
    -moz-box-sizing: content-box;
    box-sizing: content-box;
    box-shadow: none;
    background-color: #f9fafd;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -o-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    color: rgba(0,0,0,.54);
    font: 16px 'Open Sans',Helvetica,Arial,sans-serif;

}
.nnnnnsss {
    height:600px;
    transition: 0.5s ease-in;
    top:0;
}
.fixed {

    position:sticky;
    top:0;
    width: 100%;
    transition: 0.5s ease-in;
}
</style>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">

            <div class="card-header">
                <h5><?= __('Build Your Template') ?></h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        <?= __('Back'); ?>
                    </a>
                </div>
            </div>

            <form method="POST" class="formclass" onsubmit="return toSubmit();" >

                <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                <!-- custom-scroll -->
                <div class="card-body">
                    <div class="col-lg-12 row ">
                        <div class="col-lg-4 j-custom_class" >
                            <div class="custom-scroll nnnnnsss">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
                                <div class="card">
                                    <div class="card-header p-2 f-w-600" id="headingOne3">
                                        <div class="card-title" data-bs-toggle="collapse" data-bs-target="#collapseOne3"><?= __('Form Fields'); ?></div>
                                    </div>
                                    <div id="collapseOne3" class="collapse show" data-parent="#accordionExample3">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <span  class="col-4 col-form-label form-label"><?= __('Table') ?></span>
                                                <div class="col-8">
                                                    <?php
                                                    $list = getListTables();
                                                    ?>

                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control tablname_new form-select js-example-basic-single">
                                                        <option value=""><?= __('Select Table Name') ?></option>
                                                        <?php foreach($list as $result){?>
                                                            <option value="<?php echo $result['Tables_in_hrstaq_demo_2']; ?>" <?php echo $role['application_table_name']==$result['Tables_in_hrstaq_demo_2']?"selected":""; ?>><?php echo ucwords(str_replace("_"," ",($result['Tables_in_hrstaq_demo_2']))); ?></option>
                                                        <?php } ?>
                                                    <?php if(!isset($_GET['table'])){ ?>
                                                        <option value="-1"><?= __('Create New Table') ?></option>
                                                    <?php } ?>
                                                    </select>
                                                    <input aria-label="<?= htmlspecialchars(__('Enter Table Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control space tablname mt-3" placeholder="<?= __('Enter Table Name') ?>" name="tablename" type="text" value="<?php echo $role['application_table_name'];?>"  style="display:none"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <span  class="col-4 col-form-label"><?= __('Page Title') ?></span>
                                                <div class="col-8">
                                                    <input aria-label="<?= htmlspecialchars(__('Formname'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control formname" name="formname" type="text" value="<?php echo $role['page_title']; ?>" id="" Required/>
                                                </div>
                                            </div>
                                            <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5">
                                                <div class="new_fields">

                                                    <?php
                                                    $m=0;
                                                    foreach($attribute as $key=>$result){


                                                        ?>
                                                        <div class="card card_count deleterow<?php echo $m; ?>">
                                                            <div class="card-header p-2 f-w-600" id="headingOne<?php echo $m; ?>">
                                                                <div class="card-title collapsed textboxclass<?php echo $m; ?>" data-bs-toggle="collapse" data-bs-target="#collapseOnee<?php echo $m; ?>">
                                                                    <button type="button" class="btn p-0 border-0 bg-transparent" onclick="deleterowinputtype(<?php echo $m; ?>)" aria-label="<?= htmlspecialchars(__('Delete row'), ENT_QUOTES, 'UTF-8'); ?>"><i class="flaticon2-cancel-music"></i></button> <?php echo $result['lable'];?>
                                                                </div>
                                                            </div>
                                                            <div id="collapseOnee<?php echo $m; ?>" class="collapse" data-parent="#accordionExample5">
                                                                <div class="card-body">
                                                                    <div class="example-preview">
                                                                        <ul class="nav nav-tabs md-tabs infisuite_tab">
                                                                            <li class="nav-item">
                                                                                <a class="nav-link active" id="content_<?php echo $m; ?>" data-bs-toggle="tab" href="#content-<?php echo $m; ?>"   >
                                                                                    <span class="nav-text"><?= __('Content'); ?></span>
                                                                                </a>
                                                                            </li>
                                                                            <li class="nav-item">
                                                                                <a class="nav-link" id="advanced_<?php echo $m; ?>" data-bs-toggle="tab" href="#advanced-<?php echo $m; ?>"  aria-controls="profile" >
                                                                                    <span class="nav-text"><?= __('Advanced'); ?></span>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                        <div class="tab-content mt-5" id="myTabContent4">
                                                                            <div class="tab-pane fade show active" id="content-<?php echo $m; ?>" role="tabpanel" aria-labelledby="content_1">
                                                                                <div class="form-group row">
                                                                                    <span class="col-6 col-form-label"><?= __('Field Name') ?></span>


                                                                                    <div class="col-6">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementor_fieldname space elementor_fieldname<?php echo $m; ?>" data-id="<?php echo $m; ?>"  type="text" name="fieldname[]" value="<?php echo $key; ?>"  <?php if(isset($_GET['Rid']) || isset($_GET['table'])){ echo "readonly";
} ?>/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span  class="col-6 col-form-label "><?= __('Field Type') ?></span>
                                                                                    <div class="col-6">
                                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class=" elementor_fieldtype elementor_fieldtype<?php echo $m; ?> form-select js-example-basic-single" name="elementorfieldtype[]" data-elementory_id="inputbox<?php echo $m; ?>" data-id="<?php echo $m; ?>"  >
                                                                                            <option value="varchar" data-select2-id="42" <?php echo $result['elementorfieldtype']=="varchar"? "selected":""; ?>><?= __('String') ?></option>
                                                                                            <option value="text" <?php echo $result['elementorfieldtype']=="text"? "selected":""; ?>><?= __('Description') ?></option>
                                                                                            <option value="int" <?php echo  $result['elementorfieldtype']=="int"? "selected":""; ?>><?= __('Number') ?></option>
                                                                                            <option value="decimal" <?php echo  $result['elementorfieldtype']=="double"? "selected":""; ?> ><?= __('Decimal') ?></option>
                                                                                            <option value="date" <?php echo  $result['elementorfieldtype']=="date"? "selected":""; ?> ><?= __('Date') ?></option>
                                                                                            <option value="datetime" <?php echo  $result['elementorfieldtype']=="datetime"? "selected":""; ?> ><?= __('Date Time') ?></option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span class="col-6 col-form-label"><?= __('Position') ?></span>
                                                                                    <div class="col-6">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Display Postion'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control" type="text" name="display_postion[]" value="<?php echo $result['display_postion']; ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span class="col-6 col-form-label"><?= __('Label & Input One Line') ?></span>
                                                                                    <div class="col-6">
                                                                                        <span class="switch switch-icon">
                                                                                            <label>
                                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" data-id="<?php echo $m; ?>" class="js-switch elementory_labelandinputtype" data-elementory_id="inputbox<?php echo $m; ?>" data-label="inputlabel<?php echo $m; ?>" data-div="divbox<?php echo $m; ?>" <?php echo  $result['labelandinputineline']=="0"? "checked":""; ?>  name="labelandinputineline[<?php echo $m;?>]" />
                                                                                                <span></span>
                                                                                            </label>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>

<div class="form-group row">
    <span  class="col-6 col-form-label "><?= __('Type') ?></span>
    <div class="col-6">
        <select aria-label="<?= htmlspecialchars(__('Elementorytexttype'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control elementor_text_box_type form-select js-example-basic-single" onchange="Getinserttype(this.id,this.value)" id="type" name="elementorytexttype[]" data-elementory_id="inputbox<?php echo $m; ?>" data-id="<?php echo $m; ?>"  data-iddd="<?php echo $role['application_table_name']."#".$key; ?>">
            <?php echo optiontype($result['type'],$filed_array); ?>
        </select>
    </div>
</div>

<div class="form-group row">
    <span  class="col-6 col-form-label"><?= __('Label') ?></span>
    <div class="col-6">
        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementory_lable elementory_lable<?php echo $m; ?>" data-labletext="labletext<?php echo $m; ?>" data-elementory_id="inputlabel<?php echo $m; ?>" data-id="<?php echo $m; ?>"  type="text"  value="<?php echo str_replace("_"," ",$result['lable']);?>" name="elementorylabel[]" />
    </div>
</div>
                                                                                <div class="form-group row">
                                                                                    <span class="col-6 col-form-label"><?= __('Display') ?></span>
                                                                                    <div class="col-6">
                                                                                        <span class="switch switch-icon">
                                                                                            <label>
                                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" class="js-switch" type="checkbox"  <?php echo  $result['display_list']=="1"? "checked":""; ?>  name="display[<?php echo $m;?>]" />

                                                                                            </label>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group row">
                                                                                    <span class="col-6 col-form-label"><?= __('Insert Display List') ?></span>
                                                                                    <div class="col-6">
                                                                                        <span class="switch switch-icon">
                                                                                            <label>
                                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="display_status js-switch" data-id="<?php echo $m; ?>" <?php echo  $result['insert_display_list']=="1"?"checked":""; ?>  name="insert_display_list[<?php echo $m; ?>]" />
                                                                                                <span></span>
                                                                                            </label>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <span  class="col-6 col-form-label"><?= __('Accordion'); ?></span>
                                                                                    <div class="col-6">
                                                                                        <select aria-label="<?= htmlspecialchars(__('Accordion'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control form-select js-example-basic-single
                                                                                        " name="accordion[]" >
                                                                                        <option value="N" <?php echo $result['accordion']=="N"? "selected":""; ?>><?= __('Normal') ?></option>
                                                                                        <option value="R" <?php echo $result['accordion']=="R"? "selected":""; ?>><?= __('Right') ?></option>
                                                                                        <option value="L" <?php echo $result['accordion']=="L"? "selected":""; ?>><?= __('Left') ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Wizard'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Insert View Wizard'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control form-select js-example-basic-single" id="exampleSelect1" name="insert_view_wizard[]">
                                                                                        <option value="1" <?php echo $result['insert_view_wizard']=="1"? "selected":""; ?>><?= __('1- Wizard') ?></option>
                                                                                        <option value="2" <?php echo $result['insert_view_wizard']=="2"? "selected":""; ?>><?= __('2- Wizard') ?></option>
                                                                                        <option value="3" <?php echo $result['insert_view_wizard']=="3"? "selected":""; ?>><?= __('3- Wizard') ?></option>
                                                                                        <option value="4" <?php echo $result['insert_view_wizard']=="4"? "selected":""; ?>><?= __('4- Wizard') ?></option>
                                                                                        <option value="5" <?php echo $result['insert_view_wizard']=="5"? "selected":""; ?>><?= __('5- Wizard') ?></option>
                                                                                        <option value="6" <?php echo $result['insert_view_wizard']=="6"? "selected":""; ?>><?= __('6- Wizard') ?></option>
                                                                                        <option value="7" <?php echo $result['insert_view_wizard']=="7"? "selected":""; ?>><?= __('7- Wizard') ?></option>
                                                                                        <option value="8" <?php echo $result['insert_view_wizard']=="8"? "selected":""; ?>><?= __('8- Wizard') ?></option>
                                                                                        <option value="9" <?php echo $result['insert_view_wizard']=="9"? "selected":""; ?>><?= __('9- Wizard') ?></option>
                                                                                        <option value="10" <?php echo $result['insert_view_wizard']=="10"? "selected":""; ?>><?= __('10- Wizard') ?></option>
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
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" data-requerd= "labletextrequerd<?php echo $m; ?>" data-label="inputlabel<?php echo $m; ?>" data-div="divbox<?php echo $m; ?>" class="js-switch elementory_required" data-elementory_id="inputbox<?php echo $m; ?>" name="elementoryrequerd[<?php echo $m;?>]"  <?php echo $result['mandatory']=="1"? "checked":""; ?>/>
                                                                                            <span></span>
                                                                                        </label>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-6 col-form-label"><?= __('Column Width'); ?></span>
                                                                                <div class="col-6">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control elementory_width form-select js-example-basic-single" data-id="<?php echo $m; ?>" data-elementory_id="divbox<?php echo $m; ?>" name="elementorycolunmwidth[]"  data-select2-id="" tabindex="-1">
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
                                                                                        <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control valuesdefoult" data-id="<?php echo $m; ?>" name="valuesdefoult[]" value="<?php echo $result['valuesdefoult']; ?>" placeholder="<?= __('Search for...') ?>">
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
                                                                    <button type="button" class="btn btn-primary mr-3 addnewfillds"><i class="flaticon2-plus"></i> <?= __('ADD Item'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header p-2 f-w-600" id="headingThreefileupload3">
                                                        <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThreefileupload3"><?= __('File Uploads'); ?>
                                                        </div>
                                                    </div>
                                                    <div id="collapseThreefileupload3" class="collapse" data-parent="#accordionExample3">
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <span class="col-5 col-form-label"><?= __('File uploading Display'); ?></span>
                                                                <div class="col-7">
                                                                    <span class="switch switch-icon">
                                                                        <label>
                                                                            <input aria-label="<?= htmlspecialchars(__('Fileuplodingdisplay'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"  name="fileuplodingdisplay" class="js-switch fileuplodingdisplay" onclick="fileuploadingcheck()"  <?php echo $file_upload['file_upload']=="0"? "checked":""; ?> onkeydown=""> <span></span>
                                                                        </label>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <span  class="col-5 col-form-label"><?= __('Label') ?></span>
                                                                <div class="col-7">
                                                                    <input aria-label="<?= htmlspecialchars(__('Fileuplodinglabel'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodinglablel" type="text" name="fileuplodinglabel"  value="<?php echo $file_upload['fileuplodinglabel']!=""?$file_upload['fileuplodinglabel']:"File Upload"; ?>" />
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <span  class="col-5 col-form-label"><?= __('Width'); ?></span>
                                                                <div class="col-7">
                                                                    <select aria-label="<?= htmlspecialchars(__('Fileuplodingwidth'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodingwidth form-select js-example-basic-single" name="fileuplodingwidth">

                                                                        <?php echo  optiontype($file_upload['fileuplodingwidth']!=""?$file_upload['fileuplodingwidth']:"6",$widtharray); ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mt-5">
                                                                <span class="col-5 col-form-label"><?= __('Delete Files Display'); ?></span>
                                                                <div class="col-7">
                                                                    <span class="switch switch-icon">
                                                                        <label>
                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"    <?php if(isset($_GET['Rid']) || isset($_GET['table'])){ echo $file_upload['fileuplodingdeleteddisplay']=="0"? "checked":"";
}else{ echo "checked";
} ?>  class="fileuplodingdeleteddisplay js-switch" name="fileuplodingdeleteddisplay" />
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <span  class="col-5 col-form-label"><?= __('Delete width'); ?></span>
                                                                <div class="col-7">
                                                                    <select aria-label="<?= htmlspecialchars(__('Fileuplodingdleetewidth'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fileuplodingdleetewidth form-select js-example-basic-single"  name="fileuplodingdleetewidth" >
                                                                        <?php echo optiontype($file_upload['fileuplodingdleetewidth']!=""?$file_upload['fileuplodingdleetewidth']:"6",$widtharray); ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header p-2 f-w-600" id="headingTwo3">
                                                        <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo3"><?= __('Buttons'); ?>
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
                                                                    <div class="card-header p-2 f-w-600" id="headingOnebutton<?php echo $k; ?>">
                                                                        <div class="card-title collapsed title<?php echo $k; ?>" data-bs-toggle="collapse" data-bs-target="#collapseOneebutton<?php echo $k; ?>" aria-expanded="false">
                                                                            <button type="button" class="btn p-0 border-0 bg-transparent" onclick="deletebutton(<?php echo $k; ?>)" aria-label="<?= htmlspecialchars(__('Delete button'), ENT_QUOTES, 'UTF-8'); ?>"><i class="flaticon2-cancel-music"></i></button> <?php echo  $row['buttonname']; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div id="collapseOneebutton<?php echo $k; ?>" class="collapse" data-parent="#accordionExample5" style="">
                                                                        <div class="card-body">
                                                                            <div class="form-group row">
                                                                                <span  class="col-5 col-form-label"><?= __('Function Type'); ?></span>
                                                                                <div class="col-7">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control function_type function_type<?php echo $k; ?> form-select js-example-basic-single" data-id="<?php echo $k; ?>" data-select2-id="" name="functiontype[]" tabindex="-1">
                                                                                        <option value="save" <?php echo $row['functiontype']=="2"? "selected":""; ?> ><?= __('save') ?></option>
                                                                                        <option value="saveandcontinue" <?php echo $row['functiontype']=="saveandcontinue"? "selected":""; ?>><?= __('Save & continue') ?></option>
                                                                                        <option value="update" <?php echo $row['functiontype']=="update"? "selected":""; ?> ><?= __('Update') ?></option>
                                                                                        <option value="print" <?php echo $row['functiontype']=="print"? "selected":""; ?>><?= __('Print') ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-5 col-form-label"><?= __('Size'); ?></span>
                                                                                <div class="col-7">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control button_size button_sizes<?php echo $k; ?> form-select js-example-basic-single" id="" data-id="<?php echo $k; ?>" name="buttonsize[]" data-select2-id="" tabindex="-1">
                                                                                        <option value="btn-xs" <?php echo $row['buttonsize']=="btn-xs"? "selected":""; ?>><?= __('Extra Small') ?></option>
                                                                                        <option value="btn-sm" <?php echo $row['buttonsize']=="btn-sm"? "selected":""; ?>><?= __('Small') ?></option>
                                                                                        <option value="" <?php echo $row['buttonsize']==""? "selected":""; ?>><?= __('Medium') ?></option>
                                                                                        <option value="btn-lg" <?php echo $row['buttonsize']=="btn-lg"? "selected":""; ?>><?= __('Large') ?></option>
                                                                                        <option value="btn-lg btn-block" <?php echo $row['buttonsize']=="btn-lg btn-block"? "selected":""; ?>><?= __('Extra Large') ?></option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span  class="col-5 col-form-label"><?= __('Column Width'); ?></span>
                                                                                <div class="col-7">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control buttonwidth buttonwidth<?php echo $k; ?> form-select js-example-basic-single" id="" data-id="<?php echo $k; ?>" name="buttonwidth[]" data-select2-id="" tabindex="-1">
                                                                                        <option value="12" <?php echo $row['buttonwidth']=="12"? "selected":""; ?>><?= __('Default') ?></option>
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
                                                                    <button type="button" class="btn btn-primary mr-3 addnewbutton"><i class="flaticon2-plus"></i> <?= __('Add Button'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header p-2 f-w-600" id="headingThree3">
                                                                <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree3"><?= __('Email'); ?>
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
                                                            <div class="card-header p-2 f-w-600" id="headingThree4">
                                                                <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour3"><?= __('Steps Settings'); ?>
                                                                </div>
                                                            </div>
                                                            <div id="collapseFour3" class="collapse" data-parent="#accordionExample3">
                                                                <div class="card-body">
                                                                    <div class="form-group row">
                                                                        <span  class="col-5 col-form-label"><?= __('Type') ?></span>
                                                                        <div class="col-7">
                                                                            <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single" id="" name="param" data-select2-id="" >
                                                                                <option value="" data-select2-id=""><?= __('Number & Text') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('None') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Text') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Icon') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Number') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Progress Bar') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Number & Text') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Icon & Text') ?></option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <span  class="col-5 col-form-label"><?= __('Shape'); ?></span>
                                                                        <div class="col-7">
                                                                            <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single"  id="" name="param" data-select2-id="" tabindex="-1">
                                                                                <option value="" data-select2-id=""><?= __('Circle') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Square') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('Rounded') ?></option>
                                                                                <option value="" data-select2-id=""><?= __('None') ?></option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header p-2 f-w-600" id="headingThree5">
                                                                <div class="card-title collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFive5"><?= __('Additional Options'); ?>
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
                                            </div>
                                                <div class="col-lg-8">
                                                    <div class="col-12 ">
                                                        <!--begin::Wizard Body-->
                                                        <div class="row ">
                                                            <div class="col-xl-12">

                                                                <div class="form" id="kt_contact_add_form">

                                                                    <div class="pb-5 " data-wizard-type="step-content">

<div class="card-header">
    <h5 class=" textcapital font-weight-bold formnamehedimg text-dark"><?php echo $role['page_title']!=""?$role['page_title']:"Page Title"; ?></h5>

</div>

                                                                        <div class=" form-group row body_fields p-4">
<?php $s=0; foreach($attribute as $result){   ?>
    <div class="divbox<?php echo $s; ?>  col-lg-<?php echo $result['insert_view_width']; ?> <?php echo (isset($result['labelandinputineline']) && $result['labelandinputineline']==0)? "row":""; ?>  "  style="display: <?php echo $result['insert_display_list']==2?"none":""; ?>"  id="divid<?php echo $s; ?>" >
        <span class="textcapital  col-form-label <?php echo (isset($result['labelandinputineline']) && $result['labelandinputineline']==0)? "col-lg-3":""; ?> text-lg-right inputlabel<?php echo $s; ?>"><span class="labletext<?php echo $s; ?>"><?php echo $result['lable'];?></span><span class="text-danger labletextrequerd<?php echo $s; ?>" style="<?php echo $result['mandatory']==1? 'display:inherit':'display:none'; ?>" >*</span></span>
        <div class="inputtypediv<?php echo $s; ?> <?php echo (isset($result['labelandinputineline']) && $result['labelandinputineline']==0)? "col-lg-9":""; ?>" >
            <?php  textboxes($s,$result['type'],$result['elementorplacehilder'],$result['valuesdefoult']); ?>
        </div>
    </div>
    <?php $s++;
} ?>
                                                                            </div>
                                                                            <div class="fileuploadUig form-group row">
                                                                                <?php if(is_array($file_upload)){    if($file_upload['file_upload']==0 && (isset($_GET['Rid']) || isset($_GET['table']))){  ?>
                                                                                    <div class="col-lg-<?php echo $file_upload['fileuplodingwidth']; ?>  fileuploadui" id="fileuploaduiid">
                                                                                        <span class="fileuplodinglablelui textcapital" ><?php echo $file_upload['fileuplodinglabel']; ?> </span>
                                                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                            <div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
                                                                                                <div class="dropzone-msg dz-message needsclick">
                                                                                                    <h3 class="dropzone-msg-title"><?= __('Drop files here or click to upload.') ?></h3>
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
                                                            <div class="col-lg-10"></div>
                                                            <input type="hidden" name="option" value="COM_AUTO_UI_DESIGNING">
                                                            <button type="submit" class="btn btn-primary mr-2 col-lg-2" name="submit" value="submit" ><i class="la la-check">    </i><?= __('Save Atrributes'); ?></button>
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
