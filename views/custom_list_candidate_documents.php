<?php
$table_name = "candidates";
$option_c = $_GET['option'];
$RID_c = $_GET['Rid'];
if(isset($_POST['submit'])){
    $candidate_id = $_POST['candidate_id'];
    $k=0;
    $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/Documents";
    $imagename = array();
    foreach ($_FILES as $key => $file) {
        $hidimage = "hid".$key;
    $name = imageuploadwitharray($path,$key,$_POST[$hidimage],"document",$key);
    $postname = explode('_', $key);
    $imagename[$k]['id'] = $postname['1'];
    $imagename[$k]['name'] = $name;
    $k++;
  }
  $_POST['documents'] =  $imagename;
  $_POST['candidate_id'] =  $candidate_id;
  $url = API."?mode=updatecandaitedocument&method=post";
  $response = file_post_contents($url, $_POST, 'insert');
  if ($response) {
    header("location:index.php?option=COM_INSERT_CANDIDATE&NI=".$_GET['NI']."&Rid=".$RID_c);
  }
}
$vdata2["where"] = array('parent_document_id'=>0,'is_active'=>0,'category_id'=>1);
$vdata2["field"] = "id,document_name,parent_document_id";
$vdata2['order'] = "display_position ASC";
$vdata = urlencode(json_encode($vdata2));
$vurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$vdata;
$parentdocuments = callAPI($vurl);
$parentdocuments  = json_decode($parentdocuments,1);

?>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class=" flex-row ">
      <?php include_once 'candidate_side_nav.php'; ?>
      <?php
      $url1 = API."?mode=togetcandidatedocumentlist&cand_id=".$candidate_id;
      $response1 = callAPI($url1);
      $lists = json_decode($response1,1);
      foreach ($lists as $list) {
       $document_list[$list['id']] = $list['document_name'];
     }
     ?>
     <!--begin::Content-->
     <script type="text/javascript">
       function statsonlychange(id,status){
          $.ajax({
       url: "ajax.php",
       data:'page=updateonlystatus&hid='+id+'&table=candidate_documents&status='+status,
       type: "POST",
       success:function(data){
         var msg = <?php echo "'".$_SESSION['error_message']['9007']."'" ?>;
         console.log(msg);
         console.log(data);
         swal.fire({
           title: msg,
                            //text: ;,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: false,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
              if (result.value) {
                $('#kt_modal_KTDatatable_remote').modal('hide');
                window.location.reload();
              }
            });
         },

         error:function (){}
       });
      }
      function imageupload(){
          $("input[id='my_file']").click();
      }


  </script>


  <style>
    .alert.alert-custom.alert-white .alert-text {
      color: #3f4254;
    }
    .alert.alert-custom .alert-text {
      -ms-flex-item-align: center;
      align-self: center;
      -webkit-box-flex: 1;
      -ms-flex-positive: 1;
      flex-grow: 1;
    }
    .symbol {
      display: inline-block;
      -ms-flex-negative: 0;
      flex-shrink: 0;
      position: relative;
      border-radius: 0.42rem;
    }
    .nav-link {
      display: block;
      padding: 0.5rem 1rem;
    }
    .image-input {
      position: relative;
      display: inline-block;
      border-radius: 0.42rem;
      background-repeat: no-repeat;
      background-size: cover;
    }
    label.btn-hover-text-primary{
      cursor: pointer;
      position: absolute;
      right: -10px;
      top: -10px;
    }
    .image-input input {
      width: 0!important;
      height: 0!important;
      overflow: hidden;
      opacity: 0;
    }

  </style>

  <style>

    .pricing .title {
      background: #1ABB9C;
      height: 80px;
      color: #fff;
      padding: 15px 0 0;
      text-align: center;
    }
    .pricing_features {
      background: #FAFAFA;
      padding: 10px 7px;
      min-height: 230px;
      font-size: 13.5px;
    }
    .list-unstyled {
      padding-left: 0;
      list-style: none;
    }
    .pricing_features ul li {
      margin-top: 10px;
    }
    .ui-ribbon-container .ui-ribbon-wrapper {
      position: absolute;
      overflow: hidden;
      width: 85px;
      height: 88px;
      top: -3px;
      right: -3px;
    }
    .ui-ribbon-container .ui-ribbon {
      position: relative;
      display: block;
      text-align: center;
      font-size: 15px;
      font-weight: 700;
      color: #fff;
      transform: rotate(
        45deg);
      padding: 7px 0;
      left: -5px;
      top: 15px;
      width: 120px;
      line-height: 20px;
      background-color: #555;
      box-shadow: 0 0 3px rgb(0 0 0 / 30%);
    }
    .pricing .title span {
      background: rgba(51,51,51,0.28);
      padding: 2px 5px;
    }
  </style>
  <div class="flex-row-fluid ml-lg-8 ">
    <div class="row ">
     <div class="col-lg-12 ">
       <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
        <div class=" card-custom " id="kt_page_sticky_card">
         <div class="card-header table-card-header">
          <h5>  Required Documents</h5>
     </div>
     <div class="card-body">
       <div class="row">




            <?php if(count((array)$parentdocuments)>=1){
                  foreach ($parentdocuments as $parentdocuments) {
                   $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'candidate_id'=>$candidate_id,'category_id'=>1,'is_active'=>0);
                  $table = 'candidate_documents';
                   $vdata = urlencode(json_encode($whrcon));
                   $url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata;
                   $response = callAPI($url);
                   $datas = json_decode($response,1);
                   $vdata1["where"] = array('is_active'=> 0,'parent_document_id'=>$parentdocuments['id'],'category_id'=>1);
                   $vdata1["field"] = "id,document_name";
                   $vdata1['order'] = "display_position ASC";
                   $vdata = urlencode(json_encode($vdata1));
                   $vurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$vdata;
                   $vdata_details = callAPI($vurl);
                   $vdata_details = json_decode($vdata_details,1);
                   $displaydocuments = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/Documents/";
                   $ssjdj = 0;
                   foreach ($vdata_details as $valueecdsdcd){
                    $valuedd = filterArrayValue($datas,"document_list_id",$valueecdsdcd['id'])[0];

                    if(count((array)$valuedd) != 0){
                      $ssjdj = 1;
                    }

                  }

                  if($ssjdj != 0){
                    ?>
                    <div class="col-md-3 col-sm-6 col-xs-12 mb-10">
                      <div class="pricing ui-ribbon-container">

                        <div class="title" style="height: 50px !important;border-radius: 5px;">
                         <h6><?php echo ucwords($parentdocuments['document_name']); ?></h6>
                       </div>
                       <div class="x_content">
                        <div class="">
                          <div class="pricing_features">
                            <?php
                            foreach ($vdata_details as $valuee){
                             $value = filterArrayValue($datas,"document_list_id",$valuee['id'])[0]; ?>


   <div style="    min-height: 38px; box-shadow: 0px 1px 9px #cccccc; padding: 2px 10px 2px 10px;" class="alert alert-custom alert-white fade show" role="alert">
    <div class="alert-text">


     <div class="d-flex align-items-center mb-0">
      <div class="symbol symbol-35 symbol-success mr-3 mt-1">
        <?php if($value['document_shared']!=""){  ?>

          <?php if($value['current_status'] == ST_REJECTED){ ?>
            <i class="fa fa-times text-danger"></i>
          <?php }elseif($value['current_status'] == ST_APPROVED){ ?>
            <i class="fa fa-thumbs-up"></i>
          <?php }else{  ?>
           <i class="fa fa-hourglass-half"></i>
         <?php } ?>
         <?php } else { ?>
          <i class="fa fa-upload"></i>
        <?php }     ?>
      </div>
      <div class="d-flex flex-column font-weight-bold w-100">
       <a <?php if($value['document_shared']!=""){ ?> class=" nav-link text-active-primary me-6 active" href="<?php  echo $displaydocuments.$value['document_shared'];   ?>"  target="_blank" <?php
}else{ echo "class='nav-link me-6'";
} ?> >  <?php echo ucwords($document_list[$value['document_list_id']]); ?></a>
     </div>

     <div class="d-flex font-weight-bold">
       <?php if($value['current_status'] != ST_APPROVED){ ?>
         <div class="image-input image-input-empty image-input-outline mr-10 mt-2" id="kt_image_5"  style=" height:0px; ">
           <label class="btn-hover-text-primary mt-1 <?php //echo $value['document_shared']!=""?"mt-3":"mt-1"; ?>" data-action="change" data-toggle="tooltip" title="" data-original-title="Uploade File" aria-label="Upload File">

             <i class="ti-clip" style="font-size: 20px;"></i>
             <input aria-label="File upload" type="file" name="image_<?php echo $value['id']; ?>" />
             <input type="hidden" name="hidimage_<?php echo $value['id']; ?>" value="<?php echo $value['document_shared']; ?>"/>
           </label>

        </div>
      <?php } ?>
      <?php if($value['document_shared']!="" && $_SESSION['user_type']!='4'){  //&& $_SESSION['userid'] == $data['hr_connect']?>

      <div class="dropdown-primary dropdown open">
        <button class="label label-sm label-success" style="height: 18px; border-radius: 30px;" title="<?php if($value['current_status'] == ST_REJECTED){  echo "Rejected";
}elseif($value['current_status'] == ST_APPROVED){ echo "Approved";
}else{  echo "Active";
}

        ?>"  data-bs-toggle="dropdown"  aria-haspopup="true"  aria-expanded="true" type="button" data-content="<?php echo $value[$current_status]['name']; ?>">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">


        <div class="dropdown-item <?php echo $value['current_status'] == ST_APPROVED?"active":""; ?>" onclick="statsonlychange(<?php echo $value['id'] ?>,<?php echo ST_APPROVED; ?>)"  name="status" id="status" value="<?php echo ST_APPROVED; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</div>
        <div class="dropdown-item <?php echo $value['current_status'] == ST_REJECTED?"active":""; ?>" onclick="statsonlychange(<?php echo $value['id'] ?>,<?php echo ST_REJECTED; ?>)"  name="status" id="status_2" value="<?php echo ST_REJECTED; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Rejected</div>
      </div>
    </div>

  <?php } ?>

</div>

</div>
</div>
</div>

<?php } ?>






</div>
</div>

</div>
</div>
</div>



<?php
}
}
}else{

  echo '<div class="text-center"><h4 class="text-info"><i class="fa fa-warning"></i> No Required Documents</h
  4></div>';
}  ?>
</div>
</div>
</div>
<div class="card-footer">
  <div class="row">
    <div class="col-lg-4">
     <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
     <input type="hidden" name="submit" value="submit" />
     <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitto" value="exit"><i class="la la-check"></i>Submit</button>
   </div>
 </div>
</div>
</form>
</div>
</div>
</div></div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog " >
    <div class="modal-content"  id="popup1">

    </div>
  </div>
</div>
