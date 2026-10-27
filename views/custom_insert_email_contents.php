<?php

$_SESSION['apply_js']['textediter'] = '1';
$_SESSION['apply_js']['multiselest'] = 1;
if($_POST['submit']=='Add'){

    $data['id'] = $_POST['Rid'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['is_active'] = 0;
    $data['current_status']=0;
    $data['application_module_list'] = $_POST['application_module'];
    $data['template_type_id'] = $_POST['template_type_id'];
    $data['template_name'] = $_POST['template_name'];
    $data['email_subject_cc'] = $_POST['email_subject_cc'];
    $data['email_header_cc'] = $_POST['email_header_cc'];
    $data['email_id_cc'] = $_POST['email_id_cc'];
    $data['email_id_to'] = $_POST['email_id_to'];
    $data['email_content_cc'] = $_POST['email_content_cc'];
    $data['button_label'] = $_POST['button_label'];
    $data['email_footer'] = $_POST['email_footer'];
    $data['email_subject_to'] = $_POST['email_subject_to'];
    $data['email_header_to'] = $_POST['email_header_to'];
    $data['email_content_to'] = $_POST['email_content_to'];
    $data['send_notification'] = $_POST['send_notification'];
    $data['notification_cc'] = $_POST['notification_cc'];
    $data['notification_to'] = $_POST['notification_to'];
    $data['transaction_data'] = $_POST['transaction_data'];
    $data['transaction_data_display'] = isset($_POST['transaction_data_display'])?"0":"1";
    $url = API."?mode=insertemailcontain&method=post";
    $response = file_post_contents($url, $data);
    if($_POST['submit_value']=="exit"){
        header("location:index.php?option=COM_LIST_EMAIL_CONTENT");
    }

}

$table_name = "email_content";

$wh4["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wh4["field"] = "id,email_template_type";
$data4 = urlencode(json_encode($wh4));
$url4 = API."?mode=gettabledetailswithconditions&table=email_template_types&data=".$data4;
$emailtemplatetypes = callAPI($url4);
$emailtemplatetypes = json_decode($emailtemplatetypes,1);

$wh5["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wh5["field"] = "id,module_id,module_name";
$data5 = urlencode(json_encode($wh5));
$url5 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data5;
$mopdule_setup = callAPI($url5);
$mopdulesetup = json_decode($mopdule_setup,1);

$ids= encodeId($_GET['Rid']);
$ridss = explode(':',$ids);
$Rid = $ridss['1'];
$wh3["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','id'=>$Rid);
$data3 = urlencode(json_encode($wh3));
$url3 = API."?mode=gettabledetailswithconditions&table=email_content&data=".$data3;
$jsondecode3 = callAPI($url3);
$jsondecode3 = json_decode($jsondecode3,1)[0];

?>
<script type="text/javascript">
    function textValueInsert(value,id){
        CKEDITOR.instances[id].insertText(" "+value+" ");
    }
    function encodedecode(functions,classs,meanclass){
        if(functions=="encode"){
            $("#encode").val("encode"); }else{ $("#encode").val("decode");
        }
        $.ajax({
            url: "ajax.php",
            data:'page=getfildsarrayemailcontains',
            type: "POST",
            success:function(data){
                jQuery.each(JSON.parse(data), function(index, itemData) {
                    var str = CKEDITOR.instances[meanclass].getData();
                    if(functions=="encode"){
                        $("#"+meanclass+"_hid").val('1');
                        var replace_value = str.replaceAll(index, itemData[1])
                        $(".cke_wysiwyg_frame").contents().find("."+classs).html(replace_value);
                    }else{
                        $("#"+meanclass+"_hid").val('0');
                        var replace_value = str.replaceAll(itemData[1],index)
                        $(".cke_wysiwyg_frame").contents().find("."+classs).html(replace_value);
                    }
                });
            },
        });

        setTimeout(function(){
            if($("#editor1_hid").val() == '0' && $("#editor11_hid").val() == '0'){
                $(".card-footercustom").show();
            }else{
                $(".card-footercustom").hide();
            }
        }, 800);
    }

    function toSubmit(){
        var encode = $("#encode").val();
        if(encode=="decode"){
            var replace_value = $(".note-editable").html();
            $("#summernote").val(replace_value);
        }else{
            alert("Please You Select Replace code");
            return false;
        }
    }

    function emailPreviews(){
        $(".emty_temp_id").text("");
        if($("#template_id").val()==""){
            $(".emty_temp_id").text("Requerd fileds");
            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Please Enter Template Id','');
        }else{
            CKEDITOR.instances['editor1'].updateElement();

            CKEDITOR.instances['editor11'].updateElement();

            let uri = $("#formdatasub").serialize();
            uri = encodeURI(uri);
            $.ajax({
                url: "views/custom_email_content_preview_popup.php",
                data:uri,
                type: "POST",
                success:function(data){
                    $("#email_previews_modal_view").html(data);
                    $("#email_previews_modal").modal("show");
                }
            });
        }
    }

</script>
<style>
    .stickyprivewbtn{
        position: fixed;
        right: 5%;
        top: 50%;
    }
    .custom-menu {
    display: none;
    z-index: 1000;
    position: absolute;
    overflow: hidden;
    border: 1px solid #CCC;
    white-space: nowrap;
    font-family: sans-serif;
    background: #FFF;
    color: #333;
    border-radius: 5px;
}
.custom-menu li {
    padding: 8px 12px;
    cursor: pointer;
}
.custom-menu li:hover {
    background-color: #DEF;
}
</style>
<div class="page-body">
    <div class="card">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                 <div class="card-header">
                       <h5 class="f-24"> Add Email Template</h5>
                    </div>
                    <div class="card-body p-l-50 p-r-50">
                        <div class="form-group row ml-20 mr-20 ">
                            <form class="kt-form" id="formdatasub" method="post" action=""  name="myForm" enctype="multipart/form-data">
                                <input type="hidden" id="editor1_hid" value="0">
                                <input type="hidden" id="editor11_hid" value="0">
                                <input type="hidden" name="Rid" value="<?php echo $Rid; ?>">
                                <div class="row">
                                    <?php  if($Rid ==""){ ?>
                                    <div class="form-group col-lg-6 w-full">
                                        <span class="form-label">Module</span>
                                        <select aria-label="Application Module[]" class="form-select js-example-basic-multiple" id="application_module" name="application_module[]" multiple>
                                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                                                <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['application_module'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php }else{ ?>
                                        <div class="form-group col-lg-6 w-full">
                                        <span class="form-label">Module</span>
                                        <select aria-label="Application Module" class="form-select js-example-basic-single" id="application_module_2" name="application_module">
                                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                                                <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $jsondecode3['application_module'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <?php } ?>
                                    <div class="form-group col-lg-6 w-full">
                                        <span class="form-label">Template Type</span>
                                        <select aria-label="Template Type" class="form-select js-example-basic-single" id="template_type_id" name="template_type_id">
                                            <?php foreach($emailtemplatetypes as $emailtemplatetypes){ ?>
                                                <option value="<?php echo $emailtemplatetypes['id']; ?>" <?php echo $jsondecode3['template_type_id'] == $emailtemplatetypes['id']?"selected":""; ?>><?php echo $emailtemplatetypes['email_template_type']; ?></option>
                                            <?php } ?>
                                        </select>

                                        <span class="f-12 text-danger emty_temp_id"></span>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Template Name</span>
                                        <input aria-label="Template Name" class="form-control" type="text" dada="template_name" name="template_name" value="<?php echo $jsondecode3['template_name']; ?>" id="textArea_2">
                                        <span class="f-12 text-danger emty_temp_id"></span>
                                        <div>
                                            <ul class='custom-menu'>
                                              <li><button type="button" class="btn btn-link p-0 text-dark text-decoration-none border-0" onclick="addTextAtCaret('textArea','{first_name}')">First Name</button></li>
                                              <li><button type="button" class="btn btn-link p-0 text-dark text-decoration-none border-0" onclick="addTextAtCaret('textArea','{middle_name}')">Middle Name</button></li>
                                              <li><button type="button" class="btn btn-link p-0 text-dark text-decoration-none border-0" onclick="addTextAtCaret('textArea','{last_name}')">Last Name</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Button Label</span>
                                        <input aria-label="Button Label" class="form-control" type="text" id="button_label" name="button_label" value="<?php echo  $jsondecode3['button_label']; ?>">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Email Footer</span>
                                        <input aria-label="Email Footer" class="form-control" type="text" id="email_footer" name="email_footer" value="<?php echo $jsondecode3['email_footer']; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header mb-3">
                                                <h5>To</h5>
                                            </div>
                                            <div class="p-3">
                                                <div class="form-group">
                                                <span class="form-label">Email Subject</span>
                                                <input aria-label="Email Subject To" class="form-control" type="text" id="email_subject_to" name="email_subject_to" value="<?php echo $jsondecode3['email_subject_to']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <span class="form-label">Email Header</span>
                                                <input aria-label="Email Header To" class="form-control" type="text" id="email_header_to" name="email_header_to" value="<?php echo $jsondecode3['email_header_to']; ?>">
                                            </div>

                                            <div class="form-group">
                                                <span class="form-label">Email Addressed</span>
                                                <input aria-label="Email  To" class="form-control" type="text" id="email_id_to" name="email_id_to" value="<?php echo $jsondecode3['email_id_to']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <span class="form-label">Notification</span>
                                                <input aria-label="Notification To" class="form-control" type="text" id="notification_to" name="notification_to" value="<?php echo $jsondecode3['notification_to']; ?>">
                                            </div>

                                            <div class="form-group">
                                                <span class="form-label">Email Content</span>
                                                <textarea aria-label="Email Content To" class="form-control " name="email_content_to" id="editor11" style="display:inline-block;" > <?php echo $jsondecode3['email_content_to']; ?></textarea>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header mb-3">
                                                <h5>CC</h5>
                                            </div>
                                            <div class="p-3">
                                            <div class="form-group">
                                                <span class="form-label">Email Subject</span>
                                                <input aria-label="Email Subject Cc" class="form-control" type="text" id="email_subject_cc" name="email_subject_cc" value="<?php echo $jsondecode3['email_subject_cc']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <span class="form-label">Email Header</span>
                                                <input aria-label="Email Header Cc" class="form-control" type="text" id="email_header_cc" name="email_header_cc" value="<?php echo $jsondecode3['email_header_cc']; ?>">
                                            </div>

                                            <div class="form-group">
                                                <span class="form-label">Email Addressed</span>
                                                <input aria-label="Email  Cc" class="form-control" type="text" id="email_id_cc" name="email_id_cc" value="<?php echo $jsondecode3['email_id_cc']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <span class="form-label">Notification</span>
                                                <input aria-label="Notification Cc" class="form-control" type="text" id="notification_cc" name="notification_cc" value="<?php echo $jsondecode3['notification_cc']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <span class="form-label">Email Content</span>
                                                <textarea aria-label="Email Content Cc" id="editor1" class="summernote email_content_1" name="email_content_cc"  > <?php echo $jsondecode3['email_content_cc']; ?></textarea>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-2">


                                                <div class="card-footer j-datebutoon  card-footercustom">
                                                    <div class="row">
                                                        <div class="col-lg-9">
                                                            <input type="hidden" name="submit" value="Add">
                                                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i>Save &amp; exit</button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </form></div>
                                    </div>
                                </div>
                            </div>
                        </div>

   </div>

   <div id="email_previews_modal" class="modal fade" role="dialog" aria-hidden="true">
       <div class="modal-dialog modal-lg " >
           <div class="modal-content"  id="email_previews_modal_view">

           </div>
       </div>
   </div>
