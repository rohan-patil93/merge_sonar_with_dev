<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['save'])){

    $data= array();
    $data['application_table_name'] = $_POST['applicationtablelist'];
    $data['page_name'] = $_POST['formname'];
    $data['id'] = $_POST['id'];
    $data['report_id'] = $_POST['report_id'];
    $data['created_date'] = nowDate();
    $data['module_id']=$_POST['module_setup_id'];
    $data['module_group_id']=$_SESSION['module_setp'][0]['module_group_id'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['emp_id'] = $_SESSION['emp_id'];
    $data['report_type'] = $_POST['report_type'];
    $data['user_id'] = $_SESSION['userid'];

    foreach ($_POST['feildname'] as $postkey => $postvalue) {
        $data1[$postkey]['feildname'] = $_POST['feildname'][$postkey];
        $data1[$postkey]['display'] = (isset($_POST['display'][$postkey]))? 0:1;
        $data1[$postkey]['label'] = $_POST['label'][$postkey];
        $data1[$postkey]['heddingpositiontable'] = $_POST['heddingpositiontable'][$postkey];
        $data1[$postkey]['Priority'] = $_POST['Priority'][$postkey];
        $data1[$postkey]['switch'] = (isset($_POST['switch'][$postkey]))? 0:1;
        $data1[$postkey]['tablenames'] = $_POST['tablenames'][$postkey];
        $data1[$postkey]['displayfeildname'] = $_POST['displayfeildname'][$postkey];
        $data1[$postkey]['filtertype'] = $_POST['filtertype'][$postkey];
        $data1[$postkey]['grooupbyoperators'] = $_POST['grooupbyoperators'][$postkey];
    }

    foreach ($_POST['custom_report_heding'] as $repkey => $repovalue) {
        $data2[$repkey]['custom_report_heding'] = $_POST['custom_report_heding'][$repkey];
        $data2[$repkey]['custom_report_formula'] = $_POST['custom_report_formula'][$repkey];
        $data2[$repkey]['custom_report_Priority'] = $_POST['custom_report_Priority'][$repkey];
    }

    $data['report_formula'] = json_encode($data2);
    $fieldcolumnslist['rowslist'] = $_POST['rowslist'];
    $fieldcolumnslist['columnslist'] =$_POST['columnslist'];
    $fieldcolumnslist['columnvalueslist'] =$_POST['columnvalueslist'];
    $fieldcolumnslist['show_table_type'] =$_POST['show_table_type'];
    $fieldcolumnslist['master_list'] =$_POST['master_list'];
    $fieldcolumnslist_json = json_encode($fieldcolumnslist);
    $data['search_list']= $fieldcolumnslist_json;

    $data_json = json_encode($data1);
    $data['filedslist']=$data_json;
    $footer = array(
        "footerdisplay"=>(isset($_POST['footerdisplay']))? 0:1,
        "footerdescriptioon"=>$_POST['footerdescriptioon']);
    $button_json = json_encode($footer);
    $data['footerdescriptioon'] = $button_json;
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
    $url = API."?mode=insertautodesigntable&method=post";
    $response = file_post_contents($url, $data);
    header("location:index.php?option=COM_VIEW_REPORT&TYPE=MYR");
}


$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$getfieldsurl = API."?mode=getmyreportfields&Rid=".$rid;
$getfields = callAPI($getfieldsurl);
$getfieldslist = json_decode($getfields,1);
$mopdulesetup = $getfieldslist['module_setup'];
$displayattribtes = $getfieldslist['display_attributes'];
$pos = 10;
$attribute = array();
$fieldarray= [];
foreach($getfieldslist['filed'] as $filedlist){
  $fieldd =  explode('.', $filedlist);
  $fieldkeys = $fieldd[1];
  $fieldnamed = str_replace(".","_",$filedlist);
  $fieldarray[$fieldnamed] = $fieldd[0];
  if(isset($fieldd[1])){
      $attribute[$fieldnamed] = array("Field"=>$fieldnamed,"display"=>"1","label"=>$fieldkeys,"displayfeildname"=>$fieldkeys,"feildname"=>$fieldnamed,"priority"=>$pos,"tablenames"=>$fieldd[0],'filtertype'=>$displayattribtes[$fieldd[0]][$fieldd[1]]['type']);
  }else{
      $attribute[$filedlist] = array("Field"=>$filedlist,"display"=>"1","label"=>$filedlist,"displayfeildname"=>$filedlist,"feildname"=>$filedlist,"priority"=>$pos,"tablenames"=>'','filtertype'=>$displayattribtes[$fieldd[0]][$fieldd[1]]['type']);
  }
  $pos=$pos+10;
}


    $header = array("heddingposition" =>"text-center", "orderhedding" =>"5", "logoposition" =>"text-center", "orderlogo" => "2" ,"addressposition" =>"text-center", "addresorder" => "3", "heddingdislay" =>"0", "logodislay" => "0", "addressdislay" => "0");
    $role['page_title'] = $data['report_name']!=""?$data['report_name']:"Page Name";
    $role['report_id'] = $rid;
    $role['id'] ="";
    $getgetfieldslistsubmit = $getfieldslist['my_report'];
    if(isset($getgetfieldslistsubmit['id'])){
        $role['application_table_name'] = $getgetfieldslistsubmit['application_table_name'];
        $role['page_title'] = $getgetfieldslistsubmit['page_name'];
        $role['report_id'] = $getgetfieldslistsubmit['report_id'];
        $role['id'] = $getgetfieldslistsubmit['id'];
        $header  = json_decode($getgetfieldslistsubmit['header'],1);
        $footer  = json_decode($getgetfieldslistsubmit['footer'],1);
        $fieldsected = json_decode($getgetfieldslistsubmit['report_list'],1);
        $columnslist = json_decode($getgetfieldslistsubmit['search_list'],1);

        $report_formula =json_decode($getgetfieldslistsubmit['report_formula'],1);
        foreach($fieldsected as $result){
            if(isset($attribute[$result['feildname']])){
                $attribute[$result['feildname']] = array("Field"=>$result['feildname'],"display"=>$result['display'],"switch"=>$result['switch'],"label"=>$result['label'],"displayfeildname"=>$result['displayfeildname'],"feildname"=>$result['feildname'],"heddingpositiontable"=>$result['heddingpositiontable'],"priority"=>$result['Priority'],"tablenames"=>$fieldarray[$result['feildname']],'filtertype'=>$result['filtertype'],'grooupbyoperators'=>$result['grooupbyoperators']);
            }
        }
    }

    $groupbyopert = array('COUNT','MAX','MIN','SUM','AVG');


$_SESSION['download_data'] = array();
?>
<script type="text/javascript">
 function checktabletype(){
    var type = $('input[name="show_table_type"]:checked').val();
    if(type == "mtarix"){
        $("#showtypetable").show('slow');
        $("#showtypetablemaster").hide('slow');
    }else if(type == "master"){
        $("#showtypetablemaster").show('slow');
        $("#showtypetable").hide('slow');
    }else{
        $("#showtypetable").hide('slow');
        $("#showtypetablemaster").hide('slow');
    }
}
function nospaces(t){
  if(t.value.match(/\s/g)){
    t.value=t.value.replace(/\s/g,'');
}
}

function addmoreformula(){
    var maxformulid = $(".formulareportdiv").length;
    maxformulid +=200;
    maxformulid++;
    var positions = maxformulid+200;
    var html = '';
    html += '<div class="card formulareportdiv removeformula_'+maxformulid+'" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class="card-header p-2 custom_displayinline row" id="headingOnes'+maxformulid+'" ><div class="card-title collapsed f-14 mt-2 col-lg-10 f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOnes'+maxformulid+'">Formula Name</div>    <div class="col-lg-1" onclick="removeformuladiv(\''+maxformulid+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">X</div></div>';

    html += '<div id="collapseOnes'+maxformulid+'" class="collapse" data-parent="#accordionExample5"><div class="card-body"><div class=""><div class="tab-content mt-2" id="myTabContent4"><div class="tab-pane fade show active btn-sm" id="content-1" role="tabpanel" aria-labelledby="content_1">';

    html += '<div class="form-group row  mt-2"><span class="f-14">Heading<span class="tex-danger f-10">* Requerd Space used "_" </span></span><div class=""><input aria-label="Custom Report Heding[]"  class="form-control" type="text"   name="custom_report_heding[]" value=""  onkeyup="nospaces(this)" required> </div> </div>';

    html += '<div class="form-group row  mt-2"><span class="f-14">Report Formula</span><div class=""><textarea aria-label="Custom Report Formula[]" class="form-control"   name="custom_report_formula[]" rows="3" required></textarea></div></div>';

    html += '<div class=" row mt-2"><span class="f-14">Priority</span><div class=""><input aria-label="Custom Report Priority[]" class="form-control " type="text" name="custom_report_Priority[]" value="'+positions+'" required></div></div>';

    html += '</div></div></div></div></div></div>';

    $('#formulapppend').append(html);

}

function copyToClipboardMyReport(id){
    alert(id);

document.execCommand('insertHTML',true,'text to be copied');
}

function removeformuladiv(id){

    Swal({
      title: 'Do You Want To Remove Report formula?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Remove',
      denyButtonText: `Don't Remove`,
  }).then((result) => {
      if (result.isConfirmed) {
       $(".removeformula_"+id).remove()
   }
})
}

function fieldCopy(id){
    var $temp = $("<input aria-label='Input field'>");
    var content = $('#'+id).val();
    $("body").append($temp);
    $temp.val(content).select();
    document.execCommand("copy");
    $temp.remove();
    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Copied Successfully','');
}
function Getinserttype(table,field,type){
    console.log(table);
    console.log(field);
    console.log(type);
    if(type == 'select' || type == 'radio' || type == 'switch' || type == 'checkbox' || type == 'multi_select' || type =='disable_select' || type == 'auto_select' || type == 'rating'){
        jQuery.ajax({
            url: "views/custom_attributes_popup.php",
            data:'page=dis_att_type&table='+table+'&field='+field+'&type='+type,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
}
</script>

<div class="card">
    <div class="card-header">
        <h5>Custome Table</h5>
        <div class="card-header-right">
            <a href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-info waves-effect waves-light" target="_blank"><i class="fa fa-eye"></i> View Report</a>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body ">
        <form  method="POST">
            <input type="hidden" name="id" id="id" value="<?php echo $role['id']; ?>">
            <input type="hidden" name="report_id" id="report_id" value="<?php echo $role['report_id']; ?>">
            <input type="hidden" name="applicationtablelist" id="applicationtablelist" value='<?php echo $role['application_table_name']; ?>'>
            <div class="row">
                <div class="col-lg-3" >
                    <span class="form-label">Report Type</span>
                    <div class="col-lg-8 col-md-8 col-sm-12 w-full" >
                        <select aria-label="Report Type" class="form-control form-select js-example-basic-single" style="width:100%" name="report_type" >
                            <option value="MYR">My Reports</option>
                            <option value="SR">Stantdard Reports</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-3 w-full">
                            <span class="form-label">Module</span>
                            <select aria-label="Module Setup" class="form-select js-example-basic-single" id="module_setup_id" name="module_setup_id">
                                <?php foreach($mopdulesetup as $modulesetup){ ?>
                                    <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-9">
                            <span class="form-label">Display Report Type</span>
                            <div class="form-radio">
                                <div class="radio radio-outline radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Show Table Type" type="radio" name="show_table_type" onclick="checktabletype()" value="tabel" <?php echo $columnslist['show_table_type']=="tabel" || $columnslist['show_table_type']==""?"checked":""; ?> onkeydown="">
                                        <i class="helper"></i>Tabular
                                    </label>
                                </div>
                                <div class="radio radio-outline radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Show Table Type" type="radio" name="show_table_type" onclick="checktabletype()" value="mtarix" <?php echo $columnslist['show_table_type']=="mtarix"?"checked":""; ?> onkeydown="">
                                    <i class="helper"></i>Matrix
                                </label>
                            </div>
                            <div class="radio radio-outline radio-inline">
                                <label class="form-label">
                                    <input aria-label="Show Table Type" type="radio" name="show_table_type" onclick="checktabletype()" value="master" <?php echo $columnslist['show_table_type']=="master"?"checked":""; ?> onkeydown="">
                                    <i class="helper"></i>Master/Detail
                                </label>
                            </div>
                        </div>
                        <div class="row" id="showtypetablemaster" <?php if($columnslist['show_table_type'] !="master"){ ?>style="display:none" <?php
} ?>>

                            <div class="form-group col-lg-4">
                                <span class="form-label d-block">Master Table <span class="f-10 text-danger">*Select Parent Field</span></span>

                                <select aria-label="Master List"  class="form-select js-example-basic-single" name="master_list" style="width:100%">
                                   <option value=""  >Select Any One</option>
                                   <?php foreach($getfieldslist['reportattributesfields'] as $optkey2=>$v2){ ?>
                                    <option value="<?php echo $optkey2; ?>"  <?php echo $columnslist['master_list']==$optkey2?"selected":""; ?>><?php echo $optkey2; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="showtypetable" <?php if($columnslist['show_table_type'] !="mtarix"){ ?>style="display:none" <?php
} ?>>
                        <div class="col-lg-4">
                            <span >Row <span class="f-10 text-danger d-block">*select Unique value repeated records</span></span>
                            <select aria-label="Rowslist"  class="form-select js-example-basic-single" name="rowslist" style="width:100%">
                               <option value=""  >Select Any One</option>
                               <?php foreach($getfieldslist['reportattributesfields'] as $optkey2=>$v2){ ?>
                                <option value="<?php echo $optkey2; ?>"  <?php echo $columnslist['rowslist']==$optkey2?"selected":""; ?>><?php echo $optkey2; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <span >Colunms <span class="f-10 text-danger d-block">*Select Header Colunms</span></span>
                        <select aria-label="Columnslist"  class="form-select js-example-basic-single" name="columnslist" style="width:100%">
                            <option value=""  >Select Any One</option>
                            <?php foreach($getfieldslist['reportattributesfields'] as $optkey3=>$v3){ ?>
                                <option value="<?php echo $optkey3; ?>" <?php echo $columnslist['columnslist']==$optkey3?"selected":""; ?>  ><?php echo $optkey3; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <span >Colunms values <span class="f-10 text-danger d-block">*Select Header Colunms Value</span></span>
                        <select aria-label="Columnvalueslist"  class="form-select js-example-basic-single" name="columnvalueslist" style="width:100%">
                            <option value=""  >Select Any One</option>
                            <?php foreach($getfieldslist['reportattributesfields'] as $optkey4=>$v4){ ?>
                                <option value="<?php echo $optkey4; ?>" <?php echo $columnslist['columnvalueslist']==$optkey4?"selected":""; ?> ><?php echo $optkey4; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group row  m-t-20">
    <div class="col-lg-3 col-md-3 col-sm-12 custom-scroll leftsidemenu" style="height: 700px">
        <div class="accordion accordion-solid accordion-toggle-plus  displaye
        " id="accordionExample3">
        <div class="card">
            <div class="card-header p-2" id="headingOne88">
                <div class="card-title collapsed f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOne88">Heading </div>
            </div>
            <div id="collapseOne88" class="collapse" data-parent="#accordionExample88">
                <div class="card-body p-0">
                    <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample88">
                        <div class="card">
                            <div class="card-header p-2 custom_displayinline row" id="headingOne5_heading">
                                <input type="hidden" name="heddingposition" id="heddingposition" value="<?php echo $header['heddingposition'];?>">
                                <div class="card-title f-w-600  mt-0 m-l-10 col-lg-6" data-bs-toggle="collapse" data-bs-target="#collapseOne5" aria-expanded="true">
                                    Heading
                                    <span class="form-label badge bg-info header_order_position  mr-5" ><?php echo isset($header['orderhedding'])?$header['orderhedding']:"1";?></span>
                                </div>
                                <div class="col-lg-6 custom_displayinline" style="display: contents;">
                                    <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['heddingdislay']=='1')?"":"checked"; ?> name="heddingdislay[<?php echo $q; ?>]"  class="heddingdislay js-primary-sm " data-id="<?php echo $q; ?>" ><span></span>
                                    <div class="btn-group ml-2" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">
                                        <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_hedding <?php echo ($header['heddingposition']=='text-left')?' active':''; ?>" value="text-left"><i class="fa fa-align-left text-primary"></i></button>
                                        <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_hedding  <?php echo ($header['heddingposition']=='text-center')?' active':''; ?>" value="text-center"><i class="fa fa-align-center"></i></button>
                                        <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_hedding <?php echo ($header['heddingposition']=='text-right')?' active':''; ?>" value="text-right"><i class="fa fa-align-right"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="collapseOne5" class="collapse show" data-parent="#accordionExample88" style="">
                                <div class="card-body">
                                    <div class="tab-content " id="myTabContent4_2">
                                        <div class="tab-pane fade show active btn-sm" id="content-1_2" role="tabpanel" aria-labelledby="content_1">
                                            <div class="form-group row mt-2">
                                                <span class="col-4  f-14">Page Title</span>
                                                <div class="col-8">
                                                    <input aria-label="Formname" class="form-control formname" name="formname" type="text" value="<?php echo ucwords(str_replace("_"," ",$role['page_title'])); ?>" id="" required="">
                                                </div>
                                            </div>

                                            <div class="row mt-2" style="display:none">
                                                <span class="col-5  f-14">Order Position</span>
                                                <div class="col-7">
                                                    <input type="hidden" value="<?php echo isset($header['orderhedding'])?$header['orderhedding']:"1";?>" name="orderhedding"  class="header_order_position form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header p-2 row" id="headingOne5_logo">
                                <div class="card-title f-w-600 collapsed custom_displayinline mt-0 m-l-10 col-lg-6" data-bs-toggle="collapse" data-bs-target="#collapseOne66666666">
                                    Logo
                                    <span class="form-label badge bg-info header_order_logo mr-5 " ><?php  echo isset($header['orderlogo'])?$header['orderlogo']:"3";?></span>
                                </div>
                                <div class="col-lg-6 custom_displayinline" style="display: contents;">
                                  <input type="hidden" name="logoposition" id="logoposition" value="<?php  echo isset($header['logoposition'])?$header['logoposition']:"text-right";?>">

                                  <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['logodislay']=='1')?"":"checked"; ?> name="logodislay[<?php echo $q; ?>]"  class="logodislay js-primary-sm" data-id="<?php echo $q; ?>" >

                                  <div class="btn-group ml-2" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_logo <?php echo ($header['logoposition']=='text-left')?' active':''; ?>" value="text-left"><i class="fa fa-align-left text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_logo <?php echo ($header['logoposition']=='text-center')?' active':''; ?>" value="text-center"><i class="fa fa-align-center text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_logo <?php echo ($header['logoposition']=='text-right')?' active':''; ?>" value="text-right"><i class="fa fa-align-right text-primary"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne6" class="collapse" data-parent="#accordionExample88">
                            <div class="card-body">
                                <div class="">
                                    <div class="tab-content " id="myTabContent41">
                                        <div class="tab-pane fade show active btn-sm" id="content-1_3" role="tabpanel" aria-labelledby="content_1">
                                            <div class="row mt-2" style="display:none">
                                                <span class="col-5  f-14">Order Position</span>
                                                <div class="col-7">
                                                    <input type="hidden" value="<?php  echo isset($header['orderlogo'])?$header['orderlogo']:"3";?>"  name="orderlogo" class="header_order_logo form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header p-2 row" id="headingOne7">
                            <input type="hidden" name="addressposition" id="addressposition" value="<?php  echo isset($header['addressposition'])?$header['addressposition']:"";?>">
                            <div class="card-title f-w-600 collapsed custom_displayinline mt-0 m-l-10 col-lg-6" data-bs-toggle="collapse" data-bs-target="#collapseOne77">
                                Report Details
                                <span class="form-label badge bg-info header_order_address  mr-5" ><?php  echo isset($header['addresorder'])?$header['addresorder']:"6";?></span>
                            </div>
                            <div class="col-lg-6 custom_displayinline" style="display: contents;">
                                <input aria-label="Checkbox" type="checkbox"  <?php echo ($header['addressdislay']=='1')?"":"checked"; ?> name="addressdislay[<?php echo $q; ?>]"  class="addressdislay js-primary-sm" data-id="<?php echo $q; ?>" >

                                <div class="btn-group ml-2" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_address <?php echo ($header['addressposition']=='text-left')?' active':''; ?>" value="text-left"><i class="fa fa-align-left text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_address <?php echo ($header['addressposition']=='text-center')?' active':''; ?>" value="text-center"><i class="fa fa-align-center text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light header_position_address <?php echo ($header['addressposition']=='text-right')?' active':''; ?>" value="text-right"><i class="fa fa-align-right text-primary"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne7" class="collapse" data-parent="#accordionExample88">
                            <div class="card-body">
                                <div class="">
                                    <div class="tab-content " id="myTabContent4_3">
                                        <div class="tab-pane fade show active btn-sm" id="content-1_4" role="tabpanel" aria-labelledby="content_1">
                                            <div class="row mt-2" style="display:none">
                                                <span class="col-5  f-14">Order Position</span>
                                                <div class="col-7">
                                                    <input type="hidden" value="<?php  echo isset($header['addresorder'])?$header['addresorder']:"6";?>" name="addresorder" class="header_order_address form-control">
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
    <!---------------------   Fields part =------------------------->
    <div class="card mt-5">
        <div class="card-header p-2" id="headingOne5_fields">
            <div class="card-title f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOne5">Fields</div>
        </div>
        <div id="collapseOne5_2" class="collapse show" data-parent="#accordionExample5">
            <div class="card-body p-0">
                <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample5">
                    <?php $i=0;
                    foreach ($attribute as $key => $value) {  ?>
                        <input type="hidden"  name="feildname[]" id="fields_<?php echo $value['Field']; ?>" value="<?php echo $value['Field']; ?>" >
                        <input type="hidden"  name="tablenames[]" value="<?php echo $value['tablenames']; ?>">
                        <input type="hidden"  name="displayfeildname[]"  value="<?php echo $value['displayfeildname']; ?>">
                        <div class="card cursor-pointer">
                            <i class="ti-files cursor-pointer position-absolute f-10" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Copy To Used" style="z-index: 99999" onclick="fieldCopy('fields_<?php echo $value['Field']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                            <div class="card-header p-2 custom_displayinline row" id="headingOnes<?php echo $i; ?>">
                                <div class="card-title collapsed f-14 mt-2 col-lg-6" data-bs-toggle="collapse" data-bs-target="#collapseOnes<?php echo $i; ?>">
                                    <?php echo ucwords(str_replace("_"," ",$value['displayfeildname'])); ?>
                                    <span class="form-label label label-info f-10"><?php echo $value['tablenames']; ?> </span>
                                </div>
                                <div class="col-lg-6 custom_displayinline" style="display: contents;">
                                   <input type="hidden" name="heddingpositiontable[]" id="heddingpositiontable<?php echo $i; ?>" value="<?php echo $value['heddingpositiontable']; ?>">
                                   <input aria-label="Checkbox" type="checkbox" name="display[<?php echo $i; ?>]"  class="display js-primary-sm" data-id="<?php echo $i; ?>"  <?php echo  $value['display']=='0'?"checked":""; ?> >

                                   <div class="btn-group ml-2" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light  positiontable  <?php echo ($value['heddingpositiontable']=='')?' active':''; ?>" data-id="<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Left Align"><i class="fa fa-align-left text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light positiontable <?php echo ($value['heddingpositiontable']=='text-center')?' active':''; ?>" data-id="<?php echo $i; ?>" value="text-center" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Center Aligh"><i class="fa fa-align-center text-primary"></i></button>
                                    <button type="button" class="btn btn-light btn-mini waves-effect waves-light positiontable <?php echo ($value['heddingpositiontable']=='text-right')?' active':''; ?>" data-id="<?php echo $i; ?>" value="text-right" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Right Align"><i class="fa fa-align-right text-primary"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOnes<?php echo $i; ?>" class="collapse" data-parent="#accordionExample5">
                            <div class="card-body">
                                <div class="">
                                    <div class="tab-content mt-2" id="myTabContent4_4">
                                        <div class="tab-pane fade show active btn-sm" id="content-1_5" role="tabpanel" aria-labelledby="content_1">
                                            <div class="form-group row  mt-2">
                                                <span class="col-5  f-14">Label</span>
                                                <div class="col-7">
                                                    <input aria-label="Label[]" class="form-control txtlablel" type="text" name="label[]" data-id="<?php echo $i; ?>" value="<?php echo ucwords(str_replace("_"," ",$value['label'])); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row mt-2">
                                                <span class="col-5  f-14">Filter</span>
                                                <div class="col-7">
                                                    <span class="switch switch-icon">
                                                        <label>
                                                            <input aria-label="Checkbox" type="checkbox"  name="switch[<?php echo $i; ?>]"  class="switch js-primary-sm filterdisplaywitch" data-id="<?php echo $key; ?>"  <?php echo $value['switch']=='0'?"checked":""; ?> > <span></span>
                                                        </label>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-2">
                                                <span class="col-5  f-14">Filter Type</span>
                                                <div class="col-7">
                                                 $filed_array = array('text' => 'Simple Text', 'touchspin' => 'Touch Spin', 'date_pick' => 'Date','month' => 'Month','year'=>'Year','month_year'=>'Month','time_pick'=>'Time' ,'color_pick' => 'Color', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Quantity','currency'=> 'Currency', 'password'=> 'Password', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List','auto_select'=>'Auto Select' ,'multi_select'=> 'Multi Select','rating'=>'Rating','image' => 'Photo','file_upload' => 'Documents Upload','editor' => 'Editor','slider'=>'Slider','number_type'=> 'Number','read_only'=> 'Read Only Text','disable_select'=> 'Disable List','hidden'=> 'Hidden', 'serial_no'=>'Serial Number');

                                                 ?>
                                                 <select aria-label="Select option" class="form-control  form-select js-example-basic-single" id="<?php echo $value['Field']; ?>"  name="filtertype[]" onchange="Getinserttype('<?php echo $value['tablenames']; ?>','<?php echo $value['displayfeildname']; ?>',this.value)">
                                                    <?php foreach ($filed_array as $typekey => $typevalue) { ?>
                                                        <option value = "<?php echo $typekey; ?>" <?php echo  $value['filtertype']==$typekey?"selected":""; ?> ><?php echo $typevalue; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class=" row mt-2">
                                            <span class="col-5 f-14">Priority</span>
                                            <div class="col-7">
                                                <input aria-label="Priority[]" class="form-control Priority" type="text" name="Priority[]" value="<?php echo $value['priority']; ?>">
                                            </div>
                                        </div>
                                        <div class=" row mt-2">
                                            <span class="col-5 f-14">Group Opertors</span>
                                            <div class="col-7">

                                             <select aria-label="Grooupbyoperators[]"  class="kt-dual-listbox form-select js-example-basic-single" name="grooupbyoperators[]"  onchange="buildQuery()"  style="width:100%">
                                                <option value="0">Select Any One</option>
                                                <?php foreach($groupbyopert as $groupbyoperts){ ?>
                                                    <option value="<?php echo $groupbyoperts; ?>" <?php echo ($groupbyoperts == $value['grooupbyoperators'])?"selected":""; ?> ><?php echo $groupbyoperts; ?> </option>
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
            <?php $i++;
} ?>
        </div>
    </div>
</div>
</div>
<!--------------------- END  heade part =------------------------->

<div class="card">
    <div class="card-header p-2" id="headingOne9900">
        <div class="card-title collapsed f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOne9900">Report Formula</div>
    </div>
    <div id="collapseOne9900" class="collapse" data-parent="#accordionExample9900">
        <div class="card-body p-0" id="formulapppend">
            <?php $mkc = 1;?>
            <?php foreach($report_formula  as $report_formul){ ?>
                <div class="card formulareportdiv removeformula_<?php echo $mkc; ?>">
                   <div class="card-header p-2 custom_displayinline row" id="headingOnes<?php echo $mkc; ?>">
                      <div class="card-title collapsed f-14 mt-2 col-lg-10 f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOnes<?php echo $mkc; ?>">
                         <?php echo ucwords(str_replace("_"," ", $report_formul['custom_report_heding'])); ?></div>

                         <div class="col-lg-1" onclick="removeformuladiv('<?php echo $mkc; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                             X
                         </div>
                     </div>
                     <div id="collapseOnes<?php echo $mkc; ?>" class="collapse" data-parent="#accordionExample5">
                      <div class="card-body">
                         <div class="">
                            <div class="tab-content mt-2" id="myTabContent4_5">
                               <div class="tab-pane fade show active btn-sm" id="content-1_6" role="tabpanel" aria-labelledby="content_1">
                                  <div class="form-group row mt-2">
                                     <span class="f-14">Heading<span class="tex-danger f-10">* Requerd Space used "_" </span></span>
                                     <div class="">
                                        <input aria-label="Custom Report Heding[]"  class="form-control" type="text"   name="custom_report_heding[]" value="<?php echo $report_formul['custom_report_heding']; ?>"  onkeyup="nospaces(this)"> </div> </div>
                                        <div class="form-group row  mt-2">
                                         <span class="f-14">Report Formula</span>
                                         <div class="">
                                            <textarea aria-label="Custom Report Formula[]" class="form-control"   name="custom_report_formula[]" rows="3"><?php echo $report_formul['custom_report_formula'];  ?></textarea>
                                        </div>
                                    </div>
                                    <div class=" row mt-2">
                                     <span class="f-14">Priority</span>
                                     <div class="">
                                        <input aria-label="Custom Report Priority[]" class="form-control " type="text" name="custom_report_Priority[]" value="<?php echo $report_formul['custom_report_Priority']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $mkc++;
} ?>
    </div>
    <button type="button" class="btn btn-primary m-l-10 mb-1"  onclick="addmoreformula()"><i class="fa fa-plus"></i> Add More Report Formula</button>

</div>
</div>

<div class="card">
    <div class="card-header p-2" id="headingOne99">
        <div class="card-title collapsed f-w-600" data-bs-toggle="collapse" data-bs-target="#collapseOne99">Footer</div>
    </div>
    <div id="collapseOne99" class="collapse" data-parent="#accordionExample99">
        <div class="card-body">
            <div class="accordion accordion-toggle-arrow elementor-design" id="accordionExample99">
                <div class="row mt-2">
                    <span  class="col-5 ">Display</span>
                    <div class="col-7 d-flex">
                        <span class="switch switch-icon"><label>
                            <input aria-label="Checkbox" type="checkbox" class="inworddisplay js-switch" <?php echo $footer['footerdisplay']==1?"":"checked"; ?> name="footerdisplay" ><span></span>
                        </label></span>
                    </div>
                </div>
                <div class="form-group row  mt-2">
                    <span class="col-5 ">Footer Description</span>
                    <div class="col-7">
                        <textarea aria-label="Footerdescriptioon" class="form-control footerdescriptioon" id="exampleTextarea"  name="footerdescriptioon" rows="3"><?php echo $footer['footerdescriptioon']==""?"Footer Description":$footer['footerdescriptioon']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="col-lg-9 col-md-9 col-sm-12 prints  displaye j-pro">
    <div class="flex-column-fluid ">
        <div class=""> <!---- container---------->
            <div class="card card-custom overflow-hidden p-2">

                <div class="row mb-5 mt-2 m-l-5">
                    <?php $mn=1; foreach ($attribute as $key => $value){ ?>
                        <div class="filter<?php echo $key; ?> col-lg-2 p-1 form-group" <?php if($value['switch']!='0'){ ?>style="display:none" <?php
}else{ $mn = 0;
} ?>>
                            <span class="form-label"><?php echo ucwords(str_replace("_"," ",$value['label'])); ?></span>
                            <?php
                            if($value['filtertype']=="text"){
                                echo "<input aria-label='Input field' type='text' class='form-control' placeholder='".ucwords(str_replace("_"," ",$value['label']))."'>";
                            }elseif($value['filtertype']=="date_pick"){
                                echo "<input aria-label='Select date' type='date' class='form-control' placeholder='".ucwords(str_replace("_"," ",$value['label']))."'>";
                            }elseif($value['filtertype']=="select"){
                                echo '<select aria-label="Select option" class="form-select js-example-basic-single " ><option>Select Any Option</option></select>';
                            }else{ echo "<input aria-label='Input field' type='text' class='form-control' placeholder='".$value['label']."'>";
}
                            ?>
                        </div>
                    <?php  } ?>
                </div>

                <?php
                function  headerFields($seq, $headers,$id="",$label){
                    if($id==""){
                        $hedding = 5;
                        $logo = 2;
                        $addres = 3;
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
                        <h5 class="card-label text-center f-w-700 d-block mb-0">'.$label.'</h5>

                        <span class="f-10 d-block">From 17-05-2023 To 17-05-2023</span>
                        </div>
                        </div>';
                    }elseif($seq == $addres){
                        $po = isset($headers['addressposition'])?$headers['addressposition']:"";
                        $display =  $headers['addressdislay']==1?"display:none":"";
                        return '<div class="col-lg-4 '.$po.' box" draggable="true" data-id="'.$seq.'" >
                        <div id="address_div_id" data-order="header_order_address" style="'.$display.'" >
                        <span class="f-10 f-w-600"><span class="d-block">Name : '.$_SESSION['display_name'].' </span> <span> Date & Time : '.date("d-m-Y H:i:s").'</span></span>
                        </div>
                        </div>';
                    }elseif($seq == $logo){
                        $co = isset($headers['logoposition'])?$headers['logoposition']:"text-right";
                        $display =  $headers['logodislay']==1?"display:none":"";
                        return '<div draggable="true" class="col-lg-4 box" data-id="'.$seq.'" >
                        <div class="align-items-md-end px-0 '.$co.'" id="logo_div_id" data-order="header_order_logo" style="'.$display.'">
                        <img src="uploads/all/'.$_SESSION['config']['display_logo'].'" class="img-fluid  img-120" alt="Logo" />
                        </div>
                        </div>';
                    }else{
                        return '<div class="col-lg-4  box" draggable="true" data-id="'.$seq.'" ></div>';
                    }
                }
                ?>
                <div class="row">
                    <?php
                    $label = ucwords(str_replace("_"," ",$role['page_title']));
                    for($i=1; $i<=6; $i++){
                        echo headerFields($i,$header,$_GET['Rid'],$label);
                    }
                    ?>
                </div>
                <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <?php $m=0; foreach ($attribute as $key => $value) { ?>
                                            <th scope=\"col\" class="pl-0 font-weight-bold text-muted <?php echo $value['heddingpositiontable']; ?> filedsrow<?php echo $m; ?>"  id="thlabel<?php echo $m; ?>" style="display:<?php echo  $value['display']=='0'?"":"none"; ?>"><?php echo ucwords(str_replace("_"," ",$value['label'])); ?> </th>

                                            <?php $m++;
} ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="font-weight-boldest">
                                            <?php $k=0; foreach ($attribute as $key => $value) {  ?>
                                                <td class="pl-0 pt-7 filedsrow<?php echo $k; ?> <?php echo $value['heddingpositiontable']; ?>" id="thvalue<?php echo $k; ?>" style="display:<?php echo $value['display']=='0'?"":"none"; ?>" >xxxx&#160;</td>
                                                <?php $k++;
} ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row  bg-gray-100 inworddislayclas" id="inworddislayID" style="<?php echo $footer['footerdisplay']==1?"display: none;":""; ?>">
                            <div class="col-lg-12">
                                <div class=" mb-3 mt-2" >
                                    <span class="text-right font-weight-bold font-weight-boldest text-danger mb-1 footerdescriptioonview"><?php echo $footer['footerdescriptioon']==""?"Footer Description":$footer['footerdescriptioon']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-right py-8 px-8 py-md-10 px-md-0  displaye mt-5 ">
            <div class="col-md-9">
                <div class="d-flex justify-content-between">
                    <button type="submit" name="save" value="save" class="btn btn-primary m-l-50" ><i class="la la-check">  </i> Save </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>

