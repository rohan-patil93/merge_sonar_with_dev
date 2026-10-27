<?php
    $querytypeurl =  API."?mode=gettabledetails&fid=genc_code&rid=QUERYTYPES&table=".TBL_GENRAL_CODE_DETAILS;
    $querytypelist = callAPI($querytypeurl);
    $querytypelist = json_decode($querytypelist,1);
    $query_types = array();
    foreach ($querytypelist as $querystype) {
            $query_types[$querystype['genc_detail_code']] = $querystype['genc_detail_name'];
    }
    $quu['order'] = "id DESC";
    $quu["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_id'=>$rid,'table_name'=>$table_name);
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


        <div class="card">
            <div class="card-header" id="notes-accor-1">
                <div class="card-title collapsed" data-toggle="collapse" data-target="#notes-accor" aria-expanded="false" aria-controls="notes-accor">
                    Queries
                </div>
            </div>
            <div id="notes-accor" class="collapse" aria-labelledby="notes-accor-1" data-parent="#accordionExample6">
                <div class="card-body">
                    <?php if($question_display == 'Y'){ ?>
                    <form method="post" action="">
                    <div class="notes">
                        <?php foreach ($querytypelist as $querytype) { ?>
                            <input aria-label="Query Type" type="radio" name="query_type" value="<?php echo $querytype['genc_detail_code']; ?>" id="imp">
                            <label for="imp"><?php echo $querytype['genc_detail_name']; ?></label>
                        <?php } ?>
                        <textarea aria-label="Query Text" name="query_text" class="form-control"></textarea>
                        <input type="hidden" name="submit" value="query">
                        <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                        <input type="hidden" name="trno" value="<?php echo $tran_no; ?>">
                        <input type="hidden" name="table_name" value="<?php echo $table_name; ?>">
                        <input type="hidden" name="transaction_id" value="<?php echo $rid; ?>">
                        <input type="hidden" name="created_by" value="<?php echo $_SESSION['userid']; ?>">
                        <input type="hidden" name="send_id" value="<?php echo $originator; ?>">
                        <input type="hidden" name="new_reply" value="N">
                        <button class="btn btn-info" style="margin-top: 10px;">Submit New Query</button>

                    </div>
                    </form>
                    <?php } ?>
                    <div class="kt-portlet">
                        <div class="card-body">
                            <div class="kt-notes">
                <?php $k=1; foreach ($question_list as $list) { ?>
                                <div class="d-flex py-5">

                                        <div class="symbol symbol-40 symbol-light-success mr-5 mt-1">
                                               <span class="symbol-label">
                                                <i class="flaticon2-shield kt-font-brand"></i>
                                             </span>
                                           </div>
                                        <div class="d-flex flex-column flex-row-fluid">
                                             <div class="d-flex align-items-center flex-wrap">
                                                <div class="kt-notes__info">
                                                    <a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder pr-6">
                                                        Q: <?php echo $_SESSION['users'][$list['created_by']]; ?>
                                                    </a>
                                                    <span class="text-muted font-weight-normal flex-grow-1 font-size-sm">
                                                        <?php echo date('d m, Y', strtotime($list['created_date'])); ?>
                                                    </span>
                                                    <?php if($list['query_type'] == 'H'){ ?>
                                                        <span class="label label-lg label-light-primary label-inline font-weight-bold py-4"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php } elseif($list['query_type'] == 'N'){ ?>
                                                        <span class="label label-lg label-light-warning label-inline font-weight-bold py-4"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php }else{?>
                                                        <span class=" label label-lg label-light-brand label-inline font-weight-bold py-4"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php } ?>


                                                <?php if($replay_display == 'Y'){ ?>
                                                    <span class="text-muted font-weight-normal font-size-sm">
                                                <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="replyquery(this.id)" id="<?php echo $k; ?>">Reply</button>
                                                </span>
                                                <?php } ?>
                                                </div>
                                                </div>
                                                <span class="text-dark-75 font-size-sm font-weight-normal pt-1">
                                                    <?php echo $list['query_text']; ?>
                                                </span>
                                            </div>
                                            </div>
                                            <div style="margin-left: 50px;">
                                                <?php $m=1 ;if(count((array)$answer_list[$list['id']])>0){
                                                foreach($answer_list[$list['id']] as $answer){?>
                                                    <div class="kt-notes" style="padding:7px;">
                                                        <div class="kt-notes__media" style="float:left;margin-right:7px;">
                                                            <span class="kt-notes__icon">
                                                                <i class="flaticon2-line-chart kt-font-success"></i>
                                                            </span>
                                                        </div>
                                                    <div class="kt-notes__content kt-notes__content2">
                                                        <div class="kt-notes__section">
                                                            <div class="kt-notes__info">
                                                                <a href="#" class="kt-notes__title">
                                                                    Replay&nbsp;<?php echo $m; ?>:&nbsp;&nbsp; <?php echo $_SESSION['users'][$answer['created_by']]; ?>
                                                                </a>
                                                                <span class="kt-notes__desc">
                                                                    <?php echo date('d m, Y', strtotime($answer['created_date'])); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <span class="kt-notes__body">
                                                            <?php echo $answer['query_text']; ?>
                                                        </span>
                                                    </div>
                                                </div>

                                            <?php $m++; ?>
                                            <?php } ?>
                                            <?php } ?>
                                            </div>
                                            <div style="margin-top: 5px; display: none" id="reply_<?php echo $k; ?>">
                                                <form action="" method="post">
                                                <div>
                                                    <textarea aria-label="Query Text" type="text" class="form-control" name="query_text"></textarea>
                                                </div>
                                                <div style="margin-top: 3px;">
                                                    <input type="hidden" name="parent_query" value="<?php echo $list['id']; ?>">
                                                    <input type="hidden" name="query_type" value="<?php echo $list['query_type']; ?>">
                                                    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                                                    <input type="hidden" name="trno" value="<?php echo $tran_no; ?>">
                                                    <input type="hidden" name="new_reply" value="R">
                                                    <input type="hidden" name="table_name" value="<?php echo $table_name; ?>">
                                                    <input type="hidden" name="transaction_id" value="<?php echo $rid; ?>">
                                                    <input type="hidden" name="created_by" value="<?php echo $_SESSION['userid']; ?>">
                                                    <input type="hidden" name="send_id" value="<?php echo $list['created_by']; ?>">
                                                    <input type="hidden" name="submit" value="query">
                                                    <button class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">Submit</button>
                                                </div>
                                                </form>
                                            </div>

                                    </div>
                                <?php $k++;
} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
