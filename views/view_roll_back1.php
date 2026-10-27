<?php
  $_SESSION['apply_js']['multiselest'] = "1";
  if($_POST['option']!=""){
    $_GET['option'] = $_POST['option'];
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
  }else{
    $_SESSION['apply_js']['table']= 1;
  }
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
<?php
}

$viewtime =0;
if($viewtime ==1 ){
  echo "after include center start - ".date('m/d/Y h:i:s a')."</br>";
}
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);

$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
if($viewtime ==1 ){
  echo "after include center Step :1 - ".date('m/d/Y h:i:s a')."</br>";
}
$current_page = $_POST['current_page']!=""?$_POST['current_page']:1;
  $dynamictableurl = API."?mode=getdynamictabledetails&application_table_name=".$page_details['application_table_name']."&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&vendor_id=".$_SESSION['vendor_id']."&emp_id=".$_SESSION['emp_id']."&admin_access_employee=".$_SESSION['admin_access_employee']."&limit_select=20&current_page=".$current_page;
$dynamictableresult = callAPI($dynamictableurl);
$dynamictableres = json_decode($dynamictableresult,1);
if($viewtime ==1 ){
  echo "after include center Step :2 - ".date('m/d/Y h:i:s a')."</br>";
}
$list = $dynamictableres['display_attributes'];
$statusjsondecode = $dynamictableres['status_progress'];
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$table = $list['application_table_name'];
$insert_permission = $_SESSION['role_permission'][$table]['permission_insert'];
$edit_permission = $_SESSION['role_permission'][$table]['permission_update'];
$delete_permission = $_SESSION['role_permission'][$table]['permission_delete'];
$permission_print = $_SESSION['role_permission'][$table]['permission_print'];
$permission_import = $_SESSION['role_permission'][$table]['permission_import'];
$permission_download = $_SESSION['role_permission'][$table]['permission_download'];
if($insert_permission == '1' && $_SESSION['admin_type'] !='1'){$insert_permissionbtn = "disabled";
}
if($permission_import == '1' && $_SESSION['admin_type'] !='1'){$permission_importbtn = "disabled";
}
if($edit_permission == '1' && $_SESSION['admin_type'] !='1'){$edit_permissionbtn = "disabled";
}

$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$edit_insert =  $page_details['edit_page_name'];
$content = $list['help_text']!='' ? $list['help_text'] : 'Help Test';
$lists = json_decode($list['list_attributes'],1);
$empty_data = $list['empty_table_message'];
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
$whrcon = array();
$emp_array   = array();
if($viewtime ==1 ){
  echo "after include center Step :3 - ".date('m/d/Y h:i:s a')."</br>";
}
foreach ($lists as $key => $value) {
  $tablefiledslist[$key] = $value['lable'];
  if($value['display_list'] ==1 ){
    $dis[$value['display_postion']] ['name'] = $key;
    $dis[$value['display_postion']] ['lable'] = $value['lable'];
    $dis[$value['display_postion']] ['type'] = $value['type'];
    $dis[$value['display_postion']] ['add_custom_field'] = $value['add_custom_field'];
    $defalut_fields[] = $key;
  }
}
if(count((array)$dis) !=0){
  ksort($dis);
}
 $field =  $dynamictableres['field_result'];
 $primary = $dynamictableres['primarykey'];
 if($viewtime ==1 ){
  echo "after include center Step :5 - ".date('m/d/Y h:i:s a')."</br>";
}
 include_once "new_from_submit.php";
 if($viewtime ==1 ){
  echo "after include center Step :6 - ".date('m/d/Y h:i:s a')."</br>";
}
$stausresult = ViewStatusData();
$bills = $dynamictableres['dynmictabler'];
$noinsertview = array('vendors');
?>
<script type="text/javascript">
  function dynamicFilterTable(current_status="",current_page="1"){
    console.log(current_status);
    if(current_status !=''){
      $("#current_status").val(current_status);
    }
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#dyanmictableshow").html(htmloader);

    var postform = $("#custom_frm_submit").serialize();
    var limit_select = $("#limit_select").val();
    var filter_text = $("#filter_text").val();
     var filter_dynamic_select = {};
     $('.filter_dynamic_select').each(function () {
        var name = $(this).attr('name');
        var key = name.match(/\[([^\]]+)\]/)[1];
        filter_dynamic_select[key] = $(this).val();
    });
     console.log(filter_dynamic_select);
 jQuery.ajax({
            url: "views/custon_list_dynamic_table.php",
            data: postform+"&filterajax=Y&limit_select="+limit_select+"&current_page="+current_page+"&filter_text="+filter_text+"&filter_dynamic_select="+JSON.stringify(filter_dynamic_select),
            type: "POST",
            success:function(data){
              $("#dyanmictableshow").html(data);
            },
            error:function (){}
        });
      console.log(postform);
      console.log(current_status);
  }

  function dynamicinsertpopup(option){
    jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
  }
  function dynamicInsertPopUpNew(option,id=""){
    jQuery.ajax({
      url: "views/custom_inbox_page_redirection.php",
      data:'option='+option+"&rid="+id+"&ajaxpage=Y",
      type: "POST",
      success:function(data){
        $('#popup2').html(data);
        $('#kt_modal_KTDatatable_remote1').modal("show");
        //   showOn: "both",
        //   dateFormat: DateFormate,
        //   changeMonth: true,
        //   changeYear: true
      },
      error:function (){}
    });
  }

   function statuspopup(status,id){
      alert("data"+status+"id:"+id);
     var table_name = document.getElementById('table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
   }

  function displaystausdrop(id,table,status_id){
    $.ajax({
          url: "ajax.php",
          data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
          type: "POST",
          success:function(data){
            console.log(data);
              $("#dropdownlist_"+id).html(data);
          },
          error:function (){}
      });

  }
  function filterApllay(id){
    if(id!='-0'){
      $("#current_status").val(id);
    }
    $("#custom_frm_submit").submit();
  }
  function exportDyanmicCsv(){
      var dataArray = $("#custom_frm_submit").serializeArray();
       console.log(dataArray);
      var dynmicurl = "";
      $(dataArray).each(function(i, field){
        if(field.name!= 'option'){
          dynmicurl += "&"+field.name+"="+field.value;
        }
      });

      var url = "dynamic_csv_download.php?test=Y"+dynmicurl;
      window.location.href = url;
      console.log(url);
  }

  function dynamicFilterTableText(){
    var filter_text = $("#filter_text").val();
    if(filter_text.length >= 3 || filter_text.length == 0){
         dynamicFilterTable();
    }else{

    }
}

function showSelectExtraColumn(divid){
  $(".custom-popup-open").hide();
  $("#"+divid).show('slow');
}
function closeSelectExtraColumn(){
    $(".custom-popup-open").hide();
}

function goSelectExtraColumn(){
  closeSelectExtraColumn();
  dynamicFilterTable();
}

</script>
<?php
if($viewtime ==1 ){
  echo "after include center HTML - ".date('m/d/Y h:i:s a')."</br>";
}
?>
 <div class="pcoded-inner-content mob-pd">
  <div class="main-body">
    <div class="page-wrapper mob-pd p-0">
     <!-- Page-body start -->
     <div class="page-body">
        <div class="card">

          <div class="">
            <div class="card-header table-card-header">
                <div class="">
                <form method="post" id="custom_frm_submit" class="row" >
                  <input type="hidden" name="option" value="<?php echo $_GET['option']; ?>">
                  <input type="hidden" name="table" value="<?php echo $table; ?>">

                    <div class="col-lg-3">
                     <h5 class=""><?php echo $title;  ?></h5>
                     <a href="javascript:history.go(-1)" class="mb-2 mob-import ml-1 float-right">
                      <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icofont icofont-reply text-primary"></i></button>
                    </a>
                    </div>
             <div class="table-card-header col-lg-3">
                <div class="form-radio ">
                      <div class="radio radio-inline">
                        <label class="form-label">
                          <input aria-label="Status" type="radio" name="status" value="0"   onclick="dynamicFilterTable()" checked onkeydown="">
                          <i class="helper"></i>Active
                        </label>
                      </div>
                      <div class="radio radio-inline">
                        <label class="form-label">
                          <input aria-label="Status" type="radio" name="status" value="1"  onclick="dynamicFilterTable()" onkeydown="">
                          <i class="helper"></i>Inactive
                        </label>
                      </div>
                      <div class="radio radio-inline">
                        <label class="form-label">
                          <input aria-label="Status" type="radio"  name="status" value="-1" onclick="dynamicFilterTable()" onkeydown="">
                          <i class="helper"></i>All
                        </label>
                      </div>
                  </div>
              </div>
              <div class="card-header-right table-card-header col-lg-6 text-right">
                <input type="hidden" name="current_status" id="current_status" value="">
                  <?php if(count((array)$statusjsondecode) !=0){ ?>
                <div class="dropdown-warning dropdown open position-relative status_custom_btn">
                  <button class="btn btn-warning dropdown-toggle waves-effect waves-light mb-2"
                  type="button" id="dropdown-5" data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"> <i class="fa fa-filter"></i>
                  <?php if(isset($_POST['current_status']) && $_POST['current_status']!=""){ foreach($statusjsondecode as $arststuslists){    if($arststuslists['status']==$_POST['current_status']){ echo $arststuslists['name'];
}
                  }
}else{ echo "Status";
}?>
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdown-5"
              data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
              <?php foreach($statusjsondecode as $arststuslist){ ?>
                <a onclick="dynamicFilterTable('<?php echo $arststuslist['status']; ?>')" class="dropdown-item waves-light waves-effect" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $arststuslist['name']; ?></a>
                <?php } ?>
                 <a onclick="dynamicFilterTable('-1')" class="dropdown-item waves-light waves-effect" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $arststuslist['name']; ?></a>
              </div>
            </div>
       <?php } ?>

          <?php if($_SESSION['user_type']!=3 && !in_array($table,$noinsertview)){ ?>

            <?php if($table == "items"){ ?>

              <a target="_blank"  href="index.php?option=COM_INSERT_IMPORT_EXCEL_DATA"  class="btn btn-primary mb-2 desktop-view" <?php echo $permission_importbtn; ?>>
                <i class="icofont icofont-cloud-upload icon-white"></i>
                <?php echo "Import";  ?>
              </a>

            <?php }else{ ?>

          <a target="_blank"  href="index.php?option=COM_INSERT_DYNAMIC_IMPORT&table=<?php echo $table; ?>"  class="btn btn-primary mb-2 desktop-view" <?php echo $permission_importbtn; ?>>
            <i class="icofont icofont-cloud-upload icon-white"></i>
            <?php echo "Import";  ?>
          </a>
        <?php } ?>

          <?php if($permission_download == '0' || $_SESSION['admin_type'] =='1'){ ?>
          <a  href="#!" onclick="exportDyanmicCsv()" class="btn btn-inverse mb-2 desktop-view" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
            <i class="ti-export icon-white"></i>
            <?php echo "Export";  ?>
          </a>
          <?php } ?>


          <?php
          if($table=="employees" && $_SESSION['work_module_setup_id']==19){
                                $edit_insert = 'COM_VIEW_VMS_EMPLOYEE';
                                $insert = $edit_insert;
                            }

                    $link = "index.php?option=".$insert   ?>


            <?php if($list['insert_page_type'] ==2 && !in_array($table,$noinsertview)){?>
            <a target="_blank"  href="<?php echo $link; ?>"  class="btn btn-success mb-2 desktop-view" <?php echo $insert_permissionbtn; ?>>
             <i class="icofont icofont-plus icon-white"></i>
              <?php echo $title;  ?>
            </a>
            <a target="_blank"  href="<?php echo $link; ?>"  class="mb-2 mob-import" <?php echo $permission_importbtn; ?>>
             <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icon-plus text-primary"></i></button>

            </a>

            <?php }elseif($list['insert_page_type'] ==1 && !in_array($table,$noinsertview)){ ?>
              <?php if($page_details['insert_page_type']  == '2'){  ?>
            <a href="#" onclick="dynamicInsertPopUpNew(<?php echo "'".$insert."'"; ?>)" class="btn btn-success mb-2 desktop-view" <?php echo $insert_permissionbtn; ?>>
              <i class="icofont icofont-plus icon-white"></i>
              <?php echo $title;  ?>
            </button>
          <?php }else{  ?>
            <a href="#" onclick="dynamicinsertpopup(<?php echo "'".$insert."'"; ?>)" class="btn btn-success mb-2 desktop-view" <?php echo $insert_permissionbtn; ?>>
              <i class="icofont icofont-plus icon-white"></i>
              <?php echo $title;  ?>
            </button>
          <?php } ?>

            <a href="#" onclick="dynamicinsertpopup(<?php echo "'".$insert."'"; ?>)" class="mb-2 mob-import" <?php echo $permission_importbtn; ?>>
              <span class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icon-plus text-primary"></i></span>
            </button>

            <?php }else{  ?>
            <a href="<?php echo $link; ?>"  class="btn btn-success mb-2 desktop-view" <?php echo $insert_permissionbtn; ?>>
              <i class="icofont icofont-plus icon-white " ></i>
              <?php echo $title;  ?>
            </a>
            <a href="<?php echo $link; ?>"  class="mb-2 mob-import" <?php echo $permission_importbtn; ?>>
              <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icon-plus text-primary"></i></button>

            </a>
            <?php } ?>

          <?php } ?>


            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
            <button type="button" class="btn p-0 border-0 bg-transparent ti-settings f-18" data-bs-toggle="tooltip" data-placement="bottom" title="" onclick="showSelectExtraColumn('select_extra_column')" data-bs-original-title="Select Extra Column" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              </div>
                <div class="row" id="custom-popup">
                  <div class="custom-popup-open custom-popup-slide-query-contact_details text-start" style="left: 0px; top: 90px;" id="select_extra_column">
                        <div class="card">
                          <div class="card-header">
                            <h5>Column</h5>
                            <div class="card-header-right">
                              <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeSelectExtraColumn()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                            </div>
                          </div>
                          <div class="card-body">
                            <div class="form-group">
                              <select aria-label="Displayattributesfilterfields[]" class="form-select js-example-basic-multiple-limit_custom" name="displayattributesfilterfields[]" multiple="multiple">
                                <?php foreach($tablefiledslist as $keyvalue => $fieldvalue){
                                  $lock = "";
                                  if(in_array($keyvalue, (array)$defalut_fields)){
                                    $lock = 'locked="locked" disabled';
                                  }
                                  ?>
                                  <option value="<?php echo $keyvalue; ?>"  <?php echo in_array($keyvalue, (array)$defalut_fields) ? 'selected' : ''; ?>   <?php echo $lock; ?> > <?php echo $fieldvalue; ?> </option>
                                <?php  }  ?>
                              </select>
                            </div>
                          </div>
                          <div class="card-footer pt-0">
                            <button type="button" class="input-group-text addon-style cursor-pointer" onclick="goSelectExtraColumn()">Show</button>
                          </div>
                        </div>
                      </div>
                  <div class="col-lg-1 mt-2">
                    <span class="">Records</span>
                    <div class="w-full">
                      <select aria-label="Limit Select" class="form-select border js-example-basic-single" id="limit_select" name="limit_select" onchange="dynamicFilterTable()" >

                          <option value="20">20</option>
                          <option value="30">30</option>
                          <option value="40">40</option>
                          <option value="50">50</option>
                          <option value="60">60</option>
                          <option value="70">70</option>
                          <option value="80">80</option>
                          <option value="90">90</option>
                          <option value="100">100</option>
                          <option value="0">All</option>
                      </select>
                    </div>
                  </div>
                 <div class="col-lg-2 mt-2">
                   <span class=""><span class="f-10 text-warning">(Min 3 Characters Required)</span></span>
                    <input aria-label="Search" type="text" id="filter_text" class="form-control form-control-sm "  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search" onkeyup="dynamicFilterTableText()">
                </div>
                <?php foreach($field as $fieldname=>$fieldvalue){ ?>
                  <div class="col-lg-2 mt-2">
                      <span class=""><?php echo $lists[$fieldname]['lable']; ?></span>
                      <div  class="w-full">
                        <select aria-label="Select option" class="form-select border js-example-basic-single filter_dynamic_select" name="filter_dynamic_select[<?php echo $fieldname; ?>]"  onchange="dynamicFilterTable()" >
                          <option value="">All</option>
                          <?php foreach($fieldvalue as $keyfval=>$fval){ ?>
                            <option value="<?php echo $keyfval; ?>"><?php echo $fval; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                  </div>
                <?php } ?>
            </div>
               </form>

            </div>
            </div>


    <div class="card-block table-responsive" id="dyanmictableshow">
<?php

if($viewtime ==1 ){
  echo "after include center page include - ".date('m/d/Y h:i:s a')."</br>";
}
$pageurl = "custon_list_dynamic_table.php";
if(file_exists($pageurl)){
    include_once "custon_list_dynamic_table.php";
}else{
    include_once "views/custon_list_dynamic_table.php";
}
 ?>

  </div>

            </div>
          </div>


      </div>
    </div>
    <!-- Page-body end -->
  </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
<?php

if($viewtime ==1 ){
  echo "after include center page include - ".date('m/d/Y h:i:s a')."</br>";
}
?>
