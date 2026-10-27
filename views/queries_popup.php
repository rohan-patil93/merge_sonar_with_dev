<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$question_display ='Y';
$replay_display = 'N';
$originator = $_POST['originator'];
$tran_no = $_POST['tran_no'];
$table_name = $_POST['table_name'];
$rid = $_POST['rid'];
$trnsation_no = $req['transaction_number'];
if($_POST['submit'] == 'query'){
    $res = Insertqueries($_POST);
     $_POST['submit'] = '';
}
if($_POST['submit'] == 'reply'){
    $res = updatequeries($_POST);
}
if($_SESSION['userid'] == $originator){
    $question_display ='N';
    $replay_display = 'Y';
}
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

<div class="modal-header">
                        <h5 class="modal-title">
                            Querires
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" >

            <?php if($question_display == 'Y'){ ?>
                    <form method="post" action="">
                    <div class="notes">
                        <?php foreach ($querytypelist as $querytype) { ?>
                            <input aria-label="Query Type" type="radio" name="query_type" value="<?php echo $querytype['genc_detail_code']; ?>" id="imp">
                            <label for="imp"><?php echo $querytype['genc_detail_name']; ?></label>
                        <?php } ?>
                        <textarea aria-label="Query Text" name="query_text"></textarea>
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
                        <div class="kt-portlet__body">
                            <div class="kt-notes">
                                <?php $k=1; foreach ($question_list as $list) { ?>
                                <div class="kt-notes__items">
                                    <div class="kt-notes__item">
                                        <div class="kt-notes__media">
                                            <span class="kt-notes__icon">
                                                <i class="flaticon2-shield kt-font-brand"></i>
                                            </span>
                                        </div>
                                        <div class="kt-notes__content">
                                            <div class="kt-notes__section">
                                                <div class="kt-notes__info">
                                                    <a href="#" class="kt-notes__title">
                                                        Question&nbsp;&nbsp; <?php echo $_SESSION['users'][$list['created_by']]; ?>
                                                    </a>
                                                    <span class="kt-notes__desc">
                                                        <?php echo date('d m, Y', strtotime($list['created_date'])); ?>
                                                    </span>
                                                    <?php if($list['query_type'] == 'H'){ ?>
                                                        <span class="kt-badge kt-badge--danger kt-badge--inline"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php } elseif($list['query_type'] == 'N'){ ?>
                                                        <span class="kt-badge kt-badge--warning kt-badge--inline"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php }else{?>
                                                        <span class="kt-badge kt-badge--brand kt-badge--inline"><?php echo $query_types[$list['query_type']]; ?></span>
                                                    <?php } ?>

                                                </div>
                                                <?php if($replay_display == 'Y'){ ?>
                                                <button type="button" class="btn btn-bold btn-label-brand btn-sm" onclick="replyquery(this.id)" id="<?php echo $k; ?>">Reply</button>
                                                <?php } ?>
                                            </div>

                                                <div class="kt-notes__body">
                                                    <?php echo $list['query_text']; ?>
                                                </div>
                                            </div>
                                            <div style="margin-left: 10px;">
                                                <?php $m=1 ;if(count((array)$answer_list[$list['id']])>0){
                                                foreach($answer_list[$list['id']] as $answer){?>
                                                    <div class="kt-notes__item paddingless">
                                                        <div class="kt-notes__media">
                                                            <span class="kt-notes__icon">
                                                                <i class="flaticon2-line-chart kt-font-success"></i>
                                                            </span>
                                                        </div>
                                                    <div class="kt-notes__content kt-notes__content2" style="margin-top: 20px; background: #fff2f1;">
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
                                                    <button class="btn btn-info">Reply</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php $k++;
} ?>
                            </div>
                        </div>
                    </div>
                </div>
</div>
