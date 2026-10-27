<?php

$ridss = explode(':', $rid);
$rids = $ridss[1];
$table = "candidates";
$rid = $rids;
$vurl = API."?mode=processtaskschecduleview&table_name=".$table."&id=".$rid;
$configesponse = callAPI($vurl);
$tasklist = json_decode($configesponse,1);
?>
<style>
    .pricing .title {
        background: #edeff7;
        height: 90px;
        color: #3c7f4b;
        padding: 15px 0 0;

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
    .kanban-container .kanban-board .kanban-board-header.info {
        background-color: #8950fc;
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    <?php
    $color_array = array('primary','success','info');
    ?>
</style>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card-custom card-sticky" id="kt_page_sticky_card">


            <div class="card-header table-card-header">
                <h5> Process Task</h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div></div>

                <form method="post" class="">
                    <div class="">
                        <div class="card-body row">

<?php $mck = '0'; if(count((array)$tasklist) > 0){   foreach($tasklist as $row){  $proceslist = json_decode($row['tran_check_list'],1);
$datere = explode(" ",$row['tran_revised_action_date'])[0];
$todays_date = strtotime(date("d-m-Y"));
$date = strtotime($datere);
$status = 0;
foreach($proceslist as $fef){
    if($fef['check_value'] ==""){
        $status = 1;
    }
}

?>

<div class="col-md-4 col-sm-6 col-xs-12 mb-10">
    <div id="kt_kanban_2">
        <div class="kanban-container" >
            <div data-id="_inprocess" data-order="1" class="kanban-board" style="width: 100%; ">
                <header class="kanban-board-header info">
                    <div class="row p-2">
                        <div class="col-lg-2">

                            <?php if($row['user_photo']!=""){ ?>
                                <img src="uploads/all/<?php echo $row['user_photo']; ?>"   class="img-radius img-50 align-top m-r-15" alt="User Profile">
                            <?php } else { ?>
                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-14 text-center bg-white  f-w-700" ><?php echo initials($row['display_name']); ?></span>
                            <?php } ?>
                        </div>
                        <div class="col-lg-10">
                            <div class="kanban-title-board text-white"><?php echo ucwords($row['task_name']); ?></div>

                            <span class="form-label label label-inverse"><?php echo $row['display_name']; ?> (<?php echo $datere; ?>)  </span>


                            </div></div>
                        </header>
                        <main class="kanban-drag">
                            <div class="pricing_features">
                                <ul class="list-unstyled text-left">
                                    <?php foreach($proceslist as $processrow){ ?>
                                        <li>
                                            <div class="checkbox-fade fade-in-primary">
                                                <label class="form-label">
                                                    <input aria-label="Checkbox" type="checkbox"  <?php echo $processrow['check_value']!=''?"checked='checked'":""; ?>   disabled="disabled" value="" />
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span>
                                                        <?php echo ucwords($processrow['name']); ?></span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php }  ?>
                                    </ul>
                                </div>
                            </main>
                            <footer></footer>
                        </div>
                    </div>
                </div>
            </div>

            <?php  if($mck == '2'){ $mck = '0';
}else{ $mck++;
}
}
}else{   echo '<div class="text-center"><h4 class="text-info"><i class="fa fa-warning"></i> No Process Task</h
  4></div>';
} ?>
        </div>
    </div>
</div>

</div>
</div>

</div>
</div>
</div>
