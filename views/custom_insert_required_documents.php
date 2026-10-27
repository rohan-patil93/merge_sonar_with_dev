<?php

        $cat_id =1;
        $option_c = $_GET['option'];
        $RID_c = $_GET['Rid'];
        if(isset($_POST['submit'])){
            $url = API."?mode=addorupdatecandaitedocument&method=post";
            $_POST['company_id'] = $_SESSION['company_id'];
          $response = file_post_contents($url, $_POST, 'insert');
          if ($response) {  header("location:index.php?option=COM_INSERT_CANDIDATE&NI=1&Rid=".$RID_c);
}
        }
        $vdata2["where"] = array('is_active'=>0,'category_id'=>1);
        $vdata2["field"] = "id,document_name,parent_document_id";
        $vdata2['order'] = "display_position ASC";
        $vdata = urlencode(json_encode($vdata2));
        $vurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$vdata;
        $allparentdocuments = callAPI($vurl);
        $allparentdocuments  = json_decode($allparentdocuments,1);
        foreach($allparentdocuments as $alldo){
          if($alldo['parent_document_id']==0){
            $parentdocuments[] = $alldo;
          }else{
            $allvdata_details[$alldo['parent_document_id']][] = $alldo;
          }
        }
        ?>
        <style>

          .pricing .title {
            background: #edeff7;
            height: 80px;
            color: #3c7f4b;
            padding: 23px 10px;
          }
          .pricing_features {
            background: #FAFAFA;
            padding: 10px 10px;
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
        <div class="flex-column-fluid ">
            <div class="container-fluid ">
              <div class=" flex-row ">
                 <?php  include_once 'candidate_side_nav.php'; ?>
                 <?php
                 $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'candidate_id'=>$candidate_id,'is_active'=>0,'category_id'=>$cat_id);
                 $wdata = urlencode(json_encode($whrcon));
                 $url = API."?mode=gettabledetailswithconditions&table=candidate_documents&data=".$wdata;
                 $response = callAPI($url);
                 $fdata = json_decode($response,1);
                 foreach ($fdata as $value) {
                   $checkl[] = $value['document_list_id'];
                 }
                 ?>
                 <div class="flex-row-fluid ">
                   <div class="row">
                    <div class="col-lg-12">
                      <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                       <div class="" id="kt_page_sticky_card">

                         <div class="card-header table-card-header">
                          <h5> Required Documents</h5>
                          <div class="card-header-right table-card-header ">
                           <?php if($_GET['Rid']!="" || $data['current_status']!=""){ $nmm= ViewStatusValue($data['current_status'],'candidates');  ?>
                           <div class="btn-group">
                            <button onclick="displaystausdrop(<?php echo $data['id']; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $data['current_status']; ?>)" class="btn btn-primary dropdown-bs-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?> </button>
                            <div class="dropdown-menu" id="dropdownlist">                                 </div>
                          </div>
                        <?php } ?>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                          <i class="icofont icofont-reply"></i>
                          Back
                        </a>
                      </div>
                    </div>

                    <div class="card-body row">
                      <?php foreach($parentdocuments as $parentdocuments){
                       $vdata_details = $allvdata_details[$parentdocuments['id']];
                       if(count((array)$vdata_details) >= 1){
                        ?>
                        <div class="col-md-3 col-sm-3 col-xs-12 mb-10">
                          <div class="pricing ui-ribbon-container">
                            <div class="title">
                              <h5><?php echo ucwords(strtolower($parentdocuments['document_name'])); ?></h5>
                            </div>
                            <div class="x_content">
                              <div class="">
                                <div class="pricing_features">
                                 <ul class="list-unstyled text-left">
                                  <?php
                                  foreach ($vdata_details as $details) {
                                    ?>
                                    <li>
                                      <div class="checkbox-fade fade-in-primary">
                                       <label class="form-label">
                                         <input aria-label="Checkbox" type="checkbox"  <?php if(in_array($details['id'], $checkl)){ ?> checked  disabled="disabled" <?php
}else{ ?> name="document[]" <?php
} ?> value="<?php echo $details['id']; ?>" />
                                         <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span>
                                         <?php echo ucwords($details['document_name']); ?></span>
                                       </label>
                                     </div>
                                   </li>
                                 <?php }?>
                               </ul>
                             </div>
                           </div>

                         </div>
                       </div>
                     </div>
                   <?php }else{
                    echo '<div class="text-center"><h4 class="text-info"><i class="fa fa-warning"></i> No Required Documents</h
                    4></div>';
                  }
}?>
                </div>
                <?php if(count((array)$vdata_details) >= 1){ ?>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-lg-4">
                        <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                        <input type="hidden" name="submit" value="submit" />
                        <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitto" value="exit"><i class="la la-check"></i>Submit</button>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
