 <?php
/**
Purpose : Getting faq Categories
**/
$vdata1['order'] = "display_sequence ASC";
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=faq_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$data_faq = json_decode($configesponse,1);

$vdata2["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
$vdata = urlencode(json_encode($vdata2));
$vurl = API."?mode=gettabledetailswithconditions&table=faq_contents&data=".$vdata;
$configesponse = callAPI($vurl);
$faqcontents = json_decode($configesponse,1);
foreach($faqcontents as $faqcontent){
   $dat_faqcontent[$faqcontent['faq_category_id']][] = $faqcontent;
}
?>
<style type="text/css">
   .factive{
      background-color: #4099ff;
      border-color: #4099ff;
      color: #ffffff !important;
   }
</style>
<div class="d-flex flex-column-fluid">
   <div class="container-fluid">
      <div class="card-body">
         <div class="row">
          <div class="col-lg-4 col-xl-3">
           <div id="navigation">
            <div class="row">
             <div class="col-lg-12">
              <div class="card version">
               <div class="card-header borderless">
                <h5>Frequently Asked Questions</h5>
             </div>
             <div class="card-block">

                <ul class="nav navigation">

                  <?php
                  $kt = 1;
                  $faq_cat_id = $_GET['CAT']!="" ? $_GET['CAT'] : '1';
                  foreach ($data_faq as $list) {
                     $faq_name = $list['faq_name'];
                     $faq_name_id[$list['id']] = $list['faq_name'];

                     if($_GET['APPLICATION']!="" && $_GET['APPLICATION']== $list['application_page_name']) {
                        $faq_cat_id = $list['id'];
                     }
                     $setActive = '';
                     if($list['id']==$faq_cat_id) { $setActive = 'factive';
}
                     ?>
                     <li class="waves-effect waves-light <?php echo $setActive;?>">
                        <a class="<?php echo $setActive;?>" href="index.php?option=COM_LIST_FAQ&CAT=<?php echo $list['id']; ?>" ><?php echo $faq_name; ?></a>
                     </li>
                     <?php $kt++;
} ?>

                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="col-lg-8 col-xl-9">
   <div class="card">
    <div class="card-header">
     <h5 class="card-header-text"><?php echo $faq_name_id[$faq_cat_id]; ?></h5>
  </div>
  <div class="card-block accordion-block">
     <div class="accordion-box" id="single-open">
     <?php $data_faqcontents = $dat_faqcontent[$faq_cat_id];
         foreach ($data_faqcontents as $list) {
                  $tab_index++;
                  $tabid =  "kt_tab_pane_".$tab_index ;
                  $faq_question = $list['faq_question'];
                  $faq_answer = $list['faq_answer'];
                                          if($tab_index == 1 ) {
                                             $setShow = 'show';
                                             $setCollapse = '';
                                             $aria_expanded = 'true';
                                          }
                                          else {
                                             $setShow = '';
                                             $setCollapse = 'collapsed';
                                             $aria_expanded = 'false';
                                          } ?>
      <a class="accordion-msg waves-effect waves-dark f-w-700"><?php echo $faq_question; ?></a>
      <div class="accordion-desc">
       <p><?php echo $faq_answer; ?></p>
  </div>
<?php  } ?>

</div>
</div>
</div>
</div>
</div>
</div>

</div>
</div>
</div>
