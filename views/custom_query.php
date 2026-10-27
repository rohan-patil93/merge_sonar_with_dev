<?php
if($_POST['submit'] == 'query'){
    $res = Insertqueries($_POST);
    unset($_POST['submit']);
         $_POST['submit'] = '';
    }
    if($_POST['submit'] == 'reply'){
      $res = updatequeries($_POST);
 }
 $querytypeurl =  API."?mode=gettabledetails&fid=genc_code&rid=QUERYTYPES&table=".TBL_GENRAL_CODE_DETAILS;
 $querytypelist = callAPI($querytypeurl);
 $querytypelist = json_decode($querytypelist,1);
 $query_types = array();
 foreach ($querytypelist as $querystype) {
      $query_types[$querystype['genc_detail_code']] = $querystype['genc_detail_name'];
 }
 $quu['order'] = "id DESC";
 $quu["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_id'=>$data['transaction_id'],'table_name'=>$data['table']);
 $quue = urlencode(json_encode($quu));
 $quueurl = API."?mode=gettabledetailswithconditions&table=".TBL_TRANSACTION_QUERIES."&data=".$quue;
 $querylist = callAPI($quueurl);
 $querylist =  json_decode($querylist,1);
 $question_list = array(); $answer_list = array();
 foreach ($querylist as $value) {
      if($value['new_reply'] == 'N'){
       $question_list[] = $value;
  }
  if($value['new_reply'] == 'R'){
       $answer_list[$value['parent_query']][] = $value;
  }
}
?>
<script type="text/javascript">
 function replyquery(id) {
  $('#reply_'+id).css('display','block');
}
</script>
<div class="accordion accordion-solid accordion-toggle-arrow mt-2" id="accordionExample6_query">
 <div class="card acc-bgc">
  <div class="card-header" id="headingOne6">
   <div class="card-title scale_active collapsed f-16 f-w-600" data-bs-toggle="collapse"  aria-expanded="false" aria-controls="collapseOne6"  data-target="#collapseOne6" href="#collapseOne6">
    Queries </div>
</div>
<div id="collapseOne6" class="collapse" data-parent="#accordionExample6_query">
     <div class="card-body">
          <?php if($question_display == 'Y'){ ?>
          <form method="post" action="">
               <div class="notes">
                    <div class="form-radio">
                    <?php foreach ($querytypelist as $querytype) { ?>
                         <div class="radio radio-inline">
                              <label class="form-label">
                                   <input aria-label="Query Type" type="radio" name="query_type" value="<?php echo $querytype['genc_detail_code']; ?>" id="imp" checked>
                                   <i class="helper"></i><?php echo $querytype['genc_detail_name']; ?>
                              </label>
                         </div>
                    <?php } ?>
                    </div>
                   <textarea aria-label="Query Text" name="query_text" class="form-control" required></textarea>
                   <input type="hidden" name="submit" value="query">
                   <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                   <input type="hidden" name="trno" value="<?php echo $data['transaction_number']; ?>">
                   <input type="hidden" name="table_name" value="<?php echo $data['table']; ?>">
                   <input type="hidden" name="transaction_id" value="<?php echo $data['transaction_id']; ?>">
                   <input type="hidden" name="created_by" value="<?php echo $_SESSION['userid']; ?>">
                   <input type="hidden" name="send_id" value="<?php echo $data['originator']; ?>">
                   <input type="hidden" name="new_reply" value="N">
                   <button class="btn btn-info" style="margin-top: 10px;">Submit New Query</button>

               </div>
          </form>
          <?php } ?>
          <div class="kt-portlet">

                    <div class="kt-notes">
                      <?php if(count((array)$question_list) !=0){ ?>
                         <?php $k=1; foreach ($question_list as $list){ ?>
                         <div class="sub-title">
                              <div class="">
                                   <div class="symbol symbol-40 symbol-light-success mr-5 mt-1">
                                        <span class="symbol-label">
                                             <i class="flaticon2-shield kt-font-brand"></i>
                                        </span>
                                   </div>
                                   <div class="d-flex flex-column flex-row-fluid">
                                        <div class="d-flex align-items-center flex-wrap">
                                             <div class="col-lg-3">
                                                  <a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder pr-6">
                                                       <?php if($_SESSION['user_image'][$list['created_by']]!=""){ ?>
                                                       <span class="col-auto text-end update-meta p-r-0"> <img src="uploads/all/<?php echo $_SESSION['user_image'][$list['created_by']]; ?>" alt="User" class="img-radius img-40 align-top m-r-15 update-icon" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $_SESSION['users'][$list['created_by']]; ?>" aria-label="<?php echo $_SESSION['users'][$list['created_by']]; ?>">  </span>
                                                        <?php }else{ ?>
                                                            <span  alt="user" class="img-radius align-top custom_txt_workentry f-14 text-center" style=" height: 70px;line-height: 70px; width: 70px;" ><?php echo initials( $_SESSION['users'][$list['created_by']]); ?></span>
                                                            <?php } ?>
                                                  </a>
                                                  <p class="f-12 mt-2"> <?php echo $_SESSION['users'][$list['created_by']]; ?></p>
                                             </div>
                         <div class="col-lg-8">
                              <div>
                                   <span class="text-muted font-weight-normal flex-grow-1 font-size-sm">
                                        <?php echo date('M d, Y', strtotime($list['created_date'])); ?> &nbsp;
                                   </span>
                                   <?php if($list['query_type'] == 'H'){ ?>
                                   <span class="form-label label label-danger"><?php echo $query_types[$list['query_type']]; ?></span>
                                        <?php } elseif($list['query_type'] == 'N'){ ?>
                                   <span class="form-label label label-warning "><?php echo $query_types[$list['query_type']]; ?></span>
                                   <?php }else{?>
                                       <span class=" form-label label label-info"><?php echo $query_types[$list['query_type']]; ?></span>
                                   <?php } ?>

                                   <p class="text-muted font-weight-normal font-size-sm mt-2">
                                        <span class="f-w-600 f-16 text-danger">Q: &nbsp;</span>
                                        <span class="text-dark-75 font-size-sm font-weight-normal pt-1">
                                         <?php echo $list['query_text']; ?>
                                    </span>
                                    <?php if($replay_display == 'Y'){ ?>
                                        <p>
                                             <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 btn-sm" onclick="replyquery(this.id)" id="<?php echo $k; ?>">Reply</button></p>
                                        <?php } ?>
                                   </p>
                              </div>

                         <div>
                              <?php $m=1 ;if(count((array)$answer_list[$list['id']])>0){
                              foreach($answer_list[$list['id']] as $answer){   ?>
                              <div class="kt-notes">
                                   <div class="kt-notes__media" style="float:left;margin-right:7px;">
                                        <span class="kt-notes__icon">
                                        <i class="flaticon2-line-chart kt-font-success"></i>
                                        </span>
                                   </div>
                                   <div class="kt-notes__content kt-notes__content2">
                                        <div class="kt-notes__section">
                                             <div class="kt-notes__info">
                                                  <span class="f-w-600 f-16 text-success">Reply &nbsp;<?php echo $m; ?>:&nbsp;</span>
                                                  <a href="#" class="kt-notes__title">
                                                       <div class="align-middle">
                                                            <?php if($_SESSION['user_image'][$answer['created_by']]!=""){?>
                                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$answer['created_by']]; ?>"   class="img-radius img-40 align-top" alt="User Profile">
                                                            <?php }else{ ?>
                                                            <span  alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$answer['created_by']]); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                 <h6> <?php echo $_SESSION['users'][$answer['created_by']]; ?> - <?php echo date('M d, Y', strtotime($answer['created_date'])); ?></h6>
                                                                 <p class="text-muted m-b-0">  <?php echo $answer['query_text']; ?></p>
                                                            </div>
                                                       </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <?php $m++; ?>
                              <?php } ?>
                              <?php } ?>
                         </div>

                         </div>
                    </div>
               </div>
          </div>

     <div style="margin-top: 5px; display: none" id="reply_<?php echo $k; ?>">
          <form action="" method="post">
               <div>
                   <textarea aria-label="Query Text" type="text" class="form-control" name="query_text" required></textarea>
               </div>
               <div style="margin-top: 3px;">
                   <input type="hidden" name="parent_query" value="<?php echo $list['id']; ?>">
                   <input type="hidden" name="query_type" value="<?php echo $list['query_type']; ?>">
                   <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                   <input type="hidden" name="trno" value="<?php echo $data['transaction_number']; ?>">
                   <input type="hidden" name="new_reply" value="R">
                   <input type="hidden" name="table_name" value="<?php echo $data['table']; ?>">
                   <input type="hidden" name="transaction_id" value="<?php echo $data['transaction_id']; ?>">
                   <input type="hidden" name="created_by" value="<?php echo $_SESSION['userid']; ?>">
                   <input type="hidden" name="send_id" value="<?php echo $list['created_by']; ?>">
                   <input type="hidden" name="submit" value="query">
                   <button class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 mt-2">Submit</button>
               </div>
          </form>
     </div>
</div>
<?php $k++;
} ?>

<?php }else{ ?>
  <div class="card-px text-center ">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert"></i> No Queries</p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
