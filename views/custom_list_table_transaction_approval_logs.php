 <?php
if($_POST['option']!=""){
  $_GET['option'] = $_POST['option'];
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $table = $_REQUEST['table'];
}

$_SESSION['apply_js']['table']= 1;
$title = "Approval List";
$table = $_GET['table']!="" ? $_GET['table'] : 'emp_it_declaration';
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table;
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$lists = json_decode($list['list_attributes'],1);
foreach ($lists as $key => $value) {
  if ($value['display_list'] ==1 ) {
    $dis[$value['display_postion']] ['name'] = $key;
    $dis[$value['display_postion']] ['lable'] = $value['lable'];
    $dis[$value['display_postion']] ['type'] = $value['type'];
    $dis[$value['display_postion']] ['add_custom_field'] = $value['add_custom_field'];
    if(in_array($value['type'], $type_array)){
      $field[$key] =  dyanamicselectdata($table,$key);
    }

  }
}
ksort($dis);
$ststutwhere["where"] = array('table_name '=>$table,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$ststutwhere["field"] = "id,name,status";
$statusdata = urlencode(json_encode($ststutwhere));
$statusurl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$statusdata;
$statusjsondecode = callAPI($statusurl);
$statusjsondecode = json_decode($statusjsondecode,1);
 $whrcon["where"]['is_active'] = $_POST['status'] = 0;
if(isset($_POST['current_status']) && $_POST['current_status']!=""){
  $whrcon["where"]['current_status'] = $_POST['current_status'];
}
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=togettransactionlogtalbe&table=".$table."&data=".$vdata;
$response = callAPI($url);
$outdata = json_decode($response,1);
$bills = $outdata['data'];
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
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
</script>

 <div class="pcoded-inner-content mob-pd p-0">
  <div class="main-body">
    <div class="page-wrapper mob-pd p-0">
     <!-- Page-body start -->
     <div class="page-body">
      <div class="">
        <div class="">

          <div class="card">
            <div class="card-header table-card-header">
               <div class="">
                <form method="post" id="custom_frm_submit" class="row" >
                    <div class="col-lg-2">
                      <h5 class=""><?php echo $title;  ?></h5>
                      <a href="javascript:history.go(-1)" class="mb-2 mob-import ml-1 float-right">
                        <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icofont icofont-reply text-primary"></i></button>
                      </a>
                    </div>
             <div class="table-card-header col-lg-4">
                <div class="form-radio ">
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Status" type="radio" name="status" value="0" <?php echo $_POST['status']=='0'?"checked":"";?> onclick="filterApllay('-0')" onkeydown="">
                                    <i class="helper"></i>Unread
                                  </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Status" type="radio" name="status" value="1" <?php echo $_POST['status']=='1'?"checked":"";?> onclick="filterApllay('-0')" onkeydown="">
                                    <i class="helper"></i>Read
                                  </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Status" type="radio"  name="status" <?php echo ($_POST['status']=='-1' || !isset($_POST['status']))?"checked":"";?> value="-1" onclick="filterApllay('-0')" onkeydown="">
                                    <i class="helper"></i>All
                                  </label>
                                </div>
                            </div>
              </div>
              <div class="card-header-right table-card-header col-lg-6 text-right">
                <input type="hidden" name="current_status" id="current_status" value="<?php echo $_POST['current_status']; ?>">
                  <?php if(!empty($statusjsondecode)){ ?>
                <div class="dropdown-warning dropdown open">
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
                <button type="button" onclick="filterApllay(<?php echo $arststuslist['status']; ?>)" class="dropdown-item waves-light waves-effect"><?php echo $arststuslist['name']; ?></button>
                <?php } ?>
                 <button type="button" onclick="filterApllay()" class="dropdown-item waves-light waves-effect">All</button>
              </div>
            </div>
       <?php } ?>


            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>

              </div>

               </form>

            </div>
            </div>


            <div class="card-block">

         <?php if(count((array)$bills)>0){ ?>
                 <table id="new-cons" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <?php foreach ($dis as $header) {
                          $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                          ?>
                            <th scope=\"col\"><?php echo $header['lable']; ?></th>
                            <?php if($after == 1){
                              $afterdata=dyanamicafterdata($table, $header['name']);

                              if($afterdata['display'] != 'insert'){
                                $afterfield[$header['name']] =  $afterdata['value'];
                             ?>
                              <th scope=\"col\"><?php echo  $afterdata['lable']; ?></th>
                             <?php } else{
                                $afterfield[$header['name']] =  '#no#';
                             }
}?>
                      <?php } ?>
                      <th scope=\"col\">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i =1;
                    $active = "kt-badge kt-badge--brand kt-badge--inline kt-badge--pill";
                    foreach ($bills as $bill) {

                        $send = array();
                        foreach ($primary as  $value) {
                            $send[]= "$value:".$bill[$value];
                        }
                        $send = implode(",", (array)$send);
                        $rid = deocdeId($send);
                     ?>
                     <tr>
                        <?php   $date_array = array('status_date','from_date','to_date','created_date','updated_date');
                            $status_array = array('is_active','status_name');
                            $user_names = array('created_by','updated_by');

                            foreach ($dis as $header) {
                            $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                            if(in_array($header['type'], $type_array)){
                              $str = $bill[$header['name']];
                              $str =  json_decode($str);
                              if(is_array($str)){
                                foreach ($str as  $st) {
                                  $sts[] = $field[$header['name']][$st];
                                }
                                $str = implode(",", (array)$sts);

                              }elseif( $header['type'] == 'color_pick'){
                                $str = "<div style='background: #".$bill[$header['name']].";width: 20px;height: 20px;'>&nbsp;</div>";
                              }elseif( $header['type'] == 'image'){
                                $bimage = 'uploads/all/'.$bill[$header['name']];
                                $str = "<img src='$bimage' style='width:35px;' alt='Preview'>";
                              }else{
                                $str =  $bill[$header['name']];

                                $str = $field[$header['name']][$str];
                              }
                              ?>

                                 <td><?php echo todisplayviewfiled($str,$header['type']); ?></td>
                            <?php }elseif( $header['type'] == 'date_pick'){
                 ?>
                               <td><?php echo  $str = $bill[$header['name']] != "" ? fromDate($bill[$header['name']]) : ""; ?></td>
                            <?php }else{
                            if ($header['name'] == 'company_id') { ?>
                                  <td><?php echo $_SESSION['company'][$bill['company_id']]; ?></td>
                            <?php }elseif($header['type'] == 'number_type'){?>
                               <td style="text-align: right;"><?php echo customeummarmonyformate($bill[$header['name']]); ?></td>

                          <?php }  elseif (in_array($header['name'], $status_array)) { ?>
                                <td>
                                  <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                                  <span  class="form-label label label-lg  label-primary label-inline f-w-900 "><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                                </td>
                            <?php } elseif( $header['name'] == 'current_status') { ?>
                            <td>
                              <div class="btn-group">
                                                       <button onclick="displaystausdrop(<?php echo $bill['id']; ?>,<?php echo "'".$table."'"; ?>,<?php echo $bill['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$bill[$header['name']]]['name']; ?> </button>
                                                      <div class="dropdown-menu" id="dropdownlist_<?php echo $bill['id']; ?>">

                                                      </div>
                                                  </div>
                            </td>

                          <?php   }elseif (in_array($header['name'], $user_names)) { ?>
                                 <td><?php echo todisplayviewfiled($_SESSION['users'][$bill[$header['name']]],$header['type']); ?></td>
                            <?php } elseif(in_array($header['name'], $date_array)){?>
                                  <td><?php echo dateformates($bill[$header['name']],$configdata['date_format'],$configdata['date_separator']); ?></td>
                              <?php } else { ?>
                                    <td>   <?php echo todisplayviewfiled($bill[$header['name']],$header['type']); ?> </td>
                              <?php }
                          } ?>
                            <?php if($after == 1 && $afterfield[$header['name']] != '#no#' ){ ?>
                            <td><?php echo todisplayviewfiled($afterfield[$header['name']],$header['type']); ?></td>
                            <?php } ?>
                        <?php }?>
                        <td>
                          <?php
                             $edit = $edit_permission == 1 ? "index.php?option=".$insert."&Rid=".$rid : "javascript:showpermission()";
                            $link_copy = $insert_permission == 1 ? "index.php?option=".$insert."&copy=1&Rid=".$rid : "javascript:showpermission()";
                            $edit =  "index.php?option=".$insert."&Rid=".$rid;
                            $link_copy =  "index.php?option=".$insert."&copy=1&Rid=".$rid;

                            $view =  "index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=2021-2022&version_no=0";
                          ?>

                              <a href="<?php echo $view; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails"><i class="fa fa-eye"></i></a>
                        </td>

                      </tr>
                      <?php $i++;
} ?>
                  </tbody>
                   <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <h2 class="f-40 fw-bolder m-b-30">Hello!</h2>
                        <p class="text-muted f-16 fw-bold m-b-30"><?php echo $empty_data; ?></p>

                    </div>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                <?php } ?>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- Page-body end -->
  </div>
</div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
