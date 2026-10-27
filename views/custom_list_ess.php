<?php
$list = $_SESSION['employee'];
$emp_id = $list['id'];
$emp_display_name = $list['display_name'];
$emp_code = $list['emp_code'];
$emp_email = $list['email_id'];
$emp_image = $list['image'];
$vdata1['order'] = "process_default_display_sequence ASC";
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=ess_process&data=".$vdata;
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);

$vdata3["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
$vdata = urlencode(json_encode($vdata3));
$vurl = API."?mode=gettabledetailswithconditions&table=ess_process_documents&data=".$vdata;
$configesponse = callAPI($vurl);
$process_doc = json_decode($configesponse,1);
foreach($process_doc as $list_doc){
    $data_doc[$list_doc['process_id']][$list_doc['button_no']][]=$list_doc;
}
?>
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript">
    function displayitemdata(option,id,vll){


      var html = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $(".removedataajax").html(html);
      if(option!='COM_LIST_MY_TEAM'){
          $.ajax({
              url: "views/view_roll_back1.php",
              data:'option='+option+'&feild=emp_id&value='+$('#employee_id').val(),
              type: "POST",
              success:function(data){
                  $("#"+id).html(data);
                  console.log('rdya');

              },
              error:function (){}
          });
      }else{
          $.ajax({
              url: "views/custom_list_my_team.php",
              data:'option='+option+'&check=data&value='+$('#employee_id').val(),
              type: "POST",
              success:function(data){
                  $("#"+id).html(data);
              },
              error:function (){}
          });
      }
  }
  function displayitemdata_new(option,id){
        jQuery.ajax({
            url: "views/view_roll_back1.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }

</script>
<input type="hidden" name="employee_id" id="employee_id" value="<?php echo $emp_id; ?>">
<input type="hidden"  id="optionname" value="<?php echo $data[0]['application_page_name']; ?>">
<input type="hidden" id="optionid" value="kt_tab_pane_<?php echo $data[0]['id']; ?>">
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="ess-1">
                    <ul class="nav ">
                        <?php $im = 1; foreach ($data as $list){ ?>
                            <li class="nav-item" style="width: 8%">
                                <a class="nav-link <?php echo $im == '1'?"show active":"";?>"  onclick="displayitemdata(<?php echo '\''.$list['application_page_name'].'\''?>,<?php echo '\'ess_sub_middle_'.$list['id'].'\''?>)" data-bs-toggle="tab" href="<?php echo "#kt_tab_pane_".$list['id']; ?>" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <i class="<?php echo $list['process_icon']; ?>"></i>
                                    <span><?php echo $list['process_name']; ?></span>
                                </a>
                            </li>
                        <?php $im++;
} ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="ess-1">
                    <div class="tab-content mt-5" id="myTabContent">
                        <?php $tab_index = 1;
                        foreach ($data as $list){
                            $tabid =  "kt_tab_pane_".$list['id'];
                            $process_name = $list['process_name'];
                            $process_id = $list['id'];
                            $application_page_name = $list['application_page_name'];
                            $process_title = array('title'=>$process_name,'process_id'=>$process_id,'application_page_name'=>$application_page_name);
                            $process_name = $list['process_name'];
                            $process_desc = $list['process_description'];
                            $process_button1_label = $list['process_button1_label'];
                            $process_button1_url = $list['process_button1_url'];
                            $process_button2_label = $list['process_button2_label'];
                            $process_button2_url = $list['process_button2_url'];
                            $process_button3_label = $list['process_button3_label'];
                            $process_button3_url = $list['process_button3_url'];
                            $process_button4_label = $list['process_button4_label'];
                            $process_button4_url = $list['process_button4_url']; ?>
                            <div class="tab-pane  <?php echo $tab_index == '1'?"active":"";?>" id="<?php echo $tabid; ?>" role="tabpanel" aria-labelledby="<?php echo $tabid; ?>">
                                <div class="row">
                                    <div class="col-md-10">
<div class="">
                                            <div id='ess_sub_middle_<?php echo $process_title['process_id']; ?>'  class="removedataajax">
                                    <?php if($tab_index == '1'){
                                            $_POST['option'] = $list['application_page_name'];
                                            $_POST['feild'] = "emp_id";
                                            $_POST['value'] = $emp_id;
                                            $_POST['notshow'] = "N";
                                            include_once "view_roll_back1.php";
                                         }else{  ?>
                                        <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                                                </div>


                                    <?php } ?>
                                    </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="ess-related">
                                            <?php for($i=1;$i<5;$i++){
                                                $plab = "process_button".$i."_label";
                                                $purl = "process_button".$i."_url";
                                                if($list[$plab]!=""){
                                                    if($list[$purl]==""){ ?>
                                                        <div class="m-t-10" role="group">
                                                            <button id="btnGroupDrop1" type="button" class="btn btn-success font-weight-bold dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <?php echo $list[$plab]; ?>
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                                <?php if(!empty($data_doc[$list['id']][$i])){
                                                                    foreach ($data_doc[$list['id']][$i] as $dlist){
                                                                        $view = $dlist['document_url'] !="" ? "https://".$dlist['document_url'] : 'uploads/all/'.$dlist['process_file'];?>
                                                                        <a class="text-muted text-hover-primary" href="<?php echo $view; ?>" target="_blank"><?php echo $dlist['document_name']; ?></a>
                                                                <?php } ?>
                                                                <?php } else {
                                                                    echo '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9013'].'</div>';
                                                                 } ?>
                                                            </div>
                                                        </div>
                                                    <?php }else{
                                                        if(strtoupper($list[$plab]) == 'FAQ'){
                                                           $purl = "index.php?option=COM_LIST_FAQ&APPLICATION=".$list['application_page_name'];
                                                        } ?>
                                                        <a href="<?php echo $purl; ?>"   target="_blank" class="btn btn-success m-t-10"><?php echo $list[$plab]; ?></a>
                                                    <?php }
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $tab_index++;
} ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
