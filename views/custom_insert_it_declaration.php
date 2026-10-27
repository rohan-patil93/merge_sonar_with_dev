<?php
  if(isset($_POST['it_declaration'])){
      $hid = $_POST['it_plan'];
      unset($_POST['it_declaration']);
      unset($_POST['Submit']);
      unset($_POST['it_plan']);
     if($hid == ""){
          $url = API."?mode=adddata&table=emp_it_declaration&method=post";
          $response = file_post_contents($url, $_POST, 'insert');
      }else{
             $_POST['updated_date'] = nowDate();
           $_POST['updated_by'] = $_SESSION['userid'];
           $url = API."?mode=updaterowtabledata&rid=".$hid."&table=emp_it_declaration&method=post";
          $response = file_post_contents($url, $_POST, 'update');
        }
      if ($response){
        header("location:index.php?option=COM_INSERT_IT_PLANS");
      }
  }
  if(isset($_POST['AddExemption'])){
       $insert_data  = array();
       $insert_data['company_id'] = $_SESSION['company_id'];
       $insert_data['emp_id'] = $_SESSION['emp_id'];
       $insert_data['assessment_year'] = $_POST['assessment_year'];
       $insert_data['version_no'] = $version;
       $insert_data['start_date'] = date('Y-m-d', strtotime($_POST['start_date'] ));
       $insert_data['end_date'] = date('Y-m-d', strtotime($_POST['end_date'] )) ;
       $insert_data['monthly_rent'] = $_POST['monthly_rent'];
       $insert_data['annual_rent'] = $_POST['annual_rent'];
       $insert_data['address'] = $_POST['address'];
       $insert_data['landlord_pan_flag'] = $_POST['landlord_pan_flag'];
       $insert_data['lanlord_name'] = $_POST['lanlord_name'];
       $insert_data['landlord_address'] = $_POST['landlord_address'];
       $insert_data['landlord_pan_number'] = $_POST['landlord_pan_number'];
       $insert_data['is_active'] = 0;
       if($_POST['house_id']==""){
           $insert_data['created_date'] = nowDate();
           $insert_data['created_by'] = $_SESSION['userid'];
           $insert_data['current_status'] = '0';
           $url = API."?mode=adddata&table=emp_it_declaration_houserent&method=post";
           $response = file_post_contents($url, $insert_data, 'insert');
       }else{
            $insert_data['updated_date'] = nowDate();
           $insert_data['updated_by'] = $_SESSION['userid'];
           $url = API."?mode=updaterowtabledata&rid=".$_POST['house_id']."&table=emp_it_declaration_houserent&method=post";
           $response = file_post_contents($url, $insert_data, 'update');
           if ($response) {
           }

       }
  }
  if(isset($_POST['emppereviousdetails'])){
      $insert_data = array();
      $insert_data = $_POST;
      $pervious_id = $_POST['pervious_id'];
      unset($insert_data['pervious_id']);
      unset($insert_data['emppereviousdetails']);
      $insert_data['company_id'] = $_SESSION['company_id'];
      $insert_data['emp_id'] = $_SESSION['emp_id'];
      $insert_data['assessment_year'] = $_POST['assessment_year'];
      $insert_data['version_no'] = $version;
      if($pervious_id==""){
         $insert_data['created_date'] = nowDate();
         $insert_data['created_by'] = $_SESSION['userid'];
         $insert_data['current_status'] = '0';
         $insert_data['is_active'] = 0;
         $url = API."?mode=adddata&table=emp_perevious_details&method=post";
         $response = file_post_contents($url, $insert_data, 'insert');
      }else{
         $insert_data['updated_date'] = nowDate();
         $insert_data['updated_by'] = $_SESSION['userid'];
         $url = API."?mode=updaterowtabledata&rid=".$pervious_id."&table=emp_perevious_details&method=post";
         $response = file_post_contents($url, $insert_data, 'update');
         if ($response) {
         }
      }
  }
  if(isset($_POST['Add80cDeclaration'])) {
      print_r($_POST);//exit;
      $itd8amount = $_POST['itd8amount'];
      $itd8id = $_POST['itd8id'];
      $url = API."?mode=addorupdateitdeclaration80c&table=emp_it_declaration_80&method=post";
      $response = file_post_contents($url, $_POST, 'insert');
      header("location:index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=".$_POST['assessment_year']."&version_no=".$_POST['version']);
  }
  if(isset($_POST['AddotherDeclaration'])) {
         print_r($_POST);//exit;
         $url = API."?mode=addorupdateitdeclarationotherincome&table=emp_it_declaration_other&method=post";
         $response = file_post_contents($url, $_POST, 'insert');
  }

   $emp_id = $_GET['EMPID']!="" ? $_GET['EMPID'] : $_SESSION['emp_id'];
   $assessment_year = $_GET['assessment_year'];
   $version =$_GET['version_no'];
   $costurl = API."?mode=togetversonyearitdecration&assessment_year=".$assessment_year."&version_no=".$version."&empid=".$emp_id;
   $details = callAPI($costurl);
   $alldataa = json_decode($details, 1);
   $rest = $alldataa['it_declaration_categories'];
   $maindata = $alldataa['emp_it_declaration'];
   $emoitdecl80 = $alldataa['emp_it_declaration_80'];
   $emphousedata = $alldataa['emp_it_declaration_houserent'];
   $empprvedata = $alldataa['emp_perevious_details'];
   $emp_it_declaration_other_income = $alldataa['emp_it_declaration_other_income'];
   $resit =  $alldataa['it_declaration'];

   $send = array();
   foreach ($rest as $value) {
      if($value['parent_category_id'] ==0){
         $mainarray[] = $value['category_name'];
      }else{
         $send[$value['id']] = $value;
      }
   }
   $viewmanager ="";
   $aread = ""; $paread = "";
   if($_GET['EMPID']!="" ){ $aread = "readonly"; $paread = "";
}
   $viewfilemanger = "";
   if($viewfilemanger == 1 ){ $aread = "readonly"; $paread = "readonly";
}


   foreach ($emoitdecl80 as  $emoitdecl8) {
      $emp80itdec[$emoitdecl8['particular']] = $emoitdecl8['amount'];
   }

   foreach ($resit as $resitd) {
        $datails[$resitd['category_id']][] = $resitd;
   }

   $vurl = API."?mode=togetsalarytotaldetails&version_no=".$version."&assessment_year=".$assessment_year."&emp_id=".$emp_id;
   $configesponsee = callAPI($vurl);
   $saldata = json_decode($configesponsee,1);
   $tax_slab = $saldata['slads'];
   if($maindata['id'] ==""){
       $salary_template = $saldata['salary'];
       $cal_intax = 0;
       $calslab_total = $salary_template['ctc_yearly'];
       if($calslab_total >0){
          foreach ($tax_slab as $slab) {
             if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
                 $cal_amount = $calslab_total - ($slab['slad_from']-1);
                  $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
                  break;
             }else{
                $cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
                $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
             }
          }
       }
       if($cal_intax<0){
         $cal_intax = 0;
       }
      $maindata['taxable_income'] = $salary_template['ctc_yearly']>0 ? $salary_template['ctc_yearly']-25000 :0;
      $maindata['annual_tax'] = $cal_intax>0 ? $cal_intax :0;
      $maindata['potential_amount'] = $maindata['taxable_income']>0 ?$maindata['taxable_income'] :0;
      $maindata['declaration_tax'] = 0.00;
      $maindata['gap_tax'] = $maindata['taxable_income']>0 ? $maindata['taxable_income'] :0 ;
   }

   $assyear = explode('-', $_GET['assessment_year']);
   $start_now = strtotime($assyear['0'].'-04-01');
   $end_now = strtotime($assyear['1'].'-04-01');
   $annual_sal = $saldata['salary']['annual_ctc'];

?>
<div class="d-flex flex-column-fluid ">
   <div class="container-fluid ">
      <div class="card card-custom">
         <div class="card-body j-pro">
        <div> <div class="float-right f-w-700"> Assessment Year : <?php echo $_GET['assessment_year']; ?>  </div> </div>
            <div class="form-group col-lg-5 p-4">
               <table class="table table-bordered">
                  <thead>
                     <tr class="table-active">
                        <th scope="col" colspan="2">Taxable Income: <?php echo  $maindata['taxable_income']; ?> &nbsp; Annual Tax :  <?php echo  $maindata['annual_tax']; ?> </th>
                        <th scope="col" class="text-right text-primary">
                          <?php for($i=0;$i<5;$i++){
                             if($i< $maindata['star_rate']){?>
                                    <i class="fas fa-star text-warning"></i>
                           <?php }else{?>
                                    <i class="fas fa-star"></i>
                           <?php } ?>
                           <?php } ?>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <th scope="col" >Potential</th>
                        <th scope="col">Actual</th>
                        <th scope="col">GAP</th>
                     </tr>
                     <tr>
                        <td><?php echo $maindata['potential_amount']; ?></td>
                        <td><?php  echo  $maindata['declaration_tax']; ?></td>
                        <td><?php  echo  $maindata['gap_tax']; ?> </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="card-header card-header-tabs-line">
               <div class="card-toolbar">
                  <ul class="nav nav-tabs md-tabs custom-declaration" >
                     <?php $k=1; foreach ($mainarray as  $marray){ ?>
                     <li class="nav-item">
                        <a class="nav-link <?php if($k==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#kt_tab_pane_<?php echo $k; ?>_4">

                        <span class="nav-text"><i class="fa fa-commenting"></i> &nbsp; &nbsp;<?php echo $marray;  ?></span>
                        </a>
                     </li>
                     <?php $k++;
} ?>
                  </ul>
               </div>
            </div>
            <div class="card-body">
               <div class="tab-content">
                   <div class="tab-pane fade show active" id="kt_tab_pane_1_4" role="tabpanel" aria-labelledby="kt_tab_pane_1_4">
                      <?php
                        include_once 'custom_view_regime.php';
                      ?>

                  </div>
                  <div class="tab-pane fade fa-sm" id="kt_tab_pane_2_4" role="tabpanel" aria-labelledby="kt_tab_pane_2_4">
<form method="post" action="#">
<?php
$it_row = 1;
foreach ($send as $sent) { //echo "<pre>";print_r($sent); //exit;
   $colspan = 3; $width_len = 65;
   if($viewmanager!=""){
      $colspan = 4;
      $width_len = 65;
   }
   if($viewfilemanger!=""){
      $colspan = 5;
      $width_len = 50;
   }

   if($sent['individual_limit'] == 0 ){
      $colspan =  $colspan +1;
      $width_len = $width_len-15;
   }
   if($sent['age_limit'] == 0 ){
     $colspan =  $colspan +1;
     $width_len = $width_len-10;
  }
?>
   <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer fa-1s" id="invoiceItem">
      <tr class="table-primary text-white">
         <td colspan="<?php echo $colspan; ?>"><b><?php echo $sent['category_name']; ?></b></td>
      </tr>
      <tr class="table-active">
         <th scope="col" class="" style="width: 2%;">#</th>
         <th scope="col" class="" style="width: <?php echo $width_len; ?>%;">Particulars</th>
         <?php if($sent['age_limit'] == 0 ){ ?>
            <th scope="col" class="thheader" style="width: 15%;">Age </th>
         <?php } ?>
         <?php if($sent['individual_limit'] == 0 ){ ?>
            <th scope="col" class="thheader" style="width: 10%;">Max Limit </th>
         <?php } ?>
         <th scope="col" class="thheader" style="width: 13%;">Amount </th>
         <?php  if($viewmanager!="" || $viewfilemanger != ""){   ?>
            <th scope="col" class="thheader" style="width: 13%;">Approved Amount </th>
         <?php } ?>
         <?php  if($viewfilemanger != ""){ ?>
            <th scope="col" class="thheader" style="width: 5%;">File </th>
         <?php } ?>
      </tr>
      <?php $k=1;foreach ($datails[$sent['id']] as $datail) { ?>
                        <tr class="">
                           <td class="" style="width: 2%;"><?php echo $k; ?></td>
                           <td class="" style="width: <?php echo $width_len; ?>%;">
                            <?php echo $datail['name']; ?>
                               <span class="mytooltip tooltip-effect-1">
                              <span class="tooltip-item2"><i class="ti-info-alt"></i></span>
                              <span class="tooltip-content4 clearfix">
                                  <span class="tooltip-text2">
                                    <?php echo  $datail['remarks']; ?>
                                </span>
                            </span>
                            </td>
                            <?php if($sent['age_limit'] == 0 ){ ?>
                                <td class="" style="width: 15%;"><?php echo $datail['age_display']; ?></td>
                            <?php } ?>
                            <?php if($sent['individual_limit'] == 0 ){ ?>
                                <td class="" style="width: 10%;"><?php echo customeummarmonyformate($datail['max_limit']); ?></td>
                            <?php } ?>
                            <td class="thheader" style="width: 13%;">
                              <?php $it80amount = $emp80itdec[$datail['id']] !="" ?  $emp80itdec[$datail['id']] : '0.00'; ?>
                              <input aria-label="Itd8amount[]" type="text" name="itd8amount[]"  <?php echo $aread; ?> class="form-control text-right" value="<?php echo $it80amount; ?>" id="itd8amount_<?php echo $it_row; ?>">
                              <input type="hidden" name="itd8id[]" value="<?php echo $datail['id']; ?>" class="form-control" id="itd8id_<?php echo $it_row; ?>">
                               <input type="hidden" name="itd8catid[]" value="<?php echo $sent['id']; ?>" class="form-control" id="itd8catid_<?php echo $it_row; ?>">
                                <input type="hidden" name="itdmaxlimit[]" value="<?php echo $datail['max_limit']; ?>" class="form-control" id="itdmaxlimit_<?php echo $it_row; ?>">
                            </td>
            <?php  if($viewmanager!="" || $viewfilemanger != ""){ ?>
            <td>
                 <input aria-label="Itd8approveamount[]" type="text" name="itd8approveamount[]" <?php echo $paread; ?>  class="form-control text-right" value="<?php echo $it80amount; ?>" id="itd8approveamount_<?php echo $it_row; ?>">
            </td>
            <?php } ?>
            <?php  if($viewfilemanger!="" ){ ?>
            <td>
                  <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
             <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" style="left:-5px;top:-15px;">
              <i class="fa fa-cloud-upload"></i>
              <input aria-label="File[]" type="file" name="file[]" />
              <input type="hidden" name="hidden_file[]" value="<?php echo $expe['compay_file']; ?>"/>
            </label>
            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>
            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
              <i class="ki ki-bold-close icon-xs text-muted"></i>
            </span>
          </div>
            </td>
            <?php } ?>


                        </tr>
                        <?php $k++; $it_row++;
} ?>
   </table>
<?php } ?>
                     <input type="hidden" name="version_no" value="<?php echo $_GET['version_no']; ?>">
                     <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
                    <input type="hidden" name="assessment_year" value="<?php echo $_GET['assessment_year']; ?>">

                    <input type="hidden" name="potential_amount" value="<?php echo $maindata['potential_amount']; ?>">
                     <input type="hidden" name="declaration_tax" value="<?php echo $maindata['declaration_tax']; ?>">
                     <input type="hidden" name="gap_tax" value="<?php echo $maindata['gap_tax']; ?>">
                     <input type="hidden" name="taxable_income" value="<?php echo $maindata['taxable_income']; ?>">
                     <input type="hidden" name="annual_tax" value="<?php echo $maindata['annual_tax']; ?>">

                     <div class="form-group  col-lg-12">

                    <button type="submit" name="Add80cDeclaration" class="btn btn-primary waves-effect waves-light float-right" value="Submit"/>Submit</button>
                               </div>
                             </form>

                  </div>
                  <div class="tab-pane fade" id="kt_tab_pane_3_4" role="tabpanel" aria-labelledby="kt_tab_pane_3_4">
                     <div class="accordion accordion-light accordion-toggle-arrow" id="accordionExample5">
                                <?php   include_once "custom_insert_house_rent_it_declaration.php";  ?>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="kt_tab_pane_4_4" role="tabpanel" aria-labelledby="kt_tab_pane_4_4">
                     <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer " id="invoiceItem_2">

                        <tr class="table-primary text-white">
                           <td colspan="3"><b>Income From Perevious Employer</b></td>

                        </tr>
                        <tr class="table-active">
                           <th scope="col" class="" style="width: 2%;">#</th>
                           <th scope="col" class="" style="width: 85%;">Particulars</th>
                           <th scope="col" class="thheader" style="width: 13%;">Amount </th>
                        </tr>
                        <form method="post" action="#">
                        <?php  $i=1;
                          $pevios_array = array('income_after_exemption'=>'Income after Exemption','profession_tax'=>'Profession Tax-PT','provident_fund'=>'Provident Fund-PF','total_tax'=>'Total Tax','tax_income'=>'Tax On Income','surcharges'=>'Surcharges','education_cess'=>'Education cess');
                        foreach($pevios_array as $pevios=>$pevios_arr) { ?>
                        <tr>
                           <th scope="col" class="" style="width: 2%;"><?php echo $i; ?></th>
                           <th scope="col" class="" style="width: 85%;"><?php echo $pevios_arr; ?></th>
                           <th scope="col" class="thheader" style="width: 13%;">
                            <input aria-label="Input field" type="text" name="<?php echo $pevios; ?>" value = "<?php echo $empprvedata[$pevios]; ?>"  class="form-control  text-right" >
                           </th>
                        </tr>

                        <?php $i++;
} ?>


                     </table>
<?php  if($viewfilemanger!="" ){
$file_array = array('application_table_name'=>'emp_perevious_details','transaction_id'=>$empprvedata['id'] ,'is_active'=>'0','width'=>$file_data['fileuplodingwidth']!=""?$file_data['fileuplodingwidth']:'6','deletewidth'=>$file_data['fileuplodingdleetewidth']!=""?$file_data['fileuplodingdleetewidth']:'6','deleteview'=>$file_data['fileuplodingdeleteddisplay']!=""?$file_data['fileuplodingdeleteddisplay']:1,'file_size'=>$file_data['file_size']!=""?$file_data['file_size']:'6','file_limit'=>$file_data['number_of_files']!=""?$file_data['number_of_files']:'6','lable'=>$file_data['fileuplodinglabel']);
fileUplodingUi($file_array);
} ?>
                     <div class="form-group  col-lg-12">
                                        <input type="hidden" value="<?php echo $empprvedata['id']; ?>" name="pervious_id">
                                        <input type="hidden" value="<?php echo $assessment_year; ?>" name="assessment_year">
                                       <button type="submit" name="emppereviousdetails" class="btn btn-primary waves-effect waves-light float-right" value="Submit"/>Submit</button>
                                       </div>
                  </div>
                </form>
                  <div class="tab-pane fade" id="kt_tab_pane_5_4" role="tabpanel" aria-labelledby="kt_tab_pane_5_4">


                           <?php print_r($emp_it_declaration_other_income); ?>
                            <form method="post" action="#">
                           <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer " id="invoiceItem_4">

                        <tr class="table-primary text-white">
                           <td colspan="4"><b> Other Incomes</b></td>

                        </tr>
                        <tr class="table-active">
                           <th scope="col" class="" style="width: 2%;">#</th>
                           <th scope="col" class="" style="width: 70%;">Particulars</th>

                           <th scope="col" class="" style="width: 15%;">Amount </th>
                        </tr>
                        <tr>
                           <th scope="col" class="" style="width: 2%;">1</th>

                           <th scope="col" style="width: 70%;"><input aria-label="Othername[]" type="text" name="othername[]"  value="<?php echo $emp_it_declaration_other_income['0']['particular'] ?>" class="form-control" id="particular_oi_0"> </th>
                           <th scope="col" style="width: 15%;"><input aria-label="Amount[]" type="text" name="amount[]" value="<?php echo $emp_it_declaration_other_income['0']['amount'] ?>" class="form-control text-right" id="amount_oi_0"> </th>
                        </tr>
                         <tr>
                           <th scope="col" class="" style="width: 2%;">2</th>

                           <th scope="col" style="width: 70%;"><input aria-label="Othername[]" type="text" name="othername[]" value="<?php echo $emp_it_declaration_other_income['1']['particular'] ?>"   class="form-control" id="particular_oi_1"> </th>
                           <th scope="col" style="width: 15%;"><input aria-label="Amount[]" type="text" name="amount[]" value="<?php echo $emp_it_declaration_other_income['1']['amount'] ?>" class="form-control text-right" id="amount_oi_1"> </th>
                        </tr>
                         <tr>
                           <th scope="col" class="" style="width: 2%;">3</th>

                           <th scope="col" style="width: 70%;"><input aria-label="Othername[]" type="text" name="othername[]" value="<?php echo $emp_it_declaration_other_income['2']['particular'] ?>"  class="form-control" id="particular_oi_2"> </th>
                           <th scope="col" style="width: 15%;"><input aria-label="Amount[]" type="text" name="amount[]" value="<?php echo $emp_it_declaration_other_income['2']['amount'] ?>" class="form-control text-right" id="amount_oi_2"> </th>
                        </tr>
                        <tr>
                           <th scope="col" class="" style="width: 2%;">4</th>

                           <th scope="col" style="width: 70%;"><input aria-label="Othername[]" type="text" name="othername[]" value="<?php echo $emp_it_declaration_other_income['3']['particular'] ?>"  class="form-control" id="particular_oi_3"> </th>
                           <th scope="col" style="width: 15%;"><input aria-label="Amount[]" type="text" name="amount[]" value="<?php echo $emp_it_declaration_other_income['3']['amount'] ?>" class="form-control text-right" id="amount_oi_3"> </th>
                        </tr>
                        <tr>
                           <th scope="col" class="" style="width: 2%;">5</th>

                           <th scope="col" style="width: 70%;"><input aria-label="Othername[]" type="text" name="othername[]" value="<?php echo $emp_it_declaration_other_income['4']['particular'] ?>" class="form-control" id="particular_oi_4"> </th>
                           <th scope="col" style="width: 15%;"><input aria-label="Amount[]" type="text" name="amount[]" value="<?php echo $emp_it_declaration_other_income['4']['amount'] ?>" class="form-control text-right" id="amount_oi_4"> </th>
                        </tr>


                     </table>
                      <input type="hidden" name="version" value="<?php echo $_GET['version_no']; ?>">
                     <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
                    <input type="hidden" name="assessment_year" value="<?php echo $_GET['assessment_year']; ?>">
                    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                     <div class="form-group  col-lg-12">

                               <button type="submit" name="AddotherDeclaration" class="btn btn-primary waves-effect waves-light float-right" value="Submit"/>Submit</button>
                               </div>
                             </form>

                        <?php  if($viewfilemanger!="" ){
$file_array = array('application_table_name'=>'emp_perevious_details','transaction_id'=>$empprvedata['id'] ,'is_active'=>'0','width'=>$file_data['fileuplodingwidth']!=""?$file_data['fileuplodingwidth']:'6','deletewidth'=>$file_data['fileuplodingdleetewidth']!=""?$file_data['fileuplodingdleetewidth']:'6','deleteview'=>$file_data['fileuplodingdeleteddisplay']!=""?$file_data['fileuplodingdeleteddisplay']:1,'file_size'=>$file_data['file_size']!=""?$file_data['file_size']:'6','file_limit'=>$file_data['number_of_files']!=""?$file_data['number_of_files']:'6','lable'=>$file_data['fileuplodinglabel']);
fileUplodingUi($file_array);
} ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php /**/ ?>
