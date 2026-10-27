<?php
if(isset($_POST['save'])){
    $data= array();

    $data['application_table_name'] = $_POST['optiontablename'];
    $data['id'] = $_POST['id'];
    $data['created_date'] = nowDate();
    $hedder = array("heddingposition"=>$_POST['heddingposition'],
        "orderhedding"=>$_POST['orderhedding'],
        "logoposition"=>$_POST['logoposition'],
        "orderlogo"=>$_POST['orderlogo'],
        "addressposition"=>$_POST['addressposition'],
        "addresorder"=>$_POST['addresorder'],
        "heddingdislay"=> (isset($_POST['heddingdislay']))? 0:1,
        "logodislay"=> (isset($_POST['logodislay']))? 0:1,
        "addressdislay"=> (isset($_POST['addressdislay']))? 0:1);
    $header_json = json_encode($hedder);
    $data['header'] = $header_json;
    foreach ($_POST['label'] as $postkey => $postvalue) {
        $data1[$postkey]['label'] = $_POST['label'][$postkey];
        $data1[$postkey]['textfileds'] = $_POST['textfileds'][$postkey];
        $data1[$postkey]['textfiledswidth'] = $_POST['textfiledswidth'][$postkey];
        $data1[$postkey]['textfiledinline'] = (isset($_POST['textfiledinline'][$postkey]))? 0:1;
    }
    $data_json = json_encode($data1);
    $data['content1'] = $data_json;
    foreach ($_POST['tablecolumns'] as $postkey => $postvalue) {
        $data2[$postvalue]['tablecolumns'] = $_POST['tablecolumns'][$postkey];
        $data2[$postvalue]['tablevalue'] = $_POST['tablevalue'][$postkey];
        $data2[$postvalue]['tablefileddisplay'] = (isset($_POST['tablefileddisplay'][$postkey]))? 0:1;
        $data2[$postvalue]['tablevalueposition'] = $_POST['tablevalueposition'][$postkey];
        $data2[$postvalue]['tablefieldsorder'] = $_POST['tablefieldsorder'][$postkey];
        $data2[$postvalue]['tablefieldwidth'] = $_POST['tablefieldwidth'][$postkey];
    }
    $data2_json = json_encode($data2);
    $data['content2'] = $data2_json;
    $footer = array("subtotaltext"=>$_POST['subtotaltext'],
        "subtotaltextcheck"=>(isset($_POST['subtotaltextcheck']))? 0:1,
        "discounttext"=>$_POST['discounttext'],
        "discounttextcheck"=>(isset($_POST['discounttextcheck']))? 0:1,
        "taxtext"=>$_POST['taxtext'],
        "taxtextcheck"=>(isset($_POST['taxtextcheck']))? 0:1,
        "shipingtext"=>$_POST['shipingtext'],
        "shipingtextcheck"=>(isset($_POST['shipingtextcheck']))? 0:1,
        "tablec"=>$_POST['tablec']);
    $footer_json = json_encode($footer);
    $data['footer'] = $footer_json;
    $button = array(
        "printbuttonsdisplay"=>(isset($_POST['printbuttonsdisplay']))? 0:1,
        "downloadbuttonsdisplay"=>(isset($_POST['downloadbuttonsdisplay']))? 0:1,
        "inworddisplay"=>(isset($_POST['inworddisplay']))? 0:1,
        "inwordposition"=>$_POST['inwordposition']);
    $button_json = json_encode($button);
    $data['button'] = $button_json;
    $data['company_id'] = $_SESSION['company_id'];
    $url = API."?mode=insertprintdesigning&method=post";
    $response = file_post_contents($url, $data);
}

if(isset($_GET['Rid'])){
    $rids = encodeId($_GET['Rid']);
    $rids = urldecode($rids);
    $rids = explode(":",$rids);
    $id = $rids['1'];
    $tableurl = API."?mode=getprintdesigning&id=$id";
    $response = callAPI($tableurl);
    $row = json_decode($response,1);
    $header = json_decode($row['header'],1);
    $content1 = json_decode($row['content1'],1);
    $content2 = json_decode($row['content2'],1);
    $footer = json_decode($row['footer'],1);
    $button = json_decode($row['button'],1);

}

function activeClass($class,$value){
    if($value==$class){
        return "Active";
    }else{
        return "";

    }
}
$inputFildsArray = array("No"=>"1","Name"=>"infisuite","Description"=>"software company","UOM"=>"Numbers","Rate"=>"100","Amount"=>"21","Total Amount"=>"121");
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">&nbsp;<?php echo "Custome Invoice";  ?> </h3>
                </div>
                <div class="card-toolbar">
                    <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                        <i class="ki ki-long-arrow-back icon-xs"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row ">
                    <div class="col-lg-12">

                        <div class="form" id="kt_contact_add_form">

                            <div class="pb-5 " data-wizard-type="step-content">
                                <div class="card card-custom gutter-b example example-compact">
                                    <div class="card-body">
                                        <form action="index.php?option=<?php echo $_GET['option'];?>" method="POST">

                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <div class="form-group row">
                                                <div class="col-lg-4 col-md-4 col-sm-12">

                                                    <div class="d-flex flex-column-fluid">
                                                        <div class="container">
                                                            <div class="form-group row" >
                                                                <span class="col-form-label text-right col-lg-3 col-sm-12">Tables</span>
                                                                <div class="col-lg-9 col-md-9 col-sm-12" >
                                                                    <?php if($row['application_table_name']!=""){ ?>

                                                                        <select aria-label="Optiontablename" class="form-control select2-hidden-accessible table_names_select" name="optiontablename" id="kt_select2_1" >
                                                                            <option value="<?php echo $row['application_table_name']; ?>" ><?php echo $row['application_table_name']; ?></option>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <select aria-label="Optiontablename" class="form-control select2-hidden-accessible table_names_select" name="optiontablename" id="kt_select2_1_2" readonly>
                                                                            <option value="" data-select2-id="0">Select table name</option>
                                                                            <?php  $tablesList = getListTables(); foreach($tablesList as $result){ ?>
                                                                                <option value="<?php echo $result['Tables_in_letsasso_infistaq_1']; ?>" <?php echo $row['application_table_name']==$result['Tables_in_letsasso_infistaq_1']?"selected":""; ?> ><?php echo $result['Tables_in_letsasso_infistaq_1']; ?></option>
                                                                            <?php } ?>
                                                                        </select>

                                                                    <?php } ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion accordion-solid accordion-toggle-plus <?php echo $row['application_table_name']==""?"ck-target":""; ?> displaye
                                                        " id="accordionExample3">
                                                        <!---------------------   heade part =------------------------->

                                                        <div class="card">
                                                            <div class="card-header" id="headingOne3">
                                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne3">Header</div>
                                                            </div>
                                                            <div id="collapseOne3" class="collapse show" data-parent="#accordionExample3">
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5">
                                                                        <div class="card">
                                                                            <div class="card-header" id="headingOne5">
                                                                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne5">
                                                                                    Heading
                                                                                </div>
                                                                            </div>
                                                                            <div id="collapseOne5" class="collapse" data-parent="#accordionExample5">
                                                                                <div class="card-body">
                                                                                    <div class="">
                                                                                        <div class="tab-content mt-5" id="myTabContent4">
                                                                                            <div class="tab-pane fade show active btn-sm" id="content-1" role="tabpanel" aria-labelledby="content_1">
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Display</span>
                                                                                                    <div class="col-7">
                                                                                                        <span class="switch switch-icon">
                                                                                                            <label>
                                                                                                                <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['heddingdislay']=='1')?"":"checked"; ?> name="heddingdislay[<?php echo $q; ?>]"  class="heddingdislay" data-id="<?php echo $q; ?>" ><span></span>
                                                                                                            </label>
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class=" row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Position</span>
                                                                                                    <input type="hidden" name="heddingposition" id="heddingposition" value="<?php echo $header['heddingposition'];?>">
                                                                                                    <div class="col-7">
                                                                                                        <button  value="text-left" type="button" class="btn header_position_hedding btn-outline-secondary <?php echo activeClass("text-left",$header['heddingposition']); ?>">
                                                                                                            <i class="flaticon2-soft-icons"></i>
                                                                                                        </button>
                                                                                                        <button type="button" value="text-center" class="btn btn-outline-secondary header_position_hedding <?php echo activeClass("text-center",$header['heddingposition']); ?>">
                                                                                                            <i class="fas fa-align-center"></i>
                                                                                                        </button>
                                                                                                        <button type="button"  value="text-right"  class="btn btn-outline-secondary header_position_hedding <?php echo activeClass("text-right",$header['heddingposition']); ?>">
                                                                                                            <i class="fas fa-align-right"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Order Position</span>
                                                                                                    <div class="col-7">
                                                                                                        <input aria-label="Input field" type="text" value="<?php echo isset($header['orderhedding'])?$header['orderhedding']:"1";?>" name="orderhedding"  class="header_order_position form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card">
                                                                            <div class="card-header" id="headingOne5_2">
                                                                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne6">
                                                                                    Logo
                                                                                </div>
                                                                            </div>
                                                                            <div id="collapseOne6" class="collapse" data-parent="#accordionExample5">
                                                                                <div class="card-body">
                                                                                    <div class="">
                                                                                        <div class="tab-content mt-5" id="myTabContent4_2">
                                                                                            <div class="tab-pane fade show active btn-sm" id="content-1_2" role="tabpanel" aria-labelledby="content_1">
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Display</span>
                                                                                                    <div class="col-7">
                                                                                                        <span class="switch switch-icon">
                                                                                                            <label>
                                                                                                                <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['logodislay']=='1')?"":"checked"; ?> name="logodislay[<?php echo $q; ?>]"  class="logodislay" data-id="<?php echo $q; ?>" ><span></span>
                                                                                                            </label>
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class=" row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Position</span>
                                                                                                    <input type="hidden" name="logoposition" id="logoposition" value="<?php  echo isset($header['logoposition'])?$header['orderlogo']:"text-right";?>">
                                                                                                    <div class="col-7">
                                                                                                        <button  value="text-left" type="button" class="btn header_position_logo btn-outline-secondary <?php echo activeClass("text-left",$header['logoposition']); ?>">
                                                                                                            <i class="flaticon2-soft-icons"></i>
                                                                                                        </button>
                                                                                                        <button type="button" value="text-center" class="btn btn-outline-secondary header_position_logo <?php echo activeClass("text-center",$header['logoposition']); ?>">
                                                                                                            <i class="fas fa-align-center"></i>
                                                                                                        </button>
                                                                                                        <button type="button"  value="text-right"  class="btn btn-outline-secondary header_position_logo <?php echo activeClass("text-right",$header['logoposition']); ?>">
                                                                                                            <i class="fas fa-align-right"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Order Position</span>
                                                                                                    <div class="col-7">
                                                                                                        <input aria-label="Input field" type="text" value="<?php  echo isset($header['orderlogo'])?$header['orderlogo']:"3";?>"  name="orderlogo" class="header_order_logo form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card">
                                                                            <div class="card-header" id="headingOne7">
                                                                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne7">
                                                                                    Address
                                                                                </div>
                                                                            </div>
                                                                            <div id="collapseOne7" class="collapse" data-parent="#accordionExample5">
                                                                                <div class="card-body">
                                                                                    <div class="">
                                                                                        <div class="tab-content mt-5" id="myTabContent4_3">
                                                                                            <div class="tab-pane fade show active btn-sm" id="content-1_3" role="tabpanel" aria-labelledby="content_1">
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Display</span>
                                                                                                    <div class="col-7">
                                                                                                        <span class="switch switch-icon">
                                                                                                            <label>
                                                                                                                <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['addressdislay']=='1')?"":"checked"; ?> name="addressdislay[<?php echo $q; ?>]"  class="addressdislay" data-id="<?php echo $q; ?>" ><span></span>
                                                                                                            </label>
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class=" row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Position</span>
                                                                                                    <input type="hidden" name="addressposition" id="addressposition" value="<?php  echo isset($header['addressposition'])?$header['addressposition']:"";?>">

                                                                                                    <div class="col-7">
                                                                                                        <button  value="text-left" type="button" class="btn btn-outline-secondary header_position_address <?php echo activeClass("text-left",$header['addressposition']); ?>">
                                                                                                            <i class="flaticon2-soft-icons"></i>
                                                                                                        </button>
                                                                                                        <button type="button" value="text-center" class="btn btn-outline-secondary header_position_address <?php echo activeClass("text-center",$header['addressposition']); ?>">
                                                                                                            <i class="fas fa-align-center"></i>
                                                                                                        </button>
                                                                                                        <button type="button"  value="text-right"  class="btn btn-outline-secondary header_position_address <?php echo activeClass("text-right",$header['addressposition']); ?>">
                                                                                                            <i class="fas fa-align-right"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row mt-2">
                                                                                                    <span  class="col-5 col-form-label">Order Position</span>
                                                                                                    <div class="col-7">
                                                                                                        <input aria-label="Input field" type="text" value="<?php  echo isset($header['addresorder'])?$header['addresorder']:"6";?>" name="addresorder" class="header_order_address form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--------------------- END  heade part =------------------------->

                                                        <!--------------------- start body part =------------------------->
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne4">
                                                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne4">Body</div>
                                                            </div>
                                                            <div id="collapseOne4" class="collapse" data-parent="#accordionExample3">
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5_2">
                                                                        <div class="bodytextfield">

                                                                            <?php $q=0; foreach($content1 as $content1){ ?>

                                                                                <div class="card">
                                                                                    <div class="card-header" id="headingOnee<?php echo $q; ?>">
                                                                                        <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOnee<?php echo $q; ?>">
                                                                                            Text Fields
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="collapseOnee<?php echo $q; ?>" class="collapse" data-parent="#accordionExample5">
                                                                                        <div class="card-body">
                                                                                            <div class="">
                                                                                                <div class="tab-content mt-5" id="myTabContent<?php echo $q; ?>">
                                                                                                    <div class="tab-pane fade show active btn-sm" id="content-1_4" role="tabpanel" aria-labelledby="content_1">
                                                                                                        <div class="row mt-2">
                                                                                                            <span  class="col-5 col-form-label">Label</span>
                                                                                                            <div class="col-7">
                                                                                                                <input aria-label="Input field" class="form-control textfiledslabel"  data-id="<?php echo $q; ?>" name="label[]" name="textfiledslabel[]" type="text" value="<?php echo $content1['label']; ?>" >
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row mt-2">
                                                                                                            <span  class="col-5 col-form-label">Field</span>
                                                                                                            <div class="col-7">
                                                                                                                <select aria-label="Textfileds[]" class="form-control" name="textfileds[]">
                                                                                                                    <?php
                                                                                                                    $tableattributes= getListAttributes($row['application_table_name']);
                                                                                                                    foreach($tableattributes as $result){ ?>
                                                                                                                        <option value='<?php echo $result['Field']; ?>' <?php echo $result['Field']==$content1['textfileds']?"selected":""; ?>><?php echo $result['Field']; ?></option>";
                                                                                                                    <?php } ?>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row mt-2">
                                                                                                            <span  class="col-5 col-form-label">Column Width</span>
                                                                                                            <div class="col-7">
                                                                                                                <select aria-label="Select option" class="form-control textfiledswidth textfiledswidth<?php echo $q; ?>" data-id="<?php echo $q; ?>" name="textfiledswidth[]">
                                                                                                                    <?php echo optiontype($content1['textfiledswidth'],$widtharray); ?>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="row mt-2">
                                                                                                            <span  class="col-5 col-form-label">Single Line</span>
                                                                                                            <div class="col-7">
                                                                                                                <span class="switch switch-icon">
                                                                                                                    <label>
                                                                                                                        <input aria-label="Checkbox" type="checkbox" <?php echo ($content1['textfiledinline']=='0')?"checked":""; ?> name="textfiledinline[<?php echo $q; ?>]"  class="textfiledinline" data-id="<?php echo $q; ?>" ><span></span>
                                                                                                                    </label>
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div></div></div></div></div></div>

                                                                                                    <?php $q++;
} ?>

                                                                                                </div>
                                                                                                <button type="button" class="btn btn-primary font-weight-bold add_new_textfields"">Add New</button>
                                                                                                <input type="hidden" id="maxcolumns" value="<?php echo $q; ?>">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!--------------------- END  body part =------------------------->

                                                                                <!--------------------- Start Table part =------------------------->

                                                                                <div class="card">
                                                                                    <div class="card-header" id="headingOnetable">
                                                                                        <div class="card-title" data-toggle="collapse" data-target="#collapseOnetable">Table</div>
                                                                                    </div>
                                                                                    <div id="collapseOnetable" class="collapse" data-parent="#accordionExample3">
                                                                                        <div class="card-body">
                                                                                            <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5_3">

    <div class="tableb_displaycss" >
        <?php $c=0; foreach($inputFildsArray as $key => $value){  ?>
            <input type="hidden" name="tablecolumns[]" value="<?php echo $key; ?>">
            <input type="hidden" name="tablevalue[]" value="<?php echo $value; ?>">
            <div class="card">
                <div class="card-header" id="headingOnetable<?php echo $c;?>">
                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOnetable<?php echo $c;?>" aria-expanded="false">
                        <?php echo $key; ?>

                    </div>
                </div>
                <div id="collapseOnetable<?php echo $c; ?>" class="collapse" data-parent="#accordionExample5" style="">
                    <div class="card-body">
                        <div class="">
                            <div class="tab-content mt-5" id="myTabContent71">
                                <div class="tab-pane fade show active btn-sm" id="content-1_5" role="tabpanel" aria-labelledby="content_1">
                                    <div class="row mt-2">
                                        <span  class="col-5 col-form-label">Display</span>
                                        <div class="col-7 d-flex">
                                            <span class="switch switch-icon"><label>
                                                <input aria-label="Checkbox" type="checkbox" class="tablefiledsstatus" name="tablefileddisplay[<?php echo $c; ?>]"  data-value="tablefileds<?php echo $c; ?>"  <?php echo $content2[$key]['tablefileddisplay']=='1'?"":"checked"; ?>   ><span></span>
                                            </label></span>
                                        </div>
                                    </div>
                                    <div class=" row mt-2">
                                        <span  class="col-5 col-form-label">Positon</span>
                                        <input type="hidden" name="tablevalueposition[]"  id="tablevalueposition<?php echo $c; ?>" value="<?php echo $content2[$key]['tablevalueposition']; ?>">
                                        <div class="col-7">
                                            <button  value="text-left" type="button" class="btn btn-outline-secondary tablevalueposition <?php echo activeClass("text-left",$content2[$key]['tablevalueposition']); ?>" data-id="<?php echo $c; ?>">
                                                <i class="flaticon2-soft-icons"></i>
                                            </button>
                                            <button type="button" value="text-center" class="btn btn-outline-secondary tablevalueposition <?php echo activeClass("text-center",$content2[$key]['tablevalueposition']); ?>" data-id="<?php echo $c; ?>">
                                                <i class="fas fa-align-center"></i>
                                            </button>
                                            <button type="button"  value="text-right"  class="btn btn-outline-secondary tablevalueposition <?php echo activeClass("text-right",$content2[$key]['tablevalueposition']); ?>" data-id="<?php echo $c; ?>">
                                                <i class="fas fa-align-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class=" row mt-2">
                                        <span  class="col-5 col-form-label">Width</span>
                                        <div class="col-7">
                                            <input aria-label="10" type="text" placeholder="10" class="form-control tablefieldwidth" name="tablefieldwidth[]"  data-id="<?php echo $c; ?>" value="<?php echo $content2[$key]['tablefieldwidth']!=""?$content2[$key]['tablefieldwidth']:""; ?>" >
                                        </div>
                                    </div>
                                    <div class=" row mt-2">
                                        <span  class="col-5 col-form-label">Priority</span>
                                        <div class="col-7">
                                            <input aria-label="Tablefieldsorder[]" type="text" class="form-control" name="tablefieldsorder[]"  value="<?php echo (isset($content2[$key]['tablefieldsorder']) && $content2[$key]['tablefieldsorder']=="")?$content2[$key]['tablefieldsorder']:$c; ?>" >
                                        </div>
                                    </div>

                                </div></div></div></div></div></div>
                                <?php $c++;
} ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--------------------- END  Table part =------------------------->
            <!--------------------- Start Table part =------------------------->

            <div class="card tableb_displaycss">
                <div class="card-header" id="headingOne444">
                    <div class="card-title" data-toggle="collapse" data-target="#collapseOne444">Footer</div>
                </div>
                <div id="collapseOne444" class="collapse" data-parent="#accordionExample3">
                    <div class="card-body">
                        <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5_4">
                            <div class="card">
                                <div class="card-header" id="headingOne444_2">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOnesubtotal">
                                        Sub Total
                                    </div>
                                </div>
                                <div id="collapseOnesubtotal" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_4">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_6" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Label</span>
                                                        <div class="col-7">
                                                            <input aria-label="Subtotaltext" class="form-control subtotaltext" name="subtotaltext" type="text" value="<?php echo isset($footer['subtotaltext'])?$footer['subtotaltext']:"SUBTOTAL"; ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Subtotaltextcheck" type="checkbox" class="subtotaltextcheck"  name="subtotaltextcheck" <?php echo $footer['subtotaltextcheck']==1?"":"checked"; ?>  ><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingOne444_3">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#Discountacord">
                                        Discount
                                    </div>
                                </div>
                                <div id="Discountacord" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_5">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_7" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Label</span>
                                                        <div class="col-7">
                                                            <input aria-label="Discounttext" class="form-control discounttext" name="discounttext" type="text" value="<?php echo isset($footer['discounttext'])?$footer['discounttext']:"DISCOUNT"; ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Discounttextcheck" type="checkbox" class="discounttextcheck" name="discounttextcheck" <?php echo $footer['discounttextcheck']==1?"":"checked"; ?>  ><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingOne444_4">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#GSTaccording">
                                        Tax
                                    </div>
                                </div>
                                <div id="GSTaccording" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_6">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_8" role="tabpanel" aria-labelledby="content_1">

                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Label</span>
                                                        <div class="col-7">
                                                            <input aria-label="Taxtext" class="form-control taxtext"  type="text" name="taxtext" value="<?php echo isset($footer['taxtext'])?$footer['taxtext']:"TAX"; ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Taxtextcheck" type="checkbox" class="taxtextcheck" name="taxtextcheck" <?php echo $footer['taxtextcheck']==1?"":"checked"; ?>  ><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingOne444_5">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#shipingaccording">
                                        Shiping
                                    </div>
                                </div>
                                <div id="shipingaccording" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_7">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_9" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Label</span>
                                                        <div class="col-7">
                                                            <input aria-label="Shipingtext" class="form-control shipingtext"  type="text" name="shipingtext" value="<?php echo isset($footer['shipingtext'])?$footer['shipingtext']:"SHIPPING/HANDLING"; ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Shipingtextcheck" type="checkbox" class="shipingtextcheck"  name="shipingtextcheck"
                                                                <?php echo $footer['shipingtextcheck']==1?"":"checked"; ?>  ><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingOne444_6">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOnetotalamount">
                                        Total Amount
                                    </div>
                                </div>
                                <div id="collapseOnetotalamount" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_8">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_10" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Label</span>
                                                        <div class="col-7">
                                                            <input aria-label="Tablec" class="form-control tablec"  type="text" name="tablec" value="<?php echo isset($footer['tablec'])?$footer['tablec']:"TOTAL AMOUNT"; ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingOnebutton">
                    <div class="card-title" data-toggle="collapse" data-target="#collapseOnebutton">Buttons</div>
                </div>
                <div id="collapseOnebutton" class="collapse" data-parent="#accordionExample3">
                    <div class="card-body">
                        <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5_5">
                            <div class="card">
                                <div class="card-header" id="headingOnebutton_2">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseprint">
                                        Print
                                    </div>
                                </div>
                                <div id="collapseprint" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_9">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_11" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Printbuttonsdisplay" type="checkbox" class="printbuttonsdisplay" name="printbuttonsdisplay"  <?php echo $button['printbuttonsdisplay']==1?"":"checked"; ?>><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingOnebutton_3">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOneprint">
                                        Download
                                    </div>
                                </div>
                                <div id="collapseOneprint" class="collapse" data-parent="#accordionExample5">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="tab-content mt-5" id="myTabContent4_10">
                                                <div class="tab-pane fade show active btn-sm" id="content-1_12" role="tabpanel" aria-labelledby="content_1">
                                                    <div class="row mt-2">
                                                        <span  class="col-5 col-form-label">Display</span>
                                                        <div class="col-7 d-flex">
                                                            <span class="switch switch-icon"><label>
                                                                <input aria-label="Checkbox" type="checkbox" class="downloadbuttonsdisplay" <?php echo $button['downloadbuttonsdisplay']==1?"":"checked"; ?> name="downloadbuttonsdisplay"><span></span>
                                                            </label></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingOneinword">
                    <div class="card-title" data-toggle="collapse" data-target="#collapseOneinword">In Word</div>
                </div>
                <div id="collapseOneinword" class="collapse" data-parent="#accordionExample3">
                    <div class="card-body">
                        <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5_6">
                            <div class="row mt-2">
                                <span  class="col-5 col-form-label">Display</span>
                                <div class="col-7 d-flex">
                                    <span class="switch switch-icon"><label>
                                        <input aria-label="Checkbox" type="checkbox" class="inworddisplay" <?php echo $button['inworddisplay']==1?"":"checked"; ?> name="inworddisplay"><span></span>
                                    </label></span>
                                </div>
                            </div>
                            <div class=" row mt-2">
                                <span  class="col-5 col-form-label">Positon</span>
                                <input type="hidden" name="inwordposition" id="inwordposition" value="<?php echo $button['inwordposition']; ?>">
                                <div class="col-7">
                                    <button  value="text-left" type="button" class="btn btn-outline-secondary inwordposition" >
                                        <i class="flaticon2-soft-icons"></i>
                                    </button>
                                    <button type="button" value="text-center" class="btn btn-outline-secondary inwordposition" >
                                        <i class="fas fa-align-center"></i>
                                    </button>
                                    <button type="button"  value="text-right"  class="btn btn-outline-secondary inwordposition" >
                                        <i class="fas fa-align-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0 <?php echo $row['application_table_name']==""?"ck-target":""; ?>  displaye">
            <div class="col-md-9">
                <div class="d-flex justify-content-between">
                    <button type="submit" name="save" value="save" class="btn btn-primary btn-sm font-weight-bold" > Save </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12 prints <?php echo $row['application_table_name']==""?"ck-target":"displaye"; ?>  displaye">
        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="card card-custom overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row justify-content-center pt-8 px-8 px-md-0">
                            <div class="col-md-12">

                                <?php
                                function  headerFields($seq, $headers,$id=""){
                                    if($id==""){
                                        $hedding = 1;
                                        $logo = 3;
                                        $addres = 6;
                                    }else{
                                        $hedding = $headers['orderhedding'];
                                        $logo = $headers['orderlogo'];
                                        $addres = $headers['addresorder'];
                                    }
                                    if($seq == $hedding){
                                        $position = isset($headers['heddingposition'])?$headers['heddingposition']:"";
                                        $display =  $headers['heddingdislay']==1?"display:none":"";
                                        return '<div class="col-lg-4 box '.$position.'" draggable="true"  data-id="'.$seq.'" >
                                        <div class="" id="heading_div_id" data-order="header_order_position" style="'.$display.'">
                                        <h1 class="display-4 font-weight-boldest mb-10">INVOICE</h1>
                                        </div>
                                        </div>';
                                    }elseif($seq == $addres){
                                        $po = isset($headers['addressposition'])?$headers['addressposition']:"";
                                        $display =  $headers['addressdislay']==1?"display:none":"";
                                        return '<div class="col-lg-4 '.$po.' box" draggable="true" data-id="'.$seq.'" >
                                        <div id="address_div_id" data-order="header_order_address" style="'.$display.'" >
                                        <span class="d-flex flex-column align-items-md-end opacity-70">
                                        <span>Cecilia Chapman, 711-2880 Nulla St, Mankato Mississippi 96522</span>
                                        </span>
                                        </div>
                                        </div>';
                                    }elseif($seq == $logo){
                                        $co = isset($headers['logoposition'])?$headers['logoposition']:"text-right";
                                        $display =  $headers['logodislay']==1?"display:none":"";
                                        return '<div draggable="true" class="col-lg-4 box" data-id="'.$seq.'" >
                                        <div class="align-items-md-end px-0 '.$co.'" id="logo_div_id" data-order="header_order_logo" style="'.$display.'">
                                        <img src="assets/media/logos/logo-dark.png"   alt="Logo" />
                                        </div>
                                        </div>';
                                    }else{
                                        return '<div class="col-lg-4  box" draggable="true" data-id="'.$seq.'" ></div>';
                                    }
                                }
                                ?>

                                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row row">
                                    <?php
                                    $headers = json_decode($row['header'],1);
                                    for($i=1; $i<=6; $i++){
                                        echo headerFields($i,$headers,$_GET['Rid']);
                                    }
                                    ?>
                                </div>
                                <div class="border-bottom w-100"></div>

                                <div class="d-flex pt-6 row textfielddiv">
                                    <?php $k=0; $contentS = json_decode($row['content1'],1); foreach($contentS as $resu){ ?>
                                        <div class="d-flex <?PHP echo $resu['textfiledinline']==1?"flex-column":"mb-3"; ?>   col-lg-<?php echo $resu['textfiledswidth']; ?>" id="textfiledid<?php echo $k; ?>">
                                            <span class="<?PHP echo $resu['textfiledinline']==1?"mb-2":"mr-15"; ?> font-weight-bolder" id="textfiledlabel<?php echo $k; ?>"><?php echo $resu['label']; ?>:</span>
                                            <span  class="<?PHP echo $resu['textfiledinline']==1?"":"text-right "; ?>" id="textfiledvalue<?php echo $k; ?>">eg. details function</span>
                                        </div>
                                        <?php $k++;
} ?>
                                    </div>
                                </div>

                            </div>
                            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0 tableb_displaycss"  >
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table tabless" id="table">
                                            <thead>
                                                <tr>
                                                    <?php
                                                    if(isset($_GET['Rid'])){
                                                        function cmp($a, $b) {
                                                            $a = $a ?? [];
                                                            return $a['tablefieldsorder'] > $b['tablefieldsorder'];
                                                        }
                                                        usort($content2, "cmp");
                                                        $n=0; foreach($content2 as $value){ $keyvalue = $n; ?>
                                                            <th scope="col" class="pl-0 font-weight-boldest text-uppercase <?php echo $value['tablevalueposition']; ?> tablefileds<?php echo $keyvalue; ?>"     id="tablefileds<?php echo $keyvalue; ?>" data-function="positions<?php echo $n; ?>" style="<?php echo $value['tablefileddisplay']=='1'?"display:none;":""; ?> <?php echo $value['tablefieldwidth']!=''?"width: ".$value['tablefieldwidth'].'%':""; ?>" >  <?php echo $value['tablecolumns'] ?>
                                                        </th>
                                                        <?php $n++;
}
}else{ ?>
                                                            <?php  $n=0; foreach($inputFildsArray as $key => $value){ $keyvalue = $n; ?>
                                                                <th scope="col" class="pl-0 font-weight-boldest text-uppercase tablefileds<?php echo $keyvalue; ?>" id="tablefileds<?php echo $keyvalue; ?>" data-function="positions<?php echo $n; ?>"> <?php echo $key; ?> </th>
                                                                <?php $n++; ?>
                                                                <?php } ?>
                                                                <?php } ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="">
                                                                <?php if(isset($_GET['Rid'])){ $q=0; foreach($content2 as $value){  $keyvalue = $q; ?>
                                                                    <td id="tablefiledsbody<?php echo $keyvalue;?>" class="pl-0 pt-7 <?php echo $value['tablevalueposition']; ?> tablefileds<?php echo $keyvalue; ?>" style="<?php echo $value['tablefileddisplay']=='1'?"display:none":""; ?> <?php echo $value['tablefieldwidth']!=''?"width: ".$value['tablefieldwidth'].'%':""; ?>"><?php echo $value['tablevalue']; ?></td>
                                                                    <?php $q++;
}
}else{?>
                                                                        <?php  $q=0; foreach($inputFildsArray as $key => $value){  $keyvalue = $q; ?>
                                                                            <td id="tablefiledsbody<?php echo $keyvalue;?>" class="pl-0 pt-7 <?php echo $content2[$key]['tablevalueposition']; ?> tablefileds<?php echo $keyvalue; ?>" style="<?php echo $content2[$key]['tablefileddisplay']=='1'?"display:none":""; ?>"><?php echo $value; ?></td>
                                                                            <?php $q++;
}
}
                                                                            ?>

                                                                        </tr>
                                                                    </tbody>
                                                                    <?php $rowcount= count((array)$inputFildsArray);?>
                                                                    <tfoot style="font-size: 13px;">
                                                                        <tr class="font-weight-bolder " id="subtotalid" style="<?php echo $footer['subtotaltextcheck']==1?"display:none":""; ?>" >
                                                                            <td class="pl-0 text-right ssss" id="subtotaltext" > <?php echo isset($footer['subtotaltext'])?$footer['subtotaltext']:"SUBTOTAL"; ?> </td>
                                                                            <td id="subtotalamount" class="text-right"> 121</td>
                                                                        </tr>
                                                                        <tr class="font-weight-bolder " id="discountid" style="<?php echo $footer['discounttextcheck']==1?"display:none":""; ?>">
                                                                            <td class="pl-0 text-right text-uppercase ssss" id="discounttext" ><?php echo isset($footer['discounttext'])?$footer['discounttext']:"DISCOUNT"; ?></td>
                                                                            <td id="discountamount" class="text-right">11</td>
                                                                        </tr>

                                                                        <tr class="font-weight-bolder " id="taxid" style="<?php echo $footer['taxtextcheck']==1?"display:none":""; ?>">
                                                                            <td class="pl-0 text-right text-uppercase ssss" id="taxtext" ><?php echo isset($footer['taxtext'])?$footer['taxtext']:"TAX"; ?></td>
                                                                            <td id="taxamount"  class="text-right">10</td>
                                                                        </tr>
                                                                        <tr class="font-weight-bolder " id="shiping" style="<?php echo $footer['shipingtextcheck']==1?"display:none":""; ?>">
                                                                            <td class="pl-0 text-right text-uppercase ssss" id="shipingtext"  ><?php echo isset($footer['shipingtext'])?$footer['shipingtext']:"SHIPPING/HANDLING"; ?></td><td id="shipingamount" class="text-right">10</td>
                                                                        </tr>
                                                                        <tr class="font-weight-bolder " >
                                                                            <td class="font-weight-bold text-muted text-uppercase text-right ssss" id="tablec" ><?php echo isset($footer['tablec'])?$footer['tablec']:"TOTAL AMOUNT"; ?></td>
                                                                            <td  id="tablecamount" class="text-danger font-size-h3 font-weight-boldest text-right">130.00</td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end: Invoice body-->
                                                    <div class="row  bg-gray-100 inworddislayclas" id="inworddislayID" style="<?php echo $button['inworddisplay']==1?"display: none;":""; ?>" >
                                                        <div class="col-lg-12">
                                                            <div class=" mb-3 mt-2 <?php echo $button['inwordposition']!=""?$button['inwordposition']:""; ?>" id="positonwordamount" >
                                                                <div>
                                                                    <span class="text-right font-weight-bold font-weight-boldest text-danger mb-1">One hundred thirty Only</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                                                        <div class="col-md-9">
                                                            <div class="d-flex justify-content-between">
                                                                <button type="button" class="btn btn-light-primary font-weight-bold download_btn" style="<?php echo (isset($button['downloadbuttonsdisplay']) &&  $button['downloadbuttonsdisplay']==1)?"display:none":""; ?>" onclick="window.print();"> <i class="icon-2x  flaticon-multimedia-4"></i>Download Invoice</button>
                                                                <button type="button" class="btn btn-primary font-weight-bold print_btn"
                                                                style="<?php echo (isset($button['printbuttonsdisplay']) && $button['printbuttonsdisplay']==1)?"display:none":""; ?>" onclick="window.print();"  ><i class="icon-2x flaticon-technology"></i> Print </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
