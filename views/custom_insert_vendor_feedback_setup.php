<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['table']= 1;
if(isset($_POST['submitvendorsetup'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertupdatevendorsetup&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
             $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&Rid=".deocdeId('id:'.$response['LASTID']));
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}
$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$url1 = API."?mode=vendorreviewalldetails&setup_id=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$setup = $result['setup'];
$categories = $result['categories'];
$questions = $result['questions'];
$allow_locselect = array('0');
$allow_venselect = array('0');

$provide_locselect = array('0');
$provide_depselect = array('0');
$provide_desselect = array('0');
$provide_roleselect = array('0');
$provide_empselect = array('0');

if($_GET['Rid']!=""){
   $allow_locselect = json_decode($setup['applicable_locations'],1);
   $allow_venselect = json_decode($setup['applicable_vendors'],1);
   $provide_locselect = json_decode($setup['provide_locations'],1);
   $provide_depselect = json_decode($setup['provide_departments'],1);
   $provide_desselect = json_decode($setup['provide_designations'],1);
   $provide_roleselect = json_decode($setup['provide_roles'],1);
   $provide_empselect = json_decode($setup['provide_employees'],1);

}
?>
<script type="text/javascript">

  function updatedetail(field, update_id,val){
    console.log(val);
    if(val!=""){
     jQuery.ajax({
          url: "ajax.php",
          data:'page=updatedynamicfiedrojecttaskdetail&table_name=vendor_feedback_categories&field='+field+'&fielddata='+val+'&update_id='+update_id,
          type: "POST",
          success:function(data){
            console.log(data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Category Name Upadate Successfully','');
            if(field == 'assigen_id'){
            }
          },
          error:function (){}
      });
    }
  }

function addPublish(){
    console.log("working");
    $(".popup-publish").show('slow');
}
function closebtnpublish(){
    console.log("close");
    $(".popup-publish").hide('slow');
}
function addCategory(){
    console.log("working");
    $(".popup-member").show('slow');
}
function closebtn(){
    console.log("close");
    $(".popup-member").hide('slow');
}
function questionaddhtml(val){
    console('questionaddhtml'+val);
}
function addQuestion(cat_id,ques_id="",change_id=""){
    console.log("checkk"+cat_id);
    jQuery.ajax({
        url: "views/custom_insert_vendor_review_addquestion_popup.php",
        data:'cat_id='+cat_id+"&question_id="+ques_id+'&change_id='+change_id,
        type: "POST",
        success:function(data){
        $('#popup2').html(data);
        $("#kt_modal_KTDatatable_setup").modal("show");
    },
    error:function (){}
    });
 }

function Updatepublish(){
    var publish_date = $("#publish_date").val();
    var setup_id = $("#rid").val();
    var publish_by = $("#publish_by").val();
    jQuery.ajax({
        url: "ajax.php",
        data:'page=setupcomponents&table_name=vendor_feedback_setup&current_status=52&publish_date='+publish_date+'&publish_by='+publish_by+'&id='+setup_id,
        type: "POST",
        success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Created New Version Successfully','');
            window.location.href = 'index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&Rid='+data;
        },
        error:function (){}
    });
}

function addCategoryName() {
    var category_name = $("#category_name").val();
    var setup_id = $("#rid").val();

    jQuery.ajax({
        url: "ajax.php",
        data:'page=setupcomponents&table_name=vendor_feedback_categories&current_status=0&category_name='+category_name+'&vendor_feedback_setup_id='+setup_id,
        type: "POST",
        success:function(data){
            console.log("Responce:"+data);
            var result = JSON.parse(data);
             console.log("jdonpare:"+result);
            console.log("last id:"+result.LASTID);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Category is added successfully','');
            var str = '<div class="mt-3" id="cat_id_'+result.LASTID+'"><div class=" col-lg-12 row" style="clear: both;"> <div class="col-lg-10 f-15 f-w-600 p-2 text-info"><input aria-label="Category" class="cat_1" type="text"  name="category" value="'+category_name+'" onblur="updatedetail('+result.LASTID+')" ></div> <div class="col-lg-2 text-right"><button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addQuestion('+result.LASTID+')"><i class="icofont icofont-plus icon-white "></i>Questions</button></div> </div> <div class="m-l-15 " id="cat_que_id_'+result.LASTID+'"></div>';
            $('#cate').append(str);

            closebtn();
        },
        error:function (){}
    });
}
function createnewversion(setup_id){
    jQuery.ajax({
        url: "ajax.php",
        data:'page=createnewversionvendorfeedback&setup_id='+setup_id,
        type: "POST",
        success:function(data){
            console.log("Responce:"+data);
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Created New Version Successfully','');
            window.location.href = 'index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&Rid='+data;
        },
        error:function (){}
    });
}
</script>
<style type="text/css">
    .design-sec{position: absolute; background: #ffffff; z-index: 999999; border: 1px solid #cccccc; padding: 10px; border-radius: 5px; width: 320px;}
.wd-p-49{
    width: 49%;
}
.wd-p-100{
    width: 100%;
}
input.cat_1 { width: 94%; border: none; padding: 6px 8px; color: #4099ff; font-weight: 600; font-size: 18px;}
input.cat_1:focus { outline: aqua; border: 1px solid #ccc;}
</style>

</style>
    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
        <form method="post" id="dataform" action="">
        <div class="card">
            <div class="card-header">
                <h5>Vendor Feedback Setup</h5>
                <div class="card-header-right">
                    <?php if($_GET['Rid']!=""){ ?>
                    <button class="btn btn-warning" type="button" onclick="createnewversion(<?php  echo $rid; ?>)"><i class="icofont icofont-plus icon-white "></i> New Version </button>
                    <?php if( $setup['current_status'] !="52"){ ?>
                    <button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addPublish('<?php echo $i; ?>')"></i>Publish</button>
                     <div class="popup-publish design-sec text-white" style="right: 0; display: none;background-color: #2ed8b6;">
                            <div class="text-left mb-1 f-w-600 f-16" style="text-align: left;">Publish
                            <button type="button" class="float-end member-close design-close btn text-white" onclick="closebtnpublish()"><i class="ti-close"></i></button>
                            </div>
                            <div class="form-group w-full mt-2 row" style="text-align: left;">
                                <div class="col-lg-12">Publish Date </div>
                                <div class="col-lg-12">
                                    <input aria-label="Publish Date" type="text" class="form-control datepicker" id="publish_date" name="publish_date">
                                    <input type="hidden" class="form-control " id="publish_by" name="publish_by" value="<?php echo $_SESSION['userid']; ?>">

                                </div>
                            </div>
                            <button type="button" class="btn btn-primary float-right mt-2" onclick="Updatepublish()"> Publish</button>
                        </div>
                    <?php } ?>
                <?php } ?>
                </div>
            </div>
            <div class="card-body" style="padding: 15px 12%; <?php echo ( $setup['current_status'] =="52")?"pointer-events: none; opacity: 0.7;":"";?>">
                <div class="row">
                    <div class="form-group col-lg-4">
                        <span class="form-label">Vendor Review Name</span>
                        <input aria-label="Plan Name" type="text" class="form-control" name="plan_name" value="<?php echo $setup['feedback_name']; ?>" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Version</span>
                        <?php $version_no = $setup['version_no'] !="" ? $setup['version_no'] : '0.1'; ?>
                        <input aria-label="Version Nos" type="text" class="form-control" name="version_nos" value="<?php echo $version_no; ?>" readonly>

                        <input type="hidden" name="version_no" value="<?php echo $version_no; ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <span class="form-label">Can feedback be submitted multiple times?</span>
                        <?php $multiple_time = $setup['allow_multiple_times']!="" ? $setup['allow_multiple_times']: 0; ?>
                        <div class="radio radio-outline  mt-1">
                            <label class="radio radio-inline">
                                <input aria-label="Radio option" type="radio" <?php if($multiple_time==0){ ?>checked <?php
} ?>   name="multiple_time" value='0'>
                                <span></span>Yes
                            </label>
                             <label class="radio radio-inline">
                                <input aria-label="Radio option" type="radio"  <?php if($multiple_time==1){ ?>checked <?php
} ?>  name="multiple_time" value='1'>
                                <span></span>No
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Feedback Start Date</span>
                        <div class="input-group custom-date">
                            <span class="input-group-text addon-style" id="basic-addon3">
                                <span class=""><i class="fa fa-calendar"></i></span>
                            </span>
                            <input aria-label="Start Date" type="text" class="form-control datepicker" name="start_date" id="date_of_grant" value="<?php echo fromDate($setup['start_date']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Feedback End Date</span>
                        <div class="input-group custom-date">
                            <span class="input-group-text addon-style" id="basic-addon3_2">
                                <span class=""><i class="fa fa-calendar"></i></span>
                            </span>
                            <input aria-label="To Date" type="text" class="form-control datepicker" name="to_date"  value="<?php echo fromDate($setup['end_date']); ?>"  value="">
                        </div>
                    </div>

                    <div class="form-group col-lg-4">
                        <span class="form-label">Notes</span>
                        <textarea aria-label="Note" class="form-control" name="note"><?php echo $setup['description']; ?></textarea>
                    </div>
                    <div class="form-group col-lg-12" style="margin:0px;">
                        <span class="form-label">Please select the applicable criteria for vendors:</span>
                    </div>
                     <div class="form-group col-lg-4">
                        <span class="form-label">Location</span>
                        <select aria-label="Applicable Locations[]" class="form-select js-example-basic-multiple" multiple="multiple" name="applicable_locations[]">
                            <option <?php if(in_array(0,(array)$allow_locselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['locations'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$allow_locselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                     <div class="form-group col-lg-4">
                        <span class="form-label">Vendors</span>
                        <select aria-label="Applicable Vendors[]" class="form-select js-example-basic-multiple" multiple="multiple" name="applicable_vendors[]">
                            <option <?php if(in_array(0,(array)$allow_venselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['vendors'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$allow_venselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-12" style="margin:0px;">
                        <span class="form-label">Who are the users who can provide feedback :</span>
                    </div>
                     <div class="form-group col-lg-4">
                        <span class="form-label">Location</span>
                        <select aria-label="Provide Locations[]" class="form-select js-example-basic-multiple" multiple="multiple" name="provide_locations[]">
                            <option <?php if(in_array(0,(array)$provide_locselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['locations'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$provide_locselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                     <div class="form-group col-lg-4">
                        <span class="form-label">Department</span>
                        <select aria-label="Provide Departments[]" class="form-select js-example-basic-multiple" multiple="multiple" name="provide_departments[]">
                            <option <?php if(in_array(0,(array)$provide_depselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['departments'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$provide_depselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Designations</span>
                        <select aria-label="Provide Designations[]" class="form-select js-example-basic-multiple" multiple="multiple" name="provide_designations[]">
                            <option <?php if(in_array(0,(array)$provide_desselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['designations'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$provide_desselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                     <div class="form-group col-lg-4">
                        <span class="form-label">Role</span>
                        <select aria-label="Provide Roles[]" class="form-select js-example-basic-multiple" multiple="multiple" name="provide_roles[]">
                            <option <?php if(in_array(0,(array)$provide_roleselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['application_roles'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$provide_roleselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Employees</span>
                        <select aria-label="Provide Employees[]" class="form-select js-example-basic-multiple" multiple="multiple" name="provide_employees[]">
                            <option <?php if(in_array(0,(array)$provide_empselect)){ echo "selected";
} ?> value="0">ALL</option>
                            <?php foreach($result['employees'] as $list){?>
                                <option <?php if(in_array($list['id'],(array)$provide_empselect)){ echo "selected";
} ?> value="<?php echo  $list['id']; ?>"><?php echo  $list['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                     <?php if(!isset($_GET['Rid']) && $_GET['Rid']==""){ ?>
                     <div class="form-group col-lg-12">
                        <input type="hidden" name="submitvendorsetup" value="submitvendorsetup">
                         <button class="btn btn-primary  float-right"> Next </button>

                     </div>
                    </form>
                <?php } ?>
                </div>
            </div>
        </div>
        <?php if($_GET['Rid']!=""){ ?>
        <div class="card" style="<?php echo ( $setup['current_status'] =="52")?"pointer-events: none; opacity: 0.7;":"";?>">
            <div class="card-header">
                <h5>Review Category and Questions</h5>
                <div class="card-header-right">
                     <button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addCategory('<?php echo $i; ?>')"><i class="icofont icofont-plus icon-white "></i>Category</button>
                     <div class="popup-member design-sec" style="right: 0; display: none;">
                            <div class="text-left mb-1 f-w-600 f-16" style="text-align: left;">Review Category
                            <button type="button" class="float-end member-close design-close" onclick="closebtn()"><i class="ti-close"></i></button>
                            </div>
                            <div class="form-group w-full mt-2 row" style="text-align: left;">
                                <div class="col-lg-12">Category Name </div>
                                <div class="col-lg-12">
                                    <input aria-label="Category Name" type="text" class="form-control" id="category_name" name="category_name">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary float-right mt-2" onclick="addCategoryName()"> Save</button>
                        </div>
                </div>
            </div>
            <div class="card-body" style="padding: 15px 11%; ">
                <div class="" style="">

                    <!----------------- Category Start ---------------->

                    <div class="col-lg-12 row" id="cate">
                        <?php foreach($categories as $cat){ ?>
                        <div class="mt-3" id="cat_id_<?php echo $cat['id']; ?>">
                            <div class="row" style="clear: both;">
                                <div class="col-lg-10 col-md-10 col-sm-12 col-12 f-15 f-w-600 p-0 text-info">
                                     <input aria-label="Category" class="cat_1" type="text"  name="category"
                                      value="<?php echo $cat['category_name']; ?>" onblur="updatedetail('category_name', <?php echo $cat['id']; ?> ,this.value)" >
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-12 text-end p-0 mt-2">
                                    <button type="button" class="btn btn-success waves-effect waves-light" onclick="addQuestion(<?php echo $cat['id']; ?>)">
                                        <i class="icofont icofont-plus icon-white"></i>Questions
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4" id="cat_que_id_<?php echo $cat['id']; ?>">
                                <?php foreach($questions[$cat['id']] as $que_id=>$val){ ?>
                                <div class="" id="ques_id_<?php echo $que_id; ?>">
                                    <div class="p-20 z-depth-top-0 m-t-10 row" id="" >
                                        <div class="col-lg-11">
                                            <h6 class="f-w-600"><?php echo $val['question']; ?></h6>
                                        </div>
                                        <div class="col-lg-1 text-end" >
                                            <i class="ti-pencil-alt cursor-pointer" onclick="addQuestion(<?php echo $cat['id'].",".$que_id.",".$que_id; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                        </div>
                                        <div class="col-lg-12">
                                            <?php
                                            $optionlist= json_decode($val['question_option'],1);
                                            if($val['options_display_type']==4){ ?>
                                                <textarea aria-label="Text area" id="" class="form-control mb-2" readonly></textarea>
                                            <?php } ?>
                                            <?php
                                            if($val['options_display_type']==2){ ?>
                                                <div class="rating f-12 text-warning mt-2 d-inline-block">
                                                  <?php for($n=1;$n<=$optionlist['0'];$n++){ ?>
                                                      <i class="fa fa-star f-16 text-warning" ></i>
                                                  <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if($val['options_display_type']==3){?>
                                            <div class="mt-2 d-inline-block">
                                            <?php   foreach ($optionlist as $option){ ?>
                                                <b class="mr-10 d-inline-block">
                                                    <div class="radio-group required">
                                                        <input aria-label="Radio option" type="radio" value="<?php echo $option;?>" name="response" >
                                                        <label class="f-w-500" for="q1"><?php echo $option;  ?> </label>
                                                    </div>
                                                </b>
                                            <?php } ?>
                                            </div>
                                            <?php } ?>
                                        </div>


                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <!----------------- Category End   ----------------->

                </div>

            </div>
            <div class="card-footer" style="padding: 15px 11%;">
                <input type="hidden" name="submitvendorsetup" value="submitvendorsetup">
                 <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>
            </div>
        </div>
</form>

<div id="kt_modal_KTDatatable_setup" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog  modal-xl" >
    <div class="modal-content"  id="popup2">
    </div>
  </div>
</div>
<?php } ?>
