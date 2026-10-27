<?php
$_SESSION['apply_js']['multiselest'] = 1;
$mastertablename = $_GET['TNAME'];
$apiurl = API."?mode=getdisplayattributesnewversion&Tname=".$mastertablename;
$response = callAPI($apiurl);
$result = json_decode($response,1);
$table_list = $result['table_list'];
$mtablefields = $result['table_field'];
$position =10;
foreach($mtablefields as $mtablefield){
    $mtfname = ucwords(str_replace('_',' ',$mtablefield));
    $errer_message = "Please Enter ".$mtfname;
    $mastertablefield[$mtablefield] = array("fieldname"=>$mtablefield,"display_name"=>$mtfname,"type"=>"text","display_position"=>$position,"mandatory"=>"1","insert_view_width"=>6,"placeholder"=>$mtfname,"errer_message"=>$errer_message,"upload_image_size"=>"0","upload_image_width"=>"0","upload_image_height"=>"0","accordions"=>"N","insert_view_wizard"=>1);
    $position = $position+10;
}
$filed_array = array('text' => 'Simple Text', 'touchspin' => 'Touch Spin', 'date_pick' => 'Date','month_year'=>'Month','time_pick'=>'Time' ,'color_pick' => 'Color', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Quantity','currency'=> 'Currency', 'password'=> 'Password', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List','auto_select'=>'Auto Select' ,'multi_select'=> 'Multi Select','rating'=>'Rating','image' => 'Photo','file_upload' => 'Documents Upload','editor' => 'Editor','slider'=>'Slider','number_type'=> 'Number','read_only'=> 'Read Only Text','disable_select'=> 'Disable List','hidden'=> 'Hidden', 'serial_no'=>'Serial Number');
$accordion_array = array('N' => 'Normal','R' => 'Rigth','L' => 'Left');
$startseries = array(array('short_code'=>"1",'label'=>'Start 1 To Till'),array('short_code'=>"a",'label'=>'Start a To Till'),array('short_code'=>"A",'label'=>'Start A To Till'));
?>
<script type="text/javascript">
    function selectTableName(tablename){
        window.location = "index?option=COM_INSERT_DISPLAY_ATTRIBUTES_NEW&TNAME="+tablename;
    }
    function cllViewPageFunction(pagetype){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#viewpageid").html(loader);
        jQuery.ajax({
            url: "views/custom_insert_display_attributes_new_view_page.php",
            data:'ajaxpage=Y&pagetype='+pagetype+'&mastertablename=<?php echo $mastertablename; ?>',
            type: "POST",
            success:function(data){
                $('#viewpageid').html(data);
            },
            error:function (){}
        });
    }
</script>
<div class="card" >
    <div class="card-header">
        <h5>Display Attributes</h5>
        <div class="card-header-right">
            <button type="button" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <span class="form-label d-block">Select Table</span>
                            <select aria-label="Table Name" class="form-select js-example-basic-single" name="table_name"  onchange="selectTableName(this.value)">
                                   <option value="0"> Select Any One</option>
                                   <?php foreach ($table_list as $tablename) {  ?>
                                       <option value="<?php echo $tablename; ?>" <?php echo $mastertablename == $tablename?"selected":"";?>> <?php echo ucwords(str_replace('_',' ',$tablename)); ?></option>
                                   <?php } ?>
                               </select>
                        </div>
                        <div class="row">
                            <ul class="nav md-tabs infisuite_tab ml-2 mb-2">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" data-bs-toggle="tab" role="tab" tabtable" onclick="cllViewPageFunction('T')">List</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" gridview" data-bs-toggle="tab" role="tab" onclick="cllViewPageFunction('G')">Grid</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" tabform" data-bs-toggle="tab" role="tab" onclick="cllViewPageFunction('F')">Form</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" tabview" data-bs-toggle="tab" role="tab" onclick="cllViewPageFunction('V')">View</button>
                                </li>
                            </ul>
                            <div class="tab-content tabs custom-scroll" style="height: 1000px;">
                                <div class="tab-pane card-body active p-0" id="tabtable" role="tabpanel">
                                    <div class="form-group">
                                        <span class="form-label d-block">List Page Option</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <span class="form-label d-block">List Page Title</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <span class="form-label d-block">Sequence No</span>
                                            <input aria-label="Mandatory" type="checkbox" value="0" name="mandatory"  class="display js-primary-sm" <?php echo  $editfield['mandatory']=='0'?"checked":""; ?> >
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span class="form-label d-block">Start Series</span>
                                            <select aria-label="Table Name" class="form-select js-example-basic-single" name="table_name"  onchange="selectTableName(this.value)">
                                                   <option value="0"> Select Any One</option>
                                                   <?php foreach ($startseries as $ss) {  ?>
                                                       <option value="<?php echo $ss['short_code']; ?>" <?php echo $mastertablename == $ss['short_code']?"selected":"";?>> <?php echo $ss['label']; ?></option>
                                                   <?php } ?>
                                               </select>
                                        </div>
                                    </div>
                                <?php foreach($mastertablefield as $keyfield=>$mtf){ ?>
                                    <div class="card">
                                        <div class="card-body row">
                                            <span class="col-lg-8"><?php echo $mtf['display_name']; ?></span>
                                            <div class="col-lg-2">
                                                <input aria-label="Checkbox" type="checkbox" name="display[<?php echo $i; ?>]"  class="display js-primary-sm" data-id="<?php echo $i; ?>"  <?php echo  $value['display']=='0'?"checked":""; ?> >
                                            </div>
                                            <div class="col-lg-2">
                                                <i class="ti-pencil m-l-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="tab-pane card-body p-0" id="gridview" role="tabpanel">
                                        <?php foreach($mastertablefield as $keyfield=>$mtf){ ?>
                                    <div class="card">
                                        <div class="card-body row">
                                            <span class="col-lg-8"><?php echo $mtf['display_name']; ?></span>
                                            <div class="col-lg-2">
                                                <input aria-label="Checkbox" type="checkbox" name="display[<?php echo $i; ?>]"  class="display js-primary-sm" data-id="<?php echo $i; ?>"  <?php echo  $value['display']=='0'?"checked":""; ?> >
                                            </div>
                                            <div class="col-lg-2">
                                                <i class="ti-pencil m-l-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="tab-pane card-body p-0" id="tabform" role="tabpanel">
                                    <div class="form-group">
                                        <span class="form-label d-block">Insert Page Option</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <span class="form-label d-block">Insert Page Title</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>

                                        <?php foreach($mastertablefield as $keyfield=>$mtf){ ?>
                                    <div class="card">
                                        <div class="card-body row">
                                            <span class="col-lg-8"><?php echo $mtf['display_name']; ?></span>
                                            <div class="col-lg-2">
                                                <input aria-label="Checkbox" type="checkbox" name="display[<?php echo $i; ?>]"  class="display js-primary-sm" data-id="<?php echo $i; ?>"  <?php echo  $value['display']=='0'?"checked":""; ?> >
                                            </div>
                                            <div class="col-lg-2">
                                                <i class="ti-pencil m-l-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="tab-pane card-body p-0" id="tabview" role="tabpanel">
                                    <div class="form-group">
                                        <span class="form-label d-block">View Page Option</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <span class="form-label d-block">View Page Title</span>
                                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                                    </div>
                                        <?php foreach($mastertablefield as $keyfield=>$mtf){ ?>
                                    <div class="card">
                                        <div class="card-body row">
                                            <span class="col-lg-8"><?php echo $mtf['display_name']; ?></span>
                                            <div class="col-lg-2">
                                                <input aria-label="Checkbox" type="checkbox" name="display[<?php echo $i; ?>]"  class="display js-primary-sm" data-id="<?php echo $i; ?>"  <?php echo  $value['display']=='0'?"checked":""; ?> >
                                            </div>
                                            <div class="col-lg-2">
                                                <i class="ti-pencil m-l-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" >
                <div class="row" style="position: sticky; top: 0;" id="viewpageid">

                    <?php
                    include_once "custom_insert_display_attributes_new_view_page.php";
                ?>
                </div>
            </div>
            <div class="col-lg-3" >
                <div class="card">
                    <div class="card-header">
                        <h5>Field Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group">
                                <span class="form-label d-block">Display Name</span>
                                <input aria-label="Display Name" class="form-control" type="text" name="display_name" value="<?php echo $editfield['display_name']; ?>">
                            </div>
                            <div class="form-group w-full">
                                <span class="form-label d-block">Type</span>
                                <select aria-label="Type[]" class="form-select js-example-basic-single"  onchange="Getinserttype(this.id,this.value)" name= "type[]">
                                    <?php foreach ($filed_array as $typekey => $typevalue) {?>
                                        <option value ="<?php echo $typekey; ?>"  <?php echo $editfield['type'] == $typekey?"selected":"";?>><?php echo $typevalue; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Display Position</span>
                                <input aria-label="Display Position" class="form-control" type="text" name="display_position" value="<?php echo $editfield['display_position']; ?>">
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label d-block">Mandatory</span>
                                <input aria-label="Mandatory" type="checkbox" value="0" name="mandatory"  class="display js-primary-sm" <?php echo  $editfield['mandatory']=='0'?"checked":""; ?> >
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label d-block">Filter (Search)</span>
                                <input aria-label="Mandatory" type="checkbox" value="0" name="mandatory"  class="display js-primary-sm" <?php echo  $editfield['mandatory']=='0'?"checked":""; ?> >
                            </div>
                            <div class="form-group w-full">
                                <span class="form-label d-block">Width Class</span>
                                <select aria-label="Insert View Width[]" class="form-select js-example-basic-single" id="select_insert_view_width" name= "insert_view_width[]">
                                    <?php for($i=1; $i<13;$i++){
                                        $view_type = "col-lg-".$i; ?>
                                        <option  value = "<?php echo $i; ?>" <?php if($editfield['insert_view_width']== $i){ ?> selected <?php
}?>><?php echo $view_type; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Placeholder</span>
                                <input aria-label="Placeholder" class="form-control" type="text" name="placeholder" value="<?php echo $editfield['placeholder']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Errer Message</span>
                                <input aria-label="Errer Message" class="form-control" type="text" name="errer_message" value="<?php echo $editfield['errer_message']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Upload Image Size</span>
                                <div class="input-group input-group-button">
                                    <input aria-label="Upload Image Size" class="form-control width1" type="text" name="upload_image_size" value="<?php echo $editfield['upload_image_size']; ?>">
                                     <span class="input-group-text addon-style">
                                        <span class="">Bytes</span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Upload Image Witdh</span>
                                <div class="input-group input-group-button">
                                <input aria-label="Upload Image Width" class="form-control width1" type="text" name="upload_image_width" value="<?php echo $editfield['upload_image_width']; ?>">
                                 <span class="input-group-text addon-style">
                                        <span class="">PX</span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Upload Image Height</span>
                                <input aria-label="Upload Image Height" class="form-control" type="text" name="upload_image_height" value="<?php echo $editfield['upload_image_height']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Date Selection Previous Days</span>
                                <input aria-label="Upload Image Height" class="form-control" type="text" name="upload_image_height" value="<?php echo $editfield['upload_image_height']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Date Selection Feature Days</span>
                                <input aria-label="Upload Image Height" class="form-control" type="text" name="upload_image_height" value="<?php echo $editfield['upload_image_height']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Min Number</span>
                                <input aria-label="Upload Image Height" class="form-control" type="text" name="upload_image_height" value="<?php echo $editfield['upload_image_height']; ?>">
                            </div>
                            <div class="form-group">
                                <span class="form-label d-block">Max Number</span>
                                <input aria-label="Upload Image Height" class="form-control" type="text" name="upload_image_height" value="<?php echo $editfield['upload_image_height']; ?>">
                            </div>
                            <div class="form-group w-full">
                                <span class="form-label d-block">Accordion</span>
                                <select aria-label="Accordions[]" class="form-select js-example-basic-single"  id="accordion"  name= "accordions[]">
                                    <?php foreach ($accordion_array as $akey => $aarray) { ?>
                                        <option value = "<?php echo $akey; ?>" <?php if($editfield['accordions'] == $akey) { ?> selected <?php
} ?> ><?php echo $aarray; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group w-full">
                                <span class="form-label d-block">Wizard</span>
                                <select aria-label="Insert View Wizard[]" class="form-select js-example-basic-single" id="select_insert_view_wizard" name= "insert_view_wizard[]">
                                    <?php for($i=1; $i<11;$i++){
                                        $view_type = $i."- Wizard"; ?>
                                        <option  value = "<?php echo $i; ?>" <?php if($editfield['insert_view_wizard']== $i){ ?> selected <?php
}?>><?php echo $view_type; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
