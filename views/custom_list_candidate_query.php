<?php
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
if($_POST['submit'] == 'query'){
    $res = Insertqueries($_POST);
     $_POST['submit'] = '';
}
if($_POST['submit'] == 'reply'){
    $res = updatequeries($_POST);
}
?>
<div class="d-flex flex-column-fluid">

  <div class="container-fluid">
<div class="flex-row">
   <?php    include_once 'candidate_side_nav.php'; ?>

<div class="flex-row-fluid ml-lg-8">
    <div class="row">
        <div class="col-lg-12">
            <div class="" id="kt_page_sticky_card">


            <div class="card-header table-card-header">
                <h5> Queries</h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>Back
                    </a>
                </div>
            </div>


    <div class="">
<?php
if($_SESSION['user_type'] == 4){
 $question_display ='Y';
        $replay_display = 'N';
    }else{
        $question_display ='N';
        $replay_display = 'Y';
    }
        $table_name = 'candidate_info';
        $rid =$candidate_id;
        $originator= $data['hr_connect'];

include_once "new_queries.php"; ?>
</div>
</div></div>
</div>
